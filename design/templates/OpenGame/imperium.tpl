<!-- INCLUDE fleet_javascript.tpl -->
<br>
<table border="0" cellpadding="0" cellspacing="1" align=center><tbody>
  <tr valign="left">
    <td class="c" colspan="{amount}">{L_imp_overview}</td>
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

  <tr>
    <th>{L_sys_temperature}</th>
    <!-- BEGIN planet -->
      <!-- IF planet.TEMP_MAX < 40 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative' -->
      <!-- ELSEIF planet.TEMP_MAX > 40 --> 
        <!-- DEFINE $FIELD_COLOR = 'positive' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = '' -->
      <!-- ENDIF -->

      <th class="c {$FIELD_COLOR}">
        {planet.TEMP_MIN} / {planet.TEMP_MAX}
      </th>
    <!-- END planet -->
  </tr>

  <td class="c" colspan="{amount}" align="left">{L_sys_resources}</td>
  <tr>
    <th>{L_sys_metal}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
      <!-- IF planet.METAL_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative' -->
      <!-- ELSEIF planet.METAL_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning' -->
      <!-- ELSEIF planet.METAL_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive' -->
      <!-- ENDIF -->
      <th class="c">
        <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.METAL_CUR}<br><span class="{$FIELD_COLOR}">{planet.METAL_PROD}</span></a>
      </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_crystal}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
      <!-- IF planet.CRYSTAL_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative' -->
      <!-- ELSEIF planet.CRYSTAL_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning' -->
      <!-- ELSEIF planet.CRYSTAL_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive' -->
      <!-- ENDIF -->
      <th class="c">
        <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.CRYSTAL_CUR}<br><span class="{$FIELD_COLOR}">{planet.CRYSTAL_PROD}</span></a>
      </th>
    <!-- END planet -->
  </tr>
  <tr>
    <th>{L_sys_deuterium}<br>{L_imp_production}</th>
    <!-- BEGIN planet -->
      <!-- IF planet.DEUTERIUM_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative' -->
      <!-- ELSEIF planet.DEUTERIUM_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning' -->
      <!-- ELSEIF planet.DEUTERIUM_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive' -->
      <!-- ENDIF -->
      <th class="c">
        <a href="resources.php?cp={planet.ID}&re=0&planettype={planet.TYPE}">{planet.DEUTERIUM_CUR}<br><span class="{$FIELD_COLOR}">{planet.DEUTERIUM_PROD}</span></a>
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
     <td class="c" colspan="{amount}">{prods.NAME}</td>
     <!-- ENDIF -->
     
     <!-- BEGIN planet -->
     <th style="cursor: pointer;" onclick="document.location='buildings.php?mode={prods.MODE}&cp={prods.planet.ID}&re=0&planettype={prods.planet.TYPE}';">

       <!-- IF prods.planet.LEVEL_PLUS_GREEN > 0 -->
         <!-- DEFINE $PLUS_CLASS = 'positive' -->
       <!-- ELSEIF prods.planet.LEVEL_PLUS_GREEN < 0 -->
         <!-- DEFINE $PLUS_CLASS = 'negative' -->
       <!-- ENDIF -->
         
       {prods.planet.LEVEL}<!-- IF prods.planet.LEVEL_PLUS_GREEN --><span class="{$PLUS_CLASS}">{prods.planet.LEVEL_PLUS_GREEN}</span><!-- ENDIF --><!-- IF prods.planet.LEVEL_PLUS_YELLOW --><font color="yellow">{prods.planet.LEVEL_PLUS_YELLOW}</font><!-- ENDIF -->
     </th>
     <!-- END planet -->
   </tr>
   <!-- END prods -->
</tbody></table>
