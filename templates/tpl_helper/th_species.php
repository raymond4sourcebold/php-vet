<?php
/**
 * th_species.php
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
 * @package    species
 * @subpackage template_helper
 */

$div_unless_criteria		= '';

/**
 * Build the options from array.  This HTML is for the unless criterion.  This HTML is copied as many times as the user wants.
 */

$strModelBreed		= "
	<div id='divBreed_~0~' style='clear:both;'>
		<div style='float: left;'>
			<label for='frmBreed_~0~'>years</label><input class='numericEntry' type='text' id='frmBreed_~0~' name='frmBreed[~0~]' maxlength='2' size='1' value='0' />
		</div>
		<div class='floatRight'><input type='button' onclick='removeCriteria(~0~);' value='X' title='" . lang_remove_this_line . "' /></div>
	</div>
";

$aJsReplace	= array(
	'strModelBreed' 	=> "\n" . 'var strModelBreed = "' 
				. str_replace(array("\n", "\t"), '', $strModelBreed) 
				. '"' . "\n"
);


/**
 * Build table tr of Specie grid
 */
$grid_tr_rows	= '';

if (empty($aRows) || !$aRows || is_array($aRows) == false) {
	$grid_tr_rows	= "
			<tr class='dShade'><td>" . lang_empty . "</td></tr>";
} else {
	foreach ($aRows as $rowId => $row) {
		if ($row['raceCsv']) {
			$tmp	= str_replace(',', ', ', $row['raceCsv']);
			
			if (strlen($tmp) > 60) {
				$aRace		= explode(',', $row['raceCsv']);
				$raceBr		= '';
				foreach ($aRace as $race) {
					$raceBr		.= $raceBr ? '<br />' : NULL;
					$raceBr		.= $race;
				}
				$displayRaceCsv	= '<div class="divinfo"><a class="info">' . substr($tmp, 0, 60) . '...<span>' . $raceBr . '</span></a></div>';
			} else {
				$displayRaceCsv	= $tmp;
			}
		} else {
			$displayRaceCsv		= '<span style="font-style: italic; color: #999999;">' . lang_no_breed . '</span>';
		}
		
		$grid_tr_rows	.= "
			<tr id='row_{$rowId}' class='f0ShadeHover'>
				<td>" . $row['specieName'] . "</td>
				<td>" . $displayRaceCsv . "</td>
				<td><a href='#' onclick='return rowDelete(\"{$rowId}\");'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a></td>
				<td><a href='#' onclick='return rowEdit(\"{$rowId}\", \"{$row['specieName']}\", \"{$row['raceCsv']}\");'><img src='/images/modiffier.png' alt='" . lang_modify . "' border='0' /></a></td>
			</tr>";
	}
}

// eof
