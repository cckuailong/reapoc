<?php if( $subscription_fields ) { ?>
		<div id="export-subscription" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Subscription Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $subscription ) { ?>
					<p class="description"><?php _e( 'Select the Subscription fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="subscription-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="subscription-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="subscription-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="subscription-fields" class="ui-sortable striped">

		<?php foreach( $subscription_fields as $field ) { ?>
						<tr id="subscription-<?php echo $field['reset']; ?>">
							<td>
								<label<?php if( isset( $field['hover'] ) ) { ?> title="<?php echo $field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="subscription_fields[<?php echo $field['name']; ?>]" class="subscription_field"<?php ( isset( $field['default'] ) ? checked( $field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<span class="field_title"><?php echo $field['label']; ?></span>
			<?php if( isset( $field['hover'] ) && apply_filters( 'woo_ce_export_fields_hover_label', true, 'subscription' ) ) { ?>
									<span class="field_hover"><?php echo $field['hover']; ?></span>
			<?php } ?>
									<input type="hidden" name="subscription_fields_order[<?php echo $field['name']; ?>]" class="field_order" value="<?php echo $field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Subscriptions', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Subscription field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Subscriptions were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-subscriptions-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Subscription Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_subscription_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_subscription_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_subscription_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-subscription -->

<?php } ?>