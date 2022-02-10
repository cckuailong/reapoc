<?php

	define( 'WPRSS_TRANSIENT_NAME_IS_REIMPORTING', 'is_reimporting' );

    /**
     * Feed processing related functions
     *
     * @package WPRSSAggregator
     */

    /**
     * Returns whether or not the feed source will forcefully fetch the next fetch,
     * ignoring whether or not it is paused or not.
     *
     * @param $source_id    The ID of the feed soruce
     * @return boolean
     * @since 3.7
     */
    function wprss_feed_source_force_next_fetch( $source_id ) {
        $force = get_post_meta( $source_id, 'wprss_force_next_fetch', TRUE );
        return ( $force !== '' || $force == '1' );
    }


    /**
     * Change the default feed cache recreation period to 2 hours
     *
     * Probably not needed since we are now disabling caching altogether
     *
     * @since 2.1
     */
    function wprss_feed_cache_lifetime( $seconds ) {
        return 1; // one second
    }


    /**
     * Disable caching of feeds in transients, we don't need it as we are storing them in the wp_posts table
     *
     * @since 3.0
     */
    function wprss_do_not_cache_feeds( &$feed ) {
        $feed->enable_cache( false );
    }


    /**
     * Parameters for query to get all feed sources
     *
     * @since 3.0
     */
    function wprss_get_all_feed_sources() {
        // Get all feed sources
        $feed_sources = new WP_Query( apply_filters(
            'wprss_get_all_feed_sources',
            array(
                'post_type'      => 'wprss_feed',
                'post_status'    => 'publish',
                'cache_results'  => false,   // Disable caching, used for one-off queries
                'no_found_rows'  => true,    // We don't need pagination, so disable it
                'posts_per_page' => -1
            )
        ) );
        return $feed_sources;
    }


    /**
     * Retrieves the query to use for retrieving imported items.
     *
     * @since 4.17.4
     */
    function wprss_get_imported_items_query($source_id = null) {
        $args = [
            'post_type'             => array_values(get_post_types()),
            'post_status'           => 'any',
            'cache_results'         => false,
            'no_found_rows'         => true,
            'posts_per_page'        => -1,
            'ignore_sticky_posts'   => 'true',
            'orderby'               => 'date',
            'order'                 => 'DESC',
            'meta_query'            => [
                'relation' => 'AND',
            ],
            'suppress_filters'  => 1
        ];

        if ($source_id !== null) {
            $args['meta_query'][] = [
                'key'       => 'wprss_feed_id',
                'value'     => (string) $source_id,
                'compare'   => '=',
            ];
        } else {
            $args['meta_query'][] = [
                'key'       => 'wprss_feed_id',
                'compare'   => 'EXISTS',
            ];
        }

        return apply_filters('wprss_get_feed_items_for_source_args', $args, $source_id);
    }

    /**
     * Queries for imported items.
     *
     * @since 4.17.4
     */
    function wprss_get_imported_items($source_id = null) {
        return new WP_Query(wprss_get_imported_items_query($source_id));
    }

    /**
     * Returns all the feed items of a source.
     *
     * @since 3.8
     */
    function wprss_get_feed_items_for_source( $source_id ) {
        return wprss_get_imported_items($source_id);
    }


    /**
     * Parameters for query to get feed sources
     *
     * @since 3.0
     */
    function wprss_get_feed_source() {
        // Get all feed sources
        $feed_sources = new WP_Query( apply_filters(
            'wprss_get_all_feed_sources',
            array(
                'post_type'      => 'wprss_feed',
                'post_status'    => 'publish',
                'cache_results'  => false,   // Disable caching, used for one-off queries
                'no_found_rows'  => true,    // We don't need pagination, so disable it
                'posts_per_page' => -1
            )
        ) );
        return $feed_sources;
    }


    /**
     * Database query to get existing permalinks
     *
     * @since 3.0
     */
    function wprss_get_existing_permalinks( $feed_ID ) {
        global $wpdb;

        $cols = $wpdb->get_col(
            "SELECT q.`meta_value`
            FROM {$wpdb->postmeta} AS p
            JOIN {$wpdb->postmeta} AS q ON (q.`meta_key` = 'wprss_item_permalink' AND p.`post_id` = q.`post_id`)
            WHERE p.`meta_key` = 'wprss_feed_id' AND p.`meta_value` = '{$feed_ID}'"
        );

        return @array_flip($cols);
    }

    /**
     * Checks if an item title exists in the database.
     *
     * @since 4.14
     *
     * @param string $title The title to search for.
     *
     * @return bool True if the title exists, false if not.
     */
    function wprss_item_title_exists( $title ) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT *
                FROM `{$wpdb->posts}` AS p
                JOIN `{$wpdb->postmeta}` AS q ON p.`ID` = q.`post_id`
                WHERE q.`meta_key` = 'wprss_feed_id' AND p.`post_title` = %s",
            [html_entity_decode($title)]
        );

        $cols = $wpdb->get_col($query);

        return count($cols) > 0;
    }

    /**
     * Database query to get existing titles
     *
     * @since 4.7
     */
    function wprss_get_existing_titles( $feed_ID = NULL ) {
        global $wpdb;

        $condition = ($feed_ID !== NULL) ? "AND q.`meta_value` = '{$feed_ID}'" : '';

        $cols = $wpdb->get_col(
            "SELECT p.`post_title`
            FROM `{$wpdb->posts}` AS p
            JOIN `{$wpdb->postmeta}` AS q ON p.`ID` = q.`post_id`
            WHERE q.`meta_key` = 'wprss_feed_id' $condition"
        );

        return @array_flip($cols);
    }


    /**
     * Checks if a permalink exists.
     *
     * Untested!
     *
     * @param  string $permalink The permalink, expected to be normalized.
     * @return   bool
     */
    function wprss_permalink_exists( $permalink ) {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "SELECT *
                FROM {$wpdb->postmeta}
                WHERE `meta_value` = '{$permalink}'"
            )
        );

        return $wpdb->num_rows > 0;
    }


    add_action( 'publish_wprss_feed', 'wprss_fetch_insert_feed_items', 10 );
    /**
     * Fetches feed items from source provided and inserts into db
     *
     * This function is used when inserting or untrashing a new feed source, it only gets feeds from that particular source
     *
     * @since 3.0
     */
    function wprss_fetch_insert_feed_items( $post_id ) {
        wp_schedule_single_event( time(), 'wprss_fetch_single_feed_hook', array( $post_id ) );
    }


    /**
     * Returns the image of the feed.
     * The reason this function exists is for add-ons to be able to detect if the plugin core
     * supports feed image functionality through a simple function_exists() call.
     *
     * @param $source_id The ID of the feed source
     * @return string The link to the feed image
     * @since 1.0
     */
    function wprss_get_feed_image( $source_id ) {
        return get_post_meta( $source_id, 'wprss_feed_image', true );
    }


    add_action( 'post_updated', 'wprss_updated_feed_source', 10, 3 );
    /**
     * This function is triggered just after a post is updated.
     * It checks if the updated post is a feed source, and carries out any
     * updating necassary.
     *
     * @since 3.3
     */
    function wprss_updated_feed_source( $post_ID, $post_after, $post_before ) {
        // Check if the post is a feed source and is published

        if ( ( $post_after->post_type == 'wprss_feed' ) && ( $post_after->post_status == 'publish' ) ) {

            if ( isset( $_POST['wprss_url'] ) && !empty( $_POST['wprss_url'] ) ) {
                $url = $_POST['wprss_url'];
                $feed = wprss_fetch_feed( $url );
                if ( $feed !== NULL && !is_wp_error( $feed ) ) {
                    update_post_meta( $post_ID, 'wprss_site_url', $feed->get_permalink() );
                    update_post_meta( $post_ID, 'wprss_feed_image', $feed->get_image_url() );
                }
            }


        	if ( isset( $_POST['wprss_limit'] ) && !empty( $_POST['wprss_limit'] ) ) {
	            // Checking feed limit change
	            // Get the limit currently saved in db, and limit in POST request
	            //$limit = get_post_meta( $post_ID, 'wprss_limit', true );
	            $limit = $_POST['wprss_limit'];
	            // Get all feed items for this source
	            $feed_sources = new WP_Query(
					array(
						'post_type'      => 'wprss_feed_item',
						'post_status'    => 'publish',
						'cache_results'  => false,   // Disable caching, used for one-off queries
						'no_found_rows'  => true,    // We don't need pagination, so disable it
						'posts_per_page' => -1,
						'orderby' 		 => 'date',
						'order' 		 => 'ASC',
						'meta_query'     => array(
							array(
								'key'     => 'wprss_feed_id',
								'value'   => $post_ID,
								'compare' => 'LIKE'
							)
						)
					)
	            );
	            // If the limit is smaller than the number of found posts, delete the feed items
	            // and re-import, to ensure that most recent feed items are present.
	            $difference = intval( $feed_sources->post_count ) - intval( $limit );
	            if ( $difference > 0 ) {
	            	// Loop and delete the excess feed items
					while ( $feed_sources->have_posts() && $difference > 0 ) {
						$feed_sources->the_post();
						wp_delete_post( get_the_ID(), true );
						$difference--;
					}
	            }
        	}
        }
    }


    add_action( 'added_post_meta', 'wprss_update_feed_meta', 10, 4 );
    add_action( 'updated_post_meta', 'wprss_update_feed_meta', 10, 4 );
    /**
     * This function is run whenever a post is saved or updated.
     *
     * @since 3.4
     */
    function wprss_update_feed_meta( $meta_id, $post_id, $meta_key, $meta_value ) {
        $post = get_post( $post_id );
        if ( $post !== NULL && $post->post_status === 'publish' && $post->post_type === 'wprss_feed' ) {
            if ( $meta_key === 'wprss_url' )
                wprss_change_fb_url( $post_id, $meta_value );
        }
    }


    function wprss_change_fb_url( $post_id, $url ) {
        # Check if url begins with a known facebook hostname.
        if (    stripos( $url, 'http://facebook.com' ) === 0
            ||  stripos( $url, 'http://www.facebook.com' ) === 0
            ||  stripos( $url, 'https://facebook.com' ) === 0
            ||  stripos( $url, 'https://www.facebook.com' ) === 0
        ) {
            # Generate the new URL to FB Graph
            $com_index = stripos( $url, '.com' );
            $fb_page = substr( $url, $com_index + 4 ); # 4 = length of ".com"
            $fb_graph_url = 'https://graph.facebook.com' . $fb_page;
            # Contact FB Graph and get data
            $response = wp_remote_get( $fb_graph_url );
            # If the repsonse successful and has a body
            if ( !is_wp_error( $response ) && isset( $response['body'] ) ) {
                # Parse the body as a JSON string
                $json = json_decode( $response['body'], true );
                # If an id is present ...
                if ( isset( $json['id'] ) ) {
                    # Generate the final URL for this feed and update the post meta
                    $final_url = "https://www.facebook.com/feeds/page.php?format=rss20&id=" . $json['id'];
                    update_post_meta( $post_id, 'wprss_url', $final_url, $url );
                }
            }
        }
    }


    add_action( 'trash_wprss_feed', 'wprss_delete_feed_items' );   // maybe use wp_trash_post action? wp_trash_wprss_feed
    /**
     * Delete feed items on trashing of corresponding feed source
     *
     * @since 2.0
     */
    function wprss_delete_feed_items ($source_id) {
        $force_delete = apply_filters('wprss_force_delete_when_by_source', true);

        // WPML fix: removes the current language from the query WHERE and JOIN clauses
        global $sitepress;
        if ($sitepress !== null) {
            remove_filter('posts_join', [$sitepress,'posts_join_filter']);
            remove_filter('posts_where', [$sitepress,'posts_where_filter']);
        }

        $args = wprss_get_imported_items_query($source_id);
        $items = get_posts($args);

        foreach ($items as $item) {
            wp_delete_post($item->ID, $force_delete);
        }
    }


    add_action( 'wprss_delete_all_feed_items_hook', 'wprss_delete_all_feed_items' );
    /**
     * Delete all feed items
     *
     * @since 3.0
     */
    function wprss_delete_all_feed_items() {
        $args = wprss_get_imported_items_query();
        $items = get_posts($args);

        foreach ($items as $item) {
            wp_delete_post($item->ID, true);
        }
    }


    /**
     * Marks the feed source as 'updating' (importing).
     *
     * @since 4.6.6
     * @return int The time value set in the 'updating' meta field
     */
    function wprss_flag_feed_as_updating( $feed_ID ) {
        update_post_meta( $feed_ID, 'wprss_feed_is_updating', $start_time = time() );
        return $start_time;
    }

    /**
     * Marks the feed source as 'idle' (not importing).
     *
     * @since 4.6.6
     */
    function wprss_flag_feed_as_idle( $feed_ID ) {
        update_post_meta( $feed_ID, 'wprss_feed_is_updating', '' );
    }


	/**
     * Returns whether or not the feed source is updating.
     *
     * @param (string|int) The id of the feed source
     * @return (bool) TRUE if the feed source is currently updating, FALSE otherwise.
     *
     */
    function wprss_is_feed_source_updating( $id ) {
        // Get the 'updating' meta field
        $is_updating_meta = get_post_meta( $id, 'wprss_feed_is_updating', TRUE );

        // Check if the feed has the 'updating' meta field set
        if ( $is_updating_meta === '' ) {
            // If not, then the feed is not updating
            return FALSE;
        }

        // Get the limit used for the feed
        $limit = get_post_meta( $id, 'wprss_limit', true );
        if ( $limit === '' || intval( $limit ) <= 0 ) {
            $global_limit = wprss_get_general_setting('limit_feed_items_imported');
            $limit = ( $global_limit === '' || intval( $global_limit ) <= 0 ) ? NULL : $global_limit;
        }

		// Calculate the allowed maximum time, based on the maximum number of items allowed to be
        // imported from this source.
        // If no limit is used, 60s (1min) is used.
        $single_item_time_limit = wprss_get_feed_fetch_time_limit();
		$allowed_time = $limit === NULL ? 120 : $single_item_time_limit * intval( $limit );

        // Calculate how many seconds have passed since the feed last signalled that it is updating
        $diff = time() - $is_updating_meta;

        // Get the transient that is set when the import function is called and the time of the next scheduled cron
        $is_updating_transient = get_transient('wpra/feeds/importing/' . $id);
        $scheduled = (wprss_get_next_feed_source_update($id) !== false);
        // If more than 5 seconds have passed and the transient is not yet set and the cron was not scheduled
        // then the cron probably failed to be registered
        if ( $diff > 5  && !$is_updating_transient && !$scheduled) {
            wprss_flag_feed_as_idle($id);
            update_post_meta(
                $id,
                'wprss_error_last_import',
                __('The plugin failed to schedule a fetch for this feed. Please try again.', 'wprss')
            );

            return false;
        }

        // If the difference is greater than the allowed maximum amount of time, mark the feed as idle.
		if ( $diff > $allowed_time ) {
			wprss_flag_feed_as_idle( $id );
            // Feed is not updating
			return FALSE;
		}

        // Feed is updating
		return TRUE;
    }


    /**
     * Returns whether or not the feed source is deleting its feeed items.
     *
     * @param (string|int) The id of the feed source
     * @return (bool) TRUE if the feed source is currently deleting its items, FALSE otherwise.
     *
     */
    function wprss_is_feed_source_deleting( $id ) {
        $is_deleting_meta = get_post_meta( $id, 'wprss_feed_is_deleting_items', TRUE );

        if ( $is_deleting_meta === '' ) {
            return FALSE;
        }

		$diff = time() - $is_deleting_meta;

        $items = wprss_get_feed_items_for_source( $id );
        if ( $items->post_count === 0 || $diff > 300 ) {
            delete_post_meta( $id, 'wprss_feed_is_deleting_items' );
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Returns the given parameter as a string. Used in wprss_truncate_posts()
     *
     * @return string The given parameter as a string
     * @since 3.5.1
     */
    function wprss_return_as_string( $item ) {
        return "'$item'";
    }


    /**
     * Returns true if the feed item is older than the given timestamp,
     * false otherwise;
     *
     * @since 3.8
     */
    function wprss_is_feed_item_older_than( $id, $timestamp ) {
        // GET THE DATE
        $age = get_the_time( 'U', $id );
        if ( $age === '' ) return FALSE;
        // Calculate the age difference
        $difference = $age - $timestamp;
        // Return whether the difference is negative ( the age is smaller than the timestamp )
        return ( $difference <= 0 );
    }


    /**
     * Returns the maximum age setting for a feed source.
     *
     * @since 3.8
     */
    function wprss_get_max_age_for_feed_source( $source_id ) {
        $general_settings = get_option( 'wprss_settings_general' );
        // Get the meta data for age for this feed source
        $age_limit = trim( get_post_meta( $source_id, 'wprss_age_limit', TRUE ) );
        $age_unit = get_post_meta( $source_id, 'wprss_age_unit', TRUE );

        // If the meta does not exist, use the global settings
        if( $age_limit === '' ) {
            $age_limit = trim( wprss_get_general_setting( 'limit_feed_items_age' ) );
            $age_unit = wprss_get_general_setting( 'limit_feed_items_age_unit' );
        }

        // If the age limit is an empty string, use no limit
        if ( $age_limit === '' ) {
            return FALSE;
        }

        // Return the timestamp of the max age date
        return strtotime( "-$age_limit $age_unit" );
    }

    /**
     * Truncates the items for a single feed source based on its age limit.
     *
     * @since 4.14
     *
     * @param int|WP_Post $source The source ID or post instance.
     */
    function wprss_truncate_items_for_source( $source )
    {
        $id = ( $source instanceof WP_Post )
            ? $source->ID
            : $source;

        $logger = wpra_get_logger($id);

        // Get the max age setting for this feed source
        $max_age = wprss_get_max_age_for_feed_source( $id );

        // If the data is empty, do not delete
        if ( $max_age === false ) {
            return;
        }

        // Get all feed items for this source
        $feed_items = wprss_get_feed_items_for_source( $id );

        // If there are no feed items, stop
        if ( ! $feed_items->have_posts() ) {
            return;
        }

        // Extend the timeout time limit for the deletion of the feed items
        set_time_limit( wprss_get_item_import_time_limit() );

        $logger->debug('Truncating existing items');

        // For each feed item
        while ( $feed_items->have_posts() ) {
            $feed_items->the_post();
            // If the post is older than the maximum age
            $item_id = get_the_ID();

            if ( wprss_is_feed_item_older_than( $item_id, $max_age ) === true ){
                // Delete the post
                wp_delete_post( $item_id, true );
            }
        }

        // Reset feed items query data
        wp_reset_postdata();
    }

    /**
     * Delete old feed items from the database to avoid bloat.
     * As of 3.8, it uses the new feed age system.
     *
     * @since 3.8
     */
    function wprss_truncate_posts() {
        // Get general settings
        $general_settings = get_option( 'wprss_settings_general' );
        // Get all feed sources
        $feed_sources = wprss_get_all_feed_sources();

        // Check if there are feed sources
        if( $feed_sources->have_posts() ) {
            // Truncate items for each feed source
            while ( $feed_sources->have_posts() ) {
                $feed_sources->the_post();
                wprss_truncate_items_for_source( get_the_ID() );
            }
            // Reset feed sources query data
            wp_reset_postdata();
        }

        // If the filter to use the fixed limit is enabled, call the old truncation function
        if ( apply_filters( 'wprss_use_fixed_feed_limit', FALSE ) === TRUE && isset( $general_settings['limit_feed_items_db'] ) ) {
            wprss_old_truncate_posts();
        }
    }


    /**
     * The old truncation function.
     * This truncation method uses the deprecated fixed feed limit.
     *
     * @since 2.0
     */
    function wprss_old_truncate_posts() {
        global $wpdb;
        $general_settings = get_option( 'wprss_settings_general' );

        if ( $general_settings['limit_feed_items_db'] == 0 ) {
            return;
        }

        // Set your threshold of max posts and post_type name
        $threshold = $general_settings['limit_feed_items_db'];
        $post_types = apply_filters( 'wprss_truncation_post_types', array( 'wprss_feed_item' ) );
        $post_types_str = array_map( 'wprss_return_as_string', $post_types );

        $post_type_list = implode( ',' , $post_types_str );

        // Query post type
        // $wpdb query allows me to select specific columns instead of grabbing the entire post object.
        $query = "
            SELECT ID, post_title FROM $wpdb->posts
            WHERE post_type IN ($post_type_list)
            AND post_status = 'publish'
            ORDER BY post_modified DESC
        ";

        $results = $wpdb->get_results( $query );

        // Check if there are any results
        $i = 0;
        if ( count( $results ) ){
            foreach ( $results as $post ) {
                $i++;

                // Skip any posts within our threshold
                if ( $i <= $threshold )
                    continue;

                // Let the WordPress API do the heavy lifting for cleaning up entire post trails
                $purge = wp_delete_post( $post->ID, true );
            }
        }
    }


    add_filter( 'wprss_insert_post_item_conditionals', 'wprss_check_feed_item_date_on_import', 2, 3 );
    /**
     * When a feed item is imported, it's date is compared against the max age of it's feed source.
     *
     *
     * @since 3.8
     */
    function wprss_check_feed_item_date_on_import( $item, $source, $permalink ){
        if ( $item === NULL ) return NULL;

        // Get the age of the item and the max age setting for its feed source
        $age = $item->get_date( 'U' );
        $max_age = wprss_get_max_age_for_feed_source( $source );

        // If the age is not a valid timestamp, and the max age setting is disabled, return the item
        if ( $age === '' || $age === NULL || $max_age === FALSE || $max_age === NULL ) {
            return $item;
        }

        // Calculate the age difference
        $difference = $age - $max_age;

        if ( $difference <= 0 ) {
            wpra_get_logger()->debug('Item "{0}" was rejected by age limit settings.', [
                $item->get_title()
            ]);

            return NULL;
        } else {
            return $item;
        }
    }


    /**
     * Deletes all imported feeds.
     *
     * @since 3.0
     */
    function wprss_feed_reset() {
        wp_schedule_single_event( time(), 'wprss_delete_all_feed_items_hook' );
    }



	function wprss_schedule_reimport_all($deleted_ids) {
		if( !get_transient( WPRSS_TRANSIENT_NAME_IS_REIMPORTING ) )
			return;

		wprss_log( 'Re-import scheduled...', __FUNCTION__, WPRSS_LOG_LEVEL_SYSTEM);
		delete_transient( WPRSS_TRANSIENT_NAME_IS_REIMPORTING );
		wprss_fetch_insert_all_feed_items( TRUE );
	}


    /**
     * Deletes N oldest feed items for the given source
     *
     * @since 4.2
     * @deprecated
     */
    function wprss_delete_oldest_feed_items( $n, $source ) {
        // If the source does not exist, do nothing
        if ( get_post( $source ) == NULL ) return;

        // Make sure $n is an integer
        $n = intval($n);

        // Do nothing if n is zero or negative
        if ( $n <= 0 ) return;

        // Get the feed items, as an array, not WP_Query.
        // We will need to perform some array operations
        $feed_items = wprss_get_feed_items_for_source( $source );
        $feed_items = $feed_items->get_posts();
        // Get number of feed items
        $count = count( $feed_items );

        // Index of first feed item to delete
        $start = $count - $n;
        // Cut the array of feed items to get the items to delete
        $to_delete = array_slice( $feed_items, $start );
        // log -- for now
        foreach( $to_delete as $fi ) {
            //wprss_log_obj( "To delete" , $fi->ID );
        }
    }


    /**
     * Deletes the required number of feed items for the given source,
     * to keep the number of feed items below its limit.
     *
     * @since 4.2
     * @deprecated
     */
    function wprss_truncate_feed_items_for_source( $source ) {
        // Get the limit setting
        $limit = get_post_meta( $source, 'wprss_limit', true );

        // Calculate the number of feed items to delete
        $feed_items = wprss_get_feed_items_for_source( $source );
        $n = intval($feed_items->found_posts) - intval($limit);

        // Delete the feed items
        wprss_delete_oldest_feed_items( $n, $source );
    }
