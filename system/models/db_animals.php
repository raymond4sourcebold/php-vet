<?php
/**
 * Model file for database access functions of controller: animals
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    model
 */

/**
 * Class for database access functions of controller: animals
 * @package    Db_Class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves a new Animal
	 * @param integer $rowId
	 * @param string $type
	 * @param string $caption
	 */
	public function addAnimal($clientId, $animalName, $extId, $specieId, $breedId, $genderId) //, $nIdentified, $nActive, $nVaccinated)
	{
		$sql	= "INSERT INTO animal SET 
				clientId = '$clientId',
				animalName = '$animalName',
				animalExternalId = '$extId',
				specieId = '$specieId',
				raceId = '$breedId',
				genderId = '$genderId'
				/*birthDate = 'birthDate',
				deathDate = 'deathDate',*/
				";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Saves a new Animal
	 * @param integer $rowId
	 * @param string $type
	 * @param string $caption
	 * @param string $caption2
	 */
	public function updateAnimal($rowId, $clientId, $animalName, $extId, $specieId, $breedId, $genderId) //, $nIdentified, $nActive, $nVaccinated)
	{
		$sql	= "UPDATE animal SET 
				clientId = '$clientId',
				animalName = '$animalName',
				animalExternalId = '$extId',
				specieId = '$specieId',
				raceId = '$breedId',
				genderId = '$genderId'
				/*birthDate = 'birthDate',
				deathDate = 'deathDate',*/
			WHERE animalId = '$rowId'";
		return $this->safeExec($sql);
	}
	
	/**
	 * Deletes one Animal
	 * @param integer $rowId
	 * @return integer
	 */
	public function deleteAnimal($rowId)
	{
		return $this->dbc->exec("DELETE FROM animal WHERE animalId = '$rowId'");
	}
	
	/**
	 * Inserts or updates animal events
	 */
	public function saveAnimalEvent($animalId, $criteriaCaptionId, $dateValue, $boolValue)
	{
		$sql	= "INSERT INTO criteriaEvent (
				animalId,
				criteriaCaptionId,
				criteriaDateValue,
				criteriaIsTrue
			) VALUES (
				{$animalId},
				{$criteriaCaptionId},
				'{$dateValue}',
				{$boolValue}
			)
			
			ON DUPLICATE KEY
			
			UPDATE	criteriaEventId = LAST_INSERT_ID(criteriaEventId),
				animalId = {$animalId},
				criteriaCaptionId = {$criteriaCaptionId},
				criteriaDateValue = '{$dateValue}',
				criteriaIsTrue = {$boolValue}
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Inserts or updates animal dates
	 */
	public function saveAnimalDate($animalId, $criteriaCaptionId, $value, $dateClass='')
	{
		$sql    = "INSERT INTO criteriaDate SET
				animalId = {$animalId},
				criteriaCaptionId = {$criteriaCaptionId},
				criteriaDateValue = '{$value}',
				dateClass = '{$dateClass}'
				
			ON DUPLICATE KEY
			
			UPDATE  criteriaDateId = LAST_INSERT_ID(criteriaDateId),
				animalId = {$animalId},
				criteriaCaptionId = {$criteriaCaptionId},
				criteriaDateValue = '{$value}',
				dateClass = '{$dateClass}'
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Inserts or updates animal booleans
	 */
	public function saveAnimalBoolean($animalId, $criteriaCaptionId, $boolValue)
	{
		$sql	= "INSERT INTO criteriaBoolean (
				animalId,
				criteriaCaptionId,
				criteriaBooleanValue
			) VALUES (
				{$animalId},
				{$criteriaCaptionId},
				{$boolValue}
			)
			
			ON DUPLICATE KEY
			
			UPDATE	criteriaBooleanId = LAST_INSERT_ID(criteriaBooleanId),
				animalId = {$animalId},
				criteriaCaptionId = {$criteriaCaptionId},
				criteriaBooleanValue = '{$boolValue}'
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Gets race (breeds).  Does a select * from Table: race.
	 */
	public function getBreeds()
	{
		$sql	= "SELECT * FROM race ORDER BY specieId, raceName";
		$stmt	= $this->dbc->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$aReturn	= array();
		
		foreach ($stmt->fetchAll() as $row) {
			if (isset($aReturn[$row['specieId']]) == false) {
				$aReturn[$row['specieId']]	= array();
			}
			$aReturn[$row['specieId']][$row['raceId']]	= $row['raceName'];
		}
		
		return $aReturn;
	}
	
	/**
	 * Gets animal information
	 * @param integer $animalId
	 * @param integer $clientId
	 * @return array
	 */
	public function getAnimals($animalId=NULL, $clientId=NULL)
	{
		$aReturn	= array();
		
		$sqlCond		= '';
		if ($clientId) {
			$sqlCond	.= $sqlCond ? ' AND ' : ' WHERE ';
			$sqlCond	.= " animal.clientId = '$clientId' ";
		}
		
		if ($animalId) {
			$sql	= "SELECT animal.*, specie.specieName, race.raceName "
					. ',' . (isset($_SESSION['settings']['birthDate'])  ? " birthEvent.criteriaDateValue        " : " '' ") . ' AS birthDate '
					. ',' . (isset($_SESSION['settings']['deathDate'])  ? " deathEvent.criteriaDateValue        " : " '' ") . ' AS deathDate '
					. ',' . (isset($_SESSION['settings']['active'])     ? " activeBool.criteriaBooleanValue     " : " '' ") . ' AS activeBoolean '
					. ',' . (isset($_SESSION['settings']['identified']) ? " identifiedBool.criteriaBooleanValue " : " '' ") . ' AS identifiedBoolean '
					. ',' . (isset($_SESSION['settings']['vaccinated']) ? " vaccinatedBool.criteriaBooleanValue " : " '' ") . ' AS vaccinatedBoolean '
					. ',' . (isset($_SESSION['settings']['insured'])    ? " insuredBool.criteriaBooleanValue    " : " '' ") . ' AS insuredBoolean '
				. " FROM animal
				LEFT JOIN specie on specie.specieId = animal.specieId
				LEFT JOIN race on race.raceId = animal.raceId "
				. (isset($_SESSION['settings']['birthDate'])  ? " LEFT JOIN criteriaDate    AS birthEvent     ON (birthEvent.criteriaCaptionId     = {$_SESSION['settings']['birthDate']}  AND birthEvent.animalId = {$animalId}) " : NULL)
				. (isset($_SESSION['settings']['deathDate'])  ? " LEFT JOIN criteriaDate    AS deathEvent     ON (deathEvent.criteriaCaptionId     = {$_SESSION['settings']['deathDate']}  AND deathEvent.animalId = {$animalId}) " : NULL)
				. (isset($_SESSION['settings']['active'])     ? " LEFT JOIN criteriaBoolean AS activeBool     ON (activeBool.criteriaCaptionId     = {$_SESSION['settings']['active']}     AND activeBool.animalId = {$animalId}) " : NULL)
				. (isset($_SESSION['settings']['identified']) ? " LEFT JOIN criteriaBoolean AS identifiedBool ON (identifiedBool.criteriaCaptionId = {$_SESSION['settings']['identified']} AND identifiedBool.animalId = {$animalId}) " : NULL)
				. (isset($_SESSION['settings']['vaccinated']) ? " LEFT JOIN criteriaBoolean AS vaccinatedBool ON (vaccinatedBool.criteriaCaptionId = {$_SESSION['settings']['vaccinated']} AND vaccinatedBool.animalId = {$animalId}) " : NULL)
				. (isset($_SESSION['settings']['insured'])    ? " LEFT JOIN criteriaBoolean AS insuredBool    ON (insuredBool.criteriaCaptionId    = {$_SESSION['settings']['insured']}    AND insuredBool.animalId = {$animalId}) " : NULL)
				. " WHERE animal.animalId = {$animalId}
					ORDER BY animalName";
		} else {
			if (isset($_SESSION['settings']['deathDate'])) {
				$sql	= "SELECT animal.*, specie.specieName, race.raceName,
						deathEvent.criteriaDateValue     AS deathDate
					FROM animal
					LEFT JOIN specie on specie.specieId = animal.specieId
					LEFT JOIN race on race.raceId = animal.raceId
					LEFT JOIN criteriaDate AS deathEvent        ON (deathEvent.criteriaCaptionId     = {$_SESSION['settings']['deathDate']} AND deathEvent.animalId = animal.animalId)
					{$sqlCond}
					ORDER BY animalName";
			} else {
				$sql	= "SELECT animal.*, specie.specieName, race.raceName,
						''     AS deathDate
					FROM animal
					LEFT JOIN specie on specie.specieId = animal.specieId
					LEFT JOIN race on race.raceId = animal.raceId
					{$sqlCond}
					ORDER BY animalName";
			}
		}
		
		$stmt	= $this->dbc->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetchAll();
	}
}

// eof
