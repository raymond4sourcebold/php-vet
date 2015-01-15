<?php
/**
 * th_followup.php
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
 * @package    followup
 * @subpackage template_helper
 */

$select_fup_senddate	= $this->arrayToChoice('selFupSendDate', $aFupSendDate, 'select', NULL, '1:y');

$jsChannelArray		= "\n" . 'jsChannelArray = new Array();';
$hold			= '';
$defaultChannel		= '';

$aSelChannel		= array();

foreach ($aChannels as $chId => $aCh) {
	if ($hold != $aCh['channelName']) {
		$hold			= $aCh['channelName'];
		$jsChannelArray		.= "\n\t" . 'jsChannelArray["' . $hold . '"] = new Array();';
		
		// For channel dropdown (Email, SMS, Voice, Snail Mail, Fax)
		$aSelChannel[$hold]	= $hold;
		
		if ($defaultChannel == '') {
			$defaultChannel	= $hold;
		}
	}
	$jsChannelArray		.= "\n\t\t" . 'jsChannelArray["' . $hold . '"][' . $chId . '] = "' . $aCh['channelType'] . '";';
}

$select_channel	= $this->arrayToChoice('selChannel', $aSelChannel, 'select', NULL, 'SMS');

$Yyyy	= date('Y');
$MmDd	= date('m/d/');

$plus1YrMmDdYyyy	= $MmDd . ($Yyyy + 1);



/**
 * Message selection
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

$frmProcedureName	= isset($frmProcedureName) ? $frmProcedureName : NULL;

//~ $select_category	= $this->arrayToChoice('selCategory', $aCategory, 'select', '--Select category--', (isset($selCategory) ? $selCategory : NULL));
$select_category	= $this->arrayToChoice('selCategory', $aCategory, 'select', lang_dropdown_select_category);

if (isset($selCategory)) {
	$aMsgs		= $aCatMsgTitle[$selCategory];
	$strBody	= $aMsgBody[$selMessage];
} else {
	$aMsgs		= array();
	$strBody	= '';
}

//~ $select_message		= $this->arrayToChoice('selMessage', $aMsgs, 'select', '--Select message--', (isset($selMessage) ? $selMessage : NULL));
$select_message		= $this->arrayToChoice('selMessage', $aMsgs, 'select', lang_dropdown_select_message);

$textarea_message	= ''; //$strBody;




/**
 * Build table tr of follow up grid
 */
$grid_tr_rows	= '';

if (empty($aRows) || !$aRows || is_array($aRows) == false) {
	$grid_tr_rows	= "";
} else {
	$sendDateToday	= date('m/d/Y');
	
	foreach ($aRows as $rowId => $aRow) {
		if ($aRow['nDaysB4Send'] < 1) {
		
			if ($aRow['rowType'] == 'F' && $aRow['sendDateFmt'] == $sendDateToday) {
				// On Table: followUp. Waiting to be picked by Date Scanner.
				$tds	= "
				<td>{$sendDateToday}</td>
				<td>" . $aRow['procName'] . "</td>
				<td>" . $aRow['channelName'] . "</td>
				<td>" . lang_next_in_line . "</td>";
				
			} elseif ($aRow['sendDateFmt'] == '') {
				// It's now on queue
				$tds	= "
				<td><i>{$sendDateToday}</i></td>
				<td><i>" . $aRow['procName'] . "</i></td>
				<td><i>" . $aRow['channelName'] . "</i></td>
				<td><i>" . lang_processing . "...</i></td>";
				
			} else {
				$tds	= "
				<td>" . $aRow['sendDateFmt'] . "</td>
				<td>" . $aRow['procName'] . "</td>
				<td>" . $aRow['channelName'] . "</td>
				<td>" . lang_done . "</td>";
			}
			
			$grid_tr_rows	.= "
			<tr id='row_{$aRow['followUpId']}' class='f0ShadeHover'>
				{$tds}
				<td><img src='/images/supprimerDesa.png' alt='" . lang_delete . "' border='0' /></td>
				<td><img src='/images/modiffierDesa.png' alt='" . lang_modify . "' border='0' /></td>
			</tr>";
		} else {
			if ($aRow['nthReminder']) {
				$strReminder	= ' (' . lang_reminder . ')';
			} else {
				$strReminder	= '';
			}
			
			//~ if ($animalId == $aRow['animalId']) {
				// This row belongs to the Animal currently selected.  Let's make this editable.
				$delIcon	= "<a href='#' onclick='return rowDelete({$aRow['followUpId']}, {$aRow['customMsgId']});'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a>";
				$editIcon	= "<a href='#' onclick='return rowEdit({$aRow['followUpId']});'><img src='/images/modiffier.png' alt='" . lang_modify . "' border='0' /></a>";
			//~ } else {
				//~ $delIcon	= "<img src='/images/supprimerDesa.png' alt='Delete' border='0' />";
				//~ $editIcon	= "<img src='/images/modiffierDesa.png' alt='Modify' border='0' />";
			//~ }
			
			$grid_tr_rows	.= "
			<tr id='row_{$aRow['followUpId']}' class='f0ShadeHover'>
				<td>" . $aRow['sendDateFmt'] . "</td>
				<td>" . $aRow['procName'] . $strReminder . "</td>
				<td>" . $aRow['channelName'] . "</td>
				<td>" . lang_pending . "</td>
				<td>{$delIcon}</td>
				<td>{$editIcon}</td>
			</tr>";
		}
	}
}

$aJsReplace	= array(
	'honorary' 		=> "\nvar honorary= '" . $aniOwn['strHonorary'] . "';\n",
	'lastName' 		=> "\nvar lastName= '" . addslashes($aniOwn['lastName']) . "';\n",
	'animalName' 		=> "\nvar animalName= '" . addslashes($aniOwn['animalName']) . "';\n",
	'jsChannelArray' 	=> $jsChannelArray,
	'nowMmDdYyyy'		=> $MmDd . $Yyyy,
	'nowYyyy'		=> $Yyyy,
	'aCatMsg' 		=> $js,
	'defaultChannel'	=> "\nvar defaultChannel= '" . $defaultChannel . "';\n",
);

// eof
