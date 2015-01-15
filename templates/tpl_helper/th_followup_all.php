<?php
/**
 * th_followup_all.php
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
 * @package    followup_all
 * @subpackage template_helper
 */

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
				<td>Next in line</td>";
				
			} elseif ($aRow['sendDateFmt'] == '') {
				// It's now on queue
				$tds	= "
				<td><i>{$sendDateToday}</i></td>
				<td><i>" . $aRow['procName'] . "</i></td>
				<td><i>" . $aRow['channelName'] . "</i></td>
				<td><i>Processing...</i></td>";
				
			} else {
				$tds	= "
				<td>" . $aRow['sendDateFmt'] . "</td>
				<td>" . $aRow['procName'] . "</td>
				<td>" . $aRow['channelName'] . "</td>
				<td>Done</td>";
			}
			
			$grid_tr_rows	.= "
			<tr id='row_{$aRow['followUpId']}' class='f0ShadeHover'>
				{$tds}
				<td><img src='/images/supprimerDesa.png' alt='Delete' border='0' /></td>
				<td><img src='/images/modiffierDesa.png' alt='Modify' border='0' /></td>
			</tr>";
		} else {
			if ($aRow['nthReminder']) {
				$strReminder	= ' (' . lang_reminder . ')';
			} else {
				$strReminder	= '';
			}
			
			//~ if ($animalId == $aRow['animalId']) {
				//~ // This row belongs to the Animal currently selected.  Let's make this editable.
				$delIcon	= "<a href='#' onclick='return rowDelete({$aRow['followUpId']}, {$aRow['customMsgId']});'><img src='/images/supprimer.png' alt='Delete' border='0' /></a>";
				$editIcon	= "<a href='#' onclick='return rowEdit({$aRow['followUpId']});'><img src='/images/modiffier.png' alt='Modify' border='0' /></a>";
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

// eof
