<?php
// Check that code was called from WordPress with
// uninstallation constant declared
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit;

// Check if options exist and delete them if present
if ( get_option( 'LinkLibraryGeneral' ) != false ) {
    
    $genoptions = get_option( 'LinkLibraryGeneral' );

    for ($i = 1; $i <= $genoptions['numberstylesets']; $i++) {
        $settingsname = 'LinkLibraryPP' . $i;
        
        delete_option( $settingsname );
    }
    
    delete_option( 'LinkLibraryGeneral' );
}