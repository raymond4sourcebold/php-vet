<?php
/**
 * Model file of controller: species
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
 * Database access functions of controller: species
 * @package    db_class
 */
Class Db Extends Model_Base
{
	/**
	 * Saves a new Specie
	 * @return integer, string
	 */
	public function addSpecie($specie)
	{
		$sql	= "INSERT INTO specie SET specieName = '$specie'";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Saves specie breed
	 */
	public function addBreed($specieId, $breedName)
	{
		$sql	= "INSERT INTO race SET specieId = '$specieId', raceName = '$breedName'";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Updates an existing breed of specie
	 */
	public function updateBreed($specieId, $oldBreed, $updateBreed)
	{
		$sql	= "UPDATE race SET raceName = '$updateBreed'
			WHERE specieId = '$specieId'
			AND raceName = '$oldBreed'";
		return $this->safeExec($sql);
	}
	
	/**
	 * Deletes a breed of specie
	 */
	public function deleteSpecieBreed($specieId, $breed)
	{
		$sql	= "DELETE FROM race WHERE specieId = '$specieId' AND raceName = '$breed'";
		return $this->safeExec($sql);
	}
	
	/**
	 * Saves a new Specie
	 */
	public function updateSpecie($specieId, $specie) {
		$sql	= "UPDATE specie SET specieName = '$specie' WHERE specieId = '$specieId'";
		return $this->dbc->exec($sql);
	}

	/**
	 * Deletes one specie
	 * @param integer $specieId
	 * @return integer
	 */
	public function deleteSpecie($specieId)
	{
		return $this->dbc->exec("DELETE FROM specie WHERE specieId = '$specieId'");
	}
	
	/**
	 * Gets species and breeds (race)
	 * @return array
	 */
	public function getSpecieBreeds()
	{
		$sql	= "SELECT specie.specieId, specieName, GROUP_CONCAT(raceName) AS raceCsv
			FROM specie
			LEFT JOIN race on race.specieId = specie.specieId
			GROUP BY specie.specieId";
		$stmt	= $this->dbc->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$aReturn	= array();
		
		foreach ($stmt->fetchAll() as $row) {
			$aReturn[$row['specieId']]	= $row;
		}
		
		return $aReturn;
	}
}

// eof
