<?php

class DLM_Integrations_PostTypesOrder {

	/**
	 * Setup integration
	 */
	public function setup() {
		add_filter( 'dlm_admin_dashboard_popular_downloads_filters', array( $this, 'ignore_popular_downloads_order' ), 10, 2 );
	}

	/**
	 * @param $filters
	 *
	 * @return array
	 */
	public function ignore_popular_downloads_order($filters) {
		$filters['ignore_custom_sort'] = true;
		return $filters;
	}
}