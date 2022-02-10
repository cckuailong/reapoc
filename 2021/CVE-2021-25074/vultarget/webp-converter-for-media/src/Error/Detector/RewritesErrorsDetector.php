<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Conversion\Format\WebpFormat;
use WebpConverter\Conversion\OutputPath;
use WebpConverter\Error\Notice\BypassingApacheNotice;
use WebpConverter\Error\Notice\ErrorNotice;
use WebpConverter\Error\Notice\PassthruNotWorkingNotice;
use WebpConverter\Error\Notice\RewritesCachedNotice;
use WebpConverter\Error\Notice\RewritesNotExecutedNotice;
use WebpConverter\Error\Notice\RewritesNotWorkingNotice;
use WebpConverter\Loader\HtaccessLoader;
use WebpConverter\Loader\LoaderAbstract;
use WebpConverter\Loader\PassthruLoader;
use WebpConverter\PluginData;
use WebpConverter\PluginInfo;
use WebpConverter\Service\FileLoader;
use WebpConverter\Settings\Option\LoaderTypeOption;
use WebpConverter\Settings\Option\OutputFormatsOption;
use WebpConverter\Settings\Option\SupportedDirectoriesOption;

/**
 * Checks for configuration errors about non-working HTTP rewrites.
 */
class RewritesErrorsDetector implements ErrorDetector {

	const PATH_SOURCE_FILE_PNG    = '/assets/img/icon-test.png';
	const PATH_SOURCE_FILE_WEBP   = '/assets/img/icon-test.webp';
	const PATH_OUTPUT_FILE_PNG    = '/webp-converter-for-media-test.png';
	const PATH_OUTPUT_FILE_PNG2   = '/webp-converter-for-media-test.png2';
	const URL_DEBUG_HTACCESS_FILE = 'assets/img/debug-htaccess/icon-test.png';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * @var FileLoader
	 */
	private $file_loader;

	public function __construct( PluginInfo $plugin_info, PluginData $plugin_data, FileLoader $file_loader = null ) {
		$this->plugin_info = $plugin_info;
		$this->plugin_data = $plugin_data;
		$this->file_loader = $file_loader ?: new FileLoader( $plugin_info, $plugin_data );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		$plugin_settings = $this->plugin_data->get_plugin_settings();
		if ( ! $plugin_settings[ SupportedDirectoriesOption::OPTION_NAME ]
			|| ! $plugin_settings[ OutputFormatsOption::OPTION_NAME ]
			|| ! in_array( WebpFormat::FORMAT_EXTENSION, $plugin_settings[ OutputFormatsOption::OPTION_NAME ] ) ) {
			return null;
		}

		$this->convert_images_for_debug();

		do_action( LoaderAbstract::ACTION_NAME, true, true );
		$error = $this->detect_rewrites_error();
		do_action( LoaderAbstract::ACTION_NAME, true );

		return $error;
	}

	/**
	 * @return ErrorNotice|null
	 */
	private function detect_rewrites_error() {
		$settings    = $this->plugin_data->get_plugin_settings();
		$loader_type = $settings[ LoaderTypeOption::OPTION_NAME ] ?? '';

		switch ( $loader_type ) {
			case HtaccessLoader::LOADER_TYPE:
				if ( $this->if_redirects_are_works() === true ) {
					break;
				}

				if ( $this->if_bypassing_apache_is_active() === true ) {
					return new BypassingApacheNotice();
				} elseif ( $this->if_htaccess_can_be_overwritten() !== true ) {
					return new RewritesNotExecutedNotice();
				}

				return new RewritesNotWorkingNotice();
			case PassthruLoader::LOADER_TYPE:
				if ( $this->if_redirects_are_works() === true ) {
					break;
				}

				return new PassthruNotWorkingNotice();
		}

		if ( $this->if_redirects_are_cached() === true ) {
			return new RewritesCachedNotice();
		}

		return null;
	}

	/**
	 * Converts and saves files needed for testing.
	 *
	 * @return void
	 */
	private function convert_images_for_debug() {
		$uploads_dir    = apply_filters( 'webpc_dir_path', '', 'uploads' );
		$path_file_png  = $uploads_dir . self::PATH_OUTPUT_FILE_PNG;
		$path_file_png2 = $uploads_dir . self::PATH_OUTPUT_FILE_PNG2;
		if ( ! is_writable( $uploads_dir ) ) {
			return;
		}

		if ( ! file_exists( $path_file_png ) || ! file_exists( $path_file_png2 ) ) {
			copy( $this->plugin_info->get_plugin_directory_path() . self::PATH_SOURCE_FILE_PNG, $path_file_png );
			copy( $this->plugin_info->get_plugin_directory_path() . self::PATH_SOURCE_FILE_PNG, $path_file_png2 );
		}

		if ( ( $output_path = OutputPath::get_path( $path_file_png, true, WebpFormat::FORMAT_EXTENSION ) )
			&& ! file_exists( $output_path ) ) {
			copy( $this->plugin_info->get_plugin_directory_path() . self::PATH_SOURCE_FILE_WEBP, $output_path );
		}
		if ( ( $output_path = OutputPath::get_path( $path_file_png2, true, WebpFormat::FORMAT_EXTENSION ) )
			&& ! file_exists( $output_path ) ) {
			copy( $this->plugin_info->get_plugin_directory_path() . self::PATH_SOURCE_FILE_WEBP, $output_path );
		}
	}

	/**
	 * Checks if redirects to output images are works.
	 *
	 * @return bool Verification status.
	 */
	private function if_redirects_are_works(): bool {
		$uploads_dir = apply_filters( 'webpc_dir_path', '', 'uploads' );
		$uploads_url = apply_filters( 'webpc_dir_url', '', 'uploads' );
		$ver_param   = sprintf( 'ver=%s', time() );

		$file_size = $this->file_loader->get_file_size_by_path(
			$uploads_dir . self::PATH_OUTPUT_FILE_PNG
		);
		$file_webp = $this->file_loader->get_file_size_by_url(
			$uploads_url . self::PATH_OUTPUT_FILE_PNG,
			true,
			$ver_param
		);

		return ( $file_webp < $file_size );
	}

	/**
	 * Checks if server supports using .htaccess files from custom locations.
	 *
	 * @return bool Verification status.
	 */
	private function if_htaccess_can_be_overwritten(): bool {
		$file_size = $this->file_loader->get_file_size_by_url(
			$this->plugin_info->get_plugin_directory_url() . self::URL_DEBUG_HTACCESS_FILE
		);

		return ( $file_size === 0 );
	}

	/**
	 * Checks if bypassing of redirects to output images is exists.
	 *
	 * @return bool Verification status.
	 */
	private function if_bypassing_apache_is_active(): bool {
		$uploads_url = apply_filters( 'webpc_dir_url', '', 'uploads' );
		$ver_param   = sprintf( '&?ver=%s', time() );

		$file_png  = $this->file_loader->get_file_size_by_url(
			$uploads_url . self::PATH_OUTPUT_FILE_PNG,
			true,
			$ver_param
		);
		$file_png2 = $this->file_loader->get_file_size_by_url(
			$uploads_url . self::PATH_OUTPUT_FILE_PNG2,
			true,
			$ver_param
		);

		return ( $file_png > $file_png2 );
	}

	/**
	 * Checks if redirects to output images are cached.
	 *
	 * @return bool Verification status.
	 */
	private function if_redirects_are_cached(): bool {
		$uploads_url = apply_filters( 'webpc_dir_url', '', 'uploads' );
		$ver_param   = sprintf( 'ver=%s', time() );

		$file_webp     = $this->file_loader->get_file_size_by_url(
			$uploads_url . self::PATH_OUTPUT_FILE_PNG,
			true,
			$ver_param
		);
		$file_original = $this->file_loader->get_file_size_by_url(
			$uploads_url . self::PATH_OUTPUT_FILE_PNG,
			false,
			$ver_param
		);

		return ( ( $file_webp > 0 ) && ( $file_webp === $file_original ) );
	}
}
