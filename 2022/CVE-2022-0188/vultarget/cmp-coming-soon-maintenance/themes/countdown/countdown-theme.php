<!DOCTYPE html>

<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php 
        $themeslug = 'countdown';
        $lang_switcher = false;
        if ( method_exists( $html, 'cmp_render_lang_switcher' ) ) {
            $lang_switcher = $html->cmp_render_lang_switcher();
        }
        //include theme defaults
        if ( file_exists(dirname(__FILE__).'/'.$themeslug.'-defaults.php') ) {
            require ( dirname(__FILE__).'/'.$themeslug.'-defaults.php' );
        } 

        // render SEO
        echo $html->cmp_get_seo();

        $html->cmp_enqueue_styles( $themeslug, false , false, $banner_type, true, false);

        // render google fonts link
        if ( method_exists ( $html, 'cmp_get_fonts' ) ) {
            echo $html->cmp_get_fonts( $heading_font, $content_font );
        } 

        // get theme related settings
        $font_color_light           = $this->hex2hsl( $font_color, '20' );        

        ?>
        
        <style>
            body,input, select, textarea, button {font-family:'<?php echo esc_attr( $content_font['family'] );?>', 'sans-serif';color:<?php echo esc_attr( $font_color ); ?>;}
            input {font-family: <?php echo esc_attr( $content_font['family'] );?>, 'Font Awesome 5 Free';}
            
            body {font-size:<?php echo esc_attr( $content_font['size'] );?>px;}
            h1,h2,h3,h4,h5,h6 {font-family:'<?php echo esc_attr( $heading_font['family'] );?>', 'sans-serif';}
            a {color:<?php echo esc_attr( $font_color ); ?>;}
            .cmp-subscribe input[type="submit"] {background-color: <?php echo esc_attr( $active_color );?>;}
            .cmp-subscribe ::-webkit-input-placeholder {color: <?php echo esc_attr( $font_color_light );?>;}
            .cmp-subscribe ::-moz-placeholder {color: <?php echo esc_attr( $font_color_light );?>;}
            .cmp-subscribe :-ms-input-placeholder {color: <?php echo esc_attr( $font_color_light );?>;}
            .cmp-subscribe ::-moz-placeholder {color: <?php echo esc_attr( $font_color_light );?>;}
            .input-icon:before, .cmp-subscribe input[type="email"],.cmp-subscribe input[type="text"]{color: <?php echo esc_attr( $font_color_light );?>;}
            /* input[type="email"],input[type="text"] {border:1px solid <?php echo esc_attr( $font_color_light );?>;} */
            footer, footer a {color: <?php echo esc_attr( $font_color_light );?>;}
            .social-list.body a {background-color: <?php echo esc_attr( $font_color ); ?>;}
            .social-list.body a:hover {background-color: <?php echo esc_attr( $active_color ); ?>;}
            .social-list.footer a:hover {color: <?php echo esc_attr( $active_color ); ?>;}
            .social-list.footer li:not(:last-of-type)::after {background-color: <?php echo esc_attr( $font_color_light ); ?>;}
            
            .inner-content p {line-height: <?php echo esc_attr( $content_font['line-height'] );?>; letter-spacing: <?php echo esc_attr( $content_font['spacing'] );?>px;font-weight:<?php echo esc_attr($content_font_style['0']);?>;<?php echo isset( $content_font_style['1']) ? 'font-style: italic;' : '';?>; }
            h1:not(.text-logo),h2, h3,h4,h5,h6,.text-logo-wrapper {font-size:<?php echo esc_attr( $heading_font['size'] / $content_font['size'] );?>em;letter-spacing: <?php echo esc_attr( $heading_font['spacing']  );?>px;  font-weight:<?php echo esc_attr( $heading_font_style['0']);?>;<?php echo isset($heading_font_style['1'] ) ? 'font-style: italic;' : '';?>; }
            h1 { font-weight:<?php echo esc_attr( $heading_font_style['0'] );?>;<?php echo isset( $heading_font_style['1'] ) ? 'font-style: italic;' : '';?>;}
        </style>

        <?php 
        // render custom CSS 
        if ( method_exists ( $html, 'cmp_get_custom_css' ) ) {
            echo $html->cmp_get_custom_css();
        } 

        // render header javascripts
        if ( method_exists ( $html, 'cmp_head_scripts' ) ) {
            $html->cmp_head_scripts();
        } 
        
        // echo pattern copyright
        if ( $banner_type == 3 ) {
             echo '<!-- Background pattern from Subtle Patterns --!>';
        } ?>

    </head>

    <body id="body">

        <?php
        if ( $lang_switcher ) { ?>
            <div class="lang-switch-wrapper">
                <?php echo $lang_switcher; ?>
            </div>
            <?php 
        } ?>

        <div id="background-wrapper">
            <?php
            if ( method_exists ( $html, 'cmp_background' ) ) {
                echo $html->cmp_background( $banner_type, $themeslug );

            } ?>
        </div>

        <div class="inner-wrap">
            <div class="inner-content">
                <?php 
                // display logo
                if ( method_exists ( $html, 'cmp_logo' ) ) {
                    echo $html->cmp_logo( $themeslug );
                } 
                
                // display body title
                if ( method_exists ( $html, 'cmp_get_title' ) ) {
                    echo $html->cmp_get_title( );
                } 

                if ( method_exists ( $html, 'cmp_render_counter' ) ) {
                    echo $html->cmp_render_counter();
                }

                // display body content
                if ( get_option('niteoCS_body') != '' ) { ?>
                    <div class="content">             
                        <?php
                        // display body title
                        if ( method_exists ( $html, 'cmp_get_body' ) ) {
                            echo $html->cmp_get_body();
                        } 
                        ?>   
                    </div>
                    <?php 
                }       

                // display social if in body
                if ( $social_location == 'body') {  ?>

                    <div class="social-wrapper <?php echo esc_attr($social_location );?>">
                        <?php 
                        // display social icons
                        if ( method_exists ( $html, 'cmp_social_icons' ) ) {
                            echo $html->cmp_social_icons( $mode = 'icon', $title = false );
                        } ?>
                    </div>
                    <?php 
                }

                // display subscribe form
                if ( method_exists ( $html, 'cmp_subscribe_form' ) ) {
                    echo $html->cmp_subscribe_form( );
                } ?>

             </div>

            <?php 
            if ( $social_location == 'footer' || get_option('niteoCS_copyright') !== '') {

                echo '<footer>';
                
                    if ( $social_location == 'footer') {  ?>

                        <div class="social-wrapper">
                            <?php 
                            // display social icons
                            if ( method_exists ( $html, 'cmp_social_icons' ) ) {
                                echo $html->cmp_social_icons( $mode = 'icon', $title = false );
                            } ?>
                        </div>
                        <?php 
                    }

                    if ( method_exists ( $html, 'cmp_get_copyright' ) ) {
                        echo $html->cmp_get_copyright();
                    } 


                echo '</footer>';
            } ?>
        </div>

        <?php 

        // rener footer javascripts
        if ( method_exists ( $html, 'cmp_javascripts' ) ) {
            $html->cmp_javascripts( $banner_type, $themeslug );
        } ?>
    </body>
</html>
