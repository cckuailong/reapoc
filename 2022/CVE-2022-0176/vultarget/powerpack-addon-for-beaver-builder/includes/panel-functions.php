<?php

/**
 * Panel search
 */
function pp_panel_search()
{
    if ( class_exists( 'FLBuilderUIContentPanel' ) ) {
        return;
    }
    $panel_search = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_search_box');

    if ( $panel_search === false ) {
        BB_PowerPack_Admin_Settings::update_option('bb_powerpack_search_box', 1);
        $panel_search = 1;
    }

    return $panel_search;
}

/**
 * Preview button on frontend
 */
function pp_preview_button()
{
    if ( class_exists( 'FLBuilderUIContentPanel' ) ) {
        return;
    }
    $quick_preview = BB_PowerPack_Admin_Settings::get_option('bb_powerpack_quick_preview');

    if ( $quick_preview === false ) {
        BB_PowerPack_Admin_Settings::update_option('bb_powerpack_quick_preview', 1);
        $quick_preview = 1;
    }

    if ( FLBuilderModel::is_builder_active() && $quick_preview == 1 ) {
    ?>

    <div class="pp-preview-button" title="<?php _e('Preview', 'bb-powerpack-lite'); ?>">
        <div class="pp-preview-button-wrap">
            <span class="pp-preview-trigger fa fa-eye"></span>
        </div>
    </div>

    <?php
    }
}
add_action( 'wp_footer', 'pp_preview_button' );
