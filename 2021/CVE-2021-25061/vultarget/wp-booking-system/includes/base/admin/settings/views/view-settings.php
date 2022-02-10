<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$tabs 		= $this->get_tabs();
$active_tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general' );
$settings 	= get_option( 'wpbs_settings', array() );

?>

<div class="wrap wpbs-wrap">

	<form action="options.php" method="POST">

		<?php settings_fields( 'wpbs_settings' ); ?>

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Settings', 'wp-booking-system' ); ?></h1>
		<hr class="wp-header-end" />

		<!-- Navigation Tabs -->
		<h2 class="wpbs-nav-tab-wrapper nav-tab-wrapper">
			<?php

				if( ! empty( $tabs ) ) {
					foreach( $tabs as $tab_slug => $tab_name ) {

						echo '<a href="' . add_query_arg( array( 'page' => 'wpbs-settings', 'tab' => $tab_slug ), admin_url('admin.php') ) . '" data-tab="' . $tab_slug . '" class="nav-tab wpbs-nav-tab ' . ( $active_tab == $tab_slug ? 'nav-tab-active' : '' ) . '">' . $tab_name . '</a>';

					}
				}

			?>
		</h2>

		<!-- Tabs Contents -->
		<div class="wpbs-tab-wrapper">

			<?php

				if( ! empty( $tabs ) ) {

					foreach( $tabs as $tab_slug => $tab_name ) {

						echo '<div class="wpbs-tab wpbs-tab-' . $tab_slug . ' ' . ( $active_tab == $tab_slug ? 'wpbs-active' : '' ) . '" data-tab="' . $tab_slug . '">';

						// Handle general tab
						if( $tab_slug == 'general' ) {

							include 'view-settings-tab-general.php';

						// Handle languages tab
						} else if( $tab_slug == 'languages' ) {

							include 'view-settings-tab-languages.php';

						// Handle form tabs
						} else if( $tab_slug == 'form' ) {

							include 'view-settings-tab-form.php';

						// Handle dynamic tabs
						} else {

							/**
							 * Action to dynamically add content for each tab
							 *
							 */
							do_action( 'wpbs_submenu_page_settings_tab_' . $tab_slug );

						}

						echo '</div>';

					}

				}

			?>
		</div>

		<!-- Always update hidden -->
		<input type="hidden" name="wpbs_settings[always_update]" value="<?php echo ( isset( $settings['always_update'] ) && $settings['always_update'] == 1 ? 0 : 1 ); ?>" />

	</form>

</div>