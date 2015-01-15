<?php
/**
 * th_criteria.php
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
 * @package    criteria
 * @subpackage template_helper
 */

$div_input_radio	= $this->arrayToRadio('frmCriterionType', $aCriteriaTypes, NULL, true);


if (empty($aRows) || !$aRows || is_array($aRows) == false) {
	$tr_rows	= "<tr class='dShade'><td colspan='5'>" . lang_empty . "</td></tr>";
} else {
	$tr_rows	= '';
	
	$notSet		= '<i style="color:#FFB2B2;">Not set in <b>' . $_SESSION['subscriberLang'] . '/criteria.ini</b></i>';
	
	foreach ($aRows as $rowId => $criterion) {
		$rowId			= $criterion["criteriaCaptionId"];
		
		$eventsBoolean		= $criterion["criteriaCode"] . '1';
		
		$tr_rows	.= "
		<tr id='row_{$rowId}' class='f0ShadeHover'>
			<td>" . $criterion["criteriaType"] . "</td>
			<td>" . $criterion["criteriaCode"] . "</td>
			<td>" . (isset($$criterion["criteriaCode"]) ? $$criterion["criteriaCode"] : $notSet) . "</td>
			<td>" . (isset($$eventsBoolean) ? $$eventsBoolean : ($criterion["criteriaType"] == 'Event' ? $notSet : '&nbsp;')) . "</td>
			<td><a href='#' onclick='return rowDelete(\"{$rowId}\");'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a></td>
			<td><a href='#' onclick='return rowEdit(\"{$rowId}\", \"{$criterion["criteriaType"]}\", \"{$criterion["criteriaCode"]}\");'><img src='/images/modiffier.png' alt='" . lang_modify . "' border='0' /></a></td>
		</tr>";
	}
}

// eof
