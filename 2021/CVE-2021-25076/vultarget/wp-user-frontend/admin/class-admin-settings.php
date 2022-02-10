<?php

/**
 * WPUF settings
 */
class WPUF_Admin_Settings {

    /**
     * Settings API
     *
     * @var \WeDevs_Settings_API
     */
    private $settings_api;

    /**
     * Static instance of this class
     *
     * @var \self
     */
    private static $_instance;

    /**
     * Public instance of this class
     *
     * @var \self
     */
    public $subscribers_list_table_obj;

    /**
     * The menu page hooks
     *
     * Used for checking if any page is under WPUF menu
     *
     * @var array
     */
    private $menu_pages = [];

    public function __construct() {
        if ( ! class_exists( 'WeDevs_Settings_API' ) ) {
            require_once dirname( __DIR__ ) . '/lib/class.settings-api.php';
        }

        $this->settings_api = new WeDevs_Settings_API();

        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        add_filter( 'parent_file', [ $this, 'fix_parent_menu' ] );
        add_filter( 'submenu_file', [ $this, 'fix_submenu_file' ] );

        add_action( 'admin_init', [ $this, 'handle_tools_action' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );

        add_filter( 'wp_handle_upload_prefilter', [ $this, 'enable_json_upload' ], 1 );
        add_action( 'wp_ajax_wpuf_import_forms', [ $this, 'import_forms' ] );

        add_filter( 'upload_mimes', [ $this, 'add_json_mime_type' ] );
    }

    public static function init() {
        if ( ! self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function admin_init() {
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Register the admin menu
     *
     * @since 1.0
     */
    public function admin_menu() {
        global $_registered_pages;

        $capability = wpuf_admin_role();

        // Translation issue: Hook name change due to translate menu title
        $this->menu_pages[] = add_menu_page( __( 'WP User Frontend', 'wp-user-frontend' ), __( 'User Frontend', 'wp-user-frontend' ), $capability, 'wp-user-frontend', [ $this, 'wpuf_post_forms_page' ], 'data:image/svg+xml;base64,' . base64_encode( '<svg width="83px" height="76px" viewBox="0 0 83 76" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="wpuf-icon" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="ufp" fill-rule="nonzero" fill="#9EA3A8"><path d="M49.38,51.88 C49.503348,56.4604553 45.8999295,60.2784694 41.32,60.42 C36.7400705,60.2784694 33.136652,56.4604553 33.26,51.88 L33.26,40.23 L19,40.23 L19,51.88 C19,64.77 29,75.25 41.36,75.26 L41.36,75.26 C47.3622079,75.2559227 53.0954073,72.7693647 57.2,68.39 C61.4213559,63.9375842 63.7575868,58.0253435 63.72,51.89 L63.72,40.23 L49.38,40.23 L49.38,51.88 Z" id="Shape"></path><polygon id="Shape" points="32.96 0.59 0 0.59 3.77 16.68 32.96 16.68"></polygon><path d="M68,0 L49.75,0 L49.75,16.1 L68,16.1 C68.74,16.1 69.39,17.1 69.39,18.24 C69.39,19.38 68.74,20.38 68,20.38 L49.75,20.38 L49.75,36.5 L68,36.5 C76,36.5 82.5,28.31 82.5,18.25 C82.5,8.19 76,0 68,0 Z" id="Shape"></path><polygon id="Shape" points="32.96 20.41 5.31 20.41 9.07 36.5 32.96 36.5"></polygon></g></g></svg>' ), '54.2' );

        $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Post Forms', 'wp-user-frontend' ), __( 'Post Forms', 'wp-user-frontend' ), $capability, 'wpuf-post-forms', [ $this, 'wpuf_post_forms_page' ] );
        remove_submenu_page( 'wp-user-frontend', 'wp-user-frontend' );

        /*
         * @since 2.3
         */
        do_action( 'wpuf_admin_menu_top' );

        if ( 'on' === wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' ) ) {
            $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Subscriptions', 'wp-user-frontend' ), __( 'Subscriptions', 'wp-user-frontend' ), $capability, 'edit.php?post_type=wpuf_subscription' );
        }

        do_action( 'wpuf_admin_menu' );

        if ( 'on' === wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' ) ) {
            $transactions_page = add_submenu_page( 'wp-user-frontend', __( 'Transactions', 'wp-user-frontend' ), __( 'Transactions', 'wp-user-frontend' ), $capability, 'wpuf_transaction', [ $this, 'transactions_page' ] );
        }

        $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Tools', 'wp-user-frontend' ), __( 'Tools', 'wp-user-frontend' ), $capability, 'wpuf_tools', [ $this, 'tools_page' ] );

        do_action( 'wpuf_admin_menu_bottom' );

        if ( ! class_exists( 'WP_User_Frontend_Pro' ) ) {
            $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Premium', 'wp-user-frontend' ), __( 'Premium', 'wp-user-frontend' ), $capability, 'wpuf_premium', [ $this, 'premium_page' ] );
        }
        $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Help', 'wp-user-frontend' ), __( '<span style="color:#f18500">Help</span>', 'wp-user-frontend' ), $capability, 'wpuf-support', [ $this, 'support_page' ] );
        $this->menu_pages[] = add_submenu_page( 'wp-user-frontend', __( 'Settings', 'wp-user-frontend' ), __( 'Settings', 'wp-user-frontend' ), $capability, 'wpuf-settings', [ $this, 'plugin_page' ] );

        $this->menu_pages[] = add_submenu_page( 'edit.php?post_type=wpuf_subscription', __( 'Subscribers', 'wp-user-frontend' ), __( 'Subscribers', 'wp-user-frontend' ), $capability, 'wpuf_subscribers', [ $this, 'subscribers_page' ] );
        //phpcs:ignore
        $_registered_pages['user-frontend_page_wpuf_subscribers'] = true; // hack to work the nested subscribers page

        // manually add subsription page
        $this->menu_pages[] = 'edit-wpuf_subscription';
        $this->menu_pages[] = 'wpuf_subscribers';
        $this->menu_pages[] = 'user-frontend_page_wpuf_transaction';

        if ( 'on' === wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' ) ) {
            add_action( "load-$transactions_page", [ $this, 'transactions_screen_option' ] );
            // add_action( "load-wpuf_subscribers", array( $this, 'subscribers_screen_option' ) );
        }
    }

    /**
     * WPUF Settings sections
     *
     * @since 1.0
     *
     * @return array
     */
    public function get_settings_sections() {
        return wpuf_settings_sections();
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
        return wpuf_settings_fields();
    }

    public function plugin_page() {
        ?>
        <div class="wrap">

            <h2 style="margin-bottom: 15px;"><?php esc_html_e( 'Settings', 'wp-user-frontend' ); ?></h2>
            <div class="wpuf-settings-wrap">
                <?php
                settings_errors();

                $this->settings_api->show_navigation();
                $this->settings_api->show_forms();
                ?>
            </div>
            <script>
                (function () {
                document.addEventListener('DOMContentLoaded',function () {
                    var tabs    = document.querySelector('.wpuf-settings-wrap').querySelectorAll('h2 a');
                    var content = document.querySelectorAll('.wpuf-settings-wrap .metabox-holder th');
                    var close   = document.querySelector('#wpuf-search-section span');

                    var search_input = document.querySelector('#wpuf-settings-search');

                    search_input.addEventListener('keyup', function (e) {
                        var search_value = e.target.value.toLowerCase();
                        var value_tab  = [];

                        if ( search_value.length ) {
                            close.style.display = 'flex'
                            content.forEach(function (row, index) {

                                var content_id = row.closest('div').getAttribute('id');
                                var tab_id     = content_id + '-tab';
                                var found_value = row.innerText.toLowerCase().includes( search_value );

                                if ( found_value ){
                                    row.closest('tr').style.display = 'table-row';
                                }else {
                                    row.closest('tr').style.display = 'none';
                                }

                                if ( 'wpuf_mails' === content_id ){
                                    row.closest('tbody').querySelectorAll('tr').forEach(function (tr) {
                                        tr.style.display = '';
                                    });
                                }

                                if ( found_value === true && ! value_tab.includes( tab_id ) ) {
                                    value_tab.push(tab_id);
                                }
                            })

                            if ( value_tab.length ) {
                                document.getElementById(value_tab[0]).click();
                            }

                            tabs.forEach(function (tab) {
                                var tab_id = tab.getAttribute('id');
                                if ( ! value_tab.includes( tab_id ) ){
                                    document.getElementById(tab_id).style.display = 'none';
                                }else {
                                    document.getElementById(tab_id).style.display = 'block';
                                }
                            })

                        }else {
                            wpuf_search_reset();
                        }
                    })

                    close.addEventListener('click',function (event) {
                        wpuf_search_reset();
                        search_input.value = '';
                        close.style.display = 'none';
                    })

                    function wpuf_search_reset() {
                        content.forEach(function (row, index) {
                            var content_id = row.closest('div').getAttribute('id');
                            var tab_id     = content_id + '-tab';
                            document.getElementById(content_id).style.display = '';
                            document.getElementById(tab_id).style.display = '';
                            document.getElementById('wpuf_general-tab').click();
                        })
                        document.querySelector('.wpuf-settings-wrap .metabox-holder').querySelectorAll('tr').forEach(function (row) {
                                row.style.display = '';
                        });

                    }
                });
                })();
            </script>
        </div>
        <?php
    }

    public function transactions_page() {
        require_once dirname( __DIR__ ) . '/admin/transactions.php';
    }

    /**
     * Callback method for Post Forms submenu
     *
     * @since 2.5
     *
     * @return void
     */
    public function wpuf_post_forms_page() {
        $action           = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : null;
        $add_new_page_url = admin_url( 'admin.php?page=wpuf-post-forms&action=add-new' );

        switch ( $action ) {
            case 'edit':
                require_once WPUF_ROOT . '/views/post-form.php';
                break;

            case 'add-new':
                require_once WPUF_ROOT . '/views/post-form.php';
                break;

            default:
                require_once WPUF_ROOT . '/admin/post-forms-list-table-view.php';
                break;
        }
    }

    public function subscribers_page( $post_ID ) {
        include dirname( __DIR__ ) . '/admin/subscribers.php';
    }

    public function premium_page() {
        require_once dirname( __DIR__ ) . '/admin/premium.php';
    }

    public function tools_page() {
        $this->enqueue_tools_scripts();
        include dirname( __DIR__ ) . '/admin/tools.php';
    }

    public function support_page() {
        require_once dirname( __DIR__ ) . '/admin/html/support.php';
    }

    /**
     * Check if the current page is a settings/menu page
     *
     * @param string $screen_id
     *
     * @return bool
     */
    public function is_admin_menu_page( $screen ) {
        if ( $screen && in_array( $screen->id, $this->menu_pages, true ) ) {
            return true;
        }

        return false;
    }

    /**
     * Highlight the proper top level menu
     *
     * @see http://wordpress.org/support/topic/moving-taxonomy-ui-to-another-main-menu?replies=5#post-2432769
     *
     * @global obj $current_screen
     *
     * @param string $parent_file
     *
     * @return string
     */
    public function fix_parent_menu( $parent_file ) {
        $current_screen = get_current_screen();

        $post_types = [ 'wpuf_forms', 'wpuf_profile', 'wpuf_subscription', 'wpuf_coupon' ];

        if ( in_array( $current_screen->post_type, $post_types, true ) ) {
            $parent_file = 'wp-user-frontend';
        }

        if ( 'wpuf_subscription' === $current_screen->post_type && $current_screen->base === 'admin_page_the-slug' ) {
            $parent_file = 'wp-user-frontend';
        }

        return $parent_file;
    }

    /**
     * Fix the submenu class in admin menu
     *
     * @since 2.6.0
     *
     * @param string $submenu_file
     *
     * @return string
     */
    public function fix_submenu_file( $submenu_file ) {
        $current_screen = get_current_screen();

        if ( 'wpuf_subscription' === $current_screen->post_type && $current_screen->base === 'admin_page_wpuf_subscribers' ) {
            $submenu_file = 'edit.php?post_type=wpuf_subscription';
        }

        return $submenu_file;
    }

    /**
     * Hanlde tools page action
     *
     * @return void
     */
    public function handle_tools_action() {
        if ( ! isset( $_GET['wpuf_action'] ) ) {
            return;
        }

        check_admin_referer( 'wpuf-tools-action' );

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wpdb;

        $action  = isset( $_GET['wpuf_action'] ) ? sanitize_text_field( wp_unslash( $_GET['wpuf_action'] ) ) : '';
        $message = 'del_forms';

        switch ( $action ) {
            case 'clear_settings':
                delete_option( 'wpuf_general' );
                delete_option( 'wpuf_dashboard' );
                delete_option( 'wpuf_profile' );
                delete_option( 'wpuf_payment' );
                delete_option( '_wpuf_page_created' );

                $message = 'settings_cleared';
                break;

            case 'del_post_forms':
                $this->delete_post_type( 'wpuf_forms' );
                break;

            case 'del_pro_forms':
                $this->delete_post_type( 'wpuf_profile' );
                break;

            case 'del_subs':
                $this->delete_post_type( 'wpuf_subscription' );
                break;

            case 'del_coupon':
                $this->delete_post_type( 'wpuf_coupon' );
                break;

            case 'clear_transaction':
                $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wpuf_transaction" );

                $message = 'del_trans';
                break;

            default:
                // code...
                break;
        }

        wp_redirect( add_query_arg( [ 'msg' => $message ], admin_url( 'admin.php?page=wpuf_tools&action=tools' ) ) );
        exit;
    }

    /**
     * Delete all posts by a post type
     *
     * @param string $post_type
     *
     * @return void
     */
    public function delete_post_type( $post_type ) {
        $query = new WP_Query(
            [
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'post_status'    => [ 'publish', 'draft', 'pending', 'trash' ],
            ]
        );

        $posts = $query->get_posts();

        if ( $posts ) {
            foreach ( $posts as $item ) {
                wp_delete_post( $item->ID, true );
            }
        }

        wp_reset_postdata();
    }

    /**
     * Screen options.
     *
     * @return void
     */
    public function transactions_screen_option() {
        $option = 'per_page';
        $args   = [
            'label'   => __( 'Number of items per page:', 'wp-user-frontend' ),
            'default' => 20,
            'option'  => 'transactions_per_page',
        ];

        add_screen_option( $option, $args );

        if ( ! class_exists( 'WPUF_Transactions_List_Table' ) ) {
            require_once WPUF_ROOT . '/class/transactions-list-table.php';
        }

        $this->transactions_list_table_obj = new WPUF_Transactions_List_Table();
    }

    /**
     * Enqueue styles
     *
     * @return void
     */
    public function enqueue_styles() {
        if ( ! $this->is_admin_menu_page( get_current_screen() ) && get_current_screen()->parent_base === 'edit' ) {
            return;
        }

        wp_enqueue_style( 'wpuf-admin', WPUF_ASSET_URI . '/css/admin.css', false, WPUF_VERSION );
        wp_enqueue_script( 'wpuf-admin-script', WPUF_ASSET_URI . '/js/wpuf-admin.js', [ 'jquery' ], WPUF_VERSION, false );

        wp_localize_script(
            'wpuf-admin-script', 'wpuf_admin_script', [
                'ajaxurl'               => admin_url( 'admin-ajax.php' ),
                'nonce'                 => wp_create_nonce( 'wpuf_nonce' ),
                'cleared_schedule_lock' => __( 'Post lock has been cleared', 'wp-user-frontend' ),
            ]
        );
    }

    /**
     * Enqueue Tools page scripts
     *
     * @since 3.2.0
     *
     * @todo Move this method to WPUF_Admin_Tools class
     *
     * @return void
     */
    private function enqueue_tools_scripts() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_script( 'wpuf-vue', WPUF_ASSET_URI . '/vendor/vue/vue' . $prefix . '.js', [], WPUF_VERSION, true );

        wp_enqueue_media();

        wp_enqueue_script( 'wpuf-admin-tools', WPUF_ASSET_URI . '/js/wpuf-admin-tools.js', [ 'jquery', 'wpuf-vue' ], WPUF_VERSION, true );

        wp_localize_script(
            'wpuf-admin-tools', 'wpuf_admin_tools', [
                'url' => [
                    'ajax' => admin_url( 'admin-ajax.php' ),
                ],
                'nonce' => wp_create_nonce( 'wpuf_admin_tools' ),
                'i18n' => [
                    'wpuf_import_forms'      => __( 'WPUF Import Forms', 'wp-user-frontend' ),
                    'add_json_file'          => __( 'Add JSON file', 'wp-user-frontend' ),
                    'could_not_import_forms' => __( 'Could not import forms.', 'wp-user-frontend' ),
                ],
            ]
        );
    }

    /**
     * Add json file mime type to upload in WP Media
     *
     * @since 3.2.0
     *
     * @todo Move this method to WPUF_Admin_Tools class
     *
     * @param array $mime_types
     *
     * @return array
     */
    public function add_json_mime_type( $mime_types ) {
        $mime_types['json'] = 'application/json';

        return $mime_types;
    }

    /**
     * Allow json file to upload with async uploader
     *
     * @since 3.2.0
     *
     * @param array $info
     *
     * @return array
     */
    public function check_filetype_and_ext( $info ) {
        $info['ext']  = 'json';
        $info['type'] = 'application/json';

        return $info;
    }

    /**
     * Enable json file upload via ajax in tools page
     *
     * @since 3.2.0
     *
     * @todo Move this method to WPUF_Admin_Tools class
     *
     * @param array $file
     *
     * @return array
     */
    public function enable_json_upload( $file ) {
        if (
            defined( 'DOING_AJAX' )
            && DOING_AJAX
            && isset( $_POST['action'] )
            && 'upload-attachment' === $_POST['action']
            && isset( $_POST['type'] )
            && 'wpuf-form-uploader' === $_POST['type']
        ) {
            // @see wp_ajax_upload_attachment
            check_ajax_referer( 'media-form' );
            add_filter( 'wp_check_filetype_and_ext', [ $this, 'check_filetype_and_ext' ] );
        }

        return $file;
    }

    /**
     * Ajax handler to import WPUF form
     *
     * @since 3.2.0
     *
     * @todo Move this method to WPUF_Admin_Tools class
     *
     * @return void
     */
    public function import_forms() {
        check_ajax_referer( 'wpuf_admin_tools' );

        if ( ! isset( $_POST['file_id'] ) ) {
            wp_send_json_error(
                new WP_Error( 'wpuf_ajax_import_forms_error', __( 'Missing file_id param', 'wp-user-frontend' ) ),
                WP_Http::BAD_REQUEST
            );
        }

        $file_id = absint( wp_unslash( $_POST['file_id'] ) );
        $file    = get_attached_file( $file_id );

        if ( empty( $file ) ) {
            wp_send_json_error(
                new WP_Error( 'wpuf_ajax_import_forms_error', __( 'JSON file not found', 'wp-user-frontend' ) ),
                WP_Http::NOT_FOUND
            );
        }

        $filetype = wp_check_filetype( $file, [ 'json' => 'application/json' ] );

        if ( ! isset( $filetype['type'] ) || 'application/json' !== $filetype['type'] ) {
            wp_send_json_error(
                new WP_Error( 'wpuf_ajax_import_forms_error', __( 'Provided file is not a JSON file.', 'wp-user-frontend' ) ),
                WP_Http::UNSUPPORTED_MEDIA_TYPE
            );
        }

        if ( ! class_exists( 'WPUF_Admin_Tools' ) ) {
            require_once WPUF_ROOT . '/admin/class-tools.php';
        }

        $imported = WPUF_Admin_Tools::import_json_file( $file );

        if ( is_wp_error( $imported ) ) {
            wp_send_json_error( $imported, WP_Http::UNPROCESSABLE_ENTITY );
        }

        wp_send_json_success(
            [
                'message' => __( 'Forms imported successfully.', 'wp-user-frontend' ),
            ]
        );
    }
}
