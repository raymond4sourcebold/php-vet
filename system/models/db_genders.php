<?php
/**
 * Model file of controller: genders
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
 * Database access functions of controller: genders
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Saves a new Gender
	 */
	public function addGender($gender)
	{
		$sql	= "INSERT INTO gender SET 
				genderName = '$gender'";
		$insert	= $this->dbc->prepare($sql);
		$insert->execute();
		return $insert->rowCount();
	}
	
	/**
	 * Saves a new Gender
	 */
	public function updateGender($genderId, $gender)
	{
		$sql	= "UPDATE gender SET 
				genderName = '$gender' WHERE genderId = '$genderId'";
		$insert	= $this->dbc->prepare($sql);
		$insert->execute();
		return $insert->rowCount();
	}

	/**
	 * Deletes one gender
	 * @param integer $genderId
	 * @return integer
	 */
	public function deleteGender($genderId)
	{
		return $this->dbc->exec("DELETE FROM gender WHERE genderId = '$genderId'");
	}
}

// eof
