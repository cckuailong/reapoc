<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizes the values of an array recursively using sanitize_text_field
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_sanitize_text_field($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_sanitize_text_field($value);
        } else {
            $array[$key] = sanitize_text_field($value);
        }

    }

    return $array;

}

function _wpbs_recursive_array_replace($find, $replace, $array)
{
    if (!is_array($array)) {
        return str_replace($find, $replace, $array);
    }
    $newArray = array();
    foreach ($array as $key => $value) {
        $newArray[$key] = _wpbs_recursive_array_replace($find, $replace, $value);
    }
    return $newArray;
}

/**
 * Sanitizes the values of an array recursively and allows HTML tags
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_wp_kses_post($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_wp_kses_post($value);
        } else {
            $array[$key] = wp_kses_post($value);
        }

    }

    return $array;

}

/**
 * Escapes the values of an array recursively using esc_attr
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_text_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_text_field($value);
        } else {
            $array[$key] = esc_attr($value);
        }
    }

    return $array;
}

/**
 * Escapes the values of an array recursively using esc_textarea
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_textarea_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_textarea_field($value);
        } else {
            $array[$key] = esc_textarea($value);
        }
    }

    return $array;
}

/**
 * Helper function to search an array
 *
 * @param string
 * @param array
 *
 * @return bool
 *
 */
function _wpbs_recursive_array_search($needle, $haystack)
{
    foreach ($haystack as $key => $value) {
        if ($key === 'user_value') {
            continue;
        }

        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && _wpbs_recursive_array_search($needle, $value) !== false)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Returns the current locale
 *
 * @return string
 *
 */
function wpbs_get_locale()
{

    return substr(get_locale(), 0, 2);

}
