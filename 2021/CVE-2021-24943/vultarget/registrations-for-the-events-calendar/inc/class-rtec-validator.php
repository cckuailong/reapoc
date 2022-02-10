<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class RTEC_Validator
 */
class RTEC_Validator {

	/**
	 * @param $subject
	 * @param $min
	 * @param $max
	 *
	 * @return bool
	 */
	static public function length( $subject, $min, $max )
	{
		$working_max = $max;
		$working_min = $min;

		if ( $working_max === 'no-max' || $working_max > 1000 ) {
			$working_max = 1000;
		}

		if ( $working_min < 0 ) {
			$working_min = 0;
		}

		if ( strlen( $subject ) >= (int)$working_min && strlen( $subject ) <= (int)$working_max ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $subject
	 * @param $minval
	 * @param $maxval
	 *
	 * @return bool
	 */
	static public function numval( $subject, $minval, $maxval )
	{
		$working_max = $maxval;
		$working_min = $minval;

		if ( $working_max === 'no-max' || $working_max > 9999999 ) {
			$working_max = 9999999;
		}

		if ( $working_min === 'no-min' || $working_min < -9999999 ) {
			$working_min = -9999999;
		}

		if ( $subject >= (int)$working_min && $subject <= (int)$working_max ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $subject
	 *
	 * @return bool|string
	 */
	static public function email( $subject )
	{
		return is_email( $subject );
	}

	/**
	 * @param $subject
	 * @param $acceptable_counts
	 * @param string $count_what
	 *
	 * @return bool
	 */
	static public function count( $subject, $acceptable_counts, $count_what = 'numbers' )
	{
		$working_counts = $acceptable_counts;
		if ( $count_what === 'numbers') {
			$stripped_subject = preg_replace( '/\D/', '', $subject );

		} elseif ( $count_what === 'letters' ) {
			$stripped_subject = str_replace( "/^\p{L}+$/ui", '', $subject );
        } else {
			$stripped_subject = $subject;
		}

		if ( ! is_array( $working_counts ) ) {
			$working_counts = explode( ',', $working_counts );
		}

		foreach( $working_counts as $acceptable_count ) {

			if ( strlen( $stripped_subject ) === (int)$acceptable_count ) {
				return true;
			}

		}

		if ( $count_what === 'letters' ) return true;

		return false;
	}

	/**
	 * @param $first_val
	 * @param $second_val
	 * @param string $strictness
	 *
	 * @return bool
	 */
	static public function num_equality( $first_val, $second_val, $strictness = 'strict' )
	{
		if ( $strictness === 'strict' ) {
			return ( (int)$first_val === (int)$second_val );
		} else {
			return (int)$second_val > 0;
		}
	}

	static public function google_recaptcha( $recaptcha_response, $secret_key )
	{
		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => $secret_key,
					'response'     => $recaptcha_response,
				)
			)
		);

		$response = json_decode( $response['body'] );

		return (isset( $response->success ) && $response->success === true);
	}

}