<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="table-wrapper seo">
    <h3><?php _e('SEO Settings', 'cmp-coming-soon-maintenance');?></h3>
    <table class="seo">
    <tbody>

    <tr>
        <th><?php _e('Favicon', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <input type="hidden" class="widefat" id="niteoCS-favicon-id" name="niteoCS_favicon_id" value="<?php echo esc_attr( $niteoCS_favicon_id ); ?>" />
                <input id="add-favicon" type="button" class="button" value="Select Favicon" />
                
                <div class="favicon-wrapper">
                    <?php 
                    if ( isset($niteoCS_favicon_url) && $niteoCS_favicon_url !== '' ) {
                        echo '<img src="'.esc_url($niteoCS_favicon_url).'" alt="">';
                    } ?>
                </div>
                <span class="cmp-hint">* <?php _e('By default your standard Favicon will be used but you can override it for CMP page by selecting different Favicon.', 'cmp-coming-soon-maintenance');?></span>
                <br><br>
                <input id="delete-favicon" type="button" class="button" value="Remove Favicon" />
            </fieldset>
        </td>
    </tr>

    <tr class="seo-title">
        <th><?php _e('SEO Title', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <input type="text" name="niteoCS_title" id="niteoCS_title" value="<?php echo esc_attr( $niteoCS_title); ?>" class="regular-text code">
                <span class="cmp-hint">* <?php _e('It is recommended to keep title under 60 characters.', 'cmp-coming-soon-maintenance');?></span>
            </fieldset>
        </td>
    </tr>

    <tr>
        <th><?php _e('SEO Description', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <textarea name="niteoCS_descr" id="niteoCS_descr" class="code"><?php echo esc_attr( $niteoCS_descr); ?></textarea>
                <span class="cmp-hint">* <?php _e('It is recommended to keep description between 50–300 characters.', 'cmp-coming-soon-maintenance');?></span>
            </fieldset>
        </td>
    </tr>

    <tr>
        <th><?php _e('SEO Image', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <input type="hidden" class="widefat" id="niteoCS-seo_img-id" name="niteoCS_seo_img_id" value="<?php echo esc_attr( $niteoCS_seo_img_id ); ?>" />
                <input id="add-seo_img" type="button" class="button" value="Select Image" />
                
                <div class="seo_img-wrapper">
                    <?php 
                    if ( isset( $niteoCS_seo_img_url ) && $niteoCS_seo_img_url !== '' ) {
                        echo '<img src="'.esc_url( $niteoCS_seo_img_url ).'" alt="">';
                    } ?>
                </div>
                <span class="cmp-hint">* <?php _e('By default selected Background image is displayed on Social Networks if your Website is shared. You can overwrite the image by selecting your custom image here.', 'cmp-coming-soon-maintenance');?></span>
                <br><br>
                <input id="delete-seo_img" type="button" class="button" value="Remove Image" />
            </fieldset>
        </td>
    </tr>

    <tr>
        <th><?php _e('Search Engine Visibility', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <label><input id="seo-visibility" name="niteoCS_seo_visibility" type="checkbox" class="regular-text code" <?php checked( '0', $seo_visibility );?> /><?php _e('Discourage search engines from indexing this site - applies only for CMP page.', 'cmp-coming-soon-maintenance');?></label><br>
                <span class="cmp-hint">* <?php _e('It is up to search engines to honor this request.', 'cmp-coming-soon-maintenance');?></span>
                
            </fieldset>
        </td>
    </tr>

    <tr>
        <th><?php _e('No-cache Headers', 'cmp-coming-soon-maintenance');?></th>
        <td>
            <fieldset>
                <label><input id="seo-nocache" name="niteoCS_seo_nocache" type="checkbox" class="regular-text code" <?php checked( '1', $seo_nocache );?> /><?php _e('Send no-cache headers. If you don\'t want the CMP page\'s preview to be cached by Facebook or other social media then enable this option.', 'cmp-coming-soon-maintenance');?></label>  
            </fieldset>
        </td>
    </tr>

    <?php echo $this->render_settings->submit(); ?>

    </tbody> 
    </table>
</div>

<div class="table-wrapper seo">
    <h3><?php _e('Website Analytics', 'cmp-coming-soon-maintenance');?></h3>
    <table class="seo">
    <tbody>

        <tr>
            <th>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php _e('Analytics', 'cmp-coming-soon-maintenance');?></span>
                    </legend>

                    <p>
                        <label title="<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>">
                            <input type="radio" class="analytics" name="niteoCS_analytics_status" value="disabled"<?php checked( 'disabled', $niteoCS_analytics_status );?>>&nbsp;<?php _e('Disabled', 'cmp-coming-soon-maintenance');?>
                        </label>
                    </p>

                    <p>
                        <label title="<?php _e('Google Analytics', 'cmp-coming-soon-maintenance');?>">
                            <input type="radio" class="analytics" name="niteoCS_analytics_status" value="google"<?php checked( 'google', $niteoCS_analytics_status );?>>&nbsp;<?php _e('Google Analytics', 'cmp-coming-soon-maintenance');?>
                        </label>
                    </p>

                    <p>
                        <label title="<?php _e('Other', 'cmp-coming-soon-maintenance');?>">
                            <input type="radio" class="analytics" name="niteoCS_analytics_status" value="other"<?php checked( 'other', $niteoCS_analytics_status );?>>&nbsp;<?php _e('Other', 'cmp-coming-soon-maintenance');?>
                        </label>
                    </p>

                </fieldset>
            </th>

            <td>
                <fieldset>
                    <p class="analytics-switch disabled"><?php _e('Analytics is disabled', 'cmp-coming-soon-maintenance');?></p>

                    <div class="analytics-switch google">
                        <h4 for="niteoCS_analytics"><?php _e('Insert Google Analytics Tracking ID', 'cmp-coming-soon-maintenance');?></h4>
                        <input type="text" name="niteoCS_analytics" value="<?php echo esc_attr( $niteoCS_analytics ); ?>" class="regular-text code" placeholder="UA-xxxxxx-xx"/>
                    </div>

                    <div class="analytics-switch other">
                        <h4 for="niteoCS_analytics_other"><?php _e('Insert your the code provided by your Analytics Plugin or Website.', 'cmp-coming-soon-maintenance');?></h4>
                        <textarea name="niteoCS_analytics_other" rows="5" class="code"><?php echo stripslashes( $niteoCS_analytics_other ); ?></textarea>
                    </div>
                </fieldset>
            </td>
        </tr>

    <?php echo $this->render_settings->submit(); ?>

    </tbody>
    </table>
</div>