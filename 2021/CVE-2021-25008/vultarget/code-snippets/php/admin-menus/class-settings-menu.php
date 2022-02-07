<?php

/**
 * This class handles the settings admin menu
 * @since 2.4.0
 * @package Code_Snippets
 */
class Code_Snippets_Settings_Menu extends Code_Snippets_Admin_Menu {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'settings',
			_x( 'Settings', 'menu label', 'code-snippets' ),
			__( 'Snippets Settings', 'code-snippets' )
		);
	}

	/**
	 * Executed when the admin page is loaded
	 */
	public function load() {
		parent::load();

		if ( isset( $_GET['reset_settings'] ) && $_GET['reset_settings'] ) {

			if ( code_snippets_unified_settings() ) {
				delete_site_option( 'code_snippets_settings' );
			} else {
				delete_option( 'code_snippets_settings' );
			}

			add_settings_error(
				'code-snippets-settings-notices',
				'settings_reset',
				__( 'All settings have been reset to their defaults.', 'code-snippets' ),
				'updated'
			);

			set_transient( 'settings_errors', get_settings_errors(), 30 );

			wp_redirect( esc_url_raw( add_query_arg( 'settings-updated', true, remove_query_arg( 'reset_settings' ) ) ) );
			exit;
		}

		if ( is_network_admin() ) {

			if ( code_snippets_unified_settings() ) {
				$this->update_network_options();
			} else {
				wp_redirect( code_snippets()->get_menu_url( 'settings', 'admin' ) );
				exit;
			}
		}
	}

	/**
	 * Enqueue the stylesheet for the settings menu
	 */
	public function enqueue_assets() {
		$plugin = code_snippets();

		wp_enqueue_style(
			'code-snippets-edit',
			plugins_url( 'css/min/settings.css', $plugin->file ),
			array(), $plugin->version
		);

		code_snippets_editor_settings_preview_assets();
	}

	/**
	 * Render the admin screen
	 */
	public function render() {
		$update_url = is_network_admin() ? add_query_arg( 'update_site_option', true ) : admin_url( 'options.php' );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Settings', 'code-snippets' );

				if ( code_snippets()->admin->is_compact_menu() ) {

					printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
						esc_html_x( 'Manage', 'snippets', 'code-snippets' ),
						code_snippets()->get_menu_url()
					);

					printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
						esc_html_x( 'Add New', 'snippet', 'code-snippets' ),
						code_snippets()->get_menu_url( 'add' )
					);

					printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
						esc_html_x( 'Import', 'snippets', 'code-snippets' ),
						code_snippets()->get_menu_url( 'import' )
					);
				}

				?></h1>

			<?php settings_errors( 'code-snippets-settings-notices' ); ?>

			<form action="<?php echo esc_url( $update_url ); ?>" method="post">
				<?php

				settings_fields( 'code-snippets' );
				do_settings_sections( 'code-snippets' );

				?>
				<p class="submit" style="max-width: 1020px;">
					<?php submit_button( null, 'primary', 'submit', false ); ?>

					<a class="button button-secondary" style="float: right;"
					   href="<?php echo esc_url( add_query_arg( 'reset_settings', true ) ); ?>">
						<?php esc_html_e( 'Reset to Default', 'code-snippets' ); ?>
					</a>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Fill in for the Settings API in the Network Admin
	 */
	function update_network_options() {

		/* Ensure the settings have been saved */
		if ( ! isset( $_GET['update_site_option'], $_POST['code_snippets_settings'] ) || ! $_GET['update_site_option'] ) {
			return;
		}

		check_admin_referer( 'code-snippets-options' );

		/* Retrieve the saved options and save them to the database */
		$value = wp_unslash( $_POST['code_snippets_settings'] );
		update_site_option( 'code_snippets_settings', $value );

		/* Add an updated notice */
		if ( ! count( get_settings_errors() ) ) {
			add_settings_error( 'general', 'settings_updated', __( 'Settings saved.', 'code-snippets' ), 'updated' );
		}
		set_transient( 'settings_errors', get_settings_errors(), 30 );

		/* Redirect back to the settings menu */
		$goback = add_query_arg( 'settings-updated', 'true', remove_query_arg( 'update_site_option', wp_get_referer() ) );
		wp_redirect( $goback );
		exit;
	}
}
