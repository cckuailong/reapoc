<?php
/**
 *     Checks if username and access_token exist, if not redirects to settings page
 */
function wdi_check_necessary_params() {
  global $wdi_options;
  if ( empty($wdi_options['wdi_authenticated_users_list']) ) {
    WDILibrary::redirect(add_query_arg(array(
                                         'page' => 'wdi_settings',
                                       ), admin_url('admin.php')));
  }
}

/**
 * checks if argument is 1 it displays success message on settings page after uninstalling plugin
 * else it displays error message already uninstalled
 */
function wdi_uninstall_notice( $arg ) {
  if ( $arg == 1 ) {
    ?>
    <div class="updated">
      <p><?php _e('Succesfully Uninstalled!', 'wd-instagram-feed'); ?></p>
    </div>
    <?php
  }
  else {
    ?>
    <div class="error">
      <p><?php _e('Already Unistalled', 'wd-instagram-feed'); ?></p>
    </div>
    <?php
  }
}

/**
 * triggered on plugin activation
 * adds some plugin related options like plugin version and database version
 * also adds uninstalled option
 * after adding this basic options,creates tables for plugin in wordpress database
 */
function wdi_install() {
  wdi_get_options();
  global $wdi_options;
  wp_schedule_event(time(), 'wdi_autoupdate_interval', 'wdi_schedule_event_hook');
  //delete wdi_admin_check transient recheck for updates on plugin activation
  delete_transient('wdi_update_check');
  $current_version = WDI_VERSION;
  $saved_version = get_option('wdi_version');
  wdi_parse_old_options($current_version, $saved_version);
  if ( $saved_version ) {
    if ( substr($saved_version, 0, 1) == '2' && substr($current_version, 0, 1) == '1' ) {
      wdi_set_options_defaults();

      return;
    }
    $old_version = substr($saved_version, 2);
    $new_version = substr(WDI_VERSION, 2);
    $newer = version_compare($new_version, $old_version, '>');
    if ( $newer ) {
      require_once WDI_DIR . '/update/wdi_update.php';

      /*adds new params for new versions*/
      wdi_update_diff($new_version, $old_version);
      if ( !update_option('wdi_version', WDI_VERSION) ) {
        add_option('wdi_version', WDI_VERSION);
      }
    }
    wdi_set_options_defaults();
    wdi_generate_themes_files();

    return;
  }
  wdi_set_options_defaults();

  global $wpdb;
  $table_name = $wpdb->prefix . WDI_FEED_TABLE;
  $charset_collate = 'COLLATE utf8_general_ci';
  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    feed_name tinytext NOT NULL,
    feed_thumb varchar(800) NOT NULL,
    thumb_user varchar(30) NOT NULL,
    published varchar(1) NOT NULL,
    theme_id varchar(10) NOT NULL,
    feed_users varchar(2000) NOT NULL,
    feed_display_view varchar(30) NOT NULL,
    sort_images_by varchar(30) NOT NULL,
    display_order varchar(30) NOT NULL,
    follow_on_instagram_btn varchar(1) NOT NULL,
    display_header varchar(1) NOT NULL,
    number_of_photos varchar(10) NOT NULL,
    load_more_number varchar(10) NOT NULL,
    pagination_per_page_number varchar(10) NOT NULL,
    pagination_preload_number varchar(10) NOT NULL,
    image_browser_preload_number varchar(10) NOT NULL,
    image_browser_load_number varchar(10) NOT NULL,
    number_of_columns varchar(30) NOT NULL,
    
    resort_after_load_more varchar(1) NOT NULL,
    show_likes varchar(1) NOT NULL,
    show_description varchar(1) NOT NULL,
    show_comments varchar(1) NOT NULL,
    show_usernames varchar(1) NOT NULL,
    display_user_info varchar(1) NOT NULL,
    display_user_post_follow_number varchar(1) NOT NULL,
    show_full_description varchar(1) NOT NULL,
    disable_mobile_layout varchar(1) NOT NULL,
    feed_type varchar(30) NOT NULL,
    feed_item_onclick varchar(30) NOT NULL,
    
    
    popup_fullscreen varchar(1) NOT NULL,
    popup_width varchar(64) NOT NULL,
    popup_height varchar(64) NOT NULL,
    popup_type varchar(64) NOT NULL,
    popup_autoplay varchar(1) NOT NULL,
    popup_interval varchar(64) NOT NULL,
    popup_enable_filmstrip varchar(1) NOT NULL,
    popup_filmstrip_height varchar(64) NOT NULL,
    autohide_lightbox_navigation varchar(1) NOT NULL,
    popup_enable_ctrl_btn varchar(1) NOT NULL,
    popup_enable_fullscreen varchar(1) NOT NULL,
    popup_enable_info varchar(1) NOT NULL,
    popup_info_always_show varchar(1) NOT NULL,
    popup_info_full_width varchar(1) NOT NULL,
    popup_enable_comment varchar(1) NOT NULL,
    popup_enable_fullsize_image varchar(1) NOT NULL,
    popup_enable_download varchar(1) NOT NULL,
    popup_enable_share_buttons varchar(1) NOT NULL,
    popup_enable_facebook varchar(1) NOT NULL,
    popup_enable_twitter varchar(1) NOT NULL,
    popup_enable_google varchar(1) NOT NULL,
    popup_enable_pinterest varchar(1) NOT NULL,
    popup_enable_tumblr varchar(1) NOT NULL,
    show_image_counts varchar(1) NOT NULL,
    enable_loop varchar(1) NOT NULL,
    popup_image_right_click varchar(1) NOT NULL,
  
    conditional_filters varchar(10000) NOT NULL, 
    conditional_filter_type varchar(32) NOT NULL,
    show_username_on_thumb varchar(32) NOT NULL,
    conditional_filter_enable varchar(1) NOT NULL,
    liked_feed varchar(30) NOT NULL,
    mobile_breakpoint varchar(10) NOT NULL,
    redirect_url varchar(255) NOT NULL,
    feed_resolution varchar(255) NOT NULL,
    hashtag_top_recent varchar(10) NOT NULL,
    
    
    UNIQUE KEY id (id)
  ) $charset_collate;";
  if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
    //table is not created. you may create the table here.
    $wpdb->query($sql);
  }
  //dbDelta( $sql );
  $charset_collate2 = 'COLLATE latin1_general_ci';
  $table_name = $wpdb->prefix . WDI_THEME_TABLE;
  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT  PRIMARY  KEY,
    theme_name tinytext NOT NULL,
    default_theme varchar(1) NOT NULL,
    
    feed_container_bg_color varchar(32) NOT NULL,
    feed_wrapper_width varchar(32) NOT NULL,
    feed_container_width varchar(32) NOT NULL,
    feed_wrapper_bg_color varchar(32) NOT NULL,
    active_filter_bg_color varchar(32) NOT NULL,
    header_margin varchar(32) NOT NULL,
    header_padding varchar(32) NOT NULL,
    header_border_size varchar(32) NOT NULL,
    header_border_color varchar(32) NOT NULL,
    header_position varchar(32) NOT NULL,
    header_img_width varchar(32) NOT NULL,
    header_border_radius varchar(32) NOT NULL,
    header_text_padding varchar(32) NOT NULL,
    header_text_color varchar(32) NOT NULL,
    header_font_weight varchar(32) NOT NULL,
    header_text_font_size varchar(32) NOT NULL,
    header_text_font_style varchar(32) NOT NULL,
    follow_btn_border_radius varchar(32) NOT NULL,
    follow_btn_padding varchar(32) NOT NULL,
    follow_btn_margin varchar(32) NOT NULL,
    follow_btn_bg_color varchar(32) NOT NULL,
    follow_btn_border_color varchar(32) NOT NULL,
    follow_btn_text_color varchar(32) NOT NULL,
    follow_btn_font_size varchar(32) NOT NULL,
    follow_btn_border_hover_color varchar(32) NOT NULL,
    follow_btn_text_hover_color varchar(32) NOT NULL,
    follow_btn_background_hover_color varchar(32) NOT NULL,

    user_horizontal_margin varchar(32) NOT NULL,
    user_padding varchar(32) NOT NULL,
    user_border_size varchar(32) NOT NULL,
    user_border_color varchar(32) NOT NULL,
    user_img_width varchar(32) NOT NULL,
    user_border_radius varchar(32) NOT NULL,
    user_background_color varchar(32) NOT NULL,

    users_border_size varchar(32) NOT NULL,
    users_border_color varchar(32) NOT NULL,
    users_background_color varchar(32) NOT NULL,
    users_text_color varchar(32) NOT NULL,
    users_font_weight varchar(32) NOT NULL,
    users_text_font_size varchar(32) NOT NULL,
    users_text_font_style varchar(32) NOT NULL,
    user_description_font_size varchar(32) NOT NULL,

    lightbox_overlay_bg_color varchar(32) NOT NULL,
    lightbox_overlay_bg_transparent varchar(32) NOT NULL,
    lightbox_bg_color varchar(32) NOT NULL,
    lightbox_ctrl_btn_height varchar(32) NOT NULL,
    lightbox_ctrl_btn_margin_top varchar(32) NOT NULL,
    lightbox_ctrl_btn_margin_left varchar(32) NOT NULL,
    lightbox_ctrl_btn_pos varchar(32) NOT NULL,
    lightbox_ctrl_cont_bg_color varchar(32) NOT NULL,
    lightbox_ctrl_cont_border_radius varchar(32) NOT NULL,
    lightbox_ctrl_cont_transparent varchar(32) NOT NULL,
    lightbox_ctrl_btn_align varchar(32) NOT NULL,
    lightbox_ctrl_btn_color varchar(32) NOT NULL,
    lightbox_ctrl_btn_transparent varchar(32) NOT NULL,
    lightbox_toggle_btn_height varchar(32) NOT NULL,
    lightbox_toggle_btn_width varchar(32) NOT NULL,
    lightbox_close_btn_border_radius varchar(32) NOT NULL,
    lightbox_close_btn_border_width varchar(32) NOT NULL,
    lightbox_close_btn_border_style varchar(32) NOT NULL,
    lightbox_close_btn_border_color varchar(32) NOT NULL,
    lightbox_close_btn_box_shadow varchar(128) NOT NULL,
    lightbox_close_btn_bg_color varchar(32) NOT NULL,
    lightbox_close_btn_transparent varchar(32) NOT NULL,
    lightbox_close_btn_width varchar(32) NOT NULL,
    lightbox_close_btn_height varchar(32) NOT NULL,
    lightbox_close_btn_top varchar(32) NOT NULL,
    lightbox_close_btn_right varchar(32) NOT NULL,
    lightbox_close_btn_size varchar(32) NOT NULL,
    lightbox_close_btn_color varchar(32) NOT NULL,
    lightbox_close_btn_full_color varchar(32) NOT NULL,
    lightbox_close_btn_hover_color varchar(32) NOT NULL,
    lightbox_comment_share_button_color varchar(32) NOT NULL,
    lightbox_rl_btn_style varchar(32) NOT NULL,
    lightbox_rl_btn_bg_color varchar(32) NOT NULL,
    lightbox_rl_btn_transparent varchar(32) NOT NULL,
    lightbox_rl_btn_box_shadow varchar(128) NOT NULL,
    lightbox_rl_btn_height varchar(32) NOT NULL,
    lightbox_rl_btn_width varchar(32) NOT NULL,
    lightbox_rl_btn_size varchar(32) NOT NULL,
    lightbox_close_rl_btn_hover_color varchar(32) NOT NULL,
    lightbox_rl_btn_color varchar(32) NOT NULL,
    lightbox_rl_btn_border_radius varchar(32) NOT NULL,
    lightbox_rl_btn_border_width varchar(32) NOT NULL,
    lightbox_rl_btn_border_style varchar(32) NOT NULL,
    lightbox_rl_btn_border_color varchar(32) NOT NULL,
    lightbox_filmstrip_pos varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_margin varchar(128) NOT NULL,
    lightbox_filmstrip_thumb_border_width varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_border_style varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_border_color varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_border_radius varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_active_border_width varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_active_border_color varchar(32) NOT NULL,
    lightbox_filmstrip_thumb_deactive_transparent varchar(32) NOT NULL,
    lightbox_filmstrip_rl_btn_size varchar(32) NOT NULL,
    lightbox_filmstrip_rl_btn_color varchar(32) NOT NULL,
    lightbox_filmstrip_rl_bg_color varchar(32) NOT NULL,
    lightbox_info_pos varchar(32) NOT NULL,
    lightbox_info_align varchar(32) NOT NULL,
    lightbox_info_bg_color varchar(32) NOT NULL,
    lightbox_info_bg_transparent varchar(32) NOT NULL,
    lightbox_info_border_width varchar(32) NOT NULL,
    lightbox_info_border_style varchar(32) NOT NULL,
    lightbox_info_border_color varchar(32) NOT NULL,
    lightbox_info_border_radius varchar(32) NOT NULL,
    lightbox_info_padding varchar(32) NOT NULL,
    lightbox_info_margin varchar(32) NOT NULL,
    lightbox_title_color varchar(32) NOT NULL,
    lightbox_title_font_style varchar(32) NOT NULL,
    lightbox_title_font_weight varchar(32) NOT NULL,
    lightbox_title_font_size varchar(32) NOT NULL,
    lightbox_description_color varchar(32) NOT NULL,
    lightbox_description_font_style varchar(32) NOT NULL,
    lightbox_description_font_weight varchar(32) NOT NULL,
    lightbox_description_font_size varchar(32) NOT NULL,
    lightbox_info_height varchar(32) NOT NULL,
    lightbox_comment_width varchar(32) NOT NULL,
    lightbox_comment_pos varchar(32) NOT NULL,
    lightbox_comment_bg_color varchar(32) NOT NULL,
    lightbox_comment_font_size varchar(32) NOT NULL,
    lightbox_comment_font_color varchar(32) NOT NULL,
    lightbox_comment_font_style varchar(32) NOT NULL,
    lightbox_comment_author_font_size varchar(32) NOT NULL,
    lightbox_comment_author_font_color varchar(32) NOT NULL,
    lightbox_comment_author_font_color_hover varchar(32) NOT NULL,
    lightbox_comment_date_font_size varchar(32) NOT NULL,
    lightbox_comment_body_font_size varchar(32) NOT NULL,
    lightbox_comment_input_border_width varchar(32) NOT NULL,
    lightbox_comment_input_border_style varchar(32) NOT NULL,
    lightbox_comment_input_border_color varchar(32) NOT NULL,
    lightbox_comment_input_border_radius varchar(32) NOT NULL,
    lightbox_comment_input_padding varchar(32) NOT NULL,
    lightbox_comment_input_bg_color varchar(32) NOT NULL,
    lightbox_comment_button_bg_color varchar(32) NOT NULL,
    lightbox_comment_button_padding varchar(32) NOT NULL,
    lightbox_comment_button_border_width varchar(32) NOT NULL,
    lightbox_comment_button_border_style varchar(32) NOT NULL,
    lightbox_comment_button_border_color varchar(32) NOT NULL,
    lightbox_comment_button_border_radius varchar(32) NOT NULL,
    lightbox_comment_separator_width varchar(32) NOT NULL,
    lightbox_comment_separator_style varchar(32) NOT NULL,
    lightbox_comment_separator_color varchar(32) NOT NULL,
    lightbox_comment_load_more_color varchar(32) NOT NULL,
    lightbox_comment_load_more_color_hover varchar(32) NOT NULL,

    th_photo_wrap_padding varchar(32) NOT NULL,
    th_photo_wrap_border_size varchar(32) NOT NULL,
    th_photo_wrap_border_color varchar(32) NOT NULL,
    th_photo_img_border_radius varchar(32) NOT NULL,
    th_photo_wrap_bg_color varchar(32) NOT NULL,
    th_photo_meta_bg_color varchar(32) NOT NULL,
    th_photo_meta_one_line varchar(32) NOT NULL,
    th_like_text_color varchar(32) NOT NULL,
    th_comment_text_color varchar(32) NOT NULL,
    th_photo_caption_font_size varchar(32) NOT NULL,
    th_photo_caption_color varchar(32) NOT NULL,
    th_feed_item_margin varchar(32) NOT NULL,
    th_photo_caption_hover_color varchar(32) NOT NULL,
    th_like_comm_font_size varchar(32) NOT NULL,
    th_overlay_hover_color varchar(32) NOT NULL,
    th_overlay_hover_transparent varchar(32) NOT NULL,
    th_overlay_hover_icon_color varchar(32) NOT NULL,
    th_overlay_hover_icon_font_size varchar(32) NOT NULL,
    th_photo_img_hover_effect varchar(32) NOT NULL,   

    mas_photo_wrap_padding varchar(32) NOT NULL,
    mas_photo_wrap_border_size varchar(32) NOT NULL,
    mas_photo_wrap_border_color varchar(32) NOT NULL,
    mas_photo_img_border_radius varchar(32) NOT NULL,
    mas_photo_wrap_bg_color varchar(32) NOT NULL,
    mas_photo_meta_bg_color varchar(32) NOT NULL,
    mas_photo_meta_one_line varchar(32) NOT NULL,
    mas_like_text_color varchar(32) NOT NULL,
    mas_comment_text_color varchar(32) NOT NULL,
    mas_photo_caption_font_size varchar(32) NOT NULL,
    mas_photo_caption_color varchar(32) NOT NULL,
    mas_feed_item_margin varchar(32) NOT NULL,
    mas_photo_caption_hover_color varchar(32) NOT NULL,
    mas_like_comm_font_size varchar(32) NOT NULL,
    mas_overlay_hover_color varchar(32) NOT NULL,
    mas_overlay_hover_transparent varchar(32) NOT NULL,
    mas_overlay_hover_icon_color varchar(32) NOT NULL,
    mas_overlay_hover_icon_font_size varchar(32) NOT NULL,
    mas_photo_img_hover_effect varchar(32) NOT NULL,

    blog_style_photo_wrap_padding varchar(32) NOT NULL,
    blog_style_photo_wrap_border_size varchar(32) NOT NULL,
    blog_style_photo_wrap_border_color varchar(32) NOT NULL,
    blog_style_photo_img_border_radius varchar(32) NOT NULL,
    blog_style_photo_wrap_bg_color varchar(32) NOT NULL,
    blog_style_photo_meta_bg_color varchar(32) NOT NULL,
    blog_style_photo_meta_one_line varchar(32) NOT NULL,
    blog_style_like_text_color varchar(32) NOT NULL,
    blog_style_comment_text_color varchar(32) NOT NULL,
    blog_style_photo_caption_font_size varchar(32) NOT NULL,
    blog_style_photo_caption_color varchar(32) NOT NULL,
    blog_style_feed_item_margin varchar(32) NOT NULL,
    blog_style_photo_caption_hover_color varchar(32) NOT NULL,
    blog_style_like_comm_font_size varchar(32) NOT NULL,

    image_browser_photo_wrap_padding varchar(32) NOT NULL,
    image_browser_photo_wrap_border_size varchar(32) NOT NULL,
    image_browser_photo_wrap_border_color varchar(32) NOT NULL,
    image_browser_photo_img_border_radius varchar(32) NOT NULL,
    image_browser_photo_wrap_bg_color varchar(32) NOT NULL,
    image_browser_photo_meta_bg_color varchar(32) NOT NULL,
    image_browser_photo_meta_one_line varchar(32) NOT NULL,
    image_browser_like_text_color varchar(32) NOT NULL,
    image_browser_comment_text_color varchar(32) NOT NULL,
    image_browser_photo_caption_font_size varchar(32) NOT NULL,
    image_browser_photo_caption_color varchar(32) NOT NULL,
    image_browser_feed_item_margin varchar(32) NOT NULL,
    image_browser_photo_caption_hover_color varchar(32) NOT NULL,
    image_browser_like_comm_font_size varchar(32) NOT NULL,

    load_more_position varchar(32) NOT NULL,
    load_more_padding varchar(32) NOT NULL,
    load_more_bg_color varchar(32) NOT NULL,
    load_more_border_radius varchar(32) NOT NULL,
    load_more_height varchar(32) NOT NULL,
    load_more_width varchar(32) NOT NULL,
    load_more_border_size varchar(32) NOT NULL,
    load_more_border_color varchar(32) NOT NULL,
    load_more_text_color varchar(32) NOT NULL,
    load_more_text_font_size varchar(32) NOT NULL,
    load_more_wrap_hover_color varchar(32) NOT NULL,
    pagination_ctrl_color varchar(32) NOT NULL,
    pagination_size varchar(32) NOT NULL,
    pagination_ctrl_margin varchar(32) NOT NULL,
    pagination_ctrl_hover_color varchar(32) NOT NULL,
    pagination_position varchar(32) NOT NULL,
    pagination_position_vert varchar(32) NOT NULL,

    /* since v1.0.6*/
    /* keep order */
    th_thumb_user_bg_color varchar(32) NOT NULL,
    th_thumb_user_color varchar(32) NOT NULL,
    mas_thumb_user_bg_color varchar(32) NOT NULL,
    mas_thumb_user_color varchar(32) NOT NULL,
    
    UNIQUE KEY id (id)
  ) $charset_collate2;";
  // dbDelta( $sql );
  if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
    //table is not created. you may create the table here.
    $wpdb->query($sql);
    wdi_install_default_themes();
  }
  if ( !update_option('wdi_version', WDI_VERSION) ) {
    add_option('wdi_version', WDI_VERSION);
  }
  /**
   * add api update notice
   *
   * @since 1.1.0
   */
  $admin_notices_option = get_option('wdi_admin_notice', array());
  $admin_notices_option['api_update_token_reset'] = array(
    'start' => current_time("n/j/Y"),
    'int' => 0,
    'dismissed' => 1, // dismissed on activate
  );
  update_option('wdi_admin_notice', $admin_notices_option);
  wdi_generate_themes_files();
  // Using this insted of flush_rewrite_rule() for better performance with multisite.
  global $wp_rewrite;
  $wp_rewrite->init();
  $wp_rewrite->flush_rules();
}

function wdi_deactivate() {
  wp_clear_scheduled_hook('wdi_schedule_event_hook');
  // Using this insted of flush_rewrite_rule() for better performance with multisite.
  global $wp_rewrite;
  $wp_rewrite->init();
  $wp_rewrite->flush_rules();
}

//receives settings element as parameter and checks it's type and displays proper form element
function wdi_field_callback( $element ) {
  $setting = $element[0];
  $wdi_formBuilder = new WDI_admin_view();
  switch ( $setting['type'] ) {
    case 'input':
    {
      $wdi_formBuilder->input($setting);
      break;
    }
    case 'checkbox':
    {
      $wdi_formBuilder->checkbox($setting);
      break;
    }
    case 'color':
    {
      $wdi_formBuilder->color($setting);
      break;
    }
    case 'textarea':
    {
      $wdi_formBuilder->textarea($setting);
      break;
    }
    case 'select':
    {
      $wdi_formBuilder->select($setting);
      break;
    }
    case 'link_button':
    {
      $wdi_formBuilder->link_button($setting);
      break;
    }
    case 'users_list':
    {
      $wdi_formBuilder->users_list($setting);
      break;
    }
  }
}

//Validatates input form submit forms
function wdi_validate_options( $input ) {
  global $wdi_options;
  foreach ( $input as $key => $value ) {
    if ( isset($input[$key]) ) {
      $wdi_options[$key] = strip_tags(stripslashes($input[$key]));
    }
  }

  return apply_filters('wdi_validate_options', $wdi_options, $input);
}

function wdi_sanitize_options( $input ) {
  $output = array();
  $elements = wdi_get_settings();
  foreach ( $input as $key => $value ) {
    if ( isset($elements[$key]['sanitize_type']) ) {
      switch ( $elements[$key]['sanitize_type'] ) {
        case 'text':
        {
          if ( isset($input[$key]) ) {
            $output[$key] = strip_tags(stripslashes($input[$key]));
          }
          break;
        }
        case 'css':
        {
          $output[$key] = esc_js(str_replace(array( "\n", "\r" ), "", $input[$key]));
          break;
        }
        case 'users_list':
        {
          global $wdi_options;
          $users_list = array();
          $authenticated_users_list = $input['wdi_authenticated_users_list'];
          if ( !empty($authenticated_users_list) ) {
            foreach ( $authenticated_users_list as $ukey => $uval ) {
              if ( $wdi_options['wdi_user_name'] === $ukey ) {
                continue;
              }
              $users_list[$user_name] = array(
                'user_id' => $uval['user_id'],
                'user_name' => $uval['user_name'],
                'access_token' => $uval['access_token'],
              );
            }
          }

          $option = $input['wdi_authenticated_users_list'];
          //$saved_user_list = json_decode($wdi_options['wdi_authenticated_users_list'], true);

          if ( !empty($option['access_token']) ) {

            $user_count = count($option['access_token']);
            for ( $i = 0; $i < $user_count; $i++ ) {

              if ( !empty($option['access_token'][$i]) && !empty($option['user_name'][$i]) && !empty($option['user_id'][$i]) ) {

                $user_name = $option['user_name'][$i];
                if ( $wdi_options['wdi_user_name'] === $user_name ) {
                  continue;
                }
                $users_list[$user_name] = array(
                  'user_id' => $option['user_id'][$i],
                  'user_name' => $user_name,
                  'access_token' => $option['access_token'][$i],
                );
              }
            }
          }
          if ( isset($users_list[$input['wdi_user_name']]) ) {
            unset($users_list[$input['wdi_user_name']]);
          }
          $output[$key] = json_encode($users_list);
          break;
        }
        default:
        {
          if ( isset($input[$key]) ) {
            if ( is_array($input[$key]) ) {
              $output[$key] = strip_tags(stripslashes(json_encode($input[$key])));
            }
            else {
              $output[$key] = strip_tags(stripslashes($input[$key]));
            }
          }
          break;
        }
      }
    }
  }

  return apply_filters('wdi_sanitize_options', $output, $input);
}

// Sets all settings for admin pages and returns associative array of settings
function wdi_get_settings() {
  $opt = wdi_get_options();

  $settings = array(
    'wdi_access_token' => array(
      'name' => 'wdi_access_token',
      'sanitize_type' => 'text',
      'input_size' => '53',
      'type' => 'input',
      'default' => '',
      'field_or_not' => 'field',
      'section' => 'wdi_configure_section',
      'title' => __('Primary Access Token', "wd-instagram-feed"),
    ),
    'wdi_user_name' => array(
      'name' => 'wdi_user_name',
      'sanitize_type' => 'text',
      'type' => 'input',
      'input_size' => '53',
      'section' => 'wdi_configure_section',
      'field_or_not' => 'field',
      'default' => '',
      'title' => __('Primary Username', "wd-instagram-feed"),
    ),
    'wdi_user_id' => array(
      'name' => 'wdi_user_id',
      'sanitize_type' => 'text',
      'type' => 'input',
      'section' => 'wdi_configure_section',
      'readonly' => 'readonly',
      'default' => '',
      'field_or_not' => 'no_field',
    ),
    'wdi_start_in' => array(
      'name' => 'wdi_start_in',
      'sanitize_type' => 'text',
      'type' => 'input',
      'section' => 'wdi_configure_section',
      'readonly' => 'readonly',
      'default' => '',
      'field_or_not' => 'no_field',
    ),
    'wdi_expires_in' => array(
      'name' => 'wdi_expires_in',
      'sanitize_type' => 'text',
      'type' => 'input',
      'section' => 'wdi_configure_section',
      'readonly' => 'readonly',
      'default' => '',
      'field_or_not' => 'no_field',
    ),
    'wdi_transient_time' => array(
      'name' => 'wdi_transient_time',
      'sanitize_type' => 'number',
      'field_or_not' => '',
      'type' => 'input',
      'input_type' => 'number',
      'section' => 'wdi_customize_section',
      'title' => __('Check for new posts every (min)', "wd-instagram-feed"),
      'default' => '2880',
      'value' => '',
    ),
    'wdi_authenticated_users_list' => array(
      'name' => 'wdi_authenticated_users_list',
      'sanitize_type' => '', // users_list
      'input_size' => '53',
      'type' => 'users_list',
      'default' => '[]',
      'field_or_not' => 'field',
      'section' => 'wdi_customize_section',
      'title' => __('Multiple Instagram accounts ?', "wd-instagram-feed"),
    ),
    'wdi_feeds_min_capability' => array(
      'name' => 'wdi_feeds_min_capability',
      "sanitize_type" => "text",
      'title' => __('Minimal role to add and manage Feeds or Themes', "wd-instagram-feed"),
      'type' => 'select',
      'field_or_not' => 'field',
      "default" => "manage_options",
      'section' => 'wdi_customize_section',
      'valid_options' => array(
        'manage_options' => __('Administrator', 'wd-instagram-feed'),
        'publish_posts' => __('Author', 'wd-instagram-feed'),
        'contributor' => __('Contributor', 'wd-instagram-feed'),
      ),
    ),
    'wdi_custom_css' => array(
      'name' => 'wdi_custom_css',
      'sanitize_type' => 'css',
      'type' => 'textarea',
      'section' => 'wdi_customize_section',
      'field_or_not' => 'field',
      'default' => '',
      'title' => __('Custom CSS', "wd-instagram-feed"),
    ),
    'wdi_custom_js' => array(
      'name' => 'wdi_custom_js',
      'sanitize_type' => 'css',
      'type' => 'textarea',
      'section' => 'wdi_customize_section',
      'field_or_not' => 'field',
      'default' => '',
      'title' => __('Custom JavaScript', "wd-instagram-feed"),
    ),
    //'wdi_preserve_settings_when_remove'=>array('name'=>'wdi_preserve_settings_when_remove','field_or_not'=>'field','type'=>'checkbox','default'=>'1', 'section'=>'wdi_configure_section','title'=>__('Preserve Settings When Remove',"wd-instagram-feed")),
    'wdi_plugin_uninstalled' => array(
      'name' => 'wdi_plugin_uninstalled',
      'sanitize_type' => 'bool',
      'field_or_not' => 'field',
      'type' => 'input',
      'input_type' => 'hidden',
      'section' => 'wdi_customize_section',
      'title' => '',
      'default' => 'false',
      'value' => 'false',
    ),
    'wdi_uninstall' => array(
      'name' => 'wdi_uninstall',
      'sanitize_type' => '',
      'field_or_not' => '',
      'type' => 'link_button',
      'section' => 'wdi_customize_section',
      'href' => admin_url('admin.php?page=wdi_uninstall'),
      'title' => __('Uninstall', "wd-instagram-feed"),
      'default' => '',
    ),
    'wdi_cache_request_count' => array(
      'name' => 'wdi_cache_request_count',
      'sanitize_type' => 'number',
      'field_or_not' => 'field',
      'type' => 'number',
      'section' => 'wdi_customize_section',
      'title' => __('Set Maximum Count of Cache Requests', "wd-instagram-feed"),
      'default' => '10',
    ),
    //'wdi_version' => array('name'=>'wdi_version','field_or_not'=>'no_field','default'=>WDI_VERSION),
    //'wdi_first_time'=>array('name'=>'wdi_first_time','field_or_not'=>'no_field','default'=>'1')
  );

  return $settings;
}

//Sets plugin default settings
function wdi_set_options_defaults() {
  $db_options = array();
  $options = get_option(WDI_OPT);
  $settings = wdi_get_settings();
  foreach ( $settings as $setting ) {
    $settingDefault = isset($setting['default']) ? $setting['default'] : '';
    $db_options[$setting['name']] = $settingDefault;
  }
  $options = wp_parse_args($options, $db_options);


  if ( !isset($options['wdi_cache_request_count']) ) {
    $options['wdi_cache_request_count'] = 10;
  }

  if ( isset($options['wdi_plugin_uninstalled']) && $options['wdi_plugin_uninstalled'] == 'true' ) {
    $options['wdi_plugin_uninstalled'] = 'false';
  }
  if ( empty($options['wdi_authenticated_users_list']) ) {
    $options['wdi_authenticated_users_list'] = '[]';
  }
  add_option(WDI_OPT, $options, '', 'yes');
  update_option(WDI_OPT, $options, 'yes');
  wdi_get_options();
}

function wdi_get_options() {
  global $wdi_options;
  $wdi_options = get_option(WDI_OPT);

  return apply_filters('wdi_get_options', $wdi_options);
}

function wdi_install_default_themes() {
  global $wdi_options;
  global $wpdb;
  require_once WDI_DIR . "/templates/default-themes.php";
  $default_themes = wdi_get_default_themes();
  foreach ( $default_themes as $theme ) {
    $table_name = $wpdb->prefix . WDI_THEME_TABLE;
    if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
      require_once(WDI_DIR . '/framework/WDILibrary.php');
      echo WDILibrary::message(__('Database error, please uninstall the plugin and install again', "wd-instagram-feed"), 'error');
    }
    else {
      $wpdb->insert($table_name, $theme);
    }
  }
}

function wdi_get_create_feeds_cap() {
  global $wdi_options;
  $min_feeds_capability = isset($wdi_options['wdi_feeds_min_capability']) ? $wdi_options['wdi_feeds_min_capability'] : "manage_options";
  if ( $min_feeds_capability == 'publish_posts' ) {
    $min_feeds_capability = 'publish_posts';
  }
  else {
    if ( $min_feeds_capability == 'contributor' ) {
      $min_feeds_capability = 'contributor';
    }
    else {
      $min_feeds_capability = 'manage_options';
    }
  }

  return $min_feeds_capability;
}

function wdi_generate_themes_files() {
  require_once WDI_DIR . '/admin/models/WDIModelThemes_wdi.php';
  require_once WDI_DIR . '/framework/WDILibrary.php';
  $themes = WDIModelThemes_wdi::get_themes();
  if ( !is_array($themes) || empty($themes) ) {
    return;
  }
  require_once WDI_DIR . '/admin/controllers/WDIControllerThemes_wdi.php';
  WDIControllerThemes_wdi::remove_all_themes_files();
  require_once WDI_DIR . '/framework/WDI_generate_styles.php';
  foreach ( $themes as $id => $theme_title ) {
    $theme_row = WDIModelThemes_wdi::get_theme_row($id);
    $generator = new WDI_generate_styles($id, (array) $theme_row);
    $generator->all_views_styles();
  }
}

/**
 * Get authorize urls.
 * @return array
 */
function wdi_get_auth_urls() {
  require_once WDI_DIR . '/framework/WDILibrary.php';
  $config = WDILibrary::instagram_app_config();
  $state = array(
    'wp_site_url' => admin_url('admin.php?page=wdi_settings'),
  );
  $urls = array();
  $urls['personal'] = add_query_arg(array(
                                      'app_id' => $config['basic_app_id'],
                                      'redirect_uri' => $config['basic_redirect_uri'],
                                      'scope' => $config['basic_scope'],
                                      'response_type' => $config['basic_response_type'],
                                      'state' => admin_url('admin.php?wdi_settings')
                                    ), $config['basic_authorize_url']);

  $urls['business'] = add_query_arg(array(
                                      'client_id' => $config['graph_app_id'],
                                      'redirect_uri' => $config['graph_redirect_uri'],
                                      'scope' => $config['graph_scope']
                                    ), $config['graph_authorize_url']);
  $urls['business'] .= '&state=' . base64_encode(json_encode($state));

  return $urls;
}

function wdi_parse_old_options( $new_version, $old_version ) {
  if ( !empty($old_version) && version_compare($new_version, $old_version, '>') ) {
    $options = wdi_get_options();
    if ( !empty($options) && ( !empty($options['wdi_fb_auth']) || !empty($options['fb_token']) ) ) {
      $users = array();
      $users[$options['wdi_user_name']] = array(
        'type' => 'personal',
        'user_id' => $options['wdi_user_id'],
        'user_name' => $options['wdi_user_name'],
        'access_token' => $options['wdi_access_token'],
        'start_in' => $options['wdi_start_in'],
        'expires_in' => $options['wdi_expires_in'],
      );
      if ( !empty($options['wdi_business_ig_user']) ) {
        $business_users = json_decode($options['wdi_business_ig_user'], TRUE);
        if ( !empty($business_users) ) {
          $users[$business_users['username']] = array(
            'type' => 'business',
            'user_id' => $business_users['id'],
            'user_name' => $business_users['username'],
            'biography' => $business_users['biography'],
            'profile_picture_url' => $business_users['profile_picture_url'],
            'followers_count' => $business_users['followers_count'],
            'follows_count' => $business_users['follows_count'],
            'media_count' => $business_users['media_count'],
            'website' => $business_users['website'],
            'access_token' => $options['fb_token'],
            'start_in' => $options['wdi_start_in'],
            'expires_in' => $options['wdi_expires_in'],
          );
        }
      }
      if ( !empty($users) ) {
        // old options removed.
        $unset_options = array(
          'fb_token',
          'wdi_fb_auth',
          'business_account_id',
          'wdi_business_ig_user',
          'wdi_business_fb_page',
        );
        // 'wdi_access_token', 'wdi_user_name', 'wdi_user_id', 'wdi_start_in', 'wdi_expires_in',
        foreach ( $unset_options as $key => $val ) {
          if ( isset($options[$val]) ) {
            unset($options[$val]);
          }
        }
        $options['wdi_authenticated_users_list'] = json_encode($users);
        update_option(WDI_OPT, $options, 'yes');
      }
    }
  }
}