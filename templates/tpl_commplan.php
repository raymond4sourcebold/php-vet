<?php
/**
 * Communication Plan template file.
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
	Commplan page
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
			<label></label>
			<fieldset>
				<legend><?php echo lang_communication_plan; ?></legend>
				<div id="boiteaddmsg">
					<div class='capEnt' style='padding-left: 50px;'>
						<label><?php echo lang_message_sending_threshold; ?> 
<?php echo $select_priority; ?>
						</label>
						<div class='divinfo'><a class='info'>?<span>5 = <?php echo lang_life_death_issue; ?><br />4 = <?php echo lang_billing_issue; ?><br />3 = <?php echo lang_significant_issue; ?><br />2 = <?php echo lang_appointment_reminder; ?><br />1 = <?php echo lang_prevention; ?><br />0 = <?php echo lang_education; ?></span></a></div>
						
						<fieldset><legend><?php echo lang_per_client_quota; ?></legend>
						<div class='capEnt' style='padding-left: 50px;'>
							<label><?php echo lang_limit_priority_under; ?> 
<?php echo $select_limit_priority; ?>
							</label>
							<label><?php echo lang_quota_duration; ?> 
<?php echo $select_quota_duration; ?>
							</label>
							<label><?php echo lang_quantity; ?> 
<?php echo $input_quantity; ?>
							</label>
						</legend></fieldset>
					</div>
					<div align="right">
						<input id="addRowBtn" name="addmsg" type="submit" value="<?php echo lang_save; ?>">
					</div>
				</div>
			</fieldset>
			</form>
		</div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
