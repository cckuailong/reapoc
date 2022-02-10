<?php
global $post;

$form_settings = wpuf_get_form_settings( $post->ID );

$payment_options       = isset( $form_settings['payment_options'] ) ? $form_settings['payment_options'] : 'false';
$enable_pay_per_post   = isset( $form_settings['enable_pay_per_post'] ) ? $form_settings['enable_pay_per_post'] : 'false';
$force_pack_purchase   = isset( $form_settings['force_pack_purchase'] ) ? $form_settings['force_pack_purchase'] : 'false';

$pay_per_post_cost     = isset( $form_settings['pay_per_post_cost'] ) ? $form_settings['pay_per_post_cost'] : 2;
$fallback_ppp_enable   = isset( $form_settings['fallback_ppp_enable'] ) ? $form_settings['fallback_ppp_enable'] : 'false';
$fallback_ppp_cost     = isset( $form_settings['fallback_ppp_cost'] ) ? $form_settings['fallback_ppp_cost'] : 1;
$ppp_success_page      = isset( $form_settings['ppp_payment_success_page'] ) ? $form_settings['ppp_payment_success_page'] : '';

?>
    <table class="form-table">

        <!-- Added Payment Settings -->

        <tr>
            <th><?php esc_html_e( 'Payment Options', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="hidden" name="wpuf_settings[payment_options]" value="false">
                    <input type="checkbox" name="wpuf_settings[payment_options]" value="true"<?php checked( $payment_options, 'true' ); ?> />
                    <?php esc_html_e( 'Enable Payments', 'wp-user-frontend' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Check to enable Payments for this form.', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>

        <tr class="show-if-payment">
            <th>&mdash; <?php esc_html_e( 'Force Pack', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="hidden" name="wpuf_settings[force_pack_purchase]" value="false">
                    <input type="checkbox" name="wpuf_settings[force_pack_purchase]" value="true"<?php checked( $force_pack_purchase, 'true' ); ?> />
                    <?php esc_html_e( 'Force subscription pack', 'wp-user-frontend' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Force users to purchase and use subscription pack.', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>

        <tr class="show-if-payment show-if-force-pack">
            <th>&mdash; &mdash; <?php esc_html_e( 'Fallback to pay per post', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="hidden" name="wpuf_settings[fallback_ppp_enable]" value="false">
                    <input type="checkbox" name="wpuf_settings[fallback_ppp_enable]" value="true"<?php checked( $fallback_ppp_enable, 'true' ); ?> />
                    <?php esc_html_e( 'Fallback pay per post charging', 'wp-user-frontend' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Fallback to pay per post charging if pack limit exceeds', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>

        <tr class="show-if-payment show-if-force-pack">
            <th>&mdash; &mdash; <?php esc_html_e( 'Fallback cost', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="number" name="wpuf_settings[fallback_ppp_cost]" value="<?php echo esc_attr( $fallback_ppp_cost ); ?>" />
                </label>
                <p class="description"><?php esc_html_e( 'Cost of pay per post after a subscription pack limit is reached.', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>

        <tr class="show-if-payment">
            <th>&mdash; <?php esc_html_e( 'Pay Per Post', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="hidden" name="wpuf_settings[enable_pay_per_post]" value="false">
                    <input type="checkbox" name="wpuf_settings[enable_pay_per_post]" value="true"<?php checked( $enable_pay_per_post, 'true' ); ?> />
                    <?php esc_html_e( 'Enable Pay Per Post', 'wp-user-frontend' ); ?>
                </label>
                <p class="description"><?php esc_html_e( 'Charge users for posting,', 'wp-user-frontend' ); ?><a target="_blank" href="https://wedevs.com/docs/wp-user-frontend-pro/subscription-payment/how-to-charge-for-each-post-submission/"><?php esc_html_e( ' Learn More about Pay Per Post.', 'wp-user-frontend' ); ?></a></p>
            </td>
        </tr>

        <tr class="show-if-payment show-if-pay-per-post">
            <th>&mdash; &mdash; <?php esc_html_e( 'Cost Settings', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input type="number" name="wpuf_settings[pay_per_post_cost]" value="<?php echo esc_attr( $pay_per_post_cost ); ?>" />
                </label>
                <p class="description"><?php esc_html_e( 'Amount to be charged per post', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>
        <tr class="show-if-payment show-if-pay-per-post">
            <th>&mdash; &mdash; <?php esc_html_e( 'Payment Success Page', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <select name="wpuf_settings[ppp_payment_success_page]" >
                        <?php
                        foreach ( wpuf_get_pages() as $page_id => $page_name ) {
                            ?>
                            <option value="<?php echo $page_id; ?>" <?php echo $page_id === (int) $ppp_success_page ? 'selected' : ''; ?> ><?php echo $page_name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
                <p class="description"><?php esc_html_e( 'Page will show after successful payment', 'wp-user-frontend' ); ?></p>
            </td>
        </tr>
        <?php do_action( 'wpuf_form_setting_payment', $form_settings, $post ); ?>
    </table>
