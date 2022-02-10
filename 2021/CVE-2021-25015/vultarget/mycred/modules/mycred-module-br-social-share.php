<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Module_Social_Share class
 * @since 2.2
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Module_OB_Social_Share' ) ) :
	class myCRED_Module_BR_Social_Share extends myCRED_Module 
    {
        /**
		 * Construct
         * @since 2.2
         * @version 1.0
		 */
		public function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'myCRED_Module_BR_Social_Share', array(
				'module_name' => 'br_social_share',
				'defaults'    => array(
					'enable_open_badge_ss'     => 0,
					'facebook'      => '1',
					'twitter'       => '1',
					'linkedin'      => '1',
					'pinterest'     => '1',
					'button_style'  => 'button_style',
                    'enable_fb'     =>  '1',
                    'enable_twitter'     =>  '1',
                    'enable_li'     =>  '1',
                    'enable_pt'     =>  '1'
				),
				'accordion'   => false,
				'register'    => false,
				'add_to_core' => true
			), $type );

		}

        /**
		 * Load
		 * @since 2.2
		 * @version 1.0
		 */
        public function load()
        {
            add_action( 'mycred_admin_after_badges_settings', array( $this, 'after_general_settings' ), 20 );
			add_filter( 'mycred_save_core_prefs', array( $this, 'sanitize_extra_settings' ), 80, 3 );
        }

        /**
		 * Settings Page
		 * @since 2.2
		 * @version 1.0
		 */
		public function after_general_settings( $mycred = NULL ) 
        {
        	$settings = $this->br_social_share;

			?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="checkbox" style="padding-top: 4px;">
                            <label for="<?php echo $this->field_id( 'enable_open_badge_ss' ) ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_open_badge_ss' ) ?>" id="<?php echo $this->field_id( 'enable_open_badge_ss' ) ?>" <?php checked( $settings['enable_open_badge_ss'], 1 ); ?> value="1">Enable Open Badge Social Sharing</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="checkbox" style="padding-top: 4px;">
                            <label for="<?php echo $this->field_id( 'enable_fb' ) ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_fb' ) ?>" id="<?php echo $this->field_id( 'enable_fb' ) ?>" <?php checked( $settings['enable_fb'], 1 ); ?> value="1">Show Facebook button</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox" style="padding-top: 4px;">
                            <label for="<?php echo $this->field_id( 'enable_twitter' ) ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_twitter' ) ?>" id="<?php echo $this->field_id( 'enable_twitter' ) ?>" <?php checked( $settings['enable_twitter'], 1 ); ?> value="1">Show Twitter button</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox" style="padding-top: 4px;">
                            <label for="<?php echo $this->field_id( 'enable_li' ) ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_li' ) ?>" id="<?php echo $this->field_id( 'enable_li' ) ?>" <?php checked( $settings['enable_li'], 1 ); ?> value="1">Show LinkedIn button</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox" style="padding-top: 4px;">
                            <label for="<?php echo $this->field_id( 'enable_pt' ) ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_pt' ) ?>" id="<?php echo $this->field_id( 'enable_pt' ) ?>" <?php checked( $settings['enable_pt'], 1 ); ?> value="1">Show Pinterest button</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h3>Social Sharing button style</h3>
                    <div class="clearfix">
                        <input type="radio" name="<?php echo $this->field_name( 'button_style' ) ?>" <?php echo $this->is_checked( 'button_style' ) ? 'checked' : ''; ?> value="button_style">Button Style<br>
                        <button class="mycred-social-icons mycred-social-icon-facebook"><a href="javascript:void(0)">facebook</a></button>
                        <button class="mycred-social-icons mycred-social-icon-twitter"><a href="javascript:void(0)">twitter</a></button>
                        <button class="mycred-social-icons mycred-social-icon-linkedin"><a href="javascript:void(0)">linkedin</a></button>
                        <button class="mycred-social-icons mycred-social-icon-pinterest"><a href="javascript:void(0)">pinterest</a></button>
                    </div>
                    <div class="clearfix">
                        <input type="radio" name="<?php echo $this->field_name( 'button_style' ) ?>" <?php echo $this->is_checked( 'icon_style' ) ? 'checked' : ''; ?> value="icon_style">Icon Style<br>
                        <a href="javascript:void(0)" class="mycred-social-icons mycred-social-icon-facebook"></a>
                        <a href="javascript:void(0)" class="mycred-social-icons mycred-social-icon-twitter"></a>
                        <a href="javascript:void(0)" class="mycred-social-icons mycred-social-icon-linkedin"></a>
                        <a href="javascript:void(0)" class="mycred-social-icons mycred-social-icon-pinterest"></a>
                    </div>
                    <div class="clearfix">
                        <input type="radio" name="<?php echo  $this->field_name( 'button_style' ) ?>" <?php echo $this->is_checked( 'text_style' ) ? 'checked' : ''; ?> value="text_style">Text Style<br>
                        <button class="facebook social-text"><a href="javascript:void(0)">facebook</a></button>
                        <button class="twitter social-text"><a href="javascript:void(0)">twitter</a></button>
                        <button class="linkedin social-text"><a href="javascript:void(0)">linkedin</a></button>
                        <button class="pinterest social-text"><a href="javascript:void(0)">pinterest</a></button>
                    </div>
                </div>
            </div>
			<?php
        }

        public function is_checked( $key )
        {
            if( $this->br_social_share['button_style'] == $key )
                return true;
            return false;
        }

        public function sanitize_extra_settings( $new_data, $data, $core )
        { 	
        	$new_data['br_social_share']['enable_open_badge_ss'] = ( isset( $data['br_social_share']['enable_open_badge_ss'] ) ) ? $data['br_social_share']['enable_open_badge_ss'] : '0';

        	$new_data['br_social_share']['button_style'] = ( isset( $data['br_social_share']['button_style'] ) ) ? $data['br_social_share']['button_style'] : 'button_style';

            $new_data['br_social_share']['enable_fb'] = ( isset( $data['br_social_share']['enable_fb'] ) ) ? $data['br_social_share']['enable_fb'] : '0';

            $new_data['br_social_share']['enable_twitter'] = ( isset( $data['br_social_share']['enable_twitter'] ) ) ? $data['br_social_share']['enable_twitter'] : '0';
     
            $new_data['br_social_share']['enable_li'] = ( isset( $data['br_social_share']['enable_li'] ) ) ? $data['br_social_share']['enable_li'] : '0';
         
            $new_data['br_social_share']['enable_pt'] = ( isset( $data['br_social_share']['enable_pt'] ) ) ? $data['br_social_share']['enable_pt'] : '0';
       
            return apply_filters( 'mycred_brss_sanitize_settings', $new_data, $data, $core );
        }
    }
endif;

add_action( 'wp_loaded', 'mycred_load_br_social_share');

if( !function_exists( 'mycred_load_br_social_share' ) ):
function mycred_load_br_social_share()
{
    //Activate only if badges active
    if( class_exists( 'myCRED_Badge' ) )
    {
        $mycred_open_badge_socail_share = new myCRED_Module_BR_Social_Share();
        $mycred_open_badge_socail_share->load();
    }
}
endif;

