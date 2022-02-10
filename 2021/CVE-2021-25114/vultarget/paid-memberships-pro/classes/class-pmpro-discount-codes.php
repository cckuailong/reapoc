<?php

class PMPro_Discount_Code{

    function __construct( $code = NULL ) {

        if ( $code ) {
            
            if ( is_numeric( $code ) ) {
                return $this->get_discount_code_by_id( $code );
            } else {
                return $this->get_discount_code_by_code( $code );
            }

        } else {
            return $this->get_empty_discount_code();
        }
    }

    /**
     * Get an empty (but complete) discount code object.
     * @since 2.3
     */
    function get_empty_discount_code() {

        $discount_code = new stdClass();
        $discount_code->id = '';
        $discount_code->code = pmpro_getDiscountCode();
        $discount_code->starts = date( 'Y-m-d' );
        $discount_code->expires = date( 'Y-m-d', time() + 86400 );
        $discount_code->uses = '';
        $discount_code->levels = array(
        // 1 => array(
            // 'initial_payment' => '',
            // 'billing_amount' => '',
            // 'cycle_number' => '',
            // 'cycle_period' => 'Month',
            // 'billing_limit' => '',
            // 'custom_trial' => 0,
            // 'trial_amount' => '',
            // 'trial_limit' => '',
            // 'expiration_number' => '',
            // 'expiration_period' => ''
        // )
        );
        return $discount_code;
    }

    /**
     * Get discount code object.
     * @since 2.3
     * @return $dcobj object The discount code object.
     */
    function get_discount_code_object( $code ) {
        global $wpdb;

        // Get the discount code object.
        $dcobj = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * 
                FROM $wpdb->pmpro_discount_codes 
                WHERE code = %s",
                $code
            ),
            OBJECT   
        );

        return $dcobj;
    }

    /**
     *  Get levels and level billing settings by discount code.
     * @since 2.3
     * @return $levels obj levels that are tied to a discount code.
     */
    function get_discount_code_billing( $code ) {
        global $wpdb;

        $levels = $wpdb->get_results( 
            $wpdb->prepare(
                "SELECT cl.* 
                FROM $wpdb->pmpro_discount_codes_levels cl 
                LEFT JOIN $wpdb->pmpro_discount_codes cd 
                ON cl.code_id = cd.id 
                WHERE cd.code = %s",
                $code
                ),
                OBJECT
                );

        return $levels;
    }

    /**
     * Get discount code by code
     * @since 2.3
     */
    function get_discount_code_by_code( $code ) {

        // Get discount code object and levels linked to the code object..
        $dcobj = $this->get_discount_code_object( $code );
        $levels = $this->get_discount_code_billing( $code );        

        if ( ! empty( $dcobj ) ) {
            // Setup the discount code object.
            $this->id = $dcobj->id;
            $this->starts = $dcobj->starts;
            $this->expires = $dcobj->expires;
            $this->uses = $dcobj->uses;
            
            foreach( $levels as $level ) {
                $this->levels[$level->level_id] = array(
                    'initial_payment' => $level->initial_payment,
                    'billing_amount' => $level->billing_amount,
                    'cycle_number' => $level->cycle_number,
                    'cycle_period' => $level->cycle_period,
                    'billing_limit' => $level->billing_limit,
                    'custom_trial' => ! isset( $level->custom_trial ) ? 0 : $level->custom_trial,
                    'trial_amount' => $level->trial_amount,
                    'trial_limit' => $level->trial_limit,
                    'expiration_number' => $level->expiration_number,
                    'expiration_period' => $level->expiration_period
                );
            }
            
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Get discount code by ID
     * @since 2.3
     */
    function get_discount_code_by_id( $id ) {
        global $wpdb;
        // Get the discount code by code, then call function
        $id = intval( $id );

        $code = $wpdb->get_var("SELECT code FROM $wpdb->pmpro_discount_codes WHERE `id` =" . $id );

        return $this->get_discount_code_by_code( $code );

    }

    function save() {
        global $wpdb;
        
        $sql_okay = false;
        // See if code exists;
        if ( isset( $this->code ) && ! empty( $this->code ) ) {
            // see if row exists.
            $results = $wpdb->get_row( "SELECT * FROM $wpdb->pmpro_discount_codes WHERE code = '" . $this->code . "' LIMIT 1" );

            if ( $results ) {

                if ( ! isset( $this->id ) ) {
                    $this->id = $results->id;
                }

                if ( ! isset( $this->starts ) ) {
                    $this->starts = $results->starts;
                }
        
                if ( ! isset( $this->expires ) ) {
                    $this->expires = $results->expires;
                }
        
                if ( ! isset( $this->uses ) ) {
                    $this->uses = $results->uses;
                }                
                
            }

        } else {

            $this->code = pmpro_getDiscountCode();

            if ( ! isset( $this->starts ) || empty( $this->starts ) ) {
                $this->starts = date( 'Y-m-d' );
            }

            if ( ! isset( $this->expires ) || empty( $this->expires ) ) {
                $this->expires = date( 'Y-m-d', time() + 86400 );
            }

            if ( ! isset( $this->uses ) || empty( $this->uses) ) {
                $this->uses = 0;
            }
        }      

        // If the code doesn't exist, create it otherwise update it.
        if ( empty( $this->id ) ) {

            $before_action = 'pmpro_add_discount_code';
            $after_action = 'pmpro_added_discount_code';

            $this->sqlQuery = "INSERT INTO $wpdb->pmpro_discount_codes ( `code`, `starts`, `expires`, `uses` ) 
                               VALUES ('" . $this->code . "',
                                       '" . $this->starts ."',
                                       '" . $this->expires ."',
                                       " . intval( $this->uses ) ."
                               )";                      
        } else {
            
            $before_action = 'pmpro_update_discount_code';
            $after_action = 'pmpro_updated_discount_code';

            $this->sqlQuery = "UPDATE $wpdb->pmpro_discount_codes
                                SET  `code` = '" . $this->code ."',
                                    `starts` = '" . $this->starts . "',
                                    `expires` = '" . $this->expires . "',
                                    `uses` = " . intval( $this->uses ) . "
                                WHERE code = '" . $this->code . "'
                                LIMIT 1";
        }

        // Make sure query ran okay.
        do_action( $before_action, $this );
        if ( $wpdb->query( $this->sqlQuery ) !== false ) {
            if ( empty ( $this->id ) ) {
                $this->id = $wpdb->insert_id;
            } 
        }

        // Delete levels if 0 or false is passed through.
        if ( isset( $this->levels ) && ( $this->levels == 0 || $this->levels == false ) ) {
            $wpdb->delete( $wpdb->pmpro_discount_codes_levels, array( 'code_id' => $this->id ), array( '%d' ) );
        }

        // Insert discount code level/billing data if it's set in the discount code object.
        if ( isset( $this->levels ) && is_array( $this->levels ) ) {

           // Nuke the levels table and rebuild it.
           $wpdb->delete( $wpdb->pmpro_discount_codes_levels, array( 'code_id' => $this->id ), array( '%d' ) );

            foreach ( $this->levels as $key => $data ) {
                $level_id = intval( $key );
                $initial_payment = $data['initial_payment'];
                $billing_amount = $data['billing_amount'];
                $cycle_number = $data['cycle_number'];
                $cycle_period = $data['cycle_period'];
                $billing_limit = $data['billing_limit'];
                $trial_amount = $data['trial_amount'];
                $trial_limit = $data['trial_limit'];
                $expiration_number = $data['expiration_number'];
                $expiration_period = $data['expiration_period'];

                $this->sqlQuery = "INSERT INTO $wpdb->pmpro_discount_codes_levels 
                ( `code_id`, `level_id`, `initial_payment`, `billing_amount`, `cycle_number`, `cycle_period`, `billing_limit`, `trial_amount`, `trial_limit`, `expiration_number`, `expiration_period`) 
                VALUES ( " . intval( $this->id ) . ",
                        " . intval( $level_id ) . ",
                        " . floatval( $initial_payment ) . ",
                        " . floatval( $billing_amount ) . ",
                        " . intval( $cycle_number ) . ",
                        '" . $cycle_period . "',
                        " . intval( $billing_limit ) . ",
                        " . floatval( $trial_amount ) . ",
                        " . intval( $trial_limit ) . ",
                        " . intval( $expiration_number ) . ",
                        '" . $expiration_period . "'
                )";                
                
                // Run the query here.
                if ( $wpdb->query( $this->sqlQuery ) !== false ) {
                    $sql_okay = true;
                }
            }
        }  
        
        do_action( $after_action, $this ); 
        
        unset( $this->sqlQuery ); //remove SQL query.

        // Return values once updated/inserted.
        if ( $sql_okay == true ) {
            return $this->get_discount_code_by_id( $this->id );
        } else {
            return false;
        }

    }

} // end of class.