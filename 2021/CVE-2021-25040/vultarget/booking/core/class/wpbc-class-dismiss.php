<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//FixIn: 8.1.3.10

class WPBC_Dismiss {
    
    public  $element_id;
    public  $title;
    public  $hint;
    public  $html_class;
    public  $css;

    public function __construct( ) {
        
    }
    
    public function render( $params = array() ){
        if (isset($params['id'])) 
                $this->element_id = $params['id'];
        else    return  false;                                                  // Exit, because we do not have ID of element
        
        if (isset($params['title'])) 
                $this->title = $params['title'];
        else    $this->title = __( 'Dismiss'  ,'booking');
        
        if (isset($params['hint']))
                $this->hint = $params['hint'];
        else    $this->hint = __( 'Dismiss'  ,'booking');

        if (isset($params['class']))
                $this->html_class = $params['class'];
        else    $this->html_class = 'wpbc-panel-dismiss';
        
        if (isset($params['css']))
                $this->css = $params['css'];
        else    $this->css = 'text-decoration: none;font-weight: 600;';

        $this->show();
        return true;
    }

    public function show() {

	    // Check if this window is already Hided or not
		if ( '1' == get_user_option( 'booking_win_' . $this->element_id ) ){     // Panel Hided

			?><script type="text/javascript"> jQuery( '#<?php echo $this->element_id; ?>' ).hide(); </script><?php

			return false;

		} else {                                                                  // Show Panel
            ?><script type="text/javascript"> jQuery('#<?php echo $this->element_id; ?>').show(); </script><?php
        }

        wp_nonce_field('wpbc_ajax_admin_nonce',  "wpbc_admin_panel_dismiss_window_nonce" ,  true , true );
        // Show Hide link
        ?><a class="<?php echo $this->html_class; ?>"  style="<?php echo $this->css; ?>"
			 title="<?php echo esc_js( $this->hint ); ?>"
			 href="javascript:void(0)"
             onclick="javascript: if ( typeof( wpbc_hide_window ) == 'function' ) {
				 wpbc_hide_window('<?php echo $this->element_id; ?>');
				 wpbc_dismiss_window(<?php echo get_bk_current_user_id(); ?>, '<?php echo $this->element_id; ?>');
				 } else {  jQuery('#<?php echo $this->element_id; ?>').fadeOut(1000); }"
          ><?php echo esc_js( $this->title ); ?></a><?php
    }
}

global $wpbc_Dismiss;
$wpbc_Dismiss = new WPBC_Dismiss();
