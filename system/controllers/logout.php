<?php
/**
 * Controller file for Logout function.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    controller
 */

/**
 * Controller for Logout function.
 * @package    logout
 */
Class Controller_Logout Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		$lang	= $_SESSION['subscriberLang'];
		
		unset($_SESSION);
		session_destroy();
		session_start();
		
		// Restore language to session
		$_SESSION['subscriberLang']	= $lang;
		
		header('location: /login/terminated');
		exit;
	}
}

// eof
