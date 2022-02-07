<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Admin_Media_Browser class.
 */
class DLM_Admin_Media_Browser {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'media_upload_downloadable_file_browser', array( $this, 'media_browser' ) );
	}

	/**
	 * media_browser function.
	 *
	 * @access public
	 * @return void
	 */
	public function media_browser() {
		// File Manager
		$file_manager = new DLM_File_Manager();

		// Files
		$files = $file_manager->list_files( ABSPATH, 1 );

		echo '<!DOCTYPE html><html lang="en"><head><title>' . __( 'Browse for a file', 'download-monitor' ) . '</title>';

		wp_enqueue_style( 'download_monitor_admin_css', download_monitor()->get_plugin_url() . '/assets/css/admin.css', array( 'dashicons' ) );
		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );
		do_action( 'admin_head' );

		echo '<meta charset="utf-8" /></head><body>';

		echo '<ul class="download_monitor_file_browser">';

		foreach ( $files as $found_file ) {

			$file = pathinfo( $found_file['path'] );

			if ( $found_file['type'] == 'folder' ) {

				echo '<li><a href="#" class="folder" data-path="' . trailingslashit( $file['dirname'] ) . $file['basename'] . '">' . $file['basename'] . '</a></li>';

			} else {

				$filename  = $file['basename'];
				$extension = ( empty( $file['extension'] ) ) ? '' : $file['extension'];

				if ( substr( $filename, 0, 1 ) == '.' ) {
					continue;
				} // Ignore files starting with . like htaccess
				if ( in_array( $extension, array( '', 'php', 'html', 'htm', 'tmp' ) ) ) {
					continue;
				} // Ignored file types

				echo '<li><a href="#" class="file filetype-' . sanitize_title( $extension ) . '" data-path="' . trailingslashit( $file['dirname'] ) . $file['basename'] . '">' . $file['basename'] . '</a></li>';

			}

		}

		echo '</ul>';
		?>
		<script type="text/javascript">
			jQuery( function () {
				jQuery( '.download_monitor_file_browser' ).on( 'click', 'a', function () {

					var $link = jQuery( this );
					var $parent = $link.closest( 'li' );

					if ( $link.is( '.file' ) ) {

						var win = window.dialogArguments || opener || parent || top;

						win.send_to_editor( $link.attr( 'data-path' ) );

					} else if ( $link.is( '.folder_open' ) ) {

						$parent.find( 'ul' ).remove();
						$link.removeClass( 'folder_open' );

					} else {

						$link.after( '<ul class="load_tree loading"></ul>' );

						var data = {
							action: 'download_monitor_list_files',
							path: jQuery( this ).attr( 'data-path' ),
							security: '<?php echo wp_create_nonce("list-files"); ?>'
						};

						jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function ( response ) {

							$link.addClass( 'folder_open' );

							if ( response ) {
								$parent.find( '.load_tree' ).html( response );
							} else {
								$parent.find( '.load_tree' ).html( '<li class="nofiles"><?php _e('No files found', 'download-monitor'); ?></li>' );
							}
							$parent.find( '.load_tree' ).removeClass( 'load_tree loading' );

						} );
					}
					return false;
				} );
			} );
		</script>
		<?php
		echo '</body></html>';
	}

}