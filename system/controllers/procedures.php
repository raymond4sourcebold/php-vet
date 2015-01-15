<?php
/**
 * Controller file for Proc Entry and calls the appropriate step.
 * This controller checks presence of unfinished Proc Sequence and brings the user back to continue his work.
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
 * Controller for Proc Entry and calls the appropriate step.
 * @package    proc_controller
 */
Class Controller_Procedures Extends Controller_Base
{
	/**
	 * Procedure Step
	 */
	var $procStep;
	
	/**
	 * Default function to execute for this controller.
	 */
	public function index($step=NULL)
	{
		$fcn		= NULL;
		
		if (isset($_SESSION['edit_procId'])) {
			$aProc		= $this->registry['db']->getProcedure($_SESSION['edit_procId']);
			
			if (!isset($_SESSION['procAllColumnsConcat'])) {
				$_SESSION['procAllColumnsConcat']	= $this->concatProcAllColumns($aProc);
			}
		} else {
			$aProc		= $this->registry['db']->getProcedure();
		}
		
		if ($aProc == false) {
			unset($_SESSION['tempproc']);
			$this->procStep		= '/pmessage';
			
		} else {
			if ($step) {
				$aProc['step']	= $step;
				$fcn			= 'back';
			} else {
				$fcn			= 'restoresession';
			}
			
			// Save it to session
			$_SESSION['tempproc']		= $aProc;
			
			// For the autohide message
			$_SESSION['system']['message']	= $aProc['step'];
			
			$this->assignStep($aProc['step']);
		}
		
		$this->gotoProcStep($fcn);
	}
	
	/**
	 * Determines the Proc Step
	 */
	private function assignStep($step)
	{
		if ($step == '1)message') {
			$this->procStep		= '/pmessage';
			
		} elseif ($step == '2)ifcrita') {
			$this->procStep		= '/pifcrita';
			
		} elseif ($step == '3)seltype') {
			$this->procStep		= '/pseltype';
			
		} elseif ($step == '3)onestep') {
			$this->procStep		= '/ponestep';
			
		} elseif ($step == '3)twostep') {
			$this->procStep		= '/ptwostep';
			
		} elseif ($step == '3)group') {
			$this->procStep		= '/pgroup';
		
		} elseif ($step == '3.1)proctypes') {
			if ($_SESSION['tempproc']['procSteps'] == 'one') {
				$this->procStep			= '/ponestep';
				$_SESSION['tempproc']['step']	= '3)onestep';
				
			} elseif ($_SESSION['tempproc']['procSteps'] == 'two') {
				$this->procStep			= '/ptwostep';
				$_SESSION['tempproc']['step']	= '3)twostep';
				
			} elseif ($_SESSION['tempproc']['procSteps'] == 'group') {
				$this->procStep			= '/pgroup';
				$_SESSION['tempproc']['step']	= '3)group';
			}
			
		} elseif ($step == '4)unless') {
			$this->procStep		= '/punless';
			
		} elseif ($step == '5)remind') {
			$this->procStep		= '/premind';
			
		} elseif ($step == '6)punch') {
			$this->procStep		= '/ppunch';
			
		} elseif ($step == '7)complete') {
			$this->procStep		= '/pcomplete';
			
		} else {
			if ($step) {
				$this->procStep		= '/error/abort/Unknown Proc Step: ' . $step;
			} else {
				$this->procStep		= '/error/abort/Unknown Proc Step: empty, please supply. Check `tempprocedure.step` enum definition.';
			}
			
			unset($_SESSION['tempproc']);
			unset($_SESSION['system']['message']);
		}
	}
	
	/**
	 * Transfers control to the last step the user was working on.
	 * @param string $fcn
	 */
	private function gotoProcStep($fcn=NULL)
	{
		if ($fcn) {
			$fcn	= '/' . $fcn;
		}
		
		header('location: ' . $this->procStep . $fcn);
		exit;
	}
	
	/**
	 * Entry point for reviewing a previous page
	 */
	public function back()
	{
		$step	= $this->registry['router']->getArg(ARGUMENT_1);
		$this->index($step);
	}
	
	/**
	 * Creation of a new procedure starts here.
	 */
	public function create()
	{
		if (isset($_SESSION['edit_procId'])) {
			unset($_SESSION['edit_procId']);
		}
		
		$this->index();
	}
	
	/**
	 * Editing of existing procedure starts here.
	 */
	public function edit()
	{
		if (isset($_SESSION['procAllColumnsConcat'])) {
			unset($_SESSION['procAllColumnsConcat']);
		}
		
		$_SESSION['edit_procId']	= $this->registry['router']->getArg(ARGUMENT_1);
		
		$this->registry['db']->setExistingProcForEdit($_SESSION['edit_procId']);
		
		$this->index();
	}
	
	/**
	 * Concatenates all fields of Proc except scannerEvalDate and lastUpdateDate.
	 *	The purpose is to have a string that can be compare to check if anything changed on the Proc.
	 * @param array $aProc Array of one procedure columns
	 * @return string $strConcat
	 */
	private function concatProcAllColumns($aProc)
	{
		$strConcat	= '';
		
		foreach ($aProc as $key => $value) {
			if ($key == 'scannerEvalDate' || $key == 'lastUpdateDate' || $key == 'step') {
				continue;
			}
			$strConcat	.= '&' . $value; // '&' only serves as a divider
		}
		
		return $strConcat;
	}
	
	/**
	 * header redirection from pcomplete
	 */
	public function gotostart()
	{
		$this->registry['db']->setExistingProcForEdit($_SESSION['edit_procId'], '1)message');
		$this->index();
	}
	
	/**
	 * Execution when the subscriber clicks edit button on an unfinished proc on ProcGrid.
	 *	Previous finished proc ID edit is unset if it exists.
	 */
	public function inc()
	{
		if (isset($_SESSION['edit_procId'])) {
			unset($_SESSION['edit_procId']);
		}
		
		$this->index();
	}
}

// eof
