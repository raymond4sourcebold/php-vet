<?php
/**
 * Animals template file.
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
	Animals page
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
				<legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_animal; ?></a></legend>
				<div id="boiteaddmsg" style="display: inline">
					<div class='divAnimalEntry' style='float: left; width=50%'>
						<label for='frmAnimal'><?php echo lang_animal_name; ?></label><input type="text" id="frmAnimal" name="frmAnimal" maxlength="35" size="25" /><br />
						<label for='frmExternalId'><?php echo lang_external_id; ?></label><input type="text" id="frmExternalId" name="frmExternalId" maxlength="35" size="25" /><br />
						<label for='selGender'><?php echo lang_gender; ?></label>
<?php echo $selGender; ?>
						<br />
						<label for='selSpecie'><?php echo lang_species; ?></label>
<?php echo $selSpecie; ?>
						<br />
						<label for='selBreed'><?php echo lang_breed; ?></label>
<?php echo $selBreed; ?>
					</div>
					<div style='float: right; width:35%'>
						<div class='divAnimalEntryR'>
							<label for='frmBirthDate'><?php echo lang_birth_date; ?></label><input class='date-pick' type="text" id="frmBirthDate" name="frmBirthDate" maxlength="10" size="10" /><br />
							<label for='frmDeathDate'><?php echo lang_death_date; ?></label><input class='date-pick' type="text" id="frmDeathDate" name="frmDeathDate" maxlength="10" size="10" value='n/a' /><br />
							<div style='height: 5px;'>&nbsp;</div>
							<label class='lblChkR'><input type='checkbox' id='chkIdentified' name='chkIdentified' /> &nbsp; <?php echo lang_identified; ?></label><br />
							<label class='lblChkR'><input type='checkbox' id='chkActive' name='chkActive' /> &nbsp; <?php echo lang_active; ?></label><br />
							<label class='lblChkR'><input type='checkbox' id='chkVaccinated' name='chkVaccinated' /> &nbsp; <?php echo lang_vaccinated; ?></label>
							<label class='lblChkR'><input type='checkbox' id='chkInsured' name='chkInsured' /> &nbsp; <?php echo lang_insured; ?></label>
						</div>
					</div>
					
					<div class='divAnimalEntry' style='float: right; width=50%; padding: 26px 75px 0 0;'>
					</div>
					<div style="clear: both; text-align: right; padding-top: 10px;">
						<input id="addRowBtn" type="submit" value="<?php echo lang_add_animal; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_animal; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead>
			<tr class='lightHead'><th><?php echo lang_name; ?></th><th><?php echo lang_specie; ?></th><th><?php echo lang_race; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
<?php echo $tbody_animal_grid; ?>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
