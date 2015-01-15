<?php
/**
 * Genders template file.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    html_template
 */
?>

<!--
	Genders page
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
	<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
		<div style="width: 700px; margin: auto;">
			<form id="form1" name="form1" method="post" action="" onsubmit="return entryValidate();">
			<input type="hidden" id="updateRowId" name="updateRowId" />
			<label></label>
			<fieldset>
				<legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_gender; ?></a></legend>
				<div id="boiteaddmsg" style="display: none">
					<label>Gender <input type="text" id="frmGender" name="frmGender" maxlength="35" size="35" /></label>
					<br />
					<div align="right">
						<input id="addRowBtn" name="addmsg" type="submit" value="<?php echo lang_add_gender; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_gender; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_genders; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
<?php
		if (empty($aRows) || !$aRows || is_array($aRows) == false) {
			echo "<tbody><tr class='dShade'><td>" . lang_empty . "</td></tr></tbody>";
		} else {
			$strRows	= '';
			foreach ($aRows as $rowId => $genderName) {
				$strRows	.= "
				<tr id='row_{$rowId}' class='f0ShadeHover'>
					<td>" . $genderName . "</td>
					<td><a href='#' onclick='rowDelete(\"{$rowId}\");'><img src='/images/supprimer.png' alt='" . lang_delete . "' border='0' /></a></td>
					<td><a href='#' onclick='rowEdit(\"{$rowId}\", \"{$genderName}\");'><img src='/images/modiffier.png' alt='" . lang_modify . "' border='0' /></a></td>
				</tr>";
			}
			echo '<tbody>' . $strRows . '</tbody>';
		}
?>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
