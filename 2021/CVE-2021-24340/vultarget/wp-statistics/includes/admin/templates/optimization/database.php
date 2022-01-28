<div class="wrap wps-wrap">
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" colspan="2"><h3><?php _e('Database Setup', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="index-submit"><?php _e('Re-run Install:', 'wp-statistics'); ?></label>
            </th>
            <td>
                <input id="install-submit" class="button button-primary" type="button"
                       value="<?php _e('Install Now!', 'wp-statistics'); ?>" name="install-submit"
                       onclick="location.href=document.URL+'&install=1&tab=database'">
                <p class="description"><?php _e('If for some reason your installation of WP Statistics is missing the database tables or other core items, this will re-execute the install process.', 'wp-statistics'); ?></p>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row" colspan="2">
                <h3><?php _e('Repair and Optimization Database Tables', 'wp-statistics'); ?></h3></th>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="index-submit"><?php _e('Optimize Table:', 'wp-statistics'); ?></label>
            </th>
            <td>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        jQuery("#wp-statistics-run-optimize-database-table").click(function () {
                            var tbl = jQuery('#optimize-table').val();
                            if (tbl == "0") {
                                alert('<?php _e("Please select database table", "wp-statistics"); ?>');
                                return;
                            }
                            window.location.href = document.URL + '&optimize-table=' + tbl + '&tab=database';
                        });
                    });
                </script>

                <select dir="<?php echo(is_rtl() ? 'rtl' : 'ltr'); ?>" id="optimize-table" name="optimize-table">
                    <option value="0"><?php _e('Please select', 'wp-statistics'); ?></option>
                    <?php
                    foreach (WP_STATISTICS\DB::table('all') as $tbl_key => $tbl_name) {
                        echo '<option value="' . $tbl_key . '">' . $tbl_name . '</option>';
                    }
                    ?>
                    <option value="all"><?php echo __('All', 'wp-statistics'); ?></option>
                </select>
                <p class="description"><?php _e('Please select the table you would like to optimize and repair', 'wp-statistics'); ?></p>

                <input id="wp-statistics-run-optimize-database-table" class="button button-primary" type="button"
                       value="<?php _e('Run Now!', 'wp-statistics'); ?>"
                       name="wp-statistics-run-optimize-database-table" style="margin-top:5px;">
            </td>
        </tr>

        </tbody>
    </table>
</div>