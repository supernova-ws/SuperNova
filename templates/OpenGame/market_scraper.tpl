<br />
<script type="text/javascript"><!--
var originalColor;

function reCalc(){
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
    <tr><td class="c" colspan=4>{L_eco_mrk_trader}</td></tr>
    <tr align="center">
      <td class="c">{L_sys_resources}</td>
      <td class="c">{L_Metal}</td>
      <td class="c">{L_Crystal}</td>
      <td class="c">{L_Deuterium}</td>
    </tr>

<!-- BEGIN ships -->    
    <tr>
      <th>{ships.NAME}</th>
      <th>{ships.METAL}</th>
      <th>{ships.CRYSTAL}</th>
      <th>{ships.DEUTERIUM}</th>
      <th><input name="ships[ships.ID]" value="{spend3}" onKeyUp="javascript:reCalc();"></th>
      <th>{ships.COUNT}</th>
    </tr>
<!-- END ships -->    
    <tr><td class="c" colspan=4 align=center>
      <input type="submit" name="exchange" value="{L_eco_mrk_sellFor}">
    </td></tr>
    <tr><td class="c" colspan=4 align=center>{L_eco_mrk_trader_cost}{rpg_cost_trader} {L_eco_mrk_dark_matter_short}</td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
varTemp = '{exchangeTo}';

if(varTemp == '')
  varTemp = 0;

document.getElementsByName('exchangeTo')[0].selectedIndex = varTemp;
reCalc();
//--></script>
