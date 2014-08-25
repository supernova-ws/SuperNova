<?php

function geoip_status(){return sn_function_call('geoip_status', array(&$result));}
function sn_geoip_status(&$result) {
  return $result = false;
}

function geoip_ip_info($ip){return sn_function_call('geoip_ip_info', array($ip, &$result));}
function sn_geoip_ip_info($ip, &$result) {
  return $result = false;
}
