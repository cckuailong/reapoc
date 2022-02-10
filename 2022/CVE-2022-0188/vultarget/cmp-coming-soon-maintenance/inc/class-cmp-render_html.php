<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Create a new cmp_render_html class that will extend the CMP_Coming_Soon_and_Maintenance
 */
class CMP_Coming_Soon_and_Maintenance_Render_HTML extends CMP_Coming_Soon_and_Maintenance {


    function __construct() {
        $this->jquery = FALSE;
    }
    // Render Background
    public function cmp_background( $niteoCS_banner, $themeslug ) {

        include dirname( __FILE__) . '/render/graphic-background.php';

        return $html;
    }


    // render slider
    public function cmp_slider( $themeslug ) {

        include dirname( __FILE__) . '/render/graphic-slider.php';
  
        return;
    }

    /**
     * render Overlay element.
     *
     * @since 2.8
     * @return HTML 
     **/
    public function background_overlay( $themeslug ) {

        include dirname( __FILE__) . '/render/graphic-overlay.php';

        return $html;
    }


    /**
     * render graphic Overlay text.
     *
     * @since 2.9.5
     * @return HTML 
     **/
    public function background_text_overlay( $themeslug ) {

        include dirname( __FILE__) . '/render/text-overlay.php';

        return $html;

    }


    // render Social Icons
    public function cmp_social_icons( $mode = 'icon', $title = false, $themeslug = false, $ulclass = '', $liclass = '' ) {

        include dirname( __FILE__) . '/render/social-icons.php';

        return $html;
        
    }

    // Render Logo
    public function cmp_logo( $themeslug, $class = '' ) {

        include dirname( __FILE__) . '/render/logo.php';

        return $html;
    }


    // render subscribe form
    public function cmp_subscribe_form( $label = FALSE, $firstname = FALSE, $lastname = FALSE ) {
        include dirname( __FILE__) . '/render/subscribe-form.php';

        return $html;

    }
    // render subscribe form
    public function cmp_get_form( $popup = false, $label = FALSE, $firstname = FALSE, $lastname = FALSE ) {
        include dirname( __FILE__) . '/render/subscribe-form-cmp.php';

        return $html;

    }

    /**
     * returns body content.
     *
     * @since 2.4
     * @return HTML 
     **/
    public function cmp_get_body() {

        include dirname( __FILE__) . '/render/content.php';

        return $html;
    }

    /**
     * render body title.
     *
     * @since 2.4
     * @return HTML 
     **/
    public function cmp_get_title( $class = '' ) {

        include dirname( __FILE__) . '/render/title.php';

        return $html;

    }

    /**
     * render Google fonts link.
     *
     * @since 2.4
     * @param  array[font_family],[font_variant]
     * @return HTML 
     **/
    public function cmp_get_fonts( $heading_font = array(), $content_font = array() ) {

        include dirname( __FILE__) . '/render/fonts.php';

        return $google_fonts . PHP_EOL . $custom_font . PHP_EOL;
    }




    /**
     * return  theme head SEO.
     *
     * @since 2.4
     * @return HTML 
     **/
    public function cmp_get_seo() {

        include dirname( __FILE__) . '/render/seo.php';

        return $html;
    }


    /**
     * render custom CSS.
     *
     * @since 2.4
     * @return HTML 
     **/
    public function cmp_get_custom_css() {

        include dirname( __FILE__) . '/render/custom-css.php';

        return $css;
    }

    /**
     * render copyright.
     *
     * @since 2.4
     * @return HTML 
     **/
    public function cmp_get_copyright() {

        include dirname( __FILE__) . '/render/copyright.php';

        return $html;
    }

    /**
     * @since 3.2
     * echo CSS styles to head
     * @return void
     **/
    public function cmp_enqueue_styles( $themeslug = 'hardwork', $font_ani = false, $slider = '0', $banner_type = '2', $fa = false, $gutenberg = false, $lang_switcher = false ) {
        include dirname( __FILE__) . '/render/enqueue-styles.php';

        return;

    }

    /**
     * echo custom external CSS or Scripts
     *
     * @return echo HTML 
     **/
    public function cmp_head_scripts() {

        include dirname( __FILE__) . '/render/head-scripts.php';

        return;
    }


    /**
     * echo Javascripts for Themes.
     *
     * @param  array background type, themeslug
     * @return echo HTML 
     **/
    public function cmp_javascripts( $background, $themeslug ) {

        include dirname( __FILE__) . '/render/javascripts.php';

        return false;
    }

    /**
     * render Contact Form.
     *
     * @since 2.5
     * @return HTML 
     **/
    public function cmp_contact_form() {

        include dirname( __FILE__) . '/render/contact-form.php';

        return $html;
    }

    /**
     * render niteothemes info
     *
     * @since 3.2.3
     * @return HTML 
     **/
    public function cmp_render_nt_info() {

        include dirname( __FILE__) . '/render/niteothemes-info.php';

        return $html;
    }

    /**
     * render language switcher
     *
     * @since 3.7.5
     * @return HTML 
     **/
    public function cmp_render_lang_switcher() {
        $html = false;
        if ( get_option('niteoCS_lang_switcher', '1') == '1' && (function_exists('icl_register_string') || defined( 'ICL_SITEPRESS_VERSION' ))) {
            include dirname( __FILE__) . '/render/language-switcher.php';
        }
        return $html;
    }

    /**
     * render language switcher
     *
     * @since 3.7.5
     * @return HTML 
     **/
    public function cmp_render_counter( $days_only = false, $wrapper_class = '' ) {
        $html = '';
        if ( get_option('niteoCS_counter', '1') == '1' ) {
            include dirname( __FILE__) . '/render/counter.php';
        }
        return $html;
    }

    /**
     * get array of banner ids
     *
     * @since 3.4.8
     * @return array 
     **/
    public function cmp_get_banner_ids() {

        $banner_id = ( $this->isMobile() && get_option('niteoCS_custom_mobile_imgs', '0') == '1') ? get_option('niteoCS_mobile_banner_id') : get_option('niteoCS_banner_id');

        $banner_ids = array();
        if ( $banner_id != '' ) {
            $banner_ids = explode(',', $banner_id);
        }

        return $banner_ids;
    }

     /**
     * helper function to render style css for custom fonts
     *
     * @since 3.5
     * @param  string,array
     * @return HTML 
     **/
    public function cmp_get_font_src( $family, $ids ) {

        foreach ( $ids as $attachment_id ) {

            $url = wp_get_attachment_url($attachment_id);
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $src = '';
            $new_src = '';
            $eot = '';
            
            switch ($ext) {
                case 'eot':
                    $eot = 'src: url("'.esc_url($url).'");' . PHP_EOL;
                    break;
                case 'woff':
                    $new_src = 'url("'.esc_url($url).'")' . ' format("woff"),';
                    break;
                case 'woff2':
                    $new_src = 'url("'.esc_url($url).'")' . ' format("woff2"),';
                    break;
                case 'otf':
                    $new_src = 'url("'.esc_url($url).'")' . ' format("opentype"),';
                    break;
                case 'ttf':
                    $new_src = 'url("'.esc_url($url).'")' . ' format("truetype"),';
                    break;
                case 'svg':
                    $new_src = 'url("'.esc_url($url).'#filename")' . ' format("svg"),';
                    break;
                default:
                    break;
            }

            $src .=  $new_src;
        }

        return '<style>'. PHP_EOL .'@font-face {font-family: "'.$family.'";' . PHP_EOL . $eot  . 'src: ' . rtrim( $src, ',').';}'. PHP_EOL .'</style>';
    }

    /**
     * print whitelisted scripts and styles to cmp_head
     *
     * @since 3.5.6
     * @return html 
     **/
    public function cmp_wp_head() {

        // Plugin Name: LiteSpeed Cache
        if ( class_exists('LiteSpeed\Core') ) {
            define( 'LITESPEED_IS_HTML', true );
        }
        
        // Plugin Name: Insert Headers and Footers
        if ( class_exists('InsertHeadersAndFooters') ) {
            $ihaf = new InsertHeadersAndFooters();
            $ihaf->frontendHeader();
        }
        
        // Plugin Name: Cookie Notice
        if ( function_exists('cn_cookies_accepted') && get_option('cmp_cookie_notice_comp', '1') === '1' && !cn_cookies_accepted() ) { 
            $options = Cookie_Notice()->options;
            $cnargs = array(
				'ajaxUrl'				=> admin_url( 'admin-ajax.php' ),
				'nonce'					=> wp_create_nonce( 'cn_save_cases' ),
				'hideEffect'			=> $options['general']['hide_effect'],
				'position'				=> $options['general']['position'],
				'onScroll'				=> (int) $options['general']['on_scroll'],
				'onScrollOffset'		=> (int) $options['general']['on_scroll_offset'],
				'onClick'				=> (int) $options['general']['on_click'],
				'cookieName'			=> 'cookie_notice_accepted',
				'cookieTime'			=> Cookie_Notice()->settings->times[$options['general']['time']][1],
				'cookieTimeRejected'	=> Cookie_Notice()->settings->times[$options['general']['time_rejected']][1],
				'cookiePath'			=> ( defined( 'COOKIEPATH' ) ? (string) COOKIEPATH : '' ),
				'cookieDomain'			=> ( defined( 'COOKIE_DOMAIN' ) ? (string) COOKIE_DOMAIN : '' ),
				'redirection'			=> (int) $options['general']['redirection'],
				'cache'					=> (int) ( defined( 'WP_CACHE' ) && WP_CACHE ),
				'refuse'				=> (int) $options['general']['refuse_opt'],
				'revokeCookies'			=> (int) $options['general']['revoke_cookies'],
				'revokeCookiesOpt'		=> $options['general']['revoke_cookies_opt'],
				'secure'				=> (int) is_ssl()
			); ?>
            <script>var cnArgs = <?php echo json_encode( $cnargs );?></script>
            <link rel='stylesheet' id='cookie-notice-front-css'  href='<?php echo plugins_url();?>/cookie-notice/css/front.min.css' media='all' />
            <script id='cookie-notice-front-js' src='<?php echo plugins_url();?>/cookie-notice/js/front.min.js?ver=2.0.2'></script>
            <?php 
        }
    }
 
    /**
     * filtered wp_footer for CMP
     *
     * @since 3.5.6
     * @return html 
     **/
    public function cmp_wp_footer() {

        $login_icon = get_option('niteoCS_login_icon', '0');

        if ( $login_icon !== '0' ) {
            include dirname( __FILE__) . '/render/login-icon.php';
        }

        include dirname( __FILE__) . '/render/footer.php';

        return;
    }

    public function cmp_custom_footer_scripts() {
        $footer_scripts = json_decode( get_option('niteoCS_footer_scripts', '[]'), true );

        if ( !empty( $footer_scripts ) ) {
            foreach ( $footer_scripts as $f_script ) {
                if ( $f_script != '' ) {
                    $file = pathinfo( $f_script );
                    switch ( $file['extension'] ) {
                        case 'js':
                            echo '<script src="' . esc_url( $f_script ). '"></script>' . PHP_EOL;
                            break;
                        case 'css':
                            echo '<link href="' . esc_url( $f_script  ). '" rel="stylesheet">' . PHP_EOL;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

    /**
     * render progress bar
     *
     * @since 3.8.8
     * @return html 
     **/
    public function cmp_render_progress_bar( $timeout = 0, $wrapper_class = '' ) {
        if ( get_option('niteoCS_progress_bar', '1') !== '1' ) {
            return '';
        }
        
        include dirname( __FILE__) . '/render/progress-bar.php';

        return $html;
    }

    /**
     * Helper function to return subscribe type
     *
     * @since 3.6.5
     * @access public
     * @return int
     */
    public function cmp_subscribe_type() {
        $subscribe_type = get_option('niteoCS_subscribe_type', '2');

        if ( $subscribe_type == '3' ) {
            $optin_type = $this->mailoptin_campaign_type();
            $subscribe_type = $optin_type === 'lightbox' ? '0' : $subscribe_type;
        }

        return $subscribe_type;
    }

    /**
     * Helper function to determine MailOptin campaign type
     *
     * @since 3.6.5
     * @access public
     * @return string
     */
    public function mailoptin_campaign_type() {
        
        if ( class_exists('MailOptin\Core\Repositories\OptinCampaignsRepository') ) {
            $campaign_id = get_option('niteoCS_mailoptin_selected');
            $campaign = MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($campaign_id);
            return $campaign['optin_type'];
        }

        return false;
    }
    

}