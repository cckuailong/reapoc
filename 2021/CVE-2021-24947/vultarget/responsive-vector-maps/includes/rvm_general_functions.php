<?php
/**
 * GENERAL FUNCTIONS
 * ----------------------------------------------------------------------------
 */

//Get rid of last character in a  string
function rvm_delete_last_character( $str ) {
                $output_temp = substr( $str, 0, -1 );
                return $output_temp;
}
//Get rid of first character if match specific character
function rvm_delete_first_character( $str, $chartomatch ) {
                if( ( substr( $str, 0, 1 ) == $chartomatch ) ) {
                    $output_temp = substr( $str, 1 );
                }

                else { $output_temp = $str; }                
                
                return $output_temp;
}
//check numeric entry values for array
function rvm_check_is_number_in_array( $array_to_check ) //check if numeric, used e.g. for markers lat and long
                {
                $rvm_checked_number_array = $array_to_check;
                foreach ( $rvm_checked_number_array as $key => $rvm_single_value ) {
                                if ( !is_numeric( $rvm_single_value ) ) {
                                                $rvm_checked_number_array[ $key ] = '';
                                } //!is_numeric( $rvm_single_value )
                                else {
                                                $rvm_checked_number_array[ $key ] = $rvm_single_value;
                                }
                } //$rvm_checked_number_array as $key => $rvm_single_value
                return $rvm_checked_number_array;
}
//check html entry values for array and change it into html entities
function rvm_check_is_html_in_array( $array_to_check ) //check if numeric, used e.g. for markers lat and long
                {
                $rvm_checked_html_array = $array_to_check;
                foreach ( $rvm_checked_html_array as $key => $rvm_single_value ) {
                                if ( empty( $rvm_single_value ) ) {
                                                $rvm_checked_html_array[ $key ] = '';
                                } //empty( $rvm_single_value )
                                else {
                                                $rvm_checked_html_array[ $key ] = $rvm_single_value;
                                } // codify single and double quotes
                } //$rvm_checked_html_array as $key => $rvm_single_value
                return $rvm_checked_html_array;
}
//Retrieve data of custom maps
function rvm_retrieve_custom_maps_options( ) {
                // Retrieve all user options from DB
                $rvm_custom_maps_options = get_option( 'rvm_custom_maps_options' );
                return $rvm_custom_maps_options;
}
//Retrieve data of custom maps in new format ( installed as plugin since dec 2019 )
function rvm_retrieve_custom_maps_options_for_plugin_path_system() {
                // Retrieve all user options from DB
                $rvm_custom_maps_options_for_plugin_path_system = get_option( 'rvm_custom_maps_options_for_plugin_path_system' );
                return $rvm_custom_maps_options_for_plugin_path_system;
}
//Get first part of map name
function rvm_retrieve_custom_map_name( $map_name ) {
                $custom_map_name_array = explode( '_', trim( $map_name ) );
                $custom_map_name = $custom_map_name_array[ 0 ];
                return $custom_map_name;
}
//Get map name without the "_" sign
function rvm_retrieve_custom_map_name_without_underscore( $map_name ) {
                $custom_map_name_without_underscore = str_replace( '_', ' ', $map_name );
                return $custom_map_name_without_underscore;
}
//Get name of maps without file extension
function rvm_retrieve_custom_map_raw_name( $filename ) {
                $custom_map_raw_name_array = explode( '.', trim( $filename ) );
                // Explode return a string even if the delimiter is not in the string. First string would be map name in case no extension's provided
                return trim( $custom_map_raw_name_array[ 0 ] );
}
//Get filename extension: i.e. .zip
function rvm_retrieve_custom_map_ext( $filename, $map_ext ) {
                $custom_map_ext = substr( trim( $filename ), -( strlen( $map_ext ) ) );
                return trim( $custom_map_ext );
}
//Get custom map dir and url path from options
function rvm_retrieve_custom_map_dir_and_url_path( $rvm_custom_maps_options ) {
                // RVM_CUSTOM_MAPS_PATHS_DELIMITER = -@rvm@-
                $rvm_retrieve_custom_map_dir_and_url_path = explode( RVM_CUSTOM_MAPS_PATHS_DELIMITER, $rvm_custom_maps_options );
                // Access the WP filesystem and upload dir
                //WP_Filesystem();
                $destination = wp_upload_dir();
                $destination_dir_path = $destination[ 'path' ];
                $destination_basedir_path = $destination[ 'basedir' ]; //i.e /Applications/MAMP/htdocs/wordpress4.3/wp-content/uploads
                $destination_baseurl_path = $destination[ 'baseurl' ]; // i.e http://localhost:8888/wordpress4.3/wp-content/uploads 
                //if we have new dynamic format year/month, so we get 1 element only
                if ( count( $rvm_retrieve_custom_map_dir_and_url_path ) < 2 ) {
                                $rvm_retrieve_custom_map_dir_and_url_path[ 0 ] = $destination_basedir_path . '/' . $rvm_custom_maps_options;
                                $rvm_retrieve_custom_map_dir_and_url_path[ 1 ] = $destination_baseurl_path . '/' . $rvm_custom_maps_options;
                } // if( count( $rvm_retrieve_custom_map_dir_and_url_path ) < 2 ) 
                else {
                                $rvm_retrieve_custom_map_dir_path_temp = substr( $rvm_retrieve_custom_map_dir_and_url_path[ 0 ], strpos( strtolower( $rvm_retrieve_custom_map_dir_and_url_path[ 0 ] ), 'uploads' ) ); // get from uploads on included
                                $rvm_retrieve_custom_map_dir_path_temp = substr( $rvm_retrieve_custom_map_dir_path_temp, 8 ); // strips out uploads/
                                $rvm_retrieve_custom_map_url_path_temp = substr( $rvm_retrieve_custom_map_dir_and_url_path[ 1 ], strpos( strtolower( $rvm_retrieve_custom_map_dir_and_url_path[ 1 ] ), 'uploads' ) ); // get from uploads on included
                                $rvm_retrieve_custom_map_url_path_temp = substr( $rvm_retrieve_custom_map_url_path_temp, 8 ); // strips out uploads/
                                $rvm_retrieve_custom_map_dir_and_url_path[ 0 ] = $destination_basedir_path . '/' . $rvm_retrieve_custom_map_dir_path_temp;
                                $rvm_retrieve_custom_map_dir_and_url_path[ 1 ] = $destination_baseurl_path . '/' . $rvm_retrieve_custom_map_url_path_temp;
                                //$rvm_retrieve_custom_map_dir_and_url_path = array_replace( $rvm_retrieve_custom_map_dir_and_url_path,  $rvm_retrieve_custom_map_dir_and_url_path_temp );
                }
                return $rvm_retrieve_custom_map_dir_and_url_path;
}
// Check if we are in a custom map
function rvm_is_custom_map( $postid ) {
                $rvm_is_custom_map       = false;
                $rvm_custom_map_name     = get_post_meta( $postid, '_rvm_mbe_select_map', true );
                $rvm_custom_maps_options = rvm_retrieve_custom_maps_options();
                if ( !empty( $rvm_custom_maps_options ) && !empty( $rvm_custom_map_name ) ) {
                                $rvm_custom_maps_options = array_reverse( $rvm_custom_maps_options );
                                foreach ( $rvm_custom_maps_options as $key => $value ) {
                                                if ( $key === trim( $rvm_custom_map_name ) ) {
                                                                $rvm_is_custom_map = true;
                                                } //$key === trim( $rvm_custom_map_name )
                                } //$rvm_custom_maps_options as $key => $value
                } //!empty( $rvm_custom_maps_options ) && !empty( $rvm_custom_map_name )
                return $rvm_is_custom_map;
}
// Check if we are in a custom map with new plugin system ( since dec 2019 )
function rvm_is_custom_map_plugin_path_system( $postid ) {
                $rvm_is_custom_map       = false;
                $rvm_custom_map_name     = get_post_meta( $postid, '_rvm_mbe_select_map', true );
                $rvm_custom_maps_options_for_plugin_path_system = rvm_retrieve_custom_maps_options_for_plugin_path_system();
                if ( !empty( $rvm_custom_maps_options_for_plugin_path_system ) && !empty( $rvm_custom_map_name ) ) {
                                $rvm_custom_maps_options_for_plugin_path_system = array_reverse( $rvm_custom_maps_options_for_plugin_path_system );
                                foreach ( $rvm_custom_maps_options_for_plugin_path_system as $key => $value ) {
                                                if ( $key === trim( $rvm_custom_map_name ) ) {
                                                                $rvm_is_custom_map = true;
                                                } //$key === trim( $rvm_custom_map_name )
                                } //$rvm_custom_maps_options as $key => $value
                } //!empty( $rvm_custom_maps_options ) && !empty( $rvm_custom_map_name )
                return $rvm_is_custom_map;
}
function rvm_region_match_when_numeric( $value ) {
                if ( substr( trim( $value ), 0, 4 ) === PREFIX ) {
                                $path = substr( trim( $value ), 4 );
                } //substr( trim( $value ), 0, 4 ) === PREFIX
                else {
                                $path = $value;
                }
                return $path;
}
function rvm_is_map_in_download_dir_yet( $dir, $rvm_custom_map_name ) {
                $rvm_upload_dir = scandir( $dir );
                if ( in_array( $rvm_custom_map_name, $rvm_upload_dir ) ) {
                                $rvm_custom_map_still_in_upload_dir = true;
                } //in_array( $rvm_custom_map_name, $rvm_upload_dir )
                else {
                                $rvm_custom_map_still_in_upload_dir = false;
                }
                return $rvm_custom_map_still_in_upload_dir;
}
function rvm_is_dir_path_dynamic( $dir ) {
                //If the custom map path are in the old long format
                if ( count( explode( '/', $dir ) ) > 2 ) {
                                $rvm_is_dir_path_dynamic = false;
                } //count( explode( '/', $dir ) ) > 2
                else {
                                //If the custom map path are in the new dynamic format year/month
                                $rvm_is_dir_path_dynamic = true;
                }
                return $rvm_is_dir_path_dynamic;
}
function rvm_include_custom_map_settings( $map_id, $rvm_selected_map ) {
                if ( rvm_is_custom_map( $map_id ) || rvm_retrieve_custom_maps_options() ) {
                                $rvm_custom_maps_options = rvm_retrieve_custom_maps_options();
                                $rvm_custom_maps_options = array_reverse( $rvm_custom_maps_options );
                                foreach ( $rvm_custom_maps_options as $key => $value ) {
                                                if ( $key === trim( $rvm_selected_map ) ) {
                                                                $rvm_retrieve_custom_map_dir_and_url_path = rvm_retrieve_custom_map_dir_and_url_path( $value );
                                                                include $rvm_retrieve_custom_map_dir_and_url_path[ 0 ] . $key . '/rvm-cm-settings.php';
                                                                $rvm_custom_maps_found_in_option = true;
                                                } //$key === trim( $rvm_selected_map )
                                } //$rvm_custom_maps_options as $key => $value
                } //if ( rvm_is_custom_map( $map_id ) || rvm_retrieve_custom_maps_options() )

                // Check for active maps plugin using plugin name both for front and back end
				if( in_array($rvm_selected_map . '/' . $rvm_selected_map . '.php', apply_filters( 'active_plugins', get_option('active_plugins') ) ) ) { 
				    //plugin is activated
				
	                if( rvm_is_custom_map( $map_id ) || rvm_retrieve_custom_maps_options_for_plugin_path_system() ) {
	                    $rvm_custom_maps_options_for_plugin_path_system = rvm_retrieve_custom_maps_options_for_plugin_path_system();
	                    // get last value entered temporally
	                    $rvm_custom_maps_options_for_plugin_path_system = array_reverse ( $rvm_custom_maps_options_for_plugin_path_system );
	                    // Sort regions alphabetically
	                    ksort( $rvm_custom_maps_options_for_plugin_path_system );
	                    foreach ( $rvm_custom_maps_options_for_plugin_path_system as $key => $value ) {
	                        if ( $key === trim( $rvm_selected_map ) ) {
	                            @include RVM_GENERAL_PLUGIN_DIR_PATH . $key . '/rvm-cm-settings.php';
	                            $rvm_custom_maps_found_in_option = true;
	                        }


	                    }// foreach ( $rvm_custom_maps_options_for_plugin_path_system as $key => $value
	                }

                }


                if ( !isset( $rvm_custom_maps_found_in_option ) ) {//load default maps in regions folder
                                if( file_exists( RVM_INC_PLUGIN_DIR . '/regions/' . $rvm_selected_map . '-regions.php') ) {
                                    @include RVM_INC_PLUGIN_DIR . '/regions/' . $rvm_selected_map . '-regions.php';
                                    return $regions;
                                }

                                else { 
                                    $regions = "";
                                }
                                
                } //!isset($rvm_custom_maps_found_in_option)
                return $regions;
}
function rvm_retrieve_options( ) {
                // Retrieve all user options from DB
                $rvm_options = get_option( 'rvm_options' );
                return $rvm_options;
}
function rvm_retrieve_custom_maps_url_path( $rvm_custom_map_name ) {
    
                //Get custom maps if exist on DB
            $rvm_custom_maps_options = rvm_retrieve_custom_maps_options();

            //Here $key is the javascript name and $value the path to javascript itself
            if ( !empty( $rvm_custom_maps_options ) ) {
                        // get last value entered temporally
                        $rvm_custom_maps_options = array_reverse( $rvm_custom_maps_options );
                        foreach ( $rvm_custom_maps_options as $key => $value ) {
                            if( $rvm_custom_map_name ==  $key ) {
                                    $rvm_retrieve_custom_map_dir_and_url_path = rvm_retrieve_custom_map_dir_and_url_path( $value );
                                    // Check if custom map is still in original upload subdir: if not do not show it in drop down
                                    $rvm_is_map_in_download_dir_yet = rvm_is_map_in_download_dir_yet( $rvm_retrieve_custom_map_dir_and_url_path[ 0 ] , $key ) ;
                                    
                                     if ( $rvm_is_map_in_download_dir_yet ) {
                                                return $rvm_retrieve_custom_map_dir_and_url_path[ 1 ]  . $key . '/jquery-jvectormap-' . $key . '.js';
                                     }//if ( $rvm_is_map_in_download_dir_yet ) 
                                     break;                                   
                            }  //if( $rvm_custom_map_name ==  $value )                                   
                        } //$rvm_custom_maps_options as $key => $value
            } //!empty( $rvm_custom_maps_options )
}
//Get marker module with file extension (rvm_cimm.zip)
function rvm_retrieve_marker_module_name( $filename ) {
                $marker_module_name_array = explode( '/', trim( $filename ) );                
                // end() get last item of an array
                return trim( end( $marker_module_name_array ) );
}
//Retrieve marker custom icon name from path
function rvm_retrieve_marker_icon_name( $filename ) {
                //$filename will be sometjing like http://localhost/wordpress-4.9.4/wp-content/uploads/2018/03/favicon.png. In order to maintain the path even in other wordpress installation (i.e. moving WP to another domain) user will not loose the rlative path
                $marker_icon_name_array = explode( '/', trim( $filename ) );
                $marker_icon_name_array_count = count( $marker_icon_name_array );
                if ( $marker_icon_name_array_count > 2 ) {             
                    $marker_icon_name = $marker_icon_name_array[$marker_icon_name_array_count-3] . '/' .
                     $marker_icon_name_array[$marker_icon_name_array_count-2] . '/' . $marker_icon_name_array[$marker_icon_name_array_count-1] ; // i.e.: 2018/03/favicon.png
                } else { $marker_icon_name = $filename; } 
                return trim( $marker_icon_name );
}

//Retrieve marker custom icon name from path
function rvm_set_absolute_upload_dir_url() {
    // Access the WP upload dir 
    //WP_filesystem not needed. On shortcode (front-end) will not make the map displaying  
    $upload_folder = wp_upload_dir();
    $upload_folder_url = $upload_folder[ 'baseurl' ] . '/';// i.e : http://localhost:8888/wordpress4.3/wp-content/uploads/
    $upload_folder_dir = $upload_folder[ 'basedir' ] . '/';//i.e /Applications/MAMP/htdocs/wordpress4.3/wp-content/uploads/
    //$upload_folder_array[0] = is_ssl() ? substr( $upload_folder_url, 6) : substr( $upload_folder_url, 5);

    $upload_folder_array[0] = $upload_folder_url;
    //$upload_folder_array[0] = '/wordpress-4.9.4/wp-content/uploads/';
    $upload_folder_array[1] = $upload_folder_dir;
    return $upload_folder_array ;
}
//Check if user uploaded a custom marker icon
function rvm_check_custom_marker_icon_available( $rvm_custom_marker_icon_path ) {
    if( isset ( $rvm_custom_marker_icon_path ) && !empty( $rvm_custom_marker_icon_path ) && $rvm_custom_marker_icon_path != 'default' ) {
        $rvm_is_custom_marker_icon_path = true;
    }
    else {
        $rvm_is_custom_marker_icon_path = false;
    }

    return $rvm_is_custom_marker_icon_path ;
}
//Check whether marker module yet in download directory
function rvm_is_marker_module_in_download_dir_yet( $rvm_marker_module_name ) {
                //$rvm_marker_module_name : i.e: 2018/03/123456_rvm_cimm.php
                //retrieve only year/month subdir              
                $upload_yy_mm = substr($rvm_marker_module_name,0,8);//i.e. 2018/03/
                //retrieve only module name
                $rvm_marker_module_array = explode( '/', trim( $rvm_marker_module_name ) );
                $rvm_marker_module_name = end( $rvm_marker_module_array );
                $upload_folder = wp_upload_dir();
                $upload_folder_dir = $upload_folder[ 'basedir' ];                
                $rvm_upload_dir = scandir( $upload_folder_dir . '/' . $upload_yy_mm );
                if ( in_array( $rvm_marker_module_name, $rvm_upload_dir ) ) {
                                $rvm_marker_module_still_in_upload_dir = true;
                } //in_array( $rvm_custom_marker_module_name, $rvm_upload_dir )
                else {
                                $rvm_marker_module_still_in_upload_dir = false;
                }
                return $rvm_marker_module_still_in_upload_dir;
}
//Check if maps installed via plugin are still active
function rvm_is_plugin_active ( $plugin_path ) {
	// We need to include plugin.php to get is_plugin_active() in front end
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $rvm_is_plugin_active = is_plugin_active( $plugin_path );
    if( $plugin_path && $rvm_is_plugin_active ) {
        $rvm_is_plugin_active = true;
    }
    else {
        $rvm_is_plugin_active = false;
    }
    return $rvm_is_plugin_active;
}
?>