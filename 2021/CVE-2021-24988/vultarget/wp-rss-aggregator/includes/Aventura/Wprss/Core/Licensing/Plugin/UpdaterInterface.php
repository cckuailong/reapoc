<?php

namespace Aventura\Wprss\Core\Licensing\Plugin;

/**
 * Interface for a plugin updater class.
 */
interface UpdaterInterface {

    /**
     * @param type $apiUrl The URL pointing to the custom API endpoint.
     * @param type $pluginFile Path to the plugin file.
     * @param type $apiData Optional data to send with API calls.
     */
    public function __construct($apiUrl, $pluginFile, $apiData = array());
}
