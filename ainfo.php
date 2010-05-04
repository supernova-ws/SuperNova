<?php

/**
 * ainfo.php
 *
 * Alliance info for non-allied user
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */


define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

$dpath = (!$userrow["dpath"]) ? DEFAULT_SKINPATH : $userrow["dpath"];


if(!is_numeric($_GET["a"]) || !$_GET["a"] ){ message("Ung&uuml;ltige Allianz-ID","Fehler");}

$allyrow = doquery("SELECT ally_name,ally_tag,ally_description,ally_web,ally_image FROM {{table}} WHERE id=".$_GET["a"],"alliance",true);

if(!$allyrow){ message("Alliance non trouv&eacute;e","Erreur");}

$count = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE ally_id=".$_GET["a"].";","users",true);
$ally_member_scount = $count[0];

$page .="<table width=519><tr><td class=c colspan=2>Informations sur l'alliance</td></tr>";

  if($allyrow["ally_image"] != ""){
    $page .= "<tr><th colspan=2><img src=\"".$allyrow["ally_image"]."\"></td></tr>";
  }

  $page .= "<tr><th>Tag</th><th>".$allyrow["ally_tag"]."</th></tr><tr><th>Nom</th><th>".$allyrow["ally_name"]."</th></tr><tr><th>Membres</th><th>$ally_member_scount</th></tr>";

  if($allyrow["ally_description"] != ""){
    $page .= "<tr><th colspan=2 height=100>".$allyrow["ally_description"]."</th></tr>";
  }


  if($allyrow["ally_web"] != ""){
    $page .="<tr>
    <th>Site internet</th>
    <th><a href=\"".$allyrow["ally_web"]."\">".$allyrow["ally_web"]."</a></th>
    </tr>";
  }
  $page .= "</table>";

  display($page,"Information sur l'alliance [".$allyrow["ally_name"]."]",false);

// Created by Perberos. All rights reversed (C) 2006
?>