<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once plugin_dir_path( __FILE__ ) . 'link-library-defaults.php';

function addlink_render_category_list( $categories, $select_name, $depth, $order, $libraryoptions, $captureddata ) {

	$output = '';
	if ( !empty( $categories ) ) {
		if ( 0 == $depth ) {
			$output .= '<select data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
			$output .= ' id="' . $select_name . '" name="' . $select_name . '[]" ';
			if ( 'selectmultiple' == $libraryoptions['showaddlinkcat'] ) {
				$number_of_categories = sizeof( $categories );
				if ( $libraryoptions['addlinkcustomcat'] ) {
					$number_of_categories++;
				}
				$output .= 'multiple size="' . ( $number_of_categories < 10 ? $number_of_categories : 10 ) . '" style="height: auto"';
			}
			$output .= '>';

			if ( 'nodefaultcat' == $libraryoptions['addlinkdefaultcat'] && 'show' == $libraryoptions['showaddlinkcat'] ) {
				$output .= '<option value="">' . $libraryoptions['userlinkcatselectionlabel'] . '</option>';
			}

		}

		foreach ( $categories as $category ) {
			$output .= '<option value="' . $category->term_id . '" ';

			if ( isset( $captureddata['link_category'] ) && in_array( $category->term_id, $captureddata['link_category'] ) ) {
				$output .= "selected";
			} elseif ( 'nodefaultcat' != $libraryoptions['addlinkdefaultcat'] && $category->term_id == intval( $libraryoptions['addlinkdefaultcat'] ) ) {
				$output .= "selected";
			}

			$output .= '>' . str_repeat( '&nbsp;', 4 * $depth ) . $category->name . '</option>';
			$child_categories = get_terms( 'link_library_category', array( 'orderby' => 'name', 'parent' => $category->term_id, 'order' => $order, 'hide_empty' => false ) );

			if ( !empty( $child_categories ) ) {
				$output .= addlink_render_category_list( $child_categories, $select_name, $depth + 1, $order, $libraryoptions, $captureddata );
			}
		}

		if ( 0 == $depth ) {
			if ( empty( $libraryoptions['linkcustomcatlistentry'] ) ) {
				$linkcustomcatlistentry = __( 'User-submitted category (define below)', 'link-library' );
			} else {
				$linkcustomcatlistentry = $libraryoptions['linkcustomcatlistentry'];
			}

			if ( 'show' == $libraryoptions['addlinkcustomcat'] ) {
				$output .= '<OPTION VALUE="new">' . stripslashes( $linkcustomcatlistentry ) . "\n";
			}

			$output .= '</select>';
		}

	} else {
		$output .= _e( 'No link categories! Create some!', 'link-library' );
	}

	return $output;
}

function RenderLinkLibraryAddLinkForm( $LLPluginClass, $generaloptions, $libraryoptions, $settings, $code ) {

	wp_enqueue_script( 'form-validator' );
	wp_enqueue_script( 'tiptip' );
	wp_enqueue_style( 'tiptipstyle', plugins_url( '/tiptip/tipTip.css', __FILE__ ) );
	$output = '';

	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );
	extract( $generaloptions );

	$libraryoptions = wp_parse_args( $libraryoptions, ll_reset_options( 1, 'list', 'return' ) );

	if ( $libraryoptions['showaddlinkrss'] === false ) {
		$libraryoptions['showaddlinkrss'] = 'hide';
	} elseif ( $libraryoptions['showaddlinkrss'] === true ) {
		$libraryoptions['showaddlinkrss'] = 'show';
	}

	if ( $libraryoptions['showaddlinkdesc'] === false ) {
		$libraryoptions['showaddlinkdesc'] = 'hide';
	} elseif ( $libraryoptions['showaddlinkdesc'] === true ) {
		$libraryoptions['showaddlinkdesc'] = 'show';
	}

	if ( $libraryoptions['showaddlinkcat'] === false ) {
		$libraryoptions['showaddlinkcat'] = 'hide';
	} elseif ( $libraryoptions['showaddlinkcat'] === true ) {
		$libraryoptions['showaddlinkcat'] = 'show';
	}

	if ( $libraryoptions['showaddlinknotes'] === false ) {
		$libraryoptions['showaddlinknotes'] = 'hide';
	} elseif ( $libraryoptions['showaddlinknotes'] === true ) {
		$libraryoptions['showaddlinknotes'] = 'show';
	}

	if ( $libraryoptions['addlinkcustomcat'] === false ) {
		$libraryoptions['addlinkcustomcat'] = 'hide';
	} elseif ( $libraryoptions['addlinkcustomcat'] === true ) {
		$libraryoptions['addlinkcustomcat'] = 'show';
	}

	if ( $libraryoptions['showaddlinkreciprocal'] === false ) {
		$libraryoptions['showaddlinkreciprocal'] = 'hide';
	} elseif ( $libraryoptions['showaddlinkreciprocal'] === true ) {
		$libraryoptions['showaddlinkreciprocal'] = 'show';
	}

	if ( $libraryoptions['showaddlinksecondurl'] === false ) {
		$libraryoptions['showaddlinksecondurl'] = 'hide';
	} elseif ( $libraryoptions['showaddlinksecondurl'] === true ) {
		$libraryoptions['showaddlinksecondurl'] = 'show';
	}

	if ( $libraryoptions['showaddlinktelephone'] === false ) {
		$libraryoptions['showaddlinktelephone'] = 'hide';
	} elseif ( $libraryoptions['showaddlinktelephone'] === true ) {
		$libraryoptions['showaddlinktelephone'] = 'show';
	}

	if ( $libraryoptions['showaddlinkemail'] === false ) {
		$libraryoptions['showaddlinkemail'] = 'hide';
	} elseif ( $libraryoptions['showaddlinkemail'] === true ) {
		$libraryoptions['showaddlinkemail'] = 'show';
	}

	if ( $libraryoptions['showlinksubmittername'] === false ) {
		$libraryoptions['showlinksubmittername'] = 'hide';
	} elseif ( $libraryoptions['showlinksubmittername'] === true ) {
		$libraryoptions['showlinksubmittername'] = 'show';
	}

	if ( $libraryoptions['showaddlinksubmitteremail'] === false ) {
		$libraryoptions['showaddlinksubmitteremail'] = 'hide';
	} elseif ( $libraryoptions['showaddlinksubmitteremail'] === true ) {
		$libraryoptions['showaddlinksubmitteremail'] = 'show';
	}

	if ( $libraryoptions['showlinksubmittercomment'] === false ) {
		$libraryoptions['showlinksubmittercomment'] = 'hide';
	} elseif ( $libraryoptions['showlinksubmittercomment'] === true ) {
		$libraryoptions['showlinksubmittercomment'] = 'show';
	}

	if ( $libraryoptions['showcustomcaptcha'] === false ) {
		$libraryoptions['showcustomcaptcha'] = 'hide';
	} elseif ( $libraryoptions['showcustomcaptcha'] === true ) {
		$libraryoptions['showcustomcaptcha'] = 'show';
	}

	if ( $libraryoptions['showuserlargedescription'] === false ) {
		$libraryoptions['showuserlargedescription'] = 'hide';
	} elseif ( $libraryoptions['showuserlargedescription'] === true ) {
		$libraryoptions['showuserlargedescription'] = 'show';
	}

	extract( $libraryoptions );

	/* This case will only happen if the user entered bad data in the admin page or if someone is trying to inject bad data in SQL query */
	if ( !empty( $categorylist ) ) {
		$categorylistarray = explode( ',', $categorylist );

		if ( true === array_filter( $categorylistarray, 'is_int' ) ) {
			return 'List of requested categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	if ( !empty( $excludecategorylist ) ) {
		$excludecategorylistarray = explode( ',', $excludecategorylist );

		if ( true === array_filter( $excludecategorylistarray, 'is_int' ) ) {
			return 'List of requested excluded categories is invalid. Please go back to Link Library admin panel to correct.';
		}
	}

	$captureddata = array();
	if ( isset( $_GET['formdata'] ) && !empty( $_GET['formdata'] ) ) {
		$captureddata = get_transient( $_GET['formdata'] );
	}

	if ( 'link-library-addlink' == $code || 'addlink-link-library' == $code || 'link-library-addlinkcustommsg' == $code || 'addlinkcustommsg-link-library' == $code ) {
		if ( isset( $captureddata['message'] ) && !empty( $captureddata['message'] ) ) {
			if ( 1 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Confirm code not given', 'link-library') . '.</div>';
			} elseif ( 2 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Captcha code is wrong', 'link-library') . '.</div>';
			} elseif ( 3 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Captcha code is only valid for 5 minutes', 'link-library') . '.</div>';
			} elseif ( 4 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('No captcha cookie given. Make sure cookies are enabled', 'link-library') . '.</div>';
			} elseif ( 5 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Captcha answer was not provided.', 'link-library') . '</div>';
			} elseif ( 6 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Captcha answer is incorrect', 'link-library') . '.</div>';
			} elseif ( 7 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('User Category was not provided correctly. Link insertion failed.', 'link-library') . '</div>';
			} elseif ( 8 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $newlinkmsg;
				if ( !$showuserlinks ) {
					$output .= ' ' . $moderatemsg;
				}
				$output .= '</div>';
			} elseif ( 9 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Error: Link does not have an address.', 'link-library') . '</div>';
			} elseif ( 10 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Error: Link already exists.', 'link-library') . '</div>';
			} elseif ( 11 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkrsslabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 12 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkdesclabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 13 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linknoteslabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 14 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkreciprocallabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 15 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linksecondurllabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 16 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linktelephonelabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 17 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkemaillabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 18 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linksubmitternamelabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 19 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linksubmitteremaillabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 20 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linksubmittercommentlabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 21 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linklargedesclabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 22 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Link submission error', 'link-library') . '</div>';
			} elseif ( 23 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Link rejected. There is already a site with this reciprocal link.', 'link-library') . '</div>';
			} elseif ( 24 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __('Link rejected. Invalid reciprocal link.', 'link-library') . '</div>';
			} elseif ( 25 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkimagelabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 26 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . $libraryoptions['linkfilelabel'] . __(' is a required field', 'link-library') . '</div>';
			} elseif ( 27 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __( 'Invalid image file extension', 'link-library') . '</div>';
			} elseif ( 28 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __( 'Invalid link file extension', 'link-library') . '</div>';
			} elseif ( 29 == $captureddata['message'] ) {
				$output .= '<div class="llmessage">' . __( 'A link file with this name already exists', 'link-library') . '</div>';
			}
		}
	}

	if ( ( 'link-library-addlink' == $code || 'addlink-link-library' == $code ) && ( ( $addlinkreqlogin && current_user_can( 'read' ) ) || !$addlinkreqlogin ) ) {

		$output .= '<form enctype="multipart/form-data" method="post" id="lladdlink" action="">';
		$output .= '<input type="hidden" name="MAX_FILE_SIZE" value="' . wp_max_upload_size() . '" />';

		$output .= wp_nonce_field( 'LL_ADDLINK_FORM', '_wpnonce', true, false );
		$output .= '<input type="hidden" name="thankyouurl" value="' . $linksubmissionthankyouurl . '" />';
		$output .= '<input type="hidden" name="link_library_user_link_submission" value="1" />';

		global $wp_query;
		if ( isset( $wp_query->post->ID ) && !empty( $wp_query->post->ID ) ) {
			$thePostID = $wp_query->post->ID;
			$output .= '<input type="hidden" name="pageid" value="' . $thePostID . '" />';
		}

		$output .= '<input type="hidden" name="settingsid" value="' . $settings . '" />';

		$xpath = $LLPluginClass->relativePath( dirname( __FILE__ ), ABSPATH );
		$output .= '<input type="hidden" name="xpath" value="' . esc_attr( $xpath ) . '" />';
		unset( $xpath );

		$output .= "<div class='lladdlink'>\n";

		if ( empty( $addnewlinkmsg ) ) {
			$addnewlinkmsg = __( 'Add new link', 'link-library' );
		}

		$output .= '<div id="lladdlinktitle">' . $addnewlinkmsg . "</div>\n";

		$output .= "<table>\n";

		$dragndroparray = explode( ',', $libraryoptions['usersubmissiondragndroporder'] );

		foreach ( range( 1, 34 ) as $new_entry ) {
			if ( !in_array( $new_entry, $dragndroparray ) ) {
				$dragndroparray[] = $new_entry;
			}
		}

		if ( $dragndroparray ) {
			foreach ( $dragndroparray as $arrayelements ) {
				switch ( $arrayelements ) {
					case 1: 	//------------------ Link Name --------------------
						if ( empty( $linknamelabel ) ) {
							$linknamelabel = __( 'Link name', 'link-library' );
						}
				
						$output .= '<tr><th><label for="link_name">' . $linknamelabel . '</label></th><td ';
						if ( !empty( $linknametooltip ) ) {
							$output .= 'class="lltooltip" title="' . $linknametooltip . '"';
						}
				
						$output .= '><input data-validation="required length" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" type="text" name="link_name" id="link_name" value="' . ( isset( $captureddata['link_name'] ) ? esc_html( stripslashes( $captureddata['link_name'] ), '1' ) : '') . "\" /></td></tr>\n";
					break;
					case 2: 	//------------------ Link Address --------------------
						if ( $showaddlinkfile == 'hide' ) {
							if ( empty( $linkaddrlabel ) ) {
								$linkaddrlabel = __( 'Link address', 'link-library' );
							}
				
							$output .= '<tr><th><label for="link_url">' . $linkaddrlabel . '</label></th><td ';
				
							if ( !empty( $linkaddrtooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkaddrtooltip . '"';
							}
				
							$output .= '><input ';
							if ( !$addlinknoaddress ) {
								$output .= 'data-validation="required url length" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, URL', 'link-library' ) . '" ';
							}
							$output .= 'type="text" name="link_url" id="link_url" value="' . ( isset( $captureddata['link_url'] ) ? esc_html( stripslashes( $captureddata['link_url'] ), '1') : $linkaddrdefvalue ) . "\" /></td></tr>\n";
						}
					break; 
					case 3:     //------------------ Link File --------------------
						if ( $showaddlinkfile == 'show' ) {
							if ( empty( $linkfilelabel ) ) {
								$linkfilelabel = __( 'Link File', 'link-library' );
							}
				
							$output .= '<tr><th><label for="linkfile">' . $linkfilelabel . '</label></th><td ';
				
							if ( !empty( $linkfiletooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkfiletooltip . '"';
							}
				
							$output .= '>';
							$output .= '<input type="file" name="linkfile" id="linkfile" data-validation="required" data-validation-error-msg-required="' . __( 'Need to upload a file', 'link-library' ) . '">';
				
							$output .= "</td></tr>\n";
						}
					break;
					case 4:      //------------------ Link RSS --------------------         
						if ( 'show' == $showaddlinkrss || 'required' == $showaddlinkrss) {
							if ( empty( $linkrsslabel ) ) {
								$linkrsslabel = __( 'Link RSS', 'link-library' );
							}
				
							$output .= '<tr><th><label for="link_rss">' . $linkrsslabel . '</label></th><td ';
				
							if ( !empty( $linkrsstooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkrsstooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinkrss ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, 1-1024 chars', 'link-library' ) . '" ';
				
							$output .= ' type="text" name="link_rss" id="link_rss" value="' . ( isset( $captureddata['link_rss'] ) ? esc_html( stripslashes( $captureddata['link_rss'] ), '1') : '' ) . "\" /></td></tr>\n";
						}
					break;
					case 5: 	//------------------ Link Categories --------------------
						$include_links_array = array( );
						if ( !empty( $categorylist_cpt ) ) {
							$include_links_array = explode( ',', $categorylist_cpt );
						}

						if ( !empty( $excludecategorylist_cpt ) ) {
							$excluded_links_array = explode( ',', $excludecategorylist_cpt );
						}

						$link_categories_query_args = array( 'hide_empty' => false );
						if ( empty( $categorylist_cpt ) && empty( $excludecategorylist_cpt ) ) {
							$link_categories_query_args['parent'] = 0;
						}
						if ( !empty( $include_links_array ) ) {
							$link_categories_query_args['include'] = $include_links_array;
						}

						if ( !empty( $excluded_links_array ) ) {
							$link_categories_query_args['exclude'] = $excluded_links_array;
						}

						$linkcats = get_terms( 'link_library_category', $link_categories_query_args );

						if ( !empty( $include_links_array ) && !empty( $excluded_links_array ) ) {
							foreach( $linkcats as $link_key => $linkcat ) {
								foreach( $excluded_links_array as $excludedcat ) {
									if ( $linkcat->term_id == $excludedcat ) {
										unset( $linkcats[$link_key] );
									}
								}
							}
						}

						if ( $debugmode ) {
							$output .= "\n<!-- Category query for add link form:" . print_r($linkcatquery, TRUE) . "-->\n\n";
							$output .= "\n<!-- Results of Category query for add link form:" . print_r($linkcats, TRUE) . "-->\n";
						}

						if ( $linkcats ) {
							if ( 'show' == $libraryoptions['showaddlinkcat'] || 'selectmultiple' == $libraryoptions['showaddlinkcat'] ) {
								if ( empty( $linkcatlabel ) ) {
									$linkcatlabel = __( 'Link category', 'link-library' );
								}

								$output .= '<tr><th><label for="link_category">' . $linkcatlabel;
								if ( 'selectmultiple' == $libraryoptions['showaddlinkcat'] ) {
									$output .= '<br /><br /><span class="multiselecthelp">';
									$output .= __( 'Use control-click (Windows) or command-click (Mac) to select multiple', 'link-library' );
									$output .= '</span>';
								}
								$output .= '</label></th><td ';

								if ( !empty( $linkcattooltip ) ) {
									$output .= 'class="lltooltip" title="' . $linkcattooltip . '"';
								}

								$output .= '>';

								$output .= addlink_render_category_list( $linkcats, 'link_category', 0, 'ASC', $libraryoptions, $captureddata );

								$output .= "</td></tr>\n";
							} else {
								$output .= '<input type="hidden" name="link_category[]" id="link_category" value="';
								if ( 'nodefaultcat' == $addlinkdefaultcat ) {
									$output .= $linkcats[0]->term_id;
								} else {
									$output .= intval( $addlinkdefaultcat );
								}
								$output .= '">';
							}

							if ( 'show' == $addlinkcustomcat ) {
								$output .= '<tr class="customcatrow" ';
								if ( !isset( $captureddata['link_user_category'] ) || empty( $captureddata['link_user_category'] ) ) {
									$output .= 'style="display: none;"';
								}

								$output .= '><th>' .  $linkcustomcatlabel . '</th><td ';

								if ( !empty( $linkusercattooltip ) ) {
									$output .= 'class="lltooltip" title="' . $linkusercattooltip . '"';
								}

								$output .= '><input type="text" name="link_user_category" id="link_user_category" value="' . ( isset( $captureddata['link_user_category'] ) ? esc_html( stripslashes( $captureddata['link_user_category'] ), '1' ) : '') . "\" /></td></tr>\n";
							}
						}
					break;
					case 6: 	//------------------ Link Tags --------------------   
						$link_tags_query_args = array( 'hide_empty' => false, 'taxonomy' => 'link_library_tags' );
						if ( !empty( $include_links_array ) ) {
							$link_tags_query_args['include'] = explode( ',', $libraryoptions['addlinktaglistoverride'] );
						}

						$linktags = get_terms( $link_tags_query_args );

						if ( $linktags ) {
							if ( 'show' == $libraryoptions['showaddlinktags'] ) {
								if ( empty( $linktagslabel ) ) {
									$linktagslabel = __( 'Link tags', 'link-library' );
								}

								$output .= '<tr><th><label for="link_tags">' . $linktagslabel;
								$output .= '<br /><br /><span class="multiselecthelp">';
								$output .= __( 'Use control-click (Windows) or command-click (Mac) to select multiple', 'link-library' );
								$output .= '</span>';

								$output .= '</label></th><td ';

								if ( !empty( $linktagtooltip ) ) {
									$output .= 'class="lltooltip" title="' . $linktagtooltip . '"';
								}

								$output .= '>';
								$output .= '<SELECT name="link_tags[]" id="link_tags" ';

								$number_of_tags = sizeof( $linktags );
								if ( $linkcustomtaglistentry ) {
									$number_of_tags++;
								}
								$output .= 'multiple size="' . ( $number_of_tags < 10 ? $number_of_tags : 10 ) . '" style="height: auto"';

								$output .= '>';

								if ( empty( $linkcustomtaglistentry ) ) {
									$linkcustomcatlistentry = __( 'User-submitted tag (define below)', 'link-library' );
								}

								foreach ( $linktags as $linktag ) {
									$output .= '<OPTION VALUE="' . $linktag->term_id . '" ';
									if ( isset( $captureddata['link_tags'] ) && in_array( $linktag->term_id, $captureddata['link_tags'] ) ) {
										$output .= "selected";
									}
									$output .= '>' . $linktag->name;
								}

								if ( 'show' == $addlinkcustomtag ) {
									$output .= '<OPTION VALUE="new">' . stripslashes( $linkcustomtaglistentry ) . "\n";
								}

								$output .= "</SELECT></td></tr>\n";
							}

							if ( 'show' == $addlinkcustomtag ) {
								$output .= '<tr class="customtagrow" ';
								if ( !isset( $captureddata['link_user_tags'] ) || empty( $captureddata['link_user_tags'] ) ) {
									$output .= 'style="display: none;"';
								}

								$output .= '><th>' .  $linkcustomtaglabel . '</th><td ';

								if ( !empty( $linkusertagtooltip ) ) {
									$output .= 'class="lltooltip" title="' . $linkusertagtooltip . '"';
								}

								$output .= '><input type="text" name="link_user_tags" id="link_user_tags" value="' . ( isset( $captureddata['addlinkusertags'] ) ? esc_html( stripslashes( $captureddata['link_user_tags'] ), '1' ) : '') . "\" /></td></tr>\n";
							}
						}
					break;
					case 7: 	//------------------ Link Description --------------------   
						if ( 'show' == $showaddlinkdesc || 'required' == $showaddlinkdesc ) {
							if ( empty( $linkdesclabel ) ) {
								$linkdesclabel = __( 'Link description', 'link-library' );
							}
				
							$output .= '<tr><th><label for="link_description">' . $linkdesclabel . '</label></th><td ';
				
							if ( !empty( $linkdesctooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkdesctooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinkdesc ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, 1-1024 chars', 'link-library' ) . '" ';
				
							$output .= ' type="text" name="link_description" id="link_description" value="' . ( isset( $captureddata['link_description'] ) ? esc_html( stripslashes( $captureddata['link_description'] ), '1' ) : '' ) . "\" /></td></tr>\n";
						}
					break;
					case 8: 	//------------------ Link Large Description --------------------   
						if ( 'show' == $showuserlargedescription || 'required' == $showuserlargedescription ) {
							if ( empty( $linklargedesclabel ) ) {
								$linklargedesclabel = __( 'Large description', 'link-library' );
							}
				
							$output .= '<tr><th style="vertical-align: top"><label for="link_textfield">' . $linklargedesclabel . '</label></th><td ';
				
							if ( !empty( $largedesctooltip ) ) {
								$output .= 'class="lltooltip" title="' . $largedesctooltip . '"';
							}
				
							$output .= '><textarea ';
				
							if ( 'required' == $showuserlargedescription ) {
								$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
							}
				
							$output .= ' name="link_textfield" id="link_textfield" cols="66">' . ( isset( $captureddata['link_textfield'] ) ? esc_html( stripslashes( $captureddata['link_textfield'] ), '1' ) : '' ) . "</textarea></td></tr>\n";
						}
					break;
					case 9: 	//------------------ Link Notes --------------------   
						if ( 'show' == $showaddlinknotes || 'required' == $showaddlinknotes) {
							if ( empty( $linknoteslabel ) ) {
								$linknoteslabel = __( 'Link notes', 'link-library' );
							}
				
							$output .= '<tr><th><label for="link_notes">' . $linknoteslabel . '</label></th><td ';
				
							if ( !empty( $linknotestooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linknotestooltip . '"';
							}
				
							$output .= '>';
				
							if ( !$usetextareaforusersubmitnotes || empty( $usetextareaforusersubmitnotes ) ) {
								$output .= '<input ';
				
								if ( 'required' == $showaddlinknotes ) {
									$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
								}
				
								$output .= 'type="text" name="link_notes" id="link_notes" value="';
							} elseif ( $usetextareaforusersubmitnotes ) {
								$output .= '<textarea ';
				
								if ( 'required' == $showaddlinknotes ) {
									$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
								}
				
								$output .= 'name="link_notes" id="link_notes">';
							}
				
							$output .= ( isset( $captureddata['link_notes'] ) ? esc_html( stripslashes( $captureddata['link_notes'] ), '1' ) : '' );
				
							if ( !$usetextareaforusersubmitnotes || empty( $usetextareaforusersubmitnotes ) ) {
								$output .= '" />';
							} elseif ( $usetextareaforusersubmitnotes ) {
								$output .= '</textarea>';
							}
				
							$output .= "</td></tr>\n";
						}
					break;
					case 10: 	//------------------ Link Image --------------------   
						if ( 'show' == $showaddlinkimage || 'required' == $showaddlinkimage ) {
							if ( empty( $linkimagelabel ) ) {
								$linkimagelabel = __( 'Link Image (jpg, jpeg, png)', 'link-library' );
							}
				
							$output .= '<tr><th><label for="linkimage">' . $linkimagelabel . '</label></th><td ';
				
							if ( !empty( $linkimagetooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkimagetooltip . '"';
							}
				
							$output .= '>';
							$output .= '<input type="file" name="linkimage" id="linkimage">';
				
							$output .= "</td></tr>\n";
						}
					break;
					case 11: 	//------------------ Reciprocal Link --------------------   
						if ( 'show' == $showaddlinkreciprocal || 'required' == $showaddlinkreciprocal) {
							if ( empty( $linkreciprocallabel ) ) {
								$linkreciprocallabel = __( 'Reciprocal Link', 'link-library' );
							}
				
							$output .= '<tr><th><label for="ll_reciprocal">' . $linkreciprocallabel . '</label></th><td ';
				
							if ( !empty( $linkreciptooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkreciptooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinkreciprocal ) {
								$requiredtext = ' required url';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, 1-1024 chars', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_reciprocal" id="ll_reciprocal" value="' . ( isset( $captureddata['ll_reciprocal'] ) ? esc_html(stripslashes($captureddata['ll_reciprocal']), '1') : $linkaddrdefvalue ) . "\" /></td></tr>\n";
						}
					break;
					case 12: 	//------------------ Secondary Address --------------------  
						if ( 'show' == $showaddlinksecondurl || 'required' == $showaddlinksecondurl) {
							if ( empty( $linksecondurllabel ) ) {
								$linksecondurllabel = __( 'Secondary Address', 'link-library' );
							}
				
							$output .= '<tr><th><label for="ll_secondwebaddr">' . $linksecondurllabel . '</label></th><td ';
				
							if ( !empty( $linksecondtooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linksecondtooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinksecondurl ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_secondwebaddr" id="ll_secondwebaddr" value="' . ( isset( $captureddata['ll_secondwebaddr'] ) ? esc_html( stripslashes( $captureddata['ll_secondwebaddr'] ), '1' ) : $linkaddrdefvalue ) . "\" /></td></tr>\n";
						}
					break;
					case 13: 	//------------------ Link Telephone --------------------  
						if ( 'show' == $showaddlinktelephone || 'required' == $showaddlinktelephone) {
							if ( empty( $linktelephonelabel ) ) {
								$linktelephonelabel = __( 'Telephone', 'link-library' );
							}
				
							$output .= '<tr><th><label for="ll_telephone">' . $linktelephonelabel . '</label></th><td ';
				
							if ( !empty( $linktelephonetooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linktelephonetooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinktelephone ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_telephone" id="ll_telephone" value="' . ( isset( $captureddata['ll_telephone'] ) ? esc_html( stripslashes( $captureddata['ll_telephone'] ), '1' ) : '' ) . "\" /></td></tr>\n";
						}
					break;
					case 14: 	//------------------ Link E-mail --------------------  
						if ( 'show' == $showaddlinkemail || 'required' == $showaddlinkemail ) {
							if ( empty( $linkemaillabel ) ) {
								$linkemaillabel = __( 'E-mail', 'link-library' );
							}
				
							$output .= '<tr><th><label for="ll_email">' . $linkemaillabel . '</label></th><td ';
				
							if ( !empty( $linkemailtooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkemailtooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinkemail ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="email length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, proper e-mail, 1-128 chars', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_email" id="ll_email" value="' . ( isset( $captureddata['ll_email'] ) ? esc_html( stripslashes( $captureddata['ll_email'] ), '1' ) : '' ) . "\" /></td></tr>\n";
						}
					break;
					case 15: 	//------------------ Link Submitter Name --------------------  
						if ( 'show' == $showlinksubmittername || 'required' == $showlinksubmittername || is_user_logged_in() ) {
							if ( empty( $linksubmitternamelabel ) ) {
								$linksubmitternamelabel = __( 'Submitter Name', 'link-library' );
							}
				
							$name_field_value = '';
							if ( isset( $captureddata['ll_submittername'] ) ) {
								$name_field_value = esc_html( stripslashes( $captureddata['ll_submittername'] ) );
							} elseif ( is_user_logged_in() ) {
								$user_data = wp_get_current_user();
								$name_field_value = $user_data->display_name;
							}
				
							$output .= '<tr';
				
							if ( 'show' != $showlinksubmittername && 'required' != $showlinksubmittername ) {
								$output .= ' style="display:none"';
							}
							$output .= '><th><label for="ll_submittername">' . $linksubmitternamelabel . '</label></th><td ';
				
							if ( !empty( $submitternametooltip ) ) {
								$output .= 'class="lltooltip" title="' . $submitternametooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showlinksubmittername ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, 1-128 chars', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_submittername" id="ll_submittername" value="' . $name_field_value . "\" /></td></tr>\n";
						}
					break;
					case 16: 	//------------------ Link Submitter E-mail --------------------  
						if ( 'show' == $showaddlinksubmitteremail || 'required' == $showaddlinksubmitteremail || is_user_logged_in()) {
							if ( empty( $linksubmitteremaillabel ) ) {
								$linksubmitteremaillabel = __( 'Submitter E-mail', 'link-library' );
							}
				
							$email_field_value = '';
							if ( isset( $captureddata['ll_submitteremail'] ) ) {
								$email_field_value = esc_html( stripslashes( $captureddata['ll_submitteremail'] ) );
							} elseif ( is_user_logged_in() ) {
								$user_data = wp_get_current_user();
								$email_field_value = $user_data->user_email;
							}
				
							$output .= '<tr';
				
							if ( 'show' != $showaddlinksubmitteremail && 'required' != $showaddlinksubmitteremail ) {
								$output .= ' style="display:none"';
							}
							$output .= '><th><label for="ll_submitteremail">' . $linksubmitteremaillabel . '</label></th><td ';
				
							if ( !empty( $submitteremailtooltip ) ) {
								$output .= 'class="lltooltip" title="' . $submitteremailtooltip . '"';
							}
				
							$output .= '><input ';
				
							if ( 'required' == $showaddlinksubmitteremail ) {
								$requiredtext = ' required';
							} else {
								$requiredtext = '';
							}
				
							$output .= 'data-validation="email length' . $requiredtext . '" data-validation-length="max128" data-validation-error-msg-required="' . __( 'Required field, 1-128 chars', 'link-library' ) . '" ';
				
							$output .= 'type="text" name="ll_submitteremail" id="ll_submitteremail" value="' . $email_field_value . "\" /></td></tr>\n";
						}
					break;
					case 17: 	//------------------ Link Submitter Comment --------------------  
						if ( 'show' == $showlinksubmittercomment || 'required' == $showlinksubmittercomment) {
							if ( empty( $linksubmittercommentlabel ) ) {
								$linksubmittercommentlabel = __( 'Submitter Comment', 'link-library' );
							}
				
							$output .= '<tr><th style="vertical-align: top;"><label for="ll_submittercomment">' . $linksubmittercommentlabel . '</label></th><td ';
				
							if ( !empty( $submittercommenttooltip ) ) {
								$output .= 'class="lltooltip" title="' . $submittercommenttooltip . '"';
							}
				
							$output .= '><textarea ';
				
							if ( 'required' == $showlinksubmittercomment ) {
								$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
							}
				
							$output .= 'name="ll_submittercomment" id="ll_submittercomment" cols="38">' . ( isset( $captureddata['ll_submittercomment'] ) ? esc_html( stripslashes( $captureddata['ll_submittercomment']), '1' ) : '' ) . "</textarea></td></tr>\n";
						}
					break;
					case 18: 	//------------------ Captcha Question --------------------  
						if ( $showcaptcha && !is_user_logged_in() ) {
							$output .= apply_filters( 'link_library_generate_captcha', '' );
						}
				
						if ( 'show' == $showcustomcaptcha && !is_user_logged_in() ) {
							if ( empty( $customcaptchaquestion ) ) {
								$customcaptchaquestion = __( 'Is boiling water hot or cold?', 'link-library' );
							}
				
							$output .= '<tr><th style="vertical-align: top;"><label for="ll_customcaptchaanswer">' . $customcaptchaquestion . '</label></th><td><input data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" type="text" name="ll_customcaptchaanswer" id="ll_customcaptchaanswer" value="' . (isset( $captureddata['ll_customcaptchaanswer'] ) ? esc_html( stripslashes( $captureddata['ll_customcaptchaanswer'] ), '1' ) : '' ) . "\" /></td></tr>\n";
						}
					break;
					case 19: 	//------------------ Link Reference --------------------  
						if ( 'show' == $showlinkreferencelist || 'required' == $showlinkreferencelist) {
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

							if ( !empty( $categorylist_cpt ) ) {
								$link_categories_query_args['include'] = explode( ',', $categorylist_cpt );
							}

							if ( !empty( $excludecategorylist_cpt ) ) {
								$link_categories_query_args['exclude'] = explode( ',', $excludecategorylist_cpt );
							}

							if ( ( !empty( $categorysluglist ) || isset( $_GET['catslug'] ) ) && empty( $singlelinkid ) ) {
								if ( !empty( $categorysluglist ) ) {
									$link_categories_query_args['slug'] = explode( ',', $categorysluglist );
								} elseif ( isset( $_GET['catslug'] ) ) {
									$link_categories_query_args['slug'] = $_GET['catslug'];
								}
								$link_categories_query_args['include'] = array();
								$link_categories_query_args['exclude'] = array();
							}

							if ( isset( $categoryname ) && !empty( $categoryname ) && 'HTMLGETPERM' == $showonecatmode && empty( $singlelinkid ) ) {
								$link_categories_query_args['slug'] = $categoryname;
							}

							if ( ( !empty( $categorynamelist ) || isset( $_GET['catname'] ) ) && empty( $singlelinkid ) ) {
								$link_categories_query_args['name'] = explode( ',', urldecode( $categorynamelist ) );
							}

							$link_categories_query_args['orderby'] = 'name';
							$link_categories_query_args['order'] = 'ASC';

							$link_categories = get_terms( 'link_library_category', $link_categories_query_args );

							remove_filter( 'get_terms', 'link_library_get_terms_filter_only_publish' );
							remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_pending' );
							remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft' );
							remove_filter( 'get_terms', 'link_library_get_terms_filter_publish_draft_pending' );

							$post_array = array();
							foreach ( $link_categories as $link_category ) {
								$link_query_args = array( 'post_type' => 'link_library_links', 'posts_per_page' => -1 );
				
								if ( !$combineresults ) {
									$link_query_args['tax_query'][] =
										array(
											'taxonomy' => 'link_library_category',
											'field'    => 'term_id',
											'terms'    => $link_category->term_id,
											'include_children' => false
				
										);
									if ( sizeof( $link_query_args['tax_query'] ) > 1 ) {
										$link_query_args['tax_query']['relation'] = 'AND';
									}
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
				
									if ( sizeof( $link_query_args['tax_query'] ) > 1 ) {
										$link_query_args['tax_query']['relation'] = 'AND';
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
				
									if ( sizeof( $link_query_args['tax_query'] ) > 1 ) {
										$link_query_args['tax_query']['relation'] = 'AND';
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
																			
								$link_query_args['orderby']['title'] = 'ASC';
				
								$the_link_query = new WP_Query( $link_query_args );

								if ( $the_link_query->have_posts() ) {
									while ( $the_link_query->have_posts() ) {
										$the_link_query->the_post();
										$post_array[get_the_ID()] = get_the_title();
									}
								}								
							}

							$output .= '<tr><th style="vertical-align: top;"><label for="ll_linkreference">' . $linkreferencelabel . '</label></th><td ';
					
							if ( !empty( $linkreferencetooltip ) ) {
								$output .= 'class="lltooltip" title="' . $linkreferencetooltip . '"';
							}
				
							$output .= '>';

							if ( !empty( $post_array ) ) {

								asort( $post_array );
								$output .= '<select name="ll_linkreference" id="ll_linkreference"';

								if ( 'required' == $showlinkreferencelist ) {
									$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
								}

								$output .= '><option value="">' . __( 'Select a link', 'link-library' ). '</option>';
								foreach ( $post_array as $post_id => $post_item ) {
									$output .= '<option value="' . $post_id . '" ' . selected( $post_id, ( isset( $captureddata['ll_linkreference'] ) ? $captureddata['ll_linkreference'] : '' ), false ). '>' . $post_item . '</option>';
								}
								$output .= '</select>';
							}

							$output .= '</td></tr>';
						}
					break;
					case 20: 	//------------------ Custom Fields --------------------  
					case 21:
					case 22:
					case 23:
					case 24:
						$customurlfieldid = $arrayelements - 19;
						$fieldactivevar = 'customurl' . $customurlfieldid . 'active';
						$displayvar = 'showcustomurl' . $customurlfieldid;
						$labelvar = 'customurl' . $customurlfieldid . 'label';
						$tooltipvar = 'customurl' . $customurlfieldid . 'tooltip';

						if ( $$fieldactivevar ) {
							if ( 'show' == $$displayvar || 'required' == $$displayvar) {					
								$output .= '<tr><th><label for="ll_customurl' . $customurlfieldid . '">' . $$labelvar . '</label></th><td ';
					
								if ( !empty( $$tooltipvar ) ) {
									$output .= 'class="lltooltip" title="' . $$tooltipvar . '"';
								}
					
								$output .= '><input ';
					
								if ( 'required' == $$displayvar ) {
									$requiredtext = ' required url';
								} else {
									$requiredtext = '';
								}
					
								$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, 1-1024 chars', 'link-library' ) . '" ';
					
								$output .= 'type="text" name="ll_customurl' . $customurlfieldid . '" id="ll_customurl' . $customurlfieldid . '" value="' . ( isset( $captureddata['ll_customurl' . $customurlfieldid] ) ? esc_html(stripslashes($captureddata['ll_customurl' . $customurlfieldid]), '1') : $linkaddrdefvalue ) . "\" /></td></tr>\n";
							}
						}
					break;
					case 25: 	//------------------ Custom Fields --------------------  
					case 26:
					case 27:
					case 28:
					case 29:
						$customtextfieldid = $arrayelements - 24;
						$fieldactivevar = 'customtext' . $customtextfieldid . 'active';
						$displayvar = 'showcustomtext' . $customtextfieldid;
						$labelvar = 'customtext' . $customtextfieldid . 'label';
						$tooltipvar = 'customtext' . $customtextfieldid . 'tooltip';

						if ( $$fieldactivevar ) {
							if ( 'show' == $$displayvar || 'required' == $$displayvar) {					
								$output .= '<tr><th><label for="ll_customtext' . $customtextfieldid . '">' . $$labelvar . '</label></th><td ';
					
								if ( !empty( $$tooltipvar ) ) {
									$output .= 'class="lltooltip" title="' . $$tooltipvar . '"';
								}
					
								$output .= '><input ';
					
								if ( 'required' == $$displayvar ) {
									$requiredtext = ' required ';
								} else {
									$requiredtext = '';
								}
					
								$output .= 'data-validation="length' . $requiredtext . '" data-validation-length="max1024" data-validation-error-msg-required="' . __( 'Required field, 1-1024 chars', 'link-library' ) . '" ';
					
								$output .= 'type="text" name="ll_customtext' . $customtextfieldid . '" id="ll_customtext' . $customtextfieldid . '" value="' . ( isset( $captureddata['ll_customtext' . $customtextfieldid] ) ? sanitize_text_field(stripslashes($captureddata['ll_customtext' . $customtextfieldid])) : '' ) . "\" /></td></tr>\n";
							}
						}
					break;
					case 30: 	//------------------ Custom Fields --------------------  
					case 31:
					case 32:
					case 33:
					case 34:
						$customlistfieldid = $arrayelements - 29;
						$fieldactivevar = 'customlist' . $customlistfieldid . 'active';
						$displayvar = 'showcustomlist' . $customlistfieldid;
						$labelvar = 'customlist' . $customlistfieldid . 'label';
						$tooltipvar = 'customlist' . $customlistfieldid . 'tooltip';

						if ( $$fieldactivevar ) {
							if ( 'show' == $$displayvar || 'required' == $$displayvar) {					
								$output .= '<tr><th><label for="ll_customlist' . $customlistfieldid . '">' . $$labelvar . '</label></th><td ';
					
								if ( !empty( $$tooltipvar ) ) {
									$output .= 'class="lltooltip" title="' . $$tooltipvar . '"';
								}
					
								$output .= '>';
								
								$output .= '<select name="ll_customlist' . $customlistfieldid . '" id="ll_customlist' . $customlistfieldid . '"';

								if ( 'required' == $$displayvar ) {
									$output .= 'data-validation="required" data-validation-error-msg-required="' . __( 'Required field', 'link-library' ) . '" ';
								}

								$output .= '><option value="">' . __( 'Select an option', 'link-library' ). '</option>';

								$list_values = explode( ',', $generaloptions['customlist' . $customlistfieldid . 'values'] );

								if ( !empty( $list_values ) ) {
									foreach ( $list_values as $index => $list_value ) {
										$output .= '<option ' . selected( $index, ( isset( $captureddata['ll_customlist' . $customlistfieldid] ) ? $captureddata['ll_customlist' . $customlistfieldid] : '' ), false ) . ' value="' . $index . '">' . $list_value . '</option>';
									}
								}
								
								$output .= '</select>';
							}
						}
					break;
				}
			}
		}

		$output .= "</table>\n";

		if ( empty( $addlinkbtnlabel ) ) {
			$addlinkbtnlabel = __( 'Add link', 'link-library' );
		}

		$output .= '<span style="border:0;" class="LLUserLinkSubmit"><input type="submit" name="submit" value="' . $addlinkbtnlabel . '" /></span>';

		$output .= "</div>\n";
		$output .= "</form>\n\n";

		$output .= "<script type='text/javascript'>\n";

		$output .= "\tjQuery( document ).ready( function() {\n";

		$output .= "jQuery('.lltooltip').each(function () {\n";
		$output .= "\tjQuery(this).tipTip();\n";
		$output .= "});\n";

		$output .= "\tvar LinkLibraryValidationLanguage = {\n";
		$output .= "\t\terrorTitle: '" . addslashes( __( 'Form submission failed!', 'link-library' ) ) . "',\n";
		$output .= "\t\trequiredFields: '" . addslashes( __( 'You have not answered all required fields', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadTime: '" . addslashes( __( 'You have not given a correct time', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadEmail: '" . addslashes( __( 'You have not given a correct e-mail address', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadTelephone: '" . addslashes( __( 'You have not given a correct phone number', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadSecurityAnswer: '" . addslashes( __( 'You have not given a correct answer to the security question', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadDate: '" . addslashes( __( 'You have not given a correct date', 'link-library' ) ) . "',\n";
		$output .= "\t\tlengthBadStart: '" . addslashes( __( 'The input value must be between ', 'link-library' ) ) . "',\n";
		$output .= "\t\tlengthBadEnd: '" . addslashes( __( 'characters', 'link-library' ) ) . "',\n";
		$output .= "\t\tlengthTooLongStart: '" . addslashes( __( 'The input value is longer than ', 'link-library' ) ) . "',\n";
		$output .= "\t\tlengthTooShortStart: '" . addslashes( __( 'The input value is shorter than ', 'link-library' ) ). "',\n";
		$output .= "\t\tnotConfirmed: '" . addslashes( __( 'Input values could not be confirmed', 'link-library' ) ). "',\n";
		$output .= "\t\tbadDomain: '" . addslashes( __( 'Incorrect domain value', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadUrl: '" . addslashes( __( 'The input value is not a correct URL. Requires http:// or https://.', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadCustomVal: '" . addslashes( __( 'The input value is incorrect', 'link-library' ) ) . "',\n";
		$output .= "\t\tandSpaces: '" . addslashes( __( ' and spaces ', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadInt: '" . addslashes( __( 'The input value was not a correct number', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadSecurityNumber: '" . addslashes( __( 'Your social security number was incorrect', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadUKVatAnswer: '" . addslashes( __( 'Incorrect UK VAT Number', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadStrength: '" . addslashes( __( 'The password is not strong enough', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadNumberOfSelectedOptionsStart: '" . addslashes( __( 'You have to choose at least ', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadNumberOfSelectedOptionsEnd: '" . addslashes( __( ' answers', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadAlphaNumeric: '" . addslashes( __( 'The input value can only contain alphanumeric characters ', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadAlphaNumericExtra: '" . addslashes( __( ' and ', 'link-library' ) ) . "',\n";
		$output .= "\t\twrongFileSize: '" . addslashes( __( 'The file you are trying to upload is too large (max %s)', 'link-library' ) ) . "',\n";
		$output .= "\t\twrongFileType: '" . addslashes( __( 'Only files of type %s is allowed', 'link-library' ) ) . "',\n";
		$output .= "\t\tgroupCheckedRangeStart: '" . addslashes( __( 'Please choose between ', 'link-library' ) ) . "',\n";
		$output .= "\t\tgroupCheckedTooFewStart: '" . addslashes( __( 'Please choose at least ', 'link-library' ) ) . "',\n";
		$output .= "\t\tgroupCheckedTooManyStart: '" . addslashes( __( 'Please choose a maximum of ', 'link-library' ) ) . "',\n";
		$output .= "\t\tgroupCheckedEnd: '" . addslashes( __( ' item(s)', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadCreditCard: '" . addslashes( __( 'The credit card number is not correct', 'link-library' ) ) . "',\n";
		$output .= "\t\tbadCVV: '" . addslashes( __( 'The CVV number was not correct', 'link-library' ) ) . "',\n";
		$output .= "\t\twrongFileDim : '" . addslashes( __( 'Incorrect image dimensions,', 'link-library' ) ) . "',\n";
		$output .= "\t\timageTooTall : '" . addslashes( __( 'the image can not be taller than', 'link-library' ) ) . "',\n";
		$output .= "\t\timageTooWide : '" . addslashes( __( 'the image can not be wider than', 'link-library' ) ) . "',\n";
		$output .= "\t\timageTooSmall : '" . addslashes( __( 'the image was too small', 'link-library' ) ) . "',\n";
		$output .= "\t\tmin : '" . addslashes( __( 'min', 'link-library' ) ) . "',\n";
		$output .= "\t\tmax : '" . addslashes( __( 'max', 'link-library' ) ) . "',\n";
		$output .= "\t\timageRatioNotAccepted : '" . addslashes( __( 'Image ratio is not accepted', 'link-library' ) ) . "'\n";
		$output .= "\t};\n";

		$output .= "\t\tjQuery.validate({\n";
		$output .= "\t\t\tmodules : 'location, date, security, file',\n";
		$output .= "\t\t\tlanguage : LinkLibraryValidationLanguage,\n";
		$output .= "\t\t});\n";
		$output .= "\t\tjQuery('#link_category').change(function() {\n";
		$output .= "\t\tvar cat_data = jQuery('#link_category').val();\n";
		$output .= "\t\t\tif ( Array.isArray( cat_data ) ) {\n";
		$output .= "\t\t\t\tif ( cat_data.indexOf('new') != -1 ) {\n";
		$output .= "\t\t\t\t\tjQuery('.customcatrow').show();\n";
		$output .= "\t\t\t\t} else {\n";
		$output .= "\t\t\t\t\tjQuery('.customcatrow').hide();\n";
		$output .= "\t\t\t\t};\n";
		$output .= "\t\t\t} else {\n";
		$output .= "\t\t\t\tif ( jQuery('#link_category').val() == 'new' ) {\n";
		$output .= "\t\t\t\t\tjQuery('.customcatrow').show();\n";
		$output .= "\t\t\t\t} else {\n";
		$output .= "\t\t\t\t\tjQuery('.customcatrow').hide();\n";
		$output .= "\t\t\t\t};\n";
		$output .= "\t\t\t};\n";
		$output .= "\t\t});\n";

		$output .= "\t\tjQuery('#link_tags').change(function() {\n";
		$output .= "\t\tvar tag_data = jQuery('#link_tags').val();\n";
		$output .= "\t\t\tif ( Array.isArray( tag_data ) ) {\n";
		$output .= "\t\t\t\tif ( tag_data.indexOf('new') != -1 ) {\n";
		$output .= "\t\t\t\t\tjQuery('.customtagrow').show();\n";
		$output .= "\t\t\t\t} else {\n";
		$output .= "\t\t\t\t\tjQuery('.customtagrow').hide();\n";
		$output .= "\t\t\t\t};\n";
		$output .= "\t\t\t};\n";
		$output .= "\t\t});\n";

		$output .= "\t});\n";
		$output .= "</script>\n";
	}

	return $output;
}

function link_library_generate_captcha() {
	$generaloptions = get_option( 'LinkLibraryGeneral' );
	$generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );

	if ( 'easycaptcha' == $generaloptions['captchagenerator'] ) {
		$captcha = '<tr><td></td><td><span id="captchaimage"><img src="' . plugins_url( 'captcha/easycaptcha.php', __FILE__ ) . "\" /></span></td></tr>\n";
		$captcha .= '<tr><th>' . __( 'Enter code from above image', 'link-library' ) . "</th><td><input type='text' name='confirm_code' /></td></tr>\n";
	} elseif ( 'recaptcha' == $generaloptions['captchagenerator'] && !empty( $generaloptions['recaptchasitekey'] ) ) {
		$captcha = '<tr><td></td><td><div class="g-recaptcha" data-sitekey="' . $generaloptions['recaptchasitekey'] . '"></div></td></tr>';
	}

	return $captcha;
}

add_filter( 'link_library_generate_captcha', 'link_library_generate_captcha' );
