<?php
/**
 * Paypal IPN class
 * v1.0 (27/05/2008)
 * Copyright 2008 Roberto Gomes
 * http://ptdev.net
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class IPN {

	// Official url: https://www.paypal.com/cgi-bin/webscr
	// Testing urls: (do test!)
	//  - https://www.sandbox.paypal.com/cgi-bin/webscr
	//  - http://www.eliteweaver.co.uk/testing/ipntest.php
	public $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';

	// your paypal email (the one that receives the payments)
	public $paypal_email = 'paypal@example.com';

	// log to file options
	public $log_to_file = true;					// write logs to file
	public $log_filename = '/path/to/ipn.log';  // the log filename (should NOT be web accessible)

	// log to e-mail options
	public $log_to_email = true;				// send logs by e-mail
	public $log_email = 'log@example.com';		// where you want to receive the logs
	public $log_subject = 'IPN Log: ';			// prefix for the e-mail subject

	// array of currencies accepted or false to disable
	public $currencies = array('USD');

	// date format on log headers (default: dd/mm/YYYY HH:mm:ss)
	// see http://php.net/date
	public $date_format = 'd/m/Y H:i:s';

	// holds the ipn in a "pretty" way for viewing on logs and emails, can set prefix here
	public $pretty_ipn = "IPN Values received:\n\n";

	// the IPN information received by post will be on this array
	public $ipn = array();

	// this is where the action is
	public function ipn_is_valid() {

		// loop through the IPN received by POST and do 3 things:
		//  - populate the ipn_data array
		//  - generate a "pretty" list of all ipn variables received (for file logs and e-mails)
		//  - generate the IPN verification string to post back to paypal for validation
		$req = "";
		foreach ($_POST as $key => $value) {
			$this->ipn["$key"] = $value;
			$this->pretty_ipn .= "$key: $value\n";
			$req .= "&$key=".urlencode(stripslashes($value));
		}

		// post the ipn back to paypal and exit if invalid
		if(!$this->ipn_postback($req)) {
			return false;
		}

		// got verified
		// do the paypal recommended validations

		// check if payment status is completed
		if($this->ipn['payment_status'] != 'Completed') {
			$this->write_log('WARNING: payment status not completed', $this->pretty_ipn);
			return false;
		}

		// check if it was payed to your paypal e-mail or business id
		if(strtolower($this->ipn['receiver_email']) != strtolower($this->paypal_email) &&
		   strtolower($this->ipn['receiver_id']) != strtolower($this->paypal_email)) {
			$this->write_log('WARNING: payment was made to different e-mail account', $this->pretty_ipn);
			return false;
		}

		if($this->currencies && !in_array($this->ipn['mc_currency'],$this->currencies)) {
			$this->write_log('WARNING: payment in unsupported currency', $this->pretty_ipn);
			return false;
		}


		// if we didn't return false until now, then everything should be correct
		return true;

	}

	// this method must be called after ipn_is_valid() and all your custom validations have passed
	// it finally updates the database (if active) and logs the valid ipn
	// you can also call it anyway if you want to log invalid ipn's to the database
	public function complete() {
		$this->write_log('VALID IPN RECEIVED', $this->pretty_ipn);
	}


	// this method sends the ipn back to paypal
	// returns true if Paypal says verified
	protected function ipn_postback($req) {

		// split the paypal url
		$url_parsed=parse_url($this->paypal_url);

		// connect to paypal
		$socket = fsockopen("ssl://".$url_parsed['host'],443,$err_num,$err_str,30);

		if(!$socket) {
			// could not open the connection. Log it and return
			$this->write_log('WARNING: failed connection to Paypal',"Error establishing connection to paypal\n\nfsockopen error no. $err_num: $err_str");
			return false;

		} else {

			// connected, add the ipn validation cmd and post everything back to PayPal
			$req = 'cmd=_notify-validate' . $req;
			$header = "POST ".$url_parsed['path']." HTTP/1.1\r\n";
			$header .= "Host: ".$url_parsed['host']."\r\n";
			$header .= "Connection: close\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			fputs($socket, $header . $req);

			// loop through the response from the server and assign it to a variable
			$res = "";
			while(!feof($socket)) {
				$res .= fgets($socket, 1024);
			}

			// close connection
			fclose($socket);

			// check Paypals response
			if (preg_match("/VERIFIED/i",$res)) {
				return true;
			} else {
				$this->write_log('WARNING: invalid IPN detected', $this->pretty_ipn);
				return false;
			}

		}

	}

	// writes the log to file and/or sends to email according to preferences
	// parameters:
	//	$log_msg -> short descriptive msg (gets appended to e-mail subjects)
	//  $log_descr -> everyting else, generally the pretty ipn (goes in e-mail body and file logs)
	protected function write_log($log_msg,$log_descr) {

		$thelog = "------------------------------------------------\n";
		$thelog .= '----------- [ '.date($this->date_format).' ] ------------' . "\n";
		$thelog .= "------------------------------------------------\n";
		$thelog .= $log_msg . "\n\n";
		$thelog .= $log_descr . "\n";
		$thelog .= "------------------------------------------------\n";
		$thelog .= "------------------------------------------------\n\n\n\n";

		// log to file if enabled
		if($this->log_to_file) {
			$fp = fopen($this->log_filename,'a');
			fwrite($fp, $thelog);
			fclose($fp);  // close file
		}

		// send email if enabled
		if($this->log_to_email) {
			mail($this->log_email, "$this->log_subject $log_msg", $thelog);
		}

	}

}

?>
