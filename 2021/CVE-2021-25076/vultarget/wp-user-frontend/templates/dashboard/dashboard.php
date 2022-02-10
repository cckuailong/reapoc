<p><?php
    global $current_user;

    printf(
        wp_kses_post( __( 'Hello %1$s, (not %1$s? <a href="%2$s">Sign out</a>)', 'wp-user-frontend' ) ),
        '<strong>' . esc_html( $current_user->display_name ) . '</strong>',
        esc_url( wp_logout_url( get_permalink() ) )
     );
?></p>

<p><?php

    $tabs = apply_filters( 'wpuf_my_account_tab_links', wpuf_get_account_sections() );

    if ( 'off' == wpuf_get_option( 'show_subscriptions', 'wpuf_my_account', 'on' ) ) {
        unset( $tabs['subscription'] );
    }

    $links      = '';
    $count      = 1;
    $total_tabs = count( $tabs );

    foreach ( $tabs as $section => $label ) {
        // backward compatibility
        if ( is_array( $label ) ) {
            $section = $label['slug'];
            $label   = $label['label'];
        }

        if ( $total_tabs == $count ) {
            $links .= ' <a href="' . esc_url( add_query_arg( [ 'section' => $section ], get_permalink() ) ) . '">' . $label . '</a>';
            continue;
        }

        $links .= '<a href="' . esc_url( add_query_arg( [ 'section' => $section ], get_permalink() ) ) . '">' . $label . '</a>, ';
        $count++;
    }

    printf(
        wp_kses_post( __( 'From your account dashboard you can view your dashboard, manage your %s', 'wp-user-frontend' ) ),
        wp_kses( $links, [ 'a' => [ 'href' => [] ] ] )
     );
?></p>
