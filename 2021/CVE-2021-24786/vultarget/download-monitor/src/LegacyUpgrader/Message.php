<?php

class DLM_LU_Message {

	const OPTION_HIDE = 'dlm_lu_notice_hide';

	/**
	 * private void
	 */
	private function catch_hide_message() {
		if ( isset( $_GET['dlm_lu_hide_notice'] ) ) {
			$this->hide_message();
		}
	}

	/**
	 * @return void
	 */
	public function hide_message() {
		update_option( self::OPTION_HIDE, 1 );
	}

	/**
	 * @return bool
	 */
	private function is_hidden() {
		return ( 1 === absint( get_option( self::OPTION_HIDE, 0 ) ) );
	}

	/**
	 * @return void
	 */
	public function display() {
		$this->catch_hide_message();

		if ( ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'dlm_legacy_upgrade' != $_GET['page'] ) ) && ! $this->is_hidden() ) {
			add_action( 'admin_notices', array( $this, 'body' ) );
		}
	}

	/**
	 * @return void
	 */
	public function body() {
		$vm = new DLM_View_Manager();
		$vm->display( "notice-lu-upgrade" );
	}
}