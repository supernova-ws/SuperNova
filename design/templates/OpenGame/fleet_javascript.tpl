<script language="JavaScript" src="js/fleet.js"></script>
<script type="text/javascript"><!--
var fleets = Array();
<!-- BEGIN fleets -->
  fleets['{fleets.ID}'] = [[
    <!-- BEGIN ships -->
    ['{ships.NAME}', '{ships.AMOUNT}', '{ships.SPEED}', '{ships.CONSUMPTION}', '{ships.CAPACITY}', '{ships.ID}'],
    <!-- END ships -->
  ], ['{fleets.METAL}', '{fleets.CRYSTAL}', '{fleets.DEUTERIUM}']];
<!-- END fleets -->
var res_names = ['{L_sys_metal}', '{L_sys_crystal}', '{L_sys_deuterium}'];
jQuery.extend(language, {sys_ships: '{L_sys_ships}', sys_resources: '{L_sys_resources}', sys_capacity: '{L_sys_capacity}'});

//   fleets[{fleets.ID}][0][{ships.ID}] = ['{ships.NAME}', '{ships.AMOUNT}', '{ships.SPEED}', '{ships.CONSUMPTION}', '{ships.CAPACITY}', '{ships.ID}'];
--></script>
