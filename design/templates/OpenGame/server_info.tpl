<h1>{L_sys_universe} &quot;{C_game_name}&quot;</h1>

<table width="519" style="color:#FFFFFF"><tbody>
  <tr><td class="c" colspan="2">{L_adm_opt_game_settings}</td></tr>
  <tr>
    <th>{L_adm_opt_game_name}</th>
    <th>{C_game_name}</th>
  </tr>
  <tr>
    <th class="c">{L_adm_opt_game_mode}</th>
    <th class="c">
      <!-- IF C_game_mode == 1 -->
        {L_sys_game_mode[1]}
      <!-- ELSE -->
        {L_sys_game_mode[0]}
      <!-- ENDIF -->

    </th>
  </tr>
  <tr>
    <th>{L_adm_opt_speed} {L_adm_opt_game_gspeed}</th>
    <th>x&nbsp;{game_speed}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_speed} {L_adm_opt_game_fspeed}</th>
    <th>x&nbsp;{fleet_speed}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_speed} {L_adm_opt_game_pspeed}</th>
    <th>x&nbsp;{C_resource_multiplier}</th>
  </tr>
  <tr>
    <th class="c">{L_adm_opt_galaxies}</th>
    <th class="c">{C_game_maxGalaxy}</th>
  </tr>
  <tr>
    <th class="c">{L_adm_opt_systems}</th>
    <th class="c">{C_game_maxSystem}</th>
  </tr>
  <tr>
    <th class="c">{L_adm_opt_planets}</th>
    <th class="c">{C_game_maxPlanet}</th>
  </tr>

  <tr><td class="c" colspan="2">{L_adm_opt_plan_settings}</td></tr>
  <tr>
    <th>{L_adm_opt_plan_initial}</th>
    <th>{C_initial_fields} {L_adm_opt_sectors}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_plan_base_inc} {L_sys_metal}</th>
    <th>{C_metal_basic_income} {L_adm_opt_per_hour}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_plan_base_inc} {L_sys_crystal}</th>
    <th>{C_crystal_basic_income} {L_adm_opt_per_hour}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_plan_base_inc} {L_sys_deuterium}</th>
    <th>{C_deuterium_basic_income} {L_adm_opt_per_hour}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_plan_base_inc} {L_sys_energy}</th>
    <th>{C_energy_basic_income} {L_adm_opt_per_hour}</th>
  </tr>
  <tr>
    <th>{L_adm_opt_max_colonies}</th>
    <th>{C_player_max_colonies}</th>
  </tr>

  
  <tr><td class="c" colspan="2">{L_adm_opt_game_oth_info}</td></tr>
  <tr>
    <th>{L_adm_opt_exchange}</th>
    <th>{L_sys_metal}&nbsp;{C_rpg_exchange_metal}&nbsp;:&nbsp;{L_sys_crystal}&nbsp;{C_rpg_exchange_crystal}&nbsp;:&nbsp;{L_sys_deuterium}&nbsp;{C_rpg_exchange_deuterium}&nbsp;:&nbsp;{L_sys_dark_matter_sh}&nbsp;{C_rpg_exchange_darkMatter}</th>
  </tr>
  <tr> 
    <th>{L_adm_opt_vacancy_mode_is}</th> 
    <th>
      <!-- IF C_urlaubs_modus_erz -->
        {L_sys_off}
      <!-- ELSE -->
        {L_sys_on}
      <!-- ENDIF -->
    </th> 
  </tr>
  <tr>
    <th>{L_adm_opt_build_on_research}</th>
    <th>
      <!-- IF C_BuildLabWhileRun -->
        {L_sys_yes}
      <!-- ELSE -->
        {L_sys_no}
      <!-- ENDIF -->
    </th>
  </tr>

</tbody></table>
