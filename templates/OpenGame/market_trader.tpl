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
      <td class="c">{L_fl_on_stores}</td>
      <td class="c">{L_eco_mrk_trader_exchange}</td>
      <td class="c">{L_eco_mrk_trader_left}</td>
      <td class="c">{L_eco_mrk_trader_course}</td>
    </tr>

    <!-- BEGIN resources -->    
      <tr>
        <th>{resources.NAME}</th>
        <th><span class="fr">{resources.AVAIL}</span></th>
        <th><input id="spend{resources.ID}" name="spend[{resources.ID}]" value="{resources.SPEND}" onKeyUp="javascript:reCalc();"></th>
        <th><span class="fr" id="res_left{resources.ID}"></span></th>
        <th><span class="fr" id="course{resources.ID}"></span></th>
      </tr>
    <!-- END resources -->    

    <tr><td class="c" colspan=5 align=center><input type="submit" name="exchange" value="{L_eco_mrk_trader_to}">
      <select name="exchangeTo" id="exchangeTo" onChange="javascript:reCourse();">
        <option value="0">{L_sys_metal}
        <option value="1">{L_sys_crystal}
        <option value="2">{L_sys_deuterium}
      </select></td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
var originalColor;

function reCalc(){
  var inz = 0;

  si = element_cache['exchangeTo'].selectedIndex;
  sinp = element_cache['spend' + si];

  for (i=0;i<=3;i++){
    inp = element_cache['spend' + i];
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
      element_cache['res_left' + i].innerHTML = resources[i] - inp.value;
    }
  }
  sinp.value = parseFloat(inz);
  element_cache['res_left' + si].innerHTML = resources[si] + parseFloat(inz);
}

function reCourse(){
  rate_for_selected = rates[element_cache['exchangeTo'].selectedIndex];
  for (i=0;i<=3;i++){
    element_cache['course' + i].innerHTML = rates[i] / rate_for_selected;
  }
  reCalc();
}

jQuery(document).ready(
  function() 
  {
    calc_elements();
    
    varTemp = '{exchangeTo}';
    if(varTemp == '') varTemp = 0;
    element_cache['exchangeTo'].selectedIndex = varTemp;

    if (originalColor == undefined)
      originalColor = element_cache['spend0'].style.backgroundColor;
  
    reCourse();
  }
);
//--></script>
