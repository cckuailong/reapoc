<?php
/** no direct access **/
defined('MECEXEC') or die();

$styling = $this->main->get_styling();
$fonts = include MEC::import('app.features.mec.webfonts.webfonts', true, true);

$google_fonts = array();
$google_fonts['none'] = array(
	'label'=>esc_html__('Default Font', 'modern-events-calendar-lite'),
	'variants'=>array('regular'),
	'subsets'=>array(),
	'category'=>'',
    'value'=>'',
);

if(is_array($fonts))
{
	foreach($fonts['items'] as $font)
    {
        $google_fonts[$font['family']] = array(
            'label'=>$font['family'],
            'variants'=>$font['variants'],
            'subsets'=>$font['subsets'],
            'category'=>$font['category'],
        );
    }
}
?>
<div class="wns-be-container wns-be-container-sticky">

    <div id="wns-be-infobar">
        <div class="mec-search-settings-wrap">
            <i class="mec-sl-magnifier"></i>
            <input id="mec-search-settings" type="text" placeholder="<?php esc_html_e('Search...' ,'modern-events-calendar-lite'); ?>">
        </div>        
        <a href="" id="" class="dpr-btn dpr-save-btn"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></a>
    </div>
    
    <div class="wns-be-sidebar">
        <?php $this->main->get_sidebar_menu('styling'); ?>
    </div>

    <div class="wns-be-main">

        <div id="wns-be-notification"></div>

        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                
                <div class="mec-container">
                    <form id="mec_styling_form">
                        <div class="mec-options-fields">
                            <h2><?php _e('Styling', 'modern-events-calendar-lite'); ?></h2>
                        <!-- Colorskin -->
                            <h5 class="mec-form-subtitle"><?php esc_html_e('Color Skin', 'modern-events-calendar-lite' ); ?></h5>
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Predefined Color Skin', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <ul class="mec-image-select-wrap">
                                        <?php
                                        $colorskins = array(
                                            '#40d9f1'=>'mec-colorskin-1',
                                            '#0093d0'=>'mec-colorskin-2',
                                            '#e53f51'=>'mec-colorskin-3',
                                            '#f1c40f'=>'mec-colorskin-4',
                                            '#e64883'=>'mec-colorskin-5',
                                            '#45ab48'=>'mec-colorskin-6',
                                            '#9661ab'=>'mec-colorskin-7',
                                            '#0aad80'=>'mec-colorskin-8',
                                            '#0ab1f0'=>'mec-colorskin-9',
                                            '#ff5a00'=>'mec-colorskin-10',
                                            '#c3512f'=>'mec-colorskin-11',
                                            '#55606e'=>'mec-colorskin-12',
                                            '#fe8178'=>'mec-colorskin-13',
                                            '#7c6853'=>'mec-colorskin-14',
                                            '#bed431'=>'mec-colorskin-15',
                                            '#2d5c88'=>'mec-colorskin-16',
                                            '#77da55'=>'mec-colorskin-17',
                                            '#2997ab'=>'mec-colorskin-18',
                                            '#734854'=>'mec-colorskin-19',
                                            '#a81010'=>'mec-colorskin-20',
                                            '#4ccfad'=>'mec-colorskin-21',
                                            '#3a609f'=>'mec-colorskin-22',
                                            '#333333'=>'mec-colorskin-23',
                                            '#D2D2D2'=>'mec-colorskin-24',
                                            '#636363'=>'mec-colorskin-25',
                                            );

                                            foreach($colorskins as $colorskin=>$values): ?>
                                            <li class="mec-image-select">
                                                <label for="<?php echo $values; ?>">
                                                    <input type="radio" id="<?php echo $values; ?>" name="mec[styling][mec_colorskin]" value="<?php echo $colorskin; ?>" <?php if(isset($styling['mec_colorskin']) && ($styling['mec_colorskin'] == $colorskin)) echo 'checked="checked"'; ?>>
                                                    <span class="<?php echo $values; ?>"></span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Custom Color Skin', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_color" name="mec[styling][color]" value="<?php echo (isset($styling['color']) ? $styling['color'] : ''); ?>" data-default-color="" />
                                </div>
                                <div class="mec-col-12">
                                    <p><?php esc_attr_e("If you want to select a predefined color skin, you must clear the color of this item", 'modern-events-calendar-lite'); ?></p>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_styling_dark_mode"><?php _e('Dark Mode', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="hidden" name="mec[styling][dark_mode]" value="0" />
                                    <input value="1" type="checkbox" id="mec_styling_dark_mode" name="mec[styling][dark_mode]" <?php if(isset($styling['dark_mode']) and $styling['dark_mode']) echo 'checked="checked"'; ?> />
                                    <span class="mec-tooltip">
                                        <div class="box top">
                                            <h5 class="title"><?php _e('Dark Mode', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('Enable it to turn on dark mode', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/style-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Advanced Options -->
                            <h5 class="mec-form-subtitle"><?php esc_html_e('Advanced Color Options (shortcodes)', 'modern-events-calendar-lite' ); ?></h5>
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Title', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_title_color" name="mec[styling][title_color]" value="<?php echo (isset($styling['title_color']) ? $styling['title_color'] : ''); ?>" data-default-color="" />
                                </div>
                            </div>
                            
                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Title Hover', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_title_color_hover" name="mec[styling][title_color_hover]" value="<?php echo (isset($styling['title_color_hover']) ? $styling['title_color_hover'] : ''); ?>" data-default-color="" />
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Content', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_content_color" name="mec[styling][content_color]" value="<?php echo (isset($styling['content_color']) ? $styling['content_color'] : ''); ?>" data-default-color="" />
                                </div>
                            </div>

                            <!-- Typography -->
                            <h5 class="mec-form-subtitle"><?php esc_html_e('Typography', 'modern-events-calendar-lite' ); ?></h5>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_h_fontfamily"><?php _e('Heading (Events Title) Font Family', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">

                                    <select class="mec-p-fontfamily" name="mec[styling][mec_h_fontfamily]" id="mec_h_fontfamily">
                                        <?php
                                        foreach($google_fonts as $google_font)
                                        {
                                            $variants = '';
                                            foreach($google_font['variants'] as $key=>$variant)
                                            {
                                                $variants .= $variant;
                                                if(next($google_font['variants']) == true) $variants .= ",";
                                            }

                                            $value = (isset($google_font['value']) ? $google_font['value'] : '['. $google_font['label'] .','. $variants .']');
                                            if($value == '['.__('Default Font', 'modern-events-calendar-lite').',regular]') $value = '';
                                            ?>
                                            <option value="<?php echo $value; ?>" <?php if(isset($styling['mec_h_fontfamily']) and ($styling['mec_h_fontfamily'] == $value)) echo 'selected="selected"'; ?>><?php echo $google_font['label']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_p_fontfamily"><?php _e('Paragraph Font Family', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">

                                    <select class="mec-p-fontfamily" name="mec[styling][mec_p_fontfamily]" id="mec_p_fontfamily">
                                        <?php
                                        foreach($google_fonts as $google_font)
                                        {
                                            $variants = '';
                                            foreach($google_font['variants'] as $key=>$variant)
                                            {
                                                $variants .= $variant;
                                                if(next($google_font['variants']) == true) $variants .= ",";
                                            }
                                            
                                            $value = (isset($google_font['value']) ? $google_font['value'] : '['. $google_font['label'] .','. $variants .']');
                                            if($value == '['.__('Default Font', 'modern-events-calendar-lite').',regular]') $value = '';
                                            ?>
                                            <option value="<?php echo $value; ?>" <?php if(isset($styling['mec_p_fontfamily'] ) && ($styling['mec_p_fontfamily'] == $value ) ) echo 'selected'; ?>><?php echo $google_font['label']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_styling_disable_gfonts"><?php _e('Disable Google Fonts', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="hidden" name="mec[styling][disable_gfonts]" value="0" />
                                    <input value="1" type="checkbox" id="mec_styling_disable_gfonts" name="mec[styling][disable_gfonts]" <?php if(isset($styling['disable_gfonts']) and $styling['disable_gfonts']) echo 'checked="checked"'; ?> />
                                    <span class="mec-tooltip">
                                        <div class="box top">
                                            <h5 class="title"><?php _e('Disable Google Fonts', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('To be GDPR compliant you may need to disable Google fonts! set "Default Font" value for font family and enable this option.', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/style-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>

                            </div>

                            <!-- Container Width -->
                            <h5 class="mec-form-subtitle"><?php esc_html_e('Container Width', 'modern-events-calendar-lite' ); ?></h5>
                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_styling_container_normal_width"><?php _e('Desktop Normal Screens', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_styling_container_normal_width" name="mec[styling][container_normal_width]" value="<?php echo ((isset($styling['container_normal_width']) and trim($styling['container_normal_width']) != '') ? $styling['container_normal_width'] : ''); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Desktop Normal Screens', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('You can enter your theme container size in this field', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/style-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <label class="mec-col-3" for="mec_styling_container_large_width"><?php _e('Desktop Large Screens', 'modern-events-calendar-lite'); ?></label>
                                <div class="mec-col-9">
                                    <input type="text" id="mec_styling_container_large_width" name="mec[styling][container_large_width]" value="<?php echo ((isset($styling['container_large_width']) and trim($styling['container_large_width']) != '') ? $styling['container_large_width'] : ''); ?>" />
                                    <span class="mec-tooltip">
                                        <div class="box left">
                                            <h5 class="title"><?php _e('Desktop Large Screens', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content"><p><?php esc_attr_e('You can enter your theme container size in this field', 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/style-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>    
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <?php do_action('mec_end_styling_settings', $styling); ?>

                            <!-- Other Styling Option -->
                            <h5 class="mec-form-subtitle"><?php esc_html_e('Other Styling Option', 'modern-events-calendar-lite' ); ?></h5>

                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Frontend Event Submission Color', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_fes_color" name="mec[styling][fes_color]" value="<?php echo (isset($styling['fes_color']) ? $styling['fes_color'] : ''); ?>" data-default-color="" />
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <div class="mec-col-3">
                                    <span><?php esc_html_e('Notifications Background', 'modern-events-calendar-lite' ); ?></span>
                                </div>
                                <div class="mec-col-9">
                                    <input type="text" class="wp-color-picker-field" id="mec_settings_notification_bg" name="mec[styling][notification_bg]" value="<?php echo (isset($styling['notification_bg']) ? $styling['notification_bg'] : ''); ?>" data-default-color="" />
                                </div>
                            </div>

                            <div class="mec-form-row">
                                <?php wp_nonce_field('mec_options_form'); ?>
                                <button  style="display: none;" id="mec_styling_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
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
        jQuery("#mec_styling_form_button").trigger('click');
    });
});

(function($)
{
	'use strict';
	$(document).ready(function()
    {
        //Initiate Color Picker
        $('.wp-color-picker-field').wpColorPicker();
    });
    
	$('.wpsa-browse').click(function(e)
    {
		e.preventDefault();
		var image = wp.media({
			title: 'Upload',
			multiple: false
		}).open()
		.on('select', function(e)
        {
			var uploaded_image = image.state().get('selection').first();
			var image_url = uploaded_image.toJSON().url;
			$('#mec_settings_upload').val(image_url);
		});
	});
})(jQuery);

jQuery("#mec_styling_form").on('submit', function(event)
{
	event.preventDefault();

    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    var styling = jQuery("#mec_styling_form").serialize();

    jQuery.ajax(
    {
    	type: "POST",
    	url: ajaxurl,
        data: "action=mec_save_styling&"+styling,
        beforeSend: function () {
            jQuery('.wns-be-main').append('<div class="mec-loarder-wrap mec-settings-loader"><div class="mec-loarder"><div></div><div></div><div></div></div></div>');
        },
    	success: function(data)
    	{
            // Remove the loading Class to the button
            setTimeout(function()
            {
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