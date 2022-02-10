
<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ($statements->count) {
    ?>
    <div class="tutor-dashboard-statement-table-wrap">
        <table class="tutor-dashboard-statement-table tutor-table">
            <tr>
                <th><?php _e('Course', 'tutor'); ?></th>
                <th><?php _e('Earning', 'tutor'); ?></th>
                <th><?php _e('Deduct', 'tutor'); ?></th>
            </tr>

            <?php
            foreach ($statements->results as $statement){
                ?>
                <tr>
                    <td>
                        <p>
                            <a href="<?php echo get_the_permalink($statement->course_id); ?>" target="_blank">
                                <?php echo $statement->course_title; ?>
                            </a>
                        </p>

                        <p>
                            <?php _e('Price', 'tutor'); ?>
                            <?php echo tutor_utils()->tutor_price($statement->course_price_total); ?>
                        </p>

                        <p class="small-text">
                            <span class="statement-order-<?php echo $statement->order_status; ?>"><?php echo $statement->order_status; ?></span> <?php
                            _e('Order ID'); ?> #<?php echo $statement->order_id; ?>,

                            <strong><?php _e('Date:', 'tutor') ?></strong>
                            <i><?php echo date_i18n(get_option('date_format'), strtotime($statement->created_at)).' '.date_i18n(get_option('time_format'), strtotime($statement->created_at)); ?></i>
                        </p>

                        <?php
                        $order = new WC_Order($statement->order_id);
                        echo '<div class="statement-address"> <strong>Purchaser</strong> <address>'.$order->get_formatted_billing_address().'</address></div>';
                        ?>
                    </td>
                    <td>
                        <p><?php echo tutor_utils()->tutor_price($statement->instructor_amount); ?></p>
                        <p class="small-text"> <?php _e('As per');  ?> <?php echo $statement->instructor_rate ?> (<?php echo $statement->commission_type ?>) </p>
                    </td>

                    <td>
                        <p><?php _e('Commission', 'tutor'); ?> : <?php echo tutor_utils()->tutor_price($statement->admin_amount); ?> </p>
                        <p class="small-text"><?php _e('Rate', 'tutor'); ?> : <?php echo $statement->admin_rate; ?> </p>
                        <p class="small-text"><?php _e('Type', 'tutor'); ?> : <?php echo $statement->commission_type; ?> </p>

                        <p><?php _e('Deducted', 'tutor'); ?> : <?php echo $statement->deduct_fees_name; ?>  <?php echo tutor_utils()->tutor_price
                            ($statement->deduct_fees_amount); ?>
                        </p>
                        <p class="small-text"><?php _e('Type', 'tutor'); ?> : <?php echo $statement->deduct_fees_type; ?> </p>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
    <?php
}else{
    printf("<span>%s</span>", __('There is not enough sales data to generate a statement', 'tutor'));
}

?>