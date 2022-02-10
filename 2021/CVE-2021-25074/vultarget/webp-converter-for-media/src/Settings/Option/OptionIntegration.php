<?php

namespace WebpConverter\Settings\Option;

/**
 * Allows to integrate with field in plugin settings by specifying its settings and value.
 */
class OptionIntegration {

	/**
	 * Objects of supported settings options.
	 *
	 * @var OptionInterface
	 */
	private $option;

	/**
	 * @param OptionInterface $option .
	 */
	public function __construct( OptionInterface $option ) {
		$this->option = $option;
	}

	/**
	 * Returns data of option based on plugin settings.
	 *
	 * @param mixed[] $settings Plugin settings.
	 * @param bool    $is_debug Is debugging?
	 * @param bool    $is_save  Is saving?
	 *
	 * @return mixed[] Data of option.
	 */
	public function get_option_data( array $settings, bool $is_debug, bool $is_save ): array {
		$option_name     = $this->option->get_name();
		$option_type     = $this->option->get_type();
		$values          = $this->option->get_values( $settings );
		$disabled_values = $this->option->get_disabled_values( $settings );

		if ( $is_debug ) {
			$value = $this->option->get_value_for_debug( $settings );
		} else {
			$value = ( isset( $settings[ $option_name ] ) || $is_save )
				? $this->get_option_value( $settings[ $option_name ] ?? '', $option_type, $values, $disabled_values )
				: $this->option->get_default_value( $settings );
		}

		return [
			'name'         => $this->option->get_name(),
			'type'         => $option_type,
			'label'        => $this->option->get_label(),
			'notice_lines' => $this->option->get_notice_lines(),
			'info'         => $this->option->get_info(),
			'values'       => $values,
			'disabled'     => $disabled_values ?: [],
			'value'        => ( $value !== null ) ? $value : $this->option->get_default_value( $settings ),
		];
	}

	/**
	 * Returns value of option based on plugin settings.
	 *
	 * @param mixed         $current_value   Value from plugin settings.
	 * @param string        $option_type     Key of option.
	 * @param string[]|null $values          Values of option.
	 * @param string[]|null $disabled_values Disabled values of option.
	 *
	 * @return string[]|string|null Value of option.
	 */
	private function get_option_value( $current_value, string $option_type, array $values = null, array $disabled_values = null ) {
		switch ( $option_type ) {
			case OptionAbstract::OPTION_TYPE_CHECKBOX:
				$valid_values = [];
				foreach ( (array) $current_value as $option_value ) {
					if ( array_key_exists( $option_value, $values ?: [] )
						&& ! in_array( $option_value, $disabled_values ?: [] ) ) {
						$valid_values[] = $option_value;
					}
				}
				return $valid_values;
			case OptionAbstract::OPTION_TYPE_RADIO:
			case OptionAbstract::OPTION_TYPE_QUALITY:
				if ( array_key_exists( $current_value, $values ?: [] )
					&& ! in_array( $current_value, $disabled_values ?: [] ) ) {
					return $current_value;
				}
				return null;
			case OptionAbstract::OPTION_TYPE_INPUT:
			case OptionAbstract::OPTION_TYPE_TOKEN:
				return sanitize_text_field( $current_value );
		}

		return null;
	}
}
