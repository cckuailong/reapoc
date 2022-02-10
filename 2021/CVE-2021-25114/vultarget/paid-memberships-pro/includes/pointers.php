<?php
/**
 * Notes in a Pointer dialog box for guiding users in the dashboard interface.
 *
 * NOTE: Disabled for now until we need a new pointer.
 */
///add_action( 'admin_enqueue_scripts', 'pmpro_enqueue_admin_pointer_scripts' );

/**
 * Enqueue the scripts needed to builder admin pointers.
 * 
 * @return void
 */
function pmpro_enqueue_admin_pointer_scripts() {
	if ( ! current_user_can( 'pmpro_memberships_menu' ) ) {
		return;
	}
	
	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );
	add_action( 'admin_print_footer_scripts', 'pmpro_prepare_pointer_scripts' );
}
/**
 * Details about PMPro 2.0 that are added to the Admin Pointer
 * 
 * @return void
 */
function pmpro_prepare_pointer_scripts() {
	// Just one pointer for now, but eventually we will have more
	$id       = '#toplevel_page_pmpro-dashboard';
	$content  = '<h3>' .  __( 'PMPro v2.0 Update', 'paid-memberships-pro' ) . '</h3>';
	$content .= '<p>'. sprintf( __( "The Memberships menu has moved. Check out the new dashboard. The Membership Levels and Discount Codes pages can now be found under <a href=\"%s\">Settings</a>.", 'paid-memberships-pro' ) , 'admin.php?page=pmpro-membershiplevels' ). '</p>';

	$options  = array(
		'content'  => $content,
		'position' => array(
			'edge'  => 'left',
			'align' => 'left',
		),
	);

	$globally_dismissed_pointers = get_option( 'pmpro_dismissed_wp_pointers', array() );
	$user_dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$dismissed_pointers = array_merge( $globally_dismissed_pointers, $user_dismissed_pointers );

	if ( ! in_array( 'pmpro_v2_menu_moved', $dismissed_pointers ) ) {
		pmpro_build_pointer_script( $id, $options, __( 'Close', 'paid-memberships-pro' ) );
	}
}

/**
 * Output script to generate our pointers.
 * 
 * @param  string  id attribute for the pointer html.
 * 
 * @param  array 	$options  Pointer options.
 * @param  string  	$button1  Text for button 1.
 * @param  string 	$button2  Text for button 2.
 * @param  string  	$function JS code to run if button 2 is clicked.
 * @return void
 */
function pmpro_build_pointer_script( $id, $options, $button1, $button2 = false, $function = '' ) {
	?>
<script type="text/javascript">
	(function ($) {
		// Define pointer options
		var wp_pointers_tour_opts = <?php echo json_encode( $options ); ?>, setup;

		wp_pointers_tour_opts = $.extend (wp_pointers_tour_opts, {
			// Add 'Close' button
			buttons: function (event, t) {
				button = jQuery ('<a id="pointer-close" class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');
				button.bind ('click.pointer', function () {
					t.element.pointer ('close');
				});
				return button;
			},
			close: function () {
				// Post to admin ajax to disable pointers when user clicks "Close"
				$.post (ajaxurl, {
					pointer: 'pmpro_v2_menu_moved',
					action: 'dismiss-wp-pointer'
				});
			}
		});

		// This is used for our "button2" value above (advances the pointers)
		setup = function () {
			$('<?php echo $id; ?>').pointer(wp_pointers_tour_opts).pointer('open');
			
			<?php if ( $button2 ) { ?>
				jQuery ('#pointer-close').after ('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
				jQuery ('#pointer-primary').click (function () {
					<?php echo $function; ?>  // Execute button2 function
				});
				jQuery ('#pointer-close').click (function () {
					// Post to admin ajax to disable pointers when user clicks "Close"
					$.post (ajaxurl, {
						pointer: 'pmpro_v2_menu_moved',
						action: 'dismiss-wp-pointer'
					});
				})
			<?php } ?>
		};

		if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {

			$(window).bind('load.wp-pointers', setup);
		}
		else {
			setup ();
		}
	}) (jQuery);
</script>
	<?php
}
