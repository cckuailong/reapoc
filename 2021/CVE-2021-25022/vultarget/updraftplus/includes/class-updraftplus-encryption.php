<?php

if (!defined('ABSPATH')) die('No direct access.');

class UpdraftPlus_Encryption {

	/**
	 * This will decrypt an encrypted file
	 *
	 * @param String  $fullpath          This is the full filesystem path to the encrypted file location
	 * @param String  $key               This is the key to be used when decrypting
	 * @param Boolean $to_temporary_file Use if the resulting file is not intended to be kept
	 *
	 * @return Boolean|Array -An array with info on the decryption; or false for failure
	 */
	public static function decrypt($fullpath, $key, $to_temporary_file = false) {
	
		global $updraftplus;
	
		$ensure_phpseclib = $updraftplus->ensure_phpseclib('Crypt_Rijndael');
		
		if (is_wp_error($ensure_phpseclib)) {
			$updraftplus->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message());
			$updraftplus->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message(), 'error');
			return false;
		}
		
		// open file to read
		if (false === ($file_handle = fopen($fullpath, 'rb'))) return false;

		$decrypted_path = dirname($fullpath).'/decrypt_'.basename($fullpath).'.tmp';
		// open new file from new path
		if (false === ($decrypted_handle = fopen($decrypted_path, 'wb+'))) return false;

		// setup encryption
		$rijndael = new Crypt_Rijndael();
		$rijndael->setKey($key);
		$rijndael->disablePadding();
		$rijndael->enableContinuousBuffer();
		
		if (defined('UPDRAFTPLUS_DECRYPTION_ENGINE')) {
			if ('openssl' == UPDRAFTPLUS_DECRYPTION_ENGINE) {
				$rijndael->setPreferredEngine(CRYPT_ENGINE_OPENSSL);
			} elseif ('mcrypt' == UPDRAFTPLUS_DECRYPTION_ENGINE) {
				$rijndael->setPreferredEngine(CRYPT_ENGINE_MCRYPT);
			} elseif ('internal' == UPDRAFTPLUS_DECRYPTION_ENGINE) {
				$rijndael->setPreferredEngine(CRYPT_ENGINE_INTERNAL);
			}
		}

		$file_size = filesize($fullpath);
		$bytes_decrypted = 0;
		$buffer_size = defined('UPDRAFTPLUS_CRYPT_BUFFER_SIZE') ? UPDRAFTPLUS_CRYPT_BUFFER_SIZE : 2097152;

		// loop around the file
		while ($bytes_decrypted < $file_size) {
			// read buffer sized amount from file
			if (false === ($file_part = fread($file_handle, $buffer_size))) return false;
			// check to ensure padding is needed before decryption
			$length = strlen($file_part);
			if (0 != $length % 16) {
				$pad = 16 - ($length % 16);
				$file_part = str_pad($file_part, $length + $pad, chr($pad));
			}
			
			$decrypted_data = $rijndael->decrypt($file_part);
			
			if (0 == $bytes_decrypted) {
				if (UpdraftPlus_Manipulation_Functions::str_ends_with($fullpath, '.gz.crypt')) {
					$first_two_chars = unpack('C*', substr($decrypted_data, 0, 2));
					// The first two decrypted bytes of the .gz file should always be 1f 8b
					if (31 != $first_two_chars[1] || 139 != $first_two_chars[2]) {
						return false;
					}
				} elseif (UpdraftPlus_Manipulation_Functions::str_ends_with($fullpath, '.zip.crypt')) {
					$first_four_chars = unpack('C*', substr($decrypted_data, 0, 2));
					// The first four decrypted bytes of the .zip file should always be 50 4B 03 04 or 50 4B 05 06 or 50 4B 07 08
					if (80 != $first_four_chars[1] || 75 != $first_four_chars[2] || !in_array($first_four_chars[3], array(3, 5, 7)) || !in_array($first_four_chars[3], array(4, 6, 8))) {
						return false;
					}
					
				}
			}
			
			$is_last_block = ($bytes_decrypted + strlen($decrypted_data) >= $file_size);
			
			$write_bytes = min($file_size - $bytes_decrypted, strlen($decrypted_data));
			if ($is_last_block) {
				$is_padding = false;
				$last_byte = ord(substr($decrypted_data, -1, 1));
				if ($last_byte < 16) {
					$is_padding = true;
					for ($j = 1; $j<=$last_byte; $j++) {
						if (substr($decrypted_data, -$j, 1) != chr($last_byte)) $is_padding = false;
					}
				}
				if ($is_padding) {
					$write_bytes -= $last_byte;
				}
			}
			
			if (false === fwrite($decrypted_handle, $decrypted_data, $write_bytes)) return false;
			$bytes_decrypted += $buffer_size;
		}
		 
		// close the main file handle
		fclose($decrypted_handle);
		// close original file
		fclose($file_handle);
		
		// remove the crypt extension from the end as this causes issues when opening
		$fullpath_new = preg_replace('/\.crypt$/', '', $fullpath, 1);
		// //need to replace original file with tmp file
		
		$fullpath_basename = basename($fullpath_new);
		
		if ($to_temporary_file) {
			return array(
				'fullpath' 	=> $decrypted_path,
				'basename' => $fullpath_basename
			);
		}
		
		if (false === rename($decrypted_path, $fullpath_new)) return false;

		// need to send back the new decrypted path
		$decrypt_return = array(
			'fullpath' 	=> $fullpath_new,
			'basename' => $fullpath_basename
		);

		return $decrypt_return;
	}

	/**
	 * This is the encryption process when encrypting a file
	 *
	 * @param String $fullpath This is the full path to the DB file that needs ecrypting
	 * @param String $key      This is the key (salting) to be used when encrypting
	 *
	 * @return String|Boolean - Return the full path of the encrypted file, or false for an error
	 */
	public static function encrypt($fullpath, $key) {
	
		global $updraftplus;

		if (!function_exists('mcrypt_encrypt') && !extension_loaded('openssl')) {
			$updraftplus->log(sprintf(__('Your web-server does not have the %s module installed.', 'updraftplus'), 'PHP/mcrypt / PHP/OpenSSL').' '.__('Without it, encryption will be a lot slower.', 'updraftplus'), 'warning', 'nocrypt');
		}

		// include Rijndael library from phpseclib
		$ensure_phpseclib = $updraftplus->ensure_phpseclib('Crypt_Rijndael');
		
		if (is_wp_error($ensure_phpseclib)) {
			$updraftplus->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message());
			return false;
		}

		// open file to read
		if (false === ($file_handle = fopen($fullpath, 'rb'))) {
			$updraftplus->log("Failed to open file for read access: $fullpath");
			return false;
		}

		// encrypted path name. The trailing .tmp ensures that it will be cleaned up by the temporary file reaper eventually, if needs be.
		$encrypted_path = dirname($fullpath).'/encrypt_'.basename($fullpath).'.tmp';

		$data_encrypted = 0;
		$buffer_size = defined('UPDRAFTPLUS_CRYPT_BUFFER_SIZE') ? UPDRAFTPLUS_CRYPT_BUFFER_SIZE : 2097152;

		$time_last_logged = microtime(true);
		
		$file_size = filesize($fullpath);

		// Set initial value to false so we can check it later and decide what to do
		$resumption = false;

		// setup encryption
		$rijndael = new Crypt_Rijndael();
		$rijndael->setKey($key);
		$rijndael->disablePadding();
		$rijndael->enableContinuousBuffer();
		
		// First we need to get the block length, this method returns the length in bits we need to change this back to bytes in order to use it with the file operation methods.
		$block_length = $rijndael->getBlockLength() >> 3;

		// Check if the path already exists as this could be a resumption
		if (file_exists($encrypted_path)) {
			
			$updraftplus->log("Temporary encryption file found, will try to resume the encryption");

			// The temp file exists so set resumption to true
			$resumption = true;

			// Get the file size as this is needed to help resume the encryption
			$data_encrypted = filesize($encrypted_path);
			// Get the true file size e.g without padding used for various resumption paths
			$true_data_encrypted = $data_encrypted - ($data_encrypted % $buffer_size);

			if ($data_encrypted >= $block_length) {
		
				// Open existing file from the path
				if (false === ($encrypted_handle = fopen($encrypted_path, 'rb+'))) {
					$updraftplus->log("Failed to open file for write access on resumption: $encrypted_path");
					$resumption = false;
				}
				
				// First check if our buffer size needs padding if it does increase buffer size to length that doesn't need padding
				if (0 != $buffer_size % 16) {
					$pad = 16 - ($buffer_size % 16);
					$true_buffer_size = $buffer_size + $pad;
				} else {
					$true_buffer_size = $buffer_size;
				}
				
				// Now check if using modulo on data encrypted and buffer size returns 0 if it doesn't then the last block was a partial write and we need to discard that and get the last useable IV by adding this value to the block length
				$partial_data_size = $data_encrypted % $true_buffer_size;

				// We need to reconstruct the IV from the previous run in order for encryption to resume
				if (-1 === (fseek($encrypted_handle, $data_encrypted - ($block_length + $partial_data_size)))) {
					$updraftplus->log("Failed to move file pointer to correct position to get IV: $encrypted_path");
					$resumption = false;
				}

				// Read previous block length from file
				if (false === ($iv = fread($encrypted_handle, $block_length))) {
					$updraftplus->log("Failed to read from file to get IV: $encrypted_path");
					$resumption = false;
				}

				$rijndael->setIV($iv);

				// Now we need to set the file pointer for the original file to the correct position and take into account the padding added, this padding needs to be removed to get the true amount of bytes read from the original file
				if (-1 === (fseek($file_handle, $true_data_encrypted))) {
					$updraftplus->log("Failed to move file pointer to correct position to resume encryption: $fullpath");
					$resumption = false;
				}
				
			} else {
				// If we enter here then the temp file exists but it is either empty or has one incomplete block we may as well start again
				$resumption = false;
			}

			if (!$resumption) {
				$updraftplus->log("Could not resume the encryption will now try to start again");
				// remove the existing encrypted file as it's no good to us now
				@unlink($encrypted_path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// reset the data encrypted so that the loop can be entered
				$data_encrypted = 0;
				// setup encryption to reset the IV
				$rijndael = new Crypt_Rijndael();
				$rijndael->setKey($key);
				$rijndael->disablePadding();
				$rijndael->enableContinuousBuffer();
				// reset the file pointer and then we should be able to start from fresh
				if (-1 === (fseek($file_handle, 0))) {
					$updraftplus->log("Failed to move file pointer to start position to restart encryption: $fullpath");
					$resumption = false;
				}
			}
		}

		if (!$resumption) {
			// open new file from new path
			if (false === ($encrypted_handle = fopen($encrypted_path, 'wb+'))) {
				$updraftplus->log("Failed to open file for write access: $encrypted_path");
				return false;
			}
		}
		
		// loop around the file
		while ($data_encrypted < $file_size) {

			// read buffer-sized amount from file
			if (false === ($file_part = fread($file_handle, $buffer_size))) {
				$updraftplus->log("Failed to read from file: $fullpath");
				return false;
			}
			
			// check to ensure padding is needed before encryption
			$length = strlen($file_part);
			if (0 != $length % 16) {
				$pad = 16 - ($length % 16);
				$file_part = str_pad($file_part, $length + $pad, chr($pad));
			}

			$encrypted_data = $rijndael->encrypt($file_part);
			
			if (false === fwrite($encrypted_handle, $encrypted_data)) {
				$updraftplus->log("Failed to write to file: $encrypted_path");
				return false;
			}
			
			$data_encrypted += $buffer_size;
			
			$time_since_last_logged = microtime(true) - $time_last_logged;
			if ($time_since_last_logged > 5) {
				$time_since_last_logged = microtime(true);
				$updraftplus->log("Encrypting file: completed $data_encrypted bytes");
			}
			
		}

		// close the main file handle
		fclose($encrypted_handle);
		fclose($file_handle);

		// encrypted path
		$result_path = $fullpath.'.crypt';

		// need to replace original file with tmp file
		if (false === rename($encrypted_path, $result_path)) {
			$updraftplus->log("File rename failed: $encrypted_path -> $result_path");
			return false;
		}

		return $result_path;
	}
	
	/**
	 * This function spools the decrypted contents of a file to the browser
	 *
	 * @param  String $fullpath   This is the full path to the encrypted file
	 * @param  String $encryption This is the key used to decrypt the file
	 *
	 * @uses header()
	 */
	public static function spool_crypted_file($fullpath, $encryption) {
	
		global $updraftplus;
	
		if ('' == $encryption) $encryption = UpdraftPlus_Options::get_updraft_option('updraft_encryptionphrase');
		
		if ('' == $encryption) {
			header('Content-type: text/plain');
			_e("Decryption failed. The database file is encrypted, but you have no encryption key entered.", 'updraftplus');
			$updraftplus->log('Decryption of database failed: the database file is encrypted, but you have no encryption key entered.', 'error');
		} else {

			// now decrypt the file and return array
			$decrypted_file = self::decrypt($fullpath, $encryption, true);

			// check to ensure there is a response back
			if (is_array($decrypted_file)) {
				header('Content-type: application/x-gzip');
				header("Content-Disposition: attachment; filename=\"".$decrypted_file['basename']."\";");
				header("Content-Length: ".filesize($decrypted_file['fullpath']));
				readfile($decrypted_file['fullpath']);

				// need to remove the file as this is no longer needed on the local server
				unlink($decrypted_file['fullpath']);
			} else {
				header('Content-type: text/plain');
				echo __("Decryption failed. The most likely cause is that you used the wrong key.", 'updraftplus')." ".__('The decryption key used:', 'updraftplus').' '.$encryption;
				
			}
		}
	}
	
	/**
	 * Indicate whether an indicated backup file is encrypted or not, as indicated by the suffix
	 *
	 * @param String $file - the filename
	 *
	 * @return Boolean
	 */
	public static function is_file_encrypted($file) {
		return preg_match('/\.crypt$/i', $file);
	}
}
