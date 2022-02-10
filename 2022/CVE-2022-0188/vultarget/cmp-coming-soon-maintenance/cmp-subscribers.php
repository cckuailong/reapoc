<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// load WP_List_Table extension
if( ! class_exists( 'cmp_subs_list_table' ) ) {
    require( dirname(__FILE__).'/inc/class-cmp-subscribers.php' );
}

// create subscriber table
$cmp_subs_list_table = new cmp_subs_list_table();
$cmp_subs_list_table->prepare_items();
?>

<div class="wrap cmp-coming-soon-maintenance">
	<h1></h1>
	<div id="icon-users" class="icon32"></div>
	<div class="settings-wrap">
		<div class="cmp-inputs-wrapper subscribers-settings">
			<?php wp_nonce_field('cmp_process_bulk_action', '_nonce'); ?>
			 <h2><?php _e('View / Edit CMP Subscribers', 'cmp-coming-soon-maintenance');?></h2>
			 <p><?php _e('If you use MailChimp integration, you must check your Subscribers directly in MailChimp!', 'cmp-coming-soon-maintenance');?></p>
			 <form name="cmp_subscribe_form" method="post" action="admin.php?page=cmp-subscribers">
				<?php $cmp_subs_list_table->display(); ?>
			</form>
			<button id="export_csv" class="button"><?php _e('Export All Subscribers','cmp-coming-soon-maintenance');?></button>
		</div>
		<?php 
		// get sidebar with "widgets"
		if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
			require (dirname(__FILE__) . '/cmp-sidebar.php');
		} ?>
	</div>
</div>

<?php $nonce = wp_create_nonce( 'cmp-coming-soon-ajax-secret' );?>

<script type="text/javascript">
jQuery(document).ready(function($){
    jQuery('#export_csv').click(function(){
        var action = 'action=niteo_export_csv&security=<?php echo $nonce;?>';
        jQuery.post(ajaxurl, action, function(response) {
            if(response) {
                jQuery('<iframe />').attr('src', ajaxurl + '?action=niteo_export_csv&security=<?php echo $nonce;?>').appendTo('body').hide();
            } 
        });
    });
});
</script>