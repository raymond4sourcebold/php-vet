<?php
/**
 * th_ptwostep.php
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
 * @package    proc_two_step
 * @subpackage template_helper
 */

$aDateCri		= array();
foreach ($aDateCriteria as $aRow) {
	$aDateCri[$aRow["criteriaCaptionId"]]	= isset($$aRow['criteriaCode']) ? $$aRow['criteriaCode'] : $aRow['criteriaCode'];
}
$selRefDate		= $this->arrayToChoice('selReferenceDateId', $aDateCri, 'select', lang_dropdown_multi_refdate, ($selReferenceDateId ? $selReferenceDateId : NULL));

//~ $div_select_refdate	= "<div style='display: inline;'>$selRefDate</div>";

$selSingleRefDate	= $this->arrayToChoice('selSingleRefDateId', $aDateCri, 'select', lang_dropdown_single_refdate, ($selSingleRefDateId ? $selSingleRefDateId : NULL));


$aOffsetCri		= array();
foreach ($aOffset as $key => $value) {
	$aOffsetCri[$key]	= $value;
}
$selOffset		= $this->arrayToChoice('selOffset', $aOffsetCri, 'select', NULL, ($selOffset ? $selOffset : '1:y'));

//~ $div_select_offset	= "<div style='display: inline;'>$selOffset</div>";


$aAnticipationCri	= array();
foreach ($aAnticipation as $key => $value) {
	$aAnticipationCri[$key]	= $value;
}
$selAnticipation	= $this->arrayToChoice('selAnticipation', $aAnticipationCri, 'select', NULL, ($selAnticipation ? $selAnticipation : '1:m'));

//~ $div_select_anticipation	= "<div style='display: inline;'>$selAnticipation</div>";


$yesNo			= array(
	1 => lang_word_yes, 
	0 => lang_word_no
);
$radRecurringYesno	= $this->arrayToChoice('radRecurring', $yesNo, 'radio', NULL, ($radRecurring ? $radRecurring : 0));

$aRecurCri		= array();
foreach ($aRecur as $key => $value) {
	$aRecurCri[$key]	= $value;
}
$selRecurPeriod		= $this->arrayToChoice('selRecur', $aRecurCri, 'select', NULL, ($selRecur ? $selRecur : '1:y'));

$aJsReplace	= array(
	'isRecurring'	=> "\n" . 'var isRecurring = ' . ($selRecur ? 'true' : 'false') . ';' . "\n"
);

// eof
