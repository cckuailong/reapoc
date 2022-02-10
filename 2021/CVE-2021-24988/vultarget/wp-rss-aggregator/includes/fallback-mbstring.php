<?php

/**
 * A utility class that provides compatibility padding for multibyte string
 * functionality.
 * 
 * Taken mostly from {@link https://doc.wikimedia.org/mediawiki-core/master/php/Fallback_8php_source.html here}
 * 
 * @since 4.7
 */
class WPRSS_MBString {


	/**
	 * @since 4.7
	 * @param string $str
	 * @param int $start
	 * @param int|string $count
	 * @return string
	 */
	public static function mb_substr( $str, $start, $count = 'end' ) {
		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $str, $start );
		}

		if ( $start != 0 ) {
			$split = self::mb_substr_split_unicode( $str, intval( $start ) );
			$str = substr( $str, $split );
		}

		if ( $count !== 'end' ) {
			$split = self::mb_substr_split_unicode( $str, intval( $count ) );
			$str = substr( $str, 0, $split );
		}

		return $str;
	}


	/**
	 * @since 4.7
	 * @param string $str
	 * @param int $splitPos
	 * @return int
	 */
	public static function mb_substr_split_unicode( $str, $splitPos ) {
		if ( $splitPos == 0 ) {
			return 0;
		}

		$byteLen = strlen( $str );

		if ( $splitPos > 0 ) {
			if ( $splitPos > 256 ) {
			// Optimize large string offsets by skipping ahead N bytes.
			// This will cut out most of our slow time on Latin-based text,
			// and 1/2 to 1/3 on East European and Asian scripts.
				$bytePos = $splitPos;
				while ( $bytePos < $byteLen && $str[$bytePos] >= "\x80" && $str[$bytePos] < "\xc0" ) {
					++$bytePos;
				}
				$charPos = mb_strlen( substr( $str, 0, $bytePos ) );
			} else {
				$charPos = 0;
				$bytePos = 0;
			}

			while ( $charPos++ < $splitPos ) {
				++$bytePos;
				// Move past any tail bytes
				while ( $bytePos < $byteLen && $str[$bytePos] >= "\x80" && $str[$bytePos] < "\xc0" ) {
					++$bytePos;
				}
			}
		} else {
			$splitPosX = $splitPos + 1;
			$charPos = 0; // relative to end of string; we don't care about the actual char position here
			$bytePos = $byteLen;
			while ( $bytePos > 0 && $charPos-- >= $splitPosX ) {
				--$bytePos;
				// Move past any tail bytes
				while ( $bytePos > 0 && $str[$bytePos] >= "\x80" && $str[$bytePos] < "\xc0" ) {
					--$bytePos;
				}
			}
		}

		return $bytePos;
	}


	/**
	 * @since 4.7
	 * @param string $str
	 * @return int
	 */
	public static function mb_strlen( $str, $enc = '' ) {
		if ( function_exists( 'mb_strlen' ) ) {
			return mb_strlen( $str );
		}

		$counts = count_chars( $str );
		$total = 0;

		// Count ASCII bytes
		for ( $i = 0; $i < 0x80; $i++ ) {
			$total += $counts[$i];
		}

		// Count multibyte sequence heads
		for ( $i = 0xc0; $i < 0xff; $i++ ) {
			$total += $counts[$i];
		}
		return $total;
	}


	/**
	 * @since 4.7
	 * @param string $haystack
	 * @param string $needle
	 * @param int $offset
	 * @return int|boolean
	 */
	public static function mb_strpos( $haystack, $needle, $offset = 0, $encoding = '' ) {
		if ( function_exists( 'mb_strpos' ) ) {
			return mb_strpos( $haystack, $needle, $offset );
		}

		$needle = preg_quote( $needle, '/' );

		$ar = array();
		preg_match( '/' . $needle . '/u', $haystack, $ar, PREG_OFFSET_CAPTURE, $offset );

		if ( isset( $ar[0][1] ) ) {
			return $ar[0][1];
		} else {
			return false;
		}
	}


	/**
	 * @since 4.7
	 * @param string $haystack
	 * @param string $needle
	 * @param int $offset
	 * @return int|boolean
	 */
	public static function mb_stripos( $haystack, $needle, $offset = 0, $encoding = '' ) {
		if ( function_exists( 'mb_stripos' ) ) {
			return mb_stripos( $haystack, $needle, $offset );
		}


		$needle = preg_quote( $needle, '/' );

		$ar = array();
		preg_match( '/' . $needle . '/ui', $haystack, $ar, PREG_OFFSET_CAPTURE, $offset );

		if ( isset( $ar[0][1] ) ) {
			return $ar[0][1];
		} else {
			return false;
		}
	}


	/**
	 * @since 4.7
	 * @param string $haystack
	 * @param string $needle
	 * @param int $offset
	 * @return int|boolean
	 */
	public static function mb_strrpos( $haystack, $needle, $offset = 0, $encoding = '' ) {
		if ( function_exists( 'mb_strrpos' ) ) {
			return mb_strrpos( $haystack, $needle, $offset );
		}

		$needle = preg_quote( $needle, '/' );

		$ar = array();
		preg_match_all( '/' . $needle . '/u', $haystack, $ar, PREG_OFFSET_CAPTURE, $offset );

		if ( isset( $ar[0] ) && count( $ar[0] ) > 0 && isset( $ar[0][count( $ar[0] ) - 1][1] ) ) {
			return $ar[0][count( $ar[0] ) - 1][1];
		} else {
			return false;
		}
	}

	/**
	 * Lowercase a UTF-8 string.
	 * This supports accented letters, but nothing more.
	 * Taken from {@link https://github.com/drupal/drupal/blob/9.x/core/includes/unicode.inc#L432 here}.
	 *
	 * @since 4.7
	 * @param string $text The string to run the operation on.
	 * @return string The string in lowercase.
	 */
	public static function mb_strtolower( $text ) {
		if ( function_exists( 'mb_strtolower' ) )
			return mb_strtolower( $text );
		
		// Use C-locale for ASCII-only lowercase
		$text = strtolower( $text );
		// Case flip Latin-1 accented letters
		$text = preg_replace_callback( '/\xC3[\x80-\x96\x98-\x9E]/', array( __CLASS__, '_unicode_caseflip' ), $text );
		return $text;
	}
	

	/**
	 * Flips U+C0-U+DE to U+E0-U+FD and back.
	 *
	 * @since 4.7
	 * @param $matches An array of matches.
	 * @return array The Latin-1 version of the array of matches.
	 * @see mb_strtolower()
	 */
	public static function _unicode_caseflip( $matches ) {
		return $matches[0][0] . chr( ord( $matches[0][1] ) ^ 32 );
	}

}
