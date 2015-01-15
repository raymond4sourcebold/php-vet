<?php
/**
 * Proc Remind template file.
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
	5) Reminders
	
	This is the fifth page of the procedure sequence.
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
			<?php echo lang_remind_instructions; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<div class='capEnt' id='critContainer'>
					<?php echo lang_schedule_no_of_reminder; ?> 
<?php echo $radio_reminder_count; ?>
				</div>
				<div id='divSelect'>
					<div class='capEnt'><?php echo lang_select_msg_for_reminder; ?> 
<?php echo $select_category; ?>
<?php echo $select_message; ?>
					</div>
					<div class='capEnt'>
						<div id='taMsgText'><?php echo $textarea_message; ?></div>
					</div>
					<div class='capEnt'><?php echo lang_select; ?> 
<?php echo $select_event_date; ?> <?php echo lang_select; ?>
<?php echo $select_remind_after_ndays1; ?>
<?php echo $select_remind_after_ndays2; ?>
					</div>
				</div>
				
				<div style='clear: both; height: 10px;'>&nbsp;</div>
				
				<div style='float: right;'>
					<input type='button' onclick='location.href="/premind";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/4)unless";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_unless_crit_b; ?>' />
					<input type='submit' id='btnProceed' name='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_punch_list; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
