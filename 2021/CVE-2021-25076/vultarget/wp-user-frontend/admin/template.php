<?php

/**
 * WPUF Form builder template
 */
class WPUF_Admin_Template {
    public static $input_name = 'wpuf_input';

    public static $cond_name = 'wpuf_cond';

    /**
     * Legend of a form item
     *
     * @param string $title
     * @param array  $values
     */
    public static function legend( $title = 'Field Name', $values = [], $field_id = 0 ) {
        $field_label = $values ? ': <strong>' . $values['label'] . '</strong>' : '';
        $id          = isset( $values['id'] ) ? $values['id'] : ''; ?>
        <div class="wpuf-legend" title="<?php esc_html_e( 'Click and Drag to rearrange', 'wp-user-frontend' ); ?>">
            <input type="hidden" value="<?php echo esc_attr( $id ); ?>" name="wpuf_input[<?php echo esc_attr( $field_id ); ?>][id]">
            <div class="wpuf-label"><?php echo esc_attr( $title . $field_label ); ?></div>
            <div class="wpuf-actions">
                <a href="#" class="wpuf-remove"><?php esc_html_e( 'Remove', 'wp-user-frontend' ); ?></a>
                <a href="#" class="wpuf-toggle"><?php esc_html_e( 'Toggle', 'wp-user-frontend' ); ?></a>
            </div>
        </div> <!-- .wpuf-legend -->
        <?php
    }

    /**
     * Common Fields for a input field
     *
     * Contains required, label, meta_key, help text, css class name
     *
     * @param int   $id               field order
     * @param mixed $field_name_value
     * @param bool  $custom_field     if it a custom field or not
     * @param array $values           saved value
     */
    public static function common( $id, $field_name_value = '', $custom_field = true, $values = [] ) {
        $tpl                 = '%s[%d][%s]';
        $required_name       = sprintf( $tpl, self::$input_name, $id, 'required' );
        $field_name          = sprintf( $tpl, self::$input_name, $id, 'name' );
        $label_name          = sprintf( $tpl, self::$input_name, $id, 'label' );
        $is_meta_name        = sprintf( $tpl, self::$input_name, $id, 'is_meta' );
        $help_name           = sprintf( $tpl, self::$input_name, $id, 'help' );
        $css_name            = sprintf( $tpl, self::$input_name, $id, 'css' );

        // $field_name_value = $field_name_value ?
        $required            = $values ? esc_attr( $values['required'] ) : 'yes';
        $label_value         = $values ? esc_attr( $values['label'] ) : '';
        $help_value          = $values ? stripslashes( $values['help'] ) : '';
        $css_value           = $values ? esc_attr( $values['css'] ) : '';

        if ( $custom_field && $values ) {
            $field_name_value = $values['name'];
        } ?>
        <div class="wpuf-form-rows required-field">
            <label><?php esc_html_e( 'Required', 'wp-user-frontend' ); ?></label>

            <div class="wpuf-form-sub-fields">
                <label><input type="radio" name="<?php echo esc_attr( $required_name ); ?>" value="yes"<?php checked( $required, 'yes' ); ?>> <?php esc_html_e( 'Yes', 'wp-user-frontend' ); ?> </label>
                <label><input type="radio" name="<?php echo esc_attr( $required_name ); ?>" value="no"<?php checked( $required, 'no' ); ?>> <?php esc_html_e( 'No', 'wp-user-frontend' ); ?> </label>
            </div>
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Field Label', 'wp-user-frontend' ); ?></label>
            <input type="text" data-type="label" name="<?php echo esc_attr( $label_name ); ?>" value="<?php echo esc_attr( $label_value ); ?>" class="smallipopInput" title="<?php esc_html_e( 'Enter a title of this field', 'wp-user-frontend' ); ?>">
        </div> <!-- .wpuf-form-rows -->

        <?php if ( $custom_field ) { ?>
            <div class="wpuf-form-rows">
                <label><?php esc_html_e( 'Meta Key', 'wp-user-frontend' ); ?></label>
                <input type="text" data-type="name" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_name_value ); ?>" class="smallipopInput" title="<?php esc_html_e( 'Name of the meta key this field will save to', 'wp-user-frontend' ); ?>">
                <input type="hidden" name="<?php echo esc_attr( $is_meta_name ); ?>" value="yes">
            </div> <!-- .wpuf-form-rows -->
        <?php } else { ?>

            <input type="hidden" data-type="name" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_name_value ); ?>">
            <input type="hidden" name="<?php echo esc_attr( $is_meta_name ); ?>" value="no">

        <?php } ?>

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Help text', 'wp-user-frontend' ); ?></label>
            <textarea name="<?php echo esc_attr( $help_name ); ?>" class="smallipopInput" title="<?php esc_html_e( 'Give the user some information about this field', 'wp-user-frontend' ); ?>"><?php echo esc_attr( $help_value ); ?></textarea>
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'CSS Class Name', 'wp-user-frontend' ); ?></label>
            <input type="text" name="<?php echo esc_attr( $css_name ); ?>" value="<?php echo esc_attr( $css_value ); ?>" class="smallipopInput" title="<?php esc_html_e( 'Add a CSS class name for this field', 'wp-user-frontend' ); ?>">
        </div> <!-- .wpuf-form-rows -->

        <?php
    }

    /**
     * Common fields for a text area
     *
     * @param int   $id
     * @param array $values
     */
    public static function common_text( $id, $values = [] ) {
        $tpl                    = '%s[%d][%s]';
        $placeholder_name       = sprintf( $tpl, self::$input_name, $id, 'placeholder' );
        $default_name           = sprintf( $tpl, self::$input_name, $id, 'default' );
        $size_name              = sprintf( $tpl, self::$input_name, $id, 'size' );
        $word_restriction_name  = sprintf( $tpl, self::$input_name, $id, 'word_restriction' );

        $placeholder_value      = $values ? esc_attr( $values['placeholder'] ) : '';
        $default_value          = $values ? esc_attr( $values['default'] ) : '';
        $size_value             = $values ? esc_attr( $values['size'] ) : '40';
        $word_restriction_value = $values ? esc_attr( $values['word_restriction'] ) : ''; ?>
        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Placeholder text', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $placeholder_name ); ?>" title="<?php esc_attr_e( 'Text for HTML5 placeholder attribute', 'wp-user-frontend' ); ?>" value="<?php echo esc_attr( $placeholder_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Default value', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $default_name ); ?>" title="<?php esc_attr_e( 'The default value this field will have', 'wp-user-frontend' ); ?>" value="<?php echo esc_attr( $default_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Size', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $size_name ); ?>" title="<?php esc_attr_e( 'Size of this input field', 'wp-user-frontend' ); ?>" value="<?php echo esc_attr( $size_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Word Restriction', 'wp-user-frontend' ); ?></label>

            <div class="wpuf-form-sub-fields">
                <label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $word_restriction_name ); ?>" value="<?php echo esc_attr( $word_restriction_value ); ?>" title="<?php esc_attr_e( 'Numebr of words the author to be restricted in', 'wp-user-frontend' ); ?>" />
                </label>
            </div>
        </div> <!-- .wpuf-form-rows -->
        <?php
    }

    /**
     * Common fields for a textarea
     *
     * @param int   $id
     * @param array $values
     */
    public static function common_textarea( $id, $values = [] ) {
        $tpl                    = '%s[%d][%s]';
        $rows_name              = sprintf( $tpl, self::$input_name, $id, 'rows' );
        $cols_name              = sprintf( $tpl, self::$input_name, $id, 'cols' );
        $rich_name              = sprintf( $tpl, self::$input_name, $id, 'rich' );
        $placeholder_name       = sprintf( $tpl, self::$input_name, $id, 'placeholder' );
        $default_name           = sprintf( $tpl, self::$input_name, $id, 'default' );
        $word_restriction_name  = sprintf( $tpl, self::$input_name, $id, 'word_restriction' );

        $rows_value             = $values ? esc_attr( $values['rows'] ) : '5';
        $cols_value             = $values ? esc_attr( $values['cols'] ) : '25';
        $rich_value             = $values ? esc_attr( $values['rich'] ) : 'no';
        $placeholder_value      = $values ? esc_attr( $values['placeholder'] ) : '';
        $default_value          = $values ? esc_attr( $values['default'] ) : '';
        $word_restriction_value = $values ? esc_attr( $values['word_restriction'] ) : ''; ?>
        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Rows', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $rows_name ); ?>" title="Number of rows in textarea" value="<?php echo esc_attr( $rows_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Columns', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $cols_name ); ?>" title="Number of columns in textarea" value="<?php echo esc_attr( $cols_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Placeholder text', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $placeholder_name ); ?>" title="text for HTML5 placeholder attribute" value="<?php echo esc_attr( $placeholder_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Default value', 'wp-user-frontend' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo esc_attr( $default_name ); ?>" title="the default value this field will have" value="<?php echo esc_attr( $default_value ); ?>" />
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Textarea', 'wp-user-frontend' ); ?></label>

            <div class="wpuf-form-sub-fields">
                <label><input type="radio" name="<?php echo esc_attr( $rich_name ); ?>" value="no"<?php checked( $rich_value, 'no' ); ?>> <?php esc_html_e( 'Normal', 'wp-user-frontend' ); ?></label>
                <label><input type="radio" name="<?php echo esc_attr( $rich_name ); ?>" value="yes"<?php checked( $rich_value, 'yes' ); ?>> <?php esc_html_e( 'Rich textarea', 'wp-user-frontend' ); ?></label>
                <label><input type="radio" name="<?php echo esc_attr( $rich_name ); ?>" value="teeny"<?php checked( $rich_value, 'teeny' ); ?>> <?php esc_html_e( 'Teeny Rich textarea', 'wp-user-frontend' ); ?></label>
            </div>
        </div> <!-- .wpuf-form-rows -->

        <div class="wpuf-form-rows">
            <label><?php esc_html_e( 'Word Restriction', 'wp-user-frontend' ); ?></label>

            <div class="wpuf-form-sub-fields">
                <label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $word_restriction_name ); ?>" value="<?php echo esc_attr( $word_restriction_value ); ?>" title="<?php esc_attr_e( 'Numebr of words the author to be restricted in', 'wp-user-frontend' ); ?>" />
                </label>
            </div>
        </div> <!-- .wpuf-form-rows -->
        <?php
    }

    /**
     * Hidden field helper function
     *
     * @param string $name
     * @param string $value
     */
    public static function hidden_field( $name, $value = '' ) {
        printf( '<input type="hidden" name="%s" value="%s" />', esc_attr( self::$input_name ) . esc_attr( $name ), esc_attr( $value ) );
    }

    /**
     * Displays a radio custom field
     *
     * @param int    $field_id
     * @param string $name
     * @param array  $values
     */
    public static function radio_fields( $field_id, $name, $values = [] ) {
        $selected_name    = sprintf( '%s[%d][selected]', self::$input_name, $field_id );
        $input_name       = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name );
        $input_value_name = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name . '_values' );

        $selected_value   = ( $values && isset( $values['selected'] ) ) ? $values['selected'] : ''; ?>

        <label for="wpuf-<?php echo esc_attr( $name . '_' . $field_id ); ?>" class="wpuf-show-field-value">
            <input type="checkbox" class="wpuf-value-handelar" id="wpuf-<?php echo esc_attr( $name . '_' . $field_id ); ?>"><?php esc_html_e( 'Show values', 'wp-user-frontend' ); ?>
        </label>

        <div class="wpuf-option-label-value"><span><?php esc_html_e( 'Label', 'wp-user-frontend' ); ?></span><span class="wpuf-option-value" style="display: none;"><?php esc_html_e( 'Value', 'wp-user-frontend' ); ?></span></div>
        <?php
        if ( $values && $values['options'] > 0 ) {
            foreach ( $values['options'] as $key => $value ) {
                ?>
                <div class="wpuf-clone-field">
                    <input type="radio" name="<?php echo esc_attr( $selected_name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $selected_value, $value ); ?>>
                    <input type="text" data-type="option" name="<?php echo esc_attr( $input_name ); ?>[]" value="<?php echo esc_attr( $value ); ?>">
                    <input type="text" data-type="option_value" name="<?php echo esc_attr( $input_value_name ); ?>[]" value="<?php echo esc_attr( $key ); ?>" style="display:none;">

                    <?php self::remove_button(); ?>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="wpuf-clone-field">
                <input type="radio" name="<?php echo esc_attr( $selected_name ); ?>">
                <input type="text" data-type="option" name="<?php echo esc_attr( $input_name ); ?>[]" value="">
                <input type="text" data-type="option_value" name="<?php echo esc_attr( $input_value_name ); ?>[]" value="" style="display:none;">

                <?php self::remove_button(); ?>
            </div>
            <?php
        }
    }

    public static function conditional_field( $field_id, $con_fields = [] ) {
        do_action( 'wpuf_conditional_field_render_hook', $field_id, $con_fields, 'WPUF_Admin_Template' );
    }

    /**
     * Displays a checkbox custom field
     *
     * @param int    $field_id
     * @param string $name
     * @param array  $values
     */
    public static function common_checkbox( $field_id, $name, $values = [] ) {
        $selected_name    = sprintf( '%s[%d][selected]', self::$input_name, $field_id );
        $input_name       = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name );
        $input_value_name = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name . '_values' );

        $selected_value   = ( $values && isset( $values['selected'] ) ) ? $values['selected'] : []; ?>
        <style>
            .wpuf-option-label-value span {
                font-weight: bold;
                margin-left: 5%;
                margin-right: 27%;
            }
        </style>
        <input type="checkbox" class="wpuf-value-handelar" id="<?php echo esc_attr( $name . '_' . $field_id ); ?>"><label for="<?php echo esc_attr( $name . '_' . $field_id ); ?>"><?php esc_html_e( 'show values', 'wp-user-frontend' ); ?></label>
        <div class="wpuf-option-label-value"><span><?php esc_html_e( 'Label', 'wp-user-frontend' ); ?></span><span class="wpuf-option-value" style="display: none;"><?php esc_html_e( 'Value', 'wp-user-frontend' ); ?></span></div>
        <?php
        if ( $values && $values['options'] > 0 ) {
            foreach ( $values['options'] as $key => $value ) {
                ?>
                <div class="wpuf-clone-field">

                    <input type="checkbox" name="<?php echo esc_attr( $selected_name ); ?>[]" value="<?php echo esc_attr( $value ); ?>"<?php echo in_array( $value, $selected_value ) ? ' checked="checked"' : ''; ?> />
                    <input type="text" data-type="option" name="<?php echo esc_attr( $input_name ); ?>[]" value="<?php echo esc_attr( $value ); ?>">
                    <input type="text" data-type="option_value" name="<?php echo esc_attr( $input_value_name ); ?>[]" value="<?php echo esc_attr( $key ); ?>" style="display:none;">
                    <?php self::remove_button(); ?>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="wpuf-clone-field">
                <input type="checkbox" name="<?php echo esc_attr( $selected_name ); ?>[]">
                <input type="text" data-type="option" name="<?php echo esc_attr( $input_name ); ?>[]" value="">
                <input type="text" data-type="option_value" name="<?php echo esc_attr( $input_value_name ); ?>[]" value="" style="display:none;">

                <?php self::remove_button(); ?>
            </div>
            <?php
        }
    }

    /**
     * Add/remove buttons for repeatable fields
     *
     * @return void
     */
    public static function remove_button() {
        $add    = plugins_url( 'assets/images/add.png', __DIR__ );
        $remove = plugins_url( 'assets/images/remove.png', __DIR__ ); ?>
        <img style="cursor:pointer; margin:0 3px;" alt="add another choice" title="add another choice" class="wpuf-clone-field" src="<?php echo esc_attr( $add ); ?>">
        <img style="cursor:pointer;" class="wpuf-remove-field" alt="remove this choice" title="remove this choice" src="<?php echo esc_attr( $remove ); ?>">
        <?php
    }

    public static function get_buffered( $func, $field_id, $label ) {
        ob_start();

        self::$func( $field_id, $label );

        return ob_get_clean();
    }

    public static function text_field( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field text_field">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'text_field' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>
                <?php self::common_text( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function textarea_field( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field textarea_field">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'textarea' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'textarea_field' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>
                <?php self::common_textarea( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function radio_field( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field radio_field wpuf-conditional">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'radio' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'radio_field' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Options', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields wpuf-options">
                    <?php self::radio_fields( $field_id, 'options', $values ); ?>

                    </div> <!-- .wpuf-form-sub-fields -->
                    <?php self::conditional_field( $field_id, $values ); ?>
                </div> <!-- .wpuf-form-rows -->
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function checkbox_field( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field checkbox_field wpuf-conditional">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'checkbox' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'checkbox_field' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Options', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields wpuf-options">
                    <?php self::common_checkbox( $field_id, 'options', $values ); ?>

                    </div> <!-- .wpuf-form-sub-fields -->
                    <?php self::conditional_field( $field_id, $values ); ?>
                </div> <!-- .wpuf-form-rows -->
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function dropdown_field( $field_id, $label, $values = [] ) {
        $first_name  = sprintf( '%s[%d][first]', self::$input_name, $field_id );
        $first_value = $values ? $values['first'] : ' - select -';
        $help        = esc_attr( __( 'First element of the select dropdown. Leave this empty if you don\'t want to show this field', 'wp-user-frontend' ) ); ?>
        <li class="custom-field dropdown_field wpuf-conditional">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'select' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'dropdown_field' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Select Text', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $first_name ); ?>" value="<?php echo esc_attr( $first_value ); ?>" title="<?php echo esc_attr( $help ); ?>">
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Options', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields wpuf-options">
                        <?php self::radio_fields( $field_id, 'options', $values ); ?>
                    </div> <!-- .wpuf-form-sub-fields -->

                    <?php self::conditional_field( $field_id, $values ); ?>
                </div> <!-- .wpuf-form-rows -->
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function multiple_select( $field_id, $label, $values = [] ) {
        $first_name  = sprintf( '%s[%d][first]', esc_attr( self::$input_name ), esc_attr( $field_id ) );
        $first_value = $values ? $values['first'] : ' - select -';
        $help        = esc_attr( __( 'First element of the select dropdown. Leave this empty if you don\'t want to show this field', 'wp-user-frontend' ) ); ?>
        <li class="custom-field multiple_select">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'multiselect' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'multiple_select' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Select Text', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $first_name ); ?>" value="<?php echo esc_attr( $first_value ); ?>" title="<?php echo esc_attr( $help ); ?>">
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Options', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields wpuf-options">
                        <?php self::radio_fields( $field_id, 'options', $values ); ?>
                    </div> <!-- .wpuf-form-sub-fields -->

                    <?php self::conditional_field( $field_id, $values ); ?>
                </div> <!-- .wpuf-form-rows -->
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function website_url( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field website_url">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'url' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'website_url' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>
                <?php self::common_text( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function email_address( $field_id, $label, $values = [] ) {
        ?>
        <li class="custom-field eamil_address">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'email' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'email_address' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>
                <?php self::common_text( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function custom_html( $field_id, $label, $values = [] ) {
        $title_name  = sprintf( '%s[%d][label]', self::$input_name, $field_id );
        $html_name   = sprintf( '%s[%d][html]', self::$input_name, $field_id );
        $title_value = $values ? esc_attr( $values['label'] ) : '';
        $html_value  = $values ? esc_attr( $values['html'] ) : ''; ?>
        <li class="custom-field custom_html">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'html' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'custom_html' ); ?>

            <div class="wpuf-form-holder">
                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Title', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" title="Title of the section" name="<?php echo esc_attr( $title_name ); ?>" value="<?php echo esc_attr( $title_value ); ?>" />
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'HTML Codes', 'wp-user-frontend' ); ?></label>
                    <textarea class="smallipopInput" title="Paste your HTML codes, WordPress shortcodes will also work here" name="<?php echo esc_attr( $html_name ); ?>" rows="10"><?php echo esc_html( $html_value ); ?></textarea>
                </div>

                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function custom_hidden_field( $field_id, $label, $values = [] ) {
        $meta_name    = sprintf( '%s[%d][name]', self::$input_name, $field_id );
        $value_name   = sprintf( '%s[%d][meta_value]', self::$input_name, $field_id );
        $is_meta_name = sprintf( '%s[%d][is_meta]', self::$input_name, $field_id );
        $label_name   = sprintf( '%s[%d][label]', self::$input_name, $field_id );

        $meta_value   = $values ? esc_attr( $values['name'] ) : '';
        $value_value  = $values ? esc_attr( $values['meta_value'] ) : ''; ?>
        <li class="custom-field custom_hidden_field">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'hidden' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'custom_hidden_field' ); ?>

            <div class="wpuf-form-holder">
                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Meta Key', 'wp-user-frontend' ); ?></label>
                    <input type="text" name="<?php echo esc_attr( $meta_name ); ?>" value="<?php echo esc_attr( $meta_value ); ?>" class="smallipopInput" title="<?php esc_html_e( 'Name of the meta key this field will save to', 'wp-user-frontend' ); ?>">
                    <input type="hidden" name="<?php echo esc_attr( $is_meta_name ); ?>" value="yes">
                    <input type="hidden" name="<?php echo esc_attr( $label_name ); ?>" value="">
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Meta Value', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" title="<?php esc_attr_e( 'Enter the meta value', 'wp-user-frontend' ); ?>" name="<?php echo esc_attr(  $value_name ); ?>" value="<?php echo esc_attr( $value_value ); ?>">
                </div>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function section_break( $field_id, $label, $values = [] ) {
        $title_name        = sprintf( '%s[%d][label]', self::$input_name, $field_id );
        $description_name  = sprintf( '%s[%d][description]', self::$input_name, $field_id );

        $title_value       = $values ? esc_attr( $values['label'] ) : '';
        $description_value = $values ? esc_attr( $values['description'] ) : ''; ?>
        <li class="custom-field custom_html">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'section_break' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'section_break' ); ?>

            <div class="wpuf-form-holder">
                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Title', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" title="Title of the section" name="<?php echo esc_attr( $title_name ); ?>" value="<?php echo esc_attr( $title_value ); ?>" />
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Description', 'wp-user-frontend' ); ?></label>
                    <textarea class="smallipopInput" title="Some details text about the section" name="<?php echo esc_attr( $description_name ); ?>" rows="3"><?php echo esc_html( $description_value ); ?></textarea>
                </div> <!-- .wpuf-form-rows -->

                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    /**
     * Render image upload
     *
     * @param $field_id
     * @param $label
     * @param self
     * @param array $values
     */
    public static function image_upload( $field_id, $label, $values = [] ) {
        $max_size_name   = sprintf( '%s[%d][max_size]', self::$input_name, $field_id );
        $max_files_name  = sprintf( '%s[%d][count]', self::$input_name, $field_id );

        $max_size_value  = $values ? $values['max_size'] : '1024';
        $max_files_value = $values ? $values['count'] : '1';

        $help            = esc_attr( __( 'Enter maximum upload size limit in KB', 'wp-user-frontend' ) );
        $count           = esc_attr( __( 'Number of images can be uploaded', 'wp-user-frontend' ) ); ?>
        <li class="custom-field image_upload">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'image_upload' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'image_upload' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, '', true, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Max. file size', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $max_size_name ); ?>" value="<?php echo esc_attr( $max_size_value ); ?>" title="<?php echo esc_attr( $help ); ?>">
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Max. files', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $max_files_name ); ?>" value="<?php echo esc_attr( $max_files_value ); ?>" title="<?php echo esc_attr( $count ); ?>">
                </div> <!-- .wpuf-form-rows -->

                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
    <?php
    }

    /**
     * Render recaptcha
     *
     * @param $field_id
     * @param $label
     * @param array $values
     */
    public static function recaptcha( $field_id, $label, $values = [] ) {
        $title_name          = sprintf( '%s[%d][label]', self::$input_name, $field_id );
        $html_name           = sprintf( '%s[%d][html]', self::$input_name, $field_id );
        $recaptcha_type_name = sprintf( '%s[%d][recaptcha_type]', self::$input_name, $field_id );

        $title_value          = $values ? esc_attr( $values['label'] ) : '';
        $html_value           = isset( $values['html'] ) ? esc_attr( $values['html'] ) : '';
        $recaptcha_type_value = isset( $values['recaptcha_type'] ) ? esc_attr( $values['recaptcha_type'] ) : ( !empty( $values ) ? '' : 'enable_no_captcha' ); ?>
        <li class="custom-field custom_html">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'recaptcha' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'recaptcha' ); ?>

            <div class="wpuf-form-holder">
                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Title', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields">
                        <input type="text" class="smallipopInput" title="Title of the section" name="<?php echo esc_attr( $title_name ); ?>" value="<?php echo esc_attr( $title_value ); ?>" />

                        <div class="description" style="margin-top: 8px;">
                            <?php printf( esc_html( __( "Insert your public key and private key in <a href='%s'>plugin settings</a>. <a href='%s' target='_blank'>Register</a> first if you don't have any keys.", 'wp-user-frontend' ) ), esc_url( admin_url( 'admin.php?page=wpuf-settings' ) ), 'https://www.google.com/recaptcha/' ); ?>
                        </div>
                    </div> <!-- .wpuf-form-rows -->
                </div>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'reCaptcha type', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields">
                        <input type="radio" class="smallipopInput" title="reCaptcha type" name="<?php echo esc_attr( $recaptcha_type_name ); ?>" value="invisible_recaptcha" <?php echo $recaptcha_type_value == 'invisible_recaptcha' ? 'checked' : ''; ?> />
                        <?php esc_html_e( 'Enable Invisible reCaptcha', 'wp-user-frontend' ); ?>
                    </div> <!-- .wpuf-form-rows -->
                    <div class="wpuf-form-sub-fields">
                        <input type="radio" class="smallipopInput" title="reCaptcha type" name="<?php echo esc_attr( $recaptcha_type_name ); ?>" value="enable_no_captcha" <?php echo $recaptcha_type_value == 'enable_no_captcha' ? 'checked' : ''; ?> />
                        <?php esc_html_e( 'Enable noCaptcha', 'wp-user-frontend' ); ?>
                    </div> <!-- .wpuf-form-rows -->
                </div>

                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
    <?php
    }
}
