<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * mycred_Open_Badge Class 
 * @since 2.1
 * @version 1.0
 */
if ( ! class_exists('mycred_Open_Badge') ) :
	class mycred_Open_Badge {

		public $salt;

		/**
		 * Construct
		 */
		public function __construct () {

			$this->salt = apply_filters( 'mycred_open_badge_salt', 'MYCREDOPENBADGE' );
            
        }

        public function register_open_badge_routes() {

		  	register_rest_route( 'open-badge', '/assertion/', array(
			    'methods' => 'GET',
			    'callback' => array( $this, 'get_assertion_data' ),
			    'permission_callback' => '__return_true',
		  	) );

		  	register_rest_route( 'open-badge', '/info/', array(
			    'methods' => 'GET',
			    'callback' => array( $this, 'get_badge_info' ),
			    'permission_callback' => '__return_true',
		  	) );

		  	register_rest_route( 'open-badge', '/issuer/', array(
			    'methods' => 'GET',
			    'callback' => array( $this, 'get_issuer_data' ),
			    'permission_callback' => '__return_true',
		  	) );
		
		}

		function bake_users_image( $user_id, $badge_id ) {

			$badge = mycred_get_badge( $badge_id );

			if ( ! $badge->open_badge ) return;

			$wp_upload_dirs = wp_upload_dir();
			$basedir = trailingslashit( $wp_upload_dirs[ 'basedir' ] );
			$baseurl = trailingslashit( $wp_upload_dirs[ 'baseurl' ] );

			$folderName = apply_filters( 'mycred_open_badge_folder', 'open_badges' );

			$user_badge_directory = $basedir . $folderName;
			
			if ( ! file_exists( $user_badge_directory ) && ! is_dir( $user_badge_directory ) ) {
				mkdir( $user_badge_directory );         
			}
			
			$user_badge_directory = trailingslashit( $user_badge_directory );

			$badge_image = $badge->main_image_url;
			
			$badge_image_path = str_replace( $baseurl, $basedir, $badge_image );

			$user_info = get_userdata( $user_id );
			
			$identity = 'sha256$' . hash( 'sha256', $user_info->user_email . $this->salt );
			
			$json = $this->get_image_json_data( $user_id, $badge_id, $identity, $badge->title, $badge_image  );

			$filename = "badge-{$badge_id}-{$user_id}.png";

			$this->bake_image( $badge_image, $badge_image_path, $json, $filename, $user_badge_directory );

			$user_image_url = trailingslashit( $baseurl . $folderName ).$user_id;

			$json['user_image'] = trailingslashit( $user_image_url ) . $filename;

			mycred_update_user_meta( $user_id, "open-badge-{$badge_id}-{$user_id}", '', $json );

		}

		public function get_assertion_data() {

			$assertion_data = array();

			if ( ! empty( $_GET['uid'] ) && ! empty( $_GET['bid'] ) ) {
				
				$user_id  = intval( $_GET['uid'] );
				$badge_id = intval( $_GET['bid'] );

				$user_info = get_userdata( $user_id );
				$badge     = mycred_get_badge( $badge_id );

				if ( $user_info && $badge ) {

					mycred_set_current_account( $user_id );

					if( $badge->user_has_badge( $user_id ) ) {

						$identity_id = 'sha256$' . hash( 'sha256', $user_info->user_email . $this->salt );
						
						$issuedOn    = mycred_get_user_meta( $user_id, MYCRED_BADGE_KEY . $badge_id, '_issued_on', true );
					
						$assertion_data = array(
							'@context'  => 'https://w3id.org/openbadges/v2',
							'type'      => 'Assertion',
							'id'        => $this->get_assertion_url( $user_id, $badge_id ),
							'recipient'	=> array(
								'type'     => 'email',
								'hashed'   => true,
								'salt'     => $this->salt,
								'identity' => $identity_id
							),
							'badge'    => $this->get_badge_info_url( $badge_id ),
							'issuedOn' => date( 'Y-m-d\TH:i:sP', $issuedOn ),
							'image'	   => $badge->main_image_url,
							'verification' => array(
								'type' => 'HostedBadge',
								'verificationProperty' => 'id',
							),
							'evidence' => $this->get_evidence_url( $user_id, $badge_id )
						);

					}

				}

			}

			return wp_send_json( $assertion_data );

		}

		public function get_badge_info() {

			$badge_info = array();

			if ( ! empty( $_GET['bid'] ) ) {

				$badge_id = intval( $_GET['bid'] );

				$badge = mycred_get_badge( $badge_id );

				if( $badge ) {

					$badge_info = array(
						'@context'		=> 'https://w3id.org/openbadges/v2',
						'type'			=> 'BadgeClass',
						'id'			=> $this->get_badge_info_url( $badge_id ),
						'name'			=> $badge->title,
						'image'			=> $badge->main_image_url,
						'description'	=> '',
						'criteria'		=> mycred_get_permalink( $badge_id ),
						'issuer'		=> $this->get_endpoint_url( 'issuer' ),
						'tags'			=> array()
					);

				}

			}

			return wp_send_json( $badge_info );
			
		}

		public function get_issuer_data() {

			$blog_title  = get_bloginfo( 'name' );
			$admin_email = get_bloginfo( 'admin_email' );
			$blog_url    = get_site_url();

			$issuer_data = array(
				'@context' => 'https://w3id.org/openbadges/v2',
				'type'	=> 'Issuer',
				'id'	=> $this->get_endpoint_url( 'issuer' ),
				'name'	=> $blog_title,
				'url'	=> $blog_url,
				'email'	=> $admin_email
			);

			return wp_send_json( $issuer_data );

		}

		public function get_image_json_data( $user_id, $badge_id, $identity, $badge_title, $badge_image  ) {

		  	return array(
				'@context'  => 'https://w3id.org/openbadges/v2',
		        'type'      => 'Assertion',
		        'id'        => $this->get_assertion_url( $user_id, $badge_id ),
		        'recipient' => array(
		            'type'     => 'email',
		            'hashed'   => true,
		            'salt'     => $this->salt,
		            'identity' => $identity
		        ),
		        'badge' => array(
		            '@context'    => 'https://w3id.org/openbadges/v2',
		            'type'        => 'BadgeClass',
		            'id'          => $this->get_badge_info_url( $badge_id ),
		            'name'        => $badge_title,
		            'image'       => $badge_image,
		            'description' => '',
		            'criteria'    => mycred_get_permalink( $badge_id ),
					'issuer'	  => $this->get_endpoint_url( 'issuer' ),
		            'tags'        => [],
		        ),
		        'issuedOn'     => date('Y-m-d\TH:i:sP'),
		        'image'        => $badge_image,
		        'verification' => array(
		            'type'                 => 'HostedBadge',
		            'verificationProperty' => 'id',
		        ),
		        'evidence' => $this->get_evidence_url( $user_id, $badge_id )
			);

		}

		public function bake_image( $badge_image, $badge_image_path, $json, $filename, $user_badge_directory ) {

			$png = file_get_contents( $badge_image );
			
			if( $png == false ) {
			    $png = file_get_contents( $badge_image_path, true );
			}

			$embed = [
				'openbadges',
				'',
				'',
				'',
				'',
				( string ) json_encode( $json ),
			];

			// Glue with null-bytes.
			$data = implode( "\0", $embed );

			// Make the CRC.
			$crc = pack( "N", crc32( 'iTXt' . $data ) );

			// Put it all together.
			$final = pack( "N", strlen( $data ) ) . 'iTXt' . $data . $crc;

			// What's the length?
			$length = strlen( $png );

			// Put this all at the end, before IEND.
			// We _should_ be removing all other iTXt blobs with keyword openbadges
			// before writing this out.
			$png = substr( $png, 0, $length - 12 ) . $final . substr( $png, $length - 12, 12 );
			
			file_put_contents( $user_badge_directory . '/' . $filename, $png );	

		}

		public function get_endpoint_url( $endpoint ) {

			return get_site_url() . '/wp-json/open-badge/' . $endpoint;

		}

		public function get_assertion_url( $user_id, $badge_id ) {

			$assertion_url = add_query_arg( 'uid', $user_id,  $this->get_endpoint_url( 'assertion' ) );
			$assertion_url = add_query_arg( 'bid', $badge_id, $assertion_url );

			return $assertion_url;
		}

		public function get_badge_info_url( $badge_id ) {

			$badge_info_url = add_query_arg( 'bid', $badge_id,  $this->get_endpoint_url( 'info' ) );

			return $badge_info_url;

		}

		public function get_evidence_url( $user_id, $badge_id ) {

			$evidence_page_id = mycred_get_evidence_page_id();

			$evidence_url = add_query_arg( 'uid', $user_id,  mycred_get_permalink( $evidence_page_id ) );
			$evidence_url = add_query_arg( 'bid', $badge_id, $evidence_url );

			return $evidence_url;

		}

	}
endif;