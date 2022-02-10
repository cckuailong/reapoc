<?php

use Aventura\Wprss\Core\Model\Regex\HtmlEncoder;

/**
 * Helper and misc functions.
 *
 * @todo Make this part of Core instead
 */

/**
 * Checks if developer mode is enabled.
 *
 * @since 4.15.1
 *
 * @return bool
 */
function wpra_is_dev_mode()
{
    return apply_filters('wpra_dev_mode', false) === true;
}

/**
 * WP RSS Aggregator's version of {@link wp_remote_get()}.
 *
 * It ensures that the feed request useragent is used as the HTTP request's User Agent String.
 *
 * @since 4.15.1
 *
 * @see https://developer.wordpress.org/reference/classes/WP_Http/request/ Information on the $args array parameter.
 *
 * @param string $url The URL
 * @param array $args The arguments.
 *
 * @return array|WP_Error
 */
function wpra_remote_get($url, $args)
{
    $args['user-agent'] = wprss_get_general_setting('feed_request_useragent');

    return wp_remote_get($url, $args);
}

/**
 * WP RSS Aggregator's version of {@link wp_safe_remote_get()}.
 *
 * It ensures that the feed request useragent is used as the HTTP request's User Agent String.
 *
 * @since 4.15.1
 *
 * @see https://developer.wordpress.org/reference/classes/WP_Http/request/ Information on the $args array parameter.
 *
 * @param string $url The URL
 * @param array $args The arguments.
 *
 * @return array|WP_Error
 */
function wpra_safe_remote_get($url, $args)
{
    $args['user-agent'] = wprss_get_general_setting('feed_request_useragent');

    return wp_safe_remote_get($url, $args);
}

/**
 * Utility function for checking a plugin's state, even if it's inactive.
 *
 * @since 4.15.1
 *
 * @param string $basename The basename of the plugin.
 *
 * @return int 0 if the plugin is not installed, 1 if installed but not activated, 2 if installed and activated.
 */
function wpra_get_plugin_state($basename)
{
    if (!function_exists('is_plugin_active') && file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    // ACTIVE
    if (function_exists('is_plugin_active') && is_plugin_active($basename)) {
        return 2;
    }

    // INSTALLED & INACTIVE
    if (file_exists(WP_PLUGIN_DIR . '/' . $basename) && is_plugin_inactive($basename)) {
        return 1;
    }

    // NOT INSTALLED
    return 0;
}

/**
 * Utillity function for generating a URL that activates a plugin.
 *
 * @since 4.15.1
 *
 * @param string $basename The basename of the plugin.
 *
 * @return string
 */
function wpra_get_activate_plugin_url($basename)
{
    return wp_nonce_url(
        sprintf('plugins.php?action=activate&amp;plugin=%s', $basename),
        sprintf('activate-plugin_%s', $basename)
    );
}

/**
 * Retrieves the callbacks that are attached to a hook.
 *
 * @since 4.18
 *
 * @param string $hook The hook name.
 *
 * @return callable[] A list of callbacks.
 */
function wpra_get_hook_callbacks($hook)
{
    global $wp_filter;

    $results = [];

    if (isset($wp_filter[$hook])) {
        $hook = $wp_filter[$hook];

        foreach ($hook->callbacks as $list) {
            foreach ($list as $callback) {
                $results[] = $callback;
            }
        }
    }

    return $results;
}

/**
 * Returns a representation of an HTML expression that matches all representations of that HTML.
 *
 * @since 4.14
 *
 * @param string      $expr  The expression to encodify.
 * @param string|null $delim The delimiter of the expression.
 *
 * @return string The encodified HTML expression.
 */
function wprss_html_regex_encodify($expr, $delim = null)
{
    static $instance;

    if ($instance === null) {
        $instance = new HtmlEncoder();
    }

    $instance->reset();

    if ($delim !== null) {
        // Not a bug: the `setDelimiter` method call is handled using magic `__call()`
        $instance->setDelimiter($delim);
    }

    return $instance->encodify($expr);
}

if (!function_exists('wprss_get_namespace')) {

    /**
     * Get the namespace of a class
     *
     * @since 1.0
     * @param string|object|null $class The class name or instance, for which to get the namespace.
     * @param int|null $depth The depth of the namespace to retrieve.
     *  If omitted, the whole namespace will be retrieved.
     * @param bool $asString If true, the result will be a string; otherwise, array with namespace parts.
     * @return array|string The namespace of the class.
     */
    function wprss_get_namespace($class, $depth = null, $asString = false)
    {
        $ns = '\\';

        // Can accept an instance
        if (is_object($class)) {
            $class = get_class($class);
        }

        $namespace = explode($ns, (string) $class);

        // This was a root class name, no namespace
        array_pop($namespace);
        if (!count($namespace)) {
            return null;
        }

        $namespace = array_slice($namespace, 0, $depth);

        return $asString ? implode($ns, $namespace) : $namespace;
    }
}

if (!function_exists('wprss_is_root_namespace')) {

    /**
     * Check if a namespace is a root namespace.
     *
     * @since 1.0
     * @param string $namespace The namespace to check.
     * @param bool $checkClass If true, and a class or interface with the name of the specified namespace exists,
     *  will make this function return true. Otherwise, the result depends purely on the namespace string.
     * @return boolean True if the namespace is a root namespace; false otherwise.
     */
    function wprss_is_root_namespace($namespace, $checkClass = true) {
        $isRoot = substr($namespace, 0, 1) === '\\';
        return $checkClass
            ? $isRoot || class_exists($namespace)
            : $isRoot;
    }
}

if (!function_exists('string_had_prefix')) {

    /**
     * Check for and possibly remove a prefix from a string.
     *
     * @since 1.0
     * @param string $string The string to check and normalize.
     * @param string $prefix The prefix to check for.
     * @return string Checks if a string starts with the specified prefix.
     *  If yes, removes it and returns true; otherwise, false;
     */
    function string_had_prefix(&$string, $prefix)
    {
        $prefixLength = strlen($prefix);
        if (substr($string, 0, $prefixLength) === $prefix) {
            $string = substr($string, $prefixLength);
            return true;
        }

        return false;
    }
}

if (!function_exists('uri_is_absolute')) {

    /**
     * Check if the URI is absolute.
     *
     * Check is made based on whether or not there's a '//' sequence
     * somewhere in the beginning.
     *
     * @since 1.0
     * @param string $uri The URI to check.
     * @return boolean True of the given string contains '//' within the first 10 chars;
     *  otherwise, false.
     */
    function uri_is_absolute($uri)
    {
        $beginning = substr($uri, 0, 10);
        return strpos($beginning, '//') !== false;
    }
}

if ( ! function_exists('array_merge_recursive_distinct') ) {
	/**
	* array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	* keys to arrays rather than overwriting the value in the first array with the duplicate
	* value in the second array, as array_merge does. I.e., with array_merge_recursive,
	* this happens (documented behavior):
	*
	* array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	*     => array('key' => array('org value', 'new value'));
	*
	* array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
	* Matching keys' values in the second array overwrite those in the first array, as is the
	* case with array_merge, i.e.:
	*
	* array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
	*     => array('key' => array('new value'));
	*
	* Parameters are passed by reference, though only for performance reasons. They're not
	* altered by this function.
	*
     * @since 1.0
	* @param array $array1
	* @param array $array2
	* @return array
	* @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	* @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	*/
	function array_merge_recursive_distinct ( array &$array1, array &$array2 ) {
		$merged = $array1;
		foreach ( $array2 as $key => &$value ) {
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
				$merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
			}
			else $merged [$key] = $value;
		}
		return $merged;
	}
}

if (!function_exists('array_pick')) {

    /**
     * Picks values with certain keys from an array.
     *
     * @since 1.0
     * @param array $array An array, from which to pick. Will not be modified; passed by refrence for efficiency.
     * @param string|int|array $keys A key or array of keys to pick.
     * @return array
     */
    function array_pick($array, $keys)
    {
        $keys = (array)$keys;
        return array_intersect_key($array, array_flip($keys));
    }
}

/**
 * Retrieve cron jobs ready to be run.
 *
 * @since 4.17
 *
 * @return array Cron jobs ready to be run.
 */
function wpra_get_ready_cron_jobs() {
    if (function_exists('wp_get_ready_cron_jobs')) {
        return wp_get_ready_cron_jobs();
    }

    $pre = apply_filters('pre_get_ready_cron_jobs', null);
    if ($pre !== null) {
        return $pre;
    }

    $crons = _get_cron_array();

    if ($crons === false) {
        return [];
    }

    $keys = array_keys($crons);
    $gmt_time = microtime(true);

    if (isset($keys[0]) && $keys[0] > $gmt_time) {
        return [];
    }

    $results = [];
    foreach ($crons as $timestamp => $cronhooks) {
        if ($timestamp > $gmt_time) {
            break;
        }

        $results[$timestamp] = $cronhooks;
    }

    return $results;
}

/**
 * Retrieves the translation for some text from the "wprss" domain.
 *
 * @since 4.17.3
 *
 * @param string $text The text whose translation to retrieve.
 *
 * @return string
 */
function wprss_translate($text)
{
    $translations = get_translations_for_domain('wprss');

    return $translations->translate($text);
}

/**
 * Retrieves the singular or plural translation for some text from the "wprss" domain.
 *
 * @since 4.17.3
 *
 * @param string $single The singular text.
 * @param string $plural The plural text.
 * @param int    $number The amount, used to determine whether the singular or plural translation will be retrieved.
 *
 * @return string
 */
function wprss_translate_n($single, $plural, $number)
{
    $translations = get_translations_for_domain('wprss');

    return $translations->translate_plural($single, $plural, $number);
}
