<?php
namespace A3Rev\PageViewsCount;

class MetaBox
{
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		add_meta_box(
			'a3_pvc'
			,__( 'Page View Counter', 'page-views-count' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'side'
			,'high'
			, array( 
				'__block_editor_compatible_meta_box' => true,
				'__back_compat_meta_box' => false,
			)
		);
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
		global $wp_version;

		wp_enqueue_script( 'jquery' );

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'a3_pvc_activation_custom_box', 'a3_pvc_activation_custom_box_nonce' );

		$is_activated = A3_PVC::pvc_admin_is_activated( $post->ID );
		$view_status = A3_PVC::pvc_fetch_post_counts( $post->ID );

		$total_views = 0;
		$today_views = 0;
		if ( $view_status ) {
			$total_views = $view_status->total;
			$today_views = $view_status->today;
		}

		// Display the form, using the current value.
		?>
		<div class="" style="visibility: visible; height: auto;">
			<div class="">
				<div class="forminp forminp-onoff_checkbox">
		    		<input
		    			id="a3_pvc_activated"
		    			name="a3_pvc_activated"
		    			class="a3_pvc_activated"
		    			type="checkbox"
		    			value="true"
		    			checked_label="<?php _e( 'ON', 'page-views-count' ); ?>"
						unchecked_label="<?php _e( 'OFF', 'page-views-count' ); ?>"
		    			<?php checked( $is_activated ); ?> />
		    		<label for="a3_pvc_activated"><?php _e( 'Activate on this item', 'page-views-count' ) ?></label>
	    		</div>
	    		<div style="clear:both;"></div>
	    		<div class="a3_pvc_activated_container">
		    		<p>
		    			<label for="a3_pvc_total_views" style="display: inline-block; width: 100px;"><?php _e( 'All Time Views', 'page-views-count' ) ?></label>
		    			<input type="text" name="a3_pvc_total_views" id="a3_pvc_total_views" value="<?php echo esc_attr( $total_views ); ?>" style="width: 100px;" />
		    		</p>
		    		<p>
		    			<label for="a3_pvc_today_views" style="display: inline-block; width: 100px;"><?php _e( 'Today Views', 'page-views-count' ) ?></label>
		    			<input type="text" name="a3_pvc_today_views" id="a3_pvc_today_views" value="<?php echo esc_attr( $today_views ); ?>" style="width: 100px;" />
		    		</p>
	    		</div>
	    	</div>
    	</div>
    	<script type="text/javascript">
    	(function($) {
		$(document).ready(function() {
			if ( $("input.a3_pvc_activated:checked").val() != 'true') {
				$('.a3_pvc_activated_container').slideUp();
			}
			$("input.a3_pvc_activated").on( 'change', function() {
				if ( $(this).is(":checked") ) {
					$('.a3_pvc_activated_container').slideDown();
				} else {
					$('.a3_pvc_activated_container').slideUp();
				}
			});
		});
		})(jQuery);
    	</script>
    	<div style="clear:both;"></div>
    	<?php
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['a3_pvc_activation_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['a3_pvc_activation_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'a3_pvc_activation_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */
		if ( isset( $_POST['a3_pvc_activated'] ) ) {
			$a3_pvc_activated = 'true';
		} else {
			$a3_pvc_activated = 'false';
		}

		// Update the meta field.
		update_post_meta( $post_id, '_a3_pvc_activated', $a3_pvc_activated );

		// Manual change Total Views and Today Views
		if ( isset( $_POST['a3_pvc_total_views'] ) && isset( $_POST['a3_pvc_today_views'] ) ) {
			$total_views = absint( trim( $_POST['a3_pvc_total_views'] ) );
			$today_views = absint( trim( $_POST['a3_pvc_today_views'] ) );

			A3_PVC::pvc_stats_manual_update( $post_id, $total_views, $today_views );
		}
	}

}
