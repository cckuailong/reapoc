<?php

namespace TEC\Tickets\Commerce\Reports;

use Tribe__Template;

/**
 * Class Report_Abstract
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Reports
 */
abstract class Report_Abstract {
	/**
	 * The Shortcode Slug inside of WordPress.
	 *
	 * @since 5.2.0
	 *
	 * @return string
	 */
	public static function get_page_wp_slug() {
		return 'tec-tickets-' . static::get_page_slug();
	}

	/**
	 * Fetches the Page slug
	 *
	 * @since 5.2.0
	 *
	 *
	 * @return string
	 */
	public static function get_page_slug() {
		return static::$page_slug;
	}

	/**
	 * Set of template variable used to generate this shortcode.
	 *
	 * @since 5.2.0
	 *
	 * @var array
	 */
	protected $template_vars = [];

	/**
	 * Stores the instance of the template engine that we will use for rendering the page.
	 *
	 * @since 5.2.0
	 *
	 * @var Tribe__Template
	 */
	protected $template;

	/**
	 * Gets the template instance used to setup the rendering of the page.
	 *
	 * @since 5.2.0
	 *
	 * @return Tribe__Template
	 */
	public function get_template() {
		if ( empty( $this->template ) ) {
			$this->template = new Tribe__Template();
			$this->template->set_template_origin( \Tribe__Tickets__Main::instance() );
			$this->template->set_template_folder( 'src/admin-views/commerce/reports' );
			$this->template->set_template_context_extract( true );
		}

		return $this->template;
	}

	/**
	 * Method used to save the template vars for this instance of shortcode.
	 *
	 * @since 5.2.0
	 *
	 * @return void
	 */
	abstract public function setup_template_vars();

	/**
	 * Calls the template vars setup and returns after filtering.
	 *
	 * @since 5.2.0
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
	 * @since 5.2.0
	 *
	 * @param array $template_vars Which set of variables we are passing to the filters.
	 *
	 * @return array
	 */
	public function filter_template_vars( array $template_vars = [] ) {
		/**
		 * Applies a filter to template vars for this shortcode.
		 *
		 * @since 5.2.0
		 *
		 * @param array  $template_vars Current set of callbacks for arguments.
		 * @param static $instance      Which instance of shortcode we are dealing with.
		 */
		$template_vars = apply_filters( 'tec_tickets_commerce_reports_template_vars', $template_vars, $this );

		$page_slug = static::get_page_slug();

		/**
		 * Applies a filter to template vars for this shortcode, using ID and gateway.
		 *
		 * @since 5.2.0
		 *
		 * @param array  $template_vars Current set of callbacks for arguments.
		 * @param static $instance      Which instance of shortcode we are dealing with.
		 */
		return (array) apply_filters( "tec_tickets_commerce_reports_{$page_slug}_template_vars", $template_vars, $this );
	}

}