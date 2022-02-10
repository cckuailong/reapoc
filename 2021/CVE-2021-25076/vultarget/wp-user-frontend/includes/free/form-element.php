<?php

// @codingStandardsIgnoreStart
class WPUF_form_element extends WPUF_Pro_Prompt {
// @codingStandardsIgnoreEnd

    public static function add_form_custom_buttons() {
        $title = esc_attr( __( 'Click to add to the editor', 'wp-user-frontend' ) ); ?>
        <button class="button" data-name="custom_image" data-type="image" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Image Upload', 'wp-user-frontend' ); ?></button>

        <?php self::get_pro_prompt(); ?>
        <button class="button" disabled data-type="repeat" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Repeat Field', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="date" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Date', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="file" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'File Upload', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="map" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Google Maps', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="select" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Country List', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="text" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Numeric Field', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="text" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Address Field', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="text" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Step Start', 'wp-user-frontend' ); ?></button>
        <?php
    }

    /**
     * Add formbuilder's button in Others section
     */
    public static function add_form_other_buttons() {
        $title = esc_attr( __( 'Click to add to the editor', 'wp-user-frontend' ) );

        self::get_pro_prompt();
        ?>
        <button class="button" disabled data-type="shortcode" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Shortcode', 'wp-user-frontend' ); ?></button>
        <button class="button" data-name="recaptcha" data-type="captcha" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'reCaptcha', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="rscaptcha" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Really Simple Captcha', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="action" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Action Hook', 'wp-user-frontend' ); ?></button>
        <button class="button" disabled data-type="action" title="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( 'Term &amp; Conditions', 'wp-user-frontend' ); ?></button>
        <?php
    }

    /**
     * Render form expiration tab
     */
    public static function render_form_expiration_tab() {
        global $post;

        $is_post_exp_selected         = 'checked';
        $time_value                   = 1;
        $time_type                    = 'day';
        $expired_post_status          = 'draft';
        $is_enable_mail_after_expired = 'checked';
        $post_expiration_message      = '';

        self::get_pro_prompt();
        ?>
        <div id="wpuf-pro-content">
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Post Expiration', 'wp-user-frontend' ); ?></th>
                    <td>
                        <label>
                            <input disabled type="checkbox" id="wpuf-enable_post_expiration" name="" value="on" <?php echo esc_attr( $is_post_exp_selected ); ?> />
                            <?php esc_html_e( 'Enable Post Expiration', 'wp-user-frontend' ); ?>
                        </label>
                    </td>
                     <p class="description"><a target="_blank" href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/using-post-expiration-wp-user-frontend/"><?php esc_html_e( 'Learn more about Automatic Post Expiration', 'wp-user-frontend' ); ?></a></p>
                </tr>
                <tr class="wpuf_expiration_field">
                    <th><?php esc_html_e( 'Post Expiration Time', 'wp-user-frontend' ); ?></th>
                    <td>
                        <?php
                        $time_type_array = [
                            'year'  => 100,
                            'month' => 12,
                            'day'   => 30,
                        ];
                        ?>
                        <select disabled name="" id="wpuf-expiration_time_value">
                            <?php
                            for ( $i = 1; $i <= $time_type_array[ $time_type ]; $i++ ) {
                                ?>
                                <option value="<?php echo esc_attr( $i ); ?>" <?php echo $i === $time_value ? 'selected' : ''; ?> ><?php echo esc_attr( $i ); ?></option>
								<?php
                            }
                            ?>
                        </select>
                        <select disabled name="" id="wpuf-expiration_time_type">
                            <?php
                            foreach ( $time_type_array as $each_time_type => $each_time_type_val ) {
                                ?>
                                <option value="<?php echo esc_attr( $each_time_type ); ?>" <?php echo $each_time_type === $time_type ? 'selected' : ''; ?> ><?php echo esc_html( ucfirst( $each_time_type ) ); ?></option>
								<?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr class="wpuf_expiration_field">
                    <th>
                        Post Status :
                    </th>
                    <td>
                        <?php $post_statuses = get_post_statuses(); ?>
                        <select disabled name="" id="wpuf-expired_post_status">
                            <?php
                            foreach ( $post_statuses as $post_status => $text ) {
                                ?>
                                <option value="<?php echo esc_attr( $post_status ); ?>" <?php echo ( $expired_post_status === $post_status ) ? 'selected' : ''; ?> >
                                    <?php
                                    echo esc_html(
                                        $text
                                    );
									?>
                                </option>
								<?php
                            }
                            ?>
                        </select>
                        <p class="description"><?php echo esc_html( __( 'Status of post after post expiration time is over ', 'wp-user-frontend' ) ); ?></p>

                    </td>
                </tr>
                <tr class="wpuf_expiration_field">
                    <th>
                        Send Mail :
                    </th>
                    <td>
                        <label>
                            <input disabled type="checkbox" name="" value="on" <?php echo esc_attr( $is_enable_mail_after_expired ); ?> />
                            <?php echo esc_html( __( 'Send Email to Author After Exceeding Post Expiration Time', 'wp-user-frontend' ) ); ?>
                        </label>
                    </td>
                </tr>
                <tr class="wpuf_expiration_field">
                    <th>Post Expiration Message</th>
                    <td>
                        <textarea disabled name="" id="wpuf-post_expiration_message" cols="50" rows="5"><?php echo esc_html( $post_expiration_message ); ?></textarea>
                        <p class="description"><strong><?php echo esc_html( __( 'You may use: {post_author} {post_url} {blogname} {post_title} {post_status}', 'wp-user-frontend' ) ); ?></strong></p>
                    </td>
                </tr>
            </table>
        </div>

		<?php
    }

    /**
     * Add form settings content
     */
    public static function add_form_settings_content( $form_settings, $post ) {
        ?>
        <tr>
            <td colspan="2"><?php self::get_pro_prompt(); ?></td>
        </tr>
        <tr class="wpuf_enable_multistep_section wpuf-pro-content">
            <th><?php esc_html_e( 'Enable Multistep', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <input disabled type="checkbox" name="" value="yes" checked />
                    <?php esc_html_e( 'Enable Multistep', 'wp-user-frontend' ); ?>
                </label>

                <p class="description"><?php echo esc_html( __( 'If checked, form will be displayed in frontend in multiple steps', 'wp-user-frontend' ) ); ?>
                <a target="_blank" href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/how-to-add-multi-step-form/"><?php esc_html_e( ' Learn more about Multistep', 'wp-user-frontend' ); ?></a></p>
            </td>
        </tr>
        <tr class="wpuf_multistep_progress_type wpuf-pro-content">
            <th><?php esc_html_e( 'Multistep Progressbar Type', 'wp-user-frontend' ); ?></th>
            <td>
                <label>
                    <select disabled name="">
                        <option value="progressive" selected >Progressive</option>
                        <option value="step_by_step" >Step by Step</option>
                    </select>
                </label>


                <p class="description"><?php echo esc_html( __( 'Choose how you want the progressbar', 'wp-user-frontend' ) ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Add content to post notification section
     */
    public static function add_post_notification_content() {
        global $post;

        $new_mail_body  = "Hi Admin,\r\n";
        $new_mail_body .= "A new post has been created in your site %sitename% (%siteurl%).\r\n\r\n";

        $edit_mail_body = "Hi Admin,\r\n";
        $edit_mail_body .= "The post \"%post_title%\" has been updated.\r\n\r\n";

        $mail_body      = "Here is the details:\r\n";
        $mail_body .= "Post Title: %post_title%\r\n";
        $mail_body .= "Content: %post_content%\r\n";
        $mail_body .= "Author: %author%\r\n";
        $mail_body .= "Post URL: %permalink%\r\n";
        $mail_body .= 'Edit URL: %editlink%';

        $form_settings = wpuf_get_form_settings( $post->ID );

        $new_notificaton  = isset( $form_settings['notification']['new'] ) ? $form_settings['notification']['new'] : 'on';
        $new_to           = isset( $form_settings['notification']['new_to'] ) ? $form_settings['notification']['new_to'] : get_option( 'admin_email' );
        $new_subject      = isset( $form_settings['notification']['new_subject'] ) ? $form_settings['notification']['new_subject'] : __( 'New post created', 'wp-user-frontend' );
        $new_body         = isset( $form_settings['notification']['new_body'] ) ? $form_settings['notification']['new_body'] : $new_mail_body . $mail_body;

        $edit_notificaton = 'off';
        $edit_to          = get_option( 'admin_email' );
        $edit_subject     = __( 'A post has been edited', 'wp-user-frontend' );
        $edit_body        = $edit_mail_body . $mail_body;
        ?>

        <h3><?php esc_html_e( 'New Post Notification', 'wp-user-frontend' ); ?></h3>
        <table class="form-table">
            <tr>
                <th><?php esc_html_e( 'Notification', 'wp-user-frontend' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[notification][new]" value="off">
                        <input type="checkbox" name="wpuf_settings[notification][new]" value="on"<?php checked( $new_notificaton, 'on' ); ?>>
                        <?php esc_html_e( 'Enable post notification', 'wp-user-frontend' ); ?>
                    </label>
                </td>
                 <p class="description"><a target="_blank" href="https://wedevs.com/docs/wp-user-frontend-pro/posting-forms/how-to-set-up-submission-email-notification/"><?php esc_html_e( 'Learn more about Email Notification', 'wp-user-frontend' ); ?></a></p>
            </tr>

            <tr>
                <th><?php esc_html_e( 'To', 'wp-user-frontend' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[notification][new_to]" class="regular-text" value="<?php echo esc_attr( $new_to ); ?>">
                </td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Subject', 'wp-user-frontend' ); ?></th>
                <td><input type="text" name="wpuf_settings[notification][new_subject]" class="regular-text" value="<?php echo esc_attr( $new_subject ); ?>"></td>
            </tr>

            <tr>
                <th><?php esc_html_e( 'Message', 'wp-user-frontend' ); ?></th>
                <td>
                    <textarea rows="6" cols="60" name="wpuf_settings[notification][new_body]"><?php echo esc_textarea( $new_body ); ?></textarea>
                </td>
            </tr>
        </table>

        <h3><?php esc_html_e( 'Update Post Notification', 'wp-user-frontend' ); ?></h3>

        <div id="wpuf-pro-content">
            <?php self::get_pro_prompt(); ?>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Notification', 'wp-user-frontend' ); ?></th>
                    <td>
                        <label>
                            <input disabled type="checkbox" name="" value="on"<?php checked( $edit_notificaton, 'on' ); ?>>
                            <input type="hidden" name="wpuf_settings[notification][edit]" value="off">
                            <?php esc_html_e( 'Enable post notification', 'wp-user-frontend' ); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th><?php esc_html_e( 'To', 'wp-user-frontend' ); ?></th>
                    <td><input disabled type="text" name="" class="regular-text" value="<?php echo esc_attr( $edit_to ); ?>"></td>
                </tr>

                <tr>
                    <th><?php esc_html_e( 'Subject', 'wp-user-frontend' ); ?></th>
                    <td><input disabled type="text" name="" class="regular-text" value="<?php echo esc_attr( $edit_subject ); ?>"></td>
                </tr>

                <tr>
                    <th><?php esc_html_e( 'Message', 'wp-user-frontend' ); ?></th>
                    <td>
                        <textarea disabled rows="6" cols="60" name=""><?php echo esc_textarea( $edit_body ); ?></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <h3><?php esc_html_e( 'You may use in to, subject & message:', 'wp-user-frontend' ); ?></h3>
        <p>
            <code>%post_title%</code>, <code>%post_content%</code>, <code>%post_excerpt%</code>, <code>%tags%</code>, <code>%category%</code>, 
            <?php
            if ( class_exists( 'WooCommerce' ) ) :
				?>
                 <code>%product_cat%</code> <?php endif ?>,
            <code>%author%</code>, <code>%author_email%</code>, <code>%author_bio%</code>, <code>%sitename%</code>, <code>%siteurl%</code>, <code>%permalink%</code>, <code>%editlink%</code>
            <br><code>%custom_{NAME_OF_CUSTOM_FIELD}%</code> e.g: <code>%custom_website_url%</code> for <code>website_url</code> meta field
        </p>

		<?php
    }

    /**
     * Render registration form
     */
    public static function render_registration_form() {
        global $post, $pagenow, $form_inputs;

        $form_inputs = wpuf_get_form_fields( $post->ID );

        self::get_pro_prompt();
        ?>
        <div style="margin-bottom: 10px">
            <button class="button wpuf-collapse"><?php esc_html_e( 'Toggle All', 'wp-user-frontend' ); ?></button>
        </div>

        <div class="wpuf-updated">
            <p><?php esc_html_e( 'Click on a form element to add to the editor', 'wp-user-frontend' ); ?></p>
        </div>

        <ul id="wpuf-form-editor" class="wpuf-form-editor unstyled">

            <?php

            if ( $form_inputs ) {
                $count = 0;

                foreach ( $form_inputs as $order => $input_field ) {
                    $name = ucwords( str_replace( '_', ' ', $input_field['template'] ) );

                    // @codingStandardsIgnoreStart
                    WPUF_Admin_Template_Profile::$input_field['template']( $count, $name, $input_field );
                    
                    // @codingStandardsIgnoreEnd

                    $count++;
                }
            }
            ?>
        </ul>
        <?php
    }

    /**
     * Render registration settings
     */
    public static function render_registration_settings() {
        global $post;

        $form_settings = wpuf_get_form_settings( $post->ID );

        $email_verification = 'no';
        $role_selected      = 'subscriber';
        $redirect_to        = 'post';
        $message            = __( 'Registration successful', 'wp-user-frontend' );
        $update_message     = __( 'Profile updated successfully', 'wp-user-frontend' );
        $page_id            = 0;
        $url                = '';
        $submit_text        = __( 'Register', 'wp-user-frontend' );
        $update_text        = __( 'Update Profile', 'wp-user-frontend' );
        ?>
        <tr>
            <td colspan="2">
                <?php self::get_pro_prompt(); ?>
            </td>
        </tr>
        <tr class="wpuf-post-type">
            <th><?php esc_html_e( 'Enable Email Verfication', 'wp-user-frontend' ); ?></th>
            <td>
                <input type="hidden" name="" value="no">
                <input disabled type="checkbox" id="wpuf-enable_email_verification" name="" value="yes" <?php checked( $email_verification, 'yes' ); ?> > <label for="wpuf-enable_email_verification">Enable Email Verification</label>
            </td>
        </tr>

        <tr class="wpuf-post-type">
            <th><?php esc_html_e( 'New User Role', 'wp-user-frontend' ); ?></th>
            <td>
                <select disabled name="">
                    <?php
                    $user_roles = wpuf_get_user_roles();
                    foreach ( $user_roles as $role => $label ) {
                        printf( 
                            '<option value="%s"%s>%s</option>', 
                            esc_attr( $role ), 
                            esc_attr( selected( $role_selected, $role, false ) ), 
                            esc_attr( $label ) 
                        );
                    }
					?>
                </select>
            </td>
        </tr>

        <tr class="wpuf-redirect-to">
            <th><?php esc_html_e( 'Redirect To', 'wp-user-frontend' ); ?></th>
            <td>
                <select disabled name="">
                    <?php
                    $redirect_options = [
                        'same' => __( 'Same Page', 'wp-user-frontend' ),
                        'page' => __( 'To a page', 'wp-user-frontend' ),
                        'url'  => __( 'To a custom URL', 'wp-user-frontend' ),
                    ];

                    foreach ( $redirect_options as $to => $label ) {
                        printf( '<option value="%s"%s>%s</option>', esc_attr( $to ), esc_attr( selected( $redirect_to, $to, false ) ), esc_html( $label ) );
                    }
                    ?>
                </select>
                <div class="description">
                    <?php esc_html_e( 'After successfull submit, where the page will redirect to', 'wp-user-frontend' ); ?>
                </div>
            </td>
        </tr>

        <tr class="wpuf-same-page">
            <th><?php esc_html_e( 'Registration success message', 'wp-user-frontend' ); ?></th>
            <td>
                <textarea disabled rows="3" cols="40" name=""><?php echo esc_textarea( $message ); ?></textarea>
            </td>
        </tr>

        <tr class="wpuf-same-page">
            <th><?php esc_html_e( 'Update profile message', 'wp-user-frontend' ); ?></th>
            <td>
                <textarea disabled rows="3" cols="40" name=""><?php echo esc_textarea( $update_message ); ?></textarea>
            </td>
        </tr>

        <tr class="wpuf-page-id">
            <th><?php esc_html_e( 'Page', 'wp-user-frontend' ); ?></th>
            <td>
                <select disabled name="">
                    <?php
                    $pages = get_posts( [
                        'numberposts' => -1,
                        'post_type' => 'page',
                    ]);

                    foreach ( $pages as $page ) {
                        printf( 
                            '<option value="%s"%s>%s</option>', 
                            esc_attr( $page->ID ), 
                            esc_attr( selected( $page_id, $page->ID, false ) ), 
                            esc_attr( $page->post_title ) 
                        );
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="wpuf-url">
            <th><?php esc_html_e( 'Custom URL', 'wp-user-frontend' ); ?></th>
            <td>
                <input disabled type="url" name="" value="<?php echo esc_attr( $url ); ?>">
            </td>
        </tr>

        <tr class="wpuf-submit-text">
            <th><?php esc_html_e( 'Submit Button text', 'wp-user-frontend' ); ?></th>
            <td>
                <input disabled type="text" name="" value="<?php echo esc_attr( $submit_text ); ?>">
            </td>
        </tr>

        <tr class="wpuf-update-text">
            <th><?php esc_html_e( 'Update Button text', 'wp-user-frontend' ); ?></th>
            <td>
                <input disabled type="text" name="" value="<?php echo esc_attr( $update_text ); ?>">
            </td>
        </tr>
		<?php
    }

    /**
     * Checks what the post type is
     */
    public static function check_post_type( $post, $update ) {
        if ( get_post_type( $post->ID ) === 'wpuf_profile' && $update ) {
            $http_referer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
            wp_redirect( $http_referer );
            die();
        }
    }

    /**
     * Render custom taxonomies
     */
    public static function render_custom_taxonomies_element() {
        self::get_pro_prompt();
    }

    /**
     * Render conditional logic
     */
    public static function render_conditional_field( $field_id, $con_fields, $obj ) {
        ?>
        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Conditional Logic', 'wp-user-frontend' ); ?></label>

            <div class="wpuf-form-sub-fields">
                <label><input type="radio" name="" disabled class="wpuf-conditional-enable" value="yes"> <?php esc_html_e( 'Yes', 'wp-user-frontend' ); ?></label>
                <label><input type="radio" name="" disabled class="wpuf-conditional-enable" value="no" checked> <?php esc_html_e( 'No', 'wp-user-frontend' ); ?></label>

                <label class="wpuf-pro-text-alert"> (<?php echo esc_attr( self::get_pro_prompt_text() ); ?>)</label>
            </div>
        </div> <!-- .wpuf-form-rows -->
		<?php
    }
}
