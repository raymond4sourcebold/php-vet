<?php
/**
 * Define language constants.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    startup
 */

if (isset($_SESSION['subscriberLang'])) {
	/**
	 * Use language setting of subscriber
	 */
	$lang		= $_SESSION['subscriberLang'];
} else {
	/**
	 * Default to English
	 */
	$lang		= 'en';
}

if (file_exists(site_path . 'system/lang/' . $lang . '/main.ini')) {
	$aLang		= parse_ini_file(site_path . 'system/lang/' . $lang . '/main.ini');
	
	foreach ($aLang as $key => $value) {
		@define('lang_' . $key, $value);
	}
}

// eof
