<?php
/**
 * Class file for common DB getters and setters.
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
 * Class for common DB getters and setters.
 * @package    super_class
 */
class Model_Common extends Model_Base
{
	/**
	 * Common Getters - reads from DB
	 */
	
	/**
	 * Gets all Categories and Messages
	 * Called from
	 *	Controller: premind, pmessage, followup
	 * @return array
	 */
	public function getAllCategoriesMessages($includeCm=false)
	{
		if ($_SESSION['subscriberId'] == CM) {
			$cond	= "
				category.subscriberId = " . CM;
		} else {
			//~ $cond	= "
				//~ (category.subscriberId = {$_SESSION['subscriberId']}
					//~ OR 
				//~ category.subscriberId = " . CM . ")
			//~ ";
			$cond	= "
				category.subscriberId = " . $_SESSION['subscriberId'];
		}
		
		$sql	= "SELECT categoryId, categoryName, messageId, messageTitle, messageBody
			FROM message
			INNER JOIN category ON category.categoryId = message.messageCategoryId
			WHERE {$cond} AND message.isPractice = 1 
			ORDER BY categoryName";
		
		return $this->fetchAll($sql);
	}
	
	/**
	 * Gets species
	 * Called from
	 *	Controller: pifcrita, pcomplete, animals
	 * @return array
	 */
	public function getSpecies()
	{
		$aReturn	= array();
		
		$sql	= "SELECT specieId, specieName
			FROM specie
			ORDER BY specieName";
		
		foreach ($this->dbc->query($sql) as $row) {
			$aReturn[$row['specieId']]	= $row['specieName'];
		}
		
		return $aReturn;
	}
	
	/**
	 * Gets genders
	 * Called from
	 *	Controller: pifcrita, pcomplete, genders, animals
	 * @return array
	 */
	public function getGenders()
	{
		$aReturn	= array();
		
		$sql	= "SELECT genderId, genderName
			FROM gender
			ORDER BY genderName";
		
		foreach ($this->dbc->query($sql) as $row) {
			$aReturn[$row['genderId']]	= $row['genderName'];
		}
		
		return $aReturn;
	}
	
	/**
	 * Gets boolean criteria
	 * Called from
	 *	Controller: pifcrita, pcomplete
	 * @return array
	 */
	public function getBooleanCriteria($simplify=false) {
		$sql	= "SELECT criteriaCaption.criteriaCaptionId, 
				criteriaCaption.criteriaCode,
				criteriaLanguage.value AS localCaption
			FROM criteriaCaption
			LEFT JOIN criteriaLanguage ON (
				criteriaLanguage.criteriaCode = criteriaCaption.criteriaCode
			    AND criteriaLanguage.subscriberId = {$_SESSION['subscriberId']}
			    AND criteriaLanguage.language = '{$_SESSION['subscriberLang']}'
			)
			WHERE criteriaType = 'Boolean'
			
			UNION
			
			SELECT criteriaCaption.criteriaCaptionId, 
				CONCAT(criteriaCaption.criteriaCode, '1') AS criteriaCode,
				criteriaLanguage.value AS localCaption
			FROM criteriaCaption
			LEFT JOIN criteriaLanguage ON (
				criteriaLanguage.criteriaCode = criteriaCaption.criteriaCode
			    AND criteriaLanguage.subscriberId = {$_SESSION['subscriberId']}
			    AND criteriaLanguage.language = '{$_SESSION['subscriberLang']}'
			)
			WHERE criteriaType = 'Event'
			";
		
		$aBoolCrit	= $this->fetchAll($sql);
		
		if ($simplify == false) {
			return $aBoolCrit;
		}
		
		$aRet	= array();
		foreach ($aBoolCrit as $aVal) {
			$aRet[$aVal['criteriaCaptionId']]	= ($aVal['localCaption'] ? $aVal['localCaption'] : $aVal['criteriaCode']);
		}
		
		return $aRet;
	}

	/**
	 * Gets quantity criteria
	 * Called from
	 *	Controller: pifcrita, pcomplete
	 * @return array
	 */
	public function getQuantityCriteria($simplify=false) {
		$sql	= "SELECT criteriaCaptionId, criteriaCode
			FROM criteriaCaption
			WHERE criteriaType = 'Quantity'";
		
		$aQtyCrit	= $this->fetchAll($sql);
		
		if ($simplify == false) {
			return $aQtyCrit;
		}
		
		$aRet	= array();
		foreach ($aQtyCrit as $aVal) {
			$aRet[$aVal['criteriaCaptionId']]	= $aVal['criteriaCode'];
		}
		
		return $aRet;
	}
	
	/**
	 * Gets all Date criteria
	 * Called from
	 *	Controller: punless, ponestep, premind, ptwostep
	 * @return array
	 */
	public function getDateCriteria($criteriaCaptionId=NULL)
	{
		$cond	= ($criteriaCaptionId ? " AND criteriaCaptionId = " . $criteriaCaptionId : NULL);

		$sql	= "SELECT criteriaCaption.criteriaCaptionId, criteriaCaption.criteriaCode, criteriaLanguage.value AS localCaption
			FROM criteriaCaption 
			LEFT JOIN criteriaLanguage ON (
				criteriaLanguage.criteriaCode = criteriaCaption.criteriaCode
			    AND criteriaLanguage.subscriberId = {$_SESSION['subscriberId']}
			    AND criteriaLanguage.language = '{$_SESSION['subscriberLang']}'
			)
			WHERE criteriaCaption.criteriaType = 'Event' {$cond}
			
			UNION
			
			SELECT criteriaCaption.criteriaCaptionId, criteriaCaption.criteriaCode, criteriaLanguage.value AS localCaption
			FROM criteriaCaption 
			LEFT JOIN criteriaLanguage ON (
				criteriaLanguage.criteriaCode = criteriaCaption.criteriaCode
			    AND criteriaLanguage.subscriberId = {$_SESSION['subscriberId']}
			    AND criteriaLanguage.language = '{$_SESSION['subscriberLang']}'
			)
			WHERE criteriaCaption.criteriaType = 'Date' {$cond}
			
			UNION
			
			SELECT criteriaCaption.criteriaCaptionId, criteriaCaption.criteriaCode, criteriaLanguage.value AS localCaption
			FROM criteriaCaption 
			LEFT JOIN criteriaLanguage ON (
				criteriaLanguage.criteriaCode = criteriaCaption.criteriaCode
			    AND criteriaLanguage.subscriberId = {$_SESSION['subscriberId']}
			    AND criteriaLanguage.language = '{$_SESSION['subscriberLang']}'
			)
			WHERE criteriaCaption.criteriaType = 'Datetime' {$cond}
			";
		return $this->fetchAll($sql);
	}
	
	
	/**
	 * Common Setters - saves to DB
	 */
	 
	/**
	 * Saves a new Criterion
	 * Called from 
	 *	Model: db_testuploadini
	 *	Controller: criteria
	 * @param integer $rowId
	 * @param string $type
	 * @param string $caption
	 */
	public function addCriterion($type, $caption) {
		$sql	= "INSERT INTO criteriaCaption (
				criteriaType, 
				criteriaCode
			) VALUES (
				'{$type}', 
				'{$caption}'
			)
			ON DUPLICATE KEY
			
			UPDATE	criteriaCaptionId = LAST_INSERT_ID(criteriaCaptionId)
			";
		return $this->safeExecInsert($sql);
	}
	
	/**
	 * Saves procedure send date
	 * Called from
	 *	Controller: ponestep, pgroup, ptwostep
	 * @param integer $procedureId
	 * @param integer $referenceDateId
	 * @param string $offset
	 * @param string $anticipation
	 * @param integer $singleRefDateId
	 * @param string $recurPeriod
	 * @param string $groupSendDate
	 */
	public function saveProcSendDate($procedureId, $referenceDateId, $offset, $anticipation, $singleRefDateId=NULL, $recurPeriod=NULL, $groupSendDate=NULL)
	{
		if ($recurPeriod) {
			$strRecurPeriod		= ",recurringPeriod = '{$recurPeriod}'";
		} else {
			$strRecurPeriod		= ",recurringPeriod = ''";
		}
		
		if ($groupSendDate) {
			// GroupProc
			$sql	= "UPDATE `procedure` SET 
					refDateId = '',
					offset = '',
					anticipation = '',
					singleRefDateId = '0',
					groupSendDate = '{$groupSendDate}'
					{$strRecurPeriod}
				WHERE procedureId = '{$procedureId}'";
		} else {
			// One-Step, Two-Step, Recurring AutoProcs
			if ($singleRefDateId) {
				$strSingleRefDateId	= ",singleRefDateId = '{$singleRefDateId}'";
			} else {
				$strSingleRefDateId	= ",singleRefDateId = '0'";
			}
			
			$sql	= "UPDATE `procedure` SET 
					refDateId = '{$referenceDateId}',
					offset = '{$offset}',
					anticipation = '{$anticipation}'
					{$strSingleRefDateId}
					{$strRecurPeriod}
				WHERE procedureId = '{$procedureId}'";
		}
		
		$this->dbc->exec($sql);
	}
	
	/**
	 * Sets procedure creation step
	 * Called from
	 *	Controller: punless, ppunch, ponestep, premind, pifcrita, pmessage, pseltype, pgroup, ptwostep
	 * @param string $strStep
	 */
	public function setProcStep($strStep)
	{
		$sql	= "UPDATE tempprocedure SET step = '{$strStep}' WHERE subscriberId = '{$_SESSION['subscriberId']}'";
		$this->dbc->exec($sql);
	}
	
	/**
	 * Deletes all criteria values of an Animal
	 * @param integer $animalId
	 */
	public function deleteAllCritValues($animalId)
	{
		$this->safeExec("DELETE FROM criteriaBoolean  WHERE animalId = " . $animalId);
		$this->safeExec("DELETE FROM criteriaDate     WHERE animalId = " . $animalId);
		$this->safeExec("DELETE FROM criteriaEvent    WHERE animalId = " . $animalId);
		$this->safeExec("DELETE FROM criteriaQuantity WHERE animalId = " . $animalId);
	}
}

// eof
