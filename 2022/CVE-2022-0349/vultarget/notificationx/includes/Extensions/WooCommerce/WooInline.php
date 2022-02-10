<?php
/**
 * WooCommerce Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\WooCommerce;

use NotificationX\Core\PostType;
use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * WooCommerce Extension Class
 */
class WooInline extends WooCommerce {
    /**
     * Instance of WooInline
     *
     * @var WooInline
     */
    protected static $instance = null;
    public $priority        = 5;
    public $id              = 'woo_inline';
    public $img             = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/woocommerce.png';
    public $doc_link        = 'https://notificationx.com/docs/woocommerce-sales-notifications/';
    public $types           = 'inline';
    public $module          = 'modules_woocommerce';
    public $module_priority = 3;
    public $class           = '\WooCommerce';

    /**
     * Get the instance of called class.
     *
     * @return WooInline
     */
    public static function get_instance($args = null){
        if ( is_null( static::$instance ) || ! static::$instance instanceof self ) {
            $class = __CLASS__;
            if(strpos($class, "NotificationX\\") === 0){
                $pro_class = str_replace("NotificationX\\", "NotificationXPro\\", $class);
                if(class_exists($pro_class)){
                    $class = $pro_class;
                }
            }

            if(!empty($args)){
                static::$instance = new $class($args);
            }
            else{
                static::$instance = new $class;
            }
        }
        return static::$instance;
    }

    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        parent::__construct();

    }

    public function content_fields($fields){
        return $fields;
    }

}
