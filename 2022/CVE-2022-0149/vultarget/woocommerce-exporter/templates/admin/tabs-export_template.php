<h3>
	<?php _e( 'Export Templates', 'woocommerce-exporter' ); ?>
	<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
</h3>

<table class="widefat page fixed striped export-templates">
	<thead>

		<tr>
			<th class="manage-column"><?php _e( 'Name', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Status', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Excerpt', 'woocommerce-exporter' ); ?></th>
		</tr>

	</thead>
	<tbody id="the-list">

		<tr>
			<td class="colspanchange" colspan="3"><?php _e( 'No export templates found.', 'woocommerce-exporter' ); ?></td>
		</tr>
	</tbody>

</table>
<!-- .export-templates -->

<?php if( !empty( $export_templates ) ) { ?>
<p style="text-align:right;"><?php printf( __( '%d items', 'woocommerce-exporter' ), count( $export_templates ) ); ?></p>
<?php } ?>