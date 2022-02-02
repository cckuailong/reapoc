<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

/**
 * Lightweight abstraction layer for testing the connectivity of a database request
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DBTest
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */

class DUPX_DBTestIn
{
	//Create, Rename, Empty, Skip
	public $dbaction;
	public $dbhost;
	public $dbname;
	public $dbuser;
	public $dbpass;
	public $dbport;
	public $dbcollatefb;
}

class DUPX_DBTestOut extends DUPX_CTRL_Out
{
	public function __construct()
	{
		parent::__construct();
	}
}

class DUPX_DBTest
{
	public $databases		 = array();
	public $tblPerms;
	public $reqs			 = array();
	public $notices			 = array();
	public $reqsPass		 = false;
	public $noticesPass		 = false;
	public $in;
	public $ac;
	public $collationStatus = array();
    public $collationReplaceList = array();
	public $lastError;
	//JSON | PHP
	public $responseMode	 = 'JSON';
	//TEST | LIVE
	public $runMode			 = 'TEST';
	//TEXT | HTML
	public $displayMode		 = 'TEXT';
	//PRIVATE
	private $out;
	private $dbh;
	private $permsChecked  = false;
	private $newDBMade	   = false;


	public function __construct(DUPX_DBTestIn $input)
	{
		$default_msg	 = 'This test passed without any issues';
		$this->in		 = $input;
		$this->out		 = new DUPX_DBTestOut();
		$this->tblPerms	 = array('all' => -1, 'create' => -1, 'insert' => -1, 'update' => -1, 'delete' => -1, 'select' => -1, 'drop' => -1);
		$this->ac = DUPX_ArchiveConfig::getInstance();

		//REQUIRMENTS
		//Pass States: skipped = -1		failed = 0		passed = 1   warned = 2
		$this->reqs[5]	 = array('title' => "Create Database User", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[10]	 = array('title' => "Verify Host Connection", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[20]	 = array('title' => "Check Server Version", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[30]	 = array('title' => "Create New Database Tests", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[40]	 = array('title' => "Confirm Database Visibility", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[50]	 = array('title' => "Manual Table Check", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[60]	 = array('title' => "Test User Table Privileges", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[70]	 = array('title' => "Check Collation Capability", 'info' => "{$default_msg}", 'pass' => -1);
		$this->reqs[80]	 = array('title' => "Check GTID mode", 'info' => "{$default_msg}", 'pass' => -1);
		//NOTICES
		$this->notices[10]	 = array('title' => "Table Case Sensitivity", 'info' => "{$default_msg}", 'pass' => -1);
       }

	public function run()
	{
		//Requirments
        $this->runBasic();

		$this->buildStateSummary();
		$this->buildDisplaySummary();
		$this->out->payload = $this;
		foreach ($this->out->payload->in as $key=>$val) {
			$this->out->payload->in->$key = htmlentities($val);
		}
		$this->out->getProcessTime();

		//Return PHP or JSON result
		if ($this->responseMode == 'PHP') {
			$result = $this->out;
			return $result;
		} elseif ($this->responseMode == 'JSON') {
			$result = DupLiteSnapJsonU::wp_json_encode($this->out);
			return $result;
		} else {
			die('Please specific the responseMode property');
		}

	}

	private function runBasic()
	{
		//REQUIRMENTS:
		//[10]	 = "Verify Host Connection"
		//[20]	 = "Check Server Version"
		//[30]	 = "Create New Database Tests"
		//[40]	 = "Confirm Database Visibility"
		//[50]	 = "Manual Table Check"
		//[60]	 = "Test User Table Privileges"


		$this->r10All($this->reqs[10]);
		$this->r20All($this->reqs[20]);

		switch ($this->in->dbaction) {
			case "create" :
				$this->r30Basic($this->reqs[30]);
				$this->r40Basic($this->reqs[40]);
				break;
			case "empty" :
				$this->r40Basic($this->reqs[40]);
				break;
			case "rename":
				$this->r40Basic($this->reqs[40]);
				break;
			case "manual":
				$this->r40Basic($this->reqs[40]);
				$this->r50All($this->reqs[50]);
				break;
		}

		$this->r60All($this->reqs[60]);

		//NOTICES
		$this->n10All($this->notices[10]);
		$this->r70All($this->reqs[70]);
		$this->r80All($this->reqs[80]);
		$this->basicCleanup();
	}

	/**
	 * Verify Host Connection
	 *
	 * @return null
	 */
	private function r10All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			$this->dbh = DUPX_DB::connect($this->in->dbhost, $this->in->dbuser, $this->in->dbpass, null, $this->in->dbport);
			if ($this->dbh) {
				$test['pass']	 = 1;
				$test['info']	 = "The user <b>[".htmlentities($this->in->dbuser)."]</b> successfully connected to the database server on host <b>[".htmlentities($this->in->dbhost)."]</b>.";
			} else {
				$msg = "Unable to connect the user <b>[".htmlentities($this->in->dbuser)."]</b> to the host <b>[".htmlentities($this->in->dbhost)."]</b>";
				$test['pass']	 = 0;
				$test['info']	 = (mysqli_connect_error())
								? "{$msg}. The server error response was: <i>" . htmlentities(mysqli_connect_error()) . '</i>'
								: "{$msg}. Please contact your hosting provider or server administrator.";
			}

		} catch (Exception $ex) {
			$test['pass']	 = 0;
			$test['info']	 = "Unable to connect the user <b>[".htmlentities($this->in->dbuser)."]</b> to the host <b>[".htmlentities($this->in->dbhost)."]</b>.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Check Server Version
	 *
	 * @return null
	 */
	private function r20All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			$db_version		 = DUPX_DB::getVersion($this->dbh);
			$db_version_pass = version_compare('5.0.0', $db_version) <= 0;

			if ($db_version_pass) {
				$test['pass']	 = 1;
				$test['info']	 = "This test passes with a current database version of <b>[".htmlentities($db_version)."]</b>";
			} else {
				$test['pass']	 = 0;
				$test['info']	 = "The current database version is <b>[".htmlentities($db_version)."]</b> which is below the required version of 5.0.0  "
					."Please work with your server admin or hosting provider to update the database server.";
			}

		} catch (Exception $ex) {
			$test['pass']	 = 0;
			$test['info']	 = "Unable to properly check the database server version number.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Create New Database Basic Test
	 * Use selects: 'Create New Database' for basic
	 *
	 * @return null
	 */
	private function r30Basic(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			//DATABASE EXISTS
			$db_found = mysqli_select_db($this->dbh, $this->in->dbname);
			if ($db_found) {
				$test['pass']	 = 0;
				$test['info']	 = "DATABASE CREATION FAILURE: A database named <b>[".htmlentities($this->in->dbname)."]</b> already exists.<br/><br/>"
							."Please continue with the following options:<br/>"
							."- Choose a different database name or remove this one.<br/>"
							."- Change the action drop-down to an option like \"Connect and Remove All Data\".<br/>";
				return;
			}

			//CREATE & DROP DB
			$result		 = mysqli_query($this->dbh, "CREATE DATABASE IF NOT EXISTS `".mysqli_real_escape_string($this->dbh, $this->in->dbname)."`");
			$db_found	 = mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->in->dbname));

			if (!$db_found) {
				$test['pass']	 = 0;
				$test['info']	 = sprintf(ERR_DBCONNECT_CREATE, htmlentities($this->in->dbname));
				$test['info'] .= "\nError Message: ".mysqli_error($this->dbh);
			} else {
				$this->newDBMade = true;
				$test['pass']	= 1;
				$test['info'] = "Database <b>[".htmlentities($this->in->dbname)."]</b> was successfully created and dropped.  The user has enough privileges to create a new database with the "
							. "'Basic' option enabled.";
			}
		} catch (Exception $ex) {
			$test['pass']	 = 0;
			$test['info']	 = "Error creating database <b>[".htmlentities($this->in->dbname)."]</b>.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Confirm Database Visibility for Basic
	 *
	 * @return null
	 */
	private function r40Basic(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			//Show Databases by the host account, otherwise a timeout
			//to issue the 'Show Databases' query may occur on some hosts
			$host_user = substr_replace($this->in->dbuser, '', strpos($this->in->dbuser, '_'));
			$this->databases = DUPX_DB::getDatabases($this->dbh, $host_user);

			$db_found = mysqli_select_db($this->dbh, $this->in->dbname);
			if (!$db_found) {
				$test['pass'] = 0;
				$test['info'] = "The user '<b>[".htmlentities($this->in->dbuser)."]</b>' is unable to see the database named '<b>[".htmlentities($this->in->dbname)."]</b>'.  "
					. "Be sure the database name already exists and check that the database user has access to the database.  "
                                        . "If you want to create a new database choose the action 'Create New Database'.";
			} else {
				$test['pass'] = 1;
				$test['info'] = "The database user <b>[".htmlentities($this->in->dbuser)."]</b> has visible access to see the database named <b>[".htmlentities($this->in->dbname)."]</b>";
			}

		} catch (Exception $ex) {
			$test['pass'] = 0;
			$test['info'] = "The user '<b>[".htmlentities($this->in->dbuser)."]</b>' is unable to see the database named '<b>[".htmlentities($this->in->dbname)."]</b>'.  "
				. "Be sure the database name already exists and check that the database user has access to the database.  "
                                . "If you want to create a new database choose the action 'Create New Database'<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Manual Table Check
	 *
	 * User chooses "Manual SQL Execution"
	 * Core WP has 12 tables. Check to make sure at least 10 are present
	 * otherwise present an error message
	 *
	 * @return null
	 */
	private function r50All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			$tblcount = DUPX_DB::countTables($this->dbh, htmlentities($this->in->dbname));

			if ($tblcount < 10) {
				$test['pass']	 = 0;
				$test['info']	 = sprintf(ERR_DBMANUAL, htmlentities($this->in->dbname), htmlentities($tblcount));
			} else {
				$test['pass']	 = 1;
				$test['info']	 = "This test passes.  A WordPress database looks to be setup.";
			}

		} catch (Exception $ex) {
			$test['pass']	 = 0;
			$test['info']	 = "The database user <b>[".htmlentities($this->in->dbuser)."]</b> has visible access to see the database named <b>[".htmlentities($this->in->dbname)."]</b> .<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Test User Table privileges
	 *
	 * @return null
	 */
	private function r60All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			$this->checkTablePerms();

			if ($this->tblPerms['all']) {
				$test['pass']	 = 1;
				$test['info']	 = "The user <b>[".htmlentities($this->in->dbuser)."]</b> the correct privileges on the database <b>[".htmlentities($this->in->dbname)."]</b>";
			} else {
				$list		 = array();
				$test['pass']	 = 0;
				foreach ($this->tblPerms as $key => $val) {
					if ($key != 'all') {
						if ($val == false) array_push($list, $key);
					}
				}
				$list		 = implode(',', $list);
				$test['info']	 = "The user <b>[".htmlentities($this->in->dbuser)."]</b> is missing the privileges <b>[".htmlentities($list)."]</b> on the database <b>[".htmlentities($this->in->dbname)."]</b>";
			}

		} catch (Exception $ex) {
			$test['pass']	 = 0;
			$test['info']	 = "Failure in attempt to read the users table priveleges.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Check Collation Capability
	 *
	 * @return null
	 */
	private function r70All(&$test)
	{
		try {
			if ($this->isFailedState($test)) {
				return;
			}

			$this->collationStatus = DUPX_DB::getCollationStatus($this->dbh, $this->ac->dbInfo->collationList);

            $collation_arr = array(
                'utf8mb4_unicode_520_ci',
                'utf8mb4_unicode_520',
                'utf8mb4_unicode_ci',
                'utf8mb4',
                'utf8_unicode_520_ci',
                'utf8_unicode_520',
                'utf8_unicode_ci',
                'utf8',
            );
			$invalid = 0;
			$collation_arr_max = count($collation_arr);
			$invalid_match = 0;

			foreach($this->collationStatus as $key => $val) {

				if ($this->collationStatus[$key]['found'] == 0) {
				    if($this->in->dbcollatefb){
				        $not_supported_col = $this->collationStatus[$key]['name'];
                        //returns false or key
                        $i = array_search($not_supported_col,$collation_arr);

                        if($i !== false){
                            ++$i;
                            for($i; $i < $collation_arr_max; $i++) {

                                $col_status = DUPX_DB::getCollationStatus($this->dbh, array($collation_arr[$i]));
                                $cur_col_is_supported = $col_status[0]['found'];
                                if($cur_col_is_supported){
                                    $this->collationReplaceList[] = array(
                                        'search'    => $not_supported_col,
                                        'replace'   => $collation_arr[$i]
                                    );
									++$invalid_match;
									break;
                                }
                            }
                        } else {
                            $invalid = 1;
                            break;
                        }
                    } else {
                        $invalid = 1;
                        break;
                    }
				}
			}

			if($invalid_match > 0) {
				$invalid = -1;
			}

			if ($invalid === 1) {
				$test['pass']	 = 0;
				$test['info']	 = "Please check the 'Legacy' checkbox in the options section and then click the 'Retry Test' link.<br/>"
								 . "<small>Details: The database where the package was created has a collation that is not supported on this server.  This issue happens "
								 . "when a site is moved from an older version of MySQL to a newer version of MySQL. The recommended fix is to update MySQL on this server to support "
								 . "the collation that is failing below.  If that is not an option for your host then continue by clicking the 'Legacy' checkbox above.  For more "
								 . "details about this issue and other details regarding this issue see the FAQ link below. </small>";
			} else if($invalid === -1) {
                $test['pass']	 = 1;
                $test['info']	 = "There is at least one collation that is not supported, however a replacement collation is possible.  Please continue by clicking the next button and the "
								 . "installer will attempt to use a legacy/fallback collation type to create the database table.  For more details about this issue see the FAQ link below.";
            } else {
				$test['pass']	 = 1;
				$test['info']	 = "Collation test passed! This database supports the required table collations.";
			}

		} catch (Exception $ex) {
			//Return '1' to allow user to continue
			$test['pass']	 = 1;
			$test['info']	 = "Failure in attempt to check collation capability status.<br/>" . $this->formatError($ex);
		}

	}

	/**
	 * Check GTID mode
	 *
	 * @return null
	 */
	private function r80All(&$test)
	{
		try {
			if ($this->isFailedState($test)) {
				return;
			}

			$gtid_mode_enabled = false;
			$query  = "SELECT @@GLOBAL.GTID_MODE";
			$result = mysqli_query($this->dbh, $query);

			if ($result = mysqli_query($this->dbh, $query)) {
				if ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
					if ('ON' == $row[0] || 'on' == $row[0])
						$gtid_mode_enabled = true;
				}
			}

			// $gtid_mode_enabled = true;
			if ($gtid_mode_enabled) {
				$test['pass'] = 2;
				$test['info'] = "Your database server have GTID mode is on, It might make a trouble in Database installation.<br/>"
								 . "<small>Details: You might face the error something like Statement violates GTID consistency. "
								 . "You should ask hosting provider to make off GTID off. "
								 . "You can make off GTID mode as decribed in the <a href='https://dev.mysql.com/doc/refman/5.7/en/replication-mode-change-online-disable-gtids.html' target='_blank'>https://dev.mysql.com/doc/refman/5.7/en/replication-mode-change-online-disable-gtids.html</a>"
								 . "</small>";
			} else {
				$test['pass'] = 1;
				$test['info'] = "The installer have not detected GTID mode.";
			}
		} catch (Exception $ex) {			
			//Return '1' to allow user to continue
			$test['pass'] = 1;
			$test['info'] = "Failure in attempt to check GTID mode status.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Table Case Compatibility
	 *
	 * Failure occurs when:
	 *		BuildServer = lower_case_table_names=1		&&
	 *		BuildServer = HasATableUpperCase			&&
	 *		InstallServer = lower_case_table_names=0
	 *
	 * @return null
	 */
	private function n10All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			$localhostLowerCaseTables = DUPX_DB::getVariable($this->dbh, 'lower_case_table_names');
			$localhostLowerCaseTables = (empty($localhostLowerCaseTables) && DUPX_U::isWindows()) ? 0 : $localhostLowerCaseTables;

			if (isset($this->ac->dbInfo->isTablesUpperCase) && $this->ac->dbInfo->isTablesUpperCase && $this->ac->dbInfo->varLowerCaseTables == 1 && $localhostLowerCaseTables == 0) {
				$test['pass']	 = 0;
				$test['info']	 = "An upper case table name was found in the database SQL script and the server variable lower_case_table_names is set  "
					. "to <b>[".htmlentities($localhostLowerCaseTables)."]</b>.  When both of these conditions are met it can lead to issues with creating tables with upper case characters.  "
					. "<br/><b>Options</b>:<br/> "
					. " - On this server have the host company set the lower_case_table_names value to 1 or 2 in the my.cnf file.<br/>"
					. " - On the build server set the lower_case_table_names value to 2 restart server and build package.<br/>"
					. " - Optionally continue the install with data creation issues on upper case tables names.<br/>";
			} else {
				$test['pass']	 = 1;
				$test['info']	 = "No table casing issues detected. This servers variable setting for lower_case_table_names is [{$localhostLowerCaseTables}]";
			}

		} catch (Exception $ex) {
			//Return '1' to allow user to continue
			$test['pass']	 = 1;
			$test['info']	 = "Failure in attempt to read the upper case table status.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Input has UTF8 data
	 *
	 * @return null
	 */
	private function n30All(&$test)
	{
		try {

			if ($this->isFailedState($test)) {
				return;
			}

			//WARNNG: Input has utf8 data
			$dbConnItems = array($this->in->dbhost, $this->in->dbuser, $this->in->dbname, $this->in->dbpass);
			$dbUTF8_tst	 = false;
			foreach ($dbConnItems as $value) {
				if (DUPX_U::isNonASCII($value)) {
					$dbUTF8_tst = true;
					break;
				}
			}

			if (!$dbConn && $dbUTF8_tst) {
				$test['pass']	 = 0;
				$test['info']	 = ERR_TESTDB_UTF8;

			} else {
				$test['pass']	 = 1;
				$test['info']	 = "Connection string is using all non-UTF8 characters and should be safe.";
			}

		} catch (Exception $ex) {
			//Return '1' to allow user to continue
			$test['pass']	 = 1;
			$test['info']	 = "Failure in attempt to read input has utf8 data status.<br/>" . $this->formatError($ex);
		}
	}

	/**
	 * Runs a series of CREATE, INSERT, SELECT, UPDATE, DELETE and DROP statements
	 * on a temporary test table to find out the state of the users privileges
	 *
	 * @return null
	 */
	private function checkTablePerms()
	{

		if ($this->permsChecked) {
			return;
		}

		mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->in->dbname));
		$tmp_table	 = '__dpro_temp_'.rand(1000, 9999).'_'.date("ymdHis");
		$qry_create	 = @mysqli_query($this->dbh, "CREATE TABLE `".mysqli_real_escape_string($this->dbh, $tmp_table)."` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`text` text NOT NULL,
						PRIMARY KEY (`id`))");

		$this->tblPerms['create'] = ($qry_create) ? 1 : 0;

		if ($qry_create) {
			$qry_insert	 = @mysqli_query($this->dbh, "INSERT INTO `".mysqli_real_escape_string($this->dbh, $tmp_table)."` (`text`) VALUES ('Duplicator Test: Please Remove this Table')");
			$qry_insert	 = @mysqli_query($this->dbh, "INSERT INTO `".mysqli_real_escape_string($this->dbh, $tmp_table)."` (`text`) VALUES ('TEXT-1')");
			$qry_select	 = @mysqli_query($this->dbh, "SELECT COUNT(*) FROM `".mysqli_real_escape_string($this->dbh, $tmp_table)."`");
			$qry_update	 = @mysqli_query($this->dbh, "UPDATE `".mysqli_real_escape_string($this->dbh, $tmp_table)."` SET text = 'TEXT-2' WHERE text = 'TEXT-1'");
			$qry_delete	 = @mysqli_query($this->dbh, "DELETE FROM `".mysqli_real_escape_string($this->dbh, $tmp_table)."` WHERE text = 'TEXT-2'");
			$qry_drop	 = @mysqli_query($this->dbh, "DROP TABLE IF EXISTS `".mysqli_real_escape_string($this->dbh, $tmp_table)."`;");

			$this->tblPerms['insert']	 = ($qry_insert) ? 1 : 0;
			$this->tblPerms['select']	 = ($qry_select) ? 1 : 0;
			$this->tblPerms['update']	 = ($qry_update) ? 1 : 0;
			$this->tblPerms['delete']	 = ($qry_delete) ? 1 : 0;
			$this->tblPerms['drop']	 = ($qry_drop) ? 1 : 0;
		}

		$this->tblPerms['all'] = $this->tblPerms['create'] && $this->tblPerms['insert'] && $this->tblPerms['select'] &&
			$this->tblPerms['update'] && $this->tblPerms['delete'] && $this->tblPerms['drop'];

		$this->permsChecked = true;
	}

        /**
	 * Return the sql_mode set for the database
	 *
	 * @return null
	 */
	private function checkSQLMode()
	{
		if ($this->sqlmodeChecked) {
			return;
		}

        $qry_sqlmode	 = @mysqli_query($this->dbh, "SELECT @@GLOBAL.sql_mode as mode");
        if($qry_sqlmode){
            $sql_mode_array = mysqli_fetch_assoc($qry_sqlmode);

            if($sql_mode_array !== false) {
                $this->sql_modes = $sql_mode_array['mode'];
            } else {
                $this->sql_modes ="query failed <br/>".htmlentities(@mysqli_error($this->dbh));
            }

        }else{
           $this->sql_modes ="query failed <br/>".htmlentities(@mysqli_error($this->dbh));
        }

		$this->sqlmodeChecked = true;
        return $this->sql_modes;
	}

    /**
	 * Test if '0000-00-00' date query fails or not
	 *
	 * @return null
	 */
	private function testDateInsert()
	{
		if ($this->dateInsertChecked) {
			return;
		}

		mysqli_select_db($this->dbh, $this->in->dbname);

		$tmp_table	 = '__dpro_temp_'.rand(1000, 9999).'_'.date("ymdHis");
		$tmp_table	 = mysqli_real_escape_string($dbh, $tmp_table);

		$qry_create	 = @mysqli_query($this->dbh, "CREATE TABLE `{$tmp_table}` (
						`datetimefield` datetime NOT NULL,
						`datefield` date NOT NULL)");

		if ($qry_create) {
            $qry_date    = @mysqli_query($this->dbh, "INSERT INTO `".$tmp_table."` (`datetimefield`,`datefield`) VALUES ('0000-00-00 00:00:00','0000-00-00')");

            if($qry_date) {
                 $this->queryDateInserted = true;
            }
		}

		$this->dateInsertChecked = true;

        return $this->queryDateInserted;
	}

	/**
	 * Cleans up basic setup items when test mode is enabled
	 *
	 * @return null
	 */
	private function basicCleanup()
	{
		//TEST MODE ONLY
		if ($this->runMode == 'TEST') {

			//DELETE DB
			if ($this->newDBMade && $this->in->dbaction == 'create') {
				$result	= mysqli_query($this->dbh, "DROP DATABASE IF EXISTS `".mysqli_real_escape_string($this->dbh, $this->in->dbname)."`");
				if (!$result) {
					$this->reqs[30][pass] = 0;
					$this->reqs[30][info] = "The database <b>[".htmlentities($this->in->dbname)."]</b> was successfully created. However removing the database was not successful with the following response.<br/>"
								."Response Message: <i>".htmlentities(mysqli_error($this->dbh))."</i>.  This database may need to be removed manually.";
				}
			}
		}
	}

	/**
	 * Checks if any previous test has failed.  If so then prevent the current test
	 * from running
	 *
	 * @return null
	 */
	private function isFailedState(&$test)
	{
		foreach ($this->reqs as $key => $value) {
			if ($this->reqs[$key]['pass'] == 0) {
				$test['pass']	 = -1;
				$test['info']	 = 'This test has been skipped because a higher-level requirement failed. Please resolve previous failed tests.';
				return true;
			}
		}
		return false;
	}

	/**
	 * Gathers all the test data and builds a summary result
	 *
	 * @return null
	 */
	private function buildStateSummary()
	{
		$req_status		 = 1;
		$notice_status	 = -1;
		$last_error		 = 'Unable to determine error response';
		foreach ($this->reqs as $key => $value) {
			if ($this->reqs[$key]['pass'] == 0) {
				$req_status	 = 0;
				$last_error	 = $this->reqs[$key]['info'];
				break;
			}
		}

		if (1 == $req_status) {
			foreach ($this->reqs as $key => $value) {
				if ($this->reqs[$key]['pass'] == 2) {
					$req_status = 2;
					break;
				}
			}	
		}

		//Only show notice summary if a test was ran
		foreach ($this->notices as $key => $value) {
			if ($this->notices[$key]['pass'] == 0) {
				$notice_status = 0;
				break;
			} elseif ($this->notices[$key]['pass'] == 1) {
				$notice_status = 1;
			}
		}

		$this->lastError	 = $last_error;
		$this->reqsPass		 = $req_status;
		$this->noticesPass	 = $notice_status;
	}

	/**
	 * Converts all test info messages to either TEXT or HTML format
	 *
	 * @return null
	 */
	private function buildDisplaySummary()
	{
		if ($this->displayMode == 'TEXT') {
			//TODO: Format for text
		} else {
			//TODO: Format for html
		}
	}

	private function formatError(Exception $ex)
	{
		return "Message: " . htmlentities($ex->getMessage()) . "<br/>Line: " . htmlentities($ex->getFile()) . ':' . htmlentities($ex->getLine());
	}
}
