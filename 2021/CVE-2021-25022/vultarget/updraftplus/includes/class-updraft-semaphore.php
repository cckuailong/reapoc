<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Class Updraft_Semaphore_3_0
 *
 * This class is much simpler to use than the the previous series, as it has dropped support for complicated cases that were not being used. It also now only uses a single row in the options database, and takes care of creating it itself internally.
 *
 * Logging, though, may be noisier, unless your loggers are taking note of the log level and only registering what is required.
 *
 * Example of use (a lock that will expire if not released within 300 seconds)
 * 
 * See test.php for a longer example (including logging).
 *
 * $my_lock = new Updraft_Semaphore_3_0('my_lock_name', 300);
 * // If getting the lock does not succeed first time, try again up to twice
 * if ($my_lock->lock(2)) {
 *   try {
 *     // do stuff ...
 *   } catch (Exception $e) {
 *     // We are making sure we release the lock in case of an error
 *   } catch (Error $e) {
 *     // We are making sure we release the lock in case of an error
 *   }
 *   $my_lock->release();
 * } else {
 *   error_log("Sorry, could not get the lock");
 * }
 */
class Updraft_Semaphore_3_0 {

	// Time after which the lock will expire (in seconds)
	protected $locked_for;
	
	// Name for the lock in the WP options table
	protected $option_name;
	
	// Lock status - a boolean
	protected $acquired = false;
	
	// An array of loggers
	protected $loggers = array();

	/**
	 * Constructor. Instantiating does not lock anything, but sets up the details for future operations.
	 *
	 * @param String  $name		  - a unique (across the WP site) name for the lock. Should be no more than 51 characters in length (because of the use of the WP options table, with some further characters used internally)
	 * @param Integer $locked_for - time (in seconds) after which the lock will expire if not released. This needs to be positive if you don't want bad things to happen.
	 * @param Array	  $loggers	  - an array of loggers
	 */
	public function __construct($name, $locked_for = 300, $loggers = array()) {
		$this->option_name = 'updraft_lock_'.$name;
		$this->locked_for = $locked_for;
		$this->loggers = $loggers;
	}

	/**
	 * Internal function to make sure that the lock is set up in the database
	 *
	 * @return Integer - 0 means 'failed' (which could include that someone else concurrently created it); 1 means 'already existed'; 2 means 'exists, because we created it). The intention is that non-zero results mean that the lock exists.
	 */
	private function ensure_database_initialised() {
	
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s", $this->option_name);
		
		if (1 === (int) $wpdb->get_var($sql)) {
			$this->log('Lock option ('.$this->option_name.', '.$wpdb->options.') already existed in the database', 'debug');
			return 1;
		}
		
		$sql = $wpdb->prepare("INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES(%s, '0', 'no');", $this->option_name);
		
		$rows_affected = $wpdb->query($sql);
		
		if ($rows_affected > 0) {
			$this->log('Lock option ('.$this->option_name.', '.$wpdb->options.') was created in the database', 'debug');
		} else {
			$this->log('Lock option ('.$this->option_name.', '.$wpdb->options.') failed to be created in the database (could already exist)', 'notice');
		}
		
		return ($rows_affected > 0) ? 2 : 0;
	}

	/**
	 * Attempt to acquire the lock. If it was already acquired, then nothing extra will be done (the method will be a no-op).
	 *
	 * @param Integer $retries - how many times to retry (after a 1 second sleep each time)
	 *
	 * @return Boolean - whether the lock was successfully acquired or not
	 */
	public function lock($retries = 0) {
	
		if ($this->acquired) return true;
		
		global $wpdb;
		
		$time_now = time();
		$acquire_until = $time_now + $this->locked_for;
		
		$sql = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value < %d", $acquire_until, $this->option_name, $time_now);
		
		if (1 === $wpdb->query($sql)) {
			$this->log('Lock ('.$this->option_name.', '.$wpdb->options.') acquired', 'info');
			$this->acquired = true;
			return true;
		}
		
		// See if the failure was caused by the row not existing (we check this only after failure, because it should only occur once on the site)
		if (!$this->ensure_database_initialised()) return false;
		
		do {
			// Now that the row has been created, try again
			if (1 === $wpdb->query($sql)) {
				$this->log('Lock ('.$this->option_name.', '.$wpdb->options.') acquired after initialising the database', 'info');
				$this->acquired = true;
				return true;
			}
			$retries--;
			if ($retries >=0) {
				$this->log('Lock ('.$this->option_name.', '.$wpdb->options.') not yet acquired; sleeping', 'debug');
				sleep(1);
				// As a second has passed, update the time we are aiming for
				$time_now = time();
				$acquire_until = $time_now + $this->locked_for;
				$sql = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value < %d", $acquire_until, $this->option_name, $time_now);
			}
		} while ($retries >= 0);
		
		$this->log('Lock ('.$this->option_name.', '.$wpdb->options.') could not be acquired (it is locked)', 'info');
		
		return false;
	}

	/**
	 * Release the lock
	 *
	 * N.B. We don't attempt to unlock it unless we locked it. i.e. Lost locks are left to expire rather than being forced. (If we want to force them, we'll need to introduce a new parameter).
	 *
	 * @return Boolean - if it returns false, then the lock was apparently not locked by us (and the caller will most likely therefore ignore the result, whatever it is). 
	 */
	public function release() {
		if (!$this->acquired) return false;
		global $wpdb;
		$sql = $wpdb->prepare("UPDATE {$wpdb->options} SET option_value = '0' WHERE option_name = %s", $this->option_name);
		
		$this->log('Lock option ('.$this->option_name.', '.$wpdb->options.') released', 'info');
		
		$result = (int) $wpdb->query($sql) === 1;
		
		$this->acquired = false;
		
		return $result;
	}
	
	/**
	 * Cleans up the DB of any residual data. This should not be used as part of ordinary unlocking; only as part of deinstalling, or if you otherwise know that the lock will not be used again. If calling this, it's redundant to first unlock (and a no-op to attempt to do so afterwards).
	 */
	public function delete() {
		$this->acquired = false;
	
		global $wpdb;
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s", $this->option_name));

		$this->log('Lock option ('.$this->option_name.', '.$wpdb->options.') was deleted from the database');
	}
	
	/**
	 * Captures and logs any given messages
	 *
	 * @param String $message - the error message
	 * @param String $level	  - the message level (debug, notice, info, warning, error)
	 */
	public function log($message, $level = 'info') {
		if (isset($this->loggers)) {
			foreach ($this->loggers as $logger) {
				$logger->log($message, $level);
			}
		}
	}
	
	/**
	 * Sets the list of loggers for this instance (removing any others).
	 *
	 * @param Array $loggers - the loggers for this task
	 */
	public function set_loggers($loggers) {
		$this->loggers = array();
		foreach ($loggers as $logger) {
			$this->add_logger($logger);
		}
	}

	/**
	 * Add a logger to loggers list
	 *
	 * @param Callable $logger - a logger (a method with a callable function 'log', taking string parameters $level $message)
	 */
	public function add_logger($logger) {
		$this->loggers[] = $logger;
	}

	/**
	 * Return the current list of loggers
	 *
	 * @return Array
	 */
	public function get_loggers() {
		return $this->loggers;
	}
}
