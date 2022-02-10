<?php
function woo_ce_export_options_export_format() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr>
	<th>
		<label><?php _e( 'Export format', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<label><input type="radio" name="export_format" value="csv"<?php checked( 'csv', 'csv' ); ?> /> <?php _e( 'CSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="tsv"<?php checked( 'tsv', 'tsv' ); ?> /> <?php _e( 'TSV', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Tab Separated Values)', 'woocommerce-exporter' ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xls" disabled="disabled" /> <?php _e( 'Excel (XLS)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woocommerce-exporter' ); ?> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xlsx" disabled="disabled" /> <?php _e( 'Excel (XLSX)', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woocommerce-exporter' ); ?> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="xml" disabled="disabled" /> <?php _e( 'XML', 'woocommerce-exporter' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woocommerce-exporter' ); ?> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="export_format" value="rss" disabled="disabled" /> <?php _e( 'RSS 2.0', 'woocommerce-exporter' ); ?> <span class="description"><?php printf( __( '(<attr title="%s">XML</attr> feed in RSS 2.0 format)', 'woocommerce-exporter' ), __( 'EXtensible Markup Language', 'woocommerce-exporter' ) ); ?> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

function woo_ce_export_options_export_template() {

	// Store Exporter Deluxe
	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr id="export-template">
	<th>
		<label><?php _e( 'Export template', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<select id="export_template" name="export_template" disabled="disabled" class="select short">
			<option><?php _e( 'Choose a Export Template...', 'woocommerce-exporter' ); ?></option>
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
	</td>
</tr>
<?php
	ob_end_flush();

}

function woo_ce_export_options_troubleshooting() {

	ob_start(); ?>
<tr>
	<th>&nbsp;</th>
	<td>
		<p class="description">
			<?php _e( 'Having difficulty downloading your exports in one go? Use our batch export function - Limit Volume and Volume Offset - to create smaller exports.', 'woocommerce-exporter' ); ?><br />
			<?php _e( 'Set the first text field (Volume limit) to the number of records to export each batch (e.g. 200), set the second field (Volume offset) to the starting record (e.g. 0). After each successful export increment only the Volume offset field (e.g. 201, 401, 601, 801, etc.) to export the next batch of records.', 'woocommerce-exporter' ); ?>
		</p>
	</td>
</tr>
<?php
	ob_end_flush();

}

function woo_ce_export_options_limit_volume() {

	$limit_volume = woo_ce_get_option( 'limit_volume' );

	ob_start(); ?>
<tr>
	<th><label for="limit_volume"><?php _e( 'Limit volume', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<input type="text" size="3" id="limit_volume" name="limit_volume" value="<?php echo esc_attr( $limit_volume ); ?>" size="5" class="text" title="<?php _e( 'Limit volume', 'woocommerce-exporter' ); ?>" />
		<p class="description"><?php _e( 'Limit the number of records to be exported. By default this is not used and is left empty.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

function woo_ce_export_options_volume_offset() {

	$offset = woo_ce_get_option( 'offset' );

	ob_start(); ?>
			<tr>
				<th><label for="offset"><?php _e( 'Volume offset', 'woocommerce-exporter' ); ?></label></th>
				<td>
					<input type="text" size="3" id="offset" name="offset" value="<?php echo esc_attr( $offset ); ?>" size="5" class="text" title="<?php _e( 'Volume offset', 'woocommerce-exporter' ); ?>" />
					<p class="description"><?php _e( 'Set the number of records to be skipped in this export. By default this is not used and is left empty.', 'woocommerce-exporter' ); ?></p>
				</td>
			</tr>
<?php
	ob_end_flush();

}