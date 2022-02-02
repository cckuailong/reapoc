<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
global $wpdb;

//POST BACK: Rest Button
if (isset($_POST['action'])) {
    $action = sanitize_text_field($_POST['action']);
    $action_result = DUP_Settings::DeleteWPOption($action);
    switch ($action) {
        case 'duplicator_package_active' :
            $action_result = DUP_Settings::DeleteWPOption($action);
            $action_response = __('Package settings have been reset.', 'duplicator');
            break;
    }
}

DUP_Util::initSnapshotDirectory();

$Package = DUP_Package::getActive();
$dup_tests = array();
$dup_tests = DUP_Server::getRequirements();

//View State
$ctrl_ui = new DUP_CTRL_UI();
$ctrl_ui->setResponseType('PHP');
$data = $ctrl_ui->GetViewStateList();

$ui_css_storage = (isset($data->payload['dup-pack-storage-panel']) && $data->payload['dup-pack-storage-panel']) ? 'display:block' : 'display:none';
$ui_css_archive = (isset($data->payload['dup-pack-archive-panel']) && $data->payload['dup-pack-archive-panel']) ? 'display:block' : 'display:none';
$ui_css_installer = (isset($data->payload['dup-pack-installer-panel']) && $data->payload['dup-pack-installer-panel']) ? 'display:block' : 'display:none';
$dup_intaller_files = implode(", ", array_keys(DUP_Server::getInstallerFiles()));
$dbbuild_mode = (DUP_Settings::Get('package_mysqldump') && DUP_DB::getMySqlDumpPath()) ? 'mysqldump' : 'PHP';
$archive_build_mode = DUP_Settings::Get('archive_build_mode') == DUP_Archive_Build_Mode::ZipArchive ? 'zip' : 'daf';

//="No Selection", 1="Try Again", 2="Two-Part Install"
$retry_state = isset($_GET['retry']) ? $_GET['retry'] : 0;
?>

<style>
    /* REQUIREMENTS*/
    div.dup-sys-section {margin:1px 0px 5px 0px}
    div.dup-sys-title {display:inline-block; width:250px; padding:1px; }
    div.dup-sys-title div {display:inline-block;float:right; }
    div.dup-sys-info {display:none; max-width: 98%; margin:4px 4px 12px 4px}	
    div.dup-sys-pass {display:inline-block; color:green;font-weight:bold}
    div.dup-sys-fail {display:inline-block; color:#AF0000;font-weight:bold}
    div.dup-sys-contact {padding:5px 0px 0px 10px; font-size:11px; font-style:italic}
    span.dup-toggle {float:left; margin:0 2px 2px 0; }
    table.dup-sys-info-results td:first-child {width:200px}
    table.dup-sys-info-results td:nth-child(2) {width:100px; font-weight:bold}
    table.dup-sys-info-results td:nth-child(3) {font-style:italic}
</style>


<!-- ============================
TOOL BAR: STEPS -->
<table id="dup-toolbar">
    <tr valign="top">
        <td style="white-space: nowrap">
            <div id="dup-wiz">
                <div id="dup-wiz-steps">
                    <div class="active-step"><a>1-<?php esc_html_e('Setup', 'duplicator'); ?></a></div>
                    <div><a>2-<?php esc_html_e('Scan', 'duplicator'); ?> </a></div>
                    <div><a>3-<?php esc_html_e('Build', 'duplicator'); ?> </a></div>
                </div>
                <div id="dup-wiz-title">
                    <?php esc_html_e('Step 1: Package Setup', 'duplicator'); ?>
                </div> 
            </div>	
        </td>
        <td>
            <a href="?page=duplicator" class="button"><i class="fa fa-archive fa-sm"></i> <?php esc_html_e("Packages", 'duplicator'); ?></a>
            <a href="javascript:void(0)" class="button disabled"> <?php esc_html_e("Create New", 'duplicator'); ?></a>
        </td>
    </tr>
</table>	
<hr class="dup-toolbar-line">

<?php if (!empty($action_response)) : ?>
    <div id="message" class="notice notice-success is-dismissible"><p><?php echo esc_html($action_response); ?></p></div>
<?php endif; ?>	


<!-- ============================
SYSTEM REQUIREMENTS -->
<?php if (!$dup_tests['Success'] || $dup_tests['Warning']) : ?>
    <div class="dup-box">
        <div class="dup-box-title">
            <?php
            esc_html_e("Requirements:", 'duplicator');
            echo ($dup_tests['Success']) ? ' <div class="dup-sys-pass">Pass</div>' : ' <div class="dup-sys-fail">Fail</div>';
            ?>
            <div class="dup-box-arrow"></div>
        </div>

        <div class="dup-box-panel">

            <div class="dup-sys-section">
                <i><?php esc_html_e("System requirements must pass for the Duplicator to work properly.  Click each link for details.", 'duplicator'); ?></i>
            </div>

            <!-- PHP SUPPORT -->
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a><?php esc_html_e('PHP Support', 'duplicator'); ?></a>
                    <div><?php echo esc_html($dup_tests['PHP']['ALL']); ?></div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <table class="dup-sys-info-results">
                        <tr>
                            <td><?php printf("%s [%s]", esc_html__("PHP Version", 'duplicator'), phpversion()); ?></td>
                            <td><?php echo esc_html($dup_tests['PHP']['VERSION']); ?></td>
                            <td><?php esc_html_e('PHP versions 5.2.9+ or higher is required.')?></td>
                        </tr>
                        <?php if ($archive_build_mode == 'zip') : ?>
                            <tr>
                                <td><?php esc_html_e('Zip Archive Enabled', 'duplicator'); ?></td>
                                <td><?php echo esc_html($dup_tests['PHP']['ZIP']); ?></td>
                                <td>
                                    <?php printf("%s <a href='admin.php?page=duplicator-settings&tab=package'>%s</a> %s", 
                                        esc_html__("ZipArchive extension is required or", 'duplicator'),
                                        esc_html__("Switch to DupArchive", 'duplicator'),
                                        esc_html__("to by-pass this requirement.", 'duplicator'));
                                   ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td><?php esc_html_e('Safe Mode Off', 'duplicator'); ?></td>
                            <td><?php echo esc_html($dup_tests['PHP']['SAFE_MODE']); ?></td>
                            <td><?php esc_html_e('Safe Mode should be set to Off in you php.ini file and is deprecated as of PHP 5.3.0.')?></td>
                        </tr>					
                        <tr>
                            <td><?php esc_html_e('Function', 'duplicator'); ?> <a href="http://php.net/manual/en/function.file-get-contents.php" target="_blank">file_get_contents</a></td>
                            <td><?php echo esc_html($dup_tests['PHP']['FUNC_1']); ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>					
                        <tr>
                            <td><?php esc_html_e('Function', 'duplicator'); ?> <a href="http://php.net/manual/en/function.file-put-contents.php" target="_blank">file_put_contents</a></td>
                            <td><?php echo esc_html($dup_tests['PHP']['FUNC_2']); ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Function', 'duplicator'); ?> <a href="http://php.net/manual/en/mbstring.installation.php" target="_blank">mb_strlen</a></td>
                            <td><?php echo esc_html($dup_tests['PHP']['FUNC_3']); ?></td>
                            <td><?php echo ''; ?></td>
                        </tr>					
                    </table>
                    <small>
                        <?php esc_html_e("For any issues in this section please contact your hosting provider or server administrator.  For additional information see our online documentation.", 'duplicator'); ?>
                    </small>
                </div>
            </div>		

            <!-- PERMISSIONS -->
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a><?php esc_html_e('Required Paths', 'duplicator'); ?></a>
                    <div>
                        <?php
                        if ($dup_tests['IO']['ALL']) {
                            echo ($dup_tests['IO']['WPROOT'] == 'Warn') ? 'Warn' : 'Pass';
                        } else {
                            echo 'Fail';
                        }
                        ?>
                    </div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <?php
                    $abs_path = duplicator_get_abs_path();

                    printf("<b>%s</b> &nbsp; [%s] <br/>", $dup_tests['IO']['SSDIR'], DUPLICATOR_SSDIR_PATH);
                    printf("<b>%s</b> &nbsp; [%s] <br/>", $dup_tests['IO']['SSTMP'], DUPLICATOR_SSDIR_PATH_TMP);
                    printf("<b>%s</b> &nbsp; [%s] <br/>", $dup_tests['IO']['WPROOT'], $abs_path);
                    ?>
                    <div style="font-size:11px; padding-top: 3px">
                        <?php
                        if ($dup_tests['IO']['WPROOT'] == 'Warn') {
                            echo sprintf(__('If the root WordPress path is not writable by PHP on some systems this can cause issues.', 'duplicator'), $abs_path);
                            echo '<br/>';
                        }
                        esc_html_e("If Duplicator does not have enough permissions then you will need to manually create the paths above. &nbsp; ", 'duplicator');
                        ?>
                    </div>
                </div>
            </div>

            <!-- SERVER SUPPORT -->
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a><?php esc_html_e('Server Support', 'duplicator'); ?></a>
                    <div><?php echo esc_html($dup_tests['SRV']['ALL']); ?></div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <table class="dup-sys-info-results">
                        <tr>
                            <td><?php printf("%s [%s]", esc_html__("MySQL Version", 'duplicator'), esc_html(DUP_DB::getVersion())); ?></td>
                            <td><?php echo esc_html($dup_tests['SRV']['MYSQL_VER']); ?></td>
                        </tr>
                        <tr>
                            <td><?php printf("%s", esc_html__("MySQLi Support", 'duplicator')); ?></td>
                            <td><?php echo esc_html($dup_tests['SRV']['MYSQLi']); ?></td>
                        </tr>
                    </table>
                    <small>
                        <?php
                        esc_html_e("MySQL version 5.0+ or better is required and the PHP MySQLi extension (note the trailing 'i') is also required.  Contact your server administrator and request that mysqli extension and MySQL Server 5.0+ be installed.", 'duplicator');
                        echo "&nbsp;<i><a href='http://php.net/manual/en/mysqli.installation.php' target='_blank'>[" . esc_html__('more info', 'duplicator') . "]</a></i>";
                        ?>										
                    </small>
                </div>
            </div>

            <!-- RESERVED FILES -->
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a><?php esc_html_e('Reserved Files', 'duplicator'); ?></a> <div><?php echo esc_html($dup_tests['RES']['INSTALL']); ?></div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <?php if ($dup_tests['RES']['INSTALL'] == 'Pass') : ?>
                        <?php
                        esc_html_e("None of the reserved files where found from a previous install.  This means you are clear to create a new package.", 'duplicator');
                        echo "  [".esc_html($dup_intaller_files)."]";
                        ?>
                    <?php
                    else:
                        $duplicator_nonce = wp_create_nonce('duplicator_cleanup_page');
                        ?> 
                        <form method="post" action="admin.php?page=duplicator-tools&tab=diagnostics&section=info&action=installer&_wpnonce=<?php echo esc_js($duplicator_nonce); ?>">
                            <b><?php esc_html_e('WordPress Root Path:', 'duplicator'); ?></b>  <?php echo esc_html(duplicator_get_abs_path()); ?><br/>
                            <?php esc_html_e("A reserved file(s) was found in the WordPress root directory. Reserved file names include [{$dup_intaller_files}].  To archive your data correctly please remove any of these files from your WordPress root directory.  Then try creating your package again.", 'duplicator'); ?>
                            <br/><input type='submit' class='button button-small' value='<?php esc_attr_e('Remove Files Now', 'duplicator') ?>' style='font-size:10px; margin-top:5px;' />
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div><br/>
<?php endif; ?>


<!-- ============================
FORM PACKAGE OPTIONS -->
<div style="padding:5px 5px 2px 5px">
    <?php include('s1.setup2.php'); ?>
</div>

<!-- CACHE PROTECTION: If the back-button is used from the scanner page then we need to
refresh page in-case any filters where set while on the scanner page -->
<form id="cache_detection">
    <input type="hidden" id="cache_state" name="cache_state" value="" />
</form>

<script>
    jQuery(document).ready(function ($)
    {
        Duplicator.Pack.checkPageCache = function ()
        {
            var $state = $('#cache_state');
            if ($state.val() == "") {
                $state.val("fresh-load");
            } else {
                $state.val("cached");
                <?php
                $redirect = admin_url('admin.php?page=duplicator&tab=new1');
                $redirect_nonce_url = wp_nonce_url($redirect, 'new1-package');
                echo "window.location.href = '{$redirect_nonce_url}'";
                ?>
            }
        }

        //INIT
        Duplicator.Pack.checkPageCache();

        //Toggle for system requirement detail links
        $('.dup-sys-title a').each(function () {
            $(this).attr('href', 'javascript:void(0)');
            $(this).click({selector: '.dup-sys-info'}, Duplicator.Pack.ToggleSystemDetails);
            $(this).prepend("<span class='ui-icon ui-icon-triangle-1-e dup-toggle' />");
        });

        //Color code Pass/Fail/Warn items
        $('.dup-sys-title div').each(function () {
            console.log($(this).text());
            var state = $(this).text().trim();
            $(this).removeClass();
            $(this).addClass((state == 'Pass') ? 'dup-sys-pass' : 'dup-sys-fail');
        });
    });
</script>