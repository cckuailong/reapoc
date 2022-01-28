<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC WC class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_wc extends MEC_base
{
    public $ticket_names = array();

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
    }

    public function cart($event_id, $date, $other_dates, $tickets, $transaction_id = NULL)
    {
        $translated_event_id = (isset($_REQUEST['translated_event_id']) ? sanitize_text_field($_REQUEST['translated_event_id']) : 0);
        if(!trim($translated_event_id)) $translated_event_id = $event_id;

        $dates = array($date);
        if(is_array($other_dates) and count($other_dates)) $dates = array_merge($dates, $other_dates);

        $db = $this->getDB();

        // Added to cart after ticket selection
        if(!$transaction_id)
        {
            foreach($tickets as $ticket_id => $count)
            {
                if(trim($ticket_id) == '') continue;

                $ticket_key = $translated_event_id.':'.$ticket_id;

                // Get Product ID
                $product_id = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_ticket' AND `meta_value`='".$ticket_key."'", 'loadResult');

                // Create Product if Doesn't Exists
                if(!$product_id) $product_id = $this->create($translated_event_id, $ticket_id);
                // Update Existing Product
                else $this->update($product_id, $translated_event_id, $ticket_id);

                // Add to Cart
                WC()->cart->add_to_cart($product_id, ($count * max(1, count($dates))), 0, array(), array(
                    'mec_event_id' => $event_id,
                    'mec_date' => $date,
                    'mec_other_dates' => $other_dates,
                ));

                // Add to Ticket Names
                $this->ticket_names[] = $this->get_ticket_name($product_id);
            }
        }
        // Added to cart after MEC booking form
        else
        {
            foreach($tickets as $info)
            {
                $ticket_id = isset($info['id']) ? $info['id'] : '';
                if(trim($ticket_id) == '') continue;

                $ticket_key = $translated_event_id.':'.$ticket_id;

                // Get Product ID
                $product_id = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_ticket' AND `meta_value`='".$ticket_key."'", 'loadResult');

                // Create Product if Doesn't Exists
                if(!$product_id) $product_id = $this->create($translated_event_id, $ticket_id);
                // Update Existing Product
                else $this->update($product_id, $translated_event_id, $ticket_id);

                // Ticket Count
                $count = isset($info['count']) ? $info['count'] : 1;

                // Add to Cart
                WC()->cart->add_to_cart($product_id, ($count * max(1, count($dates))), 0, array(), array(
                    'mec_event_id' => $event_id,
                    'mec_date' => $date,
                    'mec_other_dates' => $other_dates,
                    'mec_transaction_id' => $transaction_id,
                ));

                // Add to Ticket Names
                $this->ticket_names[] = $this->get_ticket_name($product_id);
            }
        }

        return $this;
    }

    public function next()
    {
        // Main
        $main = $this->getMain();

        // MEC Settings
        $settings = $main->get_settings();

        // Checkout URL
        if(isset($settings['wc_after_add']) and $settings['wc_after_add'] == 'checkout') return array('type' => 'url', 'url' => wc_get_checkout_url());
        // Optional Checkout URL
        if(isset($settings['wc_after_add']) and $settings['wc_after_add'] == 'optional_cart') return array('type' => 'message', 'message' => '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert"><a href="'.esc_url(wc_get_cart_url()).'" tabindex="1" class="button wc-forward" target="_parent">'.esc_html__('View cart', 'modern-events-calendar-lite').'</a> '.esc_html(sprintf(_n('“%s” has been added to your cart.', '“%s” have been added to your cart.', count($this->ticket_names), 'modern-events-calendar-lite'), implode(', ', $this->ticket_names))).'</div></div>');
        // Optional Cart URL
        if(isset($settings['wc_after_add']) and $settings['wc_after_add'] == 'optional_chckout') return array('type' => 'message', 'message' => '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message" role="alert"><a href="'.esc_url(wc_get_checkout_url()).'" tabindex="1" class="button wc-forward" target="_parent">'.esc_html__('Checkout', 'modern-events-calendar-lite').'</a> '.esc_html(sprintf(_n('“%s” has been added to your cart.', '“%s” have been added to your cart.', count($this->ticket_names), 'modern-events-calendar-lite'), implode(', ', $this->ticket_names))).'</div></div>');
        // Cart URL
        else return array('type' => 'url', 'url' => wc_get_cart_url());
    }

    public function create($event_id, $ticket_id)
    {
        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        if(!is_array($tickets)) $tickets = array();

        $ticket = isset($tickets[$ticket_id]) ? $tickets[$ticket_id] : array();

        $product = new WC_Product();
        $product->set_name(get_the_title($event_id).': '.$ticket['name']);
        $product->set_description($ticket['description']);
        $product->set_short_description(get_the_title($event_id));
        $product->set_regular_price($ticket['price']);
        $product->set_price($ticket['price']);
        $product->set_catalog_visibility('hidden');
        $product->set_virtual(true);

        $product_id = $product->save();

        // Set the relation
        update_post_meta($product_id, 'mec_ticket', $event_id.':'.$ticket_id);

        return $product_id;
    }

    public function update($product_id, $event_id, $ticket_id)
    {
        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        if(!is_array($tickets)) $tickets = array();

        $ticket = isset($tickets[$ticket_id]) ? $tickets[$ticket_id] : array();

        $product = new WC_Product($product_id);
        $product->set_name(get_the_title($event_id).': '.$ticket['name']);
        $product->set_description($ticket['description']);
        $product->set_short_description(get_the_title($event_id));
        $product->set_regular_price($ticket['price']);
        $product->set_price($ticket['price']);
        $product->set_catalog_visibility('hidden');
        $product->set_virtual(true);

        return $product->save();
    }

    public function meta($item_id, $item)
    {
        if($item instanceof WC_Order_Item_Product)
        {
            if(isset($item->legacy_values['mec_event_id'])) wc_add_order_item_meta($item_id, 'mec_event_id', $item->legacy_values['mec_event_id']);
            if(isset($item->legacy_values['mec_date'])) wc_add_order_item_meta($item_id, 'mec_date', $item->legacy_values['mec_date']);
            if(isset($item->legacy_values['mec_other_dates'])) wc_add_order_item_meta($item_id, 'mec_other_dates', implode(',', $item->legacy_values['mec_other_dates']));
            if(isset($item->legacy_values['mec_transaction_id'])) wc_add_order_item_meta($item_id, 'mec_transaction_id', $item->legacy_values['mec_transaction_id']);
        }
    }

    public function get_event_id($order_id)
    {
        $event_id = 0;
        $order = wc_get_order($order_id);

        $items = $order->get_items();
        foreach($items as $item_id => $item)
        {
            $meta = wc_get_order_item_meta($item_id, 'mec_event_id', true);
            if(trim($meta))
            {
                $event_id = $meta;
                break;
            }
        }

        return $event_id;
    }

    public function paid($order_id)
    {
        if(!$order_id) return;

        // Main
        $main = $this->getMain();

        // MEC Settings
        $settings = $main->get_settings();

        // Auto Complete
        $autocomplete = (!isset($settings['wc_autoorder_complete']) or (isset($settings['wc_autoorder_complete']) and $settings['wc_autoorder_complete'])) ? true : false;

        // Auto Order Complete is not Enabled
        if(!$autocomplete) return;

        // It is not a MEC Order
        if(!$this->get_event_id($order_id)) return;

        $order = wc_get_order($order_id);
        $order->update_status('completed');
    }

    public function completed($order_id)
    {
        $created_booking_ids = get_post_meta($order_id, 'mec_booking_ids', true);
        if(!is_array($created_booking_ids)) $created_booking_ids = array();

        // It's already done
        if(count($created_booking_ids) == 1 and get_post($created_booking_ids[0])) return false;
        if(count($created_booking_ids) > 1) return false;

        $event_id = $this->get_event_id($order_id);

        // It is not a MEC Order
        if(!$event_id) return false;

        // WC order
        $order = wc_get_order($order_id);

        // MEC Order
        $mec = array();

        $items = $order->get_items();
        foreach($items as $item_id => $item)
        {
            $event_id = wc_get_order_item_meta($item_id, 'mec_event_id', true);
            $date = wc_get_order_item_meta($item_id, 'mec_date', true);
            $other_dates = wc_get_order_item_meta($item_id, 'mec_other_dates', true);
            $transaction_id = wc_get_order_item_meta($item_id, 'mec_transaction_id', true);

            $dates = array($date);
            if(is_array($other_dates)) $dates = array_merge($dates, $other_dates);

            if(!trim($event_id) or !trim($date)) continue;
            if(!isset($mec[$event_id])) $mec[$event_id] = array();

            $product_id = $item->get_product_id();

            $product_ids = array();
            for($i = 1; $i <= ($item->get_quantity() / count($dates)); $i++) $product_ids[] = $product_id;

            $mec[$event_id][] = array(
                'date' => $date,
                'other_dates' => $other_dates,
                'transaction_id' => $transaction_id,
                'product_ids' => $product_ids,
            );
        }

        if(!count($mec)) return false;

        // Libraries
        $main = $this->getMain();
        $book = $this->getBook();
        $gateway = new MEC_gateway_woocommerce();

        // MEC User
        $u = $this->getUser();

        // Create Bookings
        $book_ids = array();
        foreach($mec as $event_id => $bs)
        {
            foreach($bs as $b)
            {
                $transaction_id = isset($b['transaction_id']) ? $b['transaction_id'] : 0;

                $tickets = array();
                if(!$transaction_id)
                {
                    $date = $b['date'];
                    $product_ids = $b['product_ids'];

                    $other_dates = (isset($b['other_dates']) and is_array($b['other_dates'])) ? $b['other_dates'] : array();

                    $all_dates = array();
                    if(count($other_dates)) $all_dates = array_merge(array($date), $other_dates);

                    $event_tickets = get_post_meta($event_id, 'mec_tickets', true);

                    $raw_tickets = array();
                    foreach($product_ids as $product_id)
                    {
                        $key = get_post_meta($product_id, 'mec_ticket', true);
                        if(!trim($key)) continue;

                        list($e, $mec_ticket_id) = explode(':', $key);

                        if(!isset($raw_tickets[$mec_ticket_id])) $raw_tickets[$mec_ticket_id] = 1;
                        else $raw_tickets[$mec_ticket_id] += 1;

                        $ticket = array();
                        $ticket['name'] = $order->get_formatted_billing_full_name();
                        $ticket['email'] = $order->get_billing_email();
                        $ticket['id'] = $mec_ticket_id;
                        $ticket['count'] = 1;
                        $ticket['reg'] = array();
                        $ticket['variations'] = array();

                        $tickets[] = $ticket;
                    }

                    // Calculate price of bookings
                    $price_details = $book->get_price_details($raw_tickets, $event_id, $event_tickets, array(), $other_dates, false);

                    $booking = array();
                    $booking['tickets'] = $tickets;
                    $booking['first_for_all'] = 1;
                    $booking['date'] = $date;
                    $booking['all_dates'] = $all_dates;
                    $booking['other_dates'] = $other_dates;
                    $booking['event_id'] = $event_id;
                    $booking['price_details'] = $price_details;
                    $booking['total'] = $price_details['total'];
                    $booking['discount'] = 0;
                    $booking['price'] = $price_details['total'];
                    $booking['coupon'] = NULL;

                    // Save Transaction
                    $transaction_id = $book->temporary($booking);
                }

                // Transaction
                $transaction = $book->get_transaction($transaction_id);

                // Apply Coupon
                $coupons = $order->get_coupon_codes();
                if(count($coupons))
                {
                    $wc_discount = $order->get_total_discount();

                    $transaction['price_details']['details'][] = array(
                        'amount' => $wc_discount,
                        'description' => __('Discount by WC Coupon', 'modern-events-calendar-lite'),
                        'type' => 'discount',
                        'coupon' => implode(', ', $coupons)
                    );

                    $transaction['discount'] = $wc_discount;
                    $transaction['price'] = $order->get_total();
                    $transaction['coupon'] = implode(', ', $coupons);

                    $book->update_transaction($transaction_id, $transaction);
                }

                // Attendees
                $attendees = isset($transaction['tickets']) ? $transaction['tickets'] : $tickets;

                $attention_date = isset($transaction['date']) ? $transaction['date'] : '';
                $attention_times = explode(':', $attention_date);
                $date = date('Y-m-d H:i:s', trim($attention_times[0]));

                $main_attendee = isset($attendees[0]) ? $attendees[0] : array();
                $name = isset($main_attendee['name']) ? $main_attendee['name'] : '';

                $ticket_ids = '';
                $attendees_info = array();

                foreach($attendees as $i => $attendee)
                {
                    if(!is_numeric($i)) continue;

                    $ticket_ids .= $attendee['id'] . ',';
                    if(!array_key_exists($attendee['email'], $attendees_info)) $attendees_info[$attendee['email']] = array('count' => $attendee['count']);
                    else $attendees_info[$attendee['email']]['count'] = ($attendees_info[$attendee['email']]['count'] + $attendee['count']);
                }

                $ticket_ids = ',' . trim($ticket_ids, ', ') . ',';
                $user_id = $gateway->register_user($main_attendee, $transaction);

                $book_subject = $name.' - '.$u->get($user_id)->user_email;
                $book_id = $book->add(
                    array(
                        'post_author' => $user_id,
                        'post_type' => $main->get_book_post_type(),
                        'post_title' => $book_subject,
                        'post_date' => $date,
                        'attendees_info' => $attendees_info,
                        'mec_attendees' => $attendees,
                        'mec_gateway' => 'MEC_gateway_woocommerce',
                        'mec_gateway_label' => $gateway->title()
                    ),
                    $transaction_id,
                    $ticket_ids
                );

                // Assign User
                $u->assign($book_id, $user_id);

                update_post_meta($book_id, 'mec_order_id', $order_id);

                // Add WC coupon code
                if(count($coupons)) update_post_meta($book_id, 'mec_coupon_code', implode(', ', $coupons));

                $book_ids[] = $book_id;

                // Fires after completely creating a new booking
                do_action('mec_booking_completed', $book_id);
            }
        }

        update_post_meta($order_id, 'mec_booking_ids', $book_ids);

        // Redirection
        $thankyou_page_id = $main->get_thankyou_page_id($event_id);
        if($thankyou_page_id and !is_admin())
        {
            $redirect_to = $book->get_thankyou_page($thankyou_page_id, (isset($transaction_id) ? $transaction_id : NULL));

            wp_redirect($redirect_to);
            exit;
        }

        return true;
    }

    public function cancelled($order_id)
    {
        $booking_ids = get_post_meta($order_id, 'mec_booking_ids', true);
        if(!is_array($booking_ids)) $booking_ids = array();

        // No Related Bookings
        if(!count($booking_ids)) return;

        $book = $this->getBook();
        foreach($booking_ids as $booking_id)
        {
            $book->cancel($booking_id);
            $book->reject($booking_id);
        }
    }

    public function get_ticket_name($product_id)
    {
        $mec_ticket = get_post_meta($product_id, 'mec_ticket', true);
        list($event_id, $ticket_id) = explode(':', $mec_ticket);

        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        if(!is_array($tickets)) $tickets = array();

        $ticket = isset($tickets[$ticket_id]) ? $tickets[$ticket_id] : array();
        return (isset($ticket['name']) ? $ticket['name'] : '');
    }
}