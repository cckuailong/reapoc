<?php

defined( 'ABSPATH' ) or die();

add_filter( 'weglot_get_regex_checkers', 'cmplz_weglot_add_regex_checkers' );
function cmplz_weglot_add_regex_checkers( $regex_checkers ) {
	// JSON
	$regex_checkers[] = new \Weglot\Parser\Check\Regex\RegexChecker( '#var complianz = ({.*})#', 'JSON', 1,
	array('message_optout', 'message_optin', 'accept_informational' , 'accept_all', 'view_preferences', 'dismiss', 'tagmanager_categories', 'category_functional', 'category_all' , 'category_stats', 'category_prefs', 'accept', 'revoke', 'readmore_optin' , 'readmore_optout', 'readmore_optout_dnsmpi', 'readmore_privacy', 'readmore_impressum')
);

	return $regex_checkers;
}
