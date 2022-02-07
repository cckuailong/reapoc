<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Rewrite Class
 * 
 * This is used to define the job board rewrite rules. These rewrite rules prevent
 * the hotlinking of resumes & also force resumes to download rather than 
 * opening in browser.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.1.0
 * @since       2.2.3   Updated anti-hotlinking rules specific to uploads/jobpost.
 * @since       2.4.3   Removed the anti-hotlinking rules specific to uploads/jobpost.
 * 
 * @package    Simple_Job_Board
 * @subpackage Simple_Job_Board/includes
 * @author     PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Rewrite {

    /**
     * job_board_rewrite function.
     * 
     * @since   2.1.0
     */
    public function job_board_rewrite() {
        if (!function_exists('get_home_path')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        // Home Path
        $root_path = get_home_path();
        $file_existing_permission = '';

        $uploads_dir = wp_upload_dir();

        // Rules for Download Files Forcefully
        $forcedownload_rule = "AddType application/octet-stream .pdf .txt\n";

        // Changing File to Writable Mode
        if (file_exists($root_path . '.htaccess') && !is_writable($root_path . '.htaccess')) {
            $file_existing_permission = substr(decoct(fileperms($root_path . '.htaccess')), -4);
            chmod($root_path . '.htaccess', 0777);
        }

        // Appending rules in .htaccess
        if (file_exists($root_path . '.htaccess') && is_writable($root_path . '.htaccess')) {

            $forcedownload_rule = explode("\n", $forcedownload_rule);

            // Anti-Hotlinking Rules Writing in .htaccess file
            if (!function_exists('insert_with_markers')) {
                require_once( ABSPATH . 'wp-admin/includes/misc.php' );
            }

            // Remove Hotlinking Rules
            insert_with_markers($root_path . '.htaccess', 'Hotlinking', '');

            /* Revert File Permission  */
            if (!empty($file_existing_permission)) {
                chmod( $root_path . '.htaccess', $file_existing_permission );
            }
        }

        $file = array(
            'basedir' => $uploads_dir['basedir'] . '/jobpost',
            'file' => '.htaccess',
            'rules' => 'deny from all',
        );
        
        // Protect resume files from hotlinking
        if (wp_mkdir_p($file['basedir']) && !file_exists(trailingslashit($file['basedir']) . $file['file'])) {
            if ($file_handle = @fopen(trailingslashit($file['basedir']) . $file['file'], 'w')) {
                fwrite($file_handle, $file['rules']);
                fclose($file_handle);
            }
        }
    }

}

new Simple_Job_Board_Rewrite();