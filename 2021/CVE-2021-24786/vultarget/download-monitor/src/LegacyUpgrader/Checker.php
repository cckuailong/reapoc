<?php


class DLM_LU_Checker {

	/**
	 * Check if DLM has already been upgraded
	 *
	 * @return bool
	 */
	private function has_been_upgraded() {
		return ( 1 === absint( get_option( DLM_Constants::LU_OPTION_UPGRADED, 0 ) ) );
	}

	/**
	 * Check if legacy table exists
	 * @return bool
	 */
	private function has_legacy_tables() {
		global $wpdb;

		$du                    = new DLM_LU_Download_Upgrader();
		$legacy_tables         = $du->get_legacy_tables();
		$sql                   = "SELECT 1 FROM `" . $legacy_tables['files'] . "` LIMIT 1;";
		$o_suppress_errors     = $wpdb->suppress_errors;
		$wpdb->suppress_errors = true;
		$r                     = $wpdb->query( $sql );
		$wpdb->suppress_errors = $o_suppress_errors;

		return ( $r !== false );
	}

	/**
	 * Returns true if there is at least one 'new' downloads.
	 * A new download is a custom post type with type 'dlm_download'
	 * @return bool
	 */
	private function has_modern_downloads() {
		$repo   = new DLM_WordPress_Download_Repository();
		$amount = $repo->num_rows();

		return ( $amount > 0 );
	}

	/**
	 * Mark website as upgraded
	 *
	 * @return void
	 */
	public function mark_upgraded() {
		update_option( DLM_Constants::LU_OPTION_UPGRADED, 1 );
		update_option( DLM_Constants::LU_OPTION_NEEDS_UPGRADING, 0 );
	}

	/**
	 * Check if DLM needs upgrading
	 *
	 * @return bool
	 */
	public function needs_upgrading() {

		// no upgrade requests in AJAX
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		/**
		 * Check if we already checked if need to upgrade.
		 * If we already checked we stored the result in this option.
		 * This prevents us from checking on every admin load.
		 */
		$needs_upgrading = get_option( DLM_Constants::LU_OPTION_NEEDS_UPGRADING, null );

		// if the option is null, it's not set yet and we need to check.
		if ( null === $needs_upgrading ) {

			// set default to 0 (no)
			$needs_upgrading = 0;

			// check if we already upgraded
			if ( ! $this->has_been_upgraded() ) {

				// check if we have legacy tables
				if ( $this->has_legacy_tables() ) {

					/**
					 * Check if there are already 'new' download
					 * We're doing this because there are users that manually upgraded in the past
					 * So they will have the legacy tables but don't need converting
					 */
					if ( ! $this->has_modern_downloads() ) {

						// this site needs upgrading, set to 1 (yes)
						$needs_upgrading = 1;
					}

				}
			}

			// store the option so we don't have to check this everytime.
			update_option( DLM_Constants::LU_OPTION_NEEDS_UPGRADING, $needs_upgrading );
		}

		// now convert to int and return if it's 1.
		return ( 1 === absint( $needs_upgrading ) );
	}

}