<script language="JavaScript">
function f(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
  new_win.focus();
}
</script>
<div id="left_menu_wrapper">
  <table width="130" cellspacing="0" cellpadding="0" id="left_menu">
    <tr><td align=center><strong>{C_game_name}</strong></td></tr>

    <tr><td align=center class="c">{admin}</td></tr>
    <tr><td><a href="admin/overview.php">{adm_over}</a></td></tr>
    <tr><td><a href="admin/settings.php">{adm_conf}</a></td></tr>
    <tr><td><a href="admin/XNovaResetUnivers.php">{adm_reset}</a></td></tr>

    <tr><td align=center class="c">Нормальная админка</td></tr>
    <tr><td><a href="admin/admin_darkmatter.php">{dark_matter}</a></td></tr>

    <tr><td align=center class="c">{L_sys_planet}</td></tr>
    <tr><td><a href="admin/planet_compensate.php">{L_adm_lm_compensate}</a></td></tr>

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

    <tr><td align=center class="c">{player}</td></tr>
    <tr><td><a href="admin/userlist.php">{adm_plrlst}</a></td></tr>
    <tr><td><a href="admin/paneladmina.php">{adm_panel}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/planetlist.php">{adm_pltlst}</a></td></tr>
    <tr><td><a href="admin/activeplanet.php">{adm_actplt}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/moonlist.php">{adm_moonlst}</a></td></tr>
    <tr><td><a href="admin/add_moon.php">{adm_addmoon}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/ShowFlyingFleets.php">{adm_fleet}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/banned.php">{adm_ban}</a></td></tr>
    <tr><td><a href="admin/unbanned.php">{adm_unban}</a></td></tr>

    <tr><td align=center class="c">{tool}</td></tr>
    <tr><td><a href="admin/admin_chat.php">{adm_chat}</a></td></tr>
    <tr><td><a href="admin/statbuilder.php">{L_adm_updpt}</a></td></tr>
    <tr><td><a href="admin/sxd/index.php">{L_adm_backup}</a></td></tr>
    <tr><td><a href="admin/maintenance.php">{L_adm_maint}</a></td></tr>
    <tr><td><a href="admin/messagelist.php">{adm_msg}</a></td></tr>
    <tr><td><a href="admin/ElementQueueFixer.php">{adm_updrank}</a></td></tr>
    
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/tools.php">{tool}</a></td></tr>
    <tr><td style="background-color:#FFFFFF" height="1px"></td></tr>
    <tr><td><a href="admin/errors.php">{adm_error}</a></td></tr>
    <tr><td><a href="index.php" target="_top" style="color:red">{adm_back}</a></td></tr>
    <tr><td align=center background="{-path_prefix-}{dpath}img/bg1.png">{infog}</td></tr>
    <tr><td align=center><a href="login.php">OGame Triolan</a></td></tr>
  </table>
</div>