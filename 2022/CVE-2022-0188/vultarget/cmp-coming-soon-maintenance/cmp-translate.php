<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// update translation
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	if( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
		die('Sorry, but this request is invalid');
    }
    $translation = json_decode( get_option('niteoCS_translation'), true );

    $translation[0]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_0'] );
    $translation[1]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_1'] );
    $translation[2]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_2'] );
    $translation[3]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_3'] );
    $translation[4]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_4'] );
    $translation[5]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_5'] );
    $translation[6]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_6'] );
    $translation[7]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_7'] );
    $translation[8]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_8'] );
    $translation[9]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_9'] );
    $translation[10]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_10'] );
    $translation[11]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_11'] );
    $translation[12]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_12'] );
    $translation[13]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_13'] );
    $translation[14]['translation'] = sanitize_text_field( $_POST['niteoCS_translate_14'] );

    update_option('niteoCS_translation', wp_json_encode( $translation ));
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// load WP_List_Table extension
if( ! class_exists( 'cmp_translate_table' ) ) {
    require( dirname(__FILE__).'/inc/class-cmp-translate.php' );
}

// create subscriber table
$cmp_translate_table = new cmp_translate_table();
$cmp_translate_table->prepare_items();
?>
	
<div class="wrap cmp-coming-soon-maintenance">
	<h1></h1>
	<div id="icon-users" class="icon32"></div>
    <div class="settings-wrap">
    	<div class="cmp-inputs-wrapper translate-settings">
    		 <h2><?php _e('CMP Translation', 'cmp-coming-soon-maintenance');?></h2>
             <p><?php _e('You can edit any text on CMP landing page - button labels, countdown, subscription messages, etc.', 'cmp-coming-soon-maintenance');?></p>
    		 <form name="cmp_translate_form" method="post" action="admin.php?page=cmp-translate&status=settings-saved">
    			<?php $cmp_translate_table->display(); ?>
    		<p class="cmp-submit">
    			<?php wp_nonce_field('save_options','save_options_field'); ?>
    			<input type="submit" name="Submit" class="button cmp-button submit" value="<?php _e('Save All Changes', 'cmp-coming-soon-maintenance'); ?>" id="submitChanges" />
    		</p>
    		</form>

    	</div>
    	<?php 
    	// get sidebar with "widgets"
    	if ( file_exists(dirname(__FILE__) . '/cmp-sidebar.php') ) {
    		require (dirname(__FILE__) . '/cmp-sidebar.php');
    	} ?>
    </div>
</div>
