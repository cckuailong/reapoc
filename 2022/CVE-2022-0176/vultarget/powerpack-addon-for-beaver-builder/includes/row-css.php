<?php

function pp_row_render_css( $extensions ) {

    if ( array_key_exists( 'separators', $extensions['row'] ) || in_array( 'separators', $extensions['row'] ) ) {
        add_filter( 'fl_builder_render_css', 'pp_row_separators_css', 10, 3 );
    }
    
}

function pp_row_separators_css( $css, $nodes, $global_settings ) {
    foreach ( $nodes['rows'] as $row ) {
        ob_start();
        ?>

        .fl-builder-row-settings #fl-field-separator_position {
            display: none !important;
        }
        <?php if ( 'none' != $row->settings->separator_type || 'none' != $row->settings->separator_type_bottom ) { ?>

            <?php $scaleY = '-webkit-transform: scaleY(-1); -moz-transform: scaleY(-1); -ms-transform: scaleY(-1); -o-transform: scaleY(-1); transform: scaleY(-1);'; ?>
            <?php $scaleX = '-webkit-transform: scaleX(-1); -moz-transform: scaleX(-1); -ms-transform: scaleX(-1); -o-transform: scaleX(-1); transform: scaleX(-1);'; ?>

            .fl-node-<?php echo $row->node; ?> .pp-row-separator {
                position: absolute;
                left: 0;
                width: 100%;
                z-index: 1;
            }
            .pp-previewing .fl-node-<?php echo $row->node; ?> .pp-row-separator {
                z-index: 2001;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator svg {
                position: absolute;
                left: 0;
                width: 100%;
            }
			.fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
				margin-top: -1px;
			}
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                top: 0;
                bottom: auto;
            }
			.fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                margin-bottom: -1px;
			}
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                top: auto;
                bottom: 0;
            }
            <?php if ( 'triangle' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'triangle_shadow' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle-shadow {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'triangle_left' == $row->settings->separator_type ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-big-triangle-left {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'triangle_right' == $row->settings->separator_type ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-big-triangle-right {
                -webkit-transform: scale(-1);
                -moz-transform: scale(-1);
                -ms-transform: scale(-1);
                -o-transform: scale(-1);
                transform: scale(-1);
            }
            <?php } ?>
            <?php if ( 'triangle_small' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-small-triangle {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'tilt_right' == $row->settings->separator_type || 'tilt_right' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-tilt-right,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-tilt-right {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'curve' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-curve {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'wave' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-wave {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'cloud' == $row->settings->separator_type ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-cloud {
                <?php echo $scaleY; ?>
            }
            <?php } ?>
            <?php if ( 'slit' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-slit {
                <?php echo $scaleY; ?>
            }
            <?php } ?>

            <?php if ( 'water' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-water-separator {
                <?php echo $scaleY; ?>
            }
            <?php } ?>

            <?php if ( 'triangle_right' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-big-triangle-right {
                <?php echo $scaleX; ?>
            }
            <?php } ?>
            <?php if ( 'tilt_right' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg.pp-tilt-right {
                <?php echo $scaleX; ?>
            }
            <?php } ?>

            <?php if ( 'tilt_left' == $row->settings->separator_type ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg.pp-tilt-left {
                -webkit-transform: scale(-1);
                -moz-transform: scale(-1);
                -ms-transform: scale(-1);
                -o-transform: scale(-1);
                transform: scale(-1);
            }
            <?php } ?>
            <?php if ( 'zigzag' == $row->settings->separator_type || 'zigzag' == $row->settings->separator_type_bottom ) { ?>
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:after {
                content: '';
                pointer-events: none;
                position: absolute;
                right: 0;
                left: 0;
                z-index: 1;
                display: block;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:after {
                height: <?php echo $row->settings->separator_height; ?>px;
                background-size: <?php echo $row->settings->separator_height; ?>px 100%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:before,
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:after {
                height: <?php echo $row->settings->separator_height_bottom; ?>px;
                background-size: <?php echo $row->settings->separator_height_bottom; ?>px 100%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator .pp-zigzag:after {
                top: 100%;
                background-position: 50%;
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-top .pp-zigzag:after {
                background-image: -webkit-gradient(linear, 0 0, 300% 100%, color-stop(0.25, #<?php echo $row->settings->separator_color; ?>), color-stop(0.25, #<?php echo $row->settings->separator_color; ?>));
                background-image: linear-gradient(135deg, #<?php echo $row->settings->separator_color; ?> 25%, transparent 25%), linear-gradient(225deg, #<?php echo $row->settings->separator_color; ?> 25%, transparent 25%);
            }
            .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom .pp-zigzag:after {
                background-image: -webkit-gradient(linear, 0 0, 300% 100%, color-stop(0.25, #<?php echo $row->settings->separator_color_bottom; ?>), color-stop(0.25, #<?php echo $row->settings->separator_color_bottom; ?>));
                background-image: linear-gradient(135deg, #<?php echo $row->settings->separator_color_bottom; ?> 25%, transparent 25%), linear-gradient(225deg, #<?php echo $row->settings->separator_color_bottom; ?> 25%, transparent 25%);
            }
            <?php } ?>

            @media only screen and (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
                    <?php if ( 'no' == $row->settings->separator_tablet ) { ?>
                        display: none;
                    <?php } ?>
                }
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    <?php if ( 'no' == $row->settings->separator_tablet_bottom ) { ?>
                        display: none;
                    <?php } ?>
                }
                <?php if ( 'yes' == $row->settings->separator_tablet && $row->settings->separator_height_tablet > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                        height: <?php echo $row->settings->separator_height_tablet; ?>px;
                    }
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_tablet_bottom && $row->settings->separator_height_tablet_bottom > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                        height: <?php echo $row->settings->separator_height_tablet_bottom; ?>px;
                    }
                <?php } ?>
            }
            @media only screen and (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-top {
                    <?php if ( 'no' == $row->settings->separator_mobile ) { ?>
                        display: none;
                    <?php } ?>
                }
                .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom {
                    <?php if ( 'no' == $row->settings->separator_mobile_bottom ) { ?>
                        display: none;
                    <?php } ?>
                }
                <?php if ( 'yes' == $row->settings->separator_mobile && $row->settings->separator_height_mobile > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-top svg {
                        height: <?php echo $row->settings->separator_height_mobile; ?>px;
                    }
                <?php } ?>
                <?php if ( 'yes' == $row->settings->separator_mobile_bottom && $row->settings->separator_height_mobile_bottom > 0 ) { ?>
                    .fl-node-<?php echo $row->node; ?> .pp-row-separator-bottom svg {
                        height: <?php echo $row->settings->separator_height_mobile_bottom; ?>px;
                    }
                <?php } ?>
            }
        <?php } ?>

        <?php
        $css .= ob_get_clean();
    }

    return $css;
}