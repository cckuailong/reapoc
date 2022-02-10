<?php

/**
 * Attachment Uploader class
 *
 * @since 1.0
 */
class WPUF_Upload {

    public function __construct() {
        add_action( 'wp_ajax_wpuf_upload_file', [ $this, 'upload_file' ] );
        add_action( 'wp_ajax_nopriv_wpuf_upload_file', [ $this, 'upload_file' ] );

        add_action( 'wp_ajax_wpuf_file_del', [ $this, 'delete_file' ] );
        add_action( 'wp_ajax_nopriv_wpuf_file_del', [ $this, 'delete_file' ] );

        add_action( 'wp_ajax_wpuf_insert_image', [ $this, 'insert_image' ] );
        add_action( 'wp_ajax_nopriv_wpuf_insert_image', [ $this, 'insert_image' ] );
    }

    /**
     * Validate if it's coming from WordPress with a valid nonce
     *
     * @return void
     */
    public function validate_nonce() {
        $nonce = isset( $_GET['nonce'] ) ? sanitize_key( wp_unslash( $_GET['nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-upload-nonce' ) ) {
            return;
        }
    }

    public function upload_file( $image_only = false ) {
        $nonce = isset( $_REQUEST['nonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['nonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf-upload-nonce' ) ) {
            return;
        }

        // a valid request will have a form ID
        $form_id = isset( $_POST['form_id'] ) ? intval( wp_unslash( $_POST['form_id'] ) ) : false;

        if ( ! $form_id ) {
            die( 'error' );
        }

        $field_type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';

        /**
         * Hook fires before begining upload process
         *
         * @since 3.3.0
         *
         * @param int    $form_id
         * @param string $field_type
         */
        do_action( 'wpuf_upload_file_init', $form_id, $field_type );

        // check if guest post enabled for guests
        if ( ! is_user_logged_in() ) {
            $guest_post    = false;
            $form_settings = wpuf_get_form_settings( $form_id );

            if ( isset( $form_settings['guest_post'] ) && $form_settings['guest_post'] == 'true' ) {
                $guest_post = true;
            }

            // check if the request coming from weForms & allow users to upload when require login option is disabled
            if ( isset( $form_settings['require_login'] ) && $form_settings['require_login'] == 'false' ) {
                $guest_post = true;
            }

            //if it is registration form, let the user upload the file
            if ( get_post_type( $form_id ) == 'wpuf_profile' ) {
                $guest_post = true;
            }

            if ( ! $guest_post ) {
                die( 'error' );
            }
        }

        $wpuf_file = isset( $_FILES['wpuf_file'] ) ? $_FILES['wpuf_file'] : []; // WPCS: sanitization ok.

        $file_name      = pathinfo( $wpuf_file['name'], PATHINFO_FILENAME );
        $file_extension = pathinfo( $wpuf_file['name'], PATHINFO_EXTENSION );
        $hash           = wp_hash( time() );
        $hash           = substr( $hash, 0, 8 );

        $upload = [
            'name'     => $file_name . '-' . $hash . '.' . $file_extension,
            'type'     => $wpuf_file['type'],
            'tmp_name' => $wpuf_file['tmp_name'],
            'error'    => $wpuf_file['error'],
            'size'     => $wpuf_file['size'],
        ];

        header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );

        $attach = $this->handle_upload( $upload );

        if ( $attach['success'] ) {
            $response = [ 'success' => true ];

            if ( $image_only ) {
                $image_size = wpuf_get_option( 'insert_photo_size', 'wpuf_frontend_posting', 'thumbnail' );
                $image_type = wpuf_get_option( 'insert_photo_type', 'wpuf_frontend_posting', 'link' );

                /**
                 * Filter upload image size for response
                 *
                 * @since 3.3.0
                 *
                 * @param string $image_size
                 * @param int    $form_id
                 * @param string $field_type
                 */
                $image_size = apply_filters( 'wpuf_upload_response_image_size', $image_size, $form_id, $field_type );

                /**
                 * Filter upload image type for response
                 *
                 * @since 3.3.0
                 *
                 * @param string $image_size
                 * @param int    $form_id
                 * @param string $field_type
                 */
                $image_type = apply_filters( 'wpuf_upload_response_image_type', $image_type, $form_id, $field_type );

                if ( $image_type == 'link' ) {
                    $response['html'] = wp_get_attachment_link( $attach['attach_id'], $image_size );
                } else {
                    $response['html'] = wp_get_attachment_image( $attach['attach_id'], $image_size );
                }
            } else {
                $response['html'] = self::attach_html( $attach['attach_id'], $field_type, $form_id );
            }

            echo wp_kses(
                $response['html'], [
                    'li'       => [
                        'class' => [],
                    ],
                    'div'      => [
                        'class' => [],
                    ],
                    'img'      => [
                        'src'   => [],
                        'alt'   => [],
                        'class' => [],
                    ],
                    'input'    => [
                        'type'        => [],
                        'name'        => [],
                        'value'       => [],
                        'placeholder' => [],
                    ],
                    'textarea' => [
                        'name'        => [],
                        'placeholder' => [],
                    ],
                    'a'        => [
                        'href'           => [],
                        'class'          => [],
                        'data-attach-id' => [],
                    ],
                    'span'     => [
                        'class' => [],
                    ],
                ]
            );
        } else {
            wp_send_json_error( $attach['error'], 200 );
        }

        exit;
    }

    /**
     * Generic function to upload a file
     *
     * @param string $field_name file input field name
     *
     * @return bool|int attachment id on success, bool false instead
     */
    public function handle_upload( $upload_data ) {
        $check_duplicate = $this->duplicate_upload( $upload_data );

        if ( isset( $check_duplicate['duplicate'] ) && $check_duplicate['duplicate'] ) {
            return [
                'success' => true,
                'attach_id' => $check_duplicate['duplicate'],
            ];
        }

        $uploaded_file = wp_handle_upload( $upload_data, [ 'test_form' => false ] );

        // If the wp_handle_upload call returned a local path for the image
        if ( isset( $uploaded_file['file'] ) ) {
            $file_loc    = $uploaded_file['file'];
            $file_name   = basename( $upload_data['name'] );
            $upload_hash = md5( $upload_data['name'] . $upload_data['size'] );
            $file_type   = wp_check_filetype( $file_name );

            $attachment = [
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            $attach_id   = wp_insert_attachment( $attachment, $file_loc );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta( $attach_id, 'wpuf_file_hash', $upload_hash );

            return [
                'success' => true,
                'attach_id' => $attach_id,
            ];
        }

        return [
            'success' => false,
            'error' => $uploaded_file['error'],
        ];
    }

    public static function attach_html( $attach_id, $type = null, $form_id = null ) {
        if ( ! $type ) {
            $type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'image';
        }

        $attachment = get_post( $attach_id );

        if ( ! $attachment ) {
            return;
        }

        if ( wp_attachment_is_image( $attach_id ) ) {
            /**
             * Filter upload image size for response
             *
             * @since 3.3.0
             *
             * @param string $image_size
             * @param int    $form_id
             * @param string $field_type
             */
            $image_size = apply_filters( 'wpuf_upload_response_image_size', 'thumbnail', $form_id, $type );

            $image = wp_get_attachment_image_src( $attach_id, $image_size );
            $image = $image[0];
        } else {
            $image = wp_mime_type_icon( $attach_id );
        }

        /**
         * Filter uploaded image class names for the reponse
         *
         * @since 3.3.0
         *
         * @param array $class_names
         */
        $attachment_class_names = apply_filters( 'wpuf_upload_response_image_class_names', [ 'wpuf-attachment-image' ] );
        $attachment_class_names = implode( ' ', $attachment_class_names );

        $html = '<li class="ui-state-default wpuf-image-wrap thumbnail">';
        $html .= sprintf( '<div class="attachment-name"><img src="%s" alt="%s" class="%s" /></div>', $image, esc_attr( $attachment->post_title ), esc_attr( $attachment_class_names ) );

        if ( wpuf_get_option( 'image_caption', 'wpuf_frontend_posting', 'off' ) == 'on' ) {
            $html .= '<div class="wpuf-file-input-wrap">';
            $html .= sprintf( '<input type="text" name="wpuf_files_data[%d][title]" value="%s" placeholder="%s">', $attach_id, esc_attr( $attachment->post_title ), __( 'Title', 'wp-user-frontend' ) );
            $html .= sprintf( '<textarea name="wpuf_files_data[%d][caption]" placeholder="%s">%s</textarea>', $attach_id, __( 'Caption', 'wp-user-frontend' ), esc_textarea( $attachment->post_excerpt ) );
            $html .= sprintf( '<textarea name="wpuf_files_data[%d][desc]" placeholder="%s">%s</textarea>', $attach_id, __( 'Description', 'wp-user-frontend' ), esc_textarea( $attachment->post_content ) );
            $html .= '</div>';
        }

        $html .= sprintf( '<input type="hidden" name="wpuf_files[%s][]" value="%d">', $type, $attach_id );
        $html .= '<div class="caption">';
        $html .= sprintf( '<a href="#" class="attachment-delete" data-attach-id="%d"> <img src="%s" /></a>', $attach_id, WPUF_ASSET_URI . '/images/del-img.png' );
        $html .= sprintf( '<span class="wpuf-drag-file"> <img src="%s" /></span>', WPUF_ASSET_URI . '/images/move-img.png' );
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    public function delete_file() {
        check_ajax_referer( 'wpuf_nonce', 'nonce' );

        $post_data = wp_unslash( $_POST );

        $attachment_id = isset( $post_data['attach_id'] ) ? absint( $post_data['attach_id'] ) : 0;

        if ( empty( $attachment_id ) ) {
            wp_send_json_error( [ 'message' => __( 'attach_id is required.', 'wp-user-frontend' ) ], 422 );
        }

        $attachment = get_post( $attachment_id );

        // post author or editor role
        if ( get_current_user_id() == absint( $attachment->post_author ) || current_user_can( 'delete_private_pages' ) ) {
            $deleted = wp_delete_attachment( $attachment_id, true );

            if ( $deleted ) {
                wp_send_json_success( [ 'message' => __( 'Attachment deleted successfully.', 'wp-user-frontend' ) ] );
            }

            wp_send_json_error( [ 'message' => __( 'Could not deleted the attachment', 'wp-user-frontend' ) ], 422 );
        }

        wp_send_json_error( [ 'message' => __( 'Something went wrong.', 'wp-user-frontend' ) ], 422 );
    }

    public function associate_file( $attach_id, $post_id ) {
        wp_update_post(
            [
                'ID'          => $attach_id,
                'post_parent' => $post_id,
            ]
        );
    }

    public function insert_image() {
        $this->upload_file( true );
    }

    /**
     * Check if duplicate file
     *
     * @param array $file
     *
     * @return mixed
     */
    function duplicate_upload( $file ) {
        global $wpdb;

        $upload_hash = md5( $file['name'] . $file['size'] );

        $sql   = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta m JOIN $wpdb->posts p ON p.ID = m.post_id WHERE m.meta_key = 'wpuf_file_hash' AND m.meta_value = %s AND p.post_status != 'trash' LIMIT 1;", $upload_hash );
        $match = $wpdb->get_var( $sql );

        if ( $match ) {
            $file['duplicate'] = $match;
        }

        return $file;
    }
}
