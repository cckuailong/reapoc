<?php
namespace PowerpackElementsLite\Modules\QueryControl;

use PowerpackElementsLite\Base\Module_Base;
use PowerpackElementsLite\Controls\Control_Query as Query;

// Elementor Classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Module
 *
 * @since  1.2.9
 */
class Module extends Module_Base {

	/**
	 * Module constructor.
	 *
	 * @since 1.2.9
	 * @param array $args
	 */
	public function __construct() {
		parent::__construct();

		// ACF 5 and up
		if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' ) ) {
			$this->add_component( 'acf', new Types\Acf() );
		}

		// Pods
		if ( function_exists( 'pods' ) ) {
			$this->add_component( 'pods', new Types\Pods() );
		}

		// Toolset
		if ( function_exists( 'wpcf_admin_fields_get_groups' ) ) {
			$this->add_component( 'toolset', new Types\Toolset() );
		}

		$this->add_component( 'posts', new Types\Posts() );
		$this->add_component( 'terms', new Types\Terms() );
		$this->add_component( 'authors', new Types\Authors() );
		$this->add_component( 'users', new Types\Users() );
		$this->add_component( 'templates', new Types\Templates() );
		$this->add_component( 'templates-all', new Types\Templates_All() );
		$this->add_component( 'templates-page', new Types\Templates_Page() );
		$this->add_component( 'templates-section', new Types\Templates_Section() );
		$this->add_component( 'templates-widget', new Types\Templates_Widget() );

		$this->add_actions();
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'query-control';
	}

	/**
	 * Add Actions
	 * 
	 * Registeres actions to Elementor hooks
	 *
	 * @since  1.2.9
	 * @return void
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	/**
	 * Calls function depending on ajax query data
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function ajax_call_filter_autocomplete( array $data ) {

		if ( empty( $data['query_type'] ) || empty( $data['q'] ) ) {
			throw new \Exception( 'Bad Request' );
		}

		$results = $this->get_component( $data['query_type'] )->get_autocomplete_values( $data );

		return [
			'results' => $results,
		];
	}

	/**
	 * Calls function to get value titles depending on ajax query type
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function ajax_call_control_value_titles( array $request ) {

		$results = $this->get_component( $request['query_type'] )->get_value_titles( $request );

		return $results;
	}

	/**
	 * Register Elementor Ajax Actions
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'pp_query_control_value_titles', [ $this, 'ajax_call_control_value_titles' ] );
		$ajax_manager->register_ajax_action( 'pp_query_control_filter_autocomplete', [ $this, 'ajax_call_filter_autocomplete' ] );
	}
}
