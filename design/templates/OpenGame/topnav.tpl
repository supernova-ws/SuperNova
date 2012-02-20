<div id="sn_navbar">
<table width="720px" cellpadding="0" cellspacing="0" class="header c_c" id="resources"><tbody>
  <tr class="c">
    <td class="c" colspan=7>
      <span class="fl">{DATE_TEXT}<span id="top_time">{TIME_TEXT}</span></span>
      <span class="fr">{L_top_online} [{USERS_ONLINE}/{USERS_TOTAL}]</span>
    </td>
  </tr>

  <tr class="header">
    <td class="header" align="center" width="150">
      <select size="1" onchange="window.location.href = window.location.pathname + this.options[this.selectedIndex].value;">
        <!-- BEGIN topnav_planets -->
          <option{topnav_planets.SELECTED} value="?cp={topnav_planets.ID}&mode={TOPNAV_MODE}&re=0">{topnav_planets.NAME}&nbsp;{topnav_planets.COORDS}
        <!-- END topnav_planets -->
      </select>
    </td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/metall.gif" border="0" height="22" width="42" alt="{L_sys_metal}" title="{L_sys_metal}"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/kristall.gif" border="0" height="22" width="42" alt="{L_sys_crystal}" title="{L_sys_crystal}"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/deuterium.gif" border="0" height="22" width="42" alt="{L_sys_deuterium}" title="{L_sys_deuterium}"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/energie.gif" border="0" height="22" width="42" alt="{L_sys_energy}" title="{L_sys_energy}"></A></td>
    <td class="header" align="center" width="140"><A HREF="dark_matter.php"><img src="{dpath}images/dm_klein_2.jpg" border="0" height="22" width="42" alt="{L_sys_dark_matter}" title="{L_sys_dark_matter}"></A></td>
    <td class="header" align="center" width="140"><A HREF="messages.php"><img src="{dpath}images/message.gif" border="0" height="22" width="42" alt="{L_Message}" title="{L_Message}"></A></td>
  </tr>
  <tr valign=middle><form name='ressources'> <center>
    <td class="c_c" width="150"><b><font color="#FFFF00">{L_tech_storage}</font></b></td>
    <td class="c_r" width="150"><span id='top_metal'>{TOPNAV_METAL_TEXT}</span></td>
    <td class="c_r" width="150"><span id='top_crystal'>{TOPNAV_CRYSTAL_TEXT}</span></td>
    <td class="c_r" width="150"><span id='top_deuterium'>{TOPNAV_DEUTERIUM_TEXT}</span></td>
    <td class="c_r" width="150">{ENERGY_BALANCE}</td>
    <td class="c_c" width="140" rowspan="2"><font color="green"><b>{TOPNAV_DARK_MATTER}</b></font></td>

    <td class="c_c" valign=middle width="140" rowspan="2" >
      <!-- IF TOPNAV_MESSAGES_PLAYER + TOPNAV_MESSAGES_ALLIANCE + TOPNAV_MESSAGES_ADMIN -->
        <div>
          <!-- IF TOPNAV_MESSAGES_ADMIN --><a href="messages.php?mode=show&message_class={D_MSG_TYPE_ADMIN}"><span class="msg_admin">[{TOPNAV_MESSAGES_ADMIN}]</span></a><!-- ENDIF -->
          <!-- IF TOPNAV_MESSAGES_PLAYER -->&nbsp;<a href="messages.php?mode=show&message_class={D_MSG_TYPE_PLAYER}"><span class="mnl_joueur">[{TOPNAV_MESSAGES_PLAYER}]</span></a><!-- ENDIF -->
          <!-- IF TOPNAV_MESSAGES_ALLIANCE && ! TOPNAV_ALLY -->&nbsp;<a href="messages.php?mode=show&message_class={D_MSG_TYPE_ALLIANCE}"><span class="mnl_alliance">[{TOPNAV_MESSAGES_ALLIANCE}]</span></a><!-- ENDIF -->
        </div>
      <!-- ENDIF -->
      <!-- IF TOPNAV_MESSAGES_ALL -->
        <a href="messages.php"><blink>[ {TOPNAV_MESSAGES_ALL} ]</blink></a>
      <!-- ELSE -->
        <a href="messages.php">0</a>
      <!-- ENDIF -->
    </td>
  </form></tr>

  <tr>
    <td class="header c_c" width="150"><b><font color="#FFFF00">{L_tech_storage_max}</font></b></td>
    <td class="header c_r" width="150">{TOPNAV_METAL_MAX_TEXT}</td>
    <td class="header c_r" width="150">{TOPNAV_CRYSTAL_MAX_TEXT}</td>
    <td class="header c_r" width="150">{TOPNAV_DEUTERIUM_MAX_TEXT}</td>
    <td class="header c_r" width="150"><font color="#00ff00">{ENERGY_MAX}</font></td>
  </tr>

<!-- IF TOPNAV_ALLY -->
  <tr valign=middle>
    <td class="c_c" width="150"><b><font color="#FFFF00">{L_sys_ali_res_title}</font></b></td>
    <td class="c_r" width="150">{TOPNAV_ALLY_METAL}</td>
    <td class="c_r" width="150">{TOPNAV_ALLY_CRYSTAL}</td>
    <td class="c_r" width="150">{TOPNAV_ALLY_DEUTERIUM}</td>
    <td class="c_r <!-- IF TOPNAV_ALLY_BONUS -->positive<!-- ELSE -->negative<!-- ENDIF -->" width="150">{L_sys_bonus}:&nbsp;<!-- IF TOPNAV_ALLY_BONUS -->{L_sys_yes}<!-- ELSE -->{L_sys_no}<!-- ENDIF --></td>
    <td class="c_c" width="150">{TOPNAV_ALLY_DARK_MATTER}</td>
    <td class="c_c" width="150"><!-- IF TOPNAV_MESSAGES_ALLIANCE --><a href="messages.php?mode=show&message_class={D_MSG_TYPE_ALLIANCE}"><span class="mnl_alliance">[{TOPNAV_MESSAGES_ALLIANCE}]</span></a><!-- ENDIF --></td>
  </tr>
<!-- ENDIF -->

  <tr class="c_c">
    <td colspan=1 id="topnav_expedition_counter_total">
      <a href="flying_fleets.php">{L_sys_expeditions} <span id='topnav_expedition_counter'>{TOPNAV_EXPEDITIONS_FLYING}</span>/{TOPNAV_EXPEDITIONS_TOTAL}</a>
    </td>
    <td colspan=1 id='topnav_fleet_counter_total'>
      <a href="flying_fleets.php">{L_sys_fleets} <span id='topnav_fleet_counter'>{TOPNAV_FLEETS_FLYING}</span>/{TOPNAV_FLEETS_TOTAL}</a>
    </td>
    <td class="c_c" colspan=1>
      <a href="quest.php">{L_qst_quests} {TOPNAV_QUEST_COMPLETE}/{C_quest_total}</a>
    </td>
  </tr>
</tbody></table>
</div>

<script LANGUAGE='JavaScript'><!--
  sn_timers.unshift({id: 'top_metal', type: 1, active: true, start_time: {TIME_NOW}, options: {start_value: {TOPNAV_METAL}, per_second: {TOPNAV_METAL_PERHOUR} / 3600, max_value: {TOPNAV_METAL_MAX}}});
  sn_timers.unshift({id: 'top_crystal', type: 1, active: true, start_time: {TIME_NOW}, options: {start_value: {TOPNAV_CRYSTAL}, per_second: {TOPNAV_CRYSTAL_PERHOUR} / 3600, max_value: {TOPNAV_CRYSTAL_MAX}}});
  sn_timers.unshift({id: 'top_deuterium', type: 1, active: true, start_time: {TIME_NOW}, options: {start_value: {TOPNAV_DEUTERIUM}, per_second: {TOPNAV_DEUTERIUM_PERHOUR} / 3600, max_value: {TOPNAV_DEUTERIUM_MAX}}});

  sn_timers.unshift({'id': 'top_time', 'type': 2, 'active': true, 'start_time': {TIME_NOW}, options: 2});

  <!-- IF .flying_fleets -->
   sn_timers.unshift({'id': 'topnav_fleet_counter', 'type': 5, 'active': true, 'start_time': {TIME_NOW},
     'options': 
       {'msg_done': '0',
         'unchanged': false,
         'que':
           [
             <!-- BEGIN flying_fleets -->
               [{flying_fleets.TIME}, '{flying_fleets.TEXT}', '{flying_fleets.HINT}'],
             <!-- END flying_fleets -->
           ]
       }
  });
  <!-- ENDIF -->

  <!-- IF .flying_expeditions -->
  sn_timers.unshift({'id': 'topnav_expedition_counter', 'type': 5, 'active': true, 'start_time': {TIME_NOW},
    'options':
      {'msg_done': '0',
        'unchanged': false,
        'que':
          [
            <!-- BEGIN flying_expeditions -->
              [{flying_expeditions.TIME}, '{flying_expeditions.TEXT}', '{flying_expeditions.HINT}'],
            <!-- END flying_expeditions -->
          ]
      }
  });
  <!-- ENDIF -->
// --></script>
