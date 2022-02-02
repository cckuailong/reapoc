<?php
if (!defined("ABSPATH")) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php esc_html_e("Date/Time Phrases", "wpdiscuz"); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="wc_year_text"><?php esc_html_e("Year", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_year_text"]); ?>" name="wc_year_text" id="wc_year_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_year_text_plural"><?php esc_html_e("Years (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_year_text_plural"]); ?>" name="wc_year_text_plural" id="wc_year_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_month_text"><?php esc_html_e("Month", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_month_text"]); ?>" name="wc_month_text" id="wc_month_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_month_text_plural"><?php esc_html_e("Months (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_month_text_plural"]); ?>" name="wc_month_text_plural" id="wc_month_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_day_text"><?php esc_html_e("Day", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_day_text"]); ?>" name="wc_day_text" id="wc_day_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_day_text_plural"><?php esc_html_e("Days (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_day_text_plural"]); ?>" name="wc_day_text_plural" id="wc_day_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_hour_text"><?php esc_html_e("Hour", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_hour_text"]); ?>" name="wc_hour_text" id="wc_hour_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_hour_text_plural"><?php esc_html_e("Hours (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_hour_text_plural"]); ?>" name="wc_hour_text_plural" id="wc_hour_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_minute_text"><?php esc_html_e("Minute", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_minute_text"]); ?>" name="wc_minute_text" id="wc_minute_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_minute_text_plural"><?php esc_html_e("Minutes (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_minute_text_plural"]); ?>" name="wc_minute_text_plural" id="wc_minute_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_second_text"><?php esc_html_e("Second", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_second_text"]); ?>" name="wc_second_text" id="wc_second_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_second_text_plural"><?php esc_html_e("Seconds (Plural Form)", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_second_text_plural"]); ?>" name="wc_second_text_plural" id="wc_second_text_plural" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_right_now_text"><?php esc_html_e('Commented "right now" text', "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_right_now_text"]); ?>" name="wc_right_now_text" id="wc_right_now_text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_ago_text"><?php esc_html_e("Ago text", "wpdiscuz"); ?></label></th>
                <td colspan="3"><input type="text" value="<?php echo esc_attr($this->phrases["wc_ago_text"]); ?>" name="wc_ago_text" id="wc_ago_text" /></td>
            </tr>            
        </tbody>
    </table>
</div>