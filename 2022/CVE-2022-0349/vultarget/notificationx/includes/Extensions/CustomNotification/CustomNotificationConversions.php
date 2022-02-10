<?php
/**
 * CustomNotification Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\CustomNotification;

use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;

/**
 * CustomNotification Extension
 */
class CustomNotificationConversions extends Extension {
    /**
     * Instance of CustomNotificationConversions
     *
     * @var CustomNotificationConversions
     */
    use GetInstance;

    public $priority        = 30;
    public $id              = 'custom_notification_conversions';
    public $img             = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/custom.png';
    public $doc_link        = 'https://notificationx.com/docs/custom-notification';
    public $types           = 'conversions';
    public $module          = 'modules_custom_notification';
    public $module_priority = 13;
    public $is_pro          = true;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->title = __('Custom Notification', 'notificationx');
        $this->module_title = __('Custom Notification', 'notificationx');
        parent::__construct();
    }

    /**
     * Get data for CustomNotification Extension.
     *
     * @param array $args Settings arguments.
     * @return array
     */
    public function get_data( $args = array() ){
        return 'Hello From Custom Notification';
    }

    public function doc(){
        return sprintf(__('<p>You can make custom notification for its all types of campaign. For further assistance, check out our step by step <a target="_blank" href="%1$s">documentation</a>.</p>
		<p>🎦 Watch <a target="_blank" href="%2$s">video tutorial</a> to learn quickly</p>
		<p><strong>Recommended Blog:</strong></p>
		<p>🔥 How to <a target="_blank" href="%3$s">Display Custom Notification Alerts</a> On Your Website Using NotificationX</p>', 'notificationx'),
        'https://notificationx.com/docs/custom-notification/',
        'https://www.youtube.com/watch?v=OuTmDZ0_TEw',
        'https://wpdeveloper.com/custom-notificationx-alert-fomo/'
        );
    }
}
