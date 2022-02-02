<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_Web_Services
{

    /**
     * init ajax actions
     */
    public static function init()
    {
        add_action('wp_ajax_duplicator_reset_all_settings', array(__CLASS__, 'ajax_reset_all'));
        add_action('wp_ajax_duplicator_download', array(__CLASS__, 'duplicator_download'));
        add_action('wp_ajax_nopriv_duplicator_download', array(__CLASS__, 'duplicator_download'));
    }

    /**
     *
     * @param DUP_Package $package
     */
    public static function package_delete_callback($package)
    {
        $package->delete();
    }

    /**
     * reset all ajax action
     *
     * the output must be json
     */
    public static function ajax_reset_all()
    {
        ob_start();
        try {
            DUP_Handler::init_error_handler();

            if (!check_ajax_referer('duplicator_reset_all_settings', 'nonce', false)) {
                DUP_LOG::Trace('Security issue');
                throw new Exception('Security issue');
            }
            DUP_Util::hasCapability('export', DUP_Util::SECURE_ISSUE_THROW);

            /** Execute function * */
            $error  = false;
            $result = array(
                'data' => array(),
                'html' => '',
                'message' => ''
            );

            $nonce = sanitize_text_field($_POST['nonce']);
            if (!wp_verify_nonce($nonce, 'duplicator_reset_all_settings')) {
                DUP_Log::trace('Security issue');
                throw new Exception('Security issue');
            }

            DUP_Package::by_status_callback(array(__CLASS__,'package_delete_callback'),array(
                    array('op' => '<', 'status' => DUP_PackageStatus::COMPLETE)
            ));

            /** reset active package id * */
            DUP_Settings::Set('active_package_id', -1);
            DUP_Settings::Save();

            /** Clean tmp folder * */
            DUP_Package::not_active_files_tmp_cleanup();

            //throw new Exception('force error test');
        } catch (Exception $e) {
            $error             = true;
            $result['message'] = $e->getMessage();
        }

        /** Intercept output * */
        $result['html'] = ob_get_clean();

        /** check error and return json * */
        if ($error) {
            wp_send_json_error($result);
        } else {
            wp_send_json_success($result);
        }
    }

    public static function duplicator_download() {
        $file = sanitize_text_field($_GET['file']);
        $filepath = DUPLICATOR_SSDIR_PATH.'/'.$file;
        // Process download
        if(file_exists($filepath)) {
            // Clean output buffer
            if (ob_get_level() !== 0 && @ob_end_clean() === FALSE) {
                @ob_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer

            try {
                $fp = @fopen($filepath, 'r');
                if (false === $fp) {
                    throw new Exception('Fail to open the file '.$filepath);
                }
                while (!feof($fp) && ($data = fread($fp, DUPLICATOR_BUFFER_READ_WRITE_SIZE)) !== FALSE) {
                    echo $data;
                }
                @fclose($fp);
            } catch (Exception $e) {
                readfile($filepath);
            }
            exit;
        } else {
            wp_die('Invalid installer file name!!');
        }
    }
}