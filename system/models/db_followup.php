<?php
/**
 * Model file of controller: followup
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
 * Database access functions of controller: followup
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves a new custom message
	 * @return integer
	 */
	public function addCustomMessage($categoryId, $messageId, $messageBody, $channelId)
	{
		$sql	= "INSERT INTO customMessage SET
				customMsgCategoryId = '{$categoryId}',
				modelFromMsgId = '{$messageId}',
				customMsgBody = '{$messageBody}',
				customMsgChannelId = '{$channelId}'";
		return $this->safeExecInsert($sql);
	}

	/**
	 * Saves a new follow up
	 * @return integer
	 */
	public function addFollowUp($messageId, $animalId, $clientId, $sendDate, $channelId, $isCustomMessage)
	{
		$sql	= "INSERT INTO followUp SET
				animalId = '{$animalId}',
				clientId = '{$clientId}',
				subscriberId = '{$_SESSION['subscriberId']}',
				messageId = '{$messageId}',
				sendDate = '{$sendDate}',
				isCustomMessage = '{$isCustomMessage}',
				isManualFollowUp = '1',
				overrideChannelId = '{$channelId}'
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Updates an existing follow up
	 * @return integer
	 */
	public function updateFollowUp($followUpId, $messageId, $animalId, $clientId, $sendDate, $channelId, $isCustomMessage)
	{
		$sql	= "UPDATE followUp SET
				animalId = '{$animalId}',
				clientId = '{$clientId}',
				subscriberId = '{$_SESSION['subscriberId']}',
				messageId = '{$messageId}',
				sendDate = '{$sendDate}',
				isCustomMessage = '{$isCustomMessage}',
				isManualFollowUp = '1',
				overrideChannelId = '{$channelId}'
			WHERE followUpId = '{$followUpId}'";
		return $this->safeExec($sql);
	}
	
	/**
	 * Gets a follow-up
	 */
	public function getFollowUp($followUpId)
	{
		$sql	= "SELECT followUp.*, 
				customMessage.customMsgCategoryId, customMessage.modelFromMsgId, customMessage.customMsgBody
			FROM followUp
			LEFT JOIN customMessage on customMessage.customMessageId = followUp.messageId
			WHERE followUp.followUpId = '{$followUpId}'
			";
		$row	= $this->fetchAll($sql);
		
		if (isset($row[0])) {
			return $row[0];
		}
	}
	
	/**
	 * Deletes a custom message
	 * @param integer $rowId
	 */
	public function deleteCustomMessage($rowId)
	{
		$sql	= "DELETE FROM customMessage WHERE customMessageId = '{$rowId}'";
		return $this->safeExec($sql);
	}
	
	/**
	 * Returns Follow up send after nDays array
	 */
	public function getFollowUpSendDate()
	{
		return array(
			'set' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->',
			'now' => lang_immediate,
			'1:w' => '1 ' . lang_week,
			'2:w' => '2 ' . lang_weeks,
			'3:w' => '3 ' . lang_weeks,
			'1:m' => '1 ' . lang_month,
			'2:m' => '2 ' . lang_months,
			'3:m' => '3 ' . lang_months,
			'6:m' => '6 ' . lang_months,
			'9:m' => '9 ' . lang_months,
			'1:y' => '1 ' . lang_year,
			'2:y' => '2 ' . lang_years,
			'3:y' => '3 ' . lang_years
		);
	}
	
	/**
	 * Gets message delivery channels available for a client
	 * @return array
	 */
	public function getClientChannels($clientId)
	{
		$sql	= "SELECT DISTINCT (phoneType) FROM `clientPhone` WHERE clientId = $clientId";
		$aRows	= $this->fetchAll($sql);
		
		$aReturn	= array();
		
		foreach ($aRows as $aRow) {
			if ($aRow['phoneType'] == 'mobile') {
				$aReturn[2]	= array(
					'channelName' => 'SMS',
					'channelType' => lang_cell
				);
				$aReturn[3]	= array(
					'channelName' => lang_voice,
					'channelType' => lang_cell
				);
			
			} elseif ($aRow['phoneType'] == 'homeph') {
				$aReturn[4]	= array(
					'channelName' => lang_voice,
					'channelType' => lang_contact_home
				);
			
			} elseif ($aRow['phoneType'] == 'ofisph') {
				$aReturn[5]	= array(
					'channelName' => lang_voice,
					'channelType' => lang_office
				);
			
			} elseif ($aRow['phoneType'] == 'homefx') {
				$aReturn[8]	= array(
					'channelName' => lang_fax,
					'channelType' => lang_contact_home
				);
			
			} elseif ($aRow['phoneType'] == 'ofisfx') {
				$aReturn[9]	= array(
					'channelName' => lang_fax,
					'channelType' => lang_office
				);
			
			} elseif ($aRow['phoneType'] == 'homepf') {
				$aReturn[4]	= array(
					'channelName' => lang_voice,
					'channelType' => lang_contact_home
				);
				$aReturn[8]	= array(
					'channelName' => lang_fax,
					'channelType' => lang_contact_home
				);
				
			} elseif ($aRow['phoneType'] == 'ofispf') {
				$aReturn[5]	= array(
					'channelName' => lang_voice,
					'channelType' => lang_office
				);
				$aReturn[9]	= array(
					'channelName' => lang_fax,
					'channelType' => lang_office
				);
			}
		}
		
		$sql	= "SELECT email, homeAddress1, officeAddress1 FROM client WHERE clientId = $clientId";
		$aRows	= $this->fetchAll($sql);
		
		if ($aRows[0]['email']) {
			$aReturn[1]	= array(
				'channelName' => lang_email,
				'channelType' => ''
			);
		}
		if ($aRows[0]['homeAddress1']) {
			$aReturn[6]	= array(
				'channelName' => lang_snail_mail,
				'channelType' => lang_contact_home
			);
		}
		if ($aRows[0]['officeAddress1']) {
			$aReturn[7]	= array(
				'channelName' => lang_snail_mail,
				'channelType' => lang_office
			);
		}
		
		return $aReturn;
	}
	
	/**
	 * Gets schedules messages for a client
	 * @param int $clientId
	 */
	public function getSchedulesMsgs($clientId=NULL, $animalId=NULL)
	{
		$cond			= '';
		if ($clientId) {
			$cond		= ' WHERE clientId = ' . $clientId;
		}
		if ($animalId) {
			$cond		.= ($cond ? ' AND ' : ' WHERE ');
			$cond		.= ' animalId = ' . $animalId;
		}
		
		$sql	= "SELECT 'F' AS rowType,
				followUp.followUpId,
				(DATE_FORMAT(followUp.sendDate, '%m/%d/%Y')) AS sendDateFmt,
				(TO_DAYS(followUp.sendDate) - TO_DAYS(CURDATE())) AS nDaysB4Send,
				channelType.channelName,
				(IF(isCustomMessage, followUp.messageId, 0)) AS customMsgId,
				`procedure`.procName,
				followUp.animalId,
				followUp.nthReminder
			FROM followUp
			LEFT JOIN channelType ON channelType.channelId = followUp.overrideChannelId
			LEFT JOIN `procedure` ON `procedure`.procedureId = followUp.procedureId
			/*WHERE followUp.clientId = '{$clientId}'*/
				{$cond}
			
			UNION 
			
			SELECT 'Q' AS rowType,
				mqlId AS followUpId, 
				IF(sendUnixtime, FROM_UNIXTIME(sendUnixtime, '%m/%d/%Y'), '') AS sendDateFmt, 
				0 AS nDaysB4Send, 
				channel AS channelName, 
				0 AS customMsgId,
				`procedure`.procName,
				messageQueueLog.animalId,
				''	AS nthReminder
			FROM messageQueueLog
			LEFT JOIN `procedure` ON `procedure`.procedureId = messageQueueLog.procedureId
			/*WHERE clientId = '{$clientId}'*/
				{$cond}
			
			ORDER BY 3, 5
			";
		return $this->fetchAll($sql);
	}
	
	/**
	 * Deletes a row on Table: followUp
	 */
	public function manualCancelFollowUp($followUpId)
	{
		$sql	= "DELETE FROM followUp WHERE followUpId = '{$followUpId}'";
		return $this->dbc->exec($sql);
		
	}
	
	/**
	 * Gets owner, honorary and animal name using animalId
	 * @param integer $animalId
	 * @return array
	 */
	public function getAnimalAndOwner($animalId)
	{
		$sql	= "SELECT client.honoraryId,
				client.clientId,
				client.lastName,
				client.firstName,
				animal.animalName,
				specie.specieName
			FROM animal
			INNER JOIN client ON client.clientId = animal.clientId
			LEFT JOIN specie ON specie.specieId = animal.specieId
			WHERE animal.animalId = {$animalId}
				AND client.subscriberId = " . $_SESSION['subscriberId'];
		$row	= $this->fetchAll($sql);
		
		if (isset($row[0])) {
			return $row[0] + array('strHonorary' => $this->getHonorary($row[0]['honoraryId']));
		}
		
		return false;
	}
	
	/**
	 * Gets the Animal ID using Client PMS ID and Animal PMS ID
	 * @param integer $clientPmsId Client PMS ID
	 * @param integer $animalPmsId Animal PMS ID
	 * @return integer|boolean
	 */
	public function getAnimalIdUsingPmsIds($clientPmsId, $animalPmsId)
	{
		$sql	= "SELECT animalId
			FROM animal
			INNER JOIN client ON (
				client.clientId = animal.clientId 
			 AND 
				clientPmsId = '{$clientPmsId}'
			)
			WHERE animalPmsId = '{$animalPmsId}'
			";
		$row	= $this->fetchAll($sql);
		if (isset($row[0])) {
			return $row[0]['animalId'];
		}
		
		return false;
	}
}

// eof
