<?php
/**
 * Proc Message template file.
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
	Procedure Sequence:
	1) Message
	
	This is the first page of the procedure sequence.
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
	<div id="contentsInside">
<?php include 'templates/_inc_procmenu.php'; ?>
		<div style="margin-top: 25px;">
			<?php echo lang_proc_creation_intro; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<input type='hidden' name='updateRowId' />
				<input type='hidden' name='proc_page' value='message' />
				<div class='capEnt'>
					<label for='frmProcedureName'><?php echo lang_procedure_name; ?></label>
					<input type='text' id='frmProcedureName' name='frmProcedureName' maxlength='50' value='<?php echo $frmProcedureName; ?>' />
					<div class='divinfo'><a class='info'>?<span>Box to enter a string</span></a><span id='ajaxMsg'></span></div>
				</div>
				<div class='capEnt'>
					<label for='frmProcedureName'><?php echo lang_send_this_msg; ?></label>
<?php echo $select_category; ?> 
<?php echo $select_message; ?>
					<div class='divinfo'><a class='info'>?<span>Dropdowns for category and for message in category</span></a></div>
				</div>
				<div class='capEnt'>
					<textarea readonly id='taMsgText' name='taMsgText' rows='5' style='width: 100%;'><?php echo $textarea_message; ?></textarea>
				</div>
				<div class='capEnt'>
					<label for='frmProcedureName'><?php echo lang_followup_urgency; ?></label>
<?php echo $select_priority; ?>
					<div class='divinfo'><a class='info'>?<span>5 = <?php echo lang_life_death_issue; ?><br />4 = <?php echo lang_billing_issue; ?><br />3 = <?php echo lang_significant_issue; ?><br />2 = <?php echo lang_appointment_reminder; ?><br />1 = <?php echo lang_prevention; ?><br />0 = <?php echo lang_education; ?></span></a></div>
				</div>
				<div class='capEnt'><?php echo lang_commplan_squelch_note; ?></div>
				<div class='capEnt'>
					<input type='checkbox' id='chkConsolidate' name='chkConsolidate' <?php echo $checkbox_consolidate; ?> />
					<label for='chkConsolidate'><?php echo lang_consolidate; ?></label>
					<div class='divinfo'><a class='info'>?<span>Check if you want to consolidate all such message into one, in the circumstance where the Client has several animals and may receive this message for each animal.</span></a></div>
				</div>
				<div class='capEnt'>
					<input type='checkbox' id='chkSendAnimalDead' name='chkSendAnimalDead' <?php echo $checkbox_sendanimaldead; ?> />
					<label for='chkSendAnimalDead'><?php echo lang_send_if_animal_dead; ?></label>
					<div class='divinfo'><a class='info'>?<span>Send message even if animal is already dead.</span></a></div>
				</div>
				<div class='floatRight'>
					<input type='button' onclick='location.href="/pmessage";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='submit' id='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_send_date; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
