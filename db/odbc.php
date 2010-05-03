<?
function doquery($query, $table, $fetch = false){
  global $numqueries,$link,$debug,$ugamela_root_path;
	require($ugamela_root_path.'config.php');

	if(!$link)
	{$link = mysql_connect($dbsettings["server"], $dbsettings["user"], 
				$dbsettings["pass"]) or
				$debug->error(mysql_error()."<br />$query","SQL Error");

		odbc_select_db($dbsettings["name"]) or $debug->error(mysql_error()."<br />$query","SQL Error");}

	$sql = str_replace("{{table}}", $dbsettings["prefix"].$table, $query);
	$sqlquery = mysql_query($sql) or 
				$debug->error(mysql_error()."<br />$sql<br />","SQL Error");

	unset($dbsettings);
	$numqueries++;
  $arr = debug_backtrace();
  $file = end(explode('/',$arr[1]['file']));
  $line = $arr[1]['line'];
$debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$file($line)</th><th>$table</th><th>$fetch</th></tr>");

	if($fetch)
	{$sqlrow = mysql_fetch_array($sqlquery);
		return $sqlrow;
	}else{return $sqlquery;}}
?>