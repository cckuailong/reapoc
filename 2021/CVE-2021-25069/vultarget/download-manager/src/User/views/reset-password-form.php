<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 19/9/19 11:41
 */

use WPDM\__\__;
use WPDM\__\Crypt;

if(!defined("ABSPATH")) die();

$login = Crypt::decrypt(__::query_var('login'));
$user = check_password_reset_key(__::query_var('key'), $login);
if(!is_wp_error($user)){
    //\WPDM\__\Session::set('__up_user', $user);

    ?>
<div class="w3eden">
    <div id="wpdmlogin" class="lostpass">
        <form name="loginform" id="updatePassword" action="<?php echo admin_url('/admin-ajax.php?action=updatePassword'); ?>" method="post" class="login-form" >
            <?php wp_nonce_field(NONCE_KEY,'__wpdm_update_pass' ); ?>
            <input type="hidden" name="__up_user" value="<?php echo Crypt::encrypt($user); ?>">
            <h3><?php _e( "New Password" , "download-manager" ); ?></h3>
            <p>
                <?php _e('Please enter a new password', 'download-manager'); ?>
            </p>
            <div class="form-group">
                <input placeholder="<?php _e( "New Password" , "download-manager" ); ?>" type="password" name="password" id="password" class="form-control form-control-lg required text" value="" size="20" />
            </div>

            <div class="form-group">
                <input placeholder="<?php _e( "Confirm Password" , "download-manager" ); ?>" type="password" name="cpassword" id="cpassword" class="form-control form-control-lg required text" value="" size="20" />
            </div>

            <div class="row">
                <div class="col-md-12"><button type="submit" name="wp-submit" id="updatePassword-submit" class="btn btn-block no-radius btn-success btn-lg"><i class="fa fa-key"></i> &nbsp; <?php _e( "Update Password" , "download-manager" ); ?></button></div>
            </div>

        </form>
    </div>
</div>
    <script>
        jQuery(function ($) {
            var llbl = $('#updatePassword-submit').html();
            $('#updatePassword').submit(function () {
                if($('#password').val() != $('#cpassword').val()) {
                    alert('<?php _e( "Confirm password value must be same as the new password" , "download-manager" ); ?>')
                    return false;
                }
                $('#updatePassword-submit').html("<i class='fa fa-spin fa-refresh'></i> <?php _e( "Please Wait..." , "download-manager" ); ?>");
                $(this).ajaxSubmit({
                    success: function (res) {
                        if(res.success) {
                            $('#updatePassword').html("<div class='alert alert-success' data-title='<?php _e( "DONE!" , "download-manager" ); ?>'><b><?php _e( "Password Updated" , "download-manager" ); ?></b><br/><a style='margin-top:5px;text-decoration:underline !important;' href='<?php echo wpdm_user_dashboard_url(); ?>'><?php _e( "Go to your account dashboard" , "download-manager" ); ?></a></div>");
                        } else
                            $('#updatePassword').html("<div class='alert alert-danger' data-title='<?php _e( "ERROR!" , "download-manager" ); ?>'><b><?php _e( "Password Update Failed" , "download-manager" ); ?></b><br/><a style='margin-top:5px;text-decoration:underline !important;' href='<?php echo __::lostpassword_url(); ?>'>"+res.message+"</a></div>");
                        $('#updatePassword-submit').html(llbl);
                    }
                });
                return false;
            });

            $('body').on('click', 'form .alert-danger', function(){
                $(this).slideUp();
            });

        });
    </script>



<?php } else { ?>
    <div class="w3eden">
    <div id="wpdmlogin" class="lostpass">
    <div class="alert alert-danger" data-title="<?php _e( "ERROR!" , "download-manager" ); ?>">
        <?php echo $user->get_error_message(); ?>
    </div>
    </div>
    </div>

<?php }
