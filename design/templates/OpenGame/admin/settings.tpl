<h1>{L_adm_opt_title}</h1>
<!-- IF MESSAGE -->
{MESSAGE}<br>
<!-- ENDIF -->


<script>
jQuery(function() {
		jQuery( "#admin_settings" ).accordion({
			autoHeight: false,
			navigation: true,
      animated: false
		});
	});
	</script>

<form action="" method="post">
  <div id="admin_settings">
    <h3><a href="#">{L_adm_opt_maintenance}</a></h3>
    <div><p>
      <input name="game_disable" id="game_disable" type="checkbox" value="1"<!-- IF GAME_DISABLE --> checked<!-- ENDIF -->/> <label for="game_disable">{L_adm_opt_game_online}</label><br />
      <input name="game_disable_reason" size="82" value="{C_game_disable_reason}" type="text">
    </p></div>

    <h3><a href="#">{L_adm_opt_game_settings}</a></h3>
    <div>
    <table>
    <tr>
      <th>{L_adm_opt_game_mode}</a></th>
      <th>
        <select name="game_mode">
        <!-- BEGIN game_modes -->
          <!-- IF game_modes.ID == game_mode -->
            <!-- DEFINE $GAME_MODE = 'selected' -->
          <!-- ELSE -->
            <!-- DEFINE $GAME_MODE = '' -->
          <!-- ENDIF -->
          <option value="{game_modes.ID}" {$GAME_MODE}>{game_modes.NAME}
        <!-- END game_mode -->
        </select>
      </th>
    </tr>
    <tr>
      <th colspan="2">
        <div style="width: 670px">
        <input name="empire_mercenary_temporary" id="empire_mercenary_temporary" type="checkbox" value="1"<!-- IF EMPIRE_MERCENARY_TEMPORARY --> checked<!-- ENDIF -->/> <label for="empire_mercenary_temporary">{L_adm_opt_empire_mercenary_temporary}</label><br />
        {L_adm_opt_empire_mercenary_temporary_base} <input type="text" name="empire_mercenary_base_period" value="{C_empire_mercenary_base_period}"><br />
        {L_adm_opt_empire_mercenary_temporary_hint}</div></th>
    </tr>

    <tr>
      <th>{L_adm_opt_speed} {L_adm_opt_game_speed_normal}</th>
      <th>
        {L_adm_opt_game_gspeed}&nbsp;&nbsp;&nbsp;<input name="game_speed" size="4" maxlength="9" value="{C_game_speed}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_game_fspeed}&nbsp;&nbsp;&nbsp;<input name="fleet_speed" size="4" maxlength="9" value="{C_fleet_speed}" type="text">&nbsp;&nbsp;&nbsp;
        {L_adm_opt_game_pspeed}&nbsp;&nbsp;&nbsp;<input name="resource_multiplier" size="4" maxlength="9" value="{C_resource_multiplier}" type="text">
      </th>
    </tr>

    <tr>
      <th>{L_adm_opt_universe_size}</th>
      <th>
        {L_adm_opt_galaxies}&nbsp;&nbsp;&nbsp;<input name="game_maxGalaxy" maxlength="5" size="5" value="{C_game_maxGalaxy}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_systems}&nbsp;&nbsp;&nbsp;<input name="game_maxSystem" maxlength="5" size="5" value="{C_game_maxSystem}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_planets}&nbsp;&nbsp;&nbsp;<input name="game_maxPlanet" maxlength="5" size="5" value="{C_game_maxPlanet}" type="text">
      </th>
    </tr>
    <tr>
      <th>{L_adm_opt_max_colonies}</a></th>
      <th><input name="player_max_colonies" maxlength="80" size="10" value="{C_player_max_colonies}" type="text"> {L_adm_opt_main_not_counted}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_exchange}</a></th>
      <th>
        {L_sys_metal} <input name="rpg_exchange_metal" maxlength="10" size="2" value="{C_rpg_exchange_metal}" type="text"> :
        {L_sys_crystal} <input name="rpg_exchange_crystal" maxlength="10" size="2" value="{C_rpg_exchange_crystal}" type="text"> :
        {L_sys_deuterium} <input name="rpg_exchange_deuterium" maxlength="10" size="2" value="{C_rpg_exchange_deuterium}" type="text"> :
        {L_sys_dark_matter_sh} <input name="rpg_exchange_darkMatter" maxlength="10" size="8" value="{C_rpg_exchange_darkMatter}" type="text">
      </th>
    </tr>
    </table>
    </div>



    <a href="#">Section 1</a>
    <div>
    <p><a href="test">test</a>
    Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
    ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
    amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
    odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
    </p>
    </div>
  </div>

  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_maintenance}</td></tr>
    <tr>
      <th colspan=2><input name="game_disable" id="game_disable" type="checkbox" value="1"<!-- IF GAME_DISABLE --> checked<!-- ENDIF -->/> <label for="game_disable">{L_adm_opt_game_online}</label><br />
        <input name="game_disable_reason" size="82" value="{C_game_disable_reason}" type="text">
      </th>
    </tr>
  </table>
  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_game_settings}</td></tr>
    <tr>
      <th>{L_adm_opt_game_name}</th>
      <th><input name="game_name" size="40" value="{C_game_name}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_mode}</a></th>
      <th>
        <select name="game_mode">
        <!-- BEGIN game_modes -->
          <!-- IF game_modes.ID == game_mode -->
            <!-- DEFINE $GAME_MODE = 'selected' -->
          <!-- ELSE -->
            <!-- DEFINE $GAME_MODE = '' -->
          <!-- ENDIF -->
          <option value="{game_modes.ID}" {$GAME_MODE}>{game_modes.NAME}
        <!-- END game_mode -->
        </select>
      </th>
    </tr>
    <tr>
      <th colspan="2">
        <div style="width: 670px">
        <input name="empire_mercenary_temporary" id="empire_mercenary_temporary" type="checkbox" value="1"<!-- IF EMPIRE_MERCENARY_TEMPORARY --> checked<!-- ENDIF -->/> <label for="empire_mercenary_temporary">{L_adm_opt_empire_mercenary_temporary}</label><br />
        {L_adm_opt_empire_mercenary_temporary_base} <input type="text" name="empire_mercenary_base_period" value="{C_empire_mercenary_base_period}"><br />
        {L_adm_opt_empire_mercenary_temporary_hint}</div></th>
    </tr>

    <tr>
      <th>{L_adm_opt_speed} {L_adm_opt_game_speed_normal}</th>
      <th>
        {L_adm_opt_game_gspeed}&nbsp;&nbsp;&nbsp;<input name="game_speed" size="4" maxlength="9" value="{C_game_speed}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_game_fspeed}&nbsp;&nbsp;&nbsp;<input name="fleet_speed" size="4" maxlength="9" value="{C_fleet_speed}" type="text">&nbsp;&nbsp;&nbsp;
        {L_adm_opt_game_pspeed}&nbsp;&nbsp;&nbsp;<input name="resource_multiplier" size="4" maxlength="9" value="{C_resource_multiplier}" type="text">
      </th>
    </tr>

    <tr>
      <th>{L_adm_opt_universe_size}</th>
      <th>
        {L_adm_opt_galaxies}&nbsp;&nbsp;&nbsp;<input name="game_maxGalaxy" maxlength="5" size="5" value="{C_game_maxGalaxy}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_systems}&nbsp;&nbsp;&nbsp;<input name="game_maxSystem" maxlength="5" size="5" value="{C_game_maxSystem}" type="text">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {L_adm_opt_planets}&nbsp;&nbsp;&nbsp;<input name="game_maxPlanet" maxlength="5" size="5" value="{C_game_maxPlanet}" type="text">
      </th>
    </tr>
    <tr>
      <th>{L_adm_opt_max_colonies}</a></th>
      <th><input name="player_max_colonies" maxlength="80" size="10" value="{C_player_max_colonies}" type="text"> {L_adm_opt_main_not_counted}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_exchange}</a></th>
      <th>
        {L_sys_metal} <input name="rpg_exchange_metal" maxlength="10" size="2" value="{C_rpg_exchange_metal}" type="text"> :
        {L_sys_crystal} <input name="rpg_exchange_crystal" maxlength="10" size="2" value="{C_rpg_exchange_crystal}" type="text"> :
        {L_sys_deuterium} <input name="rpg_exchange_deuterium" maxlength="10" size="2" value="{C_rpg_exchange_deuterium}" type="text"> :
        {L_sys_dark_matter_sh} <input name="rpg_exchange_darkMatter" maxlength="10" size="8" value="{C_rpg_exchange_darkMatter}" type="text">
      </th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_plan_settings}</td></tr>
    <tr>
      <th>{L_adm_opt_plan_initial}</th>
      <th><input name="initial_fields" maxlength="80" size="10" value="{C_initial_fields}" type="text"> {L_adm_opt_sectors}</th>
    </tr>
    <tr>
      <th>{L_res_basic_income}</th>
      <th>
        <div class="fl" style="width: 5em;">{L_sys_metal}</div>
        <div class="fl" style="width: 10.5em;"><input name="metal_basic_income" maxlength="10" size="9" value="{C_metal_basic_income}" type="text"> {L_adm_opt_per_hour}</div>
        <div class="fl" style="width: 5.5em;">{L_sys_crystal}</div>
        <div><input name="crystal_basic_income" maxlength="10" size="9" value="{C_crystal_basic_income}" type="text"> {L_adm_opt_per_hour}</div>
        <div class="fl" style="width: 5em;">{L_sys_deuterium}</div>
        <div class="fl" style="width: 10.5em;"><input name="deuterium_basic_income" maxlength="10" size="9" value="{C_deuterium_basic_income}" type="text"> {L_adm_opt_per_hour}</div>
        <div class="fl" style="width: 5.5em;">{L_sys_energy}</div>
        <div><input name="energy_basic_income" maxlength="10" size="9" value="{C_energy_basic_income}" type="text"> {L_adm_opt_per_hour}</div>
      </th>
    </tr>
    <tr>
      <th>{L_adm_uni_price_galaxy}</a></th>
      <th><input name="uni_price_galaxy" maxlength="20" size="20" value="{C_uni_price_galaxy}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_uni_price_system}</a></th>
      <th><input name="uni_price_system" maxlength="20" size="20" value="{C_uni_price_system}" type="text"></th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_chat}</td></tr>
    <tr>
      <th>{L_adm_opt_chat_timeout}</th>
      <th><input name="chat_timeout" maxlength="80" size="10" value="{C_chat_timeout}" type="text"> {L_sys_sec} {L_adm_opt_game_zero_dsiable}</th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_links}</td></tr>
    <tr>
      <th>{L_adm_opt_game_rules}</a></th>
      <th><input name="url_rules" size="53" maxlength="254" value="{C_url_rules}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_faq}<br></th>
      <th><input name="url_faq" size="53" maxlength="254" value="{C_url_faq}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_forum}<br></th>
      <th><input name="url_forum" size="53" maxlength="254" value="{C_url_forum}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_dark_matter}<br></th>
      <th><input name="url_dark_matter" size="53" maxlength="254" value="{C_url_dark_matter}" type="text"></th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_game_defaults}</td></tr>
    <tr>
      <th>{L_adm_opt_game_default_language}</a></th>
      <th>
        <select name="game_default_language">
        <!-- BEGIN game_languages -->
          <!-- IF game_languages.ID == game_language -->
            <!-- DEFINE $GAME_LANGUAGE = 'selected' -->
          <!-- ELSE -->
            <!-- DEFINE $GAME_LANGUAGE = '' -->
          <!-- ENDIF -->
          <option value="{game_languages.ID}" {$GAME_LANGUAGE}>{game_languages.NAME}
        <!-- END game_languages -->
        </select>
      </th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_default_skin}</a></th>
      <th><input name="game_default_skin" size="40" maxlength="254" value="{C_game_default_skin}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_default_template}</a></th>
      <th><input name="game_default_template" size="40" maxlength="254" value="{C_game_default_template}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_int_news_count}<br>{L_adm_opt_game_zero_dsiable}</th>
      <th>{L_adm_opt_int_page_imperor} <input name="game_news_overview" type="text" size=3 maxlength=3 value="{C_game_news_overview}" /></th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_sys_opt_bash_info}</td></tr>
    <tr>
      <th>{L_sys_opt_bash_attacks}</th>
      <th><input name="fleet_bashing_attacks" maxlength="10" size="10" value="{C_fleet_bashing_attacks}" type="text"> {L_adm_opt_game_zero_dsiable}</th>
    </tr>
    <tr>
      <th>{L_sys_opt_bash_waves}</th>
      <th><input name="fleet_bashing_waves" maxlength="10" size="10" value="{C_fleet_bashing_waves}" type="text"></th>
    </tr>
    <tr>
      <th>{L_sys_opt_bash_interval}</th>
      <th><input name="fleet_bashing_interval" maxlength="10" size="10" value="{C_fleet_bashing_interval}" type="text"> {L_sys_sec}</th>
    </tr>
    <tr>
      <th>{L_sys_opt_bash_scope}</th>
      <th><input name="fleet_bashing_scope" maxlength="10" size="10" value="{C_fleet_bashing_scope}" type="text"> {L_sys_sec}</th>
    </tr>
    <tr>
      <th>{L_sys_opt_bash_war_delay}</th>
      <th><input name="fleet_bashing_war_delay" maxlength="10" size="10" value="{C_fleet_bashing_war_delay}" type="text"> {L_sys_sec}</th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_game_advertise}</td></tr>
    <tr>
      <th colspan=2>
        <input name="advGoogleLeftMenuIsOn" id="advGoogleLeftMenuIsOn" type="checkbox" value="1"<!-- IF ADV_LEFT_MENU --> checked<!-- ENDIF -->/> <label for="advGoogleLeftMenuIsOn">{L_adm_opt_game_oth_adds}</label>
        <textarea name="advGoogleLeftMenuCode" rows="10">{C_advGoogleLeftMenuCode}</textarea>
      </th>
    </tr>
  </table>





  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_game_oth_info}</td></tr>
    <tr>
      <th><label for="user_vacation_disable">{L_adm_opt_vacation_mode}</label></th>
      <th><input name="user_vacation_disable" id="user_vacation_disable" type="checkbox" value="1"<!-- IF USER_VACATION_DISABLE --> checked<!-- ENDIF -->/></th> 
    </tr>
    <tr>
      <th><label for="eco_scale_storage">{L_adm_opt_eco_scale_storage}</label></th>
      <th><input name="eco_scale_storage" id="eco_scale_storage" type="checkbox" value="1"<!-- IF ECO_SCALE_STORAGE --> checked<!-- ENDIF -->/></th> 
    </tr>
    <tr>
      <th><label for="allow_buffing">{L_adm_opt_allow_buffing}</label></th>
      <th><input name="allow_buffing" id="allow_buffing" type="checkbox" value="1" <!-- IF ALLOW_BUFFING -->checked<!-- ENDIF --> /></th> 
    </tr>
    <tr>
      <th><label for="ally_help_weak">{L_adm_opt_ally_help_weak}</label></th>
      <th><input name="ally_help_weak" id="ally_help_weak" type="checkbox" value="1" <!-- IF ALLY_HELP_WEAK -->checked<!-- ENDIF --> /></th> 
    </tr>
    <tr>
      <th><label for="game_email_pm">{L_adm_opt_email_pm}</label></th>
      <th><input name="game_email_pm" id="game_email_pm" type="checkbox" value="1" <!-- IF GAME_EMAIL_PM -->checked<!-- ENDIF --> /></th> 
    </tr>
    <tr>
      <th><label for="debug">{L_adm_opt_game_debugmod}</label></th>
      <th><input name="debug" id="debug" type="checkbox" value="1"<!-- IF GAME_DEBUG --> checked<!-- ENDIF --> /></th>
    </tr>
    <tr>
      <th><label for="game_counter">{L_adm_opt_game_counter}</label></th>
      <th><input name="game_counter" id="game_counter" type="checkbox" value="1"<!-- IF GAME_COUNTER --> checked<!-- ENDIF --> /></th>
    </tr>

    <tr><td class="c" colspan="2"><center><input name="save" value="{L_adm_opt_btn_save}" type="submit"></center></td></tr>
  </tbody></table>
</form>