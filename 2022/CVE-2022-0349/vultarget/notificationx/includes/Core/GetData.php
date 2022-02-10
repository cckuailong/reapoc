<?php
/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Core;

use NotificationX\Admin\Settings;
use NotificationX\GetInstance;

/**
 * ExtensionFactory Class
 */
class GetData extends \ArrayObject {


    public function offsetGet ($name){
        if(parent::offsetExists($name)){
            return parent::offsetGet($name);
        }

    }

}