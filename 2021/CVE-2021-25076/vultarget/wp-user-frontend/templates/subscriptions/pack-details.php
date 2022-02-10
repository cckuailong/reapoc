<?php
/**
 * Subscription single pack details template
 *
 * @version 2.8.8
 *
 * @var WP_Post
 * @var $billing_amount
 * @var $details_meta
 * @var $recurring_des
 * @var $trial_des
 * @var $coupon_status
 * @var $current_pack_id
 * @var $button_name
 */
?>
<div class="wpuf-pricing-wrap">
    <h3><?php echo wp_kses_post( $pack->post_title ); ?> </h3>
    <div class="wpuf-sub-amount">

        <?php if ( $billing_amount != '0.00' ) { ?>
            <span class="wpuf-sub-cost"><?php echo esc_html( wpuf_format_price( $billing_amount ) ); ?></span>
        <?php } else { ?>
            <span class="wpuf-sub-cost"><?php esc_html_e( 'Free', 'wp-user-frontend' ); ?></span>
        <?php } ?>

        <?php echo wp_kses_post( __( $recurring_des, 'wp-user-frontend')); ?>

    </div>
    <?php
    if ( $pack->meta_value['recurring_pay'] == 'yes' ) {
        ?>
        <div class="wpuf-sub-body wpuf-nullamount-hide">
            <div class="wpuf-sub-terms"><?php echo esc_html( $trial_des ); ?></div>
        </div>
        <?php
    }
    ?>
</div>
<div class="wpuf-sub-desciption">
    <?php echo wp_kses_post( wpautop( $pack->post_content ) ); ?>
</div>
<?php
$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

if ( $action == 'wpuf_pay' || $coupon_status ) {
    return;
}
?>
<div class="wpuf-sub-button">
    <a <?php echo ( esc_attr( $current_pack_status ) == 'completed' ) ? ' class = "wpuf-disabled-link" ' : ''; ?>  href="<?php echo ( esc_attr( $current_pack_status ) == 'completed' ) ? 'javascript:' : esc_attr( add_query_arg( $query_args, esc_url( $query_url ) ) ); ?>" onclick="<?php echo esc_attr( $details_meta['onclick'] ); ?>">
        <?php echo esc_html( $button_name ); ?>
    </a>
</div>
