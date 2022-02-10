<?php
global $current_user, $wpdb;
$user = get_userdata($current_user->ID);

?>

<div id="edit-profile-form">
    <form method="post" id="edit_profile" name="contact_form" action="" class="form">
        <?php wp_nonce_field(NONCE_KEY, '__wpdm_epnonce'); ?>
        <div class="card card-default dashboard-panel">
            <div class="card-header bg-white">
                <h3 class="m-0 pt-2 pb-2"><i class="fa fa-user-edit title-icon color-primary"></i> <?php echo  __('Basic Profile', WPDM_TEXT_DOMAIN); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="name"><?php _e( "Display name:" , WPDM_TEXT_DOMAIN );?> </label><input type="text" class="required form-control" required="required" value="<?php echo $user->display_name;?>" name="wpdm_profile[display_name]" id="fname"></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="username"><?php _e( "Username:" , WPDM_TEXT_DOMAIN );?></label><input type="text" class="required form-control" value="<?php echo $user->user_login;?>" id="username" readonly="readonly"></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="url"><?php _e( "Title:" , WPDM_TEXT_DOMAIN );?></label><input type="text" class="required form-control" name="wpdm_profile[title]" value="<?php echo get_user_meta($user->ID, '__wpdm_title', true);?>" id="title" ></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="email"><?php _e( "Email:" , WPDM_TEXT_DOMAIN );?></label><input type="text" class="required form-control" name="wpdm_profile[user_email]" value="<?php echo $user->user_email;?>" id="email" ></div></div>
                    <div class="col-md-12"><div class="form-group"><label for="email"><?php _e( "About Me:" , WPDM_TEXT_DOMAIN );?></label><textarea class="required form-control" name="wpdm_profile[description]" id="description" ><?php echo esc_attr(get_user_meta($user->ID, 'description', true));?></textarea></div></div>
                </div>
                <?php do_action('wpdm_update_profile_filed_html', $user); ?>
                <?php do_action('wpdm_update_profile_field_html', $user); ?>
            </div>
        </div>

        <div class="card card-default dashboard-panel mt-3">
            <div class="card-header bg-white">
                <h3 class="m-0 pt-2 pb-2"><i class="fa fa-key title-icon color-danger"></i> <?php _e( "Update Password" , WPDM_TEXT_DOMAIN ); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="new_pass"><?php _e( "New Password:" , WPDM_TEXT_DOMAIN );?> </label><input  autocomplete="off" placeholder="" type="password" class="form-control" value="" name="password" id="new_pass"> </div></div>
                    <div class="col-md-6"><div class="form-group"><label for="re_new_pass"><?php _e( "Re-type New Password:" , WPDM_TEXT_DOMAIN );?> </label><input autocomplete="off" type="password" value="" class="form-control" name="cpassword" id="re_new_pass"> </div></div>
                </div>
                <em class="note"><?php _e( "Keep empty if you don't want to change old password" , WPDM_TEXT_DOMAIN );?></em>
            </div>
        </div>

        <?php do_action("wpdm_edit_profile_form"); ?>


        <div class="card p-3 mt-3">
            <div class="text-right"><button type="submit" style="min-width: 250px" class="btn btn-lg btn-primary" id="edit_profile_sbtn"><i class="fas fa-hdd"></i> &nbsp;<?php _e( "Save Changes" , WPDM_TEXT_DOMAIN );?></button></div>
        </div>


    </form>
    <div id="edit-profile-msg">
    </div>
</div>
<div id="wpdm-fixed-top-center"></div>
<script>
    jQuery(function ($) {
        $('#edit_profile').on('submit', function (e) {
            e.preventDefault();
            var edit_profile_sbtn = $('#edit_profile_sbtn').html();
            $('#edit_profile_sbtn').html("<i class='fa fa-sync fa-spin'></i> <?php echo  esc_attr__( 'Please Wait...', WPDM_TEXT_DOMAIN ) ?>").attr('disabled','disabled');
            $(this).ajaxSubmit({
                success: function (res) {
                    WPDM.notify(res.msg, res.type, '#wpdm-fixed-top-center', 10000);
                    //$('#edit-profile-msg').html("<div class='alert alert-"+res.type+"' data-title='"+res.title+"'>"+res.msg+"</div>");
                    $('#edit_profile_sbtn').html(edit_profile_sbtn).removeAttr('disabled');
                }
            });
        });
    });
</script>
