<?php

class MECDIVI_MECShortcodes extends ET_Builder_Module {

	public $slug       = 'mecdivi_MECShortcodes';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://webnus.net',
		'author'     => 'Webnus',
		'author_uri' => 'https://webnus.net',
	);

	public function init() {
		$this->name = esc_html__( 'MEC Shortcodes', 'mecdivi-divi' );
	}

	public function get_fields() {
		$calendar_posts = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1'));
        $calendars = array();
		foreach($calendar_posts as $calendar_post) $calendars[$calendar_post->ID] = $calendar_post->post_title;
		
		return array(
			'shortcode_id'     => array(
				'label'           => esc_html__( 'MEC Shortcodes', 'mecdivi-divi' ),
				'type'            => 'select',
				'options' => $calendars,
				'description'     => esc_html__( 'Enter the shortcode_id of your choosing here.', 'mecdivi-divi' ),
			),
		);
	}
	public function render(  $attrs, $content = NULL, $render_slug = NULL ) {
		return do_shortcode('[MEC id="'.$this->props['shortcode_id'].'"]');
	}
}

new MECDIVI_MECShortcodes;
