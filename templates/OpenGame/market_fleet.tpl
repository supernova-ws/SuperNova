<script type="text/javascript"><!--
var originalColor;

function preCalc(){
  for(shipID in ships){
    ship = ships[shipID];
    if(ship != 1){
      ship[5] = document.getElementsByName('ships['+ ship[0] +']')[0];
      if (originalColor == undefined)
        originalColor = ship[5].style.backgroundColor;
    }
  }
}

function reCalc(obj){
  var t = Array(0,0,0);

  for(shipID in ships){
    ship = ships[shipID];
    if(ship != 1){
      ship_elem = ship[5]; //document.getElementsByName('ships['+ ship[0] +']')[0];

      ship_elem.value = parseFloat(0 + ship_elem.value);
      ship_count = ship_elem.value;
      if(ship_count>ship[4])
        ship_elem.style.backgroundColor = "#FF0000";
      else
        ship_elem.style.backgroundColor = originalColor;
      t[0] = t[0] + ship[1] * ship_count;
      t[1] = t[1] + ship[2] * ship_count;
      t[2] = t[2] + ship[3] * ship_count;
    }
  }

  document.getElementById('total_metal').innerHTML = t[0];
  document.getElementById('total_crystal').innerHTML = t[1];
  document.getElementById('total_deuterium').innerHTML = t[2];
}
//--></script>

<script type="text/javascript"><!--
var rates = Array ( {rpg_scrape_metal}, {rpg_scrape_crystal}, {rpg_scrape_deuterium});
//--></script>

{message}
<br />
<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=6><div class="fl"><!-- IF MODE == 2 -->{L_eco_mrk_scraper}<!-- ELSE -->{L_eco_mrk_stockman}<!-- ENDIF --></div><div class="fr">{L_eco_mrk_service_cost} {rpg_cost} {L_eco_mrk_dark_matter_short}</div></td></tr>
    <tr align="center" rowspan=2><td class="c" rowspan=2>{L_sys_ships}</td><td class="c" colspan=3><!-- IF MODE == 2 -->{L_eco_mrk_scraper_price} {L_eco_mrk_scraper_perShip}<!-- ELSE -->{L_eco_mrk_stockman_price} {L_eco_mrk_stockman_perShip}<!-- ENDIF --></td><td class="c" rowspan=2><!-- IF MODE == 2 -->{L_eco_mrk_scraper_onOrbit}<!-- ELSE -->{L_eco_mrk_stockman_onStock}<!-- ENDIF --></td><td class="c" rowspan=2><!-- IF MODE == 2 -->{L_eco_mrk_scraper_to}<!-- ELSE -->{L_eco_mrk_stockman_buy}<!-- ENDIF --></td></tr>
    <tr align="center"><td class="c">{L_Metal}</td><td class="c">{L_Crystal}</td><td class="c">{L_Deuterium}</td></tr>
<!-- IF NOSHIP -->
    <tr><th class="c" colspan="6" align=center><!-- IF MODE == 2 -->{L_eco_mrk_scraper_noShip}<!-- ELSE -->{L_eco_mrk_stockman_noShip}<!-- ENDIF --></td></tr>
<!-- ELSE --><!-- BEGIN ships -->    
    <tr>
      <th>{ships.NAME}</th>
      <th><span class="fr">{ships.METAL}</span></th>
      <th><span class="fr">{ships.CRYSTAL}</span></th>
      <th><span class="fr">{ships.DEUTERIUM}</span></th>
      <th><span class="fr">{ships.COUNT}</span></th>
      <th><input name="ships[{ships.ID}]" value="{ships.AMOUNT}" onKeyUp="javascript:reCalc(this);"></th>
    </tr>
<!-- END ships --><!-- ENDIF -->
    <tr align=right>
      <td class="c" align=left>{L_eco_mrk_scraper_total}</td>
      <td class="c"><span id="total_metal">0</span></td>
      <td class="c"><span id="total_crystal">0</span></td>
      <td class="c"><span id="total_deuterium">0</span></td>
      <td class="c" colspan=2><input type="submit" name="<!-- IF MODE == 2 -->scrape<!-- ELSE -->stock<!-- ENDIF -->" value="<!-- IF MODE == 2 -->{L_eco_mrk_scraper_to}<!-- ELSE -->{L_eco_mrk_stockman_buy}<!-- ENDIF -->"></td>
    </td></tr>
  </table>
  <input type="hidden" name="mode" value="{MODE}">
</form>

<script type="text/javascript"><!--
varTemp = '{exchangeTo}';

if(varTemp == '')
  varTemp = 0;

var ships = Array( {ships} );

preCalc();
reCalc();
//--></script>
