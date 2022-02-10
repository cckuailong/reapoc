<style>
    .frm td {
        padding: 5px;
        border-bottom: 1px solid #eeeeee;

        font-size: 10pt;

    }

    h4 {
        color: #336699;
        margin-bottom: 0px;
    }

    em {
        color: #888;
    }

    .wp-switch-editor {
        height: 27px !important;
    }
</style>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e( "Editor Settings" , "download-manager" ); ?></div>
    <div class="panel-body">
        <input type="hidden" value="0" name="__wpdm_gutenberg_editor">
        <label><input type="checkbox" name="__wpdm_gutenberg_editor" value="1" <?php checked(1, get_option('__wpdm_gutenberg_editor')); ?> > <?php echo __("Enable Gutenberg editor for WordPress Download Manager", "download-manager"); ?></label>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Access Settings", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php echo __("File Browser Root:", "download-manager"); ?></label><span
                    title="<?php echo __("Root dir for server file browser.<br/><b>*Don't add tailing slash (/)</b>", "download-manager"); ?>"
                    class="info infoicon">(?)</span>
            <div class="input-group">
                <input type="text" class="form-control"
                       value="<?php echo get_option('_wpdm_file_browser_root', str_replace("\\", "/", dirname(UPLOAD_DIR))); ?>"
                       name="_wpdm_file_browser_root" id="_wpdm_file_browser_root"/>
                <span class="input-group-btn">
                                    <button class="btn btn-secondary ttip" title="<?php _e('Reset Base Dir'); ?>"
                                            type="button"
                                            onclick="jQuery('#_wpdm_file_browser_root').val('<?php echo rtrim(str_replace("\\", "/", dirname(UPLOAD_DIR)), '/'); ?>');"><i
                                                class="fas fa-redo"></i></button>
                                </span>
            </div>
        </div>

        <div class="form-group">
            <label><?php echo __("File Browser Access:", "download-manager"); ?></label><br/>
            <input type="hidden" name="_wpdm_file_browser_access[]" value="[NONE]"/>
            <select style="width: 100%" name="_wpdm_file_browser_access[]" multiple="multiple"
                    data-placeholder="<?php _e("Who will have access to server file browser", "download-manager"); ?>">
                <?php

                $currentAccess = maybe_unserialize(get_option('_wpdm_file_browser_access', array('administrator')));
                $selz = '';

                ?>

                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach ($roles as $role => $name) {


                    if ($currentAccess) $sel = (in_array($role, $currentAccess)) ? 'selected=selected' : '';
                    else $sel = '';


                    ?>
                    <option value="<?php echo $role; ?>" <?php echo $sel ?>> <?php echo $name; ?></option>
                <?php } ?>
            </select>
        </div>


    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("reCAPTCHA Settings", "download-manager"); ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label><a name="liappid"></a><?php echo __("reCAPTCHA Site Key", "download-manager"); ?></label>
            <input type="text" class="form-control" name="_wpdm_recaptcha_site_key"
                   value="<?php echo get_option('_wpdm_recaptcha_site_key'); ?>">
            <em>Register a new site for reCAPTCHA from <a target="_blank"
                                                          href='https://www.google.com/recaptcha/admin#list'>here</a></em>
        </div>
        <div class="form-group">
            <label><a name="liappid"></a><?php echo __("reCAPTCHA Secret Key", "download-manager"); ?></label>
            <input type="text" class="form-control" name="_wpdm_recaptcha_secret_key"
                   value="<?php echo get_option('_wpdm_recaptcha_secret_key'); ?>">
            <em>Register a new site for reCAPTCHA from <a target="_blank"
                                                          href='https://www.google.com/recaptcha/admin#list'>here</a></em>
        </div>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_recaptcha_regform">
            <label><input type="checkbox" name="__wpdm_recaptcha_regform"
                          value="1" <?php checked(1, get_option('__wpdm_recaptcha_regform')); ?> > <?php echo __("Enable sign up form CAPTCHA validation", "download-manager"); ?>
            </label>
        </div>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_recaptcha_loginform">
            <label><input type="checkbox" name="__wpdm_recaptcha_loginform"
                          value="1" <?php checked(1, get_option('__wpdm_recaptcha_loginform')); ?>> <?php echo __("Enable sign in form CAPTCHA validation", "download-manager"); ?>
            </label>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Verification Settings", "download-manager"); ?></div>
    <div class="panel-body">

        <fieldset>
            <legend><?php echo __("Blocked IPs", "download-manager"); ?></legend>
            <div class="form-group">
                <textarea placeholder="<?php _e("One IP per line", "download-manager"); ?>" rows="5"
                          class="form-control"
                          name="__wpdm_blocked_ips"><?php echo esc_attr(get_option('__wpdm_blocked_ips')); ?></textarea>
                <em><?php _e("List IP Addresses to blacklist. One IP per line ( Ex: IPv4 - 192.168.23.12 or 192.168.23.1/24 or 192.168.23.* , IPv6 - 2a01:8760:2:3001::1 or 2620:112:3000::/44 )", "download-manager"); ?></em>
            </div>
            <div class="form-group">
                <textarea placeholder="<?php _e("Message to show when an IP is blocked", "download-manager"); ?>"
                          class="form-control"
                          name="__wpdm_blocked_ips_msg"><?php echo get_option('__wpdm_blocked_ips_msg'); ?></textarea>
                <em><?php _e("Message to show when an IP is blocked", "download-manager"); ?></em>
            </div>
        </fieldset>

    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Upload Settings", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label><?php _e('Allowed file types to upload', 'download-manager'); ?></label><br/>
            <?php
            $allowed_file_types = get_option("__wpdm_allowed_file_types", '');
            ?>
            <input type="text" class="form-control" value="<?php echo  $allowed_file_types; ?>"
                   placeholder="<?php echo  esc_attr__('Keep empty to use wordpress defaults', 'download-manager'); ?>"
                   name="__wpdm_allowed_file_types"/>
            <em><?php _e('Enter the file extensions you want to allow to upload through WPDM ( ex: png,jpg,pdf )', 'download-manager'); ?></em>
            <br/>

        </div>

        <hr/>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_sanitize_filename"/>
            <label><input style="margin-right: 5px" <?php checked(1, get_option('__wpdm_sanitize_filename', 0)); ?>
                          type="checkbox" value="1"
                          name="__wpdm_sanitize_filename"><?php _e("Sanitize Filename", "download-manager"); ?>
            </label><br/>
            <em><?php _e("Check the option if you want to sanitize uploaded file names to remove illegal chars", "download-manager"); ?></em>
            <br/>

        </div>

        <hr/>
        <div class="form-group">
            <input type="hidden" value="0" name="__wpdm_chunk_upload"/>
            <label><input style="margin-right: 5px" <?php checked(1, get_option('__wpdm_chunk_upload', 0)); ?>
                          type="checkbox" value="1"
                          name="__wpdm_chunk_upload"><?php _e('Chunk Upload', 'download-manager'); ?></label><br/>
            <em><?php _e('Check the option if you want to enable chunk upload to override http upload limits', 'download-manager'); ?></em>
            <br/>

        </div>
        <div class="form-group">
            <label><?php _e('Chunk Size', 'download-manager'); ?></label><br/>
            <div class="input-group">
                <input class="form-control" value="<?php echo get_option('__wpdm_chunk_size', 1024); ?>" type="number"
                       name="__wpdm_chunk_size">
                <div class="input-group-addon">KB</div>
            </div>
            <br/>

        </div>


    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("Messages", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <label for="__wpdm_permission_denied_msg"><?php echo __("Permission Denied Message for Packages:", "download-manager"); ?></label>
            <textarea id="__wpdm_permission_denied_msg" name="__wpdm_permission_denied_msg"
                      class="form-control"><?php echo stripslashes(get_option('__wpdm_permission_denied_msg')); ?></textarea>
        </div>

        <div class="form-group">
            <label><?php echo __("Login Required Message:", "download-manager"); ?></label>
            <textarea class="form-control" cols="70" rows="6"
                      name="wpdm_login_msg"><?php echo get_option('wpdm_login_msg', false) ? stripslashes(get_option('wpdm_login_msg')) : ('<div class="w3eden"><div class="panel panel-default card"><div class="panel-body card-body"><span class="text-danger">Login is required to access this page</span></div><div class="panel-footer card-footer text-right"><a href="' . wp_login_url() . '?redirect_to=[this_url]" class="btn btn-danger wpdmloginmodal-trigger btn-sm"><i class="fa fa-lock"></i> Login</a></div></div></div>'); ?></textarea>
            <em class="note"><?php echo sprintf(__("If you want to show login form instead of message user short-code [wpdm_login_form]. To show login form in a modal popup, please follow %s the doc here %s", "download-manager"), "<a target='_blank' href='https://www.wpdownloadmanager.com/how-to-add-modal-popup-login-form-in-your-wordpress-site/'>", "</a>"); ?></em>

        </div>

    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?php echo __("File Download", "download-manager"); ?></div>
    <div class="panel-body">

        <div class="form-group"><input type="hidden" name="__wpdm_mask_link" value="0">
            <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1), 1); ?> name="__wpdm_mask_dlink"
                          value="1"> <?php _e("Mask Download Link", "download-manager"); ?> &nbsp; </label>
            <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1), 0); ?> name="__wpdm_mask_dlink"
                          value="0"> <?php _e("Unmask Download Link", "download-manager"); ?></label><br/>
            <em><?php _e("Check this option if you want to mask/unmask file download link. If you unmask download link, bots will be able the find any public download link easily.", "download-manager"); ?></em>
        </div>
        <hr/>


        <div class="form-group">
            <label><?php echo __("Download Speed:", "download-manager"); ?></label>
            <div class="input-group">
                <input type=text class="form-control" name="__wpdm_download_speed"
                       value="<?php echo intval(get_option('__wpdm_download_speed', 4096)); ?>"/>
                <span class="input-group-addon">KB/s</span>
            </div>
        </div>
        <hr/>
        <em class="note"><?php _e("If you get broken download, then try enabling/disabling following options, as sometimes server may not support output buffering or partial downloads", "download-manager"); ?>
            :</em>
        <hr/>
        <div class="form-group">
            <label><?php _e("Resumable Downloads", "download-manager"); ?></label><br/>
            <select name="__wpdm_download_resume">
                <option value="1"><?php _e("Enabled", "download-manager"); ?></option>
                <option value="2" <?php selected(get_option('__wpdm_download_resume'), 2); ?>><?php _e("Disabled", "download-manager"); ?></option>
            </select>
        </div>
        <div class="form-group">
            <label><?php _e("Output Buffering", "download-manager"); ?></label><br/>
            <select name="__wpdm_support_output_buffer">
                <option value="1"><?php _e("Enabled", "download-manager"); ?></option>
                <option value="0" <?php selected(get_option('__wpdm_support_output_buffer'), 0); ?>><?php _e("Disabled", "download-manager"); ?></option>
            </select>
        </div>

        <div class="form-group">
            <hr/>
            <input type="hidden" value="0" name="__wpdm_open_in_browser"/>
            <label><input style="margin-right: 5px"
                          type="checkbox" <?php checked(get_option('__wpdm_open_in_browser'), 1); ?> value="1"
                          name="__wpdm_open_in_browser"><?php _e("Open in Browser", "download-manager"); ?></label><br/>
            <em><?php _e("Try to Open in Browser instead of download when someone clicks on download link", "download-manager"); ?></em>
            <br/>

        </div>

    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Misc Settings", "download-manager"); ?></div>
    <div class="panel-body">


        <table cellpadding="5" cellspacing="0" class="frm" width="100%">

            <?php do_action('basic_settings'); ?>

        </table>

    </div>


</div>

<?php do_action('basic_settings_section'); ?>

<script>
    jQuery(function ($) {
        $('#wpdmap').change(function () {

            if (this.value == 1)
                $('#aps').slideDown();
            else
                $('#aps').slideUp();
        });
    });
</script>
<style>
    .w3eden textarea.form-control {
        min-width: 100%;
        max-width: 100%;
        width: 100%;
        height: 70px;
    }
</style>
