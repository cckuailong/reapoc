<?php if( $product_fields ) { ?>
		<div id="export-product" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Product Fields', 'woocommerce-exporter' ); ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'fields', 'type' => 'product' ) ) ); ?>" style="float:right;"><?php _e( 'Configure', 'woocommerce-exporter' ); ?></a>
				</h3>
				<div class="inside">
	<?php if( $product ) { ?>
					<p class="description"><?php _e( 'Select the Product fields you would like to export, you can also drag-and-drop to reorder export fields. To the right you can change the label of export fields from the Configure link. Your field selection - but not filters - are saved for future exports.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="product-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="product-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="product-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="product-fields" class="ui-sortable striped">

		<?php foreach( $product_fields as $field ) { ?>
						<tr id="product-<?php echo $field['reset']; ?>">
							<td>
								<label<?php if( isset( $field['hover'] ) ) { ?> title="<?php echo $field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="product_fields[<?php echo $field['name']; ?>]" class="product_field"<?php ( isset( $field['default'] ) ? checked( $field['default'], 1 ) : '' ); ?><?php disabled( $field['disabled'], 1 ); ?> />
									<span class="field_title"><?php echo $field['label']; ?><?php if( $field['disabled'] ) { ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span><?php } ?></span>
			<?php if( isset( $field['hover'] ) && apply_filters( 'woo_ce_export_fields_hover_label', true, 'product' ) ) { ?>
									<span class="field_hover"><?php echo $field['hover']; ?></span>
			<?php } ?>
									<input type="hidden" name="product_fields_order[<?php echo $field['name']; ?>]" class="field_order" value="<?php echo $field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_product" value="<?php _e( 'Export Products', 'woocommerce-exporter' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Product field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
			</div>
			<!-- .postbox -->

			<div id="export-products-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Product Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_product_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_product_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_product_options_after_table' ); ?>

				</div>
				<!-- .inside -->

			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-product -->

<?php } ?>