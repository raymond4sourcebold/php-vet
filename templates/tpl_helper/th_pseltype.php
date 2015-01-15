<?php
/**
 * th_pseltype.php
 * This file is included inside of /system/template.php on function show().
 * Variables here are accessible on the template file with the same names.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    proc_select_type
 * @subpackage template_helper
 */

$aProcType		= array(
	'one'	=> lang_one_step, 
	'two'	=> lang_two_step, 
	'group'	=> lang_group
);


$radIsSelected		= 'false';
$radio_proctype		= '';

foreach ($aProcType as $key => $value) {
	$checked	= '';
	
	if (isset($radProcType) && $radProcType == $key) {
		$checked		= ' checked ';
		$radIsSelected		= 'true';
	}
	
	$radio_proctype		.= "<div style='margin-left: 200px;'><label><input class='radProcType' type='radio' name='radProcType' value='{$key}' {$checked} /> &nbsp; $value</label></div>";
}

$radio_proctype		= "<div style='display: block;'>$radio_proctype</div>";

$aJsReplace	= array(
	'radIsSelected'		=> "\n" . 'var radIsSelected	= ' . $radIsSelected . ';' . "\n"
);

// eof
