<?php

class PMPro_Membership_Level{

    function __construct( $id = NULL ) {
        if ( $id ) {
            return $this->get_membership_level( $id );
        } else {
            return $this->get_empty_membership_level();
        }
    }

    function __get( $key ) {
        if ( isset( $this->$key ) ) {
            $value = $this->$key;
        } else {
            $value = get_pmpro_membership_level_meta( $this->ID, $key, true );
        }
        
        return $value;
    }

    function get_empty_membership_level() {
        
        $this->ID = ''; // for backwards compatibility.
        $this->id = '';
        $this->name ='';
        $this->description = '';
        $this->confirmation = '';
        $this->initial_payment = '';
        $this->billing_amount = '';
        $this->cycle_number = '';
        $this->cycle_period = '';
        $this->billing_limit = '';
        $this->trial_amount = '';
        $this->trial_limit = '';
        $this->allow_signups = '';
        $this->expiration_number = '';
        $this->expiration_period = '';
        $this->categories = array(); // example array(1,2,4,6);

        return $this;

    }

    /**
     * Function to get the membership level object by ID.
     * @since 2.3
     */
    function get_membership_level( $id ) {

        $dblobj = $this->get_membership_level_object( $id );
        $categories = $this->get_membership_level_categories( $id );

        if ( ! empty( $dblobj ) ) {
            $this->ID = $dblobj->id;
            $this->id = $dblobj->id;
            $this->name = $dblobj->name;
            $this->description = $dblobj->description;
            $this->confirmation = $dblobj->confirmation;
            $this->initial_payment = $dblobj->initial_payment;
            $this->billing_amount = $dblobj->billing_amount;
            $this->cycle_number = $dblobj->cycle_number;
            $this->cycle_period = $dblobj->cycle_period;
            $this->billing_limit = $dblobj->billing_limit;
            $this->trial_amount = $dblobj->trial_amount;
            $this->trial_limit = $dblobj->trial_limit;
            $this->allow_signups = $dblobj->allow_signups;
            $this->expiration_number = $dblobj->expiration_number;
            $this->expiration_period = $dblobj->expiration_period;
            $this->categories = $categories;
        } else {
            return false;
        }

        return $this;
    }

    /**
     * Get a list of category ID's that belong to a membership level.
     * @since 2.3
     * @return array An array of category ID's.
     */
    function get_membership_level_categories( $id ) {
        global $wpdb;

        $dblc = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_memberships_categories WHERE membership_id = " . $id . "", ARRAY_A );

        $category_array = array();

        foreach( $dblc as $category ) {
            $category_array[] = intval( $category['category_id'] );
        }
        
        return $category_array;
    }

    /**
     * Get the object of a membership level from the database.
     * @since 2.3
     * @return object The level object.
     */
    function get_membership_level_object( $id ) {
        global $wpdb;

        // Get the discount code object.
        $dcobj = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * 
                FROM $wpdb->pmpro_membership_levels
                WHERE id = %s",
                $id
            ),
            OBJECT   
        );

        return $dcobj;
    }

    /**
     * Save or update an existing level if the level exists.
     * @since 2.3
     */
    function save() {
        global $wpdb;

        if ( empty( $this->id ) ) {
            $before_action = 'pmpro_add_membership_level';
            $after_action = 'pmpro_added_membership_level';
        } else {
            $before_action = 'pmpro_update_membership_level';
            $after_action = 'pmpro_updated_membership_level';
        }

        do_action( $before_action, $this );

        pmpro_insert_or_replace(
			$wpdb->pmpro_membership_levels,
			array(
				'id'=> $this->id,
				'name' => $this->name,
				'description' => $this->description,
				'confirmation' => $this->confirmation,
				'initial_payment' => $this->initial_payment,
				'billing_amount' => $this->billing_amount,
				'cycle_number' => $this->cycle_number,
				'cycle_period' => $this->cycle_period,
				'billing_limit' => $this->billing_limit,
				'trial_amount' => $this->trial_amount,
				'trial_limit' => $this->trial_limit,
				'expiration_number' => $this->expiration_number,
				'expiration_period' => $this->expiration_period,
				'allow_signups' => $this->allow_signups
			),
			array(
				'%d',		//id
				'%s',		//name
				'%s',		//description
				'%s',		//confirmation
				'%f',		//initial_payment
				'%f',		//billing_amount
				'%d',		//cycle_number
				'%s',		//cycle_period
				'%d',		//billing_limit
				'%f',		//trial_amount
				'%d',		//trial_limit
				'%d',		//expiration_number
				'%s',		//expiration_period
				'%d',		//allow_signups
			)
        );

        if ( $wpdb->insert_id ) {
           $this->id = $wpdb->insert_id;
        }

        // Drop all categories if there are categories set from $this.
        if ( isset( $this->categories ) && is_array( $this->categories ) ) {
            
            // Delete categories for membership ID so we can add them back again.
            $wpdb->delete( $wpdb->pmpro_memberships_categories, array('membership_id' => $this->id), array('%d') );

            foreach( $this->categories as $key => $category ) {
                if ( term_exists( get_cat_name( $category ), 'category' ) ) {
                    $wpdb->insert( $wpdb->pmpro_memberships_categories, array( 'membership_id' => $this->id, 'category_id' => $category ), array( '%d', '%d' ) );
                }
            }
        }
        
        do_action( $after_action, $this );
    }
    /**
     * Delete a membership level and categories.
     * @since 2.3
     */
    function delete() {

        if ( empty( $this->id ) ) {
            return false;
        }

        global $wpdb;
        $r1 = false; // Remove level.
        $r2 = false; // Remove categories from level.
        $r3 = true; // Remove users from level.

        if ( $wpdb->delete( $wpdb->pmpro_membership_levels, array('id' => $this->id), array('%d') ) ) {
            $r1 = true;
        }

        if ( $wpdb->delete( $wpdb->pmpro_memberships_categories, array('membership_id' => $this->id), array('%d') ) ) {
            $r2 = true;
        }

        // Try to remove users from the level too
        $user_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT user_id FROM $wpdb->pmpro_memberships_users
				WHERE membership_id = %d
				AND status = 'active'",
			 	$this->id
            ) );
            

			foreach($user_ids as $user_id) {
				//change there membership level to none. that will handle the cancel
				if(pmpro_changeMembershipLevel(0, $user_id)) {
					//okay
				} else {
					//couldn't delete the subscription
					//we should probably notify the admin
					$pmproemail = new PMProEmail();
					$pmproemail->data = array("body"=>"<p>" . sprintf(__("There was an error canceling the subscription for user with ID=%d. You will want to check your payment gateway to see if their subscription is still active.", 'paid-memberships-pro' ), $user_id) . "</p>");
					$last_order = $wpdb->get_row( $wpdb->prepare( "
						SELECT * FROM $wpdb->pmpro_membership_orders
						WHERE user_id = %d
						ORDER BY timestamp DESC LIMIT 1",
						$user_id
					) );
					if($last_order)
						$pmproemail->data["body"] .= "<p>" . __("Last Invoice", 'paid-memberships-pro' ) . ":<br />" . nl2br(var_export($last_order, true)) . "</p>";
                    $pmproemail->sendEmail(get_bloginfo("admin_email"));
                    
                    $r3 = false; // Set it to false if it couldn't delete the subscription.
				}
            }
        
            
        if ( $r1 == true && $r2 == true && $r3 == true ) {
            return true;
        } elseif ( $r1 == true && $r2 == false && $r3 == false ) {
            return 'Only the level was deleted. Users may still be assigned to this level';
        } elseif ( $r1 == false && $r2 == true && $r3 == false ) {
            return 'Only categories were deleted. Users may still be assigned to this level.';
        } elseif( $r1 == false && $r2 == false && $r3 == true ) {
            return 'Only users were removed from this level.';
        } else {
            return false;
        }

    }

} // end of class