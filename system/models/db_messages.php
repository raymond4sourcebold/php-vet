<?php
/**
 * Model file of controller: messages
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
 * Database access functions of controller: messages
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Gets message channel
	 * @return array
	 */
	public function getMessageChannels()
	{
		return $this->getEnumValues('message', 'messageChannel');
	}

	/**
	 * Gets message variables
	 * @return array
	 */
	public function getMessageVariables()
	{
		//~ $aReturn	= array();
		
		//~ $sql	= "SELECT msgVarId, msgVarInsertCode
			//~ FROM messageVariable
			//~ ORDER BY msgVarInsertCode";
		
		//~ foreach($this->dbc->query($sql) as $row) {
			//~ $aReturn[$row['msgVarId']]	= $row['msgVarInsertCode'];
		//~ }
		//~ return $aReturn;
		return array(
			1 => '[' . lang_msgvar_btn_animal . '/]',
			2 => '[' . lang_msgvar_btn_owner . '/]',
			3 => '[' . lang_msgvar_btn_honorary . '/]'
		);
	}
	
	/**
	 * Saves a new Message
	 */
	public function saveMessage($categoryId, $title, $body, $radioChannel, $isPractice)
	{
		$sql	= "INSERT INTO message
			SET
				messageCategoryId = {$categoryId}, 
				messageTitle = '{$title}',
				messageBody = '{$body}',
				messageChannel = '{$radioChannel}',
				isPractice = {$isPractice}
			ON DUPLICATE KEY UPDATE 
				messageId = LAST_INSERT_ID(messageId),
				messageCategoryId = {$categoryId}, 
				messageTitle = '{$title}',
				messageBody = '{$body}',
				messageChannel = '{$radioChannel}',
				isPractice = {$isPractice}
			";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			return 0;
		}
		return $result;
	}
	
	/**
	 * Saves a new Message
	 */
	public function updateMessage($updateMsgId, $categoryId, $title, $body, $radioChannel, $isPractice) {
		$sql	= "UPDATE message SET 
				messageCategoryId = '$categoryId', 
				messageTitle = '$title',
				messageBody = '$body',
				messageChannel = '$radioChannel',
				isPractice = '$isPractice'
			WHERE messageId = '$updateMsgId'";
		$insert	= $this->dbc->prepare($sql);
		$insert->execute();
		return $insert->rowCount();
	}

	/**
	 * Get messages from database
	 */
	public function getMessages($msgId=NULL)
	{
		$cond	= '';
		
		if ($_SESSION['subscriberId'] != CM) {
			$cond	= " WHERE category.subscriberId = '{$_SESSION['subscriberId']}' ";
		}
		
		if ($msgId) {
			$cond	.= ($cond ? ' AND ' : ' WHERE ');
			$cond	.= " messageId = '$msgId' ";
		}
		
		$sql	= "SELECT messageId, messageCategoryId, messageTitle, messageBody, isPractice, messageChannel
			FROM message
			INNER JOIN category ON category.categoryId = message.messageCategoryId
			{$cond}
			";
		$sth	= $this->dbc->prepare($sql);
		$sth->execute();
		return $sth->fetchAll();
	}
	
	/**
	 * Deletes one message
	 * @param integer $messageId
	 * @return integer
	 */
	public function deleteMessage($messageId)
	{
		return $this->dbc->exec("DELETE FROM message WHERE messageId = '$messageId'");
	}
	
	/**
	 * Saves a new Category
	 * @param string $newCategory
	 */
	public function saveCategory($newCategory)
	{
		$sql	= "INSERT INTO category (
				categoryName,
				subscriberId
			) VALUES (
				'{$newCategory}',
				{$_SESSION['subscriberId']}
			)
			ON DUPLICATE KEY UPDATE 
				categoryId = LAST_INSERT_ID(categoryId)
			";
		$result		= $this->safeExecInsert($sql);
		
		if ($result == SQL_ERROR) {
			return 0;
		}
		return $result;
	}
	
	/**
	 * Gets Enum values from a field on a table.
	 */
	protected function getEnumValues($table, $field) {
		$sql	= "SHOW COLUMNS FROM `$table` LIKE '$field'";
		$stmt	= $this->dbc->query($sql);
		$row	= $stmt->fetch(PDO::FETCH_ASSOC);
		
		if ($row) {
			$options	= explode("','", preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $row['Type'])); 
			$aReturn 	= array();
			$x = 1;
			
			foreach ($options as $value) {
				$aReturn[$x++]	= $value;
			}
			
			return $aReturn;
		}
		return NULL;
	}
	
	/**
	 * Gets message categories
	 * @return array
	 */
	public function getMessageCategories()
	{
		$aReturn	= array();
		
		$sql	= "SELECT /*categoryId,*/
				categoryName
			FROM category
			WHERE subscriberId = 1 OR subscriberId = '{$_SESSION['subscriberId']}'
			ORDER BY categoryName ASC /*,subscriberId DESC*/";
		
		$holdCatName	= '';
		
		foreach ($this->dbc->query($sql) as $row) {
			if ($holdCatName == $row['categoryName']) {
				// Disregard duplicate owned by CM.
				continue;
			}
			$holdCatName			= $row['categoryName'];
			$aReturn[$row['categoryName']]	= $row['categoryName'];
		}
		
		return $aReturn;
	}
	
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
	 *
	 */
	public function getTimeCriteria()
	{
	}
}

// eof
