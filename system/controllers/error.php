<?php
/**
 * Controller file for Error page.
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
 * Controller for Error page.
 * @package    error
 */
Class Controller_Error Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		$this->registry['template']->show($this->templateName, 'error');
	}
	
	/**
	 * Sets template error message and calls index()
	 */
	public function abort()
	{
		$msg	= $this->registry['router']->getArg(ARGUMENT_1);
		$this->registry['template']->set('error', $msg);
		
		$this->index();
	}
}

// eof
