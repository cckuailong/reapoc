<?php

use RebelCode\Wpra\Core\Templates\TwigTemplate;
use Twig\Environment;

if (defined('WPRSS_TWIG_MIN_PHP_VERSION')) {
    return;
}

// Minimum version requirement for twig
define('WPRSS_TWIG_MIN_PHP_VERSION', '5.4.0');

/**
 * Returns whether twig can be used.
 *
 * @since 4.12.1
 *
 * @return bool True if twig can be used, false if not.
 */
function wprss_can_use_twig()
{
    return version_compare(PHP_VERSION, WPRSS_TWIG_MIN_PHP_VERSION, '>=');
}

/**
 * Retrieves the twig instance for WP RSS Aggregator.
 *
 * @since 4.12
 *
 * @return Environment The twig instance.
 */
function wprss_twig()
{
    return wpra_get('twig');
}

/**
 * Loads a WPRSS twig template.
 *
 * @since 4.12
 *
 * @param string $template The template name.
 *
 * @return TwigTemplate
 */
function wprss_load_template($template)
{
    return wpra_get('twig/collection')[$template];
}

/**
 * Loads and renders a WPRSS template.
 *
 * @since 4.12
 *
 * @param string $template The template name.
 * @param array  $context  The template context.
 *
 * @return string
 */
function wprss_render_template($template, $context = [])
{
    return wprss_load_template($template)->render($context);
}
