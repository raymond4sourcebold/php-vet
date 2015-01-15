<?php
/**
 * Criteria template file.
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
	Criteria page.
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
				<legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_criteria; ?></a></legend>
				<div id="boiteaddmsg" style="display: none">
					<div class='capEnt'>
						<div style="float: left;">Criterion type</div>
						<div style="float: left; padding-left: 15px;">
<?php echo $div_input_radio; ?>
						</div><div class='clearBoth'></div>
					</div>
					<div class='capEnt'>
						<label id='lblCaption1' for='frmCaption'><?php echo lang_criteria_ini_code; ?> </label>
						<input type="text" id="frmCaption" name="frmCaption" maxlength="25" size="25" />
					</div>
					<div align="right">
						<input id="addRowBtn" name="addmsg" type="submit" value="<?php echo lang_add_criterion; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_criterion; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_type; ?></th><th><?php echo lang_ini_code; ?></th><th><?php echo lang_criteria_caption; ?></th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
			<tbody>
<?php echo $tr_rows; ?>
			</tbody>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
