<?php
/** no direct access **/
defined('MECEXEC') or die();
?>

<div class="wns-be-container wns-be-container-sticky">

    <div id="wns-be-infobar"></div>

    <div class="wns-be-sidebar">
        <?php $this->main->get_sidebar_menu('ie'); ?>
    </div>

    <div class="wns-be-main">

        <div id="wns-be-notification"></div>

        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <h2><?php _e('Import', 'modern-events-calendar-lite'); ?></h2>
                <p><?php _e('Insert your backup files below and press import to restore your site\'s options to the last backup.', 'modern-events-calendar-lite'); ?></p>
                <p style="color:#d80000; margin-bottom: 25px;"><?php _e('WARNING! Restoring backup will overwrite all of your current option values. Caution Indeed.', 'modern-events-calendar-lite'); ?></p>
                <div class="mec-import-settings-wrap">
                    <textarea class="mec-import-settings-content" placeholder="<?php esc_html_e('Please paste your options here', 'modern-events-calendar-lite'); ?>"></textarea>
                </div>
                <a class="mec-import-settings" href="#"><?php _e("Import Settings", 'modern-events-calendar-lite'); ?></a>
                <div class="mec-import-options-notification"></div>

                <h2 style="margin-top: 40px;"><?php _e('Export', 'modern-events-calendar-lite'); ?></h2>
                <?php
                    $nonce = wp_create_nonce("mec_settings_download");
                    $export_link = admin_url('admin-ajax.php?action=download_settings&nonce='.$nonce);
                ?>
                <a class="mec-export-settings" href="<?php echo $export_link; ?>"><?php _e("Download Settings", 'modern-events-calendar-lite'); ?></a>
            </div>
        </div>

    </div>

</div>