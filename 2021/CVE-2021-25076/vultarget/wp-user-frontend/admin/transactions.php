<div class="wrap">
    <h2><?php esc_html_e( 'Transactions', 'wp-user-frontend' ); ?></h2>

    <?php
        global $wpdb;
        $total_income = $wpdb->get_var( "SELECT SUM(cost) FROM {$wpdb->prefix}wpuf_transaction WHERE status = 'completed'" );
        //$month_income = $wpdb->get_var( "SELECT SUM(cost) FROM {$wpdb->prefix}wpuf_transaction WHERE YEAR(`created`) = YEAR(NOW()) AND MONTH(`created`) = MONTH(NOW()) AND status = 'completed'" );
        $total_tax = $wpdb->get_var( "SELECT SUM(tax) FROM {$wpdb->prefix}wpuf_transaction WHERE status = 'completed'" );
    ?>

    <form method="post">
        <input type="hidden" name="page" value="transactions">
        <?php
            $this->transactions_list_table_obj->prepare_items();
            $this->transactions_list_table_obj->views();
            $this->transactions_list_table_obj->display();
        ?>
    </form>
</div>
