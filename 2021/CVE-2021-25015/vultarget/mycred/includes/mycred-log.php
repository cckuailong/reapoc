<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Query Log
 * @see http://codex.mycred.me/classes/mycred_query_log/ 
 * @since 0.1
 * @version 1.4.1
 */
if ( ! class_exists( 'myCRED_Query_Log' ) ) :
	class myCRED_Query_Log {

		public $args;
		public $request;
		public $prep;
		public $num_rows;
		public $max_num_pages;
		public $total_rows;
		
		public $results;
		
		public $headers;
		public $core;

		/**
		 * Construct
		 */
		public function __construct( $args = array(), $array = false ) {
			if ( empty( $args ) ) return false;

			global $wpdb;

			$select = $where = $sortby = $limits = '';
			$prep = $wheres = array();

			// Load General Settings
			if ( isset( $args['ctype'] ) )
				$type = $args['ctype'];
			else
				$type = 'mycred_default';

			$this->core = mycred( $type );
			if ( $this->core->format['decimals'] > 0 )
				$format = '%f';
			else
				$format = '%d';

			// Prep Defaults
			$defaults = array(
				'user_id'  => NULL,
				'ctype'    => 'mycred_default',
				'number'   => 25,
				'time'     => NULL,
				'ref'      => NULL,
				'ref_id'   => NULL,
				'amount'   => NULL,
				's'        => NULL,
				'data'     => NULL,
				'orderby'  => 'time',
				'offset'   => '',
				'order'    => 'DESC',
				'ids'      => false,
				'cache'    => '',
				'paged'    => $this->get_pagenum()
			);
			$this->args = mycred_apply_defaults( $defaults, $args );
			
			// Difference between default and given args
			$this->diff = array_diff_assoc( $this->args, $defaults );
			if ( isset( $this->diff['number'] ) )
				unset( $this->diff['number'] );

			$data = false;
			if ( $this->args['cache'] != '' ) {
				$cache_id = substr( $this->args['cache'], 0, 23 );
				if ( is_multisite() )
					$data = get_site_transient( 'mycred_log_query_' . $cache_id );
				else
					$data = get_transient( 'mycred_log_query_' . $cache_id );
			}
			if ( $data === false ) {
				
				// Type
				$wheres[] = 'ctype = %s';
				$prep[] = $this->args['ctype'];

				// User ID
				if ( $this->args['user_id'] !== NULL && $this->args['user_id'] != '' ) {
					$wheres[] = 'user_id = %d';
					$prep[] = abs( $this->args['user_id'] );
				}

				// Reference
				if ( $this->args['ref'] !== NULL && $this->args['ref'] != '' ) {
					$refs = explode( ',', $this->args['ref'] );
					$ref_count = count( $refs );
					if ( $ref_count > 1 ) {
						$ref_count = $ref_count-1;
						$wheres[] = 'ref IN (%s' . str_repeat( ',%s', $ref_count ) . ')';
						foreach ( $refs as $ref )
							$prep[] = sanitize_text_field( $ref );
					}
					else {
						$wheres[] = 'ref = %s';
						$prep[] = sanitize_text_field( $refs[0] );
					}
				}

				// Reference ID
				if ( $this->args['ref_id'] !== NULL && $this->args['ref_id'] != '' ) {
					$ref_ids = explode( ',', $this->args['ref_id'] );
					if ( count( $ref_ids ) > 1 ) {
						$ref_id_count = count( $ref_ids )-1;
						$wheres[] = 'ref_id IN (%d' . str_repeat( ',%d', $ref_id_count ) . ')';
						foreach ( $ref_ids as $ref_id )
							$prep[] = (int) sanitize_text_field( $ref_id );
					}
					else {
						$wheres[] = 'ref_id = %d';
						$prep[] = (int) sanitize_text_field( $ref_ids[0] );
					}
				}

				// Amount
				if ( $this->args['amount'] !== NULL && $this->args['amount'] != '' ) {
					// Advanced query
					if ( is_array( $this->args['amount'] ) ) {
						// Range
						if ( isset( $this->args['amount']['start'] ) && isset( $this->args['amount']['end'] ) ) {
							$wheres[] = 'creds BETWEEN ' . $format . ' AND ' . $format;
							$prep[] = $this->core->number( sanitize_text_field( $this->args['amount']['start'] ) );
							$prep[] = $this->core->number( sanitize_text_field( $this->args['amount']['end'] ) );
						}
						// Compare
						elseif ( isset( $this->args['amount']['num'] ) && isset( $this->args['amount']['compare'] ) ) {
							$compare = urldecode( $this->args['amount']['compare'] );
							$wheres[] = 'creds ' . trim( $compare ) . ' ' . $format;
							$prep[] = $this->core->number( sanitize_text_field( $this->args['amount']['num'] ) );
						}
					}
					// Specific amount(s)
					else {
						$amounts = explode( ',', $this->args['amount'] );
						$amount_count = count( $amounts );
						if ( $amount_count > 1 ) {
							$amount_count = $amount_count-1;
							$wheres[] = 'amount IN (' . $format . str_repeat( ',' . $format, $ref_id_count ) . ')';
							foreach ( $amount_count as $amount )
								$prep[] = $this->core->number( sanitize_text_field( $amount ) );
						}
						else {
							$wheres[] = 'creds = ' . $format;
							$prep[] = $this->core->number( sanitize_text_field( $amounts[0] ) );
						}
					}
				}

				// Time
				if ( $this->args['time'] !== NULL && $this->args['time'] != '' ) {
					$now = date_i18n( 'U' );
					$today = strtotime( date_i18n( 'Y/m/d' ) . ' midnight' );
					$todays_date = date_i18n( 'd' );

					// Show todays entries
					if ( $this->args['time'] == 'today' ) {
						$wheres[] = "time BETWEEN $today AND $now";
					}
					// Show yesterdays entries
					elseif ( $this->args['time'] == 'yesterday' ) {
						$yesterday = strtotime( '-1 day midnight' );
						$wheres[] = "time BETWEEN $yesterday AND $today";
					}
					// Show this weeks entries
					elseif ( $this->args['time'] == 'thisweek' ) {
						$weekday = date_i18n( 'w' );
						// New week started today so show only todays
						if ( get_option( 'start_of_week' ) == $weekday ) {
							$wheres[] = "time BETWEEN $today AND $now";
						}
						// Show rest of this week
						else {
							$week_start = strtotime( '-' . ( $weekday+1 ) . ' days midnight' );
							$wheres[] = "time BETWEEN $week_start AND $now";
						}
					}
					// Show this months entries
					elseif ( $this->args['time'] == 'thismonth' ) {
						$start_of_month = strtotime( date_i18n( 'Y/m/01' ) . ' midnight' );
						$wheres[] = "time BETWEEN $start_of_month AND $now";
					}
					else {
						$times = explode( ',', $this->args['time'] );
						if ( count( $times ) == 2 ) {
							$from = sanitize_key( $times[0] );
							$to = sanitize_key( $times[1] );
							$wheres[] = "time BETWEEN $from AND $to";
						}
					}
				}

				// Entry Search
				if ( $this->args['s'] !== NULL && $this->args['s'] != '' ) {
					$search_query = sanitize_text_field( $this->args['s'] );

					if ( is_int( $search_query ) )
						$search_query = (string) $search_query;

					$wheres[] = "entry LIKE %s";
					$prep[] = "%$search_query%";
				}

				// Data
				if ( $this->args['data'] !== NULL && $this->args['data'] != '' ) {
					$data_query = sanitize_text_field( $this->args['data'] );

					if ( is_int( $data_query ) )
						$data_query = (string) $data_query;

					$wheres[] = "data LIKE %s";
					$prep[] = $data_query;
				}

				// Order by
				if ( $this->args['orderby'] != '' ) {
					// Make sure $sortby is valid
					$sortbys = array( 'id', 'ref', 'ref_id', 'user_id', 'creds', 'ctype', 'entry', 'data', 'time' );
					$allowed = apply_filters( 'mycred_allowed_sortby', $sortbys );
					if ( in_array( $this->args['orderby'], $allowed ) ) {
						$sortby = "ORDER BY " . $this->args['orderby'] . " " . $this->args['order'];
					}
				}

				// Number of results
				$number = $this->args['number'];
				if ( $number < -1 )
					$number = abs( $number );
				elseif ( $number == 0 || $number == -1 )
					$number = NULL;

				// Limits
				if ( $number !== NULL ) {
					$page = 1;
					if ( $this->args['paged'] !== NULL ) {
						$page = absint( $this->args['paged'] );
						if ( ! $page )
							$page = 1;
					}

					if ( $this->args['offset'] != '' ) {
						$pgstrt = ($page - 1) * $number . ', ';
					}
					else {
						$offset = absint( $this->args['offset'] );
						$pgstrt = $offset . ', ';
					}

					$limits = 'LIMIT ' . $pgstrt . $number;
				}
				else {
					$limits = '';
				}

				// Prep return
				if ( $this->args['ids'] === true )
					$select = 'id';
				else
					$select = '*';
				
				$found_rows = '';
				if ( $limits != '' )
					$found_rows = 'SQL_CALC_FOUND_ROWS';

				// Filter
				$select = apply_filters( 'mycred_query_log_select', $select, $this->args, $this->core );
				$sortby = apply_filters( 'mycred_query_log_sortby', $sortby, $this->args, $this->core );
				$limits = apply_filters( 'mycred_query_log_limits', $limits, $this->args, $this->core );
				$wheres = apply_filters( 'mycred_query_log_wheres', $wheres, $this->args, $this->core );

				$prep = apply_filters( 'mycred_query_log_prep', $prep, $this->args, $this->core );

				$where = 'WHERE ' . implode( ' AND ', $wheres );

				// Run
				$this->request = $wpdb->prepare( "SELECT {$found_rows} {$select} FROM {$this->core->log_table} {$where} {$sortby} {$limits}", $prep );
				$this->prep = $prep;
				
				$this->results = $wpdb->get_results( $this->request, $array ? ARRAY_A : OBJECT );
				
				if ( $limits != '' )
					$this->num_rows = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
				else
					$this->num_rows = count( $this->results );

				if ( $limits != '' )
					$this->max_num_pages = ceil( $this->num_rows / $number );

				if ( $this->args['cache'] != '' ) {
					if ( is_multisite() )
						set_site_transient( 'mycred_log_query_' . $cache_id, $this->results, DAY_IN_SECONDS * 1 );
					else
						set_transient( 'mycred_log_query_' . $cache_id, $this->results, DAY_IN_SECONDS * 1 );
				}
				
				$this->total_rows = $wpdb->get_var( "SELECT COUNT( * ) FROM {$this->core->log_table}" );
			}

			// Return the transient
			else {
				$this->request = 'transient';
				$this->results = $data;
				$this->prep = '';
				
				$this->num_rows = count( $data );
			}

			$this->headers = $this->table_headers();
		}

		/**
		 * Has Entries
		 * @returns true or false
		 * @since 0.1
		 * @version 1.0
		 */
		public function have_entries() {
			if ( ! empty( $this->results ) ) return true;
			return false;
		}

		/**
		 * Table Nav
		 * @since 0.1
		 * @version 1.1
		 */
		public function table_nav( $location = 'top', $is_profile = false ) {
			if ( $location == 'top' ) {

				$this->filter_options( $is_profile );
				$this->navigation( $location );

			}
			else {

				$this->navigation( $location );

			}
		}

		/**
		 * Item Count
		 * @since 0.1
		 * @version 1.1
		 */
		public function navigation( $location = 'top', $id = '' ) { ?>

<div class="tablenav-pages<?php if ( $this->max_num_pages == 1 ) echo ' one-page'; ?>">
	<?php $this->pagination( $location, $id ); ?>

</div>
<?php
		}

		/**
		 * Get Page Number
		 * @since 1.4
		 * @version 1.0
		 */
		public function get_pagenum() {
			global $paged;
			
			if ( $paged > 0 )
				$pagenum = absint( $paged );

			elseif ( isset( $_REQUEST['paged'] ) )
				$pagenum = absint( $_REQUEST['paged'] );

			else return 1;

			return max( 1, $pagenum );
		}

		/**
		 * Pagination
		 * @since 1.4
		 * @version 1.0
		 */
		public function pagination( $location = 'top', $id = '' ) {
			$output = '';
			$total_pages = $this->max_num_pages;
			$current = $this->get_pagenum();
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $id );
			if ( ! is_admin() )
				$current_url = str_replace( '/page/' . $current . '/', '/', $current_url );
			$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

			if ( $this->have_entries() ) {
				$total_number = count( $this->results );
				$output = '<span class="displaying-num">' . sprintf( __( 'Showing %d %s', 'mycred' ), $total_number, _n( 'entry', 'entries', $total_number, 'mycred' ) ) . '</span>';
			}

			$page_links = array();

			$disable_first = $disable_last = '';
			if ( $current == 1 )
				$disable_first = ' disabled';
			if ( $current == $total_pages )
				$disable_last = ' disabled';

			$page_links[] = sprintf( '<a class="%s" title="%s" href="%s">%s</a>',
				'btn btn-default btn-sm first-page' . $disable_first,
				esc_attr__( 'Go to the first page' ),
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				'&laquo;'
			);

			$page_links[] = sprintf( '<a class="%s" title="%s" href="%s">%s</a>',
				'btn btn-default btn-sm prev-page' . $disable_first,
				esc_attr__( 'Go to the previous page' ),
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				'&lsaquo;'
			);

			if ( 'bottom' == $location )
				$html_current_page = $current;
			else
				$html_current_page = sprintf( '<input class="current-page btn btn-sm" title="%s" type="text" name="paged" value="%s" size="%d" />',
					esc_attr__( 'Current page' ),
					$current,
					strlen( $total_pages )
				);

			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

			$page_links[] = sprintf( '<a class="%s"  title="%s" href="%s">%s</a>',
				'btn btn-default btn-sm next-page' . $disable_last,
				esc_attr__( 'Go to the next page' ),
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				'&rsaquo;'
			);

			$page_links[] = sprintf( '<a class="%s" title="%s" href="%s">%s</a>',
				'btn btn-default btn-sm last-page' . $disable_last,
				esc_attr__( 'Go to the last page' ),
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				'&raquo;'
			);

			$pagination_links_class = 'pagination-links';
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

			if ( $total_pages )
				$page_class = $total_pages < 2 ? ' one-page' : '';
			else
				$page_class = ' no-pages';

			echo "<div class='tablenav-pages{$page_class}'>$output</div>";
		}

		/**
		 * Get References
		 * Returns all available references in the database.
		 * @since 0.1
		 * @version 1.1
		 */
		public function get_refs( $req = array() ) {
			$refs = mycred_get_used_references( $this->args['ctype'] );

			foreach ( $refs as $i => $ref ) {
				if ( ! empty( $req ) && ! in_array( $ref, $req ) )
					unset( $refs[ $i ] );
			}
			$refs = array_values( $refs );

			return apply_filters( 'mycred_log_get_refs', $refs );
		}

		/**
		 * Get Users
		 * Returns an array of user id's and display names.
		 * @since 0.1
		 * @version 1.0
		 */
		protected function get_users() {
			$users = wp_cache_get( 'mycred_users' );

			if ( false === $users ) {
				$users = array();
				$blog_users = get_users( array( 'orderby' => 'display_name' ) );
				foreach ( $blog_users as $user ) {
					if ( false === $this->core->exclude_user( $user->ID ) )
						$users[ $user->ID ] = $user->display_name;
				}
				wp_cache_set( 'mycred_users', $users );
			}

			return apply_filters( 'mycred_log_get_users', $users );
		}

		/**
		 * Filter Log options
		 * @since 0.1
		 * @version 1.3
		 */
		public function filter_options( $is_profile = false, $refs = array() ) {
			echo '<div class="alignleft actions">';
			$show = false;

			// Filter by reference
			$references = $this->get_refs( $refs );
			if ( ! empty( $references ) ) {
				echo '<select name="ref" id="myCRED-reference-filter"><option value="">' . __( 'Show all references', 'mycred' ) . '</option>';
				foreach ( $references as $ref ) {
					$label = str_replace( array( '_', '-' ), ' ', $ref );
					echo '<option value="' . $ref . '"';
					if ( isset( $_GET['ref'] ) && $_GET['ref'] == $ref ) echo ' selected="selected"';
					echo '>' . ucwords( $label ) . '</option>';
				}
				echo '</select>';
				$show = true;
			}

			// Filter by user
			if ( $this->core->can_edit_creds() && ! $is_profile && $this->num_rows > 0 ) {
				echo '<input type="text" class="form-control" name="user_id" id="myCRED-user-filter" size="12" placeholder="' . __( 'User ID', 'mycred' ) . '" value="' . ( ( isset( $_GET['user_id'] ) ) ? $_GET['user_id'] : '' ) . '" /> ';
				$show = true;
			}

			// Filter Order
			if ( $this->num_rows > 0 ) {
				echo '<select name="order" id="myCRED-order-filter"><option value="">' . __( 'Show in order', 'mycred' ) . '</option>';
				$options = array( 'ASC' => __( 'Ascending', 'mycred' ), 'DESC' => __( 'Descending', 'mycred' ) );
				foreach ( $options as $value => $label ) {
					echo '<option value="' . $value . '"';
					if ( ! isset( $_GET['order'] ) && $value == 'DESC' ) echo ' selected="selected"';
					elseif ( isset( $_GET['order'] ) && $_GET['order'] == $value ) echo ' selected="selected"';
					echo '>' . $label . '</option>';
				}
				echo '</select>';
				$show = true;
			}

			// Let others play
			if ( has_action( 'mycred_filter_log_options' ) ) {
				do_action( 'mycred_filter_log_options', $this );
				$show = true;
			}

			if ( $show === true )
				echo '<input type="submit" class="btn btn-default button button-secondary button-large" value="' . __( 'Filter', 'mycred' ) . '" />';

			echo '</div>';
		}

		/**
		 * Exporter
		 * Displays all available export options.
		 * @since 0.1
		 * @version 1.0
		 */
		public function exporter( $title = '', $is_profile = false ) {
			// Must be logged in
			if ( ! is_user_logged_in() ) return;

			// Make sure current user can export
			if ( ! apply_filters( 'mycred_user_can_export', false ) && ! $this->core->can_edit_creds() ) return;

			// Check if we allow export from front end. Disallowed by default
			if ( ! apply_filters( 'mycred_allow_front_export', false ) && ! is_admin() ) return;

			// Export options
			$exports = mycred_get_log_exports();

			// A difference in the default aguments should show us "search results"
			if ( empty( $this->diff ) || ( ! empty( $this->diff ) && $this->max_num_pages < 2 ) )
				unset( $exports['search'] );
			
			// Entire log export is not available when viewing our own history
			if ( $is_profile )
				unset( $exports['all'] ); ?>

<div style="display:none;" class="clear" id="export-log-history">
	<?php	if ( ! empty( $title ) ) : ?><h3 class="group-title"><?php echo $title; ?></h3><?php endif; ?>
	<form action="<?php echo add_query_arg( array( 'mycred-export' => 'do' ) ); ?>" method="post">
		<input type="hidden" name="token" value="<?php echo wp_create_nonce( 'mycred-run-log-export' ); ?>" />
<?php
			if ( ! empty( $exports ) ) {

				foreach ( (array) $this->args as $arg_key => $arg_value )
					echo '<input type="hidden" name="' . $arg_key . '" value="' . $arg_value . '" />';

				foreach ( (array) $exports as $id => $data ) {
					// Label
					if ( $is_profile )
						$label = $data['my_label'];
					else
						$label = $data['label'];

					echo '<input type="submit" class="' . $data['class'] . '" name="action" value="' . $label . '" /> ';
				}
?>
	</form>
	<p><span class="description"><?php _e( 'Log entries are exported to a CSV file and depending on the number of entries selected, the process may take a few seconds.', 'mycred' ); ?></span></p>
<?php
			}
			else {
				echo '<p>' . __( 'No export options available.', 'mycred' ) . '</p>';
			}
?>
</div>
<script type="text/javascript">
jQuery(function($) {
	$( '.toggle-exporter' ).click(function(){
		$( '#export-log-history' ).toggle();
	});
});
</script>
<?php
		}

		/**
		 * Table Headers
		 * Returns all table column headers.
		 *
		 * @filter mycred_log_column_headers
		 * @since 0.1
		 * @version 1.1
		 */
		public function table_headers() {
			global $mycred_types;

			return apply_filters( 'mycred_log_column_headers', array(
				'column-username' => __( 'User', 'mycred' ),
				'column-time'     => __( 'Date', 'mycred' ),
				'column-creds'    => $this->core->plural(),
				'column-entry'    => __( 'Entry', 'mycred' )
			), $this );
		}

		/**
		 * Display
		 * @since 0.1
		 * @version 1.0
		 */
		public function display() {
			echo $this->get_display();
		}

		/**
		 * Get Display
		 * Generates a table for our results.
		 *
		 * @since 0.1
		 * @version 1.0
		 */
		public function get_display() {
			$output = '
<table class="table mycred-table widefat log-entries table-striped" cellspacing="0">
	<thead>
		<tr>';

			// Table header
			foreach ( $this->headers as $col_id => $col_title ) {
				$output .= '<th scope="col" id="' . str_replace( 'column-', '', $col_id ) . '" class="manage-column ' . $col_id . '">' . $col_title . '</th>';
			}

			$output .= '
		</tr>
	</thead>
	<tfoot>';

			// Table footer
			foreach ( $this->headers as $col_id => $col_title ) {
				$output .= '<th scope="col" class="manage-column ' . $col_id . '">' . $col_title . '</th>';
			}

			$output .= '
	</tfoot>
	<tbody id="the-list">';

			// Loop
			if ( $this->have_entries() ) {
				$alt = 0;
				
				foreach ( $this->results as $log_entry ) {
					$row_class = apply_filters( 'mycred_log_row_classes', array( 'myCRED-log-row' ), $log_entry );

					$alt = $alt+1;
					if ( $alt % 2 == 0 )
						$row_class[] = ' alt';

					$output .= '<tr class="' . implode( ' ', $row_class ) . '">';
					$output .= $this->get_the_entry( $log_entry );
					$output .= '</tr>';
				}
			}
			// No log entry
			else {
				$output .= '<tr><td colspan="' . count( $this->headers ) . '" class="no-entries">' . $this->get_no_entries() . '</td></tr>';
			}

			$output .= '
	</tbody>
</table>' . "\n";

			return $output;
		}

		/**
		 * The Entry
		 * @since 0.1
		 * @version 1.1
		 */
		public function the_entry( $log_entry, $wrap = 'td' ) {
			echo $this->get_the_entry( $log_entry, $wrap );
		}

		/**
		 * Get The Entry
		 * Generated a single entry row depending on the columns used / requested.
		 * @filter mycred_log_date
		 * @since 0.1
		 * @version 1.3
		 */
		public function get_the_entry( $log_entry, $wrap = 'td' ) {
			$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			$entry_data = '';

			// Run though columns
			foreach ( $this->headers as $column_id => $column_name ) {
				switch ( $column_id ) {
					// Username Column
					case 'column-username' :

						$user = get_userdata( $log_entry->user_id );
						if ( $user === false )
							$content = '<span>' . __( 'User Missing', 'mycred' ) . ' (ID: ' . $log_entry->user_id . ')</span>';
						else
							$content = '<span>' . $user->display_name . '</span>';
						
						$content = apply_filters( 'mycred_log_username', $content, $log_entry->user_id, $log_entry );

					break;
					// Date & Time Column
					case 'column-time' :

						$content = $time = apply_filters( 'mycred_log_date', date_i18n( $date_format, $log_entry->time ), $log_entry->time, $log_entry );

					break;
					// Amount Column
					case 'column-creds' :

						$content = $creds = $this->core->format_creds( $log_entry->creds );
						$content = apply_filters( 'mycred_log_creds', $content, $log_entry->creds, $log_entry );

					break;
					// Log Entry Column
					case 'column-entry' :

						$content = '<div class="mycred-mobile-log" style="display:none;">' . $time . '<div>' . $creds . '</div></div>';
						$content .= $this->core->parse_template_tags( $log_entry->entry, $log_entry );
						$content = apply_filters( 'mycred_log_entry', $content, $log_entry->entry, $log_entry );

					break;
					// Let others play
					default :
					
						$content = apply_filters( 'mycred_log_' . $column_id, '', $log_entry );
					
					break;
				}
				$entry_data .= '<' . $wrap . ' class="' . $column_id . '">' . $content . '</' . $wrap . '>';
			}
			return $entry_data;
		}

		/**
		 * Mobile Support
		 * @since 1.4
		 * @version 1.0
		 */
		public function mobile_support() {
			echo '<style type="text/css">' . apply_filters( 'mycred_log_mobile_support', '
@media all and (max-width: 480px) {
	.column-time, .column-creds { display: none; }
	.mycred-mobile-log { display: block !important; }
	.mycred-mobile-log div { float: right; font-weight: bold; }
}
' ) . '</style>';
		}

		/**
		 * No Entries
		 * @since 0.1
		 * @version 1.0
		 */
		public function no_entries() {
			echo $this->get_no_entries();
		}

		/**
		 * Get No Entries
		 * @since 0.1
		 * @version 1.0
		 */
		public function get_no_entries() {
			return __( 'No log entries found', 'mycred' );
		}

		/**
		 * Log Search
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function search() {
			if ( isset( $_GET['s'] ) && $_GET['s'] != '' )
				$serarch_string = $_GET['s'];
			else
				$serarch_string = ''; ?>

			<p class="search-box">
				<label class="screen-reader-text" for=""><?php _e( 'Search Log', 'mycred' ); ?>:</label>
				<input type="search" name="s" value="<?php echo $serarch_string; ?>" placeholder="<?php _e( 'search log entries', 'mycred' ); ?>" />
				<input type="submit" name="mycred-search-log" id="search-submit" class="button button-medium button-secondary" value="<?php _e( 'Search Log', 'mycred' ); ?>" />
			</p>
<?php
		}

		/**
		 * Filter by Dates
		 * @since 0.1
		 * @version 1.0
		 */
		public function filter_dates( $url = '' ) {
			$date_sorting = apply_filters( 'mycred_sort_by_time', array(
				''          => __( 'All', 'mycred' ),
				'today'     => __( 'Today', 'mycred' ),
				'yesterday' => __( 'Yesterday', 'mycred' ),
				'thisweek'  => __( 'This Week', 'mycred' ),
				'thismonth' => __( 'This Month', 'mycred' )
			) );

			if ( ! empty( $date_sorting ) ) {
				$total = count( $date_sorting );
				$count = 0;
				echo '<ul class="subsubsub">';
				foreach ( $date_sorting as $sorting_id => $sorting_name ) {
					$count = $count+1;
					echo '<li class="' . $sorting_id . '"><a href="';

					// Build Query Args
					$url_args = array();
					if ( isset( $_GET['user_id'] ) && $_GET['user_id'] != '' )
						$url_args['user_id'] = $_GET['user_id'];
					if ( isset( $_GET['ref'] ) && $_GET['ref'] != '' )
						$url_args['ref'] = $_GET['ref'];
					if ( isset( $_GET['order'] ) && $_GET['order'] != '' )
						$url_args['order'] = $_GET['order'];
					if ( isset( $_GET['s'] ) && $_GET['s'] != '' )
						$url_args['s'] = $_GET['s'];
					if ( $sorting_id != '' )
						$url_args['show'] = $sorting_id;

					// Build URL
					if ( ! empty( $url_args ) )
						echo add_query_arg( $url_args, $url );
					else
						echo $url;

					echo '"';

					if ( isset( $_GET['show'] ) && $_GET['show'] == $sorting_id ) echo ' class="current"';
					elseif ( ! isset( $_GET['show'] ) && $sorting_id != '' ) echo ' class="current"';

					echo '>' . $sorting_name . '</a>';
					if ( $count != $total ) echo ' | ';
					echo '</li>';
				}
				echo '</ul>';
			}
		}
		
		/**
		 * Reset Query
		 * @since 1.3
		 * @version 1.0
		 */
		public function reset_query() {
			$this->args = NULL;
			$this->request = NULL;
			$this->prep = NULL;
			$this->num_rows = NULL;
			$this->max_num_pages = NULL;
			$this->total_rows = NULL;
		
			$this->results = NULL;
		
			$this->headers = NULL;
		}
	}
endif;
?>