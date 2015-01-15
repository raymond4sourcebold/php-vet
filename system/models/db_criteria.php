<?php
/**
 * Model file of controller: criteria
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
 * Database access functions of controller: criteria
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Gets criteria types: Boolean, Quantity, Event
	 * @return array
	 */
	public function getCriteriaTypes()
	{
		return array(
			'Event'		=> 'Event', 
			'Quantity'	=> 'Quantity', 
			'Boolean'	=> 'Boolean',
			'Date'		=> 'Date'
		);
	}
	
	/**
	 * Saves a new Criterion
	 * @param integer $rowId
	 * @param string $type
	 * @param string $caption
	 */
	public function updateCriterion($rowId, $type, $caption)
	{
		$sql	= "UPDATE criteriaCaption SET 
				criteriaType = '$type', 
				criteriaCode = '$caption'
			WHERE criteriaCaptionId = '$rowId'";
		
		return $this->safeExec($sql);
	}
	
	/**
	 * Deletes one criterion
	 * @param integer $rowId
	 * @return integer
	 */
	public function deleteCriterion($rowId)
	{
		return $this->dbc->exec("DELETE FROM criteriaCaption WHERE criteriaCaptionId = '$rowId'");
	}

	/**
	 * Gets criteria
	 * @return array
	 */
	public function getCriteria() {
		$sql	= "SELECT criteriaCaptionId, criteriaCode, criteriaType
			FROM criteriaCaption
			ORDER BY criteriaType, criteriaCode";
		return $this->fetchAll($sql);
	}
}

// eof
