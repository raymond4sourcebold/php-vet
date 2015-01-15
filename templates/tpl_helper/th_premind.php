<?php
/**
 * th_premind.php
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
 * @package    proc_remind
 * @subpackage template_helper
 */

$js	= "\nvar aCatMsg = new Array();";

foreach ($aCategory as $categoryId => $category) {
	$js	.= "\n\taCatMsg[{$categoryId}] = new Array();";
	foreach ($aCatMsgId[$categoryId] as $messageId) {
		$js	.= "\n\t\taCatMsg[{$categoryId}][{$messageId}] = '" . addslashes($aMsgTitle[$messageId]) . "';";
	}
}

$js	.= "\nvar aMessages = new Array();";

foreach ($aMsgBody as $msgId => $msgBody) {
	$js	.= "\n\taMessages[{$msgId}]	= '" . addslashes(str_replace("\r\n", chr(27), $msgBody)) . "';";
}

$select_category	= $this->arrayToChoice('selCategory', $aCategory, 'select', lang_dropdown_select_category, (isset($selCategory) ? $selCategory : NULL));

if (isset($selCategory)) {
	$aMsgs		= $aCatMsgTitle[$selCategory];
	$strBody	= $aMsgBody[$selMessage];
} else {
	$aMsgs		= array();
	$strBody	= '';
}

$select_message		= $this->arrayToChoice('selMessage', $aMsgs, 'select', lang_dropdown_select_message, (isset($selMessage) ? $selMessage : NULL));

$textarea_message	= $strBody;


$nReminder	= array(
	0	=> 0,
	1	=> 1,
	2	=> 2
);

$radio_reminder_count		= $this->arrayToChoice('radReminder', $nReminder, 'radio', NULL, (isset($radReminder) ? $radReminder : NULL));

$aDays		= array(
	'1:d'	=> '1 ' . lang_day,
	'2:d'	=> '2 ' . lang_days,
	'3:d'	=> '3 ' . lang_days,
	'4:d'	=> '4 ' . lang_days,
	'5:d'	=> '5 ' . lang_days,
	'6:d'	=> '6 ' . lang_days,
	'1:w'	=> '1 ' . lang_week,
	'2:w'	=> '2 ' . lang_weeks,
	'3:w'	=> '3 ' . lang_weeks,
	'1:m'	=> '1 ' . lang_month,
	'2:m'	=> '2 ' . lang_months,
	'3:m'	=> '3 ' . lang_months,
);

$aDateCri	= array();
foreach ($aDateCriteria as $value) {
	$aDateCri[$value["criteriaCaptionId"]]		= ($value["localCaption"]) ? $value["localCaption"] : $value["criteriaCode"];
}

$select_event_date		= $this->arrayToChoice('eventDate', $aDateCri, 'select', lang_dropdown_target_evtdate, (isset($eventDate) ? $eventDate : NULL));
$select_remind_after_ndays1	= $this->arrayToChoice('reminderAfterNdays1', $aDays, 'select', lang_dropdown_remind_duration_1, (isset($reminderAfterNdays1) ? $reminderAfterNdays1 : NULL));
$select_remind_after_ndays2	= $this->arrayToChoice('reminderAfterNdays2', $aDays, 'select', lang_dropdown_remind_duration_2, (isset($reminderAfterNdays2) ? $reminderAfterNdays2 : NULL));


$aJsReplace	= array(
	'aCatMsg' 	=> $js,
	'nReminder'	=> "\nvar nReminder = " . $radReminder . ";\n"
);

// eof
