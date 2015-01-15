<?php
/**
 * Model file of controller: clients
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    model
 */

/**
 * Database access functions of controller: clients
 * @package    db_class
 */
Class Db Extends Model_Base
{
	/**
	 * Saves a new Client
	 */
	public function addClient($honorary, $lastName, $firstName, $externalId, $noMessage, $nPriority, $email, $prefChannelId, $usePreferredOnly, $backupChannelId, $nixedChannelCsv, $homeAddr, $officeAddr, $country) 
	{
		list($preferredChannel,
			$backupNixChannel,
			$homeAddress,
			$officeAddress, 
			$strCountry)	= $this->prepareOptionalFields($prefChannelId, $usePreferredOnly, $backupChannelId, $nixedChannelCsv, $homeAddr, $officeAddr, $country);
		
		$sql	= "INSERT INTO client SET 
				subscriberId = '{$_SESSION['subscriberId']}',
				honoraryId = '$honorary',
				lastName = ?,
				firstName = ?,
				clientExternalId = '$externalId',
				noMessage = '$noMessage',
				messageThreshold = '$nPriority',
				email = '$email'
				{$preferredChannel}
				{$backupNixChannel}
				{$homeAddress}
				{$officeAddress}
				{$strCountry}
				";
		//~ $this->dbc->query($sql);
		//~ return $this->dbc->lastInsertId();
		
		return $this->safeExecInsertWithParam($sql, array($lastName, $firstName));
	}
	
	/**
	 * Saves a new Client
	 */
	public function updateClient($clientId, $honorary, $lastName, $firstName, $externalId, $noMessage, $nPriority, $email, $prefChannelId, $usePreferredOnly, $backupChannelId, $nixedChannelCsv, $homeAddr, $officeAddr, $country)
	{
		list($preferredChannel,
			$backupNixChannel,
			$homeAddress,
			$officeAddress, 
			$strCountry)	= $this->prepareOptionalFields($prefChannelId, $usePreferredOnly, $backupChannelId, $nixedChannelCsv, $homeAddr, $officeAddr, $country);
		
		$sql	= "UPDATE client SET 
				honoraryId = '$honorary',
				lastName = '$lastName',
				firstName = '$firstName',
				clientExternalId = '$externalId',
				noMessage = '$noMessage',
				messageThreshold = '$nPriority',
				email = '$email'
				{$preferredChannel}
				{$backupNixChannel}
				{$homeAddress}
				{$officeAddress}
				{$strCountry}
			WHERE clientId = '$clientId'";
		return $this->dbc->exec($sql);
	}
	
	/**
	 * Prepare SQL lines for optional fields
	 */
	private function prepareOptionalFields($prefChannelId, $usePreferredOnly, $backupChannelId, $nixedChannelCsv, $homeAddr, $officeAddr, $country)
	{
		if ($prefChannelId) {
			$preferredChannel	= "
				,preferredChannelId = '$prefChannelId'
				,usePreferredExclusively = '$usePreferredOnly'";
		} else {
			$preferredChannel	= "
				,preferredChannelId = NULL
				,usePreferredExclusively = '0'";
		}
		
		$backupNixChannel	= '';
		if ($usePreferredOnly == 0) {
			$backupNixChannel	= "
				,backupChannelId = '$backupChannelId'
				,nixedChannelIdCsv = '$nixedChannelCsv'";
		}
		
		if ($homeAddr) {
			$homeAddress	= "
				,homeAddress1 = '{$homeAddr['line1']}'
				,homeAddress2 = '{$homeAddr['line2']}'
				,homeCity = '{$homeAddr['city']}'
				,homePostalCode = '{$homeAddr['postal']}'
				,homeProvinceOrState = '{$homeAddr['state']}'";
		} else {
			$homeAddress	= "
				,homeAddress1 = ''
				,homeAddress2 = ''
				,homeCity = ''
				,homePostalCode = ''
				,homeProvinceOrState = ''";
		}
		
		if ($officeAddr) {
			$officeAddress	= "
				,officeAddress1 = '{$officeAddr['line1']}'
				,officeAddress2 = '{$officeAddr['line2']}'
				,officeCity = '{$officeAddr['city']}'
				,officePostalCode = '{$officeAddr['postal']}'
				,officeProvinceOrState = '{$officeAddr['state']}'";
		} else {
			$officeAddress	= "
				,officeAddress1 = ''
				,officeAddress2 = ''
				,officeCity = ''
				,officePostalCode = ''
				,officeProvinceOrState = ''";
		}
		
		if ($country) {
			$strCountry	= "
				,country = '$country'";
		} else {
			$strCountry	= "
				,country = ''";
		}
		
		return array($preferredChannel, $backupNixChannel, $homeAddress, $officeAddress, $strCountry);
	}
	
	/**
	 * Deletes one client
	 * @param integer $clientId
	 * @return integer
	 */
	public function deleteClient($clientId)
	{
		return $this->dbc->exec("DELETE FROM client WHERE clientId = '$clientId'");
	}
	
	/**
	 * Adds client phone
	 */
	public function addClientPhone($clientId, $phonePriority, $phoneType, $phoneNumber)
	{
		$sql	= "INSERT INTO clientPhone SET
			clientId = '$clientId',
			priority = '$phonePriority',
			phoneType = '$phoneType',
			phoneNumber = '$phoneNumber'";
		return $this->dbc->exec($sql);
	}
	
	/**
	 * Returns phone prority
	 * @return array
	 */
	public function getPhonePriorityArray()
	{
		return array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5,
			6 => 6,
			7 => 7,
			8 => 8,
			9 => 9
		);
	}
	
	/**
	 * Returns phone type
	 * @return array
	 */
	public function getPhoneType()
	{
		return array(
			'mobile' => 'Cellphone', 
			'ofisph' => 'Office Phone', 
			'ofisfx' => 'Office Fax', 
			'ofispf' => 'Office Phone &amp; Fax',
			'homeph' => 'Home Phone', 
			'homefx' => 'Home Fax', 
			'homepf' => 'Home Phone &amp; Fax', 
		);
	}
	
	/**
	 * Gets clients
	 * @param integer or string $condition
	 * @return array
	 */
	public function getClients($condition=NULL)
	{
		$aReturn	= array();
		
		$sqlCond		= '';
		if (is_numeric($condition)) {
			$sqlCond	= " AND client.clientId = $condition ";
		} elseif (is_string($condition)) {
			$sqlCond	= " AND client.lastName LIKE '$condition%' ";
		}
		
		$sql	= "SELECT client.*
			FROM client
			WHERE subscriberId = {$_SESSION['subscriberId']}
			{$sqlCond}
			ORDER BY lastName, firstName";
		
		$stmt	= $this->dbc->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetchAll();
	}
	
	/**
	 * Gets client phone numbers
	 * @param integer $clientId
	 * @return array
	 */
	public function getClientPhoneNos($clientId=NULL)
	{
		$aReturn	= array();
		
		$sqlCond		= '';
		if ($clientId) {
			$sqlCond	= " WHERE clientPhone.clientId = '$clientId' ";
		}
		
		$sql	= "SELECT clientPhone.*
			FROM clientPhone
			{$sqlCond}
			ORDER BY phoneNumber";
		
		$stmt	= $this->dbc->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt->fetchAll();
	}
	
	/**
	 * Deletes all phone numbers that belong to a client
	 */
	public function deleteClientPhones($clientId)
	{
		$sql	= "DELETE FROM clientPhone WHERE clientId = '$clientId'";
		$this->dbc->exec($sql);
	}
	
	/**
	 * Gets message delivery channels
	 * @return array
	 */
	public function getChannels()
	{
		$sql	= "SELECT channelId, channelName, channelType FROM `channelType` ORDER BY channelId";
		
		$aReturn	= array();
		
		foreach ($this->dbc->query($sql) as $row) {
			$aReturn[$row['channelId']]	= array(
				'channelName' => $row['channelName'],
				'channelType' => $row['channelType']
			);
		}
		
		return $aReturn;
	}
}

// eof
