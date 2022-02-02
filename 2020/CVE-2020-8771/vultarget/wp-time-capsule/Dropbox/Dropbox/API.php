<?php

/**
 * Dropbox API base class
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @link https://www.dropbox.com/developers
 * @link https://status.dropbox.com Dropbox status
 * @package Dropbox
 */

class WPTC_Dropbox_API {
	// API Endpoints
	const API_URL     = 'https://api.dropbox.com/1/';
	const API_URL_V2  = 'https://api.dropboxapi.com/';
	const CONTENT_URL = 'https://api-content.dropbox.com/1/';
	const CONTENT_URL_V2 = 'https://content.dropboxapi.com/2/';

	/**
	 * OAuth consumer object
	 * @var null|OAuth\Consumer
	 */
	private $OAuth;

	/**
	 * The root level for file paths
	 * Either `dropbox` or `sandbox` (preferred)
	 * @var null|string
	 */
	private $root;

	/**
	 * Chunk size used for chunked uploads
	 * @see \Dropbox_API::chunkedUpload()
	 */
	private $chunkSize = 4194304;

    private $responseFormat = 'php';

	/**
	 * Object to track uploads
	 */
	private $tracker;

	private $base;

	/**
	 * Set the OAuth consumer object
	 * See 'General Notes' at the link below for information on access type
	 * @link https://www.dropbox.com/developers/reference/api
	 * @param OAuth\Consumer\ConsumerAbstract $OAuth
	 * @param string                          $root  Dropbox app access type
	 */
	public function __construct($OAuth, $root = 'sandbox') {
		$this->OAuth = $OAuth;
		$this->base = new Utils_Base();
		$this->setRoot($root);
	}

	/**
	 * Set the root level
	 * @param mixed $root
	 * @throws Exception
	 * @return void
	 */
	public function setRoot($root) {
		if ($root !== 'sandbox' && $root !== 'dropbox') {
			throw new Exception("Expected a root of either 'dropbox' or 'sandbox', got '$root'");
		} else {
			$this->root = $root;
		}
	}

	/**
	 * Set the tracker
	 * @param Tracker $tracker
	 */
	public function setTracker($tracker) {
		$this->tracker = $tracker;
	}

	/**
	 * Retrieves information about the user's account
	 * @return object stdClass
	 */
	public function accountInfo() {
		//API V1
		// return $this->fetch('POST', self::API_URL, 'account/info');

		$call = '2/users/get_current_account';
		$params = array('api_v2' => true);
		$response = $this->fetch('POST', self::API_URL_V2, $call, $params);
		return $response;
	}

	/**
	 * Retrieves information about the user's quota
	 * @return object stdClass
	 */
	public function quotaInfo() {
		$call = '2/users/get_space_usage';
		$params = array('api_v2' => true);
		$response = $this->fetch('POST', self::API_URL_V2, $call, $params);
		return $response;
	}

	/**
	 * Uploads a physical file from disk
	 * Dropbox impose a 150MB limit to files uploaded via the API. If the file
	 * exceeds this limit or does not exist, an Exception will be thrown
	 * @param  string      $file      Absolute path to the file to be uploaded
	 * @param  string|bool $filename  The destination filename of the uploaded file
	 * @param  string      $path      Path to upload the file to, relative to root
	 * @param  boolean     $overwrite Should the file be overwritten? (Default: true)
	 * @return object      stdClass
	 */
	public function putFile($file, $filename = false, $path = '', $overwrite = true) {
		if (!file_exists($file)) {
			// Throw an Exception if the file does not exist
			throw new Exception('Local file ' . $file . ' does not exist');
		}

		if (filesize($file) >= 157286400) {
			//Dropbox single file upload limit is 150MB, Greater than 150MB should be chunk uploaded
			throw new Exception('File exceeds 150MB upload limit');
		}

		$handle = @fopen($file, 'r');
		//Set the file content to $this->InFile
		if (filesize($file) > 0) {
			$this->OAuth->setInFile(fread($handle, filesize($file)));
		} else {
			$this->OAuth->setInFile('');
		}
		fclose($handle);

		$filename = (is_string($filename)) ? $filename : basename($file);
		$path = '/' . $this->encodePath($path .'/'. $filename);

		// wptc_log($path, '--------$path--------');
		
		$params = array(
			'path' => $path,
			'mute' => true,
			'mode' => 'overwrite',
			'api_v2' => true,
			'content_upload' => true
			);
		$response = $this->fetch('POST', self::CONTENT_URL_V2, 'files/upload', $params);
		// wptc_log($response, '--------$response--------');
		return $response;
	}

	/**
	 * Not used
	 * Uploads file data from a stream
	 * Note: This function is experimental and requires further testing
	 * @todo Add filesize check
	 *@ param  resource $stream    A readable stream created using fopen()
	 * @param  string   $filename  The destination filename, including path
	 * @param  boolean  $overwrite Should the file be overwritten? (Default: true)
	 * @return array
	 */
	// public function putStream($stream, $filename, $overwrite = true) {
	// 	$this->OAuth->setInFile($stream);
	// 	$path = $this->encodePath($filename);
	// 	$call = 'files_put/' . $this->root . '/' . $path;
	// 	$params = array('overwrite' => (int) $overwrite);

	// 	return $this->fetch('PUT', self::CONTENT_URL, $call, $params);
	// }

	/**
	 * Uploads large files to Dropbox in mulitple chunks
	 * @param  string      $file      Absolute path to the file to be uploaded
	 * @param  string|bool $filename  The destination filename of the uploaded file
	 * @param  string      $path      Path to upload the file to, relative to root
	 * @param  boolean     $overwrite Should the file be overwritten? (Default: true)
	 * @return stdClass
	 */
	public function chunkedUpload($file, $filename = false, $path = '', $overwrite = true, $offset = 0, $uploadID = null) {

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (!file_exists($file)) throw new Exception('Local file ' . $file . ' does not exist');

		if (!($handle = @fopen($file, 'r'))) throw new Exception('Could not open ' . $file . ' for reading');

		// Seek to the correct position on the file pointer
		fseek($handle, $offset);
		$to_exit = false;

		//Set firstCommit to true so that the upload session start endpoint is called.
		$firstCommit = (0 == $offset);

		// Read from the file handle until EOF, uploading each chunk
		while ($data = fread($handle, $this->chunkSize)) {

			// Set the file, request parameters and send the request
			$this->OAuth->setInFile($data);

			if ($firstCommit) {
				$params = array(
					'close' => false,
					'api_v2' => true,
					'content_upload' => true
				);
				$response = $this->fetch('POST', self::CONTENT_URL_V2, 'files/upload_session/start', $params);
				$firstCommit = false;

			} else {
				$params = array(
					'cursor' => array(
						'session_id' => $uploadID,
						// If you send it as a string, Dropbox will be unhappy
						'offset' => (int) $offset
					),
					'api_v2' => true,
					'content_upload' => true
				);
				$response = $this->append_upload($params, false);
			}

			// On subsequent chunks, use the upload ID returned by the previous request
			if (isset($response['body']->session_id)) {
				$uploadID = $response['body']->session_id;
			}

			/*
				API v2 no longer returns the offset, we need to manually work this out. So check that there are no errors and update the offset as well as calling the callback method.
			 */
			if (!isset($response['body']->error)) {
				$offset = ftell($handle);
				$this->OAuth->setInFile(null);
			}

			if ($this->tracker) {
				$this->tracker->track_upload($file, $uploadID, $offset);
			}

			if (is_wptc_timeout_cut()) {
				$to_exit = true;
				break;
			}
		}

		if ($to_exit) {
			wptc_log(array(), "--------exitng by backup path time--------");
			global $current_process_file_id;
			backup_proper_exit_wptc('', $current_process_file_id);
		}

		wptc_log(array(), "--------must have uploaded--------");

		if(strrpos($file, 'wordpress-db_meta_data.sql') !== false){
			$config = WPTC_Factory::get('config');
			$config->set_option('meta_data_upload_offset', -1);
			$config->set_option('meta_data_upload_id', '');
		}

		// Complete the chunked upload
		$filename = (is_string($filename)) ? $filename : basename($file);
		$params = array(
			'cursor' => array(
				'session_id' => $uploadID,
				'offset' => (int) $offset
			),
			'commit' => array(
				'path' => '/' . $this->encodePath($path .'/'. $filename),
				'mode' => 'overwrite'
			),
			'api_v2' => true,
			'content_upload' => true
		);

		return $this->append_upload($params, true);
	}

	private function append_upload($params, $last_call) {
		try {
			if ($last_call){
				$response = $this->fetch('POST', self::CONTENT_URL_V2, 'files/upload_session/finish', $params);
			} else {
				$response = $this->fetch('POST', self::CONTENT_URL_V2, 'files/upload_session/append_v2', $params);
			}
		} catch (Exception $e) {
			$responseCheck = json_decode($e->getMessage());
			if (isset($responseCheck) && strpos($responseCheck[0] , 'incorrect_offset') !== false) {
				$expected_offset = $responseCheck[1];
				throw new Exception('Submitted input out of alignment: got ['.$params['cursor']['offset'].'] expected ['.$expected_offset.']');
				//$params['cursor']['offset'] = $responseCheck[1];
				//$response = $this->append_upload($params, $last_call);
			} else {
				throw $e;
			}
		}
		return $response;
	}

	/**
	 * Downloads a file
	 * Returns the base filename, raw file data and mime type returned by Fileinfo
	 * @param  string $file     Path to file, relative to root, including path
	 * @param  string $outFile  Filename to write the downloaded file to
	 * @param  string $revision The revision of the file to retrieve
	 * @return array
	 */
	public function getFile($file, $outFile = false, $revision = null, $allow_resume = array()) {
		$handle = null;
		$tempFolder = $this->base->getTempFolderFromOutFile(wp_normalize_path($outFile));
		if ($outFile !== false) {
			// Create a file handle if $outFile is specified
			$this->prepareSetOutFile($outFile, 'w');
		}

		$file = $this->encodePath($file);
		$call = 'files/download';
		$params = array('path' => 'rev:'.$revision, 'api_v2' => true, 'content_download' => true);
		$response = $this->fetch('GET', self::CONTENT_URL_V2, $call, $params);
		// Close the file handle if one was opened
		if ($handle) fclose($handle);

		return array(
			'name' => ($outFile) ? $outFile : basename($file),
			'mime' => $this->getMimeType(($outFile) ? $outFile : $response['body'], $outFile),
			'meta' => json_decode($response['headers']['dropbox-api-result']),
			'data' => $response['body'],
		);
	}

	public function prepareSetOutFile($outFile, $mode) {
		$tempFolderFile = $this->base->getTempFolderFromOutFile(wp_normalize_path($outFile));

		//setting chmod from filesystem
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-012');
				return false;
			}
		}
		$chRes = $wp_filesystem->chmod($tempFolderFile, false, true);
		if (!$handle = @fopen($tempFolderFile, $mode)) {
			throw new Exception("Unable to open file handle for $tempFolderFile");
		} else {
			$this->OAuth->setOutFile($handle);
			return $handle;
		}
	}

	/**
	 * Downloads a file
	 * Returns the base filename, raw file data and mime type returned by Fileinfo
	 * @param  string $file     Path to file, relative to root, including path
	 * @param  string $outFile  Filename to write the downloaded file to
	 * @param  string $revision The revision of the file to retrieve
	 * @return array
	 */
	public function chunkedDownload($file, $outFile = false, $revision = null, $isChunkDownload = array(), $meta_file_download = null) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$handle = null;
		if ($outFile !== false) {
			// Create a file handle if $outFile is specified
			if ($isChunkDownload['c_offset'] == 0) {
				//while restoring ... first
				$handle = $this->prepareSetOutFile($outFile, 'w');
			} else {
				$handle = $this->prepareSetOutFile($outFile, 'a');
			}
		}

		$outFilePath = $this->base->getTempFolderFromOutFile(wp_normalize_path($outFile));
		$call = 'files/download';
		$params = array('path' =>'rev:'.$revision, 'api_v2' => true, 'content_download' => true);
		$response = $this->fetch('GET', self::CONTENT_URL_V2, $call, $params, $isChunkDownload);

		// Set the data offset
		if ($response) {
			$offset = filesize($outFilePath);
		}

		if (empty($meta_file_download)) {
			if ($this->tracker) {
				$this->tracker->track_download($outFile, false, $offset, $isChunkDownload);
			}
		} else {
			$this->tracker->track_meta_download($offset, $isChunkDownload);
		}

		// Close the file handle if one was opened
		if ($handle) {
			fclose($handle);
		}

		$data = array(
			'name' => ($outFile) ? $outFile : basename($file),
			'mime' => $this->getMimeType(($outFile) ? $outFile : $response['body'], $outFile),
			'meta' => json_decode($response['headers']['dropbox-api-result']),
			'data' => $response['body'],
			'chunked' => true,
		);
		return $data;
	}

	/**
	 * Not used
	 * Retrieves file and folder metadata
	 * @param  string $path    The path to the file/folder, relative to root
	 * @param  string $rev     Return metadata for a specific revision (Default: latest rev)
	 * @param  int    $limit   Maximum number of listings to return
	 * @param  string $hash    Metadata hash to compare against
	 * @param  bool   $list    Return contents field with response
	 * @param  bool   $deleted Include files/folders that have been deleted
	 * @return object stdClass
	 */
	// public function metaData($path = null, $rev = null, $limit = 10000, $hash = false, $list = true, $deleted = false) {
	// 	$call = 'metadata/' . $this->root . '/' . $this->encodePath($path);
	// 	$params = array(
	// 		'file_limit' => ($limit < 1) ? 1 : (($limit > 10000) ? 10000 : (int) $limit),
	// 		'hash' => (is_string($hash)) ? $hash : 0,
	// 		'list' => (int) $list,
	// 		'include_deleted' => (int) $deleted,
	// 		'rev' => (is_string($rev)) ? $rev : null,
	// 	);

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Return "delta entries", intructing you how to update
	 * your application state to match the server's state
	 * Important: This method does not make changes to the application state
	 * @param  null|string $cursor Used to keep track of your current state
	 * @return array       Array of delta entries
	 */
	// public function delta($cursor = null) {
	// 	$call = 'delta';
	// 	$params = array('cursor' => $cursor);

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Obtains metadata for the previous revisions of a file
	 * @param string Path to the file, relative to root
	 * @param integer Number of revisions to return (1-1000)
	 * @return array
	 */
	public function revisions($file_id, $limit = 50) {

		$call = '2/files/list_revisions';
		$params = array(
			"path" => $file_id,
			"mode" => "id",
			"limit" => $limit,
			"api_v2" => true,
			"headers" => array('Content-Type: application/json'),
		);

		try {
			return $this->fetch('POST', self::API_URL_V2, $call, $params);
		} catch (Exception $e) {
			return false;
		}

		return false;
	}

	/**
	 * Not used
	 * Restores a file path to a previous revision
	 * @param  string $file     Path to the file, relative to root
	 * @param  string $revision The revision of the file to restore
	 * @return object stdClass
	 */
	// public function restore($file, $revision) {
	// 	$call = 'restore/' . $this->root . '/' . $this->encodePath($file);
	// 	$params = array('rev' => $revision);

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Returns metadata for all files and folders that match the search query
	 * @param  mixed   $query   The search string. Must be at least 3 characters long
	 * @param  string  $path    The path to the folder you want to search in
	 * @param  integer $limit   Maximum number of results to return (1-1000)
	 * @param  boolean $deleted Include deleted files/folders in the search
	 * @return array
	 */
	//Not used
	// public function search($query, $path = '', $limit = 1000, $deleted = false) {
	// 	$call = 'search/' . $this->root . '/' . $this->encodePath($path);
	// 	$params = array(
	// 		'query' => $query,
	// 		'file_limit' => ($limit < 1) ? 1 : (($limit > 1000) ? 1000 : (int) $limit),
	// 		'include_deleted' => (int) $deleted,
	// 	);

	// 	return $this->fetch('GET', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Creates and returns a shareable link to files or folders
	 * The link returned is for a preview page from which the user an choose to
	 * download the file if they wish. For direct download links, see media().
	 * @param  string $path The path to the file/folder you want a sharable link to
	 * @return object stdClass
	 */
	// public function shares($path, $shortUrl = true) {
	// 	$call = 'shares/' . $this->root . '/' . $this->encodePath($path);
	// 	$params = array('short_url' => ($shortUrl) ? 1 : 0);

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Returns a link directly to a file
	 * @param  string $path The path to the media file you want a direct link to
	 * @return object stdClass
	 */
	// public function media($path) {
	// 	$call = 'media/' . $this->root . '/' . $this->encodePath($path);

	// 	return $this->fetch('POST', self::API_URL, $call);
	// }

	/**
	 * Not used
	 * Gets a thumbnail for an image
	 * @param  string $file   The path to the image you wish to thumbnail
	 * @param  string $format The thumbnail format, either JPEG or PNG
	 * @param  string $size   The size of the thumbnail
	 * @return array
	 */
	// public function thumbnails($file, $format = 'JPEG', $size = 'small') {
	// 	$format = strtoupper($format);
	// 	// If $format is not 'PNG', default to 'JPEG'
	// 	if ($format != 'PNG') {
	// 		$format = 'JPEG';
	// 	}

	// 	$size = strtolower($size);
	// 	$sizes = array('s', 'm', 'l', 'xl', 'small', 'medium', 'large');
	// 	// If $size is not valid, default to 'small'
	// 	if (!in_array($size, $sizes)) {
	// 		$size = 'small';
	// 	}

	// 	$call = 'thumbnails/' . $this->root . '/' . $this->encodePath($file);
	// 	$params = array('format' => $format, 'size' => $size);
	// 	$response = $this->fetch('GET', self::CONTENT_URL, $call, $params);

	// 	return array(
	// 		'name' => basename($file),
	// 		'mime' => $this->getMimeType($response['body']),
	// 		'meta' => json_decode($response['headers']['x-dropbox-metadata']),
	// 		'data' => $response['body'],
	// 	);
	// }

	/**
	 * Not used
	 * Creates and returns a copy_ref to a file
	 * This reference string can be used to copy that file to another user's
	 * Dropbox by passing it in as the from_copy_ref parameter on /fileops/copy
	 * @param $path File for which ref should be created, relative to root
	 * @return array
	 */
	// public function copyRef($path) {
	// 	$call = 'copy_ref/' . $this->root . '/' . $this->encodePath($path);

	// 	return $this->fetch('GET', self::API_URL, $call);
	// }

	/**
	 * Not used
	 * Copies a file or folder to a new location
	 * @param  string      $from        File or folder to be copied, relative to root
	 * @param  string      $to          Destination path, relative to root
	 * @param  null|string $fromCopyRef Must be used instead of the from_path
	 * @return object      stdClass
	 */
	// public function copy($from, $to, $fromCopyRef = null) {
	// 	$call = 'fileops/copy';
	// 	$params = array(
	// 		'root' => $this->root,
	// 		'from_path' => $this->normalisePath($from),
	// 		'to_path' => $this->normalisePath($to),
	// 	);

	// 	if ($fromCopyRef) {
	// 		$params['from_path'] = null;
	// 		$params['from_copy_ref'] = $fromCopyRef;
	// 	}

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Creates a folder
	 * @param string New folder to create relative to root
	 * @return object stdClass
	 */
	// public function create($path) {
	// 	$call = 'fileops/create_folder';
	// 	$params = array('root' => $this->root, 'path' => $this->normalisePath($path));

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Not used
	 * Deletes a file or folder
	 * @param  string $path The path to the file or folder to be deleted
	 * @return object stdClass
	 */
	// public function delete($path) {
	// 	$call = '2/files/delete';
	// 	$params = array('path' => '/' . $this->normalisePath($path), 'api_v2' => true);
	// 	$response = $this->fetch('POST', self::API_URL_V2, $call, $params);
	// 	return $response;
	// }

	/**
	 * Not used
	 * Moves a file or folder to a new location
	 * @param  string $from File or folder to be moved, relative to root
	 * @param  string $to   Destination path, relative to root
	 * @return object stdClass
	 */
	// public function move($from, $to) {
	// 	$call = 'fileops/move';
	// 	$params = array(
	// 		'root' => $this->root,
	// 		'from_path' => $this->normalisePath($from),
	// 		'to_path' => $this->normalisePath($to),
	// 	);

	// 	return $this->fetch('POST', self::API_URL, $call, $params);
	// }

	/**
	 * Intermediate fetch function
	 * @param  string $method The HTTP method
	 * @param  string $url    The API endpoint
	 * @param  string $call   The API method to call
	 * @param  array  $params Additional parameters
	 * @return mixed
	 */
	    private function fetch($method, $url, $call, array $params = array(), $isChunkDownload = array())
    {
        // Make the API call via the consumer
        $response = $this->OAuth->fetch($method, $url, $call, $params, $isChunkDownload);

        // Format the response and return
        switch ($this->responseFormat) {
            case 'json':
                return json_encode($response);
            case 'jsonp':
                $response = json_encode($response);
                return $this->callback . '(' . $response . ')';
            default:
                return $response;
        }
    }


	/**
	 * Set the chunk size for chunked uploads
	 * If $chunkSize is empty, set to 4194304 bytes (4 MB)
	 * @see \Dropbox\API\chunkedUpload()
	 */
	public function setChunkSize($chunkSize = 4194304) {
		if (!is_int($chunkSize)) {
			throw new Exception('Expecting chunk size to be an integer, got ' . gettype($chunkSize));
		} elseif ($chunkSize > 157286400) {
			throw new Exception('Chunk size must not exceed 157286400 bytes, got ' . $chunkSize);
		} else {
			$this->chunkSize = $chunkSize;
		}
	}

	/**
	 * Get the mime type of downloaded file
	 * If the Fileinfo extension is not loaded, return false
	 * @param  string         $data       File contents as a string or filename
	 * @param  string         $isFilename Is $data a filename?
	 * @return boolean|string Mime type and encoding of the file
	 */
	private function getMimeType($data, $isFilename = false) {
		if (!extension_loaded('fileinfo') || !file_exists($data)) {
			return false;
		}

		$finfo = new finfo(FILEINFO_MIME);
		if ($isFilename !== false) {
			return @$finfo->file($data);
		}

		return $finfo->buffer($data);
	}

	/**
	 * Trim the path of forward slashes and replace
	 * consecutive forward slashes with a single slash
	 * then replace backslashes with forward slashes
	 * @param  string $path The path to normalise
	 * @return string
	 */
	private function normalisePath($path) {
		$path = preg_replace('#/+#', '/', trim($path, '/'));
		return $path;
	}

	/**
	 * Encode the path, then replace encoded slashes
	 * with literal forward slash characters
	 * @param  string $path The path to encode
	 * @return string
	 */
	private function encodePath($path) {
		// in APIv1, encoding was needed because parameters were passed as part of the URL; this is no longer done in our APIv2 SDK; hence, all that we now do here is normalise.
		return $this->normalisePath($path);
	}
}
