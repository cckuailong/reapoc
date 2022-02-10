<?php

function link_library_popup_content( $my_link_library_plugin ) {
    if ( isset( $_GET['linkid'] ) && isset( $_GET['settings'] ) ) {
        $link_id = intval( $_GET['linkid'] );
        $settings_id = intval( $_GET['settings'] );
    } else {
        wp_die();
    }

    $link_data = get_post( $link_id );

    if ( !empty( $link_data ) ) {
        $link_url = get_post_meta( $link_id, 'link_url', true );
        $link_second_url = get_post_meta( $link_id, 'link_second_url', true );
        $link_description = get_post_meta( $link_id, 'link_description', true );
        $link_no_follow = get_post_meta( $link_id, 'link_no_follow', true );
        $link_target = get_post_meta( $link_id, 'link_target', true );
        $link_image = get_post_meta( $link_id, 'link_image', true );
        $link_featured = get_post_meta( $link_id, 'link_featured', true );
        $link_textfield = get_post_meta( $link_id, 'link_textfield', true );
        $link_telephone = get_post_meta( $link_id, 'link_telephone', true );
        $link_email = get_post_meta( $link_id, 'link_email', true );
        $link_visits = get_post_meta( $link_id, 'link_visits', true );
        $link_submitter_name = get_post_meta( $link_id, 'link_submitter_name', true );
        $link_rating = get_post_meta( $link_id, 'link_rating', true );
        $link_rss = get_post_meta( $link_id, 'link_rss', true );

        $the_link = '#';
        if ( !empty( $link_url ) ) {
            $the_link = esc_html( $link_url );
        }

        $the_second_link = '#';
        if ( !empty( $link_second_url ) ) {
            $the_second_link = esc_html( $link_second_url );
        }

        $cleanname = esc_html( $link_data->post_name, ENT_QUOTES );

        $name = $cleanname;

        $alt = ' alt="' . $cleanname . '"';

        $title = esc_html( $link_description, ENT_QUOTES );

        if ('' != $title)
            $title = ' title="' . $title . '"';

        $options = get_option( 'LinkLibraryPP' . $settings_id );
	    $generaloptions = get_option( 'LinkLibraryGeneral' );
	    $generaloptions = wp_parse_args( $generaloptions, ll_reset_gen_settings( 'return' ) );

        $rel = '';
        if ( ( $options['nofollow'] || $link_no_follow ) ) {
            $rel = ' rel="nofollow"';
        }

        $target = $link_target;
        if ( !empty( $target ) ) {
            $target = ' target="' . $target . '"';
        } else {
            $target = $options['linktarget'];
            if ( !empty( $target ) )
                $target = ' target="' . $target . '"';
        }

        $popup_text = ( !empty( $options['link_popup_text'] ) ? stripslashes($options['link_popup_text']) : __( '%link_image%<br />Click through to visit %link_name%.', 'link-library') );

        if ( ( strpos( $popup_text, '%link_image%' ) !== false ) && !empty( $link_image ) ) {
            $imageoutput = '<a href="';

            if ( 'primary' == $options['sourceimage'] || empty( $options['sourceimage'] ) ) {
                $imageoutput .= $the_link;
            } elseif ( 'secondary' == $options['sourceimage'] ) {
                $imageoutput .= $the_second_link;
            }

            $imageoutput .= '" id="link-' . $link_data->ID . '" class="track_this_link ' . ( $link_featured ? 'featured' : '' ). '" ' . $rel . $title . $target. '>';

            if ( $options['usethumbshotsforimages'] ) {
	            $protocol = is_ssl() ? 'https://' : 'http://';

	            if ( $generaloptions['thumbnailgenerator'] == 'robothumb' ) {
		            $imageoutput .= '<img src="' . $protocol . 'www.robothumb.com/src/?url=' . $the_link . '&size=' . $generaloptions['thumbnailsize'] . '"';
	            } elseif ( $generaloptions['thumbnailgenerator'] == 'thumbshots' ) {
		            if ( !empty( $generaloptions['thumbshotscid'] ) ) {
			            $imageoutput .= '<img src="' . $protocol . 'images.thumbshots.com/image.aspx?cid=' . rawurlencode( $generaloptions['thumbshotscid'] ) . '&v=1&w=120&url=' . $the_link . '"';
		            }
	            } elseif ( $generaloptions['thumbnailgenerator'] == 'pagepeeker' ) {
		            if ( empty( $pagepeekerid ) ) {
			            $imageoutput .= '<img src="' . $protocol . 'free.pagepeeker.com/v2/thumbs.php?size=' . $generaloptions['pagepeekersize'] . '&url=' . $the_link . '"';
		            } else {
			            $imageoutput .= '<img src="' . $protocol . 'api.pagepeeker.com/v2/thumbs.php?size=' . $generaloptions['pagepeekersize'] . '&url=' . $the_link . '"';
		            }
	            } elseif ( 'shrinktheweb' == $generaloptions['thumbnailgenerator'] ) {
		            if ( !empty( $shrinkthewebaccesskey ) ) {
			            $imageoutput .= '<img src="' . $protocol . 'images.shrinktheweb.com/xino.php?stwembed=1&stwaccesskeyid=' . rawurlencode( $generaloptions['shrinkthewebaccesskey'] ) . '&stwsize=' . $generaloptions['stwthumbnailsize'] . '&stwurl=' . $the_link . '"';
		            }
	            }
            } elseif ( strpos( $link_image, 'http' ) !== false ) {
                $imageoutput .= '<img src="' . $link_image . '"';
            } else {
                $imageoutput .= '<img src="' . get_option('siteurl') . $link_image . '"';
            }

            $imageoutput .= $alt . $title;

            if ( !empty( $options['imageclass'] ) ) {
                $imageoutput .= ' class="' . $options['imageclass'] . '" ';
            }

            $imageoutput .= "/>";

            $imageoutput .= '</a>';

            $popup_text = str_replace( '%link_image%', $imageoutput, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_image%' ) !== false ) && empty( $link_image ) ) {
            $popup_text = str_replace( '%link_image%', '', $popup_text );
        }

        if ( ( strpos( $popup_text, '%link_name%' ) !== false ) && !empty( $name ) ) {
            if ( ( 'primary' == $options['sourcename'] && $the_link != '#' ) || ( 'secondary' == $options['sourcename'] && $the_second_link != '#' ) ) {
                $nameoutput = '<a href="';

                if ( isset( $options['sourcename'] ) && ( 'primary' == $options['sourcename'] || empty( $options['sourcename'] ) ) ) {
                    $nameoutput .= $the_link;
                } elseif ( isset( $options['sourcename'] ) && 'secondary' == $options['sourcename'] ) {
                    $nameoutput .= $the_second_link;
                }

                $nameoutput .= '" id="link-' . $link_data->ID . '" class="' . ( ( isset( $enablelinkpopup ) && $enablelinkpopup ) ? 'thickbox' : 'track_this_link' ) . ( $link_featured ? ' featured' : '' ). '" ' . $rel . $title . $target. '>';
            }

            $nameoutput .= $name;

            if ( ( 'primary' == $options['sourcename'] && $the_link != '#' ) || ( $options['sourcename'] == 'secondary' && $the_second_link != '#' ) ) {
                $nameoutput .= '</a>';
            }

            $popup_text = str_replace( '%link_name%', $nameoutput, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_name%' ) !== false ) && empty( $name ) ) {
            $popup_text = str_replace( '%link_name%', '', $popup_text );
        }

        if ( ( strpos( $popup_text, '%link_url%' ) !== false ) && !empty( $link_url ) ) {
            $popup_text = str_replace( '%link_url%', $link_url, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_url%' ) !== false ) && empty( $link_url ) ) {
            $popup_text = str_replace( '%link_url%', '', $popup_text );
        }

        $link_cat_names = '';
        $link_categories = wp_get_post_terms( get_the_ID(), 'link_library_category' );
        if ( $link_categories ) {
            $countcats = 0;
            foreach ( $link_categories as $link_category ) {
                if ( $countcats >= 1 ) {
                    $link_cat_names .= ', ';
                }
                $link_cat_names .= $link_category->name;
                $countcats++;
            }
        }

        if ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && !empty( $link_cat_names ) ) {
            $popup_text = str_replace( '%link_cat_name%', $link_cat_names, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_cat_name%' ) !== false ) && empty( $link_cat_names ) ) {
            $popup_text = str_replace( '%link_cat_name%', '', $popup_text );
        }

        /* if ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && !empty( $link_description ) ) {
            $cleandesc = str_replace('[', '<', $linkitem['description']);
            $cleandesc = str_replace(']', '>', $cleandesc);

            $popup_text = str_replace( '%link_cat_desc%', $cleandesc, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_cat_desc%' ) !== false ) && empty( $linkitem['description'] ) ) {
            $popup_text = str_replace( '%link_cat_desc%', '', $popup_text );
        } */

        if ( ( strpos ( $popup_text, '%link_desc%' ) !== false ) && !empty( $link_description ) ) {
            $linkdesc = $link_description;
            $linkdesc = str_replace('[', '<', $linkdesc);
            $linkdesc = str_replace(']', '>', $linkdesc);

            $popup_text = str_replace( '%link_desc%', $linkdesc, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_desc%' ) !== false ) && empty( $link_description ) ) {
            $popup_text = str_replace( '%link_desc%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_large_desc%' ) !== false ) && !empty( $link_textfield ) ) {
            $linklargedesc = stripslashes( $link_textfield );
            $linklargedesc = str_replace('[', '<', $linklargedesc);
            $linklargedesc = str_replace(']', '>', $linklargedesc);

            $popup_text = str_replace( '%link_large_desc%', $linklargedesc, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_large_desc%' ) !== false ) && empty( $link_textfield ) ) {
            $popup_text = str_replace( '%link_large_desc%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_telephone%' ) !== false ) && !empty( $link_telephone ) ) {
            $linktelephone = stripslashes( $link_telephone );

            $popup_text = str_replace( '%link_telephone%', $linktelephone, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_telephone%' ) !== false ) && empty( $link_telephone ) ) {
            $popup_text = str_replace( '%link_telephone%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_email%' ) !== false ) && !empty( $link_email ) ) {
            $linkemail = stripslashes( $link_email );

            $popup_text = str_replace( '%link_email%', $linkemail, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_email%' ) !== false ) && empty( $link_email ) ) {
            $popup_text = str_replace( '%link_email%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_email_link%' ) !== false ) && !empty( $linkitem['link_email'] ) ) {
            $linkemail = stripslashes( $linkitem['link_email'] );
            if ( strpos ( $linkemail, '@') !== false ) {
                $linkemail = 'mailto:' . $linkemail;
            }

            $popup_text = str_replace( '%link_email_link%', $linkemail, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_email_link%' ) !== false ) && empty( $linkitem['link_email'] ) ) {
            $popup_text = str_replace( '%link_email_link%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_alt_web%' ) !== false ) && !empty( $link_second_url ) ) {
            $linkalturl = stripslashes( esc_html( $link_second_url ) );

            $popup_text = str_replace( '%link_alt_web%', $linkalturl, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_alt_web%' ) !== false ) && empty( $link_second_url ) ) {
            $popup_text = str_replace( '%link_alt_web%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_num_views%' ) !== false ) && !empty( $link_visits ) ) {
            $linkvisits = stripslashes( $link_visits );

            $popup_text = str_replace( '%link_num_views%', $linkvisits, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_num_views%' ) !== false ) && empty( $link_visits ) ) {
            $popup_text = str_replace( '%link_num_views%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_submitter_name%' ) !== false ) && !empty( $link_submitter_name ) ) {
            $linksubmitter = stripslashes( $link_submitter_name );

            $popup_text = str_replace( '%link_submitter_name%', $linksubmitter, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_submitter_name%' ) !== false ) && empty( $link_submitter_name ) ) {
            $popup_text = str_replace( '%link_submitter_name%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_rating%' ) !== false ) && !empty( $link_rating ) ) {
            $linksubmitter = stripslashes( $link_rating );

            $popup_text = str_replace( '%link_rating%', $linksubmitter, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_rating%' ) !== false ) && empty( $link_rating ) ) {
            $popup_text = str_replace( '%link_rating%', '', $popup_text );
        }

        if ( ( strpos ( $popup_text, '%link_rss%' ) !== false ) && !empty( $link_rss ) ) {
            $linksubmitter = stripslashes( $link_rss );

            $popup_text = str_replace( '%link_rss%', $linksubmitter, $popup_text );
        } elseif ( ( strpos( $popup_text, '%link_rss%' ) !== false ) && empty( $link_rss ) ) {
            $popup_text = str_replace( '%link_rss%', '', $popup_text );
        }

        $postshortcode_popup_text = apply_filters( 'the_content', $popup_text );
        echo '<div class="linkpopup">' . $postshortcode_popup_text . '</div>';

        $xpath = $my_link_library_plugin->relativePath( dirname( __FILE__ ), ABSPATH );

        $track_code = "<script type='text/javascript'>\n";
        $track_code .= "jQuery(document).ready(function()\n";
        $track_code .= "{\n";
        $track_code .= "jQuery('a.track_this_link').click(function() {\n";
        $track_code .= "linkid = this.id;\n";
        $track_code .= "linkid = linkid.substring(5);";
        $track_code .= "path = '" . $xpath . "';";
        $track_code .= "jQuery.post('" . WP_PLUGIN_URL . "/link-library/tracker.php', {id:linkid, xpath:path});\n";
        $track_code .= "return true;\n";
        $track_code .= "});\n";
        $track_code .= "});\n";
        $track_code .= "</script>";

        echo $track_code;
    }


    exit;
}

