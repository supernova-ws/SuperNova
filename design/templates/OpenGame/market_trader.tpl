<script type="text/javascript"><!--
var eco_market_resources = {};

function eco_mrk_recalc(){
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
        inp.style.backgroundColor = original_color;
      }
      inp.disabled = false;
      inz = inz + inp.value * eco_market_resources[i]['rate'] / eco_market_resources[si]['rate'];
      element_cache['res_left' + i].innerHTML = sn_format_number(eco_market_resources[i]['avail'] - inp.value);
    }
  }
  sinp.value = parseFloat(inz);
  element_cache['res_left' + si].innerHTML = sn_format_number(eco_market_resources[si]['avail'] + parseFloat(inz));
}

function eco_mrk_recourse()
{
  rate_for_selected = eco_market_resources[element_cache['exchangeTo'].value]['rate'];
  for (i in eco_market_resources)
  {
    element_cache['course' + i].innerHTML = eco_market_resources[i]['rate'] / rate_for_selected + " : 1";
    element_cache['spend' + i].value = 0;
  }
  eco_mrk_recalc();
}
//--></script>
<h2>{L_eco_mrk_title}:&nbsp;{L_eco_mrk_trader}</h2>
{message}
<form name="form_trade" action="market.php?mode={D_MARKET_RESOURCES}" method="POST">
  <table>
    <tr class="c_c">
      <th>{L_sys_resources}</th>
      <th>{L_fl_on_stores}</th>
      <th>{L_eco_mrk_trader_exchange}</th>
      <th>{L_eco_mrk_trader_left}</th>
      <th>{L_eco_mrk_trader_course}</th>
    </tr>

    <!-- BEGIN resources -->    
      <tr>
        <th>{resources.NAME}</th>
        <th><span class="fr">{resources.AVAIL_TEXT}</span></th>
        <th><input id="spend{resources.ID}" name="spend[{resources.ID}]" value="{resources.SPENT}" onKeyUp="javascript:eco_mrk_recalc();"></th>
        <th><span class="fr" id="res_left{resources.ID}"></span></th>
        <th><span class="fr" id="course{resources.ID}"></span></th>
        <script type="text/javascript"><!--
          eco_market_resources[{resources.ID}] = { avail: {resources.AVAIL}, rate: {resources.RATE} };
        //--></script>
      </tr>
    <!-- END resources -->    

    <tr><th class="c_c" colspan=5 align=center>
	   <div class="fl">{L_eco_mrk_service_cost} {C_rpg_cost_trader} {L_eco_mrk_dark_matter_short}</div>    
	   <div class="fr">
        <input type="submit" id="submit_trade" name="exchange" value="{L_eco_mrk_trader_to}" onclick = "document.form_trade.exchange.disabled = true; document.form_trade.submit();">
        <select name="exchangeTo" id="exchangeTo" onChange="javascript:eco_mrk_recourse();">
          <option value="{D_RES_METAL}">{L_sys_metal}
          <option value="{D_RES_CRYSTAL}">{L_sys_crystal}
          <option value="{D_RES_DEUTERIUM}">{L_sys_deuterium}
        </select>
	   </div>
	   
    
    </th></tr>
  </table>
</form>

<script type="text/javascript"><!--
var original_color;
jQuery(document).ready(
  function() 
  {
    varTemp = '{exchangeTo}';
    if(varTemp == '') varTemp = 0;
    element_cache['exchangeTo'].value = varTemp;

    if (original_color == undefined)
      original_color = element_cache['spend{D_RES_METAL}'].style.backgroundColor;
  
    eco_mrk_recourse();
  }
);
//--></script>
