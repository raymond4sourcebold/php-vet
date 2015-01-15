<?php
/**
 * Controller file for Login page.
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
 * Controller for Login page.
 * @package    login
 */
Class Controller_Login Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		/**
		 * Check if user is already authenticated
		 */
		if (isset($_SESSION['authorized_user'])) {
			// Redirect to home page
			header('location: /');
		}
		
		if ($_POST) {
			if ($this->processLogin() == true) {
				/**
				 * Get settings on criteriaCaption and put it in session variables for easy reference.
				 */
				$_SESSION['settings']	= $this->registry['db']->getCriteriaCaptionSettings();
				
				// Redirect the user to home page
				header('Location: /');
				exit;
				
			} else {
				$this->registry['template']->set('error', lang_wrong_magic_word);
			}
		}
		
		$this->registry['template']->set('css', array('styleL'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Sets termination message
	 */
	public function terminated()
	{
		$this->registry['template']->set('error', lang_session_terminated);
		$this->index();
	}
	
	/**
	 * Processes user login
	 */
	private function processLogin()
	{
		if ($row = $this->registry['db']->processLogin($_POST['login'], $_POST['password'])) {
			$_SESSION['authorized_user']		= true;
			$_SESSION['subscriberId']		= $row['subscriberId'];
			$_SESSION['subscriberLang']		= $row['subscriberLanguage'];
			
			/**
			 * Communication plan variables
			 */
			$_SESSION['commplan']['sendThreshold']	= $row['sendThreshold'];
			
			$aTmp		= explode(':', $row['perClientQuotaCFlow']);
			
			if (isset($aTmp[0]) && isset($aTmp[1]) && isset($aTmp[2])) {
				$_SESSION['commplan']['pcq_limit_priority']	= $aTmp[0];
				$_SESSION['commplan']['pcq_duration']		= $aTmp[1];
				$_SESSION['commplan']['pcq_qty']		= $aTmp[2];
			}
			
			return true;
		}
		
		return false;
	}
}

// eof
