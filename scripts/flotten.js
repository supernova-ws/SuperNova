function changeMission(mission)
{
  element = document.getElementById('resTable');

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
  var sp;
  sp = document.getElementsByName("speed")[0].value;

  return(sp);
}

function target() {
  var galaxy;
  var system;
  var planet;

  galaxy = document.getElementsByName("galaxy")[0].value;
  system = document.getElementsByName("system")[0].value;
  planet = document.getElementsByName("planet")[0].value;

  return("["+galaxy+":"+system+":"+planet+"]");
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

function setUnion(unionid) {
  document.getElementsByName('union2')[0].selectedIndex = unionid;
}

function setTargetLong(galaxy, solarsystem, planet, planet_type, mission, cnt) {
  setTarget(galaxy, solarsystem, planet, planet_type);
  setMission(mission);
  setUnions(cnt);
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

function get_fleet_speed() {
/*
  if(!fleet_speed)
  {
    var fleet_speed = document.getElementsByName("fleet_speed")[0].value;
  }
*/
  return(fleet_speed);
}

function distance() {
  var thisGalaxy;
  var thisSystem;
  var thisPlanet;

  var targetGalaxy;
  var targetSystem;
  var targetPlanet;

  var dist = 0;

  thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
  thisSystem = document.getElementsByName("thissystem")[0].value;
  thisPlanet = document.getElementsByName("thisplanet")[0].value;

  targetGalaxy = document.getElementsByName("galaxy")[0].value;
  targetSystem = document.getElementsByName("system")[0].value;
  targetPlanet = document.getElementsByName("planet")[0].value;

  if ((targetGalaxy - thisGalaxy) != 0) {
    dist = Math.abs(targetGalaxy - thisGalaxy) * 20000;
  } else if ((targetSystem - thisSystem) != 0) {
    dist = Math.abs(targetSystem - thisSystem) * 5 * 19 + 2700;
  } else if ((targetPlanet - thisPlanet) != 0) {
    dist = Math.abs(targetPlanet - thisPlanet) * 5 + 1000;
  } else {
    dist = 5;
  }

  return(dist);
}

function duration() {
//  var speedfactor;
//  speedfactor = document.getElementsByName("speedfactor")[0].value;

  ret = Math.round(((35000 / speed_percent() * Math.sqrt(distance() * 10 / get_fleet_speed()) + 10) / speed_factor ));
  return ret;
}

function consumption() {
  var consumption = 0;
  var spd = speed_percent() * Math.sqrt(get_fleet_speed());

  for (var i in ships) {
    shipcount = ships[i][0];
    shipspeed = ships[i][1];
    shipconsumption = ships[i][2];

    consumption += shipconsumption * shipcount  * (spd / Math.sqrt(shipspeed) / 10 + 1 ) * (spd / Math.sqrt(shipspeed) / 10 + 1 );
  }

  consumption = Math.round(distance() * consumption / 35000) + 1;
  // document.getElementById("debug").innerHTML = consumption;

  return(consumption);
}

function probeConsumption() {
  var consumption = 0;
  var basicConsumption = 0;
  var values;
  var i;

  msp = get_fleet_speed();
  sp = speed_percent();
  dist = distance();
  dur = duration();
  // speedfactor = document.getElementsByName("speedfactor")[0].value;

  if (document.getElementsByName("ship210")[0]) {
    shipspeed = document.getElementsByName("speed210")[0].value;
    spd = 35000 / (dur * speed_factor - 10) * Math.sqrt(dist * 10 / shipspeed);

    basicConsumption = document.getElementsByName("consumption210")[0].value
    * document.getElementsByName("ship210")[0].value;
    consumption += basicConsumption * dist / 35000 * ((spd / 10) + 1) * ((spd / 10) + 1);
  }


  consumption = Math.round(consumption) + 1;

  //  document.write(values);

  return(consumption);
}

function unusedProbeStorage() {

  var storage = document.getElementsByName('capacity210')[0].value * document.getElementsByName('ship210')[0].value;
  var stor =  storage - probeConsumption();
  return (stor>0)?stor:0;

}

function storage() {
  if(fleet_capacity)
  {
    return fleet_capacity;
  }
/*
  var storage = 0;

  for (i = 200; i < 300; i++) {

    if (document.getElementsByName("ship" + i)[0]) {
      if ((document.getElementsByName("ship" + i)[0].value * 1) >= 1) {
        storage
        += document.getElementsByName("ship" + i)[0].value
        *  document.getElementsByName("capacity" + i)[0].value
      }
    }
  }

  storage  = storage * getStorageFaktor();
  storage -= consumption();
  if (document.getElementsByName("ship210")[0]) {
    storage -= unusedProbeStorage();
  }

  return(storage);
*/
}


function fleetInfo() {
  document.getElementById("speed").innerHTML = speed_percent() * 10 + "%";
  document.getElementById("target").innerHTML = target();
  document.getElementById("distance").innerHTML = distance();

  var seconds = duration();
  var hours = Math.floor(seconds / 3600);
  seconds -= hours * 3600;

  var minutes = Math.floor(seconds / 60);
  seconds -= minutes * 60;

  if (minutes < 10) minutes = "0" + minutes;
  if (seconds < 10) seconds = "0" + seconds;

  document.getElementById("duration").innerHTML = hours + ":" + minutes + ":" + seconds + " h";

  var stor = storage();
  var cons = consumption();
//  document.getElementById("maxspeed").innerHTML = tsdpkt(get_fleet_speed());
  if (stor >= 0) {
    document.getElementById("consumption").innerHTML = '<font color="lime">'+cons+'</font>';
    document.getElementById("storage").innerHTML = '<font color="lime">'+stor+'</font>';
  } else {
    document.getElementById("consumption").innerHTML = '<font color="red">'+cons+'</font>';
    document.getElementById("storage").innerHTML = '<font color="red">'+stor+'</font>';
  }
  calculateTransportCapacity();
}

function shortInfo() {
//  document.getElementById("debug").innerHTML = document.getElementsByName("thisgalaxy")[0].value+':'+document.getElementsByName("thissystem")[0].value+':'+document.getElementsByName("thisplanet")[0].value;

//    thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
//  thisSystem = document.getElementsByName("thissystem")[0].value;
//  thisPlanet = document.getElementsByName("thisplanet")[0].value;
//
//  targetGalaxy = document.getElementsByName("galaxy")[0].value;
//  targetSystem = document.getElementsByName("system")[0].value;
//  targetPlanet = document.getElementsByName("planet")[0].value;



  document.getElementById("distance").innerHTML = tsdpkt(distance());
  var seconds = duration();
  var hours = Math.floor(seconds / 3600);
  seconds -= hours * 3600;

  var minutes = Math.floor(seconds / 60);
  seconds -= minutes * 60;

  if (minutes < 10) minutes = "0" + minutes;
  if (seconds < 10) seconds = "0" + seconds;

  document.getElementById("duration").innerHTML = hours + ":" + minutes + ":" + seconds + " h";
  var stor = storage();
  var cons = consumption();


//  document.getElementById("maxspeed").innerHTML = tsdpkt(get_fleet_speed());
  if (stor >= 0) {
    document.getElementById("consumption").innerHTML = '<font color="lime">'+tsdpkt(cons)+'</font>';
//    document.getElementById("storage").innerHTML = '<font color="lime">'+tsdpkt(stor)+'</font>';
  } else {
    document.getElementById("consumption").innerHTML = '<font color="red">'+tsdpkt(cons)+'</font>';
//    document.getElementById("storage").innerHTML = '<font color="red">'+tsdpkt(stor)+'</font>';
  }

}


function setResource(id, val) {
  if (document.getElementsByName(id)[0]) {
    document.getElementsByName("resource" + id)[0].value = val;
  }
}

function maxResource(id) {
  var thisresource = parseInt(document.getElementsByName("thisresource" + id)[0].value);
  var thisresourcechosen = parseInt(document.getElementsByName("resource" + id)[0].value);

  if (isNaN(thisresourcechosen)){
    thisresourcechosen = 0;
  }
  if (isNaN(thisresource))
  {
    thisresource = 0;
  }

  var storCap = storage();
  if (id==3)
  {
    thisresource -= consumption();
  }

//  document.getElementById("debug").innerHTML = storCap;

  var metalToTransport = parseInt(document.getElementsByName("resource1")[0].value);
  var crystalToTransport = parseInt(document.getElementsByName("resource2")[0].value);
  var deuteriumToTransport = parseInt(document.getElementsByName("resource3")[0].value);

  if (isNaN(metalToTransport))
  {
    metalToTransport=0;
  }
  if (isNaN(crystalToTransport))
  {
    crystalToTransport=0;
  }
  if (isNaN(deuteriumToTransport))
  {
    deuteriumToTransport=0;
  }

  var freeCapacity = Math.max(storCap - metalToTransport - crystalToTransport - deuteriumToTransport, 0);
  var cargo = Math.min (freeCapacity + thisresourcechosen, thisresource);

  if (document.getElementsByName("resource" + id)[0]) {
    document.getElementsByName("resource" + id)[0].value = cargo;
  }
  calculateTransportCapacity();
}

function maxResources() {
  var id;
  var storCap = storage();
  var metalToTransport = document.getElementsByName("thisresource1")[0].value;
  var crystalToTransport = document.getElementsByName("thisresource2")[0].value;
  var deuteriumToTransport = document.getElementsByName("thisresource3")[0].value - consumption();

  var freeCapacity = storCap - metalToTransport - crystalToTransport - deuteriumToTransport;
  if (freeCapacity < 0) {
    metalToTransport = Math.min(metalToTransport, storCap);
    crystalToTransport = Math.min(crystalToTransport, storCap - metalToTransport);
    deuteriumToTransport = Math.min(deuteriumToTransport, storCap - metalToTransport - crystalToTransport);
  }
  document.getElementsByName("resource1")[0].value = Math.max(metalToTransport, 0);
  document.getElementsByName("resource2")[0].value = Math.max(crystalToTransport, 0);
  document.getElementsByName("resource3")[0].value = Math.max(deuteriumToTransport, 0);
  calculateTransportCapacity();
}

function maxShip(id) {
  if (document.getElementsByName(id)[0]) {
    document.getElementsByName(id)[0].value = document.getElementsByName("max" + id)[0].value;
  }
}

function maxShips() {
  var id;
  for (i = 200; i < 220; i++) {
    if (i!=212){
    id = "ship"+i;
    maxShip(id);
    }
  }
}


function noShip(id) {
  if (document.getElementsByName(id)[0]) {
    document.getElementsByName(id)[0].value = 0;
  }
}


function noShips (){
  var id;
  for (i = 200; i < 220; i++) {
    id = "ship"+i;
    noShip(id);
  }
}

function calculateTransportCapacity() {
  transportCapacity = storage() - check_resource(0) - check_resource(1) - check_resource(2);

  document.getElementById("remainingresources").innerHTML = sn_format_number(transportCapacity, 0, 'lime');

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

function getLayerRef(id, document) {
  if (!document)
  document = window.document;

  if (document.layers) {
    for (var l = 0; l < document.layers.length; l++)
    if (document.layers[l].id == id)
    return document.layers[l];
    for (var l = 0; l < document.layers.length; l++) {
      var result = getLayerRef(id, document.layers[l].document);
      if (result)
      return result;
    }
    return null;
  }
  else if (document.all) {
    return document.all[id];
  }
  else if (document.getElementById) {
    return document.getElementById(id);
  }
}

function setVisibility(objLayer, visible) {
  if (document.layers) {
    objLayer.visibility =
    (visible == true) ? 'show' : 'hide';
  } else {
    objLayer.style.visibility =
    (visible == true) ? 'visible' : 'hidden';
  }
}

function setVisibilityForDivByPrefix(prefix, visible, d) {
  if (!d)
  d = window.document;

  if (document.layers) {
    for (var i = 0; i < d.layers.length; i++) {
      if (d.layers[i].id.substr(0, prefix.length) == prefix)
      setVisibility(d.layers[l], visible);
      setVisibilityForDivByPrefix(prefix, visible, d.layers[i].document);
    }
  } else if (document.all) {
    var layers = document.all.tags("div");
    for (i = 0; i < layers.length; i++) {
      if (layers[i].id.substr(0, prefix.length) == prefix)
      setVisibility(document.all.tags("div")[i].visible);
    }
  } else if (document.getElementsByTagName) {
    var layers = document.getElementsByTagName("div");
    for (i = 0; i < layers.length; i++) {
      if (layers[i].id.substr(0, prefix.length) == prefix)
      setVisibility(layers[i].visible);
    }
  }
}

function setPlanet(string) {
  var splitstring = string.split(":");
  document.getElementsByName('galaxy')[0].value = splitstring[0];
  document.getElementsByName('system')[0].value = splitstring[1];
  document.getElementsByName('planet')[0].value = splitstring[2];
  document.getElementsByName('planet_type')[0].value = splitstring[3];
  setMission(splitstring[4]);
}

function setUnions(cnt) {
  galaxy = document.getElementsByName('galaxy')[0].value;
  system = document.getElementsByName('system')[0].value;
  planet =   document.getElementsByName('planet')[0].value;
  planet_type = document.getElementsByName('planet_type')[0].value;

  thisgalaxy = document.getElementsByName("thisgalaxy")[0].value;
  thissystem = document.getElementsByName("thissystem")[0].value;
  thisplanet = document.getElementsByName("thisplanet")[0].value;
  thisplanet_type = document.getElementsByName("thisplanet_type")[0].value;

  spd = document.getElementsByName("speed")[0].value;
  // speedfactor = document.getElementsByName("speedfactor")[0].value;

  for (i = 0; i < cnt; i++) {
    //    alert ("set unions called "+ cnt);
    var string = document.getElementById("union"+i).innerHTML;
    time = document.getElementsByName('union'+i+'time')[0].value;
    /* alert ("set unions called "+ time);*/
    targetgalaxy = document.getElementsByName('union'+i+'galaxy')[0].value;
    targetsystem = document.getElementsByName('union'+i+'system')[0].value;
    targetplanet = document.getElementsByName('union'+i+'planet')[0].value;
    targetplanet_type = document.getElementsByName('union'+i+'planet_type')[0].value;

    if (targetgalaxy == galaxy && targetsystem == system
    && targetplanet == planet && targetplanet_type == planet_type){


      inSpeedLimit = isInSpeedLimit(flightTime(thisgalaxy, thissystem, thisplanet,
      targetgalaxy, targetsystem, targetplanet,
      spd, speed_factor), time);
      //      alert ("in here" + inSpeedLimit);
      if (inSpeedLimit == 2) {
        document.getElementById("union"+i).innerHTML =
        '<font color="lime">'+string+'</font>';
      } else if (inSpeedLimit == 1) {
        document.getElementById("union"+i).innerHTML =
        '<font color="orange">'+string+'</font>';
      } else {
        document.getElementById("union"+i).innerHTML =
        '<font color="red">'+string+'</font>';
      }
    } else {
      document.getElementById("union"+i).innerHTML =
      '<font color="#00a0ff">'+string+'</font>';
      //      alert("red"+i);
    }
  }
}

function isInSpeedLimit(flightlength, eventtime) {
  var time = new Date();
  time = Math.round(time / 1000);
  if (flightlength < ((eventtime - time) * (1 + 0.5))) {
    return 2;
  } else if (flightlength < ((eventtime - time) * 1)) {
    return 1;
  } else {
    return 0;
  }
}

function flightTime(galaxy, system, planet,
targetgalaxy, targetsystem, targetplanet,
spd, maxspeed, speed_factor) {
  //    alert ("flighttime called 1"+galaxy+" "+system+" "+planet+" "+targetgalaxy+" "+targetsystem+" "+targetplanet);

  if ((galaxy - targetgalaxy) != 0) {
    dist = Math.abs(galaxy - targetgalaxy) * 20000;
  } else if ((system - targetsystem) != 0) {
    dist = Math.abs(system - targetsystem) * 5 * 19 + 2700;
  } else if ((planet - targetplanet) != 0) {
    dist = Math.abs(planet - targetplanet) * 5 + 1000;
  } else {
    dist = 5;
  }
  return Math.round(((35000 / spd * Math.sqrt(dist * 10 / maxspeed) + 10) / speed_factor));
}

function showCoords() {
  document.getElementsByName('speed')[0].disabled = false;
  document.getElementsByName('galaxy')[0].disabled = false;
  document.getElementsByName('system')[0].disabled = false;
  document.getElementsByName('planet')[0].disabled = false;
  document.getElementsByName('planet_type')[0].disabled = false;
  document.getElementsByName('shortlinks')[0].disabled = false;
}

function hideCoords() {
  document.getElementsByName('speed')[0].disabled = true;
  document.getElementsByName('galaxy')[0].disabled = true;
  document.getElementsByName('system')[0].disabled = true;
  document.getElementsByName('planet')[0].disabled = true;
  document.getElementsByName('planet_type')[0].disabled = true;
  document.getElementsByName('shortlinks')[0].disabled = true;
}

function showOrders() {
  document.getElementsByName('order')[0].disabled = false;
  return;
}

function hideOrders() {
  document.getElementsByName('order')[0].disabled = true;
}

function showResources() {
  document.getElementsByName('resource1')[0].disabled = false;
  document.getElementsByName('resource2')[0].disabled = false;
  document.getElementsByName('resource3')[0].disabled = false;
  document.getElementsByName('holdingtime')[0].disabled = false;
}

function hideResources() {
  document.getElementsByName('resource1')[0].disabled = true;
  document.getElementsByName('resource2')[0].disabled = true;
  document.getElementsByName('resource3')[0].disabled = true;
  document.getElementsByName('holdingtime')[0].disabled = true;
}

function setShips(s16,s17,s18,s19,s20,s21,s22,s23,s24,s25,s27,s28,s29){

  setNumber('202',s16);
  setNumber('203',s17);
  setNumber('204',s18);
  setNumber('205',s19);
  setNumber('206',s20);
  setNumber('207',s21);
  setNumber('208',s22);
  setNumber('209',s23);
  setNumber('210',s24);
  setNumber('211',s25);
  setNumber('213',s27);
  setNumber('214',s28);
  setNumber('215',s29);

}

function setNumber(name,number){
  if (typeof document.getElementsByName('ship'+name)[0] != 'undefined'){
    document.getElementsByName('ship'+name)[0].value=number;
  }
}

function tsdpkt(f) {
  r = "";
  vz = "";
  if (f < 0) { vz = "-"; }
  f = abs(f);
  r = f % 1000;
  while (f >= 1000){
    k1 = "";
    if ((f % 1000) < 100) { k1 = "0"; }
    if ((f % 1000) < 10) { k1 = "00"; }
    if ((f % 1000) == 0) { k1 = "00"; }
    f = abs((f-(f % 1000)) / 1000);
    r = f % 1000 + "." + k1 + r;
  }
  r = vz + r;
  return r;
}

function abs(a) {
  if(a < 0) return -a;
  return a;
}




































function inc_value(id, max_value, step)
{
  step = step || 1;
  element = document.getElementsByName(id)[0];

  if(parseInt(element.value) + step < max_value)
  {
    element.value = parseInt(element.value) + step;
  }
  else
  {
    element.value = max_value;
  };
}

function dec_value(id, step)
{
  step = step || 1;
  element = document.getElementsByName(id)[0];

  if(parseInt(element.value) > step)
  {
    element.value = parseInt(element.value) - step;
  }
  else
  {
    element.value = 0;
  };
}

function zero_value(id)
{
  element = document.getElementsByName(id)[0];
  if(element)
  {
    element.value = 0;
  }
}

function max_value(id, max_value)
{
  element = document.getElementsByName(id)[0];

  if(element)
  {
    element.value = max_value;
  }
}

function zero_fleet()
{
  for (i in ships)
  {
    zero_value('ships[' + i + ']');
  }
}

function max_fleet()
{
  for (i in ships)
  {
    max_value('ships[' + i + ']', ships[i][0]);
  }
}




















function dec_resource(id)
{
  element = document.getElementsByName('resource' + id)[0];

  if(parseInt(element.value) > 1000)
  {
    element.value = parseInt(element.value) - 1000;
  }
  else
  {
    element.value = 0;
  };
  calculateTransportCapacity();
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

function inc_resource(element, id, inc_value)
{
  alert(element.test);

  if(!inc_value)
  {
    inc_value = 1;
  }
  element = document.getElementsByName('resource' + id)[0];


  if(parseInt(element.value) + 1000 < resource_max[id])
  {
    element.value = parseInt(element.value) + 1000;
  }
  else
  {
    element.value = resource_max[id];
  };
  calculateTransportCapacity();
}

function check_resource(id)
{
  var zi_res = parseInt(document.getElementById("resource" + id).value);
  if (isNaN(zi_res)){
    zi_res = 0;
  }

  document.getElementById('rest_res' + id).innerHTML = sn_format_number(resource_max[id] - zi_res, 0, 'white');

  return zi_res;
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
  var fleet_html = '<table width=100%><tr><td class=c colspan=2>' + language[0] + '</td></tr>';
  var fleet = fleets[fleet_id][0];
  var resources = fleets[fleet_id][1];

  var ship_id;

  for(ship_id in fleet)
  {
    if(fleet[ship_id][1] != 0)
    {
      fleet_html += '<tr><th>';
      switch(fleet[ship_id][0])
      {
        default:
          fleet_html += fleet[ship_id][0];
        break;
      }
      fleet_html += '</th><th>' + fleet[ship_id][1] + '</th></tr>';
    }
  };

  if(parseInt(resources[0]) + parseInt(resources[1]) + parseInt(resources[2]) > 0)
  {
    fleet_html += '<tr><td class=c colspan=2>' + language [1] + '</td></tr>';

    for(res_id in resources)
    {
      if(parseInt(resources[res_id]))
      {
        fleet_html += '<tr><th>' + res_names[res_id] + '</th><th>' + sn_format_number(parseInt(resources[res_id]), 0, 'white') + '</th></tr>';
      }
    }
  }

  fleet_html += '</table>';

  fleet_dialog.dialog( "option", "position", [clientX, clientY + 20]);
  fleet_dialog.dialog("close");
  fleet_dialog.html(fleet_html);
  fleet_dialog.dialog("open");
}

function fleet_dialog_hide()
{
  fleet_dialog.dialog("close");
}