<?php
/**
 * Proc Complete template file.
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
	7) Complete
	
	This is the seventh and last page of the procedure sequence.
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
			<p><?php echo lang_congrats_proc_complete; ?> <span class='emphasize'><?php echo $procName; ?></span></p>
			<p><?php echo lang_this_you_put_in_place; ?>:
			<div style='text-align: center; font-weight: bold;'><span class='emphasize'><?php echo $procName; ?></span>  <span class='emphasize'><?php echo $categoryName; ?></span></div>
			<p><?php echo lang_uc_send; ?> <span class='emphasize'><?php echo $messageTitle; ?></span>
			<br /><?php echo lang_uc_if; ?> <div class='emphasize' style='float: left; width:47%; margin-left: 10px;'>
				<?php echo $div_specie; ?>
				</div>
				<div class='emphasize' style='float: right; width:47%'>
				<?php echo $div_gender; ?>
				</div><div style='clear: both;'>&nbsp;</div>
				<div style='margin-left: 10px;'>
				<?php echo $strBoolean; ?>
				</div><div style='clear: both;'>&nbsp;</div>
				<div style='margin-left: 10px;'>
				<?php echo $strQuantity; ?>
				</div><div style='clear: both;'>&nbsp;</div>
				
			<p>
<?php echo $strProcType; ?>
			</p>
			<p>
<?php echo $strSendDate; ?>
<br /><?php echo $singleRefSendDate; ?>
			</p>
			<p><?php echo lang_urgency; ?>: <span class='emphasize'><?php echo $priority; ?></span>
			<br /><?php echo lang_category; ?>: <span class='emphasize'><?php echo $categoryName; ?></span>
			<br /><?php echo lang_practice_procedure; ?>: <span class='emphasize'><?php echo $isPractice; ?></span>
			<br /><?php echo lang_active_procedure; ?>: <span class='emphasize'><?php echo $isActive; ?></span></p>
		</div>
<?php echo $form_goback_button; ?>
	</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
