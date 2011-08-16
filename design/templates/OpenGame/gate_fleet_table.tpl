<h2>{L_tech[43]}</h2>
<form action="jumpgate.php" method="post">
  <table><tbody>
    <tr>
      <th>{gate_start_moon}</th>
      <th>
        {gate_start_link} {gate_start_name} 
        <!-- IF GATE_JUMP_REST_TIME -->
          <span id="jump_gate_timer"></span>
          <script type="text/javascript"><!--
            sn_timers.unshift({id: 'jump_gate_timer', type: 0, active: true, start_time: '{TIME_NOW}', options: {msg_done: '', que: [['1', '', {GATE_JUMP_REST_TIME}, '1']]}});
          // --></script>
        <!-- ENDIF -->
      </th>
    </tr>

    <tr>
      <th>{gate_dest_moon}</th>
      <th>
        <select name="jmpto">
          {gate_dest_moons}
        </select>
      </th>
    </tr>
  </tbody></table>

  <table width="519"><tbody>
    <tr>
      <th class="c_l" colspan="2">{gate_use_gate} : {gate_ship_sel}</th>
    </tr>
    {gate_fleet_rows}
    <tr>
      <th class="c_c" colspan="2"><input value="{gate_jump_btn}" type="submit"></th>
    </tr>
  </tbody></table>
</form>
