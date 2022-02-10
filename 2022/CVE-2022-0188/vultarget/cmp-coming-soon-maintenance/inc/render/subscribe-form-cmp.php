<?php 
// process emails first
$response = $this->niteo_subscribe( true );

// get current theme
$theme = $this->cmp_selectedTheme();
// get GDPR message
if ( $popup ) {
    $subscribe_label = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_subscribe_label_popup') ), 'Popup Subscribe GDPR Message' );
    $gdpr_checkbox = get_option( 'niteoCS_subscribe_gdpr_checkbox_popup', '0' );
} else {
    $subscribe_label = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_subscribe_label') ), 'Subscribe GDPR Message' );
    $gdpr_checkbox = get_option( 'niteoCS_subscribe_gdpr_checkbox', '0' );
}

//  get translation if exists
$translation = $this->cmp_wpml_niteoCS_translation();

$placeholder            = stripslashes( $translation[4]['translation'] );
$placeholder_firstname  = stripslashes( $translation[10]['translation'] );
$placeholder_lastname   = stripslashes( $translation[11]['translation'] );

$subscribe              = stripslashes( $translation[12]['translation'] );
$missing_gdpr             = stripslashes( $translation[13]['translation'] );


// overwrite it with theme specific requirements
if ( $theme == 'stylo' && !$popup ) {
    $placeholder            =  '&#xf0e0;  ' . $placeholder;
    $placeholder_firstname  =  '&#xf007;  ' . $placeholder_firstname;
    $placeholder_lastname   =  '&#xf007;  ' . $placeholder_lastname;
}

// overwrite it with theme specific requirements
if ( $theme == 'pluto' && !$popup) {
    $placeholder            =  '&#xf0e0;  ' . $placeholder;
}

$submit = stripslashes( $translation[8]['translation'] );
// set submit icon / text
if ( !$popup ) {
    switch ($theme) {
        case 'postery':
            $submit = '&#xf1d8;';
            break;
        case 'juno':
            $submit = '&#xf1d8;';
            break;
        case 'agency':
            $submit = '&#xf105;';
            break;
        case 'libra':
            $submit = '&#xf1d8;';
            break;
        
        default:
            break;
    } 

}
ob_start();
$popup = $popup ? '-popup' : '';
?>

<form id="subscribe-form<?php echo $popup;?>" method="post" class="cmp-subscribe<?php echo $popup;?>">
    <div class="cmp-form-inputs">

        <?php wp_nonce_field('cmp_subscribe','cmp_subscribe_field' . $popup); ?>
        <?php
        // display placeholders or labels
        switch ( $label ) {
            case TRUE:
                if ( $firstname === TRUE ) { ?>
                    <div class="firstname input-wrapper">
                        <label for="firstname-subscribe<?php echo $popup;?>"><?php echo esc_attr( $placeholder_firstname );?></label>
                        <input type="text" id="firstname-subscribe<?php echo $popup;?>" name="cmp_firstname<?php echo $popup;?>">
                    </div>
                    <?php 
                }

                if ( $lastname === TRUE ) { ?>
                    <div class="lastname input-wrapper">
                        <label for="lastname-subscribe<?php echo $popup;?>"><?php echo esc_attr( $placeholder_lastname );?></label>
                        <input type="text" id="lastname-subscribe<?php echo $popup;?>" name="cmp_lastname<?php echo $popup;?>">
                    </div>
                    <?php 
                } ?>
                <div class="email input-wrapper">
                    <label for="email-subscribe<?php echo $popup;?>"><?php echo esc_attr( $placeholder );?></label>
                    <input type="email" id="email-subscribe<?php echo $popup;?>" name="email<?php echo $popup;?>" required>
                </div>
                <?php 
                break;

            case FALSE: 
                if ( $firstname === TRUE ) { ?>
                    <input type="text" id="firstname-subscribe<?php echo $popup;?>" name="cmp_firstname<?php echo $popup;?>" placeholder="<?php echo esc_attr( $placeholder_firstname );?>">
                    <?php 
                }

                if ( $lastname === TRUE ) { ?>
                    <input type="text" id="lastname-subscribe<?php echo $popup;?>" name="cmp_lastname<?php echo $popup;?>" placeholder="<?php echo esc_attr( $placeholder_lastname );?>">
                    <?php 
                } ?>

                <input type="email" id="email-subscribe<?php echo $popup;?>" name="email<?php echo $popup;?>" placeholder="<?php echo esc_attr( $placeholder );?>" required> 
                <?php 
                break;

            default:
                break;
        } 

        switch ( $theme ) {
            case 'mercury': ?>
                <button type="submit" id="submit-subscribe<?php echo $popup;?>"><?php echo esc_attr( $submit );?></button>
                <?php
                break;
            case 'headliner': ?>
                <input type="submit" id="submit-subscribe<?php echo $popup;?>" value="<?php echo esc_attr( $subscribe );?>" data-subscribe="<?php echo esc_attr( $submit );?>">
                <?php
                break;
            
            default: ?>
                <input type="submit" id="submit-subscribe<?php echo $popup;?>" value="<?php echo esc_attr( $submit );?>">
                <?php
                break;
        } ?>

        <div style="display: none;">
            <input type="text" name="form_honeypot" value="" tabindex="-1" autocomplete="off">
        </div>

        <div id="subscribe-response<?php echo $popup;?>"><?php echo isset( $response ) ? $response : '';?></div>

        <div id="subscribe-overlay<?php echo $popup;?>"></div>
    </div>

    <?php 
    // render Subscribe form Message/GDPR
    if ( $subscribe_label != '' ) {

        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array()
            ),
            'input' => array(
                'type' => array(),
                'checked' => array(),
                'id' => array(),
                'name' => array(),
                'required' => array(),
            ),
            'label' => array()
        );

        $checkbox = $gdpr_checkbox ? '<label><input type="checkbox" id="gdpr-checkbox'.$popup.'" name="gdpr-checkbox'.$popup.'" required /> ' : '';
        $closing_checkbox = $gdpr_checkbox ? '</label>' : '';

        ?>
        <div class="cmp-form-notes<?php echo $popup;?>">
            <?php echo wpautop(wp_kses( $checkbox . $subscribe_label, $allowed_html, $closing_checkbox )); ?>
        </div>
        <?php 
    } ?>

</form>

<script>
window.addEventListener('DOMContentLoaded',function(event) {

const form = document.getElementById('subscribe-form<?php echo $popup;?>');
const submitButton = form.querySelector('#submit-subscribe<?php echo $popup;?>');
const resultElement = form.querySelector('#subscribe-response<?php echo $popup;?>');
const emailInput =  form.querySelector('#email-subscribe<?php echo $popup;?>');
const firstnameInput =  form.querySelector('#firstname-subscribe<?php echo $popup;?>');
const lastnameInput =  form.querySelector('#lastname-subscribe<?php echo $popup;?>');
const gdprCheckbox = form.querySelector('#gdpr-checkbox<?php echo $popup;?>');

submitButton.onclick = function( e ) {
    e.preventDefault();

    // check GDPR checkbox
    if ( gdprCheckbox && gdprCheckbox.checked === false ) {
        resultElement.innerHTML = '<?php echo esc_attr($missing_gdpr);?>';
        return false;
    } 
    
    <?php 
    $site_key = get_option('niteoCS_recaptcha_site', '');
    if ( get_option( 'niteoCS_recaptcha_status', '1' ) === '1' && !empty($site_key)) { 
         ?>
        // google recaptcha
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo esc_attr($site_key);?>').then(function(token) {
                subForm( form, resultElement, emailInput, firstnameInput, lastnameInput, token );
            });
        });

        <?php 
    } else { ?> 
        // submit form
        subForm( form, resultElement, emailInput, firstnameInput, lastnameInput );
        <?php 
    } ?>
    
}

form.onsubmit = function(){ // Prevent page refresh
    return false;
}

});
</script>

<?php 

$html = ob_get_clean();