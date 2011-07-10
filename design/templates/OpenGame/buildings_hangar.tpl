<br />
{buildinglist}

<div>{error_msg}</div>

<font color="#ff0000">{noresearch}</font>

<form action="buildings.php?mode={MODE}" method="post">
  <!-- IF .production -->
  <table valign="top" align="center" width="600px">
    <!-- BEGIN production -->
    <tr>
      <th>
        <a href="infos.php?gid={production.ID}">
          {production.NAME}<br />
          <img border=0 src="{dpath}gebaeude/{production.ID}.gif" align=top width=120 height=120>
        </a>
      </th>
      <td valign="top">
        <table border=0 valign=top>
          <tr>
            <td align=justify valign="top" style="border: 0">{production.DESCRIPTION}</td>
            <td valign=top style="border: 0">
              <table valign=top>
                <tr>
                  <th class="c_l">{L_sys_ship_armour}</th>
                  <td class="c_r">{production.ARMOR}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_sys_ship_shield}</th>
                  <td class="c_r">{production.SHIELD}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_sys_ship_weapon}</th>
                  <td class="c_r">{production.WEAPON}</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td align=left valign=top style="border: 0">
              <table align="center">
                <tr>
                  <th class="c_l">{L_ConstructionTime}</th>
                  <td class="c_r">{production.TIME}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_built}</th>
                  <td class="c_r">{production.LEVEL}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_can_build}</th>
                  <td class="c_r">{production.BUILD_CAN}</td>
                </tr>
              </table>
            </td>
            <td valign=top style="border: 0">
              <table align="center">
                <tr>
                  <th class="c_l">{L_sys_metal}</th>
                  <td class="c_r">{production.METAL_REST}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_sys_crystal}</th>
                  <td class="c_r">{production.CRYSTAL_REST}</td>
                </tr>
                <tr>
                  <th class="c_l">{L_sys_deuterium}</th>
                  <td class="c_r">{production.DEUTERIUM_REST}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <!-- IF production.BUILD_CAN && ! production.MESSAGE-->
          {L_Construire}: <input type=text name=fmenge[{production.ID}] alt='{production.NAME}' size=5 maxlength=5 value=0 tabindex="{production.TABINDEX}"> / {production.BUILD_CAN}
        <!-- ELSEIF production.MESSAGE -->
          <span class="error">{production.MESSAGE}</span>
        <!-- ENDIF -->


    </tr>
    <!-- END production -->
    <tr>
     <th class="c_c" colspan="2">
       <input type="submit" value="{L_Construire}">
     </th>
   </tr>
  </table>
  <!-- ELSE -->
  <!-- ENDIF -->

</form>
