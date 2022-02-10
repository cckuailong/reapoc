<?php
/**
 * Handles all the Country handling for PayPal Gateway.
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */

namespace TEC\Tickets\Commerce\Gateways\PayPal\Location;

/**
 * Class Country
 *
 * @since 5.2.0
 *
 */
class Country {

	public static $country_option_key = 'tickets-commerce-gateway-paypal-merchant-country';

	/**
	 * Default two char string for the default country code.
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	const DEFAULT_COUNTRY_CODE = 'US';

	/**
	 * Get PayPal base country.
	 *
	 * @since 5.2.0
	 *
	 * @return string $country The two letter country code for the site's base country
	 */
	public function get_setting() {
		$country = tribe_get_option( static::$country_option_key );

		/**
		 * Fetches the country associated with the PayPal setting.
		 *
		 * @since 5.2.0
		 *
		 * @param string $country Two letter country code in the settings.
		 */
		$country = apply_filters( 'tec_tickets_commerce_gateway_paypal_country', $country );

		// Makes sure this value is always valid.
		if ( ! $this->is_valid( $country ) ) {
			return static::DEFAULT_COUNTRY_CODE;
		}

		return $country;
	}

	/**
	 * Saves the paypal base country to the Options array.
	 *
	 * @since 5.2.0
	 *
	 * @return boolean
	 */
	public function save_setting( $value ) {
		return tribe_update_option( static::$country_option_key, $value );
	}

	/**
	 * Determines based on a country code if it's valid.
	 *
	 * @since 5.2.0
	 *
	 * @param string $country
	 *
	 * @return bool
	 */
	public function is_valid( $country ) {
		if ( empty( $country ) ) {
			return false;
		}

		$countries = $this->get_list();
		if ( empty( $countries[ $country ] ) ) {
			return false;
		}

		return true;
	}

	public function get_list() {
		$us        = static::DEFAULT_COUNTRY_CODE;
		$countries = [
			''   => '',
			$us  => esc_html__( 'United States', 'event-tickets' ),
			'CA' => esc_html__( 'Canada', 'event-tickets' ),
			'GB' => esc_html__( 'United Kingdom', 'event-tickets' ),
			'AF' => esc_html__( 'Afghanistan', 'event-tickets' ),
			'AL' => esc_html__( 'Albania', 'event-tickets' ),
			'DZ' => esc_html__( 'Algeria', 'event-tickets' ),
			'AS' => esc_html__( 'American Samoa', 'event-tickets' ),
			'AD' => esc_html__( 'Andorra', 'event-tickets' ),
			'AO' => esc_html__( 'Angola', 'event-tickets' ),
			'AI' => esc_html__( 'Anguilla', 'event-tickets' ),
			'AQ' => esc_html__( 'Antarctica', 'event-tickets' ),
			'AG' => esc_html__( 'Antigua and Barbuda', 'event-tickets' ),
			'AR' => esc_html__( 'Argentina', 'event-tickets' ),
			'AM' => esc_html__( 'Armenia', 'event-tickets' ),
			'AW' => esc_html__( 'Aruba', 'event-tickets' ),
			'AU' => esc_html__( 'Australia', 'event-tickets' ),
			'AT' => esc_html__( 'Austria', 'event-tickets' ),
			'AZ' => esc_html__( 'Azerbaijan', 'event-tickets' ),
			'BS' => esc_html__( 'Bahamas', 'event-tickets' ),
			'BH' => esc_html__( 'Bahrain', 'event-tickets' ),
			'BD' => esc_html__( 'Bangladesh', 'event-tickets' ),
			'BB' => esc_html__( 'Barbados', 'event-tickets' ),
			'BY' => esc_html__( 'Belarus', 'event-tickets' ),
			'BE' => esc_html__( 'Belgium', 'event-tickets' ),
			'BZ' => esc_html__( 'Belize', 'event-tickets' ),
			'BJ' => esc_html__( 'Benin', 'event-tickets' ),
			'BM' => esc_html__( 'Bermuda', 'event-tickets' ),
			'BT' => esc_html__( 'Bhutan', 'event-tickets' ),
			'BO' => esc_html__( 'Bolivia', 'event-tickets' ),
			'BA' => esc_html__( 'Bosnia and Herzegovina', 'event-tickets' ),
			'BW' => esc_html__( 'Botswana', 'event-tickets' ),
			'BV' => esc_html__( 'Bouvet Island', 'event-tickets' ),
			'BR' => esc_html__( 'Brazil', 'event-tickets' ),
			'IO' => esc_html__( 'British Indian Ocean Territory', 'event-tickets' ),
			'BN' => esc_html__( 'Brunei Darrussalam', 'event-tickets' ),
			'BG' => esc_html__( 'Bulgaria', 'event-tickets' ),
			'BF' => esc_html__( 'Burkina Faso', 'event-tickets' ),
			'BI' => esc_html__( 'Burundi', 'event-tickets' ),
			'KH' => esc_html__( 'Cambodia', 'event-tickets' ),
			'CM' => esc_html__( 'Cameroon', 'event-tickets' ),
			'CV' => esc_html__( 'Cape Verde', 'event-tickets' ),
			'KY' => esc_html__( 'Cayman Islands', 'event-tickets' ),
			'CF' => esc_html__( 'Central African Republic', 'event-tickets' ),
			'TD' => esc_html__( 'Chad', 'event-tickets' ),
			'CL' => esc_html__( 'Chile', 'event-tickets' ),
			'CN' => esc_html__( 'China', 'event-tickets' ),
			'CX' => esc_html__( 'Christmas Island', 'event-tickets' ),
			'CC' => esc_html__( 'Cocos Islands', 'event-tickets' ),
			'CO' => esc_html__( 'Colombia', 'event-tickets' ),
			'KM' => esc_html__( 'Comoros', 'event-tickets' ),
			'CD' => esc_html__( 'Congo, Democratic People\'s Republic', 'event-tickets' ),
			'CG' => esc_html__( 'Congo, Republic of', 'event-tickets' ),
			'CK' => esc_html__( 'Cook Islands', 'event-tickets' ),
			'CR' => esc_html__( 'Costa Rica', 'event-tickets' ),
			'CI' => esc_html__( 'Cote d\'Ivoire', 'event-tickets' ),
			'HR' => esc_html__( 'Croatia/Hrvatska', 'event-tickets' ),
			'CU' => esc_html__( 'Cuba', 'event-tickets' ),
			'CY' => esc_html__( 'Cyprus Island', 'event-tickets' ),
			'CZ' => esc_html__( 'Czech Republic', 'event-tickets' ),
			'DK' => esc_html__( 'Denmark', 'event-tickets' ),
			'DJ' => esc_html__( 'Djibouti', 'event-tickets' ),
			'DM' => esc_html__( 'Dominica', 'event-tickets' ),
			'DO' => esc_html__( 'Dominican Republic', 'event-tickets' ),
			'TP' => esc_html__( 'East Timor', 'event-tickets' ),
			'EC' => esc_html__( 'Ecuador', 'event-tickets' ),
			'EG' => esc_html__( 'Egypt', 'event-tickets' ),
			'GQ' => esc_html__( 'Equatorial Guinea', 'event-tickets' ),
			'SV' => esc_html__( 'El Salvador', 'event-tickets' ),
			'ER' => esc_html__( 'Eritrea', 'event-tickets' ),
			'EE' => esc_html__( 'Estonia', 'event-tickets' ),
			'ET' => esc_html__( 'Ethiopia', 'event-tickets' ),
			'FK' => esc_html__( 'Falkland Islands', 'event-tickets' ),
			'FO' => esc_html__( 'Faroe Islands', 'event-tickets' ),
			'FJ' => esc_html__( 'Fiji', 'event-tickets' ),
			'FI' => esc_html__( 'Finland', 'event-tickets' ),
			'FR' => esc_html__( 'France', 'event-tickets' ),
			'GF' => esc_html__( 'French Guiana', 'event-tickets' ),
			'PF' => esc_html__( 'French Polynesia', 'event-tickets' ),
			'TF' => esc_html__( 'French Southern Territories', 'event-tickets' ),
			'GA' => esc_html__( 'Gabon', 'event-tickets' ),
			'GM' => esc_html__( 'Gambia', 'event-tickets' ),
			'GE' => esc_html__( 'Georgia', 'event-tickets' ),
			'DE' => esc_html__( 'Germany', 'event-tickets' ),
			'GR' => esc_html__( 'Greece', 'event-tickets' ),
			'GH' => esc_html__( 'Ghana', 'event-tickets' ),
			'GI' => esc_html__( 'Gibraltar', 'event-tickets' ),
			'GL' => esc_html__( 'Greenland', 'event-tickets' ),
			'GD' => esc_html__( 'Grenada', 'event-tickets' ),
			'GP' => esc_html__( 'Guadeloupe', 'event-tickets' ),
			'GU' => esc_html__( 'Guam', 'event-tickets' ),
			'GT' => esc_html__( 'Guatemala', 'event-tickets' ),
			'GG' => esc_html__( 'Guernsey', 'event-tickets' ),
			'GN' => esc_html__( 'Guinea', 'event-tickets' ),
			'GW' => esc_html__( 'Guinea-Bissau', 'event-tickets' ),
			'GY' => esc_html__( 'Guyana', 'event-tickets' ),
			'HT' => esc_html__( 'Haiti', 'event-tickets' ),
			'HM' => esc_html__( 'Heard and McDonald Islands', 'event-tickets' ),
			'VA' => esc_html__( 'Holy See (City Vatican State)', 'event-tickets' ),
			'HN' => esc_html__( 'Honduras', 'event-tickets' ),
			'HK' => esc_html__( 'Hong Kong', 'event-tickets' ),
			'HU' => esc_html__( 'Hungary', 'event-tickets' ),
			'IS' => esc_html__( 'Iceland', 'event-tickets' ),
			'IN' => esc_html__( 'India', 'event-tickets' ),
			'ID' => esc_html__( 'Indonesia', 'event-tickets' ),
			'IR' => esc_html__( 'Iran', 'event-tickets' ),
			'IQ' => esc_html__( 'Iraq', 'event-tickets' ),
			'IE' => esc_html__( 'Ireland', 'event-tickets' ),
			'IM' => esc_html__( 'Isle of Man', 'event-tickets' ),
			'IL' => esc_html__( 'Israel', 'event-tickets' ),
			'IT' => esc_html__( 'Italy', 'event-tickets' ),
			'JM' => esc_html__( 'Jamaica', 'event-tickets' ),
			'JP' => esc_html__( 'Japan', 'event-tickets' ),
			'JE' => esc_html__( 'Jersey', 'event-tickets' ),
			'JO' => esc_html__( 'Jordan', 'event-tickets' ),
			'KZ' => esc_html__( 'Kazakhstan', 'event-tickets' ),
			'KE' => esc_html__( 'Kenya', 'event-tickets' ),
			'KI' => esc_html__( 'Kiribati', 'event-tickets' ),
			'KW' => esc_html__( 'Kuwait', 'event-tickets' ),
			'KG' => esc_html__( 'Kyrgyzstan', 'event-tickets' ),
			'LA' => esc_html__( 'Lao People\'s Democratic Republic', 'event-tickets' ),
			'LV' => esc_html__( 'Latvia', 'event-tickets' ),
			'LB' => esc_html__( 'Lebanon', 'event-tickets' ),
			'LS' => esc_html__( 'Lesotho', 'event-tickets' ),
			'LR' => esc_html__( 'Liberia', 'event-tickets' ),
			'LY' => esc_html__( 'Libyan Arab Jamahiriya', 'event-tickets' ),
			'LI' => esc_html__( 'Liechtenstein', 'event-tickets' ),
			'LT' => esc_html__( 'Lithuania', 'event-tickets' ),
			'LU' => esc_html__( 'Luxembourg', 'event-tickets' ),
			'MO' => esc_html__( 'Macau', 'event-tickets' ),
			'MK' => esc_html__( 'Macedonia', 'event-tickets' ),
			'MG' => esc_html__( 'Madagascar', 'event-tickets' ),
			'MW' => esc_html__( 'Malawi', 'event-tickets' ),
			'MY' => esc_html__( 'Malaysia', 'event-tickets' ),
			'MV' => esc_html__( 'Maldives', 'event-tickets' ),
			'ML' => esc_html__( 'Mali', 'event-tickets' ),
			'MT' => esc_html__( 'Malta', 'event-tickets' ),
			'MH' => esc_html__( 'Marshall Islands', 'event-tickets' ),
			'MQ' => esc_html__( 'Martinique', 'event-tickets' ),
			'MR' => esc_html__( 'Mauritania', 'event-tickets' ),
			'MU' => esc_html__( 'Mauritius', 'event-tickets' ),
			'YT' => esc_html__( 'Mayotte', 'event-tickets' ),
			'MX' => esc_html__( 'Mexico', 'event-tickets' ),
			'FM' => esc_html__( 'Micronesia', 'event-tickets' ),
			'MD' => esc_html__( 'Moldova, Republic of', 'event-tickets' ),
			'MC' => esc_html__( 'Monaco', 'event-tickets' ),
			'MN' => esc_html__( 'Mongolia', 'event-tickets' ),
			'ME' => esc_html__( 'Montenegro', 'event-tickets' ),
			'MS' => esc_html__( 'Montserrat', 'event-tickets' ),
			'MA' => esc_html__( 'Morocco', 'event-tickets' ),
			'MZ' => esc_html__( 'Mozambique', 'event-tickets' ),
			'MM' => esc_html__( 'Myanmar', 'event-tickets' ),
			'NA' => esc_html__( 'Namibia', 'event-tickets' ),
			'NR' => esc_html__( 'Nauru', 'event-tickets' ),
			'NP' => esc_html__( 'Nepal', 'event-tickets' ),
			'NL' => esc_html__( 'Netherlands', 'event-tickets' ),
			'AN' => esc_html__( 'Netherlands Antilles', 'event-tickets' ),
			'NC' => esc_html__( 'New Caledonia', 'event-tickets' ),
			'NZ' => esc_html__( 'New Zealand', 'event-tickets' ),
			'NI' => esc_html__( 'Nicaragua', 'event-tickets' ),
			'NE' => esc_html__( 'Niger', 'event-tickets' ),
			'NG' => esc_html__( 'Nigeria', 'event-tickets' ),
			'NU' => esc_html__( 'Niue', 'event-tickets' ),
			'NF' => esc_html__( 'Norfolk Island', 'event-tickets' ),
			'KP' => esc_html__( 'North Korea', 'event-tickets' ),
			'MP' => esc_html__( 'Northern Mariana Islands', 'event-tickets' ),
			'NO' => esc_html__( 'Norway', 'event-tickets' ),
			'OM' => esc_html__( 'Oman', 'event-tickets' ),
			'PK' => esc_html__( 'Pakistan', 'event-tickets' ),
			'PW' => esc_html__( 'Palau', 'event-tickets' ),
			'PS' => esc_html__( 'Palestinian Territories', 'event-tickets' ),
			'PA' => esc_html__( 'Panama', 'event-tickets' ),
			'PG' => esc_html__( 'Papua New Guinea', 'event-tickets' ),
			'PY' => esc_html__( 'Paraguay', 'event-tickets' ),
			'PE' => esc_html__( 'Peru', 'event-tickets' ),
			'PH' => esc_html__( 'Philippines', 'event-tickets' ),
			'PN' => esc_html__( 'Pitcairn Island', 'event-tickets' ),
			'PL' => esc_html__( 'Poland', 'event-tickets' ),
			'PT' => esc_html__( 'Portugal', 'event-tickets' ),
			'PR' => esc_html__( 'Puerto Rico', 'event-tickets' ),
			'QA' => esc_html__( 'Qatar', 'event-tickets' ),
			'RE' => esc_html__( 'Reunion Island', 'event-tickets' ),
			'RO' => esc_html__( 'Romania', 'event-tickets' ),
			'RU' => esc_html__( 'Russian Federation', 'event-tickets' ),
			'RW' => esc_html__( 'Rwanda', 'event-tickets' ),
			'SH' => esc_html__( 'Saint Helena', 'event-tickets' ),
			'KN' => esc_html__( 'Saint Kitts and Nevis', 'event-tickets' ),
			'LC' => esc_html__( 'Saint Lucia', 'event-tickets' ),
			'PM' => esc_html__( 'Saint Pierre and Miquelon', 'event-tickets' ),
			'VC' => esc_html__( 'Saint Vincent and the Grenadines', 'event-tickets' ),
			'SM' => esc_html__( 'San Marino', 'event-tickets' ),
			'ST' => esc_html__( 'Sao Tome and Principe', 'event-tickets' ),
			'SA' => esc_html__( 'Saudi Arabia', 'event-tickets' ),
			'SN' => esc_html__( 'Senegal', 'event-tickets' ),
			'RS' => esc_html__( 'Serbia', 'event-tickets' ),
			'SC' => esc_html__( 'Seychelles', 'event-tickets' ),
			'SL' => esc_html__( 'Sierra Leone', 'event-tickets' ),
			'SG' => esc_html__( 'Singapore', 'event-tickets' ),
			'SK' => esc_html__( 'Slovak Republic', 'event-tickets' ),
			'SI' => esc_html__( 'Slovenia', 'event-tickets' ),
			'SB' => esc_html__( 'Solomon Islands', 'event-tickets' ),
			'SO' => esc_html__( 'Somalia', 'event-tickets' ),
			'ZA' => esc_html__( 'South Africa', 'event-tickets' ),
			'GS' => esc_html__( 'South Georgia', 'event-tickets' ),
			'KR' => esc_html__( 'South Korea', 'event-tickets' ),
			'ES' => esc_html__( 'Spain', 'event-tickets' ),
			'LK' => esc_html__( 'Sri Lanka', 'event-tickets' ),
			'SD' => esc_html__( 'Sudan', 'event-tickets' ),
			'SR' => esc_html__( 'Suriname', 'event-tickets' ),
			'SJ' => esc_html__( 'Svalbard and Jan Mayen Islands', 'event-tickets' ),
			'SZ' => esc_html__( 'Eswatini', 'event-tickets' ),
			'SE' => esc_html__( 'Sweden', 'event-tickets' ),
			'CH' => esc_html__( 'Switzerland', 'event-tickets' ),
			'SY' => esc_html__( 'Syrian Arab Republic', 'event-tickets' ),
			'TW' => esc_html__( 'Taiwan', 'event-tickets' ),
			'TJ' => esc_html__( 'Tajikistan', 'event-tickets' ),
			'TZ' => esc_html__( 'Tanzania', 'event-tickets' ),
			'TG' => esc_html__( 'Togo', 'event-tickets' ),
			'TK' => esc_html__( 'Tokelau', 'event-tickets' ),
			'TO' => esc_html__( 'Tonga', 'event-tickets' ),
			'TH' => esc_html__( 'Thailand', 'event-tickets' ),
			'TT' => esc_html__( 'Trinidad and Tobago', 'event-tickets' ),
			'TN' => esc_html__( 'Tunisia', 'event-tickets' ),
			'TR' => esc_html__( 'Turkey', 'event-tickets' ),
			'TM' => esc_html__( 'Turkmenistan', 'event-tickets' ),
			'TC' => esc_html__( 'Turks and Caicos Islands', 'event-tickets' ),
			'TV' => esc_html__( 'Tuvalu', 'event-tickets' ),
			'UG' => esc_html__( 'Uganda', 'event-tickets' ),
			'UA' => esc_html__( 'Ukraine', 'event-tickets' ),
			'AE' => esc_html__( 'United Arab Emirates', 'event-tickets' ),
			'UY' => esc_html__( 'Uruguay', 'event-tickets' ),
			'UM' => esc_html__( 'US Minor Outlying Islands', 'event-tickets' ),
			'UZ' => esc_html__( 'Uzbekistan', 'event-tickets' ),
			'VU' => esc_html__( 'Vanuatu', 'event-tickets' ),
			'VE' => esc_html__( 'Venezuela', 'event-tickets' ),
			'VN' => esc_html__( 'Vietnam', 'event-tickets' ),
			'VG' => esc_html__( 'Virgin Islands (British)', 'event-tickets' ),
			'VI' => esc_html__( 'Virgin Islands (USA)', 'event-tickets' ),
			'WF' => esc_html__( 'Wallis and Futuna Islands', 'event-tickets' ),
			'EH' => esc_html__( 'Western Sahara', 'event-tickets' ),
			'WS' => esc_html__( 'Western Samoa', 'event-tickets' ),
			'YE' => esc_html__( 'Yemen', 'event-tickets' ),
			'YU' => esc_html__( 'Yugoslavia', 'event-tickets' ),
			'ZM' => esc_html__( 'Zambia', 'event-tickets' ),
			'ZW' => esc_html__( 'Zimbabwe', 'event-tickets' ),
		];

		/**
		 * Allows filtering of the available countries for PayPal gateway on Tickets Commerce.
		 *
		 * Using the two-character ISO-3166-1 code for their index.
		 *
		 * @since 5.2.0
		 *
		 * @param array $countries Which countries are available.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_countries', $countries );
	}

	/**
	 * Get Country List without postal/zip codes.
	 *
	 * @since 5.2.0
	 *
	 * @return string[] $countries A list of countries without postal/zip codes.
	 */
	public function get_list_without_postcodes() {
		$countries = [
			'AO',
			'AG',
			'AW',
			'BS',
			'BZ',
			'BJ',
			'BW',
			'BF',
			'BI',
			'CM',
			'CF',
			'KM',
			'CD',
			'CG',
			'CK',
			'CI',
			'DJ',
			'DM',
			'GQ',
			'ER',
			'FJ',
			'TF',
			'GM',
			'GH',
			'GD',
			'GN',
			'GY',
			'HK',
			'IE',
			'JM',
			'KE',
			'KI',
			'MO',
			'MW',
			'ML',
			'MR',
			'MU',
			'MS',
			'NR',
			'AN',
			'NU',
			'KP',
			'PA',
			'QA',
			'RW',
			'KN',
			'LC',
			'ST',
			'SC',
			'SL',
			'SB',
			'SO',
			'ZA',
			'SR',
			'SY',
			'TZ',
			'TK',
			'TO',
			'TT',
			'TV',
			'UG',
			'AE',
			'VU',
			'YE',
			'ZW',
		];

		/**
		 * Filter list of countries codes that do not require postal/zip codes.
		 *
		 * @since 5.2.0
		 *
		 * @param string[] $countries List of the countries.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_countries_no_postcode', $countries );
	}

	/**
	 * Get country locale settings.
	 *
	 * @since 5.2.0
	 *
	 * @return array
	 */
	public function get_list_locale() {
		/**
		 * Filter list of countries locale settings, which is used to determine how certain fields are displayed.
		 *
		 * @since 5.2.0
		 *
		 * @param string[] $countries List of the countries.
		 */
		return (array) apply_filters(
			'tec_tickets_commerce_gateway_paypal_countries_locale',
			[
				'AE' => [
					'state' => [
						'required' => false,
					],
				],
				'AF' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'AT' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'AU' => [
					'state' => [
						'label' => __( 'State', 'event-tickets' ),
					],
				],
				'AX' => [
					'state' => [
						'required' => false,
					],
				],
				'BD' => [
					'state' => [
						'label' => __( 'District', 'event-tickets' ),
					],
				],
				'BE' => [
					'state' => [
						'required' => false,
						'label'    => __( 'Province', 'event-tickets' ),
						'hidden'   => true,
					],
				],
				'BI' => [
					'state' => [
						'required' => false,
					],
				],
				'CA' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'CH' => [
					'state' => [
						'label'    => __( 'Canton', 'event-tickets' ),
						'required' => false,
						'hidden'   => true,
					],
				],
				'CL' => [
					'state' => [
						'label' => __( 'Region', 'event-tickets' ),
					],
				],
				'CN' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'CZ' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'DE' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'DK' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'EE' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'FI' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'FR' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'GP' => [
					'state' => [
						'required' => false,
					],
				],
				'GF' => [
					'state' => [
						'required' => false,
					],
				],
				'HK' => [
					'state' => [
						'label' => __( 'Region', 'event-tickets' ),
					],
				],
				'HU' => [
					'state' => [
						'label'  => __( 'County', 'event-tickets' ),
						'hidden' => true,
					],
				],
				'ID' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'IE' => [
					'state' => [
						'label' => __( 'County', 'event-tickets' ),
					],
				],
				'IS' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'IL' => [
					'state' => [
						'required' => false,
					],
				],
				'IT' => [
					'state' => [
						'required' => true,
						'label'    => __( 'Province', 'event-tickets' ),
					],
				],
				'JP' => [
					'state' => [
						'label' => __( 'Prefecture', 'event-tickets' ),
					],
				],
				'KR' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'KW' => [
					'state' => [
						'required' => false,
					],
				],
				'LB' => [
					'state' => [
						'required' => false,
					],
				],
				'MC' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'MQ' => [
					'state' => [
						'required' => false,
					],
				],
				'NL' => [
					'state' => [
						'required' => false,
						'label'    => __( 'Province', 'event-tickets' ),
						'hidden'   => true,
					],
				],
				'NZ' => [
					'state' => [
						'label' => __( 'Region', 'event-tickets' ),
					],
				],
				'NO' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'NP' => [
					'state' => [
						'label' => __( 'State / Zone', 'event-tickets' ),
					],
				],
				'PL' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'PT' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'RE' => [
					'state' => [
						'required' => false,
					],
				],
				'RO' => [
					'state' => [
						'required' => false,
					],
				],
				'SG' => [
					'state' => [
						'required' => false,
					],
					'city'  => [
						'required' => false,
					],
				],
				'SK' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'SI' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'ES' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'LI' => [
					'state' => [
						'label'    => __( 'Municipality', 'event-tickets' ),
						'required' => false,
						'hidden'   => true,
					],
				],
				'LK' => [
					'state' => [
						'required' => false,
					],
				],
				'SE' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'TR' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'US' => [
					'state' => [
						'label' => __( 'State', 'event-tickets' ),
					],
				],
				'GB' => [
					'state' => [
						'label'    => __( 'County', 'event-tickets' ),
						'required' => false,
					],
				],
				'VN' => [
					'state' => [
						'required' => false,
						'hidden'   => true,
					],
				],
				'YT' => [
					'state' => [
						'required' => false,
					],
				],
				'ZA' => [
					'state' => [
						'label' => __( 'Province', 'event-tickets' ),
					],
				],
				'PA' => [
					'state' => [
						'required' => true,
					],
				],
			]
		);
	}

	/**
	 * List of Country that have no states init.
	 *
	 * There are some country which does not have states init Example: germany.
	 *
	 * @since 5.2.0
	 *
	 * @return array $country_list
	 */
	public function list_no_states() {
		$country_list = [];
		$locale       = $this->get_list_locale();
		foreach ( $locale as $key => $value ) {
			if ( ! empty( $value['state'] ) && isset( $value['state']['hidden'] ) && true === $value['state']['hidden'] ) {
				$country_list[ $key ] = $value['state'];
			}
		}

		/**
		 * Filter can be used to add or remove the Country that does not have states init.
		 *
		 * @since 5.2.0
		 *
		 * @param array $country Contain key as there country code & value as there country name.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_countries_no_states', $country_list );
	}

	/**
	 * List of Country in which states fields is not required.
	 *
	 * There are some country in which states fields is not required Example: United Kingdom ( uk ).
	 *
	 * @since 5.2.0
	 *
	 * @return array $country_list
	 */
	public function list_states_not_required() {
		$country_list = [];
		$locale       = $this->get_list_locale();
		foreach ( $locale as $key => $value ) {
			if ( ! empty( $value['state'] ) && isset( $value['state']['required'] ) && false === $value['state']['required'] ) {
				$country_list[ $key ] = $value['state'];
			}
		}

		/**
		 * Filter can be used to add or remove the Country in which states fields is not required.
		 *
		 * @since 5.2.0
		 *
		 * @param array $country Contain key as there country code & value as there country name.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_countries_states_not_required', $country_list );
	}

	/**
	 * List of Country in which city fields is not required.
	 *
	 * There are some country in which city fields is not required Example: Singapore ( sk ).
	 *
	 * @since 5.2.0
	 *
	 * @return array $country_list
	 */
	public function list_city_not_required() {
		$country_list = [];
		$locale       = $this->get_list_locale();
		foreach ( $locale as $key => $value ) {
			if ( ! empty( $value['city'] ) && isset( $value['city']['required'] ) && false === $value['city']['required'] ) {
				$country_list[ $key ] = $value['city'];
			}
		}

		/**
		 * Filter can be used to add or remove the Country in which city fields is not required.
		 *
		 * @since 5.2.0
		 *
		 * @param array $country_list Contain key as there country code & value as there country name.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_gateway_paypal_countries_city_not_required', $country_list );
	}
}
