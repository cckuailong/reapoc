<?php

namespace WebpConverter\Conversion\Endpoint;

use WebpConverter\PluginData;
use WebpConverter\Repository\TokenRepository;

/**
 * Calculates the number of all images to be converted.
 */
class ImagesCounterEndpoint extends EndpointAbstract {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * @var TokenRepository
	 */
	private $token_repository;

	public function __construct( PluginData $plugin_data, TokenRepository $token_repository ) {
		$this->plugin_data      = $plugin_data;
		$this->token_repository = $token_repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_route_name(): string {
		return 'images-counter';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_route_response( \WP_REST_Request $request ) {
		$images_count = number_format(
			count( ( new PathsEndpoint( $this->plugin_data, $this->token_repository ) )->get_paths( false ) ),
			0,
			'',
			' '
		);

		return new \WP_REST_Response(
			[
				'value_output' => sprintf(
				/* translators: %1$s: images count */
					__( '%1$s for AVIF and %1$s for WebP', 'webp-converter-for-media' ),
					$images_count
				),
			],
			200
		);
	}
}
