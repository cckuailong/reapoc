<?php

class BWGElementor extends \Elementor\Widget_Base {

  public $shortcode_id=[];
  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'bwg-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title() {
    ?>
    <style>
      .elementor-control-bwg_view_type_shortcode .elementor-control-input-wrapper,
      .elementor-control-bwg_elementor_shortcode input {
        visibility: hidden;
      }
      .elementor-control-bwg_elementor_shortcode input {
        height: 0;
      }
      .elementor-control a.bwg-shortcode-btn {
        text-decoration: none;
        border-bottom:none;
      }
    </style>
    <?php
    return __('Gallery', BWG()->prefix);
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'fa twbb-photo-gallery twbb-widget-icon';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return [ 'tenweb-plugins-widgets' ];
  }

  /**
   * Register widget controls.
   */
  protected function _register_controls() {

    if($this->get_id() !== null){
      $settings = $this->get_settings();
    }
    $this->start_controls_section(
      'bwg_general',
      [
        'label' => __('General', BWG()->prefix),
      ]
    );
    $url = add_query_arg(array('action' => 'shortcode_bwg','elementor_callback' => 1, 'TB_iframe' => '1'), admin_url('admin-ajax.php'));

    $this->shortcode_id[$this->get_id()] = !empty($settings["bwg_view_type_shortcode"]) ? $settings["bwg_view_type_shortcode"] : '';

    ?>
    <style>
      .elementor-control-bwg_view_type_shortcode .elementor-control-input-wrapper,
      .elementor-control-bwg_elementor_shortcode input {
        visibility: hidden;
      }
      .elementor-control-bwg_elementor_shortcode input {
        height: 0;
      }
      .elementor-control a.bwg-shortcode-btn {
        text-decoration: none;
        border-bottom:none;
      }
    </style>
    <?php
    $this->add_control(
      'bwg_view_type_shortcode',
      [
        'label' => '<a onclick="if ( typeof tb_click == \'function\' && ( jQuery(this).parent().attr(\'id\') && jQuery(this).parent().attr(\'id\').indexOf(\'elementor\') !== -1 || typeof bwg_check_ready == \'function\') ) {
            tb_click.call(this);
            bwg_create_loading_block();
            bwg_set_shortcode_popup_dimensions(); } return false;" href="'.$url.'" class="bwg-shortcode-btn button">
              <img src="'.BWG()->plugin_url .'/images/tw-gb/shortcode_new_small.jpg" alt="Photo Gallery">
            </a>',
        'type' => \Elementor\Controls_Manager::CHOOSE,
        'options' => [
          'gallery',
          'gallery1'
        ],
        'description'=>'Click on icon to add/edit gallery.'
      ]
    );
    $this->add_control(
      'bwg_elementor_shortcode',
      [
        'type' => \Elementor\Controls_Manager::HIDDEN,
        'placeholder' => __( '', 'elementor' ),
        'default' => __( '', 'elementor' ),
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Render widget output on the frontend.
   */
  protected function render() {
    $settings = $this->get_settings_for_display();
    $params = array();
    if ( !isset($settings['bwg_view_type_shortcode']) ) {
      $params['gallery_type'] = isset($settings['bwg_gallery_view_type']) ? $settings['bwg_gallery_view_type'] : (isset($settings['bwg_gallery_group_view_type']) ? $settings['bwg_gallery_group_view_type'] : '');
      $params['gallery_id'] = isset($settings['bwg_galleries']) ? $settings['bwg_galleries'] : '';
      $params['tag'] = isset($settings['bwg_tags']) ? $settings['bwg_tags'] : '';
      $params['album_id'] = isset($settings['bwg_gallery_group']) ? $settings['bwg_gallery_group'] : '';
      $params['theme_id'] = isset($settings['bwg_theme']) ? $settings['bwg_theme'] : '';
    }
    else {
      $params['id'] = isset($settings['bwg_view_type_shortcode']) ? $settings['bwg_view_type_shortcode'] : 0;
    }

    if ( doing_filter('wd_seo_sitemap_images') || doing_filter('wpseo_sitemap_urlimages') ) {
      WDWSitemap::instance()->shortcode();
    }
    else {
        echo BWG()->shortcode($params);
    }
  }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new BWGElementor() );
