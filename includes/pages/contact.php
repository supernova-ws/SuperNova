<?php

/**
 * contact.php
 *
 * List of authorities: admin, ops, moders
 *
 */


$allow_anonymous = true;

//$sn_mvc['model']['chat'][] = 'sn_chat_model';
$sn_mvc['view']['contact'][] = 'sn_contact_view';

function sn_contact_view($template = null)
{
  global $template_result, $lang;

  $template = gettemplate('contact', $template);

  $query = doquery("SELECT `username`, `email`, `authlevel` FROM {{users}} WHERE `authlevel` != '0' ORDER BY `authlevel` DESC;");

  while($row = mysql_fetch_assoc($query))
  {
    $template_result['.']['contact'][] = array(
      'NAME'     => $row['username'],
      'LEVEL'    => $lang['user_level'][$row['authlevel']],
      'EMAIL'    => $row['email'],
    );
  }

  $template_result['PAGE_HEADER'] = $lang['ctc_title'];

  return $template;
}

?>
