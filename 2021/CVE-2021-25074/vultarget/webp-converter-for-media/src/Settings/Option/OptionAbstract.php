<?php

namespace WebpConverter\Settings\Option;

/**
 * Abstract class for class that supports notice displayed in admin panel.
 */
abstract class OptionAbstract implements OptionInterface {

	const OPTION_TYPE_CHECKBOX = 'checkbox';
	const OPTION_TYPE_RADIO    = 'radio';
	const OPTION_TYPE_QUALITY  = 'quality';
	const OPTION_TYPE_INPUT    = 'input';
	const OPTION_TYPE_TOKEN    = 'token';

	/**
	 * {@inheritdoc}
	 */
	public function get_notice_lines() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_disabled_values( array $settings ) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value_for_debug( array $settings ) {
		return $this->get_default_value( $settings );
	}
}
