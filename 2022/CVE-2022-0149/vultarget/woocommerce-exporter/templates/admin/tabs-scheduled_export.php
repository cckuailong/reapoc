<ul class="subsubsub">
	<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?></a> |</li>
	<li><a href="#recent-scheduled-exports"><?php _e( 'Recent Scheduled Exports', 'woocommerce-exporter' ); ?></a></li>
	<?php do_action( 'woo_ce_scheduled_export_settings_top' ); ?>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<?php do_action( 'woo_ce_before_scheduled_exports' ); ?>

<h3 id="scheduled-exports">
	<?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?>
	<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
</h3>

<table class="widefat page fixed striped scheduled-exports">
	<thead>

		<tr>
			<th class="manage-column"><?php _e( 'Name', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Type', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Format', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Method', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Status', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Frequency', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Next run', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Action', 'woocommerce-exporter' ); ?></th>
		</tr>

	</thead>
	<tbody id="the-list">
		<tr>
			<td class="colspanchange" colspan="8"><?php _e( 'No scheduled exports found.', 'woocommerce-exporter' ); ?></td>
		</tr>

	</tbody>

</table>
<!-- .scheduled-exports -->

<?php if( !empty( $scheduled_exports ) ) { ?>
<p style="text-align:right;"><?php printf( __( '%d items', 'woocommerce-exporter' ), count( $scheduled_exports ) ); ?></p>
<?php } ?>

<hr />

<?php do_action( 'woo_ce_after_scheduled_exports' ); ?>