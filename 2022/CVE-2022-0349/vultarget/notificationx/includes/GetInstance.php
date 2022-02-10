<?php
/**
 * GetInstance File
 *
 * @package NotificationX
 */

namespace NotificationX;

use NotificationX\Admin\Admin;
use NotificationX\Admin\Cron;
use NotificationX\Admin\Entries;
use NotificationX\Admin\Settings;
use NotificationX\Core\Analytics;
use NotificationX\Core\Database;
use NotificationX\Core\Modules;
use NotificationX\Core\PostType;
use NotificationX\Core\REST;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\Extensions\GlobalFields;
use NotificationX\FrontEnd\FrontEnd;
use NotificationX\Types\TypeFactory;
use NotificationX\Types\Types;
use NotificationX\Core\Limiter;
use NotificationX\Core\Locations;

/**
 * Base trait make the instances of called class.
 */
trait GetInstance {
    /**
     * Instance of Called Class.
     *
     * @var GetInstance
     */
    protected static $instance = null;
    /**
     * Get the instance of called class.
     *
     * @return Extension|Types|GlobalFields|TypeFactory|ExtensionFactory|Database|Admin|PostType|Settings|UDSettings|REST|NotificationX|Modules|FrontEnd|Entries|Limiter|Analytics|Helper|Cron|CoreInstaller|CustomNotification|Locations
     */
    public static function get_instance($args = null){
        if ( is_null( static::$instance ) || ! static::$instance instanceof self ) {
            $class = __CLASS__;
            if(strpos($class, "NotificationX\\") === 0){
                $pro_class = str_replace("NotificationX\\", "NotificationXPro\\", $class);
                if(class_exists($pro_class) && is_subclass_of($pro_class, $class)){
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

    // public function __call($name, $arguments){
    //     $class = __CLASS__ . 'Pro';

    //     if(strpos($class, "NotificationX\\") === 0){
    //         $pro_class = str_replace("NotificationX\\", "NotificationXPro\\", $class);
    //         if(class_exists($pro_class)){
    //             $obj = $pro_class::get_instance();
    //             if($obj && method_exists($obj, $name)){
    //                 $obj->$name($arguments);
    //             }
    //         }
    //     }
    // }

}
