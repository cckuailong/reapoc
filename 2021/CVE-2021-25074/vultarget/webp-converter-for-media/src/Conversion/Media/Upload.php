<?php

namespace WebpConverter\Conversion\Media;

use WebpConverter\HookableInterface;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ExtraFeaturesOption;

/**
 * Initializes image conversion when uploading images to media library.
 */
class Upload implements HookableInterface {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * Paths of converted images.
	 *
	 * @var string[]
	 */
	private $converted_paths = [];

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'wp_update_attachment_metadata', [ $this, 'init_attachment_convert' ], 10, 2 );
	}

	/**
	 * Initializes converting attachment images while attachment is uploaded.
	 *
	 * @param mixed[]|null $data          Updated attachment meta data.
	 * @param int|null     $attachment_id ID of attachment.
	 *
	 * @return mixed[]|null Attachment meta data.
	 * @internal
	 */
	public function init_attachment_convert( array $data = null, int $attachment_id = null ) {
		if ( ( $data === null ) || ( $attachment_id === null )
			|| ! is_array( $data ) || ! isset( $data['file'] ) || ! isset( $data['sizes'] ) ) {
			return $data;
		}

		$paths = $this->get_sizes_paths( $data );
		$paths = apply_filters( 'webpc_attachment_paths', $paths, $attachment_id );

		$paths                 = array_diff( $paths, $this->converted_paths );
		$this->converted_paths = array_merge( $this->converted_paths, $paths );

		$this->init_conversion( $paths );

		return $data;
	}

	/**
	 * Returns server paths of attachment image sizes.
	 *
	 * @param mixed[] $data Updated attachment meta data.
	 *
	 * @return string[] Server paths of source images.
	 */
	private function get_sizes_paths( array $data ): array {
		$directory = $this->get_attachment_directory( $data['file'] );
		$list      = [];

		$list[] = $directory . basename( $data['file'] );
		foreach ( $data['sizes'] as $size ) {
			$path = $directory . $size['file'];
			if ( ! in_array( $path, $list ) ) {
				$list[] = $path;
			}
		}
		return array_values( array_unique( $list ) );
	}

	/**
	 * Returns server path of source image.
	 *
	 * @param string $path Relative path of source image.
	 *
	 * @return string Server path of source image.
	 */
	private function get_attachment_directory( string $path ): string {
		$upload         = wp_upload_dir();
		$path_directory = rtrim( dirname( $path ), '/\\.' );
		$source         = rtrim( $upload['basedir'], '/\\' ) . '/' . $path_directory . '/';

		return str_replace( '\\', '/', $source );
	}

	/**
	 * Initializes conversion of attachment image sizes.
	 *
	 * @param string[] $paths Server paths of source images.
	 *
	 * @return void
	 */
	private function init_conversion( array $paths ) {
		$settings = $this->plugin_data->get_plugin_settings();

		if ( in_array( ExtraFeaturesOption::OPTION_VALUE_CRON_CONVERSION, $settings[ ExtraFeaturesOption::OPTION_NAME ] ) ) {
			wp_schedule_single_event( ( time() + 1 ), 'webpc_convert_paths', [ $paths ] );
		} else {
			do_action( 'webpc_convert_paths', $paths );
		}
	}
}
