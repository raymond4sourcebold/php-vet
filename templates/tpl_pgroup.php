<?php
/**
 * Group Proc template file.
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
	3) Send Date: Group Procedure
	
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
			<?php echo lang_you_successfully_chose_proctype; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<input type='hidden' name='updateRowId' />
				<input type='hidden' name='proc_page' value='message' />
				<div class='capEnt' style='line-height: 24px;'>
					<div class='floatLeft'><?php echo lang_send_date; ?>&nbsp;</div>
					<div class='floatLeft'><input class='date-pick' type='text' size='10' maxlength='10' id='frmSendDate' name='frmSendDate' value='<?php echo $frmSendDate; ?>' /></div>
				</div>
				<div class='capEnt' style='clear: both; padding-top: 7px;'><?php echo lang_is_proc_recurring; ?> 
<?php echo $radRecurringYesno; ?>				
				</div>
				
				<div id='divSelect' class='capEnt' style='float: left; margin-left: 140px; background-color: #EEE; padding: 7px 15px;'><?php echo lang_recurring_period; ?>&nbsp;
<?php echo $selRecurPeriod; ?>
				</div>
				<div class='clearBoth'>&nbsp;</div>
				<div style='float: right;'>
					<input type='button' onclick='location.href="/pgroup";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/3)seltype";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_message; ?>' />
					<input type='submit' id='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_send_date; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		<div style="margin-top: 25px;">
			<?php echo lang_important_senddate_validity; ?>
		</div>
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
