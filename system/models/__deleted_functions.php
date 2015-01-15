<?php
/**
 * DELETED FUNCTIONS
 * Not sure if we're going to use these functions so let's put them here for the mean time.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    abc
 */

class deleted_functions
{
	/**
	 * Gets category name
	 * @param int $n
	 * @return string $strReturn
	 */
	public function getCategoryName($n)
	{
		$sql	= "SELECT categoryName FROM `category` WHERE categoryId = '$n'";
		
		$sth = $this->dbc->prepare($sql);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		return $row['categoryName'];
	}
	
	/**
	 * Gets category Id
	 * @param string $category
	 * @return string $strReturn
	 */
	public function getCategoryId($category)
	{
		$sql	= "SELECT categoryId FROM `category` WHERE categoryName = '$category'";
		
		$sth = $this->dbc->prepare($sql);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		return $row['categoryId'];
	}
	
	/**
	 * Gets manual follow ups
	 */
	public function getManualFollowUp($animalId)
	{
		$sql	= "SELECT followUp.followUpId,
				(DATE_FORMAT(followUp.sendDate, '%m/%d/%Y')) AS sendDateFmt,
				(TO_DAYS(followUp.sendDate) - TO_DAYS(CURDATE())) AS nDaysB4Send,
				channelType.channelName,
				(IF(isCustomMessage, followUp.messageId, 0)) AS customMsgId,
				`procedure`.procName
			FROM followUp
			LEFT JOIN channelType ON channelType.channelId = followUp.overrideChannelId
			LEFT JOIN `procedure` ON `procedure`.procedureId = followUp.procedureId
			WHERE followUp.animalId = '{$animalId}'
				/* AND followUp.isManualFollowUp = '1' */
			
			UNION 
			
			SELECT mqlId AS followUpId, 
				IF(sendUnixtime, FROM_UNIXTIME(sendUnixtime, '%m/%d/%Y'), '') AS sendDateFmt, 
				0 AS nDaysB4Send, 
				channel AS channelName, 
				0 AS customMsgId,
				`procedure`.procName
			FROM messageQueueLog
			LEFT JOIN `procedure` ON `procedure`.procedureId = messageQueueLog.procedureId
			WHERE animalId = '{$animalId}'
			
			ORDER BY 2, 4
			";
		return $this->fetchAll($sql);
	}
	
	// FUNCTION WITH SAME NAME IS ON db_followup.php
	/**
	 * Deletes a row on Table: customMessage
	 */
	public function deleteCustomMessage($customMsgId)
	{
		$sql	= "DELETE FROM customMessage WHERE customMessageId = '{$customMsgId}'";
		return $this->dbc->exec($sql);
	}
}

// eof
