<?php
/**
 * th_pifcrita.php
 * This file is included inside of /system/template.php on function show().
 * Variables here are accessible on the template file with the same names.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    proc_if_criteria_a
 * @subpackage template_helper
 */

$div_checkbox_specie	= '';
$nValidatorSpecieSw	= 0;
$nValidatorGenderSw	= 0;

foreach ($aSpecies as $key => $value) {
	$strChecked		= '';

	// Check against value from procedure.specieIdGenderCsvc (now in $frmSpecie) to see we are going to check this one.
	if (isset($frmSpecie)) {
		if ($frmSpecie) {
			if (in_array($key, $frmSpecie)) {
				$strChecked	= 'checked="checked"';
				$nValidatorSpecieSw++;
			}
		} else {
			// Db value is empty which means all specie.
			$strChecked		= 'checked="checked"';
			$nValidatorSpecieSw++;
		}
	}
	// else {* Not set means that this is a NEW entry *}
	
	$div_checkbox_specie	.= "
		<div class='clearBoth'><label><input class='clsSpecies' type='checkbox' id='frmSpecie_{$key}' name='frmSpecie[{$key}]' {$strChecked} /> <span class='hoverhand'>$value</span></label></div>";
}

$checkbox_all_specie	= '<input type="checkbox" id="chkAllSpecies" name="chkAllSpecies" ' . (isset($frmSpecie) ? ($frmSpecie ? NULL : 'checked="checked"') : NULL) . ' />';


$div_checkbox_gender	= '';

foreach ($aGenders as $key => $value) {
	$strChecked		= '';

	// Check against value from procedure.specieIdGenderCsvc (now in $frmGender) to see we are going to check this one.
	if (isset($frmGender)) {
		if ($frmGender) {
			if (in_array($key, $frmGender)) {
				$strChecked	= 'checked="checked"';
				$nValidatorGenderSw++;
			}
		} else {
			// Db value is empty which means all gender.
			$strChecked		= 'checked="checked"';
			$nValidatorGenderSw++;
		}
	}
	// else {* Not set means that this is a NEW entry *}
	
	$div_checkbox_gender	.= "
		<div class='clearBoth'><label><input class='clsGenders' type='checkbox' id='frmGender_{$key}' name='frmGender[{$key}]' {$strChecked} /> <span class='hoverhand'>$value</span></label></div>";
}

$checkbox_all_gender	= '<input type="checkbox" id="chkAllGenders" name="chkAllGenders" ' . (isset($frmGender) ? ($frmGender ? NULL : 'checked="checked"') : NULL) . ' />';


/**
 * Create criterion entry with selected options (based on database value)
 */
$div_quantity_criteria		= '';
$div_boolean_criteria		= '';
$nCritInstance			= 0;

$allCrit	= array($aBoolCriteria, $aQtyCriteria);

// Colon separated boolean criteria from database: $aDbBoolCriteria
if (isset($aDbBoolCriteria)) {
	foreach ($aDbBoolCriteria as $aCri) {
		$aBoolCrit	= explode(':', $aCri);
		$div_boolean_criteria	.= createCriteriaUsingArray($allCrit, $nCritInstance, 'boolean', $aBoolCrit[0], $aBoolCrit[1]);
		
		$nCritInstance++;
	}
}

// Colon separated boolean criteria from database: $aDbQtyCriteria
if (isset($aDbQtyCriteria)) {
	foreach ($aDbQtyCriteria as $aCri) {
		$aQtyCrit	= explode(':', $aCri);
		$div_quantity_criteria	.= createCriteriaUsingArray($allCrit, $nCritInstance, 'quantity', $aQtyCrit[0], $aQtyCrit[2], $aQtyCrit[1]);
		
		$nCritInstance++;
	}
}


/**
 * This HTML is for the criterion for dates or boolean.  This HTML is copied as many times as the user wants.
 */
$strBoolCriteria	= '';
$strQtyCriteria		= '';

// This loop will assign i18n values if they exist.
foreach ($aBoolCriteria as $value) {
	$nLen		= strlen($value['criteriaCode']) - 1;
	if ($value['criteriaCode']{$nLen} == 1) {
		$critCodeNoSuffix	= substr($value['criteriaCode'], 0, $nLen);
	} else {
		$critCodeNoSuffix	= $value['criteriaCode']; // substr removes the suffix '1'
	}
	
	$strBoolCriteria	.= "<option value='{$value['criteriaCaptionId']}'>"
				. (isset($value['localCaption'])
					? $value['localCaption']
					: $critCodeNoSuffix
				  )
				. "</option>";
}
// This loop will assign i18n values if they exist.
foreach ($aQtyCriteria as $value) {
	//~ $strQtyCriteria		.= "<option value='{$value['criteriaCaptionId']}'>{$value['criteriaCaptionValue']}</option>";
	$strQtyCriteria		.= "<option value='{$value['criteriaCaptionId']}'>"
				. (isset($value['localCaption']) ? $value['localCaption'] : $value['criteriaCode'])
				. "</option>";
}

$strModelCriteria	= "
	<div id='divCritBoolQty_~0~' style='clear:both;'>
		<div style='float: left;'>
		Criterion type 
		<select id='criterionType_~0~' name='criterionType[~0~]' onchange='changeCriterionType(~0~);'>
			<option value=''>" . lang_dropdown_select . "</option>
			<option value='boolean'>" . lang_true_slash_false . "</option>
			<option value='quantity'>" . lang_quantitative . "</option>
		</select>
		</div>
		<div id='divCritBool_~0~' style='float: left; display: none;'> &nbsp;
			&nbsp;Criterion&nbsp;
			<select id='selBoolCrit_~0~' name='selBoolCrit[~0~]'>
				<option value=''>" . lang_dropdown_select . "</option>
	{$strBoolCriteria}
			</select>
			&nbsp;
			<select id='frmBoolean_~0~' name='frmBoolean[~0~]'>
				<option value=''>" . lang_dropdown_select . "</option>
				<option value='1'>" . lang_word_true . "</option>
				<option value='0'>" . lang_word_false . "</option>
			</select>
		</div>
		<div id='divCritQty_~0~' style='float: left; display: none;'> &nbsp;
			&nbsp;Criterion&nbsp;
			<select id='selQtyCrit_~0~' name='selQtyCrit[~0~]'>
				<option value=''>" . lang_dropdown_select . "</option>
	{$strQtyCriteria}
			</select>
			&nbsp;
			<select id='frmOperator_~0~' name='frmOperator[~0~]' title='" . lang_comparison_operator . "' onchange='changeOperator(~0~);'>
				<option value=''>&nbsp;</option>
				<option value='<'>&lt;</option>
				<option value='{'>&lt;=</option>
				<option value='='>=</option>
				<option value='}'>&gt;=</option>
				<option value='>'>&gt;</option>
			</select>
			&nbsp;
			<input class='numericEntry' type='text' id='frmQty_~0~' name='frmQty[~0~]' value='0' maxlength='8' size='8' />
		</div>
		<div class='floatRight'><input type='button' onclick='removeCriteria(~0~);' value='X' title='" . lang_remove_this_line . "' /></div>
	</div>
";

$aJsReplace	= array(
	'strModelCriteria' 	=> "\n" . 'var strModelCriteria = "' 
				. str_replace(array("\n", "\t"), '', $strModelCriteria) 
				. '"' . "\n",
	'nValidatorSpecieSw'	=> "\n" . 'var nValidatorSpecieSw = ' . $nValidatorSpecieSw . ';' . "\n",
	'nValidatorGenderSw'	=> "\n" . 'var nValidatorGenderSw = ' . $nValidatorGenderSw . ';' . "\n",
	'nCritInstance'		=> "\n" . 'var countCriteria	= ' . $nCritInstance . ';' . "\n"
);


/**
 * Returns a criterion with selected options based on values from existing criterion on database.
 * @param array $allCrit
 * @param integer $n
 * @param string $type
 * @param integer $param1
 * @param integer $param2
 * @param string $compareOperator
 * @return string
 */
function createCriteriaUsingArray($allCrit, $n, $type, $param1, $param2, $compareOperator=NULL)
{
	$aBoolCriteria		= $allCrit[0];
	$aQtyCriteria		= $allCrit[1];
	
	$strBoolCriteria	= '';
	$strQtyCriteria		= '';
	
	// For marking criterion type selected on dropdown
	$selectedBool		= '';
	$selectedQty		= '';
	
	$boolFieldId		= '';
	$qtyFieldId		= '';
	
	// For marking operator selected on dropdown
	$lt			= '';
	$lteq			= '';
	$eq			= '';
	$gteq			= '';
	$gt			= '';
	
	$qtyValue		= 0;
	
	// For marking criterion type selected on dropdown
	$selectedBoolTrue	= '';
	$selectedBoolFalse	= '';
	
	$styleShowBool		= 'display: none;';
	$styleShowQty		= 'display: none;';
	
	if ($type == 'boolean') {
		// boolean
		$selectedBool	= 'selected';
		$boolFieldId	= $param1;
		//~ var_dump('BOOL:'.$boolFieldId);
		$boolValue	= $param2;
		
		if ($boolValue) {
			$selectedBoolTrue	= 'selected';
		} else {
			$selectedBoolFalse	= 'selected';
		}
		
		$styleShowBool	= '';
		
	} else {
		// quantity
		$selectedQty	= 'selected';
		$qtyFieldId	= $param1;
		//~ var_dump('QTY:'.$qtyFieldId);
		$qtyValue	= $param2;
		
		if ($compareOperator == '<') {
			$lt	= 'selected';
		} elseif ($compareOperator == '{') {
			$lteq	= 'selected';
		} elseif ($compareOperator == '=') {
			$eq	= 'selected';
		} elseif ($compareOperator == '}') {
			$gteq	= 'selected';
		} elseif ($compareOperator == '>') {
			$gt	= 'selected';
		}
		
		$styleShowQty	= '';
	}

	foreach ($aBoolCriteria as $value) {
		if (isset($value['localCaption'])) {
			$tmpOpt			= $value['localCaption'];
		} else {
			$nLen			= strlen($value['criteriaCode']) - 1;
			if ($value['criteriaCode']{$nLen} == 1) {
				$tmpOpt		= substr($value['criteriaCode'], 0, $nLen);
			} else {
				$tmpOpt		= $value['criteriaCode']; // substr removes the suffix '1'
			}
		}
		
		$strBoolCriteria	.= "<option value='{$value['criteriaCaptionId']}' " . ($boolFieldId == $value['criteriaCaptionId'] ? 'selected' : NULL) . ">"
					. $tmpOpt
					. "</option>";
	}
	foreach ($aQtyCriteria as $value) {
		$strQtyCriteria		.= "<option value='{$value['criteriaCaptionId']}' " . ($qtyFieldId == $value['criteriaCaptionId'] ? 'selected' : NULL) . ">"
					. (isset($value['localCaption']) ? $value['localCaption'] : $value['criteriaCode'])
					. "</option>";
	}

	return "
		<div id='divCritBoolQty_$n' style='clear:both;'>
			<div style='float: left;'>
			Criterion type 
			<select id='criterionType_$n' name='criterionType[$n]' onchange='changeCriterionType($n);'>
				<option value=''>" . lang_dropdown_select . "</option>
				<option value='boolean' {$selectedBool}>" . lang_true_slash_false . "</option>
				<option value='quantity' {$selectedQty}>" . lang_quantitative . "</option>
			</select>
			</div>
			<div id='divCritBool_$n' style='float: left; {$styleShowBool}'> &nbsp;
				Criterion
				<select id='selBoolCrit_$n' name='selBoolCrit[$n]'>
					<option value=''>" . lang_dropdown_select . "</option>
		{$strBoolCriteria}
				</select>
				<select id='frmBoolean_$n' name='frmBoolean[$n]'>
					<option value=''>" . lang_dropdown_select . "</option>
					<option value='1' {$selectedBoolTrue}>" . lang_word_true . "</option>
					<option value='0' {$selectedBoolFalse}>" . lang_word_false . "</option>
				</select>
			</div>
			<div id='divCritQty_$n' style='float: left; {$styleShowQty}'> &nbsp;
				Criterion
				<select id='selQtyCrit_$n' name='selQtyCrit[$n]'>
					<option value=''>" . lang_dropdown_select . "</option>
		{$strQtyCriteria}
				</select>
				
				<select id='frmOperator_$n' name='frmOperator[$n]' title='" . lang_comparison_operator . "' onchange='changeOperator($n);'>
					<option value=''>&nbsp;</option>
					<option value='<' {$lt}>&lt;</option>
					<option value='{' {$lteq}>&lt;=</option>
					<option value='=' {$eq}>=</option>
					<option value='}' {$gteq}>&gt;=</option>
					<option value='>' {$gt}>&gt;</option>
				</select>
				<input class='numericEntry' type='text' id='frmQty_$n' name='frmQty[$n]' value='{$qtyValue}' maxlength='8' size='8' />
			</div>
			<div class='floatRight'><input type='button' onclick='removeCriteria($n);' value='X' title='" . lang_remove_this_line . "' /></div>
		</div>
	";
}

// eof
