<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Base
 *
 * @since  1.2.9
 */
class Type_Base extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {}

	/**
	 * Gets autocomplete values
	 *
	 * @since  1.2.9
	 * @return array
	 */
	protected function get_autocomplete_values( array $data ) {}

	/**
	 * Gets control values titles
	 *
	 * @since  1.2.9
	 * @return array
	 */
	protected function get_value_titles( array $request ) {}
}
