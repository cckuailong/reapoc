<?php

class WPvivid_Restore_DB_WPDB_Method
{
    private $max_allow_packet;
    private $skip_query=0;

    public function connect_db()
    {
        global $wpdb;
        $wpdb->get_results('SET NAMES utf8', ARRAY_A);
        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function test_db()
    {
        global $wpvivid_plugin;
        global $wpdb;

        $test_table_new=uniqid('wpvivid_test_tables_');
        $columns='(test_id int)';
        $test_table = $wpdb->get_results("CREATE TABLE IF NOT EXISTS $test_table_new $columns",ARRAY_A);

        if ($test_table!==false)
        {
            $wpvivid_plugin->restore_data->write_log('The test to create table succeeds.','notice');
            $test_table = $wpdb->get_results("INSERT INTO $test_table_new (`test_id`) VALUES ('123')",ARRAY_A);
            if($test_table!==false)
            {
                $wpvivid_plugin->restore_data->write_log('The test to insert into table succeeds.','notice');
                $test_table = $wpdb->get_results("DROP TABLE IF EXISTS $test_table_new",ARRAY_A);
                if($test_table!==false)
                {
                    $wpvivid_plugin->restore_data->write_log('The test to drop table succeeds.','notice');
                    return true;
                }
                else
                {
                    $error=$wpdb->last_error;
                    $wpvivid_plugin->restore_data->write_log('Unable to drop table. The reason is '.$error,'warning');
                    return false;
                }
            }
            else
            {
                $error=$wpdb->last_error;
                $wpvivid_plugin->restore_data->write_log('Unable to insert into table. The reason is '.$error,'warning');
                return false;
            }
        }
        else {
            $error=$wpdb->last_error;
            $wpvivid_plugin->restore_data->write_log('Unable to create table. The reason is '.$error,'warning');
            return false;
        }
    }

    public function check_max_allow_packet()
    {
        global $wpvivid_plugin;

        $wpvivid_plugin->restore_data->write_log('get max_allowed_packet wpdb ','notice');
        global $wpdb;
        $max_allowed_packet =$wpdb->get_var("SELECT @@session.max_allowed_packet");
        if($max_allowed_packet!==null)
        {
            if($max_allowed_packet<1048576)
            {
                $wpvivid_plugin->restore_data->write_log('warning: max_allowed_packet less than 1M :'.size_format($max_allowed_packet,2),'notice');
            }
            else if($max_allowed_packet<33554432)
            {
                $wpvivid_plugin->restore_data->write_log('max_allowed_packet less than 32M :'.size_format($max_allowed_packet,2),'notice');
            }
            $this->max_allow_packet=$max_allowed_packet;
        }
        else
        {
            $wpvivid_plugin->restore_data->write_log('get max_allowed_packet failed ','notice');
            $this->max_allow_packet=1048576;
        }
    }

    public function get_max_allow_packet()
    {
        return $this->max_allow_packet;
    }

    public function init_sql_mode()
    {
        global $wpdb;
        $res = $wpdb->get_var('SELECT @@SESSION.sql_mode');
        if($res===null)
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->restore_data->write_log('get sql_mode failed','notice');
        }
        else
        {
            $sql_mod = $res;
            $temp_sql_mode = str_replace('NO_ENGINE_SUBSTITUTION','',$sql_mod);
            $temp_sql_mode = 'ALLOW_INVALID_DATES,NO_AUTO_VALUE_ON_ZERO,'.$temp_sql_mode;
            $wpdb->get_results('SET SESSION sql_mode = "'.$temp_sql_mode.'"',ARRAY_A);
        }

    }

    public function set_skip_query($count)
    {
        $this->skip_query=$count;
    }

    public function execute_sql($query)
    {
        global $wpvivid_plugin;

        global $wpdb;
        if ($wpdb->get_results($query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->restore_data->write_log($error, 'Warning');

            /*
            if($info[1] == 2006)
            {
                if(strlen($query)>$this->max_allow_packet)
                {
                    $this->skip_query++;
                    $wpvivid_plugin->restore_data->write_log('max_allow_packet too small:'.size_format($this->max_allow_packet).' query size:'.size_format(strlen($query)), 'Warning');
                }
                $ret=$this->connect_db();
                if($ret['result']==WPVIVID_FAILED)
                {
                    $wpvivid_plugin->restore_data->write_log('reconnect failed', 'Warning');
                }
                else{
                    $wpvivid_plugin->restore_data->write_log('reconnect succeed', 'Warning');
                }
            }
            */
        }
    }

    public function query($sql,$output)
    {
        global $wpdb;
        return  $wpdb->get_results($sql,$output);
    }

    public function errorInfo()
    {
        global $wpdb;
        return $wpdb->last_error;
    }
}