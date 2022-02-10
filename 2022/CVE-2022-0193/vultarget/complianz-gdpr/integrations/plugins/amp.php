<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );



if ( ! class_exists( "cmplz_amp" ) ) {
	class cmplz_amp {
		private $banner;
		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;
			add_filter( 'amp_post_template_data', array( $this, 'enqueue_amp_assets' ), 10 );
			add_action( 'amp_post_template_footer', array( $this, 'get_amp_banner' ), 9999 );
			add_action( 'wp_footer', array( $this, 'get_amp_banner' ), 9999 );
			add_action( 'wp_ajax_cmplz_amp_endpoint', array( $this, 'amp_endpoint' ) );
			add_action( 'wp_ajax_nopriv_cmplz_amp_endpoint', array( $this, 'amp_endpoint' ) );
			add_action( 'amp_post_template_css', array( $this, 'amp_styles' ) );
			add_action( 'plugins_loaded', array( $this, 'init' ), 11 );
			add_action( 'cmplz_amp_tags', array( $this, 'handle_anonymous_settings' ) );
			add_action('wp', array($this, 'custom_amp_css') );

			add_filter('cmplz_cookieblocker_amp',  array($this, 'cookieblocker_for_amp') );
		}

		static function this() {
			return self::$_this;
		}

		/**
		 * @param $output
		 *
		 * @return string
		 */
		public function cookieblocker_for_amp( $output ) {
			$amp_tags = COMPLIANZ::$config->amp_tags;

			$amp_tags = apply_filters( 'cmplz_amp_tags', $amp_tags );
			foreach ( $amp_tags as $amp_tag ) {
				$output = str_replace( '<' . $amp_tag . ' ',
					'<' . $amp_tag . ' data-block-on-consent ', $output );
			}
			return $output;
		}

		public function custom_amp_css(){

			if ( ! cmplz_is_amp() ) {
				return;
			}
			wp_register_style( 'cmplz_amp_css', false );
			wp_enqueue_style( 'cmplz_amp_css' );
			ob_start();
			$this->amp_styles($post=false);;
			$css = ob_get_clean();
			wp_add_inline_style( 'cmplz_amp_css', $css );
		}



		/**
		 * If set up anonymously, remove the analytics tag
		 *
		 * @param $amp_tags
		 *
		 * @return mixed
		 */

		public function handle_anonymous_settings( $amp_tags ) {

			if ( COMPLIANZ::$cookie_admin->statistics_privacy_friendly() ) {
				unset( $amp_tags['amp-analytics'] );
			}

			return $amp_tags;
		}

		public function init() {

			//load default banner for settings
			$banner_id    = cmplz_get_default_banner_id();
			$this->banner = new CMPLZ_COOKIEBANNER( $banner_id );
		}

		/**
		 * Include AMP component scripts.
		 *
		 * @filter amp_post_template_data
		 *
		 * @param array $data Input from filter.
		 *
		 * @return array
		 */
		public function enqueue_amp_assets( $data ) {
			if ( ! cmplz_is_amp() ) {
				return;
			}

			$custom_component_scripts = array(
				'amp-geo'     => 'https://cdn.ampproject.org/v0/amp-geo-0.1.js',
				'amp-consent' => 'https://cdn.ampproject.org/v0/amp-consent-0.1.js',
			);

			$data['amp_component_scripts']
				= array_merge( $data['amp_component_scripts'],
				$custom_component_scripts );

			return $data;
		}


		public function get_amp_banner() {
			if ( ! cmplz_is_amp() ) {
				return;
			}

			$consentHrefUrl = add_query_arg( 'action', 'cmplz_amp_endpoint', admin_url( 'admin-ajax.php' ) );

			//amp only accepts https or //
			$consentHrefUrl = str_replace( "http://", "//", $consentHrefUrl );

			//check if we're on cookie policy page. If so, we offer a revoke option
			$regions = cmplz_get_regions();
			global $post;
			$post_id   = false;
			$is_policy = false;
			if ( $post ) {
				$post_id = $post->ID;
			}
			foreach ( $regions as $region => $label ) {
				if ( $is_policy ) {
					break;
				}
				$policy_id
					= COMPLIANZ::$document->get_shortcode_page_id( 'cookie-statement',
					$region );
				if ( $policy_id == $post_id ) {
					$is_policy = true;
				}
			}
			$postPromptUI = $is_policy ? '"cmplz-post-consent-ui"' : 'false';
			$revoke       = $is_policy
				? '<div id="cmplz-post-consent-ui"><button on="tap:consent-element.prompt" role="button">'
				  . $this->banner->revoke_x . '</button></div>' : "";
			$html         = '
            <amp-geo layout="nodisplay">
			  <script type="application/json">
			    {
			      "ISOCountryGroups": {
			        "eu": ["preset-eea"]
			      }
			    }
			  </script>
			</amp-geo>
            <amp-consent layout="nodisplay" id="consent-element">
                <script type="application/json">
                    {

                        "consents": {
                            "cmplz-consent": {
                                "promptIfUnknownForGeoGroup": "eu",
                                "checkConsentHref": "' . $consentHrefUrl . '",
                                "promptUI": "cmplz-consent-ui"
                            }
                        },
                       "postPromptUI": ' . $postPromptUI . '
                    }
                </script>
                <div id="cmplz-consent-ui">
                	<div class="cmplz-consent-message">'
			                . $this->banner->message_optin_x . '</div>
                    <button on="tap:consent-element.accept" role="button">'
			                . $this->banner->accept_x . '</button>
                    <button on="tap:consent-element.reject" role="button">'
			                . $this->banner->dismiss_x . '</button>
                </div>
                ' . $revoke . '

            </amp-consent>';
			echo apply_filters( 'cmplz_amp_html', $html );


			// if dismiss is needed
			//<button on="tap:consent-element.dismiss" role="button">Dismiss</button>
		}


		public function amp_endpoint() {
			if ( ! cmplz_is_amp() ) {
				return;
			}

			//check global cookie warning requirement
			//we currently check only for EU region. In other regions, the banner is not shown, using geo ip from Google AMP (free feature).
			$active
				= COMPLIANZ::$cookie_admin->site_needs_cookie_warning( 'eu' )
				  || COMPLIANZ::$cookie_admin->site_needs_cookie_warning( 'uk' );
			//
			//check if this user's region reguires a cookie warning
			$payload
				= file_get_contents( 'php://input' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( ! empty( $payload ) ) {
				$payload = json_decode( $payload, true );

				if ( ! empty( $payload['consentInstanceId'] )
				     && 'cmplz-consent' === $payload['consentInstanceId']
				) {
				}
			}
			wp_send_json( array('promptIfUnknown' => $active ), 200 );
		}


		public function amp_styles( $post_template ) {
			if ( ! cmplz_is_amp() ) {
				return;
			}

			printf(
				'
					.cc-revoke-custom {
						display:none;
					}
					#cmplz-consent-ui, #cmplz-post-consent-ui {
					background-color: %s;
					}
					#cmplz-consent-ui .cmplz-consent-message {
						color: %s;
						padding:6px 0 0 6px;

					}
                    #cmplz-consent-ui button, #cmplz-post-consent-ui button {
                        background-color: %s;
                        color: %s;
                        padding: 6px 11px;
                        margin: 8px;
                        }
                ',
				$this->banner->background['color'],
				$this->banner->text['color'],
				$this->banner->button_background_color,
				$this->banner->button_text_color
			);
			if ( $this->banner->use_custom_cookie_css && strlen( $this->banner->custom_css ) > 0) {
				echo $this->banner->custom_css;
			}
		}


	}
}

$amp = new cmplz_amp;
