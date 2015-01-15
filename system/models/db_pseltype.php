<?php
/**
 * Model file of controller: pseltype
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
 * Database access functions of controller: pseltype
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Sets value for procedure type: 'one', 'two', 'recur', 'group'
	 * @param integer $rowId
	 * @param string $procType
	 * @return integer
	 */
	function setProcType($rowId, $procType)
	{
		if (in_array($procType, array('one', 'two', 'recur', 'group')) == false) {
			header('location: /error/abort/pseltype 3) Send Date. Invalid procedure type ' . $procType . ', expecting one, two, recur, group.');
			exit;
		}
		
		$sql	= "UPDATE `procedure` SET 
			procSteps = '{$procType}'
			WHERE procedureId = '" . $rowId . "'";
		return $this->dbc->exec($sql);
	}
}

// eof
