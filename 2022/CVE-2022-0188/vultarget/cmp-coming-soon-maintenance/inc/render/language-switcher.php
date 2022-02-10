<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$display_flag 	= get_option('niteoCS_lang_switcher[flag]', '1');
$display_text 	= get_option('niteoCS_lang_switcher[text]', '1');
$current_lang_slug = $this->cmp_get_current_lang( 'slug' );
$current_lang_name = $this->cmp_get_current_lang( 'name' );
$class = !$display_flag || !$display_text ? ' no-padding-left' : '';

if ( function_exists('pll_the_languages') ) {
    $translations = pll_the_languages( array( 'raw' => 1 ) );
    $flag = pathinfo($translations[$current_lang_slug]['flag']);

} else if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
    $translations = apply_filters( 'wpml_active_languages', NULL, array('skip_missing' => 0) );
    if ( empty($translations) ) return;
    $flag = pathinfo( substr($translations[$current_lang_slug]['default_locale'], -2));
    
} else {
    return;
}

$country_code = strtolower($flag['filename']);

ob_start();

?>

<div class="lang-switcher flag-<?php echo esc_attr($display_flag);?> text-<?php echo esc_attr($display_text);?>">
    <div class="lang-dropdown">
        <a class="lang-dropdown-trigger" href="#">
            <?php 
            if ( $display_flag == '1' ) { ?>
            <img src="<?php echo CMP_PLUGIN_URL . 'img/flags/'.$country_code.'.svg';?>" alt="<?php echo esc_html( $current_lang_name );?> flag" width="36">
            <?php 
            }
            if ( $display_text == '1' ) {
                echo esc_html( $current_lang_name );
            }?>
        </a>
        <ul class="lang-dropdown-menu">
            <?php
            foreach ( $translations as $lang ) {
                $url = $lang['url'];
                if ( isset($_GET['cmp_preview']) && $_GET['cmp_preview'] == 'true' )  {
                    $param = strpos($url, '?') === false ? '?' : '&';
                    $url .= $param.'cmp_preview=true';
                }  ?>
                <li class="lang-dropdown-menu-item">
                    <a href="<?php echo esc_url( $url );?>"><?php echo esc_html( isset($lang['name']) ? $lang['name'] : $lang['native_name'] );?></a>
                </li>
                <?php 
            } ?>
        </ul>
    </div>
</div>

<?php 

$html = ob_get_clean();