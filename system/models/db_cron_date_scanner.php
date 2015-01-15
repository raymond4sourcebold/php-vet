<?php
/**
 * Model file for cron: Date Scanner.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    model
 */

/**
 * Include superclass Model_Cron
 */
require 'model_cron.php';

/**
 * Class of database access for cron: Date Scanner.
 * @package    Db_Scanner_Class
 */
class Db extends Model_Cron
{
	/**
	 * Gets mature messages from Table: followUp
	 * @param $evtDeathId Death Event ID.
	 * @return array
	 */
	public function getMatureMessages($evtDeathId)
	{
		$sql	= "SELECT (IF (followUp.isCustomMessage, customMessage.customMsgBody, message.messageBody)) AS msgText,
				followUp.*,
				client.*,
				animal.animalName,
				deathEvent.criteriaIsTrue AS isAnimalDead,
				subscriber.subscriberId,
				subscriber.sendThreshold,
				subscriber.perClientQuotaCFlow
			FROM followUp 
			LEFT JOIN message ON message.messageId = followUp.messageId
			LEFT JOIN customMessage ON customMessage.customMessageId = followUp.messageId
			LEFT JOIN client ON client.clientId = followUp.clientId
			LEFT JOIN animal ON animal.animalId = followUp.animalId
			LEFT JOIN criteriaEvent AS deathEvent ON (
				deathEvent.criteriaCaptionId = {$evtDeathId}
			    AND deathEvent.animalId = followUp.animalId
			)
			LEFT JOIN subscriber ON subscriber.subscriberId = followUp.subscriberId
			WHERE followUp.sendDate <= CURDATE()
			";
		return $this->fetchAll($sql);
	}
	
	public function getPhone($clientId, $phoneType)
	{
		if (is_array($phoneType)) {
			$orCond			= '';
			foreach ($phoneType as $pt) {
				$orCond		.= $orCond ? ' OR ' : NULL;
				$orCond		.= " phoneType = '{$pt}' ";
			}
			$typeCond		= "AND ({$orCond})";
			
		} else {
			$typeCond		= "AND phoneType = '{$phoneType}'";
		}
		
		$sql	= "SELECT phoneNumber FROM clientPhone
			WHERE clientId = '{$clientId}' {$typeCond}
			ORDER BY priority LIMIT 1";
		$aTmp	= $this->fetchAll($sql);
		
		return $aTmp[0]['phoneNumber'];
	}
	
	public function writeOnQueue($channel, $deliveryNoOrAddr, $message, $subscriberId, $clientId, $animalId, $procId)
	{
		$sql	= "INSERT INTO messageQueueLog SET
			messageStatusId = 0,
			sendUnixtime = 0,
			channel = '{$channel}',
			recipientContactInfo = '{$deliveryNoOrAddr}',
			message = :message,
			subscriberId = '{$subscriberId}',
			clientId = '{$clientId}',
			animalId = '{$animalId}',
			procedureId = '{$procId}'";
		$sth	= $this->dbc->prepare($sql);
		$sth->bindParam(':message', $message);
		return $sth->execute();
	}
	
	public function replaceMessageTags($message, $nHonorary, $owner, $animal)
	{
		return str_replace(
			array('[honorary/]', '[owner/]', '[animal/]'),
			array($this->getHonorary($nHonorary), $owner, $animal),
			$message
		);
	}
	
	/**
	 * Checks if (CommPlan) per client quota is already full
	 * @return boolean
	 */
	public function isPerClientQuotaFull($clientId, $aPerClientQuota)
	{
		// quota duration (w = week, m = month, q = quarter, s = semester, y = year)
		if ($aPerClientQuota['pcq_duration'] == 'w') {
			$nDays		= 7;
			
		} elseif ($aPerClientQuota['pcq_duration'] == 'm') {
			$nDays		= 365 / 12;
			
		} elseif ($aPerClientQuota['pcq_duration'] == 'q') {
			$nDays		= 365 / 4;
			
		} elseif ($aPerClientQuota['pcq_duration'] == 's') {
			$nDays		= 365 / 2;
			
		} elseif ($aPerClientQuota['pcq_duration'] == 'y') {
			$nDays		= 365;
			
		} else {
			return false; // Invalid duration code, return false.
		}
		
		$nSeconds	= $nDays * 86400;
		
		$sql	= "SELECT messageStatusId,
				COUNT(*)
			FROM messageQueueLog
			WHERE clientId = {$clientId}
				AND priority <= {$aPerClientQuota['pcq_limit_priority']}
				AND (messageStatusId = 10 OR messageStatusId = 0)
				AND sendUnixtime > (UNIX_TIMESTAMP() - $nSeconds)
			GROUP BY messageStatusId
			"; // messageStatusId = 10 - successfully sent, 0 - yet to be processed by Telecom Process
			
		$sth	= $this->dbc->prepare($sql);
		$sth->execute();
		
		$nSuccessfullySent	= 0;
		$nYetToBeProcess	= 0;
		
		while ($row = $sth->fetch(PDO::FETCH_NUM)) {
			if ($row[0] == 10) {
				// messageStatusId = 10 successful
				$nSuccessfullySent	= $row[1];
			} else {
				// messageStatusId = 0  yet to be processed
				$nYetToBeProcess	= $row[1];
			}
		}
		
		if ($nSuccessfullySent >= $aPerClientQuota['pcq_qty']) {
			// The quota is full.
			$isFull		= true;
		} elseif (($nSuccessfullySent + $nYetToBeProcess) < $aPerClientQuota['pcq_qty']) {
			// Safe to say that the quota is not full.
			$isFull		= false;
		} else {
			// Some messages are yet to be processed which could make the quota full if successfully sent.
			$isFull			= NULL;
		}

		return array($isFull, $nSuccessfullySent);
	}
	
	public function closeFollowUp($followUpId, $isRecurring, $procedureId)
	{
		$sql	= "DELETE FROM followUp WHERE followUpId = '{$followUpId}'";
		$this->dbc->exec($sql);
		
		if ($isRecurring) {
			$this->setProcForRecurScanning($procedureId);
		}
	}
	
	/**
	 * Sets a recurring proc to be picked by Proc Scanner.
	 *	By setting scannerEvalDate = '2001-01-01 11:11:11'
	 *		the Proc Scanner will pick this procedure on next scan.
	 *	Also '2001-01-01 11:11:11' means that this procedure needs to get a future send date 
	 *		by a call to getNextRecurDate().
	 */
	public function setProcForRecurScanning($procedureId)
	{
		$sql	= "UPDATE `procedure` SET scannerEvalDate = '2000-01-01 11:11:11' WHERE procedureId = {$procedureId}";
		$this->dbc->exec($sql);
	}
	
	public function deleteConsolidatedFollowUp($procedureId, $clientId, $messageId)
	{
		$sql	= "DELETE FROM followUp 
			WHERE procedureId = '{$procedureId}'
				AND clientId = '{$clientId}'
				AND messageId = '{$messageId}'
			";
		return $this->dbc->exec($sql);
	}
	
	/**
	 * IF ((TargetEventDate - ReferenceDate) - Offset) <= Reminder_Duration
	 *     DO NOT SEND REMINDER
	 */
	public function doNotSendReminder($cronSession, $animalId, $reminderTargetEventDateId, $refDate, $offset, $reminderDurationNdays)
	{
		$unixTargetEventDate		= strtotime($this->getCriterionDateValue($animalId, $reminderTargetEventDateId, $cronSession));
		
		if (is_numeric($refDate)) {
			// This is a Date ID
			$unixReferenceDate	= strtotime($this->getCriterionDateValue($animalId, $refDate, $cronSession));
		} else {
			// This is YYYY-MM-DD
			$unixReferenceDate	= strtotime($refDate);
		}
		
		$nDaysOffset	= eval(
			str_replace(
				array(':d', ':w', ':m', ':y'), 
				array(' * 1', ' * 7', ' * 30', ' * 365'), 
				$offset
			)
		);
		
		$reminderDurationNdays	= eval(
			str_replace(
				array(':d', ':w', ':m', ':y'), 
				array(' * 1', ' * 7', ' * 30', ' * 365'), 
				$reminderDurationNdays
			)
		);
		
		/**
		 * Here we go: computation and check
		 */
		if ((($unixTargetEventDate - $unixReferenceDate) - $nDaysOffset) <= $reminderDurationNdays) {
			// DO NOT SEND REMINDER
			return true;
		}
		
		return false;
	}
}

// eof
