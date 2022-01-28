<?php
/** no direct access **/
defined('MECEXEC') or die();

$third_parties = $this->main->get_integrated_plugins_for_import();
?>
<div class="wrap" id="mec-wrap">
    <h1><?php _e('MEC Import / Export', 'modern-events-calendar-lite'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo $this->main->remove_qs_var('tab'); ?>" class="nav-tab"><?php echo __('Google Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-g-calendar-export'); ?>" class="nav-tab"><?php echo __('Google Cal. Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-f-calendar-import'); ?>" class="nav-tab"><?php echo __('Facebook Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-meetup-import'); ?>" class="nav-tab"><?php echo __('Meetup Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-sync'); ?>" class="nav-tab"><?php echo __('Synchronization', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-export'); ?>" class="nav-tab"><?php echo __('Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-import'); ?>" class="nav-tab nav-tab-active"><?php echo __('Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-thirdparty'); ?>" class="nav-tab"><?php echo __('Third Party Plugins', 'modern-events-calendar-lite'); ?></a>
    </h2>
    <div class="mec-container">
        <div class="import-content w-clearfix extra">
            <h3><?php _e('Import MEC XML Feed', 'modern-events-calendar-lite'); ?></h3>
            <form id="mec_import_xml_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST" enctype="multipart/form-data">
                <div class="mec-form-row">
                    <p><?php echo sprintf(__("You can import %s events from another website to this website. You just need an XML feed of the events that can be exported from source website!", 'modern-events-calendar-lite'), '<strong>'.__('Modern Events Calendar', 'modern-events-calendar-lite').'</strong>'); ?></p>
                </div>
                <div class="mec-form-row">
                    <input type="file" name="feed" id="feed" title="<?php esc_attr_e('XML Feed', 'modern-events-calendar-lite'); ?>">
                    <input type="hidden" name="mec-ix-action" value="import-start-xml">
                    <?php wp_nonce_field('mec_import_start_upload'); ?>
                    <button class="button button-primary mec-button-primary mec-btn-2"><?php _e('Upload & Import', 'modern-events-calendar-lite'); ?></button>
                </div>
            </form>

            <?php if($this->action == 'import-start-xml'): ?>
                <div class="mec-ix-import-started">
                    <?php if($this->response['success'] == 0): ?>
                        <div class="mec-error"><?php echo $this->response['message']; ?></div>
                    <?php else: ?>
                        <div class="mec-success"><?php echo $this->response['message']; ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <br><h3><?php _e('Import .ics File', 'modern-events-calendar-lite'); ?></h3>
            <?php if($this->getPRO()): ?>
            <form id="mec_import_ics_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST" enctype="multipart/form-data">
                <div class="mec-form-row">
                    <p><?php echo sprintf(__("ICS format supports by many different service providers like Facebook. Apple Calendar etc. You can import your ics file into the %s using this form.", 'modern-events-calendar-lite'), '<strong>'.__('Modern Events Calendar', 'modern-events-calendar-lite').'</strong>'); ?></p>
                </div>
                <div class="mec-form-row">
                    <input type="file" name="feed" id="feed" title="<?php esc_attr_e('ICS Feed', 'modern-events-calendar-lite'); ?>">
                    <input type="hidden" name="mec-ix-action" value="import-start-ics">
                    <?php wp_nonce_field('mec_import_start_upload'); ?>
                    <button class="button button-primary mec-button-primary mec-btn-2"><?php _e('Upload & Import', 'modern-events-calendar-lite'); ?></button>
                </div>
            </form>
            <?php else: ?>
            <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
            <?php endif; ?>

            <?php if($this->action == 'import-start-ics'): ?>
            <div class="mec-ix-import-started">
                <?php if($this->response['success'] == 0): ?>
                <div class="mec-error"><?php echo $this->response['message']; ?></div>
                <?php else: ?>
                <div class="mec-success"><?php echo $this->response['message']; ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php do_action('mec_import_item', $this); ?>

            <br><h3><?php _e('Import Booking CSV File', 'modern-events-calendar-lite'); ?></h3>
            <?php if($this->getPRO()): ?>
            <form id="mec_import_csv_booking_form" action="<?php echo $this->main->get_full_url(); ?>" method="POST" enctype="multipart/form-data">
                <div class="mec-form-row">
                    <p><?php echo sprintf(__("You can export bookings from %s using the booking menu in source website. You need a CSV export and then you're able to simply import it using this form in to your target website.", 'modern-events-calendar-lite'), '<strong>'.__('Modern Events Calendar', 'modern-events-calendar-lite').'</strong>'); ?></p>
                    <p style="color: red;"><?php echo __("Please note that you should create (or imports) events and tickets before importing the bookings otherwise booking won't import due to lack of data.", 'modern-events-calendar-lite'); ?></p>
                </div>
                <div class="mec-form-row">
                    <input type="file" name="feed" id="feed" title="<?php esc_attr_e('CSV File', 'modern-events-calendar-lite'); ?>">
                    <input type="hidden" name="mec-ix-action" value="import-start-bookings">
                    <?php wp_nonce_field('mec_import_start_upload'); ?>
                    <button class="button button-primary mec-button-primary mec-btn-2"><?php _e('Upload & Import', 'modern-events-calendar-lite'); ?></button>
                </div>
            </form>
            <?php else: ?>
            <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
            <?php endif; ?>

            <?php if($this->action == 'import-start-bookings'): ?>
            <div class="mec-ix-import-started">
                <?php if($this->response['success'] == 0): ?>
                <div class="mec-error"><?php echo $this->response['message']; ?></div>
                <?php else: ?>
                <div class="mec-success"><?php echo $this->response['message']; ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php
            $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ) : '';
            do_action( 'mec_import_export_page', $tab );
        ?>
    </div>
</div>