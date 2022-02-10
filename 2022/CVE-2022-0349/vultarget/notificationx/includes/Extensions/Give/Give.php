<?php

/**
 * Give Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\Give;

use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;

/**
 * Give Extension
 */
class Give extends Extension {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    public $priority        = 5;
    public $id              = 'give';
    public $img             = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/give.png';
    public $doc_link        = 'https://notificationx.com/docs/givewp-donation-alert/';
    public $types           = 'donation';
    public $module          = 'modules_give';
    public $module_priority = 6;
    public $version         = '1.2.5';
    public $class           = '\Give';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('Give', 'notificationx');
        $this->module_title = __('GiveWP', 'notificationx');
        parent::__construct();
    }

    /**
     * This functions is hooked
     *
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();

        // @todo Something
        add_filter("nx_can_entry_{$this->id}", array($this, 'limit_by_selected_form'), 10, 3);
    }

    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     * @return void
     */
    public function public_actions() {
        parent::public_actions();
        // public actions will be here
        add_action('give_complete_donation', [$this, 'save_new_donation'], 10, 1);
        add_filter("nx_filtered_entry_{$this->id}", array($this, 'conversion_data'), 10, 2);
    }

    /**
     * @param array $data
     * @param array $saved_data
     * @param stdClass $settings
     * @return array
     */
    public function fallback_data($data, $saved_data, $settings) {
        $data['name']            = __('Someone', 'notificationx');
        $data['first_name']      = __('Someone', 'notificationx');
        $data['last_name']       = __('Someone', 'notificationx');
        $data['anonymous_title'] = __('Anonymous Product', 'notificationx');
        $data['sometime']        = __('Some time ago', 'notificationx');
        return $data;
    }

    // @todo Frontend
    public function conversion_data($saved_data, $settings) {
        if (isset($saved_data['amount']) && function_exists('give_currency_filter')) {
            $saved_data['amount'] = html_entity_decode(\give_currency_filter($saved_data['amount']));
        }
        return $saved_data;
    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=give&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                    __( 'You have to install', 'notificationx' ),
                    $url,
                    __( 'GiveWP Donation', 'notificationx' ),
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
     * Image action callback
     * @param array $image_data
     * @param array $data
     * @param array $settings
     * @return array
     */
    public function notification_image($image_data, $data, $settings) {
        if (!$settings['show_default_image'] && $settings['show_notification_image'] === 'featured_image') {
            if (!empty($data['give_form_id'])) {
                $image_data['url'] = get_the_post_thumbnail_url($data['give_form_id'], 'thumbnail');
            }
            if (empty($image_data['url']) && !empty($data['give_page_id'])) {
                $image_data['url'] = get_the_post_thumbnail_url($data['give_page_id'], 'thumbnail');
            }
        }
        return $image_data;
    }

    /**
     * This function is generate and save a new notification when a new donation occur
     * @param object $donation
     * @return void
     */
    public function save_new_donation($payment_id) {
        if (empty($payment_id)) {
            return;
        }
        $args = array(
            'post__in' => [$payment_id]
        );

        $result = new \Give_Payments_Query($args);
        $result = $result->get_payments();
        if (!empty($result[0])) {
            $result = $result[0];
            $key = $result->ID . '-' . $result->form_id;
            // if (!in_array($key, $this->notifications)) {
            $donation_data = array_merge(array(
                'id' => $result->ID,
                'title' => $result->form_title,
                'amount' => $result->total . ' ' . __('for', 'notificationx'),
                'link' => $result->payment_meta['_give_current_url'],
                'give_form_id' => $result->form_id,
                'give_page_id' => $result->payment_meta['_give_current_page_id'],
                'timestamp' => get_gmt_from_date($result->date),
            ), $this->get_donor($result));

            $this->update_notification([
                'source'     => $this->id,
                'entry_key'  => $key,
                'data'       => $donation_data,
            ]);
            // }
        }
    }

    public function saved_post($post, $data, $nx_id) {
        $this->delete_notification(null, $nx_id);
        $this->get_notification_ready($data);
    }

    /**
     * This function is responsible for making the notification ready for first time we make the notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready($data = array()) {
        $donations = $this->get_give_donations($data);
        if (!empty($donations)) {
            // $this->update_notification($donations, null, $data['nx_id']);
            $entries = [];
            foreach ($donations as $key => $donation) {
                $entries[] = [
                    'nx_id'      => $data['nx_id'],
                    'source'     => $this->id,
                    'entry_key'  => $key,
                    'data'       => $donation,
                ];
            }
            $this->update_notifications($entries);
        }
    }
    /**
     * Get previous give donations after select as source
     * @param array $data
     * @return array
     */
    protected function get_give_donations($data) {
        if (empty($data)) {
            return null;
        }
        $donations = [];
        $from = date(get_option('date_format'), strtotime('-' . intval($data['display_from']) . ' days'));
        $args = array(
            'number' => -1,
            'date_query' => array(
                array(
                    'after'     => $from,
                ),
            ),
        );

        if( ! class_exists('\Give_Payments_Query') ) {
            return [];
        }

        $results = new \Give_Payments_Query($args);
        $results = $results->get_payments();
        if (!empty($results)) {
            foreach ($results as $result) {
                $donations[$result->ID . '-' . $result->form_id] = array_merge(
                    array(
                        'id' => $result->ID,
                        'title' => $result->form_title,
                        'link' => $result->payment_meta['_give_current_url'],
                        'give_form_id' => $result->form_id,
                        'amount' => $result->total . ' ' . __('for', 'notificationx'),
                        'give_page_id' => $result->payment_meta['_give_current_page_id'],
                        'timestamp' => get_gmt_from_date($result->date),
                    ),
                    $this->get_donor($result)
                );
            }
        }
        return $donations;
    }

    /**
     * Get donor information
     * @param object $donation
     * @return array
     */
    protected function get_donor($donation) {
        $user_data = [];
        $first_name = $donation->first_name;
        $last_name = $donation->last_name;
        if (!empty($first_name)) {
            $user_data['first_name'] = $first_name;
        } else {
            $user_data['first_name'] = '';
        }
        if (!empty($last_name)) {
            $user_data['last_name'] = $last_name;
        } else {
            $user_data['last_name'] = '';
        }
        $user_data['name'] = trim($user_data['first_name'] . ' ' . mb_substr($user_data['last_name'], 0, 1));
        $user_data['email'] = $donation->email;
        $user_data['country'] = $donation->address['country'];
        $user_data['city'] = $donation->address['city'];
        $user_data['ip'] = give_get_payment_user_ip($donation->ID);

        return $user_data;
    }

    /**
     * Hooked with nx_can_entry_give
     *
     * @param [type] $return
     * @param [type] $entry
     * @param [type] $settings
     * @return void
     */
    public function limit_by_selected_form( $return, $entry, $settings ){
        if( empty( $settings['give_forms_control'] ) || empty( $settings['give_form_list'] ) || $settings['give_forms_control'] === 'none' ) {
            return $return;
        }

        if($settings['give_forms_control'] === 'give_form' && !empty($entry['data']['give_form_id'])){
            $give_form_id = $entry['data']['give_form_id'];
            if(!in_array($give_form_id, $settings['give_form_list'])){
                return false;
            }
        }
        return $return;
    }

    public function doc() {
        return sprintf(__('<p>Make sure that you have <a target="_blank" href="%1$s">GiveWP installed & configured</a> to use its campaign & donars data. For further assistance, check out our step by step <a href="%2$s">documentation</a>.</p>
		<p>🎦 <a target="_blank" href="%3$s">Watch video tutorial</a> to learn quickly</p>
		<p>👉 NotificationX <a target="_blank" href="%4$s">Integration with GiveWP</a></p>
		<p><strong>Recommended Blog:</strong></p>
		<p>🔥 How Does <a target="_blank" href="%5$s">NotificationX Increase Sales on WordPress</a> Websites?"</p>', 'notificationx'),
        'https://wordpress.org/plugins/give/',
        'https://notificationx.com/docs/givewp-donation-alert/',
        'https://www.youtube.com/watch?v=8EFgHSA8mOg',
        'https://notificationx.com/integrations/givewp/',
        'https://wpdeveloper.com/notificationx-increase-sales-wordpress/'
        );
    }
}
