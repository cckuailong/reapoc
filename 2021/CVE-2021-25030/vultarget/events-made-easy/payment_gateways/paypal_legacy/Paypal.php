<?php
/**
 * Paypal Buy Now Button class
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

class Paypal {

	// Official url: https://www.paypal.com/cgi-bin/webscr
	// Testing urls: (do test!)
	//  - https://www.sandbox.paypal.com/cgi-bin/webscr
	public $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';

	// time in seconds before the form is submitted to paypal
	// 0 = instantly
	// false = disabled, no auto submission
	public $timeout = false;

	// the form id, defaultsto paypal_form
	public $form_id = 'paypal_form';

	// the label for the submit button. or false to disable it (depend on js to submit, not recommended)
	public $button = 'Buy Now';
	public $button_img_url = '';

	// use encryption
	public $encrypt = true;

	// private key file to use
	public $private_key = '/path/to/privatekey.pem';

	// public certificate file to use
	public $public_cert = '/path/to/publiccert.pem';

	// Paypal's public certificate
	public $paypal_cert = '/path/to/paypalcert.pem';

	// Paypal Certificate ID to use
	public $cert_id = 'the_cert_id_here';

	// no need to edit anything below this line

	protected $fields = array();

	// constructor adds some default fields
	// set return method to post
	// set paypal cmd to _xclick for "buy now" functionality
	function __construct() {
		$this->add_field('rm','2');
    	$this->add_field('cmd','_xclick');
    }

    // adds a field to the button
	public function add_field($field,$val) {
		$this->fields["$field"] = $val;
	}


	// this function returns the buy now button in clear or encrypted mode
	public function get_button() {
		$form = '';

		if($this->timeout != false) {
			$form .= '<script type="text/javascript">';
			$form .= "addEvent(window, 'load', function() { setTimeout(\"document.forms['".$this->form_id."'].submit()\", " . $this->timeout*1000 . ");})";
			$form .= '</script>';
		}

		$form .= '<form method="post" id="'.$this->form_id.'" name="'.$this->form_id.'" action="'.$this->paypal_url.'">'."\n";

		if($this->encrypt) {
			$form .= $this->get_button_encrypted();
		} else {
			$form .= $this->get_button_plain();
		}

		if($this->button_img_url) {
			$form .= '<input type="image" src="'.$this->button_img_url.'" alt="'.$this->button.'" title="'.$this->button.'"/>'."\n";
		}
		elseif($this->button) {
			$form .= '<input type="submit" value="'.$this->button.'" />'."\n";
		}

		$form .= '</form>';

		return $form;
	}

	// creates the form fields for unencrypted mode
	protected function get_button_plain() {
		$out = '';
		foreach ($this->fields as $name => $value) {
			$out .= '<input type="hidden" name="'.$name.'" value="'.$value.'"/>'."\n";
		}
		return $out;
	}

	// created the fields in encrypted mode
	protected function get_button_encrypted() {
    	if (!file_exists($this->private_key)) {
			echo '<div style="color:red">ERROR: Local private key: ' . $this->private_key . ' not found</div>';
		}
		if (!file_exists($this->public_cert)) {
			echo '<div style="color:red">ERROR: Local public certificate: ' . $this->public_cert . ' not found</div>';
		}
		if (!file_exists($this->paypal_cert)) {
			echo '<div style="color:red">ERROR: Paypal public certificate: ' . $this->paypal_cert . ' not found</div>';
		}
		$this->add_field('cert_id',$this->cert_id);
		$out = '<input type="hidden" name="cmd" value="_s-xclick">'."\n";
		$out .= '<input type="hidden" name="encrypted" value="' . $this->paypal_encrypt($this->fields) . '">';
		return $out;
	}

	// function returns encrypted button blob using ssl
	protected function paypal_encrypt($arr) {

		require_once "crypt/EWPServices.php";

		$blob = EWPServices::encryptButton(	$arr,
											$this->public_cert,
											$this->private_key,
											'',
											$this->paypal_cert,
											$this->paypal_url);

		if(!$blob) {
			die('error getting encrypted button');
		}
		return $blob;
	}


}

?>
