<script type="text/javascript"><!--
var eco_market_resources = {};
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
        <th><input id="spend{resources.ID}" name="spend[{resources.ID}]" value="{resources.SPENT}" onKeyUp="javascript:reCalc();"></th>
        <th><span class="fr" id="res_left{resources.ID}"></span></th>
        <th><span class="fr" id="course{resources.ID}"></span></th>
        <script type="text/javascript"><!--
          eco_market_resources[{resources.ID}] = { avail: {resources.AVAIL}, rate: {resources.RATE} };
        //--></script>
      </tr>
    <!-- END resources -->    

    <tr><td class="c" colspan=5 align=center><input type="submit" name="exchange" value="{L_eco_mrk_trader_to}">
      <select name="exchangeTo" id="exchangeTo" onChange="javascript:reCourse();">
        <option value="{D_RES_METAL}">{L_sys_metal}
        <option value="{D_RES_CRYSTAL}">{L_sys_crystal}
        <option value="{D_RES_DEUTERIUM}">{L_sys_deuterium}
      </select></td></tr>
  </table>
</form>

<script type="text/javascript"><!--
var originalColor;

function reCalc(){
  var inz = 0;

  si = element_cache['exchangeTo'].value;
  sinp = element_cache['spend' + si];

  for (i in eco_market_resources)
  {
    inp = element_cache['spend' + i];
    inp.value = parseFloat(0 + inp.value);

    if(i == si)
    {
      inp.disabled = true;
      inp.style.backgroundColor = "#004400";
    }
    else
    {
      if (inp.value > eco_market_resources[i]['avail'])
      {
        inp.style.backgroundColor = "#FF0000";
      }
      else
      {
        inp.style.backgroundColor = originalColor;
      }
      inp.disabled = false;
      inz = inz + inp.value * eco_market_resources[i]['rate'] / eco_market_resources[si]['rate'];
      element_cache['res_left' + i].innerHTML = eco_market_resources[i]['avail'] - inp.value;
    }
  }
  sinp.value = parseFloat(inz);
  element_cache['res_left' + si].innerHTML = eco_market_resources[si]['avail'] + parseFloat(inz);
}

function reCourse()
{
  rate_for_selected = eco_market_resources[element_cache['exchangeTo'].value]['rate'];
  for (i in eco_market_resources)
  {
    element_cache['course' + i].innerHTML = eco_market_resources[i]['rate'] / rate_for_selected;
    element_cache['spend' + i].value = 0;
  }
  reCalc();
}
//--></script>

<script type="text/javascript"><!--
jQuery(document).ready(
  function() 
  {
    varTemp = '{exchangeTo}';
    if(varTemp == '') varTemp = 0;
    element_cache['exchangeTo'].value = varTemp;

    if (originalColor == undefined)
      originalColor = element_cache['spend{D_RES_METAL}'].style.backgroundColor;
  
    reCourse();
  }
);
//--></script>
