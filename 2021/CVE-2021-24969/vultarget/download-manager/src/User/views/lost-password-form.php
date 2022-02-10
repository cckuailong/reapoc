<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 19/9/19 11:40
 * Version: 1.1
 * Updated: 23/01/20 10:25
 */

if(!defined("ABSPATH")) die();
?>
<div class="w3eden">
    <div id="wpdmlogin" class="lostpass">
        <form name="loginform" id="resetPassword" action="<?php echo admin_url('/admin-ajax.php?action=resetPassword'); ?>" method="post" class="login-form" >
            <?php wp_nonce_field(NONCE_KEY,'__wpdm_reset_pass' ); ?>
            <h3 style="margin: 0 0 10px"><?php _e( "Lost Password?" , "download-manager" ); ?></h3>
            <p>
                <?php _e('Please enter your username or email address. You will receive a link to create a new password via email.', 'download-manager'); ?>
            </p>
            <div class="form-group">
                <div class="input-wrapper">
                    <label><?php echo __( "Username or Email", "download-manager" ); ?></label>
                    <input placeholder="<?php _e( "Username or Email" , "download-manager" ); ?>" required="required" type="text" name="user_login" id="user_login" class="form-control required text" value="" size="20" tabindex="38" />
                </div>
            </div>

            <div class="form-group">
                <button type="submit" name="wp-submit" id="resetPassword-submit" class="btn btn-block btn-info btn-lg"><i class="fa fa-key"></i> &nbsp; <?php _e( "Reset Password" , "download-manager" ); ?></button>
            </div>
            <div class="row">
                <div class="col-md-12 text-center small">
                    <a href="<?php echo home_url('/') ?>" class="color-info btn btn-link btn-xs"><i class="fab fa-fort-awesome-alt"></i> <?php _e("Home", "download-manager"); ?></a> <span class="text-muted">&nbsp; </span>
                    <a href="<?php the_permalink(); ?>" class="color-info btn btn-link btn-xs"><i class="fa fa-lock"></i> <?php _e("Login", "download-manager");  ?></a> <span class="text-muted">&nbsp; </span>
                </div>
            </div>

        </form>
    </div>
</div>
<script>
    if(__added_once === undefined) {
        var __added_once = 1;
        jQuery(function ($) {
            var __reset = 0, __progress = 0;
            var llbl = $('#resetPassword-submit').html();
            $('#resetPassword').submit(function (e) {
                e.preventDefault();

                if (__reset === 1) {

                    $('#resetPassword').prepend("<div class='alert alert-success' data-title='<?php _e("MAO: SENT!", "download-manager"); ?>'><?php _e("Password reset link sent to your email.", "download-manager"); ?></div>");
                    $('#resetPassword-submit').attr('disabled', 'disabled');
                    return false;
                }
                if (__progress === 0) {
                    __progress = 1;
                    $('#resetPassword-submit').html("<i class='fa fa-spin fa-sync'></i> <?php _e("Please Wait...", "download-manager"); ?>");
                    $(this).ajaxSubmit({
                        success: function (res) {
                            __progress = 0;
                            if (res.match(/error/)) {
                                $('form .alert').hide();
                                $('#resetPassword').prepend("<div class='alert alert-danger' data-title='<?php _e("ERROR!", "download-manager"); ?>'><?php _e("Account not found.", "download-manager"); ?></div>");
                                $('#resetPassword-submit').html(llbl);
                            } else {
                                __reset = 1;
                                $('form .alert').hide();
                                $('#resetPassword').prepend("<div class='alert alert-success' data-title='<?php _e("MAIL SENT!", "download-manager"); ?>'><?php _e("Please check your inbox.", "download-manager"); ?></div>");
                                $('#resetPassword-submit').html('<i class="fas fa-check-double"></i>').attr('disabled', 'disabled');
                            }
                        }
                    });
                }
                return false;
            });

            $('body').on('click', 'form .alert-danger', function () {
                $(this).slideUp();
            });

        });
    }
</script>
