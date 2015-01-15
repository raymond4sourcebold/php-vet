<?php
/**
 * th_clients.php
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
 * @package    clients
 * @subpackage template_helper
 */

$select_honorary	= $this->arrayToChoice('selHonorary', $aHonorary, 'select', ' ', (isset($selHonorary) ? $selHonorary : NULL));

$yesNo	= array(
	0	=> lang_continue_sending_msg,
	1	=> lang_stop_sending_msg
);

$radio_no_message	= $this->arrayToChoice('radNoMessage', $yesNo, 'radio_br', NULL, (isset($radNoMessage) ? $radNoMessage : 0));
$select_priority	= $this->arrayToChoice('selPriority', $aPriority, 'select', NULL, (isset($selPriority) ? $selPriority : 0));


/**
 * Build the options from array.  This HTML is for the client phone.  This HTML is copied as many times as the user wants.
 */
$nPhoneInstance		= 0;
$div_phone_entry	= '';

$optPhoneType		= '';
foreach ($aPhoneType as $key => $value) {
	$optPhoneType		.= "<option value='{$key}'>{$value}</option>";
}

$optPhonePriority	= '';
foreach ($aPhonePriority as $key => $value) {
	$optPhonePriority	.= "<option value='{$key}'>{$value}</option>";
}

$strModelPhone	= "
	<div class='clsDivPhone' id='divPhone_~0~' style='clear:both;'>
		<div style='float: left;'>
			" . lang_phone_number . "&nbsp;
			<input type='text' class='telephone' id='phoneNumber_~0~' name='phoneNumber[~0~]' />
			&nbsp;Type&nbsp;
			<select class='selPhoneType' id='selPhoneType_~0~' name='selPhoneType[~0~]' onchange='setPhonePriority(this.value, ~0~);'>
				<option value=''> </option>
	{$optPhoneType}
			</select>
			&nbsp;" . lang_priority . "&nbsp;
			<select id='selPhonePriority_~0~' name='selPhonePriority[~0~]' disabled>
				<option value=''></option>
	{$optPhonePriority}
			</select>
			&nbsp;<div class='divinfo'><a class='info'>?<span>" . lang_lower_value_higher_priority . "<div style='padding-top: 10px; font-size: 0.8em;'>" . lang_help_phone_priority . "</div></span></a></div>
		</div>
		<div class='floatRight'><input type='button' onclick='removePhone(~0~);' value='X' title='" . lang_remove_this_line . "' /></div>
	</div>
";

$aJsReplace	= array(
	'HTTP_HOST'		=> $_SERVER['HTTP_HOST'],
	'goCreateNew'		=> "\n" . 'var goCreateNew = ' . (isset($goCreateNew) ? 1 : 0) . ';' ."\n",
	'strModelPhone' 	=> "\n" . 'var strModelPhone = "' 
				. str_replace(array("\n", "\t"), '', $strModelPhone) 
				. '"' . "\n",
	'nPhoneInstance'		=> "\n" . 'var nPhoneInstance	= ' . $nPhoneInstance . ';' . "\n"
);

// Channels
$checkboxes_nixed_channels	= '';
foreach ($aChannels as $key => $aCh) {
	if ($aCh["channelType"]) {
		$aSelChannel[$key]	= $aCh["channelName"] . ' (' . $aCh["channelType"] . ')';
	} else {
		$aSelChannel[$key]	= $aCh["channelName"];
	}
	$chkName = 'chkNix_' . $key;
	$checkboxes_nixed_channels	.= '<div style="float: left; width: 150px; white-space: nowrap;"><label>' . $this->createCheckBox($chkName, (isset($$chkName) ? $$chkName : NULL)) . '&nbsp;' . $aSelChannel[$key] . '</label></div>';
}
$select_preferred_channel	= $this->arrayToChoice('selPreferred', $aSelChannel, 'select', lang_dropdown_select_channel, (isset($selPreferred) ? $selPreferred : 0));
$select_backup_channel		= $this->arrayToChoice('selBackup', $aSelChannel, 'select', lang_dropdown_select_channel, (isset($selBackup) ? $selBackup : 0));

// Use preferred channel exclusively
$checkbox_usepreferredonly	= $this->createCheckBox('chkUsePreferredOnly', (isset($chkUsePreferredOnly) ? $chkUsePreferredOnly : NULL), NULL, true);

// eof
