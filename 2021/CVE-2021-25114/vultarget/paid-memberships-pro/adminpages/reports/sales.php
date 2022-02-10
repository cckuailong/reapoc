<?php
/*
	PMPro Report
	Title: Sales
	Slug: sales

	For each report, add a line like:
	global $pmpro_reports;
	$pmpro_reports['slug'] = 'Title';

	For each report, also write two functions:
	* pmpro_report_{slug}_widget()   to show up on the report homepage.
	* pmpro_report_{slug}_page()     to show up when users click on the report page widget.
*/
global $pmpro_reports;
$gateway_environment = pmpro_getOption("gateway_environment");
if($gateway_environment == "sandbox")
	$pmpro_reports['sales'] = __('Sales and Revenue (Testing/Sandbox)', 'paid-memberships-pro' );
else
	$pmpro_reports['sales'] = __('Sales and Revenue', 'paid-memberships-pro' );

//queue Google Visualization JS on report page
function pmpro_report_sales_init()
{
	if ( is_admin() && isset( $_REQUEST['report'] ) && $_REQUEST[ 'report' ] == 'sales' && isset( $_REQUEST['page'] ) && $_REQUEST[ 'page' ] == 'pmpro-reports' ) {
		wp_enqueue_script( 'corechart', plugins_url( 'js/corechart.js',  plugin_dir_path( __DIR__ ) ) );
	}

}
add_action("init", "pmpro_report_sales_init");

//widget
function pmpro_report_sales_widget() {
	global $wpdb;
?>
<style>
	#pmpro_report_sales tbody td:last-child {text-align: right; }
</style>
<span id="pmpro_report_sales" class="pmpro_report-holder">
	<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th scope="col"><?php _e('Sales', 'paid-memberships-pro' ); ?></th>
			<th scope="col"><?php _e('Revenue', 'paid-memberships-pro' ); ?></th>
		</tr>
	</thead>
	<?php
		$reports = array(
			'today'      => __('Today', 'paid-memberships-pro' ),
			'this month' => __('This Month', 'paid-memberships-pro' ),
			'this year'  => __('This Year', 'paid-memberships-pro' ),
			'all time'   => __('All Time', 'paid-memberships-pro' ),
		);

	foreach ( $reports as $report_type => $report_name ) {
		//sale prices stats
		$count = 0;
		$max_prices_count = apply_filters( 'pmpro_admin_reports_max_sale_prices', 5 );
		$prices = pmpro_get_prices_paid( $report_type, $max_prices_count );	
		?>
		<tbody>
			<tr class="pmpro_report_tr">
				<th scope="row">
					<?php if( ! empty( $prices ) ) { ?>
						<button class="pmpro_report_th pmpro_report_th_closed"><?php echo esc_html($report_name); ?></button>
					<?php } else { ?>
						<?php echo esc_html($report_name); ?>
					<?php } ?>
				</th>
				<td><?php echo esc_html( number_format_i18n( pmpro_getSales( $report_type ) ) ); ?></td>
				<td><?php echo pmpro_escape_price( pmpro_formatPrice( pmpro_getRevenue( $report_type ) ) ); ?></td>
			</tr>
			<?php
				//sale prices stats
				$count = 0;
				$max_prices_count = apply_filters( 'pmpro_admin_reports_max_sale_prices', 5 );
				$prices = pmpro_get_prices_paid( $report_type, $max_prices_count );
				foreach ( $prices as $price => $quantity ) {
					if ( $count++ >= $max_prices_count ) {
						break;
					}
			?>
				<tr class="pmpro_report_tr_sub" style="display: none;">
					<th scope="row">- <?php echo pmpro_escape_price( pmpro_formatPrice( $price ) );?></th>
					<td><?php echo esc_html( number_format_i18n( $quantity ) ); ?></td>
					<td><?php echo pmpro_escape_price( pmpro_formatPrice( $price * $quantity ) ); ?></td>
				</tr>
			<?php
			}
			?>
		</tbody>
		<?php
	}
	?>
	</table>
	<?php if ( function_exists( 'pmpro_report_sales_page' ) ) { ?>
		<p class="pmpro_report-button">
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=sales' ) ); ?>"><?php _e('Details', 'paid-memberships-pro' );?></a>
		</p>
	<?php } ?>
</span>

<?php
}

function pmpro_report_sales_page()
{
	global $wpdb, $pmpro_currency_symbol, $pmpro_currency, $pmpro_currencies;

	//get values from form
	if(isset($_REQUEST['type']))
		$type = sanitize_text_field($_REQUEST['type']);
	else
		$type = "revenue";

	if($type == "sales")
		$type_function = "COUNT";
	else
		$type_function = "SUM";

	if(isset($_REQUEST['period']))
		$period = sanitize_text_field($_REQUEST['period']);
	else
		$period = "daily";

	if(isset($_REQUEST['month']))
		$month = intval($_REQUEST['month']);
	else
		$month = date_i18n("n", current_time('timestamp'));

	$thisyear = date_i18n("Y", current_time('timestamp'));
	if(isset($_REQUEST['year']))
		$year = intval($_REQUEST['year']);
	else
		$year = $thisyear;

	if(isset($_REQUEST['level']))
		$l = intval($_REQUEST['level']);
	else
		$l = "";

	if ( isset( $_REQUEST[ 'discount_code' ] ) ) {
		$discount_code = intval( $_REQUEST[ 'discount_code' ] );
	} else {
		$discount_code = '';
	}

	$currently_in_period = false;

	//calculate start date and how to group dates returned from DB
	if($period == "daily")
	{
		$startdate = $year . '-' . substr("0" . $month, strlen($month) - 1, 2) . '-01';
		$enddate = $year . '-' . substr("0" . $month, strlen($month) - 1, 2) . '-' . date_i18n('t', strtotime( $startdate ) );
		$date_function = 'DAY';
		$currently_in_period = ( intval( date( 'Y' ) ) == $year && intval( date( 'n' ) ) == $month );
	}
	elseif($period == "monthly")
	{
		$startdate = $year . '-01-01';
		$enddate = strval(intval($year)+1) . '-01-01';
		$date_function = 'MONTH';
		$currently_in_period = ( intval( date( 'Y' ) ) == $year );
	}
	else
	{
		$startdate = '1970-01-01';	//all time
		$date_function = 'YEAR';
		$currently_in_period = true;
	}

	//testing or live data
	$gateway_environment = pmpro_getOption("gateway_environment");

	// Get the estimated second offset to convert from GMT time to local.This is not perfect as daylight
	// savings time can come and go in the middle of a month, but it's a tradeoff that we are making
	// for performance so that we don't need to go through each order manually to calculate the local time.
	$tz_offset = strtotime( $startdate ) - strtotime( get_gmt_from_date( $startdate . " 00:00:00" ) );

	//get data
	$sqlQuery = "SELECT $date_function( DATE_ADD( o.timestamp, INTERVAL $tz_offset SECOND ) ) as date, $type_function(o.total) as value FROM $wpdb->pmpro_membership_orders o ";

	if ( ! empty( $discount_code ) ) {
		$sqlQuery .= "LEFT JOIN $wpdb->pmpro_discount_codes_uses dc ON o.id = dc.order_id ";
	}

	$sqlQuery .= "WHERE o.total > 0 AND o.timestamp >= DATE_ADD( '$startdate' , INTERVAL - $tz_offset SECOND ) AND o.status NOT IN('refunded', 'review', 'token', 'error') AND o.gateway_environment = '" . esc_sql( $gateway_environment ) . "' ";

	if(!empty($enddate))
		$sqlQuery .= "AND o.timestamp <= DATE_ADD( '$enddate 23:59:59' , INTERVAL - $tz_offset SECOND )";

	if(!empty($l))
		$sqlQuery .= "AND o.membership_id IN(" . esc_sql( $l ) . ") ";

	if ( ! empty( $discount_code ) ) {
		$sqlQuery .= "AND dc.code_id = '" . esc_sql( $discount_code ) . "' ";
	}

	$sqlQuery .= " GROUP BY date ORDER BY date ";

	$dates = $wpdb->get_results($sqlQuery);

	//fill in blanks in dates
	$cols = array();
	$total_in_period = 0;
	$units_in_period = 0; // Used for averages.
	
	if($period == "daily")
	{
		$lastday = date_i18n("t", strtotime($startdate, current_time("timestamp")));
		$day_of_month = intval( date( 'j' ) );
		
		for($i = 1; $i <= $lastday; $i++)
		{
			$cols[$i] = 0;
			if ( ! $currently_in_period || $i < $day_of_month ) {
				$units_in_period++;
			}
			
			foreach($dates as $date)
			{
				if($date->date == $i) {
					$cols[$i] = $date->value;
					if ( ! $currently_in_period || $i < $day_of_month ) {
						$total_in_period += $date->value;
					}
				}	
			}
		}
	}
	elseif($period == "monthly")
	{
		$month_of_year = intval( date( 'n' ) );
		for($i = 1; $i < 13; $i++)
		{
			$cols[$i] = 0;
			if ( ! $currently_in_period || $i < $month_of_year ) {
				$units_in_period++;
			}

			foreach($dates as $date)
			{
				if($date->date == $i) {
					$cols[$i] = $date->value;
					if ( ! $currently_in_period || $i < $month_of_year ) {
						$total_in_period += $date->value;
					}
				}
			}
		}
	}
	else //annual
	{
		//get min and max years
		$min = 9999;
		$max = 0;
		foreach($dates as $date)
		{
			$min = min($min, $date->date);
			$max = max($max, $date->date);
		}

		$current_year = intval( date( 'Y' ) );
		for($i = $min; $i <= $max; $i++)
		{
			if ( $i < $current_year ) {
				$units_in_period++;
			}
			foreach($dates as $date)
			{
				if($date->date == $i) {
					$cols[$i] = $date->value;
					if ( $i < $current_year ) {
						$total_in_period += $date->value;
					}
				}
			}
		}
	}
	
	$average = 0;
	if ( 0 !== $units_in_period ) {
		$average = $total_in_period / $units_in_period; // Not including this unit.
	}
	?>
	<form id="posts-filter" method="get" action="">
	<h1>
		<?php _e('Sales and Revenue', 'paid-memberships-pro' );?>
	</h1>

	<div class="tablenav top">
		<?php _e('Show', 'paid-memberships-pro' )?>
		<select id="period" name="period">
			<option value="daily" <?php selected($period, "daily");?>><?php _e('Daily', 'paid-memberships-pro' );?></option>
			<option value="monthly" <?php selected($period, "monthly");?>><?php _e('Monthly', 'paid-memberships-pro' );?></option>
			<option value="annual" <?php selected($period, "annual");?>><?php _e('Annual', 'paid-memberships-pro' );?></option>
		</select>
		<select name="type">
			<option value="revenue" <?php selected($type, "revenue");?>><?php _e('Revenue', 'paid-memberships-pro' );?></option>
			<option value="sales" <?php selected($type, "sales");?>><?php _e('Sales', 'paid-memberships-pro' );?></option>
		</select>
		<span id="for"><?php _e('for', 'paid-memberships-pro' )?></span>
		<select id="month" name="month">
			<?php for($i = 1; $i < 13; $i++) { ?>
				<option value="<?php echo esc_attr( $i );?>" <?php selected($month, $i);?>><?php echo esc_html(date_i18n("F", mktime(0, 0, 0, $i, 2)));?></option>
			<?php } ?>
		</select>
		<select id="year" name="year">
			<?php for($i = $thisyear; $i > 2007; $i--) { ?>
				<option value="<?php echo esc_attr( $i );?>" <?php selected($year, $i);?>><?php echo esc_html( $i );?></option>
			<?php } ?>
		</select>
		<span id="for"><?php _e('for', 'paid-memberships-pro' )?></span>
		<select id="level" name="level">
			<option value="" <?php if(!$l) { ?>selected="selected"<?php } ?>><?php _e('All Levels', 'paid-memberships-pro' );?></option>
			<?php
				$levels = $wpdb->get_results("SELECT id, name FROM $wpdb->pmpro_membership_levels ORDER BY name");
				$levels = pmpro_sort_levels_by_order( $levels );
				foreach($levels as $level)
				{
			?>
				<option value="<?php echo esc_attr( $level->id ); ?>" <?php if($l == $level->id) { ?>selected="selected"<?php } ?>><?php echo esc_html( $level->name); ?></option>
			<?php
				}
			?>
		</select>
		<?php
		$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->pmpro_discount_codes ";
		$sqlQuery .= "ORDER BY id DESC ";
		$codes = $wpdb->get_results($sqlQuery, OBJECT);
		if ( ! empty( $codes ) ) { ?>
		<select id="discount_code" name="discount_code">
			<option value="" <?php if ( empty( $discount_code ) ) { ?>selected="selected"<?php } ?>><?php _e('All Codes', 'paid-memberships-pro' );?></option>
			<?php foreach ( $codes as $code ) { ?>
				<option value="<?php echo esc_attr( $code->id ); ?>" <?php selected( $discount_code, $code->id ); ?>><?php echo esc_html( $code->code ); ?></option>
			<?php } ?>
		</select>
		<?php } ?>
		<input type="hidden" name="page" value="pmpro-reports" />
		<input type="hidden" name="report" value="sales" />
		<input type="submit" class="button action" value="<?php _e('Generate Report', 'paid-memberships-pro' );?>" />
	</div>
	<div id="chart_div" style="clear: both; width: 100%; height: 500px;"></div>
	<p>* <?php _e( 'Average line calculated using data prior to current day, month, or year.', 'paid-memberships-pro' ); ?></p>
	<script>
		//update month/year when period dropdown is changed
		jQuery(document).ready(function() {
			jQuery('#period').change(function() {
				pmpro_ShowMonthOrYear();
			});
		});

		function pmpro_ShowMonthOrYear()
		{
			var period = jQuery('#period').val();
			if(period == 'daily')
			{
				jQuery('#for').show();
				jQuery('#month').show();
				jQuery('#year').show();
			}
			else if(period == 'monthly')
			{
				jQuery('#for').show();
				jQuery('#month').hide();
				jQuery('#year').show();
			}
			else
			{
				jQuery('#for').hide();
				jQuery('#month').hide();
				jQuery('#year').hide();
			}
		}

		pmpro_ShowMonthOrYear();

		//draw the chart
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawVisualization);
		function drawVisualization() {

			var data = google.visualization.arrayToDataTable([
				[
					{ label: '<?php echo esc_html( $date_function );?>' },
					{ label: '<?php echo esc_html( ucwords( $type ) );?>' },
					{ label: '<?php _e( 'Average*', 'paid-memberships-pro' );?>' },
				],
				<?php foreach($cols as $date => $value) { ?>
					['<?php
						if($period == "monthly") {
							echo esc_html(date_i18n("M", mktime(0,0,0,$date,2)));
						} else {
						echo esc_html( $date );
					} ?>', <?php echo esc_html( pmpro_round_price( $value ) );?>, <?php echo esc_html( pmpro_round_price( $average ) );?>],
				<?php } ?>
			]);

			var options = {
				colors: ['<?php
					if ( $type === 'sales') {
						echo '#0099c6'; // Blue for "Sales" chart.
					} else {
						echo '#51a351'; // Green for "Revenue" chart.
					}
				?>'],
				chartArea: {width: '90%'},
				hAxis: {
					title: '<?php echo esc_html( $date_function );?>',
					textStyle: {color: '#555555', fontSize: '12', italic: false},
					titleTextStyle: {color: '#555555', fontSize: '20', bold: true, italic: false},
					maxAlternation: 1
				},
				vAxis: {
					<?php if ( $type === 'sales') { ?>
						format: '0',
					<?php } ?>
					textStyle: {color: '#555555', fontSize: '12', italic: false},
				},
				seriesType: 'bars',
				series: {1: {type: 'line', color: 'red'}},
				legend: {position: 'none'},
			};

			<?php
				if($type != "sales")
				{	
					$decimals = isset( $pmpro_currencies[ $pmpro_currency ]['decimals'] ) ? (int) $pmpro_currencies[ $pmpro_currency ]['decimals'] : 2;
					
					$decimal_separator = isset( $pmpro_currencies[ $pmpro_currency ]['decimal_separator'] ) ? $pmpro_currencies[ $pmpro_currency ]['decimal_separator'] : '.';
					
					$thousands_separator = isset( $pmpro_currencies[ $pmpro_currency ]['thousands_separator'] ) ? $pmpro_currencies[ $pmpro_currency ]['thousands_separator'] : ',';
					
					if ( pmpro_getCurrencyPosition() == 'right' ) {
						$position = "suffix";
					} else {
						$position = "prefix";
					}
					?>
					var formatter = new google.visualization.NumberFormat({
						<?php echo esc_html( $position );?>: '<?php echo esc_html( html_entity_decode($pmpro_currency_symbol) ); ?>',
						'decimalSymbol': '<?php echo esc_html( html_entity_decode( $decimal_separator ) ); ?>',
						'fractionDigits': <?php echo intval( $decimals ); ?>,
						'groupingSymbol': '<?php echo esc_html( html_entity_decode( $thousands_separator ) ); ?>',
					});
					formatter.format(data, 1);
					formatter.format(data, 2);
					<?php
				}
			?>

			var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
			chart.draw(data, options);
		}
	</script>

	</form>
	<?php
}

/*
	Other code required for your reports. This file is loaded every time WP loads with PMPro enabled.
*/

//get sales
function pmpro_getSales($period, $levels = NULL)
{
	//check for a transient
	$cache = get_transient( 'pmpro_report_sales' );
	if(!empty($cache) && !empty($cache[$period]) && !empty($cache[$period][$levels]))
		return $cache[$period][$levels];

	//a sale is an order with status NOT IN('refunded', 'review', 'token', 'error') with a total > 0
	if($period == "today")
		$startdate = date_i18n("Y-m-d", current_time('timestamp'));
	elseif($period == "this month")
		$startdate = date_i18n("Y-m", current_time('timestamp')) . "-01";
	elseif($period == "this year")
		$startdate = date_i18n("Y", current_time('timestamp')) . "-01-01";
	else
		$startdate = date_i18n("Y-m-d", 0);

	$gateway_environment = pmpro_getOption("gateway_environment");

	// Convert from local to UTC.
	$startdate = get_gmt_from_date( $startdate );

	//build query
	global $wpdb;
	$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE total > 0 AND status NOT IN('refunded', 'review', 'token', 'error') AND timestamp >= '" . esc_sql( $startdate ) . "' AND gateway_environment = '" . esc_sql( $gateway_environment ) . "' ";

	//restrict by level
	if(!empty($levels))
		$sqlQuery .= "AND membership_id IN(" . esc_sql( $levels ) . ") ";

	$sales = $wpdb->get_var($sqlQuery);

	//save in cache
	if(!empty($cache) && !empty($cache[$period]))
		$cache[$period][$levels] = $sales;
	elseif(!empty($cache))
		$cache[$period] = array($levels => $sales);
	else
		$cache = array($period => array($levels => $sales));

	set_transient( 'pmpro_report_sales', $cache, 3600*24 );

	return $sales;
}

/**
 * Gets an array of all prices paid in a time period
 *
 * @param  string $period time period to query.
 */
function pmpro_get_prices_paid( $period, $count = NULL ) {
	// Check for a transient.
	$cache = get_transient( 'pmpro_report_prices_paid' );
	if ( ! empty( $cache ) && ! empty( $cache[ $period . $count ] ) ) {
		return $cache[ $period . $count ];
	}

	// A sale is an order with status NOT IN('refunded', 'review', 'token', 'error') with a total > 0.
	if ( 'today' === $period ) {
		$startdate = date_i18n( 'Y-m-d', current_time( 'timestamp' ) );
	} elseif ( 'this month' === $period ) {
		$startdate = date_i18n( 'Y-m', current_time( 'timestamp' ) ) . '-01';
	} elseif ( 'this year' === $period ) {
		$startdate = date_i18n( 'Y', current_time( 'timestamp' ) ) . '-01-01';
	} else {
		$startdate = '1970-01-01';
	}

	// Convert from local to UTC.
	$startdate = get_gmt_from_date( $startdate );

	$gateway_environment = pmpro_getOption( 'gateway_environment' );

	// Build query.
	global $wpdb;
	$sql_query = "SELECT ROUND(total,8) as rtotal, COUNT(*) as num FROM $wpdb->pmpro_membership_orders WHERE total > 0 AND status NOT IN('refunded', 'review', 'token', 'error') AND timestamp >= '" . $startdate . "' AND gateway_environment = '" . esc_sql( $gateway_environment ) . "' ";

	// Restrict by level.
	if ( ! empty( $levels ) ) {
		$sql_query .= 'AND membership_id IN(' . $levels . ') ';
	}

	$sql_query .= ' GROUP BY rtotal ORDER BY num DESC ';

	$prices           = $wpdb->get_results( $sql_query );
	
	if( !empty( $count) ) {
		$prices = array_slice( $prices, 0, $count, true );
	}
	
	$prices_formatted = array();
	foreach ( $prices as $price ) {
		if ( isset( $price->rtotal ) ) {
			$sql_query                         = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE ROUND(total, 8) = '" . esc_sql( $price->rtotal ) . "' AND status NOT IN('refunded', 'review', 'token', 'error') AND timestamp >= '" . esc_sql( $startdate ) . "' AND gateway_environment = '" . esc_sql( $gateway_environment ) . "' ";
			$sales                             = $wpdb->get_var( $sql_query );
			$prices_formatted[ $price->rtotal ] = $sales;
		}
	}

	krsort( $prices_formatted );

	// Save in cache.
	if ( ! empty( $cache ) ) {
		$cache[ $period . $count ] = $prices_formatted;
	} else {
		$cache = array( $period . $count => $prices_formatted );
	}

	set_transient( 'pmpro_report_prices_paid', $cache, 3600 * 24 );

	return $prices_formatted;
}

//get revenue
function pmpro_getRevenue($period, $levels = NULL)
{
	//check for a transient
	$cache = get_transient("pmpro_report_revenue");
	if(!empty($cache) && !empty($cache[$period]) && !empty($cache[$period][$levels]))
		return $cache[$period][$levels];

	//a sale is an order with status NOT IN('refunded', 'review', 'token', 'error')
	if($period == "today")
		$startdate = date_i18n("Y-m-d", current_time('timestamp'));
	elseif($period == "this month")
		$startdate = date_i18n("Y-m", current_time('timestamp')) . "-01";
	elseif($period == "this year")
		$startdate = date_i18n("Y", current_time('timestamp')) . "-01-01";
	else
		$startdate = date_i18n("Y-m-d", 0);

	// Convert from local to UTC.
	$startdate = get_gmt_from_date( $startdate );

	$gateway_environment = pmpro_getOption("gateway_environment");

	//build query
	global $wpdb;
	$sqlQuery = "SELECT SUM(total) FROM $wpdb->pmpro_membership_orders WHERE status NOT IN('refunded', 'review', 'token', 'error') AND timestamp >= '" . esc_sql( $startdate ) . "' AND gateway_environment = '" . esc_sql( $gateway_environment ) . "' ";

	//restrict by level
	if(!empty($levels))
		$sqlQuery .= "AND membership_id IN(" . $levels . ") ";

	$revenue = $wpdb->get_var($sqlQuery);

	//save in cache
	if(!empty($cache) && !empty($cache[$period]))
		$cache[$period][$levels] = $revenue;
	elseif(!empty($cache))
		$cache[$period] = array($levels => $revenue);
	else
		$cache = array($period => array($levels => $revenue));

	set_transient("pmpro_report_revenue", $cache, 3600*24);

	return $revenue;
}

/**
 * Get revenue between dates.
 *
 * @param  string $start_date to track revenue from.
 * @param  string $end_date to track revenue until. Defaults to current date. YYYY-MM-DD format.
 * @param  array  $level_ids to include in report. Defaults to all.
 * @return float  revenue.
 */
function pmpro_get_revenue_between_dates( $start_date, $end_date = '', $level_ids = null ) {
	global $wpdb;
	$sql_query = "SELECT SUM(total) FROM $wpdb->pmpro_membership_orders WHERE status NOT IN('refunded', 'review', 'token', 'error') AND timestamp >= '" . esc_sql( $start_date ) . " 00:00:00'";
	if ( ! empty( $end_date ) ) {
		$sql_query .= " AND timestamp <= '" . esc_sql( $end_date ) . " 23:59:59'";
	}
	if ( ! empty( $level_ids ) ) {
		$sql_query .= ' AND membership_id IN(' . implode( ', ', $levels ) . ') ';
	}
	return $wpdb->get_var($sql_query);
}

//delete transients when an order goes through
function pmpro_report_sales_delete_transients()
{
	delete_transient( 'pmpro_report_sales' );
	delete_transient( 'pmpro_report_revenue' );
	delete_transient( 'pmpro_report_prices_paid' );
}
add_action("pmpro_after_checkout", "pmpro_report_sales_delete_transients");
add_action("pmpro_updated_order", "pmpro_report_sales_delete_transients");
