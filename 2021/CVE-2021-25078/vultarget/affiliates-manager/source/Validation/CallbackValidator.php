<?php
/**
 * @author John Hargrove
 * 
 * Date: 12/12/10
 * Time: 2:57 PM
 */

class WPAM_Validation_CallbackValidator implements WPAM_Validation_IValidator
{
	private $errorMsg;
	private $callback;

	public function __construct($errorMsg, $callback)
	{
		if (!is_callable($callback))
			throw new Exception( __( "Argument 'callback' must be callable", 'affiliates-manager' ) );

		$this->errorMsg = $errorMsg;
		$this->callback = $callback;
	}

	function getError() {
		return $this->errorMsg;
	}

	function isValid($value) {
		return call_user_func($this->callback, $value);
	}
}
