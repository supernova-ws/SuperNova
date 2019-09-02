<?php

/**
 * List of authorities: admin, ops, moders
 */

function sn_contact_view($template = null) {
  global $template_result, $lang;

  $template = SnTemplate::gettemplate('contact', $template);

  $query = db_user_list("`authlevel` > 0 ORDER BY `authlevel` ASC");

  foreach ($query as $row) {
    $template_result['.']['contact'][] = array(
      'ID'  => $row['id'],
      'NAME'  => $row['username'],
      'LEVEL' => $lang['user_level'][$row['authlevel']],
      'EMAIL' => $row['email'],
    );
  }

  $template_result['PAGE_HEADER'] = $lang['ctc_title'];

  return $template;
}
