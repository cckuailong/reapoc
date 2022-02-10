<ul class="subsubsub">
	<li><a href="#general-settings"><?php _e( 'General Settings', 'woocommerce-exporter' ); ?></a> |</li>
	<li><a href="#csv-settings"><?php _e( 'CSV Settings', 'woocommerce-exporter' ); ?></a></li>
	<?php do_action( 'woo_ce_export_settings_top' ); ?>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<form method="post">
	<table class="form-table">
		<tbody>

			<?php do_action( 'woo_ce_export_settings_before' ); ?>

			<tr id="general-settings">
				<td colspan="2" style="padding:0;">
					<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'General Settings', 'woocommerce-exporter' ); ?></h3>
					<p class="description"><?php _e( 'Manage export options across Store Exporter from this screen. Options are broken into sections for different export formats and methods. Click Save Changes to apply changes.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="export_filename"><?php _e( 'Export filename', 'woocommerce-exporter' ); ?></label></th>
				<td>
					<input type="text" name="export_filename" id="export_filename" value="<?php echo esc_attr( $export_filename ); ?>" class="large-text code" />
					<p class="description"><?php _e( 'The filename of the exported export type. Tags can be used: ', 'woocommerce-exporter' ); ?> <code>%dataset%</code>, <code>%date%</code>, <code>%time%</code>, <code>%random</code>, <code>%store_name%</code>.</p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="delete_file"><?php _e( 'Enable archives', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<select id="delete_file" name="delete_file">
						<option value="0"<?php selected( $delete_file, 0 ); ?>><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
						<option value="1"<?php selected( $delete_file, 1 ); ?>><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
					</select>
<?php if( $delete_file == 1 && woo_ce_get_option( 'hide_archives_tab', 0 ) == 1 ) { ?>
<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'restore_archives_tab', '_wpnonce' => wp_create_nonce( 'woo_ce_restore_archives_tab' ) ) ) ); ?>"><?php _e( 'Restore Archives tab', 'woocommerce-exporter' ); ?></a>
<?php } ?>
<?php if( $delete_file == 0 ) { ?>
					<p class="warning"><?php _e( 'Warning: Saving sensitve export files (e.g. Customers, Orders, etc.) to the WordPress Media directory will make the export files accessible to the public without restriction.', 'woocommerce-exporter' ); ?></p>
<?php } ?>
					<p class="description"><?php _e( 'Save copies of exports to the WordPress Media for later downloading. By default this option is turned off.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="encoding"><?php _e( 'Character encoding', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
<?php if( $file_encodings ) { ?>
					<select id="encoding" name="encoding">
						<option value=""><?php _e( 'System default', 'woocommerce-exporter' ); ?></option>
	<?php foreach( $file_encodings as $key => $chr ) { ?>
						<option value="<?php echo $chr; ?>"<?php selected( $chr, $encoding ); ?>><?php echo $chr; ?></option>
	<?php } ?>
					</select>
<?php } else { ?>
	<?php if( version_compare( phpversion(), '5', '<' ) ) { ?>
					<p class="description"><?php _e( 'Character encoding options are unavailable in PHP 4, contact your hosting provider to update your site install to use PHP 5 or higher.', 'woocommerce-exporter' ); ?></p>
	<?php } else { ?>
					<p class="description"><?php _e( 'Character encoding options are unavailable as the required mb_list_encodings() function is missing, contact your hosting provider to have the mbstring extension installed.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
<?php } ?>
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Date format', 'woocommerce-exporter' ); ?></th>
				<td>
					<ul style="margin-top:0.2em;">
						<li><label title="F j, Y"><input type="radio" name="date_format" value="F j, Y"<?php checked( $date_format, 'F j, Y' ); ?>> <span><?php echo date( 'F j, Y' ); ?></span></label></li>
						<li><label title="Y/m/d"><input type="radio" name="date_format" value="Y/m/d"<?php checked( $date_format, 'Y/m/d' ); ?>> <span><?php echo date( 'Y/m/d' ); ?></span></label></li>
						<li><label title="m/d/Y"><input type="radio" name="date_format" value="m/d/Y"<?php checked( $date_format, 'm/d/Y' ); ?>> <span><?php echo date( 'm/d/Y' ); ?></span></label></li>
						<li><label title="d/m/Y"><input type="radio" name="date_format" value="d/m/Y"<?php checked( $date_format, 'd/m/Y' ); ?>> <span><?php echo date( 'd/m/Y' ); ?></span></label></li>
						<li><label><input type="radio" name="date_format" value="custom"<?php checked( in_array( $date_format, array( 'F j, Y', 'Y/m/d', 'm/d/Y', 'd/m/Y' ) ), false ); ?>/> <?php _e( 'Custom', 'woocommerce-exporter' ); ?>: </label><input type="text" name="date_format_custom" value="<?php echo sanitize_text_field( $date_format ); ?>" class="text" /></li>
						<li><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e( 'Documentation on date and time formatting', 'woocommerce-exporter' ); ?></a>.</li>
					</ul>
					<p class="description"><?php _e( 'The date format option affects how date\'s are presented within your export file. Default is set to DD/MM/YYYY.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
<?php if( !ini_get( 'safe_mode' ) ) { ?>
			<tr>
				<th>
					<label for="timeout"><?php _e( 'Script timeout', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<select id="timeout" name="timeout">
						<option value="600"<?php selected( $timeout, 600 ); ?>><?php printf( __( '%s minutes', 'woocommerce-exporter' ), 10 ); ?></option>
						<option value="1800"<?php selected( $timeout, 1800 ); ?>><?php printf( __( '%s minutes', 'woocommerce-exporter' ), 30 ); ?></option>
						<option value="3600"<?php selected( $timeout, 3600 ); ?>><?php printf( __( '%s hour', 'woocommerce-exporter' ), 1 ); ?></option>
						<option value="0"<?php selected( $timeout, 0 ); ?>><?php _e( 'Unlimited', 'woocommerce-exporter' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Script timeout defines how long Store Exporter is \'allowed\' to process your export file, once the time limit is reached the export process halts.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>

			<tr>
				<th>&nbsp;</th>
				<td style="vertical-align:top;">
					<p><a href="#" id="advanced-settings"><?php _e( 'View advanced settings', 'woocommerce-exporter' ); ?></a></p>
					<div class="advanced-settings">
						<ul>
							<li><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'nuke_notices', '_wpnonce' => wp_create_nonce( 'woo_ce_nuke_notices' ) ) ) ); ?>" class="delete" data-confirm="<?php _e( 'This will restore all dismissed notices associated with Store Exporter Deluxe. Are you sure you want to proceed?', 'woocommerce-exporter' ); ?>"><?php _e( 'Reset dismissed Store Export Deluxe notices', 'woocommerce-exporter' ); ?></a></li>
							<li><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'nuke_options', '_wpnonce' => wp_create_nonce( 'woo_ce_nuke_options' ) ) ) ); ?>" class="delete" data-confirm="<?php _e( 'This will permanently delete all WordPress Options associated with Store Exporter Deluxe. Are you sure you want to proceed?', 'woocommerce-exporter' ); ?>"><?php _e( 'Delete Store Exporter Deluxe WordPress Options', 'woocommerce-exporter' ); ?></a></li>
							<li><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'nuke_archives', '_wpnonce' => wp_create_nonce( 'woo_ce_nuke_archives' ) ) ) ); ?>" class="delete" data-confirm="<?php _e( 'This will permanently delete all saved exports listed within the Archives screen of Store Exporter Deluxe. Are you sure you want to proceed?', 'woocommerce-exporter' ); ?>"><?php _e( 'Delete archived exports', 'woocommerce-exporter' ); ?></a></li>
						</ul>
					</div>
					<!-- .advanced-settings -->
				</td>
			</tr>

<?php } ?>

			<?php do_action( 'woo_ce_export_settings_general' ); ?>

			<tr id="csv-settings">
				<td colspan="2" style="padding:0;">
					<hr />
					<h3><div class="dashicons dashicons-media-spreadsheet"></div>&nbsp;<?php _e( 'CSV Settings', 'woocommerce-exporter' ); ?></h3>
				</td>
			</tr>
			<tr>
				<th>
					<label for="delimiter"><?php _e( 'Field delimiter', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo esc_attr( $delimiter ); ?>" maxlength="5" class="text" />
					<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="category_separator"><?php _e( 'Category separator', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo esc_attr( $category_separator ); ?>" maxlength="5" class="text" />
					<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character or \'LF\' for line breaks between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="line_ending"><?php _e( 'Line ending formatting', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<select id="line_ending" name="line_ending">
						<option value="windows" selected="selected"><?php _e( 'Windows', 'woocommerce-exporter' ); ?></option>
						<option value="mac" disabled="disabled"><?php _e( 'Mac' ,'woocommerce-exporter' ); ?></option>
						<option value="unix" disabled="disabled"><?php _e( 'Unix', 'woocommerce-exporter' ); ?></option>
					</select>
					<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					<p class="description"><?php _e( 'Choose the line ending formatting that suits the Operating System you plan to use the export file with (e.g. a Windows desktop, Mac laptop, etc.). Default is Windows.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="bom"><?php _e( 'Add BOM character', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<select id="bom" name="bom">
						<option value="1"<?php selected( $bom, 1 ); ?>><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
						<option value="0"<?php selected( $bom, 0 ); ?>><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Mark the CSV file as UTF8 by adding a byte order mark (BOM) to the export, useful for non-English character sets.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="escape_formatting"><?php _e( 'Field escape formatting', 'woocommerce-exporter' ); ?></label>
				</th>
				<td>
					<ul style="margin-top:0.2em;">
						<li><label><input type="radio" name="escape_formatting" value="all"<?php checked( $escape_formatting, 'all' ); ?> />&nbsp;<?php _e( 'Escape all fields', 'woocommerce-exporter' ); ?></label></li>
						<li><label><input type="radio" name="escape_formatting" value="excel"<?php checked( $escape_formatting, 'excel' ); ?> />&nbsp;<?php _e( 'Escape fields as Excel would', 'woocommerce-exporter' ); ?></label></li>
					</ul>
					<p class="description"><?php _e( 'Choose the field escape format that suits your spreadsheet software (e.g. Excel).', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'woo_ce_export_settings_after' ); ?>

		</tbody>
	</table>
	<!-- .form-table -->
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'woocommerce-exporter' ); ?>" />
	</p>
	<input type="hidden" name="action" value="save-settings" />
	<?php wp_nonce_field( 'woo_ce_save_settings' ); ?>
</form>
<?php do_action( 'woo_ce_export_settings_bottom' ); ?>