<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Gateways\PayPal\Repositories\Authorization;

/**
 * Class Refresh_Token
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Refresh_Token {

	/*
	 * @since 5.1.6
	 *
	 * @var Merchant
	 */
	private $merchant;

	/**
	 * @since 5.1.6
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Refresh_Token constructor.
	 *
	 * @since 5.1.6
	 *
	 * @param Merchant $merchant
	 * @param Client   $client
	 */
	public function __construct(
		Merchant $merchant = null,
		Client $client = null
	) {
		$this->merchant = $merchant ?: tribe( Merchant::class );
		$this->client   = $client ?: tribe( Client::class );
	}

	/**
	 * Return cron json name which uses to refresh token.
	 *
	 * @since 5.1.6
	 *
	 * @return string
	 */
	private function get_cron_job_hook_name() {
		return 'tec_tickets_commerce_paypal_refresh_access_token';
	}

	/**
	 * Register cron job to refresh access token.
	 * Note: only for internal use.
	 *
	 * @since 5.1.6
	 *
	 * @param string $tokenExpires What time the token expires.
	 */
	public function register_cron_job_to_refresh_token( $tokenExpires ) {
		// @todo Verify we need this as a cron, do we lose total API access if it expires (no visitors)?
		wp_schedule_single_event(
		// Refresh token before half hours of expires date.
			time() + ( $tokenExpires - 1800 ),
			$this->get_cron_job_hook_name()
		);
	}

	/**
	 * Delete cron job which refresh access token.
	 * Note: only for internal use.
	 *
	 * @since 5.1.6
	 */
	public function delete_refresh_token_cron_job() {
		wp_clear_scheduled_hook( $this->get_cron_job_hook_name() );
	}

	/**
	 * Refresh token.
	 * Note: only for internal use
	 *
	 * @since 5.1.6
	 */
	public function refresh_token() {
		// Exit if account is not connected.
		if ( ! $this->merchant->account_is_connected() ) {
			return;
		}

		$token_data = $this->client->get_access_token_from_client_credentials( $this->merchant->get_client_id(), $this->merchant->get_client_secret() );

		$this->merchant->save_access_token_data( $token_data );

		$this->register_cron_job_to_refresh_token( $token_data['expires_in'] );
	}
}
