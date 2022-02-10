<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function rss_library_display_pagination( $previouspagenumber, $nextpagenumber, $numberofpages, $pagenumber ) {

	$dotbelow = false;
	$dotabove = false;
	$paginationoutput = '';

	global $wp;

	if ( $numberofpages > 1 ) {
		$paginationoutput = '<div class="pageselector"><!-- Div Pagination -->';

		if ( 1 != $pagenumber ) {
			$paginationoutput .= '<span class="previousnextactive">';

			$argumentarray = array( 'rsslibrarypage' => $previouspagenumber ); 
			$targetaddress = esc_url( add_query_arg( $argumentarray ), '/' . $wp->request );

			$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Previous', 'link-library' ) . '</a>';

			$paginationoutput .= '</span>';
		} else {
			$paginationoutput .= '<span class="previousnextinactive">' . __('Previous', 'link-library') . '</span>';
		}

		$dotabove = false;
		$dotbelow = false;
		for ( $counter = 1; $counter <= $numberofpages; $counter++ ) {
			if ( $counter <= 2 || $counter >= $numberofpages - 1 || ( $counter <= $pagenumber + 2 && $counter >= $pagenumber - 2 ) ) {
				if ( $counter != $pagenumber ) {
					$paginationoutput .= '<span class="unselectedpage">';
				} else {
					$paginationoutput .= '<span class="selectedpage">';
				}

				$argumentarray = array( 'rsslibrarypage' => $counter ); 
			$targetaddress = esc_url( add_query_arg( $argumentarray ), '/' . $wp->request );

				$paginationoutput .= '<a href="' . $targetaddress . '">' . $counter . '</a>';

				$paginationoutput .= '</a></span>';
			}

			if ( $counter >= 2 && $counter < $pagenumber - 2 && false == $dotbelow ) {
				$dotbelow = true;
				$paginationoutput .= '...';
			} elseif ( $counter > $pagenumber + 2 && $counter < $numberofpages - 1 && false == $dotabove ) {
				$dotabove = true;
				$paginationoutput .= '...';
			}
		}

		if ( $pagenumber != $numberofpages ) {
			$paginationoutput .= '<span class="previousnextactive">';

			$argumentarray = array( 'rsslibrarypage' => $nextpagenumber ); 
			$targetaddress = esc_url( add_query_arg( $argumentarray ), '/' . $wp->request );

			$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Next', 'link-library' ) . '</a>';

			$paginationoutput .= '</span>';
		} else {
			$paginationoutput .= '<span class="previousnextinactive">' . __('Next', 'link-library') . '</span>';
		}

		$paginationoutput .= '</div><!-- Div Pagination -->';
	}

	return $paginationoutput;
}


function RenderRSSLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $parent_cat_id = 0, $level = 0, $display_children = true, &$rsscount, &$rss_array_items ) {

	extract( $generaloptions );
	extract( $libraryoptions );

	global $wp_query;

	$childcategory_cpt = array();

	/* This case will only happen if the user entered bad data in the admin page or if someone is trying to inject bad data in SQL query */
	if ( !empty( $categorylist_cpt ) ) {
		$categorylistarray = explode( ',', $categorylist_cpt );

		foreach( $categorylistarray as $key => $categorylistitem ) {
			$startpos = strpos( $categorylistitem, '(', 0 );
			if ( false !== $startpos ) {
				$endpos = strpos( $categorylistitem, ')', 0 );
				if ( false !== $endpos ) {
					$topcat = substr( $categorylistitem, 0, $startpos );
					$subcatsort = substr( $categorylistitem, $startpos + 1, $endpos - $startpos - 1 );
					$childcategory_cpt[$topcat] = $subcatsort;
					$categorylistarray[$key] = $topcat;
				}
			}
		}

		if ( true === array_filter( $categorylistarray, 'is_int' ) ) {
			return 'List of requested categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	if ( !empty( $excludecategorylist_cpt ) ) {
		$excludecategorylistarray = explode( ',', $excludecategorylist_cpt );

		if ( true === array_filter( $excludecategorylistarray, 'is_int' ) ) {
			return 'List of requested excluded categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	$validdirections = array( 'ASC', 'DESC' );

	if ( $level == 0 ) {
		$output = "\n<!-- Beginning of RSS Library Output -->\n\n";
	} else {
		$output = '';
	}

	$currentcategory = 1;
	$pagenumber = 1;
	$number_of_pages = 1;
	$categoryname = '';

	$link_count = wp_count_posts( 'link_library_links' );

	if ( isset( $link_count ) && !empty( $link_count ) && ( $link_count->publish > 0 || ( $showinvisible && $link_count->private > 0 ) || ( $showuserlinks && $link_count->pending ) ) ) {

		$link_categories_query_args = array( );

		if ( $hide_if_empty ) {
			$link_categories_query_args['hide_empty'] = true;
		} else {
			$link_categories_query_args['hide_empty'] = false;
		}

		if ( !empty( $categorylist_cpt ) && ( $level == 0 || ( $level > 0 && !empty( $categorylist_cpt ) ) ) ) {
			$link_categories_query_args['include'] = explode( ',', $categorylist_cpt );
		}

		if ( !empty( $excludecategorylist_cpt ) ) {
			$link_categories_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
		}

		$link_categories_query_args['orderby'] = 'name';
		$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';

		$link_categories = get_terms( 'link_library_category', $link_categories_query_args );

		if ( $level == 0 ) {
			$output .= "<div id='rsslist" . $settings . "' class='rsslist'><!-- Div Rsslist -->\n";
		}

		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );

		if ( !empty( $link_categories ) ) {
			foreach ( $link_categories as $link_category ) {
				if ( !empty( $maxrssfeeditems ) && is_numeric( $maxrssfeeditems ) && 0 < $maxrssfeeditems && $rsscount > $maxrssfeeditems ) {
					break;
				}

				$link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => -1 );

				$link_query_args['tax_query'][] =
					array(
						'taxonomy' => 'link_library_category',
						'field'    => 'term_id',
						'terms'    => $link_category->term_id,
						'include_children' => false
					);

				if ( !empty( $taglist_cpt ) || ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {
					$tag_array = array();

					if ( ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {
						$tag_array = explode( '.', $_GET['link_tags'] );
					} elseif( !empty( $taglist_cpt ) ) {
						$tag_array = explode( ',', $taglist_cpt );
					}

					// YL: Make this an option
					if ( !empty( $tag_array ) ) {
						$showlinksonclick = false;
					}

					if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
						$link_query_args['tax_query'][] = array(
							'taxonomy' => 'link_library_tags',
							'field' => 'slug',
							'terms' => $tag_array,
						);
					} elseif ( !empty( $taglist_cpt ) ) {
						$link_query_args['tax_query'][] = array(
							'taxonomy' => 'link_library_tags',
							'field' => 'id',
							'terms' => $tag_array,
						);
					}					
				}

				if ( !empty( $excludetaglist_cpt ) ) {
					$exclude_tag_array = array();

					if( !empty( $excludetaglist_cpt ) ) {
						$exclude_tag_array = explode( ',', $excludetaglist_cpt );
					}

					// YL: Make this an option
					if ( !empty( $exclude_tag_array ) ) {
						$showlinksonclick = false;
					}

					if ( !empty( $excludetaglist_cpt ) ) {
						$link_query_args['tax_query'][] = array(
							'taxonomy' => 'link_library_tags',
							'field' => 'id',
							'terms' => $exclude_tag_array,
							'operator' => 'NOT IN'
						);
					}
				}

				$link_query_args['post_status'] = array( 'publish' );

				$linkorder = 'name';
				$link_query_args['orderby']['title'] = 'ASC';

				if ( true == $debugmode ) {
					$linkquerystarttime = microtime ( true );
				}

				if ( isset( $link_query_args['meta_query'] ) && is_array( $link_query_args['meta_query'] ) && sizeof( $link_query_args['meta_query'] ) > 1 ) {
					$link_query_args['meta_query']['relation'] = 'AND';
				}

				if ( isset( $link_query_args['tax_query'] ) && is_array( $link_query_args['tax_query'] ) && sizeof( $link_query_args['tax_query'] ) > 1 ) {
					$link_query_args['tax_query']['relation'] = 'AND';
				}

				$the_link_query = new WP_Query( $link_query_args );

				if ( $debugmode ) {
					$output .= "\n<!-- Link Query: " . print_r( $link_query_args, TRUE ) . "-->\n\n";
					$output .= "\n<!-- Link Results: " . print_r( $the_link_query, TRUE ) . "-->\n\n";
					$output .= "\n<!-- Link Query Execution Time: " . ( microtime( true ) - $linkquerystarttime ) . "-->\n\n";
				}

				$child_cat_params = array( 'taxonomy' => 'link_library_category', 'child_of' => $link_category->term_id );

				if ( $hide_if_empty ) {
					$child_cat_params['hide_empty'] = true;
				} else {
					$child_cat_params['hide_empty'] = false;
				}

				$childcategories = get_terms( $child_cat_params );

				$cat_has_children = false;
				if ( !is_wp_error( $childcategories ) && !empty( $childcategories ) ) {
					$cat_has_children = true;

					$children_have_links = false;
				}

				// Display links
				if ( ( $the_link_query->found_posts || !$hide_if_empty || $cat_has_children ) ) {
					if ( ( $the_link_query->have_posts() || !$hide_if_empty || $cat_has_children ) && ( empty( $maxrssfeeditems ) || 0 == $maxrssfeeditems | $rsscount <= $maxrssfeeditems ) ) {
						$start_rss_count = $rsscount;
						$currentcategoryid = $link_category->term_id;

						while ( $the_link_query->have_posts() ) {
							$the_link_query->the_post();

							if ( !empty( $maxrssfeeditems ) && is_numeric( $maxrssfeeditems ) && 0 < $maxrssfeeditems && $rsscount > $maxrssfeeditems ) {
								break;
							}

							$linkitem = array();
							$linkitem['term_id'] = $link_category->term_id;
							$linkitem['link_name'] = get_the_title();
							$link_meta = get_metadata( 'post', get_the_ID() );

							$link_terms = wp_get_post_terms( get_the_ID(), 'link_library_category' );
							if ( !empty( $link_terms ) ) {
								$link_term_array = array();
								foreach( $link_terms as $link_term ) {
									$link_term_array[] = $link_term->name;
								}

								if ( !empty( $link_term_array ) ) {
									$link_term_string = implode( ', ', $link_term_array );
									$linkitem['category_name'] = $link_term_string;
								}
							}

							if ( isset( $link_meta['link_rss'] ) ) {
								$linkitem['link_rss'] = esc_url( $link_meta['link_rss'][0] );
							} else {
								$linkitem['link_rss'] = '';
							}

							include_once( ABSPATH . WPINC . '/feed.php' );

							if ( true == $debugmode ) {
								$starttimerssfeed = microtime ( true );
							}

							$current_link_array_items = get_transient( 'RSSLibraryLink' . get_the_ID() . 'RSSItems' );

							if ( false === $current_link_array_items && !empty( $linkitem['link_rss'] ) ) {
								$rss = fetch_feed( $linkitem['link_rss'] );
								if ( !is_wp_error( $rss ) ) {
									$maxitems = $rss->get_item_quantity( $rsslibraryitemspersite );

									$current_link_simplepie_objects = $rss->get_items( 0, $maxitems );

									if ( $current_link_simplepie_objects ) {
										foreach ( $current_link_simplepie_objects as $index => $item ) {
											$new_rss_item = array();
											
											$new_rss_item['pub_timestamp'] = strtotime( $item->get_date( 'F j, Y, g:i a' ) );
											$new_rss_item['site_name'] = $linkitem['link_name'];
											$new_rss_item['permalink'] = $item->get_permalink();
											$new_rss_item['title'] = $item->get_title();
											$new_rss_item['description'] = wp_trim_words( $item->get_description(), $rsslibrarymaxwordsitem );
											$new_rss_item['category_name'] = $linkitem['category_name'];

											$current_link_array_items[] = $new_rss_item;														
										}
									}
								} 

								set_transient( 'RSSLibraryLink' . get_the_ID() . 'RSSItems', $current_link_array_items, $rsscachedelay );
							} 

							if ( !empty( $current_link_array_items ) ) {
								foreach( $current_link_array_items as $array_item ) {
									$rss_array_items[] = $array_item;
								}
							}							
							
							if ( true == $debugmode ) {
								$current_cat_output .= "\n<!-- Time to render RSS Feed section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssfeed ) . " --> \n";
							}

						}

						$currentcategory = $currentcategory + 1;

						if ( $display_children && $cat_has_children ) {
							$libraryoptions['categorylist_cpt'] = '';
							if ( !empty( $childcategory_cpt ) && !empty( $childcategory_cpt[$link_category->term_id] ) ) {
								$childsortlist = $childcategory_cpt[$link_category->term_id];
								if ( !empty( $childsortlist ) ) {
									$childsortlist = str_replace( '-', ',', $childsortlist );
									$libraryoptions['categorylist_cpt'] = $childsortlist;
								}
							}

							RenderRSSLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $link_category->term_id, $level + 1, $display_children, $rsscount, $rss_array_items );
						}
					}

				} else {
					$output .= '<span class="nolinksfoundincat">' . __( 'No links found', 'link-library' ) . '</span>';
				}
			}
		} else {
			$output .= '<span class="nolinkstodisplay">' . __( 'All of your links must be assigned at least to one category to be displayed', 'link-library') . '</span>';
		}
	} else {
		$output .= '<span class="nolinksfoundallcats">' . __( 'No links found', 'link-library' ) . '</span>';
	}

	$currentcategory = $currentcategory + 1;

	if ( $level == 0 ) {
		$timestamp = array();
		foreach( $rss_array_items as $key => $row ) {
			$timestamp[$key] = $row['pub_timestamp'];
		}
		array_multisort( $timestamp, SORT_DESC, $rss_array_items );

		if ( isset( $_GET['rsslibrarypage'] ) ) {
			$current_page = intval( $_GET['rsslibrarypage'] );
		} else {
			$current_page = 1;
		}	
		
		$item_number = 0;
		foreach( $rss_array_items as $rss_array_item ) {
			$item_number++;
			if ( $rsslibrarypagination ) {
				$first_item = ( $current_page - 1 ) * $rsslibrarypaginationnb;
				$last_item =  $current_page * $rsslibrarypaginationnb;

				if ( $item_number <= $first_item || $item_number > $last_item ) {
					continue;
				}				
			}
			$template = stripslashes( $rsslibrarytemplate );
			$template = str_replace( '[link_category]', $rss_array_item['category_name'], $template );
			$template = str_replace( '[rss_item_title]', '<a target="_blank" href="' . $rss_array_item['permalink'] . '">'. $rss_array_item['title'] . '</a>', $template );
			$template = str_replace( '[link_title]', $rss_array_item['site_name'], $template );
			$template = str_replace( '[rss_item_date]', date( get_option('date_format'), $rss_array_item['pub_timestamp'] ), $template );
			$template = str_replace( '[rss_item_time]', date( get_option('time_format'), $rss_array_item['pub_timestamp'] ), $template );
			$template = str_replace( '[rss_item_content]', $rss_array_item['description'], $template );
			$output .= $template;			
		}

		if ( $rsslibrarypagination ) {
			$number_of_rss_items = count( $rss_array_items );
			$number_of_pages = ceil( $number_of_rss_items / $rsslibrarypaginationnb );					

			$output .= rss_library_display_pagination( $current_page - 1, $current_page + 1, $number_of_pages, $current_page );
		}

		$output .= '</div><!-- Div Rsslist -->';

		$output .= "\n<!-- End of RSS Library Output -->\n\n";
	}

	wp_reset_postdata();

	return do_shortcode( $output );
}
