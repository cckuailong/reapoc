<?php

class SwpmUtilsTemplate {

    /*
     * This function will load the template file in the following order
     * wp-content/themes/your-child-theme/simple-membership/template-name.php
     * wp-content/themes/your-main-theme/simple-membership/template-name.php
     * The standard plugin's template file
     */
    public static function swpm_load_template($template_name, $require_once = true) {
        
        //List of file paths (in order of priority) where the plugin should check for the template.
        $template_files = array(
            get_stylesheet_directory() . '/' . SIMPLE_WP_MEMBERSHIP_TEMPLATE_PATH . '/' . $template_name, //First check inside child theme (if you are using a child theme)
            get_template_directory() . '/' . SIMPLE_WP_MEMBERSHIP_TEMPLATE_PATH . '/' . $template_name, //Then check inside the main theme folder
            SIMPLE_WP_MEMBERSHIP_PATH . 'views/' . $template_name //Otherwise load the standard template
        );

        //Filter hook to allow overriding of the template file path
        $template_files = apply_filters( 'swpm_load_template_files', $template_files, $template_name);

        foreach ($template_files as $file) {
            if (file_exists($file)) {
                $template_to_load = $file;
                break;
            }
        }

        //Lets load this template
        if ($template_to_load) {
            if ($require_once) {
                require_once( $template_to_load );
            } else {
                require( $template_to_load );
            }
        } else {
            wp_die(SwpmUtils::_('Error! Failed to find a template path for the specified template: ' . $template_name));
        }
    }

}
