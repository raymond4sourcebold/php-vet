<?php
/**
 * Upload INI template file.
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
	Template for testuploadini
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
		<fieldset style="width: 700px; margin: auto;">
			<legend><?php echo lang_ini_file_upload; ?></legend>
			<form method="post" enctype='multipart/form-data' onsubmit='return validateEntry();'>
				<div class='capEnt'><?php echo lang_choose_ini_file; ?>
					<input type='file' id='frmUpload' name='frmUpload' />
				</div>
				<div style='float: right;'>
					<input type='submit' value='<?php echo lang_upload_client_file; ?>' />
				</div>
			</form>
		</fieldset>
<?php if (isset($uploadDetails)) { ?>
		<fieldset style="width: 700px; margin: auto;">
			<legend><?php echo lang_upload_results; ?></legend>
			<div style='font-family: "Courier New" Arial; font-size: 0.8em;'><?php echo $uploadDetails; ?></div>
		</fieldset>
<?php } ?>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
