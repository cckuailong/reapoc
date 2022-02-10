<?php


/**
 * Centralizes control over resource fetching.
 *
 * @since 4.7
 */
class WPRSS_Feed_Access
{

    const RESOURCE_CLASS = 'WPRSS_SimplePie_File';
    const ITEM_CLASS = 'WPRSS_SimplePie_Item';
    const D_REDIRECTS = 5;

    const SETTING_KEY_CERTIFICATE_PATH = 'certificate-path';
    const SETTING_KEY_FEED_REQUEST_USERAGENT = 'feed_request_useragent';
    const SETTING_KEY_CACHE = 'feed_cache_enabled';

    protected static $_instance;

    protected $_certificate_file_path;

	/**
	 * @since 4.7
	 * @return WPRSS_Feed_Access The singleton instance of this class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			$class_name = __CLASS__;
			self::$_instance = new $class_name;
		}

		return self::$_instance;
	}


	public function __construct() {
		$this->_construct();
	}


	/**
	 * The parameter-less constructor.
	 *
	 * @since 4.7
	 */
	protected function _construct()
        {
            $wprss = wprss();
            add_action( 'wprss_fetch_feed_before', array( $this, 'set_feed_options' ), 10, 2 );
            add_action( 'wprss_settings_array', array( $this, 'add_settings' ) );
            $wprss->on('fields', array($this, 'add_feed_source_fields'));
	}


	/**
	 * Sets the path to the certificate, which will be used by WPRSS to fetch remote content.
	 *
	 * @since 4.7
	 * @param string $path Absolute path to the certificate file.
	 * @return \WPRSS_Feed_Access This instance.
	 */
	public function set_certificate_file_path( $path ) {
		$this->_certificate_file_path = $path;
		return $this;
	}


	/**
	 * Gets the path to the certificate, which will be used by WPRSS to fetch remote content.
	 *
	 * @since 4.7
	 * @see get_certificate_path_setting()
	 * @return string Absolute path to the certificate file. By default will use the option.
	 */
	public function get_certificate_file_path() {
		if ( empty( $this->_certificate_file_path ) )
			$this->_certificate_file_path = $this->get_certificate_path_setting();

		return $this->_certificate_file_path;
	}


	/**
	 * Gets the value of the option that stores the path to the certificate file.
	 * Relative paths will be converted to absolute, as if relative to WP root.
	 *
	 * @since 4.7
	 * @return string Absolute path to the certificate file.
	 */
	public function get_certificate_path_setting() {
		$path = wprss_get_general_setting( self::SETTING_KEY_CERTIFICATE_PATH );

		if ( empty( $path ) )
			return $path;

		if ( !path_is_absolute( $path ) )
			$path = ABSPATH . $path;

		return $path;
	}

    /**
     * Return the value of the useragent setting.
     *
     * The setting key is determined by the SETTING_KEY_FEED_REQUEST_USERAGENT class constant.
     *
     * @since 4.8.2
     * @return string The value of the useragent setting.
     */
    public function get_useragent_setting()
    {
		return wprss_get_general_setting( self::SETTING_KEY_FEED_REQUEST_USERAGENT, true );
    }

    /**
     * Get the useragent string that will be sent with feed requests.
     *
     * @since 4.8.2
     *
     * @param int|null $feedSourceId The ID of the feed source, which to get the useragent for.
     *  Leave `null` to get a useragent independently from any feed source.
     *
     * @return string The useragent string that will be sent together with feed requests.
     *  If empty, the value of SIMPLEPIE_USERAGENT will be used.
     */
    public function get_useragent($feedSourceId = null)
    {
        $useragent = !is_null($feedSourceId)
                ? get_post_meta($feedSourceId, self::SETTING_KEY_FEED_REQUEST_USERAGENT, true)
                : $this->get_useragent_setting();

        if (!strlen(trim($useragent))) {
            $useragent = $this->get_useragent_setting();
        }

        $useragent = !strlen(trim($useragent))
            ? $this->getDefaultUseragent()
            : $useragent;

        wprss()->event('feed_access_useragent', array(
            'useragent'         => &$useragent,
            'feed_source_id'    => $feedSourceId
        ));

        return $useragent;
    }

    /**
     * Retrieve the useragent string that will be used by default.
     *
     * @since 4.10
     *
     * @return string The useragent string.
     */
    public function getDefaultUseragent()
    {
        return SIMPLEPIE_USERAGENT;
    }


	/**
	 * This happens immediately before feed initialization.
	 * Handles the `wprss_fetch_feed_before` action.
	 *
	 * @since 4.7
	 * @param SimplePie $feed The instance of the object that represents the feed to be fetched.
	 * @param int $feedSourceId ID of the feed source, which is accessing the feed.
         *  Leave `null` to skip setting feed source specific options.
	 * @param string $url The URL, from which the feed is going to be fetched.
	 */
	public function set_feed_options($feed, $feedSourceId = null)
        {
            $feed->set_item_class(static::ITEM_CLASS);
            $feed->set_file_class( static::RESOURCE_CLASS );
            $feed->set_useragent($this->get_useragent($feedSourceId));
            WPRSS_SimplePie_File::set_default_certificate_file_path($this->get_certificate_file_path());

            /*
             * Setting the file resource object for the feed to use.
             * Note: this object will only be used if cache is disabled for
             * the feed. This is why running {@see SimplePie::set_file_class()}
             * is still necessary. Like this, the correct file class will
             * still be used, although the file object set below will
             * have absolutely no effect on the feed retrieval process.
             */
            if (!$feed->file) {
                $feed->file = $this->create_resource_from_feed($feed);
            }
	}


	/**
	 * Implements a `wprss_settings_array` filter.
	 *
	 * @since 4.7
	 * @param array $settings The current settings array, where 1st dimension is secion code, 2nd is setting code, 3rd is setting option(s).
	 * @return array The new settings array.
	 */
	public function add_settings( $settings ) {
		$settings['advanced'][ self::SETTING_KEY_CERTIFICATE_PATH ] = array(
			'label'			=> __( 'Certificate path', 'wprss' ),
			'callback'		=> array( $this, 'render_certificate_path_setting' )
		);
        /* @since 4.8.2 */
		$settings['advanced'][ self::SETTING_KEY_FEED_REQUEST_USERAGENT ] = array(
			'label'			=> __( 'Feed request useragent', 'wprss' ),
			'callback'		=> array( $this, 'render_feed_request_useragent_setting' )
		);
        /* @since 4.14.1 */
        $settings['advanced'][ self::SETTING_KEY_CACHE ] = array(
            'label'			=> __( 'Enable feed cache', 'wprss' ),
            'callback'		=> array( $this, 'render_feed_cache_setting' )
        );

		return $settings;
	}

        /**
         * Adding feed source specific settings.
         *
	 * @since 4.10
         *
         * @param array $fields An array containing all existing fields by ID.
         */
        public function add_feed_source_fields($fields)
        {
            $wprss = wprss();

            $fields[self::SETTING_KEY_FEED_REQUEST_USERAGENT] = array(
                'id'            => self::SETTING_KEY_FEED_REQUEST_USERAGENT,
                'label'         => $wprss->__('Feed request useragent'),
                'placeholder'   => $wprss->__('Leave blank to inherit general setting')
            );

            return $fields;
        }

	/**
	 * Renders the setting field for the certificate path.
	 *
	 * @since 4.7
	 * @see wprss_admin_init
	 * @param array $field Data of this field.
	 */
	public function render_certificate_path_setting( $field ) {
        $feed_limit = wprss_get_general_setting($field['field_id']);

        printf(
            '<input type="text" id="%1$s" name="wprss_settings_general[%1$s]" value="%2$s" />',
            esc_attr($field['field_id']),
            esc_attr($feed_limit)
        );

        echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
	}

    /**
     * Renders the setting field for the feed request useragent.
     *
     * @since 4.8.2
	 * @see wprss_admin_init
	 * @param array $field Data of this field.
     */
    public function render_feed_request_useragent_setting( $field )
    {
        $value = wprss_get_general_setting($field['field_id']);

        printf(
            '<input type="text" id="%1$s" name="wprss_settings_general[%1$s]" value="%2$s" placeholder="%3$s" />',
            esc_attr($field['field_id']),
            esc_attr($value),
            __('Default', 'wprss')
        );

        echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
    }

    /**
     * Renders the setting field for the feed cache option.
     *
     * @since 4.15
     *
     * @param array $field The information for this field.
     */
    public function render_feed_cache_setting( $field )
    {
        $value = (int) wprss_get_general_setting($field['field_id']);

        printf(
            '<input name="wprss_settings_general[%s]" type="hidden" value="0" />',
            esc_attr($field['field_id'])
        );

        printf(
            '<input type="checkbox" value="1" id="%1$s" name="wprss_settings_general[%1$s]" %2$s />',
            esc_attr($field['field_id']),
            checked(1, $value, false)
        );

        echo wprss_settings_inline_help($field['field_id'], $field['tooltip']);
    }

    /**
     * Retrieve default headers that should be used for feed requests.
     *
     * Use the `wprss_feed_default_headers` filter to amend the whole return value.
     * Use the `wprss_feed_default_headers_accept` to amend the types in the "Accept" header.
     *
     * @since 4.10
     * @see array_merge_recursive_distinct()
     * @param array $additionalHeaders Optional headers to merge with the default ones.
     *  Merging is done recursively.
     * @return array An array of headers, where keys are header names, and values are header values.
     */
    public function default_headers(array $additionalHeaders = array())
    {
        $defaultHeaders = array(
            'Accept' => implode(', ', apply_filters('wprss_feed_default_headers_accept', array(
                'application/atom+xml',
                'application/rss+xmlm',
                'application/rdf+xml;q=0.9',
                'application/xml;q=0.8',
                'text/xml;q=0.8',
                'text/html;q=0.7',
                'unknown/unknown;q=0.1',
                'application/unknown;q=0.1',
                '*/*;q=0.1')))
        );

        $headers = array_merge_recursive_distinct($defaultHeaders, $additionalHeaders);
        $headers = apply_filters('wprss_feed_default_headers', $headers);

        return $headers;
    }

    /**
     * Creates a new object that is responsible for retrieving a remote resource.
     *
     * @since 4.10
     *
     * @see SimplePie_File::__construct()
     *
     * @param string $url
     * @param int $timeout
     * @param int $redirects
     * @param array $headers If null, {@link default_headers() default headers} will be used.
     * @param string $useragent
     * @param bool $force_fsockopen
     * @return \SimplePie_File
     */
    public function create_resource($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false)
    {
        if (is_null($headers)) {
            $headers = $this->default_headers();
        }

        if (!class_exists($resourceClass = static::RESOURCE_CLASS)) {
            throw new Exception(sprintf('Could not create resource: resource class "$1$s" does not exist', $resourceClass));
        }

        return new $resourceClass($url, $timeout, $redirects, $headers, $useragent, $force_fsockopen);
    }

    /**
     * Creates a new object that is responsible for retrieving a remote resource, using values from a feed.
     *
     * @since 4.10
     *
     * @see wprss_feed_create_resource()
     *
     * @param \SimplePie $feed The feed, based on which to create the resource.
     * @return \SimplePie_File
     */
    public function create_resource_from_feed(SimplePie $feed)
    {
        return $this->create_resource($feed->feed_url, $feed->timeout, static::D_REDIRECTS, null, $feed->useragent, $feed->force_fsockopen);
    }
}

// Initialize
add_action('wprss_init', function() {
    WPRSS_Feed_Access::instance();
});

class WPRSS_SimplePie_Item extends SimplePie_Item {

    public function sanitize($data, $type, $base = '')
    {
        if ($type & (SIMPLEPIE_CONSTRUCT_HTML | SIMPLEPIE_CONSTRUCT_XHTML | SIMPLEPIE_CONSTRUCT_MAYBE_HTML)) {
            return $data;
        }

        return parent::sanitize($data, $type, $base);
    }
}


/**
 * A padding layer used to give WPRSS more control over fetching of feed resources.
 * @since 4.7
 */
class WPRSS_SimplePie_File extends SimplePie_File {

	protected static $_default_certificate_file_path;
	protected $_certificate_file_path;


	/**
	 * Copied from {@see SimplePie_File#__construct()}.
	 * Adds call to {@see _before_curl_exec()}.
	 *
	 * @since 4.7
	 */
	public function __construct( $url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false ) {
		if ( class_exists( 'idna_convert' ) ) {
			$idn = new idna_convert();
			$parsed = SimplePie_Misc::parse_url( $url );
			$url = SimplePie_Misc::compress_parse_url( $parsed['scheme'], $idn->encode( $parsed['authority'] ), $parsed['path'], $parsed['query'], $parsed['fragment'] );
			wprss_log_obj('Converted IDNA URL', $url, null, WPRSS_LOG_LEVEL_SYSTEM);
		}
		$this->url = $url;
		$this->useragent = $useragent;
		if ( preg_match( '/^http(s)?:\/\//i', $url ) ) {
			if ( $useragent === null ) {
				$useragent = ini_get( 'user_agent' );
				$this->useragent = $useragent;
			}
			if ( !is_array( $headers ) ) {
				$headers = array();
			}
			if ( !$force_fsockopen && function_exists( 'curl_exec' ) ) {
				$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE | SIMPLEPIE_FILE_SOURCE_CURL;
				$fp = curl_init();
				$headers2 = array();
				foreach ( $headers as $key => $value ) {
					$headers2[] = "$key: $value";
				}
				if ( version_compare( SimplePie_Misc::get_curl_version(), '7.10.5', '>=' ) ) {
					curl_setopt( $fp, CURLOPT_ENCODING, '' );
				}
				curl_setopt( $fp, CURLOPT_URL, $url );
				curl_setopt( $fp, CURLOPT_HEADER, 1 );
				curl_setopt( $fp, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $fp, CURLOPT_TIMEOUT, $timeout );
				curl_setopt( $fp, CURLOPT_CONNECTTIMEOUT, $timeout );
				curl_setopt( $fp, CURLOPT_USERAGENT, $useragent );
				curl_setopt( $fp, CURLOPT_HTTPHEADER, $headers2 );
				if ( !ini_get( 'open_basedir' ) && !ini_get( 'safe_mode' ) && version_compare( SimplePie_Misc::get_curl_version(), '7.15.2', '>=' ) ) {
					curl_setopt( $fp, CURLOPT_FOLLOWLOCATION, 1 );
					curl_setopt( $fp, CURLOPT_MAXREDIRS, $redirects );
				}

				global $wpraNoSslVerification;
				if ($wpraNoSslVerification) {
				    curl_setopt( $fp, CURLOPT_SSL_VERIFYPEER, 0 );
                }

				$this->_before_curl_exec( $fp, $url );

				$this->headers = curl_exec( $fp );
				if ( curl_errno( $fp ) === 23 || curl_errno( $fp ) === 61 ) {
					curl_setopt( $fp, CURLOPT_ENCODING, 'none' );
					$this->headers = curl_exec( $fp );
				}
				if ( curl_errno( $fp ) ) {
					$this->error = 'cURL error ' . curl_errno( $fp ) . ': ' . curl_error( $fp );
					$this->success = false;
				} else {
					$info = curl_getinfo( $fp );
					curl_close( $fp );
					$this->headers = explode( "\r\n\r\n", $this->headers, $info['redirect_count'] + 1 );
					$this->headers = array_pop( $this->headers );
					$parser = new SimplePie_HTTP_Parser( $this->headers );
					if ( $parser->parse() ) {
						$this->headers = $parser->headers;
						$this->body = $this->_processBody($parser->body);
						$this->status_code = $parser->status_code;
						if ( (in_array( $this->status_code, array( 300, 301, 302, 303, 307 ) ) || $this->status_code > 307 && $this->status_code < 400) && isset( $this->headers['location'] ) && $this->redirects < $redirects ) {
							$this->redirects++;
							$location = SimplePie_Misc::absolutize_url( $this->headers['location'], $url );
							return $this->__construct( $location, $timeout, $redirects, $headers, $useragent, $force_fsockopen );
						}

                        $this->_afterCurlHeadersParsed($info);
					}
				}
			} else {
				$this->method = SIMPLEPIE_FILE_SOURCE_REMOTE | SIMPLEPIE_FILE_SOURCE_FSOCKOPEN;
				$url_parts = parse_url( $url );
				$socket_host = $url_parts['host'];
				if ( isset( $url_parts['scheme'] ) && strtolower( $url_parts['scheme'] ) === 'https' ) {
					$socket_host = "ssl://{$url_parts['host']}";
					$url_parts['port'] = 443;
				}
				if ( !isset( $url_parts['port'] ) ) {
					$url_parts['port'] = 80;
				}
				$fp = @fsockopen( $socket_host, $url_parts['port'], $errno, $errstr, $timeout );
				if ( !$fp ) {
					$this->error = 'fsockopen error: ' . $errstr;
					$this->success = false;
				} else {
					stream_set_timeout( $fp, $timeout );
					if ( isset( $url_parts['path'] ) ) {
						if ( isset( $url_parts['query'] ) ) {
							$get = "{$url_parts['path']}?{$url_parts['query']}";
						} else {
							$get = $url_parts['path'];
						}
					} else {
						$get = '/';
					}
					$out = "GET $get HTTP/1.1\r\n";
					$out .= "Host: {$url_parts['host']}\r\n";
					$out .= "User-Agent: $useragent\r\n";
					if ( extension_loaded( 'zlib' ) ) {
						$out .= "Accept-Encoding: x-gzip,gzip,deflate\r\n";
					}

					if ( isset( $url_parts['user'] ) && isset( $url_parts['pass'] ) ) {
						$out .= "Authorization: Basic " . base64_encode( "{$url_parts['user']}:{$url_parts['pass']}" ) . "\r\n";
					}
					foreach ( $headers as $key => $value ) {
						$out .= "$key: $value\r\n";
					}
					$out .= "Connection: Close\r\n\r\n";
					fwrite( $fp, $out );

					$info = stream_get_meta_data( $fp );

					$this->headers = '';
					while ( !$info['eof'] && !$info['timed_out'] ) {
						$this->headers .= fread( $fp, 1160 );
						$info = stream_get_meta_data( $fp );
					}
					if ( !$info['timed_out'] ) {
						$parser = new SimplePie_HTTP_Parser( $this->headers );
						if ( $parser->parse() ) {
							$this->headers = $parser->headers;
							$this->body = $this->_processBody($parser->body);
							$this->status_code = $parser->status_code;
							if ( (in_array( $this->status_code, array( 300, 301, 302, 303, 307 ) ) || $this->status_code > 307 && $this->status_code < 400) && isset( $this->headers['location'] ) && $this->redirects < $redirects ) {
								$this->redirects++;
								$location = SimplePie_Misc::absolutize_url( $this->headers['location'], $url );
								return $this->__construct( $location, $timeout, $redirects, $headers, $useragent, $force_fsockopen );
							}
							if ( isset( $this->headers['content-encoding'] ) ) {
								// Hey, we act dumb elsewhere, so let's do that here too
								switch ( strtolower( trim( $this->headers['content-encoding'], "\x09\x0A\x0D\x20" ) ) ) {
									case 'gzip':
									case 'x-gzip':
										$decoder = new SimplePie_gzdecode( $this->body );
										if ( !$decoder->parse() ) {
											$this->error = 'Unable to decode HTTP "gzip" stream';
											$this->success = false;
										} else {
											$this->body = $this->_processBody($decoder->data);
										}
										break;

									case 'deflate':
										if ( ($decompressed = gzinflate( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else if ( ($decompressed = gzuncompress( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else if ( function_exists( 'gzdecode' ) && ($decompressed = gzdecode( $this->body )) !== false ) {
											$this->body = $decompressed;
										} else {
											$this->error = 'Unable to decode HTTP "deflate" stream';
											$this->success = false;
										}
										$this->body = $this->_processBody($this->body);
										break;

									default:
										$this->error = 'Unknown content coding';
										$this->success = false;
								}
							}
						}
					} else {
						$this->error = 'fsocket timed out';
						$this->success = false;
					}
					fclose( $fp );
				}
			}
		} else {
			$this->method = SIMPLEPIE_FILE_SOURCE_LOCAL | SIMPLEPIE_FILE_SOURCE_FILE_GET_CONTENTS;
            $this->body = $this->_processBody(file_get_contents($url));
			if ( !$this->body ) {
				$this->error = 'file_get_contents could not read the file';
				$this->success = false;
			}
		}
	}

    /**
     * Processes the raw response body for an RSS feed.
     *
     * @since 4.17
     *
     * @param string $body The raw HTTP response body string.
     *
     * @return string The processed response body string.
     */
	protected function _processBody($body) {
	    return trim($body);
    }

	/**
	 * Additional preparation of the curl request.
	 * Sets the {@link CURLOPT_CAINFO http://php.net/manual/en/function.curl-setopt.php}
	 * cURL option to a value determined by {@see get_default_certificate_file_path}.
	 * If the value is empty, leaves it as is.
	 *
	 * @since 4.7
	 * @param resource $fp Pointer to a resource created by {@see curl_init()}.
	 * @param string $url The URL, to which the cURL request is being made.
	 * @return \WPRSS_SimplePie_File This instance.
	 */
	protected function _before_curl_exec( $fp, $url ) {
		if ( ($ca_path = self::get_default_certificate_file_path()) && !empty( $ca_path ) ) {
			$this->_certificate_file_path = $ca_path;
			curl_setopt( $fp, CURLOPT_CAINFO, $this->_certificate_file_path );
		}
		do_action( 'wprss_before_curl_exec', $fp );

		return $this;
	}


	/**
	 * Gets the path to the certificate, which will be used by this instance
	 * to fetch remote content.
	 *
	 * @since 4.7
	 * @return string Path to the certificate file.
	 */
	public function get_certificate_file_path() {
		return $this->_certificate_file_path;
	}


	/**
	 * Gets the path to the certificate file, which will be used by future
	 * instances of this class.
	 *
	 * @since 4.7
	 * @return string Path to the certificate file.
	 */
	public static function get_default_certificate_file_path() {
		return self::$_default_certificate_file_path;
	}


	/**
	 * Sets the path to the certificate file.
	 * This path will be used by future instances of this class.
	 *
	 * @since 4.7
	 * @param string $path The path to the certificate file.
	 */
	public static function set_default_certificate_file_path( $path ) {
		self::$_default_certificate_file_path = $path;
	}

    /**
     * Called right after a cURL request returns, and headers are parsed.
     *
     * This method will not be called if a cURL error is encountered.
     *
     * @param $curlInfo Result of a call to {@see curl_getinfo()} on the cURL resource.
     * @since 4.8.2
     */
    protected function _afterCurlHeadersParsed($curlInfo)
    {
        $bodyMaxLength = 150;
        $code = $this->status_code;
        $body = $this->body;
        $bodyLength = strlen($body);
        $bodyOutput = strlen($body) < $bodyMaxLength
            ? $body
            : substr($body, 0, $bodyMaxLength);
        $outputLength = strlen($bodyOutput);
        $error = implode("\n", array(
            'The resource could not be retrieved because of a %1$s error with code %2$d',
            'Server returned %3$d characters' . ($outputLength < $bodyMaxLength ? '' : ', of which ' . $outputLength . ' are below') . ':',
            '%4$s'
        ));

        if ($code >= 400 && $code < 500 ) { // Client error
            $this->error = sprintf($error, 'client', $code, $bodyLength, $bodyOutput);
            $this->success = false;
        }
        if ($code >= 500 && $code < 600 ) { // Server error
            $this->error = sprintf($error, 'server', $code, $bodyLength, $bodyOutput);
            $this->success = false;
        }
    }
}
