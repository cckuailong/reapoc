<?php


final class WDWSitemap {
  /**
   * The single instance of the class.
   */
  protected static $_instance = null;

  private $images;

  /**
   * Main WDWSitemap Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return BWG - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function add_wpseo_xml_sitemap_images( $images, $post_id ) {
    $this->images = $images;

    $post = get_post($post_id);

    remove_all_shortcodes();
    if ( defined('ELEMENTOR_VERSION') && did_action( 'elementor/loaded' ) ) {
      \Elementor\Plugin::instance()->frontend->get_builder_content($post->ID);
    }
    add_shortcode('Best_Wordpress_Gallery', array($this, 'shortcode'));
    do_shortcode($post->post_content);

    return $this->images;
  }

  public function shortcode( $params = array() ) {
    if ( isset($params['id']) && $params['id'] ) {
      global $wpdb;
      $shortcode = $wpdb->get_var($wpdb->prepare("SELECT tagtext FROM " . $wpdb->prefix . "bwg_shortcode WHERE id='%d'", $params['id']));
      if ($shortcode) {
        $shortcode_params = explode('" ', $shortcode);
        foreach ($shortcode_params as $shortcode_param) {
          $shortcode_param = str_replace('"', '', $shortcode_param);
          $shortcode_elem = explode('=', $shortcode_param);
          $params[str_replace(' ', '', $shortcode_elem[0])] = $shortcode_elem[1];
        }
      }
      else {
        return;
      }
    }

    // 'gallery_type' is the only parameter not being checked.
    // Checking for incomplete shortcodes.
    $gallery_allowed_types = array(
      'thumbnails',
      'thumbnails_masonry',
      'thumbnails_mosaic',
      'slideshow',
      'image_browser',
      'blog_style',
      'carousel',
      'album_compact_preview',
      'album_masonry_preview',
      'album_extended_preview',
    );
    if ( isset($params['gallery_type']) && in_array($params['gallery_type'], $gallery_allowed_types) ) {
      $pairs = WDWLibrary::get_shortcode_option_params( $params );
      if ( isset($params['ajax']) ) {
        $pairs['ajax'] = $params['ajax'];
      }
      $images = $this->get_shortcode_images( $pairs );
      if ( is_array( $images ) ) {
        foreach ( $images as $image ) {
          if ( strpos($image->filetype, 'EMBED') === FALSE ) {
            $this->images[] = array(
              'src' => BWG()->upload_url . $image->image_url_raw,
              'title' => $image->alt,
              'alt' => $image->alt
            );
          }
        }
      }
    }
  }

  private function get_shortcode_images( $params ) {
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    require_once(BWG()->plugin_dir . '/frontend/controllers/controller.php');
    $controller = new BWGControllerSite( ucfirst( $params[ 'gallery_type' ] ) );
    return $controller->execute($params, 'xml_sitemap');
  }
}