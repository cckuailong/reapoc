<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 1.4
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_gravity_forms_hook', 65 );
function mycred_register_gravity_forms_hook( $installed ) {

	if ( ! class_exists( 'GFForms' ) ) return $installed;

	$installed['gravityform'] = array(
		'title'         => __( 'Gravityform Submissions', 'mycred' ),
		'description'   => __( 'Awards %_plural% for successful form submissions.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/submitting-gravity-forms/',
		'callback'      => array( 'myCRED_Gravity_Forms' )
	);

	return $installed;

}

/**
 * Gravity Forms Hook
 * @since 1.4
 * @version 1.1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_gravity_forms_hook', 65 );
function mycred_load_gravity_forms_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Gravity_Forms' ) || ! class_exists( 'GFForms' ) ) return;

	class myCRED_Gravity_Forms extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'gravityform',
				'defaults' => array()
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.4
		 * @version 1.0
		 */
		public function run() {

			add_action( 'gform_after_submission', array( $this, 'form_submission' ), 10, 2 );

		}

		/**
		 * Successful Form Submission
		 * @since 1.4
		 * @version 1.1
		 */
		public function form_submission( $lead, $form ) {

			// Login is required
			if ( ! is_user_logged_in() || ! isset( $lead['form_id'] ) ) return;

			// Prep
			$user_id = absint( $lead['created_by'] );
			$form_id = absint( $lead['form_id'] );

			// Make sure form is setup and user is not excluded
			if ( ! isset( $this->prefs[ $form_id ] ) || $this->core->exclude_user( $user_id ) ) return;

			// Limit
			if ( $this->over_hook_limit( $form_id, 'gravity_form_submission' ) ) return;

			// Default values
			$amount = $this->prefs[ $form_id ]['creds'];
			$entry  = $this->prefs[ $form_id ]['log'];

			// See if the form contains myCRED fields that override these defaults
			if ( isset( $form['fields'] ) && ! empty( $form['fields'] ) ) {
				foreach ( $form['fields'] as $field ) {

					// Amount override
					if ( $field->label == 'mycred_amount' ) {
						$amount = $this->core->number( $field->defaultValue );
					}

					// Entry override
					if ( $field->label == 'mycred_entry' ) {
						$entry = sanitize_text_field( $field->defaultValue );
					}

				}
			}

			// Amount can not be zero
			if ( $amount == 0 ) return;

			// Execute
			$this->core->add_creds(
				'gravity_form_submission',
				$user_id,
				$amount,
				$entry,
				$form_id,
				'',
				$this->mycred_type
			);

		}

		/**
		 * Preferences for Gravityforms Hook
		 * @since 1.4
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;
			$forms = RGFormsModel::get_forms();

			// No forms found
			if ( empty( $forms ) ) {
				echo '<p>' . __( 'No forms found.', 'mycred' ) . '</p>';
				return;
			}

			// Loop though prefs to make sure we always have a default setting
			foreach ( $forms as $form ) {
				if ( ! isset( $prefs[ $form->id ] ) ) {
					$prefs[ $form->id ] = array(
						'creds' => 1,
						'log'   => '',
						'limit' => '0/x'
					);
				}

				if ( ! isset( $prefs[ $form->id ]['limit'] ) )
					$prefs[ $form->id ]['limit'] = '0/x';
			}

			// Set pref if empty
			if ( empty( $prefs ) ) $this->prefs = $prefs;

			// Loop for settings
			foreach ( $forms as $form ) {

?>
<div class="hook-instance">
	<h3><?php printf( __( 'Form: %s', 'mycred' ), $form->title ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $form->id, 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $form->id, 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $form->id, 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs[ $form->id ]['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $form->id, 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( $form->id, 'limit' ) ), $this->field_id( array( $form->id, 'limit' ) ), $prefs[ $form->id ]['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $form->id, 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $form->id, 'log' ) ); ?>" id="<?php echo $this->field_id( array( $form->id, 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs[ $form->id ]['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

			}

		}

		/**
		 * Sanitise Preferences
		 * @since 1.6
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			$forms = RGFormsModel::get_forms();
			foreach ( $forms as $form ) {

				if ( isset( $data[ $form->id ]['limit'] ) && isset( $data[ $form->id ]['limit_by'] ) ) {
					$limit = sanitize_text_field( $data[ $form->id ]['limit'] );
					if ( $limit == '' ) $limit = 0;
					$data[ $form->id ]['limit'] = $limit . '/' . $data[ $form->id ]['limit_by'];
					unset( $data[ $form->id ]['limit_by'] );
				}

			}

			return $data;

		}

	}

}
