<div id="mec_popup_settings" class="mec-setup-wizard-wrap lity-hide">
    <div class="mec-wizard-content wns-be-group-tab">
        <div class="mec-wizard-loading"><div class="mec-loader"></div></div>
        <div class="mec-steps-container">
            <img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/mec-logo.svg'; ?>" />
            <ul>
                <li class="mec-step mec-step-1 mec-step-passed"><span>1</span></li>
                <li class="mec-step mec-step-2"><span>2</span></li>
                <li class="mec-step mec-step-3"><span>3</span></li>
                <li class="mec-step mec-step-4"><span>4</span></li>
                <?php if($this->getPRO()) : ?>
                <li class="mec-step mec-step-5"><span>5</span></li>
                <li class="mec-step mec-step-6"><span>6</span></li>
                <?php else : ?>
                <li class="mec-step mec-step-5"><span>5</span></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="mec-steps-panel">
            <div class="mec-steps-header">
                <div class="mec-steps-header-userinfo">
                    <?php $user = wp_get_current_user(); ?>
                    <span class="mec-steps-header-img"><img src="<?php echo esc_url(get_avatar_url($user->ID)); ?>" /></span>
                    <span class="mec-steps-header-name"><?php echo $user->display_name ; ?></span>
                </div>
                <div class="mec-steps-header-dashboard">
                    <a href="<?php echo admin_url('admin.php?page=mec-intro'); ?>"><i class="mec-sl-pie-chart"></i><?php esc_html_e('Dashboard', 'modern-events-calendar-lite'); ?></a>
                </div>
                <div class="mec-steps-header-settings">
                    <a href="<?php echo admin_url('admin.php?page=MEC-settings'); ?>"><i class="mec-sl-settings"></i><?php esc_html_e('Settings', 'modern-events-calendar-lite'); ?></a>
                </div>
            </div>
            <div class="mec-step-wizard-content mec-active-step" data-step="1">
                <!-- <h4><?php _e('Do you want to import dummy events/Shortcodes?', 'modern-events-calendar-lite'); ?></h4>
                <button class="mec-button-import-events mec-wizard-inner-button"><?php _e('Import All Dummy Events', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-next-icon.svg'; ?>" /></button>
                <button class="mec-button-import-shortcodes mec-wizard-inner-button"><?php _e('Import All Dummy Shortcodes', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-next-icon.svg'; ?>" /></button> -->

                <form id="mec_save_weekdays_form" style="margin-top:-50px">
                    <?php $weekdays = $this->main->get_weekday_i18n_labels(); ?>
                    <div class="mec-form-row"  id="mec_settings_weekdays">
                        <label class="mec-col-12" for="mec_settings_weekdays"><?php _e('Weekdays', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-6">
                            <div class="mec-box">
                                <?php $mec_weekdays = $this->main->get_weekdays(); foreach($weekdays as $weekday): ?>
                                <label for="mec_settings_weekdays_<?php echo $weekday[0]; ?>">
                                    <input type="checkbox" id="mec_settings_weekdays_<?php echo $weekday[0]; ?>" name="mec[settings][weekdays][]" value="<?php echo $weekday[0]; ?>" <?php echo (in_array($weekday[0], $mec_weekdays) ? 'checked="checked"' : ''); ?> />
                                    <?php echo $weekday[1]; ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mec-form-row" id="mec_settings_weekends">
                        <label class="mec-col-12" for="mec_settings_weekends"><?php _e('Weekends', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-6">
                        <div class="mec-box">
                            <?php $mec_weekends = $this->main->get_weekends(); foreach($weekdays as $weekday): ?>
                            <label for="mec_settings_weekends_<?php echo $weekday[0]; ?>">
                                <input type="checkbox" id="mec_settings_weekends_<?php echo $weekday[0]; ?>" name="mec[settings][weekends][]" value="<?php echo $weekday[0]; ?>" <?php echo (in_array($weekday[0], $mec_weekends) ? 'checked="checked"' : ''); ?> />
                                <?php echo $weekday[1]; ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="mec-step-wizard-content" data-step="2">
                <form id="mec_save_slug_form">
                    <div class="mec-form-row">
                        <label class="mec-col-2" for="mec_settings_archive_title"><?php _e('Archive Page Title', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
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
                        <label class="mec-col-2" for="mec_settings_default_skin_archive"><?php _e('Archive Page Skin', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4 tooltip-move-up">
                            <select id="mec_settings_default_skin_archive" name="mec[settings][default_skin_archive]" onchange="mec_archive_skin_style_changed(this.value);" style="margin-bottom: 8px;">
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
                            <span class="mec-archive-skins mec-archive-monthly_view-skins" style="display: inline-block;">
                                <select id="mec_settings_monthly_view_skin_archive" name="mec[settings][monthly_view_archive_skin]" style="    min-width: 225px;">
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
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-2" for="mec_settings_slug"><?php _e('Main Slug', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
                            <input type="text" id="mec_settings_slug" name="mec[settings][slug]" value="<?php echo ((isset($settings['slug']) and trim($settings['slug']) != '') ? $settings['slug'] : 'events'); ?>" />
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
                        <label class="mec-col-2" for="mec_settings_single_event_single_style"><?php _e('Single Event Style', 'modern-events-calendar-lite'); ?></label>
                        <div class="mec-col-4">
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
                </form>
            </div>
            <div class="mec-step-wizard-content" data-step="3">
                <form id="mec_save_module_form">
                    <div class="mec-form-row">
                        <label style="display: block;">
                            <input type="hidden" name="mec[settings][countdown_status]" value="0" />
                            <input onchange="jQuery('#mec_count_down_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][countdown_status]" <?php if(isset($settings['countdown_status']) and $settings['countdown_status']) echo 'checked="checked"'; ?> /> <?php _e('Show countdown module on event page', 'modern-events-calendar-lite'); ?>
                        </label>
                        <div id="mec_count_down_container_toggle" class="mec-col-6 <?php if((isset($settings['countdown_status']) and !$settings['countdown_status']) or !isset($settings['countdown_status'])) echo 'mec-util-hidden'; ?>">
                            <div class="mec-form-row">
                                <label class="mec-col-4" for="mec_settings_countdown_list"><?php _e('Countdown Style', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-4">
                                    <select id="mec_settings_countdown_list" name="mec[settings][countdown_list]">
                                        <option value="default" <?php echo ((isset($settings['countdown_list']) and $settings['countdown_list'] == "default") ? 'selected="selected"' : ''); ?> ><?php _e('Plain Style', 'modern-events-calendar-lite'); ?></option>
                                        <option value="flip" <?php echo ((isset($settings['countdown_list']) and $settings['countdown_list'] == "flip") ? 'selected="selected"' : ''); ?> ><?php _e('Flip Style', 'modern-events-calendar-lite'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mec-form-row">
                        <label>
                            <input type="hidden" name="mec[settings][related_events]" value="0" />
                            <input onchange="jQuery('#mec_related_events_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][related_events]" <?php if(isset($settings['related_events']) and $settings['related_events']) echo 'checked="checked"'; ?> /> <?php _e('Display related events based on taxonomy in single event page.', 'modern-events-calendar-lite'); ?>
                        </label>
                        <div id="mec_related_events_container_toggle" class="mec-col-8 <?php if((isset($settings['related_events']) and !$settings['related_events']) or !isset($settings['related_events'])) echo 'mec-util-hidden'; ?>">
                            <div class="mec-form-row" style="margin-top:20px;">
                                <label style="margin-right:7px;" for="mec_settings_countdown_list"><?php _e('Select Taxonomies:', 'modern-events-calendar-lite'); ?></label>
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
                                <?php if(isset($settings['speakers_status']) and $settings['speakers_status']) : ?>
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
                        </div>
                    </div>
                    
                    <div class="mec-form-row">
                        <label>
                            <input type="hidden" name="mec[settings][next_previous_events]" value="0" />
                            <input onchange="jQuery('#mec_next_previous_events_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][next_previous_events]" <?php if(isset($settings['next_previous_events']) and $settings['next_previous_events']) echo 'checked="checked"'; ?> /> <?php _e('Display next / previous events based on taxonomy in single event page.', 'modern-events-calendar-lite'); ?>
                        </label>
                        <div id="mec_next_previous_events_container_toggle" class="mec-col-8 <?php if((isset($settings['next_previous_events']) and !$settings['next_previous_events']) or !isset($settings['next_previous_events'])) echo 'mec-util-hidden'; ?>">

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

                </form>
            </div>
            <div class="mec-step-wizard-content" data-step="4">
                <form id="mec_save_single_form">
                    <div class="mec-form-row">
                        <div class="mec-col-12">
                            <label for="mec_settings_speakers_status">
                                <input type="hidden" name="mec[settings][speakers_status]" value="0" />
                                <input type="checkbox" name="mec[settings][speakers_status]" id="mec_settings_speakers_status" <?php echo ((isset($settings['speakers_status']) and $settings['speakers_status']) ? 'checked="checked"' : ''); ?> value="1" />
                                <?php _e('Enable speakers feature', 'modern-events-calendar-lite'); ?>
                                <span class="mec-tooltip">
                                    <div class="box">
                                        <h5 class="title"><?php _e('Speakers', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("Enable this option to have speaker in Hourly Schedule in Single. Refresh after enabling it to see the Speakers menu under MEC dashboard.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/speaker/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>                                        
                            </label>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <label class="mec-col-8">
                            <input type="hidden" name="mec[settings][export_module_status]" value="0" />
                            <input onchange="jQuery('#mec_export_module_options_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][export_module_status]" <?php if(isset($settings['export_module_status']) and $settings['export_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Show export module (iCal export and add to Google calendars) on event page', 'modern-events-calendar-lite'); ?>
                        </label>
                    </div>
                    <div class="mec-form-row">
                        <div id="mec_export_module_options_container_toggle" class="<?php if((isset($settings['export_module_status']) and !$settings['export_module_status']) or !isset($settings['export_module_status'])) echo 'mec-util-hidden'; ?>">
                            <div class="mec-form-row">
                                <ul id="mec_export_module_options" class="mec-form-row">
                                    <?php
                                    $event_options = array('googlecal'=>__('Google Calendar', 'modern-events-calendar-lite'), 'ical'=>__('iCal', 'modern-events-calendar-lite'));
                                    foreach($event_options as $event_key=>$event_option): ?>
                                    <li id="mec_sn_<?php echo esc_attr($event_key); ?>" data-id="<?php echo esc_attr($event_key); ?>" class="mec-form-row mec-switcher <?php echo ((isset($settings['sn'][$event_key]) and $settings['sn'][$event_key]) ? 'mec-enabled' : 'mec-disabled'); ?>">
                                        <label class="mec-col-3"><?php echo esc_html($event_option); ?></label>
                                        <div class="mec-col-9">
                                            <input class="mec-status" type="hidden" name="mec[settings][sn][<?php echo esc_attr($event_key); ?>]" value="<?php echo (isset($settings['sn'][$event_key]) ? $settings['sn'][$event_key] : '1'); ?>" />
                                            <label for="mec[settings][sn][<?php echo esc_attr($event_key); ?>]"></label>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div> 
                    </div> 
                </form>
            </div>
            <?php if($this->getPRO()) : ?>
            <div class="mec-step-wizard-content" data-step="5">
                <form id="mec_save_booking_form">
                    <div class="mec-form-row">
                        <label>
                            <input type="hidden" name="mec[settings][booking_status]" value="0" />
                            <input onchange="jQuery('#mec_booking_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][booking_status]" <?php if(isset($settings['booking_status']) and $settings['booking_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable booking module', 'modern-events-calendar-lite'); ?>
                        </label>
                    </div>
                    <div id="mec_booking_container_toggle" class="<?php if((isset($settings['booking_status']) and !$settings['booking_status']) or !isset($settings['booking_status'])) echo 'mec-util-hidden'; ?>">
                        <div class="mec-form-row">
                            <label class="mec-col-2" for="mec_settings_booking_date_selection"><?php _e('Date Selection', 'modern-events-calendar-lite'); ?></label>
                            <div class="mec-col-4">
                                <select id="mec_settings_booking_date_selection" name="mec[settings][booking_date_selection]">
                                    <option value="dropdown" <?php echo ((!isset($settings['booking_date_selection']) or (isset($settings['booking_date_selection']) and $settings['booking_date_selection'] == 'dropdown')) ? 'selected="selected"' : ''); ?>><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></option>
                                    <option value="calendar" <?php echo ((isset($settings['booking_date_selection']) and $settings['booking_date_selection'] == 'calendar') ? 'selected="selected"' : ''); ?>><?php _e('Calendar', 'modern-events-calendar-lite'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="mec-form-row">
                            <label class="mec-col-2" for="mec_settings_booking_registration"><?php _e('Registration', 'modern-events-calendar-lite'); ?></label>
                            <div class="mec-col-4">
                                <select id="mec_settings_booking_registration" name="mec[settings][booking_registration]">
                                    <option <?php echo ((isset($settings['booking_registration']) and $settings['booking_registration'] == '1') ? 'selected="selected"' : ''); ?> value="1"><?php echo esc_html__('Enabled', 'modern-events-calendar-lite'); ?></option>
                                    <option <?php echo ((isset($settings['booking_registration']) and $settings['booking_registration'] == '0') ? 'selected="selected"' : ''); ?> value="0"><?php echo esc_html__('Disabled', 'modern-events-calendar-lite'); ?></option>
                                </select>
                                <span class="mec-tooltip">
                                    <div class="box left">
                                        <h5 class="title"><?php _e('Registration', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("If enabled MEC would create a WordPress User for main attendees. It's recommended to keep it enabled.", 'modern-events-calendar-lite'); ?></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mec-form-row">
                            <div class="mec-col-12">
                                <label for="mec_settings_booking_auto_verify_free">
                                    <input type="hidden" name="mec[settings][booking_auto_verify_free]" value="0" />
                                    <input type="checkbox" name="mec[settings][booking_auto_verify_free]" id="mec_settings_booking_auto_verify_free" <?php echo ((isset($settings['booking_auto_verify_free']) and $settings['booking_auto_verify_free'] == '1') ? 'checked="checked"' : ''); ?> value="1" />
                                    <?php _e('Auto verification for free bookings', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="mec-form-row">
                            <div class="mec-col-12">
                                <label for="mec_settings_booking_auto_verify_paid">
                                    <input type="hidden" name="mec[settings][booking_auto_verify_paid]" value="0" />
                                    <input type="checkbox" name="mec[settings][booking_auto_verify_paid]" id="mec_settings_booking_auto_verify_paid" <?php echo ((isset($settings['booking_auto_verify_paid']) and $settings['booking_auto_verify_paid'] == '1') ? 'checked="checked"' : ''); ?> value="1" />
                                    <?php _e('Auto verification for paid bookings', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="mec-form-row">
                            <div class="mec-col-12">
                                <label for="mec_settings_booking_auto_confirm_free">
                                    <input type="hidden" name="mec[settings][booking_auto_confirm_free]" value="0" />
                                    <input type="checkbox" name="mec[settings][booking_auto_confirm_free]" id="mec_settings_booking_auto_confirm_free" <?php echo ((isset($settings['booking_auto_confirm_free']) and $settings['booking_auto_confirm_free'] == '1') ? 'checked="checked"' : ''); ?> value="1" />
                                    <?php _e('Auto confirmation for free bookings', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="mec-form-row">
                            <div class="mec-col-12">
                                <label for="mec_settings_booking_auto_confirm_paid">
                                    <input type="hidden" name="mec[settings][booking_auto_confirm_paid]" value="0" />
                                    <input type="checkbox" name="mec[settings][booking_auto_confirm_paid]" id="mec_settings_booking_auto_confirm_paid" <?php echo ((isset($settings['booking_auto_confirm_paid']) and $settings['booking_auto_confirm_paid'] == '1') ? 'checked="checked"' : ''); ?> value="1" />
                                    <?php _e('Auto confirmation for paid bookings', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="mec-step-wizard-content" data-step="6">
                <form id="mec_save_styling_form">
                    <div class="mec-form-row">
                        <div class="mec-col-3">
                            <span><?php esc_html_e('Custom Color Skin', 'modern-events-calendar-lite' ); ?></span>
                        </div>
                        <div class="mec-col-6">
                            <input type="text" class="wp-color-picker-field" id="mec_settings_color" name="mec[styling][color]" value="<?php echo (isset($styling['color']) ? $styling['color'] : ''); ?>" data-default-color="" />
                        </div>
                        <div class="mec-col-12">
                            <p><?php esc_attr_e("If you want to select a predefined color skin, you must clear the color of this item", 'modern-events-calendar-lite'); ?></p>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-12">
                            <span><?php esc_html_e('Predefined Color Skin', 'modern-events-calendar-lite' ); ?></span>
                        </div>
                        <div class="mec-col-6">
                            <ul class="mec-image-select-wrap">
                                <?php
                                $colorskins = array(
                                    '#40d9f1'=>'mec-colorskin-1',
                                    '#0093d0'=>'mec-colorskin-2',
                                    '#e53f51'=>'mec-colorskin-3',
                                    '#f1c40f'=>'mec-colorskin-4',
                                    '#e64883'=>'mec-colorskin-5',
                                    '#45ab48'=>'mec-colorskin-6',
                                    '#9661ab'=>'mec-colorskin-7',
                                    '#0aad80'=>'mec-colorskin-8',
                                    '#0ab1f0'=>'mec-colorskin-9',
                                    '#ff5a00'=>'mec-colorskin-10',
                                    '#c3512f'=>'mec-colorskin-11',
                                    '#55606e'=>'mec-colorskin-12',
                                    '#fe8178'=>'mec-colorskin-13',
                                    '#7c6853'=>'mec-colorskin-14',
                                    '#bed431'=>'mec-colorskin-15',
                                    '#2d5c88'=>'mec-colorskin-16',
                                    '#77da55'=>'mec-colorskin-17',
                                    '#2997ab'=>'mec-colorskin-18',
                                    '#734854'=>'mec-colorskin-19',
                                    '#a81010'=>'mec-colorskin-20',
                                    '#4ccfad'=>'mec-colorskin-21',
                                    '#3a609f'=>'mec-colorskin-22',
                                    '#333333'=>'mec-colorskin-23',
                                    '#D2D2D2'=>'mec-colorskin-24',
                                    '#636363'=>'mec-colorskin-25',
                                    );

                                    foreach($colorskins as $colorskin=>$values): ?>
                                    <li class="mec-image-select">
                                        <label for="<?php echo $values; ?>">
                                            <input type="radio" id="<?php echo $values; ?>" name="mec[styling][mec_colorskin]" value="<?php echo $colorskin; ?>" <?php if(isset($styling['mec_colorskin']) && ($styling['mec_colorskin'] == $colorskin)) echo 'checked="checked"'; ?>>
                                            <span class="<?php echo $values; ?>"></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="mec-step-wizard-content" data-step="5">
                <form id="mec_save_styling_form">
                    <div class="mec-form-row">
                        <div class="mec-col-3">
                            <span><?php esc_html_e('Custom Color Skin', 'modern-events-calendar-lite' ); ?></span>
                        </div>
                        <div class="mec-col-6">
                            <input type="text" class="wp-color-picker-field" id="mec_settings_color" name="mec[styling][color]" value="<?php echo (isset($styling['color']) ? $styling['color'] : ''); ?>" data-default-color="" />
                        </div>
                        <div class="mec-col-6">
                            <p><?php esc_attr_e("If you want to select a predefined color skin, you must clear the color of this item", 'modern-events-calendar-lite'); ?></p>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-3">
                            <span><?php esc_html_e('Predefined Color Skin', 'modern-events-calendar-lite' ); ?></span>
                        </div>
                        <div class="mec-col-6">
                            <ul class="mec-image-select-wrap">
                                <?php
                                $colorskins = array(
                                    '#40d9f1'=>'mec-colorskin-1',
                                    '#0093d0'=>'mec-colorskin-2',
                                    '#e53f51'=>'mec-colorskin-3',
                                    '#f1c40f'=>'mec-colorskin-4',
                                    '#e64883'=>'mec-colorskin-5',
                                    '#45ab48'=>'mec-colorskin-6',
                                    '#9661ab'=>'mec-colorskin-7',
                                    '#0aad80'=>'mec-colorskin-8',
                                    '#0ab1f0'=>'mec-colorskin-9',
                                    '#ff5a00'=>'mec-colorskin-10',
                                    '#c3512f'=>'mec-colorskin-11',
                                    '#55606e'=>'mec-colorskin-12',
                                    '#fe8178'=>'mec-colorskin-13',
                                    '#7c6853'=>'mec-colorskin-14',
                                    '#bed431'=>'mec-colorskin-15',
                                    '#2d5c88'=>'mec-colorskin-16',
                                    '#77da55'=>'mec-colorskin-17',
                                    '#2997ab'=>'mec-colorskin-18',
                                    '#734854'=>'mec-colorskin-19',
                                    '#a81010'=>'mec-colorskin-20',
                                    '#4ccfad'=>'mec-colorskin-21',
                                    '#3a609f'=>'mec-colorskin-22',
                                    '#333333'=>'mec-colorskin-23',
                                    '#D2D2D2'=>'mec-colorskin-24',
                                    '#636363'=>'mec-colorskin-25',
                                    );

                                    foreach($colorskins as $colorskin=>$values): ?>
                                    <li class="mec-image-select">
                                        <label for="<?php echo $values; ?>">
                                            <input type="radio" id="<?php echo $values; ?>" name="mec[styling][mec_colorskin]" value="<?php echo $colorskin; ?>" <?php if(isset($styling['mec_colorskin']) && ($styling['mec_colorskin'] == $colorskin)) echo 'checked="checked"'; ?>>
                                            <span class="<?php echo $values; ?>"></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            <div class="mec-next-previous-buttons">
                <button class="mec-button-prev  mec-hide-button"><?php _e('Prev', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-prev-icon.svg'; ?>" /></button>
                <a class="mec-button-dashboard mec-hide-button" href="<?php echo admin_url('/admin.php?page=mec-intro'); ?>"><?php _e('Go to Dashboard', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-next-icon.svg'; ?>" /></a>
                <button class="mec-button-skip"><?php _e('Next', 'modern-events-calendar-lite'); ?><img src="<?php echo plugin_dir_url(__FILE__ ) . '../../../assets/img/popup/popup-next-icon.svg'; ?>" /></button>
                <button class="mec-button-next"><?php _e('Save', 'modern-events-calendar-lite'); ?><svg height="12pt" viewBox="0 0 512 512" width="12pt" xmlns="http://www.w3.org/2000/svg"><path d="m453.332031 512h-394.664062c-32.363281 0-58.667969-26.304688-58.667969-58.667969v-394.664062c0-32.363281 26.304688-58.667969 58.667969-58.667969h330.835937c21.054688 0 41.683594 8.535156 56.554688 23.445312l42.496094 42.496094c15.125 15.125 23.445312 35.222656 23.445312 56.574219v330.816406c0 32.363281-26.304688 58.667969-58.667969 58.667969zm-394.664062-480c-14.699219 0-26.667969 11.96875-26.667969 26.667969v394.664062c0 14.699219 11.96875 26.667969 26.667969 26.667969h394.664062c14.699219 0 26.667969-11.96875 26.667969-26.667969v-330.816406c0-12.820313-4.992188-24.871094-14.058594-33.941406l-42.496094-42.496094c-8.9375-8.957031-21.289062-14.078125-33.941406-14.078125zm0 0"/><path d="m325.332031 149.332031h-224c-8.832031 0-16-7.167969-16-16v-117.332031c0-8.832031 7.167969-16 16-16s16 7.167969 16 16v101.332031h192v-101.332031c0-8.832031 7.167969-16 16-16s16 7.167969 16 16v117.332031c0 8.832031-7.167969 16-16 16zm0 0"/><path d="m256 416c-52.929688 0-96-43.070312-96-96s43.070312-96 96-96 96 43.070312 96 96-43.070312 96-96 96zm0-160c-35.285156 0-64 28.714844-64 64s28.714844 64 64 64 64-28.714844 64-64-28.714844-64-64-64zm0 0"/></svg></button>
            </div>
        </div>
    </div>
</div>
<script>

jQuery(document).ready(function($)
{
    
    function import_loading($value){
        if ($value) {
            jQuery('.mec-wizard-wrap .mec-wizard-loading').show()
        } else {
            jQuery('.mec-wizard-wrap .mec-wizard-loading').hide()
        }
    }
    function save_loading($value){
        if ($value) {
            jQuery('.mec-setup-wizard-wrap .mec-wizard-loading').show()
        } else {
            jQuery('.mec-setup-wizard-wrap .mec-wizard-loading').hide()
        }
    }

    var $setting_wrap = jQuery(".mec-setup-wizard-wrap");
    var $se_prev = $setting_wrap.find('.mec-button-prev');
    var $se_next = $setting_wrap.find('.mec-button-next');

    $se_next.on("click", function(e){
        e.preventDefault();
        var active_step = jQuery(".mec-step-wizard-content.mec-active-step").attr("data-step")
        var next_step = Number(active_step) + 1;

        
        
        <?php if($this->getPRO()) : ?>
        if ( active_step !== '6' ) {
            $se_prev.removeClass("mec-hide-button");
            jQuery(".mec-step-wizard-content").removeClass("mec-active-step");
            jQuery(".mec-step-wizard-content[data-step=" + next_step + "]").addClass("mec-active-step");
            jQuery(".mec-step-" + next_step ).addClass("mec-step-passed");
        }
        if ( next_step == 6 ) {
            jQuery(".mec-button-skip").addClass("mec-hide-button");
        }
        // if ( next_step == 6 ) {
        //     jQuery(".mec-button-dashboard").removeClass("mec-hide-button");
        // }
        <?php else: ?>
        if ( active_step !== '5' ) {
            $se_prev.removeClass("mec-hide-button");
            jQuery(".mec-step-wizard-content").removeClass("mec-active-step");
            jQuery(".mec-step-wizard-content[data-step=" + next_step + "]").addClass("mec-active-step");
            jQuery(".mec-step-" + next_step ).addClass("mec-step-passed");
        }
        if ( next_step == 5 ) {
            jQuery(".mec-button-skip").addClass("mec-hide-button");
        }
        // if ( next_step == 5 ) {
        //     jQuery(".mec-button-dashboard").removeClass("mec-hide-button");
        // }
        <?php endif; ?>

        if ( active_step === '1' ) {
            save_step_1();
        }
        if ( active_step === '2' ) {
            save_step_2();
        }
        if ( active_step === '3' ) {
            save_step_3();
        }
        if ( active_step === '4' ) {
            save_step_4();
        }
        if ( active_step === '5' ) {
            save_step_5();
        }
        if ( active_step === '6' ) {
            save_step_6();
        }


    });

    jQuery(".mec-button-skip").on("click", function(e){
        e.preventDefault();
        var active_step = jQuery(".mec-step-wizard-content.mec-active-step").attr("data-step")
        var next_step = Number(active_step) + 1;
        $se_prev.removeClass("mec-hide-button");
        jQuery(".mec-step-wizard-content").removeClass("mec-active-step");
        jQuery(".mec-step-wizard-content[data-step=" + next_step + "]").addClass("mec-active-step");
        jQuery(".mec-step-" + next_step ).addClass("mec-step-passed");

        <?php if($this->getPRO()) : ?>
        if ( next_step == 6 ) {
            jQuery(".mec-button-skip").addClass("mec-hide-button");
        }
        // if ( next_step == 6 ) {
        //     jQuery(".mec-button-dashboard").removeClass("mec-hide-button");
        // }
        <?php else: ?>
        if ( next_step == 5 ) {
            jQuery(".mec-button-skip").addClass("mec-hide-button");
        }
        // if ( next_step == 5 ) {
        //     jQuery(".mec-button-dashboard").removeClass("mec-hide-button");
        // }
        <?php endif; ?>
    });

    $se_prev.on("click", function(e){
        e.preventDefault();
        var active_step = jQuery(".mec-step-wizard-content.mec-active-step").attr("data-step")
        var next_step = Number(active_step) - 1;
        jQuery(".mec-step-wizard-content").removeClass("mec-active-step");
        jQuery(".mec-step-wizard-content[data-step=" + next_step + "]").addClass("mec-active-step");
        jQuery(".mec-step-" + active_step ).removeClass("mec-step-passed");
            
        <?php if($this->getPRO()) : ?>
        if ( next_step != 6 ) {
            $se_next.removeClass("mec-hide-button");
            jQuery(".mec-button-skip").removeClass("mec-hide-button");
            //jQuery(".mec-button-dashboard").addClass("mec-hide-button");
        }
        <?php else: ?>
        if ( next_step != 5 ) {
            $se_next.removeClass("mec-hide-button");
            jQuery(".mec-button-skip").removeClass("mec-hide-button");
            //jQuery(".mec-button-dashboard").addClass("mec-hide-button");
        }
        <?php endif; ?>

        if ( next_step == 1 ) {
            $se_prev.addClass("mec-hide-button");
        }
    });

    jQuery(".mec-button-import-events").click(function(){
        if(confirm("Are you sure you want to import events?")){
            jQuery.ajax(
            {
                type: "POST",
                url: ajaxurl,
                data: "action=wizard_import_dummy_events",
                beforeSend: function () {
                    import_loading(true)
                },
                success: function(data)
                {
                    
                    import_loading(false)
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log('error');
                }
            });
        }
        else{
            return false;
        }
    });

    jQuery(".mec-button-import-shortcodes").click(function(){

        if(confirm("Are you sure you want to import shortcodes?")){
            jQuery.ajax(
            {
                type: "POST",
                url: ajaxurl,
                data: "action=wizard_import_dummy_shortcodes",
                beforeSend: function () {
                    import_loading(true)
                },
                success: function(data)
                {
                    
                    import_loading(false)
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    console.log('error');
                }
            });
        }
        else{
            return false;
        }

    });

    var archive_value = jQuery('#mec_settings_default_skin_archive').val();
    function mec_archive_skin_style_changed(archive_value)
    {
        jQuery('.mec-archive-skins').hide();
        jQuery('.mec-archive-skins.mec-archive-'+archive_value+'-skins').show();
    }
    mec_archive_skin_style_changed(archive_value);

    jQuery(document).ready(function()
    {
        //Initiate Color Picker
        jQuery('.wp-color-picker-field').wpColorPicker();
        
    });

    function save_step_1() {
        var settings = jQuery("#mec_save_weekdays_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_weekdays&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                save_loading(false)
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }

    function save_step_2() {
        var settings = jQuery("#mec_save_slug_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_slug&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                save_loading(false)
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }

    function save_step_3() {
        var settings = jQuery("#mec_save_module_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_module&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                save_loading(false)
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }

    function save_step_4() {
        var settings = jQuery("#mec_save_single_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_single&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                save_loading(false)
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }

    
    function save_step_5() {
        var settings = jQuery("#mec_save_booking_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_booking&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                save_loading(false)
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }
    
    
    function save_step_6() {
        var settings = jQuery("#mec_save_styling_form").serialize();
        jQuery.ajax(
        {
            type: "POST",
            url: ajaxurl,
            data: "action=wizard_save_styling&"+settings,
            beforeSend: function () {
                save_loading(true)
            },
            success: function(data)
            {
                window.location.replace('<?php echo admin_url('/admin.php?page=mec-intro'); ?>')
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log("error");
            }
        });
    }

    
    jQuery('.mec-wizard-open-popup.mec-settings').on('click', function(e)
    {
        e.preventDefault();

        if(jQuery(".mec-wizard-open-popup.mec-settings").length > 0 ) 
        {
            jQuery(".mec-wizard-open-popup.mec-settings").addClass("active")
            jQuery(".mec-wizard-open-popup.add-event").removeClass("active")
            jQuery(".mec-wizard-open-popup.add-shortcode").removeClass("active")
            jQuery(".mec-wizard-starter-video a").removeClass("active")
        }

        // Open Lightbox
        lity('.mec-setup-wizard-wrap');
    });

    jQuery(document).on('lity:open', function(event, instance)
    {
        if ( jQuery(".mec-wizard-open-popup.mec-settings").hasClass("active") ) {
            jQuery('.lity').addClass('mec-settings');
        }

        if ( jQuery(".mec-wizard-starter-video a").hasClass("active") ) {
            jQuery('.lity').addClass('wizard-video');
        }
    });

    jQuery('.mec-wizard-starter-video a').on('click', function(e)
    {
        e.preventDefault();

        if(jQuery(".mec-wizard-starter-video a").length > 0 ) 
        {
            jQuery(".mec-wizard-starter-video a").addClass("active")
            jQuery(".mec-wizard-open-popup.mec-settings").removeClass("active")
            jQuery(".mec-wizard-open-popup.add-event").removeClass("active")
            jQuery(".mec-wizard-open-popup.add-shortcode").removeClass("active")
        }

        // Open Lightbox
        lity('https://www.youtube.com/embed/FV_X341oyiw');
    });
});
</script>