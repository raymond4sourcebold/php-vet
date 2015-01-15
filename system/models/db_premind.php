<?php
/**
 * Model file of controller: premind
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
 * Database access functions of controller: premind
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves proc reminders
	 * @param integer $rowId
	 * @param integer $count
	 * @param integer $msgId
	 * @param integer $critDateId
	 * @param integer $nDays1
	 * @param integer $nDays2
	 * @return integer
	 */
	function procedureSaveReminders($rowId, $count, $msgId, $critDateId, $nDays1, $nDays2)
	{
		if (!$nDays2) {
			$nDays2		= 0;
		}
		
		$sql	= "UPDATE `procedure` SET 
				reminderCount = '{$count}',
				reminderMessageId1 = '{$msgId}',
				/*reminderMessageId2 = '',*/
				reminderTargetEventDateId = '{$critDateId}',
				reminderAfterNdays1 = '{$nDays1}',
				reminderAfterNdays2 = '{$nDays2}'
			WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
}

// eof
