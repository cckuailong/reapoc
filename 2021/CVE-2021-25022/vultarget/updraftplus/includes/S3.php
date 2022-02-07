<?php
/**
 * $Id$
 *
 * Copyright (c) 2011, Donovan Schönknecht.  All rights reserved.
 * Portions copyright (c) 2012-2021, David Anderson (https://david.dw-perspective.org.uk).  All rights reserved.
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

/**
 * Amazon S3 PHP class
 *
 * Forked originally from:
 * @link http://undesigned.org.za/2007/10/22/amazon-s3-php-class
 * @version 0.5.0-dev
 */
class UpdraftPlus_S3 {
	// ACL flags
	const ACL_PRIVATE = 'private';
	const ACL_PUBLIC_READ = 'public-read';
	const ACL_PUBLIC_READ_WRITE = 'public-read-write';
	const ACL_AUTHENTICATED_READ = 'authenticated-read';

	const STORAGE_CLASS_STANDARD = 'STANDARD';

	private $__accessKey = null; // AWS Access key
	private $__secretKey = null; // AWS Secret key
	private $__sslKey = null;
	private $__session_token = null; //For Vault temporary users
	private $_serverSideEncryption = false;

	public $endpoint = 's3.amazonaws.com';
	public $region = 'us-east-1';
	public $proxy = null;

	// Added to cope with a particular situation where the user had no permission to check the bucket location, which necessitated using DNS-based endpoints.
	public $use_dns_bucket_name = false;

	public $useSSL = false;
	public $useSSLValidation = true;
	public $useExceptions = false;

	// Added at request of a user using a non-default port.
	public $port = false;

	// SSL CURL SSL options - only needed if you are experiencing problems with your OpenSSL configuration
	public $sslKey = null;
	public $sslCert = null;
	public $sslCACert = null;

	private $__signingKeyPairId = null; // AWS Key Pair ID
	private $__signingKeyResource = false; // Key resource, freeSigningKey() must be called to clear it from memory

	public $signVer = 'v2';
	
	/**
	 * Constructor - if you're not using the class statically
	 *
	 * @param string $accessKey Access key
	 * @param string $secretKey Secret key
	 * @param boolean $useSSL Enable SSL
	 * @param boolean $sslCACert SSL Certificate
	 * @param string|null $endpoint Endpoint
	 * @param string $session_token The session token returned by AWS for temporary credentials access
	 * @param string $region Region

	 * @throws Exception If cURL extension is not present
	 *
	 * @return self
	 */
	public function __construct($accessKey = null, $secretKey = null, $useSSL = true, $sslCACert = true, $endpoint = null, $session_token = null, $region = 'us-east-1') {
		if (null !== $accessKey && null !== $secretKey) {
			$this->setAuth($accessKey, $secretKey, $session_token);
		}

		$this->setSSL($useSSL, !empty($sslCACert));

		$this->sslCACert = $sslCACert;
		if (!empty($endpoint)) {
			$this->endpoint = $endpoint;
		}

		$this->region = $region;

		if (!function_exists('curl_init')) {
			global $updraftplus;
			$updraftplus->log('The PHP cURL extension must be installed and enabled to use this remote storage method');
			throw new Exception('The PHP cURL extension must be installed and enabled to use this remote storage method');
		}
	}

	/**
	 * Set the service endpoint
	 *
	 * @param string $host Hostname
	 *
	 * @return void
	 */
	public function setEndpoint($host) {
		$this->endpoint = $host;
	}
	
	/**
	 * Set Server Side Encryption
	 * Example value: 'AES256'. See: https://docs.aws.amazon.com/AmazonS3/latest/dev/SSEUsingPHPSDK.html
	 *
	 * @param string|boolean $sse Server side encryption standard; or false for none
	 * @return void
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
	}

	/**
	 * Get the service region
	 * Note: Region calculation will be done in methods/s3.php file
	 *
	 * @return string Region
	 */
	public function getRegion() {
		return $this->region;
	}

	/**
	 * Set the service port
	 *
	 * @param Integer $port Port number
	 *
	 * @return void
	 */
	public function setPort($port) {
		$this->port = $port;
	}

	/**
	 * Set AWS access key and secret key
	 *
	 * @param string $accessKey Access key
	 * @param string $secretKey Secret key
	 *
	 * @return void
	 */
	public function setAuth($accessKey, $secretKey, $session_token = null) {
		$this->__accessKey = $accessKey;
		$this->__secretKey = $secretKey;
		$this->__session_token = $session_token;
	}

	/**
	 * Check if AWS keys have been set
	 *
	 * @return boolean
	 */
	public function hasAuth() {
		return (null !== $this->__accessKey && null !== $this->__secretKey);
	}


	/**
	 * Set SSL on or off
	 *
	 * @param boolean $enabled SSL enabled
	 * @param boolean $validate SSL certificate validation
	 *
	 * @return void
	 */
	public function setSSL($enabled, $validate = true) {
		$this->useSSL = $enabled;
		$this->useSSLValidation = $validate;
	}

	/**
	 * Get SSL value. Determines whether use it or not.
	 *
	 * @return bool
	 */
	public function getuseSSL() {
		return $this->useSSL;
	}

	/**
	 * Set SSL client certificates (experimental)
	 *
	 * @param string $sslCert SSL client certificate
	 * @param string $sslKey SSL client key
	 * @param string $sslCACert SSL CA cert (only required if you are having problems with your system CA cert)
	 *
	 * @return void
	 */
	public function setSSLAuth($sslCert = null, $sslKey = null, $sslCACert = null) {
		$this->sslCert = $sslCert;
		$this->sslKey = $sslKey;
		$this->sslCACert = $sslCACert;
	}


	/**
	 * Set proxy information
	 *
	 * @param string $host Proxy hostname and port (localhost:1234)
	 * @param string $user Proxy username
	 * @param string $pass Proxy password
	 * @param integer $type CURL proxy type
	 *
	 * @return void
	 */
	public function setProxy($host, $user = null, $pass = null, $type = CURLPROXY_SOCKS5, $port = null) {
		$this->proxy = array('host' => $host, 'type' => $type, 'user' => $user, 'pass' => $pass, 'port' => $port);
	}


	/**
	 * Set the error mode to exceptions
	 *
	 * @param boolean $enabled Enable exceptions
	 *
	 * @return void
	 */
	public function setExceptions($enabled = true) {
		$this->useExceptions = $enabled;
	}


	/**
	 * Set signing key
	 *
	 * @param string $keyPairId AWS Key Pair ID
	 * @param string $signingKey Private Key
	 * @param boolean $isFile Load private key from file, set to false to load string
	 *
	 * @return boolean
	 */
	public function setSigningKey($keyPairId, $signingKey, $isFile = true) {
		$this->__signingKeyPairId = $keyPairId;
		if (($this->__signingKeyResource = openssl_pkey_get_private($isFile ?
		file_get_contents($signingKey) : $signingKey)) !== false) return true;
		$this->__triggerError('UpdraftPlus_S3::setSigningKey(): Unable to open load private key: '.$signingKey, __FILE__, __LINE__);
		return false;
	}


	/**
	 * Free signing key from memory, MUST be called if you are using setSigningKey()
	 *
	 * @return void
	 */
	public function freeSigningKey() {
		if (false !== $this->__signingKeyResource) {
			openssl_free_key($this->__signingKeyResource);
		}
	}

	/**
	 * Set Signature Version
	 *
	 * @param string $version
	 * @return void
	 */
	public function setSignatureVersion($version = 'v2') {
		$this->signVer = $version;
	}

	/**
	 * Internal error handler
	 *
	 * @param string $message Error message
	 * @param string $file Filename
	 * @param integer $line Line number
	 * @param integer $code Error code
	 *
	 * @internal Internal error handler
	 * @throws  UpdraftPlus_S3Exception
	 *
	 * @return void
	 */
	private function __triggerError($message, $file, $line, $code = 0) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		if ($this->useExceptions) {
			throw new UpdraftPlus_S3Exception($message, $file, $line, $code);
		} else {
			trigger_error($message, E_USER_WARNING);
		}
	}


	/**
	 * Get a list of buckets
	 *
	 * @param boolean $detailed Returns detailed bucket list when true
	 *
	 * @return array | false
	 */
	public function listBuckets($detailed = false) {
		$rest = new UpdraftPlus_S3Request('GET', '', '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::listBuckets(): [%s] %s", $rest->error['code'],
			$rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		$results = array();
		if (!isset($rest->body->Buckets)) return $results;

		if ($detailed) {
			if (isset($rest->body->Owner, $rest->body->Owner->ID, $rest->body->Owner->DisplayName))
			$results['owner'] = array(
				'id' => (string)$rest->body->Owner->ID, 'name' => (string)$rest->body->Owner->ID
			);
			$results['buckets'] = array();
			foreach ($rest->body->Buckets->Bucket as $b) {
				$results['buckets'][] = array(
					'name' => (string)$b->Name, 'time' => strtotime((string)$b->CreationDate)
				);
			}
		} else {
			foreach ($rest->body->Buckets->Bucket as $b) $results[] = (string)$b->Name;
		}

		return $results;
	}

	public function useDNSBucketName($use = true, $bucket = '') {
		$this->use_dns_bucket_name = $use;
		return true;
	}

	/**
	 * Get contents for a bucket
	 *
	 * If maxKeys is null this method will loop through truncated result sets
	 *
	 * @param string $bucket Bucket name
	 * @param string $prefix Prefix
	 * @param string $marker Marker (last file listed)
	 * @param string $maxKeys Max keys (maximum number of keys to return)
	 * @param string $delimiter Delimiter
	 * @param boolean $returnCommonPrefixes Set to true to return CommonPrefixes
	 *
	 * @return array | false
	 */
	public function getBucket($bucket, $prefix = null, $marker = null, $maxKeys = null, $delimiter = null, $returnCommonPrefixes = false) {
		$rest = new UpdraftPlus_S3Request('GET', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		if (0 == $maxKeys) $maxKeys = null;
		if (!empty($prefix)) $rest->setParameter('prefix', $prefix);
		if (!empty($marker)) $rest->setParameter('marker', $marker);
		if (!empty($maxKeys)) $rest->setParameter('max-keys', $maxKeys);
		if (!empty($delimiter)) $rest->setParameter('delimiter', $delimiter);
		$response = $rest->getResponse();
		if (false === $response->error && 200 !== $response->code) {
			$response->error = array('code' => $response->code, 'message' => 'Unexpected HTTP status');
		}
		if (false !== $response->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getBucket(): [%s] %s",
			$response->error['code'], $response->error['message']), __FILE__, __LINE__);
			return false;
		}

		$results = array();

		$nextMarker = null;
		if (isset($response->body, $response->body->Contents))
		foreach ($response->body->Contents as $c) {
			$results[(string)$c->Key] = array(
				'name' => (string)$c->Key,
				'time' => strtotime((string)$c->LastModified),
				'size' => (int)$c->Size,
				'hash' => substr((string)$c->ETag, 1, -1)
			);
			$nextMarker = (string)$c->Key;
		}

		if ($returnCommonPrefixes && isset($response->body, $response->body->CommonPrefixes))
			foreach ($response->body->CommonPrefixes as $c)
				$results[(string)$c->Prefix] = array('prefix' => (string)$c->Prefix);

		if (isset($response->body, $response->body->IsTruncated) &&
		(string)$response->body->IsTruncated == 'false') return $results;

		if (isset($response->body, $response->body->NextMarker))
			$nextMarker = (string)$response->body->NextMarker;

		// Loop through truncated results if maxKeys isn't specified
		if (null == $maxKeys && null !== $nextMarker && 'true' == (string)$response->body->IsTruncated)
		do
		{
			$rest = new UpdraftPlus_S3Request('GET', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
			if (!empty($prefix)) $rest->setParameter('prefix', $prefix);
			$rest->setParameter('marker', $nextMarker);
			if (!empty($delimiter)) $rest->setParameter('delimiter', $delimiter);

			if (false == ($response = $rest->getResponse()) || 200 !== $response->code) break;

			if (isset($response->body, $response->body->Contents))
			foreach ($response->body->Contents as $c)
			{
				$results[(string)$c->Key] = array(
					'name' => (string)$c->Key,
					'time' => strtotime((string)$c->LastModified),
					'size' => (int)$c->Size,
					'hash' => substr((string)$c->ETag, 1, -1)
				);
				$nextMarker = (string)$c->Key;
			}

			if ($returnCommonPrefixes && isset($response->body, $response->body->CommonPrefixes))
				foreach ($response->body->CommonPrefixes as $c)
					$results[(string)$c->Prefix] = array('prefix' => (string)$c->Prefix);

			if (isset($response->body, $response->body->NextMarker))
				$nextMarker = (string)$response->body->NextMarker;

		} while (false !== $response && 'true' == (string)$response->body->IsTruncated);

		return $results;
	}


	/**
	 * Put a bucket
	 *
	 * @param  string $bucket Bucket name
	 * @param  string ACL_PRIVATE ACL flag
	 * @param  mixed  $location Set as "EU" to create buckets hosted in Europe
	 * @return boolean
	 */
	public function putBucket($bucket, $acl = self::ACL_PRIVATE, $location = false) {
		$rest = new UpdraftPlus_S3Request('PUT', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setAmzHeader('x-amz-acl', $acl);

		if (false === $location) $location = $this->getRegion();

		if (false !== $location && 'us-east-1' !== $location) {
			$dom = new DOMDocument;
			$createBucketConfiguration = $dom->createElement('CreateBucketConfiguration');
			$locationConstraint = $dom->createElement('LocationConstraint', $location);
			$createBucketConfiguration->appendChild($locationConstraint);
			$dom->appendChild($createBucketConfiguration);
			$rest->data = $dom->saveXML();
			$rest->size = strlen($rest->data);
			$rest->setHeader('Content-Type', 'application/xml');
		}
		$rest = $rest->getResponse();

		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::putBucket({$bucket}, {$acl}, {$location}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	/**
	 * Delete an empty bucket
	 *
	 * @param  string $bucket Bucket name
	 *
	 * @return boolean
	 */
	public function deleteBucket($bucket) {
		$rest = new UpdraftPlus_S3Request('DELETE', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest = $rest->getResponse();
		if (false === $rest->error && 204 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::deleteBucket({$bucket}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	/**
	 * Create input info array for putObject()
	 *
	 * @param  string $file Input file
	 * @param  mixed  $md5sum Use MD5 hash (supply a string if you want to use your own)
	 * @return array | false
	 */
	public function inputFile($file, $md5sum = true) {
		if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
			$this->__triggerError('UpdraftPlus_S3::inputFile(): Unable to open input file: '.$file, __FILE__, __LINE__);
			return false;
		}
		return array('file' => $file, 'size' => filesize($file), 'md5sum' => $md5sum !== false ?
		(is_string($md5sum) ? $md5sum : base64_encode(md5_file($file, true))) : '', 'sha256sum' => hash_file('sha256', $file));
	}


	/**
	 * Create input array info for putObject() with a resource
	 *
	 * @param string $resource Input resource to read from
	 * @param integer $bufferSize Input byte size
	 * @param string $md5sum MD5 hash to send (optional)
	 * @return array | false
	 */
	public function inputResource(&$resource, $bufferSize, $md5sum = '') {
		if (!is_resource($resource) || $bufferSize < 0) {
			$this->__triggerError('UpdraftPlus_S3::inputResource(): Invalid resource or buffer size', __FILE__, __LINE__);
			return false;
		}
		$input = array('size' => $bufferSize, 'md5sum' => $md5sum);
		$input['fp'] =& $resource;
		return $input;
	}

	/**
	 * Initiate a multi-part upload (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadInitiate.html)
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $acl ACL constant
	 * @param array $metaHeaders Array of x-amz-meta-* headers
	 * @param array $requestHeaders Array of request headers or content type as a string
	 * @param string $storageClass Storage class constant
	 *
	 * @return string | false
	 */
	public function initiateMultipartUpload ($bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = self::STORAGE_CLASS_STANDARD) {

		$rest = new UpdraftPlus_S3Request('POST', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('uploads','');

		// Custom request headers (Content-Type, Content-Disposition, Content-Encoding)
		if (is_array($requestHeaders))
			foreach ($requestHeaders as $h => $v) $rest->setHeader($h, $v);

		// Set storage class
		if ($storageClass !== self::STORAGE_CLASS_STANDARD) // Storage class
			$rest->setAmzHeader('x-amz-storage-class', $storageClass);

		// Set ACL headers
		$rest->setAmzHeader('x-amz-acl', $acl);
		foreach ($metaHeaders as $h => $v) $rest->setAmzHeader('x-amz-meta-'.$h, $v);

		// Carry out the HTTP operation
		$rest->getResponse();

		if (false === $rest->response->error && 200 !== $rest->response->code) {
			$rest->response->error = array('code' => $rest->response->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->response->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::initiateMultipartUpload(): [%s] %s",
			$rest->response->error['code'], $rest->response->error['message']), __FILE__, __LINE__);
			return false;
		} elseif (isset($rest->response->body)) {
			// DreamObjects already returns a SimpleXMLElement here. Not sure how that works.
			if (is_a($rest->response->body, 'SimpleXMLElement')) {
				$body = $rest->response->body;
			} else {
				$body = new SimpleXMLElement($rest->response->body);
			}
			return (string) $body->UploadId;
		}

		// It is a programming error if we reach this line
		return false;

	}

	/**
	 * Upload a part of a multi-part set (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadUploadPart.html)
	 * The chunk is read into memory, so make sure that you have enough (or patch this function to work another way!)
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $uploadId uploadId returned previously from initiateMultipartUpload
	 * @param integer $partNumber sequential part number to upload
	 * @param string $filePath file to upload content from
	 * @param integer $partSize number of bytes in each part (though final part may have fewer) - pass the same value each time (for this particular upload) - default 5Mb (which is Amazon's minimum)
	 * @return string (ETag) | false
	 */
	public function uploadPart ($bucket, $uri, $uploadId, $filePath, $partNumber, $partSize = 5242880) {

		$rest = new UpdraftPlus_S3Request('PUT', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('partNumber', $partNumber);
		$rest->setParameter('uploadId', $uploadId);

		// Where to begin
		$fileOffset = ($partNumber - 1 ) * $partSize;

		// Download the smallest of the remaining bytes and the part size
		$fileBytes = min(filesize($filePath) - $fileOffset, $partSize);
		if ($fileBytes < 0) $fileBytes = 0;

		$rest->setHeader('Content-Type', 'application/octet-stream');
		$rest->data = "";

		if ($handle = fopen($filePath, "rb")) {
			if ($fileOffset >0) fseek($handle, $fileOffset);
			$bytes_read = 0;
			while ($fileBytes>0 && $read = fread($handle, max($fileBytes, 131072))) {
				$fileBytes = $fileBytes - strlen($read);
				$bytes_read += strlen($read);
				$rest->data = $rest->data . $read;
			}
			fclose($handle);
		} else {
			return false;
		}

 		$rest->setHeader('Content-MD5', base64_encode(md5($rest->data, true)));
		$rest->size = $bytes_read;

		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::uploadPart(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return $rest->headers['hash'];
	}

	/**
	 * Complete a multi-part upload (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadComplete.html)
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $uploadId uploadId returned previously from initiateMultipartUpload
	 * @param array $parts an ordered list of eTags of previously uploaded parts from uploadPart
	 * @return boolean
	 */
	public function completeMultipartUpload($bucket, $uri, $uploadId, $parts) {
		$rest = new UpdraftPlus_S3Request('POST', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('uploadId', $uploadId);

		$xml = "<CompleteMultipartUpload>\n";
		$partno = 1;
		foreach ($parts as $etag) {
			$xml .= "<Part><PartNumber>$partno</PartNumber><ETag>$etag</ETag></Part>\n";
			$partno++;
		}
		$xml .= "</CompleteMultipartUpload>";

		$rest->data = $xml;
		$rest->size = strlen($rest->data);
		$rest->setHeader('Content-Type', 'application/xml');

		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			// Special case: when the error means "you've already done that". Turn it into success. See in: https://trello.com/c/6jJoiCG5
			if ('InternalError' == $rest->error['code'] && 'This multipart completion is already in progress' == $rest->error['message']) {
				return true;
			}
			$this->__triggerError(sprintf("UpdraftPlus_S3::completeMultipartUpload(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;

	}

	/**
	 * Abort a multi-part upload (http://docs.amazonwebservices.com/AmazonS3/latest/API/mpUploadAbort.html)
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $uploadId uploadId returned previously from initiateMultipartUpload
	 * @return boolean
	 */
	// TODO: From this line
	public function abortMultipartUpload ($bucket, $uri, $uploadId) {
		$rest = new UpdraftPlus_S3Request('DELETE', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('uploadId', $uploadId);
		$rest = $rest->getResponse();
		if (false === $rest->error && 204 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::abortMultipartUpload(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}

	/**
	 * Put an object
	 *
	 * @param mixed $input Input data
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $acl ACL constant
	 * @param array $metaHeaders Array of x-amz-meta-* headers
	 * @param array $requestHeaders Array of request headers or content type as a string
	 * @param string $storageClass Storage class constant
	 *
	 * @return boolean
	 */
	public function putObject($input, $bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = self::STORAGE_CLASS_STANDARD) {
		if ($input === false) return false;
		$rest = new UpdraftPlus_S3Request('PUT', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);

		if (!is_array($input)) $input = array(
			'data' => $input, 'size' => strlen($input),
			'md5sum' => base64_encode(md5($input, true)),
			'sha256sum' => hash('sha256', $input)
		);

		// Data
		if (isset($input['fp']))
			$rest->fp =& $input['fp'];
		elseif (isset($input['file']) && is_file($input['file']))
			$rest->fp = @fopen($input['file'], 'rb');
		elseif (isset($input['data']))
			$rest->data = $input['data'];

		// Content-Length (required)
		if (isset($input['size']) && $input['size'] >= 0) {
			$rest->size = $input['size'];
		} else {
			if (isset($input['file']))
				$rest->size = filesize($input['file']);
			elseif (isset($input['data']))
				$rest->size = strlen($input['data']);
		}

		// Custom request headers (Content-Type, Content-Disposition, Content-Encoding)
		if (is_array($requestHeaders))
			foreach ($requestHeaders as $h => $v) $rest->setHeader($h, $v);
		elseif (is_string($requestHeaders)) // Support for legacy contentType parameter
			$input['type'] = $requestHeaders;

		// Content-Type
		if (!isset($input['type'])) {
			if (isset($requestHeaders['Content-Type']))
				$input['type'] =& $requestHeaders['Content-Type'];
			elseif (isset($input['file']))
				$input['type'] = $this->__getMimeType($input['file']);
			else
				$input['type'] = 'application/octet-stream';
		}

		if ($storageClass !== self::STORAGE_CLASS_STANDARD) // Storage class
			$rest->setAmzHeader('x-amz-storage-class', $storageClass);
			
		if (!empty($this->_serverSideEncryption)) {
			$rest->setAmzHeader('x-amz-server-side-encryption', $this->_serverSideEncryption);
		}
		// We need to post with Content-Length and Content-Type, MD5 is optional
		if ($rest->size >= 0 && (false !== $rest->fp || false !== $rest->data)) {
			$rest->setHeader('Content-Type', $input['type']);
			if (isset($input['md5sum'])) $rest->setHeader('Content-MD5', $input['md5sum']);

			if (isset($input['sha256sum'])) $rest->setAmzHeader('x-amz-content-sha256', $input['sha256sum']);

			$rest->setAmzHeader('x-amz-acl', $acl);
			foreach ($metaHeaders as $h => $v) $rest->setAmzHeader('x-amz-meta-'.$h, $v);
			$rest->getResponse();
		} else {
			$rest->response->error = array('code' => 0, 'message' => 'Missing input parameters');
		}

		if (false === $rest->response->error && 200 !== $rest->response->code) {
			$rest->response->error = array('code' => $rest->response->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->response->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::putObject(): [%s] %s",
			$rest->response->error['code'], $rest->response->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	/**
	 * Put an object from a file (legacy function)
	 *
	 * @param string $file Input file path
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $acl ACL constant
	 * @param array  $metaHeaders Array of x-amz-meta-* headers
	 * @param string $contentType Content type
	 * @param string $storageClass
	 *
	 * @return boolean
	 */
	public function putObjectFile($file, $bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $contentType = null, $storageClass = self::STORAGE_CLASS_STANDARD) {
		return $this->putObject($this->inputFile($file), $bucket, $uri, $acl, $metaHeaders, $contentType, $storageClass);
	}

	/**
	 * Put an object from a string (legacy function)
	 *
	 * @param string $string Input data
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param string $acl ACL constant
	 * @param array $metaHeaders Array of x-amz-meta-* headers
	 * @param string $contentType Content type
	 * @return boolean
	 */
	public function putObjectString($string, $bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $contentType = 'text/plain') {
		return $this->putObject($string, $bucket, $uri, $acl, $metaHeaders, $contentType);
	}

	/**
	 * Get an object
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param mixed $saveTo Filename or resource to write to
	 * @param mixed $resume - if $saveTo is a resource, then this is either false or the value for a Range: header; otherwise, a boolean, indicating whether to resume if possible.
	 * @return mixed
	 */
	public function getObject($bucket, $uri, $saveTo = false, $resume = false) {
		$rest = new UpdraftPlus_S3Request('GET', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		if (false !== $saveTo) {
			if (is_resource($saveTo)) {
				$rest->fp = $saveTo;
				if (!is_bool($resume)) $rest->setHeader('Range', $resume);
			} else {
				if ($resume && file_exists($saveTo)) {
					if (false !== ($rest->fp = @fopen($saveTo, 'ab'))) {
						$rest->setHeader('Range', "bytes=".filesize($saveTo).'-');
						$rest->file = realpath($saveTo);
					} else {
						$rest->response->error = array('code' => 0, 'message' => 'Unable to open save file for writing: '.$saveTo);
					}
				} else {
					if (false !== ($rest->fp = @fopen($saveTo, 'wb')))
						$rest->file = realpath($saveTo);
					else
						$rest->response->error = array('code' => 0, 'message' => 'Unable to open save file for writing: '.$saveTo);
				}
			}
		}
		if (false === $rest->response->error) $rest->getResponse();

		if (false === $rest->response->error && ( !$resume && 200 != $rest->response->code) || ( $resume && 206 != $rest->response->code && 200 != $rest->response->code))
			$rest->response->error = array('code' => $rest->response->code, 'message' => 'Unexpected HTTP status');
		if (false !== $rest->response->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getObject({$bucket}, {$uri}): [%s] %s",
			$rest->response->error['code'], $rest->response->error['message']), __FILE__, __LINE__);
			return false;
		}
		return $rest->response;
	}


	/**
	 * Get object information
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param boolean $returnInfo Return response information
	 *
	 * @return mixed | false
	 */
	public function getObjectInfo($bucket, $uri, $returnInfo = true) {
		$rest = new UpdraftPlus_S3Request('HEAD', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest = $rest->getResponse();
		if (false === $rest->error && (200 !== $rest->code && 404 !== $rest->code))
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getObjectInfo({$bucket}, {$uri}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return (200 == $rest->code) ? ($returnInfo ? $rest->headers : true) : false;
	}

	/**
	 * Copy an object
	 *
	 * @param string $bucket Source bucket name
	 * @param string $uri Source object URI
	 * @param string $bucket Destination bucket name
	 * @param string $uri Destination object URI
	 * @param string $acl ACL constant
	 * @param array $metaHeaders Optional array of x-amz-meta-* headers
	 * @param array $requestHeaders Optional array of request headers (content type, disposition, etc.)
	 * @param string $storageClass Storage class constant
	 *
	 * @return mixed | false
	 */
	public function copyObject($srcBucket, $srcUri, $bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = self::STORAGE_CLASS_STANDARD) {
		$rest = new UpdraftPlus_S3Request('PUT', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setHeader('Content-Length', 0);
		foreach ($requestHeaders as $h => $v) $rest->setHeader($h, $v);
		foreach ($metaHeaders as $h => $v) $rest->setAmzHeader('x-amz-meta-'.$h, $v);
		if (self::STORAGE_CLASS_STANDARD !== $storageClass) // Storage class
			$rest->setAmzHeader('x-amz-storage-class', $storageClass);
		$rest->setAmzHeader('x-amz-acl', $acl);
		$rest->setAmzHeader('x-amz-copy-source', sprintf('/%s/%s', $srcBucket, rawurlencode($srcUri)));
		if (sizeof($requestHeaders) > 0 || sizeof($metaHeaders) > 0)
			$rest->setAmzHeader('x-amz-metadata-directive', 'REPLACE');

		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::copyObject({$srcBucket}, {$srcUri}, {$bucket}, {$uri}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return isset($rest->body->LastModified, $rest->body->ETag) ? array(
			'time' => strtotime((string)$rest->body->LastModified),
			'hash' => substr((string)$rest->body->ETag, 1, -1)
		) : false;
	}


	/**
	 * Set logging for a bucket
	 *
	 * @param string $bucket Bucket name
	 * @param string $targetBucket Target bucket (where logs are stored)
	 * @param string $targetPrefix Log prefix (e,g; domain.com-)
	 *
	 * @return boolean
	 */
	public function setBucketLogging($bucket, $targetBucket, $targetPrefix = null) {
		// The S3 log delivery group has to be added to the target bucket's ACP
		if (null !== $targetBucket && false !== ($acp = $this->getAccessControlPolicy($targetBucket, ''))) {
			// Only add permissions to the target bucket when they do not exist
			$aclWriteSet = false;
			$aclReadSet = false;
			foreach ($acp['acl'] as $acl)
			if ('Group' == $acl['type'] && 'http://acs.amazonaws.com/groups/s3/LogDelivery' == $acl['uri']) {
				if ($acl['permission'] == 'WRITE') $aclWriteSet = true;
				elseif ($acl['permission'] == 'READ_ACP') $aclReadSet = true;
			}
			if (!$aclWriteSet) $acp['acl'][] = array(
				'type' => 'Group', 'uri' => 'http://acs.amazonaws.com/groups/s3/LogDelivery', 'permission' => 'WRITE'
			);
			if (!$aclReadSet) $acp['acl'][] = array(
				'type' => 'Group', 'uri' => 'http://acs.amazonaws.com/groups/s3/LogDelivery', 'permission' => 'READ_ACP'
			);
			if (!$aclReadSet || !$aclWriteSet) $this->setAccessControlPolicy($targetBucket, '', $acp);
		}

		$dom = new DOMDocument;
		$bucketLoggingStatus = $dom->createElement('BucketLoggingStatus');
		$bucketLoggingStatus->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
		if (null !== $targetBucket) {
			if (null == $targetPrefix) $targetPrefix = $bucket . '-';
			$loggingEnabled = $dom->createElement('LoggingEnabled');
			$loggingEnabled->appendChild($dom->createElement('TargetBucket', $targetBucket));
			$loggingEnabled->appendChild($dom->createElement('TargetPrefix', $targetPrefix));
			// TODO: Add TargetGrants?
			$bucketLoggingStatus->appendChild($loggingEnabled);
		}
		$dom->appendChild($bucketLoggingStatus);

		$rest = new UpdraftPlus_S3Request('PUT', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('logging', null);
		$rest->data = $dom->saveXML();
		$rest->size = strlen($rest->data);
		$rest->setHeader('Content-Type', 'application/xml');
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::setBucketLogging({$bucket}, {$targetBucket}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	/**
	 * Get logging status for a bucket
	 *
	 * This will return false if logging is not enabled.
	 * Note: To enable logging, you also need to grant write access to the log group
	 *
	 * @param string $bucket Bucket name
	 *
	 * @return array | false
	 */
	public function getBucketLogging($bucket) {
		$rest = new UpdraftPlus_S3Request('GET', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('logging', null);
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getBucketLogging({$bucket}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		if (!isset($rest->body->LoggingEnabled)) return false; // No logging
		return array(
			'targetBucket' => (string)$rest->body->LoggingEnabled->TargetBucket,
			'targetPrefix' => (string)$rest->body->LoggingEnabled->TargetPrefix,
		);
	}


	/**
	 * Disable bucket logging
	 *
	 * @param string $bucket Bucket name
	 *
	 * @return boolean
	 */
	public function disableBucketLogging($bucket) {
		return $this->setBucketLogging($bucket, null);
	}

	/**
	 * Get a bucket's location
	 *
	 * @param string $bucket Bucket name
	 *
	 * @return string | false
	 */
	public function getBucketLocation($bucket) {
		$rest = new UpdraftPlus_S3Request('GET', $bucket, '', $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('location', null);
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getBucketLocation({$bucket}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		
		return (isset($rest->body[0]) && (string)$rest->body[0] !== '') ? (string)$rest->body[0] : 'US';
	}

	/**
	 * Set object or bucket Access Control Policy
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param array $acp Access Control Policy Data (same as the data returned from getAccessControlPolicy)
	 *
	 * @return boolean
	 */
	public function setAccessControlPolicy($bucket, $uri = '', $acp = array()) {
		$dom = new DOMDocument;
		$dom->formatOutput = true;
		$accessControlPolicy = $dom->createElement('AccessControlPolicy');
		$accessControlList = $dom->createElement('AccessControlList');

		// It seems the owner has to be passed along too
		$owner = $dom->createElement('Owner');
		$owner->appendChild($dom->createElement('ID', $acp['owner']['id']));
		$owner->appendChild($dom->createElement('DisplayName', $acp['owner']['name']));
		$accessControlPolicy->appendChild($owner);

		foreach ($acp['acl'] as $g) {
			$grant = $dom->createElement('Grant');
			$grantee = $dom->createElement('Grantee');
			$grantee->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
			if (isset($g['id'])) {
				// CanonicalUser (DisplayName is omitted)
				$grantee->setAttribute('xsi:type', 'CanonicalUser');
				$grantee->appendChild($dom->createElement('ID', $g['id']));
			} elseif (isset($g['email'])) {
				// AmazonCustomerByEmail
				$grantee->setAttribute('xsi:type', 'AmazonCustomerByEmail');
				$grantee->appendChild($dom->createElement('EmailAddress', $g['email']));
			} elseif ('Group' == $g['type']) {
				// Group
				$grantee->setAttribute('xsi:type', 'Group');
				$grantee->appendChild($dom->createElement('URI', $g['uri']));
			}
			$grant->appendChild($grantee);
			$grant->appendChild($dom->createElement('Permission', $g['permission']));
			$accessControlList->appendChild($grant);
		}

		$accessControlPolicy->appendChild($accessControlList);
		$dom->appendChild($accessControlPolicy);

		$rest = new UpdraftPlus_S3Request('PUT', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('acl', null);
		$rest->data = $dom->saveXML();
		$rest->size = strlen($rest->data);
		$rest->setHeader('Content-Type', 'application/xml');
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code)  {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::setAccessControlPolicy({$bucket}, {$uri}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}


	/**
	 * Get object or bucket Access Control Policy
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @return mixed | false
	 */
	public function getAccessControlPolicy($bucket, $uri = '') {
		$rest = new UpdraftPlus_S3Request('GET', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest->setParameter('acl', null);
		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::getAccessControlPolicy({$bucket}, {$uri}): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}

		$acp = array();
		if (isset($rest->body->Owner, $rest->body->Owner->ID, $rest->body->Owner->DisplayName))
			$acp['owner'] = array(
				'id' => (string)$rest->body->Owner->ID, 'name' => (string)$rest->body->Owner->DisplayName
			);

		if (isset($rest->body->AccessControlList)) {
			$acp['acl'] = array();
			foreach ($rest->body->AccessControlList->Grant as $grant) {
				foreach ($grant->Grantee as $grantee) {
					if (isset($grantee->ID, $grantee->DisplayName)) // CanonicalUser
						$acp['acl'][] = array(
							'type' => 'CanonicalUser',
							'id' => (string)$grantee->ID,
							'name' => (string)$grantee->DisplayName,
							'permission' => (string)$grant->Permission
						);
					elseif (isset($grantee->EmailAddress)) // AmazonCustomerByEmail
						$acp['acl'][] = array(
							'type' => 'AmazonCustomerByEmail',
							'email' => (string)$grantee->EmailAddress,
							'permission' => (string)$grant->Permission
						);
					elseif (isset($grantee->URI)) // Group
						$acp['acl'][] = array(
							'type' => 'Group',
							'uri' => (string)$grantee->URI,
							'permission' => (string)$grant->Permission
						);
					else continue;
				}
			}
		}
		return $acp;
	}

	/**
	 * Delete an object
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 *
	 * @return boolean
	 */
	public function deleteObject($bucket, $uri) {
		$rest = new UpdraftPlus_S3Request('DELETE', $bucket, $uri, $this->endpoint, $this->use_dns_bucket_name, $this);
		$rest = $rest->getResponse();
		if (false === $rest->error && 204 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("UpdraftPlus_S3::deleteObject(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}

	/**
	 * Get a query string authenticated URL
	 *
	 * @param string $bucket Bucket name
	 * @param string $uri Object URI
	 * @param integer $lifetime Lifetime in seconds
	 * @param boolean $hostBucket Use the bucket name as the hostname
	 * @param boolean $https Use HTTPS ($hostBucket should be false for SSL verification)
	 *
	 * @return string
	 */
	public function getAuthenticatedURL($bucket, $uri, $lifetime, $hostBucket = false, $https = false) {
		$expires = time() + $lifetime;
		$uri = str_replace(array('%2F', '%2B'), array('/', '+'), rawurlencode($uri));
		return sprintf(($https ? 'https' : 'http').'://%s/%s?AWSAccessKeyId=%s&Expires=%u&Signature=%s',
		// $hostBucket ? $bucket : $bucket.'.s3.amazonaws.com', $uri, $this->__accessKey, $expires,
		$hostBucket ? $bucket : 's3.amazonaws.com/'.$bucket, $uri, $this->__accessKey, $expires,
		urlencode($this->__getHash("GET\n\n\n{$expires}\n/{$bucket}/{$uri}")));
	}

	/**
	 * Get upload POST parameters for form uploads
	 *
	 * @param string  $bucket Bucket name
	 * @param string  $uriPrefix Object URI prefix
	 * @param string  $acl ACL constant
	 * @param integer $lifetime Lifetime in seconds
	 * @param integer $maxFileSize Maximum file size in bytes (default 5MB)
	 * @param string  $successRedirect Redirect URL or 200 / 201 status code
	 * @param array   $amzHeaders Array of x-amz-meta-* headers
	 * @param array   $headers Array of request headers or content type as a string
	 * @param boolean $flashVars Includes additional "Filename" variable posted by Flash
	 *
	 * @return object
	 */
	public function getHttpUploadPostParams($bucket, $uriPrefix = '', $acl = self::ACL_PRIVATE, $lifetime = 3600,
	$maxFileSize = 5242880, $successRedirect = "201", $amzHeaders = array(), $headers = array(), $flashVars = false) {
		// Create policy object
		$policy = new stdClass;
		$policy->expiration = gmdate('Y-m-d\TH:i:s\Z', (time() + $lifetime));
		$policy->conditions = array();
		$obj = new stdClass; $obj->bucket = $bucket; array_push($policy->conditions, $obj);
		$obj = new stdClass; $obj->acl = $acl; array_push($policy->conditions, $obj);

		$obj = new stdClass; // 200 for non-redirect uploads
		if (is_numeric($successRedirect) && in_array((int)$successRedirect, array(200, 201)))
			$obj->success_action_status = (string)$successRedirect;
		else // URL
			$obj->success_action_redirect = $successRedirect;
		array_push($policy->conditions, $obj);

		if (self::ACL_PUBLIC_READ !== $acl)
			array_push($policy->conditions, array('eq', '$acl', $acl));

		array_push($policy->conditions, array('starts-with', '$key', $uriPrefix));
		if ($flashVars) array_push($policy->conditions, array('starts-with', '$Filename', ''));
		foreach (array_keys($headers) as $headerKey)
			array_push($policy->conditions, array('starts-with', '$'.$headerKey, ''));
		foreach ($amzHeaders as $headerKey => $headerVal) {
			$obj = new stdClass;
			$obj->{$headerKey} = (string)$headerVal;
			array_push($policy->conditions, $obj);
		}
		array_push($policy->conditions, array('content-length-range', 0, $maxFileSize));
		$policy = base64_encode(str_replace('\/', '/', json_encode($policy)));

		// Create parameters
		$params = new stdClass;
		$params->AWSAccessKeyId = $this->__accessKey;
		$params->key = $uriPrefix.'${filename}';
		$params->acl = $acl;
		$params->policy = $policy; unset($policy);
		$params->signature = $this->__getHash($params->policy);
		if (is_numeric($successRedirect) && in_array((int)$successRedirect, array(200, 201)))
			$params->success_action_status = (string)$successRedirect;
		else
			$params->success_action_redirect = $successRedirect;
		foreach ($headers as $headerKey => $headerVal) $params->{$headerKey} = (string)$headerVal;
		foreach ($amzHeaders as $headerKey => $headerVal) $params->{$headerKey} = (string)$headerVal;
		return $params;
	}

	/**
	 * Get MIME type for file
	 *
	 * @internal Used to get mime types
	 *
	 * @param string &$file File path
	 *
	 * @return string
	 */
	public function __getMimeType(&$file) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		$type = false;
		// Fileinfo documentation says fileinfo_open() will use the
		// MAGIC env var for the magic file
		if (extension_loaded('fileinfo') && isset($_ENV['MAGIC']) &&
		false !== ($finfo = finfo_open(FILEINFO_MIME, $_ENV['MAGIC']))) {// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_openFound -- The function finfo_open() is not present in PHP version 5.2 or earlier
			if (false !== ($type = finfo_file($finfo, $file))) {// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_fileFound -- The function finfo_file() is not present in PHP version 5.2 or earlier
				// Remove the charset and grab the last content-type
				$type = explode(' ', str_replace('; charset=', ';charset=', $type));
				$type = array_pop($type);
				$type = explode(';', $type);
				$type = trim(array_shift($type));
			}
			finfo_close($finfo);// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_closeFound -- The function finfo_close() is not present in PHP version 5.2 or earlier

		// If anyone is still using mime_content_type()
		} elseif (function_exists('mime_content_type')) {
			$type = trim(mime_content_type($file));
		}

		if (false !== $type && strlen($type) > 0) return $type;

		// Otherwise do it the old fashioned way
		static $exts = array(
			'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png',
			'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'ico' => 'image/x-icon',
			'swf' => 'application/x-shockwave-flash', 'pdf' => 'application/pdf',
			'zip' => 'application/zip', 'gz' => 'application/x-gzip',
			'tar' => 'application/x-tar', 'bz' => 'application/x-bzip',
			'bz2' => 'application/x-bzip2', 'txt' => 'text/plain',
			'asc' => 'text/plain', 'htm' => 'text/html', 'html' => 'text/html',
			'css' => 'text/css', 'js' => 'text/javascript',
			'xml' => 'text/xml', 'xsl' => 'application/xsl+xml',
			'ogg' => 'application/ogg', 'mp3' => 'audio/mpeg', 'wav' => 'audio/x-wav',
			'avi' => 'video/x-msvideo', 'mpg' => 'video/mpeg', 'mpeg' => 'video/mpeg',
			'mov' => 'video/quicktime', 'flv' => 'video/x-flv', 'php' => 'text/x-php'
		);
		$ext = strtolower(pathInfo($file, PATHINFO_EXTENSION));
		return isset($exts[$ext]) ? $exts[$ext] : 'application/octet-stream';
	}


	/**
	 * Generate the auth string: "AWS AccessKey:Signature"
	 *
	 * @internal Used by UpdraftPlus_S3Request::getResponse()
	 *
	 * @param string $string String to sign
	 *
	 * @return string
	 */
	public function __getSignature($string) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		return 'AWS '.$this->__accessKey.':'.$this->__getHash($string);
	}


	/**
	 * Creates a HMAC-SHA1 hash
	 *
	 * This uses the hash extension if loaded
	 *
	 * @internal Used by __getSignature()
	 *
	 * @param string $string String to sign
	 *
	 * @return string
	 */
	private function __getHash($string) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		return base64_encode(extension_loaded('hash') ?
		hash_hmac('sha1', $string, $this->__secretKey, true) : pack('H*', sha1(
		(str_pad($this->__secretKey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
		pack('H*', sha1((str_pad($this->__secretKey, 64, chr(0x00)) ^
		(str_repeat(chr(0x36), 64))) . $string)))));
	}

	/**
	 * Generate the headers for AWS Signature V4
	 *
	 * @internal Used by UpdraftPlus_S3Request::getResponse()
	 * @param array $aHeaders amzHeaders
	 * @param array $headers
	 * @param string $method
	 * @param string $uri
	 * @param string $data
	 *
	 * @return array $headers
	 */
	public function __getSignatureV4($aHeaders, $headers, $method = 'GET', $uri = '', $data = '') {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		$service = 's3';
		$region = $this->getRegion();

		$algorithm   = 'AWS4-HMAC-SHA256';
		$amzHeaders  = array();
		$amzRequests = array();

		$amzDate = gmdate('Ymd\THis\Z');
		$amzDateStamp = gmdate('Ymd');

		// amz-date ISO8601 format? for aws request
		$amzHeaders['x-amz-date'] = $amzDate;

		// CanonicalHeaders
		foreach ($headers as $k => $v) {
			$amzHeaders[strtolower($k)] = trim($v);
		}

		foreach ($aHeaders as $k => $v) {
			$amzHeaders[strtolower($k)] = trim($v);
		}
		uksort($amzHeaders, 'strcmp');

		// payload
		$payloadHash = isset($amzHeaders['x-amz-content-sha256']) ? $amzHeaders['x-amz-content-sha256'] : hash('sha256', $data);

		// parameters
		$parameters = array();
		if (strpos($uri, '?')) {
			list($uri, $query_str) = @explode('?', $uri);
			parse_str($query_str, $parameters);
		}

		// Canonical Requests
		$amzRequests[] = $method;
		$uriQmPos = strpos($uri, '?');
		$amzRequests[] = (false === $uriQmPos ? $uri : substr($uri, 0, $uriQmPos));
		$amzRequests[] = http_build_query($parameters);

		// add headers as string to requests
		foreach ($amzHeaders as $k => $v) {
			$amzRequests[] = $k . ':' . $v;
		}

		// add a blank entry so we end up with an extra line break
		$amzRequests[] = '';

		// SignedHeaders
		$amzRequests[] = implode(';', array_keys($amzHeaders));

		// payload hash
		$amzRequests[] = $payloadHash;

		// request as string
		$amzRequestStr = implode("\n", $amzRequests);

		// CredentialScope
		$credentialScope = array();
		$credentialScope[] = $amzDateStamp;
		$credentialScope[] = $region;
		$credentialScope[] = $service;
		$credentialScope[] = 'aws4_request';

		// stringToSign
		$stringToSign = array();
		$stringToSign[] = $algorithm;
		$stringToSign[] = $amzDate;
		$stringToSign[] = implode('/', $credentialScope);
		$stringToSign[] = hash('sha256', $amzRequestStr);

		// as string
		$stringToSignStr = implode("\n", $stringToSign);

		// Make Signature
		$kSecret = 'AWS4' . $this->__secretKey;
		$kDate = hash_hmac('sha256', $amzDateStamp, $kSecret, true);
		$kRegion = hash_hmac('sha256', $region, $kDate, true);
		$kService = hash_hmac('sha256', $service, $kRegion, true);
		$kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
		$signature = hash_hmac('sha256', $stringToSignStr, $kSigning);

		$authorization = array(
			'Credential=' . $this->__accessKey . '/' . implode('/', $credentialScope),
			'SignedHeaders=' . implode(';', array_keys($amzHeaders)),
			'Signature=' . $signature,
		);
		$authorizationStr = $algorithm . ' ' . implode(',', $authorization);

		$resultHeaders = array(
			'X-AMZ-DATE' => $amzDate,
			'Authorization' => $authorizationStr
		);
		if (!isset($aHeaders['x-amz-content-sha256'])) {
			$resultHeaders['x-amz-content-sha256'] = $payloadHash;
		}

		return $resultHeaders;
	}

}

final class UpdraftPlus_S3Request {

	private $endpoint, $verb, $bucket, $uri, $resource = '', $parameters = array(),
	$amzHeaders = array(), $headers = array(
		'Host' => '', 'Date' => '', 'Content-MD5' => '', 'Content-Type' => ''
	);
	public $fp = false, $size = 0, $data = false, $response;
	
	private $s3;

	/**
	 * Constructor
	 *
	 * @param string  $verb Verb
	 * @param string  $bucket Bucket name
	 * @param string  $uri Object URI
	 * @param string  $endpoint Endpoint of storage
	 * @param boolean $use_dns_bucket_name
	 * @param object  $s3 S3 Object that calls these requests
	 *
	 * @return mixed
	 */
	function __construct($verb, $bucket = '', $uri = '', $endpoint = 's3.amazonaws.com', $use_dns_bucket_name = false, $s3 = null) {
		$this->endpoint = $endpoint;
		$this->verb = $verb;
		$this->bucket = $bucket;
		$this->uri = $uri !== '' ? '/'.str_replace('%2F', '/', rawurlencode($uri)) : '/';
		$this->s3 = $s3;

		//if ($this->bucket !== '')
		//	$this->resource = '/'.$this->bucket.$this->uri;
		//else
		//	$this->resource = $this->uri;

		if ('' !== $this->bucket) {
			if ($this->__dnsBucketName($this->bucket) || $use_dns_bucket_name) {
				$this->headers['Host'] = $this->bucket.'.'.$this->endpoint;
				$this->resource = '/'.$this->bucket.$this->uri;
			} else {
				$this->headers['Host'] = $this->endpoint;
				$this->uri = $this->uri;
				if ('' !== $this->bucket) $this->uri = '/'.$this->bucket.$this->uri;
				$this->bucket = '';
				$this->resource = $this->uri;
			}
		} else {
			$this->headers['Host'] = $this->endpoint;
			$this->resource = $this->uri;
		}

		$this->headers['Date'] = gmdate('D, d M Y H:i:s T');
		$this->response = new STDClass;
		$this->response->error = false;
		$this->response->body = null;
	}

	/**
	 * Set request parameter
	 *
	 * @param string $key Key
	 * @param string $value Value
	 *
	 * @return void
	 */
	public function setParameter($key, $value) {
		$this->parameters[$key] = $value;
	}


	/**
	 * Set request header
	 *
	 * @param string $key Key
	 * @param string $value Value
	 *
	 * @return void
	 */
	public function setHeader($key, $value) {
		$this->headers[$key] = $value;
	}


	/**
	 * Set x-amz-meta-* header
	 *
	 * @param string $key Key
	 * @param string $value Value
	 *
	 * @return void
	 */
	public function setAmzHeader($key, $value) {
		$this->amzHeaders[$key] = $value;
	}
	
	/**
	 * Get the S3 response
	 *
	 * @return object | false
	 */
	public function getResponse() {
		$query = '';
		if (sizeof($this->parameters) > 0) {
			$query = ('?' !== substr($this->uri, -1)) ? '?' : '&';
			foreach ($this->parameters as $var => $value)
				if (null == $value || '' == $value) $query .= $var.'&';
				else $query .= $var.'='.rawurlencode($value).'&';
			$query = substr($query, 0, -1);
			$this->uri .= $query;

			if (array_key_exists('acl', $this->parameters) ||
			array_key_exists('location', $this->parameters) ||
			array_key_exists('torrent', $this->parameters) ||
			array_key_exists('logging', $this->parameters) ||
			array_key_exists('partNumber', $this->parameters) ||
			array_key_exists('uploads', $this->parameters) ||
			array_key_exists('uploadId', $this->parameters))
				$this->resource .= $query;
		}
		$url = ($this->s3->useSSL ? 'https://' : 'http://') . ('' !== $this->headers['Host'] ? $this->headers['Host'] : $this->endpoint) . $this->uri;

		//var_dump('bucket: ' . $this->bucket, 'uri: ' . $this->uri, 'resource: ' . $this->resource, 'url: ' . $url);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT, 'S3/php');

		if ($this->s3->useSSL) {
			// SSL Validation can now be optional for those with broken OpenSSL installations
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->s3->useSSLValidation ? 2 : 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->s3->useSSLValidation ? 1 : 0);

			if (null !== $this->s3->sslKey) curl_setopt($curl, CURLOPT_SSLKEY, $this->s3->sslKey);
			if (null !== $this->s3->sslCert) curl_setopt($curl, CURLOPT_SSLCERT, $this->s3->sslCert);
			if (null !== $this->s3->sslCACert && file_exists($this->s3->sslCACert)) curl_setopt($curl, CURLOPT_CAINFO, $this->s3->sslCACert);
		}

		curl_setopt($curl, CURLOPT_URL, $url);

		$wp_proxy = new WP_HTTP_Proxy(); 

		if (null != $this->s3->proxy && isset($this->s3->proxy['host']) && $wp_proxy->send_through_proxy($url)) {
			curl_setopt($curl, CURLOPT_PROXY, $this->s3->proxy['host']);
			curl_setopt($curl, CURLOPT_PROXYTYPE, $this->s3->proxy['type']);
			if (!empty($this->s3->proxy['port'])) curl_setopt($curl,CURLOPT_PROXYPORT, $this->s3->proxy['port']);
			if (isset($this->s3->proxy['user'], $this->s3->proxy['pass']) && null != $this->s3->proxy['user'] && null != $this->s3->proxy['pass']) {
				curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
				curl_setopt($curl, CURLOPT_PROXYUSERPWD, sprintf('%s:%s', $this->s3->proxy['user'], $this->s3->proxy['pass']));
			}
		}

		// Headers
		$headers = array(); $amz = array();
		foreach ($this->amzHeaders as $header => $value)
			if (strlen($value) > 0) $headers[] = $header.': '.$value;
		foreach ($this->headers as $header => $value)
			if (strlen($value) > 0) $headers[] = $header.': '.$value;

		// Collect AMZ headers for signature
		foreach ($this->amzHeaders as $header => $value)
			if (strlen($value) > 0) $amz[] = strtolower($header).':'.$value;

		// AMZ headers must be sorted
		if (sizeof($amz) > 0) {
			//sort($amz);
			usort($amz, array(&$this, '__sortMetaHeadersCmp'));
			$amz = "\n".implode("\n", $amz);
		} else {
			$amz = '';
		}

		if ($this->s3->hasAuth()) {
			// Authorization string (CloudFront stringToSign should only contain a date)
			if ('cloudfront.amazonaws.com' == $this->headers['Host']) {
				$headers[] = 'Authorization: ' . $this->s3->__getSignature($this->headers['Date']);
			} else {
				if ('v2' === $this->s3->signVer) {
					$headers[] = 'Authorization: ' . $this->s3->__getSignature(
							$this->verb."\n".
							$this->headers['Content-MD5']."\n".
							$this->headers['Content-Type']."\n".
							$this->headers['Date'].$amz."\n".
							$this->resource
						);
				} else {
					$amzHeaders = $this->s3->__getSignatureV4(
						$this->amzHeaders,
						$this->headers,
						$this->verb,
						$this->uri,
						$this->data
					);
					foreach ($amzHeaders as $k => $v) {
						$headers[] = $k . ': ' . $v;
					}
				}
			}
		}

		if (false !== $this->s3->port) curl_setopt($curl, CURLOPT_PORT, $this->s3->port);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, '__responseWriteCallback'));
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, array(&$this, '__responseHeaderCallback'));
		@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		// Request types
		switch ($this->verb) {
			case 'GET': break;
			case 'PUT': case 'POST':
				if (false !== $this->fp) {
					curl_setopt($curl, CURLOPT_PUT, true);
					curl_setopt($curl, CURLOPT_INFILE, $this->fp);
					if ($this->size >= 0) {
						curl_setopt($curl, CURLOPT_INFILESIZE, $this->size);
					}
				} elseif (false !== $this->data) {
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
					curl_setopt($curl, CURLOPT_INFILESIZE, strlen($this->data));
				} else {
					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
				}
			break;
			case 'HEAD':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
				curl_setopt($curl, CURLOPT_NOBODY, true);
			break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			break;
			default: break;
		}

		// Execute, grab errors
		if (curl_exec($curl))
			$this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		else
			$this->response->error = array(
				'code' => curl_errno($curl),
				'message' => curl_error($curl),
				'resource' => $this->resource
			);

		@curl_close($curl);

		// Parse body into XML
		// The case in which there is not application/xml content-type header is to support a DreamObjects case seen, April 2018
		if (false === $this->response->error && isset($this->response->body) && ((isset($this->response->headers['type']) && false  !== strpos($this->response->headers['type'], 'application/xml')) || (!isset($this->response->headers['type']) && 0 === strpos($this->response->body, '<?xml')))) {
			$this->response->body = simplexml_load_string($this->response->body);

			// Grab S3 errors
			if (!in_array($this->response->code, array(200, 204, 206)) &&
			isset($this->response->body->Code)) {
				$this->response->error = array(
					'code' => (string)$this->response->body->Code,
				);
				$this->response->error['message'] = isset($this->response->body->Message) ? $this->response->body->Message : '';
				if (isset($this->response->body->Resource))
					$this->response->error['resource'] = (string)$this->response->body->Resource;
				unset($this->response->body);
			}
		}
		
		// Clean up file resources
		if (false !== $this->fp && is_resource($this->fp)) fclose($this->fp);

		return $this->response;
	}

	/**
	 * Sort compare for meta headers
	 *
	 * @internal Used to sort x-amz meta headers
	 *
	 * @param string $a String A
	 * @param string $b String B
	 *
	 * @return integer
	 */
	private function __sortMetaHeadersCmp($a, $b) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		$lenA = strpos($a, ':');
		$lenB = strpos($b, ':');
		$minLen = min($lenA, $lenB);
		$ncmp = strncmp($a, $b, $minLen);
		if ($lenA == $lenB) return $ncmp;
		if (0 == $ncmp) return $lenA < $lenB ? -1 : 1;
		return $ncmp;
	}

	/**
	 * CURL write callback
	 *
	 * @param resource $curl CURL resource
	 * @param string $data Data
	 *
	 * @return integer
	 */
	private function __responseWriteCallback($curl, $data) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		if (in_array($this->response->code, array(200, 206)) && false !== $this->fp)
			return fwrite($this->fp, $data);
		else
			$this->response->body = (empty($this->response->body)) ? $data : $this->response->body.$data;
		return strlen($data);
	}


	/**
	 * Check DNS conformity
	 *
	 * @param  string $bucket Bucket name
	 *
	 * @return boolean
	 */
	private function __dnsBucketName($bucket) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		# A DNS bucket name cannot have len>63
		# A DNS bucket name must have a character in other than a-z, 0-9, . -
		# The purpose of this second check is not clear - is it that there's some limitation somewhere on bucket names that match that pattern that means that the bucket must be accessed by hostname?
		if (strlen($bucket) > 63 || !preg_match("/[^a-z0-9\.-]/", $bucket)) return false;
		# A DNS bucket name cannot contain -.
		if (false !== strstr($bucket, '-.')) return false;
		# A DNS bucket name cannot contain ..
		if (false !== strstr($bucket, '..')) return false;
		# A DNS bucket name must begin with 0-9a-z
		if (!preg_match("/^[0-9a-z]/", $bucket)) return false;
		# A DNS bucket name must end with 0-9 a-z
		if (!preg_match("/[0-9a-z]$/", $bucket)) return false;
		return true;
	}

	/**
	 * CURL header callback
	 *
	 * @param resource $curl CURL resource
	 * @param string $data Data
	 * @return integer
	 */
	private function __responseHeaderCallback($curl, $data) {// phpcs:ignore PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.MethodDoubleUnderscore -- Method name "UpdraftPlus_S3Request::__responseHeaderCallback" is discouraged; PHP has reserved all method names with a double underscore prefix for future use.
		if (($strlen = strlen($data)) <= 2) return $strlen;
		if ('HTTP' == substr($data, 0, 4)) {
			$this->response->code = (int)substr($data, 9, 3);
		} else {
			$data = trim($data);
			if (false === strpos($data, ': ')) return $strlen;
			list($header, $value) = explode(': ', $data, 2);
			if ('last-modified' == strtolower($header))
				$this->response->headers['time'] = strtotime($value);
			elseif ('content-length' == strtolower($header))
				$this->response->headers['size'] = (int)$value;
			elseif ('content-type' == strtolower($header))
				$this->response->headers['type'] = $value;
			elseif ('etag' == strtolower($header))
				$this->response->headers['hash'] = '"' == $value[0] ? substr($value, 1, -1) : $value;
			elseif (preg_match('/^x-amz-meta-.*$/i', $header))
				$this->response->headers[strtolower($header)] = $value;
		}
		return $strlen;
	}

}
