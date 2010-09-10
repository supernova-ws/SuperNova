<br>
<table border="0" cellpadding="0" cellspacing="1" width="750" align=center><tbody>
  <tr height="20" valign="left">
    <td class="c" colspan="{mount}">{L_imp_overview}</td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <!-- BEGIN planet -->
    <th valign="top">
      <div style="position: relative; height: 75px; width: 75px;">
        <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
          <a href="overview.php?cp={planet.ID}&re=0"><img style="border-style: none;" src="{dpath}planeten/{planet.IMAGE}.jpg" height="100%" width="100%"></a>
        </span>

        <!-- IF planet.BUILDING -->
          <span title="{planet.BUILDING_TIP}" style="position: absolute; top: 0px; left: 0px; width: 25%; height: 25%">
            <a href="buildings.php?cp={planet.ID}&re=0"><img src="images/build_building.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->
        
        <!-- IF planet.TECH -->
          <span title="{planet.TECH}" style="position: absolute; top: 25%; left: 0px; width: 25%; height: 25%">
            <a href="buildings.php?mode=research&cp={planet.ID}&re=0"><img src="images/build_tech.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->

        <!-- IF planet.HANGAR -->
          <span title="{planet.HANGAR}" style="position: absolute; top: 50%; left: 0px; width: 25%; height: 25%;">
            <a href="buildings.php?mode=fleet&cp={planet.ID}&re=0"><img src="images/build_hangar.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->

        <!-- IF planet.ENEMY != 0 -->
          <span style="position: absolute; top: 26%; left: 26%; width: 48%; height: 48%;">
            <img src="images/icon_warning.png" height="100%" width="100%" style="border-style: none; cursor: pointer;" onclick="javascript:window.location = '?cp={planet.ID}&re=0'">
          </span>
        <!-- ENDIF -->

        <span style="position: absolute; left: 0; width: 100%; top: 96%; height: 4%; overflow: hidden;">
          <div class="fl" style="position: relative; left: 0px; height: 100%; width: {planet.FILL}%; background-color: <!-- IF planet.FILL >= 100 -->red<!-- ELSEIF planet.FILL > 80 -->yellow<!-- ELSE -->green<!-- ENDIF -->;"></div>
        </span>
      </div>
      <!-- IF planet.BUILDING -->{planet.BUILDING}<span id="ov_building{planet.ID}_timer"></span><!-- ENDIF -->
    </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{name}</th>
    <!-- BEGIN planet -->
    <th class="c" width="75"><a href="overview.php?cp={planet.ID}&re=0">{planet.NAME}</a></th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{coordinates}</th>
    <!-- BEGIN planet -->
    <th class="c"><a href="overview.php?cp={planet.ID}&re=0">{planet.COORDINATES}</a></th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{fields}</th>
    <!-- BEGIN planet -->
    <th class="c"><font<!-- IF planet.FIELDS_CUR >= planet.FIELDS_MAX --> color="red"<!-- ENDIF -->>{planet.FIELDS_CUR}/{planet.FIELDS_MAX}</font></th>
    <!-- END planet -->
  </tr>

  <td class="c" colspan="{mount}" align="left">{L_resources}</td>
  <tr>
    <th>{L_sys_metal}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
    <th class="c">
      <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.METAL_CUR}<br>{planet.METAL_PROD}</a>
    </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_crystal}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
    <th class="c">
      <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.CRYSTAL_CUR}<br>{planet.CRYSTAL_PROD}</a>
    </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_deuterium}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
    <th class="c">
      <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.DEUTERIUM_CUR}<br>{planet.DEUTERIUM_PROD}</a>
    </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_energy}<br>{L_imp_production}</th>
    <!-- BEGIN planet --><th class="c"><a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.ENERGY_CUR}<br>{planet.ENERGY_MAX}</a></th><!-- END planet -->
  </tr>

   <!-- BEGIN prods -->
   <tr>
     <!-- IF prods.MODE -->
     <th>{prods.NAME}</th>
     <!-- ELSE -->
     <td class="c" colspan="{mount}">{prods.NAME}</td>
     <!-- ENDIF -->
     
     <!-- BEGIN planet -->
     <th>
       <!-- IF prods.planet.LEVEL -->
       <a href="buildings.php?mode={prods.MODE}&cp={prods.planet.ID}&re=0&planettype={prods.planet.TYPE}">{prods.planet.LEVEL}</a>
       <!-- ELSE -->-<!-- ENDIF -->
     </th>
     <!-- END planet -->
   </tr>
   <!-- END prods -->
</tbody></table>
<script type="text/javascript" src="scripts/wz_tooltip.js"></script>
