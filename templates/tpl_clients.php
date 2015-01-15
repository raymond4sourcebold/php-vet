<?php
/**
 * Clients template file.
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
	Clients page
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
			<fieldset><legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_client; ?></a></legend>
				<div id="boiteaddmsg" style="display: none">
					<div style='float: left; width: 50%;'><fieldset style='height: 140px;'><legend>General Information</legend>
						<div class='capEnt'>
							<label><?php echo lang_honorary; ?> 
<?php echo $select_honorary; ?>
							</label>
						</div>
						<div class='capEnt'>
							<label><?php echo lang_last_name; ?> <input type="text" id="frmLastname" name="frmLastname" maxlength="25" size="25" /></label>
						</div>
						<div class='capEnt'>
							<label><?php echo lang_first_name; ?> <input type="text" id="frmFirstname" name="frmFirstname" maxlength="25" size="25" /></label>
						</div>
						<div class='capEnt'>
							<label><?php echo lang_external_id; ?> <input type="text" id="frmExternalId" name="frmExternalId" maxlength="50" size="25" /></label>
						</div>
					</fieldset></div>
					<div style='display: inline; width: 50%;'><fieldset style='height: 140px;'><legend><?php echo lang_control_flow; ?></legend>
						<div class='capEnt'><?php echo lang_sending_status; ?>
							<div style='padding-left: 20px;'>
<?php echo $radio_no_message; ?>
							</div>
						</div>
						<div class='capEnt' style='padding-top: 20px;'>
							<label><?php echo lang_message_threshold; ?> 
<?php echo $select_priority; ?>
							</label>
							<div class='divinfo'><a class='info'>?<span>5 = <?php echo lang_life_death_issue; ?><br />4 = <?php echo lang_billing_issue; ?><br />3 = <?php echo lang_significant_issue; ?><br />2 = <?php echo lang_appointment_reminder; ?><br />1 = <?php echo lang_prevention; ?><br />0 = <?php echo lang_education; ?></span></a></div>
						</div>
					</fieldset></div>
					<fieldset style='clear: both;'><legend><?php echo lang_contact; ?>: <i><?php echo lang_web; ?></i></legend>
						<div class='capEnt divAddressEntry'>
							<label for="frmEmail"><?php echo lang_primary_email; ?> </label><input type="text" id="frmEmail" name="frmEmail" maxlength="74" size="40" />
							<br /><label for="frmSecondaryEmail"><?php echo lang_secondary_email; ?> </label><input type="text" id="frmSecondaryEmail" name="frmSecondaryEmail" maxlength="74" size="40" />
						</div>
					</fieldset>
					<fieldset style='clear: both;'><legend><i><?php echo lang_phone; ?></i></legend>
						<div id='divClonePhone'>
						</div>
						
						<div class='clearBoth floatLeft' id='divAddPhone'>
							<input type='button' id='addPhoneBtn' value='+' title='<?php echo lang_add_a_phone; ?>' /> 
							<span class='hoverhand' id='addPhoneSpan'><?php echo lang_add_a_phone; ?></span>
						</div>
					</fieldset>
					<fieldset style='clear: both;'><legend><i><?php echo lang_address; ?></i></legend>
						<fieldset style='clear: both;'><legend><i><?php echo lang_home_address; ?></i></legend>
							<div class='capEnt divAddressEntry'>
								<label for='homeAddressLine1'><?php echo lang_address_line_1; ?></label><input type="text" id="homeAddressLine1" name="homeAddressLine1" maxlength="100" size="50" />
								<br /><label for='homeAddressLine2'><?php echo lang_address_line_2; ?></label><input type="text" id="homeAddressLine2" name="homeAddressLine2" maxlength="100" size="50" />
								<br /><label for='homeCity'><?php echo lang_city; ?></label><input type="text" id="homeCity" name="homeCity" maxlength="25" size="20" />
								<div><label for='homePostalCode'><?php echo lang_postal_code; ?></label></div>
								<div style='float: left;'><input type="text" id="homePostalCode" name="homePostalCode" maxlength="10" size="8" /></div>
								<br /><label for='homeProvOrState'><?php echo lang_province_or_state; ?></label><input type="text" id="homeProvOrState" name="homeProvOrState" maxlength="25" size="20" />
								<div class='info' style='float:right;'><input id='btnClearHomeAddr' type='button' value='clr' /><span style='width:180px;'><?php echo lang_clear_home_address; ?></span></div>
							</div>
							
						</fieldset>
						<fieldset style='clear: both;'><legend><i><?php echo lang_office_address; ?></i></legend>
							<div class='capEnt divAddressEntry'>
								<label for='ofisAddressLine1'><?php echo lang_address_line_1; ?></label><input type="text" id="ofisAddressLine1" name="ofisAddressLine1" maxlength="100" size="50" />
								<br /><label for='ofisAddressLine2'><?php echo lang_address_line_2; ?></label><input type="text" id="ofisAddressLine2" name="ofisAddressLine2" maxlength="100" size="50" />
								<br /><label for='ofisCity'><?php echo lang_city; ?></label><input type="text" id="ofisCity" name="ofisCity" maxlength="25" size="20" />
								<div><label for='ofisPostalCode'><?php echo lang_postal_code; ?></label></div>
								<div style='float: left;'><input type="text" id="ofisPostalCode" name="ofisPostalCode" maxlength="10" size="8" /></div>
								<br /><label for='ofisProvOrState'><?php echo lang_province_or_state; ?></label><input type="text" id="ofisProvOrState" name="ofisProvOrState" maxlength="25" size="20" />
								<div class='info' style='float:right;'><input id='btnClearOfficeAddr' type='button' value='clr' /><span style='width:180px;'><?php echo lang_clear_office_address; ?></span></div>
							</div>
						</fieldset>
						<div class='divAddressEntry' style='padding-top: 10px'>
							<label for='country'><?php echo lang_country; ?> </label>
							<input type="text" id="country" name="country" maxlength="25" size="20" />
						</div>
						
					</fieldset>
					<fieldset style='clear: both;'><legend><i><?php echo lang_channel_settings; ?></i></legend>
						<div class='capEnt'>
							<label><?php echo lang_preferred_channel; ?> 
<?php echo $select_preferred_channel; ?>
							</label>
							&nbsp; &nbsp; <label>
<?php echo $checkbox_usepreferredonly; ?>
							<?php echo lang_use_preferred_exclusively; ?>
							</label>
						</div>
						<div class='capEnt' id='divBackupNixChannels' style='display: none;'>
							<div class='capEnt'><label><?php echo lang_backup_channel; ?> 
<?php echo $select_backup_channel; ?>
							</label></div>
							<div class='capEnt'><?php echo lang_nixed_channels; ?>
								<div style='display: block; padding-left: 40px;'>
<?php echo $checkboxes_nixed_channels; ?>
								</div>
							</div>
						</div>
					</fieldset>
					<div style='clear: both; height: 7px;'>&nbsp;</div>

					<div align="right">
						<input id="addRowBtn" name="addmsg" type="submit" value="<?php echo lang_add_client; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_client; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div style='padding: 10px 25px;'>
			<div style='float: left;'>
				<label><input type='text' id='searchText' size='10' /> <?php echo lang_first_letters_client; ?></label>
			</div>
			<div id='nMatchCount'></div>
		</div>
		<div id="tableaustart"></div>
		<table id='tblNotSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_last_name; ?></th><th><?php echo lang_first_name; ?></th><th><?php echo lang_home_address; ?></th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
			<tbody id="idTbody"></tbody>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
