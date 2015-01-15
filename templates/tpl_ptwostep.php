<?php
/**
 * Proc Two-step template file.
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
	3) Send Date: Two-Step Procedure
	
	This is the third page of the procedure sequence.
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
			<?php echo lang_two_step_instructions; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<input type='hidden' name='updateRowId' />
				<input type='hidden' name='proc_page' value='message' />
				<div class='capEnt'>
					<div class='floatLeft'><?php echo lang_send_date; ?> 
<?php echo $selRefDate; ?>
 + <?php echo lang_offset; ?> <?php echo $selOffset; ?>
 - <?php echo lang_anticipation; ?> <?php echo $selAnticipation; ?>
					</div>
				</div>
				
				<div class='capEnt' style='clear: both;'><?php echo lang_single_refdate; ?> 
<?php echo $selSingleRefDate; ?>
				</div>
				
				<div class='capEnt' style='clear: both; padding-top: 7px;'><?php echo lang_is_proc_recurring; ?> 
<?php echo $radRecurringYesno; ?>				
				</div>
				<div id='divSelect' class='capEnt' style='float: left; margin-left: 140px; background-color: #EEE; padding: 7px 15px;'><?php echo lang_recurring_period; ?>&nbsp;
<?php echo $selRecurPeriod; ?>
				</div>
				
				<div class='clearBoth'>&nbsp;</div>
				<div style='float: right;'>
					<input type='button' onclick='location.href="/ptwostep";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/3)seltype";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_message; ?>' />
					<input type='submit' id='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_send_date; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		<div style="margin-top: 25px;">
			<?php echo lang_two_step_note; ?>
		</div>
		
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
