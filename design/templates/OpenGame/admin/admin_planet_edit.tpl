<h2>{L_adm_planet_edit}</h2>
<form method="post">
  <input type="hidden" name="mode" value="{MODE}">

  <table width=600>
    <tr>
      <th colspan="4" class="c_c">
        <div>
          <div class="fl">{L_adm_planet_id}&nbsp;<input name="planet_id" value="{PLANET_ID}"></div>
          <div class="fr"><input type="submit" name="change_data" value="{L_sys_confirm}"></div>
        </div>
      </th>
    </tr>

    <tr>
      <th colspan="4" class="c_c">
        <div>
        <!-- IF PLANET_NAME -->
          <span class="fl">{PLANET_NAME}</span>
        <!-- ENDIF -->
          <span class="fr">
            <a href="admin/planet_edit.php?planet_id={PLANET_ID}&mode=structures"><span<!-- IF MODE == 'structures' --> class="positive"<!-- ENDIF -->>{L_tech[0]}</span></a>
            <a href="admin/planet_edit.php?planet_id={PLANET_ID}&mode=fleet"><span<!-- IF MODE == 'fleet' --> class="positive"<!-- ENDIF -->>{L_tech[200]}</span></a>
            <a href="admin/planet_edit.php?planet_id={PLANET_ID}&mode=defense"><span<!-- IF MODE == 'defense' --> class="positive"<!-- ENDIF -->>{L_tech[400]}</span></a>
            <a href="admin/planet_edit.php?planet_id={PLANET_ID}&mode=resources_loot"><span<!-- IF MODE == 'resources_loot' --> class="positive"<!-- ENDIF -->>{L_tech[900]}</span></a>
          </span>
        </div>
      </th>
    </tr>
    <!-- IF .unit -->
    <tr>
      <th class="c_c">{L_sys_id}</th>
      <th class="c_c">{L_adm_name}</th>
      <th class="c_c">{L_adm_on_planet}</th>
      <th class="c_c">{L_adm_planet_change}</th>
    </tr>
    <!-- ENDIF -->
    <!-- BEGIN unit -->
      <tr>
        <td class="c_r">{unit.ID}</td>
        <td class="c_l">{unit.NAME}</td>
        <td class="c_r">{unit.AMOUNT}</td>
        <td><input name="unit_list[{unit.ID}]"></td>
      </tr>
    <!-- END unit -->
  </table>
</form>

<!-- INCLUDE page_hint.tpl -->
