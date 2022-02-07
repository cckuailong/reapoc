<?php
/**
 * GLOBAL SETTINGS ( OPTIONS PAGE )
 * ----------------------------------------------------------------------------
 */
 
 // Add settings link on plugin page
add_filter( 'plugin_action_links_' . RVM_PLUGIN_FILE , 'rvm_settings_link' ) ;
function rvm_settings_link( $links ) {
     
  $settings_link = '<a href="options-general.php?page=rvm_options_page.php">' . __( 'Settings', RVM_TEXT_DOMAIN ) . '</a>' ; 
  array_unshift( $links, $settings_link ); 
  return $links; 
  
}

// Add a menu for our option page
add_action( 'admin_menu', 'rvm_options_add_page' ) ;
function rvm_options_add_page() {
    
    add_options_page( 
        __( 'RVM settings', RVM_TEXT_DOMAIN ), // Page title on browser bar 
        __( 'RVM global settings', RVM_TEXT_DOMAIN ), // menu item text
        'manage_options', // only administartors can open this
        'rvm_options_page', // unique name of settings page
        'rvm_options_form' //call to fucntion which creates the form
     );
     
}

// Register and define the settings
add_action( 'admin_init', 'rvm_admin_init' ) ;
function rvm_admin_init(){
    
    register_setting(
        'rvm_settings'
        ,'rvm_options'
        ,''
        //,'rvm_validate_options' non need of validation at the moment
    ) ;
    
    add_settings_section(//Main settings 
        'rvm_main_settings', //id
        __( 'Main settings', RVM_TEXT_DOMAIN ), //title
        'rvm_section_main', //callback
        'rvm_options_page' //page
    ) ;
    
    add_settings_field(            
            'rvm_option_dequeue_wp_emoji', //id
            __('Disable wp_emoji', RVM_TEXT_DOMAIN ), //title
            'rvm_settings_field', //callback
            'rvm_options_page',//page
            'rvm_main_settings',//section
            array( // The $args
            	'rvm_option_dequeue_wp_emoji',// Should match Option ID
            	'checkbox' 
        	) 
    ) ;
    
    /*add_settings_field(            
            'rvm_option_custom_marker_icon_module_path', //id
            __('Install module for custom marker icon', RVM_TEXT_DOMAIN ), //title
            'rvm_settings_field', //callback
            'rvm_options_page',//page
            'rvm_main_settings',//section
            array( // The $args
            	'rvm_option_custom_marker_icon_module_path',// Should match Option ID
            	'text' 
        	)
    ) ;*/
        
}

// Add forms to options page
function rvm_section_main() {

}

// Add fields to options page
function rvm_settings_field( $args ) {
    
    $output = '';
    // Retrieve options
    $rvm_options = rvm_retrieve_options();  
    
    if ( $args[ 1 ] == 'checkbox' ) {
    	$rvm_option_dequeue_wp_emoji =  !empty( $rvm_options[ $args[ 0 ] ] ) ? 'checked="checked"' : '' ;    
    	$output .= '<input  ' .  $rvm_option_dequeue_wp_emoji  . ' type="' . $args[ 1 ] . '" name="rvm_options['.$args[ 0 ].']" id="'.$args[ 0 ].'" /><span>' . __('In case you may notice issues related to wp_emoji enable following checkbox. It\'s well documented this script has problems with svg ( vector images ) on which RVM relies on.', RVM_TEXT_DOMAIN ) . '</span>';
    }

    /*else {
    	$rvm_option_custom_marker_icon =  !empty( $rvm_options[ $args[ 0 ] ] ) ? $rvm_options[ $args[ 0 ] ] : '' ;
    	$output .= '<input type="' . $args[ 1 ] . '" name="' . $args[ 0 ] . '" id="'.$args[ 0 ].'" class="rvm_input" value="" /><input id="rvm_custom_marker_icon_module_uploader_button" class="rvm_custom_marker_icon_module_uploader_button rvm_media_uploader button-primary" name="rvm_mbe_custom_marker_icon_uploader_button" value="' . __( 'Select Marker Module', RVM_TEXT_DOMAIN ) . '" type="submit"> <input id="rvm_custom_marker_icon_module_unzipper_button" class="rvm_custom_marker_icon_module_unzipper_button button-primary" name="rvm_mbe_custom_marker_icon_module_unzipper_button" value="' . __( 'Install Marker Module', RVM_TEXT_DOMAIN ) . '" type="submit">';

        if ( !empty( $rvm_options[ 'rvm_custom_icon_marker_module_path_verified' ] ) && ( $rvm_options['rvm_custom_icon_marker_module_path_verified'] != 'default' ) && isset( $rvm_options[ 'rvm_custom_icon_marker_module_path_verified' ] ) ) {
                $output .= '<p id="rvm_marker_global_settings_message" class="rvm_messages rvm_success_messages">' . __( 'Marker Module installed', RVM_TEXT_DOMAIN ) . '</p>';
                $rvm_custom_icon_marker_module_path_verified_value = $rvm_options[ 'rvm_custom_icon_marker_module_path_verified' ];
        }

        else {
            $rvm_custom_icon_marker_module_path_verified_value = '';
        }

        //Hidden field to save eventual marker module path in DB. All options needs to be saved always, otherwise they will be overwritten
        $output .= '<div id="rvm_custom_marker_icon_module_unzip_progress"><input type="hidden" value="' . $rvm_custom_icon_marker_module_path_verified_value . '"  name="rvm_options[rvm_custom_icon_marker_module_path_verified]" id="rvm_custom_icon_marker_module_path_verified" /></div>';
    }*/
    
    // All options need to be declared here, otherwise WP will get rid in DB of non declared value 
    $rvm_version = !empty( $rvm_options[ 'ver' ] ) ? $rvm_options[ 'ver' ] : RVM_VERSION ;
    $output .= '<input type="hidden" value="' . $rvm_version . '" id="rvm_version" name="rvm_options[ver]"/>';
    
    echo $output;
}

// Add forms to options page
function rvm_options_form() {
 
    ?>
    
    <div class="wrap">
        <h2><?php _e('RVM global settings', RVM_TEXT_DOMAIN );?></h2>
                        <form action="options.php" method="post" id="rvm_options_form">
                    <?php settings_fields('rvm_settings'); ?>
                     <?php do_settings_sections('rvm_options_page'); ?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', RVM_TEXT_DOMAIN ); ?>" />
                </p>
                </form>
    </div>
    
<?php 

} 

?>