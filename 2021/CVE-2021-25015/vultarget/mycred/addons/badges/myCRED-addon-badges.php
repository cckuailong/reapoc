<?php
/**
 * Addon: Badges
 * Addon URI: http://codex.mycred.me/chapter-iii/badges/
 * Version: 1.3
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'myCRED_BADGE',               __FILE__ );
define( 'myCRED_BADGE_VERSION',       '1.3' );
define( 'MYCRED_BADGE_DIR',           myCRED_ADDONS_DIR . 'badges/' );
define( 'MYCRED_BADGE_INCLUDES_DIR',  MYCRED_BADGE_DIR . 'includes/' );
define( 'MYCRED_BADGE_TEMPLATES_DIR', MYCRED_BADGE_DIR . 'templates/' );

// Badge Key
if ( ! defined( 'MYCRED_BADGE_KEY' ) )
    define( 'MYCRED_BADGE_KEY', 'mycred_badge' );

// Badge Category
if ( ! defined( 'MYCRED_BADGE_CATEGORY' ) )
    define( 'MYCRED_BADGE_CATEGORY', 'mycred_badge_category' );

// Default badge width
if ( ! defined( 'MYCRED_BADGE_WIDTH' ) )
    define( 'MYCRED_BADGE_WIDTH', 100 );

// Default badge height
if ( ! defined( 'MYCRED_BADGE_HEIGHT' ) )
    define( 'MYCRED_BADGE_HEIGHT', 100 );

require_once MYCRED_BADGE_INCLUDES_DIR . 'mycred-badge-functions.php';
require_once MYCRED_BADGE_INCLUDES_DIR . 'mycred-badge-shortcodes.php';
require_once MYCRED_BADGE_INCLUDES_DIR . 'mycred-badge-object.php';
require_once MYCRED_BADGE_INCLUDES_DIR . 'mycred-badge-secondary.php';
require_once MYCRED_BADGE_INCLUDES_DIR . 'mycred-open-badge.php';

/**
 * myCRED_buyCRED_Module class
 * @since 1.5
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Badge_Module' ) ) :
    class myCRED_Badge_Module extends myCRED_Module {

        /**
         * Construct
         */
        function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

            parent::__construct( 'myCRED_Badge_Module', array(
                'module_name' => 'badges',
                'defaults'    => mycred_get_addon_defaults( 'badges' ),
                'add_to_core' => true,
                'register'    => false,
                'menu_pos'    => 50
            ), $type );

        }

        /**
         * Module Pre Init
         * @since 1.0
         * @version 1.2
         */
        public function module_pre_init() {

            add_action( 'wp_head', array( $this, 'social_share_br_header' ) );
            add_filter( 'mycred_add_finished', array( $this, 'add_finished' ), 30, 3 );
            add_action( 'wp_ajax_mycred_switch_all_to_open_badge', array( $this, 'mycred_switch_all_to_open_badge' ) );
            add_action( 'wp_ajax_nopriv_mycred_switch_all_to_open_badge', array( $this, 'mycred_switch_all_to_open_badge' ) );

        }

        /**
         * Switch all badges to Open Badges
         * @since 2.1
         * @version 1.0
         */
        public function mycred_switch_all_to_open_badge() {

            $args = array(
                'post_type' => 'mycred_badge'
            );

            $query = new WP_Query( $args );

            $badges = $query->posts;

            foreach ( $badges as $badge )
            {
                $badge_id = $badge->ID;

                mycred_update_post_meta( $badge_id, 'open_badge', '1' );
            }

            echo 'Badges successfully switched to Open Badge.';

            die();
        }

        /**
         * Module Init
         * @since 1.0
         * @version 1.0.3
         */
        public function module_init() {

            $this->register_badges();
            $this->register_badge_category();

            add_action( 'mycred_set_current_account', array( $this, 'populate_current_account' ) );
            add_action( 'mycred_get_account',         array( $this, 'populate_account' ) );

            add_shortcode( MYCRED_SLUG . '_my_badges', 'mycred_render_my_badges' );
            add_shortcode( MYCRED_SLUG . '_badges',    'mycred_render_badges' );


            add_shortcode( MYCRED_SLUG . '_badges_list',    'mycred_render_badges_list' );

            add_shortcode( MYCRED_SLUG . '_badge_evidence', 'mycred_render_badge_evidence' );

            // Insert into bbPress
            if ( class_exists( 'bbPress' ) ) {

                if ( $this->badges['bbpress'] == 'profile' || $this->badges['bbpress'] == 'both' )
                    add_action( 'bbp_template_after_user_profile', array( $this, 'insert_into_bbpress_profile' ) );

                if ( $this->badges['bbpress'] == 'reply' || $this->badges['bbpress'] == 'both' )
                    add_action( 'bbp_theme_after_reply_author_details', array( $this, 'insert_into_bbpress_reply' ) );

            }

            //Load open badge if enabled
            if ( $this->badges['open_badge'] ) {

                $this->mycred_open_badge_init();
            
            }

            // Insert into BuddyPress
            if ( class_exists( 'BuddyPress' ) ) {

                // Insert into header
                if ( $this->badges['buddypress'] == 'header' || $this->badges['buddypress'] == 'both' )
                    add_action( 'bp_before_member_header_meta', array( $this, 'insert_into_buddypress' ) );

                // Insert into profile
                if ( $this->badges['buddypress'] == 'profile' || $this->badges['buddypress'] == 'both' )
                    add_action( 'bp_after_profile_loop_content', array( $this, 'insert_into_buddypress' ) );

            }

            add_action( 'mycred_add_menu', array( $this, 'add_to_menu' ), $this->menu_pos );
            add_filter( 'the_content',     array( $this, 'mycred_badge_page_template' ), 10, 1 );

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        }

        /**
         * Enqueue Front End Script
         * @since 1.3
         * @version 1.0
         */
        public function enqueue_front_scripts() {

            wp_enqueue_script( 'mycred-badge-front', plugins_url( 'assets/js/front.js', myCRED_BADGE ), array('jquery'), myCRED_BADGE_VERSION );
        }

        /**
         * Enqueue Admin Script
         * @since 1.3
         * @version 1.0
         */
        public function enqueue_admin_scripts() {

            wp_enqueue_script( 'mycred-badge-admin', plugins_url( 'assets/js/admin.js', myCRED_BADGE ), '', myCRED_BADGE_VERSION );
        }

        /**
         * Module Admin Init
         * @since 1.0
         * @version 1.1
         */
        public function module_admin_init() {

            add_filter( 'parent_file',                       array( $this, 'parent_file' ) );
            add_filter( 'submenu_file',                      array( $this, 'subparent_file' ), 10, 2 );
            add_action( 'mycred_admin_enqueue',              array( $this, 'enqueue_scripts' ), $this->menu_pos );


            add_filter( 'post_updated_messages',             array( $this, 'post_updated_messages' ) );
            add_filter( 'enter_title_here',                  array( $this, 'enter_title_here' ) );
            add_action( 'post_submitbox_start',              array( $this, 'publishing_actions' ) );

            add_action( 'wp_ajax_mycred-assign-badge',       array( $this, 'action_assign_badge' ) );
            add_action( 'wp_ajax_mycred-remove-connections', array( $this, 'action_remove_connections' ) );

            add_action( 'mycred_user_edit_after_balances',   array( $this, 'badge_user_screen' ), 10 );

            add_action( 'personal_options_update',           array( $this, 'save_manual_badges' ), 10 );
            add_action( 'edit_user_profile_update',          array( $this, 'save_manual_badges' ), 10 );

            add_action( 'mycred_delete_point_type',          array( $this, 'delete_point_type' ) );
            add_action( 'before_delete_post',                array( $this, 'delete_badge' ) );

            add_filter( 'manage_' . MYCRED_BADGE_KEY . '_posts_columns',       array( $this, 'adjust_column_headers' ) );
            add_action( 'manage_' . MYCRED_BADGE_KEY . '_posts_custom_column', array( $this, 'adjust_column_content' ), 10, 2 );
            add_action( 'save_post_' . MYCRED_BADGE_KEY,                       array( $this, 'save_badge' ), 10, 2 );

            $this->flush_rewrite_rules_for_badges();

        }

        /**
         * Register Badge Post Type
         * @since 1.0
         * @version 1.0
         */
        public function register_badges() {

            $labels = array(
                'name'               => __( 'Badges', 'mycred' ),
                'singular_name'      => __( 'Badge', 'mycred' ),
                'add_new'            => __( 'Add New', 'mycred' ),
                'add_new_item'       => __( 'Add New', 'mycred' ),
                'edit_item'          => __( 'Edit Badge', 'mycred' ),
                'new_item'           => __( 'New Badge', 'mycred' ),
                'all_items'          => __( 'Badges', 'mycred' ),
                'view_item'          => __( 'View Badge', 'mycred' ),
                'search_items'       => __( 'Search Badge', 'mycred' ),
                'not_found'          => __( 'No badges found', 'mycred' ),
                'not_found_in_trash' => __( 'No badges found in Trash', 'mycred' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Badges', 'mycred' )
            );

            $args = array(
                'labels'               => $labels,
                'supports'             => array( 'title', 'editor', 'excerpt' ),
                'hierarchical'         => false,
                'public'               => false,
                'show_ui'              => true,
                'show_in_menu'         => false,
                'show_in_nav_menus'    => false,
                'show_in_admin_bar'    => false,
                'can_export'           => true,
                'has_archive'          => false,
                'exclude_from_search'  => true,
                'publicly_queryable'   => false,
                'register_meta_box_cb' => array( $this, 'add_metaboxes' ),
                'capability_type'      => 'post',
                'publicly_queryable'   => true,
                'taxonomies'           => array( MYCRED_BADGE_CATEGORY )
            );

            register_post_type( MYCRED_BADGE_KEY, apply_filters( 'mycred_register_badge', $args ) );

        }

        /**
         * Register Badges Taxonomy
         * @since 2.1
         * @version 1.0
         */
        public function register_badge_category() {

            $labels = [
                'name' => __( 'Achievement Types', 'mycred' ),
                'menu_name' =>  __( 'Achievement Types', 'mycred' ),
                'all_items' => __( 'Achievement Types', 'mycred' ),
                'add_new_item' => __( 'Add New Achievement', 'mycred' ),
                'parent_item' => __( 'Parent Achievement Type', 'mycred' ),
            ];

            $object_type = [
                MYCRED_BADGE_KEY
            ];

            $args = [
                'labels' => $labels,
                'show_ui' => true,
                'show_in_menu'  => true,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite'       => array(
                    'slug' => MYCRED_BADGE_KEY
                ),
                'hierarchical'  => true,
            ];

            register_taxonomy(
                MYCRED_BADGE_CATEGORY,
                MYCRED_BADGE_KEY,
                $args
            );

        }

        /**
         * Populate Current Account
         * @since 1.8
         * @version 1.0
         */
        public function populate_current_account() {

            global $mycred_current_account;

            if ( isset( $mycred_current_account )
                && ( $mycred_current_account instanceof myCRED_Account )
                && ( isset( $mycred_current_account->badges ) )
            ) return;

            $earned       = array();
            $users_badges = mycred_get_users_badges( $mycred_current_account->user_id, true );

            if ( ! empty( $users_badges ) ) {
                foreach ( $users_badges as $badge_id => $level ) {

                    if ( ! is_numeric( $level ) )
                        $level = 0;

                    $badge_id = absint( $badge_id );
                    $level    = absint( $level );
                    $badge    = mycred_get_badge( $badge_id, $level );

                    $earned[ $badge_id ] = $badge;

                }
            }

            $mycred_current_account->badges    = $earned;
            $mycred_current_account->badge_ids = $users_badges;

        }

        /**
         * Populate Account
         * @since 1.8
         * @version 1.0
         */
        public function populate_account() {

            global $mycred_account;

            if ( isset( $mycred_account )
                && ( $mycred_account instanceof myCRED_Account )
                && ( isset( $mycred_account->badges ) )
            ) return;

            $earned       = array();
            $users_badges = mycred_get_users_badges( $mycred_account->user_id );

            if ( ! empty( $users_badges ) ) {
                foreach ( $users_badges as $badge_id => $level ) {

                    if ( ! is_numeric( $level ) )
                        $level = 0;

                    $badge_id = absint( $badge_id );
                    $level    = absint( $level );
                    $badge    = mycred_get_badge( $badge_id, $level );

                    $earned[ $badge_id ] = $badge;

                }
            }

            $mycred_account->badges    = $earned;
            $mycred_account->badge_ids = $users_badges;

        }

        /**
         * Delete Point Type
         * When a point type is deleted, we want to remove any data saved for this point type.
         * @since 1.7
         * @version 1.0
         */
        public function delete_point_type( $point_type = NULL ) {

            if ( ! mycred_point_type_exists( $point_type ) || $point_type == MYCRED_DEFAULT_TYPE_KEY ) return;

            $mycred = mycred( $point_type );

            if ( ! $mycred->user_is_point_editor() ) return;

            mycred_delete_option( 'mycred-badge-refs-' . $point_type );

        }

        /**
         * Delete Badge
         * When a badge is deleted, we want to delete connections as well.
         * @since 1.7
         * @version 1.0
         */
        public function delete_badge( $post_id ) {

            if ( get_post_type( $post_id ) != MYCRED_BADGE_KEY ) return $post_id;

            // Delete reference list to force a new query
            foreach ( $this->point_types as $type_id => $label )
                mycred_delete_option( 'mycred-badge-refs-' . $type_id );

            global $wpdb;

            // Delete connections to keep usermeta table clean
            $query = $wpdb->get_results( 
                $wpdb->prepare( 
                    "SELECT * FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", 
                    mycred_get_meta_key( MYCRED_BADGE_KEY ) . $post_id 
                ) 
            );

            foreach ( $query as $user_meta ) {

                mycred_delete_user_meta( $user_meta->user_id, $user_meta->meta_key );
                mycred_delete_user_meta( $user_meta->user_id, $user_meta->meta_key, '_issued_on' );

                $badge_ids = mycred_get_user_meta( $user_meta->user_id, MYCRED_BADGE_KEY . '_ids', '', true );
                
                if ( isset( $badge_ids[ $post_id ] ) ) {

                    unset( $badge_ids[ $post_id ] );
                    mycred_update_user_meta( $user_meta->user_id, MYCRED_BADGE_KEY . '_ids', '', $badge_ids );
                    
                }
            
            }

        }

        /**
         * Adjust Post Updated Messages
         * @since 1.0
         * @version 1.0
         */
        public function post_updated_messages( $messages ) {

            global $post;

            $messages[ MYCRED_BADGE_KEY ] = array(
                0  => '',
                1  => __( 'Badge Updated.', 'mycred' ),
                2  => __( 'Badge Updated.', 'mycred' ),
                3  => __( 'Badge Updated.', 'mycred' ),
                4  => __( 'Badge Updated.', 'mycred' ),
                5  => false,
                6  => __( 'Badge Enabled.', 'mycred' ),
                7  => __( 'Badge Saved.', 'mycred' ),
                8  => __( 'Badge Updated.', 'mycred' ),
                9  => __( 'Badge Updated.', 'mycred' ),
                10 => __( 'Badge Updated.', 'mycred' )
            );

            return $messages;

        }

        /**
         * Add Admin Menu Item
         * @since 1.7
         * @version 1.1
         */
        public function add_to_menu() {

            // In case we are using the Master Template feautre on multisites, and this is not the main
            // site in the network, bail.
            if ( mycred_override_settings() && ! mycred_is_main_site() ) return;

            mycred_add_main_submenu(
                __( 'Achievement Types', 'mycred' ),
                __( 'Achievement Types', 'mycred' ),
                $this->core->get_point_editor_capability(),
                'edit-tags.php?post_type=' . MYCRED_BADGE_KEY . '&taxonomy=' . MYCRED_BADGE_CATEGORY
            );

            mycred_add_main_submenu(
                __( 'Badges', 'mycred' ),
                __( 'Badges', 'mycred' ),
                $this->core->get_point_editor_capability(),
                'edit.php?post_type=' . MYCRED_BADGE_KEY
            );

        }

        /**
         * Parent File
         * @since 1.6
         * @version 1.0.2
         */
        public function parent_file( $parent = '' ) {

            global $pagenow, $submenu_file;

            if ( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_BADGE_KEY ) {

                return MYCRED_MAIN_SLUG;

            }

            elseif ( $pagenow == 'post.php' && isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_BADGE_KEY ) {

                return MYCRED_MAIN_SLUG;

            }

            elseif ( ( $pagenow == 'edit-tags.php' ) && isset( $_GET['post_type'] ) && isset( $_GET['taxonomy'] ) && $_GET['post_type'] == MYCRED_BADGE_KEY &&  $_GET['taxonomy'] == MYCRED_BADGE_CATEGORY )
            {
                $submenu_file = 'edit-tags.php?post_type=' . MYCRED_BADGE_KEY . '&taxonomy=' . MYCRED_BADGE_CATEGORY;

                return MYCRED_MAIN_SLUG;
            }

            return $parent;

        }

        /**
         * Sub Parent File
         * @since 1.7
         * @version 1.0
         */
        public function subparent_file( $subparent = '', $parent = '' ) {

            global $pagenow;

            if ( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_BADGE_KEY ) {

                return 'edit.php?post_type=' . MYCRED_BADGE_KEY;

            }

            elseif ( $pagenow == 'post.php' && isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_BADGE_KEY ) {

                return 'edit.php?post_type=' . MYCRED_BADGE_KEY;

            }

            return $subparent;

        }

        /**
         * Add Finished
         * @since 1.0
         * @version 1.4
         */
        public function add_finished( $result, $request, $mycred ) {

            if ( is_bool( $request ) ) return $result;

            extract( $request );

            if ( $result !== false && $ref != 'badge_reward' ) {

                // Check if this reference has badges
                $badge_ids = mycred_ref_has_badge( $ref, $type );
                if ( $badge_ids !== false ) {

                    // Check if user gets any of the badges
                    foreach ( $badge_ids as $badge_id ) {

                        $badge = mycred_get_badge( $badge_id );
                        if ( $badge === false ) continue;

                        // Check what level we reached (if we reached any)
                        $level_reached = $badge->query_users_level( $user_id );
                        if ( $level_reached !== false )
                            $badge->assign( $user_id, $level_reached );

                    }

                }

            }

            return $result;

        }

        /**
         * Adjust Badge Column Header
         * @since 1.0
         * @version 1.0
         */
        public function adjust_column_headers( $defaults ) {

            $columns                        = array();
            $columns['cb']                  = $defaults['cb'];

            // Add / Adjust
            $columns['title']               = __( 'Badge Name', 'mycred' );
            $columns['badge-default-image'] = __( 'Default Image', 'mycred' );
            $columns['badge-earned-image']  = __( 'First Level', 'mycred' );
            $columns['badge-reqs']          = __( 'Requirements', 'mycred' );
            $columns['badge-users']         = __( 'Users', 'mycred' );
            $columns['badge-users']         = __( 'Users', 'mycred' );
            $columns['badge-type']          = __( 'Achievements Type', 'mycred' );

            if ( $this->badges['open_badge'] ) 
                $columns['badge-open-badge'] = __( 'Open Badge', 'mycred' );

            // Return
            return $columns;

        }

        /**
         * Adjust Badge Column Content
         * @since 1.0
         * @version 1.2
         */
        public function adjust_column_content( $column_name, $badge_id ) {

            // Default Images
            if ( $column_name == 'badge-default-image' ) {

                $badge = mycred_get_badge( $badge_id );
                if ( $badge === false || $badge->main_image === false )
                    echo '-';

                elseif ( $badge->main_image !== false )
                    echo $badge->main_image;

            }

            // First Level Image
            if ( $column_name == 'badge-earned-image' ) {

                $badge = mycred_get_badge( $badge_id );
                $image = $badge->get_image( 0 );
                if ( $badge->open_badge || $image === false )
                    echo '-';
                else
                    echo $image;

            }

            // Badge Requirements
            elseif ( $column_name == 'badge-reqs' ) {

                echo mycred_display_badge_requirements( $badge_id );

            }

            // Badge Users
            elseif ( $column_name == 'badge-users' ) {

                $badge = mycred_get_badge( $badge_id );
                if ( $badge === false )
                    echo 0;

                else
                    echo $badge->earnedby;

            }

            //Badge Type
            elseif ( $column_name == 'badge-type' ) {

                $badge = mycred_get_badge_type( $badge_id );

                echo $badge == false ? 'No Acheivement Type' : $badge;

            }

            //Open Badge
            elseif ( $column_name == 'badge-open-badge' ) {

                $badge = mycred_get_badge( $badge_id );

                echo $badge->open_badge ? 'Yes' : 'No';

            }

        }


        /**
         * Adjust Enter Title Here
         * @since 1.0
         * @version 1.0
         */
        public function enter_title_here( $title ) {

            global $post_type;

            if ( $post_type == MYCRED_BADGE_KEY )
                return __( 'Badge Name', 'mycred' );

            return $title;

        }

        /**
         * Enqueue Scripts
         * @since 1.0
         * @version 1.0.1
         */
        public function enqueue_scripts() {

            $screen = get_current_screen();
            if ( $screen->id == MYCRED_BADGE_KEY ) {

                wp_enqueue_media();

                wp_register_script(
                    'mycred-edit-badge',
                    plugins_url( 'assets/js/edit-badge.js', myCRED_BADGE ),
                    array( 'jquery', 'mycred-mustache' ),
                    myCRED_BADGE_VERSION . '.1'
                );

                wp_localize_script(
                    'mycred-edit-badge',
                    'myCREDBadge',
                    array(
                        'ajaxurl'      => admin_url( 'admin-ajax.php' ),
                        'addlevel'     => esc_js( __( 'Add Level', 'mycred' ) ),
                        'removelevel'  => esc_js( __( 'Remove Level', 'mycred' ) ),
                        'setimage'     => esc_js( __( 'Set Image', 'mycred' ) ),
                        'changeimage'  => esc_js( __( 'Change Image', 'mycred' ) ),
                        'remove'       => esc_js( esc_attr__( 'Are you sure you want to remove this level?', 'mycred' ) ),
                        'levellabel'   => esc_js( sprintf( '%s {{level}}', __( 'Level', 'mycred' ) ) ),
                        'uploadtitle'  => esc_js( esc_attr__( 'Badge Image', 'mycred' ) ),
                        'uploadbutton' => esc_js( esc_attr__( 'Use as Badge', 'mycred' ) ),
                        'compareAND'   => esc_js( _x( 'AND', 'Comparison of badge requirements. A AND B', 'mycred' ) ),
                        'compareOR'    => esc_js( _x( 'OR', 'Comparison of badge requirements. A OR B', 'mycred' ) )
                    )
                );

                wp_enqueue_script( 'mycred-edit-badge' );

                wp_enqueue_style( 'mycred-bootstrap-grid' );
                wp_enqueue_style( 'mycred-forms' );

                add_filter( 'postbox_classes_' . MYCRED_BADGE_KEY . '_mycred-badge-setup',   array( $this, 'metabox_classes' ) );
                add_filter( 'postbox_classes_' . MYCRED_BADGE_KEY . '_mycred-badge-default', array( $this, 'metabox_classes' ) );
                add_filter( 'postbox_classes_' . MYCRED_BADGE_KEY . '_mycred-badge-rewards', array( $this, 'metabox_classes' ) );

                echo '<style type="text/css">
#misc-publishing-actions #visibility, #misc-publishing-actions .misc-pub-post-status { display: none; }
#save-action #save-post { margin-bottom: 12px; }
</style>';

            }

            elseif ( $screen->id == 'edit-' . MYCRED_BADGE_KEY ) {

                echo '<style type="text/css">
th#badge-default-image { width: 120px; }
th#badge-earned-image { width: 120px; }
th#badge-reqs { width: 35%; }
th#badge-users { width: 10%; }
.column-badge-default-image img { max-width: 100px; height: auto; }
.mycred-badge-requirement-list { margin: 6px 0 0 0; padding: 6px 0 0 18px; border-top: 1px dashed #aeaeae; }
.mycred-badge-requirement-list li { margin: 0 0 0 0; padding: 0 0 0 0; font-size: 12px; line-height: 16px; list-style-type: circle; }
.mycred-badge-requirement-list li span { float: right; }
.column-badge-reqs strong { display: block; }
.column-badge-reqs span { color: #aeaeae; }
.mycred-badge-requirement-list.open_badge { border: 0px; }
</style>';

            }

        }

        /**
         * Add Meta Boxes
         * @since 1.0
         * @version 1.0
         */
        public function add_metaboxes() {

            add_meta_box(
                'mycred-badge-setup',
                __( 'Badge Setup', 'mycred' ),
                array( $this, 'metabox_badge_setup' ),
                MYCRED_BADGE_KEY,
                'normal',
                'low'
            );

            add_meta_box(
                'mycred-badge-default',
                __( 'Default Badge Image', 'mycred' ),
                array( $this, 'metabox_badge_default' ),
                MYCRED_BADGE_KEY,
                'side',
                'low'
            );

            if ( $this->badges['open_badge'] ) 
                add_meta_box(
                    'mycred-badge-open-badge',
                    __( 'Open Badge', 'mycred' ),
                    array( $this, 'metabox_open_badge' ),
                    MYCRED_BADGE_KEY,
                    'side',
                    'high'
                );

            add_meta_box(
                'mycred-badge-congratulation-msg',
                __( 'Congratulation Message', 'mycred' ),
                array( $this, 'metabox_congratulation_msg' ),
                MYCRED_BADGE_KEY,
                'normal',
                'low'
            );

            add_meta_box(
                'mycred-badge-align',
                __( 'Alignment', 'mycred' ),
                array( $this, 'metabox_badge_align' ),
                MYCRED_BADGE_KEY,
                'side',
                'low'
            );

            add_meta_box(
                'mycred-badge-layout',
                __( 'Layout', 'mycred' ),
                array( $this, 'metabox_badge_layout' ),
                MYCRED_BADGE_KEY,
                'side',
                'low'
            );

        }

        /**
         * Level Template
         * @since 1.7
         * @version 1.0
         */
        public function level_template( $level = 0 ) {

            $template = '<div class="row badge-level" id="mycred-badge-level{{level}}" data-level="{{level}}"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">{{removelevelbutton}}<div class="level-image"><div class="level-image-wrapper image-wrapper {{emptylevelimage}}">{{levelimage}}</div><div class="level-image-actions"><button type="button" class="button button-secondary change-level-image" data-level="{{level}}">{{levelimagebutton}}</button></div></div><div class="label-field"><input type="text" placeholder="{{levelplaceholder}}" name="mycred_badge[levels][{{level}}][label]" value="{{levellabel}}" /></div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="req-title">{{requirementslabel}}</div><div class="level-requirements">{{{requirements}}}</div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{rewards}}</div></div>';

            if ( $level == 0 ) {

                $template = '<div class="row badge-level" id="mycred-badge-level{{level}}" data-level="{{level}}"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">{{addlevelbutton}}<div class="level-image"><div class="level-image-wrapper image-wrapper {{emptylevelimage}}">{{levelimage}}</div><div class="level-image-actions"><button type="button" class="button button-secondary change-level-image" data-level="{{level}}">{{levelimagebutton}}</button></div></div><div class="label-field"><input type="text" placeholder="{{levelplaceholder}}" name="mycred_badge[levels][{{level}}][label]" value="{{levellabel}}" /></div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="req-title">{{requirementslabel}}<div class="pull-right" id="badge-requirement-compare"><a href="javascript:void(0);" data-do="AND" class="{{adnselected}}">AND</a> / <a href="javascript:void(0);" data-do="OR" class="{{orselected}}">OR</a><input type="hidden" name="mycred_badge[levels][{{level}}][compare]" value="{{badge_compare_andor}}" /></div></div><div class="level-requirements">{{{requirements}}}</div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{rewards}}</div></div>';

            }
            elseif ( $level < 0 ) {

                $template = '<div class="row badge-level" id="mycred-badge-level{{level}}" data-level="{{level}}"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="req-title">{{requirementslabel}}<div class="pull-right" id="badge-requirement-compare"><a href="javascript:void(0);" data-do="AND" class="{{adnselected}}">AND</a> / <a href="javascript:void(0);" data-do="OR" class="{{orselected}}">OR</a><input type="hidden" name="mycred_badge[levels][{{level}}][compare]" value="{{badge_compare_andor}}" /></div></div><div class="level-requirements">{{{requirements}}}</div></div><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{{rewards}}</div></div>';

            }

            return $template;

        }

        /**
         * Get Level Image
         * @since 1.7
         * @version 1.0
         */
        public function get_level_image( $setup, $level = 0 ) {

            $image = false;

            if ( $setup['attachment_id'] > 0 ) {

                $_image = wp_get_attachment_url( $setup['attachment_id'] );
                if ( strlen( $_image ) > 5 )
                    $image = '<img src="' . $_image . '" alt="Badge level image" /><input type="hidden" name="mycred_badge[levels][' . $level . '][attachment_id]" value="' . $setup['attachment_id'] . '" /><input type="hidden" name="mycred_badge[levels][' . $level . '][image_url]" value="" />';

            }
            else {

                if ( strlen( $setup['image_url'] ) > 5 )
                    $image = '<img src="' . $setup['image_url'] . '" alt="Badge level image" /><input type="hidden" name="mycred_badge[levels][' . $level . '][attachment_id]" value="0" /><input type="hidden" name="mycred_badge[levels][' . $level . '][image_url]" value="' . $setup['image_url'] . '" />';

            }

            return $image;

        }

        /**
         * Requirements Template
         * @since 1.7
         * @version 1.0
         */
        public function requirements_template( $level = 0 ) {

            // only first level dictates requirements
            if ( $level == 0 )
                return '<div class="row row-narrow" id="level{{level}}requirement{{reqlevel}}" data-row="{{reqlevel}}"><div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 form"><div class="form-group"><select name="mycred_badge[levels][{{level}}][requires][{{reqlevel}}][type]" data-row="{{reqlevel}}" class="form-control point-type">{{pointtypes}}</select></div></div><div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 form"><div class="form-group"><select name="mycred_badge[levels][{{level}}][requires][{{reqlevel}}][reference]" data-row="{{reqlevel}}" class="form-control reference">{{references}}</select></div>{{{customrequirement}}}</div><div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 form-inline"><div class="form-group"><input type="text" size="5" name="mycred_badge[levels][{{level}}][requires][{{reqlevel}}][amount]" class="form-control" value="{{reqamount}}" /></div><div class="form-group"><select name="mycred_badge[levels][{{level}}][requires][{{reqlevel}}][by]" data-row="{{reqlevel}}" class="form-control req-type">{{requirementtype}}</select></div></div><div class="col-lg-1 col-md-1 col-sm-6 col-xs-2 form">{{reqbutton}}</div></div>';

            // All other requirements reflect the level 0's setup
            return '<div class="row row-narrow" id="level{{level}}requirement{{reqlevel}}"><div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 form"><div class="form-group level-type"><p class="form-control-static level-requirement{{reqlevel}}-type">{{selectedtype}}</p></div></div><div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 form"><div class="form-group level-ref"><p class="form-control-static level-requirement{{reqlevel}}-ref">{{selectedref}}</p><p>{{refspecific}}</p></div></div><div class="col-lg-3 col-md-3 col-sm-6 col-xs-10 form-inline"><div class="form-group level-val"><input type="text" size="5" name="mycred_badge[levels][{{level}}][requires][{{reqlevel}}][amount]" class="form-control" value="{{reqamount}}" /></div><div class="form-group level-type-by"><p class="form-control-static level-requirement{{reqlevel}}-by">{{selectedby}}</p></div></div><div class="col-lg-1 col-md-1 col-sm-6 col-xs-2 level-compare form"><p class="form-control-static" data-row="{{reqlevel}}">{{comparelabel}}</p></div></div>';

        }

        /**
         * Rewards Template
         * @since 1.7
         * @version 1.0
         */
        public function rewards_template() {

            return '<div class="req-title">{{rewardlabel}}</div><div class="row form"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><select name="mycred_badge[levels][{{level}}][reward][type]" class="form-control">{{pointtypes}}</select></div><div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><input type="text" class="form-control" name="mycred_badge[levels][{{level}}][reward][log]" placeholder="{{logplaceholder}}" value="{{logtemplate}}" /></div><div class="col-lg-2 col-md-2 col-sm-12 col-xs-12"><input type="text" class="form-control" name="mycred_badge[levels][{{level}}][reward][amount]" placeholder="0" value="{{rewardamount}}" /></div></div>';

        }

        /**
         * Badge Publishing Actions
         * @since 1.7
         * @version 1.1
         */
        public function publishing_actions() {

            global $post;

            if ( ! isset( $post->post_type ) || $post->post_type != MYCRED_BADGE_KEY ) return;

            $manual_badge = ( (int) mycred_get_post_meta( $post->ID, 'manual_badge', true ) == 1 ) ? true : false;

            ?>
            <div id="mycred-badge-actions" class="seperate-bottom">

                <?php do_action( 'mycred_edit_badge_before_actions', $post ); ?>

                <input type="hidden" name="mycred-badge-edit" value="<?php echo wp_create_nonce( 'edit-mycred-badge' ); ?>" />
                <input type="button" id="mycred-assign-badge-connections"<?php if ( $manual_badge || $post->post_status != 'publish' ) echo ' disabled="disabled"'; ?> value="<?php _e( 'Assign Badge', 'mycred' ); ?>" class="button button-secondary mycred-badge-action-button" data-action="mycred-assign-badge" data-token="<?php echo wp_create_nonce( 'mycred-assign-badge' ); ?>" />
                <input type="button" id="mycred-remove-badge-connections"<?php if ( $post->post_status != 'publish' ) echo ' disabled="disabled"'; ?> value="<?php _e( 'Remove Connections', 'mycred' ); ?>" class="button button-secondary mycred-badge-action-button" data-action="mycred-remove-connections" data-token="<?php echo wp_create_nonce( 'mycred-remove-badge-connection' ); ?>" />

                <?php do_action( 'mycred_edit_badge_after_actions', $post ); ?>

                <script type="text/javascript">
                    jQuery(function($) {

                        $( 'input.mycred-badge-action-button' ).click(function(){
                            var button = $(this);
                            var label = button.val();

                            $.ajax({
                                type : "POST",
                                data : {
                                    action   : button.attr( 'data-action' ),
                                    token    : button.attr( 'data-token' ),
                                    badge_id : <?php echo $post->ID; ?>
                                },
                                dataType : "JSON",
                                url : ajaxurl,
                                beforeSend : function() {
                                    button.attr( 'value', '<?php echo esc_js( esc_attr__( 'Processing...', 'mycred' ) ); ?>' );
                                    button.attr( 'disabled', 'disabled' );
                                },
                                success : function( response ) {
                                    alert( response.data );
                                    button.removeAttr( 'disabled' );
                                    button.val( label );
                                }
                            });
                            return false;

                        });

                    });
                </script>

            </div>
            <div id="mycred-manual-badge" class="seperate-bottom">
                <label for="mycred-badge-is-manual"><input type="checkbox" name="mycred_badge[manual]" id="mycred-badge-is-manual"<?php if ( $manual_badge ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'This badge is manually awarded.', 'mycred' ); ?></label>
            </div>
            <?php

        }

        /**
         * Default Image Metabox
         * @since 1.7
         * @version 1.0
         */
        public function metabox_badge_default( $post ) {

            $default_image = $di = mycred_get_post_meta( $post->ID, 'main_image', true );
            if ( $default_image != '' )
                $default_image = '<img src="' . $default_image . '" alt="" />';

            $attachment = false;
            if ( is_numeric( $di ) && strpos( '://', $di ) === false ) {
                $attachment    = $di;
                $default_image = '<img src="' . wp_get_attachment_url( $di ) . '" alt="" />';
            }

            ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="default-image text-center seperate-bottom">
                        <div class="default-image-wrapper image-wrapper<?php if ( $default_image == '' ) echo ' empty dashicons'; ?>">
                            <?php echo $default_image; ?>
                            <input type="hidden" name="mycred_badge[main_image]" id="badge-main-image-id" value="<?php if ( $attachment ) echo esc_attr( $di ); ?>" />
                            <input type="hidden" name="mycred_badge[main_image_url]" id="badge-main-image-url" value="<?php if ( $di != '' && strpos( '://', $di ) !== false ) echo esc_attr( $default_image ); ?>" />
                        </div>
                        <div class="level-image-actions">
                            <button type="button" class="button button-secondary" id="badges-change-default-image" data-do="<?php if ( $default_image == '' ) echo 'set'; else echo 'change'; ?>"><?php if ( $default_image == '' ) _e( 'Set Image', 'mycred' ); else _e( 'Change Image', 'mycred' ); ?></button>
                            <button type="button" class="button button-secondary <?php echo ( ( ! $attachment ) ? 'hidden' : '' ); ?>" id="badges-remove-default-image"><?php _e( 'Remove Image', 'mycred' ); ?></button>
                        </div>
                    </div>
                    <span class="description"><?php _e( 'Optional image to show when a user has not earned this badge.', 'mycred' ); ?></span>
                </div>
            </div>
            <?php

        }

        public function metabox_badge_align( $post ) {

            $mycred_badge_align = mycred_get_post_meta( $post->ID, 'mycred_badge_align', true );
            require_once MYCRED_BADGE_TEMPLATES_DIR . 'mycred-metabox-badge-alignment.php';

        }



        public function metabox_badge_layout( $post ) {
            
            $mycred_layout = mycred_get_post_meta( $post->ID, 'mycred_layout_check', true );
            require_once MYCRED_BADGE_TEMPLATES_DIR . 'mycred-metabox-badge-layout.php';

        }

        /**
         * Open Badge Metabox
         * @since 2.1
         * @version 1.0
         */
        public function metabox_open_badge( $post ) {

            $open_badge = ( mycred_get_post_meta( $post->ID, 'open_badge', true ) == 1 ) ? true : false;

            ?>
            <div id="mycred-open-badge" class="seperate-bottom">
                <label for="mycred-badge-is-open-badge"><input type="checkbox" name="mycred_badge[open_badge]" id="mycred-badge-is-open-badge"<?php if ( $open_badge ) echo ' checked="checked"'; ?> value="1" /> <?php _e( 'This badge is Open Badge.', 'mycred' ); ?></label>
            </div>
            <span class="description"><?php _e( 'Multi level badge settings will be disable when switched to open badge.', 'mycred' ); ?></span>
            <?php

        }

        /**
         * Congratulation Message Metabox
         * @since 2.1
         * @version 1.0
         */
        public function metabox_congratulation_msg( $post ) {

            $congratulation_msg = mycred_get_post_meta( $post->ID, 'congratulation_msg', true );

            ?>
            <style>#mycred-badge-congratulation-msg{display:block;margin:12px 0 0;width:100%;}</style>
            <textarea name="mycred_badge[congratulation_msg]" placeholder="You have earned this Badge!" id="mycred-badge-congratulation-msg"><?php echo empty( $congratulation_msg ) ? '' : $congratulation_msg; ?></textarea>
            <?php

        }

        /**
         * Badge Setup Metabox
         * @since 1.7
         * @version 1.2
         */
        public function metabox_badge_setup( $post ) {

            $badge       = mycred_get_badge( $post->ID );
            $references  = mycred_get_all_references();
            $point_types = mycred_get_types( true );
            $open_badge  = false;

            if ( $this->badges['open_badge'] == 1 ) {
                    
                $open_badge = ( mycred_get_post_meta( $post->ID, 'open_badge', true ) == 1 ) ? true : false;

            }

            $sums = apply_filters( 'mycred_badge_requirement_sums', array(
                'count' => esc_js( __( 'Time(s)', 'mycred' ) ),
                'sum'   => esc_js( __( 'In total', 'mycred' ) )
            ), $badge );

            // Badge rewards can no be used as a requirement
            if ( array_key_exists( 'badge_reward', $references ) )
                unset( $references['badge_reward'] );

            $js_level             = $this->level_template( 1 );
            $js_requirement       = $this->requirements_template( 0 );
            $js_requirement_clone = $this->requirements_template( 1 );

            ?>
            <div id="badge-levels">
                <?php

                // Loop through each badge level
                $level_counter = 0;
                foreach ( $badge->levels as $level => $setup ) {

                    $level        = $level_counter;

                    $add_level    = '<button type="button" class="button button-seconary button-small top-right-corner" id="badges-add-new-level">' . esc_js( __( 'Add Level', 'mycred' ) ) . '</button>';
                    $remove_level = '<button type="button" class="button button-seconary button-small top-right-corner remove-badge-level" data-level="' . $level . '">' . esc_js( __( 'Remove Level', 'mycred' ) ) . '</button>';

                    $level_image  = $this->get_level_image( $setup, $level );
                    $empty_level  = 'empty dashicons';
                    if ( $level_image !== false )
                        $empty_level = '';

                    $template = $this->level_template( ( $open_badge ? -1 : $level )  );

                    $template = str_replace( '{{level}}',             $level, $template );
                    $template = str_replace( '{{addlevelbutton}}',    $add_level, $template );
                    $template = str_replace( '{{removelevelbutton}}', $remove_level, $template );

                    $js_level = str_replace( '{{removelevelbutton}}', $remove_level, $js_level );
                    $js_level = str_replace( '{{emptylevelimage}}',   $empty_level, $js_level );
                    $js_level = str_replace( '{{levelimage}}',        '', $js_level );
                    $js_level = str_replace( '{{levelimagebutton}}',  esc_js( __( 'Set Image', 'mycred' ) ), $js_level );
                    $js_level = str_replace( '{{levelplaceholder}}',  esc_js( __( 'Level', 'mycred' ) ) . ' {{levelone}}', $js_level );

                    $template = str_replace( '{{levelimage}}',        $level_image, $template );
                    $template = str_replace( '{{emptylevelimage}}',   $empty_level, $template );
                    $template = str_replace( '{{levelimagebutton}}',  ( ( $level_image === false ) ? esc_js( __( 'Set Image', 'mycred' ) ) : esc_js( __( 'Change Image', 'mycred' ) ) ), $template );

                    $template = str_replace( '{{levelplaceholder}}',  esc_js( sprintf( __( 'Level %d', 'mycred' ), $level+1 ) ), $template );
                    $template = str_replace( '{{levellabel}}',        esc_js( $setup['label'] ), $template );

                    $template = str_replace( '{{requirementslabel}}', esc_js( __( 'Requirement', 'mycred' ) ), $template );
                    $js_level = str_replace( '{{requirementslabel}}', esc_js( __( 'Requirement', 'mycred' ) ), $js_level );

                    $template = str_replace( '{{adnselected}}',       ( ( $setup['compare'] === 'AND' ) ? 'selected' : '' ), $template );
                    $template = str_replace( '{{orselected}}',        ( ( $setup['compare'] === 'OR' ) ? 'selected' : '' ), $template );

                    $template = str_replace( '{{badge_compare_andor}}',        ( ( isset($setup['compare']) && !empty($setup['compare']) ) ? $setup['compare'] : 'AND' ), $template );

                    //$requirement = $this->requirements_template( 1 );

                    $total_requirements = count( $setup['requires'] );
                    $level_requirements = '';

                    foreach ( $setup['requires'] as $req_level => $reqsetup ) {

                        $requirement         = $this->requirements_template( $level );

                        $requirement         = str_replace( '{{level}}',    $level, $requirement );
                        $requirement         = str_replace( '{{reqlevel}}', $req_level, $requirement );

                        $point_type_options  = '';
                        $point_type_options .= '<option value=""';
                        if ( $reqsetup['type'] == '' ) $point_type_options .= ' selected="selected"';
                        $point_type_options .= '>' . esc_js( __( 'Select Point Type', 'mycred' ) ) . '</option>';
                        foreach ( $point_types as $type_id => $type_label ) {
                            $point_type_options .= '<option value="' . esc_attr( $type_id ) . '"';
                            if ( $reqsetup['type'] == $type_id ) $point_type_options .= ' selected="selected"';
                            $point_type_options .= '>' . esc_html( $type_label ) . '</option>';
                        }

                        $requirement         = str_replace( '{{pointtypes}}', $point_type_options, $requirement );
                        $point_type_options  = str_replace( 'selected="selected"', '', $point_type_options );
                        $js_requirement      = str_replace( '{{pointtypes}}', $point_type_options, $js_requirement );

                        $reference_options   = '';
                        $reference_options  .= '<option value=""';
                        if ( $reqsetup['reference'] == '' ) $reference_options .= ' selected="selected"';
                        $reference_options  .= '>' . esc_js( __( 'Select Reference', 'mycred' ) ) . '</option>';
                        foreach ( $references as $ref_id => $ref_label ) {
                            $reference_options .= '<option value="' . esc_attr( $ref_id ) . '"';
                            if ( $reqsetup['reference'] == $ref_id ) $reference_options .= ' selected="selected"';
                            $reference_options .= '>' . esc_html( $ref_label ) . '</option>';
                        }

                        $requirement         = str_replace( '{{references}}', $reference_options, $requirement );

                        $requirement_specific = apply_filters( 'mycred_badge_requirement_specific_template', '', $req_level, $reqsetup, $badge, $level );
                        $requirement         = str_replace( '{{{customrequirement}}}', $requirement_specific,  $requirement );

                        $requirement         = str_replace( '{{reqamount}}',  $reqsetup['amount'], $requirement );

                        $reference_options   = str_replace( 'selected="selected"', '', $reference_options );
                        $js_requirement      = str_replace( '{{references}}', $reference_options, $js_requirement );
                        $js_requirement      = str_replace( '{{reqamount}}',  $reqsetup['amount'], $js_requirement );

                        $by_options          = '';
                        $by_options         .= '<option value=""';
                        if ( $reqsetup['by'] == '' ) $by_options .= ' selected="selected"';
                        $by_options         .= '>' . __( 'Select', 'mycred' ) . '</option>';
                        foreach ( $sums as $sum_id => $sum_label ) {
                            $by_options .= '<option value="' . $sum_id . '"';
                            if ( $reqsetup['by'] == $sum_id ) $by_options .= ' selected="selected"';
                            $by_options .= '>' . $sum_label . '</option>';
                        }

                        $requirement         = str_replace( '{{requirementtype}}', $by_options, $requirement );

                        $by_options          = str_replace( 'selected="selected"', '', $by_options );
                        $js_requirement      = str_replace( '{{requirementtype}}', $by_options, $js_requirement );

                        $selectedtype        = '-';
                        if ( array_key_exists( $reqsetup['type'], $point_types ) )
                            $selectedtype = $point_types[ $reqsetup['type'] ];

                        $requirement = str_replace( '{{selectedtype}}', $selectedtype, $requirement );

                        $requirement = str_replace( '{{refspecific}}', '', $requirement );

                        $selectedreference   = '-';
                        if ( array_key_exists( $reqsetup['reference'], $references ) )
                            $selectedreference = $references[ $reqsetup['reference'] ];

                        $requirement         = str_replace( '{{selectedref}}', $selectedreference, $requirement );

                        $selectedby          = '-';
                        if ( array_key_exists( $reqsetup['by'], $sums ) )
                            $selectedby = $sums[ $reqsetup['by'] ];

                        $requirement         = str_replace( '{{selectedby}}', $selectedby, $requirement );

                        $requirement_button  = '<button type="button" class="button button-primary form-control remove-requirement" data-req="{{reqlevel}}">-</button>';
                        $js_requirement      = str_replace( '{{reqbutton}}', $requirement_button, $js_requirement );

                        $requirement_button  = '<button type="button" class="button button-primary form-control remove-requirement" data-req="' . $req_level . '">-</button>';
                        if ( $req_level == 0 )
                            $requirement_button = '<button type="button" class="button button-secondary form-control" id="badges-add-new-requirement">+</button>';

                        $requirement         = str_replace( '{{reqbutton}}', $requirement_button, $requirement );

                        $compare_label       = '';
                        if ( $level > 0 && $req_level < $total_requirements )
                            $compare_label = ( ( $setup['compare'] === 'AND' ) ? _x( 'AND', 'Comparison of badge requirements. A AND B', 'mycred' ) : _x( 'OR', 'Comparison of badge requirements. A OR B', 'mycred' ) );

                        if ( $req_level+1 == $total_requirements )
                            $compare_label = '';

                        $requirement         = str_replace( '{{comparelabel}}', esc_js( $compare_label ), $requirement );

                        $level_requirements .= $requirement;

                    }

                    $template           = str_replace( '{{{requirements}}}', $level_requirements, $template );

                    $rewards            = $this->rewards_template();

                    $js_level           = str_replace( '{{reqamount}}',     '', $js_level );

                    $rewards            = str_replace( '{{level}}',          $level, $rewards );
                    $rewards            = str_replace( '{{rewardlabel}}',    esc_js( __( 'Reward', 'mycred' ) ), $rewards );

                    $point_type_options = '';
                    foreach ( $point_types as $type_id => $type_label ) {
                        $point_type_options .= '<option value="' . $type_id . '"';
                        if ( $setup['reward']['type'] == $type_id ) $point_type_options .= ' selected="selected"';
                        $point_type_options .= '>' . $type_label . '</option>';
                    }

                    $rewards            = str_replace( '{{pointtypes}}',     $point_type_options, $rewards );
                    $rewards            = str_replace( '{{logplaceholder}}', esc_js( __( 'Log template', 'mycred' ) ), $rewards );
                    $rewards            = str_replace( '{{logtemplate}}',    esc_js( $setup['reward']['log'] ), $rewards );
                    $rewards            = str_replace( '{{rewardamount}}',   $setup['reward']['amount'], $rewards );

                    $template           = str_replace( '{{rewards}}',       $rewards, $template );

                    $rewards            = str_replace( $level,         '{{level}}', $rewards );

                    $js_level           = str_replace( '{{rewards}}',       $rewards, $js_level );

                    echo $template;

                    $level_counter++;

                    if ( $open_badge ) break;

                }

                ?>
            </div>
            <script type="text/javascript">
                var BadgeLevel         = '<?php echo $js_level; ?>';
                var BadgeNewRequrement = '<?php echo $js_requirement; ?>';
                var BadgeRequirement   = '<?php echo $js_requirement_clone; ?>';
            </script>
            <?php

        }

        /**
         * Save Badge Details
         * @since 1.7
         * @version 1.1
         */
        public function save_badge( $post_id, $post = NULL ) {

            if ( $post === NULL || ! $this->core->user_is_point_editor() || ! isset( $_POST['mycred_badge'] ) ) return $post_id;

            // Main image (used when a user has not earned a badge
            $main_image = $_POST['mycred_badge']['main_image'];

            // If we are using an attachment
            if ( absint( $main_image ) > 0 )
                $image = absint( $main_image );

            // Else we are using a URL (old setup)
            else
                $image = sanitize_text_field( $_POST['mycred_badge']['main_image_url'] );

            $manual = 0;
            if ( isset( $_POST['mycred_badge']['manual'] ) )
                $manual = 1;

            $open_badge = 0;
            if ( isset( $_POST['mycred_badge']['open_badge'] ) )
                $open_badge = 1;

            $congratulation_msg = '';
            if ( isset( $_POST['mycred_badge']['congratulation_msg'] ) )
                $congratulation_msg = sanitize_text_field( $_POST['mycred_badge']['congratulation_msg'] );

            $badge_align = '';
            if ( isset( $_POST['mycred_badge']['mycred_badge_align'] ) )
                $badge_align = sanitize_text_field( $_POST['mycred_badge']['mycred_badge_align'] );

            $layout = '';
            if ( isset( $_POST['mycred_badge']['mycred_layout_check'] ) )
                $layout = sanitize_text_field( $_POST['mycred_badge']['mycred_layout_check'] );

            $badge_levels       = array();
            $badge_requirements = array();

            // Run through each level
            if ( ! empty( $_POST['mycred_badge']['levels'] ) ) {

                $level_row = 0;

                foreach ( $_POST['mycred_badge']['levels'] as $level_id => $level_setup ) {

                    $level = array();

                    if ( array_key_exists( 'attachment_id', $level_setup ) ) {
                        $level['attachment_id'] = absint( $level_setup['attachment_id'] );
                        $level['image_url']     = ( ( array_key_exists( 'image_url', $level_setup ) ) ? sanitize_text_field( $level_setup['image_url'] ) : '' );
                    }
                    //Setting default image on badge's level from badges
                    else {
                        $level['attachment_id'] = $image;
                        $level['image_url']     = ( ( array_key_exists( 'image_url', $level_setup ) ) ? sanitize_text_field( $level_setup['image_url'] ) : '' );
                    }

                    $level['label']         = sanitize_text_field( $level_setup['label'] );

                    if ( array_key_exists( 'compare', $level_setup ) )
                        $level['compare'] = ( ( $level_setup['compare'] == 'AND' ) ? 'AND' : 'OR' );
                    else
                        $level['compare'] = ( ( array_key_exists( 'compare', $badge_levels[0] ) ) ? $badge_levels[0]['compare'] : 'AND' );

                    $level['requires']      = array();

                    if ( array_key_exists( 'requires', $level_setup ) ) {

                        $level_requirements = array();

                        $row = 0;
                        foreach ( $level_setup['requires'] as $requirement_id => $requirement_setup ) {

                            $requirement              = array();
                            $requirement['type']      = ( ( array_key_exists( 'type', $requirement_setup ) ) ? sanitize_key( $requirement_setup['type'] ) : '' );
                            $requirement['reference'] = ( ( array_key_exists( 'reference', $requirement_setup ) ) ? sanitize_key( $requirement_setup['reference'] ) : '' );
                            $requirement['amount']    = ( ( array_key_exists( 'amount', $requirement_setup ) ) ? sanitize_text_field( $requirement_setup['amount'] ) : '' );
                            $requirement['by']        = ( ( array_key_exists( 'by', $requirement_setup ) ) ? sanitize_key( $requirement_setup['by'] ) : '' );
                            $requirement['specific']  = ( ( array_key_exists( 'specific', $requirement_setup ) ) ? sanitize_text_field( $requirement_setup['specific'] ) : '' );

                            $level_requirements[ $row ] = $requirement;
                            $row ++;

                        }

                        if ( $level_row == 0 )
                            $badge_requirements = $level_requirements;

                        $completed_requirements = array();
                        foreach ( $level_requirements as $requirement_id => $requirement_setup ) {

                            if ( $level_row == 0 ) {
                                $completed_requirements[ $requirement_id ] = $requirement_setup;
                                continue;
                            }

                            $completed_requirements[ $requirement_id ]           = $badge_requirements[ $requirement_id ];
                            $completed_requirements[ $requirement_id ]['amount'] = $requirement_setup['amount'];

                        }

                        $level['requires'] = $completed_requirements;

                    }

                    $reward = array( 'type' => '', 'log' => '', 'amount' => '' );

                    if ( array_key_exists( 'reward', $level_setup ) ) {

                        $reward['type'] = sanitize_key( $level_setup['reward']['type'] );
                        $reward['log']  = sanitize_text_field( $level_setup['reward']['log'] );

                        if ( $reward['type'] != MYCRED_DEFAULT_TYPE_KEY )
                            $mycred = mycred( $reward['type'] );
                        else
                            $mycred = $this->core;

                        $reward['amount'] = $mycred->number( $level_setup['reward']['amount'] );

                    }

                    $level['reward']  = $reward;

                    $badge_levels[] = $level;
                    $level_row ++;

                }
            }

            // Save Badge Setup
            mycred_update_post_meta( $post_id, 'badge_prefs', $badge_levels );

            // If we just set the badge to be manual we need to re-parse all references.
            $old_manual = mycred_get_post_meta( $post_id, 'manual_badge', true );
            if ( absint( $old_manual ) === 0 && $manual === 1 ) {
                foreach ( $this->point_types as $type_id => $label ) {
                    mycred_get_badge_references( $type_id, true );
                }
            }

            // Force re-calculation of used references
            foreach ( $this->point_types as $type_id => $type )
                mycred_delete_option( 'mycred-badge-refs-' . $type_id );

            // Save if badge is manuall
            mycred_update_post_meta( $post_id, 'manual_badge', $manual );

            mycred_update_post_meta( $post_id, 'open_badge', $open_badge );

            mycred_update_post_meta( $post_id, 'congratulation_msg', $congratulation_msg );

            mycred_update_post_meta( $post_id, 'main_image', $image );

            mycred_update_post_meta( $post_id, 'mycred_badge_align', $badge_align );

            mycred_update_post_meta( $post_id, 'mycred_layout_check', $layout );

            // Let others play
            do_action( 'mycred_save_badge', $post_id );

        }

        /**
         * Add to General Settings
         * @since 1.0
         * @version 1.1
         */
        public function after_general_settings( $mycred = NULL ) {      

            $settings   = $this->badges;

            $buddypress = ( ( class_exists( 'BuddyPress' ) ) ? true : false );
            $bbpress    = ( ( class_exists( 'bbPress' ) ) ? true : false );

            ?>
            <h4><span class="dashicons dashicons-admin-plugins static"></span><?php _e( 'Badges', 'mycred' ); ?></h4>
            <div class="body" style="display:none;">

                <h3><?php _e( 'Single Badge Page', 'mycred' ); ?></h3>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        
                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_level_description' ); ?>">
                                    <input type="checkbox" name="<?php echo $this->field_name( 'show_level_description' ); ?>" id="<?php echo $this->field_id( 'show_level_description' ); ?>" <?php checked( $settings['show_level_description'], 1 ); ?> value="1"><?php _e('Show Level Description', 'mycred'); ?>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_congo_text' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_congo_text' ); ?>" id="<?php echo $this->field_id( 'show_congo_text' ); ?>" <?php checked( $settings['show_congo_text'], 1 ); ?> value="1"> <?php _e('Show Congratulation Text', 'mycred'); ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_steps_to_achieve' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_steps_to_achieve' ); ?>" id="<?php echo $this->field_id( 'show_steps_to_achieve' ); ?>" <?php checked( $settings['show_steps_to_achieve'], 1 ); ?> value="1"> <?php _e('Show Steps to Achieve', 'mycred'); ?></label>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_levels' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_levels' ); ?>" id="<?php echo $this->field_id( 'show_levels' ); ?>" <?php checked( $settings['show_levels'], 1 ); ?> value="1"> <?php _e('Show Levels', 'mycred'); ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_level_points' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_level_points' ); ?>" id="<?php echo $this->field_id( 'show_level_points' ); ?>" <?php checked( $settings['show_level_points'], 1 ); ?> value="1"> <?php _e('Show Level Reward', 'mycred'); ?></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox" style="padding-top: 4px;">
                                <label for="<?php echo $this->field_id( 'show_earners' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_earners' ); ?>" id="<?php echo $this->field_id( 'show_earners' ); ?>" <?php checked( $settings['show_earners'], 1 ); ?> value="1"> <?php _e('Show Earners', 'mycred'); ?></label>
                            </div>
                        </div>

                    </div>
                </div>

                <h3><?php _e( 'Open Badge', 'mycred' ); ?></h3>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id( 'open_badge' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'open_badge' ); ?>" id="<?php echo $this->field_id( 'open_badge' ); ?>" <?php checked( $settings['open_badge'], 1 ); ?> value="1" > <?php _e( 'Enable Open Badge.', 'mycred' ); ?></label>
                            </div>
                        </div>
                    </div>
                    <?php if( $settings['open_badge'] == '1' ):?>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="<?php echo $this->field_id( 'open_badge_evidence_page' ); ?>"><?php _e( 'Evidence Page', 'mycred' ); ?></label>
                                <?php       

                                    $selectedEvidencePage = mycred_get_evidence_page_id();   

                                    $args = array(
                                        'id'       => $this->field_id( 'open_badge_evidence_page' ),
                                        'name'     => $this->field_name( 'open_badge_evidence_page' ),
                                        'selected' => $selectedEvidencePage
                                    );

                                    wp_dropdown_pages( $args );
                                ?>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <button class="button button-large large button-primary" id="switch-all-to-open-badge"><span class="dashicons dashicons-update mycred-switch-all-badges-icon"></span> Switch All Badges To Open Badge.</button>
                            </div>
                        </div>
                    <?php endif;?>
                </div>

                <h3><?php _e( 'Third-party Integrations', 'mycred' ); ?></h3>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id( 'buddypress' ); ?>">BuddyPress</label>
                            <?php if ( $buddypress ) : ?>
                            <select name="<?php echo $this->field_name( 'buddypress' ); ?>" id="<?php echo $this->field_id( 'buddypress' ); ?>" class="form-control">
                                <?php

                                $buddypress_options = array(
                                    ''        => __( 'Do not show', 'mycred' ),
                                    'header'  => __( 'Include in Profile Header', 'mycred' ),
                                    'profile' => __( 'Include under the "Profile" tab', 'mycred' ),
                                    'both'    => __( 'Include under the "Profile" tab and Profile Header', 'mycred' )
                                );

                                foreach ( $buddypress_options as $location => $description ) {
                                    echo '<option value="' . $location . '"';
                                    if ( isset( $settings['buddypress'] ) && $settings['buddypress'] == $location ) echo ' selected="selected"';
                                    echo '>' . $description . '</option>';
                                }

                                ?>

                            </select>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id( 'show_all_bp' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_all_bp' ); ?>" id="<?php echo $this->field_id( 'show_all_bp' ); ?>" <?php checked( $settings['show_all_bp'], 1 ); ?> value="1" /> <?php _e( 'Show all badges, including badges users have not yet earned.', 'mycred' ); ?></label>
                            </div>
                            <?php else : ?>
                                <input type="hidden" name="<?php echo $this->field_name( 'buddypress' ); ?>" id="<?php echo $this->field_id( 'buddypress' ); ?>" value="" />
                                <p><span class="description"><?php _e( 'Not installed', 'mycred' ); ?></span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="<?php echo $this->field_id( 'bbpress' ); ?>">bbPress</label>
                            <?php if ( $bbpress ) : ?>
                            <select name="<?php echo $this->field_name( 'bbpress' ); ?>" id="<?php echo $this->field_id( 'bbpress' ); ?>" class="form-control">
                                <?php

                                $bbpress_options = array(
                                    ''        => __( 'Do not show', 'mycred' ),
                                    'profile' => __( 'Include in Profile', 'mycred' ),
                                    'reply'   => __( 'Include in Forum Replies', 'mycred' ),
                                    'both'    => __( 'Include in Profile and Forum Replies', 'mycred' )
                                );

                                foreach ( $bbpress_options as $location => $description ) {
                                    echo '<option value="' . $location . '"';
                                    if ( isset( $settings['bbpress'] ) && $settings['bbpress'] == $location ) echo ' selected="selected"';
                                    echo '>' . $description . '</option>';
                                }

                                ?>

                            </select>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label for="<?php echo $this->field_id( 'show_all_bb' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'show_all_bb' ); ?>" id="<?php echo $this->field_id( 'show_all_bb' ); ?>" <?php checked( $settings['show_all_bb'], 1 ); ?> value="1" /> <?php _e( 'Show all badges, including badges users have not yet earned.', 'mycred' ); ?></label>
                            </div>
                            <?php else : ?>
                                <input type="hidden" name="<?php echo $this->field_name( 'bbpress' ); ?>" id="<?php echo $this->field_id( 'bbpress' ); ?>" value="" />
                                <p><span class="description"><?php _e( 'Not installed', 'mycred' ); ?></span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <h3 style="margin-bottom: 0;"><?php _e( 'Available Shortcodes', 'mycred' ); ?></h3>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p><a href="http://codex.mycred.me/shortcodes/mycred_my_badges/" target="_blank">[mycred_my_badges]</a>, <a href="http://codex.mycred.me/shortcodes/mycred_badges/" target="_blank">[mycred_badges]</a></p>
                    </div>
                </div>
                <?php do_action( 'mycred_admin_after_badges_settings' ); ?>
            </div>
            <?php

        }

        /**
         * Save Settings
         * @since 1.0
         * @version 1.0.3
         */
        public function sanitize_extra_settings( $new_data, $data, $core ) {

            $new_data['badges']['show_all_bp'] = ( isset( $data['badges']['show_all_bp'] ) ) ? $data['badges']['show_all_bp'] : 0;
            $new_data['badges']['show_all_bb'] = ( isset( $data['badges']['show_all_bb'] ) ) ? $data['badges']['show_all_bb'] : 0;

            $new_data['badges']['buddypress']  = ( isset( $data['badges']['buddypress'] ) ) ? sanitize_text_field( $data['badges']['buddypress'] ) : '';
            $new_data['badges']['bbpress']     = ( isset( $data['badges']['bbpress'] ) ) ? sanitize_text_field( $data['badges']['bbpress'] ) : '';
            
            $new_data['badges']['open_badge']  = ( isset( $data['badges']['open_badge'] ) ) ? intval( $data['badges']['open_badge'] ) : 0;

            //Specific Badge Page Setup @since 2.1
            $new_data['badges']['show_level_description'] = ( isset( $data['badges']['show_level_description'] ) ) ? intval( $data['badges']['show_level_description'] ) : 0;
            $new_data['badges']['show_congo_text'] = ( isset( $data['badges']['show_congo_text'] ) ) ? intval( $data['badges']['show_congo_text'] ) : 0;
            $new_data['badges']['show_levels'] = ( isset( $data['badges']['show_levels'] ) ) ? intval( $data['badges']['show_levels'] ) : 0;
            $new_data['badges']['show_level_points'] = ( isset( $data['badges']['show_level_points'] ) ) ? intval( $data['badges']['show_level_points'] ) : 0;
            $new_data['badges']['show_steps_to_achieve'] = ( isset( $data['badges']['show_steps_to_achieve'] ) ) ? intval( $data['badges']['show_steps_to_achieve'] ) : 0;
            $new_data['badges']['show_earners'] = ( isset( $data['badges']['show_earners'] ) ) ? intval( $data['badges']['show_earners'] ) : 0;
            $new_data['badges']['open_badge_evidence_page'] = ( isset( $data['badges']['open_badge_evidence_page'] ) ) ? intval( $data['badges']['open_badge_evidence_page'] ) : 0;


            return $new_data;

        }

        /**
         * User Badges Admin Screen
         * @since 1.0
         * @version 1.1
         */
        public function badge_user_screen( $user ) {

            // Only visible to admins
            if ( ! mycred_is_admin() ) return;

            $user_id      = $user->ID;
            $all_badges   = mycred_get_badge_ids();
            $users_badges = mycred_get_users_badges( $user_id );

            ?>
            <style type="text/css">
                .badge-wrapper { min-height: 230px; }
                .badge-wrapper select { width: 100%; }
                .badge-image-wrap { text-align: center; }
                .badge-image-wrap .badge-image { display: block; width: 100%; height: 100px; line-height: 100px; }
                .badge-image-wrap .badge-image.empty { content: "<?php _e( 'No image set', 'mycred' ); ?>"; }
                .badge-image-wrap .badge-image img { width: auto; height: auto; max-height: 100px; }
            </style>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'Badges', 'mycred' ); ?></th>
                    <td>
                        <fieldset id="mycred-badge-list" class="badge-list">
                            <legend class="screen-reader-text"><span><?php _e( 'Badges', 'mycred' ); ?></span></legend>
                            <?php

                            if ( ! empty( $all_badges ) ) {
                                foreach ( $all_badges as $badge_id ) {

                                    $badge_id     = absint( $badge_id );
                                    $badge        = mycred_get_badge( $badge_id );
                                    $earned       = 0;
                                    $earned_level = 0;
                                    $badge_image  = $badge->main_image;

                                    if ( array_key_exists( $badge_id, $users_badges ) ) {
                                        $earned       = 1;
                                        $earned_level = $users_badges[ $badge_id ];
                                        $badge_image  = $badge->get_image( $earned_level );
                                    }

                                    $level_select = '<input type="hidden" name="mycred_badge_manual[badges][' . $badge_id . '][level]" value="0" /><select disabled="disabled"><option>Level 1</option></select>';
                                    if ( count( $badge->levels ) > 1 ) {

                                        $level_select  = '<select name="mycred_badge_manual[badges][' . $badge_id . '][level]">';
                                        $level_select .= '<option value=""';
                                        if ( ! $earned ) $level_select .= ' selected="selected"';
                                        $level_select .= '>' . __( 'Select a level', 'mycred' ) . '</option>';

                                        foreach ( $badge->levels as $level_id => $level ) {
                                            $level_select .= '<option value="' . $level_id . '"';
                                            if ( $earned && $earned_level == $level_id ) $level_select .= ' selected="selected"';
                                            $level_select .= '>' . ( ( $level['label'] != '' ) ? $level['label'] : sprintf( '%s %d', __( 'Level', 'mycred' ), ( $level_id + 1 ) ) ) . '</option>';
                                        }

                                        $level_select .= '</select>';

                                    }

                                    ?>
                                    <div class="badge-wrapper color-option<?php if ( $earned === 1 ) echo ' selected'; ?>" id="mycred-badge<?php echo $badge_id; ?>-wrapper">
                                        <label for="mycred-badge<?php echo $badge_id; ?>"><input type="checkbox" name="mycred_badge_manual[badges][<?php echo $badge_id; ?>][has]" class="toggle-badge" id="mycred-badge<?php echo $badge_id; ?>" <?php checked( $earned, 1 );?> value="1" /> <?php _e( 'Earned', 'mycred' ); ?></label>
                                        <div class="badge-image-wrap">

                                            <div class="badge-image<?php if ( $badge_image == '' ) echo ' empty'; ?>"><?php echo $badge_image; ?></div>

                                            <h4><?php echo $badge->title; ?></h4>
                                        </div>
                                        <div class="badge-actions" style="min-height: 32px;">

                                            <?php echo $level_select; ?>

                                        </div>

                                        <?php if ( $badge->open_badge && array_key_exists( $badge_id, $users_badges ) ):?>
                                        <div class="badge-image-wrap">
                                            <a href="<?php echo $badge->get_earned_image( $user_id ); ?> " class="button button-primary button-large mycred-open-badge-download" download>Download</a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php

                                }
                            }

                            ?>
                        </fieldset>
                        <input type="hidden" name="mycred_badge_manual[token]" value="<?php echo wp_create_nonce( 'mycred-manual-badges' . $user_id ); ?>" />
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                jQuery(function($) {

                    $( '.badge-wrapper label input.toggle-badge' ).click(function(){

                        if ( $(this).is( ':checked' ) )
                            $( '#' + $(this).attr( 'id' ) + '-wrapper' ).addClass( 'selected' );

                        else
                            $( '#' + $(this).attr( 'id' ) + '-wrapper' ).removeClass( 'selected' );

                    });

                });
            </script>
            <?php

        }

        /**
         * Save Manual Badges
         * @since 1.0
         * @version 1.1
         */
        public function save_manual_badges( $user_id ) {

            if ( ! mycred_is_admin() ) return;

            if ( isset( $_POST['mycred_badge_manual']['token'] ) ) {

                if ( wp_verify_nonce( $_POST['mycred_badge_manual']['token'], 'mycred-manual-badges' . $user_id ) ) {

                    $added        = $removed = $updated = 0;
                    $users_badges = mycred_get_users_badges( $user_id );

                    if ( ! empty( $_POST['mycred_badge_manual']['badges'] ) ) {
                        foreach ( $_POST['mycred_badge_manual']['badges'] as $badge_id => $data ) {

                            $badge = mycred_get_badge( $badge_id );

                            // Most likely not a badge post ID
                            if ( $badge === false ) continue;

                            // Give badge
                            if ( ! array_key_exists( $badge_id, $users_badges ) && isset( $data['has'] ) && $data['has'] == 1 ) {

                                $level = 0;
                                if ( isset( $data['level'] ) && $data['level'] != '' )
                                    $level = absint( $data['level'] );

                                $badge->assign( $user_id, $level );
                                
                                $added ++;

                            }

                            // Remove badge
                            elseif ( array_key_exists( $badge_id, $users_badges ) && ! isset( $data['has'] ) ) {

                                $badge->divest( $user_id );

                                $removed ++;

                            }

                            // Level change
                            elseif ( array_key_exists( $badge_id, $users_badges ) && isset( $data['level'] ) && $data['level'] != $users_badges[ $badge_id ] ) {

                                $badge->assign( $user_id, $data['level'] );

                                $updated ++;

                            }

                        }
                    }

                    if ( $added > 0 || $removed > 0 || $updated > 0 )
                        mycred_delete_user_meta( $user_id, 'mycred_badge_ids' );

                }

            }

        }

        /**
         * AJAX: Assign Badge
         * @since 1.0
         * @version 1.3
         */
        public function action_assign_badge() {

            check_ajax_referer( 'mycred-assign-badge', 'token' );

            $badge_id = absint( $_POST['badge_id'] );
            if ( $badge_id === 0 ) wp_send_json_error();

            // Get the badge object
            $badge    = mycred_get_badge( $badge_id );

            // Most likely not a badge post ID
            if ( $badge === false ) wp_send_json_error();

            $results = $badge->assign_all();

            if ( $results > 0 )
                wp_send_json_success( sprintf( __( 'A total of %d users have received this badge.', 'mycred' ), $results ) );

            wp_send_json_error( __( 'No users has yet earned this badge.', 'mycred' ) );

        }

        /**
         * AJAX: Remove Badge Connections
         * @since 1.0
         * @version 1.1
         */
        public function action_remove_connections() {

            check_ajax_referer( 'mycred-remove-badge-connection', 'token' );

            $badge_id = absint( $_POST['badge_id'] );
            if ( $badge_id === 0 ) wp_send_json_error();

            // Get the badge object
            $badge    = mycred_get_badge( $badge_id );

            // Most likely not a badge post ID
            if ( $badge === false ) wp_send_json_error();

            $results = $badge->divest_all();

            if ( $results == 0 )
                wp_send_json_success( __( 'No connections where removed.', 'mycred' ) );

            wp_send_json_success( sprintf( __( '%s connections where removed.', 'mycred' ), $results ) );

        }

        /**
         * Insert Badges into bbPress profile
         * @since 1.0
         * @version 1.1
         */
        public function insert_into_bbpress_profile() {

            $user_id = bbp_get_displayed_user_id();
            if ( isset( $this->badges['show_all_bb'] ) && $this->badges['show_all_bb'] == 1 )
                echo mycred_render_my_badges( array(
                    'show'    => 'all',
                    'width'   => MYCRED_BADGE_WIDTH,
                    'height'  => MYCRED_BADGE_HEIGHT,
                    'user_id' => $user_id
                ) );

            else
                mycred_display_users_badges( $user_id );

        }

        /**
         * Insert Badges into bbPress
         * @since 1.0
         * @version 1.1
         */
        public function insert_into_bbpress_reply() {

            $user_id = bbp_get_reply_author_id();

            if ( $user_id > 0 ) {

                if ( isset( $this->badges['show_all_bb'] ) && $this->badges['show_all_bb'] == 1 )
                    echo mycred_render_my_badges( array(
                        'show'    => 'all',
                        'width'   => MYCRED_BADGE_WIDTH,
                        'height'  => MYCRED_BADGE_HEIGHT,
                        'user_id' => $user_id
                    ) );

                else
                    mycred_display_users_badges( $user_id );

            }

        }

        /**
         * Insert Badges in BuddyPress
         * @since 1.0
         * @version 1.1.1
         */
        public function insert_into_buddypress() {

            $user_id = bp_displayed_user_id();
            if ( isset( $this->badges['show_all_bp'] ) && $this->badges['show_all_bp'] == 1 )
                echo mycred_render_my_badges( array(
                    'show'    => 'all',
                    'width'   => MYCRED_BADGE_WIDTH,
                    'height'  => MYCRED_BADGE_HEIGHT,
                    'user_id' => $user_id
                ) );

            else
                mycred_display_users_badges( $user_id );

        }

        /**
         * Init Open Badge
         * @since 2.1
         * @version 1.0
         */
        public function mycred_open_badge_init() {

            $mycred_Open_Badge = new mycred_Open_Badge();

            add_action( 'mycred_after_badge_assign', array( $mycred_Open_Badge, 'bake_users_image' ), 10, 2 );
            add_action( 'rest_api_init',             array( $mycred_Open_Badge, 'register_open_badge_routes' ) );
        
        }

        /**
         * Loads meta in header for Social Sharing
         * @since 2.2
         * @version 1.0
         */
        public function social_share_br_header() {

            global $post;

            if( is_single() && $post->post_type == MYCRED_BADGE_KEY ) {
            
                $badge_id = $post->ID;

                $user_id = get_current_user_id();

                $badge_object = mycred_get_badge( $badge_id );
                
                $badge_image_url = $badge_object->get_earned_image( $user_id );?>

                <meta property="og:url" content="<?php echo esc_url( get_the_permalink() ); ?>">
                <meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>">
                <meta property="og:description" content="<?php echo esc_attr( $post->post_content );?>">
                <meta property="og:image" content="<?php echo esc_url( $badge_image_url ); ?>">
                <meta name="twitter:image" content="<?php echo esc_url( $badge_image_url ); ?>">
                <meta name="twitter:card" content="summary_large_image">

            <?php
            }

        }

        /**
         * Automatically runs when MYCRED_BADGE_KEY (Post Type Page Loads)
         * @param $content
         * @return string
         * @since 2.1
         * @version 1.2
         */
        public function mycred_badge_page_template( $content ) {

            global $post;

            if ( is_single() && $post->post_type == MYCRED_BADGE_KEY ) {

                $mycred = mycred();

                if ( is_array( $mycred->core ) && array_key_exists( 'badges', $mycred->core ) ) {

                    $user_id        = get_current_user_id();
                    $badge          = mycred_get_badge( $post->ID );
                    $badge_settings = $mycred->core["badges"];

                    $content = '<div class="mycred-badge-page">';

                        $content .= mycred_badge_show_congratulation_msg( $user_id, $badge, $mycred );
                        
                        $content .= '<div class="'. $badge->layout .' '. $badge->align .'">';

                            if( $badge->layout != 'mycred_layout_bottom' )
                                $content .= mycred_badge_show_main_image_with_social_icons( $user_id, $badge, $mycred );

                            $content .= '<div class="mycred_content">';

                                $content .= mycred_badge_show_description( $post, $mycred );
                                $content .= mycred_badge_show_levels( $user_id, $badge, $mycred );
                                $content .= mycred_badge_show_earners( $badge, $mycred );

                            $content .= '</div>';

                            if( $badge->layout == 'mycred_layout_bottom' )
                                $content .= mycred_badge_show_main_image_with_social_icons( $user_id, $badge, $mycred );

                            $content .= '<div class="mycred-clearfix"></div>';

                        //layout
                        $content .= '</div>';
                    
                    //mycred-badge-page
                    $content .= '</div>';

                
                }

            }

            return $content;

        }

        public function flush_rewrite_rules_for_badges() {

            global $pagenow;

            if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'mycred-addons' && isset( $_GET['badges'] ) && $_GET['badges'] == 'activate' ) {           

                flush_rewrite_rules();  
            
            }

        }

    }
endif;

/**
 * Load Badges Module
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_badges_addon' ) ) :
    function mycred_load_badges_addon( $modules, $point_types ) {

        $modules['solo']['badges'] = new myCRED_Badge_Module();
        $modules['solo']['badges']->load();

        return $modules;

    }
endif;
add_filter( 'mycred_load_modules', 'mycred_load_badges_addon', 10, 2 );