<?php

/**
 * Acf integration class
 */
class WPUF_ACF_Compatibility {

    public $id = 'acf';

    public $title = 'Advanced Custom Fields';

    public function __construct() {
        add_action( 'admin_notices', [ $this, 'maybe_show_notice' ] );

        add_action( 'wp_ajax_wpuf_dismiss_notice_' . $this->id, [ $this, 'dismiss_notice' ] );
        add_action( 'wp_ajax_wpuf_compatibility_' . $this->id, [ $this, 'maybe_compatible' ] );
        add_action( 'wp_ajax_wpuf_migrate_' . $this->id, [ $this, 'migrate_cf_data' ] );
        add_filter( 'acf/load_value', [ $this, 'load_compatible_value' ], 10, 3 );
        add_action( 'wpuf_add_post_after_insert', [ $this, 'update_acf_field_meta' ], 10, 4 );
    }

    /**
     * See if ACF plugin exists
     *
     * @return bool
     */
    public function plugin_exists() {
        return class_exists( 'acf' );
    }

    /**
     * If the prompt is dismissed
     *
     * @return bool
     */
    public function is_dismissed() {
        return 'yes' == get_option( 'wpuf_dismiss_notice_' . $this->id );
    }

    /**
     * Check if
     *
     * @return bool
     */
    public function is_compatible() {
        return 'yes' == wpuf_get_option( 'wpuf_compatibility_' . $this->id, 'wpuf_general', 'no' );
    }

    /**
     * Check if
     *
     * @return bool
     */
    public function is_migrated() {
        return 'yes' == get_option( 'wpuf_migrate_' . $this->id );
    }

    /**
     * Dismiss the prompt
     *
     * @return void
     */
    public function dismiss_prompt() {
        update_option( 'wpuf_dismiss_notice_' . $this->id, 'yes' );
    }

    /**
     * Update option
     *
     *@return void
     */
    public function maybe_compatible() {
        wpuf_update_option( 'wpuf_compatibility_' . $this->id, 'wpuf_general', 'yes' );

        wp_send_json_success();
    }

    /**
     * Update existing custom fields data
     *
     *@return void
     */
    public function migrate_cf_data() {
        $forms = $this->get_post_forms();

        if ( ! empty( $forms ) ) {
            foreach ( $forms as $form ) {
                $form_id        = $form->ID;
                $form_vars      = wpuf_get_form_fields( $form_id );
                $form_settings  = wpuf_get_form_settings( $form_id );
                $post_type      = $form_settings['post_type'];

                foreach ( $form_vars as $attr ) {
                    $field_type = $attr['input_type'];
                    $meta       = $attr['is_meta'];

                    if ( $meta == 'yes' && ( $field_type == 'checkbox' || $field_type == 'multiselect' ) ) {
                        $meta_key = $attr['name'];

                        $args = [
                            'post_type'   => $post_type,
                            'meta_key'    => '_wpuf_form_id',
                        ];
                        $posts = get_posts( $args );

                        if ( ! empty( $posts ) ) {
                            foreach ( $posts as $post ) {
                                $post_id    = $post->ID;
                                $separator  = '| ';
                                $meta_value = get_post_meta( $post_id, $meta_key );

                                if ( ! empty( $meta_value ) ) {
                                    $new_value = explode( $separator, $meta_value[0] );
                                    $new_value = maybe_serialize( $new_value );

                                    update_post_meta( $post_id, $meta_key, $new_value );
                                }
                            }
                        }
                    }
                }
            }
        }

        update_option( 'wpuf_migrate_' . $this->id, 'yes' );
        wpuf_update_option( 'wpuf_compatibility_' . $this->id, 'wpuf_general', 'yes' );

        wp_send_json_success();
    }

    /**
     * Get all post form
     *
     *@return array
     */
    public function get_post_forms() {
        $args = [
            'post_type'   => 'wpuf_forms',
            'post_status' => 'publish',
        ];

        return $form_posts = get_posts( $args );
    }

    /**
     * Dismiss the notice
     *
     * @return void
     */
    public function dismiss_notice() {
        $this->dismiss_prompt();

        wp_send_json_success();
    }

    /**
     * Show notice if the plugin found
     *
     * @return void
     */
    public function maybe_show_notice() {
        if ( ! $this->plugin_exists() ) {
            return;
        }

        if ( $this->is_dismissed() || $this->is_compatible() || $this->is_migrated() || ! current_user_can( 'manage_options' ) ) {
            return;
        } ?>
        <div class="notice notice-info">
            <p><strong><?php printf( esc_html( __( '%s Detected', 'wp-user-frontend' ) ), esc_html( $this->title ) ); ?></strong></p>
            <p><?php printf( wp_kses_post( __( 'Hey, looks like you have <strong>%s</strong> installed. What do you want to do with WPUF?', 'wp-user-frontend' ) ), esc_html( $this->title ) ); ?></p>
            <p><i><strong style="color:#46b450;">Compatible: </strong><?php printf( esc_html( __( 'It will update compatibility option only, so existing custom fields data format will not change.', 'wp-user-frontend' ) ) ); ?></i></p>
            <p><i><strong style="color:#46b450;">Compatible & Migrate: </strong><?php printf( esc_html( __( 'It will update existing custom fields data to ACF format and update compatibility option too.', 'wp-user-frontend' ) ) ); ?></i></p>

            <p>
                <a href="#" class="button button-primary" id="wpuf-compatible-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Compatible', 'wp-user-frontend' ); ?></a>
                <a href="#" class="button button-primary" id="wpuf-migrate-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'Compatible & Migrate', 'wp-user-frontend' ); ?></a>
                <a href="#" class="button" id="wpuf-dismiss-<?php echo esc_attr( $this->id ); ?>"><?php esc_html_e( 'No Thanks', 'wp-user-frontend' ); ?></a>
            </p>
        </div>

        <script type="text/javascript">
            jQuery(function($) {
                $('.notice').on('click', 'a#wpuf-compatible-<?php echo esc_attr( $this->id ); ?>', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    self.addClass('updating-message');
                    wp.ajax.send('wpuf_compatibility_<?php echo esc_attr( $this->id ); ?>', {
                        success: function() {
                            var html = '<p><strong>Compatible Option Updated</strong></p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-success').html( html );
                        },

                        error: function() {
                            var html = '<p><strong>Something went wrong.</strong></p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-error').html( html );
                        },

                        complete: function() {
                            self.removeClass('updating-message');
                        }
                    });
                });

                $('.notice').on('click', 'a#wpuf-migrate-<?php echo esc_attr( $this->id ); ?>', function(e) {
                    e.preventDefault();

                    var self = $(this);
                    self.addClass('updating-message');
                    wp.ajax.send('wpuf_migrate_<?php echo esc_attr( $this->id ); ?>', {
                        success: function() {
                            var html  = '<p><strong>Compatible option and existing custom fields data updated</strong></p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-success').html( html );
                        },

                        error: function() {
                            var html = '<p><strong>Something went wrong.</strong></p>';

                            self.closest('.notice').removeClass('notice-info').addClass('notice-error').html( html );
                        },

                        complete: function() {
                            self.removeClass('updating-message');
                        }
                    });
                });

                $('.notice').on('click', '#wpuf-dismiss-<?php echo esc_attr( $this->id ); ?>', function(e) {
                    e.preventDefault();

                    $(this).closest('.notice').remove();
                    wp.ajax.send('wpuf_dismiss_notice_<?php echo esc_attr( $this->id ); ?>');
                });

            });
        </script>

        <?php
    }

    /**
     * ACF compatible wpuf field values
     *
     * @since 3.2.0
     *
     * @param mixed $value
     * @param int   $post_id
     * @param array $field
     *
     * @return mixed
     */
    public function load_compatible_value( $value, $post_id, $field ) {
        switch ( $field['type'] ) {
            case 'checkbox':
                $value = wpuf()->fields->get_field( 'checkbox_field' )->get_formatted_value( $value );
                break;

            default:
                break;
        }

        return $value;
    }

    /**
     * Update acf post meta
     *
     * @since 3.5.20
     *
     * @param $post_id
     * @param $form_id
     * @param $form_settings
     * @param $meta_vars
     */
    public function update_acf_field_meta( $post_id, $form_id, $form_settings, $meta_vars ) {
        if ( ! $this->plugin_exists() ){
            return;
        }

        $groups = acf_get_field_groups( [ 'post_type' => $form_settings['post_type'] ] );
        $existing_meta = get_post_meta( $post_id );

        foreach ( acf_get_fields( $groups ) as $group ) {
            $meta_key = '_' . $group['name'];
            $name = $group['name'];

            if ( 'repeater' === $group['type'] ) {
                $meta_key = 'repeater';
            }

            //check key also in meta vars
            $meta_keys = array_map(
                function ( $meta_var ) {
                    return $meta_var['name'];
                }, $meta_vars
            );

            if ( ! array_key_exists( $meta_key, $existing_meta ) && in_array( $name, $meta_keys, true ) ) {
                update_post_meta( $post_id, $meta_key, $group['key'] );
            }
        }
    }
}
