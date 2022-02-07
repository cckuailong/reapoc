<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Shortcodes class.
 */
class DLM_Shortcodes {

	/**
	 * Setup the shortcodes
	 */
	public function setup() {
		add_shortcode( 'total_downloads', array( $this, 'total_downloads' ) );
		add_shortcode( 'total_files', array( $this, 'total_files' ) );
		add_shortcode( 'download', array( $this, 'download' ) );
		add_shortcode( 'download_data', array( $this, 'download_data' ) );

		// make this filterable because [downloads] has some known conflicts
		if ( apply_filters( 'dlm_add_shortcode_downloads', true ) ) {
			add_shortcode( 'downloads', array( $this, 'downloads' ) );
		}

		add_shortcode( 'dlm_no_access', array( $this, 'no_access_page' ) );
	}

	/**
	 * total_downloads function.
	 *
	 * @access public
	 * @return int
	 */
	public function total_downloads() {
		global $wpdb;

		$total = $wpdb->get_var( "
			SELECT SUM( meta_value ) FROM $wpdb->postmeta
			LEFT JOIN $wpdb->posts on $wpdb->postmeta.post_id = $wpdb->posts.ID
			WHERE meta_key = '_download_count'
			AND post_type = 'dlm_download'
			AND post_status = 'publish'
		" );

		return apply_filters( 'dlm_shortcode_total_downloads', $total );
	}

	/**
	 * total_files function.
	 *
	 * @access public
	 * @return void
	 */
	public function total_files() {
		$count_posts = wp_count_posts( 'dlm_download' );

		return $count_posts->publish;
	}

	/**
	 * download function.
	 *
	 * @access public
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function download( $atts, $content = '' ) {

		// extract shortcode atts
		extract( shortcode_atts( array(
			'id'         => '',
			'autop'      => false,
			'template'   => dlm_get_default_download_template(),
			'version_id' => '',
			'version'    => ''
		), $atts ) );

		// Make id filterable
		$id = apply_filters( 'dlm_shortcode_download_id', $id, $atts );

		// Check id
		if ( empty( $id ) ) {
			return "";
		}

		// Allow third party extensions to hijack shortcode
		$hijacked_content = apply_filters( 'dlm_shortcode_download_content', '', $id, $atts, $content );

		// If there's hijacked content, return it and be done with it
		if ( '' !== $hijacked_content ) {
			return $hijacked_content;
		}

		// shortcode output
		$output = '';

		// create download object
		try {
			/** @var DLM_Download $download */
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $id );

			// check if version is set
			if ( ! empty( $version ) ) {
				$version_id = $download->get_version_id_version_name( $version );
			}

			// check if version ID is set
			if ( isset( $version_id ) && 0 != $version_id ) {
				try {
					$version = download_monitor()->service( 'version_repository' )->retrieve_single( $version_id );
					$download->set_version( $version );
				} catch ( Exception $e ) {
				}
			}

			// if we have content, wrap in a link only
			if ( $content ) {
				$output = '<a href="' . $download->get_the_download_link() . '">' . $content . '</a>';
			} else {
				// template handler
				$template_handler = new DLM_Template_Handler();

				// buffer
				ob_start();

				// load template
				$template_handler->get_template_part( 'content-download', $template, '', array( 'dlm_download' => $download ) );

				// get output
				$output = ob_get_clean();

				// check if we need to wpautop()
				if ( 'true' === $autop || true === $autop ) {
					$output = wpautop( $output );
				}
			}
		} catch ( Exception $e ) {
			$output = '[' . __( 'Download not found', 'download-monitor' ) . ']';
		}

		return $output;
	}

	/**
	 * download_data function.
	 *
	 * @access public
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function download_data( $atts ) {

		extract( shortcode_atts( array(
			'id'         => '',
			'data'       => '',
			'version_id' => '',
			'version'    => ''
		), $atts ) );

		$id = apply_filters( 'dlm_shortcode_download_id', $id, $atts );

		if ( empty( $id ) || empty( $data ) ) {
			return "";
		}

		try {
			/** @var DLM_Download $download */
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $id );

			if ( ! empty( $version ) ) {
				$version_id = $download->get_version_id_version_name( $version );
			}

			if ( ! empty( $version_id ) ) {
				try {
					$version = download_monitor()->service( 'version_repository' )->retrieve_single( $version_id );
					$download->set_version( $version );
				} catch ( Exception $e ) {

				}
			}

			switch ( $data ) {

				// File / Version Info
				case 'filename' :
					return $download->get_version()->get_filename();
				case 'filetype' :
					return $download->get_version()->get_filetype();
				case 'filesize' :
					return $download->get_version()->get_filesize_formatted();
				case 'md5' :
					return $download->get_version()->get_md5();
				case 'sha1' :
					return $download->get_version()->get_sha1();
				case 'sha256' :
					return $download->get_version()->get_sha256();
				case 'crc32' :
				case 'crc32b' :
					return $download->get_version()->get_crc32b();
				case 'version' :
					return $download->get_version()->get_version_number();

				// Download Info
				case 'title' :
					return $download->get_title();
				case 'short_description' :
					return $download->get_excerpt();
				case 'download_link' :
					return $download->get_the_download_link();
				case 'download_count' :
					return $download->get_download_count();
				case 'post_content' :
					return wpautop( wptexturize( do_shortcode( $download->get_description() ) ) );
				case 'post_date' :
				case 'file_date' :
					return date_i18n( get_option( 'date_format' ), $download->get_version()->get_date()->format( 'U' ) );
				case 'author' :
					return $download->get_the_author();

				// Images
				case 'image' :
					return $download->get_image( 'full' );
				case 'thumbnail' :
					return $download->get_image( 'thumbnail' );

				// Taxonomies
				case 'tags' :
					$returnstr = "";
					$terms     = get_the_terms( $id, 'dlm_download_tag' );
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$terms_names = array();
						foreach ( $terms as $term ) {
							$terms_names[] = $term->name;
						}
						$returnstr = implode( ", ", $terms_names );
					}

					return $returnstr;
				case 'categories' :
					$returnstr = "";
					$terms     = get_the_terms( $id, 'dlm_download_category' );
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						$terms_names = array();
						foreach ( $terms as $term ) {
							$terms_names[] = $term->name;
						}
						$returnstr = implode( ", ", $terms_names );
					}

					return $returnstr;

			}
		} catch ( Exception $e ) {
			return '[' . __( 'Download not found', 'download-monitor' ) . ']';
		}

	}

	/**
	 * downloads function.
	 *
	 * @access public
	 *
	 * @param mixed $atts
	 *
	 * @return void
	 */
	public function downloads( $atts ) {
		global $dlm_max_num_pages;

		extract( shortcode_atts( array(
			// Query args
			'per_page'                  => '-1', // -1 = no limit
			'orderby'                   => 'date', // title, rand, ID, none, date, modifed, post__in, download_count
			'order'                     => 'desc', // ASC or DESC
			'include'                   => '', // Comma separate IDS
			'exclude'                   => '', // Comma separate IDS
			'offset'                    => '',
			'category'                  => '', // Comma separate slugs
			'category_include_children' => true, // Set to false to not include child categories
			'tag'                       => '', // Comma separate slugs
			'exclude_tag'               => '', // Comma separate slugs
			'featured'                  => false, // Set to true to only pull featured downloads
			'members_only'              => false, // Set to true to only pull member downloads

			// Output args
			'template'                  => dlm_get_default_download_template(),
			'loop_start'                => '<ul class="dlm-downloads">',
			'loop_end'                  => '</ul>',
			'before'                    => '<li>',
			'after'                     => '</li>',
			'paginate'                  => false
		), $atts ) );

		$post__in     = ! empty( $include ) ? explode( ',', $include ) : '';
		$post__not_in = ! empty( $exclude ) ? explode( ',', $exclude ) : '';
		$order        = strtoupper( $order );
		$meta_key     = '';

		switch ( $orderby ) {
			case 'title' :
			case 'rand' :
			case 'ID' :
			case 'date' :
			case 'modified' :
			case 'post__in' :
			case 'menu_order' :
				$orderby = $orderby;
				break;
			case 'id' :
				$orderby = 'ID';
				break;
			case 'hits' :
			case 'count' :
			case 'download_count' :
				$orderby  = 'meta_value_num';
				$meta_key = '_download_count';
				break;
			default :
				$orderby = 'title';
				break;
		}

		$args = array(
			'post_type'    => 'dlm_download',
			'post_status'  => 'publish',
			'orderby'      => $orderby,
			'order'        => $order,
			'meta_key'     => $meta_key,
			'post__in'     => $post__in,
			'post__not_in' => $post__not_in,
			'meta_query'   => array()
		);

		if ( $category || $tag || $exclude_tag ) {
			$args['tax_query'] = array( 'relation' => 'AND' );

			$tags = array_filter( explode( ',', $tag ) );

			// check if we include category children
			$include_children = ( $category_include_children === 'true' || $category_include_children === true );

			if ( ! empty( $category ) ) {

				if ( preg_match( '/\+/', $category ) ) {

					// categories with AND

					// string to array
					$categories = array_filter( explode( '+', $category ) );

					// check if explode had results
					if ( ! empty( $categories ) ) {

						foreach ( $categories as $category ) {
							$args['tax_query'][] = array(
								'taxonomy'         => 'dlm_download_category',
								'field'            => 'slug',
								'terms'            => $category,
								'include_children' => $include_children
							);
						}

					}

				} else {

					// categories with OR

					// string to array
					$categories = array_filter( explode( ',', $category ) );

					// check if explode had results
					if ( ! empty( $categories ) ) {

						$args['tax_query'][] = array(
							'taxonomy'         => 'dlm_download_category',
							'field'            => 'slug',
							'terms'            => $categories,
							'include_children' => $include_children
						);

					}

				}

			}

			if ( ! empty( $tags ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'dlm_download_tag',
					'field'    => 'slug',
					'terms'    => $tags
				);
			}

			if ( ! empty( $exclude_tag ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'dlm_download_tag',
					'field'    => 'slug',
					'terms'    => $exclude_tag,
					'operator' => 'NOT IN'
				);
			}
		}

		if ( $featured === 'true' || $featured === true ) {
			$args['meta_query'][] = array(
				'key'   => '_featured',
				'value' => 'yes'
			);
		} else {
			$args['meta_query'][] = array(
				'key'     => '_featured',
				'value'   => 'yes',
				'compare' => '!='
			);
		}

		if ( $members_only === 'true' || $members_only === true ) {
			$args['meta_query'][] = array(
				'key'   => '_members_only',
				'value' => 'yes'
			);
		}

		ob_start();

		// Allow filtering of arguments
		$args = apply_filters( 'dlm_shortcode_downloads_args', $args, $atts );

		$offset = $paginate ? ( max( 1, get_query_var( 'paged' ) ) - 1 ) * $per_page : $offset;

		// set offset to 0 if empty
		if ( '' === $offset ) {
			$offset = 0;
		}

		// fetch downloads
		$downloads = download_monitor()->service( 'download_repository' )->retrieve( $args, $per_page, $offset );

		// make all downloads filterable
		$downloads = apply_filters( 'dlm_shortcode_downloads_downloads', $downloads );

		// only calculate pages if we're paginating. Saves us a query when we're not
		$pages = 1;
		if ( $paginate ) {
			$pages = ceil( download_monitor()->service( 'download_repository' )->num_rows( $args ) / $per_page );
		}

		// Template handler
		$template_handler = new DLM_Template_Handler();

		if ( count( $downloads ) > 0 ) {

			// loop start output
			echo html_entity_decode( $loop_start );

			foreach ( $downloads as $download ) {

				// make download filterable
				$download = apply_filters( 'dlm_shortcode_downloads_loop_download', $download );

				// check if filtered download is still a DLM_Download instance
				if ( ! $download instanceof DLM_Download ) {
					continue;
				}

				// display the 'before'
				echo html_entity_decode( $before );

				// load the template
				if ( $download->has_version() ) {
					$template_handler->get_template_part( 'content-download', $template, '', array( 'dlm_download' => $download ) );
				} else {
					$template_handler->get_template_part( 'content-download', 'no-version', '', array( 'dlm_download' => $download ) );
				}

				// display the 'after'
				echo html_entity_decode( $after );

			} // end of the loop.

			// end of loop html
			echo html_entity_decode( $loop_end );

			if ( $paginate ) {
				$template_handler->get_template_part( 'pagination', '', '', array(
					'pages' => $pages
				) );
			} ?>

		<?php }

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * The dlm_no_access shortcode callback
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function no_access_page( $atts ) {
		global $wp;

		// atts
		$atts = shortcode_atts( array(
			'show_message' => 'true',
		), $atts );

		// start buffer
		ob_start();

		// show_message must be a bool
		$atts['show_message'] = ( 'true' === $atts['show_message'] );

		// return empty string if download-id is not set
		if ( ! isset( $wp->query_vars['download-id'] ) ) {
			return '';
		}

		// template handler
		$template_handler = new DLM_Template_Handler();

		try {
			/** @var \DLM_Download $download */
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( absint( $wp->query_vars['download-id'] ) );

			$version_id = '';

			if ( ! empty( $_GET['version'] ) ) {
				$version_id = $download->get_version_id_version_name( $_GET['version'] );
			}

			if ( ! empty( $_GET['v'] ) ) {
				$version_id = absint( $_GET['v'] );
			}

			if ( null != $download && $version_id ) {
				try {
					$version = download_monitor()->service( 'version_repository' )->retrieve_single( $version_id );
					$download->set_version( $version );
				} catch ( Exception $e ) {

				}
			}

			// load no access template
			$template_handler->get_template_part( 'no-access', '', '', array(
				'download'          => $download,
				'no_access_message' => ( ( $atts['show_message'] ) ? wp_kses_post( get_option( 'dlm_no_access_error', '' ) ) : '' )
			) );

		} catch ( Exception $exception ) {
			// no download with given ID
		}

		// set new content
		$content = ob_get_clean();

		return $content;
	}
}
