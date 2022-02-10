<?php

namespace CHT\frontend;

use CHT\admin\CHT_Admin_Base;
use CHT\admin\CHT_Social_Icons;

if (!defined('ABSPATH')) {
    exit;
}

$admin_base = CHT_ADMIN_INC . '/class-admin-base.php';
require_once($admin_base);

$social_icons = CHT_ADMIN_INC . '/class-social-icons.php';
require_once($social_icons);

class CHT_Frontend extends CHT_Admin_Base
{
    public $widget_number;
    /**
     * CHT_Frontend constructor.
     */
    public function __construct()
    {
        $this->socials = CHT_Social_Icons::get_instance()->get_icons_list();
        if (wp_doing_ajax()) {
            add_action('wp_ajax_choose_social', array($this, 'choose_social_handler'));
            add_action('wp_ajax_get_chaty_settings', array($this, 'get_chaty_settings'));     // return setting for a social media in html
            
            add_action('wp_ajax_chaty_front_form_save_data', array($this, 'chaty_front_form_save_data'));
            add_action('wp_ajax_nopriv_chaty_front_form_save_data', array($this, 'chaty_front_form_save_data'));

	        add_action('wp_ajax_remove_chaty_widget', array($this, 'remove_chaty_widget'));     // remove social media widget
	        add_action('wp_ajax_change_chaty_widget_status', array($this, 'change_chaty_widget_status'));     // remove social media widget
        }

	    if(!isset($_GET['ct_builder'])) {
		    add_action( 'wp_enqueue_scripts', array( $this, 'cht_front_end_css_and_js' ), 0 );
	    }
    }

	public function remove_chaty_widget() {
		if (current_user_can('manage_options')) {
			$widget_index = filter_input(INPUT_POST, 'widget_index', FILTER_SANITIZE_STRING);
			$widget_nonce = filter_input(INPUT_POST, 'widget_nonce', FILTER_SANITIZE_STRING);
			if (isset($widget_index) && !empty($widget_index) && !empty($widget_nonce) && wp_verify_nonce($widget_nonce, "chaty_remove_" . $widget_index)) {

				$options = array(
					'mobile' => '1',
					'desktop' => '1',
				);
                delete_option("cht_active");
                delete_option("chaty_icons_view");
                delete_option("chaty_icons_view");
                delete_option("cht_cta_text_color");
                delete_option("cht_cta_bg_color");
                delete_option("cht_pending_messages");
                delete_option("cht_number_of_messages");
                delete_option("cht_number_color");
                delete_option("cht_number_bg_color");
                delete_option("cht_cta_switcher");
                delete_option("chaty_attention_effect");
                delete_option("chaty_default_state");
                delete_option("chaty_trigger_on_time");
                delete_option("chaty_trigger_time");
                delete_option("chaty_trigger_on_exit");
                delete_option("chaty_trigger_on_scroll");
                delete_option("chaty_trigger_on_page_scroll");
                delete_option("cht_close_button");
                delete_option("cht_close_button_text");
                delete_option("chaty_updated_on");

				foreach ($this->socials as $social) {
					delete_option('cht_social_' . $social['slug']);
				}

				update_option('cht_devices', $options);
				update_option('cht_position', 'right');
				update_option('cht_cta', 'Contact us');
				update_option('cht_numb_slug', ',Phone,Whatsapp');
				update_option('cht_social_whatsapp', '');
				update_option('cht_social_phone', '');
				update_option('cht_widget_size', '54');
				update_option('widget_icon', 'chat-base');
				update_option('cht_widget_img', '');
				update_option('cht_color', '#A886CD');
				echo esc_url(admin_url("admin.php?page=chaty-app"));
				exit;
			}
		}
	}

	public function change_chaty_widget_status() {
		if (current_user_can('manage_options')) {
			$widget_index = filter_input(INPUT_POST, 'widget_index', FILTER_SANITIZE_STRING);
			$widget_nonce = filter_input(INPUT_POST, 'widget_nonce', FILTER_SANITIZE_STRING);
			if (isset($widget_index) && !empty($widget_index) && !empty($widget_nonce) && wp_verify_nonce($widget_nonce, "chaty_remove_" . $widget_index)) {
				$widget_index = trim($widget_index,"_");
				if(empty($widget_index) || $widget_index == 0) {
					$widget_index = "";
				} else {
					$widget_index = "_".$widget_index;
				}
				$status = get_option("cht_active".$widget_index);
				if($status) {
					update_option("cht_active".$widget_index, 0);
				} else {
					update_option("cht_active".$widget_index, 1);
				}
			}
		}
		echo "1"; exit;
	}

    function chaty_front_form_save_data() {
        $response = array(
            'status' => 0,
            'error' => 0,
            'errors' => array(),
            'message' => ''
        );
        $postData = filter_input_array(INPUT_POST);
        if(isset($postData['nonce']) && isset($postData['widget']) && wp_verify_nonce($postData['nonce'], "chaty-front-form".$postData['widget'])) {
            $name = isset($postData['name'])?$postData['name']:"";
            $phone = isset($postData['email'])?$postData['phone']:"";
            $email = isset($postData['phone'])?$postData['email']:"";
            $message = isset($postData['message'])?$postData['message']:"";
            $ref_url = isset($postData['ref_url'])?$postData['ref_url']:"";
            $widget = $postData['widget'];
            $channel = $postData['channel'];

            $value = get_option('cht_social_' . $channel);   //  get saved settings for button

            $errors = array();
            if(!empty($value)) {
                $field_setting = isset($value['name'])?$value['name']:array();
                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes" && isset($field_setting['is_required']) && $field_setting['is_required'] == "yes" && empty($name)) {
                    $error = array(
                        'field' => 'chaty-field-name',
                        'message' => esc_attr("this field is required", 'chaty')
                    );
                    $errors[] = $error;
                }
	            $field_setting = isset($value['phone'])?$value['phone']:array();
	            if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes" && isset($field_setting['is_required']) && $field_setting['is_required'] == "yes" && empty($phone)) {
		            $error = array(
			            'field' => 'chaty-field-phone',
			            'message' => esc_attr("this field is required", 'chaty')
		            );
		            $errors[] = $error;
	            }
                $field_setting = isset($value['email'])?$value['email']:array();
                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes" && isset($field_setting['is_required']) && $field_setting['is_required'] == "yes") {
                    if(empty($email)) {
                        $error = array(
                            'field' => 'chaty-field-email',
                            'message' => esc_attr("this field is required", 'chaty')
                        );
                        $errors[] = $error;
                    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error = array(
                            'field' => 'chaty-field-email',
                            'message' => esc_attr("email address is not valid", 'chaty')
                        );
                        $errors[] = $error;
                    }
                }
                $field_setting = isset($value['message'])?$value['message']:array();
                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes" && isset($field_setting['is_required']) && $field_setting['is_required'] == "yes" && empty($message)) {
                    $error = array(
                        'field' => 'chaty-field-message',
                        'message' => esc_attr("this field is required", 'chaty')
                    );
                    $errors[] = $error;
                }
                if(empty($errors)) {
                    $widget = trim($widget, "_");
                    $response['message'] = $value['thanks_message'];
                    $response['redirect_action'] = $value['redirect_action'];
                    $response['redirect_link'] = esc_url($value['redirect_link']);
                    $response['link_in_new_tab'] = $value['link_in_new_tab'];
                    $response['close_form_after'] = $value['close_form_after'];
                    $response['close_form_after_seconds'] = $value['close_form_after_seconds'];
                    $send_leads_in_email = $value['send_leads_in_email'];
                    $save_leads_locally = $value['save_leads_locally'];

	                date_default_timezone_set("UTC");
	                $current_date = date("Y-m-d H:i:s");

	                $new_date = get_date_from_gmt($current_date, "Y-m-d H:i:s");

                    global $wpdb;
                    $chaty_table = $wpdb->prefix . 'chaty_contact_form_leads';
                    $insert = array();
                    $field_setting = isset($value['name'])?$value['name']:array();
                    if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                        $insert['name'] = esc_sql(sanitize_text_field($name));
                    }
                    $field_setting = isset($value['email'])?$value['email']:array();
                    if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                        $insert['email'] = esc_sql(sanitize_text_field($email));
                    }
	                $field_setting = isset($value['phone'])?$value['phone']:array();
	                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
		                $insert['phone_number'] = esc_sql(sanitize_text_field($phone));
	                }
                    $field_setting = isset($value['message'])?$value['message']:array();
                    if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                        $insert['message'] = esc_sql(sanitize_text_field($message));
                    }
                    $insert['ref_page'] = $ref_url;
                    $insert['ip_address'] = $this->get_user_ipaddress();
                    $insert['widget_id'] = esc_sql(sanitize_text_field($widget));
                    $insert['created_on'] = $new_date;
                    $wpdb->insert($chaty_table, $insert);


                    $response['status'] = 1;
                } else {
                    $response['errors'] = $errors;
                    $response['error'] = 1;
                }
            } else {
                $response['message'] = "Invalid request, Please try again";
            }
        } else {
            $response['message'] = "Invalid request, Please try again";
        }
        echo json_encode($response);
        exit;
    }

    function cht_front_end_css_and_js() {
        if ($this->canInsertWidget()):
            /* Initialize widget if widget is enable for current page */
            $social = $this->get_social_icon_list();            // get active icon list
            $cht_active = get_option("cht_active");

            //$bg_color = $this->get_current_color();
            // get custom background color for widget
            $def_color = get_option('cht_color' );
            $custom_color = get_option('cht_custom_color' );     // checking for custom color
            if (!empty($custom_color)) {
                $color = $custom_color;
            } else {
                $color = $def_color;
            }
            $bg_color = strtoupper($color);

            $len = count($social);                              // get total active channels
	        $cta = nl2br(get_option('cht_cta'));
//	        $cta = str_replace(array("\r", "\n"), "", $cta);
	        $cta = str_replace("&amp;#39;","'",$cta);
	        $cta = str_replace("&#39;","'",$cta);
	        $cta = esc_attr__(wp_unslash($cta));
	        $cta = html_entity_decode($cta);

            $isPro = get_option('cht_token');                                 // is PRO version
            $isPro = (empty($isPro) || $isPro == null)?0:1;

            $positionSide = get_option('positionSide');                             // get widget position
            $cht_bottom_spacing = get_option('cht_bottom_spacing');                 // get widget position from bottom
            $cht_side_spacing = get_option('cht_side_spacing');                     // get widget position from left/Right
            $cht_widget_size = get_option('cht_widget_size');                       // get widget size
            $positionSide = empty($positionSide) ? 'right' : $positionSide;         // Initialize widget position if not exists
            $cht_side_spacing = ($cht_side_spacing) ? $cht_side_spacing : '25';     // Initialize widget from left/Right if not exists
            $cht_widget_size = ($cht_widget_size) ? $cht_widget_size : '54';        // Initialize widget size if not exists
            $position = get_option('cht_position');
            $position = ($position) ? $position : 'right';                          // Initialize widget position if not exists
            $total = $cht_side_spacing+$cht_widget_size+$cht_side_spacing;
            $cht_bottom_spacing = ($cht_bottom_spacing) ? $cht_bottom_spacing : '25';   // Initialize widget bottom position if not exists
            $cht_side_spacing = ($cht_side_spacing) ? $cht_side_spacing : '25';     // Initialize widget left/Right position if not exists
            $image_id = "";
            $imageUrl = plugin_dir_url("")."chaty-pro/admin/assets/images/chaty-default.png";       // Initialize default image
            $analytics = get_option("cht_google_analytics");                        // check for google analytics enable or not
            $analytics = empty($analytics)?0:$analytics;                            // Initialize google analytics flag to 0 if not data not exists
            $text = get_option("cht_close_button_text");                            // close button settings
            $close_text = ($text === false)?"Hide":$text;

            $imageUrl = "";
            if($image_id != "") {
                $image_data = wp_get_attachment_image_src($image_id, "full");
                if(!empty($image_data) && is_array($image_data)) {
                    $imageUrl = $image_data[0];                                     // change close button image if exists
                }
            }
            $font_family = get_option('cht_widget_font');
	        if($font_family == "System Stack") {
		        $font_family = "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif";
	        }
            /* add inline css for custom position */

            $animation_class = get_option("chaty_attention_effect");
            $animation_class = empty($animation_class)?"":$animation_class;

            $time_trigger = get_option("chaty_trigger_on_time");
            $time_trigger = empty($time_trigger)?"no":$time_trigger;

            $trigger_time = get_option("chaty_trigger_time");
            $trigger_time = (empty($trigger_time) || !is_numeric($trigger_time) || $trigger_time < 0)?"0":$trigger_time;

            $exit_intent = get_option("chaty_trigger_on_exit");
            $exit_intent = empty($exit_intent)?"no":$exit_intent;

            $on_page_scroll = get_option("chaty_trigger_on_scroll");
            $on_page_scroll = empty($on_page_scroll)?"no":$on_page_scroll;

            $page_scroll = get_option("chaty_trigger_on_page_scroll");
            $page_scroll = (empty($page_scroll) || !is_numeric($page_scroll) || $page_scroll < 0)?"0":$page_scroll;

            $state = get_option("chaty_default_state");
            $state = empty($state)?"click":$state;

            $has_close_button = get_option("cht_close_button");
            $has_close_button = empty($has_close_button)?"yes":$has_close_button;

            $display_days = get_option("cht_date_and_time_settings");
            $display_rules = array();

            $gmt = "";
            if(!empty($display_days)) {
                $count = 0;
                foreach ($display_days as $key=>$value) {
                    if($count == 0) {
                        $gmt = intval($value['gmt']);
                        $count++;
                    }
                    $record = array();
                    $record['days'] = $value['days']-1;
                    $record['start_time'] = $value['start_time'];
                    $record['start_hours'] = intval(date("G",strtotime(date("Y-m-d ".$value['start_time']))));
                    $record['start_min'] = intval(date("i",strtotime(date("Y-m-d ".$value['start_time']))));
                    $record['end_time'] = $value['end_time'];
                    $record['end_hours'] = intval(date("G",strtotime(date("Y-m-d ".$value['end_time']))));
                    $record['end_min'] = intval(date("i",strtotime(date("Y-m-d ".$value['end_time']))));
                    $display_rules[] = $record;
                }
            }
            $display_conditions = 0;
            if(!empty($display_rules)) {
                $display_conditions = 1;
            }

            $mode = get_option("chaty_icons_view");
            $mode = empty($mode) ? "vertical" : $mode;

            $pending_messages = get_option("cht_pending_messages");
            $pending_messages = ($pending_messages === false)?"off":$pending_messages;

            $click_setting = get_option("cht_cta_action");
            $click_setting = ($click_setting === false)?"click":$click_setting;

            $cht_number_of_messages = get_option("cht_number_of_messages");
            $cht_number_of_messages = ($cht_number_of_messages === false)?0:$cht_number_of_messages;

            $number_color = get_option("cht_number_color");
            $number_color = ($number_color === false)?"#ffffff":$number_color;

            $number_bg_color = get_option("cht_number_bg_color");
            $number_bg_color = ($number_bg_color === false)?"#dd0000":$number_bg_color;

            $cht_cta_text_color = get_option("cht_cta_text_color");
            $cht_cta_text_color = ($cht_cta_text_color === false)?"#dd0000":$cht_cta_text_color;

            $cht_cta_bg_color = get_option("cht_cta_bg_color");
            $cht_cta_bg_color = ($cht_cta_bg_color === false)?"#ffffff":$cht_cta_bg_color;

            if(empty($cht_number_of_messages)) {
                $pending_messages = "off";
            }

	        $bg_color = ($bg_color) ? $bg_color : '#A886CD';

            /* widget setting array */
            $settings = array();
            $settings['isPRO'] = 0;
            $settings['pending_messages'] = $pending_messages;
            $settings['cht_cta_bg_color'] = $cht_cta_bg_color;
            $settings['cht_cta_text_color'] = $cht_cta_text_color;
            $settings['click_setting'] = $click_setting;
            $settings['number_of_messages'] = $cht_number_of_messages;
            $settings['number_color'] = $number_color;
            $settings['number_bg_color'] = $number_bg_color;
            $settings['position'] = $position;;
            $settings['social'] = $this->get_social_icon_list();
            $settings['pos_side'] = $positionSide;
            $settings['bot'] = $cht_bottom_spacing;
            $settings['side'] = $cht_side_spacing;
            $settings['device'] = $this->device();
            $settings['color'] = $bg_color;
	        $settings['rgb_color'] = $this->getRGBColor($bg_color);
            $settings['widget_size'] = $cht_widget_size;
            $settings['widget_type'] = get_option('widget_icon');
            $settings['widget_img'] = $this->getCustomWidgetImg();
            $settings['cta'] = $cta;
            $settings['active'] = ($cht_active && $len >= 1) ? 'true' : 'false';
            $settings['close_text'] = $close_text;
            $settings['analytics'] = $analytics;
            $settings['save_user_clicks'] = 0;
            $settings['close_img'] = "";
            $settings['is_mobile'] = (wp_is_mobile())?1:0;
            $settings['ajax_url'] = admin_url('admin-ajax.php');
            $settings['animation_class'] = $animation_class;
            $settings['time_trigger'] = $time_trigger;
            $settings['trigger_time'] = $trigger_time;
            $settings['exit_intent'] = $exit_intent;
            $settings['on_page_scroll'] = $on_page_scroll;
            $settings['page_scroll'] = $page_scroll;
            $settings['gmt'] = $gmt;
            $settings['display_conditions'] = $display_conditions;
            $settings['display_rules'] = $display_rules;
            $settings['display_state'] = $state;
            $settings['has_close_button'] = $has_close_button;
            $settings['mode'] = $mode;
            $settings['ajax_url'] = admin_url("admin-ajax.php");

            $data = array();
            $data['object_settings'] = $settings;
            $data['ajax_url'] = admin_url("admin-ajax.php");
            ob_start();
            ?>
                <?php if($position == "left") { ?>
                #wechat-qr-code{left: <?php esc_attr_e($total) ?>px; right:auto;}
                <?php } else if($position == "right") { ?>
                #wechat-qr-code{right: <?php esc_attr_e($total) ?>px; left:auto;}
                <?php } else if($position == "custom") { ?>
                <?php if($positionSide == "left") { ?>
                #wechat-qr-code{left: <?php esc_attr_e($total) ?>px; right:auto;}
                <?php } else { ?>
                #wechat-qr-code{right: <?php esc_attr_e($total) ?>px; left:auto;}
                <?php } ?>
                <?php } ?>
                .chaty-widget-is a{display: block; margin:0; padding:0;border-radius: 50%;-webkit-border-radius: 50%;-moz-border-radius: 50%; }
                .chaty-widget-is svg{margin:0; padding:0;}
                .chaty-main-widget { display: none; }
                .chaty-in-desktop .chaty-main-widget.is-in-desktop { display: block; }
                .chaty-in-mobile .chaty-main-widget.is-in-mobile { display: block; }
                .chaty-widget.hide-widget { display: none !important; }
                .chaty-widget, .chaty-widget .get, .chaty-widget .get a { width: <?php echo esc_attr($cht_widget_size+8); ?>px }
                .facustom-icon { width: <?php echo esc_attr($cht_widget_size); ?>px; line-height: <?php echo esc_attr($cht_widget_size); ?>px; height: <?php echo esc_attr($cht_widget_size); ?>px; font-size: <?php echo esc_attr(intval($cht_widget_size/2)); ?>px; }
                .chaty-widget-is a { width: <?php echo esc_attr($cht_widget_size); ?>px; height: <?php echo esc_attr($cht_widget_size); ?>px; }
                <?php if(!empty($font_family)) { ?>
                .chaty-widget { font-family: <?php echo esc_attr($font_family) ?>; }
                <?php } ?>
                <?php foreach($settings['social'] as $social) {
                    if(!empty($social['bg_color']) && $social['bg_color'] != "#ffffff") {
                        ?>
                .facustom-icon.chaty-btn-<?php echo esc_attr($social['social_channel']) ?> {background-color: <?php echo esc_attr($social['bg_color']) ?>}
                .chaty-<?php echo esc_attr($social['social_channel']) ?> .color-element {fill: <?php echo esc_attr($social['bg_color']) ?>; background: <?php echo esc_attr($social['bg_color']) ?>}
                <?php }
                } ?>
                /*.chaty-widget-i-title.hide-it { display: none !important; }*/
                body div.chaty-widget.hide-widget { display: none !important; }
            <?php
            echo ".i-trigger .chaty-widget-i-title, .chaty-widget-i .chaty-widget-i-title {color:".esc_attr($cht_cta_text_color)." !important; background:".esc_attr($cht_cta_bg_color)." !important;}";
            //echo ".i-trigger .chaty-widget-i-title p, .chaty-widget-i.chaty-main-widget .chaty-widget-i-title p {color:".esc_attr($cht_cta_text_color)." !important; background:".esc_attr($cht_cta_bg_color)." !important;}";
            echo ".i-trigger .chaty-widget-i-title p, .chaty-widget-i.chaty-main-widget .chaty-widget-i-title p {color:".esc_attr($cht_cta_text_color)." !important; }";
            echo ".i-trigger .chaty-widget-i:not(.no-tooltip):before, .chaty-widget-i.chaty-main-widget:before { border-color: transparent transparent transparent ".esc_attr($cht_cta_bg_color)." !important;}";
            echo ".chaty-widget.chaty-widget-is-right .i-trigger .i-trigger-open.chaty-widget-i:before, .chaty-widget.chaty-widget-is-right .chaty-widget-i:before { border-color: transparent ".esc_attr($cht_cta_bg_color)." transparent transparent !important;}";
            echo ".chaty-widget.chaty-widget-is-right .i-trigger .chaty-widget-i:before, .chaty-widget.chaty-widget-is-right .chaty-widget-i:before {border-color: transparent ".esc_attr($cht_cta_bg_color)." transparent transparent !important; }";
            $chaty_css = ob_get_clean();

            if($len >= 1 && !empty($settings['social'])) {

                $chaty_updated_on = get_option("chaty_updated_on");
                if(empty($chaty_updated_on)) {
                    $chaty_updated_on = time();
                }

                /* add js for front end widget */
                if(!empty($font_family)) {
	                if(!in_array($font_family, array("Arial", "Tahoma", "Verdana", "Helvetica", "Times New Roman", "Trebuchet MS", "Georgia", "System Stack", "-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif"))) {
                        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=' . urlencode($font_family), false, false);
                    }
                }
                /* WP change this */
                wp_enqueue_style( 'chaty-front-css', CHT_PLUGIN_URL."css/chaty-front.min.css", array(), $chaty_updated_on);
                wp_add_inline_style('chaty-front-css', $chaty_css);
                wp_enqueue_script( "chaty-front-end", CHT_PLUGIN_URL."js/cht-front-script.js", array( 'jquery' ), $chaty_updated_on, false);
                wp_localize_script('chaty-front-end', 'chaty_settings',  $data);
            }
        endif;
    }

    public function get_chaty_settings() {
        $slug = filter_input(INPUT_POST, 'social', FILTER_SANITIZE_STRING);
        $channel = filter_input(INPUT_POST, 'channel', FILTER_SANITIZE_STRING);
        $status = 0;
        $data = array();
        if(!empty($slug)) {
            foreach ($this->socials as $social) {
                if ($social['slug'] == $slug) {
                    break;
                }
            }
            if (!empty($social)) {
                $status = 1;
                $data = $social;
                $data['help'] = "";
                $data['help_text'] = "";
                $data['help_link'] = "";
                if((isset($social['help']) && !empty($social['help'])) || isset($social['help_link'])) {
                    $data['help_title'] = isset($social['help_title'])?$social['help_title']:"Doesn't work?";
                    $data['help_text'] = isset($social['help'])?$social['help']:"";
                    if(isset($data['help_link']) && !empty($data['help_link'])) {
                        $data['help_link'] = $data['help_link'];
                    } else {
                        $data['help_title'] = $data['help_title'];
                    }
                }
            }
        }
        $response = array();
        $response['data'] = $data;
        $response['status'] = $status;
        $response['channel'] = $channel;
        echo json_encode($response);
        die;
    }

    /* function choose_social_handler start */
    public function choose_social_handler()
    {
        check_ajax_referer('cht_nonce_ajax', 'nonce_code');
        $slug = filter_input(INPUT_POST, 'social', FILTER_SANITIZE_STRING);

        if (!is_null($slug) && !empty($slug)) {
            foreach ($this->socials as $social) {
                if ($social['slug'] == $slug) {
                    break;
                }
            }
            if (!$social) {
                return;                                     // return if social media setting not found
            }

            $widget_index = filter_input(INPUT_POST, 'widget_index', FILTER_SANITIZE_STRING);

            $value = get_option('cht_social'.$widget_index.'_' . $slug);   // get setting for media if already saved

            if (empty($value)) {                                        // Initialize default values if not found
                $value = [
                    'value' => '',
                    'is_mobile' => 'checked',
                    'is_desktop' => 'checked',
                    'image_id' => '',
                    'title' => $social['title'],
                    'bg_color' => "",
                ];
            }
            if(!isset($value['bg_color']) || empty($value['bg_color'])) {
                $value['bg_color'] = $social['color'];                  // Initialize background color value if not exists. 2.1.0 change
            }
            if(!isset($value['image_id'])) {
                $value['image_id'] = '';                                // Initialize custom image id if not exists. 2.1.0 change
            }
            if(!isset($value['title'])) {
                $value['title'] = $social['title'];                     // Initialize title if not exists. 2.1.0 change
            }
            if(!isset($value['fa_icon'])) {
                $value['fa_icon'] = "";                     // Initialize title if not exists. 2.1.0 change
            }
            if(!isset($value['value'])) {
                $value['value'] = "";                     // Initialize title if not exists. 2.1.0 change
            }
            $imageId = $value['image_id'];
            $imageUrl = "";
            $status = 0;
            if(!empty($imageId)) {
                $imageUrl = wp_get_attachment_image_src($imageId, "full")[0];                       // get custom image URL if exists
                $status = 1;
            }
            if($imageUrl == "") {
                $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";   // Initialize with default image if custom image is not exists
                $status = 0;
                $imageId = "";
            }
            $color = "";
            if(!empty($value['bg_color'])) {
                $color = "background-color: ".$value['bg_color'];                                   // set background color of icon it it is exists
            }
            if($social['slug'] == "Whatsapp"){
                $val = $value['value'];
                $val = str_replace("+","", $val);
                $value['value'] = $val;
            } else if($social['slug'] == "Facebook_Messenger"){
                $val = $value['value'];
                $val = str_replace("facebook.com","m.me", $val);                                    // Replace facebook.com with m.me version 2.0.1 change
                $val = str_replace("www.","", $val);                                                // Replace www. with blank version 2.0.1 change
                $value['value'] = $val;

                $val = trim($val, "/");
                $val_array = explode("/", $val);
                $total = count($val_array)-1;
                $last_value = $val_array[$total];
                $last_value = explode("-", $last_value);
                $total_text = count($last_value)-1;
                $total_text = $last_value[$total_text];

                if(is_numeric($total_text)) {
                    $val_array[$total] = $total_text;
                    $value['value'] = implode("/", $val_array);
                }
            }
            $value['value'] = esc_attr__(wp_unslash($value['value']));
            $value['title'] = esc_attr__(wp_unslash($value['title']));

            $svg_icon = $social['svg'];

            $help_title = "";
            $help_text = "";
            $help_link = "";

            if((isset($social['help']) && !empty($social['help'])) || isset($social['help_link'])) {
                $help_title = isset($social['help_title'])?$social['help_title']:"Doesn't work?";
                $help_text = isset($social['help'])?$social['help']:"";
                if(isset($social['help_link']) && !empty($social['help_link'])) {
                    $help_link = $social['help_link'];
                }
            }

            $channel_type = "";
            $placeholder = $social['example'];
            if($social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "Custom_Link_4" || $social['slug'] == "Custom_Link_5") {
                if (isset($value['channel_type'])) {
                    $channel_type = esc_attr__(wp_unslash($value['channel_type']));
                }

                if(!empty($channel_type)) {
                    foreach($this->socials as $icon) {
                        if($icon['slug'] == $channel_type) {
                            $svg_icon = $icon['svg'];

                            $placeholder = $icon['example'];

                            if((isset($icon['help']) && !empty($icon['help'])) || isset($icon['help_link'])) {
                                $help_title = isset($icon['help_title'])?$icon['help_title']:"Doesn't work?";
                                $help_text = isset($icon['help'])?$icon['help']:"";
                                if(isset($icon['help_link']) && !empty($icon['help_link'])) {
                                    $help_link = $icon['help_link'];
                                }
                            }
                        }
                    }
                }
            }
            if(empty($channel_type)) {
                $channel_type = $social['slug'];
            }
	        if($channel_type == "Telegram") {
		        $value['value'] = trim($value['value'], "@");
	        }
            ob_start();
            ?>
            <!-- Social media setting box: start -->
            <li data-id="<?php echo esc_attr($social['slug']) ?>" class="chaty-channel" data-channel="<?php echo esc_attr($channel_type) ?>" id="chaty-social-<?php echo esc_attr($social['slug']) ?>">
                <div class="channels-selected__item <?php esc_attr_e(($status)?"img-active":"") ?> <?php esc_attr_e(($this->is_pro()) ? 'pro' : 'free'); ?> 1 available">
                    <div class="chaty-default-settings">
                        <div class="move-icon">
                            <img src="<?php echo esc_url(plugin_dir_url("")."/chaty/admin/assets/images/move-icon.png") ?>">
                        </div>
                        <div class="icon icon-md active" data-label="<?php esc_attr_e($social['title']); ?>">
                            <span style="" class="custom-chaty-image custom-image-<?php echo esc_attr($social['slug']) ?>" id="image_data_<?php echo esc_attr($social['slug']) ?>">
                                <img src="<?php echo esc_url($imageUrl) ?>" />
                                <span onclick="remove_chaty_image('<?php echo esc_attr($social['slug']) ?>')" class="remove-icon-img"></span>
                            </span>
                            <span class="default-chaty-icon <?php echo (isset($value['fa_icon'])&&!empty($value['fa_icon']))?"has-fa-icon":"" ?> custom-icon-<?php echo esc_attr($social['slug']) ?> default_image_<?php echo esc_attr($social['slug']) ?>" >
                                <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <?php echo $svg_icon; ?>
                                </svg>
                                <span class="facustom-icon" style="background-color: <?php echo esc_attr($value['bg_color']) ?>"><i class="<?php echo esc_attr($value['fa_icon']) ?>"></i></span>
                            </span>
                        </div>

                        <?php if($social['slug'] != 'Contact_Us') { ?>
                            <!-- Social Media input  -->
	                        <?php if(($social['slug'] == "Whatsapp" || $channel_type == "Whatsapp") && !empty($value['value'])) {
		                        $value['value'] = trim($value['value'], "+");
		                        $value['value'] = "+".$value['value'];
	                        } ?>
                            <div class="channels__input-box">
                                <input data-label="<?php echo esc_attr($social['title']) ?>" placeholder="<?php esc_attr_e($placeholder); ?>" type="text" class="channels__input custom-channel-<?php echo esc_attr__($channel_type) ?> <?php echo isset($social['attr'])?$social['attr']:"" ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[value]" value="<?php esc_attr_e(wp_unslash($value['value'])); ?>" data-gramm_editor="false" id="channel_input_<?php echo esc_attr($social['slug']); ?>" />
                            </div>
                        <?php } ?>
                        <div class="channels__device-box">
                            <?php
                            $slug =  esc_attr__($this->del_space($social['slug']));
                            $slug = str_replace(' ', '_', $slug);
                            $is_desktop = isset($value['is_desktop']) && $value['is_desktop'] == "checked" ? "checked" : '';
                            $is_mobile = isset($value['is_mobile']) && $value['is_mobile'] == "checked" ? "checked" : '';
                            ?>
                            <!-- setting for desktop -->
                            <label class="channels__view" for="<?php echo esc_attr($slug); ?>Desktop">
                                <input type="checkbox" id="<?php echo esc_attr($slug); ?>Desktop" class="channels__view-check js-chanel-icon js-chanel-desktop" data-type="<?php echo str_replace(' ', '_', strtolower(esc_attr__($this->del_space($social['slug'])))); ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_desktop]" value="checked" data-gramm_editor="false" <?php esc_attr_e($is_desktop) ?> />
                                <span class="channels__view-txt">Desktop</label>
                            </label>

                            <!-- setting for mobile -->
                            <label class="channels__view" for="<?php echo esc_attr($slug); ?>Mobile">
                                <input type="checkbox" id="<?php echo esc_attr($slug); ?>Mobile" class="channels__view-check js-chanel-icon js-chanel-mobile" data-type="<?php echo str_replace(' ', '_', strtolower(esc_attr__($this->del_space($social['slug'])))); ?>" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_mobile]" value="checked" data-gramm_editor="false" <?php esc_attr_e($is_mobile) ?> >
                                <span class="channels__view-txt">Mobile</span>
                            </label>
                        </div>

                        <?php if($social['slug'] == 'Contact_Us') { ?>
                            <div class="channels__input transparent"></div>
                        <?php } ?>

                        <?php
                        $close_class = "active";
                        if($social['slug'] == 'Contact_Us') {
                            $setting_status = get_option("chaty_contact_us_setting");
                            if($setting_status === false) {
                                $close_class = "";
                            }
                        }
                        ?>

                        <!-- button for advance setting -->
                        <div class="chaty-settings <?php echo esc_attr($close_class) ?>" data-nonce="<?php echo wp_create_nonce($social['slug']."-settings") ?>" id="<?php echo esc_attr($social['slug']); ?>-close-btn" onclick="toggle_chaty_setting('<?php echo esc_attr($social['slug']); ?>')">
                            <a href="javascript:;"><span class="dashicons dashicons-admin-generic"></span> Settings</a>
                        </div>

                        <?php if($social['slug'] != 'Contact_Us') { ?>

                            <!-- example for social media -->
                            <div class="input-example">
                                <?php esc_attr_e('For example', CHT_OPT); ?>:
                                <span class="inline-box channel-example">
                                                <?php if($social['slug'] == "Poptin") { ?>
                                                    <br/>
                                                <?php } ?>
                                    <?php esc_attr_e($placeholder); ?>
                                            </span>
                            </div>

                            <!-- checking for extra help message for social media -->
                            <div class="help-section">
                                <?php if((isset($social['help']) && !empty($social['help'])) || isset($social['help_link'])) { ?>
                                    <div class="viber-help">
                                        <?php if(isset($help_link) && !empty($help_link)) { ?>
                                            <a class="help-link" href="<?php echo esc_url($help_link) ?>" target="_blank"><?php esc_attr_e($help_title); ?></a>
                                        <?php } else if(isset($help_text) && !empty($help_text)) { ?>
                                            <span class="help-text"><?php echo $help_text; ?></span>
                                            <span class="help-title"><?php esc_attr_e($help_title); ?></span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if($social['slug'] == "Whatsapp" || $social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "Custom_Link_4" || $social['slug'] == "Custom_Link_5") { ?>
                        <div class="Whatsapp-settings advanced-settings extra-chaty-settings">
                            <?php $embedded_window = isset($value['embedded_window'])?$value['embedded_window']:"no"; ?>
                            <div class="chaty-setting-col">
                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_window]" value="no" >
                                <label class="chaty-switch chaty-embedded-window" for="whatsapp_embedded_window_<?php echo esc_attr($social['slug']); ?>">
                                    <input type="checkbox" class="embedded_window-checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_window]" id="whatsapp_embedded_window_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($embedded_window, "yes") ?> >
                                    <div class="chaty-slider round"></div>
                                    WhatsApp Chat Popup &#128172;
                                    <div class="html-tooltip top">
                                        <span class="dashicons dashicons-editor-help"></span>
                                        <span class="tooltip-text top">
                                            Show an embedded WhatsApp window to your visitors with a welcome message. Your users can start typing their own message and start a conversation with you right away once they are forwarded to the WhatsApp app.
                                            <img src="<?php echo esc_url(CHT_PLUGIN_URL) ?>/admin/assets/images/whatsapp-popup.gif" />
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <!-- advance setting for Whatsapp -->
                            <div class="whatsapp-welcome-message <?php echo ($embedded_window=="yes")?"active":"" ?>">
                                <div class="chaty-setting-col">
                                    <label style="display: block; width: 100%" for="cht_social_embedded_message_<?php echo esc_attr($social['slug']); ?>">Welcome message</label>
                                    <div class="full-width">
                                        <div class="full-width">
                                            <?php $unique_id = uniqid(); ?>
                                            <?php $embedded_message = isset($value['embedded_message'])?$value['embedded_message']:esc_html__("How can I help you? :)", "chaty"); ?>
                                            <textarea class="chaty-setting-textarea chaty-whatsapp-setting-textarea" data-id="<?php echo esc_attr($unique_id) ?>" id="cht_social_embedded_message_<?php echo esc_attr($unique_id) ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[embedded_message]" ><?php echo $embedded_message ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="chaty-setting-col">
                                    <?php $is_default_open = isset($value['is_default_open'])?$value['is_default_open']:""; ?>
                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_default_open]" value="no" >
                                    <label class="chaty-switch" for="whatsapp_default_open_embedded_window_<?php echo esc_attr($social['slug']); ?>">
                                        <input type="checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[is_default_open]" id="whatsapp_default_open_embedded_window_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($is_default_open, "yes") ?> >
                                        <div class="chaty-slider round"></div>
                                        Open the window on load
                                        <span class="icon label-tooltip" data-label="Open the WhatsApp chat popup on page load, after the user sends a message or closes the window, the window will stay closed to avoid disruption"><span class="dashicons dashicons-editor-help"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- advance setting fields: start -->
                    <?php $class_name = !$this->is_pro()?"not-is-pro":""; ?>
                    <div class="chaty-advance-settings <?php esc_attr_e($class_name); ?>" style="<?php echo (empty($close_class) && $social['slug'] == 'Contact_Us')?"display:block":""; ?>">
                        <!-- Settings for custom icon and color -->
                        <div class="chaty-setting-col">
                            <label>Icon Appearance</label>
                            <div>
                                <!-- input for custom color -->
                                <input type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[bg_color]" class="chaty-color-field" value="<?php esc_attr_e($value['bg_color']) ?>" />

                                <!-- button to upload custom image -->
                                <?php if($this->is_pro()) { ?>
                                    <a onclick="upload_chaty_image('<?php echo esc_attr($social['slug']); ?>')" href="javascript:;" class="upload-chaty-icon"><span class="dashicons dashicons-upload"></span> Custom Image</a>

                                    <!-- hidden input value for image -->
                                    <input id="cht_social_image_<?php echo esc_attr($social['slug']); ?>" type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[image_id]" value="<?php esc_attr_e($imageId) ?>" />
                                <?php } else { ?>
                                    <div class="pro-features upload-image">
                                        <div class="pro-item">
                                            <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>" class="upload-chaty-icon"><span class="dashicons dashicons-upload"></span> Custom Image</a>
                                        </div>
                                        <div class="pro-button">
                                            <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clear clearfix"></div>

                        <?php if($social['slug'] == "Link" || $social['slug'] == "Custom_Link" || $social['slug'] == "Custom_Link_3" || $social['slug'] == "Custom_Link_4" || $social['slug'] == "Custom_Link_5") {
                            $channel_type = "";
                            if(isset($value['channel_type'])) {
                                $channel_type = esc_attr__(wp_unslash($value['channel_type']));
                            }
                            $socials = $this->socials;
                            ?>
                            <div class="chaty-setting-col">
                                <label>Channel type</label>
                                <div>
                                    <!-- input for custom title -->
                                    <select class="channel-select-input" name="cht_social_<?php echo esc_attr($social['slug']); ?>[channel_type]" value="<?php esc_attr_e($value['channel_type']) ?>">
                                        <option value="<?php echo esc_attr($social['slug']) ?>">Custom channel</option>
                                        <?php foreach ($socials as $social_icon) {
                                            $selected = ($social_icon['slug'] == $channel_type)?"selected":"";
                                            if ($social_icon['slug'] != 'Custom_Link' && $social_icon['slug'] != 'Custom_Link_3' && $social_icon['slug'] != 'Custom_Link_4' && $social_icon['slug'] != 'Custom_Link_5' && $social_icon['slug'] != 'Contact_Us' && $social_icon['slug'] != 'Link') { ?>
                                                <option <?php echo esc_attr($selected) ?> value="<?php echo esc_attr($social_icon['slug']) ?>"><?php echo esc_attr($social_icon['title']) ?></option>
                                            <?php }
                                        }?>
                                    </select>
                                </div>
                            </div>
                            <div class="clear clearfix"></div>
                        <?php } ?>

                        <div class="chaty-setting-col">
                            <label>On Hover Text</label>
                            <div>
                                <input type="text" class="chaty-title" name="cht_social_<?php echo esc_attr($social['slug']); ?>[title]" value="<?php esc_attr_e($value['title']) ?>">
                            </div>
                        </div>
                        <div class="clear clearfix"></div>

                        <div class="Contact_Us-settings advanced-settings">
                            <div class="clear clearfix"></div>
                            <div class="chaty-setting-col">
                                <label>Contact Form Title</label>
                                <div>
                                    <?php $contact_form_title = isset($value['contact_form_title'])?$value['contact_form_title']:esc_html__("Contact Us", "chaty"); ?>
                                    <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[contact_form_title]" value="<?php esc_attr_e($contact_form_title) ?>" >
                                </div>
                            </div>
                            <?php
                            $fields = array(
                                'name' => array(
                                    'title' => "Name",
                                    'placeholder' => "Enter your name",
                                    'is_required' => 1,
                                    'type' => 'input',
                                    'is_enabled' => 1
                                ),
                                'email' => array(
                                    'title' => "Email",
                                    'placeholder' => "Enter your email address",
                                    'is_required' => 1,
                                    'type' => 'email',
                                    'is_enabled' => 1
                                ),
                                'phone' => array(
	                                'title' => "Phone",
	                                'placeholder' => "Enter your phone number",
	                                'is_required' => 1,
	                                'type' => 'input',
	                                'is_enabled' => 1
                                ),
                                'message' => array(
                                    'title' => "Message",
                                    'placeholder' => "Enter your message",
                                    'is_required' => 1,
                                    'type' => 'textarea',
                                    'is_enabled' => 1
                                )
                            );
                            echo '<div class="form-field-setting-col">';
                            foreach ($fields as $label => $field) {
                                $saved_value = isset($value[$label])?$value[$label]:array();
                                $field_value = array(
                                    'is_active' => (isset($saved_value['is_active']))?$saved_value['is_active']:'yes',
                                    'is_required' => (isset($saved_value['is_required']))?$saved_value['is_required']:'yes',
                                    'placeholder' => (isset($saved_value['placeholder']))?$saved_value['placeholder']:$field['placeholder'],
                                );
                                ?>
                                <div class="field-setting-col">
                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_active]" value="no">
                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_required]" value="no">

                                    <div class="left-section">
                                        <label class="chaty-switch chaty-switch-toggle" for="field_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">
                                            <input type="checkbox" class="chaty-field-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_active]" id="field_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" value="yes" <?php checked($field_value['is_active'], "yes") ?>>
                                            <div class="chaty-slider round"></div>

                                            <?php echo $field['title'] ?>
                                        </label>
                                    </div>
                                    <div class="right-section">
                                        <div class="field-settings <?php echo ($field_value['is_active']=="yes")?"active":"" ?>">
                                            <div class="inline-block">
                                                <label class="inline-block" for="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">Required?</label>
                                                <div class="inline-block">
                                                    <label class="chaty-switch" for="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">
                                                        <input type="checkbox" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][is_required]" id="field_required_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" value="yes" <?php checked($field_value['is_required'], "yes") ?>>
                                                        <div class="chaty-slider round"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear clearfix"></div>
                                    <div class="field-settings <?php echo ($field_value['is_active']=="yes")?"active":"" ?>">
                                        <div class="chaty-setting-col">
                                            <label for="placeholder_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>">Placeholder text</label>
                                            <div>
                                                <input id="placeholder_for_<?php echo esc_attr($social['slug']); ?>_<?php echo $label ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[<?php echo $label ?>][placeholder]" value="<?php esc_attr_e($field_value['placeholder']) ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($label != 'message') { ?>
                                    <div class="chaty-separator"></div>
                                <?php } ?>
                            <?php }
                            echo '</div>'; ?>
                            <div class="form-field-setting-col">
                                <div class="form-field-title">Submit Button</div>
                                <div class="color-box">
                                    <div class="clr-setting">
                                        <?php $field_value = isset($value['button_text_color'])?$value['button_text_color']:"#ffffff" ?>
                                        <div class="chaty-setting-col">
                                            <label for="button_text_color_for_<?php echo esc_attr($social['slug']); ?>">Text color</label>
                                            <div>
                                                <input id="button_text_color_for_<?php echo esc_attr($social['slug']); ?>" class="chaty-color-field button-color" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_text_color]" value="<?php esc_attr_e($field_value); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <?php $field_value = isset($value['button_bg_color'])?$value['button_bg_color']:"#A886CD" ?>
                                    <div class="clr-setting">
                                        <div class="chaty-setting-col">
                                            <label for="button_bg_color_for_<?php echo esc_attr($social['slug']); ?>">Background color</label>
                                            <div>
                                                <input id="button_bg_color_for_<?php echo esc_attr($social['slug']); ?>" class="chaty-color-field button-color" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_bg_color]" value="<?php esc_attr_e($field_value); ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $field_value = isset($value['button_text'])?$value['button_text']:"Chat" ?>
                                <div class="chaty-setting-col">
                                    <label for="button_text_for_<?php echo esc_attr($social['slug']); ?>">Button text</label>
                                    <div>
                                        <input id="button_text_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[button_text]" value="<?php esc_attr_e($field_value); ?>" >
                                    </div>
                                </div>
                                <?php $field_value = isset($value['thanks_message'])?$value['thanks_message']:"Your message was sent successfully" ?>
                                <div class="chaty-setting-col">
                                    <label for="thanks_message_for_<?php echo esc_attr($social['slug']); ?>">Thank you message</label>
                                    <div>
                                        <input id="thanks_message_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[thanks_message]" value="<?php esc_attr_e($field_value); ?>" >
                                    </div>
                                </div>
                                <div class="chaty-separator"></div>
                                <?php $field_value = isset($value['redirect_action'])?$value['redirect_action']:"no" ?>
                                <div class="chaty-setting-col">
                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_action]" value="no" >
                                    <label class="chaty-switch" for="redirect_action_<?php echo esc_attr($social['slug']); ?>">
                                        <input type="checkbox" class="chaty-redirect-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_action]" id="redirect_action_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                        <div class="chaty-slider round"></div>
                                        Redirect visitors after submission
                                    </label>
                                </div>
                                <div class="redirect_action-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                    <?php $field_value = isset($value['redirect_link'])?$value['redirect_link']:"" ?>
                                    <div class="chaty-setting-col">
                                        <label for="redirect_link_for_<?php echo esc_attr($social['slug']); ?>">Redirect link</label>
                                        <div>
                                            <input id="redirect_link_for_<?php echo esc_attr($social['slug']); ?>" placeholder="<?php echo site_url("/") ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[redirect_link]" value="<?php esc_attr_e($field_value); ?>" >
                                        </div>
                                    </div>
                                    <?php $field_value = isset($value['link_in_new_tab'])?$value['link_in_new_tab']:"no" ?>
                                    <div class="chaty-setting-col">
                                        <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_in_new_tab]" value="no" >
                                        <label class="chaty-switch" for="link_in_new_tab_<?php echo esc_attr($social['slug']); ?>">
                                            <input type="checkbox" class="chaty-field-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_in_new_tab]" id="link_in_new_tab_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                            <div class="chaty-slider round"></div>
                                            Open in a new tab
                                        </label>
                                    </div>
                                </div>
                                <div class="chaty-separator"></div>
                                <?php $field_value = isset($value['close_form_after'])?$value['close_form_after']:"no" ?>
                                <div class="chaty-setting-col">
                                    <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after]" value="no" >
                                    <label class="chaty-switch" for="close_form_after_<?php echo esc_attr($social['slug']); ?>">
                                        <input type="checkbox" class="chaty-close_form_after-setting" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after]" id="close_form_after_<?php echo esc_attr($social['slug']); ?>" value="yes" <?php checked($field_value, "yes") ?> >
                                        <div class="chaty-slider round"></div>
                                        Close form automatically after submission
                                        <span class="icon label-tooltip inline-message" data-label="Close the form automatically after a few seconds based on your choice"><span class="dashicons dashicons-editor-help"></span></span>
                                    </label>
                                </div>
                                <div class="close_form_after-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                    <?php $field_value = isset($value['close_form_after_seconds'])?$value['close_form_after_seconds']:"3" ?>
                                    <div class="chaty-setting-col">
                                        <label for="close_form_after_seconds_<?php echo esc_attr($social['slug']); ?>">Close after (Seconds)</label>
                                        <div>
                                            <input id="close_form_after_seconds_<?php echo esc_attr($social['slug']); ?>" type="number" name="cht_social_<?php echo esc_attr($social['slug']); ?>[close_form_after_seconds]" value="<?php esc_attr_e($field_value); ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-field-setting-col no-margin">
                                <input type="hidden" value="no" name="cht_social_<?php echo esc_attr($social['slug']); ?>[send_leads_in_email]" >
                                <input type="hidden" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[save_leads_locally]" >
                                <?php $field_value = isset($val['save_leads_locally'])?$val['save_leads_locally']:"yes" ?>
                                <div class="chaty-setting-col">
                                    <label for="save_leads_locally_<?php echo esc_attr($social['slug']); ?>" class="full-width chaty-switch">
                                        <input type="checkbox" disabled id="save_leads_locally_<?php echo esc_attr($social['slug']); ?>" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[save_leads_locally]" <?php checked($field_value, "yes") ?> >
                                        <div class="chaty-slider round"></div>
                                        Save leads to the local database
                                        <div class="html-tooltip top no-position">
                                            <span class="dashicons dashicons-editor-help"></span>
                                            <span class="tooltip-text top">Your leads will be saved in your local database, you'll be able to find them <a target="_blank" href="<?php echo admin_url("admin.php?page=chaty-contact-form-feed") ?>">here</a></span>
                                        </div>
                                    </label>
                                </div>
                                <?php $field_value = isset($value['send_leads_in_email'])?$value['send_leads_in_email']:"no" ?>
                                <div class="chaty-setting-col">
                                    <label for="save_leads_to_email_<?php echo esc_attr($social['slug']); ?>" class="email-setting full-width chaty-switch">
                                        <input class="email-setting-field" disabled type="checkbox" id="save_leads_to_email_<?php echo esc_attr($social['slug']); ?>" value="yes" name="cht_social_<?php echo esc_attr($social['slug']); ?>[send_leads_in_email]" >
                                        <div class="chaty-slider round"></div>
                                        Send leads to your email
                                        <span class="icon label-tooltip" data-label="Get your leads by email, whenever you get a new email you'll get an email notification"><span class="dashicons dashicons-editor-help"></span></span>
                                        <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>">(<?php esc_attr_e('Upgrade to Pro', CHT_OPT);?>)</a>
                                    </label>
                                </div>
                                <div class="email-settings <?php echo ($field_value == "yes")?"active":"" ?>">
                                    <div class="chaty-setting-col">
                                        <label for="email_for_<?php echo esc_attr($social['slug']); ?>">Email address</label>
                                        <div>
                                            <?php $field_value = isset($value['email_address'])?$value['email_address']:"" ?>
                                            <input id="email_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[email_address]" value="<?php esc_attr_e($field_value); ?>" >
                                        </div>
                                    </div>
                                    <div class="chaty-setting-col">
                                        <label for="sender_name_for_<?php echo esc_attr($social['slug']); ?>">Sender's name</label>
                                        <div>
                                            <?php $field_value = isset($value['sender_name'])?$value['sender_name']:"" ?>
                                            <input id="sender_name_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[sender_name]" value="<?php esc_attr_e($field_value); ?>" >
                                        </div>
                                    </div>
                                    <div class="chaty-setting-col">
                                        <label for="email_subject_for_<?php echo esc_attr($social['slug']); ?>">Email subject</label>
                                        <div>
                                            <?php $field_value = isset($value['email_subject'])?$value['email_subject']:"New lead from Chaty - {name} - {date} {hour}" ?>
                                            <input id="email_subject_for_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[email_subject]" value="<?php esc_attr_e($field_value); ?>" >
                                            <div class="mail-merge-tags"><span>{name}</span><span>{phone}</span><span>{email}</span><span>{date}</span><span>{hour}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($this->is_pro()) { ?>
                            <div class="clear clearfix"></div>
                            <div class="Whatsapp-settings advanced-settings">
                                <!-- advance setting for Whatsapp -->
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Pre Set Message</label>
                                    <div>
                                        <?php $pre_set_message = isset($value['pre_set_message'])?$value['pre_set_message']:""; ?>
                                        <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[pre_set_message]" value="<?php esc_attr_e($pre_set_message) ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="Email-settings advanced-settings">
                                <!-- advance setting for Email -->
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Mail Subject <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                    <div>
                                        <?php $mail_subject = isset($value['mail_subject'])?$value['mail_subject']:""; ?>
                                        <input id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="cht_social_<?php echo esc_attr($social['slug']); ?>[mail_subject]" value="<?php esc_attr_e($mail_subject) ?>" >
                                        <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                    </div>
                                </div>
                            </div>
                            <div class="WeChat-settings advanced-settings">
                                <!-- advance setting for WeChat -->
                                <?php
                                $qr_code = isset($value['qr_code'])?$value['qr_code']:"";                               // Initialize QR code value if not exists. 2.1.0 change
                                $imageUrl = "";
                                $status = 0;
                                if($qr_code != "") {
                                    $imageUrl = wp_get_attachment_image_src($qr_code, "full")[0];                       // get custom Image URL if exists
                                }
                                if($imageUrl == "") {
                                    $imageUrl = plugin_dir_url("")."chaty/admin/assets/images/chaty-default.png";   // Initialize with default image URL if URL is not exists
                                } else {
                                    $status = 1;
                                }
                                ?>
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Upload QR Code</label>
                                    <div>
                                        <!-- Button to upload QR Code image -->
                                        <a class="cht-upload-image <?php esc_attr_e(($status)?"active":"") ?>" id="upload_qr_code" href="javascript:;" onclick="upload_qr_code('<?php echo esc_attr($social['slug']); ?>')">
                                            <img id="cht_social_image_src_<?php echo esc_attr($social['slug']); ?>" src="<?php echo esc_url($imageUrl) ?>" alt="<?php esc_attr_e($value['title']) ?>">
                                            <span class="dashicons dashicons-upload"></span>
                                        </a>

                                        <!-- Button to remove QR Code image -->
                                        <a href="javascript:;" class="remove-qr-code remove-qr-code-<?php echo esc_attr($social['slug']); ?> <?php esc_attr_e(($status)?"active":"") ?>" onclick="remove_qr_code('<?php echo esc_attr($social['slug']); ?>')"><span class="dashicons dashicons-no-alt"></span></a>

                                        <!-- input hidden field for QR Code -->
                                        <input id="upload_qr_code_val-<?php echo esc_attr($social['slug']); ?>" type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[qr_code]" value="<?php esc_attr_e($qr_code) ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="Link-settings Custom_Link-settings Custom_Link_3-settings Custom_Link_4-settings Custom_Link_5-settings advanced-settings">
                                <?php $is_checked = (!isset($value['new_window']) || $value['new_window'] == 1)?1:0; ?>
                                <!-- Advance setting for Custom Link -->
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label >Open In a New Tab</label>
                                    <div>
                                        <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="0" >
                                        <label class="channels__view" for="cht_social_window_<?php echo esc_attr($social['slug']); ?>">
                                            <input id="cht_social_window_<?php echo esc_attr($social['slug']); ?>" type="checkbox" class="channels__view-check" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="1" <?php checked($is_checked, 1) ?> >
                                            <span class="channels__view-txt">&nbsp;</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="Linkedin-settings advanced-settings">
                                <?php $is_checked = isset($value['link_type'])?$value['link_type']:"personal"; ?>
                                <!-- Advance setting for Custom Link -->
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label >LinkedIn</label>
                                    <div>
                                        <label>
                                            <input type="radio" <?php checked($is_checked, "personal") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="personal">
                                            Personal
                                        </label>
                                        <label>
                                            <input type="radio" <?php checked($is_checked, "company") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="company">
                                            Company
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="clear clearfix"></div>
                            <div class="Whatsapp-settings advanced-settings">
                                <?php $pre_set_message = isset($value['pre_set_message'])?$value['pre_set_message']:""; ?>
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Pre Set Message</label>
                                    <div>
                                        <div class="pro-features">
                                            <div class="pro-item">
                                                <div class="pre-message-whatsapp">
                                                    <input disabled id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="" value="<?php esc_attr_e($pre_set_message) ?>" >
                                                    <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                                    <button data-button="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0m0 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10"></path><path d="M8 7a2 2 0 1 0-.001 3.999A2 2 0 0 0 8 7M16 7a2 2 0 1 0-.001 3.999A2 2 0 0 0 16 7M15.232 15c-.693 1.195-1.87 2-3.349 2-1.477 0-2.655-.805-3.347-2H15m3-2H6a6 6 0 1 0 12 0"></path></svg></button>
                                                </div>
                                            </div>
                                            <div class="pro-button">
                                                <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Email-settings advanced-settings">
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Mail Subject <span class="icon label-tooltip inline-tooltip" data-label="Add your own pre-set message that's automatically added to the user's message. You can also use merge tags and add the URL or the title of the current visitor's page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you"><span class="dashicons dashicons-editor-help"></span></span></label>
                                    <div>
                                        <div class="pro-features">
                                            <div class="pro-item">
                                                <input disabled id="cht_social_message_<?php echo esc_attr($social['slug']); ?>" type="text" name="" value="" >
                                                <span class="supported-tags"><span class="icon label-tooltip support-tooltip" data-label="{title} tag grabs the page title of the webpage">{title}</span> and  <span class="icon label-tooltip support-tooltip" data-label="{url} tag grabs the URL of the page">{url}</span> tags are supported</span>
                                            </div>
                                            <div class="pro-button">
                                                <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="WeChat-settings advanced-settings">
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label>Upload QR Code</label>
                                    <div>
                                        <a target="_blank" class="cht-upload-image-pro" id="upload_qr_code" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>" >
                                            <span class="dashicons dashicons-upload"></span>
                                        </a>
                                        <a target="_blank" href="<?php echo esc_url($this->getUpgradeMenuItemUrl());?>"><?php esc_attr_e('Upgrade to Pro', CHT_OPT);?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="Link-settings Custom_Link-settings Custom_Link_3-settings Custom_Link_4-settings Custom_Link_5-settings advanced-settings">
                                <?php $is_checked = 1; ?>
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label >Open In a New Tab</label>
                                    <div>
                                        <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="0" >
                                        <label class="channels__view" for="cht_social_window_<?php echo esc_attr($social['slug']); ?>">
                                            <input id="cht_social_window_<?php echo esc_attr($social['slug']); ?>" type="checkbox" class="channels__view-check" name="cht_social_<?php echo esc_attr($social['slug']); ?>[new_window]" value="1" checked >
                                            <span class="channels__view-txt">&nbsp;</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="Linkedin-settings advanced-settings">
                                <?php $is_checked = "personal"; ?>
                                <!-- Advance setting for Custom Link -->
                                <div class="clear clearfix"></div>
                                <div class="chaty-setting-col">
                                    <label >LinkedIn</label>
                                    <div>
                                        <label>
                                            <input type="radio" <?php checked($is_checked, "personal") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="personal">
                                            Personal
                                        </label>
                                        <label>
                                            <input type="radio" <?php checked($is_checked, "company") ?> name="cht_social_<?php echo esc_attr($social['slug']); ?>[link_type]" value="company">
                                            Company
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

	                    <?php $use_whatsapp_web = isset($value['use_whatsapp_web'])?$value['use_whatsapp_web']:"yes"; ?>
                        <div class="Whatsapp-settings advanced-settings">
                            <div class="clear clearfix"></div>
                            <div class="chaty-setting-col">
                                <label>Whatsapp Web</label>
                                <input type="hidden" name="cht_social_<?php echo esc_attr($social['slug']); ?>[use_whatsapp_web]" value="no" />
                                <div>
                                    <div class="checkbox">
                                        <label for="cht_social_<?php echo esc_attr($social['slug']); ?>_use_whatsapp_web" class="chaty-checkbox">
                                            <input class="sr-only" type="checkbox" id="cht_social_<?php echo esc_attr($social['slug']); ?>_use_whatsapp_web" name="cht_social_<?php echo esc_attr($social['slug']); ?>[use_whatsapp_web]" value="yes" <?php echo checked($use_whatsapp_web, "yes") ?> />
                                            <span></span>
                                            Use Whatsapp Web directly on desktop
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- advance setting fields: end -->


                    <!-- remove social media setting button: start -->
                    <button type="button" class="btn-cancel" data-social="<?php echo esc_attr($social['slug']); ?>">
                        <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(2.26764 0.0615997) rotate(45)" fill="white"/>
                            <rect width="15.6301" height="2.24494" rx="1.12247" transform="translate(13.3198 1.649) rotate(135)" fill="white"/>
                        </svg>
                    </button>
                    <!-- remove social media setting button: end -->
                </div>
            </li>
            <!-- Social media setting box: end -->
            <?php
            $html = ob_get_clean();
            echo json_encode($html);
        }
        wp_die();
    }
    /* function choose_social_handler end */

    /* get social media list for front end widget */
    public function get_social_icon_list()
    {
        $social = get_option('cht_numb_slug'.$this->widget_number); // get saved social media list
        $social = explode(",", $social);

        $arr = array();
        foreach ($social as $key_soc):
            foreach ($this->socials as $key => $social) :       // compare with Default Social media list
                if ($social['slug'] != $key_soc) {
                    continue;                                   // return if slug is not equal
                }
                $value = get_option('cht_social'.$this->widget_number.'_' . $social['slug']);   //  get saved settings for button
                if ($value) {
                    $slug = strtolower($social['slug']);
                    $channel_id = "cht-channel-0";
                    $channel_id = trim($channel_id, "_");
                    if (!empty($value['value']) || $slug == "contact_us") {
                        $slug = strtolower($social['slug']);
                        $url = "";
                        $mobile_url = "";
                        $desktop_target = "";
                        $mobile_target = "";
                        $qr_code_image = "";

                        $channel_type = $slug;

                        if(!isset($value['value'])) {
                            $value['value'] = "";
                        }

                        $svg_icon = $social['svg'];
                        if($slug == "link" || $slug == "custom_link" || $slug == "custom_link_3" || $slug == "custom_link_4" || $slug == "custom_link_5") {
                            if(isset($value['channel_type']) && !empty($value['channel_type'])) {
                                $channel_type = $value['channel_type'];

                                foreach($this->socials as $icon) {
                                    if($icon['slug'] == $channel_type) {
                                        $svg_icon = $icon['svg'];
                                    }
                                }
                            }
                        }

                        $channel_type = strtolower($channel_type);

                        if($channel_type == "viber") {
                            /* Viber change to exclude + from number for desktop */
                            $val = $value['value'];
                            if(is_numeric($val)) {
                                $fc = substr($val, 0, 1);
                                if($fc == "+") {
                                    $length = -1*(strlen($val)-1);
                                    $val = substr($val, $length);
                                }
                                if(!wp_is_mobile()) {
                                    /* Viber change to include + from number for mobile */
                                    $val = "+".$val;
                                }
                            }
                        } else if($channel_type == "whatsapp") {
                            /* Whatspp change to exclude + from phone number */
                            $val = $value['value'];
                            $val = str_replace("+","", $val);
                            $val = str_replace("-","", $val);
                            $val = str_replace(" ","", $val);
                        } else if($channel_type == "facebook_messenger") {
                            /* Facebook change to change URL from facebook.com to m.me version 2.1.0 change */
                            $val = $value['value'];
                            $val = str_replace("facebook.com","m.me", $val);                                    // Replace facebook.com with m.me version 2.0.1 change
                            $val = str_replace("www.","", $val);                                                // Replace www. with blank version 2.0.1 change
                            $value['value'] = $val;

                            $val = trim($val, "/");
                            $val_array = explode("/", $val);
                            $total = count($val_array)-1;
                            $last_value = $val_array[$total];
                            $last_value = explode("-", $last_value);
                            $total_text = count($last_value)-1;
                            $total_text = $last_value[$total_text];

                            if(is_numeric($total_text)) {
                                $val_array[$total] = $total_text;
                                $val = implode("/", $val_array);
                            }
                        } else {
                            $val = $value['value'];
                        }
                        if(!isset($value['title'])) {
                            $value['title'] = $social['title'];         // Initialize title with default title if not exists. version 2.1.0 change
                        }
                        $image_url = "";

                        /* get custom image URL if uploaded. version 2.1.0 change */
                        if(isset($value['image_id']) && !empty($value['image_id'])) {
                            $image_id = $value['image_id'];
                            if(!empty($image_id)) {
                                $image_data = wp_get_attachment_image_src($image_id, "full");
                                if(!empty($image_data) && is_array($image_data)) {
                                    $image_url = $image_data[0];
                                }
                            }
                        }

                        $on_click_fn = "";
                        $popup_html = "";
                        $has_custom_popup = 0;
                        $is_default_open = 0;
                        /* get custom icon background color if exists. version 2.1.0 change */
                        if(!isset($value['bg_color']) || empty($value['bg_color'])) {
                            $value['bg_color'] = '';
                        }
                        if($channel_type == "whatsapp") {
                            /* setting for Whatsapp URL */
                            $val = str_replace("+","",$val);
	                        if(isset($value['use_whatsapp_web']) && $value['use_whatsapp_web'] == "no") {
		                        $url = "https://wa.me/".$val;
	                        } else {
		                        $url = "https://web.whatsapp.com/send?phone=" . $val;
	                        }
                            $mobile_url = "https://wa.me/".$val;
                            // https://wa.me/$number?text=$test
                            if(isset($value['pre_set_message']) && !empty($value['pre_set_message'])) {
	                            if(isset($value['use_whatsapp_web']) && $value['use_whatsapp_web'] == "no") {
		                            $url .= "?text=".rawurlencode($value['pre_set_message']);
	                            } else {
		                            $url .= "&text=".rawurlencode($value['pre_set_message']);
	                            }
                                $mobile_url .= "?text=".rawurlencode($value['pre_set_message']);
                            }
                            if(wp_is_mobile()) {
                                $mobile_target = "";
                            } else {
                                $desktop_target = "_blank";
                            }
                            if(isset($value['embedded_window']) && $value['embedded_window'] == "yes") {
                                $embedded_message = isset($value['embedded_message'])?$value['embedded_message']:"";
                                $pre_set_message = isset($value['pre_set_message'])?$value['pre_set_message']:"";
                                $is_default_open = (isset($value['is_default_open'])&&$value['is_default_open']=="yes")?1:0;
                                $has_custom_popup = 1;
                                $mobile_url = "javascript:;";
                                $url = "javascript:;";
                                $url = "javascript:;";
                                $close_button = "<div role='button' class='close-chaty-popup is-whatsapp-btn'><div class='chaty-close-button'></div></div>";
                                $popup_html = "<div class='chaty-whatsapp-popup'>";
                                $popup_html .= "<span class='default-value' style='display:none'>".esc_attr($pre_set_message)."</span>";
                                $popup_html .= "<span class='default-msg-value' style='display:none'>".esc_attr($embedded_message)."</span>";
                                $popup_html .= "<span class='default-msg-phone' style='display:none'>".esc_attr($val)."</span>";
                                $popup_html .= "<div class='chaty-whatsapp-body'>".$close_button."<div class='chaty-whatsapp-message'></div></div>";
                                $popup_html .= "<div class='chaty-whatsapp-footer'>";
	                            if(isset($value['use_whatsapp_web']) && $value['use_whatsapp_web'] == "no") {
		                            $popup_html .= "<form class='whatsapp-chaty-form' autocomplete='off' target='_blank' action='https://wa.me/".$val."' method='get'>";
	                            } else {
		                            $popup_html .= "<form class='whatsapp-chaty-form' autocomplete='off' target='_blank' action='https://web.whatsapp.com/send' method='get'>";
	                            }
                                $popup_html .= "<div class='chaty-whatsapp-field'><input autocomplete='off' class='chaty-whatsapp-msg' name='text' value='' /></div>";
                                $popup_html .= "<input type='hidden' name='phone' class='chaty-whatsapp-phone' value='' />";
                                $popup_html .= "<input type='hidden' class='is-default-open' value='".esc_attr($is_default_open)."' />";
                                $popup_html .= "<input type='hidden' class='channel-id' value='".esc_attr($channel_id)."' />";
                                $popup_html .= "<button type='submit' class='chaty-whatsapp-submit-btn'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='#ffffff' d='M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z'></path></svg></button><div style='clear:both'></div>";
                                $popup_html .= "</form>";
                                $popup_html .= "</div>";
                                $popup_html .= "</div>";
                            }
                        } else if($channel_type == "phone") {
                            /* setting for Phone */
                            $url = "tel:".$val;
                        } else if($channel_type == "sms") {
                            /* setting for SMS */
                            $url = "sms:".$val;
                        } else if($channel_type == "telegram") {
                            /* setting for Telegram */
	                        $val = ltrim($val, "@");
                            $url = "https://telegram.me/".$val;
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "line" || $channel_type == "google_maps" || $channel_type == "poptin" || $channel_type == "waze" ) {
                            /* setting for Line, Google Map, Link, Poptin, Waze, Custom Link */
                            $url = esc_url($val);
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "link" || $channel_type == "custom_link" || $channel_type == "custom_link_3" || $channel_type == "custom_link_4" || $channel_type == "custom_link_5") {
                            $is_exist = strpos($val, "javascript");
                            $is_viber = strpos($val, "viber");
                            if($is_viber !== false) {
                                $url = $val;
                            } else if($is_exist === false) {
                                $url = esc_url($val);
                                if($channel_type == "custom_link" || $channel_type == "link" || $channel_type == "custom_link_3" || $channel_type == "custom_link_4" || $channel_type == "custom_link_5") {
                                    $desktop_target = (isset($value['new_window']) && $value['new_window'] == 0)?"":"_blank";
                                    $mobile_target = (isset($value['new_window']) && $value['new_window'] == 0)?"":"_blank";
                                }
                            } else {
                                $url = "javascript:;";
                                $on_click_fn = str_replace('"',"'",$val);
                            }
                        }else if($channel_type == "wechat") {
                            /* setting for WeChat */
                            $url = "javascript:;";
                            if(!empty($value['title'])) {
	                            $value['title'] .= ": ".$val;
                            } else {
	                            $value['title'] = $val;
                            }
                            $qr_code = isset($value['qr_code'])?$value['qr_code']:"";
                            if(!empty($qr_code)) {
                                $image_data = wp_get_attachment_image_src($qr_code, "full");
                                if(!empty($image_data) && is_array($image_data)) {
                                    $qr_code_image = $image_data[0];
                                }
                            }
                        } else if($channel_type == "viber") {
                            /* setting for Viber */
                            $url = $val;
                        } else if($channel_type == "snapchat") {
                            /* setting for SnapChat */
                            $url = "https://www.snapchat.com/add/".$val;
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "waze") {
                            /* setting for Waze */
                            $url = "javascript:;";
                            $value['title'] .= ": ".$val;
                        } else if($channel_type == "vkontakte") {
                            /* setting for vkontakte */
                            $url = "https://vk.me/".$val;
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "skype") {
                            /* setting for Skype */
                            $url = "skype:".$val."?chat";
                        } else if($channel_type == "email") {
                            /* setting for Email */
                            $url = "mailto:".$val;
                            $mail_subject = (isset($value['mail_subject']) && !empty($value['mail_subject']))?$value['mail_subject']:"";
                            if($mail_subject != "") {
                                $url .= "?subject=".urlencode($mail_subject);
                            }
                        } else if($channel_type == "facebook_messenger") {
                            /* setting for facebook URL */
                            $url = esc_url($val);
                            $url = str_replace("http:", "https:", $url);
                            if(wp_is_mobile()) {
                                $mobile_target = "";
                            } else {
                                $desktop_target = "_blank";
                            }
                        } else if($channel_type == "twitter") {
                            /* setting for Twitter */
                            $url = "https://twitter.com/".$val;
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "instagram") {
                            /* setting for Instagram */
                            $url = "https://www.instagram.com/".$val;
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "linkedin") {
                            /* setting for Linkedin */
                            $link_type = !isset($value['link_type']) || $value['link_type'] == "company"?"company":"personal";
                            if($link_type == "personal") {
                                $url = "https://www.linkedin.com/in/".$val;
                            } else {
                                $url = "https://www.linkedin.com/company/".$val;
                            }
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "slack") {
                            /* setting for slack */
                            $url = esc_url($val);
                            $desktop_target = "_blank";
                            $mobile_target = "_blank";
                        } else if($channel_type == "contact_us") {
                            $url = "javascript:;";
                            $desktop_target = "";
                            $mobile_target = "";
                            $input_fields = "";
                            if(isset($value['name']) || isset($value['email']) || isset($value['message'])) {
                                $field_setting = $value['name'];
                                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                                    $is_required = (isset($field_setting['is_required']) && $field_setting['is_required'] == "yes")?"is-required":"";
                                    $placeholder = isset($field_setting['placeholder'])?$field_setting['placeholder']:"Enter your name";
                                    $input_fields .= "<div class='chaty-input-area'>";
                                    $input_fields .= "<input autocomplete='off' class='chaty-input-field chaty-field-name {$is_required}' name='name' type='text' id='chaty-name' placeholder='{$placeholder}' />";
                                    $input_fields .= "</div>";
                                }
                                $field_setting = $value['email'];
                                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                                    $is_required = (isset($field_setting['is_required']) && $field_setting['is_required'] == "yes")?"is-required":"";
                                    $placeholder = isset($field_setting['placeholder'])?$field_setting['placeholder']:"Enter your email address";
                                    $input_fields .= "<div class='chaty-input-area'>";
                                    $input_fields .= "<input autocomplete='off' class='chaty-input-field chaty-field-email {$is_required}' name='email' type='email' id='chaty-name' placeholder='{$placeholder}' />";
                                    $input_fields .= "</div>";
                                }
	                            $field_setting = $value['phone'];
	                            if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
		                            $is_required = (isset($field_setting['is_required']) && $field_setting['is_required'] == "yes")?"is-required":"";
		                            $placeholder = isset($field_setting['placeholder'])?$field_setting['placeholder']:"Enter your phone number";
		                            $input_fields .= "<div class='chaty-input-area'>";
		                            $input_fields .= "<input autocomplete='off' class='chaty-input-field chaty-field-phone {$is_required}' name='name' type='text' id='chaty-phone' placeholder='{$placeholder}' />";
		                            $input_fields .= "</div>";
	                            }
                                $field_setting = $value['message'];
                                if(isset($field_setting['is_active']) && $field_setting['is_active'] == "yes") {
                                    $is_required = (isset($field_setting['is_required']) && $field_setting['is_required'] == "yes")?"is-required":"";
                                    $placeholder = isset($field_setting['placeholder'])?$field_setting['placeholder']:"Enter your message";
                                    $input_fields .= "<div class='chaty-input-area'>";
                                    $input_fields .= "<textarea autocomplete='off' class='chaty-input-field chaty-field-message {$is_required}' name='name' id='chaty-name' placeholder='{$placeholder}' ></textarea>";
                                    $input_fields .= "</div>";
                                }
                            }
                            if(!empty($input_fields)) {
                                $has_custom_popup = 1;
                                $button_text = isset($value['button_text']) && !empty($value['button_text'])?$value['button_text']:"Submit";
                                $button_bg_color = isset($value['button_bg_color']) && !empty($value['button_bg_color'])?$value['button_bg_color']:"#A886CD";
                                $button_text_color = isset($value['button_text_color']) && !empty($value['button_text_color'])?$value['button_text_color']:"#ffffff";
                                $contact_form_title = isset($value['contact_form_title'])?$value['contact_form_title']:"";
                                $popup_html = "<div class='chaty-contact-form'>";
                                $popup_html .= "<form action='#' method='post' class='chaty-contact-form-data' autocomplete='off'>";
                                $popup_html .= "<div class='chaty-contact-header'>".esc_attr($contact_form_title)." <div role='button' class='close-chaty-popup'><div class='chaty-close-button'></div></div><div style='clear:both'></div></div>";
                                $popup_html .= "<div class='chaty-contact-body'>";
                                $popup_html .= $input_fields;
                                $popup_html .= "<input type='hidden' class='chaty-field-widget' name='widget_id' value='' />";
                                $popup_html .= "<input type='hidden' class='chaty-field-channel' name='channel' value='{$social['slug']}' />";
                                $nonce = wp_create_nonce("chaty-front-form");
                                $popup_html .= "<input type='hidden' class='chaty-field-nonce' name='nonce' value='{$nonce}' />";
                                $popup_html .= "</div>";
                                $popup_html .= "<div class='chaty-contact-footer'>";
                                $popup_html .= "<button style='color: {$button_text_color}; background: {$button_bg_color}' type='submit' class='chaty-contact-submit-btn' data-text='{$button_text}'>{$button_text}</div>";
                                $popup_html .= "</div>";
                                $popup_html .= "</form>";
                                $popup_html .= "</div>";
                            }
                        } else if($channel_type == "tiktok") {
	                        $val = $value['value'];
	                        $firstCharacter = substr($val, 0, 1);
	                        if($firstCharacter != "@") {
		                        $val = "@".$val;
	                        }
	                        $url = esc_url("https://www.tiktok.com/".$val);
	                        $desktop_target = $mobile_target = "_blank";
                        }

                        /* Instagram checking for custom color */
                        if($channel_type == "instagram" && $value['bg_color'] == "#ffffff") {
                            $value['bg_color'] = "";
                        }

                        $svg = trim(preg_replace('/\s\s+/', '', $svg_icon));

                        $is_mobile = isset($value['is_mobile']) ? 1 : 0;
                        $is_desktop = isset($value['is_desktop']) ? 1 : 0;

                        if(empty($mobile_url)) {
                            $mobile_url = $url;
                        }

                        $svg_class = ($channel_type == "contact_us")?"color-element":"";

                        $svg = '<svg class="ico_d '.$svg_class.'" width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(0deg);">'.$svg.'</svg>';

	                    $rgb_color = $this->getRGBColor($value['bg_color']);
                        $data = array(
                            'val' => esc_attr__(wp_unslash($val)),
                            'default_icon' => $svg,
                            'bg_color' => $value['bg_color'],
                            'rbg_color' => $rgb_color,
                            'title' => esc_attr__(wp_unslash($value['title'])),
                            'img_url' => esc_url($image_url),
                            'social_channel' => $slug,
                            'channel_type' => $channel_type,
                            'href_url' => $url,
                            'desktop_target' => $desktop_target,
                            'mobile_target' => $mobile_target,
                            'qr_code_image' => esc_url($qr_code_image),
                            'channel' => $social['slug'],
                            'is_mobile' => $is_mobile,
                            'is_desktop' => $is_desktop,
                            'mobile_url' => $mobile_url,
                            'on_click' => $on_click_fn,
                            "has_font" => 0,
                            "popup_html" => $popup_html,
                            "has_custom_popup" => $has_custom_popup,
                            "is_default_open" => $is_default_open
                        );
                        $arr[] = $data;
                    }
                }
            endforeach;
        endforeach;
        return $arr;
    }

    public function insert_widget()
    {

    }

	public function getRGBColor($color) {
		if(!empty($color)) {
			if (strpos($color, '#') !== false) {
				$color = $this->hex2rgba($color);
			}
			if (strpos($color, 'rgba(') !== false || strpos($color, 'rgb(') !== false) {
				$color = explode(",", $color);
				$color = str_replace(array("rgba(", "rgb(", ")"), array("","",""), $color);
				$string = "";
				$string .= ((isset($color[0]))?trim($color[0]):"0").",";
				$string .= ((isset($color[1]))?trim($color[1]):"0").",";
				$string .= ((isset($color[2]))?trim($color[2]):"0");
				return $string;
			}
		}
		return "0,0,0";
	}

	public function hex2rgba($color, $opacity = false) {

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
			return $default;

		//Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}

    private function canInsertWidget()
    {
        return get_option('cht_active') && $this->checkChannels();
    }

    private function checkChannels()
    {
        $social = explode(",", get_option('cht_numb_slug'));
        $res = false;
        foreach ($social as $name) {
            $value = get_option('cht_social_' . strtolower($name));
            $res = $res || !empty($value['value']) || ($name == "Contact_Us");
        }
        return $res;
    }

    function get_user_ipaddress() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = sanitize_text_field( wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = sanitize_text_field( wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        }else{
            $ip = sanitize_text_field( wp_unslash($_SERVER['REMOTE_ADDR']));
        }
        return $ip;
    }
}

return new CHT_Frontend();
