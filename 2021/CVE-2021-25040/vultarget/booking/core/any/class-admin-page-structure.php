<?php /**
 * @version 1.1
 * @package Any
 * @category Page Structure in Admin Panel
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-11-02
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly


/** Define Settings Page Structure */
abstract class WPBC_Page_Structure {
    
    protected static $nav_tabs;                                                 // Tabs array, same for all objects

    private $current_page_params;                                               // Parameters for current page,  if this page selected,  otherwise its = empty array()
    private $is_only_icons = false;  
    
    protected $tags;                                                            // Defining Name of parameter in GET request for Navigation TOP and BOTTOM tabs 
                                                                                // - $_GET[ 'tab' ]    == 'payment' 
                                                                                // - $_GET[ 'subtab' ] == 'paypal' 
    
    public function __construct() {
        
        $this->tags = array();
        $this->tags['tab']    = 'tab';                                          // Defining Name of parameter in GET request - $_GET[ 'tab' ]    == 'payment' 
        $this->tags['subtab'] = 'subtab';                                       // Defining Name of parameter in GET request - $_GET[ 'subtab' ] == 'paypal' 
    
                
        $this->current_page_params = array();
        self::$nav_tabs = array();
        
        add_action('wpbc_define_nav_tabs', array( $this, 'wpbc_define_nav_tabs' ) );            // This Hook fire after creation menu in class WPBC_Admin_Menus 

        add_action('wpbc_page_structure_show', array( $this, 'content_structure' ) );           // This Hook fire in the class WPBC_Admin_Menus for showing page content of specific menu                
    }
        
    ////////////////////////////////////////////////////////////////////////////
    // Abstract Methods
    ////////////////////////////////////////////////////////////////////////////
    
    /**
	 * Define slug in what menu to show this page.                             // Parameter relative: $_GET['page'].
     * 
     * @return string         
     * 
     * Example: 
     
        return 'wpbc-settings';
     
     */
    abstract public function in_page();
    
    /**
	 * Define Tabs and Subtabs of this Admin Page
     * 
     * @return array();
     
     * Example:
     
        $tabs = array();
        $tabs[ 'form' ] = array(
                              'title' => __('Form','booking')                   // Title of TAB    
                            , 'hint' => __('Customizaton of Form Fields', 'booking')            // Hint    
                            , 'page_title' =>ucwords( __('Form fields', 'booking') )     // Title of Page    
                            //, 'link' => ''                                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            //, 'position' => ''                                // 'left'  ||  'right'  ||  ''
                            //, 'css_classes' => ''                             // CSS class(es)
                            //, 'icon' => ''                                    // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-edit'         // CSS definition  of forn Icon
                            , 'default' => false                                // Is this tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this tab disbaled: true || false. 
                            , 'hided'   => false                                // Is this tab hided: true || false. 
                            , 'subtabs' => array()
            
        );
        $tabs[ 'upgrade' ] = array(
                              'title' => __('Upgrade','booking')                // Title of TAB    
                            , 'hint' => __('Upgrade to higher version', 'booking')              // Hint    
                            //, 'page_title' => __('Upgrade', 'booking')        // Title of Page    
                            , 'link' => 'http://server.com/'                    // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
                            , 'position' => 'right'                             // 'left'  ||  'right'  ||  ''
                            //, 'css_classes' => ''                             // CSS class(es)
                            //, 'icon' => ''                                    // Icon - link to the real PNG img
                            , 'font_icon' => 'glyphicon glyphicon-shopping-cart'// CSS definition  of forn Icon
                            //, 'default' => false                              // Is this tab activated by default or not: true || false. 
                            //, 'subtabs' => array()
            
        );
        
        $subtabs = array();
        
        $subtabs['fields'] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' => __('Form','booking')                   // Title of TAB    
                            , 'page_title' => __('Form Settings', 'booking')                            // Title of Page    
                            , 'hint' => __('Customization of Form Settings', 'booking')                // Hint    
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'https://www.paypalobjects.com/webstatic/icon/pp258.png'      // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-credit-card'                        // CSS definition of Font Icon
                            , 'default' =>  true                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  =>  false                             // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $subtabs['form-separator'] = array( 
                            'type' => 'separator'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                        );        
        $subtabs['form-goto'] = array( 
                            'type' => 'goto-link'                               // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' =>ucwords( __('Form fields', 'booking') )   // Title of TAB    
                            , 'hint' => ''                                      // Hint    
                            , 'show_section' => 'id_of_show_section'            // ID of HTML element, for scroll to.
                        );
     
        ob_start();
        ...
        $html_element_data = ob_get_clean();
        
        $subtabs['form-selection'] = array( 
                            'type' => 'html'                                    // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'html' => $html_element_data
                        );
        
        $subtabs['form-save'] = array( 
                            'type' => 'button'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' => __('Save Changes','booking')           // Title of TAB    
                            , 'form' => 'wpbc_form'                             // Required for 'button'!  Name of Form  to submit    
                        );
        
        $tabs[ 'form' ][ 'subtabs' ] = $subtabs;
        
        return $tabs;
    */
    abstract public function tabs();

    /**
	 * Show Content of this page - Main function.
     *  
     *  In top  of this function  have to be checking ubout Update (saving POST request).
     * 
     *   Exmaple:
      
        //  S u b m i t  ///////////////////////////////////////////////////////
        
        $this_submit_form  = 'wpbc_emails_toolbar';                             // Define form name
        
        if ( isset( $_POST['is_form_sbmitted_'. $this_submit_form ] ) ) {

            // Check   N o n c e
            check_admin_referer( 'wpbc_settings_page_' . $this_submit_form  );  // Its stop show anything on submiting, if its not refear to the original page

            // Make Update of settings 
            $edit_field_data = $this->update_wpbc_emails_toolbar( $menu_slug );
        } 
        ////////////////////////////////////////////////////////////////////////
     */
    abstract public function content();

    
    
    ////////////////////////////////////////////////////////////////////////////
    // C O N T E N T
    ////////////////////////////////////////////////////////////////////////////
        
    /**
	 * General Page Structure
     * 
     * @param string $page_tag - its the same that  return $this->in_page()
     */        
    public function content_structure( $page_tag ) {    
        
        
        
        if ( ! $this->is_page_activated() ) 
            return  false;        

        
        
        $active_page_tab = $active_page_subtab = '';
        if (  ( isset( $this->current_page_params['tab'] ) ) && ( ! empty( $this->current_page_params['tab']['tag'] ) )  )
            $active_page_tab = $this->current_page_params['tab']['tag'];
        if (  ( isset( $this->current_page_params['subtab'] ) ) && ( ! empty( $this->current_page_params['subtab']['tag'] ) )  )
            $active_page_subtab = $this->current_page_params['subtab']['tag'];
        
        $is_show_this_page = apply_filters( 'wpbc_before_showing_settings_page_is_show_page', true, $page_tag, $active_page_tab, $active_page_subtab  );      // Fires Before showing settings Content page
        
        if ( $is_show_this_page === false ) return  false;   
        
        do_action( 'wpbc_before_settings_content', $page_tag, $active_page_tab, $active_page_subtab  );                 // Fires Before showing settings Content page
        
        ?><div id="<?php echo $page_tag; ?>-admin-page" class="wrap wpbc_page">            
            <h1 class="wpbc_header"><div class="wpbc_header_icon"></div><?php echo $this->get_page_header_h1(); ?></h1>
            <div class="wpbc_admin_message"></div>
            <div class="wpbc_admin_page">
                <div id="ajax_working"></div>
                <div class="clear wpbc_header_margin"></div>
                <div id="ajax_respond" class="ajax_respond" style="display:none;"></div>                
                <?php 
                                
                // T A B S 
                $this->show_tabs_structure( $page_tag );                        
                
                wp_nonce_field('wpbc_ajax_admin_nonce',  "wpbc_admin_panel_nonce" ,  true , true ); 
               
                // C o n t e n t
                if (  ( isset( $this->current_page_params['subtab'] ) ) && ( isset( $this->current_page_params['subtab']['content'] ) )  ) {
                
                    call_user_func( array( $this, $this->current_page_params['subtab']['content'] ) ); 
                
                } else if (  ( isset( $this->current_page_params['tab'] ) ) && ( isset( $this->current_page_params['tab']['content'] ) )  ) {
                    call_user_func( array( $this, $this->current_page_params['tab']['content'] ) );     
                
                } else 
                    $this->content();     
           
                do_action('wpbc_show_settings_content' , $page_tag, $active_page_tab, $active_page_subtab );
            ?></div>
        </div><?php    
        
        do_action( 'wpbc_after_settings_content', $page_tag, $active_page_tab, $active_page_subtab );                  // Fires After showing settings Content page
    }
      
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Active Page Parameters
    ////////////////////////////////////////////////////////////////////////////
    
    /**
	 * Check if this page selected (active), depend from the GET parameter
     *  If selected, then  define Current Page Parameters.
     * 
     * @return boolean 
     */    
    private function is_page_activated() {

        $is_page_selected = false;       
        
        $this_page = $this->in_page();

        foreach ( $this->tabs() as $this_tab_tag => $this_tab ) {               // Get First Tab Element,  which  MUST be subtab element, all other tabs, can  be links,  not really  showing content!!!
            break;
        }

        if ( empty( $this_tab ) )
            return $this_page;      // this page empty - tabs is empty  array,  probabaly  its was redefined in child CLASS to $tabs = array(); for not ability to open this page.

        $this_subtab_tag = 0;
        $this_subtab     = array( 'default' => false );    
        
        if ( isset( $this_tab['subtabs'] ) )
            foreach ( $this_tab['subtabs'] as $temp_this_subtab_tag => $temp_this_subtab  ) {
            
                if ( $temp_this_subtab['type'] == 'subtab' ) {                  // Get First Subtab element from  subtabs array
                    $this_subtab_tag = $temp_this_subtab_tag;
                    $this_subtab     = $temp_this_subtab;
                    break;
                }
            }

//debuge($this_page, $_REQUEST);            
        if (       ( isset( $_REQUEST[ 'page' ] ) ) 
                && ( $this_page == $_REQUEST[ 'page' ] )  ){                        // We are inside of this page. Menu item selected. 

            if (   ( ! isset( $_REQUEST[ $this->tags['tab'] ] ) )                            // TAB      NOT     selected    &&  Default
                //&& ( ! isset( $_REQUEST[ $this->tags['subtab'] ] ) )                       // SubTab   NOT     selected
                && ( isset( $this_tab['default'] ) ) && ( $this_tab['default'] )                                     
               ) 
                $is_page_selected = true; 
            
            if (   ( isset( $_REQUEST[ $this->tags['tab'] ] ) )                              // TAB      Selected
                && ( ! isset( $_REQUEST[ $this->tags['subtab'] ] ) )                         // SubTab   NOT     selected    &&      ! exist     ||  Default
                && ( $_REQUEST[ $this->tags['tab'] ] == $this_tab_tag )                      
                && ( ( $this_subtab_tag === 0 )  
                     || ( $this_subtab['default'] )
                   )
               ) 
                $is_page_selected = true; 
            
            if (   ( isset( $_REQUEST[ $this->tags['tab'] ] ) )                              // TAB      Selected
                && ( isset( $_REQUEST[ $this->tags['subtab'] ] ) )                           // SubTab   Selected
                && ( $_REQUEST[ $this->tags['tab'] ] == $this_tab_tag )
                && ( $_REQUEST[ $this->tags['subtab'] ] == $this_subtab_tag )
               ) 
                $is_page_selected = true; 
        }  
        
                
        if ( $is_page_selected )                                                // If this page activated,  then define Current Page parameters
            $this->define_current_page_parameters( $this_tab_tag,  $this_tab, $this_subtab_tag, $this_subtab );
        
        return $is_page_selected;
    }
    
    
    /**
	 * Define parameters for current selected page
     * 
     * @param type $paramas
     */
    private function define_current_page_parameters(  $this_tab_tag,  $this_tab, $this_subtab_tag, $this_subtab ) {
        
        $this->current_page_params = array(
                                                    'tab' => array_merge ( $this_tab,    array( 'tag' => $this_tab_tag ) )
                                               , 'subtab' => array_merge ( $this_subtab, array( 'tag' => $this_subtab_tag ) )
                                           );
    }
        
    
    /**
	 * Get Header Title (H1) of this selected page
     * Firstly  check in subtabs,  otherwise get from  tabs and if not exist  then 
     * 
     * @return string
     */
    private function get_page_header_h1() {
        
        if ( ! empty( $this->current_page_params ) ) {
            
            if ( isset( $this->current_page_params['subtab']['page_title'] ) )
                return $this->current_page_params['subtab']['page_title'];
            
            if ( isset( $this->current_page_params['tab']['page_title'] ) )
                return $this->current_page_params['tab']['page_title'];
        }
        
        return '';            
    }

    
    /**
	 * Get all SubTabs of current opened page Tab
     * 
     * @param string $menu_in_page_tag - Optional. Menu Tag, the same as $this->in_page();
     * @return array
     */
    private function get_all_sub_tabs_of_selected_tab( $menu_in_page_tag = false ) {
        
        if ($menu_in_page_tag === false ) 
            $menu_in_page_tag = $this->in_page();

        $all_sub_tabs_of_selected_tab = self::$nav_tabs[ $menu_in_page_tag ][ $this->current_page_params['tab']['tag'] ]['subtabs'];
        
        return $all_sub_tabs_of_selected_tab;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // T A B s
    ////////////////////////////////////////////////////////////////////////////

    // Define ------------------------------------------------------------------
    
    /**
	 * Define TABS structure.
     *  General structure of tabs for every plugin menu page.
     */
    public function wpbc_define_nav_tabs() {                                    // Function executed after creation menu in class WPBC_Admin_Menus     
/*
  Array (
            [wpbc-resources] => Array ()
            [wpbc-settings] => Array
                (
                    [general] => Array
                        (
                            [title] => General
                            [page_title] => General Settings
                            ...
                            [subtabs] => Array ()
                        )
                    [help] => Array
                        (
                            [title] => Help
                            [page_title] => 
                            ...
                            [subtabs] => Array ()
                        )
                    [form] => Array
                        (
                            [title] => Form
                            [hint] => Customizaton of Form Fields
                            [page_title] => Form Fields
                            ...
                            [subtabs] => Array
                                (
                                    [goto-form] => Array
                                        (
                                            [type] => goto-link
                                            [title] => Booking Form Fields
                                            ...
                                            [content] => content
                                            [update] => update
                                        )

                                    [goto-content-data] => Array
                                        (
                                            [type] => button
                                            [title] => Save Changes
                                            [form] => wpbc_form
                                            ...
                                        )

                                )

                        )
                    [payment] => Array
                        (
                            [title] => Payments
                            [hint] => Customizaton of Payment
                            [page_title] => Payment Gateways
                            ...
                            [subtabs] => Array
                                (
                                    [paypal] => Array
                                        (
                                            [type] => subtab
                                            [title] => PayPal
                                            ...
                                        )

                                    [sage] => Array
                                        (
                                            [type] => subtab
                                            [title] => Sage
                                            ...
                                        )

                                )

                        )

                )

        )
 */        
        
        if ( ! isset( self::$nav_tabs[ $this->in_page() ] ) )
            self::$nav_tabs[ $this->in_page() ] = array();                      // If this page does not exist, then define it.
        
        $current_tab = $this->tabs();
        $current_subtabs = array();                                             // Get Subtabs in separate array.
        foreach ( $current_tab as $tab_tag => $tab_array ) {
            
            if ( isset( $tab_array[ 'subtabs' ] ) ) {
                $current_subtabs[ $tab_tag ] = $tab_array[ 'subtabs' ];         // Create new Subtabs array
                
                unset( $current_tab[ $tab_tag ][ 'subtabs' ] );                 // Detach Subtabs array from Tab array. Its required for do not overwrite subtabs with  already  exist subtabs in previlosly defined tab.
            } else  
                $current_subtabs[ $tab_tag ] = array();
        }
        
        
        foreach ( $current_tab as $tab_tag => $tab_array ) {
                        
            if ( ! isset( self::$nav_tabs[ $this->in_page() ][ $tab_tag ] ) ) {                 // If this tab  ( for exmaple "payment") declared previously,  then  does not do  anything
                
                self::$nav_tabs[ $this->in_page() ][ $tab_tag ] = $current_tab[ $tab_tag ];
                self::$nav_tabs[ $this->in_page() ][ $tab_tag ][ 'subtabs' ] = array();
            }
            
            if ( isset(self::$nav_tabs[ $this->in_page() ] ) ) {
                                                                                                    // Merge subtabs (Ex: PayPal and Sage) and attach to current tab: (Ex: payment)
                self::$nav_tabs[ $this->in_page() ][ $tab_tag ][ 'subtabs' ] = array_merge( 
                                                                                    self::$nav_tabs[ $this->in_page() ][ $tab_tag ][ 'subtabs' ]
                                                                                    , $current_subtabs[ $tab_tag ]
                                                                                );                    
            }
        }        
    }
    
    /**
	 * Get array  of visible TABs
     * Tabs that  do not hided or disbaled
     * 
     * @param string $menu_in_page_tag - Menu Tag, the same as $this->in_page();
     * @return type
     */
    private function get_visible_tabs( $menu_in_page_tag ) {

        $visible_tabs = array();
        
        foreach ( self::$nav_tabs[ $menu_in_page_tag ] as $tab_tag => $tab ) {
            
            if (       empty( $tab['disabled']  )
                    && empty( $tab['hided']  )
                ) {
                    $visible_tabs[$tab_tag] = $tab;
                }
        }
        
        return $visible_tabs;
    }
    
    // Showing -----------------------------------------------------------------
    
    /**
     * 
     * @param string $menu_in_page_tag - Menu Tag, the same as $this->in_page();
     * @return boolean - true if nav tabs exist  and false if does not exist
     */
    public function show_tabs_structure( $menu_in_page_tag ) {
        
        // Exit if no Tabs in this page.
        if ( empty( self::$nav_tabs[ $menu_in_page_tag ] ) )
            return false;           
                
        // Exit if tabs hidded or disbaled
        $visible_tabs = $this->get_visible_tabs( $menu_in_page_tag );        
        if ( empty(  $visible_tabs ) ) 
            return false; 
                
        ?><span class="wpdevelop wpdvlp-nav-tabs-container">
        <div class="clear"></div><?php 
                
        wpbc_bs_toolbar_tabs_html_container_start();
        
        do_action( 'wpbc_toolbar_top_tabs_before' , $menu_in_page_tag );
        
        $this->show_tabs_line( $menu_in_page_tag );                     // T O P    T A B S                              
        
        do_action( 'wpbc_toolbar_top_tabs_after' , $menu_in_page_tag );
        
        wpbc_bs_toolbar_tabs_html_container_end();
        
        
        $bottom_tabs = $this->get_all_sub_tabs_of_selected_tab( $menu_in_page_tag );

        if ( ! empty( $bottom_tabs ) ) {                                        // S U B    T A B S
                  
            wpbc_bs_toolbar_sub_html_container_start();
            
            $this->show_subtabs_line( $bottom_tabs, $menu_in_page_tag );   

            wpbc_bs_toolbar_sub_html_container_end();

        } // Bottom Tabs
                
        ?></span><?php
        
        return true;
    }
      
    
    /**
	 * Show Top nav TABs line
     * 
     * @param string $menu_in_page_tag - Menu Tag, the same as $this->in_page();
     */
    public function show_tabs_line( $menu_in_page_tag ) {

        foreach ( self::$nav_tabs[ $menu_in_page_tag ] as $tab_tag => $tab ) {
            
            $css_classes = ( ( isset($tab['css_classes']) ) ? $tab['css_classes'] : '' );

            if (  ( isset( $this->current_page_params['tab'] ) ) && ( $this->current_page_params['tab']['tag'] == $tab_tag )  )
                    $css_classes .= ' nav-tab-active';                     
            
            if ( ! empty( $tab['position'] ) ) 
                $css_classes .= ' nav-tab-position-' . $tab['position']; 
            
            if ( ! empty( $tab['hided'] ) ) 
                $css_classes .= ' hide'; 
            
            if (  ( isset( $tab['disabled'] ) ) && ( $tab['disabled'] )  )
                $css_classes .= ' wpdevelop-tab-disabled'; 

            $tab['css_classes'] = $css_classes;
            
            $tab['link'] = ( ! empty($tab['link']) ? $tab['link'] : $this->get_tab_url( $menu_in_page_tag, $tab_tag ) );            
                        
            if (                                                                // Recheck active status of default TAB   
                    ( isset( $_REQUEST[ $this->tags['tab'] ] ) )                    // Some Tab  selected                    
                && ( $_REQUEST[ $this->tags['tab'] ] !== $tab_tag )                 // This tag  not in URL
                && ( isset($tab['default']) && ( $tab['default'] ) )                                          // This tab default,  need to  set  it as not defaultm  for not showing it selected
               ) 
                $tab['default'] = false;
            
            wpbc_bs_display_tab( $tab );
            
        }        
    }
    
    
    /**
	 * Show Sub Menu Lines at the Settings page for Active Tab
     * 
     * @param array $bottom_tabs
     * @param string $menu_in_page_tag - Menu Tag, the same as $this->in_page();
     */
    public function show_subtabs_line( $bottom_tabs, $menu_in_page_tag ) {
        
        if ( ! empty( $bottom_tabs ) )
        foreach ( $bottom_tabs as $tab_tag => $tab ) {
            
            switch ( $tab['type'] ) {
                case 'separator':                           // Separator
                    ?><span class="wpdevelop-submenu-tab-separator-vertical"></span><?php    
                break;

                case 'button':                              // Button
                   ?><div class="clear-for-mobile"></div><input 
                            type="button" 
                            class="button button-primary wpbc_submit_button" 
                            value="<?php echo $tab['title']; ?>" 
                            onclick="if (typeof document.forms['<?php echo $tab['form']; ?>'] !== 'undefined'){ document.forms['<?php echo $tab['form']; ?>'].submit(); } else { wpbc_admin_show_message( '<?php echo ' <strong>Error!</strong> Form <strong>' , $tab['form'] , '</strong> does not exist.'; ?>', 'error', 10000 ); }"     <?php  //FixIn: 7.0.1.56 ?>
                            /><?php
                break;

                case 'html':                                                    // HTML
                    echo $tab['html'];
                break;
            
                case 'goto-link':
                    ?><a    class="nav-tab wpdevelop-submenu-tab go-to-link" 
                            original-title="<?php echo (empty($tab['hint'])?'':$tab['hint']); ?>" 
                            onclick="javascript:wpbc_scroll_to('#<?php echo esc_js( $tab['show_section'] ); ?>' );" 
                            href="javascript:void(0);"><span><?php echo $tab['title']; ?></span></a><?php
                break;
            
                default:                                    // Link
                    
                    $css_classes = ( ( isset($tab['css_classes']) ) ? $tab['css_classes'] : '' );
                    if ( ! empty($tab['position'] ) ) 
                        $css_classes .= ' nav-tab-position-'.$tab['position']; 
                    if ( $tab_tag ==  $this->current_page_params['subtab']['tag'] ) 
                        $css_classes .= ' wpdevelop-submenu-tab-selected'; 
                    if ( $tab['disabled'] ) 
                        $css_classes .= ' wpdevelop-submenu-tab-disabled'; 
                   
                    $tab['css_classes'] = $css_classes;
                    
                    $tab['link'] = ( ! empty($tab['link']) ? $tab['link'] : $this->get_tab_url( $menu_in_page_tag, $this->current_page_params['tab']['tag'], $tab_tag ) );
                    
                    $tab['top'] = false;                    
                    wpbc_bs_display_tab( $tab );
                     
                break;
                
            }   // End Switch
        } // End Bottom Tabs Loop                                                                   

    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Support
    ////////////////////////////////////////////////////////////////////////////
    
    /**
	 * Get URL of settings page, based on Page Slug and Tab Slug
     * 
     * @param string $page_tag
     * @param string $tab_name
     * @param string $subtab_name  ( Optional )
     * @return string - Escaped URL to plugin  page.
     */
    private function get_tab_url( $page_tag, $tab_name, $subtab_name = false ){
        if ( $subtab_name === false )
            return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $this->tags['tab'] => $tab_name ), 'admin.php' ) ) );
        else
            return esc_url( admin_url( add_query_arg( array( 'page' => $page_tag, $this->tags['tab'] => $tab_name, $this->tags['subtab'] => $subtab_name ), 'admin.php' ) ) );
    }
    
}