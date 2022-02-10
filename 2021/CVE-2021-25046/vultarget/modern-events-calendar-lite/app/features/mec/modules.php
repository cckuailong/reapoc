<?php
/** no direct access **/
defined('MECEXEC') or die();

$settings = $this->main->get_settings();
$socials = $this->main->get_social_networks();

// WordPress Pages
$pages = get_pages();
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
        <?php $this->main->get_sidebar_menu('modules'); ?>
    </div>

    <div class="wns-be-main">
        <div id="wns-be-notification"></div>
        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">

                    <form id="mec_modules_form">

                        <div id="speakers_option" class="mec-options-fields active">

                            <h4 class="mec-form-subtitle"><?php _e('Speakers', 'modern-events-calendar-lite'); ?></h4>
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
                                    <p><?php esc_attr_e("After enabling and saving the settings, you should reload the page to see a new menu on the Dashboard > MEC", 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>

                        </div>

                        <?php if($this->main->getPRO()): ?>

                            <div id="googlemap_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Map', 'modern-events-calendar-lite'); ?></h4>
                                <?php if(!$this->main->getPRO()): ?>
                                <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                                <?php else: ?>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][google_maps_status]" value="0" />
                                        <input onchange="jQuery('#mec_google_maps_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][google_maps_status]" <?php if(isset($settings['google_maps_status']) and $settings['google_maps_status']) echo 'checked="checked"'; ?> /> <?php _e('Show Map on event page', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_google_maps_container_toggle" class="<?php if((isset($settings['google_maps_status']) and !$settings['google_maps_status']) or !isset($settings['google_maps_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_google_maps_api_key"><?php _e('Google Maps API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_google_maps_api_key" name="mec[settings][google_maps_api_key]" value="<?php echo ((isset($settings['google_maps_api_key']) and trim($settings['google_maps_api_key']) != '') ? $settings['google_maps_api_key'] : ''); ?>" />
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('Google Map Options', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e("Required!", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/google-maps-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3"><?php _e('Zoom level', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][google_maps_zoomlevel]">
                                                <?php for($i = 5; $i <= 21; $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php if(isset($settings['google_maps_zoomlevel']) and $settings['google_maps_zoomlevel'] == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                            <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Zoom level', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e("For Google Maps module in single event page. In Google Maps skin, it will calculate the zoom level automatically based on event boundaries.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/google-maps-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3"><?php _e('Google Maps Style', 'modern-events-calendar-lite'); ?></label>
                                        <?php $styles = $this->main->get_googlemap_styles(); ?>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][google_maps_style]">
                                                <option value=""><?php _e('Default', 'modern-events-calendar-lite'); ?></option>
                                                <?php foreach($styles as $style): ?>
                                                    <option value="<?php echo $style['key']; ?>" <?php if(isset($settings['google_maps_style']) and $settings['google_maps_style'] == $style['key']) echo 'selected="selected"'; ?>><?php echo $style['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3"><?php _e('Direction on single event', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <select name="mec[settings][google_maps_get_direction_status]">
                                                <option value="0"><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                                                <option value="1" <?php if(isset($settings['google_maps_get_direction_status']) and $settings['google_maps_get_direction_status'] == 1) echo 'selected="selected"'; ?>><?php _e('Simple Method', 'modern-events-calendar-lite'); ?></option>
                                                <option value="2" <?php if(isset($settings['google_maps_get_direction_status']) and $settings['google_maps_get_direction_status'] == 2) echo 'selected="selected"'; ?>><?php _e('Advanced Method', 'modern-events-calendar-lite'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_google_maps_date_format1"><?php _e('Lightbox Date Format', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" id="mec_settings_google_maps_date_format1" name="mec[settings][google_maps_date_format1]" value="<?php echo ((isset($settings['google_maps_date_format1']) and trim($settings['google_maps_date_format1']) != '') ? $settings['google_maps_date_format1'] : 'M d Y'); ?>" />
                                            <span class="mec-tooltip">
                                                <div class="box left">
                                                    <h5 class="title"><?php _e('Lightbox Date Format', 'modern-events-calendar-lite'); ?></h5>
                                                    <div class="content"><p><?php esc_attr_e("Default value is M d Y", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/google-maps-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                                </div>
                                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3"><?php _e('Google Maps API', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <label>
                                                <input type="hidden" name="mec[settings][google_maps_dont_load_api]" value="0" />
                                                <input value="1" type="checkbox" name="mec[settings][google_maps_dont_load_api]" <?php if(isset($settings['google_maps_dont_load_api']) and $settings['google_maps_dont_load_api']) echo 'checked="checked"'; ?> /> <?php _e("Don't load Google Maps API library", 'modern-events-calendar-lite'); ?>
                                            </label>
                                            <span class="mec-tooltip">
                                            <div class="box top left">
                                                <h5 class="title"><?php _e('Google Maps API', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e("Check only if another plugin/theme is loading the Google Maps API", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/google-maps-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3"><?php _e('Fullscreen Button', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <label>
                                                <input type="hidden" name="mec[settings][google_maps_fullscreen_button]" value="0" />
                                                <input value="1" type="checkbox" name="mec[settings][google_maps_fullscreen_button]" <?php if(isset($settings['google_maps_fullscreen_button']) and $settings['google_maps_fullscreen_button']) echo 'checked="checked"'; ?> /> <?php _e("Enabled", 'modern-events-calendar-lite'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php do_action('mec_map_options_after', $settings); ?>
                                </div>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>

                        <div id="export_module_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Export', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][export_module_status]" value="0" />
                                    <input onchange="jQuery('#mec_export_module_options_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][export_module_status]" <?php if(isset($settings['export_module_status']) and $settings['export_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Show export module (iCal export and add to Google calendars) on event page', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
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
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][export_module_hide_expired]" value="0" />
                                    <input value="1" type="checkbox" name="mec[settings][export_module_hide_expired]" <?php if(isset($settings['export_module_hide_expired']) and $settings['export_module_hide_expired']) echo 'checked="checked"'; ?> /> <?php _e('Hide for Expired Events', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                        </div>

                        <div id="time_module_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Local Time', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][local_time_module_status]" value="0" />
                                    <input onchange="jQuery('#mec_local_time_module_options_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][local_time_module_status]" <?php if(isset($settings['local_time_module_status']) and $settings['local_time_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Show event time based on local time of visitor on event page', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_local_time_module_options_container_toggle" class="<?php if((isset($settings['local_time_module_status']) and !$settings['local_time_module_status']) or !isset($settings['local_time_module_status'])) echo 'mec-util-hidden'; ?>">
                            </div>
                        </div>

                        <?php if($this->main->getPRO()): ?>

                            <div id="qrcode_module_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('QR Code', 'modern-events-calendar-lite'); ?></h4>

                                <?php if(!$this->main->getPRO()): ?>
                                <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                                <?php else: ?>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][qrcode_module_status]" value="0" />
                                        <input onchange="jQuery('#mec_qrcode_module_options_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][qrcode_module_status]" <?php if(!isset($settings['qrcode_module_status']) or (isset($settings['qrcode_module_status']) and $settings['qrcode_module_status'])) echo 'checked="checked"'; ?> /> <?php _e('Show QR code of event in details page and booking invoice', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_qrcode_module_options_container_toggle" class="<?php if((isset($settings['qrcode_module_status']) and !$settings['qrcode_module_status']) or !isset($settings['qrcode_module_status'])) echo 'mec-util-hidden'; ?>">
                                </div>
                                <?php endif; ?>

                            </div>

                            <div id="weather_module_option" class="mec-options-fields">
                                <h4 class="mec-form-subtitle"><?php _e('Weather', 'modern-events-calendar-lite'); ?></h4>
                                <?php if(!$this->main->getPRO()): ?>
                                <div class="info-msg"><?php echo sprintf(__("%s is required to use this feature.", 'modern-events-calendar-lite'), '<a href="'.$this->main->get_pro_link().'" target="_blank">'.__('Pro version of Modern Events Calendar', 'modern-events-calendar-lite').'</a>'); ?></div>
                                <?php else: ?>
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[settings][weather_module_status]" value="0" />
                                        <input onchange="jQuery('#mec_weather_module_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][weather_module_status]" <?php if(isset($settings['weather_module_status']) and $settings['weather_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Show weather module on event page', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_weather_module_container_toggle" class="<?php if((isset($settings['weather_module_status']) and !$settings['weather_module_status']) or !isset($settings['weather_module_status'])) echo 'mec-util-hidden'; ?>">
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_weather_module_wa_api_key"><?php _e('weatherapi.com API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" name="mec[settings][weather_module_wa_api_key]" id="mec_settings_weather_module_wa_api_key" value="<?php echo ((isset($settings['weather_module_wa_api_key']) and trim($settings['weather_module_wa_api_key']) != '') ? $settings['weather_module_wa_api_key'] : ''); ?>">
                                            <p><?php echo sprintf(__('You can get a free one at %s', 'modern-events-calendar-lite'), '<a href="https://www.weatherapi.com/signup.aspx" target="_blank">weatherapi.com</a>'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label class="mec-col-3" for="mec_settings_weather_module_api_key"><?php _e('darksky.net API Key', 'modern-events-calendar-lite'); ?></label>
                                        <div class="mec-col-9">
                                            <input type="text" name="mec[settings][weather_module_api_key]" id="mec_settings_weather_module_api_key" value="<?php echo ((isset($settings['weather_module_api_key']) and trim($settings['weather_module_api_key']) != '') ? $settings['weather_module_api_key'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="mec-form-row">
                                        <label>
                                            <input type="hidden" name="mec[settings][weather_module_imperial_units]" value="0" />
                                            <input value="1" type="checkbox" name="mec[settings][weather_module_imperial_units]" <?php if(isset($settings['weather_module_imperial_units']) and $settings['weather_module_imperial_units']) echo 'checked="checked"'; ?> /> <?php _e('Show weather imperial units', 'modern-events-calendar-lite'); ?>
                                        </label>
                                    </div>
                                    <div class="mec-form-row">
                                        <label>
                                            <input type="hidden" name="mec[settings][weather_module_change_units_button]" value="0" />
                                            <input value="1" type="checkbox" name="mec[settings][weather_module_change_units_button]" <?php if(isset($settings['weather_module_change_units_button']) and $settings['weather_module_change_units_button']) echo 'checked="checked"'; ?> /> <?php _e('Show weather change units button', 'modern-events-calendar-lite'); ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>

                        <div id="social_options" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Social Networks', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][social_network_status]" value="0" />
                                    <input onchange="jQuery('#mec_social_network_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][social_network_status]" <?php if(isset($settings['social_network_status']) and $settings['social_network_status']) echo 'checked="checked"'; ?> /> <?php _e('Show social network module', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_social_network_container_toggle" class="<?php if((isset($settings['social_network_status']) and !$settings['social_network_status']) or !isset($settings['social_network_status'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <ul id="mec_social_networks" class="mec-form-row">
                                        <?php foreach($socials as $social): ?>
                                            <li id="mec_sn_<?php echo esc_attr($social['id']); ?>" data-id="<?php echo esc_attr($social['id']); ?>" class="mec-form-row mec-switcher <?php echo ((isset($settings['sn'][$social['id']]) and $settings['sn'][$social['id']]) ? 'mec-enabled' : 'mec-disabled'); ?>">
                                                <label class="mec-col-3"><?php echo esc_html($social['name']); ?></label>
                                                <div class="mec-col-9">
                                                    <?php if ($social['id'] == 'vk' || $social['id'] == 'tumblr' ||  $social['id'] == 'pinterest' || $social['id'] == 'flipboard' || $social['id'] == 'pocket' || $social['id'] == 'reddit' || $social['id'] == 'whatsapp' || $social['id'] == 'telegram')  : ?>
                                                    <input class="mec-status" type="hidden" name="mec[settings][sn][<?php echo esc_attr($social['id']); ?>]" value="<?php echo (isset($settings['sn'][$social['id']]) ? $settings['sn'][$social['id']] : '0'); ?>" />
                                                    <label for="mec[settings][sn][<?php echo esc_attr($social['id']); ?>]"></label>
                                                    <?php else : ?>
                                                    <input class="mec-status" type="hidden" name="mec[settings][sn][<?php echo esc_attr($social['id']); ?>]" value="<?php echo (isset($settings['sn'][$social['id']]) ? $settings['sn'][$social['id']] : '1'); ?>" />
                                                    <label for="mec[settings][sn][<?php echo esc_attr($social['id']); ?>]"></label>
                                                    <?php endif; ?>    
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div id="next_event_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Next Event', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][next_event_module_status]" value="0" />
                                    <input onchange="jQuery('#mec_next_previous_event_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][next_event_module_status]" <?php if(isset($settings['next_event_module_status']) and $settings['next_event_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Show next event module on event page', 'modern-events-calendar-lite'); ?>
                                </label>
                            </div>
                            <div id="mec_next_previous_event_container_toggle" class="<?php if((isset($settings['next_event_module_status']) and !$settings['next_event_module_status']) or !isset($settings['next_event_module_status'])) echo 'mec-util-hidden'; ?>">
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_next_event_module_method"><?php _e('Method', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <select id="mec_settings_next_event_module_method" name="mec[settings][next_event_module_method]">
                                            <option value="occurrence" <?php echo ((isset($settings['next_event_module_method']) and $settings['next_event_module_method'] == 'occurrence') ? 'selected="selected"' : ''); ?>><?php _e('Next Occurrence of Current Event', 'modern-events-calendar-lite'); ?></option>
                                            <option value="multiple" <?php echo ((isset($settings['next_event_module_method']) and $settings['next_event_module_method'] == 'multiple') ? 'selected="selected"' : ''); ?>><?php _e('Multiple Occurrences of Current Event', 'modern-events-calendar-lite'); ?></option>
                                            <option value="event" <?php echo ((isset($settings['next_event_module_method']) and $settings['next_event_module_method'] == 'event') ? 'selected="selected"' : ''); ?>><?php _e('Next Occurrence of Other Events', 'modern-events-calendar-lite'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mec-form-row" id="mec_settings_next_event_module_multiple_count_wrapper" style="<?php echo ((isset($settings['next_event_module_method']) and $settings['next_event_module_method'] == 'multiple') ? '' : 'display: none;'); ?>">
                                    <label class="mec-col-3" for="mec_settings_next_event_module_multiple_count"><?php _e('Count of Events', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <input type="number" id="mec_settings_next_event_module_multiple_count" name="mec[settings][next_event_module_multiple_count]" value="<?php echo ((isset($settings['next_event_module_multiple_count']) and trim($settings['next_event_module_multiple_count']) != '') ? $settings['next_event_module_multiple_count'] : '10'); ?>" />
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <label class="mec-col-3" for="mec_settings_next_event_module_date_format1"><?php _e('Date Format', 'modern-events-calendar-lite'); ?></label>
                                    <div class="mec-col-9">
                                        <input type="text" id="mec_settings_next_event_module_date_format1" name="mec[settings][next_event_module_date_format1]" value="<?php echo ((isset($settings['next_event_module_date_format1']) and trim($settings['next_event_module_date_format1']) != '') ? $settings['next_event_module_date_format1'] : 'M d Y'); ?>" />
                                        <span class="mec-tooltip">
                                            <div class="box left">
                                                <h5 class="title"><?php _e('Date Format', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content"><p><?php esc_attr_e("Default is M d Y", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/next-event-module/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($this->getPRO()): ?>
                        <div id="auto_emails_option" class="mec-options-fields">
                            <h4 class="mec-form-subtitle"><?php _e('Auto Emails', 'modern-events-calendar-lite'); ?></h4>
                            <div class="mec-form-row">
                                <label>
                                    <input type="hidden" name="mec[settings][auto_emails_module_status]" value="0" />
                                    <input onchange="jQuery('#mec_auto_emails_module_container_toggle').toggle();" value="1" type="checkbox" name="mec[settings][auto_emails_module_status]" <?php if(isset($settings['auto_emails_module_status']) and $settings['auto_emails_module_status']) echo 'checked="checked"'; ?> /> <?php _e('Enable Auto Emails', 'modern-events-calendar-lite'); ?>
                                </label>
                                <p><?php esc_attr_e("After enabling and saving the settings, you should reload the page to see a new menu on the Dashboard > MEC", 'modern-events-calendar-lite'); ?></p>
                            </div>
                            <div id="mec_auto_emails_module_container_toggle" class="<?php if((isset($settings['auto_emails_module_status']) and !$settings['auto_emails_module_status']) or !isset($settings['auto_emails_module_status'])) echo 'mec-util-hidden'; ?>">
                                <?php $cron = MEC_ABSPATH.'app'.DS.'crons'.DS.'auto-emails.php'; ?>
                                <p id="mec_auto_emails_cron" class="mec-col-12"><strong><?php _e('Important Note', 'modern-events-calendar-lite'); ?>: </strong><?php echo sprintf(__("Set a cronjob to call %s file by php once per minute otherwise it won't send the emails.", 'modern-events-calendar-lite'), '<code>'.$cron.'</code>'); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mec-options-fields">
                            <?php wp_nonce_field('mec_options_form'); ?>
                            <button style="display: none;" id="mec_modules_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
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
        jQuery("#mec_modules_form_button").trigger('click');
    });

    jQuery('#mec_settings_next_event_module_method').on('change', function()
    {
        var value = jQuery(this).val();
        var $wrapper = jQuery('#mec_settings_next_event_module_multiple_count_wrapper');

        if(value === 'multiple') $wrapper.show();
        else $wrapper.hide();
    });
});

jQuery("#mec_modules_form").on('submit', function(event)
{
    event.preventDefault();
    
    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    if(jQuery(".mec-purchase-verify").text() != '<?php echo esc_js(esc_attr__('Verified', 'modern-events-calendar-lite')); ?>')
    {
        jQuery(".mec-purchase-verify").text("<?php echo esc_js(esc_attr__('Checking ...', 'modern-events-calendar-lite')); ?>");
    } 
    
    var settings = jQuery("#mec_modules_form").serialize();
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