<?php

function pp_row_register_settings( $extensions ) {

    if ( array_key_exists( 'gradient', $extensions['row'] ) || in_array( 'gradient', $extensions['row'] ) ) {
        add_filter( 'fl_builder_register_settings_form', 'pp_row_gradient', 10, 2 );
    }
    if ( array_key_exists( 'separators', $extensions['row'] ) || in_array( 'separators', $extensions['row'] ) ) {
		add_filter( 'fl_builder_register_settings_form', 'pp_row_settings_tab', 10, 2 );
		add_filter( 'pp_row_settings_tab_sections', 'pp_row_separators' );
	}
}

/** Row settings tab */
function pp_row_settings_tab( $form, $id ) {
	if ( 'row' != $id ) {
        return $form;
	}
	
	$tab_name = pp_get_admin_label();

	$advanced = $form['tabs']['advanced'];
	unset( $form['tabs']['advanced'] );
	
	$form['tabs']['powerpack'] = array(
		'title'			=> $tab_name,
		'sections'		=> apply_filters( 'pp_row_settings_tab_sections', array() )
	);

	$form['tabs']['advanced'] = $advanced;

    return $form;
}

function pp_row_fallback_settings( $nodes ) {
	// Loop through the nodes.
	foreach ( $nodes as $node_id => $node ) {
		// Update row settings.
		if ( 'row' === $node->type ) {
			// Gradient Background Color.
			if ( isset( $node->settings->bg_type ) && 'pp_gradient' == $node->settings->bg_type ) {
				if ( isset( $node->settings->bg_gradient ) ) {
					$gradient = array(
						'type'	=> 'linear',
						'stops'	=> array(0, 100),
						'position'	=> 'center top'
					);
					// Check gradient type.
					if ( isset( $node->settings->gradient_type ) ) {
						$gradient['type'] = $node->settings->gradient_type;
					}
					// Check gradient color.
					if ( isset( $node->settings->gradient_color ) && is_array( $node->settings->gradient_color ) ) {
						$gradient['colors'][0] = ( isset( $node->settings->gradient_color['primary'] ) ) ? $node->settings->gradient_color['primary'] : '';
						$gradient['colors'][1] = ( isset( $node->settings->gradient_color['secondary'] ) ) ? $node->settings->gradient_color['secondary'] : '';
					}
					// Check gradient direction.
					if ( 'linear' == $gradient['type'] && isset( $node->settings->linear_direction ) ) {
						$direction = $node->settings->linear_direction;
						$angle = 90;
						// Top to Bottom.
						if ( 'bottom' == $direction ) {
							$angle = 180;
						}
						// Left to Right.
						if ( 'right' == $direction ) {
							$angle = 90;
						}
						// Bottom Left to Top Right.
						if ( 'top_right_diagonal' == $direction ) {
							$angle = 45;
						}
						// Bottom Right to Top Left.
						if ( 'top_left_diagonal' == $direction ) {
							$angle = 315;
						}
						// Top Left to Bottom Right.
						if ( 'bottom_right_diagonal' == $direction ) {
							$angle = 135;
						}
						// Top Right to Bottom Left.
						if ( 'bottom_left_diagonal' == $direction ) {
							$angle = 225;
						}
						$gradient['angle'] = $angle;
					}

					$node->settings->bg_type = 'gradient';
					$node->settings->bg_gradient = $gradient;
				}
			}

			// Check for the old setting to update.
			if ( isset( $node->settings->pp_bg_overlay_type ) ) {
				// Check for PP gradient setting and assign the values to
				// BB's gradient overlay field.
				if ( 'gradient' === $node->settings->pp_bg_overlay_type ) {
					// Assign overlay type gradient.
					if ( isset( $node->settings->bg_overlay_type ) ) {
						$node->settings->bg_overlay_type = 'gradient';
					}
					// Assign PP's gradient field values to BB's gradient field.
					if ( isset( $node->settings->bg_overlay_gradient ) ) {
						$gradient = array(
							'type'	=> 'linear',
							'stops'	=> array(0, 100)
						);
						// Color 1 - "Overlay Color" - This is BB's field that we had used for Color 1.
						if ( isset( $node->settings->bg_overlay_color ) ) {
							$gradient['colors'][0] = $node->settings->bg_overlay_color;
						}
						// Color 2 - "Overlay Color 2" - This was PP's field used for Color 2.
						if ( isset( $node->settings->pp_bg_overlay_color_2 ) ) {
							$color_2 = $node->settings->pp_bg_overlay_color_2;
							if ( isset( $node->settings->bg_overlay_opacity ) ) {
								$opacity = ! empty( $node->settings->bg_overlay_opacity ) ? ( $node->settings->bg_overlay_opacity / 100 ) : 1;
								$color_2 = pp_hex2rgba( $color_2, $opacity );
							}
							// Parse opacity from Color 1 in case bg_overlay_opacity is not available.
							elseif ( isset( $node->settings->bg_overlay_color ) && ! empty( $node->settings->bg_overlay_color ) ) {
								$color_1 = $node->settings->bg_overlay_color;
								if ( false !== strpos( $color_1, 'rgba' ) ) {
									$color_1_value = str_replace( 'rgba(', '', $color_1 );
									$color_1_value = str_replace( ')', '', $color_1 );
									$color_1_opacity = explode( ',', $color_1_value );
									$color_2 = pp_hex2rgba( $color_2, trim( $color_1_opacity[3] ) );
								}
							}
							$gradient['colors'][1] = $color_2;
						}
						// PP's Gradient Direction.
						if ( isset( $node->settings->pp_bg_overlay_direction ) ) {
							$direction = $node->settings->pp_bg_overlay_direction;
							$angle = 90;
							// Top to Bottom.
							if ( 'bottom' == $direction ) {
								$angle = 180;
							}
							// Left to Right.
							if ( 'right' == $direction ) {
								$angle = 90;
							}
							// Bottom Left to Top Right.
							if ( 'top_right_diagonal' == $direction ) {
								$angle = 45;
							}
							// Bottom Right to Top Left.
							if ( 'top_left_diagonal' == $direction ) {
								$angle = 315;
							}
							// Top Left to Bottom Right.
							if ( 'bottom_right_diagonal' == $direction ) {
								$angle = 135;
							}
							// Top Right to Bottom Left.
							if ( 'bottom_left_diagonal' == $direction ) {
								$angle = 225;
							}
							$gradient['angle'] = $angle;
						}

						$node->settings->bg_overlay_gradient = $gradient;
					}

					unset( $node->settings->pp_bg_overlay_type );
				}
				// Change an old setting to a new setting.
				//$node->settings->new_setting = $node->settings->old_setting;
				// Make sure to unset the old setting so this doesn't run again.
				//unset( $node->settings->old_setting );
			}
			// Save the update settings.
			$nodes[ $node_id ]->settings = $node->settings;
		}
	}

	return $nodes;
}
add_filter( 'fl_builder_get_layout_metadata', 'pp_row_fallback_settings' );

/** Gradient */
function pp_row_gradient( $form, $id ) {

    if ( 'row' != $id ) {
        return $form;
    }

    //$border_section = $form['tabs']['style']['sections']['border'];
    //unset( $form['tabs']['style']['sections']['border'] );

    $form['tabs']['style']['sections']['background']['fields']['bg_type']['options']['pp_gradient'] = __('Gradient', 'bb-powerpack-lite');
    $form['tabs']['style']['sections']['background']['fields']['bg_type']['toggle']['pp_gradient'] = array(
        'sections'  => array('pp_row_gradient')
    );
    $form['tabs']['style']['sections']['pp_row_gradient'] = array(
        'title'     => __('Gradient', 'bb-powerpack-lite'),
        'fields'    => array(
            'gradient_type' => array(
                'type'      => 'pp-switch',
                'label'     => __('Gradient Type', 'bb-powerpack-lite'),
                'default'   => 'linear',
                'options'   => array(
                    'linear'    => __('Linear', 'bb-powerpack-lite'),
                    'radial'    => __('Radial', 'bb-powerpack-lite'),
                ),
                'toggle'    => array(
                    'linear'    => array(
                        'fields'    => array('linear_direction')
                    ),
                ),
            ),
            'gradient_color'    => array(
                'type'      => 'pp-color',
                'label'     => __('Colors', 'bb-powerpack-lite'),
                'show_reset'    => true,
                'default'   => array(
                    'primary'   => 'd81660',
                    'secondary' => '7d22bd',
                ),
                'options'   => array(
                    'primary'   => __('Primary', 'bb-powerpack-lite'),
                    'secondary'   => __('Secondary', 'bb-powerpack-lite'),
                ),
            ),
            'linear_direction'  => array(
                'type'      => 'select',
                'label'     => __('Gradient Direction', 'bb-powerpack-lite'),
                'default'   => 'bottom',
                'options'   => array(
                    'bottom' => __('Top to Bottom', 'bb-powerpack-lite'),
                    'right' => __('Left to Right', 'bb-powerpack-lite'),
                    'top_right_diagonal' => __('Bottom Left to Top Right', 'bb-powerpack-lite'),
                    'top_left_diagonal' => __('Bottom Right to Top Left', 'bb-powerpack-lite'),
                    'bottom_right_diagonal' => __('Top Left to Bottom Right', 'bb-powerpack-lite'),
                    'bottom_left_diagonal' => __('Top Right to Bottom Left', 'bb-powerpack-lite'),
                ),
            ),
        )
    );

    //$form['tabs']['style']['sections']['border'] = $border_section;

    return $form;
}

/** Separator */
function pp_row_separators( $sections ) {
	$separators = array(
		'pp_separators'	=> array( /** Separator */
			'title'			=> __('Separators', 'bb-powerpack-lite'),
			'collapsed'		=> false,
			'fields'		=> array(
				'enable_separator'          => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Enable Separator?', 'bb-powerpack-lite'),
					'default'                   => 'no',
					'options'                   => array(
						'yes'                       => __('Yes', 'bb-powerpack-lite'),
						'no'                        => __('No', 'bb-powerpack-lite')
					),
					'toggle'                    => array(
						'yes'                       => array(
							'sections'                  => array('separator_top', 'separator_bottom')
						)
					)
				)
			)
		),
		'separator_top'             => array(
			'title'                     => __('Top Separator', 'bb-powerpack-lite'),
			'collapsed'					=> true,
			'fields'                    => array(
				'separator_type'            => array(
					'type'                      => 'select',
					'label'                     => __('Type', 'bb-powerpack-lite'),
					'default'                   => 'none',
					'options'                   => array(
						'none'                      => __('None', 'bb-powerpack-lite'),
						'triangle'                  => __('Big Triangle', 'bb-powerpack-lite'),
						'triangle_shadow'           => __('Big Triangle with Shadow', 'bb-powerpack-lite'),
						'triangle_left'             => __('Big Triangle Left', 'bb-powerpack-lite'),
						'triangle_right'            => __('Big Triangle Right', 'bb-powerpack-lite'),
						'triangle_small'            => __('Small Triangle', 'bb-powerpack-lite'),
						'tilt_left'                 => __('Tilt Left', 'bb-powerpack-lite'),
						'tilt_right'                => __('Tilt Right', 'bb-powerpack-lite'),
						'curve'                     => __('Curve', 'bb-powerpack-lite'),
						'wave'                      => __('Wave', 'bb-powerpack-lite'),
						'cloud'                     => __('Cloud', 'bb-powerpack-lite'),
						'slit'                      => __('Slit', 'bb-powerpack-lite'),
						'water'                     => __('Water', 'bb-powerpack-lite'),
						'zigzag'                    => __('ZigZag', 'bb-powerpack-lite'),
					),
					'toggle'                    => array(
						'triangle_shadow'           => array(
							'fields'                    => array('separator_shadow')
						)
					)
				),
				'separator_color'           => array(
					'type'                      => 'color',
					'label'                     => __('Color', 'bb-powerpack-lite'),
					'default'                   => 'ffffff',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-top svg',
						'property'                  => 'fill'
					)
				),
				'separator_shadow'          => array(
					'type'                      => 'color',
					'label'                     => __('Shadow Color', 'bb-powerpack-lite'),
					'default'                   => 'f4f4f4',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-top svg .pp-shadow-color',
						'property'                  => 'fill'
					)
				),
				'separator_height'          => array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => 100,
					'size'                      => 5,
					'maxlength'                 => 3,
					'description'               => 'px',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-top svg',
						'property'                  => 'height',
						'unit'                      => 'px'
					)
				),
				'separator_position'        => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Position', 'bb-powerpack-lite'),
					'default'                   => 'top',
					'options'                   => array(
						'top'                       => __('Top', 'bb-powerpack-lite'),
						'bottom'                    => __('Bottom', 'bb-powerpack-lite')
					)
				),
				'separator_tablet'          => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Show on Tablet', 'bb-powerpack-lite'),
					'default'                   => 'no',
					'options'                   => array(
						'yes'                       => __('Yes', 'bb-powerpack-lite'),
						'no'                        => __('No', 'bb-powerpack-lite')
					),
					'toggle'                    => array(
						'yes'                       => array(
							'fields'                    => array('separator_height_tablet')
						)
					)
				),
				'separator_height_tablet'   => array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => '',
					'description'               => 'px',
					'size'                      => 5,
					'maxlength'                 => 3
				),
				'separator_mobile'          => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Show on Mobile', 'bb-powerpack-lite'),
					'default'                   => 'no',
					'options'                   => array(
						'yes'                       => __('Yes', 'bb-powerpack-lite'),
						'no'                        => __('No', 'bb-powerpack-lite')
					),
					'toggle'                    => array(
						'yes'                       => array(
							'fields'                    => array('separator_height_mobile')
						)
					)
				),
				'separator_height_mobile'   => array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => '',
					'description'               => 'px',
					'size'                      => 5,
					'maxlength'                 => 3
				)
			)
		),
		'separator_bottom'        => array(
			'title'                     => __('Bottom Separator', 'bb-powerpack-lite'),
			'collapsed'					=> true,
			'fields'                    => array(
				'separator_type_bottom'     => array(
					'type'                      => 'select',
					'label'                     => __('Type', 'bb-powerpack-lite'),
					'default'                   => 'none',
					'options'                   => array(
						'none'                      => __('None', 'bb-powerpack-lite'),
						'triangle'                  => __('Big Triangle', 'bb-powerpack-lite'),
						'triangle_shadow'           => __('Big Triangle with Shadow', 'bb-powerpack-lite'),
						'triangle_left'             => __('Big Triangle Left', 'bb-powerpack-lite'),
						'triangle_right'            => __('Big Triangle Right', 'bb-powerpack-lite'),
						'triangle_small'            => __('Small Triangle', 'bb-powerpack-lite'),
						'tilt_left'                 => __('Tilt Left', 'bb-powerpack-lite'),
						'tilt_right'                => __('Tilt Right', 'bb-powerpack-lite'),
						'curve'                     => __('Curve', 'bb-powerpack-lite'),
						'wave'                      => __('Wave', 'bb-powerpack-lite'),
						'cloud'                     => __('Cloud', 'bb-powerpack-lite'),
						'slit'                      => __('Slit', 'bb-powerpack-lite'),
						'water'                     => __('Water', 'bb-powerpack-lite'),
						'zigzag'                    => __('ZigZag', 'bb-powerpack-lite'),
					),
					'toggle'                    => array(
						'triangle_shadow'           => array(
							'fields'                    => array('separator_shadow_bottom')
						)
					)
				),
				'separator_color_bottom'           => array(
					'type'                      => 'color',
					'label'                     => __('Color', 'bb-powerpack-lite'),
					'default'                   => 'ffffff',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-bottom svg',
						'property'                  => 'fill'
					)
				),
				'separator_shadow_bottom'          => array(
					'type'                      => 'color',
					'label'                     => __('Shadow Color', 'bb-powerpack-lite'),
					'default'                   => 'f4f4f4',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-bottom svg .pp-shadow-color',
						'property'                  => 'fill'
					)
				),
				'separator_height_bottom'          => array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => 100,
					'size'                      => 5,
					'maxlength'                 => 3,
					'description'               => 'px',
					'preview'                   => array(
						'type'                      => 'css',
						'selector'                  => '.pp-row-separator-bottom svg',
						'property'                  => 'height',
						'unit'                      => 'px'
					)
				),
				'separator_tablet_bottom'   => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Show on Tablet', 'bb-powerpack-lite'),
					'default'                   => 'no',
					'options'                   => array(
						'yes'                       => __('Yes', 'bb-powerpack-lite'),
						'no'                        => __('No', 'bb-powerpack-lite')
					),
					'toggle'                    => array(
						'yes'                       => array(
							'fields'                    => array('separator_height_tablet_bottom')
						)
					),
					'preview'                   => array(
						'type'                      => 'none'
					)
				),
				'separator_height_tablet_bottom'=> array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => '',
					'description'               => 'px',
					'size'                      => 5,
					'maxlength'                 => 3
				),
				'separator_mobile_bottom'          => array(
					'type'                      => 'pp-switch',
					'label'                     => __('Show on Mobile', 'bb-powerpack-lite'),
					'default'                   => 'no',
					'options'                   => array(
						'yes'                       => __('Yes', 'bb-powerpack-lite'),
						'no'                        => __('No', 'bb-powerpack-lite')
					),
					'toggle'                    => array(
						'yes'                       => array(
							'fields'                    => array('separator_height_mobile_bottom')
						)
					),
					'preview'                   => array(
						'type'                      => 'none'
					)
				),
				'separator_height_mobile_bottom'   => array(
					'type'                      => 'text',
					'label'                     => __('Size', 'bb-powerpack-lite'),
					'default'                   => '',
					'description'               => 'px',
					'size'                      => 5,
					'maxlength'                 => 3
				)
			)
		),
	);

	return array_merge( $sections, $separators );
}
