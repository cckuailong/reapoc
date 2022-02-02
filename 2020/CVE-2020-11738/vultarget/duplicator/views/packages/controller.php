<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

DUP_Handler::init_error_handler();
DUP_Util::hasCapability('export');

global $wpdb;

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PLUGIN_PATH . '/views/inc.header.php');

$current_view =  (isset($_REQUEST['action']) && $_REQUEST['action'] == 'detail') ? 'detail' : 'main';

$get_package_file_nonce = wp_create_nonce('DUP_CTRL_Package_getPackageFile');
?>

<script>

</script>

<script>
    jQuery(document).ready(function($) {

        // which: 0=installer, 1=archive, 2=sql file, 3=log
        Duplicator.Pack.DownloadPackageFile = function (which, packageID)
		{
            var actionLocation = ajaxurl + '?action=DUP_CTRL_Package_getPackageFile&which=' + which + '&package_id=' + packageID + '&nonce=' + '<?php echo esc_js($get_package_file_nonce); ?>';
            if(which == 3) {
                var win = window.open(actionLocation, '_blank');
                win.focus();
            }
            else {
                location.href = actionLocation;
            }
        };

        Duplicator.Pack.DownloadFile = function(file, url)
        {
            var link = document.createElement('a');        
            link.target = "_blank";
            link.download = file;
            link.href= url;
            document.body.appendChild(link);
            
            // click event fire
            if (document.dispatchEvent) {
                // First create an event
                var click_ev = document.createEvent("MouseEvents");
                // initialize the event
                click_ev.initEvent("click", true /* bubble */, true /* cancelable */);
                // trigger the event
                link.dispatchEvent(click_ev);
            } else if (document.fireEvent) {
                link.fireEvent('onclick');
            } else if (link.click()) {
                link.click()
            }

            document.body.removeChild(link);
            return false;
        };


        /*	----------------------------------------
         * METHOD: Toggle links with sub-details */
        Duplicator.Pack.ToggleSystemDetails = function(event) {
            if ($(this).parents('div').children(event.data.selector).is(":hidden")) {
                $(this).children('span').addClass('ui-icon-triangle-1-s').removeClass('ui-icon-triangle-1-e');
                ;
                $(this).parents('div').children(event.data.selector).show(250);
            } else {
                $(this).children('span').addClass('ui-icon-triangle-1-e').removeClass('ui-icon-triangle-1-s');
                $(this).parents('div').children(event.data.selector).hide(250);
            }
        }
    });
</script>

<div class="wrap">
    <?php 
		    switch ($current_view) {
				case 'main': include('main/controller.php'); break;
				case 'detail' : include('details/controller.php'); break;
            break;	
    }
    ?>
</div>