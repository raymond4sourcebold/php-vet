<?php 
/**
 * Procedure header template file.
 * Displays the procedure sequence on top which serves as a visual guide to the user.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    html_proc_common
 */

/**
 * Determines which on the steps is current.
 */
$aTmp	= array(
	'pmessage'	=> '',
	'pifcrita'	=> '',
	/**
	 * Following controllers are under psenddate:
	 *   a) pseltype
	 *   b) ponestep
	 *   c) ptwostep
	 *   d) pgroup
	 */
	'psenddate'	=> '',
	
	'punless'	=> '',
	'premind'	=> '',
	'ppunch'	=> ''
);

if (CONTROLLER == 'pseltype' || CONTROLLER == 'ponestep' || CONTROLLER == 'ptwostep' || CONTROLLER == 'pgroup') {
	$aTmp['pmessage']	= ' id="procStepDone"';
	$aTmp['pifcrita']	= ' id="procStepDone"';
	$aTmp['psenddate']	= ' id="procStepOn"';
} else {
	if (array_key_exists(CONTROLLER, $aTmp) || CONTROLLER == 'pcomplete') {
		foreach ($aTmp as $key => $value) {
			if ($key == CONTROLLER) {
				$aTmp[$key]	= ' id="procStepOn"';
				break;
			}
			$aTmp[$key]	= ' id="procStepDone"';
		}
	}
}

?>
		<div class="procmenu">
			<img style="float: left; padding-right: 1px;" src="/images/procmenu-left.gif" />
			<ul>
				<li<?php echo $aTmp['pmessage']; ?>><?php echo lang_message; ?></li>
				<li<?php echo $aTmp['pifcrita']; ?>><?php echo lang_if_crit_a; ?></li>
				<li<?php echo $aTmp['psenddate']; ?>><?php echo lang_send_date; ?></li>
				<li<?php echo $aTmp['punless']; ?>><?php echo lang_unless_crit_b; ?></li>
				<li<?php echo $aTmp['premind']; ?>><?php echo lang_reminders; ?></li>
				<li<?php echo $aTmp['ppunch']; ?>><?php echo lang_punch_list; ?></li>
			</ul>
			<img src="/images/procmenu-right.gif" />
		</div>
