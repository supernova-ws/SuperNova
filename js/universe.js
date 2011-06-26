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
    result += "<tr><th class=c_l colspan=3>" + language['debris'] + " [" + uni_galaxy + ":" + uni_system + ":" + planet + "]</th></tr>";
    result += "<tr><td class=c_c rowspan=6><img src=" + dpath + "planeten/debris.jpg height=75 width=75 /></td></tr>";
    // debris_incoming
    result += "<tr><th class=c_c>" + language['gl_ressource'] + "</th><th class=c_r>" + sn_format_number(parseInt(uni_row[planet]['debris_metal']) + parseInt(uni_row[planet]['debris_crystal'])) + "</th></tr>";
    result += "<tr><td class=c_c>" + language['sys_metal'] + '</td><td class=c_r>' + sn_format_number(uni_row[planet]['debris_metal']) + "</td></tr>";
    result += "<tr><td class=c_c>" + language['sys_crystal'] + '</td><td class=c_r>' + sn_format_number(uni_row[planet]['debris_crystal']) + "</td></tr>";
    result += "<tr><th class=c_c colspan=2 align=center>";
    if(uni_row[planet]['debris_recyclers'] > 0)
    {
      result += "<span style='cursor:pointer'  onclick='doit(8," + uni_galaxy + "," + uni_system + "," + planet + ",2," + uni_row[planet]['debris_recyclers'] + ");'>" + language['type_mission8'] + "</span><br>";
    }
    result += language['lang_recyclers'] + ': ' + uni_row[planet]['debris_recyclers'] + (uni_row[planet]['debris_incoming'] > 0 ? '<span class="neutral">+' + uni_row[planet]['debris_incoming'] + "</span>": "") + "/" + uni_row[planet]['debris_rc_need'] + "</th></tr>";
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
    result += '<tr><th width=75><img src=' + dpath + "planeten/small/s_" + planet_image + ".jpg height=75 width=75 />" + diameter + "</th><th align=center>";

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

      result += '<span style="cursor:pointer" onclick="doit(6, ' + uni_galaxy + ', ' + uni_system + ', ' + planet + ', ' + planet_type + ', ' + uni_spies + ');">' + language['type_mission6'] + '</span><br /><br />';
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

function changeSlots(slotsInUse)
{
  var e = document.getElementById('slots');
  e.innerHTML = slotsInUse;
}

function uni_set_ships(ship, count)
{
  var e = document.getElementById(ship);
  e.innerHTML = count;
}

var ajax = new sack();
var strInfo = "";
var CmdCode = 0;

function whenResponse ()
{
  retValue  = this.response;
  retVals   = this.response.split("|");
  CmdCode   = retVals[0];
  strInfo   = retVals[1];
  UsedSlots = retVals[2];
  SpyProbes = retVals[3];
  Recyclers = retVals[4];
  Missiles  = retVals[5];
  addToTable("done", "success");
  changeSlots( UsedSlots );
  uni_set_ships("probes", SpyProbes);
  uni_set_ships("recyclers", Recyclers );
  uni_set_ships("missiles", Missiles );
}

function addToTable(strDataResult, strClass)
{
  if(CmdCode != 0)
  {
    strDataResult = language['sys_error'];
    strClass = "error";
  }
  else
  {
    strDataResult = language['sys_done'];
    strClass = "success";
  };
  var e = document.getElementById('fleetstatusrow');
  var e2 = document.getElementById('fleetstatustable');
  e.style.display = '';
  if(e2.rows.length > 2) {
    e2.deleteRow(2);
  }
  var row = e2.insertRow(0);
  var td1 = document.createElement("td");
//  var td1text = document.createTextNode(retValue);
  var td1text = document.createTextNode(strInfo);
  td1.appendChild(td1text);
  var td2 = document.createElement("td");
  var span = document.createElement("span");
  var spantext = document.createTextNode(strDataResult);
  var spanclass = document.createAttribute("class");
  spanclass.nodeValue = strClass;
  span.setAttributeNode(spanclass);
  span.appendChild(spantext);
  td2.appendChild(span);
  row.appendChild(td1);
  row.appendChild(td2);
}

function doit (order, galaxy, system, planet, planettype, shipcount)
{
  ajax.requestFile = "flotenajax.php?action=send";
  ajax.runResponse = whenResponse;
  ajax.execute = true;
  ajax.setVar("thisgalaxy", uni_user_galaxy);
  ajax.setVar("thissystem", uni_user_system);
  ajax.setVar("thisplanet", uni_user_planet);
  ajax.setVar("thisplanettype", uni_user_planet_type);
  ajax.setVar("mission", order);
  ajax.setVar("galaxy", uni_galaxy);
  ajax.setVar("system", uni_system);
  ajax.setVar("planet", planet);
  ajax.setVar("planettype", planettype);
  if (order == 6) // Spy
  {
    ajax.setVar("ship" + SHIP_SPY, shipcount);
  }
  if (order == 7) //Colonize
  {
    ajax.setVar("ship" + SHIP_COLONIZER, 1); // Colonizer
    ajax.setVar("ship" + SHIP_CARGO_BIG, 2); // Big Cargo
  }
  if (order == 8) // Recycle
  {
    ajax.setVar("ship" + SHIP_RECYCLER, shipcount);
  }
  if (order == 10) // Missile attack
  {
    ajax.setVar("ship503", shipcount);
    ajax.setVar("fleet[503]", shipcount);
    ajax.setVar("structures", document.uni_missile_form.Target.value);
  }
  ajax.runAJAX();
}
