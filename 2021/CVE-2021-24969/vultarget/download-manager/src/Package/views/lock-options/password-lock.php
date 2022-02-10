<?php
/**
 * User: shahnuralam
 * Date: 2019-01-25
 * Time: 20:49
 */
if (!defined('ABSPATH')) die();
?>
<div class="card card-default">
    <div class="card-header">
        <span id="enter_pass_label_<?php echo $package['ID']; ?>"><?php _e( "Enter Correct Password to Download" , "download-manager" ); ?></span>
        <span id="valid_pass_label_<?php echo $package['ID']; ?>" style="display: none"><?php _e( "Your Download Link is Ready" , "download-manager" ); ?></span>
    </div>
    <div class="card-body" id="wpdmdlp_<?php echo  $field_id; ?>">
        <div id="msg_<?php echo $package['ID']; ?>" style="display:none;"><?php _e( "Processing..." , "download-manager" ); ?></div>
        <form id="wpdmdlf_<?php echo $field_id; ?>" method=post action="<?php echo home_url('/'); ?>" style="margin-bottom:0px;">
            <input type=hidden name="__wpdm_ID" value="<?php echo $package['ID']; ?>" />
            <input type=hidden name="dataType" value="json" />
            <input type=hidden name="execute" value="wpdm_getlink" />
            <input type=hidden name="action" value="wpdm_ajax_call" />
            <div class="input-group input-group-lg">
                <input type="password"  class="form-control" placeholder="<?php _e( "Enter Password" , "download-manager" ); ?>" size="10" id="password_<?php echo $field_id; ?>" name="password" />
                <span class="input-group-btn input-group-append"><input id="wpdm_submit_<?php echo $field_id; ?>" class="wpdm_submit btn btn-secondary" type="submit" value="<?php _e( "Submit" , "download-manager" ); ?>" /></span>
            </div>

        </form>

        <script type="text/javascript">
            jQuery("#wpdmdlf_<?php echo $field_id; ?>").submit(function(){
                var ctz = new Date().getMilliseconds();
                jQuery("#msg_<?php echo  $package['ID']; ?>").html('<div disabled="disabled" class="btn btn-lg btn-info btn-block"><?php _e( "Processing..." , "download-manager" ); ?></div>').show();
                jQuery("#wpdmdlf_<?php echo  $field_id; ?>").hide();
                jQuery(this).removeClass("wpdm_submit").addClass("wpdm_submit_wait");
                jQuery(this).ajaxSubmit({
                    url: "<?php echo wpdm_rest_url('validate-password'); ?>",
                    success: function(res){

                        jQuery("#wpdmdlf_<?php echo  $field_id; ?>").hide();
                        jQuery("#msg_<?php echo  $package['ID']; ?>").html("verifying...").css("cursor","pointer").show().click(function(){ jQuery(this).hide();jQuery("#wpdmdlf_<?php echo  $field_id; ?>").show(); });
                        if(res.success === true) {
                            window.open(res.downloadurl, '_blank');
                            jQuery("#wpdmdlf_<?php echo  $field_id; ?>").html("<a style='color:#ffffff !important' target='_blank' class='btn btn-success btn-block btn-lg' href='"+res.downloadurl+"'><?php _e( "Download" , "download-manager" ); ?></a>");
                            jQuery("#msg_<?php echo  $package['ID']; ?>").hide();
                            jQuery("#enter_pass_label_<?php echo  $package['ID']; ?>").hide();
                            jQuery("#wpdmdlf_<?php echo  $field_id; ?>").show();
                            jQuery("#valid_pass_label_<?php echo  $package['ID']; ?>").show();
                        } else {
                            jQuery("#msg_<?php echo $package['ID']; ?>").html('<div class="btn btn-lg btn-danger btn-block" style="font-size: 8pt">'+res.message+"</div>");
                        }
                    }
                });
                return false;
            });
        </script>
    </div>
</div>
