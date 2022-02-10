<?php

function pp_column_register_settings( $extensions ) {

    // if ( array_key_exists( 'corners', $extensions['col'] ) || in_array( 'corners', $extensions['col'] ) ) {
    //     add_filter( 'fl_builder_register_settings_form', 'pp_column_round_corners', 10, 2 );
    // }
}

function pp_column_fallback_settings( $nodes ) {
	// Loop through the nodes.
	foreach ( $nodes as $node_id => $node ) {
		// Update row settings.
		if ( 'column' === $node->type ) {
			if ( isset( $node->settings->border ) && is_array( $node->settings->border ) ) {
				$border = $node->settings->border;

				// Round Corners
				if ( isset( $node->settings->pp_round_corners ) ) {
					if ( empty( $border['radius']['top_left'] ) ) {
						$border['radius']['top_left'] = $node->settings->pp_round_corners['top_left'];
					}
					if ( empty( $border['radius']['top_right'] ) ) {
						$border['radius']['top_right'] = $node->settings->pp_round_corners['top_right'];
					}
					if ( empty( $border['radius']['bottom_left'] ) ) {
						$border['radius']['bottom_left'] = $node->settings->pp_round_corners['bottom_left'];
					}
					if ( empty( $border['radius']['bottom_right'] ) ) {
						$border['radius']['bottom_right'] = $node->settings->pp_round_corners['bottom_right'];
					}

					unset( $node->settings->pp_round_corners );
				}

				$node->settings->border = $border;
			}

			// Save the update settings.
			$nodes[ $node_id ]->settings = $node->settings;
		}
	}

	return $nodes;
}
add_filter( 'fl_builder_get_layout_metadata', 'pp_column_fallback_settings' );

function pp_column_round_corners( $form, $id ) {

    if ( 'col' != $id ) {
        return $form;
    }

    $form['tabs']['style']['sections']['border']['fields']['pp_round_corners'] = array(
        'type'              => 'pp-multitext',
        'label'             => __('Round Corners', 'bb-powerpack-lite'),
        'description'       => 'px',
        'default'           => array(
            'top_left'          => 0,
            'top_right'         => 0,
            'bottom_left'       => 0,
            'bottom_right'      => 0
        ),
        'options'           => array(
            'top_left'          => array(
                'placeholder'       => __('Top Left', 'bb-powerpack-lite'),
                'tooltip'           => __('Top Left', 'bb-powerpack-lite')
            ),
            'top_right'         => array(
                'placeholder'       => __('Top Right', 'bb-powerpack-lite'),
                'tooltip'           => __('Top Right', 'bb-powerpack-lite')
            ),
            'bottom_left'       => array(
                'placeholder'       => __('Bottom Left', 'bb-powerpack-lite'),
                'tooltip'           => __('Bottom Left', 'bb-powerpack-lite')
            ),
            'bottom_right'      => array(
                'placeholder'       => __('Bottom Right', 'bb-powerpack-lite'),
                'tooltip'           => __('Bottom Right', 'bb-powerpack-lite')
            ),
        )
    );

    return $form;
}
