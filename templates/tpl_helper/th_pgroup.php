<?php
/**
 * th_pgroup.php
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
 * @package    proc_group
 * @subpackage template_helper
 */

$yesNo	= array(
	1	=> lang_word_yes,
	0	=> lang_word_no
);

$radRecurringYesno		= $this->arrayToChoice('radRecurring', $yesNo, 'radio', NULL, ($radRecurring ? $radRecurring : 0));

$aRecurCri	= array();
foreach ($aRecur as $key => $value) {
	$aRecurCri[$key]	= $value;
}
$selRecurPeriod	= $this->arrayToChoice('selRecur', $aRecurCri, 'select', NULL, ($selRecur ? $selRecur : '1:y'));

$aJsReplace	= array(
	'nowMmDdYyyy'	=> date('m/d/Y'),
	'isRecurring'	=> "\n" . 'var isRecurring = ' . ($selRecur ? 'true' : 'false') . ';' . "\n"
);

// eof
