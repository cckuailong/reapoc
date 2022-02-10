<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_mec $this */

$settings = $this->main->get_settings();

// WordPress Pages
$pages = get_pages();

// Event Fields
$event_fields = $this->main->get_event_fields();
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
        <?php $this->main->get_sidebar_menu('single_event'); ?>
    </div>

    <div class="wns-be-main">
        <div id="wns-be-notification"></div>
        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">

                    <form id="mec_single_form">

                        <div id="event_options" class="mec-options-fields active">

                            <h4 class="mec-form-subtitle"><?php _e('Single Event Page', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_single_event_date_format1"><?php _e('Single Event Date Format', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_settings_single_event_date_format1" name="mec[settings][single_date_format1]" value="<?php echo ((isset($settings['single_date_format1']) and trim($settings['single_date_format1']) != '') ? $settings['single_date_format1'] : 'M d Y'); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Single Event Date Format', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Default is M d Y", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_single_event_date_method"><?php _e('Date Method', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_single_event_date_method" name="mec[settings][single_date_method]">
                                        <option value="next" <?php echo (isset($settings['single_date_method']) and $settings['single_date_method'] == 'next') ? 'selected="selected"' : ''; ?>><?php _e('Next occurrence date', 'modern-events-calendar-lite'); ?></option>
                                        <option value="referred" <?php echo (isset($settings['single_date_method']) and $settings['single_date_method'] == 'referred') ? 'selected="selected"' : ''; ?>><?php _e('Referred date', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Date Method', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('Referred date" shows the event date based on referred date in event list.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_single_event_single_style"><?php _e('Single Event Style', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_single_event_single_style" name="mec[settings][single_single_style]">
                                        <option value="default" <?php echo (isset($settings['single_single_style']) and $settings['single_single_style'] == 'default') ? 'selected="selected"' : ''; ?>><?php _e('Default Style', 'modern-events-calendar-lite'); ?></option>
                                        <option value="modern" <?php echo (isset($settings['single_single_style']) and $settings['single_single_style'] == 'modern') ? 'selected="selected"' : ''; ?>><?php _e('Modern Style', 'modern-events-calendar-lite'); ?></option>
                                        <?php do_action('mec_single_style', $settings); ?>
                                        <?php if ( is_plugin_active( 'mec-single-builder/mec-single-builder.php' ) ) : ?>
                                        <option value="builder" <?php echo (isset($settings['single_single_style']) and $settings['single_single_style'] == 'builder') ? 'selected="selected"' : ''; ?>><?php _e('Elementor Single Builder', 'modern-events-calendar-lite'); ?></option>
                                        <?php endif; ?>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Single Event Style', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Choose your single event style.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <?php do_action('mec_single_style_setting_after', $this) ?>
                            <?php if($this->main->getPRO() and isset($this->settings['booking_status']) and $this->settings['booking_status']): ?>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_single_event_booking_style"><?php _e('Booking Style', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_single_event_booking_style" name="mec[settings][single_booking_style]">
                                        <option value="default" <?php echo (isset($settings['single_booking_style']) and $settings['single_booking_style'] == 'default') ? 'selected="selected"' : ''; ?>><?php _e('Default', 'modern-events-calendar-lite'); ?></option>
                                        <option value="modal" <?php echo (isset($settings['single_booking_style']) and $settings['single_booking_style'] == 'modal') ? 'selected="selected"' : ''; ?>><?php _e('Modal', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Booking Style', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Choose your Booking style. Note: When you set this feature to Modal, you cannot see the booking box if you set popup module view on shortcodes", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_tz_per_event"><?php _e('Timezone Per Event', 'modern-events-calendar-lite'); ?></label>
                                <label class="mec-col-9" id="mec_settings_tz_per_event" >
                                    <input type="hidden" name="mec[settings][tz_per_event]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][tz_per_event]" <?php if(isset($settings['tz_per_event']) and $settings['tz_per_event']) echo 'checked="checked"'; ?> /> <?php _e('Enable', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_gutenberg"><?php _e('Disable Block Editor (Gutenberg)', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label id="mec_settings_gutenberg" >
                                        <input type="hidden" name="mec[settings][gutenberg]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][gutenberg]" <?php if(!isset($settings['gutenberg']) or (isset($settings['gutenberg']) and $settings['gutenberg'])) echo 'checked="checked"'; ?> /> <?php _e('Disable Block Editor', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Block Editor', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If you want to use the new WordPress block editor you should keep this checkbox unchecked.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_breadcrumbs"><?php _e('Breadcrumbs', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label id="mec_settings_breadcrumbs" >
                                        <input type="hidden" name="mec[settings][breadcrumbs]" value="0" />
                                        <input type="checkbox" name="mec[settings][breadcrumbs]" id="mec_settings_breadcrumbs" <?php echo ((isset($settings['breadcrumbs']) and $settings['breadcrumbs']) ? 'checked="checked"' : ''); ?> value="1" onchange="jQuery('#mec_settings_breadcrumb_options').toggle();" /><?php _e('Enable Breadcrumbs.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Breadcrumbs', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Check this option, for showing the breadcrumbs on single event page", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div id="mec_settings_breadcrumb_options" class="<?php echo ((isset($settings['breadcrumbs']) and $settings['breadcrumbs']) ? '' : 'mec-util-hidden'); ?>">
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_breadcrumbs_category"><?php _e('Category in Breadcrumbs', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <label>
                                            <input type="hidden" name="mec[settings][breadcrumbs_category]" value="0" />
                                            <input type="checkbox" name="mec[settings][breadcrumbs_category]" id="mec_settings_breadcrumbs_category" <?php echo ((isset($settings['breadcrumbs_category']) and $settings['breadcrumbs_category']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Include Category in Breadcrumbs.', 'modern-events-calendar-lite'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_organizer_description"><?php _e('Organizer Description', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label id="mec_settings_organizer_description" >
                                        <input type="hidden" name="mec[settings][organizer_description]" value="0" />
                                        <input type="checkbox" name="mec[settings][organizer_description]" id="mec_settings_organizer_description" <?php echo ((isset($settings['organizer_description']) and $settings['organizer_description']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Enable Description For Organizer.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Organizer Description', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If you want to turn on description for other organizer plase go to 'Additional Organizers - After enabling and saving the settings, reloading the settings page.' tab", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_location_description"><?php _e('Location Description', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label id="mec_settings_location_description" >
                                        <input type="hidden" name="mec[settings][location_description]" value="0" />
                                        <input type="checkbox" name="mec[settings][location_description]" id="mec_settings_location_description" <?php echo ((isset($settings['location_description']) and $settings['location_description']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Enable Description For Location.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Location Description', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("If you want to turn on description for other location plase go to 'Additional Locations - After enabling and saving the settings, reloading the settings page.' tab", 'modern-events-calendar-lite'); ?></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_settings_single_cost_type"><?php _e('Event Cost Type', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <select id="mec_settings_single_cost_type" name="mec[settings][single_cost_type]">
                                        <option value="numeric" <?php echo (isset($settings['single_cost_type']) and $settings['single_cost_type'] == 'numeric') ? 'selected="selected"' : ''; ?>><?php _e('Numeric (Searchable)', 'modern-events-calendar-lite'); ?></option>
                                        <option value="alphabetic" <?php echo (isset($settings['single_cost_type']) and $settings['single_cost_type'] == 'alphabetic') ? 'selected="selected"' : ''; ?>><?php _e('Alphabetic (Not Searchable)', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Event Cost Type', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Choose Numeric type if you want to include the event cost field into the search form. If you do not need the search ability then you can choose Alphabetic type.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/event-detailssingle-event-page/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                            <label class="mec-col-3" for="mec_settings_location_description"><?php _e('Change Currency Per Event', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <label for="mec_settings_currency_per_event">
                                        <input type="hidden" name="mec[settings][currency_per_event]" value="0" />
                                        <input type="checkbox" name="mec[settings][currency_per_event]" id="mec_settings_currency_per_event" <?php echo ((isset($settings['currency_per_event']) and $settings['currency_per_event'] == '1') ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Enable Currency Per Event', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div id="event_form_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Custom Fields', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-container">
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][display_event_fields_backend]" value="0" />
                                        <input onchange="jQuery('#mec_event_fields_container').toggle();" value="1" type="checkbox" name="mec[settings][display_event_fields_backend]" <?php if(!isset($settings['display_event_fields_backend']) or (isset($settings['display_event_fields_backend']) and $settings['display_event_fields_backend'])) echo 'checked="checked"'; ?> /> <?php _e('Event Data', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="<?php if(isset($settings['display_event_fields_backend']) and !$settings['display_event_fields_backend'] ) echo 'mec-util-hidden'; ?>" id="mec_event_fields_container">
                                <div class="mec-container">
                                    <div class="mec-form-row" id="mec_event_form_container">
                                        <?php /** Don't remove this hidden field **/ ?>
                                        <input type="hidden" name="mec[event_fields]" value="" />

                                        <ul id="mec_event_form_fields">
                                            <?php
                                            $i = 0;
                                            foreach($event_fields as $key => $event_field)
                                            {
                                                if(!is_numeric($key)) continue;
                                                $i = max($i, $key);

                                                if($event_field['type'] == 'text') echo $this->main->field_text($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'email') echo $this->main->field_email($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'url') echo $this->main->field_url($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'date') echo $this->main->field_date($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'tel') echo $this->main->field_tel($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'textarea') echo $this->main->field_textarea($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'p') echo $this->main->field_p($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'checkbox') echo $this->main->field_checkbox($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'radio') echo $this->main->field_radio($key, $event_field, 'event');
                                                elseif($event_field['type'] == 'select') echo $this->main->field_select($key, $event_field, 'event');
                                            }
                                            ?>
                                        </ul>
                                        <div id="mec_event_form_field_types">
                                            <button type="button" class="button" data-type="text"><?php _e('Text', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="email"><?php _e('Email', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="url"><?php _e('URL', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="date"><?php _e('Date', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="tel"><?php _e('Tel', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="textarea"><?php _e('Textarea', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="p"><?php _e('Paragraph', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="checkbox"><?php _e('Checkboxes', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="radio"><?php _e('Radio Buttons', 'modern-events-calendar-lite'); ?></button>
                                            <button type="button" class="button" data-type="select"><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="mec_new_event_field_key" value="<?php echo $i + 1; ?>" />
                                <div class="mec-util-hidden">
                                    <div id="mec_event_field_text">
                                        <?php echo $this->main->field_text(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_email">
                                        <?php echo $this->main->field_email(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_url">
                                        <?php echo $this->main->field_url(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_tel">
                                        <?php echo $this->main->field_tel(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_date">
                                        <?php echo $this->main->field_date(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_textarea">
                                        <?php echo $this->main->field_textarea(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_checkbox">
                                        <?php echo $this->main->field_checkbox(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_radio">
                                        <?php echo $this->main->field_radio(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_select">
                                        <?php echo $this->main->field_select(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_p">
                                        <?php echo $this->main->field_p(':i:', array(), 'event'); ?>
                                    </div>
                                    <div id="mec_event_field_option">
                                        <?php echo $this->main->field_option(':fi:', ':i:', array(), 'event'); ?>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][display_event_fields]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][display_event_fields]" <?php if(!isset($settings['display_event_fields']) or (isset($settings['display_event_fields']) and $settings['display_event_fields'])) echo 'checked="checked"'; ?> /> <?php _e('Display Event Fields in Single Event Pages', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="countdown_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Countdown', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][countdown_status]" value="0" />
                                    <input onchange="jQuery('#mec_count_down_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][countdown_status]" <?php if(isset($settings['countdown_status']) and $settings['countdown_status']) echo 'checked="checked"'; ?> /> <?php _e('Show countdown module on event page', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_count_down_container_toggle" class="<?php if((isset($settings['countdown_status']) and !$settings['countdown_status']) or !isset($settings['countdown_status'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_countdown_list"><?php _e('Countdown Style', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <select id="mec_settings_countdown_list" name="mec[settings][countdown_list]">
                                            <option value="default" <?php echo ((isset($settings['countdown_list']) and $settings['countdown_list'] == "default") ? 'selected="selected"' : ''); ?> ><?php _e('Plain Style', 'modern-events-calendar-lite'); ?></option>
                                            <option value="flip" <?php echo ((isset($settings['countdown_list']) and $settings['countdown_list'] == "flip") ? 'selected="selected"' : ''); ?> ><?php _e('Flip Style', 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <input type="hidden" name="mec[settings][countdown_disable_for_ongoing_events]" value="0">
                                        <input type="checkbox" id="mec_settings_countdown_disable_for_ongoing_events" name="mec[settings][countdown_disable_for_ongoing_events]" value="1" <?php echo (isset($settings['countdown_disable_for_ongoing_events']) and $settings['countdown_disable_for_ongoing_events']) ? 'checked="checked"' : ''; ?>>
                                        <label for="mec_settings_countdown_disable_for_ongoing_events"><?php _e('Disable for ongoing events', 'modern-events-calendar-lite'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="exceptional_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Exceptional days (Exclude Dates)', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][exceptional_days]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][exceptional_days]" <?php if(isset($settings['exceptional_days']) and $settings['exceptional_days']) echo 'checked="checked"'; ?> /> <?php _e('Show exceptional days option on Add/Edit events page', 'modern-events-calendar-lite'); ?>
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Exceptional days (Exclude Dates)', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e("Using this option you can exclude certain days from event occurrence dates.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/exceptional-days/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="additional_organizers" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Additional Organizers', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][additional_organizers]" value="0" />
                                    <input onchange="jQuery('#mec_settings_additional_organizers_description').toggle();" value="1" type="checkbox" name="mec[settings][additional_organizers]" <?php if(!isset($settings['additional_organizers']) or (isset($settings['additional_organizers']) and $settings['additional_organizers'])) echo 'checked="checked"'; ?> /> <?php _e('Show additional organizers option on Add/Edit events page and single event page.', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_settings_additional_organizers_description" class="<?php if((isset($settings['additional_organizers']) and !$settings['additional_organizers']) or !isset($settings['additional_organizers'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label id="mec_settings_additional_organizers_description">
                                        <input type="hidden" name="mec[settings][addintional_organizers_description]" value="0" />
                                        <input type="checkbox" name="mec[settings][addintional_organizers_description]" id="mec_settings_additional_organizers_description" <?php echo ((isset($settings['addintional_organizers_description']) and $settings['addintional_organizers_description']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Enable Description For Other Organizers.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div id="additional_locations" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Additional Locations', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][additional_locations]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][additional_locations]" <?php if(!isset($settings['additional_locations']) or (isset($settings['additional_locations']) and $settings['additional_locations'])) echo 'checked="checked"'; ?> /> <?php _e('Show additional locations option on Add/Edit events page and single event page.', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_settings_additional_locations_description" class="<?php if((isset($settings['additional_locations']) and !$settings['additional_locations']) or !isset($settings['additional_locations'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label id="mec_settings_additional_locations_description">
                                        <input type="hidden" name="mec[settings][addintional_locations_description]" value="0" />
                                        <input type="checkbox" name="mec[settings][addintional_locations_description]" id="mec_settings_additional_locations_description" <?php echo ((isset($settings['addintional_locations_description']) and $settings['addintional_locations_description']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Enable Description For Other Locations.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label id="mec_settings_additional_locations_disable_title">
                                        <input type="hidden" name="mec[settings][additional_locations_disable_title]" value="0" />
                                        <input type="checkbox" name="mec[settings][additional_locations_disable_title]" id="mec_settings_additional_locations_disable_title" <?php echo ((isset($settings['additional_locations_disable_title']) and $settings['additional_locations_disable_title']) ? 'checked="checked"' : ''); ?> value="1" /><?php _e('Disable Title For Other Locations.', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="related_events" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Related Events', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][related_events]" value="0" />
                                    <input onchange="jQuery('#mec_related_events_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][related_events]" <?php if(isset($settings['related_events']) and $settings['related_events']) echo 'checked="checked"'; ?> /> <?php _e('Display related events based on taxonomy in single event page.', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_related_events_container_toggle" class="<?php if((isset($settings['related_events']) and !$settings['related_events']) or !isset($settings['related_events'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row" style="margin-top:20px;">
                                    <label style="margin-right:7px;"><?php _e('Select Taxonomies:', 'modern-events-calendar-lite'); ?></label>
                                    <label style="margin-right:7px;margin-bottom: 20px">
                                        <input type="hidden" name="mec[settings][related_events_basedon_category]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_category]" <?php if(isset($settings['related_events_basedon_category']) and $settings['related_events_basedon_category']) echo 'checked="checked"'; ?> /> <?php _e('Category', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][related_events_basedon_organizer]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_organizer]" <?php if(isset($settings['related_events_basedon_organizer']) and $settings['related_events_basedon_organizer']) echo 'checked="checked"'; ?> /> <?php _e('Organizer', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][related_events_basedon_location]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_location]" <?php if(isset($settings['related_events_basedon_location']) and $settings['related_events_basedon_location']) echo 'checked="checked"'; ?> /> <?php _e('Location', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <?php if(isset($settings['speakers_status']) and $settings['speakers_status']): ?>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][related_events_basedon_speaker]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_speaker]" <?php if(isset($settings['related_events_basedon_speaker']) and $settings['related_events_basedon_speaker']) echo 'checked="checked"'; ?> /> <?php _e('Speaker', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <?php endif; ?>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][related_events_basedon_label]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_label]" <?php if(isset($settings['related_events_basedon_label']) and $settings['related_events_basedon_label']) echo 'checked="checked"'; ?> /> <?php _e('Label', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][related_events_basedon_tag]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_basedon_tag]" <?php if(isset($settings['related_events_basedon_tag']) and $settings['related_events_basedon_tag']) echo 'checked="checked"'; ?> /> <?php _e('Tag', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_related_events_limit"><?php _e('Max Events', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <input type="number" min="1" step="1" id="mec_settings_related_events_limit" name="mec[settings][related_events_limit]" value="<?php echo ((isset($settings['related_events_limit']) and trim($settings['related_events_limit']) != '') ? $settings['related_events_limit'] : '30'); ?>" />
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][related_events_display_expireds]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][related_events_display_expireds]" <?php if(isset($settings['related_events_display_expireds']) and $settings['related_events_display_expireds']) echo 'checked="checked"'; ?> /> <?php _e('Display Expired Events', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="next_previous_events" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Next / Previous Events', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][next_previous_events]" value="0" />
                                    <input onchange="jQuery('#mec_next_previous_events_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][next_previous_events]" <?php if(isset($settings['next_previous_events']) and $settings['next_previous_events']) echo 'checked="checked"'; ?> /> <?php _e('Display next / previous events based on taxonomy in single event page.', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_next_previous_events_container_toggle" class="<?php if((isset($settings['next_previous_events']) and !$settings['next_previous_events']) or !isset($settings['next_previous_events'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row" style="margin-top:20px;">
                                    <label style="margin-right:7px;" for="mec_settings_countdown_list"><?php _e('Select Taxonomies:', 'modern-events-calendar-lite'); ?></label>
                                    <label style="margin-right:7px; margin-bottom: 20px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_category]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_category]" <?php if(isset($settings['next_previous_events_category']) and $settings['next_previous_events_category']) echo 'checked="checked"'; ?> /> <?php _e('Category', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_organizer]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_organizer]" <?php if(isset($settings['next_previous_events_organizer']) and $settings['next_previous_events_organizer']) echo 'checked="checked"'; ?> /> <?php _e('Organizer', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_location]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_location]" <?php if(isset($settings['next_previous_events_location']) and $settings['next_previous_events_location']) echo 'checked="checked"'; ?> /> <?php _e('Location', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <?php if(isset($settings['speakers_status']) and $settings['speakers_status']) : ?>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_speaker]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_speaker]" <?php if(isset($settings['next_previous_events_speaker']) and $settings['next_previous_events_speaker']) echo 'checked="checked"'; ?> /> <?php _e('Speaker', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <?php endif; ?>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_label]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_label]" <?php if(isset($settings['next_previous_events_label']) and $settings['next_previous_events_label']) echo 'checked="checked"'; ?> /> <?php _e('Label', 'modern-events-calendar-lite'); ?>
                                    </label>
                                    <label style="margin-right:7px;">
                                        <input type="hidden" name="mec[settings][next_previous_events_tag]" value="0" />
                                        <input value="1" type="checkbox" name="mec[settings][next_previous_events_tag]" <?php if(isset($settings['next_previous_events_tag']) and $settings['next_previous_events_tag']) echo 'checked="checked"'; ?> /> <?php _e('Tag', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="per_occurrences" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Edit Per Occurrences', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][per_occurrences_status]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][per_occurrences_status]" <?php if(isset($settings['per_occurrences_status']) and $settings['per_occurrences_status']) echo 'checked="checked"'; ?> /> <?php _e('Ability to edit some event information per occurrence', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>

                        <div id="shortcode_only_bookers" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Content only for bookers', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <div class="mec-col-12">
                                    <p><?php echo sprintf(__('if you need to show a certain content only for booker users, you can enclose your content using %s shortcode. For example you can use %s code to say "Hi" to bookers.', 'modern-events-calendar-lite'), '<code>[mec-only-booked-users]</code>', '<code>[mec-only-booked-users]Hi[/mec-only-booked-users]</code>'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mec-options-fields">
                            <?php wp_nonce_field('mec_options_form'); ?>
                            <button style="display: none;" id="mec_single_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
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
        jQuery("#mec_single_form_button").trigger('click');
    });
});

jQuery("#mec_single_form").on('submit', function(event)
{
    event.preventDefault();

    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
    {
        jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Checking ...', 'modern-events-calendar-lite')); ?>");
    }

    var settings = jQuery("#mec_single_form").serialize();
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