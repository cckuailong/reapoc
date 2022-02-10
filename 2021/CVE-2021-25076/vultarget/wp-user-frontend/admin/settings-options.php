<?php

/**
 * Settings Sections
 *
 * @since 1.0
 *
 * @return array
 */
function wpuf_settings_sections() {
    $sections = [
        [
            'id'    => 'wpuf_general',
            'title' => __( 'General Options', 'wp-user-frontend' ),
            'icon'  => 'dashicons-admin-generic',
        ],
        [
            'id'    => 'wpuf_frontend_posting',
            'title' => __( 'Frontend Posting', 'wp-user-frontend' ),
            'icon'  => 'dashicons-welcome-write-blog',
        ],
        [
            'id'    => 'wpuf_dashboard',
            'title' => __( 'Dashboard', 'wp-user-frontend' ),
            'icon'  => 'dashicons-dashboard',
        ],
        [
            'id'    => 'wpuf_my_account',
            'title' => __( 'My Account', 'wp-user-frontend' ),
            'icon'  => 'dashicons-id',
        ],
        [
            'id'    => 'wpuf_profile',
            'title' => __( 'Login / Registration', 'wp-user-frontend' ),
            'icon'  => 'dashicons-admin-users',
        ],
        [
            'id'    => 'wpuf_payment',
            'title' => __( 'Payments', 'wp-user-frontend' ),
            'icon'  => 'dashicons-money',
        ],
        [
            'id'    => 'wpuf_mails',
            'title' => __( 'E-Mails', 'wp-user-frontend' ),
            'icon'  => 'dashicons-email-alt',
        ],
        [
            'id'    => 'wpuf_privacy',
            'title' => __( 'Privacy Options', 'wp-user-frontend' ),
            'icon'  => 'dashicons-shield-alt',
        ],
    ];

    return apply_filters( 'wpuf_settings_sections', $sections );
}

function wpuf_settings_fields() {
    $pages      = wpuf_get_pages();
    $users      = wpuf_list_users();
    $post_types = get_post_types();
    unset( $post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item'], $post_types['wpuf_forms'], $post_types['wpuf_profile'], $post_types['wpuf_input'], $post_types['wpuf_subscription'], $post_types['custom_css'], $post_types['customize_changeset'], $post_types['wpuf_coupon'], $post_types['oembed_cache'] );

    $login_redirect_pages = [
        'previous_page' => __( 'Previous Page', 'wp-user-frontend' ),
    ] + $pages;

    $all_currencies = wpuf_get_currencies();

    $currencies = [];

    foreach ( $all_currencies as $currency ) {
        $currencies[ $currency['currency'] ] = $currency['label'] . ' (' . $currency['symbol'] . ')';
    }

    $default_currency_symbol = wpuf_get_currency( 'symbol' );

    $user_roles = [];
    $all_roles  = get_editable_roles();

    foreach ( $all_roles as $key => $value ) {
        $user_roles[ $key ] = $value['name'];
    }

    $settings_fields = [
        'wpuf_general' => apply_filters(
            'wpuf_options_others', [
                [
                    'name'    => 'show_admin_bar',
                    'label'   => __( 'Show Admin Bar', 'wp-user-frontend' ),
                    'desc'    => __( 'Select user by roles, who can view admin bar in frontend.', 'wp-user-frontend' ),
                    'callback' => 'wpuf_settings_multiselect',
                    'options' => $user_roles,
                    'default' => [ 'administrator', 'editor', 'author', 'contributor' ],
                ],
                [
                    'name'    => 'admin_access',
                    'label'   => __( 'Admin area access', 'wp-user-frontend' ),
                    'desc'    => __( 'Allow you to block specific user role to Ajax request and Media upload.', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'read',
                    'options' => [
                        'manage_options'    => __( 'Admin Only', 'wp-user-frontend' ),
                        'edit_others_posts' => __( 'Admins, Editors', 'wp-user-frontend' ),
                        'publish_posts'     => __( 'Admins, Editors, Authors', 'wp-user-frontend' ),
                        'edit_posts'        => __( 'Admins, Editors, Authors, Contributors', 'wp-user-frontend' ),
                        'read'              => __( 'Default', 'wp-user-frontend' ),
                    ],
                ],
                [
                    'name'    => 'override_editlink',
                    'label'   => __( 'Override the post edit link', 'wp-user-frontend' ),
                    'desc'    => __( 'Users see the edit link in post if s/he is capable to edit the post/page. Selecting <strong>Yes</strong> will override the default WordPress edit post link in frontend', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => [
                        'yes' => __( 'Yes', 'wp-user-frontend' ),
                        'no'  => __( 'No', 'wp-user-frontend' ),
                    ],
                ],
                [
                    'name'    => 'wpuf_compatibility_acf',
                    'label'   => __( 'ACF Compatibility', 'wp-user-frontend' ),
                    'desc'    => __( 'Select <strong>Yes</strong> if you want to make compatible WPUF custom fields data with advanced custom fields.', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => [
                        'yes' => __( 'Yes', 'wp-user-frontend' ),
                        'no'  => __( 'No', 'wp-user-frontend' ),
                    ],
                ],
                [
                    'name'    => 'load_script',
                    'label'   => __( 'Load Scripts', 'wp-user-frontend' ),
                    'desc'    => __( 'Load scripts/styles in all pages', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'  => 'recaptcha_public',
                    'label' => __( 'reCAPTCHA Site Key', 'wp-user-frontend' ),
                ],
                [
                    'name'  => 'recaptcha_private',
                    'label' => __( 'reCAPTCHA Secret Key', 'wp-user-frontend' ),
                    'desc'  => __( '<a target="_blank" href="https://www.google.com/recaptcha/">Register here</a> to get reCaptcha Site and Secret keys.', 'wp-user-frontend' ),
                ],
                [
                    'name'  => 'custom_css',
                    'label' => __( 'Custom CSS codes', 'wp-user-frontend' ),
                    'desc'  => __( 'If you want to add your custom CSS code, it will be added on page header wrapped with style tag', 'wp-user-frontend' ),
                    'type'  => 'textarea',
                ],
            ]
        ),
        'wpuf_frontend_posting' => apply_filters(
            'wpuf_options_frontend_posting', [
                [
                    'name'    => 'edit_page_id',
                    'label'   => __( 'Edit Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the page where <code>[wpuf_edit]</code> is located', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'default_post_owner',
                    'label'   => __( 'Default Post Owner', 'wp-user-frontend' ),
                    'desc'    => __( 'If guest post is enabled and user details are OFF, the posts are assigned to this user', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $users,
                    'default' => '1',
                ],
                [
                    'name'    => 'cf_show_front',
                    'label'   => __( 'Custom Fields in post', 'wp-user-frontend' ),
                    'desc'    => __( 'Show custom fields on post content area', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ],
                [
                    'name'    => 'insert_photo_size',
                    'label'   => __( 'Insert Photo image size', 'wp-user-frontend' ),
                    'desc'    => __( 'Default image size of "<strong>Insert Photo</strong>" button in post content area', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => wpuf_get_image_sizes(),
                    'default' => 'thumbnail',
                ],
                [
                    'name'    => 'insert_photo_type',
                    'label'   => __( 'Insert Photo image type', 'wp-user-frontend' ),
                    'desc'    => __( 'Default image type of "<strong>Insert Photo</strong>" button in post content area', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => [
                        'image' => __( 'Image only', 'wp-user-frontend' ),
                        'link'  => __( 'Image with link', 'wp-user-frontend' ),
                    ],
                    'default' => 'link',
                ],
                [
                    'name'    => 'image_caption',
                    'label'   => __( 'Enable Image Caption', 'wp-user-frontend' ),
                    'desc'    => __( 'Allow users to update image/video title, caption and description', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ],
                [
                    'name'    => 'default_post_form',
                    'label'   => __( 'Default Post Form', 'wp-user-frontend' ),
                    'desc'    => __( 'Fallback form for post editing if no associated form found', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => wpuf_get_pages( 'wpuf_forms' ),
                ],
            ]
        ),
        'wpuf_dashboard' => apply_filters(
            'wpuf_options_dashboard', [
                [
                    'name'    => 'enable_post_edit',
                    'label'   => __( 'Users can edit post?', 'wp-user-frontend' ),
                    'desc'    => __( 'Users will be able to edit their own posts', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' => [
                        'yes' => __( 'Yes', 'wp-user-frontend' ),
                        'no'  => __( 'No', 'wp-user-frontend' ),
                    ],
                ],
                [
                    'name'    => 'enable_post_del',
                    'label'   => __( 'User can delete post?', 'wp-user-frontend' ),
                    'desc'    => __( 'Users will be able to delete their own posts', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' => [
                        'yes' => __( 'Yes', 'wp-user-frontend' ),
                        'no'  => __( 'No', 'wp-user-frontend' ),
                    ],
                ],
                [
                    'name'    => 'disable_pending_edit',
                    'label'   => __( 'Pending Post Edit', 'wp-user-frontend' ),
                    'desc'    => __( 'Disable post editing while post in "pending" status', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'per_page',
                    'label'   => __( 'Posts per page', 'wp-user-frontend' ),
                    'desc'    => __( 'How many posts will be listed in a page', 'wp-user-frontend' ),
                    'type'    => 'text',
                    'default' => '10',
                ],
                [
                    'name'    => 'show_user_bio',
                    'label'   => __( 'Show user bio', 'wp-user-frontend' ),
                    'desc'    => __( 'Users biographical info will be shown', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'show_post_count',
                    'label'   => __( 'Show post count', 'wp-user-frontend' ),
                    'desc'    => __( 'Show how many posts are created by the user', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'  => 'show_ft_image',
                    'label' => __( 'Show Featured Image', 'wp-user-frontend' ),
                    'desc'  => __( 'Show featured image of the post (Overridden by Shortcode)', 'wp-user-frontend' ),
                    'type'  => 'checkbox',
                ],
                [
                    'name'    => 'show_payment_column',
                    'label'   => __( 'Show Payment Column', 'wp-user-frontend' ),
                    'desc'    => __( 'Enable if you want show payment column on posts table', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'ft_img_size',
                    'label'   => __( 'Featured Image size', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => wpuf_get_image_sizes(),
                ],
                [
                    'name'  => 'un_auth_msg',
                    'label' => __( 'Unauthorized Message', 'wp-user-frontend' ),
                    'desc'  => __( 'Not logged in users will see this message', 'wp-user-frontend' ),
                    'type'  => 'textarea',
                ],
            ]
        ),
        'wpuf_my_account' => apply_filters(
            'wpuf_options_wpuf_my_account', [
                [
                    'name'    => 'account_page',
                    'label'   => __( 'Account Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the page which contains <code>[wpuf_account]</code> shortcode', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'cp_on_acc_page',
                    'label'   => __( 'Select Custom Post For Account Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the post types you want to show on user dashboard.', 'wp-user-frontend' ),
                    'callback' => 'wpuf_settings_multiselect',
                    'options' => $post_types,
                ],
                [
                    'name'    => 'account_page_active_tab',
                    'label'   => __( 'Active Tab', 'wp-user-frontend' ),
                    'desc'    => __( 'Which tab should be set as active by default when opening the account page', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => wpuf_get_account_sections_list(),
                ],
                [
                    'name'    => 'show_subscriptions',
                    'label'   => __( 'Show Subscriptions', 'wp-user-frontend' ),
                    'desc'    => __( 'Show Subscriptions tab in "my account" page where <code>[wpuf_account]</code> is located', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'show_billing_address',
                    'label'   => __( 'Show Billing Address', 'wp-user-frontend' ),
                    'desc'    => __( 'Show billing address in account page.', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
            ]
        ),
        'wpuf_profile' => apply_filters(
            'wpuf_options_profile', [
                [
                    'name'    => 'autologin_after_registration',
                    'label'   => __( 'Auto Login After Registration', 'wp-user-frontend' ),
                    'desc'    => __( 'If enabled, users after registration will be logged in to the system', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'register_link_override',
                    'label'   => __( 'Login/Registration override', 'wp-user-frontend' ),
                    'desc'    => __( 'If enabled, default login and registration forms will be overridden by WPUF with pages below', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'reg_override_page',
                    'label'   => __( 'Registration Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the page you want to use as registration page override <em>(should have shortcode)</em>', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'login_page',
                    'label'   => __( 'Login Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the page which contains <code>[wpuf-login]</code> shortcode', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'redirect_after_login_page',
                    'label'   => __( 'Redirect After Login', 'wp-user-frontend' ),
                    'desc'    => __( 'After successfull login, where the page will redirect to', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $login_redirect_pages,
                ],
                [
                    'name'    => 'wp_default_login_redirect',
                    'label'   => __( 'Default Login Redirect', 'wp-user-frontend' ),
                    'desc'    => __( 'If enabled, users who login using WordPress default login form will be redirected to the selected page.', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ],
                [
                    'name'    => 'login_form_recaptcha',
                    'label'   => __( 'reCAPTCHA in Login Form', 'wp-user-frontend' ),
                    'desc'    => __( 'If enabled, users have to verify reCAPTCHA in login page. Also, make sure that reCAPTCHA is configured properly from <b>General Options</b>', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ],
            ]
        ),
        'wpuf_payment' => apply_filters(
            'wpuf_options_payment', [
                [
                    'name'    => 'enable_payment',
                    'label'   => __( 'Enable Payments', 'wp-user-frontend' ),
                    'desc'    => __( 'Enable payments on your site.', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'subscription_page',
                    'label'   => __( 'Subscription Pack Page', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the page where <code>[wpuf_sub_pack]</code> located.', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'  => 'register_subscription',
                    'label' => __( 'Subscription at registration', 'wp-user-frontend' ),
                    'desc'  => __( 'Registration time redirect to subscription page', 'wp-user-frontend' ),
                    'type'  => 'checkbox',
                ],
                [
                    'name'    => 'currency',
                    'label'   => __( 'Currency', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'USD',
                    'options' => $currencies,
                ],
                [
                    'name'    => 'currency_position',
                    'label'   => __( 'Currency Position', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'default' => 'left',
                    'options' => [
                        'left'        => sprintf( '%1$s (%2$s99.99)', __( 'Left', 'wp-user-frontend' ), $default_currency_symbol ),
                        'right'       => sprintf( '%1$s (99.99%2$s)', __( 'Right', 'wp-user-frontend' ), $default_currency_symbol ),
                        'left_space'  => sprintf( '%1$s (%2$s 99.99)', __( 'Left with space', 'wp-user-frontend' ), $default_currency_symbol ),
                        'right_space' => sprintf( '%1$s (99.99 %2$s)', __( 'Right with space', 'wp-user-frontend' ), $default_currency_symbol ),
                    ],
                ],
                [
                    'name'       => 'wpuf_price_thousand_sep',
                    'label'      => __( 'Thousand Separator', 'wp-user-frontend' ),
                    'desc'       => __( 'This sets the thousand separator of displayed prices.', 'wp-user-frontend' ),
                    'css'        => 'width:50px;',
                    'default'    => ',',
                    'type'       => 'text',
                    'desc_tip'   => true,
                ],
                [
                    'name'       => 'wpuf_price_decimal_sep',
                    'label'      => __( 'Decimal Separator', 'wp-user-frontend' ),
                    'desc'       => __( 'This sets the decimal separator of displayed prices.', 'wp-user-frontend' ),
                    'default'    => '.',
                    'type'       => 'text',
                ],

                [
                    'name'              => 'wpuf_price_num_decimals',
                    'label'             => __( 'Number of Decimals', 'wp-user-frontend' ),
                    'desc'              => __( 'This sets the number of decimal points shown in displayed prices.', 'wp-user-frontend' ),
                    'default'           => '2',
                    'type'              => 'number',
                    'custom_attributes' => [
                        'min'  => 0,
                        'step' => 1,
                    ],
                ],
                [
                    'name'    => 'sandbox_mode',
                    'label'   => __( 'Enable demo/sandbox mode', 'wp-user-frontend' ),
                    'desc'    => __( 'When sandbox mode is active, all payment gateway will be used in demo mode', 'wp-user-frontend' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ],
                [
                    'name'    => 'payment_page',
                    'label'   => __( 'Payment Page', 'wp-user-frontend' ),
                    'desc'    => __( 'This page will be used to process payment options', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'payment_success',
                    'label'   => __( 'Payment Success Page', 'wp-user-frontend' ),
                    'desc'    => __( 'After payment users will be redirected here', 'wp-user-frontend' ),
                    'type'    => 'select',
                    'options' => $pages,
                ],
                [
                    'name'    => 'active_gateways',
                    'label'   => __( 'Payment Gateways', 'wp-user-frontend' ),
                    'desc'    => __( 'Active payment gateways', 'wp-user-frontend' ),
                    'type'    => 'multicheck',
                    'options' => wpuf_get_gateways(),
                ],
                [
                    'name'    => 'failed_retry',
                    'label'   => __( 'Retry Failed Payment', 'wp-user-frontend' ),
                    'desc'    => __( 'How many times should retry for failed payment max is 4', 'wp-user-frontend' ),
                    'default'           => '2',
                    'type'              => 'number',
                    'custom_attributes' => [
                        'min'  => 1,
                        'max'  => 4,
                        'step' => 1,
                    ],
                ],
            ]
        ),
        'wpuf_mails' => apply_filters(
            'wpuf_mail_options', [
                [
                    'name'    => 'guest_email_setting',
                    'label'   => __( '<span class="dashicons dashicons-universal-access-alt"></span> Guest Email', 'wp-user-frontend' ),
                    'type'    => 'html',
                    'class'   => 'guest-email-setting',
                ],
                [
                    'name'     => 'enable_guest_email_notification',
                    'class'    => 'guest-email-setting-option',
                    'label'    => __( 'Guest Email Notification', 'wp-user-frontend' ),
                    'desc'     => __( 'Enable Guest Email Notification .', 'wp-user-frontend' ),
                    'default'  => 'on',
                    'type'     => 'checkbox',
                ],
                [
                    'name'    => 'guest_email_subject',
                    'label'   => __( 'Guest mail subject', 'wp-user-frontend' ),
                    'desc'    => __( 'This sets the subject of the emails sent to guest users', 'wp-user-frontend' ),
                    'default' => 'Please Confirm Your Email to Get the Post Published!',
                    'type'    => 'text',
                    'class'   => 'guest-email-setting-option',
                ],
                [
                    'name'    => 'guest_email_body',
                    'label'   => __( 'Guest mail body', 'wp-user-frontend' ),
                    'desc'    => __( "This sets the body of the emails sent to guest users. Please DON'T edit the <code>{activation_link}</code> part, you can use {sitename} too.", 'wp-user-frontend' ),
                    'default' => 'Hey There,

                We just received your guest post and now we want you to confirm your email so that we can verify the content and move on to the publishing process.

                Please click the link below to verify:
                {activation_link}

                Regards,
                {sitename}',
                    'type'    => 'wysiwyg',
                    'class'   => 'guest-email-setting-option',
                ],
            ]
        ),
        'wpuf_privacy' => apply_filters(
            'wpuf_privacy_options', [
                [
                    'name'    => 'export_post_types',
                    'label'   => __( 'Post Types', 'wp-user-frontend' ),
                    'desc'    => __( 'Select the post types you will allow users to export.', 'wp-user-frontend' ),
                    'callback' => 'wpuf_settings_multiselect',
                    'options' => $post_types,
                ],
            ]
        ),
    ];

    return apply_filters( 'wpuf_settings_fields', $settings_fields );
}

function wpuf_settings_field_profile( $form ) {
    $user_roles = wpuf_get_user_roles();
    $forms      = get_posts(
        [
            'numberposts' => -1,
            'post_type'   => 'wpuf_profile',
        ]
    );

    $val = get_option( 'wpuf_profile', [] );

    if ( class_exists( 'WP_User_Frontend_Pro' ) ) {
        ?>

    <p style="padding-left: 10px; font-style: italic; font-size: 13px;">
        <strong><?php esc_html_e( 'Select profile/registration forms for user roles. These forms will be used to populate extra edit profile fields in backend.', 'wp-user-frontend' ); ?></strong>
    </p>
    <table class="form-table">
        <?php
        foreach ( $user_roles as $role => $name ) {
            $current = isset( $val['roles'][ $role ] ) ? $val['roles'][ $role ] : '';
            ?>
            <tr valign="top">
                <th scrope="row"><?php echo esc_attr( $name ); ?></th>
                <td>
                    <select name="wpuf_profile[roles][<?php echo esc_attr( $role ); ?>]">
                        <option value=""><?php esc_html_e( '&mdash; Select &mdash;', 'wp-user-frontend' ); ?></option>
                        <?php foreach ( $forms as $form ) { ?>
                            <option value="<?php echo esc_attr( $form->ID ); ?>"<?php selected( $current, $form->ID ); ?>><?php echo esc_html( $form->post_title ); ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
        <?php
    }
}

add_action( 'wsa_form_bottom_wpuf_profile', 'wpuf_settings_field_profile' );
