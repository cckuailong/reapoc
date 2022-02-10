<?php

/*
 * PublishPress Capabilities [Free]
 *
 * Functions available to wp-admin requests, which are not contained within a class
 *
 */

function cme_fakefunc() {
}

function pp_capabilities_get_post_id()
{
    global $post;

    if (defined('REST_REQUEST') && REST_REQUEST) {
        if ($_post_id = apply_filters('presspermit_rest_post_id', 0)) {
            return $_post_id;
        }
    }

    if (!empty($post) && is_object($post)) {
        if ('auto-draft' == $post->post_status) {
            return 0;
        } else {
            return $post->ID;
        }

    } elseif (isset($_REQUEST['post'])) {
        return (int)$_REQUEST['post'];

    } elseif (isset($_REQUEST['post_ID'])) {
        return (int)$_REQUEST['post_ID'];

    } elseif (isset($_REQUEST['post_id'])) {
        return (int)$_REQUEST['post_id'];

    } elseif (defined('WOOCOMMERCE_VERSION') && !empty($_REQUEST['product_id'])) {
        return (int)$_REQUEST['product_id'];
    }
}

/**
 * Based on Edit Flow's \Block_Editor_Compatible::should_apply_compat method.
 *
 * @return bool
 */
function _pp_capabilities_is_block_editor_active($post_type = '', $args = [])
{
    global $current_user, $wp_version;

    $defaults = ['suppress_filter' => false, 'force_refresh' => false];
    $args = array_merge($defaults, $args);
    $suppress_filter = $args['suppress_filter'];

    // Check if Revisionary lower than v1.3 is installed. It disables Gutenberg.
    if (defined('REVISIONARY_VERSION') && version_compare(REVISIONARY_VERSION, '1.3-beta', '<')) {
        return false;
    }

    static $buffer;
    if (!isset($buffer)) {
        $buffer = [];
    }

    if (!$post_type = pp_capabilities_get_post_type()) {
        return true;
    }

    if ($post_type_obj = get_post_type_object($post_type)) {
        if (!$post_type_obj->show_in_rest) {
            return false;
        }
    }

    if (isset($buffer[$post_type]) && empty($args['force_refresh']) && !$suppress_filter) {
        return $buffer[$post_type];
    }

    if (class_exists('Classic_Editor')) {
        if (isset($_REQUEST['classic-editor__forget']) && (isset($_REQUEST['classic']) || isset($_REQUEST['classic-editor']))) {
            return false;
        } elseif (isset($_REQUEST['classic-editor__forget']) && !isset($_REQUEST['classic']) && !isset($_REQUEST['classic-editor'])) {
            return true;
        } elseif (get_option('classic-editor-allow-users') === 'allow') {
            if ($post_id = pp_capabilities_get_post_id()) {
                $which = get_post_meta( $post_id, 'classic-editor-remember', true );

                if ('block-editor' == $which) {
                    return true;
                } elseif ('classic-editor' == $which) {
                    return false;
                }
            } else {
                $use_block = ('block' == get_user_meta($current_user->ID, 'wp_classic-editor-settings'));
                return $use_block && apply_filters('use_block_editor_for_post_type', $use_block, $post_type, PHP_INT_MAX);
            }
        }
    }

    $pluginsState = array(
        'classic-editor' => class_exists( 'Classic_Editor' ), // is_plugin_active('classic-editor/classic-editor.php'),
        'gutenberg'      => function_exists( 'the_gutenberg_project' ), //is_plugin_active('gutenberg/gutenberg.php'),
        'gutenberg-ramp' => class_exists('Gutenberg_Ramp'),
    );
    
    $conditions = [];

    if ($suppress_filter) remove_filter('use_block_editor_for_post_type', $suppress_filter, 10, 2);

    /**
     * 5.0:
     *
     * Classic editor either disabled or enabled (either via an option or with GET argument).
     * It's a hairy conditional :(
     */
    // phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.Security.NonceVerification.NoNonceVerification
    $conditions[] = (version_compare($wp_version, '5.0', '>=') || $pluginsState['gutenberg'])
                    && ! $pluginsState['classic-editor']
                    && ! $pluginsState['gutenberg-ramp']
                    && apply_filters('use_block_editor_for_post_type', true, $post_type, PHP_INT_MAX);

    $conditions[] = version_compare($wp_version, '5.0', '>=')
                    && $pluginsState['classic-editor']
                    && (get_option('classic-editor-replace') === 'block'
                        && ! isset($_GET['classic-editor__forget']));

    $conditions[] = version_compare($wp_version, '5.0', '>=')
                    && $pluginsState['classic-editor']
                    && (get_option('classic-editor-replace') === 'classic'
                        && isset($_GET['classic-editor__forget']));

    $conditions[] = $pluginsState['gutenberg-ramp'] 
                    && apply_filters('use_block_editor_for_post', true, get_post(pp_capabilities_get_post_id()), PHP_INT_MAX);

    // Returns true if at least one condition is true.
    $result = count(
                array_filter($conditions,
                    function ($c) {
                        return (bool)$c;
                    }
                )
            ) > 0;
    
    if (!$suppress_filter) {
        $buffer[$post_type] = $result;
    }

    // Returns true if at least one condition is true.
    return $result;
}

/**
 * Remove all non-alphanumeric and space characters from a string.
 *
 * @param string $string .
 *
 * @return string
 *
 * @since 2.1.1
 */
function ppc_remove_non_alphanumeric_space_characters($string)
{
    return preg_replace("/(\W)+/", "", $string);
}
