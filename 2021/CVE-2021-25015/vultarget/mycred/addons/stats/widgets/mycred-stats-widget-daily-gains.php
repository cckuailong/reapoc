<?php
if ( ! defined( 'myCRED_STATS_VERSION' ) ) exit;

/**
 * Stats Widget: 
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Stats_Widget_Daily_Gains' ) ) :
	class myCRED_Stats_Widget_Daily_Gains extends myCRED_Statistics_Widget {

		/**
		 * Constructor
		 */
		function __construct( $widget_id = '', $args = array() ) {
			if ( $widget_id == '' )
				$widget_id = 'dailygains';

			parent::__construct( $widget_id, $args );
			$this->dates = mycred_get_stat_dates( 'x_dates', $this->args['span'] );
		}

		/**
		 * Get Data
		 * @version 1.0
		 */
		function get_data() {

			global $wpdb;

			$ctype_limit = '';
			if ( $this->args['ctypes'] != 'all' )
				$ctype_limit = $wpdb->prepare( "AND ctype = %s", $this->args['ctypes'] );

			return $wpdb->get_results( $wpdb->prepare( "
				SELECT ref, COUNT(*) AS count, SUM( creds ) AS total, ctype AS type 
				FROM {$this->core->log_table} 
				WHERE creds > 0 
				AND time BETWEEN %d AND %d
				{$ctype_limit}
				GROUP BY ref ORDER BY total DESC LIMIT 0,%d;", strtotime( '-' . $this->args['span'] . ' days midnight', $this->now ), $this->now, $this->args['number'] ) );

		}

		/**
		 * Get Gains
		 * @version 1.0
		 */
		function get_gains() {

			global $wpdb;

			if ( $this->args['ctypes'] == 'all' )
				$point_types = $this->ctypes;
			else
				$point_types = array( $this->args['ctypes'] => $this->ctypes[ $this->args['ctypes'] ] );

			$series = $ctypes = $categories = array();
			$num = 0;
			
			
			if ( count( $point_types ) > 0 ) {

				foreach ( $point_types as $type_id => $label ) {

					$num ++;

					$count = $wpdb->get_var( "SELECT COUNT( * ) FROM {$this->core->log_table} WHERE ctype = '{$type_id}';" );
					if ( $count === NULL )
						$count = $num;

					$ctypes[ $count ] = $type_id;

				}

				ksort( $ctypes, SORT_NUMERIC );

			}

			foreach ( $ctypes as $count => $type_id ) {

				$values = array();
				foreach ( $this->dates as $date ) {

					$query = $wpdb->get_var( $wpdb->prepare( "
						SELECT SUM( creds ) 
						FROM {$this->core->log_table} 
						WHERE creds > 0 
						AND ctype = %s 
						AND time BETWEEN %d AND %d;", $type_id, $date['from'], $date['until'] ) );

					if ( $query === NULL ) $query = 0;

					$values[] = abs( $query );
				
					if ( ! in_array( $date['label'], $categories ) )
						$categories[] = esc_attr( $date['label'] );

				}

				$mycred = mycred( $type_id );
				$series[] = "{ label : '" . esc_js( $mycred->plural() ) . "', fillColor : '" . str_replace( ',1)', ',0.3)', $this->colors[ $type_id ]['positive'] ) . "', strokeColor : '" . $this->colors[ $type_id ]['positive'] . "', pointColor : '" . $this->colors[ $type_id ]['positive'] . "', data : [" . implode( ', ', $values ) . "] }";

			}

			return array(
				'categories' => $categories,
				'series'     => $series
			);

		}

		/**
		 * Display
		 * @version 1.0.1
		 */
		function widget() {

			$label = __( 'Most common ways your users have gained points during this period.', 'mycred' );
			if ( $this->args['ctypes'] != 'all' )
				$label = $this->core->template_tags_general( __( 'Most common ways your users have gained %_plural% during this period.', 'mycred' ) );

			$ten_day_gain = $this->get_data();
			$earned = $this->get_gains();

?>
<div id="" class="clear clearfix">
	<h1><?php _e( 'Gains in the last 10 days', 'mycred' ); ?></h1>
	<p><span class="description"><?php echo $label; ?></span></p>
<?php

			if ( ! empty( $ten_day_gain ) ) {

				echo '<div class="mycred-popular-items"><ol>';
				foreach ( $ten_day_gain as $item ) {

					if ( isset( $refs[ $item->ref ] ) )
						$label = $refs[ $item->ref ];
					else
						$label = ucfirst( str_replace( '_', ' ', $item->ref ) );

					$page_id = MYCRED_SLUG;
					if ( $item->type != MYCRED_DEFAULT_TYPE_KEY )
						$page_id .= '_' . $item->type;

					$base_url = admin_url( 'admin.php' );
					$url = add_query_arg( array( 'ref' => $item->ref, 'page' => $page_id ), $base_url );

?>
<li>
	<strong style="color:<?php echo $this->colors[ $item->type ]['positive']; ?>;"><?php echo $label; ?></strong>
	<span class="view"><a href="<?php echo esc_url( $url ); ?>"><?php _e( 'View', 'mycred' ); ?></a></span>
	<ul>
		<li><?php echo number_format( $item->total, 0, '.', ' ' ); ?></li>
		<li><?php echo $item->count; ?></li>
	</ul>
	<div class="clear clearfix"></div>
</li>
<?php

				}

				echo '</ol></div><div class="last-ten-days-chart"><canvas id="daily-gain-' . $this->id . '-chart"></canvas><div id="' . $this->id . '-legend" class="mycred-chart-legend clear clearfix"></div></div>';

			}
			else {
				echo '<div class="mycred-empty-widget"><p>' . __( 'No data found', 'mycred' ) . '</p></div>';
			}

?>
	<div class="clear clearfix"></div>
</div>
<?php

			if ( ! empty( $ten_day_gain ) ) :

?>
<script type="text/javascript">
jQuery(function($) {

	var <?php echo $this->id; ?> = $( '#daily-gain-<?php echo $this->id; ?>-chart' ).get(0).getContext( '2d' );

	<?php echo $this->id; ?>.canvas.height = 400;
	var <?php echo $this->id; ?>chart = new Chart( <?php echo $this->id; ?> ).Line({
		labels   : [<?php echo "'" . implode( "', '", $earned['categories'] ) . "'"; ?>],
		datasets : [<?php echo implode( ',', $earned['series'] ); ?>]
	},{
		bezierCurve: false,
		responsive: true,
    	maintainAspectRatio: false
	});

	var <?php echo $this->id; ?>legend = <?php echo $this->id; ?>chart.generateLegend();
	$( '#<?php echo $this->id; ?>-legend' ).append( <?php echo $this->id; ?>legend );

});
</script>
<?php

			endif;

		}

	}
endif;
