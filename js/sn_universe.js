function show_user(id)
{
  if(!users[id]['cache'])
  {
    var result = "<table><tr>";
    if(opt_uni_avatar_user && users[id]['avatar'] == 1)
    {
      result += "<td rowspan=\"4\"><img src=\"" + sn_path_prefix + "images/avatar/avatar_" + id + ".png\" height=75 width=75 /></td>";
    }
    result += "<td class=c><center>";
    result += language['sys_player'] + "&nbsp;" + users[id]['name'] + /*"<img src=\"" + dpath + "images/sex_" + (users[id]['sex'] == 'female' ? 'female' : 'male') + ".png\" />" + */"<br>";
    if(users[id]['ally_title'] && users[id]['ally_title'] != undefined)
    {
//      result += language['place'] + "&nbsp;" + users[id]['ally_title'] + "/" + game_user_count + "<br>";
      result += users[id]['ally_title'] + /*"[" + users[id]['ally_tag'] + "]" + */"<br>";
    }
    result += language['place'] + "&nbsp;" + users[id]['rank'] + "/" + game_user_count;
    result += "</center></td></tr>";

    if (id != user_id)
    {
      result = result + "<tr><th><a href=messages.php?mode=write&id=" + id + ">" + language['gl_sendmess'] + "</a></th></tr>";
      result = result + "<tr><th><a href=buddy.php?a=2&u=" + id + ">" + language['gl_buddyreq'] + "</a></th></tr>";
    }
    result = result + "<tr><th><a href=\"stat.php?who=1&range=" + users[id]['rank'] + "#" + users[id]['rank'] + "\">" + language['gl_stats'] + "</a></th></tr></table>";

    users[id]['cache'] = result;
  }
  else
  {
    result = users[id]['cache'];
  }

  popup_show(result);
}

function show_alliance(id)
{
  if(!allies[id]['cache'])
  {
    var result = "<table><tr>";
    if(opt_uni_avatar_ally && allies[id]['avatar'] == 1)
    {
      result += "<td rowspan=\"4\"><img src=\"" + sn_path_prefix + "images/avatar/ally_" + id + ".png\" width=75 /></td>";
    }
    result += "<td class=c><center>" + language['sys_alliance'] + "&nbsp;" + allies[id]['name'];
    result += "<br>" + language['place'] + "&nbsp;" + allies[id]['rank'] + "/" + game_ally_count;
    result += "<br>" + language['gal_sys_members'] + allies[id]['members'];
    result += "</center></td></tr>";
    result += "<tr><th><a href=alliance.php?mode=ainfo&a=" + id + ">" + language['gl_ally_internal'] + "</a></th></tr>";
    result += "<tr><th><a href=\"stat.php?range=" + allies[id]['rank'] + "&who=2#" + allies[id]['rank'] + "\">" + language['gl_stats'] + "</a></th></tr>";
    if (allies[id]['url'])
    {
      result += "<tr><th><a href=" + allies[id]['url'] + " target=_new>" + language['gl_ally_web'] + "</th></tr>";
    }
    result += "</table>";

    allies[id]['cache'] = result;
  }

  popup_show(allies[id]['cache']);
}

function show_debris(planet)
{
  if(!uni_row[planet]['cache_debris'])
  {
    result = jQuery('#debris_template').html();

    var metal_debris_percent = Math.round(uni_row[planet]['debris_metal'] / uni_row[planet]['debris'] * 100);
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
/*
      result = result.replace('[RECYCLE_LINK]', '<tr><th class="c_c" colspan="4" style="cursor:pointer" onclick="doit(' + MT_RECYCLE + ',' + planet + ',2);">[ ' + language['uni_recyclers_send'] + ' ]</th></tr>');
*/
/*
    else
    {
      result = result.replace('[RECYCLE_LINK]', '');
    }
*/
/*
    var result = '<table>';
    result += '<tr><th class=c_l colspan=4>' + language['debris'] + ' [' + uni_galaxy + ':' + uni_system + ':' + planet + ']</th></tr>';
    result += '<tr><td class=c_c rowspan=7><img src="' + dpath + 'planeten/debris.jpg" height="75px" width="75px" /></td></tr>';
    // debris_incoming
    result += '<tr><th class="c_l">' + language['gl_ressource'] + '</th><th class=c_r>' + sn_format_number(uni_row[planet]['debris']) + '</th>' + '<th class="c_r">100%</th>' + '</tr>';
    result += '<tr><td class="c_l">' + language['sys_metal'] + '</td><td class=c_r>' + sn_format_number(uni_row[planet]['debris_metal']) + '</td>' + '<td class="c_r">' + metal_debris_percent + '%</td>' + '</tr>';
    result += '<tr><td class="c_l">' + language['sys_crystal'] + '</td><td class=c_r>' + sn_format_number(uni_row[planet]['debris_crystal']) + '</td>' + '<td class="c_r">' + (100 - metal_debris_percent) + '%</td>' + '</tr>';
    result += '<tr><th class="c_l">' + language['uni_debris_recyclable'] + '</th><th class=c_r>' + sn_format_number(uni_row[planet]['debris_gather_total']) + '</th>' + '<th class="c_r">' + uni_row[planet]['debris_gather_total_percent'] + '%</th>' + '</tr>';
    result += '<tr><td class="c_l">' + language['uni_debris_incoming_recyclers'] + '</td><td class="c_r">' + sn_format_number(uni_row[planet]['debris_reserved']) + '</td>' + '<td class="c_r">' + uni_row[planet]['debris_reserved_percent'] + '%</td>' + '</tr>';
    result += '<tr><td class="c_l">' + language['uni_debris_on_planet'] + '</td><td class="c_r">' + sn_format_number(uni_row[planet]['debris_will_gather']) + '</td>' + '<td class="c_r">' + uni_row[planet]['debris_will_gather_percent'] + '%</td>' + '</tr>';

    result += '<tr><th class="c_c" colspan="4">';
    if(PLANET_RECYCLERS > 0 && parseFloat(uni_row[planet]['debris_will_gather_percent']))
    {
      result += '<span style="cursor:pointer"  onclick="doit(' + MT_RECYCLE + ',' + planet + ',2);">[ ' + language['uni_recyclers_send'] + ' ]</span><br>';
    }
//    result += language['lang_recyclers'] + ': ' + (uni_row[planet]['debris_reserved_percent'] > 0 ? '<span class="neutral">' + uni_row[planet]['debris_reserved_percent'] + "+</span>": "") + uni_row[planet]['debris_will_gather_percent'] + "/" + uni_row[planet]['debris_gather_total_percent']
    result += "</th></tr>";
    result += "</table>";
*/
    uni_row[planet]['cache_debris'] = result;
  }
/*
  else
  {
    result = uni_row[planet]['cache_debris'];
  }

  popup_show(result);
*/
  popup_show(uni_row[planet]['cache_debris'], 400);
}

function makeAHREF(planet, planet_type, mission, mission_name){
  return '<a href=fleet.php?galaxy=' + uni_galaxy + '&system=' + uni_system + '&planet=' + planet +
    '&planettype=' + planet_type + '&target_mission=' + mission + '>' + mission_name + '</a><br />';
}

function show_planet(planet, planet_type)
{
  if(!uni_row[planet]['cache_planet' + planet_type])
  {
    var fleet_table;
    var result = '<table width=240><tr><td class=c colspan=2>';

    if(planet_type == 1)
    {
      result += language['sys_planet'];
      diameter = '';
      planet_image = uni_row[planet]['planet_image'];
      name = uni_row[planet]['planet_name'];
      fleet_table = fleet_table_make(uni_row[planet]['planet_fleet_id']);
    }
    else
    {
      result += language['sys_moon'];
      diameter = '<div>' + uni_row[planet]['moon_diameter'] + '</div>';
      planet_image = uni_row[planet]['moon_image'];
      name = uni_row[planet]['moon_name'];
      fleet_table = fleet_table_make(uni_row[planet]['moon_fleet_id']);
    }

    result += '&nbsp;' + name + '&nbsp;[' + uni_galaxy + ':' + uni_system + ':' + planet + ']</td></tr>';
    result += '<tr><th width=75><img src=' + dpath + 'planeten/small/s_' + planet_image + '.jpg height=75 width=75 />' + diameter + '</th><th align=center>';

    if(uni_row[planet]['owner'] == user_id)
    {
      result += makeAHREF(planet, planet_type, 4, language['type_mission4']);
    }
    else
    {
      if(uni_phalanx && planet_type == 1)
      {
        result += '<span style="cursor:pointer" onclick=fenster("phalanx.php?galaxy=' + uni_galaxy + '&system=' + uni_system + '&planet=' + planet + '&planettype=' + planet_type + '")>' + language['gl_phalanx'] + '</span><br />';
      }

      result += '<span style="cursor:pointer" onclick="doit(' + MT_SPY + ', ' + planet + ', ' + planet_type + ', ' + uni_spies + ');">' + language['type_mission6'] + '</span><br /><br />';
      result += makeAHREF(planet, planet_type, 1, language['type_mission1']);
      result += makeAHREF(planet, planet_type, 5, language['type_mission5']);

      if (uni_death_stars && planet_type == 3)
      {
        result += '<br />' + makeAHREF(planet, planet_type, 9, language['type_mission9']);
      }
    };
    result += '<br>' + makeAHREF(planet, planet_type, 3, language['type_mission3']) + '</th></tr></table>';

    if(fleet_table)
    {
      result += '' + fleet_table + '';
    }

    uni_row[planet]['cache_planet' + planet_type] = result;
  }

  popup_show(uni_row[planet]['cache_planet' + planet_type]);
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
