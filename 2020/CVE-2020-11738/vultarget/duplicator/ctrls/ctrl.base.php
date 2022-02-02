<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

require_once(DUPLICATOR_PLUGIN_PATH.'/classes/utilities/class.u.php');

//Enum used to define the various test statues
final class DUP_CTRL_Status
{
	const ERROR		= -2;
	const FAILED	= -1;
	const UNDEFINED	= 0;
	const SUCCESS	= 1;
}

/**
 * Base class for all controllers
 *
 * @package Duplicator
 * @subpackage classes/ctrls
 */
class DUP_CTRL_Base
{
	//Represents the name of the Nonce Action
	public $Action;
	//The return type valiad options: PHP, JSON-AJAX, JSON
	public $returnType = 'JSON-AJAX';

	public function setResponseType($type)
	{
		$opts = array('PHP', 'JSON-AJAX', 'JSON');
		if (!in_array($type, $opts)) {
			throw new Exception('The $type param must be one of the following: '.implode(',', $opts).' for the following function ['.__FUNCTION__.']');
		}
		$this->returnType = $type;
	}

	public function postParamMerge($post)
	{
		$post = is_array($post) ? $post : array();
		return array_merge($_POST, $post);
	}

	//Merges $_GET params with custom parameters.
	public function getParamMerge($params)
	{
		$params = is_array($params) ? $params : array();
		return array_merge($_GET, $params);
	}
}

/**
 * A class structer used to report on controller methods
 *
 * @package Duplicator
 * @subpackage classes/ctrls
 */
class DUP_CTRL_Report
{
	//Properties
	public $runTime;
	public $returnType;
	public $results;
	public $status;

}

/**
 * A class used format all controller responses in a consistent format.  Every controller response will
 * have a Report and Payload structer.  The Payload is an array of the result response.  The Report is used
 * report on the overall status of the controller method
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package Duplicator
 * @subpackage classes/ctrls
 * @copyright (c) 2017, Snapcreek LLC
 *
 */
class DUP_CTRL_Result
{
	//Properties
	public $report;
	public $payload;
	private $timeStart;
	private $timeEnd;
	private $CTRL;

	function __construct(DUP_CTRL_Base $CTRL_OBJ)
	{
		DUP_Util::hasCapability('export');
		$this->timeStart = $this->microtimeFloat();
		$this->CTRL		 = $CTRL_OBJ;

		//Report Data
		$this->report				 = new DUP_CTRL_Report();
		$this->report->returnType	 = $CTRL_OBJ->returnType;
	}

	/**
	 * Used to process a controller request
	 *
	 * @param object $payload The response object that will be returned
	 * @param enum $test The status of a response
	 *
	 * @return object || JSON  Returns a PHP object or json encoded object
	 */
	public function process($payload, $test = DUP_CTRL_Status::UNDEFINED)
	{
		if (is_array($this->payload)) {
			$this->payload[]		 = $payload;
			$this->report->results	 = count($this->payload);
		} else {
			$this->payload			 = $payload;
			$this->report->results	 = (is_array($payload)) ? count($payload) : 1;
		}

		$this->report->status = $test;
		$this->getProcessTime();

		switch ($this->CTRL->returnType) {
			case 'JSON' :
				return DupLiteSnapJsonU::wp_json_encode($this);
				break;
			case 'PHP' :
				return $this;
				break;
			default:
                wp_send_json($this);
				break;
		}
	}

	/**
	 * Used to process an error response
	 *
	 * @param object $exception The PHP exception object
	 *
	 * @return object || JSON  Returns a PHP object or json encoded object
	 */
	public function processError($exception)
	{
		$payload			 = array();
		$payload['Message']	 = $exception->getMessage();
		$payload['File']	 = $exception->getFile();
		$payload['Line']	 = $exception->getLine();
		$payload['Trace']	 = $exception->getTraceAsString();
		$this->process($payload, DUP_CTRL_Status::ERROR);
		die(DupLiteSnapJsonU::wp_json_encode($this));
	}

	private function getProcessTime()
	{
		$this->timeEnd			 = $this->microtimeFloat();
		$this->report->runTime	 = $this->timeEnd - $this->timeStart;
	}

	private function microtimeFloat()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float) $usec + (float) $sec);
	}
}