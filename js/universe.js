function show_user(id)
{
  if(!users[id]['cache'])
  {
    var result = "<table width=190><tr><td class=c><center>" + language['sys_player'] + "&nbsp;" + users[id]['name'] + "<br>" + language['place'] + "&nbsp;" + users[id]['rank'] + "/" + game_user_count + "</center></td></tr>";

    if (id != user_id)
    {
      result = result + "<tr><th><a href=messages.php?mode=write&id=" + id + ">" + language['gl_sendmess'] + "</a></th></tr>";
      result = result + "<tr><th><a href=buddy.php?a=2&u=" + id + ">" + language['gl_buddyreq'] + "</a></th></tr>";
    }
    result = result + "<tr><th><a href=stat.php?who=player&start=" + users[id]['rank'] + ">" + language['gl_stats'] + "</a></th></tr></table>";

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
    var result = "<table><tr><td class=c><center>" + language['sys_alliance'] + "&nbsp;";
    result += allies[id]['name'] + "<br>" + language['gal_sys_members'] + allies[id]['members'] + "</center></td></tr>";
    result += "<tr><th><a href=alliance.php?mode=ainfo&a=" + id + ">" + language['gl_ally_internal'] + "</a></th></tr>";
    result += "<tr><th><a href=stat.php?start=1&who=ally>" + language['gl_stats'] + "</a></th></tr>";
    if (allies[id]['url'])
    {
      result += "<tr><th><a href=" + allies[id]['url'] + " target=_new>" + language['gl_ally_web'] + "</th></tr>";
    }
    result += "</table>";

    allies[id]['cache'] = result;
  }
  else
  {
    result = allies[id]['cache'];
  }

  popup_show(result);
}

function show_debris(planet){
  if(!uni_row[planet]['cache_debris'])
  {
    var result = "<table>";
    result += "<tr><td class=c colspan=3>" + language['debris'] + " [" + uni_galaxy + ":" + uni_system + ":" + planet + "]</td></tr>";
    result += "<tr><th rowspan=6><img src=" + dpath + "planeten/debris.jpg height=75 width=75 /></th></tr>";
    result += "<tr><td class=c>" + language['gl_ressource'] + "</td><td class=c>" + sn_format_number(parseInt(uni_row[planet]['debris_metal']) + parseInt(uni_row[planet]['debris_crystal'])) + "</td></tr>";
    result += "<tr><th>" + language['sys_metal'] + '</th><th style="text-align: right;">' + sn_format_number(uni_row[planet]['debris_metal']) + "</th></tr>";
    result += "<tr><th>" + language['sys_crystal'] + '</th><th style="text-align: right;">' + sn_format_number(uni_row[planet]['debris_crystal']) + "</th></tr>";
    result += "<tr><td class=c colspan=2 align=center><a href=# onclick='javascript:doit(8," + uni_galaxy + "," + uni_system + "," + planet + ",2," + uni_row[planet]['debris_recyclers'] + ");'>" + language['type_mission8'] + "</a></td></tr>";
    result += "</table>";

    uni_row[planet]['cache_debris'] = result;
  }
  else
  {
    result = uni_row[planet]['cache_debris'];
  }

  popup_show(result, 250);
}

function makeAHREF(planet, planet_type, mission, mission_name){
  return '<a href=fleet.php?galaxy=' + uni_galaxy + '&system=' + uni_system + '&planet=' + planet +
    '&planettype=' + planet_type + '&target_mission=' + mission + '>' + mission_name + '</a><br />';
}

function show_planet(planet, planet_type)
{
  if(!uni_row[planet]['cache_planet' + planet_type])
  {
    var result = '<table width=240><tr><td class=c colspan=2>';

    if(planet_type == 1)
    {
      result += language['sys_planet'];
      diameter = '';
      planet_image = uni_row[planet]['planet_image'];
      name = uni_row[planet]['planet_name'];
    }
    else
    {
      result += language['sys_moon'];
      diameter = '<div>' + uni_row[planet]['moon_diameter'] + '</div>';
      planet_image = uni_row[planet]['moon_image'];
      name = uni_row[planet]['moon_name'];
    }

    result += '&nbsp;' + name + '&nbsp;[' + uni_galaxy + ':' + uni_system + ':' + planet + ']</td></tr>';
    result += '<tr><th width=75><img src=' + dpath + "planeten/small/s_" + planet_image + ".jpg height=75 width=75 />" + diameter + "</th><th align=center>";

    if(uni_row[planet]['owner'] == user_id)
    {
      result += makeAHREF(planet, planet_type, 4, language['type_mission4']);
    }
    else
    {
      if(uni_phalanx && planet_type == 1)
      {
        result += '<a href=# onclick=fenster("phalanx.php?galaxy=' + uni_galaxy + '&system=' + uni_system + '&planet=' + planet + '&planettype=' + planet_type + '")>' + language['gl_phalanx'] + '</a><br />';
      }

      result += '<a href=# onclick="javascript:doit(6, ' + uni_galaxy + ', ' + uni_system + ', ' + planet + ', ' + planet_type + ', ' + uni_spies + ');">' + language['type_mission6'] + '</a><br /><br />';
      result += makeAHREF(planet, planet_type, 1, language['type_mission1']);
      result += makeAHREF(planet, planet_type, 5, language['type_mission5']);

      if (uni_death_stars && planet_type == 3)
      {
        result += '<br />' + makeAHREF(planet, planet_type, 9, language['type_mission9']);
      }
    };
    result += '<br>' + makeAHREF(planet, planet_type, 3, language['type_mission3']) + '</th></tr></table>';

    uni_row[planet]['cache_planet' + planet_type] = result;
  }
  else
  {
    result = uni_row[planet]['cache_planet' + planet_type];
  }

  popup_show(result);
}
