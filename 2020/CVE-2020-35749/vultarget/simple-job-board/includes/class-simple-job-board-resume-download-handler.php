<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Resume_Download_Handler Class
 * 
 * Functions to download 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.4.3
 * @since       2.4.4   Fixed the "File not found error" & verified the authentication before resume download
 * @since       2.4.5   Resolved the resume downloading issue
 * @since       2.7.2   Resolved the compatibility issues with PHP 7.0*
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/includes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Resume_Download_Handler {

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     */
    public function __construct() {

        // Fire on Resume Download
        if ( isset( $_GET['resume_id'] ) || isset( $_GET['sjb_file'] ) ) {
            add_action('init', array( $this, 'download_resume' ) );
        }
    }

    /**
     * Download Resume
     * 
     * @since   2.4.3
     */
    public function download_resume() {

        // Check for User login & its capabilities
        $this->check_user_login();

        // Resume Path
        if (isset($_GET['resume_id'])) {
            $file_path = get_post_meta(intval($_GET['resume_id']), 'resume_path', TRUE);
        } elseif (isset($_GET['sjb_file'])) {

            // Get Multiple Attachments Path
            $files = get_post_meta(intval($_GET['post']), 'attachments_meta', TRUE);
            $file_path = $files['base_dir'] . '/' . esc_attr($_GET['sjb_file']);
        }
        
        if ( file_exists( $file_path ) ) {

            // Resume Name
            $filename = basename($file_path);

            if ( !$filename ) {
                wp_die(__('File not found', 'simple-job-board'), '', array('response' => 404));
            }

            // Set Server Configuration
            $this->set_server_config();

            // Clear all Buffers
            $this->clean_buffers();

            // Set the headers to prevent caching for the different browsers
            nocache_headers();

            // Download Headers
            header("X-Robots-Tag: noindex, nofollow", TRUE);
            header("Content-Type: " . $this->get_resume_content_type($file_path));
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
            header("Content-Transfer-Encoding: binary");

            // Get File Size & Set Content Header
            if ($size = @filesize($file_path)) {
                header("Content-Length: " . $size);
            }

            if (!$this->readfile_chunked($file_path)) {
                header('Location: ' . $file_path);
            }
        } else {
            wp_die(__('File not found', 'simple-job-board'), '', array('response' => 404));
        }

        exit;
    }   

    /**
     * Check login first before download file.
     * 
     * @access  private
     * @since   2.4.4
     */
    private function check_user_login() {
        
        if (!is_user_logged_in()) {
            wp_die( __('You must be logged in to download files.', 'simple-job-board') . ' <a href="' . esc_url(wp_login_url()) . '" >' . __('Login', 'simple-job-board') . '</a>', 403);
        } elseif (!current_user_can('download_resume' ) ) {
            wp_die(__('This is not your download link.', 'simple-job-board' ) . ' <a href="' . esc_url(get_admin_url()) . '" >' . __('Go to Dashboard', 'simple-job-board') . '</a>', 403);
        }
    }

    /**
     * Set Sever Config variables
     * 
     * Check and set certain server config variables to ensure downloads work as intended.
     * 
     * @since   2.4.3
     * @since   2.7.2 Removed 'get_magic_quotes_runtime' as it is deprecated since PHP 5.3 and removed since PHP 7.0
     */
    private function set_server_config() {
        $this->set_time_limit(0); // No Time Limit

        // Disable mod_deflate
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        @ini_set('zlib.output_compression', 'Off');

        // Write session data and end session
        @session_write_close();
    }

    /**
     * Clean all output buffers.
     *
     * Can prevent errors, for example: transfer closed with 3 bytes remaining to read.
     *
     * @since   2.4.3
     * 
     * @access private
     */
    private function clean_buffers() {
        if (ob_get_level()) {
            $levels = ob_get_level();
            for ($i = 0; $i < $levels; $i++) {
                @ob_end_clean();
            }
        } else {
            @ob_end_clean();
        }
    }

    /**
     * Set execution time to no limit
     * 
     * @since   2.4.3
     * @since   2.7.2 Removed 'safe_mode' as it has been depricated as of PHP 5.3.0 & removed as of PHP 5.4.0
     */
    private function set_time_limit($limit = 0) {
        if (function_exists('set_time_limit') && FALSE === strpos(ini_get('disable_functions'), 'set_time_limit')) {
            @set_time_limit($limit);
        }
    }

    /**
     * Get Content Type of Resume
     * 
     * @since   2.4.3
     * @access  private
     * 
     * @param   string  $file_path  Resume Path
     * @return  string
     */
    private function get_resume_content_type($file_path) {
        $file_extension = strtolower(substr(strrchr($file_path, "."), 1));
        $ctype = "application/force-download";

        // Checked for Allowed Mime Type
        foreach (get_allowed_mime_types() as $mime => $type) {
            $mimes = explode('|', $mime);

            if (in_array($file_extension, $mimes)) {
                $ctype = $type;
                break;
            }
        }

        return $ctype;
    }

    /**
     * readfile_chunked.
     *
     * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/.
     * 
     * @since   2.4.3
     * @access  private
     * 
     * @param   string $file
     * @return 	bool Success or fail
     */
    private function readfile_chunked($file) {
        $chunksize = 1024 * 1024;

        // Open Resume
        $handle = @fopen($file, 'r');

        if (false === $handle) {
            return FALSE;
        }

        while (!@feof($handle)) {
            echo @fread($handle, $chunksize);

            if (ob_get_length()) {
                ob_flush();
                flush();
            }
        }

        return @fclose($handle);
    }

}

new Simple_Job_Board_Resume_Download_Handler();