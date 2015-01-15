<?php
/**
 * Class file for common cron calculation and helper functions.
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
 * Include Model_Cron_Common superclass
 */
if (defined('site_path')) {
	require site_path . 'system/models/model_cron_common.php';
} else {
	require 'model_cron_common.php';
}

/**
 * Class for common cron calculation and helper functions.
 * @package    super_class
 */
class Model_Cron extends Model_Cron_Common
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Determines if a Proc recurs or not
	 * @return numeric
	 */
	public function isProcRecurring($procSteps, $recurringPeriod)
	{
		if ($recurringPeriod) {
			$isRecurring	= 1;
		} else {
			$isRecurring	= 0;
		}
		
		return $isRecurring;
	}
	
	/**
	 * Gets the most appropriate delivery Channel ID for a particular client.
	 */
	public function getAppropriateChannelId($messageChannel, $usePreferredExclusively, $preferredChannelId, $backupChannelId, $nixedChannelIdCsv)
	{
		//~ $aChannelId	= array();
		
		//~ if ($messageChannel == 'SMS') {
			//~ $aChannelId[]	= 2; // SMS	cell
			
		//~ } elseif ($messageChannel == 'Vocal') {
			//~ $aChannelId[]	= 3; // Voice	cell
			//~ $aChannelId[]	= 4; // Voice	home
			//~ $aChannelId[]	= 5; // Voice	office
			
		//~ } elseif ($messageChannel == 'Letter') {
			//~ $aChannelId[]	= 6; // Snail mail	home
			//~ $aChannelId[]	= 7; // Snail mail	office
			//~ $aChannelId[]	= 8; // Fax		home
			//~ $aChannelId[]	= 9; // Fax		office
			
		//~ } elseif ($messageChannel == 'Email') {
			//~ $aChannelId[]	= 1; // Email
		//~ }
		
		/**
		 * RAYMOND: TEMPORARY
		 *	Put it all here
		 */
		$aChannelId	= array(
			2, // SMS	cell
			3, // Voice	cell
			4, // Voice	home
			5, // Voice	office
			6, // Snail mail	home
			7, // Snail mail	office
			8, // Fax		home
			9, // Fax		office
			1  // Email
		);
		
		if ($usePreferredExclusively == 1) {
			if ($preferredChannelId && (in_array($preferredChannelId, $aChannelId))) {
				return $preferredChannelId;
			}
			
			if ($backupChannelId && (in_array($backupChannelId, $aChannelId))) {
				return $backupChannelId;
			}
			
			// As per Michel. Use multimedia channel for now which means no channel.
			//~ return 0;
		}
		
		if ($nixedChannelIdCsv) {
			foreach ($aChannelId as $key => $chId) {
				if (strpos($nixedChannelIdCsv, $chId) !== false) {
					unset($aChannelId[$key]);
				}
			}
		}
		
		// Hmmm... what to do here?  Temporarily send one of the channels
		foreach ($aChannelId as $chId) {
			return $chId;
		}
		
		return 0;
	}
	
	/**
	 * Computes the send date using the formula:
	 *	Send Date = Reference Date + Offset - Anticipation
	 */
	public function computeDerivedSendDate($animalId, $critDateId, $settings, $offset, $anticipation)
	{
		$refDate	= $this->getCriterionDateValue($animalId, $critDateId, $settings);
		
		if ($refDate === false) {
			// Value does not exist in the database for this Animal.
			return false;
		}
		
		$unixDate	= strtotime($refDate 
				. ' +' 
				. str_replace(array(':w', ':m', ':y'), array(' week', ' month', ' year'), $offset)
				);
		$unixDate	= strtotime(date('Y-m-d', $unixDate) 
				. ' -' 
				. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $anticipation)
				);
		
		return $unixDate;
	}
	
	/**
	 * Computes Reminder Date
	 * @param string $sendDate 'YYYY-MM-DD'
	 * @param string $anticipation '5:d'
	 * @param integer $reminderAfterNdays
	 * @return string 'YYYY-MM-DD'
	 */
	public function computeReminderDate($sendDate, $procSteps, $anticipation, $reminderAfterNdays)
	{
		if ($reminderAfterNdays == 0) {
			return false;
		}
		
		/**
		 * It's important to check that Proc Type is NOT 'group'
		 *	Else, there is the possibility of using anticipation from a modified Proc.
		 */
		if ($procSteps != 'group' && $anticipation) {
			/**
			 * Now we are sure that this is either a 'one' or 'two' step proc.
			 *	Let's add back anticipation period to sendDate to get the computed targetted date.
			 */
			$sendDate	= date('Y-m-d',
				strtotime($sendDate 
					. ' +' 
					. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $anticipation)
				)
			);
		}
		
		return date('Y-m-d', 
				strtotime($sendDate 
					. ' +' 
					. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $reminderAfterNdays)
				)
		);
	}
	
	public function buildCriteriaFromProcDef($aProc, $cronSession)
	{
		/**
		 * PREPARE VARIABLES FOR BUSINESS RULES CHECKING.
		 */
		
		list($specieCsv, $genderCsv)	= explode(':', $aProc['specieIdGenderCsvc']);
		/**
		 * (1) Specie filter
		 */
		$condSpecie			= '';
		if ($specieCsv) {
			$aSpecie		= explode(',', $specieCsv);
			if ($aSpecie) {
				$n	= 0;
				foreach ($aSpecie as $specieId) {
					$condSpecie	.= $condSpecie ? ', ' : NULL;
					$condSpecie	.= $specieId;
					++$n;
				}
				if ($n == 1) {
					$condSpecie	= ' AND animal.specieId = ' . $condSpecie . ' ';
				} else {
					$condSpecie	= ' AND animal.specieId IN (' . $condSpecie . ') ';
				}
			}
		}
		
		/**
		 * (2) Gender filter
		 */
		$condGender			= '';
		if ($genderCsv) {
			$aGender		= explode(',', $genderCsv);
			if ($aGender) {
				$n	= 0;
				foreach ($aGender as $genderId) {
					$condGender	.= $condGender ? ', ' : NULL;
					$condGender	.= $genderId;
					++$n;
				}
				if ($n == 1) {
					$condGender	= ' AND animal.genderId = ' . $condGender . ' ';
				} else {
					$condGender	= ' AND animal.genderId IN (' . $condGender . ') ';
				}
			}
		}
		
		/**
		 * (3) Boolean conditions
		 */
		$condBooleanJoin		= '';
		$condEvtBooleanJoin		= '';
		
		if ($aProc['criteriaBooleanIdCsvc']) {
			$aDbBoolCriteria	= explode(',', $aProc['criteriaBooleanIdCsvc']);
			
			$aBoolValues		= array();
			$aEvtBoolValues		= array();
			
			foreach ($aDbBoolCriteria as $aCri) {
				$aBoolCrit			= explode(':', $aCri);
				
				if ($aBoolCrit[0] == $cronSession['active'] || $aBoolCrit[0] == $cronSession['identified'] || $aBoolCrit[0] == $cronSession['vaccinated'] || $aBoolCrit[0] == $cronSession['insured']) {
					$aBoolValues[$aBoolCrit[0]]		= $aBoolCrit[1];
				} else {
					$aEvtBoolValues[$aBoolCrit[0]]		= $aBoolCrit[1];
				}
			}
			
			// The following INNER JOINS will take care of Boolean criteria
			if (isset($aBoolValues[$cronSession['active']])) {
				$condBooleanJoin	.= " INNER JOIN criteriaBoolean AS activeBool     ON (activeBool.criteriaCaptionId     = {$cronSession['active']}     AND activeBool.criteriaBooleanValue     = {$aBoolValues[$cronSession['active']]}     AND activeBool.animalId = animal.animalId) ";
			}
			if (isset($aBoolValues[$cronSession['identified']])) {
				$condBooleanJoin	.= " INNER JOIN criteriaBoolean AS identifiedBool ON (identifiedBool.criteriaCaptionId = {$cronSession['identified']} AND identifiedBool.criteriaBooleanValue = {$aBoolValues[$cronSession['identified']]} AND identifiedBool.animalId = animal.animalId) ";
			}
			if (isset($aBoolValues[$cronSession['vaccinated']])) {
				$condBooleanJoin	.= " INNER JOIN criteriaBoolean AS vaccinatedBool ON (vaccinatedBool.criteriaCaptionId = {$cronSession['vaccinated']} AND vaccinatedBool.criteriaBooleanValue = {$aBoolValues[$cronSession['vaccinated']]} AND vaccinatedBool.animalId = animal.animalId) ";
			}
			if (isset($aBoolValues[$cronSession['insured']])) {
				$condBooleanJoin	.= " INNER JOIN criteriaBoolean AS insuredBool    ON (insuredBool.criteriaCaptionId    = {$cronSession['insured']}    AND insuredBool.criteriaBooleanValue    = {$aBoolValues[$cronSession['insured']]}    AND insuredBool.animalId = animal.animalId) ";
			}
			
			// The following will take care of the remaining Boolean conditions, namely the Boolean part of Event criteria.
			foreach($aEvtBoolValues as $evtBoolKey => $evtBoolValue) {
				$condEvtBooleanJoin	.= " INNER JOIN criteriaEvent AS criteriaEvent_{$evtBoolKey}
					ON (criteriaEvent_{$evtBoolKey}.criteriaCaptionId	= {$evtBoolKey}
					AND criteriaEvent_{$evtBoolKey}.criteriaIsTrue		= {$evtBoolValue}
					AND criteriaEvent_{$evtBoolKey}.animalId = animal.animalId) ";
			}
		}
		
		// Quantity criteria
		$condQuantityJoin		= '';
		
		if ($aProc['criteriaQuantityIdCsvc']) {
			$aDbQtyCriteria		= explode(',', $aProc['criteriaQuantityIdCsvc']);
			
			// Loop thru the quantity criteria
			foreach ($aDbQtyCriteria as $aCri) {
				$aQtyCrit	= explode(':', $aCri);
				
				if ($aQtyCrit[1] == '{') {
					$compareOper	= '<=';
				} elseif ($aQtyCrit[1] == '}') {
					$compareOper	= '>=';
				} else {
					$compareOper	= $aQtyCrit[1];
				}
				
				$condQuantityJoin	.= " INNER JOIN criteriaQuantity AS criteriaQuantity_{$aQtyCrit[0]}
					ON (criteriaQuantity_{$aQtyCrit[0]}.criteriaCaptionId = {$aQtyCrit[0]}
					AND criteriaQuantity_{$aQtyCrit[0]}.criteriaQuantityValue $compareOper {$aQtyCrit[2]}
					AND criteriaQuantity_{$aQtyCrit[0]}.animalId = animal.animalId) ";
				
			}
		}
		/**
		 * ABOVE: Preparation of variables for Business Rules checking.
		 */
		
		return array(
			'condSpecie'		=> $condSpecie, 
			'condGender'		=> $condGender, 
			'condBooleanJoin'	=> $condBooleanJoin, 
			'condEvtBooleanJoin'	=> $condEvtBooleanJoin, 
			'condQuantityJoin'	=> $condQuantityJoin
		);
	}
	
	/**
	 * Checkes if UNLESS Crit B condition evaluates to true.
	 * @return boolean
	 */
	public function evaluateUnless($settings, $animalId, $unixSendDate, $aDtMinuen, $aDtSubtrahen, $aOp, $nDaysUnixValue)
	{
		$uCnt	= count($aDtMinuen);
		
		if ($uCnt == 0) {
			// There's no UNLESS condition to test, default to true.
			return true;
		}
		
		for ($x = 0; $x < $uCnt; $x++) {
			$minuenTime		= 0;
			$subtrahenTime		= 0;
			
			if ($aDtMinuen[$x] == 1) {
				// 1 means Send Date
				$minuenTime		= $unixSendDate;
				
			} else {
				$strDate	= $this->getCriterionDateValue($animalId, $aDtMinuen[$x], $settings);
				if ($strDate === false) {
					// Value does not exist for this animal.  Let's try the next UNLESS conditions.
					continue;
				}
				$minuenTime		= strtotime($strDate);
			}
			
			if ($aDtSubtrahen[$x] == 1) {
				// 1 means Send Date
				$subtrahenTime	= $unixSendDate;
				
			} else {
				$strDate	= $this->getCriterionDateValue($animalId, $aDtSubtrahen[$x], $settings);
				if ($strDate === false) {
					// Value does not exist for this animal.  Let's try the next UNLESS conditions.
					continue;
				}
				$subtrahenTime	= strtotime($strDate);
			}
			
			if ($minuenTime && $subtrahenTime) {
				
				$difference	= $minuenTime - $subtrahenTime;
				$result		= $difference - $nDaysUnixValue[$x];
				
				if ($aOp[$x] == '=' && $result == 0) {
					return true;
					
				} elseif ($aOp[$x] == '<' && $result < 0) {
					return true;
					
				} elseif ($aOp[$x] == '>' && $result > 0) {
					return true;
					
				} elseif ($aOp[$x] == '{' && $result <= 0) {
					return true;
					
				} elseif ($aOp[$x] == '}' && $result >= 0) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Computes the send date using the formula:
	 *	Send Date = Reference Date + Offset - Anticipation
	 * Format: YYYY-MM-DD
	 */
	public function addDaysToSendDate($animalId, $critDateId, $settings, $nDays)
	{
		$refDate	= $this->getCriterionDateValue($animalId, $critDateId, $settings);
		
		if ($refDate === false) {
			// Value does not exist in the database for this Animal.
			return false;
		}
		
		$unixDate	= strtotime($refDate . ' +' . $nDays . ' day');
		
		return date('Y-m-d', $unixDate);
	}
	
	/**
	 * Returns the next future send date.
	 */
	public function getNextSendDate($settings, $aProc, $animalId)
	{
		//
		// TODO: Should we delete follow-ups here that no longer validates?
		//
		$loopFailsafeProtection		= 50;
		
		$unixSendDate			= 0;
		
		$strAdd		= str_replace(
			array(':d', ':w', ':m', ':y'), 
			array(' day', ' week', ' month', ' year'), 
			$aProc['recurringPeriod']
		);
		
		/**
		 * Loop until we get a date in the future.
		 */
		while ($unixSendDate < $this->nowTime && $loopFailsafeProtection > 0) {
			
			if ($unixSendDate) {
				/**
				 * If send date is today
				 * THE FOLLOWING CODE WILL CAUSE UNINTENDED LOOPING OF RECURRING PROC.
				 */
				//~ if (date('Y-m-d', $unixSendDate) == $this->todayYmd) {
					//~ if ($this->usableAsSendDate($unixSendDate, $aProc['scannerEvalDate'], $aProc['lastUpdateDate'])) {
						//~ return $unixSendDate;
					//~ }
				//~ }
				
				/**
				 * SUBSEQUENT execution inside this loop
				 */
				$unixSendDate		= strtotime(date('Y-m-d', $unixSendDate) . ' +' . $strAdd);
				
			} else {
				
				/**
				 * FIRST execution of the loop
				 */
				if ($aProc['procSteps'] == 'group') {
					
					$unixSendDate		= strtotime($aProc['groupSendDate']);
					
				} elseif ($aProc['procSteps'] == 'two') {
				
					$unixSendDateMulti	= $this->computeDerivedSendDate($animalId, $aProc['refDateId'], $settings, $aProc['offset'], $aProc['anticipation']);
					$unixSendDateSingle	= $this->computeDerivedSendDate($animalId, $aProc['singleRefDateId'], $settings, $aProc['offset'], $aProc['anticipation']);
					
					if ($unixSendDateMulti === false) {
						if ($unixSendDateSingle === false) {
							// Date cannot be determined.  No hope for this one.
							return 0;
						} else {
							// Single-date is the winner.
							$unixSendDate		= $unixSendDateSingle;
						}
					} else {
						if ($unixSendDateSingle === false) {
							// Multi-date is the winner.
							$unixSendDate		= $unixSendDateMulti;
						} else {
							if ($unixSendDateMulti > $unixSendDateSingle) {
								// Multi-date is the winner.
								$unixSendDate	= $unixSendDateMulti;
							} else {
								// Single-date is the winner.
								$unixSendDate	= $unixSendDateSingle;
							}
						}
					}
					
				} elseif ($aProc['procSteps'] == 'one') {
					
					$unixSendDate		= $this->computeDerivedSendDate($animalId, $aProc['refDateId'], $settings, $aProc['offset'], $aProc['anticipation']);
					
				} else {
					// Invalid proc type???
					return 0;
				}
				
				/**
				 * Check if we derived some date on our call to computeDerivedSendDate()
				 */
				if ($unixSendDate === false) {
					// Date cannot be determined.  No hope for this one.
					return 0;
				}
			}
			
			--$loopFailsafeProtection;
		}
		
		return $unixSendDate;
	}
	
	/**
	 * Computes the send date using the formula:
	 *	Send Date = YYYY-MM-DD + Offset - Anticipation
	 */
	public function computeSendDateUsingValue($YyyyMmDd, $offset, $anticipation)
	{
		$unixDate	= strtotime($YyyyMmDd 
				. ' +' 
				. str_replace(array(':w', ':m', ':y'), array(' week', ' month', ' year'), $offset)
				);
		$unixDate	= strtotime(date('Y-m-d', $unixDate) 
				. ' -' 
				. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $anticipation)
				);
		
		return $unixDate;
	}
	
	/**
	 * Checkes if a unix date can be used to send a follow-up
	 * @return boolean
	 */
	protected function usableAsSendDate($unixDateToTest, $procScannerEvalDate, $lastUpdateDate, $animalId, $procedureId)
	{
		$dateToTest	= date('Y-m-d', $unixDateToTest);
		
		if ($dateToTest == $this->todayYmd) {
			//~ if (strtotime($lastUpdateDate) > strtotime($procScannerEvalDate)) {
				//~ return true;
			//~ }
			
			/**
			 * '2000-01-01 11:11:11' is a special date which means that this Proc is now recurring.
			 * DO NOT RELY ON procScannerEvalDate WHICH IS A PROC LEVEL VARIABLE.
			 */
			//~ if ($procScannerEvalDate == '2000-01-01 11:11:11') {
				//~ /**
				 //~ * Do not recur when send date is today, else, sending of message on that day will loop continually.
				 //~ */
				//~ return false;
			//~ }
			
			/**
			 * We evaluate sendDate = Today as a date in the past.
			 *	Basically, we are using sendDate = Today as a stopper mechanism.
			 *	  else, there is the possibility of non-intentional duplication of follow-up.
			 *
			 * Exemption: Allow sendDate = Today only if this is a new Proc.
			 */
			if ($procScannerEvalDate == '0000-00-00 00:00:00') {
				return true;
			}
			
			/**
			 * Let's check if this Procedure ID has sent a message today.
			 */
			if ($this->messageSentTodayByProc($animalId, $procedureId)) {
				return false; // Message is already sent today by Procedure
			} else {
				return true;
			}
			
		} elseif ($unixDateToTest >= $this->nowTime) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Determines whether to send or not to send Reminder
	 * @return boolean
	 */
	protected function doWeNeedToSendReminder($sendDate, $animalId, $targetEventDateId, $procType, $anticipation, $reminderAfterNdays1, $reminderAfterNdays2)
	{
		/**
		 * DATE_2
		 */
		$date_two		= $this->getCriterionDateValue($animalId, $targetEventDateId);
		$date_two		= substr($date_two, 0, 10); // removed time
		
		if ($date_two === false) {
			// Target event date is not present.  This means that the Client didn't do the requested action yet.
			return true;
		}
		
		$unix_two		= strtotime($date_two);
		
		
		/**
		 * DATE_1
		 */
		$date_one	= $sendDate;
		$unix_one	= strtotime($sendDate);
		
		
		/**
		 * Date_3
		 */
		
		if ($procType != 'group' && $anticipation) {
			// Get computed target event date by adding back Anticipation
			$computedTargetDate	= date('Y-m-d',
				strtotime($sendDate 
					. ' +' 
					. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $anticipation)
				)
			);
		} else {
			$computedTargetDate	= $sendDate;
		}
		
		// Get the available and higher nReminderDays
		if ($reminderAfterNdays2) {
			$nReminderDays		= $reminderAfterNdays2;
		} elseif ($reminderAfterNdays1) {
			$nReminderDays		= $reminderAfterNdays1;
		} else {
			return false;
		}
		
		$unix_three	= strtotime($computedTargetDate 
				. ' +' 
				. str_replace(array(':d', ':w', ':m'), array(' day', ' week', ' month'), $nReminderDays)
		);
		$date_three	= date('Y-m-d', $unix_three);
		
		/**
		 * The determining code...
		 */
		if ($unix_one <= $unix_two && $unix_two <= $unix_three) {
			// Target event date is in the middle of two dates.
			// This means that the Client did the requested action after the Original message but before the reminderDuration.
			return false;
		}
		
		// Let's compare date strings for equality
		if ($date_one == $date_two || $date_two == $date_three) {
			// Target event date is equal to one of the two dates.
			// This means that the Client did the requested action on either the Original message sendDate OR the reminder sendDate.
			return false;
		}
		
		// Target event date is not in between the two dates.  This means that the Client didn't do the requested action yet.
		return true;
	}
}

// eof
