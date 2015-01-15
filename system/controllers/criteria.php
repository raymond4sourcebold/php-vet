<?php
/**
 * Controller file for Criteria page.
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
 * Controller for Criteria page.
 * @package    criteria
 */
Class Controller_Criteria Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				if ($this->updateCriterion($_POST['updateRowId'])) {
					$_SESSION['system']['message']		= 'updated';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /criteria/success');
				}
			} else {
				if ($this->insertCriterion()) {
					$_SESSION['system']['message']		= 'saved';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /criteria/success');
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		$this->registry['template']->set('aCriteriaTypes', $this->registry['db']->getCriteriaTypes());
		
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}

	/**
	 * Builds Criteria display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$aCriteria		= $this->registry['db']->getCriteria();
		$this->registry['template']->set('aRows', $aCriteria);
	}

	/**
	 * Called to display criterion and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_criterion_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_criterion_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/criteria/delete/1
	 *	Where: criteria = controller, delete = this function, 1 = criterionId to delete
	 */
	public function delete()
	{
		$rowId		= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteCriterion($rowId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}

	
	/**
	 * Saves new criterion
	 */
	private function insertCriterion()
	{
		return $this->registry['db']->addCriterion($_POST['frmCriterionType'], $_POST['frmCaption']);
	}
	
	/**
	 * Updates criterion
	 */
	private function updateCriterion($rowId)
	{
		return $this->registry['db']->updateCriterion($rowId, $_POST['frmCriterionType'], $_POST['frmCaption']);
	}
}

// eof
