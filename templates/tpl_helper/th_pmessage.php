<?php
/**
 * th_pmessage.php
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
 * @package    proc_message
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

$aJsReplace	= array(
	'aCatMsg' 	=> $js
);

$frmProcedureName	= isset($frmProcedureName) ? $frmProcedureName : NULL;

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

$select_priority	= $this->arrayToChoice('selPriority', $aPriority, 'select', NULL, (isset($selPriority) ? $selPriority : 3));

$checkbox_consolidate	= (isset($chkConsolidate) && $chkConsolidate) ? 'checked="checked"' : NULL;

$checkbox_sendanimaldead	= (isset($chkSendAnimalDead) && $chkSendAnimalDead) ? 'checked="checked"' : NULL;

// eof
