<?php

/**
 * OAuth storage handler interface
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @package Dropbox\OAuth
 * @subpackage Storage
 */

interface Dropbox_StorageInterface
{
    /**
     * Get a token by type
     * @param string $type Token type to retrieve
     */
    public function get($type);
    
    /**
     * Set a token by type
     * @param \stdClass $token Token object to set
     * @param string $type Token type
     */
    public function set($token, $type);
    
    /**
     * Delete tokens for the current session/user
     */
    public function delete();
}
