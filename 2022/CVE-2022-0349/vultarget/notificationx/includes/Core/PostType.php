<?php

namespace NotificationX\Core;

use NotificationX\Admin\Admin;
use NotificationX\Admin\Cron;
use NotificationX\Admin\Entries;
use NotificationX\Admin\Settings;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\Extensions\GlobalFields;
use NotificationX\FrontEnd\FrontEnd;
use NotificationX\GetInstance;
use NotificationX\NotificationX;

class PostType {
    /**
     * Instance of PostType
     *
     * @var PostType
     */
    use GetInstance;

    /**
     * The type.
     *
     * @since    1.0.0
     * @access   public
     * @var string the post type of notificationx.
     */
    public $type = 'notificationx';
    public $active_items;
    public $enabled_source;
    public $_edit_link = "admin.php?page=nx-edit&post=%d";

    /**
     * Initially Invoked when initialized.
     * @hook init
     */
    public function __construct() {
        // add_action('init', array($this, 'register'));
        add_action('admin_menu', [$this, 'menu'], 15);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_filter('nx_get_post', [$this, 'get_theme_preview_image']);
		add_image_size( "_nx_notification_thumb", 100, 100, true );

    }

    /**
     * This method is reponsible for Admin Menu of
     * NotificationX
     *
     * @return void
     */
    public function menu() {
        add_submenu_page('nx-admin', __('Add New', 'notificationx'), __('Add New', 'notificationx'), 'edit_notificationx', 'nx-edit', [Admin::get_instance(), 'views'], 20);
        // add_submenu_page('nx-admin', 'Edit', 'Edit', 'edit_notificationx', 'nx-edit', [Admin::get_instance(), 'views'], 20);
    }

    /**
     * Register scripts and styles.
     *
     * @param string $hook
     * @return void
     */
    function admin_enqueue_scripts($hook) {
        if ($hook !== "toplevel_page_nx-admin" && $hook !== "notificationx_page_nx-edit" && $hook !== "notificationx_page_nx-settings" && $hook !== "notificationx_page_nx-analytics" && $hook !== "notificationx_page_nx-builder") {
            return;
        }
        // @todo not sure why did it. maybe remove.
        wp_enqueue_media();

        $tabs = $this->get_localize_scripts();

        $d = include Helper::file('admin/js/admin.asset.php');

        wp_enqueue_script(
            'notificationx-admin',
            Helper::file( 'admin/js/admin.js', true ),
            $d['dependencies'],
            $d['version'],
            true
        );
        wp_localize_script('notificationx-admin', 'notificationxTabs', $tabs);
        wp_enqueue_style( 'notificationx-admin', Helper::file( 'admin/css/admin.css', true ), [], $d['version'], 'all' );
        wp_set_script_translations( 'notificationx-admin', 'notificationx' );
        do_action('notificationx_admin_scripts');

    }

    public function get_localize_scripts(){
        $tabs                           = NotificationX::get_instance()->normalize( GlobalFields::get_instance()->tabs() );

        $tabs['createRedirect']               = !current_user_can( 'edit_notificationx' );
        $tabs['analyticsRedirect']            = !(current_user_can( 'read_notificationx_analytics' ) && Settings::get_instance()->get('settings.enable_analytics', true));
        $tabs['quick_build']                  = NotificationX::get_instance()->normalize( QuickBuild::get_instance()->tabs() );
        $tabs['rest']                         = REST::get_instance()->rest_data();
        $tabs['current_page']                 = 'add-nx';
        $tabs['analytics']                    = Analytics::get_instance()->get_total_count();
        $tabs['settings']                     = Settings::get_instance()->get_form_data();
        $tabs['settings']['settingsRedirect'] = !current_user_can( 'edit_notificationx_settings' );
        $tabs['settings']['analytics']        = $tabs['analytics'];
        $tabs['admin_url']                    = get_admin_url();
        $tabs['assets']                       = [
            'admin'  => NOTIFICATIONX_ADMIN_URL,
            'public' => NOTIFICATIONX_PUBLIC_URL,
        ];

        $tabs = apply_filters('nx_builder_configs', $tabs);
        return $tabs;
    }

    /**
     * Save data on post save.
     *
     * @param int $post_id
     * @return void
     */
    public function save_post($data) {
        $results = [
            'status' => 'success',
        ];

        if(!empty($data['update_status'])){
            return $this->update_status($data);
        }

        if(!isset($data['enabled'])){
            $data['enabled'] = $this->can_enable($data['source']);
        }

        $title = isset($data['title']) ? $data['title'] : '';
        unset($data['title']);

        $post = [
            'type'         => $data['type'],
            'source'       => $data['source'],
            'theme'        => $data['themes'],
            'global_queue' => !empty($data['global_queue']) ? $data['global_queue'] : false,
            'enabled'      => $data['enabled'],
            'is_inline'    => !empty( $data['inline_location'] ),
            'title'        => $title,
            'data'         => $data
        ];
        if( isset( $data['updated_at'] ) ) {
            $post['updated_at'] = $data['updated_at'];
        }

        $nx_id = isset($data['nx_id']) ? $data['nx_id'] : 0;

        $post = apply_filters( "nx_save_post_{$data['source']}", $post, $data, $nx_id );
        $post = apply_filters( 'nx_save_post', $post, $data, $nx_id );

        if(!empty($nx_id)){
            if(empty($post['updated_at'])){
                $post['updated_at'] = Helper::mysql_time();
            }
            if($this->update_post($post, $nx_id) === false){
                $results['status'] = 'error';
            }
        } else {
            $nx_id = $this->insert_post($post);
        }
        $data['nx_id'] = $nx_id;
        $post['nx_id'] = $nx_id;
        $post['data']['nx_id'] = $nx_id;
        // return $GLOBALS['wpdb']->last_query;

        $data = apply_filters("nx_get_post_{$data['source']}", $data);
        $data = apply_filters('nx_get_post', $data);
        do_action("nx_saved_post_{$data['source']}", $post, $data, $nx_id);
        do_action('nx_saved_post', $post, $data, $nx_id);

        $results['nx_id'] = $nx_id;
        return $data;
    }

    /**
     * Save data on post save.
     *
     * @param int $post_id
     * @return bool
     */
    public function update_status($data) {
        $is_enabled = $this->is_enabled($data['nx_id']);
        if($is_enabled == $data['enabled']){
            return true;
        }
        if( $this->can_enable( $data['source'] ) || ( isset( $data['enabled'] ) && $data['enabled'] == false ) ){
            $post = [
                'enabled'    => $data['enabled'],
                // 'updated_at' => Helper::mysql_time(),
            ];
            if($data['enabled'] == false){
                // clear cron when disabled.
                Cron::get_instance()->clear_schedule(array('post_id' => $data['nx_id']));
            }
            $this->update_enabled_source($data);
            return $this->update_post($post, $data['nx_id']);
        }
        return false;
    }

    /**
     * Save data on post save.
     *
     * @param int $post_id
     * @return void
     */
    public function update_meta($nx_id, $key, $value) {
        $post = Database::get_instance()->get_post(Database::$table_posts, $nx_id, 'data, updated_at');
        $post['data'][$key] = $value;
        return $this->update_post($post, $nx_id);
    }

    public function get_active_items(){
		if(!is_array($this->active_items)){
			$this->active_items = $this->get_col('source', []);
		}
		return $this->active_items;
    }

	public function get_enabled_source() {
		if(!is_array($this->enabled_source)){
            $this->enabled_source = [];
			$enabled_source = $this->get_posts([
                'enabled' => true,
            ], 'nx_id, source');
            if ( is_array( $enabled_source ) ) {
                foreach ( $enabled_source as $post ) {
                    $this->enabled_source[ $post['source'] ][] = $post['nx_id'];
                }
            }
		}
		return $this->enabled_source;
	}

    public function update_enabled_source($post){
        if(empty($post['source']) || empty($post['nx_id'])){
            return;
        }
        if(!empty($this->enabled_source[$post['source']])){
            foreach ($this->enabled_source as $source => $ids) {
                if($post['enabled']){
                    if(!in_array($post['nx_id'], $ids)){
                        $this->enabled_source[$source][] = $post['nx_id'];
                    }
                }
                else{
                    if($key = array_search($post['nx_id'], $ids)){
                        unset($this->enabled_source[$source][$key]);
                    }
                }

            }
        }
        else{
            if($post['enabled']){
                $this->enabled_source[$post['source']][] = $post['nx_id'];
            }
        }
    }

    public function is_enabled($id){
        $enabled_source = $this->get_enabled_source();
        foreach ($enabled_source as $source => $ids) {
            if(in_array($id, $ids)){
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether a notification can be enabled.
     *
     * @param string $source
     * @return boolean
     */
    public function can_enable($source){
        if( $source === 'press_bar' ) {
            return true;
        }

        $ext = ExtensionFactory::get_instance()->get($source);
        if($ext && $ext->is_pro && !NotificationX::is_pro()){
            return false;
        }

        $enabled_source = $this->get_enabled_source();
        unset($enabled_source['press_bar']);
        if( count( $enabled_source ) == 0 ) {
            return true;
        }
        return false;
    }

    // Wrapper function for Database functions.
    public function insert_post($post){
        if(empty($post['created_at'])){
            $post['created_at'] = Helper::mysql_time();
        }
        if(empty($post['updated_at'])){
            $post['updated_at'] = Helper::mysql_time();
        }
        return Database::get_instance()->insert_post(Database::$table_posts, $post);
    }

    public function update_post($post, $post_id){
        return Database::get_instance()->update_post(Database::$table_posts, $post, $post_id);
    }

    public function get_post($post_id, $select = "*"){
        $posts = $this->get_posts([
            'nx_id' => $post_id,
        ], $select);

        return !empty($posts[0]) ? $posts[0] : null;
    }

    public function get_posts($wheres = [], $select ="*", $join_table = '', $group_by_col = '', $join_type = 'LEFT JOIN', $extra_query = ''){
        $posts = Database::get_instance()->get_posts(Database::$table_posts, $select, $wheres, $join_table, $group_by_col, $join_type, $extra_query);
        foreach ($posts as $key => $value) {
            if(!empty($value['data'])){
                $value = array_merge($value['data'], $value);
                $value['enabled'] = (bool) $value['enabled'];
                $value['global_queue'] = (bool) $value['global_queue'];
                unset($value['data']);
            }
            // @todo maybe remove if there is another better way.
            if($select == "*"){
                $value       = NotificationX::get_instance()->normalize_post($value);
            }
            if(!empty($value['source'])){
                $value       = apply_filters("nx_get_post_{$value['source']}", $value);
            }
            $posts[$key] = apply_filters('nx_get_post', $value);
        }
        $posts = apply_filters('nx_get_posts', $posts);
        return $posts;
    }

    public function get_post_with_analytics($wheres = [], $extra_query = ''){
        $posts = $this->get_posts($wheres, 'a.*, SUM(b.clicks) AS clicks, SUM(b.views) AS views', Database::$table_stats, 'a.nx_id', 'LEFT JOIN', $extra_query);
        foreach ($posts as $key => $post) {
            $source = $post['source'];
            $posts[$key]['can_regenerate'] = false;
            $extension = ExtensionFactory::get_instance()->get($source);
            $posts[$key]['source_label'] = $extension->title;
            if (!empty($extension) && method_exists($extension, 'get_notification_ready') && $extension->is_active(false)) {
                $posts[$key]['can_regenerate'] = true;
            }
            if(!empty($extension) && $extension->get_type()){
                $type = $extension->get_type();
                $posts[$key]['type_label'] = $type->title;
            }
        }
        return $posts;
    }

    public function get_col($col, $wheres){
        return Database::get_instance()->get_col(Database::$table_posts, $col, $wheres);
    }

    public function delete_post($post_id){
        $post = $this->get_post($post_id);
        $results = Database::get_instance()->delete_post(Database::$table_posts, $post_id);
        Entries::get_instance()->delete_entries($post_id);
        Database::get_instance()->delete_posts(Database::$table_stats, ['nx_id' => $post_id]);

        do_action('nx_delete_post', $post_id, $post);
        return $results;
    }

    public function get_theme_preview_image($post) {
        $url = '';

        if(!empty($post['source']) && !empty($post['theme'])){
            $source = $post['source'];
            $theme  = $post['theme'];
            if ($ex = ExtensionFactory::get_instance()->get($source)) {
                $themes = $ex->get_themes();
                if(!empty($themes[$theme]['source'])){
                    $url = $themes[$theme]['source'];
                }
            }
            $post['preview'] = apply_filters("nx_theme_preview_{$post['source']}", $url, $post);
        }

        return $post;
    }

    public function get_edit_link($nx_id){
        return admin_url("admin.php?page=nx-edit&id=$nx_id");
    }
}
