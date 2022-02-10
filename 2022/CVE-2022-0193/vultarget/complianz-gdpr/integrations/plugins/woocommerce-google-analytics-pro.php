<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Make sure it's set as not anonymous when tracking enabled
 * @param bool $stats_category_required
 */
function cmplz_wc_google_analytics_pro_set_statistics_required( $stats_category_required ){
	$settings = get_option('woocommerce_google_analytics_pro_settings');
	if ( $settings && isset( $settings['enable_displayfeatures']) && $settings['enable_displayfeatures'] === 'yes' ) {
		$stats_category_required = true;
	}
	return $stats_category_required;
}
add_filter('cmplz_cookie_warning_required_stats', 'cmplz_wc_google_analytics_pro_set_statistics_required');


/**
 * Set analytics as suggested stats tool in the wizard
 */

add_filter( 'cmplz_default_value', 'cmplz_wc_google_analytics_pro_set_default', 20, 2 );
function cmplz_wc_google_analytics_pro_set_default( $value, $fieldname ) {
	if ( $fieldname == 'compile_statistics' ) {
		return "google-analytics";
	}

	return $value;
}

/**
 * If display ads is enabled, ensure a marketing category is added to the banner
 * @param bool $uses_marketing_cookies
 *
 * @return bool|mixed
 */
function cmplz_wc_google_analytics_pro_uses_marketing_cookies( $uses_marketing_cookies ) {
	$settings = get_option('woocommerce_google_analytics_pro_settings');
	if ( $settings && isset( $settings['enable_displayfeatures']) && $settings['enable_displayfeatures'] === 'yes' ) {
		$uses_marketing_cookies = true;
	}

	return $uses_marketing_cookies;
}
add_filter( 'cmplz_uses_marketing_cookies', 'cmplz_wc_google_analytics_pro_uses_marketing_cookies', 20, 2 );

/**
 * Add markers to the statistics markers list
 * @param $markers
 *
 * @return array
 */
function cmplz_wc_google_analytics_pro_stats_markers( $markers ) {
	$markers['google-analytics'][] = 'wc_google_analytics_pro_loaded';
	$markers['google-analytics'][] = "ga( 'send', 'pageview' )";
	$markers['google-analytics'][] = '_gaq.push';
	$markers['google-analytics'][] = 'stats.g.doubleclick.net/dc.js';
	$markers['google-analytics'][] = 'gaProperty';

	//we want TM to be treated as stats
	$markers['google-analytics'][] = 'googletagmanager.com';

	return $markers;
}
add_filter( 'cmplz_stats_markers', 'cmplz_wc_google_analytics_pro_stats_markers', 20, 1 );

/**
 * Entirely remove tag manager blocking if anonymous
 */

function cmplz_wc_google_analytics_pro_drop_tm_blocking(){
	if ( COMPLIANZ::$cookie_admin->statistics_privacy_friendly() ) {
		remove_filter( 'cmplz_known_script_tags', 'cmplz_googletagmanager_script' );
	}
}
add_action( 'init', 'cmplz_wc_google_analytics_pro_drop_tm_blocking');

/**
 * Block inline script
 * @param $tags
 *
 * @return array
 */
function cmplz_wc_google_analytics_pro_script( $tags ) {
	$tags[] = 'GoogleAnalyticsObject';
	$tags[] = 'add_to_cart_button';
	$tags[] = "ga( 'send', 'pageview' )";
	$tags[] = '_gaq.push';
	$tags[] = 'stats.g.doubleclick.net/dc.js';
	$tags[] = 'gaProperty';
	$tags[] = 'wc_ga_pro';

	return $tags;
}
add_filter( 'cmplz_known_script_tags', 'cmplz_wc_google_analytics_pro_script' );

/**
 * If "use advertising features" is enabled, block as if it's marketing
 * @param array $classes
 *
 * @return array
 */
function cmplz_wc_google_analytics_pro_script_classes( $classes ){
	$settings = get_option('woocommerce_google_analytics_pro_settings');

	if ( $settings && isset( $settings['enable_displayfeatures']) && $settings['enable_displayfeatures'] !== 'yes' ) {
		if (!in_array('cmplz-stats', $classes )) {
			$classes[] = 'cmplz-stats';
		}
	}

	if ( $settings && isset( $settings['enable_displayfeatures']) && $settings['enable_displayfeatures'] === 'yes' ) {
		if (in_array( 'cmplz-native' , $classes) ) {
			unset($classes[array_search('cmplz-native', $classes)]);
		}
	}

	return $classes;
}
add_filter( 'cmplz_statistics_script_classes', 'cmplz_wc_google_analytics_pro_script_classes', 10, 1  );


/**
 * Remove stuff which is not necessary anymore
 *
 * */

function cmplz_wc_google_analytics_pro_remove_actions() {
	remove_action( 'cmplz_notice_compile_statistics', 'cmplz_show_compile_statistics_notice', 10 );
}
add_action( 'init', 'cmplz_wc_google_analytics_pro_remove_actions' );

/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */
function cmplz_wc_google_analytics_pro_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice( sprintf( __( "You use %s, which means the answer to this question should be Google Analytics.", 'complianz-gdpr' ), 'WooCommerce Google Analytics Pro' ) );
}
add_action( 'cmplz_notice_compile_statistics', 'cmplz_wc_google_analytics_pro_show_compile_statistics_notice', 10, 1 );


/**
 * Hide the stats configuration options when wc_google_analytics_pro is enabled.
 *
 * @param $fields
 *
 * @return mixed
 */

function cmplz_wc_google_analytics_pro_filter_fields( $fields ) {
	unset( $fields['configuration_by_complianz'] );
	unset( $fields['UA_code'] );
	return $fields;
}
add_filter( 'cmplz_fields', 'cmplz_wc_google_analytics_pro_filter_fields' );

/**
 * Make sure there's no warning about configuring GA anymore
 *
 * @param $warnings
 *
 * @return mixed
 */

function cmplz_wc_google_analytics_pro_filter_warnings( $warnings ) {
	unset($warnings['ga-needs-configuring']);
	return $warnings;
}

add_filter( 'cmplz_warning_types', 'cmplz_wc_google_analytics_pro_filter_warnings' );

