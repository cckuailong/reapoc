<?php
/**
 * Abstract class of module helper
 * Module helper has all the functions that are needed in module workflow
 * Besides it contains the methods to build html elements
 */
abstract class helperUms {
	protected $_code = '';
	protected $_module = '';
	/**
	 * Construct helper class
	 * @param string $code 
	 */
	public function __construct($code) {
		$this->setCode($code);
	}
	/**
	 * Init function
	 */
	public function init(){

	}
	/**
	 * Set the helper name
	 * @param string $code 
	 */
	public function setCode($code) {
		$this->_code = $code;
	}
	/**
	 * Get the helper name
	 * @return string 
	 */
	public function getCode() {
		return $this->_code;
	}
}

