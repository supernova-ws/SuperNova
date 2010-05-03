<?php

function CheckTheUser ( $IsUserChecked )
{
	global $user;
	$Result = CheckCookies( $IsUserChecked );
	$IsUserChecked = $Result['state'];

	if ($Result['record'] != false)
	{
		$user = $Result['record'];
		if ($user['bana'] == 1 && $user['banaday'] > time())
		{
			$bantime = date("d.m.Y H:i:s", $user['banaday']);
			die ('Вы забанены. Срок окончания блокировки аккаунта: '.$bantime.' <br>Для получения информации зайдите <a href="banned.php">сюда</a>');
		} elseif ($user['bana'] == 1)
		{
			doquery("DELETE FROM {{table}} WHERE who2='$user[username]'", 'banned');
			doquery("UPDATE {{table}} SET bana=0, urlaubs_modus=0, banaday=0 WHERE username='$user[username]'", "users");
		}
		$RetValue['record'] = $user;
		$RetValue['state']  = $IsUserChecked;
	} else
	{
		$RetValue['record'] = array();
		$RetValue['state']  = false;
	}
	return $RetValue;
}

?>