<?php
/**
 * Model file of controller: testuploadini
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
 * Database access functions of controller: testuploadini
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves subscriber to database if it does not exist.
	 * @return $subscriberId
	 */
	public function processSubscriber($aData)
	{
		$subscriberVetName	= isset($aData['subscriberVetName']) ? $aData['subscriberVetName'] : NULL;
		
		$sql	= "INSERT INTO subscriber (
				subscriberLogin,
				subscriberPassword,
				subscriberPmsId,
				subscriberLastName,
				subscriberVetName
			) VALUES (
				LAST_INSERT_ID(subscriberId) + 1,
				MD5(LAST_INSERT_ID(subscriberId) + 1),
				{$aData['subscriberPmsId']},
				'{$aData['subscriberLastName']}',
				'{$subscriberVetName}'
			)
			ON DUPLICATE KEY UPDATE 
				subscriberId = LAST_INSERT_ID(subscriberId),
				subscriberLogin = LAST_INSERT_ID(subscriberId),
				subscriberPassword = MD5(LAST_INSERT_ID(subscriberId)),
				subscriberPmsId = {$aData['subscriberPmsId']},
				subscriberLastName = '{$aData['subscriberLastName']}',
				subscriberVetName = '{$subscriberVetName}'
			";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}
	
	/**
	 * Saves honorary to database if it does not exist.
	 */
	public function processHonorary($clientTitle)
	{
		$sql	= "INSERT INTO honorary SET
				honoraryTitle = '{$clientTitle}'
			ON DUPLICATE KEY UPDATE 
				honoraryId = LAST_INSERT_ID(honoraryId)
			";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}

	/**
	 * Saves client to database if it does not exist.
	 * @return $clientId
	 */
	public function processClient($subscriberId, $honoraryId, $nPreferredChannelId, $usePreferredExclusively, $aData)
	{
		if (isset($aData['emailAddress']) && $aData['emailAddress']) {
			$emailAddress	= str_replace(array(' ; ', '; ', ' ;', ';'), ',', $aData['emailAddress']);
		} else {
			$emailAddress	= '';
		}
		
		$addr1		= $aData['addressLine1'];
		$addr2		= (isset($aData['addressLine2']) ? $aData['addressLine2'] : '');
		
		$sql	= "INSERT INTO client
			SET
				subscriberId 		= {$subscriberId},
					/*clientExternalId*/
				preferredChannelId 	= {$nPreferredChannelId},
				usePreferredExclusively = {$usePreferredExclusively},
					/*backupChannelId
					nixedChannelIdCsv*/
				honoraryId 		= {$honoraryId},
				clientPmsId 		= '{$aData['clientPmsId']}',
				lastName 		= '{$aData['clientLastName']}',
				firstName 		= '{$aData['clientFirstName']}',
				email 			= '{$emailAddress}',
				homeAddress1 		= ?,
				homeAddress2 		= ?,
				homeCity 		= '{$aData['city']}',
				homePostalCode 		= '{$aData['zipCode']}',
				homeProvinceOrState 	= '" . (isset($aData['stateOrProvince']) ? $aData['stateOrProvince'] : '') . "',
					/*officeAddress1
					officeAddress2
					officeCity
					officePostalCode
					officeProvinceOrState*/
				country 		= '" . (isset($aData['country']) ? $aData['country'] : '') . "'
					/*messageThreshold
					noMessage*/
			
			ON DUPLICATE KEY
			
			UPDATE 	clientId 		= LAST_INSERT_ID(clientId),
				subscriberId 		= {$subscriberId},
					/*clientExternalId*/
				preferredChannelId 	= {$nPreferredChannelId},
				usePreferredExclusively = {$usePreferredExclusively},
					/*backupChannelId
					nixedChannelIdCsv*/
				honoraryId 		= {$honoraryId},
				clientPmsId 		= '{$aData['clientPmsId']}',
				lastName 		= '{$aData['clientLastName']}',
				firstName 		= '{$aData['clientFirstName']}',
				email 			= '{$emailAddress}',
				homeAddress1 		= ?,
				homeAddress2 		= ?,
				homeCity 		= '{$aData['city']}',
				homePostalCode 		= '{$aData['zipCode']}',
				homeProvinceOrState 	= '" . (isset($aData['stateOrProvince']) ? $aData['stateOrProvince'] : '') . "',
					/*officeAddress1
					officeAddress2
					officeCity
					officePostalCode
					officeProvinceOrState*/
				country 		= '" . (isset($aData['country']) ? $aData['country'] : '') . "'
					/*messageThreshold
					noMessage*/
			";
		$result		= $this->safeExecInsertWithParam($sql, array($addr1, $addr2, $addr1, $addr2));
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}
	
	/**
	 * Saves phone to database if these do not exist.
	 */
	public function processPhone($clientId, $phoneChannel, $nPriority, $strPhoneCsv)
	{
		if (strpos($strPhoneCsv, ';')) {
			/**
			 * Separator is colon
			 */
			$strPhoneCsv	= str_replace(array(' ; ', ' ;', '; '), ';', $strPhoneCsv);
			$aPhone		= explode(';', $strPhoneCsv);
		} else {
			/**
			 * Separator is comma
			 */
			$aPhone		= explode(',', $strPhoneCsv);
		}
		
		$n		= count($aPhone);
		--$nPriority; // Set to 0-based array pointer not 1-based.
		
		if (isset($aPhone[$nPriority])) {
			/**
			 * Rearrange according to priority
			 */
			$aPhoneArranged		= array();
			$aPhoneArranged[]	= $aPhone[$nPriority];
			
			for ($x = 0; $x < $n; ++$x) {
				if ($x == $nPriority) {
					continue;
				}
				$aPhoneArranged[]	= $aPhone[$x];
			}
		} else {
			$aPhoneArranged			= $aPhone;
		}
		
		for ($x = 1; $x <= $n; ++$x) {
			$pointer	= $x - 1;
			
			if (strlen($aPhoneArranged[$pointer]) == 9) {
				$aPhoneArranged[$pointer]	= '0' . $aPhoneArranged[$pointer];
			}
			
			$sql	= "INSERT INTO clientPhone (
					clientId,
					priority,
					phoneType,
					phoneNumber
				) VALUES (
					{$clientId},
					{$x},
					'{$phoneChannel}',
					'{$aPhoneArranged[$pointer]}'
				)
				ON DUPLICATE KEY UPDATE 
					clientPhoneId = LAST_INSERT_ID(clientPhoneId),
					priority = {$x},
					phoneType = '{$phoneChannel}'
				";
			$this->safeExecInsert($sql);
		}
	}
	
	/**
	 * Saves specie to database if these do not exist.
	 * @return $specieId
	 */
	public function processSpecie($specie)
	{
		$sql	= "INSERT INTO specie SET
				specieName = ?
			
			ON DUPLICATE KEY 
			
			UPDATE specieId=LAST_INSERT_ID(specieId)
			";
		//~ $result		= $this->safeExecInsert($sql);
		$result		= $this->safeExecInsertWithParam($sql, array($specie));
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}
	
	/**
	 * Saves gender to database if these do not exist.
	 * @return array($specieId, $genderId)
	 */
	public function processGender($gender)
	{
		$sql	= "INSERT INTO gender (
				genderName
			) VALUES (
				'{$gender}'
			)
			
			ON DUPLICATE KEY 
			
			UPDATE genderId=LAST_INSERT_ID(genderId)
			";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}

	/**
	 * Saves animal to database if it does not exist.
	 * @return $animalId
	 */
	public function processAnimal($clientId, $specieId, $genderId, $aData)
	{
		$sql	= "INSERT INTO animal SET
				animalExternalId = '" . (isset($aData['animalId']) ? $aData['animalId'] : '') . "',
				specieId = {$specieId},
				clientId = {$clientId},
				genderId = {$genderId},
				animalPmsId = '{$aData['animalPmsId']}',
				animalName = ?
			
			ON DUPLICATE KEY 
			
			UPDATE	animalId = LAST_INSERT_ID(animalId),
				animalExternalId = '" . (isset($aData['animalId']) ? $aData['animalId'] : '') . "',
				specieId = {$specieId},
				clientId = {$clientId},
				genderId = {$genderId},
				animalPmsId = '{$aData['animalPmsId']}',
				animalName = ?
			";
		//~ $result		= $this->safeExecInsert($sql);
		$result		= $this->safeExecInsertWithParam($sql, array($aData['animalName'], $aData['animalName']));
		
		if ($result == SQL_ERROR) {
			return false;
		}
		return $result;
	}

	/**
	 * Saves boolean conditions to database.
	 */
	public function processCondBooleans($animalId, $aData)
	{
		foreach ($aData as $criteriaCode => $strBoolean) {
			
			$criteriaCaptionId	= $this->addCriterion('Boolean', $criteriaCode);
			
			if ($criteriaCaptionId == SQL_ERROR) {
				continue; // Error happened.  Disregard this one and go to the next.
			}
			
			$sql	= "INSERT INTO criteriaBoolean (
					animalId,
					criteriaCaptionId,
					criteriaBooleanValue
				) VALUES (
					$animalId,
					$criteriaCaptionId,
					" . ($strBoolean == 'TRUE' ? 1 : 0) . "
				)
				
				ON DUPLICATE KEY
				
				UPDATE	criteriaBooleanId = LAST_INSERT_ID(criteriaBooleanId),
					animalId = $animalId,
					criteriaCaptionId = $criteriaCaptionId,
					criteriaBooleanValue = " . ($strBoolean == 'TRUE' ? 1 : 0);
			$this->safeExecInsert($sql);
		}
	}

	/**
	 * Saves quantity conditions to database.
	 */
	public function processCondQuantities($animalId, $aData)
	{
		foreach ($aData as $criteriaCode => $value) {
			
			$criteriaCaptionId	= $this->addCriterion('Quantity', $criteriaCode);
			
			if ($criteriaCaptionId == SQL_ERROR) {
				continue; // Error happened.  Disregard this one and go to the next.
			}
			
			$sql	= "INSERT INTO criteriaQuantity (
					animalId,
					criteriaCaptionId,
					criteriaQuantityValue
				) VALUES (
					{$animalId},
					{$criteriaCaptionId},
					'{$value}'
				)
				
				ON DUPLICATE KEY
				
				UPDATE	criteriaQuantityId = LAST_INSERT_ID(criteriaQuantityId),
					animalId = {$animalId},
					criteriaCaptionId = {$criteriaCaptionId},
					criteriaQuantityValue = '{$value}'
				";
			$this->safeExecInsert($sql);
		}
	}

	/**
	 * Saves date conditions to database.
	 */
	public function processCondDates($animalId, $aData, $dateClass='MEDICALACTS')
	{
		foreach ($aData as $criteriaCode => $value) {
			
			if (!$value) {
				continue; // Disregard empty date fields.
			}
			
			$criteriaCaptionId	= $this->addCriterion('Date', $criteriaCode);
			
			if ($criteriaCaptionId == SQL_ERROR) {
				continue; // Error happened.  Disregard this one and go to the next.
			}
			
			$sql	= "INSERT INTO criteriaDate (
					animalId,
					criteriaCaptionId,
					criteriaDateValue,
					dateClass
				) VALUES (
					{$animalId},
					{$criteriaCaptionId},
					'{$value}',
					'{$dateClass}'
				)
				
				ON DUPLICATE KEY
				
				UPDATE	criteriaDateId = LAST_INSERT_ID(criteriaDateId),
					animalId = {$animalId},
					criteriaCaptionId = {$criteriaCaptionId},
					criteriaDateValue = '{$value}',
					dateClass = '{$dateClass}'
				";
			$this->safeExecInsert($sql);
		}
	}
	
	/**
	 * Saves appointment dates to database.
	 */
	public function processCondAppointments($clientId, $animalId, $appointments)
	{
		$this->addCriterion('Datetime', 'appointment');
		
		if (strpos($appointments, ';')) {
			$aApp		= explode(';', $appointments);
		} else {
			$aApp		= explode(',', $appointments);
		}
		
		foreach ($aApp as $strDateTime) {
			$strDateTime	= trim($strDateTime);
			
			if (!$strDateTime) {
				continue; // Disregard empty date fields.
			}
			
			$sql	= "INSERT INTO appointmentDate (
					clientId,
					animalId,
					appointmentDateValue
				) VALUES (
					{$clientId},
					{$animalId},
					'{$strDateTime}'
				)
				"; //~ ON DUPLICATE KEY UPDATE appointmentDateId = LAST_INSERT_ID(appointmentDateId)
			$this->safeExecInsert($sql);
		}
	}

	/**
	 * Saves event conditions to database.
	 */
	public function processCondEvents($animalId, $aData)
	{
		foreach ($aData as $criteriaCode => $eventCsv) {
		
			if (!$eventCsv) {
				continue; // Disregard empty event fields.
			}
			
			$criteriaCaptionId	= $this->addCriterion('Event', $criteriaCode);
			
			if ($criteriaCaptionId == SQL_ERROR) {
				continue; // Error happened.  Disregard this one and go to the next.
			}
			
			// Explode data into date and boolean
			$aOneEvent	= explode(',', $eventCsv);
			$boolValue	= $aOneEvent[0] == 'TRUE' ? 1 : 0;
			if (!isset($aOneEvent[1])) {
				continue; // Disregard empty event fields.
			}
			$dateValue	= $aOneEvent[1];
			
			$sql	= "INSERT INTO criteriaEvent (
					animalId,
					criteriaCaptionId,
					criteriaDateValue,
					criteriaIsTrue
				) VALUES (
					{$animalId},
					{$criteriaCaptionId},
					'{$dateValue}',
					{$boolValue}
				)
				
				ON DUPLICATE KEY
				
				UPDATE	criteriaEventId = LAST_INSERT_ID(criteriaEventId),
					animalId = {$animalId},
					criteriaCaptionId = {$criteriaCaptionId},
					criteriaDateValue = '{$dateValue}',
					criteriaIsTrue = {$boolValue}
				";
			$this->safeExecInsert($sql);
		}
	}
	
	/**
	 * Determines channelId from INI channel
	 */
	public function iniChannelToChannelId($channel)
	{
		// SMS, EMAIL, VOICE, FAX, LETTER

		if ($channel == 'SMS') {
			return 2; //	SMS	cell
		}

		if ($channel == 'EMAIL') {
			return 1; //	Email	 
		}

		if ($channel == 'VOICE') {
			return 4;	// 3	Voice	cell
					// 4	Voice	home
					// 5	Voice	office
		}

		if ($channel == 'FAX') {
			return 8;	// 8	Fax	home
					// 9	Fax	office
		}

		if ($channel == 'LETTER') {
			return 6;	// 6	Snail mail	home
					// 7	Snail mail	office
		}
		
		return 0;
	}
	
	/**
	 * Saves a subscriber-specific caption
	 * @param string $code Criteria code
	 * @param string $caption Criteria to be shown to user
	 * @param string $languageCode
	 * @return integer Affected rows count
	 */
	public function saveSubscriberCaption($code, $caption, $languageCode)
	{
		$sql	= "INSERT INTO criteriaLanguage SET
				subscriberId	= {$_SESSION['subscriberId']},
				language	= '{$languageCode}',
				criteriaCode	= '{$code}',
				value		= '{$caption}'
			
			ON DUPLICATE KEY
			
			UPDATE	value		= '{$caption}'
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Deletes all phones that belong to a client
	 * @param integer $clientId Client ID
	 */
	public function deleteClientPhones($clientId)
	{
		$sql	= "DELETE FROM clientPhone WHERE clientId = " . $clientId;
		return $this->safeExec($sql);
	}
}

// eof
