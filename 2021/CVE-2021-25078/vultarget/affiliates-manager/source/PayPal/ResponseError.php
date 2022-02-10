<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/3/11
 * Time: 9:27 PM
 */


class WPAM_PayPal_ResponseError
{
	private $code;         public function getCode() { return $this->code; }
	private $shortMessage; public function getShortMessage() { return $this->shortMessage; }
	private $longMessage;  public function getLongMessage() { return $this->longMessage; }
	private $severityCode; public function getSeverityCode() { return $this->severityCode; }

	public function __construct($code, $shortMessage, $longMessage, $severityCode)
	{
		$this->code = $code;
		$this->shortMessage = $shortMessage;
		$this->longMessage = $longMessage;
		$this->severityCode = $severityCode;
	}
}
