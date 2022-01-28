<?php

namespace WP_STATISTICS\MetaBox;

class about
{

    public static function get($args = array())
    {
        include WP_STATISTICS_DIR . 'includes/admin/templates/meta-box/about.php';
    }

}