/**
 * Created by Gorlum on 14.10.2015.
 */

/* ---- Страница выбора услуги */
//jQuery(document).on('click', '.market_services a', function(){
//  $(this).addClass('button_pseudo_pressed');
//});

var eco_mrk_trader_recalc_lock = false;

/* ---- Страница обмена ресурсов */
function eco_mrk_trader_recalc() {
  if(eco_mrk_trader_recalc_lock) {
    return;
  }

  selected_resource_id = parseInt($('input:radio[name=exchangeTo]:checked').val());

  var resource_increase = 0;
  var block_exchange = false;
  var selected_input = $('#spend' + selected_resource_id);
  var selected_input_value = parseFloat(selected_input.val())
  !(selected_input_value) ? selected_input_value = 0 : false;

  var current_element, input_value;
  for(var i in eco_market_resources) {
    if(!eco_market_resources.hasOwnProperty(i)) {
      continue;
    }

    current_element = $('#spend' + i);
    !(input_value = parseFloat(current_element.val())) ? input_value = 0 : false;

    if((selected_resource_id != RES_DARK_MATTER && i == selected_resource_id) || (selected_resource_id == RES_DARK_MATTER && i != RES_DARK_MATTER)) {
      current_element.addClass('ok_bg');
      jQuery('[id^="' + 'spend' + i +'"]').prop('disabled', true);
      jQuery('#' + 'spend' + i +'slide').slider('disable');
    } else {
      jQuery('[id^="' + 'spend' + i +'"]').prop('disabled', false);
      jQuery('#' + 'spend' + i +'slide').slider('enable');
      if(input_value > eco_market_resources[i]['avail']) {
        current_element.addClass('error_bg');
        block_exchange = true;
      } else {
        current_element.removeClass('ok_bg').removeClass('error_bg');
      }
    }

    if(selected_resource_id == RES_DARK_MATTER && i != RES_DARK_MATTER) {
      input_value = - parseFloat(selected_input_value / 3 / eco_market_resources[i]['rate'] * eco_market_resources[selected_resource_id]['rate']);
    }

    if(selected_resource_id != RES_DARK_MATTER && i != selected_resource_id) {
      resource_increase += Math.floor(parseFloat(input_value * eco_market_resources[i]['rate'] / eco_market_resources[selected_resource_id]['rate']));
    }
    input_value = Math.floor(input_value);
    $('#res_left' + i).html(sn_format_number(eco_market_resources[i]['avail'] - input_value));
    $('#res_delta' + i).html(sn_format_number(-input_value, 0, 'positive', 0, true));
    current_element.val(Math.abs(input_value));
  }

  if(selected_resource_id == RES_DARK_MATTER) {
    $('#res_delta' + selected_resource_id).html(sn_format_number(-selected_input_value, 0, 'positive', 0, true));
    $('#res_left' + selected_resource_id).html(sn_format_number(eco_market_resources[selected_resource_id]['avail'] - selected_input_value));
    block_exchange = block_exchange || !selected_input_value;
  } else {
    selected_input.val(resource_increase);
    $('#res_delta' + selected_resource_id).html(sn_format_number(resource_increase, 0, 'positive', 0, true));
    $('#res_left' + selected_resource_id).html(sn_format_number(eco_market_resources[selected_resource_id]['avail'] + resource_increase));
    block_exchange = block_exchange || !resource_increase;
  }
  $('#submit_trade').prop('disabled', block_exchange);
}

function eco_mrk_trader_recourse(selected_resource) {
  var operation_cost, current_rate;

  selected_resource = parseInt(selected_resource);

  $('#market_trader').find('.button_pseudo').removeClass('button_pseudo_pressed');
  $('[resource_id=' + selected_resource + ']').addClass('button_pseudo_pressed');
  $('input:radio[name=exchangeTo]').val([selected_resource]);

  var rate_for_selected = eco_market_resources[selected_resource]['rate'];

  eco_mrk_trader_recalc_lock = true;

  for(var i in eco_market_resources) {
    if(!eco_market_resources.hasOwnProperty(i)) {
      continue;
    }

    current_rate = eco_market_resources[i]['rate'] / rate_for_selected;
    selected_resource == RES_DARK_MATTER ? current_rate = "1 : " + (1 / current_rate) : (current_rate += " : 1");
    $('#course' + i).html(current_rate);
    jQuery('#' + 'spend' + i +'slide').slider("value", 0);
  }
  operation_cost = C_rpg_cost_trader * (selected_resource == RES_DARK_MATTER ? 3 : 1);
  eco_market_resources[RES_DARK_MATTER]['avail'] = eco_market_resources[RES_DARK_MATTER]['start'] - operation_cost;
  $('#rpg_cost_trader').html(operation_cost);

  eco_mrk_trader_recalc_lock = false;

  eco_mrk_trader_recalc();
}

$(document).on('click', '#submit_trade', function(){
  var confirm = true, dm_amount;

  if(dm_amount = parseFloat(jQuery('#spend' + RES_DARK_MATTER).val())) {
    confirm = window.confirm(language['LA_eco_mrk_trader_exchange_dm_confirm'].format(sn_format_number(dm_amount)));
  }

  if(confirm) {
    $(this).prop('disabled', true);
    $('#form_trade').submit();
  }
  return confirm;
});

jQuery(document).on('click', '#market_trader .button_pseudo', function() {
  eco_mrk_trader_recourse($(this).attr('resource_id'));
});

jQuery(document).ready(function() {
  // Запускается только на странице обмена ресурсов
  if($('#market_trader').length) {
    eco_mrk_trader_recourse(exchange_to_resource_id ? exchange_to_resource_id : RES_METAL);
  }
});
