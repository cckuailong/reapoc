<?php



$this->cmp_wp_head();

do_action('cmp-before-header-scripts');

$head_scripts = json_decode( get_option('niteoCS_head_scripts', '[]'), true );

if ( !empty( $head_scripts ) ) {
    foreach ( $head_scripts as $script ) {
        if ( $script != '' ) {
            $file = pathinfo( $script );
            switch ( $file['extension'] ) {
                case 'js':
                    echo '<script src="' . esc_url( $script ). '"></script>' . PHP_EOL;
                    break;
                case 'css':
                    echo '<link href="' . esc_url( $script ). '" rel="stylesheet">' . PHP_EOL;
                    break;
                default:
                    break;
            }
        }
    }

    do_action('cmp-after-header-scripts');
}


if ( ( function_exists('cn_cookies_accepted') && get_option('cmp_cookie_notice_comp', '1') === '1' && cn_cookies_accepted() ) || !function_exists('cn_cookies_accepted') ) {
    switch ( get_option('niteoCS_analytics_status', 'disabled') ) {
        //disabled analytics
        case 'disabled':
            break;
        // google analytics
        case 'google':
    
            if ( get_option('niteoCS_analytics', '') !== '' ) { ?>
                <!-- Google analytics code -->
                <script>
                  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    
                  ga('create', '<?php echo esc_attr(get_option('niteoCS_analytics'));?>', 'auto');
                  ga('send', 'pageview');
    
                </script>
                <?php 
            } 
    
            break;
        // other js code
        case 'other':
            if ( get_option('niteoCS_analytics_other', '') !== '' ) {
                $analytics_code = get_option('niteoCS_analytics_other', ''); 
                echo stripslashes( $analytics_code );
            } 
    
            break;
        default:
            break;
    }
}

