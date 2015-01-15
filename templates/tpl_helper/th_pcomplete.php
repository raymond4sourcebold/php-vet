<?php
/**
 * th_pcomplete.php
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
 * @package    proc_complete
 * @subpackage template_helper
 */

list($specieCsv, $genderCsv)	= explode(':', $specieIdGenderCsvc);

$aSpecie			= '';
if ($specieCsv) {
	$aSpecie		= explode(',', $specieCsv);
}

$aGender			= '';
if ($genderCsv) {
	$aGender		= explode(',', $genderCsv);
}


$div_specie	= "<div class='clearBoth'><div>" . lang_species . ":</div>";
if ($aSpecie == '') {
	$div_specie	.= "<div style='padding-left: 50px;'>" . lang_all_species . "</div>";
} else {
	foreach ($aSpecie as $key) {
		$div_specie	.= "<div style='padding-left: 50px;'>{$aRefSpecies[$key]}</div>";
	}
}
$div_specie	.= "</div>";


$div_gender	= "<div class='clearBoth'><div>" . lang_genders . ":</div>";
if ($aGender == '') {
	$div_gender	.= "<div style='padding-left: 50px;'>" . lang_all_genders . "</div>";
} else {
	foreach ($aGender as $key) {
		$div_gender	.= "<div style='padding-left: 50px;'>{$aRefGenders[$key]}</div>";
	}
}
$div_gender	.= "</div>";


$strBoolean		= '';
$strQuantity		= '';

// Colon separated boolean criteria from database: $aDbBoolCriteria
if (isset($aDbBoolCriteria)) {
	foreach ($aDbBoolCriteria as $aCri) {
		$aBoolCrit	= explode(':', $aCri);
		
		$nLen		= strlen($aBoolCriteria[$aBoolCrit[0]]) - 1;
		
		$strBoolean	.= "<div><span class='emphasize'>" 
			. (isset($$aBoolCriteria[$aBoolCrit[0]]) 
				? $$aBoolCriteria[$aBoolCrit[0]] 
				: ($aBoolCriteria[$aBoolCrit[0]]{$nLen} == '1'
					? substr($aBoolCriteria[$aBoolCrit[0]], 0, $nLen)
					: $aBoolCriteria[$aBoolCrit[0]]
				  )
			  )
			. "</span> = <span class='emphasize'>" . ($aBoolCrit[1] ? lang_word_yes : lang_word_no) . "</span></div>";
	}
}

// Colon separated boolean criteria from database: $aDbQtyCriteria
if (isset($aDbQtyCriteria)) {
	foreach ($aDbQtyCriteria as $aCri) {
		$aQtyCrit	= explode(':', $aCri);
		
		if ($aQtyCrit[1] == '{') {
			$oper	= '< ' . lang_or . ' =';
		} elseif ($aQtyCrit[1] == '}') {
			$oper	= '> ' . lang_or . ' =';
		} else {
			$oper	= $aQtyCrit[1];
		}
		
		$strQuantity	.= "<div><span class='emphasize'>" 
			. (isset($$aQtyCriteria[$aQtyCrit[0]]) ? $$aQtyCriteria[$aQtyCrit[0]] : $aQtyCriteria[$aQtyCrit[0]])
			. "</span> {$oper} <span class='emphasize'>$aQtyCrit[2]</span></div>";
	}
}


$offset			= csvToDateSpan($offset);
$anticipation		= csvToDateSpan($anticipation);

if ($refDateId) {
	$strSendDate		= lang_on_senddate . " = <span class='emphasize'>"
			. (isset($$referenceSendDate) ? $$referenceSendDate : $referenceSendDate)
			. "</span> + <span class='emphasize'>{$offset}</span> - <span class='emphasize'>{$anticipation}</span>";
} else {
	list($yyyy, $mm, $dd)	= explode('-', $groupSendDate);
	$strSendDate		= "ON SendDate = <span class='emphasize'>{$mm}/{$dd}/{$yyyy}</span>";
}

if ($showGoBackButton) {
	$form_goback_button	= "
		<form method='post'>
			<div style='text-align:center;'><input type='submit' name='btnGoToStart' value='" . lang_goto_start . "' /></div>
		</form>";
} else {
	$form_goback_button	= '';
}


$singleRefSendDate	= '';

// Proc Type
if ($radProcType == 'one') {
	$strProcType	= lang_one_step;
} elseif ($radProcType == 'two') {
	$strProcType	= lang_two_step;
	
	if ($aTmp = $this->registry['db']->getDateCriteria($selSingleRefDateId)) {
		$selSingleRefDateCaption	= $aTmp[0]['criteriaCode'];
		$singleRefSendDate	= "ON SingleRef SendDate = <span class='emphasize'>"
				. (isset($$selSingleRefDateCaption) ? $$selSingleRefDateCaption : $selSingleRefDateCaption)
				. "</span>";
	}

} elseif ($radProcType == 'group') {
	$strProcType	= lang_group;
} else {
	$strProcType	= lang_word_not_set;
}
$strProcType		= lang_type . ": <span class='emphasize'>$strProcType" . ($recurringPeriod ? " &nbsp; <span style='color: green; font-style: italic;'>&rsaquo; " 
	. lang_procedure_recurs_every . " " 
	. str_replace(
		array(':d', ':w', ':m', ':y', '1 '), 
		array(' ' . lang_day, ' ' . lang_week, ' ' . lang_month, ' ' . lang_year, ''), 
		$recurringPeriod
	) 
	. "</span>" : NULL) . "</span>";
// end of execution



/**
 * Template helper function
 * Converts d, w, m, y to day, week, month, year
 *	csv means Colon Separated Value
 */
function csvToDateSpan($str)
{
	$dteCaption	= '';
	
	$aR	= explode(':', $str);
	
	if (isset($aR[1])) {
		$dteChar	= $aR[1];
		
		if ($dteChar == 'd') {
			$dteCaption	= lang_day;
		} elseif ($dteChar == 'w') {
			$dteCaption	= lang_week;
		} elseif ($dteChar == 'm') {
			$dteCaption	= lang_month;
		} elseif ($dteChar == 's') {
			$dteCaption	= lang_semester;
		} elseif ($dteChar == 'y') {
			$dteCaption	= lang_year;
		}
		
		if ($aR[0] > 1) {
			$dteCaption	.= 's';
		}
	}
	
	return "{$aR[0]} $dteCaption";
}

// eof
