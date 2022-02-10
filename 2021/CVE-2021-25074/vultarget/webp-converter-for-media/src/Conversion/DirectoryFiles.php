<?php

namespace WebpConverter\Conversion;

use WebpConverter\HookableInterface;
use WebpConverter\PluginData;
use WebpConverter\Service\ServerConfigurator;
use WebpConverter\Settings\Option\SupportedExtensionsOption;

/**
 * Returns paths to files in given directory.
 */
class DirectoryFiles implements HookableInterface {

	use PathsValidator;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * @var ServerConfigurator
	 */
	private $server_configurator;

	public function __construct( PluginData $plugin_data, ServerConfigurator $server_configurator = null ) {
		$this->plugin_data         = $plugin_data;
		$this->server_configurator = $server_configurator ?: new ServerConfigurator();
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'webpc_dir_files', [ $this, 'get_files_by_directory' ], 10, 3 );
	}

	/**
	 * Returns list of source images for directory.
	 *
	 * @param string[] $value          Server paths of source images.
	 * @param string   $dir_path       Server path of source directory.
	 * @param bool     $skip_converted Skip images already converted?
	 *
	 * @return string[] Server paths of source images.
	 * @internal
	 */
	public function get_files_by_directory( array $value, string $dir_path, bool $skip_converted = false ): array {
		if ( ! file_exists( $dir_path ) ) {
			return $value;
		}

		$this->server_configurator->set_memory_limit();
		$this->server_configurator->set_execution_time();

		$settings = $this->plugin_data->get_plugin_settings();
		return $this->find_files_in_directory( $dir_path, $settings[ SupportedExtensionsOption::OPTION_NAME ], $skip_converted );
	}

	/**
	 * Returns list of source images for directory.
	 *
	 * @param string   $dir_path       Server path of source directory.
	 * @param string[] $allowed_exts   File extensions to find.
	 * @param bool     $skip_converted Skip images already converted?
	 *
	 * @return string[] Server paths of source images.
	 */
	private function find_files_in_directory( string $dir_path, array $allowed_exts, bool $skip_converted ): array {
		$paths = scandir( $dir_path );
		$list  = [];
		if ( ! is_array( $paths ) ) {
			return $list;
		}

		foreach ( $paths as $path ) {
			$current_path = $dir_path . '/' . urlencode( $path ); // phpcs:ignore
			if ( is_dir( $current_path ) ) {
				if ( $this->is_supported_source_directory( $current_path ) ) {
					$list = array_merge( $list, $this->find_files_in_directory( $current_path, $allowed_exts, $skip_converted ) );
				}
			} elseif ( in_array( strtolower( pathinfo( $current_path, PATHINFO_EXTENSION ) ), $allowed_exts ) ) {
				if ( $this->is_supported_source_file( $current_path, $skip_converted ) ) {
					$list[] = $current_path;
				}
			}
		}
		return $list;
	}
}
