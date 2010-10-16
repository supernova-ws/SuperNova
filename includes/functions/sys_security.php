<?php
/**
 * CheckCookies.php
 *
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */
// TheCookie[0] = `id`
// TheCookie[1] = `username`
// TheCookie[2] = Password + Hashcode
// TheCookie[3] = 1rst Connexion time + 365 J

function CheckCookies ()
{
  global $lang, $config, $ugamela_root_path, $phpEx, $time_now;

  include("{$ugamela_root_path}config.{$phpEx}");

  if (isset($_COOKIE[$config->COOKIE_NAME]))
  {
    $TheCookie  = explode("/%/", $_COOKIE[$config->COOKIE_NAME]);
    $user = doquery("SELECT * FROM `{{users}}` WHERE `username` = '{$TheCookie[1]}';", '', true);

    // On verifie s'il y a qu'un seul enregistrement pour ce nom
    if (!$user)
    {
      message($lang['cookies']['Error1']);
    }

    // On teste si on a bien le bon UserID
    if ($user['id'] != $TheCookie[0])
    {
      message( $lang['cookies']['Error2'] );
    }

    // On teste si le mot de passe est correct !
    if (md5($user['password'] . '--' . $dbsettings['secretword']) !== $TheCookie[2])
    {
      message( $lang['cookies']['Error3'] );
    }

    $NextCookie = implode("/%/", $TheCookie);
    // Au cas ou dans l'ancien cookie il etait question de se souvenir de moi
    // 3600 = 1 Heure // 86400 = 1 Jour // 31536000 = 365 Jours
    // on ajoute au compteur!
    if ($TheCookie[3] == 1)
    {
      $ExpireTime = $time_now + 31536000;
    }
    else
    {
      $ExpireTime = 0;
    }

    setcookie ($config->COOKIE_NAME, $NextCookie, $ExpireTime, "/", "", 0);

    $ip = sys_get_user_ip();
    $user_agent = mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);

    doquery("UPDATE `{{users}}`
      SET
        `onlinetime`  = '{$time_now}',
        `user_lastip` = '{$ip['client']}',
        `user_proxy`  = '{$ip['proxy']}',
        `user_agent`  = '{$user_agent}'
      WHERE
        `id` = '{$user['id']}' LIMIT 1;");
  }
  else
  {
    $user = false;
  }

  unset($dbsettings);

  return $user;
}

function CheckTheUser()
{
  global $skip_ban_check, $IsUserChecked;

  $user = CheckCookies();
  $IsUserChecked = is_array($user);

  if($user)
  {
    sys_user_options_unpack($user);

    if(!$skip_ban_check)
    {
      if ($user['bana'] == 1 && $user['banaday'] > time())
      {
        $bantime = date(FMT_DATE_TIME, $user['banaday']);
        die ('Вы забанены. Срок окончания блокировки аккаунта: '.$bantime.' <br>Для получения информации зайдите <a href="banned.php">сюда</a>');
      }
      elseif ($user['bana'] == 1)
      {
        // doquery("DELETE FROM {{table}} WHERE who2='$user[username]'", 'banned');
        doquery("UPDATE {{users}} SET bana=0, urlaubs_modus=0, banaday=0 WHERE username='{$user['username']}'");
      }
    }
  }

  return $user;
}
?>