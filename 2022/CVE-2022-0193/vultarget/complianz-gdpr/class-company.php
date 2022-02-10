<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! class_exists( "cmplz_company" ) ) {
	class cmplz_company {
		private static $_this;


		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}
			self::$_this = $this;
		}

		static function this() {
			return self::$_this;
		}

		/**
		 * Get the default region based on region settings
		 *  - if we have one region selected, return this
		 *  - if we have more than one, try to get the one this company is based in
		 *  - if nothing found, return company region
		 *
		 * @return string region
		 */

		public function get_default_region() {
			//check default region
			$company_region_code = $this->get_company_region_code();
			$regions             = cmplz_get_regions();
			$region              = false;

			if ( is_array( $regions ) ) {
				$multiple_regions = count( $regions ) > 1;
				foreach ( $regions as $region_code => $label ) {

					//if we have one region, just return the first result
					if ( ! $multiple_regions ) {
						return $region_code;
					}

					//if we have several regions, get the one this company is located in
					if ( $company_region_code === $region_code ) {
						$region = $region_code;
					}
				}
			}

			//fallback one: company location
			if ( ! $region && is_array( $regions ) ) {
				reset( $regions );
				$region = key( $regions );
			}

			//fallback two: company location
			if ( ! $region && ! empty( $company_region_code ) ) {
				$region = $company_region_code;
			}

			//fallback if no array was returned.
			if ( ! $region ) {
				$region = CMPLZ_DEFAULT_REGION;
			}

			return $region;
		}


		/**
		 * Get the default consenttype based on region settings
		 *
		 * @return string consenttype
		 */

		public function get_default_consenttype() {
			//check default region
			$region = $this->get_default_region();
			return apply_filters('cmplz_default_consenttype',cmplz_get_consenttype_for_region( $region ));
		}

		/**
		 * Get the company region code. The EU is a region, as is the US
		 *
		 * @return string region_code
		 *
		 * */

		public function get_company_region_code() {
			$country_code = cmplz_get_value( 'country_company' );
			$region       = cmplz_get_region_for_country( $country_code );
			if ( $region ) {
				return $region;
			}

			return CMPLZ_DEFAULT_REGION;
		}

		/**
		 * Check if data was sold in the past 12 months
		 *
		 * @return bool
		 */
		public function sold_data_12months() {
			if ( ! cmplz_sells_personal_data() ) {
				return false;
			}

			$cats = cmplz_get_value( 'data_sold_us' );
			if ( ! empty( $cats ) ) {
				foreach ( $cats as $cat => $value ) {
					if ( $value == 1 ) {
						return true;
					}
				}
			}

			return false;

		}

		/**
		 * Check if data has been disclosed in the past 12 months
		 *
		 * @return bool
		 *
		 */
		public function disclosed_data_12months() {
			$cats = cmplz_get_value( 'data_disclosed_us' );
			if ( ! empty( $cats ) ) {
				foreach ( $cats as $cat => $value ) {
					if ( $value == 1 ) {
						return true;
					}
				}
			}

			return false;

		}


	}
} //class closure
