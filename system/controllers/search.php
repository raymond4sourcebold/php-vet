<?php
/**
 * Controller file for Search page.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    controller
 */

/**
 * Controller for Search page.
 * @package    search
 */
Class Controller_Search Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		$this->registry['template']->set('templateType', 'animal');
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get owner and animal data
	 */
	public function getJsonOwnerSearch()
	{
		$searchText	= $this->registry['router']->getArg(ARGUMENT_1);
		$aOwnerAnimals	= $this->registry['db']->searchOwnerFollowUp($searchText);

		$aOut	= array();

		foreach ($aOwnerAnimals as $key => $value) {
			foreach ($value as $i => $iValue) {
				$aOut[$key][$i] = htmlentities($iValue);
			}
		}
		echo json_encode($aOut);
		exit;
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get animal and owner data
	 */
	public function getJsonAnimalSearch()
	{
		$searchText	= $this->registry['router']->getArg(ARGUMENT_1);
		$aAnimals	= $this->registry['db']->searchFollowUp($searchText);
		
		$aOut	= array();

		foreach ($aAnimals as $key => $value) {
			foreach ($value as $i => $iValue) {
				$aOut[$key][$i] = htmlentities($iValue);
			}
		}
		echo json_encode($aOut);
		exit;
	}
}

// eof
