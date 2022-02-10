<?php
/**
 * Set up admin pointer tooltips to describe editor features on initial load
 *
 * @since 0.1
 */
if ( !function_exists( 'cffrtb_register_pointers' ) ) {
function cffrtb_register_pointers( $pointers ) {

	// Admin pointers
	$pointers[] = array(
		'id' => 'cffrtb-add-new',
		'target' => '.add-new-h2.add-field',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Add New Field', 'custom-fields-for-rtb' ),
				esc_html__( 'Add a new field or fieldset to your form.', 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' ),
		),
	);
	$pointers[] = array(
		'id' => 'cffrtb-order-fields',
		'target' => '.fields .field',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Re-order Fields', 'custom-fields-for-rtb' ),
				esc_html__( 'Drag and drop a field to re-order it or move it to a different fieldset.', 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' ),
		),
	);
	$pointers[] = array(
		'id' => 'cffrtb-change-labels',
		'target' => '.field a.label',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Change Labels', 'custom-fields-for-rtb' ),
				esc_html__( "Click the pencil icon to edit a field's label.", 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'left', 'align' => 'right' ),
		),
	);
	$pointers[] = array(
		'id' => 'cffrtb-disable-field',
		'target' => '.field a.delete',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Remove Fields', 'custom-fields-for-rtb' ),
				esc_html__( 'Click the X icon to remove a field. Some fields are required and can not be removed.', 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'left', 'align' => 'right' ),
		),
	);
	$pointers[] = array(
		'id' => 'cffrtb-disable-field',
		'target' => '#cffrtb-disabled .reset-all',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Disabled Fields', 'custom-fields-for-rtb' ),
				esc_html__( 'Default fields that have been removed will be listed in this column. If you get yourself into a bind, click the "Revert to Default" button that will appear to wipe away all of your changes. Be careful, though. This will delete any custom fields you have created.', 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' ),
		),
	);
	$pointers[] = array(
		'id' => 'cffrtb-add-new',
		'target' => '.add-new-h2.add-field',
		'options' => array(
			'content' => sprintf(
				'<h3>%s</h3><p>%s</p>',
				esc_html__( 'Get Started', 'custom-fields-for-rtb' ),
				esc_html__( 'Got it? Get started by adding a new custom field.', 'custom-fields-for-rtb' )
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' ),
		),
	);


	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$live_pointers = array();
	foreach( $pointers as $pointer ) {

		if ( in_array( $pointer['id'], $dismissed ) || empty( $pointer )  || empty( $pointer['id'] ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
			continue;
		}

		$live_pointers[] = $pointer;
	}

	return $live_pointers;
}
add_filter( 'cffrtb_pointers', 'cffrtb_register_pointers' );
}
