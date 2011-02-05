<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if($user_request['id_user']){
  include('ali_external_request.inc');
} else{
  switch($mode){
    case 'make': include('ali_external_create_ally.inc'); break;
    case 'search': include('ali_external_search.inc'); break;
    case 'apply': include('ali_external_request.inc'); break;
    default:
      $page .= parsetemplate(gettemplate('ali_external'), $lang);
      $page .= parsetemplate(gettemplate('ali_search'), $lang);
      display($page, $lang['alliance']);
  }
}

?>
