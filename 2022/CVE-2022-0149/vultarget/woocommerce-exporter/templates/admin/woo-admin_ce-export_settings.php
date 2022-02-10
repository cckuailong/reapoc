<ul class="subsubsub">
	<li><a href="#general-settings"><?php _e( 'General Settings', 'woo_ce' ); ?></a> |</li>
	<li><a href="#csv-settings"><?php _e( 'CSV Settings', 'woo_ce' ); ?></a></li>
	<?php do_action( 'woo_ce_export_settings_top' ); ?>
</ul>
<!-- .subsubsub -->
<form method="post">
	<table class="form-table">
		<tbody>

			<?php do_action( 'woo_ce_export_settings_before' ); ?>

			<tr id="general-settings">
				<td colspan="2" style="padding:0;">
					<h3><?php _e( 'General Settings', 'woo_ce' ); ?></h3>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="export_filename"><?php _e( 'Export filename', 'woo_ce' ); ?></label></th>
				<td>
					<input name="export_filename" type="text" id="export_filename" value="<?php echo $export_filename; ?>" class="regular-text code" />
					<p class="description"><?php _e( 'The filename of the exported export type. Tags can be used: ', 'woo_ce' ); ?> <code>%dataset%</code>, <code>%date%</code>, <code>%time%</code>, <code>%store_name%</code>.</p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="delete_temporary_csv"><?php _e( 'Enable archives', 'woo_ce' ); ?></label>
				</th>
				<td>
					<select id="delete_temporary_csv" name="delete_temporary_csv">
						<option value="0"<?php selected( $delete_csv, 0 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
						<option value="1"<?php selected( $delete_csv, 1 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Save copies of CSV exports to the WordPress Media for downloading later. By default this option is turned on.', 'woo_ce' ); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="encoding"><?php _e( 'Character encoding', 'woo_ce' ); ?></label>
				</th>
				<td>
<?php if( $file_encodings ) { ?>
					<select id="encoding" name="encoding">
						<option value=""><?php _e( 'System default', 'woo_ce' ); ?></option>
	<?php foreach( $file_encodings as $key => $chr ) { ?>
						<option value="<?php echo $chr; ?>"<?php selected( $chr, $encoding ); ?>><?php echo $chr; ?></option>
	<?php } ?>
					</select>
<?php } else { ?>
					<p class="description"><?php _e( 'Character encoding options are unavailable in PHP 4, contact your hosting provider to update your site install to use PHP 5 or higher.', 'woo_ce' ); ?></p>
<?php } ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Date format', 'woo_ce' ); ?></th>
				<td>
					<fieldset>
						<label title="F j, Y"><input type="radio" name="date_format" value="F j, Y"<?php checked( $date_format, 'F j, Y' ); ?>> <span><?php echo date( 'F j, Y' ); ?></span></label><br>
						<label title="Y/m/d"><input type="radio" name="date_format" value="Y/m/d"<?php checked( $date_format, 'Y/m/d' ); ?>> <span><?php echo date( 'Y/m/d' ); ?></span></label><br>
						<label title="m/d/Y"><input type="radio" name="date_format" value="m/d/Y"<?php checked( $date_format, 'm/d/Y' ); ?>> <span><?php echo date( 'm/d/Y' ); ?></span></label><br>
						<label title="d/m/Y"><input type="radio" name="date_format" value="d/m/Y"<?php checked( $date_format, 'd/m/Y' ); ?>> <span><?php echo date( 'd/m/Y' ); ?></span></label><br>
<!--
						<label><input type="radio" name="date_format" id="date_format_custom_radio" value="\c\u\s\t\o\m"> Custom: </label><input type="text" name="date_format_custom" value="F j, Y" class="small-text"> <span class="example"> January 6, 2014</span> <span class="spinner"></span>
						<p><a href="http://codex.wordpress.org/Formatting_Date_and_Time"><?php _e( 'Documentation on date and time formatting', 'woo_ce' ); ?></a>.</p>
-->
					</fieldset>
					<p class="description"><?php _e( 'The date format option affects how date\'s are presented within your CSV file. Default is set to DD/MM/YYYY.', 'woo_ce' ); ?></p>
				</td>
			</tr>

<?php if( !ini_get( 'safe_mode' ) ) { ?>
			<tr>
				<th>
					<label for="timeout"><?php _e( 'Script timeout', 'woo_ce' ); ?></label>
				</th>
				<td>
					<select id="timeout" name="timeout">
						<option value="600"<?php selected( $timeout, 600 ); ?>><?php printf( __( '%s minutes', 'woo_ce' ), 10 ); ?></option>
						<option value="1800"<?php selected( $timeout, 1800 ); ?>><?php printf( __( '%s minutes', 'woo_ce' ), 30 ); ?></option>
						<option value="3600"<?php selected( $timeout, 3600 ); ?>><?php printf( __( '%s hour', 'woo_ce' ), 1 ); ?></option>
						<option value="0"<?php selected( $timeout, 0 ); ?>><?php _e( 'Unlimited', 'woo_ce' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Script timeout defines how long Store Exporter is \'allowed\' to process your CSV file, once the time limit is reached the export process halts.', 'woo_ce' ); ?></p>
				</td>
			</tr>
<?php } ?>

			<tr id="csv-settings">
				<td colspan="2" style="padding:0;">
					<hr />
					<h3><?php _e( 'CSV Settings', 'woo_ce' ); ?></h3>
				</td>
			</tr>

			<tr>
				<th>
					<label for="delimiter"><?php _e( 'Field delimiter', 'woo_ce' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo $delimiter; ?>" maxlength="1" class="text" />
					<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'woo_pc' ); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="category_separator"><?php _e( 'Category separator', 'woo_ce' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo $category_separator; ?>" maxlength="1" class="text" />
					<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'woo_ce' ); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="bom"><?php _e( 'Add BOM character', 'woo_ce' ); ?></label>
				</th>
				<td>
					<select id="bom" name="bom">
						<option value="1"<?php selected( $bom, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
						<option value="0"<?php selected( $bom, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Mark the CSV file as UTF8 by adding a byte order mark (BOM) to the export, useful for non-English character sets.', 'woo_ce' ); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label for="escape_formatting"><?php _e( 'Field escape formatting', 'woo_ce' ); ?></label>
				</th>
				<td>
					<label><input type="radio" name="escape_formatting" value="all"<?php checked( $escape_formatting, 'all' ); ?> />&nbsp;<?php _e( 'Escape all fields', 'woo_ce' ); ?></label><br />
					<label><input type="radio" name="escape_formatting" value="excel"<?php checked( $escape_formatting, 'excel' ); ?> />&nbsp;<?php _e( 'Escape fields as Excel would', 'woo_ce' ); ?></label>
					<p class="description"><?php _e( 'Choose the field escape format that suits your spreadsheet software (e.g. Excel).', 'woo_ce' ); ?></p>
				</td>
			</tr>

			<?php do_action( 'woo_ce_export_settings_after' ); ?>

		</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'woo_ce' ); ?>" />
	</p>

	<input type="hidden" name="action" value="save" />
</form>
<?php do_action( 'woo_ce_export_settings_bottom' ); ?>