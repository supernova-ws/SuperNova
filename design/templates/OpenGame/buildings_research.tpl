<h2>{PAGE_HEADER}</h2>

<!-- INCLUDE _result_message.tpl -->

<!-- DEFINE $QUE_ID = '{QUE_ID}' -->
<!-- INCLUDE eco_queue.tpl -->

<table width=530>
  <!-- IF $QUE_NOT_EMPTY -->
    <tr>
      <th colspan="2" class="c" align="center">
        <table width=100% class="noborder">
          <tr>
            <th width=120px>
              <div id="ov_{QUE_ID}"></div>
              <div id="ov_{QUE_ID}_timer" style="color: lime"></div>
              <div>{L_sys_total_time}</div>
              <div id="ov_{QUE_ID}_total" style="color: red"></div>
            </th>
            <th id="ov_{QUE_ID}_que"></th>
          </tr>
        </table>
      </th>
    </tr>

    <tr>
      <td colspan="2" class="c" align="center">
        <div class="fl"><a href="buildings.php?mode={QUE_ID}&action=clear">{L_eco_que_clear}</a></div>
        <div class="fr"><a href="buildings.php?mode={QUE_ID}&action=trim">{L_eco_que_trim}</a></div>
      </td>
    </tr>
  <!-- ELSE -->
    <tr>
      <th colspan="2" class="c_c" align="center">
        {L_eco_que_empty}
      </th>
    </tr>
  <!-- ENDIF -->
  <!-- BEGIN production -->
  <tr>
    <td class="c_c">
      <a href="infos.php?gid={production.ID}">
        <img border=0 src="{dpath}gebaeude/{production.ID}.gif" align="top" width=120 height=120>
      </a>
    </td>
    <td class="c_c">
      <div><a href="infos.php?gid={production.ID}">{production.NAME}<!-- IF production.LEVEL -->&nbsp;{production.LEVEL}<!-- ENDIF --></a></div>
      <div style="text-align: justify;">{production.DESCRIPTION}</div>
      <table align="center">
        <tr>
          <th class=c_c width=45>{L_sys_resources}</th>
          <th class=c_c width=80>{L_eco_price}</th>
          <th class=c_c width=80>{L_eco_left}</th>
          <!-- IF FLEET_OWN_COUNT -->
            <th class=c_c width=80>{L_eco_left_fleet}</th>
          <!-- ENDIF -->
        </tr>
        <!-- IF production.METAL -->
        <tr>
          <th class="c_l">{L_sys_metal}</th>
          <td class="c_r" width=80>{production.METAL_PRINT}</td>
          <td class="c_r" width=80>{production.METAL_REST}</td>
          <!-- IF FLEET_OWN_COUNT -->
            <td class="c_r" width=80>{production.METAL_FLEET}</td>
          <!-- ENDIF -->
        </tr>
        <!-- ENDIF -->
        <!-- IF production.CRYSTAL -->
        <tr>
          <th class="c_l">{L_sys_crystal}</th>
          <td class="c_r">{production.CRYSTAL_PRINT}</td>
          <td class="c_r">{production.CRYSTAL_REST}</td>
          <!-- IF FLEET_OWN_COUNT -->
            <td class="c_r" width=80>{production.CRYSTAL_FLEET}</td>
          <!-- ENDIF -->
        </tr>
        <!-- ENDIF -->
        <!-- IF production.DEUTERIUM -->
        <tr>
          <th class="c_l">{L_sys_deuterium}</th>
          <td class="c_r">{production.DEUTERIUM_PRINT}</td>
          <td class="c_r">{production.DEUTERIUM_REST}</td>
          <!-- IF FLEET_OWN_COUNT -->
            <td class="c_r" width=80>{production.DEUTERIUM_FLEET}</td>
          <!-- ENDIF -->
        </tr>
        <!-- ENDIF -->
        <!-- IF production.ENERGY -->
        <tr>
          <th class="c_l">{L_sys_energy}</th>
          <td class="c_r">{production.ENERGY_PRINT}</td>
          <td class="c_r">-</td>
          <!-- IF FLEET_OWN_COUNT -->
            <td class="c_r" width=80>-</td>
          <!-- ENDIF -->
        </tr>
        <!-- ENDIF -->
      </table>
      {L_ConstructionTime}:&nbsp;{production.TIME}
      <!-- IF ! RESEARCH_ONGOING && production.BUILD_CAN2 -->
        <a href="buildings.php?mode=research&action=build&tech={production.ID}"><span class="ok">{L_Rechercher} {L_level} {production.LEVEL_NEXT}</span></a>
      <!-- ELSE -->
        <span class="error">{L_Rechercher} {L_level} {production.LEVEL_NEXT}</span>
      <!-- ENDIF -->
    </td>
  </tr>
  <!-- BEGINELSE production -->
  <tr>

  </tr>
  <!-- END production -->
</table>
