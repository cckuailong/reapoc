<?php
// Makes use of closure/anonymous functions available in PHP 5.3+
function woo_ce_sort_fields( $key ) {

	return function( $a, $b ) use ( $key ) {
		return strnatcmp( $a[$key], $b[$key] );
	};

}