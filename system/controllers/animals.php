<?php
/**
 * Controller file for Animal page.
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
 * Controller for Animal page.
 * @package    animals
 */
Class Controller_Animals Extends Controller_Base
{
	/**
	 * Client ID
	 */
	var $clientId;
	
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				if ($this->updateAnimal($_POST['updateRowId']) === SQL_ERROR) {
					header('location: /animals/error/' . $this->registry['db']->sqlerrno);
					exit;
					
				} else {
					$this->saveBirthAndDeathEvents($_POST['updateRowId']);
					
					$this->registry['tool']->asynchronousCall('cron/animal_scanner.php ' . $_POST['updateRowId']);
					
					$_SESSION['system']['message']		= 'updated';
					header('location: /animals/success');
				}
				
			} else {
				$animalId	= $this->insertAnimal();
				if ($animalId === SQL_ERROR) {
					header('location: /animals/error/' . $this->registry['db']->sqlerrno);
					exit;
					
				} else {
					$this->saveBirthAndDeathEvents($animalId);
					
					$this->registry['tool']->asynchronousCall('cron/animal_scanner.php ' . $animalId);
					
					$_SESSION['system']['message']		= 'saved';
					header('location: /animals/success');
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter', 'jquery/jquery.datePicker', 'jquery/jquery.bgiframe.min', 'jquery/date', 'validator_date'));
		$this->registry['template']->set('css', array('styleG', 'datePicker'));
		
		$this->buildEntryForm();
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Manage Animals that belong to a Client
	 */
	public function manage()
	{
		$paramClient	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($paramClient) {
			$_SESSION['clientId']		= $paramClient;
		}
		
		// Check if Client Id is passed
		if (isset($_SESSION['clientId']) == false) {
			header('location: /error/abort/Client-Animal: Client ID not supplied.');
			exit;
		}
		
		$this->clientId			= $_SESSION['clientId'];
		
		$this->index();
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get animal data
	 */
	public function getJsonAnimal() {
		$aAnimal	= array();
		
		$animalId	= $this->registry['router']->getArg(ARGUMENT_1);
		$aTmp		= $this->registry['db']->getAnimals($animalId, NULL);
		
		if (isset($aTmp[0])) {
			$aAnimal	= $aTmp[0];
		}
		
		
		$aOut	= array();
		
		foreach ($aAnimal as $key => $value) {
			$aOut[$key]	= htmlentities($value);
		}
		echo json_encode($aOut);
		exit;
	}

	/**
	 * Builds entry objects on form
	 */
	private function buildEntryForm()
	{
		$this->registry['template']->set('aSpecies', $this->registry['db']->getSpecies());
		$this->registry['template']->set('aBreeds', $this->registry['db']->getBreeds());
		$this->registry['template']->set('aGenders', $this->registry['db']->getGenders());
	}
	
	/**
	 * Builds Animals display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$this->registry['template']->set('aRows', $this->registry['db']->getAnimals(NULL, (isset($_SESSION['clientId']) ? $_SESSION['clientId'] : NULL)));
	}

	/**
	 * Called to display animal and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_animal_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_animal_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/animals/delete/1
	 *	Where: animals = controller, delete = this function, 1 = animalId to delete
	 */
	public function delete()
	{
		$animalId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteAnimal($animalId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}

	
	/**
	 * Saves new category (if new entry) and saves animal
	 * @return integer $animalId Animal ID
	 */
	private function insertAnimal()
	{
		$animalId	= $this->registry['db']->addAnimal(
			$_SESSION['clientId'],
			$_POST["frmAnimal"],
			$_POST["frmExternalId"],
			$_POST["selSpecie"],
			$_POST["selBreed"],
			$_POST["selGender"]
		);
		
		// Check if addAnimal was successful
		if (is_numeric($animalId)) {
			$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['active'], (isset($_POST["chkActive"]) ? 1 : 0));
			$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['identified'], (isset($_POST["chkIdentified"]) ? 1 : 0));
			$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['vaccinated'], (isset($_POST["chkVaccinated"]) ? 1 : 0));
			$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['insured'], (isset($_POST["chkInsured"]) ? 1 : 0));
		}
		
		return $animalId;
	}
	
	/**
	 * Saves new category (if new entry) and saves animal
	 * @param integer $result Number of rows affected
	 */
	private function updateAnimal($animalId)
	{
		$result	= $this->registry['db']->updateAnimal(
			$animalId, 
			$_SESSION['clientId'],
			$_POST["frmAnimal"],
			$_POST["frmExternalId"],
			$_POST["selSpecie"],
			$_POST["selBreed"],
			$_POST["selGender"]
		);
		
		$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['active'], (isset($_POST["chkActive"]) ? 1 : 0));
		$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['identified'], (isset($_POST["chkIdentified"]) ? 1 : 0));
		$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['vaccinated'], (isset($_POST["chkVaccinated"]) ? 1 : 0));
		$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['insured'], (isset($_POST["chkInsured"]) ? 1 : 0));
		
		return $result;
	}
	
	/**
	 * Saves (insert or update) birth and death events
	 */
	private function saveBirthAndDeathEvents($animalId)
	{
		/**
		 * Save Boolean information
		 */
		//~ $this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['born'], 1);
		$this->registry['db']->saveAnimalBoolean($animalId, $_SESSION['settings']['dead'], ($_POST["frmDeathDate"] == '' ? 0 : 1));
		
		/**
		 * Save Date information
		 */
		$this->registry['db']->saveAnimalDate($animalId, $_SESSION['settings']['birthDate'], $this->formatDateInputToDb($_POST["frmBirthDate"]));
		$this->registry['db']->saveAnimalDate($animalId, $_SESSION['settings']['deathDate'], $this->formatDateInputToDb($_POST["frmDeathDate"]));
	}
}

// eof
