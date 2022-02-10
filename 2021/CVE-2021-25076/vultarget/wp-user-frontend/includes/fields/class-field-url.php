<?php

/**
 * Url Field Class
 */
class WPUF_Form_Field_URL extends WPUF_Form_Field_Text {

    public function __construct() {
        $this->name       = __( 'Website URL', 'wp-user-frontend' );
        $this->input_type = 'website_url';
        $this->icon       = 'link';
    }

    /**
     * Render the URL field
     *
     * @param array  $field_settings
     * @param int    $form_id
     * @param string $type
     * @param int    $post_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        if ( isset( $post_id ) && $post_id !== '0' ) {
            if ( $this->is_meta( $field_settings ) ) {
                $value = $this->get_meta( $post_id, $field_settings['name'], $type );
            }
        } else {
            $value = $field_settings['default'];
        }

        $this->field_print_label( $field_settings, $form_id ); ?>
            <div class="wpuf-fields">
                <input
                    id="<?php echo esc_attr( $field_settings['name'] . '_' . $form_id ); ?>"
                    type="url" pattern="^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,20}(:[0-9]{1,20})?(\/.*)?$" class="url <?php echo esc_attr( ' wpuf_' . $field_settings['name'] . '_' . $form_id ); ?>"
                    data-required="<?php echo esc_attr( $field_settings['required'] ); ?>"
                    data-type="text"
                    name="<?php echo esc_attr( $field_settings['name'] ); ?>"
                    placeholder="<?php echo esc_attr( $field_settings['placeholder'] ); ?>"
                    value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $field_settings['size'] ); ?>"
                    autocomplete="url"
                />
                <?php $this->help_text( $field_settings ); ?>
            </div>

        <?php
        $this->after_field_print_label();
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {
        $default_options = $this->get_default_option_settings();
        $settings        = $this->get_default_text_option_settings( false ); // word_restriction = false

        $settings[] = [
            'name'      => 'open_window',
            'title'     => __( 'Open in : ', 'wp-user-frontend' ),
            'type'      => 'radio',
            'options'   => [
                'same'   => __( 'Same Window', 'wp-user-frontend' ),
                'new'    => __( 'New Window', 'wp-user-frontend' ),
            ],
            'section'   => 'basic',
            'default'   => 'same',
            'inline'    => true,
            'priority'  => 32,
            'help_text' => __( 'Choose whether the link will open in new tab or same window', 'wp-user-frontend' ),
        ];

        return array_merge( $default_options, $settings );
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {
        $defaults = $this->default_attributes();

        $props = [
            'input_type'        => 'url',
            'is_meta'           => 'yes',
            'width'             => 'large',
            'open_window'       => 'same',
            'size'              => 40,
            'id'                => 0,
            'is_new'            => true,
            'show_in_post'      => 'yes',
            'hide_field_label'  => 'no',
        ];

        return array_merge( $defaults, $props );
    }

    /**
     * Prepare entry
     *
     * @param $field
     *
     * @return mixed
     */
    public function prepare_entry( $field ) {
        check_ajax_referer( 'wpuf_form_add' );

        $field = isset( $_POST[ $field['name'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) ) : '';
        return esc_url( trim( $field ) );
    }

    /**
     * Render field data
     *
     * @since 3.3.1
     *
     * @param mixed $data
     * @param array $field
     *
     * @return string
     */
    public function render_field_data( $data, $field ) {
        $data       = implode( ',', $data );
        $hide_label = isset( $field['hide_field_label'] )
            ? wpuf_validate_boolean( $field['hide_field_label'] )
            : false;

        if ( empty( $data ) ) {
            return '';
        }

        $container_classnames = [ 'wpuf-field-data', 'wpuf-field-data-' . $this->input_type ];

        ob_start();
        ?>
            <li class="<?php echo esc_attr( implode( ' ', $container_classnames ) ); ?>">
                <?php if ( ! $hide_label ) : ?>
                    <label><?php echo esc_html( $field['label'] ); ?>:</label>
                <?php endif; ?>
                <a href="<?php echo esc_url_raw( $data ); ?>"
                    <?php echo ! empty( $field['open_window'] ) && $field['open_window'] === 'new' ? 'target="_blank" rel="noreferrer noopener"' : ''; ?> > <?php echo esc_url_raw( $data ); ?> </a>
            </li>
        <?php
        return ob_get_clean();
    }
}
