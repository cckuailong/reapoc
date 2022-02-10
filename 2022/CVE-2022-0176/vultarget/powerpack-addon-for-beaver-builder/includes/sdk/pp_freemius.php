<?php

if ( file_exists( BB_POWERPACK_DIR . 'includes/sdk/freemius/start.php' ) ) :

    function pp_fs() {
        global $pp_fs;

        if ( ! isset( $pp_fs ) ) {
            // Include Freemius SDK.
            require_once BB_POWERPACK_DIR . 'includes/sdk/freemius/start.php';

            $args = array(
                'id'                  => '764',
                'slug'                => 'powerpack-addon-for-beaver-builder',
                'type'                => 'plugin',
                'public_key'          => 'pk_18b74a8ec3b8b66e6d8cb43f43755',
                'is_live'             => true,
                'is_premium'          => false,
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           	=> 'ppbb-settings',
                    'account'        	=> false,
                    'contact'        	=> false,
                    'support'        	=> false,
                    'parent'         	=> array(
                        'slug' => 'options-general.php',
                    ),
                ),
            );

            $pp_fs = fs_dynamic_init( $args );

            return $pp_fs;
        }
    }

    pp_fs();

    function pp_fs_custom_connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link )
    {
        return sprintf(
            __fs( 'hey-x' ) . '<br>' .
            __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'bb-powerpack-lite' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }
    //pp_fs()->add_filter('connect_message_on_update', 'pp_fs_custom_connect_message_on_update', 10, 6);

endif;
