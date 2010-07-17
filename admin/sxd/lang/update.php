<?php
// Sypex Dumper 2 langfile updater
$path = './';
$langlist = array();
if (false !== ($handle = opendir($path))) {
    while (false !== ($file = readdir($handle))) {
        if (preg_match("/^lng_([a-z]{2}(-[a-z]{2})?)\.php$/", $file, $m)) {
            include($path . $file);
            $langlist[$m[1]] = $LNG['name'];
        }
    }
    closedir($handle);
    if(count($langlist) > 0){
    	$fp = fopen('list.php', 'w');
    	fwrite($fp, "<?php\n\$langs = " . var_export($langlist, 1) . ";\n?>");
	}
}
echo 'Langlist updated';
?>
