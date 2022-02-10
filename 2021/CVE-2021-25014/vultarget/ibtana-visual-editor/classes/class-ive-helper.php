<?php
/**
 * IVE Helper.
 *
 * @package IVE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'IVE_Helper' ) ) {

	/**
	 * Class IVE_Helper.
	 */
	final class IVE_Helper {


		/**
		 * Member Variable
		 *
		 * @since 0.0.1
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 0.0.1
		 * @var instance
		 */
		public static $block_list;

		/**
		 * Page Blocks Variable
		 *
		 * @since 1.6.0
		 * @var instance
		 */
		public static $page_blocks;

		/**
		 * Current Block List
		 *
		 * @since 1.13.4
		 * @var current_block_list
		 */
		public static $current_block_list = array();

		/**
		 * IVE Block Flag
		 *
		 * @since 1.13.4
		 * @var ive_flag
		 */
		public static $ive_flag = false;

		/**
		 * IVE File Generation Flag
		 *
		 * @since 1.14.0
		 * @var file_generation
		 */
		public static $file_generation = 'disabled';

		/**
		 * IVE File Generation Fallback Flag for CSS
		 *
		 * @since 1.15.0
		 * @var file_generation
		 */
		public static $fallback_css = false;
		public static $custom_fallback_css = false;


		/**
		 * IVE File Generation Fallback Flag for JS
		 *
		 * @since 1.15.0
		 * @var file_generation
		 */
		public static $fallback_js = false;
		public static $custom_fallback_js = false;


		/**
		 * Enque Style Variable
		 *
		 * @since 1.14.0
		 * @var instance
		 */
		public static $css_file_handler = array();


		public static $can_enqueue_custom_assets = false;

		/**
		 * Stylesheet
		 *
		 * @since 1.13.4
		 * @var stylesheet
		 */
		public static $stylesheet;

		public static $custom_stylesheet;
		/**
		 * Script
		 *
		 * @since 1.13.4
		 * @var script
		 */
		public static $script = '';

		public static $custom_script = '';

		/**
		 * Google fonts to enqueue
		 *
		 * @var array
		 */
		public static $gfonts = array();

		/**
		 *  Initiator
		 *
		 * @since 0.0.1
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! defined( 'FS_CHMOD_FILE' ) ) {
				define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
			}

			self::$block_list      = IVE_Config::get_block_attributes();
			self::$file_generation = self::allow_file_generation();

			$this->ive_check_if_files_can_be_enqueued();
			add_action( 'wp', array( $this, 'generate_assets' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'generate_asset_files' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'block_assets' ), 10 );
			add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 120 );
			add_action( 'wp_head', array( $this, 'print_stylesheet' ), 80 );
			add_action( 'wp_footer', array( $this, 'print_script' ), 1000 );
		}


		public function ive_check_if_files_can_be_enqueued() {
			$iepa_key = get_option( str_replace( '-', '_', 'ibtana-ecommerce-product-addons' ) . '_license_key' );


			$was_license_active_or_expired	= false;
			$is_license_active							= false;
			$is_license_suspended						= false;

			if ( $iepa_key ) {
				if ( isset( $iepa_key['license_key'] ) && isset( $iepa_key['license_status'] ) ) {

					if ( ( $iepa_key['license_key'] != '' ) && ( isset( $iepa_key['is_expired'] ) && ( $iepa_key['is_expired'] == true ) ) ) {
						$was_license_active_or_expired		= true;
					}

					if ( ( $iepa_key['license_key'] != '' ) && ( $iepa_key['license_status'] == true ) ) {
						$is_license_active		= true;
					}

					if ( isset( $iepa_key['is_suspended'] ) && ( $iepa_key['is_suspended'] == true ) ) {
						$is_license_suspended	= true;
					}
				}
			}

			if ( ( ( $was_license_active_or_expired == true ) || ( $is_license_active == true ) ) && ( $is_license_suspended == false ) ) {
				self::$can_enqueue_custom_assets = true;
			}

		}


		/**
		 * This is the action where we create dynamic asset files.
		 * CSS Path : uploads/ive-plugin/ive-style-{post_id}-{timestamp}.css
		 * JS Path : uploads/ive-plugin/ive-script-{post_id}-{timestamp}.js
		 *
		 * @since 1.15.0
		 */
		public function generate_asset_files() {

			global $content_width;
			self::$stylesheet = str_replace( '#CONTENT_WIDTH#', $content_width . 'px', self::$stylesheet );
			if ( '' !== self::$script ) {
				self::$script = 'document.addEventListener("DOMContentLoaded", function(){ ' . self::$script . ' })';
			}
			if ( 'enabled' === self::$file_generation ) {
				self::file_write( self::$stylesheet, 'css' );
				self::file_write( self::$script, 'js' );

				// Custom File Write
				if ( self::$can_enqueue_custom_assets == true ) {
					self::custom_file_write( self::$custom_stylesheet, 'custom_css' );
					self::custom_file_write( self::$custom_script, 'custom_js' );
				}

			}
		}


		public static function custom_file_write( $style_data, $type ) {
			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return false;
			}

			$var            = ( 'custom_css' === $type ) ? 'custom_css' : 'custom_js';

			$file_system    = self::get_instance()->get_filesystem();

			// Asset Info
			$uploads_dir	=	self::get_upload_dir();
			$css_suffix		= 'ive-custom-style';
			$js_suffix		= 'ive-custom-script';
			$assets_info	= array();

			if ( 'custom_css' === $type ) {

				$assets_info['custom_css']			=	$uploads_dir['path'] . $css_suffix . '.css';
				$assets_info['custom_css_url']	=	$uploads_dir['url'] . $css_suffix . '.css';

			} elseif ( 'custom_js' === $type ) {

				$assets_info['custom_js']			=	$uploads_dir['path'] . $js_suffix . '.js';
				$assets_info['custom_js_url']	=	$uploads_dir['url'] . $js_suffix . '.js';

			}

			$relative_src_path = $assets_info[ $var ];

			if ( '' === $style_data ) {
				/**
				 * This is when the generated CSS is blank.
				 * This means this page does not use IVE block.
				 * In this scenario we need to delete the existing file.
				 * This will ensure there are no extra files added for user.
				*/

				if ( file_exists( $relative_src_path ) ) {
					// Delete old file.
					wp_delete_file( $relative_src_path );
				}

				return true;
			}

			 /**
 			 * Need to create new assets.
 			 */
			if ( ! $file_system->exists( $relative_src_path ) ) {
				$did_create = self::create_custom_file( $assets_info, $style_data, $type );

				if ( $did_create ) {
					self::$css_file_handler = array_merge( self::$css_file_handler, $assets_info );
				}

				return $did_create;
			}


			/**
			 * File already exists.
			 * Need to match the content.
			 * If new content is present we update the current assets.
			 */
			if ( file_exists( $relative_src_path ) ) {
				$old_data = $file_system->get_contents( $relative_src_path );

				if ( $old_data !== $style_data ) {

					// Delete old file.
					wp_delete_file( $relative_src_path );

					// Create a new file.
					$did_create = self::create_custom_file( $assets_info, $style_data, $type );

					if ( $did_create ) {
						self::$css_file_handler = array_merge( self::$css_file_handler, $assets_info );
					}

					return $did_create;
				}
			}

			self::$css_file_handler = array_merge( self::$css_file_handler, $assets_info );

			return true;
		}


		public static function create_custom_file( $assets_info, $style_data, $type ) {

			$file_system = self::get_instance()->get_filesystem();

			// Create a new file.
			$result = $file_system->put_contents( $assets_info[ $type ], $style_data, FS_CHMOD_FILE );

			return $result;
		}


        /**
		 * Check if IVE upload folder has write permissions or not.
		 *
		 * @since  1.14.9
		 * @return bool true or false.
		 */
		public static function has_read_write_permissions() {

			$upload_dir = self::get_upload_dir();

			$file_created = self::get_instance()->get_filesystem()->put_contents( $upload_dir['path'] . 'index.html', '' );

			if ( ! $file_created ) {

				return false;
			}

			return true;
		}

        /**
		 * Returns an array of paths for the upload directory
		 * of the current site.
		 *
		 * @since 1.14.0
		 * @return array
		 */
		public static function get_upload_dir() {

			$wp_info = wp_upload_dir( null, false );

			// SSL workaround.
			if ( self::is_ssl() ) {
				$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
			}

			$dir_name = basename( IVE_DIR );
			if ( 'ibtana-visual-editor' === $dir_name ) {
				$dir_name = 'ive-plugin';
			}

			// Build the paths.
			$dir_info = array(
				'path' => trailingslashit( trailingslashit( $wp_info['basedir'] ) . $dir_name ),
				'url'  => trailingslashit( trailingslashit( $wp_info['baseurl'] ) . $dir_name ),
			);


			// Create the upload dir if it doesn't exist.
			if ( ! file_exists( $dir_info['path'] ) ) {
				// Create the directory.
				$wp_filesystem = self::get_instance()->get_filesystem();
				$wp_filesystem->mkdir( $dir_info['path'] );
				// Add an index file for security.
				$wp_filesystem->put_contents( $dir_info['path'] . 'index.html', '', FS_CHMOD_FILE );
			}

			return apply_filters( 'ive_get_upload_dir', $dir_info );
		}

        /**
		 * Checks to see if the site has SSL enabled or not.
		 *
		 * @since 1.14.0
		 * @return bool
		 */
		public static function is_ssl() {
			if (
				is_ssl() ||
				( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) ||
				( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] )
			) {
				return true;
			}
			return false;
		}

        /**
		 * Get an instance of WP_Filesystem_Direct.
		 *
		 * @since 1.14.4
		 * @return object A WP_Filesystem_Direct instance.
		 */
		public function get_filesystem() {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		}

        /**
		 * Allow File Geranation flag.
		 *
		 * @since  1.14.0
		 */
		public static function allow_file_generation() {
			return get_option( '_ive_allow_file_generation', 'disabled' );
		}

		/**
		 * Returns an array of paths for the CSS assets
		 * of the current post.
		 *
		 * @param  var $data    Gets the CSS for the current Page.
		 * @param  var $type    Gets the CSS type.
		 * @param  var $timestamp Timestamp.
		 * @since 1.14.0
		 * @return array
		 */
		public static function get_asset_info( $data, $type, $timestamp ) {

 			$post_id     = get_the_ID();
 			$uploads_dir = self::get_upload_dir();
 			$css_suffix  = 'ive-style';
 			$js_suffix   = 'ive-script';
 			$info        = array();

 			if ( 'css' === $type ) {

 				$info['css']     = $uploads_dir['path'] . $css_suffix . '-' . $post_id . '-' . $timestamp . '.css';
 				$info['css_url'] = $uploads_dir['url'] . $css_suffix . '-' . $post_id . '-' . $timestamp . '.css';

 			} elseif ( 'js' === $type ) {

 				$info['js']     = $uploads_dir['path'] . $js_suffix . '-' . $post_id . '-' . $timestamp . '.js';
 				$info['js_url'] = $uploads_dir['url'] . $js_suffix . '-' . $post_id . '-' . $timestamp . '.js';

 			}

 			return $info;
 		}

		/**
		 * Creates a new file for Dynamic CSS.
		 *
		 * @param  array  $assets_info File path and other information.
		 * @param  string $style_data The data that needs to be copied into the created file.
		 * @param  string $timestamp Current timestamp.
		 * @param  string $type Type of file - CSS.
		 * @since 1.15.0
		 * @return boolean true/false
		 */
		public static function create_file( $assets_info, $style_data, $timestamp, $type ) {

			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return false;
			}

			$file_system = self::get_instance()->get_filesystem();

			// Create a new file.
			$result = $file_system->put_contents( $assets_info[ $type ], $style_data, FS_CHMOD_FILE );

			if ( $result ) {
				// Update meta with current timestamp.
				update_post_meta( $post_id, 'ive_style_timestamp-' . $type, $timestamp );
			}

			return $result;
		}


		/**
		 * Creates css files.
		 *
		 * @param  var $style_data    Gets the CSS for the current Page.
		 * @param  var $type    Gets the CSS type.
		 * @since  1.14.0
		 */
		public static function file_write( $style_data, $type ) {
			$post_id = get_the_ID();
			if ( ! $post_id ) {
				return false;
			}

			$post_timestamp = get_post_meta( $post_id, 'ive_style_timestamp-' . $type, true );
			$var            = ( 'css' === $type ) ? 'css' : 'js';
			$date           = new DateTime();
			$new_timestamp  = $date->getTimestamp();
			$file_system    = self::get_instance()->get_filesystem();

			// Get timestamp - Already saved OR new one.
			$post_timestamp  = ( '' === $post_timestamp || false === $post_timestamp ) ? '' : $post_timestamp;
			$assets_info     = self::get_asset_info( $style_data, $type, $post_timestamp );
			$new_assets_info = self::get_asset_info( $style_data, $type, $new_timestamp );

			$relative_src_path = $assets_info[ $var ];

			if ( '' === $style_data ) {
				/**
				 * This is when the generated CSS is blank.
				 * This means this page does not use IVE block.
				 * In this scenario we need to delete the existing file.
				 * This will ensure there are no extra files added for user.
				*/

				if ( file_exists( $relative_src_path ) ) {
					// Delete old file.
					wp_delete_file( $relative_src_path );
				}

				return true;
			}

			/**
			 * Timestamp present but file does not exists.
			 * This is the case where somehow the files are delete or not created in first place.
			 * Here we attempt to create them again.
			 */
			if ( ! $file_system->exists( $relative_src_path ) && '' !== $post_timestamp ) {
				$did_create = self::create_file( $assets_info, $style_data, $post_timestamp, $type );

				if ( $did_create ) {
					self::$css_file_handler = array_merge( self::$css_file_handler, $assets_info );
				}

				return $did_create;
			}

			/**
			 * Need to create new assets.
			 * No such assets present for this current page.
			 */
			if ( '' === $post_timestamp ) {
				// Create a new file.
				$did_create = self::create_file( $new_assets_info, $style_data, $new_timestamp, $type );

				if ( $did_create ) {
					self::$css_file_handler = array_merge( self::$css_file_handler, $new_assets_info );
				}

				return $did_create;

			}

			/**
			 * File already exists.
			 * Need to match the content.
			 * If new content is present we update the current assets.
			 */
			if ( file_exists( $relative_src_path ) ) {
				$old_data = $file_system->get_contents( $relative_src_path );

				if ( $old_data !== $style_data ) {

					// Delete old file.
					wp_delete_file( $relative_src_path );

					// Create a new file.
					$did_create = self::create_file( $new_assets_info, $style_data, $new_timestamp, $type );

					if ( $did_create ) {
						self::$css_file_handler = array_merge( self::$css_file_handler, $new_assets_info );
					}

					return $did_create;
				}
			}

			self::$css_file_handler = array_merge( self::$css_file_handler, $assets_info );

			return true;
		}

		/**
		 * Generates stylesheet and appends in head tag.
		 *
		 * @since 0.0.1
		 */
		public function generate_assets() {

			$this_post = array();

			if ( class_exists( 'WooCommerce' ) ) {

				if ( is_cart() ) {

					$id        = get_option( 'woocommerce_cart_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_account_page() ) {

					$id        = get_option( 'woocommerce_myaccount_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout() ) {

					$id        = get_option( 'woocommerce_checkout_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout_pay_page() ) {

					$id        = get_option( 'woocommerce_pay_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_shop() ) {

					$id        = get_option( 'woocommerce_shop_page_id' );
					$this_post = get_post( $id );
				}

				if ( is_object( $this_post ) ) {
					$this->get_generated_stylesheet( $this_post );
				}
			}

			if ( is_single() || is_page() || is_404() ) {

				global $post;
				$this_post = $post;

				if ( ! is_object( $this_post ) ) {
					return;
				}

				/**
				 * Filters the post to build stylesheet for.
				 *
				 * @param \WP_Post $this_post The global post.
				 */
				$this_post = apply_filters( 'ive_post_for_stylesheet', $this_post );

				$this->get_generated_stylesheet( $this_post );

			} elseif ( is_archive() || is_home() || is_search() ) {

				global $wp_query;
				$cached_wp_query = $wp_query;

				foreach ( $cached_wp_query as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$this->get_generated_stylesheet( $post );
				}
			}
		}

		/**
		 * Generates stylesheet in loop.
		 *
		 * @param object $this_post Current Post Object.
		 * @since 1.7.0
		 */
		public function get_generated_stylesheet( $this_post ) {

			if ( ! is_object( $this_post ) ) {
				return;
			}

			if ( ! isset( $this_post->ID ) ) {
				return;
			}

			if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

				$blocks            = $this->parse( $this_post->post_content );
				self::$page_blocks = $blocks;

				if ( ! is_array( $blocks ) || empty( $blocks ) ) {
					return;
				}

				$assets = $this->get_assets( $blocks );

				self::$stylesheet .= $assets['css'];
				self::$script     .= $assets['js'];
			}

			// Custom CSS and JS logic
			if ( self::$can_enqueue_custom_assets == true ) {
				$ive_save_general_settings = get_option( 'ive_general_settings' );
				if ( isset( $ive_save_general_settings['ive_custom_css'] ) && ( $ive_save_general_settings['ive_custom_css'] != '' ) ) {
					self::$custom_stylesheet = $ive_save_general_settings['ive_custom_css'];
				}
				if ( isset( $ive_save_general_settings['ive_custom_js'] ) && ( $ive_save_general_settings['ive_custom_js'] != '' ) ) {
					self::$custom_script = $ive_save_general_settings['ive_custom_js'];
				}
			}

		}

		/**
		 * Parse Guten Block.
		 *
		 * @param string $content the content string.
		 * @since 1.1.0
		 */
		public function parse( $content ) {

			global $wp_version;

			return ( version_compare( $wp_version, '5', '>=' ) ) ? parse_blocks( $content ) : gutenberg_parse_blocks( $content );
		}

		/**
		 * Enqueue Gutenberg block assets for both frontend + backend.
		 *
		 * @since 1.13.4
		 */
		public function block_assets() {

			$block_list_for_assets = self::$current_block_list;

			$blocks = IVE_Config::get_block_attributes();

			foreach ( $block_list_for_assets as $key => $curr_block_name ) {

				$js_assets = ( isset( $blocks[ $curr_block_name ]['js_assets'] ) ) ? $blocks[ $curr_block_name ]['js_assets'] : array();

				$css_assets = ( isset( $blocks[ $curr_block_name ]['css_assets'] ) ) ? $blocks[ $curr_block_name ]['css_assets'] : array();

				foreach ( $js_assets as $asset_handle => $val ) {
					// Scripts.
					if ( 'ive-faq-js' === $val ) {
						if ( self::$ive_faq_layout ) {
							wp_enqueue_script( 'ive-faq-js' );
						}
					} else {
						wp_enqueue_script( $val );
					}
				}

				foreach ( $css_assets as $asset_handle => $val ) {
					// Styles.
					wp_enqueue_style( $val );
				}
			}


			if ( 'enabled' === self::$file_generation ) {
				$file_handler = self::$css_file_handler;

				if ( isset( $file_handler['css_url'] ) ) {
					wp_enqueue_style( 'ive-style', $file_handler['css_url'], array(), IVE_VER, 'all' );
				} else {
					self::$fallback_css = true;
				}
				if ( isset( $file_handler['js_url'] ) ) {
					wp_enqueue_script( 'ive-script', $file_handler['js_url'], array(), IVE_VER, true );
				} else {
					self::$fallback_js = true;
				}
			}


			// custom css and js asset files enqueue
			if ( self::$can_enqueue_custom_assets == true ) {
				if ( 'enabled' === self::$file_generation ) {
					$file_handler = self::$css_file_handler;

					if ( isset( $file_handler['custom_css_url'] ) ) {
						wp_enqueue_style( 'ive-custom-style', $file_handler['custom_css_url'], array(), IVE_VER, 'all' );
					} else {
						self::$custom_fallback_css = true;
					}
					if ( isset( $file_handler['custom_js_url'] ) ) {
						wp_enqueue_script( 'ive-custom-script', $file_handler['custom_js_url'], array(), IVE_VER, true );
					} else {
						self::$custom_fallback_js = true;
					}
				}
			}


		}


		/**
		 * Print the Script in footer.
		 */
		public function print_script() {

			if ( 'enabled' != self::$file_generation || self::$fallback_js ) {
				if ( !is_null( self::$script ) || ( '' != self::$script ) ) {
					ob_start();
					?>
					<script type="text/javascript" id="ive-script-frontend">
					<?php echo self::$script; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
					</script>
					<?php
					ob_end_flush();
				}
			}

			if ( 'enabled' != self::$file_generation || self::$custom_fallback_js ) {
				// print custom script
				if ( self::$can_enqueue_custom_assets == true ) {
					if ( !is_null( self::$custom_script ) || ( '' != self::$custom_script ) ) {
						ob_start();
						?>
						<script type="text/javascript" id="ive-custom-script-frontend"><?php echo self::$custom_script; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></script>
						<?php
						ob_end_flush();
					}
				}
			}



		}


		public function get_assets( $blocks ) {

			$desktop 		= '';
			$desktop_media  = '';
			$tablet  		= '';
			$mobile  		= '';

			$desk_styling_css = '';
			$tab_styling_css = '';
			$mob_styling_css = '';

			$js = '';

			foreach ( $blocks as $i => $block ) {

				if ( is_array( $block ) ) {

					if ( '' === $block['blockName'] ) {
						continue;
					}

					if ( 'core/block' === $block['blockName'] ) {
						$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

						if ( $id ) {
							$content = get_post_field( 'post_content', $id );

							$reusable_blocks = $this->parse( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];

						}
					} else {

						$block_assets = $this->get_block_css_and_js( $block );


						// Get CSS for the Block.
						$css = $block_assets['css'];

						if ( isset( $css['desktop'] ) ) {
							$desktop 		.= $css['desktop'];
							$desktop_media 	.= $css['desktop_media'];
							$tablet  		.= $css['tablet'];
							$mobile  		.= $css['mobile'];
						}

						if ( isset( $block_assets['js'] ) ) {
							$js .= $block_assets['js'];
						}

					}
				}
			}

			if ( ! empty( $tablet ) ) {
				$tab_styling_css .= '@media only screen and (max-width: ' . IVE_TABLET_BREAKPOINT . 'px) {';
				$tab_styling_css .= $tablet;
				$tab_styling_css .= '}';
			}

			if ( ! empty( $mobile ) ) {
				$mob_styling_css .= '@media only screen and (max-width: ' . IVE_MOBILE_BREAKPOINT . 'px) {';
				$mob_styling_css .= $mobile;
				$mob_styling_css .= '}';
			}

			if ( ! empty( $desktop_media ) ) {
				$desk_styling_css .= '@media only screen and (min-width: ' . IVE_DESKTOP_STARTPOINT . 'px) {';
				$desk_styling_css .= $desktop_media;
				$desk_styling_css .= '}';
			}

			return array(
				'css' => $desktop . $desk_styling_css . $tab_styling_css . $mob_styling_css,
				'js'  => $js,
			);
		}

		/**
		 * Print the Stylesheet in header.
		 */
		public function print_stylesheet() {

			if ( 'enabled' != self::$file_generation || self::$fallback_css ) {
				if ( !is_null( self::$stylesheet ) || ( '' != self::$stylesheet ) ) {
					ob_start();
					?>
					<style id="ive-style-frontend"><?php echo self::$stylesheet; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></style>
					<?php
					ob_end_flush();
				}
			}


			if ( 'enabled' != self::$file_generation || self::$custom_fallback_css ) {
				// Print Custom Stylesheet
				if ( self::$can_enqueue_custom_assets == true ) {
					if ( !is_null( self::$custom_stylesheet ) || ( '' != self::$custom_stylesheet ) ) {
						ob_start();
						?>
						<style id="ive-custom-style-frontend"><?php echo self::$custom_stylesheet; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></style>
						<?php
						ob_end_flush();
					}
				}
			}



		}

		/**
		 * Generates CSS recurrsively.
		 *
		 * @param object $block The block object.
		 * @since 0.0.1
		 */
		public function get_block_css_and_js( $block ) {

			$block = (array) $block;

			$name     = $block['blockName'];
			$css      = array();
			$js       = '';
			$block_id = '';

			if ( ! isset( $name ) ) {
				return array(
					'css' => array()
				);
			}

			if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
				$blockattr = $block['attrs'];
				if ( isset( $blockattr['uniqueID'] ) ) {
					$block_id = $blockattr['uniqueID'];
				}
			}

			self::$current_block_list[] = $name;
			if ( class_exists( 'WooCommerce' ) ) {
				if ( is_product() && ( strpos( $name, 'iepa/' ) !== false || strpos( $name, 'ive/' ) !== false ) ) {
					$style_woocommerce = 'ibtana-woocommerce-templates-frontend-style';
					if(
						( ! wp_style_is( $style_woocommerce, 'queue' ) ) &&
						( ! wp_style_is( $style_woocommerce, 'done' ) )
					) {
						wp_enqueue_style(
							'ibtana-woocommerce-templates-frontend-style',
							'https://vwthemesdemo.com/ibtana_json/woocommerce_templates/css/iepa-frontend.css'
						);
					}


					$is_iepa_active = false;
					$ibtana_ecommerce_product_addons_license_key	=	get_option( 'ibtana_ecommerce_product_addons_license_key' );
					if ( $ibtana_ecommerce_product_addons_license_key ) {
		        if ( isset( $ibtana_ecommerce_product_addons_license_key['license_key'] ) ) {
		          if ( ( $ibtana_ecommerce_product_addons_license_key['license_key'] != '' ) ) {
		            $is_iepa_active		=	true;
		          }
		        }
		      }

					// Premium CSS for iepa frontend
					$style_woocommerce_premium_frontend = 'ibtana-woocommerce-premium-templates-frontend-style';
					if(
						( ! wp_style_is( $style_woocommerce_premium_frontend, 'queue' ) ) &&
						( ! wp_style_is( $style_woocommerce_premium_frontend, 'done' ) ) && $is_iepa_active
					) {
						wp_enqueue_style(
							$style_woocommerce_premium_frontend,
							'https://vwthemesdemo.com/ibtana_json/woocommerce_templates/css/premium/iepa-frontend.css'
						);
					}


				}
			}

			if ( strpos( $name, 'ive/' ) !== false ) {

				$style_premium = 'ibtana-premium-templates-frontend-style';
				if( ( ! wp_style_is( $style_premium, 'queue' ) ) && ( ! wp_style_is( $style_premium, 'done' ) ) ) {
					wp_enqueue_style(
						'ibtana-premium-templates-frontend-style',
						'https://vwthemesdemo.com/ibtana_json/premium_theme/css/ive-frontend-style.css'
					);
				}

				$style_free = 'ibtana-free-templates-frontend-style';
				if( ( ! wp_style_is( $style_free, 'queue' ) ) && ( ! wp_style_is( $style_free, 'done' ) ) ) {
					wp_enqueue_style(
						'ibtana-free-templates-frontend-style',
						'https://vwthemesdemo.com/ibtana_json/free_theme/css/ive-frontend-style.css'
					);
				}

				self::$ive_flag = true;
			}

			switch ( $name ) {
				case 'ive/ibtana-visual-editorbtn':
					$css += IVE_Block_Helper::get_button_css( $blockattr, $block_id );
					IVE_Block_JS::get_button_gfonts( $blockattr );
					break;

				case 'ive/page-title':
					$css += IVE_Block_Helper::get_page_title_css( $blockattr, $block_id );
					break;

				case 'ive/google-map':
					$css += IVE_Block_Helper::get_google_map_css( $blockattr, $block_id );
					break;

				case 'ive/gallery':
					$css += IVE_Block_Helper::get_image_gallery_css( $blockattr, $block_id );
					break;

				case 'ive/icon':
					$css += IVE_Block_Helper::get_icon_css( $blockattr, $block_id );
					$js  .= IVE_Block_JS::get_social_share_js( $blockattr, $block_id );
					break;

				case 'ive/separator':
					$css += IVE_Block_Helper::get_separator_css( $blockattr, $block_id );
					break;

				case 'ive/progress-bar':
					$css += IVE_Block_Helper::get_progress_bar_css( $blockattr, $block_id );
					IVE_Block_JS::get_progress_bar_gfonts( $blockattr );
					break;

				case 'ive/ibtana-visual-editorheading':
					$css += IVE_Block_Helper::get_advanced_text_css( $blockattr, $block_id );
					IVE_Block_JS::get_advanced_text_gfonts( $blockattr );
					break;

				case 'ive/carousel':
					$css += IVE_Block_Helper::get_multiblock_slider_css( $blockattr, $block_id );
					break;

				case 'ive/carouselimage':
					$css += IVE_Block_Helper::get_multiblock_slider_image_css( $blockattr, $block_id );
					break;

				case 'ive/tabs':
					$css += IVE_Block_Helper::get_tabs_css( $blockattr, $block_id );
					IVE_Block_JS::get_tabs_gfonts( $blockattr );
					break;

				case 'ive/accordion':
					$css += IVE_Block_Helper::get_accordion_css( $blockattr, $block_id );
					IVE_Block_JS::get_accordion_title_gfonts( $blockattr );
					break;

				case 'ive/pane':
					$css += IVE_Block_Helper::get_accordion_pane_css( $blockattr, $block_id );
					break;

				case 'ive/form-field-checkbox':
					$css += IVE_Block_Helper::get_form_checkbox_css( $blockattr, $block_id );
					break;

				case 'ive/form-field-date':
					$css += IVE_Block_Helper::get_form_date_css( $blockattr, $block_id );
					break;

				case 'ive/form-field-email':
					$css += IVE_Block_Helper::get_form_email_css( $blockattr, $block_id );
					break;

				case 'ive/form-field-name':
					$css += IVE_Block_Helper::get_form_name_css( $blockattr, $block_id );
					break;

				case 'ive/form-field-number':
					$css += IVE_Block_Helper::get_form_number_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-phone':
					$css += IVE_Block_Helper::get_form_phone_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-radio':
					$css += IVE_Block_Helper::get_form_radio_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-select':
					$css += IVE_Block_Helper::get_form_select_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-text':
					$css += IVE_Block_Helper::get_form_text_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-textarea':
					$css += IVE_Block_Helper::get_form_textarea_css( $blockattr, $block_id );
					break;
				case 'ive/form-field-url':
					$css += IVE_Block_Helper::get_form_url_css( $blockattr, $block_id );
					break;
				case 'ive/button-single':
					$css += IVE_Block_Helper::get_form_button_css( $blockattr, $block_id );
					break;

				default:
					// Nothing to do here.
					break;
			}

			if ( isset( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as $j => $inner_block ) {
					if ( 'core/block' === $inner_block['blockName'] ) {
						$id = ( isset( $inner_block['attrs']['ref'] ) ) ? $inner_block['attrs']['ref'] : 0;

						if ( $id ) {
							$content = get_post_field( 'post_content', $id );

							$reusable_blocks = $this->parse( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];
						}
					} else {
						// Get CSS for the Block.
						$inner_assets    = $this->get_block_css_and_js( $inner_block );
						$inner_block_css = $inner_assets['css'];

						$css_desktop 		= ( isset( $css['desktop'] ) ? $css['desktop'] : '' );
						$css_desktop_media 	= ( isset( $css['desktop_media'] ) ? $css['desktop_media'] : '' );
						$css_tablet  		= ( isset( $css['tablet'] ) ? $css['tablet'] : '' );
						$css_mobile  		= ( isset( $css['mobile'] ) ? $css['mobile'] : '' );

						if ( isset( $inner_block_css['desktop'] ) ) {
							$css['desktop'] 		= $css_desktop . $inner_block_css['desktop'];
							$css['desktop_media'] 	= $css_desktop_media . $inner_block_css['desktop_media'];
							$css['tablet']  		= $css_tablet . $inner_block_css['tablet'];
							$css['mobile']  		= $css_mobile . $inner_block_css['mobile'];
						}

						$js .= $inner_assets['js'];
					}
				}
			}

			self::$current_block_list = array_unique( self::$current_block_list );

			return array(
				'css' => $css,
				'js'  => $js,
			);

		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $combined_selectors The combined selector array.
		 * @param string $id The selector ID.
		 * @since 1.15.0
		 */
		public static function generate_all_css( $combined_selectors, $id ) {

			return array(
				'desktop' 			=>	self::generate_css( $combined_selectors['desktop'], $id ),
				'desktop_media'	=>	self::generate_css( $combined_selectors['desktop_media'], $id ),
				'tablet'  			=>	self::generate_css( $combined_selectors['tablet'], $id ),
				'mobile'  			=>	self::generate_css( $combined_selectors['mobile'], $id ),
			);
		}

		/**
		 * Get CSS value
		 *
		 * Syntax:
		 *
		 *  get_css_value( VALUE, UNIT );
		 *
		 * E.g.
		 *
		 *  get_css_value( VALUE, 'em' );
		 *
		 * @param string $value  CSS value.
		 * @param string $unit  CSS unit.
		 * @since 1.13.4
		 */
		public static function get_css_value( $value = '', $unit = '' ) {

			if ( '' == $value ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				return $value;
			}

			$css_val = '';

			if ( ! empty( $value ) ) {
				$css_val = esc_attr( $value ) . $unit;
			}

			return $css_val;
		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $selectors The block selectors.
		 * @param string $id The selector ID.
		 * @since 0.0.1
		 */
		public static function generate_css( $selectors, $id ) {
			$styling_css = '';

			if ( empty( $selectors ) ) {
				return '';
			}

			foreach ( $selectors as $key => $value ) {

				$css = '';

				foreach ( $value as $j => $val ) {

					if ( 'font-family' === $j && 'Default' === $val ) {
						continue;
					}

					if ( ! empty( $val ) || 0 === $val ) {
						if ( 'font-family' === $j ) {
							$css .= $j . ': "' . $val . '";';
						} else {
							$css .= $j . ': ' . $val . ';';
						}
					}
				}

				if ( ! empty( $css ) ) {
					$styling_css     .= $id;
					$styling_css     .= $key . '{';
					$styling_css 	 .= $css . '}';
				}
			}

			return $styling_css;
		}

		/**
		 * Adds Google fonts all blocks.
		 *
		 * @param array $load_google_font the blocks attr.
		 * @param array $font_family the blocks attr.
		 * @param array $font_weight the blocks attr.
		 * @param array $font_subset the blocks attr.
		 */
		public static function blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset ) {

			if ( true === $load_google_font ) {
				if ( ! array_key_exists( $font_family, self::$gfonts ) ) {
					$add_font                     = array(
						'fontfamily'   => $font_family,
						'fontvariants' => ( isset( $font_weight ) && ! empty( $font_weight ) ? array( $font_weight ) : array() ),
						'fontsubsets'  => ( isset( $font_subset ) && ! empty( $font_subset ) ? array( $font_subset ) : array() ),
					);
					self::$gfonts[ $font_family ] = $add_font;
				} else {
					if ( isset( $font_weight ) && ! empty( $font_weight ) && ! in_array( $font_weight, self::$gfonts[ $font_family ]['fontvariants'], true ) ) {
						array_push( self::$gfonts[ $font_family ]['fontvariants'], $font_weight );
					}
					if ( isset( $font_subset ) && ! empty( $font_subset ) && ! in_array( $font_subset, self::$gfonts[ $font_family ]['fontsubsets'], true ) ) {
						array_push( self::$gfonts[ $font_family ]['fontsubsets'], $font_subset );
					}
				}
			}
		}

		/**
		 * Load the front end Google Fonts.
		 */
		public function frontend_gfonts() {

			if ( empty( self::$gfonts ) ) {
				return;
			}
			$show_google_fonts = apply_filters( 'ive_blocks_show_google_fonts', true );
			if ( ! $show_google_fonts ) {
				return;
			}
			$link    = '';
			$subsets = array();
			foreach ( self::$gfonts as $key => $gfont_values ) {
				if ( ! empty( $link ) ) {
					$link .= '%7C'; // Append a new font to the string.
				}
				$link .= $gfont_values['fontfamily'];
				if ( ! empty( $gfont_values['fontvariants'] ) ) {
					$link .= ':';
					$link .= implode( ',', $gfont_values['fontvariants'] );
				}
				if ( ! empty( $gfont_values['fontsubsets'] ) ) {
					foreach ( $gfont_values['fontsubsets'] as $subset ) {
						if ( ! in_array( $subset, $subsets, true ) ) {
							array_push( $subsets, $subset );
						}
					}
				}
			}
			if ( ! empty( $subsets ) ) {
				$link .= '&amp;subset=' . implode( ',', $subsets );
			}
			if ( isset( $link ) && ! empty( $link ) ) {
				echo '<link href="//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ) . '" rel="stylesheet">'; //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
			}
		}
	}

	/**
	 *  Prepare if class 'IVE_Helper' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	IVE_Helper::get_instance();
}
