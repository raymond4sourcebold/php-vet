<?php
/**
 * Messages template file.
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
	Messages page
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
		<div style="width: 700px; margin: auto;">
			<form id="form1" name="form1" method="post" action="" onsubmit="return messageValidate();">
			<input type="hidden" id="updateMessageId" name="updateMessageId" />
			<label></label>
			<fieldset>
				<legend><a id="frmMessagePane" href="#"><?php echo lang_click_new_message; ?></a></legend>
				<div id="boiteaddmsg" style="display: none">
					<label><?php echo lang_category; ?> 
<?php echo $select_msgcat; ?>
					</label>
					<label><input name="newtype" type="text" id="newtype"></label>
					<br />
<!--
					<?php echo lang_channel; ?> 
<?php echo $radio_channel; ?>
-->
					<br /><label>Description<br /><input type="text" name="textedescription" id="textedescription" style="width: 99%" /></label>
					<?php echo lang_message; ?> 
<?php echo $buttons_msgvar; ?>
<?php echo $select_msgdate; ?>
					<label> <br /><textarea id="textemessage" name="textemessage" style="width: 99%; height: 100px;"></textarea></label>
					<br />
					<label>
<?php echo $checkbox_active; ?> 
					<?php echo lang_active_for_practice; ?></label>
					<div align="right">
						<input id="addMsgButton" name="addmsg" type="submit" value="<?php echo lang_add_message; ?>">
						<input class="updateButtonFamily" id="updateMsgButton" name="updateMsgButton" type="submit" value="<?php echo lang_update_message; ?>">
						<input class="updateButtonFamily" id="updateMsgCancelButton" type="button" value="<?php echo lang_cancel_update; ?>" />
					</div>
				</div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><!--<th><?php echo lang_channel; ?></th>--><th><?php echo lang_category; ?></th><th><?php echo lang_description; ?></th><th><?php echo lang_message; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
<?php echo $table_tbody_grid; ?>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
