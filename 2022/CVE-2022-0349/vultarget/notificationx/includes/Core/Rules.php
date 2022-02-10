<?php
/**
 * Rule
 *
 * @package NotificationX\Core
 */

namespace NotificationX\Core;

use NotificationX\Admin\Settings;

class Rules {

    public static function isOfType( $key, $value = false, $_not = false, $field = null ) {
        $rule = self::_isOfType($key, $value, $_not);
        return $field ? self::add($rule, $field) : $rule;
    }

    public static function _isOfType( $key, $value =false, $_not = false ){
        return $_not ? new Rule('!isOfType', $key, $value) : new Rule('isOfType', $key, $value);
    }

    public static function is( $key, $value = false, $_not = false, $field = null ){
        $rule = self::_is($key, $value, $_not);
        return $field ? self::add($rule, $field) : $rule;
    }

    public static function includes( $key, $value = false, $_not = false, $field = null){
        if(!is_array($value)){
            $value = [$value];
        }
        $rule = self::_includes($key, $value, $_not);
        return $field ? self::add($rule, $field) : $rule;
    }

    public static function _is( $key, $value = false, $_not = false ){
        return $_not ? new Rule( '!is', $key, $value ) : new Rule( 'is', $key, $value );
    }

    public static function _includes( $key, $value = false, $_not = false){
        return $_not ? new Rule( '!includes', $key, $value ) : new Rule( 'includes', $key, $value );
    }

    public static function logicalRule( $rules, $operator = 'and', $field = null ){
        if(!empty($field)){
            foreach ($rules as $key => $rule) {
                $field = self::add($rule, $field, $operator);
            }
            return $field;
        }
        return self::_logicalRule( $rules, $operator);
    }

    public static function _logicalRule( $rules, $operator = 'and' ){
        // if operator already exist then then append the rest rules in it.
        if(!empty($rules[0]) && is_array($rules[0]) && !empty($rules[0][0]) && $rules[0][0] == $operator){
            $first = $rules[0];
            unset($rules[0]);
            return array_merge($first, $rules);
        }
        return array_merge([$operator], $rules);
    }

    public static function add( $rule, $field, $operator = 'and' ){
        if(empty($field['rules'])){
            $field['rules'] = $rule;
        }
        else{
            if(!self::add_rule($rule, $field['rules'])){
                $field['rules'] = self::_logicalRule([$field['rules'], $rule], $operator);
            }
        }
        return $field;
    }

    // Function to recursively search for a given value
    public static function add_rule($search_value, $array) {

        if(is_array($array) && count($array) > 0) {

            foreach($array as $key => $value) {
                if(is_array($value) && count($value) > 0) {
                    $return = self::add_rule($search_value, $value);
                    if($return) return true;
                }
                else if(is_a($value, 'NotificationX\Core\Rule') && $value->can_add($search_value)) {
                    $value->add_value($search_value);
                    return true;
                }
            }
        }
        elseif(is_a($array, 'NotificationX\Core\Rule') && $array->can_add($search_value)){
            $array->add_value($search_value);
            return true;
        }

        return false;
    }

}