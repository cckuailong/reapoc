<?php

function link_library_process_user_submission( $my_link_library_plugin ) {
	check_admin_referer( 'LL_ADDLINK_FORM' );

	global $wpdb; // Kept with CPT update

	require_once( ABSPATH . '/wp-admin/includes/taxonomy.php' );
	require_once( ABSPATH . '/wp-admin/includes/image.php' );

	load_plugin_textdomain( 'link-library', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	$settings     = ( isset( $_POST['settingsid'] ) ? $_POST['settingsid'] : 1 );
	$settingsname = 'LinkLibraryPP' . $settings;
	$options      = get_option( $settingsname );
	$options = wp_parse_args( $options, ll_reset_options( 1, 'list', 'return' ) );

	$genoptions = get_option( 'LinkLibraryGeneral' );
	$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

	$valid   = true;
	$requiredcheck = true;
	$message = "";

	$captureddata                           = array();
	$captureddata['link_category']          = ( isset( $_POST['link_category'] ) ? $_POST['link_category'] : '' );
	$captureddata['link_user_category']     = ( isset( $_POST['link_user_category'] ) ? $_POST['link_user_category'] : '' );
	$captureddata['link_tags']              = ( isset( $_POST['link_tags'] ) ? $_POST['link_tags'] : '' );
	$captureddata['link_user_tags']         = ( isset( $_POST['link_user_tags'] ) ? $_POST['link_user_tags'] : '' );
	$captureddata['link_description']       = ( isset( $_POST['link_description'] ) ? $_POST['link_description'] : '' );
	$captureddata['link_textfield']         = ( isset( $_POST['link_textfield'] ) ? $_POST['link_textfield'] : '' );
	$captureddata['link_name']              = ( isset( $_POST['link_name'] ) ? $_POST['link_name'] : '' );
	$captureddata['link_url']               = ( isset( $_POST['link_url'] ) ? $_POST['link_url'] : '' );
	$captureddata['link_rss']               = ( isset( $_POST['link_rss'] ) ? $_POST['link_rss'] : '' );
	$captureddata['link_notes']             = ( isset( $_POST['link_notes'] ) ? $_POST['link_notes'] : '' );
	$captureddata['ll_secondwebaddr']       = ( isset( $_POST['ll_secondwebaddr'] ) ? $_POST['ll_secondwebaddr'] : '' );
	$captureddata['ll_telephone']           = ( isset( $_POST['ll_telephone'] ) ? $_POST['ll_telephone'] : '' );
	$captureddata['ll_email']               = ( isset( $_POST['ll_email'] ) ? $_POST['ll_email'] : '' );
	$captureddata['ll_reciprocal']          = ( isset( $_POST['ll_reciprocal'] ) ? $_POST['ll_reciprocal'] : '' );
	$captureddata['ll_submittername']       = ( isset( $_POST['ll_submittername'] ) ? $_POST['ll_submittername'] : '' );
	$captureddata['ll_submitteremail']      = ( isset( $_POST['ll_submitteremail'] ) ? $_POST['ll_submitteremail'] : '' );
	$captureddata['ll_submittercomment']    = ( isset( $_POST['ll_submittercomment'] ) ? $_POST['ll_submittercomment'] : '' );
	$captureddata['ll_customcaptchaanswer'] = ( isset( $_POST['ll_customcaptchaanswer'] ) ? $_POST['ll_customcaptchaanswer'] : '' );
	$captureddata['link_category']          = ( isset( $_POST['link_category'] ) ? $_POST['link_category'] : '' );
	$captureddata['link_tags']              = ( isset( $_POST['link_tags'] ) ? $_POST['link_tags'] : '' );
	$captureddata['ll_linkreference']		= ( isset( $_POST['ll_linkreference'] ) ? $_POST['ll_linkreference'] : '' );
	$captureddata['ll_customurl1']			= ( isset( $_POST['ll_customurl1'] ) ? $_POST['ll_customurl1'] : '' );
	$captureddata['ll_customurl2']			= ( isset( $_POST['ll_customurl2'] ) ? $_POST['ll_customurl2'] : '' );
	$captureddata['ll_customurl3']			= ( isset( $_POST['ll_customurl3'] ) ? $_POST['ll_customurl3'] : '' );
	$captureddata['ll_customurl4']			= ( isset( $_POST['ll_customurl4'] ) ? $_POST['ll_customurl4'] : '' );
	$captureddata['ll_customurl5']			= ( isset( $_POST['ll_customurl5'] ) ? $_POST['ll_customurl5'] : '' );
	$captureddata['ll_customtext1']			= ( isset( $_POST['ll_customtext1'] ) ? $_POST['ll_customtext1'] : '' );
	$captureddata['ll_customtext2']			= ( isset( $_POST['ll_customtext2'] ) ? $_POST['ll_customtext2'] : '' );
	$captureddata['ll_customtext3']			= ( isset( $_POST['ll_customtext3'] ) ? $_POST['ll_customtext3'] : '' );
	$captureddata['ll_customtext4']			= ( isset( $_POST['ll_customtext4'] ) ? $_POST['ll_customtext4'] : '' );
	$captureddata['ll_customtext5']			= ( isset( $_POST['ll_customtext5'] ) ? $_POST['ll_customtext5'] : '' );
	$captureddata['ll_customlist1']			= ( isset( $_POST['ll_customlist1'] ) ? $_POST['ll_customlist1'] : '' );
	$captureddata['ll_customlist2']			= ( isset( $_POST['ll_customlist2'] ) ? $_POST['ll_customlist2'] : '' );
	$captureddata['ll_customlist3']			= ( isset( $_POST['ll_customlist3'] ) ? $_POST['ll_customlist3'] : '' );
	$captureddata['ll_customlist4']			= ( isset( $_POST['ll_customlist4'] ) ? $_POST['ll_customlist4'] : '' );
	$captureddata['ll_customlist5']			= ( isset( $_POST['ll_customlist5'] ) ? $_POST['ll_customlist5'] : '' );	

	$uploads = wp_upload_dir();

	if ( isset( $_FILES['linkimage']['name'] ) ) {
		$image_file_ext = strtolower( end( explode( '.', $_FILES['linkimage']['name'] ) ) );
		$allowed_image_extensions = array( 'jpeg', 'jpg', 'png' );
	}

	if ( isset( $_FILES['linkfile']['name'] ) ) {
		$link_file_ext = strtolower( end( explode( '.', $_FILES['linkfile']['name'] ) ) );
		$allowed_link_file_extensions = explode( ',', $options['linkfileallowedtypes'] );
	}

	if ( 'required' == $options['showaddlinkrss'] && empty( $captureddata['link_rss'] ) ) {
		$requiredcheck = false;
		$message = 11;
	} else if ( 'required' == $options['showaddlinkdesc'] && empty( $captureddata['link_description'] ) ) {
		$requiredcheck = false;
		$message = 12;
	} else if ( 'required' == $options['showaddlinknotes'] && empty( $captureddata['link_notes'] ) ) {
		$requiredcheck = false;
		$message = 13;
	} else if ( 'required' == $options['showaddlinkreciprocal'] && empty( $captureddata['ll_reciprocal'] ) ) {
		$requiredcheck = false;
		$message = 14;
	} else if ( 'required' == $options['showaddlinksecondurl'] && empty( $captureddata['ll_secondwebaddr'] ) ) {
		$requiredcheck = false;
		$message = 15;
	} else if ( 'required' == $options['showaddlinktelephone'] && empty( $captureddata['ll_telephone'] ) ) {
		$requiredcheck = false;
		$message = 16;
	} else if ( 'required' == $options['showaddlinkemail'] && empty( $captureddata['ll_email'] ) ) {
		$requiredcheck = false;
		$message = 17;
	} else if ( 'required' == $options['showlinksubmittername'] && empty( $captureddata['ll_submittername'] ) ) {
		$requiredcheck = false;
		$message = 18;
	} else if ( 'required' == $options['showaddlinksubmitteremail'] && empty( $captureddata['ll_submitteremail'] ) ) {
		$requiredcheck = false;
		$message = 19;
	} else if ( 'required' == $options['showlinksubmittercomment'] && empty( $captureddata['ll_submittercomment'] ) ) {
		$requiredcheck = false;
		$message = 20;
	} else if ( 'required' == $options['showuserlargedescription'] && empty( $captureddata['link_textfield'] ) ) {
		$requiredcheck = false;
		$message = 21;
	} else if ( 'required' == $options['showaddlinkimage'] && !file_exists( $_FILES['linkimage']['tmp_name'] ) ) {
		$requiredcheck = false;
		$message = 25;
	} else if ( 'show' == $options['showaddlinkfile'] && !file_exists( $_FILES['linkfile']['tmp_name'] ) ) {
		$requiredcheck = false;
		$message = 26;
	} else if ( isset( $_FILES['linkimage'] ) && file_exists( $_FILES['linkimage']['tmp_name'] ) && !in_array( $image_file_ext, $allowed_image_extensions ) ) {
		$requiredcheck = false;
		$message = 27;
	} else if ( isset( $_FILES['linkfile'] ) && file_exists( $_FILES['linkfile']['tmp_name'] ) && !in_array( $link_file_ext, $allowed_link_file_extensions ) ) {
		$requiredcheck = false;
		$message = 28;
	} elseif ( isset( $_FILES['linkfile'] ) && file_exists( $_FILES['linkfile']['tmp_name'] ) && file_exists( $uploads['basedir'] . '/link-library-files/' . $_FILES['linkfile']['name'] ) ) {
		$requiredcheck = false;
		$message = 29;
	}

	if ( $captureddata['link_name'] != '' && $requiredcheck ) {
		if ( $options['addlinkakismet'] && ll_akismet_is_available() ) {
			$c = array();

			if ( !empty( $captureddata['ll_submittername'] ) ) {
				$c['comment_author'] = $captureddata['ll_submittername'];
			}

			if ( !empty( $captureddata['ll_submitteremail'] ) ) {
				$c['comment_author_email'] = $captureddata['ll_submitteremail'];
			}

			if ( !empty( $captureddata['link_url'] ) ) {
				$c['comment_author_url'] = $captureddata['link_url'];
			}

			if ( !empty( $captureddata['link_description'] ) && ( 'required' == $options['showaddlinkdesc'] || 'show' == $options['showaddlinkdesc'] ) ) {
				$c['comment_content'] = $captureddata['link_description'];

			} elseif ( !empty( $captureddata['link_notes'] ) && ( 'required' == $options['showaddlinknotes'] || 'show' == $options['showaddlinknotes'] ) ) {
				$c['comment_content'] = $captureddata['link_notes'];
			} elseif ( !empty( $captureddata['link_textfield'] ) && ( 'required' == $options['showuserlargedescription'] || 'show' == $options['showuserlargedescription'] ) ) {
				$c['comment_content'] = $captureddata['link_textfield'];
			}

			$c['blog'] = get_option( 'home' );
			$c['blog_lang'] = get_locale();
			$c['blog_charset'] = get_option( 'blog_charset' );
			$c['user_ip'] = $_SERVER['REMOTE_ADDR'];
			$c['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$c['referrer'] = $_SERVER['HTTP_REFERER'];

			$c['comment_type'] = 'link-library';

			$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

			foreach ( $_SERVER as $key => $value ) {
				if ( ! in_array( $key, (array) $ignore ) )
					$c["$key"] = $value;
			}

			if ( ll_akismet_comment_check( $c ) ) {
				$valid = false;
				$message = 22;
			} else {
				$valid = true;
			};
		} elseif ( $options['addlinkakismet'] && !ll_akismet_is_available() ) {
			echo 'Akismet has been selected but is not available';
			die();
		}

		if ( $options['showcaptcha'] && !is_user_logged_in() && $valid ) {
			$message = apply_filters( 'link_library_verify_captcha', '' );
			if ( $message > 0 ) {
				$valid = false;
			} else {
				$valid = true;
			}
		}

		if ( $options['showcustomcaptcha'] == 'show' && !is_user_logged_in() && $valid ) {
			if ( $captureddata['ll_customcaptchaanswer'] == '' ) {
				$valid   = false;
				$message = 5;
			} else {
				if ( strtolower( $captureddata['ll_customcaptchaanswer'] ) == strtolower( $options['customcaptchaanswer'] ) ) {
					$valid = true;
				} else {
					$valid   = false;
					$message = 6;
				}
			}
		}

		if ( !empty( isset( $captureddata['ll_reciprocal'] ) && $captureddata['ll_reciprocal'] != '' ) ) {
			$parsed_new_reciprocal = parse_url( esc_url( $captureddata['ll_reciprocal'] ) );
			$reciprocal_domain = $parsed_new_reciprocal['host'];

			$parsed_main_site_url = parse_url( get_site_url() );
			$main_site_domain = $parsed_main_site_url['host'];

			if ( $reciprocal_domain == $main_site_domain ) {
				$valid = false;
				$message = 24;
			}
		}

		if ( $valid && $options['onereciprocaldomain'] && ( 'required' == $options['showaddlinkreciprocal'] || ( 'show' == $options['showaddlinkreciprocal'] && !empty( $captureddata['ll_reciprocal'] ) ) ) ) {

			$reciprocal_links = array('');
			$reciprocal_query = new WP_Query( array( 'post_type' => 'link_library_links', 'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ) ) );
			if ( $reciprocal_query->have_posts() ) {
				while ( $reciprocal_query->have_posts() ) {
					$reciprocal_query->the_post();
					$the_answer = get_post_meta( $reciprocal_query->ID, 'link_reciprocal' , true );
					$the_answer = trim( $the_answer );
					array_push( $reciprocal_links, $the_answer );
				}
			}

			wp_reset_postdata();
			$reciprocal_links = array_unique( $reciprocal_links );
			$reciprocal_links = sort( $reciprocal_links );

			foreach( $reciprocal_links as $recip_link ) {
				$parse_data = parse_url( $recip_link->link_reciprocal );
				if ( $reciprocal_domain == $parse_data['host'] ) {
					$valid = false;
					$message = 23;
					break;
				}
			}
		}

		if ( $valid ) {
			if ( 'hide' == $options['showaddlinkfile'] ) {
				$existinglinkquery = "SELECT * from " . $my_link_library_plugin->db_prefix() . "posts p, " . $my_link_library_plugin->db_prefix() . "postmeta pm where p.ID = pm.post_ID and pm.meta_key = 'link_url' and ";
				$existinglinkquery .= '( ';

				$existinglinkquery .= "p.post_title = '" . $captureddata['link_name'] . "' ";

				if ( ( $options['addlinknoaddress'] == false ) || ( $options['addlinknoaddress'] == true && $captureddata['link_url'] != "" ) ) {
					$existinglinkquery .= " or pm.meta_value = '" . $captureddata['link_url'] . "' ";
				}

				if ( $options['onelinkperdomain'] ) {
					$parsed_url = parse_url( $captureddata['link_url'] );
					$existinglinkquery .= " or pm.meta_value like '%" . $parsed_url['host'] . "%' ";
				}

				$existinglinkquery .= " )";

				$existinglink = $wpdb->get_var( $existinglinkquery );
			} elseif ( 'show' == $options['showaddlinkfile'] ) {
				$existinglink = '';
			}

			if ( empty( $existinglink ) && ( ( $options['addlinknoaddress'] == false && $captureddata['link_url'] != "" ) || $options['addlinknoaddress'] == true || $options['showaddlinkfile'] ) ) {

				$validcat = false;
				$newlinkcat = array();
				$newlinkcatlist = array();

				foreach ( $captureddata['link_category'] as $cat_element ) {
					if ( $cat_element == 'new' && !empty( $captureddata['link_user_category'] ) ) {

						$existingcat = get_term_by( 'name', $captureddata['link_user_category'], 'link_library_category' );

						if ( empty( $existingcat ) ) {
							$new_category = wp_insert_term( $captureddata['link_user_category'], 'link_library_category', array( 'description' => '', 'slug' => sanitize_text_field( $captureddata['link_user_category'] ) ) );

							$newlinkcat[] = $new_category['term_id'];
							$newlinkcatlist[$new_category['term_id']] = sanitize_text_field( $captureddata['link_user_category'] );
						} else {
							$newlinkcat[] = $existingcat->term_id;
							$newlinkcatlist[$existingcat->term_id] = $existingcat->name;
						}

						$message = 8;
						$validcat = true;
					} elseif ( $cat_element == 'new' && empty( $captureddata['link_user_category'] ) ) {
						$message  = 7;
					} else {
						$newlinkcat[] = $cat_element;
						$existingcat = get_term_by( 'id', $cat_element, 'link_library_category' );
						$newlinkcatlist[$existingcat->term_id] = $existingcat->name;

						$message = 8;

						$validcat = true;
					}
				}

				$newlinktags = array();
				$captured_tags = $captureddata['link_tags'];

				if ( !empty( $captured_tags ) ) {
					foreach ( $captured_tags as $tag_element ) {
						if ( $tag_element == 'new' && !empty( $captureddata['link_user_tags'] ) ) {
							$user_tag_array = explode( ',', $captureddata['link_user_tags'] );

							foreach( $user_tag_array as $user_tag ) {
								$existingtag = get_term_by( 'name', $user_tag, 'link_library_tags' );

								if ( empty( $existingtag ) ) {
									$new_tag = wp_insert_term( $user_tag, 'link_library_tags', array( 'description' => '', 'slug' => sanitize_text_field( $captureddata['link_user_tags'] ) ) );

									$newlinktags[] = $new_tag['term_id'];
								} else {
									$newlinktags[] = $existingtag->term_id;
								}
							}
						} elseif ( $cat_element == 'new' && empty( $captureddata['link_user_tags'] ) ) {
							$message  = 24;
						} else {
							$existingtagid = get_term_by( 'id', $tag_element, 'link_library_tags' );
							$newlinktags[] = $existingtagid->name;
						}
					}
				}

				if ( $validcat == true ) {
					$newlinkdesc = $captureddata['link_description'];
					if ( $options['showuserlinks'] == false ) {
						if ( $options['showifreciprocalvalid'] ) {
							$reciprocal_return = $my_link_library_plugin->CheckReciprocalLink( $genoptions['recipcheckaddress'], $captureddata['ll_reciprocal'] );

							if ( $reciprocal_return == 'exists_found' ) {
								$newlinkvisibility = 'publish';
								unset ( $message );
							} else {
								$newlinkvisibility = 'pending';
							}
						} else {
							$newlinkvisibility = 'pending';
						}
					} else {
						$newlinkvisibility = 'publish';
						unset ( $message );
					}

					$username = '';
					$current_user = wp_get_current_user();
					if ( $options['storelinksubmitter'] == true && $current_user ) {
						$username = $current_user->user_login;
					}

					if ( $current_user ) {
						if ( $genoptions['bp_log_activity'] && function_exists( 'bp_activity_add' ) ) {
							$action_message = $current_user->display_name . ' ' . __( 'added link', 'link-library' ) . ' ' . esc_html( stripslashes( $captureddata['link_name'] ) ) . ' ' . __( 'in category', 'link-library' ) . ' ';

							$catcounter = 1;
							foreach( $newlinkcatlist as $new_cat_id => $new_cat_name ) {
								if ( $catcounter > 1 ) {
									$action_message .= ', ';
								}

								if ( !empty( $genoptions['bp_link_page_url'] ) && !empty( $genoptions['bp_link_settings'] ) ) {
									$tempoptionname = "LinkLibraryPP" . $genoptions['bp_link_settings'];
									$tempoptions    = get_option( $tempoptionname );
									extract( $tempoptions );

									if ( $showonecatonly ) {
										if ( 'HTMLGET' == $showonecatmode ) {
											$cattext = '<a href="';

											if ( !empty( $genoptions['bp_link_page_url'] ) && strpos( $genoptions['bp_link_page_url'], '?' ) != false ) {
												$cattext .= $genoptions['bp_link_page_url'] . '&cat_id=';
											} elseif ( !empty( $genoptions['bp_link_page_url'] ) && strpos( $genoptions['bp_link_page_url'], '?' ) == false ) {
												$cattext .= $genoptions['bp_link_page_url'] . '?cat_id=';
											}

											$cattext .= $new_cat_id . '">';
										} elseif ( 'HTMLGETSLUG' == $showonecatmode ) {
											$temp_term = get_term_by( 'id', $new_cat_id, 'link_library_category' );
											$cattext = '<a href="';

											if ( !empty( $genoptions['bp_link_page_url'] ) && strpos( $genoptions['bp_link_page_url'], '?' ) != false ) {
												$cattext .= $genoptions['bp_link_page_url'] . '&cat=';
											} elseif ( !empty( $genoptions['bp_link_page_url'] ) && strpos( $genoptions['bp_link_page_url'], '?' ) == false ) {
												$cattext .= $genoptions['bp_link_page_url'] . '?cat=';
											} elseif ( empty( $genoptions['bp_link_page_url'] ) ) {
												$cattext .= '?cat=';
											}

											$cattext .= $temp_term->slug . '">';
										} elseif ( 'HTMLGETPERM' == $showonecatmode ) {
											$temp_term = get_term_by( 'id', $new_cat_id, 'link_library_category' );
											$cattext = '<a href="' . $genoptions['bp_link_page_url'] . '/' . $catname->slug . '">';
										}
									} else if ( $catanchor ) {
										if ( !$pagination ) {
											$temp_term = get_term_by( 'id', $new_cat_id, 'link_library_category' );

											$cattext = '<a href="' . $genoptions['bp_link_page_url'] . '/#' . $temp_term->slug . '">';
										}
									}

									$action_message .= $cattext;
								}

								$action_message .= $new_cat_name;

								if ( !empty( $genoptions['bp_link_page_url'] ) && !empty( $genoptions['bp_link_settings'] ) ) {
									$action_message .= '</a>';
								}

								$catcounter++;
							}

							bp_activity_add( array( 'action' => $action_message, 'component' => 'links', 'type' => 'created_link' ) );
						}
					}

					$new_link_data = array(
						'post_type' => 'link_library_links',
						'post_content' => '',
						'post_title' => esc_html( stripslashes( $captureddata['link_name'] ) ),
						'post_status' => $newlinkvisibility
					);

					$new_link_ID = wp_insert_post( $new_link_data );

					if ( !empty( $new_link_ID ) ) {

						if ( !empty( $newlinkcat ) ) {
							wp_set_post_terms( $new_link_ID, $newlinkcat, 'link_library_category', false );
						}

						if ( !empty( $newlinktags ) ) {
							wp_set_post_terms( $new_link_ID, $newlinktags, 'link_library_tags', false );
						}

						if ( isset( $_FILES['linkfile'] ) ) {
							if ( file_exists( $_FILES['linkfile']['tmp_name'] ) ) {
								$file_ext = strtolower( end( explode( '.', $_FILES['linkfile']['name'] ) ) );

								$extensions = explode( ',', $options['linkfileallowedtypes'] );

								if ( in_array( $file_ext, $extensions ) ) {
									if ( !file_exists( $uploads['basedir'] . '/link-library-files' ) ) {
										mkdir( $uploads['basedir'] . '/link-library-files' );
									}

									$target_link_file_name = $uploads['basedir'] . '/link-library-files/' . $_FILES['linkfile']['name'];
									$target_link_file_url = $uploads['baseurl'] . '/link-library-files/' . $_FILES['linkfile']['name'];
									move_uploaded_file( $_FILES['linkfile']['tmp_name'], $target_link_file_name );
									update_post_meta( $new_link_ID, 'link_url', $target_link_file_url );
								}
							}
						} else {
							update_post_meta( $new_link_ID, 'link_url', esc_url( stripslashes( $captureddata['link_url'] ) ) );
						}

						update_post_meta( $new_link_ID, 'link_target', $options['linktarget'] );
						update_post_meta( $new_link_ID, 'link_description', sanitize_text_field( $newlinkdesc ) );

						update_post_meta( $new_link_ID, 'link_updated', current_time( 'timestamp' ) );

						update_post_meta( $new_link_ID, 'link_notes', sanitize_text_field( $captureddata['link_notes'] ) );
						update_post_meta( $new_link_ID, 'link_rss', esc_url( stripslashes( $captureddata['link_rss'] ) ) );
						update_post_meta( $new_link_ID, 'link_second_url', esc_url( $captureddata['ll_secondwebaddr'] ) );
						update_post_meta( $new_link_ID, 'link_telephone', $captureddata['ll_telephone'] );
						update_post_meta( $new_link_ID, 'link_email', $captureddata['ll_email'] );

						update_post_meta( $new_link_ID, 'link_custom_url_1', esc_url( $captureddata['ll_customurl1'] ) );
						update_post_meta( $new_link_ID, 'link_custom_url_2', esc_url( $captureddata['ll_customurl2'] ) );
						update_post_meta( $new_link_ID, 'link_custom_url_3', esc_url( $captureddata['ll_customurl3'] ) );
						update_post_meta( $new_link_ID, 'link_custom_url_4', esc_url( $captureddata['ll_customurl4'] ) );
						update_post_meta( $new_link_ID, 'link_custom_url_5', esc_url( $captureddata['ll_customurl5'] ) );	
						
						update_post_meta( $new_link_ID, 'link_custom_text_1', sanitize_text_field( $captureddata['ll_customtext1'] ) );
						update_post_meta( $new_link_ID, 'link_custom_text_2', sanitize_text_field( $captureddata['ll_customtext2'] ) );
						update_post_meta( $new_link_ID, 'link_custom_text_3', sanitize_text_field( $captureddata['ll_customtext3'] ) );
						update_post_meta( $new_link_ID, 'link_custom_text_4', sanitize_text_field( $captureddata['ll_customtext4'] ) );
						update_post_meta( $new_link_ID, 'link_custom_text_5', sanitize_text_field( $captureddata['ll_customtext5'] ) );	

						update_post_meta( $new_link_ID, 'link_custom_list_1', sanitize_text_field( $captureddata['ll_customlist1'] ) );
						update_post_meta( $new_link_ID, 'link_custom_list_2', sanitize_text_field( $captureddata['ll_customlist2'] ) );
						update_post_meta( $new_link_ID, 'link_custom_list_3', sanitize_text_field( $captureddata['ll_customlist3'] ) );
						update_post_meta( $new_link_ID, 'link_custom_list_4', sanitize_text_field( $captureddata['ll_customlist4'] ) );
						update_post_meta( $new_link_ID, 'link_custom_list_5', sanitize_text_field( $captureddata['ll_customlist5'] ) );	

						update_post_meta( $new_link_ID, 'link_visits', 0 );
						update_post_meta( $new_link_ID, 'link_rating', 0 );
						update_post_meta( $new_link_ID, '_thumbs_rating_up', 0 );

						update_post_meta( $new_link_ID, 'link_reciprocal', $captureddata['ll_reciprocal'] );
						update_post_meta( $new_link_ID, 'link_submitter', ( isset( $username ) ? $username : null ) );
						update_post_meta( $new_link_ID, 'link_submitter_name', $captureddata['ll_submittername'] );
						update_post_meta( $new_link_ID, 'link_submitter_email', $captureddata['ll_submitteremail'] );
						
						update_post_meta( $new_link_ID, 'submitter_comment', $captureddata['ll_submittercomment'] );
						update_post_meta( $new_link_ID, 'link_reference', $captureddata['ll_linkreference'] );

						update_post_meta( $new_link_ID, 'link_submitter_comment', $submittercomment );

						update_post_meta( $new_link_ID, 'link_textfield', sanitize_text_field( $captureddata['link_textfield'] ) );

						update_post_meta( $new_link_ID, 'link_no_follow', false );
						update_post_meta( $new_link_ID, 'link_featured', 0 );
						update_post_meta( $new_link_ID, 'link_updated_manual', false );

						if ( isset( $_FILES['linkimage'] ) ) {
							if ( file_exists( $_FILES['linkimage']['tmp_name'] ) ) {
								$file_ext = strtolower( end( explode( '.', $_FILES['linkimage']['name'] ) ) );

								$extensions = array( 'jpeg', 'jpg', 'png' );

								if ( in_array( $file_ext, $extensions ) ){
									$uploads = wp_upload_dir();
									$target_file_name = $uploads['basedir'] . '/link-library-images/' . $new_link_ID . '.' . $file_ext;
									$target_image_url = $uploads['baseurl'] . '/link-library-images/' . $new_link_ID . '.' . $file_ext;

									move_uploaded_file( $_FILES['linkimage']['tmp_name'], $target_file_name );
									update_post_meta( $new_link_ID, 'link_image', $target_image_url );

									if ( empty( $target_image_url ) ) {
										delete_post_thumbnail( $new_link_ID );
									} else {
										$wpFileType = wp_check_filetype( $target_image_url, null);

										$attachment = array(
											'post_mime_type' => $wpFileType['type'],  // file type
											'post_title' => sanitize_file_name( $target_image_url ),  // sanitize and use image name as file name
											'post_content' => '',  // could use the image description here as the content
											'post_status' => 'inherit'
										);

										// insert and return attachment id
										$attachmentId = wp_insert_attachment( $attachment, $target_image_url, $new_link_ID );
										$attachmentData = wp_generate_attachment_metadata( $attachmentId, $target_image_url );
										wp_update_attachment_metadata( $attachmentId, $attachmentData );
										set_post_thumbnail( $new_link_ID, $attachmentId );
									}
								}
							}
						}
					}

					if ( $options['emailnewlink'] ) {
						if ( $genoptions['moderatoremail'] != '' ) {
							$adminmail = $genoptions['moderatoremail'];
						} else {
							$adminmail = get_option( 'admin_email' );
						}

						$link_category_name = '';
						$link_category_names_array = array();

						if ( !empty( $newlinkcat ) ) {
							foreach ( $newlinkcat as $link_cat ) {
								$existingcat = get_term_by( 'id', $link_cat, 'link_library_category' );
								if ( !empty( $existingcat ) ) {
									$link_category_names_array[] = $existingcat->name;
								}
							}

							if ( !empty( $link_category_names_array ) ) {
								$link_category_name = implode( ',', $link_category_names_array );
							}
						}

						$link_tags_name = '';
						$link_tags_names_array = array();

						if ( !empty( $newlinktags ) ) {
							foreach ( $newlinktags as $link_tag ) {
								$existingtag = get_term_by( 'id', $link_tag, 'link_library_tags' );
								if ( !empty( $existingtag ) ) {
									$link_tags_names_array[] = $existingtag->name;
								}
							}

							if ( !empty( $link_tags_names_array ) ) {
								$link_tags_name = implode( ',', $link_tags_names_array );
							}
						}

						$headers = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=utf-8\r\n";

						$emailmessage = __( 'A user submitted a new link to your Wordpress Link database.', 'link-library' ) . "<br /><br />";
						$emailmessage .= __( 'Link Name', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_name'] ) ) . "<br />";
						$emailmessage .= __( 'Link Address', 'link-library' ) . ": <a href='" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "'>" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "</a><br />";
						$emailmessage .= __( 'Link RSS', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_rss'] ) ) . "<br />";
						$emailmessage .= __( 'Link Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_description'] ) ) . "<br />";
						$emailmessage .= __( 'Link Large Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_textfield'] ) ) . "<br />";
						$emailmessage .= __( 'Link Notes', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_notes'] ) ) . "<br />";
						$emailmessage .= __( 'Link Category', 'link-library' ) . ": " . $link_category_name . "( " . implode( ', ', $captureddata['link_category'] ) . " )<br /><br />";
						if ( !empty( $link_tags_name ) ) {
							$emailmessage .= __( 'Link Tags', 'link-library' ) . ": " . $link_tags_name . "( " . implode( ', ', $captureddata['link_tags'] ) . " )<br /><br />";
						}

						$emailmessage .= __( 'Reciprocal Link', 'link-library' ) . ": " . $captureddata['ll_reciprocal'] . "<br /><br />";
						$emailmessage .= __( 'Link Secondary Address', 'link-library' ) . ": " . $captureddata['ll_secondwebaddr'] . "<br /><br />";
						$emailmessage .= __( 'Link Telephone', 'link-library' ) . ": " . $captureddata['ll_telephone'] . "<br /><br />";
						$emailmessage .= __( 'Link E-mail', 'link-library' ) . ": " . $captureddata['ll_email'] . "<br /><br />";
						$emailmessage .= __( 'Link Submitter', 'link-library' ) . ": " . $username . "<br /><br />";
						$emailmessage .= __( 'Link Submitter Name', 'link-library' ) . ": " . $captureddata['ll_submittername'] . "<br /><br />";
						$emailmessage .= __( 'Link Submitter E-mail', 'link-library' ) . ": " . $captureddata['ll_submitteremail'] . "<br /><br />";
						
						$emailmessage .= __( 'Link Comment', 'link-library' ) . ": " . $captureddata['ll_submittercomment'] . "<br /><br />";

						$emailmessage .= __( 'Referenced Link', 'link-library' ) . ": ";

						if ( !empty( $captureddata['ll_linkreference'] ) ) {
							$referenced_link = get_posts( array( 'post_type' => 'link_library_links', 'include' => array( $captureddata['ll_linkreference'] ), 'numberposts' => 1 ) );

							if ( !empty( $referenced_link ) ) {
								 $emailmessage .= $referenced_link[0]->post_title . "<br /><br />";
							}							
						}						

						for ( $customurlfieldnumber = 1; $customurlfieldnumber < 6; $customurlfieldnumber++ ) {
							if ( $genoptions['customurl' . $customurlfieldnumber . 'active'] ) {
								$emailmessage .= $genoptions['customurl' . $customurlfieldnumber . 'label'] . ': ' . esc_url( $captureddata['ll_customurl' . $customurlfieldnumber] ) . '<br /><br />';
							}
						}	
						
						for ( $customtextfieldnumber = 1; $customtextfieldnumber < 6; $customtextfieldnumber++ ) {
							if ( $genoptions['customtext' . $customtextfieldnumber . 'active'] ) {
								$emailmessage .= $genoptions['customtext' . $customtextfieldnumber . 'label'] . ': ' . sanitize_text_field( $captureddata['ll_customtext' . $customtextfieldnumber] ) . '<br /><br />';
							}
						}

						for ( $customlistfieldnumber = 1; $customlistfieldnumber < 6; $customlistfieldnumber++ ) {
							if ( $genoptions['customlist' . $customlistfieldnumber . 'active'] ) {
								$emailmessage .= $genoptions['customlist' . $customlistfieldnumber . 'label'] . ': ' . sanitize_text_field( $captureddata['ll_customlist' . $customlistfieldnumber] ) . '<br /><br />';
							}
						}	

						if ( $options['showuserlinks'] == false ) {
							$emailmessage .= '<a href="' . esc_url( add_query_arg( array( 'post_type' => 'link_library_links', 'page' => 'link-library-moderate' ), admin_url( 'edit.php' ) ) ) . '">Moderate new links</a>';
						} elseif ( $options['showuserlinks'] == true ) {
							$emailmessage .= '<a href="' . esc_url( add_query_arg( 'post_type', 'link_library_links', admin_url( 'edit.php' ) ) ) . '">View links</a>';
						}

						if ( !$genoptions['suppressemailfooter'] ) {
							$emailmessage .= "<br /><br />" . __('Message generated by', 'link-library') . " <a href='https://ylefebvre.github.io/wordpress-plugins/link-library/'>Link Library</a> for Wordpress";
						}

						if ( !empty( $genoptions['moderationnotificationtitle'] ) ) {
							$emailtitle = stripslashes( $genoptions['moderationnotificationtitle'] );
							$emailtitle = str_replace( '%linkname%', esc_html( stripslashes( $captureddata['link_name'] ) ), $emailtitle );
						} else {
							$emailtitle = htmlspecialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . " - " . __( 'New link added', 'link-library' ) . ": " . htmlspecialchars( $captureddata['link_name'] );
						}

						wp_mail( $adminmail, $emailtitle, $emailmessage, $headers );
					}

					if ( $options['emailsubmitter'] && !empty( $captureddata['ll_submitteremail'] ) && is_email( $captureddata['ll_submitteremail'] ) ) {
						$submitteremailheaders = "MIME-Version: 1.0\r\n";
						$submitteremailheaders .= "Content-type: text/html; charset=utf-8\r\n";

						if ( $genoptions['moderatorname'] != '' && $genoptions['moderatoremail'] != '' ) {
							$submitteremailheaders .= "From: \"" . $genoptions['moderatorname'] . "\" <" . $genoptions['moderatoremail'] . ">\n";
						}

						$submitteremailtitle = __( 'Link Submission Confirmation', 'link-library' );

						$submitteremailmessage = '<p>' . __( 'Thank you for your link submission on ', 'link-library' );
						$submitteremailmessage .= esc_html( get_bloginfo( 'name' ) ) . '</p>';

						if ( $options['showuserlinks'] == false ) {
							$submitteremailmessage .= '<p>' . __( 'Your link will appear once approved by the site administrator.', 'link-library' ) . '</p>';
						} elseif ( $options['showuserlinks'] == true ) {
							$submitteremailmessage .= '<p>' . __( 'Your link will immediately be added to the site.', 'link-library' ) . '</p>';
						}

						if ( !empty( $options['emailextracontent'] ) ) {
							$submitteremailmessage .= '<p>' . $options['emailextracontent'] . '</p>';
						}

						$submitteremailmessage .= __( 'Link Name', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_name'] ) ) . "<br />";
						$submitteremailmessage .= __( 'Link Address', 'link-library' ) . ": <a href='" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "'>" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "</a><br />";

						if ( 'show' == $options['showaddlinkrss'] || 'required' == $options['showaddlinkrss'] ) {
							$submitteremailmessage .= __( 'Link RSS', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_rss'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinkdesc'] || 'required' == $options['showaddlinkdesc'] ) {
							$submitteremailmessage .= __( 'Link Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_description'] ) ) . "<br />";
						}

						if ( 'show' == $options['showuserlargedescription'] || 'required' == $options['showuserlargedescription'] ) {
							$submitteremailmessage .= __( 'Link Large Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_textfield'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinknotes'] || 'required' == $options['showaddlinknotes'] ) {
							$submitteremailmessage .= __( 'Link Notes', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_notes'] ) ) . "<br />";
						}

						$submitteremailmessage .= __( 'Link Category', 'link-library' ) . ": " . $link_category_name . " ( " . $captureddata['link_category'] . " )<br /><br />";
						$submitteremailmessage .= __( 'Link Tags', 'link-library' ) . ": " . $link_tags_name_name . " ( " . $captureddata['link_tags'] . " )<br /><br />";

						if ( 'show' == $options['showaddlinkreciprocal'] || 'required' == $options['showaddlinkreciprocal'] ) {
							$submitteremailmessage .= __( 'Reciprocal Link', 'link-library' ) . ": " . $captureddata['ll_reciprocal'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksecondurl'] || 'required' == $options['showaddlinksecondurl'] ) {
							$submitteremailmessage .= __( 'Link Secondary Address', 'link-library' ) . ": " . $captureddata['ll_secondwebaddr'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinktelephone'] || 'required' == $options['showaddlinktelephone'] ) {
							$submitteremailmessage .= __( 'Link Telephone', 'link-library' ) . ": " . $captureddata['ll_telephone'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinkemail'] || 'required' == $options['showaddlinkemail'] ) {
							$submitteremailmessage .= __( 'Link E-mail', 'link-library' ) . ": " . $captureddata['ll_email'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinksubmittername'] || 'required' == $options['showlinksubmittername'] ) {
							$submitteremailmessage .= __( 'Link Submitter Name', 'link-library' ) . ": " . $captureddata['ll_submittername'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksubmitteremail'] || 'required' == $options['showaddlinksubmitteremail'] ) {
							$submitteremailmessage .= __( 'Link Submitter E-mail', 'link-library' ) . ": " . $captureddata['ll_submitteremail'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinksubmittercomment'] || 'required' == $options['showlinksubmittercomment'] ) {
							$submitteremailmessage .= __( 'Link Comment', 'link-library' ) . ": " . $captureddata['ll_submittercomment'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinkreferencelist'] || 'required' == $options['showlinkreferencelist'] ) {
							$submitteremailmessage .= __( 'Referenced Link', 'link-library' ) . ": ";
							if ( !empty( $captureddata['ll_linkreference'] ) ) {
								$referenced_link = get_posts( array( 'post_type' => 'link_library_links', 'include' => array( $captureddata['ll_linkreference'] ), 'numberposts' => 1 ) );
	
								if ( !empty( $referenced_link ) ) {
									$submitteremailmessage .= $referenced_link[0]->post_title . "<br /><br />";
								}							
							}
						}
						
						for ( $customurlfieldnumber = 1; $customurlfieldnumber < 6; $customurlfieldnumber++ ) {
							if ( 'show' == $options['showcustomurl' . $customurlfieldnumber] || 'required' == $options['showcustomurl' . $customurlfieldnumber] ) {
								if ( $genoptions['customurl' . $customurlfieldnumber . 'active'] ) {
									$emailmessage .= $genoptions['customurl' . $customurlfieldnumber . 'label'] . ': ' . esc_url( $captureddata['ll_customurl' . $customurlfieldnumber] ) . '<br /><br />';
								}
							}
						}

						for ( $customtextfieldnumber = 1; $customtextfieldnumber < 6; $customtextfieldnumber++ ) {
							if ( 'show' == $options['showcustomtext' . $customtextfieldnumber] || 'required' == $options['showcustomtext' . $customtextfieldnumber] ) {
								if ( $genoptions['customtext' . $customtextfieldnumber . 'active'] ) {
									$emailmessage .= $genoptions['customtext' . $customtextfieldnumber . 'label'] . ': ' . sanitize_text_field( $captureddata['ll_customtext' . $customtextfieldnumber] ) . '<br /><br />';
								}
							}
						}

						for ( $customlistfieldnumber = 1; $customlistfieldnumber < 6; $customlistfieldnumber++ ) {
							if ( 'show' == $options['showcustomlist' . $customlistfieldnumber] || 'required' == $options['showcustomlist' . $customlistfieldnumber] ) {
								if ( $genoptions['customlist' . $customlistfieldnumber . 'active'] ) {
									$emailmessage .= $genoptions['customlist' . $customlistfieldnumber . 'label'] . ': ' . sanitize_text_field( $captureddata['ll_customlist' . $customlistfieldnumber] ) . '<br /><br />';
								}
							}
						}

						wp_mail( $captureddata['ll_submitteremail'], $submitteremailtitle, $submitteremailmessage, $submitteremailheaders );
					}
				}
			} elseif ( $existinglink == "" && ( $options['addlinknoaddress'] == false && $captureddata['link_url'] == "" ) && 'hide' == $options['showaddlinkfile']) {
				$message = 9;
				$valid = false;
			} else {
				$message = 10;
				$valid = false;
			}
		}
	}

	$redirectaddress = '';

	if ( isset( $_POST['thankyouurl'] ) && $_POST['thankyouurl'] != '' && $requiredcheck && $valid ) {
		$redirectaddress = $_POST['thankyouurl'];
	} else {
		if ( isset ( $_POST['pageid'] ) && is_numeric( $_POST['pageid'] ) ) {
			$redirectaddress = get_permalink( $_POST['pageid'] );
		}
	}

	if ( $valid == true ) {
		$captureddata = array();
	}
	$captureddata['message'] = $message;

	$nonce = wp_generate_password( 12, false );
	set_transient( 'll_user_form_' . $nonce, $captureddata, 10 );
	$redirectaddress = esc_url_raw( add_query_arg( 'formdata', 'll_user_form_' . $nonce, $redirectaddress ) );

	wp_redirect( $redirectaddress );
	exit;
}

function link_library_verify_captcha() {

	$message = 0;

	$generaloptions = get_option( 'LinkLibraryGeneral' );
	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );

	if ( 'easycaptcha' == $generaloptions['captchagenerator'] ) {
		if ( empty( $_REQUEST['confirm_code'] ) ) {
			$message = 1;
		} else {
			if ( isset( $_COOKIE['Captcha'] ) ) {
				list( $Hash, $Time ) = explode( '.', $_COOKIE['Captcha'] );
				if ( md5( "ORHFUKELFPTUEODKFJ" . $_REQUEST['confirm_code'] . $_SERVER['REMOTE_ADDR'] . $Time ) != $Hash ) {
					$message = 2;
				} elseif ( ( time() - 5 * 60 ) > $Time ) {
					$message = 3;
				}
			} else {
				$message = 4;
			}
		}
	} elseif ( 'recaptcha' == $generaloptions['captchagenerator'] && !empty( $generaloptions['recaptchasecretkey'] ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/recaptcha/autoload.php';
		$recaptcha = new \ReCaptcha\ReCaptcha( $generaloptions['recaptchasecretkey'] );
		$resp = $recaptcha->verify( $_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );
		if ( ! $resp->isSuccess() ) {
			$message = 2;
		}
	}

	return $message;
}

add_filter( 'link_library_verify_captcha', 'link_library_verify_captcha' );

function ll_akismet_is_available() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) { // Akismet v3.0+
		return (bool) Akismet::get_api_key();
	}

	if ( function_exists( 'akismet_get_key' ) ) {
		return (bool) akismet_get_key();
	}

	return false;
}

function ll_akismet_comment_check( $comment ) {
	global $akismet_api_host, $akismet_api_port;

	$spam = false;
	$query_string = http_build_query( $comment );

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) { // Akismet v3.0+
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		$response = akismet_http_post( $query_string, $akismet_api_host,
			'/1.1/comment-check', $akismet_api_port );
	}

	if ( 'true' == $response[1] ) {
		$spam = true;
	}

	if ( class_exists( 'WPCF7_Submission' ) && $submission = WPCF7_Submission::get_instance() ) {
		$submission->akismet = array( 'comment' => $comment, 'spam' => $spam );
	}

	return apply_filters( 'll_akismet_comment_check', $spam, $comment );
}
