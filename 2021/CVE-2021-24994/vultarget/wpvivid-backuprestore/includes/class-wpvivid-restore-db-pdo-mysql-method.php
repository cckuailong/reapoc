<?php

class WPvivid_Restore_DB_PDO_Mysql_Method
{
    private $db;
    private $max_allow_packet;
    private $skip_query=0;

    public function connect_db()
    {
        try
        {
            $res = explode(':',DB_HOST);
            $db_host = $res[0];
            $db_port = empty($res[1])?'':$res[1];
            if(!empty($db_port)) {
                $dsn='mysql:host=' . $db_host . ';port=' . $db_port . ';dbname=' . DB_NAME;
            }
            else{
                $dsn='mysql:host=' . $db_host . ';dbname=' . DB_NAME;
            }
            $this->db = null;
            $this->db=new PDO($dsn, DB_USER, DB_PASSWORD);
            $this->db->exec('SET NAMES utf8');
            if(empty($this->db) || !$this->db)
            {
                if(class_exists('PDO'))
                {
                    $extensions=get_loaded_extensions();
                    if(array_search('pdo_mysql',$extensions))
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The error establishing a database connection. Please check wp-config.php file and make sure the information is correct.';
                    }
                    else{
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.';
                    }
                }
                else{
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']='The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_SUCCESS;
            }
        }
        catch (Exception $e)
        {
            if(empty($this->db) || !$this->db)
            {
                if(class_exists('PDO'))
                {
                    $extensions=get_loaded_extensions();
                    if(array_search('pdo_mysql',$extensions))
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The error establishing a database connection. Please check wp-config.php file and make sure the information is correct.';
                    }
                    else{
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.';
                    }
                }
                else{
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']='The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']=$e->getMessage();
            }
        }
        return $ret;
    }

    public function test_db()
    {
        global $wpvivid_plugin;
        $test_table_new=uniqid('wpvivid_test_tables_');
        $columns='(test_id int)';
        $test_table = $this->db->exec("CREATE TABLE IF NOT EXISTS $test_table_new $columns");

        if ($test_table!==false)
        {
            $wpvivid_plugin->restore_data->write_log('The test to create table succeeds.','notice');
            $test_table = $this->db->exec("INSERT INTO $test_table_new (`test_id`) VALUES ('123')");
            if($test_table!==false)
            {
                $wpvivid_plugin->restore_data->write_log('The test to insert into table succeeds.','notice');
                $test_table = $this->db->exec("DROP TABLE IF EXISTS $test_table_new");
                if($test_table!==false)
                {
                    $wpvivid_plugin->restore_data->write_log('The test to drop table succeeds.','notice');
                    return true;
                }
                else
                {
                    $error=$this->db->errorInfo();
                    $wpvivid_plugin->restore_data->write_log('Unable to drop table. The reason is '.$error[2],'warning');
                    return false;
                }
            }
            else
            {
                $error=$this->db->errorInfo();
                $wpvivid_plugin->restore_data->write_log('Unable to insert into table. The reason is '.$error[2],'warning');
                return false;
            }
        }
        else {
            $error=$this->db->errorInfo();
            $wpvivid_plugin->restore_data->write_log('Unable to create table. The reason is '.$error[2],'warning');
            return false;
        }
    }

    public function check_max_allow_packet()
    {
        global $wpvivid_plugin;

        $max_allowed_packet = $this->db->query("SELECT @@session.max_allowed_packet;");
        if($max_allowed_packet)
        {
            $max_allowed_packet = $max_allowed_packet -> fetchAll();

            if(is_array($max_allowed_packet)&&isset($max_allowed_packet[0])&&isset($max_allowed_packet[0][0]))
            {
                if($max_allowed_packet[0][0]<1048576)
                {
                    $wpvivid_plugin->restore_data->write_log('warning: max_allowed_packet less than 1M :'.size_format($max_allowed_packet[0][0],2),'notice');
                }
                else if($max_allowed_packet[0][0]<33554432)
                {
                    $wpvivid_plugin->restore_data->write_log('max_allowed_packet less than 32M :'.size_format($max_allowed_packet[0][0],2),'notice');
                }
                $this->max_allow_packet=$max_allowed_packet[0][0];
                $wpvivid_plugin->restore_data->write_log( $this->max_allow_packet,'notice');
            }
            else
            {
                $wpvivid_plugin->restore_data->write_log('get max_allowed_packet failed','notice');
                $this->max_allow_packet=1048576;
            }
        }
        else
        {
            $wpvivid_plugin->restore_data->write_log('get max_allowed_packet failed','notice');
            $this->max_allow_packet=1048576;
        }
    }

    public function get_max_allow_packet()
    {
        return $this->max_allow_packet;
    }

    public function init_sql_mode()
    {
        $res = $this->db->query('SELECT @@SESSION.sql_mode') -> fetchAll();
        $sql_mod = $res[0][0];
        $temp_sql_mode = str_replace('NO_ENGINE_SUBSTITUTION','',$sql_mod);
        $temp_sql_mode = 'NO_AUTO_VALUE_ON_ZERO,'.$temp_sql_mode;
        $this->db->query('SET SESSION sql_mode = "'.$temp_sql_mode.'"');
    }

    public function set_skip_query($count)
    {
        $this->skip_query=$count;
    }

    public function execute_sql($query)
    {
        global $wpvivid_plugin;

        if($this->skip_query>10)
        {
            if(strlen($query)>$this->max_allow_packet)
            {
                $wpvivid_plugin->restore_data->write_log('skip query size:'.size_format(strlen($query)), 'Warning');
                return ;
            }
        }

        if ($this->db->exec($query)===false)
        {
            $info=$this->db->errorInfo();
            $wpvivid_plugin->restore_data->write_log($query.' query: [' . implode('][', $info) . ']', 'Warning');

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
        }
    }

    public function query($sql,$output)
    {
        $ret=$this->db->query($sql);
        if($ret===false)
        {
            return $ret;
        }
        else
        {
            return $ret -> fetchAll();
        }
    }

    public function errorInfo()
    {
        return $this->db->errorInfo();
    }
}