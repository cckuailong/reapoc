<?php

use \Never5\DownloadMonitor\Util;

class DLM_Ajax_CreatePage extends DLM_Ajax {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'create_page' );
	}

	/**
	 * AJAX callback method
	 *
	 * @return void
	 */
	public function run() {
		// check nonce
		$this->check_nonce();

		// check caps
		if ( ! current_user_can( 'edit_posts' ) ) {
			exit( 0 );
		}

		if ( ! empty( $_GET['page'] ) ) {

			$pc      = new Util\PageCreator();
			$new_page_id = 0;

			switch ( $_GET['page'] ) {
				case 'no-access':
					$new_page_id = $pc->create_no_access_page();
					break;
				case 'cart':
					$new_page_id = $pc->create_cart_page();
					break;
				case 'checkout':
					$new_page_id = $pc->create_checkout_page();
					break;
			}

			if ( $new_page_id !== 0 ) {
				wp_send_json( array( 'result' => 'success' ) );
				exit;
			} else {
				wp_send_json( array(
					'result' => 'failed',
					'error'  => __( "Couldn't create page", 'download-monitor' )
				) );
			}
		}
		
		wp_send_json( array( 'result' => 'failed', 'error' => __( "No page set", 'download-monitor' ) ) );

		exit;
	}

}