<?php
/**
 * Controller file for Proc Entry: Complete step page.
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
 * Controller for Proc Entry: Complete step page.
 * @package    proc_complete
 */
Class Controller_Pcomplete Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			header('location: /procedures/gotostart');
			exit;
		}
		
		$this->buildCriterionA();
		
		/**
		 * $_SESSION['edit_procId'] is set when editing a complete proc
		 */
		if (isset($_SESSION['edit_procId'])) {
			$this->registry['template']->set('showGoBackButton', true);
		} else {
			$this->registry['template']->set('showGoBackButton', false);
			
			// We only remove temporary proc of NEW proc entries.
			$this->registry['db']->removeTemporaryProc($_SESSION['tempproc']['procedureId']);
		}
		
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
		
		// Display is done.
		
		
		$this->postShowHousekeeping();
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
	 * Builds UI
	 */
	public function buildCriterionA()
	{
		$aSpecies	= $this->registry['db']->getSpecies();
		$this->registry['template']->set('aRefSpecies', $aSpecies);
		
		$aGenders	= $this->registry['db']->getGenders();
		$this->registry['template']->set('aRefGenders', $aGenders);
		
		$aBoolCriteria	= $this->registry['db']->getBooleanCriteria(true);
		$this->registry['template']->set('aBoolCriteria', $aBoolCriteria);
		
		$aQtyCriteria	= $this->registry['db']->getQuantityCriteria(true);
		$this->registry['template']->set('aQtyCriteria', $aQtyCriteria);
	}
	
	/**
	 * This function is called after rendering the report.
	 */
	private function postShowHousekeeping()
	{
		/**
		 * Post template->show()
		 *	Check if something has changed on the Proc
		 */
		if (isset($_SESSION['edit_procId']) && isset($_SESSION['old_procAllColumnsConcat'])) {
			
			if ($_SESSION['old_procAllColumnsConcat'] != $_SESSION['procAllColumnsConcat']) {
				// Something has changed so let's update procedure.lastUpdateDate
				$this->registry['db']->setLastUpdateDate($_SESSION['edit_procId']);
			}
			
			// Everything is done, housekeeping of session.
			unset($_SESSION['procAllColumnsConcat'],
				$_SESSION['old_procAllColumnsConcat'],
				$_SESSION['tempproc']
			);
			
			// But not for $_SESSION['edit_procId'], let's allow the Subscriber to go back again to Proc msg selection.
		}
	}
}

// eof
