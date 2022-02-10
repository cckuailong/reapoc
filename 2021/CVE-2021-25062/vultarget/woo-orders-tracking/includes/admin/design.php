<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_ORDERS_TRACKING_ADMIN_DESIGN {
	protected $settings;
	protected $prefix;

	public function __construct() {
		$this->settings = new VI_WOO_ORDERS_TRACKING_DATA();
		$this->prefix   = 'vi-wot-orders-tracking-customize-';
		add_action( 'customize_register', array( $this, 'design_option_customizer' ) );
		add_action( 'wp_head', array( $this, 'customize_controls_print_styles' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'customize_controls_print_scripts' ), 30 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ), 30 );
		add_action( 'wp_ajax_vi_wot_customize_params_date_time_format', array(
			$this,
			'vi_wot_customize_params_date_time_format'
		) );
	}

	public function vi_wot_customize_params_date_time_format() {
		$format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : '';
		if ( $format ) {
			$result = date_format( date_create(), $format );
			wp_send_json(
				array(
					'status' => 'success',
					'html'   => $result
				)
			);
		}
	}

	public function design_option_customizer( $wp_customize ) {
		$this->add_section_design( $wp_customize );
		$this->add_section_design_general( $wp_customize );
		$this->add_section_design_template_one( $wp_customize );
		$this->add_section_design_custom_css( $wp_customize );

	}

	/**
	 * @param $wp_customize WP_Customize_Manager
	 */
	protected function add_section_design( $wp_customize ) {
		$wp_customize->add_panel( 'vi_wot_orders_tracking_design', array(
			'priority'       => 200,
			'capability'     => 'manage_options',
			'theme_supports' => '',
			'title'          => __( 'WooCommerce Orders Tracking ', 'woo-orders-tracking' ),
		) );
		$wp_customize->add_section( 'vi_wot_orders_tracking_design_general', array(
			'priority'       => 20,
			'capability'     => 'manage_options',
			'theme_supports' => '',
			'title'          => __( 'General', 'woo-orders-tracking' ),
			'panel'          => 'vi_wot_orders_tracking_design',
		) );
		$wp_customize->add_section( 'vi_wot_orders_tracking_design_template_one', array(
			'priority'       => 20,
			'capability'     => 'manage_options',
			'theme_supports' => '',
			'title'          => __( 'Design Template One', 'woo-orders-tracking' ),
			'panel'          => 'vi_wot_orders_tracking_design',
		) );
		$wp_customize->add_section( 'vi_wot_orders_tracking_design_custom_css', array(
			'priority'       => 20,
			'capability'     => 'manage_options',
			'theme_supports' => '',
			'title'          => __( 'Custom Css', 'woo-orders-tracking' ),
			'panel'          => 'vi_wot_orders_tracking_design',
		) );
	}

	/**
	 * @param $wp_customize WP_Customize_Manager
	 */
	protected function add_section_design_general( $wp_customize ) {
		/*
		 * sort events
		 */
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_sort_event]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_sort_event' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_sort_event]', array(
			'type'     => 'select',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_general',
			'label'    => __( 'Sort events', 'woo-orders-tracking' ),
			'choices'  => array(
				'most_recent_to_oldest' => __( 'Most recent to oldest', 'woo-orders-tracking' ),
				'oldest_to_most_recent' => __( 'Oldest to most recent', 'woo-orders-tracking' ),
			)
		) );

		/*
		 * Date format
		 */
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_date_format]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_date_format' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_date_format]', array(
			'type'     => 'select',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_general',
			'label'    => __( 'Date format', 'woo-orders-tracking' ),
			'choices'  => array(
				'j F, Y' => __( date( 'j F, Y' ) . ' ( j F, Y )', 'woo-orders-tracking' ),
				'Y-m-d'  => __( date( 'Y-m-d' ) . ' ( Y-m-d )', 'woo-orders-tracking' ),
				'm/d/Y'  => __( date( 'm/d/Y' ) . ' ( m/d/Y )', 'woo-orders-tracking' ),
				'd/m/Y'  => __( date( 'd/m/Y' ) . ' ( d/m/Y)', 'woo-orders-tracking' ),
			)
		) );
		/*
		 * time format
		 */
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_time_format]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_time_format' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_time_format]', array(
			'type'     => 'select',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_general',
			'label'    => __( 'Time format', 'woo-orders-tracking' ),
			'choices'  => array(
				'g:i a' => __( date( 'g:i a' ) . ' ( g:i a)', 'woo-orders-tracking' ),
				'g:i A' => __( date( 'g:i A' ) . ' ( g:i A )', 'woo-orders-tracking' ),
				'H:i'   => __( date( 'H:i' ) . ' ( H:i )', 'woo-orders-tracking' ),
			)
		) );
		/*
		 * template
		 */
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_template' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_template]', array(
			'type'     => 'select',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_general',
			'label'    => __( 'Timeline template', 'woo-orders-tracking' ),
			'choices'  => array(
				'1' => __( 'Template one', 'woo-orders-tracking' ),
				'2' => __( 'Template two', 'woo-orders-tracking' ),
			)
		) );


		//template title
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_title]', array(
			'default'           => htmlentities( $this->settings->get_params( 'timeline_track_info_title' ) ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_title]', array(
			'label'       => __( 'Title', 'woo-orders-tracking' ),
			'type'        => 'text',
			'section'     => 'vi_wot_orders_tracking_design_general',
			'description' => '<p >{tracking_number}:' . __( 'The tracking number', 'woo-orders-tracking' ) . '</p> <p >{carrier_name}:' . __( 'The name of carrier', 'woo-orders-tracking' ) . '</p>'
		) );


		//template title alignment
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_title_alignment]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_title_alignment' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_title_alignment]', array(
			'type'     => 'select',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_general',
			'label'    => __( 'Title text alignment', 'woo-orders-tracking' ),
			'choices'  => array(
				'center' => __( 'Center', 'woo-orders-tracking' ),
				'left'   => __( 'Left', 'woo-orders-tracking' ),
				'right'  => __( 'Right', 'woo-orders-tracking' ),
			)
		) );

		//template title text color
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_title_color]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_title_color' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_title_color]',
				array(
					'label'   => __( 'Title text color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);

		//template title font size

		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_title_font_size]', array(
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => $this->settings->get_params( 'timeline_track_info_title_font_size' ),
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[timeline_track_info_title_font_size]', array(
			'type'        => 'number',
			'section'     => 'vi_wot_orders_tracking_design_general',
			'label'       => __( 'Title font size', 'woo-orders-tracking' ),
			'input_attrs' => array(
				'min'  => 13,
				'step' => 1,
				'max'  => 60
			),
		) );


		//template status text color
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_color]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_color' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_color]',
				array(
					'label'   => __( 'Shipment status text color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);

		//template status background delivered
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_background_delivered]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_background_delivered' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_background_delivered]',
				array(
					'label'   => __( 'Shipment delivered background color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);

		//template status background pickup
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_background_pickup]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_background_pickup' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_background_pickup]',
				array(
					'label'   => __( 'Shipment pickup background color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);
		//template status background pickup
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_background_transit]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_background_transit' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_background_transit]',
				array(
					'label'   => __( 'Shipment transit background color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);

		//template status background pickup
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_background_pending]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_background_pending' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_background_pending]',
				array(
					'label'   => __( 'Shipment pending background color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);
		//template status background pickup
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_status_background_alert]', array(
			'default'           => $this->settings->get_params( 'timeline_track_info_status_background_alert' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_status_background_alert]',
				array(
					'label'   => __( 'Shipment alert background color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_general',
				) )
		);
	}

	/**
	 * @param $wp_customize WP_Customize_Manager
	 */
	protected function add_section_design_template_one( $wp_customize ) {
		//set delivered icon
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_delivered]', array(
			'default'           => $this->settings->get_params( 'icon_delivered', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control(
			new VI_WOT_Customize_Radio_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_delivered]',
				array(
					'label'   => __( 'Delivered icon', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
					'choices' => VI_WOO_ORDERS_TRACKING_DATA::get_delivered_icons()
				)
			)
		);

		//delivered icon color
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_delivered_color]', array(
			'default'           => $this->settings->get_params( 'icon_delivered_color', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_delivered_color]',
				array(
					'label'   => __( 'Delivered icon color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
				) )
		);
		//set pickup icon
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup]', array(
			'default'           => $this->settings->get_params( 'icon_pickup', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new VI_WOT_Customize_Radio_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup]',
				array(
					'label'   => __( 'Pickup icon', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
					'choices' => VI_WOO_ORDERS_TRACKING_DATA::get_pickup_icons()
				)
			)
		);

		//pickup icon color
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup_color]', array(
			'default'           => $this->settings->get_params( 'icon_pickup_color', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup_color]',
				array(
					'label'   => __( 'Pickup icon color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
				) )
		);
		//pickup icon background
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup_background]', array(
			'default'           => $this->settings->get_params( 'icon_pickup_background', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup_background]',
				array(
					'label'   => __( 'Pickup icon background', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
				) )
		);
		//set other status icon
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit]', array(
			'default'           => $this->settings->get_params( 'icon_transit', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new VI_WOT_Customize_Radio_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit]',
				array(
					'label'   => __( 'In-transit status icon', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
					'choices' => VI_WOO_ORDERS_TRACKING_DATA::get_transit_icons()
				)
			)
		);

		//other statsu icon color
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit_color]', array(
			'default'           => $this->settings->get_params( 'icon_transit_color', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit_color]',
				array(
					'label'   => __( 'In-transit status icon color', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
				) )
		);
		//other status icon background
		$wp_customize->add_setting( 'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit_background]', array(
			'default'           => $this->settings->get_params( 'icon_transit_background', 'timeline_track_info_template_one' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit_background]',
				array(
					'label'   => __( 'In-transit status icon background', 'woo-orders-tracking' ),
					'section' => 'vi_wot_orders_tracking_design_template_one',
				) )
		);
	}

	/**
	 * @param $wp_customize WP_Customize_Manager
	 */
	protected function add_section_design_custom_css( $wp_customize ) {
		$wp_customize->add_setting( 'woo_orders_tracking_settings[custom_css]', array(
			'default'           => $this->settings->get_default( 'custom_css' ),
			'type'              => 'option',
			'capability'        => 'manage_options',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'woo_orders_tracking_settings[custom_css]', array(
			'type'     => 'textarea',
			'priority' => 10,
			'section'  => 'vi_wot_orders_tracking_design_custom_css',
			'label'    => __( 'Custom CSS', 'woo-orders-tracking' )
		) );
	}

	/*
	 * wp print style
	 */
	public function customize_controls_print_styles() {
		if ( ! is_customize_preview() ) {
			return;
		}
		$service_carrier_type = $this->settings->get_params( 'service_carrier_type' );
//		wp_enqueue_style( 'vi-wot-customize-shortcode-track-order-css', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shortcode-track-order.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
//		wp_enqueue_style( 'vi-wot-customize-icon-css', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shipment-icon.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
		?>
        <style type="text/css" id="<?php echo $this->set( 'preview-custom-css' ) ?>">
            <?php
			echo $this->settings->get_params('custom_css');
		  ?>
        </style>
        <style type="text/css" id="<?php echo $this->set( 'preview-show-timeline' ) ?>">
            <?php
            $service_tracking_page = $this->settings->get_params( 'service_tracking_page' );
            if ( $service_tracking_page && $service_tracking_page_url = get_the_permalink( $service_tracking_page ) ) {
				?>
            .woo-orders-tracking-shortcode-timeline-wrap {
                display: block;
            }

            <?php
			}else{
				?>
            .woo-orders-tracking-shortcode-timeline-wrap {
                display: none !important;
            }

            <?php
			}
		  ?>
        </style>
        <style type="text/css" id="<?php echo $this->set( 'preview-show-timeline-template' ) ?>">
            <?php
            $template=$this->settings->get_params('timeline_track_info_template')? $this->settings->get_params('timeline_track_info_template'):$this->settings->get_default('timeline_track_info_template');
			switch ($template){
			    case '1':
			        ?>
            .woo-orders-tracking-preview-shortcode-template-two {
                display: none !important;
            }

            .woo-orders-tracking-preview-shortcode-template-one {
                display: block;
            }

            <?php
			        break;
			    case '2':
			        ?>
            .woo-orders-tracking-preview-shortcode-template-two {
                display: block;
            }

            .woo-orders-tracking-preview-shortcode-template-one {
                display: none !important;
            }

            <?php
			        break;
			}
		  ?>
        </style>
		<?php
		$this->add_preview_style( 'timeline_track_info_title_alignment', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'text-align', '' );
		$this->add_preview_style( 'timeline_track_info_title_color', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'color', '' );
		$this->add_preview_style( 'timeline_track_info_title_font_size', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'font-size', 'px' );

		$this->add_preview_style( 'timeline_track_info_status_color', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap', 'color', '' );
		$this->add_preview_style( 'timeline_track_info_status_background_delivered', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-delivered', 'background-color', '' );
		$this->add_preview_style( 'timeline_track_info_status_background_pickup', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pickup', 'background-color', '' );
		$this->add_preview_style( 'timeline_track_info_status_background_transit', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-transit', 'background-color', '' );
		$this->add_preview_style( 'timeline_track_info_status_background_alert', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-alert', 'background-color', '' );
		$this->add_preview_style( 'timeline_track_info_status_background_pending', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pending', 'background-color', '' );

		//template one
		$this->add_preview_style( 'icon_delivered_color',
			'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-delivered i:before',
			'color', '', 'timeline_track_info_template_one' );

		$this->add_preview_style( 'icon_pickup_color',
			'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-pickup i:before',
			'color', '', 'timeline_track_info_template_one' );
		$this->add_preview_style( 'icon_pickup_background',
			'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-pickup',
			'background-color', '', 'timeline_track_info_template_one' );

		$this->add_preview_style( 'icon_transit_color',
			'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-transit i:before',
			'color', '', 'timeline_track_info_template_one' );
		$this->add_preview_style( 'icon_transit_background',
			'.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one
.woo-orders-tracking-shortcode-timeline-events-wrap
.woo-orders-tracking-shortcode-timeline-event
.woo-orders-tracking-shortcode-timeline-icon-transit',
			'background-color', '', 'timeline_track_info_template_one' );

	}

	/*
	 * wp print script
	 */
	public function customize_controls_print_scripts() {
		if ( ! is_customize_preview() ) {
			return;
		}
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                wp.customize.section('vi_wot_orders_tracking_design_general', function (section) {
                    section.expanded.bind(function (isExpanded) {
                        if (isExpanded) {
                            wp.customize.previewer.send('vi_wot_orders_tracking_design_general', 'show');
                        }
                    })
                });
            });
            jQuery(document).ready(function ($) {
                wp.customize.section('vi_wot_orders_tracking_design_template_one', function (section) {
                    section.expanded.bind(function (isExpanded) {
                        if (isExpanded) {
                            wp.customize.previewer.send('vi_wot_orders_tracking_design_template_one', 'show');
                        }
                    })
                });
            });
        </script>
		<?php
	}

	private function add_preview_style( $name, $element, $style, $suffix = '', $type = '', $echo = true ) {
		ob_start();
		?>
        <style type="text/css"
               id="<?php echo $this->set( 'preview-' ) . str_replace( '_', '-', $name ) ?>">
            <?php echo $element . '{' . ( ( $this->settings->get_params( $name, $type ) === '' ) ? '' : ( $style . ':' . $this->settings->get_params( $name, $type ) . $suffix ) ) . '}' ?>
        </style>
		<?php
		$return = ob_get_clean();
		if ( $echo ) {
			echo $return;
		}

		return $return;
	}

	/*
	 * enqueue script
	 */
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_style( 'vi-wot-customize-preview-style', VI_WOO_ORDERS_TRACKING_CSS . 'customize-preview.css', array(), VI_WOO_ORDERS_TRACKING_VERSION );
		wp_enqueue_style( 'vi-wot-customize-icon', VI_WOO_ORDERS_TRACKING_CSS . 'frontend-shipment-icon.css', array(), VI_WOO_ORDERS_TRACKING_VERSION );
	}

	/*
	 * customize preview init
	 */
	public function customize_preview_init() {
		wp_enqueue_script( 'vi-wot-customize-preview-js', VI_WOO_ORDERS_TRACKING_JS . 'customize-preview.js', array(
			'jquery',
			'customize-preview',
			'select2',
		), VI_WOO_ORDERS_TRACKING_VERSION, true );

		wp_localize_script( 'vi-wot-customize-preview-js', 'vi_wot_customize_params', array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'service_carrier_type' => $this->settings->get_params( 'service_carrier_type' ),
			'delivered_icons'      => VI_WOO_ORDERS_TRACKING_DATA::get_delivered_icons(),
			'pickup_icons'         => VI_WOO_ORDERS_TRACKING_DATA::get_pickup_icons(),
			'transit_icons'        => VI_WOO_ORDERS_TRACKING_DATA::get_transit_icons(),
		) );
	}


	private function set( $name ) {
		if ( is_array( $name ) ) {
			return implode( ' ', array_map( array( $this, 'set' ), $name ) );

		} else {
			return esc_attr__( $this->prefix . $name );

		}
	}

}