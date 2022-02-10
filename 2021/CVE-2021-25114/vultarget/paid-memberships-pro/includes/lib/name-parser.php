<?php
/*
	Taken from: http://code.google.com/p/php-name-parser/

	Changed function names to avoid conflicts.
*/

// split full names into the following parts:
// - prefix / salutation  (Mr., Mrs., etc)
// - given name / first name
// - middle initials
// - surname / last name
// - suffix (II, Phd, Jr, etc)
if(!function_exists("pnp_split_full_name"))
{
	function pnp_split_full_name($full_name) {
		$name = [
			'salutation' => '',
			'fname'      => '',
			'initials'   => '',
			'lname'      => '',
			'suffix'     => '',
		];

		if ( empty( $full_name ) ) {
			return $name;
		}

		$fname = $lname = $initials = NULL;

		$full_name = trim($full_name);
		// split into words
		$unfiltered_name_parts = explode(" ",$full_name);
		$name_parts = array();
		// completely ignore any words in parentheses
		foreach ($unfiltered_name_parts as $word) {
			if (!empty($word) && $word[0] != "(")
				$name_parts[] = $word;
		}

		if ( empty( $name_parts ) ) {
			return $name;
		}

		$num_words = sizeof($name_parts);

		// is the first word a title? (Mr. Mrs, etc)
		$salutation = pnp_is_salutation($name_parts[0]);

		$suffix = false;

		if ( isset( $name_parts[sizeof($name_parts)-1] ) ) {
			$suffix = pnp_is_suffix( $name_parts[ sizeof( $name_parts ) - 1 ] );
		}

		// set the range for the middle part of the name (trim prefixes & suffixes)
		$start = ($salutation) ? 1 : 0;
		$end = ($suffix) ? $num_words-1 : $num_words;

		// concat the first name
		for ($i=$start; $i < $end-1; $i++) {
			$word = $name_parts[$i];
			// move on to parsing the last name if we find an indicator of a compound last name (Von, Van, etc)
			// we use $i != $start to allow for rare cases where an indicator is actually the first name (like "Von Fabella")
			if (pnp_is_compound_lname($word) && $i != $start)
				break;
			// is it a middle initial or part of their first name?
			// if we start off with an initial, we'll call it the first name
			if (pnp_is_initial($word)) {
				// is the initial the first word?
				if ($i == $start) {
					// if so, do a look-ahead to see if they go by their middle name
					// for ex: "R. Jason Smith" => "Jason Smith" & "R." is stored as an initial
					// but "R. J. Smith" => "R. Smith" and "J." is stored as an initial
					if ( isset( $name_parts[ $i + 1 ] ) && pnp_is_initial( $name_parts[ $i + 1 ] ) )
						$fname .= " ".strtoupper($word);
					else
						$initials .= " ".strtoupper($word);
				// otherwise, just go ahead and save the initial
				} else {
					$initials .= " ".strtoupper($word);
				}
			} else {
				$fname .= " ".pnp_fix_case($word);
			}
		}

		// check that we have more than 1 word in our string
		if ($end-$start > 1) {
			// concat the last name
			for ($i; $i < $end; $i++) {
				$lname .= " ".pnp_fix_case($name_parts[$i]);
			}
		} else {
			// otherwise, single word strings are assumed to be first names
			$fname = pnp_fix_case($name_parts[$i]);
		}

		// return the various parts in an array
		$name['salutation'] = $salutation;
		$name['fname'] = trim($fname);
		$name['initials'] = trim($initials);
		$name['lname'] = trim($lname);
		$name['suffix'] = $suffix;
		return $name;
	}

	// detect and format standard salutations
	// I'm only considering english honorifics for now & not words like
	function pnp_is_salutation($word) {
		// ignore periods
		$word = str_replace('.','',strtolower($word));
		// returns normalized values
		if ($word == "mr" || $word == "master" || $word == "mister")
			return "Mr.";
		else if ($word == "mrs")
			return "Mrs.";
		else if ($word == "miss" || $word == "ms")
			return "Ms.";
		else if ($word == "dr")
			return "Dr.";
		else if ($word == "rev")
			return "Rev.";
		else if ($word == "fr")
			return "Fr.";
		else
			return false;
	}

	//  detect and format common suffixes
	function pnp_is_suffix($word) {
		// ignore periods
		$word = str_replace('.','',$word);
		// these are some common suffixes - what am I missing?
		$suffix_array = array('I','II','III','IV','V','Senior','Junior','Jr','Sr','PhD','APR','RPh','PE','MD','MA','DMD','CME');
		foreach ($suffix_array as $suffix) {
			if (strtolower($suffix) == strtolower($word))
				return $suffix;
		}
		return false;
	}

	// detect compound last names like "Von Fange"
	function pnp_is_compound_lname($word) {
		$word = strtolower($word);
		// these are some common prefixes that identify a compound last names - what am I missing?
		$words = array('vere','von','van','de','del','della','di','da','pietro','vanden','du','st.','st','la','ter');
		return array_search($word,$words);
	}

	// single letter, possibly followed by a period
	function pnp_is_initial($word) {
		return ((strlen($word) == 1) || (strlen($word) == 2 && $word[1] == "."));
	}

	// detect mixed case words like "McDonald"
	// returns false if the string is all one case
	function pnp_is_camel_case($word) {
		if (preg_match("|[A-Z]+|s", $word) && preg_match("|[a-z]+|s", $word))
			return true;
		return false;
	}

	// ucfirst words split by dashes or periods
	// ucfirst all upper/lower strings, but leave camelcase words alone
	function pnp_fix_case($word) {
		// uppercase words split by dashes, like "Kimura-Fay"
		$word = pnp_safe_ucfirst("-",$word);
		// uppercase words split by periods, like "J.P."
		$word = pnp_safe_ucfirst(".",$word);
		return $word;
	}

	// helper function for fix_case
	function pnp_safe_ucfirst($seperator, $word) {
		// uppercase words split by the seperator (ex. dashes or periods)
		$parts = explode($seperator,$word);
		foreach ($parts as $word) {
			$words[] = (pnp_is_camel_case($word)) ? $word : ucfirst(strtolower($word));
		}
		return implode($seperator,$words);
	}
}
?>
