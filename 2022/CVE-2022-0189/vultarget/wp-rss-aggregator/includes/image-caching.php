<?php

/**
 * @since 4.6.10
 */
class WPRSS_Image_Cache {

	protected static $_instance;

	protected $_download_request_timeout = 300;
	/** @var string See {@see get_temp_dir() } */
	protected $_temp_dir; // System temp dir + 'remote-image-cache'
	protected $_current_time;
	protected $_ttl = 30; // Time to live, in seconds
        protected $_cache_orig_filename_length = 25; // How much of the original filename to preserve in the cache filename, in addition to the hash

	protected $_image_class_name = 'WPRSS_Image_Cache_Image';
	protected $_images = array();

	/**
	 * @since 4.6.10
	 */
	public function __construct() {
		$this->_construct();
	}

	protected function _construct() {

	}

	/**
	 * Retrieve the singleton instance of this class.
	 *
	 * @since 4.7.3
	 * @return WPRSS_Image_Cache
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self;

		return self::$_instance;
	}


	/**
	 * @since 4.6.10
	 * @param string $class_name The name of the class, instances of which will represent image files.
	 * @return \WPRSS_Image_Cache This instance.
	 */
	public function set_image_class_name( $class_name ) {
		$this->_image_class_name = $class_name;
		return $this;
	}


	/**
	 * @since 4.6.10
	 * @return string The name of the class, instances of which represent image files.
	 */
	public function get_image_class_name() {
		return trim($this->_image_class_name);
	}


	/**
	 * @since 4.6.10
	 * @param int $timeout The number of seconds, for which to wait until
	 *	the remote resource can be considered timed out.
	 * @return \WPRSS_Image_Cache This instance.
	 */
	public function set_download_request_timeout( $timeout ) {
		$this->_download_request_timeout = intval( $timeout );
		return $this;
	}


	/**
	 * @since 4.6.10
	 * @return int The number of seconds, for which the request will wait until
	 *	the remote resource can be considered timed out.
	 */
	public function get_download_request_timeout() {
		return $this->_download_request_timeout;
	}


	/**
	 * Get the unix timestamp, which represents the current moment in time.
	 *
	 * The cache expiration calculations will use this time to determine
	 * whether or not the cache for each resource is expired.
	 *
	 * @since 4.7.3
	 * @return int The unix timestamp, representing the current moment in time.
	 */
	public function get_current_time() {
		if ( !is_null( $this->_current_time ) )
			return $this->_current_time;

		return time();
	}


	/**
	 * Set the unix timestamp, which represents the current moment in time.
	 *
	 * The cache expiration calculations will use this time to determine
	 * whether or not the cache for each resource is expired.
	 *
	 * Use this to determine cache expiration for a moment in time other than now.
	 *
	 * @since 4.7.3
	 * @see normalize_time()
	 * @param int|null $time The unix timestamp, representing the current moment in time. If null, the current timestamp will be used.
	 * @return WPRSS_Image_Cache This instance.
	 */
	public function set_current_time( $time = null ) {
		if ( is_null( $time ) )
			$time = time();

		$time = $this->normalize_time( $time );

		if ( is_int( $time ) || is_object( $time ) )
			$this->_current_time = $time;

		return $this;
	}


	/**
	 * Get the TTL (Time To Live) of cache files.
	 *
	 * This value will be used by default by all instances of cache files.
	 * If a file is older than this, it will be considered to be expired.
	 *
	 * @since 4.7.3
	 * @see set_ttl()
	 * @return int The number of seconds that represents the cache lifetime.
	 */
	public function get_ttl() {
		return $this->_ttl;
	}


	/**
	 * Set the TTL (Time To Live) of cache files.
	 *
	 * This value will be used by default by all instances of cache files.
	 * If a file is older than this, it will be considered to be expired.
	 *
	 * @since 4.7.3
	 * @see get_ttl()
	 * @see normalize_time()
	 * @param int|str $time A representation of the amount of seconds, for which the cache is considered valid.
	 * @return \WPRSS_Image_Cache
	 */
	public function set_ttl( $time ) {
		$time = $this->normalize_time( $time );
		$this->_ttl = $time;

		return $this;
	}


        /**
         * Get the length of the original filename to preserve in cache.
         *
         * This is in addition to the hash. The cache filename will have the
         * following format:
         * [trimmed-orig-filename]-[hash].[orig-extension]
         * The return value determine the length of the `trimmed-orig-filename`
         * segment.
         *
         * @since 4.10
         *
         * @return int The length of the original filename to preserve.
         */
        public function get_cache_orig_filename_length() {
            return $this->_cache_orig_filename_length;
        }


        /**
         * Set the length of the original filename that will be preserved in cache.
         *
         * @see get_cache_orig_filename_length()
         *
         * @param int $length The length of the original filename preserved.
         *
         * @return \WPRSS_Image_Cache
         */
        public function set_cache_orig_filename_length($length) {
            $this->_cache_orig_filename_length = intval($length);

            return $this;
        }


	/**
	 * Converts one of many representations of time into a Unix timestamp.
	 *
	 * Supports: An integer Unix timestamp, a string Unix timestamp, a value
	 * that ca be cast to integer, or a value that can be read by {@see strtotime}.
	 *
	 * @since 4.7.3
	 * @param int|string $time A representation of time.
	 * @return int The Unix timestamp that represents a moment in time.
	 */
	public function normalize_time( $time ) {
		if ( is_string( $time ) )
			$time = is_numeric( $time )
				? intval( $time )
				: (int) strtotime( $time );

		return $time;
	}


	/**
	 * If given a cache file, a URL or a path, converts it into a path.
	 *
	 * @since 4.7.3
	 * @see WPRSS_Image_Cache_Image::get_current_path()
	 * @see is_valid_url()
	 * @see get_unique_filename()
	 * @param WPRSS_Image_Cache_Image|string $file An instance of a cache file, a URL, or path.
	 * @return string A relative path to a cache file.
	 */
	public function normalize_path( $file ) {
		if ( $file instanceof WPRSS_Image_Cache_Image )
			$file = $file->get_current_path();

		if ( $this->is_valid_url( $file ) )
			$file = $this->get_unique_filename( $file );

		return $file;
	}


	/**
	 * Create and retrieve an new instance of a cache file for the specified URL.
	 *
	 * @since 4.6.10
	 * @param string $url
	 * @return \WPRSS_Image_Cache_Image The instance of a new cache file.
	 * @throws Exception If class invalid, or not found
	 */
	public function get_new_file( $url = null ) {
		$error_caption = 'Could not create new cache image';
		$class_name = $this->get_image_class_name();
		if ( empty( $class_name ) ) throw new Exception( sprintf( '%1$s: class name must not be empty' ) );
		if ( !class_exists( $class_name ) ) throw new Exception( sprintf( '%1$s: class "%2$s" does not exist', $class_name ) );

		$image = new $class_name();
		$this->_prepare_image( $image );
		/* @var $image WPRSS_Image_Cache_Image */
		if ( !is_null( $url ) ) $image->set_url( $url );

		return $image;
	}


	/**
	 * Get a new instance of an image cache image, which points to the specified path.
	 *
	 * @param WPRSS_Image_Cache_Image|string $file An image cache instance, URL or path.
	 * @param boolean $must_exist Whether or not the image cache file must exist.
	 * @return WPRSS_Image_Cache_Image|null The new image cache instance if successful;
	 *  null if $must_exist is true, and the image cache file does not exist.
	 */
	public function get_new_file_from_path( $file, $must_exist = false ) {
		$path = $this->normalize_path( $file );

		if ( $must_exist && !$this->check_file_exists( $path ) )
			return null;

		$file = $this->get_new_file();
		$file->set_path( $path );

		return $file;
	}


	/**
	 * @since 4.6.10
	 * @param WPRSS_Image_Cache_Image $image A prepared instance of an image file.
	 */
	protected function _prepare_image( $image ) {
		$image->set_download_request_timeout( $this->get_download_request_timeout() );
		$image->set_ttl( $this->get_ttl() );
	}


	/**
	 * Get all the images that are managed by this cache instance,
	 * or just one instance that corresponds to a resource identified by the
	 * specified URL.
	 *
	 * If no instance exists yet, it will be created.
	 *
	 * @since 4.6.10
	 * @param string|null $url
	 * @return array|WPRSS_Image_Cache_Image
	 */
	public function get_images( $url = null ) {
		if ( is_null( $url ) ) return $this->_images;

		// Gotta cache one
		if ( !isset( $this->_images[ $url ] ) ) {
			$image = $this->_get_file( $url );
			$this->_images[ $url ] = $image;
		}

		return $this->_images[ $url ];
	}


	/**
	 * Delete the cache files for all resources that are managed by this instance.
	 *
	 * @since 4.6.10
	 * @return \WPRSS_Image_Cache This instance
	 */
	public function purge( $expired_only = true, $managed_only = true ) {
		$image_class_name = $this->get_image_class_name();
		$deleted_count = 0;
		foreach( $this->get_images() as $_url => $_image ) {
			/* @var $_image WPRSS_Image_Cache_Image */
			if ( is_a( $_image, $image_class_name ) ) {
				$is_delete = $expired_only
						? $_image->check_expired()
						: true;

				if ( $is_delete )
					$deleted_count += (int) $_image->delete();
			}
		}

		$this->_images = array();
		return $deleted_count;
	}


	/**
	 * @since 4.6.10
	 * @param string $url
	 * @return WPRSS_Image_Cache_Image
	 */
	protected function _get_file( $url ) {
		$image = $this->get_new_file( $url );
		$image->retrieve();

		return $image;
	}


	/**
	 * Checks whether or not the specified image exists.
	 *
	 * @since 4.7.3
	 * @param WPRSS_Image_Cache_Image|string $file An image instance, URL or file path.
	 * @return bool Whether or not the file of the specified image exists locally, and is readable.
	 */
	public function check_file_exists( $file ) {
		$file = $this->normalize_path( $file );
		$file = $this->get_tmp_dir( $file );
		return is_readable( $file );
	}


	/**
	 * Checks whether the file for a resource represented by the given
	 * file instance, URL or path has expired.
	 *
	 * If the file for the resource does not exist, it is considered to be expired.
	 *
	 * @since 4.7.3
	 * @see get_current_time()
	 * @see check_image_exists()
	 * @see get_expiration_time()
	 * @param WPRSS_Image_Cache_Image|string $image An image instance, URL or file path.
	 * @return bool Whether or not the cache of the specified image has expired.
	 */
	public function check_image_expired( $image ) {
		if ( !$this->check_file_exists( $image ) )
			return true;
		return $this->get_current_time() >= $this->get_expiration_time( $image );
	}


	/**
	 * Retrieve the time when the cache file for the specified resource will expire.
	 *
	 * @since 4.7.3
	 * @see get_ttl()
	 * @see get_file_modification_time()
	 * @param WPRSS_Image_Cache_Image|string $image Resource URL, cache file instance or path.
	 * @return int The Unix timestamp representing the moment in time when the
	 *  cache for the specified resource will expire.
	 */
	public function get_expiration_time( $image ) {
		$mod_time = $this->get_image_modification_time( $image );

		$ttl = $image instanceof WPRSS_Image_Cache_Image
			? $image->get_ttl()
			: $this->get_ttl();

		return (int) $mod_time + (int) $ttl;
	}


	/**
	 * Checks whether the specified URL is valid.
	 *
	 * Valid URLs must start with a protocol, even a relative one.
	 *
	 * @since 4.7.3
	 * @see wprss_validate_url()
	 * @param string $url A URL to validate.
	 * @return bool True if the specified string is a valud URL; false otherwize.
	 */
	public function is_valid_url( $url ) {
		$url = trim( $url );
		$protocol_regex = '^([a-z][\w-]+:)?//';
		return !is_object( $url ) && preg_match( sprintf( '!%1$s!', $protocol_regex ), $url ) && wprss_validate_url( $url );
	}


	/**
	 * Gets the modification time of the cache file for the specified resource.
	 *
	 * @since 4.7.3
	 * @todo Maybe fix in accordance with {@link http://php.net/manual/en/function.filemtime.php#100692 this bug}.
	 * @see normalize_path()
	 * @see get_tmp_dir()
	 * @see filemtime()
	 * @param WPRSS_Image_Cache_Image|string $image Resource URL, cache file instance or path.
	 * @return int|null The Unix timestamp representing the time of file modification. Null if cannot be retrieved.
	 */
	public function get_image_modification_time( $image ) {
		$path = $this->normalize_path( $image );
		$path = $this->get_tmp_dir( $path );
		$mod_time = @filemtime( $path );

		return $mod_time
			? intval( $mod_time )
			: null;
	}


	/**
	 * Get the path to the root directory of cache files.
	 *
	 * Optionally, another path can be appended.
	 *
	 * @since 4.7.3
	 * @see trailingslashit()
	 * @see get_temp_dir()
	 * @param string|null $path If specified, will be appended to the resulting path.
	 * @return string A path to the temporary directory, or a file in it. If the primer, it will have a trailing slash.
	 */
	public function get_tmp_dir( $path = null ) {
		if ( is_null( $this->_temp_dir ) )
			$this->_temp_dir = trailingslashit( get_temp_dir() ) . 'remote-image-cache';

		$temp_dir = apply_filters( 'wprss_image_cache_temp_dir', $this->_temp_dir );
		return ( is_null( $path ) )
			? $temp_dir
			: trailingslashit( $temp_dir ) . $path;
	}


	/**
	 * Downloads a resource identified by the parameters.
	 *
	 * If only the first parameter is passed, and it is an instance of a cache
	 * file, downloads the resource described by it.
	 * If it is a URL, the request timeout and target path will be computed by this instance.
	 * Otherwise, they will be overridden by the specified values, respectively.
	 *
	 * If the 'content-md5' header is present in the responce, MD5 checksum
	 * verification will take place.
	 *
	 * @since 4.7.3
	 * @see get_download_request_timeout()
	 * @see WPRSS_Image_Cache_Image::get_download_request_timeout()
	 * @see get_unique_filename()
	 * @see WPRSS_Image_Cache_Image::get_current_path()
	 * @see get_tmp_dir()
	 * @see wp_mkdir_p()
	 * @see wpra_safe_remote_get()
	 * @see verify_file_md5()
	 * @param WPRSS_Image_Cache_Image|string $image An instance of a cache file, or the URL to download.
	 * @param int|null $request_timeout The timeout for the download request.
	 * @param string|null $target_path The relative path to the target file.
	 * @return string| The absolute local path to the downloaded file,
	 *  or false if checksum verification failed.
	 * @throws Exception If the URL is invalid, or the destination path is not writable,
	 *  or the file download library cannot be read, or any other error happens during download.
	 */
	public function download_image( $image, $request_timeout = null, $target_path = null ) {
		if ( $image instanceof WPRSS_Image_Cache_Image ) {
			$url = $image->get_url();
			$timeout = $image->get_download_request_timeout();
			$path = $image->get_current_path();
		}
		else {
			$url = $image;
			$timeout = $this->get_download_request_timeout();
			$path = $this->get_unique_filename( $url );
		}

		if ( !$url ) {
			throw new Exception( sprintf( __( 'Invalid URL provided: "%1$s"' ), $url ) );
		}

		// Since image URLs may be found from "src" attributes of <img> tags, HTML entities may need to be decoded
		$url = html_entity_decode($url);

		if ( !is_null( $target_path ) ) {
			$path = $target_path;
		}

		if ( !is_null( $request_timeout ) ) {
			$timeout = $request_timeout;
		}

		// Absolute path to the cache file
		$tmpfname = $image instanceof WPRSS_Image_Cache_Image
			? $image->get_tmp_dir( $path )
			: $this->get_tmp_dir( $path );

		$this->check_is_image($tmpfname, $url);

		// WARNING: The file is not automatically deleted, The script must unlink() the file.
		$dirname = dirname( $tmpfname );
		if ( !wp_mkdir_p( $dirname ) ) {
			throw new Exception(  sprintf( __( 'Could not create directory: "%1$s". Filename: "%2$s"' ), $dirname, $tmpfname ) );
        }

		// Getting file download lib
		$file_lib_path = ABSPATH . 'wp-admin/includes/file.php';
		if ( !is_readable( $file_lib_path ) ) {
			throw new Exception( sprintf( __( 'The file library cannot be read from %1$s' ), $file_lib_path ) );
        }

		require_once( $file_lib_path );

		// Retrieving the remote resource
		$response = wpra_remote_get(
			$url,
			array(
				'timeout' => $timeout,
				'stream' => true,
				'filename' => $tmpfname,
			)
		);

		// Could not retrieve
		if ( is_wp_error( $response ) ) {
			@unlink( $tmpfname );
			throw new Exception( $response->get_error_message() );
		}

		// Retrieved, but remote server served error instead of resource
		if ( 200 != wp_remote_retrieve_response_code( $response ) ){
			@unlink( $tmpfname );
			throw new Exception( trim( wp_remote_retrieve_response_message( $response ) ) );
		}

		// Checksum verification
		$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
		if ( $content_md5 ) {
			$md5_check = verify_file_md5( $tmpfname, $content_md5 );
			if ( is_wp_error( $md5_check ) ) {
				unlink( $tmpfname );
				return $md5_check;
			}
		}

		return $tmpfname;
	}

    /**
     * Checks if a remote resource is an image.
     *
     * This method will first check the file type of the locally downloaded copy.
     * If that fails, the method will attempt to fetch the MIME type from the original remote file.
     *
     * @since 4.14
     *
     * @param string $path The path to the local file.
     * @param string $url The URL to the remote file.
     *
     * @return bool
     */
	public function check_is_image( $path, $url )
    {
        // Determine file type (ext and mime/type)
        $url_type = wp_check_filetype($path);

        // If the wp_check_filetype function fails to determine the MIME type
        if (empty($url_type['type'])) {
            $url_type = wpra_check_file_type($path, $url);
        }

        $mime_type = $url_type['type'];
        $mime_parts = explode('/', $mime_type);

        if (count($mime_parts) < 1) {
            return false;
        }

        if ($mime_parts[0] !== 'image') {
            return false;
        }

        return true;
    }

	/**
	 * Uses one of the registered hashing functions to hash the given value.
	 *
	 * The default functions that will be tried are {@see sha1()} and {@see md5()}
	 * in that order. More can be added using the `wprss_image_cache_hash_functions`
	 * filter.
	 *
	 * @since 4.7.3
	 * @param mixed $value The value to hash.
	 * @param null|mixed $default The value to return if none of the registered caching functions exist.
	 * @return string The hash string.
	 */
	public function hash( $value, $default = null ) {
		$hash_funcs = apply_filters( 'wprss_image_cache_hash_functions', array( 'sha1', 'md5' ) );
		foreach ( $hash_funcs as $_idx => $_func )
			if ( is_callable( $_func ) )
				return call_user_func_array ( $_func, array( $value ) );

		return $default;
	}


	/**
	 * Get a local relative path to for a remote resource, based on it's URL.
	 *
	 * @since 4.7.3
	 * @see generate_unique_file_name()
	 * @param WPRSS_Image_Cache_Image|string $url An instance of a cache file, or a URL of a resource.
	 * @return string|null A relative path to a cache file. If URL is empty, null is returned.
	 */
	public function get_unique_filename( $url ) {
		if ( $url instanceof WPRSS_Image_Cache_Image )
			$url = $url->get_url();

		return $this->generate_unique_file_name( $url );
	}


	/**
	 * Get a local relative path to for a remote URL.
	 *
	 * If the resource is identified by a URL with a relative protocol,
	 * the protocol will be completely ignored.
	 *
	 * @since 4.7.3
	 * @param string $url A remote resource URL.
	 * @return string A local relative path to the file for the remote resource.
	 *  The path may contain several folders, and will be of format:
	 *  [domain inc. subdomain][path to resource][resource name without extension]-[hash of the whole URL][optionally the extension]
	 */
	public function generate_unique_file_name( $url ) {
		if ( !strlen( $url ) )
			return null;

		// In case the URL is specified with a relative protocol
		$url = ltrim( $url, '/' );

		// Validate and extract extension from URL
        $pattern =
              '[^\?#]+?' // Anything that's not a '?' or '#', which denote the query string and fragment respectively
            . '(?:' // Non-matching group, just for quantifier
                . '\.' // Literal period
                . '(jpg|JPG|jpeg|JPEG|jpe|JPE|gif|GIF|png|PNG)' // The actual extension; this is what we're after
            . ')?' // The extension may not appear at all
            . '(?:#|\?|$)' // End our search with query or fragment
        ;
		preg_match( sprintf( '!%1$s!', $pattern ), $url, $ext_matches );
		$extension = isset( $ext_matches[1] ) ? $ext_matches[1] : null;
        // Fragment/query delimiters are included in the whole match
		$url_filename = basename( urldecode( trim( $ext_matches[0], '#?' ) ) );

		// Get the path to the image, without the domain. ex. /news/132456/image
		$path_matches = array();
		if ( !preg_match_all( '![^:]+://([^/]+)/([^\?]+)!', $url, $path_matches ) )
			return null;

		$path = isset( $path_matches[2][0] ) ? $path_matches[2][0] : null;
		$domain = isset( $path_matches[1][0] ) ? $path_matches[1][0] : null;

		$base_filename = $extension
			? basename( $url_filename, '.' . $extension )
			: basename( $url_filename );

		$hash = self::hash( $url );

		$path = trailingslashit( substr( $path, 0, strlen( $path ) - strlen( $url_filename ) ) );
                if ( $orig_path_length = $this->get_cache_orig_filename_length() ) {
                    $base_filename = substr( $base_filename, 0, $orig_path_length );
                }

		$unique_filename = trailingslashit( $domain ) .
                        ($orig_path_length
                            ? "$base_filename-"
                            : '') .
                        $hash .
                        (is_null( $extension )
                            ? ''
                            : ".$extension");

		return $unique_filename;
	}
}


/**
 * @since 4.6.10
 */
class WPRSS_Image_Cache_Image {

	protected $_url;
	protected $_path;
	protected $_tmp_path;
	protected $_tmp_dir;
	protected $_local_path;
	protected $_unique_name;
	protected $_size;
	protected $_download_request_timeout;
	protected $_is_attempted;
	protected $_is_fall_back_to_unsecure;
	protected $_ttl;
	protected $_cache;
	protected $_is_cached;


	/**
	 * @since 4.6.10
	 * @param string|null $data
	 */
	public function __construct( $data = null ) {
		$this->reset();

		if ( is_string( $data ) && !empty( $data ) )
			$this->_set_url( $data );
	}


	/**
	 * Brings all properties of this instance to their original values,
	 * making it re-usable.
	 *
	 * @since 4.6.10
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	public function reset() {
		$this->_url = null;
		$this->_path = null;
		$this->_tmp_path = null;
		$this->_tmp_dir = null;
		$this->_local_path = null;
		$this->_unique_name = null;
		$this->_size = null;
		$this->_download_request_timeout = 300;
		$this->_is_attempted = false;
		$this->_is_fall_back_to_unsecure = true;
		$this->_ttl;
		$this->_cache = null;
		$this->_is_cached = null;

		return $this;
	}


	/**
	 * @since 4.6.10
	 * @param string $url
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	protected function _set_url( $url ) {
		$this->_url = $url;
		return $this;
	}


	/**
	 * Sets the URL of the remote resource, which this instance represents
	 * the cache of.
	 *
	 * @since 4.6.10
	 * @param string $url
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	public function set_url( $url ) {
		$this->reset();
		$this->_set_url($url);

		return $this;
	}


	/**
	 * Retrieves the URL of the remove resource, which this instance represents
	 * the cache of.
	 *
	 * @since 4.6.10
	 * @return string The URL.
	 */
	public function get_url() {
		return $this->_url;
	}


	/**
	 * Whether or not this instance has a URL assigned.
	 *
	 * @since 4.6.10
	 * @return boolean True if this instance has a URL assigned; false otherwise.
	 */
	public function has_url() {
		return isset( $this->_url );
	}


	public function set_path( $path ) {
		$this->_set_path( $path );
		return $this;
	}


	/**
	 * @since 4.6.10
	 * @param string $path
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	protected function _set_path( $path ) {
		$this->_path = $path;
		return $this;
	}


	/**
	 * Get the path to the cache file.
	 *
	 * The path is relative to the root of the cache directory.
	 * It will only be set if the cache file is not expired, or has been
	 * successfully downloaded.
	 *
	 * @since 4.6.10
	 * @return string Relative path to the cache file.
	 */
	public function get_path() {
		return $this->_path;
	}


	/**
	 * Whether or not this instance has a local path assigned.
	 *
	 * @see get_path().
	 * @since 4.6.10
	 * @return boolean
	 */
	public function has_path() {
		return isset( $this->_path );
	}


	/**
	 * Get the local absolute path to the cache file.
	 *
	 * If local path has not yet been set, e.g. before the download of an
	 * expired file, this will return the absolute path to where the file
	 * would be after download.
	 *
	 * If the file is retrieved from cache, no local path is set explicitly,
	 * but this method will retrieve it's location regardless.
	 *
	 * @see get_current_path()
	 * @see get_tmp_dir()
	 * @see has_local_path()
	 * @since 4.7.3
	 * @return string Absolute path to the cache file.
	 */
	public function get_local_path() {
		if ( !is_null( $this->_local_path ) )
			return $this->_local_path;

		return $this->get_tmp_dir( $this->get_current_path() );
	}


	/**
	 * @since 4.7.3
	 * @param string $path The absolute path to the cache file.
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	protected function _set_local_path( $path ) {
		$this->_local_path = $path;
		return $this;
	}


	/**
	 * Whether or not the local path has been set.
	 *
	 * This usually happens after the successful download of an expired file.
	 *
	 * @see get_local_path()
	 * @since 4.7.3
	 * @return bool True if the local absolute path has been set; false otherwise.
	 */
	public function has_local_path() {
		return !is_null( $this->_local_path );
	}


	/**
	 * Whether or not the file was retrieved from cache, if attempted to retrieve.
	 * Before retrieval attempt, indicates whether or not the file cache has expired.
	 *
	 * @see is_attempted()
	 * @see check_expired()
	 * @since 4.7.3
	 * @return bool True if file was retrieved or retrievable from cache; false otherwise.
	 */
	public function is_cached() {
		return $this->is_attempted()
			? $this->_is_cached()
			: !$this->check_expired();
	}


	/**
	 *
	 * @since 4.7.3
	 * @param null|bool $is_cached If not null, will set whether or not the resource is cached.
	 *  Otherwise, will retrieve that value
	 * @return \WPRSS_Image_Cache_Image|bool This instance, if setting, or the value if retrieving.
	 */
	protected function _is_cached( $is_cached = null ) {
		if ( is_null( $is_cached ) )
			return $this->_is_cached;

		$this->_is_cached = (bool) $is_cached;
		return $this;
	}


	/**
	 * Get an instance of this image's cache controller, if set.
	 * Otherwise, get the singleton instance of {@see WPRSS_Image_Cache}.
	 *
	 * @since 4.7.3
	 * @return WPRSS_Image_Cache The instance of this image's cache controller.
	 */
	public function get_cache() {
		if ( is_null( $this->_cache ) )
			return WPRSS_Image_Cache::instance();

		return $this->_cache;
	}


	/**
	 * Set the temporary path to this image's file.
	 *
	 * @since 4.7.3
	 * @param string $path
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	protected function _set_tmp_path( $path = null ) {
		if ( is_null( $path ) )
			$path = $this->get_tmp_path();

		$this->_tmp_path = $path;
		return $this;
	}


	/**
	 * Retrieve the temporary path to this image's file, which is used for
	 * working with it. This may not be the final path.
	 *
	 * @see get_unique_name()
	 * @since 4.7.3
	 * @return string The path.
	 */
	public function get_tmp_path() {
		if ( !is_null( $this->_tmp_path ) )
			return $this->_tmp_path;

		return $this->get_unique_name();
	}


	/**
	 * The current path to the image's file.
	 * Used from outside the class for working with this image's file
	 * while it is not yet ready.
	 *
	 * @see get_path()
	 * @see get_tmp_path()
	 * @since 4.7.3
	 * @return string The path.
	 */
	public function get_current_path() {
		return !is_null( $path = $this->get_path() )
			? $path
			: $this->get_tmp_path();
	}


	/**
	 * Sets the download request timeout.
	 *
	 * @see get_download_request_timeout()
	 * @since 4.6.10
	 * @param int $timeout
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	public function set_download_request_timeout( $timeout ) {
		$this->_download_request_timeout = intval( $timeout );
		return $this;
	}


	/**
	 * Gets the download request timeout.
	 *
	 * This is the maximal number of seconds, for which the request will
	 * wait before terminating with a timeout.
	 *
	 * @see set_download_request_timeout()
	 * @since 4.6.10
	 * @return int
	 */
	public function get_download_request_timeout() {
		return $this->_download_request_timeout;
	}


	/**
	 * @since 4.6.10
	 * @param boolean|null $is_attempted
	 * @return \WPRSS_Image_Cache_Image|boolean Whether was attempted, or this instance.
	 */
	protected function _is_attempted( $is_attempted = null ) {
		if ( is_null( $is_attempted ) )
			return (bool)$this->_is_attempted;

		$this->_is_attempted = (bool) $is_attempted;
		return $this;
	}


	/**
	 * Whether or not the retrieval of the corresponding resource, e.g. file
	 * download, has been attempted.
	 *
	 * @see download()
	 * @since 4.6.10
	 * @return boolean True if retrieval of this resource was attempted; false otherwise.
	 */
	public function is_attempted() {
		return $this->_is_attempted();
	}


	/**
	 * Gets or sets whether this instance should fall back to an unsecure connection,
	 * e.g. try the HTTP protocol, if retrieval of the resource over the secure protocol,
	 * e.g. HTTPS, fails.
	 *
	 * @see download()
	 * @since 4.6.10
	 * @param boolean|null $is_fall_back
	 * @return \WPRSS_Image_Cache_Image|boolean Whether will fall back to unsecure, or this instance.
	 */
	public function is_fall_back_to_unsecure( $is_fall_back = null ) {
		if ( is_null( $is_fall_back ) )
			return (bool) $this->_is_fall_back_to_unsecure;

		$this->_is_fall_back_to_unsecure = (bool) $is_fall_back;
		return $this;
	}


	/**
	 * Retrieves the TTL (Time To Live) for the corresponding resource's cache.
	 *
	 * Falls back to the cache controller's value, if not set.
	 *
	 * @see WPRSS_Image_Cache::get_ttl()
	 * @see get_ttl()
	 * @since 4.7.3
	 * @return int The amount of seconds, for which the cache file may be considered valid.
	 */
	public function get_ttl() {
		return !is_null( $this->_ttl )
			? $this->_ttl
			: $this->get_cache()->get_ttl();
	}


	/**
	 * Sets the TTL (Time To Live) for the corresponding resource's cache.
	 *
	 * @see get_ttl()
	 * @since 4.7.3
	 * @param int $time The amount of seconds, for which the cache file should be considered valid.
	 */
	public function set_ttl( $time ) {
		$this->_ttl = $this->normalize_time( $time );
	}


	/**
	 * Normalizes a time value.
	 *
	 * This is done by converting one of a variety of representations of time
	 * into a Unix timestamp.
	 *
	 * @see WPRSS_Image_Cache::normalize_time()
	 * @since 4.7.3
	 * @param string|int $time A representation of a moment in time.
	 * @return int A timestamp.
	 */
	public function normalize_time( $time ) {
		return $this->get_cache()->normalize_time( $time );
	}


	/**
	 * Retrieves the path to the directory, where the cache file is or will be stored.
	 *
	 * If not set explicitly, falls back to the cache controller's path.
	 * Optionally, can be appended with another path.
	 *
	 * @see WPRSS_Image_Cache::get_tmp_dir()
	 * @since 4.7.3
	 * @param null|string $path A path to append to the cache directory path.
	 * @return string The path to the cache directory, optionally appended with the specified path.
	 *  It will not contain the trailing directory separator, but if appended with another path, the separator will be put in between them.
	 */
	public function get_tmp_dir( $path = null ) {
		if ( !is_null( $this->_tmp_dir ) )
			return is_null( $path )
				? $this->_tmp_dir
				: trailingslashit( $this->_tmp_dir ) . $path;

		return $this->get_cache()->get_tmp_dir( $path );
	}


	/**
	 * Retrieves the resource identified by this instance's URL.
	 *
	 * If the resource is cached, and the cache is not expired, the resource
	 * will be retrieved from cache.
	 *
	 * @see download()
	 * @since 4.7.3
	 * @return string The path to the resource's cache file.
	 */
	public function retrieve() {
		// Prepare image
		$this->_is_attempted( true )
				->_set_path( null )
				->_set_local_path( null );
		$relative_path = $this->get_current_path();

		// Cache still valid
		if ( !$this->check_expired() ) {
			$this->_set_path ( $relative_path )
					->_is_cached( true );
			return $this->get_local_path();
		}

		$local_path = $this->_retrieve();
		$this->_set_path( $relative_path );
		$this->_set_local_path( $local_path );
		$this->_is_cached( false );

		return $this->get_local_path();
	}


	/**
	 * An internal method that does just the actual retrieval of the resource.
	 *
	 * @since 4.7.3
	 * @return string The absolute local path to the retrieved file.
	 * @throws Exception If the file cannot be retrieved, due to read or write errors of some kind.
	 */
	protected function _retrieve() {
		return $this->download();
	}


	/**
	 * Downloads a file.
	 *
	 * @see get_url()
	 * @see get_local_path()
	 * @since 4.6.10
	 * @param string|null $url The URL of the file to download. If null, the URL used is that of this instance.
	 * @return string The absolute local path to the downloaded file.
	 * @throws Exception If no URL is set, or the resource is unreadable, or something went wrong.
	 */
	public function download( $url = null ) {
		if ( !is_null( $url ) )
			$this->set_url( $url );

		// Downloading the file
		$tmp_path = $this->_download();

		return $tmp_path;
	}


	/**
	 * @since 4.6.10
	 * @param string $url
	 * @param int $timeout
	 * @return string The local path to the downloaded image, if successful; an error instance if download failed.
	 */
	protected function _download() {
		$cache = $this->get_cache();

		try {
			$tmp_path = $cache->download_image( $this );
		} catch ( Exception $e ) {
			$https = 'https';
			$url = $this->get_url();
			// Should we try unsecure protocol?
			if ( $this->is_fall_back_to_unsecure() && (stripos( $url, $https ) === 0) ) {
				$orig_url = $url;
				$url = 'http' . substr( $url, strlen( $https ) );
				$this->set_url( $url );
				$tmp_path = $cache->download_image( $this );
				$this->set_url( $orig_url );
			}
			else throw $e;
		}

		return $tmp_path;
	}


	/**
	 * Retrieves the time, at which the cache file was last modified.
	 *
	 * @since 4.7.3
	 * @return int|null The timestamp that represents the time when the cache file was last modified on success;
	 *  null if timestamp could not be retrieved.
	 */
	public function get_modification_time() {
		return $this->get_cache()->get_image_modification_time( $this );
	}


	/**
	 * Checks whether or not the cache file exists.
	 *
	 * @since 4.7.3
	 * @return bool True if the cache file exists; false otherwise.
	 */
	public function check_exists() {
		return $this->get_cache()->check_file_exists( $this );
	}


	/**
	 * Checks whether or not the cache file has expired.
	 *
	 * @see get_ttl()
	 * @see WPRSS_Image_Cache::check_image_expired()
	 * @since 4.7.3
	 * @return bool True if the cache file has expired; false otherwise.
	 */
	public function check_expired() {
		return $this->get_cache()->check_image_expired( $this );
	}


	/**
	 * Get the time, at which the cache file of this instance will expire.
	 *
	 * @since 4.7.3
	 * @see WPRSS_Image_Cache::get_expiration_time()
	 * @return int Unix timestamp representing the time when the cache file of this instance will expire.
	 *  If file doesn't exist, or it's modification date cannot be retrieved, will return 0.
	 */
	public function get_expiration_time() {
		return $this->get_cache()->get_expiration_time( $this );
	}


	/**
	 * Deletes the cache file of this instance.
	 *
	 * @since 4.6.10
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	public function delete() {
		if ( $path = $this->get_path() )
			if ( file_exists( $path ) )
				return @unlink( $path );

		return false;
	}


	/**
	 * Generates and stores for subsequent use a filename, which is unique for this instance's URL.
	 *
	 * This file name is relative to the base directory of cache.
	 *
	 * @see get_tmp_dir().
	 * @since 4.6.10
	 * @return string A unique name for a local cache file, based on the URL of the resource.
	 */
	public function get_unique_name() {
		if( !isset($this->_unique_name) )
			$this->_set_unique_name( $this->get_cache()->get_unique_filename( $this ) );

		return $this->_unique_name;
	}


	/**
	 * @since 4.6.10
	 * @param string $name The relative filename to be used for this instance's cache file.
	 * @return \WPRSS_Image_Cache_Image This instance.
	 */
	protected function _set_unique_name( $name ) {
		$this->_unique_name = $name;
		return $this;
	}


	/**
	 * Get the size of the image, which is locally cached.
	 *
	 * @since 4.6.10
	 * @return array A numeric array, where index 0 holds image width, and index 1 holds image height.
	 * @throws Exception If image file is unreadable.
	 */
	public function get_size() {
		if ( !isset( $this->_size ) ) {
			$path = $this->get_local_path();
			if ( !$this->is_readable() ) throw new Exception( sprintf( '%1$s: image file is not readable', $path ) );

			// Trying simplest way
			$size = @getimagesize( $path );
			if ( $size ) {
				$this->_size = [0 => $size[0], 1 => $size[1]];
			}

            if( !$this->_size && function_exists( 'gd_info' ) ) {
				$image = file_get_contents( $path );
				$image = @imagecreatefromstring( $image );

				if ($image !== false) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $this->_size = [0 => $width, 1 => $height];

                    wprss_log(
                        sprintf('Tried GD: %1$s', empty($this->_size) ? 'failure' : 'success'),
                        __METHOD__,
                        WPRSS_LOG_LEVEL_SYSTEM
                    );
                }
            }
		}

		return $this->_size;
	}


	/**
	 * @since 4.6.10
	 * @return boolean
	 */
	public function is_readable() {
		$path = $this->get_local_path();
		if ( !$path ) return false;
		return is_readable( $path );
	}
}
