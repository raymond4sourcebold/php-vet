<?php
/**
 * Class file of functions to help view/form generation.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    template
 */

/**
 * Class of functions to help view/form generation.
 * @package    template_superclass
 */
class Form_Controls
{
	/**
	 * Builds a form selection from an array.  Calls helper functions to do the actual work.
	 * @param string $name
	 * @param array $aInput
	 * @param string $type select, radio
	 * @param string $zeroOption For dropdown selection, this is the option for value zero.
	 * @param string,integer $selected
	 * @return string
	 */
	public function arrayToChoice($name, $aInput, $type, $zeroOption=NULL, $selected=NULL)
	{
		if (is_array($aInput) == false) {
			$aInput		= array(); // enable empty selections
		}
		
		if ($type == 'select') {
			return $this->arrayToSelect($name, $aInput, $zeroOption, $selected);
		} elseif ($type == 'radio') {
			return $this->arrayToRadio($name, $aInput, $selected);
		} elseif ($type == 'radio_br') {
			return $this->arrayToRadio($name, $aInput, $selected, true);
		}
		
		return '';
	}
	
	/**
	 * Builds a from dropdown selection from an array.
	 * @param string $name
	 * @param array $aInput
	 * @param string,integer $selected
	 * @return string $retString
	 */
	public function arrayToSelect($name, $aInput, $zeroOption, $selected=NULL)
	{
		$retString	= '';
		$strCompare	= NULL;
		
		if ($zeroOption) {
			$aInput		= array(0 => $zeroOption) + $aInput;
		}
		
		if (is_null($selected) == false) {
			$strCompare	= $selected;
		} else {
			if (isset($_SESSION['tempproc'])) {
				if (isset($_SESSION['tempproc']['tpl_' . $_SESSION['tempproc']['step']][$name])) {
					$strCompare	= $_SESSION['tempproc']['tpl_' . $_SESSION['tempproc']['step']][$name];
				}
			}
		}
		
		foreach ($aInput as $key => $value) {
			if (is_null($strCompare)) {
				$retString	.= "<option value='$key'>$value</option>";
			} else {
				if ($strCompare == $key) {
					$retString	.= "<option value='$key' selected='selected'>$value</option>";
				} else {
					$retString	.= "<option value='$key'>$value</option>";
				}
			}
		}
		
		return "<select id='$name' name='$name'>$retString</select>";
	}
	
	/**
	 * Builds a from radio selection from an array.
	 * @param string $name
	 * @param array $aInput
	 * @param string,integer $selected
	 * @return string $retString
	 */
	public function arrayToRadio($name, $aInput, $selected, $nextLine=false)
	{
		$retString	= '';
		$strCompare	= NULL;
		
		if ($selected) {
			$strCompare	= $selected;
		} else {
			// This will allow the selection of the numeric value 0.
			if (is_numeric($selected) && $selected == 0) {
				$strCompare	= $selected;
			}
		}
		
		foreach ($aInput as $key => $value) {
			$retString	.= $retString ? ($nextLine ? '<br />' : NULL) : NULL;
			
			if (is_null($strCompare)) {
				$retString		.= "<label><input class='$name' type='radio' name='$name' value='$key' /> &nbsp;$value</label>";
			} else {
				if ($strCompare == $key) {
					$retString	.= "<label><input class='$name' type='radio' name='$name' value='$key' checked /> &nbsp;$value</label>";
				} else {
					$retString	.= "<label><input class='$name' type='radio' name='$name' value='$key' /> &nbsp;$value</label>";
				}
			}
		}
		
		return $retString;
	}
	
	/**
	 * Builds buttons that inserts variables from array
	 */
	public function arrayToButtons($aInput, $aValue)
	{
		$strReturn	= '';
		
		foreach ($aInput as $key => $button) {
			$name		= str_replace(array('[', ']', '/'), '', $button);
			$strReturn	.= "<input class='btnMessageVariable' type='button' name='$name' value='{$aValue[$key]}' />";
		}
		
		return $strReturn;
	}
	
	/**
	 * Creates a checkbox
	 * @param string $name
	 * @param integer $stickyValue
	 * @param string $onclick
	 * @param boolean $defChecked
	 * @return string
	 */
	public function createCheckBox($name, $stickyValue=NULL, $onclick=NULL, $defChecked=false)
	{
		$strOnclick		= '';
		if ($onclick) {
			$aOnclick	= explode(':', $onclick);
			$fcn		= $aOnclick[0];
			$fcnParam	= '';
			if (isset($aOnclick[1])) {
				$fcnParam	= $aOnclick[1];
			}
			$strOnclick	= "onclick=\"return {$fcn}('{$fcnParam}');\"";
		}
		
		$strChecked		= '';
		if (is_null($stickyValue)) {
			if ($defChecked == true) {
				$strChecked	= "checked='checked'";
			}
		} else {
			if ($stickyValue) {
				$strChecked	= "checked='checked'";
			}
		}
		
		return "<input type=\"checkbox\" id=\"$name\" name=\"$name\" {$strChecked} {$strOnclick} />";
	}

	/**
	 * Moves step relevant variables from DB names to template names.
	 * @return boolean
	 */
	protected function sessionToTemplateVars()
	{
		$aReturn	= array();
		
		if (isset($_SESSION['tempproc']) == false) {
			return $aReturn;
		}
		
		if (empty($_SESSION['tempproc']['step']) || CONTROLLER == 'error') {
			return $aReturn;
		}
		
		$step		= $_SESSION['tempproc']['step'];
		
		if ($step == '1)message') {
			$aReturn['frmProcedureName']	= $_SESSION['tempproc']['procName'];
			$aReturn['selCategory']		= $_SESSION['tempproc']['messageCategoryId'];
			$aReturn['selMessage']		= $_SESSION['tempproc']['messageId'];
			$aReturn['selPriority']		= $_SESSION['tempproc']['priority'];
			$aReturn['radioChannel']	= $_SESSION['tempproc']['messageChannel'];
			$aReturn['taMsgText']		= $_SESSION['tempproc']['messageBody'];
			$aReturn['chkConsolidate']	= $_SESSION['tempproc']['consolidate'];
			$aReturn['chkSendAnimalDead']	= $_SESSION['tempproc']['sendOnDeath'];
			
		} elseif ($step == '2)ifcrita') {
			if ($_SESSION['tempproc']['specieIdGenderCsvc']) {
				$aTmp			= explode(':', $_SESSION['tempproc']['specieIdGenderCsvc']);
				if (isset($aTmp[0])) {
					$aSpecie	= $aTmp[0];
				}
				if (isset($aTmp[1])) {
					$aGender	= $aTmp[1];
				}
				
				if ($aSpecie) {
					$aReturn['frmSpecie']	= explode(',', $aSpecie);
				} else {
					$aReturn['frmSpecie']	= '';
				}
				
				if ($aGender) {
					$aReturn['frmGender']	= explode(',', $aGender);
				} else {
					$aReturn['frmGender']	= '';
				}
			}
			
			if ($_SESSION['tempproc']['criteriaBooleanIdCsvc']) {
				$aReturn['aDbBoolCriteria']	= explode(',', $_SESSION['tempproc']['criteriaBooleanIdCsvc']);
			}
			if ($_SESSION['tempproc']['criteriaQuantityIdCsvc']) {
				$aReturn['aDbQtyCriteria']	= explode(',', $_SESSION['tempproc']['criteriaQuantityIdCsvc']);
			}
			
		} elseif ($step == '3)seltype') {
			$aReturn['radProcType']			= $_SESSION['tempproc']['procSteps'];
			
		} elseif ($step == '3)onestep') {
			$aReturn['radRecurring']		= ($_SESSION['tempproc']['recurringPeriod'] ? 1 : 0);
			$aReturn['selReferenceDateId']		= $_SESSION['tempproc']['refDateId'];
			$aReturn['selOffset']			= $_SESSION['tempproc']['offset'];
			$aReturn['selAnticipation']		= $_SESSION['tempproc']['anticipation'];
			$aReturn['selRecur']			= $_SESSION['tempproc']['recurringPeriod'];
			
		} elseif ($step == '3)twostep') {
			$aReturn['radRecurring']		= ($_SESSION['tempproc']['recurringPeriod'] ? 1 : 0);
			$aReturn['selReferenceDateId']		= $_SESSION['tempproc']['refDateId'];
			$aReturn['selOffset']			= $_SESSION['tempproc']['offset'];
			$aReturn['selAnticipation']		= $_SESSION['tempproc']['anticipation'];
			$aReturn['selSingleRefDateId']		= $_SESSION['tempproc']['singleRefDateId'];
			$aReturn['selRecur']			= $_SESSION['tempproc']['recurringPeriod'];
			
		} elseif ($step == '3)group') {
			$aReturn['radRecurring']		= ($_SESSION['tempproc']['recurringPeriod'] ? 1 : 0);
			$aReturn['selRecur']			= $_SESSION['tempproc']['recurringPeriod'];
			
			if ($_SESSION['tempproc']['groupSendDate'] == '0000-00-00') {
				$aReturn['frmSendDate']		= '';
			} else {
				list($yyyy, $mm, $dd)		= explode('-', $_SESSION['tempproc']['groupSendDate']);
				$aReturn['frmSendDate']		= "$mm/$dd/$yyyy";
			}
			
		} elseif ($step == '4)unless') {
			$aReturn['unlessCriteriaDateIdCsvc']	= $_SESSION['tempproc']['unlessCriteriaDateIdCsvc'];
			
		} elseif ($step == '5)remind') {
			$aReturn['radReminder']			= $_SESSION['tempproc']['reminderCount'];
			$aReturn['selCategory']			= $_SESSION['tempproc']['remmsgCategoryId'];
			$aReturn['selMessage']			= $_SESSION['tempproc']['reminderMessageId1'];
			$aReturn['taMsgText']			= $_SESSION['tempproc']['remmsgBody'];
			$aReturn['eventDate']			= $_SESSION['tempproc']['reminderTargetEventDateId'];
			$aReturn['reminderAfterNdays1']		= $_SESSION['tempproc']['reminderAfterNdays1'];
			$aReturn['reminderAfterNdays2']		= $_SESSION['tempproc']['reminderAfterNdays2'];
			
		} elseif ($step == '6)punch') {
			$aReturn['isPracticeProc']		= $_SESSION['tempproc']['isPractice'];
			$aReturn['isActiveProc']		= $_SESSION['tempproc']['isActive'];
			
		} elseif ($step == '7)complete') {
			$aReturn['specieIdGenderCsvc']		= $_SESSION['tempproc']['specieIdGenderCsvc'];
			$aReturn['procName']			= $_SESSION['tempproc']['procName'];
			$aReturn['procSteps']			= $_SESSION['tempproc']['procSteps'];
			$aReturn['messageTitle']		= $_SESSION['tempproc']['messageTitle'];
			$aReturn['categoryName']		= $_SESSION['tempproc']['categoryName'];
			
			if ($_SESSION['tempproc']['priority'] == 5) {
				$aReturn['priority']		= 'Life or death issue';
			} elseif ($_SESSION['tempproc']['priority'] == 4) {
				$aReturn['priority']		= 'Billing issue';
			} elseif ($_SESSION['tempproc']['priority'] == 3) {
				$aReturn['priority']		= 'Significant health issue';
			} elseif ($_SESSION['tempproc']['priority'] == 2) {
				$aReturn['priority']		= 'Appointment reminder';
			} elseif ($_SESSION['tempproc']['priority'] == 1) {
				$aReturn['priority']		= 'Prevention';
			} else {
				$aReturn['priority']		= 'Education';
			}
			
			if ($_SESSION['tempproc']['criteriaBooleanIdCsvc']) {
				$aReturn['aDbBoolCriteria']	= explode(',', $_SESSION['tempproc']['criteriaBooleanIdCsvc']);
			}
			if ($_SESSION['tempproc']['criteriaQuantityIdCsvc']) {
				$aReturn['aDbQtyCriteria']	= explode(',', $_SESSION['tempproc']['criteriaQuantityIdCsvc']);
			}
			
			$aReturn['referenceSendDate']		= $_SESSION['tempproc']['referenceSendDate'];
			$aReturn['offset']			= $_SESSION['tempproc']['offset'];
			$aReturn['anticipation']		= $_SESSION['tempproc']['anticipation'];
			
			$aReturn['isPractice']			= $_SESSION['tempproc']['isPractice'] ? 'Yes' : 'No';
			$aReturn['isActive']			= $_SESSION['tempproc']['isActive'] ? 'Yes' : 'No';
			
			$aReturn['refDateId']			= $_SESSION['tempproc']['refDateId'];
			$aReturn['groupSendDate']		= $_SESSION['tempproc']['groupSendDate'];
			$aReturn['selSingleRefDateId']		= $_SESSION['tempproc']['singleRefDateId'];
			
			$aReturn['radProcType']			= $_SESSION['tempproc']['procSteps'];
			$aReturn['recurringPeriod']		= $_SESSION['tempproc']['recurringPeriod'];
			
		} else {
			header('location: /error/abort/(TEMPLATE.php) Invalid Proc Step on function sessionToTemplateVars(): ' . $step);
			exit;
		}
		
		return $aReturn;
	}
}

// eof
