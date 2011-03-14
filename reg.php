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

includeLang('login');

$wylosuj = rand(100000,9000000);
$kod = md5($wylosuj);

$id_ref = intval($_GET['id_ref'] ? $_GET['id_ref'] : $_POST['id_ref']);

if ($_POST['submit'])
{
  session_start();
  $errors    = 0;
  $errorlist = '';

  $username = strip_tags($_POST['username']);
  $username_safe = mysql_real_escape_string($username);
  $password = strip_tags($_POST['password']);
  $email = mysql_real_escape_string(strip_tags($_POST['email']));
  $planet_name = strip_tags(trim($_POST['planet_name']));
  $language = mysql_real_escape_string(strip_tags($_POST['language']));
  $sex = mysql_real_escape_string(strip_tags($_POST['sex']));


  if (!$username)
  {
    $errorlist .= $lang['error_character'];
    $errors++;
  }
  else
  {
    $db_check = doquery("SELECT `username` FROM {{users}} WHERE `username` = '{$username_safe}' LIMIT 1;", '', true);
    if ($db_check['username']) {
      $errorlist .= $lang['error_userexist'];
      $errors++;
    }
  }

  if (strlen($password) < 4)
  {
    $errorlist .= $lang['error_password'];
    $errors++;
  }

  if (!is_email($email))
  {
    $errorlist .= "'{$email}' {$lang['error_mail']}";
    $errors++;
  }
  else
  {
    $db_check = doquery("SELECT `email` FROM {{users}} WHERE `email` = '{$email}' LIMIT 1;", '', true);
    if ($db_check['email'])
    {
      $errorlist .= $lang['error_emailexist'];
      $errors++;
    }
  }

  if (!$planet_name)
  {
    $errorlist .= $lang['error_planet'];
    $errors++;
  }

  if ($sex != 'F' && $sex != 'M')
  {
    $errorlist .= $lang['error_sex'];
    $errors++;
  }

  if ($language != 'ru')
  {
    $errorlist .= $lang['error_lang'];
    $errors++;
  }

  if (!$_POST['register']) {
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

    doquery(
      "INSERT INTO {{users}} SET
        `username` = '{$username_safe}', `email` = '{$email}', `email_2` = '{$email}',
        `lang` = '{$language}', `sex` = '{$sex}', `id_planet` = '0', `register_time` = '{$time_now}', `password` = '{$md5pass}';");

    $user = doquery("SELECT `id` FROM {{users}} WHERE `username` = '{$username_safe}' LIMIT 1;", '', true);

    if ($id_ref)
    {
      doquery( "INSERT INTO {{referrals}} SET `id` = {$user['id']}, `id_partner` = {$id_ref}");
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

      $galaxy_row = doquery( "SELECT `id` FROM {{planets}} WHERE `galaxy` = '{$galaxy}' AND `system` = '{$system}' AND `planet` = '{$planet}' AND `planet_type` = 1 LIMIT 1;", '', true);
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

//    $new_planet = doquery("SELECT `id` FROM {{planets}} WHERE `id_owner` = '{$user['id']}' LIMIT 1;", '', true);
//    $new_planet = $new_planet['id'];
    doquery("UPDATE {{users}} SET `id_planet` = '{$new_planet}', `current_planet` = '{$new_planet}', `galaxy` = '{$galaxy}', `system` = '{$system}', `planet` = '{$planet}' WHERE `id` = '{$user['id']}' LIMIT 1;");

    $config->db_saveItem('users_amount', $config->users_amount+1);

    $Message  = $lang['thanksforregistry'];
    if (sendpassemail($email, $password))
    {
      $Message .= " (" . htmlentities($email) . ")";
    }
    else
    {
      $Message .= " (" . htmlentities($email) . ")";
      $Message .= "<br><br>{$lang['error_mailsend']} <b>{$password}</b>";
    }
    $user = sn_login($username, $password);
    $user = $user['user_row'];

    message( $Message, "{$lang['reg_welldone']}<b>{$password}</b>");
  }
}
else
{
  $template = gettemplate('registry_form', true);
  $template->assign_vars(array(
    'id_ref'     => $id_ref,
    'referral'   => "?id_ref=$id_ref",
    'servername' => $config->game_name,
    'URL_RULES'  => $config->url_rules,
    'URL_FORUM'  => $config->url_forum,
    'URL_FAQ'    => $config->url_faq,
  ));

  display(parsetemplate($template), $lang['registry'], false, '', false, false);
}

function sendpassemail($emailaddress, $password) {
  global $lang, $kod;

  $parse['SN_ROOT_VIRTUAL']  = SN_ROOT_VIRTUAL;
  $parse['password'] = $password;
  $parse['kod']      = $kod;
  $email             = parsetemplate($lang['mail_welcome'], $parse);
  $status            = mymail($emailaddress, $lang['mail_title'], $email);
  return $status;
}

?>
