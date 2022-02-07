<?php
/**
 * Ajax calls
 * ----------------------------------------------------------------------------
 */
/* Map Preview */
add_action( 'wp_ajax_rvm_preview', 'rvm_ajax_preview' );
function rvm_ajax_preview( ) {
                if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'map' ] ) && $_REQUEST[ 'map' ] != 'select_country' ) {
                                // Verify that the incoming request is coming with the security nonce
                                if ( wp_verify_nonce( $_REQUEST[ 'nonce' ], 'rvm_ajax_nonce' ) ) {
                                                //inject html and javascript to create teh map preview
                                                $array_countries = rvm_countries_array();
                                                foreach ( $array_countries as $country_field ) {
                                                                if ( $_REQUEST[ 'map' ] == $country_field[ 0 ] ) {
                                                                                $js_map_id  = $country_field[ 3 ];
                                                                                $js_vectormap = $country_field[ 2 ];
                                                                                $map_group    = $country_field[ 5 ];
                                                                                $js_map_path  = $country_field[ 7 ];
                                                                } //$_REQUEST[ 'map' ] == $country_field[ 0 ]
                                                } // foreach( $array_countries as $country_field )*/
                                                $map_zoom               = empty( $_REQUEST[ 'zoom' ] ) ? 'false' : 'true';
                                                $map_bg_selected_status = empty( $_REQUEST[ 'subdivisionselectedstatus' ] ) ? 'false' : 'true';
                                                // If custom map load javascript from upload map subdir 
                                                if ( $map_group === 'custom_maps' && $js_map_path ) {
                                                                $rvm_custom_map_url = $js_map_path;
                                                                $output = '<script type="text/javascript" src="' . $rvm_custom_map_url . '/jquery-jvectormap-' . $js_map_id . '.js"></script>';
                                                } //$map_group === 'custom_maps' && $js_map_path
                                                else {
                                                                $output = '<script type="text/javascript" src="' . RVM_JS_JVECTORMAP_PLUGIN_DIR . '/jquery-jvectormap-' . $js_map_id . '.js"></script>';
                                                }
                                                $map_name = $_REQUEST[ 'map' ];
                                                $map_transparent_canvas = !empty( $_REQUEST[ 'transparentcanvas' ] ) ? true : false;
                                                $map_canvas_color = !empty( $_REQUEST[ 'canvascolor' ] ) ? $_REQUEST[ 'canvascolor' ] : RVM_CANVAS_BG_COLOUR; //default setting fallback
                                                if ( $map_transparent_canvas ) {
                                                                $map_canvas_color = 'transparent';
                                                } //$map_transparent_canvas
                                                $map_bg_color = !empty( $_REQUEST[ 'bgcolor' ] ) ? $_REQUEST[ 'bgcolor' ] : RVM_MAP_BG_COLOUR;
                                                $map_bg_selected_color = !empty( $_REQUEST[ 'bgselectedcolor' ] ) ? $_REQUEST[ 'bgselectedcolor' ] : RVM_MAP_BG_SELECTED_COLOUR;
                                                $map_border_color = !empty( $_REQUEST[ 'bordercolor' ] ) ? $_REQUEST[ 'bordercolor' ] : RVM_MAP_BORDER_COLOUR;
                                                $map_border_width = !empty( $_REQUEST[ 'borderwidth' ] ) ? $_REQUEST[ 'borderwidth' ] : RVM_MAP_BORDER_WIDTH;
                                                $map_width = !empty( $_REQUEST[ 'width' ] ) ? 'style="width: ' . $_REQUEST[ 'width' ] . ';"' : '';
                                                $map_padding =  !empty( $_REQUEST[ 'padding' ] ) ? $_REQUEST[ 'padding' ] : '';
                                                // Get padding of the map
                                                if( $map_padding ) {
                                                                $output .= '<style>';
                                                                $output .= '#' . $map_name . '-map .jvectormap-container';
                                                                $output .= '{ padding: ' . $map_padding . ' !important; box-sizing: border-box !important}';
                                                                $output .= '</style>';
                                                } 
                                                $output .= '<div class="preview-map-container" id="' . $map_name . '-map" ' . $map_width . '></div>';
                                                $output .= '<script>';
                                                $output .= '(function($) { $(function(){';
                                                $output .= '$("#' . $map_name . '-map").vectorMap({ map: "' . $js_map_id . '",';
                                                $output .= 'regionsSelectable: ' . $map_bg_selected_status . ',';
                                                $output .= 'regionStyle: { initial: { fill: "' . $map_bg_color . '", "fill-opacity": 1, stroke: "' . $map_border_color . '", "stroke-width": ' . $map_border_width . ' }, 
                                    selected: { fill: "' . $map_bg_selected_color . '" }},
                                    backgroundColor: "' . $map_canvas_color . '",';
                                                $output .= 'zoomButtons: ' . $map_zoom . ', zoomOnScroll: false });';
                                                $output .= '});})(jQuery);</script>';
                                                echo $output;
                                                die( );
                                } //wp_verify_nonce( $_REQUEST[ 'nonce' ], 'rvm_ajax_nonce' )
                                else {
                                                die( __( 'There was an issue with the preview generation tool', RVM_TEXT_DOMAIN ) );
                                }
                } //isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'map' ] ) && $_REQUEST[ 'map' ] != 'select_country'
                else {
                                die( __( 'Choose a valid map from the drop down menu', RVM_TEXT_DOMAIN ) );
                }
} // add_action( 'wp_ajax_rvm_preview', 'rvm_ajax_preview' );
/* Custom Maps */
add_action( 'wp_ajax_rvm_custom_map', 'rvm_ajax_custom_map' );
function rvm_ajax_custom_map( $post_id ) {
                // check if custom_map value is sent
                if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'map' ] ) && $_REQUEST[ 'map' ] = 'rvm_custom_map' ) {
                                if ( function_exists( 'unzip_file' ) ) {
                                                $output = '';
                                                $custom_map_filename_ext = '.zip';
                                                $custom_map_separator = '_';
                                                //Get uploaded map path getting rid of any spaces
                                                $custom_map_filename = trim( $_REQUEST[ 'custom_map_filename' ] );
                                                //check if filename has the .zip extension or not: this is not intended for file extension checking
                                                if ( rvm_retrieve_custom_map_ext( $custom_map_filename, $custom_map_filename_ext ) != $custom_map_filename_ext ) {
                                                                // so basically if user copied and pasted map name without the .zip extension, this is the right moment to add it :-)
                                                                $custom_map_filename = $custom_map_filename . $custom_map_filename_ext;
                                                } //rvm_retrieve_custom_map_ext( $custom_map_filename, $custom_map_filename_ext ) != $custom_map_filename_ext
                                                // Access the WP filesystem and upload dir
                                                WP_Filesystem();
                                                $destination = wp_upload_dir();
                                                $destination_dir_path = $destination[ 'path' ];
                                                $destination_url = $destination[ 'url' ];
                                                $destination_basedir_path = $destination[ 'basedir' ]; //i.e /Applications/MAMP/htdocs/wordpress4.3/wp-content/uploads
                                                $destination_baseurl_path = $destination[ 'baseurl' ]; // i.e http://localhost:8888/wordpress4.3/wp-content/uploads
                                                $destination_relative_uploads_path = _wp_relative_upload_path( $destination_dir_path ); // i.e 2015/10
                                                //Get list of files and directories inside WP uploads
                                                if ( is_dir( $destination_dir_path ) ) {
                                                                $rvm_upload_list = scandir( $destination_dir_path );
                                                                foreach ( $rvm_upload_list as $rvm_upload_entry ) { 
                                                                                //Check if file already exists in uploads directory
                                                                                if ( $rvm_upload_entry == rvm_retrieve_custom_map_raw_name( $custom_map_filename ) ) {
                                                                                                $rvm_map_is_in_uploads_already = true;
                                                                                                break;
                                                                                } //$rvm_upload_entry == rvm_retrieve_custom_map_raw_name( $custom_map_filename )
                                                                } //$rvm_upload_list as $rvm_upload_entry
                                                                //If already in directory
                                                                if ( isset( $rvm_map_is_in_uploads_already ) && $rvm_map_is_in_uploads_already ) {
                                                                                $old_map_dir_content = scandir( $destination_dir_path . '/' . $rvm_upload_entry );
                                                                                foreach ( $old_map_dir_content as $old_map_dir_content_single_file ) {
                                                                                                if ( $old_map_dir_content_single_file != '.' && $old_map_dir_content_single_file != '..' ) {
                                                                                                                // Clean directory content
                                                                                                                unlink( $destination_dir_path . '/' . $rvm_upload_entry . '/' . $old_map_dir_content_single_file );
                                                                                                } //$old_map_dir_content_single_file != '.' && $old_map_dir_content_single_file != '..'
                                                                                } //$old_map_dir_content as $old_map_dir_content_single_file
                                                                                // Delete old directory
                                                                                rmdir( $destination_dir_path . '/' . $rvm_upload_entry );
                                                                                //Unzip file content                                                 
                                                                                $unzipfile = unzip_file( $destination_dir_path . '/' . $custom_map_filename, $destination_dir_path );
                                                                } //isset( $rvm_map_is_in_uploads_already ) && $rvm_map_is_in_uploads_already
                                                                //Unzip file content
                                                                else {
                                                                                $unzipfile = unzip_file( $destination_dir_path . '/' . $custom_map_filename, $destination_dir_path );
                                                                }
                                                                //Get list of files and directories inside WP uploads again
                                                                $rvm_upload_list = scandir( $destination_dir_path );
                                                                // Now check if .zip file was succesfully unzipped
                                                                foreach ( $rvm_upload_list as $rvm_upload_entry ) {
                                                                                //$output  .=  $rvm_upload_entry . '<br>'; 
                                                                                //Check if the unzipped file matches the filename sent by user without ".zip" extension
                                                                                if ( $rvm_upload_entry != '.' && $rvm_upload_entry != '..' ) {
                                                                                                if ( $rvm_upload_entry == rvm_retrieve_custom_map_raw_name( $custom_map_filename ) ) {
                                                                                                                $rvm_valid_unzip = true;
                                                                                                                break;
                                                                                                } //$rvm_upload_entry == rvm_retrieve_custom_map_raw_name( $custom_map_filename )
                                                                                } //$rvm_upload_entry != '.' && $rvm_upload_entry != '..'
                                                                } //  foreach( $rvm_upload_list as  $rvm_upload_entry )
                                                                if ( $unzipfile && isset( $rvm_valid_unzip ) && $rvm_valid_unzip ) {
                                                                                // Now check if .zip file was succesfully unzipped
                                                                                foreach ( $rvm_upload_list as $rvm_upload_entry ) {
                                                                                                //Check if the zip file is still there, in case "Install your map" is clicked twice or more
                                                                                                if ( $rvm_upload_entry == $custom_map_filename ) {
                                                                                                                //Ok, we do not need you anymore: Destroy the .zip file  just uploaded and let's do a spring clean                                                
                                                                                                                unlink( $destination_dir_path . '/' . $custom_map_filename );
                                                                                                                break;
                                                                                                } //$rvm_upload_entry == rvm_retrieve_custom_map_raw_name( $custom_map_filename )
                                                                                } //  foreach( $rvm_upload_list as  $rvm_upload_entry )                
                                                                                //Get custom maps if exist on DB
                                                                                $rvm_custom_maps_options                                                             = rvm_retrieve_custom_maps_options();
                                                                                //push new value into the arary ( existing or not )
                                                                                $rvm_custom_maps_options[ rvm_retrieve_custom_map_raw_name( $custom_map_filename ) ] = $destination_relative_uploads_path . '/'; //new dynamic path format year/month
                                                                                // Let's save this path into db
                                                                                // we need this options in order to retrieve it inside the style and script register and enqueue functions
                                                                                update_option( 'rvm_custom_maps_options', $rvm_custom_maps_options );
                                                                                //Use following value to enable the publish button ONLY when a map is installed
                                                                                $output .= '<input type="hidden" id="rvm_custom_map_is_installed" name="rvm_custom_map_is_installed" value="1" />';
                                                                                $output .= '<p class="rvm_messages rvm_success_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/green-check4.png" alt="Success" /><span>' . __( 'You have succesfully installed your custom map . Well done! ', RVM_TEXT_DOMAIN ) . __( 'Now you can <strong>Publish</strong> your post.', RVM_TEXT_DOMAIN ) . '</span></p>';
                                                                                //Disable Select and Install your map buttons
                                                                                $output .= '<script>jQuery( "#rvm_custom_map_uploader_button,#unzip_button" ).attr("disabled", "disabled");</script>';
                                                                } //if ( $unzipfile && $rvm_valid_unzip  )
                                                                else {
                                                                                $output .= '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'Damn it... Something went wrong !  Please check if name of the map is correct ( place just map name)  or if you have uploaded the map previous month and try again uploading map now using wordpress media uploader.', RVM_TEXT_DOMAIN ) . '</span></p>';
                                                                }
                                                                
                                                                die( $output );
                                                } //is_dir( $destination_dir_path )
                                                else {
                                                                die( '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'It seems there is no directory where to find your map!', RVM_TEXT_DOMAIN ) . '</span></p>' );
                                                } //if( is_dir( $destination_dir_path ) )
                                } // if(  function_exists( 'unzip_file' )  )
                                else {
                                                die( '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'You have not unzip_file() function available for you WP or you did not provided a valid map name... come on !', RVM_TEXT_DOMAIN ) . '</span></p>' );
                                }
                } // if( isset( $_REQUEST[ 'nonce' ] )
                else {
                                die( __( 'Please select the custom map option from the drop menu ', RVM_TEXT_DOMAIN ) );
                }
} //function rvm_ajax_custom_map
/* Custom Marker Icon Module Installation */
add_action( 'wp_ajax_rvm_custom_marker_icon_module', 'rvm_ajax_custom_marker_icon_module' );
function rvm_ajax_custom_marker_icon_module() {
    $output = '';
        // check if marker module path value is sent together with security nonce
        if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'custom_marker_icon_module_path' ] ) ) {

            $marker_module_raw_name = "rvm_cimm";
            $marker_module_ext = ".zip";
            $marker_file_ext = ".php";
            $marker_module_name = rvm_retrieve_marker_module_name( $_REQUEST[ 'custom_marker_icon_module_path' ] );

            //If user uploading an incorrect file
            if ( isset( $marker_module_name ) || $marker_module_name == $marker_module_raw_name.$marker_module_ext ) {        

                if ( function_exists( 'unzip_file' ) ) {
                    // Access the WP filesystem and upload dir
                    WP_Filesystem();
                    $destination = wp_upload_dir();
                    $destination_dir_path = $destination[ 'path' ];// i.e : /Applications/MAMP/htdocs/wordpress-4.9.4/wp-content/uploads/2018/03
                    $destination_relative_uploads_path = _wp_relative_upload_path( $destination_dir_path ); // i.e 2015/10
                    $rvm_random_number_for_marker_file = mt_rand(100000,999999) . '-' ;

                    //Unzip the marker module
                    $unzipfile = unzip_file( $destination_dir_path . '/' . $marker_module_name, $destination_dir_path );
                    //Check if module was correctly unzipped
                    if( is_dir( $destination_dir_path ) ) {

                        $rvm_marker_module_list = scandir( $destination_dir_path );
                        foreach ( $rvm_marker_module_list as $rvm_upload_module_entry ) { 
                            //Check if the unzipped file matches the correct module filename, i.e. rvm_cimm.php
                            if ( $rvm_upload_module_entry != '.' && $rvm_upload_module_entry != '..' ) {
                                if ( $rvm_upload_module_entry == $marker_module_raw_name . $marker_file_ext ) {
                                                $rvm_marker_module_valid_unzip = true;
                                                break;
                                } //if ( $rvm_upload_module_entry == $marker_module_raw_name.$marker_file_ext )
                            }//if ( $rvm_upload_module_entry != '.' && $rvm_upload_module_entry != '..' )
                        } //foreach ( $rvm_marker_module_list as $rvm_upload_module_entry )


                        if ( $unzipfile && isset( $rvm_marker_module_valid_unzip ) && $rvm_marker_module_valid_unzip ) {

                            //Rename the module filename unzipped
                            rename(  $destination_dir_path . '/' . $marker_module_raw_name . $marker_file_ext,  $destination_dir_path . '/' . $rvm_random_number_for_marker_file . $marker_module_raw_name . $marker_file_ext );
                            //Delete the .zip file(filename)
                            unlink( $destination_dir_path . '/' . $marker_module_name );

                        }//if ( $unzipfile && isset( $rvm_marker_module_valid_unzip ) && $rvm_marker_module_valid_unzip )
                       
                    }//if( is_dir( $destination_dir_path ) )


                    $rvm_relative_path_to_new_marker_module_name = $destination_relative_uploads_path . '/' . $rvm_random_number_for_marker_file . $marker_module_raw_name . $marker_file_ext;


                    // Retrieve all default options from DB
                    $rvm_options = rvm_retrieve_options();
 
                    $rvm_custom_icon_marker_module_path_verified_value = !empty( $_REQUEST[ 'custom_marker_icon_module_path' ] ) ? $rvm_relative_path_to_new_marker_module_name : $rvm_options[ 'rvm_custom_icon_marker_module_path_verified' ] ;

                     //Push the path for the marker module file into an hidden file, so to be saved into rvm_options
                    $output .= '<input type="hidden" data-test="test" value="' . $rvm_custom_icon_marker_module_path_verified_value . '"  name="rvm_options[rvm_custom_icon_marker_module_path_verified]" id="rvm_custom_icon_marker_module_path_verified" />';

                    $output .= '<p class="rvm_messages rvm_success_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/green-check4.png" alt="check" /><span>' . __( 'Marker module installed correctly', RVM_TEXT_DOMAIN ) . '</span><p>';


                }//function_exists( 'unzip_file' )

                else {

                    die( '<p class="rvm_messages rvm_error_messages">' . __( 'You have not unzip_file() function available for you WP or you did not provided a valid marker module!', RVM_TEXT_DOMAIN ) . '<p>' );

                }

            }//if ( isset( $marker_module_raw_name ) || $marker_module_raw_name == $marker_module_name.$marker_module_ext )

            else {
                $output .= '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'It seems you are trying to upload an incorrect file', RVM_TEXT_DOMAIN ) . '</span></p>';
            }
            

        }//isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'custom_marker_icon_module_path' ] )

        else {
            $output .= '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'Uhmmm... there are some issues installing the marker module', RVM_TEXT_DOMAIN ) . '</span></p>';
        }
    
        die( $output );

} // function rvm_ajax_custom_marker_icon_module

// Custom Marker Icon Restore
add_action( 'wp_ajax_rvm_restore_default_marker_icon', 'rvm_ajax_restore_default_marker_icon' );
function rvm_ajax_restore_default_marker_icon() {
    $output = '';
    // check if nonce security value and postid are sent
    if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) ) {
        update_post_meta( $_REQUEST[ 'rvm_mbe_post_id' ], '_rvm_mbe_custom_marker_icon_path', 'default' );
        $output .= '<p class="rvm_messages rvm_success_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/green-check4.png" alt="Success" /><span>' . __( 'Default marker icon correctly restored !', RVM_TEXT_DOMAIN ) . '</span></p>';
    }
    else {
        $output .= '<p class="rvm_messages rvm_error_messages"><img  src="' . RVM_IMG_PLUGIN_DIR . '/warning-icon.png" alt="Warning" /><span>' . __( 'Uhmmm... there are some issues restoring the marker icon', RVM_TEXT_DOMAIN ) . '</span></p>';
    }

    die( $output ) ;
}//function rvm_ajax_restore_default_marker_icon()

// Export Subdivisions to csv
add_action( 'wp_ajax_rvm_export_regions', 'rvm_ajax_export_regions' );
function rvm_ajax_export_regions() {

    if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_mbe_select_map' ] ) ) {
        
        $array_regions = rvm_include_custom_map_settings( $_REQUEST[ 'rvm_mbe_post_id' ],  $_REQUEST[ 'rvm_mbe_select_map' ] );

        // Sort regiosn alphabetically
        ksort( $array_regions );
            $data = array();

            foreach ( $array_regions as $region ) {
            // function regionsparams() can be found in rvm_core.php
                $regionsparams_array = regionsparams( $_REQUEST[ 'rvm_mbe_post_id' ], $region[ 1 ] ); // get regions/
            
                $data[] = implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,array( $region[ 1 ],$region[ 2 ], $regionsparams_array[ 'field_region_link' ],$regionsparams_array[ 'field_region_bg' ],$regionsparams_array[ 'field_region_popup' ],$regionsparams_array[ 'field_region_mouse_hover_over_colour' ],$regionsparams_array[ 'field_region_onclick_action' ]));

            }


            $fp = fopen('php://output', 'w');
            foreach ( $data as $line ) {
                $val = explode( RVM_CUSTOM_MAPS_PATHS_DELIMITER, $line );
                fputcsv($fp, $val, ',', '"');
            }
            fclose($fp);

    }//isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_mbe_select_map' ] )

    die() ;

}

// Import Subdivisions from csv file
add_action( 'wp_ajax_rvm_import_regions', 'rvm_ajax_import_regions' );
function rvm_ajax_import_regions() {
    $output = '';
    if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_upload_regions_file_path' ] ) ) {

        $File = $_REQUEST[ 'rvm_upload_regions_file_path' ];

        $rvm_imported_regions_array  = array();
        $handle     = fopen($File, "r");
        if( empty($handle) === false ) {
            while( ($data = fgetcsv( $handle ) ) !== FALSE){
                $rvm_imported_regions_array[] = $data;
            }
            fclose( $handle );
        }


        for( $i=0; $i < count( $rvm_imported_regions_array ); $i++ ) {
            
            $output .= '<div class="rvm_region_name rvm_region_hide"><h4><b>' . $rvm_imported_regions_array[ $i ][ 1 ] . '</b><span class="rvm_arrow"></span></h4></div>';

//If id structure name changes, please update accordingly on rvm_general.js row 56
$output .= '<div id="rvm_region_' . $rvm_imported_regions_array[ $i ][ 0 ] . '"   class="rvm_regions_flex_wrapper">';
$output .= '<div class="rvm_regions_flex">';

//$output .= '<div class="rvm_regions_labelinput_wrapper">';

if( isset($rvm_imported_regions_array[ $i ][ 6 ])) {
    

    //In case user choose to open a link in the action we escape the db entry with esc_url WP built-in feature
    if ( $rvm_imported_regions_array[ $i ][ 6 ] == 'open_link' ||  empty( $rvm_imported_regions_array[ $i ][ 6 ] ) ) {
          $output .= '<div id="rvm_region_input_link_' . $rvm_imported_regions_array[ $i ][ 0 ] . '" class="rvm_regions_input rvm_regions_wrapper_link"><label for="' . __( 'Subdivisions name', RVM_TEXT_DOMAIN ) . '" ' . RVM_LABEL_CLASS . ' >' . __( 'Link', RVM_TEXT_DOMAIN ) . '</label><input ' . RVM_REGION_LINK_CLASS . ' type="text" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" value="' . esc_url( $rvm_imported_regions_array[ $i ][ 2 ] ) . '"></div>';//.rvm_regions_input
    }

    //case user selected to open onto custom tag
    else if ($rvm_imported_regions_array[ $i ][ 6 ] == 'show_custom_selector') {
          $output .= '<div id="rvm_region_input_link_' . $rvm_imported_regions_array[ $i ][ 0 ] . '" class="rvm_regions_input rvm_regions_wrapper_link"><label for="' . __( 'Subdivisions name', RVM_TEXT_DOMAIN ) . '" ' . RVM_LABEL_CLASS . ' >' . __( 'Show following tag (use ID selector without "#")', RVM_TEXT_DOMAIN ) . '</label><input ' . RVM_REGION_LINK_CLASS . ' type="text" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" value="' . esc_html( rvm_delete_first_character( $rvm_imported_regions_array[ $i ][ 2 ], '#') ) . '"></div>';//.rvm_regions_input
    }

    else {
        $output .= '<div id="rvm_region_input_link_' . $rvm_imported_regions_array[ $i ][ 0 ] . '" class="rvm_regions_input rvm_regions_wrapper_link rvm_hide"><label for="' . __( 'Fake input field just for serializing consistency', RVM_TEXT_DOMAIN ) . '" ' . RVM_LABEL_CLASS . ' >' . __( 'Open label on default', RVM_TEXT_DOMAIN ) . '</label><input ' . RVM_REGION_LINK_CLASS . ' type="text" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" value="' . esc_html( $rvm_imported_regions_array[ $i ][ 2 ] ) . '"></div>';//.rvm_regions_input
    }
    
}
            
else {
    $output .= '<div id="rvm_region_input_link_' . $rvm_imported_regions_array[ $i ][ 0 ] . '" class="rvm_regions_input rvm_regions_wrapper_link"><label for="' . __( 'Subdivisions name', RVM_TEXT_DOMAIN ) . '" ' . RVM_LABEL_CLASS . ' >' . __( 'Link', RVM_TEXT_DOMAIN ) . '</label><input ' . RVM_REGION_LINK_CLASS . ' type="text" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" value=""></div>';//.rvm_regions_input
}

//$output .= '</div>';//.rvm_regions_labelinput_wrapper

$output .= '<div class="rvm_regions_input rvm_regions_wrapper_bgcolor"><label for="' . __( 'Background color', RVM_TEXT_DOMAIN ) . '" ' . RVM_LABEL_CLASS . ' >' . __( 'Background', RVM_TEXT_DOMAIN ) . '</label><input class="rvm_color_picker" type="text" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" value="' . strip_tags( $rvm_imported_regions_array[ $i ][ 3 ] ) . '"></div>';//.rvm_regions_input



//$output .= '</div>';//.rvm_flex_regions

//$output .= '<div class="rvm_regions_flex">';
$output .= '<div class="rvm_regions_input rvm_regions_wrapper_popup"><label for="rvm_region_label_popup" ' . RVM_LABEL_CLASS . ' >' . __( 'Label popup', RVM_TEXT_DOMAIN ) . '</label><textarea id="rvm_region_label_popup_' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '" rows="5" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]" >' . esc_attr( wp_unslash( $rvm_imported_regions_array[ $i ][ 4 ] ) ) . '</textarea></div>';

$output .= '<div class="rvm_regions_input rvm_regions_wrapper_hover_color"><label for="rvm_region_activate_on_mouse_over" ' . RVM_LABEL_CLASS . ' >' . __( 'Activate Mouse Over Background <br> <span class="rvm_small_text">hold  [SHIFT] key for multiple select</span>', RVM_TEXT_DOMAIN ) .  '</label><input  type="checkbox" class="rvm_region_checkbox rvm_region_checkbox_bg" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]"    ' . checked( 'checked', $rvm_imported_regions_array[ $i ][ 5 ] , false ) . ' ></div>';


if( isset($rvm_imported_regions_array[ $i ][ 6 ])) {
 $output .= '<div class="rvm_regions_input rvm_regions_onclick_action"><label for="rvm_region_onclick_action" ' . RVM_LABEL_CLASS . ' >' . __( 'When click onto this subdivision: ', RVM_TEXT_DOMAIN ) .  '</label><select class="rvm_region_label_action" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]"><option ' . selected( 'open_link', $rvm_imported_regions_array[ $i ][ 6 ] , false ) . ' value="open_link">' . __( 'Open link', RVM_TEXT_DOMAIN ) .  '</option><option ' . selected( 'open_label_onto_default_card', $rvm_imported_regions_array[ $i ][ 6 ] , false ) . ' value="open_label_onto_default_card" >' . __( 'Open label content onto default card', RVM_TEXT_DOMAIN ) .  '</option><option ' . selected( 'show_custom_selector', $rvm_imported_regions_array[ $i ][ 6 ] , false ) . ' value="show_custom_selector">' . __( 'Show custom selector', RVM_TEXT_DOMAIN ) .  '</option></select>';
}
            else {
                $output .= '<div class="rvm_regions_input rvm_regions_onclick_action"><label for="rvm_region_onclick_action" ' . RVM_LABEL_CLASS . ' >' . __( 'When click onto this subdivision: ', RVM_TEXT_DOMAIN ) .  '</label><select class="rvm_region_label_action" name="' . strval( $rvm_imported_regions_array[ $i ][ 0 ] ) . '[]"><option selcted="selected" value="open_link">Open link</option><option value="open_label_onto_default_card" >Open label content onto default card</option><option value="show_custom_selector">Show custom selector</option></select>';
            }
 $output .= '<input type="hidden" class="rvm_regions_sub_block" value="' . $rvm_imported_regions_array[ $i ][ 0 ] . '"></div>'; // this is needed in conjunction with the select .rvm_region_label_action to target correct link input field and change the label

$output .= '</div>';//.rvm_flex_regions
$output .= '</div>';//.rvm_regions_flex_wrapper

        }//for( $i=0; $i < count( $rvm_imported_regions_array[ 0 ] ); $i++ ) {


        $output .= '</div>'; // close id="rvm_regions_from_db" 


    }//if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_upload_regions_file_path' ] ) )

    die( $output ) ;

}

// Export Markers to csv
add_action( 'wp_ajax_rvm_export_markers', 'rvm_ajax_export_markers' );
function rvm_ajax_export_markers() {
    if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) ) {
        // function markers() can be found in rvm_core.php
        $marker_array_serialized = markers( $_REQUEST[ 'rvm_mbe_post_id' ], 'retrieve', 'serialized' ) ;
        $marker_array_unserialized = markers( $_REQUEST[ 'rvm_mbe_post_id' ], 'retrieve', 'unserialized' ) ;

        
   
        $rvm_marker_array_count =  count( $marker_array_unserialized[ 'rvm_marker_name_array' ]  ) ;  // count element of the array starting from 1
       
        if( is_array( $marker_array_unserialized[ 'rvm_marker_name_array' ] ) && $rvm_marker_array_count > 0  ) {

            //Export the csv file
            //header('Content-Type: text/csv');
            //header('Content-Disposition: attachment; filename="test.csv"');
            
            $data = array(
                    
                    //"'" . $rvm_marker_name_array_TEMP . "'",
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_name_array' ]),
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_lat_array' ]),
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_long_array' ]),
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_link_array' ]),
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_dim_array' ]),
                implode( RVM_CUSTOM_MAPS_PATHS_DELIMITER ,$marker_array_unserialized[ 'rvm_marker_popup_array' ])     
            );

            $fp = fopen('php://output', 'w');
            foreach ( $data as $line ) {
                $val = explode( RVM_CUSTOM_MAPS_PATHS_DELIMITER, $line );
                fputcsv($fp, $val, ',', '"');
            }
            fclose($fp);

        }//if( is_array( $marker_array_unserialized[ 'rvm_marker_name_array' ] ) && $rvm_marker_array_count > 0  )

    }//if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) )

    die() ;
}

// Import Markers from csv file
add_action( 'wp_ajax_rvm_import_markers', 'rvm_ajax_import_markers' );
function rvm_ajax_import_markers() {
    $output = '';
    if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_upload_markers_file_path' ] ) ) {

        $File = $_REQUEST[ 'rvm_upload_markers_file_path' ];

        $rvm_imported_markers_array  = array();
        $handle     = fopen($File, "r");
        if( empty($handle) === false ) {
            while( ($data = fgetcsv( $handle ) ) !== FALSE){
                $rvm_imported_markers_array[] = $data;
            }
            fclose( $handle );
        }

        //Now create the markers blocks to be saved into db
        $output .= '<h4 class="rvm_title rvm_added_markers_title">' . __( 'Added Markers' , RVM_TEXT_DOMAIN ) .'<span>' . __( ' ( Not visible in map preview )' , RVM_TEXT_DOMAIN ) . '</span></h4>' ;
    
        for( $i=0; $i < count( $rvm_imported_markers_array[ 0 ] ); $i++ ) {
            
            $output .= '<div class="rvm_markers">' ;            
            $output .= '<p><label for="marker_name" class="rvm_label rvm_label_markers">' . __( 'Name' , RVM_TEXT_DOMAIN ) . '*</label><input type="text" name="rvm_marker_name[]" value="' . strip_tags( wp_unslash( $rvm_imported_markers_array[ 0 ][ $i ] ) ) . '" /></p>' ;            
            $output .= '<p><label for="marker_lat" class="rvm_label rvm_label_markers">' . __( 'Latitude' , RVM_TEXT_DOMAIN ) . '*</label><input type="text" name="rvm_marker_lat[]" value="' . strip_tags( $rvm_imported_markers_array[ 1 ][ $i ] ) . '" placeholder="e.g. 48.921537" /></p>' ;            
            $output .= '<p><label for="marker_long" class="rvm_label rvm_label_markers">' . __( 'Longitude' , RVM_TEXT_DOMAIN ) . '*</label><input type="text" name="rvm_marker_long[]" value="' . strip_tags( $rvm_imported_markers_array[ 2 ][ $i ] ) . '" placeholder="e.g. -66.829834" /></p>' ;       
            $output .= '<p><label for="marker_link" class="rvm_label rvm_label_markers">' . __( 'Link' , RVM_TEXT_DOMAIN ) . '</label><input type="text" name="rvm_marker_link[]" value="' . esc_url( $rvm_imported_markers_array[ 3 ][ $i ] ) . '" /></p>' ;
            $output .= '<p><label for="marker_dim" class="rvm_label rvm_label_markers">' . __( 'Dimension' , RVM_TEXT_DOMAIN ) . '<br><span class="rvm_small_text">'  . __( 'Use only integer or decimal' , RVM_TEXT_DOMAIN ) .  '</span></label><input type="text" name="rvm_marker_dim[]" value="' . strip_tags( $rvm_imported_markers_array[ 4 ][ $i ] ) . '" placeholder="' . __( 'e.g. 591.20' , RVM_TEXT_DOMAIN ) . '" /></p>' ;
            $output .= '<p><label for="marker_popup" class="rvm_label rvm_label_markers" style="vertical-align:top;">' . __( 'Popup label' , RVM_TEXT_DOMAIN ) . '</label><textarea name="rvm_marker_popup[]" placeholder="' . __( 'e.g. Rome precipitation (mm) long term averages' , RVM_TEXT_DOMAIN ) . '" >' . esc_attr(  wp_unslash( $rvm_imported_markers_array[ 5 ][ $i ] ) ) . '</textarea></p>' ;                
            $output .= '<input type="submit" class="rvm_remove_field button-secondary" value="' . __( 'Remove' , RVM_TEXT_DOMAIN ) . '">' ;              
            $output .= '</div>' ;
            


            //$output = __( 'Markers imported correctly', RVM_TEXT_DOMAIN );
        }

    }//if ( isset( $_REQUEST[ 'nonce' ] ) && isset( $_REQUEST[ 'rvm_mbe_post_id' ] ) && isset( $_REQUEST[ 'rvm_upload_markers_file_path' ] ) )

    else {
       $output = __( 'There are some issues here: have you selected correct csv file?', RVM_TEXT_DOMAIN ) ;
    }

    die( $output ) ;
}

?>