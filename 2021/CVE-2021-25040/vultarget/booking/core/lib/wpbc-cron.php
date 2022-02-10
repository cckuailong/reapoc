<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage CRON
 * @category Execure Recurent Actions
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2014.09.02
 * @since 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


/*

    // A D D
    WPBC()->cron->update( 'wpbc_import_gcal'                                       // Name of Cron event
                                , array(     
                                       'action' => array( 'wpbc_silent_import_all_events' )     // {REQUIRED} Name of Action Hook  and possible parameters. Before you need to add this action:  add_bk_action('wpbc_silent_import_all_events' , 'wpbc_silent_import_all_events' ); 
                                     , 'start_time' => time()                                   // Start  time of Execution. Default = Now
                                     , 'recurrence' => 2                                        // Set time interval in Hours for repeat of execution.  Default = 24
                                     , priority => 10                                           // Priority  of actions execution. Default = 10. Lower priority - execution  firstly.
                                        ) 
                                     );
      
    // D E L E T E   specific Cron
    WPBC()->cron->delete( 'wpbc_import_gcal' );                    // Name of Cron action

*/

class WPBC_Cron {

    private $actions;

    
    function __construct() {
        
        $this->actions = array();
        
        add_action('init', array($this, 'load'), 9 ); 
        add_bk_action('wpbc_other_versions_deactivation', array(&$this, 'deactivate'));
    }

    
    public function load(){
        $booking_cron = get_bk_option('booking_cron');

        if ( $booking_cron === false ) 
            $booking_cron = array();                
        else {
            if ( is_serialized( $booking_cron ) ) {
                $booking_cron = unserialize($booking_cron);                    
            } 
        }
//debuge($booking_cron);        
        $this->actions = $booking_cron;
        
        if ( ! empty( $this->actions ) )
            $this->check();
    }


    public function check(){
        
        

        // Sort by priority of execution
        $priority = array();
        foreach ($this->actions as $action_name => $action) {
            
            if ( ! isset( $priority[ $action['priority'] ] ) ) {
                $priority[ $action['priority'] ] = array();
            }
            
            $priority[ $action['priority'] ][ $action_name ] = $action;
            
        }
        ksort($priority);
        
        // check  for execution  based on priority
        foreach ($priority as $actions_list) {
            foreach ($actions_list as $action_name => $action) {
                
//debuge(time(), $action);  
              
                //1. Check for start time
                if ( $action['start_time'] > time() ) 
                    continue;
                
                //2. Get and check  for next  time execution 
                $next_time_execution = intval( $action['last_execution'] ) + intval( $action['recurrence'] )  * 60 * 60;  // number of hours    //FixIn: 8.4.5.2
                
                if  ( $next_time_execution > time() ) 
                    continue;
                                
                
                // Update last  time executed time
                $action['last_execution'] = time() + intval( $action['recurrence'] )  * 60 * 60; // number of hours     //FixIn: 8.4.5.2
                
                $this->update($action_name, $action);

                // Execute
                $this->action($action_name);
                
                
//debuge('Cron Checking', $action_name, $action);                
            }
        }
         
    }

    

    /* Example:
     *     $wpbc_cron->add('wpbc_import', array(     
                                    'action' => array( 'wpbc_silent_import_all_events' )    // Action and parameters
                                    , 'start_time' => time()                                // Now
                                    , 'recurrence' => 1                                     // each  24 hours
                                    ));
     */
    
    // Add Paramters
    public function add($action_name, $action_params) {
        if (! isset( $this->actions[$action_name] ) ) {

            if (! isset($action_params['action']))
                return  false;
            
            $defaults =  array(
                                'start_time' => time()     // GMT time, when to start this action  - Now    
                              , 'recurrence' => 24         // Each  24 hours
                              , 'priority' => 10           // Set priority  of actions execution  at the same time
                              , 'last_execution' => ''     // Set  last  time execution 
                              ) ;
            $args = wp_parse_args( $action_params, $defaults );
            
            $this->actions[$action_name] = $args;
            
            
            // Update to DB
            $booking_cron = get_bk_option('booking_cron');
            
            if ( $booking_cron === false ) 
                $booking_cron = array();                
            else {
                if ( is_serialized( $booking_cron ) ) {
                    $booking_cron = unserialize($booking_cron);                    
                } 
            }
            $booking_cron[ $action_name] =  $args;

            update_bk_option( 'booking_cron', $booking_cron );
            
            return  true;
        } else 
            return  false;
    }
 
    
    // Update Paramters
    public function update($action_name, $action_params) {
        if ( isset( $this->actions[$action_name] ) ) {
            
            $args = wp_parse_args( $action_params, $this->actions[$action_name] );
            
            $this->actions[$action_name] = $args;
            
            
            // Update to DB
            $booking_cron = get_bk_option('booking_cron');
            
            if ( $booking_cron === false ) 
                $booking_cron = array();                
            else {
                if ( is_serialized( $booking_cron ) ) {
                    $booking_cron = unserialize($booking_cron);                    
                } 
            }
            $booking_cron[ $action_name] =  $args;
            ////////////////////////////////////////////////////////////////////
            
            update_bk_option( 'booking_cron' ,   $booking_cron );
            return  true;
        } else 
            $this->add($action_name, $action_params);
    }
 
    
    public function delete($action_name) {
        
        if ( isset( $this->actions[$action_name] ) ) {
            unset( $this->actions[$action_name] );
        }
        
        // Update to DB
        $booking_cron = get_bk_option('booking_cron');

        if ( $booking_cron === false ) 
            $booking_cron = array();                
        else {
            if ( is_serialized( $booking_cron ) ) {
                $booking_cron = unserialize($booking_cron);                    
            } 
            if ( isset( $booking_cron[ $action_name] ) ) 
                unset( $booking_cron[ $action_name] );
        }
        
        ////////////////////////////////////////////////////////////////////

        update_bk_option( 'booking_cron' ,   $booking_cron );
        
    }
    
 
    private function action($action_name) {
        
        // Description:
        // 
        // This type of execution  action  add posibility to use not only name of action  
        // 
        // 'action' => array( 'wpbc_silent_import_all_events' )                 // Action and parameters
        // 
        // but also parameters
        // 
        // 'action' => array( 'wpbc_silent_import_all_events', 2, 'test' )                 // Action and parameters
        // 
        if ( isset( $this->actions[$action_name] ) )
            call_user_func_array('make_bk_action', $this->actions[$action_name]['action'] ); 
            
        // Simple Action execution without parameters.
        //make_bk_action( $this->actions[$action_name]['action'][0] );   
        
    }
    
    
    // Deactivation  of the plugin - Delete this option.
    public function deactivate(){
        delete_bk_option( 'booking_cron' );
    }
}
?>