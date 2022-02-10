<?php

if(!defined('ABSPATH')) die();

?>
<div class="w3eden">
    <div id="wpdmlogin" <?php if(wpdm_query_var('action') == 'lostpassword') echo 'class="lostpass"'; ?>>
        <?php if(isset($params['logo']) && $params['logo'] != '' && !is_user_logged_in()){ ?>
            <div class="text-center wpdmlogin-logo">
                <a href="<?php echo home_url('/'); ?>"><img alt="Logo" src="<?php echo $params['logo'];?>" /></a>
            </div>
        <?php } ?>






        <?php do_action("wpdm_before_login_form"); ?>


        <form name="loginform" id="loginform" action="" method="post" class="login-form" >

            <input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />

            <div id="__signin_msg"><?php
                $wpdm_signup_success = \WPDM\__\Session::get('__wpdm_signup_success');
                if(isset($_GET['signedup'])){
                    if($wpdm_signup_success == '') $wpdm_signup_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));
                    ?>
                    <div class="alert alert-success dismis-on-click">
                        <?php echo  $wpdm_signup_success; ?>
                    </div>
                    <?php
                }
                ?></div>


            <?php
            if(isset($params['note_before']) && $params['note_before'] !== '') {  ?>
                <div class="alert alert-info alert-note-before mb-3" >
                    <?php echo  esc_attr($params['note_before']); ?>
                </div>
            <?php } ?>

            <?php echo  $this->formFields($params); ?>


            <?php  if(isset($params['note_after']) && $params['note_before'] !== '') {  ?>
                <div class="alert alert-info alter-note-after mb-3" >
                    <?php echo  esc_attr($params['note_after']); ?>
                </div>
            <?php } ?>

            <?php do_action("wpdm_login_form"); ?>
            <?php do_action("login_form"); ?>

            <div class="row login-form-meta-text text-muted mb-3" style="font-size: 10px">
                <div class="col-md-5"><label><input class="wpdm-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /><?php _e( "Remember Me" , WPDM_TEXT_DOMAIN ); ?></label></div>
                <div class="col-md-7 text-right"><a class="color-blue" href="<?php echo add_query_arg(['action' => 'lostpassword'], $_SERVER['REQUEST_URI']); ?>"><?php _e( "Forgot Password?" , WPDM_TEXT_DOMAIN ); ?></a></div>
            </div>

            <div class="row">
                <div class="col-md-12"><button type="submit" name="wp-submit" id="loginform-submit" class="btn btn-block btn-primary btn-lg"><i class="fas fa-user-shield"></i> &nbsp;<?php _e( "Login" , WPDM_TEXT_DOMAIN ); ?></button></div>
                <?php if(isset($regurl) && $regurl != ''){ ?>
                    <div class="col-md-12"><br/><a href="<?php echo $regurl; ?>" class="btn btn-block btn-link btn-xs wpdm-reg-link  color-primary"><?php _e( "Don't have an account yet?" , WPDM_TEXT_DOMAIN ); ?> <i class="fas fa-user-plus"></i> <?php _e( "Register Now" , WPDM_TEXT_DOMAIN ); ?></a></div>
                <?php } ?>
            </div>


            <input type="hidden" name="redirect_to" value="<?php echo $log_redirect; ?>" />



        </form>



        <?php do_action("wpdm_after_login_form"); ?>

    </div>


</div>
<script>
    jQuery(function ($) {
        <?php if(!isset($params['form_submit_handler']) || $params['form_submit_handler'] !== false){ ?>
        var llbl = $('#loginform-submit').html();
        $('#loginform').submit(function () {
            $('#loginform-submit').html("<i class='fa fa-spin fa-sync'></i> <?php _e( "Logging In..." , WPDM_TEXT_DOMAIN ); ?>").attr('disabled', 'disabled');
            WPDM.blockUI('#loginform');
            $(this).ajaxSubmit({
                error: function(error) {
                    WPDM.unblockUI('#loginform');
                    $('#loginform').prepend("<div class='alert alert-danger' data-title='<?php _e( "LOGIN FAILED!" , WPDM_TEXT_DOMAIN ); ?>'>"+error.responseJSON.message+"</div>");
                    $('#loginform-submit').html(llbl).removeAttr('disabled');
                    <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                    try {
                        grecaptcha.reset();
                    } catch (e) {

                    }
                    <?php } ?>
                },
                success: function (res) {
                    WPDM.unblockUI('#loginform');
                    if (!res.success) {
                        $('form .alert-danger').hide();
                        $('#loginform').prepend("<div class='alert alert-danger' data-title='<?php _e( "LOGIN FAILED!" , WPDM_TEXT_DOMAIN ); ?>'>"+res.message+"</div>");
                        $('#loginform-submit').html(llbl).removeAttr('disabled');
                        <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                        try {
                            grecaptcha.reset();
                        } catch (e) {

                        }
                        <?php } ?>
                    } else {
                        $('#loginform-submit').html("<i class='fa fa-sun fa-spider'></i> "+res.message);
                        location.href = "<?php echo $log_redirect; ?>";
                    }
                }
            });
            return false;
        });
        <?php } ?>
        $('body').on('click', 'form .alert-danger', function(){
            $(this).slideUp();
        });

    });
</script>
