<!-- INCLUDE fleet_javascript.tpl -->
<h2>{L_flt_gather_all}</h2>
<!-- IF .results -->
  <h3>{L_flt_gather_report}</h3>
  <table border="0" cellpadding="0" cellspacing="1">
    <tr>
      <td class="c">{L_fl_from}</td>
      <td class="c">{L_flt_report}</td>
    </tr>

    <!-- BEGIN results -->
    <tr>
      <td>{results.TYPE_PRINT} &quot;{results.NAME}&quot; [{results.GALAXY}:{results.SYSTEM}:{results.PLANET}]</td>
      <td>
        <b>{results.MESSAGE}</b><br>
        <!-- BEGIN units -->
          {results.units.NAME} {results.units.AMOUNT}<br />
        <!-- END units -->
      </td>
    </tr>
    <!-- END results -->
  </table>
  <br />
<!-- ENDIF -->

<script type="text/javascript"><!--
var colonies = Array();
--></script>

<script type="text/javascript"><!--
function reCalcGathering()
{
  var resourceGrid = Array();
  resourceGrid[{D_RES_METAL}] = 0;
  resourceGrid[{D_RES_CRYSTAL}] = 0;
  resourceGrid[{D_RES_DEUTERIUM}] = 0;
  resourceGrid[0] = 0;
  for(colony in colonies)
  {
    resourceGrid[colony] = 0;
    if(document.getElementsByName('resources[' + colony + '][{D_RES_METAL}]')[0].checked)
    {
      resourceGrid[{D_RES_METAL}] += parseInt(colonies[colony][0]);
      resourceGrid[colony] += parseInt(colonies[colony][0]);
    }
    if(document.getElementsByName('resources[' + colony + '][{D_RES_CRYSTAL}]')[0].checked)
    {
      resourceGrid[{D_RES_CRYSTAL}] += parseInt(colonies[colony][1]);
      resourceGrid[colony] += parseInt(colonies[colony][1]);
    }
    if(document.getElementsByName('resources[' + colony + '][{D_RES_DEUTERIUM}]')[0].checked)
    {
      resourceGrid[{D_RES_DEUTERIUM}] += parseInt(colonies[colony][2]);
      resourceGrid[colony] += parseInt(colonies[colony][2]);
    }
    document.getElementById('ga_' + colony + '_0').innerHTML = sn_format_number(resourceGrid[colony]);
  }
  document.getElementById('ga_0_{D_RES_METAL}').innerHTML = sn_format_number(resourceGrid[{D_RES_METAL}]);
  document.getElementById('ga_0_{D_RES_CRYSTAL}').innerHTML = sn_format_number(resourceGrid[{D_RES_CRYSTAL}]);
  document.getElementById('ga_0_{D_RES_DEUTERIUM}').innerHTML = sn_format_number(resourceGrid[{D_RES_DEUTERIUM}]);
  document.getElementById('ga_0_0').innerHTML = sn_format_number(resourceGrid[{D_RES_METAL}] + resourceGrid[{D_RES_CRYSTAL}] + resourceGrid[{D_RES_DEUTERIUM}]);
}
--></script>

<form action="fleet.php?fleet_page=5" method="post">
  <table border="0" cellpadding="0" cellspacing="1">
    <tr>
      <td class="c">{L_sys_coordinates}</td>
      <td class="c">{L_fl_from}</td>
      <td class="c"><input type="checkbox" class="planet_gather" onchange="jQuery('.planet_metal').attr('checked', jQuery(this).is(':checked'));reCalcGathering();">{L_sys_metal}</td>
      <td class="c"><input type="checkbox" class="planet_gather" onchange="jQuery('.planet_crystal').attr('checked', jQuery(this).is(':checked'));reCalcGathering();">{L_sys_crystal}</td>
      <td class="c"><input type="checkbox" class="planet_gather" onchange="jQuery('.planet_deuterium').attr('checked', jQuery(this).is(':checked'));reCalcGathering();">{L_sys_deuterium}</td>
      <td class="c"><input id="planet_gather_all" type="checkbox" onchange="jQuery('.planet_gather').attr('checked', jQuery(this).is(':checked')).change();reCalcGathering();">{L_res_total}</td>
      <td class="c">{L_sys_cargo_bays}</td>
      <td class="c">{L_sys_time}</td>
    </tr>

    <!-- BEGIN colonies -->       
      <tr>
        <th>[{colonies.GALAXY}:{colonies.SYSTEM}:{colonies.PLANET}]</th>
        <th>
          <span class="fl">{colonies.TYPE_PRINT} &quot;{colonies.NAME}&quot;</span>
        </th>
        <th>
          <span class="fl"><input type="checkbox" name="resources[{colonies.ID}][{D_RES_METAL}]"     value="1" class="planet_metal planet_{colonies.ID}" onchange="reCalcGathering();"></span>
          <span class="fr">{colonies.METAL_TEXT}</span>
        </th>
        <th>
          <span class="fl"><input type="checkbox" name="resources[{colonies.ID}][{D_RES_CRYSTAL}]"   value="1" class="planet_crystal planet_{colonies.ID}" onchange="reCalcGathering();"></span>
          <span class="fr">{colonies.CRYSTAL_TEXT}</span>
        </th>
        <th>
          <span class="fl"><input type="checkbox" name="resources[{colonies.ID}][{D_RES_DEUTERIUM}]" value="1" class="planet_deuterium planet_{colonies.ID}" onchange="reCalcGathering();"></span>
          <span class="fr">{colonies.DEUTERIUM_TEXT}</span>
        </th>
        <th>
          <span class="fl"><input type="checkbox" name="planets[]" value="{colonies.ID}" class="planet_gather" id="planet_{colonies.ID}" onchange="jQuery('.planet_{colonies.ID}').attr('checked', jQuery(this).is(':checked'));reCalcGathering();"></span>
          <span class="fr" id="ga_{colonies.ID}_0">{colonies.RESOURCES_TEXT}</span>
        </th>
        <!-- IF colonies.FLEET_CAPACITY -->
        <th><span class="fr">{colonies.FLEET_CAPACITY_TEXT}</span></th>
        <th><span class="fr">{colonies.DURATION}</span></th>
        <!-- ELSE -->
        <th colspan=2><span class="negative">{L_flt_no_transports}</span></th>
        <!-- ENDIF -->
      </tr>
      <script type="text/javascript">colonies['{colonies.ID}'] = ['{colonies.METAL}', '{colonies.CRYSTAL}', '{colonies.DEUTERIUM}', '{colonies.FLEET_CAPACITY}'];</script>
    <!-- BEGINELSE colonies -->
      <th colspan="8">{L_fl_nocolonies}</th>
    <!-- END colonies -->
    <!-- IF METAL_NEED > 0 || CRYSTAL_NEED > 0 || DEUTERIUM_NEED > 0 -->
      <!-- DEFINE $ROWSPAN = 2 -->
    <!-- ENDIF -->
    <tr>
      <th class="c_l" colspan="2">{L_sys_total}</th>
      <th class="c_r"><span id="ga_0_{D_RES_METAL}"></span></th>
      <th class="c_r"><span id="ga_0_{D_RES_CRYSTAL}"></span></th>
      <th class="c_r"><span id="ga_0_{D_RES_DEUTERIUM}"></span></th>
      <th class="c_r"><span id="ga_0_0"></span></th>
      <th class="c_c" colspan="2" rowspan="{$ROWSPAN}"><input type="submit" value="{L_flt_gather_all}" /></th>
    </tr>
    <!-- IF $ROWSPAN -->
    <tr>
      <th class="c_l" colspan="2">{L_sys_need}</th>
      <th class="c_r">{METAL_NEED}</th>
      <th class="c_r">{CRYSTAL_NEED}</th>
      <th class="c_r">{DEUTERIUM_NEED}</th>
      <th class="c_r"></th>
    </tr>
    <!-- ENDIF -->
  </table>
  <!-- INCLUDE page_hint.tpl -->
</form>

<script type="text/javascript"><!--
   jQuery(document).ready(function() 
     {
       jQuery('#planet_gather_all').attr('checked', true).change();
//       jQuery('#planet_gather_all').check();
     }
   );
--></script> 
