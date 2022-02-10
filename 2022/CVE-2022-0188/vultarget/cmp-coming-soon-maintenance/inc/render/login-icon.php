<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$login_url = wp_login_url();
$custom_login_url = get_option('niteoCS_custom_login_url', get_option( 'whl_page' ));

if ( $custom_login_url ) {
    $login_url = $custom_login_url;
} ?>

<div id="login-icon">
    <a href="<?php echo esc_url( $login_url );?>"><img src="<?php echo CMP_PLUGIN_URL . 'img/login-icon.svg';?>"/></a>
</div>