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
  <div id="log_title">{L_sys_supernova}: {L_sys_universe} "{log_servername}"</div>
  <div id="log_description">{L_log_desc}</div>

  <div id="log_register" onclick="document.location.href='./reg.php{referral}';">
    {L_log_reg_main}
  </div>

  <div id="log_status">
    {L_log_online}: <font color="red">{online_users}</font> - {L_log_lastreg}: <font color="red">{last_user}</font> - {L_log_numbreg}: <font color="red">{users_amount}</font>
  </div>

  <form action="login.php" method="post">
    <div id="log_form">
      <div class="log_input_wrapper">
        {User_name}<br>
        <input name="username" type="text" value="" class="log_input" />
      </div>
      <div class="log_input_wrapper">
        {Password}<br>
        <input name="password" type="password" value="" class="log_input" /> 
      </div><br>
      <div class="log_input_wrapper">
        <a href="lostpassword.php">{L_PasswordLost}</a>
      </div>
      <div class="log_input_wrapper" style="text-align: right">
        <div class="fl"><input name="rememberme" type="checkbox"> {L_Remember_me}</div>
        <input name="submit" type="submit" value="{log_enter}" /><br />
      </div>
    </div>
  </form>

  <div id="log_menu">
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
