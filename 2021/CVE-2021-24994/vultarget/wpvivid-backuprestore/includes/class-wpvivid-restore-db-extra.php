<?php

class WPvivid_Restore_DB_Extra
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;

    private $support_engines;
    private $support_charsets;
    private $support_collates;

    private $default_engines;
    private $default_charsets;
    private $default_collates;

    public function __construct($host = 'localhost', $username = 'root', $password = '', $database = 'test', $charset = 'utf8'){
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;
    }

    public function execute_extra_sql_file($file, $option){
        global $wpvivid_plugin, $wpvivid_additional_db;
        try {
            $wpvivid_additional_db = new wpdb($this->username, $this->password, $this->database, $this->host);

            $wpvivid_plugin->restore_data->write_log('Start import sql file.', 'notice');

            $this->support_engines = array();
            $this->support_charsets = array();
            $this->support_collates = array();

            $this->default_engines = array();
            $this->default_charsets = array();
            $this->default_collates = array();

            $this->default_engines = isset($option['default_engine']) ? $option['default_engine'] : 'MyISAM';
            $this->default_charsets = isset($option['default_charsets']) ? $option['default_charsets'] : DB_CHARSET;
            $this->default_collates = isset($option['default_collations']) ? $option['default_collations'] : DB_COLLATE;

            $result = $wpvivid_additional_db->get_results("SHOW ENGINES", OBJECT_K);
            foreach ($result as $key => $value) {
                $this->support_engines[] = $key;
            }

            $result = $wpvivid_additional_db->get_results("SHOW CHARACTER SET", OBJECT_K);
            foreach ($result as $key => $value) {
                $this->support_charsets[] = $key;
            }

            $result = $wpvivid_additional_db->get_results("SHOW COLLATION", OBJECT_K);
            foreach ($result as $key => $value) {
                $this->support_collates[$key] = $value;
            }

            $sql_handle = fopen($file, 'r');
            if ($sql_handle === false) {
                $ret['result'] = WPVIVID_FAILED;
                $ret['error'] = 'file not found. file name:' . $file;
                return $ret;
            }

            $line_num = 0;
            $query = '';

            while (!feof($sql_handle)) {
                $line = fgets($sql_handle);
                $line_num++;
                $startWith = substr(trim($line), 0, 2);
                $startWithEx = substr(trim($line), 0, 3);
                $endWith = substr(trim($line), -1, 1);
                $line = rtrim($line);

                if (empty($line) || $startWith == '--' || ($startWith == '/*' && $startWithEx != '/*!') || $startWith == '//') {
                    continue;
                }

                $query = $query . $line;
                if ($endWith == ';') {
                    if (preg_match('#^\\s*CREATE TABLE#', $query)) {
                        $this->create_table($query);
                    } else if (preg_match('#^\\s*LOCK TABLES#', $query)) {
                        $this->lock_table($query);
                    } else if (preg_match('#^\\s*INSERT INTO#', $query)) {
                        $this->insert($query);
                    } else if (preg_match('#^\\s*DROP TABLE #', $query)) {
                        $this->drop_table($query);
                    } else if (preg_match('#\/*!#', $query)) {
                        if ($this->execute_sql($query) === false) {
                            $wpvivid_plugin->restore_data->write_log('Restore ' . basename($file) . ' error at line ' . $line_num . ',' . PHP_EOL . 'errorinfo: [' . implode('][', $this->errorInfo()) . ']', 'Warning');
                            $query = '';
                            continue;
                        }
                    } else {
                        if ($this->execute_sql($query) === false) {
                            $wpvivid_plugin->restore_data->write_log('Restore ' . basename($file) . ' error at line ' . $line_num . ',' . PHP_EOL . 'errorinfo: [' . implode('][', $this->errorInfo()) . ']', 'Warning');
                            $query = '';
                            continue;
                        }
                    }
                    $query = '';
                }
            }
            fclose($sql_handle);

            $ret['result'] = WPVIVID_SUCCESS;
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $message = 'An exception has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            $ret['error'] = $message;
        }
        catch (Error $error)
        {
            $ret['result']='failed';
            $message = 'An exception has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            $ret['error'] = $message;
        }
        return $ret;
    }

    private function create_table($query){
        global $wpvivid_plugin;
        $table_name='';
        if (preg_match('/^\s*CREATE TABLE +\`?([^\`]*)\`?/i', $query, $matches)) {
            $table_name = $matches[1];
        }
        $wpvivid_plugin->restore_data->write_log('Create table: '.$table_name, 'notice');

        if (preg_match('/ENGINE=([^\s;]+)/', $query, $matches)) {
            $engine = $matches[1];
            $replace_engine=true;
            foreach ($this->support_engines as $support_engine) {
                if(strtolower($engine)==strtolower($support_engine)) {
                    $replace_engine=false;
                    break;
                }
            }

            if($replace_engine!==false) {
                if(!empty($this->default_engines))
                    $replace_engine=$this->default_engines[0];
            }

            if($replace_engine!==false) {
                $wpvivid_plugin->restore_data->write_log('create table replace engine:'.$engine.' to :'.$replace_engine, 'notice');
                $query=str_replace("ENGINE=$engine", "ENGINE=$replace_engine", $query);
            }
        }

        if (preg_match('/CHARSET ([^\s;]+)/', $query, $matches)||preg_match('/CHARSET=([^\s;]+)/', $query, $matches)) {
            $charset = $matches[1];
            $replace_charset=true;
            foreach ($this->support_charsets as $support_charset) {
                if(strtolower($charset)==strtolower($support_charset)) {
                    $replace_charset=false;
                    break;
                }
            }

            if($replace_charset) {
                $replace_charset=$this->default_charsets[0];
            }

            if($replace_charset!==false) {
                $wpvivid_plugin->restore_data->write_log('create table replace charset:'.$charset.' to :'.$replace_charset, 'notice');
                $query=str_replace("CHARSET=$charset", "CHARSET=$replace_charset", $query);
                $query=str_replace("CHARSET $charset", "CHARSET=$replace_charset", $query);
                $charset=$replace_charset;
            }

            $collate='';

            if (preg_match('/ COLLATE ([a-zA-Z0-9._-]+)/i', $query, $matches)) {
                $collate = $matches[1];
            }
            else if(preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $query, $matches)) {
                $collate = $matches[1];
            }

            if(!empty($collate)) {
                $replace_collate=true;
                foreach ($this->support_collates as $key=>$support_collate) {
                    if(strtolower($charset)==strtolower($support_collate->Charset)&&strtolower($collate)==strtolower($key)) {
                        $replace_collate=false;
                        break;
                    }
                }

                if($replace_collate) {
                    $replace_collate=false;
                    foreach ($this->support_collates as $key=>$support_collate) {
                        if(strtolower($charset)==strtolower($support_collate->Charset)) {
                            if($support_collate->Default=='Yes') {
                                $replace_collate=$key;
                            }
                        }
                    }

                    if($replace_collate==false) {
                        foreach ($this->support_collates as $key=>$support_collate) {
                            if(strtolower($charset)==strtolower($support_collate->Charset)) {
                                $replace_collate=$key;
                                break;
                            }
                        }
                    }
                }

                if($replace_collate!==false) {
                    $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate, 'notice');
                    $query=str_replace("COLLATE $collate", "COLLATE $replace_collate", $query);
                    $query=str_replace("COLLATE=$collate", "COLLATE=$replace_collate", $query);
                }
            }
        }
        else
        {
            if (preg_match('/ COLLATE ([a-zA-Z0-9._-]+)/i', $query, $matches)) {
                $collate = $matches[1];
            }
            else if(preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $query, $matches)) {
                $collate = $matches[1];
            }

            if(!empty($collate)) {
                $replace_collate=true;
                foreach ($this->support_collates as $key=>$support_collate) {
                    if(strtolower($collate)==strtolower($key)) {
                        $replace_collate=false;
                        break;
                    }
                }

                if($replace_collate) {
                    $replace_collate=false;
                    foreach ($this->support_collates as $key=>$support_collate) {
                        if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset)) {
                            if($support_collate->Default=='Yes') {
                                $replace_collate=$key;
                            }
                        }
                    }

                    if($replace_collate==false) {
                        foreach ($this->support_collates as $key=>$support_collate) {
                            if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset)) {
                                $replace_collate=$key;
                                break;
                            }
                        }
                    }
                }

                if($replace_collate!==false) {
                    $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate, 'notice');
                    $query=str_replace("COLLATE $collate", "COLLATE $replace_collate", $query);
                    $query=str_replace("COLLATE=$collate", "COLLATE=$replace_collate", $query);
                }
            }
        }

        $this->execute_sql($query);
        return $table_name;
    }

    private function lock_table($query)
    {
        global $wpvivid_plugin;
        if (preg_match('/^\s*LOCK TABLES +\`?([^\`]*)\`?/i', $query, $matches)) {
            $table_name = $matches[1];
            $wpvivid_plugin->restore_data->write_log('lock table: '.$table_name, 'notice');
        }
        $this->execute_sql($query);
    }

    private function insert($query)
    {
        $this->execute_sql($query);
    }

    private function drop_table($query){
        global $wpvivid_plugin;
        if (preg_match('/^\s*DROP TABLE IF EXISTS +\`?([^\`]*)\`?\s*;/i', $query, $matches)) {
            $table_name = $matches[1];
            $wpvivid_plugin->restore_data->write_log('Drop table if exist '.$table_name, 'notice');
        }
        $this->execute_sql($query);
    }

    private function execute_sql($query)
    {
        global $wpvivid_plugin, $wpvivid_additional_db;
        if ($wpvivid_additional_db->get_results($query)===false) {
            $error=$wpvivid_additional_db->last_error;
            $wpvivid_plugin->restore_data->write_log($error, 'Warning');
        }
    }

    public function errorInfo()
    {
        global $wpvivid_additional_db;
        return $wpvivid_additional_db->last_error;
    }
}