<?php

function pp_column_settings_init() {

    require_once BB_POWERPACK_DIR . 'includes/column-settings.php';
    require_once BB_POWERPACK_DIR . 'includes/column-css.php';

    $extensions = BB_PowerPack_Admin_Settings::get_enabled_extensions();

    pp_column_register_settings( $extensions );
    pp_column_render_css( $extensions );

}

pp_column_settings_init();
