<?php
/**
 * Class file of helper functions
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    index_controller
 */

/**
 * Tool class of helper functions
 * @package    tool
 */
Class Tool
{
	private $registry;
	
	public function __construct($registry=NULL)
	{
		if ($registry) {
			$this->registry	= $registry;
		}
	}
	
	/**
	 * Formats date string mm/dd/yyyy to yyyy-mm-dd
	 */
	public function convertDateToDbFormat($strDate)
	{
		$retDate	= '';
		
		if ($strDate) {
			list($mm, $dd, $yyyy)   = explode('/', $strDate);
			$retDate		= "$yyyy-$mm-$dd";
		}
		
		return $retDate;
	}
	
	/**
	 * Used to get numeric digits from a string
	 *	Sample: 01-23-45-67-89 to 0123456789
	 */
	public function stripCharsGetNumeric($string)
	{
		$outNum		= '';
		
		for ($x = 0; $x < strlen($string); $x++) {
			if (is_numeric($string{$x})) {
				$outNum	.= $string{$x};
			}
		}
		
		return $outNum;
	}
	
	/**
	 * Checks if a string is in YYYY-MM-DD format
	 */
	public function isYyyyMmDdDashSeparated($strDate)
	{
		$Yyyy		= substr($strDate, 0, 4);
		$firstDash	= substr($strDate, 4, 1);
		$Mm		= substr($strDate, 5, 2);
		$secondDash	= substr($strDate, 7, 1);
		$Dd		= substr($strDate, 8, 2);
		
		return (is_numeric($Yyyy) && $firstDash == '-' && is_numeric($Mm) && $secondDash == '-' && is_numeric($Dd));
	}
	
	/**
	 * Calls a system script asynchronously.
	 */
	public function asynchronousCall($command)
	{
		pclose(popen($command . ' /dev/null &', 'r'));
	}
}

// eof
