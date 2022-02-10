<?php
/**
 * Author: shahnuralam
 * Date: 2018-12-30
 * Time: 02:44
 */
if (!defined('ABSPATH')) die();
?>

<div class="w3eden">
    <div id="wpdmlogin">
        <div class="card">
            <div class="card-body text-center">
                <img style="width: 128px;border-radius: 500px;margin: 20px auto" src="<?php echo get_avatar_url(get_current_user_id(), ['height' => 128, 'width' => 128]); ?>" />
                <div><?php _e("Welcome", "download-manager"); ?></div>
                <h3><?php echo $current_user->display_name; ?></h3>
                <div class="text-muted"><?php _e("You are already logged in.", "download-manager");  ?></div>
            </div>
            <div class="card-footer text-center text-small">

                <a class="color-primary" href='<?php echo wpdm_user_dashboard_url(); ?>'><i class="far fa-user-circle"></i> <?php _e("My Account", "download-manager") ?></a> &nbsp;
                <a class="color-danger" href='<?php echo wpdm_logout_url(); ?>'><i class="fas fa-door-open"></i> <?php _e( "Logout" , "download-manager" ); ?></a>

            </div>
        </div>
    </div>
</div>


<style>
    .w3eden #wpdmlogin .media .list-group-item,
    .w3eden #wpdmlogin .media h3{
        font-family: var(--fetfont) !important;
    }
    .w3eden #wpdmlogin{
        border: 0 !important;
        padding: 0 !important;
        width: 300px !important;
    }

    .panel-footer a{
        font-weight: 400 !important;
        font-size: 11px;
    }

    .w3eden #wpdmlogin .panel-footer{
        background: -moz-linear-gradient(top,  rgba(255,255,255,0) 5%, rgba(87,17,173,0.1) 100%);
        background: -webkit-linear-gradient(top,  rgba(255,255,255,0) 5%,rgba(87,17,173,0.1) 100%);
        background: linear-gradient(to bottom,  rgba(255,255,255,0) 5%,rgba(87,17,173,0.1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00000000', endColorstr='#1a5711ad',GradientType=0 );

        padding: 20px 0;

    }
    .w3eden #wpdmlogin .panel-footer,
    .w3eden #wpdmlogin .panel{
        margin: 0 !important;
        border: 0 !important;
    }
    .w3eden #wpdmlogin h3{
        margin: 0 0 15px 0;
        line-height: 1.7;
        font-size: 12pt !important;
        font-weight: 600;
    }


</style>
