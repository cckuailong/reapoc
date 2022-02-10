<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Files Loading
 * @category Bookings
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


class WPBC_TinyMCE_Buttons {
    
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" I n i t    +    H o o k s" >    
    
    private $settings = array();
    
    function __construct( $params ) {
        
        $this->settings = array(
                                  'tiny_prefix'         => 'wpbc_tiny'
                                , 'tiny_icon_url'       => WPBC_PLUGIN_URL . '/assets/img/bc_black-16x16.png'
                                , 'tiny_js_plugin'      => WPBC_PLUGIN_URL . '/js/wpbc_tinymce_btn.js'
                                , 'tiny_js_function'    => 'wpbc_init_tinymce_buttons'                     // This function NAME exist inside of this JS file: ['tiny_js_plugin']
                                , 'tiny_btn_row'        => 1
                                , 'pages_where_insert'  => array( 'post-new.php', 'page-new.php', 'post.php', 'page.php', 'widgets.php', 'customize.php'  )		//FixIn: 8.8.2.12
                                , 'buttons'             => array(
                                                          'booking_insert' => array(
                                                                                      'hint'  => __('Insert booking calendar' ,'booking')
                                                                                    , 'title' => __('Booking calendar' ,'booking')
                                                                                    , 'img'   => WPBC_PLUGIN_URL . '/assets/img/bc_black-16x16.png'
                                                                                    , 'class' => 'bookig_buttons'
                                                                                    , 'js_func_name_click'    => 'wpbc_tiny_btn_click'
                                                                                    , 'bookmark'              => 'booking'
                                                                                    , 'is_close_bookmark'     => 0
                                                                                )
                                                          )
                            );
        
        $this->settings = wp_parse_args( $params, $this->settings );
        
        add_action( 'init', array( $this, 'define_init_hooks' ) );   // Define init hooks
        
    }
    
    /** Init all hooks for showing Button in Tiny Toolbar */
    public function define_init_hooks() {


        // Don't bother doing this stuff if the current user lacks permissions
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )  return;

        if (  ( in_array( basename($_SERVER['PHP_SELF'] ),  $this->settings['pages_where_insert'] ) ) 
              // && ( get_user_option('rich_editing') == 'true' )
            ) {

            // Load JS file  - TinyMCE plugin
            add_filter( 'mce_external_plugins', array( $this, 'load_tiny_js_plugin' ) );

            // Add the custom TinyMCE buttons
            if ( 1 === $this->settings['tiny_btn_row'] ) add_filter( 'mce_buttons',                                     array( $this, 'add_tiny_button' ) );
            else                                         add_filter( 'mce_buttons_' . $this->settings['tiny_btn_row'] , array( $this, 'add_tiny_button' ) );
                                                                                    
            // Add the old style button to the non-TinyMCE editor views
			//Fix: 8.4.2.10 - compatibility with Gutenberg 4.1- 4.3 ( or newer ) at edit post page.
            //add_action( 'edit_form_advanced',   array( $this, 'add_html_button' ) );                 // Fires after 'normal' context meta boxes have been output
            add_action( 'edit_page_form',       array( $this, 'add_html_button' ) );
            add_action( 'admin_head',           array( $this, 'insert_button') );
            add_action( 'admin_footer',         array( $this, 'modal_content' ) );
            
            // JS        
            wp_enqueue_script(      'wpdevelop-bootstrap',       wpbc_plugin_url( '/assets/libs/bootstrap/js/bootstrap.js' ), array( 'jquery' ),              '3.3.5.1');        
            // Define proxy wpbc_model no conflict  object - usage like jQuery('#wpbc_tiny_modal').wpbc_modal({
            wp_enqueue_script(      'wpbc-wpdevelop-bootstrap',  wpbc_plugin_url( '/js/wpbc_bs_no_conflict.js' ),             array( 'wpdevelop-bootstrap' ), '1.0' );        
            
            // // Can not use this,  because its start support only  from WP 4.5 :(
            // // wp_add_inline_script(   'wpdevelop-bootstrap', "var wpbc_modal = jQuery.fn.modal.noConflict(); jQuery.fn.wpbc_modal = wpbc_modal;" );   // Define proxy wpbc_model no conflict  object - usage like jQuery('#wpbc_tiny_modal').wpbc_modal({

            // wp_enqueue_script( 'jquery-ui-dialog'  );


            // CSS
            wp_enqueue_style( 'wpdevelop-bts',              wpbc_plugin_url( '/assets/libs/bootstrap/css/bootstrap.css' ),          array(), '3.3.5.1');
            wp_enqueue_style( 'wpdevelop-bts-theme',        wpbc_plugin_url( '/assets/libs/bootstrap/css/bootstrap-theme.css' ),    array(), '3.3.5.1');
            
            wp_enqueue_style( 'wpbc-admin-support',         wpbc_plugin_url( '/core/any/css/admin-support.css' ),       array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-modal-popups',    wpbc_plugin_url( '/css/modal.css' ),                                    array(), WP_BK_VERSION_NUM);            
            
            wp_enqueue_style( 'wpbc-admin-pages',           wpbc_plugin_url( '/css/admin.css' ),                        array(), WP_BK_VERSION_NUM);            
            wp_enqueue_style( 'wpbc-admin-menu',            wpbc_plugin_url( '/core/any/css/admin-menu.css' ),          array(), WP_BK_VERSION_NUM);
            //wp_enqueue_style( 'wpbc-admin-toolbar',         wpbc_plugin_url( '/core/any/css/admin-toolbar.css' ),       array(), WP_BK_VERSION_NUM);
            
            add_action( 'admin_footer',         array( $this, 'write_js' ) );   //Write JavaScript


			//FixIn: 8.8.2.12
            add_action( 'customize_controls_print_footer_scripts',       	array( $this, 'add_html_button' ) );
            add_action( 'customize_controls_print_footer_scripts',          array( $this, 'insert_button') );
            add_action( 'customize_controls_print_footer_scripts',         	array( $this, 'modal_content' ) );
            add_action( 'customize_controls_print_footer_scripts',         	array( $this, 'write_js' ) );   //Write JavaScript
        }            
    }
    //                                                                              </editor-fold>
                                                                                  
                                                                                  
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" TinyMCE - Add Button " >    

    /**
	 * Load JS file  - TinyMCE plugin
     * 
     * @param array $plugins
     * @return array
     */
    public function load_tiny_js_plugin( $plugins ){
    
        $plugins[ $this->settings['tiny_prefix'] . '_quicktags'] = $this->settings['tiny_js_plugin'];
        
        return $plugins;
    }
    
    
    /**
	 * Add the custom TinyMCE buttons
     * 
     * @param array $buttons
     * @return array
     */
    public function add_tiny_button( $buttons ) {
                
        array_push( $buttons, "separator" );
        
        foreach ( $this->settings['buttons'] as $type => $strings ) {
            array_push( $buttons, $this->settings['tiny_prefix'] . '_' . $type );
        }

        return $buttons;        
    }
    
    
    /** Add the old style button to the non-TinyMCE editor views */
    public function add_html_button() {
        
        $buttonshtml = '';
        $datajs='';
        
        foreach ( $this->settings['buttons'] as $type => $props ) {

            $buttonshtml .= '<input type="button" class="ed_button button button-small" onclick="'
                                .$props['js_func_name_click'].'(\'' . $type . '\')" title="' . $props['hint'] . '" value="' . $props['title'] . '" />';

            $datajs.= " wpbc_tiny_btn_data['$type'] = {\n";
            $datajs.= '		title: "' . esc_js( $props['title'] ) . '",' . "\n";
            $datajs.= '		tag: "' . esc_js( $props['bookmark'] ) . '",' . "\n";
            $datajs.= '		tag_close: "' . esc_js( $props['is_close_bookmark'] ) . '",' . "\n";
            $datajs.= '		cal_count: "' . get_bk_option( 'booking_client_cal_count' )  . '"' . "\n";
            $datajs.=  "\n	};\n";
        }
        
        ?><script type="text/javascript">
            // <![CDATA[
            
                function wpbc_add_html_button_to_toolbar(){                     // Add buttons  ( HTML view )
                    if ( jQuery( '#ed_toolbar' ).length == 0 ) 
                        setTimeout( 'wpbc_add_html_button_to_toolbar()' , 100 );
                    else 
                        jQuery("#ed_toolbar").append( '<?php echo wp_specialchars_decode( esc_js( $buttonshtml ), ENT_COMPAT ); ?>' );
                }                
                jQuery(document).ready(function(){ 
                    setTimeout( 'wpbc_add_html_button_to_toolbar()' , 100 );
                });

                var selected_booking_shortcode = 'bookingform';
                var wpbc_tiny_btn_data={};
                <?php echo $datajs; ?>
                
            // ]]>
        </script><?php
        
    }
    
    
    public function insert_button() {
        
        $script = '';
    
        if ( ! empty( $this->settings['buttons'] ) ){
            
            $script .= '<script type="text/javascript">';

            $script .= ' function '. $this->settings['tiny_js_function'] . '(ed, url) {';

                foreach ( $this->settings['buttons'] as $type => $props ) {

                    $script .=  " if ( typeof ".$props['js_func_name_click']." == 'undefined' ) return; ";
                    $script .=  "  ed.addButton('".  $this->settings['tiny_prefix'] . '_' . $type ."', {";
                    $script .=  "		title : '". $props['hint'] ."',";
                    $script .=  "		image : '". $props['img'] ."',";
                    $script .=  "		onclick : function() {";
                    $script .=  "			". $props['js_func_name_click'] ."('". $type ."');";
                    $script .=  "		}";
                    $script .=  "	});";
                }
            
            $script .=  ' }';

            $script .= '</script>';
            
            echo $script;
        }        
    }
    
    //                                                                              </editor-fold>
    
    
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" M o d a l    C o n t e n t " >    
    public function modal_content() {
        
        ?><span class="wpdevelop wpbc_page"><div class="visibility_container clearfix-height" style="display:block;"><?php
        ?><div id="wpbc_tiny_modal" class="modal wpbc_popup_modal wpbc_tiny_modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">   
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php 
                            _e('Insert Shortcode' ,'booking'); 
                            echo ' - <span class="wpbc_shortcode_title">';  
                            _e('Booking Calendar', 'booking');
                            echo '</span>';  
                        ?></h4>                    
                    </div>
                    <div class="modal-body">
                        <div class="clear" style="height:5px;"></div>
                        <input name="wpbc_shortcode_type" id="wpbc_shortcode_type" value="booking" autocomplete="off" type="hidden" />
                        <?php
                        
                        // Tabs 
                        wpbc_bs_toolbar_tabs_html_container_start();

                            $wpbc_tabs = array();
                            $wpbc_tabs[ 'booking' ] = __( 'Booking Form' ,'booking');
                            $wpbc_tabs[ 'bookingtimeline' ] = __( 'TimeLine' ,'booking');
                            $wpbc_tabs[ 'bookingcalendar' ] = __( 'Calendar' ,'booking');
                            if ( class_exists( 'wpdev_bk_personal' ) ) { 
                                $wpbc_tabs[ 'bookingselect' ] = __( 'Resources Selection' ,'booking');
                                if ( class_exists( 'wpdev_bk_biz_l' ) ) {
                                    $wpbc_tabs[ 'bookingsearch' ] = __( 'Search' ,'booking');
                                    $wpbc_tabs[ 'bookingform' ] = __( 'Only Form' ,'booking');
                                }
                                 
                                $wpbc_tabs[ 'bookingother' ] = __( 'Other' ,'booking');
                            }

                            foreach ( $wpbc_tabs as $key => $title ) {

                                wpbc_bs_display_tab(   array(
                                                                'title'         => $title 
                                                                // , 'hint' => array( 'title' => __('Manage bookings' ,'booking') , 'position' => 'top' )
                                                                , 'onclick'     =>    "jQuery( '.wpbc_tiny_modal .visibility_container').hide();"
                                                                                    . "jQuery( '#wpbc_tiny_container_". $key ."' ).show();"
                                                                                    . "jQuery( '#wpbc_shortcode_type' ).val('" . $key . " ');"
                                                                                    . "jQuery( '.wpbc_tiny_modal .nav-tab').removeClass('nav-tab-active');"
                                                                                    . "jQuery( this ).addClass('nav-tab-active');"
                                                                                    . "jQuery( '.wpbc_tiny_modal .nav-tab i.icon-white').removeClass('icon-white');"
                                                                                    . "jQuery( '.wpbc_tiny_modal .nav-tab-active i').addClass('icon-white');"
                                                                                    . "wpbc_set_shortcode();"                                    

                                                                , 'font_icon'   => ''
                                                                , 'default'     => ( $key == 'booking' ) ? true : false
                                                                , 'checkbox'    => false
                                                ) ); 
                            }

                        wpbc_bs_toolbar_tabs_html_container_end();

                        wpbc_clear_div();

                        foreach ( $wpbc_tabs as $key => $title ) {

                                ?><div id="wpbc_tiny_container_<?php echo $key; ?>" class="visibility_container clearfix-height" style="<?php echo ( ( $key == 'booking' ) ? '' : 'display:none;' ); ?>"><?php 

                                    if ( method_exists( $this, 'shortcode_' . $key ) ) {
                                        $this->{'shortcode_' . $key}( $key );
                                    }            

                                ?></div><?php 
                        }

                        wpbc_clear_div();
                    ?>
                        <input name="wpbc_text_put_in_shortcode" id="wpbc_text_put_in_shortcode" class="put-in" readonly="readonly" onfocus="this.select()" type="text" />
						<?php //FixIn: 8.3.3.99 ?>
						<input name="wpbc_text_gettenberg_section_id" id="wpbc_text_gettenberg_section_id" type="text" style="display: none;" />
                    </div>
                    <div class="modal-footer" style="text-align:center;"> 

                        <a href="javascript:void(0)" class="button button-primary"  style="float:none;"                                        
                           onclick="javascript:wpbc_send_text_to_editor( jQuery('#wpbc_text_put_in_shortcode').val().trim() );wpbc_tiny_close();"
                           ><?php _e( 'Insert into page' ); ?></a> <a href="javascript:void(0)" class="button" style="float:none;" data-dismiss="modal"><?php _e('Close' ,'booking'); ?></a>

                   </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <?php  
        ?></div></span><?php        
    }
    //                                                                              </editor-fold>
    
    
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" S u p p o r t " >    
    function get_resources() {
    
        if ( ! class_exists( 'wpdev_bk_personal' ) ) 
            return array();
        
        $resources_cache = wpbc_br_cache();                                     // Get booking resources from  cache        
        
        $resource_objects = $resources_cache->get_resources();
        // $resource_objects = $resources_cache->get_single_parent_resources();
        
        //$resource_options = $params['resources'];

		$resource_options = array();    //FixIn: 8.2.1.12

        foreach ( $resource_objects as $br) {

            $br_option = array();
            $br_option['title'] = apply_bk_filter('wpdev_check_for_active_language', $br['title'] );

            if ( (isset( $br['parent'] )) && ($br['parent'] == 0 ) && (isset( $br['count'] )) && ($br['count'] > 1 ) )
                $br_option['title'] .= ' [' . __('parent resource', 'booking') . ']';

            $br_option['attr'] = array();
            $br_option['attr']['class'] = 'wpbc_single_resource';
            if ( isset( $br['parent'] ) ) {
                if ( $br['parent'] == 0 ) {
                    if (  ( isset( $br['count'] ) ) && ( $br['count'] > 1 )  )
                        $br_option['attr']['class'] = 'wpbc_parent_resource';
                } else {
                    $br_option['attr']['class'] = 'wpbc_child_resource';
                }
            } 

            $sufix = '';

            $resource_options[ $br['id'] . $sufix ] = $br_option;

            if ( $resource_options[ $br['id'] ]['attr']['class'] === 'wpbc_child_resource' ) {
                $resource_options[ $br['id'] ]['title'] = ' &nbsp;&nbsp;&nbsp; ' . $resource_options[ $br['id'] ]['title'];
            }
        }
        return $resource_options;
    }
    //                                                                              </editor-fold>
    
    
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" Shortcode   C o n t e n t   Sections " >    
    
        public function shortcode_bookingtimeline( $shortcode_section_key ){ 

            ?><table class="form-table"><tbody><?php   

                ////////////////////////////////////////////////////////////////////
                // Booking Resources
                ////////////////////////////////////////////////////////////////////        
                if ( class_exists( 'wpdev_bk_personal' ) ){ 

                    WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_type'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Booking resources', 'booking')
                                                                        , 'description'       => __( 'Select booking resources. Please use CTRL to select multiple booking resources.', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => true
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $this->get_resources()
                                                                    )
                                    );
                }

                ////////////////////////////////////////////////////////////////////
                // Type of View
                ////////////////////////////////////////////////////////////////////
                $view_days_optiopns = array();  
                if ( class_exists( 'wpdev_bk_personal' ) ) {
                    $view_days_optiopns[ 1 ] = array( 'title' => __('Day', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_matrix' ) );
                    $view_days_optiopns[ 7 ] = array( 'title' => __( 'Week', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_matrix' ) );
                }
                $view_days_optiopns[ 30 ] = array( 'title' => __( 'Month', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_single wpbc_bookingtimeline_matrix' ) );
                if ( class_exists( 'wpdev_bk_personal' ) ) {
                    $view_days_optiopns[ 60 ] = array( 'title' => __( '2 Months', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_matrix' ) );
                }                                                    
                $view_days_optiopns[ 90 ] = array( 'title' => __( '3 Months', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_single' ) );
                $view_days_optiopns[ 365 ] = array( 'title' => __( 'Year', 'booking' ), 'attr' => array( 'class' => 'wpbc_bookingtimeline_single' ) );
                                                                                            
                WPBC_Settings_API::field_select_row_static(   'wpbc_bookingtimeline_view_days_num'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('View mode', 'booking')
                                                                    , 'description'       => __('Select type of view format' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 30
                                                                    , 'options'           => $view_days_optiopns
                                                                )
                                );
                
                ////////////////////////////////////////////////////////////////////
                // Label near selectbox
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingtimeline_header_title'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('Label', 'booking')
                                                                , 'placeholder'       => str_replace( array( '"', "'" ), '', ucwords( __('All bookings', 'booking' ) ) )
                                                                , 'description'       => __('Title', 'booking') . '  (' . __('optional', 'booking') . ').'
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => str_replace( array( '"', "'" ), '', ucwords( __('All bookings', 'booking' ) ) )
                                                            )
                                        );

//                // Links
//                WPBC_Settings_API::field_html_row_static( 'wpbc_bookingtimeline_advanced_section'
//                                                        , array(  
//                                                                'type' => 'html'
//                                                              , 'html'  =>  
//                                                                        '<strong><a id="'.$shortcode_section_key . '_show_link" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings' ,'booking') . '</a>'
//                                                                      . '<a id="'.$shortcode_section_key . '_hide_link" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings' ,'booking') . '</a></strong>'
//                                                              , 'cols'  => 1
//                                                              , 'group' =>  $shortcode_section_key
//                                                            )
//                                        );
                
                ////////////////////////////////////////////////////////////////////
                // Scroll Months Number
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_select_row_static(   'wpbc_bookingtimeline_scroll_month'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Number of months to scroll', 'booking')
                                                                    , 'description'       => __('Select number of months to scroll after loading' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section wpbc_bookingtimeline_scroll_month'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 0
                                                                    , 'options'           => array_combine( range( -12, 12 ), range( -12, 12 ) )
                                                                )
                                );
                
                ////////////////////////////////////////////////////////////////////
                // Scroll Days Number
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_select_row_static(   'wpbc_bookingtimeline_scroll_day'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Number of days to scroll', 'booking')
                                                                    , 'description'       => __('Select number of days to scroll after loading' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section wpbc_bookingtimeline_scroll_day'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 0
                                                                    , 'options'           => array_combine( range( -90, 90 ), range( -90, 90 ) )
                                                                )
                                );
                
                
                ////////////////////////////////////////////////////////////////////
                // Start Date
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_advanced_section'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_bookingtimeline_scroll_start_date_active" class="wpbc-form-text"><?php  _e('Start Date' ,'booking'); ?></label></th>                
                    <td class=""><fieldset><?php 
                    
                        WPBC_Settings_API::field_checkbox_row_static( 'wpbc_bookingtimeline_scroll_start_date_active'  
                                                                    , array(
                                                                              'type'              => 'checkbox'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'class'             => ''
                                                                            , 'css'               => ''
                                                                            , 'tr_class'          => ''
                                                                            , 'attr'              => array()
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'only_field'        => true
                                                                            , 'is_new_line'       => false
                                                                            , 'value'             => false
                                                                        )
                                );
                    
                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_scroll_start_date_year'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:5em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date( 'Y' )
                                                                            , 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
                                                                        )
                                        );   
                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_scroll_start_date_month'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('n')
                                                                            , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                        )
                                        );   

                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_scroll_start_date_day'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('j')
                                                                            , 'options'           => array_combine( range( 1, 31 ), range( 1, 31 ) )
                                                                        )
                                        );   

                        ?><span class="description"> <?php _e('Select start date' ,'booking'); ?></span></fieldset></td>
                </tr><?php 
                
                
                //FixIn: 7.0.1.17
                
                ////////////////////////////////////////////////////////////////////
                // Select Limit Times
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_view_times'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_bookingtimeline_limit_hours" class="wpbc-form-text"><?php  _e( 'Show', 'booking' ); ?></label></th>                
                    <td class=""><fieldset><?php 
                
                        ?><span style="font-weight:400;"> <?php echo strtolower(__('From', 'booking') ); ?> </span><?php
                    
                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_limit_hours_start_time'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => 0
                                                                            , 'options'           => array_combine( range( 0, 23 ), range( 0, 23 ) )
                                                                        )
                                        );   

                        ?><span style="font-weight:400;"> <?php echo strtolower(__('Until', 'booking') ); ?> </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingtimeline_limit_hours_end_time'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => 24
                                                                            , 'options'           => array_combine( range( 1, 24 ), range( 1, 24 ) )
                                                                        )
                                        );   
                    
                        ?><span class="description"> <?php _e( 'hours', 'booking' ); ?></span></fieldset></td>
                </tr><?php 
                
            ?></tbody></table><?php              
            
echo "<pre style='display:none;'>
Parameters for possible usage of shortcode:    
<strong>Matrix view:</strong>
<strong>Day (1):</strong>
[bookingtimeline type='1,5,6,7' view_days_num=1 header_title='All Bookings' scroll_day=4 scroll_start_date='2016-11-17']
<strong>Week (7):</strong>
[bookingtimeline type='1,5,6,7' view_days_num=7 header_title='All Bookings' scroll_day=4 scroll_start_date='2016-11-17']
<strong>Month (30):</strong>
[bookingtimeline type='1,5,6,7' header_title='All Bookings' scroll_month=2 scroll_start_date='2016-11-17']
<strong>2months (60):</strong>
[bookingtimeline type='1,5,6,7' view_days_num=60 header_title='All Bookings' scroll_month=2 scroll_start_date='2016-11-17']

<hr/><strong>Single view:</strong>
<strong>Month(30):</strong>
[bookingtimeline type='1' header_title='All Bookings' scroll_day=4 scroll_start_date='2016-11-17']
<strong>3 Months(90):</strong>
[bookingtimeline type='1' view_days_num=90 header_title='All Bookings' scroll_day=5 scroll_start_date='2016-11-17']
<strong>Year (365):</strong>
[bookingtimeline type='1' view_days_num=365 header_title='All Bookings' scroll_month=3 scroll_start_date='2016-11-17']
</pre>";            
        }

    
        public function shortcode_booking( $shortcode_section_key ){         

            ?><table class="form-table"><tbody><?php   

                ////////////////////////////////////////////////////////////////////
                // Booking Resources
                ////////////////////////////////////////////////////////////////////        
                if ( class_exists( 'wpdev_bk_personal' ) ){ 

                    WPBC_Settings_API::field_select_row_static(  'wpbc_booking_type'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Booking resource', 'booking')
                                                                        , 'description'       => __( 'Select booking resource', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => false
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $this->get_resources()
                                                                    )
                                    );
                }


                ////////////////////////////////////////////////////////////////////
                // Custom form
                ////////////////////////////////////////////////////////////////////
                if ( class_exists( 'wpdev_bk_biz_m' ) ) 
                        wpbc_in_settings__form_selection( array( 
                                                                  'name'        => 'wpbc_booking_form_type'
                                                                , 'title'       => __('Booking Form', 'booking') 
                                                                , 'description' => __('Select default custom booking form' ,'booking')
                                                                , 'group'       => $shortcode_section_key                        
                                                            )            
                                                    );

                ////////////////////////////////////////////////////////////////////
                // Calendar Months Number
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_select_row_static(   'wpbc_booking_nummonths'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Visible months', 'booking')
                                                                    , 'description'       => __('Select number of month to show for calendar.' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => get_bk_option( 'booking_client_cal_count' )
                                                                    , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                )
                                );


                ////////////////////////////////////////////////////////////////////
                // Start Month
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_booking_startmonth_active" class="wpbc-form-text"><?php  _e('Start month:', 'booking'); ?></label></th>                
                    <td class=""><fieldset><?php 

                        WPBC_Settings_API::field_checkbox_row_static( 'wpbc_booking_startmonth_active'  
                                                                    , array(
                                                                              'type'              => 'checkbox'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'class'             => ''
                                                                            , 'css'               => ''
                                                                            , 'tr_class'          => ''
                                                                            , 'attr'              => array()
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'only_field'        => true
                                                                            , 'is_new_line'       => false
                                                                            , 'value'             => false
                                                                        )
                                );

                        WPBC_Settings_API::field_select_row_static(  'wpbc_booking_startmonth_year'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:5em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date( 'Y' )
                                                                            , 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
                                                                        )
                                        );   
                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_booking_startmonth_month'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('n')
                                                                            , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                        )
                                        );   

                        ?><span class="description"> <?php _e('Select start month of calendar' ,'booking'); ?></span></fieldset></td>
                </tr><?php 

                WPBC_Settings_API::field_html_row_static( 'advanced_section'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        '<strong><a id="'.$shortcode_section_key . '_show_link" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings' ,'booking') . '</a>'
                                                                      . '<a id="'.$shortcode_section_key . '_hide_link" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings' ,'booking') . '</a></strong>'
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section2'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        wpbc_show_message_in_settings( 
                                                                                sprintf( __( 'Setting advanced parameters of the calendar. %sLike width, height and structure %s' ,'booking'), '<br/><em>', '</em>' )
                                                                                . ( ( class_exists('wpdev_bk_biz_m') ) ? sprintf( __( '%s or minimum and fixed number of days selection for the specific day of week or season.%s', 'booking' ), '<em>', '</em>' ) : '' )
                                                                                , 'info', '' , false )
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                              , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                            )
                                        );
                WPBC_Settings_API::field_textarea_row_static( 'wpbc_booking_options'
                                                            , array(  
                                                                      'type'              => 'textarea'
                                                                    , 'title'             => __('Options', 'booking')
                                                                    , 'description'       => ''
                                                                    , 'placeholder'       => '{calendar months_num_in_row=2 width=100% cell_height=40px}'
                                                                    , 'description_tag'   => 'span'
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'width:99%;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => ''
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'rows'              => 3
                                                                    , 'cols'              => 20
                                                                    , 'show_in_2_cols'    => false
                                                                )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section_help2'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  wpbc_show_message_in_settings( 
                                                                                      '<strong style="padding-left: 2em;line-height:2em;">'
                                                                                    . sprintf( __( 'Please read more about the possible customizations of these %soptions%s %shere%s', 'booking' ), '<strong>', '</strong>', '<a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/" target="_blank">', '</a>' )
                                                                                    . '</strong>'
                                                                                    . '<ol style="list-style-type:disc;line-height:2em;">'
                                                                                    . '<li>'
                                                                                    . sprintf( __( 'Specify the full width of calendar, height of date cell and number of months in one row. ', 'booking' ) )
                                                                                    . '<br/><strong>' . __( 'Description', 'booking' ) . ': </strong>'
                                                                                    . __( 'Calendar have 2 months in a row, the cell height is 30px and calendar full width 568px (possible to use percentage for width: 100%)', 'booking' )
                                                                                    . '<br/><strong>' . __( 'Code Example', 'booking' ) . ': </strong>'
                                                                                    . '<code>{calendar months_num_in_row=2 width=568px cell_height=30px}</code>'
                                                                                    . '</li>'
                                                                                    . ( ( class_exists('wpdev_bk_biz_m') ) ?
                                                                                      '<li>'                                                                                
                                                                                    . sprintf( __( 'Specify that during certain seasons (or days of week), the specific minimum number of days must be booked. ', 'booking' ) )
                                                                                    . '<br/><strong>' . __( 'Description', 'booking' ) . ': </strong>'
                                                                                    . __( 'Visitor can select only 4 days starting at Monday, 3 or 7 days – Friday, 2 days – Saturday, etc…', 'booking' )
                                                                                    . '<br/><strong>' . __( 'Code Example', 'booking' ) . ': </strong>'
                                                                                    . '<code>{select-day condition="weekday" for="1" value="4"},{select-day condition="weekday" for="5" value="3,7"},{select-day condition="weekday" for="6" value="2"}</code>'
                                                                                    . '</li>'
                                                                                    : '' )
                                                                  
                                                                                    . '<li>'                                                                      
                                                                                    . sprintf(__('Please, read more about the shortcodes %shere%s or JavaScript customization of the specific shortcodes %shere%s' ,'booking')
                                                                                                , '<a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/" target="_blank">', '</a>'
                                                                                                , '<a href="https://wpbookingcalendar.com/faq/advanced-javascript-for-the-booking-shortcodes/" target="_blank">','</a>')
                                                                                    . '</li>'
                                                                                    . '</ol>'
                                                                                , 'warning', '', false )
                                                              , 'cols'  => 1
                                                              , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );

                if ( class_exists( 'wpdev_bk_personal' ) ){ 
                    $resources_list_orig = $this->get_resources();
                    $resources_list = array();
                    $resources_list[0] = array( 'title' => __('None', 'booking' ), 'attr' => array ( 'class' => 'wpbc_single_resource', 'style' => 'border-bottom:1px dashed #ddd;') );
                    foreach ($resources_list_orig as $res_id => $res_val) {
                        $resources_list[$res_id] = $res_val;
                    }

                    WPBC_Settings_API::field_select_row_static(  'wpbc_booking_aggregate'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Aggregate booking dates from other resources', 'booking')
                                                                        , 'description'       => __( 'Select booking resources, for getting booking dates from them and set such dates as unavailable in destination calendar.', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => true
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $resources_list
                                                                    )
                                    );
                }

            ?></tbody></table><?php        

        }


        public function shortcode_bookingcalendar( $shortcode_section_key ){ 

            ?><table class="form-table"><tbody><?php   

                ////////////////////////////////////////////////////////////////////
                // Booking Resources
                ////////////////////////////////////////////////////////////////////        
                if ( class_exists( 'wpdev_bk_personal' ) ){ 

                    WPBC_Settings_API::field_select_row_static(  'wpbc_bookingcalendar_type'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Booking resource', 'booking')
                                                                        , 'description'       => __( 'Select booking resource', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => false
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $this->get_resources()
                                                                    )
                                    );
                }

                ////////////////////////////////////////////////////////////////////
                // Calendar Months Number
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_select_row_static(   'wpbc_bookingcalendar_nummonths'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Visible months', 'booking')
                                                                    , 'description'       => __('Select number of month to show for calendar.' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => get_bk_option( 'booking_client_cal_count' )
                                                                    , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                )
                                );


                ////////////////////////////////////////////////////////////////////
                // Start Month
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_bookingcalendar_startmonth_active" class="wpbc-form-text"><?php  _e('Start month:', 'booking'); ?></label></th>                
                    <td class=""><fieldset><?php 

                        WPBC_Settings_API::field_checkbox_row_static( 'wpbc_bookingcalendar_startmonth_active'  
                                                                    , array(
                                                                              'type'              => 'checkbox'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'class'             => ''
                                                                            , 'css'               => ''
                                                                            , 'tr_class'          => ''
                                                                            , 'attr'              => array()
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'only_field'        => true
                                                                            , 'is_new_line'       => false
                                                                            , 'value'             => false
                                                                        )
                                );

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingcalendar_startmonth_year'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:5em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date( 'Y' )
                                                                            , 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
                                                                        )
                                        );   
                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingcalendar_startmonth_month'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('n')
                                                                            , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                        )
                                        );   

                        ?><span class="description"> <?php _e('Select start month of calendar' ,'booking'); ?></span></fieldset></td>
                </tr><?php 

                WPBC_Settings_API::field_html_row_static( 'advanced_section_calendar'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        '<strong><a id="'.$shortcode_section_key . '_show_link" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings' ,'booking') . '</a>'
                                                                      . '<a id="'.$shortcode_section_key . '_hide_link" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings' ,'booking') . '</a></strong>'
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section2_calendar'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        wpbc_show_message_in_settings( 
                                                                                sprintf( __( 'Setting advanced parameters of the calendar. %sLike width, height and structure %s' ,'booking'), '<br/><em>', '</em>' )
                                                                                . ( ( class_exists('wpdev_bk_biz_m') ) ? sprintf( __( '%s or minimum and fixed number of days selection for the specific day of week or season.%s', 'booking' ), '<em>', '</em>' ) : '' )
                                                                                , 'info', '' , false )
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                              , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                            )
                                        );
                WPBC_Settings_API::field_textarea_row_static( 'wpbc_bookingcalendar_options'
                                                            , array(  
                                                                      'type'              => 'textarea'
                                                                    , 'title'             => __('Options', 'booking')
                                                                    , 'description'       => ''
                                                                    , 'placeholder'       => '{calendar months_num_in_row=2 width=100% cell_height=40px}'
                                                                    , 'description_tag'   => 'span'
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'width:99%;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => ''
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'rows'              => 3
                                                                    , 'cols'              => 20
                                                                    , 'show_in_2_cols'    => false
                                                                )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section_help2_calendar'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  wpbc_show_message_in_settings( 
                                                                                      '<strong style="padding-left: 2em;line-height:2em;">'
                                                                                    . sprintf( __( 'Please read more about the possible customizations of these %soptions%s %shere%s', 'booking' ), '<strong>', '</strong>', '<a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/" target="_blank">', '</a>' )
                                                                                    . '</strong>'
                                                                                    . '<ol style="list-style-type:disc;line-height:2em;">'
                                                                                    . '<li>'
                                                                                    . sprintf( __( 'Specify the full width of calendar, height of date cell and number of months in one row. ', 'booking' ) )
                                                                                    . '<br/><strong>' . __( 'Description', 'booking' ) . ': </strong>'
                                                                                    . __( 'Calendar have 2 months in a row, the cell height is 30px and calendar full width 568px (possible to use percentage for width: 100%)', 'booking' )
                                                                                    . '<br/><strong>' . __( 'Code Example', 'booking' ) . ': </strong>'
                                                                                    . '<code>{calendar months_num_in_row=2 width=568px cell_height=30px}</code>'
                                                                                    . '</li>'                                                                                
                                                                                    . '</ol>'
                                                                                , 'warning', '', false )
                                                              , 'cols'  => 1
                                                              , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );

                if ( class_exists( 'wpdev_bk_personal' ) ){ 
                    $resources_list_orig = $this->get_resources();
                    $resources_list = array();
                    $resources_list[0] = array( 'title' => __('None', 'booking' ), 'attr' => array ( 'class' => 'wpbc_single_resource', 'style' => 'border-bottom:1px dashed #ddd;') );
                    foreach ($resources_list_orig as $res_id => $res_val) {
                        $resources_list[$res_id] = $res_val;
                    }

                    WPBC_Settings_API::field_select_row_static(  'wpbc_bookingcalendar_aggregate'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Aggregate booking dates from other resources', 'booking')
                                                                        , 'description'       => __( 'Select booking resources, for getting booking dates from them and set such dates as unavailable in destination calendar.', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => true
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $resources_list
                                                                    )
                                    );
                }

            ?></tbody></table><?php        

        }


        public function shortcode_bookingselect( $shortcode_section_key ){ 

            if ( ! class_exists( 'wpdev_bk_personal' ) ) return;

            ?><table class="form-table"><tbody><?php   

                WPBC_Settings_API::field_html_row_static( 'help_section_bookingselect'
                                                        , array(  
                                                                'type'      => 'html'
                                                              , 'html'      => wpbc_show_message_in_settings( 
                                                                                '<strong>' . __( 'Note!', 'booking' ) . '</strong> '
                                                                                . sprintf ( __('This shortcode %s is using for selection of the booking form of specific booking resources in selectbox' ,'booking'), '' )
                                                                                //. ( ( class_exists('wpdev_bk_biz_m') ) ? sprintf( __( '%s or minimum and fixed number of days selection for the specific day of week or season.%s', 'booking' ), '<em>', '</em>' ) : '' )
                                                                                , 'info', '' , false )
                                                              , 'cols'      => 2
                                                              , 'group'     =>  $shortcode_section_key
                                                              , 'tr_class'  => $shortcode_section_key . '_standard_section'
                                                            )
                                        );        
                ////////////////////////////////////////////////////////////////////
                // Booking Resources
                ////////////////////////////////////////////////////////////////////   
                $resources_list_orig = $this->get_resources();
                $resources_list = array();
                $resources_list[0] = array( 'title' => __('All', 'booking' ), 'attr' => array ( 'class' => 'wpbc_single_resource', 'style' => 'border-bottom:1px dashed #ddd;') );
                foreach ($resources_list_orig as $res_id => $res_val) {
                    $resources_list[$res_id] = $res_val;
                }
                WPBC_Settings_API::field_select_row_static( 'wpbc_bookingselect_type'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Booking resources', 'booking')
                                                                    , 'label'             => ''
                                                                    , 'description'       => sprintf(__('Select booking resources, for showing in selectbox. Please use CTRL to select multiple booking resources.' ,'booking'),'<br />')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'multiple'          => true
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'height:8em;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 0
                                                                    , 'options'           => $resources_list
                                                                )
                                            );        
                ////////////////////////////////////////////////////////////////////
                // Default Booking Resources
                ////////////////////////////////////////////////////////////////////        
                $resources_list_orig = $this->get_resources();
                $resources_list = array();
                $resources_list[0] = array( 'title' => __('None', 'booking' ), 'attr' => array ( 'class' => 'wpbc_single_resource', 'style' => 'border-bottom:1px dashed #ddd;') );
                foreach ($resources_list_orig as $res_id => $res_val) {
                    $resources_list[$res_id] = $res_val;
                }
                WPBC_Settings_API::field_select_row_static(  'wpbc_bookingselect_selected_type'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Preselected resource', 'booking')
                                                                    , 'description'       => __( 'Define preselected resource.', 'booking' )
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 0
                                                                    , 'options'           => $resources_list
                                                                )
                                );
                ////////////////////////////////////////////////////////////////////
                // Custom form
                ////////////////////////////////////////////////////////////////////
                if ( class_exists( 'wpdev_bk_biz_m' ) ) 
                        wpbc_in_settings__form_selection( array( 
                                                                  'name'        => 'wpbc_bookingselect_form_type'
                                                                , 'title'       => __('Booking Form', 'booking') 
                                                                , 'description' => __('Select default custom booking form' ,'booking')
                                                                , 'group'       => $shortcode_section_key 
                                                                , 'init_options' => array(                                   // Init default list of options
                                                                                        0 => array(  
                                                                                                    'title' => __('Default Form', 'booking')
                                                                                                    , 'attr' => array( 
                                                                                                                      'style' => 'padding:3px;border-bottom:1px dashed #ddd;font-weight:600;'
                                                                                                                    , 'class' => ''
                                                                                                                )                                                                                                
                                                                                                )
                                                                                    )
                                                            )            
                                                    );
                ////////////////////////////////////////////////////////////////////
                // Label near selectbox
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingselect_label'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('Label', 'booking')
                                                                , 'placeholder'       => str_replace( array( '"', "'" ), '', __('Please select the resource:', 'booking' ) )
                                                                , 'description'       => __('Title near your select box.' ,'booking') 
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => str_replace( array( '"', "'" ), '', __('Please select the resource:', 'booking' ) )
                                                            )
                                        );
                ////////////////////////////////////////////////////////////////////
                // First option title
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingselect_first_option_title'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('First option title', 'booking')
                                                                , 'placeholder'       => str_replace( array( '"', "'" ), '', __('Please Select', 'booking' ) )
                                                                , 'description'       => __('First option in dropdown list.' ,'booking') . ' <em>' . __('Please leave it empty if you want to skip it.' ,'booking') . '</em>'
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => ''
                                                            )
                                        );
                ////////////////////////////////////////////////////////////////////
                // Links
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_html_row_static( 'advanced_section_bookingselect'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        '<strong><a id="'.$shortcode_section_key . '_show_link" class="wpbc_expand_section_link" href="javascript:void(0)">+ ' . __('Show advanced settings' ,'booking') . '</a>'
                                                                      . '<a id="'.$shortcode_section_key . '_hide_link" class="wpbc_expand_section_link" href="javascript:void(0)" style="display:none;">- ' . __('Hide advanced settings' ,'booking') . '</a></strong>'
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section2_bookingselect'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  
                                                                        wpbc_show_message_in_settings( 
                                                                                sprintf( __( 'Setting advanced parameters of the calendar. %sLike width, height and structure %s' ,'booking'), ' <em>', '</em>' )
                                                                                //. ( ( class_exists('wpdev_bk_biz_m') ) ? sprintf( __( '%s or minimum and fixed number of days selection for the specific day of week or season.%s', 'booking' ), '<em>', '</em>' ) : '' )
                                                                                , 'info', '' , false )
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                              , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                            )
                                        );
                ////////////////////////////////////////////////////////////////////
                // Advanced section
                ////////////////////////////////////////////////////////////////////   


                ////////////////////////////////////////////////////////////////////
                // Calendar Months Number
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_select_row_static(   'wpbc_bookingselect_nummonths'
                                                            , array(  
                                                                      'type'              => 'select'
                                                                    , 'title'             => __('Visible months', 'booking')
                                                                    , 'description'       => __('Select number of month to show for calendar.' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''
                                                                    , 'multiple'          => false
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => get_bk_option( 'booking_client_cal_count' )
                                                                    , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                )
                                );            
                ////////////////////////////////////////////////////////////////////
                // Start Month
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_advanced_section'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_bookingselect_startmonth_active" class="wpbc-form-text"><?php  _e('Start month:', 'booking'); ?></label></th>                
                    <td class=""><fieldset><?php 

                        WPBC_Settings_API::field_checkbox_row_static( 'wpbc_bookingselect_startmonth_active'  
                                                                    , array(
                                                                              'type'              => 'checkbox'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'class'             => ''
                                                                            , 'css'               => ''
                                                                            , 'tr_class'          => ''
                                                                            , 'attr'              => array()
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'only_field'        => true
                                                                            , 'is_new_line'       => false
                                                                            , 'value'             => false
                                                                        )
                                );

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingselect_startmonth_year'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:5em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date( 'Y' )
                                                                            , 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
                                                                        )
                                        );   
                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingselect_startmonth_month'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('n')
                                                                            , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                        )
                                        );   

                        ?><span class="description"> <?php _e('Select start month of calendar' ,'booking'); ?></span></fieldset></td>
                </tr><?php 


                WPBC_Settings_API::field_textarea_row_static( 'wpbc_bookingselect_options'
                                                            , array(  
                                                                      'type'              => 'textarea'
                                                                    , 'title'             => __('Options', 'booking')
                                                                    , 'description'       => ''
                                                                    , 'placeholder'       => '{calendar months_num_in_row=2 width=100% cell_height=40px}'
                                                                    , 'description_tag'   => 'span'
                                                                    , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'width:99%;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => ''
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'rows'              => 3
                                                                    , 'cols'              => 20
                                                                    , 'show_in_2_cols'    => false
                                                                )
                                        );
                WPBC_Settings_API::field_html_row_static( 'advanced_section_help2_bookingselect'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  =>  wpbc_show_message_in_settings( 
                                                                                      '<strong style="padding-left: 2em;line-height:2em;">'
                                                                                    . sprintf( __( 'Please read more about the possible customizations of these %soptions%s %shere%s', 'booking' ), '<strong>', '</strong>', '<a href="https://wpbookingcalendar.com/faq/booking-calendar-shortcodes/" target="_blank">', '</a>' )
                                                                                    . '</strong>'
                                                                                    . '<ol style="list-style-type:disc;line-height:2em;">'
                                                                                    . '<li>'
                                                                                    . sprintf( __( 'Specify the full width of calendar, height of date cell and number of months in one row. ', 'booking' ) )
                                                                                    . '<br/><strong>' . __( 'Description', 'booking' ) . ': </strong>'
                                                                                    . __( 'Calendar have 2 months in a row, the cell height is 30px and calendar full width 568px (possible to use percentage for width: 100%)', 'booking' )
                                                                                    . '<br/><strong>' . __( 'Code Example', 'booking' ) . ': </strong>'
                                                                                    . '<code>{calendar months_num_in_row=2 width=568px cell_height=30px}</code>'
                                                                                    . '</li>'                                                                                
                                                                                    . '</ol>'
                                                                                , 'warning', '', false )
                                                              , 'cols'  => 1
                                                              , 'tr_class'          => $shortcode_section_key . '_advanced_section'
                                                              , 'group' =>  $shortcode_section_key
                                                            )
                                        );

            ?></tbody></table><?php        

        }


        public function shortcode_bookingsearch( $shortcode_section_key ){ 
            //[bookingsearchresults]

            if ( ! class_exists( 'wpdev_bk_biz_l' ) ) return;

            ?><table class="form-table"><tbody><?php   


                ////////////////////////////////////////////////////////////////////
                // Search Form  |  Search Results
                ////////////////////////////////////////////////////////////////////        
                WPBC_Settings_API::field_radio_row_static(   'wpbc_bookingsearch_type'
                                                            , array(  
                                                                      'type'              => 'radio'
                                                                    , 'title'             => __('Select shortcode to insert', 'booking')
                                                                    , 'description'       => ''
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => ''                                                                
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => ''
                                                                    , 'class'             => ''
                                                                    , 'css'               => 'margin-right:10px;'
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => 'bookingsearch'
                                                                    , 'options'           => array( 
                                                                                                      'bookingsearch'        => array(    'title' => __('Search form' ,'booking') 
                                                                                                                                        , 'attr'  => array( 'id' => 'wpbc_shortcode__bookingsearch' )
                                                                                                                                    )
                                                                                                    , 'bookingsearchresults' => array(    'title' => __('Search results' ,'booking') 
                                                                                                                                        , 'attr'  => array( 'id' => 'wpbc_shortcode__bookingsearchresults' )
                                                                                                                                    )

                                                                                                )
                                                                )
                                );

                ?><tr valign="top" style="border-top:1px dashed #ccc"><td colspan="2"></td></tr><?php

                ////////////////////////////////////////////////////////////////////
                // Activate external  search results page
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_checkbox_row_static( 'wpbc_bookingsearch_searchresults_active'  
                                                            , array(
                                                                      'type'              => 'checkbox'
                                                                    , 'title'             => ''
                                                                    , 'description'       => ''
                                                                    , 'description_tag'   => 'span'
                                                                    , 'label'             => __('Check this box to show search results on other page' ,'booking')
                                                                    , 'class'             => ''
                                                                    , 'css'               => ''
                                                                    , 'tr_class'          => $shortcode_section_key . '_search_form'
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'attr'              => array()
                                                                    , 'only_field'        => false
                                                                    , 'is_new_line'       => false
                                                                    , 'value'             => false
                                                                )
                        );
                ////////////////////////////////////////////////////////////////////
                // URL of external search results page
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingsearch_searchresults'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('URL of search results:', 'booking')
                                                                , 'placeholder'       => get_option('siteurl') . '/search-results/'
                                                                , 'description'       => __('Type the URL of search results page.' ,'booking') 
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_search_form' . ' wpbc_sub_settings_grayed' 
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => ''
                                                            )
                                        );
                ////////////////////////////////////////////////////////////////////
                // URL of external search results page
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingsearch_searchresultstitle'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('Title of Search results:', 'booking')
                                                                , 'placeholder'       => '{searchresults} ' . str_replace( '"', '', __( 'Result(s) Found', 'booking' ) )
                                                                , 'description'       => __('Type the title of Search results.' ,'booking') . ' <code>{searchresults}</code> - ' . __( 'show number of search results', 'booking' )
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_search_form' 
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => '{searchresults} ' . str_replace( '"', '', __( 'Result(s) Found', 'booking' ) )
                                                            )
                                        );
                ////////////////////////////////////////////////////////////////////
                // Nothing Found Message
                ////////////////////////////////////////////////////////////////////
                WPBC_Settings_API::field_text_row_static( 'wpbc_bookingsearch_noresultstitle'
                                                        , array(  
                                                                  'type'              => 'text'
                                                                , 'title'             => __('Nothing Found Message:', 'booking')
                                                                , 'placeholder'       => str_replace( '"', '', __( 'Nothing Found', 'booking' ) )
                                                                , 'description'       => __('Type the message, when nothing found.' ,'booking') 
                                                                , 'description_tag'   => 'span'
                                                                , 'group'             => $shortcode_section_key
                                                                , 'tr_class'          => $shortcode_section_key . '_search_form' 
                                                                , 'class'             => ''
                                                                , 'css'               => ''
                                                                , 'only_field'        => false
                                                                , 'attr'              => array()                                                    
                                                                , 'value'             => str_replace( '"', '', __( 'Nothing Found', 'booking' ) )
                                                            )
                                        );


                if ( class_exists( 'wpdev_bk_multiuser' ) ){ 
                    ////////////////////////////////////////////////////////////////////
                    // Users
                    ////////////////////////////////////////////////////////////////////
                    WPBC_Settings_API::field_text_row_static( 'wpbc_bookingsearch_users'
                                                            , array(  
                                                                      'type'              => 'text'
                                                                    , 'title'             => __('Search only for users:', 'booking')
                                                                    , 'placeholder'       => '1,3,99'
                                                                    , 'description'       =>  __('Type IDs of the users (separated by comma ",") for searching availability  only for these users, or leave it blank for searching for all users.' ,'booking')
                                                                    , 'description_tag'   => 'span'
                                                                    , 'group'             => $shortcode_section_key
                                                                    , 'tr_class'          => $shortcode_section_key . '_search_form' 
                                                                    , 'class'             => ''
                                                                    , 'css'               => ''
                                                                    , 'only_field'        => false
                                                                    , 'attr'              => array()                                                    
                                                                    , 'value'             => ''
                                                                )
                                            );
                }

                ////////////////////////////////////////////////////////////////////
                // Search Results
                ////////////////////////////////////////////////////////////////////            
                WPBC_Settings_API::field_html_row_static( 'wpbc_bookingsearchresults'
                                                        , array(  
                                                                'type' => 'html'
                                                              , 'html'  => wpbc_show_message_in_settings( 
                                                                                sprintf( __( 'This shortcode %s is using for showing the search results at specific page, if the search form is submit showing the search results at different page', 'booking' ), '<code>[bookingsearchresults]</code>' )
                                                                                , 'info', '' , false )
                                                              , 'cols'  => 1
                                                              , 'group' =>  $shortcode_section_key
                                                              , 'tr_class'          => $shortcode_section_key . '_search_results'
                                                            )
                                        );            

            ?></tbody></table><?php               
        }


        public function shortcode_bookingform( $shortcode_section_key ){ 

            // [bookingform type=1 form_type='standard' selected_dates='25.11.2014']

            if ( ! class_exists( 'wpdev_bk_biz_l' ) ) return;

            ?><table class="form-table"><tbody><?php   

                ////////////////////////////////////////////////////////////////////
                // Booking Resources
                ////////////////////////////////////////////////////////////////////        
                if ( class_exists( 'wpdev_bk_personal' ) ){ 

                    WPBC_Settings_API::field_select_row_static(  'wpbc_bookingform_type'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Booking resource', 'booking')
                                                                        , 'description'       => __( 'Select booking resource', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => false
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => $shortcode_section_key . '_standard_section'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => ''
                                                                        , 'options'           => $this->get_resources()
                                                                    )
                                    );
                }


                ////////////////////////////////////////////////////////////////////
                // Custom form
                ////////////////////////////////////////////////////////////////////
                if ( class_exists( 'wpdev_bk_biz_m' ) ) 
                        wpbc_in_settings__form_selection( array( 
                                                                  'name'        => 'wpbc_bookingform_form_type'
                                                                , 'title'       => __('Booking Form', 'booking') 
                                                                , 'description' => __('Select default custom booking form' ,'booking')
                                                                , 'group'       => $shortcode_section_key                        
                                                            )            
                                                    );

                ////////////////////////////////////////////////////////////////////
                // Start Month
                ////////////////////////////////////////////////////////////////////                
                ?><tr valign="top" class="<?php echo $shortcode_section_key . '_standard_section'; ?>">
                    <th scope="row" style="vertical-align: middle;"><label for="wpbc_bookingform_selected_dates_year" class="wpbc-form-text"><?php  echo ucfirst( __('date', 'booking') ); ?></label></th>                
                    <td class=""><fieldset><?php 

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingform_selected_dates_year'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:5em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date( 'Y' )
                                                                            , 'options'           => array_combine( range( ( date('Y') - 1 ), ( date('Y') + 10 ) ), range( ( date('Y') - 1 ), ( date('Y') + 10 ) )  )
                                                                        )
                                        );   
                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingform_selected_dates_month'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('n')
                                                                            , 'options'           => array_combine( range( 1, 12 ), range( 1, 12 ) )
                                                                        )
                                        );   

                        ?><span style="font-weight:600;"> / </span><?php

                        WPBC_Settings_API::field_select_row_static(  'wpbc_bookingform_selected_dates_day'
                                                                    , array(  
                                                                              'type'              => 'select'
                                                                            , 'title'             => ''
                                                                            , 'description'       => ''
                                                                            , 'description_tag'   => 'span'
                                                                            , 'label'             => ''
                                                                            , 'multiple'          => false
                                                                            , 'group'             => $shortcode_section_key                                                                        
                                                                            , 'class'             => ''
                                                                            , 'css'               => 'width:4em;'
                                                                            , 'only_field'        => true
                                                                            , 'attr'              => array()                                                    
                                                                            , 'value'             => date('j')
                                                                            , 'options'           => array_combine( range( 1, 31 ), range( 1, 31 ) )
                                                                        )
                                        );   

                        ?><span class="description"> <?php _e('Define date for booking' ,'booking'); ?></span></fieldset></td>
                </tr><?php 

            ?></tbody></table><?php        
        }


        public function shortcode_bookingother( $shortcode_section_key ){ 
            // [bookingresource type=3 show="title"]
            // [bookingresource type=3 show="cost"]
            // [bookingresource type=3 show="capacity"]
            //  
            // [bookingedit]

            if ( ! class_exists( 'wpdev_bk_personal' ) ) return;

                ?><table class="form-table"><tbody><?php   


                    ////////////////////////////////////////////////////////////////////
                    // Search Form  |  Search Results
                    ////////////////////////////////////////////////////////////////////        
                    WPBC_Settings_API::field_radio_row_static(   'wpbc_bookingother'
                                                                , array(  
                                                                          'type'              => 'radio'
                                                                        , 'title'             => __('Select shortcode to insert', 'booking')
                                                                        , 'description'       => ''
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''                                                                
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => ''
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => 'bookingedit'
                                                                        , 'is_new_line'       => true
                                                                        , 'options'           => array( 
                                                                                                          'bookingedit'     => array(   'title' => __('Edit Booking' ,'booking') 
                                                                                                                                      , 'attr'  => array( 'id' => 'wpbc_shortcode__bookingedit' )
                                                                                                                                    )
                                                                                                        , 'bookingcustomerlisting' => array(   'title' => __('Show listing of customer bookings' ,'booking')
                                                                                                                                      , 'attr'  => array( 'id' => 'wpbc_shortcode__bookingcustomerlisting' )
                                                                                                                                    )
                                                                                                        , 'bookingresource' => array(   'title' => __('Show info about Booking Resource' ,'booking')
                                                                                                                                      , 'attr'  => array( 'id' => 'wpbc_shortcode__bookingresource' )
                                                                                                                                    )
                                                                                                    )
                                                                    )
                                    );

                    ?><tr valign="top" style="border-top:1px dashed #ccc"><td colspan="2"></td></tr><?php

                    ////////////////////////////////////////////////////////////////////
                    // Booking Edit
                    ////////////////////////////////////////////////////////////////////            
                    WPBC_Settings_API::field_html_row_static( 'wpbc_bookingedit'
                                                            , array(  
                                                                    'type' => 'html'
                                                                  , 'html'  => wpbc_show_message_in_settings( 
                                                                                                sprintf( __( 'This shortcode %s is used on a page, where visitors can %smodify%s their own booking(s), %scancel%s or make %spayment%s after receiving an admin email payment request', 'booking' ), '<code>[bookingedit]</code>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>' )
                                                                                    . '<br/>' . sprintf( __( 'The content of field %sURL to edit bookings%s on the %sgeneral booking settings page%s must link to this page', 'booking' ), '<i>"', '"</i>', '<a href="' . wpbc_get_settings_url() . '">', '</a>' )
                                                                                    . '<br/>' . sprintf( __( 'Email templates, which use shortcodes: %s, will be linked to this page', 'booking' ), '<code>[visitorbookingediturl]</code>, <code>[visitorbookingcancelurl]</code>, <code>[visitorbookingpayurl]</code>' )
                                                                                    , 'info', '' , false )
                                                                  , 'cols'  => 1
                                                                  , 'group' =>  $shortcode_section_key
                                                                  , 'tr_class'      => 'wpbc_shortcode_bookingedit wpbc_shortcode_bookingother'
                                                                )
                                            );            
                    ////////////////////////////////////////////////////////////////////
                    // Customer bookings listing
                    ////////////////////////////////////////////////////////////////////
                    WPBC_Settings_API::field_html_row_static( 'wpbc_bookingcustomerlisting'
                                                            , array(
                                                                    'type' => 'html'
                                                                  , 'html'  => wpbc_show_message_in_settings(
                                                                                                sprintf( __( 'This shortcode %s is used on a page, where visitors can %sview listing%s of their own booking(s)', 'booking' ), '<code>[bookingcustomerlisting]</code>', '<strong>', '</strong>', '<strong>', '</strong>', '<strong>', '</strong>' )
                                                                                    . '<br/>' . sprintf( __( 'The content of field %sURL of page for customer bookings listing%s on the %sgeneral booking settings page%s must link to this page', 'booking' ), '<i>"', '"</i>', '<a href="' . wpbc_get_settings_url() . '">', '</a>' )
                                                                                    . '<br/>' . sprintf( __( 'Email templates, which use shortcodes: %s, will be linked to this page', 'booking' ), '<code>[visitorbookingslisting]</code>' )
                                                                                    . '<br/>' . sprintf( __( '%s You can use in this shortcode the same parameters as for %s shortcode', 'booking' ), '<strong>' . __('Trick', 'booking') . '.</strong> ', '<code>[bookingtimeline ... ]</code>' )
                                                                                    , 'info', '' , false )
                                                                  , 'cols'  => 1
                                                                  , 'group' =>  $shortcode_section_key
                                                                  , 'tr_class'      => 'wpbc_shortcode_bookingcustomerlisting wpbc_shortcode_bookingother'
                                                                )
                                            );
                    ////////////////////////////////////////////////////////////////////
                    // Booking Resource 
                    ////////////////////////////////////////////////////////////////////            
                    $resources_list_orig = $this->get_resources();
                    $resources_list = array();
                    // $resources_list[0] = array( 'title' => __('None', 'booking' ), 'attr' => array ( 'class' => 'wpbc_single_resource', 'style' => 'border-bottom:1px dashed #ddd;') );
                    foreach ($resources_list_orig as $res_id => $res_val) {
                        $resources_list[$res_id] = $res_val;
                    }
                    WPBC_Settings_API::field_select_row_static(  'wpbc_bookingresource_type'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Booking resource', 'booking')
                                                                        , 'description'       => __( 'Select booking resource', 'booking' )
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => false
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => 'wpbc_shortcode_bookingresource wpbc_shortcode_bookingother'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => 0
                                                                        , 'options'           => $resources_list
                                                                    )
                                    );                
                    ////////////////////////////////////////////////////////////////////
                    // What to  Show
                    ////////////////////////////////////////////////////////////////////
                    $bookingresource_show_options = array( 'title'    => __( 'Title', 'booking' ) );
                    if ( class_exists( 'wpdev_bk_biz_s' ) ) 
                        $bookingresource_show_options[ 'cost' ] = __( 'Cost', 'booking' );                    
                    if ( class_exists( 'wpdev_bk_biz_l' ) ) 
                       $bookingresource_show_options[ 'capacity' ] = __( 'Capacity', 'booking' );
                    
                    WPBC_Settings_API::field_select_row_static(   'wpbc_bookingresource_show'
                                                                , array(  
                                                                          'type'              => 'select'
                                                                        , 'title'             => __('Show', 'booking')
                                                                        , 'description'       => __('Select type of info to show.' ,'booking')
                                                                        , 'description_tag'   => 'span'
                                                                        , 'label'             => ''
                                                                        , 'multiple'          => false
                                                                        , 'group'             => $shortcode_section_key
                                                                        , 'tr_class'          => 'wpbc_shortcode_bookingresource wpbc_shortcode_bookingother'
                                                                        , 'class'             => ''
                                                                        , 'css'               => 'margin-right:10px;'
                                                                        , 'only_field'        => false
                                                                        , 'attr'              => array()                                                    
                                                                        , 'value'             => 'title'
                                                                        , 'options'           => $bookingresource_show_options
                                                                    )
                                    );

            ?></tbody></table><?php    
        }

    //                                                                              </editor-fold>             
    
        
    //                                                                              <editor-fold   defaultstate="collapsed"   desc=" J S " >    
    public function write_js() {
        ?>
        
        <!-- WPBC JavaScript -->
        <script type="text/javascript">
        <?php

        ?>
            jQuery(document).ready(function(){
                                
                ////////////////////////////////////////////////////////////////
                // [bookingtimeline ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'bookingtimeline'; ?>
                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=1]" ).prop( "disabled", true );
                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=7]" ).prop( "disabled", true );
                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=60]" ).prop( "disabled", true );                        
//                jQuery('#bookingtimeline_show_link').on( 'click', function(){                                 
//                                jQuery('#bookingtimeline_show_link').toggle(200);
//                                jQuery('#bookingtimeline_hide_link').animate( {opacity: 1}, 200 ).toggle(200);
//                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').toggle(200);
//                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').animate( {opacity: 1}, 200 ).toggle(200);
//                            } );
//                jQuery('#bookingtimeline_hide_link').on( 'click', function(){    
//                                jQuery('#bookingtimeline_hide_link').toggle(200);
//                                jQuery('#bookingtimeline_show_link').animate( {opacity: 1}, 200 ).toggle(200);
//                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').toggle(200);
//                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').animate( {opacity: 1}, 200 ).toggle(200);
//                            } );  
//FixIn: 7.0.1.17
                jQuery( '#wpbc_bookingtimeline_type,#wpbc_bookingtimeline_view_days_num,#wpbc_bookingtimeline_header_title,#wpbc_bookingtimeline_scroll_start_date_active,#wpbc_bookingtimeline_scroll_month,#wpbc_bookingtimeline_scroll_day,#wpbc_bookingtimeline_limit_hours_start_time,#wpbc_bookingtimeline_limit_hours_end_time').on( 'change', function(){      // Booking Resource Selectbox Change value
                    wpbc_set_shortcode();
                });                     
                jQuery( '#wpbc_bookingtimeline_scroll_start_date_year,#wpbc_bookingtimeline_scroll_start_date_month,#wpbc_bookingtimeline_scroll_start_date_day').on( 'change', function(){          // Start Year number Selectbox Change value
                    if ( jQuery('#wpbc_bookingtimeline_scroll_start_date_active').is(':checked') ) {   
                        wpbc_set_shortcode();
                    }
                });                     
                                
                ////////////////////////////////////////////////////////////////
                // [booking ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'booking'; ?>
                jQuery('#booking_show_link').on( 'click', function(){                                 
                                jQuery('#booking_show_link').toggle(200);
                                jQuery('#booking_hide_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );
                jQuery('#booking_hide_link').on( 'click', function(){    
                                jQuery('#booking_hide_link').toggle(200);
                                jQuery('#booking_show_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );   
                jQuery( '#wpbc_booking_type,#wpbc_booking_form_type,#wpbc_booking_nummonths,#wpbc_booking_startmonth_active,#wpbc_booking_options,#wpbc_booking_aggregate').on( 'change', function(){      // Booking Resource Selectbox Change value
                    wpbc_set_shortcode();
                });                     
                jQuery( '#wpbc_booking_startmonth_year,#wpbc_booking_startmonth_month').on( 'change', function(){          // Start Year number Selectbox Change value
                    if ( jQuery('#wpbc_booking_startmonth_active').is(':checked') ) {   
                        wpbc_set_shortcode();
                    }
                });                     
                
                ////////////////////////////////////////////////////////////////
                // [bookingcalendar ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'bookingcalendar'; ?>
                jQuery('#bookingcalendar_show_link').on( 'click', function(){                                 
                                jQuery('#bookingcalendar_show_link').toggle(200);
                                jQuery('#bookingcalendar_hide_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );
                jQuery('#bookingcalendar_hide_link').on( 'click', function(){    
                                jQuery('#bookingcalendar_hide_link').toggle(200);
                                jQuery('#bookingcalendar_show_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );   
                jQuery( '#wpbc_bookingcalendar_type,#wpbc_bookingcalendar_nummonths,#wpbc_bookingcalendar_startmonth_active,#wpbc_bookingcalendar_options,#wpbc_bookingcalendar_aggregate').on( 'change', function(){      // Booking Resource Selectbox Change value
                    wpbc_set_shortcode();
                });                     
                jQuery( '#wpbc_bookingcalendar_startmonth_year,#wpbc_bookingcalendar_startmonth_month').on( 'change', function(){          // Start Year number Selectbox Change value
                    if ( jQuery('#wpbc_bookingcalendar_startmonth_active').is(':checked') ) {   
                        wpbc_set_shortcode();
                    }
                });    
                
                ////////////////////////////////////////////////////////////////
                // [bookingselect ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'bookingselect'; ?>
                jQuery('#bookingselect_show_link').on( 'click', function(){                                 
                                jQuery('#bookingselect_show_link').toggle(200);
                                jQuery('#bookingselect_hide_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );
                jQuery('#bookingselect_hide_link').on( 'click', function(){    
                                jQuery('#bookingselect_hide_link').toggle(200);
                                jQuery('#bookingselect_show_link').animate( {opacity: 1}, 200 ).toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_advanced_section').toggle(200);
                                jQuery('.<?php echo $shortcode_section_key; ?>_standard_section').animate( {opacity: 1}, 200 ).toggle(200);
                            } );   
                jQuery( '#wpbc_bookingselect_type,#wpbc_bookingselect_selected_type,#wpbc_bookingselect_form_type,#wpbc_bookingselect_label,#wpbc_bookingselect_first_option_title,#wpbc_bookingselect_nummonths,#wpbc_bookingselect_startmonth_active,#wpbc_bookingselect_options').on( 'change', function(){      // Booking Resource Selectbox Change value
                    wpbc_set_shortcode();
                });                     
                jQuery( '#wpbc_bookingselect_startmonth_year,#wpbc_bookingselect_startmonth_month').on( 'change', function(){          // Start Year number Selectbox Change value
                    if ( jQuery('#wpbc_bookingselect_startmonth_active').is(':checked') ) {   
                        wpbc_set_shortcode();
                    }
                });                     
                
                ////////////////////////////////////////////////////////////////
                // [bookingform ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'bookingform'; ?>
                jQuery( '#wpbc_bookingform_type,#wpbc_bookingform_form_type,#wpbc_bookingform_selected_dates_year,#wpbc_bookingform_selected_dates_month,#wpbc_bookingform_selected_dates_day').on( 'change', function(){      // Booking Resource Selectbox Change value
                    wpbc_set_shortcode();
                });                     
                
                
                ////////////////////////////////////////////////////////////////
                // [bookingsearch ...] params  on change
                ////////////////////////////////////////////////////////////////
                <?php $shortcode_section_key = 'bookingsearch'; ?>
                jQuery('#wpbc_shortcode__bookingsearch').on( 'click', function(){                                 
                                jQuery('.bookingsearch_search_form').show();
                                if ( ! jQuery('#wpbc_bookingsearch_searchresults_active').is(':checked') ) { 
                                    jQuery('#wpbc_tiny_modal .bookingsearch_search_form.wpbc_sub_settings_grayed').hide();
                                }
                                jQuery('.bookingsearch_search_results').hide();
                                jQuery( '#wpbc_shortcode_type' ).val('bookingsearch');
                            } );
                jQuery('#wpbc_shortcode__bookingsearchresults').on( 'click', function(){    
                                jQuery('.bookingsearch_search_form').hide();
                                jQuery('.bookingsearch_search_results').show();
                                jQuery( '#wpbc_shortcode_type' ).val('bookingsearchresults');
                            } );  
                                                                                               
                jQuery( '#wpbc_shortcode__bookingsearch,#wpbc_shortcode__bookingsearchresults,#wpbc_bookingsearch_searchresults_active,#wpbc_bookingsearch_searchresults,#wpbc_bookingsearch_searchresultstitle,#wpbc_bookingsearch_noresultstitle,#wpbc_bookingsearch_users').on( 'change', function(){ 
                           
                    if ( jQuery('#wpbc_shortcode__bookingsearch').is(':checked') ) {                        // Check  only  for [bookingsearch]
                            if ( jQuery('#wpbc_bookingsearch_searchresults_active').is(':checked') ) {      // Check if show|hide external search  results
                                jQuery('#wpbc_tiny_modal .bookingsearch_search_form.wpbc_sub_settings_grayed').show();
                            } else {
                                jQuery('#wpbc_tiny_modal .bookingsearch_search_form.wpbc_sub_settings_grayed').hide();
                            }
                    }                        
                    wpbc_set_shortcode();
                });                     
                

                <?php $shortcode_section_key = 'bookingother'; ?>
                jQuery( '#wpbc_shortcode__bookingedit,#wpbc_shortcode__bookingcustomerlisting,#wpbc_shortcode__bookingresource,#wpbc_bookingresource_type,#wpbc_bookingresource_show').on( 'change', function(){
                           
                    wpbc_set_shortcode();
                });                     
                

                ////////////////////////////////////////////////////////////////
                wpbc_set_shortcode();
            });     
            
            function wpbc_set_shortcode(){
                
                var wpbc_shortcode = '[';                
                var wpbc_shortcode_type = jQuery( '#wpbc_shortcode_type' ).val().trim();
                
                ////////////////////////////////////////////////////////////////
                // [bookingtimeline]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingtimeline' ) {                       
                    
                    wpbc_shortcode += wpbc_shortcode_type;
                    var wpbc_is_matrix = false;        
                    if ( jQuery( '#wpbc_bookingtimeline_type' ).length > 0 ) { 
                        var wpbc_bookingtimeline_type_temp = jQuery( '#wpbc_bookingtimeline_type' ).val();
                        if ( ( wpbc_bookingtimeline_type_temp != null ) && ( wpbc_bookingtimeline_type_temp.length > 0 )  ){ 
                            
                            if ( wpbc_bookingtimeline_type_temp.length > 1 ) {  // Matrix
                                wpbc_is_matrix = true;
                                jQuery( "#wpbc_bookingtimeline_view_days_num option" ).prop( "disabled", false );                                
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=90]" ).prop( "disabled", true );
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=365]" ).prop( "disabled", true );
                            } else {                                            // Single
                                jQuery( "#wpbc_bookingtimeline_view_days_num option" ).prop( "disabled", false );
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=1]" ).prop( "disabled", true );
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=7]" ).prop( "disabled", true );                                
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=60]" ).prop( "disabled", true );
                            }
                            if ( jQuery( "#wpbc_bookingtimeline_view_days_num option:selected" ).is(':disabled') ) {
                                jQuery( "#wpbc_bookingtimeline_view_days_num option[value=30]" ).prop( "selected", true );
                            }
                            wpbc_bookingtimeline_type_temp = wpbc_bookingtimeline_type_temp.join(',')                                                    
                            wpbc_shortcode += ' type=\'' + wpbc_bookingtimeline_type_temp + '\'';
                        }
                    }
                    
                    
                    
                    if ( jQuery( '#wpbc_bookingtimeline_view_days_num' ).length > 0 ) { 
                        
                        var view_days_num_temp = parseInt( jQuery( '#wpbc_bookingtimeline_view_days_num' ).val().trim() );
                        if ( view_days_num_temp != 30 )
                            wpbc_shortcode += ' view_days_num=' + view_days_num_temp;
                        
          
                        //FixIn: 7.0.1.17
                        jQuery( '.bookingtimeline_view_times' ).hide();
                        if (
                               ( ( wpbc_is_matrix ) && ( view_days_num_temp == 1 ) )  
                            || ( ( ! wpbc_is_matrix ) && ( view_days_num_temp == 30 ) )
                        ) {
                            jQuery( '.bookingtimeline_view_times' ).show();
                            var view_times_start_temp = parseInt( jQuery( '#wpbc_bookingtimeline_limit_hours_start_time' ).val().trim() );
                            var view_times_end_temp = parseInt( jQuery( '#wpbc_bookingtimeline_limit_hours_end_time' ).val().trim() );
                            if ( (view_times_start_temp != 0 ) || ( view_times_end_temp !=24 ) ) {
                                wpbc_shortcode += ' limit_hours=\'' + view_times_start_temp + ',' + view_times_end_temp + '\'';
                            }
                        }
          
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        // Hide or Show Scrolling Days and Months, depend from type of view and number of booking resources 
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////     
                        jQuery( "#wpbc_bookingtimeline_scroll_month,#wpbc_bookingtimeline_scroll_day" ).prop( "disabled", false ); 
                        jQuery( ".wpbc_bookingtimeline_scroll_month,.wpbc_bookingtimeline_scroll_day" ).show();
                        // Matrix //////////////////////////////////////////////
                        if ( 
                              ( wpbc_is_matrix ) && ( ( view_days_num_temp == 1 ) || ( view_days_num_temp == 7 ) ) // Day | Week view
                            ) {
                                jQuery( "#wpbc_bookingtimeline_scroll_month" ).prop( "disabled", true );                            // Scroll Month NOT working
                                jQuery( '.wpbc_bookingtimeline_scroll_month' ).hide();
                            }
                        if ( 
                              ( wpbc_is_matrix )&& ( ( view_days_num_temp == 30 ) || ( view_days_num_temp == 60 ) ) // Month view
                            ) {
                                jQuery( "#wpbc_bookingtimeline_scroll_day" ).prop( "disabled", true );                              // Scroll Days NOT working
                                jQuery( '.wpbc_bookingtimeline_scroll_day' ).hide();
                            }
                        // Single //////////////////////////////////////////////
                        if ( 
                              ( ! wpbc_is_matrix ) && ( ( view_days_num_temp == 30 ) || ( view_days_num_temp == 90 ) )  // Month | 3 Months view (like week view)
                            ) {
                                jQuery( "#wpbc_bookingtimeline_scroll_month" ).prop( "disabled", true );                                        // Scroll Month NOT working
                                jQuery( '.wpbc_bookingtimeline_scroll_month' ).hide();                               
                            }
                        if ( 
                              ( ! wpbc_is_matrix )&& ( ( view_days_num_temp == 365 ) )                              // Year view
                            ) {
                                jQuery( "#wpbc_bookingtimeline_scroll_day" ).prop( "disabled", true );                                          // Scroll Days NOT working
                                jQuery( '.wpbc_bookingtimeline_scroll_day' ).hide();
                            }
                        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
                    }
                    
                    if ( jQuery( '#wpbc_bookingtimeline_header_title' ).length > 0 ) { 
                        var header_title_temp = jQuery( '#wpbc_bookingtimeline_header_title' ).val().trim();
                        header_title_temp = header_title_temp.replace(/'/gi, '');
                        if ( header_title_temp != '' )
                            wpbc_shortcode += ' header_title=\'' + header_title_temp + '\'';
                    }
                    
                    if ( ! jQuery( "#wpbc_bookingtimeline_scroll_month" ).is(':disabled') )
                        if ( parseInt( jQuery( '#wpbc_bookingtimeline_scroll_month' ).val().trim() ) != 0 )
                            wpbc_shortcode += ' scroll_month=' + jQuery( '#wpbc_bookingtimeline_scroll_month' ).val().trim();
                    
                    if ( ! jQuery( "#wpbc_bookingtimeline_scroll_day" ).is(':disabled') )
                        if ( parseInt( jQuery( '#wpbc_bookingtimeline_scroll_day' ).val().trim() ) != 0 )
                            wpbc_shortcode += ' scroll_day=' + jQuery( '#wpbc_bookingtimeline_scroll_day' ).val().trim();
                     
                    if ( jQuery('#wpbc_bookingtimeline_scroll_start_date_active').is(':checked') ) { 
                         wpbc_shortcode += ' scroll_start_date=\'' + jQuery( '#wpbc_bookingtimeline_scroll_start_date_year' ).val().trim() 
                                                             + '-' + jQuery( '#wpbc_bookingtimeline_scroll_start_date_month' ).val().trim() 
                                                             + '-' + jQuery( '#wpbc_bookingtimeline_scroll_start_date_day' ).val().trim() 
                                                            + '\'';
                    }
                }
                ////////////////////////////////////////////////////////////////
                // [booking]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'booking' ) {                       
                    
                    wpbc_shortcode += wpbc_shortcode_type;
                            
                    if ( jQuery( '#wpbc_booking_type' ).length > 0 ) {
						if ( jQuery( '#wpbc_booking_type' ).val() === null ) {											//FixIn: 8.2.1.12
							jQuery( '#wpbc_text_put_in_shortcode' ).val( '<?php esc_js( __( 'No booking resources', 'booking' )) ?>' );
							return;
						} else {
							wpbc_shortcode += ' type=' + jQuery( '#wpbc_booking_type' ).val().trim();
						}
                    }
                    if ( jQuery( '#wpbc_booking_form_type' ).length > 0 ) { 
                        var form_type_temp = jQuery( '#wpbc_booking_form_type' ).val().trim();
                        if ( form_type_temp != 'standard' )
                            wpbc_shortcode += ' form_type=\'' + jQuery( '#wpbc_booking_form_type' ).val().trim() + '\'';
                    }
                    
                    if ( parseInt( jQuery( '#wpbc_booking_nummonths' ).val().trim() ) > 1 )
                        wpbc_shortcode += ' nummonths=' + jQuery( '#wpbc_booking_nummonths' ).val().trim();
                     
                    if ( jQuery('#wpbc_booking_startmonth_active').is(':checked') ) { 
                         wpbc_shortcode += ' startmonth=\'' + jQuery( '#wpbc_booking_startmonth_year' ).val().trim() + '-' + jQuery( '#wpbc_booking_startmonth_month' ).val().trim() + '\'';
                    }
                    if ( jQuery( '#wpbc_booking_options' ).length > 0 ) { 
                        var wpbc_options_temp = jQuery( '#wpbc_booking_options' ).val().trim();
                        if ( wpbc_options_temp.length > 0 )
                            wpbc_shortcode += ' options=\'' + wpbc_options_temp + '\'';
                    }    
                    if ( jQuery( '#wpbc_booking_aggregate' ).length > 0 ) { 
                        var wpbc_aggregate_temp = jQuery( '#wpbc_booking_aggregate' ).val();
                        
                        if ( ( wpbc_aggregate_temp != null ) && ( wpbc_aggregate_temp.length > 0 )  ){ 
                            wpbc_aggregate_temp = wpbc_aggregate_temp.join(';')
                            
                            if ( wpbc_aggregate_temp != 0 )                     // Check about 0=>'None'    
                                wpbc_shortcode += ' aggregate=\'' + wpbc_aggregate_temp + '\'';
                        }
                    }                        
                }
                ////////////////////////////////////////////////////////////////
                // [bookingcalendar]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingcalendar' ) {                       
                    
                    wpbc_shortcode += wpbc_shortcode_type;
                            
                    if ( jQuery( '#wpbc_bookingcalendar_type' ).length > 0 ) {
						if ( jQuery( '#wpbc_bookingcalendar_type' ).val() === null ) {											//FixIn: 8.2.1.12
							jQuery( '#wpbc_text_put_in_shortcode' ).val( '<?php esc_js( __( 'No booking resources', 'booking' )) ?>' );
							return;
						} else {
							wpbc_shortcode += ' type=' + jQuery( '#wpbc_bookingcalendar_type' ).val().trim();
						}
                    }
                    
                    if ( parseInt( jQuery( '#wpbc_bookingcalendar_nummonths' ).val().trim() ) > 1 )
                        wpbc_shortcode += ' nummonths=' + jQuery( '#wpbc_bookingcalendar_nummonths' ).val().trim();
                     
                    if ( jQuery('#wpbc_bookingcalendar_startmonth_active').is(':checked') ) { 
                         wpbc_shortcode += ' startmonth=\'' + jQuery( '#wpbc_bookingcalendar_startmonth_year' ).val().trim() + '-' + jQuery( '#wpbc_bookingcalendar_startmonth_month' ).val().trim() + '\'';
                    }
                    if ( jQuery( '#wpbc_bookingcalendar_options' ).length > 0 ) { 
                        var wpbc_options_temp = jQuery( '#wpbc_bookingcalendar_options' ).val().trim();
                        if ( wpbc_options_temp.length > 0 )
                            wpbc_shortcode += ' options=\'' + wpbc_options_temp + '\'';
                    }    
                    if ( jQuery( '#wpbc_bookingcalendar_aggregate' ).length > 0 ) { 
                        var wpbc_aggregate_temp = jQuery( '#wpbc_bookingcalendar_aggregate' ).val();
                        
                        if ( ( wpbc_aggregate_temp != null ) && ( wpbc_aggregate_temp.length > 0 )  ){ 
                            wpbc_aggregate_temp = wpbc_aggregate_temp.join(';')
                            
                            if ( wpbc_aggregate_temp != 0 )                     // Check about 0=>'None'    
                                wpbc_shortcode += ' aggregate=\'' + wpbc_aggregate_temp + '\'';
                        }
                    }                        
                }
                ////////////////////////////////////////////////////////////////
                // [bookingselect]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingselect' ) {                       
                    
                    wpbc_shortcode += wpbc_shortcode_type;
                                        
                    var wpbc_selecttype_temp = jQuery( '#wpbc_bookingselect_type' ).val();                        
                    if ( ( wpbc_selecttype_temp != null ) && ( wpbc_selecttype_temp.length > 0 )  ){ 
                        wpbc_selecttype_temp = wpbc_selecttype_temp.join(',')

                        if ( wpbc_selecttype_temp != 0 )                     // Check about 0=>'None'    
                            wpbc_shortcode += ' type=\'' + wpbc_selecttype_temp + '\'';
                    } 
                       
                    if ( jQuery( '#wpbc_bookingselect_selected_type' ).val().trim() != '0' ) { 
                        wpbc_shortcode += ' selected_type=\'' + jQuery( '#wpbc_bookingselect_selected_type' ).val().trim() + '\'';
                    }
                    if ( jQuery( '#wpbc_bookingselect_form_type' ).length > 0 ) { 
                        var form_type_temp = jQuery( '#wpbc_bookingselect_form_type' ).val().trim();
                        if ( form_type_temp != '0' )
                            wpbc_shortcode += ' form_type=\'' + jQuery( '#wpbc_bookingselect_form_type' ).val().trim() + '\'';
                    }
                    
                    if ( jQuery( '#wpbc_bookingselect_label' ).val().trim() != '' ) {
                        var wpbc_shortcode_label_temp = jQuery( '#wpbc_bookingselect_label' ).val().trim();
                        wpbc_shortcode_label_temp = wpbc_shortcode_label_temp.replace(/'/gi, '');
                        wpbc_shortcode += ' label=\'' + wpbc_shortcode_label_temp + '\'';
                    }
                    if ( jQuery( '#wpbc_bookingselect_first_option_title' ).val().trim() != '' ) {
                        var wpbc_shortcode_label_temp = jQuery( '#wpbc_bookingselect_first_option_title' ).val().trim();
                        wpbc_shortcode_label_temp = wpbc_shortcode_label_temp.replace(/'/gi, '');
                        wpbc_shortcode +=  ' first_option_title=\'' + wpbc_shortcode_label_temp + '\'';  
                    }
                    if ( parseInt( jQuery( '#wpbc_bookingselect_nummonths' ).val().trim() ) > 1 )
                        wpbc_shortcode += ' nummonths=' + jQuery( '#wpbc_bookingselect_nummonths' ).val().trim();
                     
                    if ( jQuery('#wpbc_bookingselect_startmonth_active').is(':checked') ) { 
                         wpbc_shortcode += ' startmonth=\'' + jQuery( '#wpbc_bookingselect_startmonth_year' ).val().trim() + '-' + jQuery( '#wpbc_bookingselect_startmonth_month' ).val().trim() + '\'';
                    }
                    if ( jQuery( '#wpbc_bookingselect_options' ).length > 0 ) { 
                        var wpbc_options_temp = jQuery( '#wpbc_bookingselect_options' ).val().trim();
                        if ( wpbc_options_temp.length > 0 )
                            wpbc_shortcode += ' options=\'' + wpbc_options_temp + '\'';
                    }    
                }
                ////////////////////////////////////////////////////////////////
                // [bookingform]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingform' ) {                       
                    
                    wpbc_shortcode += wpbc_shortcode_type;
                            
                    if ( jQuery( '#wpbc_bookingform_type' ).length > 0 ) {
						if ( jQuery( '#wpbc_bookingform_type' ).val() === null ) {											//FixIn: 8.2.1.12
							jQuery( '#wpbc_text_put_in_shortcode' ).val( '<?php esc_js( __( 'No booking resources', 'booking' )) ?>' );
							return;
						} else {
							wpbc_shortcode += ' type=' + jQuery( '#wpbc_bookingform_type' ).val().trim();
						}
                    }
                    if ( jQuery( '#wpbc_bookingform_form_type' ).length > 0 ) { 
                        var form_type_temp = jQuery( '#wpbc_bookingform_form_type' ).val().trim();
                        if ( form_type_temp != 'standard' )
                            wpbc_shortcode += ' form_type=\'' + jQuery( '#wpbc_bookingform_form_type' ).val().trim() + '\'';
                    }
                    var wpbc_selected_day = jQuery( '#wpbc_bookingform_selected_dates_day' ).val().trim();
                    if ( wpbc_selected_day < 10 ) wpbc_selected_day = '0' + wpbc_selected_day;
                    var wpbc_selected_month = jQuery( '#wpbc_bookingform_selected_dates_month' ).val().trim();
                    if ( wpbc_selected_month < 10 ) wpbc_selected_month = '0' + wpbc_selected_month;
                    wpbc_shortcode += ' selected_dates=\'' + wpbc_selected_day + '.' + wpbc_selected_month + '.' + jQuery( '#wpbc_bookingform_selected_dates_year' ).val().trim() + '\'';
                }
                ////////////////////////////////////////////////////////////////
                // [bookingsearch]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingsearch' ) {                       
                    
                    if ( jQuery('#wpbc_shortcode__bookingsearchresults').is(':checked') ) {     // By  default chcked [searchresults] shortcode RADIO, so update it.
                        
                        wpbc_shortcode_type = 'bookingsearchresults';
                        
                        jQuery( '#wpbc_shortcode_type' ).val( 'bookingsearchresults' );
                        
                    } else {
                        
                        wpbc_shortcode += wpbc_shortcode_type;
                        
                        if (   ( jQuery('#wpbc_bookingsearch_searchresults_active').is(':checked') ) 
                            && ( jQuery( '#wpbc_bookingsearch_searchresults' ).val().trim() != '' )
                            ) {
                                wpbc_shortcode += ' searchresults=\'' + jQuery( '#wpbc_bookingsearch_searchresults' ).val().trim() + '\''; }

                        if ( jQuery( '#wpbc_bookingsearch_searchresultstitle' ).val().trim() != '' ) {                            
                            var wpbc_shortcode_label_temp = jQuery( '#wpbc_bookingsearch_searchresultstitle' ).val().trim();
                            wpbc_shortcode_label_temp = wpbc_shortcode_label_temp.replace(/'/gi, '');
                            wpbc_shortcode += ' searchresultstitle=\'' + wpbc_shortcode_label_temp + '\'';
                        }
                        if ( jQuery( '#wpbc_bookingsearch_noresultstitle' ).val().trim() != '' ) {
                            var wpbc_shortcode_label_temp = jQuery( '#wpbc_bookingsearch_noresultstitle' ).val().trim();
                            wpbc_shortcode_label_temp = wpbc_shortcode_label_temp.replace(/'/gi, '');                                                        
                            wpbc_shortcode += ' noresultstitle=\'' + wpbc_shortcode_label_temp + '\'';
                        }
                        if ( jQuery( '#wpbc_bookingsearch_users' ).length > 0 ) {
                            if ( jQuery( '#wpbc_bookingsearch_users' ).val().trim() != '' )
                                wpbc_shortcode += ' users=\'' + jQuery( '#wpbc_bookingsearch_users' ).val().trim() + '\'';
                        }                        
                    }
                }        
                ////////////////////////////////////////////////////////////////
                // [bookingsearchresults]
                ////////////////////////////////////////////////////////////////
                if ( wpbc_shortcode_type == 'bookingsearchresults' ) {                       
                
                        wpbc_shortcode += wpbc_shortcode_type;
                }
                ////////////////////////////////////////////////////////////////
                // [bookingedit]
                ////////////////////////////////////////////////////////////////
                if (
                       ( wpbc_shortcode_type == 'bookingother' ) 
                    || ( wpbc_shortcode_type == 'bookingedit' )
					|| ( wpbc_shortcode_type == 'bookingcustomerlisting' )
                    || ( wpbc_shortcode_type == 'bookingresource' ) 
                    ) {                       
                    
                    if ( jQuery('#wpbc_shortcode__bookingedit').is(':checked') ) {                        
                        jQuery('.wpbc_shortcode_bookingother').hide();
                        jQuery('.wpbc_shortcode_bookingedit').show();                                                        
                        wpbc_shortcode_type = 'bookingedit';                        
                        jQuery( '#wpbc_shortcode_type' ).val( 'bookingedit' );  
                        
                        wpbc_shortcode += wpbc_shortcode_type;
                    } 
                    
                    if ( jQuery('#wpbc_shortcode__bookingresource').is(':checked') ) {                        
                        jQuery('.wpbc_shortcode_bookingother').hide();
                        jQuery('.wpbc_shortcode_bookingresource').show();                                                        
                        wpbc_shortcode_type = 'bookingresource';                        
                        jQuery( '#wpbc_shortcode_type' ).val( 'bookingresource' );                        
                        
                        wpbc_shortcode += wpbc_shortcode_type;
                        
                        if ( jQuery( '#wpbc_bookingresource_type' ).length > 0 ) {
							if ( jQuery( '#wpbc_bookingresource_type' ).val() === null ) {											//FixIn: 8.2.1.12
								jQuery( '#wpbc_text_put_in_shortcode' ).val( '<?php esc_js( __( 'No booking resources', 'booking' )) ?>' );
								return;
							} else {
								wpbc_shortcode += ' type=' + jQuery( '#wpbc_bookingresource_type' ).val().trim();
							}
                        }

                        if ( jQuery( '#wpbc_bookingresource_show' ).val().trim() != 'title' )
                            wpbc_shortcode += ' show=\'' + jQuery( '#wpbc_bookingresource_show' ).val().trim() + '\'';
                    } 

                    if ( jQuery('#wpbc_shortcode__bookingcustomerlisting').is(':checked') ) {
                        jQuery('.wpbc_shortcode_bookingother').hide();
                        jQuery('.wpbc_shortcode_bookingcustomerlisting').show();
                        wpbc_shortcode_type = 'bookingcustomerlisting';
                        jQuery( '#wpbc_shortcode_type' ).val( 'bookingcustomerlisting' );

                        wpbc_shortcode += wpbc_shortcode_type;
                    }
                }
                ////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////////////
                wpbc_shortcode += ']';
                
                jQuery( '#wpbc_text_put_in_shortcode' ).val( wpbc_shortcode );
            }
            
            
            /** Open TinyMCE Modal */
            function wpbc_tiny_btn_click( tag ) {
                
                jQuery('#wpbc_tiny_modal').wpbc_modal({
                    keyboard: false
                  , backdrop: true
                  , show: true
                });
                //FixIn: 8.3.3.99
                jQuery( "#wpbc_text_gettenberg_section_id" ).val( '' );
            }            
            
            
            /** Open TinyMCE Modal */
            function wpbc_tiny_close() {
                
                jQuery('#wpbc_tiny_modal').wpbc_modal('hide');
            }    


            /** Send text  to editor */
            function wpbc_send_text_to_editor( h ) {

				// FixIn: 8.3.3.99
            	if ( typeof( wpbc_send_text_to_gutenberg ) == 'function' ){
					var is_send = wpbc_send_text_to_gutenberg( h );
					if ( true === is_send ){
						return;
					}
				}

                    var ed, mce = typeof(tinymce) != 'undefined', qt = typeof(QTags) != 'undefined';

                    if ( !wpActiveEditor ) {
                            if ( mce && tinymce.activeEditor ) {
                                    ed = tinymce.activeEditor;
                                    wpActiveEditor = ed.id;
                            } else if ( !qt ) {
                                    return false;
                            }
                    } else if ( mce ) {
                            if ( tinymce.activeEditor && (tinymce.activeEditor.id == 'mce_fullscreen' || tinymce.activeEditor.id == 'wp_mce_fullscreen') )
                                    ed = tinymce.activeEditor;
                            else
                                    ed = tinymce.get(wpActiveEditor);
                    }

                    if ( ed && !ed.isHidden() ) {
                            // restore caret position on IE
                            if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
                                    ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

                            if ( h.indexOf('[caption') !== -1 ) {
                                    if ( ed.wpSetImgCaption )
                                            h = ed.wpSetImgCaption(h);
                            } else if ( h.indexOf('[gallery') !== -1 ) {
                                    if ( ed.plugins.wpgallery )
                                            h = ed.plugins.wpgallery._do_gallery(h);
                            } else if ( h.indexOf('[embed') === 0 ) {
                                    if ( ed.plugins.wordpress )
                                            h = ed.plugins.wordpress._setEmbed(h);
                            }

                            ed.execCommand('mceInsertContent', false, h);
                    } else if ( qt ) {
                            QTags.insertContent(h);
                    } else {
                            document.getElementById(wpActiveEditor).value += h;
                    }

                    try{tb_remove();}catch(e){};
            }            
        <?php 

        ?>
        </script>
        <!-- End WPBC JavaScript -->
        <?php

    }
    //                                                                              </editor-fold>
}

$wpbc_pages_where_insert_btn = array( 'post-new.php', 'page-new.php', 'post.php', 'page.php', 'widgets.php', 'customize.php' );            //FixIn: 8.8.2.11		//FixIn: 8.8.2.12

if ( ( in_array( basename( $_SERVER['PHP_SELF'] ), $wpbc_pages_where_insert_btn ) ) ) {
    
    new WPBC_TinyMCE_Buttons( 
                            array(
                                      'tiny_prefix'     => 'wpbc_tiny'
                                    , 'tiny_icon_url'   => WPBC_PLUGIN_URL . '/assets/img/bc_black-16x16.png'
                                    , 'tiny_js_plugin'  => WPBC_PLUGIN_URL . '/js/wpbc_tinymce_btn.js'
                                    , 'tiny_js_function' => 'wpbc_init_tinymce_buttons'                     // This function NAME exist inside of this file: ['tiny_js_plugin']
                                    , 'tiny_btn_row'    => 1
                                    , 'pages_where_insert' => $wpbc_pages_where_insert_btn
                                    , 'buttons'            => array(
                                                                'booking_insert' => array(
                                                                                              'hint'  => __('Insert booking calendar' ,'booking')
                                                                                            , 'title' => __('Booking calendar' ,'booking')
                                                                                            , 'img'   => WPBC_PLUGIN_URL . '/assets/img/bc_black-16x16.png'
                                                                                            , 'class' => 'bookig_buttons'
                                                                                            , 'js_func_name_click'    => 'wpbc_tiny_btn_click'
                                                                                            , 'bookmark'              => 'booking'
                                                                                            , 'is_close_bookmark'     => 0
                                                                                        )
                                                                )
                            )
                        );
}