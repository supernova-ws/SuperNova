<?php
/**
 * Created by Gorlum 19.08.2019 19:12
 */

namespace Pages\Helpers;


use Alliance\Alliance;
use classLocale;
use mysqli_result;
use SN;
use SnTemplate;
use template;

class PageHelperAlly {
  /**
   * @param             $mode
   * @param array       $user
   * @param classLocale $lang
   */

  public static function pageExternalSearch($mode, array $user, classLocale $lang) {
    if (!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
      SN::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
    }

    $template = SnTemplate::gettemplate('ali_search', true);

    $ali_search_text = sys_get_param_str('searchtext');

    if ($ali_search_text) {
      $template->assign_var('SEARCH_TEXT', $ali_search_text);

      $search = doquery("SELECT DISTINCT * FROM {{alliance}} WHERE `ally_name` LIKE '%{$ali_search_text}%' OR `ally_tag` LIKE '%{$ali_search_text}%' LIMIT 30");
      if (db_num_rows($search)) {
        PageHelperAlly::allyFetchFromResult($template, $search, $user['total_points']);
      }
    }

    self::externalSearchRecommend($user, $template);

    SnTemplate::display($template, $lang['ali_search_title']);
  }


  /**
   * @param template           $template
   * @param mysqli_result|null $result
   * @param int                $playerPoints
   */
  public static function allyFetchFromResult(template $template, $result, $playerPoints = 0) {
    while ($ally_row = db_fetch($result)) {
      $perPlayer  = $ally_row['total_points'] / $ally_row['ally_members'];
      $pointsDiff = round($playerPoints - $perPlayer);

      $pointsRate = $playerPoints / $perPlayer;
      $pointsRate = round($pointsRate, 2);

//    $pointsRate && $pointsRate < 1 ? $pointsRate = "1 / " . round(1 / $pointsRate, 2) : false;

      $template->assign_block_vars('alliances', array(
        'ID'          => $ally_row['id'],
        'TAG'         => $ally_row['ally_tag'],
        'NAME'        => $ally_row['ally_name'],
        'MEMBERS'     => $ally_row['ally_members'],
        'NO_REQUESTS' => $ally_row['ally_request_notallow'],
        'DIFF'        => $pointsDiff,
        'RATE'        => $pointsRate,
      ));
    }
  }

  /**
   * @param array    $user
   * @param template $template
   */
  public static function externalSearchRecommend(array $user, template $template) {
    if (empty($user['ally_id'])) {
      $recommended = Alliance::recommend($user['total_points']);
      if (db_num_rows($recommended)) {
        $template->assign_block_vars('alliances', array(
          'ID' => -1,
        ));
        PageHelperAlly::allyFetchFromResult($template, $recommended, $user['total_points']);
      }
    }

    $template->assign_vars(['PAGE_HINT' => SN::$lang['ali_search_result_tip']]);
  }

}
