<?php
// Sypex Dumper 2
header("Content-Type: text/html; charset=utf-8");
error_reporting(0);
set_error_handler("sxd_error");
if(!empty($_POST['ajax']['job']) && preg_match("/^[\w-]+$/", $_POST['ajax']['job'])){
	$d = date("'Y.m.d H:i:s'");
	if(!empty($_COOKIE['sxd'])) {
		include('ses.php');
		if(!empty($SES[$_COOKIE['sxd']])) {
			$CFG = &$SES[$_COOKIE['sxd']]['cfg'];
			include(load_lang($SES[$_COOKIE['sxd']]['lng']));
		}
	}
	if(empty($LNG)) {
		include('cfg.php');
		include(load_lang($CFG['lang']));
		if(!empty($CFG['auth'])) {echo "sxd.log.add({$d},[" . esc($LNG['stop_5']) . " (1)]);sxd.hideLoading();"; exit;}
	} 
	$job_name = $_POST['ajax']['job'];
	$log_seek = !empty($_POST['ajax']['lseek']) ? (int) $_POST['ajax']['lseek'] : 0;
	$job_file = "{$CFG['backup_path']}{$job_name}.job.php";
	if (!file_exists($job_file)) exit;
	include($job_file);
	switch($_POST['ajax']['act']) {
		case 'info':
			if (!file_exists($JOB['file_rtl'])) exit;
			$fh = fopen($JOB['file_rtl'], 'r+b');
			$time = time();
			$f = explode("\t", fgets($fh));
			if(empty($f[0])) $f[0] = $time;
			$pt = !empty($f[2]) ? round(100 * $f[10] / $f[2], 2) : 100;
			$pc = !empty($f[8]) ? round(100 * $f[7] / $f[8], 2) : 100;
			$lh = fopen($JOB['file_log'], 'rb');
			fseek($lh, $log_seek);
			$rawlog = fread($lh, 8192);
			$log = '';
			$old_time = '';
			$logs = array();
			if(!empty($rawlog)){
				$temp = explode("\n", $rawlog);
				foreach($temp AS $l){
					if(empty($l)) continue;
					$t = explode("\t", $l);
					if(count($t) < 3) continue;
					if($t[0] != $old_time){
						if(!empty($logs)) $log .= "sxd.log.add('{$old_time}',[" . implode(',', $logs) . "]);";
						$old_time = $t[0];
						$logs = array();
					}
					$logs[] = esc($t[2]);	
				}
				if(!empty($logs)) $log .= "sxd.log.add('{$old_time}',[" . implode(',', $logs) . "]);";
			}
			$log_seek = ftell($lh);
			echo $log . "sxd.job.log_seek = {$log_seek};";
			// Читаем лог
			if($f[4] == 'EOJ') {
				$pt = $pc = 100; 
				fclose($lh);
				fclose($fh);
				if (function_exists('usleep')) usleep(400000);
				else sleep(1);
				if($JOB['act'] == 'backup') $f[3] = filesize(file_exists($JOB['file_name']) ? $JOB['file_name'] : $JOB['file_tmp']);
				// Обновляем список файлов
				if($JOB['act'] == 'backup') print "sxd.actions.filelist(); z('btn_down').file = '{$JOB['file']}'; z('btn_down').style.display = '';";
				echo "sxd.timer.set({$f[0]},{$f[1]},{$pt});sxd.progress.current.set({$pc}, 0, {$f[8]}, {$f[8]});sxd.progress.total.set({$pt},{$f[3]});";
				echo "sxd.log.add({$d},['{$LNG['job_done']}', '{$LNG['js']['records']}: {$f[10]}', '{$LNG['file_size']}: ' + sxd.formatSize({$f[3]},2), '{$LNG['job_time']}: {$f[5]} {$LNG['seconds']}']);sxd.hideLoading();";
				unlink($JOB['file_log']);
				unlink($JOB['file_rtl']);
				unlink($job_file);
			}
			else if($f[9] > 0){
				echo "sxd.log.add({$d},[" . esc($LNG['stop_' . $f[9]]) . "]);" . (($f[9] == 3 || $f[9] == 4) ? 'sxd.resumeJob();' : 'sxd.hideLoading();');
			}
			else{
				if($JOB['act'] == 'backup') $f[3] = filesize(file_exists($JOB['file_name']) ? $JOB['file_name'] : $JOB['file_tmp']);
				if($f[4] != 'EK' && time() > $f[1] + 45) {
					fopen($JOB['file_stp'],'w');
					$f[9] = 0;
					$f[1] = $time;
					fwrite($fh, implode("\t", $f) . "\n");
					echo "sxd.log.add({$d},[" . esc($LNG['job_freeze']) . "]);sxd.hideLoading();z('btn_resume').style.display = '';z('btn_pause').style.display = 'none';";
				}
				echo "sxd.timer.set({$f[0]},{$time},{$pt});sxd.progress.current.set({$pc},0,{$f[7]}, {$f[8]});sxd.progress.total.set({$pt},{$f[3]});";	
			}
		break;
		case 'stop': 
			$fs = fopen($JOB['file_stp'],'w');
			fwrite($fs, 1);
			echo "sxd.log.add({$d},[" . esc($LNG['stop_job']) . "]);";
		break;
		case 'pause': 
			fopen($JOB['file_stp'],'w');
			echo "sxd.log.add({$d},[" . esc($LNG['stop_job']) . "]);";
		break;
	}
}
else echo "sxd.hideLoading();";	

function load_lang($lng_name = 'auto'){
	if($lng_name == 'auto'){
		include('lang/list.php');
		$lng = 'en';
		if(preg_match_all('/[a-z]{2}(-[a-z]{2})?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $m)) {
			foreach($m[0] AS $l){
				if(isset($langs[$l])){
					$lng_name = $l;
					break;
				}
			}
		}
	}
	if(file_exists("lang/lng_{$lng_name}.php")) return "lang/lng_{$lng_name}.php";
	else return "lang/lng_en.php";
}
function esc($str){
	return "'" . addcslashes($str, "\\\0\n\r\t\'") . "'";
}

function sxd_error($errno, $errmsg, $filename, $linenum, $vars){
	global $JOB;
	if($errno == 8192) return;
	if(strpos($errmsg, 'timezone settings')) return;
	if(!empty($JOB['file_stp'])) fopen($JOB['file_stp'],'w');
	$errortype = array(1 => 'Error', 2 => 'Warning', 4 => 'Parsing Error', 8 => 'Notice', 16 => 'Core Error', 32 => 'Core Warning', 64 => 'Compile Error', 
					   128 => 'Compile Warning', 256 => 'User Error', 512 => 'User Warning', 1024 => 'User Notice');
	$str = "{$errortype[$errno]}: {$errmsg} ({$filename}:{$linenum})";
	//error_log("[info.php]\n{$str}\n", 3, "error.log"); 
    if($errno == 8 || $errno == 1024) {
    	echo "sxd.log.add('" . date("Y.m.d H:i:s") . "',[" . esc($str) . "], 4);sxd.hideLoading();";
	}
    elseif($errno < 1024) {
    	echo "sxd.log.add('" . date("Y.m.d H:i:s") . "',[" . esc($str) . "], 4);sxd.hideLoading();";
    	die;
	}
}