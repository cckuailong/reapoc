<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_publish_info.php'); else {
?>


    <div id="rm-form-publish-info">

        <div class="rmrow">
        <div id="rm-form-publish-shortcode-info">
            <?php _e('Paste following shortcode in a post or page to publish this form.', 'custom-registration-form-builder-with-submission-manager'); ?>
            <div id="rm-form-publish-shortcode" class="rmcode">
                <span id="rmformshortcode"><?php echo "[RM_Form id='{$data->form_id}']"; ?></span>
                <button onclick="rm_copy_content(document.getElementById('rmformshortcode'))"><?php _e('Copy', 'custom-registration-form-builder-with-submission-manager'); ?></button>
            </div>
        </div>
            
        </div>
        
<!--        <div class="rmrow">
        <div id="rm-form-publish-embedcode-info">
            Or, you can use following embed code to display this form outside Wordpress.
            <div id="rm-form-publish-embedcode" class="rmcode">
                <span id="rmformembedcode">&lt;<?php echo admin_url('admin-ajax.php?action=registrationmagic_embedform&rm_form_id=' . $data->form_id); ?>"&gt;</span>
                <button onclick="rm_copy_content(document.getElementById('rmformembedcode'))">Copy</button>
            </div>
        </div>
        </div>-->
        
        <div style="display:none" id="rm_msg_copied_to_clipboard"><?php _e('Copied to clipboard', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div style="display:none" id="rm_msg_not_copied_to_clipboard"><?php _e('Could not be copied. Try manually.', 'custom-registration-form-builder-with-submission-manager'); ?></div>
    </div>


<script>
function rm_copy_content(target) {

    var text_to_copy = jQuery(target).text();

    var tmp = jQuery("<input id='fd_form_shortcode_input' readonly>");
    var target_html = jQuery(target).html();
    jQuery(target).html('');
    jQuery(target).append(tmp);
    tmp.val(text_to_copy).select();
    var result = document.execCommand("copy");

    if (result != false) {
        jQuery(target).html(target_html);
        jQuery("#rm_msg_copied_to_clipboard").fadeIn('slow');
        jQuery("#rm_msg_copied_to_clipboard").fadeOut('slow');
    } else {
        jQuery(document).mouseup(function (e) {
            var container = jQuery("#fd_form_shortcode_input");
            if (!container.is(e.target) // if the target of the click isn't the container... 
                    && container.has(e.target).length === 0) // ... nor a descendant of the container 
            {
                jQuery(target).html(target_html);
            }
        });
    }
}
</script>
<?php } ?>