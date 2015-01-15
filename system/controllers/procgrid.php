<?php
/**
 * Controller file for Procedure List page.
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
 * Controller for Procedure List page.
 * @package    proc_grid
 */
Class Controller_ProcGrid Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if (isset($_POST['copyProcId'])) {
				if ($this->processCopy($_POST['copyProcId'])) {
					header('location: /procgrid/success');
				} else {
					header('location: /procgrid/error');
				}
				exit;
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Copies an existing CMI proc
	 * @param integer @procId
	 * @return boolean
	 */
	private function processCopy($procId)
	{
		$msgcatRow		= $this->registry['db']->getCmTitleAndCategory($_POST['procMsgId']);
		
		$ownCategoryId		= $this->registry['db']->copyCmCategory($msgcatRow['categoryId'], $msgcatRow['categoryName']);
		if ($ownCategoryId == 0) {
			$_SESSION['system']['message']		= 'copy_category_error';
			return false;
		}
		
		$ownMessageId		= $this->registry['db']->copyCmMessage($_POST['procMsgId'], $msgcatRow['messageTitle'], $ownCategoryId);
		if ($ownMessageId == 0) {
			$_SESSION['system']['message']		= 'copy_message_error';
			return false;
		}
		
		$procedureId		= $this->registry['db']->copyCmProc($procId, $ownMessageId, $_POST['frmProcedureName']);
		
		if ($procedureId == -1) {
			$_SESSION['system']['message']		= 'copy_procedure_duplicate_error';
			return false;
			
		} elseif ($procedureId == 0) {
			$_SESSION['system']['message']		= 'copy_procedure_error';
			return false;
		}
		
		$_SESSION['system']['message']		= 'copied';
		return true;
	}
	
	/**
	 * Builds Procedures display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$aProcedures		= $this->registry['db']->getProcGrid();
		$aFormattedProc		= array();
		
		foreach ($aProcedures as $row) {
			if ($row["subscriberId"] == 1) {
				$row['subscriberName']		= 'CMI';
			} else {
				$row['subscriberName']		= '<i>' . lang_mine . '<i>';
			}
			
			if ($row["isPractice"]) {
				$row["isPractice"]		= lang_word_yes;
			} else {
				$row["isPractice"]		= lang_word_no;
			}
			if ($row["isActive"]) {
				$row["isActive"]		= lang_word_yes;
			} else {
				$row["isActive"]		= lang_word_no;
			}
			
			$aFormattedProc[]	= $row;
		}
		
		$this->registry['template']->set('aRows', $aFormattedProc);
	}
	
	/**
	 * Shows an autohide success message
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'copied') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_cm_proc_copy_ok));
				
			}
			unset($_SESSION['system']['message']); // reset
		}
		$this->index();
	}
	
	/**
	 * Shows an autohide error message
	 */
	public function error()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'copy_procedure_duplicate_error') {
				$this->registry['template']->set('autoHideTitle', lang_failure);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_cm_proc_copy_err_duplicate));
				
			} elseif ($_SESSION['system']['message'] == 'copy_category_error') {
				$this->registry['template']->set('autoHideTitle', lang_failure);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_cm_proc_copy_err_category));
				
			} elseif ($_SESSION['system']['message'] == 'copy_message_error') {
				$this->registry['template']->set('autoHideTitle', lang_failure);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_cm_proc_copy_err_message));
				
			} elseif ($_SESSION['system']['message'] == 'copy_procedure_error') {
				$this->registry['template']->set('autoHideTitle', lang_failure);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_cm_proc_copy_err_proc));
			}
			unset($_SESSION['system']['message']); // reset
		}
		$this->index();
	}
	
	/**
	 * Saves new category (if new entry) and saves specie
	 * @return integer
	 */
	private function insertProcedure()
	{
		return $this->registry['db']->addProcedure($_POST["frmProcedure"]);
	}
	
	/**
	 * Ajax called this way:
	 *	/procgrid/delete/1
	 *	Where: procgrid = controller, delete = this function, 1 = procedureId to delete
	 */
	public function delete()
	{
		$procId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteProcedure($procId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}
}

// eof
