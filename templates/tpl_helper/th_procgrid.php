<?php
/**
 * th_procgrid.php
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
 * @package    proc_grid
 * @subpackage template_helper
 */

$tbody_procgrid		= '';

if (empty($aRows) || !$aRows || is_array($aRows) == false) {
	$tbody_procgrid	= "<tbody><tr class='dShade'><td>" . lang_empty . "</td></tr></tbody>";
} else {
	$strRows	= '';
	foreach ($aRows as $row) {
		if ($row['step'] && $row['isComplete'] == 0) {
			// This means that this procedure is not complete.
			$style		= " style='color: #999999;' title='" . lang_proc_is_not_complete . "' ";
			$procEditPage	= '/procedures/inc';
		} else {
			$style		= '';
			$procEditPage	= '/procedures/edit/' . $row["procedureId"];
		}
		
		if ($row["subscriberId"] == CM && $_SESSION['subscriberId'] != CM) {
			$opt	= "
			<td>&nbsp;</td>
			<td><a href='#' onclick='return rowCopy(\"{$row["procedureId"]}\", \"{$row["procName"]}\", {$row["messageId"]});' title='" . lang_copy_cmi_proc . "'><img src='/images/ani.png' alt='" . lang_copy . "' border='0' /></a></td>";
		} else {
			$opt	= "
			<td><a href='#' onclick='return rowDelete(\"{$row["procedureId"]}\");' title='Delete procedure'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a></td>
			<td><a href='{$procEditPage}' title='Edit procedure'><img src='/images/modiffier.png' alt='Modify' border='0' /></a></td>";
		}
		
		$strRows	.= "
		<tr id='row_{$row["procedureId"]}' class='f0ShadeHover' {$style}>
			<td>" . $row["subscriberName"] . "</td>
			<td>" . $row["procName"] . "</td>
			<td>" . $row["procSteps"] . "</td>
			<td>" . $row["isPractice"] . "</td>
			<td>" . $row["isActive"] . "</td>
			{$opt}
		</tr>";
	}
	$tbody_procgrid	.= '<tbody>' . $strRows . '</tbody>';
}

// eof
