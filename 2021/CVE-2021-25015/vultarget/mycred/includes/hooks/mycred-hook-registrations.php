<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for registrations
 * @since 0.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Hook_Registration' ) ) :
	class myCRED_Hook_Registration extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'registration',
				'defaults' => array(
					'creds'   => 10,
					'log'     => '%plural% for becoming a member'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 0.1
		 * @version 1.2.1
		 */
		public function run() {

			add_action( 'user_register', array( $this, 'registration' ) );

		}

		/**
		 * Registration Hook
		 * @since 0.1
		 * @version 1.1
		 */
		public function registration( $user_id ) {

			// Make sure user is not excluded
			if ( $this->core->exclude_user( $user_id ) ) return;

			$data = array( 'ref_type' => 'user' );

			// Make sure this is unique
			if ( $this->core->has_entry( 'registration', $user_id, $user_id, $data, $this->mycred_type ) ) return;

			// Execute
			$this->core->add_creds(
				'registration',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				$user_id,
				$data,
				$this->mycred_type
			);

		}

		/**
		 * Preference for Registration Hook
		 * @since 0.1
		 * @version 1.1
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
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'user' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

	}
endif;
