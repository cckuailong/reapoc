<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Add some custom css for the recaptcha integration
 */
function cmplz_gravityforms_recaptcha_css() {
	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		?>
		<style>
			.cmplz-gf-recaptcha {
				background-image:url(<?php echo cmplz_placeholder('google-recaptcha')?>) !important;
				cursor:pointer;
				white-space: normal;
				text-transform: initial;
				z-index: 98;
				line-height: 23px;
				height:80px !important;
				background: #FFF;
				border: 0;
				border-radius: 3px;
				box-shadow: 0 0 1px 0 rgba(0,0,0,0.5), 0 1px 10px 0 rgba(0,0,0,0.15);
				display: flex;
				justify-content: center;
				align-items: center;
				background-repeat: no-repeat !important;
				background-size: cover !important;
				position: relative;
			}
			@media only screen and (max-width: 400px) {
				.cmplz-gf-recaptcha {
					height: 100px !important
				}
			}
		</style>
	<?php
	}
}
add_action( 'wp_footer', 'cmplz_gravityforms_recaptcha_css' );

/**
 * Initialize the form after cookies have been accepted, to ensure recaptcha is enabled.
 */

function cmplz_gravifyforms_initform() {
	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		if(!wp_script_is('jquery', 'done')) {
			wp_enqueue_script('jquery');
		}
		ob_start();
		?>
		<script>
			jQuery(document).ready(function ($) {
				//store the container where gf recaptcha resides
				var reCaptchaContainer = $('.ginput_recaptcha').closest('.gfield');
				reCaptchaContainer.append('<span class="cmplz-gf-recaptcha cmplz-accept-marketing"><?php _e("Click to accept reCaptcha validation.", 'complianz-gdpr')?></span>');

				$(document).on("cmplzRunAfterAllScripts", cmplz_cf7_fire_post_render);
				function cmplz_cf7_fire_post_render() {
					//fire a DomContentLoaded event, so the Contact Form 7 reCaptcha integration will work
					window.document.dispatchEvent(new Event("gform_post_render", {
						bubbles: true,
						cancelable: true
					}));
					$('.cmplz-gf-recaptcha').remove();
				}
			})
		</script>
		<?php
		$script = ob_get_clean();
		$script = str_replace(array('<script>', '</script>'), '', $script);
		wp_add_inline_script( 'jquery', $script );
	}
}
add_action( 'wp_enqueue_scripts', 'cmplz_gravifyforms_initform' );

/**
 * Add gravify forms as form type
 * @param $formtypes
 *
 * @return mixed
 */

function cmplz_gravityforms_form_types( $formtypes ) {
	$formtypes['gf_'] = 'gravity-forms';

	return $formtypes;
}
add_filter( 'cmplz_form_types', 'cmplz_gravityforms_form_types' );

function cmplz_gravityforms_get_plugin_forms( $input_forms ) {
	$forms = GFAPI::get_forms();
	if ( is_array( $forms ) ) {
		$forms = wp_list_pluck( $forms, "title", "id" );
		foreach ( $forms as $id => $title ) {
			$input_forms[ 'gf_' . $id ] = $title . " " . __( '(Gravity Forms)',
					'complianz-gdpr' );
		}
	}

	return $input_forms;
}

add_filter( 'cmplz_get_forms', 'cmplz_gravityforms_get_plugin_forms' );

function cmplz_gravityforms_add_consent_checkbox( $form_id ) {

	$form_id = str_replace( 'gf_', '', $form_id );
	$label
	         = __( 'To submit this form, you need to accept our Privacy Statement',
		'complianz-gdpr' );

	$form                   = GFAPI::get_form( $form_id );
	$new_field_id           = 1;
	$complianz_field_exists = false;
	foreach ( $form['fields'] as $field ) {
		$field_id = $field->id;
		if ( $field_id > $new_field_id ) {
			$new_field_id = $field_id;
		}
		if ( $field->adminLabel == 'complianz_consent' ) {
			$complianz_field_exists = true;
		};
	}
	$new_field_id ++;

	if ( ! $complianz_field_exists ) {
		$inputs  = array(
			array(
				'id'    => $new_field_id . '.1',
				'label' => __( 'Accept', 'complianz-gdpr' ),
			),
		);
		$choices = array(
			array(
				'text'       => __( 'Accept', 'complianz-gdpr' ),
				'value'      => __( 'Accept', 'complianz-gdpr' ),
				'isSelected' => false,
			),
		);

		$consent_box                   = new GF_Field_Checkbox();
		$consent_box->label            = $label;
		$consent_box->adminLabel       = 'complianz_consent';
		$consent_box->id               = $new_field_id;
		$consent_box->description      = '<a href="'
		                                 . COMPLIANZ::$document->get_permalink( 'privacy-statement',
				'eu', true ) . '">' . __( "Privacy Statement",
				"complianz-gdpr" ) . '</a>';
		$consent_box->isRequired       = true;
		$consent_box->choices          = $choices;
		$consent_box->inputs           = $inputs;
		$consent_box->conditionalLogic = false;
		$form['fields'][]              = $consent_box;

		GFAPI::update_form( $form );
	}
}

add_action( "cmplz_add_consent_box_gravity-forms",
	'cmplz_gravityforms_add_consent_checkbox' );
