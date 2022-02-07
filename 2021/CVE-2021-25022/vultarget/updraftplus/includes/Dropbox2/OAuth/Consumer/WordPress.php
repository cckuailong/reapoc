<?php

/**
* OAuth consumer using the WordPress API
* @author David Anderson <david@updraftplus.com>
* @link https://github.com/DavidAnderson684/Dropbox
* @package Dropbox\OAuth
* @subpackage Consumer
*/

class Dropbox_ConsumerWordPress extends Dropbox_ConsumerAbstract
{    

    /**
     * Set properties and begin authentication
     * @param string $key
     * @param string $secret
     * @param \Dropbox\OAuth\Consumer\StorageInterface $storage
     * @param string $callback
     */
    public function __construct($key, $secret, Dropbox_StorageInterface $storage, $callback = null)
    {
        // Check we are in a WordPress environment
        if (!defined('ABSPATH')) {
            throw new Dropbox_Exception('The WordPress OAuth consumer requires a WordPress environment');
        }
        
        $this->consumerKey = $key;
        $this->consumerSecret = $secret;
        $this->storage = $storage;
        $this->callback = $callback;
        $this->authenticate();
    }

    /**
     * Execute an API call
     * @param string $method The HTTP method
     * @param string $url The API endpoint
     * @param string $call The API method to call
     * @param array $additional Additional parameters
     * @return array
     */
    public function fetch($method, $url, $call, array $additional = array())
    {
        // Get the signed request URL
        $request = $this->getSignedRequest($method, $url, $call, $additional);
        if ($method == 'GET') {
            $args = array ( );
            $response = wp_remote_get($request['url'], $args);
            $this->outFile = null;
        } elseif ($method == 'POST') {
            $args = array( 'body' => $request['postfields'] );
            $response = wp_remote_post($request['url'], $args );
        } elseif ($method == 'PUT' && $this->inFile) {
            return new WP_Error('unsupported', "WordPress does not have a native HTTP PUT function");
        }

        // If the response body is not a JSON encoded string
        // we'll return the entire response body
        // Important to do this first, as the next section relies on the decoding having taken place
        if (!$body = json_decode(wp_remote_retrieve_body($response))) {
            $body = wp_remote_retrieve_body($response);
        }

        // Check if an error occurred and throw an Exception. This is part of the authentication process - don't modify.
        if (!empty($body->error)) {
            $message = $body->error . ' (Status Code: ' . wp_remote_retrieve_response_code($response) . ')';
            throw new Dropbox_Exception($message);
        }
        
        if (is_wp_error($response)) {
            $message = $response->get_error_message();
            throw new Dropbox_Exception($message);
        }
        
        $results = array ( 'body' => $body, 'code' => wp_remote_retrieve_response_code($response), 'headers' => $response['headers'] );
        return $results;
    }
    
}
