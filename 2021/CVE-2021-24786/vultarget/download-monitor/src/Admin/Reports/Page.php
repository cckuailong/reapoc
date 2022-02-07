<?php

class DLM_Reports_Page {

	/**
	 * Setup hooks
	 */
	public function setup() {

		// menu item
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 12 );

		// setup Reports AJAX calls
		$ajax = new DLM_Reports_Ajax();
		$ajax->setup();
	}

	/**
	 * Add settings menu item
	 */
	public function add_admin_menu() {
		// Settings page
		add_submenu_page( 'edit.php?post_type=dlm_download', __( 'Reports', 'download-monitor' ), __( 'Reports', 'download-monitor' ), 'dlm_view_reports', 'download-monitor-reports', array(
			$this,
			'view'
		) );
	}

	/**
	 * Get Reports page URL
	 *
	 * @return string
	 */
	public function get_url() {
		$date_range = $this->get_date_range();

		return add_query_arg( array(
			'tab'       => $this->get_current_tab(),
			'chart'     => $this->get_current_chart(),
			'period'    => $this->get_current_period(),
			'date_from' => $date_range['from'],
			'date_to'   => $date_range['to']
		), admin_url( 'edit.php?post_type=dlm_download&page=download-monitor-reports' ) );
	}

	/**
	 * Get date range for data
	 *
	 * @return array
	 */
	private function get_date_range() {

		$from = ( isset( $_GET['date_from'] ) ) ? $_GET['date_from'] : null;
		$to   = ( isset( $_GET['date_to'] ) ) ? $_GET['date_to'] : null;

		if ( null === $to ) {
			$to_date = new DateTime( current_time( "mysql" ) );
			$to_date->setTime( 0, 0, 0 )->modify( '-1 day' );
			$to = $to_date->format( 'Y-m-d' );
		} else {
			$to_date = new DateTime( $to );
			$to_date->setTime( 0, 0, 0 );
		}

		if ( null === $from ) {
			$from = $to_date->modify( '-1 month' )->format( 'Y-m-d' );

		}

		return array(
			'from' => $from,
			'to'   => $to
		);
	}

	/**
	 * Get current tab
	 *
	 * @return string
	 */
	private function get_current_tab() {
		return ( ! empty( $_GET['tab'] ) ) ? $_GET['tab'] : "totals";
	}

	/**
	 * Get current chart
	 *
	 * @return string
	 */
	private function get_current_chart() {
		return ( ! empty( $_GET['chart'] ) ) ? $_GET['chart'] : "line";
	}

	/**
	 * Get current period
	 *
	 * @return string
	 */
	private function get_current_period() {
		$current_period = ( ! empty( $_GET['period'] ) ) ? $_GET['period'] : "day";

		// add check to prevent crazy period modifiers via get
		if ( $current_period != 'month' ) {
			$current_period = 'day';
		}

		return $current_period;
	}

	/**
	 * Char button
	 */
	private function chart_button() {
		$other_chart = ( "line" == $this->get_current_chart() ) ? "bar" : "line";
		echo "<a title='" . sprintf( __( "Switch to %s", 'download-monitor' ), $other_chart ) . "' href='" . add_query_arg( array( 'chart' => $other_chart ), $this->get_url() ) . "' class='button dlm-reports-header-chart-switcher dlm-" . $other_chart . "'></a>";
	}

	/**
	 * Date range filter element
	 */
	private function date_range_button() {

		$date_range = $this->get_date_range();
		$start      = new DateTime( $date_range['from'] );
		$end        = new DateTime( $date_range['to'] );
		?>
        <div class="dlm-reports-header-date-selector" id="dlm-date-range-picker">
			<?php echo $start->format( "d M Y" ) . " - " . $end->format( "d M Y" ); ?>
            <span class="dlm-arrow"></span>
        </div>
		<?php
	}

	/**
	 * Period interval buttons
	 */
	private function period_interval_buttons() {
		$current = $this->get_current_period();
		echo "<div class='dlm-reports-header-period'>";
		echo "<a href='" . add_query_arg( array( 'period' => 'day' ), $this->get_url() ) . "' class='button" . ( ( 'day' === $current ) ? ' active' : '' ) . "'>" . __( 'Per Day', 'download-monitor' ) . "</a>";
		echo "<a href='" . add_query_arg( array( 'period' => 'month' ), $this->get_url() ) . "' class='button" . ( ( 'month' === $current ) ? ' active' : '' ) . "'>" . __( 'Month', 'download-monitor' ) . "</a>";
		echo "</div>";
	}

	private function generate_js_data() {
		$range = $this->get_date_range();

		return ' data-type="' . $this->get_current_chart() . '" data-period="' . $this->get_current_period() . '" data-from="' . $range['from'] . '" data-to="' . $range['to'] . '"';
	}

	/**
	 * Display page
	 */
	public function view() {

		/*
		 * We'll add more tabs / reports in future versions.
		 *
		$tabs = array(
			'totals'       => __( 'Totals', 'download-monitor' ),
			'per_download' => __( 'Per Download', 'download-monitor' ),
		);

		$current_tab = $this->get_current_tab();
		*/

		/** @var DLM_WordPress_Log_Item_Repository $repo */
//		$repo = download_monitor()->service( 'log_item_repository' );
//		$data = $repo->retrieve_grouped_count( $this->generate_data_filters(), $this->get_current_period() );

		$date_range = $this->get_date_range();

		$js_url = remove_query_arg( array( 'date_from', 'date_to' ), $this->get_url() );

		?>
        <div class="wrap dlm-reports">
            <div id="icon-edit" class="icon32 icon32-posts-dlm_download"><br/></div>

            <h1><?php
				_e( 'Download Reports', 'download-monitor' );
				echo '<div class="dlm-reports-actions">';
				$this->chart_button();
				$this->date_range_button();
				$this->period_interval_buttons();
				echo "</div>";
				?></h1>
            <br/>
			<?php
			/*
			 * We'll add more tabs / reports in future versions.
			 *
			?>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab_key => $tab ) {
					echo "<a href='" . add_query_arg( array( 'tab' => $tab_key ), $this->get_url() ) . "' class='nav-tab" . ( ( $tab_key === $current_tab ) ? " nav-tab-active" : "" ) . "'>" . $tab . "</a>";
				}
				?>
			</h2>
			*/
			?>

	        <?php do_action( 'dlm_reports_page_start' ); ?>

            <div class="dlm-reports-block dlm-reports-block-chart"
                 id="total_downloads_chart"<?php echo $this->generate_js_data(); ?>></div>

            <div class="dlm-reports-block dlm-reports-block-summary"
                 id="total_downloads_summary"<?php echo $this->generate_js_data(); ?>>
                <ul>
                    <li id="total"><label>Total Downloads</label><span>...</span></li>
                    <li id="average"><label>Daily Average Downloads</label><span>...</span></li>
                    <li id="popular"><label>Most Popular Download</label><span>...</span></li>
                </ul>
            </div>

            <div class="dlm-reports-block dlm-reports-block-table dlm-reports-block-half"
                 id="total_downloads_table"<?php echo $this->generate_js_data(); ?>>
                <span class="dlm-reports-placeholder-no-data">NO DATA</span>
            </div>

            <div class="dlm-reports-block dlm-reports-block-table dlm-reports-block-half-right"
                 id="total_downloads_browser_table"<?php echo $this->generate_js_data(); ?>>
                <span class="dlm-reports-placeholder-no-data">NO DATA</span>
            </div>

            <?php do_action( 'dlm_reports_page_end' ); ?>

            <script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( '#dlm-date-range-picker' ).dlm_reports_date_range( '<?php echo $date_range['from']; ?>', '<?php echo $date_range['to']; ?>', '<?php echo $js_url; ?>' );
				} );
            </script>
        </div>
		<?php
	}
}