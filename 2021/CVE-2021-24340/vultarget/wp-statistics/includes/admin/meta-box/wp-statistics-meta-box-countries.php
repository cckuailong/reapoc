<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\Country;
use WP_STATISTICS\DB;
use WP_STATISTICS\Helper;
use WP_STATISTICS\Menus;

class countries
{

    public static function get($args = array())
    {

        // Check Number of Country
        if (!isset($args['limit'])) {
            $args['limit'] = 10;
        }

        // Get List Top Country
        $response = Country::getTop($args);

        // Check For No Data Meta Box
        if (count($response) < 1) {
            $response['no_data'] = 1;
        }

        // Response
        return $response;
    }

}