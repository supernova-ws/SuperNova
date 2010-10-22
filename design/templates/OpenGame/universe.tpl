<script language="JavaScript" src="js/lib/tw-sack.js"></script>
<script language="JavaScript" src="js/universe.js"></script>
<!-- INCLUDE fleet_javascript.tpl -->

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
    <span class="fr"><a href=# style="cursor: pointer;" onmouseout='popup_hide();' onmouseover='popup_show("\
    <table>\
    <tr><td class=c colspan=2>{L_Legend}</td></tr>\
    <tr><td><span class=strong>{L_Strong_player}</span></td><td><span class=strong>{L_strong_player_shortcut}</span></td></tr>\
    <tr><td>{L_Weak_player}</td><td><span class=noob>{L_weak_player_shortcut}</span></td></tr>\
    <tr><td>{L_Way_vacation}</td><td><span class=vacation>{L_vacation_shortcut}</span></td></tr>\
    <tr><td>{L_Pendent_user}</td><td><span class=banned>{L_banned_shortcut}</span></td></tr>\
    <tr><td>{L_Active}</td><td>{L_active_shortcut}</td></tr>\
    <tr><td>{L_Inactive_7_days}</td><td><span class=inactive>{L_inactif_7_shortcut}</span></td></tr>\
    <tr><td>{L_Inactive_28_days}</td><td><span class=longinactive>{L_inactif_28_shortcut}</span></td></tr>\
    <!-- IF SHOW_ADMIN --><tr><td>{L_user_level[3]}</td><td><span class=admin>{L_user_level_shortcut[3]}</span></td></tr>\
    <tr><td>{L_user_level[2]}</td><td><span class=admin>{L_user_level_shortcut[2]}</span></td></tr>\
    <tr><td>{L_user_level[1]}</td><td><span class=admin>{L_user_level_shortcut[1]}</span></td></tr><!-- ENDIF -->\
    </table>");'>{L_Legend}</a>
    </span></td>
  </tr>
  <tr align="center">
    <td class=c>{L_Pos}</td>
    <td class=c>{L_Planet}</td>
    <td class=c>{L_Name}</td>
    <td class=c>{L_Moon}</td>
    <td class=c>{L_uni_debris}</td>
    <td class=c>{L_Player}</td>
    <td class=c>{L_sys_alliance}</td>
    <td class=c>{L_Actions}</td>
  </tr>
<!-- BEGIN galaxyrow -->
  <tr>
    <th width=30 style="white-space: nowrap"><a href="fleet.php?galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&planettype={galaxyrow.PLANET_TYPE}&target_mission=7">{galaxyrow.PLANET_NUM}</a></th>
    <th style="white-space: nowrap;" width=30 valign=middle>
      <!-- IF galaxyrow.PLANET_ID -->
        <div style="position: relative; height: 30px; width: 30px;" onmouseover="javascript:show_planet({galaxyrow.PLANET_NUM}, {galaxyrow.PLANET_TYPE});" >
          <span style="position: absolute; top: 0; left: 0; height: 30px; width: 30px;">
            <img style="cursor: pointer;" src={dpath}planeten/small/s_{galaxyrow.PLANET_IMAGE}.jpg height=30 width=30>
          </span>
          <!-- IF galaxyrow.PLANET_FLEET_ID -->
            <span class="alpha50" style="position: absolute; top: 0; left: 0; height: 100%; width: 100%;">
              <img src=design/images/icon_fleet_own.png width=100%>
            </span>
          <!-- ENDIF -->
        </div>
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </th>
    <th style="white-space: nowrap;" width=130>
      <!-- IF galaxyrow.PLANET_ID -->
        <div class="g_galaxy_row">
          <!-- IF galaxyrow.PLANET_DESTROYED -->
            {L_gl_destroyedplanet}
          <!-- ELSE -->
            <!-- IF USER_ID == galaxyrow.USER_ID -->
              <!-- DEFINE $PLANET_CLASS = 'myplanet' -->
              <!-- DEFINE $PLANET_ACTIVITY = '' -->
            <!-- ELSE -->
              <!-- IF ALLY_ID == galaxyrow.ALLY_ID -->
                <!-- DEFINE $PLANET_CLASS = 'allymember' -->
              <!-- ELSE -->
                <!-- DEFINE $PLANET_CLASS = '' -->
              <!-- ENDIF -->

              <!-- IF galaxyrow.PLANET_ACTIVITY < 15 -->
                <!-- DEFINE $PLANET_ACTIVITY = '<15' -->
              <!-- ELSEIF galaxyrow.PLANET_ACTIVITY < 60 -->
                <!-- DEFINE $PLANET_ACTIVITY = '{galaxyrow.PLANET_ACTIVITY}' -->
              <!-- ELSE -->

              <!-- ENDIF -->
            <!-- ENDIF -->

            <a href=#<!-- IF PLANET_PHALANX --> onclick=fenster('phalanx.php?galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&planettype={galaxyrow.PLANET_TYPE}') title="{L_gl_phalanx}"<!-- ENDIF -->>
              <span class="{$PLANET_CLASS}">
                {galaxyrow.PLANET_NAME}<!-- IF $PLANET_ACTIVITY -->&nbsp;({$PLANET_ACTIVITY}&nbsp;{L_sys_min_short})<!-- ENDIF -->
              </span>
            </a>
          <!-- ENDIF -->
        </div>
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </th>
    <th width=30>
      <!-- IF galaxyrow.MOON_NAME -->
        <div style="position: relative; height: 30px; width: 30px;" onmouseover="javascript:show_planet({galaxyrow.PLANET_NUM}, 3);" >
          <span style="position: absolute; top: 0; left: 0; height: 30px; width: 30px;">
            <img src={dpath}planeten/small/s_mond.jpg height=100% width=100%>
          </span>
          <!-- IF galaxyrow.MOON_FLEET_ID -->
            <span class="alpha50" style="position: absolute; top: 0; left: 0; height: 100%; width: 100%;">
              <img src=design/images/icon_fleet_own.png width=100%>
            </span>
          <!-- ENDIF -->
        </div>
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </th>
      
    <!-- IF galaxyrow.DEBRIS_METAL + galaxyrow.DEBRIS_CRYSTAL >= 10000000 -->
      <!-- DEFINE $DEBRIS_BKG = 'rgb(100, 0, 0)' -->
    <!-- ELSEIF galaxyrow.DEBRIS_METAL + galaxyrow.DEBRIS_CRYSTAL >= 1000000 -->
      <!-- DEFINE $DEBRIS_BKG = 'rgb(100, 100, 0)' -->
    <!-- ELSEIF galaxyrow.DEBRIS_METAL + galaxyrow.DEBRIS_CRYSTAL >= 100000 -->
      <!-- DEFINE $DEBRIS_BKG = 'rgb(0, 100, 0)' -->
    <!-- ELSE -->
      <!-- DEFINE $DEBRIS_BKG = '' -->
    <!-- ENDIF -->

    <th style="white-space: nowrap; background-image: none; width: 60px; height: 100%; background-color: {$DEBRIS_BKG};	"><center>
      <!-- IF galaxyrow.DEBRIS_METAL + galaxyrow.DEBRIS_CRYSTAL -->
        <div style="position: relative; height: 30px; width: 30px;">
          <span style="position: absolute; top: 0; left: 0; height: 30px; width: 30px;">
            <img onmouseover='javascript:show_debris({galaxyrow.PLANET_NUM});' src={dpath}planeten/debris.jpg height=30px width=30px>
          </span>
          <!-- IF galaxyrow.DEBRIS_RC_INC -->
            <span class="icon_alpha" style="position: absolute; bottom: 0; right: 0;">{galaxyrow.DEBRIS_RC_INC}</span>
          <!-- ENDIF -->
        </div>
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </center></th>

    <!-- IF galaxyrow.USER_BANNED -->
      <!-- DEFINE $USER_CLASS = 'banned' -->
    <!-- ELSEIF galaxyrow.USER_VACANCY -->
      <!-- DEFINE $USER_CLASS = 'vacation' -->
    <!-- ELSEIF galaxyrow.USER_ACTIVITY >= 28 -->
      <!-- DEFINE $USER_CLASS = 'longinactive' -->
    <!-- ELSEIF galaxyrow.USER_ACTIVITY >= 7 -->
      <!-- DEFINE $USER_CLASS = 'inactive' -->
    <!-- ELSEIF galaxyrow.USER_NOOB -->
      <!-- DEFINE $USER_CLASS = 'noob' -->
    <!-- ELSEIF galaxyrow.USER_STRONG -->
      <!-- DEFINE $USER_CLASS = 'strong' -->
    <!-- ELSE -->
      <!-- DEFINE $USER_CLASS = '' -->
    <!-- ENDIF -->
    
    <th width=150 align=center>
      <!-- IF galaxyrow.USER_ID -->
        <span style="cursor: pointer;" onmouseover='javascript:show_user({galaxyrow.USER_ID});' class="{$USER_CLASS}">{galaxyrow.USER_NAME}</span>&nbsp;(<!-- IF SHOW_ADMIN && galaxyrow.USER_AUTH && galaxyrow.USER_ADMIN --><span class="admin">{galaxyrow.USER_ADMIN}</span><!-- ENDIF --><!-- IF galaxyrow.USER_BANNED --><span class="banned">{L_banned_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_VACANCY --><span class="vacation">{L_vacation_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_ACTIVITY >= 28 --><span class="longinactive">{L_inactif_28_shortcut}</span><!-- ELSEIF galaxyrow.USER_ACTIVITY >= 7 --><span class="inactive">{L_inactif_7_shortcut}</span><!-- ELSE -->{L_active_shortcut}<!-- ENDIF --><!-- IF galaxyrow.USER_NOOB --><span class="noob">{L_weak_player_shortcut}</span><!-- ENDIF --><!-- IF galaxyrow.USER_STRONG --><span class="strong">{L_strong_player_shortcut}</span><!-- ENDIF -->)
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </th>

    <th width=80>
      <!-- IF galaxyrow.ALLY_ID -->
        <span style="line-height: 1em; height: 1em; cursor: pointer;" onmouseover='javascript:show_alliance({galaxyrow.ALLY_ID});' class="<!-- IF ALLY_ID == galaxyrow.ALLY_ID -->allymember<!-- ENDIF -->">{galaxyrow.ALLY_TAG}</span>
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </th>

    <th style="white-space: nowrap" width=125 align="center"><center>
      <!-- IF galaxyrow.USER_ID && USER_ID != galaxyrow.USER_ID -->
        <!-- IF ACT_SPY -->
          <span class="fl">
            <a>
              <img onclick="javascript:doit(6, {galaxy}, {system}, {galaxyrow.PLANET_NUM}, 1, {ACT_SPIO});" src={dpath}img/e.gif alt="{L_gl_espionner}" title="{L_gl_espionner}" border=0 style="cursor: pointer;">
            </a>&nbsp;
          </span>
        <!-- ENDIF -->
        <!-- IF ACT_WRITE -->
        <span class="fl">
          <a href="messages.php?mode=write&id={galaxyrow.USER_ID}">
            <img src={dpath}img/m.gif alt="{L_gl_sendmess}" title="{L_gl_sendmess}" border=0  style="cursor: pointer;">
          </a>&nbsp;
        </span>
        <!-- ENDIF -->
        <!-- IF ACT_FRIEND -->
        <span class="fl">
          <a href="buddy.php?a=2&u={galaxyrow.USER_ID}">
            <img src={dpath}img/b.gif alt="{L_gl_buddyreq}" title="{L_gl_buddyreq}" border=0>
          </a>&nbsp;
        </span>
        <!-- ENDIF -->
        <!-- IF ACT_MISSILE -->
          <span class="fl">
            <a href="galaxy.php?mode=2&galaxy={galaxy}&system={system}&planet={galaxyrow.PLANET_NUM}&current={curPlanetID}">
              <img src={dpath}img/r.gif alt="{L_gl_mipattack}" title="{L_gl_mipattack}" border=0>
            </a>
          </span>
        <!-- ENDIF -->
      <!-- ELSE -->
        &nbsp;
      <!-- ENDIF -->
    </center></th>
  </tr>
<!-- END galaxyrow -->
  <tr>
    <th width="30">16</th>
    <th colspan=7><a href="fleet.php?galaxy={galaxy}&system={system}&planet=16&planettype=1&target_mission=15">{L_gf_unknowsp}</a></th>
  </tr>
  <tr>
    <td class=c colspan=3><span id="missiles">{MIPs}</span> {L_gf_mi_title}</td>
    <td class=c colspan=3><span id="slots">{fleet_count}</span>/{fleet_max} {L_gf_fleetslt}</td>
    <td class=c colspan=2><span id="recyclers">{RCs}</span> {L_gf_rc_title}<br><span id="probes">{SPs}</span> {L_gf_sp_title}</td>
  </tr>
  <tr style="display: none;" id="fleetstatusrow"><th class=c colspan=8><table style="font-weight: bold" width="100%" id="fleetstatustable"></table></th></tr>
</tbody></table>
<!-- INCLUDE page_hint.tpl -->

<script LANGUAGE='JavaScript'><!--
var uni_galaxy = {galaxy};
var uni_system = {system};

var user_id = {USER_ID};
var game_user_count = {userCount};
var dpath = "{dpath}";
var uni_phalanx = '{PLANET_PHALANX}';
var uni_spies = '{ACT_SPIO}';
var uni_death_stars = '{deathStars}';

jQuery.extend(language, {
  debris: '{L_Debris}',
  gal_sys_members: '{L_gal_sys_members}',
  gl_action: '{L_gl_action}',
  gl_ally_internal: '{L_gl_ally_internal}',
  gl_ally_web: '{L_gl_ally_web}',
  gl_buddyreq: '{L_gl_buddyreq}', 
  gl_phalanx: '{L_gl_phalanx}',
  gl_ressource: '{L_gl_ressource}',
  gl_sendmess: '{L_gl_sendmess}', 
  gl_stats: '{L_gl_stats}',
  place: '{L_Place}', 
  sys_alliance: '{L_sys_alliance}',
  sys_crystal: '{L_sys_crystal}',
  sys_metal: '{L_sys_metal}',
  sys_moon: '{L_Moon}',
  sys_planet: '{L_gl_planet}',
  sys_player: '{L_sys_player}', 
  type_mission1: '{L_type_mission[1]}',
  type_mission3: '{L_type_mission[3]}',
  type_mission4: '{L_type_mission[4]}',
  type_mission5: '{L_type_mission[5]}',
  type_mission6: '{L_type_mission[6]}',
  type_mission8: '{L_type_mission[8]}',
  type_mission9: '{L_type_mission[9]}',
  sys_ships: '{L_uni_incoming_fleets}',
  tech_209: '{L_tech[209]}'
});

var users = new Array();
<!-- BEGIN users -->
users[{users.ID}] = {name: '{users.NAME}', rank: '{users.RANK}'};
<!-- END users -->

var allies = new Array();
<!-- BEGIN alliances -->
allies[{alliances.ID}] = {name: '{alliances.NAME}', members: '{alliances.MEMBERS}', url: '{alliances.URL}'};
<!-- END alliances -->

var uni_row = new Array();
<!-- BEGIN galaxyrow -->uni_row[{galaxyrow.PLANET_NUM}] = 
{
  owner: '{galaxyrow.USER_ID}',
  planet: '{galaxyrow.PLANET_NUM}',
  planet_name: '{galaxyrow.PLANET_NAME}',
  planet_image: '{galaxyrow.PLANET_IMAGE}',
  planet_fleet_id: '{galaxyrow.PLANET_FLEET_ID}',
  moon_name: '{galaxyrow.MOON_NAME}',
  moon_diameter: '{galaxyrow.MOON_DIAMETER}',
  moon_image: 'mond',
  moon_fleet_id: '{galaxyrow.MOON_FLEET_ID}',
  debris_metal: '{galaxyrow.DEBRIS_METAL}',
  debris_crystal: '{galaxyrow.DEBRIS_CRYSTAL}',
  debris_recyclers: '{galaxyrow.DEBRIS_RC_SEND}',
  debris_incoming: '{galaxyrow.DEBRIS_RC_INC}',
  debris_rc_need: '{galaxyrow.DEBRIS_RC_NEED}'
};
<!-- END galaxyrow -->

--></script>