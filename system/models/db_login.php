<?php
/**
 * Model file of controller: login
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
 * Database access functions of controller: login
 * @package    db_class
 */
Class Db Extends Model_Base
{
	/**
	 * Checks login and password
	 */
	public function processLogin($login, $password)
	{
		$sql	= 'SELECT * FROM subscriber WHERE subscriberLogin = \''.$login.'\' AND subscriberPassword = MD5(\''.$password.'\')';
		
		$stmt	= $this->dbc->query($sql);
		$row	= $stmt->fetch(PDO::FETCH_ASSOC);
		
		if ($row) {
			return $row;
		}
		return false;
	}
}

// eof
