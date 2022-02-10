/**
* Block Grid
*/
const {
    jQuery: $,
    grecaptcha,
} = window;


const $doc = $( document );

/**
 * Parsley form validation.
 */
$doc.on( 'initBlocks.ive', () => {
    $( '.ive-form:not(.ive-form-ready)' ).each( function() {
        const $form = $( this );

        $form.addClass( 'ive-form-ready' );

        $form.children( 'form' ).parsley( {
            errorsContainer( parsleyField ) {
                const $parent = parsleyField.$element.closest( '.ive-form-field-name-first, .ive-form-field-name-last, .ive-form-field-email-primary, .ive-form-field-email-confirm, .ive-form-field' );

                if ( $parent.length ) {
                    return $parent;
                }

                return parsleyField;
            },
        } );
    } );
} );

/**
 * Parsley custom validations.
 */
window.Parsley.addValidator( 'confirmEmail', {
    requirementType: 'string',
    validateString( value, refOrValue ) {
        const $reference = $( refOrValue );

        if ( $reference.length ) {
            return value === $reference.val();
        }

        return value === refOrValue;
    },
} );

/**
 * Google reCaptcha
 */
if ( 'undefined' !== typeof grecaptcha ) {
    grecaptcha.ready( () => {
        const recaptchaFields = $( '[name="ive_form_google_recaptcha"]' );

        if ( ! recaptchaFields.length ) {
            return;
        }

        recaptchaFields.each( function() {
            const $recaptchaTokenField = $( this );

            grecaptcha.execute( ive_form_captcha.googleReCaptchaAPISiteKey, { action: 'ive' } ).then( ( token ) => {
                $recaptchaTokenField.val( token );
            } );
        } );
    } );
}
