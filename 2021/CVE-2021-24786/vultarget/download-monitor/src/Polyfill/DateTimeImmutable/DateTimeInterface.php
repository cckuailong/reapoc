<?php

/**
 * Interface DateTimeInterface
 *
 * Forked from https://github.com/jesseschalken/php-date-time-immutable-polyfill
 */

// To keep the result of (new \ReflectionClass('DateTimeInterface'))->_toString()
// as close as possible to PHP>=5.5, do not add, remove, reorder or change the
// signature of any methods in this interface.

if ( ! interface_exists( "DateTimeInterface" ) ) {
	interface DateTimeInterface {
		public function format( $format );

		public function getTimezone();

		public function getOffset();

		public function getTimestamp();

		public function diff( $object, $absolute = false );

		public function __wakeup();
	}
}

