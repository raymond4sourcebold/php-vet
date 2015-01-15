<?php
/**
 * Model file of controller: followup
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
 * Database access functions of controller: followup
 * @package    db_class
 */
Class Db Extends Model_Base
{	
	/**
	 * Search matching owner name
	 * @param string $term
	 */
	public function searchOwnerFollowUp($term)
	{
		$sql	= "SELECT animal.animalId, client.clientId, animal.animalName, client.lastName, client.firstName,
				IF(client.homeCity!='', client.homeCity, client.officeCity) AS city,
				IF(client.homeCity!='', client.homeAddress1, client.officeAddress1) AS addr1,
				IF(client.homeCity!='', client.homeAddress2, client.officeAddress2) AS addr2
			FROM client 
			LEFT JOIN animal ON animal.clientId = client.clientId
			WHERE subscriberId = {$_SESSION['subscriberId']}
			AND client.lastName LIKE '$term%'
			ORDER BY client.lastName, client.clientId";
		return $this->fetchAll($sql);
	}
	
	/**
	 * Search matching animal name
	 * @param string $term
	 */
	public function searchFollowUp($term)
	{
		$sql	= "SELECT animal.animalId, animal.clientId, animal.animalName, client.lastName, client.firstName,
				IF(client.homeCity!='', client.homeCity, client.officeCity) AS city
			FROM animal 
			LEFT JOIN client ON client.clientId = animal.clientId
			WHERE subscriberId = {$_SESSION['subscriberId']}
			AND animal.animalName LIKE '$term%'
			ORDER BY animal.animalName";
		return $this->fetchAll($sql);
	}
}

// eof
