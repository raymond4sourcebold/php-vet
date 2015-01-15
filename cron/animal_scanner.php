#!/usr/bin/php
<?php
/**
 * Animal Scanner
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

$timeStart		= microtime(true);

$site_path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
/**
 * Constant site_path to hold exact site path
 */
define ('site_path', $site_path);

if (file_exists(site_path . 'cron/log/ascan_is_running')) {
	exit;
}
exec('touch ' . site_path . 'cron/log/ascan_is_running');

if (strlen($argv[1]) > 4) {
	writeLog(date('Y-m-d h:i:s') . ', Sec: ' . round((microtime(true) - $timeStart), 2));
	writeLog($argv[1]. '......ERROR: Animal Scanner cannot run because no Animal ID is supplied.');
	exit;
}

$animalId		= $argv[1];
$errorMsg		= array();


$Db			= new Db;

/**
 * Get settings on criteriaCaption for easy reference.
 */
$cronSession		= $Db->getCriteriaCaptionSettings();

$tmp			= $Db->getParentIdsOfAnimal($animalId);
$subscriberId		= $tmp['subscriberId'];
$clientId		= $tmp['clientId'];

$aProcs			= $Db->getProcsToScan($subscriberId);

foreach ($aProcs as $aProc) {
	if ($aProc['isPractice'] == 0 || $aProc['isActive'] == 0) {
		/**
		 * $aProc['scanIsNecessary'] test is not needed here because this is a specific requested scan for an Animal.
		 */
		continue; // Disregard.  Proc Scanner will do the deleting of follow-ups.
	}
	
	list($acc, $noChannelOwners, $nDel)	= $Db->writeAnimalFollowUp($aProc, $cronSession, $animalId, $clientId);
	
	$errorMsg[]	= "PROC: " 	. str_pad(substr($aProc['procName'], 0, 40), 40, '.') 
		. ", Own: " 		. $acc['nOwners']
		. ", Ani: " 		. $acc['nAnimals']
		/**
		 * Follow-Up
		 */
		. ", FUP N: " 		. $acc['nNewFollowUps']
			//. " _O: " 	. $acc['nNoUpFollowUps']
			. " _Upd: " 	. $acc['nUpdatedFollowUps']
		/**
		 * Follow-Up Reminders
		 */
		. ", REM N: " 		. $acc['nReminders']
			. ($acc['nNoUpReminders'] 	? " _O: " 	. $acc['nNoUpReminders'] 		: NULL)
			. ($acc['nUpReminders'] 	? " _Upd: " 	. $acc['nUpReminders'] 			: NULL)
		/**
		 * Deletion, errors, and others
		 */
		. ($acc['nErrorOnSave'] 		? ", Err: " 	. $acc['nErrorOnSave'] 			: NULL)
		. ($acc['nX_cantDetermineSendDate'] 	? ", Xsd: " 	. $acc['nX_cantDetermineSendDate'] 	: NULL)
		. ($acc['nXr_cantDetermineSendDate'] 	? ", Xsdr: " 	. $acc['nXr_cantDetermineSendDate'] 	: NULL)
		. ($acc['nX_failedUnlessCond'] 		? ", Xun: " 	. $acc['nX_failedUnlessCond'] 		: NULL)
		. ($acc['nXr_failedUnlessCond'] 	? ", Xunr: " 	. $acc['nXr_failedUnlessCond'] 		: NULL)
		. ($acc['nX_datesame'] 			? ", DteS: " 	. $acc['nX_datesame'] 			: NULL)
		. ($acc['nX_datepast'] 			? ", DteP: " 	. $acc['nX_datepast'] 			: NULL)
		. ($noChannelOwners 			? ", noChannelOwners: " . $noChannelOwners 		: NULL)
		. ($nDel		? ", DelFU: " . $nDel			: NULL)
		. ($acc['nXr_reminder']	? ", DelRem: " . $acc['nXr_reminder']	: NULL);
}


writeLog(date('Y-m-d h:i:s') . ', Sec: ' . round((microtime(true) - $timeStart), 2));
writeLog('......Animal ID: ' . $animalId . ', Subscriber: ' . $subscriberId);

if ($errorMsg) {
	foreach ($errorMsg as $err) {
		writeLog($err);
	}
}

exec('rm ' . site_path . 'cron/log/ascan_is_running');
// end of process


/**
 * For auto-loading classes
 */
function __autoload($class_name)
{
	include site_path . 'system/models/db_cron_animal_scanner.php';
}

/**
 * Writes to log file
 */
function writeLog($str)
{
	if (is_writable(site_path . 'cron/log/ascan.log')) {
		/**
		 * File: ascan.log is the log file for execution calls from web application.
		 */
		exec('echo ' . $str . ' >> ' . site_path . 'cron/log/ascan.log');
	} else {
		/**
		 * File: iascan.log is the log file for execution calls from INI cron.
		 */
		exec('echo ' . $str . ' >> ' . site_path . 'cron/log/iascan.log');
	}
}

?>