<?php

/**
 * EDD Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\EDD;

use NotificationX\Core\Database;
use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * EDD Extension
 */
class EDD extends Extension {
    /**
     * Instance of EDD
     *
     * @var EDD
     */
    use GetInstance;

    public $priority        = 10;
    public $id              = 'edd';
    public $img             = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/edd.png';
    public $doc_link        = 'https://notificationx.com/docs/notificationx-easy-digital-downloads/';
    public $types           = 'conversions';
    public $module          = 'modules_edd';
    public $module_priority = 5;
    public $class           = 'Easy_Digital_Downloads';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title        = __( 'Easy Digital Downloads', 'notificationx' );
        $this->module_title = __( 'Easy Digital Downloads', 'notificationx' );
        parent::__construct();
    }

    /**
     * Called from Extension::_construct if module is active.
     * Not hook to ini action.
     *
     * @return void
     */
    public function init() {
        parent::init();

        add_action( 'edd_update_payment_status', array( $this, 'update_payment_status' ), 10, 3 );
    }

    public function init_fields(){
        parent::init_fields();
        add_filter('nx_link_types', [$this, 'link_types']);
    }

    /**
     * This functions is hooked
     *
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();

    }

    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     * @return void
     */
    public function public_actions() {
        parent::public_actions();
        add_filter("nx_filtered_data_{$this->id}", array($this, 'multiorder_combine'), 11, 3);
    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=easy-digital-downloads&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                    __( 'You have to install', 'notificationx' ),
                    $url,
                    __( 'Easy Digital Downloads', 'notificationx' ),
                    __( 'plugin first.', 'notificationx' )
                ),
                'html' => true,
                'type' => 'error',
                'rules' => Rules::is('source', $this->id),
            ];
        }
        return $messages;
    }


    /* #region  backend */


    public function multiorder_combine($data, $settings) {
        $should_combine = apply_filters('nx_should_combine', true, $data, $settings);
        if (!$should_combine || empty($settings['combine_multiorder']) || $settings['combine_multiorder'] != '1') {
            return $data;
        }
        $items = [];
        $item_counts = [];
        foreach ($data as $key => $item) {
            $payment_id = $item['id'];
            if (!isset($items[$payment_id])) {
                $items[$payment_id] = $item;
            } else {
                $item_counts[$payment_id] = isset($item_counts[$payment_id]) ? ++$item_counts[$payment_id] : 1;
            }
        }

        $products_more_title = isset($settings['combine_multiorder_text']) && !empty($settings['combine_multiorder_text']) ? __($settings['combine_multiorder_text'], 'notificationx') : __('more products', 'notificationx');
        foreach ($item_counts as $key => $item) {
            $items[$key]['title'] = $items[$key]['title'] . ' & ' . $item . ' ' . $products_more_title;
        }

        // @todo maybe sort
        return $items;
    }

    /**
     * Adds option to Link Type field in Content tab.
     *
     * @param array $options
     * @return array
     */
    public function link_types( $options ) {
        $options = GlobalFields::get_instance()->normalize_fields(
            array(
                'product_image' => __( 'Product Page', 'notificationx' ),
            ), 'source', $this->id, $options
        );

        $this->has_link_types = true;
        return $options;
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
    public function get_notification_ready( $post = array() ) {
        $orders = $this->get_orders( $post );
        if ( is_array( $orders ) && ! empty( $orders ) ) {
            // $orders = NotificationX_Helper::sortBy($orders, 'edd');
            $entries = [];
            foreach ( $orders as $key => $order ) {
                $entries[] = [
                    'nx_id'      => $post['nx_id'],
                    'source'     => $this->id,
                    'entry_key'  => $order['key'],
                    'data'       => $order,
                ];
            }
            $this->update_notifications($entries);
        }
    }

    /**
     * Update Payment Status
     *
     * @param int    $payment_id
     * @param string $new_status
     * @param string $old_status
     * @return void
     * @since 1.3.9
     */
    public function update_payment_status( $payment_id, $new_status, $old_status ) {
        if ( $new_status !== 'publish' ) {
            return;
        }
        $offset             = get_option( 'gmt_offset' );
        $temp_notifications = $this->single_order( $payment_id, $offset );
        if ( is_array( $temp_notifications ) ) {
            foreach ( $temp_notifications as $notification ) {
                $this->update_notification([
                    'source'     => $this->id,
                    'entry_key'  => $notification['key'],
                    'data'       => $notification,
                ]);
            }
        }
        return true;
    }

    /**
     * This function is responsible for get all payments
     *
     * @param int $days
     * @param int $amount
     * @return array
     */
    public function get_payments( $days, $amount ) {
        $date       = '-' . intval( $days ) . ' days';
        $start_date = strtotime( $date );

        $amount = $amount > 0 ? $amount : -1;

        $args = array(
            'number'     => $amount,
            'status'     => array( 'publish' ),
            'date_query' => array(
                'after' => date( 'Y-m-d', $start_date ),
            ),
        );

        return edd_get_payments( $args );
    }

    /**
     * Get all the orders from database using a date query
     * for limitation.
     *
     * @param array $data
     * @return void
     */
    public function get_orders( $data ) {
        if ( empty( $data ) ) {
            return;
        }
        $days          = $data['display_from'];
        $amount        = $data['display_last'];
        $payments      = $this->get_payments( $days, $amount );
        $notifications = array();
        if ( is_array( $payments ) && ! empty( $payments ) ) {
            $notifications = $this->ordered_products( $payments, true );
        }
        return $notifications;
    }

    /**
     * This function is responsible for
     * making ready the product notifications array
     *
     * @param int $payment_id
     * @return void
     */
    protected function ordered_products( $payments, $ready = false ) {
        $offset        = get_option( 'gmt_offset' );
        $notifications = array();
        foreach ( $payments as $payment ) :
            $temp_notifications = $this->single_order( $payment->ID, $offset );
            if ( is_array( $temp_notifications ) ) {
                foreach ( $temp_notifications as $notification ) {
                    $notifications[] = $notification;
                }
            }
        endforeach;
        return $notifications;
    }

    protected function single_order( $payment_id, $offset = 0 ) {
        if ( empty( $payment_id ) ) {
            return null;
        }
        $notifications = array();
        $data         = array();
        $payment      = new \EDD_Payment( $payment_id );
        $cart_details = $payment->cart_details;
        $user_info    = $payment->user_info;

        unset( $user_info['id'] );
        unset( $user_info['discount'] );
        unset( $user_info['address'] );

        $user_info['name']      = $this->name( $user_info['first_name'], $user_info['last_name'] );
        $user_info['timestamp'] = strtotime( $payment->date ) - ( $offset * 60 * 60 );
        $user_info['ip']        = $payment->ip;
        $user_info['id']        = $payment_id;
        if ( is_array( $cart_details ) ) {
            foreach ( $cart_details as $cart_index => $download ) {
                $if_has_course = false;
                if ( function_exists( 'tutor_utils' ) ) {
                    $if_has_course = tutor_utils()->product_belongs_with_course( $download['id'] );
                }
                if ( $if_has_course ) {
                    continue;
                }
                if ( ! $if_has_course ) {
                    $data['title']      = $download['name'];
                    $data['link']       = get_permalink( $download['id'] );
                    $data['product_id'] = $download['id'];
                    $data['key']        = $payment->key . '-' . $download['id'];
                    $notification       = array_merge( $user_info, $data );
                    $notifications[]    = $notification;
                }
            }
        }
        return $notifications;
    }
    /* #endregion */

    /**
     * Image action callback
     * @param array $image_data
     * @param array $data
     * @param stdClass $settings
     * @return array
     */
    public function notification_image($image_data, $data, $settings) {
        if (!$settings['show_default_image'] && $settings['show_notification_image'] === 'featured_image') {
            if (!empty($data['product_id']) && has_post_thumbnail($data['product_id'])) {
                $product_image = wp_get_attachment_image_src(
                    get_post_thumbnail_id($data['product_id']),
                    '_nx_notification_thumb',
                    false
                );
                $image_data['url'] = is_array($product_image) ? $product_image[0] : '';
            }
        }
        return $image_data;
    }

    /* #region  frontend */

    // @todo
    public function fallback_data( $data, $saved_data, $settings ) {
        $data['product_title']   = $saved_data['title'];
        $data['anonymous_title'] = __( 'Anonymous Product', 'notificationx' );
        return $data;
    }
    /* #endregion */

    public function doc(){
        return sprintf(__('<p>Make sure that you have <a href="%1$s" target="_blank">Easy Digital Downloads installed & activated</a> to use its campaign & product sales data. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>👉 NotificationX <a target="_blank" href="%3$s">Integration with Easy Digital Downloads</a></p>
		<p><strong>Recommended Blog:</strong></p>
		<p>🔥 How Does <a target="_blank" href="%4$s">NotificationX Increase Sales on WordPress</a> Websites?</p>', 'notificationx'),
        'https://wordpress.org/plugins/easy-digital-downloads/',
        'https://notificationx.com/docs/notificationx-easy-digital-downloads/',
        'https://notificationx.com/integrations/easy-digital-downloads/',
        'https://wpdeveloper.com/notificationx-increase-sales-wordpress/'
        );
    }
}
