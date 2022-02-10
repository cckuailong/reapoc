<?php /**
 * @version 1.0
 * @package 
 * @category Core
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2013.10.16
 */

class WPBC_CSS extends WPBC_JS_CSS{

    public function define() {
        
        $this->setType('css');
        
        /*
        // Exmaples of usage Font Avesome: http://fontawesome.io/icons/
        
        $this->add( array(
                            'handle' => 'font-awesome',
                            'src' => WPBC_PLUGIN_URL . 'assets/libs/font-awesome-4.3.0/css/font-awesome.css' ,
                            'deps' => false,
                            'version' => '4.3.0',
                            'where_to_load' => array( 'admin' ),
                            'condition' => false    
                  ) );   
        
        // Exmaples of usage Font Avesome 3.2.1 (benefits of this version - support IE7): http://fontawesome.io/3.2.1/examples/ 
        $this->add( array(
                            'handle' => 'font-awesome',
                            'src' => WPBC_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome.css' ,
                            'deps' => false,
                            'version' => '3.2.1',
                            'where_to_load' => array( 'admin' ),
                            'condition' => false    
                  ) );            
        $this->add( array(
                            'handle' => 'font-awesome-ie7',
                            'src' => WPBC_PLUGIN_URL . '/assets/libs/font-awesome/css/font-awesome-ie7.css' ,
                            'deps' => array('font-awesome'),
                            'version' => '3.2.1',
                            'where_to_load' => array( 'admin' ),
                            'condition' => 'IE 7'                               // CSS condition. Exmaple: <!--[if IE 7]>    
                  ) );  
        */
          
    }


    public function enqueue( $where_to_load ) {        
        
        wp_enqueue_style('wpdevelop-bts',       wpbc_plugin_url( '/assets/libs/bootstrap/css/bootstrap.css' ),          array(), '3.3.5.1');
        wp_enqueue_style('wpdevelop-bts-theme', wpbc_plugin_url( '/assets/libs/bootstrap/css/bootstrap-theme.css' ),    array(), '3.3.5.1');
                   
        if ( $where_to_load == 'admin' ) {                                                                                                      // Admin CSS files            

            wp_enqueue_style('wpbc-chosen',                 wpbc_plugin_url( '/assets/libs/chosen/chosen.css' ),        array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-support',         wpbc_plugin_url( '/core/any/css/admin-support.css' ),       array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-menu',            wpbc_plugin_url( '/core/any/css/admin-menu.css' ),          array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-admin-toolbar',         wpbc_plugin_url( '/core/any/css/admin-toolbar.css' ),       array(), WP_BK_VERSION_NUM);
            wp_enqueue_style( 'wpbc-settings-page',         wpbc_plugin_url( '/core/any/css/settings-page.css' ),       array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-listing-table',   wpbc_plugin_url( '/core/any/css/admin-listing-table.css' ), array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-br-table',              wpbc_plugin_url( '/core/any/css/admin-br-table.css' ),      array(), WP_BK_VERSION_NUM);                        
            wp_enqueue_style( 'wpbc-admin-modal-popups',    wpbc_plugin_url( '/css/modal.css' ),                        array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-pages',           wpbc_plugin_url( '/css/admin.css' ),                        array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-skin',            wpbc_plugin_url( '/css/admin-skin.css' ),                   array( 'wpbc-admin-pages' ), WP_BK_VERSION_NUM);            //FixIn: 8.0.2.4
            wp_enqueue_style( 'wpbc-css-print',             wpbc_plugin_url( '/css/print.css' ),                        array(), WP_BK_VERSION_NUM);

			global $wp_version;

			if (    ( version_compare( $wp_version, '5.3', '>=' ) )
			     || ( version_compare( $wp_version, '5.3-RC2-46574', '>=' ) )
			){
				/* The SVG is arrow-down-alt2 from Dashicons. */
				$css      = "
					.wp-core-ui .wpdevelop .control-group .btn-toolbar .input-group > select,
					.wp-core-ui .wpdevelop select.form-control {
						background: #fff url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%206l5%205%205-5%202%201-7%207-7-7%202-1z%22%20fill%3D%22%23555%22%2F%3E%3C%2Fsvg%3E') no-repeat right 5px top 55%;
					    padding: 2px 30px 2px 10px;
					}
				";
				wp_add_inline_style( 'wpbc-admin-pages', $css );
			}

        }         
        if (  ( $where_to_load != 'admin' ) || ( wpbc_is_new_booking_page() )  ){                                                               // Client or Add New Booking page

			if ( 'On' === get_bk_option( 'booking_timeslot_picker' ) ) {                                                //FixIn: 8.7.11.10
				wp_enqueue_style( 'wpbc-time_picker',        wpbc_plugin_url( '/css/wpbc_time-selector.css' ),          array(), WP_BK_VERSION_NUM );

		        $time_picker_skin_path = wpbc_get_time_picker_skin_url();
		        if ( ! empty( $time_picker_skin_path ) ) {
		        	wp_enqueue_style('wpbc-time_picker-skin', $time_picker_skin_path ,                                  array('wpbc-time_picker'), WP_BK_VERSION_NUM);
		        }
			}

            wp_enqueue_style( 'wpbc-client-pages',          wpbc_plugin_url( '/css/client.css' ),                       array(), WP_BK_VERSION_NUM );
        }

        //FixIn: 8.6.1.13

        // wp_enqueue_style('wpbc-datepicker-main',   wpbc_plugin_url( '/js/datepick.5.1/css/jquery.datepick.css' ),           array(), WP_BK_VERSION_NUM);

        wp_enqueue_style('wpbc-calendar',   wpbc_plugin_url( '/css/calendar.css' ),                                     array(), WP_BK_VERSION_NUM);        
                                                                                                                                                // Calendar Skins
        $calendar_skin_path = wpbc_get_calendar_skin_url();
        if ( ! empty( $calendar_skin_path ) )
            wp_enqueue_style('wpbc-calendar-skin', $calendar_skin_path ,                                                array(), WP_BK_VERSION_NUM);
    
        do_action( 'wpbc_enqueue_css_files', $where_to_load );        
    }


    public function remove_conflicts( $where_to_load ) {

    	//FixIn: 8.1.3.12
        if (
        	     wpbc_is_bookings_page()
        	  || wpbc_is_new_booking_page()
        	  || wpbc_is_resources_page()
        	  || wpbc_is_settings_page()
           ) {
            if (function_exists('wp_dequeue_style')) {
                /*
                wp_dequeue_style( 'cs-alert' );
                wp_dequeue_style( 'cs-framework' );
                wp_dequeue_style( 'cs-font-awesome' );
                wp_dequeue_style( 'icomoon' );           
                */            
                wp_dequeue_style( 'chosen'); 
                wp_dequeue_style( 'toolset-font-awesome-css' );                               // Remove this script sitepress-multilingual-cms/res/css/font-awesome.min.css?ver=3.1.6, which is load by the "sitepress-multilingual-cms"
                wp_dequeue_style( 'toolset-font-awesome' );                          //FixIn: 5.4.5.8
                wp_dequeue_style( 'the7-fontello-css' );
					wp_dequeue_style( 'dt-awsome-fonts-back-css' );                 //FixIn: 8.2.1.10           fix conflict  with https://the7.io/
	                wp_dequeue_style( 'dt-awsome-fonts-css' );
	                wp_dequeue_style( 'dt-fontello-css' );
                wp_dequeue_style( 'cs_icons_data_css_default');                         //FixIn: 8.1.3.12
	            wp_dequeue_style( 'icons-style' );                                      //FixIn: 8.2.1.22
	            wp_dequeue_style( 'fontawesome-style' );                                //FixIn: 8.2.1.22
	            wp_dequeue_style( 'bootstrap-style' );                                  //FixIn: 8.2.1.22
	            wp_dequeue_style( 'bootstrap-theme-style' );                            //FixIn: 8.2.1.22

            } 
        }
    }
}


/**
 * Get URL to  Calendar Skin ( CSS file )
 *
 * @return string - URL to  calendar skin
 */
function wpbc_get_calendar_skin_url() {
    
    // Calendar Skin ///////////////////////////////////////////////////////
    $calendar_skin_path = false;

    //FixIn: 8.7.11.11
	$check_skin_path = get_bk_option( 'booking_skin' );
	if ( false !== strpos( $check_skin_path, 'inc/skins/' ) ) {
		$check_skin_path = str_replace( 'inc/skins/', 'css/skins/', $check_skin_path );
		update_bk_option( 'booking_skin', $check_skin_path );
	}

    // Check if this skin exist in the plugin  folder //////////////////////
    if ( file_exists( WPBC_PLUGIN_DIR . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_skin') ) ) ) {
        $calendar_skin_path = WPBC_PLUGIN_URL . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_skin') );
    }

    // Check  if this skin exist  int he Custom User folder at  the http://example.com/wp-content/uploads/wpbc_skins/
    $upload_dir = wp_upload_dir();
    $custom_user_skin_folder = $upload_dir['basedir'] ;
    $custom_user_skin_url    = $upload_dir['baseurl'] ;
	//$custom_user_skin_url = str_replace( 'http:', 'https:', $custom_user_skin_url );

    if ( file_exists( $custom_user_skin_folder . str_replace(  array( WPBC_PLUGIN_URL , $custom_user_skin_url ), '', get_bk_option( 'booking_skin') ) ) ) {
        $calendar_skin_path = $custom_user_skin_url . str_replace( array(WPBC_PLUGIN_URL, $custom_user_skin_url ), '', get_bk_option( 'booking_skin') );
    }

    return $calendar_skin_path;
}

//FixIn: 8.7.11.10
function wpbc_get_time_picker_skin_url(){

	// time_picker Skin ///////////////////////////////////////////////////////
	$time_picker_skin_path = false;

	// Just  default value,  if previously  was not saved any  options.
	if ( empty( get_bk_option( 'booking_timeslot_picker_skin' ) ) ) {
		update_bk_option( 'booking_timeslot_picker_skin', '/css/time_picker_skins/grey.css' );
	}

    // Check if this skin exist in the plugin  folder //////////////////////
    if ( file_exists( WPBC_PLUGIN_DIR . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_timeslot_picker_skin') ) ) ) {
        $time_picker_skin_path = WPBC_PLUGIN_URL . str_replace( WPBC_PLUGIN_URL, '', get_bk_option( 'booking_timeslot_picker_skin') );
    }

    // Check  if this skin exist  int he Custom User folder at  the http://example.com/wp-content/uploads/wpbc_skins/
    $upload_dir = wp_upload_dir();
    $custom_user_skin_folder = $upload_dir['basedir'] ;
    $custom_user_skin_url    = $upload_dir['baseurl'] ;
    if ( file_exists( $custom_user_skin_folder . str_replace(  array( WPBC_PLUGIN_URL , $custom_user_skin_url ), '', get_bk_option( 'booking_timeslot_picker_skin') ) ) ) {
        $time_picker_skin_path = $custom_user_skin_url . str_replace( array(WPBC_PLUGIN_URL, $custom_user_skin_url ), '', get_bk_option( 'booking_timeslot_picker_skin') );
    }

    return $time_picker_skin_path;
}