<?php
if ( ! defined( 'MYCRED_PURCHASE' ) ) exit;

/**
 * myCRED_Addons_Module class
 * @since 0.1
 * @version 1.1.1
 */
if ( ! class_exists( 'myCRED_buyCRED_Reward_Hook' ) ) :
	class myCRED_buyCRED_Reward_Hook extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'buycred_reward',
				'defaults' => array(
					'creds' => array(),
					'log'   => array(),
					'min'   => array(),
					'max'   => array()
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.8
		 * @version 1.0
		 */
		public function run() {

			add_filter( 'mycred_add_finished', array( $this, 'assign_buycred_reward' ), 20, 3 );

		}

		/**
		 * Page Load
		 * @since 1.8
		 * @version 1.0
		 */
		public function assign_buycred_reward( $result, $request, $mycred ) {

			extract( $request );

			if ( ! $result || strpos( $ref, 'buy_creds_with' ) === false ) return $result;;

			// Make sure user is not excluded
			if ( $this->core->exclude_user( $user_id ) ) return;

			if ( 
				! empty( $this->prefs['creds'] ) && 
				! empty( $this->prefs['log'] ) && 
				! empty( $this->prefs['min'] ) && 
				! empty( $this->prefs['max'] ) 
			) {


				$threshold = array();

				foreach ( $this->prefs['creds'] as $key => $value ) {
					
					if ( 
						floatval( $amount ) >= floatval( $this->prefs['min'][$key] ) &&						 
						floatval( $amount ) <= floatval( $this->prefs['max'][$key] )						 
					) {
						array_push( $threshold, $key );
					}

				}

				if ( ! empty( $threshold ) ) {

					$hook_index = end( $threshold );

					// Execute
					$this->core->add_creds(
				        'buycred_reward',
				        $user_id,
				        $this->prefs['creds'][$hook_index],
				        $this->prefs['log'][$hook_index],
				        $ref_id,
				        $data,
						$this->mycred_type
					);
				}
				
			}

			return $result;

		}

		/**
		 * Preference for Anniversary Hook
		 * @since 1.8
		 * @version 1.0
		 */
		public function preferences() {

			$prefs = $this->prefs;

			if ( count( $prefs['creds'] ) > 0 ) {
				$hooks = $this->buycred_reward_arrange_data( $prefs );
				$this->buycred_reward_setting( $hooks, $this );
			}
			else {
				$default_data = array(
					array(
						'creds' => '10',
						'log'   => 'Reward for Buying %plural%.',
						'min'   => '1',
						'max'   => '10'
					)
				);
				$this->buycred_reward_setting( $default_data, $this );
			}

		}

	   /**
	   * Sanitize Preferences
	   */
		public function sanitise_preferences( $data ) {

			$new_data = array();

			foreach ( $data as $data_key => $data_value ) {
				foreach ( $data_value as $key => $value) {
					if ( $data_key == 'creds' ) {
						$new_data[$data_key][$key] = ( !empty( $value ) ) ? floatval( $value ) : 10;
					}
					else if ( $data_key == 'log' ) {
						$new_data[$data_key][$key] = ( !empty( $value ) ) ? sanitize_text_field( $value ) : 'Reward for Buying %plural%.';
					}
					else if ( $data_key == 'min' ) {
						$new_data[$data_key][$key] = ( !empty( $value ) ) ? floatval( $value ) : 1;
					}
					else if ( $data_key == 'max' ) {
						$new_data[$data_key][$key] = ( !empty( $value ) ) ? floatval( $value ) : 1;
					}
				}
			}
			return $new_data;
		}

		public function buycred_reward_arrange_data( $data ){
			$hook_data = array();
			foreach ( $data['creds'] as $key => $value ) {
				$hook_data[$key]['creds'] = $data['creds'][$key];
				$hook_data[$key]['log']   = $data['log'][$key];
				$hook_data[$key]['min']   = $data['min'][$key];
				$hook_data[$key]['max']   = $data['max'][$key];
			}
			return $hook_data;
		}

		public function buycred_reward_setting( $data ){

			foreach ( $data as $hook ):?>
				<div class="hook-instance">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="form-group">
								<label>Reward <?php echo $this->core->plural(); ?></label>
								<input type="text" name="<?php echo $this->name( $this->mycred_type, 'creds' ); ?>" value="<?php echo $this->core->number( $hook['creds'] ); ?>" class="form-control buycred-reward-creds" />
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="form-group">
								<label><?php _e( 'Log Template', 'mycred' ); ?></label>
								<input type="text" name="<?php echo $this->name( $this->mycred_type, 'log' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $hook['log'] ); ?>" class="form-control buycred-reward-log" />
								<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="form-group">
								<label><?php _e( 'Minimum', 'mycred' ); ?></label>
								<input type="text" name="<?php echo $this->name( $this->mycred_type, 'min' ); ?>" value="<?php echo $this->core->number( $hook['min'] ); ?>" class="form-control buycred-reward-min" />
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="form-group">
								<label><?php _e( 'Maximium', 'mycred' ); ?></label>
								<input type="text" name="<?php echo $this->name( $this->mycred_type, 'max' ); ?>" value="<?php echo $this->core->number( $hook['max'] ); ?>" class="form-control buycred-reward-max" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="form-group specific-hook-actions textright">
								<button class="button button-small mycred-add-specific-hook" type="button">Add More</button>
								<button class="button button-small mycred-remove-specific-hook" type="button">Remove</button>
							</div>
						</div>
					</div>
				</div>
		<?php
			endforeach;
		}

		public function name( $type, $attr ){

			$hook_prefs_key = 'mycred_pref_hooks';

			if ( $type != MYCRED_DEFAULT_TYPE_KEY ) {
				$hook_prefs_key = 'mycred_pref_hooks_'.$type;
			}

			return "{$hook_prefs_key}[hook_prefs][buycred_reward][{$attr}][]";
		}

	}
endif;