<?php

class DLM_Settings_Page {

	/**
	 * Setup hooks
	 */
	public function setup() {

		// menu item
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 12 );

		// catch setting actions
		add_action( 'admin_init', array( $this, 'catch_admin_actions' ) );
	}

	/**
	 * Add settings menu item
	 */
	public function add_admin_menu() {
		// Settings page
		add_submenu_page( 'edit.php?post_type=dlm_download', __( 'Settings', 'download-monitor' ), __( 'Settings', 'download-monitor' ), 'manage_options', 'download-monitor-settings', array(
			$this,
			'settings_page'
		) );
	}

	/**
	 * Catch and trigger admin actions
	 */
	public function catch_admin_actions() {

		if ( isset( $_GET['dlm_action'] ) && isset( $_GET['dlm_nonce'] ) ) {
			$action = $_GET['dlm_action'];
			$nonce  = $_GET['dlm_nonce'];

			// check nonce
			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die( "Download Monitor action nonce failed." );
			}

			switch ( $action ) {
				case 'dlm_clear_transients':
					$result = download_monitor()->service( 'transient_manager' )->clear_all_version_transients();
					if ( $result ) {
						wp_redirect( add_query_arg( array( 'dlm_action_done' => $action ), DLM_Admin_Settings::get_url() ) );
						exit;
					}
					break;
			}
		}

		if ( isset( $_GET['dlm_action_done'] ) ) {
			add_action( 'admin_notices', array( $this, 'display_admin_action_message' ) );
		}
	}

	/**
	 * Display the admin action success mesage
	 */
	public function display_admin_action_message() {
		?>
        <div class="notice notice-success">
			<?php
			switch ( $_GET['dlm_action_done'] ) {
				case 'dlm_clear_transients':
					echo "<p>" . __( 'Download Monitor Transients successfully cleared!', 'download-monitor' ) . "</p>";
					break;
			}
			?>
        </div>
		<?php
	}


	/**
	 * settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_page() {

		// initialize settings
		$admin_settings = new DLM_Admin_Settings();
		$settings       = $admin_settings->get_settings();
		$tab            = $this->get_active_tab();

		// print global notices
		$this->print_global_notices();
		?>
        <div class="wrap dlm-admin-settings">
        <form method="post" action="options.php">

		<?php $this->generate_tabs( $settings ); ?>

		<?php

		if ( ! empty( $_GET['settings-updated'] ) ) {
			$this->need_rewrite_flush = true;
			echo '<div class="updated notice is-dismissible"><p>' . __( 'Settings successfully saved', 'download-monitor' ) . '</p></div>';

			$dlm_settings_tab_saved = get_option( 'dlm_settings_tab_saved', 'general' );

			echo '<script type="text/javascript">var dlm_settings_tab_saved = "' . $dlm_settings_tab_saved . '";</script>';
		}

		// loop fields for this tab
		if ( isset( $settings[ $tab ] ) ) {

			$active_section = $this->get_active_section( $settings[ $tab ]['sections'] );

			// output correct settings_fields
			$option_name = "dlm_" . $tab . "_" . $active_section;
			settings_fields( $option_name );

			if ( count( $settings[ $tab ]['sections'] ) > 1 ) {
				?>
                <ul class="nav-section-wrapper">
					<?php foreach ( $settings[ $tab ]['sections'] as $section_key => $section ) : ?>
						<?php echo "<li" . ( ( $active_section == $section_key ) ? " class='active-section'" : "" ) . ">"; ?>
                                <a href = "<?php echo add_query_arg( array( 'tab'     => $tab,
								                                              'section' => $section_key
									), DLM_Admin_Settings::get_url() ); ?>" ><?php echo $section['title']; ?></a></liM>
					<?php endforeach; ?>
                </ul>
                <h2><?php echo esc_html( $settings[ $tab ]['sections'][ $active_section ]['title'] ); ?></h2>
				<?php
			}

					//echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';
					echo '<table class="form-table">';
					foreach ( $settings[ $tab ]['sections'][ $active_section ]['fields'] as $option ) {


						$cs = 1;

						echo '<tr valign="top">';
						if ( isset( $option['label'] ) && '' !== $option['label'] ) {
							echo '<th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th>';
						} else {
							$cs ++;
						}


						echo '<td colspan="' . $cs . '">';

						if ( ! isset( $option['type'] ) ) {
							$option['type'] = '';
						}

						// make new field object
						$field = DLM_Admin_Fields_Field_Factory::make( $option );

						// check if factory made a field
						if ( null !== $field ) {
							// render field
							$field->render();

							if ( isset( $option['desc'] ) && '' !== $option['desc'] ) {
								echo ' <p class="dlm-description description">' . $option['desc'] . '</p>';
							}
						}

						echo '</td></tr>';


					}
					echo '</table>';

					//echo '</div>';
				}


?>
    <p class="submit">
    <input type="submit" class="button-primary"
    value="<?php _e( 'Save Changes', 'download-monitor' ); ?>"/>
    </p>
    </form>
    </div>
	<?php
}

/**
 * Print global notices
 */
private
function print_global_notices() {

	// check for nginx
	if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stristr( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false && 1 != get_option( 'dlm_hide_notice-nginx_rules', 0 ) ) {

		// get upload dir
		$upload_dir = wp_upload_dir();

		// replace document root because nginx uses path from document root
		$upload_path = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $upload_dir['basedir'] );

		// form nginx rules
		$nginx_rules = "location " . $upload_path . "/dlm_uploads {<br/>deny all;<br/>return 403;<br/>}";
		echo '<div class="error notice is-dismissible dlm-notice" id="nginx_rules" data-nonce="' . wp_create_nonce( 'dlm_hide_notice-nginx_rules' ) . '">';
		echo '<p>' . __( "Because your server is running on nginx, our .htaccess file can't protect your downloads.", 'download-monitor' );
		echo '<br/>' . sprintf( __( "Please add the following rules to your nginx config to disable direct file access: %s", 'download-monitor' ), '<br/><br/><code class="dlm-code-nginx-rules">' . $nginx_rules . '</code>' ) . '</p>';
		echo '</div>';
	}

}

/**
 * @param array $settings
 */
private
function generate_tabs( $settings ) {
	?>
    <h2 class="nav-tab-wrapper">
		<?php
		foreach ( $settings as $key => $section ) {

			// backwards compatibility for when $section did not have 'title' index yet (it simply had the title set at 0)
			$title = ( isset( $section['title'] ) ? $section['title'] : $section[0] );

			echo '<a href="' . add_query_arg( 'tab', $key, DLM_Admin_Settings::get_url() ) . '" class="nav-tab' . ( ( $this->get_active_tab() === $key ) ? ' nav-tab-active' : '' ) . '">' . esc_html( $title ) . '</a>';
		}
		?>
    </h2><br/>
	<?php
}

/**
 * Returns first key of array
 *
 * @param $a
 *
 * @return string
 */
private
function array_first_key( $a ) {
	reset( $a );

	return key( $a );
}

/**
 * Return active tab
 *
 * @return string
 */
private
function get_active_tab() {
	return ( ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : 'general' );
}

/**
 * Return active section
 *
 * @param $sections
 *
 * @return string
 */
private
function get_active_section( $sections ) {
	return ( ! empty( $_GET['section'] ) ? sanitize_title( $_GET['section'] ) : $this->array_first_key( $sections ) );
}


}