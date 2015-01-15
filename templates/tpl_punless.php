<?php
/**
 * Proc Unless Criteria B template file.
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
	4) UNLESS Criteria B
	
	This is the fourth page of the procedure sequence.
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
			<?php echo lang_proc_unless_instructions; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<div class='capEnt' id='critContainer'>
					<div id='divCloneUnlessCrit'>
<?php echo $div_unless_criteria; ?>
					</div>
				</div>
				
				<div style='clear: both; height: 10px;'>&nbsp;</div>
				<div class='floatLeft' id='divAddCriterion'>
					<input type='button' id='addCriterionBtn' value='+' title='<?php echo lang_add_a_criterion; ?>' /> 
					<span class='hoverhand' id='addCriterionSpan'><?php echo lang_add_a_criterion; ?></span>
				</div>
				
				<div class='floatRight'>
					<input type='button' onclick='location.href="/punless";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/3.1)proctypes";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_send_date; ?>' />
					<input type='submit' id='btnProceed' name='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_reminders; ?>' />
				</div>
				<div class='clearBoth'>&nbsp;</div>
			</form>
		</div>
		
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
