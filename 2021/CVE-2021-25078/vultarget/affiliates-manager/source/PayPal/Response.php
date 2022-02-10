<?php
/**
 * @author John Hargrove
 * 
 * Date: 1/3/11
 * Time: 9:06 PM
 */

require_once "ResponseError.php";

class WPAM_PayPal_Response
{
	const ACK_SUCCESS = 'Success',
		ACK_SUCCESS_WITH_WARNING = 'SuccessWithWarning',
		ACK_PARTIAL_SUCCESS = 'PartialSuccess',
		ACK_FAILURE = 'Failure',
		ACK_FAILURE_WITH_WARNING = 'FailureWithWarning',
		ACK_WARNING = 'Warning';


	public function IsSuccess() { return in_array($this->ack, array(self::ACK_SUCCESS, self::ACK_SUCCESS_WITH_WARNING, self::ACK_PARTIAL_SUCCESS)); }
	public function IsFailure() { return in_array($this->ack, array(self::ACK_FAILURE, self::ACK_FAILURE_WITH_WARNING, self::ACK_WARNING)); }
	
	private $timestamp;     public function getTimestamp() { return $this->timestamp; }
	private $correlationId; public function getCorrelationId() { return $this->correlationId; }
	private $ack;           public function getAck() { return $this->ack; }
	private $version;       public function getVersion() { return $this->version; }
	private $build;         public function getBuild() { return $this->build; }
	private $errors;        public function getErrors() { return $this->errors; }
	private $rawResponse;   public function getRawResponse() { return $this->rawResponse; }

	public function __construct($responseString)
	{
		$this->parse($responseString);
		$this->rawResponse = $responseString;
	}

	private function parse($responseString)
	{
		$responseArray = $this->parseResponseString($responseString);

		$this->timestamp = strtotime($responseArray['TIMESTAMP']);
		$this->correlationId = $responseArray['CORRELATIONID'];
		$this->ack = $responseArray['ACK'];
		$this->version = $responseArray['VERSION'];
		$this->build = $responseArray['BUILD'];

		$errorIndex = 0;
		while (array_key_exists('L_ERRORCODE'.$errorIndex, $responseArray))
		{
			$this->errors[] = new WPAM_PayPal_ResponseError(
				$responseArray['L_ERRORCODE'.$errorIndex],
				$responseArray['L_SHORTMESSAGE'.$errorIndex],
				$responseArray['L_LONGMESSAGE'.$errorIndex],
				$responseArray['L_SEVERITYCODE'.$errorIndex]
			);
			$errorIndex++;
		}
	}

	private function parseResponseString($response)
	{
		$variables = explode('&', $response);
		$responseArray = array();
		foreach ($variables as $variable)
		{
			list ($key, $value) = explode('=', $variable);
			$key = urldecode($key);
			$value = urldecode($value);
			$responseArray[$key] = $value;
		}
		return $responseArray;
	}
}
