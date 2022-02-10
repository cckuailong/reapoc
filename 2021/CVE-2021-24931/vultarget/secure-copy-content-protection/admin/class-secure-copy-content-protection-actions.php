<?php
ob_start();

class Secure_Copy_Content_Protection_Actions {
	private $plugin_name;

	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	public function store_data( $data ) {
		global $wpdb;
		if (isset($data["sccp_action"]) && wp_verify_nonce($data["sccp_action"], 'sccp_action')) {
			$enable_protection = isset($data['sccp_enable_all_posts']) ? true : false;
			$except_types      = isset($data['sccp_except_post_types']) ? json_encode($data['sccp_except_post_types']) : '';
			$protection_text   = isset($data['sccp_notification_text']) ? stripslashes($data['sccp_notification_text']) : __('You cannot copy content of this page', $this->plugin_name);
			$audio             = isset($data['upload_audio_url']) ? trim($data['upload_audio_url']) : "";

			// MailChimp general settings
			$sccp_settings = new Sccp_Settings_Actions($this->plugin_name);

			$mailchimp_res      = ($sccp_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $sccp_settings->ays_get_setting('mailchimp');
			$mailchimp          = json_decode($mailchimp_res, true);
			$mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
			$mailchimp_api_key  = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
			// MailChimp settings
			$old_options = $this->get_data();
			$current_options           = isset( $old_options['styles'] ) && $old_options['styles'] != '' ? $old_options['styles'] : array();
            $old_enable_double_opt_in  = ( isset( $current_options['sccp_enable_mailchimp_optin'] ) && $current_options['sccp_enable_mailchimp_optin'] == 'on' ) ? 'on' : 'off';
            $old_mailchimp_list        = ( isset( $current_options['mailchimp_list'] ) && $current_options['mailchimp_list'] == 'on' ) ? 'on' : 'off';
			$old_enable_double_opt_in_option = ! array_key_exists( 'sccp_enable_mailchimp_optin', $current_options ) ? false : true;
			
			// MailChimp
            $enable_mailchimp          = isset($data['ays_enable_mailchimp']) && $data['ays_enable_mailchimp'] == 'on' ? "on": "off";
            $mailchimp_list            = !isset($data['ays_mailchimp_list'])?"":$data['ays_mailchimp_list'];
			// Mailchimp double opt-in
			$sccp_mailchimp_optin      = isset($data['ays_sccp_enable_double_opt_in']) && $data['ays_sccp_enable_double_opt_in'] == 'on' ? "on" : "off";
            if( $old_enable_double_opt_in_option ){
                if( $old_enable_double_opt_in != $sccp_mailchimp_optin || $mailchimp_list != $old_mailchimp_list ){
                    $updated_mailchip_list_data = Secure_Copy_Content_Protection_Admin::ays_add_mailchimp_update_list( $mailchimp_username, $mailchimp_api_key, $mailchimp_list, array(
                        'double_optin' => $sccp_mailchimp_optin
                    ) );
                }
            }
			
			// Copyright word
			$sccp_enable_copyright_word     = isset($data['ays_sccp_enable_copyright_word']) && $data['ays_sccp_enable_copyright_word'] == 'on' ? "on": "off";
			$sccp_copyright_word            = isset($data['ays_sccp_copyright_word']) && $data['ays_sccp_copyright_word'] != '' ? sanitize_text_field($data['ays_sccp_copyright_word']) : "";

			// Bg image positioning
        	$tooltip_bg_image_position = (isset($data['ays_sccp_tooltip_bg_image_position']) && $data['ays_sccp_tooltip_bg_image_position'] != "") ? $data['ays_sccp_tooltip_bg_image_position'] : 'center center';

        	// Box Shadow X offset
            $sccp_box_shadow_x_offset = (isset($data['ays_sccp_box_shadow_x_offset']) && sanitize_text_field( $data['ays_sccp_box_shadow_x_offset'] ) != '') ? intval( sanitize_text_field( $data['ays_sccp_box_shadow_x_offset'] ) ) : 0;

            // Box Shadow Y offset
            $sccp_box_shadow_y_offset = (isset($data['ays_sccp_box_shadow_y_offset']) && sanitize_text_field( $data['ays_sccp_box_shadow_y_offset'] ) != '') ? intval( sanitize_text_field( $data['ays_sccp_box_shadow_y_offset'] ) ) : 0;

            // Box Shadow Z offset
            $sccp_box_shadow_z_offset = (isset($data['ays_sccp_box_shadow_z_offset']) && sanitize_text_field( $data['ays_sccp_box_shadow_z_offset'] ) != '') ? intval( sanitize_text_field( $data['ays_sccp_box_shadow_z_offset'] ) ) : 15;

			// Bg image positioning
        	$tooltip_bg_image_object_fit = (isset($data['ays_sccp_tooltip_bg_image_object_fit']) && $data['ays_sccp_tooltip_bg_image_object_fit'] != "") ? $data['ays_sccp_tooltip_bg_image_object_fit'] : 'cover';
			
			$options           = array(
				"left_click"      => (isset($data["sccp_enable_left_click"])) ? "checked" : "",
				"developer_tools" => (isset($data["sccp_enable_developer_tools"])) ? "checked" : "",

				"select_all"      => (isset($data["sccp_select_all"])) ? "checked" : "",
				"select_all_mess" => (isset($data["sccp_select_all_mess"])) ? "checked" : "",
				"select_all_audio"=> (isset($data["sccp_select_all_audio"])) ? "checked" : "",
				"context_menu"    => (isset($data["sccp_enable_context_menu"])) ? "checked" : "",
				"rclick_img"      => (isset($data["sccp_disabled_rclick_img"])) ? "checked" : "",

				"drag_start"      => (isset($data["sccp_enable_drag_start"])) ? "checked" : "",
				"mobile_img"      => (isset($data["sccp_enable_mobile_img"])) ? "checked" : "",
				"ctrlc"           => (isset($data["sccp_enable_ctrlc"])) ? "checked" : "",
				"ctrlv"           => (isset($data["sccp_enable_ctrlv"])) ? "checked" : "",
				"ctrls"           => (isset($data["sccp_enable_ctrls"])) ? "checked" : "",
				"ctrla"           => (isset($data["sccp_enable_ctrla"])) ? "checked" : "",
				"ctrlx"           => (isset($data["sccp_enable_ctrlx"])) ? "checked" : "",
				"ctrlu"           => (isset($data["sccp_enable_ctrlu"])) ? "checked" : "",
				"ctrlf"           => (isset($data["sccp_enable_ctrlf"])) ? "checked" : "",
				"ctrlp"           => (isset($data["sccp_enable_ctrlp"])) ? "checked" : "",
				"ctrlh"           => (isset($data["sccp_enable_ctrlh"])) ? "checked" : "",
				"ctrll"           => (isset($data["sccp_enable_ctrll"])) ? "checked" : "",
				"ctrlk"           => (isset($data["sccp_enable_ctrlk"])) ? "checked" : "",
				"ctrlo"           => (isset($data["sccp_enable_ctrlo"])) ? "checked" : "",
				"sccp_f6"         => (isset($data["sccp_enable_f6"])) ? "checked" : "",
				"sccp_f3"         => (isset($data["sccp_enable_f3"])) ? "checked" : "",
				"sccp_altd"       => (isset($data["sccp_enable_altd"])) ? "checked" : "",
				"sccp_ctrle"      => (isset($data["sccp_enable_ctrle"])) ? "checked" : "",
				"f12"             => (isset($data["sccp_enable_f12"])) ? "checked" : "",
				"printscreen"     => (isset($data["sccp_enable_printscreen"])) ? "checked" : "",

				"left_click_mess"      => (isset($data["sccp_enable_left_click_mess"])) ? "checked" : "",
				"developer_tools_mess" => (isset($data["sccp_enable_developer_tools_mess"])) ? "checked" : "",
				"context_menu_mess"    => (isset($data["sccp_enable_context_menu_mess"])) ? "checked" : "",
				"rclick_img_mess"      => (isset($data["sccp_disabled_rclick_img_mess"])) ? "checked" : "",
				"mobile_img_mess"      => (isset($data["sccp_enable_mobile_img_mess"])) ? "checked" : "",
				"msg_only_once"        => (isset($data["sccp_show_msg_only_once"])) ? "checked" : "",
				"disable_js"      	   => (isset($data["sccp_access_disable_js"])) ? "checked" : "",
				"disable_js_msg"       => isset($data['ays_disabled_js_msg']) ? stripslashes($data['ays_disabled_js_msg']) : __('Javascript not detected. Javascript required for this site to function. Please enable it in your browser settings and refresh this page.', $this->plugin_name),
				"drag_start_mess"      => (isset($data["sccp_enable_drag_start_mess"])) ? "checked" : "",
				"ctrlc_mess"           => (isset($data["sccp_enable_ctrlc_mess"])) ? "checked" : "",
				"ctrlv_mess"           => (isset($data["sccp_enable_ctrlv_mess"])) ? "checked" : "",
				"ctrls_mess"           => (isset($data["sccp_enable_ctrls_mess"])) ? "checked" : "",
				"ctrla_mess"           => (isset($data["sccp_enable_ctrla_mess"])) ? "checked" : "",
				"ctrlx_mess"           => (isset($data["sccp_enable_ctrlx_mess"])) ? "checked" : "",
				"ctrlu_mess"           => (isset($data["sccp_enable_ctrlu_mess"])) ? "checked" : "",
				"ctrlf_mess"           => (isset($data["sccp_enable_ctrlf_mess"])) ? "checked" : "",
				"ctrlp_mess"           => (isset($data["sccp_enable_ctrlp_mess"])) ? "checked" : "",
				"ctrlh_mess"           => (isset($data["sccp_enable_ctrlh_mess"])) ? "checked" : "",
				"ctrll_mess"           => (isset($data["sccp_enable_ctrll_mess"])) ? "checked" : "",
				"ctrlk_mess"           => (isset($data["sccp_enable_ctrlk_mess"])) ? "checked" : "",
				"ctrlo_mess"           => (isset($data["sccp_enable_ctrlo_mess"])) ? "checked" : "",
				"f6_mess"          	   => (isset($data["sccp_enable_f6_mess"])) ? "checked" : "",
				"f3_mess"          	   => (isset($data["sccp_enable_f3_mess"])) ? "checked" : "",
				"altd_mess"            => (isset($data["sccp_enable_altd_mess"])) ? "checked" : "",
				"ctrle_mess"           => (isset($data["sccp_enable_ctrle_mess"])) ? "checked" : "",
				"f12_mess"             => (isset($data["sccp_enable_f12_mess"])) ? "checked" : "",
				"printscreen_mess"     => (isset($data["sccp_enable_printscreen_mess"])) ? "checked" : "",

				"left_click_audio"      => (isset($data["sccp_enable_left_click_audio"])) ? "checked" : "",
				"developer_tools_audio" => (isset($data["sccp_enable_developer_tools_audio"])) ? "checked" : "",
				"right_click_audio"     => (isset($data["sccp_enable_right_click_audio"])) ? "checked" : "",
				"rclick_img_audio"      => (isset($data["sccp_disabled_rclick_img_audio"])) ? "checked" : "",
				"drag_start_audio"      => (isset($data["sccp_enable_drag_start_audio"])) ? "checked" : "",
				"mobile_img_audio"      => (isset($data["sccp_enable_mobile_img_audio"])) ? "checked" : "",
				"exclude_inp_textarea"  => (isset($data["sccp_exclude_inp_textarea"])) ? "checked" : "",
				"exclude_css_selector"  => (isset($data["sccp_exclude_css_selector"])) ? "checked" : "",
				"ctrlc_audio"           => (isset($data["sccp_enable_ctrlc_audio"])) ? "checked" : "",
				"ctrlv_audio"           => (isset($data["sccp_enable_ctrlv_audio"])) ? "checked" : "",
				"ctrls_audio"           => (isset($data["sccp_enable_ctrls_audio"])) ? "checked" : "",
				"ctrla_audio"           => (isset($data["sccp_enable_ctrla_audio"])) ? "checked" : "",
				"ctrlx_audio"           => (isset($data["sccp_enable_ctrlx_audio"])) ? "checked" : "",
				"ctrlu_audio"           => (isset($data["sccp_enable_ctrlu_audio"])) ? "checked" : "",
				"ctrlf_audio"           => (isset($data["sccp_enable_ctrlf_audio"])) ? "checked" : "",
				"ctrlp_audio"           => (isset($data["sccp_enable_ctrlp_audio"])) ? "checked" : "",
				"ctrlh_audio"           => (isset($data["sccp_enable_ctrlh_audio"])) ? "checked" : "",
				"ctrll_audio"           => (isset($data["sccp_enable_ctrll_audio"])) ? "checked" : "",
				"ctrlk_audio"           => (isset($data["sccp_enable_ctrlk_audio"])) ? "checked" : "",
				"ctrlo_audio"           => (isset($data["sccp_enable_ctrlo_audio"])) ? "checked" : "",
				"f6_audio"           	=> (isset($data["sccp_enable_f6_audio"])) ? "checked" : "",
				"f3_audio"           	=> (isset($data["sccp_enable_f3_audio"])) ? "checked" : "",
				"altd_audio"           	=> (isset($data["sccp_enable_altd_audio"])) ? "checked" : "",
				"ctrle_audio"           => (isset($data["sccp_enable_ctrle_audio"])) ? "checked" : "",
				"f12_audio"             => (isset($data["sccp_enable_f12_audio"])) ? "checked" : "",
				"printscreen_audio"     => (isset($data["sccp_enable_printscreen_audio"])) ? "checked" : "",

				"enable_text_selecting" => (isset($data["sccp_enable_text_selecting"])) ? "checked" : "",
				"timeout"               => (isset($data["sscp_timeout"]) && $data["sscp_timeout"] > 0) ? absint($data["sscp_timeout"]) : 1000,
				"bc_header_text"        => isset($data['sccp_bc_header_text']) ? stripslashes($data['sccp_bc_header_text']) : __('You need to Enter right password', $this->plugin_name),
				"sccp_bc_button_position"        => (isset($data['sccp_bc_button_position']) && $data['sccp_bc_button_position'] != '' )? $data['sccp_bc_button_position'] : 'next-to',
				"subs_to_view_header_text"        => isset($data['sccp_subscribe_block_header_text']) ? stripslashes($data['sccp_subscribe_block_header_text']) : __('Subscribe', $this->plugin_name),
				"sccp_sub_block_button_position"        => (isset($data['sccp_sub_block_button_position']) && $data['sccp_sub_block_button_position'] != '' )? $data['sccp_sub_block_button_position'] : 'next-to',
				"enable_copyright_text" => (isset($data["sccp_enable_copyright_text"]) && sanitize_text_field( $data['sccp_enable_copyright_text'] ) == 'on') ? "on" : "off",
				"copyright_text" => (isset($data["sccp_copyright_text"]) && sanitize_text_field( $data['sccp_copyright_text'] ) != '') ? $data["sccp_copyright_text"] : "",
				"copyright_include_url" => (isset($data["sccp_copyright_include_url"]) && sanitize_text_field( $data['sccp_copyright_include_url'] ) == 'on') ? "on" : "off",

				"enable_sccp_copyright_word" => $sccp_enable_copyright_word,
				"sccp_copyright_word" => $sccp_copyright_word

			);
			$styles            = array(
				"bg_color"         		=> isset($data['bg_color']) ? $data['bg_color'] : "#ffffff",
				"bg_image"         		=> isset($data['ays_sccp_bg_image']) ? $data['ays_sccp_bg_image'] : "",
				"tooltip_opacity"  		=> isset($data['ays_sccp_tooltip_opacity']) ? $data['ays_sccp_tooltip_opacity'] : "1",
				"text_color"       		=> isset($data['text_color']) ? $data['text_color'] : "#ff0000",
				"font_size"        		=> isset($data['font_size']) ? $data['font_size'] : "12",
				"border_color"     		=> isset($data['border_color']) ? $data['border_color'] : "#b7b7b7",
				"boxshadow_color"  		=> isset($data['boxshadow_color']) ? $data['boxshadow_color'] : "",
				"sccp_box_shadow_x_offset"  	=> $sccp_box_shadow_x_offset,
				"sccp_box_shadow_y_offset"  	=> $sccp_box_shadow_y_offset,
				"sccp_box_shadow_z_offset"  	=> $sccp_box_shadow_z_offset,
				"border_width"     		=> isset($data['border_width']) ? $data['border_width'] : "1",
				"border_radius"    		=> isset($data['border_radius']) ? $data['border_radius'] : "3",
				"border_style"     		=> isset($data['border_style']) ? $data['border_style'] : "solid",
				"tooltip_position" 		=> isset($data['tooltip_position']) ? $data['tooltip_position'] : "mouse",
				"tooltip_padding"  		=> isset($data['ays_tooltip_padding']) ? $data['ays_tooltip_padding'] : "5",
				"ays_sccp_custom_class" => isset($data['ays_sccp_custom_class']) ? $data['ays_sccp_custom_class'] : "",
				"exclude_css_selectors" => isset($data['ays_sccp_exclude_css_selectors']) ? $data['ays_sccp_exclude_css_selectors'] : "",
				'mailchimp_list'        => $mailchimp_list,
				'enable_mailchimp'      => $enable_mailchimp,
				"custom_css"       		=> isset($data['custom_css']) ? wp_unslash(stripslashes( esc_attr( $data['custom_css'] ) ) ) : "",
				"sccp_enable_mailchimp_optin" => $sccp_mailchimp_optin,
				"tooltip_bg_image_position" => $tooltip_bg_image_position,
				"tooltip_bg_image_object_fit" => $tooltip_bg_image_object_fit,
			);

			$blockcont_pass 	= isset($data['sccp_blockcont_pass']) ? $data['sccp_blockcont_pass'] : false;			
			$blockcont_id   	= isset($data['sccp_blockcont_id']) ? $data['sccp_blockcont_id'] : false;
			$delete_ids   		= isset($data['deleted_ids']) ? $data['deleted_ids'] : false;

			foreach ($blockcont_id as $bc_id) {				
				$limit_user_roles[$bc_id] = !isset($data['ays_users_roles_'.$bc_id]) ? array() : $data['ays_users_roles_'.$bc_id];
				$blockcont_role = isset($data['ays_users_roles_'.$bc_id]) && !empty($data['ays_users_roles_'.$bc_id]) ? true : false;
				$bc_schedule_from[$bc_id] = !isset($data['bc_schedule_from_'.$bc_id]) ? '' : $data['bc_schedule_from_'.$bc_id];
				$bc_schedule_to[$bc_id]   = !isset($data['bc_schedule_to_'.$bc_id]) ? '' : $data['bc_schedule_to_'.$bc_id];
				$bc_pass_limit[$bc_id] = !isset($data['bc_pass_limit_'.$bc_id]) ? '' : intval($data['bc_pass_limit_'.$bc_id]);
				$bc_user_role_count[$bc_id] = !isset($data['bc_user_role_count_'.$bc_id]) ? 0 : intval($data['bc_user_role_count_'.$bc_id]);
			}			

			if ($delete_ids) {
				$delete_ids = explode(',', $delete_ids);
				foreach ( $delete_ids as $value ) {
					$wpdb->delete( esc_sql(SCCP_BLOCK_CONTENT),
					        array(
					            'id' => esc_sql($value)
					        ),
					    array( '%d' )
					);
				}
			}			

			if ($blockcont_pass) {
				foreach ( $blockcont_pass as $key => $value ) {
					$id    = esc_sql($blockcont_id[$key]);
					$bc_table = esc_sql(SCCP_BLOCK_CONTENT);

					$bc_result = $wpdb->get_row(
						    $wpdb->prepare( 'SELECT * FROM '. $bc_table .' WHERE id = %d',
						        $id
						    )
						);
					$sccp_wpdb_id = isset($bc_result->id) ? absint( intval($bc_result->id)) : null;
					$bc_result_options = isset($bc_result->options) ? json_decode($bc_result->options, true) : array();
					$bc_pass_count = isset($bc_result_options['pass_count']) ? intval($bc_result_options['pass_count']) : 0;

					$bc_options = array(
						'user_role'	 		 =>  $limit_user_roles[$id],
						'pass_count'		 =>  $bc_pass_count,
						'pass_limit'		 =>  $bc_pass_limit[$id],
						'user_role_count'	 =>  $bc_user_role_count[$id],
						'bc_schedule_from'	 =>  $bc_schedule_from[$id],
						'bc_schedule_to'	 =>  $bc_schedule_to[$id]
					);
					
					$bc_options = json_encode($bc_options);	
					$block_pass = esc_sql($blockcont_pass[$key]);
					$block_options = $bc_options;

					if ($sccp_wpdb_id != $id) {
						$wpdb->insert( $bc_table,
					        array(						            
					            'password' 	=> $block_pass,
					            'options' 	=> $block_options
					        ),
						    array( '%s', '%s' )
						);
					}else{
						$wpdb->update( $bc_table,
					        array(
					            'password' 	=> $block_pass,
					            'options' 	=> $block_options
					        ),
					        array( 'id' => $id ),
						    array( '%s', '%s' ),
						    array( '%d' )
						);
					}		
				}				
			};

			$sccp_table = esc_sql(SCCP_TABLE);
			$sql = "SELECT COUNT(*) FROM " . $sccp_table;
			$count = $wpdb->get_var($sql);

			if ($count == 0) {
				$result = $wpdb->insert(
					$sccp_table,
					array(
						"protection_text"   => $protection_text,
						"except_post_types" => $except_types,
						"protection_status" => $enable_protection,
						"blocked_ips"       => "",
						"styles"            => json_encode($styles),
						"options"           => json_encode($options),
						"audio"             => $audio,
					),
					array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
				);
			} else {
				$result = $wpdb->update(
					$sccp_table,
					array(
						"protection_text"   => $protection_text,
						"except_post_types" => $except_types,
						"protection_status" => $enable_protection,
						"blocked_ips"       => "",
						"styles"            => json_encode($styles),
						"options"           => json_encode($options),
						"audio"             => $audio
					),
					array('id' => 1),
					array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' ),
					array( '%d' )
				);
			}

			$sccp_tab = isset($data['sccp_tab']) ? $data['sccp_tab'] : 'tab1';
			if ($result >= 0) {
				$url = esc_url_raw(add_query_arg(array(
					"sccp_tab" => $sccp_tab,
					"status"   => "saved"
				)));
				wp_redirect($url);
			}

		}
	}

	public function get_data() {
		global $wpdb;
		$sccp_table = esc_sql(SCCP_TABLE);
		$sql = "SELECT * FROM " . $sccp_table . " WHERE id=1";
		$data = $wpdb->get_row($sql, ARRAY_A);

		if (!empty($data)) {
			$enable_protection = (isset($data['protection_status']) && $data['protection_status'] == 1) ? "checked" : "";
			$except_types      = (isset($data['except_post_types'])) ? json_decode($data['except_post_types']) : array();
			$protection_text   = (isset($data['protection_text']) && $data['protection_text'] != "") ? wpautop(stripslashes($data['protection_text'])) : __('You cannot copy content of this page', $this->plugin_name);
			$audio             = (isset($data['audio']) && $data['audio'] != "") ? $data['audio'] : '';
			$styles            = (isset($data['styles']) && $data['styles'] != "") ? json_decode($data['styles'], true) : array(
				"bg_color"         		=> "#ffffff",
				"bg_image"         		=> "",
				"tooltip_opacity"  		=> "1",
				"text_color"       		=> "#ff0000",
				"font_size"        		=> "12",
				"border_color"     		=> "#b7b7b7",
				"boxshadow_color"     	=> "",
				'sccp_box_shadow_x_offset' => 0,
			    'sccp_box_shadow_y_offset' => 0,
			    'sccp_box_shadow_z_offset' => 15,
				"border_width"     		=> "1",
				"border_radius"    		=> "3",
				"border_style"     		=> "solid",
				"tooltip_position" 		=> "mouse",
				"tooltip_padding"  		=> "5",
				"ays_sccp_custom_class" => "",
				"exclude_css_selectors" => "",
				"tooltip_bg_image_position"	=> "center center",
				"custom_css"       		=> "",
			);
			$options           = (isset($data['options']) && $data['options'] != "") ? json_decode($data['options'], true) : array(
				"left_click"            => "",
				"developer_tools"       => "checked",
				"select_all"            => "",
				"select_all_mess"       => "",
				"select_all_audio"      => "",
				"context_menu"          => "checked",
				"rclick_img"            => "",
				"mobile_img"            => "",
				"drag_start"            => "checked",
				"ctrlc"                 => "checked",
				"ctrlv"                 => "checked",
				"ctrls"                 => "checked",
				"ctrla"                 => "checked",
				"ctrlx"                 => "checked",
				"ctrlu"                 => "checked",
				"ctrlf"                 => "",
				"ctrlp"                 => "checked",
				"ctrlh"                 => "",
				"ctrll"                 => "",
				"ctrlk"                 => "",
				"ctrlo"                 => "",
				"sccp_f6"               => "",
				"sccp_f3"               => "",
				"sccp_altd"             => "",
				"f12"                   => "checked",
				"printscreen"           => "checked",
				"left_click_mess"       => "",
				"developer_tools_mess"  => "checked",
				"context_menu_mess"     => "checked",
				"rclick_img_mess"       => "",
				"mobile_img_mess"       => "",
				"msg_only_once"       	=> "",
				"disable_js"       		=> "",
				"drag_start_mess"       => "checked",
				"ctrlc_mess"            => "checked",
				"ctrlv_mess"            => "checked",
				"ctrls_mess"            => "checked",
				"ctrla_mess"            => "checked",
				"ctrlx_mess"            => "checked",
				"ctrlu_mess"            => "checked",
				"ctrlf_mess"            => "",
				"ctrlp_mess"            => "checked",
				"ctrlh_mess"            => "",
				"ctrll_mess"            => "",
				"ctrlk_mess"            => "",
				"ctrlo_mess"            => "",
				"f6_mess"            	=> "",
				"f3_mess"            	=> "",
				"altd_mess"            	=> "",
				"f12_mess"              => "checked",
				"printscreen_mess"      => "checked",
				"enable_text_selecting" => "",
				"enable_copyright_text"	=> "off",
				"copyright_text"		=> "",
				"copyright_include_url"	=> "off",

			);
		} else {
			$enable_protection = "checked";
			$except_types      = array();
			$protection_text   = __('You cannot copy content of this page', $this->plugin_name);
			$audio             = '';
			$styles            = array(
				"bg_color"         		=> "#ffffff",
				"bg_image"         		=> "",
				"tooltip_opacity"  		=> "1",
				"text_color"       		=> "#ff0000",
				"font_size"        		=> "12",
				"border_color"     		=> "#b7b7b7",
				"boxshadow_color"     	=> "",
				'sccp_box_shadow_x_offset' => 0,
			    'sccp_box_shadow_y_offset' => 0,
			    'sccp_box_shadow_z_offset' => 15,				
				"border_width"     		=> "1",
				"border_radius"    		=> "3",
				"border_style"     		=> "solid",
				"tooltip_position" 		=> "mouse",
				"tooltip_padding"  		=> "5",
				"ays_sccp_custom_class" => "",				
				"exclude_css_selectors" => "",
				'tooltip_bg_image_position'	=> 'center center',
				"custom_css"      		=> "",
			);
			$options           = array(
				"left_click"            => "",
				"developer_tools"       => "checked",
				"select_all"            => "",
				"select_all_mess"       => "",
				"select_all_audio"      => "",
				"context_menu"          => "checked",
				"rclick_img"            => "",
				"mobile_img"            => "",
				"drag_start"            => "checked",
				"ctrlc"                 => "checked",
				"ctrlv"                 => "checked",
				"ctrls"                 => "checked",
				"ctrla"                 => "checked",
				"ctrlx"                 => "checked",
				"ctrlu"                 => "checked",
				"ctrlf"                 => "",
				"ctrlp"                 => "checked",
				"ctrlh"                 => "",
				"ctrll"                 => "",
				"ctrlk"                 => "",
				"ctrlo"                 => "",
				"sccp_f6"               => "",
				"sccp_f3"               => "",
				"sccp_altd"             => "",
				"f12"                   => "checked",
				"printscreen"           => "checked",
				"left_click_mess"       => "",
				"developer_tools_mess"  => "checked",
				"context_menu_mess"     => "checked",
				"rclick_img_mess"       => "",
				"drag_start_mess"       => "checked",
				"mobile_img_mess"       => "",
				"msg_only_once"      	=> "",
				"disable_js"      		=> "",
				"ctrlc_mess"            => "checked",
				"ctrlv_mess"            => "checked",
				"ctrls_mess"            => "checked",
				"ctrla_mess"            => "checked",
				"ctrlx_mess"            => "checked",
				"ctrlu_mess"            => "checked",
				"ctrlf_mess"            => "",
				"ctrlp_mess"            => "checked",
				"ctrlh_mess"            => "",
				"ctrll_mess"            => "",
				"ctrlk_mess"            => "",
				"ctrlo_mess"            => "",
				"f6_mess"            	=> "",
				"f3_mess"            	=> "",
				"altd_mess"            	=> "",
				"f12_mess"              => "checked",
				"printscreen_mess"      => "checked",
				"enable_text_selecting" => "",
				"enable_copyright_text"	=> "off",
				"copyright_text"		=> "",
				"copyright_include_url"	=> "off",
			);
		}
		$bc_table = esc_sql(SCCP_BLOCK_CONTENT);
		$sql = "SELECT * FROM " . $bc_table . " ORDER BY `id`";
		$block_content = $wpdb->get_results($sql, ARRAY_A);

		if (!empty($block_content)) {
			$block_content_data = $block_content;
		} else {
			$block_content_data = array(
				array(
					"password"  => "",
					"options" 	=> "",
				)
			);
		}

		return array(
			"enable_protection" 	=> $enable_protection,
			"except_types"      	=> $except_types,
			"protection_text"   	=> $protection_text,
			"styles"            	=> $styles,
			"options"           	=> $options,
			"audio"             	=> $audio,
			"block_content_data"    => $block_content_data
		);
	}

	public function sccp_get_bc_last_id(){
		global $wpdb;
		$bc_table = esc_sql(SCCP_BLOCK_CONTENT);

		$lastId = $wpdb->get_row(
		    $wpdb->prepare( 'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s',
		        $wpdb->dbname, $bc_table
		    )
		);

		return $lastId;
	}

	public function sccp_protection_notices() {
		$status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

		if (empty($status)) {
			return;
		}
		$updated_message = '';
		if ($status == 'saved') {
			$updated_message = esc_html(__('Changes saved.', $this->plugin_name));
		} 

		if (empty($updated_message)) {
			return;
		}
		
		$content = '<div class="notice notice-success is-dismissible" style="margin-top:20px">
						<p>
							'.$updated_message.'
						</p>
					</div>';
		echo $content;
		
	}
}