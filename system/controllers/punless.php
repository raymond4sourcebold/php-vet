<?php
/**
 * Controller file for Proc Entry: Unless Criteria B (seq 4) step page.
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
 * Controller for Proc Entry: Unless Criteria B (seq 4) step page.
 * @package    proc_unless_criteria_b
 */
Class Controller_Punless Extends Controller_Base
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
		
		$aDateCriteria	= array_merge(
			array(0 => array("criteriaCaptionId" => 1, "criteriaCode" => "sendDate")), // Send Date option is manually added.
			$this->registry['db']->getDateCriteria()
		);
		
		$this->registry['template']->set('aDateCriteria', $aDateCriteria);
		
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
			$this->saveUnlessCriteria($_SESSION['tempproc']['procedureId']);
			$success	= true;
		}
		
		if ($success) {
			$this->registry['db']->setProcStep('5)remind');
			header('location: /procedures');
		}
		
		return $success;
	}
	
	/**
	 * Calls DB function to save unless criteria.
	 * @param integer $procedureId Procedure ID
	 */
	private function saveUnlessCriteria($procedureId)
	{
		$strValue	= '';
		
		if (isset($_POST["selRefId1"])) {
			foreach ($_POST["selRefId1"] as $key => $value) {
				$strValue	.= $strValue ? ',' : NULL;
				
				$nYrs		= 0;
				$nMos		= 0;
				
				if ($_POST["nMonths"][$key] >= 12) {
					$nYrs	= floor($_POST["nMonths"][$key] / 12);
					$nMos	= $_POST["nMonths"][$key] - ($nYrs * 12);
				} else {
					$nMos	= $_POST["nMonths"][$key];
				}
					
				$nYrs		+= $_POST["nYears"][$key];
				
				
				$strValue	.= $value  . ':' . $_POST["selRefId2"][$key]
					. ':' . $_POST["frmOperator"][$key]
					. ':' . round(($nYrs * 365) + ($nMos * (365/12)) + $_POST["nDays"][$key]);
			}
		}
		
		$this->registry['db']->procedureSaveUnlessCritB($procedureId, $strValue);
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
	 * Entry point when a user clicks back button
	 */
	function back()
	{
		$this->index();
	}
}

// eof
