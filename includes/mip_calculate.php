<?php

/**
 * Copyright (c) 2009 by Gorlum for ogame.triolan.com.ua
 *       OpenSource as long as you don't remove this Copyright
 * V2 2009-10-10
 * V3 2009-11-13
 */

if (!defined('INSIDE')) {
  die("Hacking attempt");
}

includeLang('mip');
include_once($ugamela_root_path . 'includes/raketenangriff.'.$phpEx);

// doquery("LOCK TABLE {{table}}aks WRITE, {{table}}lunas WRITE, {{table}}rw WRITE, {{table}}errors WRITE, {{table}}messages WRITE, {{table}}fleets WRITE, {{table}}planets WRITE, {{table}}galaxy WRITE ,{{table}}users WRITE", "");
// iraks,  planets, users, messages

if (isset($resource) && !empty($resource[401])) {
  $iraks = doquery("SELECT * FROM {{table}} WHERE `zeit` <= '" . $time_now . "'", 'iraks');

  while ($rowMIPFleet = mysql_fetch_array($iraks)) {
    if ($rowMIPFleet['zeit'] != '' && $rowMIPFleet['galaxy'] != '' && $rowMIPFleet['system'] != '' && $rowMIPFleet['planet'] != '' && is_numeric($rowMIPFleet['owner']) && is_numeric($rowMIPFleet['zielid']) && is_numeric($rowMIPFleet['anzahl']) && !empty($rowMIPFleet['anzahl'])) {
      $rowTargetPlanet = doquery("SELECT * FROM `{{table}}` WHERE
                `galaxy` = '{$rowMIPFleet['galaxy']}' AND
                `system` = '{$rowMIPFleet['system']}' AND
                `planet` = '{$rowMIPFleet['planet']}' AND
                `planet_type` = 1", 'planets', true);

      $rowDefender = doquery("SELECT `shield_tech`, `defence_tech` FROM `{{table}}` WHERE
                `id` = '{$rowMIPFleet['zielid']}'", 'users', true);

      $rowAttacker = doquery("SELECT `military_tech` FROM `{{table}}` WHERE
                `id` = '{$rowMIPFleet['owner']}'", 'users', true);

      if ($rowTargetPlanet['id']) {
        // $rowDefender = mysql_fetch_array($qDefender);
        // $rowAttacker = mysql_fetch_array($qAttacker);
        // $rowTargetPlanet = mysql_fetch_array($qTargetPlanet);

        $planetDefense = array(
          400 => array( 0, 'shield' => 0, 'structure' => 0),
          401 => array( $rowTargetPlanet['misil_launcher'], 'shield' => 0, 'structure' => 0),
          402 => array( $rowTargetPlanet['small_laser'], 'shield' => 0, 'structure' => 0),
          403 => array( $rowTargetPlanet['big_laser'], 'shield' => 0, 'structure' => 0),
          404 => array( $rowTargetPlanet['gauss_canyon'], 'shield' => 0, 'structure' => 0),
          405 => array( $rowTargetPlanet['ionic_canyon'], 'shield' => 0, 'structure' => 0),
          406 => array( $rowTargetPlanet['buster_canyon'], 'shield' => 0, 'structure' => 0),
          407 => array( $rowTargetPlanet['small_protection_shield'], 'shield' => 0, 'structure' => 0),
          408 => array( $rowTargetPlanet['big_protection_shield'], 'shield' => 0, 'structure' => 0),
          409 => array( $rowTargetPlanet['planet_protector'], 'shield' => 0, 'structure' => 0),
        );

        $message = '';
        $MIs = $rowTargetPlanet[$resource[502]]; // Number of interceptors
        $MIPs = $rowMIPFleet['anzahl']; // Number of MIP
        $qUpdate = "UPDATE `{{table}}` SET {$resource[502]} = ";
        if ($MIs >= $MIPs) {
          $message = $lang['mip_all_destroyed'];
          $qUpdate .= "{$resource[502]} - {$MIPs} ";
        } else {
          if ($MIs) {
            $message = sprintf($lang['mip_destroyed'], $MIs);
          };
          $qUpdate .= "0";
          $message .= $lang['mip_defense_destroyed'];

          $irak = MIPAttack($rowDefender, $rowAttacker, $MIPs - $MIs, $planetDefense, $rowMIPFleet['primaer']);

          foreach ($irak['structures'] as $key => $structure) {
            $destroyed = $planetDefense[$key][0] - $structure[0];
            if ($key > 400 && $destroyed) {
              $message .= "&nbsp;&nbsp;" . $lang['tech'][$key] . " - " . $destroyed . " " . $lang['quantity'] . "<br>";
              $qUpdate .= ", `" . $resource[$key] . "` = " . $structure[0];
            };
          };

          $qUpdate .= ", `metal`=`metal`+".$irak['metal'].", `crystal`=`crystal`+".$irak['crystal'];
          $message .= $lang['mip_recycled'] .
            $lang['Metal']. ": " . $irak['metal'] . ", ".
            $lang['Crystal']. ": " . $irak['crystal'] . "<br>";
        };

        $qUpdate .= " WHERE `id` = " . $rowTargetPlanet['id'] . ";";
        doquery($qUpdate, 'planets');

        $rowSourcePlanet = doquery("SELECT `name` FROM `{{table}}` WHERE ".
          "`galaxy` = '{$rowMIPFleet['galaxy_angreifer']}' AND `system` = '{$rowMIPFleet['system_angreifer']}' AND `planet` = '{$rowMIPFleet['planet_angreifer']}' and planet_type = 1",
          'planets', true);

        $name_deffer = $rowTargetPlanet['name'];

        $message_vorlage = sprintf($lang['mip_body_attack'], $rowMIPFleet['anzahl'],
          addslashes($rowSourcePlanet['name']), $rowMIPFleet['galaxy_angreifer'], $rowMIPFleet['system_angreifer'], $rowMIPFleet['planet_angreifer'],
          addslashes($name_deffer), $rowMIPFleet['galaxy'], $rowMIPFleet['system'], $rowMIPFleet['planet']);

        if (empty($message))
          $message = $lang['mip_no_defense'];

        doquery("INSERT INTO {{table}} SET
            `message_owner`=".$rowMIPFleet['owner'].", `message_sender`=0, `message_time`=UNIX_TIMESTAMP(), `message_type`=0,
            `message_from`= '".$lang['mip_sender_amd']."',
            `message_subject`= '".$lang['mip_subject_amd']."',
            `message_text`='{$message_vorlage}{$message}'" , 'messages');
        doquery("UPDATE {{table}} SET new_message=new_message+1 WHERE id=".$rowMIPFleet['owner'], 'users');

        doquery("INSERT INTO {{table}} SET
            `message_owner`=".$rowMIPFleet['zielid'].", `message_sender`=0, `message_time`=UNIX_TIMESTAMP(), `message_type`=3,
            `message_from`= '".$lang['mip_sender_amd']."',
            `message_subject`= '".$lang['mip_subject_amd']."',
            `message_text`='{$message_vorlage}{$message}'" , 'messages');
        doquery("UPDATE {{table}} SET new_message=new_message+1 WHERE id=".$rowMIPFleet['zielid'], 'users');

      };
    };
    doquery("DELETE FROM {{table}} WHERE id = '" . $rowMIPFleet['id'] . "'", 'iraks');
  };
};
?>