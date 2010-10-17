<?php

/**
 * reg.php
 *
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * 1.1 - Menage + rangement + utilisation fonction de creation planete nouvelle generation
 * 1.0 - Version originelle
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('login');

$wylosuj = rand(100000,9000000);
$kod = md5($wylosuj);

$id_ref = intval($_GET['id_ref'] ? $_GET['id_ref'] : $_POST['id_ref']);

/*
print("wylosuj=".$wylosuj."<br>");
print("kod=".$kod."<br>");
print("_REQUEST['captcha']".$_REQUEST["captcha"]."<br>");
print("_SESSION['captcha']".$_SESSION['captcha']."<br>");
*/
function sendpassemail($emailaddress, $password) {
  global $lang, $kod;

  $parse['gameurl']  = GAMEURL;
  $parse['password'] = $password;
  $parse['kod']      = $kod;
  $email             = parsetemplate($lang['mail_welcome'], $parse);
  $status            = mymail($emailaddress, $lang['mail_title'], $email);
  return $status;
}

function mymail($to, $title, $body, $from = '') {
  global $config;

  $from = trim($from);

  if (!$from) {
    $from = $config->game_adminEmail;
  }

  $rp     = $config->game_adminEmail;

  $head   = '';
  $head  .= "Content-Type: text/plain \r\n";
  $head  .= "Date: " . date('r') . " \r\n";
  $head  .= "Return-Path: $rp \r\n";
  $head  .= "From: $from \r\n";
  $head  .= "Sender: $from \r\n";
  $head  .= "Reply-To: $from \r\n";
  $head  .= "Organization: $org \r\n";
  $head  .= "X-Sender: $from \r\n";
  $head  .= "X-Priority: 3 \r\n";
  $body   = str_replace("\r\n", "\n", $body);
  $body   = str_replace("\n", "\r\n", $body);

  return mail($to, $title, $body, $head);
}

if ($_POST) {
  $errors    = 0;
  $errorlist = "";

  $_POST['email'] = strip_tags($_POST['email']);
  if (!is_email($_POST['email'])) {
    $errorlist .= "\"" . $_POST['email'] . "\" " . $lang['error_mail'];
    $errors++;
  }

  session_start();
  /*
    $girilen = $_REQUEST["captcha"];
  if($_SESSION['captcha'] == $girilen){
    echo "";
  }else{
    $errorlist .= $lang['error_captcha'];
    $errors++;
  }
  */
  if (!$_POST['planet']) {
    $errorlist .= $lang['error_planet'];
    $errors++;
  }

  if (preg_match("/[^A-z0-9_\-]/", $_POST['hplanet']) == 1) {
    $errorlist .= $lang['error_planetnum'];
    $errors++;
  }

  if (!$_POST['character']) {
    $errorlist .= $lang['error_character'];
    $errors++;
  }

  if (strlen($_POST['passwrd']) < 4) {
    $errorlist .= $lang['error_password'];
    $errors++;
  }

  if (preg_match("/[^A-z0-9_\-]/", $_POST['character']) == 1) {
    $errorlist .= $lang['error_charalpha'];
    $errors++;
  }

  if ($_POST['rgt'] != 'on') {
    $errorlist .= $lang['error_rgt'];
    $errors++;
  }

  // Le meilleur moyen de voir si un nom d'utilisateur est pris c'est d'essayer de l'appeler !!
  $ExistUser = doquery("SELECT `username` FROM {{table}} WHERE `username` = '". mysql_real_escape_string($_POST['character']) ."' LIMIT 1;", 'users', true);
  if ($ExistUser) {
    $errorlist .= $lang['error_userexist'];
    $errors++;
  }

  // Si l'on verifiait que l'adresse email n'existe pas encore ???
  $ExistMail = doquery("SELECT `email` FROM {{table}} WHERE `email` = '". mysql_real_escape_string($_POST['email']) ."' LIMIT 1;", 'users', true);
  if ($ExistMail) {
    $errorlist .= $lang['error_emailexist'];
    $errors++;
  }

  if ($_POST['sex'] != ''  &&
    $_POST['sex'] != 'F' &&
    $_POST['sex'] != 'M') {
    $errorlist .= $lang['error_sex'];
    $errors++;
  }

  if ($_POST['langer'] != ''  &&
    $_POST['langer'] != 'ru' &&
    $_POST['langer'] != 'en') {
    $errorlist .= $lang['error_lang'];
    $errors++;
  }

  if ($errors != 0)
  {
    message ($errorlist, $lang['Register']);
  }
  else
  {
    $newpass        = $_POST['passwrd'];
    $UserName       = mysql_real_escape_string(strip_tags(CheckInputStrings ( $_POST['character'] )));
    $UserEmail      = mysql_real_escape_string(CheckInputStrings ( $_POST['email']));
    $UserPlanet     = CheckInputStrings ( $_POST['planet'] );

    $md5newpass     = md5($newpass);
    // Creation de l'utilisateur
    $QryInsertUser  = "INSERT INTO {{users}} SET ";
    $QryInsertUser .= "`username` = '{$UserName}', `email` = '{$UserEmail}', `email_2` = '{$UserEmail}', ";
    $QryInsertUser .= "`lang` = '".     mysql_real_escape_string( $_POST['langer'] )      ."', ";
    $QryInsertUser .= "`sex` = '".      mysql_real_escape_string( $_POST['sex'] )         ."', ";
    $QryInsertUser .= "`id_planet` = '0', ";
    $QryInsertUser .= "`register_time` = '". time() ."', ";
    $QryInsertUser .= "`password`='{$md5newpass}';";
    doquery( $QryInsertUser);

    // On cherche le numero d'enregistrement de l'utilisateur fraichement créé
    $NewUser        = doquery("SELECT `id` FROM {{table}} WHERE `username` = '{$UserName}' LIMIT 1;", 'users', true);
    $iduser         = $NewUser['id'];

    if ($id_ref)
    {
      doquery( "INSERT INTO {{referrals}} SET `id` = {$iduser}, `id_partner` = {$id_ref}");
    }

    // Recherche d'une place libre !
    $LastSettedGalaxyPos  = $config->LastSettedGalaxyPos;
    $LastSettedSystemPos  = $config->LastSettedSystemPos;
    $LastSettedPlanetPos  = $config->LastSettedPlanetPos;
    while (!isset($newpos_checked))
    {
      for ($Galaxy = $LastSettedGalaxyPos; $Galaxy <= $config->game_maxGalaxy; $Galaxy++)
      {
        for ($System = $LastSettedSystemPos; $System <= $config->game_maxSystem; $System++)
        {
          for ($Posit = $LastSettedPlanetPos; $Posit <= 4; $Posit++)
          {
            $Planet = round (rand ( 4, 12) );

            switch ($LastSettedPlanetPos)
            {
              case 1:
                $LastSettedPlanetPos += 1;
              break;

              case 2:
                $LastSettedPlanetPos += 1;
              break;

              case 3:
                if ($LastSettedSystemPos == $config->game_maxSystem)
                {
                  $LastSettedGalaxyPos += 1;
                  $LastSettedSystemPos  = 1;
                  $LastSettedPlanetPos  = 1;
                  break;
                }
                else
                {
                  $LastSettedPlanetPos  = 1;
                }
                $LastSettedSystemPos += 1;
              break;
            }
            break;
          }
          break;
        }
        break;
      }

      $QrySelectGalaxy  = "SELECT * ";
      $QrySelectGalaxy .= "FROM {{table}} ";
      $QrySelectGalaxy .= "WHERE ";
      $QrySelectGalaxy .= "`galaxy` = '{$Galaxy}' AND ";
      $QrySelectGalaxy .= "`system` = '{$System}' AND ";
      $QrySelectGalaxy .= "`planet` = '{$Planet}' AND ";
      $QrySelectGalaxy .= "`planet_type` = 1 ";
      $QrySelectGalaxy .= "LIMIT 1;";
      $GalaxyRow = doquery( $QrySelectGalaxy, 'planets', true);

      if ($GalaxyRow["id"] == "0")
      {
        $newpos_checked = true;
      }

      if (!$GalaxyRow)
      {
        CreateOnePlanetRecord ($Galaxy, $System, $Planet, $NewUser['id'], $UserPlanet, BUILD_METAL, BUILD_CRISTAL, BUILD_DEUTERIUM, true);
        $newpos_checked = true;
      }
      if ($newpos_checked)
      {
        doquery("UPDATE {{table}} SET `config_value` = '". $LastSettedGalaxyPos ."' WHERE `config_name` = 'LastSettedGalaxyPos';", 'config');
        doquery("UPDATE {{table}} SET `config_value` = '". $LastSettedSystemPos ."' WHERE `config_name` = 'LastSettedSystemPos';", 'config');
        doquery("UPDATE {{table}} SET `config_value` = '". $LastSettedPlanetPos ."' WHERE `config_name` = 'LastSettedPlanetPos';", 'config');
      }
    }
    // Recherche de la reference de la nouvelle planete (qui est unique normalement !
    $PlanetID = doquery("SELECT `id` FROM {{table}} WHERE `id_owner` = '". $NewUser['id'] ."' LIMIT 1;", 'planets', true);

    // Mise a jour de l'enregistrement utilisateur avec les infos de sa planete mere
    $QryUpdateUser  = "UPDATE {{table}} SET ";
    $QryUpdateUser .= "`id_planet` = '". $PlanetID['id'] ."', ";
    $QryUpdateUser .= "`current_planet` = '". $PlanetID['id'] ."', ";
    $QryUpdateUser .= "`galaxy` = '". $Galaxy ."', ";
    $QryUpdateUser .= "`system` = '". $System ."', ";
    $QryUpdateUser .= "`planet` = '". $Planet ."' ";
    $QryUpdateUser .= "WHERE ";
    $QryUpdateUser .= "`id` = '". $NewUser['id'] ."' ";
    $QryUpdateUser .= "LIMIT 1;";
    doquery( $QryUpdateUser, 'users');

    // Mise a jour du nombre de joueurs inscripts
    doquery("UPDATE {{table}} SET `config_value` = `config_value` + '1' WHERE `config_name` = 'users_amount' LIMIT 1;", 'config');
    doquery("UPDATE {{table}} SET `config_value` = `config_value` + '1' WHERE `config_name` = 'aktywacjen' LIMIT 1;", 'config');

    $Message  = $lang['thanksforregistry'];
    if (sendpassemail($_POST['email'], "$newpass"))
    {
      $Message .= " (" . htmlentities($_POST["email"]) . ")";
    }
    else
    {
      $Message .= " (" . htmlentities($_POST["email"]) . ")";
      $Message .= "<br><br>". $lang['error_mailsend'] ." <b>" . $newpass . "</b>";
    }
    $user          = CheckTheUser();

    message( $Message, $lang['reg_welldone']);
  }
}
else
{
  // Afficher le formulaire d'enregistrement
  $parse               = $lang;
  $parse['id_ref']     = $id_ref;
  if($id_ref)
    $parse['referral'] = "?id_ref=$id_ref";
  $parse['servername'] = $config->game_name;
  $parse['forum_url']  = $config->forum_url;
  display(parsetemplate(gettemplate('registry_form', true), $parse), $lang['registry'], false, '', false, false);
}

// -----------------------------------------------------------------------------------------------------------
?>
