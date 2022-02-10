<?php
/** no direct access **/
defined('MECEXEC') or die();

$styles = $this->main->get_styles();
?>

<div class="wns-be-container wns-be-container-sticky">

    <div id="wns-be-infobar">
        <a href="" id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

    <div class="wns-be-sidebar">
        <?php $this->main->get_sidebar_menu('customcss'); ?>
    </div>

    <div class="wns-be-main">

        <div id="wns-be-notification"></div>

        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">
                    <form id="mec_styles_form">
                        <div class="mec-options-fields">
                            <h2><?php _e('Custom Styles', 'modern-events-calendar-lite'); ?></h2>
                            <div class="mec-form-row">
                                <textarea id="mec_styles_CSS" name="mec[styles][CSS]"><?php echo (isset($styles['CSS']) ? stripslashes($styles['CSS']) : ''); ?></textarea>
                                <p class="mec-col-12 description"><?php _e("If you're a developer or you have knowledge of CSS codes, you can place your style codes here. These codes will be included in your theme frontend after all styles so they will override MEC default (or theme) styles.", 'modern-events-calendar-lite'); ?></p>
                                <?php wp_nonce_field('mec_options_form'); ?>
                                <button style="display: none;" id="mec_styles_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="wns-be-footer">
        <a href="" id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>

</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
    jQuery(".dpr-save-btn").on('click', function(event)
    {
        event.preventDefault();
        jQuery("#mec_styles_form_button").trigger('click');
    });
});

jQuery("#mec_styles_form").on('submit', function(event)
{
    event.preventDefault();
    
    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    var styles = jQuery("#mec_styles_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=mec_save_styles&"+styles,
        beforeSend: function()
        {
            jQuery('.wns-be-main').append('<div class="mec-loarder-wrap mec-settings-loader"><div class="mec-loarder"><div></div><div></div><div></div></div></div>');
        },
        success: function(data)
        {
            // Remove the loading Class to the button
            setTimeout(function(){
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
            }, 1000);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            setTimeout(function(){
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
            }, 1000);
        }
    });
});
</script>