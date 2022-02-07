<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Here live manipulation functions that just transform input into output and do not perform any other activities
 */
class UpdraftPlus_Manipulation_Functions {

	/**
	 * Replace last occurence
	 *
	 * @param  String  $search         The value being searched for, otherwise known as the needle
	 * @param  String  $replace        The replacement value that replaces found search values
	 * @param  String  $subject        The string or array being searched and replaced on, otherwise known as the haystack
	 * @param  Boolean $case_sensitive Whether the replacement should be case sensitive or not
	 *
	 * @return String
	 */
	public static function str_lreplace($search, $replace, $subject, $case_sensitive = true) {
		$pos = $case_sensitive ? strrpos($subject, $search) : strripos($subject, $search);
		if (false !== $pos) $subject = substr_replace($subject, $replace, $pos, strlen($search));
		return $subject;
	}
	
	/**
	 * Replace the first, and only the first, instance within a string
	 *
	 * @param String $needle   - the search term
	 * @param String $replace  - the replacement term
	 * @param String $haystack - the string to replace within
	 *
	 * @return String - the filtered string
	 */
	public static function str_replace_once($needle, $replace, $haystack) {
		$pos = strpos($haystack, $needle);
		return (false !== $pos) ? substr_replace($haystack, $replace, $pos, strlen($needle)) : $haystack;
	}

	/**
	 * Remove slashes that precede a comma or the end of the string
	 *
	 * @param String $string - input string
	 *
	 * @return String - the altered string
	 */
	public static function strip_dirslash($string) {
		return preg_replace('#/+(,|$)#', '$1', $string);
	}

	/**
	 * Remove slashes from a string or array of strings.
	 *
	 * The function wp_unslash() is WP 3.6+, so therefore we have a compatibility method here
	 *
	 * @param String|Array $value String or array of strings to unslash.
	 * @return String|Array Unslashed $value
	 */
	public static function wp_unslash($value) {
		return function_exists('wp_unslash') ? wp_unslash($value) : stripslashes_deep($value);
	}
	
	/**
	 * Parse a filename into components
	 *
	 * @param String $filename - the filename
	 *
	 * @return Array|Boolean - the parsed values, or false if parsing failed
	 */
	public static function parse_filename($filename) {
		if (preg_match('/^backup_([\-0-9]{10})-([0-9]{4})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?+\.(zip|gz|gz\.crypt)$/i', $filename, $matches)) {
			return array(
				'date' => strtotime($matches[1].' '.$matches[2]),
				'nonce' => $matches[3],
				'type' => $matches[4],
				'index' => (empty($matches[5]) ? 0 : $matches[5]-1),
				'extension' => $matches[6]
			);
		} else {
			return false;
		}
	}
	
	/**
	 * Convert a number of bytes into a suitable textual string
	 *
	 * @param Integer $size - the number of bytes
	 *
	 * @return String - the resulting textual string
	 */
	public static function convert_numeric_size_to_text($size) {
		if ($size > 1073741824) {
			return round($size / 1073741824, 1).' GB';
		} elseif ($size > 1048576) {
			return round($size / 1048576, 1).' MB';
		} elseif ($size > 1024) {
			return round($size / 1024, 1).' KB';
		} else {
			return round($size, 1).' B';
		}
	}

	/**
	 * Add backquotes to tables and db-names in SQL queries. Taken from phpMyAdmin.
	 *
	 * @param  string $a_name - the table name
	 * @return string - the quoted table name
	 */
	public static function backquote($a_name) {
		if (!empty($a_name) && '*' != $a_name) {
			if (is_array($a_name)) {
				$result = array();
				foreach ($a_name as $key => $val) {
					$result[$key] = '`'.$val.'`';
				}
				return $result;
			} else {
				return '`'.$a_name.'`';
			}
		} else {
			return $a_name;
		}
	}
	
	/**
	 * Remove empty (according to empty()) members of an array
	 *
	 * @param Array $list - input array
	 * @return Array - pruned array
	 */
	public static function remove_empties($list) {
		if (!is_array($list)) return $list;
		foreach ($list as $ind => $entry) {
			if (empty($entry)) unset($list[$ind]);
		}
		return $list;
	}

	/**
	 * Sort restoration entities
	 *
	 * @param String $a - first entity
	 * @param String $b - second entity
	 *
	 * @return Integer - sort result
	 */
	public static function sort_restoration_entities($a, $b) {
		if ($a == $b) return 0;
		// Put the database first
		// Put wpcore after plugins/uploads/themes (needed for restores of foreign all-in-one formats)
		if ('db' == $a || 'wpcore' == $b) return -1;
		if ('db' == $b || 'wpcore' == $a) return 1;
		// After wpcore, next last is others
		if ('others' == $b) return -1;
		if ('others' == $a) return 1;
		// And then uploads - this is only because we want to make sure uploads is after plugins, so that we know before we get to the uploads whether the version of UD which might have to unpack them can do this new-style or not.
		if ('uploads' == $b) return -1;
		if ('uploads' == $a) return 1;
		return strcmp($a, $b);
	}
	
	/**
	 * This options filter removes ABSPATH off the front of updraft_dir, if it is given absolutely and contained within it
	 *
	 * @param  String $updraft_dir Directory
	 * @return String
	 */
	public static function prune_updraft_dir_prefix($updraft_dir) {
		if ('/' == substr($updraft_dir, 0, 1) || "\\" == substr($updraft_dir, 0, 1) || preg_match('/^[a-zA-Z]:/', $updraft_dir)) {
			$wcd = trailingslashit(WP_CONTENT_DIR);
			if (strpos($updraft_dir, $wcd) === 0) {
				$updraft_dir = substr($updraft_dir, strlen($wcd));
			}
		}
		return $updraft_dir;
	}
	
	public static function get_mime_type_from_filename($filename, $allow_gzip = true) {
		if ('.zip' == substr($filename, -4, 4)) {
			return 'application/zip';
		} elseif ('.tar' == substr($filename, -4, 4)) {
			return 'application/x-tar';
		} elseif ('.tar.gz' == substr($filename, -7, 7)) {
			return 'application/x-tgz';
		} elseif ('.tar.bz2' == substr($filename, -8, 8)) {
			return 'application/x-bzip-compressed-tar';
		} elseif ($allow_gzip && '.gz' == substr($filename, -3, 3)) {
			// When we sent application/x-gzip as a content-type header to the browser, we found a case where the server compressed it a second time (since observed several times)
			return 'application/x-gzip';
		} else {
			return 'application/octet-stream';
		}
	}
	
	/**
	 * Filter the value to ensure it is between 1 and 9999
	 *
	 * @param Integer $input
	 *
	 * @return Integer
	 */
	public static function retain_range($input) {
		$input = (int) $input;
		return ($input > 0) ? min($input, 9999) : 1;
	}

	/**
	 * Find matching string from $str_arr1 and $str_arr2
	 *
	 * @param array   $str_arr1                  array of strings
	 * @param array   $str_arr2                  array of strings
	 * @param boolean $match_until_first_numeric only match until first numeric occurence
	 * @return string matching str which will be best for replacement
	 */
	public static function get_matching_str_from_array_elems($str_arr1, $str_arr2, $match_until_first_numeric = true) {
		$matching_str = '';
		if ($match_until_first_numeric) {
			$str_partial_arr = array();
			foreach ($str_arr1 as $str1) {
				$str1_str_length = strlen($str1);
				$temp_str1_chars = str_split($str1);
				$temp_partial_str = '';
				// The flag is for whether non-numeric character passed after numeric character occurence in str1. For ex. str1 is utf8mb4, the flag wil be true when parsing m after utf8.
				$numeric_char_pass_flag = false;
				$char_position_in_str1 = 0;
				while ($char_position_in_str1 < $str1_str_length) {
					if ($numeric_char_pass_flag && !is_numeric($temp_str1_chars[$char_position_in_str1])) {
						break;
					}
					if (is_numeric($temp_str1_chars[$char_position_in_str1])) {
						$numeric_char_pass_flag = true;
					}
					$temp_partial_str .= $temp_str1_chars[$char_position_in_str1];
					$char_position_in_str1++;
				}
				$str_partial_arr[] = $temp_partial_str;
			}
			foreach ($str_partial_arr as $str_partial) {
				if (!empty($matching_str)) {
					break;
				}
				foreach ($str_arr2 as $str2) {
					if (0 === stripos($str2, $str_partial)) {
						$matching_str = $str2;
						break;
					}
				}
			}
		} else {
			$str1_partial_first_arr = array();
			$str1_partial_first_arr = array();
			$str1_partial_start_n_middle_arr = array();
			$str1_partial_middle_n_last_arr = array();
			$str1_partial_last_arr = array();
			foreach ($str_arr1 as $str1) {
				$str1_partial_arr = explode('_', $str1);
				$str1_parts_count = count($str1_partial_arr);
				$str1_partial_first_arr[] = $str1_partial_arr[0];
				$str1_last_part_index = $str1_parts_count - 1;
				if ($str1_last_part_index > 0) {
					$str1_partial_last_arr[] = $str1_partial_arr[$str1_last_part_index];
					$str1_partial_start_n_middle_arr[] = substr($str1, 0, stripos($str1, '_'));
					$str1_partial_middle_n_last_arr[] = substr($str1, stripos($str1, '_') + 1);
				}
			}
			for ($case_no = 1; $case_no <= 5; $case_no++) {
				if (!empty($matching_str)) {
					break;
				}
				foreach ($str_arr2 as $str2) {
					switch ($case_no) {
						// Case 1: Both Start and End match
						case 1:
						$str2_partial_arr = explode('_', $str2);
						$str2_first_part = $str2_partial_arr[0];
						$str2_parts_count = count($str2_partial_arr);
						$str2_last_part_index = $str2_parts_count - 1;
						if ($str2_last_part_index > 0) {
								$str2_last_part = $str2_partial_arr[$str2_last_part_index];
						} else {
														$str2_last_part = '';
						}
						if (!empty($str2_last_part) && !empty($str1_partial_last_arr) && in_array($str2_first_part, $str1_partial_first_arr) && in_array($str2_last_part, $str1_partial_last_arr)) {
								$matching_str = $str2;
						}
							break;
						// Case 2: Start Middle Match
						case 2:
						$str2_partial_first_n_middle_parts = substr($str2, 0, stripos($str2, '_'));
						if (in_array($str2_partial_first_n_middle_parts, $str1_partial_start_n_middle_arr)) {
								$matching_str = $str2;
						}
							break;
						// Case 3: End Middle Match
						case 3:
						$str2_partial_middle_n_last_parts = stripos($str2, '_') !== false ? substr($str2, stripos($str2, '_') + 1) : '';
						if (!empty($str2_partial_middle_n_last_parts) && in_array($str2_partial_middle_n_last_parts, $str1_partial_middle_n_last_arr)) {
								$matching_str = $str2;
						}
							break;
						// Case 4: Start Match (low possibilities)
						case 4:
						$str2_partial_arr = explode('_', $str2);
						$str2_first_part = $str2_partial_arr[0];
						if (in_array($str2_first_part, $str1_partial_first_arr)) {
								$matching_str = $str2;
						}
							break;
						// Case 5: End Match (low possibilities)
						case 5:
						$str2_partial_arr = explode('_', $str2);
						$str2_parts_count = count($str2_partial_arr);
						$str2_last_part_index = $str2_parts_count - 1;
						if ($str2_last_part_index > 0) {
								$str2_last_part = $str2_partial_arr[$str2_last_part_index];
						} else {
														$str2_last_part = '';
						}
						if (!empty($str2_last_part) && in_array($str2_last_part, $str1_partial_last_arr)) {
								$matching_str = $str2;
						}
							break;
					}
					if (!empty($matching_str)) {
						break;
					}
				}
			}
		}
		return $matching_str;
	}
	
	/**
	 * Produce a normalised version of a URL, useful for comparisons. This may produce a URL that does not actually reference the same location; its purpose is only to use in comparisons of two URLs that *both* go through this function.
	 *
	 * @param String $url - the URL
	 *
	 * @return String - normalised
	 */
	public static function normalise_url($url) {
		$parsed_descrip_url = parse_url($url);
		if (is_array($parsed_descrip_url)) {
			if (preg_match('/^www\./i', $parsed_descrip_url['host'], $matches)) $parsed_descrip_url['host'] = substr($parsed_descrip_url['host'], 4);
			$normalised_descrip_url = 'http://'.strtolower($parsed_descrip_url['host']);
			if (!empty($parsed_descrip_url['port'])) $normalised_descrip_url .= ':'.$parsed_descrip_url['port'];
			if (!empty($parsed_descrip_url['path'])) $normalised_descrip_url .= untrailingslashit($parsed_descrip_url['path']);
		} else {
			$normalised_descrip_url = untrailingslashit($url);
		}
		return $normalised_descrip_url;
	}

	/**
	 * Normalize a filesystem path.
	 *
	 * On windows systems, replaces backslashes with forward slashes
	 * and forces upper-case drive letters.
	 * Allows for two leading slashes for Windows network shares, but
	 * ensures that all other duplicate slashes are reduced to a single.
	 *
	 * @param string $path Path to normalize.
	 * @return string Normalized path.
	 */
	public static function wp_normalize_path($path) {
		// wp_normalize_path is not present before WP 3.9
		if (function_exists('wp_normalize_path')) return wp_normalize_path($path);
		// Taken from WP 4.6
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('|(?<=.)/+|', '/', $path);
		if (':' === substr($path, 1, 1)) {
			$path = ucfirst($path);
		}
		return $path;
	}

	/**
	 * Given a set of times, find details about the maximum
	 *
	 * @param Array	  $time_passed - a list of times passed, with numerical indexes
	 * @param Integer $upto		   - last index to consider
	 * @param Integer $first_run   - first index to consider
	 *
	 * @return Array - a list with entries, in order: maximum time, list in string format, how many run times were found
	 */
	public static function max_time_passed($time_passed, $upto, $first_run) {
		$max_time = 0;
		$timings_string = "";
		$run_times_known = 0;
		for ($i = $first_run; $i <= $upto; $i++) {
			$timings_string .= "$i:";
			if (isset($time_passed[$i])) {
				$timings_string .= round($time_passed[$i], 1).' ';
				$run_times_known++;
				if ($time_passed[$i] > $max_time) $max_time = round($time_passed[$i]);
			} else {
				$timings_string .= '? ';
			}
		}
		return array($max_time, $timings_string, $run_times_known);
	}
	
	/**
	 * Determine if a given string ends with a given substring.
	 *
	 * @param  string $haystack string
	 * @param  string $needle   substring which should be checked at the end of the string
	 * @return boolean Whether string ends with the substring or not
	 */
	public static function str_ends_with($haystack, $needle) {
		if (substr($haystack, - strlen($needle)) == $needle) return true;
		return false;
	}

	/**
	 * Returns a random string of given length.
	 *
	 * @param  string $length integer
	 * @return string random string
	 */
	public static function generate_random_string($length = 2) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$characters_length = strlen($characters);
		$random_string = '';
		for ($i = 0; $i < $length; $i++) {
			$random_string .= $characters[rand(0, $characters_length - 1)];
		}
		return $random_string;
	}
}
