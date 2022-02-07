<?php

namespace Never5\DownloadMonitor\Shop\Helper;

class Currency {

	/**
	 * Return all available currencies
	 *
	 * @return array
	 */
	public function get_available_currencies() {
		return array_unique(
			apply_filters( 'dlm_shop_currencies',
				array(
					'ARS' => __( 'Argentine Peso', 'download-monitor' ),
					'AUD' => __( 'Australian Dollars', 'download-monitor' ),
					'BDT' => __( 'Bangladeshi Taka', 'download-monitor' ),
					'BRL' => __( 'Brazilian Real', 'download-monitor' ),
					'BGN' => __( 'Bulgarian Lev', 'download-monitor' ),
					'BIF' => __( 'Burundian Franc', 'download-monitor' ),
					'CAD' => __( 'Canadian Dollars', 'download-monitor' ),
					'XAF' => __( 'CFA Franc', 'download-monitor' ),
					'CLP' => __( 'Chilean Peso', 'download-monitor' ),
					'CNY' => __( 'Chinese Yuan', 'download-monitor' ),
					'COP' => __( 'Colombian Peso', 'download-monitor' ),
					'CZK' => __( 'Czech Koruna', 'download-monitor' ),
					'DJF' => __( 'Djiboutian Franc', 'download-monitor' ),
					'DKK' => __( 'Danish Krone', 'download-monitor' ),
					'DOP' => __( 'Dominican Peso', 'download-monitor' ),
					'ETB' => __( 'Ethiopian Birr', 'download-monitor' ),
					'EGP' => __( 'Egyptian Pound', 'download-monitor' ),
					'EUR' => __( 'Euros', 'download-monitor' ),
					'GHS' => __( 'Ghanaian Cedi', 'download-monitor' ),
					'HKD' => __( 'Hong Kong Dollar', 'download-monitor' ),
					'HRK' => __( 'Croatia kuna', 'download-monitor' ),
					'HUF' => __( 'Hungarian Forint', 'download-monitor' ),
					'ISK' => __( 'Icelandic krona', 'download-monitor' ),
					'IDR' => __( 'Indonesia Rupiah', 'download-monitor' ),
					'INR' => __( 'Indian Rupee', 'download-monitor' ),
					'ILS' => __( 'Israeli Shekel', 'download-monitor' ),
					'IRR' => __( 'Iranian rial', 'download-monitor' ),
					'JPY' => __( 'Japanese Yen', 'download-monitor' ),
					'KES' => __( 'Kenyan Shilling', 'download-monitor' ),
					'KIP' => __( 'Lao Kip', 'download-monitor' ),
					'KRW' => __( 'South Korean Won', 'download-monitor' ),
					'MYR' => __( 'Malaysian Ringgits', 'download-monitor' ),
					'MXN' => __( 'Mexican Peso', 'download-monitor' ),
					'NGN' => __( 'Nigerian Naira', 'download-monitor' ),
					'NOK' => __( 'Norwegian Krone', 'download-monitor' ),
					'NPR' => __( 'Nepali Rupee', 'download-monitor' ),
					'NZD' => __( 'New Zealand Dollar', 'download-monitor' ),
					'PHP' => __( 'Philippine Pesos', 'download-monitor' ),
					'PKR' => __( 'Pakistani Rupee', 'download-monitor' ),
					'PLN' => __( 'Polish Zloty', 'download-monitor' ),
					'PYG' => __( 'Paraguayan Guaraní', 'download-monitor' ),
					'GBP' => __( 'Pounds Sterling', 'download-monitor' ),
					'RON' => __( 'Romanian Leu', 'download-monitor' ),
					'RUB' => __( 'Russian Ruble', 'download-monitor' ),
					'RWF' => __( 'Rwandan Franc', 'download-monitor' ),
					'SGD' => __( 'Singapore Dollar', 'download-monitor' ),
					'ZAR' => __( 'South African rand', 'download-monitor' ),
					'SEK' => __( 'Swedish Krona', 'download-monitor' ),
					'CHF' => __( 'Swiss Franc', 'download-monitor' ),
					'THB' => __( 'Thai Baht', 'download-monitor' ),
					'TND' => __( 'Tunisian Dinar', 'download-monitor' ),
					'TRY' => __( 'Turkish Lira', 'download-monitor' ),
					'TWD' => __( 'Taiwan New Dollars', 'download-monitor' ),
					'TZS' => __( 'Tanzanian Shilling', 'download-monitor' ),
					'UAH' => __( 'Ukrainian Hryvnia', 'download-monitor' ),
					'UGX' => __( 'Ugandan Shilling', 'download-monitor' ),
					'AED' => __( 'United Arab Emirates Dirham', 'download-monitor' ),
					'USD' => __( 'US Dollars', 'download-monitor' ),
					'VND' => __( 'Vietnamese Dong', 'download-monitor' )
				)
			)
		);
	}

	/**
	 * Returns the shop selected currency
	 *
	 * @return string
	 */
	public function get_shop_currency() {
		return download_monitor()->service( 'settings' )->get_option( 'currency' );

	}

	/**
	 * Get currency symbol of given currency.
	 * Uses setting's currency if no currency is given
	 *
	 * Forked from WP Car Manager's get_currency_symbol():
	 * https://github.com/barrykooij/wp-car-manager/blob/31225670959ee71dd2df3383aa013889a02c5aac/src/Helper/Currency.php#L22
	 *
	 * @param string $currency
	 *
	 * @return string
	 */
	public function get_currency_symbol( $currency = '' ) {
		// use default currency if currency arg not given
		if ( ! $currency ) {
			$currency = $this->get_shop_currency();
		}

		switch ( $currency ) {
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'BIF':
				$currency_symbol = 'FBu';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
				$currency_symbol = '&yen;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'DJF':
				$currency_symbol = 'Fdj';
				break;
			case 'DKK' :
				$currency_symbol = 'DKK';
				break;
			case 'DOP' :
				$currency_symbol = 'RD&#36;';
				break;
			case 'EGP' :
				$currency_symbol = 'EGP';
				break;
			case 'ETB':
				$currency_symbol = 'ETB';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'GHS':
				$currency_symbol = 'GH₵';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'INR' :
				$currency_symbol = 'Rs.';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr.';
				break;
			case 'IRR' :
				$currency_symbol = '﷼';
				break;
			case 'KES':
				$currency_symbol = 'KSh';
				break;
			case 'KIP' :
				$currency_symbol = '&#8365;';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'NPR' :
				$currency_symbol = 'Rs.';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PKR' :
				$currency_symbol = 'Rs.';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'PYG' :
				$currency_symbol = '&#8370;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'RWF':
				$currency_symbol = 'FRw';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'TND' :
				$currency_symbol = 'DT';
				break;
			case 'TRY' :
				$currency_symbol = '&#8378;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'TZS':
				$currency_symbol = 'TSh';
				break;
			case 'UAH' :
				$currency_symbol = '&#8372;';
				break;
			case 'UGX':
				$currency_symbol = 'USh';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'XAF':
				$currency_symbol = 'CFA';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			default :
				$currency_symbol = '';
				break;
		}

		return apply_filters( 'dlm_shop_currency_symbol', $currency_symbol, $currency );
	}

	/**
	 * Returns the position of the currency symbol
	 *
	 * @return mixed
	 */
	public function get_currency_position() {
		return download_monitor()->service( 'settings' )->get_option( 'currency_pos' );
	}

}