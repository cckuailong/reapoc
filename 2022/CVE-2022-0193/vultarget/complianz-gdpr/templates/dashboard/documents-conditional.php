<?php defined( 'ABSPATH' ) or die( "you do not have access to this page!" );
$docs = array(
	'privacy-statement' => array(
		'title' => __("Privacy Statements", "complianz-gdpr"),
		'regions' => array('eu', 'us', 'uk', 'ca', 'za', 'au', 'br'),
		'read-more' => 'https://complianz.io/definition/what-is-a-privacy-statement/',
		'subscription' => 'premium',
	),
	'cookie-statement' => array(
		'title' => __("Cookie Policy", 'complianz-gdpr'),
		'regions' => array('eu', 'us', 'uk', 'ca', 'za', 'au', 'br'),
		'read-more' => ' https://complianz.io/definition/what-is-a-cookie-policy/',
		'subscription' => 'free',
	),
	'impressum' => array(
		'title' => __("Impressum", 'complianz-gdpr'),
		'regions' => array('eu'),
		'read-more' => 'https://complianz.io/definition/what-is-an-impressum/',
		'subscription' => 'premium',
	),
	'do-not-sell-my-info' => array(
		'title' => __("Do Not Sell My Personal Information", 'complianz-gdpr'),
		'regions' => array('us'),
		'read-more' => 'https://complianz.io/definition/what-is-do-not-sell-my-personal-information/',
		'subscription' => 'free',
	),
	'privacy-statement-for-children' => array(
		'title' => __("Privacy Statement for Children", 'complianz-gdpr'),
		'regions' => array('us', 'uk', 'ca', 'za', 'au', 'br'),
		'read-more' => 'https://complianz.io/definition/what-is-a-privacy-statement-for-children/',
		'subscription' => 'premium',
	),
);

if (!class_exists('COMPLIANZ_TC') ) {
	$title = __("Terms and Conditions",'complianz-gdpr');
	$status = 'disabled';
	$shortcode_icon = cmplz_icon( 'shortcode', 'disabled' , __( 'Click to copy the document shortcode', 'complianz-gdpr' ));
	$sync_icon = cmplz_icon('sync', 'disabled');
	$page_exists = cmplz_icon('bullet', 'disabled');
	$generated = '<a href="'.add_query_arg( array('s'=>'complianz+terms+conditions+stand-alone', 'tab'=>'search','type'=>'term'),  admin_url('plugin-install.php') ).'">'.__('Install', 'complianz-gdpr').'</a>';
	$args = array(
		'status' => $status,
		'title' => $title,
		'page_exists' => $page_exists,
		'sync_icon' => $sync_icon,
		'shortcode_icon' => $shortcode_icon,
		'generated' => $generated,
	);
	echo cmplz_get_template('dashboard/documents-row.php', $args);
}

foreach ($docs as $index => $doc) {
	if ( $doc['subscription'] === 'free' ) continue;

	if (array_search($region, $doc['regions']) !== false) {
		$args = array(
			'status' => 'missing',
			'title' => $doc['title'],
			'page_exists' => '',
			'sync_icon' => cmplz_icon( 'sync', 'disabled' ),
			'shortcode_icon' => cmplz_icon( 'shortcode', 'disabled' ),
			'generated' => '<a href="'.$doc['read-more'].'" target="_blank" class="cmplz-premium">'.__("Read more","complianz-gdpr").'</a>',
		);
		echo cmplz_get_template('dashboard/documents-row.php', $args);
	}
}

$args = array(
	'status' => 'header',
	'title' => '<h3>'.__("Other regions", "complianz-gdpr").'</h3>',
	'page_exists' => '',
	'sync_icon' => '',
	'shortcode_icon' => '',
	'generated' => '<a href="https://complianz.io/features/" target="_blank" >'.__("Read more", "complianz-gdpr").'</a>',
);
echo cmplz_get_template('dashboard/documents-row.php', $args);

foreach ($docs as $key => $doc) {
	if ( $key === 'disclaimer' || $key === 'impressum' ) continue;

	if (($key = array_search($region, $doc['regions'])) !== false) {
		unset($doc['regions'][$key]);
	}
	echo '<div class="cmplz-document flags">';
	echo '<div>'.$doc['title'].'</div><div class="cmplz-flags-container">';
	foreach ( $doc['regions'] as $flag_region ) {
		echo '<span>';
		echo cmplz_flag( $flag_region , false );
		echo '</span>';
	}
	echo '</div></div>';
}
