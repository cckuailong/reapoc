<?php
/**
 * Zapier Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\Zapier;

use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;

/**
 * Zapier Extension
 */
class ZapierConversions extends Extension {
    /**
     * Instance of Zapier
     *
     * @var Zapier
     */
    use GetInstance;
    use Zapier;

    public $priority = 20;
    public $id       = 'zapier_conversions';
    public $img      = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/zapier.png';
    public $doc_link = 'https://notificationx.com/docs/zapier-notification-alert/';
    public $types    = 'conversions';
    public $module   = 'modules_zapier';
    public $is_pro   = true;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->title = __('Zapier', 'notificationx');
        $this->module_title = __('Zapier', 'notificationx');
        parent::__construct();
    }

    /**
     * Get data for Zapier Extension.
     *
     * @param array $args Settings arguments.
     * @return array
     */
    public function get_data( $args = array() ){
        return 'Hello From Zapier';
    }

    public function _doc(){
        return '
        <ul class="conversions nx-template-keys">
            <li><span>' . __('Field Name:', 'notificationx') . '</span> <strong>' . __('Field Key', 'notificationx') . '</strong></li>
            <li><span>' . __('Full Name:', 'notificationx') . '</span> <strong>name</strong></li>
            <li><span>' . __('First Name:', 'notificationx') . '</span> <strong>first_name</strong></li>
            <li><span>' . __('Last Name:', 'notificationx') . '</span> <strong>last_name</strong></li>
            <li><span>' . __('Sales Count:', 'notificationx') . '</span> <strong>sales_count</strong></li>
            <li><span>' . __('Customer Email:', 'notificationx') . '</span> <strong>email</strong></li>
            <li><span>' . __('Title, Product Title:', 'notificationx') . '</span> <strong>title</strong></li>
            <li><span>' . __('Anonymous Title, Product:', 'notificationx') . '</span> <strong>anonymous_title</strong></li>
            <li><span>' . __('Definite Time:', 'notificationx') . '</span> <strong>timestamp</strong></li>
            <li><span>' . __('Sometime:', 'notificationx') . '</span> <strong>sometime</strong></li>
            <li><span>' . __('In last 1 day:', 'notificationx') . '</span> <strong>1day</strong></li>
            <li><span>' . __('In last 7 days:', 'notificationx') . '</span> <strong>7days</strong></li>
            <li><span>' . __('In last 30 days:', 'notificationx') . '</span> <strong>30days</strong></li>
            <li><span>' . __('City:', 'notificationx') . '</span> <strong>city</strong></li>
            <li><span>' . __('Country:', 'notificationx') . '</span> <strong>country</strong></li>
            <li><span>' . __('City,Country:', 'notificationx') . '</span> <strong>city_country</strong></li>
        </ul>';
    }
}
