<?php

/**
 * Dropbox API base class
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @link https://www.dropbox.com/developers
 * @link https://status.dropbox.com Dropbox status
 * @package Dropbox
 */
class UpdraftPlus_Dropbox_API {
    // API Endpoints
    const API_URL_V2  = 'https://api.dropboxapi.com/';
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
     * Format of the API response
     * @var string
     */
    private $responseFormat = 'php';
    
    /**
     * JSONP callback
     * @var string
     */
    private $callback = 'dropboxCallback';
    
    /**
     * Chunk size used for chunked uploads
     * @see \Dropbox\API::chunkedUpload()
     */
    private $chunkSize = 4194304;
    
    /**
     * Set the OAuth consumer object
     * See 'General Notes' at the link below for information on access type
     * @link https://www.dropbox.com/developers/reference/api
     * @param OAuth\Consumer\ConsumerAbstract $OAuth
     * @param string $root Dropbox app access type
     */
    public function __construct(Dropbox_ConsumerAbstract $OAuth, $root = 'sandbox') {
        $this->OAuth = $OAuth;
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
     * This function will make a request to refresh the access token
     *
     * @return void
     */
    public function refreshAccessToken() {
        $this->OAuth->refreshAccessToken();
    }
    
    /**
     * Retrieves information about the user's account
     * @return object stdClass
     */
    public function accountInfo() {
        $call = '2/users/get_current_account';
        $params = array('api_v2' => true);
        $response = $this->fetch('POST', self::API_URL_V2, $call, $params);
        return $response;
    }

    /**
     * Retrieves information about the user's quota
     * @param array $options - valid keys are 'timeout'
     * @return object stdClass
     */
    public function quotaInfo($options = array()) {
        $call = '2/users/get_space_usage';
        // Cases have been seen (Apr 2019) where a response came back (HTTP/2.0 response header - suspected outgoing web hosting proxy, as everyone else seems to get HTTP/1.0 and I'm not aware that current Curl versions would do HTTP/2.0 without specifically being told to) after 180 seconds; a valid response, but took a long time.
        $params = array(
            'api_v2' => true,
            'timeout' => isset($options['timeout']) ? $options['timeout'] : 20
        );
        $response = $this->fetch('POST', self::API_URL_V2, $call, $params);
        return $response;
    }
    
    /**
     * Uploads large files to Dropbox in mulitple chunks
     * @param string $file Absolute path to the file to be uploaded
     * @param string|bool $filename The destination filename of the uploaded file
     * @param string $path Path to upload the file to, relative to root
     * @param boolean $overwrite Should the file be overwritten? (Default: true)
     * @param integer $offset position to seek to when opening the file
     * @param string $uploadID existing upload_id to resume an upload
     * @param string|array function to call back to upon each chunk
     * @return stdClass
     */
    public function chunkedUpload($file, $filename = false, $path = '', $overwrite = true, $offset = 0, $uploadID = null, $callback = null) {

		if (file_exists($file)) {
            if ($handle = @fopen($file, 'r')) {
                // Set initial upload ID and offset
                if ($offset > 0) {
                    fseek($handle, $offset);
                }

                /*
                    Set firstCommit to true so that the upload session start endpoint is called.
                 */
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
								'offset' => (int)$offset
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
                        if ($callback) {
                            call_user_func($callback, $offset, $uploadID, $file);
                        }
                        $this->OAuth->setInFile(null);
                    }
                }
                
                // Complete the chunked upload
                $filename = (is_string($filename)) ? $filename : basename($file);
                $params = array(
					'cursor' => array(
						'session_id' => $uploadID,
						'offset' => $offset
					),
					'commit' => array(
						'path' => '/' . $this->encodePath($path . $filename),
						'mode' => 'add'
					),
					'api_v2' => true,
					'content_upload' => true
				);
                $response = $this->append_upload($params, true);
                return $response;
            } else {
                throw new Exception('Could not open ' . $file . ' for reading');
            }
        }
        
        // Throw an Exception if the file does not exist
        throw new Exception('Local file ' . $file . ' does not exist');
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
				
//                 $params['cursor']['offset'] = $responseCheck[1];
//                 $response = $this->append_upload($params, $last_call);
            } elseif (isset($responseCheck) && strpos($responseCheck[0], 'closed') !== false) {
                throw new Exception("Upload with upload_id {$params['cursor']['session_id']} already completed");
            } else {
                throw $e;
            }
        }
        return $response;
    }
    
    /**
     * Chunked downloads a file from Dropbox, it will return false if a file handle is not passed and will return true if the call was successful.
     *
     * @param string $file Path - to file, relative to root, including path
     * @param resource $outFile - the local file handle
     * @param array $options    - any extra options to be passed e.g headers
     * @return boolean          - a boolean to indicate success or failure
     */
    public function download($file, $outFile = null, $options = array()) {
        // Only allow php response format for this call
        if ($this->responseFormat !== 'php') {
            throw new Exception('This method only supports the `php` response format');
        }

        if ($outFile) {
            $this->OAuth->setOutFile($outFile);

            $params = array('path' => '/' . $file, 'api_v2' => true, 'content_download' => true);

            if (isset($options['headers'])) {
                foreach ($options['headers'] as $key => $header) {
                    $headers[] = $key . ': ' . $header;
                }
                $params['headers'] = $headers;
            }

            $file = $this->encodePath($file);        
            $call = 'files/download';

            $response = $this->fetch('GET', self::CONTENT_URL_V2, $call, $params);

            fclose($outFile);

            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Calls the relevant method to return metadata for all files and folders that match the search query
     * @param mixed $query The search string. Must be at least 3 characters long
     * @param string [$path=''] The path to the folder you want to search in
     * @param integer [$limit=1000] Maximum number of results to return (1-1000)
     * @param integer [$cursor=''] A Dropbox ID to start the search from
     * @return array
     */
    public function search($query, $path = '', $limit = 1000, $cursor = '') {
        if (empty($cursor)) {
            return $this->start_search($query, $path, $limit);
        } else {
            return $this->continue_search($cursor);
        }
    }

    /**
     * This method will start a search for all files and folders that match the search query
     *
     * @param mixed   $query - the search string, must be at least 3 characters long
     * @param string  $path  - the path to the folder you want to search in
     * @param integer $limit - maximum number of results to return (1-1000)
     *
     * @return array - an array of search results
     */
    private function start_search($query, $path, $limit) {
        $call = '2/files/search_v2';
        $path = $this->encodePath($path);
        // APIv2 requires that the path match this regex: String(pattern="(/(.|[\r\n])*)?|(ns:[0-9]+(/.*)?)")
        if ($path && '/' != substr($path, 0, 1)) $path = "/$path";
        $params = array(
            'query' => $query,
            'options' => array(
                'path' => $path,
                'max_results' => ($limit < 1) ? 1 : (($limit > 1000) ? 1000 : (int) $limit),
            ),
            'api_v2' => true,
        );
        $response = $this->fetch('POST', self::API_URL_V2, $call, $params);
        return $response;
    }

    /**
     * This method will continue a previous search for all files and folders that match the previous search query
     *
     * @param string $cursor - a Dropbox ID to continue the search
     *
     * @return array - an array of search results
     */
    private function continue_search($cursor) {
        $call = '2/files/search/continue_v2';
        $params = array(
            'cursor' => $cursor,
            'api_v2' => true,
        );
        $response = $this->fetch('POST', self::API_URL_V2, $call, $params);
        return $response;
    }
    
    /**
     * Deletes a file or folder
     * @param string $path The path to the file or folder to be deleted
     * @return object stdClass
     */
    public function delete($path) {
        $call = '2/files/delete_v2';
        $params = array('path' => '/' . $this->normalisePath($path), 'api_v2' => true);
        $response = $this->fetch('POST', self::API_URL_V2, $call, $params);
        return $response;
    }

    /**
     * Intermediate fetch function
     * @param string $method The HTTP method
     * @param string $url The API endpoint
     * @param string $call The API method to call
     * @param array $params Additional parameters
     * @return mixed
     */
    private function fetch($method, $url, $call, array $params = array()) {
        // Make the API call via the consumer
        $response = $this->OAuth->fetch($method, $url, $call, $params);
        
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
     * Set the API response format
     * @param string $format One of php, json or jsonp
     * @return void
     */
    public function setResponseFormat($format) {
        $format = strtolower($format);
        if (!in_array($format, array('php', 'json', 'jsonp'))) {
            throw new Exception("Expected a format of php, json or jsonp, got '$format'");
        } else {
            $this->responseFormat = $format;
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
    * Set the JSONP callback function
    * @param string $function
    * @return void
    */
    public function setCallback($function) {
        $this->callback = $function;
    }
    
    /**
     * Get the mime type of downloaded file
     * If the Fileinfo extension is not loaded, return false
     * @param string $data File contents as a string or filename
     * @param string $isFilename Is $data a filename?
     * @return boolean|string Mime type and encoding of the file
     */
    private function getMimeType($data, $isFilename = false) {
        if (extension_loaded('fileinfo')) {
            $finfo = new finfo(FILEINFO_MIME);
            if ($isFilename !== false) {
                return $finfo->file($data);
            }
            return $finfo->buffer($data);
        }
        return false;
    }
    
    /**
     * Trim the path of forward slashes and replace
     * consecutive forward slashes with a single slash
     * @param string $path The path to normalise
     * @return string
     */
    private function normalisePath($path) {
        $path = preg_replace('#/+#', '/', trim($path, '/'));
        return $path;
    }
    
    /**
     * Encode the path, then replace encoded slashes
     * with literal forward slash characters
     * @param string $path The path to encode
     * @return string
     */
    private function encodePath($path) {
        // in APIv1, encoding was needed because parameters were passed as part of the URL; this is no longer done in our APIv2 SDK; hence, all that we now do here is normalise.
        return $this->normalisePath($path);
    }
}
