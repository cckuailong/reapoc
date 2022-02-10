<?php
$current_tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'extensions';
?>

<div class="wrap">

    <h2><?php _e('PowerPack Settings'); ?></h2>

    <?php BB_PowerPack_Admin_Settings::render_update_message(); ?>

    <form method="post" id="pp-settings-form" action="<?php echo self::get_form_action( '&tab=' . $current_tab ); ?>">

        <div class="icon32 icon32-pp-settings" id="icon-pp"><br /></div>

        <h2 class="nav-tab-wrapper pp-nav-tab-wrapper">
            <?php if ( is_network_admin() || !is_multisite() ) { ?>
                <a href="<?php echo self::get_form_action( '&tab=extensions' ); ?>" class="nav-tab<?php echo ( $current_tab == 'extensions' ? ' nav-tab-active' : '' ); ?>"><?php _e( 'Extensions', 'bb-powerpack-lite' ); ?></a>
            <?php } ?>
            <a href="<?php echo self::get_form_action( '&tab=modules' ); ?>" class="nav-tab<?php echo ( $current_tab == 'modules' ? ' nav-tab-active' : '' ); ?>"><?php _e( 'Modules', 'bb-powerpack-lite' ); ?></a>
            <a href="<?php echo self::get_form_action( '&tab=templates' ); ?>" class="nav-tab<?php echo ( $current_tab == 'templates' ? ' nav-tab-active' : '' ); ?>"><?php _e( 'Templates', 'bb-powerpack-lite' ); ?></a>
            <a href="<?php echo self::get_form_action( '&tab=integration' ); ?>" class="nav-tab<?php echo ( $current_tab == 'integration' ? ' nav-tab-active' : '' ); ?>"><?php _e( 'Integration', 'bb-powerpack-lite' ); ?></a>
            <a href="https://wpbeaveraddons.com/upgrade/?utm_medium=bb-powerpack-lite&utm_source=module-settings&utm_campaign=module-settings" class="nav-tab" target="_blank"><?php _e( 'Upgrade', 'bb-powerpack-lite' ); ?></a>
        </h2>

        <?php

            // Extensions settings.
            if ( 'extensions' == $current_tab ) {
                include BB_POWERPACK_DIR . 'includes/admin-settings-extensions.php';
            }
            if ( 'modules' == $current_tab ) {
                include BB_POWERPACK_DIR . 'includes/admin-settings-modules.php';
            }
            if ( 'templates' == $current_tab ) {
                include BB_POWERPACK_DIR . 'includes/admin-settings-templates.php';
			}
			
			// Integration settings.
			if ( 'integration' == $current_tab ) {
				include BB_POWERPACK_DIR . 'includes/admin-settings-integration.php';
			}

        ?>

    </form>

    <hr />

    <h2><?php _e('Support', 'bb-powerpack-lite'); ?></h2>
    <p><?php _e('For submitting any support queries, feedback, bug reports or feature requests, please visit <a href="https://wpbeaveraddons.com/contact/" target="_blank">this link</a>', 'bb-powerpack-lite'); ?></p>

</div>
