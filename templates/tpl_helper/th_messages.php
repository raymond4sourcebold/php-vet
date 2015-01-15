<?php
/**
 * th_messages.php
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
 * @package    messages
 * @subpackage template_helper
 */

$select_msgcat		= $this->arrayToChoice('selMsgCategory', $aMsgCat, 'select', lang_new_category);

$radio_channel		= $this->arrayToChoice('radioChannel', $aChannel, 'radio');

$buttons_msgvar		= $this->arrayToButtons($aMsgVar, array(1=>'animal', 2=>'owner', 3=>'honorary'));

$aDateCri	= array();
foreach ($aDateCriteria as $value) {
	$aDateCri[$value["criteriaCaptionId"]]	= $value['localCaption'] ? $value['localCaption'] : $value['criteriaCode'];
}
$select_msgdate		= $this->arrayToChoice('selDateId', $aDateCri, 'select', lang_dropdown_select_date);

$checkbox_active	= $this->createCheckBox('chkActive');

if (!$aMessages || is_array($aMessages) == false) {
	$table_tbody_grid	= "<tbody><tr class='dShade'><td colspan='6'>" . lang_empty . "</td></tr></tbody>";
} else {
	$strRows	= '';
	foreach ($aMessages as $row) {
		if (strlen($row['messageBody']) > 40) {
			$bodyText	= substr($row['messageBody'], 0, 40);
			$hoverBodyText	= "&nbsp;&nbsp;<a class='info' onclick='return false' href='#'
				>[.]<span>{$row['messageBody']}</span></a>";
		} else {
			$bodyText	= $row['messageBody'];
			$hoverBodyText	= NULL;
		}
		
		$strRows	.= "
		<tr id='row_{$row['messageId']}' class='f0ShadeHover'>
			<!--<td>" . $row['messageChannel'] . ".</td>-->
			<td>" . $this->registry['db']->getCategoryName($row['messageCategoryId']) . "</td>
			<td>{$row['messageTitle']}</td>
			<td>" . $bodyText . $hoverBodyText . "</td>
			<td><a href='#' onclick='return messagesDelete(\"{$row['messageId']}\");'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a></td>
			<td><a href='#' onclick='return messagesEdit(\"{$row['messageId']}\");'><img src='/images/modiffier.png' alt='" . lang_modify . "' border='0' /></a></td>
		</tr>";
	}
	$table_tbody_grid	= '<tbody>' . $strRows . '</tbody>';
}

$aJsReplace	= array(
	'goCreateNew'		=> "\n" . 'var goCreateNew = ' . (isset($goCreateNew) ? 1 : 0) . ';' ."\n"
);

// eof
