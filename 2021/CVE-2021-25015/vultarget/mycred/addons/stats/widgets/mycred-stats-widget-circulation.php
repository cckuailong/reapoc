<?php
if ( ! defined( 'myCRED_STATS_VERSION' ) ) exit;

/**
 * Stats Widget: Circulation
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Stats_Widget_Circulation' ) ) :
	class myCRED_Stats_Widget_Circulation extends myCRED_Statistics_Widget {

		/**
		 * Constructor
		 */
		function __construct( $widget_id = '', $args = array() ) {
			if ( $widget_id == '' )
				$widget_id = 'circulation';

			parent::__construct( $widget_id, $args );
			$this->dates = mycred_get_stat_dates( 'today_this' );
		}

		/**
		 * Get Data
		 * @version 1.0
		 */
		function get_data() {

			global $wpdb;

			if ( $this->args['ctypes'] == 'all' )
				$point_types = $this->ctypes;
			else
				$point_types = array( $this->args['ctypes'] => $this->ctypes[ $this->args['ctypes'] ] );

			$series = $totals = array();
			$all = 0;

			foreach ( $point_types as $type_id => $label ) {

				$mycred = mycred( $type_id );
				$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( meta_value ) FROM {$wpdb->usermeta} WHERE meta_key = %s;", $type_id ) );
				if ( $total === NULL ) $total = 0;

				$totals[] = '<strong>' . sprintf( __( 'Total %s:', 'mycred' ), $this->ctypes[ $type_id ] ) . '</strong> <span style="color:' . $this->colors[ $type_id ]['positive'] . '">' . $mycred->format_creds( $total ) . '</span>';
				$all = $all + $total;
				$series[] = "{ value: " . $total . ", color: '" . $this->colors[ $type_id ]['positive'] . "', highlight: '" . $this->colors[ $type_id ]['negative'] . "', label: '" . esc_attr( $label ) . "' }";

			}

			return array(
				'total'  => $all,
				'totals' => $totals,
				'series' => $series
			);

		}

		/**
		 * Get Gains and Loses
		 * @version 1.0
		 */
		function gains_and_loses() {

			global $wpdb;

			if ( $this->args['ctypes'] == 'all' )
				$point_types = $this->ctypes;
			else
				$point_types = array( $this->args['ctypes'] => $this->ctypes[ $this->args['ctypes'] ] );

			$series = array();
			foreach ( $point_types as $type_id => $label ) {

				if ( ! array_key_exists( $type_id, $series ) )
					$series[ $type_id ] = array();

				$values = array();
				foreach ( $this->dates as $date ) {

					if ( ! array_key_exists( $date['key'], $series[ $type_id ] ) )
						$series[ $type_id ][ $date['key'] ] = array(
							'gains' => 0,
							'loses' => 0
						);

					$query = $wpdb->get_col( $wpdb->prepare( "
						SELECT creds 
						FROM {$this->core->log_table} 
						WHERE ctype = %s 
						AND time BETWEEN %d AND %d;", $type_id, $date['from'], $date['until'] ) );

					if ( ! empty( $query ) ) {
						foreach ( $query as $entry ) {
							if ( $entry > 0 )
								$series[ $type_id ][ $date['key'] ]['gains'] = $series[ $type_id ][ $date['key'] ]['gains'] + $entry;
							else
								$series[ $type_id ][ $date['key'] ]['loses'] = $series[ $type_id ][ $date['key'] ]['loses'] + $entry;
						}
					}

				}

			}

			return $series;

		}

		/**
		 * Get Total
		 * @version 1.0
		 */
		function get_total( $ctype = '', $positive = true ) {

			global $wpdb;

			if ( $positive )
				$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( creds ) FROM {$this->core->log_table} WHERE ctype = %s AND creds > 0;", $ctype ) );
			else
				$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( creds ) FROM {$this->core->log_table} WHERE ctype = %s AND creds < 0;", $ctype ) );

			if ( $total === NULL )
				$total = 0;

			return $total;

		}

		/**
		 * Front Widget
		 * @version 1.0
		 */
		function front_widget() {

			if ( $this->args['ctypes'] == 'all' )
				$label = __( 'Total amount in circulation', 'mycred' );
			else
				$label = sprintf( __( 'Total amount of %s in circulation', 'mycred' ), $this->ctypes[ $this->args['ctypes'] ] );

			$circulation = $this->get_data();
			$gains_loses = $this->gains_and_loses();

?>
<div id="mycred-stats-overview" class="row">
	<div id="mycred-stats-<?php echo $this->id; ?>" class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
		<canvas id="total-circulation-<?php echo $this->id; ?>-chart"></canvas>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
		<h1><?php echo $label; ?>: <?php echo $this->format_number( $circulation['total'] ); ?></h1>
<?php

			if ( $this->args['ctypes'] == 'all' ) :

?>
		<ul id="total-per-point-type"><li><?php echo implode( '</li><li>', $circulation['totals'] ); ?></li></ul>
<?php

			else :

				$circulation = array( 'series' => array() );
				$mycred = mycred( $this->args['ctypes'] );

				$gains = $this->get_total( $this->args['ctypes'] );
				$loses = $this->get_total( $this->args['ctypes'], false );

				$total = $gains + abs( $loses );
				$gains_p = number_format( ( ( $gains / $total ) * 100 ), 0 );
				$gains_l = number_format( ( 100 - $gains_p ), 0 );

				$color = $gain_color = $this->colors[ $this->args['ctypes'] ]['positive'];
				$circulation['series'][] = "{ value: {$gains_p}, color: '" . $color . "', highlight: '" . $color . "', label: '" . esc_attr__( 'Total gains (%)', 'mycred' ) . "' }";

				$color = $lose_color = $this->colors[ $this->args['ctypes'] ]['positive'];
				$circulation['series'][] = "{ value: {$gains_l}, color: '" . $color . "', highlight: '" . $color . "', label: '" . esc_attr__( 'Total loses (%)', 'mycred' ) . "' }";

// 
?>
		<ul id="total-per-point-type"><li><?php printf( __( 'Total Gained: %s', 'mycred' ), '<span style="color:' . $gain_color . '">' . $mycred->format_creds( $gains ) . '</span>' ); ?></li><li><?php printf( __( 'Total Spent: %s', 'mycred' ), '<span style="color:' . $lose_color . '">' . $mycred->format_creds( $loses ) . '</span>' ); ?></li></ul>
<?php

			endif;

?>
		<div class="table-responsive mycred-stats-table">
			<table class="table table-condensed" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th class="rowtitle"></th>
						<th class="doublecell" colspan="2"><?php _e( 'Today', 'mycred' ); ?></th>
						<th class="doublecell" colspan="2"><?php _e( 'This Week', 'mycred' ); ?></th>
						<th class="doublecell" colspan="2"><?php _e( 'This Month', 'mycred' ); ?></th>
						<th class="doublecell" colspan="2"><?php _e( 'This Year', 'mycred' ); ?></th>
					</tr>
					<tr class="subheader">
						<th class="rowtitle"></th>
						<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
						<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
						<th class="cell last"><?php _e( 'Lost', 'mycred' ); ?></th>
					</tr>
				</thead>
				<tbody>
<?php

			foreach ( $gains_loses as $type_id => $dates ) {

				$mycred = mycred( $type_id );

?>
					<tr style="color: <?php echo $this->colors[ $type_id ]['positive']; ?>;">
						<td class="rowtitle"><?php echo $this->ctypes[ $type_id ]; ?></td>
<?php

				$page_id = MYCRED_SLUG;
				if ( $type_id != MYCRED_DEFAULT_TYPE_KEY )
					$page_id .= '_' . $type_id;

				$base_url = add_query_arg( array( 'page' => $page_id ), admin_url( 'admin.php' ) );
				foreach ( $dates as $key => $item ) {
					echo '<td class="cell">' . $mycred->format_number( $item['gains'] ) . '</td>';
					echo '<td class="cell">' . $mycred->format_number( $item['loses'] ) . '</td>';
				}

			}

?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($) {

	var <?php echo $this->id; ?> = $( '#total-circulation-<?php echo $this->id; ?>-chart' ).get(0).getContext( '2d' );
	<?php echo $this->id; ?>.canvas.width = 240;
	<?php echo $this->id; ?>.canvas.height = 240;

	var <?php echo $this->id; ?>chart = new Chart( <?php echo $this->id; ?> ).Doughnut([
		<?php echo implode( ',', $circulation['series'] ); ?>
	],{
		segmentStrokeColor : '#F1F1F1',
		segmentStrokeWidth : 3,
		percentageInnerCutout : 60
	});

});
</script>
<?php

		}

		/**
		 * Display
		 * @version 1.0.2
		 */
		function widget() {

			if ( $this->args['ctypes'] == 'all' )
				$label = __( 'Total amount in circulation', 'mycred' );
			else
				$label = sprintf( __( 'Total amount of %s in circulation', 'mycred' ), $this->ctypes[ $this->args['ctypes'] ] );

			$circulation = $this->get_data();
			$gains_loses = $this->gains_and_loses();

?>
<div id="mycred-stats-overview" class="clear clearfix">
	<div id="mycred-stats-<?php echo $this->id; ?>" class="left-column">
		<canvas id="total-circulation-<?php echo $this->id; ?>-chart"></canvas>
	</div>
	<h1><?php echo $label; ?>: <?php echo $this->format_number( $circulation['total'] ); ?></h1>
<?php

			if ( $this->args['ctypes'] == 'all' ) :

?>
	<ul id="total-per-point-type"><li><?php echo implode( '</li><li>', $circulation['totals'] ); ?></li></ul>
<?php

			else :

				$circulation = array( 'series' => array() );
				$mycred = mycred( $this->args['ctypes'] );

				$gains = $this->get_total( $this->args['ctypes'] );
				$loses = $this->get_total( $this->args['ctypes'], false );

				$total = $gains + abs( $loses );
				$gains_p = $gains_l = 0;
				if ( $total != 0 ) {
					$gains_p = number_format( ( ( $gains / $total ) * 100 ), 0 );
					$gains_l = number_format( ( 100 - $gains_p ), 0 );
				}

				$color = $gain_color = $this->colors[ $this->args['ctypes'] ]['positive'];
				$circulation['series'][] = "{ value: {$gains_p}, color: '" . $color . "', highlight: '" . $color . "', label: '" . esc_attr__( 'Total gains (%)', 'mycred' ) . "' }";

				
				$color = $lose_color = $this->colors[ $this->args['ctypes'] ]['negative'];
				$circulation['series'][] = "{ value: {$gains_l}, color: '" . $color . "', highlight: '" . $color . "', label: '" . esc_attr__( 'Total loses (%)', 'mycred' ) . "' }";

?>
	<ul id="total-per-point-type"><li><?php printf( __( 'Total Gained: %s', 'mycred' ), '<span style="color:' . $gain_color . '">' . $mycred->format_creds( $gains ) . '</span>' ); ?></li><li><?php printf( __( 'Total Spent: %s', 'mycred' ), '<span style="color:' . $lose_color . '">' . $mycred->format_creds( $loses ) . '</span>' ); ?></li></ul>
<?php

			endif;

?>
	<div class="table">
		<table cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th class="rowtitle"></th>
					<th class="doublecell" colspan="2"><?php _e( 'Today', 'mycred' ); ?></th>
					<th class="doublecell" colspan="2"><?php _e( 'This Week', 'mycred' ); ?></th>
					<th class="doublecell" colspan="2"><?php _e( 'This Month', 'mycred' ); ?></th>
					<th class="doublecell" colspan="2"><?php _e( 'This Year', 'mycred' ); ?></th>
				</tr>
				<tr class="subheader">
					<th class="rowtitle"></th>
					<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Lost', 'mycred' ); ?></th>
					<th class="cell"><?php _e( 'Gained', 'mycred' ); ?></th>
					<th class="cell last"><?php _e( 'Lost', 'mycred' ); ?></th>
				</tr>
			</thead>
			<tbody>
<?php

			foreach ( $gains_loses as $type_id => $dates ) {

				$type_id = str_replace( 'view_', '', $type_id );
				$mycred  = mycred( $type_id );

?>
				<tr style="color: <?php echo $this->colors[ $type_id ]['positive']; ?>;">
					<td class="rowtitle"><?php echo $this->ctypes[ $type_id ]; ?></td>
<?php

				$page_id = MYCRED_SLUG;
				if ( $type_id != MYCRED_DEFAULT_TYPE_KEY )
					$page_id .= '_' . $type_id;

				$base_url = add_query_arg( array( 'page' => $page_id ), admin_url( 'admin.php' ) );
				foreach ( $dates as $key => $item ) {
					$url = add_query_arg( array( 'show' => $key ), $base_url );
					echo '<td class="cell"><a href="' . esc_url( $url ) . '">' . $mycred->format_number( $item['gains'] ) . '</a></td>';
					$url = add_query_arg( array( 'show' => $key, 'num' => 0, 'compare' => urlencode( '<' ) ), $base_url );
					echo '<td class="cell"><a href="' . esc_url( $url ) . '">' . $mycred->format_number( $item['loses'] ) . '</a></td>';
				}

			}

?>
			</tbody>
		</table>
		<?php echo $this->action_buttons(); ?>
	</div>
	<div class="clear clearfix"></div>
</div>
<script type="text/javascript">
jQuery(function($) {

	var <?php echo $this->id; ?> = $( '#total-circulation-<?php echo $this->id; ?>-chart' ).get(0).getContext( '2d' );
	<?php echo $this->id; ?>.canvas.width = 240;
	<?php echo $this->id; ?>.canvas.height = 240;

	var <?php echo $this->id; ?>chart = new Chart( <?php echo $this->id; ?> ).Doughnut([
		<?php echo implode( ',', $circulation['series'] ); ?>
	],{
		segmentStrokeColor : '#F1F1F1',
		segmentStrokeWidth : 3,
		percentageInnerCutout : 60
	});

});
</script>
<?php

		}

	}
endif;
