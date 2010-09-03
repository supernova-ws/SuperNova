<script type="text/javascript"><!--
var production = Array();
<!-- BEGIN production -->
  <!-- IF production.ID && production.LEVEL -->
    production[{production.ID}] = [{production.LEVEL}];
  <!-- ENDIF -->
<!-- END production -->

function res_set_all(obj)
{
  for(production_id in production)
  {
    document.getElementById('production' + production_id).value = obj.value;
  }
}
--></script>

<br>
<form action="" method="post">
  <table width="569"><tbody>
    <tr><td class="c" colspan="6">{L_res_planet_production} "{PLANET_NAME}"</td></tr>
    <tr><th colspan="6">
      <div style="border: 1px solid rgb(153, 153, 255); width: 100%;">
        <div id="AlmDBar" style="background-color: <!-- IF PRODUCTION_LEVEL == 100 -->#00C000<!-- ELSE -->#C00000<!-- ENDIF -->; width: {PRODUCTION_LEVEL}%; float:left;">{PRODUCTION_LEVEL}%</div>
      </div>
    </th></tr>
    <tr align=center>
      <td class="c">&nbsp;</td>
      <td class="c" width="60">{L_sys_metal}</td>
      <td class="c" width="60">{L_sys_crystal}</td>
      <td class="c" width="60">{L_sys_deuterium}</td>
      <td class="c" width="60">{L_sys_energy}</td>
      <td class="c">
        <select size="1" onChange="res_set_all(this)" id="res_set_all()">
          <option>-</option>
          <!-- BEGIN !option -->
            <option value="{option.VALUE}">{option.VALUE}%</option>
          <!-- END option -->
        </select>
      </td>
    </tr>
    <!-- BEGIN production -->
      <tr>
        <th height="22">{production.TYPE}<!-- IF production.LEVEL --> ({production.LEVEL} {production.LEVEL_TYPE})<!-- ENDIF --></th>
        <th>{production.METAL_TYPE}</th>
        <th>{production.CRYSTAL_TYPE}</th>
        <th>{production.DEUTERIUM_TYPE}</th>
        <th>{production.ENERGY_TYPE}</th>
        <th>
          <!-- IF production.LEVEL -->
            <select name="production[{production.ID}]" id="production{production.ID}" size="1">
              <!-- BEGIN !option -->
                <option value="{option.VALUE}"<!-- IF option.VALUE == production.PERCENT --> selected<!-- ENDIF -->>{option.VALUE}%</option>
              <!-- END option -->
            </select>
          <!-- ELSE -->
            &nbsp;
          <!-- ENDIF -->
        </th>
      </tr>
    <!-- END production -->
    <tr><td class="k" colspan="6"><input value="{L_res_calculate}" type="submit"></td></tr>
  </tbody></table>
</form>

<table width="569"><tbody>
  <tr><td class="c" colspan="4">{Widespread_production}</td></tr>
  <tr>
    <th width="100">&nbsp;</th>
    <th>{L_res_daily}</th>
    <th>{L_res_weekly}</th>
    <th>{L_res_monthly}</th>
  </tr>
  <!-- BEGIN resources -->
    <tr>
      <th>{resources.NAME}</th>
      <th>{resources.DAILY}</th>
      <th>{resources.WEEKLY}</th>
      <th>{resources.MONTHLY}</th>
    </tr>
  <!-- END resources -->
</tbody></table>
<br>
<table width="569"><tbody>
  <tr><td class="c" colspan="2">{L_res_storage_fill}</td></tr>
  <!-- BEGIN resources -->
    <tr>
      <th>{resources.NAME}</th>
      <th width="469">
        <div style="border: 1px solid rgb(153, 153, 255); width: 100%;">
          <!-- IF resources.STORAGE > 100 -->
            <!-- DEFINE $BAR_COLOR = '#C00000' -->
          <!-- ELSEIF resources.STORAGE > 80 -->
            <!-- DEFINE $BAR_COLOR = '#C0C000' -->
          <!-- ELSE -->
            <!-- DEFINE $BAR_COLOR = '#00C000' -->
          <!-- ENDIF -->
          <div id="AlmMBar" style="background-color: {$BAR_COLOR}; width: {resources.BAR}%; float: left;">{resources.STORAGE}%</div>
        </div>
      </th>
    </tr>
  <!-- END resources -->
</tbody></table>
<!-- INCLUDE page_hint.tpl -->