<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

// Code - CLEAR!
// Template - CLEAR!

$parse = $lang;
$parse['searchtext'] = $POST_searchtext;
$page = parsetemplate(gettemplate('ali_search'), $parse);

if ($POST_searchtext) {
  $search = doquery("SELECT * FROM {{alliance}} WHERE `ally_name` LIKE '%{$POST_searchtext}%' OR `ally_tag` LIKE '%{$POST_searchtext}%' LIMIT 30");

  if (mysql_num_rows($search)) {
    $template = gettemplate('ali_search_result_row');

    $entry['ali_req_notAllowed'] = $lang['ali_req_notAllowed'];
    $entry['ali_req_make'] = $lang['ali_req_make'];
    while ($s = mysql_fetch_assoc($search)) {
      $entry['ally_id'] = $s['id'];
      $entry['ally_tag'] = $s['ally_tag'];
      $entry['ally_name'] = $s['ally_name'];
      $entry['ally_members'] = $s['ally_members'];

      if($s['ally_request_notallow']){
        $entry['applicationLink'] = 'class="hide"';
        $entry['applicationNone'] = '';
      }else{
        $entry['applicationLink'] = '';
        $entry['applicationNone'] = 'class="hide"';
      }

      $parse['result'] .= parsetemplate($template, $entry);
    }

    $page .= parsetemplate(gettemplate('ali_search_result'), $parse);
  }
}

display($page, $lang['ali_searchAlly']);

?>
