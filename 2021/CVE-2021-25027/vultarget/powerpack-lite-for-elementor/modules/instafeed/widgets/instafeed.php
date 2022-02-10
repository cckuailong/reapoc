<?php
namespace PowerpackElementsLite\Modules\Instafeed\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Instagram Feed Widget
 */
class Instafeed extends Powerpack_Widget {

	/**
	 * Instagram Access token.
	 *
	 * @since 2.2.4
	 * @var   string
	 */
	private $insta_access_token = null;

	/**
	 * Instagram API URL.
	 *
	 * @since 2.2.4
	 * @var   string
	 */
	private $insta_api_url = 'https://www.instagram.com/';

	/**
	 * Official Instagram API URL.
	 *
	 * @since 2.2.4
	 * @var   string
	 */
	private $insta_official_api_url = 'https://graph.instagram.com/';

	/**
	 * Retrieve instagram feed widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Instafeed' );
	}

	/**
	 * Retrieve instagram feed widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Instafeed' );
	}

	/**
	 * Retrieve instagram feed widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Instafeed' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Instafeed' );
	}

	/**
	 * Retrieve the list of scripts the instagram feed widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'isotope',
			'imagesloaded',
			'pp-magnific-popup',
			'swiper',
			'powerpack-frontend',
		);
	}

	/**
	 * Register instagram feed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * Remove this after Elementor v3.8.0
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register instagram feed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab: Instagram Account */
		$this->register_content_instaaccount_controls();

		/* Content Tab: Feed Settings */
		//$this->register_content_feed_settings_controls();

		/* Content Tab: General Settings */
		$this->register_content_general_settings_controls();

		/* Content Tab: Carousel Settings */
		$this->register_content_carousel_settings_controls();

		/* Content Tab: Help Docs */
		$this->register_content_help_docs();

		/* Style Tab: Layout */
		$this->register_style_layout_controls();

		/* Style Tab: Images */
		$this->register_style_images_controls();

		/* Style Tab: Content */
		$this->register_style_content_controls();

		/* Style Tab: Overlay */
		$this->register_style_overlay_controls();

		/* Style Tab: Feed Title */
		$this->register_style_feed_title_controls();

		/* Style Tab: Arrows */
		$this->register_style_arrows_controls();

		/* Style Tab: Dots */
		$this->register_style_dots_controls();

		/* Style Tab: Fraction */
		$this->register_style_fraction_controls();

		/* Style Tab: Load More Button */
		//$this->register_style_load_more_button_controls();
	}

	/**
	 * Content Tab: Instagram Account
	 */
	protected function register_content_instaaccount_controls() {
		$this->start_controls_section(
			'section_instaaccount',
			array(
				'label' => __( 'Instagram Account', 'powerpack' ),
			)
		);

		$this->add_control(
			'insta_display',
			[
				'label'     => __( 'Display', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'feed',
				'options'   => [
					'feed'  => __( 'My Photos', 'powerpack' ),
					'tags'  => __( 'Tagged Photos', 'powerpack' ),
				],
			]
		);

		if ( ! $this->get_insta_global_access_token() ) {
			$this->add_control(
				'access_token_missing',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						__( 'The global Instagram access token is missing. You can add it %1$shere%2$s or use a custom one below.', 'powerpack' ),
						'<a target="_blank" href="' . admin_url( 'admin.php?page=powerpack-settings&tab=integration' ) . '">',
						'</a>'
					),
					'content_classes' => 'pp-editor-info',
					'condition'       => [
						'insta_display' => 'feed',
					],
				]
			);
		}

		$this->add_control(
			'access_token',
			[
				'label'                 => __( 'Custom Access Token', 'powerpack' ),
				'description'           => __( 'Overrides global Instagram access token', 'powerpack' ),
				'label_block'           => true,
				'type'                  => Controls_Manager::TEXT,
				'condition'             => [
					'insta_display' => 'feed',
				],
			]
		);

		$this->add_control(
			'insta_hashtag',
			[
				'label'                 => __( 'Hashtag', 'powerpack' ),
				'description'           => __( 'Enter without the # symbol', 'powerpack' ),
				'label_block'           => false,
				'type'                  => Controls_Manager::TEXT,
				'condition'             => [
					'insta_display' => 'tags',
				],
			]
		);

		$this->add_control(
			'cache_timeout',
			array(
				'label'   => esc_html__( 'Cache Timeout', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hour',
				'options' => array(
					'none'   => esc_html__( 'None', 'powerpack' ),
					'minute' => esc_html__( 'Minute', 'powerpack' ),
					'hour'   => esc_html__( 'Hour', 'powerpack' ),
					'day'    => esc_html__( 'Day', 'powerpack' ),
					'week'   => esc_html__( 'Week', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'images_count',
			array(
				'label'      => __( 'Images Count', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 5 ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => '',
			)
		);

		$this->add_control(
			'resolution',
			[
				'label'                 => __( 'Image Resolution', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'thumbnail'              => __( 'Thumbnail (150x150)', 'powerpack' ),
					'low_resolution'         => __( 'Low Resolution (320x320)', 'powerpack' ),
					'standard_resolution'    => __( 'Standard Resolution (640x640)', 'powerpack' ),
					'high'                   => __( 'High Resolution (original)', 'powerpack' ),
				],
				'default'               => 'low_resolution',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Feed Settings
	 */
	protected function register_content_feed_settings_controls() {
		$this->start_controls_section(
			'section_instafeed',
			array(
				'label' => __( 'Feed Settings', 'powerpack' ),
			)
		);

		/* $this->add_control(
			'sort_by',
			[
				'label'                 => __( 'Sort By', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'none'               => __( 'None', 'powerpack' ),
					'most-recent'        => __( 'Most Recent', 'powerpack' ),
					'least-recent'       => __( 'Least Recent', 'powerpack' ),
					'most-liked'         => __( 'Most Liked', 'powerpack' ),
					'least-liked'        => __( 'Least Liked', 'powerpack' ),
					'most-commented'     => __( 'Most Commented', 'powerpack' ),
					'least-commented'    => __( 'Least Commented', 'powerpack' ),
					'random'             => __( 'Random', 'powerpack' ),
				],
				'default'               => 'none',
			]
		); */

		$this->end_controls_section();
	}

	/**
	 * Content Tab: General Settings
	 */
	protected function register_content_general_settings_controls() {
		$this->start_controls_section(
			'section_general_settings',
			array(
				'label' => __( 'General Settings', 'powerpack' ),
			)
		);

		$this->add_control(
			'feed_layout',
			array(
				'label'              => __( 'Layout', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'grid',
				'options'            => array(
					'grid'     => __( 'Grid', 'powerpack' ),
					'masonry'  => __( 'Masonry', 'powerpack' ),
					'carousel' => __( 'Carousel', 'powerpack' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'square_images',
			array(
				'label'        => __( 'Square Images', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'feed_layout' => array( 'grid', 'carousel' ),
				),
			)
		);

		$this->add_responsive_control(
			'grid_cols',
			array(
				'label'          => __( 'Grid Columns', 'powerpack' ),
				'type'           => Controls_Manager::SELECT,
				'label_block'    => false,
				'default'        => '5',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'options'        => array(
					'1' => __( '1', 'powerpack' ),
					'2' => __( '2', 'powerpack' ),
					'3' => __( '3', 'powerpack' ),
					'4' => __( '4', 'powerpack' ),
					'5' => __( '5', 'powerpack' ),
					'6' => __( '6', 'powerpack' ),
					'7' => __( '7', 'powerpack' ),
					'8' => __( '8', 'powerpack' ),
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-instagram-feed-grid .pp-feed-item' => 'width: calc( 100% / {{VALUE}} )',
				),
				'render_type'    => 'template',
				'condition'      => array(
					'feed_layout' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_control(
			'insta_likes',
			array(
				'label'              => __( 'Likes', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'no',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
				'separator'          => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'insta_comments',
			array(
				'label'              => __( 'Comments', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'no',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'insta_caption',
			array(
				'label'              => __( 'Caption', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
			)
		);

		$this->add_control(
			'insta_caption_length',
			array(
				'label'   => __( 'Caption Length', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'dynamic' => array(
					'active' => true,
				),
				'default' => 30,
				'condition'             => [
					'insta_caption' => 'yes',
				],
			)
		);

		$this->add_control(
			'content_visibility',
			array(
				'label'      => __( 'Content Visibility', 'powerpack' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'always',
				'options'    => array(
					'always' => __( 'Always', 'powerpack' ),
					'hover'  => __( 'On Hover', 'powerpack' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'insta_image_popup',
			array(
				'label'        => __( 'Lightbox', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'insta_image_link',
			array(
				'label'        => __( 'Image Link', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'insta_image_popup!' => 'yes',
				),
			)
		);

		$this->add_control(
			'insta_profile_link',
			array(
				'label'        => __( 'Show Link to Instagram Profile?', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'insta_link_title',
			array(
				'label'     => __( 'Link Title', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Follow Us @ Instagram', 'powerpack' ),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'insta_profile_url',
			array(
				'label'       => __( 'Instagram Profile URL', 'powerpack' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://www.your-link.com',
				'default'     => array(
					'url' => '#',
				),
				'condition'   => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_icon',
			array(
				'label'            => __( 'Title Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'insta_title_icon',
				'recommended'      => array(
					'fa-brands' => array(
						'instagram',
					),
					'fa-regular' => array(
						'user',
						'user-circle',
					),
					'fa-solid'  => array(
						'user',
						'user-circle',
						'user-check',
						'user-graduate',
						'user-md',
						'user-plus',
						'user-tie',
					),
				),
				'default'          => array(
					'value'   => 'fab fa-instagram',
					'library' => 'fa-brands',
				),
				'condition'        => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'insta_title_icon_position',
			array(
				'label'     => __( 'Icon Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'before_title' => __( 'Before Title', 'powerpack' ),
					'after_title'  => __( 'After Title', 'powerpack' ),
				),
				'default'   => 'before_title',
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		/* $this->add_control(
			'load_more_button',
			array(
				'label'        => __( 'Show Load More Button', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => array(
					'use_api'     => 'yes',
					'feed_layout' => 'grid',
				),
			)
		);

		$this->add_control(
			'load_more_button_text',
			array(
				'label'     => __( 'Button Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Load More', 'powerpack' ),
				'condition' => array(
					'use_api'          => 'yes',
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		); */

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Carousel Settings
	 */
	protected function register_content_carousel_settings_controls() {
		$this->start_controls_section(
			'section_carousel_settings',
			array(
				'label'     => __( 'Carousel Settings', 'powerpack' ),
				'condition' => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_responsive_control(
			'items',
			array(
				'label'      => __( 'Visible Items', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 3 ),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'size_units' => '',
				'condition'  => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => __( 'Items Gap', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 10 ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => '',
				'condition'  => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'slider_speed',
			array(
				'label'       => __( 'Slider Speed', 'powerpack' ),
				'description' => __( 'Duration of transition between slides (in ms)', 'powerpack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array( 'size' => 600 ),
				'range'       => array(
					'px' => array(
						'min'  => 100,
						'max'  => 3000,
						'step' => 1,
					),
				),
				'size_units'  => '',
				'separator'   => 'before',
				'condition'   => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'        => __( 'Pause on Interaction', 'powerpack' ),
				'description'  => __( 'Disables autoplay completely on first interaction with the carousel.', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'autoplay'    => 'yes',
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 3000,
				'title'     => __( 'Enter carousel speed', 'powerpack' ),
				'condition' => array(
					'autoplay'    => 'yes',
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'        => __( 'Infinite Loop', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'grab_cursor',
			array(
				'label'        => __( 'Grab Cursor', 'powerpack' ),
				'description'  => __( 'Shows grab cursor when you hover over the slider', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'navigation_heading',
			array(
				'label'     => __( 'Navigation', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => __( 'Arrows', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => __( 'Pagination', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'  => __( 'Dots', 'powerpack' ),
					'fraction' => __( 'Fraction', 'powerpack' ),
				),
				'condition' => array(
					'dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'     => __( 'Direction', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'powerpack' ),
					'right' => __( 'Right', 'powerpack' ),
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Help Docs
	 *
	 * @since 1.4.8
	 * @access protected
	 */
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links( 'Instafeed' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 1.4.8
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				array(
					'label' => __( 'Help Docs', 'powerpack' ),
				)
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					)
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Style Tab: Layout
	 */
	protected function register_style_layout_controls() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label'     => __( 'Layout', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'feed_layout' => array( 'grid', 'masonry' ),
				),
			)
		);$this->add_responsive_control(
			'columns_gap',
			array(
				'label'          => __( 'Columns Gap', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => '',
					'unit' => 'px',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'max' => 100,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-instafeed-grid .pp-feed-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}} .pp-instafeed-grid' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2); margin-right: calc(-{{SIZE}}{{UNIT}}/2);',
				),
				'render_type'    => 'template',
				'condition'      => array(
					'feed_layout' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label'          => __( 'Rows Gap', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => '',
					'unit' => 'px',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'max' => 100,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-instafeed-grid .pp-feed-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'render_type'    => 'template',
				'condition'      => array(
					'feed_layout' => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Images
	 */
	protected function register_style_images_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Images', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_image_style' );

		$this->start_controls_tab(
			'tab_image_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'insta_image_grayscale',
			array(
				'label'        => __( 'Grayscale Image', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'images_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-instagram-feed .pp-if-img',
			)
		);

		$this->add_control(
			'images_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-if-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_image_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'insta_image_grayscale_hover',
			array(
				'label'        => __( 'Grayscale Image', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'images_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-feed-item:hover .pp-if-img' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Content
	 */
	protected function register_style_content_controls() {
		$this->start_controls_section(
			'section_content_style',
			array(
				'label'      => __( 'Content', 'powerpack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'       => 'content_typography',
				'label'      => __( 'Typography', 'powerpack' ),
				'selector'   => '{{WRAPPER}} .pp-feed-item .pp-overlay-container',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'likes_comments_color',
			array(
				'label'      => __( 'Color', 'powerpack' ),
				'type'       => Controls_Manager::COLOR,
				'default'    => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-feed-item .pp-overlay-container' => 'color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'content_vertical_align',
			array(
				'label'                => __( 'Vertical Align', 'powerpack' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'middle',
				'options'              => array(
					'top'    => array(
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pp-overlay-container' => 'justify-content: {{VALUE}};',
				),
				'conditions'           => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'content_horizontal_align',
			array(
				'label'                => __( 'Horizontal Align', 'powerpack' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pp-overlay-container' => 'align-items: {{VALUE}};',
				),
				'conditions'           => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'text_align',
			array(
				'label'    => __( 'Text Align', 'powerpack' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => array(
					'left' => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pp-overlay-container' => 'text-align: {{VALUE}};',
				),
				'conditions'           => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-overlay-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'icons_heading',
			array(
				'label'      => __( 'Icons', 'powerpack' ),
				'type'       => Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'icons_style',
			array(
				'label'              => __( 'Style', 'powerpack' ),
				'type'               => Controls_Manager::CHOOSE,
				'label_block'        => false,
				'toggle'             => false,
				'default'            => 'solid',
				'options'            => array(
					'solid'   => array(
						'title' => __( 'Solid', 'powerpack' ),
						'icon'  => 'fa fa-comment',
					),
					'outline' => array(
						'title' => __( 'Outline', 'powerpack' ),
						'icon'  => 'fa fa-comment-o',
					),
				),
				'frontend_available' => true,
				'conditions'         => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 2.5,
						'step' => 0.1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-feed-item .pp-if-icon' => 'font-size: {{SIZE}}em;',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'insta_likes',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_comments',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'insta_caption',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Overlay
	 */
	protected function register_style_overlay_controls() {
		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_blend_mode',
			array(
				'label'     => __( 'Blend Mode', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'normal',
				'options'   => array(
					'normal'      => __( 'Normal', 'powerpack' ),
					'multiply'    => __( 'Multiply', 'powerpack' ),
					'screen'      => __( 'Screen', 'powerpack' ),
					'overlay'     => __( 'Overlay', 'powerpack' ),
					'darken'      => __( 'Darken', 'powerpack' ),
					'lighten'     => __( 'Lighten', 'powerpack' ),
					'color-dodge' => __( 'Color Dodge', 'powerpack' ),
					'color'       => __( 'Color', 'powerpack' ),
					'hue'         => __( 'Hue', 'powerpack' ),
					'hard-light'  => __( 'Hard Light', 'powerpack' ),
					'soft-light'  => __( 'Soft Light', 'powerpack' ),
					'difference'  => __( 'Difference', 'powerpack' ),
					'exclusion'   => __( 'Exclusion', 'powerpack' ),
					'saturation'  => __( 'Saturation', 'powerpack' ),
					'luminosity'  => __( 'Luminosity', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-overlay-container' => 'mix-blend-mode: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'image_overlay_normal',
				'label'    => __( 'Overlay', 'powerpack' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array(
					'image',
				),
				'selector' => '{{WRAPPER}} .pp-instagram-feed .pp-overlay-container',
			)
		);

		$this->add_control(
			'overlay_margin_normal',
			array(
				'label'     => __( 'Margin', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-overlay-container' => 'top: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px; right: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'image_overlay_opacity_normal',
			array(
				'label'      => __( 'Overlay Opacity', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-overlay-container' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'image_overlay_hover',
				'label'    => __( 'Overlay', 'powerpack' ),
				'types'    => array( 'classic', 'gradient' ),
				'exclude'  => array(
					'image',
				),
				'selector' => '{{WRAPPER}} .pp-instagram-feed .pp-feed-item:hover .pp-overlay-container',
			)
		);

		$this->add_control(
			'image_overlay_opacity_hover',
			array(
				'label'      => __( 'Overlay Opacity', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-feed-item:hover .pp-overlay-container' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Feed Title
	 */
	protected function register_style_feed_title_controls() {
		$this->start_controls_section(
			'section_feed_title_style',
			array(
				'label'     => __( 'Feed Title', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'feed_title_position',
			array(
				'label'        => __( 'Position', 'powerpack' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'default'      => 'middle',
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'powerpack' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'prefix_class' => 'pp-insta-title-',
				'condition'    => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'feed_title_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-instagram-feed-title',
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color_normal',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-instagram-feed-title-wrap .pp-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'title_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-instagram-feed-title-wrap',
			)
		);

		$this->add_control(
			'title_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-instagram-feed-title-wrap a:hover .pp-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'title_border_hover',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-instagram-feed-title-wrap:hover',
			)
		);

		$this->add_control(
			'title_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed-title-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'insta_profile_link' => 'yes',
				),
				'separator'  => 'before',
			)
		);

		$this->add_control(
			'title_icon_heading',
			array(
				'label'     => __( 'Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'title_icon_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 4 ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-icon-before_title' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-instagram-feed .pp-icon-after_title' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'insta_profile_link' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Arrows
	 */
	protected function register_style_arrows_controls() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'arrows'      => 'yes',
					'feed_layout' => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrow',
			array(
				'label'       => __( 'Choose Arrow', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'default'     => 'fa fa-angle-right',
				'options'     => array(
					'fa fa-angle-right'          => __( 'Angle', 'powerpack' ),
					'fa fa-angle-double-right'   => __( 'Double Angle', 'powerpack' ),
					'fa fa-chevron-right'        => __( 'Chevron', 'powerpack' ),
					'fa fa-chevron-circle-right' => __( 'Chevron Circle', 'powerpack' ),
					'fa fa-arrow-right'          => __( 'Arrow', 'powerpack' ),
					'fa fa-long-arrow-right'     => __( 'Long Arrow', 'powerpack' ),
					'fa fa-caret-right'          => __( 'Caret', 'powerpack' ),
					'fa fa-caret-square-o-right' => __( 'Caret Square', 'powerpack' ),
					'fa fa-arrow-circle-right'   => __( 'Arrow Circle', 'powerpack' ),
					'fa fa-arrow-circle-o-right' => __( 'Arrow Circle O', 'powerpack' ),
					'fa fa-toggle-right'         => __( 'Toggle', 'powerpack' ),
					'fa fa-hand-o-right'         => __( 'Hand', 'powerpack' ),
				),
			)
		);

		$this->add_responsive_control(
			'arrows_size',
			array(
				'label'      => __( 'Arrows Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => '22' ),
				'range'      => array(
					'px' => array(
						'min'  => 15,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_arrow_position',
			array(
				'label'      => __( 'Align Left Arrow', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_arrow_position',
			array(
				'label'      => __( 'Align Right Arrow', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'arrows_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_color_normal',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'arrows_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-instagram-feed .pp-swiper-button',
			)
		);

		$this->add_control(
			'arrows_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'arrows_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_color_hover',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .pp-swiper-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Pagination: Dots
	 */
	protected function register_style_dots_controls() {
		$this->start_controls_section(
			'section_dots_style',
			array(
				'label'     => __( 'Pagination: Dots', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_position',
			array(
				'label'        => __( 'Position', 'powerpack' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'inside'  => __( 'Inside', 'powerpack' ),
					'outside' => __( 'Outside', 'powerpack' ),
				),
				'default'      => 'outside',
				'prefix_class' => 'swiper-container-dots-',
				'condition'    => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_responsive_control(
			'dots_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 2,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_responsive_control(
			'dots_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_dots_style' );

		$this->start_controls_tab(
			'tab_dots_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_color_normal',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'active_dot_color_normal',
			array(
				'label'     => __( 'Active Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'dots_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet',
				'condition'   => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_responsive_control(
			'dots_margin',
			array(
				'label'              => __( 'Margin', 'powerpack' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'allowed_dimensions' => 'vertical',
				'placeholder'        => array(
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				),
				'selectors'          => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullets' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_color_hover',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-instagram-feed .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Pagination: Fraction
	 * -------------------------------------------------
	 */
	protected function register_style_fraction_controls() {
		$this->start_controls_section(
			'section_fraction_style',
			array(
				'label'     => __( 'Pagination: Fraction', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'fraction',
				),
			)
		);

		$this->add_control(
			'fraction_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'fraction',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'fraction_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .swiper-pagination-fraction',
				'condition' => array(
					'feed_layout'     => 'carousel',
					'dots'            => 'yes',
					'pagination_type' => 'fraction',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Load More Button
	 * -------------------------------------------------
	 */
	protected function register_style_load_more_button_controls() {
		$this->start_controls_section(
			'section_load_more_button_style',
			array(
				'label'     => __( 'Load More Button', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button-wrap' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_top_spacing',
			array(
				'label'      => __( 'Top Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 20 ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-load-more-button-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'md',
				'options'   => array(
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-load-more-button',
				'condition'   => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-load-more-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-load-more-button',
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-load-more-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-load-more-button',
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'load_more_button_icon_heading',
			array(
				'label'     => __( 'Button Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
					'button_icon!'     => '',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_margin',
			array(
				'label'       => __( 'Margin', 'powerpack' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%' ),
				'placeholder' => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'condition'   => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
					'button_icon!'     => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .pp-info-box .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-load-more-button:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_control(
			'button_animation',
			array(
				'label'     => __( 'Animation', 'powerpack' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .pp-load-more-button:hover',
				'condition' => array(
					'load_more_button' => 'yes',
					'feed_layout'      => 'grid',
				),
			)
		);
	}

	/**
	 * Get Instagram access token.
	 *
	 * @since 2.2.4
	 * @return string
	 */
	public function get_insta_access_token() {
		$settings = $this->get_settings_for_display();

		if ( ! $this->insta_access_token ) {
			$custom_access_token = $settings['access_token'];

			if ( '' !== trim( $custom_access_token ) ) {
				$this->insta_access_token = $custom_access_token;
			} else {
				$this->insta_access_token = $this->get_insta_global_access_token();
			}
		}

		return $this->insta_access_token;
	}

	/**
	 * Get Instagram access token from PowerPack options.
	 *
	 * @since 2.2.4
	 * @return string
	 */
	public function get_insta_global_access_token() {
		return \PowerpackElementsLite\Classes\PP_Admin_Settings::get_option( 'pp_instagram_access_token' );
	}

	/**
	 * Retrieve a URL for own photos.
	 *
	 * @since  2.2.4
	 * @return string
	 */
	public function get_feed_endpoint() {
		return $this->insta_official_api_url . 'me/media/';
	}

	/**
	 * Retrieve a URL for photos by hashtag.
	 *
	 * @since  2.2.4
	 * @return string
	 */
	public function get_tags_endpoint() {
		return $this->insta_api_url . 'explore/tags/%s/';
	}

	public function get_user_endpoint() {
		return $this->insta_official_api_url . 'me/';
	}

	public function get_user_media_endpoint() {
		return $this->insta_official_api_url . '%s/media/';
	}

	public function get_media_endpoint() {
		return $this->insta_official_api_url . '%s/';
	}

	public function get_user_url() {
		$url = $this->get_user_endpoint();
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			// 'fields' => 'media.limit(10){comments_count,like_count,likes,likes_count,media_url,permalink,caption}',
		], $url );

		return $url;
	}

	public function get_user_media_url( $user_id ) {
		$url = sprintf( $this->get_user_media_endpoint(), $user_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,like_count',
		], $url );

		return $url;
	}

	public function get_media_url( $media_id ) {
		$url = sprintf( $this->get_media_endpoint(), $media_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,media_type,media_url,timestamp,like_count',
		], $url );

		return $url;
	}

	public function get_insta_user_id() {
		$result = $this->get_insta_remote( $this->get_user_url() );
		return $result;
	}

	public function get_insta_user_media( $user_id ) {
		$result = $this->get_insta_remote( $this->get_user_media_url( $user_id ) );

		return $result;
	}

	public function get_insta_media( $media_id ) {
		$result = $this->get_insta_remote( $this->get_media_url( $media_id ) );

		return $result;
	}

	/**
	 * Retrieve a grab URL.
	 *
	 * @since  2.2.4
	 * @return string
	 */
	public function get_fetch_url() {
		$settings = $this->get_settings();

		if ( 'tags' === $settings['insta_display'] ) {
			$url = sprintf( $this->get_tags_endpoint(), $settings['insta_hashtag'] );
			$url = add_query_arg( array( '__a' => 1 ), $url );

		} elseif ( 'feed' === $settings['insta_display'] ) {
			$url = $this->get_feed_endpoint();
			$url = add_query_arg( [
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,likes_count,likes',
				'access_token' => $this->get_insta_access_token(),
			], $url );
		}

		return $url;
	}

	/**
	 * Get thumbnail data from response data
	 *
	 * @param $post
	 * @since 2.2.4
	 *
	 * @return array
	 */
	public function get_insta_feed_thumbnail_data( $post ) {
		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( ! empty( $post['images'] ) && is_array( $post['images'] ) ) {
			$data = $post['images'];

			$thumbnail['thumbnail'] = [
				'src'           => $data['thumbnail']['url'],
				'config_width'  => $data['thumbnail']['width'],
				'config_height' => $data['thumbnail']['height'],
			];

			$thumbnail['low'] = [
				'src'           => $data['low_resolution']['url'],
				'config_width'  => $data['low_resolution']['width'],
				'config_height' => $data['low_resolution']['height'],
			];

			$thumbnail['standard'] = [
				'src'           => $data['standard_resolution']['url'],
				'config_width'  => $data['standard_resolution']['width'],
				'config_height' => $data['standard_resolution']['height'],
			];

			$thumbnail['high'] = $thumbnail['standard'];
		}

		return $thumbnail;
	}

	/**
	 * Get data from response
	 *
	 * @param  $response
	 * @since  2.2.4
	 *
	 * @return array
	 */
	public function get_insta_feed_response_data( $response ) {
		$settings = $this->get_settings();

		if ( ! array_key_exists( 'data', $response ) ) { // Avoid PHP notices
			return;
		}

		$response_posts = $response['data'];

		if ( empty( $response_posts ) ) {
			return array();
		}

		$return_data  = array();
		$images_count = ! empty( $settings['images_count']['size'] ) ? $settings['images_count']['size'] : 5;
		$posts = array_slice( $response_posts, 0, $images_count, true );

		foreach ( $posts as $post ) {
			$_post              = array();

			$_post['id']        = $post['id'];
			$_post['link']      = $post['permalink'];
			$_post['caption']   = '';
			$_post['image']     = 'VIDEO' === $post['media_type'] ? $post['thumbnail_url'] : $post['media_url'];
			$_post['comments']  = ! empty( $post['comments_count'] ) ? $post['comments_count'] : 0;
			$_post['likes']     = ! empty( $post['likes_count'] ) ? $post['likes_count'] : 0;

			$_post['thumbnail'] = $this->get_insta_feed_thumbnail_data( $post );

			if ( ! empty( $post['caption'] ) ) {
				$_post['caption'] = wp_html_excerpt( $post['caption'], $this->get_settings( 'insta_caption_length' ), '&hellip;' );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Get data from response
	 *
	 * @param  $response
	 * @since  2.2.4
	 *
	 * @return array
	 */
	public function get_insta_tags_response_data( $response ) {
		$settings = $this->get_settings();
		$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'];

		$insta_caption_length = ( $settings['insta_caption_length'] ) ? $settings['insta_caption_length'] : 30;

		if ( empty( $response_posts ) ) {
			$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'];
		}

		$return_data  = array();
		$images_count = ! empty( $settings['images_count']['size'] ) ? $settings['images_count']['size'] : 5;
		$posts = array_slice( $response_posts, 0, $images_count, true );

		foreach ( $posts as $post ) {
			$_post              = array();

			$_post['link']      = sprintf( $this->insta_api_url . 'p/%s/', $post['node']['shortcode'] );
			$_post['caption']   = '';
			$_post['comments']  = $post['node']['edge_media_to_comment']['count'];
			$_post['likes']     = $post['node']['edge_liked_by']['count'];
			$_post['thumbnail'] = $this->get_insta_tags_thumbnail_data( $post );

			if ( isset( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
				$_post['caption'] = wp_html_excerpt( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'], $insta_caption_length, '&hellip;' );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Generate thumbnail resources.
	 *
	 * @since 2.2.4
	 * @param $post_data
	 *
	 * @return array
	 */
	public function get_insta_tags_thumbnail_data( $post ) {
		$post = $post['node'];

		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( is_array( $post['thumbnail_resources'] ) && ! empty( $post['thumbnail_resources'] ) ) {
			foreach ( $post['thumbnail_resources'] as $key => $resources_data ) {

				if ( 150 === $resources_data['config_width'] ) {
					$thumbnail['thumbnail'] = $resources_data;
					continue;
				}

				if ( 320 === $resources_data['config_width'] ) {
					$thumbnail['low'] = $resources_data;
					continue;
				}

				if ( 640 === $resources_data['config_width'] ) {
					$thumbnail['standard'] = $resources_data;
					continue;
				}
			}
		}

		if ( ! empty( $post['display_url'] ) ) {
			$thumbnail['high'] = array(
				'src'           => $post['display_url'],
				'config_width'  => $post['dimensions']['width'],
				'config_height' => $post['dimensions']['height'],
			);
		}

		return $thumbnail;
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.2.4
	 * @return string   The url of the instagram post image
	 */
	protected function get_insta_image_size() {
		$settings = $this->get_settings();

		$size = $settings['resolution'];

		switch ( $size ) {
			case 'thumbnail':
				return 'thumbnail';
			case 'low_resolution':
				return 'low';
			case 'standard_resolution':
				return 'standard';
			default:
				return 'low';
		}
	}

	/**
	 * Retrieve response from API
	 *
	 * @since  2.2.4
	 * @return array|WP_Error
	 */
	public function get_insta_remote( $url ) {
		$response       = wp_remote_get( $url, array(
			'timeout'   => 60,
			'sslverify' => false,
		) );

		$response_code  = wp_remote_retrieve_response_code( $response );
		$result         = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			$message = is_array( $result ) && isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'No posts found', 'powerpack' );

			return new \WP_Error( $response_code, $message );
		}

		if ( ! is_array( $result ) ) {
			return new \WP_Error( 'error', __( 'Data Error', 'powerpack' ) );
		}

		return $result;
	}

	/**
	 * Sanitize endpoint.
	 *
	 * @since  2.2.4
	 * @return string
	 */
	public function sanitize_endpoint() {
		$settings = $this->get_settings();

		return in_array( $settings['insta_display'], array( 'feed', 'tags' ) ) ? $settings['insta_display'] : 'tags';
	}

	/**
	 * Get transient key.
	 *
	 * @since  2.2.4
	 * @return string
	 */
	public function get_transient_key() {
		$settings = $this->get_settings();

		$endpoint = $this->sanitize_endpoint();
		$target = ( 'tags' === $endpoint ) ? sanitize_text_field( $settings['insta_hashtag'] ) : 'users';
		$insta_caption_length = ( $settings['insta_caption_length'] ) ? $settings['insta_caption_length'] : 30;
		$images_count = $settings['images_count']['size'];

		return sprintf( 'ppe_instagram_%s_%s_posts_count_%s_caption_%s',
			$endpoint,
			$target,
			$images_count,
			$insta_caption_length
		);
	}

	/**
	 * Render Title Icon.
	 *
	 * @since  2.3.2
	 */
	public function render_title_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['insta_title_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['insta_title_icon'] = 'fa fa-instagram';
		}

		$has_icon = ! empty( $settings['insta_title_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['insta_title_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['title_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['title_icon'] );
		$is_new   = ! isset( $settings['insta_title_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( $has_icon ) {
			?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'title-icon' ) ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['title_icon'], array( 'aria-hidden' => 'true' ) );
				} elseif ( ! empty( $settings['insta_title_icon'] ) ) {
					?>
					<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
					<?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render Instagram profile link.
	 *
	 * @since  2.2.4
	 * @param  array $settings
	 * @return array
	 */
	public function get_insta_profile_link() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title-icon', 'class', 'pp-icon pp-icon-' . $settings['insta_title_icon_position'] );

		if ( 'yes' === $settings['insta_profile_link'] && $settings['insta_link_title'] ) { ?>
			<span class="pp-instagram-feed-title-wrap">
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'instagram-profile-link' ) ); ?>>
					<span class="pp-instagram-feed-title">
						<?php
						if ( 'before_title' === $settings['insta_title_icon_position'] ) {
							$this->render_title_icon();
						}

						echo esc_attr( $settings['insta_link_title'] );

						if ( 'after_title' === $settings['insta_title_icon_position'] ) {
							$this->render_title_icon();
						}
						?>
					</span>
				</a>
			</span>
		<?php }
	}

	/**
	 * Retrieve Instagram posts.
	 *
	 * @since  2.2.4
	 * @param  array $settings
	 * @return array
	 */
	public function get_insta_posts( $settings ) {
		$settings = $this->get_settings();

		$transient_key = md5( $this->get_transient_key() );

		$data = get_transient( $transient_key );

		if ( ! empty( $data ) && 1 !== $settings['cache_timeout'] && array_key_exists( 'thumbnail_resources', $data[0] ) ) {
			return $data;
		}

		// $user = $this->get_insta_user_id();
		// $user_media = $this->get_insta_user_media( $user['id'] );

		// foreach( $user_media['data'] as $media ) {
		// 	$media_object = $this->get_insta_media( $media['id'] );
		// }

		$response = $this->get_insta_remote( $this->get_fetch_url() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = ( 'tags' === $settings['insta_display'] ) ? $this->get_insta_tags_response_data( $response ) : $this->get_insta_feed_response_data( $response );

		if ( empty( $data ) ) {
			return array();
		}

		set_transient( $transient_key, $data, $settings['cache_timeout'] );

		return $data;
	}

	/**
	 * Render promo box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( 'carousel' === $settings['feed_layout'] ) {
			$layout = 'carousel';
		} else {
			$layout = 'grid';
		}

		$this->add_render_attribute(
			'insta-feed-wrap',
			'class',
			array(
				'pp-instagram-feed',
				'clearfix',
				'pp-instagram-feed-' . $layout,
				'pp-instagram-feed-' . $settings['content_visibility'],
			)
		);

		if ( ( 'grid' === $settings['feed_layout'] || 'masonry' === $settings['feed_layout'] ) && $settings['grid_cols'] ) {
			$this->add_render_attribute( 'insta-feed-wrap', 'class', 'pp-instagram-feed-grid-' . $settings['grid_cols'] );
		}

		if ( 'yes' === $settings['insta_image_grayscale'] ) {
			$this->add_render_attribute( 'insta-feed-wrap', 'class', 'pp-instagram-feed-gray' );
		}

		if ( 'yes' === $settings['insta_image_grayscale_hover'] ) {
			$this->add_render_attribute( 'insta-feed-wrap', 'class', 'pp-instagram-feed-hover-gray' );
		}

		if ( 'masonry' !== $settings['feed_layout'] && 'yes' === $settings['square_images'] ) {
			$this->add_render_attribute( 'insta-feed-wrap', 'class', 'pp-if-square-images' );
		}

		$this->add_render_attribute( 'insta-feed-container', 'class', 'pp-instafeed' );

		$this->add_render_attribute(
			'insta-feed',
			array(
				'id'    => 'pp-instafeed-' . esc_attr( $this->get_id() ),
				'class' => 'pp-instafeed-grid',
			)
		);

		$this->add_render_attribute( 'insta-feed-inner', 'class', 'pp-insta-feed-inner' );

		if ( 'carousel' === $settings['feed_layout'] ) {
			$this->add_render_attribute(
				array(
					'insta-feed-inner'     => array(
						'class' => array(
							'swiper-container-wrap',
							'pp-insta-feed-carousel-wrap',
						),
					),
					'insta-feed-container' => array(
						'class' => array(
							'pp-swiper-slider',
							'swiper-container',
							'swiper-container-' . esc_attr( $this->get_id() ),
						),
					),
					'insta-feed'           => array(
						'class' => array(
							'swiper-wrapper',
						),
					),
				)
			);

			$slider_options = $this->get_swiper_slider_settings( $settings, false );

			$this->add_render_attribute(
				'insta-feed-container',
				array(
					'data-slider-settings' => wp_json_encode( $slider_options ),
				)
			);

			if ( 'right' === $settings['direction'] ) {
				$this->add_render_attribute( 'insta-feed-container', 'dir', 'rtl' );
			}
		}

		if ( ! empty( $settings['insta_profile_url']['url'] ) ) {
			$this->add_link_attributes( 'instagram-profile-link', $settings['insta_profile_url'] );
		}

		$pp_widget_options = [
			'user_id'           => '',
			'access_token'      => ! empty( $settings['access_token'] ) ? $settings['access_token'] : '',
			//'sort_by'           => ! empty( $settings['sort_by'] ) ? $settings['sort_by'] : '',
			'images_count'      => ! empty( $settings['images_count']['size'] ) ? $settings['images_count']['size'] : 5,
			'target'            => 'pp-instafeed-' . esc_attr( $this->get_id() ),
			'resolution'        => ! empty( $settings['resolution'] ) ? $settings['resolution'] : '',
			'popup'             => ( 'yes' === $settings['insta_image_popup'] ) ? '1' : '0',
			'img_link'          => ( 'yes' !== $settings['insta_image_popup'] && 'yes' === $settings['insta_image_link'] ) ? '1' : '0',
		];

		$this->render_api_images();
	}

	/**
	 * Render load more button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  2.2.4
	 * @access protected
	 */
	protected function render_api_images() {
		$settings = $this->get_settings();

		$gallery = $this->get_insta_posts( $settings );

		if ( empty( $gallery ) || is_wp_error( $gallery ) ) {
			$message = is_wp_error( $gallery ) ? $gallery->get_error_message() : esc_html__( 'No Posts Found', 'powerpack' );

			echo wp_kses_post( $message );

			return;
		}
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'insta-feed-wrap' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'insta-feed-inner' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'insta-feed-container' ) ); ?>>
					<?php $this->get_insta_profile_link(); ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'insta-feed' ) ); ?>>
						<?php
						foreach ( $gallery as $index => $item ) {
							$item_key = $this->get_repeater_setting_key( 'item', 'insta_images', $index );
							$this->add_render_attribute( $item_key, 'class', 'pp-feed-item' );

							if ( 'carousel' === $settings['feed_layout'] ) {
								$this->add_render_attribute( $item_key, 'class', 'swiper-slide' );
							}
							?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
								<div class="pp-feed-item-inner">
								<?php $this->render_image_thumbnail( $item, $index ); ?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				$this->render_dots();

				$this->render_arrows();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Image Thumbnail
	 *
	 * @since  2.2.4
	 * @return void
	 */
	protected function render_image_thumbnail( $item, $index ) {
		$settings        = $this->get_settings();
		$thumbnail_url   = $this->get_insta_image_url( $item, $this->get_insta_image_size() );
		$thumbnail_alt   = $item['caption'];
		$thumbnail_title = $item['caption'];
		$likes           = $item['likes'];
		$comments        = $item['comments'];
		$image_key       = $this->get_repeater_setting_key( 'image', 'insta', $index );
		$link_key        = $this->get_repeater_setting_key( 'link', 'image', $index );
		$item_link       = '';

		$this->add_render_attribute( $image_key, 'src', $thumbnail_url );

		if ( '' !== $thumbnail_alt ) {
			$this->add_render_attribute( $image_key, 'alt', $thumbnail_alt );
		}

		if ( '' !== $thumbnail_title ) {
			$this->add_render_attribute( $image_key, 'title', $thumbnail_title );
		}

		if ( 'yes' === $settings['insta_image_popup'] ) {

			$item_link = $this->get_insta_image_url( $item, 'high' );

			$this->add_render_attribute( $link_key, [
				'data-elementor-open-lightbox'      => 'yes',
				'data-elementor-lightbox-title'     => $thumbnail_alt,
				'data-elementor-lightbox-slideshow' => 'pp-ig-' . $this->get_id(),
			] );

			/*if ( $this->_is_edit_mode ) {
				$this->add_render_attribute( $link_key, 'class', 'elementor-clickable' );
			}*/

		} elseif ( 'yes' === $settings['insta_image_link'] ) {
			$item_link = $item['link'];

			$this->add_render_attribute( $link_key, 'target', '_blank' );
		}

		$this->add_render_attribute( $link_key, 'href', $item_link );

		$image_html = '<div class="pp-if-img">';
		$image_html .= '<div class="pp-overlay-container pp-media-overlay">';
		if ( 'yes' === $settings['insta_caption'] ) {
			$image_html .= '<div class="pp-insta-caption">' . $thumbnail_alt . '</div>';
		}
		if ( 'yes' === $settings['insta_comments'] || 'yes' === $settings['insta_likes'] ) {
			$image_html .= '<div class="pp-insta-icons">';
			if ( 'yes' === $settings['insta_comments'] ) {
				$image_html .= '<span class="comments"><i class="pp-if-icon fa fa-comment"></i> ' . $comments . '</span>';
			}
			if ( 'yes' === $settings['insta_likes'] ) {
				$image_html .= '<span class="likes"><i class="pp-if-icon fa fa-heart"></i> ' . $likes . '</span>';
			}
			$image_html .= '</div>';
		}
		$image_html .= '</div>';
		$image_html .= '<img ' . $this->get_render_attribute_string( $image_key ) . '/>';
		$image_html .= '</div>';

		if ( 'yes' === $settings['insta_image_popup'] || 'yes' === $settings['insta_image_link'] ) {
			$image_html = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
		}

		echo wp_kses_post( $image_html );
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.2.4
	 * @return string   The url of the instagram post image
	 */
	protected function get_insta_image_url( $item, $size = 'high' ) {
		$thumbnail  = $item['thumbnail'];

		if ( ! empty( $thumbnail[ $size ] ) ) {
			$image_url = $thumbnail[ $size ]['src'];
		} else {
			$image_url = isset( $item['image'] ) ? $item['image'] : '';
		}

		return $image_url;
	}

	/**
	 * Render load more button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_load_more_button() {
		$settings = $this->get_settings();

		$this->add_render_attribute( 'load-more-button', 'class', [
			'pp-load-more-button',
			'elementor-button',
			'elementor-size-' . $settings['button_size'],
		] );

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'load-more-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}

		if ( 'grid' === $settings['feed_layout'] && 'yes' === $settings['load_more_button'] ) {
			?>
			<div class="pp-load-more-button-wrap">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'load-more-button' ) ); ?>>
					<span class="pp-button-loader"></span>
					<span class="pp-load-more-button-text">
						<?php echo esc_attr( $settings['load_more_button_text'] ); ?>
					</span>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render insta feed carousel dots output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_dots() {
		$settings = $this->get_settings();

		if ( 'carousel' === $settings['feed_layout'] && 'yes' === $settings['dots'] ) {
			?>
			<!-- Add Pagination -->
			<div class="swiper-pagination swiper-pagination-<?php echo esc_attr( $this->get_id() ); ?>"></div>
			<?php
		}
	}

	/**
	 * Render insta feed carousel arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_arrows() {
		$settings = $this->get_settings();

		if ( 'carousel' === $settings['feed_layout'] && 'yes' === $settings['arrows'] ) {
			?>
			<?php
			if ( $settings['arrow'] ) {
				$next_arrow = $settings['arrow'];
				$prev_arrow = str_replace( 'right', 'left', $settings['arrow'] );
			} else {
				$next_arrow = 'fa fa-angle-right';
				$prev_arrow = 'fa fa-angle-left';
			}
			?>
			<!-- Add Arrows -->
			<div class="pp-swiper-button pp-swiper-button-prev pp-slider-arrow pp-arrow-prev swiper-button-prev-<?php echo esc_attr( $this->get_id() ); ?>">
				<i class="<?php echo esc_attr( $prev_arrow ); ?>"></i>
			</div>
			<div class="pp-swiper-button pp-swiper-button-next pp-slider-arrow pp-arrow-next swiper-button-next-<?php echo esc_attr( $this->get_id() ); ?>">
				<i class="<?php echo esc_attr( $next_arrow ); ?>"></i>
			</div>
			<?php
		}
	}

	protected function content_template() {}

}
