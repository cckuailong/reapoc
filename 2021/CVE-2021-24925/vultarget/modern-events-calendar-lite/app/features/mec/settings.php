<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_mec $this */

$settings = $this->main->get_settings();
$archive_skins = $this->main->get_archive_skins();
$category_skins = $this->main->get_category_skins();

$currencies = $this->main->get_currencies();

// WordPress Pages
$pages = get_pages();

echo $this->main->mec_custom_msg_2('yes', 'yes');
echo $this->main->mec_custom_msg('', '');

// Display Addons Notification
$get_n_option = get_option('mec_addons_notification_option');

$shortcodes = get_posts(array(
    'post_type' => 'mec_calendars',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'order' => 'DESC'
));
?>
<div class="wns-be-container wns-be-container-sticky">
    <div id="wns-be-infobar">
        <div class="mec-search-settings-wrap">
            <i class="mec-sl-magnifier"></i>
            <input id="mec-search-settings" type="text" placeholder="<?php esc_html_e('Search...' ,'modern-events-calendar-lite'); ?>">
        </div>
        <a id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

    <div class="wns-be-sidebar">
        <?php $this->main->get_sidebar_menu('settings'); ?>
    </div>

    <div class="wns-be-main">
        <div id="wns-be-notification"></div>
        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">

                    <form id="mec_settings_form">

                        <div id="general_option" class="mec-options-fields active">

                            <h4 class="mec-form-subtitle"><?php _e('General', 'modern-events-calendar-lite'); ?></h4>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_hide_time_method"><?php _e('Hide Events', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_time_format" name="mec[settings][hide_time_method]">
                                        <option value="start" <?php if(isset($settings['hide_time_method']) and 'start' == $settings['hide_time_method']) echo 'selected="selected"'; ?>><?php _e('On Event Start', 'modern-events-calendar-lite'); ?></option>
                                        <option value="plus1" <?php if(isset($settings['hide_time_method']) and 'plus1' == $settings['hide_time_method']) echo 'selected="selected"'; ?>><?php _e('+1 Hour after start', 'modern-events-calendar-lite'); ?></option>
                                        <option value="plus2" <?php if(isset($settings['hide_time_method']) and 'plus2' == $settings['hide_time_method']) echo 'selected="selected"'; ?>><?php _e('+2 Hours after start', 'modern-events-calendar-lite'); ?></option>
                                        <option value="end" <?php if(isset($settings['hide_time_method']) and 'end' == $settings['hide_time_method']) echo 'selected="selected"'; ?>><?php _e('On Event End', 'modern-events-calendar-lite'); ?></option>
                                        <?php do_action('mec_hide_time_methods', $settings); ?>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Hide Events', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("This option is for showing start/end time of events on frontend of website.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">

                                <label class="mec-col-3" for="mec_settings_multiple_day_show_method"><?php _e('Multiple Day Events Show', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_multiple_day_show_method" name="mec[settings][multiple_day_show_method]">
                                        <option value="first_day_listgrid" <?php if(isset($settings['multiple_day_show_method']) and $settings['multiple_day_show_method'] == 'first_day_listgrid') echo 'selected="selected"'; ?>><?php _e('First day on list/grid/slider/agenda skins', 'modern-events-calendar-lite'); ?></option>
                                        <option value="first_day" <?php if(isset($settings['multiple_day_show_method']) and $settings['multiple_day_show_method'] == 'first_day') echo 'selected="selected"'; ?>><?php _e('First day on all skins', 'modern-events-calendar-lite'); ?></option>
                                        <option value="all_days" <?php if(isset($settings['multiple_day_show_method']) and $settings['multiple_day_show_method'] == 'all_days') echo 'selected="selected"'; ?>><?php _e('All days', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Multiple Day Events', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("For showing all days of multiple day events on frontend or only show the first day.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>

                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_remove_data_on_uninstall"><?php _e('Remove MEC Data on Plugin Uninstall', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_remove_data_on_uninstall" name="mec[settings][remove_data_on_uninstall]">
                                        <option value="0" <?php if(isset($settings['remove_data_on_uninstall']) and !$settings['remove_data_on_uninstall']) echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                                        <option value="1" <?php if(isset($settings['remove_data_on_uninstall']) and $settings['remove_data_on_uninstall'] == '1') echo 'selected="selected"'; ?>><?php _e('Enabled', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3"><?php _e('Exclude Date Suffix', 'modern-events-calendar-lite'); ?></label>
                                <label>
                                    <input type="hidden" name="mec[settings][date_suffix]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][date_suffix]" <?php if(isset($settings['date_suffix']) and $settings['date_suffix']) echo 'checked="checked"'; ?> /> <?php _e('Remove suffix from calendars', 'modern-events-calendar-lite'); ?>
                                </label>
                                <span class="mec-tooltip">
                                    <div class="box left">
                                        <h5 class="title"><?php _e('Remove "Th" on calendar', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("Checked this checkbox to remove 'Th' on calendar ( ex: '12Th' remove Th, showing just '12' )", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>

                            <div class="mec-form-row">
                            <label class="mec-col-3" for="mec_settings_schema"><?php _e('Schema', 'modern-events-calendar-lite'); ?></label>
                                <label id="mec_settings_schema" >
                                    <input type="hidden" name="mec[settings][schema]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][schema]" <?php if(!isset($settings['schema']) or (isset($settings['schema']) and $settings['schema'])) echo 'checked="checked"'; ?> /> <?php _e('Enable Schema Code', 'modern-events-calendar-lite'); ?>
                                </label>
                                <span class="mec-tooltip">
                                    <div class="box left">
                                        <h5 class="title"><?php _e('Schema', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("You can enable/disable Schema scripts", 'modern-events-calendar-lite'); ?></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>                            

                            <?php $weekdays = $this->main->get_weekday_i18n_labels(); ?>
                            <div class="mec-form-row">

                                <label class="mec-col-3" for="mec_settings_weekdays"><?php _e('Weekdays', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <div class="mec-box">
                                        <?php $mec_weekdays = $this->main->get_weekdays(); foreach($weekdays as $weekday): ?>
                                        <label for="mec_settings_weekdays_<?php echo $weekday[0]; ?>">
                                            <input type="checkbox" id="mec_settings_weekdays_<?php echo $weekday[0]; ?>" name="mec[settings][weekdays][]" value="<?php echo $weekday[0]; ?>" <?php echo (in_array($weekday[0], $mec_weekdays) ? 'checked="checked"' : ''); ?> />
                                            <?php echo $weekday[1]; ?>
                                        </label>
                                        <?php endforeach; ?>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Weekdays', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e("Proceed with caution. Default is set to Monday, Tuesday, Wednesday, Thursday and Friday ( you can change 'Week Starts' on WordPress Dashboard > Settings > General - bottom of the page ).", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <div class="mec-form-row">

                                <label class="mec-col-3" for="mec_settings_weekends"><?php _e('Weekends', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                <div class="mec-box">
                                    <?php $mec_weekends = $this->main->get_weekends(); foreach($weekdays as $weekday): ?>
                                    <label for="mec_settings_weekends_<?php echo $weekday[0]; ?>">
                                        <input type="checkbox" id="mec_settings_weekends_<?php echo $weekday[0]; ?>" name="mec[settings][weekends][]" value="<?php echo $weekday[0]; ?>" <?php echo (in_array($weekday[0], $mec_weekends) ? 'checked="checked"' : ''); ?> />
                                        <?php echo $weekday[1]; ?>
                                    </label>
                                    <?php endforeach; ?>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Weekends', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Proceed with caution. Default is set to Saturday and Sunday (you can change 'Week Starts' on WordPress Dashboard > Settings > General - bottom of the page).", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                                </div>

                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_datepicker_format"><?php _e('Datepicker Format', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_datepicker_format" name="mec[settings][datepicker_format]">
                                        <?php
                                            $selected = (isset($settings['datepicker_format']) and trim($settings['datepicker_format'])) ? trim($settings['datepicker_format']) : 'yy-mm-dd&Y-m-d';
                                            $current_time = current_time('timestamp', 0);
                                        ?>
                                        <!-- ++++ dd-mm-yy ++++ -->
                                        <option value="yy-mm-dd&Y-m-d" <?php selected($selected, 'yy-mm-dd&Y-m-d'); ?>><?php echo date('Y-m-d', $current_time) . ' ' . __('(Y-m-d)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="dd-mm-yy&d-m-Y" <?php selected($selected, 'dd-mm-yy&d-m-Y'); ?>><?php echo date('d-m-Y', $current_time) . ' ' . __('(d-m-Y)', 'modern-events-calendar-lite'); ?></option>

                                        <!-- ++++ dd/mm/yy ++++ -->
                                        <option value="yy/mm/dd&Y/m/d" <?php selected($selected, 'yy/mm/dd&Y/m/d'); ?>><?php echo date('Y/m/d', $current_time) . ' ' . __('(Y/m/d)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="mm/dd/yy&m/d/Y" <?php selected($selected, 'mm/dd/yy&m/d/Y'); ?>><?php echo date('m/d/Y', $current_time) . ' ' . __('(m/d/Y)', 'modern-events-calendar-lite'); ?></option>

                                        <!-- ++++ dd.mm.yy ++++ -->
                                        <option value="yy.mm.dd&Y.m.d" <?php selected($selected, 'yy.mm.dd&Y.m.d'); ?>><?php echo date('Y.m.d', $current_time) . ' ' . __('(Y.m.d)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="dd.mm.yy&d.m.Y" <?php selected($selected, 'dd.mm.yy&d.m.Y'); ?>><?php echo date('d.m.Y', $current_time) . ' ' . __('(d.m.Y)', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_midnight_hour"><?php _e('Midnight Hour', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_midnight_hour" name="mec[settings][midnight_hour]">
                                        <option value="0" <?php if(isset($settings['midnight_hour']) and !$settings['midnight_hour']) echo 'selected="selected"'; ?>><?php _e('12 AM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="1" <?php if(isset($settings['midnight_hour']) and $settings['midnight_hour'] == '1') echo 'selected="selected"'; ?>><?php _e('1 AM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="2" <?php if(isset($settings['midnight_hour']) and $settings['midnight_hour'] == '2') echo 'selected="selected"'; ?>><?php _e('2 AM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="3" <?php if(isset($settings['midnight_hour']) and $settings['midnight_hour'] == '3') echo 'selected="selected"'; ?>><?php _e('3 AM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="4" <?php if(isset($settings['midnight_hour']) and $settings['midnight_hour'] == '4') echo 'selected="selected"'; ?>><?php _e('4 AM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="5" <?php if(isset($settings['midnight_hour']) and $settings['midnight_hour'] == '5') echo 'selected="selected"'; ?>><?php _e('5 AM', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Midnight Hour', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("12 AM is midnight by default but you can change it if your event ends after 12 AM and you don't want those events considered as multiple days events!", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_event_as_popup"><?php _e('Open "Add Event" as Popup', 'modern-events-calendar-lite'); ?></label>
                                <label id="mec_settings_event_as_popup" >
                                    <input type="hidden" name="mec[settings][event_as_popup]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][event_as_popup]" <?php if(!isset($settings['event_as_popup']) or (isset($settings['event_as_popup']) and $settings['event_as_popup'])) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_sh_as_popup"><?php _e('Open "Add Shortcode" as Popup', 'modern-events-calendar-lite'); ?></label>
                                <label id="mec_settings_sh_as_popup" >
                                    <input type="hidden" name="mec[settings][sh_as_popup]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][sh_as_popup]" <?php if(!isset($settings['sh_as_popup']) or (isset($settings['sh_as_popup']) and $settings['sh_as_popup'])) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_include_image_in_feed"><?php _e('Include Event Featured Image in Feed', 'modern-events-calendar-lite'); ?></label>
                                <label id="mec_settings_sh_as_popup" >
                                    <input type="hidden" name="mec[settings][include_image_in_feed]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][include_image_in_feed]" <?php if(!isset($settings['include_image_in_feed']) or (isset($settings['include_image_in_feed']) and $settings['include_image_in_feed'])) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fallback_featured_image_status"><?php _e('Fallback Featured Image', 'modern-events-calendar-lite'); ?></label>
                                <label id="mec_settings_sh_as_popup" >
                                    <input type="hidden" name="mec[settings][fallback_featured_image_status]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fallback_featured_image_status]" <?php if(isset($settings['fallback_featured_image_status']) and $settings['fallback_featured_image_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <div class="mec-form-row">

                                <label class="mec-col-3" for="mec_settings_tag_method"><?php _e('Tag Method', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_tag_method" name="mec[settings][tag_method]">
                                        <option value="post_tag" <?php if(isset($settings['tag_method']) and $settings['tag_method'] == 'post_tag') echo 'selected="selected"'; ?>><?php _e('Post Tags', 'modern-events-calendar-lite'); ?></option>
                                        <option value="mec_tag" <?php if(isset($settings['tag_method']) and $settings['tag_method'] == 'mec_tag') echo 'selected="selected"'; ?>><?php _e('Independent Tags', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Tag Method', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If you select Post Tags then mec would share post tags and event tags but if you select independent tags, mec would use its own tags.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/general-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>

                            </div>

                            <div class="mec-form-row" style="padding-bottom: 3px;">
                                <label class="mec-col-3" for="mec_settings_ical_feed"><?php _e('iCal Feed', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label id="mec_settings_tag_method" >
                                        <input type="hidden" name="mec[settings][ical_feed]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][ical_feed]" <?php if(isset($settings['ical_feed']) and $settings['ical_feed']) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <p style="margin-top: 0;"><?php echo sprintf(esc_html__('If enabled, users are able to use %s URL to subscribe to your events.', 'modern-events-calendar-lite'), '<a href="'.trim($this->main->URL('site'), '/ ').'/?mec-ical-feed=1" target="_blank">'.trim($this->main->URL('site'), '/ ').'/?mec-ical-feed=1</a>'); ?></p>
                                </div>
                            </div>

                        </div>

                        <div id="email_option" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Email', 'modern-events-calendar-lite'); ?></h4>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_booking_sender_name"><?php _e('Sender Name', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_booking_sender_name" name="mec[settings][booking_sender_name]"
                                           value="<?php echo (isset($settings['booking_sender_name']) and trim($settings['booking_sender_name'])) ? esc_attr(stripslashes($settings['booking_sender_name'])) : ''; ?>" placeholder="<?php _e('e.g. Webnus', 'modern-events-calendar-lite'); ?>"/>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_booking_sender_email"><?php _e('Sender Email', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_booking_sender_email" name="mec[settings][booking_sender_email]"
                                           value="<?php echo (isset($settings['booking_sender_email']) and trim($settings['booking_sender_email'])) ? $settings['booking_sender_email'] : ''; ?>" placeholder="<?php _e('e.g. info@webnus.biz', 'modern-events-calendar-lite'); ?>"/>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_booking_recipients_method"><?php _e('Recipients Method', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_booking_recipients_method" name="mec[settings][booking_recipients_method]">
                                        <option value="BCC" <?php echo ((isset($settings['booking_recipients_method']) and trim($settings['booking_recipients_method']) == 'BCC') ? 'selected="selected"' : ''); ?>><?php esc_html_e('BCC (Invisible)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="CC" <?php echo ((isset($settings['booking_recipients_method']) and trim($settings['booking_recipients_method']) == 'CC') ? 'selected="selected"' : ''); ?>><?php esc_html_e('CC (Visible)', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div id="archive_options" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Archive Pages', 'modern-events-calendar-lite'); ?></h4>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_archive_title"><?php _e('Archive Page Title', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_archive_title" name="mec[settings][archive_title]" value="<?php echo ((isset($settings['archive_title']) and trim($settings['archive_title']) != '') ? $settings['archive_title'] : 'Events'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Archive Page Title', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value is Events - It's title of the page", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/archive-pages/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_archive_title_tag"><?php _e('Tag of Archive Page Title', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_archive_title_tag" name="mec[settings][archive_title_tag]">
                                        <option value="h1" <?php if(isset($settings['archive_title_tag']) and 'h1' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 1'); ?></option>
                                        <option value="h2" <?php if(isset($settings['archive_title_tag']) and 'h2' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 2'); ?></option>
                                        <option value="h3" <?php if(isset($settings['archive_title_tag']) and 'h3' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 3'); ?></option>
                                        <option value="h4" <?php if(isset($settings['archive_title_tag']) and 'h4' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 4'); ?></option>
                                        <option value="h5" <?php if(isset($settings['archive_title_tag']) and 'h5' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 5'); ?></option>
                                        <option value="h6" <?php if(isset($settings['archive_title_tag']) and 'h6' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Heading 6'); ?></option>
                                        <option value="div" <?php if(isset($settings['archive_title_tag']) and 'div' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Division'); ?></option>
                                        <option value="p" <?php if(isset($settings['archive_title_tag']) and 'p' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Paragraph'); ?></option>
                                        <option value="strong" <?php if(isset($settings['archive_title_tag']) and 'strong' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Inline Bold Text'); ?></option>
                                        <option value="span" <?php if(isset($settings['archive_title_tag']) and 'span' == $settings['archive_title_tag']) echo 'selected="selected"'; ?>><?php esc_html_e('Inline Text'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_default_skin_archive"><?php _e('Archive Page Skin', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9 tooltip-move-up">
                                    <select id="mec_settings_default_skin_archive" name="mec[settings][default_skin_archive]" onchange="mec_archive_skin_style_changed(this.value);">
                                        <?php foreach($archive_skins as $archive_skin): ?>
                                            <option value="<?php echo $archive_skin['skin']; ?>" <?php if(isset($settings['default_skin_archive']) and $archive_skin['skin'] == $settings['default_skin_archive']) echo 'selected="selected"'; ?>><?php echo $archive_skin['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="mec-archive-skins mec-archive-custom-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Put shortcode...', 'modern-events-calendar-lite'); ?>" id="mec_settings_custom_archive" name="mec[settings][custom_archive]" value='<?php echo ((isset($settings['custom_archive']) and trim($settings['custom_archive']) != '') ? $settings['custom_archive'] : ''); ?>' />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-full_calendar-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-yearly_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Modern Style', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-monthly_view-skins">
                                        <select id="mec_settings_monthly_view_skin_archive" name="mec[settings][monthly_view_archive_skin]">
                                            <option value="classic" <?php if(isset($settings['monthly_view_archive_skin']) &&  $settings['monthly_view_archive_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['monthly_view_archive_skin']) &&  $settings['monthly_view_archive_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['monthly_view_archive_skin']) &&  $settings['monthly_view_archive_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="novel" <?php if(isset($settings['monthly_view_archive_skin']) &&  $settings['monthly_view_archive_skin'] == 'novel') echo 'selected="selected"'; ?>><?php echo esc_html__('Novel' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="simple" <?php if(isset($settings['monthly_view_archive_skin']) &&  $settings['monthly_view_archive_skin'] == 'simple') echo 'selected="selected"'; ?>><?php echo esc_html__('Simple' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-archive-skins mec-archive-weekly_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-daily_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-timetable-skins">
                                        <select id="mec_settings_timetable_skin_archive" name="mec[settings][timetable_archive_skin]">
                                            <option value="modern" <?php if(isset($settings['timetable_archive_skin']) &&  $settings['timetable_archive_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['timetable_archive_skin']) &&  $settings['timetable_archive_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-archive-skins mec-archive-masonry-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-list-skins">
                                        <select id="mec_settings_list_skin_archive" name="mec[settings][list_archive_skin]">
                                            <option value="classic" <?php if(isset($settings['list_archive_skin']) &&  $settings['list_archive_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="minimal" <?php if(isset($settings['list_archive_skin']) &&  $settings['list_archive_skin'] == 'minimal') echo 'selected="selected"'; ?>><?php echo esc_html__('Minimal' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['list_archive_skin']) &&  $settings['list_archive_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="standard" <?php if(isset($settings['list_archive_skin']) &&  $settings['list_archive_skin'] == 'standard') echo 'selected="selected"'; ?>><?php echo esc_html__('Standard' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="accordion" <?php if(isset($settings['list_archive_skin']) &&  $settings['list_archive_skin'] == 'accordion') echo 'selected="selected"'; ?>><?php echo esc_html__('Accordion' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-archive-skins mec-archive-grid-skins">
                                        <select id="mec_settings_grid_skin_archive" name="mec[settings][grid_archive_skin]">
                                            <option value="classic" <?php if(isset($settings['grid_archive_skin']) &&  $settings['grid_archive_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="minimal" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'minimal') echo 'selected="selected"'; ?>><?php echo esc_html__('Minimal' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="simple" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'simple') echo 'selected="selected"'; ?>><?php echo esc_html__('Simple' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="colorful" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'colorful') echo 'selected="selected"'; ?>><?php echo esc_html__('colorful' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="novel" <?php if(isset($settings['grid_archive_skin'])  &&  $settings['grid_archive_skin'] == 'novel') echo 'selected="selected"'; ?>><?php echo esc_html__('Novel' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-archive-skins mec-archive-agenda-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Clean Style', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-archive-skins mec-archive-map-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Archive Page Skin', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value is Calendar/Monthly View, But you can change it ", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/archive-pages/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a><a href="https://webnus.net/modern-events-calendar/" target="_blank"><?php _e('See Demo', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_default_skin_category"><?php _e('Category Page Skin', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9 tooltip-move-up">
                                    <select id="mec_settings_default_skin_category" name="mec[settings][default_skin_category]" onchange="mec_category_skin_style_changed(this.value);">
                                        <?php foreach($category_skins as $category_skin): ?>
                                            <option value="<?php echo $category_skin['skin']; ?>" <?php if(isset($settings['default_skin_category']) and $category_skin['skin'] == $settings['default_skin_category']) echo 'selected="selected"'; if(!isset($settings['default_skin_category']) and $category_skin['skin'] == 'list') echo 'selected="selected"'; ?>><?php echo $category_skin['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="mec-category-skins mec-category-custom-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Put shortcode...', 'modern-events-calendar-lite'); ?>" id="mec_settings_custom_archive_category" name="mec[settings][custom_archive_category]" value='<?php echo ((isset($settings['custom_archive_category']) and trim($settings['custom_archive_category']) != '') ? stripslashes($settings['custom_archive_category']) : ''); ?>' />
                                    </span>
                                    <span class="mec-category-skins mec-category-full_calendar-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-yearly_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Modern Style', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-monthly_view-skins">
                                        <select id="mec_settings_monthly_view_skin_category" name="mec[settings][monthly_view_category_skin]">
                                            <option value="classic" <?php if(isset($settings['monthly_view_category_skin']) &&  $settings['monthly_view_category_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['monthly_view_category_skin']) &&  $settings['monthly_view_category_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['monthly_view_category_skin']) &&  $settings['monthly_view_category_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="novel" <?php if(isset($settings['monthly_view_category_skin']) &&  $settings['monthly_view_category_skin'] == 'novel') echo 'selected="selected"'; ?>><?php echo esc_html__('Novel' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="simple" <?php if(isset($settings['monthly_view_category_skin']) &&  $settings['monthly_view_category_skin'] == 'simple') echo 'selected="selected"'; ?>><?php echo esc_html__('Simple' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-category-skins mec-category-weekly_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-daily_view-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-timetable-skins">
                                        <select id="mec_settings_timetable_skin_category" name="mec[settings][timetable_category_skin]">
                                            <option value="modern" <?php if(isset($settings['timetable_category_skin']) &&  $settings['timetable_category_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['timetable_category_skin']) &&  $settings['timetable_category_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-category-skins mec-category-masonry-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-list-skins">
                                        <select id="mec_settings_list_skin_category" name="mec[settings][list_category_skin]">
                                            <option value="classic" <?php if(isset($settings['list_category_skin']) &&  $settings['list_category_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="minimal" <?php if(isset($settings['list_category_skin']) &&  $settings['list_category_skin'] == 'minimal') echo 'selected="selected"'; ?>><?php echo esc_html__('Minimal' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['list_category_skin']) &&  $settings['list_category_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="standard" <?php if(isset($settings['list_category_skin']) &&  $settings['list_category_skin'] == 'standard') echo 'selected="selected"'; ?>><?php echo esc_html__('Standard' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="accordion" <?php if(isset($settings['list_category_skin']) &&  $settings['list_category_skin'] == 'accordion') echo 'selected="selected"'; ?>><?php echo esc_html__('Accordion' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-category-skins mec-category-grid-skins">
                                        <select id="mec_settings_grid_skin_category" name="mec[settings][grid_category_skin]">
                                            <option value="classic" <?php if(isset($settings['grid_category_skin']) &&  $settings['grid_category_skin'] == 'classic') echo 'selected="selected"'; ?>><?php echo esc_html__('Classic' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="clean" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'clean') echo 'selected="selected"'; ?>><?php echo esc_html__('Clean' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="minimal" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'minimal') echo 'selected="selected"'; ?>><?php echo esc_html__('Minimal' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="modern" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'modern') echo 'selected="selected"'; ?>><?php echo esc_html__('Modern' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="simple" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'simple') echo 'selected="selected"'; ?>><?php echo esc_html__('Simple' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="colorful" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'colorful') echo 'selected="selected"'; ?>><?php echo esc_html__('colorful' , 'modern-events-calendar-lite'); ?></option>
                                            <option value="novel" <?php if(isset($settings['grid_category_skin'])  &&  $settings['grid_category_skin'] == 'novel') echo 'selected="selected"'; ?>><?php echo esc_html__('Novel' , 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </span>
                                    <span class="mec-category-skins mec-category-agenda-skins">
                                        <input type="text" placeholder="<?php esc_html_e('Clean Style', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-category-skins mec-category-map-skins">
                                        <input type="text" placeholder="<?php esc_html_e('There is no skins', 'modern-events-calendar-lite'); ?>" disabled />
                                    </span>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Category Page Skin', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value is List View - But you can change it to set a skin for all categories.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/archive-pages/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a><a href="https://webnus.net/modern-events-calendar/" target="_blank"><?php _e('See Demo', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_category_events_method"><?php _e('Category Events Method', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_category_events_method" name="mec[settings][category_events_method]">
                                        <option value="1" <?php if(!isset($settings['category_events_method']) or (isset($settings['category_events_method']) and $settings['category_events_method'] == 1)) echo 'selected="selected"'; ?>><?php _e('Upcoming Events', 'modern-events-calendar-lite'); ?></option>
                                        <option value="2" <?php if(isset($settings['category_events_method']) and $settings['category_events_method'] == 2) echo 'selected="selected"'; ?>><?php _e('Expired Events', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Category Events Method', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value is Upcoming Events", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/archive-pages/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_archive_status"><?php _e('Events Archive Status', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_archive_status" name="mec[settings][archive_status]">
                                        <option value="1" <?php if(isset($settings['archive_status']) and $settings['archive_status'] == '1') echo 'selected="selected"'; ?>><?php _e('Enabled (Recommended)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="0" <?php if(isset($settings['archive_status']) and !$settings['archive_status']) echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Events Archive Status', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If you disable it, then you should create a page as archive page of MEC. Page's slug must equals to \"Main Slug\" of MEC. Also it will disable all of MEC rewrite rules.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/archive-pages/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div id="slug_option" class="mec-options-fields">

                            <h4 class="mec-form-subtitle"><?php _e('Slugs/Permalinks', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_slug"><?php _e('Main Slug', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_slug" name="mec[settings][slug]" value="<?php echo ((isset($settings['slug']) and trim($settings['slug']) != '') ? esc_attr($settings['slug']) : 'events'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Main Slug', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value is events. You can not have a page with this name. MEC allows you to create custom URLs for the permalinks and archives to enhance the applicability and forward-compatibility of the links.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slug-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                    <p><?php esc_attr_e("Valid characters are lowercase a-z, - character and numbers.", 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_category_slug"><?php _e('Category Slug', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_category_slug" name="mec[settings][category_slug]" value="<?php echo ((isset($settings['category_slug']) and trim($settings['category_slug']) != '') ? esc_attr($settings['category_slug']) : 'mec-category'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Category Slug', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("It's slug of MEC categories, you can change it to events-cat or something else. Default value is mec-category. You can not have a page with this name.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slug-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                    <p><?php esc_attr_e("Valid characters are lowercase a-z, - character and numbers.", 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div id="currency_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Currency', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_currency"><?php _e('Currency', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select name="mec[settings][currency]" id="mec_settings_currency">
                                        <?php foreach($currencies as $currency=>$currency_name): ?>
                                            <option value="<?php echo $currency; ?>" <?php echo ((isset($settings['currency']) and $settings['currency'] == $currency) ? 'selected="selected"' : ''); ?>><?php echo $currency_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_currency_symptom"><?php _e('Currency Sign', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" name="mec[settings][currency_symptom]" id="mec_settings_currency_symptom" value="<?php echo (isset($settings['currency_symptom']) ? $settings['currency_symptom'] : ''); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Currency Sign', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default value will be \"currency\" if you leave it empty.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/currency-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_currency_sign"><?php _e('Currency Position', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select name="mec[settings][currency_sign]" id="mec_settings_currency_sign">
                                        <option value="before" <?php echo ((isset($settings['currency_sign']) and $settings['currency_sign'] == 'before') ? 'selected="selected"' : ''); ?>><?php _e('$10 (Before)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="before_space" <?php echo ((isset($settings['currency_sign']) and $settings['currency_sign'] == 'before_space') ? 'selected="selected"' : ''); ?>><?php _e('$ 10 (Before with Space)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="after" <?php echo ((isset($settings['currency_sign']) and $settings['currency_sign'] == 'after') ? 'selected="selected"' : ''); ?>><?php _e('10$ (After)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="after_space" <?php echo ((isset($settings['currency_sign']) and $settings['currency_sign'] == 'after_space') ? 'selected="selected"' : ''); ?>><?php _e('10 $ (After with Space)', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_thousand_separator"><?php _e('Thousand Separator', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" name="mec[settings][thousand_separator]" id="mec_settings_thousand_separator" value="<?php echo (isset($settings['thousand_separator']) ? $settings['thousand_separator'] : ','); ?>" />
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_decimal_separator"><?php _e('Decimal Separator', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" name="mec[settings][decimal_separator]" id="mec_settings_decimal_separator" value="<?php echo (isset($settings['decimal_separator']) ? $settings['decimal_separator'] : '.'); ?>" />
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <label for="mec_settings_decimal_separator_status">
                                        <input type="hidden" name="mec[settings][decimal_separator_status]" value="1" />
                                        <input type="checkbox" name="mec[settings][decimal_separator_status]" id="mec_settings_decimal_separator_status" <?php echo ((isset($settings['decimal_separator_status']) and $settings['decimal_separator_status'] == '0') ? 'checked="checked"' : ''); ?> value="0" />
                                        <?php _e('No decimal', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="assets_per_page_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Assets Per Page', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][assets_per_page_status]" value="0" />
                                    <input onchange="jQuery('#mec_assets_per_page_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][assets_per_page_status]" <?php if(isset($settings['assets_per_page_status']) and $settings['assets_per_page_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Assets Per Page', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_assets_per_page_container_toggle" class="<?php if((isset($settings['assets_per_page_status']) and !$settings['assets_per_page_status']) or !isset($settings['assets_per_page_status'])) echo 'mec-util-hidden'; ?>">
                                <p class="notice-red" style="color: #b94a48; text-shadow: unset;"><?php echo esc_html__("By enabling this option MEC won't include any JavaScript or CSS files in frontend of your website unless you enable the assets inclusion in page options.", 'modern-events-calendar-lite'); ?></p>
                            </div>
                        </div>

                        <div id="recaptcha_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Google Recaptcha', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][google_recaptcha_status]" value="0" />
                                    <input onchange="jQuery('#mec_google_recaptcha_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][google_recaptcha_status]" <?php if(isset($settings['google_recaptcha_status']) and $settings['google_recaptcha_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Google Recaptcha', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_google_recaptcha_container_toggle" class="<?php if((isset($settings['google_recaptcha_status']) and !$settings['google_recaptcha_status']) or !isset($settings['google_recaptcha_status'])) echo 'mec-util-hidden'; ?>">

                                <?php if($this->getPro()): ?>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][google_recaptcha_booking]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][google_recaptcha_booking]" <?php if(isset($settings['google_recaptcha_booking']) and $settings['google_recaptcha_booking']) echo 'checked="checked"'; ?> /> <?php _e('Enable on booking form', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <?php endif; ?>

                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][google_recaptcha_fes]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][google_recaptcha_fes]" <?php if(isset($settings['google_recaptcha_fes']) and $settings['google_recaptcha_fes']) echo 'checked="checked"'; ?> /> <?php _e('Enable on "Frontend Event Submission" form', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_google_recaptcha_sitekey"><?php _e('Site Key', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <input type="text" id="mec_settings_google_recaptcha_sitekey" name="mec[settings][google_recaptcha_sitekey]" value="<?php echo ((isset($settings['google_recaptcha_sitekey']) and trim($settings['google_recaptcha_sitekey']) != '') ? $settings['google_recaptcha_sitekey'] : ''); ?>" />
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_google_recaptcha_secretkey"><?php _e('Secret Key', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <input type="text" id="mec_settings_google_recaptcha_secretkey" name="mec[settings][google_recaptcha_secretkey]" value="<?php echo ((isset($settings['google_recaptcha_secretkey']) and trim($settings['google_recaptcha_secretkey']) != '') ? $settings['google_recaptcha_secretkey'] : ''); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>                    

                        <div id="fes_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Frontend Event Submission', 'modern-events-calendar-lite'); ?></h4>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_time_format"><?php _e('Time Format', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_time_format" name="mec[settings][time_format]">
                                        <option value="12" <?php if(isset($settings['time_format']) and '12' == $settings['time_format']) echo 'selected="selected"'; ?>><?php _e('12 hours format with AM/PM', 'modern-events-calendar-lite'); ?></option>
                                        <option value="24" <?php if(isset($settings['time_format']) and '24' == $settings['time_format']) echo 'selected="selected"'; ?>><?php _e('24 hours format', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Time Format', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("This option, affects the selection of Start/End time.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/frontend-event-submission/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_list_page"><?php _e('Events List Page', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_list_page" name="mec[settings][fes_list_page]">
                                        <option value="">----</option>
                                        <?php foreach($pages as $page): ?>
                                            <option <?php echo ((isset($settings['fes_list_page']) and $settings['fes_list_page'] == $page->ID) ? 'selected="selected"' : ''); ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php echo sprintf(__('Put %s shortcode into the page.', 'modern-events-calendar-lite'), '<code>[MEC_fes_list]</code>'); ?></p>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_form_page"><?php _e('Add/Edit Events Page', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_form_page" name="mec[settings][fes_form_page]">
                                        <option value="">----</option>
                                        <?php foreach($pages as $page): ?>
                                            <option <?php echo ((isset($settings['fes_form_page']) and $settings['fes_form_page'] == $page->ID) ? 'selected="selected"' : ''); ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php echo sprintf(__('Put %s shortcode into the page.', 'modern-events-calendar-lite'), '<code>[MEC_fes_form]</code>'); ?></p>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_new_event_status"><?php _e('New Events Status', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_new_event_status" name="mec[settings][fes_new_event_status]">
                                        <option value=""><?php esc_html_e('Let WordPress decide', 'modern-events-calendar-lite'); ?></option>
                                        <option <?php echo ((isset($settings['fes_new_event_status']) and $settings['fes_new_event_status'] == 'pending') ? 'selected="selected"' : ''); ?> value="pending"><?php esc_html_e('Pending', 'modern-events-calendar-lite'); ?></option>
                                        <option <?php echo ((isset($settings['fes_new_event_status']) and $settings['fes_new_event_status'] == 'publish') ? 'selected="selected"' : ''); ?> value="publish"><?php esc_html_e('Publish', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_display_date_in_list"><?php _e('Display Event Date in List', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_display_date_in_list" name="mec[settings][fes_display_date_in_list]">
                                        <option <?php echo ((isset($settings['fes_display_date_in_list']) and $settings['fes_display_date_in_list'] == '0') ? 'selected="selected"' : ''); ?> value="0"><?php esc_html_e('No', 'modern-events-calendar-lite'); ?></option>
                                        <option <?php echo ((isset($settings['fes_display_date_in_list']) and $settings['fes_display_date_in_list'] == '1') ? 'selected="selected"' : ''); ?> value="1"><?php esc_html_e('Yes', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <!-- Start FES Thank You Page -->
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_thankyou_page"><?php _e('Thank You Page', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_thankyou_page" name="mec[settings][fes_thankyou_page]">
                                        <option value="">----</option>
                                        <?php foreach($pages as $page): ?>
                                            <option <?php echo ((isset($settings['fes_thankyou_page']) and $settings['fes_thankyou_page'] == $page->ID) ? 'selected="selected"' : ''); ?> value="<?php echo intval($page->ID); ?>"><?php echo $page->post_title; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Thank You Page', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("User is redirected to this page after a new event submission. Leave it empty if you want it disabled.", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_thankyou_page_url"><?php _e('Thank You Page URL', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="url" id="mec_settings_fes_thankyou_page_url" name="mec[settings][fes_thankyou_page_url]" value="<?php echo ((isset($settings['fes_thankyou_page_url']) and trim($settings['fes_thankyou_page_url']) != '') ? esc_url($settings['fes_thankyou_page_url']) : ''); ?>" placeholder="<?php echo esc_attr('http://yoursite/com/desired-url/'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Thank You Page URL', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If filled it will use instead of thank you page set above.", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- End FES Thank You Page -->
                            <!-- Start FES Thank You Page Time -->
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_thankyou_page_time"><?php _e('Thank You Page Time Interval', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="number" id="mec_settings_fes_thankyou_page_time" name="mec[settings][fes_thankyou_page_time]" value="<?php echo ((isset($settings['fes_thankyou_page_time']) and trim($settings['fes_thankyou_page_time']) != '0') ? intval($settings['fes_thankyou_page_time']) : '2000'); ?>" placeholder="<?php esc_attr_e('2000 mean 2 seconds', 'modern-events-calendar-lite'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Thank You Page Time Interval', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Waiting time before redirecting to thank you page. It's in miliseconds so 2000 means 2 seconds.", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- End FES Thank You Page Time -->
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_fes_max_file_size"><?php _e('Maximum File Size', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="number" id="mec_settings_fes_max_file_size" name="mec[settings][fes_max_file_size]" value="<?php echo ((isset($settings['fes_max_file_size']) and trim($settings['fes_max_file_size']) != '0') ? intval($settings['fes_max_file_size']) : '5000'); ?>" placeholder="<?php esc_attr_e('in KB', 'modern-events-calendar-lite'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Maximum File Size', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("In Kilo Bytes so 5000 means 5MB (Approximately)", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_guest_status]" value="0" />
                                    <input onchange="jQuery('#mec_fes_guest_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][fes_guest_status]" <?php if(isset($settings['fes_guest_status']) and $settings['fes_guest_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable event submission by guest (Not logged in) users', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_fes_guest_status_container_toggle" class="<?php if((isset($settings['fes_guest_status']) and !$settings['fes_guest_status']) or !isset($settings['fes_guest_status'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_guest_name_email]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_guest_name_email]" <?php if(!isset($settings['fes_guest_name_email']) or (isset($settings['fes_guest_name_email']) and $settings['fes_guest_name_email'])) echo 'checked="checked"'; ?> /> <?php _e('Enable mandatory email and name for guest user', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_guest_user_creation]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_guest_user_creation]" <?php if(isset($settings['fes_guest_user_creation']) and $settings['fes_guest_user_creation']) echo 'checked="checked"'; ?> /> <?php _e('Automatically create users after event publish and assign event to the created user', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <br>
                            <h5 class="mec-form-subtitle"><?php _e('Frontend Event Submission Sections', 'modern-events-calendar-lite'); ?></h5>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_data_fields]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_data_fields]" <?php if(!isset($settings['fes_section_data_fields']) or (isset($settings['fes_section_data_fields']) and $settings['fes_section_data_fields'])) echo 'checked="checked"'; ?> /> <?php _e('Event Data Fields', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_event_links]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_event_links]" <?php if(!isset($settings['fes_section_event_links']) or (isset($settings['fes_section_event_links']) and $settings['fes_section_event_links'])) echo 'checked="checked"'; ?> /> <?php _e('Event Links', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_cost]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_cost]" <?php if(!isset($settings['fes_section_cost']) or (isset($settings['fes_section_cost']) and $settings['fes_section_cost'])) echo 'checked="checked"'; ?> /> <?php echo $this->main->m('event_cost', __('Event Cost', 'modern-events-calendar-lite')); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_featured_image]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_featured_image]" <?php if(!isset($settings['fes_section_featured_image']) or (isset($settings['fes_section_featured_image']) and $settings['fes_section_featured_image'])) echo 'checked="checked"'; ?> /> <?php _e('Featured Image', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_categories]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_categories]" <?php if(!isset($settings['fes_section_categories']) or (isset($settings['fes_section_categories']) and $settings['fes_section_categories'])) echo 'checked="checked"'; ?> /> <?php _e('Event Categories', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_labels]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_labels]" <?php if(!isset($settings['fes_section_labels']) or (isset($settings['fes_section_labels']) and $settings['fes_section_labels'])) echo 'checked="checked"'; ?> /> <?php _e('Event Labels', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_shortcode_visibility]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_shortcode_visibility]" <?php if(!isset($settings['fes_section_shortcode_visibility']) or (isset($settings['fes_section_shortcode_visibility']) and $settings['fes_section_shortcode_visibility'])) echo 'checked="checked"'; ?> /> <?php _e('Event Visibility', 'modern-events-calendar-lite'); ?>
                                </label>
                                <span class="mec-tooltip">
                                    <div class="box right">
                                        <h5 class="title"><?php _e('Event Visibility', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("This feature adds the ability to hide the current event to all MEC Shortcodes.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/add-event/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_event_color]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_event_color]" <?php if(!isset($settings['fes_section_event_color']) or (isset($settings['fes_section_event_color']) and $settings['fes_section_event_color'])) echo 'checked="checked"'; ?> /> <?php _e('Event Color', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_tags]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_tags]" <?php if(!isset($settings['fes_section_tags']) or (isset($settings['fes_section_tags']) and $settings['fes_section_tags'])) echo 'checked="checked"'; ?> /> <?php _e('Event Tags', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_location]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_location]" <?php if(!isset($settings['fes_section_location']) or (isset($settings['fes_section_location']) and $settings['fes_section_location'])) echo 'checked="checked"'; ?> /> <?php _e('Event Location', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_organizer]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_organizer]" <?php if(!isset($settings['fes_section_organizer']) or (isset($settings['fes_section_organizer']) and $settings['fes_section_organizer'])) echo 'checked="checked"'; ?> onchange="jQuery('#mec_settings_fes_use_all_organizers_wrapper').toggle();" /> <?php _e('Event Organizer', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row <?php echo ((!isset($settings['fes_section_organizer']) or (isset($settings['fes_section_organizer']) and $settings['fes_section_organizer'])) ? '' : 'mec-util-hidden'); ?>" id="mec_settings_fes_use_all_organizers_wrapper">
                                <label class="mec-col-3" for="mec_settings_fes_use_all_organizers"><?php _e('Ability to Use All Organizers', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_fes_use_all_organizers" name="mec[settings][fes_use_all_organizers]">
                                        <option <?php echo ((isset($settings['fes_use_all_organizers']) and $settings['fes_use_all_organizers'] == '1') ? 'selected="selected"' : ''); ?> value="1"><?php esc_html_e('Yes', 'modern-events-calendar-lite'); ?></option>
                                        <option <?php echo ((isset($settings['fes_use_all_organizers']) and $settings['fes_use_all_organizers'] == '0') ? 'selected="selected"' : ''); ?> value="0"><?php esc_html_e('No', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Use All Organizers', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Users are able to see list of ogranizers and use them for their event. Set it to \"No\" if you want to disable this functionality and \"Other Organizers\" feature.", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_speaker]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_speaker]" <?php if(isset($settings['fes_section_speaker']) and $settings['fes_section_speaker']) echo 'checked="checked"'; ?> /> <?php _e('Speakers', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_hourly_schedule]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_hourly_schedule]" <?php if(!isset($settings['fes_section_hourly_schedule']) or (isset($settings['fes_section_hourly_schedule']) and $settings['fes_section_hourly_schedule'])) echo 'checked="checked"'; ?> /> <?php _e('Hourly Schedule', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <?php if($this->getPRO()): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_booking]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_booking]" <?php if(!isset($settings['fes_section_booking']) or (isset($settings['fes_section_booking']) and $settings['fes_section_booking'])) echo 'checked="checked"'; ?> onchange="jQuery('#mec_fes_booking_section_options').toggle();" /> <?php _e('Booking Options', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_fes_booking_section_options" style="margin: 0 0 40px 0; padding: 20px 20px 4px; border: 1px solid #ddd;" class="<?php echo ((!isset($settings['fes_section_booking']) or (isset($settings['fes_section_booking']) and $settings['fes_section_booking'])) ? '' : 'mec-util-hidden'); ?>">
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_tbl]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_tbl]" <?php if(!isset($settings['fes_section_booking_tbl']) or (isset($settings['fes_section_booking_tbl']) and $settings['fes_section_booking_tbl'])) echo 'checked="checked"'; ?> /> <?php _e('Total Booking Limit', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_dpur]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_dpur]" <?php if(!isset($settings['fes_section_booking_dpur']) or (isset($settings['fes_section_booking_dpur']) and $settings['fes_section_booking_dpur'])) echo 'checked="checked"'; ?> /> <?php _e('Discount Per User Roles', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_bao]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_bao]" <?php if(!isset($settings['fes_section_booking_bao']) or (isset($settings['fes_section_booking_bao']) and $settings['fes_section_booking_bao'])) echo 'checked="checked"'; ?> /> <?php _e('Book All Occurrences', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_io]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_io]" <?php if(!isset($settings['fes_section_booking_io']) or (isset($settings['fes_section_booking_io']) and $settings['fes_section_booking_io'])) echo 'checked="checked"'; ?> /> <?php _e('Interval Options', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_aa]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_aa]" <?php if(!isset($settings['fes_section_booking_aa']) or (isset($settings['fes_section_booking_aa']) and $settings['fes_section_booking_aa'])) echo 'checked="checked"'; ?> /> <?php _e('Automatic Approval', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_tubl]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_tubl]" <?php if(!isset($settings['fes_section_booking_tubl']) or (isset($settings['fes_section_booking_tubl']) and $settings['fes_section_booking_tubl'])) echo 'checked="checked"'; ?> /> <?php _e('Total User Booking Limits', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_lftp]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_lftp]" <?php if(!isset($settings['fes_section_booking_lftp']) or (isset($settings['fes_section_booking_lftp']) and $settings['fes_section_booking_lftp'])) echo 'checked="checked"'; ?> /> <?php _e('Last Few Tickets Percentage', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_typ]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_typ]" <?php if(!isset($settings['fes_section_booking_typ']) or (isset($settings['fes_section_booking_typ']) and $settings['fes_section_booking_typ'])) echo 'checked="checked"'; ?> /> <?php _e('Thank You Page', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_tickets]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_tickets]" <?php if(!isset($settings['fes_section_tickets']) or (isset($settings['fes_section_tickets']) and $settings['fes_section_tickets'])) echo 'checked="checked"'; ?> /> <?php _e('Ticket Options', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][booking_private_description]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][booking_private_description]" <?php if(!isset($settings['booking_private_description']) or (isset($settings['booking_private_description']) and $settings['booking_private_description'])) echo 'checked="checked"'; ?> /> <?php _e('Private Description', 'modern-events-calendar-lite'); ?>
                                </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_reg_form]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_reg_form]" <?php if(!isset($settings['fes_section_reg_form']) or (isset($settings['fes_section_reg_form']) and $settings['fes_section_reg_form'])) echo 'checked="checked"'; ?> /> <?php _e('Booking Form', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_fees]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_fees]" <?php if(!isset($settings['fes_section_fees']) or (isset($settings['fes_section_fees']) and $settings['fes_section_fees'])) echo 'checked="checked"'; ?> /> <?php _e('Fees / Taxes Options', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_ticket_variations]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_ticket_variations]" <?php if(!isset($settings['fes_section_ticket_variations']) or (isset($settings['fes_section_ticket_variations']) and $settings['fes_section_ticket_variations'])) echo 'checked="checked"'; ?> /> <?php _e('Ticket Variations / Options', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][fes_section_booking_att]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][fes_section_booking_att]" <?php if(!isset($settings['fes_section_booking_att']) or (isset($settings['fes_section_booking_att']) and $settings['fes_section_booking_att'])) echo 'checked="checked"'; ?> /> <?php _e('Attendees', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_schema]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_schema]" <?php if(!isset($settings['fes_section_schema']) or (isset($settings['fes_section_schema']) and $settings['fes_section_schema'])) echo 'checked="checked"'; ?> /> <?php _e('SEO Schema', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_excerpt]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_excerpt]" <?php if(isset($settings['fes_section_excerpt']) and $settings['fes_section_excerpt']) echo 'checked="checked"'; ?> /> <?php _e('Excerpt', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>

                            <?php if(isset($settings['downloadable_file_status']) and $settings['downloadable_file_status']): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_downloadable_file]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_downloadable_file]" <?php if(!isset($settings['fes_section_downloadable_file']) or (isset($settings['fes_section_downloadable_file']) and $settings['fes_section_downloadable_file'])) echo 'checked="checked"'; ?> /> <?php _e('Downloadable File', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if(isset($settings['per_occurrences_status']) and $settings['per_occurrences_status']): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_occurrences]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_occurrences]" <?php if(!isset($settings['fes_section_occurrences']) or (isset($settings['fes_section_occurrences']) and $settings['fes_section_occurrences'])) echo 'checked="checked"'; ?> /> <?php _e('Occurrences', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if(is_plugin_active('mec-virtual-events/mec-virtual-events.php')): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_virtual_events]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_virtual_events]" <?php if(!isset($settings['fes_section_virtual_events']) or (isset($settings['fes_section_virtual_events']) and $settings['fes_section_virtual_events'])) echo 'checked="checked"'; ?> /> <?php _e('Virtual Event', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php if(is_plugin_active('mec-zoom-integration/mec-zoom-integration.php')): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_section_zoom_integration]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_section_zoom_integration]" <?php if(!isset($settings['fes_section_zoom_integration']) or (isset($settings['fes_section_zoom_integration']) and $settings['fes_section_zoom_integration'])) echo 'checked="checked"'; ?> /> <?php _e('Zoom Event', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php endif; ?>

                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_note]" value="0" />
                                    <input onchange="jQuery('#mec_fes_note_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][fes_note]" <?php if(isset($settings['fes_note']) and $settings['fes_note']) echo 'checked="checked"'; ?> /> <?php _e('Event Note', 'modern-events-calendar-lite'); ?>
                                </label>
                                <span class="mec-tooltip">
                                    <div class="box right">
                                        <h5 class="title"><?php _e('Event Note', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("Users can put a note for editors while they're submitting the event. Also you can put %%event_note%% into the new event notification in order to get users' note in email.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/frontend-event-submission/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                            <div id="mec_fes_note_container_toggle" class="<?php if((isset($settings['fes_note']) and !$settings['fes_note']) or !isset($settings['fes_note'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_fes_note_visibility"><?php _e('Note visibility', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <select id="mec_settings_fes_note_visibility" name="mec[settings][fes_note_visibility]">
                                            <option <?php echo ((isset($settings['fes_note_visibility']) and $settings['fes_note_visibility'] == 'always') ? 'selected="selected"' : ''); ?> value="always"><?php _e('Always', 'modern-events-calendar-lite'); ?></option>
                                            <option <?php echo ((isset($settings['fes_note_visibility']) and $settings['fes_note_visibility'] == 'pending') ? 'selected="selected"' : ''); ?> value="pending"><?php _e('While event is not published', 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Note visibility', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e("Event Note shows on Frontend Submission Form and Edit Event in backend.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/frontend-event-submission/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5 class="mec-form-subtitle"><?php _e('Required Fields', 'modern-events-calendar-lite'); ?></h5>

                            <?php foreach(array(
                                'body' => __('Event Description', 'modern-events-calendar-lite'),
                                'excerpt' => __('Excerpt', 'modern-events-calendar-lite'),
                                'cost' => __('Cost', 'modern-events-calendar-lite'),
                                'event_link' => __('Event Link', 'modern-events-calendar-lite'),
                                'more_info_link' => __('More Info Link', 'modern-events-calendar-lite'),
                                'category' => __('Category', 'modern-events-calendar-lite'),
                                'label' => __('Label', 'modern-events-calendar-lite')) as $req_field => $label): ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][fes_required_<?php echo $req_field; ?>]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][fes_required_<?php echo $req_field; ?>]" <?php if(isset($settings['fes_required_'.$req_field]) and $settings['fes_required_'.$req_field]) echo 'checked="checked"'; ?> /> <?php echo esc_html($label); ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="user_profile_options" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('User Profile', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <p><?php echo sprintf(__('Put %s shortcode into your desired page. Then users are able to see the history of their bookings.', 'modern-events-calendar-lite'), '<code>[MEC_profile]</code>'); ?></p>
                                <p><?php echo sprintf(__('Use %s attribute to hide canceled bookings. Like %s', 'modern-events-calendar-lite'), '<code>hide-canceleds="1"</code>', '<code>[MEC_profile hide-canceleds="1"]</code>'); ?></p>
                                <p><?php echo sprintf(__('Use %s attribute to show upcoming bookings. Like %s', 'modern-events-calendar-lite'), '<code>show-upcomings="1"</code>', '<code>[MEC_profile show-upcomings="1"]</code>'); ?></p>
                            </div>
                        </div>

                        <div id="user_events_options" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('User Events', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <p><?php echo sprintf(__('Put %s shortcode into your desired page. Then users are able to see the their own events.', 'modern-events-calendar-lite'), '<code>[MEC_userevents]</code>'); ?></p>
                            </div>
                            <div class="mec-form-row">
                                <select name="mec[settings][userevents_shortcode]" id="mec_settings_userevents_shortcode">
                                    <?php foreach($shortcodes as $shortcode): $skin = get_post_meta($shortcode->ID, 'skin', true); if(!in_array($skin, array('monthly_view', 'daily_view', 'weekly_view', 'list', 'grid', 'agenda'))) continue; ?>
                                    <option value="<?php echo $shortcode->ID; ?>" <?php echo ((isset($settings['userevents_shortcode']) and $settings['userevents_shortcode'] == $shortcode->ID) ? 'selected="selected"' : ''); ?>><?php echo $shortcode->post_title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div id="search_bar_options" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Search Bar', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <p><?php echo sprintf(__('Put %s shortcode into your desired page. Then users are able to search events', 'modern-events-calendar-lite'), '<code>[MEC_search_bar]</code>'); ?></p>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_ajax_mode]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_ajax_mode]" <?php if(isset($settings['search_bar_ajax_mode']) and $settings['search_bar_ajax_mode']) echo 'checked="checked"'; ?> /> <?php _e('Ajax Live mode', 'modern-events-calendar-lite'); ?>
                                </label>
                                    <span class="mec-tooltip">
                                    <div class="box">
                                        <h5 class="title"><?php _e('Ajax mode', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("if you enable this option, the search button will disappear. To use this feature, text input field must be enabled.", 'modern-events-calendar-lite'); ?></p></div>    
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_modern_type]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_modern_type]" <?php if(isset($settings['search_bar_modern_type']) and $settings['search_bar_modern_type']) echo 'checked="checked"'; ?> /> <?php _e('Modern Type', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <br>
                            <h5 class="mec-form-subtitle"><?php _e('Search bar fields', 'modern-events-calendar-lite'); ?></h5>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_category]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_category]" <?php if(isset($settings['search_bar_category']) and $settings['search_bar_category']) echo 'checked="checked"'; ?> /> <?php _e('Category', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_location]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_location]" <?php if(isset($settings['search_bar_location']) and $settings['search_bar_location']) echo 'checked="checked"'; ?> /> <?php _e('Location', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_organizer]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_organizer]" <?php if(isset($settings['search_bar_organizer']) and $settings['search_bar_organizer']) echo 'checked="checked"'; ?> /> <?php _e('Organizer', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php if(isset($settings['speakers_status']) and $settings['speakers_status']) : ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_speaker]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_speaker]" <?php if(isset($settings['search_bar_speaker']) and $settings['search_bar_speaker']) echo 'checked="checked"'; ?> /> <?php _e('Speaker', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_tag]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_tag]" <?php if(isset($settings['search_bar_tag']) and $settings['search_bar_tag']) echo 'checked="checked"'; ?> /> <?php _e('Tag', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_label]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_label]" <?php if(isset($settings['search_bar_label']) and $settings['search_bar_label']) echo 'checked="checked"'; ?> /> <?php _e('Label', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][search_bar_text_field]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][search_bar_text_field]" <?php if(isset($settings['search_bar_text_field']) and $settings['search_bar_text_field']) echo 'checked="checked"'; ?> /> <?php _e('Text input', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>

                        <?php if($this->main->getPRO()): ?>

                            <div id="mailchimp_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Mailchimp Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][mchimp_status]" value="0" />
                                        <input onchange="jQuery('#mec_mchimp_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][mchimp_status]" <?php if(isset($settings['mchimp_status']) and $settings['mchimp_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Mailchimp Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_mchimp_status_container_toggle" class="<?php if((isset($settings['mchimp_status']) and !$settings['mchimp_status']) or !isset($settings['mchimp_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mchimp_api_key"><?php _e('API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_mchimp_api_key" name="mec[settings][mchimp_api_key]" value="<?php echo ((isset($settings['mchimp_api_key']) and trim($settings['mchimp_api_key']) != '') ? $settings['mchimp_api_key'] : ''); ?>" />
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('API Key', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e("Required!", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/mailchimp-integration/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mchimp_list_id"><?php _e('List ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_mchimp_list_id" name="mec[settings][mchimp_list_id]" value="<?php echo ((isset($settings['mchimp_list_id']) and trim($settings['mchimp_list_id']) != '') ? $settings['mchimp_list_id'] : ''); ?>" />
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('List ID', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e("Required!", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/mailchimp-integration/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mchimp_subscription_status"><?php _e('Subscription Status', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][mchimp_subscription_status]" id="mec_settings_mchimp_subscription_status">
                                                <option value="subscribed" <?php if(isset($settings['mchimp_subscription_status']) and $settings['mchimp_subscription_status'] == 'subscribed') echo 'selected="selected"'; ?>><?php _e('Subscribe automatically', 'modern-events-calendar-lite'); ?></option>
                                                <option value="pending" <?php if(isset($settings['mchimp_subscription_status']) and $settings['mchimp_subscription_status'] == 'pending') echo 'selected="selected"'; ?>><?php _e('Subscribe by verification', 'modern-events-calendar-lite'); ?></option>
                                            </select>
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('Subscription Status', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e('If you choose "Subscribe by verification" then an email will be send to the user by mailchimp for subscription verification.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/mailchimp-integration/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label>
                                            <input type="hidden" name="mec[settings][mchimp_segment_status]" value="0" />
                                            <input value="1" type="checkbox" name="mec[settings][mchimp_segment_status]" <?php if(isset($settings['mchimp_segment_status']) and $settings['mchimp_segment_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Segment Creation by Event Title and Booking Date', 'modern-events-calendar-lite'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="campaign_monitor_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Campaign Monitor Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][campm_status]" value="0" />
                                        <input onchange="jQuery('#mec_campm_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][campm_status]" <?php if(isset($settings['campm_status']) and $settings['campm_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Campaign Monitor Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_campm_status_container_toggle" class="<?php if((isset($settings['campm_status']) and !$settings['campm_status']) or !isset($settings['campm_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_campm_api_key"><?php _e('API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_campm_api_key" name="mec[settings][campm_api_key]" value="<?php echo ((isset($settings['campm_api_key']) and trim($settings['campm_api_key']) != '') ? $settings['campm_api_key'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_campm_list_id"><?php _e('List ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_campm_list_id" name="mec[settings][campm_list_id]" value="<?php echo ((isset($settings['campm_list_id']) and trim($settings['campm_list_id']) != '') ? $settings['campm_list_id'] : ''); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="mailerlite_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('MailerLite Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][mailerlite_status]" value="0" />
                                        <input onchange="jQuery('#mec_mailerlite_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][mailerlite_status]" <?php if(isset($settings['mailerlite_status']) and $settings['mailerlite_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable MailerLite Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_mailerlite_status_container_toggle" class="<?php if((isset($settings['mailerlite_status']) and !$settings['mailerlite_status']) or !isset($settings['mailerlite_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mailerlite_api_key"><?php _e('API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_mailerlite_api_key" name="mec[settings][mailerlite_api_key]" value="<?php echo ((isset($settings['mailerlite_api_key']) and trim($settings['mailerlite_api_key']) != '') ? $settings['mailerlite_api_key'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mailerlite_list_id"><?php _e('Group ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_mailerlite_list_id" name="mec[settings][mailerlite_list_id]" value="<?php echo ((isset($settings['mailerlite_list_id']) and trim($settings['mailerlite_list_id']) != '') ? $settings['mailerlite_list_id'] : ''); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="constantcontact_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Constant Contact Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][constantcontact_status]" value="0" />
                                        <input onchange="jQuery('#mec_constantcontact_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][constantcontact_status]" <?php if(isset($settings['constantcontact_status']) and $settings['constantcontact_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable constantcontact Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_constantcontact_status_container_toggle" class="<?php if((isset($settings['constantcontact_status']) and !$settings['constantcontact_status']) or !isset($settings['constantcontact_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_constantcontact_api_key"><?php _e('API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_constantcontact_api_key" name="mec[settings][constantcontact_api_key]" value="<?php echo ((isset($settings['constantcontact_api_key']) and trim($settings['constantcontact_api_key']) != '') ? $settings['constantcontact_api_key'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_constantcontact_access_token"><?php _e('Access Token', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_constantcontact_access_token" name="mec[settings][constantcontact_access_token]" value="<?php echo ((isset($settings['constantcontact_access_token']) and trim($settings['constantcontact_access_token']) != '') ? $settings['constantcontact_access_token'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <?php
                                    $lists = '';
                                    if ( isset($settings['constantcontact_access_token']) and trim($settings['constantcontact_access_token']) != '' and isset($settings['constantcontact_api_key']) and trim($settings['constantcontact_api_key']) != '' ){
                                        $api_key = $settings['constantcontact_api_key'];
                                        $lists  = wp_remote_retrieve_body(wp_remote_get("https://api.constantcontact.com/v2/lists?api_key=".$api_key, array(
                                            'body' => null,
                                            'timeout' => '10',
                                            'redirection' => '10',
                                            'headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $settings['constantcontact_access_token']),
                                        )));
                                    }
                                    
                                    ?>
                                    
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_constantcontact_list_id"><?php _e('Select List', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][constantcontact_list_id]" id="mec_settings_constantcontact_list_id">
                                                <?php
                                                if ( isset($lists) and !empty($lists)) {
                                                    foreach (json_decode($lists) as $list) {
                                                    ?>
                                                        <option <?php if(isset($settings['constantcontact_list_id']) and $list->id == $settings['constantcontact_list_id']) echo 'selected="selected"'; ?> value="<?php echo $list->id ; ?>"><?php echo $list->name ; ?></option>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('Select List', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e("Please fill in the API key and Access Token field and save settings. after that, please refresh the page and select a list.", 'modern-events-calendar-lite'); ?></p></div>    
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="active_campaign_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Active Campaign Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][active_campaign_status]" value="0" />
                                        <input onchange="jQuery('#mec_active_campaign_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][active_campaign_status]" <?php if(isset($settings['active_campaign_status']) and $settings['active_campaign_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Active Campaign Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_active_campaign_status_container_toggle" class="<?php if((isset($settings['active_campaign_status']) and !$settings['active_campaign_status']) or !isset($settings['active_campaign_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_active_campaign_api_url"><?php _e('API URL', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_active_campaign_api_url" name="mec[settings][active_campaign_api_url]" value="<?php echo ((isset($settings['active_campaign_api_url']) and trim($settings['active_campaign_api_url']) != '') ? $settings['active_campaign_api_url'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_active_campaign_api_key"><?php _e('API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_active_campaign_api_key" name="mec[settings][active_campaign_api_key]" value="<?php echo ((isset($settings['active_campaign_api_key']) and trim($settings['active_campaign_api_key']) != '') ? $settings['active_campaign_api_key'] : ''); ?>" />
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_active_campaign_list_id"><?php _e('List ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_active_campaign_list_id" name="mec[settings][active_campaign_list_id]" value="<?php echo ((isset($settings['active_campaign_list_id']) and trim($settings['active_campaign_list_id']) != '') ? $settings['active_campaign_list_id'] : ''); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="aweber_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('AWeber Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][aweber_status]" value="0" />
                                        <input onchange="jQuery('#mec_aweber_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][aweber_status]" <?php if(isset($settings['aweber_status']) and $settings['aweber_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable AWeber Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_aweber_status_container_toggle" class="<?php if((isset($settings['aweber_status']) and !$settings['aweber_status']) or !isset($settings['aweber_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_aweber_list_id"><?php _e('List ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_aweber_list_id" name="mec[settings][aweber_list_id]" value="<?php echo ((isset($settings['aweber_list_id']) and trim($settings['aweber_list_id']) != '') ? $settings['aweber_list_id'] : ''); ?>" />
                                            <p class="description"><?php echo sprintf(__("%s plugin should be installed and connected to your AWeber account.", 'modern-events-calendar-lite'), '<a href="https://wordpress.org/plugins/aweber-web-form-widget/" target="_blank">AWeber for WordPress</a>'); ?></p>
                                            <p class="description"><?php echo sprintf(__('More information about the list ID can be found %s.', 'modern-events-calendar-lite'), '<a href="https://help.aweber.com/hc/en-us/articles/204028426" target="_blank">'.__('here', 'modern-events-calendar-lite').'</a>'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="mailpoet_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('MailPoet Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][mailpoet_status]" value="0" />
                                        <input onchange="jQuery('#mec_mailpoet_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][mailpoet_status]" <?php if(isset($settings['mailpoet_status']) and $settings['mailpoet_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable MailPoet Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_mailpoet_status_container_toggle" class="<?php if((isset($settings['mailpoet_status']) and !$settings['mailpoet_status']) or !isset($settings['mailpoet_status'])) echo 'mec-util-hidden'; ?>">
                                    <?php if(class_exists(\MailPoet\API\API::class)): $mailpoet_api = \MailPoet\API\API::MP('v1'); $mailpoets_lists = $mailpoet_api->getLists(); ?>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_mailpoet_list_id"><?php _e('List', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][mailpoet_list_id]" id="mec_settings_mailpoet_list_id">
                                                <option value="">-----</option>
                                                <?php foreach($mailpoets_lists as $mailpoets_list): ?>
                                                <option value="<?php echo $mailpoets_list['id']; ?>" <?php echo ((isset($settings['mailpoet_list_id']) and trim($settings['mailpoet_list_id']) == $mailpoets_list['id']) ? 'selected="selected"' : ''); ?>><?php echo $mailpoets_list['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <p class="description"><?php echo sprintf(__("%s plugin should be installed and activated.", 'modern-events-calendar-lite'), '<a href="https://wordpress.org/plugins/mailpoet/" target="_blank">MailPoet</a>'); ?></p>
                                </div>
                            </div>

                            <div id="sendfox_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Sendfox Integration', 'modern-events-calendar-lite'); ?></h4>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][sendfox_status]" value="0" />
                                        <input onchange="jQuery('#mec_sendfox_status_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][sendfox_status]" <?php if(isset($settings['sendfox_status']) and $settings['sendfox_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Sendfox Integration', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_sendfox_status_container_toggle" class="<?php if((isset($settings['sendfox_status']) and !$settings['sendfox_status']) or !isset($settings['sendfox_status'])) echo 'mec-util-hidden'; ?>">

                                    <?php if(function_exists('gb_sf4wp_get_lists')): $sendfox_lists = gb_sf4wp_get_lists(); ?>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_sendfox_list_id"><?php _e('List ID', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][sendfox_list_id]" id="mec_settings_sendfox_list_id">
                                                <?php foreach($sendfox_lists['result']['data'] as $sendfox_list): ?>
                                                <option value="<?php echo $sendfox_list['id']; ?>" <?php echo ((isset($settings['sendfox_list_id']) and trim($settings['sendfox_list_id']) == $sendfox_list['id']) ? 'selected="selected"' : ''); ?>><?php echo $sendfox_list['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <p class="description"><?php echo sprintf(__("%s plugin should be installed and connected to your Sendfox account.", 'modern-events-calendar-lite'), '<a href="https://wordpress.org/plugins/wp-sendfox/" target="_blank">WP Sendfox</a>'); ?></p>
                                </div>
                            </div>

                        <?php endif; ?>

                        <?php do_action('mec-settings-page-before-form-end', $settings) ?>

                        <div class="mec-options-fields">
                            <?php wp_nonce_field('mec_options_form'); ?>
                            <button style="display: none;" id="mec_settings_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="wns-be-footer">
        <a id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

</div>

<script type="text/javascript">
jQuery(document).ready(function()
{   
    jQuery(".dpr-save-btn").on('click', function(event)
    {
        event.preventDefault();
        jQuery("#mec_settings_form_button").trigger('click');
    });
});

var archive_value = jQuery('#mec_settings_default_skin_archive').val();
function mec_archive_skin_style_changed(archive_value)
{
    jQuery('.mec-archive-skins').hide();
    jQuery('.mec-archive-skins.mec-archive-'+archive_value+'-skins').show();
}
mec_archive_skin_style_changed(archive_value);

var category_value = jQuery('#mec_settings_default_skin_category').val();
function mec_category_skin_style_changed(category_value)
{
    jQuery('.mec-category-skins').hide();
    jQuery('.mec-category-skins.mec-category-'+category_value+'-skins').show();
}
mec_category_skin_style_changed(category_value);

jQuery("#mec_settings_form").on('submit', function(event)
{
    event.preventDefault();
    
    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
    {
        jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Checking ...', 'modern-events-calendar-lite')); ?>");
    } 
    
    var settings = jQuery("#mec_settings_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=mec_save_settings&"+settings,
        beforeSend: function () {
            jQuery('.wns-be-main').append('<div class="mec-loarder-wrap mec-settings-loader"><div class="mec-loarder"><div></div><div></div><div></div></div></div>');
        },
        success: function(data)
        {
            // Remove the loading Class to the button
            setTimeout(function()
            {
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
                if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
                {
                    jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Please Refresh Page', 'modern-events-calendar-lite')); ?>");
                }
            }, 1000);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            setTimeout(function()
            {
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
            }, 1000);
        }
    });
});
</script>