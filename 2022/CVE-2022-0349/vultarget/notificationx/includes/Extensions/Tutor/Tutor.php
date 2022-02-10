<?php

/**
 * Tutor Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\Tutor;

use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * Tutor Extension
 */
class Tutor extends Extension {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    public $priority        = 5;
    public $id              = 'tutor';
    public $img             = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/tutor.png';
    public $doc_link        = 'https://notificationx.com/docs/tutor-lms/';
    public $types           = 'elearning';
    public $module          = 'modules_tutor';
    public $module_priority = 7;
    public $function        = 'tutor_lms';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title        = __('Tutor', 'notificationx');
        $this->module_title = __('Tutor LMS', 'notificationx');
        parent::__construct();
    }

    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     * @return void
     */
    public function public_actions() {
        parent::public_actions();

        $monetize_by      = 'free';
        $temp_monetize_by = tutils()->get_option('monetize_by');
        if (!empty($temp_monetize_by)) {
            $monetize_by = $temp_monetize_by;
        }
        // public actions will be here
        if (class_exists('WooCommerce') && $monetize_by === 'wc') {
            add_action('woocommerce_new_order_item', array($this, 'save_new_enrollment'), 10, 3);
        }
        // public actions will be here
        if (class_exists('Easy_Digital_Downloads') && $monetize_by === 'edd') {
            add_action('edd_update_payment_status', array($this, 'save_new_enroll_payment_status'), 10, 3);
        }

        if (
            $monetize_by === 'free' ||
            (!class_exists('WooCommerce') && $monetize_by === 'wc') ||
            (!class_exists('Easy_Digital_Downloads') && $monetize_by === 'edd')
        ) {
            add_action('tutor_after_enroll', array($this, 'do_enroll'), 10, 2);
        }
    }

    /**
     * This functions is hooked
     *
     * @hooked nx_admin_action
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();
        $monetize_by = tutils()->get_option('monetize_by');
        // admin actions will be here ...
        if (class_exists('WooCommerce') && $monetize_by == 'wc') {
            add_action('woocommerce_order_status_changed', array($this, 'status_transition'), 10, 4);
        }
    }

    /**
     * Image action callback
     *
     * @param array    $image_data
     * @param array    $data
     * @param array $settings
     * @return array
     */
    // @todo Something
    public function notification_image($image_data, $data, $settings) {
        if(!$settings['show_default_image']){
            switch ($settings['show_notification_image']) {
                case 'featured_image':
                    $image_data['url'] = get_the_post_thumbnail_url($data['product_id'], 'thumbnail');
                    $image_data['alt'] = !empty($data['title']) ? $data['title'] : '';
                    break;
                case 'gravatar':
                    if(!empty($data['user_id'])){
                        $image_data['url'] = get_avatar_url($data['user_id'], array('size' => '100'));
                        $image_data['alt'] = !empty($data['name']) ? $data['name'] : '';
                    }
            }
        }
        return $image_data;
    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=tutor&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                    __( 'You have to install', 'notificationx' ),
                    $url,
                    __( 'Tutor LMS', 'notificationx' ),
                    __( 'plugin first.', 'notificationx' )
                ),
                'html' => true,
                'type' => 'error',
                'rules' => Rules::is('source', $this->id),
            ];
        }
        return $messages;
    }

    /**
     * This function is generate and save a new notification when user enroll in a new course
     *
     * @param int $user_id
     * @param int $course_id
     * @return void
     */
    public function save_new_enrollment($item_id, $item, $order_id) {
        $single_notification = $this->ordered_product($item_id, $item, $order_id);
        if (!empty($single_notification)) {
            $key = $order_id . '-' . $item_id;
            $this->update_notification(
                array(
                    'source'    => $this->id,
                    'entry_key' => $key,
                    'data'      => $single_notification,
                )
            );
            return true;
        }
        return false;
    }

    public function save_new_enroll_payment_status($payment_id, $new_status, $old_status) {
        if ($new_status !== 'publish') {
            return;
        }

        $data         = array();
        $offset       = get_option('gmt_offset');
        $payment      = new \EDD_Payment($payment_id);
        $cart_details = $payment->cart_details;
        $user_info    = $payment->user_info;

        unset($user_info['id']);
        unset($user_info['discount']);
        unset($user_info['address']);

        $user_info['name']      = $this->name($user_info['first_name'], $user_info['last_name']);
        $user_info['timestamp'] = strtotime($payment->date) - ($offset * 60 * 60);
        $user_info['ip']        = $payment->ip;

        if (is_array($cart_details)) {
            foreach ($cart_details as $cart_index => $download) {
                $if_has_course = tutor_utils()->product_belongs_with_course($download['id']);
                if ($if_has_course) {
                    $data['title']      = $download['name'];
                    $course_id          = $if_has_course->post_id;
                    $data['link']       = get_permalink($course_id);
                    $data['product_id'] = $course_id;
                    $key                = $payment->key . '-' . $download['id'];
                    $notification       = array_merge($user_info, $data);
                    return $this->update_notification(
                        array(
                            'source'    => $this->id,
                            'entry_key' => $key,
                            'data'      => $notification,
                        )
                    );
                }
            }
        }
    }

    public function do_enroll($course_id, $isEnrolled) {
        $user_id  = get_current_user_id();
        $userdata = get_userdata($user_id);
        $data     = array();
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $user_ip    = $_SERVER['REMOTE_ADDR'];
            $data['ip'] = $user_ip;
        }
        $data['first_name'] = $userdata->first_name;
        $data['last_name']  = $userdata->last_name;
        $data['name']       = $userdata->display_name;
        $data['email']      = $userdata->user_email;
        $data['title']      = get_the_title($course_id);
        $data['link']       = get_permalink($course_id);
        $data['product_id'] = $course_id;
        $data['timestamp']  = time();

        if (!empty($data)) {
            $key = $course_id . '-' . $isEnrolled;
            return $this->update_notification(
                array(
                    'source'    => $this->id,
                    'entry_key' => $key,
                    'data'      => $data,
                )
            );
        }
    }

    public function ready_enrolled_data($enrolled_data) {
        $userdata = get_userdata($enrolled_data->post_author);
        $data     = array();

        $meta_info = metadata_exists('post', $enrolled_data->ID, '_tutor_enrolled_by_order_id');
        $order_id  = 0;
        if ($meta_info == true) {
            $order_id = get_post_meta($enrolled_data->ID, '_tutor_enrolled_by_order_id', true);
        }
        $user_data = $this->user_data_by($enrolled_data->post_author, $order_id);

        $data['title']      = get_the_title($enrolled_data->post_parent);
        $data['link']       = get_permalink($enrolled_data->post_parent);
        $data['product_id'] = $enrolled_data->post_parent;
        $data['timestamp']  = get_gmt_from_date($enrolled_data->post_date);
        $data               = array_merge($data, $user_data);

        if (!empty($data)) {
            return $data;
        }
    }

    protected function user_data_by($user_id = 0, $order_id = 0) {
        $data = array();

        if ($user_id != 0 && $order_id === 0) {
            $userdata           = get_userdata($user_id);
            $data['first_name'] = $userdata->first_name;
            $data['last_name']  = $userdata->last_name;
            $data['name']       = $this->name($userdata->first_name, $userdata->last_name);

            $data['email'] = $userdata->user_email;
        }

        if ($order_id && class_exists('WC_Order')) {
            $order = wc_get_order($order_id);
            if ($order) {
                $date               = $order->get_date_created();
                $first_name         = $last_name = $email = '';
                $first_name         = $order->get_billing_first_name();
                $last_name          = $order->get_billing_last_name();
                $email              = $order->get_billing_email();
                $data['first_name'] = $first_name;
                $data['last_name']  = $last_name;
                $data['name']       = $this->name($first_name, $last_name);
                $data['email']      = $email;
                $data['timestamp']  = $date->getTimestamp();
            }
        }
        if ($order_id && function_exists('edd_get_payment_meta')) {
            $offset       = get_option('gmt_offset');
            $payment_meta = edd_get_payment_meta($order_id);
            if (is_array($payment_meta)) {
                $user_info = $payment_meta['user_info'];
                $date      = $payment_meta['date'];

                $data['name']       = $this->name($user_info['first_name'], $user_info['last_name']);
                $data['first_name'] = $user_info['first_name'];
                $data['last_name']  = $user_info['last_name'];
                $data['email']      = $user_info['email'];
                $data['timestamp']  = strtotime($date) - ($offset * 60 * 60);
            }
        }

        return $data;
    }

    public function status_transition($id, $from, $to, $order) {
        $items         = $order->get_items();
        $if_has_course = true;
        $status        = array('on-hold', 'cancelled', 'refunded', 'failed', 'pending', 'wcf-main-order');
        $done          = array('completed', 'processing');

        if (in_array($from, $done) && in_array($to, $status)) {
            foreach ($items as $item) {
                if (function_exists('tutor_utils')) {
                    $if_has_course = tutor_utils()->product_belongs_with_course($item->get_product_id());
                }
                if (!$if_has_course) {
                    continue;
                }
                $key = $id . '-' . $item->get_id();
                $this->delete_notification($key);
            }
        }

        if (in_array($from, $status) && in_array($to, $done)) {
            $orders = array();

            foreach ($items as $item) {
                $key = $id . '-' . $item->get_id();
                if (function_exists('tutor_utils')) {
                    $if_has_course = tutor_utils()->product_belongs_with_course($item->get_product_id());
                }
                if (!$if_has_course) {
                    continue;
                }

                if (isset($this->notifications[$key])) {
                    continue;
                }
                $single_notification = $this->ordered_product($item->get_id(), $item, $order);
                if (!empty($single_notification)) {
                    $this->update_notification(
                        array(
                            'source'    => $this->id,
                            'entry_key' => $key,
                            'data'      => $single_notification,
                        )
                    );
                }
            }
        }

        return;
    }

    /**
     * This function is responsible for making ready the orders data.
     *
     * @param int                   $item_id
     * @param WC_Order_Item_Product $item
     * @param int                   $order_id
     * @return void
     */
    public function ordered_product($item_id, $item, $order_id) {
        if ($item instanceof \WC_Order_Item_Shipping) {
            return false;
        }

        $product_id    = $item->get_product_id();
        $if_has_course = tutor_utils()->product_belongs_with_course($product_id);
        if (!$if_has_course) {
            return false;
        }

        $new_order = array();
        if (is_int($order_id)) {
            $order  = new \WC_Order($order_id);
            $status = $order->get_status();
            $done   = array('completed', 'processing', 'pending');
            if (!in_array($status, $done)) {
                return false;
            }
        } else {
            $order = $order_id;
        }

        $date             = $order->get_date_created();
        $countries        = new \WC_Countries();
        $shipping_country = $order->get_billing_country();
        if (empty($shipping_country)) {
            $shipping_country = $order->get_shipping_country();
        }
        if (!empty($shipping_country)) {
            $new_order['country'] = isset($countries->countries[$shipping_country]) ? $countries->countries[$shipping_country] : '';
            $shipping_state       = $order->get_shipping_state();
            if (!empty($shipping_state)) {
                $new_order['state'] = isset($countries->states[$shipping_country], $countries->states[$shipping_country][$shipping_state]) ? $countries->states[$shipping_country][$shipping_state] : $shipping_state;
            }
        }
        $new_order['city'] = $order->get_billing_city();
        if (empty($new_order['city'])) {
            $new_order['city'] = $order->get_shipping_city();
        }

        $new_order['ip'] = $order->get_customer_ip_address();
        $product_data    = $this->ready_product_data($item->get_data());
        $course_id       = $if_has_course->post_id;
        if (!empty($product_data)) {
            $new_order['id']         = is_int($order_id) ? $order_id : $order_id->get_id();
            $new_order['product_id'] = $course_id;
            $new_order['title']      = $product_data['title'];
        }
        $new_order['timestamp'] = $date->getTimestamp();
        $new_order['link']      = get_permalink($course_id);

        $data = array_merge($new_order, $this->buyer($order));
        return $data;
    }

    /**
     * It will take an array to make data clean
     *
     * @param array $data
     * @return void
     */
    protected function ready_product_data($data) {
        if (empty($data)) {
            return;
        }
        return array(
            'title' => $data['name'],
        );
    }

    /**
     * This function is responsible for getting
     * the buyer name from order.
     *
     * @param WC_Order $order
     * @return void
     */
    protected function buyer(\WC_Order $order) {
        $first_name = $last_name = $email = '';
        $buyer_data = array();

        if (empty($first_name)) {
            $first_name = $order->get_billing_first_name();
        }

        if (empty($last_name)) {
            $last_name = $order->get_billing_last_name();
        }

        if (empty($email)) {
            $email = $order->get_billing_email();
        }

        $buyer_data['first_name'] = $first_name;
        $buyer_data['last_name']  = $last_name;
        $buyer_data['name']       = $this->name($first_name, $last_name);
        $buyer_data['email']      = $email;

        return $buyer_data;
    }

    public function saved_post($post, $data, $nx_id) {
        $this->delete_notification(null, $nx_id);
        $this->get_notification_ready($data);
    }

    /**
     * This function is responsible for making the notification ready for first time we make the notification.
     *
     * @param string $type
     * @param array  $data
     * @return void
     */
    public function get_notification_ready($data = array()) {
        $enrollments = $this->get_purchased_course($data);
        if (!empty($enrollments)) {
            // $enrollments = NotificationX_Helper::sortBy( $enrollments, 'tutor' );
            $entries = [];
            foreach ($enrollments as $key => $enrollment) {
                $entries[] = array(
                        'nx_id'     => $data['nx_id'],
                        'source'    => $this->id,
                        'entry_key' => $key,
                        'data'      => $enrollment,
                    );
            }
            $this->update_notifications($entries);
        }
    }
    /**
     * Get all the orders from database using a date query
     * for limitation.
     *
     * @param array $data
     * @return array
     */
    public function get_purchased_course($data = array()) {
        if (empty($data)) {
            return null;
        }
        $orders   = array();
        $from     = date(get_option('date_format'), strtotime('-' . intval($data['display_from']) . ' days'));
        $enrolled = get_posts(
            array(
                'post_type'      => 'tutor_enrolled',
                'post_status'    => 'completed',
                'date_query'     => array(
                    array(
                        'after' => $from,
                    ),
                ),
                'numberposts'    => -1,
                'posts_per_page' => -1,
            )
        );
        foreach ($enrolled as $single_enroll) {
            $orders[$single_enroll->post_parent . '-' . $single_enroll->ID] = $this->ready_enrolled_data($single_enroll);
        }
        wp_reset_postdata();
        return $orders;
    }

    /**
     * @param array    $data
     * @param array    $saved_data
     * @param stdClass $settings
     * @return array
     */
    public function fallback_data($data, $saved_data, $settings) {
        $data['name']            = __('Someone', 'notificationx');
        $data['first_name']      = __('Someone', 'notificationx');
        $data['last_name']       = __('Someone', 'notificationx');
        $data['anonymous_title'] = __('Anonymous Product', 'notificationx');
        $data['course_title']    = $saved_data['title'];
        return $data;
    }

    public function doc(){
        return sprintf(__('<p>Make sure that you have <a href="%1$s" target="_blank">Tutor LMS installed & configured</a> to use its campaign & course selling data. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>🎦 Watch <a target="_blank" href="%3$s">video tutorial</a> to learn quickly</p>
		<p>👉 NotificationX <a target="_blank" href="%4$s">Integration with Tutor LMS</a></p>', 'notificationx'),
        'https://wordpress.org/plugins/tutor/',
        'https://notificationx.com/docs/tutor-lms/',
        'https://www.youtube.com/watch?v=EMrjLfL563Q',
        'https://notificationx.com/integrations/tutor-lms/'
        );
    }
}
