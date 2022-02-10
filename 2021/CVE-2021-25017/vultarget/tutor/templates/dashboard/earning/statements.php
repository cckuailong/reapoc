<?php
/**
 * Template for displaying Instructor Statements
 *
 * @since v.1.1.2
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$sub_page = 'this_month';
if ( ! empty($_GET['time_period'])){
	$sub_page = sanitize_text_field($_GET['time_period']);
}
if ( ! empty($_GET['date_range_from']) && ! empty($_GET['date_range_to'])){
	$sub_page = 'date_range';
}
?>
    <div class="tutor-dashboard-inline-links">
        <ul>
            <li><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning'); ?>"> <?php _e('Earning'); ?></a>
            </li>
            <li><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning/report'); ?>"> <?php _e('Report', 'tutor'); ?> </a></li>
            <li class="active">
                <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning/statements'); ?>">
					<?php _e('Statements', 'tutor'); ?> </a>
            </li>
        </ul>
    </div>
<?php
tutor_load_template('dashboard.earning.earning-report-top-menu', compact('sub_page'));

$user_id = get_current_user_id();

$complete_status = tutor_utils()->get_earnings_completed_statuses();
$statuses = $complete_status;
$complete_status = "'".implode("','", $complete_status)."'";

$statements = false;

//Pagination Variable
$per_page = 1;//tutor_utils()->get_option('statement_show_per_page', 20);
$current_page = max( 1, tutor_utils()->avalue_dot('current_page', $_GET) );
$offset = ($current_page-1)*$per_page;


switch ($sub_page){

	case 'last_year':
		$year = date('Y', strtotime('-1 year'));
		$dataFor = 'yearly';

		$statements = tutor_utils()->get_earning_statements($user_id, compact('year', 'dataFor', 'per_page', 'offset'));
		break;

	case 'this_year':
		$year = date('Y');
		$dataFor = 'yearly';

		$statements = tutor_utils()->get_earning_statements($user_id, compact('year', 'dataFor', 'per_page', 'offset'));
		break;

	case 'last_month':
		$start_date = date("Y-m", strtotime('-1 month'));
		$start_date = $start_date.'-1';
		$end_date = date("Y-m-t", strtotime($start_date));

		$statements = tutor_utils()->get_earning_statements($user_id, compact('start_date', 'end_date', 'per_page', 'offset'));
		break;

	case 'this_month':

		$start_date = date("Y-m-01");
		$end_date = date("Y-m-t");

		$statements = tutor_utils()->get_earning_statements($user_id, compact('start_date', 'end_date', 'per_page', 'offset'));
		break;

	case 'last_week':

		$previous_week = strtotime("-1 week +1 day");
		$start_date = strtotime("last sunday midnight",$previous_week);
		$end_date = strtotime("next saturday",$start_date);
		$start_date = date("Y-m-d",$start_date);
		$end_date = date("Y-m-d",$end_date);

		$statements = tutor_utils()->get_earning_statements($user_id, compact('start_date', 'end_date', 'per_page', 'offset'));
		break;


	case 'this_week':
		$start_date = date("Y-m-d", strtotime("last sunday midnight"));
		$end_date = date("Y-m-d", strtotime("next saturday"));

		$statements = tutor_utils()->get_earning_statements($user_id, compact('start_date', 'end_date', 'per_page', 'offset'));
		break;

	case 'date_range':

		$start_date = sanitize_text_field(tutor_utils()->avalue_dot('date_range_from', $_GET));
		$end_date = sanitize_text_field(tutor_utils()->avalue_dot('date_range_to', $_GET));

		$statements = tutor_utils()->get_earning_statements($user_id, compact('start_date', 'end_date', 'per_page', 'offset'));
		break;
}

?>

<div class="tutor-dashboard-item-group">
    <h4><?php _e('Statements', 'tutor'); ?></h4>
<?php

if ($statements->count) {
	?>

    <p class="tutor-dashboard-pagination-results-stats">

		<?php
		echo sprintf(__('Showing results %d to %d of %d', 'tutor'), $offset +1, min($statements->count, $offset +1+tutor_utils()->count($statements->results)), $statements->count) ;
		?>
    </p>

    <div class="tutor-dashboard-statement-table-wrap">
        <table class="tutor-dashboard-statement-table tutor-table">
            <tr>
                <th><?php _e('Course Info', 'tutor'); ?></th>
                <th><?php _e('Earning', 'tutor'); ?></th>
                <th><?php _e('Commission', 'tutor'); ?></th>
                <th><?php _e('Deduct', 'tutor'); ?></th>
            </tr>

            <?php
            foreach ($statements->results as $statement){
                ?>
                <tr>
                    <td>

                        <p class="small-text">
                            <span class="statement-order-<?php echo $statement->order_status; ?>"><?php echo $statement->order_status; ?></span>
                            &nbsp; <strong><?php _e('Date:', 'tutor') ?></strong>
                            <i><?php echo date_i18n(get_option('date_format'), strtotime($statement->created_at)).' '.date_i18n(get_option('time_format'), strtotime($statement->created_at)); ?></i>
                        </p>

                        <p>
                            <a href="<?php echo get_the_permalink($statement->course_id); ?>" target="_blank">
                                <?php echo $statement->course_title; ?>
                            </a>
                        </p>

                        <p>
                            <strong><?php _e('Price: ', 'tutor'); ?></strong>
                            <?php echo tutor_utils()->tutor_price($statement->course_price_total); ?>
                        </p>

                        <p class="small-text"><strong><?php _e('Order ID'); ?> #<?php echo $statement->order_id; ?></strong></p>

                        <?php
                        $order = wc_get_order($statement->order_id);
                        if($order && is_object($order)) {
                            $billing_address = $order->get_formatted_billing_address();
                            if($billing_address) {
                                echo '<div class="statement-address">
                                    <strong>' . __('Purchaser', 'tutor') . ': </strong> <address>' . $billing_address . '</address>
                                </div>';
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <p><?php echo tutor_utils()->tutor_price($statement->instructor_amount); ?></p>
                        <p class="small-text"> <?php _e('As per');  ?> <?php echo $statement->instructor_rate ?> (<?php echo $statement->commission_type ?>) </p>
                    </td>

                    <td>
                        <p><?php echo tutor_utils()->tutor_price($statement->admin_amount); ?> </p>
                        <p class="small-text"><?php _e('Rate', 'tutor'); ?> : <?php echo $statement->admin_rate; ?> </p>
                        <p class="small-text"><?php _e('Type', 'tutor'); ?> : <?php echo $statement->commission_type; ?> </p>

                    </td>

                    <td>
                        <p><?php echo $statement->deduct_fees_name; ?>  <?php echo tutor_utils()->tutor_price($statement->deduct_fees_amount); ?>
                        </p>
                        <p class="small-text"><?php _e('Type', 'tutor'); ?> : <?php echo $statement->deduct_fees_type; ?> </p>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

<?php } else{
    printf("<span>%s</span>", __('There is not enough sales data to generate a statement', 'tutor'));
} ?>

</div>

<?php

if ($statements->count){
    ?>
        <div class="tutor-pagination">
            <?php
                echo paginate_links( array(
                    'format' => '?current_page=%#%',
                    'current' => $current_page,
                    'total' => ceil($statements->count/$per_page)
                ) );
            ?>

        </div>

    <?php
}

