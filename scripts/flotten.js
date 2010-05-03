function speed() {
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

function maxspeed() {
  var msp = 1000000000;
  for (i = 200; i < 220; i++) {
    if (document.getElementsByName("ship" + i)[0]) {
      if ((document.getElementsByName("speed" + i)[0].value * 1) >= 1
      && (document.getElementsByName("ship" + i)[0].value * 1) >= 1) {
        msp = min(msp, document.getElementsByName("speed" + i)[0].value);
      }
    }
  }

  return(msp);
}

function distance() {
  var thisGalaxy;
  var thisSystem;
  var thisPlanet;

  var targetGalaxy;
  var targetSystem;
  var targetPlanet;

  var dist;

  thisGalaxy = document.getElementsByName("thisgalaxy")[0].value;
  thisSystem = document.getElementsByName("thissystem")[0].value;
  thisPlanet = document.getElementsByName("thisplanet")[0].value;

  targetGalaxy = document.getElementsByName("galaxy")[0].value;
  targetSystem = document.getElementsByName("system")[0].value;
  targetPlanet = document.getElementsByName("planet")[0].value;

  dist = 0;
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
  var speedfactor;

  speedfactor = document.getElementsByName("speedfactor")[0].value;
  msp = maxspeed();
  sp = speed();
  dist = distance();

  ret = Math.round(((35000 / sp * Math.sqrt(dist * 10 / msp) + 10) / speedfactor ));
  return ret;
}

function consumption2() {
  var consumption;
  var basicConsumption = 0;

  for (i = 200; i < 220; i++) {
    if (document.getElementsByName("ship" + i)[0]) {
      basicConsumption = basicConsumption +
      document.getElementsByName("consumption" + i)[0].value
      * document.getElementsByName("ship" + i)[0].value;
    }
  }

  speedfactor = document.getElementsByName("speedfactor")[0].value;
  msp = maxspeed();
  sp = speed();
  dist = distance();

  consumption = Math.round(basicConsumption * dist / 35000 * ((sp / 10) + 1) * ((sp / 10) + 1)) + 1;

  return(consumption);
}

function consumption() {
  var consumption = 0;
  var basicConsumption = 0;
  var values;
  var i;

  msp = maxspeed();
  sp = speed();
  dist = distance();
  dur = duration();
  speedfactor = document.getElementsByName("speedfactor")[0].value;

  for (i = 200; i < 220; i++) {
    if (document.getElementsByName("ship" + i)[0]) {
      shipspeed = document.getElementsByName("speed" + i)[0].value;
      spd = 35000 / (dur * speedfactor - 10) * Math.sqrt(dist * 10 / shipspeed);

      //spd = Math.max(msp / document.getElementsByName("speed" + i)[0].value, 0.1);
      //spd = Math.min(spd, 1.0);
      //spd = spd * sp;
      //spd = 10;
      basicConsumption = document.getElementsByName("consumption" + i)[0].value
      * document.getElementsByName("ship" + i)[0].value;
      consumption += basicConsumption * dist / 35000 * ((spd / 10) + 1) * ((spd / 10) + 1);
      //      values = values + " " + spd;
    }
  }

  consumption = Math.round(consumption) + 1;

  //  document.write(values);
  document.getElementById("debug").innerHTML = consumption;


  return(consumption);
}

function probeConsumption() {
  var consumption = 0;
  var basicConsumption = 0;
  var values;
  var i;

  msp = maxspeed();
  sp = speed();
  dist = distance();
  dur = duration();
  speedfactor = document.getElementsByName("speedfactor")[0].value;


  if (document.getElementsByName("ship210")[0]) {
    shipspeed = document.getElementsByName("speed210")[0].value;
    spd = 35000 / (dur * speedfactor - 10) * Math.sqrt(dist * 10 / shipspeed);

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
}


function fleetInfo() {
  document.getElementById("speed").innerHTML = speed() * 10 + "%";
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
  document.getElementById("maxspeed").innerHTML = tsdpkt(maxspeed());
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


  document.getElementById("maxspeed").innerHTML = tsdpkt(maxspeed());
  if (stor >= 0) {
    document.getElementById("consumption").innerHTML = '<font color="lime">'+tsdpkt(cons)+'</font>';
    document.getElementById("storage").innerHTML = '<font color="lime">'+tsdpkt(stor)+'</font>';
  } else {
    document.getElementById("consumption").innerHTML = '<font color="red">'+tsdpkt(cons)+'</font>';
    document.getElementById("storage").innerHTML = '<font color="red">'+tsdpkt(stor)+'</font>';
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
    thisresourcechosen=0;
  }
  if (isNaN(thisresource)){
    thisresource=0;
  }

  var storCap = storage();
  if (id==3){
    thisresource -= consumption();
  }

//  document.getElementById("debug").innerHTML = storCap;

  var metalToTransport = parseInt(document.getElementsByName("resource1")[0].value);
  var crystalToTransport = parseInt(document.getElementsByName("resource2")[0].value);
  var deuteriumToTransport = parseInt(document.getElementsByName("resource3")[0].value);

  if (isNaN(metalToTransport)){
    metalToTransport=0;
  }
  if (isNaN(crystalToTransport)){
    crystalToTransport=0;
  }
  if (isNaN(deuteriumToTransport)){
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
  var metal = Math.abs(document.getElementsByName("resource1")[0].value);
  var crystal = Math.abs(document.getElementsByName("resource2")[0].value);
  var deuterium = Math.abs(document.getElementsByName("resource3")[0].value);

  transportCapacity =  storage() - metal - crystal - deuterium;

  if (transportCapacity < 0) {
    document.getElementById("remainingresources").innerHTML="<font color=red>"+transportCapacity+"</font>";
  } else {
    document.getElementById("remainingresources").innerHTML="<font color=lime>"+transportCapacity+"</font>";
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


/*
function disableSome() {
document.forms[0].mission[6].disabled = true;
document.forms[0].mission[7].disabled = true;
document.forms[0].mission[8].disabled = true;
}
*/
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
  speedfactor = document.getElementsByName("speedfactor")[0].value;

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
      spd, speedfactor), time);
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
spd, maxspeed, speedfactor) {
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
  return Math.round(((35000 / spd * Math.sqrt(dist * 10 / maxspeed) + 10) / speedfactor));
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