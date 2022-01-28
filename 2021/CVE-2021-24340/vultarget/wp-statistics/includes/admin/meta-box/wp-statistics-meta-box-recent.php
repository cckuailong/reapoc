<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\Visitor;

class recent
{

    public static function get($args = array())
    {

        // Prepare Response
        try {
            $response = Visitor::get($args);
        } catch (\Exception $e) {
            \WP_Statistics::log($e->getMessage());
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