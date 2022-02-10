<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Conversion\Endpoint\PathsEndpoint;
use WebpConverter\Error\Notice\AccessTokenInvalidNotice;
use WebpConverter\Error\Notice\ApiLimitExceededNotice;
use WebpConverter\PluginData;
use WebpConverter\Repository\TokenRepository;
use WebpConverter\Service\TokenValidator;
use WebpConverter\Settings\Option\AccessTokenOption;

/**
 * Checks for the token status for the PRO version.
 */
class TokenStatusDetector implements ErrorDetector {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * @var TokenRepository
	 */
	private $token_repository;

	/**
	 * @var TokenValidator
	 */
	private $token_validator;

	public function __construct(
		PluginData $plugin_data,
		TokenRepository $token_repository = null,
		TokenValidator $token_validator = null
	) {
		$this->plugin_data      = $plugin_data;
		$this->token_repository = $token_repository ?: new TokenRepository();
		$this->token_validator  = $token_validator ?: new TokenValidator();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		$settings = $this->plugin_data->get_plugin_settings();
		if ( ! isset( $settings[ AccessTokenOption::OPTION_NAME ] ) || ! $settings[ AccessTokenOption::OPTION_NAME ] ) {
			return null;
		}

		$token = $this->token_repository->get_token();
		if ( ! $token->get_valid_status() ) {
			return new AccessTokenInvalidNotice();
		}

		$images_usage = ( $token->get_images_usage() + ( PathsEndpoint::PATHS_PER_REQUEST_REMOTE_SMALL * 2 ) );
		if ( $images_usage > $token->get_images_limit() ) {
			$token = $this->token_validator->validate_token( $token->get_token_value() );
		}

		$images_usage = ( $token->get_images_usage() + ( PathsEndpoint::PATHS_PER_REQUEST_REMOTE_SMALL * 2 ) );
		if ( $images_usage > $token->get_images_limit() ) {
			return new ApiLimitExceededNotice();
		}

		return null;
	}
}
