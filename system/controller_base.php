<?php
/**
 * Abstract class file for controller
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    controller_base
 */

/**
 * Abstract class for controller.  All controllers extends this class.
 * @package    controller_superclass
 */
Abstract Class Controller_Base
{
	/**
	 * Registry class-wide variable
	 */
	protected $registry;
	
	/**
	 * Template-name variable
	 */
	protected $templateName;

	function __construct($registry, $controllerName)
	{
		$this->registry		= $registry;
		$this->templateName	= $controllerName;
	}

	abstract function index();

	/**
	 * Called to display animal and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			/**
			 * Session is the first and outdated implementation.
			 */
			$code		= $_SESSION['system']['message'];
			unset($_SESSION['system']['message']); // reset
		} else {
			/**
			 * Call using this will be /controller/success/saved
			 */
			$code		= $this->registry['router']->getArg(ARGUMENT_1);
		}
		
		if ($code == 'saved') {
			$this->registry['template']->set('autoHideTitle', 'Success!');
			$this->registry['template']->set('autoHideMessage', 'Your entry was successfully saved.');
		} elseif ($code == 'updated') {
			$this->registry['template']->set('autoHideTitle', 'Success!');
			$this->registry['template']->set('autoHideMessage', 'Your entry was successfully updated.');
		}
		
		$this->index();
	}
	
	/**
	 * Called to display error message
	 */
	public function error()
	{
		$SQLstate	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if (isset($_SESSION['system']['message'])) {
			switch ($_SESSION['system']['message']) {
				case 'duplicate':
					$this->registry['template']->set('autoHideTitle', 'Error');
					$this->registry['template']->set('autoHideMessage', 'Duplicate error: This one already exists.');
					break;
				
				case 'prerequisite':
					if (isset($_SESSION['system']['prerequisite'])) {
						$this->registry['template']->set('autoHideTitle', 'Pre-requisite');
						$this->registry['template']->set('autoHideMessage', addslashes($_SESSION['system']['prerequisite']));
						
						unset($_SESSION['system']['prerequisite']);
						break;
					}
					
				case 'Failure':
					if (isset($_SESSION['system']['autoHideMessage'])) {
						$this->registry['template']->set('autoHideTitle', lang_failure);
						$this->registry['template']->set('autoHideMessage', addslashes($_SESSION['system']['autoHideMessage']));
						
						unset($_SESSION['system']['autoHideMessage']);
						break;
					}
					
				default:
					$this->registry['template']->set('autoHideTitle', 'Error');
					$this->registry['template']->set('autoHideMessage', 'Unknown error: Unexpected error occurred! SQLSTATE: '
						. $SQLstate);
			}
			
			unset($_SESSION['system']['message']); // reset
			
		} else {
			$this->registry['template']->set('autoHideMessage', 'Unknown error: Unexpected error occurred! SQLSTATE: '
				. $SQLstate);
		}
		
		if (isset($_SESSION['system']['action'])) {
			$tmp	= $_SESSION['system']['action'];
			unset($_SESSION['system']['action']);
			
			$this->$tmp();
		} else {
			$this->index();
		}
	}

	/**
	 * Return true if $_SESSION['tempproc'] is set
	 * @return boolean
	 */
	public function isTempProc()
	{
		if (isset($_SESSION['tempproc'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Show the restored procedure creation
	 */
	public function showProcRestoredSess()
	{
		if ($this->willShowProcRestoredSess()) {
			if ($_SESSION['system']['message'] == '1)message') {
				$this->registry['template']->set('autoHideTitle', 'Restoring Previous Session');
				$this->registry['template']->set('autoHideMessage', 'Step 1 Message: &nbsp; &nbsp; <i>please continue...</i>');
				
			} elseif ($_SESSION['system']['message'] == '2)ifcrita') {
				$this->registry['template']->set('autoHideTitle', 'Restoring Previous Session');
				$this->registry['template']->set('autoHideMessage', 'Step 2 If Criteria A: &nbsp; &nbsp; <i>please continue...</i>');
				
			} elseif ($_SESSION['system']['message'] == '3)seltype') {
				$this->registry['template']->set('autoHideTitle', 'Restoring Previous Session');
				$this->registry['template']->set('autoHideMessage', 'Step 3 Procedure Type Select: &nbsp; &nbsp; <i>please continue...</i>');
			}
			
			unset($_SESSION['system']['message']); // reset
		}
	}
	
	/**
	 * Show the restored procedure creation message only once after login
	 * @return boolean
	 */
	private function willShowProcRestoredSess()
	{
		// If variable is not set, show once only.
		if (isset($_SESSION['system']['showProcMessage']) == false) {
		
			// Setting it to false here which makes it set.
			$_SESSION['system']['showProcMessage']	= false;
			
			if (isset($_SESSION['system']['message'])) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Formats date from mm/dd/yyyy to yyyy-mm-dd
	 * @param string
	 * @return string
	 */
	protected function formatDateInputToDb($entryDate)
	{
		if ($entryDate) {
			list($mm, $dd, $yyyy)		= explode('/', $entryDate);
			return "$yyyy-$mm-$dd";
		}
		return '0000-00-00';
	}
	
	/**
	 * Returns the translation.  Uses the default language: en if no equivalent is found.
	 * TODO: Default to English
	 */
	//~ protected function lang($i18nCode)
	//~ {
		//~ if (isset($_SESSION['lang'][CONTROLLER][$i18nCode])) {
			//~ return $_SESSION['lang'][CONTROLLER][$i18nCode];
		//~ }
		//~ if (isset($_SESSION['lang']['common'][$i18nCode])) {
			//~ return $_SESSION['lang'][CONTROLLER][$i18nCode];
		//~ }
		
		//~ /**
		 //~ * Return this as default which signals a missing language definition.
		 //~ */
		//~ return 'Lang-' . $i18nCode;
	//~ }
}

// eof
