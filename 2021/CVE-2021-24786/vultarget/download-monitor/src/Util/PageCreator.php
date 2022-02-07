<?php

namespace Never5\DownloadMonitor\Util;

class PageCreator {

	/**
	 * Create no access page
	 *
	 * @return int
	 */
	public function create_no_access_page() {
		return $this->setup_page(
			__( 'No Access', 'download-monitor' ),
			'[dlm_no_access]',
			'dlm_no_access_page'
		);
	}

	/**
	 * Create cart page
	 *
	 * @return int
	 */
	public function create_cart_page() {
		return $this->setup_page(
			__( 'Cart', 'download-monitor' ),
			'[dlm_cart]',
			'dlm_page_cart'
		);
	}

	/**
	 * Create checkout page
	 *
	 * @return int
	 */
	public function create_checkout_page() {
		return $this->setup_page(
			__( 'Checkout', 'download-monitor' ),
			'[dlm_checkout]',
			'dlm_page_checkout'
		);
	}

	/**
	 * Creates page and set the ID as option with given option key
	 *
	 * @param string $title
	 * @param string $content
	 * @param string $option
	 *
	 * @return int
	 */
	private function setup_page( $title, $content, $option ) {
		$new_page_id = $this->create_page( $title, $content );

		if ( 0 !== $new_page_id ) {
			update_option( $option, absint( $new_page_id ) );
		}

		return $new_page_id;
	}

	/**
	 * Create page with given title and content, returns newly created ID
	 *
	 * @param string $title
	 * @param string $content
	 *
	 * @return int
	 */
	private function create_page( $title, $content ) {
		// create no-access listing page if not exists
		$slug = sanitize_title( $title );
		$page = get_page_by_path( $slug );

		// check if listings page exists
		if ( null == $page ) {

			// create page
			$page_id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => $title,
				'post_content' => $content,
				'post_status'  => 'publish'
			) );
		} else {
			$page_id = $page->ID;
		}

		if ( is_wp_error( $page_id ) ) {
			$page_id = 0;
		}

		return $page_id;
	}

}