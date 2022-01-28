<?php

namespace ProfilePress\Core\Classes;

class ExtensionManager
{
    const DB_OPTION_NAME = 'ppress_extension_manager';

    const EMAIL_CONFIRMATION = 'email_confirmation';
    const JOIN_BUDDYPRESS_GROUPS = 'join_buddypress_groups';
    const BUDDYPRESS_SYNC = 'buddypress_sync';
    const MULTISITE = 'multisite';
    const WOOCOMMERCE = 'woocommerce';
    const AKISMET = 'akismet';
    const CAMPAIGN_MONITOR = 'campaign_monitor';
    const MAILCHIMP = 'mailchimp';
    const POLYLANG = 'polylang';
    const PASSWORDLESS_LOGIN = 'passwordless_login';
    const USER_MODERATION = 'user_moderation';
    const RECAPTCHA = 'recaptcha';
    const SOCIAL_LOGIN = 'social_login';
    const CUSTOM_FIELDS = 'custom_fields';

    public static function is_premium()
    {
        return defined('PROFILEPRESS_PRO_DETACH_LIBSODIUM');
    }

    public static function class_map()
    {
        return [
            self::EMAIL_CONFIRMATION     => 'ProfilePress\Libsodium\EmailConfirmation',
            self::JOIN_BUDDYPRESS_GROUPS => 'ProfilePress\Libsodium\BuddyPressJoinGroupSelect\Init',
            self::BUDDYPRESS_SYNC        => 'ProfilePress\Libsodium\BuddyPressProfileSync',
            self::MULTISITE              => 'ProfilePress\Libsodium\MultisiteIntegration\Init',
            self::WOOCOMMERCE            => 'ProfilePress\Libsodium\MultisiteIntegration\Init',
            self::AKISMET                => 'ProfilePress\Libsodium\AkismetIntegration',
            self::CAMPAIGN_MONITOR       => 'ProfilePress\Libsodium\CampaignMonitorIntegration\Init',
            self::MAILCHIMP              => 'ProfilePress\Libsodium\MailchimpIntegration\Init',
            self::POLYLANG               => 'ProfilePress\Libsodium\PolylangIntegration',
            self::PASSWORDLESS_LOGIN     => 'ProfilePress\Libsodium\PasswordlessLogin',
            self::USER_MODERATION        => 'ProfilePress\Libsodium\UserModeration\UserModeration',
            self::RECAPTCHA              => 'ProfilePress\Libsodium\Recaptcha\Init',
            self::SOCIAL_LOGIN           => 'ProfilePress\Libsodium\SocialLogin\Init',
            self::CUSTOM_FIELDS          => 'ProfilePress\Libsodium\CustomProfileFields\Init',
        ];
    }

    public static function available_extensions()
    {
        return apply_filters('ppress_available_extensions', [
            self::CUSTOM_FIELDS          => [
                'title'       => esc_html__('Custom Fields', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/custom-fields/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Collect unlimited additional information from users besides the standard profile data.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M567.938 243.908L462.25 85.374A48.003 48.003 0 0 0 422.311 64H153.689a48 48 0 0 0-39.938 21.374L8.062 243.908A47.994 47.994 0 0 0 0 270.533V400c0 26.51 21.49 48 48 48h480c26.51 0 48-21.49 48-48V270.533a47.994 47.994 0 0 0-8.062-26.625zM162.252 128h251.497l85.333 128H376l-32 64H232l-32-64H76.918l85.334-128z"></path></svg>'
            ],
            self::EMAIL_CONFIRMATION     => [
                'title'       => esc_html__('Email Confirmation', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/email-confirmation/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Ensure newly registered users confirm their email addresses before they can log in.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M567.938 243.908L462.25 85.374A48.003 48.003 0 0 0 422.311 64H153.689a48 48 0 0 0-39.938 21.374L8.062 243.908A47.994 47.994 0 0 0 0 270.533V400c0 26.51 21.49 48 48 48h480c26.51 0 48-21.49 48-48V270.533a47.994 47.994 0 0 0-8.062-26.625zM162.252 128h251.497l85.333 128H376l-32 64H232l-32-64H76.918l85.334-128z"></path></svg>'
            ],
            self::USER_MODERATION        => [
                'title'       => esc_html__('User Moderation', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/user-moderation/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Decide whether to approve newly registered users or not. You can also block and unblock users at any time.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M622.3 271.1l-115.2-45c-4.1-1.6-12.6-3.7-22.2 0l-115.2 45c-10.7 4.2-17.7 14-17.7 24.9 0 111.6 68.7 188.8 132.9 213.9 9.6 3.7 18 1.6 22.2 0C558.4 489.9 640 420.5 640 296c0-10.9-7-20.7-17.7-24.9zM496 462.4V273.3l95.5 37.3c-5.6 87.1-60.9 135.4-95.5 151.8zM224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm96 40c0-2.5.8-4.8 1.1-7.2-2.5-.1-4.9-.8-7.5-.8h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c6.8 0 13.3-1.5 19.2-4-54-42.9-99.2-116.7-99.2-212z"></path></svg>'
            ],
            self::SOCIAL_LOGIN           => [
                'title'       => esc_html__('Social Login', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/social-login/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Let users easily register/login to your site using their social network accounts (Facebook, Twitter, Google, LinkedIn, GitHub, VK).', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"></path></svg>'
            ],
            self::PASSWORDLESS_LOGIN     => [
                'title'       => esc_html__('Passwordless Login', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/passwordless-login/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Let users log in to your website via a one-time URL sent to their email addresses.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M416 448h-84c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h84c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32h-84c-6.6 0-12-5.4-12-12V76c0-6.6 5.4-12 12-12h84c53 0 96 43 96 96v192c0 53-43 96-96 96zm-47-201L201 79c-15-15-41-4.5-41 17v96H24c-13.3 0-24 10.7-24 24v96c0 13.3 10.7 24 24 24h136v96c0 21.5 26 32 41 17l168-168c9.3-9.4 9.3-24.6 0-34z"></path></svg>'
            ],
            self::RECAPTCHA              => [
                'title'       => esc_html__('Google reCAPTCHA', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/recaptcha/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Protect your forms against spam and bot attacks.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M224,192a16,16,0,1,0,16,16A16,16,0,0,0,224,192ZM466.5,83.68l-192-80A57.4,57.4,0,0,0,256.05,0a57.4,57.4,0,0,0-18.46,3.67l-192,80A47.93,47.93,0,0,0,16,128C16,326.5,130.5,463.72,237.5,508.32a48.09,48.09,0,0,0,36.91,0C360.09,472.61,496,349.3,496,128A48,48,0,0,0,466.5,83.68ZM384,256H371.88c-28.51,0-42.79,34.47-22.63,54.63l8.58,8.57a16,16,0,1,1-22.63,22.63l-8.57-8.58C306.47,313.09,272,327.37,272,355.88V368a16,16,0,0,1-32,0V355.88c0-28.51-34.47-42.79-54.63-22.63l-8.57,8.58a16,16,0,0,1-22.63-22.63l8.58-8.57c20.16-20.16,5.88-54.63-22.63-54.63H128a16,16,0,0,1,0-32h12.12c28.51,0,42.79-34.47,22.63-54.63l-8.58-8.57a16,16,0,0,1,22.63-22.63l8.57,8.58c20.16,20.16,54.63,5.88,54.63-22.63V112a16,16,0,0,1,32,0v12.12c0,28.51,34.47,42.79,54.63,22.63l8.57-8.58a16,16,0,0,1,22.63,22.63l-8.58,8.57C329.09,189.53,343.37,224,371.88,224H384a16,16,0,0,1,0,32Zm-96,0a16,16,0,1,0,16,16A16,16,0,0,0,288,256Z"></path></svg>'
            ],
            self::JOIN_BUDDYPRESS_GROUPS => [
                'title'        => esc_html__('Join BuddyPress Groups', 'wp-user-avatar'),
                'url'          => 'https://profilepress.net/addons/join-buddypress-groups/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description'  => esc_html__('Let users select the BuddyPress groups to join during registration.', 'wp-user-avatar'),
                'icon'         => '<span class="dashicons dashicons-buddicons-buddypress-logo"></span>',
                'is_available' => function () {
                    return class_exists('BuddyPress') ? true : esc_html__('BuddyPress plugin is not active', 'wp-user-avatar');
                }
            ],
            self::BUDDYPRESS_SYNC        => [
                'title'        => esc_html__('BuddyPress Profile Sync', 'wp-user-avatar'),
                'url'          => 'https://profilepress.net/addons/buddypress-profile-sync/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description'  => esc_html__('It provides a 2-way synchronization between WordPress profile fields and BuddyPress extended profile.', 'wp-user-avatar'),
                'icon'         => '<span class="dashicons dashicons-buddicons-buddypress-logo"></span>',
                'is_available' => function () {
                    return class_exists('BuddyPress') ? true : esc_html__('BuddyPress plugin is not active', 'wp-user-avatar');
                }
            ],
            self::MULTISITE              => [
                'title'        => esc_html__('Site Creation', 'wp-user-avatar'),
                'url'          => 'https://profilepress.net/addons/site-creation/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description'  => esc_html__('Allow users to create new sites on a multisite network via a registration form powered by ProfilePress.', 'wp-user-avatar'),
                'icon'         => '<span class="dashicons dashicons-networking"></span>',
                'is_available' => function () {
                    return is_multisite() ? true : esc_html__('This is not a multisite installation', 'wp-user-avatar');
                }
            ],
            self::WOOCOMMERCE            => [
                'title'        => esc_html__('WooCommerce', 'wp-user-avatar'),
                'url'          => 'https://profilepress.net/addons/woocommerce/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description'  => esc_html__('It allows you to manage WooCommerce billing and shipping fields, replaces WooCommerce login and edit account forms in checkout and “My Account” pages with that of ProfilePress.', 'wp-user-avatar'),
                'icon'         => '<span class="dashicons dashicons-cart"></span>',
                'is_available' => function () {
                    return class_exists('WooCommerce') ? true : esc_html__('WooCommerce is not active', 'wp-user-avatar');
                }
            ],
            self::MAILCHIMP              => [
                'title'       => esc_html__('Mailchimp', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/mailchimp/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Subscribe members to your Mailchimp audiences when they register and automatically sync profile changes with Mailchimp.', 'wp-user-avatar'),
                'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M330.61 243.52a36.15 36.15 0 0 1 9.3 0c1.66-3.83 1.95-10.43.45-17.61-2.23-10.67-5.25-17.14-11.48-16.13s-6.47 8.74-4.24 19.42c1.26 6 3.49 11.14 6 14.32zM277.05 252c4.47 2 7.2 3.26 8.28 2.13 1.89-1.94-3.48-9.39-12.12-13.09a31.44 31.44 0 0 0-30.61 3.68c-3 2.18-5.81 5.22-5.41 7.06.85 3.74 10-2.71 22.6-3.48 7-.44 12.8 1.75 17.26 3.71zm-9 5.13c-9.07 1.42-15 6.53-13.47 10.1.9.34 1.17.81 5.21-.81a37 37 0 0 1 18.72-1.95c2.92.34 4.31.52 4.94-.49 1.46-2.22-5.71-8-15.39-6.85zm54.17 17.1c3.38-6.87-10.9-13.93-14.3-7s10.92 13.88 14.32 6.97zm15.66-20.47c-7.66-.13-7.95 15.8-.26 15.93s7.98-15.81.28-15.96zm-218.79 78.9c-1.32.31-6 1.45-8.47-2.35-5.2-8 11.11-20.38 3-35.77-9.1-17.47-27.82-13.54-35.05-5.54-8.71 9.6-8.72 23.54-5 24.08 4.27.57 4.08-6.47 7.38-11.63a12.83 12.83 0 0 1 17.85-3.72c11.59 7.59 1.37 17.76 2.28 28.62 1.39 16.68 18.42 16.37 21.58 9a2.08 2.08 0 0 0-.2-2.33c.03.89.68-1.3-3.35-.39zm299.72-17.07c-3.35-11.73-2.57-9.22-6.78-20.52 2.45-3.67 15.29-24-3.07-43.25-10.4-10.92-33.9-16.54-41.1-18.54-1.5-11.39 4.65-58.7-21.52-83 20.79-21.55 33.76-45.29 33.73-65.65-.06-39.16-48.15-51-107.42-26.47l-12.55 5.33c-.06-.05-22.71-22.27-23.05-22.57C169.5-18-41.77 216.81 25.78 273.85l14.76 12.51a72.49 72.49 0 0 0-4.1 33.5c3.36 33.4 36 60.42 67.53 60.38 57.73 133.06 267.9 133.28 322.29 3 1.74-4.47 9.11-24.61 9.11-42.38s-10.09-25.27-16.53-25.27zm-316 48.16c-22.82-.61-47.46-21.15-49.91-45.51-6.17-61.31 74.26-75.27 84-12.33 4.54 29.64-4.67 58.49-34.12 57.81zM84.3 249.55C69.14 252.5 55.78 261.09 47.6 273c-4.88-4.07-14-12-15.59-15-13.01-24.85 14.24-73 33.3-100.21C112.42 90.56 186.19 39.68 220.36 48.91c5.55 1.57 23.94 22.89 23.94 22.89s-34.15 18.94-65.8 45.35c-42.66 32.85-74.89 80.59-94.2 132.4zM323.18 350.7s-35.74 5.3-69.51-7.07c6.21-20.16 27 6.1 96.4-13.81 15.29-4.38 35.37-13 51-25.35a102.85 102.85 0 0 1 7.12 24.28c3.66-.66 14.25-.52 11.44 18.1-3.29 19.87-11.73 36-25.93 50.84A106.86 106.86 0 0 1 362.55 421a132.45 132.45 0 0 1-20.34 8.58c-53.51 17.48-108.3-1.74-126-43a66.33 66.33 0 0 1-3.55-9.74c-7.53-27.2-1.14-59.83 18.84-80.37 1.23-1.31 2.48-2.85 2.48-4.79a8.45 8.45 0 0 0-1.92-4.54c-7-10.13-31.19-27.4-26.33-60.83 3.5-24 24.49-40.91 44.07-39.91l5 .29c8.48.5 15.89 1.59 22.88 1.88 11.69.5 22.2-1.19 34.64-11.56 4.2-3.5 7.57-6.54 13.26-7.51a17.45 17.45 0 0 1 13.6 2.24c10 6.64 11.4 22.73 11.92 34.49.29 6.72 1.1 23 1.38 27.63.63 10.67 3.43 12.17 9.11 14 3.19 1.05 6.15 1.83 10.51 3.06 13.21 3.71 21 7.48 26 12.31a16.38 16.38 0 0 1 4.74 9.29c1.56 11.37-8.82 25.4-36.31 38.16-46.71 21.68-93.68 14.45-100.48 13.68-20.15-2.71-31.63 23.32-19.55 41.15 22.64 33.41 122.4 20 151.37-21.35.69-1 .12-1.59-.73-1-41.77 28.58-97.06 38.21-128.46 26-4.77-1.85-14.73-6.44-15.94-16.67 43.6 13.49 71 .74 71 .74s2.03-2.79-.56-2.53zm-68.47-5.7zm-83.4-187.5c16.74-19.35 37.36-36.18 55.83-45.63a.73.73 0 0 1 1 1c-1.46 2.66-4.29 8.34-5.19 12.65a.75.75 0 0 0 1.16.79c11.49-7.83 31.48-16.22 49-17.3a.77.77 0 0 1 .52 1.38 41.86 41.86 0 0 0-7.71 7.74.75.75 0 0 0 .59 1.19c12.31.09 29.66 4.4 41 10.74.76.43.22 1.91-.64 1.72-69.55-15.94-123.08 18.53-134.5 26.83a.76.76 0 0 1-1-1.12z"></path></svg>'
            ],
            self::CAMPAIGN_MONITOR       => [
                'title'       => esc_html__('Campaign Monitor', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/campaign-monitor/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Subscribe members to your Campaign Monitor lists when they register and automatically sync profile changes with Campaign Monitor.', 'wp-user-avatar'),
                'icon'        => '<span class="dashicons dashicons-email"></span>'
            ],
            self::AKISMET                => [
                'title'       => esc_html__('Akismet', 'wp-user-avatar'),
                'url'         => 'https://profilepress.net/addons/akismet/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description' => esc_html__('Block spam and bot user registrations with Akismet and keep your membership site safe and secured.', 'wp-user-avatar'),
                'icon'        => '<span class="dashicons dashicons-shield"></span>'
            ],
            self::POLYLANG               => [
                'title'        => esc_html__('Polylang', 'wp-user-avatar'),
                'url'          => 'https://profilepress.net/addons/polylang/?utm_source=liteplugin&utm_medium=extension-page&utm_campaign=learn-more',
                'description'  => esc_html__('It allows you to build multilingual login, registration, password reset and edit profile forms.', 'wp-user-avatar'),
                'icon'         => '<span class="dashicons dashicons-flag"></span>',
                'is_available' => function () {
                    return class_exists('Polylang') ? true : esc_html__('Polylang plugin is not active', 'wp-user-avatar');
                }
            ],
        ]);
    }

    public static function is_enabled($extension_id)
    {
        return ExtensionManager::is_premium() &&
               class_exists(ppress_var(self::class_map(), $extension_id)) &&
               ppress_var(get_option(self::DB_OPTION_NAME, []), $extension_id) == 'true';
    }
}