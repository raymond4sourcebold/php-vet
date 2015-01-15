<?php
/**
 * Model file of controller: punless
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
 * Database access functions of controller: punless
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves unless criterion B
	 * @param integer $rowId
	 * @param array $strUnlessCriteria
	 * @return integer
	 */
	function procedureSaveUnlessCritB($rowId, $strUnlessCriteria)
	{
		$sql	= "UPDATE `procedure` SET 
				procedureId = '{$rowId}',
				unlessCriteriaDateIdCsvc = '{$strUnlessCriteria}'
			WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
}

// eof
