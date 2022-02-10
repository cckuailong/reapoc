<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Network class
 * @since 0.1
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Network_Module' ) ) {
	class myCRED_Network_Module {

		public $core;
		public $plug;

		/**
		 * Construct
		 */
		function __construct() {
			global $mycred_network;
			$this->core = mycred();
		}

		/**
		 * Load
		 * @since 0.1
		 * @version 1.0
		 */
		public function load() {
			add_action( 'admin_init',         array( $this, 'module_admin_init' ) );
			add_action( 'admin_head',         array( $this, 'admin_menu_styling' ) );
			add_action( 'network_admin_menu', array( $this, 'add_menu' ) );

			add_filter( 'site_option_active_sitewide_plugins', array( $this, 'network_check' ) );
		}

		/**
		 * Init
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_admin_init() {
			register_setting( 'mycred_network', 'mycred_network', array( $this, 'save_network_prefs' ) );
		}

		/**
		 * Add Network Menu Items
		 * @since 0.1
		 * @version 1.2
		 */
		public function add_menu() {
			$pages[] = add_menu_page(
				__( 'myCRED', 'mycred' ),
				__( 'myCRED', 'mycred' ),
				'manage_network_options',
				'myCRED_Network',
				'',
				'dashicons-star-filled'
			);
			$pages[] = add_submenu_page(
				'myCRED_Network',
				__( 'Network Settings', 'mycred' ),
				__( 'Network Settings', 'mycred' ),
				'manage_network_options',
				'myCRED_Network',
				array( $this, 'admin_page_settings' )
			);

			foreach ( $pages as $page )
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_menu_styling' ) );
		}

		/**
		 * Network Check
		 * Blocks mycred from being used if the plugin is network wide
		 * enabled.
		 * @since 1.3
		 * @version 1.0
		 */
		public function network_check( $value ) {
			global $current_blog;
			
			$network = mycred_get_settings_network();
			if ( empty( $network['block'] ) ) return $value;
			
			$list = explode( ',', $network['block'] );
			if ( in_array( $current_blog->blog_id, $list ) ) {
				unset( $value['mycred/mycred.php'] );
			}
			
			return $value;
		}

		/**
		 * Add Admin Menu Styling
		 * @since 0.1
		 * @version 1.0
		 */
		public function admin_menu_styling() {
			global $wp_version;

			wp_enqueue_style( 'mycred-admin' );
			$image = plugins_url( 'assets/images/logo-menu.png', myCRED_THIS ); ?>

<style type="text/css">
h4:before { float:right; padding-right: 12px; font-size: 14px; font-weight: normal; color: silver; }
h4.ui-accordion-header.ui-state-active:before { content: "<?php _e( 'click to close', 'mycred' ); ?>"; }
h4.ui-accordion-header:before { content: "<?php _e( 'click to open', 'mycred' ); ?>"; }
</style>
<?php
		}

		/**
		 * Load Admin Page Styling
		 * @since 0.1
		 * @version 1.0
		 */
		public function admin_print_styles() {
			if ( ! wp_style_is( 'mycred-admin', 'registered' ) ) {
				wp_register_style(
					'mycred-admin',
					plugins_url( 'assets/css/admin.css', myCRED_THIS ),
					false,
					myCRED_VERSION . '.1',
					'all'
				);
			}
			wp_enqueue_style( 'mycred-admin' );

			if ( ! wp_script_is( 'mycred-admin', 'registered' ) ) {
				wp_register_script(
					'mycred-admin',
					plugins_url( 'assets/js/accordion.js', myCRED_THIS ),
					array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ),
					myCRED_VERSION . '.1'
				);
				wp_localize_script( 'mycred-admin', 'myCRED', apply_filters( 'mycred_localize_admin', array( 'active' => '-1' ) ) );
			}
			wp_enqueue_script( 'mycred-admin' );
		}

		/**
		 * Network Settings Page
		 * @since 0.1
		 * @version 1.1
		 */
		public function admin_page_settings() {
			// Security
			if ( ! current_user_can( 'manage_network_options' ) ) wp_die( __( 'Access Denied', 'mycred' ) );

			global $mycred_network;

			$prefs = mycred_get_settings_network();
			$name = mycred_label(); ?>

	<div class="wrap" id="myCRED-wrap">
		<div id="icon-myCRED" class="icon32"><br /></div>
		<h2> <?php echo sprintf( __( '%s Network', 'mycred' ), $name ); ?></h2>
		<?php
			
			// Inform user that myCRED has not yet been setup
			$setup = get_blog_option( 1, 'mycred_setup_completed', false );
			if ( $setup === false )
				echo '<div class="error"><p>' . sprintf( __( 'Note! %s has not yet been setup.', 'mycred' ), $name ) . '</p></div>';

			// Settings Updated
			if ( isset( $_GET['settings-updated'] ) )
				echo '<div class="updated"><p>' . __( 'Network Settings Updated', 'mycred' ) . '</p></div>'; ?>

<p><?php echo sprintf( __( 'Configure network settings for %s.', 'mycred' ), $name ); ?></p>
<form method="post" action="<?php echo admin_url( 'options.php' ); ?>" class="">
	<?php settings_fields( 'mycred_network' ); ?>

	<div class="list-items expandable-li" id="accordion">
		<h4><div class="icon icon-inactive core"></div><?php _e( 'Settings', 'mycred' ); ?></h4>
		<div class="body" style="display:block;">
			<label class="subheader"><?php _e( 'Master Template', 'mycred' ); ?></label>
			<ol id="myCRED-network-settings-enabling">
				<li>
					<input type="radio" name="mycred_network[master]" id="myCRED-network-overwrite-enabled" <?php checked( $prefs['master'], 1 ); ?> value="1" /> 
					<label for="myCRED-network-"><?php _e( 'Yes', 'mycred' ); ?></label>
				</li>
				<li>
					<input type="radio" name="mycred_network[master]" id="myCRED-network-overwrite-disabled" <?php checked( $prefs['master'], 0 ); ?> value="0" /> 
					<label for="myCRED-network-"><?php _e( 'No', 'mycred' ); ?></label>
				</li>
				<li>
					<p class="description"><?php echo sprintf( __( "If enabled, %s will use your main site's settings for all other sites in your network.", 'mycred' ), $name ); ?></p>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Central Logging', 'mycred' ); ?></label>
			<ol id="myCRED-network-log-enabling">
				<li>
					<input type="radio" name="mycred_network[central]" id="myCRED-network-overwrite-log-enabled" <?php checked( $prefs['central'], 1 ); ?> value="1" /> 
					<label for="myCRED-network-"><?php _e( 'Yes', 'mycred' ); ?></label>
				</li>
				<li>
					<input type="radio" name="mycred_network[central]" id="myCRED-network-overwrite-log-disabled" <?php checked( $prefs['central'], 0 ); ?> value="0" /> 
					<label for="myCRED-network-"><?php _e( 'No', 'mycred' ); ?></label>
				</li>
				<li>
					<p class="description"><?php echo sprintf( __( "If enabled, %s will log all site actions in your main site's log.", 'mycred' ), $name ); ?></p>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Site Block', 'mycred' ); ?></label>
			<ol id="myCRED-network-site-blocks">
				<li>
					<div class="h2"><input type="text" name="mycred_network[block]" id="myCRED-network-block" value="<?php echo $prefs['block']; ?>" class="long" /></div>
					<span class="description"><?php echo sprintf( __( 'Comma separated list of blog ids where %s is to be disabled.', 'mycred' ), $name ); ?></span>
				</li>
			</ol>
			<?php do_action( 'mycred_network_prefs', $this ); ?>

		</div>
		<?php do_action( 'mycred_after_network_prefs', $this ); ?>

	</div>
	<p><?php submit_button( __( 'Save Network Settings', 'mycred' ), 'primary large', 'submit', false ); ?></p>
</form>	
<?php do_action( 'mycred_bottom_network_page', $this ); ?>

</div>
<?php
		}

		/**
		 * Save Network Settings
		 * @since 0.1
		 * @version 1.1
		 */
		public function save_network_prefs( $settings ) {

			$new_settings = array();
			$new_settings['master'] = ( isset( $settings['master'] ) ) ? $settings['master'] : 0;
			$new_settings['central'] = ( isset( $settings['central'] ) ) ? $settings['central'] : 0;
			$new_settings['block'] = sanitize_text_field( $settings['block'] );

			$new_settings = apply_filters( 'mycred_save_network_prefs', $new_settings, $settings, $this->core );

			return $new_settings;
		}
	}
}
?>