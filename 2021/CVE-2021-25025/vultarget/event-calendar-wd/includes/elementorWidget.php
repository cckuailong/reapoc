<?php

class ECWDElementor extends \Elementor\Widget_Base {
  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'ecwd-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title() {
    return __('Event Calendar', 'ecwd');
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'twbb-calendar twbb-widget-icon';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return ['tenweb-plugins-widgets'];
  }

  /**
   * Register widget controls.
   */
  protected function _register_controls() {
	/* start general section */
    $this->start_controls_section('section_general',
		[
			'label' => __('General', 'ecwd'),
		]
    );

    $calendars = $this->get_calendars();
    if($this->get_id() !== null){
      $settings = $this->get_init_settings();
    }
    $ecwd_edit_link = "edit.php?post_type=ecwd_calendar";
    if(isset($settings) && isset($settings["control_calendar"]) && intval($settings["control_calendar"])>0){
      $ecwd_edit_link  = "post.php?post=".intval($settings["control_calendar"])."&action=edit";
    }
		$this->add_control('control_calendar',
			[
				'label' => __('Select Calendar', 'ecwd'),
				'label_block' => TRUE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 0,
				'options' => $calendars,
        'description' => __('Select the calendar to display.', 'ecwd') .' <a target="_blank" " href="'.$ecwd_edit_link.'">' . __('Edit calendar', 'ecwd') . '</a>',
      ]
		);

		$this->add_control('control_view_type',
			[
				'label' => __( 'Select View type', 'ecwd' ),
				'label_block' => FALSE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'mini' => __( 'Mini', 'ecwd' ),
					'full' => __( 'Full', 'ecwd' )
				],
				'default' => 'full'
			]
		);

		$this->add_control('control_per_page',
			[
				'label' => __( 'Per page in list view', 'ecwd' ),
				'label_block' => FALSE,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '5'
			]
		);

		$this->add_control('control_calendar_start_date',
			[
				'label' => __( 'Calendar start date', 'ecwd' ),
				'label_block' => FALSE,
				'type' => \Elementor\Controls_Manager::TEXT,				
				'description' => __( 'Date format Y-m (2016-05) or empty for current date', 'ecwd' )
			]
		);

		$this->add_control('control_enable_event_search',
			[
				'label' => __( 'Enable event search', 'ecwd' ),
				'label_block' => FALSE,
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_yes' => __( 'Yes', 'ecwd' ),
				'label_no' => __( 'No', 'ecwd' ),
				'default' => 'yes',
				'description' => ( ECWD_PRO == 0 ) ? '<a href="https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin" target="_blank">' . __( 'Upgrade to Premium version.', 'ecwd' ) . '</a>' : ''
			]
		);

    $this->end_controls_section();
	/* end general section */

	/* start views section */
	$this->start_controls_section('section_views',
		[
			'label' => __('Views', 'ecwd'),
		]
    );
		$view_options = [
			'none' => __('None', 'ecwd'),
			'full' => __('Month', 'ecwd'),
			'list' => __('List', 'ecwd'),
			'week' => __('Week', 'ecwd'),
			'day' => __('Day', 'ecwd'),
			'4day' => __('4 Days', 'ecwd'),
			'map' => __('Map', 'ecwd'),
			'posterboard' => __('Posterboard', 'ecwd')
		];
		
		$this->add_control('control_view_1',
			[
				'label' => __('View 1', 'ecwd'),
				'label_block' => FALSE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $view_options,
				'default' => 'full'
			]
		);

		$this->add_control('control_view_2',
			[
				'label' => __('View 2', 'ecwd'),
				'label_block' => FALSE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $view_options,
				'default' => 'list'
			]
		);

		$this->add_control('control_view_3',
			[
				'label' => __('View 3', 'ecwd'),
				'label_block' => FALSE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $view_options,
				'default' => 'week'
			]
		);

		$this->add_control('control_view_4',
			[
				'label' => __('View 4', 'ecwd'),
				'label_block' => FALSE,
				'show_label' => TRUE,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $view_options,
				'default' => 'day',
				'description' => ( ECWD_PRO == 0 ) ? '<a href="https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin" target="_blank">' . __( 'Upgrade to Premium version to access three more view options: posterboard, map and 4 days' ) . '</a>' : ''
			]
		);

		if ( ECWD_PRO ) {
			$this->add_control('control_view_5',
				[
					'label' => __('View 5', 'ecwd'),
					'label_block' => FALSE,
					'show_label' => TRUE,
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => $view_options,
					'default' => 'map'
				]
			);

			$this->add_control('control_view_6',
				[
					'label' => __('View 6', 'ecwd'),
					'label_block' => FALSE,
					'show_label' => TRUE,
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => $view_options,
					'default' => '4day'
				]
			);

			$this->add_control('control_view_7',
				[
					'label' => __('View 7', 'ecwd'),
					'label_block' => FALSE,
					'show_label' => TRUE,
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => $view_options,
					'default' => 'posterboard'
				]
			);
		}
    $this->end_controls_section();
	/* end views section */

	/* start views section */
/*	$this->start_controls_section('section_filters',
		[
			'label' => __('Filters', 'ecwd'),
		]
    );
		// Get and activate filters add-on
    $this->end_controls_section();*/
	/* end views section */
  }



	protected function get_calendars() {
		$args = array(
			'post_type' => ECWD_PLUGIN_PREFIX . '_calendar',
			'post_status' => 'publish',
			'posts_per_page' => - 1,
			'ignore_sticky_posts' => 1
		);
		$results = [];
		$posts = get_posts($args);
		if ( !empty($posts) ) {
			foreach ( $posts as $i => $post ) {
				$data[$post->ID] = $post->post_title;
			}

			$results[0] = __('Select', 'ecwd');
			foreach ( $data as $id => $val ) {
				$results[$id] = $val;
			}
		}
		return $results;
	}
  /**
   * Render widget output on the frontend.
   */
  protected function render() {
	include_once ECWD_DIR . '/includes/ecwd-functions.php';
	include_once ECWD_DIR . '/includes/ecwd-shortcodes.php';
    $settings = $this->get_settings_for_display();
	$displays = '';
	$displays .= ($settings['control_view_type'] == 'mini') ? $settings['control_view_type'] . ',' : $settings['control_view_1'] . ',';
	$displays .= $settings['control_view_2'] . ',';
	$displays .= $settings['control_view_3'] . ',';
	$displays .= $settings['control_view_4'] . ',';
	if ( ECWD_PRO ) {
		$displays .= $settings['control_view_5'] . ',';
		$displays .= $settings['control_view_6'] . ',';
		$displays .= $settings['control_view_7'];
	}
	$params = [
		'id' => $settings['control_calendar'],
		'type' => $settings['control_view_type'],
		'page_items' => $settings['control_per_page'],
		'calendar_start_date' => ! empty($settings['control_calendar_start_date']) ? $settings['control_calendar_start_date'] : '',
		'event_search' => $settings['control_enable_event_search'],
		'display' => $settings['control_view_type'],
		'displays' => $displays,
		'filters' => '' 
	];
	if ( $settings['control_calendar'] ) {
		echo ecwd_shortcode($params);
	}else {
		echo '<div class="fm-message fm-notice-error">' . __('There is no Calendar selected or the Calendar was deleted.', 'ecwd') . '</div>';
	} 
  }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ECWDElementor() );