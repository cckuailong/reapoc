<!DOCTYPE html>

<html <?php language_attributes(); ?>>
    <head>
    	<meta charset="<?php bloginfo( 'charset' ); ?>">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

        <?php 
        $themeslug = 'hardwork';
        $lang_switcher = false;
        if ( method_exists( $html, 'cmp_render_lang_switcher' ) ) {
            $lang_switcher = $html->cmp_render_lang_switcher();
        }
        //include theme defaults
        if ( file_exists(dirname(__FILE__).'/'.$themeslug.'-defaults.php') ) {
            require ( dirname(__FILE__).'/'.$themeslug.'-defaults.php' );
        } 

        // render SEO
        if ( method_exists ( $html, 'cmp_get_seo' ) ) {
            echo $html->cmp_get_seo();
        }

        $html->cmp_enqueue_styles( $themeslug, false , false, $banner_type, true, false);

        // render google fonts link
        if ( method_exists ( $html, 'cmp_get_fonts' ) ) {
            echo $html->cmp_get_fonts( $heading_font, $content_font );
        }  ?>

        <style>
            body,input {font-family:'<?php echo esc_attr($content_font['family']);?>', 'sans-serif';color:<?php echo esc_attr( $font_color ); ?>;}
            <?php 
            if ( $footer_opacity  != '0' ) { ?>
                .social-list {background-color: <?php echo esc_attr( $this->hex2rgba( $footer_background, $footer_opacity ) );?>;}
                <?php 
            } ?>
            a {color:<?php echo esc_attr( $font_color ); ?>;}
            h1,h2,h3,h4,h5,h6 {font-family:'<?php echo esc_attr($heading_font['family']);?>', 'sans-serif';}
            body {font-size:<?php echo esc_attr( $content_font['size'] );?>px; letter-spacing: <?php echo esc_attr( $content_font['spacing'] );?>px; font-weight:<?php echo esc_attr( $content_font_style['0'] );?>;<?php echo isset($content_font_style['1']) ? 'font-style: italic;' : '';?>; }
            h1:not(.text-logo),h2, h3,h4,h5,h6,.text-logo-wrapper {font-size:<?php echo esc_attr( $heading_font['size'] / $content_font['size'] );?>em;letter-spacing: <?php echo esc_attr( $heading_font['spacing'] );?>px; font-weight:<?php echo esc_attr( $heading_font_style['0'] );?>;<?php echo isset($heading_font_style['1'] ) ? 'font-style: italic;' : 'font-style: normal;';?>; }
            h1 {font-weight:<?php echo esc_attr($heading_font_style['0']);?>;<?php echo isset($heading_font_style['1']) ? 'font-style: italic;' : 'font-style: normal;';?>;}
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
        } 

        ?>

    </head>


    <body id="body">
        <div id="background-wrapper">

        <?php
        if ( method_exists ( $html, 'cmp_background' ) ) {
            echo $html->cmp_background( $banner_type, $themeslug );

        } ?>

        </div>

        <?php
        if ( $lang_switcher ) { ?>
            <div class="lang-switch-wrapper">
                <?php echo $lang_switcher; ?>
            </div>
            <?php 
        } ?>

        <section class="section section-body">
            <?php 

             // display logo
            if ( method_exists ( $html, 'cmp_logo' ) ) {
                echo $html->cmp_logo( $themeslug );
            } 

            // display body title
            if ( method_exists ( $html, 'cmp_get_title' ) ) {
                echo $html->cmp_get_title( );
            } 

            // display body title
            if ( method_exists ( $html, 'cmp_get_body' ) ) {
                echo $html->cmp_get_body();
            } ?>
         
         </section>
        <?php
        // display social icons
        if ( method_exists ( $html, 'cmp_social_icons' ) ) {
            echo $html->cmp_social_icons( $mode = 'icon', $title = false );
        } 

        // render footer javascripts
        if ( method_exists ( $html, 'cmp_javascripts' ) ) {
            $html->cmp_javascripts( $banner_type, $themeslug );
        } ?>

    </body>

</html>
