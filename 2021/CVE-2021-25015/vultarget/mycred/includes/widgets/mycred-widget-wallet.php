<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Widget: myCRED Wallet
 * @since 1.4
 * @version 1.2.1
 */
if ( ! class_exists( 'myCRED_Widget_Wallet' ) ) :
	class myCRED_Widget_Wallet extends WP_Widget {

		/**
		 * Construct
		 */
		public function __construct() {

			parent::__construct(
				'mycred_widget_wallet',
				sprintf( __( '(%s) Wallet', 'mycred' ), mycred_label( true ) ),
				array(
					'classname'   => 'widget-my-wallet',
					'description' => __( 'Shows multiple balances.', 'mycred' )
				)
			);

		}

		/**
		 * Widget Output
		 */
		public function widget( $args, $instance ) {

			extract( $args, EXTR_SKIP );

			$mycred = mycred();

			// If we are logged in
			if ( is_user_logged_in() ) {

				if ( ! isset( $instance['types'] ) || empty( $instance['types'] ) )
					$instance['types'] = array( MYCRED_DEFAULT_TYPE_KEY );

				// Get Current Users Account Object
				$account = mycred_get_account();
				if ( $account === false ) return;

				// Excluded users have no balance(s)
				if ( ! isset( $account->point_types ) || empty( $account->point_types ) ) return;

				// Start
				echo $before_widget;

				// Title
				if ( ! empty( $instance['title'] ) )
					echo $before_title . $instance['title'] . $after_title;

				$current_user = wp_get_current_user();

				// Loop through balances
				foreach ( $account->balance as $point_type_id => $balance ) {

					if ( ! in_array( $point_type_id, $instance['types'] ) || $balance === false ) continue;

					$point_type = mycred( $point_type_id );

					$layout     = $instance['row'];
					$layout     = $point_type->template_tags_amount( $layout, $balance->current );
					$layout     = $point_type->template_tags_user( $layout, false, $current_user );
					$layout     = str_replace( '%label%', $balance->point_type->plural, $layout );

					echo '<div class="myCRED-balance mycred-balance-' . esc_attr( $point_type_id ) . '">' . do_shortcode( $layout ) . '</div>';

				}

				// End
				echo $after_widget;

			}

			// Visitor
			elseif ( ! is_user_logged_in() && $instance['show_visitors'] ) {

				echo $before_widget;

				// Title
				if ( ! empty( $instance['title'] ) )
					echo $before_title . $instance['title'] . $after_title;

				$message = $instance['message'];
				$message = $mycred->template_tags_general( $message );

				echo '<div class="myCRED-wallet-message"><p>' . wptexturize( $message ) . '</p></div>';

				echo $after_widget;

			}

		}

		/**
		 * Outputs the options form on admin
		 */
		public function form( $instance ) {

			$mycred        = mycred();

			// Defaults
			$title         = isset( $instance['title'] )         ? $instance['title']         : 'My Wallet';
			$types         = isset( $instance['types'] )         ? $instance['types']         : array();
			$row_template  = isset( $instance['row'] )           ? $instance['row']           : '%label%: %cred_f%';
			$show_visitors = isset( $instance['show_visitors'] ) ? $instance['show_visitors'] : 0;
			$message       = isset( $instance['message'] )       ? $instance['message']       : '<a href="%login_url_here%">Login</a> to view your balance.';

?>
<!-- Widget Options -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'mycred' ); ?>:</label>
	<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
</p>

<!-- Point Type -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'types' ) ); ?>"><?php _e( 'Point Types', 'mycred' ); ?>:</label><br />
	<?php mycred_types_select_from_checkboxes( $this->get_field_name( 'types' ) . '[]', $this->get_field_id( 'types' ), $types ); ?>
</p>

<!-- Row layout -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'row' ) ); ?>"><?php _e( 'Row Layout', 'mycred' ); ?>:</label>
	<textarea name="<?php echo esc_attr( $this->get_field_name( 'row' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'row' ) ); ?>" rows="3" cols="20" class="widefat"><?php echo esc_attr( $row_template ); ?></textarea>
	<small><?php echo $mycred->available_template_tags( array( 'general', 'amount' ) ); ?></small>
</p>

<!-- Show to Visitors -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_visitors' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>" value="1"<?php checked( $show_visitors, 1 ); ?> class="checkbox" /> <?php _e( 'Show message when not logged in', 'mycred' ); ?></label>
</p>
<div id="<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>-details" class="mycred-hidden<?php if ( $show_visitors == 1 ) echo ' ex-field'; ?>">
	<p class="myCRED-widget-field">
		<label for="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>"><?php _e( 'Message', 'mycred' ); ?>:</label>
		<textarea name="<?php echo esc_attr( $this->get_field_name( 'message' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>" rows="3" cols="20" class="widefat"><?php echo esc_attr( $message ); ?></textarea>
		<small><?php echo $mycred->available_template_tags( array( 'general', 'amount' ) ); ?></small>
	</p>
</div>
<!-- Widget Admin Scripting -->
<script type="text/javascript">//<![CDATA[
jQuery(function($) {

	$( '#<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>' ).change(function(){
		$( '#' + $(this).attr( 'id' ) + '-details' ).toggleClass( 'ex-field' );
	});

});//]]>
</script>
<?php

		}

		/**
		 * Processes widget options to be saved
		 */
		public function update( $new_instance, $old_instance ) {

			$instance                  = $old_instance;

			$instance['title']         = wp_kses_post( $new_instance['title'] );
			$instance['types']         = (array) $new_instance['types'];
			$instance['row']           = wp_kses_post( $new_instance['row'] );
			$instance['show_visitors'] = ( isset( $new_instance['show_visitors'] ) ) ? 1 : 0;
			$instance['message']       = wp_kses_post( $new_instance['message'] );

			mycred_flush_widget_cache( 'mycred_widget_wallet' );

			return $instance;

		}

	}
endif;
