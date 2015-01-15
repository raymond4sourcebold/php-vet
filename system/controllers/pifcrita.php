<?php
/**
 * Controller file for Proc Entry: If Criteria A (seq 2) step page.
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
 * Controller for Proc Entry: If Criteria A (seq 2) step page.
 * @package    proc_if_criteria_a
 */
Class Controller_Pifcrita Extends Controller_Base
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
		
		$this->buildCriterionA();
		
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Alias for index() that is used when user clicked on the back button.
	 */
	function back()
	{
		$this->index();
	}
	
	/**
	 * Processes page submission and redirect user to next page
	 * @return boolean
	 */
	private function processPost()
	{
		$success	= false;
		
		if ($this->isTempProc()) {
			$this->updateProcIfcrita($_SESSION['tempproc']['procedureId']);
			$this->registry['db']->setProcStep('3)seltype');
			$_SESSION['system']['message']		= 'updated';
			$success	= true;
			
		} elseif ($_POST['updateRowId']) {
			if ($this->updateIfCriteriaA($_POST['updateRowId'])) {
				$_SESSION['system']['message']		= 'updated';
				$success	= true;
			}
		}
		
		if ($success) {
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
	 * Alias for index() that is used when previous processing is successful.
	 */
	function success()
	{
		$this->index();
	}
	
	/**
	 * Updates IfCriteriaA
	 * @return integer
	 */
	private function updateProcIfcrita($rowId)
	{
		return $this->registry['db']->procedureUpdateIfcrita(
			$rowId,
			$_POST['frmSpecie'],
			$_POST['frmGender']
		);
	}
	
	/**
	 * Builds UI
	 */
	public function buildCriterionA()
	{
		$aSpecies	= $this->registry['db']->getSpecies();
		$this->registry['template']->set('aSpecies', $aSpecies);
		
		$aGenders	= $this->registry['db']->getGenders();
		$this->registry['template']->set('aGenders', $aGenders);
		
		$aBoolCriteria	= $this->registry['db']->getBooleanCriteria();
		$this->registry['template']->set('aBoolCriteria', $aBoolCriteria);
		
		$aQtyCriteria	= $this->registry['db']->getQuantityCriteria();
		$this->registry['template']->set('aQtyCriteria', $aQtyCriteria);
	}
}

// eof
