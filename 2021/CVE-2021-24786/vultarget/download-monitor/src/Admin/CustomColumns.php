<?php

class DLM_Custom_Columns {

	public function setup() {
		add_filter( 'manage_edit-dlm_download_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_dlm_download_posts_custom_column', array( $this, 'column_data' ), 2 );
		add_filter( 'manage_edit-dlm_download_sortable_columns', array( $this, 'sortable_columns' ) );
	}

	/**
	 * columns function.
	 *
	 * @access public
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_columns( $columns ) {
		$columns = array();

		$columns["cb"]             = "<input type=\"checkbox\" />";
		$columns["thumb"]          = '<span>' . __( "Image", 'download-monitor' ) . '</span>';
		$columns["title"]          = __( "Title", 'download-monitor' );
		$columns["download_id"]    = __( "ID", 'download-monitor' );
		$columns["file"]           = __( "File", 'download-monitor' );
		$columns["version"]        = __( "Version", 'download-monitor' );
		$columns["download_cat"]   = __( "Categories", 'download-monitor' );
		$columns["download_tag"]   = __( "Tags", 'download-monitor' );
		$columns["download_count"] = __( "Download count", 'download-monitor' );
		$columns["featured"]       = __( "Featured", 'download-monitor' );
		$columns["members_only"]   = __( "Members only", 'download-monitor' );
		$columns["redirect_only"]  = __( "Redirect only", 'download-monitor' );
		$columns["date"]           = __( "Date posted", 'download-monitor' );

		return $columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 *
	 * @param mixed $column
	 *
	 * @return void
	 */
	public function column_data( $column ) {
		global $post;

		/** @var DLM_Download $download */
		$downloads = download_monitor()->service( 'download_repository' )->retrieve( array(
			'p'           => absint( $post->ID ),
			'post_status' => array( 'any', 'trash' )
		), 1 );

		if ( 0 == count( $downloads ) ) {
			return;
		}

		$download = $downloads[0];

		/** @var DLM_Download_Version $file */
		$file = $download->get_version();

		switch ( $column ) {
			case "thumb" :
				echo $download->get_image();
				break;
			case "download_id" :
				echo $post->ID;
				break;
			case "download_cat" :
				if ( ! $terms = get_the_term_list( $post->ID, 'dlm_download_category', '', ', ', '' ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					echo $terms;
				}
				break;
			case "download_tag" :
				if ( ! $terms = get_the_term_list( $post->ID, 'dlm_download_tag', '', ', ', '' ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					echo $terms;
				}
				break;
			case "featured" :
				if ( $download->is_featured() ) {
					echo '<span class="yes">' . __( 'Yes', 'download-monitor' ) . '</span>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case "members_only" :
				if ( $download->is_members_only() ) {
					echo '<span class="yes">' . __( 'Yes', 'download-monitor' ) . '</span>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case "redirect_only" :
				if ( $download->is_redirect_only() ) {
					echo '<span class="yes">' . __( 'Yes', 'download-monitor' ) . '</span>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case "file" :
				if ( $file ) {
					echo '<a href="' . $download->get_the_download_link() . '"><code>' . $file->get_filename();
					if ( $size = $download->get_version()->get_filesize_formatted() ) {
						echo ' &ndash; ' . $size;
					}
					echo '</code></a>';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case "version" :
				if ( $file && $file->get_version() ) {
					echo $file->get_version();
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
			case "download_count" :
				echo number_format( $download->get_download_count(), 0, '.', ',' );
				break;
			case "featured" :
				if ( $download->is_featured() ) {
					echo '<img src="' . download_monitor()->get_plugin_url() . '/assets/images/on.png" alt="yes" />';
				} else {
					echo '<span class="na">&ndash;</span>';
				}
				break;
		}
	}

	/**
	 * sortable_columns function.
	 *
	 * @access public
	 *
	 * @param mixed $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$custom = array(
			'download_id'    => 'download_id',
			'download_count' => 'download_count',
			'featured'       => 'featured',
			'members_only'   => 'members_only',
			'redirect_only'  => 'redirect_only',
		);

		return wp_parse_args( $custom, $columns );
	}

}
