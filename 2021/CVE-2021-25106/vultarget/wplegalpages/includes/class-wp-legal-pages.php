<?php
/**
 * The file that defines the core WPLegalPages class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wplegalpages.com/
 * @since      1.5.2
 *
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 */

/**
 * The core WPLegalPages class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this WPLegalPages as well as the current
 * version of the WPLegalPages.
 *
 * @since      1.5.2
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/includes
 * @author     WPEka <support@wplegalpages.com>
 */
if ( ! class_exists( 'WP_Legal_Pages' ) ) {
	/**
	 * The core WPLegalPages class.
	 *
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 *
	 * Also maintains the unique identifier of this WPLegalPages as well as the current
	 * version of the WPLegalPages.
	 *
	 * @since      1.5.2
	 * @package    WP_Legal_Pages
	 * @subpackage WP_Legal_Pages/includes
	 * @author     WPEka <support@wplegalpages.com>
	 */
	class WP_Legal_Pages {
		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the WPLegalPages.
		 *
		 * @since    1.5.2
		 * @access   protected
		 * @var      WP_Legal_Pages_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */

		protected $loader;

		/**
		 * The unique identifier of WPLegalPages.
		 *
		 * @since    1.5.2
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		public $plugin_name;

		/**
		 * The current version of the WPLegalPages.
		 *
		 * @since    1.5.2
		 * @access   protected
		 * @var      string    $version    The current version of the WPLegalPages.
		 */

		public $version;

		/**
		 * Define the core functionality of the WPLegalPages.
		 *
		 * Set the WPLegalPages name and the WPLegalPages version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.5.2
		 */
		public function __construct() {

			global $table_prefix;
			$this->plugin_name = 'wp-legal-pages';
			$this->version     = '2.7.0';
			$this->tablename   = $table_prefix . 'legal_pages';
			$this->popuptable  = $table_prefix . 'lp_popups';
			$this->plugin_url  = plugin_dir_path( dirname( __FILE__ ) );
			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}

		/**
		 * What type of request is this?
		 *
		 * @since 2.3.9
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
			}
		}

		/**
		 * Load the required dependencies for WPLegalPages.
		 *
		 * Include the following files that make up the WPLegalPages:
		 *
		 * - WP_Legal_Pages_Loader. Orchestrates the hooks of the plugin.
		 * - WP_Legal_Pages_I18n. Defines internationalization functionality.
		 * - WP_Legal_Pages_Admin. Defines all hooks for the admin area.
		 * - WP_Legal_Pages_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.5.2
		 * @access   private
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core WP_Legal_Pages.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-legal-pages-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the WP_Legal_Pages.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-legal-pages-i18n.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-legal-pages-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-legal-pages-public.php';

			/**
			 * The class responsible for defining widget specific functionality
			 * of the WP_Legal_Pages.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/class-wp-widget-legal-pages.php';

			$this->loader = new WP_Legal_Pages_Loader();

		}

		/**
		 * Define the locale for this WP_Legal_Pages for internationalization.
		 *
		 * Uses the WP_Legal_Pages_I18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.5.2
		 * @access   private
		 */
		private function set_locale() {

			$plugin_i18n = new WP_Legal_Pages_I18n();
			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the WP_Legal_Pages.
		 *
		 * @since    1.5.2
		 * @access   private
		 */
		private function define_admin_hooks() {
			$plugin_admin = new WP_Legal_Pages_Admin( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'admin_footer', $plugin_admin, 'wplegalpages_mascot_enqueue' );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'wplegalpages_hidden_meta_boxes' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'wplegal_admin_init' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( 'wp_ajax_lp_accept_terms', $plugin_admin, 'wplegal_accept_terms' );
			$this->loader->add_action( 'wp_ajax_nopriv_lp_accept_terms', $plugin_admin, 'wplegal_accept_terms' );
			$this->loader->add_filter( 'plugin_action_links_' . WPL_LITE_PLUGIN_BASENAME, $plugin_admin, 'wplegal_plugin_action_links' );
			$this->loader->add_action( 'wp_ajax_get_accept_terms', $plugin_admin, 'wplegal_get_accept_terms' );
			$this->loader->add_action( 'wp_ajax_save_accept_terms', $plugin_admin, 'wplegal_save_accept_terms' );
			$this->loader->add_filter( 'nav_menu_meta_box_object', $plugin_admin, 'wplegalpages_add_menu_meta_box', 10, 1 );
			$this->loader->add_action( 'wp_ajax_wplegalpages_disable_settings_warning', $plugin_admin, 'wplegalpages_disable_settings_warning', 10, 1 );
			$this->loader->add_action( 'wp_ajax_lp_save_admin_settings', $plugin_admin, 'wplegalpages_ajax_save_settings', 10, 1 );
			$this->loader->add_filter( 'style_loader_src', $plugin_admin, 'wplegalpages_dequeue_styles' );
			$this->loader->add_filter( 'print_styles_array', $plugin_admin, 'wplegalpages_remove_forms_style' );
			$this->loader->add_action( 'wp_ajax_lp_save_footer_form', $plugin_admin, 'wplegalpages_save_footer_form' );
			$this->loader->add_filter( 'wp_ajax_save_banner_form', $plugin_admin, 'wplegalpages_save_banner_form' );
			$this->loader->add_action( 'wp_ajax_save_cookie_bar_form', $plugin_admin, 'wplegalpages_save_cookie_bar_form' );
			$this->loader->add_action( 'post_updated', $plugin_admin, 'wplegalpages_post_updated', 10, 1 );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the WP_Legal_Pages.
		 *
		 * @since    1.5.2
		 * @access   private
		 */
		private function define_public_hooks() {
			$plugin_public     = new WP_Legal_Pages_Public( $this->get_plugin_name(), $this->get_version() );
			$lp_general        = get_option( 'lp_general' );
			$lp_banner_options = get_option( 'lp_banner_options' );
			if ( isset( $lp_general['generate'] ) && '1' === $lp_general['generate'] ) {
				$this->loader->add_filter( 'the_content', $plugin_public, 'wplegal_post_generate' );
			}
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_script' ) );
			add_action( 'wp_footer', array( $this, 'wp_legalpages_show_eu_cookie_message' ) );
			$this->loader->add_action( 'wp_footer', $plugin_public, 'wp_legalpages_show_footer_message' );
			if ( isset( $lp_banner_options['bar_position'] ) && 'bottom' === $lp_banner_options['bar_position'] ) {
				$this->loader->add_action( 'wp_footer', $plugin_public, 'wplegal_announce_bar_content' );
			}
			if ( isset( $lp_banner_options['bar_position'] ) && 'top' === $lp_banner_options['bar_position'] ) {
				$this->loader->add_action( 'wp_head', $plugin_public, 'wplegal_announce_bar_content' );
			}

		}

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.5.2
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the WP_Legal_Pages used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.5.2
		 * @return    string    The name of the WP_Legal_Pages.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the WP_Legal_Pages.
		 *
		 * @since     1.5.2
		 * @return    WP_Legal_Pages_Loader    Orchestrates the hooks of the WP_Legal_Pages.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the WP_Legal_Pages.
		 *
		 * @since     1.5.2
		 * @return    string    The version number of the WP_Legal_Pages.
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Enqueue jQuery Cookie js library.
		 */
		public function enqueue_frontend_script() {
			wp_register_script( $this->plugin_name . '-jquery-cookie', WPL_LITE_PLUGIN_URL . 'admin/js/jquery.cookie.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name . '-jquery-cookie' );
			wp_register_script( $this->plugin_name . 'banner-cookie', WPL_LITE_PLUGIN_URL . 'public/js/wplegalpages-banner-cookie' . WPLPP_SUFFIX . '.js', array(), $this->version, true );
		}

		/**
		 * Display EU cookie message on frontend.
		 */
		public function wp_legalpages_show_eu_cookie_message() {

			$lp_eu_get_visibility = get_option( 'lp_eu_cookie_enable' );

			if ( 'ON' === $lp_eu_get_visibility ) {
				$lp_eu_theme_css         = get_option( 'lp_eu_theme_css' );
				$lp_eu_title             = get_option( 'lp_eu_cookie_title' );
				$lp_eu_message           = get_option( 'lp_eu_cookie_message' );
				$lp_eu_box_color         = get_option( 'lp_eu_box_color' );
				$lp_eu_button_color      = get_option( 'lp_eu_button_color' );
				$lp_eu_button_text_color = get_option( 'lp_eu_button_text_color' );
				$lp_eu_text_color        = get_option( 'lp_eu_text_color' );
				$lp_eu_button_text       = get_option( 'lp_eu_button_text' );
				$lp_eu_link_text         = get_option( 'lp_eu_link_text' );
				$lp_eu_link_url          = get_option( 'lp_eu_link_url' );
				$lp_eu_text_size         = get_option( 'lp_eu_text_size' );
				$lp_eu_link_color        = get_option( 'lp_eu_link_color' );
				$lp_eu_head_text_size    = $lp_eu_text_size + 4;

				if ( ! $lp_eu_button_text || $lp_eu_button_text === '' ) {
					$lp_eu_button_text = 'I agree';
					update_option( 'lp_eu_button_text', $lp_eu_button_text );
				}
				
				$lp_eu_html  = '<div id="lp_eu_container">';
				$lp_eu_html .= '<table id="lp_eu_table" class="lp_eu_table" style="border:none;"><tr><td width="90%">';

				if ( ! empty( $lp_eu_title ) ) {
					$lp_eu_html .= '<b id="lp_eu_title">' . $lp_eu_title . '</b>';
				}

				$lp_eu_html .= '<p id="lp_eu_body">' . stripslashes( html_entity_decode( $lp_eu_message ) );

				$lp_eu_html .= ' <a id="lp_eu_link" target="_blank" href="' . $lp_eu_link_url . '">' . $lp_eu_link_text . '</a></p></td>';
				$lp_eu_html .= '<td width="10%" ><div id="lp_eu_right_container"><p id="lp_eu_close_button"></p><p style="min-height:50%"></p><p id="lp_eu_btnContainer"><button type="button" id="lp_eu_btn_agree">' . $lp_eu_button_text . '</button></p></td></div></tr></table>';
				$lp_eu_html .= '</div>';
				echo '<style>
					.lp_eu_table td{
						border:none;
					}
					#lp_eu_table{
						border-color:rgba(255,255,255,0.9);
						margin-bottom : 0em;
								margin-top : 0em;
								width: 100%;
							}
							#lp_eu_table td	{
								vertical-align: middle;
		            		}
							#lp_eu_table th, td {
							    padding: inherit;
		 					}
					#lp_eu_container{
						display: none;
						margin: 1%;
						padding: 5px 10px;
						width: 98%;
						z-index: 9999;
						position: fixed;
						bottom: 0px;
						border-radius: 10px;
						box-shadow: 2px 2px 5px #888 inset;
						box-sizing : border-box;
						opacity: 0.8;
					}
					#lp_eu_close_button:before{
						content: "\2716";
						font-size: ' . $lp_eu_text_size . 'px;
					}
					#lp_eu_close_button{
						padding-left: 85%;
						position: absolute;
						top: 0px;
						right: 15px;
					}
					#lp_eu_right_container{
						display:flex;
						flex-direction: column;
						align-items : end;	
					}
					#lp_eu_btnContainer{
						text-align: center;
						padding-right: 15%;
						align-items: center;
						display: flex;
						justify-content: center;
					}
					#lp_eu_title{
						margin: inherit;
					}
					#lp_eu_body,#lp_eu_btnContainer{
						margin: 0px;
					}
					a#lp_eu_link{
            		  border-bottom: 1px dotted;
					  text-decoration: none;
            		}
					@media only screen and (max-width: 360px) {
						#lp_eu_table td {
						    border-width: 0 1px 1px 0;
						    box-sizing: border-box;
						    display: block;
						    width: 100%;
						}
 					}
				</style>';

				?>
							<script type="text/javascript">
								jQuery(document).ready(function(){
									if (jQuery.cookie('lp_eu_agree') == null) {
										jQuery.cookie('lp_eu_agree', 'NO', { expires: 7, path: '/' });
										lp_eu_show_cookie_bar();
									}
									else if (jQuery.cookie('lp_eu_agree') == 'NO') {
										lp_eu_show_cookie_bar();
									}
									jQuery('#lp_eu_btn_agree').click(function (){
									jQuery.cookie('lp_eu_agree', 'YES', { expires: 7, path: '/' });
									jQuery('#lp_eu_container').hide(500);
								});
								jQuery('#lp_eu_close_button').click(function(){
										jQuery('#lp_eu_container').css('display','none');
										jQuery.cookie('lp_eu_agree', 'close', { expires: 7, path: '/' });
								})
							});
							function lp_eu_show_cookie_bar(){
								jQuery('body').prepend('<?php echo $lp_eu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>');
									<?php if ( '0' === $lp_eu_theme_css ) { ?>
												// container deisgn
										jQuery('#lp_eu_container').css( { 'background-color' : '<?php echo esc_attr( $lp_eu_box_color ); ?>',
																					'border-color'	  :	'<?php echo esc_attr( $lp_eu_text_color ); ?>',
																					'color'            : '<?php echo esc_attr( $lp_eu_text_color ); ?>' });

										//Text font
										jQuery('p#lp_eu_body').css('font-size', '<?php echo esc_attr( $lp_eu_text_size ) . 'px'; ?>');

										// Title design.
										jQuery('#lp_eu_title').css('font-size','<?php echo esc_attr( $lp_eu_head_text_size ) . 'px'; ?>');

										// agree button design
										jQuery('#lp_eu_btn_agree').css( { 'background-color' : '<?php echo esc_attr( $lp_eu_button_color ); ?>',
																			'color'            : '<?php echo esc_attr( $lp_eu_button_text_color ); ?>',
																			'border-style'	  : 'none',
																			'border'			  : '1px solid #bbb',
																			'border-radius'	  : '5px',
																			'box-shadow'		  : 'inset 0 0 1px 1px #f6f6f6',
																			'line-height'	  : 1,
																			'padding'		  : '7px',
																			'padding-bottom'  : '9px',
																			'text-align'		  : 'center',
																			'text-shadow'      : '0 1px 0 #fff',
																			'cursor'			  : 'pointer',
																			'font-size'		    : '<?php echo esc_attr( $lp_eu_text_size ) . 'px'; ?>'
																		});

										// link color.
										jQuery('#lp_eu_link').css({ 'color' : '<?php echo esc_attr( $lp_eu_link_color ); ?>' });

										<?php
									} else {
										// container design.
										?>
										jQuery('#lp_eu_container').css({ 'background-color' : '<?php echo 'inherit'; ?>', 'color' : '<?php echo 'inherit'; ?>' });

										<?php
									}
									?>
									jQuery('#lp_eu_container').show(500);
								}
							</script>
								<?php
			}
		}

	}
}
