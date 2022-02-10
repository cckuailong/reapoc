<?php


class Drip
{
	protected $api_endpoint = 'https://api.getdrip.com/v2';

	protected static $eventSubscriptions = [];
	protected static $receivedWebhook = false;
	
	protected $token      = false;
	protected $accountID  = false;
	protected $verify_ssl = false;

	public function __construct($token, $accountID)
	{
		$this->token     = $token;
		$this->accountID = $accountID;
	}

	public function post($api_method, $args, $timeout=10)
	{
		return $this->makeRequest('post', $api_method, $args, $timeout);
	}

	public function get($api_method, $args=array(), $timeout=10)
	{
		return $this->makeRequest('get', $api_method, $args, $timeout);
	}

	public function delete($api_method, $args=array(), $timeout=10)
	{
		return $this->makeRequest('delete', $api_method, $args, $timeout);
	}

	public function disableSSLVerification()
	{
		$this->verify_ssl = false;
	}

	public static function subscribeToWebhook($event, callable $callback)
	{
		if (!isset(self::$eventSubscriptions[$event])) self::$eventSubscriptions[$event] = [];
		self::$eventSubscriptions[$event][] = $callback;

		self::receiveWebhook();
	}

	public static function receiveWebhook($input=null)
	{
		if (is_null($input)) {
			if (self::$receivedWebhook!==false) {
				$input = self::$receivedWebhook;
			} else {
				$input = file_get_contents("php://input");
			}
		}

		if ($input) {
			return self::processWebhook($input);
		}	
		
		return false;
	}

	protected static function processWebhook($input) 
	{
		if ($input) {
			self::$receivedWebhook = $input;
			$result = json_decode($input, true);
			if ($result && isset($result['event'])) {
				self::dispatchWebhookEvent($result['event'], $result['data']);
				return $result;
			}
		}

		return false;
	}

	protected static function dispatchWebhookEvent($event, $data)
	{
		if (isset(self::$eventSubscriptions[$event])) {
			foreach(self::$eventSubscriptions[$event] as $callback) {
				$callback($data);
			}
			// reset subscriptions
			self::$eventSubscriptions[$event] = [];
		}
		return false;
	}

	protected function makeRequest($http_verb='post', $api_method, $args=array(), $timeout=10)
	{
		$url = $this->api_endpoint.'/'.$this->accountID.'/'.$api_method;
        
		if (function_exists('curl_init') && function_exists('curl_setopt')) {

			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
												'Accept: application/vnd.api+json',
												'Content-Type: application/vnd.api+json',
											]); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'DrewM/Drip (github.com/drewm/drip)');
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
			curl_setopt($ch, CURLOPT_USERPWD, $this->token.': '); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($ch, CURLOPT_URL, $url);

			switch($http_verb) {
				case 'post':
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args)); 
					break;

				case 'get':
					$query = http_build_query($args);
					curl_setopt($ch, CURLOPT_URL, $url.'?'.$query);
					break;

				case 'delete':
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;
			}

		    $result = curl_exec($ch); 

		    if(!curl_errno($ch)) {
			 	$info = curl_getinfo($ch);
			 	curl_close($ch);
			 	return new Response($info, $result);
			}

			$errno = curl_errno($ch);
			$error = curl_error($ch);

		    curl_close($ch); 

		    throw new \Exception($error, $errno);
		}else{
			throw new \Exception("cURL support is required, but can't be found.", 1);
		}
	}
}
