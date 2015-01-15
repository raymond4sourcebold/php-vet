<?php
/**
 * th_punch.php
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
 * @package    proc_punch
 * @subpackage template_helper
 */

$checkbox_practice_proc		= $this->createCheckBox('isPracticeProc', (isset($isPracticeProc) ? $isPracticeProc : NULL), NULL, true);
$checkbox_active_proc		= $this->createCheckBox('isActiveProc', (isset($isActiveProc) ? $isActiveProc : NULL), 'fcnConfirm');

// eof
