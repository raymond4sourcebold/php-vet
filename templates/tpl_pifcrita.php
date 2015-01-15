<?php
/**
 * Proc If Criteria A template file.
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
	1) If Criterion A
	
	This is the second page of the procedure sequence.
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
			<?php echo lang_you_completed_msg_selection; ?>
		</div>
		<hr />
		<div>
			<form method="post" onsubmit='return validateEntry();'>
				<input type='hidden' id='updateRowId' name='updateRowId' />
				<div style='float: left; width: 50%;'><?php echo lang_specie_s; ?>:</div><div style="float: left;  width: 50%"><?php echo lang_gender_s; ?>:</div>
				<div class='capEnt' style='float: left; width: 50%;'>
					<div style='float: left;'><label><?php echo $checkbox_all_specie; ?> <span class='hoverhand'><?php echo lang_all; ?></span></label></div>
					<div style="float: left; padding-left: 20px;">
<?php echo $div_checkbox_specie; ?>
					</div>
				</div>
				<div class='capEnt' style="float: left;  width: 50%">
					<div style='float: left;'><label><?php echo $checkbox_all_gender; ?> <span class='hoverhand'><?php echo lang_all; ?></span></label></div>
					<div style="float: left; padding-left: 20px;">
<?php echo $div_checkbox_gender; ?>
					</div>
				</div>
				
				<div style='clear:both; height:10px;'>&nbsp;</div>
				
				<div class='capEnt' id='critContainer'>
<?php echo $div_boolean_criteria; ?>
<?php echo $div_quantity_criteria; ?>
					<div id='divCloneCritBoolQty'></div>
				</div>
				
				<div style='clear: both; height: 10px;'>&nbsp;</div>
				<div class='capEntFloatLeft' id='divAddCriterion'>
					<input type='button' id='addCriterionBtn' value='+' title='<?php echo lang_add_a_criterion; ?>' /> 
					<span class='hoverhand' id='addCriterionSpan'><?php echo lang_add_a_criterion; ?></span>
				</div>
				<div style='float: right;'>
					<input type='button' onclick='location.href="/pifcrita";' value='<?php echo lang_undo_changes; ?>' title='<?php echo lang_reload_page; ?>' />
					<input type='button' onclick='location.href="/procedures/back/1)message";'  value='&lt;&lt; <?php echo lang_back; ?>' title='<?php echo lang_back_previous_step; ?>: <?php echo lang_message; ?>' />
					<input type='submit' id='btnProceed' value='<?php echo lang_proceed; ?> &gt;&gt;' title='<?php echo lang_proceed_next_step; ?>: <?php echo lang_select_proc_type; ?>' />
				</div>
			</form>
		</div>
		<div style='clear: both;'></div>
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
