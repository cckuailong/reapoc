<?php

include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-restore-db-pdo-mysql-method.php';
include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-restore-db-wpdb-method.php';

class WPvivid_Restore_DB_Method
{
    private $db;
    private $type;

    public function __construct()
    {
        global $wpvivid_plugin;
        $common_setting = WPvivid_Setting::get_setting(false, 'wpvivid_common_setting');
        $db_connect_method = isset($common_setting['options']['wpvivid_common_setting']['db_connect_method']) ? $common_setting['options']['wpvivid_common_setting']['db_connect_method'] : 'wpdb';
        if($db_connect_method === 'wpdb'){
            $wpvivid_plugin->restore_data->write_log('wpdb', 'Warning');
            $this->db =new WPvivid_Restore_DB_WPDB_Method();
            $this->type='wpdb';
        }
        else{
            $wpvivid_plugin->restore_data->write_log('pdo_mysql', 'Warning');
            $this->db =new WPvivid_Restore_DB_PDO_Mysql_Method();
            $this->type='pdo_mysql';
        }
    }

    public function get_type()
    {
        return $this->type;
    }

    public function connect_db()
    {
        return $this->db->connect_db();
    }

    public function test_db()
    {
        return $this->db->test_db();
    }

    public function check_max_allow_packet()
    {
        $this->db->check_max_allow_packet();
    }

    public function get_max_allow_packet()
    {
        return $this->db->get_max_allow_packet();
    }

    public function init_sql_mode()
    {
        $this->db->init_sql_mode();
    }

    public function set_skip_query($count)
    {
        $this->db->set_skip_query($count);
    }

    public function execute_sql($query)
    {
        $this->db->execute_sql($query);
    }

    public function query($sql,$output=ARRAY_A)
    {
        return $this->db->query($sql,$output);
    }

    public function errorInfo()
    {
        return $this->db->errorInfo();
    }
}