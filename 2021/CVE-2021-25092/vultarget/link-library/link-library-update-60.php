<?php

function link_library_60_update( $plugin_class, $continue = false ) {
	update_option( 'LinkLibrary60Update', true );

	global $wpdb;

	$link_query = 'select count(*) from ' . $plugin_class->db_prefix() . 'links';
	$link_count = $wpdb->get_var( $link_query );

	if ( $link_count > 0 ) {
		$all_link_cats_query = 'SELECT t.name, t.term_id, tt.description ';
		$all_link_cats_query .= 'FROM ' . $plugin_class->db_prefix() . 'terms t ';
		$all_link_cats_query .= 'LEFT JOIN ' . $plugin_class->db_prefix() . 'term_taxonomy tt ON (t.term_id = tt.term_id) ';
		$all_link_cats_query .= 'LEFT JOIN ' . $plugin_class->db_prefix() . 'term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ';
		$all_link_cats_query .= 'WHERE tt.taxonomy = "link_category" ';

		$all_link_cats = $wpdb->get_results( $all_link_cats_query );

		foreach ( $all_link_cats as $link_cat ) {
			$cat_string = $link_cat->name;

			$cat_matched_term = get_term_by( 'name', $cat_string, 'link_library_category' );

			if ( false === $cat_matched_term ) {
				$new_cat_term_data   = wp_insert_term( $cat_string, 'link_library_category', array( 'description' => $link_cat->description ) );
				if ( is_wp_error( $new_cat_term_data ) ) {
					print_r( 'Failed creating category ' . $cat_string );
				} else {
					$linkcaturl = get_metadata( 'linkcategory', $link_cat->term_id, 'linkcaturl', true );

					if ( !empty( $linkcaturl ) ) {
						update_term_meta( $new_cat_term_data['term_id'], 'linkcaturl', $linkcaturl );
					}
				}
			}
		}

		$wpdb->links_extrainfo = $plugin_class->db_prefix() . 'links_extrainfo';

		$creationquery = "CREATE TABLE " . $wpdb->links_extrainfo . " (
			link_id bigint(20) NOT NULL DEFAULT '0',
			link_second_url varchar(255) CHARACTER SET utf8 DEFAULT NULL,
			link_telephone varchar(128) CHARACTER SET utf8 DEFAULT NULL,
			link_email varchar(128) CHARACTER SET utf8 DEFAULT NULL,
			link_visits bigint(20) DEFAULT '0',
			link_reciprocal varchar(255) DEFAULT NULL,
			link_submitter varchar(255) DEFAULT NULL,
			link_submitter_name VARCHAR(128) CHARACTER SET utf8 NULL,
			link_submitter_email VARCHAR(128) NULL,
			link_textfield TEXT CHARACTER SET utf8 NULL,
			link_no_follow VARCHAR(1) NULL,
			link_featured VARCHAR(1) NULL,
			link_manual_updated VARCHAR(1) NULL,
			link_addl_rel VARCHAR(256) NULL,
			PRIMARY KEY  (link_id)
			)";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $creationquery );

		$wpdb->linkcategorymeta = $plugin_class->db_prefix() . 'linkcategorymeta';

		$meta_creation_query =
			'CREATE TABLE ' . $wpdb->linkcategorymeta . ' (
	        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	        linkcategory_id bigint(20) unsigned NOT NULL DEFAULT "0",
	        meta_key varchar(255) DEFAULT NULL,
	        meta_value longtext,
	        PRIMARY KEY  (meta_id)
	        );';

		dbDelta ( $meta_creation_query );

		$links_import_query = "SELECT distinct l.link_id as import_link_id, l.link_name, l.link_url, l.link_rss, l.link_description, l.link_notes, ";
		$links_import_query .= "GROUP_CONCAT( t.name ) as cat_name, l.link_visible, le.link_second_url, le.link_telephone, le.link_email, le.link_reciprocal, ";
		$links_import_query .= "l.link_image, le.link_textfield, le.link_no_follow, l.link_rating, l.link_target, l.link_updated, le.link_visits, ";
		$links_import_query .= "le.link_submitter, le.link_submitter_name, le.link_submitter_email, le.link_addl_rel, le.link_featured, le.link_manual_updated, l.link_owner ";
		$links_import_query .= "FROM " . $plugin_class->db_prefix() . "terms t ";
		$links_import_query .= "LEFT JOIN " . $plugin_class->db_prefix() . "term_taxonomy tt ON (t.term_id = tt.term_id) ";
		$links_import_query .= "LEFT JOIN " . $plugin_class->db_prefix() . "term_relationships tr ON (tt.term_taxonomy_id = tr.term_taxonomy_id) ";
		$links_import_query .= "LEFT JOIN " . $plugin_class->db_prefix() . "links l ON (tr.object_id = l.link_id) ";
		$links_import_query .= "LEFT JOIN " . $plugin_class->db_prefix() . "links_extrainfo le ON (l.link_id = le.link_id) ";
		$links_import_query .= "WHERE tt.taxonomy = 'link_category' ";
		$links_import_query .= "GROUP BY l.link_id ";

		$links_to_import = $wpdb->get_results( $links_import_query );

		foreach ( $links_to_import as $link_to_import ) {

			global $wpdb;

			if ( $continue ) {
				$query = 'SELECT ID FROM ' . $wpdb->posts . ' p, ' . $wpdb->postmeta . ' pm WHERE post_title = "' . $link_to_import->link_name. '" AND post_type = \'link_library_links\' AND p.ID = pm.post_ID and pm.meta_key = "link_url" and pm.meta_value = "' . $link_to_import->link_url . '"';
				$wpdb->query( $query );

				if ( $wpdb->num_rows ) {
					continue;
				}
			}

			if ( !empty( $link_to_import->link_name ) ) {
				$matched_link_cats = array();
				if ( !empty( $link_to_import->cat_name ) ) {
					$link_cats = explode( ',', $link_to_import->cat_name );

					foreach ( $link_cats as $link_cat ) {
						$cat_string = $link_cat;

						$cat_matched_term = get_term_by( 'name', $cat_string, 'link_library_category' );

						if ( false !== $cat_matched_term ) {
							$matched_link_cats[] = $cat_matched_term->term_id;
						}
					}
				}

				$new_link_data = array(
					'post_type' => 'link_library_links',
					'post_content' => '',
					'post_title' => $link_to_import->link_name,
					'post_author' => $link_to_import->link_owner,
				);

				if ( 'N' == $link_to_import->link_visible ) {
					if ( false !== strpos( $link_to_import->link_description, '(LinkLibrary:AwaitingModeration:RemoveTextToApprove)' ) ) {
						$new_link_data['post_status'] = 'pending';
						$link_to_import->link_description = str_replace( '(LinkLibrary:AwaitingModeration:RemoveTextToApprove)', '', $link_to_import->link_description );
					} else {
						$new_link_data['post_status'] = 'private';
					}
				} elseif ( 'Y' == $link_to_import->link_visible ) {
					$new_link_data['post_status'] = 'publish';
				}

				if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ){
					$new_link_ID = wp_insert_post( $new_link_data );
				}

				if ( !empty( $new_link_ID ) ) {
					wp_set_post_terms( $new_link_ID, $matched_link_cats, 'link_library_category', false );

					update_post_meta( $new_link_ID, 'legacy_link_id', $link_to_import->import_link_id );
					update_post_meta( $new_link_ID, 'link_url', $link_to_import->link_url );
					update_post_meta( $new_link_ID, 'link_image', $link_to_import->link_image );
					update_post_meta( $new_link_ID, 'link_target', $link_to_import->link_target );
					update_post_meta( $new_link_ID, 'link_description', $link_to_import->link_description );
					update_post_meta( $new_link_ID, 'link_rating', $link_to_import->link_rating );

					if ( '0000-00-00 00:00:00' ==  $link_to_import->link_updated ) {
						update_post_meta( $new_link_ID, 'link_updated', current_time( 'timestamp' ) );
					} else {
						update_post_meta( $new_link_ID, 'link_updated', strtotime( $link_to_import->link_updated ) );
					}

					update_post_meta( $new_link_ID, 'link_notes', $link_to_import->link_notes );
					update_post_meta( $new_link_ID, 'link_rss', $link_to_import->link_rss );
					update_post_meta( $new_link_ID, 'link_second_url', $link_to_import->link_second_url );
					update_post_meta( $new_link_ID, 'link_telephone', $link_to_import->link_telephone );
					update_post_meta( $new_link_ID, 'link_email', $link_to_import->link_email );

					if ( empty( $link_to_import->link_visits ) ) {
						update_post_meta( $new_link_ID, 'link_visits', 0 );
					} else {
						update_post_meta( $new_link_ID, 'link_visits', $link_to_import->link_visits );
					}

					update_post_meta( $new_link_ID, 'link_reciprocal', $link_to_import->link_reciprocal );
					update_post_meta( $new_link_ID, 'link_submitter', $link_to_import->link_submitter );
					update_post_meta( $new_link_ID, 'link_submitter_name', $link_to_import->link_submitter_name );
					update_post_meta( $new_link_ID, 'link_submitter_email', $link_to_import->link_submitter_email );
					update_post_meta( $new_link_ID, 'link_textfield', $link_to_import->link_textfield );
					update_post_meta( $new_link_ID, 'link_rel', $link_to_import->link_addl_rel );

					if ( '1' == $link_to_import->link_no_follow ) {
						update_post_meta( $new_link_ID, 'link_no_follow', true );
					} else {
						update_post_meta( $new_link_ID, 'link_no_follow', false );
					}

					if ( '1' == $link_to_import->link_featured ) {
						update_post_meta( $new_link_ID, 'link_featured', 1 );
					} else {
						update_post_meta( $new_link_ID, 'link_featured', 0 );
					}

					if ( 'Y' == $link_to_import->link_manual_updated ) {
						update_post_meta( $new_link_ID, 'link_updated_manual', true );
					} elseif ( 'N' == $link_to_import->link_manual_updated ) {
						update_post_meta( $new_link_ID, 'link_updated_manual', false );
					}
				}
			}
		}
	}

	$genoptions = get_option( 'LinkLibraryGeneral' );
	if ( !empty( $genoptions ) ) {
		for ( $i = 1; $i <= $genoptions['numberstylesets']; $i++ ) {
			$settingsname = 'LinkLibraryPP' . $i;
			$options      = get_option( $settingsname );

			if ( !empty( $options ) ) {
				$lists_of_cats = array( 'categorylist', 'excludecategorylist', 'defaultsinglecat' );

				foreach ( $lists_of_cats as $list_of_cats ) {
					if ( !empty( $options[$list_of_cats] ) ) {
						$category_list_array = explode( ',', $options[$list_of_cats] );
						$new_category_list_array = array();

						foreach ( $category_list_array as $category_list_item ) {
							$original_term = get_term( $category_list_item, 'link_category' );
							if ( !empty( $original_term ) ) {
								$corresponding_term = get_term_by( 'name', $original_term->name, 'link_library_category' );
								if ( !empty( $corresponding_term ) ) {
									$new_category_list_array[] = $corresponding_term->term_id;
								}
							}
						}

						$new_category_list = implode( ',', $new_category_list_array );
						$options[$list_of_cats . '_cpt'] = $new_category_list;
					} else {
						$options[$list_of_cats . '_cpt'] = '';
					}
				}

				$newcolumnoptions = array();
				if ( isset( $options['linkheader'] ) ) {
					$newcolumnoptions[] = $options['linkheader'];
				}

				if ( isset( $options['descheader'] ) ) {
					$newcolumnoptions[] = $options['descheader'];
				}

				if ( isset( $options['notesheader'] ) ) {
					$newcolumnoptions[] = $options['notesheader'];
				}

				$options['columnheaderoverride'] = implode( ',', $newcolumnoptions );

				update_option( $settingsname, $options );
			}
		}
	}
}
