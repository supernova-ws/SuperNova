<style type="text/css"><!--
body {
    background-image: url(../images/bg-unauthorized.jpg);
    background-repeat: no-repeat;
    text-align: center;
    font-family:Arial, Helvetica, sans-serif;
}
--></style>

<div id="log_main">
  <div id="log_title">{L_log_reg} - {L_sys_universe} "{C_game_name}"</div>
  <div id="log_description">{L_log_reg_text0} <a href="{C_rules_url}"><u><font color="red">{L_reg_with_rules}</font></u></a>. {L_log_reg_text1}</div>

  <form name="registerForm" method="POST" action="" onsubmit="changeAction('register');" >
    <input type="hidden" name="id_ref" value="{id_ref}">
    <table width="340" align="center">
      <tbody>
        <tr align="center">
          <td colspan="2" class="c">{form}</td>
        </tr>

        <tr>
          <th width="179">{L_User_name}</th>
          <th width="161"><input name="character" type="text" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_neededpass}:</th>
          <th><input name="passwrd" type="password" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_E-Mail}:</th>
          <th><input name="email" type="text" size="20" maxlength="40" /></th>
        </tr>

        <tr>
          <th>{L_MainPlanet}:</th>
          <th><input name="planet" type="text" size="20" maxlength="20" /></th>
        </tr>

        <tr>
          <th>{L_Sex}:</th>
  
          <th>
            <select name="sex">
              <option value="M" selected="selected">{L_Male}</option>
              <option value="F">{L_Female}</option>
            </select>
          </th>
        </tr>

        <!--
        <tr>
          <th>{L_Languese}:</th>
          <th>
            <select name="langer">
              <option value="ru" selected="selected">{ru}</option>
            </select>
          </th>
        </tr>
        -->
        <input type="hidden" name="langer" value="ru">

        <tr>
          <th><img src="captcha.php" /></th>
          <th><input name="captcha" type="text" size="20" maxlength="20" /></th>
        </tr>
    
        <tr>
          <th colspan=2><input name="rgt" type="checkbox" /> {L_reg_i_agree} <a href="{C_rules_url}"><u><font color="red">{L_reg_with_rules}</font></u></a></th>
        </tr>
      </tbody>
    </table>
    <input name="submit" type="submit" value="{L_signup}!" />
  </form><br>
  {L_log_reg_already} <a href="login.php{referral}">{L_log_login_page}</a><br><br>

  <div id="log_menu">
    <a href="login.php{referral}">{L_log_login_page}</a> ::
    <a href="{C_rules_url}">{L_log_rules}</a> ::
<!--    <a href="faq.php">{log_faq}</a> ::-->
    <a href="stat.php">{log_stat_menu}</a> ::
    <a href="{forum_url}">{log_forums}</a><br>
    <a href="credit.php">{log_cred}</a> ::
    <a href="contact.php">{log_contacts}</a> ::
    <a href="banned.php">{log_banned}</a>
  </div>

  <div id="log_status">
    &copy; 2009-2010 Gorlum for http://supernova.ws
  </div>
</div>
