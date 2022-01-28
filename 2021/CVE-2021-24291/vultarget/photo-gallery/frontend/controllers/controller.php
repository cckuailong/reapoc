<?php
class BWGControllerSite {

  private $model;
  private $view;
  public $thumb_urls;

  public function __construct( $view = 'Thumbnails' ) {
    require_once BWG()->plugin_dir . "/frontend/models/model.php";
    $this->model = new BWGModelSite();
    require_once BWG()->plugin_dir . "/frontend/views/view.php";
    require_once BWG()->plugin_dir . '/frontend/views/BWGView' . $view . '.php';
    $view_class = 'BWGView' . $view;
    $this->view = new $view_class();

    do_action('bwg_before_init_gallery');
  }

  public function execute( $params = array(), $from_shortcode = 0, $bwg = 0 ) {
    $theme_id = $params['theme_id'];
    $theme_row = $this->model->get_theme_row_data($theme_id);
    $params['pagination_default_style'] = 0;

    if (!isset($params['type'])) {
      $params['type'] = '';
    }
    $sort_by = WDWLibrary::get('sortImagesByValue_' . $bwg);
    if ( !empty($sort_by) ) {
      if ($sort_by == 'random') {
        $params['sort_by'] = 'RAND()';
      } else {
        if (in_array($sort_by, array('default', 'filename', 'size'))) {
          $params['sort_by'] = $sort_by;
        }
      }
    }

    if ( strpos($params['gallery_type'], 'album') !== FALSE ) { //Album views (compact/masonry/extended).
      // View type.
      $params['view_type'] = 'album';
      // Type in album view (album or gallery).
      $album_view_type = !empty($params['type']) ? $params['type'] : $params['view_type'];
      // Album or gallery in album.
      $params['album_view_type'] = WDWLibrary::get('type_' . $bwg, $album_view_type);
      // Album or gallery id.
      $params['album_gallery_id'] = WDWLibrary::get('album_gallery_id_' . $bwg, $params['album_id'], 'intval');
      $params['cur_alb_gal_id'] = $params['album_gallery_id'];

      if (isset($params['compuct_album_image_thumb_width'])) { // Compact album view.
        // Gallery type in album (thumbnail/masonry/mosaic).
        $params['gallery_view_type'] = $params['compuct_album_view_type'];
        $params['image_enable_page'] = $params['compuct_album_enable_page'];
        $params['container_id'] = 'bwg_album_compact_' . $bwg;
        /* Set theme parameters for back button.*/
        $theme_row->back_padding = $theme_row->album_compact_back_padding;
        $theme_row->back_font_size = $theme_row->album_compact_back_font_size;
        $theme_row->back_font_style = $theme_row->album_compact_back_font_style;
        $theme_row->back_font_weight = $theme_row->album_compact_back_font_weight;
        $theme_row->back_font_color = $theme_row->album_compact_back_font_color;
      } elseif (isset($params['extended_album_image_thumb_width'])) { // Extended album view.
        // Gallery type in album (thumbnail/masonry/mosaic).
        $params['gallery_view_type'] = $params['extended_album_view_type'];
        $params['image_enable_page'] = $params['extended_album_enable_page'];
        $params['container_id'] = 'bwg_album_extended_' . $bwg;
        /* Set theme parameters for back button.*/
        $theme_row->back_padding = $theme_row->album_extended_back_padding;
        $theme_row->back_font_size = $theme_row->album_extended_back_font_size;
        $theme_row->back_font_style = $theme_row->album_extended_back_font_style;
        $theme_row->back_font_weight = $theme_row->album_extended_back_font_weight;
        $theme_row->back_font_color = $theme_row->album_extended_back_font_color;
      } elseif (isset($params['masonry_album_thumb_width'])) {
        $params['gallery_view_type'] = 'masonry';
        $params['image_enable_page'] = $params['masonry_album_enable_page'];
        $params['container_id'] = 'bwg_album_masonry_' . $bwg;
        /* Set theme parameters for back button.*/
        $theme_row->back_padding = $theme_row->album_masonry_back_padding;
        $theme_row->back_font_size = $theme_row->album_masonry_back_font_size;
        $theme_row->back_font_style = $theme_row->album_masonry_back_font_style;
        $theme_row->back_font_weight = $theme_row->album_masonry_back_font_weight;
        $theme_row->back_font_color = $theme_row->album_masonry_back_font_color;
      }

      $params['showthumbs_name'] = $params['show_album_name'];
      if ($params['album_view_type'] == 'album') { // Album in album.
        $from = (isset($params['from']) ? esc_html($params['from']) : 0);
        $album_row = $this->model->get_album_row_data($params['album_gallery_id'], $from === "widget");
        $params['album_row'] = $album_row;
        if (isset($album_row->published) && $album_row->published == 0) {
          return;
        }
        if (!$params['album_row']) {
          echo WDWLibrary::message(__('There is no album selected or the gallery was deleted.', BWG()->prefix), 'wd_error');
          return;
        }
        if ('xml_sitemap' == $from_shortcode) {
          return $this->model->get_image_rows_data_from_album($album_row->id);
        }

        // Disable features for album.
        $params['gallery_download'] = FALSE;
        $params['show_sort_images'] = FALSE;
        $params['show_tag_box'] = FALSE;
        $params['gallery_id'] = 0;
        if ($params['gallery_view_type'] == 'slideshow') {
          $params['gallery_type'] = 'slideshow';
        } elseif ($params['gallery_view_type'] == 'image_browser') {
          $params['gallery_type'] = 'image_browser';
          $params['pagination_default_style'] = 1;
        } elseif ($params['gallery_view_type'] == 'blog_style') {
          $params['gallery_type'] = 'blog_style';
        } elseif ($params['gallery_view_type'] == 'carousel') {
          $params['gallery_type'] = 'carousel';
        }

        if (isset($params['compuct_album_image_thumb_width'])) { // Compact album view.
          $params['image_enable_page'] = $params['compuct_album_enable_page'];
          $params['images_per_page'] = $params['compuct_albums_per_page'];
          $params['items_col_num'] = $params['compuct_album_column_number'];
        } elseif (isset($params['extended_album_image_thumb_width'])) { // Extended album view.
          $params['image_enable_page'] = $params['extended_album_enable_page'];
          $params['images_per_page'] = $params['extended_albums_per_page'];
          $params['items_col_num'] = $params['extended_album_image_column_number'];
          $params['image_column_number'] = $params['extended_album_image_column_number'];
        } elseif (isset($params['masonry_album_thumb_width'])) {
          $params['image_enable_page'] = $params['masonry_album_enable_page'];
          $params['images_per_page'] = $params['masonry_albums_per_page'];
          $params['items_col_num'] = $params['masonry_album_column_number'];
          $params['image_column_number'] = $params['masonry_album_image_column_number'];
        } else {
          $params['image_enable_page'] = $params['compuct_album_enable_page'];
          $params['images_per_page'] = $params['compuct_albums_per_page'];
          $params['items_col_num'] = $params['compuct_album_column_number'];
        }

        $params['album_gallery_div_class'] = 'bwg_album_thumbnails_' . $bwg;
        $params['load_more_image_count'] = $params['images_per_page'];
        $params['items_per_page'] = array('images_per_page' => $params['images_per_page'], 'load_more_image_count' => $params['load_more_image_count']);
        $album_gallery_rows = $this->model->get_alb_gals_row($bwg, $params['album_gallery_id'], $params['images_per_page'], $params['album_sort_by'], $params['album_order_by'], $params['image_enable_page'], $from);
        $params['album_gallery_rows'] = $album_gallery_rows;
      }
      else { // Gallery views (thumbnail/masonry/mosaic).
        /* Set parameters for gallery view from album shortcode.*/
        /* album used all parmas for view */
        if (isset($params['compuct_album_image_thumb_width'])) { // Compact album view.
          $params['thumb_width'] = $params['compuct_album_image_thumb_width'];
          $params['thumb_height'] = $params['compuct_album_image_thumb_height'];
          $params['image_title'] = $params['compuct_album_image_title'];

          $params['image_column_number'] = $params['compuct_album_image_column_number'];
          $params['images_per_page'] = $params['compuct_album_images_per_page'];

          $params['mosaic_hor_ver'] = $params['compuct_album_mosaic_hor_ver'];
          $params['resizable_mosaic'] = $params['compuct_album_resizable_mosaic'];
          $params['mosaic_total_width'] = $params['compuct_album_mosaic_total_width'];

          $params['items_col_num'] = $params['compuct_album_column_number'];
        } elseif (isset($params['extended_album_image_thumb_width'])) { // Extended album view.
          $params['thumb_width'] = $params['extended_album_image_thumb_width'];
          $params['thumb_height'] = $params['extended_album_image_thumb_height'];
          $params['image_title'] = $params['extended_album_image_title'];

          $params['image_column_number'] = $params['extended_album_image_column_number'];
          $params['images_per_page'] = $params['extended_album_images_per_page'];

          $params['mosaic_hor_ver'] = $params['extended_album_mosaic_hor_ver'];
          $params['resizable_mosaic'] = $params['extended_album_resizable_mosaic'];
          $params['mosaic_total_width'] = $params['extended_album_mosaic_total_width'];
        } elseif (isset($params['masonry_album_thumb_width'])) {
          $params['thumb_width'] = $params['masonry_album_image_thumb_width'];
          $params['image_column_number'] = $params['masonry_album_image_column_number'];
          $params['images_per_page'] = $params['masonry_album_images_per_page'];
          $params['play_icon'] = BWG()->options->masonry_play_icon;
        }

        $params['gallery_type'] = 'thumbnails';
        if ($params['gallery_view_type'] == 'slideshow') {
          $params['gallery_type'] = 'slideshow';
          $params['slideshow_effect'] = BWG()->options->slideshow_type;
          $params['slideshow_interval'] = BWG()->options->slideshow_interval;
          $params['slideshow_width'] = BWG()->options->slideshow_width;
          $params['slideshow_height'] = BWG()->options->slideshow_height;
          $params['slideshow_sort_by'] = BWG()->options->slideshow_sort_by;
          $params['slideshow_order_by'] = BWG()->options->slideshow_order_by;
          $params['enable_slideshow_autoplay'] = BWG()->options->slideshow_enable_autoplay;
          $params['enable_slideshow_shuffle'] = BWG()->options->slideshow_enable_shuffle;
          $params['enable_slideshow_ctrl'] = BWG()->options->slideshow_enable_ctrl;
          $params['autohide_slideshow_navigation'] = BWG()->options->autohide_slideshow_navigation;
          $params['enable_slideshow_filmstrip'] = BWG()->options->slideshow_enable_filmstrip;
          $params['slideshow_filmstrip_height'] = BWG()->options->slideshow_filmstrip_height;
          $params['slideshow_enable_title'] = BWG()->options->slideshow_enable_title;
          $params['slideshow_title_position'] = BWG()->options->slideshow_title_position;
          $params['slideshow_title_full_width'] = BWG()->options->slideshow_title_full_width;
          $params['slideshow_enable_description'] = BWG()->options->slideshow_enable_description;
          $params['slideshow_description_position'] = BWG()->options->slideshow_description_position;
          $params['enable_slideshow_music'] = BWG()->options->slideshow_enable_music;
          $params['slideshow_music_url'] = BWG()->options->slideshow_audio_url;
          $params['slideshow_effect_duration'] = BWG()->options->slideshow_effect_duration;
          $params['slideshow_gallery_download'] = BWG()->options->slideshow_gallery_download;
          $params['image_column_number'] = 0;
          $params['images_per_page'] = 0;
        }
        if ($params['gallery_view_type'] == 'image_browser') {
          $params['gallery_type'] = 'image_browser';
          $params['image_enable_page'] = BWG()->options->image_enable_page;
          $params['image_browser_width'] = BWG()->options->image_browser_width;
          $params['image_browser_title_enable'] = BWG()->options->image_browser_title_enable;
          $params['image_browser_description_enable'] = BWG()->options->image_browser_description_enable;
          $params['image_browser_sort_by'] = BWG()->options->image_browser_sort_by;
          $params['image_browser_order_by'] = BWG()->options->image_browser_order_by;
          $params['image_browser_show_gallery_title'] = BWG()->options->image_browser_show_gallery_title;
          $params['image_browser_show_gallery_description'] = BWG()->options->image_browser_show_gallery_description;
          $params['image_browser_show_search_box'] = BWG()->options->image_browser_show_search_box;
          $params['image_browser_show_sort_images'] = BWG()->options->image_browser_show_sort_images;
          $params['image_browser_show_tag_box'] = BWG()->options->image_browser_show_tag_box;
          $params['image_browser_placeholder'] = BWG()->options->image_browser_placeholder;
          $params['image_browser_search_box_width'] = BWG()->options->image_browser_search_box_width;
          $params['image_browser_gallery_download'] = BWG()->options->image_browser_gallery_download;
          $params['compuct_album_image_column_number'] = 1;
          $params['compuct_album_images_per_page'] = 1;
          $params['extended_album_image_column_number'] = 1;
          $params['extended_album_images_per_page'] = 1;
          $params['load_more_image_count'] = 1;
          $params['images_per_page'] = 1;
        }
        if ($params['gallery_view_type'] == 'blog_style') {
          $params['gallery_type'] = 'blog_style';
          $params['blog_style_width'] = BWG()->options->blog_style_width;
          $params['blog_style_title_enable'] = BWG()->options->blog_style_title_enable;
          $params['blog_style_images_per_page'] = BWG()->options->blog_style_images_per_page;
          $params['blog_style_load_more_image_count'] = BWG()->options->blog_style_load_more_image_count;
          $params['blog_style_enable_page'] = BWG()->options->blog_style_enable_page;
          $params['blog_style_description_enable'] = BWG()->options->blog_style_description_enable;
          $params['blog_style_sort_by'] = BWG()->options->blog_style_sort_by;
          $params['blog_style_order_by'] = BWG()->options->blog_style_order_by;
          $params['blog_style_show_gallery_title'] = BWG()->options->blog_style_show_gallery_title;
          $params['blog_style_show_gallery_description'] = BWG()->options->blog_style_show_gallery_description;
          $params['blog_style_show_search_box'] = BWG()->options->blog_style_show_search_box;
          $params['blog_style_placeholder'] = BWG()->options->blog_style_placeholder;
          $params['blog_style_search_box_width'] = BWG()->options->blog_style_search_box_width;
          $params['blog_style_show_sort_images'] = BWG()->options->blog_style_show_sort_images;
          $params['blog_style_show_tag_box'] = BWG()->options->blog_style_show_tag_box;
          $params['blog_style_gallery_download'] = BWG()->options->blog_style_gallery_download;
        }
        if ($params['gallery_view_type'] == 'carousel') {
          $params['gallery_type'] = 'carousel';
          $params['carousel_interval'] = BWG()->options->carousel_interval;
          $params['carousel_width'] = BWG()->options->carousel_width;
          $params['carousel_height'] = BWG()->options->carousel_height;
          $params['carousel_image_column_number'] = BWG()->options->carousel_image_column_number;
          $params['carousel_image_par'] = BWG()->options->carousel_image_par;
          $params['carousel_show_gallery_title'] = BWG()->options->carousel_show_gallery_title;
          $params['carousel_show_gallery_description'] = BWG()->options->carousel_show_gallery_description;
          $params['enable_carousel_title'] = BWG()->options->carousel_enable_title;
          $params['enable_carousel_autoplay'] = BWG()->options->carousel_enable_autoplay;
          $params['carousel_r_width'] = BWG()->options->carousel_r_width;
          $params['carousel_fit_containerWidth'] = BWG()->options->carousel_fit_containerWidth;
          $params['carousel_prev_next_butt'] = BWG()->options->carousel_prev_next_butt;
          $params['carousel_play_pause_butt'] = BWG()->options->carousel_play_pause_butt;
          $params['image_column_number'] = 0;
          $params['images_per_page'] = 0;
        }
        if ($params['gallery_view_type'] == 'masonry') {
          $params['gallery_type'] = 'thumbnails_masonry';
        }
        if ($params['gallery_view_type'] == 'mosaic') {
          $params['gallery_type'] = 'thumbnails_mosaic';
        }

        $params['gallery_id'] = $params['album_gallery_id'];
        $params['load_more_image_count'] = $params['images_per_page'];
        $params['items_per_page'] = array('images_per_page' => $params['images_per_page'], 'load_more_image_count' => $params['load_more_image_count']);

        $params['container_id'] = 'bwg_' . $params['gallery_type'] . '_' . $bwg;
        $params['masonry_hor_ver'] = BWG()->options->masonry;
        $params['show_masonry_thumb_description'] = BWG()->options->show_masonry_thumb_description;

        $gallery_row = $this->model->get_gallery_row_data($params['gallery_id']);

        if (empty($gallery_row) && $params['type'] == '' && $params["tag"] == 0) {
          echo WDWLibrary::message(__('There is no gallery selected or the gallery was deleted.', BWG()->prefix), 'wd_error');
          return;
        } else {
          $params['gallery_row'] = $gallery_row;
        }

        if ('xml_sitemap' == $from_shortcode) {
          $params['images_per_page'] = 0;
        }
        $params['image_rows'] = WDWLibrary::get_image_rows_data($params['gallery_id'], $bwg, $params['type'], 'bwg_tag_id_bwg_' . $params['gallery_type'] . '_' . $bwg, $params['tag'], $params['images_per_page'], $params['load_more_image_count'], $params['sort_by'], $params['order_by']);
        if ('xml_sitemap' == $from_shortcode) {
          return $params['image_rows']['images'];
        }
        // Disable Jetpack Photon module for gallery images.
        $this->thumb_urls = $params['image_rows']['thumb_urls'];
        if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
          add_filter('jetpack_photon_skip_image', array($this, 'disable_jetpack'), 11, 3);
        }

        $params['tags_rows'] = $this->model->get_tags_rows_data($params['gallery_id']);
      }
    }
    else { // View type gallery.
      $params['view_type'] = 'gallery';
      $params['album_view_type'] = '';
      $params['album_gallery_id'] = 0;
      $params['container_id'] = 'bwg_' . $params['gallery_type'] . '_' . $bwg;
      $params['cur_alb_gal_id'] = 0;
      $gallery_row = $this->model->get_gallery_row_data($params['gallery_id']);

      if (!empty($gallery_row) && isset($gallery_row->published) && $gallery_row->published == 0) {
        return;
      }
      if (empty($gallery_row) && $params['type'] == '' && $params["tag"] == 0) {
        echo WDWLibrary::message(__('There is no gallery selected or the gallery was deleted.', BWG()->prefix), 'wd_error');
        return;
      } else {
        $params['gallery_row'] = $gallery_row;
      }

      $params['load_more_image_count'] = (isset($params['load_more_image_count']) && ($params['image_enable_page'] == 2)) ? $params['load_more_image_count'] : $params['images_per_page'];
      $params['items_per_page'] = array('images_per_page' => $params['images_per_page'], 'load_more_image_count' => $params['load_more_image_count']);

      if ($params['gallery_type'] == 'image_browser') {
        $params['image_enable_page'] = 1;
        $params['images_per_page'] = 1;
        $params['load_more_image_count'] = 1;
      }
      if ($params['gallery_type'] == 'blog_style') {
        $params['image_enable_page'] = $params['blog_style_enable_page'];
        $params['images_per_page'] = $params['blog_style_images_per_page'];
        $params['load_more_image_count'] = (isset($params['blog_style_load_more_image_count']) && ($params['image_enable_page'] == 2)) ? $params['blog_style_load_more_image_count'] : $params['images_per_page'];
        $params['items_per_page'] = array('images_per_page' => $params['images_per_page'], 'load_more_image_count' => $params['load_more_image_count']);
      }
      $params['masonry_hor_ver'] = (isset($params['masonry_hor_ver']) && $params['masonry_hor_ver'] == 'horizontal') ? 'horizontal' : 'vertical';

      if ('xml_sitemap' == $from_shortcode) {
        $params['images_per_page'] = 0;
      }
      $params['image_rows'] = WDWLibrary::get_image_rows_data($params['gallery_id'], $bwg, $params['type'], 'bwg_tag_id_bwg_' . $params['gallery_type'] . '_' . $bwg, $params['tag'], $params['images_per_page'], $params['load_more_image_count'], $params['sort_by'], $params['order_by']);
      if ('xml_sitemap' == $from_shortcode) {
        return $params['image_rows']['images'];
      }
      // Disable Jetpack Photon module for gallery images.
      $this->thumb_urls = $params['image_rows']['thumb_urls'];
      if (class_exists('Jetpack') && Jetpack::is_module_active('photon')) {
        add_filter('jetpack_photon_skip_image', array($this, 'disable_jetpack'), 11, 3);
      }

      $params['tags_rows'] = $this->model->get_tags_rows_data($params['gallery_id']);
    }

    if ( !isset( $params['current_url'] ) ) {
      $params['current_url'] = trim((is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }

    $params_array = array(
      'action' => 'GalleryBox',
      'current_view' => $bwg,
      'gallery_id' => $params['gallery_id'],
      'tag' => (isset($params['tag']) ? $params['tag'] : 0),
      'theme_id' => $params['theme_id'],
      'shortcode_id' => isset($params['id']) ? $params['id'] : 0,
      'sort_by'  => ($params['sort_by'] == 'random') ? 'casual' : $params['sort_by'], // For widgets.
      'order_by' => $params['order_by'], // For widgets.
      'current_url' => urlencode( $params['current_url'] ),
    );

    $params['params_array'] = $params_array;
    $params['theme_row'] = $theme_row;

    $this->display($params, $from_shortcode, $bwg);
  }

  public function display($params = array(), $from_shortcode = 0, $bwg = 0) {
    $params['ajax'] = isset($params['ajax']) ? TRUE : FALSE;
    $this->view->display($params, $bwg, $params['ajax']);
    if ($from_shortcode) {
      return;
    }
    else {
      die();
    }
  }

  /**
   * Disable Jetpack Photon module for gallery images.
   *
   * @param $val
   * @param $src
   * @param $tag
   *
   * @return bool
   */
  public function disable_jetpack( $val, $src, $tag ) {
    if ( in_array($src, $this->thumb_urls) ) {
      return TRUE;
    }

    return $val;
  }
}
