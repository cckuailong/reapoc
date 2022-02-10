<?php

/**
 * Handles form generaton and posting for add/edit post in frontend
 */
class WPUF_Render_Form {
    public static $meta_key            = 'wpuf_form';

    public static $separator           = ' | ';

    public static $config_id           = '_wpuf_form_id';

    private $form_condition_key = 'wpuf_cond';

    private static $_instance;

    private $field_count = 0;

    public $multiform_start = 0;

    public static function init() {
        if ( !self::$_instance ) {
            self::$_instance = new WPUF_Render_Form();
        }

        return self::$_instance;
    }

    /**
     * Send json error message
     *
     * @param string $error
     */
    public function send_error( $error ) {
        echo json_encode( [
            'success' => false,
            'error'   => $error,
        ] );

        die();
    }

    /**
     * Search on multi dimentional array
     *
     * @param array  $array
     * @param string $key   name of key
     * @param string $value the value to search
     *
     * @return array
     */
    public function search( $array, $key, $value ) {
        $results = [];

        if ( is_array( $array ) ) {
            if ( isset( $array[$key] ) && $array[$key] == $value ) {
                $results[] = $array;
            }

            foreach ( $array as $subarray ) {
                $results = array_merge( $results, $this->search( $subarray, $key, $value ) );
            }
        }

        return $results;
    }

    /**
     * Really simple captcha validation
     *
     * @return void
     */
    public function validate_rs_captcha() {
        $nonce = isset( $_REQUEST['wpuf-login-nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['wpuf-login-nonce'] ) ) : '';

        if ( isset( $nonce) && ! wp_verify_nonce( $nonce, 'wpuf_login_action' ) ) {
            return ;
        }

        $rs_captcha_input = isset( $_POST['rs_captcha'] ) ? sanitize_text_field( wp_unslash( $_POST['rs_captcha'] ) ) : '';
        $rs_captcha_file  = isset( $_POST['rs_captcha_val'] ) ? sanitize_text_field( wp_unslash( $_POST['rs_captcha_val'] ) ) : '';

        if ( class_exists( 'ReallySimpleCaptcha' ) ) {
            $captcha_instance = new ReallySimpleCaptcha();

            if ( !$captcha_instance->check( $rs_captcha_file, $rs_captcha_input ) ) {
                $this->send_error( __( 'Really Simple Captcha validation failed', 'wp-user-frontend' ) );
            } else {
                // validation success, remove the files
                $captcha_instance->remove( $rs_captcha_file );
            }
        }
    }

    /**
     * reCaptcha Validation
     *
     * @return void
     */
    public function validate_re_captcha( $no_captcha = '', $invisible = '' ) {
        $nonce = isset( $_REQUEST['wpuf-login-nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['wpuf-login-nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf_login_action' ) ) {
            return ;
        }
        // need to check if invisible reCaptcha need library or we can do it here.
        // ref: https://shareurcodes.com/blog/google%20invisible%20recaptcha%20integration%20with%20php
        $site_key             = wpuf_get_option( 'recaptcha_public', 'wpuf_general' );
        $private_key          = wpuf_get_option( 'recaptcha_private', 'wpuf_general' );
        $rremote_addr         = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $g_recaptcha_response = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

        if ( $no_captcha == 1 && 0 == $invisible ) {
            if ( !class_exists( 'WPUF_ReCaptcha' ) ) {
                require_once WPUF_ROOT . '/lib/recaptchalib_noCaptcha.php';
            }

            $response  = null;
            $reCaptcha = new WPUF_ReCaptcha( $private_key );

            $resp = $reCaptcha->verifyResponse(
                $rremote_addr,
                $g_recaptcha_response
             );

            if ( !$resp->success ) {
                $this->send_error( __( 'noCaptcha reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        } elseif ( $no_captcha == 0 && 0 == $invisible  ) {
            $recap_challenge = isset( $_POST['recaptcha_challenge_field'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_challenge_field'] ) ) : '';
            $recap_response  = isset( $_POST['recaptcha_response_field'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_response_field'] ) ) : '';

            $resp            = recaptcha_check_answer( $private_key, $rremote_addr, $recap_challenge, $recap_response );

            if ( !$resp->is_valid ) {
                $this->send_error( __( 'reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        } elseif ( $no_captcha == 0 && 1 == $invisible ) {
            $response  = null;
            $recaptcha = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
            $object    = new Invisible_Recaptcha( $site_key, $private_key );

            $response  = $object->verifyResponse( $recaptcha );

            if ( isset( $response['success'] ) and $response['success'] != true ) {
                $this->send_error( __( 'Invisible reCAPTCHA validation failed', 'wp-user-frontend' ) );
            }
        }
    }

    /**
     * Guess a suitable username for registration based on email address
     *
     * @param string $email email address
     *
     * @return string username
     */
    public function guess_username( $email ) {
        // username from email address
        $username = sanitize_user( substr( $email, 0, strpos( $email, '@' ) ) );

        if ( !username_exists( $username ) ) {
            return $username;
        }

        // try to add some random number in username
        // and may be we got our username
        $username .= rand( 1, 199 );

        if ( !username_exists( $username ) ) {
            return $username;
        }
    }

    /**
     * Get input meta fields separated as post vars, taxonomy and meta vars
     *
     * @param int $form_id form id
     *
     * @return array
     */
    public static function get_input_fields( $form_id ) {
        $form_vars    = wpuf_get_form_fields( $form_id );

        $ignore_lists = ['section_break', 'html'];
        $post_vars    = $meta_vars = $taxonomy_vars = [];

        foreach ( $form_vars as $key => $value ) {

            // ignore section break and HTML input type
            if ( in_array( $value['input_type'], $ignore_lists ) ) {
                continue;
            }

            //separate the post and custom fields
            if ( isset( $value['is_meta'] ) && $value['is_meta'] == 'yes' ) {
                $meta_vars[] = $value;
                continue;
            }

            if ( $value['input_type'] == 'taxonomy' ) {

                // don't add "category"
                if ( $value['name'] == 'category' ) {
                    continue;
                }

                $taxonomy_vars[] = $value;
            } else {
                $post_vars[] = $value;
            }
        }

        return [$post_vars, $taxonomy_vars, $meta_vars];
    }

    public static function prepare_meta_fields( $meta_vars ) {
        // loop through custom fields
        // skip files, put in a key => value paired array for later executation
        // process repeatable fields separately
        // if the input is array type, implode with separator in a field
        check_ajax_referer( 'wpuf_form_add' );

        $files          = [];
        $meta_key_value = [];
        $multi_repeated = []; //multi repeated fields will in sotre duplicated meta key

        foreach ( $meta_vars as $key => $value ) {
            $value_name = isset( $_POST[$value['name']] ) ? sanitize_text_field( wp_unslash( $_POST[$value['name']] ) ) : '';
            if ( isset( $_POST['wpuf_files'][$value['name']] ) ) {
                $wpuf_files = isset( $_POST['wpuf_files'] ) ? sanitize_text_field( wp_unslash( $_POST['wpuf_files'][$value['name']] ) ) : [];
            } else {
                $wpuf_files = [];
            }
            switch ( $value['input_type'] ) {

                // put files in a separate array, we'll process it later
                case 'file_upload':
                case 'image_upload':

                    $files[] = [
                        'name'  => $value['name'],
                        // 'value' => isset( $wpuf_files[$value['name']] ) ? $wpuf_files[$value['name']] : [],
                        'value' => isset( $wpuf_files ) ? $wpuf_files : [],
                        'count' => $value['count'],
                    ];
                    break;

                case 'repeat':

                    // if it is a multi column repeat field
                    if ( isset( $value['multiple'] ) && $value['multiple'] == 'true' ) {

                        // if there's any items in the array, process it
                        if ( $value_name ) {
                            $ref_arr = [];
                            $cols    = count( $value['columns'] );
                            $first   = array_shift( array_values( $value_name ) ); //first element
                            $rows    = count( $first );

                            // loop through columns
                            for ( $i = 0; $i < $rows; $i++ ) {

                                // loop through the rows and store in a temp array
                                $temp = [];

                                for ( $j = 0; $j < $cols; $j++ ) {
                                    $temp[] = $value_name[$j][$i];
                                }

                                // store all fields in a row with self::$separator separated
                                $ref_arr[] = implode( self::$separator, $temp );
                            }

                            // now, if we found anything in $ref_arr, store to $multi_repeated
                            if ( $ref_arr ) {
                                $multi_repeated[$value['name']] = array_slice( $ref_arr, 0, $rows );
                            }
                        }
                    } else {
                        $meta_key_value[$value['name']] = implode( self::$separator, $value_name );
                    }

                    break;

                case 'address':

                    if ( is_array( $value_name ) ) {
                        foreach ( $value_name as $address_field => $field_value ) {
                            $meta_key_value[ $value['name'] ][ $address_field ] = sanitize_text_field( $field_value );
                        }
                    }

                    break;

                case 'text':
                case 'email':
                case 'number':
                case 'date':

                    $meta_key_value[$value['name']] = sanitize_text_field( trim( $value_name ) );

                    break;

                case 'textarea':

                    $meta_key_value[$value['name']] = wp_kses_post( $value_name );

                    break;

                case 'map':
                    $data           = [];
                    $map_field_data = sanitize_text_field( trim( $value_name ) );

                    if ( !empty( $map_field_data ) ) {
                        list( $data['address'], $data['lat'], $data['lng'] ) = explode( ' || ', $map_field_data );
                        $meta_key_value[$value['name']]                      = $data;
                    }
                    break;

                default:
                    // if it's an array, implode with this->separator
                    if ( is_array( $value_name ) ) {
                        $acf_compatibility = wpuf_get_option( 'wpuf_compatibility_acf', 'wpuf_general', 'no' );

                        if ( $value['input_type'] == 'address' ) {
                            $meta_key_value[$value['name']] = $value_name;
                        } elseif ( !empty( $acf_compatibility ) && $acf_compatibility == 'yes' ) {
                            $meta_key_value[$value['name']] = maybe_serialize( $value_name );
                        } else {
                            $meta_key_value[$value['name']] = implode( self::$separator, $value_name );
                        }
                    } else {
                        $meta_key_value[$value['name']] = trim( $value_name );
                    }

                    break;
            }
        } //end foreach

        return [$meta_key_value, $multi_repeated, $files];
    }

    public function guest_fields( $form_settings ) {
        ?>
        <li class="el-name">
            <div class="wpuf-label">
                <label><?php echo esc_attr( $form_settings['name_label'] ); ?> <span class="required">*</span></label>
            </div>

            <div class="wpuf-fields">
                <input type="text" required="required" data-required="yes" data-type="text" name="guest_name" value="" size="40">
            </div>
        </li>

        <li class="el-email">
            <div class="wpuf-label">
                <label><?php echo esc_attr( $form_settings['email_label'] ); ?> <span class="required">*</span></label>
            </div>

            <div class="wpuf-fields">
                <input type="email" required="required" data-required="yes" data-type="email" name="guest_email" value="" size="40">
            </div>
        </li>
        <?php
    }

    /**
     * Handles the add post shortcode
     *
     * @param int $form_id
     * @param int $post_id
     */
    public function render_form( $form_id, $post_id = null ) {
        $form_status = get_post_status( $form_id );

        if ( !$form_status ) {
            echo wp_kses_post( '<div class="wpuf-message">' . __( 'Your selected form is no longer available.', 'wp-user-frontend' ) . '</div>' );

            return;
        }

        if ( $form_status != 'publish' ) {
            echo wp_kses_post( '<div class="wpuf-message">' . __( "Please make sure you've published your form.", 'wp-user-frontend' ) . '</div>' );

            return;
        }

        $form_vars       = wpuf_get_form_fields( $form_id );
        $form_settings   = wpuf_get_form_settings( $form_id );
        $label_position  = isset( $form_settings['label_position'] ) ? $form_settings['label_position'] : 'left';
        $layout          = isset( $form_settings['form_layout'] ) ? $form_settings['form_layout'] : 'layout1';
        $theme_css       = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-style';

        do_action( 'wpuf_before_form_render', $form_id );

        if ( !empty( $layout ) ) {
            wp_enqueue_style( 'wpuf-' . $layout );
        }

        if ( !is_user_logged_in() && $form_settings['guest_post'] != 'true' ) {
            echo wp_kses_post( '<div class="wpuf-message">' . $form_settings['message_restrict'] . '</div>' );

            return;
        }

        if ( $form_vars ) {
            ?>
            <form class="wpuf-form-add wpuf-form-<?php echo esc_attr( $layout ); ?> <?php echo ( $layout == 'layout1' ) ? esc_attr( $theme_css ) : 'wpuf-style'; ?>" action="" method="post">

                <ul class="wpuf-form form-label-<?php echo esc_attr( $label_position ); ?>">

                    <?php
                    if ( !$post_id ) {
                        do_action( 'wpuf_add_post_form_top', $form_id, $form_settings );
                    } else {
                        do_action( 'wpuf_edit_post_form_top', $form_id, $post_id, $form_settings );
                    }

            if ( !is_user_logged_in() && $form_settings['guest_post'] == 'true' && $form_settings['guest_details'] == 'true' ) {
                $this->guest_fields( $form_settings );
            }

            $this->render_items( $form_vars, $post_id, 'post', $form_id, $form_settings );
            $this->submit_button( $form_id, $form_settings, $post_id );

            if ( !$post_id ) {
                do_action( 'wpuf_add_post_form_bottom', $form_id, $form_settings );
            } else {
                do_action( 'wpuf_edit_post_form_bottom', $form_id, $post_id, $form_settings );
            } ?>

                </ul>

            </form>

            <?php
        } //endif
        do_action( 'wpuf_after_form_render', $form_id );
    }

    public function render_item_before( $form_field, $post_id ) {
        $label_exclude = ['section_break', 'html', 'action_hook', 'toc', 'shortcode'];
        $el_name       = !empty( $form_field['name'] ) ? $form_field['name'] : '';
        $class_name    = !empty( $form_field['css'] ) ? ' ' . $form_field['css'] : '';
        $field_size    = !empty( $form_field['width'] ) ? ' field-size-' . $form_field['width'] : '';

        printf( '<li class="wpuf-el %s%s%s" data-label="%s">', esc_attr( $el_name ), esc_attr( $class_name ), esc_attr( $field_size ),esc_attr(  $form_field['label'] ) );

        if ( isset( $form_field['input_type'] ) && !in_array( $form_field['input_type'], $label_exclude ) ) {
            $this->label( $form_field, $post_id );
        }
    }

    public function render_item_after( $form_field ) {
        echo wp_kses_post( '</li>' );
    }

    public function conditional_logic( $form_field, $form_id ) {
        $cond_inputs                     = $form_field['wpuf_cond'];
        $cond_inputs['condition_status'] = isset( $cond_inputs['condition_status'] ) ? $cond_inputs['condition_status'] : '';

        if ( $cond_inputs['condition_status'] == 'yes' ) {
            $cond_inputs['type']    = $form_field['input_type'];
            $cond_inputs['name']    = $form_field['name'];
            $cond_inputs['form_id'] = $form_id;
            $condition              = json_encode( $cond_inputs );
        } else {
            $condition = '';
        }

        //taxnomy name create unique
        if ( $form_field['input_type'] == 'taxonomy' ) {
            $cond_inputs['name'] = $form_field['name'] . '_' . $form_field['type'] . '_' . $form_field['id'];
            $condition           = json_encode( $cond_inputs );
        }

        //for section break
        if ( $form_field['input_type'] == 'section_break' ) {
            $cond_inputs['name'] = $form_field['name'] . '_' . $form_field['id'];
            $condition           = json_encode( $cond_inputs );
        } ?>
        <script type="text/javascript">
            wpuf_conditional_items.push(<?php echo esc_html( $condition ); ?>);
        </script>
        <?php
    }

    /**
     * Render form items
     *
     * @param array    $form_vars
     * @param int|null $post_id
     * @param string   $type      type of the form. post or user
     */
    public function render_items( $form_vars, $post_id, $type = 'post', $form_id, $form_settings, $cond_inputs = [] ) {
        $edit_ignore   = [ 'really_simple_captcha' ];
        $hidden_fields = []; ?>
        <script type="text/javascript">
            if ( typeof wpuf_conditional_items === 'undefined' ) {
                wpuf_conditional_items = [];
            }

            if ( typeof wpuf_plupload_items === 'undefined' ) {
                wpuf_plupload_items = [];
            }

            if ( typeof wpuf_map_items === 'undefined' ) {
                wpuf_map_items = [];
            }
        </script>
        <?php

        //through var, we will know if multiform step started already
        //$multiform_start = 0;

        //if multistep form is enabled
        if ( isset( $form_settings['enable_multistep'] ) && $form_settings['enable_multistep'] == 'yes' ) {
            $ms_ac_txt_color   = isset( $form_settings['ms_ac_txt_color'] ) ? $form_settings['ms_ac_txt_color'] : '#ffffff';
            $ms_active_bgcolor = isset( $form_settings['ms_active_bgcolor'] ) ? $form_settings['ms_active_bgcolor'] : '#00a0d2';
            $ms_bgcolor        = isset( $form_settings['ms_bgcolor'] ) ? $form_settings['ms_bgcolor'] : '#E4E4E4'; ?>
            <style type="text/css">
                .wpuf-form .wpuf-multistep-progressbar ul.wpuf-step-wizard li,
                .wpuf-form .wpuf-multistep-progressbar.ui-progressbar {
                    background-color:  <?php echo esc_html( $ms_bgcolor ); ?>;
                    background:  <?php echo esc_html( $ms_bgcolor ); ?>;
                }
                .wpuf-form .wpuf-multistep-progressbar ul.wpuf-step-wizard li::after{
                    border-left-color: <?php echo esc_html( $ms_bgcolor ); ?>;
                }
                .wpuf-form .wpuf-multistep-progressbar ul.wpuf-step-wizard li.active-step,
                .wpuf-form .wpuf-multistep-progressbar .ui-widget-header{
                    color: <?php echo esc_html( $ms_ac_txt_color ); ?>;
                    background-color:  <?php echo esc_html( $ms_active_bgcolor ); ?>;
                }
                .wpuf-form .wpuf-multistep-progressbar ul.wpuf-step-wizard li.active-step::after {
                    border-left-color: <?php echo esc_html( $ms_active_bgcolor ); ?>;
                }
                .wpuf-form .wpuf-multistep-progressbar.ui-progressbar .wpuf-progress-percentage{
                    color: <?php echo esc_html( $ms_ac_txt_color ); ?>;
                }
            </style>
            <input type="hidden" name="wpuf_multistep_type" value="<?php echo esc_attr( $form_settings['multistep_progressbar_type'] ); ?>"/>
            <?php
            if ( $form_settings['multistep_progressbar_type'] == 'step_by_step' ) {
                ?>
                <!--wpuf-multistep-progressbar-> wpuf_ms_pb-->
                <div class="wpuf-multistep-progressbar">

                </div>
            <?php
            } else {
                ?>
                <div class="wpuf-multistep-progressbar">

                </div>
            <?php
            }
        }

        foreach ( $form_vars as $key => $form_field ) {

            // check field visibility options
            if ( array_key_exists( 'wpuf_visibility', $form_field ) ) {
                $visibility_selected = $form_field['wpuf_visibility']['selected'];
                $visibility_choices  = $form_field['wpuf_visibility']['choices'];
                $show_field          = false;

                if ( $visibility_selected == 'everyone' ) {
                    $show_field = true;
                }

                if ( $visibility_selected == 'hidden' ) {
                    $form_field['css'] .= ' wpuf_hidden_field';
                    $show_field = true;
                }

                if ( $visibility_selected == 'logged_in' && is_user_logged_in() ) {
                    if ( empty( $visibility_choices ) ) {
                        $show_field = true;
                    } else {
                        foreach ( $visibility_choices as $key => $choice ) {
                            if ( current_user_can( $choice ) ) {
                                $show_field = true;
                                break;
                            }
                            continue;
                        }
                    }
                }

                if ( $visibility_selected == 'subscribed_users' && is_user_logged_in() ) {
                    $user_pack  = WPUF_Subscription::init()->get_user_pack( get_current_user_id() );

                    if ( empty( $visibility_choices ) && !empty( $user_pack ) ) {
                        $show_field = true;
                    } elseif ( !empty( $user_pack ) && !empty( $visibility_choices ) ) {
                        foreach ( $visibility_choices as $pack => $id ) {
                            if ( $user_pack['pack_id'] == $id ) {
                                $show_field = true;
                                break;
                            }
                            continue;
                        }
                    }
                }

                if ( !$show_field ) {
                    continue;
                }
            }

            // don't show captcha in edit page
            if ( $post_id && in_array( $form_field['input_type'], $edit_ignore ) ) {
                continue;
            }

            // igonre the hidden fields
            if ( $form_field['input_type'] == 'hidden' ) {
                $hidden_fields[] = $form_field;
                continue;
            }

            if ( $form_field['input_type'] != 'step_start' && $form_field['input_type'] != 'step_end' ) {
                $this->render_item_before( $form_field, $post_id );
            }

            $this->field_count++;

            switch ( $form_field['input_type'] ) {
                case 'text':
                    $this->text( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'textarea':
                    $this->textarea( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'select':
                    $this->select( $form_field, false, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'multiselect':
                    $this->select( $form_field, true, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'radio':
                    $this->radio( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'checkbox':
                    $this->checkbox( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'url':
                    $this->url( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'email':
                    $this->email( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'password':
                    $this->password( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'taxonomy':

                    $this->taxonomy( $form_field, $post_id, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'section_break':
                    $form_field['name'] = 'section_break';
                    $this->section_break( $form_field, $post_id, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'html':
                    $form_field['name'] = 'custom_html_' . str_replace( ' ', '_', $form_field['label'] );

                    $this->html( $form_field, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'image_upload':
                    $this->image_upload( $form_field, $post_id, $type, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                case 'recaptcha':
                    $this->recaptcha( $form_field, $post_id, $form_id );
                    $this->conditional_logic( $form_field, $form_id );
                    break;

                default:

                    // fallback for a dynamic method of this class if exists
                    $dynamic_method = 'field_' . $form_field['input_type'];

                    if ( method_exists( $this, $dynamic_method ) ) {
                        $this->{$dynamic_method}( $form_field, $post_id, $type, $form_id );
                    }

                    do_action( 'wpuf_render_form_' . $form_field['input_type'], $form_field, $form_id, $post_id, $form_settings );
                    do_action( 'wpuf_render_pro_' . $form_field['input_type'], $form_field, $post_id, $type, $form_id, $form_settings, 'WPUF_Render_Form', $this, $this->multiform_start, isset( $form_settings['enable_multistep'] ) ? $form_settings['enable_multistep'] : '' );
                    break;
            }

            $this->render_item_after( $form_field );
        } //end foreach

        if ( $hidden_fields ) {
            foreach ( $hidden_fields as $field ) {
                printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $field['name'] ), esc_attr( $field['meta_value'] ) );
                echo "\r\n";
            }
        }
    }

    public function submit_button( $form_id, $form_settings, $post_id ) {
        ?>
        <li class="wpuf-submit">
            <div class="wpuf-label">
                &nbsp;
            </div>

            <?php wp_nonce_field( 'wpuf_form_add' ); ?>
            <input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
            <input type="hidden" name="page_id" value="<?php echo get_post() ? esc_attr( get_the_ID() ) : '0'; ?>">
            <input type="hidden" id="del_attach" name="delete_attachments[]">
            <input type="hidden" name="action" value="wpuf_submit_post">

            <?php
            if ( $post_id ) {
                $cur_post = get_post( $post_id ); ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                <input type="hidden" name="post_date" value="<?php echo esc_attr( $cur_post->post_date ); ?>">
                <input type="hidden" name="comment_status" value="<?php echo esc_attr( $cur_post->comment_status ); ?>">
                <input type="hidden" name="post_author" value="<?php echo esc_attr( $cur_post->post_author ); ?>">
                <input type="submit" class="wpuf-submit-button" name="submit" value="<?php echo esc_attr( $form_settings['update_text'] ); ?>" />
            <?php
            } else { ?>
                <input type="submit" class="wpuf-submit-button" name="submit" value="<?php echo esc_attr( $form_settings['submit_text'] ); ?>" />
                <input type="hidden" name="wpuf_form_status" value="new">
            <?php } ?>

            <?php if ( isset( $form_settings['draft_post'] ) && $form_settings['draft_post'] == 'true' ) { ?>
                <a href="#" class="btn" id="wpuf-post-draft"><?php esc_html_e( 'Save Draft', 'wp-user-frontend' ); ?></a>
            <?php } ?>
        </li>
    <?php
    }

    /**
     * Form preview handler
     *
     * @return void
     */
    public function preview_form() {
        $form_id = isset( $_GET['form_id'] ) ? intval( wp_unslash( $_GET['form_id'] ) ) : 0;

        if ( $form_id ) {
            ?>

            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Form Preview</title>
                    <link rel="stylesheet" href="<?php echo esc_url( plugins_url( 'assets/css/frontend-forms.css', __DIR__ ) ); ?>">

                    <style type="text/css">
                        body {
                            margin: 0;
                            padding: 0;
                            background: #eee;
                        }

                        .container {
                            width: 700px;
                            margin: 0 auto;
                            margin-top: 20px;
                            padding: 20px;
                            background: #fff;
                            border: 1px solid #DFDFDF;
                            -webkit-box-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                            box-shadow: 1px 1px 2px rgba(0,0,0,0.1);
                        }
                    </style>

                    <script type="text/javascript" src="<?php echo esc_url( includes_url( 'js/jquery/jquery.js' ) ); ?>"></script>
                </head>
                <body>
                    <div class="container">
                        <?php $this->render_form( $form_id, null ); ?>
                    </div>
                </body>
            </html>

            <?php
        } else {
            wp_die( 'Error generating the form preview' );
        }

        exit;
    }

    /**
     * Prints required field asterisk
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_mark( $attr ) {
        if ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) {
            return ' <span class="required">*</span>';
        }
    }

    /**
     * Prints HTML5 required attribute
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_html5( $attr ) {
        if ( $attr['required'] == 'yes' ) {
            // echo ' required="required"';
        }
    }

    /**
     * Print required class name
     *
     * @param array $attr
     *
     * @return string
     */
    public function required_class( $attr ) {
        if ( $attr['required'] == 'yes' ) {
            echo ' required';
        }
    }

    /**
     * Prints form input label
     *
     * @param array $attr
     */
    public function label( $attr, $post_id = 0 ) {
        if ( $post_id && $attr['input_type'] == 'password' ) {
            $attr['required'] = 'no';
        }

        if ( isset( $attr['input_type'] ) && $attr['input_type'] == 'recaptcha' && $attr['recaptcha_type'] == 'invisible_recaptcha' ) {
            return;
        } ?>
        <div class="wpuf-label">
            <label for="<?php echo isset( $attr['name'] ) ? esc_attr( $attr['name'] ) : 'cls'; ?>"><?php echo esc_attr( $attr['label'] ) . esc_attr( $this->required_mark( $attr ) ); ?></label>
        </div>
        <?php
    }

    /**
     * Prints help text for a field
     *
     * @param array $attr
     */
    public function help_text( $attr ) {
        if ( empty( $attr['help'] ) ) {
            return;
        } ?>
        <span class="wpuf-help"><?php echo esc_html( stripslashes( $attr['help'] ) ); ?></span>
        <?php
    }

    /**
     * Check if its a meta field
     *
     * @param array $attr
     *
     * @return bool
     */
    public function is_meta( $attr ) {
        if ( isset( $attr['is_meta'] ) && $attr['is_meta'] == 'yes' ) {
            return true;
        }

        return false;
    }

    /**
     * Get a meta value
     *
     * @param int    $object_id user_ID or post_ID
     * @param string $meta_key
     * @param string $type      post or user
     * @param bool   $single
     *
     * @return string
     */
    public function get_meta( $object_id, $meta_key, $type = 'post', $single = true ) {
        if ( !$object_id ) {
            return '';
        }

        if ( $type == 'post' ) {
            return get_post_meta( $object_id, $meta_key, $single );
        }

        return get_user_meta( $object_id, $meta_key, $single );
    }

    public function get_user_data( $user_id, $field ) {
        return get_user_by( 'id', $user_id )->$field;
    }

    /**
     * Prints a text field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function text( $attr, $post_id, $type = 'post', $form_id = null ) {
        // checking for user profile username
        $username = false;
        $taxonomy = false;

        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type );
            } else {

                // applicable for post tags
                if ( $type == 'post' && $attr['name'] == 'tags' ) {
                    $post_tags = wp_get_post_tags( $post_id );
                    $tagsarray = [];

                    foreach ( $post_tags as $tag ) {
                        $tagsarray[] = $tag->name;
                    }

                    $value    = implode( ', ', $tagsarray );
                    $taxonomy = true;
                } elseif ( $type == 'post' ) {
                    $value = get_post_field( $attr['name'], $post_id );
                } elseif ( $type == 'user' ) {
                    $name  = $attr['name'];
                    $value = get_user_by( 'id', $post_id )->$name;

                    if ( $attr['name'] == 'user_login' ) {
                        $username = true;
                    }
                }
            }
        } else {
            $value = $attr['default'];

            if ( $type == 'post' && $attr['name'] == 'tags' ) {
                $taxonomy = true;
            }
        } ?>

        <div class="wpuf-fields">
            <input class="textfield<?php echo esc_attr( $this->required_class( $attr ) );
        echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" id="<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" type="text" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $attr['size'] ); ?>" <?php echo esc_attr( $username ) ? 'disabled' : ''; ?> />
            <span class="wpuf-wordlimit-message wpuf-help"></span>
            <?php $this->help_text( $attr ); ?>

            <?php if ( $taxonomy ) { ?>
            <script type="text/javascript">
                ;(function($) {
                    $(document).ready( function(){
                        $('li.tags input[name=tags]').suggest( wpuf_frontend.ajaxurl + '?action=wpuf-ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } );
                    });
                })(jQuery);
            </script>
            <?php } ?>
        </div>

        <?php
        if ( isset( $attr['word_restriction'] ) && $attr['word_restriction'] ) {
            $this->check_word_restriction_func( $attr['word_restriction'], 'no', $attr['name'] . '_' . $form_id );
        }
    }

    /**
     * Function to check word restriction
     *
     * @param $word_nums number of words allowed
     */
    public function check_word_restriction_func( $word_nums, $rich_text, $field_name ) {
        // bail out if it is dashboard
        if ( is_admin() ) {
            return;
        } ?>
        <script type="text/javascript">
            ;(function($) {
                $(document).ready( function(){
                    WP_User_Frontend.editorLimit.bind(<?php printf( '%d, "%s", "%s"', esc_attr( $word_nums ), esc_attr( $field_name ), esc_attr( $rich_text ) ); ?>);
                });
            })(jQuery);
        </script>
        <?php
    }

    /**
     * Prints a textarea field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function textarea( $attr, $post_id, $type, $form_id ) {
        $req_class = ( $attr['required'] == 'yes' ) ? 'required' : 'rich-editor';

        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type, true );
            } else {
                if ( $type == 'post' ) {
                    $value = get_post_field( $attr['name'], $post_id );
                } else {
                    $value = $this->get_user_data( $post_id, $attr['name'] );
                }
            }
        } else {
            $value = $attr['default'];
        } ?>

        <?php if ( in_array( $attr['rich'], [ 'yes', 'teeny' ] ) ) { ?>
            <div class="wpuf-fields wpuf-rich-validation <?php printf( 'wpuf_%s_%s', esc_attr( $attr['name'] ), esc_attr( $form_id ) ); ?>" data-type="rich" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-id="<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" data-name="<?php echo esc_attr( $attr['name'] ); ?>">
        <?php } else { ?>
            <div class="wpuf-fields">
        <?php } ?>

            <?php if ( isset( $attr['insert_image'] ) && $attr['insert_image'] == 'yes' ) { ?>
                <div id="wpuf-insert-image-container">
                    <a class="wpuf-button wpuf-insert-image" id="wpuf-insert-image_<?php echo esc_attr( $form_id ); ?>" href="#" data-form_id="<?php echo esc_attr( $form_id ); ?>">
                        <span class="wpuf-media-icon"></span>
                        <?php esc_html_e( 'Insert Photo', 'wp-user-frontend' ); ?>
                    </a>
                </div>

                <script type="text/javascript">
                    ;(function($) {
                        $(document).ready( function(){
                            WP_User_Frontend.insertImage('wpuf-insert-image_<?php echo esc_attr( $form_id ); ?>', '<?php echo esc_attr( $form_id ); ?>');
                        });
                    })(jQuery);
                </script>
            <?php } ?>

            <?php
            $form_settings = wpuf_get_form_settings( $form_id );
        $layout            = isset( $form_settings['form_layout'] ) ? $form_settings['form_layout'] : 'layout1';
        $textarea_id       = $attr['name'] ? $attr['name'] . '_' . $form_id : 'textarea_' . $this->field_count;
        $content_css       = includes_url() . 'js/tinymce/skins/wordpress/wp-content.css';

        if ( $attr['rich'] == 'yes' ) {
            $editor_settings = [
                    'textarea_rows' => $attr['rows'],
                    'quicktags'     => false,
                    'media_buttons' => false,
                    'editor_class'  => $req_class,
                    'textarea_name' => $attr['name'],
                    'tinymce'       => [
                        'content_css'   => $content_css . ', ' . WPUF_ASSET_URI . '/css/frontend-form/' . $layout . '.css',
                    ],
                ];

            $editor_settings = apply_filters( 'wpuf_textarea_editor_args', $editor_settings );
            wp_editor( $value, $textarea_id, $editor_settings );
        } elseif ( $attr['rich'] == 'teeny' ) {
            $editor_settings = [
                    'textarea_rows' => $attr['rows'],
                    'quicktags'     => false,
                    'media_buttons' => false,
                    'teeny'         => true,
                    'editor_class'  => $req_class,
                    'textarea_name' => $attr['name'],
                    'tinymce'       => [
                        'content_css'   => $content_css . ', ' . WPUF_ASSET_URI . '/css/frontend-form/' . $layout . '.css',
                    ],
                ];

            $editor_settings = apply_filters( 'wpuf_textarea_editor_args', $editor_settings );
            wp_editor( $value, $textarea_id, $editor_settings );
        } else {
            ?>
                <textarea class="textareafield<?php echo esc_attr( $this->required_class( $attr ) ); ?> <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" id="<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" name="<?php echo esc_attr( $attr['name'] ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="textarea"<?php $this->required_html5( $attr ); ?> placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" rows="<?php echo esc_attr( $attr['rows'] ); ?>" cols="<?php echo esc_attr( $attr['cols'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
                <span class="wpuf-wordlimit-message wpuf-help"></span>
            <?php
        } ?>
            <?php $this->help_text( $attr ); ?>
        </div>
        <?php

        if ( isset( $attr['word_restriction'] ) && $attr['word_restriction'] ) {
            $this->check_word_restriction_func( $attr['word_restriction'], $attr['rich'], $attr['name'] . '_' . $form_id );
        }
    }

    /**
     * Prints a select or multiselect field
     *
     * @param array    $attr
     * @param bool     $multiselect
     * @param int|null $post_id
     */
    public function select( $attr, $multiselect = false, $post_id, $type, $form_id = null ) {
        if ( $post_id ) {
            $selected = $this->get_meta( $post_id, $attr['name'], $type );

            if ( $multiselect ) {
                if ( is_serialized( $selected ) ) {
                    $selected = maybe_unserialize( $selected );
                } elseif ( is_array( $selected ) ) {
                    $selected = $selected;
                } else {
                    $selected = explode( self::$separator, $selected );
                }
            }
        } else {
            $selected = isset( $attr['selected'] ) ? $attr['selected'] : '';
            $selected = $multiselect ? ( is_array( $selected ) ? $selected : [] ) : $selected;
        }

        $name      = $multiselect ? $attr['name'] . '[]' : $attr['name'];
        $multi     = $multiselect ? ' multiple="multiple"' : '';
        $data_type = $multiselect ? 'multiselect' : 'select';
        $css       = $multiselect ? ' class="multiselect  wpuf_' . $attr['name'] . '_' . $form_id . '"' : ''; ?>

        <div class="wpuf-fields">
            <select <?php echo esc_attr( $css ); ?> class="<?php echo 'wpuf_' . esc_attr( $attr['name'] ) . '_' .esc_attr(  $form_id ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo esc_attr( $multi ); ?> data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="<?php echo esc_attr($data_type ); ?>"<?php esc_attr( $this->required_html5( $attr ) ); ?>>

                <?php if ( !empty( $attr['first'] ) ) { ?>
                    <option value=""><?php echo esc_attr( $attr['first'] ); ?></option>
                <?php } ?>

                <?php
                if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                    foreach ( $attr['options'] as $value => $option ) {
                        $current_select = $multiselect ? selected( in_array( $value, $selected ), true, false ) : selected( $selected, $value, false ); ?>
                        <option value="<?php echo esc_attr( $value ); ?>"<?php echo esc_attr( $current_select ); ?>><?php echo esc_html( $option ); ?></option>
                        <?php
                    }
                } ?>
            </select>
            <?php $this->help_text( $attr ); ?>
        </div>
        <?php
    }

    /**
     * Prints a radio field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function radio( $attr, $post_id, $type, $form_id ) {
        $selected = isset( $attr['selected'] ) ? $attr['selected'] : '';

        if ( $post_id ) {
            $selected = $this->get_meta( $post_id, $attr['name'], $type, true );
        } ?>

        <div class="wpuf-fields" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="radio">

            <?php
            if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                foreach ( $attr['options'] as $value => $option ) {
                    ?>

                    <label <?php echo $attr['inline'] == 'yes' ? 'class="wpuf-radio-inline"' : 'class="wpuf-radio-block"'; ?>>
                        <input name="<?php echo esc_attr( $attr['name'] ); ?>" class="<?php echo 'wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" type="radio" value="<?php echo esc_attr( $value ); ?>"<?php checked( $selected, $value ); ?> />
                        <?php echo esc_html( $option ); ?>
                    </label>
                    <?php
                }
            } ?>

            <?php $this->help_text( $attr ); ?>
        </div>

        <?php
    }

    /**
     * Prints a checkbox field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function checkbox( $attr, $post_id, $type, $form_id ) {
        $selected = isset( $attr['selected'] ) ? $attr['selected'] : [];

        if ( $post_id ) {
            if ( $value = $this->get_meta( $post_id, $attr['name'], $type, true ) ) {
                if ( is_serialized( $value ) ) {
                    $selected = maybe_unserialize( $value );
                } elseif ( is_array( $value ) ) {
                    $selected = $value;
                } else {
                    $selected = explode( self::$separator, $value );
                }
            }
        } ?>

        <div class="wpuf-fields" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="radio">

            <?php
            if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                foreach ( $attr['options'] as $value => $option ) {
                    ?>
                    <label <?php echo $attr['inline'] == 'yes' ? 'class="wpuf-checkbox-inline"' : 'class="wpuf-checkbox-block"'; ?>>
                        <input type="checkbox" class="<?php echo 'wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" name="<?php echo esc_attr( $attr['name'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected ) ? ' checked="checked"' : ''; ?> />
                        <?php echo esc_html( $option ); ?>
                    </label>
                    <?php
                }
            } ?>

            <?php $this->help_text( $attr ); ?>

        </div>

        <?php
    }

    /**
     * Prints a url field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function url( $attr, $post_id, $type, $form_id ) {
        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type, true );
            } else {
                //must be user profile url
                $value = $this->get_user_data( $post_id, $attr['name'] );
            }
        } else {
            $value = $attr['default'];
        } ?>

        <div class="wpuf-fields">
            <input id="wpuf-<?php echo esc_attr( $attr['name'] ); ?>" type="url" class="url <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $attr['size'] ); ?>" />
            <?php $this->help_text( $attr ); ?>
        </div>

        <?php
    }

    /**
     * Prints a email field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function email( $attr, $post_id, $type = 'post', $form_id ) {
        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type, true );
            } else {
                //must be user email
                $value = $this->get_user_data( $post_id, $attr['name'] );
            }
        } else {
            $value = $attr['default'];
        } ?>

        <div class="wpuf-fields">
            <input id="wpuf-<?php echo esc_attr( $attr['name'] ); ?>" type="email" class="email <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="email" <?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $attr['size'] ); ?>" />
            <?php $this->help_text( $attr ); ?>
        </div>

        <?php
    }

    /**
     * Prints a email field
     *
     * @param array $attr
     */
    public function password( $attr, $post_id, $type, $form_id ) {
        if ( $post_id ) {
            $attr['required'] = 'no';
        }

        $repeat_pass   = ( $attr['repeat_pass'] == 'yes' ) ? true : false;
        $pass_strength = ( $attr['pass_strength'] == 'yes' ) ? true : false; ?>

        <div class="wpuf-fields">
            <input id="<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_1'; ?>" type="password" class="password <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="password"<?php $this->required_html5( $attr ); ?> data-repeat="<?php echo esc_attr( $repeat_pass ) ? 'true' : 'false'; ?>" name="pass1" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="" size="<?php echo esc_attr( $attr['size'] ); ?>" />
            <?php $this->help_text( $attr ); ?>
        </div>

        <?php
        if ( $repeat_pass ) {
            $field_size    = !empty( $attr['width'] ) ? ' field-size-' . $attr['width'] : '';

            echo wp_kses_post( '</li>' );
            echo wp_kses_post( '<li class="wpuf-el password-repeat ' . $field_size . '" data-label="' . esc_attr( __( 'Confirm Password', 'wp-user-frontend' ) ) . '">' );

            $this->label( ['name' => 'pass2', 'label' => $attr['re_pass_label'], 'required' => $post_id ? 'no' : 'yes'] ); ?>

            <div class="wpuf-fields">
                <input id="<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_2'; ?>" type="password" class="password <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="confirm_password"<?php $this->required_html5( $attr ); ?> name="pass2" value="" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" size="<?php echo esc_attr( $attr['size'] ); ?>" />
            </div>

            <?php
        }

        if ( $pass_strength ) {
            echo wp_kses_post( '</li>' );
            echo wp_kses_post( '<li>' );

            wp_enqueue_script( 'zxcvbn' );
            wp_enqueue_script( 'password-strength-meter' ); ?>
            <div class="wpuf-label">
                &nbsp;
            </div>

            <div class="wpuf-fields">
                <div class="pass-strength-result" id="pass-strength-result_<?php echo esc_attr( $form_id ); ?>" style="display: block"><?php esc_html_e( 'Strength indicator', 'wp-user-frontend' ); ?></div>
            </div>

            <script type="text/javascript">
                jQuery(function($) {
                    function check_pass_strength() {
                        var pass1 = $("#<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_1'; ?>").val(),
                            pass2 = $("#<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_2'; ?>").val(),
                            strength;

                        if ( typeof pass2 === undefined ) {
                            pass2 = pass1;
                        }

                        $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").removeClass('short bad good strong');
                        if (!pass1) {
                            $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").html(pwsL10n.empty);
                            return;
                        }

                        strength = wp.passwordStrength.meter(pass1, wp.passwordStrength.userInputBlacklist(), pass2);

                        switch (strength) {
                            case 2:
                                $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").addClass('bad').html(pwsL10n.bad);
                                break;
                            case 3:
                                $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").addClass('good').html(pwsL10n.good);
                                break;
                            case 4:
                                $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").addClass('strong').html(pwsL10n.strong);
                                break;
                            case 5:
                                $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").addClass('short').html(pwsL10n.mismatch);
                                break;
                            default:
                                $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").addClass('short').html(pwsL10n['short']);
                        }
                    }

                    $("#<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_1'; ?>").val('').keyup(check_pass_strength);
                    $("#<?php echo esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ) . '_2'; ?>").val('').keyup(check_pass_strength);
                    $("#pass-strength-result_<?php echo esc_attr( $form_id ); ?>").show();
                });
            </script>
            <?php
        }
    }

    public function taxnomy_select( $terms, $attr ) {
        $selected           = $terms ? $terms : '';
        $required           = sprintf( 'data-required="%s" data-type="select"', $attr['required'] );
        $taxonomy           = $attr['name'];
        $class              = ' wpuf_' . $attr['name'] . '_' . $selected;
        $exclude_type       = isset( $attr['exclude_type'] ) ? $attr['exclude_type'] : 'exclude';
        $exclude            = isset( $attr['exclude'] ) ? $attr['exclude'] : '';

        if ( $exclude_type == 'child_of' && !empty( $exclude ) ) {
            $exclude = $exclude[0];
        }

        $tax_args           = [
            'show_option_none' => __( '-- Select --', 'wp-user-frontend' ),
            'hierarchical'     => 1,
            'hide_empty'       => 0,
            'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
            'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
            'name'             => $taxonomy . '[]',
            'taxonomy'         => $taxonomy,
            'echo'             => 0,
            'title_li'         => '',
            'class'            => 'cat-ajax ' . $taxonomy . $class,
            $exclude_type      => $exclude,
            'selected'         => $selected,
            'depth'            => 1,
            'child_of'         => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
        ];

        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

        $select = wp_dropdown_categories( $tax_args );

        echo wp_kses_post( str_replace( '<select', '<select ' . $required, $select ) );
        $attr = [
            'required'     => $attr['required'],
            'name'         => $attr['name'],
            'exclude_type' => $attr['exclude_type'],
            'exclude'      => isset( $attr['exclude'] ) ? $attr['exclude'] : '',
            'orderby'      => $attr['orderby'],
            'order'        => $attr['order'],
            //'last_term_id' => isset( $attr['parent_cat'] ) ? $attr['parent_cat'] : '',
            //'term_id'      => $selected
        ];
        $attr = apply_filters( 'wpuf_taxonomy_checklist_args', $attr ); ?>
        <span data-taxonomy=<?php echo esc_attr( json_encode( $attr ) ); ?>></span>
        <?php
    }

    /**
     * Prints a taxonomy field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function taxonomy( $attr, $post_id, $form_id ) {
        $exclude_type       = isset( $attr['exclude_type'] ) ? esc_attr( $attr['exclude_type'] ) : 'exclude';
        // $exclude            = $attr['exclude'];
        $exclude            = isset( $attr['exclude'] ) ? esc_attr( $attr['exclude'] ) : '';

        if ( $exclude_type == 'child_of' ) {
            $exclude = $exclude[0];
        }

        $taxonomy           = $attr['name'];
        $class              = ' wpuf_' . $attr['name'] . '_' . $form_id;
        $current_user       = get_current_user_id();

        $terms = [];

        if ( $post_id && $attr['type'] == 'text' ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy, ['fields' => 'names'] );
        } elseif ( $post_id ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy, ['fields' => 'ids'] );
        }

        if ( !taxonomy_exists( $taxonomy ) ) {
            echo wp_kses_post( '<br><div class="wpuf-message">' . __( 'This field is no longer available.', 'wp-user-frontend' ) . '</div>' );

            return;
        }

        $div_class = 'wpuf_' . $attr['name'] . '_' . $attr['type'] . '_' . $attr['id'] . '_' . $form_id; ?>


        <?php if ( $attr['type'] == 'checkbox' ) { ?>
            <div class="wpuf-fields <?php echo esc_attr( $div_class ); ?>" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="tax-checkbox">
        <?php } else { ?>
            <div class="wpuf-fields <?php echo esc_attr( $div_class ); ?>">
        <?php } ?>

                <?php
                switch ( $attr['type'] ) {
                    case 'ajax':
                        $class = ' wpuf_' . $attr['name'] . '_' . $form_id;
                        ?>
                        <div class="category-wrap <?php echo esc_attr( $class ); ?>">
                            <?php

                            if ( !count( $terms ) ) {
                                ?>
                                <div id="lvl0" level="0">
                                    <?php $this->taxnomy_select( null, $attr, $form_id ); ?>
                                </div>
                                <?php
                            } else {
                                $level = 0;
                                asort( $terms );
                                $last_term_id = end( $terms );

                                foreach ( $terms as $term_id ) {
                                    $class = ( $last_term_id != $term_id ) ? 'hasChild' : ''; ?>
                                    <div id="lvl<?php echo esc_attr( $level ); ?>" level="<?php echo esc_attr( $level ); ?>" >
                                        <?php $this->taxnomy_select( $term_id, $attr ); ?>
                                    </div>
                                <?php
                                    $attr['parent_cat'] = $term_id;
                                    $level++;
                                }
                            }

                        ?>
                        </div>
                        <span class="loading"></span>
                        <?php
                        break;

                    case 'select':
                        $selected = $terms ? $terms[0] : '';
                        $required = sprintf( 'data-required="%s" data-type="select"', $attr['required'] );
                        $tax_args = [
                            'show_option_none' => isset( $attr['first'] ) ? $attr['first'] : '--select--',
                            'hierarchical'     => 1,
                            'hide_empty'       => 0,
                            'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
                            'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
                            'name'             => $taxonomy . '[]',
                            'taxonomy'         => $taxonomy,
                            'echo'             => 0,
                            'title_li'         => '',
                            'class'            => $taxonomy . $class,
                            $exclude_type      => $exclude,
                            'selected'         => $selected,
                        ];

                        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

                        $select   = wp_dropdown_categories( $tax_args );

                        echo wp_kses_post( str_replace( '<select', '<select ' . $required, $select ) );
                        break;

                    case 'multiselect':
                        $selected = $terms ? $terms : [];
                        $required = sprintf( 'data-required="%s" data-type="multiselect"', $attr['required'] );
                        $walker   = new WPUF_Walker_Category_Multi();
                        $tax_args = [
                            // 'show_option_none' => __( '-- Select --', 'wpuf' ),
                            'hierarchical'     => 1,
                            'hide_empty'       => 0,
                            'orderby'          => isset( $attr['orderby'] ) ? $attr['orderby'] : 'name',
                            'order'            => isset( $attr['order'] ) ? $attr['order'] : 'ASC',
                            'name'             => $taxonomy . '[]',
                            'id'               => 'cat-ajax',
                            'taxonomy'         => $taxonomy,
                            'echo'             => 0,
                            'title_li'         => '',
                            'class'            => $taxonomy . ' multiselect' . $class,
                            $exclude_type      => $exclude,
                            'selected'         => $selected,
                            'walker'           => $walker,
                        ];

                        $tax_args = apply_filters( 'wpuf_taxonomy_checklist_args', $tax_args );

                        $select   = wp_dropdown_categories( $tax_args );

                        echo wp_kses_post( str_replace( '<select', '<select multiple="multiple" ' . $required, $select ) );
                        break;

                    case 'checkbox':
                        wpuf_category_checklist( $post_id, false, $attr, $class );
                        break;

                    case 'text':
                        ?>

                        <input class="textfield<?php echo esc_attr( $this->required_class( $attr ) ); ?>" id="<?php echo esc_attr( $attr['name'] ); ?>" type="text" data-required="<?php echo esc_attr( $attr['required'] ); ?>" data-type="text"<?php esc_attr( $this->required_html5( $attr ) ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" value="<?php echo esc_attr( implode( ', ', $terms ) ); ?>" size="40" />

                        <script type="text/javascript">
                            ;(function($) {
                                $(document).ready( function(){
                                        $('#<?php echo esc_attr( $attr['name'] ); ?>').suggest( wpuf_frontend.ajaxurl + '?action=wpuf-ajax-tag-search&tax=<?php echo esc_attr( $attr['name'] ); ?>', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } );
                                });
                            })(jQuery);
                        </script>

                        <?php
                        break;

                    default:
                        // code...
                        break;
                } ?>
            <?php $this->help_text( $attr ); ?>
        </div>


        <?php
    }

    /**
     * Prints a HTML field
     *
     * @param array $attr
     */
    public function html( $attr, $form_id ) {
        ?>
        <div class="wpuf-fields <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>">
            <?php echo esc_html( $attr['html'] ); ?>
        </div>
        <?php
    }

    /**
     * Prints a image upload field
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function image_upload( $attr, $post_id, $type, $form_id ) {
        $has_featured_image = false;
        $has_images         = false;
        $has_avatar         = false;
        $unique_id          = sprintf( '%s-%d', $attr['name'], $form_id );

        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $images = $this->get_meta( $post_id, $attr['name'], $type, false );

                if ( $images ) {
                    if ( is_serialized( $images[0] ) ) {
                        $images = maybe_unserialize( $images[0] );
                    }

                    if ( is_array( $images[0] ) ) {
                        $images = $images[0];
                    }
                }

                $has_images = true;
            } else {
                if ( $type == 'post' ) {
                    // it's a featured image then
                    $thumb_id = get_post_thumbnail_id( $post_id );

                    if ( $thumb_id ) {
                        $has_featured_image = true;
                        $featured_image     = WPUF_Upload::attach_html( $thumb_id, 'featured_image' );
                    }
                } else {
                    // it must be a user avatar
                    $has_avatar     = true;
                    $featured_image = get_avatar( $post_id );
                }
            }
        }
        $button_label = empty( $attr['button_label'] ) ? __( 'Select Image', 'wp-user-frontend' ) : $attr['button_label']; ?>

        <div class="wpuf-fields">
            <div id="wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container">
                <div class="wpuf-attachment-upload-filelist" data-type="file" data-required="<?php echo esc_attr( $attr['required'] ); ?>">
                    <a id="wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles" data-form_id="<?php echo esc_attr( $form_id ); ?>" class="button file-selector <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>" href="#"><?php echo esc_attr( $button_label ); ?></a>

                    <ul class="wpuf-attachment-list thumbnails">
                        <?php
                        if ( $has_featured_image ) {
                            echo esc_attr( $featured_image );
                        }

        if ( $has_avatar ) {
            $avatar = get_user_meta( $post_id, 'user_avatar', true );

            if ( $avatar ) {
                echo wp_kses_post( '<li>' . $featured_image );
                printf( '<br><a href="#" data-confirm="%s" class="btn btn-danger btn-small wpuf-button button wpuf-delete-avatar">%s</a>', esc_html( __( 'Are you sure?', 'wp-user-frontend' ) ),esc_html(  __( 'Delete', 'wp-user-frontend' ) ) );
                echo wp_kses_post( '</li>' );
            }
        }

        if ( $has_images ) {
            foreach ( $images as $attach_id ) {
                echo esc_attr( WPUF_Upload::attach_html( $attach_id, $attr['name'] ) );
            }
        } ?>
                    </ul>
                </div>
            </div><!-- .container -->

            <?php $this->help_text( $attr ); ?>

        </div> <!-- .wpuf-fields -->

        <script type="text/javascript">
            ;(function($) {
                $(document).ready( function(){
                    var uploader = new WPUF_Uploader('wpuf-<?php echo esc_attr( $unique_id ); ?>-pickfiles', 'wpuf-<?php echo esc_attr( $unique_id ); ?>-upload-container', <?php echo esc_attr( $attr['count'] ); ?>, '<?php echo esc_attr( $attr['name'] ); ?>', 'jpg,jpeg,gif,png,bmp', <?php echo esc_attr( $attr['max_size'] ); ?>);
                    wpuf_plupload_items.push(uploader);
                });
            })(jQuery);
        </script>
    <?php
    }

    /**
     * Prints a section break
     *
     * @param array    $attr
     * @param int|null $post_id
     */
    public function section_break( $attr, $post_id, $form_id ) {
        ?>
        <div class="wpuf-section-wrap <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $attr['id'] ) . '_' . esc_attr( $form_id ); ?>">
            <h2 class="wpuf-section-title"><?php echo esc_attr( $attr['label'] ); ?></h2>
            <div class="wpuf-section-details"><?php echo esc_attr( $attr['description'] ); ?></div>
        </div>
        <?php
    }

    /**
     * Prints recaptcha field
     *
     * @param array $attr
     */
    public static function recaptcha( $attr, $post_id, $form_id ) {
        if ( $post_id ) {
            return;
        }
        $enable_no_captcha = $enable_invisible_recaptcha = '';

        if ( isset( $attr['recaptcha_type'] ) ) {
            $enable_invisible_recaptcha = $attr['recaptcha_type'] == 'invisible_recaptcha' ? true : false;
            $enable_no_captcha          = $attr['recaptcha_type'] == 'enable_no_captcha' ? true : false;
        }

        if ( $enable_invisible_recaptcha ) { ?>
            <script src="https://www.google.com/recaptcha/api.js?onload=wpufreCaptchaLoaded&render=explicit&hl=en" async defer></script>
            <script>
                jQuery(document).ready(function($) {
                    jQuery('[name="submit"]').removeClass('wpuf-submit-button').addClass('g-recaptcha').attr('data-sitekey', '<?php echo esc_html( wpuf_get_option( 'recaptcha_public', 'wpuf_general' ) ); ?>');

                    $(document).on('click','.g-recaptcha', function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        grecaptcha.execute();
                    })
                });

                var wpufreCaptchaLoaded = function() {
                    grecaptcha.render('recaptcha', {
                        'size' : 'invisible',
                        'callback' : wpufRecaptchaCallback
                    });
                    grecaptcha.execute();
                };

                function wpufRecaptchaCallback(token) {
                    jQuery('[name="g-recaptcha-response"]').val(token);
                    jQuery('[name="submit"]').removeClass('g-recaptcha').addClass('wpuf-submit-button');
                }
            </script>

            <div type="submit" id='recaptcha' class="g-recaptcha" data-sitekey=<?php echo esc_attr( wpuf_get_option( 'recaptcha_public', 'wpuf_general' ) ); ?> data-callback="onSubmit" data-size="invisible"></div>
        <?php } else { ?>
            <div class="wpuf-fields <?php echo ' wpuf_' . esc_attr( $attr['name'] ) . '_' . esc_attr( $form_id ); ?>">
                <?php echo esc_html( recaptcha_get_html( wpuf_get_option( 'recaptcha_public', 'wpuf_general' ), $enable_no_captcha, null, is_ssl() ) ); ?>
            </div>
        <?php }
    }
}
