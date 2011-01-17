<script type="text/javascript" src="js/fleet.js"></script>

<br>
<form action="fleet.php?fleet_page=5" method="post">
  <table border="0" cellpadding="0" cellspacing="1">
    <tr><td colspan="4" class="c">{L_flt_gather_all}</td></tr>
    <tr>
      <td class="c"><input id="planet_gather_all" type="checkbox" onclick="jQuery('.planet_gather').attr('checked', jQuery('#planet_gather_all').is(':checked'));"></td>
      <td class="c">{L_sys_planet_coords}</td>
      <td class="c">{L_sys_planet_name}</td>
      <td class="c">{L_flt_planet_type}</td>
    </tr>
    
    <!-- BEGIN colonies -->       
      <tr>
        <th><input type="checkbox" name="planets[]" value="{colonies.ID}" class="planet_gather">{colonies.ID}</th>
        <th>[{colonies.GALAXY}:{colonies.SYSTEM}:{colonies.PLANET}]</th>
        <th>{colonies.NAME}</th>
        <th>{colonies.TYPE_PRINT}</th>
      </tr>
    <!-- BEGINELSE colonies -->
      <th colspan="4">{L_fl_nocolonies}</th>
    <!-- END colonies -->
    <tr><th colspan="4"><input type="submit" value="{L_fl_continue}" /></th></tr>

  </table>
  <!-- INCLUDE page_hint.tpl -->
</form>

<script type="text/javascript"><!--
   jQuery(document).ready(function() 
     {
       jQuery('#planet_gather_all').attr('checked', true);
       jQuery('#planet_gather_all').click();
     }
   );
 --></script> 
