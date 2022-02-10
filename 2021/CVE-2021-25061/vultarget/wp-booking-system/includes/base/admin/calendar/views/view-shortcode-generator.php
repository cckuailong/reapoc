<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div id="wpbs-modal-add-calendar-shortcode" class="wpbs-modal">

	<!-- Modal Header -->
	<div class="wpbs-modal-header">

		<h2>
			<span class="dashicons dashicons-calendar-alt"><!-- --></span>
			<?php echo __( 'Add Calendar', 'wp-booking-system' ); ?>
		</h2>

		<span class="wpbs-modal-close dashicons dashicons-no-alt"><!-- --></span>

	</div>

	<!-- Modal Body -->
	<div class="wpbs-modal-body">

		<!-- Modal Nav Tab  -->
		<ul class="wpbs-modal-nav-tab-wrapper">
			<?php

				if( ! empty( $tabs ) ) {
					foreach( $tabs as $tab_slug => $tab_name ) {
						echo '<li class="wpbs-nav-tab wpbs-modal-nav-tab '.( 'insert-calendar' == $tab_slug ? 'wpbs-active' : '' ).'" data-tab="'.$tab_slug.'"><a href="#">'.$tab_name.'</a></li>';
					}
				}

			?>
			
		</ul>

		<!-- Modal Body Inner -->
		<div class="wpbs-modal-inner">

			
			<?php 
			if( ! empty( $tabs ) ) {
				$dir_path = plugin_dir_path( __FILE__ );

				$calendars = wpbs_get_calendars(array('status' => 'active'));

				foreach( $tabs as $tab_slug => $tab_name ) {
					if(file_exists($dir_path . 'view-shortcode-generator-'.$tab_slug.'.php')){
						include 'view-shortcode-generator-'.$tab_slug.'.php';
					} else {
						do_action( 'wpbs_shortcode_generator_tab_' . $tab_slug );
					}
					
				}
			}
			?>
			

		</div>

	</div>

</div>

<div id="wpbs-modal-add-calendar-shortcode-overlay" class="wpbs-modal-overlay"><!-- --></div>