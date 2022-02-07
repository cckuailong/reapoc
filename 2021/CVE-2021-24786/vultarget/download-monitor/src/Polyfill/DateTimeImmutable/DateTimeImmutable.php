<?php

/**
 * Class DateTimeImmutable
 *
 * Forked from https://github.com/jesseschalken/php-date-time-immutable-polyfill
 */

// To keep the result of (new \ReflectionClass('DateTimeImmutable'))->_toString()
// as close as possible to PHP>=5.5, do not add, remove, reorder or change the
// signature of any methods in this class.

if ( ! class_exists( "DateTimeImmutable" ) ) {


	class DateTimeImmutable implements DateTimeInterface {
		public static function __set_state() {
		}

		public static function createFromFormat( $format, $time, $object = null ) {
			$dt = DateTime::createFromFormat( $format, $time, $object );
			if ( $dt === false ) {
				return false;
			}
			$self     = new self( '@0' );
			$self->dt = $dt;

			return $self;
		}

		public static function getLastErrors() {
			return DateTime::getLastErrors();
		}

		public static function createFromMutable( $DateTime ) {
			$self     = new self( '@0' );
			$self->dt = clone $DateTime;

			return $self;
		}

		private $dt;

		public function __construct( $time = "now", $object = null ) {
			$this->dt = new DateTime( $time, $object );
		}

		public function __wakeup() {
		}

		public function format( $format ) {
			return $this->dt->format( $format );
		}

		public function getTimezone() {
			return $this->dt->getTimezone();
		}

		public function getOffset() {
			return $this->dt->getOffset();
		}

		public function getTimestamp() {
			return $this->dt->getTimestamp();
		}

		public function diff( $object, $absolute = false ) {
			return $this->dt->diff( $object, $absolute );
		}

		public function modify( $modify ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->modify( $modify );

			return $clone;
		}

		public function add( $interval ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->add( $interval );

			return $clone;
		}

		public function sub( $interval ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->sub( $interval );

			return $clone;
		}

		public function setTimezone( $timezone ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->setTimezone( $timezone );

			return $clone;
		}

		public function setTime( $hour, $minute, $second = 0 ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->setTime( $hour, $minute, $second );

			return $clone;
		}

		public function setDate( $year, $month, $day ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->setDate( $year, $month, $day );

			return $clone;
		}

		public function setISODate( $year, $week, $day = 1 ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->setISODate( $year, $week, $day );

			return $clone;
		}

		public function setTimestamp( $unixtimestamp ) {
			$clone     = clone $this;
			$clone->dt = clone $clone->dt;
			$clone->dt->setTimestamp( $unixtimestamp );

			return $clone;
		}
	}
}