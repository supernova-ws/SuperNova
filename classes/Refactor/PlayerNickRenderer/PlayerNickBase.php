<?php

/** Created by Gorlum 24.01.2026 16:02 */

namespace Refactor\PlayerNickRenderer;

use DateTime;
use SN;

final class PlayerNickBase extends AbstractPlayerNickRenderStep {
  /**
   * @param PlayerNickRenderContext $context
   *
   * @return array
   */
  public function renderToArray(PlayerNickRenderContext $context): array {
    $options = &$context->options;
    $user    = &$context->user;

    if (!empty($options['birthday']) && !empty($user['user_birthday'])) {
      $birthday = DateTime::createFromFormat('Y-m-d', $user['user_birthday']);
      $today    = new DateTime();

      if ($birthday && $birthday->format('m-d') === $today->format('m-d')) {
        $result[NICK_BIRTHDAY] = '';
      }
    }

    if (!empty($options['gender']) && isset($user['gender'])) {
      $result[NICK_GENDER] = $user['gender'] == GENDER_UNKNOWN ? 'unknown' : ($user['gender'] == GENDER_FEMALE ? 'female' : 'male');
    }

    if (!empty($options['vacancy']) && !empty($user['vacation'])) {
      $result[NICK_VACATION] = $user['vacation'];
    }

    if (!empty($options['player_rank']) && isset($user['total_points'])) {
      $result[NICK_RANK] = SN::$gc->playerLevelHelper->getPointLevel($user['total_points'], $user['authlevel']);
    }

    if (!empty($options[NICK_RANK_NO_TEXT])) {
      $result[NICK_RANK_NO_TEXT] = $options[NICK_RANK_NO_TEXT];
    }

    if (!empty($options['color']) && ($user_auth_level = $user['authlevel'])) {
      $result[NICK_AUTH_LEVEL] = $user_auth_level;
    }

    if (!empty($options['class'])) {
      $result[NICK_CLASS] = (isset($options[NICK_CLASS]) ? ' ' . $options[NICK_CLASS] : '') . $options['class'];
    }

    if (!empty($user['ally_tag']) && !empty($options['ally'])) {
      $result[NICK_ALLY] = $user['ally_tag'];
    }

    $result[NICK_NICK] = $user['username'];

    return $context->result = $result;
  }

  public function renderArrayToHtml(PlayerNickRenderContextHtml $context): array {
    $nick_array = &$context->nick_array;

    static $iconCache = array();

    if (empty($iconCache['gender_' . $nick_array[NICK_GENDER]])) {
      $iconCache['gender_' . $nick_array[NICK_GENDER]] = SN::$gc->skinModel->getImageCurrent("gender_{$nick_array[NICK_GENDER]}|html");
      $iconCache['icon_vacation'] = SN::$gc->skinModel->getImageCurrent('icon_vacation|html');
      $iconCache['icon_birthday'] = SN::$gc->skinModel->getImageCurrent('icon_birthday|html');
    }

    // ALL STRING ARE UNSAFE!!!
    if (isset($nick_array[NICK_BIRTHDAY])) {
      $result[NICK_BIRTHDAY] = $iconCache['icon_birthday'];
    }

    if (isset($nick_array[NICK_VACATION])) {
      $result[NICK_VACATION] = $iconCache['icon_vacation'];
    }

    if (isset($nick_array[NICK_RANK])) {
      $result[NICK_RANK] = SN::$gc->playerLevelHelper->renderRank($nick_array[NICK_RANK], $nick_array[NICK_RANK_NO_TEXT]);
    }

    if (isset($nick_array[NICK_GENDER])) {
      $result[NICK_GENDER] = $iconCache['gender_' . $nick_array[NICK_GENDER]];
    }

    $highlight = null;
//    if (isset($nick_array[NICK_PREMIUM])) {
//      $highlight = SN::$config->chat_highlight_premium;
//    }

    if (isset($nick_array[NICK_AUTH_LEVEL])) {
      switch ($nick_array[NICK_AUTH_LEVEL]) {
        case 4:
          $highlight = SN::$config->chat_highlight_developer;
        break;

        case 3:
          $highlight = SN::$config->chat_highlight_admin;
        break;

        case 2:
          $highlight = SN::$config->chat_highlight_operator;
        break;

        case 1:
          $highlight = SN::$config->chat_highlight_moderator;
        break;
      }
    }

    if ($highlight) {
      [$result[NICK_HIGHLIGHT], $result[NICK_HIGHLIGHT_END]] = explode('$1', $highlight);
    }

    if (isset($nick_array[NICK_CLASS])) {
      $result[NICK_CLASS] = '<span ' . $nick_array[NICK_CLASS] . '>';
      $result[NICK_CLASS_END] = '</span>';
    }

    $result[NICK_NICK] = sys_safe_output($nick_array[NICK_NICK]);

    if (isset($nick_array[NICK_ALLY])) {
      $result[NICK_ALLY] = '[' . sys_safe_output($nick_array[NICK_ALLY]) . ']';
    }

//    $result[NICK_HTML] = true;

    return $context->result = $result;
  }

}
