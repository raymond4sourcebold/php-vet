<?php
/**
 * Model file of controller: pifcrita
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
 * Database access functions of controller: pifcrita
 * @package    db_class
 */
Class Db Extends Model_Common
{
	/**
	 * Updates procedure
	 */
	public function procedureUpdateIfcrita($rowId)
	{
		$strSpecie		= '';
		if (!isset($_POST["chkAllSpecies"])) {
			$strSpecie	= implode(",", array_keys($_POST['frmSpecie']));
		}
		
		$strGender		= '';
		if (!isset($_POST["chkAllGenders"])) {
			$strGender	= implode(",", array_keys($_POST['frmGender']));
		}
		
		list($critBool, $critQty)	= $this->convertBoolQtyPost2Csvc();
		
		$sql	= "UPDATE `procedure` SET 
			specieIdGenderCsvc = '{$strSpecie}:{$strGender}',
			criteriaBooleanIdCsvc = '{$critBool}',
			criteriaQuantityIdCsvc = '{$critQty}'
			WHERE procedureId = '" . $rowId . "'";
		$this->dbc->exec($sql);
	}
	
	/**
	 * Converts Boolean and Quantity POST into DB Csvc format
	 */
	private function convertBoolQtyPost2Csvc()
	{
		$strBoolean	= '';
		$strQuantity	= '';
		
		if (isset($_POST["criterionType"]) && is_array($_POST["criterionType"])) {
			foreach ($_POST["criterionType"] as $key => $condType) {
				if ($condType == "boolean") {
					$strBoolean	.= $strBoolean ? ',' : NULL;
					// boolean condition
					$strBoolean	.= $_POST["selBoolCrit"][$key] . ':' . $_POST["frmBoolean"][$key];
					
				} else {
					$strQuantity	.= $strQuantity ? ',' : NULL;
					// quantity condition
					$strQuantity	.= $_POST["selQtyCrit"][$key] . ':' . $_POST["frmOperator"][$key] . ':' . $_POST["frmQty"][$key];
				}
			}
		}
		
		return array($strBoolean, $strQuantity);
	}
	
}

// eof
