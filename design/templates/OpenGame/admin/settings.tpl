<h1>{L_adm_opt_title}</h1>
<!-- IF MESSAGE -->
{MESSAGE}<br>
<!-- ENDIF -->
<form action="" method="post">
  <table style="color:#FFFFFF;" class="table"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_maintenance}</td></tr>
    <tr>
      <th colspan=2><input name="game_disable" type="checkbox" value="1" {game_disable} /> {L_adm_opt_game_online}<br>
        <input name="game_disable_reason" size="82" value="{C_game_disable_reason}" type="text">
        <!--
        <textarea name="game_disable_reason" cols="50" rows="2" size="50" >{C_game_disable_reason}</textarea>
        -->
      </th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_debugmod}</a></th>
      <th><input name="debug" {debug} type="checkbox" value="1" /></th>
    </tr>

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
<!--
    <tr>
      <th>{L_adm_opt_game_gspeed}</th>
      <th><input name="game_speed" size="10" maxlength="10" value="{C_game_speed}" type="text"> {L_adm_opt_game_speed_normal}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_fspeed}</th>
      <th><input name="fleet_speed" size="10" maxlength="10" value="{C_fleet_speed}" type="text"> {L_adm_opt_game_speed_normal}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_pspeed}</th>
      <th><input name="resource_multiplier" size="10" maxlength="10" value="{C_resource_multiplier}" type="text"> {L_adm_opt_game_speed_normal}</th>
    </tr>
-->    
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
<!--
        <div class="fl" style="width: 6em;">{L_adm_opt_galaxies}</div>
        <div class="fl" style="width: 4.5em;"><input name="game_maxGalaxy" maxlength="5" size="5" value="{C_game_maxGalaxy}" type="text"></div>
        <div class="fl" style="width: 6em;">{L_adm_opt_systems}</div>
        <div class="fl" style="width: 4.5em;"><input name="game_maxSystem" maxlength="5" size="5" value="{C_game_maxSystem}" type="text"></div>
        <div class="fl" style="width: 6em;">{L_adm_opt_planets}</div>
        <div class="fl"><input name="game_maxPlanet" maxlength="5" size="5" value="{C_game_maxPlanet}" type="text"></div>
-->
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
  
    <tr><td class="c" colspan="2">{L_adm_opt_links}</td></tr>
    <tr>
      <th>{L_adm_opt_game_rules}</a></th>
      <th><input name="url_rules" size="53" maxlength="254" value="{C_url_rules}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_forum}<br></th>
      <th><input name="url_forum" size="53" maxlength="254" value="{C_url_forum}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_dark_matter}<br></th>
      <th><input name="url_dark_matter" size="53" maxlength="254" value="{C_url_dark_matter}" type="text"></th>
    </tr>

    <tr><td class="c" colspan="2">{L_adm_opt_game_defaults}</td></tr>
    <tr>
      <th>{L_adm_opt_game_default_language}</a></th>
      <th><input name="game_default_language" size="40" maxlength="254" value="{C_game_default_language}" type="text"></th>
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

    <tr><td class="c" colspan="2">{L_adm_opt_game_advertise}</td></tr>
    <tr>
      <th colspan=2>
        <input name="advGoogleLeftMenuIsOn" {advGoogleLeftMenuIsOn} type="checkbox" value="1" /> {L_adm_opt_game_oth_adds}
        <textarea name="advGoogleLeftMenuCode" rows="10">{C_advGoogleLeftMenuCode}</textarea>
      </th>
    </tr>

    <tr><td class="c" colspan="2">{L_adm_opt_game_oth_info}</td></tr>
    <tr> 
      <th>{L_adm_opt_vacancy_mode}</th> 
      <th><input name="urlaubs_modus_erz" type="checkbox" value="1" {urlaubs_modus_erz} /></th> 
    </tr>

    <tr><td class="c" colspan="2"><center><input name="save" value="{L_adm_opt_btn_save}" type="submit"></center></td></tr>
  </tbody></table>
</form>