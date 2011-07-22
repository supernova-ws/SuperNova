<center>
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
      <a href="infos.php?gid={production.ID}">{production.NAME}</a>
      {production.LEVEL}<br>
      {production.DESCRIPTION}<br>
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
            {production.TECH_LINK}
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
