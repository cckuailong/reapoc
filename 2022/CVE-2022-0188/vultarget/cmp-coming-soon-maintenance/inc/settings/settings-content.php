<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if ( isset($_POST['niteoCS_content_slider']) ) {
		update_option('niteoCS_content_slider', $this->sanitize_checkbox($_POST['niteoCS_content_slider']));
	} else {
		update_option('niteoCS_content_slider', '0');
	}
}


$langs = $this->cmp_get_language_list();

if ( is_array( $langs ) ) {
    foreach ( $langs as $lang ) {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            if( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
                die('Sorry, but this request is invalid');
            }

            if ( isset($_POST['niteoCS_body_' . $lang]) ) {
                update_option('niteoCS_body_' . $lang, $this->niteo_sanitize_html( $_POST['niteoCS_body_' . $lang]));
            }
        }
    }
} 

$niteoCS_body_title 		= stripslashes( get_option('niteoCS_body_title', 'SOMETHING IS HAPPENING!') );
$niteoCS_body 				= stripslashes( get_option('niteoCS_body', '') );

if ( function_exists('pll_default_language') || defined('ICL_SITEPRESS_VERSION') ) {
    $niteoCS_body = stripslashes( get_option('niteoCS_body_' . $this->cmp_get_default_language(), get_option('niteoCS_body', '')) );
}

?>

<div class="table-wrapper content">
    <h3><?php _e('Main Content', 'cmp-coming-soon-maintenance');?></h3>
    <table class="content">
        <tbody>
        <tr class="body-title">
            <th><?php _e('Heading', 'cmp-coming-soon-maintenance');?></th>
            <td>
                <fieldset>
                    <input type="text" name="niteoCS_body_title" id="niteoCS_body_title" value="<?php echo esc_attr( $niteoCS_body_title ); ?>" class="regular-text code" placeholder="<?php _e('Leave empty to disable', 'cmp-coming-soon-maintenance');?>">
                </fieldset>
            </td>
        </tr>

        <tr>
            <th><?php _e('Message', 'cmp-coming-soon-maintenance');?></th>
            <td>
                <?php 
                if ( is_array( $langs ) ) {
                    foreach ( $langs as $lang ) {  ?>
                        <textarea style="display:none"name="niteoCS_body_<?php echo esc_attr($lang);?>" id="niteoCS_body_<?php echo esc_attr($lang);?>"><?php echo stripslashes(get_option('niteoCS_body_' . $lang, $this->niteo_sanitize_html( $niteoCS_body )));?></textarea>
                        <?php 
                    }
                }
                wp_editor( $this->niteo_sanitize_html( $niteoCS_body ), 'niteoCS_body', $settings = array('textarea_name'=>'niteoCS_body', 'editor_height'=>'300') ); ?><br>

                <?php if ( $themeslug === 'atlas' ) : ?>
                <label><input type="checkbox" name="niteoCS_content_slider" value="1" <?php checked( '1', get_option( 'niteoCS_content_slider', '1' ) ); ?> class="regular-text code"><?php _e('Display Gallery as Slider', 'cmp-coming-soon-maintenance');?></label><br><br>
                <?php endif; ?>

                <span class="cmp-hint">* <?php _e('WordPress embeds, custom HTML and 3rd party shortcodes are fully supported.', 'cmp-coming-soon-maintenance');?></span>
                <span class="cmp-hint"><?php printf(__('If you are having trouble with 3rd-party shortcodes you can %1$s', 'cmp-coming-soon-maintenance'), sprintf('<a href="' . admin_url() . 'admin.php?page=cmp-advanced#cmp-misc">%s</a>', __('disable automatic paragraphs function.', 'cmp-coming-soon-maintenance')));?></span><br>

            </td>
        </tr>

        <?php echo $this->render_settings->submit(); ?>

        </tbody>
    </table>

</div>