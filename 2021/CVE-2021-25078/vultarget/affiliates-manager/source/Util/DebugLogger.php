<?php
/* 
 * Logs debug data to a file. Here is an example usage
 * global $aio_wp_security;
 * $aio_wp_security->debug_logger->log_debug("Log messaged goes here");
 */
class WPAM_Logger
{

    public static $debug_status = array('SUCCESS','STATUS','NOTICE','WARNING','FAILURE','CRITICAL');
    public static $section_break_marker = "\n----------------------------------------------------------\n\n";
    public static $log_reset_marker = "-------- Log File Reset --------\n";
    
    function __construct()
    {
        //Done nothing
    }
    
    public static function is_logger_enabled()
    {        
        if(get_option(WPAM_PluginConfig::$AffEnableDebug) == 1){
            return true;
        } 
        return false;
    }
    public static function get_debug_timestamp()
    {
        return '['.date('m/d/Y g:i A').'] - ';
    }
    
    public static function get_debug_status($level)
    {
        $size = count(WPAM_Logger::$debug_status);
        if($level >= $size){
            return 'UNKNOWN';
        }
        else{
            return WPAM_Logger::$debug_status[$level];
        }
    }
    
    public static function get_section_break($section_break)
    {
        if ($section_break) {
            return WPAM_Logger::$section_break_marker;
        }
        return "";
    }
    
    public static function append_to_file($content,$file_name)
    {
        //Check the file
        if(empty($file_name)){
            $file_name = 'wpam-log.txt';
        }
        
        $debug_log_file = WPAM_PATH . '/logs/'.$file_name;
        $fp=fopen($debug_log_file,'a');
        fwrite($fp, $content);
        fclose($fp);
    }
    
    public static function reset_log_file($file_name='')
    {
        if(empty($file_name)){$file_name = 'wpam-log.txt';}
        
        $debug_log_file = WPAM_PATH . '/logs/'.$file_name;
        $content = WPAM_Logger::get_debug_timestamp().WPAM_Logger::$log_reset_marker;
        $fp=fopen($debug_log_file,'w');
        fwrite($fp, $content);
        fclose($fp);
    }
    
    public static function log_debug($message,$level=0,$section_break=false,$file_name='')
    {
        //Check if logger is enabled
        if (!WPAM_Logger::is_logger_enabled()) return;
        
        //Log stuff
        $content = WPAM_Logger::get_debug_timestamp();//Timestamp
        $content .= WPAM_Logger::get_debug_status($level);//Debug status
        $content .= ' : ';
        $content .= $message . "\n";
        $content .= WPAM_Logger::get_section_break($section_break);
        WPAM_Logger::append_to_file($content, $file_name);
    }
    
    public static function log_debug_array($array_to_write,$level=0,$section_break=false,$file_name='')
    {
        //Check if logger is enabled
        if (!WPAM_Logger::is_logger_enabled()) return;
        
        //Log stuff
        $content = WPAM_Logger::get_debug_timestamp();//Timestamp
        $content .= WPAM_Logger::get_debug_status($level);//Debug status
        $content .= ' : ';
        ob_start(); 
	print_r($array_to_write); 
	$var = ob_get_contents(); 
	ob_end_clean(); 
        $content .= $var;
        $content .= WPAM_Logger::get_section_break($section_break);
        WPAM_Logger::append_to_file($content, $file_name);
    }
}