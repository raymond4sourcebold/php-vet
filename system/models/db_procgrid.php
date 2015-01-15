<?php
/**
 * Model file of controller: procgrid
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
 * Database access functions of controller: procgrid
 * @package    db_class
 */
Class Db Extends Model_Base
{
	/**
	 * Gets Procedure for display on grid.
	 */
	public function getProcGrid()
	{
		$aReturn	= array();
		
		$sql	= "SELECT `procedure`.procedureId, `procedure`.subscriberId, procName, procSteps, isPractice, isActive, tempprocedure.step,
				tempprocedure.isComplete,
				`procedure`.messageId
			FROM `procedure` 
			LEFT JOIN tempprocedure ON tempprocedure.procedureId = `procedure`.procedureId
			WHERE `procedure`.subscriberId = 1 OR `procedure`.subscriberId = '{$_SESSION['subscriberId']}';";
			
		$aRows	= $this->fetchAll($sql);
		foreach($aRows as $row) {
			$aReturn[$row['procedureId']]	= $row;
		}
		return $aReturn;
	}
	
	/**
	 * Deletes a procedure even on the temporary table
	 */
	public function deleteProcedure($procId)
	{
		$this->dbc->exec("DELETE FROM `tempprocedure` WHERE procedureId = '$procId'");
		return $this->dbc->exec("DELETE FROM `procedure` WHERE procedureId = '$procId'");
	}
	
	public function getCmTitleAndCategory($messageId)
	{
		$sql	= "SELECT category.categoryId, category.categoryName, message.messageTitle
			FROM category
			INNER JOIN message ON message.messageCategoryId = category.categoryId
			WHERE message.messageId = " . $messageId
			. " AND subscriberId = " . CM;
		$row	= $this->fetchAll($sql);
		if (isset($row[0])) {
			return $row[0];
		}
		return NULL;
	}
	
	/**
	 * Inserts a category if it does not exist for Subscriber yet.
	 * @return integer $categoryId
	 */
	public function copyCmCategory($cmCategoryId, $categoryName)
	{
		// Set subscriber from CM to user.
		$sql	= "INSERT INTO category SET 
				subscriberId = {$_SESSION['subscriberId']},
				categoryName = '{$categoryName}'
			ON DUPLICATE KEY UPDATE
				categoryId = LAST_INSERT_ID(categoryId)
			";
		$categoryId	= $this->safeExecInsert($sql);
		if ($categoryId == SQL_ERROR) {
			return 0;
		}
		return $categoryId;
	}
	
	/**
	 * Inserts a message if "messageTitle + categoryId" does not exist for Subscriber yet.
	 * @param integer $cmMessageId       CM Message ID to copy
	 * @param string  $messageTitle      CM Message Title
	 * @param integer $ownCategoryId     Subscriber Category ID to be used for the new copy
	 * @return integer $messageId
	 */
	public function copyCmMessage($cmMessageId, $messageTitle, $ownCategoryId)
	{
		// Check if messageTitle for this Category ID exists for our Subscriber.
		$sql	= "SELECT messageId FROM message 
			WHERE messageTitle = '{$messageTitle}'
				AND messageCategoryId = '{$ownCategoryId}'
			";
		if ($row = $this->fetchAll($sql)) {
			return $row[0]['messageId'];
		}
		
		// Get CM source row to copy.
		$sql	= "SELECT sourcemsg.* FROM message AS sourcemsg WHERE sourcemsg.messageId = " . $cmMessageId;
		$source	= $this->fetchAll($sql);
		if (!$source) {
			return 0;
		}
		
		// Let's insert a copy now.
		$sql	= "INSERT INTO message SET
				messageCategoryId = {$ownCategoryId},
				messageTitle = ?,
				messageBody = ?,
				isPractice = {$source[0]['isPractice']},
				messageChannel = '{$source[0]['messageChannel']}'
			";
		
		$messageId	= $this->safeExecInsertWithParam($sql, array($source[0]['messageTitle'], $source[0]['messageBody']));
		if ($messageId === SQL_ERROR) {
			return 0;
		}
		return $messageId;
	}
	
	/**
	 * Copies a CM Procedure to be used by the Subscriber.
	 * @param integer $procedureId
	 * @param integer $ownMessageId
	 * @return integer
	 */
	public function copyCmProc($procedureId, $ownMessageId, $frmProcedureName)
	{
		// Get CM source row to copy.
		$sql	= "SELECT sourceproc.* FROM `procedure` AS sourceproc WHERE sourceproc.procedureId = " . $procedureId;
		$source	= $this->fetchAll($sql);
		if (!$source) {
			return 0;
		}
		
		$assign			= '';
		
		foreach ($source[0] as $key => $value) {
			if ($key == 'procedureId') {
				continue;
			}
			
			$assign		.= ($assign ? ', ' : NULL);
			
			if ($key == 'messageId') {
				$assign		.= "`$key` = " . $ownMessageId;
				
			} elseif ($key == 'subscriberId') {
				$assign		.= "`$key` = " . $_SESSION['subscriberId'];
				
			} elseif ($key == 'isActive') {
				$assign		.= "`$key` = 0";
				
			} elseif ($key == 'procName') {
				$assign		.= "`$key` = '{$frmProcedureName}'";
				
			} elseif ($key == 'scannerEvalDate') {
				$assign		.= "`$key` = '0000-00-00 00:00:00'";
				
			} elseif ($key == 'lastUpdateDate') {
				$assign		.= "`$key` = NOW()";
				
			} elseif ($key == 'procCategoryId') {
				/**
				 * To do: possible removal of this column.
				 */
				$assign		.= "`$key` = NULL";
				
			} else {
				$assign		.= "`$key` = '$value'";
			}
		}
		
		// Set subscriber from CM to user.
		$sql		= "INSERT INTO `procedure` SET {$assign}";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			if ($this->sqlerrno == '23000') {
				return -1;
			}
			return 0;
		}
		return $result;
	}
}

// eof
