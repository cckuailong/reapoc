<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:       Site Reviews
 * Plugin URI:        https://wordpress.org/plugins/site-reviews
 * Description:       Receive and display reviews on your website
 * Version:           5.17.2
 * Author:            Paul Ryley
 * Author URI:        https://geminilabs.io
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.5
 * Requires PHP:      5.6.20
 * Text Domain:       site-reviews
 * Domain Path:       languages
 */
defined('ABSPATH') || die;

if (!class_exists('GL_Plugin_Check_v5')) {
    require_once __DIR__.'/activate.php';
}
if ((new GL_Plugin_Check_v5(__FILE__))->canProceed()) {
    require_once __DIR__.'/autoload.php';
    require_once __DIR__.'/compatibility.php';
    require_once __DIR__.'/deprecated.php';
    require_once __DIR__.'/helpers.php';
    require_once __DIR__.'/migration.php';
    $app = GeminiLabs\SiteReviews\Application::load();
    $app->make('Provider')->register($app);
    register_deactivation_hook(__FILE__, array($app, 'deactivate'));
    register_shutdown_function(array($app, 'catchFatalError'));
    $app->init();
}
