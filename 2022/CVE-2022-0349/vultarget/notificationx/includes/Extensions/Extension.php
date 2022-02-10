<?php

/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions;

use NotificationX\NotificationX;
use NotificationX\Admin\Entries;
use NotificationX\Admin\Settings;
use NotificationX\Core\Database;
use NotificationX\Core\Helper;
use NotificationX\Core\Modules;
use NotificationX\Core\PostType;
use NotificationX\Core\Rules;
use NotificationX\Core\Themes;
use NotificationX\Types\TypeFactory;
use NotificationX\Types\Types;
use NotificationX\Core\Limiter;

/**
 * Extension Abstract for all Extension.
 */
abstract class Extension {

    public $id;
    public $title;
    public $img             = '';
    public $doc_link        = 'https://notificationx.com/docs/';
    public $types           = '';
    public $themes          = [];
    public $selected_themes = [];
    public $is_pro          = false;
    public $has_link_types  = false;
    public $module          = '';
    public $module_title    = '';
    public $version         = '';
    public $class           = '';
    public $function        = '';
    public $templates       = [];
    public $exclude_custom_themes = false;

    /**
     * All Active Notification Items
     *
     * @var array
     */
    public $enabled_types  = [];
    public $default_theme = '';

    // @todo Something
    // public abstract function save_post($post_id, $post, $update);
    // public abstract function get_notification_ready($type, $data = array());

    /**
     * Initially Invoked when initialized.
     *
     * @hook init; Called in init hook.
     */
    public function __construct() {
        $modules = Modules::get_instance();
        $module_name = $this->register_module();
        if ($modules->is_enabled($module_name)) {
            $type_factory = TypeFactory::get_instance();
            $type_factory->register_types($this->types);
            $this->initialize();
        }
    }

    public function initialize(){
        do_action('nx::extension::init', $this);
        add_action('nx_before_metabox_load', [$this, 'init_fields']);
        add_action('nx_before_settings_fields', [$this, 'init_settings_fields']);
        // add_action('init', [$this, 'init']);

        if($this->is_active()) {
            $this->init();
            $this->admin_actions();
            $this->public_actions();
            if(did_action('wpml_st_loaded')){
                $this->wpml_actions();
            }
        }
    }

    /**
     * common init function for admin and frontend.
     */
    public function init(){
        // shouldn't do is_active check.
        if(method_exists($this, 'save_post')){
            add_filter("nx_save_post_{$this->id}", array($this, 'save_post'), 10, 3);
        }
        if(method_exists($this, 'saved_post')){
            add_filter("nx_saved_post_{$this->id}", array($this, 'saved_post'), 10, 3);
        }
    }

    /**
     * common init function for admin and frontend.
     */
    public function init_settings_fields(){

    }

    /**
     * common init function for admin and frontend.
     */
    public function init_fields(){
        add_filter('nx_themes', [$this, 'nx_themes']);
        add_filter('nx_sources', [$this, 'nx_sources'], 10, 1);
        add_filter('nx_link_types_dependency', [$this, 'link_types_dependency']);
        add_filter('nx_notification_template', [$this, 'notification_template']);
        add_filter('nx_notification_template_dependency', [$this, 'notification_template_dependency']);
        add_filter('nx_source_trigger', [$this, 'source_trigger']);
        add_filter('nx_themes_trigger', [$this, 'themes_trigger']);
        add_filter('nx_is_pro_sources', [$this, 'is_pro_sources']);

        if(method_exists($this, 'doc')){
            add_filter('nx_instructions', [$this, 'nx_instructions']);
        }
        if(method_exists($this, 'source_error_message')){
            add_filter('source_error_message', [$this, 'source_error_message']);
        }
    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function public_actions() {
        if (method_exists($this, 'fallback_data')) {
            add_filter("nx_fallback_data_{$this->id}", array($this, 'fallback_data'), 11, 3);
        }
        if (method_exists($this, 'notification_image')) {
            add_filter("nx_notification_image_{$this->id}", array($this, 'notification_image'), 10, 3);
        }

    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function admin_actions() {
        // add_action('nx_get_conversions_ready', array($this, 'get_notification_ready'), 10, 2);
    }

    public function wpml_actions(){

    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function source_trigger($triggers){
        if(empty($this->default_theme)){
            $type = TypeFactory::get_instance()->get($this->types);
            $this->default_theme = $type->default_theme;
        }
        if(!empty($this->default_theme)){
            $triggers[$this->id]['themes']   = "@themes:{$this->default_theme}";
        }
        $triggers[$this->id]['position'] = "@position:bottom_left";
        return $triggers;
    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function nx_themes($themes) {
        $_themes = $this->get_themes();

        $i = 0;
        if(is_array($_themes)){
            foreach ($_themes as $tname => $theme) {
                if (empty($themes[$tname])) {
                    $themes[$tname] = [
                        'label'   => $tname,
                        'value'   => $tname,
                        'is_pro'  => isset($theme['is_pro']) ? $theme['is_pro'] && ! NotificationX::is_pro() : null,
                        'icon'    => isset($theme['source']) ? $theme['source'] : $theme,
                        // @todo converts
                        // 'trigger' => isset($theme['template']) ? ['notification-template' => $theme['template']] : null,
                    ];
                    if(!empty($theme['column'])){
                        $themes[$tname]['column'] = $theme['column'];
                    }
                }

                $themes[$tname] = Rules::includes('source', $this->id, false, $themes[$tname]);
                // $themes[$tname] = Rules::includes('type', $this->types, false, $themes[$tname]);
                // $themes[$tname] = Rules::includes('i', [++$i], false, $themes[$tname]);
                // $themes[$tname] = Rules::includes('i', [++$i], false, $themes[$tname]);
                // $themes[$tname] = Rules::includes('i', [++$i], false, $themes[$tname]);
            }
        }
        return $themes;
    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function themes_trigger($triggers) {
        $_themes = $this->get_themes();
        if (is_array($_themes)) {
            foreach ($_themes as $tname => $theme) {
                if(!empty($theme['template']) && $templates = $theme['template']){
                    foreach ($templates as $key => $value) {
                        $t = "@notification-template.{$key}:{$value}";
                        if(empty($triggers[$tname]) || !in_array($t, $triggers[$tname])){
                            $triggers[$tname][] = $t;
                        }
                    }
                }
                if(!empty($theme['image_shape'])){
                    $t = "@image_shape:{$theme['image_shape']}";
                    if(empty($triggers[$tname]) || !in_array($t, $triggers[$tname])){
                        $triggers[$tname][] = $t;
                    }
                    // default image shape for theme.
                    $t = "@image_shape_default:{$theme['image_shape']}";
                    if(empty($triggers[$tname]) || !in_array($t, $triggers[$tname])){
                        $triggers[$tname][] = $t;
                    }
                }
                if(!empty($theme['show_notification_image'])){
                    $t = "@show_notification_image:{$theme['show_notification_image']}";
                    if(empty($triggers[$tname]) || !in_array($t, $triggers[$tname]))
                        $triggers[$tname][] = $t;
                }
            }
        }
        return $triggers;
    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function nx_sources($sources) {
        $sources[] = [
            'rules'    => ['is', 'type', $this->types],
            'label'    => $this->title,
            'icon'     => $this->img,
            'value'    => $this->id,
            'is_pro'   => $this->is_pro && ! NotificationX::is_pro(),
            'priority' => $this->priority,
        ];
        return $sources;
    }

    /**
     * Adds dependency if a option was added to link type field from Types.
     *
     * @param array $dependency
     * @return array
     */
    public function link_types_dependency($dependency) {
        if ($this->has_link_types){
            $dependency[] = $this->id;
        } else {
            $type = $this->get_type();
            if(!empty($type->has_link_types) && $type->has_link_types){
                $dependency[] = $this->id;
            }
        }
        return $dependency;
    }

    /**
     * Adds options and dependency for `Notification Template` field from `Content` tab.
     *
     * @param array $templates `Notification Template` fields.
     * @return array
     */
    public function notification_template($templates){
        foreach ($this->get_templates() as $key => $tmpl) {
            if(!empty($tmpl['_themes'])){
                $type = 'themes';
                $themes = $tmpl['_themes'];
                unset($tmpl['_themes']);
            }
            elseif(!empty($tmpl['_source'])) {
                $type = 'source';
                $themes = $tmpl['_source'];
                unset($tmpl['_source']);
            }

            foreach ($tmpl as $param => $options) {
                foreach ($options as $name => $label) {
                    if(empty($templates[$param]['options'][$name])){
                        if(is_array($label)){
                            $templates[$param]['options'][$name] = $label;
                        }
                        else{
                            $templates[$param]['options'][$name] = [
                                'value'     => $name,
                                'label'     => $label,
                                'rules'     => [],
                            ];
                        }
                    }
                    $templates[$param]['options'][$name] = Rules::includes($type, $themes, false, $templates[$param]['options'][$name]);
                }
            }
        }

        if(is_array($this->get_themes())){
            foreach ($this->get_themes() as $name => $theme) {
                if(!empty($theme['template'])){
                    foreach ($theme['template'] as $param => $value) {
                        if(strpos($param, 'custom_') === 0 || empty($templates[$param])) continue;
                        $templates[$param] = Rules::includes('themes', [$name], false, $templates[$param]);
                    }
                }
            }
        }

        return $templates;
    }

    /**
     * Adds options and dependency for `Notification Template` field from `Content` tab.
     *
     * @param array $templates `Notification Template` fields.
     * @return array
     */
    public function notification_template_dependency($dependency){
        if($this->get_templates()){
            $dependency[] = $this->id;
        }
        return $dependency;
    }

    public function get_type(){
        return TypeFactory::get_instance()->get($this->types);
    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function get_themes() {
        if (empty($this->themes)) {
            $type_obj = $this->get_type();
            if ($type_obj instanceof Types) {
                $themes = $type_obj->get_themes();
                return $this->array_add_prefix($themes, $this->types . "_");
            }
        }
        return $this->array_add_prefix($this->themes, $this->id . "_");
    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function get_themes_name() {
        return array_keys($this->get_themes());
    }

    public function array_add_prefix($array, $prefix){
        return ! is_array( $array ) ? $array : array_combine(
            array_map( function( $key ) use( $prefix ) {
                return $prefix . $key;
            }, array_keys( $array ) ),
            $array
        );
    }

    /**
     * Get templates for the extension.
     *
     *
     * @return array
     */
    public function get_templates(){
        if(empty($this->templates)){
            $type_obj = $this->get_type();
            if ($type_obj instanceof Types) {
                return $type_obj->get_templates();
            }
        }
        return $this->templates;
    }

    /**
     * Undocumented function
     *
     * @param [array] $modules
     * @return array
     */
    public function register_module() {
        $modules = Modules::get_instance();
        return $modules->add(array(
            'value'    => $this->module,
            'label'    => $this->module_title,
            'link'     => $this->doc_link,
            'is_pro'   => $this->is_pro && ! NotificationX::is_pro(),
            'badge'    => $this->is_pro,
            'priority' => $this->module_priority,
        ));
    }

    public function delete_notification($entry_key = null, $nx_id = null) {
        $where = [
            // 'source' => $this->id
        ];
        if (!empty($entry_key)) {
            $where['entry_key'] = $entry_key;
        }
        if (!empty($nx_id)) {
            $where['nx_id'] = $nx_id;
        }
        if (!empty($where)) {
            return Entries::get_instance()->delete_entries($where);
        }
        return false;
    }

    // @todo accept multiple entries.
    public function update_notifications($entries) {
        if(is_array($entries) && !empty($entries[0]['nx_id'])){
            $post = PostType::get_instance()->get_post($entries[0]['nx_id']);
            foreach ($entries as $key => $entry) {
                $can_entry = apply_filters("nx_can_entry_{$this->id}", true, $entry, $post);
                if(!$can_entry){
                    unset($entries[$key]);
                }
            }
            Limiter::get_instance()->remove($post['nx_id'], count($entries));
            Entries::get_instance()->insert_entries(array_values($entries));
        }
    }

    // @todo Something
    public function update_notification($entry, $force = true) { // , $nx_id = 0
        if (empty($entry['nx_id'])) { // empty($nx_id) &&
            $this->save($entry, $force);
        } else {
            if(!$force){
                $is_exits = Database::get_instance()->get_posts(
                    Database::$table_entries, 'count(*)', [
                    'nx_id'     => $entry['nx_id'],
                    'source'    => $this->id,
                    'entry_key' => $entry['entry_key'],
                ] );
                if(!empty($is_exits[0]['count(*)'])) return false;
            }
            // @todo add object caching
            $post = PostType::get_instance()->get_post($entry['nx_id']);
            $can_entry = apply_filters("nx_can_entry_{$this->id}", true, $entry, $post);
            if($can_entry){
                Limiter::get_instance()->remove($post['nx_id'], 1);
                Entries::get_instance()->insert_entry($entry);
            }
        }
    }

    /**
     * This method is responsible for save the data
     *
     * @param string $this->id - notification type
     * @param array $data - notification data to save.
     * @return boolean
     */
    protected function save($entry, $force = true) {
        $posts = PostType::get_instance()->get_posts([
            'source' => $this->id,
            'enabled' => true,
        ]);
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $entry['nx_id'] = $post['nx_id'];
                $this->update_notification($entry, $force);
            }
        }
    }

    /**
     * this function is responsible for check a type of notification is created or not
     *
     * @param string $type
     * @return boolean
     */
    public function is_active($check_enabled = true) {
        if (!empty($this->class) && !class_exists($this->class)) {
            return false;
        }
        if (!empty($this->function) && !function_exists($this->function)) {
            return false;
        }
        if ($check_enabled) {
            $active_sources = PostType::get_instance()->get_active_items();
            if (!empty($active_sources)) {
                return in_array($this->id, $active_sources);
            }
            return false;
        }
        return true;
    }

    public function class_exists() {
        if (!empty($this->class)) {
            return class_exists($this->class);
        }
        if (!empty($this->function)) {
            return function_exists($this->function);
        }
        return true;
    }

    /**
     * This method is responsible for save the data
     *
     * @param string $this->id - notification type
     * @param array $data - notification data to save.
     * @return boolean
     */
    protected function notEmpty($key, $array, $default = null) {
        if(!empty($array[$key])){
            return $array[$key];
        }
        return $default;
    }

    /**
     * Generating Full Name with one letter from last name
     * @since 1.3.9
     * @param string $first_name
     * @param string $last_name
     * @return string
     */
    protected function name($first_name = '', $last_name = '') {
        $name = Helper::name($first_name, $last_name);
        return $name;
    }

    public function remote_get($url, $args = array(), $raw = false) {
        return Helper::remote_get($url, $args, $raw);
    }

    /**
     * This method is responsible for save the data
     *
     * @param string $type - notification type
     * @param array $data - notification data to save.
     * @return boolean
     */
    protected function sort_data($arr) {
        // @todo add sorting.
        return $arr;
    }

    public function restResponse( $params ){
        error_log('FROM Extenson');
        error_log( $params );
    }

    public function nx_instructions($instructions){
        if(method_exists($this, 'doc')){
            $instructions[$this->types][$this->id] = $this->doc();
        }
        return $instructions;
    }

    public function is_pro_sources($sources){
        $sources[$this->id] = $this->is_pro;
        return $sources;
    }

}
