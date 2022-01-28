<?php

namespace ProfilePress\Core\Classes;

use WP_Error;

class ImageUploader
{
    const AVATAR = 'avatar';
    const COVER_IMAGE = 'cover_image';

    /**
     * @param $image
     * @param string $image_id used to identify in the code if image is an avatar or cover image.
     * @param mixed|void $path
     *
     * @return string|string[]|WP_Error|null
     */
    public static function process($image, $image_id = 'avatar', $path = PPRESS_AVATAR_UPLOAD_DIR)
    {
        /**
         * Fires before image is process for validation and uploading
         */
        do_action('ppress_before_saving_image', $image);

        if ( ! empty($image)) {

            if ($image["error"] !== 0) {
                return new WP_Error(
                    'file_error',
                    apply_filters('ppress_image_unexpected_error',
                        esc_html__('Unexpected error with image upload, Please try again.', 'wp-user-avatar')));
            }

            $field_name = esc_html__('Uploaded image', 'wp-user-avatar');

            // filesize in kilobyte. default is 1000KB
            $size = 1000;

            if ($image_id == self::COVER_IMAGE) {
                $field_name = esc_html__('Cover Image', 'wp-user-avatar');
                /** WP User Avatar Adapter STARTS */
                global $wpua_cover_upload_size_limit;
                $size = (int)floor($wpua_cover_upload_size_limit / 1024);
                /** WP User Avatar Adapter ENDS */
            }

            if ($image_id == self::AVATAR) {
                $field_name = esc_html__('Profile Picture', 'wp-user-avatar');
                /** WP User Avatar Adapter STARTS */
                global $wpua_upload_size_limit;
                $size = (int)floor($wpua_upload_size_limit / 1024);
                /** WP User Avatar Adapter ENDS */
            }

            $file_size = apply_filters('ppress_image_upload_size', $size, $image_id);

            if ($image["size"] > ($file_size * 1024)) {

                return new WP_Error('file_too_large', apply_filters(
                        'ppress_image_too_large',
                        sprintf(
                            esc_html__('%s is greater than the allowed sized of %s', 'wp-user-avatar'),
                            $field_name,
                            "$file_size KB"
                        )
                    )
                );
            }

            // verify the file is a GIF, JPEG, or PNG
            $fileType = exif_imagetype($image["tmp_name"]);

            $allowed_image_type = apply_filters('ppress_allowed_image_type', array(
                IMAGETYPE_GIF,
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG
            ));

            if ( ! in_array($fileType, $allowed_image_type)) {
                return new WP_Error(
                    'image_invalid',
                    apply_filters('ppress_image_not_image_error', esc_html__('Uploaded file not an image.', 'wp-user-avatar'))
                );
            }

            $image_upload_dir = apply_filters('ppress_image_upload_dir', $path);

            // ensure a safe filename
            $file_name = preg_replace("/[^A-Z0-9._-]/i", "_", $image["name"]);

            // explode the file
            $parts = pathinfo($file_name);

            $file_name = md5($parts["filename"]) . '.' . $parts["extension"];

            // don't overwrite an existing file
            $i                = 0;
            $file_exist_parts = pathinfo($file_name);
            while (file_exists($image_upload_dir . $file_name)) {
                $i++;
                $file_name = $file_exist_parts["filename"] . "-" . $i . "." . $file_exist_parts["extension"];
                $file_name = md5($file_name) . '.' . $file_exist_parts["extension"];
            }

            if ( ! file_exists($image_upload_dir)) {
                mkdir($image_upload_dir, 0755, true);
            }

            // create index.php file in theme assets folder
            if ( ! file_exists($image_upload_dir . '/index.php')) {
                ppress_create_index_file($image_upload_dir);
            }

            // preserve file from temporary directory
            $success = move_uploaded_file($image["tmp_name"], $image_upload_dir . $file_name);

            if ( ! $success) {
                return new WP_Error ('save_error', esc_html__('Unable to save file, please try again.', 'wp-user-avatar'));
            }

            if ($image_id == self::AVATAR) {
                /** WP User Avatar Adapter STARTS */
                global $wp_user_avatar, $wpua_resize_crop, $wpua_resize_h, $wpua_resize_upload, $wpua_resize_w;

                if ( ! $wp_user_avatar->wpua_is_author_or_above() && (bool)$wpua_resize_upload === true) {

                    $uploaded_image = wp_get_image_editor($image_upload_dir . $file_name);

                    if ( ! is_wp_error($uploaded_image)) {

                        $uploaded_image->resize($wpua_resize_w, $wpua_resize_h, $wpua_resize_crop == '1');

                        $uploaded_image->save($image_upload_dir . $file_name);
                    }
                }
                /** WP User Avatar Adapter ENDS */
            }

            // set proper permissions on the new file
            chmod($image_upload_dir . $file_name, 0644);

            /**
             * Fires after image have been saved
             *
             * @param string $file_name uploaded image url
             */
            do_action('ppress_after_saving_image', $file_name, $image_upload_dir);

            return $file_name;
        }
    }
}