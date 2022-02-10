<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC main class
 * @author Webnus <info@webnus.biz>
 */
class MEC
{
    /**
     * Instance of this class. This is a singleton class
     * @var object
     */
    private static $instance = NULL;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    protected function __construct()
    {
        // MEC EP
        if(!defined('EP_MEC_EVENTS')) define('EP_MEC_EVENTS', 555);

        // Import Base library
        $this->import('app.libraries.base');
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
    }

    /**
     * Getting instance. This Class is a singleton class
     * @author Webnus <info@webnus.biz>
     * @return \static
     */
    public static function instance()
	{
        // Get an instance of Class
        if(!self::$instance) self::$instance = new self();

        // Return the instance
        return self::$instance;
	}
    
    /**
     * This method initialize the MEC, This add WordPress Actions, Filters and Widgets
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Import MEC Factory, This file will do the rest
        $factory = MEC::getInstance('app.libraries.factory');

        // Deactivate MEC Lite when Pro is installed
        if(!$factory->getPRO())
        {
            if(!function_exists('is_plugin_active')) include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            if(is_plugin_active('modern-events-calendar/mec.php')) deactivate_plugins('modern-events-calendar-lite/modern-events-calendar-lite.php');
        }

        // Initialize Auto Update Feaature
        if($factory->getPRO()) $factory->load_auto_update();

        // Registering MEC actions
        $factory->load_actions();

        // Registering MEC filter methods
        $factory->load_filters();

        // Registering MEC hooks such as activate, deactivate and uninstall hooks
        $factory->load_hooks();

        // Loading MEC features
        $factory->load_features();

        // Loading MEC skins
        $factory->load_skins();

        // Loading MEC addons
        $factory->load_addons();

        // Register MEC Widget
        $factory->action('widgets_init', array($factory, 'load_widgets'));

        // MEC Body Class
        $factory->action('body_class', array($factory, 'mec_body_class'));

        // MEC Admin Body Class
        $factory->action('admin_body_class', array($factory, 'mec_admin_body_class'));

        // Register MEC Menus
        $factory->action('admin_menu', array($factory, 'load_menus'), 1);

        // Register MEC Menus
        $factory->action('init', array($factory, 'mec_dyncss'));

        // Include needed assets (CSS, JavaScript etc) in the WordPress backend
        $factory->action('admin_enqueue_scripts', array($factory, 'load_backend_assets'), 0);

        // Include needed assets (CSS, JavaScript etc) in the website frontend
		$factory->action('wp_enqueue_scripts', array($factory, 'load_frontend_assets'), 0);

        // Register the shortcodes
        $factory->action('init', array($factory, 'load_shortcodes'));

        // Register language files for localization
        $factory->action('plugins_loaded', array($factory, 'load_languages'));

        // Plugin Update Notification
        $factory->action('in_plugin_update_message-' . MEC_BASENAME , array($factory, 'mecShowUpgradeNotification') , 10,2);
    }
    
    /**
     * Getting a instance of a MEC library
     * @author Webnus <info@webnus.biz>
     * @static
     * @param string $file
     * @param string $class_name
     * @return mixed
     */
    public static function getInstance($file, $class_name = NULL)
    {
        /** Generate class name if not provided **/
        if(!trim($class_name))
        {
            $ex = explode('.', $file);
            $file_name = end($ex);
            $class_name = 'MEC_'.$file_name;
        }

        /** Import the file using import method **/
        if(!class_exists($class_name)) self::import($file);
        
        /** Generate the object **/
        if(class_exists($class_name)) return new $class_name();
        else return false;
    }
    
    /**
     * Imports the MEC file
     * @author Webnus <info@webnus.biz>
     * @static
     * @param string $file Use 'app.libraries.base' for including /path/to/plugin/app/libraries/base.php file
     * @param boolean $override include overridden file or not (if exists)
     * @param boolean $return_path Return the file path or not
     * @return boolean|string
     */
    public static function import($file, $override = true, $return_path = false)
    {
        // Converting the MEC path to normal path (app.libraries.base to /path/to/plugin/app/libraries/base.php)
        $original_exploded = explode('.', $file);
        $file = implode(DS, $original_exploded) . '.php';
        
        $path = MEC_ABSPATH . $file;
        $overridden = false;
        
        // Including override file from theme
        if($override)
        {
            // Search the file in the main theme
            $theme_path = get_template_directory() .DS. 'webnus' .DS. MEC_DIRNAME .DS. $file;
            
            /**
             * If overridden file exists on the main theme, then use it instead of normal file
             * For example you can override /path/to/plugin/app/libraries/base.php file in your theme by adding a file into the /path/to/theme/webnus/modern-events-calendar/app/libraries/base.php
             */
            if(file_exists($theme_path))
            {
                $overridden = true;
                $path = $theme_path;
            }
            
            // If the theme is a child theme then search the file in child theme
            if(get_template_directory() != get_stylesheet_directory())
            {
                // Child theme overriden file
                $child_theme_path = get_stylesheet_directory() .DS. 'webnus' .DS. MEC_DIRNAME .DS. $file;

                /**
                * If overridden file exists on the child theme, then use it instead of normal or main theme file
                * For example you can override /path/to/plugin/app/libraries/base.php file in your theme by adding a file into the /path/to/child/theme/webnus/modern-events-calendar/app/libraries/base.php
                */
                if(file_exists($child_theme_path))
                {
                    $overridden = true;
                    $path = $child_theme_path;
                }
            }
        }
        
        // Return the file path without importing it
        if($return_path) return $path;
        
        // Import the file and return override status
        if(file_exists($path)) require_once $path;
        return $overridden;
    }
    
    /**
     * Load MEC language file from plugin language directory or WordPress language directory
     * @author Webnus <info@webnus.biz>
     */
    public function load_languages()
    {
        // MEC File library
        $file = MEC::getInstance('app.libraries.filesystem', 'MEC_file');
        if(!$file->getPRO())
        {
            // Get current locale
            $locale = apply_filters('plugin_locale', get_locale(), 'modern-events-calendar-lite');
            
            // WordPress language directory /wp-content/languages/mec-en_US.mo
            $language_filepath = WP_LANG_DIR.DS.'modern-events-calendar-lite-'.$locale.'.mo';
            
            // If language file exists on WordPress language directory use it
            if($file->exists($language_filepath))
            {
                load_textdomain('modern-events-calendar-lite', $language_filepath);
            }
            // Otherwise use MEC plugin directory /path/to/plugin/languages/modern-events-calendar-lite-en_US.mo
            else
            {
                load_plugin_textdomain('modern-events-calendar-lite', false, dirname(plugin_basename(__FILE__)).DS.'languages'.DS);
            }
        }
        else
        {
            // Get current locale
            $locale = apply_filters('plugin_locale', get_locale(), 'modern-events-calendar-lite');
            
            // WordPress language directory /wp-content/languages/mec-en_US.mo
            $language_filepath = WP_LANG_DIR.DS.'mec-'.$locale.'.mo';
            
            // If language file exists on WordPress language directory use it
            if($file->exists($language_filepath))
            {
                load_textdomain('mec', $language_filepath);
            }
            // Otherwise use MEC plugin directory /path/to/plugin/languages/mec-en_US.mo
            else
            {
                load_plugin_textdomain('mec', false, dirname(plugin_basename(__FILE__)).DS.'languages'.DS);
            }
        }
    }
    
    /**
     * Load Single event full content
     * @author Webnus <info@webnus.biz>
     */
    public function single()
    {
        // Import Render Library
        $render = MEC::getInstance('app.libraries.render');
        return $render->vsingle(array('id'=>get_the_ID()));
    }
    
    /**
     * Load category archive page
     * @author Webnus <info@webnus.biz>
     */
    public function category()
    {
        // Import Render Library
        $render = MEC::getInstance('app.libraries.render');
        return $render->vcategory();
    }
}