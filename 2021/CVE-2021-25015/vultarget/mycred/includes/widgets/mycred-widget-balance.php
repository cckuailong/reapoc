<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Widget: myCRED Balance
 * @since 0.1
 * @version 1.4.3
 */
if ( ! class_exists( 'myCRED_Widget_Balance' ) ) :
	class myCRED_Widget_Balance extends WP_Widget {

		/**
		 * Construct
		 */
		public function __construct() {

			parent::__construct(
				'mycred_widget_balance',
				sprintf( __( '(%s) My Balance', 'mycred' ), mycred_label( true ) ),
				array(
					'classname'   => 'widget-my-cred',
					'description' => __( 'Show the current users balance and history.', 'mycred' )
				)
			);

		}

		/**
		 * Widget Output
		 */
		public function widget( $args, $instance ) {

			extract( $args, EXTR_SKIP );

			// Make sure we always have a type set
			if ( ! isset( $instance['type'] ) || $instance['type'] == '' )
				$instance['type'] = MYCRED_DEFAULT_TYPE_KEY;

			// If we are logged in
			if ( is_user_logged_in() ) {

				// Get Current Users Account Object
				$account = mycred_get_account( get_current_user_id() );
				if ( $account === false ) return;

				// Excluded users have no balance(s)
				if ( ! isset( $account->point_types ) || empty( $account->point_types ) || $account->balance[ $instance['type'] ] === false ) return;

				// Get balance object
				$balance = $account->balance[ $instance['type'] ];
				$mycred  = mycred( $instance['type'] );

				// Start
				echo $before_widget;

				// Title
				if ( ! empty( $instance['title'] ) )
					echo $before_title . $instance['title'] . $after_title;

				$layout = $mycred->template_tags_amount( $instance['cred_format'], $balance->current );
				$layout = $mycred->template_tags_user( $layout, false, wp_get_current_user() );

				echo '<div class="myCRED-balance ' . esc_attr( $instance['type'] ) . '">' . do_shortcode( $layout ) . '</div>';

				// If we want to include history
				if ( MYCRED_ENABLE_LOGGING && $instance['show_history'] ) {

					echo '<div class="myCRED-widget-history">';

					// Query Log
					$log = new myCRED_Query_Log( array(
						'user_id' => $account->user_id,
						'number'  => $instance['number'],
						'ctype'   => $instance['type']
					) );

					// Have results
					if ( $log->have_entries() ) {

						// Title
						if ( ! empty( $instance['history_title'] ) )
							echo $before_title . $mycred->template_tags_general( $instance['history_title'] ) . $after_title;

						// Organized List
						echo '<ol class="myCRED-history">';
						$alt         = 0;
						$date_format = get_option( 'date_format' );
						foreach ( $log->results as $entry ) {

							// Row Layout
							$layout = $instance['history_format'];

							$layout = str_replace( '%date%',  '<span class="date">' . date( $date_format, $entry->time ) . '</span>', $layout );
							$layout = str_replace( '%entry%', $mycred->parse_template_tags( $entry->entry, $entry ), $layout );

							$layout = $mycred->template_tags_amount( $layout, $entry->creds );

							// Alternating rows
							$alt = $alt+1;
							if ( $alt % 2 == 0 ) $class = 'entry-row alternate';
							else $class = 'entry-row';

							// Output list item
							echo '<li class="' . $class . '">' . $layout . '</li>';

						}
						echo '</ol>';

					}
					$log->reset_query();

					echo '</div>';
				}

				// End
				echo $after_widget;

			}

			// Visitor
			else {

				// If we want to show a message, then do so
				if ( $instance['show_visitors'] ) {

					echo $before_widget;

					$mycred = mycred( $instance['type'] );

					// Title
					if ( ! empty( $instance['title'] ) )
						echo $before_title . $instance['title'] . $after_title;

					$message = $instance['message'];
					$message = $mycred->template_tags_general( $message );
					$message = $mycred->allowed_tags( $message );

					echo '<div class="myCRED-my-balance-message"><p>' . nl2br( $message ) . '</p></div>';

					echo $after_widget;

				}

			}

		}

		/**
		 * Outputs the options form on admin
		 */
		public function form( $instance ) {

			// Defaults
			$title          = isset( $instance['title'] )          ? $instance['title']          : 'My Balance';
			$type           = isset( $instance['type'] )           ? $instance['type']           : MYCRED_DEFAULT_TYPE_KEY;
			$cred_format    = isset( $instance['cred_format'] )    ? $instance['cred_format']    : '%cred_f%';
			$show_history   = isset( $instance['show_history'] )   ? $instance['show_history']   : 0;
			$history_title  = isset( $instance['history_title'] )  ? $instance['history_title']  : '%plural% History';
			$history_entry  = isset( $instance['history_format'] ) ? $instance['history_format'] : '%entry% <span class="creds">%cred_f%</span>';
			$history_length = isset( $instance['number'] )         ? $instance['number']         : 5;
			$show_visitors  = isset( $instance['show_visitors'] )  ? $instance['show_visitors']  : 0;
			$message        = isset( $instance['message'] )        ? $instance['message']        : '<a href="%login_url_here%">Login</a> to view your balance.';

			$mycred         = mycred( $type );
			$mycred_types   = mycred_get_types();

?>
<!-- Widget Admin Styling -->
<style type="text/css">
div.mycred-hidden { display: none; }
div.mycred-hidden.ex-field { display: block; }
</style>

<!-- Widget Options -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'mycred' ); ?>:</label>
	<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
</p>

<!-- Point Type -->
<?php if ( count( $mycred_types ) > 1 ) : ?>
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Point Type', 'mycred' ); ?>:</label>
	<?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type ); ?>
</p>
<?php else : ?>
	<?php mycred_types_select_from_dropdown( $this->get_field_name( 'type' ), $this->get_field_id( 'type' ), $type ); ?>
<?php endif; ?>

<!-- Balance layout -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'cred_format' ) ); ?>"><?php _e( 'Balance Layout', 'mycred' ); ?>:</label>
	<textarea name="<?php echo esc_attr( $this->get_field_name( 'cred_format' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'cred_format' ) ); ?>" rows="3" cols="20" class="widefat"><?php echo esc_attr( $cred_format ); ?></textarea>
	<small><?php echo $mycred->available_template_tags( array( 'general', 'amount', 'user' ) ); ?></small>
</p>
<?php if ( MYCRED_ENABLE_LOGGING ) : ?>
<!-- History -->
<p class="myCRED-widget-field">
	<label for="<?php echo esc_attr( $this->get_field_id( 'show_history' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_history' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_history' ) ); ?>" value="1"<?php checked( $show_history, 1 ); ?> class="checkbox" /> <?php _e( 'Include history', 'mycred' ); ?></label>
</p>
<div id="<?php echo esc_attr( $this->get_field_id( 'show_history' ) ); ?>-details" class="mycred-hidden<?php if ( $show_history == 1 ) echo ' ex-field'; ?>">
	<p class="myCRED-widget-field">
		<label for="<?php echo esc_attr( $this->get_field_id( 'history_title' ) ); ?>"><?php _e( 'History Title', 'mycred' ); ?>:</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'history_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'history_title' ) ); ?>" type="text" value="<?php echo esc_attr( $history_title ); ?>" class="widefat" />
	</p>
	<p class="myCRED-widget-field">
		<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of entires', 'mycred' ); ?>:</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo absint( $history_length ); ?>" size="3" class="widefat" /><br />
	</p>
	<p class="myCRED-widget-field">
		<label for="<?php echo esc_attr( $this->get_field_id( 'history_format' ) ); ?>"><?php _e( 'Row layout', 'mycred' ); ?>:</label>
		<textarea name="<?php echo esc_attr( $this->get_field_name( 'history_format' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'history_format' ) ); ?>" rows="3" cols="20" class="widefat"><?php echo esc_attr( $history_entry ); ?></textarea>
		<small><?php echo $mycred->available_template_tags( array( 'general', 'widget' ) ); ?></small>
	</p>
</div>
<?php else : ?>
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'show_history' ) ); ?>" value="<?php echo esc_attr( $show_history ); ?>" />
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'history_title' ) ); ?>" value="<?php echo esc_attr( $history_title ); ?>" />
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo esc_attr( $history_length ); ?>" />
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'history_format' ) ); ?>" value="<?php echo esc_attr( $history_entry ); ?>" />
<?php endif; ?>
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

	$( '#<?php echo esc_attr( $this->get_field_id( 'show_history' ) ); ?>, #<?php echo esc_attr( $this->get_field_id( 'show_visitors' ) ); ?>' ).change(function(){
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

			$instance                   = $old_instance;

			$instance['title']          = wp_kses_post( $new_instance['title'] );
			$instance['type']           = sanitize_text_field( $new_instance['type'] );
			$instance['cred_format']    = wp_kses_post( $new_instance['cred_format'] );
			$instance['show_history']   = ( isset( $new_instance['show_history'] ) ) ? 1 : 0;
			$instance['history_title']  = wp_kses_post( $new_instance['history_title'] );
			$instance['history_format'] = wp_kses_post( $new_instance['history_format'] );
			$instance['number']         = absint( $new_instance['number'] );
			$instance['show_visitors']  = ( isset( $new_instance['show_visitors'] ) ) ? 1 : 0;
			$instance['message']        = wp_kses_post( $new_instance['message'] );

			mycred_flush_widget_cache( 'mycred_widget_balance' );

			return $instance;

		}

	}
endif;
