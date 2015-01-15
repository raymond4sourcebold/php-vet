#!/usr/bin/php
<?php
/**
 * Procedure Scanner
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

chdir('/home/cmuser/dev.contactmaster.biz/cron');

if (file_exists('log/pscan_is_running')) {
	exit;
}
exec('touch log/pscan_is_running');

$timeStart		= microtime(true);

$Db			= new Db;


/**
 * Get settings on criteriaCaption for easy reference.
 */
$cronSession['settings']	= $Db->getCriteriaCaptionSettings();

$counter		= 0;
$errorMsg		= array();



$aProcs			= $Db->getProcsToScan();

foreach ($aProcs as $aProc) {
	/**
	 * Delete non-practice follow-ups
	 */
	if ($aProc['isPractice'] == 0) {
		$nDel		= $Db->deleteFollowUp($aProc['procedureId'], false, false);
		if ($nDel) {
			$errorMsg[]	= "PROC is non-practice: " . $aProc['procName'] . ", FUP deleted: " . $nDel;
		}
		continue;
	}
	
	/**
	 * Delete inactive follow-ups
	 */
	if ($aProc['isActive'] == 0) {
		$nDel		= $Db->deleteFollowUp($aProc['procedureId'], false, false);
		if ($nDel) {
			$errorMsg[]	= "PROC is inactive: " . $aProc['procName'] . ", FUP deleted: " . $nDel;
		}
		continue;
	}
	
	/**
	 * Touch followUps
	 */
	if ($aProc['scanIsNecessary'] == 0) {
		$Db->touchFollowUp($aProc['procedureId']);
		continue;
	}
	
	/**
	 * BUSINESS RULES
	 *
	 *	Pre-qualification to write or not to write a Follow-Up is in Db::writeFollowUpToOwners()
	 *		in File: db_cron_proc_scanner.php
	 */
	
	//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'TEST') . ' >> log/pscan.log');
	
	/**
	 * WRITE FOLLOWUP
	 */
	list($acc, $noChannelOwners)	= $Db->writeFollowUpToOwners($aProc, $cronSession);
	
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
		. ($acc['nXr_reminder']	? ", DelRem: " . $acc['nXr_reminder']	: NULL);
	
	/**
	 * Update procedure.scannerEvalDate
	 */
	$Db->setProcEvalDate($aProc['procedureId']);
	
	$counter++;
}

if ($nDel = $Db->discardFollowUps()) {
	$errorMsg[]	= 'Discarded FUP: ' . $nDel;
}

exec('echo ' . date('Y-m-d h:i:s')
	. ', Sec: ' . round((microtime(true) - $timeStart), 2) 
	. ' >> log/pscan.log');

if ($errorMsg) {
	foreach ($errorMsg as $err) {
		exec('echo ... ' . $err . ' >> log/pscan.log');
	}
}

exec('rm log/pscan_is_running');
// end of process


/**
 * For auto-loading classes
 */
function __autoload($class_name)
{
	if ($class_name == 'Db') {
		include '../system/models/db_cron_proc_scanner.php';
		
	} elseif ($class_name == 'Model_Base') {
		include '../system/models/model_base.php';
	}
}

?>
