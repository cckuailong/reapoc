<?php

defined( 'ABSPATH' ) || exit;


if( ! class_exists( 'Ibtana_AddOns_Update_Checker' ) ) {

  class Ibtana_AddOns_Update_Checker {

    public $version;
		public $plugin_slug;
    public $plugin_file;
		public $cache_key;
		public $cache_allowed;

    private $iepa_license_key = false;

		public function __construct( $params ) {
			$this->plugin_slug   = $params['plugin_slug'];                                               //
      $this->plugin_file   = $params['plugin_file'];                                               //
			$this->version       = $params['version'];                                                   //
			$this->cache_key     = str_replace( '-', '_', $params['plugin_slug'] ) . '_custom_updater';  //
			$this->cache_allowed = false;                                                                //

			add_filter( 'plugins_api', array( $this, 'ive_plugin_info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( $this, 'ive_update_plugins' ) );
			add_action( 'upgrader_process_complete', array( $this, 'ive_plugin_cache_purge' ), 10, 2 );
		}

    public function ibtana_update_checker_get_the_license_status() {
      $iepa_key     = str_replace( '-', '_', get_plugin_data( IEPA_PLUGIN_FILE )['TextDomain'] ) . '_license_key';
      $iepa_key_arr = get_option( $iepa_key );
      $iepa_key_arr_license_status = false;
      if ( $iepa_key_arr ) {
        if ( isset( $iepa_key_arr['license_status'] ) ) {
          if ( true == $iepa_key_arr['license_status'] ) {
            $this->iepa_license_key       = $iepa_key_arr['license_key'];
            $iepa_key_arr_license_status  = true;
          }
        }
      }
      return $iepa_key_arr_license_status;
    }

		public function ive_request_plugin_update() {

      if ( !$this->ibtana_update_checker_get_the_license_status() ) {
        return false;
      }

      $remote = get_transient( $this->cache_key );

			if( false === $remote || ! $this->cache_allowed ) {

        $remote = wp_remote_post(
          IBTANA_LICENSE_API_ENDPOINT . 'get_client_ibtana_add_on_plugin_updater_info_json',
          array(
            'method'      => 'POST',
            'body'        => wp_json_encode( array(
                'add_on_key'                =>  $this->iepa_license_key,
                'site_url'                  =>  site_url(),
                'add_on_plugin_text_domain' =>  $this->plugin_slug,
                'add_on_text_domain'        =>  get_plugin_data( IEPA_PLUGIN_FILE )['TextDomain']
            ) ),
            'timeout'     => 10,
						'headers'     => array(
              'Accept'        =>  'application/json',
              'Content-Type'  =>  'application/json'
            ),
            'data_format' => 'body'
          )
        );

				if(
					is_wp_error( $remote )
					|| 200 !== wp_remote_retrieve_response_code( $remote )
					|| empty( wp_remote_retrieve_body( $remote ) )
				) {
					return false;
				}

				set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

			}

			$remote = json_decode( wp_remote_retrieve_body( $remote ) );

			return $remote;
		}


		function ive_plugin_info( $res, $action, $args ) {
			// do nothing if you're not getting plugin information right now
			if( 'plugin_information' !== $action ) {
				return false;
			}

			// do nothing if it is not our plugin
			if( $this->plugin_slug !== $args->slug ) {
				return false;
			}

			// get updates
			$remote = $this->ive_request_plugin_update();

			if( ! $remote ) {
				return false;
			}

			$res = new stdClass();

			$res->name           = $remote->name;
			$res->slug           = $remote->slug;
			$res->version        = $remote->version;
			$res->tested         = $remote->tested;
			$res->requires       = $remote->requires;
			$res->author         = $remote->author;
			$res->author_profile = $remote->author_profile;
			$res->download_link  = $remote->download_url;
			$res->trunk          = $remote->download_url;
			$res->requires_php   = $remote->requires_php;
			$res->last_updated   = $remote->last_updated;

			$res->sections = array(
				'description'   =>  $remote->sections->description,
				'installation'  =>  $remote->sections->installation,
				'changelog'     =>  $remote->sections->changelog
			);

			if( ! empty( $remote->banners ) ) {
				$res->banners = array(
					'low' => $remote->banners->low,
					'high' => $remote->banners->high
				);
			}

			return $res;

		}

		public function ive_update_plugins( $transient ) {

			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$remote = $this->ive_request_plugin_update();

			if(
				$remote
				&& version_compare( $this->version, $remote->version, '<' )
				&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )
				&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
			) {
				$res = new stdClass();

				$res->slug        = $this->plugin_slug;
				$res->plugin      = $this->plugin_slug . '/' . $this->plugin_file; // plugin-folder/plugin-file.php
				$res->new_version = $remote->version;
				$res->tested      = $remote->tested;
				$res->package     = $remote->download_url;

				$transient->response[ $res->plugin ] = $res;
	    }

			return $transient;

		}

		public function ive_plugin_cache_purge() {

      if (
				$this->cache_allowed
				&& 'update' === $options['action']
				&& 'plugin' === $options[ 'type' ]
			) {
				// just clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );
			}

		}


	}



}
