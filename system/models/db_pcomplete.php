<?php
/**
 * Model file of controller: pcomplete
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
 * Database access functions of controller: pcomplete
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Delete temporary row holder
	 * @param integer $rowId
	 */
	public function removeTemporaryProc($rowId)
	{
		$sql	= "DELETE FROM tempprocedure WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
	
	/**
	 * Set lastUpdateDate to "NOW() + 1 second" so that this Proc will be scanned on the next Proc Scan.
	 */
	public function setLastUpdateDate($procedureId)
	{
		$sql    = "UPDATE `procedure` SET
				lastUpdateDate  = NOW() + INTERVAL 1 SECOND
			WHERE procedureId = {$procedureId}
			";
		$this->safeExec($sql);
	}
}

// eof
