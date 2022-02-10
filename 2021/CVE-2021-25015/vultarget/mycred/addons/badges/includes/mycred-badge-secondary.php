<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for site visits
 * @since 1.5
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Badge_Secondary' ) ) :
	class myCRED_Badge_Secondary {

		// Instnace
		protected static $_instance = NULL;

		/**
		 * Construct
		 */
		function __construct() {

			add_filter( 'mycred_badge_requirement',    array( $this, 'mycred_badge_specific_requirement'), 10, 5 );
			add_filter( 'mycred_badge_requirement_specific_template', array( $this, 'badge_specific_template'), 10, 5 );
            add_action( 'admin_head',                  array( $this, 'admin_header' ) );
		
		}

		/**
		 * Setup Instance
		 * @since 1.7
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function mycred_badge_specific_requirement( $query, $requirement_id, $requirement, $having, $user_id ) {
			
			global $wpdb, $mycred_log_table;

			if( $requirement['reference'] == 'link_click' && ! empty( $requirement['specific'] ) && $requirement['specific'] != 'Any' ) {
				
				$query = $wpdb->get_var( $wpdb->prepare( "SELECT {$having} FROM {$mycred_log_table} WHERE ctype = %s AND ref = %s AND data LIKE %s AND user_id = %d;", $requirement['type'], $requirement['reference'], '%'.$requirement['specific'].'%', $user_id ) );
			
			}
			else if( $requirement['reference'] == 'gravity_form_submission' && ! empty( $requirement['specific'] ) && $requirement['specific'] != 'Any' ) {
				$query = $wpdb->get_var( $wpdb->prepare( "SELECT {$having} FROM {$mycred_log_table} WHERE ctype = %s AND ref = %s AND ref_id = %d AND user_id = %d;", $requirement['type'], $requirement['reference'], $requirement['specific'], $user_id ) );
			}
			return $query;
		}

		public function badge_specific_template( $data, $requirement_id, $requirement, $badge, $level ) {

			if( $requirement['reference'] == 'link_click' && ! empty( $requirement['specific'] ) && $requirement['specific'] != 'Any' ) {
				
				$data = '<div class="form-group"><input type="text" name="mycred_badge[levels]['.$level.'][requires]['.$requirement_id.'][specific]" class="form-control specific" value="'.$requirement['specific'].'" data-row="'.$requirement_id.'" /></div>';
			
			}
			else if( $requirement['reference'] == 'gravity_form_submission' && ! empty( $requirement['specific'] ) && $requirement['specific'] != 'Any' ) {
				if( class_exists('RGFormsModel') ) {
					$gravityforms = RGFormsModel::get_forms();
					$form_list = '<option>Any</option>';
					foreach ($gravityforms as $form) {
						$form_list .= '<option value="'.$form->id.'" '.( $requirement['specific'] == $form->id ? ' selected="selected"' : '').' >'. htmlentities( $form->title, ENT_QUOTES ) .'</option>';
					}
					$data = '<div class="form-group"><select name="mycred_badge[levels]['.$level.'][requires]['.$requirement_id.'][specific]" class="form-control specific" data-row="'.$requirement_id.'" >'.$form_list.'</select></div>';
				}
			}
			return $data;

		}

		public function admin_header() {
			$screen = get_current_screen();
			
			if ( $screen->id == MYCRED_BADGE_KEY ):?>
		    <script type="text/javascript">
		    	var mycred_badge_link_click = '<div class="form-group"><input type="text" name="{{element_name}}" data-row="{{reqlevel}}" class="form-control specific" /></div>';

		    <?php
		    	if( class_exists('RGFormsModel') ) {
					$gravityforms = RGFormsModel::get_forms();
					$form_list = '<option>Any</option>';
					foreach ( $gravityforms as $form ) {
						$form_list .= '<option value="'.$form->id.'">'. htmlentities( $form->title, ENT_QUOTES ) .'</option>';
					}
					$data = '<div class="form-group"><select name="{{element_name}}" class="form-control specific" data-row="{{reqlevel}}" >'.$form_list.'</select></div>';
					echo "var mycred_badge_gravity_form_submission = '".$data."';";
				}
		    ?>
		    </script>
			<?php endif;
		}

	}
endif;

function myCRED_Badge_Secondary_init() {
	return myCRED_Badge_Secondary::instance();
}
myCRED_Badge_Secondary_init();

