<?php
/**
 * Controller file for Proc Entry: One-Step Proc (seq 3b) step page.
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
 * Controller for Proc Entry: One-Step Proc (seq 3b) step page.
 * @package    proc_one_step
 */
Class Controller_Ponestep Extends Controller_Base
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
		
		$this->registry['template']->set('aRecur', $this->registry['const']->getSendDateRecur());
		
		$aDateCriteria	= $this->registry['db']->getDateCriteria();
		$this->registry['template']->set('aDateCriteria', $aDateCriteria);
		$this->registry['template']->set('aOffset', $this->registry['const']->getSendDateOffset());
		$this->registry['template']->set('aAnticipation', $this->registry['const']->getSendDateAnticipation());
		
		var_dump($aDateCriteria);
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
			$this->saveOneStepProcSendDate($_SESSION['tempproc']['procedureId']);
			$success	= true;
		}
		
		if ($success) {
			$this->registry['db']->setProcStep('4)unless');
			header('location: /procedures');
		}
		
		return $success;
	}
	
	/**
	 * Calls DB function to save One-Step procedure send date.
	 * @param integer $procedureId Procedure ID
	 * @return integer
	 */
	private function saveOneStepProcSendDate($procedureId)
	{
		$this->registry['db']->saveProcSendDate(
			$procedureId,
			$_POST['selReferenceDateId'],
			$_POST['selOffset'],
			$_POST['selAnticipation'],
			NULL,
			($_POST['radRecurring'] && isset($_POST['selRecur']) ? $_POST['selRecur'] : NULL)
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
	 * Entry point for a success processing
	 */
	function success()
	{
		$this->index();
	}
	
	/**
	 * Entry point for a user that clicked the back button
	 */
	function back()
	{
		$this->index();
	}
}

// eof
