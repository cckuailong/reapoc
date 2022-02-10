<?php

namespace TEC\Tickets\Commerce\Shortcodes;

use TEC\Tickets\Commerce\Gateways\Manager;
use Tribe\Shortcode\Shortcode_Abstract as Common_Shortcode_Abstract;

/**
 * Class Shortcode_Abstract
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Shortcodes
 */
abstract class Shortcode_Abstract extends Common_Shortcode_Abstract {
	/**
	 * Configures this instance of the shortcode.
	 *
	 * @since 5.1.9
	 */
	public function __construct() {
		$this->slug = static::get_wp_slug();
	}

	/**
	 * The Shortcode Slug inside of WordPress.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public static function get_wp_slug() {
		return 'tec_tickets_' . static::$shortcode_id;
	}

	/**
	 * Set of template variable used to generate this shortcode.
	 *
	 * @since 5.1.9
	 *
	 * @var array
	 */
	protected $template_vars = [];

	/**
	 * Stores the instance of the template engine that we will use for rendering the page.
	 *
	 * @since 5.1.9
	 *
	 * @var \Tribe__Template
	 */
	protected $template;

	/**
	 * Gets the template instance used to setup the rendering of the page.
	 *
	 * @since 5.1.9
	 *
	 * @return \Tribe__Template
	 */
	public function get_template() {
		if ( empty( $this->template ) ) {
			$this->template = new \Tribe__Template();
			$this->template->set_template_origin( \Tribe__Tickets__Main::instance() );
			$this->template->set_template_folder( 'src/views/v2/commerce' );
			$this->template->set_template_context_extract( true );
			$this->template->set_template_folder_lookup( true );
		}

		return $this->template;
	}

	/**
	 * Method used to save the template vars for this instance of shortcode.
	 *
	 * @since 5.1.9
	 *
	 * @return void
	 */
	abstract public function setup_template_vars();

	/**
	 * Gets the current active gateway slug.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_gateway_slug() {
		return (string) tribe( Manager::class )->get_current_gateway();
	}

	/**
	 * Calls the template vars setup and returns after filtering.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_template_vars() {
		$this->setup_template_vars();

		return (array) $this->filter_template_vars( $this->template_vars );
	}

	/**
	 * Enables filtering of the template variables.
	 *
	 * @since 5.1.9
	 *
	 * @param array $template_vars Which set of variables we are passing to the filters.
	 *
	 * @return array
	 */
	public function filter_template_vars( array $template_vars = [] ) {
		/**
		 * Applies a filter to template vars for this shortcode.
		 *
		 * @since 5.1.9
		 *
		 * @param array  $template_vars Current set of callbacks for arguments.
		 * @param static $instance      Which instance of shortcode we are dealing with.
		 */
		$template_vars = apply_filters( 'tec_tickets_commerce_shortcode_page_template_vars', $template_vars, $this );

		$shortcode_id = static::$shortcode_id;

		/**
		 * Applies a filter to template vars for this shortcode, using ID.
		 *
		 * @since 5.1.9
		 *
		 * @param array  $template_vars Current set of callbacks for arguments.
		 * @param static $instance      Which instance of shortcode we are dealing with.
		 */
		$template_vars = apply_filters( "tec_tickets_commerce_shortcode_{$shortcode_id}_page_template_vars", $template_vars, $this );

		$gateway = $this->get_gateway_slug();

		/**
		 * Applies a filter to template vars for this shortcode, using ID and gateway.
		 *
		 * @since 5.1.9
		 *
		 * @param array  $template_vars Current set of callbacks for arguments.
		 * @param static $instance      Which instance of shortcode we are dealing with.
		 */
		return (array) apply_filters( "tec_tickets_commerce_success_shortcode_{$shortcode_id}_page_{$gateway}_template_vars", $template_vars, $this );
	}

}