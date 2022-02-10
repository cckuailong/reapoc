<?php
// HTML template for Filter Commissions by Commission Date widget on Store Exporter screen
function woo_ce_commissions_filter_by_date() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$today = date( 'l' );
	$yesterday = date( 'l', strtotime( '-1 days' ) );
	$current_month = date( 'F' );
	$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
	$commission_dates_variable = '';
	$commission_dates_variable_length = '';
	$commission_dates_from = woo_ce_get_commission_first_date();
	$commission_dates_to = date( 'd/m/Y' );

	ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-date" /> <?php _e( 'Filter Commissions by Commission Date', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="today" disabled="disabled" /> <?php _e( 'Today', 'woocommerce-exporter' ); ?> (<?php echo $today; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="yesterday" disabled="disabled" /> <?php _e( 'Yesterday', 'woocommerce-exporter' ); ?> (<?php echo $yesterday; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_week" disabled="disabled" /> <?php _e( 'Current week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_week" disabled="disabled" /> <?php _e( 'Last week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_month" disabled="disabled" /> <?php _e( 'Current month', 'woocommerce-exporter' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_month" disabled="disabled" /> <?php _e( 'Last month', 'woocommerce-exporter' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
<!--
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_quarter" disabled="disabled" /> <?php _e( 'Last quarter', 'woocommerce-exporter' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="commission_dates_filter" value="variable" disabled="disabled" /> <?php _e( 'Variable date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<?php _e( 'Last', 'woocommerce-exporter' ); ?>
				<input type="text" name="commission_dates_filter_variable" class="text code" size="4" maxlength="4" value="<?php echo $commission_dates_variable; ?>" disabled="disabled" />
				<select name="commission_dates_filter_variable_length" style="vertical-align:top;">
					<option value=""<?php selected( $commission_dates_variable_length, '' ); ?>>&nbsp;</option>
					<option value="second"<?php selected( $commission_dates_variable_length, 'second' ); ?> disabled="disabled"><?php _e( 'second(s)', 'woocommerce-exporter' ); ?></option>
					<option value="minute"<?php selected( $commission_dates_variable_length, 'minute' ); ?> disabled="disabled"><?php _e( 'minute(s)', 'woocommerce-exporter' ); ?></option>
					<option value="hour"<?php selected( $commission_dates_variable_length, 'hour' ); ?> disabled="disabled"><?php _e( 'hour(s)', 'woocommerce-exporter' ); ?></option>
					<option value="day"<?php selected( $commission_dates_variable_length, 'day' ); ?> disabled="disabled"><?php _e( 'day(s)', 'woocommerce-exporter' ); ?></option>
					<option value="week"<?php selected( $commission_dates_variable_length, 'week' ); ?> disabled="disabled"><?php _e( 'week(s)', 'woocommerce-exporter' ); ?></option>
					<option value="month"<?php selected( $commission_dates_variable_length, 'month' ); ?> disabled="disabled"><?php _e( 'month(s)', 'woocommerce-exporter' ); ?></option>
					<option value="year"<?php selected( $commission_dates_variable_length, 'year' ); ?> disabled="disabled"><?php _e( 'year(s)', 'woocommerce-exporter' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="manual" disabled="disabled" /> <?php _e( 'Fixed date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="commission_dates_from" name="commission_dates_from" value="<?php echo esc_attr( $commission_dates_from ); ?>" class="text code datepicker" disabled="disabled" /> to <input type="text" size="10" maxlength="10" id="commission_dates_to" name="commission_dates_to" value="<?php echo esc_attr( $commission_dates_to ); ?>" class="text code datepicker" disabled="disabled" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first Commission to today.', 'woocommerce-exporter' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-commissions-filters-date -->
<?php
	ob_end_flush();

}