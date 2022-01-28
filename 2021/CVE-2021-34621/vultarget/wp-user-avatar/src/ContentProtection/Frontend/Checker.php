<?php

namespace ProfilePress\Core\ContentProtection\Frontend;


use ProfilePress\Core\ContentProtection\ContentConditions;

class Checker
{
    public static function is_blocked($who_can_access = 'everyone', $roles = [])
    {
        if ('login' == $who_can_access) {

            if ( ! is_user_logged_in()) return true;

            if ( ! empty($roles)) {

                $user_roles = wp_get_current_user()->roles;

                if (empty(array_intersect($roles, $user_roles))) return true;
            }
        }

        if ('logout' == $who_can_access) {

            if (is_user_logged_in()) return true;
        }

        return false;
    }

    /**
     * @param $protection_rule
     * @param bool $is_redirect set to true if this is a redirect check and not post content check.
     *
     * @return bool
     */
    public static function content_match($protection_rule, $is_redirect = false)
    {
        $content_match = false;

        if (empty($protection_rule)) return $content_match;

        // All Groups Must Return True. Break if any is false and set $loadable to false.
        foreach ($protection_rule as $group => $conditions) {

            // Groups are false until a condition proves true.
            $group_check = false;

            // At least one group condition must be true. Break this loop if any condition is true.
            foreach ($conditions as $condition) {

                $match = self::check_condition($condition['condition'], ppress_var($condition, 'value', []), $is_redirect);

                // If any condition passes, set $group_check true and break.
                if ($match) {
                    $group_check = true;
                    break;
                }
            }

            // If any group of conditions doesn't pass, popup is not loadable.
            if ( ! $group_check) {
                $content_match = false;
                break;
            } else {
                $content_match = true;
            }
        }

        return $content_match;

    }

    public static function check_condition($condition_id, $rule_saved_value, $is_redirect = false)
    {
        $condition = ContentConditions::get_instance()->get_condition($condition_id);

        if ( ! $condition) return false;

        return call_user_func($condition['callback'], $condition_id, $rule_saved_value, $is_redirect);
    }
}