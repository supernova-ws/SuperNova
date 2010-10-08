<h2>{L_adm_opt_title}</h2>
<!-- IF MESSAGE -->
{MESSAGE}<br>
<!-- ENDIF -->
<form action="" method="post">
  <table width="519" style="color:#FFFFFF"><tbody>
    <tr><td class="c" colspan="2">{L_adm_opt_game_settings}</td></tr>
    <tr>
      <th>{L_adm_opt_game_name}</th>
      <th><input name="game_name" size="20" value="{C_game_name}" type="text"></th>
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
      <th>{L_adm_opt_game_gspeed} {L_adm_opt_game_speed_normal}</th>
      <th><input name="game_speed" size="20" value="{C_game_speed}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_fspeed} {L_adm_opt_game_speed_normal}</th>
      <th><input name="fleet_speed" size="20" value="{C_fleet_speed}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_pspeed} {L_adm_opt_game_pspeed_norm}</th>
      <th><input name="resource_multiplier" maxlength="80" size="10" value="{C_resource_multiplier}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_galaxies}</a></th>
      <th><input name="game_maxGalaxy" maxlength="80" size="10" value="{C_game_maxGalaxy}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_systems}</a>
      <th><input name="game_maxSystem" maxlength="80" size="10" value="{C_game_maxSystem}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_planets}</a></th>
      <th><input name="game_maxPlanet" maxlength="80" size="10" value="{C_game_maxPlanet}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_max_colonies}</a></th>
      <th><input name="player_max_colonies" maxlength="80" size="10" value="{C_player_max_colonies}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_exchange}</a></th>
      <th>
        {L_sys_metal} <input name="rpg_exchange_metal" maxlength="5" size="2" value="{C_rpg_exchange_metal}" type="text"> :
        {L_sys_crystal} <input name="rpg_exchange_crystal" maxlength="5" size="2" value="{C_rpg_exchange_crystal}" type="text"> :
        {L_sys_deuterium} <input name="rpg_exchange_deuterium" maxlength="5" size="2" value="{C_rpg_exchange_deuterium}" type="text"> :
        {L_sys_dark_matter_sh} <input name="rpg_exchange_darkMatter" maxlength="10" size="8" value="{C_rpg_exchange_darkMatter}" type="text">
      </th>
    </tr>


    <tr><td class="c" colspan="2">{L_adm_opt_plan_settings}</td></tr>
    <tr>
      <th>{L_adm_opt_plan_initial}</th>
      <th><input name="initial_fields" maxlength="80" size="10" value="{C_initial_fields}" type="text"> {L_adm_opt_sectors}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_plan_base_inc}{L_sys_metal}</th>
      <th><input name="metal_basic_income" maxlength="80" size="10" value="{C_metal_basic_income}" type="text"> {L_adm_opt_per_hour}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_plan_base_inc}{L_sys_crystal}</th>
      <th><input name="crystal_basic_income" maxlength="80" size="10" value="{C_crystal_basic_income}" type="text"> {L_adm_opt_per_hour}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_plan_base_inc}{L_sys_deuterium}</th>
      <th><input name="deuterium_basic_income" maxlength="80" size="10" value="{C_deuterium_basic_income}" type="text"> {L_adm_opt_per_hour}</th>
    </tr>
    <tr>
      <th>{L_adm_opt_plan_base_inc}{L_sys_energy}</th>
      <th><input name="energy_basic_income" maxlength="80" size="10" value="{C_energy_basic_income}" type="text"> {L_adm_opt_per_hour}</th>
    </tr>
  
    <tr><td class="c" colspan="2">{L_adm_opt_links}</td></tr>
    <tr>
      <th>{L_adm_opt_game_forum}<br></th>
      <th><input name="forum_url" size="40" maxlength="254" value="{C_forum_url}" type="text"></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_rules}</a></th>
      <th><input name="rules_url" size="40" maxlength="254" value="{C_rules_url}" type="text"></th>
    </tr>

    <tr><td class="c" colspan="2">{L_adm_opt_maintenance}</td></tr>
    <tr>
      <th>{L_adm_opt_game_online}<br></th>
      <th><input name="game_disable" type="checkbox" value="1" {game_disable} /></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_offreaso}<br></th>
      <th><textarea name="game_disable_reason" cols="80" rows="5" size="80" >{C_game_disable_reason}</textarea></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_debugmod}</a></th>
      <th><input name="debug" {debug} type="checkbox" value="1" /></th>
    </tr>

    <tr><td class="c" colspan="2">{L_adm_opt_game_oth_info}</td></tr>
    <tr>
      <th>{L_adm_opt_game_oth_news}<br></th>
      <th><input name="game_news_overview" type="text" value="{C_game_news_overview}" /></th>
    </tr>
    <tr>
      <th>{L_adm_opt_game_oth_adds}</th>
      <th><input name="advGoogleLeftMenuIsOn" {advGoogleLeftMenuIsOn} type="checkbox" value="1" /></th>
    </tr>
    <tr>
      <th colspan="2"><textarea name="advGoogleLeftMenuCode" cols="80" rows="5" size="80" >{C_advGoogleLeftMenuCode}</textarea></th>
    </tr>
    <tr> 
      <th>{L_adm_opt_vacancy_mode}</th> 
      <th><input name="urlaubs_modus_erz" type="checkbox" value="1" {urlaubs_modus_erz} /></th> 
    </tr>

    <tr><th colspan="2"><input name="save" value="{L_adm_opt_btn_save}" type="submit"></th></tr>
  </tbody></table>
</form>