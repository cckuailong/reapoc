<?php
/**
 * Copyright 2007 PayPal, Inc. All Rights Reserved.
 */

/**
 * This class provides a utility sign and encrypt function of a string using PKCS7
 */
class PPCrypto
{
	/**
	 * Sign and Envelope the passed data string, returning a PKCS7 blob that can be posted to PayPal.
	 * Make sure the passed data string is seperated by UNIX linefeeds (ASCII 10, '\n').
	 *
	 * @param	string	The candidate for signature and encryption
	 * @param	string	The file path to the EWP(merchant) certificate
	 * @param	string	The file path to the EWP(merchant) private key
	 * @param	string	The EWP(merchant) private key password
	 * @param	string	The file path to the PayPal Certificate
	 * @return	array	Contains a bool status, error_msg, error_no, and an encrypted string: encryptedData if successfull
	 *
	 * @access	public
	 * @static
	 */
	function signAndEncrypt($dataStr_, $ewpCertPath_, $ewpPrivateKeyPath_, $ewpPrivateKeyPwd_, $paypalCertPath_)
	{
		$dataStrFile  = realpath(tempnam('/tmp', 'pp_'));
        $fd = fopen($dataStrFile, 'w');
		if(!$fd) {
			$error = "Could not open temporary file $dataStrFile.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		fwrite($fd, $dataStr_);
		fclose($fd);

		$signedDataFile = realpath(tempnam('/tmp', 'pp_'));
		if(!@openssl_pkcs7_sign(	$dataStrFile,
									$signedDataFile,
									"file://$ewpCertPath_",
									array("file://$ewpPrivateKeyPath_", $ewpPrivateKeyPwd_),
									array(),
									PKCS7_BINARY)) {
			unlink($dataStrFile);
			unlink($signedDataFile);
			$error = "Could not sign data: ".openssl_error_string();
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($dataStrFile);

		$signedData = file_get_contents($signedDataFile);
		$signedDataArray = explode("\n\n", $signedData);
		$signedData = $signedDataArray[1];
		$signedData = base64_decode($signedData);

		unlink($signedDataFile);

		$decodedSignedDataFile = realpath(tempnam('/tmp', 'pp_'));
		$fd = fopen($decodedSignedDataFile, 'w');
		if(!$fd) {
			$error = "Could not open temporary file $decodedSignedDataFile.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		fwrite($fd, $signedData);
		fclose($fd);

		$encryptedDataFile = realpath(tempnam('/tmp', 'pp_'));
		if(!@openssl_pkcs7_encrypt(	$decodedSignedDataFile,
									$encryptedDataFile,
									file_get_contents($paypalCertPath_),
									array(),
									PKCS7_BINARY)) {
			unlink($decodedSignedDataFile);
			unlink($encryptedDataFile);
			$error = "Could not encrypt data: ".openssl_error_string();
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($decodedSignedDataFile);

		$encryptedData = file_get_contents($encryptedDataFile);
		if(!$encryptedData) {
			$error = "Encryption and signature of data failed.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($encryptedDataFile);

		$encryptedDataArray = explode("\n\n", $encryptedData);
		$encryptedData = trim(str_replace("\n", '', $encryptedDataArray[1]));

        return array("status" => true, "encryptedData" => $encryptedData);
	} // signAndEncrypt
} // PPCrypto

?>