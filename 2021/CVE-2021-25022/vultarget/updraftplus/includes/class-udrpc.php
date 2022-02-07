<?php
// @codingStandardsIgnoreStart
/*
This class provides methods for encrypting, sending, receiving and decrypting messages of arbitrary length, using standard encryption methods and including protection against replay attacks.

Example:

// Set a key and encrypt with it
$ud_rpc = new UpdraftPlus_Remote_Communications($name_indicator); // $name_indicator is a key indicator - indicating which key is being used.
$ud_rpc->set_key_local($our_private_key);
$ud_rpc->set_key_remote($their_public_key);
$encrypted = $ud_rpc->encrypt_message('blah blah');

// Use the saved WP site option
$ud_rpc = new UpdraftPlus_Remote_Communications($name_indicator); // $name_indicator is a key indicator - indicating which key is being used.
$ud_rpc->set_option_name('udrpc_remotekey');
if (!$ud_rpc->get_key_remote()) throw new Exception('...');
$encrypted = $ud_rpc->encrypt_message('blah blah');

// Generate a new key
$ud_rpc = new UpdraftPlus_Remote_Communications('myindicator.example.com');
$ud_rpc->set_option_name('udrpc_localkey'); // Save as a WP site option
$new_pair = $ud_rpc->generate_new_keypair();
if ($new_pair) {
	$local_private_key = $ud_rpc->get_key_local();
	$remote_public_key = $ud_rpc->get_key_remote();
	// ...
} else {
	throw new Exception('...');
}

// Send a message
$ud_rpc->activate_replay_protection();
$ud_rpc->set_destination_url('https://example.com/path/to/wp');
$ud_rpc->send_message('ping');
$ud_rpc->send_message('somecommand', array('param1' => 'data', 'param2' => 'moredata'));

// N.B. The data sent needs to be something that will pass json_encode(). So, it may be desirable to base64-encode it first.

// Create a listener for incoming messages

add_filter('udrpc_command_somecommand', 'my_function', 10, 3);
// function my_function($response, $data, $name_indicator) { ... ; return array('response' => 'my_reply', 'data' => 'any mixed data'); }
// Or:
// add_filter('udrpc_action', 'some_function', 10, 4); // Function must return something other than false to indicate that it handled the specific command. Any returned value will be sent as the reply.
// function some_function($response, $command, $data, $name_indicator) { ...; return array('response' => 'my_reply', 'data' => 'any mixed data'); }
$ud_rpc->set_option_name('udrpc_local_private_key');
$ud_rpc->activate_replay_protection();
if ($ud_rpc->get_key_local()) {
	// Make sure you call this before the wp_loaded action is fired (e.g. at init)
	$ud_rpc->create_listener();
}

// Instead of using activate_replay_protection(), you can use activate_sequence_protection() (receiving side) and set_next_send_sequence_id(). They are very similar; but, the sequence number code isn't tested, and is problematic if you may have multiple clients that don't share storage (you can use the current time as a sequence number, but if two clients send at the same millisecond (or whatever granularity you use), you may have problems); whereas the replay protection code relies on database storage on the sending side (not just the receiving).

*/
// @codingStandardsIgnoreEnd
if (!class_exists('UpdraftPlus_Remote_Communications')) :
class UpdraftPlus_Remote_Communications {

	// Version numbers relate to versions of this PHP library only (i.e. it's not a protocol support number, and version numbers of other compatible libraries (e.g. JavaScript) are not comparable)
	public $version = '1.4.23';

	private $key_name_indicator;

	private $key_option_name = false;

	private $key_remote = false;

	private $key_local = false;

	private $can_generate = false;

	private $destination_url = false;

	private $maximum_replay_time_difference = 300;

	private $extra_replay_protection = false;

	private $sequence_protection_tolerance;

	private $sequence_protection_table;

	private $sequence_protection_column;

	private $sequence_protection_where_sql;

	// Debug may log confidential data using $this->log() - so only use when you are in a secure environment
	private $debug = false;

	private $next_send_sequence_id;

	private $allow_cors_from = array();

	private $http_transport = null;

	// Default protocol version - this can be over-ridden with set_message_format
	// Protocol version 1 (which uses only one RSA key-pair, instead of two) is legacy/deprecated
	private $format = 2;

	private $http_credentials = array();

	private $incoming_message = null;

	private $message_random_number = null;
	
	private $require_message_to_be_understood = false;

	public function __construct($key_name_indicator = 'default') {
		$this->set_key_name_indicator($key_name_indicator);
	}

	public function set_key_name_indicator($key_name_indicator) {
		$this->key_name_indicator = $key_name_indicator;
	}

	public function set_can_generate($can_generate = true) {
		$this->can_generate = $can_generate;
	}

	/**
	 * Which sites to allow CORS requests from
	 *
	 * @param string $allow_cors_from
	 */
	public function set_allow_cors_from($allow_cors_from) {
		$this->allow_cors_from = $allow_cors_from;
	}

	public function set_maximum_replay_time_difference($replay_time_difference) {
		$this->maximum_replay_time_difference = (int) $replay_time_difference;
	}

	/**
	 * This will cause more things to be sent to $this->log()
	 *
	 * @param boolean $debug
	 */
	public function set_debug($debug = true) {
		$this->debug = (bool) $debug;
	}

	/**
	 * Supported values: a Guzzle object, or, if not, then WP's HTTP API function siwll be used
	 *
	 * @param string $transport
	 */
	public function set_http_transport($transport) {
		$this->http_transport = $transport;
	}

	/**
	 * Sequence protection and replay protection perform similar functions, and using both is often over-kill; the distinction is that sequence protection can be used without needing to do database writes on the sending side (e.g. use the value of time() as the sequence number).
	 * The only rule of sequences is that the receiving side will reject any sequence number that is less than the last previously seen one, within the bounds of the tolerance (but it may also reject those if they are repeats).
	 * The given table/column will record a comma-separated list of recently seen sequences numbers within the tolerance threshold.
	 *
	 * @param  string  $table
	 * @param  string  $column
	 * @param  string  $where_sql
	 * @param  integer $tolerance
	 */
	public function activate_sequence_protection($table, $column, $where_sql, $tolerance = 5) {
		$this->sequence_protection_tolerance = (int) $tolerance;
		$this->sequence_protection_table = (string) $table;
		$this->sequence_protection_column = (string) $column;
		$this->sequence_protection_where_sql = (string) $where_sql;
	}

	private function ensure_crypto_loaded() {
		if (!class_exists('Crypt_Rijndael') || !class_exists('Crypt_RSA') || !class_exists('Crypt_Hash')) {
			global $updraftplus;
			// phpseclib 1.x uses deprecated PHP4-style constructors
			$this->no_deprecation_warnings_on_php7();
			if (is_a($updraftplus, 'UpdraftPlus')) {
				// Since May 2019, the second parameter is unused; but, since we don't know the version, we send it.
				$ensure_phpseclib = $updraftplus->ensure_phpseclib(array('Crypt_Rijndael', 'Crypt_RSA', 'Crypt_Hash'), array('Crypt/Rijndael', 'Crypt/RSA', 'Crypt/Hash'));
				if (is_wp_error($ensure_phpseclib)) return $ensure_phpseclib;
			} elseif (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/vendor/phpseclib/phpseclib/phpseclib')) {
				$pdir = UPDRAFTPLUS_DIR.'/vendor/phpseclib/phpseclib/phpseclib';
				if (false === strpos(get_include_path(), $pdir)) set_include_path($pdir.PATH_SEPARATOR.get_include_path());
				if (!class_exists('Crypt_Rijndael')) include_once 'Crypt/Rijndael.php';
				if (!class_exists('Crypt_RSA')) include_once 'Crypt/RSA.php';
				if (!class_exists('Crypt_Hash')) include_once 'Crypt/Hash.php';
			} elseif (file_exists(dirname(dirname(__FILE__)).'/vendor/phpseclib/phpseclib/phpseclib')) {
				$pdir = dirname(dirname(__FILE__)).'/vendor/phpseclib/phpseclib/phpseclib';
				if (false === strpos(get_include_path(), $pdir)) set_include_path($pdir.PATH_SEPARATOR.get_include_path());
				if (!class_exists('Crypt_Rijndael')) include_once 'Crypt/Rijndael.php';
				if (!class_exists('Crypt_RSA')) include_once 'Crypt/RSA.php';
				if (!class_exists('Crypt_Hash')) include_once 'Crypt/Hash.php';
			}
		}
	}

	/**
	 * Ugly, but necessary to prevent debug output breaking the conversation when the user has debug turned on
	 */
	private function no_deprecation_warnings_on_php7() {
		// PHP_MAJOR_VERSION is defined in PHP 5.2.7+
		// We don't test for PHP > 7 because the specific deprecated element will be removed in PHP 8 - and so no warning should come anyway (and we shouldn't suppress other stuff until we know we need to).
		// @codingStandardsIgnoreLine
		if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION == 7) {
			$old_level = error_reporting();
			// @codingStandardsIgnoreLine
			$new_level = $old_level & ~E_DEPRECATED;
			if ($old_level != $new_level) error_reporting($new_level);
		}
	}

	public function set_destination_url($destination_url) {
		$this->destination_url = $destination_url;
	}

	public function get_destination_url() {
		return $this->destination_url;
	}

	public function set_option_name($key_option_name) {
		$this->key_option_name = $key_option_name;
	}

	/**
	 * Method to get the remote key
	 *
	 * @return string
	 */
	public function get_key_remote() {
		if (empty($this->key_remote) && $this->can_generate) {
			$this->generate_new_keypair();
		}

		return empty($this->key_remote) ? false : $this->key_remote;
	}

	/**
	 * Set the remote key
	 *
	 * @param string $key_remote
	 */
	public function set_key_remote($key_remote) {
		$this->key_remote = $key_remote;
	}

	/**
	 * Used for sending - when receiving, the format is part of the message
	 *
	 * @param integer $format
	 */
	public function set_message_format($format = 2) {
		$this->format = $format;
	}

	/**
	 * Used for sending - when receiving, the format is part of the message
	 *
	 * @return integer
	 */
	public function get_message_format() {
		return $this->format;
	}
	
	/**
	 * Method to get the local key
	 *
	 * @return string
	 */
	public function get_key_local() {
		if (empty($this->key_local)) {
			if ($this->key_option_name) {
				$key_local = get_site_option($this->key_option_name);
				if ($key_local) {
					$this->key_local = $key_local;
				}
			}
		}
		if (empty($this->key_local) && $this->can_generate) {
			$this->generate_new_keypair();
		}

		return empty($this->key_local) ? false : $this->key_local;
	}

	/**
	 * Tests whether a supplied string (after trimming) is a valid portable bundle
	 *
	 * @param  string $bundle [description]
	 * @param  string $format same as get_portable_bundle()
	 * @return array (which the consumer is free to use - e.g. convert into internationalised string), with keys 'code' and (perhaps) 'data'
	 */
	public function decode_portable_bundle($bundle, $format = 'raw') {
		$bundle = trim($bundle);
		if ('base64_with_count' == $format) {
			if (strlen($bundle) < 5) return array('code' => 'invalid_wrong_length', 'data' => 'too_short');
			$len = substr($bundle, 0, 4);
			$bundle = substr($bundle, 4);
			$len = hexdec($len);
			if (strlen($bundle) != $len) return array('code' => 'invalid_wrong_length', 'data' => "1,$len,".strlen($bundle));
			if (false === ($bundle = base64_decode($bundle))) return array('code' => 'invalid_corrupt', 'data' => 'not_base64');
			if (null === ($bundle = json_decode($bundle, true))) return array('code' => 'invalid_corrupt', 'data' => 'not_json');
		}
		if (empty($bundle['key'])) return array('code' => 'invalid_corrupt', 'data' => 'no_key');
		if (empty($bundle['url'])) return array('code' => 'invalid_corrupt', 'data' => 'no_url');
		if (empty($bundle['name_indicator'])) return array('code' => 'invalid_corrupt', 'data' => 'no_name_indicator');

		return $bundle;
	}

	/**
	 * Method to get a portable bundle sufficient to contact this site (i.e. remote site - so you need to have generated a key-pair, or stored the remote key somewhere and restored it)
	 *
	 * @param  string $format     Supported formats: base64_with_count and default)raw
	 * @param  array  $extra_info needs to be JSON-serialisable, so be careful about what you put into it.
	 * @param  array  $options    [description]
	 * @return array
	 */
	public function get_portable_bundle($format = 'raw', $extra_info = array(), $options = array()) {

		$bundle = array_merge($extra_info, array(
			'key' => empty($options['key']) ? $this->get_key_remote() : $options['key'],
			'name_indicator' => $this->key_name_indicator,
			'url' => trailingslashit(network_site_url()),
			'admin_url' => trailingslashit(admin_url()),
			'network_admin_url' => trailingslashit(network_admin_url()),
			'format_support' => 2,
		));

		if ('base64_with_count' == $format) {
			$bundle = base64_encode(json_encode($bundle));

			$len = strlen($bundle); // Get the length
			$len = dechex($len); // The first bytes of the message are the bundle length
			$len = str_pad($len, 4, '0', STR_PAD_LEFT); // Zero pad

			return $len.$bundle;

		} else {
			return $bundle;
		}

	}

	public function set_key_local($key_local) {
		$this->key_local = $key_local;
		if ($this->key_option_name) update_site_option($this->key_option_name, $this->key_local);
	}

	public function generate_new_keypair($key_size = 2048) {

		$this->ensure_crypto_loaded();

		$rsa = new Crypt_RSA();
		$keys = $rsa->createKey($key_size);

		if (empty($keys['privatekey'])) {
			$this->set_key_local(false);
		} else {
			$this->set_key_local($keys['privatekey']);
		}

		if (empty($keys['publickey'])) {
			$this->set_key_remote(false);
		} else {
			$this->set_key_remote($keys['publickey']);
		}

		return empty($keys['publickey']) ? false : true;
	}

	/**
	 * A base-64 encoded RSA hash (PKCS_1) of the message digest
	 *
	 * @param  string  $message
	 * @param  boolean $use_key
	 * @return array
	 */
	public function signature_for_message($message, $use_key = false) {

		$hash_algorithm = 'sha256';

		// Sign with the private (local) key
		if (!$use_key) {
			if (!$this->key_local) throw new Exception('No signing key has been set');
			$use_key = $this->key_local;
		}

		$this->ensure_crypto_loaded();

		$rsa = new Crypt_RSA();
		$rsa->loadKey($use_key);
		// This is the older signature mode; phpseclib's default is the preferred CRYPT_RSA_SIGNATURE_PSS; however, Forge JS doesn't yet support this. More info: https://en.wikipedia.org/wiki/PKCS_1
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);

		// Don't do this: Crypt_RSA::sign() already calculates the digest of the hash
		// $hash = new Crypt_Hash($hash_algorithm);
		// $hashed = $hash->hash($message);
		
		// if ($this->debug) $this->log("Message hash (hash=$hash_algorithm) (hex): ".bin2hex($hashed));

		// phpseclib defaults to SHA1
		$rsa->setHash($hash_algorithm);
		$encrypted = $rsa->sign($message);

		if ($this->debug) $this->log('Signed hash (mode='.CRYPT_RSA_SIGNATURE_PKCS1.') (hex): '.bin2hex($encrypted));

		$signature = base64_encode($encrypted);

		if ($this->debug) $this->log("Message signature (base64): $signature");

		return $signature;
	}

	/**
	 * Log description
	 *
	 * @param  string $message
	 * @param  string $level   $level is not yet used much
	 */
	private function log($message, $level = 'notice') {
		// Allow other plugins to do something with the message
		do_action('udrpc_log', $message, $level, $this->key_name_indicator, $this->debug, $this);
		if ('info' != $level) error_log('UDRPC ('.$this->key_name_indicator.", $level): $message");
	}

	/**
	 * Encrypt the message, using the local key (which needs to exist)
	 *
	 * @param  string  $plaintext
	 * @param  boolean $use_key
	 * @param  integer $key_length
	 * @return array
	 */
	public function encrypt_message($plaintext, $use_key = false, $key_length = 32) {

		if (!$use_key) {
			if (1 == $this->format) {
				if (!$this->key_local) throw new Exception('No encryption key has been set');
				$use_key = $this->key_local;
			} else {
				if (!$this->key_remote) throw new Exception('No encryption key has been set');
				$use_key = $this->key_remote;
			}
		}

		$this->ensure_crypto_loaded();

		$rsa = new Crypt_RSA();

		if (defined('UDRPC_PHPSECLIB_ENCRYPTION_MODE')) $rsa->setEncryptionMode(UDRPC_PHPSECLIB_ENCRYPTION_MODE);

		$rij = new Crypt_Rijndael();

		// Generate Random Symmetric Key
		$sym_key = crypt_random_string($key_length);

		if ($this->debug) $this->log('Unencrypted symmetric key (hex): '.bin2hex($sym_key));

		// Encrypt Message with new Symmetric Key
		$rij->setKey($sym_key);
		$ciphertext = $rij->encrypt($plaintext);

		if ($this->debug) $this->log('Encrypted ciphertext (hex): '.bin2hex($ciphertext));

		$ciphertext = base64_encode($ciphertext);

		// Encrypt the Symmetric Key with the Asymmetric Key
		$rsa->loadKey($use_key);
		$sym_key = $rsa->encrypt($sym_key);

		if ($this->debug) $this->log('Encrypted symmetric key (hex): '.bin2hex($sym_key));

		// Base 64 encode the symmetric key for transport
		$sym_key = base64_encode($sym_key);

		if ($this->debug) $this->log('Encrypted symmetric key (b64): '.$sym_key);

		$len = str_pad(dechex(strlen($sym_key)), 3, '0', STR_PAD_LEFT); // Zero pad to be sure.

		// 16 characters of hex is enough for the payload to be to 16 exabytes (giga < tera < peta < exa) of data
		$cipherlen = str_pad(dechex(strlen($ciphertext)), 16, '0', STR_PAD_LEFT);

		// Concatenate the length, the encrypted symmetric key, and the message
		return $len.$sym_key.$cipherlen.$ciphertext;

	}

	/**
	 * Decrypt the message, using the local key (which needs to exist)
	 *
	 * @param  string $message
	 * @return array
	 */
	public function decrypt_message($message) {

		if (!$this->key_local) throw new Exception('No decryption key has been set');

		$this->ensure_crypto_loaded();

		$rsa = new Crypt_RSA();
		if (defined('UDRPC_PHPSECLIB_ENCRYPTION_MODE')) $rsa->setEncryptionMode(UDRPC_PHPSECLIB_ENCRYPTION_MODE);
		// Defaults to CRYPT_AES_MODE_CBC
		$rij = new Crypt_Rijndael();

		// Extract the Symmetric Key
		$len = substr($message, 0, 3);
		$len = hexdec($len);
		$sym_key = substr($message, 3, $len);

		// Extract the encrypted message
		$cipherlen = substr($message, ($len + 3), 16);
		$cipherlen = hexdec($cipherlen);

		$ciphertext = substr($message, ($len + 19), $cipherlen);
		$ciphertext = base64_decode($ciphertext);

		// Decrypt the encrypted symmetric key
		$rsa->loadKey($this->key_local);
		$sym_key = base64_decode($sym_key);
		$sym_key = $rsa->decrypt($sym_key);

		// Decrypt the message
		$rij->setKey($sym_key);

		return $rij->decrypt($ciphertext);

	}

	/**
	 * Creates a message
	 *
	 * @param  string  $command
	 * @param  string  $data
	 * @param  boolean $is_response
	 * @param  boolean $use_key_remote
	 * @param  boolean $use_key_local
	 * @return array which the caller will then format as required (e.g. use as body in post, or JSON-encode, etc.)                 [description]
	 */
	public function create_message($command, $data = null, $is_response = false, $use_key_remote = false, $use_key_local = false) {

		if ($is_response) {
			$send_array = array('response' => $command);
		} else {
			$send_array = array('command' => $command);
		}

		$send_array['time'] = time();
		// This goes in the encrypted portion as well to prevent replays with a different unencrypted name indicator
		$send_array['key_name'] = $this->key_name_indicator;

		// This random element means that if the site needs to send two identical commands or responses in the same second, then it can, and still use replay protection
		// The value of PHP_INT_MAX on a 32-bit platform
		$this->message_random_number = rand(1, 2147483647);
		$send_array['rand'] = $this->message_random_number;

		if ($this->next_send_sequence_id) {
			$send_array['sequence_id'] = $this->next_send_sequence_id;
			++$this->next_send_sequence_id;
		}

		if ($is_response && !empty($this->incoming_message) && isset($this->incoming_message['rand'])) {
			$send_array['incoming_rand'] = $this->incoming_message['rand'];
		}

		if (null !== $data) $send_array['data'] = $data;
		$send_data = $this->encrypt_message(json_encode($send_array), $use_key_remote);

		$message = array(
			'format' => $this->format,
			'key_name' => $this->key_name_indicator,
			'udrpc_message' => $send_data,
		);

		if ($this->format >= 2) {
			$signature = $this->signature_for_message($send_data, $use_key_local);
			$message['signature'] = $signature;
		}

		return $message;

	}

	/**
	 * N.B. There's already some time-based replay protection. This can be turned on to beef it up.
	 * This is only for listeners. Replays can only be detection if transients are working on the WP site (which by default only means that the option table is working).
	 *
	 * @param  boolean $activate
	 */
	public function activate_replay_protection($activate = true) {
		$this->extra_replay_protection = (bool) $activate;
	}

	public function set_next_send_sequence_id($id) {
		$this->next_send_sequence_id = $id;
	}

	/**
	 * Set_http_credentials
	 *
	 * @param string $credentials should be an array with entries for 'username' and 'password'
	 */
	public function set_http_credentials($credentials) {
		$this->http_credentials = $credentials;
	}

	/**
	 * This needs only to return an array with keys body and response - where response is also an array, with key 'code' (the HTTP status code)
	 * The $post_options array support these keys: timeout, body,
	 * Public, to allow short-circuiting of the library's own encoding/decoding (e.g. for acting as a proxy for a message already encrypted elsewhere)
	 *
	 * @param  array $post_options
	 * @return array
	 */
	public function http_post($post_options) {
		global $wp_version;
		include ABSPATH.WPINC.'/version.php';
		$http_credentials = $this->http_credentials;

		if (is_a($this->http_transport, 'GuzzleHttp\Client')) {

			// https://guzzle.readthedocs.org/en/5.3/clients.html
			
			$client = $this->http_transport;

			$guzzle_options = array(
				'body' => $post_options['body'],
				'headers' => array(
					'User-Agent' => 'WordPress/'.$wp_version.'; class-udrpc.php-Guzzle/'.$this->version.'; '.get_bloginfo('url'),
				),
				'exceptions' => false,
				'timeout' => $post_options['timeout'],
			);

			if (!class_exists('WP_HTTP_Proxy')) include_once ABSPATH.WPINC.'/class-http.php';
			$proxy = new WP_HTTP_Proxy();
			if ($proxy->is_enabled()) {
				$user = $proxy->username();
				$pass = $proxy->password();
				$host = $proxy->host();
				$port = (int) $proxy->port();
				if (empty($port)) $port = 8080;
				if (!empty($host) && $proxy->send_through_proxy($this->destination_url)) {
					$proxy_auth = '';
					if (!empty($user)) {
						$proxy_auth = $user;
						if (!empty($pass)) $proxy_auth .= ':'.$pass;
						$proxy_auth .= '@';
					}
					$guzzle_options['proxy'] = array(
						'http' => "http://${proxy_auth}$host:$port",
						'https' => "http://${proxy_auth}$host:$port",
					);
				}
			}

			if (defined('UDRPC_GUZZLE_SSL_VERIFY')) {
				$verify = UDRPC_GUZZLE_SSL_VERIFY;
			} elseif (file_exists(ABSPATH.WPINC.'/certificates/ca-bundle.crt')) {
				$verify = ABSPATH.WPINC.'/certificates/ca-bundle.crt';
			} else {
				$verify = true;
			}
			
			$guzzle_options['verify'] = apply_filters('udrpc_guzzle_verify', $verify);

			if (!empty($http_credentials['username'])) {

				$authentication_method = empty($http_credentials['authentication_method']) ? 'basic' : $http_credentials['authentication_method'];

				$password = empty($http_credentials['password']) ? '' : $http_credentials['password'];

				$guzzle_options['auth'] = array(
					$http_credentials['username'],
					$password,
					$authentication_method,
				);

			}

			$response = $client->post($this->destination_url, apply_filters('udrpc_guzzle_options', $guzzle_options, $this));

			$formatted_response = array(
				'response' => array(
					'code' => $response->getStatusCode(),
				),
				'body' => $response->getBody(),
			);

			return $formatted_response;

		} else {

			$post_options['user-agent'] = 'WordPress/'.$wp_version.'; class-udrpc.php/'.$this->version.'; '.get_bloginfo('url');

			if (!empty($http_credentials['username'])) {

				$authentication_type = empty($http_credentials['authentication_type']) ? 'basic' : $http_credentials['authentication_type'];

				if ('basic' != $authentication_type) {
					return new WP_Error('unsupported_http_authentication_type', 'Only HTTP basic authentication is supported (for other types, use Guzzle)');
				}

				$password = empty($http_credentials['password']) ? '' : $http_credentials['password'];
				$post_options['headers'] = array(
					'Authorization' => 'Basic '.base64_encode($http_credentials['username'].':'.$password),
				);
			}

			return wp_remote_post(
				$this->destination_url,
				$post_options
			);
		}
	}

	public function send_message($command, $data = null, $timeout = 20) {

		if (empty($this->destination_url)) return new WP_Error('not_initialised', 'RPC error: URL not initialised');

		$message = $this->create_message($command, $data);

		$post_options = array(
			'timeout' => $timeout,
			'body' => $message,
		);

		$post_options = apply_filters('udrpc_post_options', $post_options, $command, $data, $timeout, $this);

		// Make the memory available - may be useful if the message was large
		unset($data);
		
		try {
			$post = $this->http_post($post_options);
		} catch (Exception $e) {
			// Curl can return an error code 0, which causes WP_Error to return early, without recording the message. So, we prefix the code.
			return new WP_Error('http_post_'.$e->getCode(), $e->getMessage());
		}

		if (is_wp_error($post)) return $post;

		$response_code = wp_remote_retrieve_response_code($post);

		if (empty($response_code)) return new WP_Error('empty_http_code', 'Unexpected HTTP response code');

		if ($response_code < 200 || $response_code >= 300) return new WP_Error('unexpected_http_code', 'Unexpected HTTP response code ('.$response_code.')', $post);

		$response_body = wp_remote_retrieve_body($post);

		if (empty($response_body)) return new WP_Error('empty_response', 'Empty response from remote site');

		$decoded = json_decode($response_body, true);

		if (empty($decoded)) {

			if (false != ($found_at = strpos($response_body, '{"format":'))) {
				$new_body = substr($response_body, $found_at);
				$decoded = json_decode($new_body, true);
			}

			if (empty($decoded)) {
				$this->log('response from remote site ('.$this->destination_url.') could not be understood: '.substr($response_body, 0, 100).' ... ');
				return new WP_Error('response_not_understood', 'Response from remote site could not be understood', $response_body);
			}
		}

		if (!is_array($decoded) || empty($decoded['udrpc_message'])) return new WP_Error('response_not_understood', 'Response from remote site was not in the expected format ('.$post['body'].')', $decoded);

		if ($this->format >= 2) {
			if (empty($decoded['signature'])) {
				$this->log('No message signature found');
				die;
			}
			if (!$this->key_remote) {
				$this->log('No signature verification key has been set');
				die;
			}
			if (!$this->verify_signature($decoded['udrpc_message'], $decoded['signature'], $this->key_remote)) {
				$this->log('Signature verification failed; discarding');
				die;
			}
		}

		$decoded = $this->decrypt_message($decoded['udrpc_message']);

		if (!is_string($decoded)) return new WP_Error('not_decrypted', 'Response from remote site was not successfully decrypted', $decoded['udrpc_message']);

		$json_decoded = json_decode($decoded, true);

		if (!is_array($json_decoded) || empty($json_decoded['response']) || empty($json_decoded['time']) || !is_numeric($json_decoded['time'])) return new WP_Error('response_corrupt', 'Response from remote site was not in the expected format', $decoded);

		// Don't do the reply detection until now, because $post['body'] may not be a message that originated from the remote component at all (e.g. an HTTP error)
		if ($this->extra_replay_protection) {
			$message_hash = $this->calculate_message_hash((string) $post['body']);
			if ($this->message_hash_seen($message_hash)) {
				return new WP_Error('replay_detected', 'Message refused: replay detected', $message_hash);
			}
		}

		$time_difference = absint((time() - $json_decoded['time']));
		if ($time_difference > $this->maximum_replay_time_difference) return new WP_Error('window_error', 'Message refused: maxium replay time difference exceeded', $time_difference);

		if (isset($json_decoded['incoming_rand']) && !empty($this->message_random_number) && $json_decoded['incoming_rand'] != $this->message_random_number) {
			// @codingStandardsIgnoreLine
			$this->log('UDRPC: Message mismatch (possibly MITM) (sent_rand=' + $this->message_random_number + ', returned_rand='.$json_decoded['incoming_rand'].'): dropping', 'error');

			return new WP_Error('message_mismatch_error', 'Message refused: message mismatch (possible MITM)');

		}

		// Should be an array with keys including 'response' and (if relevant) 'data'
		return $json_decoded;

	}

	/**
	 * Returns a boolean indicating whether a listener was created - which depends on whether one was needed (so, false does not necessarily indicate an error condition)
	 *
	 * @return boolean
	 */
	public function create_listener() {

		$http_origin = function_exists('get_http_origin') ? get_http_origin() : (empty($_SERVER['HTTP_ORIGIN']) ? '' : $_SERVER['HTTP_ORIGIN']);

		// Create the WP actions to handle incoming commands, handle built-in commands (e.g. ping, create_keys (authenticate with admin creds)), dispatch them to the right place, and die
		if ((!empty($_POST) && !empty($_POST['udrpc_message']) && !empty($_POST['format'])) || (!empty($_SERVER['REQUEST_METHOD']) && 'OPTIONS' == $_SERVER['REQUEST_METHOD'] && $http_origin)) {
			add_action('wp_loaded', array($this, 'wp_loaded'));
			add_action('wp_loaded', array($this, 'wp_loaded_final'), 10000);
			return true;
		}

		return false;
	}

	public function wp_loaded_final() {
		if (empty($this->require_message_to_be_understood)) return;
		$message_for = empty($_POST['key_name']) ? '' : (string) $_POST['key_name'];
		$this->log("Message was received, but not understood by local site (for: $message_for)");
		die;
	}

	public function wp_loaded() {

		/*
		// What if something else already set some response headers?
		if (function_exists('apache_response_headers')) {
			$apache_response_headers = apache_response_headers();
			// Do something...
		}
		*/

		// CORS: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
		// get_http_origin() : since WP 3.4
		$http_origin = function_exists('get_http_origin') ? get_http_origin() : (empty($_SERVER['HTTP_ORIGIN']) ? '' : $_SERVER['HTTP_ORIGIN']);
		if (!empty($_SERVER['REQUEST_METHOD']) && 'OPTIONS' == $_SERVER['REQUEST_METHOD'] && $http_origin) {
			if (in_array($http_origin, $this->allow_cors_from)) {
				// @codingStandardsIgnoreLine
				if (!defined('UDRPC_DO_NOT_SEND_CORS_HEADERS') || !UDRPC_DO_NOT_SEND_CORS_HEADERS) {
					header("Access-Control-Allow-Origin: $http_origin");
					header('Access-Control-Allow-Credentials: true');
					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header('Access-Control-Allow-Methods: POST, OPTIONS');
					if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header('Access-Control-Allow-Headers: '.$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
				}
				die;
			} elseif ($this->debug) {
				$this->log('Non-allowed CORS from: '.$http_origin);
			}
			// Having detected that this is a CORS request, there's nothing more to do. We return, because a different listener might pick it up, even though we didn't.
			return;
		}

		// Silently return, rather than dying, in case another instance is able to handle this
		if (empty($_POST['format']) || (1 != $_POST['format'] && 2 != $_POST['format'])) return;

		$this->require_message_to_be_understood = true;
		
		$format = $_POST['format'];

		/*
		In format 1 (legacy/obsolete), the one encrypts (the shared AES key) using one half of the key-pair, and decrypts with the other; whereas the other side of the conversation does the reverse when replying (and uses a different shared AES key). Though this is possible in RSA, this is the wrong thing to do - see https://crypto.stackexchange.com/questions/2123/rsa-encryption-with-private-key-and-decryption-with-a-public-key
		In format 2, both sides have their own private and public key. The sender encrypts using the other side's public key, and decrypts using its own private key. Messages are signed (the message digest is SHA-256).
		*/

		// Is this for us?
		if (empty($_POST['key_name']) || $_POST['key_name'] != $this->key_name_indicator) {
			return;
		}

		// wp_unslash() does not exist until after WP 3.5
		// $udrpc_message = function_exists('wp_unslash') ? wp_unslash($_POST['udrpc_message']) : stripslashes_deep($_POST['udrpc_message']);
		
		// Data should not have any slashes - it is base64-encoded
		$udrpc_message = (string) $_POST['udrpc_message'];

		// Check this now, rather than allow the decrypt method to thrown an Exception
		
		if (empty($this->key_local)) {
			$this->log('no local key (format 1): cannot decrypt', 'error');
			die;
		}

		if ($format >= 2) {
			if (empty($_POST['signature'])) {
				$this->log('No message signature found', 'error');
				die;
			}
			if (!$this->key_remote) {
				$this->log('No signature verification key has been set', 'error');
				die;
			}
			if (!$this->verify_signature($udrpc_message, $_POST['signature'], $this->key_remote)) {
				$this->log('Signature verification failed; discarding', 'error');
				die;
			}
		}

		try {
			$udrpc_message = $this->decrypt_message($udrpc_message);
		} catch (Exception $e) {
			$this->log('Exception ('.get_class($e).'): '.$e->getMessage(), 'error');
			die;
		}

		$udrpc_message = json_decode($udrpc_message, true);

		if (empty($udrpc_message) || !is_array($udrpc_message) || empty($udrpc_message['command']) || !is_string($udrpc_message['command'])) {
			$this->log('Could not decode JSON on incoming message', 'error');
			die;
		}

		if (empty($udrpc_message['time'])) {
			$this->log('No time set in incoming message', 'error');
			die;
		}

		// Mismatch indicating a replay of the message with a different key name in the unencrypted portion?
		if (empty($udrpc_message['key_name']) || $_POST['key_name'] != $udrpc_message['key_name']) {
			$this->log('key_name mismatch between encrypted and unencrypted portions', 'error');
			die;
		}

		if ($this->extra_replay_protection) {
			$message_hash = $this->calculate_message_hash((string) $_POST['udrpc_message']);
			if ($this->message_hash_seen($message_hash)) {
				$this->log("Message dropped: apparently a replay (hash: $message_hash)", 'error');
				die;
			}
		}

		// Do this after the extra replay protection, as that checks hashes within the maximum time window - so don't check the maximum time window until afterwards, to avoid a tiny window (race) in between.
		$time_difference = absint($udrpc_message['time'] - time());
		if ($time_difference > $this->maximum_replay_time_difference) {
			$this->log("Time in incoming message is outside of allowed window ($time_difference > ".$this->maximum_replay_time_difference.')', 'error');
			die;
		}

		// The sequence number should always be larger than any previously-sent sequence number
		if ($this->sequence_protection_tolerance) {

			if ($this->debug) $this->log('Sequence protection is active; tolerance: '.$this->sequence_protection_tolerance);

			global $wpdb;

			if (!isset($udrpc_message['sequence_id']) || !is_numeric($udrpc_message['sequence_id'])) {
				$this->log('a numerical sequence number is required, but none was included in the message - dropping', 'error');
				die;
			}

			$message_sequence_id = (int) $udrpc_message['sequence_id'];
			$recently_seen_sequences_ids = $wpdb->get_var($wpdb->prepare('SELECT %s FROM %s LIMIT 1 WHERE '.$this->sequence_protection_where_sql, $this->sequence_protection_column, $this->sequence_protection_table));

			if ('' === $recently_seen_sequences_ids) $recently_seen_sequences_ids = '0';

			$recently_seen_sequences_ids_as_array = explode($recently_seen_sequences_ids, ',');
			sort($recently_seen_sequences_ids_as_array);

			// Seen before?
			if (in_array($message_sequence_id, $recently_seen_sequences_ids_as_array)) {
				$this->log("message with duplicate sequence number received - dropping (received=$message_sequence_id, seen=$recently_seen_sequences_ids)");
				die;
			}

			// Within the tolerance threshold? That means: a) either bigger than the max, or b) no more than <tolerance> lower than the least
			if ($message_sequence_id > max($recently_seen_sequences_ids)) {
				if ($this->debug) $this->log("Sequence id ($message_sequence_id) is greater than any previous (".max($recently_seen_sequences_ids).') - message is thus OK');
				// All is well
				$recently_seen_sequences_ids_as_array[] = $message_sequence_id;
			} elseif ((max($recently_seen_sequences_ids) - $message_sequence_id) <= $this->sequence_protection_tolerance) {
				// All is well - was one of those 'missing' in the sequence
				if ($this->debug) $this->log("Sequence id ($message_sequence_id) is within tolerance range of previous maximum (".max($recently_seen_sequences_ids).') - message is thus OK');
				$recently_seen_sequences_ids_as_array[] = $message_sequence_id;
			} else {
				$this->log("message received outside of allowed sequence window - dropping (received=$message_sequence_id, seen=$recently_seen_sequences_ids, tolerance=".$this->sequence_protection_tolerance.')', 'error');
				die;
			}

			// Remove out-of-bounds seen IDs
			$max_sequence_id_seen = max($recently_seen_sequences_ids_as_array);
			foreach ($recently_seen_sequences_ids_as_array as $k => $id) {
				if ($max_sequence_id_seen - $id > $this->sequence_protection_tolerance) {
					if ($this->debug) $this->log("Removing no-longer-relevant sequence from list of those recently seen: $id");
					unset($recently_seen_sequences_ids_as_array[$k]);
				}
			}

			// Allow reset
			if ($message_sequence_id > PHP_INT_MAX - 10) {
				$recently_seen_sequences_ids_as_array = array(0);
			}

			// Write them back to the database
			$sql = $wpdb->prepare('UPDATE %s SET %s=%s WHERE '.$this->sequence_protection_where_sql, $this->sequence_protection_table, $this->sequence_protection_column, implode(',', $recently_seen_sequences_ids_as_array));
			if ($this->debug) $this->log("SQL to send recent sequence IDs back to the database: $sql");
			$wpdb->query($sql);

		}

		$this->incoming_message = $udrpc_message;

		$command = (string) $udrpc_message['command'];
		$data = empty($udrpc_message['data']) ? null : $udrpc_message['data'];

		// @codingStandardsIgnoreLine
		if ($http_origin && !empty($udrpc_message['cors_headers_wanted']) && (!defined('UDRPC_DO_NOT_SEND_CORS_HEADERS') || !UDRPC_DO_NOT_SEND_CORS_HEADERS)) {
			header("Access-Control-Allow-Origin: $http_origin");
			header('Access-Control-Allow-Credentials: true');
		}

		$this->log('Command received: '.$command, 'info');

		if ('ping' == $command) {
			$response = array('response' => 'pong', 'data' => null);
		} else {
			if (has_filter('udrpc_command_'.$command)) {
				$response = apply_filters('udrpc_command_'.$command, null, $data, $this->key_name_indicator);
			} else {
				$response = array('response' => 'rpcerror', 'data' => array('code' => 'unknown_rpc_command', 'data' => $command));
			}
		}

		$response = apply_filters('udrpc_action', $response, $command, $data, $this->key_name_indicator, $this);

		if (is_array($response)) {

			if ($this->debug) {
				$this->log('UDRPC response (pre-encoding/encryption): '.serialize($response));
			}

			$data = isset($response['data']) ? $response['data'] : null;
			
			$final_response = json_encode($this->create_message($response['response'], $data, true));
			
			do_action('udrpc_action_send_response', $final_response, $command);
			
			echo $final_response;
		}
		
		die;

	}

	/**
	 * The hash needs to be in a format that phpseclib likes. phpseclib uses lower case.
	 * Pass in a base64-encoded signature (i.e. just as signature_for_message creates)
	 *
	 * @param  string $message
	 * @param  string $signature
	 * @param  string $key
	 * @param  string $hash_algorithm
	 * @return boolean
	 */
	public function verify_signature($message, $signature, $key, $hash_algorithm = 'sha256') {
		$this->ensure_crypto_loaded();
		$rsa = new Crypt_RSA();
		$rsa->setHash(strtolower($hash_algorithm));
		// This is not the default, but is what we use
		$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$rsa->loadKey($key);

		// Don't hash it - Crypt_RSA::verify() already does that
		// $hash = new Crypt_Hash($hash_algorithm);
		// $hashed = $hash->hash($message);
		
		$verified = $rsa->verify($message, base64_decode($signature));

		if ($this->debug) $this->log('Signature verification result: '.serialize($verified));

		return $verified;
	}

	private function calculate_message_hash($message) {
		return hash('sha256', $message);
	}

	private function message_hash_seen($message_hash) {
		// 39 characters - less than the WP site transient name limit (40). Though, we use a normal transient, as these don't auto-load at all times.
		$transient_name = 'udrpch_'.md5($this->key_name_indicator);
		$seen_hashes = get_transient($transient_name);
		if (!is_array($seen_hashes)) $seen_hashes = array();
		$time_now = time();
		// $any_changes = false;
		// Prune the old hashes
		foreach ($seen_hashes as $hash => $last_seen) {
			if ($last_seen < ($time_now - $this->maximum_replay_time_difference)) {
		// $any_changes = true;
				unset($seen_hashes[$hash]);
			}
		}
		if (isset($seen_hashes[$message_hash])) {
			return true;
		}
		$seen_hashes[$message_hash] = $time_now;
		set_transient($transient_name, $seen_hashes, $this->maximum_replay_time_difference);

		return false;
	}
}

endif;
