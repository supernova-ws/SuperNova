<?php

/**
 * admin/adm_message_list.php
 *
 * @version 2
 * @copyright 2014 by Gorlum for http://supernova.ws/
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_messagelist', true);

$int_type_selected = sys_get_param_int('int_type_selected', -1);
$allowed_types = array(
  -1 => array(
    'VALUE' => -1,
    'TEXT' => $lang['adm_pay_filter_all']
  ),
);
$template->assign_block_vars('int_type_selected', $allowed_types[-1]);
foreach($sn_message_class_list as $key => $value)
{
  if($key == MSG_TYPE_NEW || $key == MSG_TYPE_OUTBOX)
  {
    continue;
  }

  $template->assign_block_vars('int_type_selected', $allowed_types[$key] = array(
    'VALUE' => $key,
    'TEXT' => $lang['msg_class'][$key],
  ));
}


$message_delete = sys_get_param_id('msg_del');
if(sys_get_param('str_delete_selected') && is_array($message_delete = sys_get_param('selected')) && !empty($message_delete))
{
  $message_delete = implode(', ', $message_delete);
}

if($message_delete)
{
  doquery("DELETE FROM {{messages}} WHERE `message_id` in ({$message_delete});");
  $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['mlst_messages_deleted'], $message_delete)));
}


if(sys_get_param('str_delete_date') && checkdate($month = sys_get_param_id('delete_month'), $day = sys_get_param_id('delete_day'), $year = sys_get_param_id('delete_year')))
{
  $delete_date = "{$year}-{$month}-{$day}";
  doquery("DELETE FROM {{messages}} WHERE message_time <= UNIX_TIMESTAMP('{$delete_date}')" . ($int_type_selected >= 0 ? " AND `message_type` = {$int_type_selected}" : ''));
  $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['mlst_messages_deleted_date'], $allowed_types[$int_type_selected]['TEXT'], $delete_date)));
}


$page_max = doquery('SELECT COUNT(*) AS `max` FROM {{messages}}' . ($int_type_selected >= 0 ? " WHERE `message_type` = {$int_type_selected};" : ''), true);
$page_max = ceil($page_max['max'] / 25);

$int_page_current = min(sys_get_param_id('int_page_current', 1), $page_max);

if(sys_get_param('page_prev') && $int_page_current > 1)
{
  $int_page_current--;
}
elseif(sys_get_param('page_next') && $int_page_current < $page_max)
{
  $int_page_current++;
}

for($i = 1; $i <= $page_max; $i++)
{
  $template->assign_block_vars('page', array('NUMBER' => $i));
}


/*
$Prev       = ( !empty($_POST['prev'])   ) ? true : false;
$Next       = ( !empty($_POST['next'])   ) ? true : false;
$DelSel     = ( !empty($_POST['delsel']) ) ? true : false;
$DelDat     = ( !empty($_POST['deldat']) ) ? true : false;
$CurrPage   = ( !empty($_POST['curr'])   ) ? $_POST['curr'] : 1;
$SelType    = $_POST['type'];
$SelPage    = $_POST['page'];

$ViewPage = 1;
if($type_selected != $SelType)
{
  $type_selected = $SelType;
  $ViewPage = 1;
}
elseif($CurrPage != $SelPage)
{
  $ViewPage = ( !empty($SelPage) ) ? $SelPage : 1;
}

if ($Prev == true)
{
    $CurrPage -= 1;
    if ($CurrPage >= 1) {
        $ViewPage = $CurrPage;
    } else {
        $ViewPage = 1;
    }
}
elseif($Next   == true)
{
    $Mess      = doquery("SELECT COUNT(*) AS `max` FROM {{messages}} WHERE `message_type` = '". $int_type_selected ."';", '', true);
    $MaxPage   = ceil ( ($Mess['max'] / 25) );
    $CurrPage += 1;
    if ($CurrPage <= $MaxPage) {
    $ViewPage = $CurrPage;
    } else {
    $ViewPage = $MaxPage;
    }
}
elseif($DelSel == true)
{
    foreach($_POST['sele'] as $MessId => $Value) {
        if ($Value = "on") {
            doquery ( "DELETE FROM {{messages}} WHERE `message_id` = '". $MessId ."';");
        }
    }
}
elseif($DelDat == true)
{
    $SelDay    = $_POST['selday'];
    $SelMonth  = $_POST['selmonth'];
    $SelYear   = $_POST['selyear'];
    $LimitDate = mktime (0,0,0, $SelMonth, $SelDay, $SelYear );
    if ($LimitDate != false) {
        doquery ( "DELETE FROM {{messages}} WHERE `message_time` <= '". $LimitDate ."';");
        doquery ( "DELETE FROM {{rw}} WHERE `time` <= '". $LimitDate ."';");
    }
}
*/

$StartRec = ($int_page_current - 1) * 25;

$Messages = doquery($q = "SELECT
  message_id as `ID`,
  message_from as `FROM`,
  message_owner as `OWNER_ID`,
  u.username as `OWNER_NAME`,
  message_text as `TEXT`,
  FROM_UNIXTIME(message_time) as `TIME`
FROM
  {{messages}} AS m
  LEFT JOIN {{users}} AS u ON u.id = m.message_owner " .
($int_type_selected >= 0 ? "WHERE `message_type` = {$int_type_selected} " : '') .
"ORDER BY
  `message_id` DESC
LIMIT
  {$StartRec}, 25;");
while($row = mysql_fetch_assoc($Messages))
{
  $row['FROM'] = htmlentities($row['FROM'], ENT_COMPAT, 'UTF-8');
  $row['OWNER_NAME'] = htmlentities($row['OWNER_NAME'], ENT_COMPAT, 'UTF-8');
  $row['TEXT'] = nl2br($row['TEXT']);
  $template->assign_block_vars('message', $row);
}

$template->assign_vars(array(
  'PAGE_MAX' => $page_max,
  'PAGE_CURRENT' => $int_page_current,
  'TYPE_SELECTED' => $int_type_selected,
));

display(parsetemplate($template, $parse), $lang['mlst_title'], false, '', true);
