<?php

class WPvivid_DB_Method
{
    public $db_handle;
    public $type;

    public function connect_db()
    {
        $common_setting = WPvivid_Setting::get_setting(false, 'wpvivid_common_setting');
        $db_connect_method = isset($common_setting['options']['wpvivid_common_setting']['db_connect_method']) ? $common_setting['options']['wpvivid_common_setting']['db_connect_method'] : 'wpdb';
        if($db_connect_method === 'wpdb'){
            global $wpdb;
            $this->db_handle=$wpdb;
            $this->type='wpdb';
            return array('result'=>WPVIVID_SUCCESS);
        }
        else{
            if(class_exists('PDO')) {
                $extensions=get_loaded_extensions();
                if(array_search('pdo_mysql',$extensions)) {
                    $res = explode(':',DB_HOST);
                    $db_host = $res[0];
                    $db_port = empty($res[1])?'':$res[1];

                    if(!empty($db_port)) {
                        $dsn='mysql:host=' . $db_host . ';port=' . $db_port . ';dbname=' . DB_NAME;
                    }
                    else{
                        $dsn='mysql:host=' . $db_host . ';dbname=' . DB_NAME;
                    }

                    $this->db_handle=new PDO($dsn, DB_USER, DB_PASSWORD);

                    $this->type='pdo_mysql';
                    return array('result'=>WPVIVID_SUCCESS);
                }
                else{
                    return array('result'=>WPVIVID_FAILED, 'error'=>'The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.');
                }
            }
            else{
                return array('result'=>WPVIVID_FAILED, 'error'=>'The pdo_mysql extension is not detected. Please install the extension first or choose wpdb option for Database connection method.');
            }
        }
    }

    public function check_db($fcgi)
    {
        $ret=$this->connect_db();

        if($ret['result']==WPVIVID_FAILED)
        {
            return $ret;
        }

        if($this->type=='pdo_mysql')
        {
            return $this->check_db_pdo($fcgi);
        }
        else if($this->type=='wpdb')
        {
            return $this->check_db_wpdb($fcgi);
        }

        return array('result' => WPVIVID_FAILED,'error' => 'db handle type not found.');
    }

    public function check_db_pdo($fcgi)
    {
        $ret['alert_db']=false;
        $ret['result']=WPVIVID_SUCCESS;
        $ret['big_tables']=array();
        $db_info=array();

        $sth = $this->db_handle->query('SHOW TABLE STATUS');
        $dbSize = 0;
        $sum_rows=0;
        $rows = $sth->fetchAll();
        foreach ($rows as $row)
        {
            global $wpdb;
            if (is_multisite() && !defined('MULTISITE'))
            {
                $prefix = $wpdb->base_prefix;
            } else {
                $prefix = $wpdb->get_blog_prefix(0);
            }
            if(preg_match('/^(?!'.$prefix.')/', $row["Name"]) == 1){
                continue;
            }

            $db_info[$row["Name"]]["Rows"]=$row["Rows"];
            $db_info[$row["Name"]]["Data_length"]=size_format($row["Data_length"]+$row["Index_length"],2);
            if($row["Rows"]>1000000)
            {
                $ret['big_tables'][$row["Name"]]['Rows']=$row["Rows"];
                $ret['big_tables'][$row["Name"]]['Data_length']=size_format($row["Data_length"]+$row["Index_length"],2);
            }

            $sum_rows+=$row["Rows"];
            $dbSize+=$row["Data_length"]+$row["Index_length"];
        }
        if($fcgi)
        {
            $alter_sum_rows=4000000;
        }
        else
        {
            $alter_sum_rows=4000000*3;
        }

        $memory_limit = ini_get('memory_limit');
        $ret['memory_limit']=$memory_limit;
        $memory_limit = trim($memory_limit);
        $memory_limit_int = (int) $memory_limit;
        $last = strtolower(substr($memory_limit, -1));

        if($last == 'g')
            $memory_limit_int = $memory_limit_int*1024*1024*1024;
        if($last == 'm')
            $memory_limit_int = $memory_limit_int*1024*1024;
        if($last == 'k')
            $memory_limit_int = $memory_limit_int*1024;

        if($dbSize>($memory_limit_int*0.9))
        {
            $max_rows=0;
        }
        else
        {
            $max_rows=(($memory_limit_int*0.9)-$dbSize)/49;
        }

        $max_rows=max($max_rows,1048576);

        if($sum_rows>$alter_sum_rows||$sum_rows>$max_rows)
        {
            //big db alert
            $ret['alert_db']=true;
            $ret['sum_rows']=$sum_rows;
            $ret['db_size']=size_format($dbSize,2);
            if($fcgi)
                $ret['alter_fcgi']=true;
        }

        $ret['db_size']=size_format($dbSize,2);
        return $ret;
    }

    public function check_db_wpdb($fcgi)
    {
        $ret['alert_db']=false;
        $ret['result']=WPVIVID_SUCCESS;
        $ret['big_tables']=array();
        $db_info=array();

        global $wpdb;
        $result=$wpdb->get_results('SHOW TABLE STATUS',ARRAY_A);

        //$sth = $this->db_handle->query('SHOW TABLE STATUS');
        $dbSize = 0;
        $sum_rows=0;
        //$rows = $sth->fetchAll();
        foreach ($result as $row)
        {
            global $wpdb;
            if (is_multisite() && !defined('MULTISITE'))
            {
                $prefix = $wpdb->base_prefix;
            } else {
                $prefix = $wpdb->get_blog_prefix(0);
            }
            if(preg_match('/^(?!'.$prefix.')/', $row["Name"]) == 1){
                continue;
            }

            $db_info[$row["Name"]]["Rows"]=$row["Rows"];
            $db_info[$row["Name"]]["Data_length"]=size_format($row["Data_length"]+$row["Index_length"],2);
            if($row["Rows"]>1000000)
            {
                $ret['big_tables'][$row["Name"]]['Rows']=$row["Rows"];
                $ret['big_tables'][$row["Name"]]['Data_length']=size_format($row["Data_length"]+$row["Index_length"],2);
            }

            $sum_rows+=$row["Rows"];
            $dbSize+=$row["Data_length"]+$row["Index_length"];
        }
        if($fcgi)
        {
            $alter_sum_rows=4000000;
        }
        else
        {
            $alter_sum_rows=4000000*3;
        }

        $memory_limit = ini_get('memory_limit');
        $ret['memory_limit']=$memory_limit;
        $memory_limit = trim($memory_limit);
        $memory_limit_int = (int) $memory_limit;
        $last = strtolower(substr($memory_limit, -1));

        if($last == 'g')
            $memory_limit_int = $memory_limit_int*1024*1024*1024;
        if($last == 'm')
            $memory_limit_int = $memory_limit_int*1024*1024;
        if($last == 'k')
            $memory_limit_int = $memory_limit_int*1024;

        if($dbSize>($memory_limit_int*0.9))
        {
            $max_rows=0;
        }
        else
        {
            $max_rows=(($memory_limit_int*0.9)-$dbSize)/49;
        }

        $max_rows=max($max_rows,1048576);

        if($sum_rows>$alter_sum_rows||$sum_rows>$max_rows)
        {
            //big db alert
            $ret['alert_db']=true;
            $ret['sum_rows']=$sum_rows;
            $ret['db_size']=size_format($dbSize,2);
            if($fcgi)
                $ret['alter_fcgi']=true;
        }

        $ret['db_size']=size_format($dbSize,2);
        return $ret;
    }

    public function get_sql_mode()
    {
        try {
            $ret['result'] = WPVIVID_SUCCESS;
            $ret['mysql_mode'] = '';

            global $wpdb;
            $result = $wpdb->get_results('SELECT @@SESSION.sql_mode', ARRAY_A);
            foreach ($result as $row) {
                $ret['mysql_mode'] = $row["@@SESSION.sql_mode"];
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            return array('result'=>'failed','error'=>$message);
        }

        return $ret;
    }

    public function get_mysql_version()
    {
        global $wpdb;

        $mysql_version = $wpdb->db_version();

        return $mysql_version;
    }

    public function check_max_allowed_packet()
    {
        global $wpvivid_plugin,$wpdb;

        $max_allowed_packet = (int) $wpdb->get_var("SELECT @@session.max_allowed_packet");

        if($max_allowed_packet<1048576)
        {
            $wpvivid_plugin->wpvivid_log->WriteLog('warning: max_allowed_packet less than 1M :'.size_format($max_allowed_packet,2),'notice');
        }
        else if($max_allowed_packet<33554432)
        {
            $wpvivid_plugin->wpvivid_log->WriteLog('max_allowed_packet less than 32M :'.size_format($max_allowed_packet,2),'notice');
        }
    }
}