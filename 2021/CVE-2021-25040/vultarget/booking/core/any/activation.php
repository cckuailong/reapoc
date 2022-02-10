<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @category Installation 
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2015-04-09, 2016-03-17
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/** 
 * Activation | Deactivation Class  
 */
abstract class WPBC_Install {
    
    private $init_option;
    
    function __construct() {
        
        $default_init_option_names = array( 
                            'option-version_num'                   => 'booking_version_num'
                          , 'option-is_delete_if_deactive'         => 'booking_is_delete_if_deactive'
                          , 'option-activation_process'            => 'booking_activation_process'
                          , 'transient-wpbc_activation_redirect'   => '_booking_activation_redirect'
                          , 'message-delete_data'                  =>  '<strong>Warning!</strong> ' . 'All plugin data will be deleted when plugin had deactivated.' . '<br />'
                                                                      . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the', '<strong>', '</strong>')
                          , 'link_settings'                        => '<a href="">Settings</a>'
                          , 'link_whats_new'                       => '<a href="">Whats New</a>'            
                        ); 
        
        $init_option = $this->get_init_option_names();
        
        $this->init_option = wp_parse_args( $init_option, $default_init_option_names );

        
        register_activation_hook(   WPBC_FILE, array( $this, 'wpbc_activate_initial' ) );                  // WordPress > Plugins > "Activate" link.
        
        register_deactivation_hook( WPBC_FILE, array( $this, 'wpbc_deactivate' ) );                        // WordPress > Plugins > "Deactivate" link.
        
        add_filter('upgrader_post_install',    array( $this, 'wpbc_install_in_bulk_upgrade' ), 10, 2 );    // Upgrade during bulk upgrade of plugins
        
        // Settings link at the plugin page
        add_filter('plugin_action_links',   array( $this, 'plugin_links'), 10, 2 );
        // Warning message in plugin info 
        add_filter('plugin_row_meta',       array( $this, 'plugin_row_meta'), 10, 4 );        
        
        $this->check_if_need_to_update();                                                                  // Check upgrade, if was no activation process 
    }

    
    /**
	 * Must be overloaded in child CLASS
     * 
     * * Important! for correct loading of trasnaltions later, we must  do not use here loacale of plugin. So here will be untranslated strings!!!
     * 
     *  Exmaple:
     *         return  array(
                  'option-version_num'                   => 'booking_version_num'
                , 'option-is_delete_if_deactive'         => 'booking_is_delete_if_deactive'
                , 'option-activation_process'            => 'booking_activation_process'
                , 'transient-wpbc_activation_redirect'   => '_booking_activation_redirect'
                , 'message-delete_data'                  =>  '<strong>Warning !!!</strong> ' . 'All plugin data will be deleted when plugin had deactivated.' . '<br />'
                                                            . sprintf( 'If you want to save your plugin data, please uncheck the %s"Delete plugin data"%s at the settings page.', '<strong>', '</strong>')
                , 'link_settings'                        => '<a href="">Settings</a>'
                , 'link_whats_new'                       => '<a href="">Whats New</a>'            
        );
     */
    abstract function get_init_option_names();

    
    /**
	 * Must be overloaded in child CLASS
     *  Exmaple:
     * 
        return false
    */
    abstract function is_update_from_lower_to_high_version();

    
    ////////////////////////////////////////////////////////////////////////////

    
    // <editor-fold defaultstate="collapsed" desc="    Update info of plugin at the plugins section   ">
    
    /** Update info of plugin at the plugins section */
    function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $context ) {

        $this_plugin = plugin_basename( WPBC_FILE );

        if ($plugin_file == $this_plugin ) {

            $is_delete_if_deactive =  get_bk_option( $this->init_option['option-is_delete_if_deactive'] ); // check

            if ($is_delete_if_deactive == 'On') { ?>
                <div class="plugin-update-tr">
                    <div class="update-message notice inline notice-warning notice-altNO" style="font-size: 1em;line-height: 2em;margin:0 5px 10px;"><?php echo $this->init_option['message-delete_data']; ?></div>
                </div>
                <?php
            }

            /*
            [$plugin_meta] => Array
                (
                    [0] => Version 2.8.35
                    [1] => By wpdevelop
                    [2] => Visit plugin site
                )

            [$plugin_file] => booking/WPBC.php
            [$plugin_data] => Array
                (
                    [Name] => Booking Calendar
                    [PluginURI] => https://wpbookingcalendar.com/demo/
                    [Version] => 2.8.35
                    [Description] => Online booking and availability checking service for your site.
                    [Author] => wpdevelop
                    [AuthorURI] => https://wpbookingcalendar.com/
                    [TextDomain] =>
                    [DomainPath] =>
                    [Network] =>
                    [Title] => Booking Calendar
                    [AuthorName] => wpdevelop
                )

            [$context] => all
            /**/

            // Echo plugin description here
            return $plugin_meta;
            
        } else     
            return $plugin_meta;
    }


    // Adds Settings link to plugins settings
    function plugin_links($links, $file) {

        $this_plugin = plugin_basename( WPBC_FILE );

        if ( $file == $this_plugin ) {
            
            array_unshift( $links, $this->init_option['link_settings'] );
            
            array_unshift( $links, $this->init_option['link_whats_new'] );
        }
        return $links;
    }
    
    // </editor-fold>
    
    
    ////////////////////////////////////////////////////////////////////////////
    
    
    // Check  about ability to upgrade, if was no activation  process
    private function check_if_need_to_update() {
        
        if( is_admin() ) {

            $wpbc_version_num = get_option( $this->init_option['option-version_num'] );        

            if ($wpbc_version_num === false ) 
                $wpbc_version_num = '0';

            $is_make_activation = false;
            
            if ( version_compare( WP_BK_VERSION_NUM, $wpbc_version_num) > 0 ) {

                $is_make_activation = true;

            } else {    
            
                // Check if we was update from free to paid or from lower to higher versions,  
                // and do not make normal activation. In this case we need to make update.
                $is_make_activation = $this->is_update_from_lower_to_high_version(); 
                
            }
            
            // Add hook  for initial activation.
            if ( $is_make_activation ) {
                add_action( 'plugins_loaded', array( $this, 'wpbc_activate_initial' ) , 1030 );
            }                        
        } 
    }
    
    
    /**
	 * Upgrade during bulk upgrade of plugins
     * 
     * @param type $return
     * @param type $hook_extra
     * @return type
     */
    public function wpbc_install_in_bulk_upgrade( $return, $hook_extra ){

        if ( is_wp_error( $return ) )
                return $return;

        if ( isset( $hook_extra ) )
            if ( isset( $hook_extra['plugin'] ) ) {
                $file_name = basename( WPBC_FILE );
                $pos = strpos( $hook_extra['plugin'],  trim( $file_name ) );
                if ( $pos !== false ) {
                    $this->wpbc_activate();
                }
            }
        return $return;                
    }

    
    /**
	 * User clicked on "Activate" link at Plugins Menu.
     * 
     * @return type
     */
    public function wpbc_activate_initial(){                                    

        // Activate the plugin
        $this->wpbc_activate();

        // Bail if this demo or activating from network, or bulk
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) || wpbc_is_this_demo() )
                return;

        // Add the transient to redirect - Showing Welcome screen
        set_transient( $this->init_option['transient-wpbc_activation_redirect'], true, 30 );                         
    }

    
    ////////////////////////////////////////////////////////////////////////////
   
    
    /** Run Activate */
    public function wpbc_activate() {

    	//FixIn: 8.4.7.24
	    if ( function_exists( 'set_time_limit' ) ) {
		    set_time_limit( 900 );
	    }
        
        ini_set('memory_limit','256M');                                         //FixIn:6.1.1.15
        ini_set('max_execution_time', 300);                                     //FixIn: 7.0.1.57
        
        update_bk_option( $this->init_option['option-activation_process'], 'On' );        

        make_bk_action( 'wpbc_activation' );                                    //  S T A R T

        update_bk_option( $this->init_option['option-version_num'], WP_BK_VERSION_NUM );

        update_bk_option( $this->init_option['option-activation_process'], 'Off');
    }


    /** Run Deactivate */
    public function wpbc_deactivate() {

    	//FixIn: 8.4.7.24
	    if ( function_exists( 'set_time_limit' ) ) {
		    set_time_limit( 900 );
	    }

        ini_set('memory_limit','256M');                                         //FixIn:6.1.1.15
        ini_set('max_execution_time', 300);                                     //FixIn: 7.0.1.57

        $is_delete_if_deactive =  get_bk_option( $this->init_option['option-is_delete_if_deactive'] ); // check

        if ( $is_delete_if_deactive == 'On' ) {

            make_bk_action( 'wpbc_deactivation' );                              //  F I N I S H
            
            delete_bk_option( $this->init_option['option-version_num'] );
            
            delete_bk_option( $this->init_option['option-activation_process'] );
        }
    }

}