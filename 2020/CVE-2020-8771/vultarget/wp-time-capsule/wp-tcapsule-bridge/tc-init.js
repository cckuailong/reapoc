function refreshRestore() {
    start_restore_wptc();
    check_if_no_response_wptc('start_restore_wptc');

    //calling the progress bar function
    show_backup_progress_dialog_wptc('', 'incremental');
}

function start_bridge_copy_wptc(data) {

    console.log('start_bridge_copy_wptc inside tc-init');

    //register the startTIme
    start_time_tc = Date.now();

    var this_data = {};
    if (typeof data != 'undefined') {
        this_data = data;
    }

    this_data['wptc_request'] = true;

    jQuery.ajax({
        traditional: true,
        type: 'post',
        url: 'wptc-copy.php',
        dataType: 'json',
        data: this_data,
        success: function(request) {
            if (request == 'wptcs_callagain_wptce') {
                start_bridge_copy_wptc();
            }
            if (request == 'wptcs_over_wptce') {
                //clear timeout for start_bridge_copy_wptc function and then do the stuffs to perform the after restore options
                clearTimeout(checkIfNoResponseTimeout);
                //show the completed dialog box
                var this_html = '<div class="this_modal_div" style="background-color: #f1f1f1;font-family: \'open_sansregular\' !important;color: #444;padding: 0px 34px 26px 34px; left:20%; z-index:1000"><div class="pu_title">DONE</div><div class="wcard clearfix">  <div class="l1">Your website was restored successfully. Yay! <br> Restoring in 5 secs...</div>  <a class="btn_pri" style="margin: 0 42px 20px; width: 250px; text-align: center;">GO TO ' + site_url_wptc + '</a></div></div>';
                jQuery("#TB_ajaxContent").html(this_html);

                //redirect to the site after 3 secs
                //setInterval(function(){window.location = site_url_wptc;},5000);
            } else if (typeof request != 'undefined' && typeof request != null && request['error']) {
                //clear timeout for start_bridge_copy_wptc function and then do the stuffs to perform the after restore options

            }
        }, // End success
        error: function(errData) {}
    });
}

function check_if_no_response_wptc(this_func) {
    //this function is called every 15 secs to see if there is any activity.
    //if there is no response for the last 60 secs .. then this function calls the respective ajax functions.

    //set global ajax_function variable
    if (typeof this_func != 'undefined' && this_func != null) {
        ajax_function_tc = this_func;
    }

    var this_time_tc = Date.now();
    if ((this_time_tc - start_time_tc) >= 60000) {
        if (ajax_function_tc == 'start_bridge_copy_wptc') {
            var continue_bridge = {};
            continue_bridge['wp_prefix'] = wp_prefix_wptc; //am sending the prefix ; since it is a bridge
            start_bridge_copy_wptc();
        } else {
            start_restore_wptc();
        }
    }
    checkIfNoResponseTimeout = setTimeout(function() { check_if_no_response_wptc(); }, 15000);
}

jQuery(document).ready(function($) {
    if (typeof reresh_restore_wptc != 'undefined' && reresh_restore_wptc) {
        refreshRestore();
    }
});
