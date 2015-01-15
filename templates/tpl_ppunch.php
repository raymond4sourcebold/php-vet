<?php
/**
 * Proc Punch template file.
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
	6) Punch List
	
	This is the sixth page of the procedure sequence.
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
			<?php echo lang_punch_instructions; ?>
		</div>
		<hr />
		<div>
			<form method="post">
				<div class='capEnt'>
					<label><?php echo $checkbox_practice_proc; ?>&nbsp; <?php echo lang_mark_practice_proc; ?></label>
				</div>
				<div class='capEnt'>
					<label><?php echo $checkbox_active_proc; ?>&nbsp; <?php echo lang_activate_proc; ?></label>
				</div>
				<div style='clear: both; height: 10px;'>&nbsp;</div>
				
				<div style='float: right;'>
					<input type='button' onclick='location.href="/ppunch";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/5)remind";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_reminders; ?>' />
					<input type='submit' id='btnProceed' name='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_finish_proc_process; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
