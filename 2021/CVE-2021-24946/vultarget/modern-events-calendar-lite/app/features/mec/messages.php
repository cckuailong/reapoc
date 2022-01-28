<?php
/** no direct access **/
defined('MECEXEC') or die();

/** @var MEC_feature_mec $this */

$multilingual = $this->main->is_multilingual();
$locale = $this->main->get_backend_active_locale();

$messages = $this->main->get_messages();
$values = $this->main->get_messages_options(($multilingual ? $locale : NULL));
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
        <?php $this->main->get_sidebar_menu('messages'); ?>
    </div>

    <div class="wns-be-main">

        <div id="wns-be-notification"></div>

        <div id="wns-be-content">
            <div class="wns-be-group-tab">
                <div class="mec-container">
                    <form id="mec_messages_form">
                        <div class="mec-options-fields active">
                            <h2><?php _e('Messages', 'modern-events-calendar-lite'); ?></h2>
                            <p><?php _e("You can change some MEC messages here. For example if you like to change \"REGISTER\" button label, you can do it here. By the Way, if your website is a multilingual website, we recommend you to change the messages/phrases from language files.", 'modern-events-calendar-lite'); ?></p>
                            <div class="mec-form-row" id="mec_messages_form_container">
                                <ul class="mec-accordion mec-message-categories" id="mec_message_categories_wp">
                                    <?php foreach($messages as $cat_key=>$category): ?>
                                        <li class="mec-acc-label" data-key="<?php echo $cat_key; ?>" data-status="close">
                                            <div class="mec-acc-cat-name"><?php echo $category['category']['name']; ?></div>
                                            <ul id="mec-acc-<?php echo $cat_key; ?>">
                                                <?php foreach($category['messages'] as $key=>$message): ?>
                                                    <li>
                                                        <label for="<?php echo 'mec_m_'.$key; ?>"><?php echo $message['label']; ?></label>
                                                        <input id="<?php echo 'mec_m_'.$key; ?>" name="mec[messages][<?php echo $key; ?>]" type="text" placeholder="<?php echo esc_attr($message['default']); ?>" value="<?php echo (isset($values[$key]) and trim($values[$key])) ? esc_attr(stripslashes($values[$key])) : ''; ?>" />
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="mec-form-row">
                                <?php wp_nonce_field('mec_options_form'); ?>
                                <?php if($multilingual): ?>
                                <input name="mec_locale" type="hidden" value="<?php echo esc_attr($locale); ?>" />
                                <?php endif; ?>
                                <button style="display: none;" id="mec_messages_form_button" class="button button-primary mec-button-primary" type="submit"><?php _e('Save Changes', 'modern-events-calendar-lite'); ?></button>
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
        jQuery("#mec_messages_form_button").trigger('click');
    });
});

jQuery("#mec_messages_form").on('submit', function(event)
{
    event.preventDefault();
    
    // Add loading Class to the button
    jQuery(".dpr-save-btn").addClass('loading').text("<?php echo esc_js(esc_attr__('Saved', 'modern-events-calendar-lite')); ?>");
    jQuery('<div class="wns-saved-settings"><?php echo esc_js(esc_attr__('Settings Saved!', 'modern-events-calendar-lite')); ?></div>').insertBefore('#wns-be-content');

    var messages = jQuery("#mec_messages_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=mec_save_messages&"+messages,
        beforeSend: function()
        {
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
            setTimeout(function()
            {
                jQuery(".dpr-save-btn").removeClass('loading').text("<?php echo esc_js(esc_attr__('Save Changes', 'modern-events-calendar-lite')); ?>");
                jQuery('.wns-saved-settings').remove();
                jQuery('.mec-loarder-wrap').remove();
            }, 1000);
        }
    });
});
</script>