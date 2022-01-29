<?php 
if (!defined('WPINC')) {
    die('Closed');
}
$help_link = "https://registrationmagic.com/feedback-support-form/";
$deactivate_reasons = array(
                    'feature_not_available'=> array(
                                'title' => '<span class="rm-feedback-emoji">&#x1f61e;</span>'.__("Doesn't have the feature I need",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => __("Please let us know the missing feature...",'custom-registration-form-builder-with-submission-manager'),
                                'show_help_link' => false
                        ),
                         'feature_not_working'=> array(
                                'title'=> '<span class="rm-feedback-emoji">&#x1f615;</span>'.__("One of the feature didn't worked",'custom-registration-form-builder-with-submission-manager'),
                                'input_placeholder'=>  '',
                                'show_help_link' => __("Get free help to fix the feature",'custom-registration-form-builder-with-submission-manager')
                         ),           
			'found_a_better_plugin' => array(
				'title' => '<span class="rm-feedback-emoji">&#x1f60a;</span>'.__("Moved to a different plugin",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => __("Could you please share the plugin's name",'custom-registration-form-builder-with-submission-manager'),
                                'show_help_link' => false
			),
                        'plugin_broke_site' => array(
				'title' => '<span class="rm-feedback-emoji">&#x1f621;</span>'.__("The plugin broke my site",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => '',
                                'show_help_link' => __("Get free help to fix the site",'custom-registration-form-builder-with-submission-manager')
			),
                        'plugin_stopped_working' => array(
				'title' => '<span class="rm-feedback-emoji">&#x1f620;</span>'.__("The plugin stopped working",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => '',
                                'show_help_link' => __("Get free help to fix the issue",'custom-registration-form-builder-with-submission-manager')
			),
			'temporary_deactivation' => array(
				'title' => '<span class="rm-feedback-emoji">&#x1f60a;</span>'.__("It's a temporary deactivation",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => '',
                                'show_help_link' => false
			),
                        'upgrade'=> array(
                            'title'=> '<span class="rm-feedback-emoji">&#x1f601;</span>'.__("Upgraded to Premium",'custom-registration-form-builder-with-submission-manager'),
                            'input_placeholder' => '',
                            'show_help_link' => false
                        ),
			'other' => array(
				'title' => '<span class="rm-feedback-emoji">&#x1f610;</span>'.__("Other",'custom-registration-form-builder-with-submission-manager'),
				'input_placeholder' => __("Please share the reason",'custom-registration-form-builder-with-submission-manager'),
                                'show_help_link' => false
			),
	);
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                
                var rmDeactivateLocation;
                // Shows feedback dialog     
                jQuery('#the-list').find( '[data-slug="custom-registration-form-builder-with-submission-manager"] span.deactivate a' ).click(function(event){
                    jQuery("#rm-deactivate-feedback-dialog-wrapper, .rm-modal-overlay").show();
                    rmDeactivateLocation= jQuery(this).attr('href');
                    event.preventDefault();
                });
                
                jQuery("#rm-feedback-btn").click(function(){
                    var selectedVal= jQuery("input[name='rm_feedback_key']:checked").val();
                    if(selectedVal===undefined){
                        location.href= rmDeactivateLocation;
                        return;
                    }
                        
                    var feedbackInput= jQuery("input[name='reason_"+ selectedVal + "']");
                     var data = {
                        'action': 'rm_post_feedback',
                        'feedback': jQuery("input[name='rm_feedback_key']:checked").val(),
                        'msg': feedbackInput.val()
                        
                    };
                    jQuery(".rm-ajax-loader").show();
                    jQuery.post(ajaxurl, data, function (response) {
                         jQuery(".rm-ajax-loader").hide();
                         location.href= rmDeactivateLocation;  
                    });
                });
                
                jQuery("input[name='rm_feedback_key']").change(function(){
                  
                       var selectedVal= jQuery(this).val();
                       var reasonElement= jQuery("#reason_" + selectedVal);
                       jQuery(".rm-deactivate-feedback-dialog-input-wrapper .rminput").hide();
                       if(reasonElement!==undefined)
                       {
                         reasonElement.show();  
                       }
                       var helplinkElement= jQuery("#help_link_" + selectedVal);
                       if(typeof helplinkElement !== "undefined")
                       {
                         helplinkElement.show();  
                       } else {
                           helplinkElement.hide();  
                       }
                       
                       
                });
                
                jQuery("#rm-feedback-cancel-btn").click(function(){
                    jQuery("#rm-deactivate-feedback-dialog-wrapper").hide();
                });
                
                jQuery(".rm-modal-close").click(function(){
                    jQuery(".rm-modal-view").hide();
                    
                });
                
                
                
            });
            
            
        </script>    
        <div class="rmagic rm-hide-version-number">
            <div id="rm-deactivate-feedback-dialog-wrapper"  class="rm-modal-view" style="display:none; float:right">
                <div class="rm-modal-overlay"></div>
                <div  class="rm-modal-wrap rm-deactivate-feedback" >

                    <div class="rm-modal-titlebar rm-new-form-popup-header">
                        <div class="rm-modal-title">
                            <?php _e("RegistrationMagic Feedback",'custom-registration-form-builder-with-submission-manager') ?>
                        </div>
                        <span  class="rm-modal-close">&times;</span>
                    </div>
                    <div class="rm-modal-container">
                        <form id="rm-deactivate-feedback-dialog-form" method="post">
                            <input type="hidden" name="action" value="rm_deactivate_feedback" />
                            <div class="rmrow">
                                <div id="rm-deactivate-feedback-dialog-form-caption"><?php _e("If you have a moment, please share why you are deactivating RegistrationMagic:",'custom-registration-form-builder-with-submission-manager') ?></div>
                                <div id="rm-deactivate-feedback-dialog-form-body">
                                    <?php foreach ($deactivate_reasons as $reason_key => $reason) : ?>
                                        <div class="rm-deactivate-feedback-dialog-input-wrapper">                                            
                                            <input id="rm-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="rm-deactivate-feedback-dialog-input" type="radio" name="rm_feedback_key" value="<?php echo esc_attr($reason_key); ?>" />
                                            <label for="rm-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="rm-deactivate-feedback-dialog-label"><?php echo $reason['title']; ?></label>
                                            <?php if (!empty($reason['input_placeholder'])) : ?>
                                                <div class="rminput" id="reason_<?php echo esc_attr($reason_key); ?>" style="display:none" ><input class="rm-feedback-text" type="text" name="reason_<?php echo esc_attr($reason_key); ?>" placeholder="<?php echo esc_attr($reason['input_placeholder']); ?>" /></div>
                                            <?php endif; ?>
                                            <?php if($reason['show_help_link']): ?>
                                                <div class="rminput" id="help_link_<?php echo esc_attr($reason_key); ?>" style="display:none">
                                                    <a class="rm_help_link" href="<?php echo $help_link; ?>" target="_blank"> <?php echo $reason['show_help_link']; ?> </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <div class="rm-ajax-loader" style="display:none">
                                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                                <span class="sr-only"><?php _e("Loading...",'custom-registration-form-builder-with-submission-manager') ?></span>
                            </div>

                            <div class="rm-modal-footer rmrow">
                            <input type="button" id="rm-feedback-cancel-btn" class="rm-feedback-cancel-btn" value="← &nbsp; Cancel"/>
                            <input type="button" id="rm-feedback-btn" value="<?php _e("Submit & Deactivate",'custom-registration-form-builder-with-submission-manager') ?>"/>
                      
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>