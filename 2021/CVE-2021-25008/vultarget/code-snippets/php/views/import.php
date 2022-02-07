<?php

/**
 * HTML code for the Import Snippets page
 *
 * @package Code_Snippets
 * @subpackage Views
 */

/* Bail if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$max_size_bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );

?>
<div class="wrap">
	<h1><?php _e( 'Import Snippets', 'code-snippets' );

		$admin = code_snippets()->admin;

		if ( $admin->is_compact_menu() ) {

			printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
				esc_html_x( 'Manage', 'snippets', 'code-snippets' ),
				code_snippets()->get_menu_url()
			);

			printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
				esc_html_x( 'Add New', 'snippet', 'code-snippets' ),
				code_snippets()->get_menu_url( 'add' )
			);

			if ( isset( $admin->menus['settings'] ) ) {
				printf( '<a href="%2$s" class="page-title-action">%1$s</a>',
					esc_html_x( 'Settings', 'snippets', 'code-snippets' ),
					code_snippets()->get_menu_url( 'settings' )
				);
			}
		}

	?></h1>

	<div class="narrow">

		<p><?php _e( 'Upload one or more Code Snippets export files and the snippets will be imported.', 'code-snippets' ); ?></p>

		<p><?php
			printf(
				/* translators: %s: link to snippets admin menu */
				__( 'Afterwards, you will need to visit the <a href="%s">All Snippets</a> page to activate the imported snippets.', 'code-snippets' ),
				code_snippets()->get_menu_url( 'manage' )
			); ?></p>


		<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" name="code_snippets_import">
			<?php wp_nonce_field( 'import_code_snippets_file' ); ?>

			<h2><?php _e( 'Duplicate Snippets', 'code-snippets' ); ?></h2>

			<p class="description">
				<?php esc_html_e( 'What should happen if an existing snippet is found with an identical name to an imported snippet?', 'code-snippets' ); ?>
			</p>

			<fieldset>
				<p>
					<label>
						<input type="radio" name="duplicate_action" value="ignore" checked="checked">
						<?php esc_html_e( 'Ignore any duplicate snippets: import all snippets from the file regardless and leave all existing snippets unchanged.', 'code-snippets' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="duplicate_action" value="replace">
						<?php esc_html_e( 'Replace any existing snippets with a newly imported snippet of the same name.', 'code-snippets' ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="duplicate_action" value="skip">
						<?php esc_html_e( 'Do not import any duplicate snippets; leave all existing snippets unchanged.', 'code-snippets' ); ?>
					</label>
				</p>
			</fieldset>

			<h2><?php _e( 'Upload Files', 'code-snippets' ); ?></h2>

			<p class="description">
				<?php _e( 'Choose one or more Code Snippets (.xml or .json) files to upload, then click "Upload files and import".', 'code-snippets' ); ?>
			</p>

			<fieldset>
				<p>
					<label for="upload"><?php esc_html_e( 'Choose files from your computer:', 'code-snippets' ); ?></label>
					<?php printf(
						/* translators: %s: size in bytes */
						esc_html__( '(Maximum size: %s)', 'code-snippets' ),
						size_format( $max_size_bytes )
					); ?>
					<input type="file" id="upload" name="code_snippets_import_files[]" size="25" accept="application/json,.json,text/xml" multiple="multiple">
					<input type="hidden" name="action" value="save">
					<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $max_size_bytes ); ?>">
				</p>
			</fieldset>

			<?php
			do_action( 'code_snippets/admin/import_form' );
			submit_button( __( 'Upload files and import', 'code-snippets' ) );
			?>
		</form>
	</div>
</div>
