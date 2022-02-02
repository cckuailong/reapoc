<?php
/*
 * Duplicator Website Installer
 * Copyright (C) 2018, Snap Creek LLC
 * website: snapcreek.com
 *
 * Duplicator (Pro) Plugin is distributed under the GNU General Public License, Version 3,
 * June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

/** Absolute path to the Installer directory. - necessary for php protection */
if ( !defined('DUPXABSPATH') ) {
    define('DUPXABSPATH', dirname(__FILE__));
}

define('ERR_CONFIG_FOUND', 'A wp-config.php already exists in this location.  This error prevents users from accidentally overwriting a WordPress site or trying to install on top of an existing one.  Extracting an archive on an existing site will overwrite existing files and intermix files causing site incompatibility issues.<br/><br/>  It is highly recommended to place the installer and archive in an empty directory. If you have already manually extracted the archive file that is associated with this installer then choose option #1 below; other-wise consider the other options: <ol><li>Click &gt; Try Again &gt; Options &gt; choose "Manual Archive Extraction".</li><li>Empty the directory except for the archive.zip/daf and installer.php and try again.</li><li>Advanced users only can remove the existing wp-config.php file and try again.</li></ol>');

ob_start();
try {
    $GLOBALS['DUPX_ROOT']     = str_replace("\\", '/', (realpath(dirname(__FILE__).'/..')));
    $GLOBALS['DUPX_ROOT_URL'] = 'http'.(!empty($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $GLOBALS['DUPX_INIT']     = "{$GLOBALS['DUPX_ROOT']}/dup-installer";
    require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.boot.php');
    /**
     * init constants and include
     */
    DUPX_Boot::init();
    DUPX_Boot::initArchiveAndLog();

    require_once($GLOBALS['DUPX_INIT'].'/classes/class.installer.state.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.password.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.db.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.http.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.server.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.package.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/config/class.conf.srv.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/utilities/class.u.php');
    require_once($GLOBALS['DUPX_INIT'].'/classes/class.view.php');

    DUPX_U::init();
    DUPX_ServerConfig::init();

    $exceptionError = false;
    // DUPX_log::error thotw an exception
    DUPX_Log::setThrowExceptionOnError(true);

    require_once($GLOBALS['DUPX_INIT'].'/classes/class.csrf.php');

    // ?view=help
    if (!empty($_GET['view']) && 'help' == $_GET['view']) {
        if (!isset($_GET['archive'])) {
            // RSR TODO: Fail gracefully
            DUPX_Log::error("Archive parameter not specified");
        }
        if (!isset($_GET['bootloader'])) {
            // RSR TODO: Fail gracefully
            DUPX_Log::error("Bootloader parameter not specified");
        }
    } else if (isset($_GET['is_daws']) && 1 == $_GET['is_daws']) { // For daws action
        $post_ctrl_csrf_token = isset($_GET['daws_csrf_token']) ? DUPX_U::sanitize_text_field($_GET['daws_csrf_token']) : '';
        if (DUPX_CSRF::check($post_ctrl_csrf_token, 'daws')) {
            $outer_root_path = dirname($GLOBALS['DUPX_ROOT']);
            if (
                (isset($_GET['daws_action']) && 'start_expand' == $_GET['daws_action'])
                &&
                (
                    !$GLOBALS['DUPX_AC']->installSiteOverwriteOn 
                    && (
                            file_exists($GLOBALS['DUPX_ROOT'].'/wp-config.php') 
                            || 
                            (
                                @file_exists($outer_root_path.'/wp-config.php') 
                                && 
                                !@file_exists($GLOBALS['DUPX_ROOT'].'/wp-settings.php')
                                && 
                                @file_exists($GLOBALS['DUPX_ROOT'].'/wp-admin') 
                                &&
                                @file_exists($GLOBALS['DUPX_ROOT'].'/wp-includes')
                            )
                        )
                )
            ) {
                $resp = array(
                    'pass' => 0,
                    'isWPAlreadyExistsError' => 1,
                    'error' => "<b style='color:#B80000;'>INSTALL ERROR!</b><br/>". ERR_CONFIG_FOUND,                    
                );
                echo DupLiteSnapJsonU::wp_json_encode($resp);
            } else {
                require_once($GLOBALS['DUPX_INIT'].'/lib/dup_archive/daws/daws.php');
            }
            die('');
        } else {
            DUPX_Log::error("An invalid request was made to 'daws'.  In order to protect this request from unauthorized access please "
            . "<a href='../{$GLOBALS['BOOTLOADER_NAME']}'>restart this install process</a>.");
        }        
    } else {
        if (!isset($_POST['archive'])) {
            $archive = DUPX_CSRF::getVal('archive');
            if (false !== $archive) {
                $_POST['archive'] = $archive;
            } else {
                // RSR TODO: Fail gracefully
                DUPX_Log::error("Archive parameter not specified");
            }
        }
        if (!isset($_POST['bootloader'])) {
            $bootloader = DUPX_CSRF::getVal('bootloader');
            if (false !== $bootloader) {
                $_POST['bootloader'] = $bootloader;
            } else {
                // RSR TODO: Fail gracefully
                DUPX_Log::error("Bootloader parameter not specified");
            }
        }
    }

    DUPX_InstallerState::init($GLOBALS['INIT']);

    if ($GLOBALS['DUPX_AC'] == null) {
        DUPX_Log::error("Can't initialize config globals! Please try to re-run installer.php");
    }

    //Password Check
    $_POST['secure-pass'] = isset($_POST['secure-pass']) ? $_POST['secure-pass'] : '';
    if ($GLOBALS['DUPX_AC']->secure_on && $GLOBALS['VIEW'] != 'help') {
        $pass_hasher = new DUPX_PasswordHash(8, FALSE);
        $pass_check  = $pass_hasher->CheckPassword(base64_encode($_POST['secure-pass']), $GLOBALS['DUPX_AC']->secure_pass);
        if (! $pass_check) {
            $GLOBALS['VIEW'] = 'secure';
        }
    }

    // Constants which are dependent on the $GLOBALS['DUPX_AC']
    $GLOBALS['SQL_FILE_NAME']		= "dup-installer-data__{$GLOBALS['DUPX_AC']->package_hash}.sql";

    if($GLOBALS["VIEW"] == "step1") {
        $init_state = true;
    } else {
        $init_state = false;
    }

    // TODO: If this is the very first step
    $GLOBALS['DUPX_STATE'] = DUPX_InstallerState::getInstance($init_state);
    if ($GLOBALS['DUPX_STATE'] == null) {
        DUPX_Log::error("Can't initialize installer state! Please try to re-run installer.php");
    }

    if (!empty($GLOBALS['view'])) {
        $post_view = $GLOBALS['view'];
    } elseif (!empty($_POST['view'])) {
        $post_view = DUPX_U::sanitize_text_field($_POST['view']);
    } else {
        $post_view = '';
    }

    // CSRF checking
    if (!empty($post_view)) {
        $csrf_views = array(
            'secure',
            'step1',
            'step2',
            'step3',
            'step4',
        );

        if (in_array($post_view, $csrf_views)) {
            if (isset($_POST['csrf_token']) && !DUPX_CSRF::check($_POST['csrf_token'], $post_view)) {
                DUPX_Log::error("An invalid request was made to '{$post_view}'.  In order to protect this request from unauthorized access please "
                . "<a href='../{$GLOBALS['BOOTLOADER_NAME']}'>restart this install process</a>.");
            }
        }
    }

    // for ngrok url and Local by Flywheel Live URL
    if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
        $host = $_SERVER['HTTP_X_ORIGINAL_HOST'];
    } else {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];//WAS SERVER_NAME and caused problems on some boxes
    }
    $GLOBALS['_CURRENT_URL_PATH'] = $host . dirname($_SERVER['PHP_SELF']);
    $GLOBALS['NOW_TIME']		  = @date("His");

    if (!chdir($GLOBALS['DUPX_INIT'])) {
        // RSR TODO: Can't change directories
        DUPX_Log::error("Can't change to directory ".$GLOBALS['DUPX_INIT']);
    }

    if (isset($_POST['ctrl_action'])) {
        $post_ctrl_csrf_token = isset($_POST['ctrl_csrf_token']) ? $_POST['ctrl_csrf_token'] : '';
        $post_ctrl_action = DUPX_U::sanitize_text_field($_POST['ctrl_action']);
        if (!DUPX_CSRF::check($post_ctrl_csrf_token, $post_ctrl_action)) {
            DUPX_Log::error("An invalid request was made to '{$post_ctrl_action}'.  In order to protect this request from unauthorized access please "
                . "<a href='../{$GLOBALS['BOOTLOADER_NAME']}'>restart this install process</a>.");
        }
        //PASSWORD CHECK
        if ($GLOBALS['DUPX_AC']->secure_on) {
            $pass_hasher = new DUPX_PasswordHash(8, FALSE);
            $pass_check  = $pass_hasher->CheckPassword(base64_encode($_POST['secure-pass']), $GLOBALS['DUPX_AC']->secure_pass);
            if (! $pass_check) {
                DUPX_Log::error("Unauthorized Access:  Please provide a password!");
            }
        }

        // the controllers must die in case of error
        DUPX_Log::setThrowExceptionOnError(false);
        switch ($post_ctrl_action) {
            case "ctrl-step1" :
                require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.s1.php');
                break;

            case "ctrl-step2" :
                require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.s2.dbtest.php');
                require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.s2.dbinstall.php');
                require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.s2.base.php');
                break;

            case "ctrl-step3" :
                require_once($GLOBALS['DUPX_INIT'].'/classes/class.engine.php');
                require_once($GLOBALS['DUPX_INIT'].'/ctrls/ctrl.s3.php');
                break;
            default:
                DUPX_Log::setThrowExceptionOnError(true);
                DUPX_Log::error('No valid action request');
        }
        DUPX_Log::setThrowExceptionOnError(true);
        DUPX_Log::error('Ctrl action problem');
    }
} catch (Exception $e) {
    $exceptionError = $e;
}

/**
 * clean output
 */
$unespectOutput = ob_get_contents();
ob_clean();
if (!empty($unespectOutput)) {
    DUPX_Log::info('ERROR: Unespect output '.DUPX_Log::varToString($unespectOutput));
    $exceptionError = new Exception('Unespected output '.DUPX_Log::varToString($unespectOutput));
}
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow">
	<title>Duplicator</title>
    <link rel='stylesheet' href='assets/font-awesome/css/all.min.css' type='text/css' media='all' />

    <link rel="apple-touch-icon" sizes="180x180" href="favicon/lite01_apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/lite01_favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/lite01_favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="mask-icon" href="favicon/lite01_safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="favicon/lite01_favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

	<?php
		require_once($GLOBALS['DUPX_INIT'] . '/assets/inc.libs.css.php');
		require_once($GLOBALS['DUPX_INIT'] . '/assets/inc.css.php');
		require_once($GLOBALS['DUPX_INIT'] . '/assets/inc.libs.js.php');
		require_once($GLOBALS['DUPX_INIT'] . '/assets/inc.js.php');
	?>
</head>
<body id="body-<?php echo $GLOBALS["VIEW"]; ?>" >

<div id="content">

<!-- HEADER TEMPLATE: Common header on all steps -->
<table cellspacing="0" class="header-wizard">
	<tr>
		<td style="width:100%;">
			<div class="dupx-branding-header">Duplicator <?php echo ($GLOBALS["VIEW"] == 'help') ? 'help' : ''; ?></div>
		</td>
		<td class="wiz-dupx-version">
            <?php  if ($GLOBALS["VIEW"] !== 'help') { ?>
			<a href="javascript:void(0)" onclick="DUPX.openServerDetails()">version:<?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_dup); ?></a>&nbsp;
			<?php DUPX_View_Funcs::helpLockLink(); ?>
			<div style="padding: 6px 0">
                <?php DUPX_View_Funcs::helpLink($GLOBALS["VIEW"]); ?>
			</div>
            <?php } ?>
		</td>
	</tr>
</table>

<div class="dupx-modes">
	<?php
		$php_enforced_txt = ($GLOBALS['DUPX_ENFORCE_PHP_INI']) ? '<i style="color:red"><br/>*PHP ini enforced*</i>' : '';
		$db_only_txt = ($GLOBALS['DUPX_AC']->exportOnlyDB) ? ' - Database Only' : '';
		$db_only_txt = $db_only_txt . $php_enforced_txt;

		if ($GLOBALS['DUPX_AC']->installSiteOverwriteOn) {
			echo  ($GLOBALS['DUPX_STATE']->mode === DUPX_InstallerMode::OverwriteInstall)
				? "<span class='dupx-overwrite'>Mode: Overwrite Install {$db_only_txt}</span>"
				: "Mode: Standard Install {$db_only_txt}";
		} else {
			echo "Mode: Standard Install {$db_only_txt}";
		}
	?>
</div>

<?php

/****************************/
/*** NOTICE MANAGER TESTS ***/
//DUPX_NOTICE_MANAGER::testNextStepFullMessageData();
//DUPX_NOTICE_MANAGER::testNextStepMessaesLevels();
//DUPX_NOTICE_MANAGER::testFinalReporMessaesLevels();
//DUPX_NOTICE_MANAGER::testFinalReportFullMessages();
/****************************/

DUPX_NOTICE_MANAGER::getInstance()->nextStepLog();
// display and remove next step notices
DUPX_NOTICE_MANAGER::getInstance()->displayStepMessages();
?>

<!-- =========================================
FORM DATA: User-Interface views -->
<div id="content-inner">
	<?php
    if ($exceptionError === false) {
        try {
            ob_start();
            switch ($GLOBALS["VIEW"]) {
                case "secure" :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.init1.php');
                    break;

                case "step1"   :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.s1.base.php');
                    break;

                case "step2" :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.s2.base.php');
                    break;

                case "step3" :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.s3.php');
                    break;

                case "step4"   :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.s4.php');
                    break;

                case "help"   :
                    require_once($GLOBALS['DUPX_INIT'] . '/views/view.help.php');
                    break;
                
                default :
                    echo "Invalid View Requested";
            }
        } catch (Exception $e) {
            /** delete view output **/
            ob_clean();
            $exceptionError = $e;
        }

        /** flush view output **/
        ob_end_flush();
        
    }

    if ($exceptionError !== false) {
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info('EXCEPTION: '.$exceptionError->getMessage());
        DUPX_Log::info('TRACE:');
        DUPX_Log::info($exceptionError->getTraceAsString());
        DUPX_Log::info("--------------------------------------");
        /**
         *   $exceptionError call in view
         */
        require_once($GLOBALS['DUPX_INIT'] . '/views/view.exception.php');
    }
	?>
</div>
</div>


<!-- SERVER INFO DIALOG -->
<div id="dialog-server-details" title="Setup Information" style="display:none">
	<!-- DETAILS -->
	<div class="dlg-serv-info">
		<?php
			$ini_path 		= php_ini_loaded_file();
			$ini_max_time 	= ini_get('max_execution_time');
			$ini_memory 	= ini_get('memory_limit');
			$ini_error_path = ini_get('error_log');
		?>
         <div class="hdr">SERVER DETAILS</div>
		<label>Web Server:</label>  			<?php echo DUPX_U::esc_html($_SERVER['SERVER_SOFTWARE']); ?><br/>
        <label>PHP Version:</label>  			<?php echo DUPX_U::esc_html(DUPX_Server::$php_version); ?><br/>
		<label>PHP INI Path:</label> 			<?php echo empty($ini_path ) ? 'Unable to detect loaded php.ini file' : $ini_path; ?>	<br/>
		<?php
		$php_sapi_name = php_sapi_name();
		?>
		<label>PHP SAPI:</label>  				<?php echo DUPX_U::esc_html($php_sapi_name); ?><br/>
		<label>PHP ZIP Archive:</label> 		<?php echo class_exists('ZipArchive') ? 'Is Installed' : 'Not Installed'; ?> <br/>
		<label>PHP max_execution_time:</label>  <?php echo $ini_max_time === false ? 'unable to find' : DUPX_U::esc_html($ini_max_time); ?><br/>
		<label>PHP memory_limit:</label>  		<?php echo empty($ini_memory)      ? 'unable to find' : DUPX_U::esc_html($ini_memory); ?><br/>
		<label>Error Log Path:</label>  		<?php echo empty($ini_error_path)      ? 'unable to find' : DUPX_U::esc_html($ini_error_path); ?><br/>

        <br/>
        <div class="hdr">PACKAGE BUILD DETAILS</div>
        <label>Plugin Version:</label>  		<?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_dup); ?><br/>
        <label>WordPress Version:</label>  		<?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_wp); ?><br/>
        <label>PHP Version:</label>             <?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_php); ?><br/>
        <label>Database Version:</label>        <?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_db); ?><br/>
        <label>Operating System:</label>        <?php echo DUPX_U::esc_html($GLOBALS['DUPX_AC']->version_os); ?><br/>

	</div>
</div>

<script>
DUPX.openServerDetails = function ()
{
	$("#dialog-server-details").dialog({
	  resizable: false,
	  height: "auto",
	  width: 700,
	  modal: true,
	  position: { my: 'top', at: 'top+150' },
	  buttons: {"OK": function() {$(this).dialog("close");} }
	});
}

$(document).ready(function ()
{
	//Disable href for toggle types
	$("a[data-type='toggle']").each(function() {
		$(this).attr('href', 'javascript:void(0)');
	});

});
</script>


<?php if ($GLOBALS['DUPX_DEBUG']) :?>
<form id="form-debug" method="post" action="?debug=1" autocomplete="off" >
		<input id="debug-view" type="hidden" name="view" />
		<br/><hr size="1" />
		DEBUG MODE ON
		<br/><br/>
		<a href="javascript:void(0)"  onclick="$('#debug-vars').toggle()"><b>PAGE VARIABLES</b></a>
		<pre id="debug-vars"><?php print_r($GLOBALS); ?></pre>
	</form>

	<script>
		DUPX.debugNavigate = function(view)
		{
		//TODO: Write app that captures all ajax requets and logs them to custom console.
		}
	</script>
<?php endif; ?>


<!-- Used for integrity check do not remove:
DUPLICATOR_INSTALLER_EOF -->
</body>
</html>
<?php
ob_end_flush(); // Flush the output from the buffer
