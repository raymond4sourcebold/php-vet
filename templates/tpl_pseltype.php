<?php
/**
 * Proc Select Type template file.
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
	1) Select Type
	
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
			<?php echo lang_proc_seltype_instructions; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<input type='hidden' name='updateRowId' />
				<input type='hidden' name='proc_page' value='message' />
				<div class='capEnt'>
					<div class='floatLeft'><?php echo lang_select_proc_type; ?></div>
<?php echo $radio_proctype; ?>
				</div>
				<div style='float: right;'>
					<input type='button' onclick='location.href="/pseltype";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/2)ifcrita";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_if_crit_a; ?>' />
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
