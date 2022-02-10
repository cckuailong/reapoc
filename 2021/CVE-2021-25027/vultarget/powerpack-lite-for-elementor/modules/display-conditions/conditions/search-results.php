<?php
namespace PowerpackElementsLite\Modules\DisplayConditions\Conditions;

// Powerpack Elements Classes
use PowerpackElementsLite\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * \Modules\DisplayConditions\Conditions\Search_Results
 *
 * @since  1.2.7
 */
class Search_Results extends Condition {

	/**
	 * Get Group
	 *
	 * Get the group of the condition
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_group() {
		return 'archive';
	}

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_name() {
		return 'search_results';
	}

	/**
	 * Get Title
	 *
	 * Get the title of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_title() {
		return __( 'Search', 'powerpack' );
	}

	/**
	 * Get Value Control
	 *
	 * Get the settings for the value control
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type'          => Controls_Manager::TEXT,
			'default'       => '',
			'placeholder'   => __( 'Keywords', 'powerpack' ),
			'description'   => __( 'Enter keywords, separated by commas, to condition the display on specific keywords and leave blank for any.', 'powerpack' ),
			'label_block'   => true,
		];
	}

	/**
	 * Check if keyword exists in phrase
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 *
	 * @param string    $keyword    Keyword to search
	 * @param string    $phrase     Phrase to search in
	 */
	protected function keyword_exists( $keyword, $phrase ) {
		return strpos( $phrase, trim( $keyword ) ) !== false;
	}

	/**
	 * Check condition
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 *
	 * @param string    $name       The control name to check
	 * @param string    $operator   Comparison operator
	 * @param mixed     $value      The control value to check
	 */
	public function check( $name, $operator, $value ) {
		$show = false;

		if ( is_search() ) {

			if ( empty( $value ) ) { // We're showing on all search pages

				$show = true;

			} else { // We're showing on specific keywords

				$phrase = get_search_query(); // The user search query

				if ( '' !== $phrase && ! empty( $phrase ) ) { // Only proceed if there is a query

					$keywords = explode( ',', $value ); // Separate keywords

					foreach ( $keywords as $index => $keyword ) {
						if ( $this->keyword_exists( trim( $keyword ), $phrase ) ) {
							$show = true;
							break;
						}
					}
				}
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
