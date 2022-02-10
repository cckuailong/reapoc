<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

use ElementorPro\Modules\DynamicTags\Tags\Base\Data_Tag;

class register_tag extends Data_Tag {

/**
* Get Name
*
* Returns the Name of the tag
*
* @since 2.0.0
* @access public
*
* @return string
*/
public function get_name() {
	return 'acf-photo-gallery';
}

/**
* Get Title
*
* Returns the title of the Tag
*
* @since 2.0.0
* @access public
*
* @return string
*/
public function get_title() {
	return __( 'ACF Photo Gallery', 'elementor-pro' );
}

/**
* Get Group
*
* Returns the Group of the tag
*
* @since 2.0.0
* @access public
*
* @return string
*/
public function get_group() {
	return 'acf';
}

/**
* Get Categories
*
* Returns an array of tag categories
*
* @since 2.0.0
* @access public
*
* @return array
*/
public function get_categories() {
	return [ \Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY ];
}

/**
* Register Controls
*
* Registers the Dynamic tag controls
*
* @since 2.0.0
* @access protected
*
* @return void
*/
protected function register_controls() {
	$this->add_control(
		'Key',
		[
			'label' => __( 'Key', 'elementor-pro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'groups' => self::get_control_options(),
		]
	);
}

/**
* Render
*
* Prints out the value of the Dynamic tag
*
* @since 2.0.0
* @access public
*
* @return void
*/
public function render() {
	$key = $this->get_settings( 'Key' );
	if ( !empty($key) ){
		list( $field_key, $meta_key ) = explode( ':', $key );
		if ( 'options' === $field_key ) {
			$field = get_field_object( $meta_key, $field_key );
		} else {
			$field = get_field_object( $field_key, get_queried_object() );
		}
		echo $field;
	}
	return;
}

public function get_value( array $options = [] ) {
	$images = [];
	$key = $this->get_settings( 'Key' );

	list( $field, $meta_key ) = explode( ':', $key );

	//$field = get_field($meta_key, get_the_ID());
	//$value = explode(',', $field);
	$value = get_field($meta_key, get_the_ID());

	if ( is_array( $value ) && !empty( $value ) ) {
		foreach ( $value as $image ) {
			$images[] = [
				'id' => $image['id'],
			];
		}
	}

	return $images;
}


public static function get_control_options() {

	// ACF >= 5.0.0
	if ( function_exists( 'acf_get_field_groups' ) ) {
		$acf_groups = acf_get_field_groups();
	} else {
		$acf_groups = apply_filters( 'acf/get_field_groups', [] );
	}

	$groups = [];

	$options_page_groups_ids = [];

	if ( function_exists( 'acf_options_page' ) ) {
		$pages = acf_options_page()->get_pages();
		foreach ( $pages as $slug => $page ) {
			$options_page_groups = acf_get_field_groups( [
				'options_page' => $slug,
			] );

			foreach ( $options_page_groups as $options_page_group ) {
				$options_page_groups_ids[] = $options_page_group['ID'];
			}
		}
	}

	foreach ( $acf_groups as $acf_group ) {
		// ACF >= 5.0.0
		if ( function_exists( 'acf_get_fields' ) ) {
			if ( isset( $acf_group['ID'] ) && ! empty( $acf_group['ID'] ) ) {
				$fields = acf_get_fields( $acf_group['ID'] );
			} else {
				$fields = acf_get_fields( $acf_group );
			}
		} else {
			$fields = apply_filters( 'acf/field_group/get_fields', [], $acf_group['id'] );
		}

		$options = [];

		if ( ! is_array( $fields ) ) {
			continue;
		}

		$has_option_page_location = in_array( $acf_group['ID'], $options_page_groups_ids, true );
		$is_only_options_page = $has_option_page_location && 1 === count( $acf_group['location'] );
					
		foreach ( $fields as $field ) {
			if ( ! in_array( $field['type'], array('photo_gallery'), true ) ) {
				continue;
			}

			// Use group ID for unique keys
			if ( $has_option_page_location ) {
				$key = 'options:' . $field['name'];
				$options[ $key ] = __( 'Options', 'elementor-pro' ) . ':' . $field['label'];
				if ( $is_only_options_page ) {
					continue;
				}
			}

			$key = $field['key'] . ':' . $field['name'];
			$options[ $key ] = $field['label'];
		}

		if ( empty( $options ) ) {
			continue;
		}

		if ( 1 === count( $options ) ) {
			$options = [ -1 => ' -- ' ] + $options;
		}

		$groups[] = [
			'label' => $acf_group['title'],
			'options' => $options,
		];
	} // End foreach().

	return $groups;
}

}