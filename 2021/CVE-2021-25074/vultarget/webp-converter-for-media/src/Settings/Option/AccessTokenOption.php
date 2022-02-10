<?php

namespace WebpConverter\Settings\Option;

use WebpConverter\Repository\TokenRepository;
use WebpConverter\WebpConverterConstants;

/**
 * {@inheritdoc}
 */
class AccessTokenOption extends OptionAbstract {

	const OPTION_NAME = 'access_token';

	/**
	 * @var TokenRepository
	 */
	private $token_repository;

	public function __construct( TokenRepository $token_repository = null ) {
		$this->token_repository = $token_repository ?: new TokenRepository();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 30;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name(): string {
		return self::OPTION_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_type(): string {
		return OptionAbstract::OPTION_TYPE_TOKEN;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return __( 'Access Token', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		if ( $this->token_repository->get_token()->get_valid_status() ) {
			return sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				__( 'To manage your subscriptions, please visit %1$sour website%2$s.', 'webp-converter-for-media' ),
				'<a href="' . esc_url( WebpConverterConstants::SUBSCRIPTION_MANAGEMENT_URL ) . '" target="_blank">',
				'</a>'
			);
		}

		return sprintf(
		/* translators: %1$s: open anchor tag, %2$s: close anchor tag, %3$s: open anchor tag, %4$s: close anchor tag */
			__( 'Provide a valid value to access %1$sthe PRO functionalities%2$s. You can find out more about it %3$shere%4$s.', 'webp-converter-for-media' ),
			'<a href="' . esc_url( sprintf( WebpConverterConstants::UPGRADE_PRO_PREFIX_URL, 'field-access-token-pro-features' ) ) . '" target="_blank">',
			'</a>',
			'<a href="' . esc_url( sprintf( WebpConverterConstants::UPGRADE_PRO_PREFIX_URL, 'field-access-token-upgrade' ) ) . '" target="_blank">',
			'</a>'
		);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value( array $settings = null ): string {
		return '';
	}
}
