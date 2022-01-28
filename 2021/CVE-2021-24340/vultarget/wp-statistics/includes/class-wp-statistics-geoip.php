<?php

namespace WP_STATISTICS;

use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoIP
{
    /**
     * List Geo ip Library
     *
     * @var array
     */
    public static $library = array(
        'country' => array(
            'source' => 'https://raw.githubusercontent.com/wp-statistics/GeoLite2-Country/master/GeoLite2-Country.mmdb.gz',
            'file'   => 'GeoLite2-Country',
            'opt'    => 'geoip',
            'cache'  => 31536000 //1 Year
        ),
        'city'    => array(
            'source' => 'https://raw.githubusercontent.com/wp-statistics/GeoLite2-City/master/GeoLite2-City.mmdb.gz',
            'file'   => 'GeoLite2-City',
            'opt'    => 'geoip_city',
            'cache'  => 6998000 //3 Month
        )
    );

    /**
     * Geo IP file Extension
     *
     * @var string
     */
    public static $file_extension = 'mmdb';

    /**
     * Default Private Country
     *
     * @var String
     */
    public static $private_country = '000';

    /**
     * Cache Option name For Store User City
     *
     * @var string
     */
    public static $city_cache_object_name = 'wp_statistics_users_city';

    /**
     * Get Geo IP Path
     *
     * @param $pack
     * @return mixed
     */
    public static function get_geo_ip_path($pack)
    {
        return wp_normalize_path(path_join(Helper::get_uploads_dir(WP_STATISTICS_UPLOADS_DIR), self::$library[strtolower($pack)]['file'] . '.' . self::$file_extension));
    }

    /**
     * Check Is Active Geo-ip
     *
     * @param bool $which
     * @param bool $CheckDBFile
     * @return boolean
     */
    public static function active($which = false, $CheckDBFile = true)
    {

        //Default Geo-Ip Option name
        $which = ($which === false ? 'country' : $which);
        $opt   = ($which == "city" ? 'geoip_city' : 'geoip');
        $value = Option::get($opt);

        //Check Exist GEO-IP file
        $file = self::get_geo_ip_path($which);
        if ($CheckDBFile and !file_exists($file)) {
            if ($value) {
                Option::update($opt, false);
            }
            return false;
        }

        // Return
        return $value;
    }

    /**
     * geo ip Loader
     *
     * @param $pack
     * @return bool|\GeoIp2\Database\Reader
     */
    public static function Loader($pack)
    {

        // Check file Exist
        $file = self::get_geo_ip_path($pack);
        if (file_exists($file)) {
            try {

                //Load GeoIP Reader
                $reader = new \GeoIp2\Database\Reader($file);
            } catch (InvalidDatabaseException $e) {
                \WP_Statistics::log($e->getMessage());
                return false;
            }
        } else {
            return false;
        }

        return $reader;
    }

    /**
     * Get Default Country Code
     *
     * @return String
     */
    public static function getDefaultCountryCode()
    {

        $opt = Option::get('private_country_code');
        if (isset($opt) and !empty($opt)) {
            return trim($opt);
        }

        return self::$private_country;
    }

    /**
     * Get Country Detail By User IP
     *
     * @param bool $ip
     * @param string $return
     * @return String|null
     * @throws \Exception
     * @see https://github.com/maxmind/GeoIP2-php
     */
    public static function getCountry($ip = false, $return = 'isoCode')
    {

        // Check in WordPress Cache
        $user_country = wp_cache_get('country-' . $ip, 'wp-statistics');
        if ($user_country != false) {
            return $user_country;
        }

        // Check in WordPress Database
        $user_country = self::getUserCountryFromDB($ip);
        if ($user_country != false) {
            wp_cache_set('country-' . $ip, $user_country, 'wp-statistics', DAY_IN_SECONDS);
            return $user_country;
        }

        // Default Country Name
        $default_country = self::getDefaultCountryCode();

        // Get User IP
        $ip = ($ip === false ? IP::getIP() : $ip);

        // Check Unknown IP
        if ($default_country != self::$private_country) {
            if (IP::CheckIPRange(IP::$private_SubNets)) {
                return $default_country;
            }
        }

        // Sanitize IP
        if (IP::isIP($ip) === false) {
            return $default_country;
        }

        // Load GEO-IP
        $reader = self::Loader('country');

        //Get Country name
        if ($reader != false) {

            try {
                //Search in Geo-IP
                $record = $reader->country($ip);

                //Get Country
                if ($return == "all") {
                    $location = $record->country;
                } else {
                    $location = $record->country->{$return};
                }
            } catch (AddressNotFoundException $e) {
                \WP_Statistics::log($e->getMessage());
            } catch (InvalidDatabaseException $e) {
                \WP_Statistics::log($e->getMessage());
            }
        }

        # Check Has Location
        if (isset($location) and !empty($location)) {
            wp_cache_set('country-' . $ip, $location, 'wp-statistics', DAY_IN_SECONDS);
            return $location;
        }

        return $default_country;
    }

    /**
     * Get User Country From Database
     *
     * @param $ip
     * @return false|string
     */
    public static function getUserCountryFromDB($ip)
    {
        global $wpdb;
        $date = date('Y-m-d', current_time('timestamp') - self::$library['country']['cache']);
        $user = $wpdb->get_row("SELECT `location` FROM " . DB::table('visitor') . " WHERE `ip` = '{$ip}' and `last_counter` >= '{$date}' ORDER BY `ID` DESC LIMIT 1");
        if (null !== $user) {
            return $user->location;
        }

        return false;
    }

    /**
     * This function downloads the GeoIP database from MaxMind.
     *
     * @param $pack
     * @param string $type
     *
     * @return mixed
     */
    public static function download($pack, $type = "enable")
    {

        // Create Empty Return Function
        $result["status"] = false;

        // Sanitize Pack name
        $pack = strtolower($pack);

        // If GeoIP is disabled, bail out.
        if ($type == "update" and Option::get(GeoIP::$library[$pack]['opt']) == '') {
            return '';
        }

        // Load Require Function
        if (!function_exists('download_url')) {
            include(ABSPATH . 'wp-admin/includes/file.php');
        }
        if (!function_exists('wp_generate_password')) {
            include(ABSPATH . 'wp-includes/pluggable.php');
        }

        // Get the upload directory from WordPress.
        $upload_dir = wp_upload_dir();

        // We need the gzopen() function
        if (false === function_exists('gzopen')) {
            if ($type == "enable") {
                Option::update(GeoIP::$library[$pack]['opt'], '');
            }

            return array_merge($result, array("notice" => __('Error the gzopen() function do not exist!', 'wp-statistics')));
        }

        // This is the location of the file to download.
        $download_url = GeoIP::$library[$pack]['source'];
        $response = wp_remote_get($download_url, array(
            'timeout' => 60,
            'sslverify' => false
        ));

        if (is_wp_error($response)) {
            \WP_Statistics::log(array('code' => 'download_geoip', 'type' => $pack, 'message' => $response->get_error_message()));
            return array_merge($result, array("notice" => $response->get_error_message()));
        }

        // Change download url if the maxmind.com doesn't response.
        if (wp_remote_retrieve_response_code($response) != '200') {
            return array_merge($result, array("notice" => sprintf(__('Error to get %s from %s', 'wp-statistics'), $pack, $download_url)));
        }

        // Create a variable with the name of the database file to download.
        $DBFile = self::get_geo_ip_path($pack);

        // Check to see if the subdirectory we're going to upload to exists, if not create it.
        if (!file_exists($upload_dir['basedir'] . '/' . WP_STATISTICS_UPLOADS_DIR)) {
            if (!@mkdir($upload_dir['basedir'] . '/' . WP_STATISTICS_UPLOADS_DIR, 0755)) {
                if ($type == "enable") {
                    Option::update(GeoIP::$library[$pack]['opt'], '');
                }

                return array_merge($result, array("notice" => sprintf(__('Error creating GeoIP database directory, make sure your web server has permissions to create directories in: %s', 'wp-statistics'), $upload_dir['basedir'])));
            }
        }

        if (!is_writable($upload_dir['basedir'] . '/' . WP_STATISTICS_UPLOADS_DIR)) {
            if ($type == "enable") {
                Option::update(GeoIP::$library[$pack]['opt'], '');
            }

            return array_merge($result, array("notice" => sprintf(__('Error setting permissions of the GeoIP database directory, make sure your web server has permissions to write to directories in : %s', 'wp-statistics'), $upload_dir['basedir'])));
        }

        // Download the file from MaxMind, this places it in a temporary location.
        $TempFile = download_url($download_url);

        // If we failed, through a message, otherwise proceed.
        if (is_wp_error($TempFile)) {
            if ($type == "enable") {
                Option::update(GeoIP::$library[$pack]['opt'], '');
            }

            return array_merge($result, array("notice" => sprintf(__('Error downloading GeoIP database from: %s - %s', 'wp-statistics'), $download_url, $TempFile->get_error_message())));
        } else {
            // Open the downloaded file to unzip it.
            $ZipHandle = gzopen($TempFile, 'rb');

            // Create th new file to unzip to.
            $DBfh = fopen($DBFile, 'wb');

            // If we failed to open the downloaded file, through an error and remove the temporary file.  Otherwise do the actual unzip.
            if (!$ZipHandle) {
                if ($type == "enable") {
                    Option::update(GeoIP::$library[$pack]['opt'], '');
                }

                unlink($TempFile);
                return array_merge($result, array("notice" => sprintf(__('Error could not open downloaded GeoIP database for reading: %s', 'wp-statistics'), $TempFile)));
            } else {
                // If we failed to open the new file, throw and error and remove the temporary file.  Otherwise actually do the unzip.
                if (!$DBfh) {
                    if ($type == "enable") {
                        Option::update(GeoIP::$library[$pack]['opt'], '');
                    }

                    unlink($TempFile);
                    return array_merge($result, array("notice" => sprintf(__('Error could not open destination GeoIP database for writing %s', 'wp-statistics'), $DBFile)));
                } else {
                    while (($data = gzread($ZipHandle, 4096)) != false) {
                        fwrite($DBfh, $data);
                    }

                    // Close the files.
                    gzclose($ZipHandle);
                    fclose($DBfh);

                    // Delete the temporary file.
                    unlink($TempFile);

                    // Display the success message.
                    $result["status"] = true;
                    $result["notice"] = __('GeoIP Database updated successfully!', 'wp-statistics');

                    // Update the options to reflect the new download.
                    if ($type == "update") {
                        Option::update('last_geoip_dl', time());
                        Option::update('update_geoip', false);
                    }

                    // Populate any missing GeoIP information if the user has selected the option.
                    if ($pack == "country") {
                        if (Option::get('geoip') && GeoIP::IsSupport() && Option::get('auto_pop')) {
                            self::Update_GeoIP_Visitor();
                        }
                    }
                }
            }
        }

        // Send Email
        if (Option::get('geoip_report') == true) {
            Helper::send_mail(Option::getEmailNotification(), __('GeoIP update on', 'wp-statistics') . ' ' . get_bloginfo('name'), $result['notice']);
        }

        return $result;
    }

    /**
     * Update All GEO-IP Visitors
     *
     * @return array
     */
    public static function Update_GeoIP_Visitor()
    {
        global $wpdb;

        // Find all rows in the table that currently don't have GeoIP info or have an unknown ('000') location.
        $result = $wpdb->get_results("SELECT id,ip FROM `" . DB::table('visitor') . "` WHERE location = '' or location = '" . GeoIP::$private_country . "' or location IS NULL");

        // Try create a new reader instance.
        $reader = false;
        if (Option::get('geoip')) {
            $reader = GeoIP::Loader('country');
        }

        if ($reader === false) {
            return array('status' => false, 'data' => __('Unable to load the GeoIP database, make sure you have downloaded it in the settings page.', 'wp-statistics'));
        }

        $count = 0;

        // Loop through all the missing rows and update them if we find a location for them.
        foreach ($result as $item) {
            $count++;

            // If the IP address is only a hash, don't bother updating the record.
            if (IP::IsHashIP($item->ip) === false and $reader != false) {
                try {
                    $record   = $reader->country($item->ip);
                    $location = $record->country->isoCode;
                    if ($location == "") {
                        $location = GeoIP::$private_country;
                    }
                } catch (\Exception $e) {
                    \WP_Statistics::log($e->getMessage());
                    $location = GeoIP::$private_country;
                }

                // Update the row in the database.
                $wpdb->update(
                    DB::table('visitor'),
                    array('location' => $location),
                    array('id' => $item->id)
                );
            }
        }

        return array('status' => true, 'data' => sprintf(__('Updated %s GeoIP records in the visitors database.', 'wp-statistics'), $count));
    }

    /**
     * if PHP modules we need for GeoIP exists.
     *
     * @return bool
     */
    public static function IsSupport()
    {
        $enabled = true;

        // PHP cURL extension installed
        if (!function_exists('curl_init')) {
            $enabled = false;
        }

        // PHP NOT running in safe mode
        if (ini_get('safe_mode')) {
            // Double check php version, 5.4 and above don't support safe mode but the ini value may still be set after an upgrade.
            if (!version_compare(phpversion(), '5.4', '<')) {
                $enabled = false;
            }
        }

        return $enabled;
    }

    /**
     * Get City Detail By User IP
     *
     * @param bool $ip
     * @param string $return
     * @return String|null
     * @throws \Exception
     * @see https://github.com/maxmind/GeoIP2-php
     */
    public static function getCity($ip = false, $return = 'name')
    {

        // Check in WordPress WP_Cache
        $user_city = wp_cache_get('city-' . $ip, 'wp-statistics');
        if ($user_city != false) {
            return $user_city;
        }

        // Check in WordPress Persist Cache
        $user_city = self::getCacheCity($ip);
        if ($user_city != false) {
            self::setCacheCity($ip, $user_city);
            return $user_city;
        }

        // Default City Name
        $default_city = __('Unknown', 'wp-statistics');

        // Get User IP
        $ip = ($ip === false ? IP::getIP() : $ip);

        // Sanitize IP
        if (IP::isIP($ip) === false) {
            return $default_city;
        }

        // Load GEO-IP
        $reader = self::Loader('city');

        //Get City name
        if ($reader != false) {

            try {
                //Search in Geo-IP
                $record = $reader->city($ip);

                //Get City
                if ($return == "all") {
                    $location = $record->city;
                } else {
                    $location = $record->city->{$return};
                }
            } catch (AddressNotFoundException $e) {
                //Don't Stuff
            } catch (InvalidDatabaseException $e) {
                //Don't Stuff
            }
        }

        # Check Has Location
        if (isset($location) and !empty($location)) {
            self::setCacheCity($ip, $location);
            return $location;
        }

        return $default_city;
    }

    /**
     * Set Cache User City
     *
     * @param $ip
     * @param $city
     * @param bool $opt
     */
    public static function setCacheCity($ip, $city, $opt = false)
    {
        if (!$opt) {
            $opt = get_option(self::$city_cache_object_name);
        }
        if (empty($opt) || !is_array($opt)) {
            $opt = array();
        }
        $opt[$ip] = array($city, current_time('timestamp'));
        wp_cache_set('city-' . $ip, $city, 'wp-statistics', DAY_IN_SECONDS);
        $opt = self::cleanCacheCity($opt);
        update_option(self::$city_cache_object_name, $opt, 'no');
    }

    /**
     * Clean Cache City
     *
     * @param bool $option
     * @param bool $save
     * @return bool
     */
    public static function cleanCacheCity($option = false, $save = false)
    {
        if (!$option) {
            $option = get_option(self::$city_cache_object_name);
        }
        if (!empty($option) and is_array($option) and count($option) > 0) {
            foreach ($option as $ip => $value) {
                if (isset($value[1])) {
                    $expire_time = (int)$value[1] + self::$library['city']['cache'];
                    if ($expire_time <= current_time('timestamp')) {
                        unset($option[$ip]);
                    }
                }
            }
        }
        if ($save) {
            update_option(self::$city_cache_object_name, $option, 'no');
        }
        return $option;
    }

    /*
     * Get City with IP From WordPress Option Cache
     */
    public static function getCacheCity($ip)
    {
        $opt = get_option(self::$city_cache_object_name);
        return (isset($opt[$ip]) ? $opt[$ip][0] : false);
    }

    /**
     * Geo IP Tools Link
     *
     * @param $ip
     * @return string
     */
    public static function geoIPTools($ip)
    {
        return "http://www.geoiptool.com/en/?IP={$ip}";
    }

}