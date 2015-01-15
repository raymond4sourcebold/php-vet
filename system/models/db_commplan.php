<?php
/**
 * Model file of controller: commplan
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    model
 */

/**
 * Database access functions of controller: commplan
 * @package    db_class
 */
Class Db Extends Model_Base 
{
	/**
	 * Saves a communication plan
	 */
	public function saveCommPlan($nPriority, $perClientQuotaCFlow)
	{
		$sql	= "UPDATE subscriber SET 
				sendThreshold = '{$nPriority}',
				perClientQuotaCFlow = '{$perClientQuotaCFlow}'
			WHERE subscriberId = '{$_SESSION['subscriberId']}'";
		return $this->safeExec($sql);
	}
	
	public function getQuotaDurationArray()
	{
		return array(
			'w' => lang_week,
			'm' => lang_month, 
			'q' => lang_quarter, 
			's' => lang_semester, 
			'y' => lang_year
		);
	}
}

// eof
