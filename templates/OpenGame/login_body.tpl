<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<title>{log_servername}</title>
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="css/styles.css">
<link rel="stylesheet" type="text/css" href="css/about.css">
</head><body>
<div><div id="main">
</div>
<div id="login">
<div id="login_text_1">
        <div style="position: absolute; left: 12px; width: 110px;">{User_name}</div>
        <div style="position: absolute; left: 195px; width: 50px;">{Password}</div>
  </div>
     <div id="login_input">
     <table border="0" cellpadding="0" cellspacing="0"><tbody><tr style="vertical-align: top;"><td style="padding-right: 4px;">
	<form name="formular" action="login.php" method="post">
    <input type="hidden" name="v" value="2">
	<input name="username" style="width: 180px" type="text" value="" class="eingabe" />
	<input name="password" style="width: 180px" type="password" value="" class="eingabe" /> 
 	<input name="submit" style="width: 62px" type="submit" value="{log_enter}" /><br />
 	{Remember_me} <input name="rememberme" type="checkbox"> <script type="text/javascript">document.formular.Uni.focus(); </script>
     </td></tr></tbody></table>
     </div>
     <div id="downmenu">
     </div>    
</div>
<div id="mainmenu">
    <div class="menupoint">:: {log_menu} ::</div>
    	<a href="reg.php{referral}">{log_reg}!</a>
	<a href="lostpassword.php">{PasswordLost}</a>
	<a href="stat.php">{log_stat_menu}</a>
	<a href="contact.php">{log_contacts}</a>
	<a href="faq.php">{log_faq}</a>
	<a href="credit.php">{log_cred}</a>
	<a href="banned.php">{log_banned}</a>
	<a href="{forum_url}">{log_forums}</a>
</div>
&corp; OGame Triolan 2009
<div id="rightmenu" class="rightmenu">
    <div id="title">{log_servername}</div>
    <div id="content">
        <div id="text1">{log_desc}</div>
        <div id="register" class="bigbutton" onclick="document.location.href='./reg.php';">{log_reg_main}</div>
        <div id="text2">
          <div class="eingabe" align="center"><b>{log_online}: <font color="red">{online_users}</font> - {log_lastreg}: <font color="red">{last_user}</font> - {log_numbreg}: <font color="red">{users_amount}</font></b></div>
        </div>
</div>
</div>
</body></html>