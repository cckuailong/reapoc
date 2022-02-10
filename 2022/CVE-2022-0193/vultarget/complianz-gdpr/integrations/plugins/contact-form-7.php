<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
function cmplz_cf7_initDomContentLoaded() {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return;

	if (class_exists('IQFix_WPCF7_Deity')) return;

	$service = WPCF7_RECAPTCHA::get_instance();
	if ( $service->is_active() ) {
		if (version_compare(WPCF7_VERSION, 5.2, '>=') ) {
			?>
			<script>
				jQuery(document).ready(function ($) {
					$(document).on("cmplzRunAfterAllScripts", cmplz_cf7_fire_domContentLoadedEvent);

					function cmplz_cf7_fire_domContentLoadedEvent() {
						wpcf7_recaptcha.execute = function (action) {
							grecaptcha.execute(
								wpcf7_recaptcha.sitekey,
								{action: action}
							).then(function (token) {
								var event = new CustomEvent('wpcf7grecaptchaexecuted', {
									detail: {
										action: action,
										token: token,
									},
								});

								document.dispatchEvent(event);
							});
						};

						wpcf7_recaptcha.execute_on_homepage = function () {
							wpcf7_recaptcha.execute(wpcf7_recaptcha.actions['homepage']);
						};

						wpcf7_recaptcha.execute_on_contactform = function () {
							wpcf7_recaptcha.execute(wpcf7_recaptcha.actions['contactform']);
						};

						grecaptcha.ready(
							wpcf7_recaptcha.execute_on_homepage
						);

						document.addEventListener('change',
							wpcf7_recaptcha.execute_on_contactform
						);

						document.addEventListener('wpcf7submit',
							wpcf7_recaptcha.execute_on_homepage
						);
					}
				})
			</script>
			<?php
		} else {
			?>
			<script>
				jQuery(document).ready(function ($) {
					$(document).on("cmplzRunAfterAllScripts", cmplz_cf7_fire_domContentLoadedEvent);

					function cmplz_cf7_fire_domContentLoadedEvent() {
						//fire a DomContentLoaded event, so the Contact Form 7 reCaptcha integration will work
						window.document.dispatchEvent(new Event("DOMContentLoaded", {
							bubbles: true,
							cancelable: true
						}));
					}
				})
			</script>
			<?php
		}
	}
}
add_action( 'wp_footer', 'cmplz_cf7_initDomContentLoaded' );


/**
 * Customize the error message on submission of the form before consent
 *
 * @param $message
 * @param $status
 *
 * @return string
 */
function cmplz_contactform7_errormessage( $message, $status ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $message;

	if ( $status === 'spam' ) {


		if ( version_compare(WPCF7_VERSION, 5.4, '<') ) {
			$message = apply_filters( 'cmplz_accept_cookies_contactform7', __( 'Click to accept marketing cookies and enable this form', 'complianz-gdpr' ) );
			$message = '<span class="cmplz-blocked-content-notice cmplz-accept-marketing"><a href="#" role="button">' . $message . '</a></span>';
		} else {
			$message = apply_filters( 'cmplz_accept_cookies_contactform7', __( 'Please accept marketing cookies to enable this form', 'complianz-gdpr' ) );
		}
	}

	return $message;
}

add_filter( 'wpcf7_display_message', 'cmplz_contactform7_errormessage', 20, 2 );

/**
 * Add the CF7 form type
 *
 * @param $formtypes
 *
 * @return mixed
 */
function cmplz_contactform7_form_types( $formtypes ) {
	$formtypes['cf7_'] = 'contact-form-7';

	return $formtypes;
}

add_filter( 'cmplz_form_types', 'cmplz_contactform7_form_types' );


/**
 * Conditionally add the dependency from the CF 7 inline script to the .js file
 */

add_filter( 'cmplz_dependencies', 'cmplz_contactform7_dependencies' );
function cmplz_contactform7_dependencies( $tags ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $tags;

	if (class_exists('IQFix_WPCF7_Deity')) return $tags;

	$service = WPCF7_RECAPTCHA::get_instance();
	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		if ( $service->is_active() ) {
			if (version_compare(WPCF7_VERSION, 5.2, '>=')){
				$tags['recaptcha/api.js'] = 'modules/recaptcha/script.js';
			} else {
				$tags['recaptcha/api.js'] = 'grecaptcha';
			}
		}
	}
	return $tags;
}

add_filter( 'cmplz_known_script_tags', 'cmplz_contactform7_script' );
function cmplz_contactform7_script( $tags ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $tags;

	$service = WPCF7_RECAPTCHA::get_instance();
	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		if ( $service->is_active() ) {
			$tags[] = 'modules/recaptcha/script.js';
			$tags[] = 'recaptcha/index.js';
			$tags[] = 'recaptcha/api.js';
		}
	}
	return $tags;
}


/**
 * Get list of CF7 contact forms
 *
 * @param $input_forms
 *
 * @return mixed
 */

function cmplz_contactform7_get_plugin_forms( $input_forms ) {
	$forms = get_posts( array( 'post_type' => 'wpcf7_contact_form' ) );
	$forms = wp_list_pluck( $forms, "post_title", "ID" );
	foreach ( $forms as $id => $title ) {
		$input_forms[ 'cf7_' . $id ] = $title . " " . __( '(Contact form 7)', 'complianz-gdpr' );
	}

	return $input_forms;
}

add_filter( 'cmplz_get_forms', 'cmplz_contactform7_get_plugin_forms' );

/**
 * Add consent checkbox to CF 7
 *
 * @param $form_id
 */
function cmplz_contactform7_add_consent_checkbox( $form_id ) {
	$form_id = str_replace( 'cf7_', '', $form_id );

	$warning = 'acceptance_as_validation: on';
	$label
	         = sprintf( __( 'To submit this form, you need to accept our %sPrivacy Statement%s',
		'complianz-gdpr' ),
		'<a href="' . COMPLIANZ::$document->get_permalink( 'privacy-statement',
			'eu', true ) . '">', '</a>' );

	$tag = "\n" . '[acceptance cmplz-acceptance]' . $label . '[/acceptance]'
	       . "\n\n";

	$contact_form = wpcf7_contact_form( $form_id );

	if ( ! $contact_form ) {
		return;
	}

	$properties = $contact_form->get_properties();
	$title      = $contact_form->title();
	$locale     = $contact_form->locale();

	//check if it's already there
	if ( strpos( $properties['form'], '[acceptance' ) === false ) {
		$properties['form'] = str_replace( '[submit', $tag . '[submit',
			$properties['form'] );
	}

	if ( strpos( $properties['additional_settings'], $warning ) === false ) {
		$properties['additional_settings'] .= "\n" . $warning;
	}

	//replace [submit
	$args = array(
		'id'                  => $form_id,
		'title'               => $title,
		'locale'              => $locale,
		'form'                => $properties['form'],
		'mail'                => $properties['mail'],
		'mail_2'              => $properties['mail_2'],
		'messages'            => $properties['messages'],
		'additional_settings' => $properties['additional_settings'],
	);
	remove_action( 'wpcf7_after_save', 'wpcf7_mch_save_mailchimp' );
	wpcf7_save_contact_form( $args );
}

add_action( "cmplz_add_consent_box_contact-form-7",
	'cmplz_contactform7_add_consent_checkbox' );


/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_contactform7_detected_services( $services ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $services;

	$recaptcha = WPCF7_RECAPTCHA::get_instance();

	if ( $recaptcha->is_active()
	     && ! in_array( 'google-recaptcha', $services )
	) {
		$services[] = 'google-recaptcha';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_contactform7_detected_services' );

/**
 * Add a warning that we're dropping support for further Contact Form 7 changes
 *
 * @param array $warnings
 *
 * @return array
 */
function cmplz_cf7_warnings_types($warnings)
{
	$warnings['contact-form-7'] = array(
		'plus_one' => true,
		'warning_condition' => '_true_',
		'open' => __( 'Due to continuous breaking changes in Contact Form 7 we are dropping the CF7 integration as of CF7 5.4. We have concluded that the only viable solution is for Contact Form 7 to integrate with the WP Consent API.', 'complianz-gdpr' ).cmplz_read_more('https://complianz.io/why-the-wp-consent-api-is-important-a-case-study-with-cf7-and-recaptcha/'),
	);

	return $warnings;
}
add_filter('cmplz_warning_types', 'cmplz_cf7_warnings_types');

/**
 * Check if cf7 recaptch activate for >5.4 versions.
 * @return bool
 */

function cmplz_cf7_recaptcha_active(){
	//it works before 5.4.
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '<')) {
		return false;
	}
	$recaptcha = WPCF7_RECAPTCHA::get_instance();
	if ( $recaptcha->is_active() ) {
		return true;
	}
	return false;
}
