<?php
/**
 * Rule
 *
 * @package NotificationX\Core
 */

namespace NotificationX\Core;

use ArrayIterator;
use ArrayObject;
use JsonSerializable;
use NotificationX\Admin\Settings;
use Serializable;

class Rule implements JsonSerializable {
    public $condition;
    public $name;
    public $value;
    public function __construct($condition, $name, $value){
        $this->condition = $condition;
        $this->name      = $name;
        $this->value     = $value;
    }

    public function add_value($value){
        if(!is_array($this->value)){
            $this->value = [$this->value];
        }
        if(is_a($value, 'NotificationX\Core\Rule')){
            $value = $value->value;
        }
        if(is_array($value)){
            $this->value = array_merge($this->value, $value);
        }
        else{
            $this->value[] = $value;
        }
        $this->value = array_values(array_unique($this->value));
    }

    public function can_add($rule){
        if($this->condition == 'includes' && $this->condition == $rule->condition && $this->name == $rule->name){
            return true;
        }
        return false;
    }

    public function jsonSerialize(){
        return [
            $this->condition,
            $this->name,
            $this->value,
        ];
    }
}