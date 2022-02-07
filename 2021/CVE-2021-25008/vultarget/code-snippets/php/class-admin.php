<?php

/**
 * Functions specific to the administration interface
 *
 * @package Code_Snippets
 */
class Code_Snippets_Admin {

	public $menus = array();

	function __construct() {

		if ( is_admin() ) {
			$this->run();
		}
	}

	public function load_classes() {
		$this->menus['manage'] = new Code_Snippets_Manage_Menu();
		$this->menus['edit'] = new Code_Snippets_Edit_Menu();
		$this->menus['import'] = new Code_Snippets_Import_Menu();

		if ( is_network_admin() === code_snippets_unified_settings() ) {
			$this->menus['settings'] = new Code_Snippets_Settings_Menu();
		}

		foreach ( $this->menus as $menu ) {
			$menu->run();
		}
	}

	public function run() {
		add_action( 'init', array( $this, 'load_classes' ), 11 );

		add_filter( 'mu_menu_items', array( $this, 'mu_menu_items' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CODE_SNIPPETS_FILE ), array( $this, 'plugin_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links' ), 10, 2 );
		add_action( 'code_snippets/admin/manage', array( $this, 'survey_message' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_menu_icon' ) );

		if ( isset( $_POST['save_snippet'] ) && $_POST['save_snippet'] ) {
			add_action( 'code_snippets/allow_execute_snippet', array( $this, 'prevent_exec_on_save' ), 10, 3 );
		}
	}

	/**
	 * @return bool
	 */
	public function is_compact_menu() {
		return ! is_network_admin() && apply_filters( 'code_snippets_compact_menu', false );
	}

	/**
	 * Allow super admins to control site admin access to
	 * snippet admin menus
	 *
	 * Adds a checkbox to the *Settings > Network Settings*
	 * network admin menu
	 *
	 * @since 1.7.1
	 *
	 * @param  array $menu_items The current mu menu items
	 *
	 * @return array             The modified mu menu items
	 */
	function mu_menu_items( $menu_items ) {
		$menu_items['snippets'] = __( 'Snippets', 'code-snippets' );
		$menu_items['snippets_settings'] = __( 'Snippets &raquo; Settings', 'code-snippets' );

		return $menu_items;
	}

	/**
	 * Load the stylesheet for the admin menu icon
	 */
	function load_admin_menu_icon() {

		wp_enqueue_style(
			'menu-icon-snippets',
			plugins_url( 'css/min/menu-icon.css', code_snippets()->file ),
			array(), code_snippets()->version
		);
	}

	/**
	 * Prevent the snippet currently being saved from being executed
	 * so it is not run twice (once normally, once
	 *
	 * @param bool   $exec Whether the snippet will be executed
	 * @param int    $exec_id The ID of the snippet being executed
	 * @param string $table_name
	 *
	 * @return bool Whether the snippet will be executed
	 */
	function prevent_exec_on_save( $exec, $exec_id, $table_name ) {

		if ( ! isset( $_POST['save_snippet'], $_POST['snippet_id'] ) ) {
			return $exec;
		}

		if ( code_snippets()->db->get_table_name() !== $table_name ) {
			return $exec;
		}

		$id = intval( $_POST['snippet_id'] );

		if ( $id === $exec_id ) {
			return false;
		}

		return $exec;
	}

	/**
	 * Adds a link pointing to the Manage Snippets page
	 *
	 * @since 2.0
	 *
	 * @param  array $links The existing plugin action links
	 *
	 * @return array        The modified plugin action links
	 */
	function plugin_settings_link( $links ) {
		array_unshift( $links, sprintf(
			'<a href="%1$s" title="%2$s">%3$s</a>',
			code_snippets()->get_menu_url(),
			__( 'Manage your existing snippets', 'code-snippets' ),
			__( 'Snippets', 'code-snippets' )
		) );

		return $links;
	}

	/**
	 * Adds extra links related to the plugin
	 *
	 * @since 2.0
	 *
	 * @param  array  $links The existing plugin info links
	 * @param  string $file The plugin the links are for
	 *
	 * @return array         The modified plugin info links
	 */
	function plugin_meta_links( $links, $file ) {

		/* We only want to affect the Code Snippets plugin listing */
		if ( plugin_basename( CODE_SNIPPETS_FILE ) !== $file ) {
			return $links;
		}

		$format = '<a href="%1$s" title="%2$s">%3$s</a>';

		/* array_merge appends the links to the end */

		return array_merge( $links, array(
			sprintf( $format,
				'https://wordpress.org/plugins/code-snippets/',
				__( 'Visit the WordPress.org plugin page', 'code-snippets' ),
				__( 'About', 'code-snippets' )
			),
			sprintf( $format,
				'https://wordpress.org/support/plugin/code-snippets/',
				__( 'Visit the support forums', 'code-snippets' ),
				__( 'Support', 'code-snippets' )
			),
			sprintf( $format,
				'https://sheabunge.com/donate/',
				__( "Support this plugin's development", 'code-snippets' ),
				__( 'Donate', 'code-snippets' )
			),
		) );
	}

	/**
	 * Print a notice inviting people to participate in the Code Snippets Survey
	 *
	 * @since  1.9
	 * @return void
	 */
	function survey_message() {
		global $current_user;

		$key = 'ignore_code_snippets_survey_message';

		/* Bail now if the user has dismissed the message */
		if ( get_user_meta( $current_user->ID, $key ) ) {
			return;
		} elseif ( isset( $_GET[ $key ], $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $key ) ) {
			add_user_meta( $current_user->ID, $key, true, true );

			return;
		}

		?>

		<br />

		<div class="updated code-snippets-survey-message">
			<p>

				<?php _e( "<strong>Have feedback on Code Snippets?</strong> Please take the time to answer a short survey on how you use this plugin and what you'd like to see changed or added in the future.", 'code-snippets' ); ?>

				<a href="https://codesnippets.pro/survey/" class="button secondary"
				   target="_blank" style="margin: auto .5em;">
					<?php esc_html_e( 'Take the survey now', 'code-snippets' ); ?>
				</a>

				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( $key, true ), $key ) ); ?>"><?php esc_html_e( 'Dismiss', 'code-snippets' ); ?></a>

			</p>
		</div>

		<?php
	}
}
