<?php

$label_position = isset( $form_settings['label_position'] ) ? $form_settings['label_position'] : 'left';
$form_layout    = isset( $form_settings['form_layout'] ) ? $form_settings['form_layout'] : 'layout1';
$theme_css      = isset( $form_settings['use_theme_css'] ) ? $form_settings['use_theme_css'] : 'wpuf-theme-style';
?>

<table class="form-table">

    <tr class="wpuf-label-position">
        <th><?php esc_html_e( 'Label Position', 'wp-user-frontend' ); ?></th>
        <td>
            <select name="wpuf_settings[label_position]">
                <?php
                $positions = [
                    'above'  => __( 'Above Element', 'wp-user-frontend' ),
                    'left'   => __( 'Left of Element', 'wp-user-frontend' ),
                    'right'  => __( 'Right of Element', 'wp-user-frontend' ),
                    'hidden' => __( 'Hidden', 'wp-user-frontend' ),
                ];

                foreach ( $positions as $to => $label ) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $to ), esc_attr( selected( $label_position, $to, false ) ), esc_html( $label ) );
                }
                ?>
            </select>

            <p class="description">
                <?php esc_html_e( 'Where the labels of the form should display', 'wp-user-frontend' ); ?>
            </p>
        </td>
    </tr>

    <tr class="wpuf-override-theme-css">
        <th><?php esc_html_e( 'Use Theme CSS', 'wp-user-frontend' ); ?></th>
        <td>
            <select name="wpuf_settings[use_theme_css]">
                <?php
                $options = [
                    'wpuf-style'         => __( 'No', 'wp-user-frontend' ),
                    'wpuf-theme-style'   => __( 'Yes', 'wp-user-frontend' ),
                ];

                foreach ( $options as $to => $label ) {
                    printf( '<option value="%s"%s>%s</option>', esc_attr( $to ), esc_attr( selected( $theme_css, $to, false ) ), esc_html( $label ) );
                }
                ?>
            </select>

            <p class="description">
                <?php esc_html_e( 'Selecting "Yes" will use your theme\'s style for form fields.', 'wp-user-frontend' ); ?>
            </p>
        </td>
    </tr>

    <?php if ( class_exists( 'WP_User_Frontend_Pro' ) ) { ?>
        <tr class="wpuf-form-layouts">
            <th><?php esc_html_e( 'Form Style', 'wp-user-frontend' ); ?></th>
            <td>
                <ul>
                    <?php
                    $layouts = [
                        'layout1' => WPUF_PRO_ASSET_URI . '/images/forms/layout1.png',
                        'layout2' => WPUF_PRO_ASSET_URI . '/images/forms/layout2.png',
                        'layout3' => WPUF_PRO_ASSET_URI . '/images/forms/layout3.png',
                        'layout4' => WPUF_PRO_ASSET_URI . '/images/forms/layout4.png',
                        'layout5' => WPUF_PRO_ASSET_URI . '/images/forms/layout5.png',
                    ];

                    foreach ( $layouts as $key => $image ) {
                        $active = '';

                        if ( $key == $form_layout ) {
                            $active = 'active';
                        }

                        $output = '<li class="' . $active . '">';
                        $output .= '<input type="radio" name="wpuf_settings[form_layout]" value="' . $key . '" ' . checked( $form_layout, $key, false ) . '>';
                        $output .= '<img src="' . $image . '" alt="">';
                        $output .= '</li>';

                        echo $output; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </ul>
            </td>
        </tr>
    <?php } ?>

</table>
