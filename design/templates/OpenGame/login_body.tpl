<style type="text/css"><!--
 @import url(./design/css/login.css);
--></style>

<div id="log_skipper"></div>
<div id="log_main">
  <div id="log_title">{L_sys_supernova}: {L_sys_universe} "{C_game_name}"</div>
  <div id="log_status">
    <font color="red">{C_game_maxGalaxy}</font> {L_adm_opt_galaxies} - <font color="red">{C_game_maxSystem}</font> {L_adm_opt_systems} - <font color="red">{C_game_maxPlanet}</font> {L_adm_opt_planets}
  </div>
  <div id="log_status">
    {L_adm_opt_speed}:
    {L_adm_opt_game_gspeed} <font color="red">x{C_game_speed}</font> -
    {L_adm_opt_game_fspeed} <font color="red">x{C_fleet_speed}</font> -
    {L_adm_opt_game_pspeed} <font color="red">x{C_resource_multiplier}</font>
  </div>

  <div id="log_description">{L_log_desc}</div>

  <div id="log_register" onclick="document.location.href='./reg.php{LANG}{referral}';">
    {L_log_reg_main}
  </div>

  <div id="log_status">
    {L_log_online}: <font color="red">{online_users}</font> - {L_log_lastreg}: <font color="red">{last_user}</font> - {L_log_numbreg}: <font color="red">{C_users_amount}</font><br>
  </div>

  <div id="log_form">
    <form action="login.php{LANG}{referral}" method="post">
      <div class="log_input_wrapper">
        {L_User_name}<br>
        <input name="username" type="text" value="" class="log_input" />
      </div>
      <div class="log_input_wrapper">
        {L_Password}<br>
        <input name="password" type="password" value="" class="log_input" /> 
      </div><br>
      <div class="log_input_wrapper">
        <a href="lostpassword.php{LANG}{referral}" class="link">{L_PasswordLost}</a>
      </div>
      <div class="log_input_wrapper" style="text-align: right">
        <div class="fl"><input name="rememberme" type="checkbox" value="1"> {L_Remember_me}</div>
        <input name="submit" type="submit" value="{L_log_enter}" /><br />
      </div>
    </form>
  </div>

  <!-- INCLUDE login_menu.tpl -->
</div>
