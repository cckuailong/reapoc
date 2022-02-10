<div class="mystickyelements-help-form">
    <form action="<?php echo admin_url( 'admin-ajax.php' ) ?>" method="post" id="mystickyelements-help-form">
        <div class="mystickyelements-help-header">
            <b>Gal Dubinski</b> Co-Founder at Premio
        </div>
        <div class="mystickyelements-help-content">
            <p><?php echo __("Hello! Are you experiencing any problems with My Sticky Elements? Please let me know :)", "mystickyelements") ?></p>
            <div class="mystickyelements-form-field">
                <input type="text" name="user_email" id="user_email" placeholder="<?php echo __("Email", "mystickyelements") ?>">
            </div>
            <div class="mystickyelements-form-field">
                <textarea type="text" name="textarea_text" id="textarea_text" placeholder="<?php echo __("How can I help you?", "mystickyelements") ?>"></textarea>
            </div>
            <div class="form-button">
                <button type="submit" class="mystickyelements-help-button" ><?php echo __("Chat") ?></button>
                <input type="hidden" name="action" value="mystickyelements_admin_send_message_to_owner"  >
                <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce("mystickyelements_send_message_to_owner") ?>">
            </div>
			<p class="mystickyelements-help-center">
				Or
			</p>
			<p class="mystickyelements-help-center" >
				<a href="https://premio.io/help/mystickyelements/?utm_source=pluginchat" target="_blank" >Visit our Help Center >></a>
			</p>
        </div>
    </form>
</div>
<div class="mystickyelements-help-btn">
    <a class="mystickyelements-help-tooltip" href="javascript:;"><img src="<?php echo MYSTICKYELEMENTS_URL ?>images/owner.png" alt="<?php echo __("Need help?", "mystickyelements") ?>"  /></a>
    <span class="tooltiptext"><?php echo __("Need help?", "mystickyelements") ?></span>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery("#mystickyelements-help-form").on( 'submit', function(){			
            jQuery(".mystickyelements-help-button").attr("disabled",true);
            jQuery(".mystickyelements-help-button").text("<?php echo __("Sending Request...") ?>");
            formData = jQuery(this).serialize();
            jQuery.ajax({
                url: "<?php echo admin_url( 'admin-ajax.php' ) ?>",
                data: formData,
                type: "post",
                success: function(responseText){
                    jQuery("#mystickyelements-help-form").find(".error-message").remove();
                    jQuery("#mystickyelements-help-form").find(".input-error").removeClass("input-error");                    
                    responseArray = jQuery.parseJSON(responseText);
                    if(responseArray.error == 1) {
                        jQuery(".mystickyelements-help-button").attr("disabled",false);
                        jQuery(".mystickyelements-help-button").text("<?php echo __("Chat", "mystickyelements") ?>");
                        for(i=0;i<responseArray.errors.length;i++) {
                            jQuery("#"+responseArray.errors[i]['key']).addClass("input-error");
                            jQuery("#"+responseArray.errors[i]['key']).after('<span class="error-message">'+responseArray.errors[i]['message']+'</span>');
                        }
                    } else if(responseArray.status == 1) {
                        jQuery(".mystickyelements-help-button").text("<?php echo __("Done!", "mystickyelements") ?>");
                        setTimeout(function(){
                            jQuery(".mystickyelements-help-header").remove();
                            jQuery(".mystickyelements-help-content").html("<p class='success-p'><?php echo __("Your message was sent successfully.", "mystickyelements") ?></p>");
                        },1000);
                    } else if(responseArray.status == 0) {
                        jQuery(".mystickyelements-help-content").html("<p class='error-p'><?php echo __("There is some problem in sending request. Please send us mail on <a href='mailto:contact@premio.io'>contact@premio.io</a>", "mystickyelements") ?></p>");
                    }
                }
            });
            return false;
        });
        jQuery(".mystickyelements-help-tooltip").on( 'click', function(e){
            e.stopPropagation();
            jQuery(".mystickyelements-help-form").toggleClass("active");
        });
        jQuery(".mystickyelements-help-form").on( 'click', function(e){
            e.stopPropagation();
        });
        jQuery("body").on( 'click', function(){
            jQuery(".mystickyelements-help-form").removeClass("active");
        });
    });
</script>