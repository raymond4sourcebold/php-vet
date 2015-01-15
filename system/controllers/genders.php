<?php
/**
 * Controller file for Genders page.
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
 * Controller for Genders page.
 * @package    genders
 */
Class Controller_Genders Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				if ($this->updateGender($_POST['updateRowId'])) {
					$_SESSION['system']['message']		= 'updated';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /genders/success');
				}
			} else {
				if ($this->insertGender()) {
					$_SESSION['system']['message']		= 'saved';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /genders/success');
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}

	/**
	 * Builds Genders display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$aGenders		= $this->registry['db']->getGenders();
		$this->registry['template']->set('aRows', $aGenders);
	}

	/**
	 * Called to display gender and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_gender_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_gender_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/genders/delete/1
	 *	Where: genders = controller, delete = this function, 1 = genderId to delete
	 */
	public function delete()
	{
		$genderId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteGender($genderId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}

	/**
	 * Saves new category (if new entry) and saves gender
	 * @return integer
	 */
	private function insertGender()
	{
		return $this->registry['db']->addGender($_POST["frmGender"]);
	}
	
	/**
	 * Saves new category (if new entry) and saves gender
	 * @return integer
	 */
	private function updateGender($genderId)
	{
		return $this->registry['db']->updateGender($genderId, $_POST["frmGender"]);
	}
}

// eof
