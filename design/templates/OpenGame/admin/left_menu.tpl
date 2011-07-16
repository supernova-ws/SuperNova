<div id="left_menu_wrapper">
  <table width="130" cellspacing="0" cellpadding="0" id="left_menu">
    <tr><td class="c_c"><strong>{C_game_name}</strong></td></tr>
    <tr><th id="server_time"></td></tr>

    <tr><td align=center class="c">{USER_AUTHLEVEL_NAME}</td></tr>
    <tr><td><a href="admin/overview.php">{L_adm_over}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/settings.php">{L_adm_conf}</a></td></tr>
    <tr><td><a href="admin/admin_darkmatter.php">{L_dark_matter}</a></td></tr>
    <tr><td><a href="admin/adm_quest.php">{L_qst_quests}</a></td></tr>
    <!-- ENDIF -->

    <tr><td align=center class="c">{L_player}</td></tr>
    <tr><td><a href="admin/banned.php">{L_adm_ban}/{L_adm_unban}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/userlist.php">{L_adm_plrlst}</a></td></tr>
    <tr><td><a href="admin/paneladmina.php">{L_adm_panel}</a></td></tr>
    <!-- ENDIF -->
    <!-- IF USER_AUTHLEVEL >= 2 -->
    <tr><td><a href="admin/add_research.php">Add Research</a></td></tr>
    <tr><td><a href="admin/del_research.php">Del Research</a></td></tr>
    <!-- ENDIF -->

    <tr><td align=center class="c">{L_sys_universe}</td></tr>
    <tr><td><a href="admin/adm_planet_list.php?planet_active=1">{L_adm_planet_active}</a></td></tr>
    <tr><td><a href="admin/adm_planet_list.php?planet_type=1">{L_adm_pltlst}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 2 -->
    <tr><td><a href="admin/adm_planet_list.php?planet_type=3">{L_adm_moonlst}</a></td></tr>
    <tr><td><a href="admin/add_moon.php">{L_adm_addmoon}</a></td></tr>
    <tr><td><a href="admin/planet_edit.php">{L_adm_lm_planet_edit}</a></td></tr>
    <!-- ENDIF -->
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/planet_compensate.php">{L_adm_lm_compensate}</a></td></tr>
    <!-- ENDIF -->
    <!-- IF USER_AUTHLEVEL >= 2 -->
    <tr><td><a href="admin/ShowFlyingFleets.php">{L_adm_fleet}</a></td></tr>
    <!-- ENDIF -->

    <tr><td align=center class="c link"><a href="admin/tools.php">{L_tool}</a></td></tr>
    <tr><td><a href="admin/statbuilder.php">{L_adm_updpt}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/maintenance.php">{L_adm_maint}</a></td></tr>
    <tr><td><a href="admin/sxd/index.php">{L_adm_backup}</a></td></tr>
    <tr><td><a href="admin/messagelist.php">{L_adm_msg}</a></td></tr>
    <tr><td><a href="admin/admin_chat.php">{L_adm_chat}</a></td></tr>
    <tr><td><a href="admin/errors.php">{L_adm_error}</a></td></tr>
    <!-- ENDIF -->
    
    <tr><td align=center class="c"><a href="index.php" style="color:red">{L_adm_back}</a></td></tr>
  </table>
</div>

<script LANGUAGE='JavaScript'><!--
  sn_timers.unshift({'id': 'server_time', 'type': 2, 'active': true, 'start_time': {SERVER_TIME}, options: 3});
// --></script>
