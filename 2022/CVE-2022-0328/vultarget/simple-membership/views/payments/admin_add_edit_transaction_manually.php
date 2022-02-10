<?php

/*******************************************************************
 * Render and process the interface for add new transaction manually
 ******************************************************************/

function swpm_handle_add_new_txn_manually(){
    global $wpdb;

    echo '<div class="swpm-grey-box">';
    SwpmUtils::e( 'You can add a new transaction record manually using this interface. It can be useful if you manually accept cash payment for your memberships.' );
    echo '</div>';

    if( isset( $_REQUEST['swpm_add_new_txn_save_submit'])){
        //Check nonce first
        check_admin_referer( 'swpm_admin_add_new_txn_form_action', 'swpm_admin_add_new_txn_form_field' );

        $current_date = SwpmUtils::get_current_date_in_wp_zone();

        $txn_data = array();
        $txn_data['email'] = sanitize_text_field( $_POST['email_address'] );
        $txn_data['first_name'] = sanitize_text_field( $_POST['first_name'] );
        $txn_data['last_name'] = sanitize_text_field( $_POST['last_name'] );
        $txn_data['ip'] = '';
        $txn_data['member_id'] = isset ( $_POST['member_id'] ) ? intval( $_POST['member_id' ] ) : '';
        $txn_data['membership_level'] = isset ( $_POST['membership_level_id'] ) ? intval( $_POST['membership_level_id' ] ) : '';

        $txn_data['txn_date'] = isset ( $_POST['txn_date'] ) ? sanitize_text_field( $_POST['txn_date' ] ) : $current_date;
        $txn_data['txn_id'] = isset ( $_POST['txn_id'] ) ? sanitize_text_field( $_POST['txn_id' ] ) : '';
        $txn_data['subscr_id'] = isset ( $_POST['subscriber_id'] ) ? sanitize_text_field( $_POST['subscriber_id' ] ) : '';
        $txn_data['reference'] = '';
        $txn_data['payment_amount'] = isset ( $_POST['payment_amount'] ) ? sanitize_text_field( $_POST['payment_amount' ] ) : '';
        $txn_data['gateway'] = 'manual';
        $txn_data['status'] = isset ( $_POST['txn_status'] ) ? sanitize_text_field( $_POST['txn_status' ] ) : '';

        //Insert the manual txn to the payments table
        $txn_data = array_filter( $txn_data );//Remove any null values.
        $wpdb->insert( $wpdb->prefix . 'swpm_payments_tbl', $txn_data );

        $db_row_id = $wpdb->insert_id;

        //let's also store transactions data in swpm_transactions CPT
        $post                = array();
        $post['post_title']  = '';
        $post['post_status'] = 'publish';
        $post['content']     = '';
        $post['post_type']   = 'swpm_transactions';
        $post_id = wp_insert_post( $post );
        update_post_meta( $post_id, 'db_row_id', $db_row_id );

        SwpmLog::log_simple_debug("Manual transaction added successfully.", true);

        echo '<div class="swpm-orange-box">';
        SwpmUtils::e('Manual transaction added successfully. ');
        echo '<a href="admin.php?page=simple_wp_membership_payments">View all transactions</a>';
        echo '</div>';

    } else {
        //Show the form to add manual txn record
        swpm_show_add_new_txn_form();
    }

}

function swpm_show_add_new_txn_form(){
    ?>
    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php echo SwpmUtils::_('Add New Transaction'); ?></label></h3>
        <div class="inside">

            <form id="pp_button_config_form" method="post">
                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Email Address'); ?></th>
                        <td>
                            <input type="text" size="70" name="email_address" value="" required />
                            <p class="description">Email address of the customer.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('First Name'); ?></th>
                        <td>
                            <input type="text" size="50" name="first_name" value="" required />
                            <p class="description">First name of the customer.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Last Name'); ?></th>
                        <td>
                            <input type="text" size="50" name="last_name" value="" required />
                            <p class="description">Last name of the customer.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Member ID'); ?></th>
                        <td>
                            <input type="text" size="20" name="member_id" value="" />
                            <p class="description">The Member ID number of the member's profile that corresponds to this transaction.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo SwpmUtils::membership_level_dropdown(); ?>
                            </select>
                            <p class="description">Select the membership level this transaction is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Amount'); ?></th>
                        <td>
                            <input type="text" size="10" name="payment_amount" value="" required />
                            <p class="description">Enter the payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Date'); ?></th>
                        <td>
                            <input type="text" size="20" name="txn_date" value="" />
                            <p class="description">The date for this transaction. Use format YYYY-MM-DD.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Transaction ID'); ?></th>
                        <td>
                            <input type="text" size="50" name="txn_id" value="" />
                            <p class="description">The unique transaction ID of this transaction so you can identify it easily.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Subscriber ID'); ?></th>
                        <td>
                            <input type="text" size="50" name="subscriber_id" value="" />
                            <p class="description">The subscriber ID (if any) from the member's profile.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo SwpmUtils::_('Status/Note'); ?></th>
                        <td>
                            <input type="text" size="50" name="txn_status" value="" />
                            <p class="description">A status value for this transaction. This will go to the Status/Note column of the transaction record.</p>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <?php wp_nonce_field( 'swpm_admin_add_new_txn_form_action', 'swpm_admin_add_new_txn_form_field' ) ?>
                    <input type="submit" name="swpm_add_new_txn_save_submit" class="button-primary" value="<?php echo SwpmUtils::_('Save Transaction Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}