<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\Referred;

class referring
{

    public static function get($args = array())
    {

        // Check Number of Country
        $number = (isset($args['number']) ? $args['number'] : 10);

        // Get List Top Referring
        try {
            $response = Referred::getTop($number);
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

    public static function lang()
    {
        return array(
            'server_ip'  => __('Server IP', 'wp-statistics'),
            'references' => __('References', 'wp-statistics')
        );
    }


}