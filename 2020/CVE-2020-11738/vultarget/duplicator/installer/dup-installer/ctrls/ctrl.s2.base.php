<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
//-- START OF ACTION STEP 2
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

$_POST['dbaction'] = isset($_POST['dbaction']) ? DUPX_U::sanitize_text_field($_POST['dbaction']) : 'create';

if (isset($_POST['dbhost'])) {
    $post_db_host = DUPX_U::sanitize_text_field($_POST['dbhost']);
    $_POST['dbhost'] = trim($post_db_host);
} else {
    $_POST['dbhost'] = null;
}

if (isset($_POST['dbname'])) {
    $post_db_name = DUPX_U::sanitize_text_field($_POST['dbname']);
    $_POST['dbname'] = trim($post_db_name);
} else {
    $_POST['dbname'] = null;
}

$_POST['dbuser'] = isset($_POST['dbuser']) ? DUPX_U::sanitize_text_field($_POST['dbuser']) : null;
$_POST['dbpass'] = isset($_POST['dbpass']) ? trim($_POST['dbpass']) : null;

if (isset($_POST['dbhost'])) {
    $post_db_host = DUPX_U::sanitize_text_field($_POST['dbhost']);
    $_POST['dbport'] = parse_url($post_db_host, PHP_URL_PORT);
} else {
    $_POST['dbport'] = 3306;
}

$_POST['dbport'] = (!empty($_POST['dbport'])) ? DUPX_U::sanitize_text_field($_POST['dbport']) : 3306;
$_POST['dbnbsp']		= (isset($_POST['dbnbsp']) && $_POST['dbnbsp'] == '1') ? true : false;

if (isset($_POST['dbcharset'])) {
    $post_db_charset = DUPX_U::sanitize_text_field($_POST['dbcharset']);
    $_POST['dbcharset'] = trim($post_db_charset);
} else {
    $_POST['dbcharset'] = $GLOBALS['DBCHARSET_DEFAULT'];
}

if (isset($_POST['dbcollate'])) {
    $post_db_collate = DUPX_U::sanitize_text_field($_POST['dbcollate']);
    $_POST['dbcollate'] = trim($post_db_collate);
} else {
    $_POST['dbcollate'] = $GLOBALS['DBCOLLATE_DEFAULT'];
}

$_POST['dbcollatefb']	= (isset($_POST['dbcollatefb']) && $_POST['dbcollatefb'] == '1') ? true : false;
$_POST['dbobj_views']	= isset($_POST['dbobj_views']) ? true : false; 
$_POST['dbobj_procs']	= isset($_POST['dbobj_procs']) ? true : false;
$_POST['config_mode']	= (isset($_POST['config_mode'])) ? DUPX_U::sanitize_text_field($_POST['config_mode']) : 'NEW';

$ajax2_start	 = DUPX_U::getMicrotime();
$root_path		 = $GLOBALS['DUPX_ROOT'];
$JSON			 = array();
$JSON['pass']	 = 0;

$nManager = DUPX_NOTICE_MANAGER::getInstance();

/**
JSON RESPONSE: Most sites have warnings turned off by default, but if they're turned on the warnings
cause errors in the JSON data Here we hide the status so warning level is reset at it at the end */
$ajax2_error_level = error_reporting();
error_reporting(E_ERROR);
($GLOBALS['LOG_FILE_HANDLE'] != false) or DUPX_Log::error(ERR_MAKELOG);


//===============================================
//DB TEST & ERRORS: From Postback
//===============================================
//INPUTS
$dbTestIn			 = new DUPX_DBTestIn();
$dbTestIn->mode		 = DUPX_U::sanitize_text_field($_POST['view_mode']);
$dbTestIn->dbaction	 = DUPX_U::sanitize_text_field($_POST['dbaction']);
$dbTestIn->dbhost	 = DUPX_U::sanitize_text_field($_POST['dbhost']);
$dbTestIn->dbuser	 = DUPX_U::sanitize_text_field($_POST['dbuser']);
$dbTestIn->dbpass    = trim($_POST['dbpass']);
$dbTestIn->dbname	 = DUPX_U::sanitize_text_field($_POST['dbname']);
$dbTestIn->dbport	 = DUPX_U::sanitize_text_field($_POST['dbport']);
$dbTestIn->dbcollatefb = DUPX_U::sanitize_text_field($_POST['dbcollatefb']);

$dbTest	= new DUPX_DBTest($dbTestIn);

//CLICKS 'Test Database'
if (isset($_GET['dbtest'])) {
	
	$dbTest->runMode = 'TEST';
	$dbTest->responseMode = 'JSON';
	if (!headers_sent()) {
		header('Content-Type: application/json');
	}
	die($dbTest->run());
} 

$not_yet_logged = (isset($_POST['first_chunk']) && $_POST['first_chunk']) || (!isset($_POST['continue_chunking']));

if($not_yet_logged){
    DUPX_Log::info("\n\n\n********************************************************************************");
    DUPX_Log::info('* DUPLICATOR-LITE INSTALL-LOG');
    DUPX_Log::info('* STEP-2 START @ '.@date('h:i:s'));
    DUPX_Log::info('* NOTICE: Do NOT post to public sites or forums!!');
    DUPX_Log::info("********************************************************************************");

    $labelPadSize = 20;
    DUPX_Log::info("USER INPUTS");
    DUPX_Log::info(str_pad('VIEW MODE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['view_mode']));
    DUPX_Log::info(str_pad('DB ACTION', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbaction']));
    DUPX_Log::info(str_pad('DB HOST', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
    DUPX_Log::info(str_pad('DB NAME', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
    DUPX_Log::info(str_pad('DB PASS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
    DUPX_Log::info(str_pad('DB PORT', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString('**OBSCURED**'));
    DUPX_Log::info(str_pad('NON-BREAKING SPACES', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbnbsp']));
    DUPX_Log::info(str_pad('MYSQL MODE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbmysqlmode']));
    DUPX_Log::info(str_pad('MYSQL MODE OPTS', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbmysqlmode_opts']));
    DUPX_Log::info(str_pad('CHARSET', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbcharset']));
    DUPX_Log::info(str_pad('COLLATE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbcollate']));
    DUPX_Log::info(str_pad('COLLATE FB', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbcollatefb']));
    DUPX_Log::info(str_pad('VIEW CREATION', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbobj_views']));
    DUPX_Log::info(str_pad('STORED PROCEDURE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($_POST['dbobj_procs']));
    DUPX_Log::info("********************************************************************************\n");

    $POST_LOG = $_POST;
    unset($POST_LOG['dbpass']);
    ksort($POST_LOG);
    $log = "--------------------------------------\n";
    $log .= "POST DATA\n";
    $log .= "--------------------------------------\n";
    $log .= print_r($POST_LOG, true);
    DUPX_Log::info($log, DUPX_Log::LV_DEBUG, true);

}


//===============================================
//DATABASE ROUTINES
//===============================================
$dbinstall = new DUPX_DBInstall($_POST, $ajax2_start);
if ($_POST['dbaction'] != 'manual') {
    if(!isset($_POST['continue_chunking'])){
        $dbinstall->prepareDB();
    } else if($_POST['first_chunk'] == 1) {
        $dbinstall->prepareDB();
    }
}
if($not_yet_logged) {

	//Fatal Memory errors from file_get_contents is not catchable.
	//Try to warn ahead of time with a check on buffer in memory difference
	$current_php_mem = DUPX_U::returnBytes($GLOBALS['PHP_MEMORY_LIMIT']);
	$current_php_mem = is_numeric($current_php_mem) ? $current_php_mem : null;

	if ($current_php_mem != null && $dbinstall->dbFileSize > $current_php_mem) {
		$readable_size = DUPX_U::readableByteSize($dbinstall->dbFileSize);
		$msg   = "\nWARNING: The database script is '".DUPX_U::sanitize_text_field($readable_size)."' in size.  The PHP memory allocation is set\n";
		$msg  .= "at '".DUPX_U::sanitize_text_field($GLOBALS['PHP_MEMORY_LIMIT'])."'.  There is a high possibility that the installer script will fail with\n";
		$msg  .= "a memory allocation error when trying to load the database.sql file.  It is\n";
		$msg  .= "recommended to increase the 'memory_limit' setting in the php.ini config file.\n";
		$msg  .= "see: ".DUPX_U::esc_url($faq_url.'#faq-trouble-056-q')." \n";
		DUPX_Log::info($msg);
		unset($msg);
	}

    DUPX_Log::info("--------------------------------------");
    DUPX_Log::info("DATABASE RESULTS");
    DUPX_Log::info("--------------------------------------");
}

if ($_POST['dbaction'] == 'manual') {
	DUPX_Log::info("\n** SQL EXECUTION IS IN MANUAL MODE **");
	DUPX_Log::info("- No SQL script has been executed -");
	$JSON['pass'] = 1;
} elseif(!isset($_POST['continue_chunking'])) {
    $dbinstall->writeInDB();
    $rowCountMisMatchTables = $dbinstall->getRowCountMisMatchTables();
    $JSON['pass'] = 1;
    if (!empty($rowCountMisMatchTables)) {
		$errMsg = 'ERROR: Database Table row count verification was failed for table(s): '.implode(', ', $rowCountMisMatchTables);
		DUPX_Log::info($errMsg);		
	}
}

$dbinstall->profile_end = DUPX_U::getMicrotime();
$dbinstall->writeLog();
$JSON = $dbinstall->getJSON($JSON);
$nManager->saveNotices();

//FINAL RESULTS
$ajax1_sum	 = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $dbinstall->start_microtime);
DUPX_Log::info("\nINSERT DATA RUNTIME: " . DUPX_U::elapsedTime($dbinstall->profile_end, $dbinstall->profile_start));
DUPX_Log::info('STEP-2 COMPLETE @ '.@date('h:i:s')." - RUNTIME: {$ajax1_sum}");

error_reporting($ajax2_error_level);
die(DupLiteSnapJsonU::wp_json_encode($JSON));
