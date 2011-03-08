<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

$template = gettemplate('ali_search', true);

$ali_search_text = sys_get_param_str('searchtext');
if ($ali_search_text)
{
  $template->assign_var('SEARCH_TEXT', $ali_search_text);

  $search = doquery("SELECT DISTINCT * FROM {{alliance}} WHERE `ally_name` LIKE '%{$ali_search_text}%' OR `ally_tag` LIKE '%{$ali_search_text}%' LIMIT 30");
  if (mysql_num_rows($search))
  {
    while ($ally_row = mysql_fetch_assoc($search))
    {
      $template->assign_block_vars('alliances', array(
        'ID' => $ally_row['id'],
        'TAG' => $ally_row['ally_tag'],
        'NAME' => $ally_row['ally_name'],
        'MEMBERS' => $ally_row['ally_members'],
        'NO_REQUESTS' => $ally_row['ally_request_notallow']
      ));
    }
  }
}

display(parsetemplate($template), $lang['ali_search_title']);

?>
