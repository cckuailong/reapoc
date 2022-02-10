<?php
namespace PowerpackElementsLite\Modules\TeamMember\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
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
 * Team Member Carousel Widget
 */
class Team_Member_Carousel extends Powerpack_Widget {

	/**
	 * Retrieve team member carousel widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Team_Member_Carousel' );
	}

	/**
	 * Retrieve team member carousel widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Team_Member_Carousel' );
	}

	/**
	 * Retrieve team member carousel widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Team_Member_Carousel' );
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
		return parent::get_widget_keywords( 'Team_Member_Carousel' );
	}

	/**
	 * Retrieve the list of scripts the team member carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'swiper',
			'powerpack-frontend',
		);
	}

	/**
	 * Retrieve the list of styles the team member carousel widget depended on.
	 *
	 * Used to set style dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_style_depends() {
		return array(
			'font-awesome',
		);
	}

	/**
	 * Register team member carousel widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		/*-----------------------------------------------------------------------------------*/
		/* CONTENT TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Team Members
		 */
		$this->start_controls_section(
			'section_team_member',
			array(
				'label' => __( 'Team Members', 'powerpack' ),
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'team_member_tabs' );

		$repeater->start_controls_tab( 'tab_content', array( 'label' => __( 'Content', 'powerpack' ) ) );

			$repeater->add_control(
				'team_member_name',
				array(
					'label'   => __( 'Name', 'powerpack' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'John Doe', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'team_member_position',
				array(
					'label'   => __( 'Position', 'powerpack' ),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'WordPress Developer', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'team_member_description',
				array(
					'label'   => __( 'Description', 'powerpack' ),
					'type'    => Controls_Manager::TEXTAREA,
					'dynamic' => array(
						'active' => true,
					),
					'default' => __( 'Enter member description here which describes the position of member in company', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'team_member_image',
				array(
					'label'   => __( 'Image', 'powerpack' ),
					'type'    => Controls_Manager::MEDIA,
					'dynamic' => array(
						'active' => true,
					),
					'default' => array(
						'url' => Utils::get_placeholder_image_src(),
					),
				)
			);

			$repeater->add_control(
				'link_type',
				array(
					'label'   => __( 'Link Type', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => array(
						'none'  => __( 'None', 'powerpack' ),
						'image' => __( 'Image', 'powerpack' ),
						'title' => __( 'Title', 'powerpack' ),
					),
				)
			);

			$repeater->add_control(
				'link',
				array(
					'label'       => __( 'Link', 'powerpack' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'placeholder' => 'https://www.your-link.com',
					'default'     => array(
						'url' => '#',
					),
					'condition'   => array(
						'link_type!' => 'none',
					),
				)
			);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_social_links', array( 'label' => __( 'Social Links', 'powerpack' ) ) );

			$repeater->add_control(
				'facebook_url',
				array(
					'name'        => 'facebook_url',
					'label'       => __( 'Facebook', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Facebook page or profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'twitter_url',
				array(
					'name'        => 'twitter_url',
					'label'       => __( 'Twitter', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Twitter profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'instagram_url',
				array(
					'label'       => __( 'Instagram', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Instagram profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'linkedin_url',
				array(
					'label'       => __( 'Linkedin', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Linkedin profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'youtube_url',
				array(
					'label'       => __( 'YouTube', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter YouTube profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'pinterest_url',
				array(
					'label'       => __( 'Pinterest', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Pinterest profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'dribbble_url',
				array(
					'label'       => __( 'Dribbble', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter Dribbble profile URL of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'email',
				array(
					'label'       => __( 'Email', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter email ID of team member', 'powerpack' ),
				)
			);

			$repeater->add_control(
				'phone',
				array(
					'label'       => __( 'Contact Number', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'description' => __( 'Enter contact number of team member', 'powerpack' ),
				)
			);

		$this->add_control(
			'team_member_details',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'team_member_name'     => 'Team Member #1',
						'team_member_position' => 'WordPress Developer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'instagram_url'        => '#',
					),
					array(
						'team_member_name'     => 'Team Member #2',
						'team_member_position' => 'Web Designer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'instagram_url'        => '#',
					),
					array(
						'team_member_name'     => 'Team Member #3',
						'team_member_position' => 'Testing Engineer',
						'facebook_url'         => '#',
						'twitter_url'          => '#',
						'instagram_url'        => '#',
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ team_member_name }}}',
			)
		);

		$this->end_controls_section();

		/**
		 * Content Tab: General Settings
		 */
		$this->start_controls_section(
			'section_member_box_settings',
			array(
				'label' => __( 'General Settings', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'thumbnail', // Usage: '{name}_size' and '{name}_custom_dimension', in this case 'thumbnail_size' and 'thumbnail_custom_dimension'.,
				'label'   => __( 'Image Size', 'powerpack' ),
				'default' => 'full',
			)
		);

		$this->add_control(
			'name_html_tag',
			array(
				'label'     => __( 'Name HTML Tag', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h4',
				'options'   => array(
					'h1'   => __( 'H1', 'powerpack' ),
					'h2'   => __( 'H2', 'powerpack' ),
					'h3'   => __( 'H3', 'powerpack' ),
					'h4'   => __( 'H4', 'powerpack' ),
					'h5'   => __( 'H5', 'powerpack' ),
					'h6'   => __( 'H6', 'powerpack' ),
					'div'  => __( 'div', 'powerpack' ),
					'span' => __( 'span', 'powerpack' ),
					'p'    => __( 'p', 'powerpack' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'position_html_tag',
			array(
				'label'   => __( 'Position HTML Tag', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array(
					'h1'   => __( 'H1', 'powerpack' ),
					'h2'   => __( 'H2', 'powerpack' ),
					'h3'   => __( 'H3', 'powerpack' ),
					'h4'   => __( 'H4', 'powerpack' ),
					'h5'   => __( 'H5', 'powerpack' ),
					'h6'   => __( 'H6', 'powerpack' ),
					'div'  => __( 'div', 'powerpack' ),
					'span' => __( 'span', 'powerpack' ),
					'p'    => __( 'p', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'member_social_links',
			array(
				'label'        => __( 'Show Social Icons', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'social_links_position',
			array(
				'label'     => __( 'Social Icons Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after_desc',
				'options'   => array(
					'before_desc' => __( 'Before Description', 'powerpack' ),
					'after_desc'  => __( 'After Description', 'powerpack' ),
				),
				'condition' => array(
					'member_social_links' => 'yes',
					'overlay_content'     => [ 'none', 'all_content' ],
				),
			)
		);

		$this->add_control(
			'overlay_content',
			array(
				'label'     => __( 'Overlay Content', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none'         => __( 'None', 'powerpack' ),
					'social_icons' => __( 'Social Icons', 'powerpack' ),
					'content'      => __( 'Description', 'powerpack' ),
					'all_content'  => __( 'Description', 'powerpack' ) . ' + ' . __( 'Social Icons', 'powerpack' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'member_title_divider',
			array(
				'label'        => __( 'Divider after Name', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'member_position_divider',
			array(
				'label'        => __( 'Divider after Position', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'hide',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'member_description_divider',
			array(
				'label'        => __( 'Divider after Description', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'hide',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Carousel Settings
		 */
		$this->start_controls_section(
			'section_slider_settings',
			array(
				'label' => __( 'Carousel Settings', 'powerpack' ),
			)
		);

		$this->add_responsive_control(
			'items',
			array(
				'label'          => __( 'Visible Items', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array( 'size' => 3 ),
				'tablet_default' => array( 'size' => 2 ),
				'mobile_default' => array( 'size' => 1 ),
				'range'          => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'size_units'     => '',
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'          => __( 'Items Gap', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array( 'size' => 10 ),
				'tablet_default' => array( 'size' => 10 ),
				'mobile_default' => array( 'size' => 10 ),
				'range'          => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units'     => '',
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
				'separator'    => 'before',
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
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'      => __( 'Autoplay Speed', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => 3000 ),
				'range'      => array(
					'px' => array(
						'min'  => 500,
						'max'  => 5000,
						'step' => 1,
					),
				),
				'size_units' => '',
				'condition'  => array(
					'autoplay' => 'yes',
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
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'name_navigation_heading',
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
				'label'                 => __( 'Direction', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'left',
				'options'               => [
					'auto'  => __( 'Auto', 'powerpack' ),
					'left'  => __( 'Left', 'powerpack' ),
					'right' => __( 'Right', 'powerpack' ),
				],
				'separator'             => 'before',
			)
		);

		$this->end_controls_section();

		$help_docs = PP_Config::get_widget_help_links( 'Team_Member_Carousel' );
		if ( ! empty( $help_docs ) ) {
			/**
			 * Content Tab: Docs Links
			 *
			 * @since 2.4.1
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

		/*-----------------------------------------------------------------------------------*/
		/* STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Box Style
		 */
		$this->start_controls_section(
			'section_member_box_style',
			array(
				'label' => __( 'Box Style', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'member_box_alignment',
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
					'{{WRAPPER}} .pp-tm' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_box_style' );

		$this->start_controls_tab(
			'tab_box_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'member_container_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-tm',
			)
		);

		$this->add_control(
			'member_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_container_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_box_active',
			array(
				'label' => __( 'Active', 'powerpack' ),
			)
		);

		$this->add_control(
			'member_container_border_color_active',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .swiper-slide-active .pp-tm' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Content
		 */
		$this->start_controls_section(
			'section_member_content_style',
			array(
				'label' => __( 'Content', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'member_box_bg_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-content-normal' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'member_box_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'separator'   => 'before',
				'selector'    => '{{WRAPPER}} .pp-tm-content-normal',
			)
		);

		$this->add_control(
			'member_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm-content-normal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_box_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm-content-normal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pa_member_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-tm-content-normal',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Overlay
		 */
		$this->start_controls_section(
			'section_member_overlay_style',
			array(
				'label'     => __( 'Overlay', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'overlay_content!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'overlay_alignment',
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
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-overlay-content-wrap' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'overlay_content!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'overlay_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .pp-tm-overlay-content-wrap:before',
				'condition' => array(
					'overlay_content!' => 'none',
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Opacity', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-overlay-content-wrap:before' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'overlay_content!' => 'none',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_member_image_style',
			array(
				'label' => __( 'Image', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'member_image_width',
			array(
				'label'          => __( 'Image Width', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%', 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 1200,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-image img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'member_image_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-tm-image img',
			)
		);

		$this->add_control(
			'member_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm-image img, {{WRAPPER}} .pp-tm-overlay-content-wrap:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_image_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Name
		 */
		$this->start_controls_section(
			'section_member_name_style',
			array(
				'label' => __( 'Name', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'member_name_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .pp-tm-name',
			)
		);

		$this->add_control(
			'member_name_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'member_name_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'name_divider_heading',
			array(
				'label'     => __( 'Divider', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'name_divider_color',
			array(
				'label'     => __( 'Divider Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-title-divider' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'name_divider_style',
			array(
				'label'     => __( 'Divider Style', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => __( 'Solid', 'powerpack' ),
					'dotted' => __( 'Dotted', 'powerpack' ),
					'dashed' => __( 'Dashed', 'powerpack' ),
					'double' => __( 'Double', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-title-divider' => 'border-bottom-style: {{VALUE}}',
				),
				'condition' => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'name_divider_width',
			array(
				'label'          => __( 'Divider Width', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 100,
					'unit' => 'px',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'max' => 800,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-title-divider' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'name_divider_height',
			array(
				'label'          => __( 'Divider Height', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 4,
				),
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 20,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-title-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'name_divider_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-title-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_title_divider' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Position
		 */
		$this->start_controls_section(
			'section_member_position_style',
			array(
				'label' => __( 'Position', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'member_position_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_2,
				'selector' => '{{WRAPPER}} .pp-tm-position',
			)
		);

		$this->add_control(
			'member_position_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-position' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'member_position_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'position_divider_heading',
			array(
				'label'     => __( 'Divider', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'position_divider_color',
			array(
				'label'     => __( 'Divider Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-position-divider' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'position_divider_style',
			array(
				'label'     => __( 'Divider Style', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => __( 'Solid', 'powerpack' ),
					'dotted' => __( 'Dotted', 'powerpack' ),
					'dashed' => __( 'Dashed', 'powerpack' ),
					'double' => __( 'Double', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-position-divider' => 'border-bottom-style: {{VALUE}}',
				),
				'condition' => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'position_divider_width',
			array(
				'label'          => __( 'Divider Width', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 100,
					'unit' => 'px',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'max' => 800,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-position-divider' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'position_divider_height',
			array(
				'label'          => __( 'Divider Height', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 4,
				),
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 20,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-position-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'position_divider_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-position-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_position_divider' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Description
		 */
		$this->start_controls_section(
			'section_member_description_style',
			array(
				'label' => __( 'Description', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'member_description_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .pp-tm-description',
			)
		);

		$this->add_control(
			'member_description_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-description' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'member_description_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'description_divider_heading',
			array(
				'label'     => __( 'Divider', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'description_divider_color',
			array(
				'label'     => __( 'Divider Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-description-divider' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->add_control(
			'description_divider_style',
			array(
				'label'     => __( 'Divider Style', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => __( 'Solid', 'powerpack' ),
					'dotted' => __( 'Dotted', 'powerpack' ),
					'dashed' => __( 'Dashed', 'powerpack' ),
					'double' => __( 'Double', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-description-divider' => 'border-bottom-style: {{VALUE}}',
				),
				'condition' => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'description_divider_width',
			array(
				'label'          => __( 'Divider Width', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 100,
					'unit' => 'px',
				),
				'size_units'     => array( 'px', '%' ),
				'range'          => array(
					'px' => array(
						'max' => 800,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-description-divider' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'description_divider_height',
			array(
				'label'          => __( 'Divider Height', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 4,
				),
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 20,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-description-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'description_divider_margin',
			array(
				'label'          => __( 'Margin Bottom', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 10,
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
					'{{WRAPPER}} .pp-tm-description-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'member_description_divider' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Social Icons
		 */
		$this->start_controls_section(
			'section_member_social_links_style',
			array(
				'label' => __( 'Social Icons', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'member_icons_gap',
			array(
				'label'          => __( 'Icons Gap', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array( 'size' => 10 ),
				'size_units'     => array( '%', 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 60,
					),
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-social-links li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_icon_size',
			array(
				'label'          => __( 'Icon Size', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'max' => 30,
					),
				),
				'default'        => array(
					'size' => '14',
					'unit' => 'px',
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_links_style' );

		$this->start_controls_tab(
			'tab_links_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'member_links_icons_color',
			array(
				'label'     => __( 'Icons Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'member_links_bg_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'member_links_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'separator'   => 'before',
				'selector'    => '{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap',
			)
		);

		$this->add_control(
			'member_links_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'member_links_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'member_links_icons_color_hover',
			array(
				'label'     => __( 'Icons Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap:hover .pp-tm-social-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'member_links_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'member_links_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-tm-social-links .pp-tm-social-icon-wrap:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Arrows
		 */
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_arrow',
			array(
				'label'                  => __( 'Choose Arrow', 'powerpack' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'arrow',
				'label_block'            => false,
				'default'                => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => 'svg',
				'recommended'            => array(
					'fa-regular' => array(
						'arrow-alt-circle-right',
						'caret-square-right',
						'hand-point-right',
					),
					'fa-solid'   => array(
						'angle-right',
						'angle-double-right',
						'chevron-right',
						'chevron-circle-right',
						'arrow-right',
						'long-arrow-alt-right',
						'caret-right',
						'caret-square-right',
						'arrow-circle-right',
						'arrow-alt-circle-right',
						'toggle-right',
						'hand-point-right',
					),
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'arrows_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Dots
		 */
		$this->start_controls_section(
			'section_dots_style',
			array(
				'label'     => __( 'Pagination: Dots', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_control(
			'dots_position',
			array(
				'label'     => __( 'Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'inside'  => __( 'Inside', 'powerpack' ),
					'outside' => __( 'Outside', 'powerpack' ),
				),
				'default'   => 'outside',
				'condition' => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'background: {{VALUE}};',
				),
				'condition' => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				),
				'condition' => array(
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
				'selector'    => '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet',
				'condition'   => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullets' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'dots'            => 'yes',
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Pagination: Fraction
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_fraction_style',
			array(
				'label'     => __( 'Pagination: Fraction', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
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
					'dots'            => 'yes',
					'pagination_type' => 'fraction',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image    = $this->get_settings( 'member_image' );

		$this->add_render_attribute( 'team-member-carousel-wrap', 'class', [
			'swiper-container-wrap',
			'pp-team-member-carousel-wrap',
		] );

		if ( $settings['dots_position'] ) {
			$this->add_render_attribute( 'team-member-carousel-wrap', 'class', 'swiper-container-wrap-dots-' . $settings['dots_position'] );
		}

		$this->add_render_attribute(
			'team-member-carousel',
			array(
				'class' => array( 'pp-tm-wrapper', 'pp-tm-carousel', 'pp-swiper-slider', 'swiper-container' ),
				'id'    => 'swiper-container-' . esc_attr( $this->get_id() ),
			)
		);

		if ( 'auto' === $settings['direction'] ) {
			if ( is_rtl() ) {
				$this->add_render_attribute( 'team-member-carousel', 'dir', 'rtl' );
			}
		} else {
			if ( 'right' === $settings['direction'] ) {
				$this->add_render_attribute( 'team-member-carousel', 'dir', 'rtl' );
			}
		}

		$slider_options = $this->get_swiper_slider_settings( $settings, false );

		$this->add_render_attribute(
			'team-member-carousel',
			array(
				'data-slider-settings' => wp_json_encode( $slider_options ),
			)
		);
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'team-member-carousel-wrap' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'team-member-carousel' ) ); ?>>
				<div class="swiper-wrapper">
					<?php foreach ( $settings['team_member_details'] as $index => $item ) : ?>
						<div class="swiper-slide">
							<div class="pp-tm">
								<div class="pp-tm-image"> 
									<?php
									if ( $item['team_member_image']['url'] ) {
										$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['team_member_image']['id'], 'thumbnail', $settings );

										if ( $image_url ) {
											$image_html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['team_member_image'] ) ) . '">';
										} else {
											$image_html = '<img src="' . esc_url( $item['team_member_image']['url'] ) . '">';
										}

										if ( 'image' === $item['link_type'] && $item['link']['url'] ) {

											$link_key = $this->get_repeater_setting_key( 'link', 'team_member_image', $index );

											$this->add_link_attributes( $link_key, $item['link'] );
											?>
											<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
												<?php echo wp_kses_post( $image_html ); ?>
											</a>
											<?php
										} else {
											echo wp_kses_post( $image_html );
										}
									}
									?>

									<?php if ( 'none' !== $settings['overlay_content'] ) { ?>
										<div class="pp-tm-overlay-content-wrap">
											<div class="pp-tm-content">
												<?php
												if ( 'yes' === $settings['member_social_links'] ) {
													if ( 'social_icons' === $settings['overlay_content'] ) {
														$this->member_social_links( $item );
													} elseif ( 'all_content' === $settings['overlay_content'] ) {
														if ( 'before_desc' === $settings['social_links_position'] ) {
															$this->member_social_links( $item );
														}
													}
												}

												if ( 'content' === $settings['overlay_content'] || 'all_content' === $settings['overlay_content'] ) {
													$this->render_description( $item );
												}

												if ( 'yes' === $settings['member_social_links'] && 'all_content' === $settings['overlay_content'] ) {
													if ( 'after_desc' === $settings['social_links_position'] ) {
														$this->member_social_links( $item );
													}
												}
												?>
											</div>
										</div>
									<?php } ?>
								</div>
								<div class="pp-tm-content pp-tm-content-normal">
									<?php
									$this->render_name( $item, $index );

									$this->render_position( $item );

									if ( 'yes' === $settings['member_social_links'] && ( 'none' === $settings['overlay_content'] || 'content' === $settings['overlay_content'] ) ) {
										if ( 'none' === $settings['overlay_content'] ) {
											if ( 'before_desc' === $settings['social_links_position'] ) {
												$this->member_social_links( $item );
											}
										} else {
											$this->member_social_links( $item );
										}
									}

									if ( 'none' === $settings['overlay_content'] || 'social_icons' === $settings['overlay_content'] ) {
										$this->render_description( $item );
									}

									if ( 'yes' === $settings['member_social_links'] && ( 'none' === $settings['overlay_content'] || 'content' === $settings['overlay_content'] ) ) {
										if ( 'after_desc' === $settings['social_links_position'] && 'none' === $settings['overlay_content'] ) {
											$this->member_social_links( $item );
										}
									}
									?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
				$this->render_dots();

				$this->render_arrows();
			?>
		</div>
		<?php
	}

	protected function render_name( $item, $index ) {
		$settings = $this->get_settings_for_display();

		if ( ! $item['team_member_name'] ) {
			return;
		}

		$member_key = $this->get_repeater_setting_key( 'team_member_name', 'team_member_details', $index );
		$link_key   = $this->get_repeater_setting_key( 'link', 'team_member_details', $index );

		$this->add_render_attribute( $member_key, 'class', 'pp-tm-name' );

		$member_name = $item['team_member_name'];

		if ( 'title' === $item['link_type'] && ! empty( $item['link']['url'] ) ) {
			$this->add_link_attributes( $link_key, $item['link'] );

			$member_name = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $member_name . '</a>';
		}

		$name_html_tag = PP_Helper::validate_html_tag( $settings['name_html_tag'] );
		?>
		<<?php echo esc_html( $name_html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $member_key ) ); ?>>
			<?php echo wp_kses_post( $member_name ); ?>
		</<?php echo esc_html( $name_html_tag ); ?>>

		<?php if ( 'yes' === $settings['member_title_divider'] ) { ?>
			<div class="pp-tm-title-divider-wrap">
				<div class="pp-tm-divider pp-tm-title-divider"></div>
			</div>
			<?php
		}
	}

	protected function render_position( $item ) {
		$settings = $this->get_settings_for_display();

		if ( $item['team_member_position'] ) {
			$position_html_tag = PP_Helper::validate_html_tag( $settings['position_html_tag'] );
			?>
			<<?php echo esc_html( $position_html_tag ); ?> class="pp-tm-position">
				<?php echo wp_kses_post( $item['team_member_position'] ); ?>
			</<?php echo esc_html( $position_html_tag ); ?>>
			<?php
		}
		?>
		<?php if ( 'yes' === $settings['member_position_divider'] ) { ?>
			<div class="pp-tm-position-divider-wrap">
				<div class="pp-tm-divider pp-tm-position-divider"></div>
			</div>
			<?php
		}
	}

	protected function render_description( $item ) {
		$settings = $this->get_settings_for_display();
		if ( $item['team_member_description'] ) {
			?>
			<div class="pp-tm-description">
				<?php echo $this->parse_text_editor( $item['team_member_description'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php } ?>
		<?php if ( 'yes' === $settings['member_description_divider'] ) { ?>
			<div class="pp-tm-description-divider-wrap">
				<div class="pp-tm-divider pp-tm-description-divider"></div>
			</div>
			<?php
		}
	}

	private function member_social_links( $item ) {
		$social_links = array();

		( $item['facebook_url'] ) ? $social_links['facebook']   = $item['facebook_url'] : '';
		( $item['twitter_url'] ) ? $social_links['twitter']     = $item['twitter_url'] : '';
		( $item['instagram_url'] ) ? $social_links['instagram'] = $item['instagram_url'] : '';
		( $item['linkedin_url'] ) ? $social_links['linkedin']   = $item['linkedin_url'] : '';
		( $item['youtube_url'] ) ? $social_links['youtube']     = $item['youtube_url'] : '';
		( $item['pinterest_url'] ) ? $social_links['pinterest'] = $item['pinterest_url'] : '';
		( $item['dribbble_url'] ) ? $social_links['dribbble']   = $item['dribbble_url'] : '';
		( $item['email'] ) ? $social_links['envelope']          = $item['email'] : '';
		( $item['phone'] ) ? $social_links['phone-alt']         = $item['phone'] : '';
		?>
		<div class="pp-tm-social-links-wrap">
			<ul class="pp-tm-social-links">
				<?php
				foreach ( $social_links as $icon_id => $icon_url ) {
					if ( $icon_url ) {
						if ( 'envelope' === $icon_id ) {
							?>
							<li>
								<a href="mailto:<?php echo sanitize_email( $icon_url ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
									<span class="pp-tm-social-icon-wrap">
										<span class="pp-tm-social-icon far fa fa-<?php echo esc_attr( $icon_id ); ?>"></span>
									</span>
								</a>
							</li>
							<?php
						} elseif ( 'phone-alt' === $icon_id ) {
							?>
							<li>
								<a href="tel:<?php echo esc_attr( $icon_url ); ?>">
									<span class="pp-tm-social-icon-wrap">
										<span class="pp-tm-social-icon fas fa fa-<?php echo esc_attr( $icon_id ); ?> fa-phone"></span>
									</span>
								</a>
							</li>
							<?php
						} else {
							?>
							<li>
								<a href="<?php echo esc_url( $icon_url ); ?>">
									<span class="pp-tm-social-icon-wrap">
										<span class="pp-tm-social-icon fab fa fa-<?php echo esc_attr( $icon_id ); ?>"></span>
									</span>
								</a>
							</li>
							<?php
						}
					}
				}
				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render team member carousel dots output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_dots() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['dots'] ) {
			?>
			<!-- Add Pagination -->
			<div class="swiper-pagination swiper-pagination-<?php echo esc_attr( $this->get_id() ); ?>"></div>
			<?php
		}
	}

	/**
	 * Render team member carousel arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_arrows() {
		PP_Helper::render_arrows( $this );
	}

	/**
	 * Render team member carousel widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.4.1
	 * @access protected
	 */
	protected function content_template() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$elementor_bp_tablet = get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile = get_option( 'elementor_viewport_md' );
		$elementor_bp_lg     = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md     = get_option( 'elementor_viewport_md' );
		$bp_desktop          = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet           = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile           = 320;
		$this->get_swiper_slider_settings_js();
		?>
		<#
			var i = 1;

			function member_social_links_template( item ) {
				var facebook_url       = item.facebook_url,
					twitter_url        = item.twitter_url,
					linkedin_url       = item.linkedin_url,
					instagram_url      = item.instagram_url,
					youtube_url        = item.youtube_url,
					pinterest_url      = item.pinterest_url,
					dribbble_url       = item.dribbble_url;
					email              = item.email;
					phone              = item.phone;
				#>
				<div class="pp-tm-social-links-wrap">
					<ul class="pp-tm-social-links">
						<# if ( facebook_url ) { #>
							<li>
								<a href="{{ facebook_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-facebook"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( twitter_url ) { #>
							<li>
								<a href="{{ twitter_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-twitter"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( instagram_url ) { #>
							<li>
								<a href="{{ instagram_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-instagram"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( linkedin_url ) { #>
							<li>
								<a href="{{ linkedin_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-linkedin"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( youtube_url ) { #>
							<li>
								<a href="{{ youtube_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-youtube"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( pinterest_url ) { #>
							<li>
								<a href="{{ pinterest_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-pinterest"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( dribbble_url ) { #>
							<li>
								<a href="{{ dribbble_url }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fab fa fa-dribbble"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( email ) { #>
							<li>
								<a href="mailto:{{ email }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon far fa fa-envelope"></span></span>
								</a>
							</li>
						<# } #>
						<# if ( phone ) { #>
							<li>
								<a href="tel:{{ phone }}">
									<span class="pp-tm-social-icon-wrap"><span class="pp-tm-social-icon fas fa fa-phone-alt fa-phone"></span></span>
								</a>
							</li>
						<# } #>
					</ul>
				</div>
				<#
			}

			function name_template( item ) {
				if ( item.team_member_name != '' ) {
					var name = item.team_member_name;

					view.addRenderAttribute( 'team_member_name', 'class', 'pp-tm-name' );

					if ( item.link_type == 'title' && item.link.url != '' ) {

						var target = item.link.is_external ? ' target="_blank"' : '';
						var nofollow = item.link.nofollow ? ' rel="nofollow"' : '';
				   
						var name = '<a href="' + item.link.url + '" ' + target + '>' + name + '</a>';
					}
				   
					var name_html = '<' + settings.name_html_tag  + ' ' + view.getRenderAttributeString( 'team_member_name' ) + '>' + name + '</' + settings.name_html_tag + '>';
				   
					print(name_html);
				}
						   
				if ( settings.member_title_divider == 'yes' ) {
					#>
					<div class="pp-tm-title-divider-wrap">
						<div class="pp-tm-divider pp-tm-title-divider"></div>
					</div>
					<#
				}
			}

			function position_template( item ) {
				if ( item.team_member_position != '' ) {
					var position = item.team_member_position;

					view.addRenderAttribute( 'team_member_position', 'class', 'pp-tm-position' );

					var position_html = '<' + settings.position_html_tag  + ' ' + view.getRenderAttributeString( 'team_member_position' ) + '>' + position + '</' + settings.position_html_tag + '>';

					print( position_html );
				}
				if ( settings.member_position_divider == 'yes' ) {
					#>
					<div class="pp-tm-position-divider-wrap">
						<div class="pp-tm-divider pp-tm-position-divider"></div>
					</div>
					<#
				}
			}

			function description_template( item ) {
				if ( item.team_member_description != '' ) {
					var description = item.team_member_description;

					view.addRenderAttribute( 'team_member_description', 'class', 'pp-tm-description' );

					var description_html = '<div' + ' ' + view.getRenderAttributeString( 'team_member_description' ) + '>' + description + '</div>';

					print( description_html );
				}
		   
				if ( settings.member_description_divider == 'yes' ) {
					#>
					<div class="pp-tm-description-divider-wrap">
						<div class="pp-tm-divider pp-tm-description-divider"></div>
					</div>
					<#
				}
			}

			function dots_template() {
				if ( settings.dots == 'yes' ) {
					#>
					<div class="swiper-pagination"></div>
					<#
				}
			}

			function arrows_template() {
				var arrowIconHTML = elementor.helpers.renderIcon( view, settings.select_arrow, { 'aria-hidden': true }, 'i' , 'object' ),
					arrowMigrated = elementor.helpers.isIconMigrated( settings, 'select_arrow' );

				if ( settings.arrows == 'yes' ) {
					if ( settings.arrow || settings.select_arrow.value ) {
						if ( arrowIconHTML && arrowIconHTML.rendered && ( ! settings.arrow || arrowMigrated ) ) {
							var pp_next_arrow = settings.select_arrow.value;
							var pp_prev_arrow = pp_next_arrow.replace('right', "left");
						} else if ( settings.arrow != '' ) {
							var pp_next_arrow = settings.arrow;
							var pp_prev_arrow = pp_next_arrow.replace('right', "left");
						}
						else {
							var pp_next_arrow = 'fa fa-angle-right';
							var pp_prev_arrow = 'fa fa-angle-left';
						}
						#>
						<div class="swiper-button-next">
							<i class="{{ pp_next_arrow }}"></i>
						</div>
						<div class="swiper-button-prev">
							<i class="{{ pp_prev_arrow }}"></i>
						</div>
						<#
					}
				}
			}

			view.addRenderAttribute(
				'container',
				{
					'class': [ 'swiper-container', 'pp-tm-wrapper', 'pp-tm-carousel', 'pp-swiper-slider' ],
				}
			);

			if ( settings.direction == 'auto' ) {
				#>
				<?php if ( is_rtl() ) { ?>
					<# view.addRenderAttribute( 'container', 'dir', 'rtl' ); #>
				<?php } ?>
				<#
			} else {
				if ( settings.direction == 'right' ) {
					view.addRenderAttribute( 'container', 'dir', 'rtl' );
				}
			}

			var slider_options = get_slider_settings( settings );

			view.addRenderAttribute( 'container', 'data-slider-settings', JSON.stringify( slider_options ) );
		#>
		<div class="swiper-container-wrap pp-team-member-carousel-wrap swiper-container-wrap-dots-{{ settings.dots_position }}">
			<div {{{ view.getRenderAttributeString( 'container' ) }}}>
				<div class="swiper-wrapper">
					<# _.each( settings.team_member_details, function( item ) { #>
						<div class="swiper-slide">
							<div class="pp-tm">
								<div class="pp-tm-image">
									<#
										if ( item.team_member_image.url != '' ) {
									   
											var image = {
												id: item.team_member_image.id,
												url: item.team_member_image.url,
												size: settings.thumbnail_size,
												dimension: settings.thumbnail_custom_dimension,
												model: view.getEditModel()
											};

											var image_url = elementor.imagesManager.getImageUrl( image );
									   
											if ( item.link_type == 'image' && item.link.url != '' ) {
									   
												var target = item.link.is_external ? ' target="_blank"' : '';
												var nofollow = item.link.nofollow ? ' rel="nofollow"' : '';
												#>
												<a href="{{ item.link.url }}"{{ target }}{{ nofollow }}>
													<img src="{{{ image_url }}}" />
												</a>
												<#
											} else {
												#>
												<img src="{{{ image_url }}}" />
												<#
											}
										}
									#>

									<# if ( settings.overlay_content != 'none' ) { #>
										<div class="pp-tm-overlay-content-wrap">
											<div class="pp-tm-content">
												<#
													if ( settings.member_social_links == 'yes' ) {
														if ( settings.overlay_content == 'social_icons' ) {
															member_social_links_template( item );
														} else if ( settings.overlay_content == 'all_content' ) {
															if ( settings.social_links_position == 'before_desc' ) {
																member_social_links_template( item );
															}
														}
													}
												   
													if ( settings.overlay_content == 'content' || settings.overlay_content == 'all_content' ) {
														description_template( item );
													}
												   
													if ( settings.member_social_links == 'yes' && settings.overlay_content == 'all_content' ) {
														if ( settings.social_links_position == 'after_desc' ) {
															member_social_links_template( item );
														}
													}
												#>
											</div>
										</div>
									<# } #>
								</div>
								<div class="pp-tm-content pp-tm-content-normal">
									<#
										name_template( item );
										position_template( item );

										if ( settings.member_social_links == 'yes' && ( settings.overlay_content == 'none' || settings.overlay_content == 'content' ) ) {
											if ( settings.overlay_content == 'none' ) {
												if ( settings.social_links_position == 'before_desc' ) {
													member_social_links_template( item );
												}
											} else {
												member_social_links_template( item );
											}
										}

										if ( settings.overlay_content == 'none' || settings.overlay_content == 'social_icons' ) {
											description_template( item );
										}

										if ( settings.member_social_links == 'yes' && ( settings.overlay_content == 'none' || settings.overlay_content == 'content' ) ) {
											if ( settings.social_links_position == 'after_desc' && settings.overlay_content == 'none' ) {
												member_social_links_template( item );
											}
										}
									#>
								</div>
							</div>
						</div>
					<# i++ } ); #>
				</div>
			</div>
			<# dots_template(); #>
			<# arrows_template(); #>
		</div>    
		<?php
	}
}
