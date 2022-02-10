<?php defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
/**
 *
 * API for Gutenberg blocks
 *
 * @return array documents (id, title, content)
 *
 */

add_action( 'rest_api_init', 'cmplz_documents_rest_route' );
function cmplz_documents_rest_route() {
	if ( isset($_GET['locale'])) {
		switch_to_locale( sanitize_text_field( $_GET['locale'] ) );
	}

	register_rest_route( 'complianz/v1', 'documents/', array(
		'methods'  => 'GET',
		'callback' => 'cmplz_rest_api_documents',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'complianz/v1', 'banner/', array(
		'methods'  => 'GET',
		'callback' => 'cmplz_rest_api_banner_data',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'complianz/v1', 'track/', array(
		'methods'  => 'POST',
		'callback' => 'cmplz_rest_api_ajax_track_status',
		'permission_callback' => '__return_true',
	) );

	register_rest_route( 'complianz/v1', 'manage_consent_html/', array(
		'methods'  => 'GET',
		'callback' => 'cmplz_rest_api_manage_consent_html',
		'permission_callback' => '__return_true',
	) );
}

/**
 * Track the status selected by the user, for statistics.
 *
 * */

function cmplz_rest_api_ajax_track_status( WP_REST_Request $request ) {
	$params = $request->get_json_params();
	$consented_categories = isset($params['consented_categories']) ? array_map('sanitize_title', $params['consented_categories']) : array('no_choice');
	$consenttype = isset($params['consenttype']) ? sanitize_title($params['consenttype']) : COMPLIANZ::$company->get_default_consenttype();

	foreach($consented_categories as $key => $consented_category ) {
		$consented_categories[$key] = str_replace(array('cmplz_rt_', 'cmplz_'), '', $consented_category);
	}
	do_action( 'cmplz_store_consent', $consented_categories, $consenttype );

	$response = json_encode( array(
		'success' => true,
	) );
	header( "Content-Type: application/json" );
	echo $response;
	exit;
}

/**
 * Get Banner data
 * @param WP_REST_Request $request
 */
function cmplz_rest_api_banner_data(WP_REST_Request $request){

	/**
	 * By default, the region which is returned is the region as selected in the wizard settings.
	 *
	 * */

	$data                       = apply_filters( 'cmplz_user_data', array() );
	$data['consenttype']        = apply_filters( 'cmplz_user_consenttype', COMPLIANZ::$company->get_default_consenttype() );
	$data['region']             = apply_filters( 'cmplz_user_region', COMPLIANZ::$company->get_default_region() );
	$data['version']            = cmplz_version;
	$data['forceEnableStats']   = apply_filters( 'cmplz_user_force_enable_stats', false );
	$data['do_not_track']       = apply_filters( 'cmplz_dnt_enabled', false );
	//We need this here because the integrations are not loaded yet, so the filter will return empty, overwriting the loaded data.
	//@todo: move this to the inline script  generation
	//and move all generic, not banner specific data away from the banner.

	unset( $data["set_cookies"] );
	$banner_id              = cmplz_get_default_banner_id();
	$banner                 = new CMPLZ_COOKIEBANNER( $banner_id );
	$data['banner_version'] = $banner->banner_version;
	$data                   = apply_filters('cmplz_ajax_loaded_banner_data', $data);

	$response               = json_encode( $data );
	header( "Content-Type: application/json" );
	echo $response;
	exit;
}

/**
 * @param WP_REST_Request $request
 *
 * @return array
 */
function cmplz_rest_api_documents( WP_REST_Request $request ) {
	$documents = COMPLIANZ::$document->get_required_pages();
	$output    = array();
	if ( is_array( $documents ) ) {
		foreach ( $documents as $region => $region_documents ) {

			foreach ( $region_documents as $type => $document ) {
				$html       = COMPLIANZ::$document->get_document_html( $type, $region );
				$region_ext = ( $region === 'eu' ) ? '' : '-' . $region;
				$output[]
				            = array(
					'id'      => $type . $region_ext,
					'title'   => $document['title'],
					'content' => $html,
				);
			}
		}
	}

	return $output;
}


/**
 * Output category consent checkboxes html
 */
function cmplz_rest_api_manage_consent_html( WP_REST_Request $request )
{
	$do_not_track = apply_filters( 'cmplz_dnt_enabled', false );
	if ( $do_not_track ) {
		$html
			= sprintf( _x( "We have received a privacy signal from your browser. For this reason we have set your privacy settings on this website to strictly necessary. If you want to have full functionality, please consider excluding %s from your privacy settings.",
			"cookie policy", "complianz-gdpr" ), site_url() );
	} else {
		$consenttype = apply_filters( 'cmplz_user_consenttype', COMPLIANZ::$company->get_default_consenttype() );
		$banner = new CMPLZ_COOKIEBANNER(apply_filters('cmplz_user_banner_id', cmplz_get_default_banner_id()));

		$use_revoke_button = false;
		if ( $consenttype === 'optin' && $banner->use_categories === 'no' ) {
			$use_revoke_button = true;
		} elseif ( $consenttype === 'optinstats' && $banner->use_categories_optinstats === 'no' ) {
			$use_revoke_button = true;
		} elseif ( $consenttype ==='optout' ){
			$use_revoke_button = true;
		}

		if ( $use_revoke_button ) {
			$html = cmplz_revoke_link();
		} else {
			$html = $banner->get_consent_checkboxes('document', $consenttype);
		}
	}

	$response = json_encode( $html );
	header( "Content-Type: application/json" );
	echo $response;
	exit;

}




