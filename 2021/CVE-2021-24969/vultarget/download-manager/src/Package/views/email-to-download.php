<?php
/**
 * Author: shahnuralam
 * Date: 2018-12-28
 * Time: 11:58
 */
if (!defined('ABSPATH')) die();

    $idl = 0;
    $form_id = "__wpdm_email_2download_{$params['id']}";
    $form_button_label = isset($params['btn_label']) ? $params['btn_label'] : __( "Subscribe", "download-manager" );
    $section_id = "section_".uniqid();

?>

<div id="<?php echo $section_id;?>" class="<?php echo $section_id;?>">
<form id="<?php echo $form_id; ?>" class="<?php echo $form_id; ?>" method=post action="<?php echo wpdm_rest_url('email-to-download'); ?>" style="font-weight:normal;font-size:12px;padding:0px;margin:0px">

        <div class="wpdm-email-to-download">
            <h3><?php echo isset($params['title']) ? $params['title'] : ''; ?></h3>
            <?php echo isset($params['intro']) ? $params['intro'] : ''; ?>

            <input type=hidden name="__wpdm_ID" value="<?php echo $params['id']; ?>" />
            <input type=hidden name="name" value="" />

            <div class="media">
                <div class="media-body"><input type="email" required="required"  oninvalid="this.setCustomValidity('<?php echo __( "Please enter a valid email address" , "download-manager" ) ?>')" class="form-control form-control-lg group-item email-lock-mail" placeholder="<?php _e("Email Address", "download-manager"); ; ?>" size="20" id="email_<?php echo $params['id']; ?>" name="email" /></div>
                <div class="ml-3"><button id="wpdm_submit_<?php echo $params['id']; ?>" class="wpdm_submit btn btn-<?php echo isset($params['btn']) ? $params['btn'] : 'success'; ?> btn-lg group-item"  type=submit><?php echo $form_button_label; ?></button></div>
            </div>

        </div>
</form>
</div>

<script type="text/javascript">
    jQuery(function($){
        var sname = localStorage.getItem("email_lock_name");
        var semail = localStorage.getItem("email_lock_mail");

        if(sname != "undefined")
            $(".email-lock-mail").val(semail);
        if(sname != "undefined")
            $(".email-lock-name").val(sname);

        $(".<?php echo $form_id; ?>").submit(function(){
            var paramObj = {};
            WPDM.blockUI('.<?php echo $section_id; ?>');
            $.each($(this).serializeArray(), function(_, kv) {
                paramObj[kv.name] = kv.value;
            });
            var nocache = new Date().getMilliseconds();

            $(this).ajaxSubmit({
                url: '<?php echo wpdm_rest_url('email-to-download'); ?>',
                success:function(res){

                    WPDM.unblockUI('.<?php echo $section_id; ?>');
                    WPDM.notify(res.msg, res.type);
                }});

            return false;
        });
    });

</script>

