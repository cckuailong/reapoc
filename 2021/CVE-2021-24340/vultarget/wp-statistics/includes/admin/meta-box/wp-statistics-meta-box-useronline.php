<?php

namespace WP_STATISTICS\MetaBox;

class useronline
{

    public static function get($args = array())
    {

        // Prepare Response
        try {
            $response = \WP_STATISTICS\UserOnline::get($args);
        } catch (\Exception $e) {
            $response = array();
        }

        // Check For No Data Meta Box
        if (count($response) < 1) {
            $response['no_data'] = 1;
        }

        // Response
        return $response;

    }

}