<?php
/**
 * Template for displaying instructors earnings
 *
 * @since v.1.1.2
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $wpdb;

$user_id = get_current_user_id();

/**
 * Getting the last week
 */
$start_date = date("Y-m-01");
$end_date = date("Y-m-t");

$stats = tutils()->get_earning_chart( $user_id, $start_date, $end_date );
extract($stats);

if ( ! $earning_sum){
	echo '<p>'.__('No Earning info available', 'tutor' ).'</p>';
	return;
}

?>


<h3><?php _e('Earnings', 'tutor') ?></h3>

<div class="tutor-dashboard-content-inner">

	<div class="tutor-dashboard-inline-links">
		<ul>
			<li class="active">
                <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning'); ?>">
                    <?php _e('Earnings', 'tutor'); ?>
                </a>
			</li>
			<li>
                <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning/report'); ?>">
                    <?php _e('Reports', 'tutor'); ?>
                </a>
			</li>
			<li>
                <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('earning/statements'); ?>">
                    <?php _e('Statements', 'tutor'); ?>
                </a>
            </li>
		</ul>
	</div>

    <div class="tutor-dashboard-earning-info-cards">
        <div class="tutor-dashboard-info-card">
            <p>
                <span> <?php _e('My Balance', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->balance); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card" title="<?php _e('All Time', 'tutor'); ?>">
            <p>
                <span> <?php _e('My Earnings', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->instructor_amount); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card"  title="<?php _e('Based on course price', 'tutor'); ?>">
            <p>
                <span> <?php _e('All time sales', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->course_price_total); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card" title="<?php _e('All of withdraw type excluding rejected.', 'tutor'); ?>">
            <p>
                <span> <?php _e('All time withdrawals', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->withdraws_amount); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card">
            <p>
                <span> <?php _e('Deducted Commissions', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->admin_amount); ?></span>
            </p>
        </div>

        <?php if ($earning_sum->deduct_fees_amount > 0){ ?>
            <div class="tutor-dashboard-info-card">
                <p>
                    <span> <?php _e('Deducted Fees.', 'tutor'); ?> </span>
                    <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->deduct_fees_amount); ?></span>
                </p>
            </div>
        <?php } ?>
    </div>

    <div class="tutor-dashboard-item-group">
        <h4><?php _e('Earnings Chart for this month', 'tutor') ?> (<?php echo date("F") ?>)</h4>
        <canvas id="tutorChart" style="width: 100%; height: 400px;"></canvas>
    </div>

</div>


<?php
$tutor_primary_color = tutor_utils()->get_option('tutor_primary_color');
if ( ! $tutor_primary_color){
    $tutor_primary_color = '#3057D5';
}
?>

<script>
    var ctx = document.getElementById("tutorChart").getContext('2d');
    var tutorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($chartData)); ?>,
            datasets: [{
                label: '<?php _e('Earning','tutor');?>',
                backgroundColor: '<?php echo $tutor_primary_color; ?>',
                borderColor: '<?php echo $tutor_primary_color; ?>',
                data: <?php echo json_encode(array_values($chartData)); ?>,
                borderWidth: 2,
                fill: false,
                lineTension: 0,
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        min: 0, // it is for ignoring negative step.
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                }]
            },

            legend: {
                display: false
            }
        }
    });
</script>