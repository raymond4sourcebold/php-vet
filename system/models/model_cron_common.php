<?php
/**
 * Class file for common Cron DB getters and setters.
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
 * Include Model_Base superclass
 */
if (defined('site_path')) {
	require site_path . 'system/models/model_base.php';
} else {
	require 'model_base.php';
}

/**
 * Class for common Cron DB getters and setters.
 * @package    super_class
 */
class Model_Cron_Common extends Model_Base
{
	protected $timeNow;
	
	function __construct()
	{
		parent::__construct();
		$this->timeNow		= time();
	}
	
	public function getProcsToScan($subscriberId=NULL)
	{
		/**
		 * Change Log:
		 * 1. Removed condition:
		 *	AND (
		 *		`procedure`.lastUpdateDate > `procedure`.scannerEvalDate
		 *	    OR
		 *		(`procedure`.procSteps = 'group' AND `procedure`.recurringPeriod != '')
		 *	    OR
		 *		`procedure`.procSteps = 'recur'
		 *	    OR
		 *	        (`procedure`.procSteps = 'two' AND `procedure`.singleRefDateUsed = 1)
		 *	)
		 *	
		 * 2. Removed condition: `procedure`.isPractice = 1 AND `procedure`.isActive = 1
		 *
		 *	So that non-practice and inactive Procs will be fetch and processed for the 
		 *	purpose of DELETING follow-Ups.
		 *
		 * 3. Removed condition: `procedure`.lastUpdateDate > `procedure`.scannerEvalDate
		 *
		 *	So that all existing follow-ups can be touched by the Proc Scanner.
		 *	Touching follow-ups mean updating the followUp.scanTouchTime
		 */
		$sql	= "SELECT `procedure`.*, 
				message.messageChannel,
				reminder.messageChannel	AS reminderMsgChannel,
				IF (`procedure`.lastUpdateDate > `procedure`.scannerEvalDate, 1, 0) AS scanIsNecessary
			FROM `procedure`
			LEFT JOIN message		ON message.messageId = `procedure`.messageId
			LEFT JOIN message AS reminder	ON reminder.messageId = `procedure`.reminderMessageId1 "
			. ($subscriberId
				? ' WHERE `procedure`.subscriberId = ' . $subscriberId 
				: ' ORDER BY `procedure`.subscriberId'
			  );
		return $this->fetchAll($sql);
	}
	
	public function getOwners($subscriberId, $clientId=NULL)
	{
		$cond		= '';
		if ($subscriberId) {
			$cond	= ' WHERE subscriberId = ' . $subscriberId . ' ';
		}
		if ($clientId) {
			$cond	.= ($cond == '' ? ' WHERE ' : ' AND ');
			$cond	.= ' clientId = ' . $clientId;
		}
		
		$sql	= "SELECT * FROM client " . $cond;
		
		return $this->fetchAll($sql);
	}
	
	public function getAnimalsOfOwner($animalId, $clientId, $condSpecie, $condGender, $condBooleanJoin, $condEvtBooleanJoin, $condQuantityJoin)
	{
		$sql	= "SELECT animal.*, specie.specieName, race.raceName
			FROM animal
			LEFT JOIN specie on specie.specieId = animal.specieId
			LEFT JOIN race on race.raceId = animal.raceId
			{$condBooleanJoin}
			{$condEvtBooleanJoin}
			{$condQuantityJoin}
			WHERE animal.clientId = '{$clientId}' "
			. ($animalId ? ' AND animal.animalId = ' . $animalId . ' ' : NULL)
			. "	{$condSpecie}
				{$condGender}
			ORDER BY animalName
			";
		return $this->fetchAll($sql);
	}
	
	/**
	 * Saves a new follow up
	 * @return boolean
	 */
	public function saveFollowUp($messageId, $animalId, $clientId, $sendDate, $subscriberId, $nPriority, $procId, $consolidate, $sendOnDeath, $channelId, $isRecurring, 
		$goInsert=false, $nthReminder=0, $reminderTargetEventDateId=NULL, $refDate=NULL, $offset=NULL, $reminderDurationNdays=NULL)
	{
		/**
		 * nthReminder = {$nthReminder} means that we are checking for the existence of
		 *	(a) Primary Message, (b) Reminder 1, and (c) Reminder 2
		 *	If it exists, we do SQL update, else, it would be SQL insert.
		 */
		$sql	= "SELECT 1 FROM followUp
			WHERE
				procedureId = {$procId}
			    AND animalId = {$animalId}
			    AND clientId = {$clientId}
			    
			    AND nthReminder = {$nthReminder}
			    
			LIMIT 1
			";
			
		$tmp	= $this->fetchAll($sql);
		
		if (isset($tmp[0])) {
			/**
			 * Row exists, let's update.
			 */
			$sql	= "UPDATE followUp SET
				subscriberId = {$subscriberId},
				messageId = {$messageId},
				consolidate = {$consolidate},
				sendOnDeath = {$sendOnDeath},
				priority = {$nPriority},
				sendDate = '{$sendDate}',
				overrideChannelId = {$channelId},
				isRecurring = {$isRecurring},
				reminderTargetEventDateId = " . ($reminderTargetEventDateId ? $reminderTargetEventDateId : 'NULL') . ", 
				refDate = " . ($refDate ? "'$refDate'" : 'NULL') . ", 
				offset = " . ($offset ? "'$offset'" : 'NULL') . ", 
				reminderDurationNdays = " . ($reminderDurationNdays ? "'$reminderDurationNdays'" : "''") . ",
				scanTouchTime = " . $this->timeNow . "
			WHERE
				procedureId = {$procId}
			    AND animalId = {$animalId}
			    AND clientId = {$clientId}
			    
			    AND nthReminder = {$nthReminder}
			    
			LIMIT 1";
			
			if ($this->safeExec($sql) == 1) {
				return 'UPDATED';
			}
			return 'NO_UPDATE';
		}
		
		$sql	= "INSERT INTO followUp SET
			procedureId = {$procId},
			animalId = {$animalId},
			clientId = {$clientId},
			subscriberId = {$subscriberId},
			messageId = {$messageId},
			consolidate = {$consolidate},
			sendOnDeath = {$sendOnDeath},
			priority = {$nPriority},
			sendDate = '{$sendDate}',
			overrideChannelId = {$channelId},
			nthReminder = {$nthReminder},
			isRecurring = {$isRecurring},
			reminderTargetEventDateId = " . ($reminderTargetEventDateId ? $reminderTargetEventDateId : 'NULL') . ", 
			refDate = " . ($refDate ? "'$refDate'" : 'NULL') . ", 
			offset = " . ($offset ? "'$offset'" : 'NULL') . ", 
			reminderDurationNdays = " . ($reminderDurationNdays ? "'$reminderDurationNdays'" : "''") . ",
			scanTouchTime = " . $this->timeNow . "
		";
		
		if ($this->safeExec($sql) == 1) {
			return 'NEW';
		}
		
		return false;
	}
	
	/**
	 * Deletes existing follow-ups that are cancelled because of updated criteria or updated values.
	 */
	public function deleteFollowUp($procId, $animalId, $clientId)
	{
		if ($animalId === false) {
			$condAnimal	= '';
		} else {
			$condAnimal	= ' AND animalId = ' . $animalId;
		}
		
		if ($clientId === false) {
			$condClient	= '';
		} else {
			$condClient	= ' AND clientId = ' . $clientId;
		}
		
		/**
		 * Delete includes both ORIGINAL messages AND its REMINDERS.
		 */
		$sql	= "DELETE FROM followUp
			WHERE procedureId = {$procId}
				{$condAnimal}
				{$condClient}
			";
		return $this->safeExec($sql);
	}
	
	/**
	 * Deletes existing reminders that are cancelled because of Clients execution of requested action.
	 */
	public function deleteFupReminder($procId, $animalId, $clientId)
	{
		if ($animalId === false) {
			$condAnimal	= '';
		} else {
			$condAnimal	= ' AND animalId = ' . $animalId;
		}
		
		if ($clientId === false) {
			$condClient	= '';
		} else {
			$condClient	= ' AND clientId = ' . $clientId;
		}
		
		/**
		 * Delete REMINDERS only
		 */
		$sql	= "DELETE FROM followUp
			WHERE procedureId = {$procId}
				AND nthReminder != 0
				{$condAnimal}
				{$condClient}
			";
		return $this->safeExec($sql);
	}
	
	/**
	 * Gets the date from criteria tables (YYYY-MM-DD format)
	 */
	public function getCriterionDateValue($animalId, $criteriaCaptionId, $cronSession=NULL)
	{
		/**
		 * If Cron Session is not passed, let's use our Class copy.
		 * TODO: For all code to use Class copy instead of passing a copy around.
		 */
		//~ if (is_null($cronSession)) {
			//~ $cronSession		= $this->aCaptionSettings;
		//~ }
		
		//~ if (in_array($criteriaCaptionId, $cronSession)) {
			//~ // Condition is an event
			//~ $sql	= "SELECT criteriaDateValue 
				//~ FROM criteriaEvent 
				//~ WHERE animalId = {$animalId} 
					//~ AND criteriaCaptionId = {$criteriaCaptionId}";
		//~ } else {
			//~ // Condition is a date
			//~ $sql	= "SELECT criteriaDateValue 
				//~ FROM criteriaDate 
				//~ WHERE animalId = {$animalId} 
					//~ AND criteriaCaptionId = {$criteriaCaptionId}";
		//~ }
		
		$sql	= "SELECT criteriaDate.criteriaDateValue,
				criteriaEvent.criteriaDateValue AS criteriaEdateValue
			FROM criteriaCaption
			LEFT JOIN criteriaDate  ON (criteriaDate.criteriaCaptionId  = criteriaCaption.criteriaCaptionId AND criteriaDate.animalId  = {$animalId})
			LEFT JOIN criteriaEvent ON (criteriaEvent.criteriaCaptionId = criteriaCaption.criteriaCaptionId AND criteriaEvent.animalId = {$animalId})
			WHERE criteriaCaption.criteriaCaptionId = {$criteriaCaptionId}
			LIMIT 1
			";
		
		$rows	= $this->fetchAll($sql);
		
		if (isset($rows[0])) {
			if ($rows[0]['criteriaDateValue']) {
				return $rows[0]['criteriaDateValue'];
				
			} elseif ($rows[0]['criteriaEdateValue']) {
				return $rows[0]['criteriaEdateValue'];
			}
		}
		
		return false;
	}
	
	public function touchFollowUp($procId)
	{
		$sql	= "UPDATE followUp SET scanTouchTime = " . $this->timeNow . " WHERE procedureId = " . $procId;
		$this->safeExec($sql);
	}
	
	public function discardFollowUps($procedureId=NULL, $animalId=NULL)
	{
		if ($procedureId) {
			$cond		= " procedureId = " . $procedureId;
		} else {
			$cond		= " procedureId != 0 AND scanTouchTime < " . $this->timeNow; //($this->timeNow - 359);
		}
		
		if ($animalId) {
			$cond		.= " AND animalId = " . $animalId;
		}
		
		$sql	= "DELETE FROM followUp WHERE " . $cond;
		return $this->safeExec($sql);
	}
	
	/**
	 * Checks if a message is sent today by Procedure to Animal
	 * @return boolean
	 */
	protected function messageSentTodayByProc($animalId, $procedureId)
	{
		$unixStartOfDay	= mktime(0, 0, 0, date('m')  , date('d'), date('Y'));

		$sql	= "SELECT 1
			FROM messageQueueLog
			WHERE animalId = {$animalId}
			AND procedureId = {$procedureId}
			AND sendUnixtime > {$unixStartOfDay}";
		$aRow	= $this->fetchAll($sql);
		return isset($aRow[0]);
	}
}

// eof
