<?php

/**
 * records.php
 *
 * 1.4st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.4
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);
if (HIDE_BUILDING_RECORDS) return;

  includeLang('records');

  $RecordTpl = gettemplate('records_body');
  $HeaderTpl = gettemplate('records_section_header');
  $TableRows = gettemplate('records_section_rows');

  $parse['rec_title'] = $lang['rec_title'];

  $bloc['section']    = $lang['rec_build'];
  $bloc['player']     = $lang['rec_playe'];
  $bloc['level']      = $lang['rec_level'];
  $parse['building']  = parsetemplate( $HeaderTpl, $bloc);

  $bloc['section']    = $lang['rec_specb'];
  $bloc['player']     = $lang['rec_playe'];
  $bloc['level']      = $lang['rec_level'];
  $parse['buildspe']  = parsetemplate( $HeaderTpl, $bloc);

  $bloc['section']    = $lang['rec_techn'];
  $bloc['player']     = $lang['rec_playe'];
  $bloc['level']      = $lang['rec_level'];
  $parse['research']  = parsetemplate( $HeaderTpl, $bloc);

  $bloc['section']    = $lang['rec_fleet'];
  $bloc['player']     = $lang['rec_playe'];
  $bloc['level']      = $lang['rec_nbre'];
  $parse['fleet']     = parsetemplate( $HeaderTpl, $bloc);

  $bloc['section']    = $lang['rec_defes'];
  $bloc['player']     = $lang['rec_playe'];
  $bloc['level']      = $lang['rec_nbre'];
  $parse['defenses']  = parsetemplate( $HeaderTpl, $bloc);


  foreach($lang['tech'] as $Element => $ElementName) {
    if ($ElementName != "") {
      if ($resource[$Element] != "") {
        // Je sais bien qu'il n'y a aucune raison de blinder ce test ...
        // Mais avec les zozos qui vont le pomper ... Mieux vaut prevoir que guerir !!
        if       ($Element >=   1 && $Element <=  39 || $Element == 44) {
          // Batiment
          $PlanetRow          = doquery ("SELECT `id_owner`, `". $resource[$Element] ."` AS `current` FROM {{table}} WHERE `". $resource[$Element]. "` = (SELECT MAX(`". $resource[$Element] ."`) FROM {{table}} WHERE `id_level` = '0');", 'planets', true);
          $UserRow            = doquery ("SELECT `username` FROM {{table}} WHERE `id` = '".$PlanetRow['id_owner']."';", 'users', true);
          $Row['element']     = $ElementName;
          $Row['winner']      = ($PlanetRow['current'] != 0) ? $UserRow['username'] : $lang['rec_rien'];
          $Row['count']       = ($PlanetRow['current'] != 0) ? pretty_number( $PlanetRow['current'] ) : $lang['rec_rien'];
          $parse['building'] .= parsetemplate( $TableRows, $Row);
        } elseif ($Element >=  41 && $Element <=  99 && $Element != 44) {
          // Batiment spéciaux
          $PlanetRow          = doquery ("SELECT `id_owner`, `". $resource[$Element] ."` AS `current` FROM {{table}} WHERE `". $resource[$Element]. "` = (SELECT MAX(`". $resource[$Element] ."`) FROM {{table}} WHERE `id_level` = '0');", 'planets', true);
          $UserRow            = doquery ("SELECT `username` FROM {{table}} WHERE `id` = '".$PlanetRow['id_owner']."';", 'users', true);
          $Row['element']     = $ElementName;
          $Row['winner']      = ($PlanetRow['current'] != 0) ? $UserRow['username'] : $lang['rec_rien'];
          $Row['count']       = ($PlanetRow['current'] != 0) ? pretty_number( $PlanetRow['current'] ) : $lang['rec_rien'];
          $parse['buildspe'] .= parsetemplate( $TableRows, $Row);
        } elseif ($Element >= 101 && $Element <= 199) {
          // Techno
          $UserRow            = doquery ("SELECT `username`, `". $resource[$Element] ."` AS `current` FROM {{table}} WHERE `". $resource[$Element] ."` = (SELECT MAX(`". $resource[$Element] ."`) FROM {{table}} WHERE `authlevel` = '0');", 'users', true);
          $Row['element']     = $ElementName;
          $Row['winner']      = ($UserRow['current'] != 0) ? $UserRow['username'] : $lang['rec_rien'];
          $Row['count']       = ($UserRow['current'] != 0) ? pretty_number( $UserRow['current'] ) : $lang['rec_rien'];
          $parse['research'] .= parsetemplate( $TableRows, $Row);
        } elseif ($Element >= 201 && $Element <= 399) {
          // Flotte
          $PlanetRow          = doquery ("SELECT `id_owner`, `". $resource[$Element] ."` AS `current` FROM {{table}} WHERE `". $resource[$Element]. "` = (SELECT MAX(`". $resource[$Element] ."`) FROM {{table}} WHERE `id_level` = '0');", 'planets', true);
          $UserRow            = doquery ("SELECT `username` FROM {{table}} WHERE `id` = '".$PlanetRow['id_owner']."';", 'users', true);
          $Row['element']     = $ElementName;
          $Row['winner']      = ($PlanetRow['current'] != 0) ? $UserRow['username'] : $lang['rec_rien'];
          $Row['count']       = ($PlanetRow['current'] != 0) ? pretty_number( $PlanetRow['current'] ) : $lang['rec_rien'];
          $parse['fleet']    .= parsetemplate( $TableRows, $Row);
        } elseif ($Element >= 401 && $Element <= 599) {
          // Défenses
          $PlanetRow          = doquery ("SELECT `id_owner`, `". $resource[$Element] ."` AS `current` FROM {{table}} WHERE `". $resource[$Element]. "` = (SELECT MAX(`". $resource[$Element] ."`) FROM {{table}} WHERE `id_level` = '0');", 'planets', true);
          $UserRow            = doquery ("SELECT `username` FROM {{table}} WHERE `id` = '".$PlanetRow['id_owner']."';", 'users', true);
          $Row['element']     = $ElementName;
          $Row['winner']      = ($PlanetRow['current'] != 0) ? $UserRow['username'] : $lang['rec_rien'];
          $Row['count']       = ($PlanetRow['current'] != 0) ? pretty_number( $PlanetRow['current'] ) : $lang['rec_rien'];
          $parse['defenses'] .= parsetemplate( $TableRows, $Row);
        }
      }
    }
  }

  $page = parsetemplate( $RecordTpl, $parse );
  display($page, $lang['rec_title']);

// -----------------------------------------------------------------------------------------------------------
// History version
// - 1.0 Réécriture
// - 1.1 Ajout du test de presence d'un chmap de la base de données ... Si apres ca ca plante c'est
//       que l'utilisateur de ce module est vraiment trop con et devrait arreter l'informatique pour aller
//       vendre des frittes chez Mc Do ou autre FastFood
// - 1.2 Separateur de chiffres ... qu'ils soient comme partout ailleur dans le jeu
// - 1.3 Remplacement des 0 par un texte ou un '-' (suggestion matdu57)
// - 1.4 Non prise en compte des planetes protégées
?>
