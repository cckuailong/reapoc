<?php

namespace ProfilePress\Core\ContentProtection;


class ConditionCallbacks
{
    /**
     * Checks if this is one of the selected post_type items.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     * @param bool $is_redirect
     *
     * @return bool
     */
    public static function post_type($condition_id, $rule_saved_value, $is_redirect = false)
    {
        global $post;

        $target = explode('_', $condition_id);

        // Modifier should be the last key.
        $modifier = array_pop($target);

        // Post type is the remaining keys combined.
        $post_type = implode('_', $target);

        $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];

        switch ($modifier) {
            case 'index':

                if (is_post_type_archive($post_type)) return true;
                break;

            case 'all':

                if (self::_is_post_type($post_type)) {

                    // do not redirect home and blog page. if there is a need, add the homepage and blog page OR rule.
                    if (true === $is_redirect && ( ! is_singular($post_type) || is_front_page() || is_home())) return false;

                    return true;
                }
                break;

            case 'selected':

                if (self::_is_post_type($post_type) && in_array($post->ID, wp_parse_id_list($selected))) {

                    if (true === $is_redirect && ! is_singular($post_type)) return false;

                    return true;
                }
                break;

            case 'children':

                if ( ! is_post_type_hierarchical($post_type) || ! self::_is_post_type($post_type)) return false;

                $selected = wp_parse_id_list($selected);

                foreach ($selected as $id) {

                    if ($post->post_parent == $id) {

                        if (true === $is_redirect && ! is_singular($post_type)) return false;

                        return true;
                    }
                }
                break;

            case 'ancestors':

                if ( ! is_post_type_hierarchical($post_type) || ! self::_is_post_type($post_type)) return false;

                $selected = wp_parse_id_list($selected);

                foreach ($selected as $id) {

                    $ancestors = get_post_ancestors($id);

                    if (in_array($post->ID, $ancestors)) {

                        if (true === $is_redirect && ! is_singular($post_type)) return false;

                        return true;
                    }
                }
                break;

            case 'template':

                if (is_page() && is_page_template($selected)) return true;
                break;
        }

        return false;
    }

    /**
     * Checks if this is one of the selected taxonomy term.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     *
     * @return bool
     */
    public static function taxonomy($condition_id, $rule_saved_value)
    {
        $target = explode('_', $condition_id);

        // Remove the tax_ prefix.
        array_shift($target);

        // Assign the last key as the modifier _all, _selected
        $modifier = array_pop($target);

        // Whatever is left is the taxonomy.
        $taxonomy = implode('_', $target);

        if ($taxonomy == 'category') {
            return self::_category($condition_id, $rule_saved_value);
        }

        if ($taxonomy == 'post_tag') {
            return self::_post_tag($condition_id, $rule_saved_value);
        }

        switch ($modifier) {
            case 'all':
                if (is_tax($taxonomy)) {
                    return true;
                }
                break;

            case 'selected':
                $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];

                if (is_tax($taxonomy, wp_parse_id_list($selected))) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Checks if the post_type has the selected categories.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     * @param bool $is_redirect
     *
     * @return bool
     */
    public static function post_type_tax($condition_id, $rule_saved_value, $is_redirect = false)
    {
        $target = explode('_w_', $condition_id);

        // First key is the post type.
        $post_type = array_shift($target);

        // Last Key is the taxonomy
        $taxonomy = array_pop($target);

        if ($taxonomy == 'category') {

            if (true === $is_redirect && ! is_singular($post_type)) return false;

            return self::_post_type_category($condition_id, $rule_saved_value);
        }

        if ($taxonomy == 'post_tag') {

            if (true === $is_redirect && ! is_singular($post_type)) return false;

            return self::_post_type_tag($condition_id, $rule_saved_value);
        }

        $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];

        if (self::_is_post_type($post_type) && has_term(wp_parse_id_list($selected), $taxonomy)) {

            if (true === $is_redirect && ! is_singular($post_type)) return false;

            return true;
        }

        return false;
    }


    /**
     * Checks if this is one of the selected categories.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     *
     * @return bool
     */
    public static function _category($condition_id, $rule_saved_value)
    {
        $target = explode('_', $condition_id);

        // Assign the last key as the modifier _all, _selected
        $modifier = array_pop($target);

        switch ($modifier) {
            case 'all':
                if (is_category()) return true;
                break;

            case 'selected':
                $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];
                if (is_category(wp_parse_id_list($selected))) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Checks if this is one of the selected tags.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     *
     * @return bool
     */
    public static function _post_tag($condition_id, $rule_saved_value)
    {
        $target = explode('_', $condition_id);

        $modifier = array_pop($target);

        switch ($modifier) {
            case 'all':
                if (is_tag()) {
                    return true;
                }
                break;

            case 'selected':
                $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];
                if (is_tag(wp_parse_id_list($selected))) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Checks if the post_type has the selected categories.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     *
     * @return bool
     */
    public static function _post_type_category($condition_id, $rule_saved_value)
    {
        $target = explode('_w_', $condition_id);

        // First key is the post type.
        $post_type = array_shift($target);

        $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];

        if (self::_is_post_type($post_type) && has_category(wp_parse_id_list($selected))) {
            return true;
        }

        return false;
    }

    /**
     * Checks is a post_type has the selected tags.
     *
     * @param string $condition_id
     * @param mixed $rule_saved_value
     *
     * @return bool
     */
    public static function _post_type_tag($condition_id, $rule_saved_value)
    {
        $target = explode('_w_', $condition_id);

        // First key is the post type.
        $post_type = array_shift($target);

        $selected = ! empty($rule_saved_value) ? $rule_saved_value : [];
        if (self::_is_post_type($post_type) && has_tag(wp_parse_id_list($selected))) {
            return true;
        }

        return false;
    }

    /**
     * @param string $post_type
     *
     * @return bool
     */
    public static function _is_post_type($post_type)
    {
        global $post;

        return is_object($post) && $post->post_type == $post_type;
    }
}