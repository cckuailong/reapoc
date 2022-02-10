<?php

/**
 * Move form fields from meta to a post_type
 *
 * @return void
 */
function wpuf_upgrade_2_1_9_form_fields() {
    $posts = get_posts( [
        'post_type'   => [ 'wpuf_forms', 'wpuf_profile' ],
        'numberposts' => '-1',
    ] );

    if ( !$posts ) {
        return;
    }

    foreach ( $posts as $key => $post ) {
        $posts_meta = get_post_meta( $post->ID, 'wpuf_form', true );
        $posts_meta = is_array( $posts_meta ) ? $posts_meta : [];

        foreach ( $posts_meta as $key => $post_meta ) {
            $post_meta['wpuf_cond'] = [];

            // if key empty then replace by its value
            if ( array_key_exists( 'options', $post_meta ) ) {
                foreach ( $post_meta['options'] as $key => $value ) {
                    $post_meta['options'][$value] = $value;
                    unset( $post_meta['options'][$key] );
                }
            }

            wpuf_insert_form_field( $post->ID, $post_meta, null, $key );
            delete_post_meta( $post->ID, 'wpuf_form' );
        }
    }
}

/**
 * Move subscriptions to post type from custom table
 *
 * @return void
 */
function wpuf_upgrade_2_1_9_subscription() {
    global $wpdb;

    $table   = $wpdb->prefix . 'wpuf_subscription';
    $results = $wpdb->get_results( "SELECT name, description, count, duration, cost FROM $table" );

    if ( !$results ) {
        return;
    }

    $post_type = WPUF_Subscription::init()->get_all_post_type();

    foreach ( $results as $key => $result ) {
        $args = [
            'post_title'   => $result->name,
            'post_content' => $result->description,
            'post_status'  => 'publish',
            'post_type'    => 'wpuf_subscription',
        ];

        $post_ID = wp_insert_post( $args );

        if ( $post_ID ) {
            foreach ( $post_type as $key => $name ) {
                $post_type[$key] = $result->count;
            }

            $post = [
                'cost'           => $result->cost,
                'duration'       => $result->duration,
                'recurring_pay'  => 'no',
                'trial_period'   => '',
                'post_type_name' => $post_type,
            ];

            wpuf_get_user( $post_ID )->subscription()->update_meta( $post );
        }
    }

    $sql = "DROP TABLE IF_EXISTS $table";
    $wpdb->query( $sql );
}

wpuf_upgrade_2_1_9_form_fields();
wpuf_upgrade_2_1_9_subscription();
