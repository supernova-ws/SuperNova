<style type="text/css"><!--
.bo
{
  border: dashed 1px red;
}

body {
    background-color:#000000;
    background-image: url(../images/tlo.jpg);
    background-repeat: no-repeat;
    text-align: center;
    font-family:Arial, Helvetica, sans-serif;
}

#log_main {
  margin-top: 10%;
  position: relative;
  width: 450px;
  background-repeat: none;
  border:1px solid #FFFFFF;

  font-color: #0a1c25;
  color:  #b1daf2;

  background-color     : #000000;
  filter               : alpha(opacity=70);
  -moz-opacity         : 0.7;
  -khtml-opacity       : 0.7;
  opacity              : 0.7;
}

#log_status, #log_title 
{
  font-family: Arial, Helvetica, sans-serif;
  font-weight: bold;

  color: #0a1c25;
  width: 100%;
  text-align: center;

  background-color     : #FFFFFF;
  filter               : alpha(opacity=90);
  -moz-opacity         : 0.9;
  -khtml-opacity       : 0.9;
  opacity              : 0.9;
}

#log_title 
{
  font-size: 20px;
}

#log_description
{
  margin: 20px;
  color:  #b1daf2;
  text-align: justify;
}

#log_register {
  font-family:Arial, Helvetica, sans-serif;
  font-size: 16px;

  margin:20px;
  padding: 0.3em;
  text-align: center;
  font-weight: bold;
  color:#497ec7;
  cursor: pointer;

  background-color     : #000000;
  border:1px solid #FFFFFF;
}

#log_register:hover 
{
  color:#ffffff;
}

#log_form {
  padding: 2%;
  height: 5em;
}

.log_input_wrapper
{
  float: left; 
  margin-left: 2%;
  width: 46%; 
  text-align: left;
}

.log_input
{
  border:1px solid #000000;
  background:#ffffff url(../templates/OpenGame/img/eingabe_back.png) repeat-x;
  color: #000000;
  font-size:10px;
  width: 100%;
  left: 0px;
}

form
{
  margin: 0px;
  border: 0px;
  padding: 0px;
}

#log_menu {
  padding-top: 10px;
  padding-bottom: 10px;
  border-top: solid 1px;
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
