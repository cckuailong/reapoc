<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Social Login", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_connect_with"><?php esc_html_e("Connect with", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_connect_with"]); ?>" name="wc_connect_with" id="wc_connect_with" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_social_login_agreement_label"><?php esc_html_e("Social login agreement label", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_social_login_agreement_label"]); ?>" name="wc_social_login_agreement_label" id="wc_social_login_agreement_label" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_social_login_agreement_desc"><?php esc_html_e("Social login agreement  description", "wpdiscuz"); ?></label></th>
                <td colspan="3"><textarea id="wc_social_login_agreement_desc" name="wc_social_login_agreement_desc"><?php echo esc_html($this->phrases["wc_social_login_agreement_desc"]); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_agreement_button_disagree"><?php esc_html_e("Disagree", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" id="wc_agreement_button_disagree" name="wc_agreement_button_disagree" value="<?php echo esc_attr($this->phrases["wc_agreement_button_disagree"]); ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_agreement_button_agree"><?php esc_html_e("Agree", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" id="wc_agreement_button_agree" name="wc_agreement_button_agree" value="<?php echo esc_attr($this->phrases["wc_agreement_button_agree"]); ?>"></td>
            </tr>
        </tbody>
    </table>
</div>