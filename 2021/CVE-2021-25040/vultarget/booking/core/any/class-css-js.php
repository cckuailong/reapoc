<?php /**
 * @version 1.1
 * @package  Any
 * @category Load JS and CSS files
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-10-28
 */

abstract class WPBC_JS_CSS {

    public $objects = array();
    public $type;               // css || js
    
    function __construct() {        
        $this->define();
        add_action( 'admin_enqueue_scripts', array( $this, 'registerScripts' ) );
        add_action( 'wp_enqueue_scripts',    array( $this, 'registerScripts' ) );
        
        add_action( 'wpbc_load_js_on_admin_page',  array( $this, 'load_js_on_admin_page' ) );    // Load JS.   Hook fire only in admin pages of plugin. CLASS: WPBC_Admin_Menus (..\any\class\class-admin-menu.php)
        add_action( 'wpbc_load_css_on_admin_page', array( $this, 'load_css_on_admin_page' ) );   // Load CSS.  Hook fire only in admin pages of plugin. CLASS: WPBC_Admin_Menus (..\any\class\class-admin-menu.php)
    }

    public function load_css_on_admin_page() {
        
        if ( $this->getType() == 'css' ) 
            $this->load();
    }
    
    public function load_js_on_admin_page() {
        
        if ( $this->getType() == 'js' ) 
            $this->load();
    }
    
    /** Define all Scripts or Styles here */
    abstract public function define();
    
    /**
	 * Enqueue Scripts or Styles.
     * 
     * @param type $where_to_load - can be "admin" or "client"
     */
    abstract public function enqueue( $where_to_load );
    
    
    /**
	 * Deregister some Conflict scripts from other plugins.
     * 
     * @param type $where_to_load - can be "admin" or "client"
     */
    abstract public function remove_conflicts( $where_to_load );
    
    
    // Define CSS or JavaScript
    public function setType($param) {
        $this->type = $param;
    }
    
    // Get type of this script
    public function getType() {
        return $this->type;
    }
    
    // Add new Style or Script
    public function add($param) {
        $this->objects[] = $param;
    }

    
    private function isLoad( $whereToLoadArray ) {
        $is_load = false;

        if ( ( is_admin() ) && ( in_array('admin', $whereToLoadArray ) ) ) 
            $is_load = true;   

        if ( ( ! is_admin() ) && ( in_array('client', $whereToLoadArray ) ) ) 
            $is_load = true;    

        return $is_load;
    }

    
    // Register
    public function registerScripts() {
        
        //if ( function_exists( 'wp_dequeue_script' ) ) 
        $this->remove_conflicts(  ( is_admin() ? 'admin': 'client' )  );
        
        foreach( $this->objects as $script ) {
                            
            if ( $this->isLoad( $script['where_to_load'] ) ) {
                
                if ( $this->getType() == 'css' )
                    wp_register_style( $script['handle'], $script['src'], $script['deps'], $script['version'] ); 
                else
                    wp_register_script( $script['handle'], $script['src'], $script['deps'], $script['version'] );
            }
        }
    }
        
    
    // Load scripts or styles here
    public function load(){
        
        $is_load_scripts = true;
        
        $is_load_scripts = apply_filters( 'wpbc_is_load_script_on_this_page', $is_load_scripts );
        
        if ( ! $is_load_scripts ) return;                                       // Exist,  if on some page we do not need to  load scripts
        
        
        foreach( $this->objects as $num => $script ) {
                            
            if ( $this->isLoad( $script['where_to_load'] ) ) {
                
                if ( $this->getType() == 'css' ) {
                    
                    if ( $script['condition'] === false )
                        
                        wp_enqueue_style( $script['handle'] );
                    
                    else {
                        
                        if (! function_exists('wp_style_add_data') ) {           // This function  is available only since WordPress 3.6.0 Update
                            wp_enqueue_style(  $script['handle'] );
                            wp_style_add_data( $script['handle'], 'conditional', $script['condition'] );
                        } else {                                                // Add additional "dynamic CSS" if the WP version older than 3.6.0 (its function  suport since WP 3.3)
                            if ( ($num-1) > -1 ) {  // Its because "wp_add_inline_style" add the CSS to the already added style. So its require that some other simple style was added before
                                wp_enqueue_style(  $this->objects[($num-1)]['handle'] );
                                wp_add_inline_style( $this->objects[($num-1)]['handle'], 
                                                            sprintf("<!--[if ".$script['condition']."]>\n" .
                                                            "<link rel='stylesheet' id='".$script['handle']."-css' href='". $script['src'] ."?ver=" . $script['version'] . "' type='text/css' media='all' />\n" .
                                                            "<![endif]-->\n")
                                                   );
                            }
                        }                        
                    }
                    
                } else {
                    wp_enqueue_script( $script['handle'] );        
                }
                
            }
        }        
        
        $this->enqueue( ( is_admin() ? 'admin': 'client' ) );
        
        if ( $this->getType() == 'css' ) 
            do_action( 'wpbc_enqueue_style',  ( is_admin() ? 'admin': 'client' ) );     
        
        if ( $this->getType() == 'js' ) 
            do_action( 'wpbc_enqueue_script', ( is_admin() ? 'admin': 'client' ) );     
    }
    
} 