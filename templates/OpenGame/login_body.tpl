<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<title>{log_servername}</title>
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="css/styles.css">
<link rel="stylesheet" type="text/css" href="css/about.css">
</head><body>
<div  style="position: absolute; left: 100px; width: 110px;"><div id="main">
</div>
<div id="login" style="position: relative; top: -140px; left: 404px">
 <div id="login_input">
	<form name="formular" action="login.php" method="post">
     <input type="hidden" name="v" value="2">
     <table border="0" cellpadding="0" cellspacing="0"><tbody>
       <tr style="vertical-align: top;">
         <td style="padding-right: 4px;" align=left>{User_name}</td>
         <td style="padding-right: 4px;" align=left>{Password}</td>
       </tr>
       <tr>
         <td valign=bottom style="padding-right: 4px;"><input name="username" style="width: 180px" type="text" value="" class="eingabe" /></td>
         <td style="padding-right: 4px;">
           <input name="password" style="width: 180px" type="password" value="" class="eingabe" />
 	        <input name="submit" style="width: 62px" type="submit" value="{log_enter}" />
 	      </td>
 	    </tr>
 	    <tr>
 	      <td colspan=2 style="padding-right: 4px;">{Remember_me} <input name="rememberme" type="checkbox"></td>
 	    </tr>
 	  </tbody></table>
     <script type="text/javascript">document.formular.Uni.focus(); </script>
   </form>
 </div>
     <div id="downmenu">
     </div>    
</div>
<div id="mainmenu">
    <div class="menupoint">:: {log_menu} ::</div>
    	<a href="reg.php">{log_reg}!</a>
	<a href="lostpassword.php">{PasswordLost}</a>
	<a href="stat_menu.php">{log_stat_menu}</a>
	<a href="contact.php">{log_contacts}</a>
	<a href="faq.php">{log_faq}</a>
	<a href="credit.php">{log_cred}</a>
	<a href="{forum_url}">{log_forums}</a>
</div>
&copy; OGame Triolan 2009
<div id="rightmenu" class="rightmenu">
    <div id="title" style="font-color: white">{log_servername}</div>
    <div id="content">
        <div id="text1">{log_desc}</div>
        <div id="register" class="bigbutton" onclick="document.location.href='./reg.php';">{log_reg_main}</div>
        <div id="text2">
          <div class="eingabe" align="center" style="width: 410px"><b>{log_online}: <font color="red">{online_users}</font> - {log_lastreg}: <font color="red">{last_user}</font> - {log_numbreg}: <font color="red">{users_amount}</font></b></div>
        </div>
</div>
</div>
</body></html>