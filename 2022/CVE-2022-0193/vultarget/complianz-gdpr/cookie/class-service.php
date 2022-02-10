<?php defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
if ( ! class_exists( "CMPLZ_SERVICE" ) ) {
	class CMPLZ_SERVICE {
		private $ID = false;
		private $name;
		private $serviceType;
		private $category;
		private $sharesData;
		private $thirdParty;
		private $secondParty; //service that share data, but have cookies on the sites domain
		private $sync;
		private $synced;
		private $privacyStatementURL;
		private $isTranslationFrom;
		private $lastUpdatedDate;
		private $language;
		private $languages;
		private $complete;
		private $slug;

		function __construct( $ID = false, $language = 'en' ) {
			$this->language = cmplz_sanitize_language( $language );

			if ( $ID ) {
				if ( is_numeric( $ID ) ) {
					$this->ID = intval( $ID );
				} else {
					$this->name = $this->sanitize_service( $ID );
				}
			}

			if ( $this->name !== false || $this->ID !== false ) {
				//initialize the cookie with this id.
				$this->get();
			}
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
		 * retrieve list of cookies with this service
		 *
		 * @return array() $cookies
		 */

		public function get_cookies() {
			if ( ! $this->ID ) {
				return array();
			}
			global $wpdb;
			$cookies
				= $wpdb->get_results( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_cookies where serviceID = %s ",
				$this->ID ) );

			return $cookies;
		}

		/**
		 * Retrieve the service data from the table
		 *
		 * @param $parent //if it should be the parent itme
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

			if ( $this->ID ) {
				$service
					= $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_services where ID = %s ",
					$this->ID ) );
			} else {
				$service
					= $wpdb->get_row( $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_services where name = %s and language = %s "
					                                  . $sql, $this->name,
					$this->language ) );
			}

			if ( $service ) {
				$this->ID                  = $service->ID;
				$this->name                = $service->name;
				$this->serviceType         = $service->serviceType;
				$this->sharesData
				                           = $service->thirdParty; //legacy, sharesData was first called thirdparty
				$this->secondParty         = $service->secondParty;
				$this->thirdParty          = $this->sharesData
				                             && ! $service->secondParty;
				$this->sync                = $service->sync;
				$this->privacyStatementURL = $service->privacyStatementURL;
				$this->language            = $service->language;
				$this->category            = $service->category;
				$this->isTranslationFrom   = $service->isTranslationFrom;
				$this->lastUpdatedDate     = $service->lastUpdatedDate;
				$this->synced              = $service->lastUpdatedDate > 0 ? true : false;
				$this->slug                = $service->slug;

				$this->complete = ! ( strlen( $this->name ) == 0
				                      || ( strlen( $this->privacyStatementURL )
				                           == 0
				                           && $this->sharesData )
				                      || strlen( $this->serviceType ) == 0
				                      || strlen( $this->name ) == 0 );
			}

		}

		/**
		 * Saves the data for a given service, ore creates a new one if no ID was passed.
		 * @param bool $updateAllLanguages
		 * @param bool   $forceWizardUpdate
		 */
		public function save( $updateAllLanguages = false, $forceWizardUpdate = true) {
			if ( strlen( $this->name ) == 0 ) {
				return;
			}

			if ($forceWizardUpdate) $this->add_to_wizard( $this->name );

			cmplz_register_translation($this->serviceType, 'service_type');

			$update_array = array(
				'name'                => sanitize_text_field( $this->name ),
				'thirdParty'          => boolval( $this->sharesData ),
				//legacy, sharesData was first called third party.
				'sharesData'          => boolval( $this->sharesData ),
				//fluid upgrade
				'secondParty'         => boolval( $this->secondParty ),
				'sync'                => boolval( $this->sync ),
				'serviceType'         => sanitize_text_field( $this->serviceType ),
				'privacyStatementURL' => sanitize_text_field( $this->privacyStatementURL ),
				'language'            => cmplz_sanitize_language( $this->language ),
				'category'            => sanitize_text_field( $this->category ),
				'isTranslationFrom'   => sanitize_text_field( $this->isTranslationFrom ),
				'lastUpdatedDate'     => intval( $this->lastUpdatedDate ),
				'slug'                => sanitize_title( $this->slug ),

			);

			global $wpdb;
			//if we have an ID, we update the existing value
			if ( $this->ID ) {
				$wpdb->update( $wpdb->prefix . 'cmplz_services',
					$update_array,
					array( 'ID' => $this->ID )
				);
			} else {
				$wpdb->insert(
					$wpdb->prefix . 'cmplz_services',
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
					$translation               = new CMPLZ_SERVICE( $translationID );
					$translation->name         = $this->name;
					$translation->sync         = $this->sync;
					$translation->serviceType  = $this->serviceType;
					$translation->sharesData   = $this->sharesData;
					$translation->showOnPolicy = $this->showOnPolicy;
					$translation->lastUpdatedDate = $this->lastUpdatedDate;
					$translation->save();
				}
			}
		}


		/**
		 * Delete this service, and all translations linked to it.
		 */

		public function delete() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! $this->ID ) {
				return;
			}

			//get all related cookies, and delete them.
			$cookies = $this->get_cookies();
			foreach ( $cookies as $service_cookie ) {
				$cookie = new CMPLZ_COOKIE( $service_cookie->ID );
				$cookie->delete();
			}

			$this->drop_from_wizard( $this->name );
			$translations = $this->get_translations();
			global $wpdb;
			foreach ( $translations as $ID ) {
				$wpdb->delete(
					$wpdb->prefix . 'cmplz_services',
					array( 'ID' => $ID )
				);
			}
		}

		/**
		 * Keep services in sync with the services in the list of the wizard.
		 *
		 * @param $service
		 */

		private function drop_from_wizard( $service ) {
			$slug            = $this->get_service_slug( $service );
			$wizard_settings = get_option( 'complianz_options_wizard' );
			if ( isset( $wizard_settings['thirdparty_services_on_site'][ $slug ] )
			     && $wizard_settings['thirdparty_services_on_site'][ $slug ]
			        == 1
			) {
				unset( $wizard_settings['thirdparty_services_on_site'][ $slug ] );
				update_option( 'complianz_options_wizard', $wizard_settings );
			}

			if ( isset( $wizard_settings['socialmedia_on_site'][ $slug ] )
			     && $wizard_settings['socialmedia_on_site'][ $slug ] == 1
			) {
				unset( $wizard_settings['socialmedia_on_site'][ $slug ] );
				update_option( 'complianz_options_wizard', $wizard_settings );
			}

		}


		/**
		 * Keep services in sync with the services in the list of the wizard.
		 *
		 * @param $service
		 */

		private function add_to_wizard( $service ) {

			$slug                = $this->get_service_slug( $service );
			$wizard_settings     = get_option( 'complianz_options_wizard' );
			$registered_services = COMPLIANZ::$config->thirdparty_services;

			if ( isset( $registered_services[ $slug ] )
			     && ( ! isset( $wizard_settings['thirdparty_services_on_site'][ $slug ] )
			          || $wizard_settings['thirdparty_services_on_site'][ $slug ]
			             != 1 )
			) {
				$wizard_settings['thirdparty_services_on_site'][ $slug ] = 1;
				update_option( 'complianz_options_wizard', $wizard_settings );
			}

			$registered_social = COMPLIANZ::$config->thirdparty_socialmedia;

			if ( isset( $registered_social[ $slug ] )
			     && ( ! isset( $wizard_settings['socialmedia_on_site'][ $slug ] )
			          || $wizard_settings['socialmedia_on_site'][ $slug ] != 1 )
			) {
				$wizard_settings['socialmedia_on_site'][ $slug ] = 1;
				update_option( 'complianz_options_wizard', $wizard_settings );
			}
		}

		/**
		 * Get slug from service
		 *
		 * @param $name
		 *
		 * @return bool|false|int|string
		 */

		private function get_service_slug( $name ) {
			$services = COMPLIANZ::$config->thirdparty_services;
			if ( ( $slug = array_search( $name, $services ) ) !== false ) {
				return $slug;
			}
			$social = COMPLIANZ::$config->thirdparty_socialmedia;
			if ( ( $slug = array_search( $name, $social ) ) !== false ) {
				return $slug;
			}

			return false;
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
				          = $wpdb->prepare( "select * from {$wpdb->prefix}cmplz_services where isTranslationFrom = %s",
				$parent_id );
			$results      = $wpdb->get_results( $sql );
			$translations = wp_list_pluck( $results, 'ID' );

			//add the parent id
			$translations[] = $parent_id;

			return $translations;
		}

		/**
		 * Add service to the database
		 *
		 * @param        $name
		 * @param array  $languages
		 * @param string $return_language
		 * @param string $categoryc
		 * @param bool   $sync_on
		 *
		 * @return bool|int
		 */

		public function add(
			$name, $languages = array( 'en' ), $return_language = 'en',
			$category = '', $sync_on = true
		) {
			$return_id = false;
			//insert for each language
			$this->languages = cmplz_sanitize_languages( $languages );
			$this->name      = $name;
			//check if there is a parent cookie for this name
			$this->get( true );

			//if no ID is found, insert in the database
			if ( ! $this->ID ) {
				$this->sync     = $sync_on;
				$this->category = $category;
				$this->save();
			}

			$parent_ID = $this->ID;

			if ( $return_language == 'en' ) {
				$return_id = $this->ID;
			}

			//make sure each language is available
			foreach ( $this->languages as $language ) {
				if ( $language == 'en' ) {
					continue;
				}

				$translated_service = new CMPLZ_SERVICE( $name, $language );
				if ( ! $translated_service->ID ) {
					$translated_service->sync = $sync_on;
				}
				$translated_service->category          = $category;
				$translated_service->isTranslationFrom = $parent_ID;
				$translated_service->save(false, false);

				if ( $return_language && $language == $return_language ) {
					$return_id = $translated_service->ID;
				}

			}

			return $return_id;

		}

		/**
		 * Validate a service string
		 *
		 * @param $service
		 *
		 * @return string|bool
		 */

		private function sanitize_service( $service ) {

			return sanitize_text_field( $service );
		}


	}
}


