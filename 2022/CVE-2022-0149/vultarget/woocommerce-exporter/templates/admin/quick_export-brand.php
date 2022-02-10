<?php if( $brand_fields ) { ?>
		<div id="export-brand" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Brand Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $brand ) { ?>
					<p class="description"><?php _e( 'Select the Brand fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="brand-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="brand-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="brand-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="brand-fields" class="ui-sortable striped">

		<?php foreach( $brand_fields as $field ) { ?>
						<tr id="brand-<?php echo $field['reset']; ?>">
							<td>
								<label<?php if( isset( $field['hover'] ) ) { ?> title="<?php echo $field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="brand_fields[<?php echo $field['name']; ?>]" class="brand_field"<?php ( isset( $field['default'] ) ? checked( $field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<span class="field_title"><?php echo $field['label']; ?></span>
			<?php if( isset( $field['hover'] ) && apply_filters( 'woo_ce_export_fields_hover_label', true, 'brand' ) ) { ?>
									<span class="field_hover"><?php echo $field['hover']; ?></span>
			<?php } ?>
									<input type="hidden" name="brand_fields_order[<?php echo $field['name']; ?>]" class="field_order" value="<?php echo $field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Brands', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Brand field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Brands were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-brands-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Brand Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_brand_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_brand_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_brand_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-brand -->

<?php } ?>