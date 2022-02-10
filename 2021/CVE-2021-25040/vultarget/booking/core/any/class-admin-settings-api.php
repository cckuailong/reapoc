<?php
/**
 * @version     1.0
 * @package     Settings
 * @category    Abstract Class
 * @author      wpdevelop
 *
 * @web-site    https://wpbookingcalendar.com/
 * @email       info@wpbookingcalendar.com 
 * @modified    2015-10-06
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


abstract class WPBC_Settings_API  {
        
    /**
     * ID of specific Settings page
     * basically its prefix near each generated Input field
     * 
     * @var String
     */
    public $id;
    
    /**
     * Define different options for CLASS - check  more in constructor.
     * 
     * @var array
     */
    public $options;
    
    /**
     * List of all settings rows
     * @var array 
     */
    public $fields = array();
    
    /**
     * Values of all settings fields
     * @var array 
     */
    public $fields_values = array();
    
    /**
     * Validated fields after $_POST request - for example before saving to DB
     * 
     * @var array
     */
    public $validated_fields = array();
    
    
    /**
	 * If setted,  then  we can validate all form  fields, BEFORE post.
     * Each form  field that  we need to  validate have to  have parameter: "validate_as" which  ia array, like array('required')
     * HTML fields elements will  have additional CSS classes,  like "validate-required"
     * If this $validated_form_id empty  so  then  no validation  procedure.
     *
     * @var string 
     */
    public $validated_form_id = '';
    /**
     * Constructor for Settings API
     * 
     * @param string $id        - unique ID of this Settings
     * @param array $options    - Optional. Array of parameters. 
     *                            array(
     *                                  'db_prefix_option'  => 'wpbc_'
     *                                  , 'db_saving_type'  => 'togather'       ['togather'|'separate'|'separate_prefix']
     *                            )
     * @param array $fields_values   - optional array of values. If skipped, then case system try to get values from DB.     
     */
    public function __construct( $id, $options = array(), $fields_values = array() ) {

        $this->id = $id;                                                        // Define name of this settings page        
        
        $default_options = array(
                'db_prefix_option'  => 'wpbc_'                                  // Prefix for adding to option name before saving to DB
              , 'db_saving_type'    => 'separate'                               // Type of DB saving: 'togather' | 'separate' | 'separate_prefix'
                                                                                /*  'togather' - saving all parameters from settings page to  one DB option
                                                                                    'separate' - saving each settings parameters as separate DB options
                                                                                    'separate_prefix' - saving each settings parameters as separate DB options 
                                                                                                        with prefix $id */
        );
        
        $this->options = wp_parse_args( $options, $default_options );

        // Define what  fields we are having
        $this->init_settings_fields();                                          // Init all Fields Rows for settings page
        
        
        // Get Values for the fields
        if ( empty( $fields_values ) ) {                        
            $this->define_fields_values_from_db();                              // Define Fields Values from DB
        } else {                
            $this->define_fields_values( $fields_values );                      // Define Fields by transmited values
        }
        
        // Set Values for the fields
        $this->set_values_to_fields();                                          // Assign $this->fields_values['some_field']   to  $this->fields[ 'some_field' ]['value']        
        
        add_action( 'wpbc_after_settings_content', array($this, 'enqueue_validate_js'), 10, 3 );        // Add JavaScript if you need
    }
    
    
    /**
	 * Validate Form Fields before $_POST request
     * 
     * @param string $page_tag
     */
    public function enqueue_validate_js( $page_tag, $active_page_tab, $active_page_subtab ) {

        if ( empty( $this->validated_form_id ) ) 
            return;
    
        // Get Fields to  validate    
        $fields_to_validate = array();
        foreach ( $this->fields as $field_name => $field_values ) {

            if (  ( isset( $field_values['validate_as'] ) ) && ( ! empty( $field_values['validate_as'] ) )  ) {
                $field_html_id =  $this->id . '_' . $field_name;
                $fields_to_validate[ $field_html_id ] = $field_values['validate_as'];
            }
        }
        // JavaScript //////////////////////////////////////////////////////////////
        
        $js_script = '';

        $js_script .= " jQuery('#". $this->validated_form_id ."').on( 'submit', function(){ ";      // Catch Submit event
            
            foreach ( $fields_to_validate as $field_html_id => $field_validate_array ) {
                
                // Validate Required.
                $js_script .= " if ( jQuery('#". $field_html_id ."').val() == '' ) { " . "\n" ;
                
                $js_script .= "     wpbc_field_highlight( '#". $field_html_id ."' );" . "\n" ;                                
                
                //$js_script .= "     wpbc_scroll_to( '#". $field_html_id ."' );" . "\n" ;                
                //$js_script .= "     showWarningUnderElement( '#". $field_html_id ."' );" . "\n" ;
                
                $js_script .= "     return false; " . "\n" ;                                        // - cancel event - Submit
                
                $js_script .= " }" . "\n" ;
            }
            
        $js_script .= "      } ); " . "\n";
        
        // Eneque JS to  the footer of the page
        wpbc_enqueue_js( $js_script );
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // Abstract methods
    ////////////////////////////////////////////////////////////////////////////        
    
    /**
     * Init all fields rows for settings page
     */
    abstract public function init_settings_fields();
    

    
    
    ////////////////////////////////////////////////////////////////////////////
    // Functions
    ////////////////////////////////////////////////////////////////////////////    
    
    /**
	 * Get array of Default fields Names and Values.
     *  Example:
     *           $default_values = array(
     *                           'my_date_format'           => get_option('date_format') 
     *                         , 'my_time_format'           => get_option('time_format')
     *                       );
     * 
     * @return array
     */
    public function get_default_values() {
        
        $default_values = array();
        
        foreach ( $this->fields as $field_name => $field_data ) {
            if ( isset( $field_data['default'] ) ) {
                $default_values[ $field_name ] = $field_data['default'];
            } else {
                // Action  here if no default value in field.
            }
        }

        return $default_values;
    }    

    
    /**
	 * Define "Default Values" for all exist settings Fields.
     * 
     * @param array $fields_values - Optional. Field values
     */
    public function define_fields_values( $fields_values = array(), $is_check_exist_values_from_fields = true ) {
        
        // Default 
        if ( $is_check_exist_values_from_fields )
            $default_values = $this->get_default_values();
        else 
            $default_values = array();
        
        // Parse
        $this->fields_values = wp_parse_args( $fields_values, $default_values );
        
        if ( $is_check_exist_values_from_fields ) {                             // We no need to  check  it after saving to DB
            $defined_values_in_fields = array();
            foreach ( $this->fields as $field_name => $field_data) {
                if (isset($field_data['value'])) {
                    $defined_values_in_fields[$field_name] = $field_data['value'];
                }
            }
            $this->fields_values = wp_parse_args( $defined_values_in_fields, $this->fields_values );
        }
    }
    

        
    /**
	 * Set Values to Fiels
     *   Assign $this->fields_values   to  $fields[ 'some_field' ]['value']
     */
    public function set_values_to_fields() {
        
        foreach ( $this->fields_values as $field_name => $field_value ) {
            
            if ( isset( $this->fields[ $field_name ] ) ) {
                $this->fields[ $field_name ][ 'value' ] = $field_value;
            }             
        }
    }
    
    
    /**
     * Get ID of this settings page
     * 
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    
    /**
     *  Get Value of specfic Field
     * @param string $field_name
     * @return mixed or false,  if field does not exist
     * 
     */
    public function get_field_value( $field_name ) {
        
        if ( empty( $this->fields_values ) )
            $this->define_fields_values();                                      // If empty,  then define by Default values
        
        return isset( $this->fields_values[ $field_name ] ) ? $this->fields_values[ $field_name ] : false;
    }
    

    
    /**
	 * Set Value to specfic Field
     * @param string $field_name
     * @return mixed or false,  if field does not exist
     * 
     */
    public function set_field_value( $field_name , $field_value ) {
        
        if ( empty( $this->fields_values ) )
            $this->define_fields_values();                                      // If empty,  then define by Default values
        
        $this->fields_values[ $field_name ] = $field_value;
    }
    
    
    /**
     * Get all exist form  fields
     * and Init fields,  if fields are empty.
     * 
     * @return array
     */
    public function get_form_fields() {
        
        if ( empty( $this->fields ) ) {
            $this->init_settings_fields();
        }
        
        return $this->fields;
    }
    
    
    /**
     *  Generate Settings Table
     */
    public function show( $group = false ) {        
        ?>
        <table class="form-table">
            <?php $this->generate_settings_rows( $group ); ?>
        </table><?php        
    }
    
    
    /**
     *  Loop through the fields array and show settings.
     */
    public function generate_settings_rows( $group = false, $form_fields = false ) {

        if ( ! $form_fields ) {
            $form_fields = $this->get_form_fields();
        }

        $html = '';
        foreach ( $form_fields as $k => $v ) {
            
            $k = $this->id . '_' . $k;
            
            if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) {
                $v['type'] = 'text'; 
            }
            
            if (    ( $group === false ) 
                 || ( ( ! isset( $v['group'] ) ) && ( $group == 'general' ) ) 
                 || ( (   isset( $v['group'] ) ) && ( $group == $v['group'] ) ) 
               ) {            
                if ( method_exists( $this, 'field_' . $v['type'] . '_row' ) ) {
                    $html .= $this->{'field_' . $v['type'] . '_row'}( $k, $v );
                } else {
                    $html .= $this->{'field_text_row'}( $k, $v );
                }            
            }
        }

        echo $html;        
    }
    
    
    // <editor-fold     defaultstate="collapsed"                        desc=" G e n e r a t e    F i e l d s "  >
      
    ////////////////////////////////////////////////////////////////////////////
    // Input Fields
    ////////////////////////////////////////////////////////////////////////////
        
    /**
     *  Text Input Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_text_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_text_row_static( $field_name, $field, $echo );
        else 
            self::field_text_row_static( $field_name, $field, $echo );
    }

    
    /**
     *  Text Color Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_color_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_color_row_static( $field_name, $field, $echo );
        else 
            self::field_color_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  Textarea Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_textarea_row( $field_name, $field, $echo = true ) {        
        if ( ! $echo ) 
            return self::field_textarea_row_static( $field_name, $field, $echo );
        else 
            self::field_textarea_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  WP Rich Content Edit Textarea Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_wp_textarea_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_wp_textarea_row_static( $field_name, $field, $echo );
        else 
            self::field_wp_textarea_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     * Radio buttons field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_radio_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_radio_row_static( $field_name, $field, $echo);
        else 
            self::field_radio_row_static( $field_name, $field, $echo);
    }
    
    
    /**
     * Selectbox field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_select_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_select_row_static( $field_name, $field, $echo );
        else 
            self::field_select_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     * Checkbox field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_checkbox_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_checkbox_row_static( $field_name, $field, $echo );
        else 
            self::field_checkbox_row_static( $field_name, $field, $echo );
    }
        
    ////////////////////////////////////////////////////////////////////////////
    // Static Methods
    ////////////////////////////////////////////////////////////////////////////
        
    /**
     *  Static Text Input Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_text_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'description'       => '',
                'attr'              => array(),
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'description_tag'   => 'p'
                , 'validate_as'     => array()
        );

        $field = wp_parse_args( $field, $defaults );
     
        if ( ! $echo ) {
            ob_start();
        }
        
        if ( ! $field['only_field'] ) { 
        ?>
          <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            </th>
            <td><fieldset><?php 
        }    
        ?>                
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>                    
                    <input  type="<?php echo esc_attr( $field['type'] ); ?>" 
                            id="<?php echo esc_attr( $field_name ); ?>" 
                            name="<?php echo esc_attr( $field_name ); ?>" 
                            value="<?php echo esc_attr($field['value']); ?>" 
                            class="regular-text <?php echo esc_attr( $field['class'] ); 
                                                      echo ( ! empty($field['validate_as']) ) ? ' validate-' . implode( ' validate-', $field['validate_as'] ) : ''; ?>" 
                            style="<?php echo esc_attr( $field['css'] ); ?>" 
                            placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
                            <?php echo self::get_custom_attr_static( $field ); ?> 
                            <?php disabled( $field['disabled'], true ); ?> 
                            autocomplete="off"
                        /> 
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {         
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }
    
                
    /**
     *  Static Color Input Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_color_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'description'       => '',
                'attr'              => array(),
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'description_tag'   => 'p'
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        if ( ! $field['only_field'] ) { 
        ?>
          <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            </th>
            <td><fieldset><?php 
        }    
        ?>      
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>                    
                    <input  type="text" 
                            id="<?php echo esc_attr( $field_name ); ?>" 
                            name="<?php echo esc_attr( $field_name ); ?>" 
                            value="<?php echo esc_attr($field['value']); ?>" 
                            class="regular-text wpbc_colorpick <?php echo esc_attr( $field['class'] ); ?>"                                 
                            style="width: 6em; background-color: <?php echo $field['value']; ?>;<?php echo esc_attr( $field['css'] ); ?>" 
                            placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
                            <?php echo self::get_custom_attr_static( $field ); ?> 
                            <?php disabled( $field['disabled'], true ); ?> 
                            autocomplete="off"
                        /> 
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {         
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    /**
     *  Static Textarea Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_textarea_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'description'       => '',
                'attr'              => array(),
                'rows'              => 3, 
                'cols'              => 20, 
                'show_in_2_cols'    => false, 
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'description_tag'   => 'p'
                , 'validate_as'     => array()
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        
        if ( ! $field['only_field'] ) { 
        ?>
          <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            <?php if ( $field['show_in_2_cols'] ) { ?>
                </th>
                <td></td>
              </tr>
              <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
                <td colspan="2">
            <?php } else { ?>    
            </th>
            <td><fieldset>
            <?php } ?><?php 
        }
        ?>                
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>                    
                    <textarea  
                            rows="<?php echo esc_attr( $field['rows'] ); ?>" 
                            cols="<?php echo esc_attr( $field['cols'] ); ?>" 
                            <?php /* type="<?php echo esc_attr( $field['type'] ); ?>" */ ?>
                            id="<?php echo esc_attr( $field_name ); ?>" 
                            name="<?php echo esc_attr( $field_name ); ?>" 
                            class="input-text wide-input <?php echo esc_attr( $field['class'] ); 
                                                               echo ( ! empty($field['validate_as']) ) ? ' validate-' . implode( ' validate-', $field['validate_as'] ) : ''; ?>" 
                            style="<?php echo esc_attr( $field['css'] ); ?>" 
                            placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
                            <?php echo self::get_custom_attr_static( $field ); ?> 
                            <?php disabled( $field['disabled'], true ); ?> 
                            autocomplete="off"
                        ><?php 
                        echo esc_textarea( $field['value'] ); ?></textarea> 
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {         
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    /**
     *  Static WP Rich Content Edit Textarea Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_wp_textarea_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'placeholder'       => '',
                'type'              => 'text',
                'description'       => '',
                'attr'              => array(),
                'rows'              => 3, 
                'cols'              => 20, 
                'teeny'             => true, 
                    'show_visual_tabs'  => true,
                    'default_editor' => 'tinymce',                              // 'tinymce' | 'html'       // 'html' is used for the "Text" editor tab.
                'drag_drop_upload'  => false, 
                'show_in_2_cols'    => false, 
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'description_tag'   => 'p'
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        if ( ! $field['only_field'] ) { 
        ?>
          <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            <?php if ( $field['show_in_2_cols'] ) { ?>
                </th>
                <td></td>
              </tr>
              <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
                <td colspan="2">
            <?php } else { ?>    
            </th>
            <td><fieldset>
            <?php } ?><?php 
        }
        ?>                    
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend><?php                    
                    wp_editor(  
                        $field['value'], 
                        esc_attr( $field_name ),  
                        array(
                              'wpautop'          => false
                            , 'media_buttons'    => false
                            , 'textarea_name'    => esc_attr( $field_name )
                            , 'textarea_rows'    => $field['rows']
                            , 'editor_class'     => 'wpbc-textarea-tinymce ' . esc_attr( $field['class'] )    // Any extra CSS Classes to append to the Editor textarea 
                            , 'teeny'            => esc_attr( $field['teeny'] )                               // Whether to output the minimal editor configuration used in PressThis 
                            , 'drag_drop_upload' => esc_attr( $field['drag_drop_upload'] )                    // Enable Drag & Drop Upload Support (since WordPress 3.9) 
                            , 'tinymce'          => $field['show_visual_tabs']                                // Remove Visual Mode from the Editor        
                            , 'default_editor'   => $field['default_editor']                                  // 'tinymce' | 'html'     // 'html' is used for the "Text" editor tab.
                            )
                    ); 
                   echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {         
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }    
    
    
    /**
     * Static Radio buttons field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_radio_row_static( $field_name, $field, $echo = true ) {

        $defaults = array(
                'title'             => '',
                'label'             => '',
                'disabled'          => false,
                'disabled_options'  => array(),
                'class'             => '',
                'css'               => '',
                'type'              => 'radio',
                'description'       => '',
                'attr'              => array(),
                'options'           => array(), 
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'is_new_line'       => false,
                'description_tag'   => 'span'
        );

        $field = wp_parse_args( $field, $defaults );
        
        if ( ! $echo ) {
            ob_start();
        }
        if ( ! $field['only_field'] ) {
        ?>
        <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            </th>
            <td><fieldset><?php 
        }
        ?>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>                    
                    <?php 
                    foreach ($field['options'] as $option_value => $option_title ) { 
                        
                        $option_parameters = array();
                        if ( is_array( $option_title ) ) {
                            $option_parameters = $option_title;
                            $option_title = $option_title['title'];
                        }
                                          
                  ?><label  class="wpbc-form-radio" title="<?php echo esc_attr( $option_title ); ?>">
                        <input  type="radio" 
                                name="<?php echo esc_attr( $field_name ); ?>" 
                                value="<?php echo  esc_attr( $option_value ); ?>" 
                                class="<?php echo esc_attr( $field['class'] ); ?>"                                 
                                style="<?php echo esc_attr( $field['css'] ); ?>" 
                                <?php echo self::get_custom_attr_static( $field ); ?> 
                                    <?php echo self::get_custom_attr_static( $option_parameters ); ?> 
                                <?php checked(  $field['value'], $option_value ); ?>  
                                <?php disabled( $field['disabled'], true ); ?> 
                                <?php disabled( in_array($option_value, $field['disabled_options'] ), true ); ?> 
                                autocomplete="off"
                            /> <?php echo wp_kses_post( $option_title ); ?></label><?php echo ( $field['is_new_line'] ) ? '<br/>' : '&nbsp; &nbsp; &nbsp;';
                            
                            if ( isset( $option_parameters['html'] ) ) {
                                echo $option_parameters['html'];
                            }
                    } ?>
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {                
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    /**
     * Static Selectbox field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_select_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'label'             => '',
                'disabled'          => false,
                'disabled_options'  => array(),
                'class'             => '',
                'css'               => '',
                'type'              => 'select',
                'description'       => '',
                'multiple'          => false,
                'attr'              => array(),
                'options'           => array(), 
                'group'             => 'general',
                'tr_class'          => '',
                'only_field'        => false,
                'description_tag'   => 'span'
        );

        $field = wp_parse_args( $field, $defaults );
        
        if ( ! $echo ) {
            ob_start();
        }
        
        if ( ! $field['only_field'] ) {
        ?>
        <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>
            </th>
            <td><fieldset>
        <?php         
        }
        ?>                
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>
                    <select
                            id="<?php echo esc_attr( $field_name ); ?>" 
                            name="<?php echo esc_attr( $field_name ); echo ( $field['multiple'] ? '[]' : '' ); ?>" 
                            class="<?php echo esc_attr( $field['class'] ); ?>"                                 
                            style="<?php echo esc_attr( $field['css'] ); ?>" 
                            <?php disabled( $field['disabled'], true ); ?> 
                            <?php echo self::get_custom_attr_static( $field ); ?> 
                            <?php echo ( $field['multiple'] ? ' multiple="MULTIPLE"' : '' ); ?>
                            autocomplete="off"
                        ><?php 
                            
                        foreach (  (array) $field['options'] as $option_value => $option_title ) { 
                            
                            $option_parameters = array();
                            if ( is_array( $option_title ) ) {
                                $option_parameters = $option_title;
                                $option_title = $option_title['title'];
                            }
                                
            
//                            array(         
//                                  'title' => 'Option Group Title'             // Title
//                                , 'optgroup' => true                          // Use only  if you need to show OPTGROUP - Also  need to  use 'title' of start, end 'close' for END
//                                , 'close'  => false
//                            ) 

                            if ( ! empty( $option_parameters['optgroup'] ) ) {                                   // OPTGROUP

                                if ( ! $option_parameters['close'] ) {
                                    ?><optgroup label="<?php  echo esc_attr( $option_parameters['title'] ); ?>"><?php 
                                } else {
                                    ?></optgroup><?php     
                                }
                            } else {                           
                            
                            
                                ?><option value="<?php echo esc_attr( $option_value ); ?>" 
                                          <?php 
                                            if ( (  is_array( $field['value'] ) ) && ( in_array( $option_value, $field['value'] ) ) ) {
                                                selected( true );  
                                            } else {
												if ( ! is_array( $field['value'] ) ) {									//FixIn: 8.1.1.2
													selected( $option_value, $field['value'] );
												}
                                            }
                                          ?> 
                                          <?php disabled( in_array($option_value, $field['disabled_options'] ), true ); ?> 
                                          <?php echo self::get_custom_attr_static( $option_parameters ); ?> 
                                    ><?php echo ( $option_title ); ?></option><?php
                            }
                        } ?>
                    </select>                     
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) {
        ?>
            </fieldset></td>
        </tr>
        <?php
        }
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }
    
    
    /**
     * Static Checkbox field row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_checkbox_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'title'             => '',
                'label'             => '',
                'disabled'          => false,
                'class'             => '',
                'css'               => '',
                'type'              => 'checkbox',
                'description'       => '',
                'attr'              => array(),
                'group'             => 'general',
                'tr_class'          => '', 
                'only_field'        => false,
                'is_new_line'       => true,
                'description_tag'   => 'span'
        );

        $field = wp_parse_args( $field, $defaults );
        
        if ( ! $echo ) {
            ob_start();
        }
        if ( ! $field['only_field'] ) {
        ?>
        <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <th scope="row">
                <?php echo self::label_static( $field_name, $field ); ?>                
            </th>
        <td><fieldset><?php         
        } 
        ?>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>                    
                    <label  class="wpbc-form-checkbox" for="<?php echo esc_attr( $field_name ); ?>">
                        <input  type="checkbox" 
                                id="<?php echo esc_attr( $field_name ); ?>" 
                                name="<?php echo esc_attr( $field_name ); ?>" 
                                value="<?php echo  esc_attr( $field['value'] ); ?>" 
                                class="<?php echo esc_attr( $field['class'] ); ?>"                                 
                                style="<?php echo esc_attr( $field['css'] ); ?>" 
                                <?php echo self::get_custom_attr_static( $field ); ?> 
                                <?php checked(  $field['value'], 'On' ); ?>                                 
                                <?php disabled( $field['disabled'], true ); ?> 
                                autocomplete="off"
                            /> <?php echo wp_kses_post( $field['label'] ); ?></label><?php echo ( $field['is_new_line'] ) ? '<br/>' : '&nbsp; &nbsp; &nbsp;'; ?>
                    <?php echo self::description_static( $field, $field['description_tag'] ); ?>
        <?php 
        if ( ! $field['only_field'] ) { 
        ?>
            </fieldset></td>
        </tr><?php           
        
        } 

        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    ////////////////////////////////////////////////////////////////////////////
    // Text, Help, HTML, JS Elements
    ////////////////////////////////////////////////////////////////////////////
        
    
    /**
     *  Help Info Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_help_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_help_row_static( $field_name, $field, $echo );
        else 
            self::field_help_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  Static Help Info Row
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_help_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'value'             => array(),
                'class'             => '',
                'css'               => '',
                'description'       => '',
                'cols'              => 1, 
                'group'             => 'general',
                'tr_class'          => '',
                'description_tag'   => 'span'
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        ?>
          <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <?php if ( $field['cols'] == 1 ) { ?>
            <th scope="row"></th>
            <td>
            <?php } else { ?>  
            <td colspan="2">    
            <?php }?>
                <div class="wpbc-help-message <?php echo esc_attr( $field['class'] ); ?>" style="margin-top:10px; <?php echo esc_attr( $field['css'] ); ?>">
                    <?php
                    $field['value'] = (array) $field['value'];

                    foreach ( $field['value'] as $help_text ) {
                        ?><p class="description" style="font-weight: 400;"><?php 

                            echo $help_text;

                        ?></p><?php    
                    } 
                    ?>
                </div>
                <?php echo self::description_static( $field, $field['description_tag'] ); ?>
            </td>
        </tr>
        <?php

        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    /**
     *  Horizontal Separator
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_hr_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_hr_row_static( $field_name, $field, $echo );
        else 
            self::field_hr_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  Static Horizontal Separator
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_hr_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'class'             => '',
                'css'               => '',
                'group'             => 'general',
                'tr_class'          => ''
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        ?>
        <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <td style="padding:10px 0px; " colspan="2">
                <div class="<?php echo esc_attr( $field['class'] ); ?>" style="border-bottom:1px solid #cccccc;<?php echo esc_attr( $field['css'] ); ?>" ></div>
            </td>
        </tr>        
        <?php

        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }

    
    /**
     *  HTML Row - show any html. Warning parameter html do not escaped,  check  it before assign.
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_html_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_html_row_static( $field_name, $field, $echo );
        else 
            self::field_html_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  Static HTML Row - show any html. Warning parameter html do not escaped,  check  it before assign.
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_html_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'html'             => '',
                'cols'              => 1, 
                'group'             => 'general',
                'tr_class'          => ''
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        ?>
        <tr valign="top" class="wpbc_tr_<?php echo esc_attr( $field_name ), ' ', esc_attr( $field['tr_class'] ); ?>">
            <?php if ( $field['cols'] == 1 ) { ?>
            <th scope="row"></th>
            <td>
            <?php } else { ?>  
            <td colspan="2">    
            <?php }?>
                <?php echo $field['html']; ?>                
            </td>
        </tr>
        <?php

        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }
    
    
    /**
     *  HTML  - show only this html, Without any Table Rows. Warning parameter html do not escaped,  check  it before assign.
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_pure_html_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_pure_html_row_static( $field_name, $field, $echo );
        else 
            self::field_pure_html_row_static( $field_name, $field, $echo );
    }
    
    
    /**
     *  Static HTML  - show only this html, Without any Table Rows. Warning parameter html do not escaped,  check  it before assign.
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_pure_html_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'html'             => '',
                'group'             => 'general'
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
 
        echo $field['html'];
        
        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }
    
    
    /**
     *  JavaScript Row - insert JavaScript
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public function field_js_row( $field_name, $field, $echo = true ) {
        if ( ! $echo ) 
            return self::field_js_row_static( $field_name, $field, $echo );
        else 
            self::field_js_row_static( $field_name, $field, $echo );    
    }
    
    
    /**
     *  Static JavaScript Row - insert JavaScript
     * 
     * @param string $field_name - name of field
     * @param array $field - parameters
     * @param boolean $echo - show or return reaults {default true}
     * @return string - html || nothing
     */
    public static function field_js_row_static( $field_name, $field, $echo = true ) {
        
        $defaults = array(
                'js'                => '',
                'group'             => 'general'
        );

        $field = wp_parse_args( $field, $defaults );
            
        if ( ! $echo ) {
            ob_start();
        }
        ?>
        <script type="text/javascript">
            <?php echo $field['js']; ?>                            
        </script>
        <?php

        if ( ! $echo ) {
            return ob_get_clean();        
        }
    }        
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Support functions
    ////////////////////////////////////////////////////////////////////////////
        
    /**
     * Get custom attributes
     *
     * @param  array $field
     * @return string
     */
    public static function get_custom_attr_static( $field ) {

        $attributes = array();

        if ( ! empty( $field['attr'] ) && is_array( $field['attr'] ) ) {

            foreach ( $field['attr'] as $attr => $attr_v ) {
                $attributes[] = esc_attr( $attr ) . '="' . esc_attr( $attr_v ) . '"';
            }
        }

        return implode( ' ', $attributes );
    }
    
    
    /**
	 * Get Description
     *
     * @param  array $field
     * @param  string $html_tag - HTML element,  which will be used as separator. Default - 'p' 
     * @return string
     */
    public static function description_static( $field, $html_tag = 'p' ) {
        if ( empty( $html_tag ) ) 
            $html_tag = 'p';
        
        return $field['description'] ? '<'.$html_tag.' class="description">' . wp_kses_post( $field['description'] ) . '</'.$html_tag.'>' . "\n" : '';
    }

    public static function label_static( $field_name, $field ) {                        

        if ( empty( $field['title'] ) )
            return '';
        
        $defaults = array(
                'title'             => '',
                'label_class'             => '',
                'label_css'         => '',
                'type'              => ''
        );
        $field = wp_parse_args( $field, $defaults );
        
        if ( ! empty($field['type'] ) )
            $field['label_class'] .= ' wpbc-form-' . $field['type'];
        
        if ( ! empty( $field['label_css'] ) )
            $field['label_css'] = ' style="' . $field['label_css'] . '"';
        
        return '<label for="' . esc_attr( $field_name ) . '" class="' . $field['label_class'] . '"'.$field['label_css'].'>' . wp_kses_post( $field['title'] ) . '</label>';
    }
    
    // </editor-fold>

    
    ////////////////////////////////////////////////////////////////////////////
    // Validate POSTs
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Validate Settings Field Data.
     *
     * Validate the data on the "Settings" form.
     *
     * @since 1.0.0
     * @uses method_exists()
     * @param array $form_fields (default: array())
     */
    public function validate_post( $form_fields = array() ) {

        // Check  server restrictions in php.ini file relative to  length of $_POST variabales
        if (  function_exists ('wpbc_check_post_key_max_number')) { wpbc_check_post_key_max_number(); }

        if ( ! $form_fields ) {
            $form_fields = $this->get_form_fields();
        }

        $this->validated_fields = array();

        foreach ( $form_fields as $k => $v ) {

            if ( ! empty( $v['is_demo_safe'] ) ) {                              // Skip saving values for fields with  this parameter ( 'is_demo_safe' => true )
                    continue;
            }
            
            if ( empty( $v['type'] ) ) {
                $v['type'] = 'text'; 
            }

            // Look for a validate_FIELDID_post method for special handling
            if ( method_exists( $this, 'validate_' . $k . '_post' ) ) {
                $field = $this->{'validate_' . $k . '_post'}( $this->id . '_' . $k );
                $this->validated_fields[ $k ] = $field;

            // Look for a validate_FIELDTYPE_post method
            } elseif ( method_exists( $this, 'validate_' . $v['type'] . '_post' ) ) {
                $field = $this->{'validate_' . $v['type'] . '_post'}( $this->id . '_' . $k );
                $this->validated_fields[ $k ] = $field;

            // Default to text
            } else {
                if ( isset( $_POST[ $this->id . '_' . $k ] ) ) {                            //Check this for non Fields elements,  like simple HTML or TEXT or JS
                    $field = $this->{'validate_text_post'}( $this->id . '_' . $k );
                    $this->validated_fields[ $k ] = $field;
                }
            }
        }
        
        return $this->validated_fields;
    }


    // <editor-fold     defaultstate="collapsed"                        desc=" V a l i d a t e    P O S T    F i e l d s "  >
    
    /**
     * Validate Text in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public function validate_text_post( $post_key ) {
        return self::validate_text_post_static( $post_key );
    }
    
    
    /**
     * Static Validate Text in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public static function validate_text_post_static( $post_key, $post_index = false ) {

        $value = false;

        if ( $post_index !== false ) {
            $post_value = $_POST[ $post_key ][ $post_index ];
        } else {
            $post_value = $_POST[ $post_key ];
        }
        
        if ( isset( $post_value ) ) {

            $value = wp_kses_post( trim( stripslashes( $post_value ) ) );
        }

        return $value;
    }        


    /**
     * Validate Email field in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public function validate_email_post( $post_key ) {
        return self::validate_email_post_static( $post_key );
    }

    
    /**
     * Static Validate Email field in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public static function validate_email_post_static( $post_key ) {

        $value = false;

        if ( isset( $_POST[ $post_key ] ) ) {
            $value = sanitize_email( trim( stripslashes( $_POST[ $post_key ] ) ) );
        }

        return $value;
    }    
        
    
    /**
     * Validate Textarea in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public function validate_textarea_post( $post_key ) {
        return self::validate_textarea_post_static( $post_key );
    }
    
    
    /**
     * Static Validate Textarea in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public static function validate_textarea_post_static( $post_key ) {

        $value = false;

        if ( isset( $_POST[ $post_key ] ) ) {

            $value = wp_kses(   trim( stripslashes( $_POST[ $post_key ] ) ),
                                array_merge(
                                                array(
                                                        'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
                                                ),
                                                wp_kses_allowed_html( 'post' )
                                )
                    );
        }

            return $value;
    }


    /**
     * Validate WP Textarea in POST request - escape data correctly.     
     * 
     * Same as Textarea
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public function validate_wp_textarea_post( $post_key ){
        return self::validate_wp_textarea_post_static( $post_key );
    }

    
    /**
     * Static Validate WP Textarea in POST request - escape data correctly.     
     * 
     * Same as Textarea
     *
     * @param string $post_key - key for POST 
     * @return string | false,  if no such POST
     */
    public static function validate_wp_textarea_post_static( $post_key ){
        return self::validate_textarea_post_static( $post_key );
    }

    
    /**
     * Validate Checkbox in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string: 'On' | 'Off'
     */
    public function validate_checkbox_post( $post_key ) {
        return self::validate_checkbox_post_static( $post_key );
    }
    
    
    /**
     * Static Validate Checkbox in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string: 'On' | 'Off'
     */
    public static function validate_checkbox_post_static( $post_key ) {

        $status = 'Off';

        if (  isset( $_POST[ $post_key ] ) && ( in_array( $_POST[ $post_key ], array( 'On', 'Off' ) ) )  ) {
            $status = 'On';
        }

        return $status;
    }
    
    
    /**
     * Validate Select in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | array | false,  if no such POST
     */
    public function validate_select_post( $post_key ) {
        return self::validate_select_post_static( $post_key );
    }
    
    
    /**
     * Static Validate Select in POST request - escape data correctly.     
     *
     * @param string $post_key - key for POST 
     * @return string | array | false,  if no such POST
     */
    public static function validate_select_post_static( $post_key ) {

        $value = false;

        if ( isset( $_POST[ $post_key ] ) ) {
            
            if ( is_array(  $_POST[ $post_key ] ) ) {
                $value = array_map( 'sanitize_text_field', array_map( 'stripslashes', (array) $_POST[ $post_key ] ) );
            } else {
                $value = sanitize_text_field( stripslashes( $_POST[ $post_key ] ) );
            }
        }

        return $value;
    }
    

    /**
     * Validate Radio in POST request - escape data correctly.  
     * 
     * Its the same as Select field   
     *
     * @param string $post_key - key for POST 
     * @return string | array | false,  if no such POST
     */
    public function validate_radio_post( $post_key ) {
        return self::validate_radio_post_static( $post_key );
    }
    
    
    /**
     * Static Validate Radio in POST request - escape data correctly.  
     * 
     * Its the same as Select field   
     *
     * @param string $post_key - key for POST 
     * @return string | array | false,  if no such POST
     */
    public static function validate_radio_post_static( $post_key ) {
        return self::validate_select_post_static( $post_key );
    }
        
    // </editor-fold>
    
    ////////////////////////////////////////////////////////////////////////////
    // Save | Get from DB
    ////////////////////////////////////////////////////////////////////////////
        
    /**
     * Save Setting Options to DB,  then Reinit Settings Fields with  these new Values...
     * 
     * @param string $settings_id - ID of the settings
     * @param array $validated_fields  - List of validated fields in format array( field_name => field_value, ... )
     * @param string $how_to_save      - 'togather' - default - save as one field | 'separately_with_prefix' - save separately but with adding settings_id |  'separately' - separately  each  field only with adding db  prefix
     */    
    public function save_to_db( $validated_fields ){       
            
        $settings_id = $this->get_id();
        
        $how_to_save = $this->options['db_saving_type'];
            
        
        if ( $how_to_save == 'togather' ) {
                                                                                /* wpbc_ settings_general = 
                                                                                                            Array (
                                                                                                                    [date_format] => F j, Y
                                                                                                                    [time_format] => H:i
                                                                                                                    [hr_time_format] => 
                                                                                                            ) */
            update_bk_option( $this->options['db_prefix_option'] . $settings_id , $validated_fields );
            
        } elseif ( $how_to_save == 'separate_prefix' ) {                        // wpbc_ settings_general_ date_format => F j, Y
                        
            foreach ( (array) $validated_fields as $field_name => $field_value ) {
                update_bk_option( $this->options['db_prefix_option'] . $settings_id . '_' . $field_name , $field_value );
            }
        } else {                                                                // $how_to_save == 'separate' // wpbc_ date_format => F j, Y            

            foreach ( (array) $validated_fields as $field_name => $field_value ) {
                update_bk_option( $this->options['db_prefix_option'] . $field_name , $field_value );
            }
        } 
          
        
        // Redefine fields values for New Saved values
        $this->define_fields_values( $validated_fields , false );
        
        // Set Values for the fields
        $this->set_values_to_fields();                                          // Assign $this->fields_values['some_field']   to  $this->fields[ 'some_field' ]['value']          
        
        
        $this->fields = apply_filters( 'wpbc_fields_after_saving_to_db', $this->fields, $this->id );
        
//debuge('After Saving to DB', $this->fields_values, $this->fields);        
    }    
    
    
    public function define_fields_values_from_db() {
                
        $how_to_save = $this->options['db_saving_type'];
        
        switch ( $how_to_save ) {
            
            case 'togather':
                
                $fields_values_from_db = get_bk_option( $this->options['db_prefix_option'] . $this->get_id() );

                $this->define_fields_values( $fields_values_from_db );          // Define Fields by values from DB                
                
                break;
            
            case 'separate_prefix':
                    
                $this->define_fields_values();                                  // Reinit Fields - need to know how many fields and names of fields            

                $fields_values_from_db = array();

                foreach ( $this->fields_values as $field_name => $field_value ) {

                    $got_value = get_bk_option( $this->options['db_prefix_option'] . $this->get_id() . '_' . $field_name );
                    
                    // If we do not have this value in DB ( === false ), then do not assing this value - will have  Default Value
                    if ( $got_value !== false )
                         $fields_values_from_db[ $field_name ] = $got_value;
                }

                $this->define_fields_values( $fields_values_from_db );          // Define Fields by values from DB                

                break;
            
            default:    // 'separate'
                    
                $this->define_fields_values();                                  // Reinit Fields - need to know how many fields and names of fields            

                $fields_values_from_db = array();
//debuge('Before Loading from DB', $this->fields_values, $this->fields);
                foreach ( $this->fields_values as $field_name => $field_value ) {

                    $got_value = get_bk_option( $this->options['db_prefix_option'] . $field_name );
                    
                    // If we do not have this value in DB ( === false ), then do not assing this value - will have  Default Value
                    if ( $got_value !== false )
                         $fields_values_from_db[ $field_name ] = $got_value;
                }

                $this->define_fields_values( $fields_values_from_db );          // Define Fields by values from DB
//debuge('After loaded from DB', $this->fields_values, $this->fields);                
                break;
        }
        
    }
    

    
    ////////////////////////////////////////////////////////////////////////////
    //  Install | Uninstall   
    ////////////////////////////////////////////////////////////////////////////

    /**  Actiovation of Plugin. Save to DB initial values of Settings Fields. */    
    public function activate() {

        $settings_id = $this->get_id();
        
        $how_to_save = $this->options['db_saving_type'];
            
        
        $default_values = $this->get_default_values();                          // Get "Default" values from $this->fields array.
        
        if ( $how_to_save == 'togather' ) {
                                                                                /* wpbc_ settings_general = 
                                                                                                            Array (
                                                                                                                    [date_format] => F j, Y
                                                                                                                    [time_format] => H:i
                                                                                                                    [hr_time_format] => 
                                                                                                            ) */
            add_bk_option( $this->options['db_prefix_option'] . $settings_id , $default_values );

        } elseif ( $how_to_save == 'separate_prefix' ) {                        // wpbc_ settings_general_ date_format => F j, Y

            foreach ( (array) $default_values as $field_name => $field_value ) {
                add_bk_option( $this->options['db_prefix_option'] . $settings_id . '_' . $field_name , $field_value );
            }
        } else {                                                                // $how_to_save == 'separate' // wpbc_ date_format => F j, Y            

            foreach ( (array) $default_values as $field_name => $field_value ) {
                add_bk_option( $this->options['db_prefix_option'] . $field_name , $field_value );
            }
        } 
    }

    
    /** Uninstall. Deactivation of Plugin. Delete Settings Fields from DB. */
    public function deactivate() {
        
        $settings_id = $this->get_id();
        
        $how_to_save = $this->options['db_saving_type'];
        
        $default_values = $this->get_default_values();                          // Get "Default" values from $this->fields array.
        
        
        if ( $how_to_save == 'togather' ) {
                                                                                /* wpbc_ settings_general = 
                                                                                                            Array (
                                                                                                                    [date_format] => F j, Y
                                                                                                                    [time_format] => H:i
                                                                                                                    [hr_time_format] => 
                                                                                                            ) */
            delete_bk_option( $this->options['db_prefix_option'] . $settings_id );

        } elseif ( $how_to_save == 'separate_prefix' ) {                        // wpbc_ settings_general_ date_format => F j, Y

            foreach ( (array) $default_values as $field_name => $field_value ) {
                delete_bk_option( $this->options['db_prefix_option'] . $settings_id . '_' . $field_name );
            }
        } else {                                                                // $how_to_save == 'separate' // wpbc_ date_format => F j, Y            

            foreach ( (array) $default_values as $field_name => $field_value ) {
                delete_bk_option( $this->options['db_prefix_option'] . $field_name );
            }
        }         
    }    
    
}