<?php
if (!class_exists('WP_Statistics_Updates')) {
    class WP_Statistics_Updates
    {
        public static $geoip = array();

        static function do_upgrade()
        {
        }

        static function download_geoip($pack, $type = "enable")
        {
        }

        static function download_referrerspam()
        {
        }

        static function populate_geoip_info()
        {
        }
    }
}
