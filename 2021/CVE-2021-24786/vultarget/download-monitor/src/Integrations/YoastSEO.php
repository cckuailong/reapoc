<?php

class DLM_Integrations_YoastSEO {

	/**
	 * Setup integration
	 */
	public function setup() {
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( $this, 'exclude_taxonomies' ), 10, 2 );
	}

	/**
	 * Exclude our categories from YoastSEO sitemap
	 *
	 * @param $is_excluded
	 * @param $taxonomy_name
	 *
	 * @return bool
	 */
	public function exclude_taxonomies( $is_excluded, $taxonomy_name ) {
		if ( "dlm_download_category" == $taxonomy_name || "dlm_download_tag" == $taxonomy_name ) {
			$is_excluded = true;
		}

		return $is_excluded;
	}
}