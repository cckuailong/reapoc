<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Admin_Insert class.
 */
class DLM_Admin_Media_Insert {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'media_buttons', array( $this, 'media_buttons' ), 20 );
		add_action( 'media_upload_add_download', array( $this, 'media_browser' ) );
	}

	/**
	 * media_buttons function.
	 *
	 * @param String $editor_id
	 *
	 * @access public
	 * @return void
	 */
	public function media_buttons( $editor_id = 'content' ) {
		global $post;

		if ( isset( $post ) && isset( $post->post_type ) && 'dlm_download' === $post->post_type ) {
			return;
		}

		echo '<a href="#" class="button insert-download dlm_insert_download" data-editor="' . esc_attr( $editor_id ) . '" title="' . esc_attr__( 'Insert Download', 'download-monitor' ) . '">' . __( 'Insert Download', 'download-monitor' ) . '</a>';
	}

	/**
	 * media_browser function.
	 *
	 * @access public
	 * @return void
	 */
	public function media_browser() {

		// Enqueue scripts and styles for panel
		wp_enqueue_style( 'download_monitor_admin_css', download_monitor()->get_plugin_url() . '/assets/css/admin.css', array( 'dashicons' ) );
		wp_enqueue_script( 'common' );
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		wp_enqueue_style( 'colors' );
		wp_enqueue_script( 'plupload-all' );

		echo '<!DOCTYPE html><html lang="en"><head><title>' . __( 'Insert Download', 'download-monitor' ) . '</title><meta charset="utf-8" />';

		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );
		do_action( 'admin_head' );

		echo '<body id="insert-download" class="wp-core-ui">';

		?>
		<h2 class="nav-tab-wrapper">
			<a href="#insert-shortcode"
			   class="nav-tab nav-tab-active"><?php _e( 'Insert Shortcode', 'download-monitor' ); ?></a><a
				href="#quick-add" class="nav-tab"><?php _e( 'Quick-add download', 'download-monitor' ); ?></a>
		</h2>
		<?php

		// Handle quick-add form
		/**
		 * TODO: Use new repository here
		 */
		if ( ! empty( $_POST['download_url'] ) && ! empty( $_POST['download_title'] ) && wp_verify_nonce( $_POST['quick-add-nonce'], 'quick-add' ) ) {

			$url     = stripslashes( $_POST['download_url'] );
			$title   = sanitize_text_field( stripslashes( $_POST['download_title'] ) );
			$version = sanitize_text_field( stripslashes( $_POST['download_version'] ) );

			try {

				$download = array(
					'post_title'   => $title,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => get_current_user_id(),
					'post_type'    => 'dlm_download'
				);

				$download_id = wp_insert_post( $download );

				if ( $download_id ) {

					// Meta
					update_post_meta( $download_id, '_featured', 'no' );
					update_post_meta( $download_id, '_members_only', 'no' );
					update_post_meta( $download_id, '_redirect_only', 'no' );
					update_post_meta( $download_id, '_download_count', 0 );

					// File
					$file = array(
						'post_title'   => 'Download #' . $download_id . ' File Version',
						'post_content' => '',
						'post_status'  => 'publish',
						'post_author'  => get_current_user_id(),
						'post_parent'  => $download_id,
						'post_type'    => 'dlm_download_version'
					);

					$file_id = wp_insert_post( $file );

					if ( ! $file_id ) {
						throw new Exception( __( 'Error: File was not created.', 'download-monitor' ) );
					}

					// File Manager
					$file_manager = new DLM_File_Manager();

					// Meta
					update_post_meta( $file_id, '_version', $version );
					update_post_meta( $file_id, '_filesize', $file_manager->get_file_size( $url ) );
					update_post_meta( $file_id, '_files', $file_manager->json_encode_files( array( $url ) ) );

					// Hashes
					$hashes = download_monitor()->service( 'hasher' )->get_file_hashes( $url );

					// Set hashes
					update_post_meta( $file_id, '_md5', $hashes['md5'] );
					update_post_meta( $file_id, '_sha1', $hashes['sha1'] );
					update_post_meta( $file_id, '_crc32', $hashes['crc32b'] );

					// Success message
					echo '<div class="updated"><p>' . __( 'Download successfully created.', 'download-monitor' ) . '</p></div>';

				} else {
					throw new Exception( __( 'Error: Download was not created.', 'download-monitor' ) );
				}

			} catch ( Exception $e ) {
				echo '<div class="error"><p>' . $e->getMessage() . "</p></div>";
			}

		}

		?>
		<form id="insert-shortcode" method="post">

            <?php
            $search_query = ( ! empty( $_POST['dlm_search'] ) ? $_POST['dlm_search'] : '' );
            $limit      = 10;
            $page       = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
            $filters    = array( 'post_status' => 'publish' );
            if ( ! empty( $search_query ) ) {
	            $filters['s'] = $search_query;
            }
            $d_num_rows = download_monitor()->service( 'download_repository' )->num_rows( $filters );
            $downloads  = download_monitor()->service( 'download_repository' )->retrieve( $filters, $limit, ( ( $page - 1 ) * $limit ) );
            ?>
            <fieldset>
                <legend><?php _e( 'Search download', 'download-monitor' ); ?>:</legend>
                <label>
                    <input type="text" name="dlm_search" value='<?php echo str_replace( "'", "", stripslashes( ( $search_query ) ) ); ?>'/>
                    <input type="submit" name="dlm_search_submit" value="Search" class="button button-primary" />
                </label>
            </fieldset>

			<fieldset>
				<legend><?php _e( 'Choose a download', 'download-monitor' ); ?>:</legend>
				<?php

				foreach ( $downloads as $download ) {
					echo '<label><input name="download_id" class="radio" type="radio" value="' . absint( $download->get_id() ) . '" /> #' . $download->get_id() . ' &ndash; ' . $download->get_title() . ' &ndash; ' . $download->get_version()->get_filename() . '</label>';
				}

				if ( $d_num_rows > $limit ) {

					echo paginate_links( apply_filters( 'download_monitor_pagination_args', array(
						'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ),
						'format'    => '',
						'current'   => $page,
						'total'     => ceil( $d_num_rows / $limit ),
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3
					) ) );
				}
				?>
			</fieldset>

			<p>
				<label for="template_name"><?php _e( 'Template', 'download-monitor' ); ?>:</label>
				<input type="text" id="template_name" value="" class="input"
				       placeholder="<?php _e( 'Template Name', 'download-monitor' ); ?>"/>
				<span class="description">
					<?php _e( 'Leaving this blank will use the default <code>content-download.php</code> template file. If you enter, for example, <code>image</code>, the <code>content-download-image.php</code> template will be used instead.', 'download-monitor' ); ?>
				</span>
			</p>

			<p>
				<input type="button" class="button insert_download button-primary button-large"
				       value="<?php _e( 'Insert Shortcode', 'download-monitor' ); ?>"/>
			</p>

		</form>

		<form id="quick-add" action="" method="post">

			<!-- Uploader section -->
			<div id="plupload-upload-ui" class="hide-if-no-js">
				<div id="drag-drop-area" style="height:240px">
					<div class="drag-drop-inside">
						<p class="drag-drop-info"><?php _e( 'Drop file here', 'download-monitor' ); ?></p>

						<p><?php echo _x( 'or', 'Drop file here *or* select file', 'download-monitor' ); ?></p>

						<p class="drag-drop-buttons"><input id="plupload-browse-button" type="button"
						                                    value="<?php esc_attr_e( 'Select File', 'download-monitor' ); ?>"
						                                    class="button"/></p>
					</div>
				</div>
				<p><a href="#" class="add_manually"><?php _e( 'Enter URL manually', 'download-monitor' ); ?> &rarr;</a>
				</p>
			</div>
			<div id="quick-add-details" style="display:none">
				<p>
					<label for="download_url"><?php _e( 'Download URL', 'download-monitor' ); ?>:</label>
					<input type="text" name="download_url" id="download_url" value="" class="download_url input"
					       placeholder="<?php _e( 'Required URL', 'download-monitor' ); ?>"/>
				</p>

				<p>
					<label for="download_title"><?php _e( 'Download Title', 'download-monitor' ); ?>:</label>
					<input type="text" name="download_title" id="download_title" value="" class="download_title input"
					       placeholder="<?php _e( 'Required title', 'download-monitor' ); ?>"/>
				</p>

				<p>
					<label for="download_version"><?php _e( 'Version', 'download-monitor' ); ?>:</label>
					<input type="text" name="download_version" id="download_version" value="" class="input"
					       placeholder="<?php _e( 'Optional version number', 'download-monitor' ); ?>"/>
				</p>

				<p>
					<input type="submit" class="button button-primary button-large"
					       value="<?php _e( 'Save Download', 'download-monitor' ); ?>"/>
					<?php wp_nonce_field( 'quick-add', 'quick-add-nonce' ) ?>
				</p>
			</div>

		</form>

		<script type="text/javascript">
			jQuery( function () {

				jQuery( '.nav-tab-wrapper a' ).click( function () {
					jQuery( '#insert-shortcode, #quick-add' ).hide();
					jQuery( jQuery( this ).attr( 'href' ) ).show();
					jQuery( 'a.nav-tab-active' ).removeClass( 'nav-tab-active' );
					jQuery( this ).addClass( 'nav-tab-active' );
					return false;
				} );

				jQuery( '#quick-add' ).hide();

				jQuery( 'body' ).on( 'click', '.insert_download', function () {

					var win = window.dialogArguments || opener || parent || top;

					var download_id = jQuery( 'input[name="download_id"]:checked' ).val();
					var template = jQuery( '#template_name' ).val();
					var shortcode = '[download id="' + download_id + '"';

					if ( template )
						shortcode = shortcode + ' template="' + template + '"';

					shortcode = shortcode + ']';

					win.send_to_editor( shortcode );

					return false;
				} );

				jQuery( '.add_manually' ).click( function () {
					jQuery( '#plupload-upload-ui' ).slideUp();
					jQuery( '#quick-add-details' ).slideDown();
					return false;
				} );

				<?php
					$plupload_init = array(
						'runtimes'            => 'html5,silverlight,flash,html4',
						'browse_button'       => 'plupload-browse-button',
						'container'           => 'plupload-upload-ui',
						'drop_element'        => 'drag-drop-area',
						'file_data_name'      => 'async-upload',
						'multiple_queues'     => false,
						'max_file_size'       => wp_max_upload_size() . 'b',
						'url'                 => admin_url( 'admin-ajax.php' ),
						'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
						'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
						'filters'             => array( array( 'title' => __( 'Allowed Files' ), 'extensions' => '*' ) ),
						'multipart'           => true,
						'urlstream_upload'    => true,

						// additional post data to send to our ajax hook
						'multipart_params'    => array(
							'_ajax_nonce' => wp_create_nonce( 'file-upload' ),
							'action'      => 'download_monitor_insert_panel_upload',
							'type'        => 'dlm_download'
						),
					);

					// we should probably not apply this filter, plugins may expect wp's media uploader...
					$plupload_init = apply_filters('plupload_init', $plupload_init);
				?>

				// create the uploader and pass the config from above
				var uploader = new plupload.Uploader( <?php echo json_encode( $plupload_init ); ?> );

				// checks if browser supports drag and drop upload, makes some css adjustments if necessary
				uploader.bind( 'Init', function ( up ) {
					var uploaddiv = jQuery( '#plupload-upload-ui' );

					if ( up.features.dragdrop ) {
						uploaddiv.addClass( 'drag-drop' );

						jQuery( '#drag-drop-area' )
							.bind( 'dragover.wp-uploader', function () {
								uploaddiv.addClass( 'drag-over' );
							} )
							.bind( 'dragleave.wp-uploader, drop.wp-uploader', function () {
								uploaddiv.removeClass( 'drag-over' );
							} );

					} else {
						uploaddiv.removeClass( 'drag-drop' );
						jQuery( '#drag-drop-area' ).unbind( '.wp-uploader' );
					}
				} );

				uploader.init();

				// a file was added in the queue
				uploader.bind( 'FilesAdded', function ( up, files ) {
					var hundredmb = 100 * 1024 * 1024, max = parseInt( up.settings.max_file_size, 10 );

					plupload.each( files, function ( file ) {
						if ( max > hundredmb && file.size > hundredmb && up.runtime != 'html5' ) {
							// file size error?
						} else {
							jQuery( '.drag-drop-inside' ).html( '<p><?php _e( 'Please wait...', 'download-monitor' ); ?></p>' );
						}
					} );

					up.refresh();
					up.start();
				} );

				// a file was uploaded
				uploader.bind( 'FileUploaded', function ( up, file, response ) {
					jQuery( '#quick-add-details' ).find( 'input.download_url' ).val( response.response );
					jQuery( '#quick-add-details' ).find( 'input.download_title' ).val( basename( response.response ) );
					jQuery( '#plupload-upload-ui' ).slideUp();
					jQuery( '#quick-add-details' ).slideDown();
				} );

				function basename( path ) {
					return path.split( '/' ).reverse()[ 0 ];
				}

			} );
		</script>
		<?php
		echo '</body></html>';
	}
}