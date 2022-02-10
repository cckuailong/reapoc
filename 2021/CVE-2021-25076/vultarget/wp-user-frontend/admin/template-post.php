<?php
/**
 * Post related form templates
 */
class WPUF_Admin_Template_Post extends WPUF_Admin_Template {

    public static function post_title( $field_id, $label, $values = [] ) {
        ?>
        <li class="post_title">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'post_title' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'post_title', false, $values ); ?>
                <?php self::common_text( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function post_content( $field_id, $label, $values = [] ) {
        $image_insert_name  = sprintf( '%s[%d][insert_image]', self::$input_name, $field_id );
        $image_insert_value = isset( $values['insert_image'] ) ? $values['insert_image'] : 'yes'; ?>
        <li class="post_content">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'textarea' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'post_content' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'post_content', false, $values ); ?>
                <?php self::common_textarea( $field_id, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Enable Image Insertion', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields">
                        <label>
                            <?php self::hidden_field( "[$field_id][insert_image]", 'no' ); ?>
                            <input type="checkbox" name="<?php echo esc_attr( $image_insert_name ); ?>" value="yes"<?php checked( $image_insert_value, 'yes' ); ?> />
                            <?php esc_html_e( 'Enable image upload in post area', 'wp-user-frontend' ); ?>
                        </label>
                    </div>
                </div> <!-- .wpuf-form-rows -->

                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function post_excerpt( $field_id, $label, $values = [] ) {
        ?>
        <li class="post_excerpt">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'textarea' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'post_excerpt' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'post_excerpt', false, $values ); ?>
                <?php self::common_textarea( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function post_tags( $field_id, $label, $values = [] ) {
        ?>
        <li class="post_tags">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'post_tags' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'tags', false, $values ); ?>
                <?php self::common_text( $field_id, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function featured_image( $field_id, $label, $values = [] ) {
        $max_file_name  = sprintf( '%s[%d][max_size]', self::$input_name, $field_id );
        $max_file_value = $values ? $values['max_size'] : '1024';
        $help           = esc_attr( __( 'Enter maximum upload size limit in KB', 'wp-user-frontend' ) ); ?>
        <li class="featured_image">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'image_upload' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'image_upload' ); ?>
            <?php self::hidden_field( "[$field_id][count]", '1' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'featured_image', false, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Max. file size', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $max_file_name ); ?>" value="<?php echo esc_attr( $max_file_value ); ?>" title="<?php echo esc_attr( $help ); ?>">
                </div> <!-- .wpuf-form-rows -->
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function post_category( $field_id, $label, $values = [] ) {
        ?>
        <li class="post_category">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'post_category' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, 'category', false, $values ); ?>
                <?php self::conditional_field( $field_id, $values ); ?>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    public static function taxonomy( $field_id, $label, $taxonomy = '', $values = [] ) {
        $type_name          = sprintf( '%s[%d][type]', self::$input_name, $field_id );
        $order_name         = sprintf( '%s[%d][order]', self::$input_name, $field_id );
        $orderby_name       = sprintf( '%s[%d][orderby]', self::$input_name, $field_id );
        $exclude_type_name  = sprintf( '%s[%d][exclude_type]', self::$input_name, $field_id );
        $exclude_name       = sprintf( '%s[%d][exclude]', self::$input_name, $field_id );
        $woo_attr_name      = sprintf( '%s[%d][woo_attr]', self::$input_name, $field_id );
        $woo_attr_vis_name  = sprintf( '%s[%d][woo_attr_vis]', self::$input_name, $field_id );

        $type_value         = $values ? esc_attr( $values['type'] ) : 'select';
        $order_value        = $values ? esc_attr( $values['order'] ) : 'ASC';
        $orderby_value      = $values ? esc_attr( $values['orderby'] ) : 'name';
        $exclude_type_value = $values ? esc_attr( $values['exclude_type'] ) : 'exclude';
        $exclude_value      = $values ? esc_attr( $values['exclude'] ) : '';
        $woo_attr_value     = $values ? esc_attr( $values['woo_attr'] ) : 'no';
        $woo_attr_vis_value = $values ? esc_attr( $values['woo_attr_vis'] ) : 'no'; ?>
        <li class="taxonomy <?php echo esc_attr( $taxonomy ); ?> wpuf-conditional">
            <?php self::legend( $label, $values, $field_id ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'taxonomy' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'taxonomy' ); ?>

            <div class="wpuf-form-holder">
                <?php self::common( $field_id, $taxonomy, false, $values ); ?>

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Type', 'wp-user-frontend' ); ?></label>
                    <select name="<?php echo esc_attr( $type_name ); ?>">
                        <option value="select"<?php selected( $type_value, 'select' ); ?>><?php esc_html_e( 'Dropdown', 'wp-user-frontend' ); ?></option>
                        <option value="multiselect"<?php selected( $type_value, 'multiselect' ); ?>><?php esc_html_e( 'Multi Select', 'wp-user-frontend' ); ?></option>
                        <option value="checkbox"<?php selected( $type_value, 'checkbox' ); ?>><?php esc_html_e( 'Checkbox', 'wp-user-frontend' ); ?></option>
                        <option value="text"<?php selected( $type_value, 'text' ); ?>><?php esc_html_e( 'Text Input', 'wp-user-frontend' ); ?></option>
                        <option value="ajax"<?php selected( $type_value, 'ajax' ); ?>><?php esc_html_e( 'Ajax', 'wp-user-frontend' ); ?></option>
                    </select>
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Order By', 'wp-user-frontend' ); ?></label>
                    <select name="<?php echo esc_attr( $orderby_name ); ?>">
                        <option value="name"<?php selected( $orderby_value, 'name' ); ?>><?php esc_html_e( 'Name', 'wp-user-frontend' ); ?></option>
                        <option value="id"<?php selected( $orderby_value, 'id' ); ?>><?php esc_html_e( 'Term ID', 'wp-user-frontend' ); ?></option>
                        <option value="slug"<?php selected( $orderby_value, 'slug' ); ?>><?php esc_html_e( 'Slug', 'wp-user-frontend' ); ?></option>
                        <option value="count"<?php selected( $orderby_value, 'count' ); ?>><?php esc_html_e( 'Count', 'wp-user-frontend' ); ?></option>
                        <option value="term_group"<?php selected( $orderby_value, 'term_group' ); ?>><?php esc_html_e( 'Term Group', 'wp-user-frontend' ); ?></option>
                    </select>
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Order', 'wp-user-frontend' ); ?></label>
                    <select name="<?php echo esc_attr( $order_name ); ?>">
                        <option value="ASC"<?php selected( $order_value, 'ASC' ); ?>><?php esc_html_e( 'ASC', 'wp-user-frontend' ); ?></option>
                        <option value="DESC"<?php selected( $order_value, 'DESC' ); ?>><?php esc_html_e( 'DESC', 'wp-user-frontend' ); ?></option>
                    </select>
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Selection Type', 'wp-user-frontend' ); ?></label>
                    <select name="<?php echo esc_attr( $exclude_type_name ); ?>">
                        <option value="exclude"<?php selected( $exclude_type_value, 'exclude' ); ?>><?php esc_html_e( 'Exclude', 'wp-user-frontend' ); ?></option>
                        <option value="include"<?php selected( $exclude_type_value, 'include' ); ?>><?php esc_html_e( 'Include', 'wp-user-frontend' ); ?></option>
                        <option value="child_of"<?php selected( $exclude_type_value, 'child_of' ); ?>><?php esc_html_e( 'Child of', 'wp-user-frontend' ); ?></option>
                    </select>
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'Selection terms', 'wp-user-frontend' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo esc_attr( $exclude_name ); ?>" title="<?php esc_html_e( 'Search the terms name.', 'wp-user-frontend' ); ?>" value="<?php echo esc_attr( $exclude_value ); ?>" />
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows">
                    <label><?php esc_html_e( 'WooCommerce Attribute', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields">
                        <label>
                            <?php self::hidden_field( "[$field_id][woo_attr]", 'no' ); ?>
                            <input type="checkbox" class="woo_attr" name="<?php echo esc_attr( $woo_attr_name ); ?>" value="yes"<?php checked( $woo_attr_value, 'yes' ); ?> />
                            <?php esc_html_e( 'This taxonomy is a WooCommerce attribute', 'wp-user-frontend' ); ?>
                        </label>
                    </div>
                </div> <!-- .wpuf-form-rows -->

                <div class="wpuf-form-rows<?php echo $woo_attr_value == 'no' ? ' wpuf-hide' : ''; ?>">
                    <label><?php esc_html_e( 'Visibility', 'wp-user-frontend' ); ?></label>

                    <div class="wpuf-form-sub-fields">
                        <label>
                            <?php self::hidden_field( "[$field_id][woo_attr_vis]", 'no' ); ?>
                            <input type="checkbox" name="<?php echo esc_attr( $woo_attr_vis_name ); ?>" value="yes"<?php checked( $woo_attr_vis_value, 'yes' ); ?> />
                            <?php esc_html_e( 'Visible on product page', 'wp-user-frontend' ); ?>
                        </label>
                    </div>
                </div> <!-- .wpuf-form-rows -->

                <?php self::conditional_field( $field_id, $values ); ?>
                <div class="wpuf-options">
                    <?php

                    $tax = get_terms( $taxonomy, [
                        'orderby'    => 'count',
                        'hide_empty' => 0,
                    ] );

        $tax = is_array( $tax ) ? $tax : [];

        foreach ( $tax as $tax_obj ) {
            ?>
                        <div>
                            <input type="hidden" value="<?php echo esc_attr( $tax_obj->name ); ?>" data-taxonomy="yes" data-term-id="<?php echo esc_attr( $tax_obj->term_id ); ?>"  data-type="option">
                            <input type="hidden" value="<?php echo esc_attr( $tax_obj->term_id ); ?>" data-taxonomy="yes" data-term-id="<?php echo esc_attr( $tax_obj->term_id ); ?>"  data-type="option_value">
                        </div>
                      <?php
        } ?>
                </div>
            </div> <!-- .wpuf-form-holder -->
        </li>
        <?php
    }

    /**
     * Drop Down portion
     *
     * @param array $param
     */
    public static function render_drop_down_portion( $param = [ 'names_to_hide' => [ 'name' => '', 'value' => '' ], 'names_to_show' => [ 'name' => '', 'value' => '' ], 'option_to_chose' => ['name' => '', 'value' => '' ] ] ) {
        empty( $param['option_to_chose']['value'] ) ? ( $param['option_to_chose']['value'] = 'all' ) : ''; ?>
        <div class="wpuf-form-rows">
            <label><input type="radio" name="<?php echo esc_attr( $param['option_to_chose']['name'] ); ?>" value="<?php esc_html_e( 'all', 'wp-user-frontend' ); ?>" <?php echo  ( $param['option_to_chose']['value'] == 'all' ) ? 'checked' : ''; ?> /><?php esc_html_e( 'Show All', 'wp-user-frontend' ); ?></label>
        </div>
        <div class="wpuf-form-rows">
            <label><input type="radio" name="<?php echo esc_attr( $param['option_to_chose']['name'] ); ?>" value="<?php esc_html_e( 'hide', 'wp-user-frontend' ); ?>" <?php echo  ( $param['option_to_chose']['value'] == 'hide' ) ? 'checked' : ''; ?>  /><?php esc_html_e( 'Hide These Countries', 'wp-user-frontend' ); ?></label>
            <select name="<?php echo esc_attr( $param['names_to_hide']['name'] ); ?>" class="wpuf-country_to_hide" multiple data-placeholder="<?php esc_attr_e( 'Chose Country to hide from List', 'wp-user-frontend' ); ?>"></select>
        </div>

        <div class="wpuf-form-rows">
            <label><input type="radio" name="<?php echo esc_attr( $param['option_to_chose']['name'] ); ?>" value="<?php esc_html_e( 'show', 'wp-user-frontend' ); ?>" <?php echo  ( $param['option_to_chose']['value'] == 'show' ) ? 'checked' : ''; ?>  /><?php esc_html_e( 'Show These Countries', 'wp-user-frontend' ); ?></label>
            <select name="<?php echo esc_attr( $param['names_to_show']['name'] ); ?>" class="wpuf-country_to_hide" multiple data-placeholder="<?php esc_attr_e( 'Add Country to List', 'wp-user-frontend' ); ?>"></select>
        </div>

        <script>
            (function($){
                $(document).ready(function(){
                    var hide_field_name = '<?php echo esc_attr( $param['names_to_hide']['name'] ); ?>';
                    var hide_field_value = JSON.parse('<?php echo json_encode( $param['names_to_hide']['value'] ); ?>');
                    var show_field_name = '<?php echo esc_attr( $param['names_to_show']['name'] ); ?>';
                    var show_field_value = JSON.parse('<?php echo esc_attr( json_encode( $param['names_to_show']['value'] ) ); ?>');
                    var countries = <?php echo esc_attr( wpuf_get_countries( 'json' ) ); ?>;
                    var hide_field_option_string = '';
                    var show_field_option_string = '';

                    for(country in countries){
                        hide_field_option_string = hide_field_option_string + '<option value="'+ countries[country].code +'" '+ (( $.inArray(countries[country].code,hide_field_value) != -1 )?'selected':'') +'>'+ countries[country].name +'</option>';
                        show_field_option_string = show_field_option_string + '<option value="'+ countries[country].code +'" '+ (( $.inArray(countries[country].code,show_field_value) != -1 )?'selected':'') +'>'+ countries[country].name +'</option>';
                    }

                    jQuery('select[name="'+ hide_field_name +'"]').html(hide_field_option_string);
                    jQuery('select[name="'+ show_field_name +'"]').html(show_field_option_string);
                    jQuery('select[name="'+ hide_field_name +'"],select[name="'+ show_field_name +'"]').chosen({allow_single_deselect:true});
                })

            }(jQuery))

        </script>
        <?php
    }
}
