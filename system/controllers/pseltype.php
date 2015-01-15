<?php
/**
 * Controller file for Proc Entry: Select Proc Type (seq 3a) step page.
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
 * Controller for Proc Entry: Select Proc Type (seq 3a) step page.
 * @package    proc_select_type
 */
Class Controller_Pseltype Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($this->processPost() == false) {
				header('location: /error/abort/pifcrita 2) If Criteria A. POST processing went wrong.');
			}
			exit;
		}
		
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Processes page submission and redirect user to next page
	 * @return boolean
	 */
	private function processPost()
	{
		$success	= false;
		
		if ($this->isTempProc()) {
			$this->registry['db']->setProcType($_SESSION['tempproc']['procedureId'], $_POST['radProcType']);
			$success	= true;
			
		} elseif ($_POST['updateRowId']) {
			if ($this->updateIfCriteriaA($_POST['updateRowId'])) {
				$_SESSION['system']['message']		= 'updated';
				$success	= true;
			}
		} else {
			if ($this->insertIfCriteriaA()) {
				$_SESSION['system']['message']		= 'saved';
				$success	= true;
			}
		}
		
		if ($success) {
			if ($_POST['radProcType'] == 'one') {
				$this->registry['db']->setProcStep('3)onestep'); // one
				
			} elseif ($_POST['radProcType'] == 'two') {
				$this->registry['db']->setProcStep('3)twostep'); // two
				
			} elseif ($_POST['radProcType'] == 'recur') {
				$this->registry['db']->setProcStep('3)recur'); // recur
				
			} else {
				$this->registry['db']->setProcStep('3)group'); // group
			}
			
			header('location: /procedures');
		}
		
		return $success;
	}
	
	/**
	 * Entry point for a restored session
	 */
	function restoresession()
	{
		$this->showProcRestoredSess();
		$this->index();
	}

	/**
	 * Entry point for successful processing
	 */
	function success()
	{
		$this->index();
	}
	
	/**
	 * Entry point when back button is clicked
	 */
	function back()
	{
		$this->index();
	}
}

// eof
