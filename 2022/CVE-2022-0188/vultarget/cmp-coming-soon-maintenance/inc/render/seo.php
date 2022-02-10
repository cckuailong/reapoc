<?php

ob_start();

$title = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_title', get_bloginfo('name').' Coming soon!') ), 'SEO Title' );
$descr = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_descr', 'Just Another Coming Soon Page') ), 'SEO Description' );

$seo_visibility = get_option('niteoCS_seo_visibility', get_option( 'blog_public', '1' ));
$seo_img_id = get_option('niteoCS_seo_img_id');
$seo_img_url = wp_get_attachment_image_src($seo_img_id, 'large');
$seo_img_url = isset($seo_img_url[0]) ? $seo_img_url[0] : $this->cmp_get_background_img_for_seo();
?>
<!-- SEO -->
<?php 
if ( $title !== '' ) {
echo '<title>'.esc_html( $title ).'</title>';
}  
if ( $descr !== '' ) {
echo '<meta name="description" content="'.esc_attr( $descr ).'"/>';
} ?>

<!-- og meta for facebook, googleplus -->
<meta property="og:title" content="<?php echo esc_html( $title ); ?>"/>
<meta property="og:description" content="<?php echo esc_html( $descr ); ?>"/>
<meta property="og:url" content="<?php echo get_home_url()?>"/>
<meta property="og:type" content="website" />
<meta property="og:image" content="<?php echo esc_url( $seo_img_url );?>"/>

<!-- twitter meta -->
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="<?php echo esc_html( $title ); ?>"/>
<meta name="twitter:description" content="<?php echo esc_html( $descr ); ?>"/>
<meta name="twitter:url" content="<?php echo get_home_url();?>"/>
<meta name="twitter:image" content="<?php echo esc_url( $seo_img_url );?>"/>

<?php 
// display favicon
$favicon_id = get_option('niteoCS_favicon_id');

if ( $favicon_id && $favicon_id != '' ) {
    $favicon_url = wp_get_attachment_image_src( $favicon_id, 'thumbnail' );
    if ( isset($favicon_url[0]) ) { 
        echo '<link id="favicon" rel="shortcut icon" href="' . $favicon_url[0] . '" type="image/x-icon"/>';
    } 
} else {
   wp_site_icon();
}

if ( $seo_visibility == '0' ) {
    echo '<meta name="robots" content="noindex,nofollow" />' . PHP_EOL; 
} 

$html = ob_get_clean();
