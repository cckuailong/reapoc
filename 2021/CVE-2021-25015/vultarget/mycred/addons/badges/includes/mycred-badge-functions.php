<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Get Badge
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_badge' ) ) :
	function mycred_get_badge( $badge_id = NULL, $level = NULL ) {

		if ( absint( $badge_id ) === 0 || mycred_get_post_type( $badge_id ) != MYCRED_BADGE_KEY ) return false;

		global $mycred_badge;

		$badge_id     = absint( $badge_id );

		if ( isset( $mycred_badge )
			&& ( $mycred_badge instanceof myCRED_Badge )
			&& ( $badge_id === $mycred_badge->post_id )
		) {
			return $mycred_badge;
		}

		$mycred_badge = new myCRED_Badge( $badge_id, $level );

		do_action( 'mycred_get_badge' );

		return $mycred_badge;

	}
endif;

/**
 * Get Badge References
 * Returns an array of references used by badges for quicker checks.
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_badge_references' ) ) :
	function mycred_get_badge_references( $point_type = MYCRED_DEFAULT_TYPE_KEY, $force = false ) {

		$references = mycred_get_option( 'mycred-badge-refs-' . $point_type );
		if ( ! is_array( $references ) || empty( $references ) || $force ) {

			global $wpdb;

			$new_list = array();

			// Old versions
			$references = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'badge_requirements';" );
			if ( ! empty( $references ) ) {
				foreach ( $references as $entry ) {

					$requirement = maybe_unserialize( $entry->meta_value );
					if ( ! is_array( $requirement ) || empty( $requirement ) ) continue;

					if ( ! array_key_exists( 'type', $requirement ) || $requirement['type'] != $point_type || $requirement['reference'] == '' ) continue;

					if ( ! array_key_exists( $requirement['reference'], $new_list ) )
						$new_list[ $requirement['reference'] ] = array();

					if ( ! in_array( $entry->post_id, $new_list[ $requirement['reference'] ] ) )
						$new_list[ $requirement['reference'] ][] = $entry->post_id;

				}
			}


			// New version (post 1.7)
			$table      = mycred_get_db_column( 'postmeta' );
			$references = $wpdb->get_results( "SELECT post_id, meta_value FROM {$table} WHERE meta_key = 'badge_prefs';" );
			if ( ! empty( $references ) ) {
				foreach ( $references as $entry ) {

					// Manual badges should be ignored
					if ( absint( mycred_get_post_meta( $entry->post_id, 'manual_badge', true ) ) === 1 ) continue;

					$levels = maybe_unserialize( $entry->meta_value );
					if ( ! is_array( $levels ) || empty( $levels ) ) continue;

					foreach ( $levels as $level => $setup ) {

						if ( $level > 0 ) continue;

						foreach ( $setup['requires'] as $requirement_row => $requirement ) {

							if ( $requirement['type'] != $point_type || $requirement['reference'] == '' ) continue;

							if ( ! array_key_exists( $requirement['reference'], $new_list ) )
								$new_list[ $requirement['reference'] ] = array();

							if ( ! in_array( $entry->post_id, $new_list[ $requirement['reference'] ] ) )
								$new_list[ $requirement['reference'] ][] = $entry->post_id;

						}

					}

				}
			}

			if ( ! empty( $new_list ) )
				mycred_update_option( 'mycred-badge-references-' . $point_type, $new_list );

			$references = $new_list;

		}

		return apply_filters( 'mycred_get_badge_references', $references, $point_type );

	}
endif;

/**
 * Get Badge Requirements
 * Returns the badge requirements as an array.
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_badge_requirements' ) ) :
	function mycred_get_badge_requirements( $badge_id = NULL ) {

		return mycred_get_badge_levels( $badge_id );

	}
endif;

/**
 * Get Badge Levels
 * Returns an array of levels associated with a given badge.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_get_badge_levels' ) ) :
	function mycred_get_badge_levels( $badge_id ) {

		$setup = mycred_get_post_meta( $badge_id, 'badge_prefs', true );
		if ( ! is_array( $setup ) || empty( $setup ) ) {

			// Backwards comp.
			$old_setup = mycred_get_post_meta( $badge_id, 'badge_requirements', true );

			// Convert old setup to new
			if ( is_array( $old_setup ) && ! empty( $old_setup ) ) {

				$new_setup = array();
				foreach ( $old_setup as $level => $requirements ) {

					$level_image = mycred_get_post_meta( $badge_id, 'level_image' . $level, true );
					if ( $level_image == '' || $level == 0 )
						$level_image = mycred_get_post_meta( $badge_id, 'main_image', true );

					$row = array(
						'image_url'     => $level_image,
						'attachment_id' => 0,
						'label'         => '',
						'compare'       => 'AND',
						'requires'      => array(),
						'reward'        => array(
							'type'   => MYCRED_DEFAULT_TYPE_KEY,
							'amount' => 0,
							'log'    => ''
						)
					);

					$row['requires'][] = $requirements;

					$new_setup[] = $row;

				}

				if ( ! empty( $new_setup ) ) {

					mycred_update_post_meta( $badge_id, 'badge_prefs', $new_setup );
					mycred_delete_post_meta( $badge_id, 'badge_requirements' );

					$setup = $new_setup;

				}

			}

		}

		if ( empty( $setup ) && ! is_array( $setup ) )
			$setup = array();

		if ( empty( $setup ) )
			$setup[] = array(
				'image_url'     => '',
				'attachment_id' => 0,
				'label'         => '',
				'compare'       => 'AND',
				'requires'      => array(
					0 => array(
						'type'      => MYCRED_DEFAULT_TYPE_KEY,
						'reference' => '',
						'amount'    => '',
						'by'        => ''
					)
				),
				'reward'        => array(
					'type'   => MYCRED_DEFAULT_TYPE_KEY,
					'amount' => 0,
					'log'    => ''
				)
			);

		return apply_filters( 'mycred_badge_levels', $setup, $badge_id );

	}
endif;

/**
 * Display Badge Requirements
 * Returns the badge requirements as a string in a readable format.
 * @since 1.5
 * @version 1.2.2
 */
if ( ! function_exists( 'mycred_display_badge_requirement' ) ) :
	function mycred_display_badge_requirements( $badge_id = NULL ) {

		$badge  = mycred_get_badge( $badge_id );
		$levels = mycred_get_badge_levels( $badge_id );

		if ( empty( $levels ) ) {

			$reply = '-';

		}
		else {

			$point_types = mycred_get_types( true );
			$references  = mycred_get_all_references();
			$req_count   = count( $levels[0]['requires'] );

			// Get the requirements for the first level
			$base_requirements = array();
			foreach ( $levels[0]['requires'] as $requirement_row => $requirement ) {

				if ( $requirement['type'] == '' )
					$requirement['type'] = MYCRED_DEFAULT_TYPE_KEY;

				if ( ! array_key_exists( $requirement['type'], $point_types ) )
					continue;

				if ( ! array_key_exists( $requirement['reference'], $references ) )
					$reference = '-';
				else
					$reference = $references[ $requirement['reference'] ];

				$base_requirements[ $requirement_row ] = array(
					'type'   => $requirement['type'],
					'ref'    => $reference,
					'amount' => $requirement['amount'],
					'by'     => $requirement['by']
				);

			}

			// Loop through each level
			$output = array();
			foreach ( $levels as $level => $setup ) {

				$level_label = '';

				if ( ! $badge->open_badge ) {
						
					$level_label = '<strong>' . sprintf( __( 'Level %s', 'mycred' ), ( $level + 1 ) ) . ':</strong>';
					if ( $levels[ $level ]['label'] != '' )
						$level_label = '<strong>' . $levels[ $level ]['label'] . ':</strong>';

				}

				// Construct requirements to be used in an unorganized list.
				$level_req = array();
				foreach ( $setup['requires'] as $requirement_row => $requirement ) {

					$level_value = $requirement['amount'];
					$requirement = $base_requirements[ $requirement_row ];

					$mycred = mycred( $requirement['type'] );

					if ( $level > 0 )
						$requirement['amount'] = $level_value;

					if ( $requirement['by'] == 'count' )
						$rendered_row = sprintf( _x( '%s for "%s" x %d', '"Points" for "reference" x times', 'mycred' ), $mycred->plural(), $requirement['ref'], $requirement['amount'] );
					else
						$rendered_row = sprintf( _x( '%s %s for "%s"', '"Gained/Lost" "x points" for "reference"', 'mycred' ), ( ( $requirement['amount'] < 0 ) ? __( 'Lost', 'mycred' ) : __( 'Gained', 'mycred' ) ), $mycred->format_creds( $requirement['amount'] ), $requirement['ref'] );

					$compare = _x( 'OR', 'Comparison of badge requirements. A OR B', 'mycred' );
					if ( $setup['compare'] === 'AND' )
						$compare = _x( 'AND', 'Comparison of badge requirements. A AND B', 'mycred' );

					if ( $req_count > 1 && $requirement_row+1 < $req_count )
						$rendered_row .= ' <span>' . $compare . '</span>';

					$level_req[] = $rendered_row;

				}

				if ( empty( $level_req ) ) continue;

				$output[] = $level_label . '<ul class="mycred-badge-requirement-list '. ( $badge->open_badge ? 'open_badge' : '' ) .'"><li>' . implode( '</li><li>', $level_req ) . '</li></ul>';

			}

			if ( (int) mycred_get_post_meta( $badge_id, 'manual_badge', true ) === 1 )
				$output[] = '<strong><small><em>' . __( 'This badge is manually awarded.', 'mycred' ) . '</em></small></strong>';

			$reply = implode( '', $output );

		}

		return apply_filters( 'mycred_badge_display_requirements', $reply, $badge_id );

	}
endif;

/**
 * Count Users with Badge
 * Counts the number of users that has the given badge. Option to get count
 * of a specific level.
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_count_users_with_badge' ) ) :
	function mycred_count_users_with_badge( $badge_id = NULL, $level = NULL ) {

		$badge_id = absint( $badge_id );

		if ( $badge_id === 0 ) return false;

		// Get the badge object
		$badge    = mycred_get_badge( $badge_id );

		// Most likely not a badge post ID
		if ( $badge === false ) return false;

		return $badge->get_user_count( $level );

	}
endif;

/**
 * Count Users without Badge
 * Counts the number of users that does not have a given badge.
 * @since 1.5
 * @version 1.2
 */
if ( ! function_exists( 'mycred_count_users_without_badge' ) ) :
	function mycred_count_users_without_badge( $badge_id = NULL ) {

		$total      = count_users();
		$with_badge = mycred_count_users_with_badge( $badge_id );
		if ( $with_badge === false ) $with_badge = 0;

		$without_badge = $total['total_users'] - $with_badge;

		return apply_filters( 'mycred_count_users_without_badge', absint( $without_badge ), $badge_id );

	}
endif;

/**
 * Reference Has Badge
 * Checks if a given reference has a badge associated with it.
 * @since 1.5
 * @version 1.4
 */
if ( ! function_exists( 'mycred_ref_has_badge' ) ) :
	function mycred_ref_has_badge( $reference = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$badge_ids        = array();
		if ( $reference === NULL || strlen( $reference ) == 0 || ! mycred_point_type_exists( $point_type ) ) return $badge_ids;

		$badge_references = mycred_get_badge_references( $point_type );
		$badge_references = maybe_unserialize( $badge_references );

		if ( ! empty( $badge_references ) && array_key_exists( $reference, $badge_references ) )
			$badge_ids = $badge_references[ $reference ];

		if ( empty( $badge_ids ) )
			$badge_ids = false;

		return apply_filters( 'mycred_ref_has_badge', $badge_ids, $reference, $badge_references, $point_type );

	}
endif;

/**
 * Badge Level Reached
 * Checks what level a user has earned for a badge. Returns false if badge was not earned.
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_badge_level_reached' ) ) :
	function mycred_badge_level_reached( $user_id = NULL, $badge_id = NULL ) {

		$user_id  = absint( $user_id );
		$badge_id = absint( $badge_id );

		if ( $user_id === 0 || $badge_id === 0 ) return false;

		// Get the badge object
		$badge    = mycred_get_badge( $badge_id );

		// Most likely not a badge post ID
		if ( $badge === false ) return false;

		return $badge->query_users_level( $user_id );

	}
endif;

/**
 * Check if User Gets Badge
 * Checks if a given user has earned one or multiple badges.
 * @since 1.5
 * @version 1.4
 */
if ( ! function_exists( 'mycred_check_if_user_gets_badge' ) ) :
	function mycred_check_if_user_gets_badge( $user_id = NULL, $badge_ids = array(), $depreciated = array(), $save = true ) {

		$user_id          = absint( $user_id );
		if ( $user_id === 0 ) return false;

		$earned_badge_ids = array();
		if ( ! empty( $badge_ids ) ) {
			foreach ( $badge_ids as $badge_id ) {

				$badge         = mycred_get_badge( $badge_id );
				if ( $badge === false ) continue;

				$level_reached = $badge->get_level_reached( $user_id );
				if ( $level_reached !== false ) {

					if ( $save )
						$badge->assign( $user_id, $level_reached );

					$earned_badge_ids[] = $badge_id;

				}

			}
		}

		return $earned_badge_ids;

	}
endif;

/**
 * Assign Badge
 * Assigns a given badge to all users that fulfill the badges requirements.
 * @since 1.7
 * @version 1.2
 */
if ( ! function_exists( 'mycred_assign_badge' ) ) :
	function mycred_assign_badge( $badge_id = NULL ) {

		$user_id  = absint( $user_id );
		$badge_id = absint( $badge_id );

		if ( $user_id === 0 || $badge_id === 0 ) return false;

		// Get the badge object
		$badge    = mycred_get_badge( $badge_id );

		// Most likely not a badge post ID
		if ( $badge === false ) return false;

		return $badge->assign_all();

	}
endif;

/**
 * Assign Badge to User
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_assign_badge_to_user' ) ) :
	function mycred_assign_badge_to_user( $user_id = NULL, $badge_id = NULL, $level = 0 ) {

		$user_id  = absint( $user_id );
		$badge_id = absint( $badge_id );
		$level    = absint( $level );

		if ( $user_id === 0 || $badge_id === 0 ) return false;

		// Get the badge object
		$badge    = mycred_get_badge( $badge_id );

		// Most likely not a badge post ID
		if ( $badge === false ) return false;

		return $badge->assign( $user_id, $level );

	}
endif;

/**
 * User Has Badge
 * Checks if a user has a particular badge by badge ID.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_user_has_badge' ) ) :
	function mycred_user_has_badge( $user_id = 0, $badge_id = NULL, $level_id = 0 ) {

		$user_id  = absint( $user_id );
		$badge_id = absint( $badge_id );
		$level_id = absint( $level_id );

		if ( $user_id === 0 || $badge_id === 0 ) return false;

		global $mycred_current_account;

		if ( mycred_is_current_account( $user_id ) && isset( $mycred_current_account->badge_ids ) && ! empty( $mycred_current_account->badge_ids ) ) {

			$has_badge = array_key_exists( $badge_id, $mycred_current_account->badge_ids );

		}
		else {

			// Get the badge object
			$badge    = mycred_get_badge( $badge_id );

			// Most likely not a badge post ID
			if ( $badge !== false )
				$has_badge = $badge->user_has_badge( $user_id, $level_id );

		}

		return $has_badge;

	}
endif;

/**
 * Get Users Badge Level
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_users_badge_level' ) ) :
	function mycred_get_users_badge_level( $user_id = 0, $badge_id = NULL ) {

		$user_id  = absint( $user_id );
		$badge_id = absint( $badge_id );

		if ( $user_id === 0 || $badge_id === 0 ) return false;

		global $mycred_current_account;

		if ( mycred_is_current_account( $user_id ) && isset( $mycred_current_account->badges ) && ! empty( $mycred_current_account->badges ) && array_key_exists( $badge_id, $mycred_current_account->badges ) )
			return $mycred_current_account->badges[ $badge_id ]->level_id;

		// Get the badge object
		$badge    = mycred_get_badge( $badge_id );

		// Most likely not a badge post ID
		if ( $badge === false ) return false;

		return $badge->get_users_current_level( $user_id );

	}
endif;

/**
 * Get Users Badges
 * Returns the badge post IDs that a given user currently holds.
 * @since 1.5
 * @version 1.3
 */
if ( ! function_exists( 'mycred_get_users_badges' ) ) :
	function mycred_get_users_badges( $user_id = NULL, $force = false ) {

		if ( $user_id === NULL ) return array();

		global $mycred_current_account;

		if ( mycred_is_current_account( $user_id ) && isset( $mycred_current_account->badge_ids ) && $force == false )
			return $mycred_current_account->badge_ids;

		$badge_ids = mycred_get_user_meta( $user_id, MYCRED_BADGE_KEY . '_ids', '', true );
		if ( !isset($badge_ids) || $badge_ids == '' || $force ) {

			global $wpdb;

			$badge_ids = array();
			$query     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s AND meta_key NOT LIKE '%_issued_on' AND meta_key NOT LIKE '%_ids'", $user_id, mycred_get_meta_key( MYCRED_BADGE_KEY ) . '%' ) );

			if ( ! empty( $query ) ) {

				foreach ( $query as $badge ) {

					$badge_id = str_replace( MYCRED_BADGE_KEY, '', $badge->meta_key );
					if ( $badge_id == '' ) continue;
				
					$badge_id = absint( $badge_id );
					if ( ! array_key_exists( $badge_id, $badge_ids ) )
						$badge_ids[ $badge_id ] = absint( $badge->meta_value );

				}

				mycred_update_user_meta( $user_id, MYCRED_BADGE_KEY . '_ids', '', $badge_ids );

			}

		}

		$clean_ids = array();
		if ( ! empty( $badge_ids ) ) {
			foreach ( $badge_ids as $id => $level ) {

				$id = absint( $id );
				if ( $id === 0 || strlen( $level ) < 1 ) continue;
				$clean_ids[ $id ] = absint( $level );

			}
		}

		return apply_filters( 'mycred_get_users_badges', $clean_ids, $user_id );

	}
endif;

/**
 * Display Users Badges
 * Will echo all badge images a given user has earned.
 * @since 1.5
 * @version 1.3.2
 */
if ( ! function_exists( 'mycred_display_users_badges' ) ) :
	function mycred_display_users_badges( $user_id = NULL, $width = MYCRED_BADGE_WIDTH, $height = MYCRED_BADGE_HEIGHT ) {

		$user_id = absint( $user_id );
		if ( $user_id === 0 ) return;

		$users_badges = mycred_get_users_badges( $user_id );

		echo '<div class="row" id="mycred-users-badges"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';

		do_action( 'mycred_before_users_badges', $user_id, $users_badges );

		if ( ! empty( $users_badges ) ) {

			foreach ( $users_badges as $badge_id => $level ) {

				$badge = mycred_get_badge( $badge_id, $level );
				if ( $badge === false ) continue;

				$badge->image_width  = $width;
				$badge->image_height = $height;

				$badge_image = '';

				if ( $badge->level_image !== false )
					$badge_image = $badge->get_image( $level );
				else if( $badge->main_image !== false )
					$badge_image = $badge->get_image( 'main' );

				if ( !empty( $badge_image ) )
					echo apply_filters( 'mycred_the_badge', $badge_image, $badge_id, $badge, $user_id );

			}

		}

		do_action( 'mycred_after_users_badges', $user_id, $users_badges );

		echo '</div></div>';

	}
endif;

/**
 * Get Badge IDs
 * Returns all published badge post IDs.
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_badge_ids' ) ) :
	function mycred_get_badge_ids() {

		$badge_ids = wp_cache_get( 'badge_ids', MYCRED_SLUG );
		if ( $badge_ids !== false && is_array( $badge_ids ) ) return $badge_ids;

		global $wpdb;

		$table     = mycred_get_db_column( 'posts' );
		$badge_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID 
			FROM {$table} 
			WHERE post_type = %s 
			AND post_status = 'publish' 
			ORDER BY post_date ASC;", MYCRED_BADGE_KEY ) );

		wp_cache_set( 'badge_ids', $badge_ids, MYCRED_SLUG );

		return apply_filters( 'mycred_get_badge_ids', $badge_ids );

	}
endif;

/**
 * Get Badges by Term ID
 * @since 2.1
 * @version 1.0
 * @param $term_id Pass term Id
 * return posts by term Id
 */
if( !function_exists( 'mycred_get_badges_by_term_id' ) ) :
    function mycred_get_badges_by_term_id($term_id ) {
        $badge_args = query_posts(array(
                'post_type' => MYCRED_BADGE_KEY,
                'showposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => MYCRED_BADGE_CATEGORY,
                        'terms' => $term_id,
                        'field' => 'term_id',
                    )
                ),
                'orderby' => 'title',
                'order' => 'ASC' )
        );
        return $badge_args;
    }
endif;

/**
 * Get Badge/ Level Requirements
 * @since 2.1
 * @version 1.0
 * @param $badge_id Pass Badge ID
 */
if( !function_exists( 'mycred_show_badge_requirements' ) ) :
    function mycred_show_badge_requirements( $badge_id ) {

        $data = array();
        $levels = mycred_get_badge_levels( $badge_id );
        if ( empty( $levels ) ) {

            $reply = '-';

        }
        else {

            $point_types = mycred_get_types(true);
            $references = mycred_get_all_references();
            $req_count = count($levels[0]['requires']);

            // Get the requirements for the first level
            $base_requirements = array();
            foreach ($levels[0]['requires'] as $requirement_row => $requirement) {

                if ($requirement['type'] == '')
                    $requirement['type'] = MYCRED_DEFAULT_TYPE_KEY;

                if (!array_key_exists($requirement['type'], $point_types))
                    continue;

                if (!array_key_exists($requirement['reference'], $references))
                    $reference = '-';
                else
                    $reference = $references[$requirement['reference']];

                $base_requirements[$requirement_row] = array(
                    'type' => $requirement['type'],
                    'ref' => $reference,
                    'amount' => $requirement['amount'],
                    'by' => $requirement['by']
                );

            }

            // Loop through each level
            $output = array();
            foreach ($levels as $level => $setup) {
                //collecting images
                $image = false;

                if ( $setup['attachment_id'] > 0 ) {

                    $_image = wp_get_attachment_url( $setup['attachment_id'] );
                    if ( strlen( $_image ) > 5 )
                        $output['image'] = $_image;

                }
                else {

                    if ( strlen( $setup['image_url'] ) > 5 )
                        $output['image'] = $setup['image_url'];
                }

                $level_label = sprintf(__('Level %s', 'mycred'), ($level + 1));
                if ($levels[$level]['label'] != '')
                    $level_label =  $levels[$level]['label'];

                // Construct requirements to be used in an unorganized list.
                $level_req = array();
                foreach ($setup['requires'] as $requirement_row => $requirement) {
                    $level_value = $requirement['amount'];
                    $requirement = $base_requirements[$requirement_row];

                    $mycred = mycred($requirement['type']);

                    if ($level > 0)
                        $requirement['amount'] = $level_value;

                    if ($requirement['by'] == 'count')
                        $rendered_row = sprintf(_x('%s for "%s" x %d', '"Points" for "reference" x times', 'mycred'), $mycred->plural(), $requirement['ref'], $requirement['amount']);
                    else
                        $rendered_row = sprintf(_x('%s %s for "%s"', '"Gained/Lost" "x points" for "reference"', 'mycred'), (($requirement['amount'] < 0) ? __('Lost', 'mycred') : __('Gained', 'mycred')), $mycred->format_creds($requirement['amount']), $requirement['ref']);

                    $compare = _x('OR', 'Comparison of badge requirements. A OR B', 'mycred');
                    if ($setup['compare'] === 'AND')
                        $compare = _x('AND', 'Comparison of badge requirements. A AND B', 'mycred');

                    if ($req_count > 1 && $requirement_row + 1 < $req_count)
                        $rendered_row .= ' <span>' . $compare . '</span>';

                    $level_req[] = $rendered_row;

                }

                if ( empty( $level_req ) ) continue;

                $output['heading']      = $level_label;
                $output['requirements'] = $level_req;
                $output["reward_type"]  = $setup["reward"]["type"];
                $output["amount"]       = $setup["reward"]["amount"];

                array_push( $data, $output );

            }

            if ( (int) mycred_get_post_meta( $badge_id, 'manual_badge', true ) === 1 )
                $output[] = '<strong><small><em>' . __('This badge is manually awarded.', 'mycred') . '</em></small></strong>';

            return $data;
        }

    }
endif;

/**
 * Get users have have specific badge
 * @param $badge_id Pass Badge ID
 * @param int $level_id
 * @return array Will return Array of User ID's have the specific Badge
 * @since 2.1
 * @version 1.0
 */
if( !function_exists( 'mycred_get_users_has_earned_badge' ) ) :
    function mycred_get_users_has_earned_badge( $badge_id, $level_id = 0 ) {

        $users_has_badge = array();

        $badge_id = absint( $badge_id );

        $args = array(
            'fields'    =>  array(
                'ID',
                'display_name'
            )
        );

        $users = get_users( $args );

        foreach ( $users as $user ) {

            $has_badge = false;

            $user_id = $user->ID;

            // Get the badge object
            $badge = mycred_get_badge( $badge_id );

            // Most likely not a badge post ID
            if ( $badge !== false ) {
            	
                $current_level = mycred_get_user_meta( $user_id, MYCRED_BADGE_KEY . $badge_id, '', true );

                $current_level = $current_level == '0' ? true : $current_level;

                if ( $current_level )
                {
                    $has_badge = true;

                    if ( (int) $current_level < absint( $level_id ) )
                    {
                        $has_badge = false;
                    }
                    if ( $has_badge )
                        $users_has_badge[] = $user_id;
                }
            }
        }
        return $users_has_badge;

    }
endif;

/**
 * Get Badge Types
 * @since 2.1
 * @version 2.1
 * @param $badge_id Pass the Badge id
 * @return array Will return all the categories
 */
if ( !function_exists('mycred_get_badge_type') ) :
    function mycred_get_badge_type( $badge_id ) {

        $badge_terms = get_the_terms( $badge_id, MYCRED_BADGE_CATEGORY, '', ', ' );

        if (is_array( $badge_terms ) || is_object($badge_terms))
        {
            $badge_type = join( ', ', wp_list_pluck($badge_terms, 'name') );

            return $badge_type;
        }

        return false;

    }
endif;

/**
 * Get Badge Level Image Url By Passing setup of Level Requirements Use Function mycred_get_badge_requirements()
 * @since 2.1
 * @version 1.0
 * @param $setup
 * @return bool|false|mixed|string
 */
if( !function_exists( 'mycred_get_level_image_url' ) ) :
    function mycred_get_level_image_url( $setup ) {

        $image = false;

        if ( $setup['attachment_id'] > 0 ) {

            $_image = wp_get_attachment_url( $setup['attachment_id'] );
            if ( strlen( $_image ) > 5 )
                return $_image;

        }
        else {

            if ( strlen( $setup['image_url'] ) > 5 )
                return $setup['image_url'];
        }

        return $image;

    }
endif;

/**
 * Cretae Evidence page
 * @since 2.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_evidence_page_id' ) ) :
	function mycred_get_evidence_page_id() {

		$evidencePageId = 0;

		$badges = mycred_get_addon_settings( 'badges' );

        //If Open badge enabled
        if ( isset( $badges['open_badge'] ) && $badges['open_badge'] == '1' ) {

            $canCreatePage = true;

            $evidence_page_refrence = mycred_get_option( 'open_badge_evidence_page', 0 );

            if ( ! empty( $badges['open_badge_evidence_page'] ) || ! empty( $evidence_page_refrence ) ) {

            	$pageId = intval( $evidence_page_refrence );

            	if ( ! empty( $badges['open_badge_evidence_page'] ) ) {
            			
            		$pageId = intval( $badges['open_badge_evidence_page'] );

            	}

                if ( get_post_status( $pageId ) == 'publish' ) {
                    
                    $canCreatePage  = false;
                    $evidencePageId = $pageId;

                }

            }

            if ( $canCreatePage ) {

                $postData = array(
                    'post_content'   => '[' . MYCRED_SLUG . '_badge_evidence]',
                    'post_title'     => 'Badge Evidence',
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                    'post_name'      => 'Badge Evidence'
                );

                $pageId = wp_insert_post( $postData );

                $evidencePageId = intval( $pageId );

                mycred_update_option( 'open_badge_evidence_page', $evidencePageId );

                mycred_set_badge_evidence_page( $evidencePageId );

            }
        
        }

        return $evidencePageId;

    }
endif;

/**
 * Set Evidence page
 * @since 2.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_set_badge_evidence_page' ) ) :
	function mycred_set_badge_evidence_page( $page_id ) {

		$settings = mycred_get_option( 'mycred_pref_core' );

		if ( isset( $settings[ 'badges' ] ) ) {

			$settings[ 'badges' ][ 'open_badge_evidence_page' ] = intval( $page_id );

			mycred_update_option( 'mycred_pref_core', $settings );

		}

	}
endif;

/**
 * Get badges list
 * @since 2.1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_uncategorized_badge_list' ) ) :
	function mycred_get_uncategorized_badge_list() {

		$badge_list = '';

		//Get Badges
        $args = array(
            'numberposts' => -1,
            'post_type'   => MYCRED_BADGE_KEY
        );
             
        $badges = get_posts( $args );

        $user_id = get_current_user_id();
        $categories = get_categories( $args );
        $category_count = count( $categories );

        //Show Badges
        foreach ( $badges as $badge ) {

            $badge_object = mycred_get_badge( $badge->ID );

            $image_url    = $badge_object->main_image_url;

            $has_earned   = $badge_object->user_has_badge( $user_id ) ? 'earned' : 'not-earned';

            $category     = mycred_get_badge_type( $badge->ID );

            $categories   = explode( ',', $category );

            $badge_list .= '<div class="mycred-badges-list-item '.$has_earned.'" data-url="'.mycred_get_permalink( $badge->ID ).'">';

            if ( $image_url )
            	$badge_list .= '<img src="'.esc_url( $image_url ).'" alt="Badge Image">';

            $badge_list .= '<div class="mycred-left">';
            $badge_list .= '<h3>'.$badge->post_title.'</h3>';
            if( $category_count > 0 ) {

                foreach ( $categories as $category ) {
                
                    if( $category != '' ) {

                        $badge_list .= '<sup class="mycred-sup-category">'.$category.'</sup>';
                    
                    }
                
                }
            
            }

            $badge_list .= '<p>'.$badge->post_excerpt.'</p>';

            //mycred-left
            $badge_list .= '</div>';
            
            $badge_list .= '<div class="clear"></div>';

            //mycred-badges-list-item
            $badge_list .= '</div>';
           	
        }

        return $badge_list;

	}
endif;

/**
 * Get badges list by categories
 * @since 2.1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_categorized_badge_list' ) ) :
	function mycred_get_categorized_badge_list() {

		$user_id = get_current_user_id();

        $args = array(
            'taxonomy'      => MYCRED_BADGE_CATEGORY,
            'orderby'       => 'name',
            'field'         => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false
        );

        $categories         = get_categories( $args );
        $category_count     = count( $categories );
        $badges_list_tabs   = array();
        $badges_list_panels = array();

        $counter = 1;

        foreach ( $categories as $category ) {

            $category_id     = $category->cat_ID;
            $category_name   = $category->cat_name;
            $category_badges = mycred_get_badges_by_term_id( $category_id );
            $badges_count    = count( $category_badges );

            if ( $badges_count > 0 ) {
                
                $badges_list_tabs[ $category_id ]  = '<li data-id="' . $category_id . '" '. ( $counter == 1 ? 'class="active"' : '' ) .'>';
                $badges_list_tabs[ $category_id ] .= $category_name . '<span class="mycred-badge-count">' . $badges_count . '</span>';
                $badges_list_tabs[ $category_id ] .= '</li>';

                $badges_list_panels[ $category_id ]  = '<div data-id="'.$category_id.'" class="mycred-badges-list-panel '. ( $counter == 1 ? 'active' : '' ) .'">';
                    
                foreach ( $category_badges as $badge ) {

                    $badge_id     = $badge->ID;

                    $badge_object = mycred_get_badge( $badge_id );

                    $image_url    = $badge_object->main_image_url;

                    $has_earned   = $badge_object->user_has_badge( $user_id ) ? 'earned' : 'not-earned';

                    $badges_list_panels[ $category_id ] .= '<div class="mycred-badges-list-item '. $has_earned .'" data-url="' . mycred_get_permalink( $badge_id ) . '">';

                    if ( $image_url ) {

                        $badges_list_panels[ $category_id ] .= '<img src="' . esc_url( $image_url ) . '" alt="Badge Image">';
                    
                    }

                    $badges_list_panels[ $category_id ] .= '<div class="mycred-left"><h3>' . $badge->post_title . '</h3>' . $badge->post_excerpt . '</div>';
                    $badges_list_panels[ $category_id ] .= '<div class="clear"></div>';
                    $badges_list_panels[ $category_id ] .= '</div>';
                    
                }

                $badges_list_panels[ $category_id ] .= '</div>';

                $counter++;

            }

        }

        return array(
        	'tabs'           => $badges_list_tabs,
        	'panels'         => $badges_list_panels,
        	'category_count' => $category_count,
        );

	}
endif;

/**
 * Returns Badge congratulation message.
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_badge_show_congratulation_msg' ) ) :
	function mycred_badge_show_congratulation_msg( $user_id, $badge, $settings = NULL ) {

		$content = '';

		if ( empty( $settings ) ) $settings = mycred();

		if( ! empty( $settings->core["badges"]["show_congo_text"] ) && $badge->user_has_badge( $user_id ) && ! empty( $badge->congratulation_msg ) ) {

			$content .= '<div class="mycred-badge-congratulation-msg">' . $badge->congratulation_msg . '</div>';
		
		}

		return apply_filters( 'mycred_badge_show_congratulation_msg', $content, $badge, $settings );

	}
endif;

/**
 * Returns Badge main image with share icons.
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_badge_show_main_image_with_social_icons' ) ) :
	function mycred_badge_show_main_image_with_social_icons( $user_id, $badge, $mycred = NULL ) {

		$content = '';

		$image_url = $badge->get_earned_image( $user_id );

		if ( ! empty( $image_url ) ) {

			$content .= '<div class="mycred-badge-image-wrapper">';

			$content .= '<img src="' . $image_url . '" class="mycred-badge-image" alt="Badge Image">';

			if ( empty( $mycred ) ) $mycred = mycred();

			//If user has earned badge, show user sharing badge option
            if( 
            	$badge->user_has_badge( $user_id ) && 
            	! empty( $mycred->core["br_social_share"]["enable_open_badge_ss"] ) 
            ) {

                $facebook_url  = "http://www.facebook.com/sharer.php?u=".get_permalink()."&p[images][0]=$image_url";
                $twitter_url   = "https://twitter.com/share?url=".get_permalink()."";
                $linkedin_url  = "http://www.linkedin.com/shareArticle?url=".get_permalink()."";
                $pinterest_url = "https://pinterest.com/pin/create/bookmarklet/?media=$image_url&amp;url=".get_permalink()."";

                $content .= mycred_br_get_social_icons( $facebook_url, $twitter_url, $linkedin_url, $pinterest_url );

            } 

            $content .= '</div>';
			
		}

		return apply_filters( 'mycred_badge_show_main_image_with_social_icons', $content, $badge, $mycred );

	}
endif;

/**
 * Returns Badge description.
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_badge_show_description' ) ) :
	function mycred_badge_show_description( $post, $settings = NULL ) {

		$content = '';

		if ( empty( $settings ) ) $settings = mycred();

		if( ! empty( $settings->core["badges"]["show_level_description"] ) && ! empty( $post->post_content ) ) {

			$content .= "<h3>" . __( "Description", "mycred" ) . "</h3>";
            $content .= "<p>" . $post->post_content . "</p>";
		
		}

		return apply_filters( 'mycred_badge_show_description', $content, $post, $settings );

	}
endif;

/**
 * Returns Badge levels.
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_badge_show_levels' ) ) :
	function mycred_badge_show_levels( $user_id, $badge, $settings = NULL ) {

		$content = '';

		if ( empty( $settings ) ) $settings = mycred();

		if( ! empty( $settings->core["badges"]["show_levels"] ) || $badge->open_badge && ! empty( $settings->core["badges"]["show_steps_to_achieve"] ) ) {

			if ( ! $badge->open_badge )
                $content .= "<h3>" . __("Levels", "mycred") . "</h3>";
            else 
                $content .= "<h3>" . __("Requirement", "mycred") . "</h3>";

            $levels = mycred_show_badge_requirements( $badge->post_id );

            foreach ( $levels as $id => $level ) {

	            $level_image_url = $level["image"];

	            $heading = $level["heading"];

	            $requirements = $level["requirements"];

	            $reward = $level["amount"];

	            $content .= '<div class="mycred-badge-page-level">';

	            if ( ! $badge->open_badge ) {

	                if ( ! empty( $level_image_url ) )
	                    $content .= '<img src="'.$level_image_url.'" class="mycred-level-image mycred-float-left" />';

	                $content .= "<h4>$heading</h4>";
	            }

	            $content .= "<div class='clear'></div>";

	            if ( ! empty( $settings->core["badges"]["show_steps_to_achieve"] ) )
	                $content .= mycred_badge_level_req_check( $badge->post_id, $id );

	            if ( ! empty( $settings->core["badges"]["show_level_points"] ) && $reward != 0 ) {

	                $reward_type = mycred( $level['reward_type'] );

	                $content .= '<div class="mycred-level-reward">' . __('Reward:', 'mycred') . ' ' . $reward_type->format_creds($reward ) .'</div>';
	            }

	            $content .= '</div>';
	        }

	        $content .= '<div class="clear"></div>';
		
		}

		return apply_filters( 'mycred_badge_show_levels', $content, $user_id, $badge, $settings );

	}
endif;

/**
 * Returns badge earners.
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_badge_show_earners' ) ) :
	function mycred_badge_show_earners( $badge, $settings = NULL ) {

		$content = '';

		if ( empty( $settings ) ) $settings = mycred();

		if ( ! empty( $settings->core["badges"]["show_earners"] ) ) {

			$args = array(
			    'meta_query' => array(
			        array(
			            'key' => MYCRED_BADGE_KEY . $badge->post_id,
			            'compare' => 'EXISTS'
			        )
			    )
			);
			$users_have_badge = get_users( $args );

			if ( ! empty( $users_have_badge ) ) {
				
				$content .= '<div class="mycred-badge-earners">';
				$content .= "<h3>" . __("Earners", "mycred") . "</h3>";
                $content .= '<div class="mycred-badge-earner-list">';

				foreach ( $users_have_badge as $user ) {

	                $user_avatar = get_avatar_url( $user->ID );

	                $content .= '<div class="mycred-badge-earner-grid mycred-float-left">';
	                $content .= '<div><img src="' . $user_avatar . '" /></div>';
	                $content .= "<h4>$user->display_name</h4>";
	                $content .= '</div>';

				}
				$content .= '<div class="mycred-clearfix"></div>';
                $content .= '</div>';
				$content .= '</div>';

			}
			
		}

		return apply_filters( 'mycred_badge_show_earners', $content, $badge, $settings );

	}
endif;