<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Booking Record class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_bookingRecord extends MEC_base
{
    /**
     * @var MEC_main
     */
    public $main;

    /**
     * @var MEC_db
     */
    public $db;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // Import MEC DB
        $this->db = $this->getDB();
    }

    /**
     * @param WP_Post|integer $booking
     * @return array
     */
    public function insert($booking)
    {
        // Get Booking by ID
        if(is_numeric($booking)) $booking = get_post($booking);

        $user_id = $booking->post_author;
        $verified = get_post_meta($booking->ID, 'mec_verified', true);
        $confirmed = get_post_meta($booking->ID, 'mec_confirmed', true);
        $event_id = get_post_meta($booking->ID, 'mec_event_id', true);
        $ticket_ids = get_post_meta($booking->ID, 'mec_ticket_id', true);
        $transaction_id = get_post_meta($booking->ID, 'mec_transaction_id', true);

        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        $all_occurrences = (isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0);

        $all_dates = get_post_meta($booking->ID, 'mec_all_dates', true);
        $timestamps = array();

        // Multiple Dates
        if($all_dates and is_array($all_dates) and count($all_dates)) $timestamps = $all_dates;
        // Single Date
        else $timestamps[] = get_post_meta($booking->ID, 'mec_date', true);

        $ids = array();
        foreach($timestamps as $timestamp)
        {
            $timestamp = explode(':', $timestamp)[0];
            if(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

            if(!trim($timestamp)) continue;

            // Exists?
            $exists = $this->db->select("SELECT `id` FROM `#__mec_bookings` WHERE `transaction_id`='".esc_sql($transaction_id)."' AND `timestamp`='".esc_sql($timestamp)."'", 'loadResult');
            if($exists) continue;

            // Insert
            $query = "INSERT INTO `#__mec_bookings` (`booking_id`,`user_id`,`transaction_id`,`event_id`,`ticket_ids`,`status`,`confirmed`,`verified`,`all_occurrences`,`date`,`timestamp`) VALUES ('".esc_sql($booking->ID)."','".esc_sql($user_id)."','".esc_sql($transaction_id)."','".esc_sql($event_id)."','".esc_sql($ticket_ids)."','".$booking->post_status."','".esc_sql($confirmed)."','".esc_sql($verified)."','".esc_sql($all_occurrences)."','".date('Y-n-d H:i:s', $timestamp)."','".esc_sql($timestamp)."');";
            $ids[] = $this->db->q($query, 'INSERT');
        }

        return $ids;
    }

    /**
     * @param WP_Post|integer $booking
     * @return array
     */
    public function update($booking)
    {
        // Delete
        $this->delete($booking);

        return $this->insert($booking);
    }

    /**
     * @param WP_Post|integer $booking
     */
    public function delete($booking)
    {
        // Get Booking by ID
        if(is_numeric($booking)) $booking = get_post($booking);

        $this->db->q("DELETE FROM `#__mec_bookings` WHERE `booking_id`='".$booking->ID."'");
    }

    public function confirm($booking)
    {
        return $this->set($booking, array('confirmed' => 1));
    }

    public function reject($booking)
    {
        return $this->set($booking, array('confirmed' => -1));
    }

    public function pending($booking)
    {
        return $this->set($booking, array('confirmed' => 0));
    }

    public function verify($booking)
    {
        return $this->set($booking, array('verified' => 1));
    }

    public function cancel($booking)
    {
        return $this->set($booking, array('verified' => -1));
    }

    public function waiting($booking)
    {
        return $this->set($booking, array('verified' => 0));
    }

    public function set($booking, $values)
    {
        // Get Booking by ID
        if(is_numeric($booking)) $booking = get_post($booking);

        $q = "";
        foreach($values as $key => $value) $q .= "`".esc_attr($key)."`='".esc_sql($value)."',";

        // Nothing to Update!
        if(trim($q) == '') return false;

        return $this->db->q("UPDATE `#__mec_bookings` SET ".trim($q, ', ')." WHERE `booking_id`='".esc_sql($booking->ID)."'");
    }
}