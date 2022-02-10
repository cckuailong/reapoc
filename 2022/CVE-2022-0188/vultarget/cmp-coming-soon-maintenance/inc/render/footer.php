<?php 
// render subscribe POPUP form
$subs_type = get_option('niteoCS_subscribe_type', '2');
if ( $subs_type == '2' ) {
    $subscribe_popup = get_option('niteoCS_subscribe_popup', '0');
    if ( $subscribe_popup ) { 
        $subscribe_popup_title = stripslashes( get_option('niteoCS_subscribe_popup_title', get_option('niteoCS_subscribe_title', 'SUBSCRIBE US')) );
        $subscribe_popup_title = $this->cmp_wpml_translate_string( $subscribe_popup_title, 'Subscribe Popup Form Title' );
        $niteoCS_subs_img_popup_id = get_option('niteoCS_subs_img_popup_id', '');
        $subscribe_popup_time = get_option('niteoCS_subscribe_popup_time', '10');
        $subs_img_url = '';
        $firstname = get_option( 'niteoCS_subscribe_firstname_popup', '0' ) ? true : false;
        $lastname = get_option( 'niteoCS_subscribe_lastname_popup', '0' ) ? true : false;
        
        if ( $niteoCS_subs_img_popup_id != '' ) {
            $subs_img_url = wp_get_attachment_image_src($niteoCS_subs_img_popup_id, 'large');
            if ( isset($subs_img_url[0]) ){
                $subs_img_url = $subs_img_url[0];
            }
        } ?>
    
        <div id="subscribe-container-popup" class="form-container animated-fast">
    
            <div class="form-wrapper-popup<?php echo $subs_img_url == '' ? ' no-img' : '';?>">
                <?php if ( $subs_img_url !== '' ) : ?>
                <div class="subs-img" style="background-image:url(<?php echo esc_url($subs_img_url);?>)"></div>
                <?php endif;?>
                <div class="form-content">
                    <?php if (!empty($subscribe_popup_title)) { ?>
                        <h4 class="form-title"><?php echo esc_html($subscribe_popup_title);?></h4>
                        <?php 
                    } ?>
                    <div class="close-popup"><i class="fa fa-times" aria-hidden="true"></i></div>
                    <?php 
                    echo $html = $this->cmp_get_form($popup = true, $label = false, $firstname, $lastname); ?>
                </div>
            </div>
        </div>
    
        <script>
            var subsContainerPopup = document.getElementById('subscribe-container-popup');
            var closePopup = subsContainerPopup.querySelector('.close-popup');
    
            setTimeout(() => {
                subsContainerPopup.classList.add('in-focus', 'zoomIn');
            }, <?php echo esc_attr($subscribe_popup_time * 1000);?>);
    
    
            closePopup.onclick = function() {
                subsContainerPopup.classList.remove('in-focus', 'zoomIn');
            }
        </script>
        <?php 
    } 
}

/**
 * Detect plugin. For use on Front End only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Plugin Name: Insert Headers and Footers
if ( class_exists('InsertHeadersAndFooters') ) {
    $ihaf = new InsertHeadersAndFooters();
    $ihaf->frontendFooter();
}

// Plugin Name: Cookie Notice
if ( class_exists('Cookie_Notice_Frontend') && get_option('cmp_cookie_notice_comp', '1') === '1' ) {
    // access cookie notice class
    $cookie_notice = new Cookie_Notice_Frontend();
    
    $cookie_notice->add_cookie_notice();
}

// Plugin Name: SimpleAnalytics
if ( is_plugin_active( 'simpleanalytics/simple-analytics.php' ) ) {
    echo '<script src="https://cdn.simpleanalytics.io/hello.js"></script>' . PHP_EOL;
}

// Plugin Name: MailOption
if ( get_option('niteoCS_subscribe_type', '2') === '3' && defined('MAILOPTIN_VERSION_NUMBER') )  {

    $optin_id = get_option('niteoCS_mailoptin_selected');
    $campaign_type = MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($optin_id);

    if ( $campaign_type['optin_type'] === 'lightbox') {
        if ( !$this->jquery ) {
            echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
            $this->jquery = TRUE;
        }
        echo MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory::build(absint($optin_id));
    } ?>

    <script type='text/javascript'>
    /* <![CDATA[ */
    var mailoptin_globals = {"mailoptin_ajaxurl":"?mailoptin-ajax=%%endpoint%%","is_customize_preview":"false","disable_impression_tracking":"false","sidebar":"0"};
    /* ]]> */
    </script>
    <script type='text/javascript' src='<?php echo MAILOPTIN_ASSETS_URL;?>js/mailoptin.min.js?ver=<?php echo MAILOPTIN_VERSION_NUMBER;?>'></script>
    <?php
} 

// scripts for Plugin Name Weglot
if ( is_plugin_active( 'weglot/weglot.php' ) && defined('WEGLOT_VERSION') ) { ?>
    <script src='https://cmp.weglot-translate.com/wp-content/plugins/weglot/dist/front-js.js?ver=<?php echo WEGLOT_VERSION;?>' id='wp-weglot-js-js'></script>
    <?php
}


do_action('cmp_footer');