<?php

/**
 * Class GalleriesModel_bwg
 */
class GalleriesModel_bwg {
  /**
   * Get rows data or total count.
   *
   * @param      $params
   * @param bool $total
   *
   * @return array|null|object|string
   */
  public function get_rows_data( $params, $total = FALSE ) {
    global $wpdb;
    $prepareArgs = array();
    $order = $params['order'];
    $orderby = $params['orderby'];
    $page_per = $params['items_per_page'];
    $page_num = $params['page_num'];
    $search = $params['search'];

    if ( !$total ) {
      $query = 'SELECT t1.*, count(t2.id) as images_count';
    }
    else {
      $query = 'SELECT COUNT(*)';
    }
    $query .= ' FROM (SELECT * FROM `' . $wpdb->prefix . 'bwg_gallery`';
    if ( !current_user_can('manage_options') && BWG()->options->gallery_role ) {
      $query .= " WHERE author=%d";
      $prepareArgs[] = get_current_user_id();
    }
    else {
      $query .= " WHERE author>=0";
    }
    if ( $search ) {
      $query .= ' AND `name` LIKE %s';
      $prepareArgs[] = "%" . $wpdb->esc_like($search) . "%";
    }
    if ( !$total ) {
      $query .= ' ORDER BY `' . $orderby . '` ' . $order;
      $query .= ' LIMIT %d, %d';
      $prepareArgs[] = $page_num;
      $prepareArgs[] = $page_per;
    }
    $query .= ') as t1';
    if ( !$total ) {
      $query .= ' LEFT JOIN `' . $wpdb->prefix . 'bwg_image` as t2 on t1.id=t2.gallery_id';
    }
    if ( !$total ) {
      $query .= " GROUP BY t1.id ORDER BY t1.`" . $orderby . "` " . $order;
    }
    if ( !$total ) {
      $rows = $wpdb->get_results( $wpdb->prepare($query, $prepareArgs) );
      if ( !empty($rows) ) {
        foreach ( $rows as $row ) {
          $row->preview_image = WDWLibrary::image_url_version($row->preview_image, $row->modified_date);
          $row->random_preview_image = WDWLibrary::image_url_version($row->random_preview_image, $row->modified_date);
        }
      }
    }
    else {
      if ( !empty( $prepareArgs ) ) {
          $rows = $wpdb->get_var( $wpdb->prepare( $query, $prepareArgs ) );
      } else {
          $rows = $wpdb->get_var( $query );
      }
    }
    return $rows;
  }

  /**
   * Return total count.
   *
   * @param $params
   *
   * @return array|null|object|string
   */
  public function total($params) {
    return $this->get_rows_data($params, TRUE);
  }

  /**
   * Delete.
   *
   * @param      $id
   * @param bool $all
   *
   * @return int
   */
  public function delete( $id, $all = FALSE ) {
    global $wpdb;

    $where = '';
    $image_where = '';
    $alb_gal_where = '';
    $prepareArgs = array();

    if ( !$all ) {
      $where = ' WHERE id=%d';
      $image_where = ' WHERE gallery_id=%d';
      $alb_gal_where = ' AND alb_gal_id=%d';
      $prepareArgs[] = $id;
    }

    // Remove custom post.
    if ( $all ) {
      $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'posts` WHERE `post_type`="%s"', "bwg_gallery"));
    }
    else {
      $row = $wpdb->get_row( $wpdb->prepare('SELECT `slug` FROM `' . $wpdb->prefix . 'bwg_gallery` WHERE id="%d"', $id) );
      if ( !empty($row) ) {
        WDWLibrary::bwg_remove_custom_post( array( 'slug' => $row->slug, 'post_type' => 'bwg_gallery') );
      }
    }

    if( !empty($prepareArgs) ) {
        $delete = $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_gallery`' . $where, $prepareArgs));
        $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image`' . $image_where, $prepareArgs));
        $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_album_gallery` WHERE is_album="0"' . $alb_gal_where, $prepareArgs));
    } else {
        $delete = $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'bwg_gallery`' . $where);
        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'bwg_image`' . $image_where);
        $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'bwg_album_gallery` WHERE is_album="0"' . $alb_gal_where);
    }
    if ( $delete ) {
      if ( $all ) {
        $message = 5;
      }
      else {
        $message = 3;
      }
    }
    else {
      $message = 2;
    }

    return $message;
  }

  /**
   * Duplicate.
   *
   * @param      $id
   * @param bool $all
   *
   * @return int
   */
  public function duplicate( $idtoget, $all = FALSE ) {
    global $wpdb;

    if (!$idtoget) {
      $ids = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'bwg_gallery');
    } else {
      $ids = array($idtoget);
    }
    foreach ($ids as $id) {
      $row = $this->get_row_data($id);
      if ($row) {
        $data = array(
          'name' => $row->name,
          'slug' => $row->slug,
          'description' => $row->description,
          'page_link' => '',
          'preview_image' => $row->preview_image,
          'random_preview_image' => $row->random_preview_image,
          'order' => $row->order,
          'author' => get_current_user_id(),
          'published' => $row->published,
          'gallery_type' => $row->gallery_type,
          'gallery_source' => $row->gallery_source,
          'autogallery_image_number' => $row->autogallery_image_number,
          'update_flag' => $row->update_flag,
          'modified_date' => time(),
        );

        $format = array(
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%s',
          '%d',
          '%d',
          '%d',
          '%s',
          '%s',
          '%d',
          '%s',
          '%d',
        );

        $saved = $wpdb->insert($wpdb->prefix . 'bwg_gallery', $data, $format);
        if ($saved !== FALSE) {
          $new_gallery_id = $wpdb->insert_id;

          $query = "SELECT * FROM " . $wpdb->prefix . "bwg_image where gallery_id=%d";
          $images = $wpdb->get_results( $wpdb->prepare($query,$id) );

          foreach ($images as $key => $value) {
            $old_image_id = $value->id;

            $value->gallery_id = $new_gallery_id;
            $value->id = null;
            $format = array(
              '%d',
              '%d',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%d',
              '%d',
              '%d',
              '%d',
              '%d',
              '%d',
              '%d',
              '%s',
              '%d',
              '%d',
            );
            $wpdb->insert($wpdb->prefix . 'bwg_image', (array)$value, $format);
            $new_image_id = $wpdb->insert_id;

            $query = "SELECT * FROM " . $wpdb->prefix . "bwg_image_tag where gallery_id=%d and image_id=%d";
            $image_tags = $wpdb->get_results( $wpdb->prepare($query, array($id,$old_image_id)) );

            foreach ($image_tags as $image_tag) {
              $image_tag->id = null;
              $image_tag->image_id = $new_image_id;
              $image_tag->gallery_id = $new_gallery_id;
              $format = array(
                '%d',
                '%d',
                '%d',
              );
              $wpdb->insert($wpdb->prefix . 'bwg_image_tag', (array)$image_tag, $format);
            }
          }
          $slug = $this->bwg_get_unique_slug($row->name, $id);
          // Create custom post (type is gallery).
          $custom_post_params = array(
            'id' => $new_gallery_id,
            'title' => $row->name,
            'slug' => $slug,
            'old_slug' => $slug,
            'type' => array(
              'post_type' => 'gallery',
              'mode' => '',
            ),
          );
          WDWLibrary::bwg_create_custom_post($custom_post_params);
          if ($all) {
            $message_id = 28;
          } else {
            $message_id = 11;
          }
        }
      }
    }

    return $message_id;
  }

  /**
   * Delete images without gallery.
   */
  public function delete_unknown_images() {
    global $wpdb;
    $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image` WHERE gallery_id=%d',0));
  }

  /**
   * Get images rows data or total count.
   *
   * @param      $gallery_id
   * @param      $params
   * @param bool $total
   *
   * @return array|null|object|string
   */
  public function get_image_rows_data($gallery_id, $params, $total = FALSE) {
    global $wpdb;
	  $rows = array();
    $order = $params['order'];
    $orderby = $params['orderby'];
    $page_per = $params['items_per_page'];
    $page_num = $params['page_num'];
    $search = $params['search'];
    $prepareArgs = array();

    $ecommerce_addon = function_exists('BWGEC');

    if ( !$total ) {
      $query = 'SELECT T_IMAGE.*';
      if ( $ecommerce_addon ) {
        $query .= ", T_PRICELISTS.title AS priselist_name, T_PRICELIST_ITEMS.item_longest_dimension, T_PRICELISTS.sections";
      }
    }
    else {
      $query = 'SELECT COUNT(*)';
    }
    $query .= ' FROM `' . $wpdb->prefix . 'bwg_image` AS T_IMAGE';
    if ( $ecommerce_addon ) {
      $query .= " LEFT JOIN `" . $wpdb->prefix . "wdpg_ecommerce_pricelists` AS T_PRICELISTS ON T_IMAGE.pricelist_id = T_PRICELISTS.id";
      $query .= " LEFT JOIN ( SELECT  MAX(item_longest_dimension) AS item_longest_dimension, pricelist_id  FROM  `" . $wpdb->prefix . "wdpg_ecommerce_pricelist_items` GROUP BY pricelist_id) AS T_PRICELIST_ITEMS ON T_PRICELIST_ITEMS.pricelist_id = T_PRICELISTS.id";
    }

    if ( !current_user_can('manage_options') && BWG()->options->image_role ) {
      $query .= " WHERE author=%d";
      $prepareArgs[] = get_current_user_id();
    }
    else {
      $query .= " WHERE author>=0";
    }
    $query .= " AND `gallery_id`=%d";
    $prepareArgs[] = $gallery_id;
	  $search_where = '';
    if ( $search ) {
      $search_keys = explode(' ', trim($search));
      $alt_search = '(';
      $filename_search = '(';
      $description_search = '(';
      foreach( $search_keys as $search_key) {
        $alt_search .= '`T_IMAGE`.`alt` LIKE %s AND ';
        $filename_search .= '`T_IMAGE`.`filename` LIKE %s AND ';
        $description_search .= '`T_IMAGE`.`description` LIKE %s AND ';
        $prepareArgs[] = "%" . trim($search_key) . "%";
        $prepareArgs[] = "%" . trim($search_key) . "%";
        $prepareArgs[] = "%" . trim($search_key) . "%";
      }
      $alt_search = rtrim($alt_search, 'AND ');
      $alt_search .= ')';
      $filename_search = rtrim($filename_search, 'AND ');
      $filename_search .= ')';
      $description_search = rtrim($description_search, 'AND ');
      $description_search .= ')';
      $search_where = ' AND (' . $filename_search . ' OR ' . $alt_search . ' OR ' . $description_search . ') ';
    }
	  $query .= $search_where;
    if ( !$total ) {
      $query .= ' ORDER BY `' . $orderby . '` ' . $order;
      $query .= ' LIMIT %d, %d';
      $prepareArgs[] = $page_num;
      $prepareArgs[] = $page_per;
    }
    if ( !$total ) {
      $rows = $wpdb->get_results($wpdb->prepare($query, $prepareArgs));
      if ( $ecommerce_addon ) {
        foreach ( $rows as $value ) {
          $value->not_set_items = 0;
          if ( $value->item_longest_dimension && strpos($value->sections, "downloads") !== FALSE ) {
            $file_path = str_replace("thumb", ".original", htmlspecialchars_decode(BWG()->upload_dir . $value->thumb_url, ENT_COMPAT | ENT_QUOTES));
            WDWLibrary::repair_image_original($file_path);
            list($img_width) = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));
            if ( $value->item_longest_dimension > $img_width ) {
              $value->not_set_items = 1;
            }
          }
        }
      }
      $rows['template'] = new stdClass();
      $rows['template']->id = "tempid";
      $rows['template']->gallery_id = $gallery_id;
      $rows['template']->order = 0;
      $rows['template']->published = 1;
      $rows['template']->tags = array();
      $rows['template']->image_url = "tempimage_url";
      $rows['template']->thumb_url = "tempthumb_url";
      $rows['template']->filename = "tempfilename";
      $rows['template']->date = "tempdate";
      $rows['template']->resolution = "tempresolution";
      $rows['template']->resolution_thumb = "tempthumbresolution";
      $rows['template']->size = "tempsize";
      $rows['template']->filetype = "tempfiletype";
      $rows['template']->description = "tempdescription";
      $rows['template']->alt = "tempalt";
      $rows['template']->author = get_current_user_id();
      $rows['template']->comment_count = 0;
      $rows['template']->avg_rating = 0;
      $rows['template']->rate_count = 0;
      $rows['template']->hit_count = 0;
      $rows['template']->redirect_url = '';
      $rows['template']->pricelist_id = 0;
      $rows['template']->priselist_name = '';
      $rows['template']->not_set_items = 0;
      $rows['template']->modified_date = '';

      foreach ( $rows as $key => $value ) {
        $value->tags = $this->get_tag_rows_data($value->id);
        $value->pure_image_url = $value->image_url;
        $value->pure_thumb_url = $value->thumb_url;
        $value->image_url = WDWLibrary::image_url_version($value->image_url, $value->modified_date);
        $value->thumb_url = WDWLibrary::image_url_version($value->thumb_url, $value->modified_date);
        if ( $key != 'template') {
          $value->pure_image_url = esc_url($value->pure_image_url);
          $value->pure_thumb_url = esc_url($value->pure_thumb_url);
          $value->image_url = esc_url($value->image_url);
          $value->thumb_url = esc_url($value->thumb_url);
        }
      }
    }
    else {
      $rows = $wpdb->get_var($wpdb->prepare($query, $prepareArgs));
    }
    return $rows;
  }

  /**
   * Return images total count.
   *
   * @param $gallery_id
   * @param $params
   *
   * @return array|null|object|string
   */
  public function image_total($gallery_id, $params) {
    return $this->get_image_rows_data($gallery_id, $params, TRUE);
  }

  /**
   * Get tags by image.
   *
   * @param $image_id
   *
   * @return mixed
   */
  public function get_tag_rows_data($image_id) {
    global $wpdb;
    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "terms AS table1 INNER JOIN " . $wpdb->prefix . "bwg_image_tag AS table2 ON table1.term_id=table2.tag_id WHERE table2.image_id='%d' ORDER BY table2.tag_id", $image_id));

    if ( !$rows ) {
      $rows = array();
    }
    $rows['template'] = new stdClass();
    $rows['template']->term_id = "temptagid";
    $rows['template']->name = "temptagname";

    return $rows;
  }

  /**
   * Get gallery row by id.
   *
   * @param $id
   *
   * @return stdClass
   */
  public function get_row_data( $id ) {
    $prepareArgs = array();
    if ( $id != 0 ) {
      if ( !current_user_can('manage_options') && BWG()->options->gallery_role ) {
        $where = " WHERE author = %d";
        $prepareArgs[] = get_current_user_id();
      }
      else {
        $where = " WHERE author >= 0 ";
      }
      $prepareArgs[] = $id;
      global $wpdb;
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_gallery`' . $where . ' AND id="%d"', $prepareArgs));
    }
    else {
      $row = new stdClass();
      $row->id = 0;
      $row->name = '';
      $row->slug = '';
      $row->description = '';
      $row->preview_image = '';
      $row->order = 0;
      $row->author = get_current_user_id();
      $row->images_count = 0;
      $row->published = 1;
      $row->gallery_type = '';
      $row->gallery_source = '';
      $row->autogallery_image_number = 12;
      $row->update_flag = '';
      $row->modified_date = time();
    }
    $user_data = get_userdata($row->author);
    $row->author = ($user_data != FALSE ? $user_data->display_name : '');

    return $row;
  }

  /**
   * Save.
   *
   * @param string $image_action
   *
   * @return array
   */
  public function save($image_action = '') {
    $gallery_id = $this->save_db();
    $data = $this->save_image_db( $gallery_id, $image_action );
    return array(
      'id' => $gallery_id,
      'saved' => (($gallery_id === FALSE || $data['images_saved'] === FALSE) ? FALSE : TRUE),
      'image_message' => $data['image_message'],
    );
  }

  /**
   * Save Gallery.
   *
   * @return bool|int
   */
  public function save_db() {
    global $wpdb;
    $id = WDWLibrary::get('current_id', 0, 'intval', 'POST');
    $name = WDWLibrary::get('name');
    $name = htmlentities($name, ENT_QUOTES);
    $name = $this->bwg_get_unique_name($name, $id);
    $slug = WDWLibrary::get('slug');
    $slug = $this->bwg_get_unique_slug((empty($slug) ? $name : $slug), $id);
    $old_slug = WDWLibrary::get('old_slug');
    $preview_image = WDWLibrary::get('preview_image');
    $random_preview_image = '';
    if ( $preview_image == '' ) {
      if ( $id != 0 ) {
        $random_preview_image = $wpdb->get_var($wpdb->prepare("SELECT random_preview_image FROM " . $wpdb->prefix . "bwg_gallery WHERE id='%d'", $id));
        if ( $random_preview_image == '' || !file_exists(BWG()->upload_dir . $random_preview_image) ) {
          $random_preview_image = $wpdb->get_var($wpdb->prepare("SELECT thumb_url FROM " . $wpdb->prefix . "bwg_image WHERE gallery_id='%d' ORDER BY `order`", $id));
        }
        if ( empty($random_preview_image) ) {
          $random_preview_image = $this->get_post_random_image( $_REQUEST );
        }
      }
      else {
        $random_preview_image = $this->get_post_random_image( $_REQUEST );
      }
    }
    if ( !WDWLibrary::check_external_link($preview_image) ) {
      $preview_image = wp_normalize_path($preview_image);
    }
    if ( !WDWLibrary::check_external_link($random_preview_image) ) {
      $random_preview_image = wp_normalize_path($random_preview_image);
    }
    if(empty($random_preview_image)) {
      $random_preview_image = '';
    }

    $data = array(
      'name' => $name,
      'slug' => $slug,
      'description' => strip_tags(htmlspecialchars_decode(WDWLibrary::get('description', '', FALSE)),"<b>,<p>,<a>,<strong>,<span>,<br>,<ul>,<ol>,<li>,<i>"),
      'page_link' => '',
      'preview_image' => $preview_image,
      'random_preview_image' => $random_preview_image,
      'order' => 0,
      'author' => get_current_user_id(),
      'published' => WDWLibrary::get('published', 1, 'intval'),
      'gallery_type' => WDWLibrary::get('gallery_type'),
      'gallery_source' => WDWLibrary::get('gallery_source'),
      'autogallery_image_number' => WDWLibrary::get('autogallery_image_number', 12, 'intval'),
      'update_flag' => WDWLibrary::get('update_flag'),
      'modified_date' => WDWLibrary::get('modified_date', time(), 'intval')
    );
    $format = array(
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d',
      '%d',
      '%d',
      '%s',
      '%s',
      '%d',
      '%s',
      '%d',
    );
    if ( $id == 0 ) {
      $saved = $wpdb->insert($wpdb->prefix . 'bwg_gallery', $data, $format);
      $id = $wpdb->insert_id;
    }
	  else {
      $saved = $wpdb->update($wpdb->prefix . 'bwg_gallery', $data, array( 'id' => $id ), $format);
    }

    if ( $saved !== FALSE ) {
      // Create custom post (type is gallery).
      $custom_post_params = array(
        'id' => $id,
        'title' => $name,
        'slug' => $slug,
        'old_slug' => $old_slug,
        'type' => array(
          'post_type' => 'gallery',
          'mode' => '',
        ),
      );
      WDWLibrary::bwg_create_custom_post($custom_post_params);
      return $id;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Save Images.
   *
   * @param int $gallery_id
   * @param string $image_action
   *
   * @return array
   */
  public function save_image_db( $gallery_id = 0, $image_action = '' ) {
    global $wpdb;
    $image_ids = WDWLibrary::get('ids_string');
    $image_id_array = explode(',', $image_ids);
    $save = TRUE;
    $author = get_current_user_id();
    $all = WDWLibrary::get('check_all_items', FALSE);
    $image_message = '';
    $checked_items_count = WDWLibrary::get('checked_items_count', 0, 'intval');
    foreach ( $image_id_array as $image_id ) {
      if ( $image_id ) {
        $image_url = WDWLibrary::get('image_url_' . $image_id, '', 'esc_url_raw');
        $thumb_url = WDWLibrary::get('thumb_url_' . $image_id, '', 'esc_url_raw');
        $filetype = WDWLibrary::get('input_filetype_' . $image_id);
        $is_oembed_instagram_post = FALSE;
        if ( $filetype == 'EMBED_OEMBED_INSTAGRAM_POST' ) {
          $is_oembed_instagram_post = TRUE;
          $image_url = WDWLibrary::get('image_url_' . $image_id, '');
        }
        if ( !$is_oembed_instagram_post && !WDWLibrary::check_external_link($image_url) && !file_exists(urldecode(BWG()->upload_dir . $image_url)) ) {
          continue;
        }
        $filename = WDWLibrary::get('input_filename_' . $image_id);
        $description = str_replace(array('\\', '\t'), '', WDWLibrary::get('image_description_' . $image_id, '', 'wp_filter_post_kses'));
        $description = strip_tags(htmlspecialchars_decode($description),"<b>,<p>,<a>,<strong>,<span>,<br>,<ul>,<ol>,<li>,<i>");
        $alt = str_replace(array('\\', '\t'), '',  WDWLibrary::get('image_alt_text_' . $image_id, '', 'wp_filter_post_kses'));
        $alt = preg_replace("/<a[^>]*>|<\/a>/", '', $alt);
        $alt = strip_tags(htmlspecialchars_decode($alt),"<b>,<p>,<a>,<strong>,<span>,<br>,<ul>,<ol>,<li>,<i>");
        $date = WDWLibrary::get('input_date_modified_' . $image_id, date('Ymd'));
        $size = WDWLibrary::get('input_size_' . $image_id);

        $resolution = WDWLibrary::get('input_resolution_' . $image_id);
        $resolution_thumb = WDWLibrary::get('input_resolution_thumb_' . $image_id);
        if( ($resolution_thumb == '' || $resolution_thumb == 'x') && $thumb_url != '' ) {
          $resolution_thumb = WDWLibrary::get_thumb_size( $thumb_url );
        }
        $order = WDWLibrary::get('order_input_' . $image_id, 0, 'intval');
        $redirect_url = WDWLibrary::get('redirect_url_' . $image_id, '', 'esc_url_raw');
        $tags_ids = WDWLibrary::get('tags_' . $image_id);
        $data = array(
          'gallery_id' => $gallery_id,
          'slug' => WDWLibrary::spider_replace4byte($alt),
          'description' => WDWLibrary::spider_replace4byte($description),
          'redirect_url' => $redirect_url,
          'alt' => WDWLibrary::spider_replace4byte($alt),
          'date' => date('Y-m-d H:i:s', strtotime($date)),
          'size' => $size,
          'filetype' => $filetype,
          'resolution' => $resolution,
          'resolution_thumb' => $resolution_thumb,
          'order' => $order,
        );
        $temp_image_id = $image_id;
        if ( strpos($image_id, 'pr_') !== FALSE ) {
          if ( !WDWLibrary::check_external_link($image_url) ) {
            $image_url = wp_normalize_path($image_url);
          }
          if ( !WDWLibrary::check_external_link($thumb_url) ) {
            $thumb_url = wp_normalize_path($thumb_url);
          }

          $data += array(
            'filename' => $filename,
            'image_url' => $image_url,
            'thumb_url' => $thumb_url,
            'author' => $author,
            'published' => 1,
            'comment_count' => 0,
            'avg_rating' => 0,
            'rate_count' => 0,
            'hit_count' => 0,
            'pricelist_id' => 0,
            'modified_date' => time(),
          );
          $format = array('%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s','%s','%d','%d','%d','%d','%d','%d','%d','%d');
          $save = $wpdb->insert($wpdb->prefix . 'bwg_image', $data, $format);
          $image_id = $wpdb->insert_id;
        }
        else {
          $format = array('%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d');
          if( WDWLibrary::get('ajax_task') == 'image_rotate_right' || WDWLibrary::get('ajax_task') == 'image_rotate_left' || $data['resolution_thumb'] == '' ) {
              unset($data['resolution_thumb']);
              unset($format[9]);
          }
          $save = $wpdb->update($wpdb->prefix . 'bwg_image', $data, array( 'id' => $image_id ), $format);
        }

        $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE image_id="%d" AND gallery_id="%d"', $image_id, $gallery_id));
        if ( $save !== FALSE ) {
          $tag_id_array = explode(',', $tags_ids);
          foreach ( $tag_id_array as $tag_id ) {
            if ( $tag_id ) {
              if ( strpos($tag_id, 'pr_') !== FALSE ) {
                $tag_id = substr($tag_id, 3);
              }

              if ( strpos($tag_id, 'bwg_') === 0 ) {
                // If tags added to image from image file meta keywords.
                $tag_name = str_replace('bwg_', '', $tag_id);
                $term = term_exists($tag_name, 'bwg_tag');
                if ( $term === 0 || $term === NULL || is_array($term) ) {
                  $term = wp_insert_term($tag_name, 'bwg_tag');
                  // If term exist, get the existing term id.
                  if ( is_wp_error($term) ) {
                    if ( isset($term->error_data) ) {
                      $error_data = $term->error_data;
                      $term = array();
                      $term['term_id'] = $error_data['term_exists'];
                    }
                  }
                }
                $tag_id = isset($term['term_id']) ? $term['term_id'] : 0;
              }

              if ( $tag_id ) {
                $wpdb->insert($wpdb->prefix . 'bwg_image_tag', array(
                  'tag_id' => $tag_id,
                  'image_id' => $image_id,
                  'gallery_id' => $gallery_id,
                ), array('%d','%d','%d'));
                // Increase tag count in term_taxonomy table.
                $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
              }
            }
          }
        }

        if ( !$all
          && $image_action
          && method_exists($this, $image_action)
          && isset($_POST['check_' . $temp_image_id]) ) {
          $image_message = $this->$image_action($image_id, $gallery_id);
        }
      }
    }

    $wpdb->query('SET @i = 0');
    $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `order` = @i := @i + 1 WHERE `gallery_id` = "%d" ORDER BY `order` ASC', $gallery_id));
    if ( !in_array($image_message, WDWLibrary::error_message_ids()) && $image_action && $checked_items_count ) {
      $actions = WDWLibrary::image_actions();
      $image_message = sprintf(_n('%s item successfully %s.', '%s items successfully %s.', $checked_items_count, BWG()->prefix), $checked_items_count, $actions[$image_action]['bulk_action']);
    }
    if ( $all
      && $image_action
      && method_exists($this, $image_action) ) {
      $image_message = $this->$image_action(0, $gallery_id, TRUE);
    }

    $images_saved = ($save !== FALSE) ? TRUE : FALSE;

    return array( 'images_saved' => $images_saved, 'image_message' => $image_message );
  }

  /**
   * Get POST random image.
   *
   * @param array $params
   *
   * @return string
   */
  public function get_post_random_image( $params = array() ) {
    $i = 0;
    $random_preview_image = '';
    while ( isset($params['input_filetype_pr_' . $i]) ) {
      if ( isset($params['thumb_url_pr_' . $i]) ) {
        $random_preview_image = esc_html(stripslashes($params['thumb_url_pr_' . $i]));
      }
      $i++;
    }

	  return $random_preview_image;
  }

  public function bwg_get_unique_slug( $slug, $id ) {
    global $wpdb;
    $slug = sanitize_title($slug);
    if ( $id != 0 ) {
      $query = $wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s AND id != %d", $slug, $id);
    }
    else {
      $query = $wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s", $slug);
    }
    if ( $wpdb->get_var($query) ) {
      $num = 2;
      do {
        $alt_slug = $slug . "-$num";
        $num++;
        $slug_check = $wpdb->get_var($wpdb->prepare("SELECT slug FROM " . $wpdb->prefix . "bwg_gallery WHERE slug = %s", $alt_slug));
      }
      while ( $slug_check );
      $slug = $alt_slug;
    }

    return $slug;
  }

  public function bwg_get_unique_name( $name, $id ) {
    global $wpdb;
    if ( $id != 0 ) {
      $query = $wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s AND id != %d", $name, $id);
    }
    else {
      $query = $wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s", $name);
    }
    if ( $wpdb->get_var($query) ) {
      $num = 2;
      do {
        $alt_name = $name . "-$num";
        $num++;
        $slug_check = $wpdb->get_var($wpdb->prepare("SELECT name FROM " . $wpdb->prefix . "bwg_gallery WHERE name = %s", $alt_name));
      }
      while ( $slug_check );
      $name = $alt_name;
    }

    return $name;
  }

  /**
   * Delete image.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_delete( $id, $gallery_id = 0, $all = FALSE ) {
    global $wpdb;
    $prepareArgs = array();
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = 'WHERE gallery_id=%d';
    $prepareArgs[] = $gallery_id;
    $where .= ($all ? '' : ' AND id=' . $id);
    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }
    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $delete = $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image`' . $where, $prepareArgs));

    $prepareArgs = array();
    if ( $all ) {
      $image_where = 'WHERE gallery_id=%d';
      $prepareArgs[] = $gallery_id;
    } else {
      $image_where = ' WHERE image_id=%d';
      $prepareArgs[] = $id;
    }

    $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image_comment`' . $image_where, $prepareArgs));
    $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image_rate`' . $image_where, $prepareArgs));
    $tag_ids = $wpdb->get_col($wpdb->prepare('SELECT tag_id FROM `' . $wpdb->prefix . 'bwg_image_tag`' . $image_where, $prepareArgs));
    $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'bwg_image_tag`' . $image_where, $prepareArgs));
    // Increase tag count in term_taxonomy table.
    if ( !empty($tag_ids) ) {
      foreach ( $tag_ids as $tag_id ) {
        $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
      }
    }

    if ( !empty($current_id) ) {
      // after deleted and empty image lists then update `preview_image` and `random_preview_image` colums.
      $row = $wpdb->get_row( $wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_gallery` AS `g` INNER JOIN `' . $wpdb->prefix . 'bwg_image` AS `i` ON (`g`.`id` = `i`.`gallery_id`) WHERE `g`.`id` = %d', $current_id) );
      if ( empty($row) ) {
        $wpdb->update($wpdb->prefix . 'bwg_gallery',
          array('preview_image' => '', 'random_preview_image' => ''),
          array('id' => $current_id)
        );
      }
    }

    $message = 2;
    if ( $delete ) {
      if ( $all ) {
        $message = 5;
      }
      else {
        $message = 3;
      }
    }
    return $message;
  }

  /**
   * Publish image.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_publish( $id, $gallery_id = 0, $all = FALSE ) {
    global $wpdb;
    $prepareArgs = array();
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = ' WHERE gallery_id=%d';
    $prepareArgs[] = $gallery_id;

    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET published=1' . $where, $prepareArgs) );

	  $message = 2;
    if ( $updated !== FALSE ) {
      $message = 9;
    }
    return $message;
  }

  /**
   * Unpublish image.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_unpublish( $id, $gallery_id = 0, $all = FALSE ) {
    global $wpdb;
    $prepareArgs = array();
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = ' WHERE gallery_id=%d';
    $prepareArgs[] = $gallery_id;

    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET published=0' . $where, $prepareArgs) );

	  $message = 2;
    if ( $updated !== FALSE ) {
      $message = 10;
    }
    return $message;
  }

  /**
   * Reset image.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_reset( $id, $gallery_id = 0, $all = FALSE ) {
    if ( $all ) {
      if ( $gallery_id == 0 ) {
        $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
      }
      $limit = WDWLibrary::get('limit', 0, 'intval');
      WDWLibrary::bwg_image_recover_all($gallery_id, $limit);
    }
    else {
      global $wpdb;
      $thumb_width = BWG()->options->upload_thumb_width;
      $width = BWG()->options->upload_img_width;
      $image = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_image WHERE id="%d"', $id));
      WDWLibrary::recover_image($image, $thumb_width, $width, 'gallery_page');
      $where = ($id) ? ' `id` = ' . $id : 1;
      WDWLibrary::update_image_modified_date( $where );
    }

    return 20;
  }

  /**
   * Set watermark.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_set_watermark($id, $gallery_id = 0, $all = FALSE) {
    if ( ini_get('allow_url_fopen') == 0 ) {
      $message_id = 27;
    }
    else {
      $options = new WD_BWG_Options();
      list($width_watermark, $height_watermark, $type_watermark) = @getimagesize(str_replace(' ', '%20', $options->built_in_watermark_url));
      if ( $options->built_in_watermark_type == 'image' && (empty($width_watermark) OR empty($height_watermark) OR empty($type_watermark)) ) {
        $message_id = 26;
      }
      else {
        if ( $gallery_id == 0 ) {
          $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
        }
        $limit = WDWLibrary::get('limit', 0, 'intval');
        $message_id = WDWLibrary::bwg_image_set_watermark($gallery_id, ($all ? 0 : $id), $limit);
      }
    }

    return $message_id;
  }

  /**
   * Rotate left.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_rotate_left($id, $gallery_id = 0, $all = FALSE) {
    return $this->rotate(90, $id, $gallery_id, $all);
  }

  /**
   * Rotate right.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_rotate_right($id, $gallery_id = 0, $all = FALSE) {
    return $this->rotate(270, $id, $gallery_id, $all);
  }

  /**
   * Rotate.
   *
   * @param      $edit_type
   * @param int  $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function rotate( $edit_type, $id = 0, $gallery_id = 0, $all = FALSE ) {
    global $wpdb;
    $prepareArgs = array();
    $image_id = ($all ? 0 : $id);
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }

    $where = 1;
    if ($gallery_id) {
      $where = ' `gallery_id` = %d';
      $prepareArgs[] = $gallery_id;
      if($image_id) {
        $where .= ' AND `id` = %d';
        $prepareArgs[] = $image_id;
      }
    }
	  //$where = ( ($gallery_id) ? ' `gallery_id` = ' . $gallery_id . ($image_id ? ' AND `id` = ' . $image_id : '' ) : 1 );
    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    if( !empty($prepareArgs) ) {
        $images_data = $wpdb->get_results($wpdb->prepare('SELECT id, image_url, thumb_url, resolution_thumb FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where, $prepareArgs));
    } else {
        $images_data = $wpdb->get_results('SELECT id, image_url, thumb_url, resolution_thumb FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where);
    }
    @ini_set('memory_limit', '-1');
    foreach ( $images_data as $image_data ) {
      $image_data->image_url = stripcslashes($image_data->image_url);
      $filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->image_url, ENT_COMPAT | ENT_QUOTES);
      $thumb_filename = htmlspecialchars_decode(BWG()->upload_dir . $image_data->thumb_url, ENT_COMPAT | ENT_QUOTES);
      list($width_rotate, $height_rotate, $type_rotate) = getimagesize($filename);
      if ( $edit_type == '270' || $edit_type == '90' ) {
        if ( $type_rotate == 2 ) {
          $source = imagecreatefromjpeg($filename);
          $thumb_source = imagecreatefromjpeg($thumb_filename);
          $rotate = imagerotate($source, $edit_type, 0);
          $thumb_rotate = imagerotate($thumb_source, $edit_type, 0);
          imagejpeg($thumb_rotate, $thumb_filename, BWG()->options->jpeg_quality);
          imagejpeg($rotate, $filename, BWG()->options->jpeg_quality);
          imagedestroy($source);
          imagedestroy($rotate);
          imagedestroy($thumb_source);
          imagedestroy($thumb_rotate);
        }
        elseif ( $type_rotate == 3 ) {
          $source = imagecreatefrompng($filename);
          $thumb_source = imagecreatefrompng($thumb_filename);
          imagealphablending($source, FALSE);
          imagealphablending($thumb_source, FALSE);
          imagesavealpha($source, TRUE);
          imagesavealpha($thumb_source, TRUE);
          $rotate = imagerotate($source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
          $thumb_rotate = imagerotate($thumb_source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
          imagealphablending($rotate, FALSE);
          imagealphablending($thumb_rotate, FALSE);
          imagesavealpha($rotate, TRUE);
          imagesavealpha($thumb_rotate, TRUE);
          imagepng($rotate, $filename, BWG()->options->png_quality);
          imagepng($thumb_rotate, $thumb_filename, BWG()->options->png_quality);
          imagedestroy($source);
          imagedestroy($rotate);
          imagedestroy($thumb_source);
          imagedestroy($thumb_rotate);
        }
        elseif ( $type_rotate == 1 ) {
          $source = imagecreatefromgif($filename);
          $thumb_source = imagecreatefromgif($thumb_filename);
          imagealphablending($source, FALSE);
          imagealphablending($thumb_source, FALSE);
          imagesavealpha($source, TRUE);
          imagesavealpha($thumb_source, TRUE);
          $rotate = imagerotate($source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
          $thumb_rotate = imagerotate($thumb_source, $edit_type, imageColorAllocateAlpha($source, 0, 0, 0, 127));
          imagealphablending($rotate, FALSE);
          imagealphablending($thumb_rotate, FALSE);
          imagesavealpha($rotate, TRUE);
          imagesavealpha($thumb_rotate, TRUE);
          imagegif($rotate, $filename);
          imagegif($thumb_rotate, $thumb_filename);
          imagedestroy($source);
          imagedestroy($rotate);
          imagedestroy($thumb_source);
          imagedestroy($thumb_rotate);
        }
      }
        $resolution_thumb = isset($image_data->resolution_thumb) ? $image_data->resolution_thumb : '';
        if ( $resolution_thumb == '' ) {
          $resolution_thumb = WDWLibrary::get_thumb_size( $image_data->thumb_url );
        }
        if ($resolution_thumb != '') {
            $res = explode('x', $resolution_thumb);
            $resolution_thumb = $res[1] . "x" . $res[0];
            WDWLibrary::update_thumb_dimansions($resolution_thumb,"id = $image_data->id");
        }
    }

    WDWLibrary::update_image_modified_date($where);

	return 22;
  }

  /**
   * Recreate thumbnail.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_recreate_thumbnail($id, $gallery_id = 0, $all = FALSE) {
    $image_id = ($all ? 0 : $id);
    global $wpdb;
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
	  //$where = ( ($gallery_id) ? ' `gallery_id` = ' . $gallery_id . ( $image_id ? ' AND `id` = ' . $image_id : '' ) : 1 );
    $where = 1;
    $prepareArgs = array();
    if ($gallery_id) {
      $where = ' `gallery_id` = %d';
      $prepareArgs[] = $gallery_id;
      if($image_id) {
        $where .= ' AND `id` = %d';
        $prepareArgs[] = $image_id;
      }
    }
    if( !empty($prepareArgs) ) {
        $img_ids = $wpdb->get_results($wpdb->prepare('SELECT id, thumb_url FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where, $prepareArgs));
    } else {
        $img_ids = $wpdb->get_results('SELECT id, thumb_url FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where);
    }
    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";

    }
    foreach ( $img_ids as $img_id ) {
      $file_path = str_replace("thumb", ".original", htmlspecialchars_decode(BWG()->upload_dir . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES));
      $new_file_path = htmlspecialchars_decode(BWG()->upload_dir . $img_id->thumb_url, ENT_COMPAT | ENT_QUOTES);
      if ( WDWLibrary::repair_image_original($file_path) ) {
        WDWLibrary::resize_image( $file_path, $new_file_path, BWG()->options->upload_thumb_width, BWG()->options->upload_thumb_height );
        $resolution_thumb = WDWLibrary::$thumb_dimansions;
        if($resolution_thumb != '') {
            WDWLibrary::update_thumb_dimansions($resolution_thumb,"id = $img_id->id");
        }
      }
    }
    WDWLibrary::update_image_modified_date( $where, $prepareArgs );

    return 23;
  }

  /**
   * Resize image.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_resize($id, $gallery_id = 0, $all = FALSE) {
    $image_id = ($all ? 0 : $id);
    global $wpdb;
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $image_width = WDWLibrary::get('image_width', 1600, 'intval');
    $image_height = WDWLibrary::get('image_height', 1200, 'intval');
	  $where = ' gallery_id=%d';
    $prepareArgs = array($gallery_id);
    if( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $images = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_image` WHERE ' . $where, $prepareArgs) );
    if ( !empty($images) ) {
      foreach ( $images as $image ) {
        $file_path = BWG()->upload_dir . $image->image_url;
        $thumb_filename = BWG()->upload_dir . $image->thumb_url;
        $original_filename = str_replace('/thumb/', '/.original/', $thumb_filename);
        if ( WDWLibrary::repair_image_original($original_filename) ) {
          WDWLibrary::resize_image( $original_filename, $file_path, $image_width, $image_height );
        }
      }
    }
    WDWLibrary::update_image_modified_date( $where );

    return 24;
  }

  /**
   * Edit image alt/description/redirect URL.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_edit($id, $gallery_id = 0, $all = FALSE) {
    $title = WDWLibrary::get('title');
    $desc = WDWLibrary::get('desc');
    $redirecturl = WDWLibrary::get('redirecturl', '', 'esc_url_raw');
    $prepareArgs = array($title,$desc,$redirecturl);
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = ' WHERE gallery_id=%d';
    $prepareArgs[] = $gallery_id;
    $format = array('%d');
    if( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    global $wpdb;

    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `alt`="%s", `description`="%s", `redirect_url`="%s"' . $where, $prepareArgs) );
    $message = 2;
    if ( $updated !== FALSE ) {
      $message = 25;
    }
    return $message;
  }

  /**
   * Edit image alt/title.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_edit_alt( $id, $gallery_id = 0, $all = FALSE ) {
    $title = WDWLibrary::get('title');

    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = ' WHERE gallery_id=%d';
    $prepareArgs = array( $title, $gallery_id );

    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }
    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }

    global $wpdb;
    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `alt`="%s"' . $where, $prepareArgs) );
    $message = 2;
    if ( $updated !== FALSE ) {
      $message = 25;
    }
    return $message;
  }

  /**
   * Edit image description.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_edit_description( $id, $gallery_id = 0, $all = FALSE ) {
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $desc = WDWLibrary::get('desc');
    $where = ' WHERE gallery_id=%d';
    $prepareArgs = array( $desc, $gallery_id );
    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }

    global $wpdb;
    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `description`="%s"' . $where, $prepareArgs) );
    $message = 2;
    if ( $updated !== FALSE ) {
      $message = 25;
    }
    return $message;
  }

  /**
   * Edit image redirect url.
   *
   * @param      $id
   * @param      $gallery_id
   * @param bool $all
   *
   * @return int
   */
  public function image_edit_redirect( $id, $gallery_id = 0, $all = FALSE ) {
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $redirecturl = WDWLibrary::get('redirecturl', '', 'esc_url_raw');
    $prepareArgs = array( $redirecturl, $gallery_id );
    $where = ' WHERE gallery_id=%d';
    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }

    global $wpdb;
    $updated = $wpdb->query( $wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET `redirect_url`="%s"' . $where, $prepareArgs) );
    $message = 2;
    if ( $updated !== FALSE ) {
      $message = 25;
    }
    return $message;
  }

  public function image_add_tag($id, $gallery_id = 0, $all = FALSE) {
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }

    $tag_ids = WDWLibrary::get('added_tags_id');
    $tag_act = WDWLibrary::get('added_tags_act');
    $tag_ids_array = explode(',', $tag_ids);
    global $wpdb;
    $where = ' WHERE gallery_id=%d';
    $prepareArgs = array($gallery_id);
    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND (`alt` LIKE %s';
      $where .= ' OR `filename` LIKE %s';
      $where .= ' OR `description` LIKE %s)';
      $prepareArgs[] = "%" . trim($search) . "%";
      $prepareArgs[] = "%" . trim($search) . "%";
      $prepareArgs[] = "%" . trim($search) . "%";
    }
    $images = $wpdb->get_results($wpdb->prepare('SELECT * FROM `' . $wpdb->prefix . 'bwg_image`' . $where, $prepareArgs));
    foreach ( $images as $image ) {
      foreach ( $tag_ids_array as $tag_id ) {
        if ( $tag_id ) {
          $exist_tag = $wpdb->get_var($wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d" AND image_id="%d" AND gallery_id="%d"', $tag_id, $image->id, $gallery_id));
          if( $tag_act == 'add' ) {
            if ( $exist_tag == NULL ) {
              $wpdb->insert($wpdb->prefix . 'bwg_image_tag', array(
                'tag_id' => $tag_id,
                'image_id' => $image->id,
                'gallery_id' => $gallery_id,
              ));
              // Increase tag count in term_taxonomy table.
              $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
            }
          } elseif( $tag_act == 'remove' ) {
            if ( $exist_tag != NULL ) {
              $wpdb->delete($wpdb->prefix . 'bwg_image_tag', array(
                'tag_id' => $tag_id,
                'image_id' => $image->id,
                'gallery_id' => $gallery_id,
              ));
              // Increase tag count in term_taxonomy table.
              $wpdb->query($wpdb->prepare('UPDATE ' . $wpdb->prefix . 'term_taxonomy SET count="%d" WHERE term_id="%d"', $wpdb->get_var($wpdb->prepare('SELECT COUNT(image_id) FROM ' . $wpdb->prefix . 'bwg_image_tag WHERE tag_id="%d"', $tag_id)), $tag_id));
            }
          }
        }
      }
    }

    return 25;
  }

  public function set_image_pricelist($id, $gallery_id = 0, $all = FALSE) {
    global $wpdb;
    $pricelist_id = WDWLibrary::get('image_pricelist_id', 0, 'intval');
    $item_longest_dimension = $wpdb->get_var('SELECT MAX(item_longest_dimension) AS item_longest_dimension  FROM ' . $wpdb->prefix . 'wdpg_ecommerce_pricelist_items AS T_PRICELIST_ITEMS LEFT JOIN ' . $wpdb->prefix . 'wdpg_ecommerce_pricelists AS T_PRICELISTS ON T_PRICELIST_ITEMS.pricelist_id = T_PRICELISTS.id  WHERE T_PRICELIST_ITEMS.pricelist_id="' . $pricelist_id . '" AND T_PRICELISTS.sections LIKE "%downloads%"');
    $not_set_items = array();
    if ($pricelist_id) {
      $image_id = ($all ? 0 : $id);
      if ( $gallery_id == 0 ) {
        $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
      }
      $where = ' WHERE gallery_id=%d';
      $prepareArgs = array($gallery_id);
      if ( !$all ) {
        $where .= ' AND id=%d';
        $prepareArgs[] = $id;
      }

      $search = WDWLibrary::get('s');
      if ( $search ) {
        $where .= ' AND `filename` LIKE %s';
        $prepareArgs[] = "%" . $search . "%";
      }
      $image_ids_col = $wpdb->get_col($wpdb->prepare('SELECT id FROM `' . $wpdb->prefix . 'bwg_image`' . $where, $prepareArgs) );
      foreach ($image_ids_col as $image_id) {
        $thumb_url_image_id = WDWLibrary::get('thumb_url_' . $image_id, '', 'esc_url_raw');
        $file_path = str_replace("thumb", ".original", htmlspecialchars_decode(BWG()->upload_dir . $thumb_url_image_id, ENT_COMPAT | ENT_QUOTES));
        WDWLibrary::repair_image_original($file_path);
        list($img_width) = @getimagesize(htmlspecialchars_decode($file_path, ENT_COMPAT | ENT_QUOTES));
        if ($item_longest_dimension > $img_width && $img_width) {
          $not_set_items[] = $image_id . "-" . $item_longest_dimension;
        }
        $wpdb->update($wpdb->prefix . 'bwg_image', array('pricelist_id' => $pricelist_id), array('id' => $image_id), array('%d'));
      }
    }
    if ( empty($not_set_items) === FALSE ) {
      echo "<div class='bwg_msg'>" . __('Selected pricelist item longest dimension greater than some original images dimensions.', BWG()->prefix) . "</div>";
    }
  }


  public function remove_image_pricelist() {
    global $wpdb;
    $image_id = WDWLibrary::get('remove_pricelist', 0, 'intval');
    if ( $image_id ) {
      $wpdb->update($wpdb->prefix . 'bwg_image', array( 'pricelist_id' => 0 ), array( 'id' => $image_id ));
    }
  }

  public function remove_pricelist_all($id, $gallery_id = 0, $all = FALSE) {
    global $wpdb;
    if ( $gallery_id == 0 ) {
      $gallery_id = WDWLibrary::get('current_id', 0, 'intval');
    }
    $where = ' WHERE gallery_id=' . $gallery_id;
    $prepareArgs = array($gallery_id);
    if ( !$all ) {
      $where .= ' AND id=%d';
      $prepareArgs[] = $id;
    }

    $search = WDWLibrary::get('s');
    if ( $search ) {
      $where .= ' AND `filename` LIKE %s';
      $prepareArgs[] = "%" . $search . "%";
    }
    $wpdb->query($wpdb->prepare('UPDATE `' . $wpdb->prefix . 'bwg_image` SET pricelist_id=0' . $where, $prepareArgs));
  }

  /**
   * Ordering.
   *
   * @param array $orders
   *
   * @return int
   */
  public function ordering( $orders = array() ) {
    global $wpdb;
    $message_id = 2;
    if ( !empty($orders) ) {
      foreach ( $orders as $order => $id ) {
        $upd_query = 'UPDATE ' . $wpdb->prefix . 'bwg_gallery SET `order` = %d WHERE `id` = %d';
        $update = $wpdb->query($wpdb->prepare($upd_query, array($order,$id)));
        if ( $update ) {
          $message_id = 1;
        }
      }
    }

    return $message_id;
  }
}
