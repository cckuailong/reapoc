<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	if( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
		die('Sorry, but this request is invalid');
    }
   
    if ( isset( $_POST['niteoCS_lang_switcher'] ) ) {
        update_option( 'niteoCS_lang_switcher', sanitize_text_field($_POST['niteoCS_lang_switcher']) );
    }

    if ( isset( $_POST['niteoCS_lang_switcher_flag'] ) ) {
        update_option( 'niteoCS_lang_switcher[flag]', $this->sanitize_checkbox($_POST['niteoCS_lang_switcher_flag']) );
        
    } else {
        update_option( 'niteoCS_lang_switcher[flag]', '0');
    }

    if ( isset( $_POST['niteoCS_lang_switcher_text'] ) ) {
        update_option( 'niteoCS_lang_switcher[text]', $this->sanitize_checkbox($_POST['niteoCS_lang_switcher_text']) );
        
    } else {
        update_option( 'niteoCS_lang_switcher[text]', '0');
    }
}

$lang_switcher  	    = get_option('niteoCS_lang_switcher', '1');
$lang_switcher_flag 	= get_option('niteoCS_lang_switcher[flag]', '1');
$lang_switcher_text 	= get_option('niteoCS_lang_switcher[text]', '1');

?>

<div class="table-wrapper content">
	<h3><?php _e('Language Switcher', 'cmp-coming-soon-maintenance');?></h3>
	<table class="content">
	<tbody>
		<tr>
			<th>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php _e('Language Switcher', 'cmp-coming-soon-maintenance');?></span>
					</legend>

					<p>
						<label title="Enabled">
							<input type="radio" class="lang-switcher" name="niteoCS_lang_switcher" value="1" <?php checked( '1', $lang_switcher );?>>&nbsp;<?php _e('Enabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

					<p>
						<label title="Disabled">
							<input type="radio" class="lang-switcher" name="niteoCS_lang_switcher" value="0" <?php checked( '0', $lang_switcher );?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
						</label>
					</p>

				</fieldset>
			</th>

			<td class="lang-switcher-switch x0">
				<fieldset>
					<p><?php _e('Language Switcher is disabled', 'cmp-coming-soon-maintenance')?></p>
				</fieldset>
            </td>

            <td class="lang-switcher-switch x1">

				<fieldset>
                    <label>
                        <input type="checkbox" name="niteoCS_lang_switcher_flag" id="niteoCS_lang_switcher_flag" <?php checked( '1', $lang_switcher_flag ); ?>">
                        <?php _e('Display Country Flag', 'cmp-coming-soon-maintenance');?>
                    </label>
				</fieldset>

				<fieldset style="margin-top:1em">
                    <label>
                        <input type="checkbox" name="niteoCS_lang_switcher_text" id="niteoCS_lang_switcher_text" <?php checked( '1', $lang_switcher_text ); ?>">
                        <?php _e('Display Country Name', 'cmp-coming-soon-maintenance');?>
                    </label>
				</fieldset>
                <br>
			</td>
		</tr>

		<?php echo $this->render_settings->submit(); ?>
		
		</tbody>
	</table>

</div>

