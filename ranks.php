<?php
/**
 * Created by Gorlum 18.02.2018 16:42
 */

include_once('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = SnTemplate::gettemplate('rank_list', true);

for ($i = 0; $i <= 20; $i++) {
  $template->assign_block_vars('player_rank', [
    'ID' => $i,
    'NAME' => $lang['ranks'][$i],
    'SELECTED' => $i == SN::$gc->playerLevelHelper->getPointLevel($user['total_points'], $user['authlevel']),
  ]);
}

$template->assign_vars([
  'PAGE_HEADER' => $lang['rank_page_title'],
]);

SnTemplate::display($template, $lang['rank_page_title']);
