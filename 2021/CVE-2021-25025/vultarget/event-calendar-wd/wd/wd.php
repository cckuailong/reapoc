<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    class TenWebLib {
        ////////////////////////////////////////////////////////////////////////////////////////
        // Events                                                                             //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constants                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Variables                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////
        public static $instance;          
        public $overview_instance;  
        public $subscribe_instance;   
        public $config;
        private $version = "1.1.1";
				
        ////////////////////////////////////////////////////////////////////////////////////////
        // Constructor & Destructor                                                           //
        ////////////////////////////////////////////////////////////////////////////////////////
        public function __construct() {
            // Add menu for Overview page
            add_action( 'admin_menu', array( $this, 'wd_overview_menu_page' ), 10 ); 

        }
        ////////////////////////////////////////////////////////////////////////////////////////
        // Public Methods                                                                     //
        ////////////////////////////////////////////////////////////////////////////////////////

        // Init plugin data
        public function wd_init( $options ) {

            if(!is_array($options)){
                return false;
            }
            $config = new TenWebLibConfig();
            $config->set_options( $options );
            $this->config = $config;
            if( !class_exists("TenWebLibApi") ){
                $this->wd_includes();
            }

			$this->init_classes();
			$this->register_hooks();

        }

        // Create overview menu page
        public function wd_overview_menu_page() {
            $wd_options =  $this->config;

            $capability = $wd_options->menu_capability ? $wd_options->menu_capability : "manage_options";
            if( get_option( $wd_options->prefix . "_subscribe_done" ) == 1 || $wd_options->subscribe === false ){
                    $parent_slug = $wd_options->custom_post;            
            }
            else{

                $subscribe_page = add_menu_page( $wd_options->plugin_menu_title, $wd_options->plugin_menu_title, "manage_options", $wd_options->prefix . '_subscribe' , array( $this, 'display_subscribew_page' ), $wd_options->plugin_menu_icon, $wd_options->menu_position );

                $subscribe_instance = new TenWebLibSubscribe($this->config);
                $this->subscribe_instance = $subscribe_instance;        
                add_action( 'admin_print_styles-' . $subscribe_page, array( $subscribe_instance, 'subscribe_styles' ) );
                add_action( 'admin_print_scripts-' . $subscribe_page, array( $subscribe_instance, 'subscribe_scripts' ) );
                
                $parent_slug = null;                
            }
            if ($wd_options->display_overview) {
              $title = __( 'Premium', $wd_options->prefix );
              if ( FALSE && !get_transient( $wd_options->prefix . '_overview_visited' ) ) {
                $title .= ' <span class="update-plugins count-2" > <span class="plugin-count">1</span></span>';
              }
              $overview_page = add_submenu_page( $parent_slug, __( 'Premium', $wd_options->prefix ), '<span style="color:#4481ea;">' . $title . '</span>', $capability, 'overview_' . $wd_options->prefix, array( $this, 'display_overview_page' ) );


              $overview_instance = new TenWebLibOverview( $this->config );
              $this->overview_instance = $overview_instance;
              add_action( 'admin_print_styles-' . $overview_page, array( $overview_instance, 'overview_styles' ) );
              add_action( 'admin_print_scripts-' . $overview_page, array( $overview_instance, 'overview_scripts' ) );
            }
        }
		
		public function display_subscribew_page(){
			$this->subscribe_instance->subscribe_display_page();
		}
        
        // Display overview page
        public function display_overview_page() {
			$this->overview_instance->display_overview_page();
       }
       
       
	   // Includs
	    public function wd_includes(){
            $wd_options =  $this->config;

            require_once $wd_options->wd_dir_includes . '/deactivate.php' ;
            // notices
            require_once $wd_options->wd_dir_includes . '/api.php';
            require_once $wd_options->wd_dir_includes . '/notices.php';
            require_once $wd_options->wd_dir_includes . "/overview.php";
            require_once $wd_options->wd_dir_includes . "/subscribe.php";
                       
        }
        public function init_classes(){
            $wd_options =  $this->config;

            $current_url =  $_SERVER['REQUEST_URI'];
            if( $wd_options->deactivate === true ){
                if(strpos( $current_url, "plugins.php" ) !== false ){   
                    new TenWebLibDeactivate( $this->config );
                }                
            }           
            
            new TenWebLibNotices( $this->config );

        }
		
		public function register_hooks(){
            $wd_options =  $this->config; 
            if( $wd_options->deactivate === true ){       
                add_filter( 'plugin_action_links_' . plugin_basename( $wd_options->plugin_main_file ),  array( $this, 'change_deactivation_link' ) );
            }
            		
		}


		public function change_deactivation_link ( $links ) {
            $wd_options =  $this->config;
      $deactivate_url =
        add_query_arg(
          array(
            'action' => 'deactivate',
            'plugin' => plugin_basename( $wd_options->plugin_main_file ),
            '_wpnonce' => wp_create_nonce( 'deactivate-plugin_' . plugin_basename( $wd_options->plugin_main_file ) )
          ),
          admin_url( 'plugins.php' )
        );

      $links["deactivate"] = '<a href="'.$deactivate_url.'" class="' . $wd_options->prefix . '_deactivate_link">Deactivate</a>';
			return  $links;
		}
      		
        ////////////////////////////////////////////////////////////////////////////////////////
        // Getters & Setters                                                                  //
        ////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        // Private Methods                                                                    //
        ////////////////////////////////////////////////////////////////////////////////////////
        
        ////////////////////////////////////////////////////////////////////////////////////////
        // Listeners                                                                          //
        ////////////////////////////////////////////////////////////////////////////////////////

    }



