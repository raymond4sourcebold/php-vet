<?php
/**
 * Controller file for Index page.
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
 * Controller for Index page.
 * @package    index
 */
Class Controller_Index Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		/**
		 * Default page is search
		 */
		header('location: /search');
	}
	
	/**
	 * PHP information for programmer's use.
	 */
	public function phpinfo()
	{
		phpinfo();
	}
}

// eof
