<?php
/**
 * Class SB_Facebook_Data_Encryption
 *
 * @copyright 2021 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 */

namespace CustomFacebookFeed;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Class responsible for encrypting and decrypting data.
 *
 * @since 2.9.4/5.12.4
 * @access private
 * @ignore
 */
class SB_Facebook_Data_Encryption {

	/**
	 * Key to use for encryption.
	 *
	 * @since 2.9.4/5.12.4
	 * @var string
	 */
	private $key;

	/**
	 * Salt to use for encryption.
	 *
	 * @since 2.9.4/5.12.4
	 * @var string
	 */
	private $salt;

	/**
	 * Constructor.
	 *
	 * @since 2.9.4/5.12.4
	 */
	public function __construct( $remote = array() ) {
		if ( ! empty( $remote ) ) {
			$this->key  = $remote['key'];
			$this->salt = $remote['salt'];
		} else {
			$this->key  = $this->get_default_key();
			$this->salt = $this->get_default_salt();
		}
	}

	/**
	 * Encrypts a value.
	 *
	 * If a user-based key is set, that key is used. Otherwise the default key is used.
	 *
	 * @since 2.9.4/5.12.4
	 *
	 * @param string $value Value to encrypt.
	 * @return string|bool Encrypted value, or false on failure.
	 */
	public function encrypt( $value ) {
		if ( ! cff_doing_openssl() ) {
			return $value;
		}

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = openssl_random_pseudo_bytes( $ivlen );

		$raw_value = openssl_encrypt( $value . $this->salt, $method, $this->key, 0, $iv );
		if ( ! $raw_value ) {
			return false;
		}

		return base64_encode( $iv . $raw_value );
	}

	/**
	 * Decrypts a value.
	 *
	 * If a user-based key is set, that key is used. Otherwise the default key is used.
	 *
	 * @since 2.9.4/5.12.4
	 *
	 * @param string $raw_value Value to decrypt.
	 * @return string|bool Decrypted value, or false on failure.
	 */
	public function decrypt( $raw_value ) {
		if ( ! cff_doing_openssl() ) {
			return $raw_value;
		}

		$raw_value = base64_decode( $raw_value, true );

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = substr( $raw_value, 0, $ivlen );

		$raw_value = substr( $raw_value, $ivlen );

		$value = openssl_decrypt( $raw_value, $method, $this->key, 0, $iv );
		if ( ! $value || substr( $value, - strlen( $this->salt ) ) !== $this->salt ) {
			return false;
		}

		return substr( $value, 0, - strlen( $this->salt ) );
	}


	public function maybe_encrypt( $raw_value ) {
		$maybe_decrypted = $this->decrypt( $raw_value );

		if ( $maybe_decrypted ) {
			return $this->encrypt( $maybe_decrypted );
		}

		return $this->encrypt( $raw_value );
	}

	/**
	 * Uses a raw value and attempts to decrypt it
	 *
	 * @param $value
	 *
	 * @return bool|string
	 */
	public function maybe_decrypt( $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}
		if ( strpos( $value, '{' ) === 0 ) {
			return $value;
		}

		$decrypted = $this->decrypt( $value );

		if ( ! $decrypted ) {
			return $value;
		}

		return $decrypted;
	}

	/**
	 * Gets the default encryption key to use.
	 *
	 * @since 2.9.4/5.12.4
	 *
	 * @return string Default (not user-based) encryption key.
	 */
	private function get_default_key() {
		if ( defined( 'CFF_ENCRYPTION_KEY' ) && '' !== CFF_ENCRYPTION_KEY ) {
			return CFF_ENCRYPTION_KEY;
		}

		if ( defined( 'LOGGED_IN_KEY' ) && '' !== LOGGED_IN_KEY ) {
			return LOGGED_IN_KEY;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimer-schluessel';
	}

	/**
	 * Gets the default encryption salt to use.
	 *
	 * @since 2.9.4/5.12.4
	 *
	 * @return string Encryption salt.
	 */
	private function get_default_salt() {
		if ( defined( 'CFF_ENCRYPTION_SALT' ) && '' !== CFF_ENCRYPTION_SALT ) {
			return CFF_ENCRYPTION_SALT;
		}

		if ( defined( 'LOGGED_IN_SALT' ) && '' !== LOGGED_IN_SALT ) {
			return LOGGED_IN_SALT;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimes-salz';
	}
}
