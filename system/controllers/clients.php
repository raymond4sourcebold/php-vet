<?php
/**
 * Controller file for Clients page.
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
 * Controller for Clients page.
 * @package    clients
 */
Class Controller_Clients Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				if ($this->updateClient($_POST['updateRowId'])) {
					$_SESSION['system']['message']		= 'updated';
					header('location: /clients/success');
				}
			} else {
				if ($this->insertClient()) {
					$_SESSION['system']['message']		= 'saved';
					header('location: /clients/success');
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		$this->buildEntryForm();
		
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Displays client entry form
	 */
	public function create()
	{
		$this->registry['template']->set('goCreateNew', true);
		$this->index();
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get client data
	 */
	public function getJsonClientSearch()
	{
		$searchText	= $this->registry['router']->getArg(ARGUMENT_1);
		$aClients	= $this->registry['db']->getClients($searchText);
		
		$aOut	= array();
		
		foreach ($aClients as $key => $value) {
			foreach ($value as $i => $iValue) {
				$aOut[$key][$i] = htmlentities($iValue);
			}
		}
		echo json_encode($aOut);
		exit;
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get client data
	 */
	public function getJsonClient()
	{
		$clientId	= $this->registry['router']->getArg(ARGUMENT_1);
		list($aClient)	= $this->registry['db']->getClients($clientId);
		
		$aOut	= array();
		
		foreach ($aClient as $key => $value) {
			$aOut[$key] = htmlentities($value);
		}
		echo json_encode($aOut);
		exit;
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get client data
	 */
	public function getJsonCliPhone()
	{
		$clientId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		$aPhones	= $this->registry['db']->getClientPhoneNos($clientId);
		
		// encode array $json to JSON string
		$encoded	= json_encode($aPhones);
		
		// send response back and end script execution
		die($encoded);
	}
	
	/**
	 * Builds entry objects on form
	 */
	private function buildEntryForm()
	{
		$this->registry['template']->set('aHonorary', $this->registry['db']->getHonorary());
		$this->registry['template']->set('aPriority', $this->registry['const']->getPriorityArray());
		$this->registry['template']->set('aPhonePriority', $this->registry['db']->getPhonePriorityArray());
		$this->registry['template']->set('aPhoneType', $this->registry['db']->getPhoneType());
		$this->registry['template']->set('aChannels', $this->registry['db']->getChannels());
	}

	/**
	 * Called to display Client and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_client_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_client_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/clients/delete/1
	 *	Where: Clients = controller, delete = this function, 1 = ClientId to delete
	 */
	public function delete()
	{
		$clientId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteClient($clientId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}

	
	/**
	 * Saves new category (if new entry) and saves Client
	 * @return boolean
	 */
	private function insertClient()
	{
		list($nixedChannelCsv, $homeAddr, $officeAddr)	= $this->prepareFieldsForDbSave();
		
		$insertId	= $this->registry['db']->addClient(
			$_POST['selHonorary'], 
			$_POST['frmLastname'], 
			$_POST['frmFirstname'],
			$_POST['frmExternalId'],
			$_POST['radNoMessage'],
			$_POST['selPriority'],
			$_POST['frmEmail'] . ($_POST['frmSecondaryEmail'] ? ',' . $_POST['frmSecondaryEmail'] : NULL),
			$_POST['selPreferred'],
			(isset($_POST['chkUsePreferredOnly']) ? 1 : 0),
			$_POST['selBackup'],
			$nixedChannelCsv,
			$homeAddr,
			$officeAddr,
			$_POST['country']
		);
		
		if ($insertId) {
			if (isset($_POST['phoneNumber'])) {
				foreach ($_POST['phoneNumber'] as $key => $phoneNumber) {
					$this->registry['db']->addClientPhone(
						$insertId,
						$_POST['selPhonePriority'][$key],
						$_POST['selPhoneType'][$key],
						$this->registry['tool']->stripCharsGetNumeric($phoneNumber)
					);
				}
			}
		} else {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Saves new category (if new entry) and saves Client
	 * @param integer $clientId Client ID
	 * @return boolean 
	 */
	private function updateClient($clientId)
	{
		list($nixedChannelCsv, $homeAddr, $officeAddr)	= $this->prepareFieldsForDbSave();
		
		$this->registry['db']->updateClient(
			$clientId,
			$_POST['selHonorary'], 
			$_POST['frmLastname'], 
			$_POST['frmFirstname'],
			$_POST['frmExternalId'],
			$_POST['radNoMessage'],
			$_POST['selPriority'],
			$_POST['frmEmail'] . ($_POST['frmSecondaryEmail'] ? ',' . $_POST['frmSecondaryEmail'] : NULL),
			$_POST['selPreferred'],
			(isset($_POST['chkUsePreferredOnly']) ? 1 : 0),
			$_POST['selBackup'],
			$nixedChannelCsv,
			$homeAddr,
			$officeAddr,
			$_POST['country']
		);
		
		// Delete all client phones before inserting if any.
		$this->registry['db']->deleteClientPhones($clientId);
		
		if (isset($_POST['phoneNumber'])) {
			foreach ($_POST['phoneNumber'] as $key => $phoneNumber) {
				$this->registry['db']->addClientPhone(
					$clientId,
					$_POST['selPhonePriority'][$key],
					$_POST['selPhoneType'][$key],
					$this->registry['tool']->stripCharsGetNumeric($phoneNumber)
				);
			}
		}
		
		return true;
	}
	
	/**
	 * Prepare SQL lines for optional fields
	 * @return array
	 */
	private function prepareFieldsForDbSave()
	{
		$nixedChannelCsv	= '';
		for ($x = 1; $x <= 9; $x++) {
			if (isset($_POST['chkNix_' . $x])) {
				$nixedChannelCsv	.= $nixedChannelCsv ? ',' : NULL;
				$nixedChannelCsv	.= $x;
			}
		}
		
		$homeAddr	= NULL;
		if ($_POST['homeAddressLine1']) {
			$homeAddr	= array(
				'line1'  => $_POST['homeAddressLine1'],
				'line2'  => $_POST['homeAddressLine2'],
				'city'   => $_POST['homeCity'],
				'postal' => $_POST['homePostalCode'],
				'state'  => $_POST['homeProvOrState']
			);
		}
		$officeAddr	= NULL;
		if ($_POST['ofisAddressLine1']) {
			$officeAddr	= array(
				'line1'  => $_POST['ofisAddressLine1'],
				'line2'  => $_POST['ofisAddressLine2'],
				'city'   => $_POST['ofisCity'],
				'postal' => $_POST['ofisPostalCode'],
				'state'  => $_POST['ofisProvOrState']
			);
		}
		
		return array($nixedChannelCsv, $homeAddr, $officeAddr);
	}
}

// eof
