<?php

/**
 * The installer class
 *
 * @since 2.6.0
 */
class WPUF_Installer {

    /**
     * The installer class
     *
     * @return void
     */
    public function install() {
        $this->create_tables();
        $this->schedule_events();

        $installed = get_option( 'wpuf_installed' );

        if ( !$installed ) {
            update_option( 'wpuf_installed', time() );
        }

        flush_rewrite_rules( false );

        set_transient( 'wpuf_activation_redirect', true, 30 );
        update_option( 'wpuf_version', WPUF_VERSION );
    }

    /**
     * Create the table schema
     *
     * @return void
     */
    public function create_tables() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( !empty( $wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }

            if ( !empty( $wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

        $table_schema = [
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpuf_transaction` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) DEFAULT NULL,
                `status` varchar(60) NOT NULL DEFAULT 'pending_payment',
                `subtotal` varchar(255) DEFAULT '',
                `tax` varchar(255) DEFAULT '',
                `cost` varchar(255) DEFAULT '',
                `post_id` varchar(20) DEFAULT NULL,
                `pack_id` bigint(20) DEFAULT NULL,
                `payer_first_name` varchar(60),
                `payer_last_name` varchar(60),
                `payer_email` varchar(100),
                `payment_type` varchar(20),
                `payer_address` longtext,
                `transaction_id` varchar(60),
                `created` datetime NOT NULL,
                PRIMARY KEY (`id`),
                key `user_id` (`user_id`),
                key `post_id` (`post_id`),
                key `pack_id` (`pack_id`),
                key `payer_email` (`payer_email`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpuf_subscribers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `name` varchar(191) NOT NULL,
                `subscribtion_id` varchar(191) NOT NULL,
                `subscribtion_status` varchar(191) NOT NULL,
                `gateway` varchar(191) NOT NULL,
                `transaction_id` varchar(191) NOT NULL,
                `starts_from` varchar(191) NOT NULL,
                `expire` varchar(191) NOT NULL,
                PRIMARY KEY (`id`),
                key `user_id` (`user_id`)
            ) $collate;",
        ];

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        foreach ( $table_schema as $table ) {
            dbDelta( $table );
        }
    }

    /**
     * Schedules the post expiry event
     *
     * @since 2.2.7
     */
    public function schedule_events() {
        wp_schedule_event( time(), 'daily', 'wpuf_remove_expired_post_hook' );
    }
}
