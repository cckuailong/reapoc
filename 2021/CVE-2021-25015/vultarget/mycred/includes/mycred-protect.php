<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Protect class
 * Will be replaced by openssl_encrypt() in 1.8
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_Protect' ) ) :
	class myCRED_Protect {

		public $skey;

		/**
		 * Construct
		 */
		public function __construct( $custom_key = NULL ) {

			if ( $custom_key !== NULL )
				$this->skey = $custom_key;
			else {
				$skey = mycred_get_option( 'mycred_key', false );
				if ( $skey === false || strlen( $skey ) != 16 )
					$skey = $this->reset_key();

				$this->skey = $skey;
			}

		}

		/**
		 * Reset Key
		 */
		public function reset_key() {

			$skey = wp_generate_password( 16, true, true );
			mycred_update_option( 'mycred_key', $skey );
			$this->skey = $skey;

		}

		/**
		 * Encode
		 */
		public function do_encode( $value = NULL ) {

			if ( $value === NULL || empty( $value ) ) return false;

            return trim( $this->do_safe_b64encode( $value ));
		}

		/**
		 * Decode
		 */
		public function do_decode( $value ) {

			if ( $value === NULL || empty( $value ) ) return false;

            return trim( $this->do_safe_b64decode( $value ) );
		}

		/**
		 * Retrieve
		 */
		protected function do_retrieve( $value ) {

			if ( $value === NULL || empty( $value ) ) return false;

            $crypttext   = $this->do_safe_b64decode( $value );
            $string      = trim( $crypttext );

            parse_str( $string, $output );

            return $output;
		}

		/**
		 * Safe Encode
		 */
		protected function do_safe_b64encode( $string ) {

			$data = base64_encode( $string );
			$data = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), $data );

			return $data;

		}

		/**
		 * Safe Decode
		 */
		protected function do_safe_b64decode( $string ) {

			$data = str_replace( array( '-', '_' ), array( '+', '/' ), $string );
			$mod4 = strlen( $data ) % 4;
			if ( $mod4 )
				$data .= substr( '====', $mod4 );

			return base64_decode( $data );

		}

	}
endif;

/**
 * Load myCRED Protect
 * @since 0.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_protect' ) ) :
	function mycred_protect() {

		if ( ! class_exists( 'myCRED_Protect' ) || MYCRED_DISABLE_PROTECTION ) return false;

		global $mycred_protect;

		if ( ! is_object( $mycred_protect ) )
			$mycred_protect = new myCRED_Protect();

		return $mycred_protect;

	}
endif;

/**
 * Create Encrypted Token
 * Converts an array of data into an encrypted string.
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_create_token' ) ) :
	function mycred_create_token( $string = '' ) {

		if ( is_array( $string ) )
			$string = implode( ':', $string );

		$protect = mycred_protect();
		if ( $protect !== false )
			$encoded = $protect->do_encode( $string );
		else
			$encoded = $string;

		return apply_filters( 'mycred_create_token', $encoded, $string );

	}
endif;

/**
 * Verify Encrypted Token
 * Will either return an array of data that have been decrypted or
 * false if the string is invalid. Also checks the number of variables in the array.
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_verify_token' ) ) :
	function mycred_verify_token( $string = '', $length = 1 ) {

		$reply   = false;

		$protect = mycred_protect();
		if ( $protect !== false ) {

			$decoded = $protect->do_decode( $string );
			$array   = explode( ':', $decoded );
			if ( count( $array ) == $length )
				$reply = $array;
		}
		else {
			$reply = $string;
		}

		return apply_filters( 'mycred_verify_token', $reply, $string, $length );
	}
endif;
