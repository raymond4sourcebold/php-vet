<?php
/**
 * MVC system file for template handling.
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
 * MVC system class for template handling.
 * @package    template
 */
Class Template extends Form_Controls
{
	private $registry;
	private $controller;
	private $vars = array();
	
	function __construct($registry, $contrl)
	{
		$this->registry		= $registry;
		$this->controller	= $contrl;
	}

	function set($varname, $value, $overwrite=false)
	{
		if (isset($this->vars[$varname]) == true AND $overwrite == false) {
			trigger_error ('Unable to set var `' . $varname . '`. Already set, and overwrite not allowed.', E_USER_NOTICE);
			return false;
		}

		$this->vars[$varname] = $value;
		return true;
	}

	function remove($varname)
	{
		unset($this->vars[$varname]);
		return true;
	}

	/**
	 * Include Javascript, CSS and template files.
	 */
	function show($name)
	{
		/**
		 * $javascript may be manually assigned by programmer on controller.
		 */
		$javascript	= NULL;
		/**
		 * $css may be manually assigned by programmer on controller.
		 */
		$css		= NULL;
		/**
		 * $aJsReplace is defined on tpl_helper/th_<controller>.php
		 */
		$aJsReplace	= NULL;
			
		$page		= CONTROLLER;
		
		$templateFile		= site_path . 'templates' . DIRSEP . 'tpl_' . $name . '.php';
		if (file_exists($templateFile) == false) {
			$this->vars['error'][]		= "404 &nbsp; &nbsp; Template <b>`$name`</b> does not exist.";
			$this->vars['error'][]		= "Please create it in <b>`$templateFile`</b>.";
			$templateFile			= site_path . 'templates' . DIRSEP . 'tpl_error.php';
			$page				= 'error';
		}
		
		
		/**
		 * Load language variables to be used by tpl_helper/th_<template> and tpl_<template> files.
		 */
		
		// Load common language variables
		//~ foreach ($_SESSION['lang']['common'] as $key => $value) {
			//~ $$key	= $value;
		//~ }
		
		if (CONTROLLER == 'pifcrita'
		 || CONTROLLER == 'ponestep'
		 || CONTROLLER == 'ptwostep'
		 || CONTROLLER == 'punless'
		 || CONTROLLER == 'premind'
		 || CONTROLLER == 'pcomplete'
		 || CONTROLLER == 'criteria') {
			// Load criteria language variables if it is set
			if (isset($_SESSION['lang']['criteria'])) {
				foreach ($_SESSION['lang']['criteria'] as $key => $value) {
					$$key	= $value;
				}
			}
		}
		// Load page-specific language variables it is set
		//~ if (isset($_SESSION['lang'][CONTROLLER])) {
			//~ foreach ($_SESSION['lang'][CONTROLLER] as $key => $value) {
				//~ $$key	= $value;
			//~ }
		//~ }
		
		
		// Load variables
		foreach ($this->vars as $key => $value) {
			$$key	= $value;
		}
		
		if ($aTplVars = $this->sessionToTemplateVars()) {
			// Load session variables
			foreach ($aTplVars as $key => $value) {
				$$key = $value;
			}
		}
		
		/**
		 * tpl helpers convert array to selects, radio, checkbox.
		 */
		$tpl_helper	= site_path . 'templates' . DIRSEP . 'tpl_helper' . DIRSEP . 'th_' . $name . '.php';
		if (file_exists($tpl_helper)) {
			include ($tpl_helper);
		}
		
		$_javascript	= '';
		// Add autohide message to $aJsReplace array.
		if (isset($autoHideTitle)) {
			$_javascript	.= "\n<script type='text/javascript'>"
						. "\n\tvar autoHideTitle	= '{$autoHideTitle}';"
						. "\n\tvar autoHideMessage	= '{$autoHideMessage}';"
					. "\n</script>";
		} else {
			$_javascript	.= "\n<script type='text/javascript'>"
						. "\n\tvar autoHideTitle	= '';"
						. "\n\tvar autoHideMessage	= '';"
					. "\n</script>";
		}
		
		$_javascript	.= $this->processJavascript($page, $javascript, $aJsReplace);
		$_css		= $this->processCss($page, $css);
		
		include (site_path . 'templates' . DIRSEP . '_header.php');
		include ($templateFile);
		include (site_path . 'templates' . DIRSEP . '_footer.html');
	}
	
	/**
	 * Auto includes Javascript files
	 * @param string $page
	 * @param array $javascript
	 * @param array $aJsReplace
	 * @return string $retString
	 */
	private function processJavascript($page, $javascript, $aJsReplace=NULL)
	{
		$retString	= '';
		
		// Include programmer specified Javascript files
		if (isset($javascript) && is_array($javascript)) {
			foreach ($javascript as $jsFile) {
				$retString	.= "\n<script type=\"text/javascript\" src=\"/javascript/{$jsFile}.js\"></script>";
			}
		}
		
		/**
		 * Auto include same name Javascript file.
		 */
		if (file_exists(site_path . "javascript/$page.js")) {
			$strJs		= file_get_contents(site_path . "javascript/$page.js");
			
			// Apply javascript replacements from array variable.
			if (isset($aJsReplace) && is_array($aJsReplace)) {
				foreach ($aJsReplace as $key => $value) {
					$strJs		= str_replace('{' . $key .'}', $value, $strJs);
				}
			}
			
			/**
			 * Apply language into Javascript file
			 */
			$aCurlyLang	= $this->getCurlyLang($strJs);
			foreach ($aCurlyLang as $replaceWithLang) {
				/**
				 * Add slashes to fix Javascript errors
				 */
				$localWord		= constant($replaceWithLang);
				$localWordSlashed	= addslashes($localWord);
				
				$strJs		= str_replace('{' . $replaceWithLang . '}', $localWordSlashed, $strJs);
			}
			
			$retString	.= "<script type='text/javascript'>{$strJs}</script>";
		}
		
		return $retString;
	}
	
	/**
	 * Auto includes CSS files
	 * @param string $page
	 * @param array $css
	 * @return string $retString
	 */
	private function processCss($page, $css)
	{
		$retString	= '';
		$useDefaultCss	= true;
		
		// Include programmer specified CSS files
		if (isset($css) && is_array($css)) {
			foreach ($css as $cssfile) {
				// To prevent duplicate inclusion of own CSS which is included below.
				if ($cssfile != $page) {
					$useDefaultCss	= false;
					$retString	.= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"/css/{$cssfile}.css\" />";
				}
			}
		}
		
		// Check if own CSS exists before inclusion
		if (file_exists(site_path . "css/$page.css")) {
			$retString	.= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"/css/$page.css\" />";
		} else {
			if ($useDefaultCss) {
				$retString	.= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"/css/default.css\" />";
			}
		}
		
		return $retString;
	}
	
	/**
	 * Gets string on curly braces with 'lang_' as first characters
	 * @param string $strJs Javascript code string
	 * @return array
	 */
	private function getCurlyLang($strJs)
	{
		$retArr		= array();
		
		$nLen		= strlen($strJs);
		
		$nStartCurly	= false;
		
		for ($x = 0; $x < $nLen; $x++) {
			$char	= $strJs{$x};
			
			if ($char == '{') {
				/**
				 * Get five characters starting on previous instance of '{'
				 */
				if (substr($strJs, ($x + 1), 5) == 'lang_') {
					$strSubset	= substr($strJs, ($x + 1));
					
					$oLen		= strlen($strSubset);
					
					$z	= 0;
					
					for ($y = 0; $y < $oLen; $y++) {
						if ($strSubset{$y} == '}') {
							$z	= $y;
							break;
						}
						if (strpos('_abcdefghijklmnopqrstuvwxyz0123456789', $strSubset{$y}) === false) {
							break;
						}
					}
					
					if ($z) {
						/**
						 * This is a language constant
						 */
						$retArr[]	= substr($strJs, $x + 1, $y);
					}
				}
			}
		}
		
		return $retArr;
	}
}

// eof
