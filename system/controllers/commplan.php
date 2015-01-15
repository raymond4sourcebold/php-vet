<?php
/**
 * Controller file for Communication Plan (CommPlan) page.
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
 * Controller for Communication Plan (CommPlan) page.
 * @package    comm_plan
 */
Class Controller_CommPlan Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($this->saveCommPlanEntries($_POST['selPriority']) === SQL_ERROR) {
				$_SESSION['system']['message']		= 'updated';
				header('location: /commplan/error');
			} else {
				$_SESSION['system']['message']		= 'updated';
				
				/**
				 * Assign updated comm plan values
				 */
				$_SESSION['commplan']['sendThreshold']		= $_POST['selPriority'];
				$_SESSION['commplan']['pcq_limit_priority']	= $_POST['selBelowPriority'] - 1;
				$_SESSION['commplan']['pcq_duration']		= $_POST['selQuotaDuration'];
				$_SESSION['commplan']['pcq_qty']		= $_POST['frmNoOfDays'];
				
				header('location: /commplan/success');
			}
			exit;
		}
		
		$this->registry['template']->set('aPriority', $this->registry['const']->getPriorityArray());
		$this->registry['template']->set('aQuotaDuration', $this->registry['db']->getQuotaDurationArray());
		
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Saves communication plan entries
	 * @return integer
	 */
	private function saveCommPlanEntries()
	{
		$nPriority	= $_POST['selBelowPriority'] - 1;
		
		return $this->registry['db']->saveCommPlan(
			$_POST['selPriority'],
			$nPriority . ':' . $_POST['selQuotaDuration'] . ':' . $_POST['frmNoOfDays']
		);
	}
}

// eof
