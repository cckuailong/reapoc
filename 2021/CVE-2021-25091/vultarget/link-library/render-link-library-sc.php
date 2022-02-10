<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

/* Support functions to render output of link-library shortcode */

function link_library_add_http( $url ) {
	if ( !preg_match( '~^(?:f|ht)tps?://~i', $url ) ) {
		$url = 'http://' . $url;
	}
	return $url;
}

function link_library_highlight_phrase( $str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>' ) {
	if ( empty( $str ) ) {
		return '';
	}

	if ( !empty( $phrase ) ) {
		return preg_replace( '/(' . preg_quote( $phrase, '/') . '(?![^<]*>))/i', $tag_open . "\\1" . $tag_close, $str );
	}

	return $str;
}

function link_library_get_breadcrumb_path( $slug, $rewritepage, $level = 0 ) {
	$cat_path = '';

	$term = get_term_by( 'slug', $slug, 'link_library_category' );

	if ( !empty( $term ) ) {
		$parent_term = get_term_by( 'id', $term->parent, 'link_library_category' );
		if ( !empty( $parent_term ) ) {
			$cat_path .= link_library_get_breadcrumb_path( $parent_term->slug, $rewritepage, $level + 1 ) . ' - ';
		}
	}

	$new_link = esc_url( home_url() . '/' . $rewritepage . '/' . $slug );
	if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
		$new_link = add_query_arg( 'link_tags', $_GET['link_tags'], $new_link );
	}

	if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
		$new_link = add_query_arg( 'link_price', $_GET['link_price'], $new_link );
	}

	if ( $level != 0 ) {
		$cat_path .= '<a href="' . $new_link . '">' . $term->name . '</a>';		
	} elseif ( $level == 0 ) {
		$cat_path .= $term->name;
		$new_top_link = esc_url( home_url() . '/' . $rewritepage );

		if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
			$new_top_link = add_query_arg( 'link_tags', $_GET['link_tags'], $new_top_link );
		}
		if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
			$new_top_link = add_query_arg( 'link_price', $_GET['link_price'], $new_top_link );
		}

		$cat_path = '<a href="' . $new_top_link .  '">Home</a> - ' . $cat_path;
	}

	return $cat_path;
}

function link_library_display_pagination( $previouspagenumber, $nextpagenumber, $numberofpages, $pagenumber,
	$showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter ) {

	$dotbelow = false;
	$dotabove = false;
	$paginationoutput = '';

	if ( isset( $_GET ) ) {
		$incomingget = $_GET;
		unset ( $incomingget['page_id'] );
		unset ( $incomingget['linkresultpage'] );
		unset ( $incomingget['cat_id'] );
		unset ( $incomingget['catletter'] );
	}

	if ( 1 < $numberofpages ) {
		$paginationoutput = '<div class="pageselector"><!-- Div Pagination -->';

		if ( 1 != $pagenumber ) {
			$paginationoutput .= '<span class="previousnextactive">';

			if ( !$showonecatonly ) {
				if ( 'AJAX' == $showonecatmode ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . '</a>';
				} else {
					global $page_query;
					$argumentarray = array( 'linkresultpage' => $previouspagenumber );

					if ( ! empty( $currentcatletter ) ) {
						$argumentarray['catletter'] = $currentcatletter;
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Previous', 'link-library' ) . '</a>';
				}
			} elseif ( $showonecatonly ) {
				if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $previouspagenumber . ");return false;\" >" . __('Previous', 'link-library') . '</a>';
				} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
					if ( 'HTMLGET' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'cat_id' => $AJAXcatid );
					} elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'catname' => $AJAXcatid );
					} else {
						$argumentarray = array ( 'linkresultpage' => $previouspagenumber, 'cat' => $AJAXcatid );
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '" >' . __('Previous', 'link-library') . '</a>';
				}
			}

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
					$paginationoutput .= '<span class="selectedpage">' . $counter . '</span>';
					continue;
				}

				if ( !$showonecatonly ) {
					if ( 'AJAX' == $showonecatmode ) {
						$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . '</a>';
					} else {
						$argumentarray = array( 'linkresultpage' => $counter );

						if ( ! empty( $currentcatletter ) ) {
							$argumentarray['catletter'] = $currentcatletter;
						}

						$argumentarray = array_merge( $argumentarray, $incomingget );
						$targetaddress = esc_url( add_query_arg( $argumentarray ) );

						$paginationoutput .= '<a href="' . $targetaddress . '">' . $counter . '</a>';
					}
				} elseif ( $showonecatonly ) {
					if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
						$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $counter . ");return false;\" >" . $counter . '</a>';
					} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
						if ( 'HTMLGET' == $showonecatmode ) {
							$argumentarray = array ( 'linkresultpage' => $counter, 'cat_id' => $AJAXcatid );
						} elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
							$argumentarray = array ( 'linkresultpage' => $counter, 'catname' => $AJAXcatid );
						} else {
							$argumentarray = array ( 'linkresultpage' => $counter, 'cat' => $AJAXcatid );
						}

						$argumentarray = array_merge( $argumentarray, $incomingget );
						$targetaddress = esc_url( add_query_arg( $argumentarray ) );

						$paginationoutput .= '<a href="' . $targetaddress . '" >' . $counter . '</a>';
					}
				}

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

			if ( !$showonecatonly ) {
				if ( 'AJAX' == $showonecatmode ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . '</a>';
				} else {
					$argumentarray = array( 'page_id' => $pageID, 'linkresultpage' => $nextpagenumber );

					if ( ! empty( $currentcatletter ) ) {
						$argumentarray['catletter'] = $currentcatletter;
					}

					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '">' . __( 'Next', 'link-library' ) . '</a>';
				}
			} elseif ( $showonecatonly ) {
				if ( 'AJAX' == $showonecatmode || empty( $showonecatmode ) ) {
					$paginationoutput .= "<a href=\"#\" onClick=\"showLinkCat" . $settings . "('" . $AJAXcatid . "', '" . $settings . "', " . $nextpagenumber . ");return false;\" >" . __('Next', 'link-library') . '</a>';
				} elseif ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode || 'HTMLGETPERM' == $showonecatmode ) {
					if ( 'HTMLGET' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $nextpagenumber, 'cat_id' => $AJAXcatid );
					} elseif ( 'HTMLGETCATNAME' == $showonecatmode ) {
						$argumentarray = array ( 'linkresultpage' => $nextpagenumber, 'catname' => $AJAXcatid );
					} else {
						$argumentarray = array ( 'linkresultpage' => $nextpagenumber, 'cat' => $AJAXcatid );
					}
					
					$argumentarray = array_merge( $argumentarray, $incomingget );
					$targetaddress = esc_url( add_query_arg( $argumentarray ) );

					$paginationoutput .= '<a href="' . $targetaddress . '" >' . __('Next', 'link-library') . '</a>';
				}

			}

			$paginationoutput .= '</span>';
		} else {
			$paginationoutput .= '<span class="previousnextinactive">' . __('Next', 'link-library') . '</span>';
		}

		$paginationoutput .= '</div><!-- Div Pagination -->';
	}

	if ( 'AJAX' == $showonecatmode ) {
		$nonce = wp_create_nonce( 'link_library_ajax_refresh' );

		$paginationoutput .= "<script type=\"text/javascript\">\n";
		$paginationoutput .= "var ajaxobject;\n";
		$paginationoutput .= "if(typeof showLinkCat" . $settings . " !== 'function'){\n";
		$paginationoutput .= "window.showLinkCat" . $settings . " = function ( _incomingID, _settingsID, _pagenumber, _searchll ) {\n";
		$paginationoutput .= "if (typeof(ajaxobject) != \"undefined\") { ajaxobject.abort(); }\n";

		$paginationoutput .= "\tjQuery('#contentLoading" . $settings . "').toggle();" .
		                     "jQuery.ajax( {" .
		                     "    type: 'POST', " .
		                     "    url: '" . admin_url( 'admin-ajax.php' ) . "', " .
		                     "    data: { action: 'link_library_ajax_update', " .
		                     "            _ajax_nonce: '" . $nonce . "', " .
		                     "            id : _incomingID, " .
		                     "            settings : _settingsID, " .
		                     "            ajaxupdate : true, " .
		                     "            searchll : _searchll, " .
		                     "            linkresultpage: _pagenumber }, " .
		                     "    success: function( data ){ " .
		                     "            jQuery('#linklist" . $settings. "').html( data ); " .
		                     "            jQuery('#contentLoading" . $settings . "').toggle();\n" .
		                     "            } } ); ";
		$paginationoutput .= "}\n";
		$paginationoutput .= "}\n";

		$paginationoutput .= "</script>\n\n";
	}

	return $paginationoutput;
}

/**
 *
 * Render the output of the link-library shortcode
 *
 * @param $LLPluginClass    Link Library main plugin class
 * @param $generaloptions   General Plugin Settings
 * @param $libraryoptions   Selected library settings array
 * @param $settings         Settings ID
 * @return                  List of categories output for browser
 */

function RenderLinkLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $onlycount = 'false', $parent_cat_id = 0, $level = 0, $display_children = true, $hide_children_cat_links = false, &$linkcount ) {

	$showonecatonly = '';
	$showonecatmode = '';
	$AJAXcatid = '';

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );
	extract( $libraryoptions );

	remove_filter('posts_request', 'relevanssi_prevent_default_request');
	remove_filter('the_posts', 'relevanssi_query', 99);

	global $wp_query;

	if ( $level == 0 && ( ( isset( $_GET['cat_name'] ) && !empty( $_GET['cat_name'] ) ) || ( isset( $wp_query->query_vars['cat_name'] ) && !empty( $wp_query->query_vars['cat_name'] ) ) ) ) {
		if ( !empty( $_GET['cat_name'] ) ) {
			$category_entry = get_term_by( 'slug', $_GET['cat_name'], 'link_library_category', OBJECT );
		} elseif ( !empty( $wp_query->query_vars['cat_name'] ) ) {
			$last_slash_pos = strripos( $wp_query->query_vars['cat_name'], '/' );
			if ( $last_slash_pos != 0 ) {
				$cat_string = substr( $wp_query->query_vars['cat_name'], $last_slash_pos );
			} else {
				$cat_string = $wp_query->query_vars['cat_name'];
			}

			$category_entry = get_term_by( 'slug', $cat_string, 'link_library_category', OBJECT );
		}

		if ( !empty( $category_entry ) ) {
			$AJAXcatid = $category_entry->term_id;
			$parent_cat_id = $AJAXcatid;
		}
	}

	if ( 0 == $parent_cat_id && $hidechildcatlinks ) {
		$hide_children_cat_links = $hidechildcatlinks;
	}

	if ( 0 == $parent_cat_id && $hidechildcattop ) {
		$display_children = false;
	}

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

	$linkeditoruser = current_user_can( 'manage_options' );

	if ( $level == 0 ) {
		$output = "\n<!-- Beginning of Link Library Output -->\n\n";
	} else {
		$output = '';
	}

	$currentcategory = 1;
	$pagenumber = 1;
	$currentcatletter = '';
	$number_of_pages = 1;
	$categoryname = '';
	$mode = 'normal';

	$AJAXnocatset = false;
	if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && empty( $AJAXcatid ) ) {
		$AJAXnocatset = true;
	}

	$GETnocatset = false;
	if ( $showonecatonly && ( 'HTMLGET' == $showonecatmode || 'HTMLGETSLUG' == $showonecatmode || 'HTMLGETCATNAME' == $showonecatmode ) ) {
		if ( 'HTMLGET' == $showonecatmode && ( !isset( $_GET['cat_id'] ) || ( isset( $_GET['cat_id'] ) && empty( $_GET['cat_id'] ) ) ) ) {
			$GETnocatset = true;
		} elseif ( 'HTMLGETSLUG' == $showonecatmode && ( !isset( $_GET['catslug'] ) || ( isset( $_GET['catslug'] ) && empty( $_GET['catslug'] ) ) ) ) {
			$GETnocatset = true;
		} elseif ( 'HTMLGETCATNAME' == $showonecatmode && ( !isset( $_GET['catname'] ) || ( isset( $_GET['catname'] ) && empty( $_GET['catname'] ) ) ) ) {
			$GETnocatset = true;
		}

	}

	if ( $showonecatonly && 'AJAX' == $showonecatmode && isset( $AJAXcatid ) && !empty( $AJAXcatid ) && ( !isset( $_GET['searchll'] ) || empty( $_GET['searchll'] ) || ( $searchfiltercats && isset( $_POST['searchll'] ) ) ) ) {
		$categorylist_cpt = $AJAXcatid;
	} elseif ( ( $showonecatonly && 'HTMLGET' == $showonecatmode && isset( $_GET['cat_id'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) || ( $searchfiltercats && isset( $_GET['cat_id'] ) && isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) ) {
		$categorylist_cpt = intval( $_GET['cat_id'] );
		$AJAXcatid = $categorylist_cpt;
	} elseif ( ( $showonecatonly && 'HTMLGETSLUG' == $showonecatmode && isset( $_GET['catslug'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) || ( $searchfiltercats && isset( $_GET['catslug'] ) && isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) ) {
		$categorysluglist = $_GET['catslug'];
	} elseif ( ( $showonecatonly && 'HTMLGETCATNAME' == $showonecatmode && isset( $_GET['catname'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) || ( $searchfiltercats && isset( $_GET['catname'] ) && isset( $_GET['searchll'] ) && !empty( $_GET['searchll'] ) ) ) {
		$categorynamelist = $_GET['catname'];
	} elseif ( $showonecatonly && 'HTMLGETPERM' == $showonecatmode && empty( $_GET['searchll'] ) ) {
		global $wp_query;

		$categoryname = '';
		if ( isset( $wp_query->query_vars['cat_name'] ) ) {
			$categoryname = $wp_query->query_vars['cat_name'];
		}

		$AJAXcatid = $categoryname;
		$categorysluglist = '';
		if ( isset( $_GET['catslug'] ) ) {
			$categorysluglist = $_GET['catslug'];
		}
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && !empty( $defaultsinglecat_cpt ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorylist_cpt = $defaultsinglecat_cpt;
		$AJAXcatid = $categorylist_cpt;
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && isset( $_GET['cat_id'] ) && !empty( $_GET['cat_id'] ) && ( !isset( $_GET['searchll'] ) || ( isset( $_GET['searchll'] ) && empty( $_GET['searchll'] ) ) ) ) {
		$categorylist_cpt = intval( $_GET['cat_id'] );
		$AJAXcatid = $categorylist_cpt;
		$defaultsinglecat = $AJAXcatid;
	} elseif ( $showonecatonly && ( !isset( $AJAXcatid ) || empty( $AJAXcatid ) ) && empty( $defaultsinglecat_cpt ) && empty( $_GET['searchll'] ) ) {

		$show_one_cat_query_args = array( );

		if ( $hide_if_empty ) {
			$show_one_cat_query_args['hide_empty'] = true;
		} else {
			$show_one_cat_query_args['hide_empty'] = false;
		}

		if ( !$showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_only_publish', 10, 3 );
		} elseif ( $showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending', 10, 3 );
		} elseif ( !$showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft', 10, 3 );
		} elseif ( $showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending', 10, 3 );
		}

		if ( !empty( $categorylist_cpt ) ) {
			$show_one_cat_query_args['include'] = explode( ',', $categorylist_cpt );
		}

		if ( !empty( $excludecategorylist_cpt ) ) {
			$show_one_cat_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
		}

		if ( ( !empty( $categorysluglist ) || isset( $_GET['catslug'] ) ) && empty( $singlelinkid ) ) {
			if ( !empty( $categorysluglist ) ) {
				$show_one_cat_query_args['slug'] = explode( ',', $categorysluglist );
			} elseif ( isset( $_GET['catslug'] ) ) {
				$show_one_cat_query_args['slug'] = isset( $_GET['catslug'] );
			}

		}

		if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) ) {
			$show_one_cat_query_args['slug'] = $categoryname;
		}

		if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) ) {
			$show_one_cat_query_args['name'] = explode( ',', urldecode( $categorynamelist ) );
		}

		if ( 'name' == $order ) {
			$show_one_cat_query_args['orderby'] = 'name';
			$show_one_cat_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'id' == $order ) {
			$show_one_cat_query_args['orderby'] = 'id';
			$show_one_cat_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		}

		$show_one_cat_query_args['taxonomy'] = 'link_library_category';

		$show_one_cat_link_categories = get_terms( $show_one_cat_query_args );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_only_publish' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending' );

		$mode = 'normal';

		if ( $debugmode ) {
			$output .= "\n<!-- AJAX Default Category Query: " . print_r( $show_one_cat_query_args, TRUE ) . "-->\n\n";
			$output .= "\n<!-- AJAX Default Category Results: " . print_r( $show_one_cat_link_categories, TRUE ) . "-->\n\n";
		}

		if ( $show_one_cat_link_categories ) {
			$categorylist_cpt = $show_one_cat_link_categories[0]->term_id;
			$AJAXcatid = $categorylist_cpt;
		}
	}

	$searchstring = '';
	$searchterms = '';

	if ( ( isset($_GET['searchll'] ) && !empty( $_GET['searchll'] ) || ( isset( $_POST['searchll'] ) && !empty( $_POST['searchll'] ) ) ) && empty( $singlelinkid ) ) {
		if ( isset( $_GET['searchll'] ) ) {
			$searchstring = $_GET['searchll'];
		} elseif ( isset( $_POST['searchll'] ) ) {
			$searchstring = $_POST['searchll'];
		}

		$searchstringcopy = $searchstring;
		$searchterms  = array();

		$offset = 0;
		while ( false !== strpos( $searchstringcopy, '"', $offset ) ) {
			if ( 0 == $offset ) {
				$offset = strpos( $searchstringcopy, '"' );
			} else {
				$endpos        = strpos( $searchstringcopy, '"', $offset + 1 );
				$searchterms[] = substr( $searchstringcopy, $offset + 1, $endpos - $offset - 2 );
				$strlength     = ( $endpos + 1 ) - ( $offset + 1 );
				$searchstringcopy  = substr_replace( $searchstringcopy, '', $offset - 1, $endpos + 2 - ( $offset ) );
				$offset        = 0;
			}
		}

		if ( ! empty( $searchstringcopy ) ) {
			$searchterms = array_merge( $searchterms, explode( " ", $searchstringcopy ) );
		}

		if ( !empty( $searchstring ) ) {
			$mode = 'search';
			$showlinksonclick = false;
		}
	}

	$link_count = wp_count_posts( 'link_library_links' );

	if ( isset( $link_count ) && !empty( $link_count ) && ( $link_count->publish > 0 || ( $showinvisible && $link_count->private > 0 ) || ( $showuserlinks && $link_count->pending ) ) ) {
		$currentcatletter = '';

		if ( $level == 0 && $cat_letter_filter != 'no' ) {
			require_once plugin_dir_path( __FILE__ ) . 'render-link-library-alpha-filter.php';
			$result = RenderLinkLibraryAlphaFilter( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $mode );

			$currentcatletter = $result['currentcatletter'];

			if ( 'beforelinks' == $cat_letter_filter || 'beforecatsandlinks' == $cat_letter_filter ) {
				$output .= $result['output'];
			}
		}

		$link_categories_query_args = array( );

		if ( $hide_if_empty ) {
			$link_categories_query_args['hide_empty'] = true;
		} else {
			$link_categories_query_args['hide_empty'] = false;
		}

		if ( !$showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_only_publish', 10, 3 );
		} elseif ( $showuserlinks && !$showinvisible && !$showinvisibleadmin ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending', 10, 3 );
		} elseif ( !$showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft', 10, 3 );
		} elseif ( $showuserlinks && ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) ) {
			add_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending', 10, 3 );
		}

		if ( ( !empty( $categorylist_cpt ) || isset( $_GET['cat_id'] ) ) && empty( $singlelinkid ) && ( 'search' != $mode || false == $searchfromallcats ) && ( $level == 0 || ( $level > 0 && !empty( $categorylist_cpt ) ) ) ) {
			$link_categories_query_args['include'] = explode( ',', $categorylist_cpt );
		}

		if ( !empty( $excludecategorylist_cpt ) && empty( $singlelinkid ) && ( 'search' != $mode || false == $searchfromallcats ) ) {
			$link_categories_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
		}

		if ( ( !empty( $categorysluglist ) || isset( $_GET['catslug'] ) ) && empty( $singlelinkid ) && ( 'search' != $mode || false == $searchfromallcats ) ) {
			if ( !empty( $categorysluglist ) ) {
				$link_categories_query_args['slug'] = explode( ',', $categorysluglist );
			} elseif ( isset( $_GET['catslug'] ) ) {
				$link_categories_query_args['slug'] = $_GET['catslug'];
			}
			$link_categories_query_args['include'] = array();
			$link_categories_query_args['exclude'] = array();
		}

		if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) && ( 'search' != $mode || false == $searchfromallcats ) ) {
			$link_categories_query_args['slug'] = $categoryname;
		}

		if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) && ( 'search' != $mode || false == $searchfromallcats ) ) {
			$link_categories_query_args['name'] = explode( ',', urldecode( $categorynamelist ) );
		}

		if ( 'name' == $order ) {
			$link_categories_query_args['orderby'] = 'name';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'id' == $order ) {
			$link_categories_query_args['orderby'] = 'id';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		} elseif ( 'slug' == $order ) {
			$link_categories_query_args['orderby'] = 'slug';
			$link_categories_query_args['order'] = in_array( $direction, $validdirections ) ? $direction : 'ASC';
		}

		if ( isset( $AJAXcatid ) && !empty( $AJAXcatid ) ) {
			$link_categories_query_args['include'] = $AJAXcatid;
		} elseif ( empty( $link_categories_query_args['slug'] ) ) {
			$no_sub_cat = true;
			if ( !empty( $link_categories_query_args['include'] ) ) {
				foreach ( $link_categories_query_args['include'] as $include_cat ) {
					$cat_term = get_term_by( 'id', $include_cat, 'link_library_category' );
					if ( !empty( $cat_term ) ) {
						if ( $cat_term->parent != 0 && $level == 0 ) {
							$no_sub_cat = false;
						}
					}
				}
			}
			if ( $no_sub_cat ) {
				$link_categories_query_args['parent'] = $parent_cat_id;
			}
		}

		$link_categories = get_terms( 'link_library_category', $link_categories_query_args );

		remove_filter( 'get_terms', 'link_library_get_terms_filter_only_publish' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft' );
		remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending' );

		if ( 'catlist' == $order && is_array( $link_categories ) && !empty( $link_categories_query_args['include'] ) ) {
			$temp_link_categories = $link_categories;
			$link_categories = array();
			$exploded_include_list = explode( ',', $categorylist_cpt );
			foreach ( $exploded_include_list as $sort_link_category_id ) {
				foreach ( $temp_link_categories as $temp_link_cat ) {
					if ( $sort_link_category_id == $temp_link_cat->term_id ) {
						$link_categories[] = $temp_link_cat;
						continue;
					}
				}
			}
		}

		if ( !empty( $currentcatletter ) && $cat_letter_filter != 'no' ) {
			foreach ( $link_categories as $index => $link_category ) {
				if ( substr( $link_category->name, 0, 1) != $currentcatletter ) {
					unset( $link_categories[$index] );
				}
			}
		}

		if ( $pagination && 'search' != $mode ) {
			if ($linksperpage == 0 || empty( $linksperpage ) ) {
				$linksperpage = 5;
			}

			$number_of_links = 0;

			if ( !empty( $taglist_cpt ) || ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {

				$tag_array = array();

				if ( ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) ) {
					$tag_array = explode( '.', $_GET['link_tags'] );
				} elseif( !empty( $taglist_cpt ) ) {
					$tag_array = explode( ',', $taglist_cpt );
				}				
			}

			foreach ( $link_categories as $cat_array_index => $link_category ) {

				$args = array(
					'post_type' => 'link_library_links',
					'tax_query' => array( 
						array(
							'taxonomy'  => 'link_library_category',
							'field'     => 'term_id',
							'terms'     => $link_category->term_id
						)
					),
					'numberposts' => '-1'
				);

				if ( !empty( $tag_array ) ) {
					$args['tax_query'][] = array( 
						array(
							'taxonomy'  => 'link_library_tags',
							'field'     => 'slug',
							'terms'     => $tag_array
						)
					);
				}

				if ( isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) && sizeof( $args['tax_query'] ) > 1 ) {
					$args['tax_query']['relation'] = 'AND';
				}

				$posts_array = get_posts( $args );				

				$number_of_links += sizeof( $posts_array );
				$link_categories[$cat_array_index]->count = sizeof( $posts_array );
			}

			if ( $number_of_links > $linksperpage ) {
				$nextpage = true;
			} else {
				$nextpage = false;
			}

			if ( isset( $number_of_links ) ) {
				$preroundpages = $number_of_links / $linksperpage;
				$number_of_pages = ceil( $preroundpages * 1 ) / 1;
			}

			if ( isset( $_POST['linkresultpage'] ) || isset( $_GET['linkresultpage'] ) ) {

				if ( isset( $_POST['linkresultpage'] ) ) {
					$pagenumber = $_POST['linkresultpage'];
				} elseif ( isset( $_GET['linkresultpage'] ) ) {
					$pagenumber = $_GET['linkresultpage'];
				}
				$startingitem = ( $pagenumber - 1 ) * $linksperpage + 1;
			} else {
				$pagenumber = 1;
				$startingitem = 1;
			}
		}

		if ( $level == 0 ) {
			$output .= "<div id='linklist" . $settings . "' class='linklist";

			if ( 'categorymasonrygrid' == $displayastable ) {
				$output .= ' grid';
			}
			
			$output .= "'><!-- Div Linklist -->\n";
		}

		if ( $level == 0 && $pagination && $mode != "search" && 'BEFORE' == $paginationposition ) {
			$previouspagenumber = $pagenumber - 1;
			$nextpagenumber = $pagenumber + 1;

			$pageID = get_queried_object_id();

			if ( empty( $AJAXcatid ) && !empty( $categorysluglist ) ) {
				$AJAXcatid = $categorysluglist;
			}
			if ( empty( $AJAXcatid ) && !empty( $categorynamelist ) ) {
				$AJAXcatid = $categorynamelist;
			}

			$output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $number_of_pages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
		}

		if ( $level == 0 && 'search' == $mode ) {
			$output .= '<div class="resulttitle">' . __('Search Results for', 'link-library') . ' "' . esc_html( stripslashes( $searchstring ) ) . '"</div><!-- Div search results title -->';
		}

		if ( $enablerewrite && !empty( $toppagetext ) && $parent_cat_id == 0 ) {
			$output .= '<div class="toppagetext">' . nl2br( $toppagetext ) . '</div>';
		}

		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );

		if ( !empty( $link_categories ) ) {
			foreach ( $link_categories as $link_category ) {
				if ( !empty( $maxlinks ) && is_numeric( $maxlinks ) && 0 < $maxlinks && $linkcount > $maxlinks ) {
					break;
				}

				if ( $enablerewrite && $showbreadcrumbspermalinks && $parent_cat_id != 0 && $level == 0) {
					$breadcrumb = '<div class="breadcrumb">' . link_library_get_breadcrumb_path( $link_category->slug, $rewritepage ) . '</div>';
					$output .= $breadcrumb;
				}

				if ( $pagination && 'search' != $mode && !$combineresults ) {
					if ( $linkcount + $link_category->count - 1 < $startingitem || $linkcount > $startingitem + $linksperpage - 1 ) {
						$linkcount = $linkcount + $link_category->count;
						continue;
					}
				}

				if ( !empty( $singlelinkid ) && intval( $singlelinkid ) && $linkcount > 1 ) {
					break;
				}

				$link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => -1 );

				if ( !$combineresults ) {
					$link_query_args['tax_query'][] =
						array(
							'taxonomy' => 'link_library_category',
							'field'    => 'term_id',
							'terms'    => $link_category->term_id,
							'include_children' => false

						);
				}

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

				if ( !empty( $singlelinkid ) && intval( $singlelinkid ) ) {
					$link_query_args['p'] = $singlelinkid;
				}

				$link_query_args['post_status'] = array( 'publish' );

				if ( $showuserlinks ) {
					$link_query_args['post_status'][] = 'pending';
				}

				if ( $showinvisible || ( $showinvisibleadmin && $linkeditoruser ) ) {
					$link_query_args['post_status'][] = 'draft';
				}

				if ( $showscheduledlinks ) {
					$link_query_args['post_status'][] = 'future';
				}

				if ( !empty( $searchstring ) ) {
					add_filter( 'posts_search', 'll_expand_posts_search', 10, 2 );
					$link_query_args['s'] = $searchstring;
				}

				if ( !empty( $customqueryarg ) && !empty( $customqueryargvalue ) ) {
					$link_query_args[$customqueryarg] = $customqueryargvalue;
				}				

				if ( isset( $_GET['linkname'] ) && in_array( $_GET['linkname'], array( 'ASC', 'DESC' ) ) ) {
					$linkorder = 'name';
					$linkdirection = $_GET['linkname'];
				} elseif ( isset( $_GET['linkprice'] ) && in_array( $_GET['linkprice'], array( 'ASC', 'DESC' ) ) ) {
					$linkorder = 'price';
					$linkdirection = $_GET['linkprice'];
				}

				if ( $featuredfirst && 'random' != $linkorder ) {
					$link_query_args['meta_query']['link_featured_clause'] = array( 'key' => 'link_featured' );
					$link_query_args['orderby']['meta_value_num'] = 'DESC';
				}

				if ( 'name' == $linkorder ) {
					$link_query_args['orderby']['title'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'id' == $linkorder ) {
					$link_query_args['orderby']['ID'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'date' == $linkorder ) {
					$link_query_args['meta_query']['link_updated_clause'] = array( 'key' => 'link_updated' );
					$link_query_args['orderby']['link_updated_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'pubdate' == $linkorder ) {
					$link_query_args['orderby'] = 'publish_date';
					$link_query_args['order'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'price' == $linkorder ) {
					$link_query_args['meta_query']['link_price_clause'] = array( 'key' => 'link_price' );
					$link_query_args['orderby']['link_price_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'random' == $linkorder ) {
					$link_query_args['orderby'] = 'rand';
				} elseif ( 'hits' == $linkorder ) {
					$link_query_args['meta_query']['link_visits_clause'] = array( 'key' => 'link_visits', 'type' => 'numeric' );
					$link_query_args['orderby']['link_visits_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'uservotes' == $linkorder ) {
					$link_query_args['meta_query']['link_votes_clause'] = array( 'key' => '_thumbs_rating_up', 'type' => 'numeric' );
					$link_query_args['orderby']['link_votes_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( 'scpo' == $linkorder ) {
					$link_query_args['orderby']['menu_order'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
				} elseif ( false !== strpos( $linkorder, 'customtext' ) ) {
					$customtextid = substr( $linkorder, 10 );

					if ( is_integer( intval( $customtextid ) ) ) {
						$customtextactivevar = 'customtext' . $customtextid . 'active';
						if ( $$customtextactivevar ) {
							$link_query_args['meta_query']['custom_text_clause'] = array( 'key' => 'link_custom_text_' . $customtextid, 'type' => 'char' );
							$link_query_args['orderby']['custom_text_clause'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
						} else {
							$link_query_args['orderby']['title'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
						}
					} else {
						$link_query_args['orderby']['title'] = in_array( $linkdirection, $validdirections ) ? $linkdirection : 'ASC';
					}
				}

				if ( $current_user_links ) {
					$user_data = wp_get_current_user();
					$name_field_value = $user_data->display_name;

					$link_query_args['meta_query']['link_submitter_clause'] =
						array(
							'key'     => 'link_submitter',
							'value'   => $name_field_value,
							'compare' => '=',
						);
				}

				if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
					$link_query_args['meta_query'][] =
						array(
							'key'     => 'link_price',
							'value'   => floatval( 0.0 ),
							'compare' => '=',
						);
				}

				if ( $showupdatedonly ) {
					$link_query_args['date_query'] = array(
						array(
							'after' => '-' . $updateddays . ' days',
							'column' => 'post_date',
						),
					);
				}

				if ( isset( $_GET['link_letter'] ) && !empty( $_GET['link_letter'] ) ) {
					$link_query_args['link_starts_with'] = $_GET['link_letter'];
				}

				if ( true == $debugmode ) {
					$linkquerystarttime = microtime ( true );
				}

				if ( $combineresults && !empty( $maxlinks ) && 0 < intval( $maxlinks ) ) {
					$link_query_args['posts_per_page'] = intval ( $maxlinks );
				} elseif ( !empty( $maxlinkspercat ) && 0 < intval( $maxlinkspercat ) ) {
					$link_query_args['posts_per_page'] = intval ( $maxlinkspercat );
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

				if ( $debugmode ) {
					$output .= '<!-- showonecatmode: ' . $showonecatonly . ', AJAXnocatset: ' . $AJAXnocatset . ', nocatonstartup: ' . $nocatonstartup . '-->';
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
				if ( ( $the_link_query->found_posts && $showonecatonly && ( ( 'AJAX' == $showonecatmode && $AJAXnocatset ) || ( 'AJAX' != $showonecatmode && $GETnocatset ) ) && $nocatonstartup && empty( $searchstring ) ) || ( 0 == $the_link_query->found_posts && $nocatonstartup && empty( $searchstring ) ) ) {
					$output .= "<div id='linklist" . $settings . "' class='linklist'>\n";
					$output .= '</div><!-- Div empty list -->';
				} elseif ( ( $the_link_query->found_posts || !$hide_if_empty || $cat_has_children ) ) {
					if ( ( $the_link_query->have_posts() || !$hide_if_empty || $cat_has_children ) && ( empty( $maxlinks ) || 0 == $maxlinks | $linkcount <= $maxlinks ) ) {
						$current_cat_output = '';
						$start_link_count = $linkcount;
						if ( ! $combineresults ) {
							$currentcategoryid = $link_category->term_id;
							$current_cat_output .= '<div class="LinkLibraryCat LinkLibraryCat' . $currentcategoryid;
							
							if ( 'categorymasonrygrid' == $displayastable ) {
								$current_cat_output .= ' ll-grid-item ';
							}
							
							$current_cat_output .=  ( $level == 0 ? '' : ' childlevel'). ' level' . $level .'"><!-- Div Category -->';

							$catlink = '';
							$cattext = '';
							$catenddiv = '';

							if ( 1 == $catlistwrappers && !empty( $beforecastlist1 ) ) {
								$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
							} else if ( $catlistwrappers == 2 && !empty( $beforecatlist2 ) && !empty( $beforecatlist1 ) ) {
								$remainder = $currentcategory % $catlistwrappers;
								switch ( $remainder ) {

									case 0:
										$current_cat_output .= '<div class="' . $beforecatlist2 . '">';
										break;

									case 1:
										$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
										break;
								}
							} else if ( 3 == $catlistwrappers && !empty( $beforecatlist3 ) && !empty( $beforecatlist2 ) && !empty( $beforecatlist1 )) {
								$remainder = $currentcategory % $catlistwrappers;
								switch ( $remainder ) {

									case 0:
										$current_cat_output .= '<div class="' . $beforecatlist3 . '">';
										break;

									case 2:
										$current_cat_output .= '<div class="' . $beforecatlist2 . '">';
										break;

									case 1:
										$current_cat_output .= '<div class="' . $beforecatlist1 . '">';
										break;
								}
							}

							// Display the category name
							if ( !$hidecategorynames || empty( $hidecategorynames ) ) {
								$caturl = get_term_meta( $link_category->term_id, 'linkcaturl', true );

								if ( $catanchor ) {
									$cattext = '<div id="' . $link_category->slug . '"><!-- Div Category Name -->';
								} else {
									$cattext = '';
								}

								if ( !$divorheader ) {
									if ( 'search' == $mode ) {
										foreach ( $searchterms as $searchterm ) {
											$link_category->name = link_library_highlight_phrase( $link_category->name, $searchterm, '<span class="highlight_word">', '</span>' );
										}
									}

									$catlink = '<div class="' . $catnameoutput . '"><!-- Div Cat Name -->';

									if ( 'right' == $catdescpos || 'aftercatname' == $catdescpos || 'aftertoplevelcatname' == $catdescpos || empty( $catdescpos ) ) {
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										} /* elseif ( $catlinkspermalinksmode ) {
											// Generating cat link
										} */
										$catlink .= '<span class="linklistcatclass">' . $link_category->name . '</span>';
										if ( !empty( $caturl && $catnamelink ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showcategorydesclinks && ( 'left' == $catdescpos || 'right' == $catdescpos ) ) {
										$catlink .= '<span class="linklistcatnamedesc">';
										$linkitem['description'] = str_replace( '[', '<', $link_category->description );
										$linkitem['description'] = str_replace( ']', '>', $linkitem['description'] );
										$catlink .= $linkitem['description'];
										$catlink .= '</span>';
									}

									if ( 'left' == $catdescpos ) {
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										}
										$catlink .= '<span class="linklistcatclass">' . $link_category->name . '</span>';
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '</a>';
										}
									}

									if ( $showlinksonclick && ( $the_link_query->found_posts > 0 || $cat_has_children ) ) {
										$catlink .= '<span class="expandlinks" id="LinksInCat' . $link_category->term_id . '"';

										if ( $cat_has_children ) {
											$catlink .= ' data-subcat="Cat' . $link_category->term_id . 'SubCategories"';
										}

										$catlink .= '>';
										$catlink .= '<img class="arrow-down" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '<img class="arrow-up" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/collapse-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '</span>';
									}

									$catlink .= '</div><!-- DivOrHeader -->';
								} else if ( $divorheader ) {
									if ( 'search' == $mode ) {
										foreach ( $searchterms as $searchterm ) {
											$link_category->name = link_library_highlight_phrase( $link_category->name, $searchterm, '<span class="highlight_word">', '</span>' );
										}
									}

									$catlink = '<'. $catnameoutput . '>';

									if ( 'right' == $catdescpos || 'aftercatname' == $catdescpos || 'aftertoplevelcatname' == $catdescpos || empty( $catdescpos ) ) {
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ). '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										} elseif ( $catlinkspermalinksmode && !empty( $rewritepage ) ) {
											$cat_path = $link_category->slug;

											if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
												$cat_path = add_query_arg( 'link_tags', $_GET['link_tags'], $cat_path );
											}

											if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
												$cat_path = add_query_arg( 'link_price', $_GET['link_price'], $cat_path );
											}

											$catlink .= '<a href="' . esc_url( site_url() . '/' . $rewritepage . $cat_path ) . '">';
										}
										$catlink .= '<span class="linklistcatclass">' . $link_category->name . '</span>';
										if ( ( !empty( $caturl ) && $catnamelink ) || ( $catlinkspermalinksmode && !empty( $rewritepage ) ) ) {
											$catlink .= '</a>';
										}
									}

									if ( $showcategorydesclinks && ( 'left' == $catdescpos || 'right' == $catdescpos ) ) {
										$catlink .= '<span class="linklistcatnamedesc">';
										$linkitem['description'] = str_replace( '[', '<', $link_category->description );
										$linkitem['description'] = str_replace(']', '>', $linkitem['description'] );
										$catlink .= $linkitem['description'];
										$catlink .= '</span>';
									}

									if ( 'left' == $catdescpos ) {
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '<a href="' . link_library_add_http( $caturl ) . '" ';

											if ( !empty( $linktarget ) )
												$catlink .= ' target="' . $linktarget . '"';

											$catlink .= '>';
										}
										$catlink .= '<span class="linklistcatclass">' . $link_category->name . '</span>';
										if ( !empty( $caturl ) && $catnamelink ) {
											$catlink .= '</a>';
										}
									}

									if ( $showlinksonclick && $the_link_query->found_posts > 0 ) {
										$catlink .= '<span class="expandlinks" id="LinksInCat' . $link_category->term_id . '">';
										$catlink .= '<img class="arrow-down" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/expand-32.png', __FILE__ );
										}

										$catlink .= '" />';

										$catlink .= '<img class="arrow-up" src="';

										if ( !empty( $expandiconpath ) ) {
											$catlink .= $expandiconpath;
										} else {
											$catlink .= plugins_url( 'icons/collapse-32.png', __FILE__ );
										}

										$catlink .= '" />';
										$catlink .= '</span>';
									}

									$catlink .= '</' . $catnameoutput . '>';
								}

								if ($catanchor) {
									$catenddiv = '</div><!-- Div Category Name -->';
								} else {
									$catenddiv = '';
								}
							}

							$current_cat_output .= $cattext . $catlink . $catenddiv;

							// YL: Add option to control this
							//		if ( 0 != $parent_cat_id ) {
							if ( $showcategorydesclinks && ( 'aftercatname' == $catdescpos || ( 'aftertoplevelcatname' == $catdescpos && $level == 0 ) ) ) {
								$current_cat_output .= '<div class="parentcatdesc">' . nl2br( $link_category->description ) . '</div>';
							}
							//	}

							if ( $showlinksonclick ) {
								$current_cat_output .= '<div class="LinksInCat' . $currentcategoryid . ' LinksInCat"><!-- Div show links on click -->';
							}
						}

						if ( !empty( $beforefirstlink ) && $the_link_query->found_posts > 0 ) {
							$current_cat_output .= stripslashes( $beforefirstlink );
						}

						if ( 'linkmasonrygrid' == $displayastable ) {
							$current_cat_output .= '<div class="grid">';
						}

						$display_as_table = 'false';

						if ( is_bool( $displayastable ) && $displayastable ) {
							$display_as_table = 'true';
						} elseif( is_bool( $displayastable ) && !$displayastable ) {
							$display_as_table = 'false';
						} elseif ( in_array( $displayastable, array( 'true', 'false', 'nosurroundingtags', 'linkmasonrygrid', 'categorymasonrygrid' ) ) ) {
							$display_as_table = $displayastable;
						}

						if ( $display_as_table === 'true' && ( ! $combineresults || ( $combineresults && $linkcount > 0 ) ) ) {
							$catstartlist = "\n\t<table class='linklisttable'>\n";
							if ( $showcolumnheaders ) {
								if ( !empty( $columnheaderoverride ) && !$allowcolumnsorting ) {
									$catstartlist .= '<div class="linklisttableheaders"><tr>';

									$columnheaderarray = explode( ',', $columnheaderoverride );
									foreach( $columnheaderarray as $columnheader ) {
										if ( !empty( $columnheader ) ) {
											$catstartlist .= '<th><div class="linklistcolumnheader">' . $columnheader . '</div></th>';
										}
									}

									$catstartlist .= "</tr></div>\n";
								} elseif ( $allowcolumnsorting ) {
									$sorting_labels = array( 2 => 'linkname', 16 => 'linkprice' );
									$settings_sort_label = array( 2 => 'name' );
									$activation_variables = array( 1 => 'show_images', 2 => 'showname', 3 => 'showdate', 4 => 'showdescription',
									                               5 => 'shownotes', 6 => 'show_rss', 7 => 'displayweblink', 8 => 'showtelephone',
									                               9 => 'showemail', 10 => 'showlinkhits', 11 => 'showrating', 12 => 'showlargedescription',
									                               13 => 'showsubmittername', 14 => 'showcatdesc', 15 => 'showlinktags', 16 => 'showlinkprice',
									                               17 => 'showcatname' );

									$default_labels = array( 1 => __( 'Image', 'link-library' ), 2 => __( 'Name', 'link-library' ),
									                         3 => __( 'Date', 'link-library' ), 4 => __( 'Description', 'link-library'),
									                         5 => __( 'Notes', 'link-library'), 6 => __( 'RSS', 'link-library' ),
									                         7 => __( 'Web Link', 'link-library' ), 8 => __( 'Telephone', 'link-library'),
									                         9 => __( 'E-mail', 'link-library' ), 10 => __( 'Hits', 'link-library' ),
									                         11 => __( 'Rating', 'link-library' ), 12 => __( 'Large Description', 'link-library' ),
									                         13 => __( 'Submitter Name', 'link-library' ), 14 => __( 'Category Description', 'link-library' ),
									                         15 => __( 'Tags', 'link-library' ), 16 => __( 'Price', 'link-library'),
									                         17 => __( 'Category Name', 'link-library' ) );

									$dragndroparray = explode( ',', $dragndroporder );

									foreach ( range( 1, 28 ) as $new_entry ) {
										if ( !in_array( $new_entry, $dragndroparray ) ) {
											$dragndroparray[] = $new_entry;
										}
									}

									$catstartlist .= '<div class="linklisttableheaders"><tr>';
									$columnheaderarray = explode( ',', $columnheaderoverride );

									$actual_column = 0;

									foreach ( $dragndroparray as $index => $display_item ) {
										$can_sort = false;
										$sort_label = '';
										$column_label = '';
										$show_column_header = false;

										if ( isset( $columnheaderarray[$actual_column] ) ) {
											$column_label = $columnheaderarray[$actual_column];
										}

										if ( isset( $sorting_labels[$display_item] ) ) {
											$can_sort = true;
											$sort_label = $sorting_labels[$display_item];
										}

										$act_var_name = $activation_variables[$display_item];

										if ( isset( $activation_variables[$display_item] ) && $$act_var_name ) {
											if ( !in_array( $display_item, array( 7, 8, 9 ) ) || ( in_array( $display_item, array( 7, 8, 9 ) ) && $$act_var_name != 'false' ) ) {
												if ( empty( $column_label ) ) {
													$column_label = $default_labels[$display_item];
												}

												$show_column_header = true;
												$actual_column++;
											}
										}

										if ( $show_column_header ) {
											$catstartlist .= '<th><div class="linklistcolumnheader">';
											if ( $can_sort ) {
												$sort_direction = 'ASC';
												if ( ( isset( $_GET[$sorting_labels[$display_item]] ) && 'ASC' == $_GET[$sorting_labels[$display_item]] ) ||
												     ( isset( $settings_sort_label[$display_item] ) && $linkorder = $settings_sort_label[$display_item] && $linkdirection == 'ASC' ) ) {
													$sort_direction = 'DESC';
												}
												$sort_url = add_query_arg( $sorting_labels[$display_item], $sort_direction, '' );
												if ( isset( $_GET['link_tags'] ) && !empty( $_GET['link_tags'] ) ) {
													$sort_url = add_query_arg( 'link_tags', $_GET['link_tags'], $sort_url );
												}

												if ( isset( $_GET['link_price'] ) && !empty( $_GET['link_price'] ) ) {
													$sort_url = add_query_arg( 'link_price', $_GET['link_price'], $sort_url );
												}

												$catstartlist .= '<a href="' . $sort_url . '">';
											}

											$catstartlist .= $column_label;
											if ( $can_sort ) {
												$catstartlist .= '</a>';
											}
											$catstartlist .= '</div></th>';
										}
									}

									$catstartlist .= "</tr></div>\n";
								}
							} else {
								$catstartlist .= '';
							}
						} elseif ( $display_as_table === 'false' && ( ! $combineresults || ( $combineresults && $linkcount > 0 ) ) ) {
							$catstartlist = "\n\t<ul>\n";
						} else {
							$catstartlist = '';
						}

						if ( 0 == $the_link_query->found_posts && !$cat_has_children && !$hide_children_cat_links ) {
							$current_cat_output .= __('No links found', 'link-library');
						} elseif ( !$hide_children_cat_links ) {
							if ( $the_link_query->found_posts > 0 ) {
								$current_cat_output .= $catstartlist;
							}

							while ( $the_link_query->have_posts() ) {
								$the_link_query->the_post();

								if ( 'linkmasonrygrid' == $displayastable ) {
									$current_cat_output .= '<div class="ll-grid-item">';
								}

								if ( !empty( $maxlinks ) && is_numeric( $maxlinks ) && 0 < $maxlinks && $linkcount > $maxlinks ) {
									break;
								}

								if ( $pagination && 'search' != $mode ) {
									if ( $linkcount > $pagenumber * $linksperpage || $linkcount < $startingitem ) {
										$linkcount++;
										continue;
									}
								}

								$linkitem['term_id'] = $link_category->term_id;
								$linkitem['link_name'] = get_the_title();
								$linkitem['link_permalink'] = get_the_permalink( get_the_ID() );
								$linkitem['publication_date'] = get_the_time( 'U', get_the_ID() );
								$link_meta = get_metadata( 'post', get_the_ID() );

								$linkitem['category_description'] = $link_category->description;

								$linkitem['category_name'] = '';
								if ( $combineresults ) {
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
								} else {
									$linkitem['category_name'] = $link_category->name;
								}


								if ( isset( $link_meta['link_url'] ) ) {
									$linkitem['link_url'] = esc_html ( $link_meta['link_url'][0] );
								} else {
									$linkitem['link_url'] = '';
								}

								$linkitem['proper_link_id'] = get_the_ID();

								if ( isset( $link_meta['link_description'] ) ) {
									$linkitem['link_description'] = esc_html( $link_meta['link_description'][0] );
								} else {
									$linkitem['link_description'] = '';
								}

								if ( isset( $link_meta['link_notes'] ) ) {
									$linkitem['link_notes'] = esc_html( $link_meta['link_notes'][0] );
								} else {
									$linkitem['link_notes'] = '';
								}

								if ( isset( $link_meta['link_second_url'] ) ) {
									$linkitem['link_second_url'] = esc_url( $link_meta['link_second_url'][0] );
								} else {
									$linkitem['link_second_url'] = '';
								}

								if ( isset( $link_meta['link_no_follow'] ) ) {
									$linkitem['link_no_follow'] = esc_html( $link_meta['link_no_follow'][0] );
								} else {
									$linkitem['link_no_follow'] = '';
								}

								if ( isset( $link_meta['link_textfield'] ) ) {
									$linkitem['link_textfield'] = $link_meta['link_textfield'][0];
								} else {
									$linkitem['link_textfield'] = '';
								}

								if ( isset( $link_meta['link_target'] ) ) {
									$linkitem['link_target'] = esc_html( $link_meta['link_target'][0] );
								} else {
									$linkitem['link_target'] = '';
								}

								if ( isset( $link_meta['link_image'] ) ) {
									$linkitem['link_image'] = esc_url( $link_meta['link_image'][0] );
								} else {
									$linkitem['link_image'] = '';
								}

								if ( isset( $link_meta['link_featured'] ) ) {
									$linkitem['link_featured'] = esc_html( $link_meta['link_featured'][0] );
								} else {
									$linkitem['link_featured'] = '';
								}

								if ( isset( $link_meta['link_rss'] ) ) {
									$linkitem['link_rss'] = esc_url( $link_meta['link_rss'][0] );
								} else {
									$linkitem['link_rss'] = '';
								}

								if ( isset( $link_meta['link_telephone'] ) ) {
									$linkitem['link_telephone'] = esc_html( $link_meta['link_telephone'][0] );
								} else {
									$linkitem['link_telephone'] = '';
								}

								if ( isset( $link_meta['link_email'] ) ) {
									$linkitem['link_email'] = esc_html( $link_meta['link_email'][0] );
								} else {
									$linkitem['link_email'] = '';
								}

								if ( isset( $link_meta['link_reciprocal'] ) ) {
									$linkitem['link_reciprocal'] = esc_url( $link_meta['link_reciprocal'][0] );
								} else {
									$linkitem['link_reciprocal'] = '';
								}

								if ( isset( $link_meta['link_rel'] ) ) {
									$linkitem['link_rel'] = esc_html( $link_meta['link_rel'][0] );
								} else {
									$linkitem['link_rel'] = '';
								}

								if ( isset( $link_meta['link_submitter'][0] ) ) {
									$linkitem['link_submitter'] = esc_html( $link_meta['link_submitter'][0] );
								} else {
									$linkitem['link_submitter'] = '';
								}

								if ( isset( $link_meta['link_submitter_name'][0] ) ) {
									$linkitem['link_submitter_name'] = esc_html( $link_meta['link_submitter_name'][0] );
								} else {
									$linkitem['link_submitter_name'] = '';
								}

								if ( isset( $link_meta['link_submitter_email'][0] ) ) {
									$linkitem['link_submitter_email'] = esc_html( $link_meta['link_submitter_email'][0] );
								} else {
									$linkitem['link_submitter_email'] = '';
								}

								$linkitem['link_price'] = floatval( get_post_meta( get_the_ID(), 'link_price', true ) );

								if ( isset( $link_meta['link_visits'][0] ) ) {
									$linkitem['link_visits'] = esc_html( $link_meta['link_visits'][0] );
								} else {
									$linkitem['link_visits'] = '';
								}

								if ( isset( $link_meta['link_rating'][0] ) ) {
									$linkitem['link_rating'] = esc_html( $link_meta['link_rating'][0] );
								} else {
									$linkitem['link_rating'] = '';
								}

								for ( $customurlfieldnumber = 1; $customurlfieldnumber < 6; $customurlfieldnumber++ ) {
									$linkitem['link_custom_url_' . $customurlfieldnumber] = '';
									if ( isset( $link_meta['link_custom_url_' . $customurlfieldnumber][0] ) ) {
										$linkitem['link_custom_url_' . $customurlfieldnumber] = esc_url( $link_meta['link_custom_url_' . $customurlfieldnumber][0] );
									} else {
										$linkitem['link_custom_url_' . $customurlfieldnumber] = '';
									}
								}

								for ( $customtextfieldnumber = 1; $customtextfieldnumber < 6; $customtextfieldnumber++ ) {
									$linkitem['link_custom_text_' . $customtextfieldnumber] = '';
									if ( isset( $link_meta['link_custom_text_' . $customtextfieldnumber][0] ) ) {
										$linkitem['link_custom_text_' . $customtextfieldnumber] = sanitize_text_field( $link_meta['link_custom_text_' . $customtextfieldnumber][0] );
									} else {
										$linkitem['link_custom_text_' . $customtextfieldnumber] = '';
									}
								}

								for ( $customlistfieldnumber = 1; $customlistfieldnumber < 6; $customlistfieldnumber++ ) {
									$linkitem['link_custom_list_' . $customlistfieldnumber] = '';
									if ( isset( $link_meta['link_custom_list_' . $customlistfieldnumber][0] ) ) {
										$linkitem['link_custom_list_' . $customlistfieldnumber] = sanitize_text_field( $link_meta['link_custom_list_' . $customlistfieldnumber][0] );
									} else {
										$linkitem['link_custom_list_' . $customlistfieldnumber] = '';
									}
								}

								$date_diff = time() - intval( $linkitem['publication_date'] );

								if ( $date_diff < 86400 * $updateddays ) {
									$linkitem['recently_updated'] = true;
								} else {
									$linkitem['recently_updated'] = false;
								}

								$linkitem['link_updated'] = $link_meta['link_updated'][0];

								if ( true == $debugmode ) {
									$linkstarttime = microtime ( true );
								}

								$between = "\n";
								$rss_array_items = array();

								if ( $rssfeedinline ) {
									include_once( ABSPATH . WPINC . '/feed.php' );

									if ( true == $debugmode ) {
										$starttimerssfeed = microtime ( true );
									}

									$rss_array_items = get_transient( 'Link' . get_the_ID() . 'RSSItems' );

									if ( false === $rss_array_items ) {
										$rss_array_items = array();
										$rss = fetch_feed( $linkitem['link_rss'] );
										if ( !is_wp_error( $rss ) ) {
											$maxitems = $rss->get_item_quantity( $rssfeedinlinecount );

											$rss_items = $rss->get_items( 0, $maxitems );

											if ( $rss_items ) {
												foreach ( $rss_items as $index => $item ) {
													$new_rss_item = array();
													$diff_published = current_time( 'timestamp' ) - strtotime( $item->get_date( 'j F o' ) );
													if ( 0 != $rssfeedinlinedayspublished && $diff_published > 60 * 60 * 24 * intval( $rssfeedinlinedayspublished ) ) {
														unset( $rss_items[$index] );
													} else {
														$new_rss_item['pub_date'] = $item->get_date( 'F j, Y, g:i a' );
														$new_rss_item['permalink'] = $item->get_permalink();
														$new_rss_item['title'] = $item->get_title();
														$new_rss_item['description'] = $item->get_description();

														$rss_array_items[] = $new_rss_item;														
													}
												}

												if ( empty( $rss_array_items ) && $rssfeedinlineskipempty ) {
													continue;
												}
											}
										} else {
											$rss_array_items = 'ERROR';
										}

										set_transient( 'Link' . get_the_ID() . 'RSSItems', $rss_array_items, $rsscachedelay );
									}									

									if ( true == $debugmode ) {
										$current_cat_output .= "\n<!-- Time to render RSS Feed section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssfeed ) . " --> \n";
									}
								}

								if ( $linkaddfrequency > 0 ) {
									if ( $the_link_query->current_post == 0 || $the_link_query->current_post % $linkaddfrequency == 0 ) {
										$current_cat_output .= stripslashes( $addbeforelink );
									}
								}

								if ( !isset( $linkitem['recently_updated'] ) ) {
									$linkitem['recently_updated'] = false;
								}

								$current_cat_output .= stripslashes( $beforeitem );

								$the_link = '#';
								if ( !empty( $linkitem['link_url'] ) ) {
									$the_link = esc_html( $linkitem['link_url'] );
								}

								if ( !empty( $extraquerystring ) ) {
									parse_str( $extraquerystring, $expanded_query_string );
									if ( !empty( $expanded_query_string ) ) {
										$the_link = add_query_arg( $expanded_query_string, $the_link );
									}
								}

								$cat_extra_query_string = get_metadata( 'linkcategory', $linkitem['term_id'], 'linkextraquerystring', true );
								if ( !empty( $cat_extra_query_string ) ) {
									parse_str( $cat_extra_query_string, $cat_expanded_query_string );
									if ( !empty( $cat_expanded_query_string ) ) {
										$the_link = add_query_arg( $cat_expanded_query_string, $the_link );
									}
								}

								$the_second_link = '#';
								if ( !empty( $linkitem['link_second_url'] ) ) {
									$the_second_link = esc_html( stripslashes( $linkitem['link_second_url'] ) );
								}

								$the_permalink = '#';
								if ( !empty( $linkitem['link_permalink'] ) ) {
									$the_permalink = $linkitem['link_permalink'];
								}

								if ( !$suppressnoreferrer ) {
									$rel_list = array( 'noopener', 'noreferrer' );
								} else {
									$rel_list = array();
								}

								if ( $nofollow ) {
									$rel_list[] = 'nofollow';
								}

								if ( !empty( $linkitem['link_rel'] ) ) {
									$rel_list[] = $linkitem['link_rel'];
								}

								if ( !empty( $rel_list ) ) {
									$linkitem['link_rel'] = trim( ' rel="' . implode( ' ', $rel_list ) . '"' );
								}

								$linkitem['link_textfield'] = do_shortcode( $linkitem['link_textfield'] );

								if ( $use_html_tags ) {
									$descnotes = $linkitem['link_notes'];
									$descnotes = str_replace( '[', '<', $descnotes );
									$descnotes = str_replace( ']', '>', $descnotes );
									$desc = $linkitem['link_description'];
									$desc = str_replace("[", "<", $desc);
									$desc = str_replace("]", ">", $desc);
									$textfield = stripslashes( $linkitem['link_textfield'] );
									$textfield = str_replace( '[', '<', $textfield );
									$textfield = str_replace( ']', '>', $textfield );
								} else {
									$descnotes = esc_html( $linkitem['link_notes'], ENT_QUOTES );
									$desc = esc_html($linkitem['link_description'], ENT_QUOTES);
									$textfield = stripslashes( $linkitem['link_textfield'] );
								}

								$cleandesc = $desc;
								$cleanname = esc_html( $linkitem['link_name'], ENT_QUOTES );

								if ( 'search' == $mode ) {
									foreach ( $searchterms as $searchterm ) {
										$descnotes = link_library_highlight_phrase( $descnotes, $searchterm, '<span class="highlight_word">', '</span>' );
										$desc = link_library_highlight_phrase( $desc, $searchterm, '<span class="highlight_word">', '</span>' );
										$name = link_library_highlight_phrase( $linkitem['link_name'], $searchterm, '<span class="highlight_word">', '</span>' );
										$textfield = link_library_highlight_phrase( $textfield, $searchterm, '<span class="highlight_word">', '</span>' );
									}
								} else {
									$name = $cleanname;
								}

								if ( 'linkname' == $linktitlecontent ) {
									$title = $cleanname;
								} elseif ( 'linkdesc' == $linktitlecontent ) {
									$title = $cleandesc;
								}

								if ( $showupdatedtooltip ) {
									$date_format_string = get_option( 'date_format' );
									$cleandate = date_i18n( $date_format_string, intval( $linkitem['link_updated'] ) );
									if ( substr( $cleandate, 0, 2 ) != '00' ) {
										$title .= ' ('.__('Last updated', 'link-library') . '  ' . date_i18n(get_option('links_updated_date_format'), intval( $linkitem['link_updated'] ) ) .')';
									}
								}

								if ( !empty( $title ) ) {
									$title = ' title="' . $title . '"';
								}

								$alt = ' alt="' . $cleanname . '"';

								$target = $linkitem['link_target'];
								if ( !empty( $target ) ) {
									$target = ' target="' . $target . '"';
								} else {
									$target = $linktarget;
									if ( !empty( $target ) ) {
										$target = ' target="' . $target . '"';
									}
								}

								$dragndroparray = explode( ',', $dragndroporder );

								foreach ( range( 1, 33 ) as $new_entry ) {
									if ( !in_array( $new_entry, $dragndroparray ) ) {
										$dragndroparray[] = $new_entry;
									}
								}

								if ( $dragndroparray ) {
									foreach ( $dragndroparray as $arrayelements ) {
										switch ( $arrayelements ) {

											case 1: 	//------------------ Image Output --------------------

												if ( $suppress_image_if_empty && empty( $linkitem['link_image'] ) ) {
													break;
												}

												$imageoutput = '';

												if ( ( $show_images && !$shownameifnoimage ) || ( $show_images && $shownameifnoimage && !empty( $linkitem['link_image'] ) ) || $usethumbshotsforimages ) {
													$imageoutput .= stripslashes( $beforeimage );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_image'] ) ) ) {

														if ( !empty( $linkitem['link_image'] ) || $usethumbshotsforimages ) {
															if ( true == $debugmode ) {
																$starttimeimage = microtime ( true );
															}

															if ( 'imageonly' != $sourceimage ) {
																$imageoutput .= '<a href="';

																if ( !$enable_link_popup ) {
																	if ( 'primary' == $sourceimage || empty( $sourceimage ) ) {
																		$imageoutput .= $the_link;
																	} elseif ( 'secondary' == $sourceimage ) {
																		$imageoutput .= $the_second_link;
																	} elseif ( 'permalink' == $sourceimage ) {
																		$imageoutput .= $the_permalink;
																	}
																} else {
																	$imageoutput .= admin_url( 'admin-ajax.php' . '?action=link_library_popup_content&linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popup_height ) ? 300 : $popup_height ) . '&width=' . ( empty( $popup_width ) ? 400 : $popup_width ) . '&xpath=' . $xpath );
																}

																$imageoutput .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? 'featured' : '' ). '" ' . $linkitem['link_rel'] . $title . $target. '>';
															}

															if ( $usethumbshotsforimages && ( !$uselocalimagesoverthumbshots || empty( $uselocalimagesoverthumbshots ) || ( $uselocalimagesoverthumbshots && empty( $linkitem['link_image'] ) ) ) ) {
																$protocol = is_ssl() ? 'https://' : 'http://';

																if ( $thumbnailgenerator == 'robothumb' ) {
																	$imageoutput .= '<img src="' . $protocol . 'www.robothumb.com/src/?url=' . $the_link . '&size=' . $generaloptions['thumbnailsize'] . '"';
																} elseif ( $thumbnailgenerator == 'thumbshots' ) {
																	if ( !empty( $thumbshotscid ) ) {
																		$imageoutput .= '<img src="' . $protocol . 'images.thumbshots.com/image.aspx?cid=' . rawurlencode( $thumbshotscid ) . '&v=1&w=120&url=' . $the_link . '"';
																	}
																} elseif ( $thumbnailgenerator == 'pagepeeker' ) {
																	if ( empty( $pagepeekerid ) ) {
																		$imageoutput .= '<img src="' . $protocol . 'free.pagepeeker.com/v2/thumbs.php?size=' . $pagepeekersize . '&url=' . $the_link . '"';
																	} else {
																		$imageoutput .= '<img src="' . $protocol . 'api.pagepeeker.com/v2/thumbs.php?size=' . $pagepeekersize . '&url=' . $the_link . '"';
																	}
																} elseif ( 'shrinktheweb' == $thumbnailgenerator ) {
																	if ( !empty( $shrinkthewebaccesskey ) ) {
																		$imageoutput .= '<img src="' . $protocol . 'images.shrinktheweb.com/xino.php?stwembed=1&stwaccesskeyid=' . rawurlencode( $shrinkthewebaccesskey ) . '&stwsize=' . $stwthumbnailsize . '&stwurl=' . $the_link . '"';
																	}
																}
															} else if ( !$usethumbshotsforimages || ( $usethumbshotsforimages && $uselocalimagesoverthumbshots && !empty( $linkitem['link_image'] ) ) ) {
																if ( strpos( $linkitem['link_image'], 'http' ) !== false ) {
																	$imageoutput .= '<img src="' . $linkitem['link_image'] . '"';
																} else {
																	// If it's a relative path
																	$imageoutput .= '<img src="' . get_option( 'siteurl' ) . $linkitem['link_image'] . '"';
																}
															}

															if ( !$usethumbshotsforimages || ($usethumbshotsforimages && !empty( $thumbshotscid ) ) || ( $usethumbshotsforimages && $uselocalimagesoverthumbshots && !empty( $linkitem['link_image'] ) ) ) {

																$imageoutput .= $alt . $title;

																if ( $lazyloadimages ) {
																	$imageoutput .= ' loading="lazy"';
																}

																if ( !empty( $imageclass ) ) {
																	$imageoutput .= ' class="' . $imageclass . '" ';
																}
															}
															$imageoutput .= '/>';

															if ( 'imageonly' != $sourceimage ) {
																$imageoutput .= '</a>';
															}

															if ( true == $debugmode ) {
																$current_cat_output .= '<!-- Time to render image section of link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeimage ) . " --> \n";
															}

														}
													}

													$imageoutput .= stripslashes( $afterimage );

													if ( ( !empty( $imageoutput ) || ( $usethumbshotsforimages && !empty( $thumbshotscid ) ) ) && $show_images ) {
														$current_cat_output .= $imageoutput;
													}
												}

												break;

											case 2: 	//------------------ Name Output --------------------

												if ( ( $showname && 2 == $arrayelements ) || ( $show_images && $shownameifnoimage && empty( $linkitem['link_image'] ) && !$usethumbshotsforimages && 1 == $arrayelements ) ) {
													$current_cat_output .= stripslashes( $beforelink );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $name ) ) ) {
														if ( true == $debugmode ) {
															$starttimename = microtime ( true );
														}

														if ( $showupdated && $linkitem['recently_updated'] && 'before' == $showupdatedpos ) {
															$current_cat_output .= '<span class="recently_updated">' . $updatedlabel . '</span>';
														}

														if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) || ( 'permalink' == $sourcename && $the_permalink != '#' ) ) {
															$current_cat_output .= '<a href="';

															if ( !$enable_link_popup ) {
																if ( 'primary' == $sourcename || empty( $sourcename ) ) {
																	$current_cat_output .= $the_link;
																} elseif ( 'secondary' == $sourcename ) {
																	$current_cat_output .= $the_second_link;
																} elseif ( 'permalink' == $sourcename ) {
																	$current_cat_output .= $the_permalink;
																}
															} else {
																$current_cat_output .= admin_url( 'admin-ajax.php' . '?action=link_library_popup_content&linkid=' . $linkitem['proper_link_id'] . '&settings=' . $settings . '&height=' . ( empty( $popup_height ) ? 300 : $popup_height ) . '&width=' . ( empty( $popup_width ) ? 400 : $popup_width ) . '&xpath=' . $xpath );
															}

															if ( 'description' == $tooltipname && !empty( $desc ) ) {
																$title = ' title="' . $cleandesc . '"';
															} else {
																$title = '';
															}

															$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="' . ( $enable_link_popup ? 'thickbox' : 'track_this_link' ) . ' ' . ( $linkitem['link_featured'] ? ' featured' : '' ). '" ' . $linkitem['link_rel'] . $title . $target. '>';
														}

														$current_cat_output .= $name;

														if ( ( 'primary' == $sourcename && $the_link != '#') || ( 'secondary' == $sourcename && $the_second_link != '#' ) || ( 'permalink' == $sourcename && $the_permalink != '#' ) ) {
															$current_cat_output .= '</a>';
														}

														if ( $showadmineditlinks && $linkeditoruser ) {
															$current_cat_output .= $between . '<span class="editlink"><a href="' . esc_url( add_query_arg( array(
																	'action' => 'edit', 'post' => $linkitem['proper_link_id'] ),
																	admin_url( 'post.php' ) ) ) . '">(' . __('Edit', 'link-library') . ')</a></span>';
														}

														if ( $showupdated && $linkitem['recently_updated'] && 'after' == $showupdatedpos ) {
															$current_cat_output .= '<span class="recently_updated">' . $updatedlabel . '</span>';
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimename ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterlink );
												}

												break;

											case 3: 	//------------------ Date Output --------------------

												if ( $suppress_link_date_if_empty && empty( $linkitem['link_updated'] ) ) {
													break;
												}

												if ( $showdate ) {
													$current_cat_output .= $between . stripslashes( $beforedate );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_updated'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimedate = microtime ( true );
														}

														if ( 'linkupdated' == $datesource ) {
															$formatteddate = date_i18n( get_option( 'links_updated_date_format' ), intval( $linkitem['link_updated'] ) );
														} else {
															$formatteddate = date_i18n( get_option( 'links_updated_date_format' ), get_the_time( 'U', get_the_ID() ) );
														}

														$current_cat_output .= $formatteddate;

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render date section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedate ) . " --> \n";
														}
													}
													$current_cat_output .= stripslashes( $afterdate );
												}

												break;

											case 4: 	//------------------ Description Output --------------------

												if ( $suppress_link_desc_if_empty && empty( $desc ) ) {
													break;
												}

												if ( $showdescription ) {
													$current_cat_output .= $between . stripslashes( $beforedesc );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $desc ) ) ) {
														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														$current_cat_output .= $desc;

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterdesc );
												}

												break;

											case 5: 	//------------------ Notes Output --------------------

												if ( $suppress_link_notes_if_empty && empty( $descnotes ) ) {
													break;
												}

												if ( $shownotes ) {
													$current_cat_output .= $between . stripslashes( $beforenote );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $descnotes ) ) ) {
														if ( true == $debugmode ) {
															$starttimenotes = microtime ( true );
														}

														$current_cat_output .= $descnotes;

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render notes section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimenotes ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afternote );
												}

												break;

											case 6: 	//------------------ RSS Icons Output --------------------

												if ( $suppress_rss_icon_if_empty && empty( $linkitem['link_rss'] ) ) {
													break;
												}

												if ( $show_rss || $show_rss_icon || $rsspreview ) {
													$current_cat_output .= stripslashes( $beforerss );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rss'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimerssicon = microtime ( true );
														}

														$current_cat_output .= '<div class="rsselements">';

														if ( $show_rss && !empty( $linkitem['link_rss'] ) ) {
															$current_cat_output .= $between . '<a class="rss" href="' . $linkitem['link_rss'] . '">RSS</a>';
														}

														if ( $show_rss_icon && !empty( $linkitem['link_rss'] ) ) {
															$current_cat_output .= $between . '<a class="rssicon" href="' . $linkitem['link_rss'] . '"><img src="' . plugins_url( 'icons/feed-icon-14x14.png', __FILE__ ) . '" /></a>';
														}

														if ( $rsspreview && !empty( $linkitem['link_rss'] ) ) {
															$current_cat_output .= $between . '<a href="' . home_url() . '/?link_library_rss_preview=1&keepThis=true&linkid=' . $linkitem['proper_link_id'] . '&previewcount=' . $rsspreviewcount . 'height=' . ( empty( $rsspreviewwidth ) ?  900 : $rsspreviewwidth ) . '&width=' . ( empty( $rsspreviewheight ) ? 700 : $rsspreviewheight ) . '&xpath=' . urlencode( $xpath ) . '" title="' . __('Preview of RSS feed for', 'link-library') . ' ' . $cleanname . '" class="thickbox"><img src="' . plugins_url( 'icons/preview-16x16.png', __FILE__ ) . '" /></a>';
														}

														$current_cat_output .= '</div><!-- Div RSS -->';

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render RSS Icon section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerssicon ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterrss );
												}

												if ( $rssfeedinline && $linkitem['link_rss'] ) {
													if ( !empty( $rss_array_items ) && 'ERROR' != $rss_array_items ) {
														$current_cat_output .= '<div id="ll_rss_results">';
														$date_format_string = get_option( 'date_format' );

														foreach ( $rss_array_items as $item ) {
															$current_cat_output .= '<div class="chunk" style="padding:0 5px 5px;">';
															$item_timestamp = strtotime( $item['pub_date'] );

															$formatted_date = date_i18n( $date_format_string, $item_timestamp );
															$current_cat_output .= '<div class="rsstitle"><a target="feedwindow" href="' . $item['permalink'] . '">' . $item['title'] . '</a><span class="rsstimestamp"> - ' . $formatted_date . '</span></div><!-- RSS Feed title -->';

															if ( $rssfeedinlinecontent ) {
																$current_cat_output .= '<div class="rsscontent">' . $item['description'] . '</div><!-- RSS Content -->';
															}

															$current_cat_output .= '</div><!-- RSS Chunk -->';
															$current_cat_output .= '<br />';
														}

														$current_cat_output .= '</div><!-- RSS Results -->';
													} elseif ( 'ERROR' == $rss_array_items ) {
														$current_cat_output .= '<div class="rss_feed_error">' . __( 'Invalid RSS feed', 'link-library' ) . '</div>';
													}
												}

												break;
											case 7: 	//------------------ Web Link Output --------------------

												if ( 'false' != $displayweblink ) {
													$current_cat_output .= $between . stripslashes( $beforeweblink );

													if
												     ( !$nooutputempty ||
												       ( $nooutputempty && !empty( $the_link ) && 'label' != $displayweblink && '#' != $the_link && 'primary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $the_second_link ) && 'label' != $displayweblink && '#' != $the_second_link && 'secondary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $weblinklabel ) && 'label' == $displayweblink && !empty( $the_link ) && '#' != $the_link && 'primary' == $sourceweblink ) ||
												       ( $nooutputempty && !empty( $weblinklabel ) && 'label' == $displayweblink && !empty( $the_second_link ) && '#' != $the_second_link && 'secondary' == $sourceweblink )
												     ) {
														if ( true == $debugmode ) {
															$starttimerweblink = microtime ( true );
														}

														if ( 'addressonly' == $displayweblink ) {
															if ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) {
																$current_cat_output .= $the_link;
															} elseif ( 'secondary' == $sourceweblink ) {
																$current_cat_output .= $the_second_link;
															}
														} else {
															$current_cat_output .= '<a href="';

															if ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) {
																$current_cat_output .= $the_link;
															} elseif ( 'secondary' == $sourceweblink ) {
																$current_cat_output .= $the_second_link;
															}

															if ( !empty( $weblinktarget ) ) {
																$weblinktarget = ' target="' . $weblinktarget . '"';
															} elseif ( !empty( $target ) && empty( $weblinktarget ) ) {
																$weblinktarget = $target;
															}

															$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" ' . $weblinktarget . '>';

															if ( 'address' == $displayweblink ) {
																if ( ( 'primary' == $sourceweblink || empty( $sourceweblink ) ) && !empty( $the_link ) ) {
																	$current_cat_output .= $the_link;
																} elseif ( 'secondary' == $sourceweblink && !empty( $the_second_link ) ) {
																	$current_cat_output .= $the_second_link;
																}
															} elseif ( 'label' == $displayweblink && !empty( $weblinklabel ) ) {
																$current_cat_output .= stripslashes( $weblinklabel );
															}

															$current_cat_output .= '</a>';
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render web link section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerweblink ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterweblink );
												}

												break;
											case 8: 	//------------------ Telephone Output --------------------

												if ( $suppress_tel_if_empty && empty( $linkitem['link_telephone'] ) ) {
													break;
												}

												if ( 'false' != $showtelephone ) {
													$current_cat_output .= $between . stripslashes( $beforetelephone );

												    if ( !$nooutputempty ||
												       ( $nooutputempty && !empty( $linkitem['link_telephone'] ) && ( 'link' == $showtelephone || 'plain' == $showtelephone ) ) ||
												       ( $nooutputempty && !empty( $telephonelabel ) && 'label' == $showtelephone )
												     )
													{
														if ( true == $debugmode ) {
															$starttimertelephone = microtime ( true );
														}

														if ( 'plain' != $showtelephone ) {
															$current_cat_output .= '<a href="';

															if ( ( 'primary' == $sourcetelephone || empty( $sourcetelephone ) ) && !empty( $the_link ) ) {
																$current_cat_output .= $the_link;
															} elseif ( 'secondary' == $sourcetelephone && !empty( $the_second_link ) ) {
																$current_cat_output .= $the_second_link;
															} elseif ( 'phone' == $sourcetelephone && !empty( $the_second_link ) ) {
																$current_cat_output .= 'tel:' . $linkitem['link_telephone'];
															}

															$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" >';
														}

														if ( 'link' == $showtelephone || 'plain' == $showtelephone ) {
															$current_cat_output .= $linkitem['link_telephone'];
														} elseif ( 'label' == $showtelephone ) {
															$current_cat_output .= $telephonelabel;
														}

														if ( 'plain' != $showtelephone ) {
															$current_cat_output .= '</a>';
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render telephone section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimertelephone ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $aftertelephone );
												}
												break;
											case 9: 	//------------------ E-mail Output --------------------

												if ( $suppress_email_if_empty && empty( $linkitem['link_email'] ) ) {
													break;
												}

												if ( 'false' != $showemail ) {
													$current_cat_output .= $between . stripslashes( $beforeemail );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_email'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimeremail = microtime ( true );
														}

														if ( 'plain' != $showemail ) {
															$current_cat_output .= '<a href="';

															if ( 'mailto' == $showemail || 'mailtolabel' == $showemail ) {
																if ( false === strpos( $linkitem['link_email'], '@' ) ) {
																	$current_cat_output .= esc_url( $linkitem['link_email'] );
																} else {
																	$current_cat_output .= 'mailto:' . $linkitem['link_email'];
																}
															} elseif ( 'command' == $showemail || 'commandlabel' == $showemail ) {
																$newcommand = str_replace( '#email', $linkitem['link_email'], $emailcommand );
																$cleanlinkname = str_replace( ' ', '%20', $linkitem['link_name'] );
																$newcommand = str_replace( '#company', $cleanlinkname, $newcommand );
																$current_cat_output .= $newcommand;
															}

															$current_cat_output .= '">';
														}

														if ( 'plain' == $showemail || 'mailto' == $showemail || 'command' == $showemail ) {
															$current_cat_output .= $linkitem['link_email'];
														} elseif ( 'mailtolabel' == $showemail || 'commandlabel' == $showemail ) {
															$current_cat_output .= $emaillabel;
														}

														if ( 'plain' != $showemail ) {
															$current_cat_output .= '</a>';
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render e-mail section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimeremail ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afteremail );
												}

												break;
											case 10: 	//------------------ Link Hits Output --------------------

												if ( $showlinkhits ) {
													$current_cat_output .= $between . stripslashes( $beforelinkhits );

													if ( true == $debugmode ) {
														$starttimerhits = microtime ( true );
													}

													$current_cat_output .= $linkitem['link_visits'];

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render link hits section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerhits ) . " --> \n";
													}

													$current_cat_output .= stripslashes( $afterlinkhits );
												}

												break;

											case 11: 	//------------------ Link Rating Output --------------------

												if ( $suppress_rating_if_empty && empty( $linkitem['link_rating'] ) ) {
													break;
												}

												if ( $showrating ) {
													$current_cat_output .= $between . stripslashes( $beforelinkrating );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_rating'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimerrating = microtime ( true );
														}

														$current_cat_output .= $linkitem['link_rating'];

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render link rating section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerrating ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterlinkrating );
												}

												break;

											case 12: 	//------------------ Link Large Description Output --------------------

												if ( $suppress_large_desc_if_empty && empty( $textfield ) ) {
													break;
												}

												if ( $showlargedescription ) {
													$current_cat_output .= $between . stripslashes( $beforelargedescription );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $textfield ) ) ) {
														if ( true == $debugmode ) {
															$starttimerlargedesc = microtime ( true );
														}

														$current_cat_output .= $textfield;

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerlargedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterlargedescription );
												}

												break;

											case 13: 	//------------------ Submitter Name Output --------------------

												if ( $suppress_submitter_if_empty && empty( $linkitem['link_submitter_name'] ) ) {
													break;
												}

												if ( $showsubmittername ) {
													$current_cat_output .= $between . stripslashes( $beforesubmittername );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['link_submitter_name'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimersubmittername = microtime ( true );
														}

														$current_cat_output .= $linkitem['link_submitter_name'];

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimersubmittername ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $aftersubmittername );
												}

												break;

											case 14: 	//------------------ Category Description Output --------------------

												if ( $suppress_cat_desc_if_empty && empty( $linkitem['category_description'] ) ) {
													break;
												}

												if ( isset( $linkitem['category_description'] ) ) {
													$linkitem['category_description'] = str_replace( '[', '<', $linkitem['category_description'] );
													$linkitem['category_description'] = str_replace( ']', '>', $linkitem['category_description'] );
												} else {
													$linkitem['category_description'] = '';
												}

												if ( $showcatdesc ) {
													$current_cat_output .= $between . stripslashes( $beforecatdesc );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['category_description'] ) ) ) {

														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														$current_cat_output .= $linkitem['category_description'];

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render category description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $aftercatdesc );
												}

												break;

											case 15: 	//------------------ Link Tags Output --------------------

												$link_tags = wp_get_post_terms( $linkitem['proper_link_id'], 'link_library_tags' );

												if ( $suppress_link_tags_if_empty && empty( $link_tags ) ) {
													break;
												}

												if ( $showlinktags ) {
													$current_cat_output .= $between . stripslashes( $beforelinktags );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $link_tags ) ) ) {

														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														$link_tags_array = array();
														global $wp;

														foreach ( $link_tags as $link_tag ) {
															$pageID = get_queried_object_id();
															$argumentarray = array ( 'link_tags' => $link_tag->slug );
															$targetaddress = esc_url( add_query_arg( $argumentarray ), home_url( $wp->request ) );
															if ( $taglinks == 'active' ) {
																$link_tags_array[] = '<a href="' . $targetaddress . '">' . $link_tag->name . '</a>';
															} else {
																$link_tags_array[] = $link_tag->name;
															}

														}
														$current_cat_output .= implode( ', ', $link_tags_array );

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render category description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterlinktags );
												}

												break;

											case 16: 	//------------------ Link Price Output --------------------

												if ( $suppress_link_price_if_empty && empty( $linkitem['link_price'] ) ) {
													break;
												}

												if ( $showlinkprice ) {
													$current_cat_output .= $between . stripslashes( $beforelinkprice );

													if ( !$nooutputempty || ( $nooutputempty && '' !== $linkitem['link_price'] ) ) {
														if ( true == $debugmode ) {
															$starttimersubmittername = microtime ( true );
														}

														if ( 'before' == $linkcurrencyplacement && !empty( $linkcurrency ) && $linkitem['link_price'] > 0 ) {
															$current_cat_output .= $linkcurrency;
														}

														$value = number_format((float)$linkitem['link_price'], 2, '.', '');
														if ( $value == 0 ) {
															$value = __( 'Free', 'link-library' );
														}
														$current_cat_output .= $value;

														if ( 'after' == $linkcurrencyplacement && !empty( $linkcurrency ) && $linkitem['link_price'] > 0 ) {
															$current_cat_output .= $linkcurrency;
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render link large description section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimersubmittername ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $afterlinkprice );
												}

												break;

											case 17: 	//------------------ Category Name Output --------------------

												if ( $suppress_cat_name_if_empty && empty( $linkitem['category_name'] ) ) {
													break;
												}

												if ( $showcatname ) {
													$current_cat_output .= $between . stripslashes( $beforecatname );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $linkitem['category_name'] ) ) ) {
														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														if ( 'currentcatname' == $catnameformat ) {
															$current_cat_output .= $linkitem['category_name'];
														} elseif( 'allcatnames' == $catnameformat ) {
															$link_terms = wp_get_post_terms( get_the_ID(), 'link_library_category' );

															$link_terms_array = array();
															if ( !empty( $link_terms ) ) {
																foreach( $link_terms as $link_term ) {
																	$link_cat_string = '';
																	$cat_url = '';
																	if ( $enablerewrite && !empty( $rewritepage ) ) {
																		$cat_url = esc_url( site_url() . '/' . $rewritepage . '/' . $link_term->slug );
																	} else {
																		$cat_url = get_term_meta( $link_term->term_id, 'linkcaturl', true );
																	}																	
																	
																	if ( !empty( $cat_url ) ) {
																		$link_cat_string .= '<a href="' . $cat_url . '">';
																	}
																	
																	$link_cat_string .= $link_term->name;

																	if ( !empty( $cat_url ) ) {
																		$link_cat_string .= '</a>';
																	}

																	$link_terms_array[] = $link_cat_string;
																}
															}
															if ( !empty( $link_terms_array ) ) {
																$current_cat_output .= implode( $categoryseparator, $link_terms_array );
															}
														}														

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render category name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $aftercatname );
												}

												break;
											case 18: 	//------------------ Custom URL Output --------------------
											case 19:
											case 20:
											case 21:
											case 22:

												$customurlfieldid = $arrayelements - 17;
												$fieldactivevar = 'customurl' . $customurlfieldid . 'active';
												$displayvar = 'displaycustomurl' . $customurlfieldid;
												$beforevar = 'beforecustomurl' . $customurlfieldid;
												$aftervar = 'aftercustomurl' . $customurlfieldid;
												$labelvar = 'customurl' . $customurlfieldid . 'label';
												$targetvar = 'customurl' . $customurlfieldid . 'target';
												$suppressvar = 'suppress_custom_url_' . $customurlfieldid . '_if_empty';

												$customurl = $linkitem['link_custom_url_' . $customurlfieldid];

												if ( $suppressvar && empty( $customurl ) ) {
													break;
												}

												if ( $$fieldactivevar && 'false' != $$displayvar ) {
													$current_cat_output .= $between . stripslashes( $$beforevar );

													if
													( !$nooutputempty ||
													  ( $nooutputempty && !empty( $customurl ) && 'label' != $$displayvar && '#' != $customurl ) || ( $nooutputempty && !empty( $$labelvar ) && 'label' == $$displayvar && !empty( $customurl ) && '#' != $customurl )
													) {
														if ( true == $debugmode ) {
															$starttimerweblink = microtime ( true );
														}

														if ( 'addressonly' == $$displayvar ) {
															$current_cat_output .= $customurl;
														} else {
															$current_cat_output .= '<a href="';

															$current_cat_output .= $customurl;

															if ( !empty( $$targetvar ) ) {
																$weblinktarget = ' target="' . $$targetvar . '"';
															} elseif ( !empty( $target ) ) {
																$weblinktarget = ' target="' . $target . '"';
															}

															$current_cat_output .= '" id="link-' . $linkitem['proper_link_id'] . '" class="track_this_link" ' . $$targetvar . '>';

															if ( 'address' == $$displayvar ) {
																$current_cat_output .= $customurl;
															} elseif ( 'label' == $$displayvar && !empty( $$labelvar ) ) {
																$current_cat_output .= stripslashes( $$labelvar );
															}

															$current_cat_output .= '</a>';
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render custom URL section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimerweblink ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $$aftervar );
												}

												break;
											case 23: 	//------------------ User Votes Output --------------------

												if ( $showuservotes && ( !$membersonlylinkvotes || ( $membersonlylinkvotes && is_user_logged_in() ) ) ) {
													$current_cat_output .= $between . stripslashes( $beforeuservotes );

													if ( true == $debugmode ) {
														$starttimedesc = microtime ( true );
													}

													$current_cat_output .= thumbs_rating_getlink( '', '', true, $uservotelikelabel );

													if ( true == $debugmode ) {
														$current_cat_output .= "\n<!-- Time to render category name section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
													}

													$current_cat_output .= stripslashes( $afteruservotes );
												}

												break;
											case 24: 	//------------------ Custom Text Output --------------------
											case 25:
											case 26:
											case 27:
											case 28:

												$customtextfieldid = $arrayelements - 23;
												$fieldactivevar = 'customtext' . $customtextfieldid . 'active';
												$displayvar = 'displaycustomtext' . $customtextfieldid;
												$beforevar = 'beforecustomtext' . $customtextfieldid;
												$aftervar = 'aftercustomtext' . $customtextfieldid;
												$suppressvar = 'suppress_custom_text_' . $customtextfieldid . '_if_empty';

												$customtext = $linkitem['link_custom_text_' . $customtextfieldid];

												if ( $suppressvar && empty( $customtext ) ) {
													break;
												}

												if ( $$fieldactivevar && 'false' != $$displayvar ) {
													$current_cat_output .= $between . stripslashes( $$beforevar );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $customtext ) ) ) {

														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														$current_cat_output .= $customtext;

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render custom text section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $$aftervar );
												}

												break;
											case 29: 	//------------------ Custom List Output --------------------
											case 30:
											case 31:
											case 32:
											case 33:

												$customlistfieldid = $arrayelements - 28;
												$fieldactivevar = 'customlist' . $customlistfieldid . 'active';
												$customlistvalues = 'customlist' . $customlistfieldid . 'values';
												$customlisthtmlname = 'customlist' . $customlistfieldid . 'html';
												$displayvar = 'displaycustomlist' . $customlistfieldid;
												$beforevar = 'beforecustomlist' . $customlistfieldid;
												$aftervar = 'aftercustomlist' . $customlistfieldid;
												$suppressvar = 'suppress_custom_list_' . $customtextfieldid . '_if_empty';

												$customlistselection = '';
												if ( isset( $linkitem['link_custom_list_' . $customlistfieldid] ) ) {
													$customlistselection = $linkitem['link_custom_list_' . $customlistfieldid];
												}

												if ( $suppressvar && empty( $customlistselection ) ) {
													break;
												}

												if ( $$fieldactivevar && 'false' != $$displayvar ) {
													$list_values = array();
													if ( !empty( $$customlistvalues ) ) {
														$list_values = explode( ',', $$customlistvalues );
													}

													$html_values = array();

													if ( !empty( $$customlisthtmlname ) ) {
														$html_values = $$customlisthtmlname;
													}

													$current_cat_output .= $between . stripslashes( $$beforevar );

													if ( !$nooutputempty || ( $nooutputempty && !empty( $customlistselection ) ) ) {

														if ( true == $debugmode ) {
															$starttimedesc = microtime ( true );
														}

														if ( 'listentry' == $$displayvar && isset( $list_values[$customlistselection] ) ) {
															$current_cat_output .= $list_values[$customlistselection];
														} elseif( 'listhtml' == $$displayvar && isset( $html_values[$customlistselection] ) ) {
															$current_cat_output .= stripslashes( $html_values[$customlistselection] );
														}

														if ( true == $debugmode ) {
															$current_cat_output .= "\n<!-- Time to render custom text section of link id " . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $starttimedesc ) . " --> \n";
														}
													}

													$current_cat_output .= stripslashes( $$aftervar );
												}

												break;
										}
									}
								}

								$current_cat_output .= stripslashes( $afteritem ) . "\n";

								if ( $linkaddfrequency > 0 ) {
									if ( ( $the_link_query->current_post + 1 ) % $linkaddfrequency === 0 || $the_link_query->current_post + 1 == $the_link_query->found_posts ) {
										$current_cat_output .= stripslashes( $addafterlink );
									}
								}

								if ( true == $debugmode ) {
									$current_cat_output .= '<!-- Time to render link id ' . $linkitem['proper_link_id'] . ': ' . ( microtime( true ) - $linkstarttime ) . " --> \n";
								}

								$linkcount++;

								if ( 'linkmasonrygrid' == $displayastable ) {
									$current_cat_output .= '</div>';
								}
							}

							// Close the category
							if ( $the_link_query->found_posts > 0 ) {
								if ( 'true' == $display_as_table ) {
									$current_cat_output .= "\t</table>\n";
								} elseif ( 'false' == $display_as_table ) {
									$current_cat_output .= "\t</ul>\n";
								}

								if ( 'linkmasonrygrid' == $displayastable ) {
									$current_cat_output .= '</div>';
								}
							}
						}

						if ( !empty( $catlistwrappers ) && !empty( $beforecastlist1 ) ) {
							$current_cat_output .= '</div><!-- Div cat list wrappers -->';
						}

						if ( !empty( $afterlastlink ) && $the_link_query->found_posts > 0 ) {
							$current_cat_output .= stripslashes( $afterlastlink );
						}

						if ( $showlinksonclick ) {
							$current_cat_output .= '</div><!-- Div Show Links on click -->';
						}

						$currentcategory = $currentcategory + 1;

						if ( $display_children && $cat_has_children && !$showonecatonly ) {
							if ( $showlinksonclick ) {
								$current_cat_output .= '<div class="Cat' . $link_category->term_id . 'SubCategories SubCategories">';
							}

							$libraryoptions['categorylist_cpt'] = '';
							if ( !empty( $childcategory_cpt ) && !empty( $childcategory_cpt[$link_category->term_id] ) ) {
								$childsortlist = $childcategory_cpt[$link_category->term_id];
								if ( !empty( $childsortlist ) ) {
									$childsortlist = str_replace( '-', ',', $childsortlist );
									$libraryoptions['categorylist_cpt'] = $childsortlist;
								}
							}

							$current_cat_output .= RenderLinkLibrary( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $onlycount, $link_category->term_id, $level + 1, $display_children, $hidechildcatlinks, $linkcount );

							if ( $showlinksonclick ) {
								$current_cat_output .= '</div>';
							}
						}

						if ( $combineresults ) {
							if ( $start_link_count != $linkcount ) {
								$output .= $current_cat_output;
							}
							break;
						} else {
							$current_cat_output .= "</div><!-- Div End Category -->\n";
						}
					}

					if ( $start_link_count != $linkcount ) {
						$output .= $current_cat_output;
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

	if ( !empty( $searchstring ) && $linkcount == 1 && $level == 0 ) {
		$output .= '<span class="nolinksfoundallcats">' . $searchnoresultstext . "</span>\n";
	}

	if ( $usethumbshotsforimages && $level == 0 ) {
		if ( $thumbnailgenerator == 'robothumb' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.robothumb.com" target="_blank">' . __( 'Screenshots by Robothumb', 'link-library' ) . '</a></div>';
		} elseif ( $thumbnailgenerator == 'thumbshots' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Screenshots by Thumbshots">' . __( 'Thumbnail Screenshots by Thumbshots', 'link-library' ) . '</a></div>';
		} elseif ( $thumbnailgenerator == 'Shrink the Web' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.shrinktheweb.com" target="_blank" title="Thumbnails Screenshots by Shrink the Web">' . __( 'Thumbnail Screenshots by Shrink the Web', 'link-library' ) . '</a></div>';
		} elseif ( $thumbnailgenerator == 'Page Peeker' ) {
			$output .= '<div class="llthumbshotsnotice"><a href="http://www.shrinktheweb.com" target="_blank" title="Thumbnails Screenshots by Page Peeker">' . __( 'Thumbnail Screenshots by Page Peeker', 'link-library' ) . '</a></div>';
		}
	}

	if ( $level == 0 && $pagination && 'search' != $mode && ( 'AFTER' == $paginationposition || empty( $pagination ) ) ) {
		$previouspagenumber = $pagenumber - 1;
		$nextpagenumber = $pagenumber + 1;
		$pageID = get_queried_object_id();

		$output .= link_library_display_pagination( $previouspagenumber, $nextpagenumber, $number_of_pages, $pagenumber, $showonecatonly, $showonecatmode, $AJAXcatid, $settings, $pageID, $currentcatletter );
	}

	if ( $level == 0 ) {
		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );
		$nonce = wp_create_nonce( 'll_tracker' );

		$output .= "<script type='text/javascript'>\n";

		$output .= "jQuery(document).ready(function()\n";
		$output .= "{\n";
		
		if ( 'linkmasonrygrid' == $displayastable || 'categorymasonrygrid' == $displayastable ) {
			$output .= "jQuery( '.grid' ).masonry({\n";
			$output .= "\titemSelector: '.ll-grid-item',\n";
			$output .= "\tcolumnWidth: 1\n";
			$output .= "});\n";
		}
		
		$output .= "jQuery('.arrow-up').hide();\n";
		$output .= "jQuery('#linklist" . $settings . " a.track_this_link').click(function() {\n";
		$output .= "linkid = this.id;\n";
		$output .= "linkid = linkid.substring(5);\n";
		$output .= "path = '" . $xpath . "';\n";
		$output .= "jQuery.ajax( {" .
		           "    type: 'POST'," .
		           "    url: '" . admin_url( 'admin-ajax.php' ) . "', " .
		           "    data: { action: 'link_library_tracker', " .
		           "            _ajax_nonce: '" . $nonce . "', " .
		           "            id:linkid, xpath:path } " .
		           "    });\n";
		$output .= "return true;\n";
		$output .= "});\n";

		$output .= "jQuery('#linklist" . $settings . " .expandlinks').click(function() {\n";
		$output .= "target = '.' + jQuery(this).attr('id');\n";
		$output .= "subcattarget = '.' + jQuery(this).attr('data-subcat');\n";
		$output .= "if ( jQuery( target ).is(':visible') ) {\n";
		$output .= "jQuery(target).slideUp();\n";
		$output .= "jQuery(subcattarget).slideToggle();\n";

		$output .= "jQuery(this).children('img').attr('src', '";

		if ( !empty( $expandiconpath ) ) {
			$output .= $expandiconpath;
		} else {
			$output .= plugins_url( 'icons/expand-32.png', __FILE__ );
		}

		$output .= "');\n";
		$output .= "} else {\n";
		$output .= "jQuery(target).slideDown();\n";

		$output .= "jQuery(subcattarget).slideToggle();\n";

		$output .= "jQuery(this).children('img').attr('src', '";

		if ( !empty( $collapseiconpath ) ) {
			$output .= $collapseiconpath;
		} else {
			$output .= plugins_url( 'icons/collapse-32.png', __FILE__ );
		}

		$output .= "');\n";
		$output .= "}\n";
		$output .= "});\n";

		$output .= "jQuery('#linklist" . $settings . " .linklistcatclass').click(function() {\n";
		$output .= "jQuery(this).siblings('.expandlinks').click();\n";
		$output .= "});\n";

		$output .= "jQuery('#linklist" . $settings . " .linklistcatnamedesc').click(function() {\n";
		$output .= "jQuery(this).siblings('.expandlinks').click();\n";
		$output .= "});\n";

		$output .= "});\n";
		$output .= "</script>\n";
		unset( $xpath );

		if ( $showuservotes ) {
			$output .= thumbs_rating_check();
		}
	}

	$currentcategory = $currentcategory + 1;

	if ( $level == 0 ) {
		$output .= '</div><!-- Div Linklist -->';

		$output .= "\n<!-- End of Link Library Output -->\n\n";
	}

	remove_filter( 'posts_search', 'll_expand_posts_search', 10 );

	wp_reset_postdata();

	return do_shortcode( $output );
}
