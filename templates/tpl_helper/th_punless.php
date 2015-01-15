<?php
/**
 * th_unless.php
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
 * @package    proc_unless_criteria_b
 * @subpackage template_helper
 */

$nCritInstance			= 0;
$div_unless_criteria		= '';

/**
 * Build the options from array.  This HTML is for the unless criterion.  This HTML is copied as many times as the user wants.
 */

// Colon separated boolean criteria from database: $aDbUnless
if ($unlessCriteriaDateIdCsvc) {
	$aDteCriteria	= explode(',', $unlessCriteriaDateIdCsvc);
	
	foreach ($aDteCriteria as $strCriterion) {
		$optionsSelRefDate1	= $tmp1		= '';
		$optionsSelRefDate2	= $tmp2		= '';
		
		$aCondFld	= explode(':', $strCriterion);
		$dbDateId1	= $aCondFld['0'];
		$dbDateId2	= $aCondFld['1'];
		$dbCompare	= $aCondFld['2'];
		$dbDays		= $aCondFld['3'];
		
		foreach ($aDateCriteria as $aRow) {
			$key		= $aRow["criteriaCaptionId"];
			$value		= $aRow['localCaption'] ? $aRow['localCaption'] : $aRow['criteriaCode'];
			
			$tmp1		.= "<option value='{$key}' " . ($dbDateId1 == $key ? 'selected' : NULL) . ">{$value}</option>";
			$tmp2		.= "<option value='{$key}' " . ($dbDateId2 == $key ? 'selected' : NULL) . ">{$value}</option>";
			
			$optionsSelRefDate1	.= "<option value='{$key}'>{$value}</option>";
			$optionsSelRefDate2	.= "<option value='{$key}'>{$value}</option>";
		}
		
		$div_unless_criteria	.= createUnlessCritUsingArray($tmp1, $tmp2, $nCritInstance, $dbCompare, $dbDays);
		
		$nCritInstance++;
	}
} else {
	$optionsSelRefDate1		= '';
	$optionsSelRefDate2		= '';
	
	foreach ($aDateCriteria as $aRow) {
		$key		= $aRow["criteriaCaptionId"];
		$value		= (isset($aRow['localCaption']) && $aRow['localCaption']) ? $aRow['localCaption'] : $aRow['criteriaCode'];
		
		$optionsSelRefDate1	.= "<option value='{$key}'>{$value}</option>";
		$optionsSelRefDate2	.= "<option value='{$key}'>{$value}</option>";
	}
	
	//~ $div_unless_criteria	.= createUnlessCritUsingArray($optionsSelRefDate1, $optionsSelRefDate2, 0);
	//~ $nCritInstance		= 1;
}



$strModelCriteria	= "
	<div id='divUnlessCrit_~0~' style='clear:both;'>
		<div style='float: left;'>
			Criteria
			<select id='selRefId1_~0~' name='selRefId1[~0~]'>
				<option value=''>" . lang_dropdown_refdate_1 . "</option>
	{$optionsSelRefDate1}
			</select> - 
			<select id='selRefId2_~0~' name='selRefId2[~0~]'>
				<option value=''>" . lang_dropdown_refdate_2 . "</option>
	{$optionsSelRefDate2}
			</select>
			<select id='frmOperator_~0~' name='frmOperator[~0~]' title='" . lang_comparison_operator . "' onchange='changeOperator(~0~);'>
				<option value=''>&nbsp;</option>
				<option value='<'>&lt;</option>
				<option value='{'>&lt;=</option>
				<option value='='>=</option>
				<option value='}'>&gt;=</option>
				<option value='>'>&gt;</option>
			</select>
			<label><input class='numericEntry' type='text' id='nYears_~0~' name='nYears[~0~]' maxlength='2' size='1' value='0' />" . lang_years . "</label>
			<label><input class='numericEntry' type='text' id='nMonths_~0~' name='nMonths[~0~]' maxlength='2' size='2' value='0' />" . lang_months . "</label>
			<label><input class='numericEntry' type='text' id='nDays_~0~' name='nDays[~0~]' maxlength='3' size='2' value='0' />" . lang_days . "</label>
		</div>
		<div class='floatRight'><input type='button' onclick='removeCriteria(~0~);' value='X' title='" . lang_remove_this_line . "' /></div>
	</div>
";

$aJsReplace	= array(
	'strModelCriteria' 	=> "\n" . 'var strModelCriteria = "' 
				. str_replace(array("\n", "\t"), '', $strModelCriteria) 
				. '"' . "\n",
	'nCritInstance'		=> "\n" . 'var countCriteria	= ' . $nCritInstance . ';' . "\n"
);


/**
 * Builds an unless date criterion from array (previously saved on database).
 */
function createUnlessCritUsingArray($optionsSelRefDate1, $optionsSelRefDate2, $n, $operator=NULL, $nDays=0)
{
	$op['<']	= '';
	$op['{']	= '';
	$op['=']	= '';
	$op['}']	= '';
	$op['>']	= '';
	
	if ($operator) {
		$op[$operator]	= 'selected';
	}
	
	$computedYrs	= 0;
	$computedMos	= 0;
	$computedDays	= 0;
	
	if ($nDays >= 365) {
		$computedYrs	= floor($nDays / 365);
		$nDays		= $nDays - ($computedYrs * 365);
	}
	if ($nDays >= (365/12)) {
		$computedMos	= floor($nDays / (365/12));
		$nDays		= $nDays - round($computedMos * (365/12));
	}
	$computedDays		= $nDays;
		
	return "<div id='divUnlessCrit_{$n}' style='clear:both;'>
		<div style='float: left;'>
			Criteria<select id='selRefId1_{$n}' name='selRefId1[{$n}]'>
				<option value=''>" . lang_dropdown_refdate_1 . "</option>
	{$optionsSelRefDate1}
			</select> - 
			<select id='selRefId2_{$n}' name='selRefId2[{$n}]'>
				<option value=''>" . lang_dropdown_refdate_2 . "</option>
	{$optionsSelRefDate2}
			</select><select id='frmOperator_{$n}' name='frmOperator[{$n}]' title='" . lang_comparison_operator . "' onchange='changeOperator({$n});'>
				<option value=''>&nbsp;</option>
				<option value='<' {$op['<']}>&lt;</option>
				<option value='{' {$op['{']}>&lt;=</option>
				<option value='=' {$op['=']}>=</option>
				<option value='}' {$op['}']}>&gt;=</option>
				<option value='>' {$op['>']}>&gt;</option>
			</select><label><input class='numericEntry' type='text' id='nYears_{$n}' name='nYears[{$n}]' maxlength='2' size='1' 
			value='{$computedYrs}' />" . lang_years . "</label><label><input class='numericEntry' type='text' id='nMonths_{$n}' name='nMonths[{$n}]' maxlength='2' size='2' 
			value='{$computedMos}' />" . lang_months . "</label><label><input class='numericEntry' type='text' id='nDays_{$n}' name='nDays[{$n}]' maxlength='3' size='2' 
			value='{$computedDays}' />" . lang_days . "</label>
		</div>
		<div class='floatRight'><input type='button' onclick='removeCriteria({$n});' value='X' title='" . lang_comparison_operator . "' /></div>
	</div>
	";
}

// eof
