<?php
/**
 * Render post author select field
 */

defined( 'ABSPATH' ) || exit;

$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-select.php';

$args = array(
	'role__in' => array( 'administrator', 'editor', 'author' ),
);

$author_args = apply_filters( 'wpcf7r_get_authors_args', $args );

$authors = get_users( $author_args );

$field['options']['current_user'] = __( 'Current Logged In User', 'wpcf7-redirect' );

foreach ( $authors as $author ) {
	$field['options'][ $author->ID ] = $author->data->user_nicename;
}


include $template;
