<?php

namespace ProfilePress\Core;

use ProfilePress\Core\Admin\SettingsPages\AdminFooter;
use ProfilePress\Core\Admin\SettingsPages\ExtensionsSettingsPage;
use ProfilePress\Core\Admin\SettingsPages\IDUserColumn;
use ProfilePress\Core\Admin\SettingsPages\MemberDirectories;
use ProfilePress\Core\Admin\SettingsPages\ToolsSettingsPage;
use ProfilePress\Core\Classes\GlobalSiteAccess;
use ProfilePress\Core\ContentProtection;
use ProfilePress\Core\Admin\SettingsPages\EmailSettings\DefaultTemplateCustomizer;
use ProfilePress\Core\Admin\SettingsPages\EmailSettings\EmailSettingsPage;
use ProfilePress\Core\Admin\SettingsPages\Forms;
use ProfilePress\Core\Admin\SettingsPages\GeneralSettings;
use ProfilePress\Core\Classes\AdminNotices;
use ProfilePress\Core\Classes\AjaxHandler;
use ProfilePress\Core\Classes\BuddyPressBbPress;
use ProfilePress\Core\Classes\FormPreviewHandler;
use ProfilePress\Core\Classes\GDPR;
use ProfilePress\Core\Classes\Miscellaneous;
use ProfilePress\Core\Classes\ModifyRedirectDefaultLinks;
use ProfilePress\Core\Classes\PPRESS_Session;
use ProfilePress\Core\Classes\ProfileUrlRewrite;
use ProfilePress\Core\Classes\UserAvatar;
use ProfilePress\Core\Classes\UsernameEmailRestrictLogin;
use ProfilePress\Core\Classes\UserSignupLocationListingPage;
use ProfilePress\Core\ShortcodeParser\Builder\EditProfileBuilder;
use ProfilePress\Core\ShortcodeParser\Builder\GlobalShortcodes;
use ProfilePress\Core\ShortcodeParser\Builder\PasswordResetBuilder;
use ProfilePress\Core\ShortcodeParser\Builder\RegistrationFormBuilder;
use ProfilePress\Core\ShortcodeParser\EditProfileTag;
use ProfilePress\Core\ShortcodeParser\FrontendProfileTag;
use ProfilePress\Core\ShortcodeParser\Builder\LoginFormBuilder;
use ProfilePress\Core\ShortcodeParser\LoginFormTag;
use ProfilePress\Core\ShortcodeParser\MelangeTag;
use ProfilePress\Core\ShortcodeParser\MemberDirectoryTag;
use ProfilePress\Core\ShortcodeParser\MyAccount\MyAccountTag;
use ProfilePress\Core\ShortcodeParser\PasswordResetTag;
use ProfilePress\Core\ShortcodeParser\RegistrationFormTag;
use ProfilePress\Core\Widgets\Init as WidgetsInit;

define('PROFILEPRESS_SRC', plugin_dir_path(PROFILEPRESS_SYSTEM_FILE_PATH) . 'src/');
define('PPRESS_ADMIN_SETTINGS_PAGE_FOLDER', PROFILEPRESS_SRC . 'Admin/SettingsPages/');

define('PPRESS_ERROR_LOG_FOLDER', WP_CONTENT_DIR . "/uploads/profilepress-logs/");

define('PPRESS_SETTINGS_SLUG', 'pp-config');
define('PPRESS_FORMS_SETTINGS_SLUG', 'pp-forms');
define('PPRESS_MEMBER_DIRECTORIES_SLUG', 'pp-directories');

define('PPRESS_CONTENT_PROTECTION_SETTINGS_SLUG', 'pp-content-protection');
define('PPRESS_EXTENSIONS_SETTINGS_SLUG', 'pp-extensions');

define('PPRESS_SETTINGS_SETTING_PAGE', admin_url('admin.php?page=' . PPRESS_SETTINGS_SLUG));
define('PPRESS_CUSTOM_FIELDS_SETTINGS_PAGE', add_query_arg(['view' => 'custom-fields'], PPRESS_SETTINGS_SETTING_PAGE));
define('PPRESS_CONTACT_INFO_SETTINGS_PAGE', add_query_arg(['view' => 'custom-fields', 'type' => 'contact-info'], PPRESS_SETTINGS_SETTING_PAGE));
define('PPRESS_SETTINGS_EMAIL_SETTING_PAGE', add_query_arg('view', 'email', PPRESS_SETTINGS_SETTING_PAGE));
define('PPRESS_FORMS_SETTINGS_PAGE', admin_url('admin.php?page=' . PPRESS_FORMS_SETTINGS_SLUG));
define('PPRESS_MEMBER_DIRECTORIES_SETTINGS_PAGE', admin_url('admin.php?page=' . PPRESS_MEMBER_DIRECTORIES_SLUG));
define('PPRESS_USER_PROFILES_SETTINGS_PAGE', add_query_arg('form-type', 'user-profile', PPRESS_FORMS_SETTINGS_PAGE));
define('PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE', admin_url('admin.php?page=' . PPRESS_CONTENT_PROTECTION_SETTINGS_SLUG));
define('PPRESS_EXTENSIONS_SETTINGS_PAGE', admin_url('admin.php?page=' . PPRESS_EXTENSIONS_SETTINGS_SLUG));

define('PPRESS_LICENSE_SETTINGS_PAGE', add_query_arg('view', 'license', PPRESS_SETTINGS_SETTING_PAGE));

define('PPRESS_SETTINGS_DB_OPTION_NAME', 'ppress_settings_data');
define('PPRESS_FORMS_DB_OPTION_NAME', 'pp_forms');
define('PPRESS_CONTACT_INFO_OPTION_NAME', 'ppress_contact_info');

define('PPRESS_ASSETS_URL', plugin_dir_url(PROFILEPRESS_SYSTEM_FILE_PATH) . 'assets');

// Directory for uploaded avatar
define("PPRESS_AVATAR_UPLOAD_DIR", apply_filters('ppress_avatar_folder', WP_CONTENT_DIR . '/uploads/pp-avatar/'));
define("PPRESS_COVER_IMAGE_UPLOAD_DIR", apply_filters('ppress_cover_image_folder', WP_CONTENT_DIR . '/uploads/pp-avatar/cover/'));

define("PPRESS_AVATAR_UPLOAD_URL", apply_filters('ppress_avatar_url', WP_CONTENT_URL . '/uploads/pp-avatar/'));
define("PPRESS_COVER_IMAGE_UPLOAD_URL", apply_filters('ppress_cover_image_url', WP_CONTENT_URL . '/uploads/pp-avatar/cover/'));

// Directory for file custom fields
define("PPRESS_FILE_UPLOAD_DIR", apply_filters('ppress_files_folder', WP_CONTENT_DIR . '/uploads/pp-files/'));
define("PPRESS_FILE_UPLOAD_URL", apply_filters('ppress_files_url', WP_CONTENT_URL . '/uploads/pp-files/'));

class Base
{
    // core contact info fields
    const cif_facebook = 'facebook';
    const cif_twitter = 'twitter';
    const cif_linkedin = 'linkedin';
    const cif_youtube = 'youtube';
    const cif_vk = 'vk';
    const cif_instagram = 'instagram';
    const cif_github = 'github';

    public function __construct()
    {
        register_activation_hook(PROFILEPRESS_SYSTEM_FILE_PATH, ['ProfilePress\Core\RegisterActivation\Base', 'run_install']);

        if (version_compare(get_bloginfo('version'), '5.1', '<')) {
            add_action('wpmu_new_blog', ['ProfilePress\Core\RegisterActivation\Base', 'multisite_new_blog_install']);
        } else {
            add_action('wp_insert_site', function (\WP_Site $new_site) {
                RegisterActivation\Base::multisite_new_blog_install($new_site->blog_id);
            });
        }

        add_action('activate_blog', ['ProfilePress\Core\RegisterActivation\Base', 'multisite_new_blog_install']);

        add_filter('wpmu_drop_tables', array($this, 'wpmu_drop_tables'));

        add_action('admin_init', function () {
            if (isset($_GET['ppress_create_pages']) && $_GET['ppress_create_pages'] == 'true' && current_user_can('manage_options')) {
                check_admin_referer('ppress_create_pages', 'ppress_nonce');
                RegisterActivation\Base::create_pages();
                \PAnD::set_admin_notice_cache('ppress-create-plugin-pages-notice', 'forever');
                wp_safe_redirect(PPRESS_SETTINGS_SETTING_PAGE . '#global_settings');
                exit;
            }
        });

        // handles edge case where register activation isn't triggered especially when migrating from wp user avatar.
        add_action('admin_init', function () {
            if (get_option('ppress_plugin_activated') != 'true') {
                RegisterActivation\Base::run_install();
            }
        });

        do_action('ppress_loaded');

        GlobalSiteAccess::init();

        DefaultTemplateCustomizer::get_instance();

        RegisterScripts::get_instance();

        PPRESS_Session::get_instance();

        UserAvatar::get_instance();
        ModifyRedirectDefaultLinks::get_instance();
        UsernameEmailRestrictLogin::get_instance();
        BuddyPressBbPress::get_instance();
        AjaxHandler::get_instance();

        GlobalShortcodes::initialize();
        EditProfileBuilder::get_instance();
        LoginFormBuilder::get_instance();
        PasswordResetBuilder::get_instance();
        RegistrationFormBuilder::initialize();

        LoginFormTag::get_instance();
        EditProfileTag::get_instance();
        FrontendProfileTag::get_instance();
        MelangeTag::get_instance();
        PasswordResetTag::get_instance();
        RegistrationFormTag::get_instance();
        MyAccountTag::get_instance();
        MemberDirectoryTag::get_instance();

        Miscellaneous::get_instance();
        ProfileUrlRewrite::get_instance();

        WidgetsInit::init();

        AdminBarDashboardAccess\Init::get_instance();

        FormPreviewHandler::get_instance();

        ContentProtection\Init::get_instance();

        NavigationMenuLinks\Init::init();

        $this->admin_hooks();

        add_action('plugins_loaded', [$this, 'db_updates']);
    }

    public static function form_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'ppress_forms';
    }

    public static function form_meta_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'ppress_formsmeta';
    }

    public static function passwordless_login_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'ppress_passwordless';
    }

    public static function meta_data_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'ppress_meta_data';
    }

    public static function profile_fields_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'ppress_profile_fields';
    }

    public function admin_hooks()
    {
        if ( ! is_admin()) {
            return;
        }

        GeneralSettings::get_instance();
        EmailSettingsPage::get_instance();

        ToolsSettingsPage::get_instance();

        Forms::get_instance();
        MemberDirectories::get_instance();
        ExtensionsSettingsPage::get_instance();

        AdminNotices::get_instance();
        UserSignupLocationListingPage::get_instance();
        AdminFooter::get_instance();

        IDUserColumn::get_instance();

        GDPR::get_instance();

        do_action('ppress_admin_hooks');
    }

    public function db_updates()
    {
        if ( ! is_admin()) {
            return;
        }

        DBUpdates::get_instance()->maybe_update();
    }

    public function wpmu_drop_tables($tables)
    {
        $tables[] = Base::form_db_table();
        $tables[] = Base::form_meta_db_table();
        $tables[] = Base::meta_data_db_table();
        $tables[] = Base::passwordless_login_db_table();
        $tables[] = Base::profile_fields_db_table();

        $tables = apply_filters('ppress_drop_mu_database_tables', $tables);

        return $tables;
    }

    /**
     * Singleton.
     *
     * @return Base
     */
    public static function get_instance()
    {
        /** WP User Avatar Adapter STARTS */
        require dirname(PROFILEPRESS_SYSTEM_FILE_PATH) . '/deprecated/wp-user-avatar/wp-user-avatar.php';
        /** WP User Avatar Adapter ENDS */

        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}