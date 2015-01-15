<?php
/**
 * Model file for static data.
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
 * Model for static data.
 * The following functions do not access the database but returns static data.
 * @package    Index_Class
 */
class Const_Data
{
	/**
	 * Returns message prority
	 * Called from
	 *	Controller: clients, pmessage, commplan
	 * @return array
	 */
	public function getPriorityArray()
	{
		return array(
			5 => 5, // life or death
			4 => 4, // billing issue
			3 => 3, // significant health issue
			2 => 2, // appointment reminder
			1 => 1, // prevention
			0 => 0  // education
		);
	}
	
	/**
	 * Returns send date offset
	 * Called from
	 *	Controller: ponestep, ptwostep
	 * @return array
	 */
	public function getSendDateOffset()
	{
		return array(
			'0:d'	=> '0',
			'1:w'	=> '1 ' . lang_week,
			'2:w'	=> '2 ' . lang_weeks,
			'3:w'	=> '3 ' . lang_weeks, 
			'1:m'	=> '1 ' . lang_month,
			'2:m'	=> '2 ' . lang_months,
			'3:m'	=> '3 ' . lang_months,
			'6:m'	=> '6 ' . lang_months,
			'1:y'	=> '1 ' . lang_year,
			'2:y'	=> '2 ' . lang_years,
			'3:y'	=> '3 ' . lang_years,
			'5:y'	=> '5 ' . lang_years,
			'8:y'	=> '8 ' . lang_years,
			'10:y'	=> '10 ' . lang_years,
			'12:y'	=> '12 ' . lang_years,
			'15:y'	=> '15 ' . lang_years,
			'20:y'	=> '20 ' . lang_years
		);
	}

	/**
	 * Returns send date recurring period
	 * Called from
	 *	Controller: ponestep, pgroup, ptwostep
	 * @return array
	 */
	public function getSendDateRecur()
	{
		return array(
			'1:w'	=> '1 ' . lang_week,
			'2:w'	=> '2 ' . lang_weeks,
			'3:w'	=> '3 ' . lang_weeks, 
			'1:m'	=> '1 ' . lang_month,
			'2:m'	=> '2 ' . lang_months,
			'3:m'	=> '3 ' . lang_months,
			'6:m'	=> '6 ' . lang_months,
			'1:y'	=> '1 ' . lang_year
		);
	}

	/**
	 * Returns send date anticipation
	 * Called from
	 *	Controller: ponestep, ptwostep
	 * @return array
	 */
	public function getSendDateAnticipation()
	{
		return array(
			'0:d'	=> '0 ' . lang_day,
			'1:d'	=> '1 ' . lang_day,
			'2:d'	=> '2 ' . lang_days,
			'3:d'	=> '3 ' . lang_days, 
			'4:d'	=> '4 ' . lang_days,
			'5:d'	=> '5 ' . lang_days,
			'6:d'	=> '6 ' . lang_days,
			'1:w'	=> '1 ' . lang_week,
			'2:w'	=> '2 ' . lang_weeks,
			'3:w'	=> '3 ' . lang_weeks,
			'1:m'	=> '1 ' . lang_month
		);
	}
}

// eof
