<script language="JavaScript" src="js/fleet.js"></script>
<script type="text/javascript"><!--
var fleets = Array();
<!-- BEGIN fleets -->
  fleets[{fleets.ID}] = 
    [
      [
        <!-- BEGIN ships -->
        ['{ships.NAME}', '{ships.AMOUNT}', '{ships.SPEED}', '{ships.CONSUMPTION}', '{ships.CAPACITY}'],
        <!-- END ships -->
      ],
      ['{fleets.METAL}', '{fleets.CRYSTAL}', '{fleets.DEUTERIUM}'],
    ];
<!-- END fleets -->

var res_names = ['{L_sys_metal}', '{L_sys_crystal}', '{L_sys_deuterium}'];
var language = ['{L_sys_ships}', '{L_sys_resources}', '{L_sys_capacity}'];
--></script>
