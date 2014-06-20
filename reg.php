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

include('includes/init.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('login');
lng_include('admin');

// $wylosuj = rand(100000,9000000);
// $kod = md5($wylosuj);

$id_ref = sys_get_param_int('id_ref');

if($_POST['submit'])
{
  session_start();
  $errors    = 0;
  $errorlist = '';

  $username = sys_get_param_str_raw('username');
  $username_safe = mysql_real_escape_string($username);
  $password = sys_get_param('password');
  $email_unsafe = sys_get_param_str_raw('email');
  $email = mysql_real_escape_string($email_unsafe);
  $planet_name = sys_get_param_str_raw('planet_name');
  $sex = sys_get_param_str('sex');


  if(!$username)
  {
    $errorlist .= $lang['error_character'];
    $errors++;
  }
  else
  {
    $db_check = doquery("SELECT `player_id` FROM {{player_name_history}} WHERE `player_name` = '{$username_safe}' LIMIT 1;", true);
    if($db_check) {
      $errorlist .= $lang['error_userexist'];
      $errors++;
    }
  }

  if(strlen($password) < 4)
  {
    $errorlist .= $lang['error_password'];
    $errors++;
  }

  if(!is_email($email))
  {
    $errorlist .= "'{$email}' {$lang['error_mail']}";
    $errors++;
  }
  else
  {
    $db_check = db_user_by_email($email_unsafe, true, false, 'id');
    if($db_check)
    {
      $errorlist .= $lang['error_emailexist'];
      $errors++;
    }
  }

  if(!$planet_name)
  {
    $errorlist .= $lang['error_planet'];
    $errors++;
  }

  if($sex != 'F' && $sex != 'M')
  {
    $errorlist .= $lang['error_sex'];
    $errors++;
  }

  if(!$_POST['register']) {
    $errorlist .= $lang['error_rgt'];
    $errors++;
  }

  if ($errors)
  {
    message ($errorlist, $lang['Register']);
  }
  else
  {
    $md5pass = md5($password);

    $language = $language ? $language : DEFAULT_LANG;
    $def_skin = DEFAULT_SKINPATH;

    $user = classSupernova::db_ins_record(LOC_USER, "`username` = '{$username_safe}', `email` = '{$email}', `email_2` = '{$email}', `design` = '1', `dpath` = '{$def_skin}',
        `lang` = '{$language}', `sex` = '{$sex}', `id_planet` = '0', `register_time` = '{$time_now}', `password` = '{$md5pass}',
        `options` = 'opt_mnl_spy^1|opt_email_mnl_spy^1|opt_email_mnl_joueur^1|opt_email_mnl_alliance^1|opt_mnl_attaque^1|opt_email_mnl_attaque^1|opt_mnl_exploit^1|opt_email_mnl_exploit^1|opt_mnl_transport^1|opt_email_mnl_transport^1|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^1|opt_mnl_buildlist^1|opt_email_mnl_buildlist^1|opt_int_navbar_resource_force^1|';");

    // $user = db_user_by_username($username, false, 'id');
    doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user['id']}, `player_name` = \"{$username_safe}\"");

    if($id_ref)
    {
      $referral_row = db_user_by_id($id_ref, true);
      if($referral_row)
      {
        doquery("INSERT INTO {{referrals}} SET `id` = {$user['id']}, `id_partner` = {$id_ref}");
      }
    }

    $galaxy = $config->LastSettedGalaxyPos;
    $system = $config->LastSettedSystemPos;
    $segment_size = floor($config->game_maxPlanet/3);
    $segment = floor($config->LastSettedPlanetPos / $segment_size);
    $segment++;
    $planet = mt_rand(1 + $segment*$segment_size, ($segment + 1)*$segment_size);

    $planet_set = false;
    while (!$planet_set)
    {
      if($planet > $config->game_maxPlanet)
      {
        $planet = mt_rand(0, $segment_size - 1) + 1;
        $system++;
      }
      if($system > $config->game_maxSystem)
      {
        $system = 1;
        $galaxy++;
      }
      if($galaxy > $config->game_maxGalaxy)
      {
        $galaxy = 1;
      }

      $galaxy_row = db_planet_by_gspt($galaxy, $system, $planet, PT_PLANET, true, 'id');
      if(!$galaxy_row['id'])
      {
        $planet_set = true;
        $config->db_saveItem(array(
          'LastSettedGalaxyPos' => $galaxy,
          'LastSettedSystemPos' => $system,
          'LastSettedPlanetPos' => $planet
        ));
        $new_planet = uni_create_planet($galaxy, $system, $planet, $user['id'], $planet_name, true);
        break;
      }
      $planet += 3;
    }

    sys_player_new_adjust($user['id'], $new_planet);

    db_user_set_by_id($user['id'], "`id_planet` = '{$new_planet}', `current_planet` = '{$new_planet}', `galaxy` = '{$galaxy}', `system` = '{$system}', `planet` = '{$planet}'");

    $config->db_saveItem('users_amount', $config->users_amount+1);

    $Message = $lang['thanksforregistry'];
    if (sendpassemail($username, $password, $email))
    {
      $Message .= " (" . htmlentities($email) . ")";
    }
    else
    {
      $Message .= " (" . htmlentities($email) . ")";
      $Message .= "<br><br>{$lang['error_mailsend']} <br /><b>{$password}</b><br />";
    }
    $user = sn_login($username, $password);
    $user = $user['user_row'];

    message($Message . $config->adv_conversion_code_register, "{$lang['reg_welldone']}<b>{$password}</b> <script>document.getElementById('sn_navbar').style.display='none';document.getElementById('page_body').style.marginLeft='0px'; document.getElementById('page_body').style.marginTop='0px';jQuery(document).ready(function(){setTimeout(function(){parent.location='overview.php';}, 10000);});</script>");
  }
}
else
{
  $template = gettemplate('registry_form', true);
  $template->assign_vars(array(
    'id_ref'       => $id_ref,
    'servername'   => $config->game_name,
    'last_user'    => db_user_last_registered_username(),
    'online_users' => db_user_count(true),
    'URL_RULES'    => $config->url_rules,
    'URL_FORUM'    => $config->url_forum,
    'URL_FAQ'      => $config->url_faq,
  ));

  tpl_login_lang($template, $id_ref);

  display($template, $lang['registry'], false, '', false, false);
}

function sendpassemail($username, $password, $emailaddress)
{
  global $lang, $config;

  $email  = sprintf($lang['mail_welcome'], $config->game_name, SN_ROOT_VIRTUAL, sys_safe_output($username), sys_safe_output($password));
  $status = mymail($emailaddress, sprintf($lang['mail_title'], $config->game_name), $email);
  return $status;
}

?>
