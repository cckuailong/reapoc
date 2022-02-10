<div id="lock-options"  class="tab-pane">
    <?php echo __( "You can use one or more of following methods to lock your package download:" , "download-manager" ); ?>
    <br/>
    <br/>
    <div class="wpdm-accordion w3eden">

        <!-- Terms Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" class="wpdmlock" rel='terms' name="file[terms_lock]" <?php if(get_post_meta($post->ID,'__wpdm_terms_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Must Agree with Terms" , "download-manager" ); ?></label></h3>
            <div  id="terms" class="fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_terms_lock', true)!='1') echo "style='display:none'"; ?> >
                <div class="form-group">
                    <label><?php echo __( "Terms Page:" , "download-manager" ); ?></label><br/>
                    <?php wp_dropdown_pages(['name' => 'file[terms_page]', 'class' => 'form-control d-block', 'id' => 'wpdm_terms_page', 'show_option_none' => __( 'Use custom content below', 'download-manager' ), 'selected' => get_post_meta($post->ID, '__wpdm_terms_page', true)]) ?>
                </div>
                <div class="form-group">
                <label for="pps_z"><?php echo __( "Terms Title:" , "download-manager" ); ?></label>
                <input type="text" class="form-control input-lg" name="file[terms_title]" value="<?php echo esc_html(stripslashes(get_post_meta($post->ID,'__wpdm_terms_title', true))); ?>" />
                </div>
                <div class="form-group">
                <label for="pps_z"><?php echo __( "Terms and Conditions:" , "download-manager" ); ?></label>
                    <?php
                    wp_editor(stripslashes(get_post_meta($post->ID,'__wpdm_terms_conditions', true)), "tc_z", ['textarea_name'  =>  'file[terms_conditions]', 'media_buttons' => false]);
                    ?>
                </div>
                <label for="pps_z"><?php echo __( "Terms Checkbox Label:" , "download-manager" ); ?></label>
                <input type="text" class="form-control input-lg" name="file[terms_check_label]" value="<?php echo esc_html(stripslashes(get_post_meta($post->ID,'__wpdm_terms_check_label', true))); ?>" />


            </div>
        </div>

        <!-- Password Lock -->
        <div class="panel panel-default">
        <h3 class="panel-heading"><label><input type="checkbox" class="wpdmlock" rel='password' name="file[password_lock]" <?php if(get_post_meta($post->ID,'__wpdm_password_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Password Lock" , "download-manager" ); ?></label></h3>
        <div  id="password" class="fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_password_lock', true)!='1') echo "style='display:none'"; ?> >
            <div class="form-group">

                <label><?php echo __( "Password:" , "download-manager" ); ?></label>
                <div class="input-group"><input class="form-control" type="text" name="file[password]" id="pps_z" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_password', true)); ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" onclick="return generatepass('pps_z');" type="button"><i class="fa fa-ellipsis-h"></i></button>
                      </span>
                </div>
                <div class="note"><?php echo __( "You can use single or multiple password for a package. If you are using multiple password then separate each password by []. example [password1][password2]", "download-manager" ) ?></div>
            </div>
            <div class="form-group">
                <label><?php echo __( "PW Usage Limit:" , "download-manager" ); ?></label>
                <div class="input-group">
                    <input size="10" class="form-control" type="text" disabled="disabled"  value="Available for the pro users only" />
                    <span class="input-group-addon">
                       <span class="input-group-text"> / <?php echo __( "password" , "download-manager" ); ?></span>
                    </span>
                    <span class="input-group-addon">
                       <label  class="input-group-text" style="color: var(--color-info);"><input type="checkbox" disabled="disabled" value="0" /> <?php echo __( "Reset Password Usage Count" , "download-manager" ); ?></label>
                    </span>
                </div>
                <div class="note"><?php echo __( "Password will expire after it exceed this usage limit" , "download-manager" ); ?></div>
            </div>

        </div>
        </div>

        <!-- Captcha Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="captcha" class="wpdmlock" name="file[captcha_lock]" <?php if(get_post_meta($post->ID,'__wpdm_captcha_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Captcha Lock" , "download-manager" ); ?></label></h3>
            <div id="captcha" class="frm fwpdmlock panel-body"  <?php if(get_post_meta($post->ID,'__wpdm_captcha_lock', true)!='1') echo "style='display:none'"; ?> >

                <a href="edit.php?post_type=wpdmpro&page=settings"><?php if(!get_option('_wpdm_recaptcha_site_key') || !get_option('_wpdm_recaptcha_secret_key')) _e( "Please configure reCAPTCHA" , "download-manager" ); ?></a>
                <?php _e( "Users will be asked for reCAPTCHA verification before download." , "download-manager" ); ?>

            </div>
        </div>



        <?php do_action('wpdm_download_lock_option',$post); ?>
    </div>
    <div class="clear"></div>
</div>
<!-- Generate password modal  -->
<div class="modal fade" tabindex="-1" role="dialog" id="generatepass">
    <div class="modal-dialog" role="document" style="max-width: 400px">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-key"></i> <?php echo __( "Generate Password", "download-manager" ) ?></h4>
            </div>
            <div class="modal-body-np">
                <div class="pfs panel panel-default card card-default" style="border-radius:0;box-shadow: none;border: 0 !important;">
                    <div class="panel-heading card-header" style="border-top: 1px solid #ddd !important;border-radius:0;"><b><?php _e( "Password Lenght & Count" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <div class="row">
                            <div class="col-md-6">

                                    <b><?php _e( "Number of passwords:" , "download-manager" ); ?></b><Br/>
                                    <input class="form-control" type="number" id='pcnt' value="">

                            </div>
                            <div  class="col-md-6">

                                    <b><?php _e( "Password length:" , "download-manager" ); ?></b><Br/>
                                    <input  class="form-control" type="number" id='ncp' value="">

                            </div>
                        </div>
                    </div>
                    <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #ddd"><b><?php _e( "Password Strength" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <input style="padding:0;" type="range" min="1" max="4" value="2" class="form-control" id="passtrn">
                                <div class="row">
                                    <div class="col-md-6" style="color: var(--color-danger);"><?php echo __( "Weak", "download-manager" ) ?></div>
                                    <div class="col-md-6 text-right" style="color: var(--color-success);"><?php echo __( "Strong", "download-manager" ) ?></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="button" id="gpsc" class="btn btn-secondary btn-lg btn-block" value="Generate" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #dddddd"><b><?php _e( "Generated Passwords" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <textarea id="ps" class="form-control"></textarea>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <input type="button" id="pins" class="btn btn-primary btn-lg btn-block" value="<?php _e( "Insert Password(s)" , "download-manager" ); ?>" />
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
