<br />
<!-- IF MESSAGE --><span class="error">{MESSAGE}</span><!-- ENDIF -->
<table width=530>
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
      <table width="100%" border=0>
        <tr>
          <td valign="middle" style="border: 0">
            {L_ConstructionTime}:&nbsp;{production.TIME}
            <table align="center">
              <tr>
                <th class=c_c width=45>{L_sys_resources}</th>
                <th class=c_c width=65>{L_eco_price}</th>
                <th class=c_c width=65>{L_eco_left}</th>
              </tr>
              <!-- IF production.METAL -->
              <tr>
                <th class="c_l">{L_sys_metal}</th>
                <td class="c_r" width=65>{production.METAL_PRINT}</td>
                <td class="c_r" width=65>{production.METAL_REST}</td>
              </tr>
              <!-- ENDIF -->
              <!-- IF production.CRYSTAL -->
              <tr>
                <th class="c_l">{L_sys_crystal}</th>
                <td class="c_r">{production.CRYSTAL_PRINT}</td>
                <td class="c_r">{production.CRYSTAL_REST}</td>
              </tr>
              <!-- ENDIF -->
              <!-- IF production.DEUTERIUM -->
              <tr>
                <th class="c_l">{L_sys_deuterium}</th>
                <td class="c_r">{production.DEUTERIUM_PRINT}</td>
                <td class="c_r">{production.DEUTERIUM_REST}</td>
              </tr>
              <!-- ENDIF -->
              <!-- IF production.ENERGY -->
              <tr>
                <th class="c_l">{L_sys_energy}</th>
                <td class="c_r">{production.ENERGY_PRINT}</td>
                <td class="c_r">-</td>
              </tr>
              <!-- ENDIF -->
            </table>
          </td>
          <td valign="middle" style="border: 0">
            <!-- IF RESEARCH_ONGOING -->
              <!-- IF RESEARCH_TECH == production.ID -->
                <span id="research_timer_timer">2</span><br />
                <a href=buildings.php?cp={RESEARCH_HOME_ID}&mode=research&cmd=cancel&tech={production.ID}  id="research_timer"></a>
                <script type="text/javascript"><!--
                  sn_timers.unshift({id: 'research_timer', type: 0, active: true, start_time: {TIME_NOW}, options: { msg_done: '<a href=buildings.php?mode=research&cp={RESEARCH_HOME_ID}>{L_continue}</a>', que: [
                    ['0', '{L_cancel}<!-- IF RESEARCH_HOME_NAME --><br>{RESEARCH_HOME_NAME}<!-- ENDIF -->', {RESEARCH_TIME}, '0']
                  ]}});
                --></script>
              <!-- ELSE -->
               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <!-- ENDIF -->
            <!-- ELSE -->
              <!-- IF production.BUILD_CAN2 -->
                <a href="buildings.php?mode=research&cmd=search&tech={production.ID}"><span class="ok">{L_Rechercher}<br />{L_level} {production.LEVEL_NEXT}</span></a>
              <!-- ELSE -->
                <span class="error">{L_Rechercher}<br />{L_level} {production.LEVEL_NEXT}</span>
              <!-- ENDIF -->
            <!-- ENDIF -->
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- BEGINELSE production -->
  <tr>

  </tr>
  <!-- END production -->
</table>
