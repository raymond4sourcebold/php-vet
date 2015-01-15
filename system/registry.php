<?php
/**
 * MVC system file for registering variables.
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
 * MVC system class for registering variables.
 * @package    registry
 */
Class Registry Implements ArrayAccess
{
	private $vars = array();

	function __construct()
	{
	}

	function set($key, $var)
	{
		if (isset($this->vars[$key]) == true) {
			throw new Exception('(CLASS: REGISTRY) Unable to set var `' . $key . '`. Already set.');
		}

		$this->vars[$key] = $var;
		return true;
	}

	function get($key)
	{
		if (isset($this->vars[$key]) == false) {
			return null;
		}

		return $this->vars[$key];
	}

	function remove($var)
	{
		unset($this->vars[$key]);
	}

	function offsetExists($offset)
	{
		return isset($this->vars[$offset]);
	}

	function offsetGet($offset)
	{
		return $this->get($offset);
	}

	function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	function offsetUnset($offset)
	{
		unset($this->vars[$offset]);
	}
}

// eof
