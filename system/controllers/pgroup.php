<?php
/**
 * Controller file for Proc Entry: Group Proc (seq 3b) step page.
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
 * Controller for Proc Entry: Group Proc (seq 3b) step page.
 * @package    proc_group
 */
Class Controller_Pgroup Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($this->processPost() == false) {
				header('location: /error/abort/pgroup 3) Send Date: Group. POST processing went wrong.');
			}
			exit;
		}
		
		$this->registry['template']->set('aRecur', $this->registry['const']->getSendDateRecur());
		
		$this->registry['template']->set('javascript', array('jquery/jquery.datePicker', 'jquery/jquery.bgiframe.min', 'jquery/date', 'validator_date'));
		$this->registry['template']->set('css', array('styleG', 'datePicker'));
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
			$this->saveGroupProcSendDate($_SESSION['tempproc']['procedureId']);
			$success	= true;
		}
		
		if ($success) {
			$this->registry['db']->setProcStep('4)unless');
			header('location: /procedures');
		}
		
		return $success;
	}
	
	/**
	 * Calls DB function to save group procedure send date.
	 * @return integer
	 */
	private function saveGroupProcSendDate($procedureId)
	{
		list($mm, $dd, $yyyy)	= explode('/', $_POST['frmSendDate']);
		
		$this->registry['db']->saveProcSendDate(
			$procedureId,
			NULL,
			NULL,
			NULL,
			NULL,
			(isset($_POST['selRecur']) ? $_POST['selRecur'] : NULL),
			"$yyyy-$mm-$dd"
		);
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
	 * Alias for index() that is used when previous processing is successful.
	 */
	function success()
	{
		$this->index();
	}
	
	/**
	 * Alias for index() that is used when user clicked on the back button.
	 */
	function back()
	{
		$this->index();
	}
}

// eof
