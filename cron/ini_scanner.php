#!/usr/bin/php
<?php
/**
 * INI Scanner
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    scanner
 */

// Get site path
$site_path	= realpath(dirname(__FILE__) . '/' . '..' . '/') . '/';
define ('site_path', $site_path);

chdir(site_path . 'cron');

if (file_exists(site_path . 'cron/log/iscan_is_running')) {
	exit;
}
exec('touch ' . site_path . 'cron/log/iscan_is_running');

exec('echo STARTING ' . date('Y-m-d h:i:s')
	. ', Sec: ' . round((microtime(true) - $timeStart), 2) 
	. ' >> ' . site_path . 'cron/log/iscan.log');


$timeStart		= microtime(true);

$Db			= new Db;

while (1) {
	/**
	 * This while loop is perpetual.  Sleep(1) puts the delay of 1 seconds in between execution.
	 */
	sleep(1);
	
	$aFiles			= scandir(site_path . 'ini_uploads');

	foreach ($aFiles as $filename) {
		if ($filename == '.' || $filename == '..') {
			continue;
		}
		
		if (strtolower(substr($filename, -4)) != '.ini') {
			continue;
		}
		
		if ($Db->writeIniFilename($filename) == 0) {
			/**
			 * If an INI file is already processed, it will be disregarded.
			 */
			//~ $errorMsg[]	= ' -';
			continue;
		}
		
		//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), site_path . $filename) . ' >> /tmp/iscan.log');
		
		$result	= $Db->processIniFile($filename);
		
		//~ $errorMsg[]	= $result . ': ' . $filename;
		exec('echo ... ' . $result . ': ' . $filename . ' >> ' . site_path . 'cron/log/iscan.log');
	}
}

//~ if ($errorMsg) {
	//~ foreach ($errorMsg as $err) {
		//~ exec('echo ... ' . $err . ' >> log/iscan.log');
	//~ }
//~ }
 // forever loop

exec('rm ' . site_path . 'cron/log/iscan_is_running');
// end of process


/**
 * For auto-loading classes
 */
function __autoload($class_name)
{
	if ($class_name == 'Db') {
		include '../system/models/db_cron_ini_scanner.php';
		
	} elseif ($class_name == 'Model_Base') {
		include '../system/models/model_base.php';
	}
}

?>
