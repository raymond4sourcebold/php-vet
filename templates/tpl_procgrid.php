<?php
/**
 * Proc Grid template file.
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
	Procedure Grid
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
	<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
		<div style="width: 700px; margin: auto;">
			<a href="/procedures/create"><?php echo lang_create_new_proc; ?></a>
		</div>
		<div id='divProcCopy' style="width: 700px; margin: auto; display: none;">
			<form id="form1" name="form1" method="post" action="" onsubmit="return entryValidate();">
			<input type='hidden' id='copyProcId' name='copyProcId' />
			<input type='hidden' id='procMsgId' name='procMsgId' />
			<fieldset>
				<legend><?php echo lang_proc_copy; ?></legend>
				<div class='capEnt'>
					<label for='frmProcedureName'><?php echo lang_save_it_as; ?></label>
					<input type='text' id='frmProcedureName' name='frmProcedureName' maxlength='50' size='30' />
					<div class='divinfo'><span id='ajaxMsg'></span></div>
				</div>
				<div style='float: right;'><input id='btnCopy' type='submit' value='<?php echo lang_copy; ?>' /><input id='btnCancel' type='button' value='<?php echo lang_cancel; ?>' /></div>
			</fieldset>
			</form>
		</div>
		<div id="tableaustart"></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
		<thead><tr class='lightHead'><th><?php echo lang_owner; ?></th><th><?php echo lang_name; ?></th><th><?php echo lang_step; ?></th><th><?php echo lang_practice; ?></th><th><?php echo lang_active; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
<?php echo $tbody_procgrid; ?>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
