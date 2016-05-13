<?php

/**
 *
 * List of authorities: admin, ops, moders
 *
 */

classSupernova::$sn_mvc['view']['contact'][] = 'sn_contact_view';

function sn_contact_view($template = null) {
  global $template_result;

  $template = gettemplate('contact', $template);

  $query = DBStaticUser::db_user_list("`authlevel` > 0 ORDER BY `authlevel` ASC");

  // while($row = db_fetch($query))
  foreach($query as $row) {
    $template_result['.']['contact'][] = array(
      'NAME'  => $row['username'],
      'LEVEL' => classLocale::$lang['user_level'][$row['authlevel']],
      'EMAIL' => $row['email'],
    );
  }

  $template_result['PAGE_HEADER'] = classLocale::$lang['ctc_title'];

  return $template;
}
