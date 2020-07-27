<?php /** @noinspection PhpDeprecationInspection */
/** @noinspection SqlIdentifier */
/** @noinspection SqlRedundantOrderingDirection */
/** @noinspection SqlResolve */

/**
 * Created by Gorlum 08.10.2017 17:14
 */

namespace Pages\Deprecated;

use DBAL\db_mysql;
use \SN;
use \classLocale;
use DBAL\DbSqlPaging;
use Fleet\MissionEspionageReport;
use General\Helpers\PagingRenderer;
use Pm\DecodeEspionage;
use SnTemplate;
use \template;

/**
 * Class PageMessage
 * @package Deprecated
 *
 * Refactor of /messages.php
 */
class PageMessage extends PageDeprecated {
  /**
   * List messages amount per category
   */
  const MESSAGES_MODE_CATEGORIES = '';
  /**
   * Delete message(s)
   */
  const MESSAGES_MODE_DELETE = 'delete';
  /**
   * Write personal message
   */
  const MESSAGES_MODE_COMPOSE = 'write';
  /**
   * Show messages in category
   */
  const MESSAGES_MODE_MESSAGES = 'show';

  const MESSAGES_DELETE_RANGE_NONE = '';
//  const MESSAGES_DELETE_RANGE_UNCHECKED = 'unchecked';
  const MESSAGES_DELETE_RANGE_CHECKED = 'checked';
  const MESSAGES_DELETE_RANGE_CLASS = 'class';
  const MESSAGES_DELETE_RANGE_ALL = 'all';

  const MESSAGES_DELETE_RANGES_ALLOWED = [
//    self::MESSAGES_DELETE_RANGE_UNCHECKED,
    self::MESSAGES_DELETE_RANGE_CHECKED,
    self::MESSAGES_DELETE_RANGE_CLASS,
    self::MESSAGES_DELETE_RANGE_ALL,
  ];

  /**
   * @var array[] $messageClassList - [int => ['name' => string, 'switchable' => bool, 'email' => bool]]
   */
  protected $messageClassList = [];

  /**
   * @var classLocale $lang
   */
  protected $lang;

  /**
   * Current page mode
   *
   * @var $mode
   * // TODO - change type to INT when finish
   */
  protected $mode;

  /**
   * @var int $current_class
   */
  protected $current_class;

  /**
   * @var string $recipient_name_unsafe
   */
  // TODO - UNSAFE USAGES!
  protected $recipient_name_unsafe = '';

  /**
   * @var int|string $recipient_id_unsafe
   */
  protected $recipient_id_unsafe = 0;

  protected $showAll = false;

  /**
   * @var string $subject_unsafe
   */
  protected $subject_unsafe = '';

  /**
   * Text to send
   *
   * @var string $sendText_unsafe
   */
  protected $sendText_unsafe = '';

  /**
   * Reference to current PLAYER DB record
   *
   * @var array $user
   */
  protected $user;

  /**
   * @var int|string
   */
  protected $playerId = 0;

  /**
   * @var mixed $doSend
   */
  protected $doSend = false;

  /**
   * @var string $deleteRange
   */
  protected $deleteRange = '';

  /**
   * @var db_mysql $db
   */
  protected $db;

  /**
   * @var array
   */
  protected $markedMessageIdList = [];


  /**
   * PageMessage constructor.
   */
  public function __construct() {
    parent::__construct();

    global $sn_message_class_list;

    $this->lang = SN::$lang;
    $this->messageClassList = $sn_message_class_list;

    $this->db = SN::$gc->db;

    $this->loadParams();
  }

  public function route() {
    $this->getUserRef();

    $template = null;

    switch ($this->mode) {
      case static::MESSAGES_MODE_COMPOSE:
        $this->modelCompose();
        $template = $this->viewCompose();
      break;

      /** @noinspection PhpMissingBreakStatementInspection */
      case static::MESSAGES_MODE_DELETE:
        $this->modelDelete();

      case static::MESSAGES_MODE_MESSAGES:
        if (sys_get_param_int('return')) {
          sys_redirect('messages.php');
        }

        $template = $this->viewMessageList();
      break;

      default:
        $template = $this->viewCategories();
      break;
    }

    SnTemplate::display($template, $this->lang['msg_page_header']);
  }

  protected function modelCompose() {
    $this->getRecipientData();

    if ($this->recipient_id_unsafe == $this->playerId) {
      $this->resultAdd($this->lang['msg_err_self_send'], ERR_ERROR);
    }

    if ($this->doSend) {
      if (empty($this->recipient_id_unsafe)) {
        $this->resultAdd($this->lang['msg_err_player_not_found'], ERR_ERROR);
      }

      if (!$this->sendText_unsafe) {
        $this->resultAdd($this->lang['msg_err_no_text'], ERR_ERROR);
      }

      if (!$this->resultCount()) {
        $this->wrapSendPm();

        $this->sendText_unsafe = '';

        $this->resultAdd($this->lang['msg_not_message_sent'], ERR_NONE);
      }
    }
  }

  /**
   * @return template
   */
  protected function viewCompose() {
    $template = SnTemplate::gettemplate('msg_message_compose', true);
    $template->assign_vars([
      'RECIPIENT_ID'   => $this->recipient_id_unsafe,
      'RECIPIENT_NAME' => htmlspecialchars($this->recipient_name_unsafe),
      'SUBJECT'        => htmlspecialchars($this->subject_unsafe),
      'TEXT'           => htmlspecialchars($this->sendText_unsafe),
    ]);

    $this->resultTemplatize($template);

    $recipientIdSafe = db_escape($this->recipient_id_unsafe);
    $message_query = doquery(
      "SELECT * FROM {{messages}}
        WHERE
          `message_type` = '" . MSG_TYPE_PLAYER . "' AND
          ((`message_owner` = '{$this->playerId}' AND `message_sender` = '{$recipientIdSafe}')
          OR
          (`message_sender` = '{$this->playerId}' AND `message_owner` = '{$recipientIdSafe}')) 
        ORDER BY `message_time` DESC LIMIT 20;");
    while ($message_row = db_fetch($message_query)) {
      $template->assign_block_vars('messages', array(
        'ID'   => $message_row['message_id'],
        'DATE' => date(FMT_DATE_TIME, $message_row['message_time'] + SN_CLIENT_TIME_DIFF),
        'FROM' => htmlspecialchars($message_row['message_from']),
        'SUBJ' => htmlspecialchars($message_row['message_subject']),
        'TEXT' => in_array($message_row['message_type'], array(MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE)) && $message_row['message_sender'] ? nl2br(htmlspecialchars($message_row['message_text'])) : nl2br($message_row['message_text']),

        'FROM_ID' => $message_row['message_sender'],
//        'SUBJ_SANITIZED' => htmlspecialchars($message_row['message_subject']),
      ));
    }

    return $template;
  }


  protected function modelDelete() {
    // No range specified - nothing to do
    if ($this->deleteRange == static::MESSAGES_DELETE_RANGE_NONE) {
      return;
    }
    // Сurrent range is CHECKED and NO messages marked - nothing to do
    if ($this->deleteRange == static::MESSAGES_DELETE_RANGE_CHECKED && empty($this->markedMessageIdList)) {
      return;
    }

    $query_add = '';

    switch ($this->deleteRange) {
//      case static::MESSAGES_DELETE_RANGE_UNCHECKED:
      /** @noinspection PhpMissingBreakStatementInspection */
      case static::MESSAGES_DELETE_RANGE_CHECKED:
        $query_add = implode(',', $this->markedMessageIdList);
        if ($query_add) {
          $query_add = "IN ({$query_add})";
//          if ($this->deleteRange == static::MESSAGES_DELETE_RANGE_UNCHECKED) {
//            $query_add = "NOT {$query_add}";
//          }
          $query_add = " AND `message_id` {$query_add}";
        }

      /** @noinspection PhpMissingBreakStatementInspection */
      case static::MESSAGES_DELETE_RANGE_CLASS:
        if ($this->current_class != MSG_TYPE_OUTBOX && $this->current_class != MSG_TYPE_NEW) {
          $query_add .= " AND `message_type` = {$this->current_class}";
        }
      case static::MESSAGES_DELETE_RANGE_ALL:
        $query_add = $query_add ? $query_add : true;
      break;
    }

    if ($this->deleteRange && $query_add) {
      $query_add = $query_add === true ? '' : $query_add;
      doquery("DELETE FROM `{{messages}}` WHERE `message_owner` = '{$this->playerId}'{$query_add};");
    }
  }

  /**
   * @return template
   */
  protected function viewMessageList() {
    require_once('includes/includes/coe_simulator_helpers.php');

    $pager = null;

    if ($this->current_class == MSG_TYPE_OUTBOX) {
      $message_query = "SELECT {{messages}}.message_id, {{messages}}.message_owner, {{users}}.id AS message_sender, {{messages}}.message_time,
          {{messages}}.message_type, {{users}}.username AS message_from, {{messages}}.message_subject, {{messages}}.message_text
       FROM
         {{messages}} LEFT JOIN {{users}} ON {{users}}.id = {{messages}}.message_owner WHERE `message_sender` = '{$this->playerId}' AND `message_type` = 1
       ORDER BY `message_time` DESC;";
    } else {
      if ($this->current_class == MSG_TYPE_NEW) {
        $SubUpdateQry = array();
        foreach ($this->messageClassList as $message_class_id => $message_class) {
          if ($message_class_id != MSG_TYPE_OUTBOX) {
            $SubUpdateQry[] = "`{$message_class['name']}` = '0'";
            $this->user[$message_class['name']] = 0;
          }
        }
        $SubUpdateQry = implode(',', $SubUpdateQry);
        $SubSelectQry = '';
      } else {
        $messageClassNameNew = $this->messageClassList[MSG_TYPE_NEW]['name'];
        $messageClassNameCurrent = $this->messageClassList[$this->current_class]['name'];
        $SubUpdateQry = "`{$messageClassNameCurrent}` = '0', `{$messageClassNameNew}` = `{$messageClassNameNew}` - '{$this->user[$messageClassNameCurrent]}'";
        $SubSelectQry = "AND `message_type` = '{$this->current_class}'";

        $this->user[$messageClassNameNew] -= $this->user[$messageClassNameCurrent];
        $this->user[$messageClassNameCurrent] = 0;
      }

      db_user_set_by_id($this->playerId, $SubUpdateQry);
      $message_query =
        "SELECT m.*, sender.authlevel as sender_auth 
        FROM `{{messages}}` as m 
          LEFT JOIN `{{users}}` as sender on sender.id = m.message_sender
        WHERE m.`message_owner` = '{$this->playerId}' {$SubSelectQry} 
        ORDER BY m.`message_time` DESC;";
    }

    if ($this->showAll) {
      $message_query = $this->db->selectIterator($message_query);
    } else {
      $message_query = new DbSqlPaging($message_query, PAGING_PAGE_SIZE_DEFAULT_MESSAGES, sys_get_param_int(PagingRenderer::KEYWORD));
      $pager = new PagingRenderer($message_query, 'messages.php?mode=show&message_class=' . $this->current_class);
    }

    $wasIgnored = 0;
    $template = SnTemplate::gettemplate('msg_message_list', true);
    foreach ($message_query as $message_row) {
      if(
        $message_row['message_type'] == MSG_TYPE_PLAYER
        &&
        SN::$gc->ignores->isIgnored(floatval($message_row['message_owner']), floatval($message_row['message_sender']))
      ) {
        $wasIgnored++;
        continue;
      }

      $text = $message_row['message_text'];
      if ($message_row['message_json']) {
        switch ($message_row['message_type']) {
          case MSG_TYPE_SPY:
            $text = DecodeEspionage::decode(MissionEspionageReport::fromJson($text));
          break;
          default:
            $text = '{ Unauthorised access - please contact Administration! }';
          break;

        }
      } else {
        if (in_array($message_row['message_type'], [MSG_TYPE_PLAYER, MSG_TYPE_ALLIANCE]) && $message_row['message_sender']) {
          if ($message_row['sender_auth'] >= AUTH_LEVEL_ADMINISTRATOR) {
            $text = SN::$gc->bbCodeParser->expandBbCode($message_row['message_text'], intval($message_row['sender_auth']), HTML_ENCODE_NONE);
          } else {
            $text = htmlspecialchars($message_row['message_text']);
          }
        }
        $text = nl2br($text);
      }

      $template->assign_block_vars('messages', array(
        'ID'   => $message_row['message_id'],
        'DATE' => date(FMT_DATE_TIME, $message_row['message_time'] + SN_CLIENT_TIME_DIFF),
        'FROM' => htmlspecialchars($message_row['message_from']),
        'SUBJ' => htmlspecialchars($message_row['message_subject']),
        'TEXT' => $text,

        'CAN_IGNORE' => $message_row['message_type'] == MSG_TYPE_PLAYER,

        'FROM_ID'        => $message_row['message_sender'],
        'SUBJ_SANITIZED' => htmlspecialchars($message_row['message_subject']),
        'STYLE'          => $this->current_class == MSG_TYPE_OUTBOX ? $this->messageClassList[MSG_TYPE_OUTBOX]['name'] : $this->messageClassList[$message_row['message_type']]['name'],
      ));
    }

    $current_class_text = $this->lang['msg_class'][$this->current_class];

    $template->assign_vars(array(
      "MESSAGE_CLASS"      => $this->current_class,
      "MESSAGE_CLASS_TEXT" => $current_class_text,
      "PAGER_MESSAGES"     => $pager ? $pager->render() : '',
      "MESSAGES_IGNORED"   => $wasIgnored,
    ));

    return $template;
  }

  /**
   * @return template
   */
  protected function viewCategories() {
    $messages_total = [];

    $query = doquery(
      "SELECT `message_owner`, `message_type`, COUNT(`message_id`) AS `message_count` 
         FROM `{{messages}}` 
         WHERE `message_owner` = {$this->playerId} 
         GROUP BY `message_owner`, `message_type` 
         ORDER BY `message_owner` ASC, `message_type`;"
    );
    while ($message_row = db_fetch($query)) {
      $messages_total[$message_row['message_type']] = $message_row['message_count'];
      $messages_total[MSG_TYPE_NEW] += $message_row['message_count'];
    }

    $query = doquery(
      "SELECT COUNT(`message_id`) AS message_count 
         FROM `{{messages}}` 
         WHERE `message_sender` = {$this->playerId} AND `message_type` = " . MSG_TYPE_PLAYER .
      " GROUP BY `message_sender`;",
      '',
      true
    );
    $messages_total[MSG_TYPE_OUTBOX] = intval($query['message_count']);

    $template = SnTemplate::gettemplate('msg_message_class', true);
    foreach ($this->messageClassList as $message_class_id => $message_class) {
      $template->assign_block_vars('message_class', array(
        'ID'     => $message_class_id,
        'STYLE'  => $message_class['name'],
        'TEXT'   => $this->lang['msg_class'][$message_class_id],
        'UNREAD' => $this->user[$message_class['name']],
        'TOTAL'  => intval($messages_total[$message_class_id]),
      ));
    }

    $template->assign_vars(array(
      'PAGE_HINT' => $this->lang['msg_page_hint_class'],
    ));

    return $template;
  }


  protected function getRecipientData() {
    if (!empty($this->recipient_name_unsafe)) {
      $recipient_row = db_user_by_username($this->recipient_name_unsafe);
    }

    if (empty($recipient_row) && !empty($this->recipient_id_unsafe)) {
      $recipient_row = db_user_by_id($this->recipient_id_unsafe);
    }

    if (is_array($recipient_row) && !empty($recipient_row)) {
      $this->recipient_id_unsafe = $recipient_row['id'];
      $this->recipient_name_unsafe = $recipient_row['username'];
    } else {
      $this->recipient_id_unsafe = 0;
      $this->recipient_name_unsafe = '';
    }
  }

  protected function getUserRef() {
    global $user;

    $this->user = &$user;
    $this->playerId = idval($user['id']);
  }

  /**
   * Working on subject
   */
  protected function transformSubject() {
    $re = 0;
    // Removing extra Re:Re:Re:... from subject start
    if ($this->subject_unsafe) {
      $reLength = strlen($this->lang['msg_answer_prefix']);
      while (strpos($this->subject_unsafe, $this->lang['msg_answer_prefix']) === 0) {
        $this->subject_unsafe = trim(substr($this->subject_unsafe, $reLength));
        $re++;
      }
    }

    if ($this->subject_unsafe) {
      $re ? $this->subject_unsafe = $this->lang['msg_answer_prefix'] . $this->subject_unsafe : false;
    } else {
      $this->subject_unsafe = $this->lang['msg_subject_default'];
    }
  }

  protected function loadParams() {
    parent::loadParams();

    $this->current_class = sys_get_param_int('message_class');
    if (!isset($this->messageClassList[$this->current_class])) {
      $this->current_class = 0;
      $this->mode = static::MESSAGES_MODE_CATEGORIES;
    } else {
      $this->mode = sys_get_param_str('msg_delete') ? static::MESSAGES_MODE_DELETE : sys_get_param_str('mode');
    }

    if ($this->showAll = sys_get_param_str('msg_show_all') ? true : false) {
      $this->mode = static::MESSAGES_MODE_MESSAGES;
    }

    $this->loadParamsCompose();
    $this->loadParamsDelete();
  }

  protected function loadParamsCompose() {
    $this->recipient_name_unsafe = sys_get_param_str_unsafe('recipient_name');
    $this->recipient_id_unsafe = sys_get_param_id('id');
    // Removing starting and trailing blank chars
    $this->subject_unsafe = trim(sys_get_param_str_unsafe('subject'));
    $this->transformSubject();
    $this->sendText_unsafe = sys_get_param_str_unsafe('text');
    $this->doSend = sys_get_param_str('msg_send');
  }

  protected function loadParamsDelete() {
    $this->deleteRange = sys_get_param_str('message_range', static::MESSAGES_DELETE_RANGE_NONE);
    // Incorrect range - do nothing
    if (!in_array($this->deleteRange, static::MESSAGES_DELETE_RANGES_ALLOWED)) {
      return;
    }

    $this->markedMessageIdList = [];
    $unsafeMarkList = sys_get_param('mark', []);

    foreach ($unsafeMarkList as $unsafeMark) {
      if (!empty($unsafeMark = idval($unsafeMark))) {
        $this->markedMessageIdList[] = $unsafeMark;
      }
    }
  }

  protected function wrapSendPm() {
    msg_send_simple_message(
      $this->recipient_id_unsafe,
      $this->playerId,
      SN_TIME_NOW,
      MSG_TYPE_PLAYER,
      "{$this->user['username']} [{$this->user['galaxy']}:{$this->user['system']}:{$this->user['planet']}]",
      $this->subject_unsafe,
      $this->sendText_unsafe,
      STRING_NEED_ESCAPING
    );
  }

}
