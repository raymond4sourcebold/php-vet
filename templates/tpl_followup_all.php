<?php
/**
 * All Follow-up template file.
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
	All FollowUps for a client.
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
	<?php include 'templates/_inc_mainmenu.php'; ?>
	<div id="headercontenu"></div>
	<div id="contenu">
		<div id="tableaustart" style='margin: 0 auto;'></div>
		<table id='tblSortable' style='border: 0px; width: 700px; border-spacing: 0px; margin:auto; text-align: center;'>
			<thead><tr class='lightHead'><th><?php echo lang_send_date; ?></th><th><?php echo lang_proc; ?></th><th><?php echo lang_category; ?></th><th><?php echo lang_status; ?></th><th>&nbsp;</th><th>&nbsp;</th></tr></thead>
			<tbody>
<?php echo $grid_tr_rows; ?>
			</tbody>
		</table>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
