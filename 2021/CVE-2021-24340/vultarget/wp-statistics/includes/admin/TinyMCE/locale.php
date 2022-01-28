<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//Load TinyMCE Plugin translation
function wp_statistic_tinymce_plugin_translation() {
    $lang = \WP_STATISTICS\TinyMCE::lang();
    $translated = $lang['translate'];
    return $translated;
}
$strings = wp_statistic_tinymce_plugin_translation();