#!/usr/bin/php
<?php
/**
 * Date Scanner
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

if (file_exists('log/dscan_is_running')) {
	exit;
}
exec('touch log/dscan_is_running');

$timeStart	= microtime(true);

$Db		= new Db;

/**
 * Get settings on criteriaCaption for easy reference.
 */
$cronSession['settings']	= $Db->getCriteriaCaptionSettings();

if ($cronSession['settings']['dead']) {
	$deathCriteriaCode	= 'dead';
} else {
	$deathCriteriaCode	= 'death';
}

if (!$cronSession['settings'][$deathCriteriaCode]) {
	/**
	 * There is no death criteria code, the Db is not initialized with data yet.  Let's stop here.
	 */
	exec('echo .......Not ready: no death criteria code >> log/dscan.log');
	exec('rm log/dscan_is_running');
	exit;
}

$aMsgs		= $Db->getMatureMessages($cronSession['settings'][$deathCriteriaCode]);

$aDoneC11n_csv	= array();

$counter	= 0;
$echoMsg	= array();

$aPerClientQuota	= array();

foreach ($aMsgs as $aMessage) {
	
	/**
	 * CONTROL FLOW
	 */
	 
	// Is sending status stopped?
	if ($aMessage['noMessage'] == 1) {
		$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
		$echoMsg[]	= "Owner sending status set to STOP [CLIENT: {$aMessage['clientId']}]";
		continue;
	}
	
	// Send even if animal is dead
	if ($aMessage['isAnimalDead']) {
		if ($aMessage['sendOnDeath'] == 0) {
			$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
			$echoMsg[]	= "Dead animal [PROC: {$aMessage['procedureId']}, ANIMAL: {$aMessage['animalId']}, CLIENT: {$aMessage['clientId']}, MSG: {$aMessage['messageId']}]";
			continue;
		}
	}
	
	// Consolidation (Proc)
	if ($aMessage['consolidate']) {
		// Consolidation check.  Let's see if this row is already processed
		if (in_array($aMessage['procedureId'] . ',' . $aMessage['clientId'] . ',' . $aMessage['messageId'], $aDoneC11n_csv)) {
			if ($aMessage['isRecurring']) {
				$Db->setProcForRecurScanning($aMessage['procedureId']);
			}
			// Already processed, disregard this one.
			continue;
		}
	}
	
	// Practice Minimum Priority (Subscriber)
	if ($aMessage['priority'] < $aMessage['sendThreshold']) {
		$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
		$echoMsg[]	= "Priority lower than Subscriber Threshold [PROC: {$aMessage['procedureId']}, CLIENT: {$aMessage['clientId']}, MSG: {$aMessage['messageId']}]: {$aMessage['priority']} \< {$aMessage['sendThreshold']}";
		continue;
	}
	
	// Client Minimum Priority (Client)
	if ($aMessage['priority'] < $aMessage['messageThreshold']) {
		$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
		$echoMsg[]	= "Priority lower than Client Threshold [PROC: {$aMessage['procedureId']}, CLIENT: {$aMessage['clientId']}, MSG: {$aMessage['messageId']}]: {$aMessage['priority']} \< {$aMessage['messageThreshold']}";
		continue;
	}
	
	// Per Client Quota (Subscriber)
	if (!isset($aPerClientQuota[$aMessage['subscriberId']]['pcq_status'])) {
		if (!isset($aPerClientQuota[$aMessage['subscriberId']])) {
			$aTmp		= explode(':', $aMessage['perClientQuotaCFlow']);
			
			if (isset($aTmp[0]) && isset($aTmp[1]) && isset($aTmp[2])) {
				/**
				 * Assign the value for a subscriber only once.
				 */
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_limit_priority']	= $aTmp[0];
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_duration']		= $aTmp[1];
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_qty']			= $aTmp[2];
			} else {
				$aPerClientQuota[$aMessage['subscriberId']]			= false;
			}
		}
		
		if ($aPerClientQuota[$aMessage['subscriberId']]) {
			list(
				$isFull, 
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_no_successfully_sent']
			) = $Db->isPerClientQuotaFull($aMessage['clientId'], $aPerClientQuota[$aMessage['subscriberId']]);
			
			if (is_null($isFull)) {
				// DO NOT DELETE, LET'S WAIT FOR THE MESSAGES TO BE PROCESSED BY TELECOM PROCESS.
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_status']	= 'TELECOM_IS_BUSY';
				$echoMsg[]	= "Subscriber: {$aMessage['subscriberId']} per client quota may be full after Telecom Process is done processing [CLIENT: {$aMessage['clientId']} MSG PRIORITY: {$aMessage['priority']}]";
				continue;
			}
			
			if ($isFull === true) {
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_status']	= 'IT_IS_FULL';
				$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
				$echoMsg[]	= "Subscriber: {$aMessage['subscriberId']} per client quota is full [CLIENT: {$aMessage['clientId']} MSG PRIORITY: {$aMessage['priority']}]";
				continue;
			}
			
			if ($isFull === false) {
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_status']	= 'GO_SEND';
			}
		}
	} else {
		if ($aPerClientQuota[$aMessage['subscriberId']]['pcq_status'] == 'GO_SEND') {
			if ($aPerClientQuota[$aMessage['subscriberId']]['pcq_qty'] <= $aPerClientQuota[$aMessage['subscriberId']]['pcq_no_successfully_sent']) {
				$aPerClientQuota[$aMessage['subscriberId']]['pcq_status']	= 'IT_IS_FULL';
			}
		}
		if ($aPerClientQuota[$aMessage['subscriberId']]['pcq_status'] == 'IT_IS_FULL') {
			$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
			$echoMsg[]	= "Subscriber: {$aMessage['subscriberId']} per client quota is full [CLIENT: {$aMessage['clientId']} MSG PRIORITY: {$aMessage['priority']}]";
			continue;
		}
		if ($aPerClientQuota[$aMessage['subscriberId']]['pcq_status'] == 'TELECOM_IS_BUSY') {
			$echoMsg[]	= "Subscriber: {$aMessage['subscriberId']} per client quota may be full after Telecom Process is done processing [CLIENT: {$aMessage['clientId']} MSG PRIORITY: {$aMessage['priority']}]";
			continue;
		}
	}
	// End of Control Flow
	
	
	if ($aMessage['overrideChannelId'] == 1) {
		// Email
		$channel		= 'Email';
		$deliveryNoOrAddr	= $aMessage['email'];
		
	} elseif ($aMessage['overrideChannelId'] == 2) {
		// SMS	cell
		$channel		= 'SMS';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], 'mobile');
		
	} elseif ($aMessage['overrideChannelId'] == 3) {
		// Voice	cell
		$channel		= 'Voice';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], 'mobile');
		
	} elseif ($aMessage['overrideChannelId'] == 4) {
		// Voice	home
		$channel		= 'Voice';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], array('homeph', 'homepf'));
		
	} elseif ($aMessage['overrideChannelId'] == 5) {
		// Voice	office
		$channel		= 'Voice';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], array('ofisph', 'ofispf'));
		
	} elseif ($aMessage['overrideChannelId'] == 6) {
		// Snail mail	home
		$channel		= 'Snail mail';
		$deliveryNoOrAddr	= $aMessage['homeAddress1'] . ', '
			. $aMessage['homeAddress2'] ? $aMessage['homeAddress2'] . ', ' : NULL
			. $aMessage['homeCity'] . ' '
			. $aMessage['homePostalCode'] . ' '
			. $aMessage['homeProvinceOrState'] . ', '
			. $aMessage['country'];
		
	} elseif ($aMessage['overrideChannelId'] == 7) {
		// Snail mail	office
		$channel		= 'Snail mail';
		$deliveryNoOrAddr	= $aMessage['officeAddress1'] . ', '
			. $aMessage['officeAddress2'] ? $aMessage['officeAddress2'] . ', ' : NULL
			. $aMessage['officeCity'] . ' '
			. $aMessage['officePostalCode'] . ' '
			. $aMessage['officeProvinceOrState'] . ', '
			. $aMessage['country'];
		
	} elseif ($aMessage['overrideChannelId'] == 8) {
		// Fax	home
		$channel		= 'Fax';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], array('homefx', 'homepf'));
		
	} elseif ($aMessage['overrideChannelId'] == 9) {
		// Fax	office
		$channel		= 'Fax';
		$deliveryNoOrAddr	= $Db->getPhone($aMessage['clientId'], array('ofisfx', 'ofispf'));
	}
	
	if (!$deliveryNoOrAddr || !$channel) {
		$echoMsg[]		= $aMessage['followUpId'] . " --incomplete-- To: $deliveryNoOrAddr, Channel: $channel";
		continue;
	}
	
	/**
	 * REMINDER Control Flow
	 */
	if ($aMessage['nthReminder']) {
		/**
		 * This message is a reminder.
		 */
		if ($Db->doNotSendReminder($cronSession['settings'], $aMessage['animalId'], $aMessage['reminderTargetEventDateId'], $aMessage['refDate'], $aMessage['offset'], $aMessage['reminderDurationNdays'])) {
			$echoMsg[]	= 'Reminder not sent, PROC: ' . $aMessage['procedureId'] . ', ANIMAL: ' . $aMessage['animalId'];
			continue;
		}
	}
	
	/**
	 * WRITE TO SENDING QUEUE
	 */
	if ($Db->writeOnQueue(
			$channel, 
			$deliveryNoOrAddr, 
			$Db->replaceMessageTags($aMessage['msgText'], $aMessage['honoraryId'], $aMessage['lastName'], $aMessage['animalName']),
			$aMessage['subscriberId'], 
			$aMessage['clientId'], 
			$aMessage['animalId'],
			$aMessage['procedureId']
		)
	){
		$aPerClientQuota[$aMessage['subscriberId']]['pcq_no_successfully_sent']++;
	}
	
	/**
	 * Consolidation is done by deleting consolidated rows on Table: followUp
	 *	and not processing rows with the same ProcId, ClientId, and MsgId.
	 */
	if ($aMessage['consolidate']) {
		$nDelRows	= $Db->deleteConsolidatedFollowUp($aMessage['procedureId'], $aMessage['clientId'], $aMessage['messageId']);
		$echoMsg[]	= "Consolidated [PROC: {$aMessage['procedureId']}, CLIENT: {$aMessage['clientId']}, MSG: {$aMessage['messageId']}]: "
				. $nDelRows;
		
		$aDoneC11n_csv[]	= $aMessage['procedureId'] . ',' . $aMessage['clientId'] . ',' . $aMessage['messageId'];
	} else {
		$Db->closeFollowUp($aMessage['followUpId'], $aMessage['isRecurring'], $aMessage['procedureId']);
	}
	
	$counter++;
}

exec('echo ' . date('Y-m-d h:i:s') . '", "' . $counter 
	. ' Sec: ' . round((microtime(true) - $timeStart), 2) . ' >> log/dscan.log');

if ($echoMsg) {
	foreach ($echoMsg as $err) {
		exec('echo .......' . $err . ' >> log/dscan.log');
	}
}

exec('rm log/dscan_is_running');
// end of process


/**
 * For auto-loading classes
 */
function __autoload($class_name)
{
	if ($class_name == 'Db') {
		include '../system/models/db_cron_date_scanner.php';
		
	} elseif ($class_name == 'Model_Base') {
		include '../system/models/model_base.php';
	}
}

?>
