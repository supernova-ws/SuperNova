<div id="left_menu_wrapper">
  <table width="130" cellspacing="0" cellpadding="0" id="left_menu">
    <tr><td align=center><strong>{C_game_name}</strong></td></tr>

    <tr><td align=center class="c">{USER_AUTHLEVEL_NAME}</td></tr>
    <tr><td><a href="admin/overview.php">{L_adm_over}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/settings.php">{L_adm_conf}</a></td></tr>
    <tr><td><a href="admin/admin_darkmatter.php">{L_dark_matter}</a></td></tr>
    <tr><td><a href="admin/adm_quest.php">{L_qst_quests}</a></td></tr>
    <!-- ENDIF -->

    <tr><td align=center class="c">{L_player}</td></tr>
    <tr><td><a href="admin/banned.php">{L_adm_ban}</a></td></tr>
    <tr><td><a href="admin/banned.php?mode=unbanit">{L_adm_unban}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/userlist.php">{L_adm_plrlst}</a></td></tr>
    <tr><td><a href="admin/paneladmina.php">{L_adm_panel}</a></td></tr>
    <!-- ENDIF -->

    <tr><td align=center class="c">{L_sys_planet}</td></tr>
    <tr><td><a href="admin/planetlist.php">{L_adm_pltlst}</a></td></tr>
    <tr><td><a href="admin/activeplanet.php">{L_adm_actplt}</a></td></tr>
    <!-- IF USER_AUTHLEVEL >= 3 -->
    <tr><td><a href="admin/planet_compensate.php">{L_adm_lm_compensate}</a></td></tr>
    <!-- ENDIF -->

    <!-- IF USER_AUTHLEVEL >= 2 -->
    <tr><td align=center class="c">Add Menu</td></tr>
    <tr><td><a href="admin/add_building.php">Add Building </a></td></tr>
    <tr><td><a href="admin/add_def.php">Add Def </a></td></tr>
    <tr><td><a href="admin/add_money.php">Add Money </a></td></tr>
    <tr><td><a href="admin/add_research.php">Add Research</a></td></tr>
    <tr><td><a href="admin/add_ship.php">Add Ships </a></td></tr>

    <tr><td align=center class="c">Delete Menu</td></tr>
    <tr><td><a href="admin/del_building.php">Del Building </a></td></tr>
    <tr><td><a href="admin/del_def.php">Del Def </a></td></tr>
    <tr><td><a href="admin/del_money.php">Del Money </a></td></tr>
    <tr><td><a href="admin/del_research.php">Del Research</a></td></tr>
    <tr><td><a href="admin/del_ship.php">Del Ships </a></td></tr>

    <tr><td align=center class="c">{L_sys_moon}</td></tr>
    <tr><td><a href="admin/moonlist.php">{L_adm_moonlst}</a></td></tr>
    <tr><td><a href="admin/add_moon.php">{L_adm_addmoon}</a></td></tr>
    
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/ShowFlyingFleets.php">{L_adm_fleet}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
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