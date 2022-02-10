<?php

if ( ! class_exists( 'WPUF_WeDevs_Insights' ) ) :

/**
 * weDevs Tracker
 *
 * This is a tracker class to track plugin usage based on if the customer has opted in.
 * No personal information is being tracked by this class, only general settings, active plugins, environment details
 * and admin email.
 *
 * @version 1.0
 *
 * @author Tareq Hasan <tareq@wedevs.com>
 */
class WPUF_WeDevs_Insights {

    /**
     * Initialize the class
     */
    public function __construct( $file ) {

        if ( ! class_exists( 'Appsero\Client' ) ) {
            require_once __DIR__ . '/appsero/Client.php';
        }

        $client = new Appsero\Client( '958afc63-99f8-4b98-b321-fcbc5cf95694', 'WP User Frontend', $file );

        $this->insights = $client->insights();

        // Active insights
        $this->insights->init();
    }

}

endif;
