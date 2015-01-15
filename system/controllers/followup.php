<?php
/**
 * Controller file for Follow-Up page.
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
 * Controller for Follow-Up page.
 * @package    follow_up
 */
Class Controller_FollowUp Extends Controller_Base
{
	/**
	 * Animal ID
	 */
	var $animalId;
	
	/**
	 * Client ID
	 */
	var $clientId;
	
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($this->animalId) {
			$this->main();
		} else {
			/**
			 * No direct call.  Animal Id is required by this page.
			 */
			header('location: /search');
			exit;
		}
	}
	
	/**
	 * Displays the follow-up form.
	 */
	public function main()
	{
		if ($_POST) {
			if ($_POST['updateRowId']) {
				if ($this->updateFollowUp($_POST['updateRowId'])) {
					$_SESSION['system']['message']		= 'updated';
					header('location: /followup/success/' . $_POST['animalId']);
				}
			} else {
				if ($this->saveFollowUp() == false) {
					if ($this->registry['db']->sqlerrno == '23000') {
						$_SESSION['system']['message']		= 'duplicate';
						$this->registry['template']->set('autoHideMessage', addslashes(lang_duplicate_error));
						
					} else {
						header('location: /error/abort/Insert FollowUp: ' . $this->registry['db']->sqlerror);
						exit;
					}
				} else {
					$_SESSION['system']['message']		= 'saved';
					header('location: /followup/success/' . $_POST['animalId']);
				}
			}
		}
		
		$this->registry['template']->set('javascript', array('mktime_date', 'jquery/jquery.tablesorter', 'jquery/jquery.datePicker', 'jquery/jquery.bgiframe.min', 'jquery/date', 'validator_date'));
		$this->registry['template']->set('css', array('styleG', 'datePicker'));
		
		$this->registry['template']->set('animalId', $this->animalId);
		
		$aAnimalAndOwner	= $this->registry['db']->getAnimalAndOwner($this->animalId);
		
		if ($aAnimalAndOwner === false) {
			$_SESSION['system']['message']		= lang_failure;
			$_SESSION['system']['autoHideMessage']	= 'Animal ID: ' . $this->animalId . ', ' . addslashes(lang_animal_not_found);
			
			header('location: /search/error');
			exit;
		}
		
		$this->registry['template']->set('aniOwn', $aAnimalAndOwner);
		$this->registry['template']->set('aFupSendDate', $this->registry['db']->getFollowUpSendDate());
		$this->registry['template']->set('aChannels', $this->registry['db']->getClientChannels($aAnimalAndOwner['clientId']));
		$this->buildEntryForm();
		$this->buildGrid();
		
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Can be called internally or publicly as webpage
	 * @param integer $animalId Animal ID
	 */
	public function manage($animalId=NULL)
	{
		if (is_null($animalId)) {
			$animalId			= $this->registry['router']->getArg(ARGUMENT_1);
			
			if (!$animalId) {
				/**
				 * Nothing to manage, redirect to search
				 */
				header('location: /search');
				exit;
			}
			
			if (strpos($animalId, ',') !== false) {
				/**
				 * The parameter is not an Animal ID but a CSV of Client PMS ID and Animal PMS ID.
				 */
				$aTmp			= explode(',', $animalId);
				
				if (count($aTmp) != 2) {
					header('location: /search');
					exit;
				}
				
				$clientPmsId		= trim($aTmp[0], '\"');
				$animalPmsId		= trim($aTmp[1], '\"');
				
				$animalId		= $this->registry['db']->getAnimalIdUsingPmsIds($clientPmsId, $animalPmsId);
				if (!$animalId) {
					/**
					 * Client PMS ID and Animal PMS ID not matched
					 */
					header('location: /search');
					exit;
				}
			}
		}
		
		$this->animalId		= $animalId;
		
		$this->main();
	}
	
	/**
	 * Displays all follow-up for a client.
	 */
	public function all()
	{
		$this->clientId		= $this->registry['router']->getArg(ARGUMENT_1);
		
		$this->registry['template']->set('css', array('styleG'));
		
		$this->registry['template']->set('aRows', $this->registry['db']->getSchedulesMsgs($this->clientId));
		$this->registry['template']->show('followup_all');
	}

	/**
	 * Builds entry objects on form
	 */
	private function buildEntryForm()
	{
		$allCatMsg	= $this->registry['db']->getAllCategoriesMessages();
		
		if (empty($allCatMsg)) {
			$_SESSION['system']['message']		= 'prerequisite';
			$_SESSION['system']['prerequisite']	= lang_no_message_create_first;
			$_SESSION['system']['action']		= 'create';
			
			header('location: /messages/error');
			exit;
		}
		
		foreach ($allCatMsg as $aCatMsg) {
			$aCategory[$aCatMsg['categoryId']]	= $aCatMsg['categoryName'];
			$aMsgTitle[$aCatMsg['messageId']]	= $aCatMsg['messageTitle'];
			$aMsgBody[$aCatMsg['messageId']]	= $aCatMsg['messageBody'];
			
			$aCatMsgId[$aCatMsg['categoryId']][]	= $aCatMsg['messageId'];
			$aCatMsgTitle[$aCatMsg['categoryId']][$aCatMsg['messageId']]	= $aCatMsg['messageTitle'];
		}
		
		$this->registry['template']->set('aCategory', $aCategory);
		$this->registry['template']->set('aMsgTitle', $aMsgTitle);
		$this->registry['template']->set('aMsgBody', $aMsgBody);
		$this->registry['template']->set('aCatMsgId', $aCatMsgId);
		$this->registry['template']->set('aCatMsgTitle', $aCatMsgTitle);
	}
	
	/**
	 * Builds FollowUp display in table format by calling helper functions.
	 */
	private function buildGrid()
	{
		$this->registry['template']->set('aRows', $this->registry['db']->getSchedulesMsgs(NULL, $this->animalId));
	}
	
	/**
	 * Called to display success message
	 */
	public function success()
	{
		$this->animalId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_followup_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_followup_updated));
			}
			
			unset($_SESSION['system']['message']);
		}
		
		$this->manage($this->animalId);
	}
	
	/**
	 * Ajax called this way:
	 *	/FollowUp/delete/25/1
	 *	Where: FollowUp = controller, delete = this function, 25 = FollowUpId to delete, 1 = messageId if custom, else 0
	 */
	public function delete()
	{
		$followUpId	= $this->registry['router']->getArg(ARGUMENT_1);
		$messageId	= $this->registry['router']->getArg(ARGUMENT_2);
		
		if ($messageId) {
			$this->registry['db']->deleteCustomMessage($messageId);
		}
		
		$n = $this->registry['db']->manualCancelFollowUp($followUpId);
		
		if ($n == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}
	
	/**
	 * Saves new category (if new entry) and saves FollowUp
	 * @param integer $followUpId
	 * @return integer
	 */
	private function updateFollowUp($followUpId)
	{
		if ($_POST['customMessageId']) {
			/**
			 * This means that this follow-up we are updating uses a custom message.
			 *	Now, we're going to delete the custom message attached to it before adding one if any.
			 */
			$this->registry['db']->deleteCustomMessage($_POST['customMessageId']);
		}
		
		return $this->saveFollowUp($followUpId);
	}

	/**
	 * Saves new follow up
	 * @param integer $updateThisId
	 * @return integer, boolean False on failure
	 */
	private function saveFollowUp($updateThisId=NULL)
	{
		$sendDate	= $this->registry['tool']->convertDateToDbFormat($_POST["dteSendDate"]);
		
		if ($_POST["isCustomMessage"]) {
			$isCustom	= 1;
			$messageId	= $this->registry['db']->addCustomMessage(
				$_POST["selCategory"],
				$_POST["selMessage"],
				$_POST["taMsgText"],
				$_POST["selChannelReceiver"]
			);
			
			if ($messageId == SQL_ERROR) {
				return false;
			}
		} else {
			$isCustom	= 0;
			$messageId	= $_POST['selMessage'];
		}
		
		if ($updateThisId) {
			$result		= $this->registry['db']->updateFollowUp(
				$updateThisId,
				$messageId,
				$this->animalId,
				$_POST["clientId"],
				$sendDate,
				$_POST["selChannelReceiver"],
				$isCustom
			);
		} else {
			$result		= $this->registry['db']->addFollowUp(
				$messageId,
				$this->animalId,
				$_POST["clientId"],
				$sendDate,
				$_POST["selChannelReceiver"],
				$isCustom
			);
		}
			
		if ($result == SQL_ERROR) {
			return false;
		}
		
		return $result;
	}
	
	/**
	 * Generate JSON data
	 * Ajax called to get a follow-up for update
	 */
	public function getJsonFollowUp()
	{
		$followUpId	= $this->registry['router']->getArg(ARGUMENT_1);
		$aFollowUp	= $this->registry['db']->getFollowUp($followUpId);
		echo json_encode($aFollowUp);
		exit;
	}
}

// eof
