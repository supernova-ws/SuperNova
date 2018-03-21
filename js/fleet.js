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

var prev_mission = 0;

function changeMission(mission) {
  if($(mission).val() == prev_mission) {
    return;
  }

  prev_mission = $(mission).val();

  jQuery("img.mission_button_image.button_pseudo_pressed").removeClass('button_pseudo_pressed');
  var button_pressed = jQuery("#mission_button" + prev_mission);
  button_pressed.addClass('button_pseudo_pressed');

  //var mini_mission = $('#fleet_mini_mission');
  //mini_mission.length ? mini_mission.html('<img src="design/images/mission_pointer_' + prev_mission + '.png" /><br />' + button_pressed.parent().text()) : false;
  var mini_mission = $('#fleet_mini_mission');
  mini_mission.length ? mini_mission.find('span').text(button_pressed.parent().find('#mission_name').text()) : false;

  switch(prev_mission) {
    case '1': // Attack
    case '2': // AKS
    case '5': // Hold
    case '6': // Spy
    case '8': // Recycle
    case '9': // Destroy
    case '10': // Missile
    case '15':// Explore
      jQuery('#resTable').hide();
      jQuery('#resource0').val(0).trigger('change');
      jQuery('#resource1').val(0).trigger('change');
      jQuery('#resource2').val(0).trigger('change');
    break;

    default:
      jQuery('#resTable').show();
    break;
  }

  if(prev_mission == 15) {
    jQuery('.fleet_expedition_warning').show();
  } else {
    jQuery('.fleet_expedition_warning').hide();
  }
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
  $('#end_coords').html('[' + galaxy + ':' + solarsystem + ':' + planet + ']');
  $('#end_planet_type').html(planet_types[planet_type]);
  planet_name = planet_names['g' + galaxy + 's' + solarsystem + 'p' + planet + 't' + planet_type];
  $('#end_name').html(planet_name ? planet_name : '');
  shortInfo();
  return false;
}

function setMission(mission) {
  document.getElementsByName('order')[0].selectedIndex = mission;
  return;
}

function setACS(fleet_group) {
  $('#fleet_group').val(fleet_group);
//   document.getElementsByName('fleet_group')[0].value = fleet_group;
//   return;
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
  if(thisGalaxy.length) {
    thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
    thisSystem = document.getElementsByName("thissystem")[0].value;
    thisPlanet = document.getElementsByName("thisplanet")[0].value;

    if ((targetGalaxy - thisGalaxy) != 0) {
      dist = Math.abs(targetGalaxy - thisGalaxy) * UNIVERSE_GALAXY_DISTANCE;
    } else if ((targetSystem - thisSystem) != 0) {
      dist = Math.abs(targetSystem - thisSystem) * 5 * 19 + 2700;
    } else if ((targetPlanet - thisPlanet) != 0) {
      dist = Math.abs(targetPlanet - thisPlanet) * 5 + 1000;
    } else {
      dist = 5;
    }
  } else {
    dist = UNIVERSE_GALAXY_DISTANCE;
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

function shortInfo() {
  jQuery("#distance").html(sn_format_number(distance()));

  var seconds = duration();
  var duration_tick = seconds * 1000;
  if(seconds) {
    var hours = Math.floor(seconds / 3600);
    seconds -= hours * 3600;

    var minutes = Math.floor(seconds / 60);
    seconds -= minutes * 60;

    if (hours < 10) hours = "0" + hours;
    if (minutes < 10) minutes = "0" + minutes;
    if (seconds < 10) seconds = "0" + seconds;

    jQuery("#duration").html(hours + ":" + minutes + ":" + seconds);

    time_temp_local = new Date();
    time_temp_local.setTime(time_temp_local.valueOf() + duration_tick);
    jQuery('#time_dst_local').html(time_temp_local.toLocaleString());

    time_temp = new Date(time_temp_local.valueOf() - timeDiff * 1000);
    jQuery('#time_dst').html(time_temp.toLocaleString());

    time_temp_local.setTime(time_temp_local.valueOf() + duration_tick);
    jQuery('#time_src_local').html(time_temp_local.toLocaleString());

    time_temp.setTime(time_temp.valueOf() + duration_tick);
    jQuery('#time_src').html(time_temp.toLocaleString());
  } else {
    jQuery("#duration").html("-");
  }
  var cons = consumption();

  jQuery("#consumption").html(sn_format_number(cons, 0, 'positive'));
  jQuery("#capacity").html(sn_format_number(fleet_capacity - cons, 0, 'positive'));
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
  var transportCapacity = fleet_capacity - check_resource(0) - check_resource(1) - check_resource(2);

  $("#remainingresources").html(sn_format_number(transportCapacity, 0, 'positive'));

  $("#fleet_page2_submit").prop('disabled', transportCapacity < 0);
  if(transportCapacity < 0) {
    $(".fleet_expedition_not_enough_fuel").show();
  } else {
    $(".fleet_expedition_not_enough_fuel").hide();
  }

  // document.getElementById("fleet_page2_submit").disabled = transportCapacity < 0;
  //if(transportCapacity < 0) {
  //  document.getElementById("fleet_page2_submit").disabled = true;
  //} else {
  //  document.getElementById("fleet_page2_submit").disabled = false;
  //}
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
/*
  for (i in ships)
  {
    jQuery('#ships' + i + 'slide').slider("value", 0);
  }
*/
  jQuery('[id^="ships"][id$="slide"]').slider("value", 0);
  fleet_global_update = false;
  fl_calc_stats();
}

function expe_fleet(expeditions)
{
  if(expeditions > 1)
  {
    // expeditions = 1 / expeditions;
    fleet_global_update = true;
  /*
    for (i in ships)
    {
      jQuery('#ships' + i + 'slide').slider("value", ships[i][C1_SHIP_AMOUNT]);
    }
  */
    jQuery('[id^="ships"][id$="slide"]').each(function(){
      jQuery(this).slider("value", Math.floor(jQuery(this).slider("option", "max") / expeditions));
    });
    fleet_global_update = false;
    fl_calc_stats();
  }
}

function max_fleet()
{
  fleet_global_update = true;
/*
  for (i in ships)
  {
    jQuery('#ships' + i + 'slide').slider("value", ships[i][C1_SHIP_AMOUNT]);
  }
*/
  jQuery('[id^="ships"][id$="slide"]').each(function(){
    jQuery(this).slider("value", jQuery(this).slider("option", "max"));
  });
  fleet_global_update = false;
  fl_calc_stats();
}

function check_resource(id)
{
//  var zi_res = parseInt(document.getElementById("resource" + id).value);
  var zi_res = parseInt($('#resource' + id).val());
  zi_res = zi_res ? zi_res : 0;

  $('#rest_res' + id).html(sn_format_number(resource_max[id] - zi_res, 0, 'zero'));
  // document.getElementById('rest_res' + id).innerHTML = sn_format_number(resource_max[id] - zi_res, 0, 'zero');

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

function max_resources() {
  for (var i in resource_max) {
    max_resource(i);
  }
  calculateTransportCapacity();
}

function fleet_dialog_show(caller, fleet_id) {
  popup_show(fleet_table_make(fleet_id), {my: 'left top', at: 'right top', of: caller});
}

function fleet_table_make(fleet_id)
{
  if(!fleets[fleet_id]) {
    return '';
  }

  if(!fleets[fleet_id][9]) {
    var fleet_html = '<table class="no_border_image"><tr><td class="c" colspan="2">' + language['sys_fleet_composition'] + '</td></tr>';
    var fleet = fleets[fleet_id][0];
    var resources = fleets[fleet_id][1];

    var ship_id, res_id;
    var fleet_capacity = 0;

    for(ship_id in fleet) {
      if(!fleet.hasOwnProperty(ship_id)) {
        continue;
      }

      fleet_html += '<tr><td class="c_l">';
      fleet_html += fleet[ship_id][C_SHIP_NAME];
      fleet_html += '</td><td class="c_r">' + sn_format_number(parseInt(fleet[ship_id][C_SHIP_AMOUNT]));
      fleet_html += '</td></tr>';
      fleet_capacity += fleet[ship_id][C_SHIP_CAPACITY] * fleet[ship_id][C_SHIP_AMOUNT];
    }

    if(fleet_capacity) {
      fleet_html += '<tr><td class="c">' + language['sys_capacity'] + '</td><td class="c" style="padding-right: 3px;">' + sn_format_number(fleet_capacity, 0, 'zero') + '</td></tr>';
    }

    var resources_total = parseInt(resources[0]) + parseInt(resources[1]) + parseInt(resources[2]);
    if(resources_total > 0) {
      for(res_id in resources) {
        if(!resources.hasOwnProperty(res_id) || !parseInt(resources[res_id])) {
          continue;
        }
        fleet_html += '<tr><th class=c><div style="text-align: left">' + res_names[res_id] + '</div></th><th><div style="text-align: right;">' + sn_format_number(parseInt(resources[res_id]), 0, 'zero') + '</div></th></tr>';
      }

      fleet_html += '<tr><td class=c>' + language['sys_resources'] + '</td><td class=c style="text-align: right; padding-right: 3px;">' + sn_format_number(resources_total, 0, 'zero') + '</td></tr>';
    }

    fleet_html += '</table>';

    fleets[fleet_id][9] = fleet_html;
  }

  return(fleets[fleet_id][9]);
}

function fleet_page_2_loaded() {
  mission_checked = 0;
  $("[name='target_mission']:checked").each(function(){
    mission_checked = $(this);
  });

  if(!mission_checked) {
    mission_checked = $("[name='target_mission']:first");
    mission_checked.attr('checked', 1);
  }
  changeMission(mission_checked);

  calculateTransportCapacity();
}

function fleet_page_2_prepare_slider(resourceID, resourceOnPlanet, fleetCapacity) {
  sn_ainput_make('resource' + resourceID, {max: Math.min(resourceOnPlanet, fleetCapacity), step: 1000, button_max: true, button_zero: true});

  jQuery('#resource' + resourceID).on('keyup change', function(event, ui) {
    calculateTransportCapacity();
  });

  jQuery('#resource' + resourceID + 'slide').on('slide slidechange', function(event, ui) {
    if(fleet_slide_changing) {
      return;
    } else {
      fleet_slide_changing = true;
    }
    var transportCapacity = fleetCapacity - check_resource(0) - check_resource(1) - check_resource(2);

    for(i = 0; i < 3; i++) {
      aSlider = jQuery('#resource' + i + 'slide');
      aSlider.slider("option", "max", Math.min(aSlider.slider("value") + transportCapacity, resource_max[i]));
      jQuery('#resource' + i).change();
    }
    fleet_slide_changing = false;
  });
}