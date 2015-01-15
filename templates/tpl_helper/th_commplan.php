<?php
/**
 * th_commplan.php
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
 * @package    comm_plan
 * @subpackage template_helper
 */

$select_priority	= $this->arrayToChoice('selPriority', $aPriority, 'select', NULL, $_SESSION['commplan']['sendThreshold']);

$select_limit_priority	= $this->arrayToChoice('selBelowPriority', array(5=>5, 4=>4, 3=>3, 2=>2, 1=>1), 'select', NULL, isset($_SESSION['commplan']['pcq_limit_priority']) ? ($_SESSION['commplan']['pcq_limit_priority'] + 1) : 1);

$select_quota_duration	= $this->arrayToChoice('selQuotaDuration', $aQuotaDuration, 'select', NULL, isset($_SESSION['commplan']['pcq_duration']) ? $_SESSION['commplan']['pcq_duration'] : 'q');

$input_quantity		= "<input class='numericEntry' type='text' id='frmNoOfDays' name='frmNoOfDays' value='" . (isset($_SESSION['commplan']['pcq_qty']) ? $_SESSION['commplan']['pcq_qty'] : NULL) . "' size='4' maxlength='4' >";

// eof
