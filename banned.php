<?php

/**
 * banned.php
 *
 * List of all issued bans
 *
 * 2.0 (c) copyright 2010-2011 by Gorlum for http://supernova.ws
 *  [!] Full rewrite
 *  [~] Complies with PCG1
 *  [~] Utilize PTE
 * @version 1.0 Created by e-Zobar (XNova Team). All rights reversed (C) 2008
 *
 */

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = SnTemplate::gettemplate('banned_body', true);

$query = doquery("SELECT * FROM {{banned}} ORDER BY `ban_id` DESC;");
$i=0;
while($ban_row = db_fetch($query))
{
  $template->assign_block_vars('banlist', array(
    'USER_NAME'   => $ban_row['ban_user_name'],
    'REASON'      => $ban_row['ban_reason'],
    'FROM'        => $ban_row['ban_time'] ? date(FMT_DATE_TIME, $ban_row['ban_time']) : '--',
    'UNTIL'       => date(FMT_DATE_TIME, $ban_row['ban_until']),
    'ISSUER_NAME' => $ban_row['ban_issuer_name']
  ));
  $i++;
}

$template->assign_var('BANNED_COUNT', $i);
SnTemplate::display($template);
