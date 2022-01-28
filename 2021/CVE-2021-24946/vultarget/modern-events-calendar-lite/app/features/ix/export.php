<?php
/** no direct access **/
defined('MECEXEC') or die();

$events = $this->main->get_events('-1');

// Settings
$settings = $this->main->get_settings();
?>
<div class="wrap" id="mec-wrap">
    <h1><?php _e('MEC Import / Export', 'modern-events-calendar-lite'); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo $this->main->remove_qs_var('tab'); ?>" class="nav-tab"><?php echo __('Google Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-g-calendar-export'); ?>" class="nav-tab"><?php echo __('Google Cal. Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-f-calendar-import'); ?>" class="nav-tab"><?php echo __('Facebook Cal. Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-meetup-import'); ?>" class="nav-tab"><?php echo __('Meetup Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-sync'); ?>" class="nav-tab"><?php echo __('Synchronization', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-export'); ?>" class="nav-tab nav-tab-active"><?php echo __('Export', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-import'); ?>" class="nav-tab"><?php echo __('Import', 'modern-events-calendar-lite'); ?></a>
        <a href="<?php echo $this->main->add_qs_var('tab', 'MEC-thirdparty'); ?>" class="nav-tab"><?php echo __('Third Party Plugins', 'modern-events-calendar-lite'); ?></a>
    </h2>
    <div class="mec-container">
        <div class="export-content w-clearfix extra">
            <div class="mec-export-all-events">
                <h3><?php _e('Export all events to file', 'modern-events-calendar-lite'); ?></h3>
                <p class="description"><?php _e("This will export all of your website events' data into your desired format.", 'modern-events-calendar-lite'); ?></p>
                <ul>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-events', 'format'=>'ical')); ?>"><?php _e('iCal', 'modern-events-calendar-lite'); ?></a></li>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-events', 'format'=>'csv')); ?>"><?php _e('CSV', 'modern-events-calendar-lite'); ?></a></li>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-events', 'format'=>'ms-excel')); ?>"><?php _e('MS Excel', 'modern-events-calendar-lite'); ?></a></li>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-events', 'format'=>'xml')); ?>"><?php _e('XML', 'modern-events-calendar-lite'); ?></a></li>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-events', 'format'=>'json')); ?>"><?php _e('JSON', 'modern-events-calendar-lite'); ?></a></li>
                </ul>
            </div>
            <div class="mec-export-certain-events">
                <h3><?php _e('Export certain events', 'modern-events-calendar-lite'); ?></h3>
                <p class="description"><?php echo sprintf(__("For exporting filtered events, you can use bulk actions in %s page.", 'modern-events-calendar-lite'), '<a href="'.$this->main->URL('backend').'edit.php?post_type=mec-events">'.__('Events', 'modern-events-calendar-lite').'</a>'); ?></p>
            </div>

            <?php if(isset($settings['ical_feed']) and $settings['ical_feed']): ?>
            <div class="mec-export-certain-events">
                <h3><?php _e('iCal Feed', 'modern-events-calendar-lite'); ?></h3>
                <p class="description"><?php echo sprintf(esc_html__('You can use %s URL to export all events. Also you can include the URL into your website so your website users can subscribe to events.', 'modern-events-calendar-lite'), '<a href="'.trim($this->main->URL('site'), '/ ').'/?mec-ical-feed=1" target="_blank">'.trim($this->main->URL('site'), '/ ').'/?mec-ical-feed=1</a>'); ?></p>
            </div>
            <?php endif; ?>

            <hr>
            <div class="mec-export-all-bookings">
                <h3><?php _e('Export all bookings to file', 'modern-events-calendar-lite'); ?></h3>
                <p class="description"><?php _e("This will export all of your website bookings' data into your desired format.", 'modern-events-calendar-lite'); ?></p>
                <ul>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-bookings', 'format'=>'csv')); ?>"><?php _e('CSV', 'modern-events-calendar-lite'); ?></a></li>
                    <li><a href="<?php echo $this->main->add_qs_vars(array('mec-ix-action'=>'export-bookings', 'format'=>'ms-excel')); ?>"><?php _e('MS Excel', 'modern-events-calendar-lite'); ?></a></li>
                </ul>
            </div>
            <div class="mec-export-certain-bookings">
                <h3><?php _e('Export certain bookings', 'modern-events-calendar-lite'); ?></h3>
                <p class="description"><?php echo sprintf(__("For exporting bookings, you can use bulk actions in %s page.", 'modern-events-calendar-lite'), '<a href="'.$this->main->URL('backend').'edit.php?post_type=mec-books">'.__('Bookings', 'modern-events-calendar-lite').'</a>'); ?></p>
            </div>

            <?php
                $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ) : '';
                do_action( 'mec_import_export_page', $tab );
            ?>
        </div>
    </div>
</div>