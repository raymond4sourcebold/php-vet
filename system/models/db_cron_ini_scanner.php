<?php
/**
 * Model file for cron: INI Scanner.
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
 * Include superclass Model_Cron
 */
//~ require 'model_cron.php';
require site_path . 'system/models/db_cron_uploadini.php';

/**
 * Include Tool class
 */
require site_path . 'system/tool.php';

/**
 * Include INI reader
 */
require site_path . 'system/ini_reader.php';

/**
 * Class of database access for cron: Procedure Scanner.
 * @package    db_scanner_class
 */
//~ class Db extends Model_Cron
class Db extends Db_Cron_UploadIni
{
	/**
	 * Language as defined in INI
	 */
	protected $language;
	
	/**
	 * Raw Date Format as defined in INI
	 */
	protected $dateFormat;
	
	/**
	 * Path and name of uploaded file
	 */
	protected $uploadedFile;
	
	/**
	 * Subscriber ID
	 */
	protected $subscriberId;
	
	/**
	 * Animal ID
	 */
	protected $animalId;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Writes filename to INI log table
	 * @param string $filename INI Filename
	 * @return numeric Number of affected rows
	 */
	public function writeIniFilename($filename)
	{
		$sql	= "SELECT 1 FROM iniUpload WHERE filename = '{$filename}'";
		$result	= $this->fetchAll($sql);
		if (is_array($result) && count($result)) {
			return 0;
		}
		
		$sql	= "INSERT INTO iniUpload SET
				filename = ?,
				uploadTime = NOW()
			";
		$result	= $this->safeExecInsertWithParam($sql, array($filename), true);
		if ($result === SQL_ERROR) {
			return 0;
		}
		
		return $result;
	}
	
	
	/**
	 * This MAIN function calls all other functions on this class.
	 * @param string $filename
	 */
	public function processIniFile($filename)
	{
		$this->subscriberId	= $this->getSubscriberId($filename);
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'getSubscriberId() DONE' . $this->subscriberId) . ' >> /tmp/iniscan.log');
		if ($this->subscriberId == 0) {
			/**
			 * Subscriber ID does not exist.
			 */
			return 'SUBS_NOT_FOUND';
		}
		
		if ($this->moveToUploadFolder($filename) === false) {
			return 'INI_COPY_FAILED';
		}
		
		$result = $this->processUpload();
		
		if ($result) {
			return $result;
		} else {
			return 'DONE';
		}
	}
	
	/**
	 * Process uploaded INI file
	 */
	private function processUpload()
	{
		/**
		 * Writes the uploaded file to log/iscan.log.  Requested by Julien.
		 */
		exec('echo ... ' . $result . ': ' . $this->uploadedFile . ' >> ' . site_path . 'cron/log/iscan.log');
		
		$aRaw			= file($this->uploadedFile);
		
		if (!$aRaw) {
			return 'PROCESS STEP 1: file\(\) returned empty array.';
		}
		
		$aRaw			= $this->removeComments($aRaw);
		
		list($aCm, $aCustom)		= $this->separateCmAndCustom($aRaw);
		
		/**
		 * Handling for standard CM INI values
		 */
		$aCm			= $this->parseRawArray($aCm);
		
		if ($aCm['CLIENTCRITERIA']) {
			$this->removeParentheses($aCm['CLIENTCRITERIA']);
		}
		
		// Add sections using the v2.0.1 criteria categories
		$aCm			+= $this->getIniCriteria($aCm);
		$aCm			= $this->formatParsedArray($aCm);
		
		/**
		 * Handling for custom Subscriber-specific INI values
		 */
		list($aEvent, $aChange)		= $this->separateEventAndChange($aCustom);
		
		$aEvent				= $this->parseRawArray($aEvent, true);
		$aChange			= $this->parseRawArray($aChange, true);
		
		list($aCaption1, $aEvent)	= $this->customMultiStringToArray($aEvent);
		list($aCaption2, $aChange)	= $this->customMultiStringToArray($aChange);
		
		$aCaption	= array(
			'CAPTIONS'	=> $aCaption1 + $aCaption2
		);
		
		/**
		 * Save to database the INI file data.
		 */
		$this->saveCaptionsToDb($aCaption['CAPTIONS']);
		
		if ($this->saveIniToDb($aCm)) {
			/**
			 * Save subscriber custom data
			 */
			$this->saveCustomIniToDb($aEvent);
			$this->saveCustomIniToDb($aChange);
			
			//~ $this->success(lang_db_ini_update_ok . ": <b>" . $_FILES['frmUpload']['name'] . "</b>");
			
			//~ $uploadDetails		= $this->echoArray($aCm)
				//~ . '<br /><br />(Event)' . $this->echoArray($aEvent, true)
				//~ . '<br /><br />(Change)' . $this->echoArray($aChange, true)
				//~ . '<br /><br />' . $this->echoArray($aCaption);
			
			//~ $this->registry['template']->set('uploadDetails', $uploadDetails);
			
		//~ } else {
			//~ $this->error(lang_db_ini_update_error);
		}
	}
	
	/**
	 * Format it for display for testing purposes only.
	 * @param array $aData String on array to format for display
	 */
	private function echoArray($aData, $detailArray=false)
	{
		$details	= '';
		foreach($aData as $unit => $aRow) {
			$details	.= ($details ? '<br /><br />' : NULL) . $unit;
			foreach ($aRow as $key => $value) {
				$details	.= "<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
				
				if ($detailArray && is_array($value)) {
					$echoStr	= '';
					
					foreach ($value as $val) {
						$echoStr	.= $echoStr ? ' , ' : NULL;
						$echoStr	.= $val;
					}
					
					$value	= $echoStr;
				}
				$details	.= "`$key` = <span style='background-color: pink;'>$value</span>";
			}
		}
		return $details;
	}
	
	/**
	 * Sets associated Subscriber ID for this INI upload procedure
	 * @param array $file The INI file uploaded by the user
	 */
	private function getSubscriberId($file)
	{
		$nLen		= strlen($file) - 1;
		$start		= false;
		$subscriberId	= '';
		
		for ($x = $nLen; $x >= 0; $x--) {
			$char	= $file{$x};
			
			if ($char == ']') {
				if ($start == false) {
					$start		= true;
					continue;
				} else {
					// Here is double instance of ']'
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'subs id E 1') . ' >> /tmp/iniscan.log');
					return 0;
				}
			}
			
			if ($char == '[') {
				// Subscriber ID is complete
				break;
			}
			
			if ($start) {
				if (!is_numeric($char)) {
					// Subscriber ID is not numeric, this is not valid
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'subs id E 2') . ' >> /tmp/iniscan.log');
					return 0;
				}
				
				$subscriberId	= $char . $subscriberId;
			}
		}
		
		if ($subscriberId) {
			$sql	= "SELECT 1 FROM subscriber WHERE subscriberId = " . $subscriberId;
			
			$aRow	= $this->fetchAll($sql);
			
			if (is_array($aRow) && $aRow) {
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'subs id ' . $subscriberId) . ' >> /tmp/iniscan.log');
				return $subscriberId;
			}
		}
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'subs id E 3') . ' >> /tmp/iniscan.log');
		return 0;
	}
	
	/**
	 * Moves uploaded INI file to ini_uploads/
	 * @param array $file The INI file uploaded by the user
	 */
	private function moveToUploadFolder($file)
	{
		//~ $file	= str_replace(
				//~ array(' ', "'"), 
				//~ array('\ ', "\'"), 
				//~ $file);
				
		//~ $iniSource		= '/var/www/upload/' . $file;
				
		//~ $this->uploadedFile	= site_path . 'ini_uploads/' . $file;
		
		//~ $this->uploadedFile	= site_path . 'ini_uploads/' . date('Ymdhis') . '.INI';
		
		//~ exec('cp '
			//~ . str_replace(
				//~ array('{', '}', ' ', "'"), 
				//~ array('\{', '\}', '\ ', "\'"), 
				//~ $iniSource)
			//~ . ' ' 
			//~ . str_replace(
				//~ array('{', '}', ' ', "'"), 
				//~ array('\{', '\}', '\ ', "\'"), 
				//~ $this->uploadedFile)
		//~ );
		//~ return copy($iniSource, $this->uploadedFile);
		$iniSource		= site_path . 'ini_uploads/' . $file;
		$iniDoneIndicatorFile	= site_path . 'ini_uploads/' . str_replace('.INI', '.OK', $file);
		
		$loopCounterSafe	= 0;
		
		while (1) {
			//~ if (is_readable($iniSource) || $loopCounterSafe > 100) {
			if (file_exists($iniDoneIndicatorFile)) {
				$this->uploadedFile  = $iniSource;
				return true;
			}
			if ($loopCounterSafe > 100) {
				return false;
			}
			
			sleep(1);
			++$loopCounterSafe;
		}
	}
	
	/**
	 * Saves INI file data to database
	 * @param array $aData Data to save from the uploaded INI
	 * @return boolean
	 */
	private function saveIniToDb($aData)
	{
		/**
		 * SUBSCRIBER
		 */
		if (!isset($aData['SUBSCRIBER'])) {
			return false;
		}
		
		//~ if (!isset($aData['SUBSCRIBER']['subscriberId'])) {
			//~ $this->error('INI Error: `subscriberId` under [SUBSCRIBER] is required.');
			//~ return false;
		//~ }
		
		//~ $subscriberId	= $aData['SUBSCRIBER']['subscriberId'];
		
		//~ if ($subscriberId != $_SESSION['subscriberId']) {
			//~ $this->error('The `subscriberId` on the INI file does not belong to the logged subscriber.  Use ID: ' . $_SESSION['subscriberId']);
			//~ return false;
		//~ }
		$subscriberId	= $this->subscriberId;
		
		if (!isset($aData['CLIENT'])) {
			return false;
		}
		
		/**
		 * HONORARY (Owner)
		 */
		$honoraryId		= $this->processHonorary($aData['CLIENT']['clientTitle']);
		
		/**
		 * CLIENT (Owner)
		 */
		$nPreferredChannelId		= 0;
		$usePreferredExclusively	= 0;
		$nPriorityOnPrefCh		= 1;
		
		//~ if (isset($aData['CLIENT']['preferredChannel'])) {
			//~ $aPreferredChannel	= explode(',', $aData['CLIENT']['preferredChannel']);
			//~ // SMS, EMAIL, VOICE, FAX, LETTER
			//~ if (isset($aPreferredChannel[0])) {
				//~ $nPreferredChannelId		= $this->iniChannelToChannelId($aPreferredChannel[0]);
				//~ $usePreferredExclusively	= 1;
			//~ }
			//~ $nPriorityOnPrefCh	= isset($aPreferredChannel[1]) ? $aPreferredChannel[1] : 1;
		//~ }
		if (isset($aData['CLIENT']['preferredChannelType'])) {
			$nPreferredChannelId		= $this->iniChannelToChannelId($aData['CLIENT']['preferredChannelType']);
			$usePreferredExclusively	= 1;
		}
		if (isset($aData['CLIENT']['preferredChannelNumber'])) {
			$nPriorityOnPrefCh	= $aData['CLIENT']['preferredChannelNumber'];
		} else {
			$nPriorityOnPrefCh	= 1;
		}
		
		$clientId		= $this->processClient($subscriberId, $honoraryId, $nPreferredChannelId, $usePreferredExclusively, $aData['CLIENT']);
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'post CLIENT: ' . $clientId) . ' >> /tmp/iscan.log');
		if (!$clientId) {
			return false;
		}
		
		/**
		 * PHONE (Owner)
		 */
		$this->deleteClientPhones($clientId);
		
		if (isset($aData['CLIENT']['smsCapablePhone'])) {
			$this->processPhone($clientId, 'mobile', $nPriorityOnPrefCh, $aData['CLIENT']['smsCapablePhone']);
		}
		
		if (isset($aData['CLIENT']['voiceCapablePhone'])) {
			$this->processPhone($clientId, 'homeph', $nPriorityOnPrefCh, $aData['CLIENT']['voiceCapablePhone']);
		}
		
		if (isset($aData['CLIENT']['faxCapablePhone'])) {
			$this->processPhone($clientId, 'homefx', $nPriorityOnPrefCh, $aData['CLIENT']['faxCapablePhone']);
		}
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'pre SPECIE') . ' >> /tmp/iscan.log');
		/**
		 * NARYCRITERIA: specie, gender
		 */
		if (!isset($aData['ANIMALCRITERIA'])) {
			return false;
		}
		// specie
		if (!isset($aData['ANIMALCRITERIA']['specie'])) {
			return false;
		}
		$specieId		= $this->processSpecie($aData['ANIMALCRITERIA']['specie']);
//~ exec('echo ' . str_replace(array("\n", '(', ')'), array('', '\(', '\)'), 'post SPECIE') . ' >> /tmp/iscan.log');
		// gender
		if (!isset($aData['ANIMALCRITERIA']['gender'])) {
			return false;
		}
		$genderId		= $this->processGender($aData['ANIMALCRITERIA']['gender']);
		
		/**
		 * ANIMAL
		 */
		if ($specieId == false || $genderId == false || !isset($aData['ANIMAL'])) {
			return false;
		}
		$animalId		= $this->processAnimal($clientId, $specieId, $genderId, $aData['ANIMAL']);
		
		if (!$animalId) {
			return false;
		}
		
		/**
		 * Assign Animal ID to class-wide variable.
		 */
		$this->animalId		= $animalId;
		
		/**
		 * Clear all conditions before saving the conditions anew.
		 */
		$this->deleteAllCritValues($animalId);
		
		/**
		 * BOOLEANS
		 */
		if (isset($aData['BOOLEANS'])) {
			$this->processCondBooleans($animalId, $aData['BOOLEANS']);
		}
		
		/**
		 * QUANTITIES
		 */
		$this->processCondQuantities($animalId, $aData['QUANTITIES']);
		
		/**
		 * MEDICALACTS, VACCINES (Dates)
		 */
		//~ $this->processCondDates($animalId, $aData['MEDICALACTS'], 'MEDICALACTS');
		//~ $this->processCondDates($animalId, $aData['VACCINES'], 'VACCINES');
		if (isset($aData['DATES'])) {
			$this->processCondDates($animalId, $aData['DATES'], '');
		}

		/**
		 * APPOINTMENTS (Multiple Dates)
		 */
		if (isset($aData['APPOINTMENTS']['nextAppointment'])) {
			$this->processCondAppointments($clientId, $animalId, $aData['APPOINTMENTS']['nextAppointment']);
		}
		
		/**
		 * EVENTS, DIAGNOSTICS (Boolean and Date)
		 */
		//~ $this->processCondEvents($animalId, $aData['EVENTS']);
		//~ $this->processCondEvents($animalId, $aData['DIAGNOSTICS']);
		
		/**
		 * Call Proc Scanner for this Animal.
		 */
		Tool::asynchronousCall(site_path . 'cron/animal_scanner.php ' . $animalId);
		
		return true;
	}
	
	/**
	 * Parses the raw INI array into usable data.
	 * @param array $aInput The contents of the INI file on array
	 */
	private function parseRawArray($aInput, $isCustom=false)
	{
		$parser		= new ini_reader();
		
		if ($isCustom) {
			$aIni	= $parser->loadCustomArray($aInput);
		} else {
			$aIni	= $parser->loadArray($aInput);
		}
		
		if (isset($aIni['PMS']['dateFormat'])) {
			$this->dateFormat	= $aIni['PMS']['dateFormat'];
		}
		if (isset($aIni['PMS']['language'])) {
			if ($aIni['PMS']['language'] == 'FRENCH') {
				$this->language		= 'fr';
			} else {
				$this->language		= 'en'; // English by default
			}
		}
		
		return $aIni;
	}
	
	/**
	 * Formats the raw INI array into usable data.
	 * @param array $aInput The contents of the INI file on array
	 */
	private function formatParsedArray($aInput)
	{
		$aData		= array();
		
		foreach($aInput as $unit => $aRow) {
			//~ if ($unit == 'MEDICALACTS' || $unit == 'VACCINES') {
			if ($unit == 'DATES') {
				$aData[$unit]	= $this->convertFromRawDate($aRow);
				
			} elseif ($unit == 'APPOINTMENTS') {
				$aData[$unit]	= $this->convertFromRawAppointment($aRow);
				
			//~ } elseif ($unit == 'CLIENT') {
				//~ $aData[$unit]	= $this->convertFromRawClient($aRow);
				
			//~ } elseif ($unit == 'EVENTS' || $unit == 'DIAGNOSTICS') {
				//~ $aData[$unit]	= $this->convertFromRawEvents($aRow);
				
			} elseif ($unit == 'CLIENT') {
				if (isset($aRow['smsCapablePhone']) && is_numeric($aRow['smsCapablePhone'])) {
					$aRow['smsCapablePhone']	= "0{$aRow['smsCapablePhone']}";
					
				} elseif (isset($aRow['voiceCapablePhone']) && is_numeric($aRow['voiceCapablePhone'])) {
					$aRow['voiceCapablePhone']	= "0{$aRow['voiceCapablePhone']}";
					
				} elseif (isset($aRow['faxCapablePhone']) && is_numeric($aRow['faxCapablePhone'])) {
					$aRow['faxCapablePhone']	= "0{$aRow['faxCapablePhone']}";
				}
				
				$aData[$unit]	= $aRow;
				
			} else {
				$aData[$unit]	= $aRow;
			}
		}
		
		return $aData;
	}
	
	/**
	 * Convert the raw INI data into usable data.
	 * @param string $file_contents The contents of the INI file as-is
	 */
	private function convertRawData($file_contents)
	{
		$aData		= array();
		
		$parser		= new ini_reader($file_contents);
		$aIni		= $parser->loadString($file_contents);
		
		$this->dateFormat	= $aIni['PMS']['dateFormat'];
		
		foreach($aIni as $unit => $aRow) {
			if ($unit == 'MEDICALACTS' || $unit == 'VACCINES') {
				$aData[$unit]	= $this->convertFromRawDate($aRow);
				
			} elseif ($unit == 'APPOINTMENTS') {
				$aData[$unit]	= $this->convertFromRawAppointment($aRow);
				
			} elseif ($unit == 'CLIENT') {
				$aData[$unit]	= $this->convertFromRawClient($aRow);
				
			} elseif ($unit == 'EVENTS' || $unit == 'DIAGNOSTICS') {
				$aData[$unit]	= $this->convertFromRawEvents($aRow);
				
			} else {
				$aData[$unit]	= $aRow;
			}
		}
		
		return $aData;
	}
	
	/**
	 * Convert the raw Events INI data into usable data.
	 * @param array $aRow Events data to convert
	 */
	private function convertFromRawEvents($aRow)
	{
		$aOutput		= array();
		
		foreach ($aRow as $key => $value) {
			$aBoolDate	= explode('","', $value);
			
			if (isset($aBoolDate[0]) && isset($aBoolDate[1])) {
				$aOutput[$key]		= $aBoolDate[0] . ',' . $this->formatRawDate($aBoolDate[1]);
			} else {
				if (isset($aBoolDate[0])) {
					$tmp	= strtoupper($aBoolDate[0]);
					
					if ($tmp == 'TRUE",' || $tmp == 'FALSE",') {
						$aOutput[$key]		= str_replace('"', '', $tmp);
						
					} elseif ($tmp == 'TRUE' || $tmp == 'FALSE') {
						// Supplied part is the boolean
						$aOutput[$key]		= $tmp . ',';
					} else {
						// Supplied part is the date
						$aOutput[$key]		= ',' . $this->formatRawDate($aBoolDate[0]);
					}
				}
			}
		}
		
		return $aOutput;
	}
	
	/**
	 * Convert the raw Client INI data into usable data.
	 * @param array $aRow Client data to convert
	 */
	private function convertFromRawClient($aRow)
	{
		$aOutput		= array();
		
		foreach ($aRow as $key => $value) {
			if ($key == 'smsCapablePhone'
			 || $key == 'voiceCapablePhone'
			 || $key == 'faxCapablePhone'
			 || $key == 'emailAddress'
			 || $key == 'preferredChannel') {
				$aOutput[$key]	= str_replace('","', ',', $value);
				
			} else {
				$aOutput[$key]	= $value;
			}
		}
		
		return $aOutput;
	}
	
	/**
	 * Convert the raw Appointment INI data into usable data.
	 * This formats only the date portion since the time portion is already formatted.
	 *	Sample input: 2010200811:30
	 *		      DDMMYYYYHH:MM
	 * @param array $aRow Appointment data to convert
	 */
	private function convertFromRawAppointment($aRow)
	{
		$aOutput		= array();
		
		// Foreach loop but we only expect one line under [APPOINTMENTS].
		foreach ($aRow as $nextApp => $dataTimeCsv) {
			if ($nextApp != 'nextAppointment') {
				$aOutput[$nextApp]	= $dataTimeCsv; // Invalid key, just return the data as-is.
				continue;
			}
			
			//~ $aDataAndTime	= explode('","', $dataTimeCsv);
			$aDataAndTime		= explode(' ; ', $dataTimeCsv);
			
			$oneLineOutput		= '';
			
			foreach ($aDataAndTime as $value) {
				
				$strTime	= substr($value, -4);
				$strDate	= str_replace($strTime, '', $value);
				
				$oneLineOutput		.= $oneLineOutput ? ',' : NULL;
				if (is_numeric($strDate)) {
					// This is a date variable.
					$oneLineOutput		.= $this->formatRawDate($strDate) . ' ' 
						. substr($strTime, 0, 2)
						. ':'
						. substr($strTime, 2);
					
				} else {
					$oneLineOutput		.= $value; // return as-is
				}
			}
			
			$aOutput[$nextApp]	= $oneLineOutput;
		}
		
		return $aOutput;
	}
	
	/**
	 * Convert the raw Date INI data into usable data.
	 * Formats date value of MEDICALACTS and VACCINES
	 *	It is assumed that all date passed are of date type.
	 * @param array $aRow Date data to convert
	 */
	private function convertFromRawDate($aRow)
	{
		$aOutput	= array();
		
		foreach ($aRow as $key => $value) {
			if (is_numeric($value)) {
				/**
				 * We assume that this is a date variable.
				 */
				$aOutput[$key]		= $this->formatRawDate($value);
			} else {
				$aOutput[$key]		= $value;
			}
		}
		
		return $aOutput;
	}
	
	/**
	 * Converts DDMMYYYY or MMDDYYYY to YYYY-MM-DD
	 * When format of input is not MMDDYYYY, DDMMYYYY is assumed.
	 * @param string $strDate Raw string date
	 * @return string Date in string format
	 */
	private function formatRawDate($strDate)
	{
		if (strlen($strDate) == 7) {
			// Only 1 digit for MM, let's pad one zero.
			$strDate	= '0' . $strDate;
		}
		
		if ($this->dateFormat == 'MMDDYYYY') {
			$month		= substr($strDate, 0, 2);
			$day		= substr($strDate, -6, 2);
			
		} else {
			$day		= substr($strDate, 0, 2);
			$month		= substr($strDate, -6, 2);
		}
		
		return substr($strDate, -4) . "-$month-$day";
	}
	
	/**
	 * Remove comments
	 * @param array $aRaw
	 * @return array
	 */
	private function removeComments($aRaw)
	{
		$aRet	= array();
		
		foreach ($aRaw as $line) {
			$noCommentLine		= $this->removeLineComment($line);
			
			if ($noCommentLine) {
				if ($noCommentLine == '[ENDOFFILE]') {
					/**
					 * Get out of loop when [ENDOFFILE] is encountered.
					 */
					break;
				}
				
				$aRet[]		= $noCommentLine;
			}
		}
		
		return $aRet;
	}
	
	/**
	 * Removes comment on line
	 * @param string $line
	 * @return string
	 */
	private function removeLineComment($line)
	{
		$outLine	= '';
		
		$line		= trim($line);
		$nLen		= strlen($line);
		$outsideQuote	= true;
		
		for ($x = 0; $x < $nLen; $x++) {
			$char	= $line{$x};
			
			if ($char == '"') {
				$outsideQuote 	= !$outsideQuote;
				
			} elseif ($char == '#' && $outsideQuote) {
				break;
			}
			
			$outLine	.= $char;
		}
		
		return trim($outLine);
	}
	
	/**
	 * Separate INI array into CM and custom data.
	 * @param array $aRaw
	 * @return array
	 */
	private function separateCmAndCustom($aRaw)
	{
		$aCm		= array();
		$aCustom	= array();
		
		$isCm		= false;
		
		foreach ($aRaw as $line) {
			if ($line{0} == '[' && $line{(strlen($line) - 1)} == ']') {
				/**
				 * Section header line
				 */
				if (substr($line, -4) == '.CM]') {
					$aCm[]		= str_replace('.CM', '', $line);
					
					$isCm		= true;
				} else {
					$aCm[]		= $line;
					$aCustom[]	= $line;
					
					$isCm		= false;
				}
			} else {
				/**
				 * Data line
				 */
				if (strpos($line, '=') === false) {
					/**
					 * Data lines with no equal sign is ignored.
					 */
					continue;
				}
				
				list($key, $value)	= explode('=', $line);
				$key		= trim($key);
				
				if (substr($key, -3) == '.CM') {
					$aCm[]		= str_replace('.CM', '', $key) . ' = ' . $value;
				} elseif ($isCm) {
					$aCm[]		= $line;
				} else {
					$aCustom[]	= $line;
				}
			}
		}
		
		return array($aCm, $aCustom);
	}
	
	/**
	 * Removes string inside of parentheses
	 * @param array $aClientCrit
	 */
	private function removeParentheses(& $aClientCrit)
	{
		foreach ($aClientCrit as $key => $value) {
			$nPos			= strpos($value, ')');
			
			if ($nPos === false) {
				continue;
			}
			
			$aClientCrit[$key]	= substr($value, $nPos + 1);
		}
	}
	
	/**
	 * Get boolean values
	 * @param array $aCm
	 * @return array
	 */
	private function getIniCriteria($aCm)
	{
		//~ EVENTS
		
		$retArr		= array();
		
		/**
		 * From ANIMALCRITERIA
		 */
		if (isset($aCm['ANIMALCRITERIA'])) {
			foreach ($aCm['ANIMALCRITERIA'] as $key => $value) {
				
				$key		= trim($key);
				$value		= trim($value);
				
				if ($key == 'birthDate' || $key == 'deathDate' || $key == 'lastVisitDate') {
					$retArr['DATES'][$key]			= $value;
					
				} elseif ($key == 'nextAppointments') {
					$retArr['APPOINTMENTS']['nextAppointment']	= $value;
					
				} elseif ($value == 'TRUE' || $value == 'FALSE') {
					$retArr['BOOLEANS'][$key]		= $value;
					
				} elseif (is_numeric($value)) {
					$retArr['QUANTITIES'][$key]		= $value;
				}
			}
		}
		
		/**
		 * From CLIENTCRITERIA
		 */
		if (isset($aCm['CLIENTCRITERIA'])) {
			foreach ($aCm['CLIENTCRITERIA'] as $key => $value) {
				
				$key		= trim($key);
				$value		= trim($value);
				
				if ($value == 'TRUE' || $value == 'FALSE') {
					$retArr['BOOLEANS'][$key]		= $value;
					
				} elseif (is_numeric($value)) {
					$retArr['QUANTITIES'][$key]		= $value;
				}
			}
		}
		
		return $retArr;
	}
	
	/**
	 * Separate caption and value from INI lines
	 * @param array $aCustom
	 * @return array $retArr
	 */
	private function customMultiStringToArray($aCustom)
	{
		$aCaption		= array();
		$aValues		= array();
		
		$outValueHeader		= '';
		
		foreach ($aCustom as $sectionHeader => $aRows) {
			
			if ($outValueHeader != $sectionHeader) {
				$outValueHeader		= $sectionHeader;
			}
			
			foreach ($aRows as $code => $allTheLineValue) {
				//~ if (strpos($allTheLineValue, '<sep;>')) {
					//~ $aRealColonSepVal	= explode('<sep;>', $allTheLineValue);
				//~ }
				$aRealColonSepVal	= explode('<sep;>', $allTheLineValue);
				
				foreach ($aRealColonSepVal as $colonSepValues) {
					$aHold			= $this->csvIniValuesToArray($colonSepValues);
					
					/**
					 * First item is the caption, separate it.
					 */
					$aCaption[$code]	= trim(array_shift($aHold), ' "');
					
					/**
					 * Remaining items are all values
					 */
					if (!isset($aValues[$outValueHeader])) {
						$aValues[$outValueHeader]		= array(); // initialize
					}
					if (!isset($aValues[$outValueHeader][$code])) {
						$aValues[$outValueHeader][$code]	= array(); // initialize
					}
					
					$aValues[$outValueHeader][$code][]	= $aHold;
				}
			}
		}
		
		/**
		 * Remove duplicates on caption array
		 */
		$aRetCaption		= array_unique($aCaption);
		
		/**
		 * Remove duplicates and use latest value
		 */
		$aRetValues		= array();
		
		foreach ($aValues as $sectionHeader => $aSection) {
			foreach ($aSection as $code => $array_values) {
				foreach ($array_values as $one_array_value) {
					if (!isset($aRetValues[$sectionHeader])) {
						$aRetValues[$sectionHeader]		= array(); // initialize
					}
					if (!isset($aRetValues[$sectionHeader][$code])) {
						$aRetValues[$sectionHeader][$code]	= $one_array_value;
					} else {
						// Determine which is latest value here
						$aRetValues[$sectionHeader][$code]	= $this->getNewerValueBetween($aRetValues[$sectionHeader][$code], $one_array_value);
					}
				}
			}
		}
		
		/**
		 * Move single values outside of their array
		 */
		foreach ($aRetValues as $sectionHeader => $aSection) {
			foreach ($aSection as $code => $value) {
				if (count($value) == 1) {
					$aRetValues[$sectionHeader][$code]	= $value[0];
				} else {
					$aRetValues[$sectionHeader][$code]	= $value;
				}
			}
		}
		
		return array($aRetCaption, $aRetValues);
	}
	
	/**
	 * Separates Event from Change from Subscriber custom entries on INI file
	 * @param array $aCustom Custom data from INI file
	 * @return array
	 */
	private function separateEventAndChange($aCustom)
	{
		$aRetEvent		= array();
		$aRetChange		= array();
		
		$sectionHeader		= '';
		
		foreach ($aCustom as $value) {
			if (strpos($value, '=')) {
				list($assigned, $assignValue)	= explode('=', $value);
				$assignValue			= ltrim($assignValue);
				
				if (substr($assignValue, 0, 5) == 'EVENT') {
					$tmp			= str_replace('EVENT ;', 'EVENT;', $value);
					$aRetEvent[$sectionHeader][]		= str_replace('EVENT;', '', $tmp);
					
				} elseif (substr($assignValue, 0, 6) == 'CHANGE') {
					$tmp			= str_replace('CHANGE ;', 'CHANGE;', $value);
					$aRetChange[$sectionHeader][]		= str_replace('CHANGE;', '', $tmp);
				}
			} else {
				$sectionHeader			= $value;
			}
		}
		
		return array($aRetEvent, $aRetChange);
	}
	
	/**
	 * Saves subscriber-specific captions to database
	 * @param array $aCaptions
	 * @return boolean
	 */
	private function saveCaptionsToDb($aCaptions)
	{
		foreach ($aCaptions as $code => $caption) {
			$result		= $this->saveSubscriberCaption($code, $caption, $this->language);
			
			if ($result == SQL_ERROR) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Saves subscriber-specific criteria to database
	 * @param array $aCustom
	 */
	private function saveCustomIniToDb($aCustom)
	{
		$aToDbBoolean		= array();
		$aToDbDate		= array();
		$aToDbEvent		= array();
		
		foreach ($aCustom as $aRows) {
			foreach ($aRows as $code => $value) {
				if (is_array($value)) {
					/**
					 * Data pair, could be an Event
					 */
					if ($value[0] == 'TRUE' || $value[0] == 'FALSE') {
						
						$valBoolean		= $value[0];
						$valDate		= $value[1];
						
						if ($valDate) {
							/**
							 * We have a pair of Boolean and Date data, process this as Event
							 */
							$aToDbEvent[$code]	= $valBoolean . ',' . $valDate; // Make it a CSV.
						} else {
							/**
							 * Boolean only but no Date, process this as Boolean
							 */
							$aToDbBoolean[$code]	= $valBoolean;
						}
					} else {
						// DISREGARD, in an array, the boolean part should always be the first element
						continue;
					}
				} else {
					/**
					 * Single data only
					 */
					if ($value == 'TRUE' || $value == 'FALSE') {
						$aToDbBoolean[$code]	= $value;
					} else {
						// Assume value to be date
						$aToDbDate[$code]	= $value;
					}
				}
			}
		}
		
		if ($aToDbBoolean) {
			$this->processCondBooleans($this->animalId, $aToDbBoolean);
		}
		if ($aToDbDate) {
			$this->processCondDates($this->animalId, $aToDbDate);
		}
		if ($aToDbEvent) {
			$this->processCondEvents($this->animalId, $aToDbEvent);
		}
	}
	
	/**
	 * Compares two dates and returns the latest date
	 * @param string $strDate1
	 * @param string $strDate2
	 * @return string date
	 */
	private function assignDateIfLater($strDate1, $strDate2)
	{
		if (strtotime($strDate2) > strtotime($strDate1)) {
			return $strDate2;
		}
		
		return $strDate1;
	}
	
	private function csvIniValuesToArray($colonSepValues)
	{
		$aVal		= explode(';', $colonSepValues);
		
		$retArr		= array();
		
		//~ $loopCount	= 0;
		
		foreach ($aVal as $k => $v) {
			/**
			 * Trim values
			 */
			$holdVal	= trim($v, ' "');
			
			if (is_numeric($holdVal)) {
				$holdVal	= $this->formatRawDate($holdVal);
			}
			
			$retArr[$k]	= $holdVal;
			
			/**
			 * Increment and check.  Process first two array items only
			 */
			//~ ++$loopCount;
			//~ if ($loopCount >= 2) {
				//~ break;
			//~ }
		}
		
		return $retArr;
	}
	
	private function getNewerValueBetween($aOld, $aNew)
	{
		foreach ($aOld as $key => $value) {
			if (Tool::isYyyyMmDdDashSeparated($value)) {
				if (strtotime($aNew[$key]) > strtotime($aOld[$key])) {
					return $aNew;
				} else {
					return $aOld;
				}
			}
		}
		
		// By default, the later entry on INI is newer than the earlier entry of the same code.
		return $aNew;
	}
}

// eof
