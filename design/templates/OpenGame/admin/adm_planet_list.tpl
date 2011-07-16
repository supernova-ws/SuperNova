<h2>{PAGE_TITLE}</h2>
<table>
  <tr class="c_c">
    <th rowspan="2">{L_sys_id}</th>
    <th rowspan="2">{L_adm_name}</th>
    <th colspan="3">{L_sys_coordinates}</th>
    <th rowspan="2">{L_sys_planet_title_short}</th>
    <!-- IF PARENT_COLUMN -->
      <th colspan="2">{L_adm_planet_parent}</th>
    <!-- ENDIF -->
    <th colspan="2">{L_adm_sys_owner}</th>
  </tr>
  <tr class="c_c">
    <th>{L_sys_galaxy}</th>
    <th>{L_sys_system}</th>
    <th>{L_sys_planet}</th>
    <!-- IF PARENT_COLUMN -->
      <th>{L_sys_id}</th>
      <th>{L_adm_name}</th>
    <!-- ENDIF -->
    <th>{L_sys_id}</th>
    <th>{L_sys_user_name_short}</th>
  </tr>
  <!-- BEGIN planet -->
  <tr>
    <td class="c_r">{planet.ID}</td>
    <td>{planet.NAME}</td>
    <td>{planet.GALAXY}</td>
    <td>{planet.SYSTEM}</td>
    <td>{planet.PLANET}</td>
    <td>{planet.PLANET_TYPE_PRINT}</td>
    <!-- IF PARENT_COLUMN -->
      <td class="c_r">{planet.PARENT_ID}</td>
      <td>{planet.PARENT_NAME}</td>
    <!-- ENDIF -->
    <td class="c_r">{planet.OWNER_ID}</td>
    <td>{planet.OWNER}</td>
  </tr>
  <!-- END planet -->
</table>
