function fenster(target_url,win_name) {
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');
  new_win.focus();
}

function universe_popup_show(popup) {
  if($(popup['of']).attr('popup_opened_here')) {
    popup_hide();
  } else {
    popup_show(popup['html'], 'auto', 0, 0, popup);
    $(popup['of']).attr('popup_opened_here', 1);
  }
}

var universe_popup;

function sn_universe_show_planet(that) {
  planet_pos = that.parent().attr('planet_pos');
  planet_type = that.attr('planet_type');
  if(!uni_row[planet_pos] || !planet_type || (planet_type == 3 && !parseInt(uni_row[planet_pos]['moon_diameter'])) || parseInt(uni_row[planet_pos]['planet_destroyed'])) {
    return;
  }

  result = jQuery('#planet_template').html();
  result = result.replace(/\[PLANET_POS\]/g, planet_pos);
  result = result.replace(/\[PLANET_TYPE\]/g, planet_type);
  result = result.replace(/\[PLANET_TYPE_TEXT\]/g, language[planet_type == 1 ? 'sys_planet' : 'sys_moon']);
  result = result.replace(/\[PLANET_NAME\]/g, uni_row[planet_pos][planet_type == 1 ? 'planet_name' : 'moon_name']);
  result = result.replace(/\[PLANET_IMAGE\]/g, uni_row[planet_pos][planet_type == 1 ? 'planet_image' :'moon_image']);
  result = result.replace(/\[PLANET_DIAMETER\]/g, uni_row[planet_pos][planet_type == 1 ? 'planet_diameter' : 'moon_diameter']);

  result = result.replace(/\[FLEET_TABLE\]/g, fleet_table_make(uni_row[planet_pos][planet_type == 1 ? 'planet_fleet_id' : 'moon_fleet_id']));

  result = result.replace(/\[HIDE_PLANET_PHALANX\]/g,
    uni_row[planet_pos]['owner'] != user_id && uni_phalanx && planet_type == 1 ? '' : 'display: none;');

  result = result.replace(/\[HIDE_PLANET_DESTROY\]/g,
    uni_row[planet_pos]['owner'] != user_id && parseFloat(uni_death_stars) && planet_type == 3 ? '' : 'display: none;');

  if(uni_row[planet_pos]['owner'] == user_id) {
    result = result.replace(/\[HIDE_PLANET_RELOCATE\]/g, '');
    result = result.replace(/\[HIDE_PLANET_SPY\]/g, 'display: none;');
    result = result.replace(/\[HIDE_PLANET_ATTACK\]/g, 'display: none;');
    result = result.replace(/\[HIDE_PLANET_HOLD\]/g, 'display: none;');
  } else {
    result = result.replace(/\[HIDE_PLANET_RELOCATE\]/g, 'display: none;');
    result = result.replace(/\[HIDE_PLANET_SPY\]/g, '');
    result = result.replace(/\[HIDE_PLANET_ATTACK\]/g, '');
    result = result.replace(/\[HIDE_PLANET_HOLD\]/g, '');
  }

  return {html: result, my: planet_type == 3 ? 'right top' : 'left top', at: planet_type == 3 ? 'left top' : 'right top', of: that};
}

function sn_universe_show_debris(that) {
  planet_pos = that.parent().attr('planet_pos');
  if(!uni_row[planet_pos] || !parseFloat(uni_row[planet_pos]['debris'])) {
    return;
  }

  var metal_debris_percent = Math.round(uni_row[planet_pos]['debris_metal'] / uni_row[planet_pos]['debris'] * 100);

  result = jQuery('#debris_template').html();
  result = result.replace(/\[CURRENT_PLANET\]/g, planet_pos);
  result = result.replace('[DEBRIS]', sn_format_number(uni_row[planet_pos]['debris']));
  result = result.replace('[DEBRIS_METAL]', sn_format_number(uni_row[planet_pos]['debris_metal']));
  result = result.replace('[DEBRIS_METAL_PERCENT]', metal_debris_percent);
  result = result.replace('[DEBRIS_CRYSTAL]', sn_format_number(uni_row[planet_pos]['debris_crystal']));
  result = result.replace('[DEBRIS_CRYSTAL_PERCENT]', 100 - metal_debris_percent);

  result = result.replace('[DEBRIS_GATHER_TOTAL]', sn_format_number(uni_row[planet_pos]['debris_gather_total']));
  result = result.replace('[DEBRIS_GATHER_TOTAL_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_gather_total_percent']));

  result = result.replace('[DEBRIS_RESERVED]', sn_format_number(uni_row[planet_pos]['debris_reserved']));
  result = result.replace('[DEBRIS_RESERVED_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_reserved_percent']));

  result = result.replace('[DEBRIS_WILL_GATHER]', sn_format_number(uni_row[planet_pos]['debris_will_gather']));
  result = result.replace('[DEBRIS_WILL_GATHER_PERCENT]', sn_format_number(uni_row[planet_pos]['debris_will_gather_percent']));

  result = result.replace('[HIDE_RECYCLER_LINK]',
    PLANET_RECYCLERS > 0 && parseFloat(uni_row[planet_pos]['debris_will_gather']) ? '' : 'display: none;');

  return {html: result, my: 'middle bottom', at: 'middle top', of: that};
}

function sn_universe_show_user(that) {
  id = that.parent().attr('user_id');
  if(!id || !users[id]) {
    return;
  }

  result = jQuery('#user_template').html();
  result = result.replace(/\[USER_ID\]/g, id);
  result = result.replace(/\[USER_NAME\]/g, users[id]['name']);
  result = result.replace(/\[USER_RANK\]/g, users[id]['rank']);

  result = result.replace(/\[HIDE_USER_AVATAR\]/g, opt_uni_avatar_user && users[id]['avatar'] == 1 ? '' : 'display: none;');
  result = result.replace(/\[USER_COLSPAN]/g, opt_uni_avatar_user && users[id]['avatar'] == 1 ? 2 : 1);

  result = result.replace(/\[USER_ALLY_TAG\]/g, users[id]['ally_tag']);
  if(allies[users[id]['ally_id']]) {
    result = result.replace(/\[USER_ALLY_NAME\]/g, allies[users[id]['ally_id']]['name']);
  }
  result = result.replace(/\[USER_ALLY_TITLE\]/g, users[id]['ally_title']);
  result = result.replace(/\[HIDE_USER_ALLY\]/g,
    users[id]['ally_title'] && users[id]['ally_title'] != undefined ? '' : 'display: none;');

  return {html: result, my: 'right top', at: 'left top', of: that};
}

function sn_universe_show_ally(that) {
  id = that.parent().attr('ally_id');
  if(!id || !allies[id]) {
    return;
  }

  result = jQuery('#ally_template').html();
  result = result.replace(/\[ALLY_ID\]/g, id);
  result = result.replace(/\[ALLY_NAME\]/g, allies[id]['name']);
  result = result.replace(/\[ALLY_RANK\]/g, allies[id]['rank']);
  result = result.replace(/\[ALLY_MEMBERS\]/g, allies[id]['members']);

  result = result.replace(/\[HIDE_ALLY_AVATAR\]/g, opt_uni_avatar_ally && allies[id]['avatar'] == 1 ? '' : 'display: none;');
  result = result.replace(/\[ALLY_COLSPAN]/g, opt_uni_avatar_ally && allies[id]['avatar'] == 1 ? 2 : 1);
  result = result.replace(/\[ALLY_URL\]/g, allies[id]['url']);
  result = result.replace(/\[HIDE_ALLY_URL\]/g, allies[id]['url'] ? '' : 'display: none');

  return {html: result, my: 'right top', at: 'left top', of: that};
}

$(function(){
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
