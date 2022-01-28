<?php
class BWGModelSite {

  public function get_theme_row_data($id = 0) {
    global $wpdb;
    if (!$id) {
      $id = $wpdb->get_var('SELECT id FROM ' . $wpdb->prefix . 'bwg_theme WHERE default_theme=1');
    }
    $row = new WD_BWG_Theme($id);
    return $row;
  }

  public function get_gallery_row_data($id = 0, $from = '') {
    global $wpdb;
    $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_gallery WHERE id="%d"', $id));
    if ( $row ) {
      $row->permalink = '';
      if ($from != '') {
        $row->permalink = self::get_custom_post_permalink( array( 'slug' => $row->slug, 'post_type' => 'gallery' ) );
      }
      if ( !empty($row->preview_image) ) {
        $row->preview_image = WDWLibrary::image_url_version($row->preview_image, $row->modified_date);
      }
      if ( !empty($row->random_preview_image) ) {
        $row->random_preview_image = WDWLibrary::image_url_version($row->random_preview_image, $row->modified_date);
      }
    }
    else if ( !$id ) { /* Select all Galleries */
      $row_count = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'bwg_gallery WHERE published=1');
      if ( !$row_count ) {
        return false;
      }
      else {
        $row = new stdClass();
        $row->id = 0;
        $row->name = '';
      }
    }

    return $row;
  }

  public function get_album_row_data( $id, $from ) {
    global $wpdb;
    if ( $id == 0 ) {
      $row = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'bwg_gallery');
    }
    else {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'bwg_album WHERE id="%d"', $id));
    }
    if ( is_object($row) ) {
      if ( $from ) {
        $row->permalink = WDWLibrary::get_custom_post_permalink(array( 'slug' => $row->slug, 'post_type' => 'album' ));
      }
      if ( !empty($row->preview_image) ) {
        $row->preview_image = WDWLibrary::image_url_version($row->preview_image, $row->modified_date);
      }
      if ( !empty($row->random_preview_image) ) {
        $row->random_preview_image = WDWLibrary::image_url_version($row->random_preview_image, $row->modified_date);
      }
    }
    return $row;
  }


  /**
   * Get images from Gallery Group for XML Sitemap.
   * Selecting only first level Galleries to avoid unnecessarily big data.
   * Inner Gallery Groups data will not be included in sitemap.
  */
  public function get_image_rows_data_from_album($album_id) {

    global $wpdb;
    $format = '';
    if( $album_id ) {
      $where = 'image.gallery_id IN (SELECT alb_gal_id FROM `' . $wpdb->prefix . 'bwg_album_gallery` as albgal WHERE albgal.album_id=%d AND (SELECT gal.published from `' . $wpdb->prefix . 'bwg_gallery` as gal WHERE gal.id=albgal.alb_gal_id))';
      $format = intval( $album_id );
    } else {
      $where = '(SELECT gal.published from `' . $wpdb->prefix . 'bwg_gallery` as gal WHERE gal.id=image.gallery_id)=%d';
      $format = 1;
    }

    $query = 'SELECT image.* FROM `' . $wpdb->prefix . 'bwg_image` as image WHERE image.published=1 AND ' . $where;
    return $wpdb->get_results($wpdb->prepare($query, $format));
  }

  public function get_tags_rows_data($gallery_id) {
    global $wpdb;
    if( $gallery_id ) {
      $row = $wpdb->get_results($wpdb->prepare('Select t1.* FROM ' . $wpdb->prefix . 'terms AS t1 LEFT JOIN ' . $wpdb->prefix . 'term_taxonomy AS t2 ON t1.term_id = t2.term_id' . ($gallery_id ? ' LEFT JOIN (SELECT DISTINCT tag_id , gallery_id  FROM ' . $wpdb->prefix . 'bwg_image_tag) AS t3 ON t1.term_id=t3.tag_id' : '') . ' WHERE taxonomy="bwg_tag"' . ($gallery_id ? ' AND t3.gallery_id="%d"' : '') . ' ORDER BY t1.name  ASC', $gallery_id));
    } else {
      $row = $wpdb->get_results('Select t1.* FROM ' . $wpdb->prefix . 'terms AS t1 LEFT JOIN ' . $wpdb->prefix . 'term_taxonomy AS t2 ON t1.term_id = t2.term_id' . ($gallery_id ? ' LEFT JOIN (SELECT DISTINCT tag_id , gallery_id  FROM ' . $wpdb->prefix . 'bwg_image_tag) AS t3 ON t1.term_id=t3.tag_id' : '') . ' WHERE taxonomy="bwg_tag"' . ($gallery_id ? ' AND t3.gallery_id="%d"' : '') . ' ORDER BY t1.name  ASC');
    }
    return $row;
  }

  public function get_alb_gals_row( $bwg, $id, $albums_per_page, $sort_by, $order_by, $pagination_type = 0, $from = '' ) {
    $prepareArgs = array();
    if ( $albums_per_page < 0 ) {
      $albums_per_page = 0;
    }
    global $wpdb;
    $order_by = 'ORDER BY `' . ( ( !empty( $from ) && $from === 'widget' ) ? 'id' : $sort_by ) . '` ' . $order_by;
    if ( $sort_by == 'random' || $sort_by == 'RAND()' ) {
      $order_by = 'ORDER BY RAND()';
    }
    $search_where = '';
    $search_value = trim( WDWLibrary::get( 'bwg_search_' . $bwg ) );
    if ( !empty( $search_value ) ) {
      $search_keys = explode( ' ', $search_value );
      $alt_search = '(';
      $description_search = '(';
      foreach( $search_keys as $search_key) {
        $alt_search .= '`{{table}}`.`name` LIKE %s AND ';
        $description_search .= '`{{table}}`.`description` LIKE %s AND ';
        $prepareArgs[] = "%" . trim($search_key) . "%";
        $prepareArgs[] = "%" . trim($search_key) . "%";
      }
      $alt_search = rtrim( $alt_search, 'AND ' );
      $alt_search .= ')';
      $description_search = rtrim( $description_search, 'AND ' );
      $description_search .= ')';
      $search_where = ' AND (' . $alt_search . ' OR ' . $description_search . ')';

    }
    $limit = 0;
    $page_number = WDWLibrary::get( 'page_number_' . $bwg, 0, 'intval' );
    if ( $page_number ) {
      $limit = ( (int)$page_number - 1 ) * $albums_per_page;
    }
    $limit_str = '';
    if ( $albums_per_page ) {
      $limit_str = 'LIMIT ' . $limit . ',' . $albums_per_page;
    }
    if ( WDWLibrary::get( 'action_' . $bwg ) == 'back' && ( $pagination_type == 2 || $pagination_type == 3 ) ) {
      if ( $page_number ) {
        if ( $albums_per_page ) {
          $limit = $albums_per_page * $page_number;
          $limit_str = 'LIMIT 0,' . $limit;
        }
      }
    }
    // Select all galleries.
    if ( $id == 0 ) {
	    $query = 'SELECT * FROM `' . $wpdb->prefix . 'bwg_gallery` WHERE `published`=1' . str_replace('{{table}}', $wpdb->prefix . 'bwg_gallery', $search_where);
      $limitation = ' ' . $order_by . ' ' . $limit_str;
      $sql = $query . $limitation;
      if( !empty($prepareArgs) ) {
          $rows = $wpdb->get_results($wpdb->prepare($sql, $prepareArgs));
          $total = $wpdb->get_var($wpdb->prepare('SELECT count(*) FROM `' . $wpdb->prefix . 'bwg_gallery` WHERE `published`=1' . str_replace('{{table}}', $wpdb->prefix . 'bwg_gallery', $search_where), $prepareArgs));
      } else {
          $rows = $wpdb->get_results($sql);
          $total = $wpdb->get_var('SELECT count(*) FROM `' . $wpdb->prefix . 'bwg_gallery` WHERE `published`=1' . str_replace('{{table}}', $wpdb->prefix . 'bwg_gallery', $search_where));
      }
    }
    else {
      $prepareArgsnew = array_merge($prepareArgs, $prepareArgs);
      $query  = '( SELECT t.*, t1.preview_image, t1.random_preview_image, t1.name, t1.description, t1.slug, t1.modified_date FROM `' . $wpdb->prefix . 'bwg_album_gallery` as t';
      $query .= ' LEFT JOIN `' . $wpdb->prefix . 'bwg_album` as t1 ON (t.is_album=1 AND t.alb_gal_id = t1.id)';
      $query .= ' WHERE t.album_id="' . $id . '"';
      $query .= ' AND t1.published=1' . str_replace( '{{table}}', 't1', $search_where );
      $query .= ') ';
      $query .= ' UNION ';
      $query .= '( SELECT t.*, t2.preview_image, t2.random_preview_image, t2.name, t2.description, t2.slug, t2.modified_date FROM `' . $wpdb->prefix . 'bwg_album_gallery` as t';
      $query .= ' LEFT JOIN `' . $wpdb->prefix . 'bwg_gallery` as t2 ON (t.is_album=0 AND t.alb_gal_id = t2.id)';
      $query .= ' WHERE t.album_id="' . $id . '"';
      $query .= ' AND t2.published=1' . str_replace( '{{table}}', 't2', $search_where );
      $query .= ')';
      $limitation = ' ' . $order_by . ' ' . $limit_str;
      $sql = $query . $limitation;
      if( !empty($prepareArgs) ) {
          $rows = $wpdb->get_results($wpdb->prepare($sql, $prepareArgsnew));
          $total = count($wpdb->get_results($wpdb->prepare($query, $prepareArgsnew)));
      } else {
          $rows = $wpdb->get_results($sql);
          $total = count($wpdb->get_results($query));
      }
    }
    if ( $rows ) {
      foreach ( $rows as $row ) {
        $row->def_type = isset( $row->is_album ) && $row->is_album ? 'album' : 'gallery';
        if ( $from ) {
          $row->permalink = WDWLibrary::get_custom_post_permalink( array( 'slug' => $row->slug, 'post_type' => $row->def_type ) );
        }
        else {
          $row->permalink = '';
        }

        if ( !empty( $row->preview_image ) ) {
          $row->resolution_thumb = WDWLibrary::get_thumb_size( $row->preview_image );
          if ( $row->resolution_thumb == "" ) {
            $row->resolution_thumb = $this->get_album_preview_thumb_dimensions( $row->preview_image );
          }

          $row->preview_image = WDWLibrary::image_url_version( $row->preview_image, $row->modified_date );
        }
        if ( !empty( $row->random_preview_image ) ) {
          $row->resolution_thumb = WDWLibrary::get_thumb_size( $row->random_preview_image );
          if ( $row->resolution_thumb == "" ) {
            $row->resolution_thumb = $this->get_album_preview_thumb_dimensions( $row->random_preview_image );
          }
          $row->random_preview_image = WDWLibrary::image_url_version( $row->random_preview_image, $row->modified_date );
        }
        if ( !$row->preview_image ) {
          $row->preview_image = $row->random_preview_image;
        }

        if ( !$row->preview_image ) {
          $row->preview_image = BWG()->plugin_url . '/images/no-image.png';
          $row->preview_path = BWG()->plugin_dir . '/images/no-image.png';
        }
        else {
          $parsed_prev_url = parse_url( $row->preview_image, PHP_URL_SCHEME );
          if ( $parsed_prev_url == 'http' || $parsed_prev_url == 'https' ) {
            $row->preview_path = $row->preview_image;
            $row->preview_image = $row->preview_image;
          }
          else {
            $row->preview_path = BWG()->upload_dir . $row->preview_image;
            $row->preview_image = BWG()->upload_url . $row->preview_image;
          }
        }

        $row->description = wpautop( $row->description );
      }
    }

    $page_nav[ 'limit' ] = 1;
    $page_nav[ 'total' ] = $total;
    $page_number = WDWLibrary::get( 'page_number_' . $bwg, 0, 'intval' );
    if ( $page_number ) {
      $page_nav[ 'limit' ] = (int)$page_number;
    }

    return array( 'rows' => $rows, 'page_nav' => $page_nav );
  }

  /**
   * Get thumb resolution from bwg_image row.
   *
   * @param  string $thumb_url
   *
   * @return string $resolution
   */
  public function get_album_preview_thumb_dimensions( $thumb_url ) {
    global $wpdb;
    $resolution = $wpdb->get_var($wpdb->prepare('SELECT resolution_thumb FROM ' . $wpdb->prefix . 'bwg_image WHERE thumb_url = "%s"', $thumb_url));
    return $resolution;
  }
}
