<?php
/**
 * Execute all start up codes here.
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

/**
 * Set error reporting to show all
 */
error_reporting (E_ALL);

/**
 * Set session expire time to 24 hours
 */
ini_set('session.gc_maxlifetime', (24 * 60 * 60));
/**
 * Start session
 */
session_start();

/**
 * Constants
 */
define ('DIRSEP', DIRECTORY_SEPARATOR);
define ('CM', 1);

// Call arguments
define ('ARGUMENT_1', 2);
define ('ARGUMENT_2', 3);
define ('ARGUMENT_3', 4);
define ('ARGUMENT_4', 5);
define ('ARGUMENT_5', 6);
define ('ARGUMENT_6', 7);
define ('ARGUMENT_7', 8);
define ('ARGUMENT_8', 9);
define ('ARGUMENT_9', 10);

// SQL execution error result constant
define ('SQL_ERROR', 'SQL_ERROR');

// Get site path
$site_path = realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP) . DIRSEP;
define ('site_path', $site_path);

/**
 * Include language script
 */
include site_path . 'system/language.php';

/**
 * Instantiate registry
 */
$registry = new Registry;

// Save controller name into variable
if (isset($_GET['route'])) {
	if (($nPos = strpos($_GET['route'], '/')) !== false) {
		$registry->set('controller', substr($_GET['route'], 0, $nPos));
	} else {
		$registry->set('controller', $_GET['route']);
	}
} else {
	$registry->set('controller', 'index');
}

// Name of controller constant
define('CONTROLLER', $registry->get('controller'));



/**
 * For auto-loading classes
 */
function __autoload($class_name)
{
	if ($class_name == 'Db') {
		$file		= site_path . 'system/models' . DIRSEP . 'db_' . CONTROLLER . '.php';
		
		if (file_exists($file) == false) {
			// If no model is found, use the default model.
			$file	= site_path . 'system/models' . DIRSEP . 'db_default.php';
		}
	} else {
		$file		= site_path . 'system/models' . DIRSEP . strtolower($class_name) . '.php';
		
		if (file_exists($file) == false) {
			$file		= site_path . 'system' . DIRSEP . strtolower($class_name) . '.php';
			if (file_exists($file) == false) {
				return false;
			}
		}
	}

	include ($file);
}

// eof
