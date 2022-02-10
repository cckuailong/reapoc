<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:23
 */

if(!defined("ABSPATH")) die();

if($ttype == 'email'){ ?>
    <form method="post" id="emlstform">
        <?php wp_nonce_field(WPDM_PRI_NONCE, "__sesnonce"); ?>
        <div class="panel panel-default">
            <div class="panel-heading">Email Settings</div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php _e( "Email Template" , "download-manager" ); ?>
                            <select name="__wpdm_email_template" class="form-control wpdm-custom-select" style="width: 200px" id="etmpl">
                                <?php
                                $eds = \WPDM\__\FileSystem::scanDir(WPDM()->email->templateDir);
                                $__wpdm_email_template = get_option('__wpdm_email_template', "default.html");
                                $__wpdm_email_setting = maybe_unserialize(get_option('__wpdm_email_setting'));
                                foreach ($eds as $file) {
                                    if(strstr($file, ".html")) {
                                        ?>
                                        <option value="<?php echo basename($file); ?>" <?php selected($__wpdm_email_template, basename($file)); ?> ><?php echo ucfirst(str_replace(".html", "", basename($file))); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <?php _e( "Logo URL" , "download-manager" ); ?>
                            <?php echo wpdm_media_field(array('placeholder' => __("Logo URL" , "download-manager"), 'name' => '__wpdm_email_setting[logo]', 'id' => 'logo-url', 'value' => (isset($__wpdm_email_setting['logo'])?$__wpdm_email_setting['logo']:''))); ?>
                        </div>
                        <div class="form-group">
                            <?php _e( "Banner/Background Image URL" , "download-manager" ); ?>
                            <?php echo wpdm_media_field(array('placeholder' => __("Banner/Background Image URL" , "download-manager"), 'name' => '__wpdm_email_setting[banner]', 'id' => 'banner-url', 'value' => (isset($__wpdm_email_setting['banner'])?$__wpdm_email_setting['banner']:''))); ?>

                        </div>
                        <div class="form-group">
                            <?php _e( "Footer Text" , "download-manager" ); ?>
                            <textarea name="__wpdm_email_setting[footer_text]" class="form-control"><?php echo isset($__wpdm_email_setting['footer_text'])?stripslashes($__wpdm_email_setting['footer_text']):'';?></textarea>
                        </div>
                        <div class="form-group">
                            <?php _e( "Facebook Page URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[facebook]" value="<?php echo isset($__wpdm_email_setting['facebook'])?($__wpdm_email_setting['facebook']):'';?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <?php _e( "Twitter Profile URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[twitter]" value="<?php echo isset($__wpdm_email_setting['twitter'])?$__wpdm_email_setting['twitter']:'';?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <?php _e( "Youtube Profile URL" , "download-manager" ); ?>
                            <input type="text" name="__wpdm_email_setting[youtube]" value="<?php echo isset($__wpdm_email_setting['youtube'])?$__wpdm_email_setting['youtube']:'';?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="w3econtainer">
                            <div class="w3erow">
                                <div class="w3ecolumn w3eleft">
                                    <span class="w3edot" style="background:#ED594A;"></span>
                                    <span class="w3edot" style="background:#FDD800;"></span>
                                    <span class="w3edot" style="background:#5AC05A;"></span>
                                </div>
                                <div class="w3ecolumn w3emiddle">
                                    /email-templates/<span id="etplname"></span>
                                </div>
                                <div class="w3ecolumn w3eright">
                                    <div style="float:right">
                                        <span class="w3ebar"></span>
                                        <span class="w3ebar"></span>
                                        <span class="w3ebar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="w3econtent">
                                <iframe style="margin: 0;width: 100%;height: 550px;border-radius: 3px" id="preview" src="edit.php?action=email_template_preview&id=user-signup&etmpl=<?php echo $__wpdm_email_template; ?>&__empnonce=<?php echo wp_create_nonce(WPDM_PRI_NONCE); ?>">

                                </iframe>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-primary" id="emsbtn" style="width: 180px;"><i class="fas fa-hdd"></i> <?php _e( "Save Changes" , "download-manager" ); ?></button>
            </div>
        </div>
    </form>
<?php } ?>
