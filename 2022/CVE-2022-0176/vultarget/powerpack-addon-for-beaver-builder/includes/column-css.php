<?php

function pp_column_render_css( $extensions ) {

    // if ( array_key_exists( 'corners', $extensions['col'] ) || in_array( 'corners', $extensions['col'] ) ) {
    //     add_filter( 'fl_builder_render_css', 'pp_column_round_corners_css', 10, 3 );
    // }
}

/** Corners */
function pp_column_round_corners_css( $css, $nodes, $global_settings ) {

    foreach ( $nodes['columns'] as $column ) {

        ob_start();
    ?>
        .fl-node-<?php echo $column->node; ?> .fl-col-content {
            <?php if ( isset( $column->settings->pp_round_corners ) ) { ?>
                <?php if ( $column->settings->pp_round_corners['top_left'] > 0 ) { ?>
                border-top-left-radius: <?php echo $column->settings->pp_round_corners['top_left']; ?>px;
                <?php } ?>
                <?php if ( $column->settings->pp_round_corners['top_right'] > 0 ) { ?>
                border-top-right-radius: <?php echo $column->settings->pp_round_corners['top_right']; ?>px;
                <?php } ?>
                <?php if ( $column->settings->pp_round_corners['bottom_left'] > 0 ) { ?>
                border-bottom-left-radius: <?php echo $column->settings->pp_round_corners['bottom_left']; ?>px;
                <?php } ?>
                <?php if ( $column->settings->pp_round_corners['bottom_right'] > 0 ) { ?>
                border-bottom-right-radius: <?php echo $column->settings->pp_round_corners['bottom_right']; ?>px;
                <?php } ?>
            <?php } ?>
        }

    <?php $css .= ob_get_clean();
    }

    return $css;
}
