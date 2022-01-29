<?php
/**
 * Plugin Name: WPDB PHP Sessions
 * Description: Use $wpdb to store $_SESSION data.
 */

class RM_WPDB_Session_Handler {
	public static $instance = null;
	public static $config = null;
	public $version = 1;

	/**
	 * Open a session.
	 */
	public function open() {
		return true;
	}
        

	/**
	 * Close a session.
	 */
	public function close() {
		return true;
	}

	/**
	 * Read session data.
	 *
	 * @param sting $id Session id.
	 * @return mixed Session data or null.
	 */
	public function read($id) {
            global $wpdb;
            //error_reporting(E_ALL ^ E_WARNING);
            $data = $wpdb->get_var($wpdb->prepare("SELECT `data` FROM `{$wpdb->prefix}rm_sessions` WHERE id = %s", $id));
            //php 7.1 fix. read handler must return string empty or otherwise.
            if(!$data)
                return '';
            else
                return $data;
            //error_reporting(E_ALL);
         }

	/**
	 * Write a session.
	 *
	 * @param string $id Session id.
	 * @param string $data Session data (serialized for session storage).
	 */
	public function write( $id, $data ) {
		global $wpdb;
                if(empty($wpdb)){
                    return;
                }
		return (bool)$wpdb->query( $wpdb->prepare( "REPLACE INTO `{$wpdb->prefix}rm_sessions` VALUES ( %s, %s, %d );", $id, $data, time() ) );
	}

	/**
	 * Destroy a session.
	 *
	 * @param string $id Session id.
	 */
	public function destroy( $id ) {
		global $wpdb;
		return (bool) $wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}rm_sessions` WHERE `id` = %s;", $id ) );
	}

	/**
	 * Garbage collection.
	 */
	public function gc( $max ) {
		return true;
	}

	/**
	 * Compare versions and maybe run an upgrade routine.
	 */
	public function maybe_upgrade() {
		$current_version = (int) get_site_option( 'pj_wpdb_sessions_version', 0 );
		if ( version_compare( $this->version, $current_version, '>' ) )
			$this->do_upgrade( $current_version );
	}

	/**
	 * Perform an upgrade routine.
	 *
	 * @param int $current_version The version number from which to perform the upgrades.
	 */
	public function do_upgrade( $current_version ) {
		global $wpdb;
              //  echo $current_version; die;
		if ( $current_version < 1 ) {
			  
			$current_version = 1;
			update_site_option( 'pj_wpdb_sessions_version', $current_version );
		}
	}

	/**
	 * Cron-powered garbage collection.
	 */
	public static function cron_gc() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}rm_sessions` WHERE `timestamp` < %d;", time() - HOUR_IN_SECONDS * 24 ) );
	}

	/**
	 * If we have a global configuration, try and read it.
	 *
	 * @param array $defaults The default settings.
	 */
	public static function maybe_user_config( $defaults ) {
		if ( ! function_exists( 'pj_user_config' ) )
			return $defaults;

		$pj_user_config = pj_user_config();
		if ( empty( $pj_user_config['wpdb_sessions'] ) || ! is_array( $pj_user_config['wpdb_sessions'] ) )
			return $defaults;

		return wp_parse_args( $pj_user_config['wpdb_sessions'], $defaults );
	}

	/**
	 * Runs at the end of this script.
	 */
	public static function init() {
		self::$config = self::maybe_user_config( array(
            'enable' => true,
        ) );
                
		// Enable this plugin via a pj user config.
		if ( !self::$config['enable'] ) {
            return null;
        }

		if ( !self::$instance ) { 
			self::$instance = new RM_WPDB_Session_Handler;
			self::$instance->wpdb = $GLOBALS['wpdb']; 
			RM_Table_Tech::create_session_table();
            $gopts = new RM_Options;
            $session_policy= $gopts->get_value_of('session_policy');
            if(version_compare(PHP_VERSION, '5.4.0', '>=')){
                if($session_policy=='file' || (session_status()==PHP_SESSION_ACTIVE || session_status()==PHP_SESSION_NONE)){
                    return null;
                }
            }else{
                if($session_policy=='file' || session_id() == ''){
                    return null;
                }
            }
            
            session_set_save_handler(
				array( self::$instance, 'open' ),
				array( self::$instance, 'close' ),
				array( self::$instance, 'read' ),
				array( self::$instance, 'write' ),
				array( self::$instance, 'destroy' ),
				array( self::$instance, 'gc' )
			);
			register_shutdown_function( 'session_write_close' );
                        
			if (!wp_next_scheduled('pj_wpdb_sessions_gc') ){
				//wp_schedule_event( time(), 'hourly', 'pj_wpdb_sessions_gc' );
            }
            self::cron_gc();
			//add_action( 'pj_wpdb_sessions_gc', array( self::$instance, 'cron_gc' ) );
		}

		return self::$instance;
	}

	// No outsiders.
	public function __construct() {}
}

RM_WPDB_Session_Handler::init();