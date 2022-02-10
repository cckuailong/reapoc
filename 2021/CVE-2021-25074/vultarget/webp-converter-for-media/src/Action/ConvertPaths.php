<?php

namespace WebpConverter\Action;

use WebpConverter\Conversion\Method\MethodIntegrator;
use WebpConverter\Conversion\PathsValidator;
use WebpConverter\HookableInterface;
use WebpConverter\PluginData;

/**
 * Initializes conversion of all images in list of paths.
 */
class ConvertPaths implements HookableInterface {

	use PathsValidator;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'webpc_convert_paths', [ $this, 'convert_files_by_paths' ] );
	}

	/**
	 * Converts all given images to output formats.
	 *
	 * @param string[] $paths Server paths of images.
	 *
	 * @return void
	 * @internal
	 */
	public function convert_files_by_paths( array $paths ) {
		( new MethodIntegrator( $this->plugin_data ) )
			->init_conversion( $this->remove_paths_from_excluded_paths( $paths ), false );
	}

	/**
	 * Removes paths of source images from excluded paths.
	 *
	 * @param string[] $source_paths Server paths of images.
	 *
	 * @return string[]
	 */
	private function remove_paths_from_excluded_paths( array $source_paths ): array {
		foreach ( $source_paths as $path_index => $path ) {
			if ( ! $this->is_supported_source_file( $path ) ) {
				unset( $source_paths[ $path_index ] );
			}
		}
		return $source_paths;
	}
}
