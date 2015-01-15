<?php
/**
 * Model file of controller: pmessage
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
 * Database access functions of controller: pmessage
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves new row to temporary proc table
	 */
	public function procedureCreateTemp($newId, $step)
	{
		$sql	= "INSERT INTO tempprocedure SET
			procedureId = '{$newId}',
			subscriberId = '{$_SESSION['subscriberId']}',
			step = '{$step}'";
		
		$insert	= $this->dbc->prepare($sql);
		$insert->execute();
		return $insert->rowCount();
	}
	
	/**
	 * Saves a new Message
	 * @param string procedureName
	 * @param integer messageId
	 * @param integer nPriority
	 * @param integer nConsolidate
	 * @param integer nSendAnimalDead
	 * @return integer
	 */
	public function procedureCreateMessage($procedureName, $messageId, $nPriority, $nConsolidate, $nSendAnimalDead) {
		$sql	= "INSERT INTO `procedure` SET 
				subscriberId = '{$_SESSION['subscriberId']}',
				procName = '$procedureName', 
				messageId = '$messageId', 
				priority = '$nPriority', 
				consolidate = '$nConsolidate', 
				sendOnDeath = '$nSendAnimalDead'";
		var_dump($sql);
		$this->dbc->query($sql);
		return $this->dbc->lastInsertId();
	}
	
	/**
	 * Updates a Proc Message
	 */
	public function procedureUpdateMessage($rowId, $procedureName, $messageId, $nPriority, $nConsolidate, $nSendAnimalDead) {
		$sql	= "UPDATE `procedure` SET 
				subscriberId = '{$_SESSION['subscriberId']}',
				procName = '$procedureName', 
				messageId = '$messageId', 
				priority = '$nPriority', 
				consolidate = '$nConsolidate', 
				sendOnDeath = '$nSendAnimalDead'
			WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
	
	/**
	 * Returns id of procedure name for the current user.
	 * @param string $procedureName
	 * @return integer
	 */
	public function getIdOfProcName($procedureName)
	{
		$sql	= "SELECT procedureId FROM `procedure` 
			WHERE subscriberId = '{$_SESSION['subscriberId']}'
			AND procName = '" . trim($procedureName) . "'";
			
		$sth = $this->dbc->prepare($sql);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($row == false) {
			return 0;
		} else {
			return $row['procedureId'];
		}
	}

	/**
	 * Checks if a procedureId exists on Table: tempprocedure
	 * @param integer $procId
	 * @return boolean
	 */
	public function isTempProcedure($procId)
	{
		$sql	= "SELECT procedureId FROM tempprocedure
			WHERE procedureId = {$procId}
				AND isComplete = 0
			";
		$sth = $this->dbc->prepare($sql);
		$sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($row == false) {
			return false;
		} else {
			return true;
		}
	}
}

// eof
