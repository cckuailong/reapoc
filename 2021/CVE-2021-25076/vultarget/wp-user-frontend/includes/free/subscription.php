<?php

class WPUF_Subscription_Element extends WPUF_Pro_Prompt {

    public static function add_subscription_element( $sub_meta, $hidden_recurring_class, $hidden_trial_class, $obj ) {
        ?>
        <tr valign="top">
            <th><label><?php esc_html_e( 'Recurring', 'wp-user-frontend' ); ?></label></th>
            <td>
                <label for="wpuf-recuring-pay">
                    <input type="checkbox" disabled checked size="20" style="" id="wpuf-recuring-pay" value="yes" name="" />
                    <?php esc_html_e( 'Enable Recurring Payment', 'wp-user-frontend' ); ?>
                </label>

                <label class="wpuf-pro-text-alert"> (<?php echo wp_kses_post( self::get_pro_prompt_text() ); ?>)</label>
            </td>
        </tr>
    <?php
    }
}
