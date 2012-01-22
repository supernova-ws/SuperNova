<script type="text/javascript"><!--
var eco_market_resources = {};
var res_resource_dm = {D_RES_DARK_MATTER};
var rpg_cost_trader = {C_rpg_cost_trader};

function eco_mrk_recalc()
{
  var resource_increase = 0;
  var block_exchange = false;

  selected_resource = element_cache['exchangeTo'].value;
  selected_input = element_cache['spend' + selected_resource];
  selected_input_value = parseFloat(0 + selected_input.value);

  for(i in eco_market_resources)
  {
    input_element = element_cache['spend' + i];
    input_element.value = input_value = parseFloat(0 + input_element.value);

    if((selected_resource != res_resource_dm && i == selected_resource) || (selected_resource == res_resource_dm && i != res_resource_dm))
    {
      input_element.disabled = true;
      input_element.style.backgroundColor = "#004400";
    }
    else
    {
      input_element.disabled = false;
      if(input_value > eco_market_resources[i]['avail'])
      {
        input_element.style.backgroundColor = "#FF0000";
        block_exchange = true;
      }
      else
      {
        input_element.style.backgroundColor = original_color;
      }
    }

    if(selected_resource == res_resource_dm && i != res_resource_dm)
    {
      input_value = - parseFloat(selected_input_value / 3 / eco_market_resources[i]['rate'] * eco_market_resources[selected_resource]['rate']);
    }

    if(selected_resource != res_resource_dm && i != selected_resource)
    {
      resource_increase += Math.floor(parseFloat(input_value * eco_market_resources[i]['rate'] / eco_market_resources[selected_resource]['rate']));
    }
    input_value = Math.floor(input_value);
    element_cache['res_left' + i].innerHTML = sn_format_number(eco_market_resources[i]['avail'] - input_value);
    input_element.value = Math.abs(input_value);
  }
  if(selected_resource == res_resource_dm)
  {
    selected_input.value = selected_input_value;
    element_cache['res_left' + selected_resource].innerHTML = sn_format_number(eco_market_resources[selected_resource]['avail'] - selected_input_value);
  }
  else
  {
    selected_input.value = resource_increase;
    element_cache['res_left' + selected_resource].innerHTML = sn_format_number(eco_market_resources[selected_resource]['avail'] + parseFloat(resource_increase));
  }
  element_cache['submit_trade'].disabled = block_exchange;
}

function eco_mrk_recourse()
{
  var selected_resource = element_cache['exchangeTo'].value;
  var rate_for_selected = eco_market_resources[selected_resource]['rate'];

  for(i in eco_market_resources)
  {
    if(selected_resource == res_resource_dm)
    {
      element_cache['course' + i].innerHTML = "1 : " + rate_for_selected / eco_market_resources[i]['rate'];
    }
    else
    {
      element_cache['course' + i].innerHTML = eco_market_resources[i]['rate'] / rate_for_selected + " : 1";
    }
    element_cache['spend' + i].value = 0;
  }
  eco_market_resources[res_resource_dm]['avail'] += parseFloat(element_cache['rpg_cost_trader'].innerHTML);
  element_cache['rpg_cost_trader'].innerHTML = rpg_cost_trader * (selected_resource == res_resource_dm ? 3 : 1);
  eco_market_resources[res_resource_dm]['avail'] -= parseFloat(element_cache['rpg_cost_trader'].innerHTML);

  eco_mrk_recalc();
}
//--></script>
<span id="debug"></span>
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
     <div class="fl">{L_eco_mrk_service_cost} <span id="rpg_cost_trader">{C_rpg_cost_trader}</span> {L_eco_mrk_dark_matter_short}</div>    
     <div class="fr">
        <input type="submit" id="submit_trade" name="exchange" value="{L_eco_mrk_trader_to}" onclick = "document.form_trade.exchange.disabled = true; document.form_trade.submit();">
        <select name="exchangeTo" id="exchangeTo" onChange="javascript:eco_mrk_recourse();">
          <option value="{D_RES_METAL}">{L_sys_metal}
          <option value="{D_RES_CRYSTAL}">{L_sys_crystal}
          <option value="{D_RES_DEUTERIUM}">{L_sys_deuterium}
          <option value="{D_RES_DARK_MATTER}">{L_eco_mrk_trader_resources_all}
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
//    varTemp = '{D_RES_DARK_MATTER}';
    if(varTemp == '') varTemp = 0;
    element_cache['exchangeTo'].value = varTemp;

    if (original_color == undefined)
    {
      original_color = element_cache['spend{D_RES_METAL}'].style.backgroundColor;
    }

    eco_mrk_recourse();
  }
);
//--></script>
