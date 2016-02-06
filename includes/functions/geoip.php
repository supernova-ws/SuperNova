<?php

function geoip_status(){return sn_function_call(__FUNCTION__, array(&$result));}
function sn_geoip_status(&$result) {
  return $result = false;
}

function geoip_ip_info($ip){return sn_function_call(__FUNCTION__, array($ip, &$result));}
function sn_geoip_ip_info($ip, &$result) {
  return $result = false;
}
