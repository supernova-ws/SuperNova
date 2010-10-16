<div id="left_menu_wrapper">
  <script language="JavaScript">
  function f(target_url,win_name) {
    var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
    new_win.focus();
  }
  </script>

  <table width="100%" cellspacing="0" cellpadding="0" id="left_menu">
    <tr><th id="server_name">{servername}</th></tr>

    <tr><td align="center"><img border="0" src="../design/images/supernova.png" alt="supernova.ws"></td></tr>

    {ADMIN_LINK}
    <tr><th>{m_h_rules}</th></tr>
    <tr><td><a href="{C_rules_url}">Правила игры</a></td></tr>
  <!--  <tr><td title="{m_faq_hint}"><a href="http://forum.supernova.ws/phpBB3/viewtopic.php?f=3&t=333">{m_faq}</a></td></tr> -->
  <!--  <tr><td><a href="faq.php">{m_faq}</a></td></tr> -->
    <tr>
      <td><a href="announce.php">{adm_announce}
      <!-- IF new_announce_count -->
      <font color=red><strong><blink>{L_lm_announce_fresh}</blink></strong></font>
      <!-- ENDIF -->
      </a>
      </td>
    </tr>
    <tr><th><a href="affilates.php" id="lm_affilates">{m_affilates}</a></th></tr>

    <tr><th>{L_sys_planet}</th></tr>
    <tr><td><a href="overview.php" id="lm_overview">{Overview}</a></td></tr>
    <tr><td><a href="resources.php">{Resources}</a></td></tr>
    <tr><td><a href="fleet.php" id="lm_fleet">{Fleet}</a></td></tr>
    <tr><td><a href="buildings.php" id="lm_buildings">{Buildings}</a></td></tr>
    <tr><td><a href="buildings.php?mode=fleet" id="lm_shipyard">{Shipyard}</a></td></tr>
    <tr><td><a href="buildings.php?mode=defense">{Defense}</a></td></tr>

    <tr><th>{Imperium}</th></tr>
    <tr><td><a href="imperator.php" style="color: purple">{L_imp_imperator}</a></th></tr>
    <tr><td><a href="imperium.php">{L_imp_overview}</a></th></tr>
    <tr><td><a href="techtree.php" id="lm_techtree">{Technology}</a></td></tr>
    <tr><td><a href="buildings.php?mode=research" id="lm_techtree">{Research}</a></td></tr>
    <tr><td><a href="officier.php">{L_tech[600]}</a></td></tr>
    <tr><td><a href="market.php">{L_rinok}</a></td></tr>
    <tr><td><a href="galaxy.php?mode=0" id="lm_universe">{sys_universe}</a></td></tr>

    <tr><th>{m_communication}</th></tr>
    <tr><td><a href="{forum_url}" id="lm_forum">{m_forum}</a></td></tr>
    <tr><td><a href="messages.php">{Messages}</a></td></tr>
    <tr><td><a href="chat.php" id="lm_chat">{Chat}</a></td></tr>
    <tr><td><a href="alliance.php">{Alliance}</a></td></tr>
    <tr><td><a href="chat.php?chat_type=ally" id="lm_chatally">{AllyChat}</a></td></tr>
    <tr><td><a href="buddy.php">{Buddylist}</a></td></tr>
    <tr><td><a href="search.php" id="lm_search">{Search}</a></td></tr>

    <tr><th>{navig}</th></tr>
    <tr><td><a href="stat.php?start=%7Buser_rank%7D">{Statistics}</a></td></tr>
    <tr><td><a href="records.php">{Records}</a></td></tr>
    <tr><td><a href="banned.php">{L_lm_banned}</a></td></tr>
    <tr><td><a href="contact.php">{commun}</a></td></tr>
    <tr><td><a href="server_info.php">{L_lm_server_info}</a></td></tr>

    <tr><th>{m_others}</th></tr>
    <tr><td><a href="simulator.php" id="lm_simulator">{m_simulator}</a></td></tr>
    <tr><td><a href="notes.php">{Notes}</a></td></tr>
    <tr><td><a href="fleetshortcut.php">{lm_shortcuts}</a></td></tr>

    <tr><th><a href="options.php" id="lm_options">{Options}</a></th></tr>
    <tr><th><a href="javascript:top.location.href='logout.php'" id="lm_logout">{Logout}</a></th></tr>

    <tr><td align="center"><a href="http://games.triolan.ua" target="_blank"><img border="0" src="../design/images/banners/bannergtu.gif" alt="Games @ Triolan.UA"></font></a></td></tr>
    <tr><th><a href="http://wow.triolan.com.ua/" target="_blank"><img border="0" src="../design/images/banners/wow.gif" width="130" height="25" alt="Wow Triolan Server"></a></th></tr>
    <!-- IF ADV_LEFT_BOTTOM -->
    <tr><td align="center">{ADV_LEFT_BOTTOM}</td></tr>
    <!-- ENDIF -->
  </table>
</div>