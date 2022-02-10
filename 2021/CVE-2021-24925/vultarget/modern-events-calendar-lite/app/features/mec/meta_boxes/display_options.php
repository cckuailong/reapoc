<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_mec $this */

// Fix conflict between ACF and niceSelect
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if(is_plugin_active('advanced-custom-fields/acf.php')) remove_action('admin_footer', 'acf_enqueue_uploader', 5);
if(is_plugin_active('advanced-custom-fields-pro/acf.php')) remove_action('admin_footer', 'acf_enqueue_uploader', 5);
if(is_plugin_active('wp-recipe-maker/wp-recipe-maker.php')) remove_action('admin_footer', array('WPRM_Modal', 'add_modal_content'));

// Skin Options
$skins = $this->main->get_skins();
$selected_skin = get_post_meta($post->ID, 'skin', true);
$sk_options = get_post_meta($post->ID, 'sk-options', true);

// MEC Events
$events = $this->main->get_events();

// Upcoming Events
$upcoming_event_ids = $this->main->get_upcoming_event_ids();
?>
<div class="mec-calendar-metabox">

    <!-- SKIN OPTIONS -->
    <div class="mec-meta-box-fields" id="mec_meta_box_calendar_skin_options">
        <div class="mec-form-row">
            <label class="mec-col-4" for="mec_skin"><?php _e('Skin', 'modern-events-calendar-lite'); ?></label>
            <select class="mec-col-4 wn-mec-select mec-custom-nice-select" name="mec[skin]" id="mec_skin" onchange="if(jQuery('#mec_skin').val() != 'carousel' ){ jQuery('.mec-carousel-archive-link').hide();jQuery('.mec-carousel-head-text').hide();}">
                <?php foreach($skins as $skin=>$name): ?>
                <option value="<?php echo $skin; ?>" <?php if($selected_skin == $skin) echo 'selected="selected"'; ?>>
                    <?php echo $name; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mec-skins-options-container">

            <!-- List View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_list_skin_options_container">
                <?php $sk_options_list = isset($sk_options['list']) ? $sk_options['list'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_list_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][list][style]" id="mec_skin_list_style" onchange="mec_skin_style_changed('list', this.value, this); if(this.value == 'accordion'){ jQuery('.mec-sed-methode-container').hide();jQuery('.mec-toggle-month-divider').show(); }else{ jQuery('.mec-sed-methode-container').show();jQuery('.mec-toggle-month-divider').hide()}">
						<option value="classic" <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] == 'classic') echo 'selected="selected"'; ?>><?php _e('Classic', 'modern-events-calendar-lite'); ?></option>
                        <option value="minimal" <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] == 'minimal') echo 'selected="selected"'; ?>><?php _e('Minimal', 'modern-events-calendar-lite'); ?></option>
                        <option value="modern" <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <option value="standard" <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] == 'standard') echo 'selected="selected"'; ?>><?php _e('Standard', 'modern-events-calendar-lite'); ?></option>
                        <option value="accordion" <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] == 'accordion') echo 'selected="selected"'; ?>><?php _e('Accordion', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_list_fluent', $sk_options_list['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_list_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][list][start_date_type]" id="mec_skin_list_start_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_list_start_date_container').show(); else jQuery('#mec_skin_list_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_list['start_date_type']) or (isset($sk_options_list['start_date_type']) and $sk_options_list['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_list_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][list][start_date]" id="mec_skin_list_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_list['start_date'])) echo $sk_options_list['start_date']; ?>" />
                    </div>
                </div>
                <!-- Start Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_list_end_date_type"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][list][end_date_type]" id="mec_skin_list_end_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_list_end_date_container').show(); else jQuery('#mec_skin_list_end_date_container').hide();">
                        <option value="date" <?php if(isset($sk_options_list['end_date_type']) and $sk_options_list['end_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                        <option value="today" <?php if(isset($sk_options_list['end_date_type']) and $sk_options_list['end_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_list['end_date_type']) and $sk_options_list['end_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php echo (!isset($sk_options_list['end_date_type']) or (isset($sk_options_list['end_date_type']) and $sk_options_list['end_date_type'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_list_end_date_container">
                        <input type="text" class="mec_date_picker" name="mec[sk-options][list][maximum_date_range]" value="<?php echo isset($sk_options_list['maximum_date_range']) ? esc_attr($sk_options_list['maximum_date_range']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <!-- End Maximum Date -->
                <div class="mec-form-row mec-skin-list-date-format-container <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] != 'classic') echo 'mec-util-hidden'; ?>" id="mec_skin_list_date_format_classic_container">
                    <label class="mec-col-4" for="mec_skin_list_classic_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][list][classic_date_format1]" id="mec_skin_list_classic_date_format1" value="<?php echo ((isset($sk_options_list['classic_date_format1']) and trim($sk_options_list['classic_date_format1']) != '') ? $sk_options_list['classic_date_format1'] : 'M d Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "M d Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-list-date-format-container <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] != 'M d Y') echo 'mec-util-hidden'; ?>" id="mec_skin_list_date_format_minimal_container">
                    <label class="mec-col-4" for="mec_skin_list_minimal_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][list][minimal_date_format1]" id="mec_skin_list_minimal_date_format1" value="<?php echo ((isset($sk_options_list['minimal_date_format1']) and trim($sk_options_list['minimal_date_format1']) != '') ? $sk_options_list['minimal_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][list][minimal_date_format2]" id="mec_skin_list_minimal_date_format2" value="<?php echo ((isset($sk_options_list['minimal_date_format2']) and trim($sk_options_list['minimal_date_format2']) != '') ? $sk_options_list['minimal_date_format2'] : 'M'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][list][minimal_date_format3]" id="mec_skin_list_minimal_date_format3" value="<?php echo ((isset($sk_options_list['minimal_date_format3']) and trim($sk_options_list['minimal_date_format3']) != '') ? $sk_options_list['minimal_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, M and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-list-date-format-container <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] != 'modern') echo 'mec-util-hidden'; ?>" id="mec_skin_list_date_format_modern_container">
                    <label class="mec-col-4" for="mec_skin_list_modern_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][list][modern_date_format1]" id="mec_skin_list_modern_date_format1" value="<?php echo ((isset($sk_options_list['modern_date_format1']) and trim($sk_options_list['modern_date_format1']) != '') ? $sk_options_list['modern_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][list][modern_date_format2]" id="mec_skin_list_modern_date_format2" value="<?php echo ((isset($sk_options_list['modern_date_format2']) and trim($sk_options_list['modern_date_format2']) != '') ? $sk_options_list['modern_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][list][modern_date_format3]" id="mec_skin_list_modern_date_format3" value="<?php echo ((isset($sk_options_list['modern_date_format3']) and trim($sk_options_list['modern_date_format3']) != '') ? $sk_options_list['modern_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-list-date-format-container <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] != 'standard') echo 'mec-util-hidden'; ?>" id="mec_skin_list_date_format_standard_container">
                    <label class="mec-col-4" for="mec_skin_list_standard_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][list][standard_date_format1]" id="mec_skin_list_standard_date_format1" value="<?php echo ((isset($sk_options_list['standard_date_format1']) and trim($sk_options_list['standard_date_format1']) != '') ? $sk_options_list['standard_date_format1'] : 'd M'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "M d"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-list-date-format-container <?php if(isset($sk_options_list['style']) and $sk_options_list['style'] != 'accordion') echo 'mec-util-hidden'; ?>" id="mec_skin_list_date_format_accordion_container">
                    <label class="mec-col-4" for="mec_skin_list_accordion_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][list][accordion_date_format1]" id="mec_skin_list_accordion_date_format1" value="<?php echo ((isset($sk_options_list['accordion_date_format1']) and trim($sk_options_list['accordion_date_format1']) != '') ? $sk_options_list['accordion_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][list][accordion_date_format2]" id="mec_skin_list_accordion_date_format2" value="<?php echo ((isset($sk_options_list['accordion_date_format2']) and trim($sk_options_list['accordion_date_format2']) != '') ? $sk_options_list['accordion_date_format2'] : 'F'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('TDefault values are d and F', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_list_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][list][limit]" id="mec_skin_list_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_list['limit'])) echo $sk_options_list['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_list_localtime">
					<div class="mec-col-4">
						<label for="mec_skin_list_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][include_local_time]" id="mec_skin_list_include_local_time" value="1" <?php if(isset($sk_options_list['include_local_time']) and trim($sk_options_list['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Include Events Times -->
                <div class="mec-form-row mec-switcher mec-include-events-times">
					<div class="mec-col-4">
						<label for="mec_skin_list_include_events_times"><?php _e('Include Events Times', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][include_events_times]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][include_events_times]" id="mec_skin_list_include_events_times" value="1" <?php if(isset($sk_options_list['include_events_times']) and trim($sk_options_list['include_events_times'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_include_events_times"></label>
                    </div>
                </div>
                <!-- End Include Events Times -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
                        <label for="mec_skin_list_load_more_button"><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][list][load_more_button]" value="0" />
						<input type="checkbox" name="mec[sk-options][list][load_more_button]" id="mec_skin_list_load_more_button" value="1" <?php if(!isset($sk_options_list['load_more_button']) or (isset($sk_options_list['load_more_button']) and $sk_options_list['load_more_button'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_load_more_button"></label>
					</div>
                </div>
                <div class="mec-form-row mec-switcher mec-not-list-fluent">
					<div class="mec-col-4">
						<label for="mec_skin_list_month_divider"><?php _e('Show Month Divider', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][list][month_divider]" value="0" />
						<input type="checkbox" name="mec[sk-options][list][month_divider]" id="mec_skin_list_month_divider" value="1" <?php if(!isset($sk_options_list['month_divider']) or (isset($sk_options_list['month_divider']) and $sk_options_list['month_divider'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_list_month_divider"></label>
					</div>
                </div>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher" id="mec_skin_list_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_list_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][display_label]" id="mec_skin_list_display_label" value="1" <?php if(isset($sk_options_list['display_label']) and trim($sk_options_list['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_list_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_list_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][reason_for_cancellation]" id="mec_skin_list_reason_for_cancellation" value="1" <?php if(isset($sk_options_list['reason_for_cancellation']) and trim($sk_options_list['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_list_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_list_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][display_categories]" id="mec_skin_list_display_categories" value="1" <?php if(isset($sk_options_list['display_categories']) and trim($sk_options_list['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('list', (isset($sk_options_list['display_organizer']) ? $sk_options_list['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label for="mec_skin_list_map_on_top"><?php _e('Show Map on top', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <?php if(!$this->main->getPRO()): ?>
                        <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                        <?php else: ?>
                        <input type="hidden" name="mec[sk-options][list][map_on_top]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][map_on_top]" id="mec_skin_list_map_on_top" value="1" onchange="mec_skin_map_toggle(this);" <?php if(isset($sk_options_list['map_on_top']) and $sk_options_list['map_on_top']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_map_on_top"></label>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Start Set Map Geolocation -->
                <div class="mec-form-row mec-switcher mec-set-geolocation <?php if(!isset($sk_options_list['map_on_top']) or (isset($sk_options_list['map_on_top']) and !$sk_options_list['map_on_top'])) echo 'mec-util-hidden'; ?>">
                    <div class="mec-col-4">
                        <label for="mec_skin_list_set_geo_location"><?php _e('Geolocation', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][set_geolocation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][set_geolocation]" id="mec_skin_list_set_geo_location" value="1" onchange="mec_skin_geolocation_toggle(this);"
                            <?php if(isset($sk_options_list['set_geolocation']) and trim($sk_options_list['set_geolocation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_set_geo_location"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher mec-set-geolocation-focus <?php if((!isset($sk_options_list['set_geolocation']) or (isset($sk_options_list['set_geolocation']) and !$sk_options_list['set_geolocation'])) or (!isset($sk_options_list['map_on_top']) or (isset($sk_options_list['map_on_top']) and !$sk_options_list['map_on_top']))) echo 'mec-util-hidden'; ?>">
                    <div class="mec-col-4">
                        <label for="mec_skin_list_set_geo_location_focus"><?php _e('Disable Geolocation Force Focus', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][set_geolocation_focus]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][set_geolocation_focus]" id="mec_skin_list_set_geo_location_focus" value="1"
                            <?php if(isset($sk_options_list['set_geolocation_focus']) and trim($sk_options_list['set_geolocation_focus'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_list_set_geo_location_focus"></label>
                    </div>
                </div>
                <!-- End Set Map Geolocation -->
                <?php echo $this->booking_button_field('list', (isset($sk_options_list['booking_button']) ? $sk_options_list['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('list', (isset($sk_options_list['custom_data']) ? $sk_options_list['custom_data'] : 0)); ?>
                <div class="mec-form-row mec-switcher mec-toggle-month-divider mec-not-list-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_list_toggle_month_divider"><?php _e('Toggle for Month Divider', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][list][toggle_month_divider]" value="0" />
                        <input type="checkbox" name="mec[sk-options][list][toggle_month_divider]" id="mec_skin_toggle_month_divider" value="1" <?php if(isset($sk_options_list['toggle_month_divider']) and $sk_options_list['toggle_month_divider']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_toggle_month_divider"></label>
                    </div>
                </div>
                <div class="mec-sed-methode-container">
                    <?php echo $this->sed_method_field('list', (isset($sk_options_list['sed_method']) ? $sk_options_list['sed_method'] : 0), (isset($sk_options_list['image_popup']) ? $sk_options_list['image_popup'] : 0)); ?>
                </div>
                <?php do_action('mec_skin_options_list_end', $sk_options_list); ?>
            </div>

            <!-- Grid View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_grid_skin_options_container">
                <?php $sk_options_grid = isset($sk_options['grid']) ? $sk_options['grid'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_grid_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][grid][style]" id="mec_skin_grid_style" onchange="mec_skin_style_changed('grid', this.value);">
                        <option value="classic" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'classic') echo 'selected="selected"'; ?>><?php _e('Classic', 'modern-events-calendar-lite'); ?></option>
                        <option value="clean" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'clean') echo 'selected="selected"'; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <option value="minimal" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'minimal') echo 'selected="selected"'; ?>><?php _e('Minimal', 'modern-events-calendar-lite'); ?></option>
                        <option value="modern" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <option value="simple" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'simple') echo 'selected="selected"'; ?>><?php _e('Simple', 'modern-events-calendar-lite'); ?></option>
                        <option value="colorful" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'colorful') echo 'selected="selected"'; ?>><?php _e('Colorful', 'modern-events-calendar-lite'); ?></option>
                        <option value="novel" <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] == 'novel') echo 'selected="selected"'; ?>><?php _e('Novel', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_grid_fluent', $sk_options_grid['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_grid_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][grid][start_date_type]" id="mec_skin_grid_start_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_grid_start_date_container').show(); else jQuery('#mec_skin_grid_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_grid['start_date_type']) or (isset($sk_options_grid['start_date_type']) and $sk_options_grid['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_grid_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][grid][start_date]" id="mec_skin_grid_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_grid['start_date'])) echo $sk_options_grid['start_date']; ?>" />
                    </div>
                </div>
                <!-- Start Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_grid_end_date_type"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][grid][end_date_type]" id="mec_skin_grid_end_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_grid_end_date_container').show(); else jQuery('#mec_skin_grid_end_date_container').hide();">
                        <option value="date" <?php if(isset($sk_options_grid['end_date_type']) and $sk_options_grid['end_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                        <option value="today" <?php if(isset($sk_options_grid['end_date_type']) and $sk_options_grid['end_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_grid['end_date_type']) and $sk_options_grid['end_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php echo (!isset($sk_options_grid['end_date_type']) or (isset($sk_options_grid['end_date_type']) and $sk_options_grid['end_date_type'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_grid_end_date_container">
                        <input type="text" class="mec_date_picker" name="mec[sk-options][grid][maximum_date_range]" value="<?php echo isset($sk_options_grid['maximum_date_range']) ? esc_attr($sk_options_grid['maximum_date_range']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <!-- End Maximum Date -->
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'classic') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_classic_container">
                    <label class="mec-col-4" for="mec_skin_grid_classic_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][grid][classic_date_format1]" id="mec_skin_grid_classic_date_format1" value="<?php echo ((isset($sk_options_grid['classic_date_format1']) and trim($sk_options_grid['classic_date_format1']) != '') ? $sk_options_grid['classic_date_format1'] : 'd F Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "d F Y', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'clean') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_clean_container">
                    <label class="mec-col-4" for="mec_skin_grid_clean_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][clean_date_format1]" id="mec_skin_grid_clean_date_format1" value="<?php echo ((isset($sk_options_grid['clean_date_format1']) and trim($sk_options_grid['clean_date_format1']) != '') ? $sk_options_grid['clean_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][clean_date_format2]" id="mec_skin_grid_clean_date_format2" value="<?php echo ((isset($sk_options_grid['clean_date_format2']) and trim($sk_options_grid['clean_date_format2']) != '') ? $sk_options_grid['clean_date_format2'] : 'F'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d and F', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'minimal') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_minimal_container">
                    <label class="mec-col-4" for="mec_skin_grid_minimal_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][minimal_date_format1]" id="mec_skin_grid_minimal_date_format1" value="<?php echo ((isset($sk_options_grid['minimal_date_format1']) and trim($sk_options_grid['minimal_date_format1']) != '') ? $sk_options_grid['minimal_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][minimal_date_format2]" id="mec_skin_grid_minimal_date_format2" value="<?php echo ((isset($sk_options_grid['minimal_date_format2']) and trim($sk_options_grid['minimal_date_format2']) != '') ? $sk_options_grid['minimal_date_format2'] : 'M'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d and M', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'modern') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_modern_container">
                    <label class="mec-col-4" for="mec_skin_grid_modern_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][modern_date_format1]" id="mec_skin_grid_modern_date_format1" value="<?php echo ((isset($sk_options_grid['modern_date_format1']) and trim($sk_options_grid['modern_date_format1']) != '') ? $sk_options_grid['modern_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][grid][modern_date_format2]" id="mec_skin_grid_modern_date_format2" value="<?php echo ((isset($sk_options_grid['modern_date_format2']) and trim($sk_options_grid['modern_date_format2']) != '') ? $sk_options_grid['modern_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][grid][modern_date_format3]" id="mec_skin_grid_modern_date_format3" value="<?php echo ((isset($sk_options_grid['modern_date_format3']) and trim($sk_options_grid['modern_date_format3']) != '') ? $sk_options_grid['modern_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'simple') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_simple_container">
                    <label class="mec-col-4" for="mec_skin_grid_simple_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][grid][simple_date_format1]" id="mec_skin_grid_simple_date_format1" value="<?php echo ((isset($sk_options_grid['simple_date_format1']) and trim($sk_options_grid['simple_date_format1']) != '') ? $sk_options_grid['simple_date_format1'] : 'M d Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "M d Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'colorful') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_colorful_container">
                    <label class="mec-col-4" for="mec_skin_grid_colorful_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][grid][colorful_date_format1]" id="mec_skin_grid_colorful_date_format1" value="<?php echo ((isset($sk_options_grid['colorful_date_format1']) and trim($sk_options_grid['colorful_date_format1']) != '') ? $sk_options_grid['colorful_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][grid][colorful_date_format2]" id="mec_skin_grid_colorful_date_format2" value="<?php echo ((isset($sk_options_grid['colorful_date_format2']) and trim($sk_options_grid['colorful_date_format2']) != '') ? $sk_options_grid['colorful_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][grid][colorful_date_format3]" id="mec_skin_grid_colorful_date_format3" value="<?php echo ((isset($sk_options_grid['colorful_date_format3']) and trim($sk_options_grid['colorful_date_format3']) != '') ? $sk_options_grid['colorful_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-grid-date-format-container <?php if(isset($sk_options_grid['style']) and $sk_options_grid['style'] != 'novel') echo 'mec-util-hidden'; ?>" id="mec_skin_grid_date_format_novel_container">
                    <label class="mec-col-4" for="mec_skin_grid_novel_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][grid][novel_date_format1]" id="mec_skin_grid_novel_date_format1" value="<?php echo ((isset($sk_options_grid['novel_date_format1']) and trim($sk_options_grid['novel_date_format1']) != '') ? $sk_options_grid['novel_date_format1'] : 'd F Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "d F Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/grid-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_grid_count"><?php _e('Count in row', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][grid][count]" id="mec_skin_grid_count">
                        <option value="1" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 1) ? 'selected="selected"' : ''; ?>>1</option>
                        <option value="2" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 2) ? 'selected="selected"' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 3) ? 'selected="selected"' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 4) ? 'selected="selected"' : ''; ?>>4</option>
                        <option value="6" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 6) ? 'selected="selected"' : ''; ?>>6</option>
                        <option value="12" <?php echo (isset($sk_options_grid['count']) and $sk_options_grid['count'] == 12) ? 'selected="selected"' : ''; ?>>12</option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_grid_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][grid][limit]" id="mec_skin_grid_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_grid['limit'])) echo $sk_options_grid['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_grid_localtime">
					<div class="mec-col-4">
						<label for="mec_skin_grid_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][include_local_time]" id="mec_skin_grid_include_local_time" value="1" <?php if(isset($sk_options_grid['include_local_time']) and trim($sk_options_grid['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Include Events Times -->
                <div class="mec-form-row mec-switcher mec-include-events-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_grid_include_events_times"><?php _e('Include Events Times', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][include_events_times]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][include_events_times]" id="mec_skin_grid_include_events_times" value="1" <?php if(isset($sk_options_grid['include_events_times']) and trim($sk_options_grid['include_events_times'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_include_events_times"></label>
                    </div>
                </div>
                <!-- End Include Events Times -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_grid_load_more_button"><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][load_more_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][load_more_button]" id="mec_skin_grid_load_more_button" value="1" <?php if(!isset($sk_options_grid['load_more_button']) or (isset($sk_options_grid['load_more_button']) and $sk_options_grid['load_more_button'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_load_more_button"></label>
                    </div>
                </div>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_grid_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_grid_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][display_label]" id="mec_skin_grid_display_label" value="1" <?php if(isset($sk_options_grid['display_label']) and trim($sk_options_grid['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_grid_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_grid_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][reason_for_cancellation]" id="mec_skin_grid_reason_for_cancellation" value="1" <?php if(isset($sk_options_grid['reason_for_cancellation']) and trim($sk_options_grid['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_grid_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_grid_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][display_categories]" id="mec_skin_grid_display_categories" value="1" <?php if(isset($sk_options_grid['display_categories']) and trim($sk_options_grid['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('grid', (isset($sk_options_grid['display_organizer']) ? $sk_options_grid['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label for="mec_skin_grid_map_on_top"><?php _e('Show Map on top', 'modern-events-calendar-lite'); ?></label>
					</div>
                    <div class="mec-col-4">
                        <?php if(!$this->main->getPRO()): ?>
                        <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                        <?php else: ?>
                        <input type="hidden" name="mec[sk-options][grid][map_on_top]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][map_on_top]" id="mec_skin_grid_map_on_top" value="1" onchange="mec_skin_map_toggle(this);" <?php if(isset($sk_options_grid['map_on_top']) and $sk_options_grid['map_on_top']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_map_on_top"></label>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Start Set Map Geolocation -->
                <div class="mec-form-row mec-switcher mec-set-geolocation <?php if(!isset($sk_options_grid['map_on_top']) or (isset($sk_options_grid['map_on_top']) and !$sk_options_grid['map_on_top'])) echo 'mec-util-hidden'; ?>">
                    <div class="mec-col-4">
                        <label for="mec_skin_grid_set_geo_location"><?php _e('Geolocation', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][set_geolocation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][set_geolocation]" id="mec_skin_grid_set_geo_location" value="1" onchange="mec_skin_geolocation_toggle(this);"
                            <?php if(isset($sk_options_grid['set_geolocation']) and trim($sk_options_grid['set_geolocation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_set_geo_location"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher mec-set-geolocation-focus <?php if((!isset($sk_options_grid['set_geolocation']) or (isset($sk_options_grid['set_geolocation']) and !$sk_options_grid['set_geolocation'])) or (!isset($sk_options_grid['map_on_top']) or (isset($sk_options_grid['map_on_top']) and !$sk_options_grid['map_on_top']))) echo 'mec-util-hidden'; ?>">
                    <div class="mec-col-4">
                        <label for="mec_skin_grid_set_geo_location_focus"><?php _e('Disable Geolocation Force Focus', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][grid][set_geolocation_focus]" value="0" />
                        <input type="checkbox" name="mec[sk-options][grid][set_geolocation_focus]" id="mec_skin_grid_set_geo_location_focus" value="1"
                            <?php if(isset($sk_options_grid['set_geolocation_focus']) and trim($sk_options_grid['set_geolocation_focus'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_grid_set_geo_location_focus"></label>
                    </div>
                </div>
                <!-- End Set Map Geolocation -->
                <?php echo $this->booking_button_field('grid', (isset($sk_options_grid['booking_button']) ? $sk_options_grid['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('grid', (isset($sk_options_grid['custom_data']) ? $sk_options_grid['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('grid', (isset($sk_options_grid['sed_method']) ? $sk_options_grid['sed_method'] : 0), (isset($sk_options_grid['image_popup']) ? $sk_options_grid['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_grid_end', $sk_options_grid); ?>
            </div>

            <!-- Agenda View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_agenda_skin_options_container">

                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_agenda = isset($sk_options['agenda']) ? $sk_options['agenda'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_agenda_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][agenda][style]" id="mec_skin_agenda_style" onchange="mec_skin_style_changed('agenda', this.value);">
                        <option value="clean" <?php if(isset($sk_options_agenda['style']) and $sk_options_agenda['style'] == 'clean') echo 'selected="selected"'; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_agenda_fluent', $sk_options_agenda['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_agenda_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][agenda][start_date_type]" id="mec_skin_agenda_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_agenda_start_date_container').show(); else jQuery('#mec_skin_agenda_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_agenda['start_date_type']) or (isset($sk_options_agenda['start_date_type']) and $sk_options_agenda['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_agenda_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][agenda][start_date]" id="mec_skin_agenda_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_agenda['start_date'])) echo $sk_options_agenda['start_date']; ?>" />
                    </div>
                </div>
                <!-- Start Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_agenda_end_date_type"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][agenda][end_date_type]" id="mec_skin_agenda_end_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_agenda_end_date_container').show(); else jQuery('#mec_skin_agenda_end_date_container').hide();">
                        <option value="date" <?php if(isset($sk_options_agenda['end_date_type']) and $sk_options_agenda['end_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                        <option value="today" <?php if(isset($sk_options_agenda['end_date_type']) and $sk_options_agenda['end_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_agenda['end_date_type']) and $sk_options_agenda['end_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php echo (!isset($sk_options_agenda['end_date_type']) or (isset($sk_options_agenda['end_date_type']) and $sk_options_agenda['end_date_type'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_agenda_end_date_container">
                        <input type="text" class="mec_date_picker" name="mec[sk-options][agenda][maximum_date_range]" value="<?php echo isset($sk_options_agenda['maximum_date_range']) ? esc_attr($sk_options_agenda['maximum_date_range']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <!-- End Maximum Date -->
                <div class="mec-form-row mec-skin-agenda-date-format-container <?php if(isset($sk_options_agenda['style']) and $sk_options_agenda['style'] != 'clean') echo 'mec-util-hidden'; ?>" id="mec_skin_agenda_date_format_clean_container">
                    <label class="mec-col-4" for="mec_skin_agenda_clean_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][agenda][clean_date_format1]" id="mec_skin_agenda_clean_date_format1" value="<?php echo ((isset($sk_options_agenda['clean_date_format1']) and trim($sk_options_agenda['clean_date_format1']) != '') ? $sk_options_agenda['clean_date_format1'] : 'l'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][agenda][clean_date_format2]" id="mec_skin_agenda_clean_date_format2" value="<?php echo ((isset($sk_options_agenda['clean_date_format2']) and trim($sk_options_agenda['clean_date_format2']) != '') ? $sk_options_agenda['clean_date_format2'] : 'F j'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are l and F j', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/agenda-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_agenda_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][agenda][limit]" id="mec_skin_agenda_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_agenda['limit'])) echo $sk_options_agenda['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_agenda_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][agenda][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][agenda][include_local_time]" id="mec_skin_agenda_include_local_time" value="1" <?php if(isset($sk_options_agenda['include_local_time']) and trim($sk_options_agenda['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_agenda_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_agenda_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_agenda_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][agenda][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][agenda][display_label]" id="mec_skin_agenda_display_label" value="1" <?php if(isset($sk_options_agenda['display_label']) and trim($sk_options_agenda['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_agenda_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_agenda_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_agenda_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][agenda][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][agenda][reason_for_cancellation]" id="mec_skin_agenda_reason_for_cancellation" value="1" <?php if(isset($sk_options_agenda['reason_for_cancellation']) and trim($sk_options_agenda['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_agenda_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_agenda_load_more_button"><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][agenda][load_more_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][agenda][load_more_button]" id="mec_skin_agenda_load_more_button" value="1" <?php if(!isset($sk_options_agenda['load_more_button']) or (isset($sk_options_agenda['load_more_button']) and $sk_options_agenda['load_more_button'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_agenda_load_more_button"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_agenda_month_divider"><?php _e('Show Month Divider', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][agenda][month_divider]" value="0" />
                        <input type="checkbox" name="mec[sk-options][agenda][month_divider]" id="mec_skin_agenda_month_divider" value="1" <?php if(isset($sk_options_agenda['month_divider']) and $sk_options_agenda['month_divider']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_agenda_month_divider"></label>
                    </div>
                </div>
                <?php echo $this->booking_button_field('agenda', (isset($sk_options_agenda['booking_button']) ? $sk_options_agenda['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('agenda', (isset($sk_options_agenda['custom_data']) ? $sk_options_agenda['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('agenda', (isset($sk_options_agenda['sed_method']) ? $sk_options_agenda['sed_method'] : 0), (isset($sk_options_agenda['image_popup']) ? $sk_options_agenda['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_agenda_end', $sk_options_agenda); ?>
            </div>

            <!-- Full Calendar -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_full_calendar_skin_options_container">
                <?php $sk_options_full_calendar = isset($sk_options['full_calendar']) ? $sk_options['full_calendar'] : array(); ?>
                <?php do_action('mec_skin_options_full_calendar_init', $sk_options_full_calendar); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_full_calendar_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][full_calendar][start_date_type]" id="mec_skin_full_calendar_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_full_calendar_start_date_container').show(); else jQuery('#mec_skin_full_calendar_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_full_calendar['start_date_type']) or (isset($sk_options_full_calendar['start_date_type']) and $sk_options_full_calendar['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_full_calendar_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][full_calendar][start_date]" id="mec_skin_full_calendar_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_full_calendar['start_date'])) echo $sk_options_full_calendar['start_date']; ?>" />
                    </div>
				</div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_full_calendar_default_view"><?php _e('Default View', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][full_calendar][default_view]" id="mec_skin_full_calendar_default_view">
                        <option value="list" <?php echo (!isset($sk_options_full_calendar['list']) or (isset($sk_options_full_calendar['list']) and $sk_options_full_calendar['list'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'list') ? 'selected="selected"' : ''; ?>><?php _e('List View', 'modern-events-calendar-lite'); ?></option>
                        <option value="grid" <?php echo (!isset($sk_options_full_calendar['grid']) or (isset($sk_options_full_calendar['grid']) and $sk_options_full_calendar['grid'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'grid') ? 'selected="selected"' : ''; ?>><?php _e('Grid View', 'modern-events-calendar-lite'); ?></option>
                        <option value="tile" <?php echo (!isset($sk_options_full_calendar['tile']) or (isset($sk_options_full_calendar['tile']) and $sk_options_full_calendar['tile'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'tile') ? 'selected="selected"' : ''; ?>><?php _e('Tile View', 'modern-events-calendar-lite'); ?></option>
                        <option value="yearly" <?php echo (!isset($sk_options_full_calendar['yearly']) or (isset($sk_options_full_calendar['yearly']) and $sk_options_full_calendar['yearly'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'yearly') ? 'selected="selected"' : ''; ?>><?php _e('Yearly View', 'modern-events-calendar-lite'); ?></option>
                        <option value="monthly" <?php echo (!isset($sk_options_full_calendar['monthly']) or (isset($sk_options_full_calendar['monthly']) and $sk_options_full_calendar['monthly'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'monthly') ? 'selected="selected"' : ''; ?>><?php _e('Monthly/Calendar View', 'modern-events-calendar-lite'); ?></option>
                        <option value="weekly" <?php echo (!isset($sk_options_full_calendar['weekly']) or (isset($sk_options_full_calendar['weekly']) and $sk_options_full_calendar['weekly'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'weekly') ? 'selected="selected"' : ''; ?>><?php _e('Weekly View', 'modern-events-calendar-lite'); ?></option>
                        <option value="daily" <?php echo (!isset($sk_options_full_calendar['daily']) or (isset($sk_options_full_calendar['daily']) and $sk_options_full_calendar['daily'])) ? '' : 'disabled="disabled"'; ?> <?php echo (isset($sk_options_full_calendar['default_view']) and $sk_options_full_calendar['default_view'] == 'daily') ? 'selected="selected"' : ''; ?>><?php _e('Daily View', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row mec-not-full_calendar-fluent <?php echo (!isset($sk_options_full_calendar['monthly']) or (isset($sk_options_full_calendar['monthly']) and $sk_options_full_calendar['monthly'])) ? '' : 'mec-util-hidden'; ?>" id="mec_full_calendar_monthly_style">
                    <label class="mec-col-4" for="mec_skin_full_calendar_monthly_style"><?php _e('Monthly Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][full_calendar][monthly_style]" id="mec_skin_full_calendar_monthly_style">
                        <option value="clean" <?php echo (isset($sk_options_full_calendar['monthly_style']) and $sk_options_full_calendar['monthly_style'] == 'clean') ? 'selected="selected"' : ''; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <option value="novel" <?php echo (isset($sk_options_full_calendar['monthly_style']) and $sk_options_full_calendar['monthly_style'] == 'novel') ? 'selected="selected"' : ''; ?>><?php _e('Novel', 'modern-events-calendar-lite'); ?></option>
                        <option value="simple" <?php echo (isset($sk_options_full_calendar['monthly_style']) and $sk_options_full_calendar['monthly_style'] == 'simple') ? 'selected="selected"' : ''; ?>><?php _e('Simple', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_full_calendar_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][full_calendar][limit]" id="mec_skin_full_calendar_limit" placeholder="eg. 6" value="<?php if(isset($sk_options_full_calendar['limit'])) esc_attr_e($sk_options_full_calendar['limit'], 'modern-events-calendar-lite'); ?>">
                </div>
                <div class="mec-skin-full-calendar-list-wrap">
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label for="mec_skin_full_calendar_list"><?php _e('List View', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][full_calendar][list]" value="0" />
                            <input type="checkbox" name="mec[sk-options][full_calendar][list]" id="mec_skin_full_calendar_list" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(!isset($sk_options_full_calendar['list']) or (isset($sk_options_full_calendar['list']) and $sk_options_full_calendar['list'])) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_full_calendar_list"></label>
                        </div>
                    </div>
                    <?php
                        $date_format_list = 'd M';

                        if(isset($sk_options_full_calendar['date_format_list']) and trim($sk_options_full_calendar['date_format_list']) != '') $date_format_list = trim(trim($sk_options_full_calendar['date_format_list']));
                        elseif(isset($sk_options_list['standard_date_format1']) and trim($sk_options_list['standard_date_format1']) != '') $date_format_list = trim($sk_options_list['standard_date_format1']);
                    ?>
                    <div class="mec-form-row mec-date-format <?php echo (!isset($sk_options_full_calendar['list']) or (isset($sk_options_full_calendar['list']) and $sk_options_full_calendar['list'])) ? '' : 'mec-util-hidden'; ?>">
                        <div class="mec-form-row">
                            <label class="mec-col-4" for="mec_skin_full_calendar_date_format_list"><?php _e('List View Date Formats', 'modern-events-calendar-lite'); ?></label>
                            <input type="text" class="mec-col-4" name="mec[sk-options][full_calendar][date_format_list]" id="mec_skin_full_calendar_date_format_list" value="<?php esc_attr_e($date_format_list); ?>"/>
                        </div>
                        <div class="mec-form-row">
                            <label class="mec-col-4" for="mec_skin_full_calendar_end_date_type_list"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                            <select class="mec-col-4 wn-mec-select" name="mec[sk-options][full_calendar][end_date_type_list]" id="mec_skin_full_calendar_end_date_type_list" onchange="if(this.value === 'date') jQuery('#mec_skin_full_calendar_end_date_list_container').show(); else jQuery('#mec_skin_full_calendar_end_date_list_container').hide();">
                                <option value="date" <?php if(isset($sk_options_full_calendar['end_date_type_list']) and $sk_options_full_calendar['end_date_type_list'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                                <option value="today" <?php if(isset($sk_options_full_calendar['end_date_type_list']) and $sk_options_full_calendar['end_date_type_list'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                                <option value="tomorrow" <?php if(isset($sk_options_full_calendar['end_date_type_list']) and $sk_options_full_calendar['end_date_type_list'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                            </select>
                            <div class="mec-col-4 <?php echo (!isset($sk_options_full_calendar['end_date_type_list']) or (isset($sk_options_full_calendar['end_date_type_list']) and $sk_options_full_calendar['end_date_type_list'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_full_calendar_end_date_list_container">
                                <input type="text" class="mec_date_picker" name="mec[sk-options][full_calendar][maximum_date_range_list]" value="<?php echo isset($sk_options_full_calendar['maximum_date_range_list']) ? esc_attr($sk_options_full_calendar['maximum_date_range_list']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                                <span class="mec-tooltip">
                                    <div class="box top">
                                        <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mec-skin-full-calendar-grid-wrap">
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label for="mec_skin_full_calendar_grid"><?php _e('Grid View', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][full_calendar][grid]" value="0" />
                            <input type="checkbox" name="mec[sk-options][full_calendar][grid]" id="mec_skin_full_calendar_grid" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(isset($sk_options_full_calendar['grid']) and $sk_options_full_calendar['grid']) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_full_calendar_grid"></label>
                        </div>
                    </div>
                    <div class="mec-form-row mec-date-format <?php echo (!isset($sk_options_full_calendar['grid']) or (isset($sk_options_full_calendar['grid']) and $sk_options_full_calendar['grid'])) ? '' : 'mec-util-hidden'; ?>">
                        <div class="mec-form-row">
                            <label class="mec-col-4" for="mec_skin_full_calendar_end_date_type_grid"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                            <select class="mec-col-4 wn-mec-select" name="mec[sk-options][full_calendar][end_date_type_grid]" id="mec_skin_full_calendar_end_date_type_grid" onchange="if(this.value === 'date') jQuery('#mec_skin_full_calendar_end_date_grid_container').show(); else jQuery('#mec_skin_full_calendar_end_date_grid_container').hide();">
                                <option value="date" <?php if(isset($sk_options_full_calendar['end_date_type_grid']) and $sk_options_full_calendar['end_date_type_grid'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                                <option value="today" <?php if(isset($sk_options_full_calendar['end_date_type_grid']) and $sk_options_full_calendar['end_date_type_grid'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                                <option value="tomorrow" <?php if(isset($sk_options_full_calendar['end_date_type_grid']) and $sk_options_full_calendar['end_date_type_grid'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                            </select>
                            <div class="mec-col-4 <?php echo (!isset($sk_options_full_calendar['end_date_type_grid']) or (isset($sk_options_full_calendar['end_date_type_grid']) and $sk_options_full_calendar['end_date_type_grid'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_full_calendar_end_date_grid_container">
                                <input type="text" class="mec_date_picker" name="mec[sk-options][full_calendar][maximum_date_range_grid]" value="<?php echo isset($sk_options_full_calendar['maximum_date_range_grid']) ? esc_attr($sk_options_full_calendar['maximum_date_range_grid']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                                <span class="mec-tooltip">
                                    <div class="box top">
                                        <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_full_calendar_tile"><?php _e('Tile View', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][full_calendar][tile]" value="0" />
                        <input type="checkbox" name="mec[sk-options][full_calendar][tile]" id="mec_skin_full_calendar_tile" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(isset($sk_options_full_calendar['tile']) and $sk_options_full_calendar['tile']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_full_calendar_tile"></label>
                    </div>
                </div>
                <div class="mec-skin-full-calendar-yearly-wrap">
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label for="mec_skin_full_calendar_yearly"><?php _e('Yearly View', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][full_calendar][yearly]" value="0" />
                            <?php
                                if($this->main->getPRO())
                                {
                                    echo '<input type="checkbox" name="mec[sk-options][full_calendar][yearly]" id="mec_skin_full_calendar_yearly" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1"';
                                    if(isset($sk_options_full_calendar['yearly']) and $sk_options_full_calendar['yearly']) echo 'checked="checked"';
                                }
                                else
                                {
                                    echo '<input type="checkbox" name="mec[sk-options][full_calendar][yearly]" id="mec_skin_full_calendar_yearly" value="0"';
                                }
                            ?> />
                            <label for="mec_skin_full_calendar_yearly"></label>
                        </div>
                    </div>
                    <?php
                        $date_format_yearly_1 = 'l';
                        $date_format_yearly_2 = 'F j';
                        $sk_options_yearly_view = isset($sk_options['yearly_view']) ? $sk_options['yearly_view'] : array();

                        if(isset($sk_options_full_calendar['date_format_yearly_1']) and trim($sk_options_full_calendar['date_format_yearly_1']) != '') $date_format_yearly_1 = trim($sk_options_full_calendar['date_format_yearly_1']);
                        elseif(isset($sk_options_yearly_view['modern_date_format1']) and trim($sk_options_yearly_view['modern_date_format1']) != '') $date_format_yearly_1 = trim($sk_options_yearly_view['modern_date_format1']);

                        if(isset($sk_options_full_calendar['date_format_yearly_2']) and trim($sk_options_full_calendar['date_format_yearly_2']) != '') $date_format_yearly_2 = trim($sk_options_full_calendar['date_format_yearly_2']);
                        elseif(isset($sk_options_yearly_view['modern_date_format2']) and trim($sk_options_yearly_view['modern_date_format2']) != '') $date_format_yearly_2 = trim($sk_options_yearly_view['modern_date_format2']);
                    ?>
                    <div class="mec-form-row mec-date-format mec-not-full_calendar-fluent <?php echo (isset($sk_options_full_calendar['yearly']) and $sk_options_full_calendar['yearly']) ? '' : 'mec-util-hidden'; ?>">
                        <label class="mec-col-4" for="mec_skin_full_calendar_date_format_yearly_1"><?php _e('Yearly View Date Formats', 'modern-events-calendar-lite'); ?></label>
                        <input type="text" class="mec-col-2" name="mec[sk-options][full_calendar][date_format_yearly_1]" id="mec_skin_full_calendar_date_format_yearly_1" value="<?php esc_attr_e($date_format_yearly_1); ?>"/>
                        <input type="text" class="mec-col-2" name="mec[sk-options][full_calendar][date_format_yearly_2]" id="mec_skin_full_calendar_date_format_yearly_2" value="<?php esc_attr_e($date_format_yearly_2); ?>"/>
                    </div>
                </div>
                <div class="mec-form-row">
                <?php if(!$this->main->getPRO()): ?>
                    <div class="info-msg"><?php echo sprintf(__("%s is required to use <b>Yearly View</b> skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>
                </div>
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label for="mec_skin_full_calendar_monthly"><?php _e('Monthly/Calendar View', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][full_calendar][monthly]" value="0" />
						<input type="checkbox" name="mec[sk-options][full_calendar][monthly]" id="mec_skin_full_calendar_monthly" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(!isset($sk_options_full_calendar['monthly']) or (isset($sk_options_full_calendar['monthly']) and $sk_options_full_calendar['monthly'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_full_calendar_monthly"></label>
					</div>
                </div>
                <div id="mec_full_calendar_monthly_view_options" <?php echo (isset($sk_options_full_calendar['monthly']) and $sk_options_full_calendar['monthly']) ? '' : 'style="display:none;"'; ?>>
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label for="mec_skin_full_calendar_activate_first_date"><?php _e('Activate First upcoming Date with Event', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][full_calendar][activate_first_date]" value="0" />
                            <input type="checkbox" name="mec[sk-options][full_calendar][activate_first_date]" id="mec_skin_full_calendar_activate_first_date" value="1" <?php if(isset($sk_options_full_calendar['activate_first_date']) and trim($sk_options_full_calendar['activate_first_date'])) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_full_calendar_activate_first_date"></label>
                        </div>
                    </div>
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label for="mec_skin_full_calendar_activate_current_day"><?php _e('Activate Current Day in Next / Previous Months', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][full_calendar][activate_current_day]" value="0" />
                            <input type="checkbox" name="mec[sk-options][full_calendar][activate_current_day]" id="mec_skin_full_calendar_activate_current_day" value="1" <?php if(!isset($sk_options_full_calendar['activate_current_day']) or (isset($sk_options_full_calendar['activate_current_day']) and trim($sk_options_full_calendar['activate_current_day']))) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_full_calendar_activate_current_day"></label>
                        </div>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label for="mec_skin_full_calendar_weekly"><?php _e('Weekly View', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][full_calendar][weekly]" value="0" />
						<input type="checkbox" name="mec[sk-options][full_calendar][weekly]" id="mec_skin_full_calendar_weekly" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(!isset($sk_options_full_calendar['weekly']) or (isset($sk_options_full_calendar['weekly']) and $sk_options_full_calendar['weekly'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_full_calendar_weekly"></label>
					</div>
                </div>
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label for="mec_skin_full_calendar_daily"><?php _e('Daily View', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][full_calendar][daily]" value="0" />
						<input type="checkbox" name="mec[sk-options][full_calendar][daily]" id="mec_skin_full_calendar_daily" onchange="mec_skin_full_calendar_skin_toggled(this);" value="1" <?php if(!isset($sk_options_full_calendar['daily']) or (isset($sk_options_full_calendar['daily']) and $sk_options_full_calendar['daily'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_full_calendar_daily"></label>
					</div>
                </div>
                <p class="description"><?php _e("The price shows only in List View.", 'modern-events-calendar-lite'); ?></p>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_full_calendar_display_price"><?php _e('Display Event Price', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][full_calendar][display_price]" value="0" />
                        <input type="checkbox" name="mec[sk-options][full_calendar][display_price]" id="mec_skin_full_calendar_display_price" value="1" <?php if(isset($sk_options_full_calendar['display_price']) and $sk_options_full_calendar['display_price']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_full_calendar_display_price"></label>
                    </div>
                </div>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_full_calendar_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_full_calendar_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][full_calendar][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][full_calendar][display_label]" id="mec_skin_full_calendar_display_label" value="1" <?php if(isset($sk_options_full_calendar['display_label']) and trim($sk_options_full_calendar['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_full_calendar_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_full_calendar_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_full_calendar_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][full_calendar][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][full_calendar][reason_for_cancellation]" id="mec_skin_full_calendar_reason_for_cancellation" value="1" <?php if(isset($sk_options_full_calendar['reason_for_cancellation']) and trim($sk_options_full_calendar['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_full_calendar_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_full_calendar_localtime">
                    <div class="mec-col-4">
                        <label for="mec_skin_full_calendar_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][full_calendar][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][full_calendar][include_local_time]" id="mec_skin_full_calendar_include_local_time" value="1" <?php if(isset($sk_options_full_calendar['include_local_time']) and trim($sk_options_full_calendar['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_full_calendar_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <?php echo $this->booking_button_field('full_calendar', (isset($sk_options_full_calendar['booking_button']) ? $sk_options_full_calendar['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('full_calendar', (isset($sk_options_full_calendar['custom_data']) ? $sk_options_full_calendar['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('full_calendar', (isset($sk_options_full_calendar['sed_method']) ? $sk_options_full_calendar['sed_method'] : 0), (isset($sk_options_full_calendar['image_popup']) ? $sk_options_full_calendar['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_full_calendar_end', $sk_options_full_calendar); ?>
            </div>

            <!-- Yearly View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_yearly_view_skin_options_container">

                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_yearly_view = isset($sk_options['yearly_view']) ? $sk_options['yearly_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_yearly_view_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][yearly_view][style]" id="mec_skin_yearly_view_style" onchange="mec_skin_style_changed('yearly_view', this.value);">
                        <option value="modern" <?php if(isset($sk_options_yearly_view['style']) and $sk_options_yearly_view['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_yearly_fluent', $sk_options_yearly_view['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_yearly_view_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][yearly_view][start_date_type]" id="mec_skin_yearly_view_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_yearly_view_start_date_container').show(); else jQuery('#mec_skin_yearly_view_start_date_container').hide();">
                        <option value="start_current_year" <?php if(isset($sk_options_yearly_view['start_date_type']) and $sk_options_yearly_view['start_date_type'] == 'start_current_year') echo 'selected="selected"'; ?>><?php _e('Start of Current Year', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_year" <?php if(isset($sk_options_yearly_view['start_date_type']) and $sk_options_yearly_view['start_date_type'] == 'start_next_year') echo 'selected="selected"'; ?>><?php _e('Start of Next Year', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_year" <?php if(isset($sk_options_yearly_view['start_date_type']) and $sk_options_yearly_view['start_date_type'] == 'start_last_year') echo 'selected="selected"'; ?>><?php _e('Start of Last Year', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_yearly_view['start_date_type']) and $sk_options_yearly_view['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_yearly_view['start_date_type']) or (isset($sk_options_yearly_view['start_date_type']) and $sk_options_yearly_view['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_yearly_view_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][yearly_view][start_date]" id="mec_skin_yearly_view_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_yearly_view['start_date'])) echo $sk_options_yearly_view['start_date']; ?>" />
                    </div>
                </div>
                <div class="mec-form-row mec-skin-yearly-view-date-format-container mec-not-yearly_view-fluent <?php if(isset($sk_options_yearly_view['style']) and $sk_options_yearly_view['style'] != 'modern') echo 'mec-util-hidden'; ?>" id="mec_skin_yearly_view_date_format_modern_container">
                    <label class="mec-col-4" for="mec_skin_agenda_modern_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][yearly_view][modern_date_format1]" id="mec_skin_yearly_view_modern_date_format1" value="<?php echo ((isset($sk_options_yearly_view['modern_date_format1']) and trim($sk_options_yearly_view['modern_date_format1']) != '') ? $sk_options_yearly_view['modern_date_format1'] : 'l'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][yearly_view][modern_date_format2]" id="mec_skin_yearly_view_modern_date_format2" value="<?php echo ((isset($sk_options_yearly_view['modern_date_format2']) and trim($sk_options_yearly_view['modern_date_format2']) != '') ? $sk_options_yearly_view['modern_date_format2'] : 'F j'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are l and F j', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/yearly-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-not-yearly_view-fluent">
                    <label class="mec-col-4" for="mec_skin_yearly_view_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][yearly_view][limit]" id="mec_skin_yearly_view_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_yearly_view['limit'])) echo $sk_options_yearly_view['limit']; ?>" />
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_yearly_view_months_1"><?php _e('Months to Display', 'modern-events-calendar-lite'); ?></label>
                    <div class="mec-col-8" id="mec-all-month">
                        <?php foreach($this->main->get_months_labels() as $n => $month): ?>
                        <div style="margin-bottom: 5px;">
                            <input type="hidden" name="mec[sk-options][yearly_view][months][<?php echo $n; ?>]" value="0" />
                            <input type="checkbox" name="mec[sk-options][yearly_view][months][<?php echo $n; ?>]" id="mec_skin_yearly_view_months_<?php echo $n; ?>" value="1" <?php echo (!isset($sk_options_yearly_view['months']) or (isset($sk_options_yearly_view['months']) and isset($sk_options_yearly_view['months'][$n]) and $sk_options_yearly_view['months'][$n])) ? 'checked="checked"' : ''; ?> />
                            <label for="mec_skin_yearly_view_months_<?php echo $n; ?>"><?php echo $month; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_yearly_view_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][yearly_view][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][yearly_view][include_local_time]" id="mec_skin_yearly_view_include_local_time" value="1" <?php if(isset($sk_options_yearly_view['include_local_time']) and trim($sk_options_yearly_view['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_yearly_view_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][yearly_view][next_previous_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][yearly_view][next_previous_button]" id="mec_skin_yearly_view_next_previous_button" value="1" <?php if(!isset($sk_options_yearly_view['next_previous_button']) or (isset($sk_options_yearly_view['next_previous_button']) and $sk_options_yearly_view['next_previous_button'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_yearly_view_next_previous_button"></label>
                    </div>
                </div>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_yearly_view_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_yearly_view_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][yearly_view][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][yearly_view][display_label]" id="mec_skin_yearly_view_display_label" value="1" <?php if(isset($sk_options_yearly_view['display_label']) and trim($sk_options_yearly_view['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_yearly_view_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_yearly_view_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_yearly_view_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][yearly_view][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][yearly_view][reason_for_cancellation]" id="mec_skin_yearly_view_reason_for_cancellation" value="1" <?php if(isset($sk_options_yearly_view['reason_for_cancellation']) and trim($sk_options_yearly_view['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_yearly_view_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <p class="description"><?php _e('For showing next/previous year navigation.', 'modern-events-calendar-lite'); ?></p>
                <?php echo $this->booking_button_field('yearly_view', (isset($sk_options_yearly_view['booking_button']) ? $sk_options_yearly_view['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('yearly_view', (isset($sk_options_yearly_view['custom_data']) ? $sk_options_yearly_view['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('yearly_view', (isset($sk_options_yearly_view['sed_method']) ? $sk_options_yearly_view['sed_method'] : 0), (isset($sk_options_yearly_view['image_popup']) ? $sk_options_yearly_view['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_yearly_view_end', $sk_options_yearly_view); ?>
            </div>

            <!-- Monthly View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_monthly_view_skin_options_container">
                <?php $sk_options_monthly_view = isset($sk_options['monthly_view']) ? $sk_options['monthly_view'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_monthly_view_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][monthly_view][style]" id="mec_skin_monthly_view_style" onchange="mec_skin_style_changed('monthly_view', this.value);">
                        <option value="classic" <?php if(isset($sk_options_monthly_view['style']) and $sk_options_monthly_view['style'] == 'classic') echo 'selected="selected"'; ?>><?php _e('Classic', 'modern-events-calendar-lite'); ?></option>
                        <option value="clean" <?php if(isset($sk_options_monthly_view['style']) and $sk_options_monthly_view['style'] == 'clean') echo 'selected="selected"'; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <option value="modern" <?php if(isset($sk_options_monthly_view['style']) and $sk_options_monthly_view['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <option value="novel" <?php if(isset($sk_options_monthly_view['style']) and $sk_options_monthly_view['style'] == 'novel') echo 'selected="selected"'; ?>><?php _e('Novel', 'modern-events-calendar-lite'); ?></option>
                        <option value="simple" <?php if(isset($sk_options_monthly_view['style']) and $sk_options_monthly_view['style'] == 'simple') echo 'selected="selected"'; ?>><?php _e('Simple', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_monthly_fluent', $sk_options_monthly_view['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_monthly_view_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][monthly_view][start_date_type]" id="mec_skin_monthly_view_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_monthly_view_start_date_container').show(); else jQuery('#mec_skin_monthly_view_start_date_container').hide();">
                        <option value="start_current_month" <?php if(isset($sk_options_monthly_view['start_date_type']) and $sk_options_monthly_view['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_monthly_view['start_date_type']) and $sk_options_monthly_view['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_monthly_view['start_date_type']) and $sk_options_monthly_view['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_monthly_view['start_date_type']) and $sk_options_monthly_view['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_monthly_view['start_date_type']) or (isset($sk_options_monthly_view['start_date_type']) and $sk_options_monthly_view['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_monthly_view_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][monthly_view][start_date]" id="mec_skin_monthly_view_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_monthly_view['start_date'])) echo $sk_options_monthly_view['start_date']; ?>" />
                    </div>
				</div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_monthly_view_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][monthly_view][limit]" id="mec_skin_monthly_view_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_monthly_view['limit'])) echo $sk_options_monthly_view['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-monthly_view-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_monthly_view_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][include_local_time]" id="mec_skin_monthly_view_include_local_time" value="1" <?php if(isset($sk_options_monthly_view['include_local_time']) and trim($sk_options_monthly_view['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][monthly_view][next_previous_button]" value="0" />
						<input type="checkbox" name="mec[sk-options][monthly_view][next_previous_button]" id="mec_skin_monthly_view_next_previous_button" value="1" <?php if(!isset($sk_options_monthly_view['next_previous_button']) or (isset($sk_options_monthly_view['next_previous_button']) and $sk_options_monthly_view['next_previous_button'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_monthly_view_next_previous_button"></label>
					</div>
                </div>
                <p class="description"><?php _e('For showing next/previous month navigation.', 'modern-events-calendar-lite'); ?></p>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-monthly_view-fluent" id="mec_skin_monthly_view_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_monthly_view_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][display_label]" id="mec_skin_monthly_view_display_label" value="1" <?php if(isset($sk_options_monthly_view['display_label']) and trim($sk_options_monthly_view['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher mec-not-monthly_view-fluent" id="mec_skin_monthly_view_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_monthly_view_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][reason_for_cancellation]" id="mec_skin_monthly_view_reason_for_cancellation" value="1" <?php if(isset($sk_options_monthly_view['reason_for_cancellation']) and trim($sk_options_monthly_view['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <div class="mec-form-row mec-switcher mec-not-monthly_view-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_monthly_view_activate_first_date"><?php _e('Activate First upcoming Date with Event', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][activate_first_date]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][activate_first_date]" id="mec_skin_monthly_view_activate_first_date" value="1" <?php if(isset($sk_options_monthly_view['activate_first_date']) and trim($sk_options_monthly_view['activate_first_date'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_activate_first_date"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher mec-not-monthly_view-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_monthly_view_activate_current_day"><?php _e('Activate Current Day in Next / Previous Months', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][activate_current_day]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][activate_current_day]" id="mec_skin_monthly_view_activate_current_day" value="1" <?php if(!isset($sk_options_monthly_view['activate_current_day']) or (isset($sk_options_monthly_view['activate_current_day']) and trim($sk_options_monthly_view['activate_current_day']))) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_activate_current_day"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher mec-not-monthly_view-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_monthly_view_display_all"><?php _e('Display all events in right section', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][monthly_view][display_all]" value="0" />
                        <input type="checkbox" name="mec[sk-options][monthly_view][display_all]" id="mec_skin_monthly_view_display_all" value="1" <?php if(isset($sk_options_monthly_view['display_all']) and trim($sk_options_monthly_view['display_all'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_monthly_view_display_all"></label>
                    </div>
                </div>
                <!-- <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label><?php _e('Uppercase Text', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][monthly_view][uppercase_text]" value="0" />
						<input type="checkbox" name="mec[sk-options][monthly_view][uppercase_text]" id="mec_skin_monthly_view_uppercase_text" value="1" <?php if(!isset($sk_options_monthly_view['uppercase_text']) or (isset($sk_options_monthly_view['uppercase_text']) and $sk_options_monthly_view['uppercase_text'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_monthly_view_uppercase_text"></label>
					</div>
				</div> -->
                <?php echo $this->booking_button_field('monthly_view', (isset($sk_options_monthly_view['booking_button']) ? $sk_options_monthly_view['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('monthly_view', (isset($sk_options_monthly_view['custom_data']) ? $sk_options_monthly_view['custom_data'] : 0)); ?>
                <?php echo $this->display_detailed_time_field('monthly_view', (isset($sk_options_monthly_view['detailed_time']) ? $sk_options_monthly_view['detailed_time'] : 0)); ?>
                <?php echo $this->sed_method_field('monthly_view', (isset($sk_options_monthly_view['sed_method']) ? $sk_options_monthly_view['sed_method'] : 0), (isset($sk_options_monthly_view['image_popup']) ? $sk_options_monthly_view['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_monthly_view_end', $sk_options_monthly_view); ?>
            </div>

            <!-- Map Skin -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_map_skin_options_container">

                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_map = isset($sk_options['map']) ? $sk_options['map'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_map_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][map][start_date_type]" id="mec_skin_map_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_map_start_date_container').show(); else jQuery('#mec_skin_map_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_map['start_date_type']) or (isset($sk_options_map['start_date_type']) and $sk_options_map['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_map_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][map][start_date]" id="mec_skin_map_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_map['start_date'])) echo $sk_options_map['start_date']; ?>" />
                    </div>
				</div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_map_limit"><?php _e('Maximum events', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][map][limit]" id="mec_skin_map_limit" placeholder="<?php _e('eg. 200', 'modern-events-calendar-lite'); ?>" value="<?php echo (isset($sk_options_map['limit']) ? $sk_options_map['limit'] : 200); ?>" />
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Geolocation', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][map][geolocation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][map][geolocation]" id="mec_skin_map_geolocation" value="1" onchange="mec_skin_geolocation_toggle(this);" <?php if(isset($sk_options_map['geolocation']) and $sk_options_map['geolocation']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_map_geolocation"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher mec-set-geolocation-focus <?php if(!isset($sk_options_map['geolocation']) or (isset($sk_options_map['geolocation']) and !$sk_options_map['geolocation'])) echo 'mec-util-hidden'; ?>">
                    <div class="mec-col-4">
                        <label><?php _e('Disable Geolocation Force Focus', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][map][geolocation_focus]" value="0" />
                        <input type="checkbox" name="mec[sk-options][map][geolocation_focus]" id="mec_skin_map_geolocation_focus" value="1" <?php if(isset($sk_options_map['geolocation_focus']) and $sk_options_map['geolocation_focus']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_map_geolocation_focus"></label>
                    </div>
                </div>
                <p class="description"><?php _e('The geolocation feature works only in secure (https) websites.', 'modern-events-calendar-lite'); ?></p>
                <?php do_action('mec_location_shortcode_filter', $post); ?>
            </div>

            <!-- Daily View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_daily_view_skin_options_container">
                <?php $sk_options_daily_view = isset($sk_options['daily_view']) ? $sk_options['daily_view'] : array(); ?>
                <?php do_action('mec_skin_options_daily_init', $sk_options_daily_view); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_daily_view_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][daily_view][start_date_type]" id="mec_skin_daily_view_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_daily_view_start_date_container').show(); else jQuery('#mec_skin_daily_view_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>

                    <div class="mec-col-4 <?php if(!isset($sk_options_daily_view['start_date_type']) or (isset($sk_options_daily_view['start_date_type']) and $sk_options_daily_view['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_daily_view_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][daily_view][start_date]" id="mec_skin_daily_view_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_daily_view['start_date'])) echo $sk_options_daily_view['start_date']; ?>" />
                    </div>
				</div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_daily_view_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][daily_view][limit]" id="mec_skin_daily_view_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_daily_view['limit'])) echo $sk_options_daily_view['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_daily_view_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][daily_view][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][daily_view][include_local_time]" id="mec_skin_daily_view_include_local_time" value="1" <?php if(isset($sk_options_daily_view['include_local_time']) and trim($sk_options_daily_view['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_daily_view_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][daily_view][next_previous_button]" value="0" />
						<input type="checkbox" name="mec[sk-options][daily_view][next_previous_button]" id="mec_skin_daily_view_next_previous_button" value="1" <?php if(!isset($sk_options_daily_view['next_previous_button']) or (isset($sk_options_daily_view['next_previous_button']) and $sk_options_daily_view['next_previous_button'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_daily_view_next_previous_button"></label>
					</div>
                </div>
                <p class="description"><?php _e('For showing next/previous month navigation.', 'modern-events-calendar-lite'); ?></p>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_daily_view_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_daily_view_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][daily_view][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][daily_view][display_label]" id="mec_skin_daily_view_display_label" value="1" <?php if(isset($sk_options_daily_view['display_label']) and trim($sk_options_daily_view['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_daily_view_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_daily_view_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_daily_view_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][daily_view][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][daily_view][reason_for_cancellation]" id="mec_skin_daily_view_reason_for_cancellation" value="1" <?php if(isset($sk_options_daily_view['reason_for_cancellation']) and trim($sk_options_daily_view['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_daily_view_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_daily_view_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_daily_view_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][daily_view][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][daily_view][display_categories]" id="mec_skin_daily_view_display_categories" value="1" <?php if(isset($sk_options_daily_view['display_categories']) and trim($sk_options_daily_view['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_daily_view_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('daily_view', (isset($sk_options_daily_view['display_organizer']) ? $sk_options_daily_view['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <?php echo $this->booking_button_field('daily_view', (isset($sk_options_daily_view['booking_button']) ? $sk_options_daily_view['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('daily_view', (isset($sk_options_daily_view['custom_data']) ? $sk_options_daily_view['custom_data'] : 0)); ?>
                <?php echo $this->display_detailed_time_field('daily_view', (isset($sk_options_daily_view['detailed_time']) ? $sk_options_daily_view['detailed_time'] : 0)); ?>
                <?php echo $this->sed_method_field('daily_view', (isset($sk_options_daily_view['sed_method']) ? $sk_options_daily_view['sed_method'] : 0), (isset($sk_options_daily_view['image_popup']) ? $sk_options_daily_view['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_daily_view_end', $sk_options_daily_view); ?>
            </div>

            <!-- Weekly View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_weekly_view_skin_options_container">
                <?php $sk_options_weekly_view = isset($sk_options['weekly_view']) ? $sk_options['weekly_view'] : array(); ?>
                <?php do_action('mec_skin_options_weekly_init', $sk_options_weekly_view); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_weekly_view_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][weekly_view][start_date_type]" id="mec_skin_weekly_view_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_weekly_view_start_date_container').show(); else jQuery('#mec_skin_weekly_view_start_date_container').hide();">
                        <option value="start_current_week" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_current_week') echo 'selected="selected"'; ?>><?php _e('Current Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_week" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_next_week') echo 'selected="selected"'; ?>><?php _e('Next Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_week" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_last_week') echo 'selected="selected"'; ?>><?php _e('Last Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_weekly_view['start_date_type']) or (isset($sk_options_weekly_view['start_date_type']) and $sk_options_weekly_view['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_weekly_view_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][weekly_view][start_date]" id="mec_skin_weekly_view_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_weekly_view['start_date'])) echo $sk_options_weekly_view['start_date']; ?>" />
                    </div>
				</div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_weekly_view_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][weekly_view][limit]" id="mec_skin_weekly_view_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_weekly_view['limit'])) echo $sk_options_weekly_view['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-weekly_view-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_weekly_view_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][weekly_view][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][weekly_view][include_local_time]" id="mec_skin_weekly_view_include_local_time" value="1" <?php if(isset($sk_options_weekly_view['include_local_time']) and trim($sk_options_weekly_view['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_weekly_view_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-weekly_view-fluent" id="mec_skin_weekly_view_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_weekly_view_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][weekly_view][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][weekly_view][display_label]" id="mec_skin_weekly_view_display_label" value="1" <?php if(isset($sk_options_weekly_view['display_label']) and trim($sk_options_weekly_view['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_weekly_view_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher mec-not-weekly_view-fluent" id="mec_skin_weekly_view_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_weekly_view_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][weekly_view][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][weekly_view][reason_for_cancellation]" id="mec_skin_weekly_view_reason_for_cancellation" value="1" <?php if(isset($sk_options_weekly_view['reason_for_cancellation']) and trim($sk_options_weekly_view['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_weekly_view_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_weekly_view_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_weekly_view_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][weekly_view][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][weekly_view][display_categories]" id="mec_skin_weekly_view_display_categories" value="1" <?php if(isset($sk_options_weekly_view['display_categories']) and trim($sk_options_weekly_view['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_weekly_view_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('weekly_view', (isset($sk_options_weekly_view['display_organizer']) ? $sk_options_weekly_view['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
					<div class="mec-col-4">
						<label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
						<input type="hidden" name="mec[sk-options][weekly_view][next_previous_button]" value="0" />
						<input type="checkbox" name="mec[sk-options][weekly_view][next_previous_button]" id="mec_skin_weekly_view_next_previous_button" value="1" <?php if(!isset($sk_options_weekly_view['next_previous_button']) or (isset($sk_options_weekly_view['next_previous_button']) and $sk_options_weekly_view['next_previous_button'])) echo 'checked="checked"'; ?> />
						<label for="mec_skin_weekly_view_next_previous_button"></label>
					</div>
                </div>
                <p class="description"><?php _e('For showing next/previous month navigation.', 'modern-events-calendar-lite'); ?></p>
                <?php echo $this->booking_button_field('weekly_view', (isset($sk_options_weekly_view['booking_button']) ? $sk_options_weekly_view['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('weekly_view', (isset($sk_options_weekly_view['custom_data']) ? $sk_options_weekly_view['custom_data'] : 0)); ?>
                <?php echo $this->display_detailed_time_field('weekly_view', (isset($sk_options_weekly_view['detailed_time']) ? $sk_options_weekly_view['detailed_time'] : 0)); ?>
                <?php echo $this->sed_method_field('weekly_view', (isset($sk_options_weekly_view['sed_method']) ? $sk_options_weekly_view['sed_method'] : 0), (isset($sk_options_weekly_view['image_popup']) ? $sk_options_weekly_view['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_weekly_view_end', $sk_options_weekly_view); ?>
            </div>

            <!-- Timetable View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_timetable_skin_options_container">

                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_timetable = isset($sk_options['timetable']) ? $sk_options['timetable'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timetable_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timetable][style]" id="mec_skin_timetable_style" onchange="mec_skin_style_changed('timetable', this.value); if(this.value == 'clean' || this.value == 'fluent'){ jQuery('.mec-timetable-clean-style-depended').show(); jQuery('.mec-timetable-classic-style-depended').hide(); jQuery('.mec-timetable-modern-style-depended').hide(); } else if ( this.value == 'classic' ) { jQuery('.mec-timetable-classic-style-depended').show(); jQuery('.mec-timetable-clean-style-depended').show(); jQuery('.mec-timetable-modern-style-depended').hide(); } else { jQuery('.mec-timetable-clean-style-depended').hide(); jQuery('.mec-timetable-classic-style-depended').hide(); jQuery('.mec-timetable-modern-style-depended').show(); }">
                        <option value="modern" <?php if(isset($sk_options_timetable['style']) and $sk_options_timetable['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <option value="clean" <?php if(isset($sk_options_timetable['style']) and $sk_options_timetable['style'] == 'clean') echo 'selected="selected"'; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <!-- <option value="classic" <?php if(isset($sk_options_timetable['style']) and $sk_options_timetable['style'] == 'classic') echo 'selected="selected"'; ?>><?php _e('Classic', 'modern-events-calendar-lite'); ?></option> -->
                        <?php do_action('mec_timetable_fluent', $sk_options_timetable['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timetable_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timetable][start_date_type]" id="mec_skin_timetable_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_timetable_start_date_container').show(); else jQuery('#mec_skin_timetable_start_date_container').hide();">
                        <option value="start_current_week" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_current_week') echo 'selected="selected"'; ?>><?php _e('Current Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_week" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_next_week') echo 'selected="selected"'; ?>><?php _e('Next Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_week" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_last_week') echo 'selected="selected"'; ?>><?php _e('Last Week', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_timetable['start_date_type']) or (isset($sk_options_timetable['start_date_type']) and $sk_options_timetable['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_timetable_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][timetable][start_date]" id="mec_skin_timetable_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_timetable['start_date'])) echo $sk_options_timetable['start_date']; ?>" />
                    </div>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timetable_limit"><?php _e('Events per day', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][timetable][limit]" id="mec_skin_timetable_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_timetable['limit'])) echo $sk_options_timetable['limit']; ?>" />
                </div>
                <div class="mec-timetable-modern-style-depended">
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_skin_timetable_number_of_days_modern"><?php _e('Number of Days Per Week', 'modern-events-calendar-lite'); ?></label>
                        <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timetable][number_of_days_modern]" id="mec_skin_timetable_number_of_days_modern">
                            <option value="7" <?php if(isset($sk_options_timetable['number_of_days_modern']) and $sk_options_timetable['number_of_days_modern'] == '7') echo 'selected="selected"'; ?>>7</option>
                            <option value="6" <?php if(isset($sk_options_timetable['number_of_days_modern']) and $sk_options_timetable['number_of_days_modern'] == '6') echo 'selected="selected"'; ?>>6</option>
                            <option value="5" <?php if(isset($sk_options_timetable['number_of_days_modern']) and $sk_options_timetable['number_of_days_modern'] == '5') echo 'selected="selected"'; ?>>5</option>
                        </select>
                    </div>
                </div>
                <div class="mec-timetable-clean-style-depended mec-timetable-fluent">
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_skin_timetable_number_of_days"><?php _e('Number of Days Per Week', 'modern-events-calendar-lite'); ?></label>
                        <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timetable][number_of_days]" id="mec_skin_timetable_number_of_days">
                            <option value="5" <?php if(isset($sk_options_timetable['number_of_days']) and $sk_options_timetable['number_of_days'] == '5') echo 'selected="selected"'; ?>>5</option>
                            <option value="6" <?php if(isset($sk_options_timetable['number_of_days']) and $sk_options_timetable['number_of_days'] == '6') echo 'selected="selected"'; ?>>6</option>
                            <option value="7" <?php if(isset($sk_options_timetable['number_of_days']) and $sk_options_timetable['number_of_days'] == '7') echo 'selected="selected"'; ?>>7</option>
                        </select>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_skin_timetable_week_start"><?php _e('Week Start', 'modern-events-calendar-lite'); ?></label>
                        <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timetable][week_start]" id="mec_skin_timetable_week_start">
                            <option value="-1" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '-1') echo 'selected="selected"'; ?>><?php _e('Inherite from WordPress options', 'modern-events-calendar-lite'); ?></option>
                            <option value="0" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '0') echo 'selected="selected"'; ?>><?php _e('Sunday', 'modern-events-calendar-lite'); ?></option>
                            <option value="1" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '1') echo 'selected="selected"'; ?>><?php _e('Monday', 'modern-events-calendar-lite'); ?></option>
                            <option value="2" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '2') echo 'selected="selected"'; ?>><?php _e('Tuesday', 'modern-events-calendar-lite'); ?></option>
                            <option value="3" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '3') echo 'selected="selected"'; ?>><?php _e('Wednesday', 'modern-events-calendar-lite'); ?></option>
                            <option value="4" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '4') echo 'selected="selected"'; ?>><?php _e('Thursday', 'modern-events-calendar-lite'); ?></option>
                            <option value="5" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '5') echo 'selected="selected"'; ?>><?php _e('Friday', 'modern-events-calendar-lite'); ?></option>
                            <option value="6" <?php if(isset($sk_options_timetable['week_start']) and $sk_options_timetable['week_start'] == '6') echo 'selected="selected"'; ?>><?php _e('Saturday', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="mec-timetable-classic-style-depended">
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_skin_grid_clean_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                        <select class="mec-col-2 wn-mec-select" name="mec[sk-options][timetable][start_time]" id="mec_skin_timetable_start_time">
                            <option value="1" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '1') echo 'selected="selected"'; ?>><?php _e('1:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="2" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '2') echo 'selected="selected"'; ?>><?php _e('2:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="3" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '3') echo 'selected="selected"'; ?>><?php _e('3:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="4" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '4') echo 'selected="selected"'; ?>><?php _e('4:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="5" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '5') echo 'selected="selected"'; ?>><?php _e('5:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="6" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '6') echo 'selected="selected"'; ?>><?php _e('6:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="7" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '7') echo 'selected="selected"'; ?>><?php _e('7:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="8" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '8') echo 'selected="selected"'; ?>><?php _e('8:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="9" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '9') echo 'selected="selected"'; ?>><?php _e('9:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="10" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '10') echo 'selected="selected"'; ?>><?php _e('10:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="11" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '11') echo 'selected="selected"'; ?>><?php _e('11:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="12" <?php if(isset($sk_options_timetable['start_time']) and $sk_options_timetable['start_time'] == '12') echo 'selected="selected"'; ?>><?php _e('12:00', 'modern-events-calendar-lite'); ?></option>
                        </select>
                        <select class="mec-col-2 wn-mec-select" name="mec[sk-options][timetable][end_time]" id="mec_skin_timetable_end_time">
                            <option value="13" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '13') echo 'selected="selected"'; ?>><?php _e('13:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="14" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '14') echo 'selected="selected"'; ?>><?php _e('14:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="15" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '15') echo 'selected="selected"'; ?>><?php _e('15:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="16" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '16') echo 'selected="selected"'; ?>><?php _e('16:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="17" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '17') echo 'selected="selected"'; ?>><?php _e('17:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="18" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '18') echo 'selected="selected"'; ?>><?php _e('18:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="19" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '19') echo 'selected="selected"'; ?>><?php _e('19:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="20" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '20') echo 'selected="selected"'; ?>><?php _e('20:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="21" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '21') echo 'selected="selected"'; ?>><?php _e('21:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="22" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '22') echo 'selected="selected"'; ?>><?php _e('22:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="23" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '23') echo 'selected="selected"'; ?>><?php _e('23:00', 'modern-events-calendar-lite'); ?></option>
                            <option value="24" <?php if(isset($sk_options_timetable['end_time']) and $sk_options_timetable['end_time'] == '24') echo 'selected="selected"'; ?>><?php _e('24:00', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-timetable-fluent">
                    <div class="mec-col-4">
                        <label for="mec_skin_timetable_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timetable][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timetable][include_local_time]" id="mec_skin_timetable_include_local_time" value="1" <?php if(isset($sk_options_timetable['include_local_time']) and trim($sk_options_timetable['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timetable_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times mec-not-timetable-fluent" id="mec_skin_timetable_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_timetable_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timetable][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timetable][display_label]" id="mec_skin_timetable_display_label" value="1" <?php if(isset($sk_options_timetable['display_label']) and trim($sk_options_timetable['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timetable_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher mec-not-timetable-fluent" id="mec_skin_timetable_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_timetable_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timetable][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timetable][reason_for_cancellation]" id="mec_skin_timetable_reason_for_cancellation" value="1" <?php if(isset($sk_options_timetable['reason_for_cancellation']) and trim($sk_options_timetable['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timetable_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <div class="mec-timetable-modern-style-depended">
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][timetable][next_previous_button]" value="0" />
                            <input type="checkbox" name="mec[sk-options][timetable][next_previous_button]" id="mec_skin_timetable_next_previous_button" value="1" <?php if(!isset($sk_options_timetable['next_previous_button']) or (isset($sk_options_timetable['next_previous_button']) and $sk_options_timetable['next_previous_button'])) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_timetable_next_previous_button"></label>
                        </div>
                    </div>
                    <p class="description"><?php _e('For showing next/previous month navigation.', 'modern-events-calendar-lite'); ?></p>
                </div>
                <div class="mec-timetable-sed-methode-container">
                    <?php echo $this->booking_button_field('timetable', (isset($sk_options_timetable['booking_button']) ? $sk_options_timetable['booking_button'] : 0)); ?>
                    <?php echo $this->display_custom_data_field('timetable', (isset($sk_options_timetable['custom_data']) ? $sk_options_timetable['custom_data'] : 0)); ?>
                    <?php echo $this->sed_method_field('timetable', (isset($sk_options_timetable['sed_method']) ? $sk_options_timetable['sed_method'] : 0), (isset($sk_options_timetable['image_popup']) ? $sk_options_timetable['image_popup'] : 0)); ?>
                </div>
                <?php do_action('mec_skin_options_timetable_end', $sk_options_timetable); ?>
            </div>

            <!-- Masonry View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_masonry_skin_options_container">
                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_masonry = isset($sk_options['masonry']) ? $sk_options['masonry'] : array(); ?>
                <?php do_action('mec_skin_options_masonry_init', $sk_options_masonry); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_masonry_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][masonry][start_date_type]" id="mec_skin_masonry_start_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_masonry_start_date_container').show(); else jQuery('#mec_skin_masonry_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_masonry['start_date_type']) or (isset($sk_options_masonry['start_date_type']) and $sk_options_masonry['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_masonry_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][masonry][start_date]" id="mec_skin_masonry_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_masonry['start_date'])) echo $sk_options_masonry['start_date']; ?>" />
                    </div>
                </div>
                <!-- Start Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_masonry_end_date_type"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][masonry][end_date_type]" id="mec_skin_masonry_end_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_masonry_end_date_container').show(); else jQuery('#mec_skin_masonry_end_date_container').hide();">
                        <option value="date" <?php if(isset($sk_options_masonry['end_date_type']) and $sk_options_masonry['end_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                        <option value="today" <?php if(isset($sk_options_masonry['end_date_type']) and $sk_options_masonry['end_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_masonry['end_date_type']) and $sk_options_masonry['end_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php echo (!isset($sk_options_masonry['end_date_type']) or (isset($sk_options_masonry['end_date_type']) and $sk_options_masonry['end_date_type'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_masonry_end_date_container">
                        <input type="text" class="mec_date_picker" name="mec[sk-options][masonry][maximum_date_range]" value="<?php echo isset($sk_options_masonry['maximum_date_range']) ? esc_attr($sk_options_masonry['maximum_date_range']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <!-- End Maximum Date -->
                <div class="mec-form-row mec-skin-masonry-date-format-container">
                    <label class="mec-col-4" for="mec_skin_masonry_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][masonry][date_format1]" id="mec_skin_masonry_date_format1" value="<?php echo ((isset($sk_options_masonry['date_format1']) and trim($sk_options_masonry['date_format1']) != '') ? $sk_options_masonry['date_format1'] : 'j'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][masonry][date_format2]" id="mec_skin_masonry_date_format2" value="<?php echo ((isset($sk_options_masonry['date_format2']) and trim($sk_options_masonry['date_format2']) != '') ? $sk_options_masonry['date_format2'] : 'F'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are j and F', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/masonry-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_masonry_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][masonry][limit]" id="mec_skin_masonry_limit" placeholder="<?php _e('eg. 24', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_masonry['limit'])) echo $sk_options_masonry['limit']; ?>" />
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_masonry_filter_by"><?php _e('Filter By', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][masonry][filter_by]" id="mec_skin_masonry_filter_by">
                        <option value="" <?php if(isset($sk_options_masonry['filter_by']) and $sk_options_masonry['filter_by'] == '') echo 'selected="selected"'; ?>><?php _e('None', 'modern-events-calendar-lite'); ?></option>
                        <option value="category" <?php if(isset($sk_options_masonry['filter_by']) and $sk_options_masonry['filter_by'] == 'category') echo 'selected="selected"'; ?>><?php _e('Category', 'modern-events-calendar-lite'); ?></option>
                        <option value="label" <?php if(isset($sk_options_masonry['filter_by']) and $sk_options_masonry['filter_by'] == 'label') echo 'selected="selected"'; ?>><?php _e('Label', 'modern-events-calendar-lite'); ?></option>
                        <option value="location" <?php if(isset($sk_options_masonry['filter_by']) and $sk_options_masonry['filter_by'] == 'location') echo 'selected="selected"'; ?>><?php _e('Location', 'modern-events-calendar-lite'); ?></option>
                        <option value="organizer" <?php if(isset($sk_options_masonry['filter_by']) and $sk_options_masonry['filter_by'] == 'organizer') echo 'selected="selected"'; ?>><?php _e('Organizer', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_masonry_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][include_local_time]" id="mec_skin_masonry_include_local_time" value="1" <?php if(isset($sk_options_masonry['include_local_time']) and trim($sk_options_masonry['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_masonry_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_masonry_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][display_label]" id="mec_skin_masonry_display_label" value="1" <?php if(isset($sk_options_masonry['display_label']) and trim($sk_options_masonry['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_masonry_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_masonry_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][reason_for_cancellation]" id="mec_skin_masonry_reason_for_cancellation" value="1" <?php if(isset($sk_options_masonry['reason_for_cancellation']) and trim($sk_options_masonry['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_masonry_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_masonry_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][display_categories]" id="mec_skin_masonry_display_categories" value="1" <?php if(isset($sk_options_masonry['display_categories']) and trim($sk_options_masonry['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('masonry', (isset($sk_options_masonry['display_organizer']) ? $sk_options_masonry['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Fit to row', 'modern-events-calendar-lite'); ?></label>
                        <p class="description"><?php _e('Items are arranged into rows. Rows progress vertically. Similar to what you would expect from a layout that uses CSS floats.', 'modern-events-calendar-lite'); ?></p>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][fit_to_row]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][fit_to_row]" id="mec_skin_masonry_fit_to_row" value="1" <?php if(isset($sk_options_masonry['fit_to_row']) and $sk_options_masonry['fit_to_row']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_fit_to_row"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Convert Masonry to Grid', 'modern-events-calendar-lite'); ?></label>
                        <p class="description"><?php _e('For using this option, your events should come with image', 'modern-events-calendar-lite'); ?></p>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][masonry_like_grid]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][masonry_like_grid]" id="mec_skin_masonry_like_to_grid" value="1" <?php if(isset($sk_options_masonry['masonry_like_grid']) and $sk_options_masonry['masonry_like_grid']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_like_to_grid"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][masonry][load_more_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][masonry][load_more_button]" id="mec_skin_masonry_load_more_button" value="1" <?php if(isset($sk_options_masonry['load_more_button']) and $sk_options_masonry['load_more_button']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_masonry_load_more_button"></label>
                    </div>
                </div>
                <?php echo $this->booking_button_field('masonry', (isset($sk_options_masonry['booking_button']) ? $sk_options_masonry['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('masonry', (isset($sk_options_masonry['custom_data']) ? $sk_options_masonry['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('masonry', (isset($sk_options_masonry['sed_method']) ? $sk_options_masonry['sed_method'] : 0), (isset($sk_options_masonry['image_popup']) ? $sk_options_masonry['image_popup'] : 0)); ?>
                <?php do_action('mec_skin_options_masonry_end', $sk_options_masonry); ?>
            </div>

            <!-- Cover -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_cover_skin_options_container">
                <?php $sk_options_cover = isset($sk_options['cover']) ? $sk_options['cover'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_cover_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][cover][style]" id="mec_skin_cover_style" onchange="mec_skin_style_changed('cover', this.value);">
						<option value="classic" <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] == 'classic') echo 'selected="selected"'; ?>><?php _e('Classic', 'modern-events-calendar-lite'); ?></option>
                        <option value="clean" <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] == 'clean') echo 'selected="selected"'; ?>><?php _e('Clean', 'modern-events-calendar-lite'); ?></option>
                        <option value="modern" <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] == 'modern') echo 'selected="selected"'; ?>><?php _e('Modern', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_cover_fluent', $sk_options_cover['style'], ['type1', 'type2', 'type3', 'type4'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row mec-skin-cover-date-format-container <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] != 'clean') echo 'mec-util-hidden'; ?>" id="mec_skin_cover_date_format_clean_container">
                    <label class="mec-col-4" for="mec_skin_cover_date_format_clean1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][cover][date_format_clean1]" id="mec_skin_cover_date_format_clean1" value="<?php echo ((isset($sk_options_cover['date_format_clean1']) and trim($sk_options_cover['date_format_clean1']) != '') ? $sk_options_cover['date_format_clean1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][cover][date_format_clean2]" id="mec_skin_cover_date_format_clean2" value="<?php echo ((isset($sk_options_cover['date_format_clean2']) and trim($sk_options_cover['date_format_clean2']) != '') ? $sk_options_cover['date_format_clean2'] : 'M'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][cover][date_format_clean3]" id="mec_skin_cover_date_format_clean3" value="<?php echo ((isset($sk_options_cover['date_format_clean3']) and trim($sk_options_cover['date_format_clean3']) != '') ? $sk_options_cover['date_format_clean3'] : 'Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, M and Y', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/cover-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-cover-date-format-container <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] != 'classic') echo 'mec-util-hidden'; ?>" id="mec_skin_cover_date_format_classic_container">
                    <label class="mec-col-4" for="mec_skin_cover_date_format_classic1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][cover][date_format_classic1]" id="mec_skin_cover_date_format_classic1" value="<?php echo ((isset($sk_options_cover['date_format_classic1']) and trim($sk_options_cover['date_format_classic1']) != '') ? $sk_options_cover['date_format_classic1'] : 'F d'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][cover][date_format_classic2]" id="mec_skin_cover_date_format_classic2" value="<?php echo ((isset($sk_options_cover['date_format_classic2']) and trim($sk_options_cover['date_format_classic2']) != '') ? $sk_options_cover['date_format_classic2'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are "F d" and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/cover-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-cover-date-format-container <?php if(isset($sk_options_cover['style']) and $sk_options_cover['style'] != 'modern') echo 'mec-util-hidden'; ?>" id="mec_skin_cover_date_format_modern_container">
                    <label class="mec-col-4" for="mec_skin_cover_date_format_modern1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][cover][date_format_modern1]" id="mec_skin_cover_date_format_modern1" value="<?php echo ((isset($sk_options_cover['date_format_modern1']) and trim($sk_options_cover['date_format_modern1']) != '') ? $sk_options_cover['date_format_modern1'] : 'l, F d Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "l, F d Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/cover-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_cover_event_id"><?php _e('Event', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][cover][event_id]" id="mec_skin_cover_event_id">
                        <?php foreach($events as $event): ?>
                        <option value="<?php echo $event->ID; ?>" <?php if(isset($sk_options_cover['event_id']) and $sk_options_cover['event_id'] == $event->ID) echo 'selected="selected"'; ?>><?php echo $event->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_cover_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][cover][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][cover][include_local_time]" id="mec_skin_cover_include_local_time" value="1" <?php if(isset($sk_options_cover['include_local_time']) and trim($sk_options_cover['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_cover_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_cover_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_cover_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][cover][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][cover][display_label]" id="mec_skin_cover_display_label" value="1" <?php if(isset($sk_options_cover['display_label']) and trim($sk_options_cover['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_cover_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_cover_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_cover_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][cover][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][cover][reason_for_cancellation]" id="mec_skin_cover_reason_for_cancellation" value="1" <?php if(isset($sk_options_cover['reason_for_cancellation']) and trim($sk_options_cover['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_cover_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <?php do_action('mec_skin_options_cover_end', $sk_options_cover); ?>
            </div>

            <!-- CountDown -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_countdown_skin_options_container">
                <?php $sk_options_countdown = isset($sk_options['countdown']) ? $sk_options['countdown'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_countdown_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][countdown][style]" id="mec_skin_countdown_style" onchange="mec_skin_style_changed('countdown', this.value);">
						<option value="style1" <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] == 'style1') echo 'selected="selected"'; ?>><?php _e('Style 1', 'modern-events-calendar-lite'); ?></option>
                        <option value="style2" <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] == 'style2') echo 'selected="selected"'; ?>><?php _e('Style 2', 'modern-events-calendar-lite'); ?></option>
                        <option value="style3" <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] == 'style3') echo 'selected="selected"'; ?>><?php _e('Style 3', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_countdown_fluent', $sk_options_countdown['style']); ?>
                    </select>
                </div>
                <div class="mec-form-row mec-skin-countdown-date-format-container <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] != 'clean') echo 'mec-util-hidden'; ?>" id="mec_skin_countdown_date_format_style1_container">
                    <label class="mec-col-4" for="mec_skin_countdown_date_format_style11"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][countdown][date_format_style11]" id="mec_skin_countdown_date_format_style11" value="<?php echo ((isset($sk_options_countdown['date_format_style11']) and trim($sk_options_countdown['date_format_style11']) != '') ? $sk_options_countdown['date_format_style11'] : 'j F Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "j F Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/countdown-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-countdown-date-format-container <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] != 'style2') echo 'mec-util-hidden'; ?>" id="mec_skin_countdown_date_format_style2_container">
                    <label class="mec-col-4" for="mec_skin_countdown_date_format_style21"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][countdown][date_format_style21]" id="mec_skin_countdown_date_format_style21" value="<?php echo ((isset($sk_options_countdown['date_format_style21']) and trim($sk_options_countdown['date_format_style21']) != '') ? $sk_options_countdown['date_format_style21'] : 'j F Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "j F Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/countdown-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-countdown-date-format-container <?php if(isset($sk_options_countdown['style']) and $sk_options_countdown['style'] != 'style3') echo 'mec-util-hidden'; ?>" id="mec_skin_countdown_date_format_style3_container">
                    <label class="mec-col-4" for="mec_skin_countdown_date_format_style31"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-1" name="mec[sk-options][countdown][date_format_style31]" id="mec_skin_countdown_date_format_style31" value="<?php echo ((isset($sk_options_countdown['date_format_style31']) and trim($sk_options_countdown['date_format_style31']) != '') ? $sk_options_countdown['date_format_style31'] : 'j'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][countdown][date_format_style32]" id="mec_skin_countdown_date_format_style32" value="<?php echo ((isset($sk_options_countdown['date_format_style32']) and trim($sk_options_countdown['date_format_style32']) != '') ? $sk_options_countdown['date_format_style32'] : 'F'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][countdown][date_format_style33]" id="mec_skin_countdown_date_format_style33" value="<?php echo ((isset($sk_options_countdown['date_format_style33']) and trim($sk_options_countdown['date_format_style33']) != '') ? $sk_options_countdown['date_format_style33'] : 'Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are j, F and Y', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/countdown-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_countdown_event_id"><?php _e('Event', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][countdown][event_id]" id="mec_skin_countdown_event_id">
                        <option value="-1" <?php if(isset($sk_options_countdown['event_id']) and $sk_options_countdown['event_id'] == '-1') echo 'selected="selected"'; ?>><?php echo __(' -- Next Upcoming Event -- ', 'modern-events-calendar-lite') ?></option>
                        <?php foreach($upcoming_event_ids as $upcoming_event_id): $event = get_post($upcoming_event_id); ?>
                        <option value="<?php echo $event->ID; ?>" <?php if(isset($sk_options_countdown['event_id']) and $sk_options_countdown['event_id'] == $event->ID) echo 'selected="selected"'; ?>><?php echo $event->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mec-form-row mec-not-countdown-fluent">
                    <label class="mec-col-4" for="mec_skin_countdown_bg_color"><?php _e('Background Color', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4 mec-color-picker wp-color-picker-field" id="mec_skin_countdown_bg_color" name="mec[sk-options][countdown][bg_color]" value="<?php echo ((isset($sk_options_countdown['bg_color']) and trim($sk_options_countdown['bg_color']) != '') ? $sk_options_countdown['bg_color'] : '#437df9'); ?>" data-default-color="#437df9" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_countdown_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][countdown][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][countdown][include_local_time]" id="mec_skin_countdown_include_local_time" value="1" <?php if(isset($sk_options_countdown['include_local_time']) and trim($sk_options_countdown['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_countdown_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_countdown_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_countdown_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][countdown][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][countdown][display_label]" id="mec_skin_countdown_display_label" value="1" <?php if(isset($sk_options_countdown['display_label']) and trim($sk_options_countdown['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_countdown_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_countdown_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_countdown_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][countdown][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][countdown][reason_for_cancellation]" id="mec_skin_countdown_reason_for_cancellation" value="1" <?php if(isset($sk_options_countdown['reason_for_cancellation']) and trim($sk_options_countdown['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_countdown_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <?php do_action('mec_skin_options_countdown_end', $sk_options_countdown); ?>
            </div>

            <!-- Available Spot -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_available_spot_skin_options_container">

                <?php if(!$this->main->getPRO()): ?>
                <div class="info-msg"><?php echo sprintf(__("%s is required to use this skin.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                <?php endif; ?>

                <?php $sk_options_available_spot = isset($sk_options['available_spot']) ? $sk_options['available_spot'] : array(); ?>
                <?php do_action('mec_skin_options_available_spot_init', $sk_options_available_spot); ?>
                <div class="mec-form-row mec-skin-available-spot-date-format-container mec-not-available_spot-fluent">
                    <label class="mec-col-4" for="mec_skin_available_spot_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][available_spot][date_format1]" id="mec_skin_available_spot_date_format1" value="<?php echo ((isset($sk_options_available_spot['date_format1']) and trim($sk_options_available_spot['date_format1']) != '') ? $sk_options_available_spot['date_format1'] : 'j'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][available_spot][date_format2]" id="mec_skin_available_spot_date_format2" value="<?php echo ((isset($sk_options_available_spot['date_format2']) and trim($sk_options_available_spot['date_format2']) != '') ? $sk_options_available_spot['date_format2'] : 'F'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are j and F', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/available-spots-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_available_spot_event_id"><?php _e('Event', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][available_spot][event_id]" id="mec_skin_available_spot_event_id">
                        <option value="-1" <?php if(isset($sk_options_available_spot['event_id']) and $sk_options_available_spot['event_id'] == '-1') echo 'selected="selected"'; ?>><?php echo __(' -- Next Upcoming Event -- ', 'modern-events-calendar-lite') ?></option>
                        <?php foreach($events as $event): ?>
                            <option value="<?php echo $event->ID; ?>" <?php if(isset($sk_options_available_spot['event_id']) and $sk_options_available_spot['event_id'] == $event->ID) echo 'selected="selected"'; ?>><?php echo $event->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_available_spot_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][available_spot][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][available_spot][include_local_time]" id="mec_skin_available_spot_include_local_time" value="1" <?php if(isset($sk_options_available_spot['include_local_time']) and trim($sk_options_available_spot['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_available_spot_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_available_spot_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_available_spot_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][available_spot][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][available_spot][display_label]" id="mec_skin_available_spot_display_label" value="1" <?php if(isset($sk_options_available_spot['display_label']) and trim($sk_options_available_spot['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_available_spot_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_available_spot_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_available_spot_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][available_spot][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][available_spot][reason_for_cancellation]" id="mec_skin_available_spot_reason_for_cancellation" value="1" <?php if(isset($sk_options_available_spot['reason_for_cancellation']) and trim($sk_options_available_spot['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_available_spot_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <?php do_action('mec_skin_options_available_spot_end', $sk_options_available_spot); ?>
            </div>

            <!-- Carousel View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_carousel_skin_options_container">
                <?php $sk_options_carousel = isset($sk_options['carousel']) ? $sk_options['carousel'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_carousel_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][carousel][style]" id="mec_skin_carousel_style" onchange="mec_skin_style_changed('carousel', this.value); if(this.value == 'type4'){ jQuery('.mec-carousel-archive-link').show();jQuery('.mec-carousel-head-text').show();} else { jQuery('.mec-carousel-archive-link').hide(); jQuery('.mec-carousel-head-text').hide();  }">
                        <option value="type1" <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] == 'type1') echo 'selected="selected"'; ?>><?php _e('Type 1', 'modern-events-calendar-lite'); ?></option>
                        <option value="type2" <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] == 'type2') echo 'selected="selected"'; ?>><?php _e('Type 2', 'modern-events-calendar-lite'); ?></option>
                        <option value="type3" <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] == 'type3') echo 'selected="selected"'; ?>><?php _e('Type 3', 'modern-events-calendar-lite'); ?></option>
                        <option value="type4" <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] == 'type4') echo 'selected="selected"'; ?>><?php _e('Type 4', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_carousel_fluent', $sk_options_carousel['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_carousel_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][carousel][start_date_type]" id="mec_skin_carousel_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_carousel_start_date_container').show(); else jQuery('#mec_skin_carousel_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_carousel['start_date_type']) or (isset($sk_options_carousel['start_date_type']) and $sk_options_carousel['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_carousel_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][carousel][start_date]" id="mec_skin_carousel_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_carousel['start_date'])) echo $sk_options_carousel['start_date']; ?>" />
                    </div>
                </div>
                <div class="mec-form-row mec-skin-carousel-date-format-container <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] != 'type1') echo 'mec-util-hidden'; ?>" id="mec_skin_carousel_date_format_type1_container">
                    <label class="mec-col-4" for="mec_skin_carousel_type1_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][carousel][type1_date_format1]" id="mec_skin_carousel_type1_date_format1" value="<?php echo ((isset($sk_options_carousel['type1_date_format1']) and trim($sk_options_carousel['type1_date_format1']) != '') ? $sk_options_carousel['type1_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][carousel][type1_date_format2]" id="mec_skin_carousel_type1_date_format2" value="<?php echo ((isset($sk_options_carousel['type1_date_format2']) and trim($sk_options_carousel['type1_date_format2']) != '') ? $sk_options_carousel['type1_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][carousel][type1_date_format3]" id="mec_skin_carousel_type1_date_format3" value="<?php echo ((isset($sk_options_carousel['type1_date_format3']) and trim($sk_options_carousel['type1_date_format3']) != '') ? $sk_options_carousel['type1_date_format3'] : 'Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and Y', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/carousel-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-carousel-date-format-container <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] != 'type2') echo 'mec-util-hidden'; ?>" id="mec_skin_carousel_date_format_type2_container">
                    <label class="mec-col-4" for="mec_skin_carousel_type2_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][carousel][type2_date_format1]" id="mec_skin_carousel_type2_date_format1" value="<?php echo ((isset($sk_options_carousel['type2_date_format1']) and trim($sk_options_carousel['type2_date_format1']) != '') ? $sk_options_carousel['type2_date_format1'] : 'M d, Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "M d, Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/carousel-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-carousel-date-format-container <?php if(isset($sk_options_carousel['style']) and $sk_options_carousel['style'] != 'type3') echo 'mec-util-hidden'; ?>" id="mec_skin_carousel_date_format_type3_container">
                    <label class="mec-col-4" for="mec_skin_carousel_type3_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][carousel][type3_date_format1]" id="mec_skin_carousel_type3_date_format1" value="<?php echo ((isset($sk_options_carousel['type3_date_format1']) and trim($sk_options_carousel['type3_date_format1']) != '') ? $sk_options_carousel['type3_date_format1'] : 'M d, Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "M d, Y"', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/carousel-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_carousel_count"><?php _e('Count in row', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][carousel][count]" id="mec_skin_carousel_count">
                        <option value="2" <?php echo (isset($sk_options_carousel['count']) and $sk_options_carousel['count'] == 2) ? 'selected="selected"' : ''; ?>>2</option>
                        <option value="3" <?php echo (isset($sk_options_carousel['count']) and $sk_options_carousel['count'] == 3) ? 'selected="selected"' : ''; ?>>3</option>
                        <option value="4" <?php echo (isset($sk_options_carousel['count']) and $sk_options_carousel['count'] == 4) ? 'selected="selected"' : ''; ?>>4</option>
                        <option value="6" <?php echo (isset($sk_options_carousel['count']) and $sk_options_carousel['count'] == 6) ? 'selected="selected"' : ''; ?>>6</option>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_carousel_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][carousel][limit]" id="mec_skin_carousel_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_carousel['limit'])) echo $sk_options_carousel['limit']; ?>" />
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_autoplay_status"><?php _e('Auto Play', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][autoplay_status]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][autoplay_status]" id="mec_skin_carousel_autoplay_status" value="1" <?php if(!isset($sk_options_carousel['autoplay_status']) or (isset($sk_options_carousel['autoplay_status']) and trim($sk_options_carousel['autoplay_status']))) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_autoplay_status"></label>
                    </div>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_carousel_autoplay"><?php _e('Auto Play Time', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" min="1000" step="1" name="mec[sk-options][carousel][autoplay]" id="mec_skin_carousel_autoplay" placeholder="<?php _e('eg. 3000 default is 3 second', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_carousel['autoplay']) && $sk_options_carousel['autoplay'] != '') echo $sk_options_carousel['autoplay']; ?>" />
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_loop_status"><?php _e('Loop', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][loop_status]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][loop_status]" id="mec_skin_carousel_loop_status" value="1" <?php if(!isset($sk_options_carousel['loop_status']) or (isset($sk_options_carousel['loop_status']) and trim($sk_options_carousel['loop_status']))) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_loop_status"></label>
                    </div>
                </div>
                <?php echo $this->booking_button_field('carousel', (isset($sk_options_carousel['booking_button']) ? $sk_options_carousel['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('carousel', (isset($sk_options_carousel['custom_data']) ? $sk_options_carousel['custom_data'] : 0)); ?>
                <div class="mec-form-row mec-carousel-archive-link">
                    <label class="mec-col-4" for="mec_skin_carousel_archive_link"><?php _e('Archive Link', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][carousel][archive_link]" id="mec_skin_carousel_archive_link" value="<?php echo ((isset($sk_options_carousel['archive_link']) and trim($sk_options_carousel['archive_link']) != '') ? $sk_options_carousel['archive_link'] : ''); ?>" />
                </div>
                <div class="mec-form-row mec-carousel-head-text">
                    <label class="mec-col-4" for="mec_skin_carousel_head_text"><?php _e('Head Text', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][carousel][head_text]" id="mec_skin_carousel_head_text" value="<?php echo ((isset($sk_options_carousel['head_text']) and trim($sk_options_carousel['head_text']) != '') ? $sk_options_carousel['head_text'] : ''); ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][include_local_time]" id="mec_skin_carousel_include_local_time" value="1" <?php if(isset($sk_options_carousel['include_local_time']) and trim($sk_options_carousel['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Include Events Times -->
                <div class="mec-form-row mec-switcher mec-include-events-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_include_events_times"><?php _e('Include Events Times', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][include_events_times]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][include_events_times]" id="mec_skin_carousel_include_events_times" value="1" <?php if(isset($sk_options_carousel['include_events_times']) and trim($sk_options_carousel['include_events_times'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_include_events_times"></label>
                    </div>
                </div>
                <!-- End Include Events Times -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_carousel_display_normal_label">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][display_label]" id="mec_skin_carousel_display_label" value="1" <?php if(isset($sk_options_carousel['display_label']) and trim($sk_options_carousel['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_carousel_display_reason_for_cancellation">
                    <div class="mec-col-4">
                        <label for="mec_skin_carousel_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][carousel][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][carousel][reason_for_cancellation]" id="mec_skin_carousel_reason_for_cancellation" value="1" <?php if(isset($sk_options_carousel['reason_for_cancellation']) and trim($sk_options_carousel['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_carousel_reason_for_cancellation"></label>
                    </div>
                </div>
                <div class="mec-sed-methode-container">
                    <?php echo $this->sed_method_field('carousel', (isset($sk_options_carousel['sed_method']) ? $sk_options_carousel['sed_method'] : 0), (isset($sk_options_carousel['image_popup']) ? $sk_options_carousel['image_popup'] : 0)); ?>
                </div>
                <!-- End Display Reason for Cancellation -->
                <?php do_action('mec_skin_options_carousel_end', $sk_options_carousel); ?>
            </div>

            <!-- Slider View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_slider_skin_options_container">
                <?php $sk_options_slider = isset($sk_options['slider']) ? $sk_options['slider'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_slider_style"><?php _e('Style', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][slider][style]" id="mec_skin_slider_style" onchange="mec_skin_style_changed('slider', this.value);">
                        <option value="t1" <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] == 't1') echo 'selected="selected"'; ?>><?php _e('Type 1', 'modern-events-calendar-lite'); ?></option>
                        <option value="t2" <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] == 't2') echo 'selected="selected"'; ?>><?php _e('Type 2', 'modern-events-calendar-lite'); ?></option>
                        <option value="t3" <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] == 't3') echo 'selected="selected"'; ?>><?php _e('Type 3', 'modern-events-calendar-lite'); ?></option>
                        <option value="t4" <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] == 't4') echo 'selected="selected"'; ?>><?php _e('Type 4', 'modern-events-calendar-lite'); ?></option>
                        <option value="t5" <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] == 't5') echo 'selected="selected"'; ?>><?php _e('Type 5', 'modern-events-calendar-lite'); ?></option>
                        <?php do_action('mec_slider_fluent', $sk_options_slider['style'] ); ?>
                    </select>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_slider_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][slider][start_date_type]" id="mec_skin_slider_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_slider_start_date_container').show(); else jQuery('#mec_skin_slider_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_slider['start_date_type']) or (isset($sk_options_slider['start_date_type']) and $sk_options_slider['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_slider_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][slider][start_date]" id="mec_skin_slider_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_slider['start_date'])) echo $sk_options_slider['start_date']; ?>" />
                    </div>
                </div>
                <div class="mec-form-row mec-skin-slider-date-format-container <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] != 't1') echo 'mec-util-hidden'; ?>" id="mec_skin_slider_date_format_t1_container">
                    <label class="mec-col-4" for="mec_skin_slider_type1_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][slider][type1_date_format1]" id="mec_skin_slider_type1_date_format1" value="<?php echo ((isset($sk_options_slider['type1_date_format1']) and trim($sk_options_slider['type1_date_format1']) != '') ? $sk_options_slider['type1_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type1_date_format2]" id="mec_skin_slider_type1_date_format2" value="<?php echo ((isset($sk_options_slider['type1_date_format2']) and trim($sk_options_slider['type1_date_format2']) != '') ? $sk_options_slider['type1_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type1_date_format3]" id="mec_skin_slider_type1_date_format3" value="<?php echo ((isset($sk_options_slider['type1_date_format3']) and trim($sk_options_slider['type1_date_format3']) != '') ? $sk_options_slider['type1_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slider-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-slider-date-format-container <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] != 't2') echo 'mec-util-hidden'; ?>" id="mec_skin_slider_date_format_t2_container">
                    <label class="mec-col-4" for="mec_skin_slider_type2_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][slider][type2_date_format1]" id="mec_skin_slider_type2_date_format1" value="<?php echo ((isset($sk_options_slider['type2_date_format1']) and trim($sk_options_slider['type2_date_format1']) != '') ? $sk_options_slider['type2_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type2_date_format2]" id="mec_skin_slider_type2_date_format2" value="<?php echo ((isset($sk_options_slider['type2_date_format2']) and trim($sk_options_slider['type2_date_format2']) != '') ? $sk_options_slider['type2_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type2_date_format3]" id="mec_skin_slider_type2_date_format3" value="<?php echo ((isset($sk_options_slider['type2_date_format3']) and trim($sk_options_slider['type2_date_format3']) != '') ? $sk_options_slider['type2_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slider-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-slider-date-format-container <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] != 't3') echo 'mec-util-hidden'; ?>" id="mec_skin_slider_date_format_t3_container">
                    <label class="mec-col-4" for="mec_skin_slider_type3_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][slider][type3_date_format1]" id="mec_skin_slider_type3_date_format1" value="<?php echo ((isset($sk_options_slider['type3_date_format1']) and trim($sk_options_slider['type3_date_format1']) != '') ? $sk_options_slider['type3_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type3_date_format2]" id="mec_skin_slider_type3_date_format2" value="<?php echo ((isset($sk_options_slider['type3_date_format2']) and trim($sk_options_slider['type3_date_format2']) != '') ? $sk_options_slider['type3_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type3_date_format3]" id="mec_skin_slider_type3_date_format3" value="<?php echo ((isset($sk_options_slider['type3_date_format3']) and trim($sk_options_slider['type3_date_format3']) != '') ? $sk_options_slider['type3_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slider-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-slider-date-format-container <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] != 't4') echo 'mec-util-hidden'; ?>" id="mec_skin_slider_date_format_t4_container">
                    <label class="mec-col-4" for="mec_skin_slider_type4_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][slider][type4_date_format1]" id="mec_skin_slider_type4_date_format1" value="<?php echo ((isset($sk_options_slider['type4_date_format1']) and trim($sk_options_slider['type4_date_format1']) != '') ? $sk_options_slider['type4_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type4_date_format2]" id="mec_skin_slider_type4_date_format2" value="<?php echo ((isset($sk_options_slider['type4_date_format2']) and trim($sk_options_slider['type4_date_format2']) != '') ? $sk_options_slider['type4_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type4_date_format3]" id="mec_skin_slider_type4_date_format3" value="<?php echo ((isset($sk_options_slider['type4_date_format3']) and trim($sk_options_slider['type4_date_format3']) != '') ? $sk_options_slider['type4_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slider-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row mec-skin-slider-date-format-container <?php if(isset($sk_options_slider['style']) and $sk_options_slider['style'] != 't5') echo 'mec-util-hidden'; ?>" id="mec_skin_slider_date_format_t5_container">
                    <label class="mec-col-4" for="mec_skin_slider_type5_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][slider][type5_date_format1]" id="mec_skin_slider_type5_date_format1" value="<?php echo ((isset($sk_options_slider['type5_date_format1']) and trim($sk_options_slider['type5_date_format1']) != '') ? $sk_options_slider['type5_date_format1'] : 'd'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type5_date_format2]" id="mec_skin_slider_type5_date_format2" value="<?php echo ((isset($sk_options_slider['type5_date_format2']) and trim($sk_options_slider['type5_date_format2']) != '') ? $sk_options_slider['type5_date_format2'] : 'F'); ?>" />
                    <input type="text" class="mec-col-1" name="mec[sk-options][slider][type5_date_format3]" id="mec_skin_slider_type5_date_format3" value="<?php echo ((isset($sk_options_slider['type5_date_format3']) and trim($sk_options_slider['type5_date_format3']) != '') ? $sk_options_slider['type5_date_format3'] : 'l'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are d, F and l', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/slider-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_slider_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][slider][limit]" id="mec_skin_slider_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_slider['limit'])) echo $sk_options_slider['limit']; ?>" />
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_slider_autoplay"><?php _e('Auto Play Time', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" min="1000" step="1" name="mec[sk-options][slider][autoplay]" id="mec_skin_slider_autoplay" placeholder="<?php _e('eg. 3000 default is 3 second', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_slider['autoplay']) && $sk_options_slider['autoplay'] != '') echo $sk_options_slider['autoplay']; ?>" />
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_slider_transition_time"><?php _e('Transition Time', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" min="0" step="1" name="mec[sk-options][slider][transition_time]" id="mec_skin_slider_transition_time" placeholder="<?php _e('eg. 1000 is 1 second', 'modern-events-calendar-lite'); ?>" value="<?php echo ((isset($sk_options_slider['transition_time']) && $sk_options_slider['transition_time'] != '') ? $sk_options_slider['transition_time'] : 250); ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_slider_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][slider][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][slider][include_local_time]" id="mec_skin_slider_include_local_time" value="1" <?php if(isset($sk_options_slider['include_local_time']) and trim($sk_options_slider['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_slider_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_slider_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_slider_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][slider][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][slider][display_label]" id="mec_skin_slider_display_label" value="1" <?php if(isset($sk_options_slider['display_label']) and trim($sk_options_slider['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_slider_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_slider_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_slider_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][slider][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][slider][reason_for_cancellation]" id="mec_skin_slider_reason_for_cancellation" value="1" <?php if(isset($sk_options_slider['reason_for_cancellation']) and trim($sk_options_slider['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_slider_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <?php echo $this->display_custom_data_field('slider', (isset($sk_options_slider['custom_data']) ? $sk_options_slider['custom_data'] : 0)); ?>
                <?php do_action('mec_skin_options_slider_end', $sk_options_slider); ?>
            </div>

            <!-- Timeline View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_timeline_skin_options_container">

                <?php $sk_options_timeline = isset($sk_options['timeline']) ? $sk_options['timeline'] : array(); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timeline_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timeline][start_date_type]" id="mec_skin_timeline_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_timeline_start_date_container').show(); else jQuery('#mec_skin_timeline_start_date_container').hide();">
                        <option value="today" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                        <option value="yesterday" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'yesterday') echo 'selected="selected"'; ?>><?php _e('Yesterday', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_current_month" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_timeline['start_date_type']) or (isset($sk_options_timeline['start_date_type']) and $sk_options_timeline['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_timeline_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][timeline][start_date]" id="mec_skin_timeline_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_timeline['start_date'])) echo $sk_options_timeline['start_date']; ?>" />
                    </div>
                </div>
                <!-- Start Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timeline_end_date_type"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][timeline][end_date_type]" id="mec_skin_timeline_end_date_type" onchange="if(this.value === 'date') jQuery('#mec_skin_timeline_end_date_container').show(); else jQuery('#mec_skin_timeline_end_date_container').hide();">
                        <option value="date" <?php if(isset($sk_options_timeline['end_date_type']) and $sk_options_timeline['end_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                        <option value="today" <?php if(isset($sk_options_timeline['end_date_type']) and $sk_options_timeline['end_date_type'] == 'today') echo 'selected="selected"'; ?>><?php _e('Today', 'modern-events-calendar-lite'); ?></option>
                        <option value="tomorrow" <?php if(isset($sk_options_timeline['end_date_type']) and $sk_options_timeline['end_date_type'] == 'tomorrow') echo 'selected="selected"'; ?>><?php _e('Tomorrow', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php echo (!isset($sk_options_timeline['end_date_type']) or (isset($sk_options_timeline['end_date_type']) and $sk_options_timeline['end_date_type'] == 'date')) ? '' : 'mec-util-hidden'; ?>" id="mec_skin_timeline_end_date_container">
                        <input type="text" class="mec_date_picker" name="mec[sk-options][timeline][maximum_date_range]" value="<?php echo isset($sk_options_timeline['maximum_date_range']) ? esc_attr($sk_options_timeline['maximum_date_range']) : ''; ?>" placeholder="<?php _e('Maximum Date', 'modern-events-calendar-lite'); ?>" autocomplete="off" />
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Maximum Date', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php _e('Show events before the specified date.', 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <!-- End Maximum Date -->
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timeline_classic_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-4" name="mec[sk-options][timeline][classic_date_format1]" id="mec_skin_timeline_classic_date_format1" value="<?php echo ((isset($sk_options_timeline['classic_date_format1']) and trim($sk_options_timeline['classic_date_format1']) != '') ? $sk_options_timeline['classic_date_format1'] : 'd F Y'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default value is "d F Y', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/timeline-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_timeline_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                    <input class="mec-col-4" type="number" name="mec[sk-options][timeline][limit]" id="mec_skin_timeline_limit" placeholder="<?php _e('eg. 6', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_timeline['limit'])) echo $sk_options_timeline['limit']; ?>" />
                </div>
                <!-- Start LocalTime -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times">
                    <div class="mec-col-4">
                        <label for="mec_skin_timeline_include_local_time"><?php _e('Include Local Time', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][include_local_time]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][include_local_time]" id="mec_skin_timeline_include_local_time" value="1" <?php if(isset($sk_options_timeline['include_local_time']) and trim($sk_options_timeline['include_local_time'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_include_local_time"></label>
                    </div>
                </div>
                <!-- End LocalTime -->
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_timeline_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_timeline_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][display_label]" id="mec_skin_timeline_display_label" value="1" <?php if(isset($sk_options_timeline['display_label']) and trim($sk_options_timeline['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_timeline_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_timeline_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][reason_for_cancellation]" id="mec_skin_timeline_reason_for_cancellation" value="1" <?php if(isset($sk_options_timeline['reason_for_cancellation']) and trim($sk_options_timeline['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_timeline_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_timeline_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][display_categories]" id="mec_skin_timeline_display_categories" value="1" <?php if(isset($sk_options_timeline['display_categories']) and trim($sk_options_timeline['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('timeline', (isset($sk_options_timeline['display_organizer']) ? $sk_options_timeline['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_timeline_load_more_button"><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][load_more_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][load_more_button]" id="mec_skin_timeline_load_more_button" value="1" <?php if(!isset($sk_options_timeline['load_more_button']) or (isset($sk_options_timeline['load_more_button']) and $sk_options_timeline['load_more_button'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_load_more_button"></label>
                    </div>
                </div>
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label for="mec_skin_timeline_month_divider"><?php _e('Show Month Divider', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][timeline][month_divider]" value="0" />
                        <input type="checkbox" name="mec[sk-options][timeline][month_divider]" id="mec_skin_timeline_month_divider" value="1" <?php if(isset($sk_options_timeline['month_divider']) and $sk_options_timeline['month_divider']) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_timeline_month_divider"></label>
                    </div>
                </div>
                <?php echo $this->booking_button_field('timeline', (isset($sk_options_timeline['booking_button']) ? $sk_options_timeline['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('timeline', (isset($sk_options_timeline['custom_data']) ? $sk_options_timeline['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('timeline', (isset($sk_options_timeline['sed_method']) ? $sk_options_timeline['sed_method'] : 0), (isset($sk_options_timeline['image_popup']) ? $sk_options_timeline['image_popup'] : 0)); ?>
            </div>

            <!-- Tile View -->
            <div class="mec-skin-options-container mec-util-hidden" id="mec_tile_skin_options_container">
                <?php $sk_options_tile = isset($sk_options['tile']) ? $sk_options['tile'] : array(); ?>
                <?php do_action('mec_skin_options_tile_init', $sk_options_tile); ?>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_tile_start_date_type"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][tile][start_date_type]" id="mec_skin_tile_start_date_type" onchange="if(this.value == 'date') jQuery('#mec_skin_tile_start_date_container').show(); else jQuery('#mec_skin_tile_start_date_container').hide();">
                        <option value="start_current_month" <?php if(isset($sk_options_tile['start_date_type']) and $sk_options_tile['start_date_type'] == 'start_current_month') echo 'selected="selected"'; ?>><?php _e('Start of Current Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_next_month" <?php if(isset($sk_options_tile['start_date_type']) and $sk_options_tile['start_date_type'] == 'start_next_month') echo 'selected="selected"'; ?>><?php _e('Start of Next Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="start_last_month" <?php if(isset($sk_options_tile['start_date_type']) and $sk_options_tile['start_date_type'] == 'start_last_month') echo 'selected="selected"'; ?>><?php _e('Start of Last Month', 'modern-events-calendar-lite'); ?></option>
                        <option value="date" <?php if(isset($sk_options_tile['start_date_type']) and $sk_options_tile['start_date_type'] == 'date') echo 'selected="selected"'; ?>><?php _e('On a certain date', 'modern-events-calendar-lite'); ?></option>
                    </select>
                    <div class="mec-col-4 <?php if(!isset($sk_options_tile['start_date_type']) or (isset($sk_options_tile['start_date_type']) and $sk_options_tile['start_date_type'] != 'date')) echo 'mec-util-hidden'; ?>" id="mec_skin_tile_start_date_container">
                        <input class="mec_date_picker" type="text" name="mec[sk-options][tile][start_date]" id="mec_skin_tile_start_date" placeholder="<?php echo sprintf(__('eg. %s', 'modern-events-calendar-lite'), date('Y-n-d')); ?>" value="<?php if(isset($sk_options_tile['start_date'])) echo $sk_options_tile['start_date']; ?>" />
                    </div>
                </div>
                <div class="mec-form-row mec-skin-tile-date-format-container <?php if(isset($sk_options_tile['style']) and $sk_options_tile['style'] != 'clean') echo 'mec-util-hidden'; ?>" id="mec_skin_tile_date_format_clean_container">
                    <label class="mec-col-4" for="mec_skin_tile_minimal_date_format1"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></label>
                    <input type="text" class="mec-col-2" name="mec[sk-options][tile][clean_date_format1]" id="mec_skin_tile_clean_date_format1" value="<?php echo ((isset($sk_options_tile['clean_date_format1']) and trim($sk_options_tile['clean_date_format1']) != '') ? $sk_options_tile['clean_date_format1'] : 'j'); ?>" />
                    <input type="text" class="mec-col-2" name="mec[sk-options][tile][clean_date_format2]" id="mec_skin_tile_clean_date_format2" value="<?php echo ((isset($sk_options_tile['clean_date_format2']) and trim($sk_options_tile['clean_date_format2']) != '') ? $sk_options_tile['clean_date_format2'] : 'M'); ?>" />
                    <span class="mec-tooltip">
                        <div class="box top">
                            <h5 class="title"><?php _e('Date Formats', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e('Default values are j and M', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/list-view-skin/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_skin_tile_count"><?php _e('Count in row', 'modern-events-calendar-lite'); ?></label>
                    <select class="mec-col-4 wn-mec-select" name="mec[sk-options][tile][count]" id="mec_skin_tile_count">
                        <option value="4" <?php echo (isset($sk_options_tile['count']) and $sk_options_tile['count'] == 4) ? 'selected="selected"' : ''; ?>>4</option>
                        <option value="3" <?php echo (isset($sk_options_tile['count']) and $sk_options_tile['count'] == 3) ? 'selected="selected"' : ''; ?>>3</option>
                        <option value="2" <?php echo (isset($sk_options_tile['count']) and $sk_options_tile['count'] == 2) ? 'selected="selected"' : ''; ?>>2</option>
                    </select>
                </div>
                <!-- Start Display Label -->
                <div class="mec-form-row mec-switcher mec-include-events-local-times" id="mec_skin_tile_display_normal_label">
					<div class="mec-col-4">
						<label for="mec_skin_tile_display_label"><?php _e('Display Normal Labels', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][tile][display_label]" value="0" />
                        <input type="checkbox" name="mec[sk-options][tile][display_label]" id="mec_skin_tile_display_label" value="1" <?php if(isset($sk_options_tile['display_label']) and trim($sk_options_tile['display_label'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_tile_display_label"></label>
                    </div>
                </div>
                <!-- End Display Label -->
                <!-- Start Reason for Cancellation -->
                <div class="mec-form-row mec-switcher" id="mec_skin_tile_display_reason_for_cancellation">
					<div class="mec-col-4">
						<label for="mec_skin_tile_reason_for_cancellation"><?php _e('Display Reason for Cancellation', 'modern-events-calendar-lite'); ?></label>
					</div>
					<div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][tile][reason_for_cancellation]" value="0" />
                        <input type="checkbox" name="mec[sk-options][tile][reason_for_cancellation]" id="mec_skin_tile_reason_for_cancellation" value="1" <?php if(isset($sk_options_tile['reason_for_cancellation']) and trim($sk_options_tile['reason_for_cancellation'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_tile_reason_for_cancellation"></label>
                    </div>
                </div>
                <!-- End Display Reason for Cancellation -->
                <!-- Start Display Categories -->
                <div class="mec-form-row mec-switcher" id="mec_skin_tile_display_categories_wp">
                    <div class="mec-col-4">
                        <label for="mec_skin_tile_display_categories"><?php _e('Display Categories', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][tile][display_categories]" value="0" />
                        <input type="checkbox" name="mec[sk-options][tile][display_categories]" id="mec_skin_tile_display_categories" value="1" <?php if(isset($sk_options_tile['display_categories']) and trim($sk_options_tile['display_categories'])) echo 'checked="checked"'; ?> />
                        <label for="mec_skin_tile_display_categories"></label>
                    </div>
                </div>
                <!-- End Display Categories -->
                <!-- Start Display Organizer -->
                <?php echo $this->display_organizer_field('tile', (isset($sk_options_tile['display_organizer']) ? $sk_options_tile['display_organizer'] : 0)); ?>
                <!-- End Display Organizer -->
                <div class="mec-form-row mec-switcher">
                    <div class="mec-col-4">
                        <label><?php _e('Next/Previous Buttons', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-col-4">
                        <input type="hidden" name="mec[sk-options][tile][next_previous_button]" value="0" />
                        <input type="checkbox" name="mec[sk-options][tile][next_previous_button]" id="mec_skin_tile_next_previous_button" value="1" <?php if(!isset($sk_options_tile['next_previous_button']) or (isset($sk_options_tile['next_previous_button']) and $sk_options_tile['next_previous_button'])) echo 'checked="checked"'; ?> onchange="jQuery('#mec_tile_off_month_options').toggle();" />
                        <label for="mec_skin_tile_next_previous_button"></label>
                    </div>
                </div>
                <p class="description"><?php _e('For showing next/previous month navigation.', 'modern-events-calendar-lite'); ?></p>
                <div id="mec_tile_off_month_options" <?php if(!isset($sk_options_tile['next_previous_button']) or (isset($sk_options_tile['next_previous_button']) and $sk_options_tile['next_previous_button'])) echo 'style="display:none;"'; ?>>
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_skin_tile_limit"><?php _e('Limit', 'modern-events-calendar-lite'); ?></label>
                        <input class="mec-col-4" type="number" name="mec[sk-options][tile][limit]" id="mec_skin_tile_limit" placeholder="<?php _e('eg. 60', 'modern-events-calendar-lite'); ?>" value="<?php if(isset($sk_options_tile['limit'])) echo $sk_options_tile['limit']; ?>" />
                    </div>
                    <div class="mec-form-row mec-switcher">
                        <div class="mec-col-4">
                            <label><?php _e('Load More Button', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-col-4">
                            <input type="hidden" name="mec[sk-options][tile][load_more_button]" value="0" />
                            <input type="checkbox" name="mec[sk-options][tile][load_more_button]" id="mec_skin_tile_load_more_button" value="1" <?php if(!isset($sk_options_tile['load_more_button']) or (isset($sk_options_tile['load_more_button']) and $sk_options_tile['load_more_button'])) echo 'checked="checked"'; ?> />
                            <label for="mec_skin_tile_load_more_button"></label>
                        </div>
                    </div>
                </div>
                <?php echo $this->booking_button_field('tile', (isset($sk_options_tile['booking_button']) ? $sk_options_tile['booking_button'] : 0)); ?>
                <?php echo $this->display_custom_data_field('tile', (isset($sk_options_tile['custom_data']) ? $sk_options_tile['custom_data'] : 0)); ?>
                <?php echo $this->sed_method_field('tile', (isset($sk_options_tile['sed_method']) ? $sk_options_tile['sed_method'] : 0), (isset($sk_options_tile['image_popup']) ? $sk_options_tile['image_popup'] : 0)); ?>
            </div>


            <!-- Custom Skins -->
            <?php do_action('mec_skin_options', $sk_options); ?>
        </div>
    </div>
</div>

<script>
    // Niceselect
    jQuery(document).ready(function() {
        jQuery('.mec-custom-nice-select').find('li').each( function(index, elemement) {
            var $this = jQuery(this),
                $name = $this.text();
            $this.text('');
            $this.append('<span class="wn-mec-text">'+$name+'</span>');
        });
        jQuery('.mec-custom-nice-select li[data-value="list"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="grid"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="agenda"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/agenda.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="full_calendar"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full_calendar.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="yearly_view"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/yearly.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="monthly_view"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="daily_view"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/daily.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="weekly_view"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/weekly.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="timetable"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/timetable.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="masonry"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/masonry.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="map"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/map.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="cover"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/cover.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="countdown"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/countdown.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="available_spot"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/available_spot.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="carousel"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/carousel.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="slider"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="timeline"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/timeline.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="tile"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/tile.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="custom"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/shortcode-designer.svg'; ?>" /></div>');
        jQuery('.mec-custom-nice-select li[data-value="general_calendar"]').prepend('<div class="wn-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly.svg'; ?>" /></div>');

        /** List View Skins */
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list/list-classic.png'; ?>" /></span>');
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="minimal"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list/list-minimal.png'; ?>" /></span>');
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list/list-modern.png'; ?>" /></span>');
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="standard"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list/list-standard.png'; ?>" /></span>');
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="accordion"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/list/list-accordion.png'; ?>" /></span>');
        jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-list-view.png'; ?>" /></span>');

        /** Grid View Skins */
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-classic.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-clean.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="minimal"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-minimal.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-modern.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="simple"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-simple.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="colorful"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-colorful.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="novel"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-novel.png'; ?>" /></span>');
        jQuery('#mec_grid_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-grid-view.png'; ?>" /></span>');

        /** Agenda View Skins */
        jQuery('#mec_agenda_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/agenda/agenda-clean.png'; ?>" /></span>');
        jQuery('#mec_agenda_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-agenda-view.png'; ?>" /></span>');

        /** FullCalendar View Skins */
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="list"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-list.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="grid"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/grid/grid-modern.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="tile"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/tile/tile-classic.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="daily"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-daily.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="weekly"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-weekly.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="monthly"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-monthly.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="yearly"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-yearly.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-full-calendar-view.png'; ?>" /></span>');

        /** FullCalendar View Skins > Monthly Style */
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/full-calendar/full-calendar-monthly.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-clean.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="novel"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-novel.png'; ?>" /></span>');
        jQuery('#mec_full_calendar_skin_options_container .mec-form-row .nice-select .list li[data-value="simple"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-simple.png'; ?>" /></span>');

        /** Yearly View Skins */
        jQuery('#mec_yearly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/yearly/yearly-modern.png'; ?>" /></span>');
        jQuery('#mec_yearly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-yearly-view.png'; ?>" /></span>');

        /** Monthly View Skins */
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-classic.png'; ?>" /></span>');
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-clean.png'; ?>" /></span>');
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-modern.png'; ?>" /></span>');
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="novel"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-novel.png'; ?>" /></span>');
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="simple"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/monthly/monthly-simple.png'; ?>" /></span>');
        jQuery('#mec_monthly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-monthly-view.png'; ?>" /></span>');

        /** Time Table View Skins */
        jQuery('#mec_timetable_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/time-table/time-table-modern.png'; ?>" /></span>');
        jQuery('#mec_timetable_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/time-table/time-table-clean.png'; ?>" /></span>');
        jQuery('#mec_timetable_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-time-table-view.png'; ?>" /></span>');

        /** Cover View Skins */
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/cover/cover-classic.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="clean"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/cover/cover-clean.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="modern"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/cover/cover-modern.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type1"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-cover-view-type1.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type2"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-cover-view-type2.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type3"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-cover-view-type3.png'; ?>" /></span>');
        jQuery('#mec_cover_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type4"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-cover-view-type4.png'; ?>" /></span>');

        /** Countdown View Skins */
        jQuery('#mec_countdown_skin_options_container .mec-form-row .nice-select .list li[data-value="style1"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/countdown/countdown-type-1.png'; ?>" /></span>');
        jQuery('#mec_countdown_skin_options_container .mec-form-row .nice-select .list li[data-value="style2"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/countdown/countdown-type-2.png'; ?>" /></span>');
        jQuery('#mec_countdown_skin_options_container .mec-form-row .nice-select .list li[data-value="style3"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/countdown/countdown-type-3.png'; ?>" /></span>');
        jQuery('#mec_countdown_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-countdown-view.png'; ?>" /></span>');

        /** Carousel View Skins */
        jQuery('#mec_carousel_skin_options_container .mec-form-row .nice-select .list li[data-value="type1"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/carousel/carousel-type-1.png'; ?>" /></span>');
        jQuery('#mec_carousel_skin_options_container .mec-form-row .nice-select .list li[data-value="type2"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/carousel/carousel-type-2.png'; ?>" /></span>');
        jQuery('#mec_carousel_skin_options_container .mec-form-row .nice-select .list li[data-value="type3"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/carousel/carousel-type-3.png'; ?>" /></span>');
        jQuery('#mec_carousel_skin_options_container .mec-form-row .nice-select .list li[data-value="type4"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/carousel/carousel-type-4.png'; ?>" /></span>');
        jQuery('#mec_carousel_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-carousel-view.png'; ?>" /></span>');

        /** Slider View Skins */
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="t1"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider/slider-type-1.png'; ?>" /></span>');
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="t2"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider/slider-type-2.png'; ?>" /></span>');
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="t3"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider/slider-type-3.png'; ?>" /></span>');
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="t4"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider/slider-type-4.png'; ?>" /></span>');
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="t5"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/slider/slider-type-5.png'; ?>" /></span>');
        jQuery('#mec_slider_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-slider-view.png'; ?>" /></span>');

        /** Daily View Skins */
        jQuery('#mec_daily_view_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/daily/daily-classic.png'; ?>" /></span>');
        jQuery('#mec_daily_view_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-daily-view.png'; ?>" /></span>');

        /** Weekly View Skins */
        jQuery('#mec_weekly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/weekly/weekly-classic.png'; ?>" /></span>');
        jQuery('#mec_weekly_view_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-weekly-view.png'; ?>" /></span>');

        /** Masonry View Skins */
        jQuery('#mec_masonry_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/masonry/masonry-classic.png'; ?>" /></span>');
        jQuery('#mec_masonry_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-masonry-view.png'; ?>" /></span>');

        /** Tile View Skins */
        jQuery('#mec_tile_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/tile/tile-classic.png'; ?>" /></span>');
        jQuery('#mec_tile_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-tile-view.png'; ?>" /></span>');

        /** Available Spot View Skins */
        jQuery('#mec_available_spot_skin_options_container .mec-form-row .nice-select .list li[data-value="classic"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/available-spot/available-spot-classic.png'; ?>" /></span>');
        jQuery('#mec_available_spot_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type1"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-available-spot-view-type1.png'; ?>" /></span>');
        jQuery('#mec_available_spot_skin_options_container .mec-form-row .nice-select .list li[data-value="fluent-type2"]').append('<span class="wn-hover-img-sh"><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../../assets/img/skins/fluent/fluent-available-spot-view-type2.png'; ?>" /></span>');

        /** Hide Local time option when list view skin has been set on accorion */
        // jQuery('#mec_list_skin_options_container .mec-form-row .nice-select .list li[data-value="accordion"]').parents()eq(4).find('#mec_skin_list_localtime').hide();

    });
</script>