<?php
if (!defined('ABSPATH')) { exit; }
$days = array(
    "0" => "Everyday of week",
    "1" => "Sunday",
    "2" => "Monday",
    "3" => "Tuesday",
    "4" => "Wednesday",
    "5" => "Thursday",
    "6" => "Friday",
    "7" => "Saturday",
    "8" => "Sunday to Thursday",
    "9" => "Monday to Friday",
    "10" => "Weekend",
)
?>

<section class="section">
    <h1 class="section-title">
        <strong><?php esc_attr_e('Step', CHT_OPT);?> 3:</strong> <?php esc_attr_e('Triggers and targeting', CHT_OPT);?>
    </h1>

    <div class="form-horizontal">
        <div class="form-horizontal__item flex-center single-channel-setting active">
            <label class="form-horizontal__item-label" for="chaty_icons_view"><?php esc_attr_e('Icons view', CHT_OPT);?>:</label>
            <div>
                <?php
                $modes = array(
                    "vertical" => "Vertical mode",
                    "horizontal" => "Horizontal mode"
                );
                $mode = get_option('chaty_icons_view');
                $mode = empty($mode)?"vertical":$mode;
                ?>
                <select name="chaty_icons_view" id="chaty_icons_view" class="chaty-select">
                    <?php foreach ($modes as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($mode, $key); ?>><?php echo esc_attr($value); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-horizontal__item flex-center single-channel-setting active">
            <label class="form-horizontal__item-label"><?php esc_attr_e('Default state', CHT_OPT);?>:</label>
            <div>
                <?php
                $states = array(
                    "click" => "Click to open",
                    "hover" => "Hover to open",
                    "open" => "Opened by default"
                );
                $state = get_option('chaty_default_state');
                $state = empty($state)?"click":$state;
                ?>
                <select name="chaty_default_state" id="chaty_default_state" class="chaty-select">
                    <?php foreach ($states as $key => $value): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($state, $key); ?>><?php echo esc_attr($value); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-horizontal__item flex-center hide-show-button <?php echo esc_attr($state=="open"?"active":"") ?>" >
            <label class="form-horizontal__item-label"><?php esc_attr_e('Show close button', CHT_OPT);?>:</label>
            <div>
                <label class="switch">
                    <span class="switch__label"><?php esc_attr_e('Off', CHT_OPT);?></span>
                    <?php $close_button = get_option('cht_close_button'); ?>
                    <?php $close_button = empty($close_button)?"yes":$close_button; ?>
                    <input type="hidden" name="cht_close_button" value="no" >
                    <input data-gramm_editor="false" type="checkbox" id="cht_close_button" name="cht_close_button" value="yes" <?php checked($close_button, "yes") ?> >
                    <span class="switch__styled"></span>
                    <span class="switch__label"><?php esc_attr_e('On', CHT_OPT);?></span>
                </label>
            </div>
        </div>
        <div class="form-horizontal__item" id="trigger-setting">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center">Your Chaty widget will first appear to the user according to the selected trigger. After the widget appeared for the first time, it'll always be visible on-load - once the user is aware of the widget, the user expects it to always appear</span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
                <?php esc_attr_e('Trigger', CHT_OPT);?>:
            </label>
            <div class="trigger-block">
                <?php $checked = get_option('chaty_trigger_on_time') ?>
                <?php $time = get_option('chaty_trigger_time'); ?>
                <?php $time = empty($time)?"0":$time; ?>
                <?php $checked = empty($checked)?"yes":$checked; ?>
                <input type="hidden" name="chaty_trigger_on_time" value="no" >
                <div class="trigger-option-block">
                    <label class="chaty-switch" for="trigger_on_time">
                        <input type="checkbox" name="chaty_trigger_on_time" id="trigger_on_time" value="yes" <?php checked($checked, "yes") ?> >
                        <div class="chaty-slider round"></div>
                        Time Delay
                    </label>
                    <div class="trigger-block-input">
                        Display after <input type="number" id="chaty_trigger_time" name="chaty_trigger_time" value="<?php echo esc_attr($time) ?>"> seconds on the page
                    </div>
                </div>
                <?php $checked = get_option('chaty_trigger_on_exit') ?>
                <?php $time = get_option('chaty_trigger_on_exit'); ?>
                <?php $time = empty($time)?"0":$time; ?>
                <?php $checked = empty($checked)?"no":$checked; ?>
                <div class="trigger-option-block">
                    <input type="hidden" name="chaty_trigger_on_exit" value="no" >
                    <label class="chaty-switch" for="chaty_trigger_on_exit">
                        <input type="checkbox" name="chaty_trigger_on_exit" id="chaty_trigger_on_exit" value="yes" <?php checked($checked, "yes") ?> >
                        <div class="chaty-slider round"></div>
                        Exit intent
                    </label>
                    <div class="trigger-block-input">
                        Display when visitor is about to leave the page
                    </div>
                </div>
                <?php $checked = get_option('chaty_trigger_on_scroll') ?>
                <?php $time = get_option('chaty_trigger_on_page_scroll'); ?>
                <?php $time = empty($time)?"0":$time; ?>
                <?php $checked = empty($checked)?"no":$checked; ?>
                <div class="trigger-option-block">
                    <input type="hidden" name="chaty_trigger_on_scroll" value="no" >
                    <label class="chaty-switch" for="chaty_trigger_on_scroll">
                        <input type="checkbox" name="chaty_trigger_on_scroll" id="chaty_trigger_on_scroll" value="yes" <?php checked($checked, "yes") ?> >
                        <div class="chaty-slider round"></div>
                        Page Scroll
                    </label>
                    <div class="trigger-block-input">
                        Display after <input type="number" id="chaty_trigger_on_page_scroll" name="chaty_trigger_on_page_scroll" value="<?php echo esc_attr($time) ?>"> % on page
                    </div>
                </div>
            </div>
        </div>
        <div class="form-horizontal__item  flex-center" id="scroll-to-item">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center"><?php esc_html_e('Schedule the specific time and date when your Chaty widget appears.', CHT_OPT);?></span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
			    <?php esc_attr_e('Date scheduling', CHT_OPT);?>:
            </label>
		    <?php
		    $timezone = "";
		    $start_date = "";
		    $start_time = "";
		    $end_date = "";
		    $end_time = "";
		    $status = "no";
		    ?>
            <div class="chaty-option-box">
                <div id="date-schedule" class="<?php echo ($status=="yes")?"active":"" ?>">
                    <div class="date-schedule-box">
                        <div class="date-schedule">
                            <div class="date-schedule-items">
                                <div class="select-box">
                                    <label><?php esc_html_e('Timezone', CHT_OPT);?></label>
                                    <select class="select2-box" name="cht_date_rules[timezone]" id="cht_date_rules_time_zone">
                                        <?php echo chaty_timezone_choice($timezone, true );?>
                                    </select>
                                </div>
                                <div class="date-time-box">
                                    <div class="date-select-option">
                                        <label for="date_start_date">
                                            <span class="header-tooltip">
                                            <span class="header-tooltip-text text-center"><?php esc_html_e('Schedule a date from which the Chaty widget will be displayed (the starting date is included)', CHT_OPT);?></span>
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                            <?php esc_html_e('Start Date', CHT_OPT);?></label>
                                        <input type="text" name="cht_date_rules[start_date]" id="date_start_date" value="<?php echo esc_attr($start_date) ?>" >
                                    </div>
                                    <div class="time-select-option">
                                        <label for="date_start_time"><?php esc_html_e('Start Time', CHT_OPT);?></label>
                                        <input type="text" name="cht_date_rules[start_time]" id="date_start_time" value="<?php echo esc_attr($start_time) ?>">
                                    </div>
                                    <div class="clearfix clear"></div>
                                </div>
                                <div class="date-time-box">
                                <div class="date-select-option">
                                    <label for="date_end_date">
                                        <label for="date_start_date">
                                        <span class="header-tooltip">
                                        <span class="header-tooltip-text text-center"><?php esc_html_e('Schedule a date from which the Chaty widget will stop being displayed (the end date is included)', CHT_OPT);?></span>
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
										    <?php esc_html_e('End Date', CHT_OPT);?></label>
                                        <input type="text" name="cht_date_rules[end_date]" id="date_end_date" value="<?php echo esc_attr($end_date) ?>">
                                </div>
                                <div class="time-select-option">
                                    <label for="date_end_time"><?php esc_html_e('End Time', CHT_OPT);?></label>
                                    <input type="text" name="cht_date_rules[end_time]" id="date_end_time" value="<?php echo esc_attr($end_time) ?>">
                                </div>
                                <div class="clearfix clear"></div>
                            </div>
                            </div>
	                        <?php if(!$this->is_pro()) { ?>
                                <div class="chaty-pro-feature">
                                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()) ?>">
				                        <?php esc_attr_e('Upgrade to Pro', CHT_OPT); ?>
                                    </a>
                                </div>
	                        <?php } ?>
                        </div>
                        <a href="javascript:;" class="create-rule remove-rules" id="remove-date-rule"><?php esc_html_e('Remove Rules', CHT_OPT);?></a>
                    </div>
                    <div class="date-schedule-button">
                        <a href="javascript:;" class="create-rule" id="create-date-rule"><?php esc_html_e('Add Rule', CHT_OPT);?></a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="cht_date_rules[status]" id="cht_date_rules" value="<?php echo esc_attr($status) ?>" />
        </div>
        <div class="form-horizontal__item flex-center">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center">Display the widget on specific days and hours based on your opening days and hours</span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
                <?php esc_attr_e('Days and hours', CHT_OPT);?>:</label>
            <div class="chaty-option-box">
                <div class="chaty-page-options" id="chaty-page-options">
                    <div class="chaty-data-and-time-rules ">
                        <div class="chaty-date-time-option first last" data-index="__count__">
                            <div class="date-time-content">
                                <div class="day-select">
                                    <select class="cht-free-required" id="url_shown_on___count___option">
                                        <?php foreach ($days as $key=>$value) { ?>
                                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($value) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="day-label">
                                    From
                                </div>
                                <div class="day-time">
                                    <input type="text" class="cht-free-required time-picker" value=""  id="start_time___count__" />
                                </div>
                                <div class="day-label">
                                    To
                                </div>
                                <div class="day-time">
                                    <input type="text" class="cht-free-required time-picker" value="" id="end_time___count__" />
                                </div>
                                <div class="day-label">
                                    <span class="gmt-data">GMT</span>
                                </div>
                                <div class="day-time gtm-select">
                                    <select class="cht-free-required gmt-data" id="url_shown_on___count___option">
                                        <?php echo chaty_timezone_choice("", false) ?>
                                    </select>
                                </div>
                                <div class="day-buttons">
                                    <a class="remove-page-option" href="javascript:;">
                                        <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"></rect>
                                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"></rect>
                                        </svg>
                                    </a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                            <?php if(!$this->is_pro()) { ?>
                                <div class="chaty-pro-feature">
                                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl()) ?>">
                                        <?php esc_attr_e('Upgrade to Pro', CHT_OPT); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <a href="javascript:;" class="create-rule" id="create-data-and-time-rule">Add Rule</a>
            </div>
        </div>

        <div class="form-horizontal__item">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center">Show or don't show the widget on specific pages. You can use rules like contains, exact match, starts with, and ends with</span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
                Show on pages:</label>
            <div class="chaty-option-box">
                <div class="page-options">
                    <div class="chaty-page-option">
                        <div class="url-content">
                            <div class="url-select">
                                <select class="cht-free-required" id="url_shown_on___count___option">
                                    <option value="show_on">Show on</option>
                                    <option value="not_show_on">Don't show on</option>
                                </select>
                            </div>
                            <div class="url-option">
                                <select class="url-options cht-free-required" id="url_rules___count___option">
                                    <option selected="selected" disabled value="">Select Rule</option>
                                    <option>pages that contain</option>
                                    <option>a specific page</option>
                                    <option>pages starting with</option>
                                    <option>pages ending with</option>
                                </select>
                            </div>
                            <div class="url-box">
                                <span class='chaty-url'><?php echo esc_url(site_url("/")); ?></span>
                            </div>
                            <div class="url-values">
                                <input type="text" class="cht-free-required" value="" id="url_rules___count___value" />
                            </div>
                            <div class="url-buttons">
                                <a class="remove-chaty" href="javascript:;">
                                    <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"></rect>
                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"></rect>
                                    </svg>
                                </a>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="chaty-pro-feature">
                        <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">
                            <?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>
                        </a>
                    </div>
                </div>
                <a href="javascript:;" class="create-rule" id="create-rule">Add Rule</a>
            </div>
        </div>

        <div class="form-horizontal__item" id="custom-rules">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center">Show the widget only to visitors who come from specific traffic sources including direct traffic, social networks, search engines, Google Ads, or any other traffic source.</span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
                Traffic source:
            </label>
            <?php
            $checked = get_option('chaty_traffic_source');
            $checked = empty($checked)?"no":$checked;
            ?>
            <div class="chaty-option-box traffic-options-box <?php echo ($checked=="yes")?"active":"" ?>">
                <div class="traffic-default">
                    <a href="javascript:;" class="create-rule" id="update-chaty-traffic-source-rule">Add Rule</a>
                    <input type="hidden" name="chaty_traffic_source" id="chaty_traffic_source" value="<?php echo $checked ?>">
                </div>
                <div class="traffic-active">
                    <div class="trigger-block no-margin">
                        <div class="chaty-pro-block">
                            <?php
                            $checked = get_option('chaty_traffic_source_direct_visit');
                            $checked = empty($checked)?"no":$checked;
                            ?>
                            <input type="hidden" name="chaty_traffic_source_direct_visit" value="no">
                            <div class="trigger-option-block">
                                <label class="chaty-switch" for="chaty_traffic_source_direct_visit">
                                    <input type="checkbox" disabled="disabled" name="chaty_traffic_source_direct_visit" id="chaty_traffic_source_direct_visit" value="yes"  >
                                    <div class="chaty-slider round"></div>
                                    <span class="header-tooltip">
                                        <span class="header-tooltip-text text-center">Show the Chaty to visitors who arrived to your website from direct traffic</span>
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                    Direct visit
                                </label>
                            </div>
                            <?php
                            $checked = get_option('chaty_traffic_source_social_network');
                            $checked = empty($checked)?"no":$checked;
                            ?>
                            <div class="trigger-option-block">
                                <input type="hidden" name="chaty_traffic_source_social_network" value="no">
                                <label class="chaty-switch" for="chaty_traffic_source_social_network">
                                    <input type="checkbox" disabled="disabled" name="chaty_traffic_source_social_network" id="chaty_traffic_source_social_network" value="yes" >
                                    <div class="chaty-slider round"></div>
                                    <span class="header-tooltip">
                                        <span class="header-tooltip-text text-center">Show the Chaty to visitors who arrived to your website from social networks including: Facebook, Twitter, Pinterest, Instagram, Google+, LinkedIn, Delicious, Tumblr, Dribbble, StumbleUpon, Flickr, Plaxo, Digg and more</span>
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                    Social networks
                                </label>
                            </div>
                            <?php
                            $checked = get_option('chaty_traffic_source_search_engine');
                            $checked = empty($checked)?"no":$checked;
                            ?>
                            <div class="trigger-option-block">
                                <input type="hidden" name="chaty_traffic_source_search_engine" value="no">
                                <label class="chaty-switch" for="chaty_traffic_source_search_engine">
                                    <input type="checkbox" disabled="disabled" name="chaty_traffic_source_search_engine" id="chaty_traffic_source_search_engine" value="yes" >
                                    <div class="chaty-slider round"></div>
                                    <span class="header-tooltip">
                                        <span class="header-tooltip-text text-center">Show the Chaty to visitors who arrived from search engines including: Google, Bing, Yahoo!, Yandex, AOL, Ask, WOW,  WebCrawler, Baidu and more</span>
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                    Search engines
                                </label>
                            </div>
                            <?php
                            $checked = get_option('chaty_traffic_source_google_ads');
                            $checked = empty($checked)?"no":$checked;
                            ?>
                            <div class="trigger-option-block">
                                <input type="hidden" name="chaty_traffic_source_google_ads" value="no">
                                <label class="chaty-switch" for="chaty_traffic_source_google_ads">
                                    <input type="checkbox" disabled="disabled" name="chaty_traffic_source_google_ads" id="chaty_traffic_source_google_ads" value="yes" >
                                    <div class="chaty-slider round"></div>
                                    <span class="header-tooltip">
                                        <span class="header-tooltip-text text-center">Show the Chaty to visitors who arrived from search engines including: Google, Bing, Yahoo!, Yandex, AOL, Ask, WOW,  WebCrawler, Baidu and more</span>
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                    Google Ads
                                </label>
                            </div>
                            <div class="clear clearfix"></div>
                            <?php
                            $custom_rules = get_option("chaty_custom_traffic_rules")
                            ?>
                            <div class="traffic-custom-rules">
                                <div class="custom-rule-title">Specific URL</div>
                                <div class="traffic-custom-rules-box">
                                    <?php if(!empty($custom_rules) && is_array($custom_rules) && count($custom_rules) > 0) {
                                        foreach ($custom_rules as $key=>$rule) { ?>
                                            <div class="custom-traffic-rule">
                                                <div class="traffic-option">
                                                    <select name="chaty_custom_traffic_rules[<?php echo esc_attr($key) ?>][url_option]">
                                                        <option value="contain" <?php selected($rule['url_option'], "contain") ?>>Contains</option>
                                                        <option value="not_contain" <?php selected($rule['url_option'], "not_contain") ?>>Not contains</option>
                                                    </select>
                                                </div>
                                                <div class="traffic-url">
                                                    <input type="text" name="chaty_custom_traffic_rules[<?php echo esc_attr($key) ?>][url_value]" value="<?php echo esc_attr($rule['url_value']) ?>" placeholder="https://www.example.com" />
                                                </div>
                                                <div class="traffic-action">
                                                    <a class="remove-traffic-option" href="javascript:;">
                                                        <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"></rect>
                                                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"></rect>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="custom-traffic-rule">
                                            <div class="traffic-option">
                                                <select name="chaty_custom_traffic_rules[0][url_option]">
                                                    <option value="contain">Contains</option>
                                                    <option value="not_contain">Not contains</option>
                                                </select>
                                            </div>
                                            <div class="traffic-url">
                                                <input type="text" name="chaty_custom_traffic_rules[0][url_value]" />
                                            </div>
                                            <div class="traffic-action">
                                                <a class="remove-traffic-option" href="javascript:;">
                                                    <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"></rect>
                                                        <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"></rect>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="chaty-pro-feature">
                                <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">
                                    <?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>
                                </a>
                            </div>
                        </div>
                        <div class="clear clearfix"></div>
                        <div class="traffic-rule-actions">
                            <a href="javascript:;" class="create-rule" id="add-traffic-rule">Add Rule</a>
                            <a href="javascript:;" class="create-rule remove-rules" id="remove-traffic-rules">Remove Rules</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-horizontal__item">
            <label class="form-horizontal__item-label">
                <span class="header-tooltip">
                    <span class="header-tooltip-text text-center">Target your widget to specific countries. You can create different widgets for different countries</span>
                    <span class="dashicons dashicons-editor-help"></span>
                </span>
                Country targeting:</label>
            <div class="country-option-box">
                <div class="country-list-box">
                    <select class="country-list chaty-select">
                        <option value="">All Countries</option>
                    </select>
                </div>
                <div class="chaty-pro-feature">
                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">
                        <?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-horizontal__item">
            <label class="form-horizontal__item-label">Custom CSS:</label>
            <div class="country-option-box">
                <div class="country-list-box">
                    <textarea class="custom-css"></textarea>
                </div>
                <div class="chaty-pro-feature">
                    <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">
                        <?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="chaty_updated_on" value="<?php echo time(); ?>">
</section>