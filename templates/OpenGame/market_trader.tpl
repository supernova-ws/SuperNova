<script type="text/javascript"><!--
var originalColor;

function reCalc(){
  var inz = 0;

  si = document.getElementsByName('exchangeTo')[0].selectedIndex;
  sinp = document.getElementsByName('spend[' + si + ']')[0];

  for (i=0;i<=3;i++){
    inp = document.getElementsByName('spend[' + i + ']')[0];
    inp.value = parseFloat(0 + inp.value);

    if(i == si){
      inp.disabled = true;
      inp.style.backgroundColor = "#004400";
    }else{
      if(inp.value>resources[i]){
        inp.style.backgroundColor = "#FF0000";
      }else{
        inp.style.backgroundColor = originalColor;
      }
      inp.disabled = false;
      inz = inz + inp.value * rates[i] / rates[si];
      document.getElementsByName('res_left' + i + '')[0].innerHTML = resources[i] - inp.value;
    }
  }
  sinp.value = parseFloat(inz);
  document.getElementsByName('res_left' + si + '')[0].innerHTML = resources[si] + parseFloat(inz);
}

function reCourse(){
  si = document.getElementsByName('exchangeTo')[0].selectedIndex;
  for (i=0;i<=3;i++){
    document.getElementById('course' + i).innerHTML = rates[i] / rates[si];
  }
  reCalc();
}
//--></script>

<script type="text/javascript"><!--
var rates = Array ( {rpg_exchange_metal}, {rpg_exchange_crystal}, {rpg_exchange_deuterium}, {rpg_exchange_darkMatter});
var resources = Array ( {resources} );
//--></script>

{message}
<br />
<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=5><div class="fl">{L_eco_mrk_trader}</div><div class="fr">{L_eco_mrk_service_cost} {rpg_cost_trader} {L_eco_mrk_dark_matter_short}</div></td></tr>
    <tr align="center">
      <td class="c">{L_sys_resources}</td>
      <td class="c">{L_Ressverf}</td>
      <td class="c">{L_eco_mrk_trader_exchange}</td>
      <td class="c">{L_eco_mrk_trader_left}</td>
      <td class="c">{L_eco_mrk_trader_course}</td>
    </tr>

<!-- BEGIN resources -->    
    <tr>
      <th>{resources.NAME}</th>
      <th><span class="fr">{resources.AVAIL}</span></th>
      <th><input name="spend[{resources.ID}]" value="{resources.SPEND}" onKeyUp="javascript:reCalc();"></th>
      <th><span class="fr" id="res_left{resources.ID}"></span></th>
      <th><span class="fr" id="course{resources.ID}"></span></th>
    </tr>
<!-- END resources -->    

    <tr><td class="c" colspan=5 align=center><input type="submit" name="exchange" value="{L_eco_mrk_trader_to}">
      <select name="exchangeTo" onChange="javascript:reCourse();">
        <option value="0">{L_sys_metal}
        <option value="1">{L_sys_crystal}
        <option value="2">{L_sys_deuterium}
      </select></td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
varTemp = '{exchangeTo}';
if(varTemp == '') varTemp = 0;
document.getElementsByName('exchangeTo')[0].selectedIndex = varTemp;

if (originalColor == undefined)
  originalColor = document.getElementsByName('spend[0]')[0].style.backgroundColor;

reCourse();
//--></script>
