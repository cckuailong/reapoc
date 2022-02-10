<?php
if ( !class_exists( 'myCred_Uninstall_Settings' ) ):
    class myCred_Uninstall_Settings
    {
        private static $_instance;

        /**
         * myCred_Uninstall_Settings constructor.
         * @since 2.1.1
         * @version 1.0
         */
        public function __construct()
        {
            add_action( 'mycred_after_core_prefs', array( $this, 'uninstall_settings' ) );
            add_filter( 'mycred_save_core_prefs',  array( $this, 'sanitize_extra_settings' ), 10, 3 );
        }

        /**
         * @return mixed
         * @since 2.1.1
         * @version 1.0
         */
        public static function get_instance()
        {
            if ( self::$_instance == null )
                self::$_instance = new self();

            return self::$_instance;
        }

        /**
         * Generates Uninstall Menu In myCred Settings
         * @since 2.1.1
         * @version 1.0
         */
        public function uninstall_settings()
        {
            ?>
            <h4>
                <span class="dashicons dashicons-trash"></span>
                Uninstall
            </h4>
            <div class="body" style="display:none;">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label>Remove</label>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-types"><input type="checkbox" name="mycred_pref_core[uninstall][types]" <?php echo $this->checked( 'types' ) ? 'checked' : ''; ?> id="mycred-uninstall-types" value="1"> All Point Types</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-logs"><input type="checkbox" name="mycred_pref_core[uninstall][logs]" <?php echo $this->checked( 'logs' ) ? 'checked' : ''; ?> id="mycred-uninstall-logs" value="1"> All Logs</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-users-data"><input type="checkbox" name="mycred_pref_core[uninstall][users]" <?php echo $this->checked( 'users' ) ? 'checked' : ''; ?> id="mycred-uninstall-users-data" value="1"> All Users' Data</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-addon-settings"><input type="checkbox" name="mycred_pref_core[uninstall][addon]" <?php echo $this->checked( 'addon' ) ? 'checked' : ''; ?> id="mycred-uninstall-addon-settings" value="1"> All Addon's Settings</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-hooks"><input type="checkbox" name="mycred_pref_core[uninstall][hooks]" <?php echo $this->checked( 'hooks' ) ? 'checked' : ''; ?> id="mycred-uninstall-hooks" value="1"> All Hook's Settings</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-badges"><input type="checkbox" name="mycred_pref_core[uninstall][badges]" <?php echo $this->checked( 'badges' ) ? 'checked' : ''; ?> id="mycred-uninstall-badges" value="1"> All Badges</label>
                            </div>
                            <div class="radio" style="margin: 10px 0;">
                                <label for="mycred-uninstall-ranks"><input type="checkbox" name="mycred_pref_core[uninstall][ranks]" <?php echo $this->checked( 'ranks' ) ? 'checked' : ''; ?> id="mycred-uninstall-ranks" value="1"> All Ranks</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


        /**
         * Checks the checkbox if already checked
         * @param $key
         * @return bool
         * @since 2.1.1
         * @version 1.0
         */
        public function checked( $key )
        {
            $hooks = mycred_get_option( 'mycred_pref_core', false );

            if ( is_array( $hooks ) && in_array( $key, $hooks ) )
                if ( array_key_exists( 'uninstall', $hooks ) && array_key_exists( $key, $hooks['uninstall'] ) && $hooks['uninstall'][$key] == 1 )
                    return true;

            return false;
        }

        /**
         * Sanitizes and saves settings
         * @param $new_data
         * @param $data
         * @param $core
         * @return mixed
         * @since 2.1.1
         * @version 1.0
         */
        public function sanitize_extra_settings($new_data, $data, $core )
        {
            if( array_key_exists( 'uninstall', $data ) )
            {
                $new_data['uninstall']['types'] = ( isset( $data['uninstall']['types'] ) ) ? sanitize_text_field( $data['uninstall']['types'] ) : 0;
                $new_data['uninstall']['logs'] = ( isset( $data['uninstall']['logs'] ) ) ? sanitize_text_field( $data['uninstall']['logs'] ) : 0;
                $new_data['uninstall']['users'] = ( isset( $data['uninstall']['users'] ) ) ? sanitize_text_field( $data['uninstall']['users'] ) : 0 ;
                $new_data['uninstall']['addon'] = ( isset( $data['uninstall']['addon'] ) ) ? sanitize_text_field( $data['uninstall']['addon'] ) : 0 ;
                $new_data['uninstall']['hooks'] = ( isset( $data['uninstall']['hooks'] ) ) ? sanitize_text_field( $data['uninstall']['hooks'] ) : 0 ;
                $new_data['uninstall']['badges'] = ( isset( $data['uninstall']['badges'] ) ) ? sanitize_text_field( $data['uninstall']['badges'] ) : 0 ;
                $new_data['uninstall']['ranks'] = ( isset( $data['uninstall']['ranks'] ) ) ? sanitize_text_field( $data['uninstall']['ranks'] ) : 0 ;
            }

            return $new_data;
        }
    }
endif;

myCred_Uninstall_Settings::get_instance();