<?php
/**
 * Controller file for Messages page.
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
 * Controller for Messages page.
 * @package    messages
 */
Class Controller_Messages Extends Controller_Base
{
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_POST) {
			if ($_POST['updateMessageId']) {
				if ($this->updateMessageEntry($_POST['updateMessageId'])) {
					$_SESSION['system']['message']		= 'updated';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /messages/success');
					exit;
				}
			} else {
				if ($this->saveMessageEntry()) {
					$_SESSION['system']['message']		= 'saved';
					// The purpose of this redirection is to get rid of the POST.
					header('location: /messages/success');
					exit;
				}
			}
			header('location: /messages/error');
			exit;
		}
		$this->buildMessageEntryForm();
		$this->buildMessageGrid();
		
		$aDateCriteria	= $this->registry['db']->getDateCriteria();
		$this->registry['template']->set('aDateCriteria', $aDateCriteria);
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
	 * Called to display message and call this controller's default function
	 */
	public function success()
	{
		if (isset($_SESSION['system']['message'])) {
			if ($_SESSION['system']['message'] == 'saved') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_message_saved));
			} elseif ($_SESSION['system']['message'] == 'updated') {
				$this->registry['template']->set('autoHideTitle', lang_success);
				$this->registry['template']->set('autoHideMessage', addslashes(lang_message_updated));
			}
			
			unset($_SESSION['system']['message']); // reset
		}
		
		$this->index();
	}
	
	/**
	 * Ajax called this way:
	 *	/messages/delete/1
	 *	Where: messages = controller, delete = this function, 1 = messageId to delete
	 */
	public function delete()
	{
		$messageId	= $this->registry['router']->getArg(ARGUMENT_1);
		
		if ($this->registry['db']->deleteMessage($messageId) == 1) {
			echo 'SUCCESS';
		} else {
			echo 'ERROR';
		}
	}
	
	/**
	 * Ajax called this way:
	 *	/messages/fetch2js/jsFunction/msgId
	 */
	public function fetch2js()
	{
		$jsFcnReceiver	= $this->registry['router']->getArg(ARGUMENT_1);
		$msgId		= $this->registry['router']->getArg(ARGUMENT_2);
		
		$aRows		= $this->registry['db']->getMessages($msgId);
		
		foreach ($aRows as $row) {
			// Get one row only, then break.
			break;
		}
		
		$body	= str_replace("\r\n", chr(27), $row['messageBody']);
		$body	= addslashes($body);
		
		echo "fetch2js_$jsFcnReceiver(
			'{$row['messageCategoryId']}',
			'{$row['messageTitle']}',
			'{$row['messageChannel']}',
			'{$body}',
			{$row['isPractice']}
		);";
	}
	
	/**
	 * Saves new category (if new entry) and saves message
	 * @return integer
	 */
	private function saveMessageEntry()
	{
		if ($_POST["newtype"]) {
			// This is a new category
			$categoryId	= $this->registry['db']->saveCategory($_POST["newtype"]);
		} else {
			$categoryId	= $this->registry['db']->saveCategory($_POST["selMsgCategory"]);
		}
		
		if ($categoryId == 0) {
			return false;
		}
		/**
		 * As per Michel, use multimedia channel for now.
		 *	Let's use channel = 1.
		 */
		//~ return $this->registry['db']->saveMessage($categoryId, $_POST["textedescription"], $_POST["textemessage"], $_POST["radioChannel"], isset($_POST["chkActive"]));
		return $this->registry['db']->saveMessage($categoryId, $_POST["textedescription"], $_POST["textemessage"], 1, (isset($_POST["chkActive"]) ? 1 : 0));
	}
	
	/**
	 * Saves new category (if new entry) and saves message
	 * @return integer | boolean False on failure
	 */
	private function updateMessageEntry($updateMsgId)
	{
		if ($_POST["newtype"]) {
			// This is a new category
			$categoryId	= $this->registry['db']->saveCategory($_POST["newtype"]);
		} else {
			$categoryId	= $this->registry['db']->saveCategory($_POST["selMsgCategory"]);
		}
		
		if ($categoryId == 0) {
			return false;
		}
		/**
		 * As per Michel, use multimedia channel for now.
		 *	Let's use channel = 1.
		 */
		//~ return $this->registry['db']->updateMessage($updateMsgId, $categoryId, $_POST["textedescription"], $_POST["textemessage"], $_POST["radioChannel"], isset($_POST["chkActive"]));
		return $this->registry['db']->updateMessage($updateMsgId, $categoryId, $_POST["textedescription"], $_POST["textemessage"], 1, isset($_POST["chkActive"]));
	}
	
	/**
	 * Builds entry objects on form
	 */
	private function buildMessageEntryForm()
	{
		$this->registry['template']->set('javascript', array('jquery/jquery.tablesorter'));
		$this->registry['template']->set('css', array('styleG'));
		
		// Get category selection array
		$aMsgCat	= $this->registry['db']->getMessageCategories();
		$this->registry['template']->set('aMsgCat', $aMsgCat);
		
		// Get radio channel array
		$aTmp		= $this->registry['db']->getMessageChannels('radioChannel');
		$aChannel	= array();
		foreach ($aTmp as $strChannel) {
			$aChannel[$strChannel]		= $strChannel;
		}
		$this->registry['template']->set('aChannel', $aChannel);
		
		// Get message variables array
		$aMsgVar	= $this->registry['db']->getMessageVariables();
		$this->registry['template']->set('aMsgVar', $aMsgVar);
	}
	
	/**
	 * Builds Messages display in table format by calling helper functions.
	 */
	private function buildMessageGrid()
	{
		$aMsgs		= $this->registry['db']->getMessages();
		$this->registry['template']->set('aMessages', $aMsgs);
	}
}

// eof
