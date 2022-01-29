<?php
/**
 * PHP Paypal IPN Integration Class
 * 6.25.2008 - Eric Wang, http://code.google.com/p/paypal-ipn-class-php/
 * 
 * This file provides neat and simple method to validate the paid result with Paypal IPN. 
 * It's NOT intended to make the paypal integration "plug 'n' play". 
 * It still requires the developer to understand the paypal process and know the variables 
 * you want/need to pass to paypal to achieve what you want.
 * 
 * @author		Eric Wang <eric.wzy@gmail.com>
 * @copyright  (C) 2008 - 2009 Eric.Wang
 * 
 */
/** filename of the IPN log */

if(!defined('LOG_FILE'))
	define('LOG_FILE', '.ipn_results.log');

if(!defined('SSL_P_URL'))
	define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');

if(!defined('SSL_SAND_URL'))
	define('SSL_SAND_URL','https://www.sandbox.paypal.com/cgi-bin/webscr');

class rm_paypal_class {
	
	public $ipn_status;                // holds the last status
	public $admin_mail; 				// receive the ipn status report pre transaction
	public $paypal_mail;				// paypal account, if set, class need to verify receiver
	public $txn_id;						// array: if the txn_id array existed, class need to verified the txn_id duplicate
	public $ipn_log;                    // bool: log IPN results to text file?
	public $ipn_response;              // holds the IPN response from paypal   
	public $ipn_data = array();         // array contains the POST values for IPN
	public $fields = array();          // array holds the fields to submit to paypal
	public $ipn_debug; 				// ipn_debug
	public $sandbox;
	
	// initialization constructor.  Called when class is created.
	function __construct() {
		$this->ipn_status = '';
		$this->admin_mail = null;
		$this->paypal_mail = null;
		$this->txn_id = null;
		$this->tax = null;
		$this->ipn_log = true;
		$this->ipn_response = '';
		$this->ipn_debug = false;
		$this->sandbox = 'no';
	}
        
    public function toggle_sandbox($trueorfalse)
    {
           $this->sandbox = $trueorfalse;
    }
        
	// adds a key=>value pair to the fields array, which is what will be 
	// sent to paypal as POST variables. 
	public function add_field($field, $value) {
		$this->fields["$field"] = $value;
	}
	// this function actually generates an entire HTML page consisting of
	// a form with hidden elements which is submitted to paypal via the 
	// BODY element's onLoad attribute.  We do this so that you can validate
	// any POST vars from you custom form before submitting to paypal.  So 
	// basically, you'll have your own form which is submitted to your script
	// to validate the data, which in turn calls this function to create
	// another hidden form and submit to paypal.
		
	// The user will briefly see a message on the screen that reads:
	// "Please wait, your order is being processed..." and then immediately
	// is redirected to paypal.
	public function submit_paypal_post() {
		$paypal_url = ($this->sandbox) ? SSL_SAND_URL : SSL_P_URL;
		ob_start();
		echo "<html>\n";
		echo "<head><title>".__('Processing Payment...','custom-registration-form-builder-with-submission-manager')."</title></head>\n";
		echo "<body>\n";
		echo "<center><h2>".__('Please wait, your order is being processed. You will be redirected to the PayPal website shortly.','custom-registration-form-builder-with-submission-manager')."</h2></center>\n";
		echo "<form target=\"_parent\" method=\"post\" name=\"paypal_form\" ";
		echo "action=\"".$paypal_url."\">\n";
		if (isset($this->paypal_mail))echo "<input type=\"hidden\" name=\"business\" value=\"$this->paypal_mail\"/>\n";
		foreach ($this->fields as $name => $value) {
			echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		echo "<center><br/><br/>".__('If you are not automatically redirected to PayPal within 5 seconds...', 'custom-registration-form-builder-with-submission-manager');
		echo "<br/><br/>\n";
		echo "<input type=\"submit\" value='".__('Click here', 'custom-registration-form-builder-with-submission-manager')."'></center>\n";
		
		echo "</form>\n";
                echo "<script>jQuery(document).ready(function(){ document.forms['paypal_form'].submit()});</script>";
		echo "</body></html>\n";
		return ob_get_contents();
		
	}
   
/**
 * validate the	IPN
 * 
 * @return bool IPN validation result
 */
	public function validate_ipn() {
		
		$hostname = gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] );
        
        /*
		if (! preg_match ( '/paypal\.com$/', $hostname )) {
			$this->ipn_status = 'Validation post isn\'t from PayPal';
			$this->log_ipn_results ( false );
			return false;
		}
        */
		
		if (isset($this->paypal_mail) && strtolower ( $_POST['receiver_email'] ) != strtolower(trim( $this->paypal_mail ))) {
			$this->ipn_status = "Receiver Email Not Match";
			$this->log_ipn_results ( false );
			return false;
		}
		
		if (isset($this->txn_id)&& in_array($_POST['txn_id'],$this->txn_id)) {
			$this->ipn_status = "txn_id have a duplicate";
			$this->log_ipn_results ( false );
			return false;
		}
		// parse the paypal URL
		$paypal_url = ($_POST['test_ipn'] == 1) ? SSL_SAND_URL : SSL_P_URL;
		$url_parsed = parse_url($paypal_url);        
		
		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = '';    
		foreach ($_POST as $field=>$value) { 
			$this->ipn_data["$field"] = $value;
			$post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
		}
		$post_string.="cmd=_notify-validate"; // append ipn command
		
		// open the connection to paypal
		if (isset($_POST['test_ipn']) )
			$fp = fsockopen ( 'ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60 );
		else
			$fp = fsockopen ( 'ssl://www.paypal.com', "443", $err_num, $err_str, 60 );
 
		if(!$fp) {
			// could not open the connection.  If logging is on, the error message
			// will be in the log.
			$this->ipn_status = "fsockopen error no. $err_num: $err_str";
			$this->log_ipn_results(false);       
			return false;
		} else { 
			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
			fputs($fp, "Host: $url_parsed[host]\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $post_string . "\r\n\r\n"); 
		
			// loop through the response from the server and append to variable
			while(!feof($fp)) { 
		   	$this->ipn_response .= fgets($fp, 1024); 
		   } 
		  fclose($fp); // close connection
		}
		
		// Invalid IPN transaction.  Check the $ipn_status and log for details.
		if (! preg_match("/VERIFIED/i",$this->ipn_response)) {
			$this->ipn_status = 'IPN Validation Failed';
			$this->log_ipn_results(false);   
			return false;
		} else {
			$this->ipn_status = "IPN VERIFIED";
			$this->log_ipn_results(true); 
			return true;
		}
	} 
   
	public function log_ipn_results($success) {
		$hostname = gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] );
		// Timestamp
		$text = '[' . date ( 'm/d/Y g:i A' ) . '] - ';
		// Success or failure being logged?
		if ($success)
			$this->ipn_status = $text . 'SUCCESS:' . $this->ipn_status . "\r\n";
		else
			$this->ipn_status = $text . 'FAIL: ' . $this->ipn_status . "\r\n";
			// Log the POST variables
		$this->ipn_status .= "[From:" . $hostname . "|" . $_SERVER ['REMOTE_ADDR'] . "]IPN POST Vars Received By Paypal_IPN Response API:\r\n";
		foreach ( $this->ipn_data as $key => $value ) {
			$this->ipn_status .= "<p>$key=$value </p>\r\n";
		}
		// Log the response from the paypal server
		$this->ipn_status .= "IPN Response from PayPal Server:\r\n" . $this->ipn_response;
		$this->write_to_log ();
	}
	
	public function write_to_log() {
		if (! $this->ipn_log)
			return; // is logging turned off?
        
        if(file_exists(LOG_FILE))
            @unlink(LOG_FILE);
        
        // Write to log
		$fp = fopen ( LOG_FILE , 'w' );
		fwrite ( $fp, $this->ipn_status );
		fclose ( $fp ); // close file
		chmod ( LOG_FILE , 0600 );
	}
    
	public function send_report($to, $subject = null, $from = null, $opening_msg = null) {
		
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";

		if(!$subject)
			$subject = 'Instant Payment Notification - Recieved Payment';

		if($opening_msg)
			$body = $opening_msg."\r\n";
		else
			$body = "";
		
		$body .= "from " . $this->ipn_data ['payer_email'] . " on " . date ( 'm/d/Y' );
		$body .= " at " . date ( 'g:i A' ) . "\n\nDetails:\n" . $this->ipn_status;

		if($from)
			$headers .= 'From:'.$from. "\r\n"; 	
		
		wp_mail( $to, $subject, $body, $headers );
	}
	public function print_report(){
		$find [] = "\n";
		$replace [] = '<br/>';
		$html_content = str_replace ( $find, $replace, $this->ipn_status );
		echo $html_content;
	}
	
	public function dump_fields() {
 
		// Used for debugging, this function will output all the field/value pairs
		// that are currently defined in the instance of the class using the
		// add_field() function.
		echo "<h3>rm_paypal_class->dump_fields() Output:</h3>";
		echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">".__('Field Name','custom-registration-form-builder-with-submission-manager')."</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">".__('Value','custom-registration-form-builder-with-submission-manager')."</font></b></td>
            </tr>"; 
		ksort($this->fields);
		foreach ($this->fields as $key => $value) {echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";}
		echo "</table><br>"; 
	}
	public function debug($msg) {
		
		if (! $this->ipn_debug)
			return;
		
		$today = date ( "Y-m-d H:i:s " );
		$myFile = ".ipn_debugs.log";
                
                if(file_exists($myFile))
                    @unlink($myFile);
                return;
                
		$fh = fopen ( $myFile, 'w' ) or die ( "Can't open debug file. Please manually create the 'debug.log' file and make it writable." );
		$ua_simple = preg_replace ( "/(.*)\s\(.*/", "\\1", $_SERVER ['HTTP_USER_AGENT'] );
		fwrite ( $fh, "");
		fclose ( $fh );
		chmod ( $myFile, 0600 );
	}
}         
 
