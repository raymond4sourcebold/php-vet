<?php
/**
 * Model file for cron: Procedure Scanner.
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
 * Class of database access for cron: Procedure Scanner.
 * @package    db_scanner_class
 */
class Db extends Model_Cron
{
	var $todayYmd;
	var $nowTime;
	
	function __construct()
	{
		parent::__construct();
		$this->todayYmd		= date('Y-m-d');
		$this->nowTime		= time();
	}
	
	public function writeFollowUpToOwners($aProc, $cronSession)
	{
		/**
		 * Initialize our accumulators
		 */
		$acc['nOwners']			= 0;
		$acc['nAnimals']		= 0;
		$acc['nNewFollowUps']		= 0;
		$acc['nUpdatedFollowUps']	= 0;
		$acc['nNoUpFollowUps']		= 0;
		$acc['nReminders']		= 0;
		$acc['nUpReminders']		= 0;
		$acc['nNoUpReminders']		= 0;
		$acc['nErrorOnSave']		= 0;
		
		$acc['nX_cantDetermineSendDate']	= 0; // Number of animal instances
		$acc['nX_failedUnlessCond']		= 0;
		
		$acc['nX_datesame']			= 0; // Number of animal instances
		$acc['nX_datepast']			= 0;
		
		$acc['nXr_cantDetermineSendDate']	= 0; // Number of rows deleted which includes reminders
		$acc['nXr_failedUnlessCond']		= 0;
		
		$acc['nXr_reminder']		= 0;
		
		$noChannelOwners		= '';
		
		
		/**
		 * Determine whether this Proc will recur or not.
		 */
		$isRecurring			= $this->isProcRecurring($aProc['procSteps'], $aProc['recurringPeriod']);
		
		$aProcDefCrit			= $this->buildCriteriaFromProcDef($aProc, $cronSession);
		
		$condSpecie		= $aProcDefCrit['condSpecie'];
		$condGender		= $aProcDefCrit['condGender'];
		$condBooleanJoin	= $aProcDefCrit['condBooleanJoin'];
		$condEvtBooleanJoin	= $aProcDefCrit['condEvtBooleanJoin'];
		$condQuantityJoin	= $aProcDefCrit['condQuantityJoin'];
		
		
		// UNLESS Criteria B
		if ($aProc['unlessCriteriaDateIdCsvc']) {
			$aUnlessOrCrit		= explode(',', $aProc['unlessCriteriaDateIdCsvc']);
			
			foreach ($aUnlessOrCrit as $aCri) {
				list($dtMinuen, $dtSubtrahen, $op, $nDays)	= explode(':', $aCri);
				
				$aDtMinuen[]		= $dtMinuen;
				$aDtSubtrahen[]		= $dtSubtrahen;
				$aOp[]			= $op;
				$nDaysUnixValue[]	= $nDays * 86400;
			}
		}
		
		
		$aOwners		= $this->getOwners($aProc['subscriberId']);
		
		foreach ($aOwners as $aOwner) {
			
			++$acc['nOwners'];
			
			$channelId	= $this->getAppropriateChannelId(
				$aProc['messageChannel'],
				$aOwner['usePreferredExclusively'],
				$aOwner['preferredChannelId'],
				$aOwner['backupChannelId'],
				$aOwner['nixedChannelIdCsv']
			);
			
			if ($channelId == 0) {
				$noChannelOwners	.= ($noChannelOwners ? ', ' : NULL) . "{$aOwner['clientId']} NOT {$aProc['messageChannel']}";
				continue;
			}
			
			// Reminder message channel
			$reminderChannelId	= $this->getAppropriateChannelId(
				$aProc['reminderMsgChannel'],
				$aOwner['usePreferredExclusively'],
				$aOwner['preferredChannelId'],
				$aOwner['backupChannelId'],
				$aOwner['nixedChannelIdCsv']
			);
			
			$aAnimals	= $this->getAnimalsOfOwner(NULL, $aOwner['clientId'], $condSpecie, $condGender, $condBooleanJoin, $condEvtBooleanJoin, $condQuantityJoin);
			
			foreach ($aAnimals as $aAnimal) {
				
				if ($aProc['recurringPeriod']) {
					/**
					 * DETERMINE SEND DATE: RECURRING
					 */
					
					/**
					 * '2000-01-01 11:11:11' means that this Proc is now recurring.
					 */
					$unixSendDate	= $this->getNextSendDate($cronSession['settings'], $aProc, $aAnimal['animalId']);
					
					if ($unixSendDate == 0) {
						// Disregard, send date cannot be determined because of Animal's missing fields.
						++$acc['nX_cantDetermineSendDate'];
						$acc['nXr_cantDetermineSendDate'] += $this->deleteFollowUp($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
						continue;
					}
					
					$sendDate		= date('Y-m-d', $unixSendDate);
					
					$reminderReferenceDate	= $sendDate;
					
				} else {
					/**
					 * DETERMINE SEND DATE: NON-RECURRING
					 */
					if ($aProc['procSteps'] == 'group') {
						$sendDate		= $aProc['groupSendDate'];
						$unixSendDate           = strtotime($sendDate);
						
						if ($this->usableAsSendDate($unixSendDate, $aProc['scannerEvalDate'], $aProc['lastUpdateDate'], $aAnimal['animalId'], $aProc['procedureId']) == false) {
							/**
							 * Send date is in the past, no hope for this one because it's not recurring.
							 */
							++$acc['nX_cantDetermineSendDate'];
							$acc['nXr_cantDetermineSendDate'] += $this->deleteFollowUp($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
							continue;
						}
						
						$reminderReferenceDate	= $aProc['groupSendDate'];
						
					} else {
						if ($aProc['procSteps'] == 'one') {
							/**
							 * One-Step Procedure
							 */
							$unixSendDate			= $this->computeDerivedSendDate($aAnimal['animalId'], $aProc['refDateId'], $cronSession['settings'], $aProc['offset'], $aProc['anticipation']);
							
							if ($this->usableAsSendDate($unixSendDate, $aProc['scannerEvalDate'], $aProc['lastUpdateDate'], $aAnimal['animalId'], $aProc['procedureId']) == false) {
								/**
								 * Send date is in the past, no hope for this one because it's not recurring.
								 */
								++$acc['nX_cantDetermineSendDate'];
								$acc['nXr_cantDetermineSendDate'] += $this->deleteFollowUp($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
								continue;
							}
							$reminderReferenceDate			= $aProc['refDateId'];
							
						} elseif ($aProc['procSteps'] == 'two') {
							/**
							 * Two-Step Procedure
							 */
							$unixSendDateMulti		= $this->computeDerivedSendDate($aAnimal['animalId'], $aProc['refDateId'], $cronSession['settings'], $aProc['offset'], $aProc['anticipation']);
							
							if ($this->usableAsSendDate($unixSendDateMulti, $aProc['scannerEvalDate'], $aProc['lastUpdateDate'], $aAnimal['animalId'], $aProc['procedureId'])) {
								$unixSendDate		= $unixSendDateMulti;
								$reminderReferenceDate	= $aProc['refDateId'];
								
							} else {
								$unixSendDateSingle		= $this->computeDerivedSendDate($aAnimal['animalId'], $aProc['singleRefDateId'], $cronSession['settings'], $aProc['offset'], $aProc['anticipation']);
								
								if ($this->usableAsSendDate($unixSendDateSingle, $aProc['scannerEvalDate'], $aProc['lastUpdateDate'], $aAnimal['animalId'], $aProc['procedureId'])) {
									$unixSendDate		= $unixSendDateSingle;
									$reminderReferenceDate	= $aProc['singleRefDateId'];
								} else {
									/**
									 * Both dates are in the past, no hope for this one because it's not recurring.
									 */
									++$acc['nX_datepast'];
									$acc['nXr_cantDetermineSendDate'] += $this->deleteFollowUp($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
									continue;
								}
							}
						} else {
							// Invalid proc type???
							$acc['nX_cantDetermineSendDate']	= -10000; // SOS, something is wrong!
							continue;
						}
						
						// Foolproof check
						if (!$unixSendDate) {
							// Why no value for unixSendDate???
							$acc['nX_cantDetermineSendDate']	= -90000; // SOS, something is wrong!
							continue;
						}
						$sendDate		= date('Y-m-d', $unixSendDate);
					}
				}
				
				
				/**
				 * UNLESS Criteria B For Animals
				 */
				 
				if ($this->evaluateUnless($cronSession['settings'], $aAnimal['animalId'], $unixSendDate, $aDtMinuen, $aDtSubtrahen, $aOp, $nDaysUnixValue) == false) {
					// Disregard this Animal because UNLESS condition did not validate.
					++$acc['nX_failedUnlessCond'];
					$acc['nXr_failedUnlessCond'] += $this->deleteFollowUp($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
					continue;
				}
				
				++$acc['nAnimals'];
				
				
				/**
				 * FINAL STEP: write the follow-up
				 */
				$result	= $this->saveFollowUp(
					$aProc['messageId'],
					$aAnimal['animalId'],
					$aOwner['clientId'],
					$sendDate,
					$aProc['subscriberId'],
					$aProc['priority'],
					$aProc['procedureId'],
					$aProc['consolidate'],
					$aProc['sendOnDeath'],
					$channelId,
					$isRecurring
				);
				
				if ($result == 'UPDATED') {
					++$acc['nUpdatedFollowUps'];
					
				} elseif ($result == 'NO_UPDATE') {
					++$acc['nNoUpFollowUps'];
					
				} elseif ($result == 'NEW') {
					++$acc['nNewFollowUps'];
					
				} elseif ($result === false) {
					++$acc['nErrorOnSave'];
					continue;
				}
				
				/**
				 * WRITE REMINDERS if any.
				 */
				if ($aProc['reminderMessageId1'] == 0) {
					// There are no reminders.
					continue;
				}
				if ($reminderChannelId == 0) {
					continue;
				}
				
				/**
				 * REMINDER 1
				 */
				$sendRemDate1	= $this->computeReminderDate($sendDate, $aProc['procSteps'], $aProc['anticipation'], $aProc['reminderAfterNdays1']);
				
				if ($sendRemDate1 === false) {
					// Disregard, there are no reminders.
					$acc['nXr_reminder'] += $this->deleteFupReminder($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
					continue;
				}
				
				if ($this->doWeNeedToSendReminder($sendDate, $aAnimal['animalId'], $aProc['reminderTargetEventDateId'], $aProc['procSteps'], $aProc['anticipation'], $aProc['reminderAfterNdays1'], $aProc['reminderAfterNdays2']) 
				 === false) {
					// Cancel reminders, targetted action is accomplished by Client.
					$acc['nXr_reminder'] += $this->deleteFupReminder($aProc['procedureId'], $aAnimal['animalId'], $aOwner['clientId']);
					continue;
				}
				
				$result	= $this->saveFollowUp(
					$aProc['reminderMessageId1'],
					$aAnimal['animalId'],
					$aOwner['clientId'],
					$sendRemDate1,
					$aProc['subscriberId'],
					$aProc['priority'],
					$aProc['procedureId'],
					$aProc['consolidate'],
					$aProc['sendOnDeath'],
					$reminderChannelId,
					$isRecurring,
					true,
					1, // means 1st reminder
					$aProc['reminderTargetEventDateId'],
					$reminderReferenceDate,
					$aProc['offset'],
					$aProc['reminderAfterNdays1']
				);
				if ($result == 'NEW') {
					++$acc['nReminders'];
				} elseif ($result == 'UPDATED') {
					++$acc['nUpReminders'];
				} elseif ($result == 'NO_UPDATE') {
					++$acc['nNoUpReminders'];
				}
				
				
				/**
				 * REMINDER 2
				 */
				$sendRemDate2	= $this->computeReminderDate($sendDate, $aProc['procSteps'], $aProc['anticipation'], $aProc['reminderAfterNdays2']);
				
				if ($sendRemDate2 === false) {
					// Disregard, there is no second reminder.
					continue;
				}
				$result	= $this->saveFollowUp(
					$aProc['reminderMessageId1'],
					$aAnimal['animalId'],
					$aOwner['clientId'],
					$sendRemDate2,
					$aProc['subscriberId'],
					$aProc['priority'],
					$aProc['procedureId'],
					$aProc['consolidate'],
					$aProc['sendOnDeath'],
					$reminderChannelId,
					$isRecurring,
					true,
					2, // means 2nd reminder
					$aProc['reminderTargetEventDateId'],
					$reminderReferenceDate,
					$aProc['offset'],
					$aProc['reminderAfterNdays2']
				);
				if ($result == 'NEW') {
					++$acc['nReminders'];
				} elseif ($result == 'UPDATED') {
					++$acc['nUpReminders'];
				} elseif ($result == 'NO_UPDATE') {
					++$acc['nNoUpReminders'];
				}
			} // each Animals
		} // each Owners
		
		return array($acc, $noChannelOwners);
	}
	
	public function setProcEvalDate($procedureId)
	{
		$sql	= "UPDATE `procedure` SET scannerEvalDate = NOW() WHERE procedureId = '{$procedureId}'";
		$this->safeExec($sql);
	}
	
	public function updateGroupSendDate($procedureId, $nextRecurDate)
	{
		$sql	= "UPDATE `procedure` SET 
				groupSendDate	= '{$nextRecurDate}',
				lastUpdateDate  = NOW() + INTERVAL 1 SECOND
			WHERE procedureId = '{$procedureId}'
			";
		$this->safeExec($sql);
	}
}

// eof
