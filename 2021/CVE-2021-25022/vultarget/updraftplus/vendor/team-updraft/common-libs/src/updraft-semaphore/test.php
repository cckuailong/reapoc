<?php

/*
Example usage:

php -a
require 'wp-load.php';
define('I_AM_TESTING', true);
require 'test.php';
*/

if (!defined('ABSPATH')) die('No direct access.');

if (!defined('I_AM_TESTING') || !I_AM_TESTING) die('Please define I_AM_TESTING.');

require_once(dirname(__FILE__).'/class-updraft-semaphore.php');

class Test_Logger_1 {
	function log($message, $level) {
		echo "Test_Logger_1::log(level=$level, message=$message)\n";
	}
}

class Test_Logger_2 {
	function log($message, $level) {
		echo "Test_Logger_2::log(level=$level, message=$message)\n";
	}
}

$my_lock = new Updraft_Semaphore_3_0('my_test_lock_name', 4, array(new Test_Logger_1()));

if ($my_lock->lock()) {
	try {
		// do stuff ...
		$my_lock_again = new Updraft_Semaphore_3_0('my_test_lock_name', 4, array(new Test_Logger_2()));
		$time_now = microtime(true);
		if ($my_lock_again->lock(6)) {
			echo "Eventually got it after ".round(microtime(true) - $time_now, 3)." seconds\n";
			$my_lock_again->release();
		} else {
			echo("Sorry, could not get the second lock\n");
		}
		
	} catch (Exception $e) {
		var_dump($e);
		// We are making sure we release the lock in case of an error
	} catch (Error $e) { // phpcs:ignore PHPCompatibility.Classes.NewClasses.errorFound
		var_dump($e);
		// We are making sure we release the lock in case of an error
	}
	
	$my_lock->release();
	
} else {
	echo("Sorry, could not get the first lock\n");
}
