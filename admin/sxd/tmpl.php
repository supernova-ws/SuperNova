<?php
// Templates
function sxd_tpl_page(){
global $SXD;
return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$SXD->name}</title>
<link rel="stylesheet" type="text/css" href="load.php?sxd.v208.css">
<script type="text/javascript" src="load.php?sxd.v208.js"></script>
<script type="text/javascript" src="load.php?{$SXD->LNG['name']}.lng.js"></script>
<link rel="shortcut icon" href="load.php?favicon.v208.ico">
</head>

<body>
<div id="main_div">
	<div id="header">{$SXD->name}</div>
	<div id="sxdToolbar"></div>
	<div id="name"><div id="loading"></div><b></b></div>
	<div id="content" class="content">
		<table cellspacing="0" id="tab_backup">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="backup_db"></div>
					<div class="caption">{$SXD->LNG['combo_charset']}</div><div id="backup_charset"></div>
					<div class="caption">{$SXD->LNG['combo_zip']}</div><div id="backup_zip"></div>
					<div class="caption">{$SXD->LNG['combo_comments']}</div>
					<div class="border"><textarea cols="10" rows="3" id="backup_comment"></textarea></div>
					<div class="caption" style="margin-top:12px;">
						<fieldset><legend>{$SXD->LNG['del_legend']}</legend>
						<div class="caption">&nbsp;– {$SXD->LNG['del_date']}</div>
						<div class="caption">&nbsp;– {$SXD->LNG['del_count']}</div>
						</fieldset>
					</div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=backup_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td></td><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.showDialog('savejob');z('sj_name').value = sxd.combos.backup_db.value;"> <input type="button" value="{$SXD->LNG['btn_exec']}" onclick="sxd.runBackup();"></td></tr>
		</table>
		
		<table cellspacing="0" id="tab_restore" style="display:none;">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="restore_db"></div>
					<div class="caption">{$SXD->LNG['combo_charset']}</div><div id="restore_charset"></div>
					<div class="caption">{$SXD->LNG['combo_file']}</div><div id="restore_file"></div>
					<div class="caption">{$SXD->LNG['combo_comments']}</div>
					<div class="border"><textarea cols="10" rows="3" id="restore_comment" readonly></textarea></div>
					<div class="caption">{$SXD->LNG['combo_strategy']}</div><div id="restore_type"></div>
					<div class="caption" style="margin-top:17px;">
						<fieldset><legend>{$SXD->LNG['ext_legend']}</legend>
						<div class="caption"><label><input type="checkbox" id="correct"> {$SXD->LNG['correct']}</label></div>
						<div class="caption"><label><input type="checkbox" id="autoinc"> {$SXD->LNG['autoinc']}</label></div>
						</fieldset>
					</div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=restore_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td></td><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.showDialog('savejob');z('sj_name').value = sxd.combos.restore_db.value;" id=restore_savejob> <input type="button" value="{$SXD->LNG['btn_exec']}" onclick="sxd.runRestore();" id=restore_runjob></td></tr>
		</table>
		
		<table cellspacing="0" id="tab_log" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div id=sxdGrid1></div> 
				</td>
			</tr>
			<tr><td colspan=2>
			<table class=progress>
				<tr><td>{$SXD->LNG['status_current']}</td><td><div id="sxdProc1"></div></td><td width=60>{$SXD->LNG['time_elapsed']}</td><td width=40 align=right id="sxdTime1">00:00</td></tr>
				<tr><td>{$SXD->LNG['status_total']}</td><td><div id="sxdProc2"></div></td><td>{$SXD->LNG['time_left']}</td><td align=right id="sxdTime2">00:00</td></tr>
			</table>
			</td></tr>
			<tr><td width="152"><input type="button" value="{$SXD->LNG['btn_clear']}" onclick="sxd.log.clear();"></td><td width="380" align="right">
			<input type="button" value="{$SXD->LNG['btn_download']}" id="btn_down" onclick="sxd.runFiles('download', this.file);" style="display:none;">
			<input type="button" value="{$SXD->LNG['btn_again']}" id="btn_again" onclick="sxd.runAgain();" disabled>
			<input type="button" value="{$SXD->LNG['btn_stop']}" id="btn_stop" onclick="sxd.stopJob();" disabled>
			<input type="button" value="{$SXD->LNG['btn_pause']}" id="btn_pause" onclick="sxd.pauseJob();" disabled>
			<input type="button" value="{$SXD->LNG['btn_resume']}" id="btn_resume" onclick="sxd.resumeJob();" style="display:none;">
			</td></tr>
		</table>
		<table cellspacing="0" id="tab_result" style="display:none;">
			<tr>
				<td valign="top">
					<div id=sxdGrid3></div> 
				</td>
			</tr>
			<tr><td>
			<input type="button" value="{$SXD->LNG['btn_clear']}" onclick="sxd.result.clear();">
			</td></tr>
		</table>
		
		<table cellspacing="0" id="tab_files" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div id=sxdGrid2></div> 
				</td>
			</tr>
			<tr><td width="242"><form id="save_file" method="GET" style="visibility:hidden;display:inline;" target=save></form><input type="button" value="{$SXD->LNG['btn_delete']}" onclick="sxd.runFiles('delete')"></td><td width="290" align="right">
			<input type="button" value="{$SXD->LNG['btn_download']}" onclick="sxd.runFiles('download')">
			<input type="button" value="{$SXD->LNG['btn_open']}" onclick="sxd.runFiles('open')">
			</td></tr>
		</table>
		
		
		<table cellspacing="0" id="tab_services" style="display:none;">
			<tr>
				<td width="242" valign="top">
					<div class="caption">{$SXD->LNG['combo_db']}</div><div id="services_db"></div>
					<br>
					<div class="caption">{$SXD->LNG['opt_check']}</div><div id="services_check"></div>
					<div class="caption">{$SXD->LNG['opt_repair']}</div><div id="services_repair"></div>
				</td>
				<td width="308" valign="top">
					<div class="caption">{$SXD->LNG['tree']}</div><div id=services_tree class="zTree"></div>
				</td>
			</tr>
			<tr><td align="right" colspan=2><input type="button" value="{$SXD->LNG['btn_delete_db']}" onclick="sxd.runServices('delete')" style="float:left;"> <input type="button" value="{$SXD->LNG['btn_check']}" onclick="sxd.runServices('check')"> <input type="button" value="{$SXD->LNG['btn_repair']}" onclick="sxd.runServices('repair')"> <input type="button" value="{$SXD->LNG['btn_analyze']}" onclick="sxd.runServices('analyze')">  <input type="button" value="{$SXD->LNG['btn_optimize']}" onclick="sxd.runServices('optimize')"></td></tr>
		</table>
		<table cellspacing="0" id="tab_options" style="display:none;">
			<tr>
				<td valign="top" colspan=2>
					<div style="height: 341px;">
					<fieldset>
					<legend>{$SXD->LNG['cfg_legend']}</legend>
						<table cellspacing="0">
							<tr>
								<td width=190>{$SXD->LNG['cfg_time_web']}</td>
								<td width=45><input type="text" id="time_web" style="width:40px;"></td>
								<td align="right">{$SXD->LNG['cfg_time_cron']}</td>
								<td width=40 align="right"><input type="text" id="time_cron" style="width:40px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_backup_path']}</td>
								<td colspan=3><input type="text" id="backup_path" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_backup_url']}</td>
								<td colspan=3><input type="text" id="backup_url" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_globstat']}</td>
								<td colspan=3><input type="checkbox" id="globstat" value="1"></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
					<legend>{$SXD->LNG['cfg_confirm']}</legend>
						<table cellspacing="0">
							<tr>
								<td width="33%"><label><input type="checkbox" id="conf_import" value="1"> {$SXD->LNG['cfg_conf_import']}</label></td>
								<td width="34%"><label><input type="checkbox" id="conf_file" value="1"> {$SXD->LNG['cfg_conf_file']}</label></td>
								<td width="33%"><label><input type="checkbox" id="conf_db" value="1"> {$SXD->LNG['cfg_conf_db']}</label></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
					<legend>{$SXD->LNG['cfg_extended']}</legend>
						<table cellspacing="0">
							<tr>
								<td width=190>{$SXD->LNG['cfg_charsets']}</td>
								<td><input type="text" id="charsets" value="" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_only_create']}</td>
								<td><input type="text" id="only_create" value="" style="width:351px;"></td>
							</tr>
							<tr>
								<td>{$SXD->LNG['cfg_auth']}</td>
								<td><input type="text" id="auth" value="" style="width:351px;"></td>
							</tr>
						</table>
					</fieldset>
					</div>
				</td>
			</tr>
			<tr><td align="right" colspan=2><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveOptions();"></td></tr>
		</table>
	</div>
</div>

<div id="overlay"></div>
<div class="dialog" id ="dia_connect">
	<div class="header">{$SXD->LNG['con_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['connect']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['my_host']}</td>
							<td width="126"><input type="text" id="con_host" style="width:120px;"></td>
							<td width="40" align="right">{$SXD->LNG['my_port']}</td>
							<td width="36"><input type="text" id="con_port" maxlength="5" style="width:30px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_user']}</td>
							<td colspan="3"><input type="text" id="con_user" name="user" style="width:202px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_pass']}</td>
							<td colspan="3"><input type="password" id="con_pass" name="pass" title="{$SXD->LNG['my_pass_hidden']}" style="width:202px;" onchange="this.changed = true;"></td>
						</tr>
						<tr>
							<td></td>
							<td colspan="3"><label><input type="checkbox" id="con_comp" value="1"> {$SXD->LNG['my_comp']}</label></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_db']}</td>
							<td colspan="3"><input type="text" id="con_db" style="width:202px;"></td>
						</tr>
					</table></fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveConnect();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('connect');"></td></tr>
		</table>
	</div>
</div>
<div class="dialog" id ="dia_savejob">
	<div class="header">{$SXD->LNG['sj_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['sj_job']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['sj_name']}</td>
							<td><input type="text" id="sj_name" style="width:202px;" maxlength="12" value=""></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['sj_title']}</td>
							<td><input type="text" id="sj_title" maxlength="64" style="width:202px;"></td>
						</tr>
					</table></fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_save']}" onclick="sxd.saveJob();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('savejob');"></td></tr>
		</table>
	</div>
</div>
<div class=dialog id="dia_createdb">
	<div class="header">{$SXD->LNG['cdb_header']}</div>
	<div class="content">
		<table cellspacing="5">
			<tr>
				<td valign="top">
				<fieldset>
				<legend>{$SXD->LNG['cdb_detail']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="80">{$SXD->LNG['cdb_name']}</td>
							<td width="202"><input type="text" id="db_name" value="my_db_1" style="width:202px;"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['combo_charset']}</td>
							<td><div id="db_charset"></div></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['combo_collate']}</td>
							<td><div id="db_charset_col"></div></td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="right"><input type="button" value="{$SXD->LNG['btn_create']}" onclick="sxd.addDb();"><input type="button" value="{$SXD->LNG['btn_cancel']}" onclick="sxd.hideDialog('createdb');"></td></tr>
		</table>
	</div>
</div>

<div id=sxdMenu style="display:none;z-index:9999;"></div>
<script type="text/javascript">
sxd.init();
sxd.backupUrl = '{$SXD->CFG['backup_url']}';
sxd.tbar.init('sxdToolbar', {$SXD->VAR['toolbar']}); 
{$SXD->VAR['combos']}
sxd.actions.tab_backup();
</script>
</body>
</html>
HTML;
}

function sxd_tpl_auth($error = ''){
global $SXD;
return <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">  
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$SXD->name}</title>
<link rel="shortcut icon" href="load.php?favicon.v208.ico">
<link rel="stylesheet" type="text/css" href="load.php?sxd.v208.css">
</head>
<body>
<div class="dialog" id="dia_auth">
	<div class="header"><a href="http://sypex.net/">{$SXD->name}</a></div>
	<div class="content" id="div_1" style="line-height:50px;text-align:center;">{$SXD->LNG['js_required']}</div>
	<div class="content" id="div_2" style="display:none;">
		<form method="post">
		<table cellspacing="5">
			<tr>
				<td valign="top" colspan="3">
				<fieldset>
				<legend>{$SXD->LNG['auth']}</legend>
					<table cellspacing="3">
						<tr>
							<td width="90">{$SXD->LNG['auth_user']}</td>
							<td width="192"><input type="text" name="user" value="{$_POST['user']}" class="i202"></td>
						</tr>
						<tr>
							<td>{$SXD->LNG['my_pass']}</td>
							<td><input type="password" name="pass" value="{$_POST['pass']}" class="i202"></td>
						</tr>
						<tr>
							<td></td>
							<td><label><input type="checkbox" name="save" value="1"{$_POST['save']}> {$SXD->LNG['auth_remember']}</label></td>
						</tr>
						<tr>
							<td>Language:</td>
							<td><select type="text" name="lang" style="width:198px;" onChange="this.form.submit();">{$SXD->lng_list}</select></td>
						</tr>
					</table>
					<table cellspacing="3" id="hst" style="display:none;">
						<tr>
							<td width="90">{$SXD->LNG['my_host']}</td>
							<td width="116"><input type="text" name="host" style="width:110px;" value="{$_POST['host']}"></td>
							<td width="40" align="right">{$SXD->LNG['my_port']}</td>
							<td width="36"><input type="text" name="port" maxlength="5" style="width:30px;" value="{$_POST['port']}"></td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
			<tr class="buttons"><td align="left"><input type="button" value="{$SXD->LNG['btn_details']}" onclick="var s = document.getElementById('hst').style; s.display = (s.display == 'block') ? 'none' : 'block';"></td><td align="right"><input type="submit" value="{$SXD->LNG['btn_enter']}"></td></tr>
		</table>
		</form>
	</div>
	<script type="text/javascript">document.getElementById('div_1').style.display = 'none';document.getElementById('div_2').style.display = 'block';</script>
</div>
</body>
</html>
HTML;
}