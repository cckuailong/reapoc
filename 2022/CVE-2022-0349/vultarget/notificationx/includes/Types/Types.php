<?php

/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;

use NotificationX\Modules;
use NotificationX\Core\Themes;
use NotificationX\Extensions\GlobalFields;

/**
 * Extension Abstract for all Extension.
 */
abstract class Types {

    public $id;
    public $title;
    public $is_pro = false;
    public $themes = [];
    public $module = [];
    public $templates = [];
    public $has_link_types = false;
    public $default_source    = '';
    public $default_theme = '';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('nx_before_metabox_load', [$this, 'init_fields']);
        add_filter('nx_type_trigger', [$this, 'type_trigger']);
    }

    /**
     * admin int hook.
     *
     *
     * @return void
     */
    public function init(){

    }

    /**
     * admin int hook.
     *
     *
     * @return void
     */
    public function init_fields(){

    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function type_trigger($triggers){
        $triggers[$this->id] = "@source:{$this->default_source}";
        return $triggers;
    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function get_themes(){
        return $this->themes;
    }

    /**
     * Get templates for the extension.
     *
     *
     * @return array
     */
    public function get_templates(){
        return $this->templates;
    }
}
