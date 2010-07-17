<?php
if(!empty($_SERVER['QUERY_STRING']) && preg_match("/^(\w+)(\.v\d+)?\.(lng\.js|js|css|gif|png|ico)$/", $_SERVER['QUERY_STRING'], $m)){
	$compress = true;
	$skin = '';
	$file = $skin;
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 1209600) . ' GMT');
	header('Cache-Control: max-age=1209600, public');
	switch($m[3]) {
		case 'css': $type = 'text/css; charset=UTF-8'; break;
		case 'js':  $type = 'application/x-javascript; charset=UTF-8'; break;
		case 'lng.js': 
			header('Content-Type: application/x-javascript; charset=UTF-8');
			if(!ini_get('zlib.output_compression') && function_exists('ob_gzhandler')) ob_start('ob_gzhandler');
			include("lang/lng_{$m[1]}.php");
			echo 'sxdlng = ' . sxd_php2json($LNG['js']) . ';';
			exit;
		case 'png': $file = 'img/'; $type = 'image/png';$compress = false; break;
		case 'gif': $file = 'img/'; $type = 'image/gif';$compress = false; break;
		case 'ico': $file = ''; $type = 'image/x-icon';$compress = false; break;
	}	
	$file .= $m[1] . '.' . $m[3];
	if(is_file($file)){
		if($compress) if(!ini_get('zlib.output_compression') && function_exists('ob_gzhandler')) ob_start('ob_gzhandler');
		header('Content-Type: ' . $type);
		readfile($file);
	}
}
function sxd_php2json($obj){
	if(count($obj) == 0) return '[]';
	$is_obj = isset($obj[count($obj) - 1]) ? false : true;
	$str = $is_obj ? '{' : '[';
    foreach ($obj AS $key  => $value) {
    	$str .= $is_obj ? "'" . addcslashes($key, "\n\r\t'\\/") . "'" . ':' : ''; 
        if     (is_array($value))   $str .= sxd_php2json($value);
        elseif (is_null($value))    $str .= 'null';
        elseif (is_bool($value))    $str .= $value ? 'true' : 'false';
		elseif (is_numeric($value)) $str .= $value;
		else                        $str .= "'" . addcslashes($value, "\n\r\t'\\/") . "'";
		$str .= ',';
    }
	return  substr_replace($str, $is_obj ? '}' : ']', -1);
}
?>