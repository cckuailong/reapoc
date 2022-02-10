<?php

//display admin functions menu
function wpam_display_admin_functions_menu()
{
    ?>
    <div class="wrap">
    <h2><?php _e('Admin Functions', 'affiliates-manager');?></h2>
    <p><?php _e('The Admin Functions page lets you do various admin stuff from time to time.', 'affiliates-manager');?></p>
    <div id="poststuff"><div id="post-body">
    <?php
    if (isset($_POST['wpam_delete_clicks_data'])) {
        $nonce = $_POST['_wpnonce'];
        if(!wp_verify_nonce($nonce, 'wpam_delete_clicks_data_nonce')){
            wp_die(__('Error! Nonce Security Check Failed! Go back to the Admin Functions menu to delete clicks data.', 'affiliates-manager'));
        }
        $date_valid = true;
        $args = array();
        $args['start_date'] = '';
        if(isset($_POST['clicks_start_date']) && !empty($_POST['clicks_start_date'])){
            $start_date = sanitize_text_field($_POST['clicks_start_date']);
            if(date("Y-m-d", strtotime($start_date)) === $start_date){ //valid date
                $args['start_date'] = date("Y-m-d H:i:s", strtotime($start_date));
            }
            else{
                $date_valid = false;
                echo '<div id="message" class="error fade"><p>'.__('Start Date is not valid', 'affiliates-manager').'</p></div>';
            }
        }
        $args['end_date'] = '';
        if(isset($_POST['clicks_end_date']) && !empty($_POST['clicks_end_date'])){
            $end_date = sanitize_text_field($_POST['clicks_end_date']);
            if(date("Y-m-d", strtotime($end_date)) === $end_date){ //valid date
                $args['end_date'] = date("Y-m-d H:i:s", strtotime($end_date));
            }
            else{
                $date_valid = false;
                echo '<div id="message" class="error fade"><p>'.__('End Date is not valid', 'affiliates-manager').'</p></div>';
            }
        }
        if($date_valid){
            WPAM_Click_Tracking::delete_clicks_data_by_date($args);
            echo '<div id="message" class="updated fade"><p>'.__('Clicks data has been deleted!', 'affiliates-manager').'</p></div>';
        }
        //print_r($args);
    }
    ?>
    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Reset Buttons', 'affiliates-manager');?></label></h3>
    <div class="inside">
        
    <form method="post" action="" onSubmit="return confirm('<?php _e('Do you really want to delete the clicks data? This action cannot be undone.', 'affiliates-manager');?>');">
    <?php wp_nonce_field('wpam_delete_clicks_data_nonce'); ?>    
    <?php _e('Start Date:', 'affiliates-manager');?> <input class="wpam_date" name="clicks_start_date" type="text" id="clicks_start_date" value="" size="12" />
    <?php _e('End Date:', 'affiliates-manager');?> <input class="wpam_date" name="clicks_end_date" type="text" id="clicks_end_date" value="" size="12" />
    <p><?php _e('Select a Start Date and an End Date to delete all clicks data within this period. If you only select a Start Date all clicks data recorded on or after this date will be deleted. If you only select an End Date all clicks data recorded up to this date will be deleted. If no date is selected all clicks data will be deleted.', 'affiliates-manager');?></p>
    <div class="submit">
    <input type="submit" class="button" name="wpam_delete_clicks_data" value="<?php _e('Delete Clicks Data', 'affiliates-manager'); ?>" />
    </div>
    </form>
    </div></div> 
            
    </div></div>         
    </div>
    <script>
    jQuery(function($) {
        $( ".wpam_date" ).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
    </script>
    <?php
}
