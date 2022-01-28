<?php

namespace WP_STATISTICS;

class Visit
{
    /**
     * Check Active Record Visits
     *
     * @return mixed
     */
    public static function active()
    {
        return (has_filter('wp_statistics_active_visits')) ? apply_filters('wp_statistics_active_visits', true) : Option::get('visits');
    }

    /**
     * Record Users Visit in DB
     */
    public static function record()
    {
        global $wpdb;

        // Check to see if we're a returning visitor.
        $result = $wpdb->get_row("SELECT * FROM `" . DB::table('visit') . "` ORDER BY `" . DB::table('visit') . "`.`ID` DESC");

        // if we have not a Visitor in This Day then create new row or Update before row in DB
        if (is_null($result) || ($result->last_counter != TimeZone::getCurrentDate('Y-m-d'))) {
            $wpdb->query($wpdb->prepare('INSERT INTO `' . DB::table('visit') . '` (last_visit, last_counter, visit) VALUES ( %s, %s, %d) ON DUPLICATE KEY UPDATE visit = visit + ' . Visitor::getCoefficient(), TimeZone::getCurrentDate(), TimeZone::getCurrentDate('Y-m-d'), Visitor::getCoefficient()));
        } else {
            $wpdb->query('UPDATE `' . DB::table('visit') . '` SET `visit` = `visit` + ' . Visitor::getCoefficient() . ', `last_visit` = "' . TimeZone::getCurrentDate() . '" WHERE `last_counter` = "' . $result->last_counter . '"');
        }

    }

}