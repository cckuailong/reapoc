<?php
/**
 * @version 1.0
 * @package Content
 * @category Menu
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com 
 * 
 * @modified 2016-12-09
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly


if ( ! defined( 'WPBC_EMAIL_TRASH_PREFIX' ) )   define( 'WPBC_EMAIL_TRASH_PREFIX',  'booking_email_' ); // Its defined in api-emails.php file & its same for all emails, here its used only for easy coding...

if ( ! defined( 'WPBC_EMAIL_TRASH_ID' ) )       define( 'WPBC_EMAIL_TRASH_ID',      'trash' );      /* Define Name of Email Template.   
                                                                                                                   Note. Prefix "booking_email_" defined in api-emails.php file. 
                                                                                                                   Full name of option is - "booking_email_trash"
                                                                                                                   Other email templates names:
                                                                                                                                            - 'new_admin'    
                                                                                                                                            - 'new_visitor'    
                                                                                                                                            - 'approved'    
                                                                                                                                            - 'deny'                 -- pending, trash, deleted
                                                                                                                                            - 'payment_request'    
                                                                                                                                            - 'modification'        
                                                                                                                */


require_once( WPBC_PLUGIN_DIR . '/core/any/api-emails.php' );           // API



// <editor-fold     defaultstate="collapsed"                        desc=" Import Old Data "  >

function wpbc_import6_is_old_email_trash_exist() {
    
    $is_old_data_exist = get_bk_option( 'booking_email_deny_content' );
        
    if ( ! empty( $is_old_data_exist ) )                                        // Check if Old data exist
        return  true;
    else
        return  false;
}

function wpbc_import6_get_old_email_trash_data() {
    
    $default_values = array();
    if ( wpbc_import6_is_old_email_trash_exist() ) {
                
        $default_values['enabled']  = get_bk_option( 'booking_is_email_deny_adress' );
        $default_values['copy_to_admin']       = get_bk_option( 'booking_is_email_deny_send_copy_to_admin' );          //Parse it to  Name and Email 
        $default_values['from']     = get_bk_option( 'booking_email_deny_adress' );     //Parse it to  Name and Email, relpace [visitoremail]
        $default_values['subject']  = get_bk_option( 'booking_email_deny_subject' );
        $default_values['content']  = get_bk_option( 'booking_email_deny_content' );

    }
    return $default_values;
}


function wpbc_import6_is_new_email_trash_exist() {
    
    $is_data_exist = get_bk_option( WPBC_EMAIL_TRASH_PREFIX . WPBC_EMAIL_TRASH_ID );           // ''booking_email_' - defined in api-emails.php  file.
        
    if ( ! empty( $is_data_exist ) )                                            // Check if data exist
        return  true;
    else
        return  false;
}


function wpbc_import6_email__trash__get_fields_array_for_activation() {
    
           
    if ( ! wpbc_import6_is_new_email_trash_exist() ) {                      // New Email Template NOT exist
    
        // Import
        $old_data = wpbc_import6_get_old_email_trash_data();
        if ( ! empty( $old_data ) ) {                                           // Get Old data
                           
            /*  
            $old_data = [ 
                            [enabled] => On
                            [to] => "Booking system" <beta@wpbookingcalendar.com>
                            [from] => [visitoremail]
                            [subject] => New booking
                            [content] => You need to approve a new booking [bookingtype] for: [dates]<br/><br/> Person detail information:<br/> [content]<br/><br/> Currently a new booking is waiting for approval. Please visit the moderation panel [moderatelink]<br/><br/>Thank you, Beta<br/>[siteurl]
                        ] 
             wpbc_get_email_parts() >>> return [
                            [email] => beta@wpbookingcalendar.com
                            [title] => Booking system
                            [original] => "Booking system" 
                            [original_to_show] => "Booking system" <beta@wpbookingcalendar.com>
                        ]
             */

            // Make transform - emails    
//            $email_to   = wpbc_get_email_parts( $old_data['to'] );
            $email_from = wpbc_get_email_parts( $old_data['from'] );

//            $old_data['to']         = $email_to['email'];                       // 'beta@wpbookingcalendar.com'     // booking_email_reservation_adress
//            $old_data['to_name']    = $email_to['title'];                       // 'Booking system'                 // booking_email_reservation_adress
            /* Description  of Code.
             * Here we replacing any  shortcodes, like [visitoremail] to get_option( 'admin_email' )
             * because we are setting in header "Reply-To" visitor emil  from the booking form - its exist  by hook for this email  template at the wpbc-emails.php  file
             * 
             * Here we need to  use for field "From" email  with DNS same as DNS in the website. In most  cases its have to be the get_option( 'admin_email' )
             * 
             * After parsing with wpbc_get_email_parts,  if we was have [visitoremail] in this field,  then $email_from['email'] will be empty. So  we check it and set  admin email.
             * 
             */
            $old_data['from']       = ( empty( $email_from['email'] ) ? get_option( 'admin_email' ) /*$email_from['original']*/ : $email_from['email'] );    // [visitoremail] | email@server.com   // booking_email_reservation_from_adress
            $old_data['from_name']  = $email_from['title'];                     // [visitoremail] | 'Booking System' | ''    // booking_email_reservation_from_adress            
            
//            $old_data['subject'] = html_entity_decode( $old_data['subject'] );
//            $old_data['content'] = html_entity_decode( $old_data['content'] );
        } 

        // Default  
        $init_fields_values = $old_data; 
        $mail_api = new WPBC_Emails_API_Trash( WPBC_EMAIL_TRASH_ID , $init_fields_values );    
        
        $default_fields_in_settings = $mail_api->get_default_values() ;         // Get default fields values, from settings - defined in function init_settings_fields {class WPBC_Emails_API_Trash}

        $fields_values = wp_parse_args( $old_data, $default_fields_in_settings );
        
        return array( WPBC_EMAIL_TRASH_PREFIX . WPBC_EMAIL_TRASH_ID => $fields_values );
        
        /*
        Array ( 
                [booking_email_trash] => Array
                                                (
                                                    [enabled] => On
                                                    [to] => beta@wpbookingcalendar.com
                                                    [to_name] => Booking system
                                                    [from] => admin@wpbookingcalendar.com
                                                    [from_name] => 
                                                    [subject] => New booking
                                                    [content] => You need to approve a new booking [bookingtype] for: [dates]...
                                                    [header_content] => 
                                                    [footer_content] => 
                                                    [template_file] => plain
                                                    [base_color] => #557da1
                                                    [background_color] => #f5f5f5
                                                    [body_color] => #fdfdfd
                                                    [text_color] => #505050
                                                    [email_content_type] => html
                                                )
        )

        // $mail_api->save_to_db( $fields_values );

         */        
                        
    } else {                // New Email Template is Exist   - return empty array(), its will  make loading of data from  DB,  during activation Mail API class.
        return array( WPBC_EMAIL_TRASH_PREFIX . WPBC_EMAIL_TRASH_ID => array() );
    }
}
// </editor-fold>



/** Email   F i e l d s  */
class WPBC_Emails_API_Trash extends WPBC_Emails_API  {                       // O v e r r i d i n g     "WPBC_Emails_API"     ClASS

        
    /**  Overrided functions - define Email Fields & Values  */
    public function init_settings_fields() {
        
        $this->fields = array();

        $this->fields['enabled'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'On'            
                                    , 'title'       => __('Enable / Disable', 'booking')
                                    , 'label'       => __('Enable this email notification', 'booking')   
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );

        $this->fields['enabled_hr'] = array( 'type' => 'hr' );

        $this->fields['copy_to_admin'] = array(   
                                      'type'        => 'checkbox'
                                    , 'default'     => 'Off'            
                                    , 'title'       => __('Copy(ies)', 'booking')																						//FixIn: 8.1.2.17.1
                                    , 'label'       => __('Enable / disable sending email(s) to additional addresses', 'booking') . ' (admin@; sales@; others@)'		//FixIn: 8.1.2.17.1
                                    , 'description' => ''
                                    , 'group'       => 'general'

                                );
        
//FixIn: 8.1.2.17
        $this->fields['to_html_prefix'] = array(
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top" class="wpbc_tr_copy_to_admin_to">
                                                        <th scope="row">
                                                            <label class="wpbc-form-email" for="'
                                                                             . esc_attr( 'approved_to' )
                                                            . '">' . wp_kses_post(  __('To' ,'booking') )  . ' (' . strtolower( __('Copy(ies)', 'booking') ). ')'		//FixIn: 8.1.2.17.1
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );
        $this->fields['to'] = array(
                                      'type'        => 'text'               // We are using here 'text'  and not 'email',  for ability to  save several comma seperated emails.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Email Address', 'booking') . '. ' . __('Required', 'booking') . '. '
													   	. __('You can put multiple emails separated by', 'booking') . ' <code>;</code>'									//FixIn: 8.1.2.17.1
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field'  => true
                                    , 'validate_as' => array(  )
                                );
        $this->fields['to_html_middle'] = array(
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );
        $this->fields['to_name'] = array(
                                      'type'        => 'text'
                                    , 'default'     => 'Booking system'
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Title', 'booking') . '  (' . __('optional', 'booking') . ').' //. ' ' . __('If empty then title defined as WordPress', 'booking')
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['to_html_sufix'] = array(
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'
                        );

        $this->fields['to_hr'] = array( 'type' => 'hr' );
/**/
//FixIn: 8.1.2.17 - END

    

        $this->fields['from_html_prefix'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '<tr valign="top">
                                                        <th scope="row">
                                                            <label class="wpbc-form-email" for="' 
                                                                             . esc_attr( 'trash_from' ) 
                                                            . '">' . wp_kses_post(  __('From' ,'booking') ) 
                                                            . '</label>
                                                        </th>
                                                        <td><fieldset style="float:left;width:50%;margin-right:5%;">'
                                );

//$activated_user = apply_bk_filter( 'wpbc_is_user_in_activation_process' );                
//if ( $activated_user === false ) {                                      // Users activation
//   $user_bk_email =  get_user_option('user_email',  $activated_user );        
//}
        $this->fields['from'] = array(  
                                      'type'        => 'email'              // Its can  be only 1 email,  so check  it as Email  field.
                                    , 'default'     => get_option( 'admin_email' )
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Email Address', 'booking') . '. ' . __('Required', 'booking') . '.' 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                    , 'validate_as' => array( 'required' )
                                );            
        $this->fields['from_html_middle'] = array(   
                                    'type'          => 'pure_html'
                                    , 'group'       => 'general'
                                    , 'html'        => '</fieldset><fieldset style="float:left;width:45%;">'
                                );                
        $this->fields['from_name'] = array(  
                                      'type'        => 'text'
                                    , 'default'     => 'Booking system'
                                    //, 'placeholder' => ''
                                    , 'title'       => ''
                                    , 'description' => __('Title', 'booking') . '  (' . __('optional', 'booking') . ').' //. ' ' . __('If empty then title defined as WordPress', 'booking') 
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'only_field' => true
                                );
        $this->fields['from_html_sufix'] = array(   
                                'type'          => 'pure_html'
                                , 'group'       => 'general'
                                , 'html'        => '    </fieldset>
                                                        </td>
                                                    </tr>'            
                        );                    

        $this->fields['from_hr'] = array( 'type' => 'hr' );            

        

        $this->fields['subject'] = array(   
                                      'type'        => 'text'
                                    , 'default'     => __( 'Your booking has been declined', 'booking' )
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Subject', 'booking')
                                    , 'description' => sprintf(__('Type your email %ssubject%s for the booking confimation message.' ,'booking'),'<b>','</b>') . ' ' . __('Required', 'booking') . '.'
                                    , 'description_tag' => ''
                                    , 'css'         => 'width:100%'
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'validate_as' => array( 'required' )
                            );

        $blg_title = get_option( 'blogname' );
        $blg_title = str_replace( array( '"', "'" ), '', $blg_title );
        
        $email_content = sprintf( __( 'Your booking %s for: %s has been  canceled. %sThank you, %s', 'booking' ), '[bookingtype]', '[dates]', '<br/>[denyreason]<br/><br/>[content]<br/><br/>', $blg_title . '<br/>[siteurl]' );
        
        $this->fields['content'] = array(   
                                      'type'        => 'wp_textarea'
                                    , 'default'     => $email_content
                                    //, 'placeholder' => ''
                                    , 'title'       => __('Content', 'booking')
                                    , 'description' => __('Type your email message content. ', 'booking') 
                                    , 'description_tag' => ''
                                    , 'css'         => ''
                                    , 'group'       => 'general'
                                    , 'tr_class'    => ''
                                    , 'rows'        => 10
                                    , 'show_in_2_cols' => true
                            );
//        $this->fields['content'] = htmlspecialchars( $this->fields['content'] );// Convert > to &gt;
//        $this->fields['content'] = html_entity_decode( $this->fields['content'] );// Convert &gt; to >
        


        ////////////////////////////////////////////////////////////////////
        // Style
        ////////////////////////////////////////////////////////////////////


        $this->fields['header_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Heading', 'booking')
                                    , 'description'  => __('Enter main heading contained within the email notification.', 'booking') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => "width:100%;"
                                    , 'group' => 'parts'                        
                            );
        $this->fields['footer_content'] = array(   
                                    'type' => 'textarea'
                                    , 'default' => ''
                                    , 'title' => __('Email Footer Text', 'booking')
                                    , 'description'  => __('Enter text contained within footer of the email notification', 'booking') 
                                    //, 'placeholder' => ''
                                    , 'rows'  => 2
                                    , 'css' => 'width:100%;'
                                    , 'group' => 'parts'                        
                            );

        $this->fields['template_file'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email template', 'booking')
                                    , 'description' => __('Choose email template.', 'booking')  
                                    , 'description_tag' => 'span'
                                    , 'css' => ''
                                    , 'options' => array(
                                                            'plain'     => __('Plain (without styles)', 'booking')  
                                                          , 'standard'  => __('Standard 1 column', 'booking')                                                              
                                                    )      
                                    , 'group' => 'style'
                            );

        $this->fields['template_file_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array( sprintf( __('You can override this email template in this folder %s', 'booking')                                                
                                                                , '<code>' . realpath( dirname(__FILE__) . '/../any/emails_tpl/' ) . '</code>' ) 
                                                      )
                                    , 'cols' => 2
                                    , 'group' => 'style'
                            );

        $this->fields['base_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#557da1'
                                    , 'title'   => __('Base Color', 'booking')
                                    , 'description'  => __('The base color for email templates.', 'booking') 
                                                        . ' ' . __('Default color', 'booking') .': <code>#557da1</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );                
        $this->fields['background_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#f5f5f5'
                                    , 'title'   => __('Background Color', 'booking')
                                    , 'description' => __('The background color for email templates.', 'booking') 
                                                       . ' ' . __('Default color', 'booking') .': <code>#f5f5f5</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['body_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#fdfdfd'
                                    , 'title'   => __('Email Body Background Color', 'booking')
                                    , 'description' =>  __('The main body background color for email templates.', 'booking') 
                                                        . ' ' . __('Default color', 'booking') .': <code>#fdfdfd</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );
        $this->fields['text_color'] = array(   
                                    'type'      => 'color'
                                    , 'default'   => '#505050'
                                    , 'title'   => __('Email Body Text Colour', 'booking')
                                    , 'description' =>  __('The main body text color for email templates.', 'booking') 
                                                        . ' ' . __('Default color', 'booking') .': <code>#505050</code>.'
                                    , 'group'   => 'style'
                                    , 'tr_class'    => 'template_colors'
                            );


        ////////////////////////////////////////////////////////////////////
        // Email format: Plain, HTML, MultiPart
        ////////////////////////////////////////////////////////////////////


        $this->fields['email_content_type'] = array(   
                                    'type' => 'select'
                                    , 'default' => 'plain'
                                    , 'title' => __('Email format', 'booking')
                                    , 'description' => __('Choose which format of email to send.', 'booking')  
                                    , 'description_tag' => 'p'
                                    , 'css' => 'width:100%;'
                                    , 'options' => array(
                                                            'plain' => __('Plain text', 'booking')  
                                                        //  , 'html' => __('HTML', 'booking')  
                                                        //  , 'multipart' => __('Multipart', 'booking')  
                                                    )      
                                    , 'group' => 'email_content_type'
                            );
        if ( class_exists( 'DOMDocument' ) ) {
            $this->fields['email_content_type']['options']['html']        = __('HTML', 'booking');
            $this->fields['email_content_type']['options']['multipart']   = __('Multipart', 'booking');

            $this->fields['email_content_type']['default'] = 'html';
        }



        ////////////////////////////////////////////////////////////////////
        // Help
        ////////////////////////////////////////////////////////////////////

        $this->fields['content_help'] = array(   
                                    'type' => 'help'                                        
                                    , 'value' => array()
                                    , 'cols' => 2
                                    , 'group' => 'help'
                            );

        $skip_shortcodes = array(
                               // 'denyreason'
                               'moderatelink'
                              , 'paymentreason'
                              , 'visitorbookingediturl'
		  				      //, 'visitorbookingslisting'             //FixIn: 8.1.3.5.1
                              , 'visitorbookingcancelurl'
                              , 'visitorbookingpayurl'
                          );
        $email_example = sprintf(__('For example: "You have a new reservation %s on the following date(s): %s Contact information: %s You can approve or cancel this booking at: %s Thank you, Reservation service."' ,'booking'),'','[dates]&lt;br/&gt;&lt;br/&gt;','&lt;br/&gt; [content]&lt;br/&gt;&lt;br/&gt;', htmlentities( ' <a href="[moderatelink]">'.__('here' ,'booking').'</a> ') . '&lt;br/&gt;&lt;br/&gt; ');

        $help_fields = wpbc_get_email_help_shortcodes( $skip_shortcodes, $email_example );

        foreach ( $help_fields as $help_fields_key => $help_fields_value ) {
            $this->fields['content_help']['value'][] = $help_fields_value;
        }
            
    }    
        
}



/** Settings Emails   P a g e  */
class WPBC_Settings_Page_Email_Trash extends WPBC_Page_Structure {

    public $email_settings_api = false;
    
    
    /**
	 * Define interface for  Email API
     * 
     * @param string $selected_email_name - name of Email template
     * @param array $init_fields_values - array of init form  fields data
     * @return object Email API
     */
    public function mail_api( $selected_email_name ='',  $init_fields_values = array() ){
        
        if ( $this->email_settings_api === false ) {
            $this->email_settings_api = new WPBC_Emails_API_Trash( $selected_email_name , $init_fields_values );    
        }
        
        return $this->email_settings_api;
    }
    
    
    public function in_page() {                                                 // P a g e    t a g
        return 'wpbc-settings';
    }
    
    
    public function tabs() {                                                    // T a b s      A r r a y
        
        $tabs = array();
                
//        $tabs[ 'email' ] = array(
//                              'title'     => __( 'Emails', 'booking')               // Title of TAB    
//                            , 'page_title'=> __( 'Emails Settings', 'booking')      // Title of Page    
//                            , 'hint'      => __( 'Emails Settings', 'booking')      // Hint                
//                            //, 'link'      => ''                                   // Can be skiped,  then generated link based on Page and Tab tags. Or can  be extenral link
//                            //, 'position'  => ''                                   // 'left'  ||  'right'  ||  ''
//                            //, 'css_classes'=> ''                                  // CSS class(es)
//                            //, 'icon'      => ''                                   // Icon - link to the real PNG img
//                            , 'font_icon' => 'glyphicon glyphicon-envelope'         // CSS definition  of forn Icon
//                            //, 'default'   => false                                // Is this tab activated by default or not: true || false. 
//                            //, 'disabled'  => false                                // Is this tab disbaled: true || false. 
//                            //, 'hided'     => false                                // Is this tab hided: true || false. 
//                            , 'subtabs'   => array()   
//                    );

        $subtabs = array();
        

        $is_data_exist = get_bk_option( WPBC_EMAIL_NEW_ADMIN_PREFIX . WPBC_EMAIL_TRASH_ID );
        if (  ( ! empty( $is_data_exist ) ) && ( isset( $is_data_exist['enabled'] ) ) && ( $is_data_exist['enabled'] == 'On' )  )
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-check"></i> &nbsp; ';
        else 
            $icon = '<i class="menu_icon icon-1x glyphicon glyphicon-unchecked"></i> &nbsp; ';

        if (  ( ! empty( $is_data_exist ) ) && ( isset( $is_data_exist['copy_to_admin'] ) ) && ( $is_data_exist['copy_to_admin'] == 'On' )  )
            $sufix = '<sup> 2</sup>';
        else 
            $sufix = '';
        
        
        $subtabs['trash'] = array( 
                            'type' => 'subtab'                                  // Required| Possible values:  'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
                            , 'title' =>  $icon . __('Trash / Reject' ,'booking') . $sufix        // Title of TAB
                            , 'page_title' => __('Emails Settings', 'booking')  // Title of Page   
                            , 'hint' => __('Customization of email template, which is sent to Visitor after rejecting of booking' ,'booking')   //FixIn: 8.1.2.17.1
                            , 'link' => ''                                      // link
                            , 'position' => ''                                  // 'left'  ||  'right'  ||  ''
                            , 'css_classes' => ''                               // CSS class(es)
                            //, 'icon' => 'http://.../icon.png'                 // Icon - link to the real PNG img
                            //, 'font_icon' => 'glyphicon glyphicon-envelope'   // CSS definition of Font Icon
                            , 'default' =>  false                                // Is this sub tab activated by default or not: true || false. 
                            , 'disabled' => false                               // Is this sub tab deactivated: true || false. 
                            , 'checkbox'  => false                              // or definition array  for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' )   //, 'checkbox'  => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
                            , 'content' => 'content'                            // Function to load as conten of this TAB
                        );
        
        $tabs[ 'email' ]['subtabs'] = $subtabs;
                        
        return $tabs;
    }
    
    
    /** Show Content of Settings page */
    public function content() {

//debuge ( wpbc_import6_email__trash__get_fields_array_for_activation() );

        $this->css();
        
        ////////////////////////////////////////////////////////////////////////
        // Checking 
        ////////////////////////////////////////////////////////////////////////
        
        do_action( 'wpbc_hook_settings_page_header', 'emails_settings');       // Define Notices Section and show some static messages, if needed
        
        if ( ! wpbc_is_mu_user_can_be_here('activated_user') ) return false;    // Check if MU user activated, otherwise show Warning message.
   
        // if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false;  // User is not Super admin, so exit.  Basically its was already checked at the bottom of the PHP file, just in case.

        
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////
        
        /* Check if New Email Template   Exist or NOT
         * Exist     -  return  empty array in format: array( OPTION_NAME => array() ) 
         *              Its will  load DATA from DB,  during creattion mail_api CLASS
         *              during initial activation  of the API  its try  to get option  from DB
         *              We need to define this API before checking POST, to know all available fields
         *              Define Email Name & define field values from DB, if not exist, then default values. 
         * Not Exist -  import Old Data from DB
         *              or get "default" data from settings and return array with  this data
         *              This data its initial  parameters for definition fields in mail_api CLASS 
         * 
         */
        
        $init_fields_values = wpbc_import6_email__trash__get_fields_array_for_activation();
        
        // Get Value of first element - array of default or imported OLD data,  because need only  array  of values without key - name of options for wp_options table
        $init_fields_values_temp = array_values( $init_fields_values );             //FixIn: 7.0.1.32
        $init_fields_values = array_shift( $init_fields_values_temp );

        
        $this->mail_api( WPBC_EMAIL_TRASH_ID, $init_fields_values );
        
        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Actions  -  S e n d   
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name_action = 'wpbc_form_action';                                      // Define form name
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name_action ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name_action );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update_actions();
        }                        
        ?>
        <form  name="<?php echo $submit_form_name_action; ?>" id="<?php echo $submit_form_name_action; ?>" action="" method="post" autocomplete="off">
           <?php 
              // N o n c e   field, and key for checking   S u b m i t 
              wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name_action );
           ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" id="is_form_sbmitted_<?php echo $submit_form_name_action; ?>" value="1" />
             <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
        <?php

        
        ////////////////////////////////////////////////////////////////////////
        //  S u b m i t   Main Form  
        ////////////////////////////////////////////////////////////////////////
        
        $submit_form_name = 'wpbc_emails_template';                             // Define form name
        
        $this->mail_api()->validated_form_id = $submit_form_name;               // Define ID of Form for ability to  validate fields before submit.
        
        if ( isset( $_POST['is_form_sbmitted_'. $submit_form_name ] ) ) {

            // Nonce checking    {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
            $nonce_gen_time = check_admin_referer( 'wpbc_settings_page_' . $submit_form_name );  // Its stop show anything on submiting, if its not refear to the original page

            // Save Changes 
            $this->update();
        }                
        
        
        ////////////////////////////////////////////////////////////////////////
        // JavaScript: Tooltips, Popover, Datepick (js & css) 
        ////////////////////////////////////////////////////////////////////////
        
        echo '<span class="wpdevelop">';
        
        wpbc_js_for_bookings_page();                                        
        
        echo '</span>';

        
        ////////////////////////////////////////////////////////////////////////
        // Content
        ////////////////////////////////////////////////////////////////////////
        ?>         
        <div class="clear" style="margin-bottom:10px;"></div>                        
                
        <span class="metabox-holder">
            
            <form  name="<?php echo $submit_form_name; ?>" id="<?php echo $submit_form_name; ?>" action="" method="post" autocomplete="off">
                <?php 
                   // N o n c e   field, and key for checking   S u b m i t 
                   wp_nonce_field( 'wpbc_settings_page_' . $submit_form_name );
                ?><input type="hidden" name="is_form_sbmitted_<?php echo $submit_form_name; ?>" id="is_form_sbmitted_<?php echo $submit_form_name; ?>" value="1" />


                <div class="clear"></div>    
                <div class="metabox-holder">

                    <div class="wpbc_settings_row wpbc_settings_row_left" >
                    <?php 
                            
                        wpbc_open_meta_box_section( $submit_form_name . 'general', __('Email is sent to visitor after cancelling of booking (moved to trash).', 'booking')   );    //FixIn: 8.1.2.17.1
                            
                            $this->mail_api()->show( 'general' ); 
                            
                        wpbc_close_meta_box_section(); 
                            
                        
                        wpbc_open_meta_box_section( $submit_form_name . 'parts' , __('Header / Footer', 'booking') ); 
                            
                            $this->mail_api()->show( 'parts' );
                        
                        wpbc_close_meta_box_section();
                            
                        
                        wpbc_open_meta_box_section( $submit_form_name . 'style' , __('Email Styles', 'booking') ); 
                            
                            $this->mail_api()->show( 'style' );
                        
                        wpbc_close_meta_box_section();
                        
                    ?>    
                    </div>

                    <div class="wpbc_settings_row wpbc_settings_row_right">
                    <?php 
                    
                        wpbc_open_meta_box_section( $submit_form_name . 'actions', __('Actions', 'booking') ); 

                            ?><a class="button button-secondary" style="margin:0 0 5px;" href="javascript:void(0)" 
                                 onclick="javascript: jQuery('#form_action').val('test_send'); jQuery('form#<?php echo $submit_form_name_action; ?>').trigger( 'submit' );"
                                ><?php _e('Send Test Email', 'booking'); ?></a><?php  
                                
                            ?><input type="submit" value="<?php _e('Save Changes', 'booking'); ?>" class="button button-primary right" style="margin:0 0 5px 5px;" /><?php 
                            
                            /* ?>
                            <a class="button button-secondary" href="javascript:void(0)" ><?php _e('Preview Email', 'booking'); ?></a>
                            <hr />
                            <a  class="button button-secondary right"   
                                href="javascript:void(0)" 
                                onclick="javascript: if ( wpbc_are_you_sure('<?php echo esc_js(__('Do you really want to delete this item?', 'booking')); ?>') ){ 
                                                         jQuery('#form_action').val('delete');
                                                         jQuery('form#<?php echo $submit_form_name_action; ?>').trigger( 'submit' );
                                                     }"
                                ><?php _e('Delete Email', 'booking'); ?></a>
                             <?php */ 
                            
                            ?><div class="clear"></div><?php   
                        
                        wpbc_close_meta_box_section(); 
                        
                        wpbc_open_meta_box_section( $submit_form_name . 'email_content_type', __('Type', 'booking') );
                            
                            $this->mail_api()->show( 'email_content_type' );
                            
                        wpbc_close_meta_box_section(); 
                        
                        
                        wpbc_open_meta_box_section( $submit_form_name . 'help', __('Help', 'booking') );
                            
                            $this->mail_api()->show( 'help' );
                            
                        wpbc_close_meta_box_section(); 
                        
                    ?>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <input type="submit" value="<?php _e('Save Changes', 'booking'); ?>" class="button button-primary" />  
            </form>
        </span>
        <?php
        
        $this->enqueue_js();
    }
    
    
    /**
     * Update form  from Toolbar - create / delete/ load email templates
     * 
     * @return boolean
     */
    public function update_actions(  ) {
    
             
        if ( $_POST['form_action'] == 'test_send' ) {                           // Sending test  email
            
            //$to = sanitize_email( $this->mail_api()->fields_values['to'] );
            $replace = array ();
            $replace['booking_id'] = '99';
            $replace['id'] = '99';
            $replace['dates'] = 'July 5, 2016 10:00 - July 5, 2016 12:00';
            $replace['check_in_date'] = 'July 5, 2016 10:00';
            $replace['check_out_date'] = 'July 5, 2016 12:00';
            $replace['check_out_plus1day'] = 'July 6, 2016 12:00';
            $replace['dates_count'] = '2';
            $replace['cost'] = '250.00';            
            $replace[ 'siteurl' ]       = htmlspecialchars_decode( '<a href="' . home_url() . '">' . home_url() . '</a>' );
            $replace[ 'remote_ip'     ] = wpbc_get_user_ip();   //FixIn:7.1.2.4                      // The IP address from which the user is viewing the current page. 
            $replace[ 'user_agent'    ] = $_SERVER['HTTP_USER_AGENT'];                  // Contents of the User-Agent: header from the current request, if there is one. 
            $replace[ 'request_url'   ] = ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );                     // The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
            $replace[ 'current_date' ]  = date_i18n( get_bk_option( 'booking_date_format' ) );
            $replace[ 'current_time' ]  = date_i18n( get_bk_option( 'booking_time_format' ) );                                                    
            $replace['resource_title'] = 'Standard';
            $replace['bookingtype'] = 'Standard';
            $replace['rangetime'] = '10:00 - 12:00';
            $replace['name'] = 'John';
            $replace['secondname'] = 'Smith';
            $replace['email'] = 'smith@your-server.com';
            $replace['phone'] = '57835873656';
            $replace['address'] = 'Baker stt, 10';
            $replace['city'] = 'London';
            $replace['postcode'] = '243';
            $replace['country'] = 'GB';
            $replace['visitors'] = '1';
            $replace['children'] = '1';
            $replace['details'] = 'Test data';
            $replace['term_and_condition'] = 'I Accept term and conditions';
            $replace['booking_resource_id'] = '1';
            $replace['resource_id'] = '1';
            $replace['type_id'] = '1';
            $replace['type'] = '1';
            $replace['resource'] = '1';
            $replace['content'] = '<h2>Test Email</h2><div class="payment-content-form"> <strong>Times</strong>:<span class="fieldvalue">10:00 - 12:00</span><br/> <strong>First Name</strong>:<span class="fieldvalue">John</span><br/> <strong>Last Name</strong>:<span class="fieldvalue">Smith</span><br/> <strong>Email</strong>:<span class="fieldvalue">smith@your-server.com</span><br/> <strong>Phone</strong>:<span class="fieldvalue">57835873656</span><br/> <strong>Address</strong>:<span class="fieldvalue">Baker stt, 10</span><br/> <strong>City</strong>:<span class="fieldvalue">London</span><br/> <strong>Post code</strong>:<span class="fieldvalue">243</span><br/> <strong>Country</strong>:<span class="fieldvalue">GB</span><br/> <strong>Adults</strong>:<span class="fieldvalue"> 1</span><br/> <strong>Children</strong>:<span class="fieldvalue"> 1</span><br/> <strong>Details</strong>:<br /><span class="fieldvalue"> Test data.</span> </div>';
            $replace['moderatelink'] = 'http://your-server.com/';
            $replace['visitorbookingediturl'] = 'http://your-server.com/';
            $replace['visitorbookingslisting'] = 'http://your-server.com/';	//FixIn: 8.1.3.5.1
            $replace['visitorbookingcancelurl'] = 'your-server.com/';
            $replace['visitorbookingpayurl'] = 'your-server.com/';
            $replace['bookinghash'] = '17854sdgfdsgdff3044786da105fe5650b29';                   
            
            $to = $this->mail_api()->get_from__email_address();
            $to_name = $this->mail_api()->get_from__name();
            $to = trim(  $to_name ) . ' <' .  $to . '> ';
        
        
            $email_result = $this->mail_api()->send( $to , $replace );

            if ( $email_result ) 
                wpbc_show_message ( __('Email sent to ', 'booking') . $this->mail_api()->get_from__email_address() , 5 );             
            else 
                wpbc_show_message ( __('Email was not sent. An error occurred.', 'booking'), 5 ,'error' );    
        }
    }
    
    
    /** Update Email template to DB */
    public function update() {

        // Get Validated Email fields
        $validated_fields = $this->mail_api()->validate_post();
        
//debuge($validated_fields);        
        
        $this->mail_api()->save_to_db( $validated_fields );
                
        wpbc_show_message ( __('Settings saved.', 'booking'), 5 );              // Show Save message
    }

    // <editor-fold     defaultstate="collapsed"                        desc=" CSS & JS  "  >
    
    /** CSS for this page */
    private function css() {
        ?>
        <style type="text/css">  
            .wpbc-help-message {
                border:none;
                margin:0 !important;
                padding:0 !important;
            }
            
            @media (max-width: 399px) {
            }
        </style>
        <?php
    }
    

    /**
	 * Add Custon JavaScript - for some specific settings options
     *      Executed After post content, after initial definition of settings,  and possible definition after POST request.
     * 
     * @param type $menu_slug
     * 
     */
    private function enqueue_js(){                                               // $page_tag, $active_page_tab, $active_page_subtab ) {

        
//debuge( $this, $_GET, $page_tag, $active_page_tab, $active_page_subtab);        
        // Check if this correct  page /////////////////////////////////////////////

//        if ( !(
//                   ( $page_tag == 'wpbc-settings')                              // Load only at 'wpbc-settings' menu
//                && ( $_GET['tab'] == 'email' )                                  // At ''general' tab
//                && ( $_GET['subtab'] == 'new-visitor' )                         
//              )
//          ) return;

        // JavaScript //////////////////////////////////////////////////////////////
        
        $js_script = '';
        //Show or hide colors section  in settings page depend form  selected email  template.
        $js_script .= " jQuery('select[name=\"trash_template_file\"]').on( 'change', function(){    
                                if ( jQuery('select[name=\"trash_template_file\"] option:selected').val() == 'plain' ) {   
                                    jQuery('.template_colors').hide();                                    
                                } else {
                                    jQuery('.template_colors').show();                                    
                                }
                            } ); ";    
        $js_script .= "\n";                                                     //New Line
        $js_script .= " if ( jQuery('select[name=\"trash_template_file\"] option:selected').val() == 'plain' ) {   
                            jQuery('.template_colors').hide();                                    
                        } ";    
        
        // Show Warning messages if Title (optional) is empty - title of email  will be "WordPress
        $js_script .= " jQuery(document).ready(function(){ ";
        $js_script .= "     if (  jQuery('#trash_to_name').val() == ''  ) {";
        $js_script .= "         jQuery('#trash_to_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'booking' ))."</div>')";
        $js_script .= "     }";
        $js_script .= "     if (  jQuery('#trash_from_name').val() == ''  ) {";
        $js_script .= "         jQuery('#trash_from_name').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('If empty then title defined as WordPress', 'booking' ))."</div>')";
        $js_script .= "     }";
        $js_script .= "  }); ";
          // Show Warning messages if "From" Email DNS different from current website DNS
        $js_script .= " jQuery(document).ready(function(){ ";
        
        $js_script .= "     var wpbc_email_from = jQuery('#trash_from').val();";    // from@wpbookingcalendar.com 
        $js_script .= "     wpbc_email_from = wpbc_email_from.split('@');";             // ['from', 'wpbookingcalendar.com']
        $js_script .= "     wpbc_email_from.shift();";                                  // ['wpbookingcalendar.com']
        $js_script .= "     wpbc_email_from = wpbc_email_from.join('');";              // 'wpbookingcalendar.com'        

        $js_script .= "     var wpbc_website_dns = jQuery(location).attr('hostname');"; 								// www.server.com
		//FixIn: 8.0.2.9
        $js_script .= "     var wpbc_website_dns_sub = jQuery(location).attr('hostname').split('.');"; 					// ['www', 'server', 'com']
        $js_script .= "     wpbc_website_dns_sub.shift();";                                  							// ['server', 'com']
        $js_script .= "     wpbc_website_dns_sub = wpbc_website_dns_sub.join('.');";              						// 'server.com'
        $js_script .= "     if ( ( wpbc_email_from != wpbc_website_dns_sub ) && ( wpbc_email_from != wpbc_website_dns ) ){";
        $js_script .= "         jQuery('#trash_from').parent().append('<div class=\'updated\' style=\'border-left-color:#ffb900;padding:5px 10px;\'>". esc_js(__('Email different from website DNS, its can be a reason of not delivery emails. Please use the email withing the same domain as your website!', 'booking' ))."</div>')";
        $js_script .= "     }";

        $js_script .= "  }); ";
        
        
        //FixIn: 8.1.2.17
		$js_script .= " jQuery('#trash_copy_to_admin').on( 'change', function(){ ";
        $js_script .= "     if ( jQuery('#trash_copy_to_admin').is(':checked') ) {";
        $js_script .= "         jQuery('.wpbc_tr_copy_to_admin_to').show();";
        $js_script .= "     } else { ";
        $js_script .= "         jQuery('.wpbc_tr_copy_to_admin_to').hide();";
        $js_script .= "     }  ";
		$js_script .= "  }); ";
		$js_script .= " jQuery(document).ready(function(){ ";
        $js_script .= "     if ( jQuery('#trash_copy_to_admin').is(':checked') ) {";
        $js_script .= "         jQuery('.wpbc_tr_copy_to_admin_to').show();";
        $js_script .= "     } else { ";
        $js_script .= "         jQuery('.wpbc_tr_copy_to_admin_to').hide();";
        $js_script .= "     }  ";
		$js_script .= "  }); ";
		//FixIn: 8.1.2.17 - END

        // Eneque JS to  the footer of the page
        wpbc_enqueue_js( $js_script );                
    }

    
    // </editor-fold>    
}
add_action('wpbc_menu_created',  array( new WPBC_Settings_Page_Email_Trash() , '__construct') );    // Executed after creation of Menu



// <editor-fold     defaultstate="collapsed"                        desc=" Emails Sending"  >

/**
	 * Get ShortCodes to Replace
 * 
 * @param int $booking_id - ID of booking 
 * @param int $bktype     - booking resource type 
 * @param string $formdata   - booking form data content
 */
function wpbc__get_replace_shortcodes__email_trash( $booking_id, $bktype, $formdata ) { 
    
    $replace = array();   

    // Resources /////////////////////////////////////////////////////////////// 
    $bk_title = '';
    if ( function_exists( 'get_booking_title' ) )
        $bk_title = get_booking_title( $bktype );

    // Dates ///////////////////////////////////////////////////////////////////
    $my_dates4emeil = wpbc_get_str_sql_dates_in_booking( $booking_id );
    if ( get_bk_option( 'booking_date_view_type' ) == 'short' )
        $my_dates_4_send = wpbc_get_dates_short_format( $my_dates4emeil );
    else
        $my_dates_4_send = wpbc_change_dates_format( $my_dates4emeil );
    
    $my_dates4emeil_check_in_out = explode(',', $my_dates4emeil );

    $my_check_in_date  = wpbc_change_dates_format( $my_dates4emeil_check_in_out[0] );
    $my_check_out_date = wpbc_change_dates_format( $my_dates4emeil_check_in_out[ count( $my_dates4emeil_check_in_out ) - 1 ] );
    //FixIn: 8.7.2.5
    $my_check_in_onlydate	= wpbc_change_dates_format( date_i18n( 'Y-m-d 00:00:00', strtotime( $my_dates4emeil_check_in_out[0] ) ) );
    $my_check_out_onlydate  = wpbc_change_dates_format( date_i18n( 'Y-m-d 00:00:00', strtotime( $my_dates4emeil_check_in_out[ count( $my_dates4emeil_check_in_out ) - 1 ] ) ) );

    $my_check_out_plus1day = wpbc_change_dates_format( date_i18n( 'Y-m-d H:i:s', strtotime( $my_dates4emeil_check_in_out[ count( $my_dates4emeil_check_in_out ) - 1 ] . " +1 day" ) ) ); //FixIn: 6.0.1.11

    // Cost ////////////////////////////////////////////////////////////////////
    $booking_cost = apply_bk_filter( 'get_booking_cost_from_db', '', $booking_id );    
    $booking_cost = wpbc_get_cost_with_currency_for_user( $booking_cost );
    
    // Other ///////////////////////////////////////////////////////////////////
    $replace[ 'booking_id' ]    = $booking_id;
    $replace[ 'id' ]            = $replace[ 'booking_id' ];
    $replace[ 'dates' ]         = $my_dates_4_send;
    $replace[ 'check_in_date' ] = $my_check_in_date;
    $replace[ 'check_out_date' ]    = $my_check_out_date;
    //FixIn: 8.7.2.5
    $replace[ 'check_in_only_date' ] 	= $my_check_in_onlydate;
    $replace[ 'check_out_only_date' ]   = $my_check_out_onlydate;

    $replace[ 'check_out_plus1day'] = $my_check_out_plus1day;                   //FixIn: 6.0.1.11
    $replace[ 'dates_count' ]   = count( $my_dates4emeil_check_in_out );
    $replace[ 'cost' ]          = $booking_cost;
    $replace[ 'siteurl' ]       = htmlspecialchars_decode( '<a href="' . home_url() . '">' . home_url() . '</a>' );
    $replace[ 'resource_title'] = apply_bk_filter( 'wpdev_check_for_active_language', $bk_title );
    $replace[ 'bookingtype' ]   = $replace[ 'resource_title'];
    $replace[ 'remote_ip'     ] = wpbc_get_user_ip();   //FixIn:7.1.2.4                      // The IP address from which the user is viewing the current page. 
    $replace[ 'user_agent'    ] = $_SERVER['HTTP_USER_AGENT'];                  // Contents of the User-Agent: header from the current request, if there is one. 
    $replace[ 'request_url'   ] = ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );                     // The address of the page (if any) where action was occured. Because we are sending it in Ajax request, we need to use the REFERER HTTP
    $replace[ 'current_date' ]  = date_i18n( get_bk_option( 'booking_date_format' ) );
    $replace[ 'current_time' ]  = date_i18n( get_bk_option( 'booking_time_format' ) );                                                    
    
    // Form Fields /////////////////////////////////////////////////////////////
    $booking_form_show_array = get_form_content( $formdata, $bktype, '', $replace );    // We use here $replace array,  becaise in "Content of booking filds data" form  can  be shortcodes from above definition
                    
    foreach ( $booking_form_show_array['_all_fields_'] as $shortcode_name => $shortcode_value ) {
        
        if ( ! isset( $replace[ $shortcode_name ] ) )
            $replace[ $shortcode_name ] = $shortcode_value;
    }
    $replace[ 'content' ]       = $booking_form_show_array['content'];

    // Links ///////////////////////////////////////////////////////////////////
    $replace[ 'moderatelink' ]  = htmlspecialchars_decode( 
                                                        //    '<a href="' . 
                                                            esc_url( wpbc_get_bookings_url() . '&view_mode=vm_listing&tab=actions&wh_booking_id=' . $booking_id ) 
                                                        //    . '">' . __('here', 'booking') . '</a>'  
                                                        );    
    $replace[ 'visitorbookingediturl' ]     = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingediturl]', $booking_id );
    $replace[ 'visitorbookingslisting' ]     = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingslisting]', $booking_id );	//FixIn: 8.1.3.5.1
    $replace[ 'visitorbookingcancelurl' ]   = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingcancelurl]', $booking_id );
    $replace[ 'visitorbookingpayurl' ]      = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[visitorbookingpayurl]', $booking_id );
    $replace[ 'bookinghash' ]               = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '[bookinghash]', $booking_id );

    ////////////////////////////////////////////////////////////////////////////
	// Get additional  replace paramaters to the email shortcodes
	$replace = apply_filters( 'wpbc_replace_params_for_booking', $replace, $booking_id, $bktype, $formdata );			//FixIn: 8.0.1.7


    return $replace;
    
    
}


/**
	 * Send email
 * 
 * @param type $booking_id - ID of booking
 * @param type $bktype - type
 * @param type $formdata - booking form data
 */
                                
function wpbc_send_email_trash( $trash_id_str, $is_send_emeils, $trashreason = '' ) {
    
    global $wpdb;
    $sql = "SELECT * FROM {$wpdb->prefix}booking as bk WHERE bk.booking_id IN ({$trash_id_str})";
    $result = $wpdb->get_results( $sql  );
    
    foreach ( $result as $res ) {
    
        $booking_id = $res->booking_id;
        $bktype = $res->booking_type;
        $formdata = $res->form;
        
        $previous_active_user = apply_bk_filter( 'wpbc_mu_set_environment_for_owner_of_resource', -1, $bktype );    // MU
                
        ////////////////////////////////////////////////////////////////////////
        // Load Data 
        ////////////////////////////////////////////////////////////////////////

        /* Check if New Email Template   Exist or NOT
         * Exist     -  return  empty array in format: array( OPTION_NAME => array() ) 
         *              Its will  load DATA from DB,  during creattion mail_api CLASS
         *              during initial activation  of the API  its try  to get option  from DB
         *              We need to define this API before checking POST, to know all available fields
         *              Define Email Name & define field values from DB, if not exist, then default values. 
         * Not Exist -  import Old Data from DB
         *              or get "default" data from settings and return array with  this data
         *              This data its initial  parameters for definition fields in mail_api CLASS 
         * 
         */

        $init_fields_values = wpbc_import6_email__trash__get_fields_array_for_activation();

        // Get Value of first element - array of default or imported OLD data,  because need only  array  of values without key - name of options for wp_options table
        $init_fields_values_temp = array_values( $init_fields_values );             //FixIn: 7.0.1.32
        $init_fields_values = array_shift( $init_fields_values_temp );


        $mail_api = new WPBC_Emails_API_Trash( WPBC_EMAIL_TRASH_ID, $init_fields_values );

        ////////////////////////////////////////////////////////////////////////////

        if ( $mail_api->fields_values['enabled'] == 'Off' )     return false;       // Email  template deactivated - exit.


        $replace = wpbc__get_replace_shortcodes__email_trash( $booking_id, $bktype, $formdata );
        $replace[ 'denyreason' ] = $trashreason;                                //FixIn: 7.0.1.1
        
        // Replace shortcodes with  custom URL parameter,  like: 'visitorbookingediturl', 'visitorbookingcancelurl', 'visitorbookingpayurl'
        foreach ( array( 'visitorbookingediturl', 'visitorbookingcancelurl', 'visitorbookingpayurl' , 'visitorbookingslisting') as $url_shortcode ) {                     //FixIn: 7.0.1.8            //FixIn: 8.1.3.5.1
            
            // Loop to  search  if we are having several such  shortcodes in our $mail_api->fields_values['content']  (For example,  if we have several  languges ).
            $pos = 0;                                                               //FixIn: 7.0.1.52        
            do {
                $shortcode_params = wpbc_get_params_of_shortcode_in_string( $url_shortcode, $mail_api->fields_values['content'] , $pos );

                if (  ( ! empty( $shortcode_params ) ) && ( isset( $shortcode_params['url'] ) )  ){

                    $pos = $shortcode_params['end'];

                    $exist_replace =  substr( $mail_api->fields_values['content'], $shortcode_params['start'], ( $shortcode_params['end'] - $shortcode_params['start'] ) );

                    $new_replace = $url_shortcode . rand(1000,9000);

                    $mail_api->fields_values['content'] = str_replace( $exist_replace,  $new_replace ,$mail_api->fields_values['content'] );

                    $replace[ $new_replace ] = apply_bk_filter( 'wpdev_booking_set_booking_edit_link_at_email', '['.$exist_replace.']', $booking_id );
                } else if (																								//FixIn: 8.1.1.8
                				   ( ! empty( $shortcode_params ) )
								&& ( isset( $shortcode_params['end'] ) )
								&& ( $shortcode_params['end'] < strlen( $mail_api->fields_values['content'] ) )
						)  {
					$pos = $shortcode_params['end'];
                } else {
                    $shortcode_params = false;                                      //FixIn: 7.0.1.58                           
                } 

            } while ( ! empty( $shortcode_params ) );                               //FixIn: 7.0.1.52
         
        }
        
        $mail_api->set_replace( $replace );
        $mail_api->fields_values['from_name'] = $mail_api->replace_shortcodes( $mail_api->fields_values['from_name'] );                         //FixIn: 7.0.1.29
        
//        // Get To field
//        if ( isset( $replace['email'] ) ) { 
//            $to = $replace['email'];
//        }

        if ( isset( $replace['email'] ) ) {
            $to = wpbc_email_prepand_person_name( $replace['email'] , $replace );

            $to = wpbc_check_for_several_emails_in_form( $to, $formdata, $bktype );     //FixIn: 6.0.1.9   

            $to = str_replace( ';', ',', $to );

            /*
            if ( (  strpos( $mail_api->fields_values['to'], ',') === false ) && (  strpos( $mail_api->fields_values['to'], ';') === false ) ) {

                $valid_email = sanitize_email( $mail_api->fields_values['to'] );
                if ( ! empty( $valid_email ) )
                    $to = trim( wp_specialchars_decode( esc_html( stripslashes( $mail_api->fields_values['to_name'] ) ), ENT_QUOTES ) ) 
                          . ' <' .  $valid_email . '> ';
            } else {

                // Comma separated several  emails - validate  all  these emails in Email API class
                $to_array = $mail_api->fields_values['to']; 

                $to_array = str_replace(';', ',', $to_array);
                if ( !is_array( $to_array ) ) $to_array = explode( ',', $to_array );

                $to_name = str_replace(';', ',', $mail_api->fields_values['to_name']);
                if ( !is_array( $to_name ) ) $to_name = explode( ',', $to_name );

                $to = array();
                foreach ( $to_array as $to_ind => $to_email) {

                    $to_name_str = $to_name[ ( count( $to_name ) - 1 ) ];

                    if ( isset( $to_name[ $to_ind ] ) )
                        $to_name_str = $to_name[ $to_ind ];

                    $valid_email = sanitize_email( $to_email );
                    if ( ! empty( $valid_email ) )
                        $to[] = trim( wp_specialchars_decode( esc_html( stripslashes( $to_name_str ) ), ENT_QUOTES ) ) . ' <' . $valid_email  . '> ';            
                }
                $to = implode(',', $to);

            }*/


            if (  ( strpos( $to ,'@blank.com' ) === false ) && ( strpos( $replace[ 'content' ],'admin@blank.com') === false )  ) {

                $email_result = $mail_api->send( $to , $replace );
            }
        }
        make_bk_action( 'wpbc_mu_set_environment_for_user', $previous_active_user );     // MU
        

    //debuge( (int) $email_result, $to , $replace);
    } 
    if ( isset($email_result ) )
        return $email_result;    
    else 
        return false;
}

// </editor-fold>
