function fenster(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');
  new_win.focus();
}

function universe_popup_show(popup) {
  if($(popup['of']).attr('popup_opened_here')) {
    popup_hide();
  } else {
    popup_show(popup['html'], popup);
    $(popup['of']).attr('popup_opened_here', 1);
  }
}

var universe_popup;

function sn_universe_show_planet(that) {
  var a_parent = that.parents('.uni_planet_row');
  var planet_pos = a_parent.attr('planet_pos');
  var planet_type = that.attr('planet_type');
  if(!uni_row[planet_pos] || !planet_type || (planet_type == 3 && !parseInt(uni_row[planet_pos]['moon_diameter'])) || parseInt(uni_row[planet_pos]['planet_destroyed'])) {
    return;
  }

  var user_is_planet_owner = uni_row[planet_pos]['owner'] == user_id;
  var planet_type_string = planet_type == 1 ? 'planet' : 'moon';

  var result = jQuery('#planet_template').html()
    .replace(/\[PLANET_POS\]/g, planet_pos)
    .replace(/\[PLANET_TYPE\]/g, planet_type)
    .replace(/\[PLANET_TYPE_TEXT\]/g, language[planet_type == 1 ? 'sys_planet' : 'sys_moon'])
    .replace(/\[PLANET_TYPE_TEXT_SHORT\]/g, language['sys_' + planet_type_string + '_short'])
    .replace(/\[PLANET_NAME\]/g, uni_row[planet_pos][planet_type_string + '_name'])
    .replace(/\[PLANET_IMAGE\]/g, uni_row[planet_pos][planet_type_string + '_image'])
    .replace(/\[PLANET_DIAMETER\]/g, uni_row[planet_pos][planet_type_string + '_diameter'])
    .replace(/\[FLEET_TABLE\]/g, fleet_table_make(uni_row[planet_pos][planet_type_string + '_fleet_id']))
    .replace(/\[HIDE_PLANET_PHALANX\]/g, !user_is_planet_owner && uni_phalanx && planet_type == 1 ? '' : 'display: none;')
    .replace(/\[HIDE_PLANET_MISSILE\]/g, !user_is_planet_owner && window.uni_missiles && planet_type == 1 ? '' : 'display: none;')
    .replace(/\[HIDE_PLANET_DESTROY\]/g, !user_is_planet_owner && parseFloat(uni_death_stars) && planet_type == 3 ? '' : 'display: none;')
    .replace(/\[HIDE_PLANET_RELOCATE\]/g, user_is_planet_owner ? '' : 'display: none;')
    .replace(/\[HIDE_PLANET_SPY\]/g, user_is_planet_owner ? 'display: none;' : '')
    .replace(/\[HIDE_PLANET_ATTACK\]/g, user_is_planet_owner ? 'display: none;' : '')
    .replace(/\[HIDE_PLANET_HOLD\]/g, user_is_planet_owner ? 'display: none;' : '');

  return {html: result, my: planet_type == 3 ? 'left middle' : 'right middle', at: planet_type == 3 ? 'right middle' : 'left middle', of: that};
}

function sn_universe_show_debris(that) {
  var a_parent = that.parents('.uni_planet_row');
  var planet_pos = a_parent.attr('planet_pos');
  if(!uni_row[planet_pos] || !parseFloat(uni_row[planet_pos]['debris'])) {
    return;
  }

  var metal_debris_percent = Math.round(uni_row[planet_pos]['debris_metal'] / uni_row[planet_pos]['debris'] * 100);

  var result = jQuery('#debris_template').html()
    .replace(/\[CURRENT_PLANET\]/g, planet_pos)
    .replace('[DEBRIS]', sn_format_number(uni_row[planet_pos]['debris']))
    .replace('[DEBRIS_METAL]', sn_format_number(uni_row[planet_pos]['debris_metal']))
    .replace('[DEBRIS_METAL_PERCENT]', metal_debris_percent)
    .replace('[DEBRIS_CRYSTAL]', sn_format_number(uni_row[planet_pos]['debris_crystal']))
    .replace('[DEBRIS_CRYSTAL_PERCENT]', 100 - metal_debris_percent)
    .replace('[DEBRIS_GATHER_TOTAL]', sn_format_number(uni_row[planet_pos]['debris_gather_total']))
    .replace('[DEBRIS_GATHER_TOTAL_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_gather_total_percent']))
    .replace('[DEBRIS_RESERVED]', sn_format_number(uni_row[planet_pos]['debris_reserved']))
    .replace('[DEBRIS_RESERVED_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_reserved_percent']))
    .replace('[DEBRIS_WILL_GATHER]', sn_format_number(uni_row[planet_pos]['debris_will_gather']))
    .replace('[DEBRIS_WILL_GATHER_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_will_gather_percent']))
    .replace('[HIDE_RECYCLER_LINK]', PLANET_RECYCLERS > 0 && parseFloat(uni_row[planet_pos]['debris_will_gather']) ? '' : 'display: none;');

  return {html: result, my: 'middle bottom', at: 'middle top', of: that};
}

function sn_universe_show_user(that) {
  var a_parent = that.parents('.uni_planet_row');
  var user_id = a_parent.attr('user_id');
  if(!user_id || !users[user_id]) {
    return;
  }

  var result = jQuery('#user_template').html()
    .replace(/\[USER_ID\]/g, user_id)
    .replace(/\[USER_NAME\]/g, users[user_id]['name'])
    .replace(/\[USER_RANK\]/g, users[user_id]['rank'])
    .replace(/\[HIDE_USER_AVATAR\]/g, opt_uni_avatar_user && users[user_id]['avatar'] == 1 ? '' : 'display: none;')
    .replace(/\[USER_COLSPAN]/g, opt_uni_avatar_user && users[user_id]['avatar'] == 1 ? 2 : 1)
    .replace(/\[USER_ALLY_TAG\]/g, users[user_id]['ally_tag'])
    .replace(/\[USER_ALLY_NAME\]/g, allies[users[user_id]['ally_id']] ? allies[users[user_id]['ally_id']]['name'] : '')
    .replace(/\[USER_ALLY_TITLE\]/g, users[user_id]['ally_title'])
    .replace(/\[HIDE_USER_ALLY\]/g, users[user_id]['ally_title'] && users[user_id]['ally_title'] !== undefined ? '' : 'display: none;');

  return {html: result, my: 'left middle', at: 'right middle', of: that};
}

function sn_universe_show_ally(that) {
  var a_parent = that.parents('.uni_planet_row');
  var ally_id = a_parent.attr('ally_id');
  if(!ally_id || !allies[ally_id]) {
    return;
  }

  var result = jQuery('#ally_template').html()
    .replace(/\[ALLY_ID\]/g, ally_id)
    .replace(/\[ALLY_NAME\]/g, allies[ally_id]['name'])
    .replace(/\[ALLY_RANK\]/g, allies[ally_id]['rank'])
    .replace(/\[ALLY_MEMBERS\]/g, allies[ally_id]['members'])
    .replace(/\[HIDE_ALLY_AVATAR\]/g, opt_uni_avatar_ally && allies[ally_id]['avatar'] == 1 ? '' : 'display: none;')
    .replace(/\[ALLY_COLSPAN]/g, opt_uni_avatar_ally && allies[ally_id]['avatar'] == 1 ? 2 : 1)
    .replace(/\[ALLY_URL\]/g, allies[ally_id]['url'])
    .replace(/\[HIDE_ALLY_URL\]/g, allies[ally_id]['url'] ? '' : 'display: none');

  return {html: result, my: 'left top', at: 'right top', of: that};
}

$(function(){
  $(document).on('click mouseenter', '#universe_legend', function() {
    popup_show(jQuery('#legend_template').html(), {my: 'right top', at: 'right bottom', of: this});
  });

  $(document).on('mouseleave', '#universe_legend', function() {
    universe_popup = null;
  });

  $(document).on('mouseleave', '.uni_show_planet,.uni_show_debris,.uni_show_user,.uni_show_ally', function() {
    universe_popup = null;
  });

  $(document).on('click mouseenter', '.uni_show_planet,.uni_show_debris,.uni_show_user,.uni_show_ally', function(event) {
    that = $(this);
    universe_popup = that;
    sn_delay(function(that) {
      if(that != universe_popup) {
        return false;
      }
      result = false;
      switch(true) {
        case that.hasClass('uni_show_planet'):
          result = sn_universe_show_planet(that);
          break;
        case that.hasClass('uni_show_debris'):
          result = sn_universe_show_debris(that);
          break;
        case that.hasClass('uni_show_user'):
          result = sn_universe_show_user(that);
          break;
        case that.hasClass('uni_show_ally'):
          result = sn_universe_show_ally(that);
          break;
      }
      if(result) {
        universe_popup_show(result);
      }
    }, event.type == 'mouseenter' ? opt_uni_tooltip_time : 1, that);
  });
});

$(document).on('click', '.planet_template .ownmissile, .missile_attack_prepare', function() {
  popup_hide();
  uni_missile_planet = attr_on_me_or_parent(this, 'planet_planet');
  jQuery('#uni_missile_planet').html(uni_missile_planet);
  jQuery('#uni_missile_form').show();
  $('html, body').animate({
    scrollTop: $("#uni_missile_form").offset().top
  }, 2000);
});

$(document).on('click', '.debris_template .ownharvest', function() {
  popup_hide();
  doit(MT_RECYCLE, $(this).attr('planet_planet'), PT_DEBRIS);
});

$(document).on('click', '#galaxyLeft', function() {
  $('#galaxy').val((parseInt($('#galaxy').val()) ? parseInt($('#galaxy').val()) : 0) - 1);
  $('#galaxy_form').submit();
});

$(document).on('click', '#galaxyRight', function() {
  $('#galaxy').val((parseInt($('#galaxy').val()) ? parseInt($('#galaxy').val()) : 0) + 1);
  $('#galaxy_form').submit();
});

$(document).on('click', '#systemLeft', function() {
  $('#system').val((parseInt($('#system').val()) ? parseInt($('#system').val()) : 0) - 1);
  $('#galaxy_form').submit();
});

$(document).on('click', '#systemRight', function() {
  $('#system').val((parseInt($('#system').val()) ? parseInt($('#system').val()) : 0) + 1);
  $('#galaxy_form').submit();
});
