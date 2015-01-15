<?php
/**
 * Controller file for Proc Entry: Punch (seq 6) step page.
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
 * Controller for Proc Entry: Punch (seq 6) step page.
 * @package    proc_punch
 */
Class Controller_Ppunch Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			$result		= $this->processPost();
			
			if ($result == true) {
				/**
				 * Everything is finalized for this Procedure.  The next step only shows a report
				 *	but these series of Proc creation/update steps now stops here.
				 *
				 *	Last task: Update procedure.lastUpdateDate on next step only if something has changed.
				 *		We use $_SESSION['procAllColumnsConcat'] to determine whether a change has occured.
				 */
				if (isset($_SESSION['edit_procId'])) {
					$_SESSION['old_procAllColumnsConcat']	= $_SESSION['procAllColumnsConcat'];
					unset($_SESSION['procAllColumnsConcat']);
				}
				
				$this->registry['db']->setProcStep('7)complete');
				header('location: /procedures');
			} else {
				header('location: /error/abort/ppunch 6) Punch List. POST processing went wrong.');
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
			$this->savePunchList($_SESSION['tempproc']['procedureId']);
			$success	= true;
		}
		
		return $success;
	}
	
	/**
	 * Calls DB function to save unless criteria.
	 * @param integer $procedureId Procedure ID
	 */
	private function savePunchList($procedureId)
	{
		$this->registry['db']->procedurePunchList(
			$procedureId,
			(isset($_POST['isPracticeProc']) ? 'on' : NULL),
			(isset($_POST['isActiveProc']) ? 'on' : NULL)
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
	 * Entry point for successful processing
	 */
	function success()
	{
		$this->index();
	}
	
	/**
	 * Entry point when a user clicks on back button
	 */
	function back()
	{
		$this->index();
	}
}

// eof
