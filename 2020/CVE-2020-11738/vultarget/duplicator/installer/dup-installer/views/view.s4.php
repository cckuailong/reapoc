<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/** IDE HELPERS */
/* @var $GLOBALS['DUPX_AC'] DUPX_ArchiveConfig */

$_POST['exe_safe_mode']	= isset($_POST['exe_safe_mode']) ? DUPX_U::sanitize_text_field($_POST['exe_safe_mode']) : 0;

$url_new_rtrim  = rtrim(DUPX_U::sanitize_text_field($_POST['url_new']), "/");
$admin_base		= basename($GLOBALS['DUPX_AC']->wplogin_url);
$admin_redirect ="{$url_new_rtrim}/wp-admin/admin.php?page=duplicator-tools&tab=diagnostics";

$safe_mode		= DUPX_U::sanitize_text_field($_POST['exe_safe_mode']);
$admin_redirect = "{$admin_redirect}&in={$GLOBALS['BOOTLOADER_NAME']}&sm={$safe_mode}" ;
$admin_redirect = urlencode($admin_redirect);
$admin_url_qry  = (strpos($admin_base, '?') === false) ? '?' : '&';
$admin_login	= "{$url_new_rtrim}/{$admin_base}{$admin_url_qry}redirect_to={$admin_redirect}";

// Few machines doen's have utf8_decode
if (!function_exists('utf8_decode')) {
    function utf8_decode($s) {
        $s = (string) $s;
        $len = strlen($s);
        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;
                case "\xF0":
                    ++$i;
                    // no break
                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;
                default:
                    $s[$j] = $s[$i];
            }
        }
        return substr($s, 0, $j);
    }
}
//Sanitize
$json_result = true;
$_POST['json'] = isset($_POST['json']) ? DUPX_U::esc_attr($_POST['json']) : 'json data not set';
$json_data   = utf8_decode(urldecode($_POST['json']));
$json_decode = json_decode($json_data);
if ($json_decode == NULL || $json_decode == FALSE) {
    $json_data  = "{'json reset invalid form value sent.  Possible site script attempt'}";
    $json_result = false;
}

?>

<script>
	DUPX.getAdminLogin = function()
    {
		if ($('input#auto-delete').is(':checked')) {
			var action = encodeURIComponent('&action=installer');
			window.open('<?php echo $admin_login; ?>' + action, '_blank');
		} else {
			window.open('<?php echo $admin_login; ?>', '_blank');
		}
	};
</script>

<!-- =========================================
VIEW: STEP 4- INPUT -->
<form id='s4-input-form' method="post" class="content-form" style="line-height:20px" autocomplete="off">
	<input type="hidden" name="url_new" id="url_new" value="<?php echo DUPX_U::esc_attr($url_new_rtrim); ?>" />
	<div class="logfile-link"><?php DUPX_View_Funcs::installerLogLink(); ?></div>

	<div class="hdr-main">
		Step <span class="step">4</span> of 4: Test Site
	</div><br/>

	<!--  POST PARAMS -->
	<div class="dupx-debug">
		<i>Step 4 - Page Load</i>
		<input type="hidden" name="view" value="step4" />
		<input type="hidden" name="csrf_token" value="<?php echo DUPX_CSRF::generate('step4'); ?>">
		<input type="hidden" name="exe_safe_mode" id="exe-safe-mode" value="<?php echo DUPX_U::esc_attr($_POST['exe_safe_mode']); ?>" />
	</div>

	<table class="s4-final-step">
		<tr style="vertical-align: top">
			<td style="padding-top:10px">
				<button type="button" class="s4-final-btns" onclick="DUPX.getAdminLogin()"><i class="fab fa-wordpress"></i> Admin Login</button>
			</td>
			<td>
				Login to the WordPress Admin to finalize this install.<br/>
				<input type="checkbox" name="auto-delete" id="auto-delete" checked="true"/>
				<label for="auto-delete">Auto delete installer files after login <small>(recommended)</small></label>
				<br/><br/>
								
				<!-- WARN: SAFE MODE MESSAGES -->
				<div class="s4-warn" style="display:<?php echo ($safe_mode > 0 ? 'block' : 'none')?>">
					<b>Safe Mode</b><br/>
					Safe mode has <u>deactivated</u> all plugins. Please be sure to enable your plugins after logging in. <i>If you notice that problems arise when activating
					the plugins then active them one-by-one to isolate the plugin that	could be causing the issue.</i>
				</div>
			</td>
		</tr>
	</table>
	<i style="color:maroon; font-size:12px">
		<b><i class="fa fa-exclamation-triangle fa-sm"></i> IMPORTANT FINAL STEPS:</b> Login into the WordPress Admin to remove all <?php 
        DUPX_View_Funcs::helpLink('step4', 'installation files'); ?> and finalize the install process.
        This install is NOT complete until all installer files are removed.  Leaving the installer files on this server can lead to security issues.
	</i>
	<br/><br/><br/>

    <?php
    $nManager = DUPX_NOTICE_MANAGER::getInstance();
    if ($json_decode->step1->query_errs > 0) {
        $linkAttr = './'.DUPX_U::esc_attr($GLOBALS["LOG_FILE_NAME"]);
        $longMsg  = <<<LONGMSG
        Queries that error during the deploy step are logged to the <a href="{$linkAttr}" target="dup-installer">install-log.txt</a> file and
and marked with an **ERROR** status.   If you experience a few errors (under 5), in many cases they can be ignored as long as your site is working correctly.
However if you see a large amount of errors or you experience an issue with your site then the error messages in the log file will need to be investigated.
<br/><br/>

<b>COMMON FIXES:</b>
<ul>
    <li>
        <b>Unknown collation:</b> See Online FAQ:
        <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q" target="_blank">What is Compatibility mode & 'Unknown collation' errors?</a>
    </li>
    <li>
        <b>Query Limits:</b> Update MySQL server with the <a href="https://dev.mysql.com/doc/refman/5.5/en/packet-too-large.html" target="_blank">max_allowed_packet</a>
        setting for larger payloads.
    </li>
</ul>
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'STEP 2 - INSTALL NOTICES ('.$json_decode->step1->query_errs.')',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode'=> DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections' => array('database'),
            'priority' => 5,
            'open' => true
        ));
    }

    if ($json_decode->step3->errsql_sum > 0) {
        $longMsg = <<<LONGMSG
            Update errors that show here are queries that could not be performed because the database server being used has issues running it.  Please validate the query, if
			it looks to be of concern please try to run the query manually.  In many cases if your site performs well without any issues you can ignore the error.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'STEP 3 - UPDATE NOTICES ('.$json_decode->step3->errsql_sum.')',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => $longMsg,
            'sections' => array('database'),
            'priority' => 5,
            'open' => true
        ));
    }

    if ($json_decode->step3->errkey_sum > 0) {
        $longMsg = <<<LONGMSG
            Notices should be ignored unless issues are found after you have tested an installed site. This notice indicates that a primary key is required to run the
            update engine. Below is a list of tables and the rows that were not updated.  On some databases you can remove these notices by checking the box 'Enable Full Search'
            under options in step3 of the installer.
            <br/><br/>
            <small>
                <b>Advanced Searching:</b><br/>
                Use the following query to locate the table that was not updated: <br/>
                <i>SELECT @row := @row + 1 as row, t.* FROM some_table t, (SELECT @row := 0) r</i>
            </small>
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'TABLE KEY NOTICES  ('.$json_decode->step3->errkey_sum.')',
            'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg' => $longMsg,
            'longMsgMode'=> DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections' => array('database'),
            'priority' => 5,
            'open' => true
        ));
    }

    if ($json_decode->step3->errser_sum > 0) {
        $longMsg = <<<LONGMSG
            Notices should be ignored unless issues are found after you have tested an installed site.  The SQL below will show data that may have not been
            updated during the serialization process.  Best practices for serialization notices is to just re-save the plugin/post/page in question.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'SERIALIZATION NOTICES  ('.$json_decode->step3->errser_sum.')',
            'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg' => $longMsg,
            'sections' => array('search_replace'),
            'priority' => 5,
            'open' => true
        ));
    }

    $numGeneralNotices = $nManager->countFinalReportNotices('general', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numGeneralNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No general notices',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'sections' => array('general'),
            'priority' => 5
        ));
    } else {
        $longMsg = <<<LONGMSG
            The following is a list of notices that may need to be fixed in order to finalize your setup. These values should only be investigated if you're running into
            issues with your site. For more details see the <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">WordPress Codex</a>.
LONGMSG;

        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Info',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => $longMsg,
            'longMsgMode'=> DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            'sections' => array('general'),
            'priority' => 5,
            'open' => true
        ));
    }

    $numDbNotices = $nManager->countFinalReportNotices('database', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numDbNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No errors in database',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else if ($numDbNotices <= 10) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Some errors in the database ('.$numDbNotices.')',
            'level' => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg' => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else if ($numDbNotices <= 100) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Errors in the database ('.$numDbNotices.')',
            'level' => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsg' => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    } else {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'Many errors in the database ('.$numDbNotices.')',
            'level' => DUPX_NOTICE_ITEM::CRITICAL,
            'longMsg' => '',
            'sections' => 'database',
            'priority' => 5
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
    }


    $numSerNotices = $nManager->countFinalReportNotices('search_replace', DUPX_NOTICE_ITEM::NOTICE, '>=');
    if ($numSerNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No search and replace data errors',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => '',
            'sections' => 'search_replace',
            'priority' => 5
        ));
    }

    $numFilesNotices = $nManager->countFinalReportNotices('files', DUPX_NOTICE_ITEM::NOTICE, '>=');

    if ($numFilesNotices == 0) {
        $nManager->addFinalReportNotice(array(
            'shortMsg' => 'No files extraction errors',
            'level' => DUPX_NOTICE_ITEM::INFO,
            'longMsg' => '',
            'sections' => 'files',
            'priority' => 5
        ));
    }

    $nManager->sortFinalReport();
    $nManager->finalReportLog(array('general','files','database','search_replace'));
    ?>

	<div class="s4-go-back">
		Additional Details:
		<ul style="margin-top: 1px">
			<li>
                <a href="javascript:void(0)" onclick="$('#s4-install-report').toggle(400)">Review Migration Report</a><br/><br>
                <table class='s4-report-results' style="width:100%">
                    <tbody>
                        <tr>
                            <td>Files notices</td>
                            <td>(<?php echo $numFilesNotices; ?>)</td>
                            <td> <?php $nManager->getSectionErrLevelHtml('files'); ?></td>
                        </tr>
                        <tr>
                            <td>Database Notices</td>
                            <td>(<?php echo $numDbNotices; ?>)</td>
                            <td><?php $nManager->getSectionErrLevelHtml('database'); ?></td>
                        </tr>
                        <tr>
                            <td>Search &amp; Replace Notices</td>
                            <td>(<?php echo $numSerNotices; ?>)</td>
                            <td> <?php $nManager->getSectionErrLevelHtml('search_replace'); ?></td>
                        </tr>
                        <tr>
                            <td>General Notices</td>
                            <td>(<?php echo $numGeneralNotices; ?>)</td>
                            <td><?php $nManager->getSectionErrLevelHtml('general'); ?></td>
                        </tr>
                    </tbody>
                </table><br>
			</li>
			<li>
				Review this sites <a href="<?php echo DUPX_U::esc_attr($url_new_rtrim); ?>" target="_blank">front-end</a> or
				re-run the installer and <a href="<?php echo DUPX_U::esc_url("{$url_new_rtrim}/installer.php"); ?>">go back to step 1</a>
			</li>
            <?php
            $wpconfigNotice = $nManager->getFinalReporNoticeById('wp-config-changes');
            $htaccessNorice = $nManager->getFinalReporNoticeById('htaccess-changes');
            ?>
			<li>Please validate <?php echo $wpconfigNotice->longMsg; ?> and <?php echo $htaccessNorice->longMsg; ?></li>
			<li>For additional help and questions visit the <a href='https://snapcreek.com/duplicator/docs/faqs-tech/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=inst4_step4_troubleshoot' target='_blank'>online FAQs</a></li>
		</ul>
	</div>

	<!-- ========================
	INSTALL REPORT -->
	<div id="s4-install-report" style='display:none'>
		<table class='s4-report-results' style="width:100%">
			<tr><th colspan="4"><i class="fas fa-database fa-sm"></i> Database Report</th></tr>
			<tr style="font-weight:bold">
				<td style="width:150px"></td>
				<td>Tables</td>
				<td>Rows</td>
				<td>Cells</td>
			</tr>
			<tr data-bind="with: status.step1">
				<td>Created</td>
				<td><span data-bind="text: table_count"></span></td>
				<td><span data-bind="text: table_rows"></span></td>
				<td>n/a</td>
			</tr>
			<tr data-bind="with: status.step3">
				<td>Scanned</td>
				<td><span data-bind="text: scan_tables"></span></td>
				<td><span data-bind="text: scan_rows"></span></td>
				<td><span data-bind="text: scan_cells"></span></td>
			</tr>
			<tr data-bind="with: status.step3">
				<td>Updated</td>
				<td><span data-bind="text: updt_tables"></span></td>
				<td><span data-bind="text: updt_rows"></span></td>
				<td><span data-bind="text: updt_cells"></span></td>
			</tr>
		</table>
		<br/>

        <div id="s4-notice-reports" class="report-sections-list">
            <?php
                $nManager->displayFinalRepostSectionHtml('files' , 'Files notices report');
                $nManager->displayFinalRepostSectionHtml('database' , 'Database Notices');
                $nManager->displayFinalRepostSectionHtml('search_replace' , 'Search &amp; Replace Notices');
                $nManager->displayFinalRepostSectionHtml('general' , 'General Notices');
            ?>
        </div>

	</div><br/><br/>

	<div class='s4-connect' style="display:none">
		<a href='http://snapcreek.com/support/docs/faqs/' target='_blank'>FAQs</a> |
		<a href='https://snapcreek.com' target='_blank'>Support</a>
	</div><br/>

    <?php  
        // Switched to Get Duplicator Pro wording based on split testing results
        $key = 'free_inst_s3btn_dp1324';
        $txt = 'Get Duplicator Pro!';
	?>
    
    

	<div class="s4-gopro-btn">
		<a href="https://snapcreek.com/duplicator/?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=duplicator_pro&utm_content=<?php echo DUPX_U::esc_attr($key);?>" target="_blank">
			<?php echo DUPX_U::esc_html($txt);?>
		</a>
	</div>
	<br/><br/><br/>
</form>

<script>
<?php if ($json_result) : ?>
	MyViewModel = function() {
		this.status = <?php echo $json_data; ?>;
		var errorCount =  this.status.step1.query_errs || 0;
		(errorCount >= 1 )
			? $('#s4-install-report-count').css('color', '#BE2323')
			: $('#s4-install-report-count').css('color', '#197713')
	};
	ko.applyBindings(new MyViewModel());
<?php else: ?>
	console.log("Cross site script attempt detected, unable to create final report!");
<?php endif; ?>

//DOCUMENT LOAD
$(document).ready(function () {

    //INIT Routines
	$("*[data-type='toggle']").click(DUPX.toggleClick);
	$("#tabs").tabs();

});
</script>



