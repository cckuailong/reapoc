<?php

class RM_Chronos {    
    
    const TASKS_TABLE = 'rm_tasks';
    const RULES_TABLE = 'rm_rules';
    const TASK_EXE_LOG = 'rm_task_exe_log';
    const CRON_INTERVAL = 3600;
    protected static $base_dir, $base_url;
    
    public static function init() {
        self::$base_dir = plugin_dir_path(plugin_dir_path(__FILE__)); 
        self::$base_url = plugin_dir_url(plugin_dir_path(__FILE__));    
        self::attach_hooks();
        //schedule cron
        if ( ! wp_next_scheduled( 'rm_chronos_task_exe_hook' ) ) {
            wp_schedule_event( time(), 'rm_automation_interval', 'rm_chronos_task_exe_hook' );
        }
    }
    
    public static function get_base_dir() {
        return self::$base_dir;    
    }
    
    public static function get_base_url() {
        return self::$base_url;    
    }
    
    public static function activate() {
        self::create_tables();
    }
    
    public static function deactivate() {
        //remove cron job here
        //die("death is smiling at upon us all.");
    }
    
    public static function attach_hooks() {
        add_action("rm_admin_menu_after_field_stats", "RM_Chronos::add_admin_pages");
        add_action("rm_per_site_tables_created", "RM_Chronos::create_tables");
        add_action("rm_migration_finished", "RM_Chronos::db_migrate");
        add_action("registrationmagic_deactivated", "RM_Chronos::deactivate");
        add_filter( 'cron_schedules', 'RM_Chronos::add_cron_interval' );
        add_action( 'rm_chronos_task_exe_hook', 'RM_Chronos::run_tasks' );
        add_action( 'wp_ajax_rm_chronos_ajax', 'RM_Chronos::handle_ajax_req' );
        add_action("rm_form_settings_dashboard_action_icon", "RM_Chronos::add_rm_form_setting_menu");
        add_action('media_buttons', 'RM_Chronos::add_field_dropdown_to_editor');
        add_action("rm_formcard_menu_action_icon", "RM_Chronos::add_rm_formcard_menu", 10, 2);
    }
    
    public static function add_cron_interval($schedules) {
        $schedules['rm_automation_interval'] = array(
            'interval' => self::CRON_INTERVAL,
            'display'  => __("RM Automation Interval",'custom-registration-form-builder-with-submission-manager'),
        );
 
        return $schedules;
    }
    
    public static function add_admin_pages()
    {
        $menu_title = "Automation";     
        $rm_automation_intro_time = get_site_option('rm_option_automation_intro_time', null);
        $thirty_days = 30*24*3600;
        
        if(!$rm_automation_intro_time) {
            $rm_automation_intro_time = time();
            update_site_option('rm_option_automation_intro_time', $rm_automation_intro_time);
        }
        
        if((time() - (int)$rm_automation_intro_time) <= $thirty_days )
            $menu_title .= "";
        add_submenu_page("rm_form_manage",__('Automation','custom-registration-form-builder-with-submission-manager'), $menu_title, "manage_options", "rm_ex_chronos_manage_tasks",  "RM_Chronos::handle_request" );
        add_submenu_page("","RM Chronos 3", __('RM Chronos 3','custom-registration-form-builder-with-submission-manager'), "manage_options", "rm_ex_chronos_edit_task",  "RM_Chronos::handle_request" );
    }
    
    public static function db_migrate() {
        //self::create_tables();
        $existing_db_version = get_site_option("rm_option_ex_chronos_db_version", false);
        
        if(!$existing_db_version) {
            update_site_option("rm_option_ex_chronos_db_version", RM_CHRONOS_DB_VERSION);
            return "chronos_fresh_install";
        }
        
        if($existing_db_version && floatval($existing_db_version) >= RM_CHRONOS_DB_VERSION) {
            return "chronos_better_or_equal";
        }
        
        //perform migration
        update_site_option("rm_option_ex_chronos_db_version", RM_CHRONOS_DB_VERSION);
    }
    
    public static function get_table_name_for($identifier) {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table_name = null;
        switch($identifier) {
            case 'TASKS':            
                $table_name = $prefix.self::TASKS_TABLE;
                break;
            case 'RULES':
                $table_name = $prefix.self::RULES_TABLE;
                break;
            case 'TASK_EXE_LOG':
                $table_name = $prefix.self::TASK_EXE_LOG;
                break;
        }
        return $table_name;
    }
    
    public static function create_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TASKS_TABLE;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `task_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `form_id` INT(6),
                    `name` VARCHAR(255),
                    `desc` VARCHAR(1000),
                    `must_rules` TEXT,
                    `any_rules` TEXT,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `actions` TEXT,
                    `task_order` INT(6),
                    `meta` TEXT
                    )$charset_collate;";

            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix.self::RULES_TABLE;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `rule_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `type` INT(6),
                    `attr_name` VARCHAR(255),
                    `attr_value` VARCHAR(1000),
                    `operator` VARCHAR(20),
                    `meta` TEXT
                    )$charset_collate;";

            dbDelta($sql);
        }
        
        $table_name = $wpdb->prefix.self::TASK_EXE_LOG;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            //Ensures proper charset support. Also limits support for WP v3.5+.
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                    `texe_log_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `task_id` INT(6),
                    `action` INT(6),
                    `sub_ids` LONGTEXT,
                    `user_ids` LONGTEXT,
                    `meta` TEXT
                    )$charset_collate;";

            dbDelta($sql);
        }
    }    
    //$state = null => all tasks
    // active => only active/enabled tasks
    // inactive => only inactive/disabled tasks
    // if form_id is null, tasks for all forms are returned
    public static function get_tasks($form_id = null, $state = null) {
        global $wpdb;        
        $tasks_table = self::get_table_name_for("TASKS");
        
        $query = "SELECT * FROM {$tasks_table} WHERE ";
        
        $where_clause = array();
        
        switch($state) {
            case 'active':
                $where_clause[] = "`is_active` = 1";
                break;
            case 'inactive':
                $where_clause[] = "`is_active` = 0";
                break;
        }
        
        if($form_id)
            $where_clause[] = "`form_id` = {$form_id}";
        
        $where_clause = implode(" AND ", $where_clause);
        if(!$where_clause)
            $where_clause = "1";
        
        $res = $wpdb->get_results("SELECT * FROM {$tasks_table} WHERE {$where_clause} ORDER BY `form_id`, `task_order`, `task_id`");
        
        if(!$res || !is_array($res))
            $res = array();
        
        return $res;
    }
    
    public static function handle_request() {        
        $req = stripslashes_deep($_REQUEST);
        
        if(!isset($req['page']))
            return;
        
        $task_controller = new RM_Chronos_Task_controller;
        $x = isset($req['rm_cron_slug']) ? $req['rm_cron_slug'] : $req['page'];
        $action = substr($x, strlen('rm_ex_chronos_'));
        if(is_callable(array($task_controller, $action)))
            $task_controller->$action($req);
    }
    
    public static function run_tasks() {        
        $tasks = self::get_tasks(null, 'active');    
        $task_factory = new RM_Chronos_Task_Factory();
        foreach($tasks as $task) {            
            $task = $task_factory->create_task($task->task_id);
            if($task instanceof RM_Chronos_Task)
                $task->execute();
        }
    }
    
    protected static function run_single_task_ajax() {    
        if(isset($_POST['task_id'])) {
            $task_id = $_POST['task_id'];
            $task_factory = new RM_Chronos_Task_Factory();
            $task = $task_factory->create_task($task_id);
            if($task && $task instanceof RM_Chronos_Task) {
                $task->execute();
                return json_encode(array("result"=>"done","type"=>""));
            } else {
                return json_encode(array("result"=>"error","type"=>"invalid_task_id"));
            }
        } else {
            return json_encode(array("result"=>"error","type"=>"invalid_task_id"));
        }       
    }
    
    protected static function delete_tasks_batch() {    
        if(isset($_POST['task_ids'])) {
            $task_ids = $_POST['task_ids'];
            if(is_array($task_ids)) {
                $service = new RM_Chronos_Service();
                $service->remove_tasks_batch($task_ids);
            }
        }    
    }
    
    protected static function duplicate_tasks_batch() {    
        if(isset($_POST['task_ids'])) {
            $task_ids = $_POST['task_ids'];
            if(is_array($task_ids)) {
                $service = new RM_Chronos_Service();
                $service->duplicate_tasks_batch($task_ids);
            }
        }    
    }
    
    protected static function set_state_tasks_batch() {    
        if(isset($_POST['task_ids'], $_POST['state'])) {
            $task_ids = $_POST['task_ids'];
            if(is_array($task_ids)) {
                $service = new RM_Chronos_Service();
                $service->set_state_tasks_batch($task_ids, $_POST['state']);
            }
        }    
    }
    
    protected static function update_task_order() {    
        if(isset($_POST['ordered_task_ids'])) {
            $ordered_task_ids = $_POST['ordered_task_ids'];
            if(is_array($ordered_task_ids)) {
                $service = new RM_Chronos_Service();
                $service->update_task_order($ordered_task_ids);
            }
        }    
    }
    
    public static function handle_ajax_req() {
        if(current_user_can('manage_options') && isset($_POST['rm_chronos_ajax_action'])) {
            
            switch($_POST['rm_chronos_ajax_action']) {
                case 'trigger_task':
                    echo self::run_single_task_ajax();
                    break;
                
                case 'delete_tasks_batch':
                    echo self::delete_tasks_batch();
                    break;
                
                case 'duplicate_tasks_batch':
                    echo self::duplicate_tasks_batch();
                    break;
                
                case 'set_state_tasks_batch':
                    echo self::set_state_tasks_batch();
                    break;
                
                case 'update_task_order':
                    echo self::update_task_order();
                    break;
            }
        }
        wp_die();
    }
    
    public static function add_rm_form_setting_menu($form_id) {
        $rm_task_manager_url = admin_url("admin.php?page=rm_ex_chronos_manage_tasks&rm_form_id={$form_id}");
        ?>
            <div class="rm-grid-icon difl" id="rm-automate-icon">
                <a href="<?php echo $rm_task_manager_url; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_Chronos::get_base_url(); ?>images/automation.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_Chronos_UI_Strings::get('LABEL_TASKS'); ?></div>
                </a>
            </div> 
        <?php
    }
    
    public static function add_rm_formcard_menu($form_id, $rdrto="") {
        $rm_task_manager_url = admin_url("admin.php?page=rm_ex_chronos_manage_tasks&rm_form_id={$form_id}&rdrto={$rdrto}");
        ?>            
            <div class="rm-formcard-tab-item">
                <a href="<?php echo $rm_task_manager_url; ?>" class="rm_fd_link">   
                    <img class="rm-formcard-icon" src="<?php echo RM_Chronos::get_base_url(); ?>images/automation.png">
                    <div class="rm-formcard-label"><?php echo RM_Chronos_UI_Strings::get('LABEL_TASKS'); ?></div>
                </a>
            </div>
        <?php
    }
    
    public static function add_field_dropdown_to_editor($editor_id) {
        
        if(!is_admin() || !function_exists('get_current_screen'))
            return;
        
        $screen = get_current_screen();
        if(!$screen || $screen->id != 'admin_page_rm_ex_chronos_edit_task')
            return;
        
        $service = new RM_Chronos_Service;
        $tmp = explode("_", $editor_id);
        $form_id = end($tmp);
        $fields = $service->get_all_form_fields($form_id);        
        ?>
        <select id="rmc_field_selector_task_action_send_mail">
            <option value=""><?php _e("Select a Field",'custom-registration-form-builder-with-submission-manager') ?></option>
            <?php foreach($fields as $field): 
                    echo "<option value='{$field->field_type}_{$field->field_id}'>{$field->field_label}</option>";
                  endforeach; ?>
        </select>
        <?php
    }
}
