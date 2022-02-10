<?php
/**
 * Custom KSES to allow some otherwise excluded attributes.
 *
 * @category Utilities
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Execute KSES post on strings, otherwise, return as is.
 *
 * @param string $string Any string.
 *
 * @return Value passed or cleaned string
 */
function mc_kses_post( $string ) {
	if ( ! is_string( $string ) ) {
		return $string;
	} else {
		return wp_kses( $string, 'mycalendar' );
	}
}

add_filter( 'wp_kses_allowed_html', 'mc_allowed_tags', 10, 2 );
/**
 * My Calendar needs to allow input and select in posts and a variety of other key elements; also provide support for schema.org data.
 * Call using wp_kses( $data, 'mycalendar' );
 *
 * @param array  $tags Original allowed tags.
 * @param string $context Custom context for My Calendar to avoid running elsewhere.
 *
 * @return return array tags
 */
function mc_allowed_tags( $tags, $context ) {
	if ( 'mycalendar' === $context ) {
		global $allowedposttags;
		$tags = $allowedposttags;

		if ( current_user_can( 'unfiltered_html' ) ) {
			$tags['input'] = array(
				'type'             => true,
				'value'            => true,
				'name'             => true,
				'class'            => true,
				'aria-labelledby'  => true,
				'aria-describedby' => true,
				'disabled'         => true,
				'readonly'         => true,
				'min'              => true,
				'max'              => true,
				'id'               => true,
				'checked'          => true,
				'required'         => true,
			);

			$tags['select'] = array(
				'name'  => true,
				'id'    => true,
				'class' => true,
			);

			$formtags     = ( isset( $tags['form'] ) && is_array( $tags['form'] ) ) ? $tags['form'] : array();
			$tags['form'] = array_merge(
				$formtags,
				array(
					'action' => true,
					'method' => true,
					'class'  => true,
					'id'     => true,
				)
			);
		}

		$tags['span'] = array_merge(
			$tags['span'],
			array(
				'itemprop'  => true,
				'itemscope' => true,
				'itemtype'  => true,
			)
		);

		$tags['button'] = array_merge(
			$tags['button'],
			array(
				'name'     => true,
				'type'     => true,
				'disabled' => true,
				'class'    => true,
			)
		);

		$tags['div'] = array_merge(
			$tags['div'],
			array(
				'class'     => true,
				'id'        => true,
				'aria-live' => true,
			)
		);

		$tags['fieldset'] = array_merge( $tags['fieldset'], array() );
		$tags['legend']   = array_merge( $tags['legend'], array() );
		$tags['p']        = array_merge(
			$tags['p'],
			array(
				'class' => true,
			)
		);

		$tags['img'] = array_merge(
			$tags['img'],
			array(
				'class'    => true,
				'src'      => true,
				'alt'      => true,
				'width'    => true,
				'height'   => true,
				'id'       => true,
				'longdesc' => true,
				'tabindex' => true,
			)
		);

		$tags['iframe'] = array(
			'width'       => true,
			'height'      => true,
			'src'         => true,
			'frameborder' => true,
		);

		$tags['a'] = array(
			'aria-labelledby'  => true,
			'aria-describedby' => true,
			'href'             => true,
			'class'            => true,
			'target'           => true,
		);
	}

	return apply_filters( 'mc_kses_post', $tags );
}
