<?php


class Tribe__Tickets__REST__V1__Headers__Base implements Tribe__REST__Headers__Base_Interface {

	/**
	 * @var string
	 */
	protected $api_version_header = 'X-ET-API-VERSION';
	/**
	 * @var string
	 */
	protected $api_root_header = 'X-ET-API-ROOT';
	/**
	 * @var string
	 */
	protected $api_origin_header = 'X-ET-API-ORIGIN';
	/**
	 * @var string
	 */
	protected $api_version_meta_name = 'et-api-version';
	/**
	 * @var string
	 */
	protected $api_origin_meta_name = 'et-api-origin';

	/**
	 * Returns the header that the REST API will print on the page head to report
	 * its version.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_api_version_header() {
		return $this->api_version_header;
	}

	/**
	 * Returns the header the REST API will print on the page head to report its root
	 * url.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_api_root_header() {
		return $this->api_root_header;
	}

	/**
	 * Returns the header the REST API will print on the page head to report its origin
	 * url. Normaly the home_url()
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_api_origin_header() {
		return $this->api_origin_header;
	}

	/**
	 * Returns the `name` of the meta tag that will be printed on the page to indicate
	 * the REST API version.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_api_version_meta_name() {
		return $this->api_version_meta_name;
	}

	/**
	 * Returns the `name` of the meta tag that will be printed on the page to indicate
	 * the REST API Origin URL.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_api_origin_meta_name() {
		return $this->api_origin_meta_name;
	}

	/**
	 * Returns the REST API URL.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_rest_url() {
		if ( is_single() && tribe_events_product_is_ticket( get_the_ID() ) ) {
			return tribe_tickets_rest_url( 'tickets/' . Tribe__Main::post_id_helper() );
		}

		/** @var WP_Query $wp_query */
		if ( ! $wp_query = tribe_get_global_query_object() ) {
			return;
		}

		return tribe_tickets_rest_url();
	}

	/**
	 * Returns the REST API Origin Site.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public function get_rest_origin_url() {
		return home_url();
	}
}
