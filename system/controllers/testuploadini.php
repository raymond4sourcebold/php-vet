<?php
/**
 * Controller file for INI Upload page.
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
 * Controller for INI Upload page.
 * @package    upload_ini
 */
class Controller_TestUploadINI Extends Controller_Base
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
	 * Animal ID
	 */
	protected $animalId;
	
	/**
	 * Default function to execute for this controller.
	 */
	public function index()
	{
		if ($_FILES) {
			if ($this->moveToUploadFolder($_FILES['frmUpload'])) {
				$this->processUpload();
				$_SESSION['settings']		= $this->registry['db']->getCriteriaCaptionSettings();
			} else {
				$this->error(lang_upload_file_error);
			}
		}
		
		$this->registry['template']->set('css', array('styleG'));
		$this->registry['template']->show($this->templateName);
	}
	
	/**
	 * Process uploaded INI file
	 */
	private function processUpload()
	{
		$aRaw			= $this->removeComments(file($this->uploadedFile));
		
		list($aCm, $aCustom)		= $this->separateCmAndCustom($aRaw);
		
		/**
		 * Handling for standard CM INI values
		 */
		$aCm			= $this->parseRawArray($aCm);
		
		if (isset($aCm['CLIENTCRITERIA']) && $aCm['CLIENTCRITERIA']) {
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
		$resultCm	= $this->saveIniToDb($aCm);
		
		if ($resultCm) {
			/**
			 * Save subscriber custom data
			 */
			$this->saveCustomIniToDb($aEvent);
			$this->saveCustomIniToDb($aChange);
			
			$this->success(lang_db_ini_update_ok . ": <b>" . $_FILES['frmUpload']['name'] . "</b>");
			
			$uploadDetails		= $this->echoArray($aCm)
				. '<br /><br />(Event)' . $this->echoArray($aEvent, true)
				. '<br /><br />(Change)' . $this->echoArray($aChange, true)
				. '<br /><br />' . $this->echoArray($aCaption);
			
			$this->registry['template']->set('uploadDetails', $uploadDetails);
			
		} else {
			$this->error(lang_db_ini_update_error);
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
	 * Moves uploaded INI file to ini_uploads/
	 * @param array $file The INI file uploaded by the user
	 */
	private function moveToUploadFolder($file)
	{
		$upFile			= basename($file['name']);
		$this->uploadedFile	= "ini_uploads/" . str_replace('.ini', '', $upFile) . '_' . time() . '.ini';
		
		return move_uploaded_file($file['tmp_name'], $this->uploadedFile);
	}
	
	/**
	 * Saves INI file data to database
	 * @param array $aData Data to save from the uploaded INI
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
		$subscriberId	= $_SESSION['subscriberId'];
		
		if (!isset($aData['CLIENT'])) {
			return false;
		}
		
		/**
		 * HONORARY (Owner)
		 */
		$honoraryId		= $this->registry['db']->processHonorary($aData['CLIENT']['clientTitle']);
		
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
				//~ $nPreferredChannelId		= $this->registry['db']->iniChannelToChannelId($aPreferredChannel[0]);
				//~ $usePreferredExclusively	= 1;
			//~ }
			//~ $nPriorityOnPrefCh	= isset($aPreferredChannel[1]) ? $aPreferredChannel[1] : 1;
		//~ }
		if (isset($aData['CLIENT']['preferredChannelType']) && $aData['CLIENT']['preferredChannelType']) {
			$nPreferredChannelId		= $this->registry['db']->iniChannelToChannelId($aData['CLIENT']['preferredChannelType']);
			$usePreferredExclusively	= 1;
		}
		if (isset($aData['CLIENT']['preferredChannelNumber']) && $aData['CLIENT']['preferredChannelNumber']) {
			$nPriorityOnPrefCh	= $aData['CLIENT']['preferredChannelNumber'];
		} else {
			$nPriorityOnPrefCh	= 1;
		}
		
		$clientId		= $this->registry['db']->processClient($subscriberId, $honoraryId, $nPreferredChannelId, $usePreferredExclusively, $aData['CLIENT']);
		
		if (!$clientId) {
			return false;
		}
		
		/**
		 * PHONE (Owner)
		 */
		$this->registry['db']->deleteClientPhones($clientId);
		
		if (isset($aData['CLIENT']['smsCapablePhone'])) {
			$this->registry['db']->processPhone($clientId, 'mobile', $nPriorityOnPrefCh, $aData['CLIENT']['smsCapablePhone']);
		}
		
		if (isset($aData['CLIENT']['voiceCapablePhone'])) {
			$this->registry['db']->processPhone($clientId, 'homeph', $nPriorityOnPrefCh, $aData['CLIENT']['voiceCapablePhone']);
		}
		
		if (isset($aData['CLIENT']['faxCapablePhone'])) {
			$this->registry['db']->processPhone($clientId, 'homefx', $nPriorityOnPrefCh, $aData['CLIENT']['faxCapablePhone']);
		}
		
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
		$specieId		= $this->registry['db']->processSpecie($aData['ANIMALCRITERIA']['specie']);

		// gender
		if (!isset($aData['ANIMALCRITERIA']['gender'])) {
			return false;
		}
		$genderId		= $this->registry['db']->processGender($aData['ANIMALCRITERIA']['gender']);
		
		/**
		 * ANIMAL
		 */
		if ($specieId == false || $genderId == false || !isset($aData['ANIMAL'])) {
			return false;
		}
		$animalId		= $this->registry['db']->processAnimal($clientId, $specieId, $genderId, $aData['ANIMAL']);
		
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
		$this->registry['db']->deleteAllCritValues($animalId);
		
		/**
		 * BOOLEANS
		 */
		if (isset($aData['BOOLEANS'])) {
			$this->registry['db']->processCondBooleans($animalId, $aData['BOOLEANS']);
		}
		
		/**
		 * QUANTITIES
		 */
		if (isset($aData['QUANTITIES']) && $aData['QUANTITIES']) {
			$this->registry['db']->processCondQuantities($animalId, $aData['QUANTITIES']);
		}
		
		/**
		 * MEDICALACTS, VACCINES (Dates)
		 */
		//~ $this->registry['db']->processCondDates($animalId, $aData['MEDICALACTS'], 'MEDICALACTS');
		//~ $this->registry['db']->processCondDates($animalId, $aData['VACCINES'], 'VACCINES');
		if (isset($aData['DATES'])) {
			$this->registry['db']->processCondDates($animalId, $aData['DATES'], '');
		}

		/**
		 * APPOINTMENTS (Multiple Dates)
		 */
		if (isset($aData['APPOINTMENTS']['nextAppointment'])) {
			$this->registry['db']->processCondAppointments($clientId, $animalId, $aData['APPOINTMENTS']['nextAppointment']);
		}
		
		/**
		 * EVENTS, DIAGNOSTICS (Boolean and Date)
		 */
		//~ $this->registry['db']->processCondEvents($animalId, $aData['EVENTS']);
		//~ $this->registry['db']->processCondEvents($animalId, $aData['DIAGNOSTICS']);
		
		/**
		 * Call Proc Scanner for this Animal.
		 */
		$this->registry['tool']->asynchronousCall('cron/animal_scanner.php ' . $animalId);
		
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
	 * Shows an autohide message
	 * @param string $msg Message to show
	 */
	public function success($msg)
	{
		$this->registry['template']->set('autoHideTitle', lang_success);
		$this->registry['template']->set('autoHideMessage', addslashes($msg));
	}
	
	/**
	 * Shows an autohide message
	 * @param string $msg Message to show
	 */
	public function error($msg)
	{
		static $isErrorSet;
		
		if ($isErrorSet === true) {
			return;
		}
		
		$this->registry['template']->set('autoHideTitle', lang_failure);
		$this->registry['template']->set('autoHideMessage', addslashes($msg));
		
		$isErrorSet =	true;
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
	//~ private function customMultiStringToArray($aCustom)
	//~ {
		//~ $aRetCaption		= array();
		//~ $aRetValues		= array();
		
		//~ $outValueHeader		= '';
		
		//~ foreach ($aCustom as $sectionHeader => $aRows) {
			
			//~ if ($outValueHeader != $sectionHeader) {
				//~ $outValueHeader		= $sectionHeader;
			//~ }
			
			//~ foreach ($aRows as $code => $colonSepValues) {
				//~ /**
				 //~ * Get the caption
				 //~ */
				//~ $firstColon		= strpos($colonSepValues, ';');
				//~ $caption		= substr($colonSepValues, 0, $firstColon + 1);
				
				//~ $aRetCaption[$code]	= trim($caption, ';"');
				
				//~ /**
				 //~ * Get the value part of the assignment
				 //~ */
				//~ $allValues		= substr($colonSepValues, $firstColon + 1);
				
				//~ /**
				 //~ * Get the first value
				 //~ */
				//~ $allValues		= trim($allValues, ';"');
				//~ $secondColon		= strpos($allValues, ';');
				
				//~ /**
				 //~ * Get the second value
				 //~ */
				//~ $remainingValues	= trim(substr($allValues, $secondColon + 1), ' ;"');
				//~ if (strpos($remainingValues, ';')) {
					//~ $disregardComplexValue		= true;
				//~ } else {
					//~ $disregardComplexValue		= false;
				//~ }
				
				//~ if ($secondColon === false) {
					//~ /**
					 //~ * No second separator was found which means there is only one value.
					 //~ */
					//~ $tmpVal		= trim($allValues, ' ;"');
					
					//~ if ($tmpVal) {
						//~ /**
						 //~ * Convert to date string if value is numeric
						 //~ */
						//~ if (is_numeric($tmpVal)) {
							//~ $aRetValues[$outValueHeader][$code]	= $this->formatRawDate($tmpVal);
						//~ } else {
							//~ $aRetValues[$outValueHeader][$code]	= $tmpVal;
						//~ }
					//~ }
					
					//~ continue;
					
				//~ } else {
					
					//~ $tmpVal		= substr($allValues, 0, $secondColon + 1);
					//~ $tmpVal		= trim($tmpVal, ' ;"');
					
					//~ if ($tmpVal) {
						//~ $aRetValues[$outValueHeader][$code]	= array();
						
						//~ if ($disregardComplexValue) {
							//~ /**
							 //~ * Convert to date string if value is numeric
							 //~ */
							//~ if (is_numeric($tmpVal)) {
								//~ $aRetValues[$outValueHeader][$code]	= $this->formatRawDate($tmpVal);
							//~ } else {
								//~ $aRetValues[$outValueHeader][$code]	= $tmpVal;
							//~ }
						//~ } else {
							//~ /**
							 //~ * Convert to date string if value is numeric
							 //~ */
							//~ if (is_numeric($tmpVal)) {
								//~ $aRetValues[$outValueHeader][$code][]	= $this->formatRawDate($tmpVal);
							//~ } else {
								//~ $aRetValues[$outValueHeader][$code][]	= $tmpVal;
							//~ }
						//~ }
					//~ }
				//~ }
				
				//~ if ($disregardComplexValue) {
					//~ /**
					 //~ * This is a complex value.
					 //~ *	This will be processed in the future but DISREGARD it for v2.0.1.
					 //~ */
					//~ continue;
				//~ }
				
				//~ /**
				 //~ * Convert to date string if value is numeric
				 //~ */
				//~ if (is_numeric($remainingValues)) {
					//~ $aRetValues[$outValueHeader][$code][]		= $this->formatRawDate($remainingValues);
				//~ } else {
					//~ $aRetValues[$outValueHeader][$code][]		= $remainingValues;
				//~ }
			//~ }
		//~ }
		
		//~ return array($aRetCaption, $aRetValues);
	//~ }
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
			$result		= $this->registry['db']->saveSubscriberCaption($code, $caption, $this->language);
			
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
						if (!isset($value[1])) {
							var_dump($value);
							exit;
						}
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
			$this->registry['db']->processCondBooleans($this->animalId, $aToDbBoolean);
		}
		if ($aToDbDate) {
			$this->registry['db']->processCondDates($this->animalId, $aToDbDate);
		}
		if ($aToDbEvent) {
			$this->registry['db']->processCondEvents($this->animalId, $aToDbEvent);
		}
	}
		
	/**
	 * Convert Date values into YYYY-MM-DD
	 * Process only the first two values (event)
	 */
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
			if ($this->registry['tool']->isYyyyMmDdDashSeparated($value)) {
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
