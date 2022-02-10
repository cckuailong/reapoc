<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for Anniversary
 * @since 1.8
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Hook_Anniversary' ) ) :
	class myCRED_Hook_Anniversary extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'anniversary',
				'defaults' => array(
					'creds'   => 10,
					'log'     => '%plural% for being a member for a year'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.8
		 * @version 1.0
		 */
		public function run() {

			add_action( 'template_redirect', array( $this, 'page_load' ) );

		}

		/**
		 * Page Load
		 * @since 1.8
		 * @version 1.0
		 */
		public function page_load() {

			if ( ! is_user_logged_in() ) return;

			$user_id  = get_current_user_id();

			// Make sure user is not excluded
			if ( $this->core->exclude_user( $user_id ) ) return;

			// Make sure this only runs once a day
			$last_run = mycred_get_user_meta( $user_id, 'anniversary-' . $this->mycred_type, '', true );
			$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
			if ( $last_run == $today ) return;

			global $wpdb;

			$result = $wpdb->get_row( $wpdb->prepare( "SELECT user_registered, TIMESTAMPDIFF( YEAR, user_registered, CURDATE()) AS difference FROM {$wpdb->users} WHERE ID = %d;", $user_id ) );

			// If we have been a member for more then one year
			if ( isset( $result->user_registered ) && $result->difference >= 1 ) {

				$year_joined = substr( $result->user_registered, 0, 4 );
				$date_joined = strtotime( $result->user_registered );

				// First time we give points we might need to give for more then one year
				// so we give points for each year.
				for ( $i = 0; $i < $result->difference; $i++ ) {

					$year_joined++;
					if ( $this->core->has_entry( 'anniversary', $year_joined, $user_id, $date_joined, $this->mycred_type ) ) continue;

					// Execute
					$this->core->add_creds(
						'anniversary',
						$user_id,
						$this->prefs['creds'],
						$this->prefs['log'],
						$year_joined,
						$date_joined,
						$this->mycred_type
					);

				}

			}

			mycred_update_user_meta( $user_id, 'anniversary-' . $this->mycred_type, '', $today );

		}

		/**
		 * Preference for Anniversary Hook
		 * @since 1.8
		 * @version 1.0
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'creds' ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>" value="<?php echo $this->core->number( $prefs['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'log' ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

	}
endif;
