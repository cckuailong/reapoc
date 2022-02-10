<p><?php esc_html_e( "You've subscribed to the following package.", 'wp-user-frontend' ); ?></p>
<div class="wpuf_sub_info">
    <h3><?php esc_html_e( 'Subscription Details', 'wp-user-frontend' ); ?></h3>
    <div class="wpuf-text">
        <div><strong><?php esc_html_e( 'Subcription Name: ', 'wp-user-frontend' ); ?></strong><?php echo esc_html( $pack->post_title ); ?></div>
        <div>
            <strong><?php esc_html_e( 'Package & billing details: ', 'wp-user-frontend' ); ?></strong>
            <?php echo esc_html( $billing_amount . ' ' . $recurring_des ); ?>
        </div>
        <?php  if ( is_wp_error( $user_sub ) ) { ?>
        <div>
            <strong><?php esc_html_e( 'Subscription Status: ', 'wp-user-frontend' ); ?></strong>
            <?php esc_html_e( 'Subscription Expired!', 'wp-user-frontend' ); ?>
        </div>
        <?php } else {
             if ( ! empty( $user_sub['total_feature_item'] ) ) { ?>
                <div><strong><?php esc_html_e( 'Number of featured item: ', 'wp-user-frontend' ); ?></strong><?php echo $user_sub['total_feature_item']; ?></div>
            <?php } ?>
            <div>
                <strong><?php esc_html_e( 'Remaining post: ', 'wp-user-frontend' ); ?></strong>
                <?php
                $i = 0;

                foreach ( $user_sub['posts'] as $key => $value ) {
                    $value = intval( $value );

                    if ( $value === 0 ) {
                        continue;
                    }

                    $post_type_obj = get_post_type_object( $key );

                    if ( ! $post_type_obj ) {
                        continue;
                    }
                    $value = ( $value == '-1' ) ? __( 'Unlimited', 'wp-user-frontend' ) : $value; ?>
                    <div><?php echo esc_html( $post_type_obj->labels->name ) . ': ' . esc_html( $value ); ?></div>
                    <?php
                    $i ++;
                }
                echo $i ? '' : esc_attr( $i );
                ?>
            </div>
            <?php
            if ( $user_sub['recurring'] != 'yes' ) {
                if ( !empty( $user_sub['expire'] ) ) {
                    $expiry_date =  ( $user_sub['expire'] == 'unlimited' ) ? ucfirst( 'unlimited' ) : wpuf_get_date( wpuf_date2mysql( $user_sub['expire'] ) ); ?>
                    <div class="wpuf-expire">
                        <strong><?php echo esc_html__( 'Expire date:', 'wp-user-frontend' ); ?></strong> <?php echo esc_html( $expiry_date ); ?>
                    </div>
                    <?php
                }
            }

            if ( $user_sub['recurring'] == 'yes' ) {
                global $wpdb;

                $user_id         = get_current_user_id();
                $payment_gateway = $wpdb->get_var( "SELECT payment_type FROM {$wpdb->prefix}wpuf_transaction WHERE user_id = {$user_id} AND status = 'completed' ORDER BY created DESC" );

                $payment_gateway = strtolower( $payment_gateway ); ?>
                <br>
                <p><i><?php esc_html_e( 'To cancel the pack, press the following cancel button.', 'wp-user-frontend' ); ?></i></p>
                <form action="" method="post" style="text-align: center;">
                    <?php wp_nonce_field( 'wpuf-sub-cancel' ); ?>
                    <input type="hidden" name="gateway" value="<?php echo esc_attr( $payment_gateway ); ?>">
                    <input type="submit" name="wpuf_cancel_subscription" class="btn btn-sm btn-danger" value="<?php esc_html_e( 'Cancel', 'wp-user-frontend' ); ?>">
                </form>
                <?php
            }
        }
        ?>
    </div>
</div>
