<?php /**
 * @version 1.1
 * @package Any
 * @category Menu in Admin Panel
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

class WPBC_Admin_Menus {
    
    /* Static Variables */
    static $capability = array(     'administrator' => 'activate_plugins',
                                    'editor'        => 'publish_pages',
                                    'author'        => 'publish_posts',
                                    'contributor'   => 'edit_posts',
                                    'subscriber'    => 'read' 
                         );
    protected $menu_tag;
    protected $menu_title;        
    protected $menu_title_second;                                               // For root menu chnage second title in submenu - little hack
    protected $page_header;                                                     // Header - H2, in page content
    protected $browser_header;                                                  // Browser Header Title
    protected $in_menu;
    protected $user_role;                                                       // Acceess Role for current menu item
    protected $mune_icon_url;                                                   // Icon - if used 'none', then you can define it in the CSS backgound: .toplevel_page_wpbc .wp-menu-image { background-image: url("../img/icon-16x16.png"); background-position: 7px 7px; }   But its mean that  you are need to load this CSS in every admin page.
    
    public    $root_position;                                                   /* Positions for Core Menu Items
                                                                                    2 Dashboard
                                                                                    4 Separator
                                                                                    5 Posts
                                                                                    10 Media
                                                                                    15 Links
                                                                                    20 Pages
                                                                                    25 Comments
                                                                                    59 Separator
                                                                                    60 Appearance
                                                                                    65 Plugins
                                                                                    70 Users
                                                                                    75 Tools
                                                                                    80 Settings
                                                                                    99 Separator
                                                                                     */
        
    function __construct( $slug, $param = array() ) {
        
        $this->in_menu = false;
        $this->menu_tag = $slug;                                                // For exmaple: 'wpbc-settings' - its 'page' parameter in URL    
        
        if ( isset( $param['menu_title'] ) )        $this->menu_title       = $param['menu_title'];        
        if ( isset( $param['menu_title_second'] ) ) $this->menu_title_second = $param['menu_title_second'];        
        if ( isset( $param['page_header'] ) )       $this->page_header      = $param['page_header'];        
        if ( isset( $param['browser_header'] ) )    $this->browser_header   = $param['browser_header'];        
        if ( isset( $param['in_menu'] ) )           $this->in_menu          = $param['in_menu'];                        
        if ( isset( $param['position'] ) )          $this->root_position    = $param['position'];                        
        else                                        $this->root_position    = null;    
        
        if ( isset( $param['mune_icon_url'] ) )     $this->mune_icon_url          = $param['mune_icon_url'];
        else                                        $this->mune_icon_url          = 'none';
        
        if ( isset( $param['user_role'] ) )         $this->user_role = $param['user_role'];
        else                                        $this->user_role = 'subscriber';
                                
        add_action( 'admin_menu', array($this, 'new_admin_page'), 10 );
        
        add_action( 'admin_menu', array($this, 'change_second_root_menu_title'), 11 );  // Change Title of Submenu title for root menu item
    }

    
    public function load_js() {
        do_action( 'wpbc_load_js_on_admin_page' );
    }

    
    public function load_css() {
        do_action( 'wpbc_load_css_on_admin_page' );
    }
                    
    
    /**
	 * Define Plugin Menu Page
     * 
     */
    public function new_admin_page(){
        
        if ( $this->in_menu == 'root' ) {    // Main Menu
                                                
            $page = $this->create_plugin_menu( 
                                      $this->browser_header                     // Browser Page Header Title
                                    , $this->menu_title                         // Menu Title
                                    , (  ( isset( self::$capability[ $this->user_role ] ) ) ? self::$capability[ $this->user_role ] : self::$capability[ 'subscriber' ]  )  // Capabilities
                                    , $this->menu_tag                           // Slug     // I was used early -> plugin_basename(WPBC_FILE).'wpbc'
                                    , $this->mune_icon_url                      // Icon     // - if used 'none', then you can define it in the CSS backgound: .toplevel_page_wpbc .wp-menu-image { background-image: url("../img/icon-16x16.png"); background-position: 7px 7px; }   But its mean that  you are need to load this CSS in every admin page.
                                    , array( $this, 'content' )                 // Function for output content of page
                                );        

        } else {                // Sub Menu
        
            $page = $this->create_plugin_submenu( 
                                               $this->in_menu                               // The slug name for parent menu (or file name of standard WordPress admin page)                                                                                            
                                               , $this->browser_header                      // Page Title
                                               , $this->menu_title                          // Menu Title
                                               , (  ( isset( self::$capability[ $this->user_role ] ) ) ? self::$capability[ $this->user_role ] : self::$capability[ 'subscriber' ]  )  // Capabilities
                                               , $this->menu_tag                            // Slug
                                               , array( $this, 'content' )                  // Function for output content of page
                                             );
        }
        
        //do_action('wpbc_define_settings_pages', $this->menu_tag );              // Define sub classes, like: page-settings-general.php      $this->menu_tag - 'wpbc-settings' - its 'page' parameter in URL               
        
        do_action('wpbc_menu_created',    $this->menu_tag );                    // Define sub classes, like: page-settings-general.php      $this->menu_tag - 'wpbc-settings' - its 'page' parameter in URL               
        do_action('wpbc_define_nav_tabs', $this->menu_tag );                    // Define Nav tabs.
    }

    
    /**
	 * Content of the Menu Page
     * 
         Define page   S t r u c t u r e,   nav   T A B s ,  set   N O N C E:   wpbc_ajax_admin_nonce field  
     
         in ..\any\class\class-admin-page-structure.php 
         
         then show page C O N T E N T in files, like page-structure.php
         
         $this->menu_tag - 'wpbc-settings' - its 'page' parameter in URL               
     */
    public function content() {                
        
        do_action('wpbc_page_structure_show', $this->menu_tag );              
    }
        
        
    
    /**
	 * Hack for changing Root 2nd Submenu Title
     * 
     * @global type $submenu
     */
    public function change_second_root_menu_title() {
        
        // Change Title of the Main menu inside of submenu              
        global $submenu;                                        
        
        if (       ( $this->in_menu == 'root' ) 
                && ( isset( $submenu[ $this->menu_tag ] ) )  
                && ( isset( $this->menu_title_second ) ) 
            ) {
            $submenu[ $this->menu_tag ][0][0] = $this->menu_title_second; 
        }        
    }
    
    
    /**
	 * Add Menu
     * 
     * @param type $menu_page_title
     * @param type $menu_title
     * @param type $capability
     * @param type $menu_slug
     * @param type $mune_icon_url
     * @param type $page_content
     * @param type $page_css
     * @param type $page_js
     * @return type
     */
    protected function create_plugin_menu($menu_page_title, $menu_title, $capability, $menu_slug, $mune_icon_url, $page_content ) {
        /** 
         * add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
         * @return string The resulting page's hook_suffix
         */
        
        $page = add_menu_page(  $menu_page_title,                               // The text to be displayed in the title tags of the page when the menu is selected
                                $menu_title,                                    // The text to be used for the menu
                                $capability,                                    // The capability required for this menu to be displayed to the user.
                                $menu_slug,                                     // The slug name to refer to this menu by (should be unique for this menu)
                                $page_content,                                  // The function to be called to output the content for this page.
                                (($mune_icon_url=='none')?'none':((empty($mune_icon_url))?'':plugins_url($mune_icon_url, WPBC_FILE)) )          // The url to the icon to be used for this menu. Using 'none' would leave div.wp-menu-image empty so an icon can be added as background with CSS.
                                , $this->root_position                          /* @param int $position The position in the menu order this one should appear
                                                                                 * Positions for Core Menu Items
                                                                                    2 Dashboard
                                                                                    4 Separator
                                                                                    5 Posts
                                                                                    10 Media
                                                                                    15 Links
                                                                                    20 Pages
                                                                                    25 Comments
                                                                                    59 Separator
                                                                                    60 Appearance
                                                                                    65 Plugins
                                                                                    70 Users
                                                                                    75 Tools
                                                                                    80 Settings
                                                                                    99 Separator
                                                                                     */
                             );
        
        add_action( 'admin_print_styles-' . $page, array( $this, 'load_css' ) );
        
        add_action( 'admin_print_scripts-' . $page, array( $this, 'load_js' ) );
                        
        return $page;
    }
    

    /**
	 * Add Sub Menu
     * 
     * @param type $parent_menu_slug -  The slug name for the parent menu (or the file name of a standard WordPress admin page)
     * @param type $menu_page_title
     * @param type $menu_title
     * @param type $capability
     * @param type $menu_slug
     * @param type $page_content
     * @param type $page_css
     * @param type $page_js
     * @return type
     */
    protected function create_plugin_submenu( $parent_menu_slug, $menu_page_title, $menu_title, $capability, $menu_slug, $page_content ) {

        /** 
         * function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' );
         * @return string|bool The resulting page's hook_suffix, or false if the user does not have the capability required.
         */
        
        $page = add_submenu_page(  
                                $parent_menu_slug,                              // The slug name for the parent menu (or the file name of a standard WordPress admin page)
                                $menu_page_title,                               // The text to be displayed in the title tags of the page when the menu is selected
                                $menu_title,                                    // The text to be used for the menu
                                $capability,                                    // The capability required for this menu to be displayed to the user.
                                $menu_slug,                                     // The slug name to refer to this menu by (should be unique for this menu)
                                $page_content                                   // The function to be called to output the content for this page.
                             );

        add_action( 'admin_print_styles-'  . $page, array( $this, 'load_css' ) );
        
        add_action( 'admin_print_scripts-' . $page, array( $this, 'load_js' ) );
        
        return $page;
    }    
}
