<?php
/**
 * Species template file.
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
	Species and Breed (Race) page.
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
				<legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_specie; ?></a></legend>
				<div id="boiteaddmsg" style="display: inline">
					<div class='divSpecieEntry'>
						<label for='frmSpecie'><?php echo lang_specie; ?></label><input type="text" id="frmSpecie" name="frmSpecie" maxlength="35" size="25" />
					</div>
					
					<div class='capEnt' style='clear: both; padding-top: 6px'>
						<div class='divSpecieEntry' id='divCloneBreed'>
						</div>
					</div>
					
					<div class='floatLeft' id='divAddBreed' style='clear: both; padding: 6px 0 0 20px;'>
						<input type='button' id='addBreedBtn' value='+' title='<?php echo lang_add_a_breed; ?>' /> 
						<span class='hoverhand' id='addBreedSpan'><?php echo lang_add_a_breed; ?></span>
					</div>
					
					<div style="clear: both; text-align: right; padding-top: 10px;">
						<input id="addRowBtn" type="submit" value="<?php echo lang_add_specie; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_specie; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_species; ?></th><th><?php echo lang_breeds; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
			<tbody>
<?php echo $grid_tr_rows; ?>
			</tbody>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
