<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

//-- START OF ACTION STEP 3: Update the database
require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.archive.config.php');
require_once($GLOBALS['DUPX_INIT'].'/lib/config/class.wp.config.tranformer.php');
require_once($GLOBALS['DUPX_INIT'].'/lib/config/class.wp.config.tranformer.src.php');
require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.search.reaplce.manager.php');
require_once($GLOBALS['DUPX_INIT'].'/classes/class.s3.func.php');

/** JSON RESPONSE: Most sites have warnings turned off by default, but if they're turned on the warnings
  cause errors in the JSON data Here we hide the status so warning level is reset at it at the end */
// We have already removing warning from json resp
// It cause 500 internal server error so commenting out
/*
  $ajax3_error_level	 = error_reporting();
  error_reporting(E_ERROR);
 */
try {
    DUPX_Log::setThrowExceptionOnError(true);

    $nManager = DUPX_NOTICE_MANAGER::getInstance();
    $s3Func   = DUPX_S3_Funcs::getInstance();

    switch ($s3Func->getEngineMode()) {
        case DUPX_S3_Funcs::MODE_NORMAL:
        default:
            $s3Func->initLog();
            $s3Func->runSearchAndReplace();

            $s3Func->removeLicenseKey();
            $s3Func->createNewAdminUser();
            $s3Func->configurationFileUpdate();
            $s3Func->htaccessUpdate();
            $s3Func->generalUpdateAndCleanup();

            $s3Func->noticeTest();
            $s3Func->cleanupTmpFiles();
            $s3Func->finalReportNotices();
            $s3Func->complete();
    }

    $nManager->saveNotices();
} catch (Exception $e) {
    $s3Func->error($e->getMessage());
}

$json = $s3Func->getJsonReport();
DUPX_Log::close();
die(DupLiteSnapJsonU::wp_json_encode($json));
