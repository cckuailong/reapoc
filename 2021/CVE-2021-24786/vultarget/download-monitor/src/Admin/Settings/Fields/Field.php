<?php

abstract class DLM_Admin_Fields_Field {

	/** @var String */
	private $name;

	/** @var String */
	private $value;

	/** @var String */
	private $placeholder;

	/**
	 * DLM_Admin_Fields_Field constructor.
	 *
	 * @param String $name
	 * @param String $value
	 * @param String $placeholder
	 */
	public function __construct( $name, $value, $placeholder ) {
		$this->name        = $name;
		$this->value       = $value;
		$this->placeholder = $placeholder;
	}

	/**
	 * @return String
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param String $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @return String
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @param String $value
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}

	/**
	 * @return String
	 */
	public function get_placeholder() {
		return $this->placeholder;
	}

	/**
	 * @param String $placeholder
	 */
	public function set_placeholder( $placeholder ) {
		$this->placeholder = $placeholder;
	}

	/**
	 * Echo the placeholder
	 */
	public function e_placeholder() {
		$placeholder = $this->get_placeholder();
		echo ( ! empty( $placeholder ) ) ? 'placeholder="' . esc_attr_e( $placeholder ) . '"' : '';
	}

	abstract public function render();

}