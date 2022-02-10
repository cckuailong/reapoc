<?php 
/** 
 * Beaver Builder Compatibility
 */
function pmpro_beaver_builder_compatibility() {
	// Filter members-only content later so that the builder's filters run before PMPro.
	remove_filter('the_content', 'pmpro_membership_content_filter', 5);
	add_filter('the_content', 'pmpro_membership_content_filter', 15);
}
add_action( 'init', 'pmpro_beaver_builder_compatibility' );

/**
 * Add PMPro to row settings.
 *
 * @param array  $form Row form settings.
 * @param string $id The node/row ID.
 *
 * @return array Updated form settings.
 */
function pmpro_beaver_builder_settings_form( $form, $id ) {
	if ( 'row' !== $id ) {
		return $form;
	}
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return $form;
	}
	global $membership_levels;
	$levels = array();
	$levels[0] = __( 'Non-members', 'paid-memberships-pro' );
	foreach ( $membership_levels as $level ) {
		$levels[ $level->id ] = $level->name;
	}

	$row_settings_pmpro = array(
		'title'    => __( 'PMPro', 'paid-memberships-pro' ),
		'sections' => array(
			'paid-memberships-pro' => array(
				'title'  => __( 'General', 'paid-memberships-pro' ),
				'fields' => array(
					'pmpro_enable'      => array(
						'type'    => 'select',
						'label'   => __( 'Enable Paid Memberships Pro module visibility?', 'paid-memberships-pro' ),
						'options' => array(
							'yes' => __( 'Yes', 'paid-memberships-pro' ),
							'no'  => __( 'No', 'paid-memberships-pro' ),
						),
						'default' => 'no',
						'toggle'  => array(
							'yes' => array(
								'fields' => array(
									'pmpro_memberships',
								),
							),
						),
					),
					'pmpro_memberships' => array(
						'label'        => __( 'Select a level for module access', 'paid-memberships-pro' ),
						'type'         => 'select',
						'options'      => $levels,
						'multi-select' => true,
					),
				),
			),
		),
	);

	$form['tabs'] = array_merge(
		array_slice( $form['tabs'], 0, 2 ),
		array( 'PMPro' => $row_settings_pmpro ),
		array_slice( $form['tabs'], 2 )
	);
	return $form;
}
add_filter( 'fl_builder_register_settings_form', 'pmpro_beaver_builder_settings_form', 10, 2 );

/**
 * Determine if the node (row/module) should be visible based on membership level.
 *
 * @param bool   $is_visible Whether the module/row is visible.
 * @param object $node The node type.
 *
 * @return bool True if visible, false if not.
 */
function pmpro_beaver_builder_check_field_connections( $is_visible, $node ) {
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return $is_visible;
	}

	if ( 'row' === $node->type ) {
		if ( isset( $node->settings->pmpro_enable ) && 'yes' === $node->settings->pmpro_enable ) {
			if ( pmpro_hasMembershipLevel( $node->settings->pmpro_memberships ) || empty( $node->settings->pmpro_memberships ) ) {
				return $is_visible;
			} else {
				return false;
			}
		}
	}
	if ( isset( $node->settings->pmpro_enable ) && 'yes' === $node->settings->pmpro_enable ) {
		if ( pmpro_hasMembershipLevel( $node->settings->pmpro_memberships ) || empty( $node->settings->pmpro_memberships ) ) {
			return $is_visible;
		} else {
			return false;
		}
	}
	return $is_visible;
}
add_filter( 'fl_builder_is_node_visible', 'pmpro_beaver_builder_check_field_connections', 200, 2 );

/**
 * Add PMPro to all modules in Beaver Builder
 *
 * @param array  $form The form to add a custom tab for.
 * @param string $slug The module slug.
 *
 * @return array The updated form array.
 */
function pmpro_beaver_builder_add_custom_tab_all_modules( $form, $slug ) {
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return $form;
	}
	$modules = FLBuilderModel::get_enabled_modules(); // * getting all active modules slug

	if ( in_array( $slug, $modules, true ) ) {
		global $membership_levels;
		$levels = array();
		$levels[0] = __( 'Non-members', 'paid-memberships-pro' );
		foreach ( $membership_levels as $level ) {
			$levels[ $level->id ] = $level->name;
		}
		$form['pmpro-bb'] = array(
			'title'    => __( 'PMPro', 'paid-memberships-pro' ),
			'sections' => array(
				'memberships' => array(
					'title'  => __( 'Membership Levels', 'paid-memberships-pro' ),
					'fields' => array(
						'pmpro_enable'      => array(
							'type'    => 'select',
							'label'   => __( 'Enable Paid Memberships Pro module visibility?', 'paid-memberships-pro' ),
							'options' => array(
								'yes' => __( 'Yes', 'paid-memberships-pro' ),
								'no'  => __( 'No', 'paid-memberships-pro' ),
							),
							'default' => 'no',
							'toggle'  => array(
								'yes' => array(
									'fields' => array(
										'pmpro_memberships',
									),
								),
							),
						),
						'pmpro_memberships' => array(
							'label'        => __( 'Select a level for module access', 'paid-memberships-pro' ),
							'type'         => 'select',
							'options'      => $levels,
							'multi-select' => true,
						),
					),
				),
			),
		);
	}

	return $form;
}
add_filter( 'fl_builder_register_settings_form', 'pmpro_beaver_builder_add_custom_tab_all_modules', 10, 2 );
