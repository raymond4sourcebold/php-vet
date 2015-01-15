<?php
/**
 * Controller file for Species page.
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
 * Controller for Species page.
 * @package    species
 */
Class Controller_Species Extends Controller_Base
{
	var $aSpecieRace	= array();

	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				$this->getExistingBreedValues($this->registry['db']->getSpecieBreeds());
				
				if ($this->updateSpecie($_POST['updateRowId'])) {
					$_SESSION['system']['message']		= 'updated';
					header('location: /species/success');
				}
			} else {
				if ($this->insertSpecie() == false) {
					if ($this->registry['db']->sqlerrno == '23000') {
						$_SESSION['system']['message']	= 'duplicate';
						header('location: /species/error');
					} else {
						header('location: /error/abort/Insert Specie: ' . $this->registry['db']->sqlerror);
						exit;
					}
				} else {
					$_SESSION['system']['message']		= 'saved';
					header('location: /species/success');
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Builds Species display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$this->registry['template']->set('aRows', $this->registry['db']->getSpecieBreeds());
	}
	
	/**
	 * Save array which will be used later on update.
	 */
	private function getExistingBreedValues($aData)
	{
		foreach ($aData as $specieId => $aVal) {
			if ($aVal['raceCsv']) {
				$aBreed				= explode(',', $aVal['raceCsv']);
				
				$this->aSpecieRace[$specieId]	= array();
				
				foreach ($aBreed as $breed) {
					$this->aSpecieRace[$specieId][]		= $breed;
				}
			}
		}
	}
	
	/**
	 * Called to display success message
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_specie_saved_ok));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_specie_saved_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Called to display error message
	 */
	public function error()
	{
		if (isset($_SESSION['system']['message'])) {
			$this->registry['template']->set('autoHideTitle', lang_failure);
			
			if ($_SESSION['system']['message'] == 'duplicate') {
				$this->registry['template']->set('autoHideMessage', addslashes(lang_specie_duplicate_error));
			} else {
				$this->registry['template']->set('autoHideMessage', addslashes(lang_unknown_error));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/species/delete/1
	 *	Where: species = controller, delete = this function, 1 = specieId to delete
	 */
	public function delete()
	{
		$specieId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteSpecie($specieId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}
	
	/**
	 * Saves new category (if new entry) and saves specie
	 * @return integer, boolean
	 */
	private function insertSpecie()
	{
		$result		= $this->registry['db']->addSpecie($_POST["frmSpecie"]);
		
		if ($result == 'SQL_ERROR') {
			return false;
		}
		
		$specieId	= $result;
		
		if (isset($_POST['frmInsertBreed'])) {
			foreach ($_POST['frmInsertBreed'] as $breed) {
				$this->registry['db']->addBreed($specieId, $breed);
			}
		}
		
		return $result;
	}
	
	/**
	 * Saves new category (if new entry) and saves specie
	 * @param integer $specieId
	 */
	private function updateSpecie($specieId)
	{
		$result		= $this->registry['db']->updateSpecie($specieId, $_POST["frmSpecie"]);
		
		/**
		 * Update existing breeds
		 * This part is complicated.  We are going to use the saved array on buildGrid().
		 *	Now let's go thru existing breeds and delete or update them as necessary.
		 */
		if (isset($_POST['frmUpdateBreed'])) {
			/**
			 * Now let us loop thru existing breeds
			 */
			foreach ($this->aSpecieRace[$specieId] as $dbOldBreed) {
				
				$missing	= true;
				
				/**
				 * Loop thru POST old breed to get $key
				 *	and check also if it was deleted.
				 */
				foreach ($_POST['frmOldBreed'] as $key => $postOldBreed) {
					if ($dbOldBreed == $postOldBreed) {
						$missing	= false;
						// Let break now and use the value of $key and $postOldBreed!
						break;
					}
				}
				
				if ($missing) {
					/**
					 * DB value is no longer in POST.  This means it was deleted by user.
					 */
					$this->registry['db']->deleteSpecieBreed($specieId, $dbOldBreed);
				} else {
					if ($postOldBreed != $_POST['frmUpdateBreed'][$key]) {
						/**
						 * Entry field was changed.  Let's update.
						 */
						$this->registry['db']->updateBreed($specieId, $postOldBreed, $_POST['frmUpdateBreed'][$key]);
					}
				}
			}
		}
		
		// Insert new breeds
		if (isset($_POST['frmInsertBreed'])) {
			foreach ($_POST['frmInsertBreed'] as $breed) {
				$this->registry['db']->addBreed($specieId, $breed);
			}
		}
		
		return $result;
	}
}

// eof
