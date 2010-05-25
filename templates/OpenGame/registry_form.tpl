<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<title>{log_servername}</title>
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="css/styles.css">
<link rel="stylesheet" type="text/css" href="css/about.css">
</head><body>
<div><div id="main">
</div>
<div id="mainmenu">
    <div class="menupoint">:: {log_menu} ::</div>
	<a href="login.php">Главная</a>
	<a href="lostpassword.php">Забыли пароль?</a>
	<a href="credit.php">{log_cred}</a>
	<a href="{forum_url}">{log_forums}</a>
</div>
</div>
    <div id="rightmenu" class="rightmenu_register">
        <div id="title">{log_reg}!</div>
        <div id="content"  align="justify">
            <div id="text1">{log_reg_text}</div>
            <div id="register_container">
                <form name="registerForm"  method="POST" action="" onsubmit="changeAction('register');" >
                  <input type="hidden" name="id_ref" value="{id_ref}">
                  <table width="340" align="center">
                    <tbody>
                      <tr>
                        <td colspan="2" class="c"><div align="center">* {form} * </div></td>
                      </tr>
                      <tr>
                        <th width="179">{GameName}:</th>
                        <th width="161"><input name="character" type="text" onkeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;" size="20" maxlength="20" />
                        </th>
                      </tr>
                      <tr>
                        <th>{neededpass}:</th>
                        <th><input name="passwrd" type="password" onkeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;" size="20" maxlength="20" /></th>
                      </tr>
                      <tr>
                        <th>{E-Mail}:</th>
                        <th><input name="email" type="text" onkeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;" size="20" maxlength="40" /></th>
                      </tr>
                      <tr>
                        <th>{MainPlanet}:</th>
                        <th><input name="planet" type="text" onkeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;" size="20" maxlength="20" />
                                                    </th>
                      </tr>
                      <tr>
                        <th>{Sex}:</th>
                        <th>
                          <select name="sex">
                            <option value="M" selected="selected">{Male}</option>
                            <option value="F">{Female}</option>
                          </select>
                        </th>
                      </tr>
                      <tr>
                        <th>{Languese}:</th>
                        <th>
                          <select name="langer">
                            <option value="ru" selected="selected">{ru}</option>
                        </select>
                       </th>
                      </tr>
                      <tr>
                        <th><img src="captcha.php" /></th>
                        <th><input name="captcha" type="text" size="20" maxlength="20" /></th>
                      </tr>
                      <tr>
                        <th>
                        <input name="rgt" type="checkbox" /> {accept}
			</th>
                        <th><input name="submit" type="submit" value="{signup}!" /></th>
                      </tr>
                    </tbody>
                  </table>
                </form>
            </div>
            <div id="infotext"></div>
            <div id="statustext"></div>
</body></html>
