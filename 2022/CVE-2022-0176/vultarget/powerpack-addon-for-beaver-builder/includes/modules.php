<?php
/**
 * List of modules.
 */
$modules = array(
	'modules/pp-heading/pp-heading.php',
	'modules/pp-dual-button/pp-dual-button.php',
	'modules/pp-spacer/pp-spacer.php',
	'modules/pp-iconlist/pp-iconlist.php',
	'modules/pp-infobox/pp-infobox.php',
	'modules/pp-infolist/pp-infolist.php',
	'modules/pp-fancy-heading/pp-fancy-heading.php',
	'modules/pp-business-hours/pp-business-hours.php',
	'modules/pp-line-separator/pp-line-separator.php',
	'modules/pp-facebook-page/pp-facebook-page.php',
    'modules/pp-facebook-button/pp-facebook-button.php',
    'modules/pp-facebook-embed/pp-facebook-embed.php',
	'modules/pp-facebook-comments/pp-facebook-comments.php',
	'modules/pp-twitter-tweet/pp-twitter-tweet.php',
    'modules/pp-twitter-grid/pp-twitter-grid.php',
    'modules/pp-twitter-timeline/pp-twitter-timeline.php',
    'modules/pp-twitter-buttons/pp-twitter-buttons.php',
    'modules/pp-breadcrumbs/pp-breadcrumbs.php',
);

/* Form Modules */
if ( class_exists( 'WPCF7_ContactForm' ) ) {
	$modules[] = 'modules/pp-contact-form-7/pp-contact-form-7.php';
}

foreach ( $modules as $module ) {
	require_once BB_POWERPACK_DIR . $module;
}