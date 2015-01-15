<?php 
/** 
 * Helper class file for INI File reader
 * 
 * @copyright  Copyright (c) 2008 Tim Cameron Ryan 
 * @license    Released under the GPL v2.0 
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    helper
 */ 

/** 
 * Helper class for INI File reader
 * @package    upload_ini
 */ 
class ini_reader 
{
	/**
	 * Array $data to hold data
	 */
	protected $data = array('' => array()); 
	
	/**
	 * Loads INI string to array
	 * @param array $data
	 * @return array $data
	 */
	public function loadString($data) { 
		// parse data 
		$this->data = array('' => array()); 
		$currentSection = ''; 

		foreach (preg_split('/\r\n?|\r?\n/', $data) as $line) 
		{
			// parse line 
			if (preg_match('/^\s*\[\s*(.*)\s*\]\s*$/', $line, $matches)) { 
				// section header 
				$currentSection = $matches[1]; 

			} else if (preg_match('/^\s*([^;\s].*?)\s*=\s*([^\s].*?)$/', $line, $matches)) {
				// parse value
				//~ if (preg_match('/^"(?:\\.|[^"])*"|^\'(?:[^\']|\\.)*\'/', $matches[2], $value)) {
					//~ $value = stripslashes(substr($value[0], 1, -1)); 
				//~ } else {
					//~ $value = preg_replace('/^["\']|\s*;.*$/', '', $matches[2]); 
				//~ }

				$value		= $matches[2];

				// parse data types 
				$value_noquotes		= trim($value, "\"");
				
				if (is_numeric($value_noquotes)) {
					$value = (float) $value_noquotes; 
					
				//~ } else if (strtolower($value_noquotes) == 'true') {
					//~ $value = true; 
					
				//~ } else if (strtolower($value_noquotes) == 'false') {
					//~ $value = false; 
					
				} else {
					$value	= $value_noquotes;
				}

				// set value 
				$name		= $matches[1]; 
				$section	=& $this->parseVariableName($name, $currentSection, true); 
				$section[$name]	= $value; 

			} else if (preg_match('/^\s*([^;\s].*?)\s*$/', $line, $matches)) {
				/**
				 * This path allows the instantiation of variable with no values
				 *	E.g. stateOrProvince = ""
				 */

				if (isset($matches[2]) == false) {
					continue;
				}

				// parse value
				if (preg_match('/^"(?:\\.|[^"])*"|^\'(?:[^\']|\\.)*\'/', $matches[2], $value)) {
					$value = stripslashes(substr($value[0], 1, -1)); 
				} else {
					$value = preg_replace('/^["\']|\s*;.*$/', '', $matches[2]); 
				}

				// parse data types 
				if (is_numeric($value)) {
					$value = (float) $value; 
				} else if (strtolower($value) == 'true') {
					$value = true; 
				} else if (strtolower($value) == 'false') {
					$value = false; 
				}

				// set value 
				$name	= trim(str_replace('=', '', $matches[1]));
				$section =& $this->parseVariableName($name, $currentSection, true); 
				$section[$name] = $value; 
			}
		}

		return $this->data;
	} 
	
	/**
	 * Parse variable name
	 * @param string $name
	 * @param string $section
	 * @param boolean $create
	 * @return string $section
	 */
	protected function &parseVariableName(&$name, $section = '', $create = false) 
	{ 
		// parse name 
		$levels = explode('.', $name); 
		// check array 
		if (substr($name, -2, 2) == '[]') 
			$name = '[]'; 
		else 
			$name = array_pop($levels); 

		// climb section heirarchy 
		$section =& $this->data[$section]; 
		foreach ($levels as $level) 
		{ 
			if (!is_array($section[$level]) && !$create) 
				return false; 
			else if (!is_array($section[$level])) 
				$section[$level] = array(); 
			$section =& $section[$level]; 
		} 

		// get array key 
		if ($name == '[]') 
			$name = count($section); 
		// return section 
		return $section; 
	}
} 

// eof
