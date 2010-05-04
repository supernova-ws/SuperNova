<?
if (ini_get('register_globals') != 1){
if ((isset($_POST) == true) && (is_array($_POST) == true)) extract($_POST, EXTR_OVERWRITE);
if ((isset($_GET) == true) && (is_array($_GET) == true)) extract($_GET, EXTR_OVERWRITE);}
foreach ($_GET as $u_url){
if ((eregi("<[^>]*script*\"?[^>]*>", $u_url)) || (eregi("<[^>]*object*\"?[^>]*>", $u_url)) ||
(eregi("<[^>]*iframe*\"?[^>]*>", $u_url)) || (eregi("<[^>]*applet*\"?[^>]*>", $u_url)) ||
(eregi("<[^>]*meta*\"?[^>]*>", $u_url)) || (eregi("<[^>]*style*\"?[^>]*>", $u_url)) ||
(eregi("<[^>]*form*\"?[^>]*>", $u_url)) || (eregi("\([^>]*\"?[^)]*\)", $u_url)) ||
(eregi("\"", $u_url))){
die ();}}
unset($u_url);
function czysc_url($url){
$bad_entities = array("&", "\"", "'", '\"', "\'", "<", ">", "(", ")");
$safe_entities = array("&amp;", "", "", "", "", "", "", "", "");
$url = str_replace($bad_entities, $safe_entities, $url);
return $url;}
$_SERVER['PHP_SELF'] = czysc_url($_SERVER['PHP_SELF']);
$_SERVER['QUERY_STRING'] = isset($_SERVER['QUERY_STRING']) ? czysc_url($_SERVER['QUERY_STRING']) : "";
$_SERVER['REQUEST_URI'] = isset($_SERVER['REQUEST_URI']) ? czysc_url($_SERVER['REQUEST_URI']) : "";
$PHP_SELF = czysc_url($_SERVER['PHP_SELF']);
function is__Num($value){
return (preg_match("/^[0-9]+$/", $value));}
function verify_image($file){ 
$txt = file_get_contents($file); 
$image_safe = true; 
if (preg_match('#&(quot|lt|gt|nbsp);#i', $txt)) {$image_safe = false;} 
elseif (preg_match("#&\#x([0-9a-f]+);#i", $txt)) {$image_safe = false;} 
elseif (preg_match('#&\#([0-9]+);#i', $txt)) {$image_safe = false;} 
elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $txt)) {$image_safe = false;} 
elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $txt)) {$image_safe = false;} 
elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $txt)) {$image_safe = false;} 
elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $txt)) {$image_safe = false;} 
elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $txt)) {$image_safe = false;} 
elseif (preg_match("#</*(applet|link|style|script|iframe|frame|frameset)[^>]*>#i", $txt)) {$image_safe = false;} 
return $image_safe;}
?>