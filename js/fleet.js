var C_SHIP_NAME        = 0,
    C_SHIP_AMOUNT      = 1,
    C_SHIP_SPEED       = 2,
    C_SHIP_CONSUMPTION = 3,
    C_SHIP_CAPACITY    = 4;

var C1_SHIP_NAME        = 0,
    C1_SHIP_AMOUNT      = 0,
    C1_SHIP_SPEED       = 1,
    C1_SHIP_CONSUMPTION = 2,
    C1_SHIP_CAPACITY    = 3;

function changeMission(mission)
{
  var element = document.getElementById('resTable');

  switch(mission.value)
  {
    case '1': // Attack
    case '2': // AKS
    case '5': // Hold
    case '6': // Spy
    case '8': // Recycle
    case '9': // Destroy
    case '15':// Explore
      element.style.display = "none";
    break;

    default:
      element.style.display = "inline";
    break;
  };
}

function speed_percent() {
  var sp = document.getElementsByName("speed")
  return sp.length ? sp[0].value : 10;
}

function setTarget(galaxy, solarsystem, planet, planet_type) {
  document.getElementsByName('galaxy')[0].value = galaxy;
  document.getElementsByName('system')[0].value = solarsystem;
  document.getElementsByName('planet')[0].value = planet;
  document.getElementsByName('planet_type')[0].value = planet_type;
}

function setMission(mission) {
  document.getElementsByName('order')[0].selectedIndex = mission;
  return;
}

function setACS(fleet_group) {
   document.getElementsByName('fleet_group')[0].value = fleet_group;
   return;
}

function setACS_target(acs_target_mr) {
   document.getElementsByName('acs_target_mr')[0].value = acs_target_mr;
   return;
}

function min(a, b) {
  a = a * 1;
  b = b * 1;
  if (a > b) {
    return b;
  } else {
    return a;
  }
}

function distance() {
  var thisGalaxy;
  var thisSystem;
  var thisPlanet;

  var targetGalaxy;
  var targetSystem;
  var targetPlanet;

  var dist = 0;

  targetGalaxy = document.getElementsByName("galaxy")[0].value;
  targetSystem = document.getElementsByName("system")[0].value;
  targetPlanet = document.getElementsByName("planet")[0].value;

  thisGalaxy = document.getElementsByName("thisgalaxy");
  if(thisGalaxy.length)
  {
    thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
    thisSystem = document.getElementsByName("thissystem")[0].value;
    thisPlanet = document.getElementsByName("thisplanet")[0].value;

    if ((targetGalaxy - thisGalaxy) != 0) {
      dist = Math.abs(targetGalaxy - thisGalaxy) * 20000;
    } else if ((targetSystem - thisSystem) != 0) {
      dist = Math.abs(targetSystem - thisSystem) * 5 * 19 + 2700;
    } else if ((targetPlanet - thisPlanet) != 0) {
      dist = Math.abs(targetPlanet - thisPlanet) * 5 + 1000;
    } else {
      dist = 5;
    }
  }
  else
  {
    dist = 20000;
  }

  return(dist);
}

function duration() {
  ret = Math.round(((35000 / speed_percent() * Math.sqrt(distance() * 10 / fleet_speed) + 10) / speed_factor ));
  return ret;
}

function consumption() {
  var consumption = 0;
  var spd = speed_percent() * Math.sqrt(fleet_speed);

  for (var i in ships) {
    shipcount = ships[i][C1_SHIP_AMOUNT];
    shipspeed = ships[i][C1_SHIP_SPEED];
    shipconsumption = ships[i][C1_SHIP_CONSUMPTION];

    consumption += shipconsumption * shipcount  * (spd / Math.sqrt(shipspeed) / 10 + 1 ) * (spd / Math.sqrt(shipspeed) / 10 + 1 );
  }

  consumption = Math.round(distance() * consumption / 35000) + 1;
  return(consumption);
}

function probeConsumption() {
  var consumption = 0;
  var basicConsumption = 0;
  var values;
  var i;

  dist = distance();
  dur = duration();

  if (document.getElementsByName("ship" + SHIP_SPY)[0]) {
    shipspeed = document.getElementsByName("speed" + SHIP_SPY)[0].value;
    spd = 35000 / (dur * speed_factor - 10) * Math.sqrt(dist * 10 / shipspeed);

    basicConsumption = document.getElementsByName("consumption" + SHIP_SPY)[0].value
    * document.getElementsByName("ship" + SHIP_SPY)[0].value;
    consumption += basicConsumption * dist / 35000 * ((spd / 10) + 1) * ((spd / 10) + 1);
  }

  consumption = Math.round(consumption) + 1;
  return(consumption);
}

function unusedProbeStorage() {
  var stor =  document.getElementsByName('capacity' + SHIP_SPY)[0].value * document.getElementsByName('ship' + SHIP_SPY)[0].value - probeConsumption();

  return (stor>0) ? stor : 0;
}

function shortInfo() {
  document.getElementById("distance").innerHTML = sn_format_number(distance());

  var seconds = duration();
  var duration_tick = seconds * 1000;
  if(seconds)
  {
    var hours = Math.floor(seconds / 3600);
    seconds -= hours * 3600;

    var minutes = Math.floor(seconds / 60);
    seconds -= minutes * 60;

    if (minutes < 10) minutes = "0" + minutes;
    if (seconds < 10) seconds = "0" + seconds;

    document.getElementById("duration").innerHTML = hours + ":" + minutes + ":" + seconds;

    time_temp = new Date();

    time_temp.setTime(time_temp.valueOf() + duration_tick);
    element = document.getElementById("time_dst");
    if(element)
    {
      element.innerHTML = time_temp.toLocaleString();
    }

    time_temp.setTime(time_temp.valueOf() + duration_tick);
    element = document.getElementById("time_src");
    if(element)
    {
      element.innerHTML = time_temp.toLocaleString();
    }
  }
  else
  {
    document.getElementById("duration").innerHTML = "-";
  }
  var cons = consumption();

  element = document.getElementById("consumption");
  if(element)
  {
    element.innerHTML = sn_format_number(cons, 0, 'positive');
  }

  element = document.getElementById("capacity");
  if(element)
  {
    element.innerHTML = sn_format_number(fleet_capacity - cons, 0, 'positive');
  }
}

var fleet_consumption = 0;
var fleet_capacity    = 0;
var fleet_speed       = Infinity;

function fl_calc_stats(event, ui) {
  if(fleet_global_update)
  {
    return;
  }

  fleet_consumption = 0;
  fleet_capacity    = 0;
  fleet_speed       = Infinity;

  var ship_number = Array();

  for(i in ships)
  {
    ship_number[i] = jQuery('#ships' + i + 'slide').slider("value");
    if( ship_number[i] != 0)
    {
      fleet_speed = Math.min(fleet_speed, ships[i][C1_SHIP_SPEED]);
      fleet_capacity += ship_number[i] * ships[i][C1_SHIP_CAPACITY];
    }
  }


  var spd = speed_percent() * Math.sqrt(fleet_speed);
  for(i in ships)
  {
    if( ship_number[i] != 0)
    {
      fleet_consumption += ships[i][C1_SHIP_CONSUMPTION] * ship_number[i]  * (spd / Math.sqrt(ships[i][C1_SHIP_SPEED]) / 10 + 1 ) * (spd / Math.sqrt(ships[i][C1_SHIP_SPEED]) / 10 + 1 );
    }
  }
  fleet_consumption = Math.round(distance() * fleet_consumption / 35000) + 1;
  if(fleet_capacity > 0)
  {
    fleet_capacity -= fleet_consumption;
  }
  else
  {
    fleet_consumption = 0;
  }

  document.getElementById('int_fleet_capacity').innerHTML = sn_format_number(fleet_capacity);
  document.getElementById('int_fleet_consumption').innerHTML = sn_format_number(fleet_consumption);
  document.getElementById('int_fleet_speed').innerHTML = fleet_speed == Infinity ? '-' : sn_format_number(fleet_speed);

  shortInfo();
}

function calculateTransportCapacity() {
  transportCapacity = fleet_capacity - check_resource(0) - check_resource(1) - check_resource(2);

  document.getElementById("remainingresources").innerHTML = sn_format_number(transportCapacity, 0, 'positive');

  if(transportCapacity<0)
  {
    document.getElementById("fleet_page2_submit").disabled = true;
  }
  else
  {
    document.getElementById("fleet_page2_submit").disabled = false;
  }
  return transportCapacity;
}

function setNumber(name,number){
  if (typeof document.getElementsByName('ship'+name)[0] != 'undefined'){
    document.getElementsByName('ship'+name)[0].value=number;
  }
}

function abs(a) {
  return a < 0 ? -a : a;
}


var fleet_global_update = false;

function zero_fleet()
{
  fleet_global_update = true;
  for (i in ships)
  {
    jQuery('#ships' + i + 'slide').slider("value", 0);
  }
  fleet_global_update = false;
  fl_calc_stats();
}

function max_fleet()
{
  fleet_global_update = true;
  for (i in ships)
  {
    jQuery('#ships' + i + 'slide').slider("value", ships[i][C1_SHIP_AMOUNT]);
  }
  fleet_global_update = false;
  fl_calc_stats();
}

function check_resource(id)
{
  var zi_res = parseInt(document.getElementById("resource" + id).value);
  if (isNaN(zi_res)){
    zi_res = 0;
  }

  document.getElementById('rest_res' + id).innerHTML = sn_format_number(resource_max[id] - zi_res, 0, 'zero');

  return zi_res;
}

function zero_resource(id)
{
  element = document.getElementsByName('resource' + id)[0];

  if(element)
  {
    element.value = 0;
    jQuery("#resource" + id).trigger('change');
  }
  calculateTransportCapacity();
}

function zero_resources()
{
  for (i in resource_max)
  {
    zero_resource(i);
  }
  calculateTransportCapacity();
}

function max_resource(id) {
  if (document.getElementsByName("resource" + id)[0])
  {
    var freeCapacity = Math.max(fleet_capacity - check_resource(0) - check_resource(1) - check_resource(2), 0);
    var cargo = Math.min (freeCapacity + check_resource(id), resource_max[id]);

    document.getElementsByName("resource" + id)[0].value = cargo;
    jQuery("#resource" + id).trigger('change');
    calculateTransportCapacity();
  }
}

function max_resources()
{
  for (i in resource_max)
  {
    max_resource(i);
  }
  calculateTransportCapacity();
}

function fleet_dialog_show(caller, fleet_id)
{
  popup_show(fleet_table_make(fleet_id));
}

function fleet_table_make(fleet_id)
{
  if(!fleets[fleet_id])
  {
    return false;
  }

  if(!fleets[fleet_id][9])
  {
    var fleet_html = '<table><tr><td class=c colspan=2>' + language['sys_ships'] + '</td></tr>';
    var fleet = fleets[fleet_id][0];
    var resources = fleets[fleet_id][1];

    var ship_id;
    var fleet_capacity = 0;

    for(ship_id in fleet)
    {
      //if(fleet[ship_id][C_SHIP_AMOUNT] != 0)
      {
        fleet_html += '<tr><td class="c_l">';
        switch(fleet[ship_id][C_SHIP_NAME])
        {
          default:
            fleet_html += fleet[ship_id][C_SHIP_NAME];
          break;
        }
        fleet_html += '</td><td class="c_r">' + sn_format_number(parseInt(fleet[ship_id][C_SHIP_AMOUNT]));
        fleet_html += '</td></tr>';
        fleet_capacity += fleet[ship_id][C_SHIP_CAPACITY] * fleet[ship_id][C_SHIP_AMOUNT];
      }
    };

    if(fleet_capacity)
    {
      fleet_html += '<tr><td class="c">' + language['sys_capacity'] + '</td><td class="c" style="padding-right: 3px;">' + sn_format_number(fleet_capacity, 0, 'zero') + '</td></tr>';
    }

    var resources_total = parseInt(resources[0]) + parseInt(resources[1]) + parseInt(resources[2]);
    if(resources_total > 0)
    {
      for(res_id in resources)
      {
        if(parseInt(resources[res_id]))
        {
          fleet_html += '<tr><th class=c><div style="text-align: left">' + res_names[res_id] + '</div></th><th><div style="text-align: right;">' + sn_format_number(parseInt(resources[res_id]), 0, 'zero') + '</div></th></tr>';
        }
      }

      fleet_html += '<tr><td class=c>' + language['sys_resources'] + '</td><td class=c style="text-align: right; padding-right: 3px;">' + sn_format_number(resources_total, 0, 'zero') + '</td></tr>';
    }

    fleet_html += '</table>';

    fleets[fleet_id][9] = fleet_html;
  }

  return(fleets[fleet_id][9]);
}