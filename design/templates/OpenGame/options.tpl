<form action="options.php?mode=change" method="post" name="fOptions">
  <table width="519">
    <tbody>
      <!-- IF SAVED -->
        <tr><td class="c positive" colspan=2 align=center>{L_opt_saved}</td></tr>
      <!-- ENDIF -->

      <!-- IF IS_ADMIN -->
        <tr><td class="c" colspan="2">{L_opt_adm_title}</td></tr>
        <tr>
          <th><label for="adm_pl_prot">{L_opt_adm_planet_prot}</label></th>
          <th><input name="adm_pl_prot"{adm_pl_prot_data} type="checkbox" id="adm_pl_prot" /></th>
        </tr>
      <!-- ENDIF -->
      
      <tr>
        <td class="c" colspan="2">{L_userdata}</td>
      </tr>
      <tr>
        <th>{L_username}</th>
        <th width=330>{opt_usern_data}</th>
      </tr>
      <tr>
        <th>{L_lastpassword}</th>
        <th><input name="db_password" size="20" value="" type="password"></th>
      </tr>
      <tr>
        <th>{L_newpassword}</th>
        <th><input name="newpass1" size="20" maxlength="40" type="password"></th>
      </tr>
      <tr>
        <th>{L_newpasswordagain}</th>
        <th><input name="newpass2" size="20" maxlength="40" type="password"></th>
      </tr>
      <tr title="{L_emaildir_tip}">
        <th>{L_emaildir}</th>
        <th><input name="db_email" maxlength="100" size="20" value="{opt_mail1_data}" type="text"></th>
      </tr>
      <tr>
        <th>{L_permanentemaildir}</th>
        <th>{opt_mail2_data}</th>
      </tr>
<!--
      <tr> 
        <th>{L_opt_language}</th>
        <th>
          <select name="langer">
            {opt_lst_lang_data}
          </select>
        </th>
      </tr>
-->

      <tr>
        <td class="c" colspan="2">{L_general_settings}</td>
      </tr>
      
      <tr>
        <th>{L_opt_lst_ord}</th>
        <th>
          <select name="settings_order">
            {opt_lst_cla_data}
          </select>
          <select name="settings_sort">
            {opt_lst_ord_data}
          </select>
        </th>
      </tr>
      <tr>
        <th>{L_skins_example}<!--<br> <a href="http://80.237.203.201/download/" target="_blank">{Download}</a>--></th>
        <th><input name="design"{opt_sskin_data} type="checkbox" id="design" /> <label for="design">{L_opt_chk_skin}</label><br>
          <input name="dpath" maxlength="80" size="40" value="{opt_dpath_data}" type="text">&nbsp;<select name="dpaths" size="1" onchange="document.fOptions.dpath.value='skins/'+this.value+'/';">
            {opt_lst_skin_data}
          </select>
        </th>
      </tr>
      <tr>
        <th>{L_avatar_example}<br> </th>
        <th><input name="avatar" maxlength="80" size="40" value="{opt_avata_data}" type="text">&nbsp;<a href="http://www.google.com.ar/imghp" target="_blank">{Search}</a></th>
      </tr>
      
<!--
      <tr title="{L_untoggleip_tip}">
        <th><label for="noipcheck">{L_untoggleip}</label></th>
        <th><input name="noipcheck"{opt_noipc_data} type="checkbox" id="noipcheck" /></th>
      </tr>
-->
      
      <tr>
        <td class="c" colspan="2">{L_galaxyvision_options}</td>
      </tr>
      <tr title="{L_spy_cant_tip}">
        <th>{L_spy_cant}</th>
        <th><input name="spio_anz" maxlength="2" size="2" value="{opt_probe_data}" type="text"></th>
      </tr>
      <tr>
        <th>{L_tooltip_time}</th>
        <th><input name="settings_tooltiptime" maxlength="2" size="2" value="{opt_toolt_data}" type="text"> {seconds}</th>
      </tr>
      <tr>
      <th>{L_mess_ammount_max}</th>
      <th><input name="settings_fleetactions" maxlength="2" size="2" value="{opt_fleet_data}" type="text"></th>
      </tr><tr>
      <th><label for="settings_allylogo">{L_show_ally_logo}</label></th>
      <th><input name="settings_allylogo"{opt_allyl_data} type="checkbox" id="settings_allylogo" /></th>
      </tr><tr>
      <th>{L_shortcut}</th>
      <th>
        <input name="settings_esp"{user_settings_esp} type="checkbox" id="settings_esp" />&nbsp;<label for="settings_esp"><img src="{dpath}img/e.gif" alt="{spy}" />&nbsp;{L_spy}</label>
        <input name="settings_mis"{user_settings_mis} type="checkbox" id="settings_mis" />&nbsp;<label for="settings_mis"><img src="{dpath}img/r.gif" alt="{attack_with_missile}" />&nbsp;{L_attack_with_missile}</label><br />
        <input name="settings_wri"{user_settings_wri} type="checkbox" id="settings_wri" />&nbsp;<label for="settings_wri"><img src="{dpath}img/m.gif" alt="{write_a_messege}" />&nbsp;{L_write_a_messege}</label>
        <input name="settings_bud"{user_settings_bud} type="checkbox" id="settings_bud" />&nbsp;<label for="settings_bud"><img src="{dpath}img/b.gif" alt="{add_to_buddylist}">&nbsp;{L_add_to_buddylist}</label>
<!--      <img src="{dpath}img/s.gif" alt="{show_report}"> {L_show_report} <input name="settings_rep"{user_settings_rep} type="checkbox" /> -->
      </th>
      </tr>
      
      <tr>
      <td class="c" colspan="2">{L_delete_vacations}</td>
      </tr>
<!-- IF ! USER_VACATION_DISABLE -->
      <tr title="{L_vacations_tip}">
      <th colspan=2><span class="fl"><input name="vacation"{opt_modev_data} type="checkbox" id="vacation" /> <label for="vacation">{L_mode_vacations} {L_opt_vacation_min} <span id="vacancy_to"></span></label></span></th>
      </tr>
<!-- ENDIF -->
      <tr>
      <th colspan=2><span class="fl"><input name="db_deaktjava"{opt_delac_data} type="checkbox" id="db_deaktjava" /> <label for="db_deaktjava">{L_deleteaccount}. {L_deleteaccount_tip}</span></label></th>
      </th>
      </tr>
      <tr>
      <td class="c" align="center" colspan="2"><input value="{L_save_settings}" type="submit"></td>
      </tr>
    </tbody>
  </table>
</form>
<script type="text/javascript"><!--
  sn_timers.unshift({id: 'vacancy_to', type: 4, active: true, start_time: '{TIME_NOW}', options: {format: 3, delta: '{VACATION_TIME}'}});
// --></script>