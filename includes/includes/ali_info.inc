<?php
  // Evitamos errores casuales xD
  // query
  if ($tag) {
    $allyrow = doquery("SELECT * FROM {{table}} WHERE ally_tag='{$tag}'", "alliance", true);
  } elseif ($id_ally) {
    $allyrow = doquery("SELECT * FROM {{table}} WHERE id='{$id_ally}'", "alliance", true);
  }
  // Si no existe
  if (!$allyrow)
    message($lang['ali_sys_notFound'], $lang['Ally_info_1']);

  extract($allyrow);

  if ($ally_image) {
    $ally_image = "<tr><th colspan=2><img src=\"{$ally_image}\"></td></tr>";
  }

  if (!$ally_description)
    $ally_description = $lang['Ally_nodescription'];

  $ally_description = "<tr><th colspan=2 height=100>{$ally_description}</th></tr>";

  if ($ally_web) {
    $ally_web = "<tr>
    <th>{$lang['Initial_page']}</th>
    <th><a href=\"{$ally_web}\">{$ally_web}</a></th>
    </tr>";
  }

  $lang['ally_member_scount'] = $ally_members;
  $lang['ally_name'] = $ally_name;
  $lang['ally_tag'] = $ally_tag;
  // codigo raro
  $patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
  $replacements[] = '<font color="\1">\2</font>';
  $patterns[] = '#\[img\](.*?)\[/img\]#Smi';
  $replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
  $patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
  $replacements[] = '<font color="\1">';
  $patterns[] = "#\[/f\]#Ssi";
  $replacements[] = '</font>';
  $ally_description = preg_replace($patterns, $replacements, $ally_description);

  $lang['ally_description'] = nl2br($ally_description);
  $lang['ally_image'] = $ally_image;
  $lang['ally_web'] = $ally_web;
  $lang['ally_id'] = $id_ally;

  if ($user['ally_id'] == 0) {
  } else
    $lang['ally_application'] = 'class="hide"';

  $page .= parsetemplate(gettemplate('ali_external_info'), $lang);
  display($page, str_replace('%s', $ally_name, $lang['Info_of_Alliance']));
?>