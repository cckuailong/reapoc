<?php

add_action( 'wp_ajax_ive_install_and_activate_plugin', 'ive_install_and_activate_plugin' );


function ive_install_and_activate_plugin() {

	// Check for nonce security
	if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
		exit;
	}

	$post_plugin_details	= IVE_Loader::ive_sanitize_array( $_POST['plugin_details'] );

	$plugin_text_domain		= sanitize_text_field( $post_plugin_details['plugin_text_domain'] );
	$plugin_main_file			=	sanitize_text_field( $post_plugin_details['plugin_main_file'] );
	$plugin_url						=	sanitize_text_field( $post_plugin_details['plugin_url'] );

	$plugin = array(
		'text_domain'	=> $plugin_text_domain,
		'path' 				=> $plugin_url,
		'install' 		=> $plugin_text_domain . '/' . $plugin_main_file
	);

	$is_installed = ive_get_plugins( $plugin );

	$msg = '';
	if ( $is_installed ) {
		$is_installed = true;
		$msg = __( 'Plugin Installed Successfully!', 'ibtana-visual-editor' );
	} else {
		$is_installed = false;
		$msg = __( 'Something Went Wrong!', 'ibtana-visual-editor' );
	}
	$response = array( 'status' => $is_installed, 'msg' => $msg );
	wp_send_json( $response );
	exit;
}


function ive_get_plugins( $plugin ) {
	$args = array(
		'path'					=>	ABSPATH . 'wp-content/plugins/',
		'preserve_zip'	=>	false
	);
	$get_plugins = get_plugins();
	if ( !isset( $get_plugins[ trim( $plugin['install'] ) ] ) ) {
		ive_plugin_download( $plugin['path'], $args['path'] . $plugin['text_domain'] . '.zip' );
		ive_plugin_unpack( $args, $args['path'] . $plugin['text_domain'] . '.zip' );
	}
	$is_activated = ive_plugin_activate( $plugin['install'] );
	return $is_activated;
}


function ive_plugin_download($url, $path) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	curl_close($ch);

	if( file_put_contents( $path, $data ) ) {
		return true;
	} else {
		return false;
	}
}


function ive_plugin_unpack( $args, $target ) {

	$file_system = IVE_Helper::get_instance()->get_filesystem();

	$plugin_path = str_replace( ABSPATH, $file_system->abspath(), IBTANA_PLUGIN_DIR ); /* get remote system absolute path */

	$plugin_path = str_replace( "ibtana-visual-editor/", "", $plugin_path );

	/* Acceptable way to use the function */
	$file	=	$target;
	$to		=	$plugin_path;

	$result = unzip_file( $file, $to );

	if( $result !== true ) {
		return false;
	} else {
		wp_delete_file( $file );
		return true;
	}
}


function ive_plugin_activate( $installer ) {
	wp_cache_flush();

	$plugin = plugin_basename( trim( $installer ) );

	$activate_plugin = activate_plugin( $plugin );

	return true;
}
