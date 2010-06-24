<script language="JavaScript">
function f(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
  new_win.focus();
}
</script>
<style type="text/css">
<!--
.style1 {color: #FF0000; font-weight: bold}
.style2 {
	color: #FF0000;
	font-weight: bold;
	font-size: xx-small;
}
.style4 {
	color: #00FF00;
	font-weight: bold;
}
.style6 {color: #FF0000; font-weight: bold; font-size: x-small; }
.style7 {color: #FF00FF}
.style8 {color: #00FFFF}
.style9 {color: #FF0000}
.style10 {color: #FFFF00}
.style11 {color: #FFFFFF}
.important {color: #00FF00}
.leftmenu_header {color: #FFFFFF}
-->
</style>

<style type="text/css">
td.lm_para {
  color: #63698c;
  background-image     : url({dpath}img/bg1.gif);
  background-color : #212c42;
  text-align: center;
}

.lm_overview {
/*  color: #949A39;*/
  color: #999933;
}

.lm_fleet {
/*  color: #B12326;*/
  color: #CC3333;
}

.lm_galaxy {
/*  color: #948E52;*/
  color: #999966;
}

.lm_buildings {
/*  color: #7B964A;*/
  color: #669933;
}

.lm_shipyard {
/*  color: #9C20A5;*/
  color: #993399;
}

.lm_techtree {
  color: #999933;
/*  color: #9C8242;*/
}

.lm_forum {
  color: #0066CC;
}

.lm_chat {
  color: #996699;
}

.lm_chatally {
  color: #996699;
}

.lm_simulator {
  color: #FF9966;
}

.lm_search {
/*  color: #BDBA7B;*/
  color: #887766;
}

.lm_options {
/*  color: #8C7D63;*/
  color: #CCCC66;
}

.lm_logout {
/*  color: #943439;*/
  color: #993333;
}

</style>

<table width="130" cellspacing="0" cellpadding="0" style="font-weight: bold;" align="center">
  <tr><td style="border-top: 1px #545454 solid" align="center">
    <span class="style4">{servername}</span>
  </td></tr>

  <tr><td align="center"><a href="http://games.triolan.ua" target="_blank"><img border="0" src="/images/banners/bannergtu.gif" alt="Games @ Triolan.UA"></font></a></td></tr>

  <tr><td class="lm_para">{m_h_rules}</td></tr>
  <tr><td><a href="http://forum.supernova.ws/viewtopic.php?f=3&t=974">Правила игры</a></td></tr>
<!--  <tr><td title="{m_faq_hint}"><a href="http://forum.supernova.ws/phpBB3/viewtopic.php?f=3&t=333">{m_faq}</a></td></tr> -->
<!--  <tr><td><a href="faq.php">{m_faq}</a></td></tr> -->
  <tr><td><a href="announce.php">{adm_announce}</a></td></tr>
  <tr><td class="lm_para"><a href="affilates.php"><span class="lm_logout">{m_affilates}</span></a></td></tr>

  <tr><td class="lm_para">{m_h_control}</td></tr>
  <tr><td><a href="overview.php"><span class="lm_overview">{Overview}</span></a></td></tr>
  <tr><td><a href="resources.php" accesskey="r">{Resources}</a></td></tr>
  <tr><td><a href="fleet.php" accesskey="t"><span class="lm_fleet">{Fleet}</span></a></td></tr>
  <tr><td><a href="galaxy.php?mode=0" accesskey="s"><span class="lm_galaxy">{sys_universe}</span></a></td></tr>
  <tr><td><a href="officier.php" accesskey="o">{Officiers}</a></td></tr>
  <tr><td><a href="rinok.php" accesskey="m">{rinok}</a></td></tr>
<!--  <tr><td><a href="market.php" accesskey="m">{rinok}</a></td></tr> -->

  <tr><td class="lm_para">{Building}</td></tr>
  <tr><td><a href="buildings.php" accesskey="b"><span class="lm_buildings">{Buildings}</span></a></td></tr>
  <tr><td><a href="buildings.php?mode=fleet" accesskey="f"><span class="lm_shipyard">{Shipyard}</span></a></td></tr>
  <tr><td><a href="buildings.php?mode=defense" accesskey="d">{Defense}</a></td></tr>
  <tr><td><a href="buildings.php?mode=research" accesskey="r">{Research}</a></td></tr>

  <tr><td class="lm_para">{navig}</td></tr>
  <tr><td><a href="techtree.php" accesskey="g"><span class="lm_techtree">{Technology}</span></a></td></tr>
  <tr><td><a href="imperium.php" accesskey="i">{Imperium}</a></td></tr>
  <tr><td><a href="annonce.php" accesskey="m">{m_exchange}</a></td></tr>
  <tr><td><a href="stat.php?start=%7Buser_rank%7D" accesskey="3">{Statistics}</a></td></tr>
  <tr><td><a href="records.php">{Records}</a></td></tr>
  
  <tr><td class="lm_para">{m_communication}</td></tr>
  <tr><td><a href="messages.php" accesskey="c">{Messages}</a></td></tr>
  <tr><td><a href="{forum_url}" accesskey="1"><span class="lm_forum">{m_forum}</span></a></td></tr>
  <tr><td><a href="chat.php" accesskey="a"><span class="lm_chat">{Chat}</span></a></td></tr>
  <tr><td><a href="alliance.php" accesskey="a">{Alliance}</a></td></tr>
  <tr><td><a href="chat.php?chat_type=ally" accesskey="a"><span class="lm_chatally">{AllyChat}</span></a></td></tr>

  <tr><td class="lm_para">{m_others}</td></tr>
  <tr><td><a href="simulator.php" accesskey="p"><span class="lm_simulator">{m_simulator}</span></a></td></tr>
  <tr><td><a href="buddy.php">{Buddylist}</a></td></tr>
  <tr><td><a href="notes.php">{Notes}</a></td></tr>
  <tr><td><a href="fleetshortcut.php">{lm_shortcuts}</a></td></tr>
  <tr><td><a href="search.php"><span class="lm_search">{Search}</span></a></td></tr>

  <tr><td class="lm_para">{tool}</td></tr>
  <tr><td><a href="options.php" accesskey="o"><span class="lm_options">{Options}</span></a></td></tr>
  <tr><td><a href="contact.php" accesskey="3">{commun}</a></td></tr>
  <tr><td class="lm_para"><a href="javascript:top.location.href='logout.php'"><span class="lm_logout">{Logout}</span></a></td></tr>
  {ADMIN_LINK}

  <tr><td class="lm_para"><a href="http://wow.triolan.com.ua/" target="_blank"><img border="0" src="/images/banners/wow.gif" width="130" height="25" alt="Wow Triolan Server"></a></td></tr>
  {GoogleCode}
</table>