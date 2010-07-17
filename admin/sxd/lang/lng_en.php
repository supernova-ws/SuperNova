<?php
// Language File for Sypex Dumper 2
$LNG = array(

// Information about the language file
'ver'				=> 20005, // Dumper version
'translated'		=> 'zapimir (http://sypex.net/)', // Contacts
'name'				=> 'English', // Lang name

// Toolbar
'tbar_backup'		=> 'Export',
'tbar_restore'		=> 'Import', 
'tbar_files'		=> 'Files',
'tbar_services'		=> 'Services',
'tbar_options'		=> 'Options',
'tbar_createdb'		=> 'Create DB',
'tbar_connects'		=> 'Connection',
'tbar_exit'			=> 'Exit',

// Names of objects in the tree
'obj_tables'		=> 'Tables',
'obj_views'			=> 'Views',
'obj_procs'			=> 'Procedures',
'obj_funcs'			=> 'Functions',
'obj_trigs'			=> 'Triggers',
'obj_events'		=> 'Events',

// Export
'zip_max'			=> 'max',
'zip_min'			=> 'min',
'zip_none'			=> 'Uncompressed',
'default'			=> 'default',
'combo_db'			=> 'Database (Schema):', 
'combo_charset'		=> 'Charset:', 
'combo_zip'			=> 'Compression:', 
'combo_comments'	=> 'Comment:',
'del_legend'		=> 'Autodelete if:',
'del_date'			=> 'age of files more than %s days',
'del_count'			=> 'number of files more than %s',
'tree'				=> 'Select objects:',
'no_saved'			=> 'No saved jobs',
'btn_save'			=> 'Save',
'btn_exec'			=> 'Execute',

// Import	
'combo_file'		=> 'File:',
'combo_strategy'	=> 'Restore strategy:',
'ext_legend'		=> 'Extended options:',
'correct'			=> 'Charset correction',
'autoinc'			=> 'Reset AUTO_INCREMENT',

// Log
'status_current'	=> 'Current status:',
'status_total'		=> 'Total status:',
'time_elapsed'		=> 'Elapsed:',
'time_left'			=> 'Left:',
'btn_stop'			=> 'Abort',
'btn_pause'			=> 'Pause',
'btn_resume'		=> 'Resume',
'btn_again'			=> 'Repeat',
'btn_clear'			=> 'Clear log',

// Files
'btn_delete'		=> 'Delete',
'btn_download'		=> 'Download',
'btn_open'			=> 'Open',

// Services
'opt_check'			=> 'Options for Check:',
'opt_repair'		=> 'Options for Repair:',
'btn_delete_db'		=> 'Delete DB',
'btn_check'			=> 'Check',
'btn_repair'		=> 'Repair',
'btn_analyze'		=> 'Analyze',
'btn_optimize'		=> 'Optimize',

// Options
'cfg_legend'		=> 'Basic settings:',
'cfg_time_web'		=> 'Timelimit web (seconds):',
'cfg_time_cron'		=> 'Timelimit cron (seconds):',
'cfg_backup_path'	=> 'Path to backup directory:',
'cfg_backup_url'	=> 'URL to backup directory:',
'cfg_globstat'		=> 'Global statistics:',
'cfg_extended'		=> 'Extended settings:',
'cfg_charsets'		=> 'Charset filter:',
'cfg_only_create'	=> 'Only create types:',
'cfg_auth'			=> 'Authorization chain:',
'cfg_confirm'		=> 'Ask confirmation for:',
'cfg_conf_import'	=> 'import',
'cfg_conf_file'		=> 'file delete',
'cfg_conf_db'		=> 'database delete',

// Connection
'con_header'		=> 'Connection Settings',
'connect'			=> 'Connection',
'my_host'			=> 'Host:',
'my_port'			=> 'Port:',
'my_user'			=> 'User:',
'my_pass'			=> 'Password:',
'my_pass_hidden'	=> 'Password is not shown',
'my_comp'			=> 'Compression protocol',
'my_db'				=> 'Databases:',
'btn_cancel'		=> 'Cancel',

// Save Job
'sj_header'			=> 'Save job',
'sj_job'			=> 'Job',
'sj_name'			=> 'Name (eng.):',
'sj_title'			=> 'Description:',

// Create DB
'cdb_header'		=> 'Create new database',
'cdb_detail'		=> 'Details',
'cdb_name'			=> 'Name:',
'combo_collate'		=> 'Collation:',
'btn_create'		=> 'Create',

// Authorization
'js_required'		=> 'JavaScript must be enabled',
'auth'				=> 'Authorization',
'auth_user'			=> 'User:',
'auth_remember'		=> 'remember',
'btn_enter'			=> 'Enter',
'btn_details'		=> 'Details',

// Log messages
'not_found_rtl'		=> 'RTL-file not exists',
'backup_begin'		=> 'Start export DB `%s`',
'backup_TC'			=> 'Export table `%s`',
'backup_VI'			=> 'Export view `%s`',
'backup_PR'			=> 'Export procedure `%s`',
'backup_FU'			=> 'Export function `%s`',
'backup_EV'			=> 'Export event `%s`',
'backup_TR'			=> 'Export trigger `%s`',
'continue_from'		=> 'from positions %s',
'backup_end'		=> 'Export database `%s` finished.',
'autodelete'		=> 'Autodelete of old files:',
'del_by_date'		=> '- `%s` - deleted (by date)',
'del_by_count'		=> '- `%s` - deleted (by count)',
'del_fail'			=> '- `%s` - delete fail',
'del_nothing'		=> '- no files to delete',
'set_names'			=> 'Set connection encoding: `%s`',
'restore_begin'		=> 'Start import DB `%s`',
'restore_TC'		=> 'Import table `%s`',
'restore_VI'		=> 'Import view `%s`',
'restore_PR'		=> 'Import procedure `%s`',
'restore_FU'		=> 'Import function `%s`',
'restore_EV'		=> 'Import event `%s`',
'restore_TR'		=> 'Import trigger `%s`',
'restore_keys'		=> 'Enable indexes',
'restore_end'		=> 'DB `%s` restored from a backup.',
'stop_1'			=> 'Execution aborted by user', 
'stop_2'			=> 'Execution stopped by user',
'stop_3'			=> 'Execution stopped by timer',
'stop_4'			=> 'Execution stopped by timeout',
'stop_5'			=> 'Execution aborted because of an error',
'job_done'			=> 'Job successful',
'file_size'			=> 'File size',
'job_time'			=> 'Time spent',
'seconds'			=> 'seconds',
'job_freeze'		=> 'The process has not been updated for more than 30 seconds. Click Resume',
'stop_job'			=> 'Stop request',

// For JS
'js' => array(
	
	// Tabs names
	'backup'		=> 'Export database (schema)',
	'restore'		=> 'Import database (schema)',
	'log'			=> 'Log',
	'result'		=> 'Results',
	'files'			=> 'Files',
	'services'		=> 'Services',
	'options'		=> 'Options',

	// Tables header
	'dt'			=> 'Date/time',
	'action'		=> 'Action',
	'db'			=> 'Database',
	'type'			=> 'Type',
	'tab'			=> 'Tabs',
	'records'		=> 'Records',
	'size'			=> 'Size',
	'comment'		=> 'Comments',

	// AJAX Status
	'load'			=> 'Loading',
	'run'			=> 'Running...',
	'sdb'			=> 'Create new database',
	'sc'			=> 'Save connection',
	'sj'			=> 'Save job',
	'so'			=> 'Save options',

	// Messages
	'pro'			=> 'Option available only in Pro-version',
	'err_fopen'		=> 'Unable to open file',
	'err_sxd2'		=> 'View file contents available only for files created by Sypex Dumper 2',
	'err_empty_db'	=> 'Database is empty',
	'fdc'			=> 'Do you really want to delete file?',
	'ddc'			=> 'Do you really want to delete database?',
	'fic'			=> 'Do you really want to import file?',

	// Sizes
	'sizes'			=> array('B', 'KB', 'MB', 'GB'),
)
);
?>
