<font color="red">{vocation_time_out}</font>
<br>
<form action="{PHP_SELF}?mode=change" method="post" name="fOptions">
  <table width="519">
    <tbody>
      {opt_adm_frame}
      
      <tr>
        <td class="c" colspan="2">{userdata}</td>
      </tr>
      <tr>
        <th>{username}</th>
        <th>{opt_usern_data}</th>
      </tr>
      <tr>
        <th>{lastpassword}</th>
        <th><input name="db_password" size="20" value="" type="password"></th>
      </tr>
      <tr>
        <th>{newpassword}</th>
        <th><input name="newpass1" size="20" maxlength="40" type="password"></th>
      </tr>
      <tr>
        <th>{newpasswordagain}</th>
        <th><input name="newpass2" size="20" maxlength="40" type="password"></th>
      </tr>
      <tr>
        <th><a title="{emaildir_tip}">{emaildir}</a></th>
        <th><input name="db_email" maxlength="100" size="20" value="{opt_mail1_data}" type="text"></th>
      </tr>
      <tr>
        <th>{permanentemaildir}</th>
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
        <td class="c" colspan="2">{general_settings}</td>
      </tr>
      
      <tr>
        <th>{opt_lst_ord}</th>
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
        <th>{skins_example}<!--<br> <a href="http://80.237.203.201/download/" target="_blank">{Download}</a>--></th>
        <th>{opt_chk_skin} <input name="design"{opt_sskin_data} type="checkbox"><br>
          <input name="dpath" maxlength="80" size="40" value="{opt_dpath_data}" type="text">&nbsp;<select name="dpaths" size="1" onchange="document.fOptions.dpath.value='skins/'+this.value+'/';">
            {opt_lst_skin_data}
          </select>
        </th>
      </tr>
      <tr>
        <th>{avatar_example}<br> </th>
        <th><input name="avatar" maxlength="80" size="40" value="{opt_avata_data}" type="text">&nbsp;<a href="http://www.google.com.ar/imghp" target="_blank">{Search}</a></th>
      </tr>
      <tr>
        <th><a title="{untoggleip_tip}">{untoggleip}</a></th>
        <th><input name="noipcheck"{opt_noipc_data} type="checkbox" /></th>
      </tr>
      
      <tr>
        <td class="c" colspan="2">{galaxyvision_options}</td>
      </tr>
      <tr>
        <th><a title="{spy_cant_tip}">{spy_cant}</a></th>
        <th><input name="spio_anz" maxlength="2" size="2" value="{opt_probe_data}" type="text"></th>
      </tr>
      <tr>
        <th>{tooltip_time}</th>
        <th><input name="settings_tooltiptime" maxlength="2" size="2" value="{opt_toolt_data}" type="text"> {seconds}</th>
      </tr>
      <tr>
      <th>{mess_ammount_max}</th>
      <th><input name="settings_fleetactions" maxlength="2" size="2" value="{opt_fleet_data}" type="text"></th>
      </tr><tr>
      <th>{show_ally_logo}</th>
      <th><input name="settings_allylogo"{opt_allyl_data} type="checkbox" /></th>
      </tr><tr>
      <th>{shortcut}</th>
      <th><input name="settings_esp"{user_settings_esp} type="checkbox" /> <img src="{dpath}img/e.gif" alt="{spy}"> {spy}
      <input name="settings_mis"{user_settings_mis} type="checkbox" /> <img src="{dpath}img/r.gif" alt="{attack_with_missile}"> {attack_with_missile} <br>
      <input name="settings_wri"{user_settings_wri} type="checkbox" /> <img src="{dpath}img/m.gif" alt="{write_a_messege}"> {write_a_messege} 
      <input name="settings_bud"{user_settings_bud} type="checkbox" /> <img src="{dpath}img/b.gif" alt="{add_to_buddylist}"> {add_to_buddylist}
<!--      <img src="{dpath}img/s.gif" alt="{show_report}"> {show_report} <input name="settings_rep"{user_settings_rep} type="checkbox" /> -->
      </th>
      </tr>
      
      <tr>
      <td class="c" colspan="2">{delete_vacations}</td>
      </tr>
      <tr>
      <th><a title="{vacations_tip}">{mode_vacations}</a></th>
      <th><input name="urlaubs_modus"{opt_modev_data} type="checkbox" /></th>
      </tr><tr>
      <th><a title="{deleteaccount_tip}">{deleteaccount}</a></th>
      <th><input name="db_deaktjava"{opt_delac_data} type="checkbox" /></th>
      </tr>
      
      <tr>
      <th colspan="2"><input value="{save_settings}" type="submit"></th>
      </tr>
    </tbody>
  </table>
</form>
