<?php
/**
 * Model file of controller: ppunch
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
 * Database access functions of controller: ppunch
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves proc punch list step
	 * @param integer $rowId
	 * @param array $strUnlessCriteria
	 * @return integer
	 */
	function procedurePunchList($rowId, $isPracticeProc, $isActiveProc)
	{
		if ($isPracticeProc == 'on') {
			$isPracticeProc		= 1;
		} else {
			$isPracticeProc		= 0;
		}
		
		if ($isActiveProc == 'on') {
			$isActiveProc		= 1;
		} else {
			$isActiveProc		= 0;
		}
		
		$sql	= "UPDATE `procedure` SET 
				isPractice = '{$isPracticeProc}',
				isActive = '{$isActiveProc}'
			WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
}

// eof
