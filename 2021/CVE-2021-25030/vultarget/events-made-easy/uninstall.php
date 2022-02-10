<?php

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

include("events-manager.php");

// For Single site
if ( !is_multisite() ) 
{
   _eme_uninstall(1);
}
// For Multisite
else 
{
   // For regular options.
   global $wpdb;
   $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
   //$original_blog_id = get_current_blog_id();
   foreach ( $blog_ids as $blog_id ) 
   {
      switch_to_blog( $blog_id );
      _eme_uninstall(1);
      restore_current_blog();
   }
   //switch_to_blog( $original_blog_id );
}

?>
