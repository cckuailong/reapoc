<?php

class RM_Attachment_Service extends RM_Services {
    /*
     * Function to upload submission attachments in WordPress media
     */

    public function media_handle_attachment($file_handler, $post_id, $set_thu = false) {

        /*
         * Including default WordPress libraries
         */
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        $attach_id = media_handle_upload($file_handler, $post_id);

        return $attach_id;
    }

    public function attach() {
        $attachment_ids = array();
        $original_files = $_FILES;

        /*
         * Get file upload global settings
         */
        $multiple = get_option('rm_option_allow_multiple_file_uploads');

        /*
         * Handling multiple attachments
         */
        if ($multiple == "yes") {
            foreach ($_FILES as $f_name => $name) {
                // var_dump($f_name);
                $files = $_FILES[$f_name];
                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name' => $files['name'][$key],
                            'type' => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error' => $files['error'][$key],
                            'size' => $files['size'][$key]
                        );
                        $_FILES = array($f_name => $file);
                        foreach ($_FILES as $file => $array) {
                            $attach_id = $this->media_handle_attachment($file, 0);
                            if (is_wp_error($attach_id)) {
                                break;
                            } else {
                                $attachment_ids[$f_name][] = $attach_id;
                            }
                        }
                    }
                    $_FILES = $original_files;
                }
            }
        } else {
            /*
             * Handling single attachment
             */
            foreach ($_FILES as $key => $file) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                require_once(ABSPATH . "wp-admin" . '/includes/media.php');

                $attach_id = media_handle_upload($key, 0);
                if (is_wp_error($attach_id)) {
                    break;
                } else {
                    $attachment_ids[$key] = $attach_id;
                }
            }
        }

        return $attachment_ids;
    }

    /**
     * This function is used to get all the attachment ids from the submissions
     * 
     * @param int $form_id
     * @return boolean
     */
    public function get_all_form_attachments($form_id, $return_type = 'ids', $limit = false) {

        $attachments = RM_DBManager::get_all_form_attachments($form_id);

        if ($attachments) {

            $all_attachments = array();

            foreach ($attachments as $key => $attachment) {

                $attachment = maybe_unserialize($attachment);
                if (isset($attachment['rm_field_type']))
                    unset($attachment['rm_field_type']);
                if (is_array($attachment))
                    foreach ($attachment as $att) {
                    // if (!in_array($att, $all_attachments))
                        //if (wp_get_attachment_url($att))
                        $all_attachments[] = $att;
                    }
            }
            //var_dump($all_attachments);die;
        } else
            return false;
        // var_dump($return_type);die;
        switch ($return_type) {
            case 'ids':
                $loop = new WP_Query(array('post_type' => 'attachment', 'post__in' => $all_attachments, 'post_status' => 'inherit', 'posts_per_page' => ((int) $limit) ? : -1, 'fields' => 'ids'));
                return $loop->posts;
            case 'posts' :
                $loop = new WP_Query(array('post_type' => 'attachment', 'post__in' => $all_attachments, 'post_status' => 'inherit', 'posts_per_page' => ((int) $limit) ? : -1));
                return $loop->posts;
            case 'count':
                $loop = new WP_Query(array('post_type' => 'attachment', 'post__in' => $all_attachments, 'post_status' => 'inherit', 'posts_per_page' => -1, 'fields' => 'ids'));
                return $loop->post_count;
        }
    }

    /**
     * Function is used to create a zip of wp-attachments from their ids
     * 
     * As per our need this function creates a zip file with name 'rm_attachments.zip' in Temp_Files
     * folder in the our plugins dir. If needed dynamic name and and temp file creation will be added.
     * 
     * @param array $attachment_ids array of ids of the attachments to be added to the zip.
     */
    public function get_zip(array $attachment_ids) {
        global $rm_env_requirements;

        if (!($rm_env_requirements & RM_REQ_EXT_ZIP))
            return;

        $file_name = 'rm_attachments' . time() . mt_rand(10, 1000000);

        $file_path = get_temp_dir() . $file_name . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($file_path, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            exit("cannot open <$file_name>\n");
        }

        foreach ($attachment_ids as $attachment) {
            $file = get_attached_file($attachment);
            $basename = basename($file);
            if (file_exists($file))
                $zip->addFile($file, $basename);
        }

        $zipped_file = $zip->filename;
        $zip->close();

        return $zipped_file;
    }

}
