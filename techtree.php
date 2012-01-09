<?php

/**
 * techtree.php
 *
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$HeadTpl = gettemplate('techtree_head');
$RowTpl  = gettemplate('techtree_row');

foreach($lang['tech'] as $Element => $ElementName)
{
  $parse            = array();
  $parse['tt_name'] = $ElementName;
  if (!isset($sn_data[$Element]['name']))
  {
    $parse['Requirements']  = $lang['Requirements'];
    $page                  .= parsetemplate($HeadTpl, $parse);
  }
  else
  {
    if(isset($sn_data[$Element]['require']) && !(in_array($Element, $sn_data['groups']['mercenaries']) && $config->empire_mercenary_temporary))
    {
      $parse['required_list'] = "";
      foreach($sn_data[$Element]['require'] as $ResClass => $Level)
      {
        $actual_level = 0;
        if(in_array($ResClass, $sn_data['groups']['mercenaries']))
        {
          $actual_level = mrc_get_level($user, $planetrow, $ResClass);
        }
        elseif(isset($user[$sn_data[$ResClass]['name']]))
        {
          $actual_level = $user[$sn_data[$ResClass]['name']];
        }
        elseif(isset($planetrow[$sn_data[$ResClass]['name']]))
        {
          $actual_level = $planetrow[$sn_data[$ResClass]['name']];
        }
        elseif(in_array($ResClass, $sn_data['groups']['governors']) && $planetrow['PLANET_GOVERNOR_ID'] == $ResClass)
        {
          $actual_level = $planetrow['PLANET_GOVERNOR_LEVEL'];
        }
        $parse['required_list'] .= "<font color=\"" . ($actual_level >= $Level ? '#00ff00' : '#ff0000') . "\">{$lang['tech'][$ResClass]} ( {$lang['level']} {$actual_level} / {$Level} )</font><br>";
      }
    }
    else
    {
      $parse['required_list'] = "";
    }
    $parse['tt_info']   = $Element;
    $page              .= parsetemplate($RowTpl, $parse);
  }
}

$parse['techtree_list'] = $page;

display(parsetemplate(gettemplate('techtree_body'), $parse), $lang['Tech']);

// -----------------------------------------------------------------------------------------------------------
// History version
// - 1.0 mise en conformité code avec skin XNova
// - 1.1 ajout lien pour les details des technos
// - 1.2 suppression du lien details ou il n'est pas necessaire

?>
