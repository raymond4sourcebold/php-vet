<?php
/**
 * Model file of controller: procedures
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
 * Database access functions of controller: procedures
 * @package    db_class
 */
Class Db Extends Model_Base
{
	/**
	 * Gets temp proc
	 */
	public function getProcedure($procedureId=NULL)
	{
		$sql	= "SELECT `procedure`.*, 
				tempprocedure.step,
				message.*,
				remmsg.messageCategoryId	AS remmsgCategoryId,
				remmsg.messageTitle		AS remmsgTitle,
				remmsg.messageBody		AS remmsgBody,
				remmsg.isPractice		AS remmsgIsPractice,
				remmsg.messageChannel		AS remmsgChannel,
				criteriaCaption.criteriaCode	AS referenceSendDate,
				category.categoryName,
				`procedure`.isPractice
			FROM tempprocedure 
			INNER JOIN `procedure` ON `procedure`.procedureId = tempprocedure.procedureId
			LEFT JOIN message ON message.messageId = `procedure`.messageId
			LEFT JOIN message AS remmsg ON remmsg.messageId = `procedure`.reminderMessageId1
			LEFT JOIN criteriaCaption ON criteriaCaption.criteriaCaptionId = `procedure`.refDateId
			LEFT JOIN category ON category.categoryId = message.messageCategoryId
			WHERE tempprocedure.subscriberId = {$_SESSION['subscriberId']} "
			. ($procedureId	
				? " AND tempprocedure.procedureId = {$procedureId} AND tempprocedure.isComplete = 1 "
				: ' AND tempprocedure.isComplete = 0 '
			  )
			. " LIMIT 1
			";
                $stmt   = $this->dbc->query($sql);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function setExistingProcForEdit($procedureId, $step='7)complete')
	{
		$sql	= "INSERT INTO tempprocedure
			SET
				procedureId = {$procedureId},
				subscriberId = {$_SESSION['subscriberId']},
				step = '{$step}',
				isComplete = 1
			ON DUPLICATE KEY UPDATE
				procedureId = {$procedureId},
				subscriberId = {$_SESSION['subscriberId']},
				step = '{$step}',
				isComplete = 1
			";
		return $this->safeExecInsert($sql);
	}
}

// eof
