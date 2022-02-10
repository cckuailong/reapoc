<?php
/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;
use NotificationX\GetInstance;
use NotificationX\Modules;

/**
 * Extension Abstract for all Extension.
 */
class CustomNotification extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
	use GetInstance;
    public $priority = 55;
    public $is_pro = true;
    public $themes = 'all';
    public $module = ['modules_custom_notification'];
    public $default_source    = 'custom_notification';
    // @todo default theme for custom
    // public $default_theme = 'conversions_theme-one';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->id = 'custom';
        $this->title = __('Custom Notification', 'notificationx');
        parent::__construct();
    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function init(){
        parent::init();

    }



}
