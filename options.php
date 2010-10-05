<?php

/**
 * options.php
 *
 * 1.1s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

includeLang('options');

$lang['PHP_SELF'] = 'options.' . $phpEx;

$dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
$mode = SYS_mysqlSmartEscape($_GET['mode']);
$POST_db_character = SYS_mysqlSmartEscape($_POST["db_character"]);

if ($_POST && $mode == "exit") { // Array ( [db_character]
  if (isset($_POST["exit_modus"]) && $_POST["exit_modus"] == 'on' and $user['urlaubs_until'] <= time()){
    $urlaubs_modus = "0";
      doquery("UPDATE {{table}} SET
        `urlaubs_modus` = '0',
        `urlaubs_until` = '0'
        WHERE `id` = '".$user['id']."' LIMIT 1", "users");
    message($lang['succeful_save'], $lang['Options'],"options.php",1);
  }else{
    $urlaubs_modus = "1";
    message($lang['You_cant_exit_vmode'], $lan['Error'] ,"options.php",1);
   }
}

if ($_POST && $mode == "change") { // Array ( [db_character]
  $iduser = $user["id"];
  $avatar = SYS_mysqlSmartEscape($_POST["avatar"]);
  $dpath = SYS_mysqlSmartEscape($_POST["dpath"]);
  $languese = SYS_mysqlSmartEscape($_POST["langer"]);

  // Gestion des options speciales pour les admins
  if ($user['authlevel'] > 0) {
    if ($_POST['adm_pl_prot'] == 'on') {
      doquery ("UPDATE {{table}} SET `id_level` = '".$user['authlevel']."' WHERE `id_owner` = '".$user['id']."';", 'planets');
    } else {
      doquery ("UPDATE {{table}} SET `id_level` = '0' WHERE `id_owner` = '".$user['id']."';", 'planets');
    }
  }
  // Mostrar skin
  if (isset($_POST["design"]) && $_POST["design"] == 'on') {
    $design = "1";
  } else {
    $design = "0";
  }
  // Desactivar comprobaci? de IP
  if (isset($_POST["noipcheck"]) && $_POST["noipcheck"] == 'on') {
    $noipcheck = "1";
  } else {
    $noipcheck = "0";
  }
  // Nombre de usuario
  if (isset($POST_db_character) && $POST_db_character && $config->game_user_changename) {
    $username = CheckInputStrings ( $_POST['db_character'] );
  } else {
    $username = $user['username'];
  }
  // Adresse e-Mail
  if (isset($_POST["db_email"]) && $_POST["db_email"] != '') {
    $db_email = SYS_mysqlSmartEscape(CheckInputStrings ( $_POST['db_email'] ));
  } else {
    $db_email = $user['email'];
  }
  // Cantidad de sondas de espionaje
  if (isset($_POST["spio_anz"]) && is_numeric($_POST["spio_anz"])) {
    $spio_anz = intval($_POST["spio_anz"]);
  } else {
    $spio_anz = "1";
  }
  // Mostrar tooltip durante
  if (isset($_POST["settings_tooltiptime"]) && is_numeric($_POST["settings_tooltiptime"])) {
    $settings_tooltiptime = intval($_POST["settings_tooltiptime"]);
  } else {
    $settings_tooltiptime = "1";
  }
  // Maximo mensajes de flotas
  if (isset($_POST["settings_fleetactions"]) && is_numeric($_POST["settings_fleetactions"])) {
    $settings_fleetactions = intval($_POST["settings_fleetactions"]);
  } else {
    $settings_fleetactions = "1";
  } //
  // Mostrar logos de los aliados
  if (isset($_POST["settings_allylogo"]) && $_POST["settings_allylogo"] == 'on') {
    $settings_allylogo = "1";
  } else {
    $settings_allylogo = "0";
  }
  // Espionaje
  if (isset($_POST["settings_esp"]) && $_POST["settings_esp"] == 'on') {
    $settings_esp = "1";
  } else {
    $settings_esp = "0";
  }
  // Escribir mensaje
  if (isset($_POST["settings_wri"]) && $_POST["settings_wri"] == 'on') {
    $settings_wri = "1";
  } else {
    $settings_wri = "0";
  }
  // A?dir a lista de amigos
  if (isset($_POST["settings_bud"]) && $_POST["settings_bud"] == 'on') {
    $settings_bud = "1";
  } else {
    $settings_bud = "0";
  }
  // Ataque con misiles
  if (isset($_POST["settings_mis"]) && $_POST["settings_mis"] == 'on') {
    $settings_mis = "1";
  } else {
    $settings_mis = "0";
  }
  // Ver reporte
  if (isset($_POST["settings_rep"]) && $_POST["settings_rep"] == 'on') {
    $settings_rep = "1";
  } else {
    $settings_rep = "0";
  }

  if($_POST['compat_builds'])
  {
    $user['compat_builds'] = 1;
  }
  else
  {
    $user['compat_builds'] = 0;
  }

  // Modo vacaciones
  if (isset($_POST['urlaubs_modus']) && $_POST['urlaubs_modus'] == 'on')
  {
    $urlaubs_modus = '1';

    $time = time();

    if($user['authlevel'] < 3)
    {
      if(CheckIfIsBuilding($user))
      {
        message($lang['Building_something'], $lang['Error'], "options.php", 1);
      }

      $time += VOCATION_TIME;

      $query = doquery("SELECT * FROM {{planets}} WHERE id_owner = '{$user['id']}'");
      while($id = mysql_fetch_array($query))
      {
        doquery("UPDATE {{planets}} SET
          metal_perhour = '".$config->metal_basic_income."',
          crystal_perhour = '".$config->metal_basic_income."',
          deuterium_perhour = '".$config->metal_basic_income."',
          energy_used = '0',
          energy_max = '0',
          metal_mine_porcent = '0',
          crystal_mine_porcent = '0',
          deuterium_sintetizer_porcent = '0',
          solar_plant_porcent = '0',
          fusion_plant_porcent = '0',
          solar_satelit_porcent = '0'
        WHERE id = '{$id['id']}' AND `planet_type` = 1 ");
      }
    }

    doquery("UPDATE {{users}} SET `urlaubs_modus` = '$urlaubs_modus', `urlaubs_until` = '$time' WHERE `id` = '$iduser' LIMIT 1");
  }
  else
  {
    $urlaubs_modus = '0';
  }

  // Borrar cuenta
  if (isset($_POST["db_deaktjava"]) && $_POST["db_deaktjava"] == 'on') {
    $db_deaktjava = "1";
    $Del_Time = time()+604800;
  } else {
    $db_deaktjava = "0";
    $Del_Time = "0";
  }
  $SetSort  = intval($_POST['settings_sort']);
  $SetOrder = intval($_POST['settings_order']);

//  $dpath = str_replace('\\','\\\\',$dpath);

  $options = sys_user_options_pack($user);

  doquery("UPDATE {{users}} SET
  `email` = '$db_email',
  `lang` = '$languese',
  `avatar` = '$avatar',
  `dpath` = '$dpath',
  `design` = '$design',
  `noipcheck` = '$noipcheck',
  `planet_sort` = '$SetSort',
  `planet_sort_order` = '$SetOrder',
  `spio_anz` = '$spio_anz',
  `settings_tooltiptime` = '$settings_tooltiptime',
  `settings_fleetactions` = '$settings_fleetactions',
  `settings_allylogo` = '$settings_allylogo',
  `settings_esp` = '$settings_esp',
  `settings_wri` = '$settings_wri',
  `settings_bud` = '$settings_bud',
  `settings_mis` = '$settings_mis',
  `settings_rep` = '$settings_rep',
  `urlaubs_modus` = '$urlaubs_modus',
  `db_deaktjava` = '$db_deaktjava',
  `kolorminus` = '$kolorminus',
  `kolorplus` = '$kolorplus',
  `kolorpoziom` = '$kolorpoziom',
  `urlaubs_modus_time` = '$urlaubs_modus_time',
  `options` = '$options',
  `deltime` = '$Del_Time'
  WHERE `id` = '$iduser' LIMIT 1");

  if (isset($_POST["db_password"]) && md5($_POST["db_password"]) == $user["password"]) {

    if ($_POST["newpass1"] == $_POST["newpass2"] && $_POST["newpass1"] != NULL) {
    $newpass = md5($_POST["newpass1"]);
      doquery("UPDATE {{table}} SET `password` = '{$newpass}' WHERE `id` = '{$user['id']}' LIMIT 1", "users");
      setcookie(COOKIE_NAME, "", time()-100000, "/", "", 0); //le da el expire
      message($lang['succeful_changepass'], $lang['changue_pass']);
    }
  }

  if ($user['username'] != $username && $config->game_user_changename) {
    $query = doquery("SELECT id FROM {{table}} WHERE username='{$POST_db_character}'", 'users', true);
    if (!$query) {
      doquery("UPDATE {{table}} SET username='{$username}' WHERE id='{$user['id']}' LIMIT 1", "users");
      setcookie(COOKIE_NAME, "", time()-100000, "/", "", 0); //le da el expire
      message($lang['succeful_changename'], $lang['changue_name']);
    }
  }

  if ($user['urlaubs_modus_time'] != 0){
    $query =  ("UPDATE {{table}} SET ");
    $query .= ("`last_update` = '".time()."' ");
    $query .= ("WHERE id_owner='{$user['id']}';");
    doquery($query, 'planets');
    message($lang['vacations_exit'], $lang['mode_vacations']);
  }

  message($lang['succeful_save'], $lang['Options']);
} else {

  if($user['urlaubs_modus']){
    $template = gettemplate('options_body_vmode', true);
  }else{
    $template = gettemplate('options_body', true);
  }

  $parse = $lang;

  $parse['dpath'] = $dpath;
//-------------------------------
  $skin_dir = dir($ugamela_root_path."skins");
  $parse['opt_lst_skin_data']="<option value =\"\">".$lang['select_skin_path']."</option>";
  while (false !== ($entry = $skin_dir->read())) {
    if (is_dir("skins/".$entry) && $entry[0] !=".") {
      $parse['opt_lst_skin_data'].="<option value =\"$entry\">$entry</option>";
    }
  }
  $skin_dir->close();

//  $parse['opt_lst_skin_data']  = "<option value =\"skins/xnova/\">skins/xnova/</option>";
  $parse['opt_lst_ord_data']   = "<option value =\"0\"". (($user['planet_sort'] == 0) ? " selected": "") .">". $lang['opt_lst_ord0'] ."</option>";
  $parse['opt_lst_ord_data']  .= "<option value =\"1\"". (($user['planet_sort'] == 1) ? " selected": "") .">". $lang['opt_lst_ord1'] ."</option>";
  $parse['opt_lst_ord_data']  .= "<option value =\"2\"". (($user['planet_sort'] == 2) ? " selected": "") .">". $lang['opt_lst_ord2'] ."</option>";

  $parse['opt_lst_cla_data']   = "<option value =\"0\"". (($user['planet_sort_order'] == 0) ? " selected": "") .">". $lang['opt_lst_cla0'] ."</option>";
  $parse['opt_lst_cla_data']  .= "<option value =\"1\"". (($user['planet_sort_order'] == 1) ? " selected": "") .">". $lang['opt_lst_cla1'] ."</option>";

  $parse['opt_lst_lang_data']   = "<option value =\"ru\"". (($user['lang'] == ru) ? " selected": "") .">". $lang['ru'] ."</option>";
  $parse['opt_lst_lang_data']  .= "<option value =\"en\"". (($user['lang'] == en) ? " selected": "") .">". $lang['en'] ."</option>";
  if ($user['authlevel'] > 0) {
    $FrameTPL = gettemplate('options_admadd');
    $IsProtOn = doquery ("SELECT `id_level` FROM {{table}} WHERE `id_owner` = '".$user['id']."' LIMIT 1;", 'planets', true);
    $bloc['opt_adm_title']       = $lang['opt_adm_title'];
    $bloc['opt_adm_planet_prot'] = $lang['opt_adm_planet_prot'];
    $bloc['adm_pl_prot_data']    = ($IsProtOn['id_level'] > 0) ? " checked='checked'/":'';
    $parse['opt_adm_frame']      = parsetemplate($FrameTPL, $bloc);
  }
  if ($user['urlaubs_modus_time'] != 0){
  $parse['vocation_time_out'] = $lang['exit_vacations'];
  }
  $parse['opt_usern_data'] = $user['username'];
  $parse['opt_mail1_data'] = $user['email'];
  $parse['opt_mail2_data'] = $user['email_2'];
  $parse['opt_dpath_data'] = $user['dpath'];
  $parse['opt_avata_data'] = $user['avatar'];
  $parse['opt_probe_data'] = $user['spio_anz'];
  $parse['opt_toolt_data'] = $user['settings_tooltiptime'];
  $parse['opt_fleet_data'] = $user['settings_fleetactions'];
  $parse['opt_sskin_data'] = ($user['design'] == 1) ? " checked='checked'":'';
  $parse['opt_noipc_data'] = ($user['noipcheck'] == 1) ? " checked='checked'":'';
  $parse['opt_allyl_data'] = ($user['settings_allylogo'] == 1) ? " checked='checked'/":'';
  $parse['opt_delac_data'] = ($user['db_deaktjava'] == 1) ? " checked='checked'/":'';
  $parse['opt_modev_data'] = ($user['urlaubs_modus'] == 1)?" checked='checked'/":'';
  $parse['opt_modev_exit'] = ($user['urlaubs_modus'] == 0)?" checked='1'/":'';
  $parse['Vaccation_mode'] = $lang['Vaccation_mode'];
  $parse['vacation_until'] = date("d.m.Y G:i:s",$user['urlaubs_until']);
  $parse['user_settings_rep'] = ($user['settings_rep'] == 1) ? " checked='checked'/":'';
  $parse['user_settings_esp'] = ($user['settings_esp'] == 1) ? " checked='checked'/":'';
  $parse['user_settings_wri'] = ($user['settings_wri'] == 1) ? " checked='checked'/":'';
  $parse['user_settings_mis'] = ($user['settings_mis'] == 1) ? " checked='checked'/":'';
  $parse['user_settings_bud'] = ($user['settings_bud'] == 1) ? " checked='checked'/":'';
  $parse['kolorminus']  = $user['kolorminus'];
  $parse['kolorplus']   = $user['kolorplus'];
  $parse['kolorpoziom'] = $user['kolorpoziom'];

  $options = sys_user_options_unpack($user);
  foreach($options as $option_name => $option_value)
  {
    $parse[$option_name] = $user[$option_name];
  }

  display(parsetemplate($template, $parse), 'Options', false);
  die();
}
?>