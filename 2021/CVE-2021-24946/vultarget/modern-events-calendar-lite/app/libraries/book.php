<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC book class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_book extends MEC_base
{
    /**
     * @var array
     */
    public $settings;
    public $main;
    public $PT;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Book Post Type Name
        $this->PT = $this->main->get_book_post_type();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    /**
     * Get invoice (Ticket price + Fees) based on tickets
     * @author Webnus <info@webnus.biz>
     * @param array $tickets
     * @param int $event_id
     * @param array $event_tickets
     * @param array $variations
     * @param array $other_dates
     * @param boolean $apply_fees
     * @return array
     */
    public function get_price_details($tickets, $event_id, $event_tickets, $variations = array(), $other_dates = array(), $apply_fees = true)
    {
        $dates_count = count($other_dates) + 1;

        $total_tickets_amount = 0;
        $total_tickets_count = 0;
        $total_variations_amount = 0;
        $total_fee_amount = 0;

        $variation_details = array();
        $fee_details = array();

        $details = array();
        for($c = 1; $c <= $dates_count; $c++)
        {
            $date_tickets_amount = 0;
            $date_variations_amount = 0;
            $date_fee_amount = 0;

            foreach($tickets as $ticket_id=>$count)
            {
                if(!$count) continue;
                if(!isset($event_tickets[$ticket_id])) continue;

                $total_tickets_count += $count;

                $t_price = (isset($event_tickets[$ticket_id]) and isset($event_tickets[$ticket_id]['price'])) ? $this->get_ticket_price($event_tickets[$ticket_id], current_time('Y-m-d'), $event_id) : 0;
                if(!is_numeric($t_price)) $t_price = 0;

                $date_tickets_amount = $date_tickets_amount+($t_price*$count);

                // Variations module is enabled and some variations bought
                if(isset($this->settings['ticket_variations_status']) and $this->settings['ticket_variations_status'] and is_array($variations) and count($variations))
                {
                    $ticket_variations = $this->main->ticket_variations($event_id, $ticket_id);

                    foreach($ticket_variations as $key=>$ticket_variation)
                    {
                        if(!is_numeric($key)) continue;
                        if(!isset($ticket_variation['title']) or (isset($ticket_variation['title']) and !trim($ticket_variation['title']))) continue;

                        $booked_variations = (isset($variations[$ticket_id]) and is_array($variations[$ticket_id])) ? $variations[$ticket_id] : array();

                        $variation_count = isset($booked_variations[$key]) ? $booked_variations[$key] : 0;
                        if(!$variation_count or ($variation_count and $variation_count < 0)) continue;

                        $variation_amount = $ticket_variation['price']*$variation_count;
                        $variation_title = $ticket_variation['title'].' ('.$variation_count.')';

                        // Add To Total
                        $date_variations_amount += $variation_amount;

                        // Price Details
                        if(!isset($variation_details[$key])) $variation_details[$key] = array('amount'=>$variation_amount, 'description'=>__($variation_title, 'modern-events-calendar-lite'), 'type'=>'variation', 'count' => $variation_count);
                        else
                        {
                            $variation_details[$key]['amount'] += $variation_amount;

                            $new_count = ((int) $variation_details[$key]['count'] + $variation_count);
                            $variation_details[$key]['count'] = $new_count;
                            $variation_details[$key]['description'] = __($ticket_variation['title'].' ('.$new_count.')', 'modern-events-calendar-lite');
                        }
                    }
                }
            }

            $total_tickets_amount += $date_tickets_amount;
            $total_variations_amount += $date_variations_amount;

            // Fees module is enabled
            if($apply_fees and isset($this->settings['taxes_fees_status']) and $this->settings['taxes_fees_status'])
            {
                $fees = $this->get_fees($event_id);

                foreach($fees as $key=>$fee)
                {
                    $fee_amount = 0;
                    if(!is_numeric($key)) continue;

                    if($fee['type'] == 'percent') $fee_amount += (($total_tickets_amount+$total_variations_amount)*$fee['amount'])/100;
                    elseif($fee['type'] == 'amount') $fee_amount += ($total_tickets_count*$fee['amount']);
                    elseif($fee['type'] == 'amount_per_booking') $fee_amount += $fee['amount'];

                    // Add to Total
                    $date_fee_amount += $fee_amount;

                    // Price Details
                    if(!isset($fee_details[$key])) $fee_details[$key] = array('amount'=>$fee_amount, 'description'=>__($fee['title'], 'modern-events-calendar-lite'), 'type'=>'fee', 'fee_type'=>$fee['type'], 'fee_amount'=>$fee['amount']);
                    else $fee_details[$key]['amount'] += $fee_amount;
                }
            }

            $total_fee_amount += $date_fee_amount;
        }

        // Ticket Details
        $details[] = array('amount'=>$total_tickets_amount, 'description'=>sprintf(__('%s Price', 'modern-events-calendar-lite'), $this->main->m('tickets', __('Tickets', 'modern-events-calendar-lite'))), 'type'=>'tickets');

        // Variation Details
        foreach($variation_details as $variation_detail) $details[] = $variation_detail;

        // Fee Details
        foreach($fee_details as $fee_detail) $details[] = $fee_detail;

        return array('total'=>($total_tickets_amount+$total_fee_amount+$total_variations_amount), 'details'=>$details);
    }

    /**
     * Get fees of a certain event
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @return array
     */
    public function get_fees($event_id)
    {
        $fees_global_inheritance = get_post_meta($event_id, 'mec_fees_global_inheritance', true);
        if(is_string($fees_global_inheritance) and trim($fees_global_inheritance) == '') $fees_global_inheritance = 1;

        // Event fees
        $fees = get_post_meta($event_id, 'mec_fees', true);

        // Get fees from global options
        if($fees_global_inheritance) $fees = isset($this->settings['fees']) ? $this->settings['fees'] : array();

        // Clean
        if(isset($fees[':i:'])) unset($fees[':i:']);

        return $fees;
    }

    /**
     * Save a temporary booking
     * @author Webnus <info@webnus.biz>
     * @param array $data
     * @return int
     */
    public function temporary($data = array())
    {
        $transaction_id = $this->get_transaction_id();
        update_option($transaction_id, $data, false);

        return $transaction_id;
    }

    /**
     * Generate a transaction id for bookings
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_transaction_id()
    {
        $method = ((isset($this->settings['booking_tid_gen_method']) and trim($this->settings['booking_tid_gen_method'])) ? $this->settings['booking_tid_gen_method'] : 'random');

        $string = str_shuffle('ABCDEFGHJKLMNOPQRSTUVWXYZ');
        $prefix = substr($string, 0, 3);

        if($method === 'ordered')
        {
            $start = ((isset($this->settings['booking_tid_start_from']) and is_numeric($this->settings['booking_tid_start_from']) and $this->settings['booking_tid_start_from'] >= 1) ? $this->settings['booking_tid_start_from'] : 10000);
            $existing = get_option('mec_tid_current', 1);

            $number = max($start, $existing)+1;

            $key = $prefix.$number;
            update_option('mec_tid_current', $number);
        }
        else
        {
            $key = $prefix.mt_rand(10000, 99999);
        }

        // If the key exist then generate another key
        if(get_option($key, false) !== false) $key = $this->get_transaction_id();

        return $key;
    }

    /**
     * Get transaction data
     * @author Webnus <info@webnus.biz>
     * @param string $transaction_id
     * @return array
     */
    public function get_transaction($transaction_id)
    {
        return get_option($transaction_id, array());
    }

    /**
     * Update a transaction
     * @author Webnus <info@webnus.biz>
     * @param string $transaction_id
     * @param array $data
     */
    public function update_transaction($transaction_id, $data)
    {
        update_option($transaction_id, $data, false);
    }

    /**
     * Add a booking
     * @author Webnus <info@webnus.biz>
     * @param array $values
     * @param string $transaction_id
     * @param int $ticket_ids
     * @return int|boolean
     */
    public function add($values, $transaction_id, $ticket_ids)
    {
        // Check Transaction State
        $db = $this->main->getDB();
        $db_transaction_ids = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key` = 'mec_transaction_id' AND `meta_value` = '{$transaction_id}'", 'loadObjectList');

        foreach($db_transaction_ids as $db_transaction_id)
        {
            $book_status = get_post_status($db_transaction_id->post_id);
            if(trim($book_status) == 'trash') unset($db_transaction_ids[$db_transaction_id->post_id]);
        }

        if(count($db_transaction_ids)) return false;

        // Transaction Data
        $transaction = $this->get_transaction($transaction_id);
        $event_id = $transaction['event_id'];

        $attention_date = isset($transaction['date']) ? $transaction['date'] : '';
        $attention_times = explode(':', $attention_date);

        // Default values
        if(!isset($values['post_date'])) $values['post_date'] = date('Y-m-d H:i:s', trim($attention_times[0]));
        if(!isset($values['post_status'])) $values['post_status'] = 'publish';

        $book_id = wp_insert_post($values);

        // Update transaction id after insert book for prevent repeat reservation books.
        update_post_meta($book_id, 'mec_transaction_id', $transaction_id);

        $transaction['booking_id'] = $book_id;
        $transaction['invoice_key'] = md5(time().mt_rand(10000, 99999));
        $this->update_transaction($transaction_id, $transaction);

        // Publish it
        wp_publish_post($book_id);

        // Assign User
        if(isset($values['post_author']) and $values['post_author'])
        {
            $u = $this->getUser();
            $u->assign($book_id, $values['post_author']);
        }

        update_post_meta($book_id, 'mec_verified', 0);
        update_post_meta($book_id, 'mec_verification_key', md5(time().mt_rand(10000, 99999)));
        update_post_meta($book_id, 'mec_cancellation_key', md5(time().mt_rand(10000, 99999)));

        update_post_meta($book_id, 'mec_confirmed', 0);

        update_post_meta($book_id, 'mec_event_id', $event_id);
        update_post_meta($book_id, 'mec_date', $transaction['date']);
        update_post_meta($book_id, 'mec_ticket_id', $ticket_ids);
        update_post_meta($book_id, 'mec_booking_time', current_time('Y-m-d H:i:s'));

        // Multiple Dates
        if(isset($transaction['all_dates']) and is_array($transaction['all_dates'])) update_post_meta($book_id, 'mec_all_dates', $transaction['all_dates']);
        if(isset($transaction['other_dates']) and is_array($transaction['other_dates'])) update_post_meta($book_id, 'mec_other_dates', $transaction['other_dates']);

        update_post_meta($book_id, 'mec_attention_time', $attention_date);
        update_post_meta($book_id, 'mec_attention_time_start', $attention_times[0]);
        update_post_meta($book_id, 'mec_attention_time_end', $attention_times[1]);

        // For Badget Bubble Notification Alert Count From It.
        update_post_meta($book_id, 'mec_book_date_submit', date('YmdHis', current_time('timestamp', 0)));

        $location_id = $this->main->get_master_location_id($event_id, $attention_times[0]);
        if(!empty($location_id)) update_post_meta($book_id, 'mec_booking_location', $location_id);

        // Event Tickets
        $tickets = get_post_meta($event_id, 'mec_tickets', true);

        if(isset($values['mec_attendees']))
        {
            foreach($values['mec_attendees'] as $k => $mec_attendee)
            {
                if(!is_numeric($k)) continue;
                $values['mec_attendees'][$k]['buyerip'] = $this->main->get_client_ip();

                $ticket_id = isset($mec_attendee['id']) ? $mec_attendee['id'] : 0;
                $ticket_price = (isset($tickets[$ticket_id]) ? $tickets[$ticket_id]['price'] : 0);

                update_post_meta($book_id, 'mec_ticket_price_'.$ticket_id, $ticket_price);
            }

            update_post_meta($book_id, 'mec_attendees', $values['mec_attendees']);
        }

        $price = isset($transaction['price']) ? $transaction['price'] : (isset($transaction['total']) ? $transaction['total'] : 0);
        update_post_meta($book_id, 'mec_price', $price);

        // A coupon applied
        if(isset($transaction['coupon']))
        {
            $coupon_id = $this->coupon_get_id($transaction['coupon']);
            if($coupon_id)
            {
                wp_set_object_terms($book_id, $coupon_id, 'mec_coupon');
                update_post_meta($book_id, 'mec_coupon_code', $transaction['coupon']);
            }
        }

        // Payment Gateway
        if(isset($values['mec_gateway']) and isset($values['mec_gateway_label']))
        {
            update_post_meta($book_id, 'mec_gateway', $values['mec_gateway']);
            update_post_meta($book_id, 'mec_gateway_label', $values['mec_gateway_label']);
        }

        // Fires after adding a new booking to send notifications etc
        do_action('mec_booking_added', $book_id);

        list($auto_verify_free, $auto_verify_paid) = $this->get_auto_verification_status($event_id);
        list($auto_confirm_free, $auto_confirm_paid) = $this->get_auto_confirmation_status($event_id);

        // Auto verification for free bookings is enabled
        if($price <= 0 and $auto_verify_free)
        {
            $this->verify($book_id);
        }

        // Auto verification for paid bookings is enabled
        if($price > 0 and $auto_verify_paid)
        {
            $this->verify($book_id);
        }

        // Auto confirmation for free bookings is enabled
        if($price <= 0 and $auto_confirm_free)
        {
            $this->confirm($book_id, 'auto');
        }

        // Auto confirmation for paid bookings is enabled
        if($price > 0 and $auto_confirm_paid)
        {
            // Work or don't work auto confirmation when pay through pay locally payment.
            $gateways_settings = get_option('mec_options', array());
            $pay_locally_gateway = ((isset($_GET['action']) and trim($_GET['action']) == 'mec_do_transaction_pay_locally') and (isset($gateways_settings['gateways'][1]['disable_auto_confirmation']) and trim($gateways_settings['gateways'][1]['disable_auto_confirmation']))) ? true : false;
            $bank_transfer_gateway = ((isset($_GET['action']) and trim($_GET['action']) == 'mec_do_transaction_bank_transfer') and (isset($gateways_settings['gateways'][8]['disable_auto_confirmation']) and trim($gateways_settings['gateways'][8]['disable_auto_confirmation']))) ? true : false;

            if(!$pay_locally_gateway and !$bank_transfer_gateway) $this->confirm($book_id, 'auto');
        }

        // Booking Record
        $this->getBookingRecord()->insert($book_id);

        return $book_id;
    }

    /**
     * Confirm a booking
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @param string $mode
     * @return boolean
     */
    public function confirm($book_id, $mode = 'manually')
    {
        update_post_meta($book_id, 'mec_confirmed', 1);

        // Fires after confirming a booking to send notifications etc.
        do_action('mec_booking_confirmed', $book_id, $mode);

        $event_id = get_post_meta($book_id, 'mec_event_id', true);
        $date = get_post_meta($book_id, 'mec_date', true);
        $timestamps = explode(':', $date);

        // Event is soldout so fire the hook
        $soldout = $this->main->is_sold($event_id, $timestamps[0]);
        if($soldout) do_action('mec_event_soldout', $event_id, $book_id);

        // Booking Records
        $this->getBookingRecord()->confirm($book_id);

        return true;
    }

    /**
     * Reject a booking
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @return boolean
     */
    public function reject($book_id)
    {
        update_post_meta($book_id, 'mec_confirmed', -1);

        // Fires after rejecting a booking to send notifications etc.
        do_action('mec_booking_rejected', $book_id);

        // Booking Records
        $this->getBookingRecord()->reject($book_id);

        return true;
    }

    /**
     * Make a booking pending
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @return boolean
     */
    public function pending($book_id)
    {
        update_post_meta($book_id, 'mec_confirmed', 0);

        // Fires after pending a booking to send notifications etc.
        do_action('mec_booking_pended', $book_id);

        // Booking Records
        $this->getBookingRecord()->pending($book_id);

        return true;
    }

    /**
     * Verify a booking
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @return boolean
     */
    public function verify($book_id)
    {
        update_post_meta($book_id, 'mec_verified', 1);

        // Fires after verifying a booking to send notifications etc.
        do_action('mec_booking_verified', $book_id);

        // Booking Records
        $this->getBookingRecord()->verify($book_id);

        return true;
    }

    /**
     * Cancel a booking
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @return boolean
     */
    public function cancel($book_id)
    {
        $verified = -1;
        $verified = apply_filters('mec_verified_value', $verified, $book_id);

        if($verified != -1) return true;

        update_post_meta($book_id, 'mec_verified', -1);
        update_post_meta($book_id, 'mec_cancelled_date', date('Y-m-d H:i:s', current_time('timestamp', 0)));

        $refund = (isset($this->settings['booking_auto_refund']) and $this->settings['booking_auto_refund']);
        $gateway = get_post_meta($book_id, 'mec_gateway', true);

        if($refund and $gateway == 'MEC_gateway_stripe')
        {
            $stripe = new MEC_gateway_stripe();
            $stripe->refund($book_id);

            // Actions
            do_action('mec_booking_refunded', $book_id);
        }

        // Fires after canceling a booking to send notifications etc.
        do_action('mec_booking_canceled', $book_id);

        // Booking Records
        $this->getBookingRecord()->cancel($book_id);

        return true;
    }

    /**
     * Waiting a booking
     * @author Webnus <info@webnus.biz>
     * @param int $book_id
     * @return boolean
     */
    public function waiting($book_id)
    {
        update_post_meta($book_id, 'mec_verified', 0);

        // Fires after waiting a booking to send notifications etc.
        do_action('mec_booking_waiting', $book_id);

        // Booking Records
        $this->getBookingRecord()->waiting($book_id);

        return true;
    }

    /**
     * Returns ticket availabilities of an event for a certain date
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @param int $timestamp
     * @param string $mode
     * @return array|integer
     */
    public function get_tickets_availability($event_id, $timestamp, $mode = 'availability')
    {
        $ex = explode(':', $timestamp);
        $timestamp = $ex[0];

        if(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

        $availability = array();
        $tickets = get_post_meta($event_id, 'mec_tickets', true);

        // No Ticket Found!
        if(!is_array($tickets) or (is_array($tickets) and !count($tickets)))
        {
            if($mode == 'reservation') return 0;
            else return $availability;
        }

        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        if(!is_array($booking_options)) $booking_options = array();

        $total_bookings_limit = (isset($booking_options['bookings_limit']) and trim($booking_options['bookings_limit'])) ? $booking_options['bookings_limit'] : 100;
        $bookings_limit_unlimited = isset($booking_options['bookings_limit_unlimited']) ? $booking_options['bookings_limit_unlimited'] : 0;
        $book_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? (int) $booking_options['bookings_all_occurrences'] : 0;

        if($bookings_limit_unlimited == '1') $total_bookings_limit = '-1';

        // Get Per Occurrence
        $total_bookings_limit = MEC_feature_occurrences::param($event_id, $timestamp, 'bookings_limit', $total_bookings_limit);

        // Total Booking Limit
        $total_bookings_limit_original = $total_bookings_limit;

        // Ticket Selling Stop
        $event_date = date('Y-m-d h:i a', $timestamp);

        if(!$book_all_occurrences) $date_query = " AND `timestamp`=".$timestamp;
        else $date_query = "";

        // Database
        $db = $this->getDB();

        $booked = 0;
        foreach($tickets as $ticket_id=>$ticket)
        {
            $limit = (isset($ticket['limit']) and trim($ticket['limit']) != '') ? $ticket['limit'] : -1;
            $records = $db->select("SELECT `id`,`ticket_ids` FROM `#__mec_bookings` WHERE `event_id`=".$event_id." AND `ticket_ids` LIKE '%,".$ticket_id.",%' AND `status` IN ('publish', 'pending', 'draft', 'future', 'private') AND `confirmed`!='-1' AND `verified`!='-1'".$date_query);

            $bookings = 0;
            foreach($records as $record)
            {
                $ticket_ids = explode(',', trim($record->ticket_ids, ', '));
                $ticket_ids_count = array_count_values($ticket_ids);

                $bookings += (isset($ticket_ids_count[$ticket_id]) and is_numeric($ticket_ids_count[$ticket_id])) ? $ticket_ids_count[$ticket_id] : 0;
            }

            if($total_bookings_limit > 0) $total_bookings_limit = max(($total_bookings_limit - $bookings), 0);
            $booked += $bookings;

            // Ticket Selling Stop
            $stop_selling_value = isset($ticket['stop_selling_value']) ? trim($ticket['stop_selling_value']) : 0;
            $stop_selling_type = isset($ticket['stop_selling_type']) ? trim($ticket['stop_selling_type']) : 'day';

            if($stop_selling_value > 0 and $this->main->check_date_time_validation('Y-m-d h:i a', strtolower($event_date)))
            {
                if(strtotime("-{$stop_selling_value}{$stop_selling_type}", strtotime($event_date)) <= current_time('timestamp', 0))
                {
                    $availability[$ticket_id] = 0;
                    $availability['stop_selling_'.$ticket_id] = true;
                    continue;
                }
            }

            if((isset($ticket['unlimited']) and $ticket['unlimited'] == 1) or $limit == -1)
            {
                $availability[$ticket_id] = ($total_bookings_limit > 0) ? $total_bookings_limit : -1;
                continue;
            }

            if($limit == '') $limit = 0;

            // Unlimited Total
            if($total_bookings_limit == '-1') $ticket_availability = $limit-$bookings;
            else $ticket_availability = min(($limit-$bookings), max($total_bookings_limit, 0));

            $availability[$ticket_id] = $ticket_availability >= 0 ? $ticket_availability : 0;
        }

        // For the time being set reservation parameter
        if($mode == 'reservation') return $booked;

        // Set Total Booking Limit
        $availability['total'] = $total_bookings_limit;

        // Do not send higher limit for tickets compared to total limit
        if($total_bookings_limit != '-1' and $total_bookings_limit > 0)
        {
            $new_availability = array();
            foreach($availability as $ticket_id=>$limit)
            {
                if(is_numeric($ticket_id)) $new_availability[$ticket_id] = min($limit, $total_bookings_limit);
                else $new_availability[$ticket_id] = $limit;
            }

            return $new_availability;
        }

        // Total Booking Limit Reached
        if($total_bookings_limit_original != -1 and $booked >= $total_bookings_limit_original)
        {
            $new_availability = array();
            foreach($availability as $ticket_id=>$limit)
            {
                if(is_numeric($ticket_id)) $new_availability[$ticket_id] = 0;
                else $new_availability[$ticket_id] = $limit;
            }

            return $new_availability;
        }

        return $availability;
    }

    /**
     * Returns ticket availabilities of an event for a certain date
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @param array $dates
     * @return array
     */
    public function get_tickets_availability_multiple($event_id, $dates)
    {
        $availability = array();
        foreach($dates as $date)
        {
            $ex = explode(':', $date);
            $date = $ex[0];

            $a = $this->get_tickets_availability($event_id, $date);
            if(!is_array($a)) continue;

            // Fill Compatibility
            if(!count($availability)) $availability = $a;

            // Minimum Availability
            foreach($availability as $k => $v)
            {
                if(isset($a[$k])) $availability[$k] = min($a[$k], $v);
            }
        }

        return $availability;
    }

    /**
     * Check validity of a coupon
     * @author Webnus <info@webnus.biz>
     * @param string $coupon
     * @param int $event_id
     * @param array $transaction
     * @return int
     */
    public function coupon_check_validity($coupon, $event_id, $transaction)
    {
        $term = get_term_by('name', $coupon, 'mec_coupon');
        $coupon_id = isset($term->term_id) ? $term->term_id : 0;

        // Coupon is not exists
        if(!$coupon_id) return 0;

        // Usage Limit
        $usage_limit = get_term_meta($coupon_id, 'usage_limit', true);
        $status = ($usage_limit == '-1' or (int) $usage_limit > $term->count) ? 1 : -1;

        // Expiration Date
        if($status === 1)
        {
            $expiration_date = get_term_meta($coupon_id, 'expiration_date', true);
            if(trim($expiration_date) and strtotime($expiration_date) < strtotime(date('Y-m-d')))
            {
                $status = -2;
            }
        }

        // Event Specification
        if($status === 1)
        {
            $all_events = get_term_meta($coupon_id, 'target_event', true);
            if(trim($all_events) == '') $all_events = 1;

            $target_events = get_term_meta($coupon_id, 'target_events', true);
            if(!is_array($target_events))
            {
                $target_events = array();
                if($all_events and $all_events != 1) $target_events[] = $all_events;
            }

            if(!$all_events and is_array($target_events) and count($target_events) and !in_array($event_id, $target_events))
            {
                $status = -3;
            }
        }

        // Category Specification
        if($status === 1)
        {
            $all_target_categories = get_term_meta($coupon_id, 'target_category', true);
            if(trim($all_target_categories) == '') $all_target_categories = 1;

            $target_categories = get_term_meta($coupon_id, 'target_categories', true);
            if(!$all_target_categories and is_array($target_categories) and count($target_categories))
            {
                $event_categories = wp_get_post_terms($event_id, 'mec_category', array('fields' => 'ids'));

                $found = false;
                foreach($target_categories as $target_category)
                {
                    if(in_array($target_category, $event_categories))
                    {
                        $found = true;
                        break;
                    }
                }

                if(!$found) $status = -6;
            }
        }

        // Minimum Tickets
        if($status === 1)
        {
            $ticket_minimum = get_term_meta($coupon_id, 'ticket_minimum', true);
            if(!trim($ticket_minimum)) $ticket_minimum = 1;

            $tickets = isset($transaction['tickets']) ? $transaction['tickets'] : array();
            if(isset($tickets['attachments'])) unset($tickets['attachments']);

            if(count($tickets) < $ticket_minimum)
            {
                $status = -4;
            }
        }

        // Maximum Tickets
        if($status === 1)
        {
            $ticket_maximum = get_term_meta($coupon_id, 'ticket_maximum', true);
            if(trim($ticket_maximum))
            {
                $tickets = isset($transaction['tickets']) ? $transaction['tickets'] : array();
                if(isset($tickets['attachments'])) unset($tickets['attachments']);

                if(count($tickets) > $ticket_maximum)
                {
                    $status = -5;
                }
            }
        }

        return $status;
    }

    /**
     * Apply a coupon to a transaction
     * @author Webnus <info@webnus.biz>
     * @param string $coupon
     * @param int $transaction_id
     * @return int
     */
    public function coupon_apply($coupon, $transaction_id)
    {
        $transaction = $this->get_transaction($transaction_id);
        $event_id = isset($transaction['event_id']) ? $transaction['event_id'] : NULL;

        // Verify validity of coupon
        if($this->coupon_check_validity($coupon, $event_id, $transaction) != 1) return 0;

        $total = $transaction['total'];
        $discount = $this->coupon_get_discount($coupon, $total);

        $after_discount = $total - $discount;

        $transaction['price_details']['total'] = $after_discount;

        $price_details = $transaction['price_details']['details'];
        foreach($price_details as $i => $price_detail)
        {
            if(isset($price_detail['type']) and $price_detail['type'] == 'discount' and isset($price_detail['coupon'])) unset($price_details[$i]);
        }

        $price_details[] = array('amount'=>$discount, 'description'=>__('Discount', 'modern-events-calendar-lite'), 'type'=>'discount', 'coupon'=>$coupon);

        $transaction['price_details']['details'] = $price_details;
        $transaction['discount'] = $discount;
        $transaction['price'] = $after_discount;
        $transaction['coupon'] = $coupon;

        // Re-caclculate
        $transaction = $this->recalculate($transaction);

        // Update Transaction
        $this->update_transaction($transaction_id, $transaction);

        return (isset($transaction['discount']) ? $transaction['discount'] : $discount);
    }

    /**
     * Get discount of a coupon
     * @author Webnus <info@webnus.biz>
     * @param string $coupon
     * @param int $total
     * @return int
     */
    public function coupon_get_discount($coupon, $total)
    {
        $term = get_term_by('name', $coupon, 'mec_coupon');
        $coupon_id = isset($term->term_id) ? $term->term_id : 0;

        // Coupon is not exists
        if(!$coupon_id) return 0;

        $discount_type = get_term_meta($coupon_id, 'discount_type', true);
        $discount = get_term_meta($coupon_id, 'discount', true);

        if($discount_type == 'percent')
        {
            $discount_amount = ($total*$discount)/100;

            $discount_max = get_term_meta($coupon_id, 'maximum_discount', true);
            if(trim($discount_max) and is_numeric($discount_max)) $discount_amount = min($discount_amount, $discount_max);
        }
        else $discount_amount = min($discount, $total);

        return $discount_amount;
    }

    /**
     * Get id of a coupon by coupon number
     * @author Webnus <info@webnus.biz>
     * @param string $coupon
     * @return int
     */
    public function coupon_get_id($coupon)
    {
        $term = get_term_by('name', $coupon, 'mec_coupon');
        return isset($term->term_id) ? $term->term_id : 0;
    }

    public function recalculate($transaction)
    {
        $price_details = $transaction['price_details']['details'];
        $total_tickets_count = count($transaction['tickets']);

        $total_fee_amount = 0;
        $taxable = 0;
        $total_discount = 0;
        $fees_to_apply = array();
        $discounts_to_apply = array();

        foreach($price_details as $i => $item)
        {
            $type = isset($item['type']) ? $item['type'] : '';
            $amount = isset($item['amount']) ? $item['amount'] : 0;

            if($type == 'fee') $fees_to_apply[] = $item;
            elseif($type == 'discount') $discounts_to_apply[] = $item;
            else $taxable += $amount;

            // Remove Fee and Discount Items
            if(in_array($type, array('fee', 'discount'))) unset($price_details[$i]);
        }

        $total = $taxable;

        // Apply Discounts
        foreach($discounts_to_apply as $discount_item)
        {
            $discount = $this->coupon_get_discount($discount_item['coupon'], $taxable);
            $taxable = max(0, ($taxable - $discount));
            $total_discount += $discount;

            $price_details[] = array('amount'=>$discount, 'description'=>__('Discount', 'modern-events-calendar-lite'), 'type'=>'discount', 'coupon'=>$discount_item['coupon']);
        }

        // Apply Fees
        foreach($fees_to_apply as $fee_item)
        {
            $fee_amount = 0;

            if($fee_item['fee_type'] == 'percent') $fee_amount += ($taxable*$fee_item['fee_amount'])/100;
            elseif($fee_item['fee_type'] == 'amount') $fee_amount += ($total_tickets_count*$fee_item['fee_amount']);
            elseif($fee_item['fee_type'] == 'amount_per_booking') $fee_amount += $fee_item['fee_amount'];

            $total_fee_amount += $fee_amount;
            $price_details[] = array('amount'=>$fee_amount, 'description'=>__($fee_item['description'], 'modern-events-calendar-lite'), 'type'=>'fee', 'fee_type'=>$fee_item['fee_type'], 'fee_amount'=>$fee_item['fee_amount']);
        }

        $total += $total_fee_amount;
        $payable = ($taxable + $total_fee_amount);

        $transaction['price_details']['total'] = $payable;
        $transaction['price_details']['details'] = $price_details;
        $transaction['discount'] = $total_discount;
        $transaction['price'] = $payable;
        $transaction['total'] = $total;

        return $transaction;
    }

    /**
     * Get invoice link for certain transaction
     * @author Webnus <info@webnus.biz>
     * @param $transaction_id
     * @return string
     */
    public function get_invoice_link($transaction_id)
    {
        if(isset($this->settings['booking_invoice']) and !$this->settings['booking_invoice']) return '';

        $main = $this->getMain();

        $url = $main->URL('site');
        $url = $main->add_qs_var('method', 'mec-invoice', $url);

        // Invoice Key
        $transaction = $this->get_transaction($transaction_id);
        if(isset($transaction['invoice_key'])) $url = $main->add_qs_var('mec-key', $transaction['invoice_key'], $url);

        return apply_filters('mec_booking_invoice_url', $main->add_qs_var('id', $transaction_id, $url), $transaction_id);
    }

    /**
     * Get Downloadable file link for certain transaction
     * @author Webnus <info@webnus.biz>
     * @param $book_id
     * @return string
     */
    public function get_dl_file_link($book_id)
    {
        if(!isset($this->settings['downloadable_file_status']) or (isset($this->settings['downloadable_file_status']) and !$this->settings['downloadable_file_status'])) return '';

        $event_id = get_post_meta($book_id, 'mec_event_id', true);
        $dl_file_id = get_post_meta($event_id, 'mec_dl_file', true);

        return apply_filters('mec_booking_dl_file_url', ($dl_file_id ? wp_get_attachment_url($dl_file_id) : ''), $book_id);
    }

    public function get_bookings_by_transaction_id($transaction_id)
    {
        $main = $this->getMain();

        return get_posts(array(
            'posts_per_page' => -1,
            'post_type' => $main->get_book_post_type(),
            'meta_key' => 'mec_transaction_id',
            'meta_value' => $transaction_id,
        ));
    }

    public function get_thankyou_page($page_id, $transaction_id)
    {
        $main = $this->getMain();
        $page = get_permalink($page_id);

        return ($transaction_id ? $main->add_qs_var('transaction', $transaction_id, $page) : $page);
    }

    public function invoice_link_shortcode()
    {
        $transaction = isset($_GET['transaction']) ? sanitize_text_field($_GET['transaction']) : NULL;
        if(!$transaction) return NULL;

        $book = $this->getBook();
        return '<a href="'.$book->get_invoice_link($transaction).'" target="_blank">'.__('Download Invoice', 'modern-events-calendar-lite').'</a>';
    }

    public function get_total_attendees($book_id)
    {
        $attendees = get_post_meta($book_id, 'mec_attendees', true);
        $count = 0;

        if(is_array($attendees))
        {
            foreach($attendees as $key => $attendee)
            {
                if($key === 'attachments') continue;

                if(!isset($attendee[0]['MEC_TYPE_OF_DATA'])) $count++;
                elseif($attendee[0]['MEC_TYPE_OF_DATA'] != 'attachment') $count++;
            }
        }

        return $count;
    }

    public function get_attendees($book_id)
    {
        $attendees = get_post_meta($book_id, 'mec_attendees', true);
        $clean = array();

        if(is_array($attendees))
        {
            foreach($attendees as $key => $attendee)
            {
                if($key === 'attachments') continue;

                $clean[$key] = $attendee;
            }
        }

        return $clean;
    }

    public function get_transaction_id_book_id($book_id)
    {
        return get_post_meta($book_id, 'mec_transaction_id', true);
    }

    public function get_book_id_transaction_id($transaction_id)
    {
        $db = $this->getDB();
        return $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_transaction_id' AND `meta_value`='".$db->escape($transaction_id)."'", 'loadResult');
    }

    public function get_ticket_price_label($ticket, $date, $event_id)
    {
        return $this->get_ticket_price_key($ticket, $date, $event_id, 'price_label');
    }

    public function get_ticket_price($ticket, $date, $event_id)
    {
        return $this->get_ticket_price_key($ticket, $date, $event_id, 'price');
    }

    public function get_ticket_price_key($ticket, $date, $event_id, $key)
    {
        $data = isset($ticket[$key]) ? $ticket[$key] : NULL;

        $price_dates = (isset($ticket['dates']) and is_array($ticket['dates'])) ? $ticket['dates'] : array();
        if(!count($price_dates)) return $this->get_price_for_loggedin_users($event_id, $data, $key);

        $time = strtotime($date);
        foreach($price_dates as $k => $price_date)
        {
            if(!is_numeric($k)) continue;

            $start = $price_date['start'];
            $end = $price_date['end'];

            if($time >= strtotime($start) and $time <= strtotime($end))
            {
                if($key == 'price_label') $data = $price_date['label'];
                else $data = $price_date[$key];
            }
        }

        return $this->get_price_for_loggedin_users($event_id, $data, $key);
    }

    /**
     * Returns tickets prices of an event for a certain date
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @param string $date
     * @param string $key
     * @return array
     */
    public function get_tickets_prices($event_id, $date, $key = 'price')
    {
        $prices = array();
        $tickets = get_post_meta($event_id, 'mec_tickets', true);

        // No Ticket Found!
        if(!is_array($tickets) or (is_array($tickets) and !count($tickets))) return $prices;

        foreach($tickets as $ticket_id=>$ticket) $prices[$ticket_id] = $this->get_ticket_price_key($ticket, $date, $event_id, $key);
        return $prices;
    }

    public function get_price_for_loggedin_users($event_id, $price, $type = 'price')
    {
        if(!is_user_logged_in()) return $price;

        $user_id = get_current_user_id();
        return $this->get_price_for_users($event_id, $price, $user_id, $type);
    }

    public function get_price_for_users($event_id, $price, $user_id, $type = 'price')
    {
        // Guest User
        if(!$user_id) return $price;

        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        if(!is_array($booking_options)) $booking_options = array();

        // User
        $user = get_user_by('id', $user_id);

        // Invalid User ID
        if(!$user or ($user and !isset($user->roles))) return $price;

        $roles = (array) $user->roles;

        $loggedin_discount = (isset($booking_options['loggedin_discount']) ? $booking_options['loggedin_discount'] : 0);
        $role_discount = $loggedin_discount;

        // Step through all roles in Array that comes from WordPress Core
        foreach($roles as $key => $role)
        {
            // If role discount is higher than the preset role OR a previous roles discount, set it to the new higher discount
            if(isset($booking_options['roles_discount_'.$role]) and is_numeric($booking_options['roles_discount_'.$role]) and $booking_options['roles_discount_'.$role] > $role_discount) $role_discount = $booking_options['roles_discount_'.$role];
        }

        if(trim($role_discount) and is_numeric($role_discount))
        {
            if($type === 'price_label' and !is_numeric($price))
            {
                $numeric = preg_replace("/[^0-9.]/", '', $price);
                if(is_numeric($numeric)) $price = $this->main->render_price(($numeric - (($numeric * $role_discount) / 100)), $event_id);
            }
            else
            {
                $price = $price - (($price * $role_discount) / 100);
            }
        }

        return $price;
    }

    public function get_user_booking_limit($event_id)
    {
        $unlimited = false;
        $limit = 12;
        $mec_settings = $this->main->get_settings();
        $booking_options = get_post_meta($event_id, 'mec_booking', true);

        // Total user booking limited
        if(isset($booking_options['bookings_user_limit_unlimited']) and !trim($booking_options['bookings_user_limit_unlimited']))
        {
            $limit = (isset($booking_options['bookings_user_limit']) and trim($booking_options['bookings_user_limit'])) ? trim($booking_options['bookings_user_limit']) : $limit;
        }
        else
        {
            // If Inherit from global options activate
            if(!isset($mec_settings['booking_limit']) or (isset($mec_settings['booking_limit']) and !trim($mec_settings['booking_limit']))) $unlimited = true;
            else $limit = trim($mec_settings['booking_limit']);
        }

        return array($limit, $unlimited);
    }

    public function timestamp($start, $end)
    {
        // Timestamp is already available
        if(isset($start['timestamp']) and isset($end['timestamp']))
        {
            return $start['timestamp'].':'.$end['timestamp'];
        }

        $s_hour = $start['hour'];
        if(strtoupper($start['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

        $e_hour = $end['hour'];
        if(strtoupper($end['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

        $start_time = $start['date'].' '.sprintf("%02d", $s_hour).':'.sprintf("%02d", $start['minutes']).' '.$start['ampm'];
        $end_time = $end['date'].' '.sprintf("%02d", $e_hour).':'.sprintf("%02d", $end['minutes']).' '.$end['ampm'];

        return strtotime($start_time).':'.strtotime($end_time);
    }

    public function get_event_id_by_transaction_id($transaction_id)
    {
        $transaction = $this->get_transaction($transaction_id);
        return (isset($transaction['event_id']) ? $transaction['event_id'] : 0);
    }

    public function get_attendee_price($transaction, $email)
    {
        if(!is_array($transaction)) $transaction = $this->get_transaction($transaction);

        // No Attendees found!
        if(!isset($transaction['tickets']) or (isset($transaction['tickets']) and !is_array($transaction['tickets']))) return false;

        $attendee = array();
        foreach($transaction['tickets'] as $key => $ticket)
        {
            if(!is_numeric($key)) continue;

            if($ticket['email'] == $email)
            {
                $attendee = $ticket;
                break;
            }
        }

        // Attendee not found
        if(!count($attendee)) return false;

        $event_id = isset($transaction['event_id']) ? $transaction['event_id'] : 0;
        if(!$event_id) return false;

        $tickets = get_post_meta($event_id, 'mec_tickets', true);

        $dates = explode(':', $transaction['date']);

        $ticket_price = isset($tickets[$attendee['id']]) ? $this->get_ticket_price($tickets[$attendee['id']], $dates[0], $event_id) : 0;
        if(!$ticket_price) return false;

        $variation_price = 0;

        // Ticket Variations
        if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
        {
            $ticket_variations = $this->main->ticket_variations($event_id, $attendee['id']);
            foreach($attendee['variations'] as $variation_id=>$variation_count)
            {
                if(!$variation_count or ($variation_count and $variation_count < 0)) continue;

                $variation_price += isset($ticket_variations[$variation_id]['price']) ? $ticket_variations[$variation_id]['price'] : 0;
            }
        }

        return ($ticket_price+$variation_price);
    }

    public function get_auto_verification_status($event_id)
    {
        // Booking Options
        $BO = get_post_meta($event_id, 'mec_booking', true);
        if(!is_array($BO)) $BO = array();

        $event_auto_verify = (isset($BO['auto_verify']) and trim($BO['auto_verify']) != '') ? $BO['auto_verify'] : 'global';
        if(is_numeric($event_auto_verify)) $event_auto_verify = (int) $event_auto_verify;

        if($event_auto_verify == 'global')
        {
            $auto_verify_free = (isset($this->settings['booking_auto_verify_free']) ? $this->settings['booking_auto_verify_free'] : 0);
            $auto_verify_paid = (isset($this->settings['booking_auto_verify_paid']) ? $this->settings['booking_auto_verify_paid'] : 0);
        }
        else
        {
            $auto_verify_free = $event_auto_verify;
            $auto_verify_paid = $event_auto_verify;
        }

        return array($auto_verify_free, $auto_verify_paid);
    }

    public function get_auto_confirmation_status($event_id)
    {
        // Booking Options
        $BO = get_post_meta($event_id, 'mec_booking', true);
        if(!is_array($BO)) $BO = array();

        $event_auto_confirm = (isset($BO['auto_confirm']) and trim($BO['auto_confirm']) != '') ? $BO['auto_confirm'] : 'global';
        if(is_numeric($event_auto_confirm)) $event_auto_confirm = (int) $event_auto_confirm;

        if($event_auto_confirm == 'global')
        {
            $auto_confirm_free = (isset($this->settings['booking_auto_confirm_free']) ? $this->settings['booking_auto_confirm_free'] : 0);
            $auto_confirm_paid = (isset($this->settings['booking_auto_confirm_paid']) ? $this->settings['booking_auto_confirm_paid'] : 0);
        }
        else
        {
            $auto_confirm_free = $event_auto_confirm;
            $auto_confirm_paid = $event_auto_confirm;
        }

        return array($auto_confirm_free, $auto_confirm_paid);
    }

    public function get_all_sold_tickets($event_id)
    {
        $query = new WP_Query(array(
            'post_type' => $this->PT,
            'posts_per_page' => -1,
            'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
            'meta_query' => array
            (
                array('key'=>'mec_event_id', 'value'=>$event_id, 'compare'=>'='),
            )
        ));

        $sold = 0;
        if($query->have_posts())
        {
            // The Loop
            while($query->have_posts())
            {
                $query->the_post();
                $sold += $this->get_total_attendees(get_the_ID());
            }

            // Restore original Post Data
            wp_reset_postdata();
        }

        return $sold;
    }

    public function get_ticket_total_price($transaction, $attendee, $booking_id)
    {
        $event_id = $transaction['event_id'];

        $all_attendees = get_post_meta($booking_id, 'mec_attendees', true);
        if(!is_array($all_attendees) or (is_array($all_attendees) and !count($all_attendees))) $all_attendees = array(get_post_meta($booking_id, 'mec_attendee', true));

        if(isset($all_attendees['attachments'])) unset($all_attendees['attachments']);

        $total_price = get_post_meta($booking_id, 'mec_price', true);
        if(count($all_attendees) == 1) return $total_price;

        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        $ticket_id = $attendee['id'];

        $ticket_variations = $this->main->ticket_variations($event_id, $ticket_id);

        $ticket_price_booking_saved = get_post_meta($booking_id, 'mec_ticket_price_'.$ticket_id, true);
        if(trim($ticket_price_booking_saved) === '') $ticket_price_booking_saved = 0;

        $ticket_price = (isset($tickets[$ticket_id]) ? $tickets[$ticket_id]['price'] : $ticket_price_booking_saved);

        $user = $this->getUser();
        $booking_user = $user->booking($booking_id);

        $ticket_price = $this->get_price_for_users($event_id, $ticket_price, $booking_user->ID, 'price');

        // Price Per Date
        if(isset($tickets[$ticket_id]['dates']) and is_array($tickets[$ticket_id]['dates']) and count($tickets[$ticket_id]['dates']))
        {
            $book_time = strtotime(get_post_meta($booking_id, 'mec_booking_time', true));
            if($book_time)
            {
                $pdates = $tickets[$ticket_id]['dates'];
                foreach($pdates as $pdate)
                {
                    if(!isset($pdate['start']) or !isset($pdate['end'])) continue;

                    $t_start = strtotime($pdate['start']);
                    $t_end = strtotime($pdate['end']);

                    if($book_time >= $t_start and $book_time <= $t_end and isset($pdate['price']))
                    {
                        $ticket_price = $pdate['price'];
                        break;
                    }
                }
            }
        }

        $variation_price = 0;
        if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
        {
            foreach($attendee['variations'] as $variation_id => $count)
            {
                if(!trim($count)) continue;
                if(!isset($ticket_variations[$variation_id])) continue;

                $p = $ticket_variations[$variation_id]['price'];
                if(is_numeric($p) and is_numeric($count)) $variation_price += ($p * $count);
            }
        }

        // Fees
        $total_fees = 0;

        // Discounts
        $discounts = 0;

        if(isset($transaction['price_details']) and isset($transaction['price_details']['details']) and is_array($transaction['price_details']['details']) and count($transaction['price_details']['details']))
        {
            foreach($transaction['price_details']['details'] as $detail)
            {
                if(!isset($detail['type'])) continue;

                if($detail['type'] == 'fee' and isset($detail['amount']) and is_numeric($detail['amount'])) $total_fees += $detail['amount'];
                if($detail['type'] == 'discount' and isset($detail['amount']) and is_numeric($detail['amount'])) $discounts += $detail['amount'];
            }
        }

        $ticket_total_price = NULL;
        if(is_numeric($ticket_price) and is_numeric($variation_price) and is_numeric($total_fees) and is_numeric($discounts)){

            $ticket_total_price = ($ticket_price + $variation_price + ($total_fees / count($all_attendees))) - ($discounts / count($all_attendees));
        }

        return (!is_null($ticket_total_price) ? $ticket_total_price : $total_price);
    }

    /**
     * Remove Fees From a Transaction
     * @author Webnus <info@webnus.biz>
     * @param int $transaction_id
     * @return boolean
     */
    public function remove_fees($transaction_id)
    {
        $transaction = $this->get_transaction($transaction_id);
        $price_details = $transaction['price_details']['details'];

        $removed_fees = array();
        foreach($price_details as $i => $price_detail)
        {
            if(isset($price_detail['type']) and $price_detail['type'] == 'fee')
            {
                $removed_fees[] = $price_detail;
                unset($price_details[$i]);
            }
        }

        $transaction['price_details']['details'] = $price_details;
        $transaction['removed_fees_status'] = 1;
        $transaction['removed_fees'] = (count($removed_fees) ? $removed_fees : (isset($transaction['removed_fees']) ? $transaction['removed_fees'] : array()));

        // Re-caclculate
        $transaction = $this->recalculate($transaction);

        // Update Transaction
        $this->update_transaction($transaction_id, $transaction);

        return true;
    }

    /**
     * Re-Add Fees To a Transaction
     * @author Webnus <info@webnus.biz>
     * @param int $transaction_id
     * @return boolean
     */
    public function readd_fees($transaction_id)
    {
        $transaction = $this->get_transaction($transaction_id);

        $is_removed = (isset($transaction['removed_fees_status']) ? $transaction['removed_fees_status'] : 0);
        if(!$is_removed) return false;

        $price_details = $transaction['price_details']['details'];

        $removed_fees = (isset($transaction['removed_fees']) and is_array($transaction['removed_fees'])) ? $transaction['removed_fees'] : array();
        foreach($removed_fees as $removed_fee) $price_details[] = $removed_fee;

        $transaction['price_details']['details'] = $price_details;

        unset($transaction['removed_fees_status']);
        unset($transaction['removed_fees']);

        // Re-caclculate
        $transaction = $this->recalculate($transaction);

        // Update Transaction
        $this->update_transaction($transaction_id, $transaction);

        return true;
    }
}