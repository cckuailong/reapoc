<?php
// get type of susbscribe
$subscribe_type = get_option('niteoCS_subscribe_type', '2');
$html = '';

switch ($subscribe_type) {
    // custom shortcode
    case '1':
        $replace  = array('<p>', '</p>' );
        $html =  str_replace($replace, '', do_shortcode( stripslashes( get_option('niteoCS_subscribe_code') ))) ; 
        break;
    // CMP subscribe form
    case '2':
        if ( get_option('niteoCS_inpage_subscribe', '1') ) {
            $html = $this->cmp_get_form($popup = false, $label, $firstname, $lastname);
        }
        break;
    // MailOPtin
    case '3':

        if ( defined('MAILOPTIN_VERSION_NUMBER') ) {

            $campaign_id = get_option('niteoCS_mailoptin_selected');
            $campaign= MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($campaign_id);
            if ( $campaign['optin_type'] !== 'lightbox' ) {
                if ( !$this->jquery ) {
                    echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
                    $this->jquery = TRUE;
                }
                $html = do_shortcode( '[mo-optin-form id="'. get_option('niteoCS_mailoptin_selected') .'"]' );
            }
        }
        break;
    default:
        break;
}

return $html;