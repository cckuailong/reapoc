<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Widgets Class
 *
 * @link        http://presstigers.com
 * @since       2.4.3
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */
class Simple_Job_Board_Widgets
{

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.4.3
     */
    public function __construct() {
        
        // WP DYNAMO Core Recent Jobs Widget
        require_once plugin_dir_path(__FILE__) . 'class-simple-job-board-widgets-recent-jobs.php';
        
        // WP DYNAMO Core Register Widgets
        add_action('widgets_init', array($this, 'register_simple_job_board_widgets'));
    }
    
    /**
     * Register About Us Widget
     * 
     * @since   2.4.3
     */
    public function register_simple_job_board_widgets() {
        
        // Register Recent Jobs Widget
        register_widget('Simple_Job_Board_Widgets_Recent_Jobs');
    }
}
new Simple_Job_Board_Widgets();