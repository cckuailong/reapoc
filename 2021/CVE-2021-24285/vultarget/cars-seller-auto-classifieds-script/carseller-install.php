<?php




global $carsellers_db_version;
$carsellers_db_version = "1.0";

function carsellers_install() {
   global $wpdb;
   global $carsellers_db_version;

   $table_name = $wpdb->prefix . "carsellers_requests";
      
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carseller_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `moving_time` varchar(300) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "carsellers_db_version", $carsellers_db_version );
}

function carsellers_install_data() {
   global $wpdb;
   $welcome_name = "";
   $welcome_text = "Congratulations, you just completed the installation!";
   $table_name = $wpdb->prefix . "liveshoutbox";
   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
}

