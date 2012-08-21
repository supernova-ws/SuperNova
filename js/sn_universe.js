function show_planet(planet, planet_type)
{
  if(!uni_row[planet]['cache_planet' + planet_type])
  {
    result = jQuery('#planet_template').html();
    result = result.replace(/\[PLANET_POS\]/g, planet);
    result = result.replace(/\[PLANET_TYPE\]/g, planet_type);
    result = result.replace(/\[PLANET_TYPE_TEXT\]/g, language[planet_type == 1 ? 'sys_planet' : 'sys_moon']);
    result = result.replace(/\[PLANET_NAME\]/g, uni_row[planet][planet_type == 1 ? 'planet_name' : 'moon_name']);
    result = result.replace(/\[PLANET_IMAGE\]/g, uni_row[planet][planet_type == 1 ? 'planet_image' :'moon_image']);
    result = result.replace(/\[PLANET_DIAMETER\]/g, uni_row[planet][planet_type == 1 ? 'planet_diameter' : 'moon_diameter']);

    result = result.replace(/\[FLEET_TABLE\]/g, fleet_table_make(uni_row[planet][planet_type == 1 ? 'planet_fleet_id' : 'moon_fleet_id']));

    if(uni_row[planet]['owner'] != user_id && uni_phalanx && planet_type == 1)
    {
      result = result.replace(/\[HIDE_PLANET_PHALANX\]/g, '');
    }
    else
    {
      result = result.replace(/\[HIDE_PLANET_PHALANX\]/g, 'display: none;');
    }

    if(uni_row[planet]['owner'] != user_id && parseFloat(uni_death_stars) && planet_type == 3)
    {
      result = result.replace(/\[HIDE_PLANET_DESTROY\]/g, '');
    }
    else
    {
      result = result.replace(/\[HIDE_PLANET_DESTROY\]/g, 'display: none;');
    }

    if(uni_row[planet]['owner'] == user_id)
    {
      result = result.replace(/\[HIDE_PLANET_RELOCATE\]/g, '');
      result = result.replace(/\[HIDE_PLANET_SPY\]/g, 'display: none;');
      result = result.replace(/\[HIDE_PLANET_ATTACK\]/g, 'display: none;');
      result = result.replace(/\[HIDE_PLANET_HOLD\]/g, 'display: none;');
    }
    else
    {
      result = result.replace(/\[HIDE_PLANET_RELOCATE\]/g, 'display: none;');
      result = result.replace(/\[HIDE_PLANET_SPY\]/g, '');
      result = result.replace(/\[HIDE_PLANET_ATTACK\]/g, '');
      result = result.replace(/\[HIDE_PLANET_HOLD\]/g, '');
    };

    uni_row[planet]['cache_planet' + planet_type] = result;
  }

  popup_show(uni_row[planet]['cache_planet' + planet_type], 240);
}

function show_debris(planet)
{
  if(!uni_row[planet]['cache_debris'])
  {
    var metal_debris_percent = Math.round(uni_row[planet]['debris_metal'] / uni_row[planet]['debris'] * 100);

    result = jQuery('#debris_template').html();
    result = result.replace(/\[CURRENT_PLANET\]/g, planet);
    result = result.replace('[DEBRIS]', sn_format_number(uni_row[planet]['debris']));
    result = result.replace('[DEBRIS_METAL]', sn_format_number(uni_row[planet]['debris_metal']));
    result = result.replace('[DEBRIS_METAL_PERCENT]', metal_debris_percent);
    result = result.replace('[DEBRIS_CRYSTAL]', sn_format_number(uni_row[planet]['debris_crystal']));
    result = result.replace('[DEBRIS_CRYSTAL_PERCENT]', 100 - metal_debris_percent);

    result = result.replace('[DEBRIS_GATHER_TOTAL]', sn_format_number(uni_row[planet]['debris_gather_total']));
    result = result.replace('[DEBRIS_GATHER_TOTAL_PERCENT]', sn_format_number(uni_row[planet]['debris_gather_total_percent']));

    result = result.replace('[DEBRIS_RESERVED]', sn_format_number(uni_row[planet]['debris_reserved']));
    result = result.replace('[DEBRIS_RESERVED_PERCENT]', sn_format_number(uni_row[planet]['debris_reserved_percent']));

    result = result.replace('[DEBRIS_WILL_GATHER]', sn_format_number(uni_row[planet]['debris_will_gather']));
    result = result.replace('[DEBRIS_WILL_GATHER_PERCENT]', sn_format_number(uni_row[planet]['debris_will_gather_percent']));
    if(PLANET_RECYCLERS > 0 && parseFloat(uni_row[planet]['debris_will_gather_percent']))
    {
      result = result.replace('[HIDE_RECYCLER_LINK]', '');
    }
    else
    {
      result = result.replace('[HIDE_RECYCLER_LINK]', 'display: none;');
    }
    uni_row[planet]['cache_debris'] = result;
  }
  popup_show(uni_row[planet]['cache_debris'], 400);
}

function show_user(id)
{
  if(!users[id]['cache'])
  {
    result = jQuery('#user_template').html();
    result = result.replace(/\[USER_ID\]/g, id);
    result = result.replace(/\[USER_NAME\]/g, users[id]['name']);
    result = result.replace(/\[USER_RANK\]/g, users[id]['rank']);

    if(opt_uni_avatar_user && users[id]['avatar'] == 1)
    {
      result = result.replace(/\[HIDE_USER_AVATAR\]/g, '');
    }
    else
    {
      result = result.replace(/\[HIDE_USER_AVATAR\]/g, 'display: none;');
    }

    if(users[id]['ally_title'] && users[id]['ally_title'] != undefined)
    {
      result = result.replace(/\[USER_ALLY_TITLE\]/g, users[id]['ally_title']);
      result = result.replace(/\[HIDE_USER_ALLY\]/g, '');
    }
    else
    {
      result = result.replace(/\[HIDE_USER_ALLY\]/g, 'display: none;');
    }

    users[id]['cache'] = result;
  }

  popup_show(users[id]['cache']);
}

function show_alliance(id)
{
  if(!allies[id]['cache'])
  {
    result = jQuery('#ally_template').html();
    result = result.replace(/\[ALLY_ID\]/g, id);
    result = result.replace(/\[ALLY_NAME\]/g, allies[id]['name']);
    result = result.replace(/\[ALLY_RANK\]/g, allies[id]['rank']);
    result = result.replace(/\[ALLY_MEMBERS\]/g, allies[id]['members']);

    if(opt_uni_avatar_ally && allies[id]['avatar'] == 1)
    {
      result = result.replace(/\[HIDE_ALLY_AVATAR\]/g, '');
    }
    else
    {
      result = result.replace(/\[HIDE_ALLY_AVATAR\]/g, 'display: none;');
    }

    if(allies[id]['url'])
    {
      result = result.replace(/\[HIDE_ALLY_URL\]/g, '');
      result = result.replace(/\[ALLY_URL\]/g, allies[id]['url']);
    }
    else
    {
      result = result.replace(/\[HIDE_ALLY_URL\]/g, 'display: none');
    }

    allies[id]['cache'] = result;
  }

  popup_show(allies[id]['cache']);
}

function galaxy_submit(value)
{
  document.getElementById('auto').name = value;
  document.getElementById('galaxy_form').submit();
}

function fenster(target_url,win_name)
{
  var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');
  new_win.focus();
}

function makeAHREF(planet, planet_type, mission, mission_name){
  return '<a href=fleet.php?galaxy=' + uni_galaxy + '&system=' + uni_system + '&planet=' + planet +
    '&planettype=' + planet_type + '&target_mission=' + mission + '>' + mission_name + '</a><br />';
}
