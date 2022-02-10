<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Cash_Payment_Gateway class
 * @see http://codex.mycred.me/classes/myCRED_Cash_Payment_Gateway/
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_Cash_Payment_Gateway' ) ) :
	abstract class myCRED_Cash_Payment_Gateway {

		/**
		 * The Gateways Unique ID
		 */
		public $id                = false;

		/**
		 * Gateway Label
		 */
		public $label             = '';

		/**
		 * Indicates if the gateway is operating in sandbox mode or not
		 */
		public $sandbox_mode      = false;

		/**
		 * Gateways Settings
		 */
		public $prefs             = false;

		/**
		 * Main Point Type Settings
		 */
		public $core;

		/**
		 * The current users ID
		 */
		public $current_user_id   = 0;

		protected $errors         = array();

		/**
		 * Construct
		 */
		public function __construct( $args = array(), $gateway_prefs = NULL ) {

			// Make sure gateway prefs is set
			if ( $gateway_prefs === NULL ) return;

			// Populate
			$this->current_user_id  = get_current_user_id();
			$this->core             = mycred();

			// Arguments
			if ( ! empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					$this->$key = $value;
				}
			}

			$gateway_settings       = $this->defaults;
			if ( is_array( $gateway_prefs ) && array_key_exists( $this->id, $gateway_prefs ) )
				$gateway_settings = $gateway_prefs[ $this->id ];

			elseif ( is_object( $gateway_prefs ) && array_key_exists( $this->id, $gateway_prefs->gateway_prefs ) )
				$gateway_settings = $gateway_prefs->gateway_prefs[ $this->id ];

			$this->prefs            = shortcode_atts( $this->defaults, $gateway_settings );

			// Sandbox Mode
			$this->sandbox_mode     = ( isset( $this->prefs['sandbox'] ) ) ? (bool) $this->prefs['sandbox'] : false;

		}

		/**
		 * Process Purchase
		 * @since 0.1
		 * @version 1.0
		 */
		public function process($post = false) { }

		/**
		 * Results Handler
		 * @since 0.1
		 * @version 1.0
		 */
		public function returning() { }

		/**
		 * Admin Init Handler
		 * @since 1.7
		 * @version 1.0
		 */
		public function admin_init() { }

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.0
		 */
		public function preferences() {

			echo '<p>This Payment Gateway has no settings</p>';

		}

		/**
		 * Sanatize Prefs
		 * @since 0.1
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			return $data;

		}

		/**
		 * Exchange Rate Setup
		 * @since 1.5
		 * @version 1.1
		 */
		public function exchange_rate_setup( $default = 'USD' ) {

			if ( ! isset( $this->prefs['exchange'] ) ) return;

			$content     = '';
			$point_types = mycred_get_types();
 
			foreach ( $point_types as $type_id => $label ) {

				$mycred = mycred( $type_id );

				if ( ! isset( $this->prefs['exchange'][ $type_id ] ) )
					$this->prefs['exchange'][ $type_id ] = 1;

				$content .= '
<table>
	<tr>
		<td style="min-width: 100px;"><div class="form-control-static">1 ' . esc_html( $mycred->singular() ) . '</div></td>
		<td style="width: 10px;"><div class="form-control-static">=</div></td>
		<td><input type="text" name="' . $this->field_name( array( 'exchange' => $type_id ) ) . '" id="' . $this->field_id( array( 'exchange' => $type_id ) ) . '" value="' . esc_attr( $this->prefs['exchange'][ $type_id ] ) . '" size="8" /> ';


		if ( isset( $this->prefs['currency'] ) )
			$content .= '<span class="mycred-gateway-' . $this->id . '-currency">' . ( ( $this->prefs['currency'] == '' ) ? __( 'Select currency', 'mycred' ) : esc_attr( $this->prefs['currency'] ) ) . '</span>';

		else
			$content .= '<span>' . esc_attr( $default ) . '</span>';

		$content .= '</td>
	</tr>
</table>';

			}

			echo apply_filters( 'mycred_cashcred_exchange_rate_field', $content, $default, $this );

		}

		/**
		 * Get Field Name
		 * Returns the field name for the current gateway
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_name( $field = '' ) {

			if ( is_array( $field ) ) {

				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '-', '_', $parent );

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = str_replace( '-', '_', $child );
				}
				$field = '[' . implode( '][', $array ) . ']';

			}
			else {

				$field = '[' . $field . ']';

			}

			return 'mycred_pref_cashcreds[gateway_prefs][' . $this->id . ']' . $field;

		}

		/**
		 * Get Field ID
		 * Returns the field id for the current gateway
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_id( $field = '' ) {

			if ( is_array( $field ) ) {

				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '_', '-', $parent );

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = str_replace( '_', '-', $child );
				}
				$field = implode( '-', $array );

			}
			else {

				$field = str_replace( '_', '-', $field );

			}

			return 'mycred-gateway-prefs-' . str_replace( '_', '-', $this->id ) . '-' . $field;

		}

		/**
		 * Get Errors
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_errors() {

			if ( empty( $this->errors ) ) return;

			$errors = array();
			foreach ( $this->errors as $form_field => $error_message )
				$errors[] = $error_message;

?>
<div class="gateway-error"><?php echo implode( '<br />', $errors ); ?></div>
<?php

		}

		/**
		 * Currencies Dropdown
		 * @since 0.1
		 * @version 1.0.2
		 */
		public function currencies_dropdown( $name = '', $js = '' ) {
			$currencies = [
                'AUD'		=>	'Australian dollar',
                'BRL'		=>	'Brazilian real',
                'CAD'		=>	'Canadian dollar',
                'CNY'		=>	'Chinese Renmenbi',
                'CZK'		=>	'Czech koruna',
                'DKK'		=>	'Danish krone',
                'EUR'		=>	'Euro',
                'HKD'		=>	'Hong Kong dollar',
                'HUF'		=>	'Hungarian forint',
                'INR'		=>	'Indian rupee',
                'ILS'		=>	'Israeli new shekel',
                'JPY'		=>	'Japanese yen',
                'MYR'		=>	'Malaysian ringgit',
                'MXN'		=>	'Mexican peso',
                'TWD'		=>	'New Taiwan dollar',
                'NZD'		=>	'New Zealand dollar',
                'NOK'		=>	'Norwegian krone',
                'PHP'		=>	'Philippine peso',
                'PLN'		=>	'Polish złoty',
                'GBP'		=>	'Pound sterling',
                'RUB'		=>	'Russian ruble',
                'SGD'		=>	'Singapore dollar',
                'SEK'		=>	'Swedish krona',
                'CHF'		=>	'Swiss franc',
                'THB'		=>	'Thai baht',
                'USD'		=>	'United States dollar'
            ];

			$currencies = apply_filters( 'mycred_dropdown_currencies', $currencies, $this->id );
			$currencies = apply_filters( 'mycred_dropdown_currencies_' . $this->id, $currencies );

			if ( $js != '' )
				$js = ' data-update="' . $js . '"';

			echo '<select name="' . $this->field_name( $name ) . '" id="' . $this->field_id( $name ) . '" class="currency form-control"' . $js . '>';
			echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';
			foreach ( $currencies as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( isset( $this->prefs[ $name ] ) && $this->prefs[ $name ] == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			echo '</select>';

		}

		/**
		 * Item Type Dropdown
		 * @since 0.1
		 * @version 1.0
		 */
		public function item_types_dropdown( $name = '' ) {

			$types = array(
				'product'  => 'Product',
				'service'  => 'Service',
				'donation' => 'Donation'
			);
			$types = apply_filters( 'mycred_dropdown_item_types', $types );

			echo '<select name="' . $this->field_name( $name ) . '" id="' . $this->field_id( $name ) . '">';
			echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';
			foreach ( $types as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( isset( $this->prefs[ $name ] ) && $this->prefs[ $name ] == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			echo '</select>';

		}

		/**
		 * Countries Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_countries( $selected = '' ) {

			$countries = array (
				"US"  =>  "UNITED STATES",
				"AF"  =>  "AFGHANISTAN",
				"AL"  =>  "ALBANIA",
				"DZ"  =>  "ALGERIA",
				"AS"  =>  "AMERICAN SAMOA",
				"AD"  =>  "ANDORRA",
				"AO"  =>  "ANGOLA",
				"AI"  =>  "ANGUILLA",
				"AQ"  =>  "ANTARCTICA",
				"AG"  =>  "ANTIGUA AND BARBUDA",
				"AR"  =>  "ARGENTINA",
				"AM"  =>  "ARMENIA",
				"AW"  =>  "ARUBA",
				"AU"  =>  "AUSTRALIA",
				"AT"  =>  "AUSTRIA",
				"AZ"  =>  "AZERBAIJAN",
				"BS"  =>  "BAHAMAS",
				"BH"  =>  "BAHRAIN",
				"BD"  =>  "BANGLADESH",
				"BB"  =>  "BARBADOS",
				"BY"  =>  "BELARUS",
				"BE"  =>  "BELGIUM",
				"BZ"  =>  "BELIZE",
				"BJ"  =>  "BENIN",
				"BM"  =>  "BERMUDA",
				"BT"  =>  "BHUTAN",
				"BO"  =>  "BOLIVIA",
				"BA"  =>  "BOSNIA AND HERZEGOVINA",
				"BW"  =>  "BOTSWANA",
				"BV"  =>  "BOUVET ISLAND",
				"BR"  =>  "BRAZIL",
				"IO"  =>  "BRITISH INDIAN OCEAN TERRITORY",
				"BN"  =>  "BRUNEI DARUSSALAM",
				"BG"  =>  "BULGARIA",
				"BF"  =>  "BURKINA FASO",
				"BI"  =>  "BURUNDI",
				"KH"  =>  "CAMBODIA",
				"CM"  =>  "CAMEROON",
				"CA"  =>  "CANADA",
				"CV"  =>  "CAPE VERDE",
				"KY"  =>  "CAYMAN ISLANDS",
				"CF"  =>  "CENTRAL AFRICAN REPUBLIC",
				"TD"  =>  "CHAD",
				"CL"  =>  "CHILE",
				"CN"  =>  "CHINA",
				"CX"  =>  "CHRISTMAS ISLAND",
				"CC"  =>  "COCOS (KEELING) ISLANDS",
				"CO"  =>  "COLOMBIA",
				"KM"  =>  "COMOROS",
				"CG"  =>  "CONGO",
				"CD"  =>  "CONGO, THE DEMOCRATIC REPUBLIC OF THE",
				"CK"  =>  "COOK ISLANDS",
				"CR"  =>  "COSTA RICA",
				"CI"  =>  "COTE D'IVOIRE",
				"HR"  =>  "CROATIA",
				"CU"  =>  "CUBA",
				"CY"  =>  "CYPRUS",
				"CZ"  =>  "CZECH REPUBLIC",
				"DK"  =>  "DENMARK",
				"DJ"  =>  "DJIBOUTI",
				"DM"  =>  "DOMINICA",
				"DO"  =>  "DOMINICAN REPUBLIC",
				"EC"  =>  "ECUADOR",
				"EG"  =>  "EGYPT",
				"SV"  =>  "EL SALVADOR",
				"GQ"  =>  "EQUATORIAL GUINEA",
				"ER"  =>  "ERITREA",
				"EE"  =>  "ESTONIA",
				"ET"  =>  "ETHIOPIA",
				"FK"  =>  "FALKLAND ISLANDS (MALVINAS)",
				"FO"  =>  "FAROE ISLANDS",
				"FJ"  =>  "FIJI",
				"FI"  =>  "FINLAND",
				"FR"  =>  "FRANCE",
				"GF"  =>  "FRENCH GUIANA",
				"PF"  =>  "FRENCH POLYNESIA",
				"TF"  =>  "FRENCH SOUTHERN TERRITORIES",
				"GA"  =>  "GABON",
				"GM"  =>  "GAMBIA",
				"GE"  =>  "GEORGIA",
				"DE"  =>  "GERMANY",
				"GH"  =>  "GHANA",
				"GI"  =>  "GIBRALTAR",
				"GR"  =>  "GREECE",
				"GL"  =>  "GREENLAND",
				"GD"  =>  "GRENADA",
				"GP"  =>  "GUADELOUPE",
				"GU"  =>  "GUAM",
				"GT"  =>  "GUATEMALA",
				"GN"  =>  "GUINEA",
				"GW"  =>  "GUINEA-BISSAU",
				"GY"  =>  "GUYANA",
				"HT"  =>  "HAITI",
				"HM"  =>  "HEARD ISLAND AND MCDONALD ISLANDS",
				"VA"  =>  "HOLY SEE (VATICAN CITY STATE)",
				"HN"  =>  "HONDURAS",
				"HK"  =>  "HONG KONG",
				"HU"  =>  "HUNGARY",
				"IS"  =>  "ICELAND",
				"IN"  =>  "INDIA",
				"ID"  =>  "INDONESIA",
				"IR"  =>  "IRAN, ISLAMIC REPUBLIC OF",
				"IQ"  =>  "IRAQ",
				"IE"  =>  "IRELAND",
				"IL"  =>  "ISRAEL",
				"IT"  =>  "ITALY",
				"JM"  =>  "JAMAICA",
				"JP"  =>  "JAPAN",
				"JO"  =>  "JORDAN",
				"KZ"  =>  "KAZAKHSTAN",
				"KE"  =>  "KENYA",
				"KI"  =>  "KIRIBATI",
				"KP"  =>  "KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF",
				"KR"  =>  "KOREA, REPUBLIC OF",
				"KW"  =>  "KUWAIT",
				"KG"  =>  "KYRGYZSTAN",
				"LA"  =>  "LAO PEOPLE'S DEMOCRATIC REPUBLIC",
				"LV"  =>  "LATVIA",
				"LB"  =>  "LEBANON",
				"LS"  =>  "LESOTHO",
				"LR"  =>  "LIBERIA",
				"LY"  =>  "LIBYAN ARAB JAMAHIRIYA",
				"LI"  =>  "LIECHTENSTEIN",
				"LT"  =>  "LITHUANIA",
				"LU"  =>  "LUXEMBOURG",
				"MO"  =>  "MACAO",
				"MK"  =>  "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF",
				"MG"  =>  "MADAGASCAR",
				"MW"  =>  "MALAWI",
				"MY"  =>  "MALAYSIA",
				"MV"  =>  "MALDIVES",
				"ML"  =>  "MALI",
				"MT"  =>  "MALTA",
				"MH"  =>  "MARSHALL ISLANDS",
				"MQ"  =>  "MARTINIQUE",
				"MR"  =>  "MAURITANIA",
				"MU"  =>  "MAURITIUS",
				"YT"  =>  "MAYOTTE",
				"MX"  =>  "MEXICO",
				"FM"  =>  "MICRONESIA, FEDERATED STATES OF",
				"MD"  =>  "MOLDOVA, REPUBLIC OF",
				"MC"  =>  "MONACO",
				"MN"  =>  "MONGOLIA",
				"MS"  =>  "MONTSERRAT",
				"MA"  =>  "MOROCCO",
				"MZ"  =>  "MOZAMBIQUE",
				"MM"  =>  "MYANMAR",
				"NA"  =>  "NAMIBIA",
				"NR"  =>  "NAURU",
				"NP"  =>  "NEPAL",
				"NL"  =>  "NETHERLANDS",
				"AN"  =>  "NETHERLANDS ANTILLES",
				"NC"  =>  "NEW CALEDONIA",
				"NZ"  =>  "NEW ZEALAND",
				"NI"  =>  "NICARAGUA",
				"NE"  =>  "NIGER",
				"NG"  =>  "NIGERIA",
				"NU"  =>  "NIUE",
				"NF"  =>  "NORFOLK ISLAND",
				"MP"  =>  "NORTHERN MARIANA ISLANDS",
				"NO"  =>  "NORWAY",
				"OM"  =>  "OMAN",
				"PK"  =>  "PAKISTAN",
				"PW"  =>  "PALAU",
				"PS"  =>  "PALESTINIAN TERRITORY, OCCUPIED",
				"PA"  =>  "PANAMA",
				"PG"  =>  "PAPUA NEW GUINEA",
				"PY"  =>  "PARAGUAY",
				"PE"  =>  "PERU",
				"PH"  =>  "PHILIPPINES",
				"PN"  =>  "PITCAIRN",
				"PL"  =>  "POLAND",
				"PT"  =>  "PORTUGAL",
				"PR"  =>  "PUERTO RICO",
				"QA"  =>  "QATAR",
				"RE"  =>  "REUNION",
				"RO"  =>  "ROMANIA",
				"RU"  =>  "RUSSIAN FEDERATION",
				"RW"  =>  "RWANDA",
				"SH"  =>  "SAINT HELENA",
				"KN"  =>  "SAINT KITTS AND NEVIS",
				"LC"  =>  "SAINT LUCIA",
				"PM"  =>  "SAINT PIERRE AND MIQUELON",
				"VC"  =>  "SAINT VINCENT AND THE GRENADINES",
				"WS"  =>  "SAMOA",
				"SM"  =>  "SAN MARINO",
				"ST"  =>  "SAO TOME AND PRINCIPE",
				"SA"  =>  "SAUDI ARABIA",
				"SN"  =>  "SENEGAL",
				"CS"  =>  "SERBIA AND MONTENEGRO",
				"SC"  =>  "SEYCHELLES",
				"SL"  =>  "SIERRA LEONE",
				"SG"  =>  "SINGAPORE",
				"SK"  =>  "SLOVAKIA",
				"SI"  =>  "SLOVENIA",
				"SB"  =>  "SOLOMON ISLANDS",
				"SO"  =>  "SOMALIA",
				"ZA"  =>  "SOUTH AFRICA",
				"GS"  =>  "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS",
				"ES"  =>  "SPAIN",
				"LK"  =>  "SRI LANKA",
				"SD"  =>  "SUDAN",
				"SR"  =>  "SURINAME",
				"SJ"  =>  "SVALBARD AND JAN MAYEN",
				"SZ"  =>  "SWAZILAND",
				"SE"  =>  "SWEDEN",
				"CH"  =>  "SWITZERLAND",
				"SY"  =>  "SYRIAN ARAB REPUBLIC",
				"TW"  =>  "TAIWAN, PROVINCE OF CHINA",
				"TJ"  =>  "TAJIKISTAN",
				"TZ"  =>  "TANZANIA, UNITED REPUBLIC OF",
				"TH"  =>  "THAILAND",
				"TL"  =>  "TIMOR-LESTE",
				"TG"  =>  "TOGO",
				"TK"  =>  "TOKELAU",
				"TO"  =>  "TONGA",
				"TT"  =>  "TRINIDAD AND TOBAGO",
				"TN"  =>  "TUNISIA",
				"TR"  =>  "TURKEY",
				"TM"  =>  "TURKMENISTAN",
				"TC"  =>  "TURKS AND CAICOS ISLANDS",
				"TV"  =>  "TUVALU",
				"UG"  =>  "UGANDA",
				"UA"  =>  "UKRAINE",
				"AE"  =>  "UNITED ARAB EMIRATES",
				"GB"  =>  "UNITED KINGDOM",
				"US"  =>  "UNITED STATES",
				"UM"  =>  "UNITED STATES MINOR OUTLYING ISLANDS",
				"UY"  =>  "URUGUAY",
				"UZ"  =>  "UZBEKISTAN",
				"VU"  =>  "VANUATU",
				"VE"  =>  "VENEZUELA",
				"VN"  =>  "VIET NAM",
				"VG"  =>  "VIRGIN ISLANDS, BRITISH",
				"VI"  =>  "VIRGIN ISLANDS, U.S.",
				"WF"  =>  "WALLIS AND FUTUNA",
				"EH"  =>  "WESTERN SAHARA",
				"YE"  =>  "YEMEN",
				"ZM"  =>  "ZAMBIA",
				"ZW"  =>  "ZIMBABWE"
			);
			$countries = apply_filters( 'mycred_list_option_countries', $countries );

			foreach ( $countries as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( $selected == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}

		}

		/**
		 * US States Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_us_states( $selected = '', $non_us = false ) {

			$states = array (
				"AL"  =>  "Alabama",
				"AK"  =>  "Alaska",
				"AZ"  =>  "Arizona",
				"AR"  =>  "Arkansas",
				"CA"  =>  "California",
				"CO"  =>  "Colorado",
				"CT"  =>  "Connecticut",
				"DC"  =>  "D.C.",
				"DE"  =>  "Delaware",
				"FL"  =>  "Florida",
				"GA"  =>  "Georgia",
				"HI"  =>  "Hawaii",
				"ID"  =>  "Idaho",
				"IL"  =>  "Illinois",
				"IN"  =>  "Indiana",
				"IA"  =>  "Iowa",
				"KS"  =>  "Kansas",
				"KY"  =>  "Kentucky",
				"LA"  =>  "Louisiana",
				"ME"  =>  "Maine",
				"MD"  =>  "Maryland",
				"MA"  =>  "Massachusetts",
				"MI"  =>  "Michigan",
				"MN"  =>  "Minnesota",
				"MS"  =>  "Mississippi",
				"MO"  =>  "Missouri",
				"MT"  =>  "Montana",
				"NE"  =>  "Nebraska",
				"NV"  =>  "Nevada",
				"NH"  =>  "New Hampshire",
				"NJ"  =>  "New Jersey",
				"NM"  =>  "New Mexico",
				"NY"  =>  "New York",
				"NC"  =>  "North Carolina",
				"ND"  =>  "North Dakota",
				"OH"  =>  "Ohio",
				"OK"  =>  "Oklahoma",
				"OR"  =>  "Oregon",
				"PA"  =>  "Pennsylvania",
				"RI"  =>  "Rhode Island",
				"SC"  =>  "South Carolina",
				"SD"  =>  "South Dakota",
				"TN"  =>  "Tennessee",
				"TX"  =>  "Texas",
				"UT"  =>  "Utah",
				"VT"  =>  "Vermont",
				"VA"  =>  "Virginia",
				"WA"  =>  "Washington",
				"WV"  =>  "West Virginia",
				"WI"  =>  "Wisconsin",
				"WY"  =>  "Wyoming"
			);
			$states = apply_filters( 'mycred_list_option_us', $states );

			$outside = 'Outside US';
			if ( $non_us == 'top' ) echo '<option value="">' . $outside . '</option>';
			foreach ( $states as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( $selected == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			if ( $non_us == 'bottom' ) echo '<option value="">' . $outside . '</option>';

		}

		/**
		 * Months Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_months( $selected = '' ) {

			$months = array (
				"01"  =>  __( 'January', 'mycred' ),
				"02"  =>  __( 'February', 'mycred' ),
				"03"  =>  __( 'March', 'mycred' ),
				"04"  =>  __( 'April', 'mycred' ),
				"05"  =>  __( 'May', 'mycred' ),
				"06"  =>  __( 'June', 'mycred' ),
				"07"  =>  __( 'July', 'mycred' ),
				"08"  =>  __( 'August', 'mycred' ),
				"09"  =>  __( 'September', 'mycred' ),
				"10"  =>  __( 'October', 'mycred' ),
				"11"  =>  __( 'November', 'mycred' ),
				"12"  =>  __( 'December', 'mycred' )
			);

			foreach ( $months as $number => $text ) {
				echo '<option value="' . $number . '"';
				if ( $selected == $number ) echo ' selected="selected"';
				echo '>' . $text . '</option>';
			}

		}

		/**
		 * Years Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_card_years( $selected = '', $number = 16 ) {

			$now     = current_time( 'timestamp' );
			$yy      = date( 'y', $now );
			$yyyy    = date( 'Y', $now );
			$count   = 0;
			$options = array();

			while ( $count <= (int) $number ) {
				$count ++;
				if ( $count > 1 ) {
					$yy++;
					$yyyy++;
				}
				$options[ $yy ] = $yyyy;
			}

			foreach ( $options as $key => $value ) {
				echo '<option value="' . $key . '"';
				if ( $selected == $key ) echo ' selected="selected"';
				echo '>' . $value . '</option>';
			}

		}

	}
endif;
