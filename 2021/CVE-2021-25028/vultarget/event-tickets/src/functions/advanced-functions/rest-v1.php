<?php
if ( ! function_exists( 'tribe_tickets_rest_url_prefix' ) ) {
	/**
	 * Returns TEC REST API URL prefix.
	 *
	 * @since 4.7.5
	 *
	 * @return string TEC REST API URL prefix; default `wp-json/tribe/tickets/v1`.
	 */
	function tribe_tickets_rest_url_prefix() {
		return tribe( 'tickets.rest-v1.main' )->get_url_prefix();
	}
}

if ( ! function_exists( 'tribe_tickets_rest_url' ) ) {
	/**
	 * Retrieves the URL to a TEC REST endpoint on a site.
	 *
	 * Note: The returned URL is NOT escaped.
	 *
	 * @since 4.7.5
	 *
	 * @global WP_Rewrite $wp_rewrite
	 *
	 * @param string      $path    Optional. TEC REST route. Default '/'.
	 * @param string      $scheme  Optional. Sanitization scheme. Default 'rest'.
	 * @param int         $blog_id Optional. Blog ID. Default of null returns URL for current blog.
	 *
	 * @return string Full URL to the endpoint.
	 */
	function tribe_tickets_rest_url( $path = '/', $scheme = 'rest', $blog_id = null ) {
		return tribe( 'tickets.rest-v1.main' )->get_url( $path, $scheme, $blog_id );
	}
}
