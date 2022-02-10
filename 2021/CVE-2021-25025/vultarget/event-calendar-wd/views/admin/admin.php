<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $ecwd_settings;
global $ecwd_tabs;

?>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="ecwd-settings">

		<div id="ecwd-settings-content">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<h2 class="nav-tab-wrapper">
				<?php
				$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field($_GET['tab']) : 'general';
				foreach ($ecwd_settings as $key=>$ecwd_setting){
					$active = $current_tab == $key ? 'nav-tab-active' : '';
					echo '<a class="nav-tab ' . $active . '" href="' . ECWD_MENU_SLUG . '&page=ecwd_general_settings&tab=' . $key . '">' . $ecwd_tabs[$key] . '</a>';
				}
				?>

			</h2>

			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php
				settings_fields( ECWD_PLUGIN_PREFIX.'_settings_'.$current_tab );
				do_settings_sections( ECWD_PLUGIN_PREFIX.'_settings_'.$current_tab );

				?>

				<?php submit_button(null,'primary','submit',false); ?>
                <a id="ecwd_reset_settings_button" href="#" class="button">Reset</a>

			</form>

            <form method="post" id="ecwd_reset_settings_form">
              <?php wp_nonce_field('ecwd_reset_settings', 'ecwd_reset_settings_nonce'); ?>
                <input type="hidden" name="ecwd_reset_settings"
                       value="<?php echo ECWD_PLUGIN_PREFIX . '_settings_' . $current_tab; ?>"/>
            </form>
        </div>
        <div id="ecwd_past_event_list_popup" class="ecwd_past_event_list_popup mfp-hide">
            <img class="ecwd_past_event_list_popup_loader" src="<?php echo ECWD_URL;?>/assets/loading.gif">
            <button class="button button-primary button-large ecwd_delete_events ecwd_past_events_delete_button">Delete</button>
        </div>
        <!-- #ecwd-settings-content -->

	</div>
	<!-- #ecwd-settings -->
</div><!-- .wrap -->