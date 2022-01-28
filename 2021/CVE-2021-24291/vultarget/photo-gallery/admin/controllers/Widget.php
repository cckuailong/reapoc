<?php

/**
 * Class WidgetController_bwg
 */
class WidgetController_bwg extends WP_Widget {

	private $view;
	private $model;

	public function __construct() {
		$widget_ops = array(
			'classname' => 'bwp_gallery',
			'description' => __('Add Photo Gallery albums or galleries to Your widget area.', BWG()->prefix)
		);
		// Widget Control Settings.
		$control_ops = array('id_base' => 'bwp_gallery');
		// Create the widget.
		parent::__construct('bwp_gallery', 'Photo Gallery Widget', $widget_ops, $control_ops);
		require_once( BWG()->plugin_dir . '/admin/models/Widget.php');
		$this->model = new WidgetModel_bwg();

		require_once( BWG()->plugin_dir . '/admin/views/Widget.php');
		$this->view = new WidgetView_bwg();
	}

  /**
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
		// Set params for view.
		$params = array(
			'id_title' => parent::get_field_id('title'),
			'name_title' => parent::get_field_name('title'),
			'id_type' => parent::get_field_id('type'),
			'name_type' => parent::get_field_name('type'), 
			'id_show' => parent::get_field_id('show'), 
			'name_show' => parent::get_field_name('show'), 
			'id_gallery_id' => parent::get_field_id('gallery_id'), 
			'name_gallery_id' => parent::get_field_name('gallery_id'), 
			'id_album_id' => parent::get_field_id('album_id'), 
			'name_album_id' => parent::get_field_name('album_id'), 
			'id_count' => parent::get_field_id('count'),
			'name_count' => parent::get_field_name('count'), 
			'id_width' => parent::get_field_id('width'), 
			'name_width' => parent::get_field_name('width'), 
			'id_height' => parent::get_field_id('height'), 
			'name_height' => parent::get_field_name('height'), 
			'id_theme_id' => parent::get_field_id('theme_id'), 
			'name_theme_id' => parent::get_field_name('theme_id'), 
			'id_view_type' => parent::get_field_id('view_type'), 
			'name_view_type' => parent::get_field_name('view_type'),
			'gallery_rows' => $this->model->get_gallery_rows_data(),
			'album_rows' => $this->model->get_album_rows_data(),
			'theme_rows' => $this->model->get_theme_rows_data()
		);
		$this->view->form($params, $instance);    
	}

  /**
   * Update.
   *
   * @param array $new_instance
   * @param array $old_instance
   * @return mixed
   */
	public function update($new_instance, $old_instance) {
		$instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		$instance['type'] = isset($new_instance['type']) ? $new_instance['type'] : 'gallery';
		$instance['gallery_id'] = isset($new_instance['gallery_id']) ? $new_instance['gallery_id'] : 0;
		$instance['album_id'] = isset($new_instance['album_id']) ? $new_instance['album_id'] : 0;
		$instance['show'] = isset($new_instance['show']) ? $new_instance['show'] : 'random';
		$instance['count'] = isset($new_instance['count']) ? $new_instance['count'] : 4;
		$instance['width'] = isset($new_instance['width']) ? $new_instance['width'] : 100;
		$instance['height'] = isset($new_instance['height']) ? $new_instance['height'] : 100;
		$instance['theme_id'] = isset($new_instance['theme_id']) ? $new_instance['theme_id'] : 1;
		$instance['view_type'] = isset($new_instance['view_type']) ? $new_instance['view_type'] : 'thumbnails';
		return $instance;
	}
}

/**
 * Class BWGControllerWidget
 *
 * Allow to work old widgets registered with this name of class added with SiteOrigin builder.
 */
class BWGControllerWidget extends WidgetController_bwg {}
