<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

?>

<h2><?php _e('Purchase History', 'tutor'); ?></h2>

<?php
$orders = tutils()->get_orders_by_user_id();
$monetize_by = tutils()->get_option('monetize_by');

if (tutils()->count($orders)){
	?>
    <div class="responsive-table-wrap">
        <table class="tutor-table">
            <tr>
                <th><?php _e('ID', 'tutor'); ?></th>
                <th><?php _e('Courses', 'tutor'); ?></th>
                <th><?php _e('Amount', 'tutor'); ?></th>
                <th><?php _e('Status', 'tutor'); ?></th>
                <th><?php _e('Date', 'tutor'); ?></th>
            </tr>
            <?php
            foreach ($orders as $order) {
                if ($monetize_by === 'wc') {
                    $wc_order = wc_get_order($order->ID);
                    $price = tutils()->tutor_price($wc_order->get_total());
                    $status = tutils()->order_status_context($order->post_status);
                } else if ($monetize_by === 'edd') {
                    $edd_order = edd_get_payment($order->ID);
                    $price = edd_currency_filter( edd_format_amount( $edd_order->total ), edd_get_payment_currency_code( $order->ID ) );
                    $status = $edd_order->status_nicename;
                }
                ?>
                <tr>
                    <td>#<?php echo $order->ID; ?></td>
                    <td>
                        <?php
                        $courses = tutils()->get_course_enrolled_ids_by_order_id($order->ID);
                        if (tutils()->count($courses)){
                            foreach ($courses as $course){
                                echo '<p>'.get_the_title($course['course_id']).'</p>';
                            }
                        }
                        ?>
                    </td>
                    <td><?php echo $price; ?></td>
                    <td><?php echo $status; ?></td>

                    <td>
                        <?php echo date_i18n(get_option('date_format'), strtotime($order->post_date)) ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>

	<?php
}else{
	echo _e('No purchase history available', 'tutor');
}

?>
