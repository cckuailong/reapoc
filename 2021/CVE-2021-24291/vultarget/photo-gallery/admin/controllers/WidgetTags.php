<?php

/**
 * Class WidgetTagsController_bwg
 */
class WidgetTagsController_bwg extends WP_Widget {

	private $view;
	private $model;
 
	public function __construct() {
		$widget_ops = array(
		  'classname' => 'bwp_gallery_tags',
		  'description' => __('Add Photo Gallery Tags dynamic cloud to Your widget area.', BWG()->prefix)
		);
		// Widget Control Settings.
		$control_ops = array('id_base' => 'bwp_gallery_tags');
		// Create the widget.
		parent::__construct('bwp_gallery_tags', 'Photo Gallery Tags Cloud', $widget_ops, $control_ops);
		require_once( BWG()->plugin_dir . '/admin/models/Widget.php');
		$this->model = new WidgetModel_bwg();

		require_once( BWG()->plugin_dir . '/admin/views/WidgetTags.php');
		$this->view = new WidgetTagsView_bwg();
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
		// Set params for view.
		$params = array(
      'id_title' => parent::get_field_id('title'),
      'name_title' => parent::get_field_name('title'),
      'id_type' => parent::get_field_id('type'),
      'name_type' => parent::get_field_name('type'),
      'id_show_name' => parent::get_field_id('show_name'),
      'name_show_name' => parent::get_field_name('show_name'),
      'id_open_option' =>	parent::get_field_id('open_option'),
      'name_open_option' => parent::get_field_name('open_option'),
      'id_count' => parent::get_field_id('count'),
      'name_count' => parent::get_field_name('count'),
      'id_width' => parent::get_field_id('width'),
      'name_width' => parent::get_field_name('width'),
      'id_height' => parent::get_field_id('height'),
      'name_height' => parent::get_field_name('height'),
      'id_background_transparent' => parent::get_field_id('background_transparent'),
      'name_background_transparent' => parent::get_field_name('background_transparent'),
      'id_background_color' => parent::get_field_id('background_color'),
      'name_background_color' => parent::get_field_name('background_color'),
      'id_text_color' => parent::get_field_id('text_color'),
      'name_text_color' => parent::get_field_name('text_color'),
      'id_theme_id' => parent::get_field_id('theme_id'),
      'name_theme_id' => parent::get_field_name('theme_id'),
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
		$instance['type'] = isset($new_instance['type']) ? $new_instance['type'] : 'text';
		$instance['show_name'] = isset($new_instance['show_name']) ? $new_instance['show_name'] : 0;
		$instance['open_option'] = isset($new_instance['open_option']) ? $new_instance['open_option'] : 'gallery';
		$instance['count'] = isset($new_instance['count']) ? $new_instance['count'] : 0;
		$instance['width'] = isset($new_instance['width']) ? $new_instance['width'] : 250;
		$instance['height'] = isset($new_instance['height']) ? $new_instance['height'] : 250;
		$instance['background_transparent'] = isset($new_instance['background_transparent']) ? $new_instance['background_transparent'] : 1;
		$instance['background_color'] = isset($new_instance['background_color']) ? $new_instance['background_color'] : '000000';
		$instance['text_color'] = isset($new_instance['text_color']) ? $new_instance['text_color'] : 'eeeeee';
		$instance['theme_id'] = isset($new_instance['theme_id']) ? $new_instance['theme_id'] : 1;
		return $instance;
	}
}

/**
 * Class BWGControllerWidgetTags
 *
 * Allow to work old widgets registered with this name of class added with SiteOrigin builder.
 */
class BWGControllerWidgetTags extends WidgetTagsController_bwg {}
