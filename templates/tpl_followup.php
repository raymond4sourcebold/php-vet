<?php
/**
 * Follow-up template file.
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
	FollowUps and Breed (Race) page.
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
			<input type="hidden" id="isCustomMessage" name="isCustomMessage" />
			<input type="hidden" id="customMessageId" name="customMessageId" />
			<input type="hidden" name="clientId" value="<?php echo $aniOwn['clientId']; ?>" />
			<input type="hidden" name="animalId" value="<?php echo $animalId; ?>" />
			<label></label>
			<fieldset>
				<legend><a id="frmEntryPane" href="#"><?php echo lang_click_new_followup; ?></a></legend>
				<div id="boiteaddmsg" style="display: inline">
					<div class='capEnt'><?php echo lang_msg_is_for; ?> <span class='emphasize'><?php echo $aniOwn['specieName']; ?> <b><?php echo $aniOwn['animalName']; ?></b></span> <?php echo lang_which_belongs_to; ?> <span class='emphasize'><?php echo $aniOwn['strHonorary'] . ' <b>' . $aniOwn['firstName'] . ' ' . $aniOwn['lastName']; ?></b></span></div>
					
					<div class='capEnt' style='float: left;'>
						<label for='frmFollowUp'><?php echo lang_msg_will_be_sent_in; ?> 
<?php echo $select_fup_senddate; ?>
						</label>
					</div>
					<div class='capEnt' style='float: left;'>
						<input class='date-pick' type="text" id="dteSendDate" name="dteSendDate" maxlength="10" size="10" value="<?php echo $plus1YrMmDdYyyy; ?>" />
					</div>
					
					<div class='clearBoth'>
						<div style='float: left;'>
							<label for='frmFollowUp'><?php echo lang_msg_will_be_sent_by; ?> 
<?php echo $select_channel; ?>
							</label>
						</div>
						
						<div id='divChannelReceiver' style='float: left;'><label for='frmFollowUp'>&nbsp;<?php echo lang_to; ?> 
							<select id='selChannelReceiver' name='selChannelReceiver'></select>
						</label></div>
					</div>
					
					<div class='capEnt clearBoth'>
						<label for='frmProcedureName'><?php echo lang_send_this_msg; ?></label>
<?php echo $select_category; ?>
<?php echo $select_message; ?>
					</div>
					<div class='capEnt'>
						<textarea id='taMsgText' name='taMsgText' rows='5' style='width: 100%;'><?php echo $textarea_message; ?></textarea>
					</div>

					<div style="clear: both; text-align: right; padding-top: 10px;">
						<input id="addRowBtn" type="submit" value="<?php echo lang_add_followup; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_followup; ?>">
						<input class="updateButtonFamily" id="updateRowCancelBtn" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>		
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_send_date; ?></th><th><?php echo lang_proc; ?></th><th><?php echo lang_category; ?></th><th><?php echo lang_status; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
			<tbody>
<?php echo $grid_tr_rows; ?>
			</tbody>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
