<?php
/**
 * MVC system file for controller routing function.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    system_controller
 */

/**
 * MVC system class for controller routing function.
 * @package    router
 */
Class Router
{
	private $registry;
	private $path;
	private $args = array();
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}

	function setPath($path)
	{
		$path = trim($path, '\\');
		$path .= DIRSEP;
		
		if (is_dir($path) == false) {
			throw new Exception ('Invalid controller path: `' . $path . '`');
		}

		$this->path = $path;
	}

	function getArg($key)
	{
		if (!isset($this->args[$key])) { return null; }
		return $this->args[$key];
	}
	
	function getWholeArg()
	{
		return $this->args;
	}

	function get($idx)
	{
		if (isset($this->$idx)) {
			return $this->$idx;
		}
		return NULL;
	}

	/**
	 * Give control from index.php to the controller determined by this router class.
	 */
	function delegate()
	{
		// Analyze route
		$this->getController($scriptController, $strController, $action, $args);
		
		// Redirect user to login form when not authorized
		if (empty($_SESSION['authorized_user']) && $strController != 'login' && $strController != 'error') {
			header('location: /login');
			exit;
		}
		
		// File available?
		if (is_readable($scriptController) == false) {
			header('location: /error/abort/Controller: ' . $scriptController . ' is not readable.  Please check file permissions.');
			exit;
		}
		
		/**
		 * Load language file to session if it's not there yet
		 * Caching is accomplished by saving the language INI to session.
		 */
		//~ if (!isset($_SESSION['lang'])) {
			//~ $_SESSION['lang']	= array();
		//~ }

		# Load common words file
		//~ if (!isset($_SESSION['lang']['common'])) {
			//~ $_SESSION['lang']['common']	= parse_ini_file(site_path . 'system/lang/common.ini');
		//~ }
		
		if (!isset($_SESSION['subscriberLang'])) {
			// User is not identified yet, let's use the English language temporarily.
			$_SESSION['subscriberLang']	= 'en';
		}
		
		// Load criteria always when we are working on it.
		//~ if (CONTROLLER == 'criteria' && isset($_SESSION['lang']['criteria'])) {
			//~ unset($_SESSION['lang']['criteria']);
		//~ }
		
		//~ $this->loadLanguageToSession('criteria');
		//~ $this->loadLanguageToSession(CONTROLLER);
		
		$this->extractArgs($args);
		
		// Include the controller file
		include ($scriptController);
		
		// Initiate the controller class
		$class		= 'Controller_' . $strController;
		$controller	= new $class($this->registry, $strController);
		
		// Action available?
		if (is_callable(array($controller, $action)) == false) {
			$this->registry['template']->set ('error', "404 &nbsp; &nbsp; <b>$action</b> does not exist.");
			
			// Let's not include error controller if it's directly called which means it's already instantiated above.
			if ($strController != 'error') {
				// Include error class
				include (substr($scriptController, 0, strrpos($scriptController, '/')) . '/error.php');
				$controller	= new Controller_Error($this->registry, 'error');
			}
			$action		= 'index';
		}
		
		// Run action
		$controller->$action();
	}

	private function extractArgs($args)
	{
		if (count($args) == 0) { return false; }
		$this->args = $args;
	}
	
	private function getController(&$file, &$controller, &$action, &$args)
	{
		$route	= (empty($_GET['route'])) ? '' : $_GET['route'];

		if (empty($route)) { $route = 'index'; }

		// Get separate parts
		$route	= trim($route, '/\\');
		$parts	= explode('/', $route);
		$args	= $parts;

		// Find right controller
		$cmd_path = $this->path;
		foreach ($parts as $part) {
			$fullpath = $cmd_path . $part;
			
			// Is there a dir with this path?
			if (is_dir($fullpath)) {
				$cmd_path .= $part . DIRSEP;
				array_shift($parts);
				continue;
			}

			// Find the file
			if (is_file($fullpath . '.php')) {
				$controller = $part;
				array_shift($parts);
				break;
			}
		}

		if (empty($controller)) { $controller = 'index'; };
		
		// Get action
		$action = array_shift($parts);
		if (empty($action)) { 
			$action = 'index'; 
		}

		$file = $cmd_path . $controller . '.php';
	}
	
	/**
	 * Loads language file to session
	 * @param string $controller
	 */
	//~ private function loadLanguageToSession($controller)
	//~ {
		//~ if (isset($_SESSION['lang'][$controller])) {
			//~ // Already loaded, no need to load it again.
			//~ return;
		//~ }
		
		//~ if (file_exists(site_path . 'system/lang/' . $_SESSION['subscriberLang'] . '/' . $controller . '.ini') == false) {
			//~ return;
		//~ }
		
		//~ $tmp	= parse_ini_file(site_path . 'system/lang/' . $_SESSION['subscriberLang'] . '/' . $controller . '.ini');
		
		//~ foreach ($tmp as $mainKey => $var) {
			//~ $aVar		= explode(' , ', $var);
			
			//~ if (is_array($aVar)) {
				//~ foreach ($aVar as $k => $v) {
					//~ if ($k == 0) {
						//~ $_SESSION['lang'][$controller][$mainKey]	= $v;
					//~ } else {
						//~ $_SESSION['lang'][$controller]["$mainKey$k"]	= $v;
					//~ }
				//~ }
			//~ } else {
				//~ $_SESSION['lang'][$controller][$mainKey]			= $var;
			//~ }
		//~ }
	//~ }
}

// eof
