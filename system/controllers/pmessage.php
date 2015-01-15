<?php
/**
 * Controller file for Proc Entry: Message (seq 1) step page.
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
 * Controller for Proc Entry: Message (seq 1) step page.
 * @package    proc_message
 */
Class Controller_Pmessage Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($this->processPost() == false) {
				header('location: /error/abort/Step 1) Message. POST processing went wrong.');
			}
			exit;
		}
		
		$this->registry['template']->set('tempProc', 'test');
		$this->buildPmessage();
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
			$this->updateProcMessage($_SESSION['tempproc']['procedureId']);
			$this->registry['db']->setProcStep('2)ifcrita');
			$_SESSION['system']['message']		= 'updated';
			$success	= true;
			
		} else {
			if ($resultId = $this->insertProcMessage()) {
				$_SESSION['updateRowId']		= $resultId;
				$_SESSION['system']['message']		= 'saved';
				
				if ($this->insertTempProc($resultId) == false) {
					header('location: /error/abort/Step 1) Message. Temporary proc not created.');
					exit;
				}
				
				$success	= true;
			}
		}
		
		if ($success) {
			$_SESSION['system']['showProcMessage']		= false;
			header('location: /procedures');
		}
			
		return $success;
	}
	
	/**
	 * Entry point for reviewing a previous page
	 */
	function back()
	{
		$this->index();
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
	 * Saves proc temporary row
	 * @param integer $newProcId New Procedure ID
	 * @return integer
	 */
	function insertTempProc($newProcId)
	{
		return $this->registry['db']->procedureCreateTemp(
			$newProcId,
			'2)ifcrita'
		);
	}
	
	/**
	 * Saves new ProcMessage
	 * @return integer
	 */
	private function insertProcMessage()
	{
		return $this->registry['db']->procedureCreateMessage(
			$_POST["frmProcedureName"],
			$_POST["selMessage"],
			$_POST["selPriority"],
			isset($_POST["chkConsolidate"]) ? 1 : 0,
			isset($_POST["chkSendAnimalDead"]) ? 1 : 0
		);
	}
	
	/**
	 * Updates ProcMessage
	 * @param integer $rowId Procedure ID
	 * @return integer
	 */
	private function updateProcMessage($rowId)
	{
		return $this->registry['db']->procedureUpdateMessage(
			$rowId,
			$_POST["frmProcedureName"],
			$_POST["selMessage"],
			$_POST["selPriority"],
			isset($_POST["chkConsolidate"]) ? 1 : 0,
			isset($_POST["chkSendAnimalDead"]) ? 1 : 0
		);
	}
	
	/**
	 * Builds UI
	 */
	public function buildPmessage()
	{
		$allCatMsg	= $this->registry['db']->getAllCategoriesMessages();
		
		if (empty($allCatMsg)) {
			$_SESSION['system']['message']		= 'prerequisite';
			$_SESSION['system']['prerequisite']	= lang_no_message_create_first;
			$_SESSION['system']['action']		= 'create';
			
			header('location: /messages/error');
			exit;
		}
		
		foreach ($allCatMsg as $aCatMsg) {
			$aCategory[$aCatMsg['categoryId']]	= $aCatMsg['categoryName'];
			$aMsgTitle[$aCatMsg['messageId']]	= $aCatMsg['messageTitle'];
			$aMsgBody[$aCatMsg['messageId']]	= $aCatMsg['messageBody'];
			
			$aCatMsgId[$aCatMsg['categoryId']][]	= $aCatMsg['messageId'];
			$aCatMsgTitle[$aCatMsg['categoryId']][$aCatMsg['messageId']]	= $aCatMsg['messageTitle'];
		}
		
		$this->registry['template']->set('aCategory', $aCategory);
		$this->registry['template']->set('aMsgTitle', $aMsgTitle);
		$this->registry['template']->set('aMsgBody', $aMsgBody);
		$this->registry['template']->set('aCatMsgId', $aCatMsgId);
		$this->registry['template']->set('aCatMsgTitle', $aCatMsgTitle);
		
		$aPriority	= $this->registry['const']->getPriorityArray();
		$this->registry['template']->set('aPriority', $aPriority);
	}

	/**
	 * Ajax called to check if a procedure name exists on Table: procedure but not a temporary proc.
	 *	/pmessage/checkprocname/<procedure name to test>
	 */
	public function checkprocname()
	{
		$echoResult	= 'NOT_FOUND';
		
		$procedureName	= $this->registry['router']->getArg(ARGUMENT_1);
		
		$rowId	= $this->registry['db']->getIdOfProcName($procedureName);
		
		if ($rowId) {
			/**
			 * Check if we are editing an existing Proc
			 */
			if (isset($_SESSION['edit_procId'])) {
				// Yes, we are!
				if ($rowId == $_SESSION['edit_procId']) {
					// Ok, this Procedure name already exists but we have no problem of possible Proc name duplication
					//	because this is the one we are currently in the process of updating.
					$echoResult	= 'FOUND_SELF';
				} else {
					// Bad: Renaming to a Procedure name that exists
					$echoResult	= 'RENAME_TO_EXIST';
				}
			} else {
				// No, we are creating a new Proc.
				if ($this->registry['db']->isTempProcedure($rowId) == false) {
					// Bad: Procedure name exists
					$echoResult	= $rowId;
				}
			}
		}
		
		echo $echoResult;
	}
}

// eof
