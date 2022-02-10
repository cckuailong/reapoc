<?php

namespace WebpConverter\Conversion\Method;

use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ConversionMethodOption;

/**
 * Initializes image conversion using active image conversion method.
 */
class MethodIntegrator {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * Initializes converting source images using active and set conversion method.
	 *
	 * @param string[] $paths            Server paths for source images.
	 * @param bool     $regenerate_force .
	 *
	 * @return mixed[]|null Results data of conversion.
	 */
	public function init_conversion( array $paths, bool $regenerate_force ) {
		if ( ! $method = $this->get_method_used() ) {
			return null;
		}

		$method->convert_paths( $paths, $this->plugin_data->get_plugin_settings(), $regenerate_force );
		return [
			'is_fatal_error' => $method->is_fatal_error(),
			'errors'         => apply_filters( 'webpc_convert_errors', $method->get_errors() ),
			'size'           => [
				'before' => $method->get_size_before(),
				'after'  => $method->get_size_after(),
			],
		];
	}

	/**
	 * Returns active and set conversion method.
	 *
	 * @return MethodInterface|null Object of conversion method.
	 */
	private function get_method_used() {
		if ( apply_filters( 'webpc_server_errors', [], true ) ) {
			return null;
		}

		$method_key = $this->plugin_data->get_plugin_settings()[ ConversionMethodOption::OPTION_NAME ] ?? null;
		$methods    = ( new MethodFactory() )->get_methods_objects();
		foreach ( $methods as $method_name => $method ) {
			if ( $method_key === $method_name ) {
				return $method;
			}
		}
		return null;
	}
}
