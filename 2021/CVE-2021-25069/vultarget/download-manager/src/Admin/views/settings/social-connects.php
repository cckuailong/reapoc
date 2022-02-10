<?php
/**
 * Date: 9/28/16
 * Time: 9:26 PM
 */
if(!defined('ABSPATH')) die('!');
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Social Lock Panel" , "download-manager" ); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label><?php echo __( "Title" , "download-manager" ); ?></label>
            <input type="text" class="form-control" name="_wpdm_social_lock_panel_title" value="<?php echo get_option('_wpdm_social_lock_panel_title', 'Like or Share to Download'); ?>">
        </div>
        <div class="form-group">
            <label><?php echo __( "Description" , "download-manager" ); ?></label>
            <input type="text" class="form-control" name="_wpdm_social_lock_panel_desc" value="<?php echo get_option('_wpdm_social_lock_panel_desc', 'Please support us, use one of the buttons below to unlock the download link.'); ?>">
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Facebook App Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label><a name="fbappid"></a><?php echo __( "Facebook APP ID" , "download-manager" ); ?></label>
            <input type="text" class="form-control" name="_wpdm_facebook_app_id" value="<?php echo get_option('_wpdm_facebook_app_id'); ?>">
            <em>Create new facebook app from <a target="_blank" href='https://developers.facebook.com/apps'>here</a></em>
        </div>
        <div class="form-group">
            <label><a name="fbappid"></a><?php echo __( "Facebook APP Secret" , "download-manager" ); ?></label>
            <input type="text" class="form-control" name="_wpdm_facebook_app_secret" value="<?php echo get_option('_wpdm_facebook_app_secret'); ?>">
        </div>

    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Login Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?sociallogin=facebook'); ?>" />
    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Connect Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?connect=facebook'); ?>" />
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <a target="_blank" href="https://console.developers.google.com/apis/dashboard"><?php _e("Google Developer Console", "download-manager");  ?></a>
        </div>
        <b><?php _e( "Google API Credentials" , "download-manager" ); ?></b></div>

    <div class="panel-body">

        <p class="text-muted">
            <?php _e("You need to create an app from Google developer console, them from Credentials tab, create an API key and OAuth client ID and use those info in the following fields.", "download-manager");  ?>
        </p>
        <hr/>

        <div class="form-group">
            <label>API Key</label>
            <input type="text" name="_wpdm_google_app_secret" class="form-control"
                       value="<?php echo get_option('_wpdm_google_app_secret'); ?>"/>

        </div>
        <div  class="form-group">
            <label>Client ID</label>
            <input type="text" name="_wpdm_google_client_id" class="form-control"
                       value="<?php echo get_option('_wpdm_google_client_id'); ?>"/>

        </div>
        <div  class="form-group">
            <label>Client Secret</label>
            <input type="text" name="_wpdm_google_client_secret" class="form-control"
                       value="<?php echo get_option('_wpdm_google_client_secret'); ?>"/>

        </div>

    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Login Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?sociallogin=google'); ?>" />
    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Connect Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?connect=google'); ?>" />
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "LinkedIn App Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "LinkedIn Client ID" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_linkedin_client_id" value="<?php echo get_option('_wpdm_linkedin_client_id'); ?>">
        <em>Create new linkedin app from <a target="_blank" href='https://www.linkedin.com/developer/apps'>here</a></em>
    </div>
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "LinkedIn Client Secret" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_linkedin_client_secret" value="<?php echo get_option('_wpdm_linkedin_client_secret'); ?>">
        <em>Create new linkedin app from <a target="_blank" href='https://www.linkedin.com/developer/apps'>here</a></em>
    </div>
    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Login Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?sociallogin=linkedin'); ?>" />
    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Connect Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('?connect=linkedin'); ?>" />
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <a target="_blank" href="https://developer.twitter.com/en/apps" class="pull-right">Create Twitter App</a>
        <?php echo __( "Twitter App Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "Access Token" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_twitter_access_token" value="<?php echo get_option('_wpdm_twitter_access_token'); ?>">
    </div>
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "Access Token Secret" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_twitter_access_token_secret" value="<?php echo get_option('_wpdm_twitter_access_token_secret'); ?>">
    </div>
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "Consumer Key (API Key)" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_twitter_api_key" value="<?php echo get_option('_wpdm_twitter_api_key'); ?>">
    </div>
    <div class="form-group">
        <label><a name="liappid"></a><?php echo __( "Consumer Secret (API Secret)" , "download-manager" ); ?></label>
        <input type="text" class="form-control" name="_wpdm_twitter_api_secret" value="<?php echo get_option('_wpdm_twitter_api_secret'); ?>">
    </div>
    </div>
    <div class="panel-footer">
        <b style="display: inline-block;width: 250px">Redirect URI:</b> &nbsp; <input onclick="this.select()" type="text" class="form-control" style="background: #fff;cursor: copy;display: inline;width: 400px" readonly="readonly" value="<?php echo home_url('/'); ?>" />
    </div>
</div>
<?php $social_logins = get_option('__wpdm_social_login'); if(!is_array($social_logins)) $social_logins = array(); ?>
<div class="panel panel-default">
    <div class="panel-heading"><?php echo __( "Enable Social Logins" , "download-manager" ); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <input type="hidden" name="__wpdm_social_login[none]" value="" />
            <label><input type="checkbox" name="__wpdm_social_login[google]" value="1" <?php checked(true, isset($social_logins['google'])); ?>> <?php echo __( "Google" , "download-manager" ); ?></label>
            <label><input type="checkbox" name="__wpdm_social_login[facebook]" value="1" <?php checked(true, isset($social_logins['facebook'])); ?>> <?php echo __( "Facebook" , "download-manager" ); ?></label>
            <label><input type="checkbox" name="__wpdm_social_login[twitter]" value="1" <?php checked(true, isset($social_logins['twitter'])); ?>> <?php echo __( "Twitter" , "download-manager" ); ?></label>
            <label><input type="checkbox" name="__wpdm_social_login[linkedin]" value="1" <?php checked(true, isset($social_logins['linkedin'])); ?>> <?php echo __( "LinkedIn" , "download-manager" ); ?></label>

        </div>
    </div>
</div>
