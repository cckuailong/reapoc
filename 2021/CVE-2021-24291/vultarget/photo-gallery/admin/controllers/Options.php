<?php

/**
 * Class OptionsController_bwg
 */
class OptionsController_bwg {

  public function __construct() {
    $this->model = new OptionsModel_bwg();
    $this->view = new OptionsView_bwg();
    $this->page = WDWLibrary::get('page');
  }

  public function execute() {
    $task = WDWLibrary::get('task');
    if($task != ''){
      check_admin_referer(BWG()->nonce, BWG()->nonce);
    }
    $params = array();
    $params['permissions'] = array(
      'manage_options' => 'Administrator',
      'moderate_comments' => 'Editor',
      'publish_posts' => 'Author',
      'edit_posts' => 'Contributor',
    );
    $built_in_watermark_fonts = array();
    foreach (scandir(path_join(BWG()->plugin_dir, 'fonts')) as $filename) {
      if ( strpos($filename, '.') === 0 || strpos($filename, 'twbb') !== FALSE ) {
        continue;
      }
      else {
        $built_in_watermark_fonts[] = $filename;
      }
    }
    $params['built_in_watermark_fonts'] = $built_in_watermark_fonts;
    $params['watermark_fonts'] = array(
      'arial' => 'Arial',
      'Lucida grande' => 'Lucida grande',
      'segoe ui' => 'Segoe ui',
      'tahoma' => 'Tahoma',
      'trebuchet ms' => 'Trebuchet ms',
      'verdana' => 'Verdana',
      'cursive' =>'Cursive',
      'fantasy' => 'Fantasy',
      'monospace' => 'Monospace',
      'serif' => 'Serif',
    );
    $params['page_title'] = __('Global Settings', BWG()->prefix);
    $params['active_tab'] = WDWLibrary::get('active_tab', 0, 'intval');
    $params['gallery_type'] = WDWLibrary::get('gallery_type', 'thumbnails');
    $params['album_type'] = WDWLibrary::get('album_type', 'album_compact_preview');
    $params['gallery_types_name'] = array(
      'thumbnails' => __('Thumbnails', BWG()->prefix),
      'thumbnails_masonry' => __('Masonry', BWG()->prefix),
      'thumbnails_mosaic' => __('Mosaic', BWG()->prefix),
      'slideshow' => __('Slideshow', BWG()->prefix),
      'image_browser' => __('Image Browser', BWG()->prefix),
      'blog_style' => __('Blog Style', BWG()->prefix),
      'carousel' => __('Carousel', BWG()->prefix),
    );
    $params['album_types_name'] = array(
      'album_compact_preview' => __('Compact', BWG()->prefix),
      'album_masonry_preview' => __('Masonry', BWG()->prefix),
      'album_extended_preview' => __('Extended', BWG()->prefix),
    );
    if (method_exists($this, $task)) {
      $this->$task($params);
    }
    else {
      do_action('bwg_options_execute_task', $task);
      $this->display($params);
    }
  }

    /**
     * Display.
     *
     * @param $params
     */
  public function display($params = array()) {
    $row = new WD_BWG_Options();
    // Set Instagram access token.
    if ( isset( $_GET[ 'wdi_access_token' ] ) ) {
      ob_end_clean();
      $success = $this->model->set_instagram_access_token( false );
      if ( $success ) {
        wp_redirect( add_query_arg( array('page' => $this->page .'&instagram_token=' . time() ), admin_url('admin.php')) );
      }
    }

    $params['row']  = $row;
    $params['row']->lightbox_shortcode = 0;
    $params['page'] = $this->page;
    $params['imgcount'] = $this->model->get_image_count();
    $params['options_url_ajax'] = add_query_arg( array(
													'action' => 'options_' . BWG()->prefix,
													BWG()->nonce => wp_create_nonce(BWG()->nonce),
												), admin_url('admin-ajax.php') );

    $params['instagram_return_url'] = 'https://api.instagram.com/oauth/authorize/?app_id=734781953985462&scope=user_profile,user_media&redirect_uri=https://instagram-api.10web.io/instagram/personal/&state=' . urlencode( admin_url('admin.php?options_bwg')) . '&response_type=code';
    $params['instagram_reset_href'] =  add_query_arg( array(
														'page' => $this->page,
														'task' => 'reset_instagram_access_token',
														BWG()->nonce => wp_create_nonce(BWG()->nonce),
													), admin_url('admin.php') );

    $this->view->display($params);
  }

    /**
     * Reset.
     *
     * @param array $params
     */
  public function reset( $params = array() ) {
    $params['row'] = new WD_BWG_Options(true);
    $params['page'] = $this->page;
  	$params['imgcount'] = $this->model->get_image_count();
    $params['options_url_ajax'] = add_query_arg( array(
													'action' => 'options_' . BWG()->prefix,
													BWG()->nonce => wp_create_nonce(BWG()->nonce),
												), admin_url('admin-ajax.php') );
    $params['instagram_return_url'] = 'https://api.instagram.com/oauth/authorize/?client_id=54da896cf80343ecb0e356ac5479d9ec&scope=basic+public_content&redirect_uri=http://api.web-dorado.com/instagram/?return_url=' . urlencode( admin_url('admin.php?page=options_bwg')) . '&response_type=token';
    $params['instagram_reset_href'] =  add_query_arg( array(
			'page' => $this->page,
			'task' => 'reset_instagram_access_token',
			BWG()->nonce => wp_create_nonce(BWG()->nonce),
		), admin_url('admin.php'));
    echo WDWLibrary::message_id(0, __('Default values restored. Changes must be saved.', BWG()->prefix), 'notice notice-warning');
    $this->view->display($params);
  }

  /**
   * Reset instagram access token.
   *
   * @param array $params
   */
  function reset_instagram_access_token ( $params = array() ) {
    ob_end_clean();
    $success = $this->model->set_instagram_access_token();
    if ( $success ) {
      wp_redirect( add_query_arg( array( 'page' => $this->page . '&instagram_token=' . time() ), admin_url( 'admin.php' ) ) );
    }
  }

  public function save( $params = array() ) {
    $this->save_db();
    $this->display( $params );
  }

  public function save_db() {
    $row = new WD_BWG_Options();
    if ( WDWLibrary::get('old_images_directory') ) {
      $row->old_images_directory = WDWLibrary::get('old_images_directory');
    }

    if ( WDWLibrary::get('images_directory', '', 'sanitize_text_field') ) {
      $row->images_directory = WDWLibrary::get('images_directory', '', 'sanitize_text_field');
      if (!is_dir(BWG()->abspath . $row->images_directory) || (is_dir(BWG()->abspath . $row->images_directory . '/photo-gallery') && $row->old_images_directory && $row->old_images_directory != $row->images_directory)) {
        if (!is_dir(BWG()->abspath . $row->images_directory)) {
          echo WDWLibrary::message_id(0, __('Uploads directory doesn\'t exist. Old value is restored.', BWG()->prefix), 'error');
        }
        else {
          echo WDWLibrary::message_id(0, __('Warning: "photo-gallery" folder already exists in uploads directory. Old value is restored.', BWG()->prefix), 'error');
        }
        if ($row->old_images_directory) {
          $row->images_directory = $row->old_images_directory;
        }
        else {
          $upload_dir = wp_upload_dir();
          if (!is_dir($upload_dir['basedir'] . '/photo-gallery')) {
            mkdir($upload_dir['basedir'] . '/photo-gallery', 0755);
          }
          $row->images_directory = str_replace(BWG()->abspath, '', $upload_dir['basedir']);
        }
      }
    }

    foreach ($row as $name => $value) {
      if ($name == 'autoupdate_interval') {
        $autoupdate_interval_hour = WDWLibrary::get('autoupdate_interval_hour', 0, 'intval');
        $autoupdate_interval_min = WDWLibrary::get('autoupdate_interval_min', 1, 'intval');
        $autoupdate_interval = ( isset($autoupdate_interval_hour) && isset($autoupdate_interval_min) ? ($autoupdate_interval_hour * 60 + $autoupdate_interval_min) : null);
        /*minimum autoupdate interval is 1 min*/
        $row->autoupdate_interval = isset($autoupdate_interval) && $autoupdate_interval >= 1 ? $autoupdate_interval : 30;
      }
      else if ( $name != 'images_directory' ) {
        $row->$name = WDWLibrary::get($name, $row->$name);
      }
    }
    $save = update_option('wd_bwg_options', json_encode($row), 'no');
    if ( WDWLibrary::get('recreate') == "resize_image_thumb" ) {
      $this->resize_image_thumb();
      echo WDWLibrary::message_id(0, __('All thumbnails are successfully recreated.', BWG()->prefix));
    }

    if ( $save ) {
      // Move images folder to the new direction if image directory has been changed.
      if ($row->old_images_directory && $row->old_images_directory != $row->images_directory) {
        rename(BWG()->abspath . $row->old_images_directory . '/photo-gallery', BWG()->abspath . $row->images_directory . '/photo-gallery');
      }

      if (!is_dir(BWG()->abspath . $row->images_directory . '/photo-gallery')) {
        mkdir(BWG()->abspath . $row->images_directory . '/photo-gallery', 0755);
      }
      else {
        echo WDWLibrary::message_id(0, __('Item Succesfully Saved.', BWG()->prefix));
      }

      if ( BWG()->is_pro ) {
        // Clear hook for scheduled events.
        wp_clear_scheduled_hook('bwg_schedule_event_hook');
        // Refresh filter according to new time interval.
        remove_filter('cron_schedules', array( BWG(), 'autoupdate_interval' ));
        add_filter('cron_schedules', array( BWG(), 'autoupdate_interval' ));
        // Then add new schedule with the same hook name.
        wp_schedule_event(time(), 'bwg_autoupdate_interval', 'bwg_schedule_event_hook');
      }
    }
  }

  public function image_set_watermark($params = array()) {
	$limitstart = WDWLibrary::get('limitstart', 0, 'intval');
    /*  Update options only first time of the loop  */
    if ( $limitstart == 0 ) {
		$update_options = array(
			'built_in_watermark_type' => WDWLibrary::get('built_in_watermark_type'),
			'built_in_watermark_position' => WDWLibrary::get('built_in_watermark_position')
		);
		if ( $update_options['built_in_watermark_type'] == 'text' ){
			$update_options['built_in_watermark_text'] = WDWLibrary::get('built_in_watermark_text');
			$update_options['built_in_watermark_font_size'] = WDWLibrary::get('built_in_watermark_font_size', 20, 'intval');
			$update_options['built_in_watermark_font'] = WDWLibrary::get('built_in_watermark_font');
			$update_options['built_in_watermark_color'] = WDWLibrary::get('built_in_watermark_color');
      $update_options['built_in_watermark_opacity'] = WDWLibrary::get('built_in_opacity');
		} 
		else {
			$update_options['built_in_watermark_size'] = WDWLibrary::get('built_in_watermark_size', 20, 'intval');
			$update_options['built_in_watermark_url'] = WDWLibrary::get('built_in_watermark_url', '', 'esc_url');
		}
		$this->model->update_options_by_key( $update_options );
    }

	$error = false;
    if ( ini_get('allow_url_fopen') == 0 ) {
      $error = true;
      $message = WDWLibrary::message_id(0, __('http:// wrapper is disabled in the server configuration by allow_url_fopen=0.', $this->prefix), 'error');
    }
    else {
      list($width_watermark, $height_watermark, $type_watermark) = getimagesize($update_options['built_in_watermark_url']);
      if ( $update_options['built_in_watermark_type'] == 'image' && (empty($width_watermark) OR empty($height_watermark) OR empty($type_watermark)) ) {
        $error = TRUE;
        $message = WDWLibrary::message_id(0, __('Watermark could not be set. The image URL is incorrect.', $this->prefix), 'error');
      }
      if ( $error === FALSE ) {
        WDWLibrary::bwg_image_set_watermark(0, 0, $limitstart);
        $message = WDWLibrary::message_id(0, __('All images are successfully watermarked.', $this->prefix), 'updated');
      }
    }
    $json_data = array('error' => $error, 'message' => $message);
    echo json_encode($json_data); die();
  }

  public function image_recover_all($params = array()) {
    $limitstart = WDWLibrary::get('limitstart', 0, 'intval');
    WDWLibrary::bwg_image_recover_all(0, $limitstart);
  }

  public function resize_image_thumb($params = array()) {
    global $wpdb;
    $max_width = WDWLibrary::get('img_option_width', 500, 'intval');
    $max_height = WDWLibrary::get('img_option_height', 500, 'intval');
    $limitstart = WDWLibrary::get('limitstart', 0, 'intval');

    /*  Update options only first time of the loop  */
    if ( $limitstart == 0 ) {
      $this->model->update_options_by_key( array('upload_thumb_width' => $max_width,'upload_thumb_height' => $max_height ) );
    }
    $img_ids = $wpdb->get_results($wpdb->prepare('SELECT id, thumb_url, filetype FROM ' . $wpdb->prefix . 'bwg_image LIMIT 50 OFFSET %d', $limitstart));
    foreach ($img_ids as $img_id) {
      if ( preg_match('/EMBED/', $img_id->filetype) == 1 ) {
        continue;
      }
      $file_path = str_replace("thumb", ".original", htmlspecialchars_decode(BWG()->upload_dir . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES));
      $new_file_path = htmlspecialchars_decode( BWG()->upload_dir . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES );
      if ( WDWLibrary::repair_image_original($file_path) ) {
        WDWLibrary::resize_image( $file_path, $new_file_path, $max_width, $max_height );
      }
    }
  }
}