<?php

class DLM_LU_Page {

	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 12 );
	}

	/**
	 * Add settings menu item
	 */
	public function add_admin_menu() {
		// Settings page
		add_submenu_page( '_dlm_not_existing_slug', __( 'Legacy Upgrader', 'download-monitor' ), __( 'Legacy Upgrader', 'download-monitor' ), 'manage_downloads', 'dlm_legacy_upgrade', array(
			$this,
			'view'
		) );
	}

	/**
	 * Display page
	 */
	public function view() {

		$show_upgrader = true;
		$checker       = new DLM_LU_Checker();
		if ( ! $checker->needs_upgrading() ) {
			$show_upgrader = false;
		}

		if ( isset( $_GET['dlm_lu_force'] ) ) {
			$show_upgrader = true;
		}


		?>
        <div class="wrap">
            <h1><?php _e( 'Download Monitor - Legacy Upgrade', 'download-monitor' ); ?></h1><br/>
            <p><?php printf( __( "Welcome to the Download Monitor Legacy Upgrader. On this page we will upgrade your old Download Monitor (legacy) data so it will work with the latest version. If you're on this page, it should mean that you updated to this version from Download Monitor %s. If you're unsure if this is correct, or you want to read more about the legacy upgrade, we've setup a page that will explain this process in a lot more detail. %sClick here%s if to view that page.", 'download-monitor' ), "<strong>3.x</strong>", "<a href='https://www.download-monitor.com/kb/legacy-upgrade?utm_source=plugin&utm_medium=dlm-lu-upgrade-page&utm_campaign=dlm-lu-more-information' target='_blank'>", "</a>" ); ?></p>
			<?php
			if ( ! $show_upgrader ) {
				?>
                <p style='font-weight:bold;color:#a00;font-size:1.4em;'><?php _e( "WARNING: We don't think your database needs upgrading. Only continue if you're 100% sure what you're doing!", 'download-monitor' ); ?></p>
                <a class="button button-primary button-large"
                   href="<?php echo add_query_arg( array( 'page' => 'dlm_legacy_upgrade', 'dlm_lu_force' => 'true' ), admin_url( 'options.php' ) ); ?>"><?php _e( "I'm sure I want to run the upgrader anyway", 'download-monitor' ); ?></a>
				<?php
			} else {
				?>
                <div id="dlm-legacy-upgrade-container"></div>
				<?php
			}
			?>
        </div>
		<?php
	}

}