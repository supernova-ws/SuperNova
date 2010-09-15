<script language="JavaScript" src="scripts/flotten.js"></script>
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

var fleet_dialog = jQuery(document.createElement("span"));
fleet_dialog.dialog({ autoOpen: false, width: 200, resizable: false });

var res_names = ['{L_sys_metal}', '{L_sys_crystal}', '{L_sys_deuterium}'];
var language = ['{L_sys_ships}', '{L_sys_resources}', '{L_sys_capacity}'];
--></script>
