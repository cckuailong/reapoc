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
 * Getting the Last Month
 */
$year = date('Y', strtotime('-1 year'));

$stats = tutils()->get_earning_chart_yearly( $user_id, $year );
extract($stats);

if ( ! $earning_sum){
	echo '<p>'.__('No Earning info available', 'tutor' ).'</p>';
	return;
}

?>

    <div class="tutor-dashboard-earning-info-cards">
        <div class="tutor-dashboard-info-card" title="<?php _e('All time', 'tutor'); ?>">
            <p>
                <span> <?php _e('My Earning', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->instructor_amount); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card" title="<?php _e('Based on course price', 'tutor'); ?>">
            <p>
                <span> <?php _e('All time sales', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->course_price_total); ?></span>
            </p>
        </div>
        <div class="tutor-dashboard-info-card">
            <p>
                <span> <?php _e('Deducted Commissions', 'tutor'); ?> </span>
                <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->admin_amount); ?></span>
            </p>
        </div>


        <?php if ($earning_sum->deduct_fees_amount > 0){ ?>
            <div class="tutor-dashboard-info-card" title="<?php _e('Deducted Fees', 'tutor'); ?>">
                <p>
                    <span> <?php _e('Deducted Fees', 'tutor'); ?> </span>
                    <span class="tutor-dashboard-info-val"><?php echo tutor_utils()->tutor_price($earning_sum->deduct_fees_amount); ?></span>
                </p>
            </div>
        <?php } ?>
    </div>





<div class="tutor-dashboard-item-group">
    <h4><?php echo sprintf(__("Earning Data for the year of %s", 'tutor'), $year);?></h4>
    <?php
    tutor_load_template('dashboard.earning.chart-body', compact('chartData', 'statements'));
    ?>
</div>

<div class="tutor-dashboard-item-group">
    <h4><?php _e('Sales statements for this period', 'tutor') ?></h4>
    <?php tutor_load_template('dashboard.earning.statement', compact('chartData', 'statements')); ?>
</div>
