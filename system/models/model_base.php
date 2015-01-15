<?php
/**
 * Abstract base class file for model.  All models extends this class.
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
 * This is an abstract base class for model.  All models extends this class.
 * @package    abstract_class
 */
Abstract Class Model_Base
{
	protected $dbc;
	
	public $sqlerror;
	public $sqlerrno;
	
	private $dbHost	= 'localhost';
	private $dbUser	= 'root';
	private $dbPass	= 'root1'; //'xSRdSBjWqTGZWqQx';
	private $dbName	= 'cm2';
	
	protected $aCaptionSettings;

	function __construct()
	{
		$this->dbc	= new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbName, $this->dbUser, $this->dbPass);
		$this->dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	
	/**
	 * Include database API functions.
	 */
	
	/**
	 * Internal function that calls PDO fetchAll().  Callable only from this inside this file.
	 * @param string $sql
	 * @return array
	 */
	protected function fetchAll($sql)
	{
		$sth	= $this->dbc->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Handles SQL errors
	 * @return string
	 */
	protected function databaseError()
	{
		$this->sqlerrno		= $this->dbc->errorCode();
		if ($this->sqlerrno == '23000') {
			$this->sqlerror		= 'Cannot commit your changes.  This usually happens to protect data dependencies of related information.';
		} else {
			$this->sqlerror		= 'Unknown database error occurred.';
		}
		return SQL_ERROR;
	}
	
	/**
	 * Inserts a row and returns the Insert Id.  Function databaseError() does error handling.
	 * @return integer, string
	 */
	protected function safeExecInsert($sql)
	{
		try {
			$this->dbc->query($sql);
			return $this->dbc->lastInsertId();
			
		} catch(PDOException $e) {
			return $this->databaseError();
		}
	}
	
	/**
	 * @param $sql Sample: "insert into R_QQQ (QUANTIEME,RUN) values (?,?)"
	 * @param @aParam Sample: array("ZYX", "053");
	 */
	protected function safeExecInsertWithParam($sql, $aParam, $getAffectedRows=false)
	{
		try {
			$smtp	= $this->dbc->prepare($sql);
			$smtp->execute($aParam);
			
			if ($getAffectedRows) {
				return $smtp->rowCount();
			} else {
				return $this->dbc->lastInsertId();
			}
			
		} catch(PDOException $e) {
			return $this->databaseError();
		}
	}
	
	/**
	 * Calls PDO exec.  Function databaseError() does error handling.
	 * @return integer, string
	 */
	protected function safeExec($sql)
	{
		try {
			return $this->dbc->exec($sql);
			
		} catch(PDOException $e) {
			return $this->databaseError();
		}
	}
	
	
	/**
	 * Include common getter functions
	 */
	
	/**
	 * Returns honorary array
	 * Called from
	 *	Model: db_followup, db_cron_date_scanner
	 *	Controller: clients
	 * @param integer $n
	 */
	public function getHonorary($n=NULL)
	{
		if (is_null($n)) {
			$sql	= "SELECT * from honorary ORDER BY honoraryId";
			$rows	= $this->fetchAll($sql);
			
			$aHonorary	= array();
			
			foreach ($rows as $onerow) {
				$aHonorary[$onerow['honoraryId']]	= $onerow['honoraryTitle'];
			}
			
			return $aHonorary;
		}
		
		$sql	= "SELECT * from honorary WHERE honoraryId = " . $n;
		$rows	= $this->fetchAll($sql);
		return $rows[0]['honoraryTitle'];
	}
	
	/**
	 * Get settings on criteriaCaption and put it in session variables for easy reference.
	 *	Settings:
	 *		birth (animal)
	 *		death (animal)
	 */
	public function getCriteriaCaptionSettings()
	{
                $sql    = "SELECT criteriaCaptionId,
                                criteriaCode,
                                criteriaType
                        FROM criteriaCaption
                        WHERE criteriaType = 'Event' OR criteriaType = 'Boolean' OR criteriaType = 'Date'
                        ";
                $aRows  = $this->fetchAll($sql);
		
                $aReturn        = array();
		
                foreach ($aRows as $row) {
                        if ($row['criteriaType'] == 'Boolean') {
                                $code   = strtolower($row['criteriaCode']);
                                if ($code == 'active' || $code == 'identified' || $code == 'vaccinated'|| $code == 'insured' || $code == 'dead') {
                                        // Ok, include this to settings
                                        $aReturn[$row['criteriaCode']]          = $row['criteriaCaptionId'];
                                } else {
                                        continue;
                                }
                        } else {
                                $aReturn[$row['criteriaCode']]          = $row['criteriaCaptionId'];
                        }
                }
		
                /**
                 * Save it to Class-wide variable.
                 */
                $this->aCaptionSettings         = $aReturn;
		
                return $aReturn;	
	}
}

// eof
