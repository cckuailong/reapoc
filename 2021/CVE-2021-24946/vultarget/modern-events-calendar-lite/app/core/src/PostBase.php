<?php

namespace MEC;

abstract class PostBase {

	/**
	 * @var int
	 */
	public $ID;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var array
	 */
	public $data;

	/**
	 * Constructor
	 *
	 * @param int|\WP_Post|array $data
	 */
	public function __construct( $data, $load_post = true ) {

		if ( is_numeric( $data ) && $load_post) {

			$data = get_post( $data );
		}elseif( is_numeric( $data ) && !$load_post ){

			$data = array(
				'ID' => $data,
			);
		}

		if ( is_a( $data, '\WP_Post' ) ) {

			$data = (array) $data;
		} elseif ( is_object( $data ) ) {

			$data = (array) $data;
		}

		$this->data = $data;
		$this->ID   = isset($this->data['ID']) ? $this->data['ID'] : 0;
	}

	/**
	 * @return int
	 */
	public function get_id() {

		return $this->ID;
	}

	/**
	 * @return int
	 */
	public function get_title() {

		return isset($this->data['title']) ? $this->data['title'] : get_the_title($this->ID);
	}

	/**
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_data( $key, $default = null ) {

		$v = isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;

		return apply_filters( 'mec_' . $this->type . '_get_data', $v, $key, $this->data, $default );
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_data( $key, $value ) {

		$value              = apply_filters( 'mec_' . $this->type . '_get_data', $value, $key, $this->data );
		$this->data[ $key ] = $value;
	}

	public function get_cached_all_data() {

		return $this->data;
	}

	/**
	 * @return array|int|\WP_Post|null
	 */
	public function to_array() {

		return $this->get_cached_all_data();
	}

	/**
	 * @param string $key
	 * @param bool   $return_cached
	 *
	 * @return mixed
	 */
	public function get_meta( $key, $return_cached = true ) {

		if(empty($key)){

			return null;
		}

		$data = $this->get_data( $key );
		if ( is_null( $data ) || !$return_cached ) {

			$data               = get_post_meta( $this->ID, $key, true );
			$this->data[ $key ] = $data;
		}

		return $data;
	}

	/**
	 * @param string $key
	 * @param        $value
	 *
	 * @return void
	 */
	public function set_meta( $key, $value ) {

		update_post_meta( $this->ID, $key, $value );
		$this->set_data( $key, $value );
	}

}
