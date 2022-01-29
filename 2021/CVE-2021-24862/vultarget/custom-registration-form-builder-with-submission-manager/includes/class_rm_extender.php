<?php

/**
 * This class manages RM Extensions.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 * @author     CMSHelplive
 */

class RM_Extender
{
    
    public static $instance;
    public static $extensions = array();

    public function __construct()
    {
       self::load_extensions();
    }
    
    public static function init()
    {
        return self::get_instance();
    }
    
    public static function get_instance()
    {
        if (null === static::$instance)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    public static function load_extensions()
    {
        self::populate_extension_list();
        self::activate_extensions();
    }
    
    public static function populate_extension_list()
    {
        $dir = new DirectoryIterator(RM_BASE_DIR.'plus/');
        foreach ($dir as $fileinfo)
        {
            if (!$fileinfo->isDot() && $fileinfo->isDir())
            {
                $ex_name = $fileinfo->getFilename();
                if(file_exists(RM_BASE_DIR."plus/$ex_name/$ex_name.php"))
                {
                    //Extension can only be activated if it is enabled. HeHaa.
                    self::$extensions[$ex_name] = (object)array("enabled" => true, "activated" => false);
                }
            }
        }
    }
    
    //Activate extension. If null is passed it will activate all extensions
    public static function activate_extensions( $ext = null)
    {
        if($ext)
        {
            if(isset(self::$extensions[$ext]) && self::$extensions[$ext]->enabled)
            {
                include_once RM_BASE_DIR."plus/$ext/$ext.php";
                self::$extensions[$ext]->activated = true;
            }
        }
        else
        {
            foreach (self::$extensions as $ex_name => $ex)
            {
               if($ex->enabled)
               {
                   include_once RM_BASE_DIR."plus/$ex_name/$ex_name.php";
                   self::$extensions[$ex_name]->activated = true;
               }
            }
        }
    }
   
}
