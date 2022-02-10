<?php defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
if ( ! class_exists( "CMPLZ_COOKIE" ) ) {
	class CMPLZ_COOKIE {
		private $ID = false;
		private $name;

		/**
		 * Sync should the cookie stay in sync or not
		 *
		 * @var bool
		 */
		private $sync = true;

		/**
		 * Retention period
		 *
		 * @var string
		 */
		private $retention;
		private $type;
		private $service;
		private $serviceID;
		private $collectedPersonalData;
		private $cookieFunction;
		private $purpose;
		private $isTranslationFrom;
		private $lastUpdatedDate;
		private $lastAddDate;
		private $firstAddDate;
		private $synced;
		private $complete;
		private $slug = '';
		private $old;
		private $isOwnDomainCookie=false;

		/**
		 * in CDB, we can mark a cookie as not relevant to users.
		 *
		 * @var int
		 */
		private $ignored;
		/**
		 * we do not actually delete it , otherwise it would be found on next run again
		 *
		 * @var
		 */
		private $deleted;
		/**
		 * give user the possibility to hide a cookie
		 *
		 * @var bool
		 */
		private $showOnPolicy = true;
		private $isPersonalData;
		private $isMembersOnly;
		private $languages;
		private $language;

		function __construct(
			$name = false, $language = 'en', $service_name = false
		) {
			if ( is_numeric( $name ) ) {
				$this->ID = intval( $name );
			} else {
				$this->name = $this->sanitize_cookie( $name );
			}

			$this->language = cmplz_sanitize_language( $language );
			if ( $service_name ) {
				$this->service = $service_name;
			}

			if ( $this->name !== false ) {
				//initialize the cookie with this id.
				$this->get();

				//make sure each language is available
			}
		}

		/**
		 * Add a new cookie for each passed language.
		 *
		 * @param             $name
		 * @param array       $languages
		 * @param string|bool $return_language
		 * @param bool        $service_name
		 * @param bool        $sync_on
		 *
		 * @return int cookie_id
		 */

		public function add(
			$name, $languages = array( 'en' ), $return_language = false, $service_name = false, $sync_on = true
		) {
			$this->name = $this->sanitize_cookie( $name );

			//the parent cookie gets "en" as default language
			$this->language = 'en';
			$return_id      = 0;

			$this->languages = cmplz_sanitize_languages( $languages );

			//check if there is a parent cookie for this name
			$this->get( true );
			//if no ID is found, insert  in the database
			if ( ! $this->ID ) {
				$this->service      = $service_name;
				$this->sync         = $sync_on;
				$this->showOnPolicy = true;
			}

			//we save, to update previous, but also to make sure last add date is saved.
			$this->lastAddDate = time();
			$this->save();

			//we now should have an ID, which will be the parent item
			$parent_ID = $this->ID;

			if ( $return_language == 'en' ) {
				$return_id = $this->ID;
			}

			//make sure each language is available
			foreach ( $this->languages as $language ) {
				if ( $language == 'en' ) {
					continue;
				}
				$translated_cookie = new CMPLZ_COOKIE( $name, $language, $service_name );
				if ( ! $translated_cookie->ID ) {
					$translated_cookie->sync         = $sync_on;
					$translated_cookie->showOnPolicy = true;
				}
				$translated_cookie->isOwnDomainCookie = $this->isOwnDomainCookie;
				$translated_cookie->isTranslationFrom = $parent_ID;
				$translated_cookie->service           = $service_name;
				$translated_cookie->lastAddDate       = time();
				$translated_cookie->save();
				if ( $return_language && $language == $return_language ) {
					$return_id = $translated_cookie->ID;
				}

			}

			return $return_id;

		}

		public function __get( $property ) {
			if ( property_exists( $this, $property ) ) {
				return $this->$property;
			}
		}

		public function __set( $property, $value ) {
			if ( property_exists( $this, $property ) ) {
				$this->$property = $value;
			}

			return $this;
		}

		/**
		 * Delete this cookie, and all translations linked to it.
		 */

		public function delete() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! $this->ID ) {
				return;
			}

			$translations = $this->get_translations();
			global $wpdb;
			foreach ( $translations as $ID ) {
				$wpdb->update(
					$wpdb->prefix . 'cmplz_cookies',
					array( 'deleted' => true ),
					array( 'ID' => $ID )
				);
			}
		}

		/**
		 * Restore a deleted cookie
		 */

		public function restore() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! $this->ID ) {
				return;
			}

			$translations = $this->get_translations();
			global $wpdb;
			foreach ( $translations as $ID ) {
				$wpdb->update(
					$wpdb->prefix . 'cmplz_cookies',
					array( 'deleted' => false ),
					array( 'ID' => $ID )
				);
			}
		}


		public function get_translations() {
			global $wpdb;
			//check if this cookie is a parent
			if ( ! $this->isTranslationFrom ) {
				//is parent. Get all cookies where translationfrom = this id
				$parent_id = $this->ID;
			} else {
				//not parent.
				$parent_id = $this->isTranslationFrom;
			}

			$sql
				          = $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where isTranslationFrom = %s",
				$parent_id );
			$results      = $wpdb->get_results( $sql );
			$translations = wp_list_pluck( $results, 'ID' );

			//add the parent id
			$translations[] = $parent_id;

			return $translations;
		}

		/**
		 * Retrieve the cookie data from the table
		 *
		 * @param bool $parent get only the parent cookie, not a translation
		 */

		private function get( $parent = false ) {
			global $wpdb;

			if ( ! $this->name && ! $this->ID ) {
				return;
			}
			$sql = '';
			if ( $parent ) {
				$sql = " AND isTranslationFrom = FALSE";
			}

			//if the service is set, we check within the service as well.
			if ( $this->service ) {
				$service = new CMPLZ_SERVICE($this->service, $this->language );
				if ($service->ID) $sql .= $wpdb->prepare(" AND serviceID = %s", $service->ID);
			}

			if ( $this->ID ) {
				$cookie = $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where ID = %s ", $this->ID ) );
			} else {
				$cookie = $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where name = %s and language = %s $sql", $this->name, $this->language ) );
			}

			//if there's no match, try to do a fuzzy match
			if ( ! $cookie ) {
				$cookies = $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where language = %s $sql", $this->language ) );
				$cookies = wp_list_pluck( $cookies, 'name', 'ID' );
				$cookie_id = $this->get_fuzzy_match( $cookies, $this->name );
				if ( $cookie_id ) {
					$cookie
						= $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where ID = %s",
						$cookie_id ) );
				}
			}

			if ( $cookie ) {
				$this->ID                    = $cookie->ID;
				$this->name                  = substr($cookie->name, 0, 200); //maximize cookie name length
				$this->serviceID             = $cookie->serviceID;
				$this->sync                  = $cookie->sync;
				$this->language              = $cookie->language;
				$this->ignored               = $cookie->ignored;
				$this->deleted               = $cookie->deleted;
				$this->retention             = $cookie->retention;
				$this->type                  = $cookie->type;
				$this->isOwnDomainCookie     = $cookie->isOwnDomainCookie;
				$this->cookieFunction        = $cookie->cookieFunction;
				$this->purpose               = $cookie->purpose;
				$this->isPersonalData        = $cookie->isPersonalData;
				$this->isMembersOnly         = $cookie->isMembersOnly;
				$this->collectedPersonalData = $cookie->collectedPersonalData;
				$this->isTranslationFrom     = $cookie->isTranslationFrom;
				$this->showOnPolicy          = $cookie->showOnPolicy;
				$this->lastUpdatedDate       = $cookie->lastUpdatedDate;
				$this->lastAddDate           = $cookie->lastAddDate;
				$this->firstAddDate          = $cookie->firstAddDate;
				$this->slug                  = $cookie->slug;
				$this->synced                = $cookie->lastUpdatedDate > 0
					? true : false;
				$this->old                   = $cookie->lastAddDate
				                               < strtotime( '-3 months' )
				                               && $cookie->lastAddDate > 0
					? true : false;
			}

			/**
			 * Don't translate with Polylang, as polylang does not use the fieldname to translate. This causes mixed up strings when context differs.
			 * To prevent newly added cookies from getting translated, only translate when not in admin or cron, leaving front-end, where cookies aren't saved.
			 */
			if ( !defined('POLYLANG_VERSION') && $this->language !== 'en' && !is_admin() && !wp_doing_cron() ) {
				if (!empty($this->retention) ) $this->retention = cmplz_translate($this->retention, 'cookie_retention');
				//type should not be translated
				if (!empty($this->cookieFunction) ) $this->cookieFunction = cmplz_translate($this->cookieFunction, 'cookie_function');
				if (!empty($this->purpose) ) $this->purpose = cmplz_translate($this->purpose, 'cookie_purpose');
				if (!empty($this->collectedPersonalData) ) $this->collectedPersonalData = cmplz_translate($this->collectedPersonalData, 'cookie_collected_personal_data');
			}

			/**
			 * complianz cookie retention can be retrieved form this site
			 */

			if ( strpos( $this->name, 'cmplz' ) !== false
			     || strpos( $this->name, 'complianz' ) !== false
			) {
				$this->retention = sprintf( __( "%s days", "complianz-gdpr" ),
					cmplz_get_value( 'cookie_expiry' ) );
			}

			//get serviceid from service name
			if ( $this->serviceID ) {
				$service       = new CMPLZ_SERVICE( $this->serviceID,
					$this->language );
				$this->service = $service->name;
			}

			$this->complete = ( strlen( $this->name ) != 0
			                    && strlen( $this->purpose ) != 0
			                    && strlen( $this->retention ) != 0
			                    && strlen( $this->service ) != 0
			);

		}

		/**
		 * - opslaan service ID met ID uit CDB
		 * - Als SERVICE ID er nog niet is, toevoegen in tabel
		 * - Synce services met CDB
		 */


		/**
		 * Saves the data for a given Cookie, or creates a new one if no ID was passed.
		 *
		 * @param bool $updateAllLanguages
		 */

		public function save( $updateAllLanguages = false ) {
			//don't save empty items.
			if ( strlen( $this->name ) == 0 ) {
				return;
			}
			//get serviceid from service name
			if ( strlen( $this->service ) != 0 ) {
				$service = new CMPLZ_SERVICE( $this->service, $this->language );
				if ( ! $service->ID ) {
					$languages       = $this->get_used_languages();
					$this->serviceID = $service->add( $this->service, $languages, $this->language );
				} else {
					$this->serviceID = $service->ID;
				}
			}

			/**
			 * complianz cookie retention can be retrieved form this site
			 */

			if ( strpos( $this->name, 'cmplz' ) !== false
			     || strpos( $this->name, 'complianz' ) !== false
			) {
				$this->retention = sprintf( __( "%s days", "complianz-gdpr" ),
					cmplz_get_value( 'cookie_expiry' ) );
			}

			/**
			 * Don't translate with Polylang, as polylang does not use the fieldname to translate. This causes mixed up strings when context differs.
			 */

			if ( !defined('POLYLANG_VERSION') ) {
				cmplz_register_translation($this->retention, 'cookie_retention');
				cmplz_register_translation($this->type, 'cookie_storage_type');
				cmplz_register_translation($this->cookieFunction, 'cookie_function');
				cmplz_register_translation($this->purpose, 'cookie_purpose');
				cmplz_register_translation($this->collectedPersonalData, 'cookie_collected_personal_data');
			}

			$update_array = array(
				'name'                  => sanitize_text_field( $this->name ),
				'retention'             => sanitize_text_field( $this->retention ),
				'type'                  => sanitize_text_field( $this->type ),
				'isOwnDomainCookie'       => boolval( $this->isOwnDomainCookie ),
				'serviceID'             => intval( $this->serviceID ),
				'cookieFunction'        => sanitize_text_field( $this->cookieFunction ),
				'purpose'               => sanitize_text_field( $this->purpose ),
				'isPersonalData'        => boolval( $this->isPersonalData ),
				'isMembersOnly'         => boolval( $this->isMembersOnly ),
				'collectedPersonalData' => sanitize_text_field( $this->collectedPersonalData ),
				'sync'                  => boolval( $this->sync ),
				'ignored'               => boolval( $this->ignored ),
				'deleted'               => boolval( $this->deleted ),
				'language'              => cmplz_sanitize_language( $this->language ),
				'isTranslationFrom'     => intval( $this->isTranslationFrom ),
				'showOnPolicy'          => boolval( $this->showOnPolicy ),
				'lastUpdatedDate'       => intval( $this->lastUpdatedDate ),
				'lastAddDate'           => intval( $this->lastAddDate ),
				'slug'                  => sanitize_title( $this->slug ),
			);

			if ( strlen( $this->firstAddDate ) == 0 ) {
				$update_array['firstAddDate'] = time();
			}


			global $wpdb;
			//if we have an ID, we update the existing value
			if ( $this->ID ) {

				$wpdb->update( $wpdb->prefix . 'cmplz_cookies',
					$update_array,
					array( 'ID' => $this->ID )
				);
			} else {
				$wpdb->insert(
					$wpdb->prefix . 'cmplz_cookies',
					$update_array
				);
				$this->ID = $wpdb->insert_id;
			}


			if ( $updateAllLanguages ) {
				//keep all translations in sync
				$translationIDS = $this->get_translations();
				foreach ( $translationIDS as $translationID ) {

					if ( $this->ID == $translationID ) {
						continue;
					}
					$translation                 = new CMPLZ_COOKIE( $translationID );
					$translation->name           = $this->name;
					$translation->serviceID      = $this->serviceID;
					$translation->sync           = $this->sync;
					$translation->save();
				}
			}
		}


		private function get_used_languages() {
			global $wpdb;

			$sql = "SELECT language FROM {$wpdb->prefix}cmplz_cookies group by language";
			$languages = $wpdb->get_results( $sql );
			$languages = wp_list_pluck( $languages, 'language' );

			return $languages;
		}

		/**
		 * Validate a cookie string
		 *
		 * @param $cookie
		 *
		 * @return string|bool
		 */

		private function sanitize_cookie( $cookie ) {
			if ( ! $this->is_valid_cookie( $cookie ) ) {
				return false;
			}

			$cookie = sanitize_text_field( $cookie );

			//100 characters max
			$cookie = substr($cookie, 0, 100);

			//remove whitespace
			$cookie = trim( $cookie );

			//strip double and single quotes
			$cookie = str_replace( '"', '', $cookie );
			$cookie = str_replace( "'", '', $cookie );

			return $cookie;
		}

		/**
		 * Check if a cookie is of a valid cookie structure
		 *
		 * @param $id
		 *
		 * @return bool
		 */

		private function is_valid_cookie( $id ) {
			if ( ! is_string( $id ) ) {
				return false;
			}

			$pattern = '/[a-zA-Z0-9\_\-\*]/i';

			return (bool) preg_match( $pattern, $id );
		}


		private function get_fuzzy_match( $cookies, $search ) {
			//to prevent match from wp_comment_123 on wp_*
			//we keep track of all matches, and only return the longest match, which is the closest match.
			$match            = false;
			$new_match        = false;
			$match_length     = 0;
			$new_match_length = 0;
			$partial          = '*';

			//clear up items without any match possibility
			foreach ( $cookies as $post_id => $cookie_name ) {
				if ( strpos( $cookie_name, $partial ) === false ) {
					unset( $cookies[ $post_id ] );
				}
			}

			foreach ( $cookies as $post_id => $compare_cookie_name ) {
				//check if the string "partial" is in the comparison cookie name
				//check if it has an underscore before or after the partial. If so, take it into account

				//get the substring before or after the partial
				$str1 = substr( $compare_cookie_name, 0,
					strpos( $compare_cookie_name, $partial ) );
				$str2 = substr( $compare_cookie_name,
					strpos( $compare_cookie_name, $partial )
					+ strlen( $partial ) );
				//a partial match is enough on this type

				//$str2: match should end with this string
				if ( strlen( $str1 ) == 0 ) {
					$len     = strlen( $search ); //"*test" : 5
					$pos     = strpos( $search, $str2 ); //"*test" : 1
					$sub_len = strlen( $str2 ); // 4
					if ( $pos !== false && ( $len - $sub_len == $pos ) ) {
						$new_match_length = strlen( $str1 ) + strlen( $str2 );
						$new_match        = $post_id;
					}
					//match should start with this string
				} elseif ( strlen( $str2 ) == 0 ) {

					$pos = strpos( $search, $str1 );
					if ( $pos === 0 ) {
						$new_match_length = strlen( $str1 ) + strlen( $str2 );
						$new_match        = $post_id;
					}
				} else {
					$len2     = strlen( $search ); //"*test" : 5
					$pos2     = strpos( $search, $str2 ); //"*test" : 1
					$sub_len2 = strlen( $str2 ); // 4
					if ( strpos( $search, $str1 ) === 0
					     && strpos( $search, $str2 ) !== false
					     && ( $len2 - $sub_len2 == $pos2 )
					) {
						$new_match_length = strlen( $str1 ) + strlen( $str2 );
						$new_match        = $post_id;
					}
				}

				if ( $new_match_length > $match_length ) {
					$match_length = $new_match_length;
					$match        = $new_match;
				}
			}

			return $match;
		}

	}
}

/**
 * Install cookies table
 * */

add_action( 'admin_init', 'cmplz_install_cookie_table' );
function cmplz_install_cookie_table() {
	if ( get_option( 'cmplz_cookietable_version' ) != cmplz_version ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'cmplz_cookies';
		$sql             = "CREATE TABLE $table_name (
             `ID` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(250) NOT NULL,
            `slug` varchar(250) NOT NULL,
            `sync` int(11) NOT NULL,
            `ignored` int(11) NOT NULL,
            `retention` text NOT NULL,
            `type` text NOT NULL,
            `serviceID` int(11) NOT NULL,
            `cookieFunction` text NOT NULL,
            `collectedPersonalData` text NOT NULL,
            `purpose` text NOT NULL,
            `language` varchar(6) NOT NULL,
            `isTranslationFrom` int(11) NOT NULL,
            `isPersonalData` int(11) NOT NULL,
            `isOwnDomainCookie` int(11) NOT NULL,
            `deleted` int(11) NOT NULL,
            `isMembersOnly` int(11) NOT NULL,
            `showOnPolicy` int(11) NOT NULL,
            `lastUpdatedDate` int(11) NOT NULL,
            `lastAddDate` int(11) NOT NULL,
            `firstAddDate` int(11) NOT NULL,
              PRIMARY KEY  (ID)
            ) $charset_collate;";
		dbDelta( $sql );

		/**
		 * Services
		 */
		$table_name = $wpdb->prefix . 'cmplz_services';
		$sql        = "CREATE TABLE $table_name (
                 `ID` int(11) NOT NULL AUTO_INCREMENT,
                 `name` varchar(250) NOT NULL,
                 `slug` varchar(250) NOT NULL,
                 `serviceType` varchar(250) NOT NULL,
                 `category` varchar(250) NOT NULL,
                 `thirdParty` int(11) NOT NULL,
                 `sharesData` int(11) NOT NULL,
                 `secondParty` int(11) NOT NULL,
                 `privacyStatementURL` varchar(250) NOT NULL,
                 `language` varchar(6) NOT NULL,
                `isTranslationFrom` int(11) NOT NULL,
                `sync` int(11) NOT NULL,
                `lastUpdatedDate` int(11) NOT NULL,
                  PRIMARY KEY  (ID)
                ) $charset_collate;";
		dbDelta( $sql );

		update_option( 'cmplz_cookietable_version', cmplz_version );

	}
}
