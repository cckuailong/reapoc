<?php

/*
 * Provides some helpful functions to deal with the transactions
 */

class SwpmTransactions {

	static function save_txn_record( $ipn_data, $items = array() ) {
		global $wpdb;

		$current_date = SwpmUtils::get_current_date_in_wp_zone();//date( 'Y-m-d' );
		$custom_var   = self::parse_custom_var( $ipn_data['custom'] );

		$txn_data                     = array();
		$txn_data['email']            = $ipn_data['payer_email'];
		$txn_data['first_name']       = $ipn_data['first_name'];
		$txn_data['last_name']        = $ipn_data['last_name'];
		$txn_data['ip_address']       = $ipn_data['ip'];
		$txn_data['member_id']        = isset ( $custom_var['swpm_id'] ) ? $custom_var['swpm_id'] : '';
		$txn_data['membership_level'] = isset ( $custom_var['subsc_ref'] ) ? $custom_var['subsc_ref'] : '';

		$txn_data['txn_date']       = $current_date;
		$txn_data['txn_id']         = $ipn_data['txn_id'];
		$txn_data['subscr_id']      = $ipn_data['subscr_id'];
		$txn_data['reference']      = isset( $custom_var['reference'] ) ? $custom_var['reference'] : '';
		$txn_data['payment_amount'] = $ipn_data['mc_gross'];
		$txn_data['gateway']        = $ipn_data['gateway'];
		$txn_data['status']         = $ipn_data['status'];

		$txn_data = array_filter( $txn_data );//Remove any null values.
		$wpdb->insert( $wpdb->prefix . 'swpm_payments_tbl', $txn_data );

		$db_row_id = $wpdb->insert_id;

                /*** Save to the swpm_transactions CPT also ***/
		//Let's also store the transactions data in swpm_transactions CPT.
		$post                = array();
		$post['post_title']  = '';
		$post['post_status'] = 'publish';
		$post['content']     = '';
		$post['post_type']   = 'swpm_transactions';

		$post_id = wp_insert_post( $post );

		update_post_meta( $post_id, 'db_row_id', $db_row_id );

                //Add the payment_button_id to the txn_data array so it can be saved to the swpm_transactions CPT.
		if ( isset( $ipn_data['payment_button_id'] ) ) {
			$txn_data['payment_button_id'] = $ipn_data['payment_button_id'];
		}

                //Add the is_live to the txn_data array so it can be saved to the swpm_transactions CPT.
		if ( isset( $ipn_data['is_live'] ) ) {
			$txn_data['is_live'] = $ipn_data['is_live'];
		}

                //Add the custom value to the txn_data array so it can be saved to the swpm_transactions CPT.
		if ( isset( $ipn_data['custom'] ) ) {
			$txn_data['custom'] = $ipn_data['custom'];
		}

                //Save the $txn_data to the swpm_transactions CPT as post meta.
		foreach ( $txn_data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

                //Trigger the action hook.
		do_action( 'swpm_txn_record_saved', $txn_data, $db_row_id, $post_id );

	}

	static function parse_custom_var( $custom ) {
		$delimiter       = '&';
		$customvariables = array();

		$namevaluecombos = explode( $delimiter, $custom );
		foreach ( $namevaluecombos as $keyval_unparsed ) {
			$equalsignposition = strpos( $keyval_unparsed, '=' );
			if ( $equalsignposition === false ) {
				$customvariables[ $keyval_unparsed ] = '';
				continue;
			}
			$key                     = substr( $keyval_unparsed, 0, $equalsignposition );
			$value                   = substr( $keyval_unparsed, $equalsignposition + 1 );
			$customvariables[ $key ] = $value;
		}

		return $customvariables;
	}

        static function get_transaction_row_by_subscr_id ($subscr_id) {
                global $wpdb;
                $query_db = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_payments_tbl WHERE subscr_id = %s", $subscr_id ), OBJECT );
                return $query_db;
        }

        static function get_original_custom_value_for_subscription_payment ( $subscr_id ) {
            if ( isset ( $subscr_id )){
                //Lets check if a proper custom field value is already saved in the CPT for this stripe subscription.
                $txn_cpt_qry_args = array(
                        'post_type'  => 'swpm_transactions',
                        'orderby'    => 'post_id',
                        'order'      => 'ASC',
                        'meta_query' => array(
                                array(
                                        'key' => 'subscr_id',
                                        'value' => $subscr_id
                                ),
                        )
                );
                $txn_cpt_qry = new WP_Query( $txn_cpt_qry_args );

                $found_posts = $txn_cpt_qry->found_posts;
                if ( $found_posts ) {
                    //Found a match so this is a subscription payment notification.
                    //Read the posts array.
                    $posts = $txn_cpt_qry->posts;

                    //The fist post entry will be the original stripe webhook notification.
                    $first_entry = array_shift($posts);
                    //Get the ID of the post.
                    $cpt_post_id = $first_entry->ID;
                    //Retrieve the original custom value saved for this post.
                    $orig_custom_value = get_post_meta( $cpt_post_id, 'custom', true );
                    return $orig_custom_value;
                }
            }
            return '';
        }

}
