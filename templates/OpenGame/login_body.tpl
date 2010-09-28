<style type="text/css"><!--
body {
    background-image: url(../images/bg-unauthorized.jpg);
    background-repeat: no-repeat;
    text-align: center;
    font-family:Arial, Helvetica, sans-serif;
}
--></style>

<div id="log_main">
  <div id="log_title">{L_sys_supernova}: {L_sys_universe} "{C_game_name}"</div>
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
        {L_User_name}<br>
        <input name="username" type="text" value="" class="log_input" />
      </div>
      <div class="log_input_wrapper">
        {L_Password}<br>
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
    <a href="reg.php{referral}">{L_log_reg}</a> ::
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
