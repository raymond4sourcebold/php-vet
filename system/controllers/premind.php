<?php
/**
 * Controller file for Proc Entry: Reminder (seq 5) step page.
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
 * Controller for Proc Entry: Reminder (seq 5) step page.
 * @package    proc_remind
 */
Class Controller_Premind Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($this->processPost() == false) {
				header('location: /error/abort/premind 5) Reminders. POST processing went wrong.');
			}
			exit;
		}
		
		$aDateCriteria		= $this->registry['db']->getDateCriteria();
		
		$this->registry['template']->set('aDateCriteria', $aDateCriteria);
		
		$this->buildRemindMessage();
		
		
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
			$this->saveReminders($_SESSION['tempproc']['procedureId']);
			$success	= true;
		}
		
		if ($success) {
			$this->registry['db']->setProcStep('6)punch');
			header('location: /procedures');
		}
		
		return $success;
	}
	
	/**
	 * Calls DB function to save unless criteria.
	 */
	private function saveReminders($procedureId)
	{
		if ($_POST['radReminder'] == 0) {
			$this->registry['db']->procedureSaveReminders(
				$procedureId,
				0,
				0,
				0,
				0,
				0
			);
		} else {
			$this->registry['db']->procedureSaveReminders(
				$procedureId,
				$_POST['radReminder'],
				$_POST['selMessage'],
				$_POST['eventDate'],
				$_POST['reminderAfterNdays1'],
				$_POST['reminderAfterNdays2']
			);
		}
	}
	
	public function buildRemindMessage()
	{
		$allCatMsg	= $this->registry['db']->getAllCategoriesMessages();
		
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
	}
	
	/**
	 * Entry point for a restored session
	 */
	function restoresession()
	{
		$this->showProcRestoredSess();
		$this->index();
	}

	function success()
	{
		$this->index();
	}
	
	function back()
	{
		$this->index();
	}
}

// eof
