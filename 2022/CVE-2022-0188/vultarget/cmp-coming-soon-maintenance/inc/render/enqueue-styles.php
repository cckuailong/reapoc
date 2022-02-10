<?php

$ver = $this->cmp_theme_version( $themeslug );

// get font awesome, if subsscribe popup is enabled
if ( get_option('niteoCS_subscribe_type', '2') == '2' && get_option('niteoCS_subscribe_popup', '0') ) {
    $fa = true;
}

if ( $gutenberg === true ) {
    echo '<link rel="stylesheet" href="'.includes_url('/css/dist/block-library/style.min.css').'" type="text/css" media="all" />' . PHP_EOL;
}

// theme stylesheet
echo '<link rel="stylesheet" href="' . $this->cmp_themeURL( $themeslug ) . $themeslug.'/style.css?v='.$ver . '" type="text/css" media="all">' . PHP_EOL;

if ( $font_ani !== false || $font_ani !== 'none' ) {
    echo '<link rel="stylesheet" href="'. esc_url( $this->cmp_asset_url('/css/animate.min.css') ) . '">' . PHP_EOL;
}

if ( $slider == '1' && ($banner_type == '0' || $banner_type == '1') ) {
    echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" rel="stylesheet">' . PHP_EOL;
}

if ( $fa === true ) {
    echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" rel="stylesheet" >' . PHP_EOL;
}

if ( get_option('niteoCS_lang_switcher', '1') == '1' && $this->translation_active() ) {
    echo '<link href="' . esc_url( CMP_PLUGIN_URL . 'css/lang-switcher.css' ). '" rel="stylesheet">' . PHP_EOL;
}