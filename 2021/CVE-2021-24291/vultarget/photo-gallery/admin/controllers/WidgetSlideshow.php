<?php

/**
 * Class WidgetSlideshowController_bwg
 */
class WidgetSlideshowController_bwg extends WP_Widget {

	private $view;
	private $model;

	public function __construct() {
		$widget_ops = array(
		  'classname' => 'bwp_gallery_slideshow',
		  'description' => __('Add Photo Gallery slideshow to Your widget area.', BWG()->prefix)
		);
		// Widget Control Settings.
		$control_ops = array('id_base' => 'bwp_gallery_slideshow');
		// Create the widget.
		parent::__construct('bwp_gallery_slideshow', 'Photo Gallery Slideshow', $widget_ops, $control_ops);
		require_once( BWG()->plugin_dir . '/admin/models/Widget.php');
		$this->model = new WidgetModel_bwg();

		require_once( BWG()->plugin_dir . '/admin/views/WidgetSlideshow.php');
		$this->view = new WidgetSlideshowView_bwg($this->model);
	}

  /**
   * Widget.
   *
   * @param array $args
   * @param array $instance
   */
	public function widget($args, $instance) {
		$this->view->widget($args, $instance);
	}

  /**
   * Form.
   *
   * @param array $instance
   */
 	public function form( $instance ) {
		$slideshow_effects = array(
		  'none' => __('None', BWG()->prefix),
		  'cubeH' => __('Cube Horizontal', BWG()->prefix),
		  'cubeV' => __('Cube Vertical', BWG()->prefix),
		  'fade' => __('Fade', BWG()->prefix),
		  'sliceH' => __('Slice Horizontal', BWG()->prefix),
		  'sliceV' => __('Slice Vertical', BWG()->prefix),
		  'slideH' => __('Slide Horizontal', BWG()->prefix),
		  'slideV' => __('Slide Vertical', BWG()->prefix),
		  'scaleOut' => __('Scale Out', BWG()->prefix),
		  'scaleIn' => __('Scale In', BWG()->prefix),
		  'blockScale' => __('Block Scale', BWG()->prefix),
		  'kaleidoscope' => __('Kaleidoscope', BWG()->prefix),
		  'fan' => __('Fan', BWG()->prefix),
		  'blindH' => __('Blind Horizontal', BWG()->prefix),
		  'blindV' => __('Blind Vertical', BWG()->prefix),
		  'random' => __('Random', BWG()->prefix),
		);

		// Set params for view.
		$params = array(
      'id_title' => parent::get_field_id('title'),
      'name_title' => parent::get_field_name('title'),
      'id_gallery_id' => parent::get_field_id('gallery_id'),
      'name_gallery_id' => parent::get_field_name('gallery_id'),
      'id_width' => parent::get_field_id('width'),
      'name_width' => parent::get_field_name('width'),
      'id_height' => parent::get_field_id('height'),
      'name_height' => parent::get_field_name('height'),
      'id_filmstrip_height' => parent::get_field_id('filmstrip_height'),
      'name_filmstrip_height' => parent::get_field_name('filmstrip_height'),
      'id_effect' => parent::get_field_id('effect'),
      'name_effect' => parent::get_field_name('effect'),
      'id_interval' => parent::get_field_id('interval'),
      'name_interval' => parent::get_field_name('interval'),
      'id_shuffle' => parent::get_field_id('shuffle'),
      'name_shuffle' => parent::get_field_name('shuffle'),
      'id_theme_id' => parent::get_field_id('theme_id'),
      'name_theme_id' => parent::get_field_name('theme_id'),
      'id_enable_ctrl_btn' => parent::get_field_id('enable_ctrl_btn'),
      'name_enable_ctrl_btn' => parent::get_field_name('enable_ctrl_btn'),
      'id_enable_autoplay' => parent::get_field_id('enable_autoplay'),
      'name_enable_autoplay' => parent::get_field_name('enable_autoplay'),
      'gallery_rows' => $this->model->get_gallery_rows_data(),
      'theme_rows' => $this->model->get_theme_rows_data(),
      'slideshow_effects' => $slideshow_effects
    );
		$this->view->form($params, $instance);
	}

	// Update Settings.
	public function update($new_instance, $old_instance) {
		$instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		$instance['gallery_id'] = isset($new_instance['gallery_id']) ? $new_instance['gallery_id'] : 0;
		$instance['width'] = isset($new_instance['width']) ? $new_instance['width'] : 200;
		$instance['height'] = isset($new_instance['height']) ? $new_instance['height'] : 200;
		$instance['filmstrip_height'] = isset($new_instance['filmstrip_height']) ? $new_instance['filmstrip_height'] : 40;
		$instance['effect'] = isset($new_instance['effect']) ? $new_instance['effect'] : 'fade';
		$instance['interval'] = isset($new_instance['interval']) ? $new_instance['interval'] : 5;
		$instance['shuffle'] = isset($new_instance['shuffle']) ? $new_instance['shuffle'] : 0;
		$instance['theme_id'] = isset($new_instance['theme_id']) ? $new_instance['theme_id'] : 1;
		$instance['enable_ctrl_btn'] = isset($new_instance['enable_ctrl_btn']) ? $new_instance['enable_ctrl_btn'] : 0;
		$instance['enable_autoplay'] = isset($new_instance['enable_autoplay']) ? $new_instance['enable_autoplay'] : 0;
		return $instance;
	}
}

/**
 * Class BWGControllerWidgetSlideshow
 *
 * Allow to work old widgets registered with this name of class added with SiteOrigin builder.
 */
class BWGControllerWidgetSlideshow extends WidgetSlideshowController_bwg {}
