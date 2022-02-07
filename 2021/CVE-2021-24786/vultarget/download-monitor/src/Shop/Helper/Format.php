<?php

namespace Never5\DownloadMonitor\Shop\Helper;

use Never5\DownloadMonitor\Shop\Services\Services;

class Format {

	/**
	 * @param float $cents
	 * @param array $args
	 *
	 * @return string
	 */
	public function money( $cents, $args = array() ) {

		/** @var \DLM_Settings_Helper $settings_helper */
		$settings_helper = download_monitor()->service( 'settings' );

		/** @var Currency $currency_helper */
		$currency_helper = Services::get()->service( 'currency' );

		$args = apply_filters( 'wpcm_format_price_args', wp_parse_args( $args, array(
			'currency'           => $currency_helper->get_shop_currency(),
			'currency_position'  => $currency_helper->get_currency_position(),
			'decimal_separator'  => $settings_helper->get_option( 'decimal_separator' ),
			'thousand_separator' => $settings_helper->get_option( 'thousand_separator' ),
			'decimals'           => 2
		) ) );

		$price_format = $this->get_money_format( $args['currency_position'] );

		$negative = $cents < 0;
		$price    = $cents / 100;
		$price    = floatval( $negative ? $price * - 1 : $price );
		$price    = number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, $currency_helper->get_currency_symbol( $args['currency'] ), $price );

		return apply_filters( 'dlm_format_money', $formatted_price );

	}

	/**
	 * Return the format for money, based on user settings
	 *
	 * @param string $currency_pos
	 *
	 * @return string
	 */
	private function get_money_format( $currency_pos ) {

		$format = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		return $format;
	}

}