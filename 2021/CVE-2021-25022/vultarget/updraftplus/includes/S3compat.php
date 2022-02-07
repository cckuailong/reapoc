<?php
// @codingStandardsIgnoreStart
// This is a compatibility library, using Amazon's official PHP SDK (PHP 5.3.3+), but providing the methods of Donovan Schönknecht's S3.php library (which we used to always use) - but we've only cared about making code-paths in UpdraftPlus work, so be careful if re-deploying this in another project. And, we have a few bits of UpdraftPlus-specific code below, for logging.

/**
 *
 * Copyright (c) 2012-9, David Anderson (https://www.simbahosting.co.uk).  All rights reserved.
 * Portions copyright (c) 2011, Donovan Schönknecht.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Amazon S3 is a trademark of Amazon.com, Inc. or its affiliates.
 */
// @codingStandardsIgnoreEnd

require_once(UPDRAFTPLUS_DIR.'/vendor/autoload.php');

// SDK uses namespacing - requires PHP 5.3 (actually the SDK states its requirements as 5.3.3)
use Aws\S3;

/**
 * Amazon S3 PHP class
 * http://undesigned.org.za/2007/10/22/amazon-s3-php-cla
 *
 * @version Release: 0.5.0-dev
 */
class UpdraftPlus_S3_Compat {

	// ACL flags
	const ACL_PRIVATE = 'private';
	const ACL_PUBLIC_READ = 'public-read';
	const ACL_PUBLIC_READ_WRITE = 'public-read-write';
	const ACL_AUTHENTICATED_READ = 'authenticated-read';

	const STORAGE_CLASS_STANDARD = 'STANDARD';
	
	private $config = array('scheme' => 'https', 'service' => 's3');

	private $__access_key = null; // AWS Access key

	private $__secret_key = null; // AWS Secret key
	
	private $__session_token = null;

	private $__ssl_key = null;

	public $endpoint = 's3.amazonaws.com';

	public $proxy = null;

	private $region = 'us-east-1';

	// Added to cope with a particular situation where the user had no pernmission to check the bucket location, which necessitated using DNS-based endpoints.
	public $use_dns_bucket_name = false;

	public $use_ssl = false;

	public $use_ssl_validation = true;

	public $use_exceptions = false;

	private $_server_side_encryption = null;

	// SSL CURL SSL options - only needed if you are experiencing problems with your OpenSSL configuration
	public $ssl_key = null;
	
	public $ssl_cert = null;

	public $ssl_ca_cert = null;
	
	// Added at request of a user using a non-default port.
	public static $port = false;

	/**
	 * Constructor - if you're not using the class statically
	 *
	 * @param string         $access_key    Access key
	 * @param string         $secret_key    Secret key
	 * @param boolean        $use_ssl       Enable SSL
	 * @param string|boolean $ssl_ca_cert   Certificate authority (true = bundled Guzzle version; false = no verify, 'system' = system version; otherwise, path)
	 * @param Null|String    $endpoint      Endpoint (if omitted, it will be set by the SDK using the region)
	 * @param Null|String    $session_token The session token returned by AWS for temporary credentials access
	 * @param Null|String    $region        Region. Currently unused, but harmonised with UpdraftPlus_S3 class
	 * @return void
	 */
	public function __construct($access_key = null, $secret_key = null, $use_ssl = true, $ssl_ca_cert = true, $endpoint = null, $session_token = null, $region = null) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $region is unused 
		if (null !== $access_key && null !== $secret_key)
			$this->setAuth($access_key, $secret_key, $session_token);

		$this->use_ssl = $use_ssl;
		$this->ssl_ca_cert = $ssl_ca_cert;

		$opts = array(
			'key' => $access_key,
			'secret' => $secret_key,
			'scheme' => ($use_ssl) ? 'https' : 'http',
			// Using signature v4 requires a region (but see the note below)
			// 'signature' => 'v4',
			// 'region' => $this->region
			// 'endpoint' => 'somethingorother.s3.amazonaws.com'
		);

		if ($endpoint) {
			// Can't specify signature v4, as that requires stating the region - which we don't necessarily yet know.
			// Later comment: however, it looks to me like in current UD (Sep 2017), $endpoint is never used for Amazon S3/Vault, and there may be cases (e.g. DigitalOcean Spaces) where we might prefer v4 (DO support v2 too, currently) without knowing a region.
			$this->endpoint = $endpoint;
			$opts['endpoint'] = $endpoint;
		} else {
			// Using signature v4 requires a region. Also, some regions (EU Central 1, China) require signature v4 - and all support it, so we may as well use it if we can.
			$opts['signature'] = 'v4';
			$opts['region'] = $this->region;
		}

		if ($session_token) {
		  $opts['token'] = $session_token;
		}
	
		if ($use_ssl) $opts['ssl.certificate_authority'] = $ssl_ca_cert;

		$this->client = Aws\S3\S3Client::factory($opts);
	}

	/**
	 * Set AWS access key and secret key
	 *
	 * @param string      $access_key    Access key
	 * @param string      $secret_key    Secret key
	 * @param null|string $session_token The session token returned by AWS for temporary credentials access
	 * @return void
	 */
	public function setAuth($access_key, $secret_key, $session_token = null) {
		$this->__access_key = $access_key;
		$this->__secret_key = $secret_key;
		$this->__session_token = $session_token;
	}

	/**
	 * Example value: 'AES256'. See: https://docs.aws.amazon.com/AmazonS3/latest/dev/SSEUsingPHPSDK.html
	 * Or, false to turn off.
	 *
	 * @param boolean $value Set if Value
	 */
	public function setServerSideEncryption($value) {
		$this->_serverSideEncryption = $value;
	}

	/**
	 * Set the service region
	 *
	 * @param string $region Region
	 * @return void
	 */
	public function setRegion($region) {
		$this->region = $region;
		if ('eu-central-1' == $region || 'cn-north-1' == $region) {
			// $this->config['signature'] =  new Aws\S3\S3SignatureV4('s3');
			// $this->client->setConfig($this->config);
		}
		$this->client->setRegion($region);
	}

	/**
	 * Set the service endpoint
	 *
	 * @param string $host   Hostname
	 * @param string $region Region
	 * @return void
	 */
	public function setEndpoint($host, $region) {
		$this->endpoint = $host;
		$this->region = $region;
		$this->config['endpoint_provider'] = $this->return_provider();
		$this->client->setConfig($this->config);
	}

	/**
	 * Set the service port
	 *
	 * @param Integer $port Port number
	 */
	public function setPort($port) {
		// Not used with AWS (which is the only thing using this class)
		self::$port = $port;
	}

	public function return_provider() {
		$our_endpoints = array(
			'endpoint' => $this->endpoint
		);
		if ('eu-central-1' == $this->region || 'cn-north-1' == $this->region) $our_endpoints['signatureVersion'] = 'v4';
		$endpoints = array(
			'version' => 2,
			'endpoints' => array(
				"*/s3" => $our_endpoints
			)
		);
		return new Aws\Common\RulesEndpointProvider($endpoints);
	}

	/**
	 * Set SSL on or off
	 * This code relies upon the particular pattern of SSL options-setting in s3.php in UpdraftPlus
	 *
	 * @param boolean $enabled  SSL enabled
	 * @param boolean $validate SSL certificate validation
	 * @return void
	 */
	public function setSSL($enabled, $validate = true) {
		$this->use_ssl = $enabled;
		$this->use_ssl_validation = $validate;
		// http://guzzle.readthedocs.org/en/latest/clients.html#verify
		if ($enabled) {

			// Do nothing - in UpdraftPlus, setSSLAuth will be called later, and we do the calls there

// $verify_peer = ($validate) ? true : false;
// $verify_host = ($validate) ? 2 : 0;
//
// $this->config['scheme'] = 'https';
// $this->client->setConfig($this->config);
//
// $this->client->setSslVerification($validate, $verify_peer, $verify_host);


		} else {
			$this->config['scheme'] = 'http';
// $this->client->setConfig($this->config);
		}
		$this->client->setConfig($this->config);
	}

	public function getuseSSL() {
		return $this->use_ssl;
	}

	/**
	 * Set SSL client certificates (experimental)
	 *
	 * @param string $ssl_cert    SSL client certificate
	 * @param string $ssl_key     SSL client key
	 * @param string $ssl_ca_cert SSL CA cert (only required if you are having problems with your system CA cert)
	 * @return void
	 */
	public function setSSLAuth($ssl_cert = null, $ssl_key = null, $ssl_ca_cert = null) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (!$this->use_ssl) return;

		if (!$this->use_ssl_validation) {
			$this->client->setSslVerification(false);
		} else {
			if (!$ssl_ca_cert) {
				$client = $this->client;
				// "Static class properties and methods, as well as class constants, could not be accessed using a dynamic (variable) classname in PHP 5.2 or earlier." But the present file is not loaded in PHP 5.2.
				// @codingStandardsIgnoreLine
				$this->config[$client::SSL_CERT_AUTHORITY] = false;
				$this->client->setConfig($this->config);
			} else {
				$this->client->setSslVerification(realpath($ssl_ca_cert), true, 2);
			}
		}

// $this->client->setSslVerification($ssl_ca_cert, $verify_peer, $verify_host);
// $this->config['ssl.certificate_authority'] = $ssl_ca_cert;
// $this->client->setConfig($this->config);
	}

	/**
	 * Set proxy information
	 *
	 * @param string   $host Proxy hostname and port (localhost:1234)
	 * @param string   $user Proxy username
	 * @param string   $pass Proxy password
	 * @param constant $type CURL proxy type
	 * @param integer  $port Port number
	 * @return void
	 */
	public function setProxy($host, $user = null, $pass = null, $type = CURLPROXY_SOCKS5, $port = null) {

		$this->proxy = array('host' => $host, 'type' => $type, 'user' => $user, 'pass' => $pass, 'port' => $port);

		if (!$host) return;

		$wp_proxy = new WP_HTTP_Proxy();
		if ($wp_proxy->send_through_proxy('https://s3.amazonaws.com')) {

			global $updraftplus;
			$updraftplus->log("setProxy: host=$host, user=$user, port=$port");

			// N.B. Currently (02-Feb-15), only support for HTTP proxies has ever been requested for S3 in UpdraftPlus
			$proxy_url = 'http://';
			if ($user) {
				$proxy_url .= $user;
				if ($pass) $proxy_url .= ":$pass";
				$proxy_url .= "@";
			}

			$proxy_url .= $host;

			if ($port) $proxy_url .= ":$port";

			$this->client->setDefaultOption('proxy', $proxy_url);
		}

	}

	/**
	 * Set the error mode to exceptions
	 *
	 * @param boolean $enabled Enable exceptions
	 * @return void
	 */
	public function setExceptions($enabled = true) {
		$this->useExceptions = $enabled;
	}

	/**
	 * A no-op in this compatibility layer (for now - not yet found a use)...
	 *
	 * @param  boolean $use    Bucket use
	 * @param  string  $bucket Bucket name
	 * @return boolean
	 */
	public function useDNSBucketName($use = true, $bucket = '') {
		$this->use_dns_bucket_name = $use;
		if ($use && $bucket) {
			$this->setEndpoint($bucket.'.s3.amazonaws.com', $this->region);
		}
		return true;
	}

	/**
	 * Get contents for a bucket
	 * If max_keys is null this method will loop through truncated result sets
	 * N.B. UpdraftPlus does not use the $delimiter or $return_common_prefixes parameters (nor set $prefix or $marker to anything other than null)
	 * $return_common_prefixes is not implemented below
	 *
	 * @param  string  $bucket                 Bucket name
	 * @param  string  $prefix                 Prefix
	 * @param  string  $marker                 Marker (last file listed)
	 * @param  string  $max_keys               Max keys (maximum number of keys to return)
	 * @param  string  $delimiter              Delimiter
	 * @param  boolean $return_common_prefixes Set to true to return CommonPrefixes
	 * @return array
	 */
	public function getBucket($bucket, $prefix = null, $marker = null, $max_keys = null, $delimiter = null, $return_common_prefixes = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $return_common_prefixes is unused (commented out) so kept in just incase it is reused again
		try {
			if (0 == $max_keys) $max_keys = null;
			
			$vars = array('Bucket' => $bucket);
			if (null !== $prefix && '' !== $prefix) $vars['Prefix'] = $prefix;
			if (null !== $marker && '' !== $marker) $vars['Marker'] = $marker;
			if (null !== $max_keys && '' !== $max_keys) $vars['MaxKeys'] = $max_keys;
			if (null !== $delimiter && '' !== $delimiter) $vars['Delimiter'] = $delimiter;
			$result = $this->client->listObjects($vars);

			if (!is_a($result, 'Guzzle\Service\Resource\Model')) {
				return false;
			}

			$results = array();
			$next_marker = null;
			// http://docs.aws.amazon.com/AmazonS3/latest/dev/ListingObjectKeysUsingPHP.html
			// UpdraftPlus does not use the 'hash' result
			if (empty($result['Contents'])) $result['Contents'] = array();
			foreach ($result['Contents'] as $c) {
				$results[(string) $c['Key']] = array(
					'name' => (string) $c['Key'],
					'time' => strtotime((string) $c['LastModified']),
					'size' => (int) $c['Size'],
					// 'hash' => trim((string)$c['ETag'])
					// 'hash' => substr((string)$c['ETag'], 1, -1)
				);
				$next_marker = (string) $c['Key'];
			}

			if (isset($result['IsTruncated']) && empty($result['IsTruncated'])) return $results;

			if (isset($result['NextMarker'])) $next_marker = (string) $result['NextMarker'];

			// Loop through truncated results if max_keys isn't specified
			if (null == $max_keys && null !== $next_marker && !empty($result['IsTruncated']))
			do {
				$vars['Marker'] = $next_marker;
				$result = $this->client->listObjects($vars);

				if (!is_a($result, 'Guzzle\Service\Resource\Model') || empty($result['Contents'])) break;

				foreach ($result['Contents'] as $c) {
					$results[(string) $c['Key']] = array(
						'name' => (string) $c['Key'],
						'time' => strtotime((string) $c['LastModified']),
						'size' => (int) $c['Size'],
						// 'hash' => trim((string)$c['ETag'])
						// 'hash' => substr((string)$c['ETag'], 1, -1)
					);
					$next_marker = (string) $c['Key'];
				}

				// if ($return_common_prefixes && isset($response->body, $response->body->CommonPrefixes))
				// foreach ($response->body->CommonPrefixes as $c)
				// $results[(string)$c->Prefix] = array('prefix' => (string)$c->Prefix);

				if (isset($result['NextMarker']))
					$next_marker = (string) $result['NextMarker'];

			} while (is_a($result, 'Guzzle\Service\Resource\Model') && !empty($result['Contents']) && !empty($result['IsTruncated']));

			return $results;

		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
	}

	/**
	 * This is crude - nothing is returned
	 *
	 * @param  string $bucket Name of the Bucket
	 * @return array  Returns an array of results if bucket exists
	 */
	public function waitForBucket($bucket) {
		try {
			$this->client->waitUntil('BucketExists', array('Bucket' => $bucket));
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
	}

	/**
	 * Put a bucket
	 *
	 * @param string   $bucket   Bucket name
	 * @param constant $acl      ACL flag
	 * @param string   $location Set as "EU" to create buckets hosted in Europe
	 * @return boolean Returns true or false; or may throw an exception
	 */
	public function putBucket($bucket, $acl = self::ACL_PRIVATE, $location = false) {
		if (!$location) {
			$location = $this->region;
		} else {
			$this->setRegion($location);
		}
		$bucket_vars = array(
			'Bucket' => $bucket,
			'ACL' => $acl,
		);
		// http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_createBucket
		$location_constraint = apply_filters('updraftplus_s3_putbucket_defaultlocation', $location);
		if ('us-east-1' != $location_constraint) $bucket_vars['LocationConstraint'] = $location_constraint;
		try {
			$result = $this->client->createBucket($bucket_vars);
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('RequestId')) {
				$this->client->waitUntil('BucketExists', array('Bucket' => $bucket));
				return true;
			}
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
	}

	/**
	 * Initiate a multi-part upload (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadInitiate.html)
	 *
	 * @param string   $bucket          Bucket name
	 * @param string   $uri             Object URI
	 * @param constant $acl             ACL constant
	 * @param array    $meta_headers    Array of x-amz-meta-* headers
	 * @param array    $request_headers Array of request headers or content type as a string
	 * @param constant $storage_class   Storage class constant
	 * @return string | false
	 */
	public function initiateMultipartUpload($bucket, $uri, $acl = self::ACL_PRIVATE, $meta_headers = array(), $request_headers = array(), $storage_class = self::STORAGE_CLASS_STANDARD) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$vars = array(
			'ACL' => $acl,
			'Bucket' => $bucket,
			'Key' => $uri,
			'Metadata' => $meta_headers,
			'StorageClass' => $storage_class
		);

		$vars['ContentType'] = ('.gz' == strtolower(substr($uri, -3, 3))) ? 'application/octet-stream' : 'application/zip';

		if (!empty($this->_serverSideEncryption)) $vars['ServerSideEncryption'] = $this->_serverSideEncryption;

		try {
			$result = $this->client->createMultipartUpload($vars);
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('UploadId')) return $result->get('UploadId');
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
	}

	/**
	 * Upload a part of a multi-part set (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadUploadPart.html)
	 * The chunk is read into memory, so make sure that you have enough (or patch this function to work another way!)
	 *
	 * @param  string  $bucket      Bucket name
	 * @param  string  $uri         Object URI
	 * @param  string  $upload_id   upload_id returned previously from initiateMultipartUpload
	 * @param  string  $file_path   file to upload content from
	 * @param  integer $part_number sequential part number to upload
	 * @param  integer $part_size   number of bytes in each part (though final part may have fewer) - pass the same value each time (for this particular upload) - default 5Mb (which is Amazon's minimum)
	 * @return string (ETag) | false]
	 */
	public function uploadPart($bucket, $uri, $upload_id, $file_path, $part_number, $part_size = 5242880) {
		$vars = array(
			'Bucket' => $bucket,
			'Key' => $uri,
			'PartNumber' => $part_number,
			'UploadId' => $upload_id
		);

		// Where to begin
		$file_offset = ($part_number - 1 ) * $part_size;

		// Download the smallest of the remaining bytes and the part size
		$file_bytes = min(filesize($file_path) - $file_offset, $part_size);
		if ($file_bytes < 0) $file_bytes = 0;

// $rest->setHeader('Content-Type', 'application/octet-stream');
		$data = "";

		if ($handle = fopen($file_path, "rb")) {
			if ($file_offset > 0) fseek($handle, $file_offset);
			// $bytes_read = 0;
			while ($file_bytes > 0 && $read = fread($handle, max($file_bytes, 131072))) {
				$file_bytes = $file_bytes - strlen($read);
				// $bytes_read += strlen($read);
				$data .= $read;
			}
			fclose($handle);
		} else {
			return false;
		}

		$vars['Body'] = $data;

		try {
			$result = $this->client->uploadPart($vars);
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('ETag')) return $result->get('ETag');
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;

	}

	/**
	 * Complete a multi-part upload (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadComplete.html)
	 *
	 * @param string $bucket    Bucket name
	 * @param string $uri       Object URI
	 * @param string $upload_id upload_id returned previously from initiateMultipartUpload
	 * @param array  $parts     an ordered list of eTags of previously uploaded parts from uploadPart
	 * @return boolean Returns either true of false
	 */
	public function completeMultipartUpload($bucket, $uri, $upload_id, $parts) {
		$vars = array(
			'Bucket' => $bucket,
			'Key' => $uri,
			'UploadId' => $upload_id
		);

		$partno = 1;
		$send_parts = array();
		foreach ($parts as $etag) {
			$send_parts[] = array('ETag' => $etag, 'PartNumber' => $partno);
			$partno++;
		}

		$vars['Parts'] = $send_parts;

		try {
			$result = $this->client->completeMultipartUpload($vars);
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('ETag')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
	}

	/**
	 * Put an object from a file (legacy function)
	 *
	 * @param string   $file          Input file path
	 * @param string   $bucket        Bucket name
	 * @param string   $uri           Object URI
	 * @param constant $acl           ACL constant
	 * @param array    $meta_headers  Array of x-amz-meta-* headers
	 * @param string   $content_type  Content type
	 * @param string   $storage_class STORAGE_CLASS_STANDARD constant
	 * @return boolean returns either true of false
	 */
	public function putObjectFile($file, $bucket, $uri, $acl = self::ACL_PRIVATE, $meta_headers = array(), $content_type = null, $storage_class = self::STORAGE_CLASS_STANDARD) {
		try {
			$options = array(
				'Bucket' => $bucket,
				'Key' => $uri,
				'SourceFile' => $file,
				'StorageClass' => $storage_class,
				'ACL' => $acl
			);
			if ($content_type) $options['ContentType'] = $content_type;
			if (!empty($this->_serverSideEncryption)) $options['ServerSideEncryption'] = $this->_serverSideEncryption;
			if (!empty($meta_headers)) $options['Metadata'] = $meta_headers;
			$result = $this->client->putObject($options);
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('RequestId')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
	}


	/**
	 * Put an object from a string (legacy function)
	 * Only the first 3 parameters vary in UpdraftPlus
	 *
	 * @param string   $string       Input data
	 * @param string   $bucket       Bucket name
	 * @param string   $uri          Object URI
	 * @param constant $acl          ACL constant
	 * @param array    $meta_headers Array of x-amz-meta-* headers
	 * @param string   $content_type Content type
	 * @return boolean returns either true of false
	 */
	public function putObjectString($string, $bucket, $uri, $acl = self::ACL_PRIVATE, $meta_headers = array(), $content_type = 'text/plain') { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- legacy function
		try {
			$result = $this->client->putObject(array(
				'Bucket' => $bucket,
				'Key' => $uri,
				'Body' => $string,
				'ContentType' => $content_type
			));
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('RequestId')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
	}


	/**
	 * Get an object
	 *
	 * @param string $bucket  Bucket name
	 * @param string $uri     Object URI
	 * @param mixed  $save_to Filename or resource to write to
	 * @param mixed  $resume  - if $save_to is a resource, then this is either false or the value for a Range: header; otherwise, a boolean, indicating whether to resume if possible.
	 * @return mixed
	 */
	public function getObject($bucket, $uri, $save_to = false, $resume = false) {
		try {
			// SaveAs: "Specify where the contents of the object should be downloaded. Can be the path to a file, a resource returned by fopen, or a Guzzle\Http\EntityBodyInterface object." - http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.S3.S3Client.html#_getObject

			$range_header = false;
			if (is_resource($save_to)) {
				$fp = $save_to;
				if (!is_bool($resume)) $range_header = $resume;
			} elseif (file_exists($save_to)) {
				if ($resume && ($fp = @fopen($save_to, 'ab')) !== false) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$range_header = "bytes=".filesize($save_to).'-';
				} else {
					throw new Exception('Unable to open save file for writing: '.$save_to);
				}
			} else {
				if (($fp = @fopen($save_to, 'wb')) !== false) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$range_header = false;
				} else {
					throw new Exception('Unable to open save file for writing: '.$save_to);
				}
			}

			$vars = array(
				'Bucket' => $bucket,
				'Key' => $uri,
				'SaveAs' => $fp
			);
			if (!empty($range_header)) $vars['Range'] = $range_header;

			$result = $this->client->getObject($vars);

			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('RequestId')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
	}


	/**
	 * Get a bucket's location
	 *
	 * @param string $bucket Bucket name
	 * @return string | false
	 */
	public function getBucketLocation($bucket) {
		try {
			$result = $this->client->getBucketLocation(array('Bucket' => $bucket));
			$location = $result->get('Location');
			if ($location) return $location;
		} catch (Aws\S3\Exception\NoSuchBucketException $e) {
			return false;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
	}

	private function trigger_from_exception($e) {
		trigger_error($e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')', E_USER_WARNING);
		return false;
	}

	/**
	 * Delete an object
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri    Object URI
	 * @return boolean
	 */
	public function deleteObject($bucket, $uri) {
		try {
			$result = $this->client->deleteObject(array(
				'Bucket' => $bucket,
				'Key' => $uri
			));
			if (is_object($result) && method_exists($result, 'get') && '' != $result->get('RequestId')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
	}

	public function setCORS($policy) {
		try {
			$cors = $this->client->putBucketCors($policy);
			if (is_object($cors) && method_exists($cors, 'get') && '' != $cors->get('RequestId')) return true;
		} catch (Exception $e) {
			if ($this->useExceptions) {
				throw $e;
			} else {
				return $this->trigger_from_exception($e);
			}
		}
		return false;
		
	}
}
