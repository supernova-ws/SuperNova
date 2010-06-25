<script language="JavaScript" src="scripts/tw-sack.js"></script>

<script type="text/javascript"><!--
function galaxy_submit(value) {
  document.getElementById('auto').name = value;
  document.getElementById('galaxy_form').submit();
}

function fenster(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');
  new_win.focus();
}

var ajax = new sack();
var strInfo = "";

function whenResponse () {
  retVals   = this.response.split("|");
  Message   = retVals[0];
  Infos     = retVals[1];
  retVals   = Infos.split(" ");
  UsedSlots = retVals[0];
  SpyProbes = retVals[1];
  Recyclers = retVals[2];
  Missiles  = retVals[3];
  retVals   = Message.split(";");
  CmdCode   = retVals[0];
  strInfo   = retVals[1];
  addToTable("done", "success");
  changeSlots( UsedSlots );
  setShips("probes", SpyProbes );
  setShips("recyclers", Recyclers );
  setShips("missiles", Missiles );
}

function addToTable(strDataResult, strClass) {
  var e = document.getElementById('fleetstatusrow');
  var e2 = document.getElementById('fleetstatustable');
  e.style.display = '';
  if(e2.rows.length > 2) {
    e2.deleteRow(2);
  }
  var row = e2.insertRow(0);
  var td1 = document.createElement("td");
  var td1text = document.createTextNode(strInfo);
  td1.appendChild(td1text);
  var td2 = document.createElement("td");
  var span = document.createElement("span");
  var spantext = document.createTextNode(strDataResult);
  var spanclass = document.createAttribute("class");
  spanclass.nodeValue = strClass;
  span.setAttributeNode(spanclass);
  span.appendChild(spantext);
  td2.appendChild(span);
  row.appendChild(td1);
  row.appendChild(td2);
}

function changeSlots(slotsInUse) {
  var e = document.getElementById('slots');
  e.innerHTML = slotsInUse;
}

function setShips(ship, count) {
  var e = document.getElementById(ship);
  e.innerHTML = count;
}
// --></script>

<script type="text/javascript"><!--
function doit (order, galaxy, system, planet, planettype, shipcount) {
  ajax.requestFile = "flotenajax.php?action=send";
  ajax.runResponse = whenResponse;
  ajax.execute = true;
  ajax.setVar("thisgalaxy", "{curPlanetG}");
  ajax.setVar("thissystem", "{curPlanetS}");
  ajax.setVar("thisplanet", "{curPlanetP}");
  ajax.setVar("thisplanettype", "{curPlanetPT}");
  ajax.setVar("mission", order);
  ajax.setVar("galaxy", galaxy);
  ajax.setVar("system", system);
  ajax.setVar("planet", planet);
  ajax.setVar("planettype", planettype);
  if (order == 6)
    ajax.setVar("ship210", shipcount);
  if (order == 7) {
    ajax.setVar("ship208", 1);
    ajax.setVar("ship203", 2);
  }
  if (order == 8)
    ajax.setVar("ship209", shipcount);
  ajax.runAJAX();
}
// --></script>

<script type="text/javascript"><!--
var allies = new Array();

function showAlly(id){
  var result = "<table><tr><td class=c><center>{L_sys_alliance}&nbsp;";
  result = result + allies[id][1] + "<br>{L_gal_sys_members}" +  allies[id][2] + "</center></td></tr>";
  result = result + "<tr><th><a href=alliance.php?mode=ainfo&a=" + id + ">{L_gl_ally_internal}</a></th></tr>";
  result = result + "<tr><th><a href=stat.php?start=1&who=ally>{L_gl_stats}</a></th></tr>";
  if (allies[id][0]) {
    result = result + "<tr><th><a href=" + allies[id][0] + " target=_new>{L_gl_ally_web}</th></tr>";
  }
  result = result + "</table>";

  return overlib(result, STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -10, OFFSETY, -45);
}
// --></script>

<script type="text/javascript"><!--
var users = new Array();

function showUser(id){
  var result = "<table width=190><tr><td class=c><center>{L_sys_player}&nbsp;" + users[id][0] + "<br>{L_Place}&nbsp;" + users[id][1] + "/{userCount}</center></td></tr>";

  if (id != curUserID) {
    result = result + "<tr><th><a href=messages.php?mode=write&id=" + id + ">{L_gl_sendmess}</a></th></tr>";
    result = result + "<tr><th><a href=buddy.php?a=2&u=" + id + ">{L_gl_buddyreq}</a></th></tr>";
  }
  result = result + "<tr><th><a href=stat.php?who=player&start=" + users[id][1] + ">{L_gl_stats}</a></th></tr></table>";

  return overlib(result, STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -45);
}
// --></script>

<script type="text/javascript"><!--
var planets = new Array();

function showPlanet(id){
  var result = "<table width=190><tr><td class=c><center>{L_sys_player}&nbsp;" + users[id][0] + "<br>{L_Place}&nbsp;" + users[id][1] + "/{userCount}</center></td></tr>";

  if (id != curUserID) {
    result = result + "<tr><th><a href=messages.php?mode=write&id=" + id + ">{L_gl_sendmess}</a></th></tr>";
    result = result + "<tr><th><a href=buddy.php?a=2&u=" + id + ">{L_gl_buddyreq}</a></th></tr>";
  }
  result = result + "<tr><th><a href=stat.php?who=player&start=" + users[id][1] + ">{L_gl_stats}</a></th></tr></table>";

  return overlib(result, STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -45);
}
// --></script>

<br />
<form action="galaxy.php?mode=1" method="post" id="galaxy_form"><table border="0"><tbody>
  <tr>
    <td>
      <table><tbody><tr>
        <td class="c" colspan="3">{L_Galaxy}</td></tr><tr>
        <td class="l"><input name="galaxyLeft" value="&lt;-" type="button" onClick="this.form.galaxy.value--;this.form.submit();"></td>
        <td class="l"><input name="galaxy" value="{galaxy}" size="5" maxlength="3" tabindex="1" type="text"></td>
        <td class="l"><input name="galaxyRight" value="-&gt;" type="button" onClick="this.form.galaxy.value++;this.form.submit();"></td>
      </tr></tbody></table>
    </td>
    <td>
      <table><tbody><tr>
        <td class="c" colspan="3">{L_Solar_system}</td></tr><tr>
        <td class="l"><input name="systemLeft" value="&lt;-" type="button" onClick="this.form.system.value--;this.form.submit();"></td>
        <td class="l"><input name="system" value="{system}" size="5" maxlength="3" tabindex="2" type="text"></td>
        <td class="l"><input name="systemRight" value="-&gt;" type="button" onClick="this.form.system.value++;this.form.submit();"></td>
      </tr></tbody></table>
    </td>
  </tr>
  <tr>
    <td class="l" colspan="2" align="center">
      <input value="{L_sys_goto}" type="submit">
      <input type="hidden" id="auto" value="dr" >
    </td>
  </tr>
</tbody></table></form>

<!-- IF MODE == 2 -->
<form action="raketenangriff.php?c={curPlanetID}&mode=2&galaxy={galaxy}&system={system}&planet={planet}" method=POST><table border=0>
  <tr>
    <td class=c colspan=3>
       <span class="fl">{L_gm_launch} [{galaxy}:{system}:{planet}]</span>
       <span class="fr">{L_gal_mis_rest}{MIPs}</span>
    </td>
  </tr>
  <tr>
    <th class=c>{L_gal_mis_toLaunch} <input type=text name=SendMI size=7 maxlength=7 /></th>
    <th class=c>{L_gm_target}
      <select name=Target>
        <option value=all selected>{L_gm_all}</option>
        <option value=401>{L_tech[401]}</option>
        <option value=402>{L_tech[402]}</option>
        <option value=403>{L_tech[403]}</option>
        <option value=404>{L_tech[404]}</option>
        <option value=405>{L_tech[405]}</option>
        <option value=406>{L_tech[406]}</option>
        <option value=407>{L_tech[407]}</option>
        <option value=408>{L_tech[408]}</option>
        <option value=409>{L_tech[409]}</option>
      </select>
    </th>
    <th class=c><input type=submit name=aktion value={L_gal_mis_launch}></th>
  </tr>
</table></form>
<!-- ENDIF -->
<table width=569><tbody>
  <tr><td class=c colspan=8><span class="fl">{L_Solar_system} [{galaxy}:{system}] - {planets}</span>
    <span class="fr"><a href=# style="cursor: pointer;" onmouseout='return nd();' onmouseover='return overlib("\
    <table width=240>\
    <tr><td class=c colspan=2>{L_Legend}</td></tr>\
    <tr><td width=220><span class=strong>{L_Strong_player}</span></td><td><span class=strong>{L_strong_player_shortcut}</span></td></tr>\
    <tr><td width=220>{L_Weak_player}</td><td><span class=noob>{L_weak_player_shortcut}</span></td></tr>\
    <tr><td width=220>{L_Way_vacation}</td><td><span class=vacation>{L_vacation_shortcut}</span></td></tr>\
    <tr><td width=220>{L_Pendent_user}</td><td><span class=banned>{L_banned_shortcut}</span></td></tr>\
    <tr><td width=220>{L_Inactive_7_days}</td><td><span class=inactive>{L_inactif_7_shortcut}</span></td></tr>\
    <tr><td width=220>{L_Inactive_28_days}</td><td><span class=longinactive>{L_inactif_28_shortcut}</span></td></tr>\
    <!-- IF SHOW_ADMIN --><tr><td width=220>{L_user_level[3]}</td><td><span class=admin>{L_user_level_shortcut[3]}</span></td></tr>\
    <tr><td width=220>{L_user_level[2]}</td><td><span class=admin>{L_user_level_shortcut[2]}</span></td></tr>\
    <tr><td width=220>{L_user_level[1]}</td><td><span class=admin>{L_user_level_shortcut[1]}</span></td></tr><!-- ENDIF -->\
    </table>", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -150, OFFSETY, -150 );'>{L_Legend}</a>
    </span></td>
  </tr>
  <tr align="center">
    <td class=c>{L_Pos}</td>
    <td class=c>{L_Planet}</td>
    <td class=c>{L_Name}</td>
    <td class=c>{L_Moon}</td>
    <td class=c>{L_Debris}</td>
    <td class=c>{L_Player}</td>
    <td class=c>{L_sys_alliance}</td>
    <td class=c>{L_Actions}</td>
  </tr>

<!-- BEGIN galaxyrow -->
  <tr>
    <th width=30 style="white-space: nowrap"><a href="fleet.php?galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&planettype=0&target_mission=7">{galaxyrow.PLANET_NUM}</a></th>
    {galaxyrow.PLANET}
    <th style="white-space: nowrap;" width=130><!-- IF galaxyrow.PLANET_ID --><div class="g_galaxy_row"><!-- IF galaxyrow.PLANET_DESTROYED -->{L_gl_destroyedplanet}<!-- ELSE --><a href=#<!-- IF galaxyrow.PLANET_PHALANX --> onclick=fenster('phalanx.php?galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&planettype={galaxyrow.PLANET_TYPE}') title="{L_gl_phalanx}"<!-- ENDIF -->><span class="<!-- IF USER_ID == galaxyrow.USER_ID -->myplanet<!-- ELSEIF ALLY_ID == galaxyrow.ALLY_ID -->allymember<!-- ENDIF -->">{galaxyrow.PLANET_NAME}&nbsp;<!-- IF USER_ID != galaxyrow.USER_ID --><!-- IF galaxyrow.PLANET_ACTIVITY < 15 -->({L_sys_lessThen15min})<!-- ELSEIF galaxyrow.PLANET_ACTIVITY < 60 -->({galaxyrow.PLANET_ACTIVITY}&nbsp;{L_sys_min_short})<!-- ENDIF --><!-- ENDIF --></span></a><!-- ENDIF --></div><!-- ELSE -->&nbsp;<!-- ENDIF --></th>
    {galaxyrow.MOON}
    {galaxyrow.DEBRIS}
    <th width=150 align=center><!-- IF galaxyrow.USER_ID --><a style="cursor: pointer;" onmouseover='javascript:showUser({galaxyrow.USER_ID});' onmouseout='return nd();'><span class="<!-- IF galaxyrow.USER_BANNED -->banned<!-- ELSEIF galaxyrow.USER_VACANCY -->vacation<!-- ELSEIF galaxyrow.USER_ACTIVITY >= 28 -->longinactive<!-- ELSEIF galaxyrow.USER_ACTIVITY >= 7 -->inactive<!-- ELSEIF galaxyrow.USER_NOOB -->noob<!-- ELSEIF galaxyrow.USER_STRONG -->strong<!-- ENDIF -->">{galaxyrow.USER_NAME}</span>&nbsp;(<!-- IF SHOW_ADMIN && galaxyrow.USER_AUTH && galaxyrow.USER_ADMIN --><span class="admin">{galaxyrow.USER_ADMIN}</span><!-- ENDIF --><!-- IF galaxyrow.USER_BANNED --><span class="banned">{L_banned_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_VACANCY --><span class="vacation">{L_vacation_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_ACTIVITY >= 28 --><span class="longinactive">{L_inactif_28_shortcut}</span><!-- ELSEIF galaxyrow.USER_ACTIVITY >= 7 --><span class="inactive">{L_inactif_7_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_NOOB --><span class="noob">{L_weak_player_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_STRONG --><span class="strong">{L_strong_player_shortcut}</span><!-- ENDIF -->)</a><!-- ELSE -->&nbsp;<!-- ENDIF --></th>
    <th width=80><!-- IF galaxyrow.ALLY_ID --><div style="line-height: 1em; height: 1em"><a style="cursor: pointer;" onmouseover='javascript:showAlly({galaxyrow.ALLY_ID});' onmouseout='return nd();'><span class="<!-- IF ALLY_ID == galaxyrow.ALLY_ID -->allymember<!-- ENDIF -->">{galaxyrow.ALLY_TAG}</span></a></div><!-- ELSE -->&nbsp;<!-- ENDIF --></th>
    <th style="white-space: nowrap" width=125 align="center"><!-- IF galaxyrow.USER_ID && USER_ID != galaxyrow.USER_ID --><!-- IF ACT_SPY --><a 
        href=# onclick="javascript:doit(6, {galaxy}, {system}, {galaxyrow.PLANET_NUM}, 1, {ACT_SPIO});"><img 
        src={dpath}img/e.gif alt="{L_gl_espionner}" title="{L_gl_espionner}" 
        border=0></a><!-- ENDIF -->&nbsp;<!-- IF ACT_WRITE --><a 
        href=messages.php?mode=write&id={galaxyrow.USER_ID}><img src={dpath}img/m.gif alt="{L_gl_sendmess}" 
        title="{L_gl_sendmess}" border=0></a><!-- ENDIF -->&nbsp;<!-- IF ACT_FRIEND --><a 
        href=buddy.php?a=2&u={galaxyrow.USER_ID}><img src={dpath}img/b.gif alt="{L_gl_buddyreq}" title="{L_gl_buddyreq}" 
        border=0></a><!-- ENDIF -->&nbsp;<!-- IF galaxyrow.ACT_MISSILE --><a 
        href=galaxy.php?mode=2&galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&current={curPlanetID}><img 
        src={dpath}img/r.gif alt="{L_gl_mipattack}" title="{L_gl_mipattack}" border=0></a><!-- ENDIF --><!-- ELSE -->&nbsp;<!-- ENDIF --></th>
  </tr>
<!-- END galaxyrow -->

  <tr>
    <th width="30">16</th>
    <th colspan=7><a href="fleet.php?galaxy={galaxy}&system={system}&planet=16&planettype=1&target_mission=15">{L_gf_unknowsp}</a></th>
  </tr>

  <tr>
    <td class=c colspan=3><span id="missiles">{MIPs}</span> {L_gf_mi_title}</td>
    <td class=c colspan=3><span id="slots">{fleet_count}</span>/{fleet_max} {L_gf_fleetslt}</td>
    <td class=c colspan=2>
      <span id="recyclers">{RCs}</span> {L_gf_rc_title}<br>
      <span id="probes">{SPs}</span> {L_gf_sp_title}
    </td>
  </tr>

  <tr style="display: none;" id="fleetstatusrow"><th class=c colspan=8>
    <table style="font-weight: bold" width="100%" id="fleetstatustable"></table>
  </th></tr>
</tbody></table>
<br>
<table width="519">
  <tr><td class="c">{L_sys_hint}</td></tr>
  <tr><th><div class="hint">{L_gal_sys_hint}</div></th></tr>
</table>

<script LANGUAGE='JavaScript'><!--
{script}
var curUserID = {USER_ID};
--></script>