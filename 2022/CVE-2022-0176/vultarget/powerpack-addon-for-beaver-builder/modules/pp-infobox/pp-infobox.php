<?php

/**
 * @class PPInfoBoxModule
 */
class PPInfoBoxModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Info Box', 'bb-powerpack-lite'),
			'description'   => __('Addon to display info box.', 'bb-powerpack-lite'),
			'group'         => pp_get_modules_group(),
			'category'		=> pp_get_modules_cat( 'content' ),
			'dir'           => BB_POWERPACK_DIR . 'modules/pp-infobox/',
			'url'           => BB_POWERPACK_URL . 'modules/pp-infobox/',
			'editor_export' => true, // Defaults to true and can be omitted.
			'enabled'       => true, // Defaults to true and can be omitted.
			'partial_refresh'   => true,
		));
		
		$this->add_css( BB_POWERPACK_LITE()->fa_css );
	}
	
	public function filter_settings( $settings, $helper )
	{
		// Handle old link, link_target, link_nofollow fields.
		$settings = PP_Module_Fields::handle_link_field( $settings, array(
			'link'			=> array(
				'type'			=> 'link'
			),
			'link_target'	=> array(
				'type'			=> 'target'
			),
			'link_nofollow'	=> array(
				'type'			=> 'nofollow'
			)
		), 'link' );

		// Handle button old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'button_padding', 'padding' );

		// Handle button old margin field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'read_more_margin', 'margin' );

		// Handle old button border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'button_border_custom'	=> array(
				'type'		=> 'width',
				'condition'	=> ( isset( $settings->button_border ) && 'custom' == $settings->button_border )
			),
			'button_border_color'	=> array(
				'type'		=> 'color',
				'condition'	=> ( isset( $settings->button_border ) && 'custom' == $settings->button_border )
			),
			'button_border_style'	=> array(
				'type'		=> 'style',
				'condition'	=> ( isset( $settings->button_border ) && 'custom' == $settings->button_border )
			),
			'button_radius'	=> array(
				'type'			=> 'radius'
			)
		), 'button_border_setting' );

		if ( isset( $settings->button_border ) ) {
			unset( $settings->button_border );
		}

		// Handle box old border fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'box_border_style'	=> array(
				'type'				=> 'style'
			),
			'box_border_width'	=> array(
				'type'				=> 'width'
			),
			'box_border_color'	=> array(
				'type'				=> 'color'
			),
			'box_border_radius'	=> array(
				'type'				=> 'radius'
			)
		), 'box_border' );

		// Handle box old alignment_responsive field.
		$settings = PP_Module_Fields::handle_alignment_field( $settings, 'alignment', 'alignment_responsive' );

		// Handle icon old border fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'icon_border_width'		=> array(
				'type'			=> 'width',
				'condition'		=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border )
			),
			'icon_border_color'		=> array(
				'type'			=> 'color',
				'condition'		=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border )
			),
			'icon_border_style'		=> array(
				'type'			=> 'style',
				'condition'		=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border )
			),
			'icon_border_radius'	=> array(
				'type'			=> 'radius'
			)
		), 'icon_border' );

		// Handle old typography fields - Title Prefix.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_prefix_font'	=> array(
				'type'				=> 'font'
			),
			'title_prefix_font_size'	=> array(
				'type'				=> 'font_size'
			),
			'title_prefix_line_height'	=> array(
				'type'				=> 'line_height'
			)
		), 'title_prefix_typography' );

		// Handle old typography fields - Title.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font'	=> array(
				'type'				=> 'font'
			),
			'title_font_size'	=> array(
				'type'				=> 'font_size'
			),
			'title_line_height'	=> array(
				'type'				=> 'line_height'
			)
		), 'title_typography' );

		// Handle old typography fields - Description.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'text_font'	=> array(
				'type'				=> 'font'
			),
			'text_font_size'	=> array(
				'type'				=> 'font_size'
			),
			'text_line_height'	=> array(
				'type'				=> 'line_height'
			)
		), 'desc_typography' );

		// Handle old typography fields - Button.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'pp_infobox_read_more_font'	=> array(
				'type'				=> 'font'
			),
			'pp_infobox_read_more_font_size'	=> array(
				'type'				=> 'font_size'
			),
		), 'button_typography' );

		return $settings;
	}

	public function render_link()
	{
		$settings 		= $this->settings;
		$button_class 	= ( 'button' == $settings->pp_infobox_link_type && '' != $settings->link_css_class ) ? ' ' . $settings->link_css_class : '';
		$nofollow		= ( isset( $settings->link_nofollow ) && 'yes' == $settings->link_nofollow ) ? ' rel="nofollow"' : '';

		if ( 'button' == $settings->pp_infobox_link_type ) {
			?>
			<div class="pp-infobox-button pp-button-wrap">
				<a class="pp-more-link pp-button<?php echo $button_class; ?>" href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>"<?php echo $nofollow; ?>>
					<?php if ( isset( $settings->button_icon ) && 'left' === $settings->button_icon_aligment ) { ?>
						<i class="pp-button-icon pp-button-icon-left <?php echo $settings->button_icon; ?>"></i>
					<?php } ?>
					<?php echo $settings->pp_infobox_read_more_text; ?>
					<?php if ( isset( $settings->button_icon ) && 'right' === $settings->button_icon_aligment ) { ?>
						<i class="pp-button-icon pp-button-icon-right <?php echo $settings->button_icon; ?>"></i>
					<?php } ?>
				</a>
			</div>
			<?php
		}
		if ( 'read_more' == $settings->pp_infobox_link_type ) {
			?>
			<div class="pp-infobox-button">
				<p>
					<a class="pp-more-link<?php echo $button_class; ?>" href="<?php echo $settings->link; ?>" target="<?php echo $settings->link_target; ?>"<?php echo $nofollow; ?>>
						<?php echo $settings->pp_infobox_read_more_text; ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * @method get_alt
	 */
	public function get_alt()
	{
		$photo = FLBuilderPhoto::get_attachment_data($this->settings->image_select);

		if(!empty($photo->alt)) {
			return htmlspecialchars($photo->alt);
		}
		else if(!empty($photo->description)) {
			return htmlspecialchars($photo->description);
		}
		else if(!empty($photo->caption)) {
			return htmlspecialchars($photo->caption);
		}
		else if(!empty($photo->title)) {
			return htmlspecialchars($photo->title);
		}
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPInfoBoxModule', array(
	'general'      => array( // Tab
		'title'         => __('General', 'bb-powerpack-lite'), // Tab title
		'sections'      => array( // Tab Sections
			'layout'        => array(
				'title'         => '',
				'fields'        => array(
					'layouts'       => array(
						'type'      => 'select',
						'label'     => __('Layout', 'bb-powerpack-lite'),
						'default'   => '5',
						'options'   => array(
							'0'         => __('No Icon', 'bb-powerpack-lite'),
							'5'         => __('Icon at Top', 'bb-powerpack-lite'),
							'3'         => __('Icon at Left', 'bb-powerpack-lite'),
							'4'         => __('Icon at Right', 'bb-powerpack-lite'),
							'1'         => __('Icon at Left with Title', 'bb-powerpack-lite'),
							'2'         => __('Icon at Right with Title', 'bb-powerpack-lite'),
							'6'         => __('Icon below Title', 'bb-powerpack-lite'),
						),
						'toggle'        => array(
							'1'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
							),
							'2'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
							),
							'3'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
								'fields'        => array('space_bt_icon_text', 'icon_position')
							),
							'4'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
								'fields'        => array('space_bt_icon_text', 'icon_position')
							),
							'5'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
							),
							'6'             => array(
								'tabs'          => array('icon_styles'),
								'sections'      => array('type'),
							),
						)
					),
					'icon_position'	=> array(
						'type'			=> 'select',
						'label'			=> __('Align Icon Vertically', 'bb-powerpack-lite'),
						'default'		=> 'default',
						'options'		=> array(
							'default'		=> __('Default', 'bb-powerpack-lite'),
							'top'			=> __('Top', 'bb-powerpack-lite'),
							'center'		=> __('Center', 'bb-powerpack-lite'),
							'bottom'		=> __('Bottom', 'bb-powerpack-lite'),
						)
					)
				)
			),
			'type'      => array(
				'title'     => __('Icon', 'bb-powerpack-lite'),
				'fields'    => array(
					'icon_type'      => array(
						'type'      => 'select',
						'label'     => __('Icon Type', 'bb-powerpack-lite'),
						'default'   => 'icon',
						'options'   => array(
							'icon'      => __('Icon', 'bb-powerpack-lite'),
							'image'      => __('Image', 'bb-powerpack-lite'),
						),
						'toggle'        => array(
							'icon'      => array(
								'sections'      => array('icon_colors'),
								'fields'        => array('icon_select', 'icon_color', 'icon_font_size', 'icon_color_hover', 'icon_background', 'icon_background_hover', 'icon_width'),
							),
							'image'      => array(
								'fields'        => array('image_select', 'image_width_type'),
							),
						),
					),
					'icon_select'       => array(
						'type'      => 'icon',
						'label'     => __('Icon', 'bb-powerpack-lite'),
						'show_remove'   => true
					),
					'image_select'       => array(
						'type'      => 'photo',
						'label'     => __('Image Icon', 'bb-powerpack-lite'),
						'show_remove'   => true,
						'connections'   => array( 'photo' ),
					),
					'space_bt_icon_text'    => array(
						'type'                  => 'unit',
						'label'                 => __('Space between icon and text', 'bb-powerpack-lite'),
						'units'           		=> array('px'),
						'default'               => 10,
						'slider'				=> true,
						'preview'               => array(
							'type'                  => 'css',
							'rules'                 => array(
								array(
									'selector'              => '.pp-infobox-wrap .layout-3 .pp-icon-wrapper',
									'property'              => 'margin-right',
									'unit'                  => 'px'
								),
								array(
									'selector'              => '.pp-infobox-wrap .layout-4 .pp-icon-wrapper',
									'property'              => 'margin-left',
									'unit'                  => 'px'
								)
							)
						)
					),
					'icon_animation'     => array(
						'type'      => 'select',
						'label'     => __('Animation', 'bb-powerpack-lite'),
						'default'     => 'none',
						'options'       => array(
							'none'          => __('None', 'bb-powerpack-lite'),
							'swing'          => __('Swing', 'bb-powerpack-lite'),
							'pulse'          => __('Pulse', 'bb-powerpack-lite'),
							'flash'          => __('Flash', 'bb-powerpack-lite'),
							'fadeIn'          => __('Fade In', 'bb-powerpack-lite'),
							'fadeInUp'          => __('Fade In Up', 'bb-powerpack-lite'),
							'fadeInDown'          => __('Fade In Down', 'bb-powerpack-lite'),
							'fadeInLeft'          => __('Fade In Left', 'bb-powerpack-lite'),
							'fadeInRight'          => __('Fade In Right', 'bb-powerpack-lite'),
							'slideInUp'          => __('Slide In Up', 'bb-powerpack-lite'),
							'slideInDown'          => __('Slide In Down', 'bb-powerpack-lite'),
							'slideInLeft'          => __('Slide In Left', 'bb-powerpack-lite'),
							'slideInRight'          => __('Slide In Right', 'bb-powerpack-lite'),
							'bounceIn'          => __('Bounce In', 'bb-powerpack-lite'),
							'bounceInUp'          => __('Bounce In Up', 'bb-powerpack-lite'),
							'bounceInDown'          => __('Bounce In Down', 'bb-powerpack-lite'),
							'bounceInLeft'          => __('Bounce In Left', 'bb-powerpack-lite'),
							'bounceInRight'          => __('Bounce In Right', 'bb-powerpack-lite'),
							'flipInX'          => __('Flip In X', 'bb-powerpack-lite'),
							'FlipInY'          => __('Flip In Y', 'bb-powerpack-lite'),
							'lightSpeedIn'          => __('Light Speed In', 'bb-powerpack-lite'),
							'rotateIn'          => __('Rotate In', 'bb-powerpack-lite'),
							'rotateInUpLeft'          => __('Rotate In Up Left', 'bb-powerpack-lite'),
							'rotateInUpRight'          => __('Rotate In Up Right', 'bb-powerpack-lite'),
							'rotateInDownLeft'          => __('Rotate In Down Left', 'bb-powerpack-lite'),
							'rotateInDownRight'          => __('Rotate In Down Right', 'bb-powerpack-lite'),
							'rollIn'          => __('Roll In', 'bb-powerpack-lite'),
							'zoomIn'          => __('Zoom In', 'bb-powerpack-lite'),
							'slideInUp'          => __('Slide In Up', 'bb-powerpack-lite'),
							'slideInDown'          => __('Slide In Down', 'bb-powerpack-lite'),
							'slideInLeft'          => __('Slide In Left', 'bb-powerpack-lite'),
							'slideInRight'          => __('Slide In Right', 'bb-powerpack-lite'),
						)
					),
					'animation_duration'    => array(
						'type'      => 'text',
						'label'     => __('Animation Duration', 'bb-powerpack-lite'),
						'default'     => '500',
						'maxlength'     => '4',
						'size'      => '5',
						'description'   => _x( 'ms', 'Value unit for animation duration. Such as: "1s"', 'bb-powerpack-lite' ),
						'preview'       => array(
							'type'      => 'css',
							'selector'  => '.animated',
							'property'  => 'animation-duration'
						),
					),
				),
			),
		)
	),
	'content'   => array(
		'title'     => __('Content', 'bb-powerpack-lite'),
		'sections'  => array(
			'title_prefix'     => array(
				'title'     => __('Title Prefix', 'bb-powerpack-lite'),
				'fields'    => array(
					'title_prefix'     => array(
						'type'      => 'text',
						'label'     => '',
						'connections'   => array( 'string', 'html', 'url' ),
					),
				),
			),
			'title'     => array(
				'title'     => __('Title', 'bb-powerpack-lite'),
				'fields'    => array(
					'title'     => array(
						'type'      => 'text',
						'label'     => '',
						'connections'   => array( 'string', 'html', 'url' ),
						'preview'       => array(
							'type'          => 'text',
							'selector'      => '.pp-infobox-title-wrapper .pp-infobox-title'
						)
					),
				),
			),
			'description'     => array(
				'title'     => __('Description', 'bb-powerpack-lite'),
				'fields'    => array(
					'description'     => array(
						'type'      	=> 'editor',
						'label'     	=> '',
						'default'     	=> '',
						'media_buttons' => true,
						'rows'      	=> 4,
						'connections'   => array( 'string', 'html', 'url' ),
						'preview'       => array(
							'type'          => 'text',
							'selector'      => '.pp-infobox-description .pp-description-wrap'
						)
					),
				),
			),
		)
	),
	'button_link'   => array(
		'title'         => __('Link', 'bb-powerpack-lite'),
		'sections'      => array(
			'pp_infobox_link_type'     => array(
				'collapsed'		=> false,
				'title'     => __('Link', 'bb-powerpack-lite'),
				'fields'    => array(
					'pp_infobox_link_type'      => array(
						'type'      => 'select',
						'label'     => __('Link Type', 'bb-powerpack-lite'),
						'default'   => 'none',
						'options'   => array(
							'none'      => __('None', 'bb-powerpack-lite'),
							'box'       => __('Complete Box', 'bb-powerpack-lite'),
							'title'     => __('Title Only', 'bb-powerpack-lite'),
							'read_more' => __('Text Link', 'bb-powerpack-lite'),
							'button'    => __('Button', 'bb-powerpack-lite'),
						),
						'toggle'    => array(
							'box'     => array(
								'fields'    => array('link')
							),
							'title'     => array(
								'fields'    => array('link')
							),
							'read_more'     => array(
								'fields'        => array('pp_infobox_read_more_text', 'link'),
								'sections'      => array('link_style', 'button_typography')
							),
							'button'     => array(
								'fields'        => array('pp_infobox_read_more_text', 'button_icon', 'button_icon_aligment', 'button_icon_spacing', 'link', 'link_css_class', 'button_bg_color', 'button_bg_hover_color', 'button_padding', 'button_radius', 'button_width', 'field_separator_1', 'field_separator_3', 'button_icon_color', 'button_icon_color_hover', 'button_icon_size'),
								'sections'      => array('link_style', 'button_border', 'button_typography')
							),
						)
					),
					'pp_infobox_read_more_text' => array(
						'type'      => 'text',
						'label'         => __('Text', 'bb-powerpack-lite'),
						'default'       => __('Read More', 'bb-powerpack-lite'),
						'preview'       => array(
							'type'      => 'text',
							'selector'  => '.pp-more-link'
						),
					),
					'button_icon'               => array(
						'type'          => 'icon',
						'label'         => __('Icon', 'bb-powerpack-lite')
					),
					'button_icon_aligment'             => array(
						'type'          => 'pp-switch',
						'label'         => __('Icon Position', 'bb-powerpack-lite'),
						'default'       => 'left',
						'options'       => array(
							'left'      => __('Before Text', 'bb-powerpack-lite'),
							'right'     => __('After Text', 'bb-powerpack-lite')
						),
					),
					'button_icon_spacing'              => array(
						'type'        => 'unit',
						'label'       => __('Spacing', 'bb-powerpack-lite'),
						'default'     => '10',
						'units'       => array( 'px' ),
						'slider'      => true,
					),
					'link'                      => array(
						'type'          => 'link',
						'label'         => __('Link', 'bb-powerpack-lite'),
						'placeholder'   => 'http://www.example.com',
						'show_target'	=> true,
						'show_nofollow'	=> true,
						'connections'   => array( 'url' ),
						'preview'       => array(
							'type'          => 'none'
						),
					),
					'link_css_class'            => array(
						'type'  => 'text',
						'label' => __('Custom CSS Class', 'bb-powerpack-lite'),
						'default'   => ''
					),
				),
			),
			'link_style'    => array(
				'collapsed'		=> true,
				'title'         => __('Style', 'bb-powerpack-lite'),
				'fields'        => array(
					'button_bg_color'                  => array(
						'type'      => 'color',
						'label'     => __('Background Color', 'bb-powerpack-lite'),
						'default'   => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'   => array(
							'type'      => 'css',
							'selector'  => '.pp-infobox .pp-more-link',
							'property'  => 'background-color'
						),
					),
					'button_bg_hover_color'            => array(
						'type'      => 'color',
						'label'     => __('Background Hover Color', 'bb-powerpack-lite'),
						'default'   => '',
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'   => array(
							'type'      => 'none',
						),
					),
					'field_separator_1'                => array(
						'type'      => 'pp-separator',
						'color'   => 'eeeeee'
					),
					'pp_infobox_read_more_color'       => array(
						'type'      => 'color',
						'label'     => __('Text Color', 'bb-powerpack-lite'),
						'default'   => '',
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'   => array(
							'type'  => 'css',
							'selector'  => '.pp-infobox .pp-more-link',
							'property'  => 'color'
						),
					),
					'pp_infobox_read_more_color_hover' => array(
						'type'      => 'color',
						'label'     => __('Text Hover Color', 'bb-powerpack-lite'),
						'default'   => '',
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'   => array(
							'type'  => 'css',
							'selector'  => '.pp-infobox .pp-more-link:hover',
							'property'  => 'color'
						),
					),
					'field_separator_2'                => array(
						'type'      => 'pp-separator',
						'color'     => 'eeeeee'
					),
					'button_padding'                   => array(
						'type'				=> 'dimension',
						'label'				=> __('Padding', 'bb-powerpack-lite'),
						'default'			=> '10',
						'units'				=> array('px'),
						'slider'			=> true,
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-infobox .pp-more-link',
							'property'			=> 'padding',
							'unit'				=> 'px'
						)
					),
					'read_more_margin'                 => array(
						'type'              => 'dimension',
						'label'             => __('Margin', 'bb-powerpack-lite'),
						'default'           => '',
						'units'				=> array('px'),
						'slider'			=> true,
						'responsive'		=> true,
						'preview'			=> array(
							'type'				=> 'css',
							'selector'			=> '.pp-infobox .pp-more-link',
							'property'			=> 'margin',
							'unit'				=> 'px'
						)
					),
					'field_separator_3'                => array(
						'type'      => 'pp-separator',
						'color'     => 'eeeeee'
					),
					'button_width'                     => array(
						'type'          => 'pp-switch',
						'label'         => __('Button Width', 'bb-powerpack-lite'),
						'default'       => 'auto',
						'options'       => array(
							'auto'          => __('Auto', 'bb-powerpack-lite'),
							'custom'        => __('Custom', 'bb-powerpack-lite'),
							'full'          => __('Full Width', 'bb-powerpack-lite')
						),
						'toggle'        => array(
							'custom'        => array(
								'fields'        => array('button_width_custom')
							)
						)
					),
					'button_width_custom'              => array(
						'type'                  => 'unit',
						'label'                 => __('Custom Width', 'bb-powerpack-lite'),
						'default'               => '',
						'units'					=> array('px'),
						'slider'           		=> true,
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-infobox .pp-more-link',
							'property'				=> 'width',
							'unit'					=> 'px'
						)
					),
					'button_icon_color'                => array(
						'type'        => 'color',
						'label'       => __('Icon Color', 'bb-powerpack-lite'),
						'default'     => '',
						'show_reset'  => true,
						'connections' => array('color'),
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.pp-button-icon',
							'property' => 'color'
						),
					),
					'button_icon_color_hover'          => array(
						'type'        => 'color',
						'label'       => __('Icon Hover Color', 'bb-powerpack-lite'),
						'default'     => '',
						'show_reset'  => true,
						'connections' => array('color'),
						'preview'     => array(
							'type'     => 'none',
						),
					),
					'button_icon_size'                 => array(
						'type'        => 'unit',
						'label'       => __('Icon Size', 'bb-powerpack-lite'),
						'default'     => '15',
						'units'       => array( 'px' ),
						'slider'      => true,
					),
				),
			),
			'button_border' => array(
				'collapsed'		=> true,
				'title'         => __('Border', 'bb-powerpack-lite'),
				'fields'        => array(
					'button_border_setting' => array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack-lite' ),
						'responsive'	=> true,
						'preview'       => array(
							'type'          => 'css',
							'selector'		=> '.pp-infobox .pp-more-link',
							'important'		=> true,
						),
					),
					'button_border_hover_color'   => array(
						'type'          => 'color',
						'label'         => __('Border Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'none',
						),
					)
				)
			)
		)
	),
	'box'       => array(
		'title'     => __('Style', 'bb-powerpack-lite'),
		'sections'  => array(
			'box_style'     => array(
				'collapsed'		=> false,
				'title'         => __('Box', 'bb-powerpack-lite'),
				'fields'        => array(
					'box_background'    => array(
						'type'              => 'color',
						'label'             => __('Background Color', 'bb-powerpack-lite'),
						'default'           => 'ffffff',
						'show_reset'        => true,
						'show_alpha'    	=> true,
						'connections'		=> array('color'),
						'preview'           => array(
							'type'              => 'css',
							'selector'          => '.pp-infobox',
							'property'          => 'background'
						),
					),
					'box_background_hover'    => array(
						'type'      => 'color',
						'label'     => __('Background Hover Color', 'bb-powerpack-lite'),
						'default'   => '',
						'show_reset'    => true,
						'show_alpha'    => true,
						'connections'	=> array('color'),
						'preview'   => array(
							'type'  => 'css',
							'selector'  => '.pp-infobox:hover',
							'property'  => 'background'
						),
					),
					'padding_top'   => array(
						'type'      	=> 'unit',
						'label'     	=> __('Top/Bottom Padding', 'bb-powerpack-lite'),
						'default'       => '20',
						'units'   		=> array('px'),
						'slider'		=> true,
						'preview'       => array(
							'type'          => 'css',
							'rules'     	=> array(
								array(
									'selector'      => '.pp-infobox',
									'property'      => 'padding-top',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox',
									'property'      => 'padding-bottom',
									'unit'          => 'px'
								),
							),
						)
					),
					'padding_left'   => array(
						'type'      	=> 'unit',
						'label'     	=> __('Left/Right Padding', 'bb-powerpack-lite'),
						'default'       => '20',
						'units'   		=> array('px'),
						'slider'		=> true,
						'preview'       => array(
							'type'          => 'css',
							'rules'     	=> array(
								array(
									'selector'      => '.pp-infobox',
									'property'      => 'padding-left',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox',
									'property'      => 'padding-right',
									'unit'          => 'px'
								),
							),
						)
					),
					'alignment' 	=> array(
						'type'      	=> 'align',
						'label'     	=> __('Content Alignment', 'bb-powerpack-lite'),
						'default'   	=> 'center',
						'responsive'	=> true
					),
					'box_border'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack-lite' ),
						'responsive'	=> true,
						'preview'   	=> array(
							'type'  		=> 'css',
							'selector'  	=> '.pp-infobox',
							'property'  	=> 'border',
						),
					),
				)
			),
			'title_prefix_style'   => array(
				'collapsed'		=> true,
				'title'         => __('Title Prefix', 'bb-powerpack-lite'),
				'fields'        => array(
					'title_prefix_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-title-prefix',
							'property'      => 'color',
						)
					),
					'title_prefix_margin'      => array(
						'type'              => 'pp-multitext',
						'label'             => __('Margin', 'bb-powerpack-lite'),
						'description'       => 'px',
						'default'           => array(
							'top'               => '',
							'bottom'            => '',
						),
						'options'           => array(
							'top'               => array(
								'placeholder'       => __('Top', 'bb-powerpack-lite'),
								'tooltip'           => __('Top', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-up',
								'preview'           => array(
									'selector'          => '.pp-infobox-title-prefix',
									'property'          => 'margin-top',
									'unit'              => 'px'
								),
							),
							'bottom'            => array(
								'placeholder'       => __('Bottom', 'bb-powerpack-lite'),
								'tooltip'           => __('Bottom', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-down',
								'preview'           => array(
									'selector'          => '.pp-infobox-title-prefix',
									'property'          => 'margin-bottom',
									'unit'              => 'px'
								),
							),
						)
					),
				)
			),
			'title_style'   => array(
				'collapsed'		=> true,
				'title'         => __('Title', 'bb-powerpack-lite'),
				'fields'        => array(
					'title_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-title-wrapper .pp-infobox-title',
							'property'      => 'color',
						)
					),
					'title_color_h'    => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'none',
						)
					),
					'title_margin'      => array(
						'type'              => 'pp-multitext',
						'label'             => __('Margin', 'bb-powerpack-lite'),
						'description'       => 'px',
						'default'           => array(
							'top'               => 10,
							'bottom'            => 10,
						),
						'options'           => array(
							'top'               => array(
								'placeholder'       => __('Top', 'bb-powerpack-lite'),
								'tooltip'           => __('Top', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-up',
								'preview'           => array(
									'selector'          => '.pp-infobox-title-wrapper .pp-infobox-title',
									'property'          => 'margin-top',
									'unit'              => 'px'
								),
							),
							'bottom'            => array(
								'placeholder'       => __('Bottom', 'bb-powerpack-lite'),
								'tooltip'           => __('Bottom', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-down',
								'preview'           => array(
									'selector'          => '.pp-infobox-title-wrapper .pp-infobox-title',
									'property'          => 'margin-bottom',
									'unit'              => 'px'
								),
							),
						)
					),
				)
			),
			'description_style' => array(
				'collapsed'		=> true,
				'title'             => __('Description', 'bb-powerpack-lite'),
				'fields'            => array(
					'text_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-description .pp-description-wrap',
							'property'      => 'color',
						)
					),
					'text_color_h'    => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'none',
						)
					),
					'text_margin'       => array(
						'type'              => 'pp-multitext',
						'label'             => __('Margin', 'bb-powerpack-lite'),
						'description'       => 'px',
						'default'           => array(
							'top'               => 0,
							'bottom'            => 0,
						),
						'options'           => array(
							'top'               => array(
								'placeholder'       => __('Top', 'bb-powerpack-lite'),
								'tooltip'           => __('Top', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-up',
								'preview'           => array(
									'selector'          => '.pp-infobox-description .pp-description-wrap',
									'property'          => 'margin-top',
									'unit'              => 'px'
								),
							),
							'bottom'            => array(
								'placeholder'       => __('Bottom', 'bb-powerpack-lite'),
								'tooltip'           => __('Bottom', 'bb-powerpack-lite'),
								'icon'              => 'fa-long-arrow-down',
								'preview'           => array(
									'selector'          => '.pp-infobox-description .pp-description-wrap',
									'property'          => 'margin-bottom',
									'unit'              => 'px'
								),
							),
						)
					),
				)
			),
		),
	),
	'icon_styles'   => array(
		'title'     => __('Icon Style', 'bb-powerpack-lite'),
		'sections'  => array(
			'icon_size'   => array(
				'collapsed'	=> false,
				'title'     => __('Size', 'bb-powerpack-lite'),
				'fields'    => array(
					'icon_font_size'    => array(
						'type'          => 'unit',
						'label'         => __('Icon Size', 'bb-powerpack-lite'),
						'default'       => '16',
						'units'   		=> array('px'),
						'slider'		=> true,
						'preview'       => array(
							'type'          => 'css',
							'rules'         => array(
								array(
									'selector'      => '.pp-infobox-icon-inner span.pp-icon',
									'property'      => 'font-size',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-icon-inner span.pp-icon:before',
									'property'      => 'font-size',
									'unit'          => 'px'
								),
							),
						)
					),
					'icon_width'    => array(
						'type'          => 'unit',
						'label'         => __('Icon Box Size', 'bb-powerpack-lite'),
						'default'       => '0',
						'units'   		=> array('px'),
						'slider'		=> true,
						'preview'       => array(
							'type'          => 'css',
							'rules'           => array(
								array(
									'selector'      => '.pp-infobox-icon-inner',
									'property'     => 'width',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-icon-inner',
									'property'     => 'height',
									'unit'          => 'px'
								),
							),
						)
					),
					'image_width_type'  => array(
						'type'              => 'pp-switch',
						'label'             => __('Image Icon Size', 'bb-powerpack-lite'),
						'default'           => 'custom',
						'options'           => array(
							'default'           => __('Default', 'bb-powerpack-lite'),
							'custom'            => __('Custom', 'bb-powerpack-lite'),
						),
						'toggle'            => array(
							'custom'            => array(
								'fields'            => array('image_width')
							)
						)
					),
					'image_width'    => array(
						'type'          => 'unit',
						'label'         => __('Image Icon Custom Size', 'bb-powerpack-lite'),
						'default'     	=> '100',
						'units'   		=> array('px'),
						'slider'		=> true,
						'preview'       => array(
							'type'          => 'css',
							'rules'           => array(
								array(
									'selector'      => '.pp-infobox-image',
									'property'     => 'width',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-image',
									'property'     => 'height',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-image img',
									'property'     => 'width',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-image img',
									'property'     => 'height',
									'unit'          => 'px'
								),
							),
						)
					),
				),
			),
			'icon_colors'   => array(
				'collapsed'		=> true,
				'title'         => __('Colors', 'bb-powerpack-lite'),
				'fields'        => array(
					'icon_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-icon-inner',
							'property'      => 'color',
						)
					),
					'icon_color_hover'    => array(
						'type'          => 'color',
						'label'         => __('Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-icon:hover-inner',
							'property'      => 'color',
						)
					),
					'icon_background'    => array(
						'type'          => 'color',
						'label'         => __('Background', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-icon-inner',
							'property'      => 'background',
						)
					),
					'icon_background_hover'    => array(
						'type'          => 'color',
						'label'         => __('Background Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'show_alpha'	=> true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.pp-infobox-icon-inner:hover',
							'property'      => 'background',
						)
					),
				)
			),
			'icon_border'   => array(
				'collapsed'		=> true,
				'title'         => __('Border', 'bb-powerpack-lite'),
				'fields'        => array(
					'icon_border'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack-lite' ),
						'responsive'	=> true,
					),
					'icon_border_color_hover'    => array(
						'type'          => 'color',
						'label'         => __('Border Hover Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
						'preview'       => array(
							'type'          => 'css',
							'rules'     => array(
								array(
									'selector'      => '.pp-infobox-icon:hover',
									'property'      => 'border-color',
								),
								array(
									'selector'      => '.pp-infobox-image img:hover',
									'property'      => 'border-color',
								),
							),
						)
					),
					'icon_box_size'     => array(
						'type'          	=> 'unit',
						'default'     		=> '0',
						'label'         	=> __('Inside Spacing', 'bb-powerpack-lite'),
						'units'   			=> array('px'),
						'slider'			=> true,
						'help'      		=> __('Space between icon and the border', 'bb-powerpack-lite'),
						'preview'       	=> array(
							'type'          	=> 'css',
							'rules'           	=> array(
								array(
									'selector'      => '.pp-infobox-image img',
									'property'     => 'padding',
									'unit'          => 'px'
								),
								array(
									'selector'      => '.pp-infobox-icon',
									'property'     => 'padding',
									'unit'          => 'px'
								),
							),
						)
					),
				)
			),
		),
	),
	'typography'      => array( // Tab
		'title'         => __('Typography', 'bb-powerpack-lite'), // Tab title
		'sections'      => array( // Tab Sections
			'title_prefix'     => array(
				'collapsed'	=> false,
				'title'     => __('Title Prefix', 'bb-powerpack-lite'),
				'fields'    => array(
					'title_prefix_tag' => array(
						'type'      => 'select',
						'label'     => __('HTML Tag', 'bb-powerpack-lite'),
						'default'   => 'span',
						'options'   => array(
							'h1'        => 'H1',
							'h2'        => 'H2',
							'h3'        => 'H3',
							'h4'        => 'H4',
							'h5'        => 'H5',
							'h6'        => 'H6',
							'p'         => 'p',
							'span'      => 'span'
						)
					),
					'title_prefix_typography'		=> array(
						'type'					=> 'typography',
						'label'					=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-infobox-title-prefix'
						)
					),
				),
			),
			'general'     => array(
				'collapsed'	=> true,
				'title'     => __('Title', 'bb-powerpack-lite'),
				'fields'    => array(
					'title_tag' => array(
						'type'      => 'select',
						'label'     => __('HTML Tag', 'bb-powerpack-lite'),
						'default'   => 'h4',
						'options'   => array(
							'h1'        => 'H1',
							'h2'        => 'H2',
							'h3'        => 'H3',
							'h4'        => 'H4',
							'h5'        => 'H5',
							'h6'        => 'H6',
							'p'         => 'p'
						)
					),
					'title_typography'		=> array(
						'type'					=> 'typography',
						'label'					=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-infobox-title-wrapper .pp-infobox-title'
						)
					),
				),
			),
			'text_typography'   => array(
				'collapsed'	=> true,
				'title'     => __('Description', 'bb-powerpack-lite'),
				'fields'    => array(
					'desc_typography'		=> array(
						'type'					=> 'typography',
						'label'					=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-infobox-description'
						)
					),
				)
			),
			'button_typography'     => array(
				'collapsed'	=> true,
				'title'     => __('Link', 'bb-powerpack-lite'),
				'fields'    => array(
					'button_typography'	=> array(
						'type'					=> 'typography',
						'label'					=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-more-link'
						)
					),
				)
			),
		)
	)
));
