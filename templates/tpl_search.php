<?php
/**
 * Search template file.
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
	Follow-up Owner or Animal Search Form
-->
<div id="haut"></div>
<div id="logo"></div>
<div id="general">
<?php include 'templates/_inc_mainmenu.php'; ?>
<div id="headercontenu"></div>
	<div id="contenu">
		<div align="center" style='float: left; margin-left: 25px;'>
			<label><?php echo lang_owners_name; ?> <input type="text" id="ownerName" name="ownerName"></label>
			<label><?php echo lang_or_animals_name; ?> <input type="text" id="animalName" name="animalName"></label>
		</div>
		<div id='nMatchCount' style='float: right; margin-right: 23px;'></div>
		<div class='clearBoth'></div>
		<div id="tableaustart"></div>
		<div id="clientGrid">
			<table style="border: 0px none ; margin: auto; width: 700px; border-spacing: 0px; text-align: center;">
				<thead><tr class="lightHead">
					<th>
						<div style='float: left;'><?php echo lang_owner; ?></div>
					</th>
				</tr></thead>
				<tbody id='gridClientTbody'></tbody>
			</table>
		</div>
		<div id="animalGrid">
			<table style="border: 0px none ; margin: auto; width: 700px; border-spacing: 0px; text-align: center;" id="tblNotSortable">
				<thead><tr class="lightHead">
					<th><?php echo lang_animal_name; ?></th>
					<th><?php echo lang_owners_name; ?></th>
					<th><?php echo lang_town; ?></th>
					<th>&nbsp;</th>
				</tr></thead>
				<tbody id="gridAnimalTbody"></tbody>
			</table>
		</div>
		<div id="tableaustop"></div>
	</div>
	<div id="contenutext"></div>
	<div id="footer"></div>
</div>
