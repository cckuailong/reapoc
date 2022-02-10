<?php
/*
|| --------------------------------------------------------------------------------------------
|| Dilaz Metabox Functions
|| --------------------------------------------------------------------------------------------
||
|| @package		Dilaz Metabox
|| @subpackage	Functions
|| @since		Dilaz Metabox 1.0
|| @author		WebDilaz Team, http://webdilaz.com
|| @copyright	Copyright (C) 2017, WebDilaz LTD
|| @link		http://webdilaz.com/metaboxes
|| @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
|| 
*/

defined('ABSPATH') || exit;

/**
 * Functions class
 */
if (!class_exists('DilazMetaboxFunction')) {
	class DilazMetaboxFunction {
		
		function __construct() {
			add_action('wp_ajax_dilaz_mb_query_select', array($this, 'query_select'));
			add_action('wp_ajax_dilaz_mb_get_post_titles', array($this, 'get_post_titles'));
		}
		
		
		/**
		 * Add underscore to prefix
		 *
		 * @since 2.1
		 *
		 * @param  string prefix metabox options prefix
		 *
		 * @return json.data
		 */
		public static function preparePrefix($prefix) {
			return rtrim($prefix, '_') . '_';
		}
		
		
		/**
		 * Query select function
		 *
		 * @since 1.0
		 *
		 * @global wpdb   $wpdb                WordPress database abstraction object
		 * @param  string $_POST['q']          search string
		 * @param  array  $_POST['selected']   selected items
		 * @param  string $_POST['query_type'] 'post', 'user', 'term'
		 * @param  array  $_POST['query_args'] query arguments
		 *
		 * @return json.data
		 */
		public function query_select() {
			
			global $wpdb;
			
			$search     = isset($_POST['q']) ? $wpdb->esc_like($_POST['q']) : '';
			$selected   = isset($_POST['selected']) ? (array)$_POST['selected'] : '';
			$query_type = isset($_POST['query_type']) ? sanitize_text_field($_POST['query_type']) : '';
			$query_args = isset($_POST['query_args']) ? $_POST['query_args'] : '';
			
			$data = array();
			
			if ($query_type == 'post') {
				
				/* The callback is a closure that needs to use the $search from the current scope */
				add_filter('posts_where', function ($where) use ($search) {
					$where .= (' AND post_title LIKE "%'. $search .'%"');
					return $where;
				});
				
				$default_args = array(
					'post__not_in'     => $selected,
					'suppress_filters' => false,
				);
				
				$query = wp_parse_args( unserialize(base64_decode($query_args)), $default_args );
				$posts = get_posts($query);
				
				foreach ($posts as $post) {
					$data[] = array(
						'id'   => $post->ID,
						'name' => $post->post_title,
					);
				}
				
			} else if ($query_type == 'user') {
				
				$default_args = array(
					'search'  => '*'. $search .'*',
					'exclude' => $selected
				);
				
				$query = wp_parse_args( unserialize(base64_decode($query_args)), $default_args );
				$users = get_users($query);
				
				foreach ($users as $user) {
					$data[] = array(
						'id'   => $user->ID,
						'name' => $user->nickname,
					);
				}
				
			} else if ($query_type == 'term') {
				
				$default_args = array(
					'name__like' => $search,
					'exclude'    => $selected
				);
				
				$query = wp_parse_args( unserialize(base64_decode($query_args)), $default_args );
				$terms = get_terms($query);
				
				foreach ($terms as $term) {
					$data[] = array(
						'id'   => $term->term_id,
						'name' => $term->name,
					);
				}
				
			}
			
			echo json_encode($data);
			
			die();
		}
		
		
		/**
		 * Get post titles
		 *
		 * @since 1.0
		 *
		 * @param array $_POST['selected'] selected items
		 *
		 * @return json.data
		 */
		public function get_post_titles() {
			
			$result = array();
			
			$selected = isset($_POST['selected']) ? $_POST['selected'] : '';
			
			if (is_array($selected) && !empty($selected)) {
				$posts = get_posts(array(
					'posts_per_page' => -1,
					'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
					'post__in'       => $selected,
					'post_type'      => 'any'
				));
				
				foreach ($posts as $post) {
					$result[] = array(
						'id'    => $post->ID,
						'title' => $post->post_title,
					);
				}
			}
			
			echo json_encode($result);
			
			die;
		}
		
		
		/**
		 * Find position of array using its key and value
		 *
		 * @param array  $array	array to be searched through
		 * @param string $field	key of the array
		 * @param string $value	value of the array
		 * @since 1.0
		 *
		 * @return integer
		 */
		public static function find_array_key_by_value($array, $field, $value) {
			foreach ($array as $key => $array_item) {
				if ($array_item[$field] === $value)
					return $key;
			}
			
			return false;
		}
		
		
		/**
		 * Insert an array before the key of another array
		 *
		 * @param array  $array           array to insert into
		 * @param array  $data            array to be inserted
		 * @param string $key_offset      key position of the array to be inserted
		 * @param string $insert_position 'before' or 'after' or 'last', default: before
		 * @since 1.0
		 *
		 * @return	array
		 */
		public static function insert_array_adjacent_to_key($array, $data, $key_offset, $insert_position = 'before') {
			
			if (!is_array($data)) return false;
			
			switch ($insert_position) {
				case 'before' : $offset = $key_offset; break;
				case 'after'  : $offset = $key_offset+1; break;
				case 'last'   : $offset = count($array); break; # usually used when inserting a tab to be the last one
				default       : $offset = $key_offset; break;
			}
			
			foreach ($data as $item) {
				$new_array = array_merge( array_slice($array, 0, $offset, true), (array) $item, array_slice($array, $offset, NULL, true) );  
			}
			
			return $new_array;  
		}
		
		
		/**
		 * Get all fields within a metabox set
		 *
		 * @param array  $dilaz_meta_boxes all metaboxes array
		 * @param string $metabox_set_id   options set id
		 * @since 1.0
		 *
		 * @return array
		 */
		public static function get_meta_box_content($dilaz_meta_boxes, $metabox_set_id) {
			
			$set_id = 0;
			$box_contents = array();
			
			foreach ($dilaz_meta_boxes as $key => $val) {
				
				if (!isset($val['type'])) continue;
				
				if (isset($val['type'])) {
					if ($val['type'] == 'metabox_set') {
						$set_id = sanitize_key($val['id']);
					}
				}
				
				if ($set_id == $metabox_set_id) {
					$box_contents['fields'][] = $val;
				}
			}
			
			return $box_contents;
		}
		
		
		/**
		 * Add/Insert metabox field before a specific field
		 *
		 * @param array  $meta_boxes      all metaboxes array
		 * @param string $metabox_set_id  target options set id
		 * @param string $before_field_id target metabox field id
		 * @param string $context         tabs or fields, default: fields
		 * @param array  $insert_data     metabox fields to be inserted
		 * @param string $insert_position 'before' or 'after'
		 * @since 1.0
		 *
		 * @return array
		 */
		public static function insert_field($meta_boxes, $metabox_set_id, $before_field_id, $insert_data, $insert_position) {
			
			$metabox_content = self::get_meta_box_content($meta_boxes, $metabox_set_id);
			
			/* bail if fileds not found */
			if (!isset($metabox_content['fields'])) return;
			
			$metabox_content_data = $metabox_content['fields'];
			
			/* get array key position */
			$key_offset = isset($metabox_content_data) ? self::find_array_key_by_value($metabox_content_data, 'id', $before_field_id) : '';
			
			/* new array after another array has been inserted  */
			$new_array_modified = isset($metabox_content_data) ? self::insert_array_adjacent_to_key($metabox_content_data, array($insert_data), $key_offset, $insert_position) : $meta_boxes;
			
			/* merge the new array with the entire metabox options array */
			$new_meta_boxes = $new_array_modified;
			
			return $new_meta_boxes;
		}
		
		
		/**
		 * Timezones list with GMT offset
		 *
		 * @return array
		 * @link   http://stackoverflow.com/a/9328760
		 */
		public static function time_zones() {
			$zones_array = array();
			$timestamp = time();
			
			if (function_exists('timezone_identifiers_list')) {
				foreach (timezone_identifiers_list() as $key => $zone) {
					date_default_timezone_set($zone);
					$zones_array[$key]['zone'] = $zone;
					$zones_array[$key]['diff_from_GMT'] = 'UTC/GMT '. date('P', $timestamp);
				}
			}
			
			return $zones_array;
		}
		
		
		/**
		 * Default option vars
		 *
		 * @since 1.0
		 *
		 * @param string  $var option variable name
		 *
		 * @return mixed
		 */
		public static function choice($var) {
			
			switch ($var) {
				
				case 'yes_no_bool': 
					$output = array( 1 => __('Yes', 'dilaz-metabox'), 0 => __('No', 'dilaz-metabox') ); 
					break;
					
				case 'yes_no': 
					$output = array( 'yes' => __('Yes', 'dilaz-metabox'), 'no' => __('No', 'dilaz-metabox') );
					break;
					
				case 'def_yes_no': 
					$output = array( 'default' => __('Default', 'dilaz-metabox'), 'yes' => __('Yes', 'dilaz-metabox'), 'no' => __('No', 'dilaz-metabox') );
					break;
					
				case 'on_off': 
					$output = array( 'on' => __('On', 'dilaz-metabox'), 'off' => __('Off', 'dilaz-metabox') );
					break;
					
				case 'true_false': 
					$output = array( 'true' => __('True', 'dilaz-metabox'), 'false'	=> __('False', 'dilaz-metabox') );
					break;
					
				case 'bg_repeat': 
					$output = array( 
						'repeat'    => __('Repeat', 'dilaz-metabox'),
						'no-repeat'	=> __('No Repeat', 'dilaz-metabox'),
						'repeat-x'  => __('Repeat Horizontally', 'dilaz-metabox'),
						'repeat-y'  => __('Repeat Vertically', 'dilaz-metabox') 
					);
					break;
					
				case 'body_pos': 
					$output = array( 
						'top left'		=> __('Top Left', 'dilaz-metabox'),
						'top center'	=> __('Top Center', 'dilaz-metabox'),
						'top right'		=> __('Top Right', 'dilaz-metabox'),
						'center left '	=> __('Center Left', 'dilaz-metabox'),
						'center center'	=> __('Center Center', 'dilaz-metabox'),
						'center right'	=> __('Center Right', 'dilaz-metabox'),
						'bottom left'	=> __('Bottom Left', 'dilaz-metabox'),
						'bottom center'	=> __('Bottom Center', 'dilaz-metabox'),
						'bottom right'	=> __('Bottom Right', 'dilaz-metabox') 
					);
					break;
					
				case 'bg_attachment': 
					$output = array(
						'scroll' => __('Scroll', 'dilaz-metabox'),
						'fixed'	 => __('Fixed', 'dilaz-metabox')
					);
					break;
					
				case 'country_list': 
					$output = array(
						""   => "",
						"AF" => "Afghanistan",
						"AL" => "Albania",
						"DZ" => "Algeria",
						"AS" => "American Samoa",
						"AD" => "Andorra",
						"AO" => "Angola",
						"AI" => "Anguilla",
						"AQ" => "Antarctica",
						"AG" => "Antigua and Barbuda",
						"AR" => "Argentina",
						"AM" => "Armenia",
						"AW" => "Aruba",
						"AU" => "Australia",
						"AT" => "Austria",
						"AZ" => "Azerbaijan",
						"BS" => "Bahamas",
						"BH" => "Bahrain",
						"BD" => "Bangladesh",
						"BB" => "Barbados",
						"BY" => "Belarus",
						"BE" => "Belgium",
						"BZ" => "Belize",
						"BJ" => "Benin",
						"BM" => "Bermuda",
						"BT" => "Bhutan",
						"BO" => "Bolivia",
						"BA" => "Bosnia and Herzegovina",
						"BW" => "Botswana",
						"BV" => "Bouvet Island",
						"BR" => "Brazil",
						"BQ" => "British Antarctic Territory",
						"IO" => "British Indian Ocean Territory",
						"VG" => "British Virgin Islands",
						"BN" => "Brunei",
						"BG" => "Bulgaria",
						"BF" => "Burkina Faso",
						"BI" => "Burundi",
						"KH" => "Cambodia",
						"CM" => "Cameroon",
						"CA" => "Canada",
						"CT" => "Canton and Enderbury Islands",
						"CV" => "Cape Verde",
						"KY" => "Cayman Islands",
						"CF" => "Central African Republic",
						"TD" => "Chad",
						"CL" => "Chile",
						"CN" => "China",
						"CX" => "Christmas Island",
						"CC" => "Cocos [Keeling] Islands",
						"CO" => "Colombia",
						"KM" => "Comoros",
						"CG" => "Congo - Brazzaville",
						"CD" => "Congo - Kinshasa",
						"CK" => "Cook Islands",
						"CR" => "Costa Rica",
						"HR" => "Croatia",
						"CU" => "Cuba",
						"CY" => "Cyprus",
						"CZ" => "Czech Republic",
						"CI" => "Côte d’Ivoire",
						"DK" => "Denmark",
						"DJ" => "Djibouti",
						"DM" => "Dominica",
						"DO" => "Dominican Republic",
						"NQ" => "Dronning Maud Land",
						"DD" => "East Germany",
						"EC" => "Ecuador",
						"EG" => "Egypt",
						"SV" => "El Salvador",
						"GQ" => "Equatorial Guinea",
						"ER" => "Eritrea",
						"EE" => "Estonia",
						"ET" => "Ethiopia",
						"FK" => "Falkland Islands",
						"FO" => "Faroe Islands",
						"FJ" => "Fiji",
						"FI" => "Finland",
						"FR" => "France",
						"GF" => "French Guiana",
						"PF" => "French Polynesia",
						"TF" => "French Southern Territories",
						"FQ" => "French Southern and Antarctic Territories",
						"GA" => "Gabon",
						"GM" => "Gambia",
						"GE" => "Georgia",
						"DE" => "Germany",
						"GH" => "Ghana",
						"GI" => "Gibraltar",
						"GR" => "Greece",
						"GL" => "Greenland",
						"GD" => "Grenada",
						"GP" => "Guadeloupe",
						"GU" => "Guam",
						"GT" => "Guatemala",
						"GG" => "Guernsey",
						"GN" => "Guinea",
						"GW" => "Guinea-Bissau",
						"GY" => "Guyana",
						"HT" => "Haiti",
						"HM" => "Heard Island and McDonald Islands",
						"HN" => "Honduras",
						"HK" => "Hong Kong SAR China",
						"HU" => "Hungary",
						"IS" => "Iceland",
						"IN" => "India",
						"ID" => "Indonesia",
						"IR" => "Iran",
						"IQ" => "Iraq",
						"IE" => "Ireland",
						"IM" => "Isle of Man",
						"IL" => "Israel",
						"IT" => "Italy",
						"JM" => "Jamaica",
						"JP" => "Japan",
						"JE" => "Jersey",
						"JT" => "Johnston Island",
						"JO" => "Jordan",
						"KZ" => "Kazakhstan",
						"KE" => "Kenya",
						"KI" => "Kiribati",
						"KW" => "Kuwait",
						"KG" => "Kyrgyzstan",
						"LA" => "Laos",
						"LV" => "Latvia",
						"LB" => "Lebanon",
						"LS" => "Lesotho",
						"LR" => "Liberia",
						"LY" => "Libya",
						"LI" => "Liechtenstein",
						"LT" => "Lithuania",
						"LU" => "Luxembourg",
						"MO" => "Macau SAR China",
						"MK" => "Macedonia",
						"MG" => "Madagascar",
						"MW" => "Malawi",
						"MY" => "Malaysia",
						"MV" => "Maldives",
						"ML" => "Mali",
						"MT" => "Malta",
						"MH" => "Marshall Islands",
						"MQ" => "Martinique",
						"MR" => "Mauritania",
						"MU" => "Mauritius",
						"YT" => "Mayotte",
						"FX" => "Metropolitan France",
						"MX" => "Mexico",
						"FM" => "Micronesia",
						"MI" => "Midway Islands",
						"MD" => "Moldova",
						"MC" => "Monaco",
						"MN" => "Mongolia",
						"ME" => "Montenegro",
						"MS" => "Montserrat",
						"MA" => "Morocco",
						"MZ" => "Mozambique",
						"MM" => "Myanmar [Burma]",
						"NA" => "Namibia",
						"NR" => "Nauru",
						"NP" => "Nepal",
						"NL" => "Netherlands",
						"AN" => "Netherlands Antilles",
						"NT" => "Neutral Zone",
						"NC" => "New Caledonia",
						"NZ" => "New Zealand",
						"NI" => "Nicaragua",
						"NE" => "Niger",
						"NG" => "Nigeria",
						"NU" => "Niue",
						"NF" => "Norfolk Island",
						"KP" => "North Korea",
						"VD" => "North Vietnam",
						"MP" => "Northern Mariana Islands",
						"NO" => "Norway",
						"OM" => "Oman",
						"PC" => "Pacific Islands Trust Territory",
						"PK" => "Pakistan",
						"PW" => "Palau",
						"PS" => "Palestinian Territories",
						"PA" => "Panama",
						"PZ" => "Panama Canal Zone",
						"PG" => "Papua New Guinea",
						"PY" => "Paraguay",
						"YD" => "People's Democratic Republic of Yemen",
						"PE" => "Peru",
						"PH" => "Philippines",
						"PN" => "Pitcairn Islands",
						"PL" => "Poland",
						"PT" => "Portugal",
						"PR" => "Puerto Rico",
						"QA" => "Qatar",
						"RO" => "Romania",
						"RU" => "Russia",
						"RW" => "Rwanda",
						"RE" => "Réunion",
						"BL" => "Saint Barthélemy",
						"SH" => "Saint Helena",
						"KN" => "Saint Kitts and Nevis",
						"LC" => "Saint Lucia",
						"MF" => "Saint Martin",
						"PM" => "Saint Pierre and Miquelon",
						"VC" => "Saint Vincent and the Grenadines",
						"WS" => "Samoa",
						"SM" => "San Marino",
						"SA" => "Saudi Arabia",
						"SN" => "Senegal",
						"RS" => "Serbia",
						"CS" => "Serbia and Montenegro",
						"SC" => "Seychelles",
						"SL" => "Sierra Leone",
						"SG" => "Singapore",
						"SK" => "Slovakia",
						"SI" => "Slovenia",
						"SB" => "Solomon Islands",
						"SO" => "Somalia",
						"ZA" => "South Africa",
						"GS" => "South Georgia and the South Sandwich Islands",
						"KR" => "South Korea",
						"ES" => "Spain",
						"LK" => "Sri Lanka",
						"SD" => "Sudan",
						"SR" => "Suriname",
						"SJ" => "Svalbard and Jan Mayen",
						"SZ" => "Swaziland",
						"SE" => "Sweden",
						"CH" => "Switzerland",
						"SY" => "Syria",
						"ST" => "São Tomé and Príncipe",
						"TW" => "Taiwan",
						"TJ" => "Tajikistan",
						"TZ" => "Tanzania",
						"TH" => "Thailand",
						"TL" => "Timor-Leste",
						"TG" => "Togo",
						"TK" => "Tokelau",
						"TO" => "Tonga",
						"TT" => "Trinidad and Tobago",
						"TN" => "Tunisia",
						"TR" => "Turkey",
						"TM" => "Turkmenistan",
						"TC" => "Turks and Caicos Islands",
						"TV" => "Tuvalu",
						"UM" => "U.S. Minor Outlying Islands",
						"PU" => "U.S. Miscellaneous Pacific Islands",
						"VI" => "U.S. Virgin Islands",
						"UG" => "Uganda",
						"UA" => "Ukraine",
						"SU" => "Union of Soviet Socialist Republics",
						"AE" => "United Arab Emirates",
						"GB" => "United Kingdom",
						"US" => "United States",
						"ZZ" => "Unknown or Invalid Region",
						"UY" => "Uruguay",
						"UZ" => "Uzbekistan",
						"VU" => "Vanuatu",
						"VA" => "Vatican City",
						"VE" => "Venezuela",
						"VN" => "Vietnam",
						"WK" => "Wake Island",
						"WF" => "Wallis and Futuna",
						"EH" => "Western Sahara",
						"YE" => "Yemen",
						"ZM" => "Zambia",
						"ZW" => "Zimbabwe",
						"AX" => "Åland Islands",
					);
					break;
					
				case 'us_states': 
					$output = array(
						''=>'',
						'AL'=>'Alabama',
						'AK'=>'Alaska',
						'AS'=>'American Samoa',
						'AZ'=>'Arizona',
						'AR'=>'Arkansas',
						'CA'=>'California',
						'CO'=>'Colorado',
						'CT'=>'Connecticut',
						'DE'=>'Delaware',
						'DC'=>'District of Columbia',
						'FM'=>'Federated States of Micronesia',
						'FL'=>'Florida',
						'GA'=>'Georgia',
						'GU'=>'Guam GU',
						'HI'=>'Hawaii',
						'ID'=>'Idaho',
						'IL'=>'Illinois',
						'IN'=>'Indiana',
						'IA'=>'Iowa',
						'KS'=>'Kansas',
						'KY'=>'Kentucky',
						'LA'=>'Louisiana',
						'ME'=>'Maine',
						'MH'=>'Marshall islands',
						'MD'=>'Maryland',
						'MA'=>'Massachusetts',
						'MI'=>'Michigan',
						'MN'=>'Minnesota',
						'MS'=>'Mississippi',
						'MO'=>'Missouri',
						'MT'=>'Montana',
						'NE'=>'Nebraska',
						'NV'=>'Nevada',
						'NH'=>'New Hampshire',
						'NJ'=>'New Jersey',
						'NM'=>'New Mexico',
						'NY'=>'New York',
						'NC'=>'North Carolina',
						'ND'=>'North Dakota',
						'MP'=>'Northern Mariana Islands',
						'OH'=>'Ohio',
						'OK'=>'Oklahoma',
						'OR'=>'Oregon',
						'PW'=>'Palau',
						'PA'=>'Pennsylvania',
						'PR'=>'Puerto Rico',
						'RI'=>'Rhode Island',
						'SC'=>'South Carolina',
						'SD'=>'South Dakota',
						'TN'=>'Tennessee',
						'TX'=>'Texas',
						'UT'=>'Utah',
						'VT'=>'Vermont',
						'VI'=>'Virgin Islands',
						'VA'=>'Virginia',
						'WA'=>'Washington',
						'WV'=>'West Virginia',
						'WI'=>'Wisconsin',
						'WY'=>'Wyoming',
						'AE'=>'Armed Forces Africa \ Canada \ Europe \ Middle East',
						'AA'=>'Armed Forces America (Except Canada)',
						'AP'=>'Armed Forces Pacific'
					);
					break;
				
				/* add custom variables via this hook */
				case $var : 
					$output = apply_filters('dilaz_mb_choice_'. $var .'_action');
					break; 

				default: break;
				
			}
			
			return $output;
		}
	}
	
	new DilazMetaboxFunction;
}