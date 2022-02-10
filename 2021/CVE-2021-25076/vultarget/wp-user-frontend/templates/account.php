<div class="wpuf-dashboard-container">
    <nav class="wpuf-dashboard-navigation">
        <ul>
            <?php
                if ( is_user_logged_in() ) {
                    foreach ( $sections as $section => $label ) {
                        // backward compatibility
                        if ( is_array( $label ) ) {
                            $section = $label['slug'];
                            $label   = $label['label'];
                        }

                        if ( 'subscription' == $section ) {
                            if ( 'off' == wpuf_get_option( 'show_subscriptions', 'wpuf_my_account', 'on' ) || 'on' != wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' ) ) {
                                continue;
                            }
                        }

                        if ( 'billing-address' == $section ) {
                            if ( 'off' == wpuf_get_option( 'show_billing_address', 'wpuf_my_account', 'on' ) || 'on' != wpuf_get_option( 'enable_payment', 'wpuf_payment', 'on' ) ) {
                                continue;
                            }
                        }

                        $default_active_tab = wpuf_get_option( 'account_page_active_tab', 'wpuf_my_account', 'dashboard' );
                        $active_tab         = false;

                        if ( ( isset( $_GET['section'] ) && $_GET['section'] == $section ) || ( !isset( $_GET['section'] ) && $default_active_tab == $section ) ) {
                            $active_tab = true;
                        }

                        $active = $active_tab ? $section . ' active' : $section;
                        echo sprintf(
                            '<li class="wpuf-menu-item %s"><a href="%s">%s</a></li>',
                            esc_attr( $active ),
                            esc_attr( add_query_arg( [ 'section' => $section ], get_permalink() ) ),
                            esc_attr( $label )
                         );
                    }
                }
            ?>
        </ul>
    </nav>

    <div class="wpuf-dashboard-content <?php echo ( !empty( $current_section ) ) ? esc_attr( $current_section ) : ''; ?>">
        <?php
            if ( !empty( $current_section ) && is_user_logged_in() ) {
                do_action( "wpuf_account_content_{$current_section}", $sections, $current_section );
            }
        ?>
    </div>
</div>
