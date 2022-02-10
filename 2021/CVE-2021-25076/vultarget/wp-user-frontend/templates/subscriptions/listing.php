<?php
/**
 * Subscriptions Template to Display list of subscriptions packages
 *
 * @version 2.8.8
 *
 * @var WPUF_Subscription
 * @var $packs            array
 * @var $pack_order       array
 * @var $args             array
 * @var $details_meta
 * @var $current_pack
 */
do_action( 'wpuf_before_subscription_listing', $packs );

if ( $packs ) {
    echo wp_kses_post( '<ul class="wpuf_packs">' );

    if ( isset( $args['include'] ) && $args['include'] != '' ) {
        for ( $i = 0; $i < count( $pack_order ); $i++ ) {
            foreach ( $packs as $pack ) {
                if (  (int) $pack->ID == $pack_order[$i] ) {
                    $class = 'wpuf-pack-' . $pack->ID; ?>
                    <li class="<?php echo esc_attr( $class ); ?>">
                        <?php $subscription->pack_details( $pack, $details_meta, isset( $current_pack['pack_id'] ) ? $current_pack['pack_id'] : '' ); ?>
                    </li>
                    <?php
                }
            }
        }
    } else {
        foreach ( $packs as $pack ) {
            $class = 'wpuf-pack-' . $pack->ID; ?>
            <li class="<?php echo esc_attr( $class ); ?>">
                <?php $subscription->pack_details( $pack, $details_meta, isset( $current_pack['pack_id'] ) ? $current_pack['pack_id'] : '' ); ?>
            </li>
            <?php
        }
    }
    echo wp_kses_post( '</ul>' );
}

do_action( 'wpuf_after_subscription_listing', $packs );
