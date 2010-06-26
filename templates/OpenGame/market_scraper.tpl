<br />
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

//  alert(obj.value);

/*
  var inz = 0;

  si = document.getElementsByName('exchangeTo')[0].selectedIndex;
  sinp = document.getElementsByName('spend[' + si + ']')[0];

  for (i=0;i<=3;i++){
    document.getElementById('course' + i).innerHTML = rates[i] / rates[si];

    inp = document.getElementsByName('spend[' + i + ']')[0];
    if(i == si){
      inp.disabled = true;
      inp.style.backgroundColor = "#00FF00";
    }else{
      if (originalColor == undefined)
        originalColor = inp.style.backgroundColor;
      inp.style.backgroundColor = originalColor;

      inp.disabled = false;
      inz = inz + inp.value * rates[i] / rates[si];
      inp.value = parseFloat(0 + inp.value);
    }
  }
  sinp.value = parseFloat(inz);
*/
}
//--></script>

<script type="text/javascript"><!--
var rates = Array ( {rpg_exchange_metal}, {rpg_exchange_crystal}, {rpg_exchange_deuterium}, {rpg_exchange_darkMatter});
//--></script>

<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=6>{L_eco_mrk_scraper}</td></tr>
    <tr align="center" rowspan=2><td class="c" rowspan=2>{L_sys_resources}</td><td class="c" colspan=3>{L_eco_mrk_scrapePrice}</td><td class="c" rowspan=2>{L_eco_mrk_toScrape}</td><td class="c" rowspan=2>{L_eco_mrk_orbiting}</td></tr>
    <tr align="center"><td class="c">{L_Metal}</td><td class="c">{L_Crystal}</td><td class="c">{L_Deuterium}</td></tr>
<!-- BEGIN ships -->    
    <tr><th>{ships.NAME}</th><th>{ships.METAL}</th><th>{ships.CRYSTAL}</th><th>{ships.DEUTERIUM}</th>
      <th><input name="ships[{ships.ID}]" value="{ships.SELL}" onKeyUp="javascript:reCalc(this);"></th>
      <th>{ships.COUNT}</th>
    </tr>
<!-- END ships -->    
    <tr><td class="c" colspan=6 align=center>
      <input type="submit" name="exchange" value="{L_eco_mrk_sellFor}">
      {L_Metal} <span id="total_metal">0</span>
      {L_Crystal} <span id="total_crystal">0</span>
      {L_Deuterium} <span id="total_deuterium">0</span>
    </td></tr>
    <tr><td class="c" colspan=6 align=center>{L_eco_mrk_scraper_cost} {rpg_cost_scraper} {L_eco_mrk_dark_matter_short}</td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
varTemp = '{exchangeTo}';

if(varTemp == '')
  varTemp = 0;

var ships = Array( {ships} );

preCalc();
reCalc();
//--></script>
