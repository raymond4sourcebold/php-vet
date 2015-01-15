<?php
/**
 * This script is where all web page requests on this site begin to execute.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    index
 */

/**
 * Startup tasks
 */
require 'system/startup.php';

/**
 * Connect to DB
 */
$db		= new Db;
$registry->set('db', $db);

/**
 * Load toolbox
 */
$tool		= new Tool($registry);
$registry->set('tool', $tool);

/**
 * Load static data
 */
$const		= new Const_Data;
$registry->set('const', $const);

/**
 * Load router
 */
$router		= new Router($registry);
$registry->set('router', $router);
$router->setPath (site_path . 'system/controllers');

/**
 * Load template object
 */
$template	= new Template($registry, $router->get('controller'));
$registry->set('template', $template);

/**
 * Render the view
 */
$router->delegate();

// eof
