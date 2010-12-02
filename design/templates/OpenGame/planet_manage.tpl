<br />
<form action="overview.php?mode=renameplanet&pl={planet_id}" method="POST">
  <table width=519>
    <tr>
      <td style="white-space: nowrap;" class="c" colspan=4>
        <div class="fl">{PLANET_TYPE_TEXT} "{PLANET_NAME}" <a href="galaxy.php?mode=0&galaxy={PLANET_GALAXY}&system={PLANET_SYSTEM}">[{PLANET_GALAXY}:{PLANET_SYSTEM}:{PLANET_PLANET}]</a></div>
        <div class="fr"><a href="overview.php?mode=manage"><font color="green">[{L_ov_return}]</font></a></td></div>
      </td>
    </tr>

    <tr>
      <th class="c">
        <!-- INCLUDE planet_governor.tpl -->
      </th>
      <th class="c" colspan=3>
        <table>
          <tr>
            <!-- BEGIN governors -->
              <th>
                {governors.NAME}<br>
                <img src="{dpath}gebaeude/{governors.ID}.jpg" align="top" width="120" height="120" />
              </th>
              <!-- IF (governors.S_ROW_COUNT + 1) mod 3 == 0 -->
                </tr><tr>
              <!-- ENDIF -->

            <!-- END governors -->
          </tr>
        </table>
      </th>
    </tr>

    <tr>
      <th>{L_ov_new_name}</th>
      <th><input type="text" name="newname" size=25 maxlength=20 value="{PLANET_NAME}"></th>
      <th><input type="submit" name="action" value="{L_ov_rename}"></th>
    </tr>

    <tr>
      <th colspan=3>
        <input type="submit" name="action" value="{L_colony_abandon}" alt="{L_colony_abandon}">
      </th>
     </tr>
  </table>
</form>
