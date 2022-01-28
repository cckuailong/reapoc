<?php

namespace WP_STATISTICS\MetaBox;

class pages
{
    /**
     * Get MetaBox Rest API Data
     *
     * @param array $args
     * @return array
     */
    public static function get($args = array())
    {

        // Get List Top Page
        $response = \WP_STATISTICS\Pages::getTop($args);

        // Check For No Data Meta Box
        if (count($response) < 1) {
            $response['no_data'] = 1;
        }

        // Response
        return $response;
    }

}