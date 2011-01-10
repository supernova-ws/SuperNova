<!-- INCLUDE fleet_javascript.tpl -->
<br>
<table border="0" cellpadding="0" cellspacing="1" align=center><tbody>
  <tr valign="left">
    <td class="c" colspan="{mount}">{L_imp_overview}</td>
  </tr>

  <tr>
    <th>&nbsp;</th>
    <!-- INCLUDE planet_list.tpl -->
  </tr>

  <tr>
    <th>{L_imp_name}</th>
    <!-- BEGIN planet -->
      <th class="c" width="75" style="width: 75">
        <!-- IF planet.ID -->
          <a href="overview.php?cp={planet.ID}&re=0">{planet.NAME}</a>
        <!-- ELSE -->
          {planet.NAME}
        <!-- ENDIF -->
      </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_coordinates}</th>
    <!-- BEGIN planet -->
      <th class="c">
        <!-- IF planet.ID -->
          <a href="overview.php?cp={planet.ID}&re=0">{planet.COORDINATES}</a>
        <!-- ENDIF -->&nbsp;
      </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_fields}</th>
    <!-- BEGIN planet -->
      <!-- IF planet.FIELDS_CUR >= planet.FIELDS_MAX --> 
        <!-- DEFINE $FIELD_COLOR = 'color="red"' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = '' -->
      <!-- ENDIF -->

      <th class="c">
        <!-- IF planet.FIELDS_CUR -->
          <font {$FIELD_COLOR}>{planet.FIELDS_CUR}/{planet.FIELDS_MAX}</font>
        <!-- ENDIF -->&nbsp;
      </th>
    <!-- END planet -->
  </tr>

  <td class="c" colspan="{mount}" align="left">{L_sys_resources}</td>
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
     <th style="cursor: pointer;" onclick="document.location='buildings.php?mode={prods.MODE}&cp={prods.planet.ID}&re=0&planettype={prods.planet.TYPE}';">
       <!-- IF prods.planet.LEVEL -->
         {prods.planet.LEVEL}<!-- ELSE -->-<!-- ENDIF --><!-- IF prods.planet.LEVEL_PLUS_GREEN --><font color="lime">{prods.planet.LEVEL_PLUS_GREEN}</font><!-- ENDIF --><!-- IF prods.planet.LEVEL_PLUS_YELLOW --><font color="yellow">{prods.planet.LEVEL_PLUS_YELLOW}</font><!-- ENDIF -->
     </th>
     <!-- END planet -->
   </tr>
   <!-- END prods -->
</tbody></table>
