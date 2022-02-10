<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<div class="table-wrapper general">
    <h3><?php _e('Available Themes', 'cmp-coming-soon-maintenance');?></h3>
    <table class="general theme-selector">
        <tr>
        <td class="theme-selector">
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Free Themes', 'cmp-coming-soon-maintenance');?> </span>
                </legend>
                <?php
                // move active theme to beginning
                $key = array_search ( $this->cmp_selectedTheme(), $this->cmp_themes_available() );

                $themes = $this->cmp_themes_available();
                
                if ( $key ) {
                    $active = $themes[$key];
                    unset( $themes[$key] );
                    array_unshift( $themes, $active );
                }

                // define what attribute we want from style.css header
                $headers  = array('Version');

                foreach ( $themes as $theme_slug ) {

                    $version = $this->cmp_theme_version( $theme_slug );
                    $type = 'standard';

                    // if premium get theme version 
                    if ( in_array( $theme_slug, $this->cmp_premium_themes_installed() ) ) {
                        $type = 'premium';
                    }

                    // get thumbnail
                    $thumbnail = CMP_PLUGIN_URL . 'img/thumbnails/'. $theme_slug . '_thumbnail.jpg';

                    // if no thumbnail in CMP plugin folder, check directly in CMP theme folder
                    if ( !file_exists( CMP_PLUGIN_DIR . 'img/thumbnails/'. $theme_slug . '_thumbnail.jpg' ) ) {
                        $thumbnail = $this->cmp_themeURL( $theme_slug ) . $theme_slug . '/img/thumbnail.jpg';
                    } ?>

                    <div class="theme-wrapper<?php if ( $this->cmp_selectedTheme() == $theme_slug ) { echo ' active'; } ?>" data-security="<?php echo esc_attr($ajax_nonce);?>" data-type="<?php echo esc_attr($type);?>" data-purchased="1" data-slug="<?php echo esc_attr($theme_slug);?>" data-version="<?php echo esc_html($version);?>" data-remote_url="<?php echo esc_url(CMP_UPDATE_URL);?>">
                        <div class="thumbnail-holder theme-details" style="background-image:url('<?php echo esc_url( $thumbnail ); ?>')"></div>
                        
                        <div class="buttons-wrapper">

                            <a href="<?php echo esc_url( get_site_url().'?cmp_preview=true&cmp_theme='.$theme_slug );?>" target="_blank" class="theme-preview button"><i class="fas fa-external-link-alt"></i><?php _e('PREVIEW', 'cmp-coming-soon-maintenance');?></a>
                            
                            <a href="#theme-details" class="theme-details button"><i class="far fa-eye"></i><?php _e('DETAILS', 'cmp-coming-soon-maintenance');?></a>
                        </div>

                        <div class="theme-inputs">

                            <?php 

                            if ( $this->cmp_selectedTheme() !== $theme_slug ) { 
                                echo  '<button class="cmp-activate-theme" type="submit" name="niteoCS_select_theme" value="'.esc_attr($theme_slug).'">' . __('Activate', 'cmp-coming-soon-maintenance') . '</button>';
                            } 
                            
                            echo ( $this->cmp_selectedTheme() == $theme_slug ) ? ' <span class="italic">'.__('Active', 'cmp-coming-soon-maintenance').'</span>' : '';?>
                            
                            <span class="theme-title"><?php echo ucwords(esc_html(str_replace('_', ' ', $theme_slug)));?></span>

                            <span class="theme-version">ver. <?php echo esc_html( $version );?></span>

                        </div> <!-- theme-inputs -->
                    </div> <!-- theme-wrapper -->

                    <?php
                } ?>
            </fieldset>
        </td>
    </tr>
    
    <?php echo $this->render_settings->submit(); ?>

    </tbody>
    </table>
    <div class="theme-overlay cmp"></div>
</div>

<?php
if ( !empty( $downloadable_themes ) ) { ?>

<div class="table-wrapper general">
    <h3><?php _e('Download more CMP Themes', 'cmp-coming-soon-maintenance');?></h3>
    <table class="general theme-selector">
    <tbody>
        <tr>
        <td class="theme-selector">
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Premium Themes', 'cmp-coming-soon-maintenance');?> </span>
                </legend>
                <?php 

                // build previews for downloadable themes
                foreach ( $downloadable_themes as $premium_theme ) {
                    $theme_slug = $premium_theme['name'];

                    $thumbnail = plugins_url('../../img/thumbnails/'. $theme_slug . '_thumbnail.jpg', __FILE__); ?>

                    <div class="theme-wrapper premium" data-security="<?php echo esc_attr($ajax_nonce);?>" data-slug="<?php echo esc_attr($theme_slug);?>" data-type="premium" data-buy="">

                        <div class="thumbnail-holder theme-details" style="background-image:url('<?php echo esc_url( $thumbnail ); ?>')"></div>
                        
                        <div class="buttons-wrapper">

                            <a href="<?php echo 'https://niteothemes.com/cmp-coming-soon-maintenance/?theme='.$theme_slug.'&utm_source=cmp&utm_medium=referral&utm_campaign='.$theme_slug;?>" target="_blank" class="theme-preview button"><i class="fas fa-external-link-alt"></i><?php _e('PREVIEW', 'cmp-coming-soon-maintenance');?></a>

                            <a href="theme-details" class="theme-details button"><i class="far fa-eye" aria-hidden="true"></i><?php _e('DETAILS', 'cmp-coming-soon-maintenance');?></a>

                        </div>

                        <div class="theme-inputs">
                            <a href="<?php echo esc_url ( $premium_theme['url'] );?>" target="_blank" class="cmp-purchase-theme"><i class="fas fa-cart-arrow-down"></i><?php echo sprintf(__('Get %s', 'cmp-coming-soon-maintenance'), ucwords( esc_html( str_replace('_', ' ', $theme_slug) ) ));?></a>
                        </div>
                    </div>

                    <?php
                }  ?>
            </fieldset>
        </td>
    </tr>

    <?php echo $this->render_settings->submit(); ?>

    </tbody>
    </table>
</div>

<?php 
} // if !empty($premium_themes 