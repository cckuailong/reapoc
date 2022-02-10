<?php if( $commission_fields ) { ?>
		<div id="export-commission" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Commission Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $commission ) { ?>
					<p class="description"><?php _e( 'Select the Commission fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="commission-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="commission-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="commission-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="commission-fields" class="ui-sortable striped">

		<?php foreach( $commission_fields as $field ) { ?>
						<tr id="commission-<?php echo $field['reset']; ?>">
							<td>
								<label<?php if( isset( $field['hover'] ) ) { ?> title="<?php echo $field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="commission_fields[<?php echo $field['name']; ?>]" class="commission_field"<?php ( isset( $field['default'] ) ? checked( $field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<span class="field_title"><?php echo $field['label']; ?></span>
			<?php if( isset( $field['hover'] ) && apply_filters( 'woo_ce_export_fields_hover_label', true, 'commission' ) ) { ?>
									<span class="field_hover"><?php echo $field['hover']; ?></span>
			<?php } ?>
									<input type="hidden" name="commission_fields_order[<?php echo $field['name']; ?>]" class="field_order" value="<?php echo $field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Commissions', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Commission field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Commissions were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-commissions-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Commission Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_commission_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_commission_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_commission_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-commission -->

<?php } ?>