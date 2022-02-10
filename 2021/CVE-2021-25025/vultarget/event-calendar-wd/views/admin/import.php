<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

//global $ecwd_options;

?>

<div class="wrap">
    <?php settings_errors(); ?>
    <div id="ecwd-settings" class="ecwd-import-settings">
        <div id="ecwd-settings-content">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

            <form method="post" action="" enctype="multipart/form-data">
                <!-- import  -->
                <div id="ecwd-form-import" style='margin-top:10px;' class="ecwd-form-import">
                    <select name="ecwd__import">
                        <option value="events"><?php _e('Events', 'event-calendar-wd')?></option>
                        <option value="organizers"><?php _e('Organizers', 'event-calendar-wd')?></option>
                        <option value="venues"><?php _e('Venues', 'event-calendar-wd')?></option>
                    </select></br>
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>
                    <input type='file' name='ecwd_calendar_events_xml_file' class=''/>
					<div class="ecwd-calendar-event-import"><?php _e('Choose csv file', 'event-calendar-wd'); ?></div>
                </div>
                <?php submit_button(__('Import', 'event-calendar-wd') ); ?>
            </form>
            <!-- export -->
            <form method="post" action="">
                <div id="ecwd-form-export" style='margin-top:10px;' class="ecwd-form-export">
                    <div class="ecwd-calendar-event-import">
                        <select name="ecwd__export">
                            <option value="events"><?php _e('Events', 'event-calendar-wd')?></option>
                            <option value="organizers"><?php _e('Organizers', 'event-calendar-wd')?></option>
                            <option value="venues"><?php _e('Venues', 'event-calendar-wd')?></option>
                        </select>
                        <input type="hidden" name="ecwd_calendar_events_export" value="1"/>
                        <?php //_e('export events from this calendar as xml file', 'ecwd'); ?></div>
                    <?php submit_button( __('Download Export File', 'event-calendar-wd') )?>
                </div>
            </form>
        </div>
        <!-- #ecwd-settings-content -->
    </div>
    <!-- #ecwd-settings -->
</div><!-- .wrap -->